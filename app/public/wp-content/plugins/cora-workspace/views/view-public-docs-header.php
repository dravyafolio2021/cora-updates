<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<header class="sticky top-0 z-50 w-full bg-white/95 backdrop-blur-md border-b border-zinc-200/80 transition-colors duration-200 select-none">
    <div class="max-w-7xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between gap-4">
        <!-- Left Section: Branding -->
        <div class="flex items-center gap-3">
            <!-- Hamburger Menu Button (Mobile Only) -->
            <button id="cora-docs-mobile-menu-btn" onclick="coraToggleMobileSidebar()" class="md:hidden text-zinc-600 hover:text-zinc-950 p-1 bg-transparent border-none cursor-pointer flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            <a href="<?php echo esc_url( home_url( '/docs' ) ); ?>" class="flex items-center gap-2.5 text-zinc-950 hover:opacity-90 transition-opacity">
                <!-- SVG book icon -->
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-950 hidden sm:block">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span class="font-bold text-base tracking-tight font-display">Cora</span>
            </a>
            <span class="text-zinc-300 hidden md:inline">/</span>
            <span class="text-zinc-500 font-normal text-xs tracking-tight hidden md:inline">Developer Docs</span>
            <!-- Version pill -->
            <span class="hidden md:inline-block text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-zinc-100 border border-zinc-200 text-zinc-600 ">v2.2.1</span>
        </div>

        <!-- Center Section: Search box -->
        <div class="flex-1 max-w-sm flex justify-end md:justify-center">
            <!-- Desktop Search Button -->
            <button onclick="window.coraOpenSearchModal()" class="hidden md:flex w-full items-center justify-between gap-3 px-3 py-1.5 rounded-lg border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100/50 text-zinc-400 hover:text-zinc-500 transition-all text-xs text-left cursor-pointer group focus:outline-none">
                <div class="flex items-center gap-2">
                    <!-- SVG Search Icon -->
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 group-hover:text-zinc-500 transition-colors">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Search docs...</span>
                </div>
                <!-- Shortcut Indicator -->
                <kbd class="pointer-events-none inline-flex h-5 select-none items-center gap-0.5 rounded border border-zinc-250/60 bg-white px-1.5 font-mono text-[9px] font-medium text-zinc-400 shadow-sm">⌘K</kbd>
            </button>
            
            <!-- Mobile Search Icon -->
            <button onclick="window.coraOpenSearchModal()" class="md:hidden flex items-center justify-center p-1.5 rounded-lg border border-zinc-200 bg-zinc-50/50 text-zinc-500 cursor-pointer focus:outline-none">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </div>

        <!-- Right Section: Actions & User Avatar -->
        <div class="flex items-center gap-2 sm:gap-4">
            <!-- Ask Cora AI Button -->
            <button onclick="window.coraToggleAiSidebar(true)" class="flex items-center gap-1.5 px-2 py-1.5 sm:px-3 sm:py-1.5 bg-zinc-950 hover:bg-zinc-900 text-white rounded-lg text-xs font-bold transition-colors cursor-pointer select-none border border-zinc-900 ">
                <!-- Sparkles SVG icon -->
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-white ">
                    <path d="M9.81 2.863a.5.5 0 0 0-.955 0L7.335 6.757 3.441 8.277a.5.5 0 0 0 0 .955l3.894 1.52 1.52 3.894a.5.5 0 0 0 .955 0l1.52-3.894 3.894-1.52a.5.5 0 0 0 0-.955l-3.894-1.52-1.52-3.894zM19 14.5a.5.5 0 0 0-.955 0l-.608 1.558-1.558.608a.5.5 0 0 0 0 .955l1.558.608.608 1.558a.5.5 0 0 0 .955 0l.608-1.558 1.558-.608a.5.5 0 0 0 0-.955l-1.558-.608-.608-1.558zM19 3.5a.5.5 0 0 0-.955 0l-.608 1.558-1.558.608a.5.5 0 0 0 0 .955l1.558.608.608 1.558a.5.5 0 0 0 .955 0l.608-1.558 1.558-.608a.5.5 0 0 0 0-.955l-1.558-.608-.608-1.558z"/>
                </svg>
                <span class="hidden sm:inline">Ask Cora AI</span>
            </button>

            <!-- Changelog Link Button -->
            <button onclick="coraPublicShowSection('changelog')" class="hidden md:block text-xs font-medium text-zinc-600 hover:text-zinc-950 transition-colors bg-transparent border-none p-0 cursor-pointer select-none">
                Changelog
            </button>

            <!-- Theme Toggle (Locked to Light Mode for this release) -->
            <button onclick="coraTogglePublicDarkMode()" class="hidden text-zinc-500 hover:text-zinc-900 transition-colors p-1.5 rounded-lg border-none bg-transparent cursor-pointer select-none flex items-center justify-center" aria-label="Toggle dark mode">
                <!-- SVG sun icon (shown in light mode to switch to dark) -->
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="block ">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <!-- SVG moon icon (shown in dark mode to switch to light) -->
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="hidden ">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>


        </div>
    </div>
</header>

<script>
function coraTogglePublicDarkMode() {
    // Locked for this release
}
// Initialize Theme dynamically from stored preferences (Force Light Mode)
(function() {
    document.documentElement.classList.remove('dark');
    try {
        localStorage.setItem('cora-theme', 'light');
    } catch(e) {}
})();
</script>

