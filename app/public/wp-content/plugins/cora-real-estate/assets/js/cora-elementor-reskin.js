/**
 * Cora Elementor Reskin JS
 * Intercepts Elementor UI events and styles the panel widgets to match Studio Minimalist.
 */
function initCoraReskin() {
    // Override the default browser alert inside the editor iframe
    window.alert = function(msg) {
        if (window.parent && window.parent.coraShowToast) {
            window.parent.coraShowToast(msg);
        } else {
            console.log("Intercepted Elementor Alert: ", msg);
        }
    };

    let observer;
    
    // Inject components and format elements list dynamically
    const renameTabs = () => {
        const tabs = document.querySelectorAll('.elementor-panel-navigation-tab');
        tabs.forEach(tab => {
            const text = tab.textContent.trim();
            if (text === 'Elements') {
                tab.textContent = 'Widgets';
            } else if (text === 'Global' || text === 'Globals') {
                tab.textContent = 'Globals';
            }
        });
        
        // Add a "Components" tab between Widgets and Globals if not already present
        const nav = document.getElementById('elementor-panel-elements-navigation');
        if (nav && !document.getElementById('cora-injected-components-tab')) {
            const compTab = document.createElement('button');
            compTab.id = 'cora-injected-components-tab';
            compTab.className = 'elementor-component-tab elementor-panel-navigation-tab';
            compTab.textContent = 'Components';
            compTab.onclick = function() {
                if (window.parent && window.parent.coraShowToast) {
                    window.parent.coraShowToast('Components are loaded from the active Theme template libraries.');
                }
            };
            const firstTab = nav.firstElementChild;
            if (firstTab && firstTab.nextSibling) {
                nav.insertBefore(compTab, firstTab.nextSibling);
            } else {
                nav.appendChild(compTab);
            }
        }
    };

    const injectElementsTitle = () => {
        const nav = document.getElementById('elementor-panel-elements-navigation');
        if (nav && !document.getElementById('cora-injected-elements-title')) {
            const titleContainer = document.createElement('div');
            titleContainer.id = 'cora-injected-elements-title';
            titleContainer.className = 'cora-injected-elements-title';
            titleContainer.textContent = 'Elements';
            nav.parentNode.insertBefore(titleContainer, nav);
        }
    };

    const customizeCategories = () => {
        const headings = document.querySelectorAll('.elementor-panel-category-title');
        headings.forEach(heading => {
            const titleSpan = heading.querySelector('.elementor-panel-heading-title');
            if (titleSpan) {
                const titleText = titleSpan.textContent.trim();
                // Match either "Basic" or our already altered title, avoiding loops
                if (titleText === 'Basic' || titleText.includes('Atomic Elements')) {
                    if (!titleSpan.querySelector('.cora-injected-new-badge')) {
                        titleSpan.innerHTML = `
                            Atomic Elements
                            <span class="cora-injected-new-badge">New</span>
                            <span class="cora-injected-info-icon">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            </span>
                        `;
                    }
                }
            }
        });
    };

    const customizeWidgets = () => {
        const elements = document.querySelectorAll('.elementor-element');
        elements.forEach(el => {
            const titleEl = el.querySelector('.title');
            if (!titleEl) return;
            
            let titleText = titleEl.textContent.trim();
            const iconContainer = el.querySelector('.icon');
            if (!iconContainer) return;
            
            // Map title names matching Webflow atomic widgets
            if (titleText === 'Container') {
                titleText = 'Div block';
                titleEl.textContent = 'Div block';
            } else if (titleText === 'Inner Section') {
                titleText = 'Flexbox';
                titleEl.textContent = 'Flexbox';
            } else if (titleText === 'Text Editor') {
                titleText = 'Paragraph';
                titleEl.textContent = 'Paragraph';
            } else if (titleText === 'Icon') {
                titleText = 'SVG';
                titleEl.textContent = 'SVG';
            } else if (titleText === 'Video') {
                titleText = 'YouTube';
                titleEl.textContent = 'YouTube';
            }
            
            // Avoid duplicate icon injection
            if (iconContainer.classList.contains('cora-injected-icon')) return;
            iconContainer.classList.add('cora-injected-icon');
            
            let svg = '';
            switch (titleText.toLowerCase()) {
                case 'div block':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>`;
                    break;
                case 'flexbox':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="7" height="18" rx="1"></rect><rect x="14" y="3" width="7" height="18" rx="1"></rect></svg>`;
                    break;
                case 'grid':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>`;
                    break;
                case 'tabs':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="3" x2="9" y2="9"></line></svg>`;
                    break;
                case 'heading':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M6 4v16M18 4v16M6 12h12"></path></svg>`;
                    break;
                case 'image':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>`;
                    break;
                case 'paragraph':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="21" y1="6" x2="3" y2="6"></line><line x1="15" y1="12" x2="3" y2="12"></line><line x1="17" y1="18" x2="3" y2="18"></line></svg>`;
                    break;
                case 'svg':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="12 8 8 12 12 16 16 12 12 8"></polygon></svg>`;
                    break;
                case 'button':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="6" width="18" height="12" rx="2"></rect><path d="M8 12h8"></path></svg>`;
                    break;
                case 'youtube':
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>`;
                    break;
                default:
                    svg = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>`;
                    break;
            }
            iconContainer.innerHTML = svg;
        });
    };

    const runFormattingOverrides = () => {
        if (observer) {
            observer.disconnect();
        }
        
        renameTabs();
        injectElementsTitle();
        customizeCategories();
        customizeWidgets();
        
        if (observer) {
            observer.observe(document.body, { childList: true, subtree: true });
        }
    };

    // Watch for dynamic updates inside Elementor panel wrapper
    observer = new MutationObserver(function(mutations) {
        runFormattingOverrides();
        
        // Hide standard upsell dialogue widgets
        const dialogs = document.querySelectorAll('.elementor-dialog-widget-promotion, .elementor-dialog-pro-badge');
        dialogs.forEach(dialog => {
            if (dialog.style.display !== 'none') {
                dialog.style.display = 'none';
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    // Initial load run
    runFormattingOverrides();

    // Wait for Elementor topbar to render, then inject custom breadcrumbs and exit button
    const checkTopbar = setInterval(() => {
        const topbar = document.querySelector('#elementor-editor-wrapper-v2 header, #editor-one-top-bar, #e-top-bar, .e-top-bar');
        if (topbar) {
            clearInterval(checkTopbar);
            
            if (!document.getElementById('cora-editor-injected-nav')) {
                const navContainer = document.createElement('div');
                navContainer.id = 'cora-editor-injected-nav';
                navContainer.className = 'cora-editor-injected-nav';
                
                const exitBtn = document.createElement('button');
                exitBtn.className = 'cora-injected-exit-btn';
                exitBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Theme Dashboard
                `;
                exitBtn.onclick = function() {
                    if (window.parent && window.parent.closeElementorEditor) {
                        window.parent.closeElementorEditor();
                    }
                };

                const separator = document.createElement('div');
                separator.className = 'cora-injected-separator';

                const breadcrumbs = document.createElement('div');
                breadcrumbs.className = 'cora-injected-breadcrumbs';
                
                const themeTitle = (window.parent && window.parent.canvasState && window.parent.canvasState.activeThemeName) 
                    ? window.parent.canvasState.activeThemeName 
                    : 'Theme';
                const pageTitle = (window.parent && window.parent.document.getElementById('editor-page-title')) 
                    ? window.parent.document.getElementById('editor-page-title').innerText 
                    : 'Page';

                breadcrumbs.innerHTML = `
                    <span>Canvas</span>
                    <span>/</span>
                    <span class="cora-injected-crumb-theme">${themeTitle}</span>
                    <span>/</span>
                    <span class="cora-injected-crumb-page">${pageTitle}</span>
                `;

                navContainer.appendChild(exitBtn);
                navContainer.appendChild(separator);
                navContainer.appendChild(breadcrumbs);

                topbar.insertBefore(navContainer, topbar.firstChild);
            }
        }
    }, 200);

    // Auto-click "Take Over" modal button if it appears
    const checkTakeOver = setInterval(() => {
        const dialogButtons = document.querySelectorAll('button');
        dialogButtons.forEach(btn => {
            if (btn.textContent.trim() === 'Take Over') {
                btn.click();
            }
        });
    }, 500);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCoraReskin);
} else {
    initCoraReskin();
}
