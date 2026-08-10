<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Safely serialize the collections for client-side search fallbacks and offline view rendering
$pages_json = json_encode( isset( $pages ) ? $pages : array() );
$apis_json = json_encode( isset( $apis ) ? $apis : array() );
$changelogs_json = json_encode( isset( $changelogs ) ? $changelogs : array() );
?>

<!-- Scoped styling for the Search Modal and highlight markers conforming to the monochromatic theme -->
<style>
#cora-docs-search-modal mark {
    background-color: #f4f4f5; /* zinc-100 */
    color: #09090b; /* zinc-950 */
    padding: 0.05rem 0.2rem;
    border-radius: 0.125rem;
}
.dark #cora-docs-search-modal mark {
    background-color: #27272a; /* zinc-800 */
    color: #f4f4f5; /* zinc-100 */
}
/* Scrollbar formatting */
#cora-docs-search-results-list::-webkit-scrollbar {
    width: 6px;
}
#cora-docs-search-results-list::-webkit-scrollbar-track {
    background: transparent;
}
#cora-docs-search-results-list::-webkit-scrollbar-thumb {
    background-color: #e4e4e7; /* zinc-200 */
    border-radius: 3px;
}
.dark #cora-docs-search-results-list::-webkit-scrollbar-thumb {
    background-color: #27272a; /* zinc-800 */
}
</style>

<!-- ⌘K Search Overlay Modal Component -->
<div id="cora-docs-search-modal" class="fixed inset-0 z-50 flex items-start justify-center pt-[15vh] px-4 md:px-0 hidden" role="dialog" aria-modal="true">
    <!-- Backdrop Blur Overlay -->
    <div class="fixed inset-0 backdrop-blur-md bg-black/30 transition-opacity" id="cora-docs-search-backdrop"></div>
    
    <!-- Modal Card Container -->
    <div class="relative w-full max-w-xl bg-white border border-zinc-200/80 rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[60vh] transition-all transform z-10">
        
        <!-- Search Input Bar -->
        <div class="p-3.5 flex items-center gap-3 bg-white border-b border-zinc-100 ">
            <!-- Search Icon -->
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 flex-shrink-0">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            
            <!-- Input Box -->
            <input type="text" id="cora-docs-search-input" oninput="coraOnSearchInput(this.value)" class="flex-1 text-xs border-0 bg-transparent text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-0 w-full" placeholder="Search documentation, modules, and API reference..." autocomplete="off">
            
            <!-- Loading Spinner -->
            <svg id="cora-docs-search-spinner" class="animate-spin h-3.5 w-3.5 text-zinc-450 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            
            <!-- Shortcut Badges -->
            <kbd class="pointer-events-none inline-flex h-5 select-none items-center gap-0.5 rounded border border-zinc-250/60 bg-zinc-50 px-1.5 font-mono text-[9px] font-medium text-zinc-450 shadow-sm">ESC</kbd>
        </div>
        
        <!-- Results Scrollable List -->
        <div id="cora-docs-search-results-list" class="overflow-y-auto max-h-[40vh] bg-white ">
            <!-- Dynamically populated -->
        </div>
        
        <!-- Keyboard shortcuts hints footer -->
        <div class="border-t border-zinc-100 px-4 py-2.5 bg-zinc-50/50 text-[9px] text-zinc-400 flex items-center justify-between select-none">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1">
                    <kbd class="px-1 border border-zinc-200 bg-white rounded font-mono text-[8px]">↑↓</kbd> to navigate
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="px-1 border border-zinc-200 bg-white rounded font-mono text-[8px]">↵</kbd> to select
                </span>
            </div>
            <div>
                <span class="flex items-center gap-1">
                    <kbd class="px-1 border border-zinc-200 bg-white rounded font-mono text-[8px]">Esc</kbd> to close
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Asynchronous AJAX Router and UI integration scripts -->
<script>
// Base configurations extracted from PHP
const ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
const docsBaseUrl = '<?php echo esc_url( home_url( '/docs/' ) ); ?>';
const coraPublicPages = <?php echo $pages_json; ?>;
const coraPublicApis = <?php echo $apis_json; ?>;
const coraPublicChangelogs = <?php echo $changelogs_json; ?>;

// Search configuration variables
let debounceTimer = null;
window.currentSelectedIndex = -1;

/**
 * ── 1. Asynchronous Client-side Router ────────────────────────────────────
 */

/**
 * Dynamically retrieves page markdown contents, displays skeleton loaders, and updates active nav highlights.
 */
window.coraPublicLoadPage = function(event, slug, element, pushToHistory = true) {
    if (event) {
        event.preventDefault();
    }
    
    // Resolve sub-containers
    const pageLayout = document.getElementById('cora-public-page-layout');
    const apiLayout = document.getElementById('cora-public-api-layout');
    const changelogLayout = document.getElementById('cora-public-changelog-layout');
    const aiLayout = document.getElementById('cora-public-ai-layout');
    
    if (pageLayout) pageLayout.classList.remove('hidden');
    if (apiLayout) apiLayout.classList.add('hidden');
    if (changelogLayout) changelogLayout.classList.add('hidden');
    if (aiLayout) aiLayout.classList.add('hidden');
    
    const bodyContainer = document.getElementById('cora-public-markdown-body');
    const specialGrid = document.getElementById('cora-platform-overview-special');
    
    // Display skeleton loader inside the body container while loading
    if (bodyContainer) {
        bodyContainer.classList.remove('hidden');
        showSkeletonLoader(bodyContainer);
    }
    if (specialGrid) {
        specialGrid.classList.add('hidden');
    }
    
    // Reset active nav link visual selections
    document.querySelectorAll('.cora-nav-link').forEach(link => {
        deactivateLink(link);
    });
    
    // Resolve active sidebar link item
    let activeLink = element;
    if (!activeLink && slug) {
        activeLink = document.querySelector(`.cora-nav-link[data-slug="${slug}"]`);
    }
    if (activeLink) {
        activateLink(activeLink);
        expandParentSidebarGroup(activeLink);
    }
    
    // Fetch page from core documentation system AJAX endpoint
    fetch(`${ajaxUrl}?action=cora_public_get_page&slug=${encodeURIComponent(slug)}`)
        .then(response => response.json())
        .then(res => {
            if (res.success && res.data) {
                // Update title & content area
                document.title = `${res.data.title} | Cora Platform Developer Hub`;
                
                // Update dynamic DOM elements inside #cora-public-page-layout
                const breadcrumbCategory = document.getElementById('cora-public-breadcrumb-category');
                const breadcrumbTitle = document.getElementById('cora-public-breadcrumb-title');
                const pageTitle = document.getElementById('cora-public-page-title');
                const pageUpdated = document.getElementById('cora-public-page-updated');
                const pageCategory = document.getElementById('cora-public-page-category');
                const pageReadingTime = document.getElementById('cora-public-page-reading-time');
                
                // Resolve category name from sidebar grouping if element is provided, fallback to DB
                let categoryName = res.data.category;
                if (element) {
                    const group = element.closest('.cora-sidebar-group');
                    if (group) {
                        const headerSpan = group.querySelector('button span');
                        if (headerSpan) categoryName = headerSpan.textContent.trim();
                    }
                }
                const catText = categoryName.replace('-', ' ');
                
                if (breadcrumbCategory) breadcrumbCategory.textContent = catText;
                if (breadcrumbTitle) breadcrumbTitle.textContent = res.data.title.replace('👋 ', '');
                
                let displayTitle = res.data.title;
                if (slug === 'platform-overview' && !displayTitle.includes('👋')) {
                    displayTitle = '👋 ' + displayTitle;
                }
                if (pageTitle) pageTitle.textContent = displayTitle;
                
                let updatedStr = 'Updated ';
                if (res.data.updated_at === 'yesterday' || !res.data.updated_at) {
                    updatedStr += 'yesterday';
                } else {
                    updatedStr += res.data.updated_at;
                }
                if (pageUpdated) pageUpdated.textContent = updatedStr;
                if (pageCategory) pageCategory.textContent = catText;
                
                if (bodyContainer) {
                    bodyContainer.innerHTML = res.data.html;
                }
                
                // Recalculate reading time
                const words = bodyContainer ? bodyContainer.textContent.trim().split(/\s+/).length : 350;
                const readingTime = Math.max(1, Math.ceil(words / 200)) + ' min read';
                if (pageReadingTime) pageReadingTime.textContent = readingTime;
                
                // Show/hide platform overview special grid
                if (slug === 'platform-overview') {
                    if (specialGrid) specialGrid.classList.remove('hidden');
                    if (bodyContainer) bodyContainer.classList.add('hidden');
                    const overviewDbContent = document.getElementById('cora-platform-overview-db-content');
                    if (overviewDbContent) {
                        overviewDbContent.innerHTML = res.data.html;
                    }
                } else {
                    if (specialGrid) specialGrid.classList.add('hidden');
                    if (bodyContainer) bodyContainer.classList.remove('hidden');
                }
                
                // Refresh table of contents scrollspy
                if (typeof window.coraDocsRefreshTOC === 'function') {
                    window.coraDocsRefreshTOC();
                }
                
                // Final check to bind newly loaded dynamic elements if needed
                if (!activeLink) {
                    activeLink = document.querySelector(`.cora-nav-link[data-slug="${slug}"]`);
                    if (activeLink) {
                        activateLink(activeLink);
                        expandParentSidebarGroup(activeLink);
                    }
                }
            } else {
                if (bodyContainer) {
                    bodyContainer.innerHTML = `
                        <div class="p-8 border border-zinc-200/80 rounded-xl bg-white text-center max-w-xl mx-auto my-12">
                            <h2 class="text-sm font-bold text-zinc-900 ">Error Loading Page</h2>
                            <p class="text-xs text-zinc-505 mt-2">${res.data && res.data.message ? res.data.message : 'The requested page could not be found or loaded.'}</p>
                            <a href="#" onclick="coraPublicLoadPage(event, 'platform-overview')" class="inline-block mt-4 text-xs font-semibold text-zinc-955 underline">Back to Overview</a>
                        </div>
                    `;
                }
            }
        })
        .catch(err => {
            console.error('AJAX router fetch error:', err);
            if (bodyContainer) {
                bodyContainer.innerHTML = `
                    <div class="p-8 border border-zinc-200/80 rounded-xl bg-white text-center max-w-xl mx-auto my-12">
                        <h2 class="text-sm font-bold text-zinc-900 ">Connection Refused</h2>
                        <p class="text-xs text-zinc-505 mt-2">Failed to connect to the database router. Check your local environment server status.</p>
                    </div>
                `;
            }
        });
        
    // Update browser navigation state
    if (pushToHistory) {
        history.pushState({ slug: slug }, '', docsBaseUrl + slug);
    }
};

/**
 * Renders static reference modules (API Reference, Changelog, AI Copilot) inside the content panel.
 */
window.coraPublicShowSection = function(section, pushToHistory = true) {
    // Resolve sub-containers
    const pageLayout = document.getElementById('cora-public-page-layout');
    const apiLayout = document.getElementById('cora-public-api-layout');
    const changelogLayout = document.getElementById('cora-public-changelog-layout');
    const aiLayout = document.getElementById('cora-public-ai-layout');
    
    // Clear sidebar navigation highlight highlights
    document.querySelectorAll('.cora-nav-link').forEach(link => {
        deactivateLink(link);
    });
    
    // Highlight correct section anchor button
    const queryEscaped = section.replace(/['"]/g, '\\$&');
    const activeLink = document.querySelector(`[onclick*="coraPublicShowSection('${queryEscaped}')"]`) 
                     || document.querySelector(`[onclick*="coraPublicShowSection(\"${queryEscaped}\")"]`);
    if (activeLink) {
        activateLink(activeLink);
    }
    
    // Render/show target sub-container
    if (section === 'api') {
        document.title = `API Reference Registry | Cora Developer Hub`;
        if (pageLayout) pageLayout.classList.add('hidden');
        if (apiLayout) {
            apiLayout.classList.remove('hidden');
            if (apiLayout.innerHTML === '') {
                renderApiRegistry(apiLayout);
            }
        }
        if (changelogLayout) changelogLayout.classList.add('hidden');
        if (aiLayout) aiLayout.classList.add('hidden');
    } else if (section === 'changelog') {
        document.title = `Changelog & Updates | Cora Developer Hub`;
        if (pageLayout) pageLayout.classList.add('hidden');
        if (apiLayout) apiLayout.classList.add('hidden');
        if (changelogLayout) {
            changelogLayout.classList.remove('hidden');
            if (changelogLayout.innerHTML === '') {
                renderChangelogFeed(changelogLayout);
            }
        }
        if (aiLayout) aiLayout.classList.add('hidden');
    } else if (section === 'cora-ai') {
        document.title = `Ask Cora AI Copilot | Cora Developer Hub`;
        if (pageLayout) pageLayout.classList.add('hidden');
        if (apiLayout) apiLayout.classList.add('hidden');
        if (changelogLayout) changelogLayout.classList.add('hidden');
        if (aiLayout) {
            aiLayout.classList.remove('hidden');
            if (aiLayout.innerHTML === '') {
                renderCoraAiChat(aiLayout);
            }
        }
    }
    
    // Refresh table of contents scrollspy
    if (typeof window.coraDocsRefreshTOC === 'function') {
        window.coraDocsRefreshTOC();
    }
    
    // Sync browser URL history
    if (pushToHistory) {
        history.pushState({ section: section }, '', docsBaseUrl + section);
    }
};

/**
 * ── 2. Keyboard Command Palette and Search Functions ─────────────────────────
 */

/**
 * Handle debounced searches on typing
 */
window.coraOnSearchInput = function(val) {
    clearTimeout(debounceTimer);
    const spinner = document.getElementById('cora-docs-search-spinner');
    const query = val.trim();
    
    if (query.length < 2) {
        if (spinner) spinner.classList.add('hidden');
        coraRenderRecentOrEmptyResults();
        return;
    }
    
    if (spinner) spinner.classList.remove('hidden');
    
    debounceTimer = setTimeout(() => {
        fetch(`${ajaxUrl}?action=cora_public_search_docs&q=${encodeURIComponent(query)}`)
            .then(r => r.json())
            .then(res => {
                if (spinner) spinner.classList.add('hidden');
                if (res.success && res.data) {
                    coraRenderSearchResults(res.data, query);
                } else {
                    coraRenderNoResults(query);
                }
            })
            .catch(err => {
                if (spinner) spinner.classList.add('hidden');
                console.error('Public docs search error:', err);
                coraRenderNoResults(query);
            });
    }, 200);
};

/**
 * Open search modal and configure initial state
 */
window.coraOpenSearchModal = function() {
    const modal = document.getElementById('cora-docs-search-modal');
    const input = document.getElementById('cora-docs-search-input');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Lock background scroll
        if (input) {
            input.value = '';
            input.focus();
        }
        coraRenderRecentOrEmptyResults();
    }
};

/**
 * Close search modal
 */
window.coraCloseSearchModal = function() {
    const modal = document.getElementById('cora-docs-search-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = ''; // Unlock scroll
    }
};

/**
 * Render quick links navigation when input field is empty
 */
function coraRenderRecentOrEmptyResults() {
    const container = document.getElementById('cora-docs-search-results-list');
    if (!container) return;
    
    container.innerHTML = `
        <div class="p-6 text-center select-none">
            <div class="text-[10px] text-zinc-400 uppercase font-bold tracking-wider mb-3">Quick Shortcuts</div>
            <div class="grid grid-cols-2 gap-2.5 max-w-sm mx-auto">
                <button onclick="coraSelectQuickLink('platform-overview')" class="flex items-center gap-2 p-2 rounded-lg border border-zinc-200/80 hover:bg-zinc-50 text-left text-xs text-zinc-700 cursor-pointer bg-white transition-colors">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                    <span class="font-medium">Overview</span>
                </button>
                <button onclick="coraSelectQuickLink('workspace-configuration')" class="flex items-center gap-2 p-2 rounded-lg border border-zinc-200/80 hover:bg-zinc-50 text-left text-xs text-zinc-700 cursor-pointer bg-white transition-colors">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    <span class="font-medium">Config Guide</span>
                </button>
                <button onclick="coraSelectQuickLink('api')" class="flex items-center gap-2 p-2 rounded-lg border border-zinc-200/80 hover:bg-zinc-50 text-left text-xs text-zinc-700 cursor-pointer bg-white transition-colors">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                    <span class="font-medium">API Registry</span>
                </button>
                <button onclick="coraSelectQuickLink('changelog')" class="flex items-center gap-2 p-2 rounded-lg border border-zinc-200/80 hover:bg-zinc-50 text-left text-xs text-zinc-700 cursor-pointer bg-white transition-colors">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span class="font-medium">Changelog</span>
                </button>
            </div>
        </div>
    `;
    window.currentSelectedIndex = -1;
}

/**
 * Handle selection of quick links
 */
window.coraSelectQuickLink = function(slug) {
    coraCloseSearchModal();
    if (slug === 'api' || slug === 'changelog') {
        coraPublicShowSection(slug);
    } else {
        coraPublicLoadPage(null, slug, null);
    }
};

/**
 * Render grouped search results lists
 */
function coraRenderSearchResults(groupedData, query) {
    const container = document.getElementById('cora-docs-search-results-list');
    if (!container) return;
    
    const categories = Object.keys(groupedData);
    let totalItems = 0;
    
    categories.forEach(cat => {
        totalItems += groupedData[cat].length;
    });
    
    if (totalItems === 0) {
        coraRenderNoResults(query);
        return;
    }
    
    let html = `<div class="py-2">`;
    let elementIndex = 0;
    
    categories.forEach(cat => {
        const items = groupedData[cat];
        if (items.length === 0) return;
        
        const catName = cat.charAt(0).toUpperCase() + cat.slice(1);
        
        html += `
            <div class="text-[9px] font-bold text-zinc-455 uppercase tracking-wider px-4 pt-3 pb-1 border-none select-none">
                ${catName}
            </div>
            <div class="space-y-0.5">
        `;
        
        items.forEach(item => {
            const highlightedTitle = highlightSearchTerm(item.title, query);
            const snippetText = item.snippet ? highlightSearchTerm(item.snippet, query) : '';
            
            html += `
                <a href="#" onclick="coraNavigateFromSearch(event, '${item.slug}')" data-slug="${item.slug}" data-index="${elementIndex}" class="cora-search-result-item flex items-start gap-3 px-4 py-2 mx-2 rounded-lg text-left text-xs text-zinc-650 hover:bg-zinc-50 hover:text-zinc-950 transition-all group">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450 mt-0.5 flex-shrink-0 transition-colors group-hover:text-zinc-950 ">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-zinc-800 group-hover:text-zinc-955 leading-tight">${highlightedTitle}</div>
                        ${snippetText ? `<div class="text-[10.5px] text-zinc-400 mt-1 line-clamp-1 truncate">${snippetText}</div>` : ''}
                    </div>
                    <!-- Selection Indicator chevron -->
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 opacity-0 group-hover:opacity-100 mt-1 transition-all">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            `;
            elementIndex++;
        });
        
        html += `</div>`;
    });
    
    html += `</div>`;
    container.innerHTML = html;
    
    // Select first matched item by default
    window.currentSelectedIndex = 0;
    coraHighlightResultItem(0);
}

/**
 * Highlights matches within result content text blocks
 */
function highlightSearchTerm(text, query) {
    if (!text || !query) return text || '';
    const escapedQuery = query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
    const regex = new RegExp(`(${escapedQuery})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
}

/**
 * Render empty state query message
 */
function coraRenderNoResults(query) {
    const container = document.getElementById('cora-docs-search-results-list');
    if (!container) return;
    
    container.innerHTML = `
        <div class="p-8 text-center space-y-2 select-none">
            <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-300 mx-auto">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="8" y1="11" x2="14" y2="11"></line>
            </svg>
            <h3 class="text-xs font-bold text-zinc-800 ">No results found</h3>
            <p class="text-[10.5px] text-zinc-450 max-w-xs mx-auto">We couldn't find matching titles or content for "${escapeHtml(query)}". Try verifying module names.</p>
        </div>
    `;
    window.currentSelectedIndex = -1;
}

/**
 * Highlight results visually via Arrow key transitions
 */
function coraHighlightResultItem(index) {
    const items = document.querySelectorAll('.cora-search-result-item');
    items.forEach((item, idx) => {
        if (idx === index) {
            item.classList.add('bg-zinc-50', 'text-zinc-955');
            item.classList.remove('text-zinc-650');
            const chevron = item.querySelector('svg:last-child');
            if (chevron) chevron.classList.remove('opacity-0');
            item.scrollIntoView({ block: 'nearest' });
        } else {
            item.classList.remove('bg-zinc-50', 'text-zinc-955');
            item.classList.add('text-zinc-650');
            const chevron = item.querySelector('svg:last-child');
            if (chevron) chevron.classList.add('opacity-0');
        }
    });
}

/**
 * Router transition callback triggered from search list item anchors
 */
window.coraNavigateFromSearch = function(event, slug) {
    if (event) event.preventDefault();
    coraCloseSearchModal();
    coraPublicLoadPage(null, slug, null);
};


/**
 * ── 3. Structural Helpers and View Renderers ─────────────────────────────
 */

/**
 * Resolves content area element or dynamically creates one inside main wrapper
 */
function resolveContentContainer() {
    let container = document.getElementById('cora-docs-content-area') 
                 || document.getElementById('cora-public-docs-content');
                 
    if (!container) {
        const main = document.querySelector('main');
        if (main) {
            let wrapper = main.querySelector('.cora-docs-content-wrapper');
            if (!wrapper) {
                wrapper = document.createElement('div');
                wrapper.className = 'cora-docs-content-wrapper flex-1 min-w-0';
                wrapper.id = 'cora-docs-content-area';
                main.insertBefore(wrapper, main.firstChild);
            }
            container = wrapper;
        }
    }
    return container;
}

/**
 * Removes active styling layout tokens from link elements
 */
function deactivateLink(link) {
    link.classList.remove('bg-zinc-950', 'text-white', 'font-semibold', 'font-medium');
    link.classList.add('text-zinc-650', 'hover:text-zinc-950', 'hover:bg-zinc-100/70');
}

/**
 * Adds active monochromatic highlight tokens to link element
 */
function activateLink(link) {
    link.classList.remove('text-zinc-650', 'text-zinc-655', 'hover:text-zinc-950', 'hover:text-zinc-955', 'hover:bg-zinc-100/70');
    link.classList.add('bg-zinc-950', 'text-white', 'font-semibold');
}

/**
 * Automatically opens/expands parent collapsible accordion panels
 */
function expandParentSidebarGroup(link) {
    const group = link.closest('.cora-sidebar-group');
    if (group) {
        const content = group.querySelector('.cora-sidebar-group-content');
        const chevron = group.querySelector('.cora-chevron-icon');
        if (content && (content.classList.contains('hidden') || content.style.maxHeight === '0px' || !content.style.maxHeight)) {
            content.classList.remove('hidden');
            content.style.maxHeight = content.scrollHeight + 'px';
            if (chevron) chevron.classList.add('rotate-180');
        }
    }
}

/**
 * Generates beautiful skeleton loading layouts
 */
function showSkeletonLoader(container) {
    container.innerHTML = `
        <div class="animate-pulse space-y-6 max-w-3xl select-none">
            <div class="h-3 w-1/4 bg-zinc-200 rounded"></div>
            <div class="h-8 w-2/3 bg-zinc-200 rounded-md mt-4"></div>
            <div class="flex gap-4 mt-2">
                <div class="h-3.5 w-24 bg-zinc-200 rounded"></div>
                <div class="h-3.5 w-32 bg-zinc-200 rounded"></div>
            </div>
            <hr class="border-t border-zinc-200/60 my-6" />
            <div class="space-y-3">
                <div class="h-4 w-full bg-zinc-200 rounded"></div>
                <div class="h-4 w-11/12 bg-zinc-200 rounded"></div>
                <div class="h-4 w-4/5 bg-zinc-200 rounded"></div>
            </div>
            <div class="space-y-2.5 pt-4">
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 bg-zinc-200 rounded-full"></div>
                    <div class="h-3.5 w-1/2 bg-zinc-200 rounded"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 bg-zinc-200 rounded-full"></div>
                    <div class="h-3.5 w-2/3 bg-zinc-200 rounded"></div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Replaces page contents and title headers
 */
function renderPageContent(container, data) {
    const pageUrl = window.location.origin + docsBaseUrl + data.slug;
    const catName = data.category.charAt(0).toUpperCase() + data.category.slice(1);
    
    container.innerHTML = `
        <article class="max-w-3xl w-full space-y-6">
            <!-- Breadcrumbs & Actions Row -->
            <div class="flex flex-wrap items-center justify-between gap-4 text-[11px] text-zinc-400 select-none">
                <div class="flex items-center gap-1.5 font-medium">
                    <span>Docs</span>
                    <span>/</span>
                    <span class="text-zinc-500 ">${catName}</span>
                    <span>/</span>
                    <span class="text-zinc-700 ">${data.title}</span>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button onclick="coraCopyDocLink('${pageUrl}', this)" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-600 hover:text-zinc-950 transition-colors bg-white font-bold cursor-pointer text-[10.5px]">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
                        <span>Copy Link</span>
                    </button>
                </div>
            </div>
            
            <!-- Title & Meta -->
            <div class="space-y-2 mt-4">
                <h1 class="text-3xl font-bold tracking-tight text-zinc-955 font-display">${data.title}</h1>
                <div class="flex items-center gap-3 text-xs text-zinc-450 ">
                    <div class="flex items-center gap-1.5 select-none">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Updated ${data.updated_at}</span>
                    </div>
                </div>
            </div>
            
            <hr class="border-t border-zinc-200/60 my-6" />
            
            <!-- Main Content Container -->
            <div class="prose max-w-none text-zinc-800 text-xs leading-relaxed space-y-4">
                ${data.html}
            </div>
        </article>
    `;
}

/**
 * Copies documentation URLs to clipboard
 */
window.coraCopyDocLink = function(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
        const span = btn.querySelector('span');
        const originalText = span.textContent;
        span.textContent = 'Copied!';
        
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Success', 'Documentation link copied.');
        }
        
        setTimeout(() => {
            span.textContent = originalText;
        }, 1500);
    });
};

/**
 * Renders static API reference list dynamically
 */
function renderApiRegistry(container) {
    if (!coraPublicApis || coraPublicApis.length === 0) {
        container.innerHTML = `
            <div class="p-8 border border-zinc-200/80 rounded-xl bg-white text-center select-none">
                <h2 class="text-xs font-bold text-zinc-900 ">API Registry Empty</h2>
                <p class="text-[10.5px] text-zinc-500 mt-2">No API endpoint schemas are registered on the platform yet.</p>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="space-y-6">
            <div class="space-y-2">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-955 font-display">API Endpoint Registry</h1>
                <p class="text-xs text-zinc-500 ">Complete reference of core REST API endpoints and local MCP gateway controllers.</p>
            </div>
            <hr class="border-t border-zinc-200/60 my-4" />
            <div class="space-y-4">
    `;
    
    coraPublicApis.forEach(api => {
        const methodColor = api.method === 'GET' 
            ? 'bg-zinc-100 border border-zinc-200 text-zinc-700 ' 
            : 'bg-zinc-950 text-white border border-zinc-950 ';
            
        const mcpBadge = api.mcp_compatible == 1
            ? `<span class="inline-flex items-center gap-1 text-[9px] font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200/60 px-1.5 py-0.5 rounded-full select-none">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> MCP Ready
               </span>`
            : '';
            
        html += `
            <div class="border border-zinc-200/80 rounded-xl bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="p-4 flex flex-wrap items-center justify-between gap-3 bg-zinc-50/50 border-b border-zinc-100 ">
                    <div class="flex items-center gap-2.5 font-mono text-xs">
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] tracking-wide ${methodColor}">${api.method}</span>
                        <span class="font-bold text-zinc-850 ">${api.path}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        ${mcpBadge}
                        <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-zinc-100 border border-zinc-200 text-zinc-500 uppercase">${api.permission_level}</span>
                    </div>
                </div>
                <div class="p-4 space-y-4">
                    <p class="text-xs leading-relaxed text-zinc-650 ">${api.description || 'No description provided.'}</p>
                    
                    ${api.required_permissions ? `
                        <div class="text-[11px] text-zinc-500 ">
                            <span class="font-semibold text-zinc-700 ">Required Scopes:</span> 
                            <code class="bg-zinc-100 border border-zinc-200 px-1.5 py-0.5 rounded text-zinc-800 font-mono text-[10px]">${api.required_permissions}</code>
                        </div>
                    ` : ''}
                    
                    ${api.request_schema && api.request_schema !== '{}' ? `
                        <div class="space-y-1.5">
                            <div class="text-[9px] font-bold text-zinc-450 uppercase tracking-wider">Request Schema</div>
                            <pre class="bg-zinc-950 text-zinc-50 p-3 rounded-lg font-mono text-[10.5px] border border-zinc-900 overflow-x-auto">${escapeHtml(api.request_schema)}</pre>
                        </div>
                    ` : ''}
                    
                    ${api.response_schema && api.response_schema !== '{}' ? `
                        <div class="space-y-1.5">
                            <div class="text-[9px] font-bold text-zinc-455 uppercase tracking-wider">Response Schema</div>
                            <pre class="bg-zinc-950 text-zinc-50 p-3 rounded-lg font-mono text-[10.5px] border border-zinc-900 overflow-x-auto">${escapeHtml(api.response_schema)}</pre>
                        </div>
                    ` : ''}
                    
                    ${api.example ? `
                        <div class="space-y-1.5">
                            <div class="text-[9px] font-bold text-zinc-450 uppercase tracking-wider">Usage Example</div>
                            <pre class="bg-zinc-950 text-zinc-50 p-3 rounded-lg font-mono text-[10.5px] border border-zinc-900 overflow-x-auto">${escapeHtml(api.example)}</pre>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    });
    
    html += `
            </div>
        </div>
    `;
    container.innerHTML = html;
}

/**
 * Renders static Changelog feed dynamically
 */
function renderChangelogFeed(container) {
    if (!coraPublicChangelogs || coraPublicChangelogs.length === 0) {
        container.innerHTML = `
            <div class="p-8 border border-zinc-200/80 rounded-xl bg-white text-center select-none">
                <h2 class="text-xs font-bold text-zinc-900 ">Changelog Feed Empty</h2>
                <p class="text-[10.5px] text-zinc-505 mt-2">No release history has been registered yet.</p>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="space-y-6">
            <div class="space-y-2">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-955 font-display">Changelog Feed</h1>
                <p class="text-xs text-zinc-500 ">Chronological history of features, bugfixes, and security integrations across the Cora Platform.</p>
            </div>
            <hr class="border-t border-zinc-200/60 my-4" />
            <div class="relative pl-6 border-l border-zinc-200 space-y-8 mt-6">
    `;
    
    coraPublicChangelogs.forEach(entry => {
        const dateStr = new Date(entry.created_at).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
        
        const moduleBadge = entry.module_key 
            ? `<span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-zinc-150/70 border border-zinc-200/80 text-zinc-650 select-none">${entry.module_key}</span>`
            : '';
            
        const ticketBadge = entry.ticket_id
            ? `<code class="text-[10px] font-mono text-zinc-400 hover:text-zinc-900 transition-colors">${entry.ticket_id}</code>`
            : '';
            
        html += `
            <div class="relative">
                <div class="absolute -left-[31px] top-1.5 w-2.5 h-2.5 rounded-full bg-zinc-950 border-2 border-white ring-4 ring-zinc-100 "></div>
                <div class="space-y-2.5">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-xs font-mono font-bold px-1.5 py-0.5 rounded bg-zinc-950 text-white ">v${entry.version}</span>
                        <h3 class="text-sm font-bold text-zinc-955 leading-none">${entry.title}</h3>
                        ${moduleBadge}
                        ${ticketBadge}
                        <span class="text-[10.5px] text-zinc-400 ml-auto select-none">${dateStr}</span>
                    </div>
                    <div class="text-xs leading-relaxed text-zinc-655 max-w-2xl bg-zinc-50/20 p-3 rounded-lg border border-zinc-200/60 shadow-sm">
                        ${entry.description || ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    html += `
            </div>
        </div>
    `;
    container.innerHTML = html;
}

/**
 * Renders static Cora AI chatbot panel dynamically
 */
function renderCoraAiChat(container) {
    container.innerHTML = `
        <div class="border border-zinc-200/80 rounded-xl bg-white overflow-hidden flex flex-col h-[550px] shadow-sm max-w-2xl">
            <div class="px-4 py-3.5 bg-zinc-50/50 border-b border-zinc-100 flex items-center justify-between select-none">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-950 ">
                        <path d="M9.81 2.863a.5.5 0 0 0-.955 0L7.335 6.757 3.441 8.277a.5.5 0 0 0 0 .955l3.894 1.52 1.52 3.894a.5.5 0 0 0 .955 0l1.52-3.894 3.894-1.52a.5.5 0 0 0 0-.955l-3.894-1.52-1.52-3.894zM19 14.5a.5.5 0 0 0-.955 0l-.608 1.558-1.558.608a.5.5 0 0 0 0 .955l1.558.608.608 1.558a.5.5 0 0 0 .955 0l.608-1.558 1.558-.608a.5.5 0 0 0 0-.955l-1.558-.608-.608-1.558z"/>
                    </svg>
                    <span class="font-bold text-sm text-zinc-900 font-display">Cora AI Copilot</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-mono text-zinc-400 font-bold uppercase tracking-wider">RAG Gateway Active</span>
                </div>
            </div>
            
            <div id="cora-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-zinc-50/10 scrollbar-thin">
                <div class="flex gap-3 max-w-[85%]">
                    <div class="w-6 h-6 rounded-full bg-zinc-950 flex items-center justify-center text-[10px] font-bold text-white select-none flex-shrink-0">AI</div>
                    <div class="bg-zinc-100 text-zinc-800 p-3 rounded-lg text-xs leading-relaxed border border-zinc-200/50 ">
                        Hello! I am Cora AI, your developer reference assistant. Ask me anything about Cora platform architecture, modules, roles, or API integrations.
                    </div>
                </div>
            </div>
            
            <div class="p-3 border-t border-zinc-100 bg-white ">
                <form id="cora-chat-form" onsubmit="coraSubmitChat(event)" class="flex gap-2">
                    <input type="text" id="cora-chat-input" class="flex-1 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs bg-white focus:border-zinc-950 focus:outline-none text-zinc-900 placeholder-zinc-400" placeholder="Ask a question about permissions, RAG layer setup, etc..." required autocomplete="off">
                    <button type="submit" class="px-4 py-1.5 bg-zinc-950 hover:bg-zinc-900 text-white font-bold rounded-lg transition-colors text-xs flex items-center gap-1.5 border border-zinc-900 cursor-pointer select-none">
                        <span>Send</span>
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-white ">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    `;
}

/**
 * Handles chatbot submission and RAG fallbacks
 */
window.coraSubmitChat = function(event) {
    if (event) event.preventDefault();
    
    const input = document.getElementById('cora-chat-input');
    const doubt = input.value.trim();
    if (!doubt) return;
    
    input.value = '';
    
    // Append user query to chat history
    const msgList = document.getElementById('cora-chat-messages');
    msgList.insertAdjacentHTML('beforeend', `
        <div class="flex gap-3 max-w-[85%] ml-auto justify-end">
            <div class="bg-zinc-955 text-white p-3 rounded-lg text-xs leading-relaxed border border-zinc-955 shadow-sm">
                ${escapeHtml(doubt)}
            </div>
            <div class="w-6 h-6 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center text-[10px] font-bold text-zinc-650 select-none flex-shrink-0">Me</div>
        </div>
    `);
    
    msgList.scrollTop = msgList.scrollHeight;
    
    // Add dynamic dot loading animation
    const typingId = 'cora-typing-' + Date.now();
    msgList.insertAdjacentHTML('beforeend', `
        <div id="${typingId}" class="flex gap-3 max-w-[85%] select-none">
            <div class="w-6 h-6 rounded-full bg-zinc-900 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">AI</div>
            <div class="bg-zinc-100 text-zinc-400 p-3.5 rounded-lg text-xs border border-zinc-200/50 flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
            </div>
        </div>
    `);
    msgList.scrollTop = msgList.scrollHeight;
    
    // Build query body
    const formData = new FormData();
    formData.append('action', 'cora_ajax_ask_llm_doubt');
    formData.append('doubt', doubt);
    formData.append('security', '<?php echo wp_create_nonce( "cora_ajax_nonce" ); ?>');
    
    fetch(ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();
        
        if (res.success && res.data && res.data.reply) {
            addAiMessage(msgList, res.data.reply);
        } else {
            const reply = coraSimulateRagReply(doubt);
            addAiMessage(msgList, reply);
        }
    })
    .catch(err => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();
        
        const reply = coraSimulateRagReply(doubt);
        addAiMessage(msgList, reply);
    });
};

/**
 * Appends AI message response
 */
function addAiMessage(msgList, text) {
    const formatted = formatMessageMarkdown(text);
    
    msgList.insertAdjacentHTML('beforeend', `
        <div class="flex gap-3 max-w-[85%]">
            <div class="w-6 h-6 rounded-full bg-zinc-900 flex items-center justify-center text-[10px] font-bold text-white select-none flex-shrink-0">AI</div>
            <div class="bg-zinc-100 text-zinc-800 p-3 rounded-lg text-xs leading-relaxed border border-zinc-200/50 shadow-sm">
                ${formatted}
            </div>
        </div>
    `);
    msgList.scrollTop = msgList.scrollHeight;
}

/**
 * Escapes tags to prevent input injections
 */
function escapeHtml(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

/**
 * Simple client-side markdown interpreter for chatbot responses
 */
function formatMessageMarkdown(text) {
    let html = escapeHtml(text);
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-zinc-950 ">$1</strong>');
    html = html.replace(/\`(.*?)\`/g, '<code class="font-mono text-[10.5px] px-1 bg-zinc-200/60 text-zinc-900 rounded">$1</code>');
    html = html.replace(/^\- (.*?)$/gm, '<li class="ml-4 list-disc">$1</li>');
    html = html.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="#" onclick="coraSelectQuickLink(\'$2\')" class="underline font-semibold hover:text-zinc-950 ">$1</a>');
    return html.replace(/\n/g, '<br>');
}

/**
 * High-fidelity client-side RAG fallback simulation over pages collection
 */
function coraSimulateRagReply(doubt) {
    const terms = doubt.toLowerCase().split(/\s+/).filter(t => t.length > 2);
    if (terms.length === 0) {
        return "I'm sorry, I couldn't interpret your query. Could you please specify a module name or feature capability?";
    }
    
    let bestMatch = null;
    let maxMatches = 0;
    
    coraPublicPages.forEach(page => {
        let matches = 0;
        const titleLower = page.title.toLowerCase();
        const contentLower = page.content.toLowerCase();
        
        terms.forEach(term => {
            if (titleLower.includes(term)) matches += 6;
            if (contentLower.includes(term)) matches += 1;
        });
        
        if (matches > maxMatches) {
            maxMatches = matches;
            bestMatch = page;
        }
    });
    
    if (bestMatch && maxMatches > 0) {
        const content = bestMatch.content;
        const cleanContent = content.replace(/[#*`\-]/g, '');
        let snippet = '';
        
        let pos = -1;
        for (let i = 0; i < terms.length; i++) {
            const index = cleanContent.toLowerCase().indexOf(terms[i]);
            if (index !== -1) {
                pos = index;
                break;
            }
        }
        
        if (pos !== -1) {
            const start = Math.max(0, pos - 40);
            snippet = cleanContent.substr(start, 180);
            if (start > 0) snippet = '...' + snippet;
            if (cleanContent.length > start + 180) snippet += '...';
        } else {
            snippet = cleanContent.substr(0, 180) + '...';
        }
        
        return `Based on our documentation for **${bestMatch.title}**:\n\n"${snippet.trim()}"\n\nYou can read the full documentation page [here](${bestMatch.slug}) or look up details under the **${bestMatch.category}** category index.`;
    }
    
    return `I searched the local RAG documentation store but couldn't find matching segments. Try searching for **User Management**, **Invoices**, or **MCP Gateway** to locate the desired workspace capabilities.`;
}


/**
 * ── 4. Global Event Handlers and Initialization ─────────────────────────
 */

/**
 * Handle backdrop overlay dismissal clicks
 */
document.addEventListener('click', (e) => {
    const backdrop = document.getElementById('cora-docs-search-backdrop');
    if (e.target === backdrop) {
        coraCloseSearchModal();
    }
});

/**
 * Global Keyboard Listeners (Esc, ⌘K, Arrow keys, Enter)
 */
document.addEventListener('keydown', (e) => {
    // Escape key closes modal
    if (e.key === 'Escape') {
        const modal = document.getElementById('cora-docs-search-modal');
        if (modal && !modal.classList.contains('hidden')) {
            coraCloseSearchModal();
            e.preventDefault();
        }
        return;
    }
    
    // Command+K or Control+K opens modal
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        coraOpenSearchModal();
        return;
    }
    
    // Rest of operations only focus inside active search modal
    const modal = document.getElementById('cora-docs-search-modal');
    if (!modal || modal.classList.contains('hidden')) return;
    
    const items = document.querySelectorAll('.cora-search-result-item');
    if (items.length === 0) return;
    
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        window.currentSelectedIndex++;
        if (window.currentSelectedIndex >= items.length) {
            window.currentSelectedIndex = 0;
        }
        coraHighlightResultItem(window.currentSelectedIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        window.currentSelectedIndex--;
        if (window.currentSelectedIndex < 0) {
            window.currentSelectedIndex = items.length - 1;
        }
        coraHighlightResultItem(window.currentSelectedIndex);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (window.currentSelectedIndex >= 0 && window.currentSelectedIndex < items.length) {
            const activeItem = items[window.currentSelectedIndex];
            const slug = activeItem.getAttribute('data-slug');
            coraNavigateFromSearch(null, slug);
        }
    }
});

/**
 * Resolve slugs on browser forward/backward buttons
 */
window.addEventListener('popstate', (e) => {
    if (e.state && e.state.slug) {
        coraPublicLoadPage(null, e.state.slug, null, false);
    } else if (e.state && e.state.section) {
        coraPublicShowSection(e.state.section, false);
    } else {
        const slug = getSlugFromUrl();
        if (slug === 'api' || slug === 'changelog' || slug === 'cora-ai') {
            coraPublicShowSection(slug, false);
        } else if (slug) {
            coraPublicLoadPage(null, slug, null, false);
        } else {
            coraPublicLoadPage(null, 'platform-overview', null, false);
        }
    }
});

/**
 * Helper to parse slug segment from address URL pathname
 */
function getSlugFromUrl() {
    const path = window.location.pathname;
    const parts = path.split('/').filter(p => p.length > 0);
    const docsIndex = parts.indexOf('docs');
    if (docsIndex !== -1 && parts.length > docsIndex + 1) {
        return parts[parts.length - 1];
    }
    return '';
}

/**
 * Initial route check on page DOM complete
 */
document.addEventListener('DOMContentLoaded', () => {
    const slug = getSlugFromUrl();
    if (slug === 'api' || slug === 'changelog' || slug === 'cora-ai') {
        coraPublicShowSection(slug, false);
    } else if (slug) {
        // Hydrate active sidebar selection classes on load
        const activeLink = document.querySelector(`.cora-nav-link[data-slug="${slug}"]`);
        if (activeLink) {
            activateLink(activeLink);
            expandParentSidebarGroup(activeLink);
        }
    } else {
        // Fallback default
        const activeLink = document.querySelector('.cora-nav-link[data-slug="platform-overview"]');
        if (activeLink) {
            activateLink(activeLink);
        }
    }
});
</script>

<!-- Ask Cora AI sliding right sidebar container -->
<div id="cora-public-ai-sidebar" class="fixed top-0 right-0 bottom-0 w-[420px] bg-white border-l border-zinc-200 z-50 shadow-2xl transition-transform duration-300 transform translate-x-full flex flex-col font-sans">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-zinc-100 flex items-center justify-between select-none shrink-0 bg-white">
        <div class="flex items-center gap-2.5">
            <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-950 flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
            </span>
            <span class="font-bold text-sm text-zinc-950 font-display">Ask Cora AI</span>
            <span class="flex items-center gap-1 px-1.5 py-0.5 rounded bg-green-50 text-green-700 text-[9px] font-bold border border-green-200/30">
                <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span>
                RAG Online
            </span>
        </div>
        <button onclick="window.coraToggleAiSidebar(false)" class="p-1 rounded-lg text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 transition-all cursor-pointer border-none bg-transparent" title="Close Sidebar">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Scrollable Chat Area -->
    <div id="cora-sidebar-messages-container" class="flex-1 overflow-y-auto p-5 space-y-6 bg-zinc-50/10 scrollbar-thin">
        
        <!-- Welcome view including greetings, suggested actions, and hub grids -->
        <div id="cora-sidebar-welcome-view" class="space-y-6">
            <!-- Welcome Greeting -->
            <div class="space-y-1.5 select-none pt-2">
                <h3 class="text-xl font-bold text-zinc-900 tracking-tight">Hi Dravya! 👋</h3>
                <p class="text-xs text-zinc-500 leading-relaxed font-semibold">
                    I'm Cora AI, your documentation assistant.<br>
                    How can I help you today?
                </p>
            </div>
            
            <!-- Quick Questions list -->
            <div class="space-y-2">
                <button onclick="window.coraSubmitSidebarQuickQuery('Explain the authentication flow')" class="w-full text-left p-3.5 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl text-xs text-zinc-700 hover:text-zinc-950 hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs font-semibold flex items-center justify-between group">
                    <span class="flex items-center gap-2.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-800 transition-colors"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        <span>Explain the authentication flow</span>
                    </span>
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-800 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
                <button onclick="window.coraSubmitSidebarQuickQuery('How do I create a new invoice?')" class="w-full text-left p-3.5 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl text-xs text-zinc-700 hover:text-zinc-950 hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs font-semibold flex items-center justify-between group">
                    <span class="flex items-center gap-2.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-800 transition-colors"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>How do I create a new invoice?</span>
                    </span>
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-800 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
                <button onclick="window.coraSubmitSidebarQuickQuery('Show me API rate limits')" class="w-full text-left p-3.5 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl text-xs text-zinc-700 hover:text-zinc-950 hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs font-semibold flex items-center justify-between group">
                    <span class="flex items-center gap-2.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-800 transition-colors"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>Show me API rate limits</span>
                    </span>
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-800 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
                <button onclick="window.coraSubmitSidebarQuickQuery('What permissions does a crew member have?')" class="w-full text-left p-3.5 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl text-xs text-zinc-700 hover:text-zinc-950 hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs font-semibold flex items-center justify-between group">
                    <span class="flex items-center gap-2.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-800 transition-colors"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <span>What permissions does a crew member have?</span>
                    </span>
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-800 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
            
            <!-- Reference Hub Grid inside welcome container -->
            <div class="grid grid-cols-2 gap-4 pt-2">
                <!-- Popular Topics -->
                <div class="space-y-2">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">Popular Topics</span>
                    <div class="space-y-2">
                        <a href="#" onclick="window.coraSidebarTopicLink(event, 'platform-overview')" class="flex items-center gap-2.5 p-2 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl transition-all group">
                            <span class="p-1 bg-zinc-100 text-zinc-600 rounded-lg group-hover:bg-zinc-950 group-hover:text-white transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" /></svg>
                            </span>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-zinc-850 text-[11px] truncate group-hover:text-zinc-955 transition-colors">Getting Started</span>
                                <span class="text-[9px] text-zinc-400 truncate">New to Cora? Start here</span>
                            </div>
                        </a>
                        <a href="#" onclick="window.coraSidebarTopicLink(event, 'workspace-roles')" class="flex items-center gap-2.5 p-2 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl transition-all group">
                            <span class="p-1 bg-zinc-100 text-zinc-600 rounded-lg group-hover:bg-zinc-955 group-hover:text-white transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
                            </span>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-zinc-850 text-[11px] truncate group-hover:text-zinc-955 transition-colors">Authentication</span>
                                <span class="text-[9px] text-zinc-400 truncate">API keys, tokens and security</span>
                            </div>
                        </a>
                        <a href="#" onclick="window.coraSidebarTopicLink(event, 'api')" class="flex items-center gap-2.5 p-2 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl transition-all group">
                            <span class="p-1 bg-zinc-100 text-zinc-600 rounded-lg group-hover:bg-zinc-955 group-hover:text-white transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                            </span>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-zinc-850 text-[11px] truncate group-hover:text-zinc-955 transition-colors">API Reference</span>
                                <span class="text-[9px] text-zinc-400 truncate">Complete endpoint documentation</span>
                            </div>
                        </a>
                        <a href="#" onclick="window.coraSidebarTopicLink(event, 'pwa-push-notifications')" class="flex items-center gap-2.5 p-2 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl transition-all group">
                            <span class="p-1 bg-zinc-100 text-zinc-600 rounded-lg group-hover:bg-zinc-955 group-hover:text-white transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </span>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-zinc-850 text-[11px] truncate group-hover:text-zinc-955 transition-colors">Webhook Events</span>
                                <span class="text-[9px] text-zinc-400 truncate">Real-time event notifications</span>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- Recently Viewed -->
                <div class="space-y-2">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">Recently Viewed</span>
                    <div class="border border-zinc-150 bg-white rounded-xl divide-y divide-zinc-100 overflow-hidden shadow-3xs">
                        <div class="flex items-center justify-between p-2.5 text-xs hover:bg-zinc-50/50 transition-colors">
                            <span class="font-bold text-zinc-800 text-[11px] truncate pr-1">/api/v1/auth/login</span>
                            <span class="text-[9px] text-zinc-450 shrink-0">Just now</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 text-xs hover:bg-zinc-50/50 transition-colors">
                            <span class="font-bold text-zinc-800 text-[11px] truncate pr-1">Invoices Engine</span>
                            <span class="text-[9px] text-zinc-450 shrink-0">10m ago</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 text-xs hover:bg-zinc-50/50 transition-colors">
                            <span class="font-bold text-zinc-800 text-[11px] truncate pr-1">Crew Scheduler</span>
                            <span class="text-[9px] text-zinc-450 shrink-0">1h ago</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 text-xs hover:bg-zinc-50/50 transition-colors">
                            <span class="font-bold text-zinc-800 text-[11px] truncate pr-1">Webhook Events</span>
                            <span class="text-[9px] text-zinc-450 shrink-0">2h ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Input Area -->
    <div class="p-4 border-t border-zinc-100 bg-white shrink-0">
        <form id="cora-sidebar-chat-form" onsubmit="window.coraSubmitSidebarChat(event)" class="relative flex items-center">
            <input type="text" id="cora-sidebar-chat-input" class="w-full border border-zinc-200 rounded-lg pl-3 pr-9 py-2.5 text-xs bg-zinc-50 focus:bg-white focus:border-zinc-950 outline-none text-zinc-900 transition-all placeholder-zinc-400 font-sans" placeholder="Ask anything about Cora..." required autocomplete="off">
            <button type="submit" id="cora-sidebar-chat-send-btn" class="absolute right-2 text-zinc-450 hover:text-zinc-900 transition-colors p-1.5 cursor-pointer bg-transparent border-none flex items-center justify-center" title="Send Doubt">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
        </form>
        <div class="mt-2.5 text-center text-[10px] text-zinc-400 select-none">
            Powered by Cora AI &bull; Using docs v2.2.1
        </div>
    </div>
</div>

<script>
window.coraToggleAiSidebar = function(show) {
    if (window.innerWidth >= 1024) {
        // Desktop: Toggle inline sidebar column visibility
        const inlineSidebar = document.getElementById('cora-docs-widgets-column');
        if (inlineSidebar) {
            if (show) {
                inlineSidebar.classList.remove('hidden');
                inlineSidebar.classList.add('lg:flex');
                setTimeout(() => {
                    const input = document.getElementById('cora-inline-chat-input');
                    if (input) {
                        input.focus();
                        input.classList.add('border-zinc-950', 'ring-2', 'ring-zinc-950/10');
                        setTimeout(() => {
                            input.classList.remove('border-zinc-950', 'ring-2', 'ring-zinc-950/10');
                        }, 800);
                    }
                }, 100);
            } else {
                inlineSidebar.classList.remove('lg:flex');
                inlineSidebar.classList.add('hidden');
            }
        }
        return;
    }

    // Mobile: Toggle sliding sidebar drawer
    const sidebar = document.getElementById('cora-public-ai-sidebar');
    if (!sidebar) return;
    if (show) {
        sidebar.classList.remove('translate-x-full');
        sidebar.classList.add('translate-x-0');
        setTimeout(() => {
            const input = document.getElementById('cora-sidebar-chat-input');
            if (input) input.focus();
        }, 300);
    } else {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('translate-x-full');
    }
};

window.coraSubmitSidebarQuickQuery = function(query) {
    const input = document.getElementById('cora-sidebar-chat-input');
    if (input) {
        input.value = query;
        window.coraSubmitSidebarChat();
    }
};

window.coraSidebarTopicLink = function(e, slug) {
    if (e) e.preventDefault();
    window.coraToggleAiSidebar(false);
    if (slug === 'api' || slug === 'changelog') {
        coraPublicShowSection(slug);
    } else {
        coraPublicLoadPage(null, slug, null);
    }
};

window.coraSubmitSidebarChat = function(event) {
    if (event) event.preventDefault();
    
    const input = document.getElementById('cora-sidebar-chat-input');
    const sendBtn = document.getElementById('cora-sidebar-chat-send-btn');
    const doubt = input.value.trim();
    if (!doubt) return;
    
    input.value = '';
    input.disabled = true;
    if (sendBtn) sendBtn.disabled = true;
    
    // Append user query to chat history
    const msgList = document.getElementById('cora-sidebar-messages-container');
    msgList.insertAdjacentHTML('beforeend', `
        <div class="flex gap-3 max-w-[90%] ml-auto justify-end">
            <div class="bg-zinc-950 text-white p-3 rounded-lg text-xs leading-relaxed border border-zinc-950 shadow-xs">
                ${escapeHtml(doubt)}
            </div>
            <div class="w-6 h-6 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center text-[10px] font-bold text-zinc-650 select-none flex-shrink-0">Me</div>
        </div>
    `);
    
    msgList.scrollTop = msgList.scrollHeight;
    
    // Add dynamic dot loading animation
    const typingId = 'cora-sidebar-typing-' + Date.now();
    msgList.insertAdjacentHTML('beforeend', `
        <div id="${typingId}" class="flex gap-3 max-w-[90%] select-none">
            <div class="w-6 h-6 rounded-full bg-zinc-950 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">AI</div>
            <div class="bg-zinc-100 text-zinc-400 p-3 rounded-lg text-xs border border-zinc-200/55 flex items-center gap-1 shadow-3xs">
                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
            </div>
        </div>
    `);
    msgList.scrollTop = msgList.scrollHeight;
    
    // Build query body
    const formData = new FormData();
    formData.append('action', 'cora_ajax_ask_llm_doubt');
    formData.append('doubt', doubt);
    formData.append('security', '<?php echo wp_create_nonce( "cora_ajax_nonce" ); ?>');
    
    fetch(ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();
        
        input.disabled = false;
        if (sendBtn) sendBtn.disabled = false;
        input.focus();
        
        if (res.success && res.data && res.data.reply) {
            addSidebarAiMessage(msgList, res.data.reply);
        } else {
            const reply = coraSimulateRagReply(doubt);
            addSidebarAiMessage(msgList, reply);
        }
    })
    .catch(err => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();
        
        input.disabled = false;
        if (sendBtn) sendBtn.disabled = false;
        input.focus();
        
        const reply = coraSimulateRagReply(doubt);
        addSidebarAiMessage(msgList, reply);
    });
};

function addSidebarAiMessage(msgList, text) {
    const formatted = formatMessageMarkdown(text);
    
    msgList.insertAdjacentHTML('beforeend', `
        <div class="flex gap-3 max-w-[90%]">
            <div class="w-6 h-6 rounded-full bg-zinc-950 flex items-center justify-center text-[10px] font-bold text-white select-none flex-shrink-0">AI</div>
            <div class="bg-zinc-100 text-zinc-800 p-3 rounded-lg text-xs leading-relaxed border border-zinc-200/50 shadow-xs">
                ${formatted}
            </div>
        </div>
    `);
    msgList.scrollTop = msgList.scrollHeight;
}
</script>

