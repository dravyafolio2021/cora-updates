<?php
/**
 * Workspace Media Vault Selector Modal (Level 03 Organism / Rule 12 & Rule 13 Compliant)
 * 
 * Provides a unified modal interface connecting every media trigger in the workspace
 * directly to the active Workspace Media Vault.
 * Supports:
 *  - Cover Image selection ('cover')
 *  - Featured Image / Thumbnail selection ('thumbnail')
 *  - Inline Media insertion into Quill ('inline')
 *  - Multi-Asset Responsive Gallery insertion ('gallery')
 * 
 * Strictly adheres to:
 *  - Monochromatic palette (zinc-50 through zinc-950)
 *  - Soft tonal surface selection without heavy outline styling (Rule 13)
 *  - Mobile bottom-up sheet and desktop centered modal (Rule 12)
 *  - Zero browser alert/confirm defaults; uses monochromatic Toast notifications (Rule 1)
 *  - Zero use of owner name (Rule 3)
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!-- Unified Workspace Media Vault Selector Modal -->
<div id="cora-workspace-media-selector-modal" class="hidden fixed inset-0 font-sans select-none flex items-center justify-center p-3 sm:p-6" style="z-index: 10000000 !important;" role="dialog" aria-modal="true" aria-labelledby="cora-media-selector-title">
    <!-- Dark Blurred Backdrop Overlay (Rule 12 Standard) -->
    <div id="cora-workspace-media-backdrop" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-xs transition-opacity duration-200" style="z-index: 10000000 !important;" onclick="coraCloseWorkspaceMediaSelector()"></div>

    <!-- Modal Dialog Window (Centered Dialog) -->
    <div class="relative w-full max-w-5xl h-[88vh] max-h-[820px] bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col overflow-hidden" style="z-index: 10000001 !important;" onclick="event.stopPropagation();">
        
        <!-- Mobile Drag Indicator Handle (Rule 12 Standard) -->
        <div class="flex sm:hidden items-center justify-center pt-2.5 pb-1 cursor-pointer shrink-0" onclick="coraCloseWorkspaceMediaSelector()">
            <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-700"></div>
        </div>

        <!-- Header -->
        <header class="flex items-center justify-between px-5 sm:px-6 py-3.5 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center border border-zinc-200/80 dark:border-zinc-700/80 shrink-0">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h3 id="cora-media-selector-title" class="text-sm font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Workspace Media Vault</h3>
                    <span id="cora-media-selector-mode-pill" class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700">Select Media</span>
                </div>
            </div>

            <button type="button" onclick="coraCloseWorkspaceMediaSelector()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer border-none bg-transparent" aria-label="Close Media Vault">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </header>

        <!-- Filter & Search Toolbar -->
        <div class="px-5 sm:px-6 py-3 border-b border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/70 dark:bg-zinc-900/60 shrink-0 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            
            <!-- Left Controls: Search + Folder Filter -->
            <div class="flex flex-1 items-center gap-2.5 min-w-0">
                <!-- Search Box -->
                <div class="relative flex-1 max-w-sm">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="cora-media-selector-search" placeholder="Search by title, filename, caption..." class="w-full pl-9 pr-8 py-1.5 text-xs bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-zinc-400 dark:focus:border-zinc-500 transition-all font-sans" oninput="coraMediaSelectorDebouncedSearch()">
                    <button type="button" id="cora-media-selector-clear-search" onclick="coraMediaSelectorClearSearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 border-none bg-transparent cursor-pointer p-0.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                <!-- Folder Filter Dropdown -->
                <div class="relative shrink-0">
                    <select id="cora-media-selector-folder" class="py-1.5 pl-3 pr-8 text-xs bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-zinc-400 dark:focus:border-zinc-500 cursor-pointer appearance-none font-sans" onchange="coraMediaSelectorFilterChange()">
                        <option value="">All Folders</option>
                        <option value="-1">Unorganized Assets</option>
                    </select>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>

            <!-- Right Controls: Media Type Pills + In-Modal Upload Button -->
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <!-- Media Type Segmented Control (Level 01 Atom) -->
                <div class="p-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg border border-zinc-200/80 dark:border-zinc-700 flex items-center text-xs font-semibold">
                    <button type="button" data-type="all" onclick="coraMediaSelectorSetType('all', this)" class="cora-media-type-btn px-2.5 py-1 rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-3xs font-bold transition-all cursor-pointer border-none">All</button>
                    <button type="button" data-type="image" onclick="coraMediaSelectorSetType('image', this)" class="cora-media-type-btn px-2.5 py-1 rounded-md text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-all cursor-pointer border-none bg-transparent">Images</button>
                    <button type="button" data-type="video" onclick="coraMediaSelectorSetType('video', this)" class="cora-media-type-btn px-2.5 py-1 rounded-md text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-all cursor-pointer border-none bg-transparent">Videos</button>
                    <button type="button" data-type="document" onclick="coraMediaSelectorSetType('document', this)" class="cora-media-type-btn px-2.5 py-1 rounded-md text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-all cursor-pointer border-none bg-transparent">Documents</button>
                </div>

                <!-- Hidden file input for in-modal upload -->
                <input type="file" id="cora-media-selector-file-input" class="hidden" multiple accept="image/*,video/*,application/pdf" onchange="coraMediaSelectorHandleUpload(this.files)">

                <!-- In-modal Upload to Workspace Button -->
                <button type="button" id="cora-media-selector-upload-btn" onclick="document.getElementById('cora-media-selector-file-input').click()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs font-semibold shadow-3xs transition-all cursor-pointer active:scale-97">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <span>Upload to Vault</span>
                </button>
                <span id="cora-media-selector-upload-status" class="hidden text-[11px] font-medium text-zinc-500 animate-pulse">Uploading...</span>
            </div>
        </div>

        <!-- Media Grid Area -->
        <div id="cora-media-selector-grid-wrap" class="flex-1 overflow-y-auto p-4 sm:p-5 bg-zinc-50/40 dark:bg-zinc-950/40">
            <div id="cora-media-selector-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                <!-- Assets dynamically rendered via JS -->
            </div>
        </div>

        <!-- Footer -->
        <footer class="flex items-center justify-between px-5 sm:px-6 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0">
            <div class="flex items-center gap-2.5">
                <span id="cora-media-selector-counter" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">0 assets selected</span>
                <button type="button" id="cora-media-selector-clear-selection-btn" onclick="coraMediaSelectorClearSelection()" class="hidden text-[11px] font-medium text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 underline cursor-pointer border-none bg-transparent">Clear</button>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="coraCloseWorkspaceMediaSelector()" class="px-3.5 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors text-xs cursor-pointer shadow-3xs">
                    Cancel
                </button>
                <button type="button" id="cora-media-selector-confirm-btn" onclick="coraConfirmWorkspaceMediaSelection()" disabled class="px-4 py-2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 font-bold rounded-lg hover:bg-zinc-800 dark:hover:bg-white transition-all text-xs cursor-pointer shadow-sm disabled:opacity-40 disabled:cursor-not-allowed">
                    Use Selected Asset
                </button>
            </div>
        </footer>
    </div>
</div>
