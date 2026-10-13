<?php
/**
 * Cora Growth Platform — Projects & Portfolio Case Studies Management Suite
 *
 * Provides workspace-level portfolio showcase management, 4-act narrative editing,
 * 1-click publishing, proof KPI metrics, and direct sync to the Next.js frontend.
 * Conforms 100% to Cora Master Atomic Design System & Notion/Shopify monochromatic standards.
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'cora_projects';

// Check if table exists, else initialize
if ( class_exists( 'Cora_Projects_Module' ) ) {
    Cora_Projects_Module::setup_database_table();
}

// Multi-tenancy: Scoped strictly to the active workspace agency_id
$agency_id = function_exists( 'cora_db_get_agency_id' ) ? strval( cora_db_get_agency_id() ) : '1';

// Fetch projects for this workspace
$projects_raw = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE agency_id = %s ORDER BY is_featured DESC, sort_order ASC, created_at DESC",
        $agency_id
    ),
    ARRAY_A
);

if ( empty( $projects_raw ) ) {
    $projects_raw = array();
}

// Calculate real, verifiable workspace portfolio metrics (Zero fabricated data)
$total_projects     = count( $projects_raw );
$published_count    = 0;
$drafts_count       = 0;
$featured_count     = 0;
$testimonials_count = 0;
$unique_services    = array();

foreach ( $projects_raw as $p ) {
    if ( ( $p['status'] ?? 'draft' ) === 'published' ) {
        $published_count++;
    } else {
        $drafts_count++;
    }
    if ( ! empty( $p['is_featured'] ) ) {
        $featured_count++;
    }
    if ( ! empty( $p['testimonial_quote'] ) ) {
        $testimonials_count++;
    }
    $decoded_services = json_decode( $p['services'] ?? '[]', true );
    if ( is_array( $decoded_services ) ) {
        foreach ( $decoded_services as $s ) {
            $s_clean = trim( strval( $s ) );
            if ( $s_clean !== '' && ! in_array( $s_clean, $unique_services, true ) ) {
                $unique_services[] = $s_clean;
            }
        }
    }
}
$services_count = count( $unique_services );
$published_pct  = $total_projects > 0 ? round( ( $published_count / $total_projects ) * 100 ) : 0;

$frontend_base = home_url();
?>

<!-- SCOPED STYLING FOR PORTFOLIO MODULE -->
<style>
#cora-projects-module {
    position: relative;
    font-family: var(--cora-font-sans, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif);
}

/* =========================================================================
   EDGE-TO-EDGE FULL-VIEWPORT DRAWER & BACKDROP ARCHITECTURE (RULE 1 & RULE 12)
   ========================================================================= */

/* =========================================================================
   SHOPIFY-INSPIRED MAIN-AREA DOCKED VIEW PANEL (GLOBAL CORA STANDARD)
   - Bottom-slide motion when opened: transform: translateY(100%) -> translateY(0)
   - Zero overlay or floating blur: no backdrop element
   - Docks directly below the header: top: var(--cora-main-top, 52px); height: calc(100vh - 52px);
   - Occupies the EXACT available workspace width (based on sidebar status)
   - Zero shadow, square corners (border-radius: 0)
   - Clear formal structural borders: border-left & border-top
   - Sidebar remains 100% visible, clickable, and accessible at all times
   ========================================================================= */

:root {
    --cora-main-left: 16rem;
    --cora-main-top: 52px;
    --cora-main-width: calc(100vw - 16rem);
    --cora-main-height: calc(100vh - 52px);
}

/* MAIN-AREA DOCKED DRAWER PANEL (Shopify Inspired - Canvas Docked) */
#cora-project-drawer {
    position: fixed !important;
    top: var(--cora-main-top, 52px) !important;
    bottom: 0 !important;
    left: var(--cora-main-left, 16rem) !important;
    right: 0 !important;
    width: var(--cora-main-width, calc(100vw - 16rem)) !important;
    max-width: var(--cora-main-width, calc(100vw - 16rem)) !important;
    height: var(--cora-main-height, calc(100vh - 52px)) !important;
    max-height: var(--cora-main-height, calc(100vh - 52px)) !important;
    margin: 0 !important;
    padding: 0 !important;
    border-radius: 0 !important;
    border-top: 1px solid #e4e4e7 !important;
    border-left: 1px solid #e4e4e7 !important;
    border-right: none !important;
    border-bottom: none !important;
    background-color: #ffffff !important;
    z-index: 40 !important; /* Kept strictly below sidebar (z-index: 60) and topbar (z-index: 9999) */
    box-shadow: none !important;
    -webkit-box-shadow: none !important;
    transition: left 0.2s cubic-bezier(0.4, 0, 0.2, 1), width 0.2s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.2s cubic-bezier(0.4, 0, 0.2, 1), transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.15s ease !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}

.dark #cora-project-drawer {
    background-color: #18181b !important;
    border-top: 1px solid #27272a !important;
    border-left: 1px solid #27272a !important;
}

/* SIDEBAR COLLAPSED: Dynamically adjust to 4rem (64px / w-16) on desktop */
@media (min-width: 1024px) {
    body:has(.cora-sidebar.collapsed-sidebar) #cora-project-drawer:not(.maximized),
    body.cora-sidebar-collapsed #cora-project-drawer:not(.maximized),
    .cora-sidebar.collapsed-sidebar ~ .cora-main #cora-project-drawer:not(.maximized),
    #cora-project-drawer.sidebar-collapsed:not(.maximized) {
        left: 4rem !important;
        width: calc(100vw - 4rem) !important;
        max-width: calc(100vw - 4rem) !important;
    }
}

/* MAXIMIZED FULLSCREEN VIEW (Shopify Expand ⤢ Mode) */
#cora-project-drawer.maximized {
    left: 0 !important;
    width: 100vw !important;
    max-width: 100vw !important;
    border-left: none !important;
    z-index: 70 !important;
}

/* MOBILE / TABLET (< 1024px): Full viewport width */
@media (max-width: 1023px) {
    #cora-project-drawer,
    body:has(.cora-sidebar.collapsed-sidebar) #cora-project-drawer,
    body.cora-sidebar-collapsed #cora-project-drawer,
    #cora-project-drawer.sidebar-collapsed {
        left: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        border-left: none !important;
        z-index: 55 !important;
    }
}

/* OPEN & COLLAPSED STATES (BOTTOM SLIDE MOTION) */
#cora-project-drawer.open:not(.collapsed) {
    transform: translateY(0) !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}

#cora-project-drawer.collapsed,
#cora-project-drawer:not(.open) {
    transform: translateY(100%) !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
    display: none !important;
}

.cora-proj-tab-panel {
    display: none;
    max-width: 64rem;
    margin-left: auto;
    margin-right: auto;
    width: 100%;
}
.cora-proj-tab-panel.active { display: block; }

/* Level 01 Segmented Switcher Scoped Overrides in Header Sub-Tabs */
.cora-sub-tabs-container #view-mode-table-btn,
.cora-sub-tabs-container #view-mode-grid-btn {
    height: 22px !important;
    min-height: 22px !important;
    max-height: 22px !important;
    width: 26px !important;
    min-width: 26px !important;
    max-width: 26px !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    line-height: 1 !important;
    box-sizing: border-box !important;
}
.cora-sub-tabs-container #view-mode-table-btn svg,
.cora-sub-tabs-container #view-mode-grid-btn svg {
    display: block !important;
    margin: 0 auto !important;
}

/* Mobile Search Bar Expansion in Sub-Tabs */
@media (max-width: 639px) {
    #cora-proj-search-wrapper.mobile-expanded {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 70px;
        z-index: 30;
        display: flex !important;
        align-items: center;
        background: #FBFaf7;
        padding-left: 12px;
        padding-right: 6px;
    }
    .dark #cora-proj-search-wrapper.mobile-expanded {
        background: #0c0c0e;
    }
}
</style>

<div id="cora-projects-module" class="w-full flex-1 min-h-0 flex flex-col gap-4">

    <!-- 1. WORKSPACE PAGE HEADER (Level 03 Standard Organism) -->
    <?php
    $portfolio_header_args = array(
        'title'              => 'Portfolio',
        'description'        => 'Curate client case studies, transformation narratives, proof metrics, and publish to your showcase.',
        'icon'               => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>',
        'ai_stack'           => true,
        'cta'                => array(
            'id'          => 'btn-new-project',
            'text'        => 'New Project',
            'mobile_text' => 'Project',
            'onclick'     => 'coraOpenProjectDrawerForCreate()',
            'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
            'visible'     => true,
        ),
        'tabs_extra_html'    => '<div class="h-full flex items-center gap-1.5 sm:gap-2">
            <!-- Mobile Search Trigger Button (< 640px) -->
            <button 
                type="button" 
                id="cora-proj-search-trigger-btn"
                onclick="coraToggleMobileSearch(true)"
                class="h-7 w-7 flex sm:hidden items-center justify-center rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer shadow-2xs shrink-0 select-none"
                title="Search projects"
                aria-label="Search projects"
            >
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>

            <!-- Search Input Wrapper (Responsive: Expands on mobile, permanent inline on desktop) -->
            <div id="cora-proj-search-wrapper" class="cora-proj-search-wrapper hidden sm:flex items-center">
                <div class="relative w-full sm:w-48 md:w-56 lg:w-60 flex items-center">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none shrink-0"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input 
                        type="text" 
                        id="cora-proj-search-input"
                        placeholder="Search projects..." 
                        class="h-7 w-full pl-8 pr-7 sm:pr-2.5 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-400 transition-all shadow-2xs"
                        oninput="coraFilterProjectsList()"
                        onkeyup="coraFilterProjectsList()"
                        onkeydown="if(event.key===\'Escape\'){ coraToggleMobileSearch(false); }"
                        autocomplete="off"
                    />
                    <!-- Mobile Close Button -->
                    <button 
                        type="button" 
                        id="cora-proj-search-close-btn"
                        onclick="coraToggleMobileSearch(false)"
                        class="absolute right-1 top-1/2 -translate-y-1/2 h-5 w-5 flex sm:hidden items-center justify-center rounded-md text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer"
                        title="Close search"
                        aria-label="Close search"
                    >
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>

            <!-- Level 01 Segmented Switcher (Table vs Grid) -->
            <div class="h-7 inline-flex items-center p-0.5 bg-zinc-200/60 dark:bg-zinc-800/80 rounded-lg border border-zinc-200/70 dark:border-zinc-700/70 shrink-0 box-border select-none">
                <button type="button" onclick="coraSetViewMode(\'table\')" id="view-mode-table-btn" class="h-[22px] w-[26px] p-0 m-0 rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white shadow-2xs border-0 cursor-pointer inline-flex items-center justify-center transition-all" title="Table View">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                </button>
                <button type="button" onclick="coraSetViewMode(\'grid\')" id="view-mode-grid-btn" class="h-[22px] w-[26px] p-0 m-0 rounded-md text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer inline-flex items-center justify-center transition-all" title="Grid View">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </button>
            </div>
        </div>',
        'tabs'               => array(
            array(
                'id'           => 'all',
                'dom_id'       => 'tab-projects-all',
                'label'        => 'All Projects',
                'mobile_label' => 'All',
                'badge'        => '<span class="ml-1 px-1.5 py-0.2 rounded-full bg-zinc-200/60 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-mono leading-tight">' . esc_html( $total_projects ) . '</span>',
                'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect></svg>',
                'active'       => true,
                'onclick'      => "coraSwitchTabFilter('all', this)",
            ),
            array(
                'id'           => 'published',
                'dom_id'       => 'tab-projects-published',
                'label'        => 'Published',
                'mobile_label' => 'Live',
                'badge'        => '<span class="ml-1 px-1.5 py-0.2 rounded-full bg-zinc-200/60 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-mono leading-tight">' . esc_html( $published_count ) . '</span>',
                'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>',
                'active'       => false,
                'onclick'      => "coraSwitchTabFilter('published', this)",
            ),
            array(
                'id'           => 'draft',
                'dom_id'       => 'tab-projects-draft',
                'label'        => 'Drafts In Review',
                'mobile_label' => 'Drafts',
                'badge'        => '<span class="ml-1 px-1.5 py-0.2 rounded-full bg-zinc-200/60 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-mono leading-tight">' . esc_html( $drafts_count ) . '</span>',
                'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
                'active'       => false,
                'onclick'      => "coraSwitchTabFilter('draft', this)",
            ),
        ),
    );

    if ( function_exists( 'cora_render_workspace_header' ) ) {
        cora_render_workspace_header( $portfolio_header_args );
    }
    ?>

    <!-- 2. WORKSPACE KPI METRIC CARDS (Sleek Monochromatic Cards - Atomic Level 02) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-3.5">
        <!-- 1. Total Case Studies -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 sm:p-3.5 flex items-center gap-3 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300 shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block truncate">Total Projects</span>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-sm sm:text-base font-bold font-mono text-zinc-950 dark:text-zinc-50"><?php echo esc_html( $total_projects ); ?></span>
                    <span class="text-[10.5px] text-zinc-500 font-sans truncate"><?php echo esc_html( $featured_count ); ?> featured</span>
                </div>
            </div>
        </div>

        <!-- 2. Live Showcase Readiness -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 sm:p-3.5 flex items-center gap-3 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300 shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block truncate">Live Readiness</span>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-sm sm:text-base font-bold font-mono text-zinc-950 dark:text-zinc-50"><?php echo esc_html( $published_count ); ?>/<?php echo esc_html( $total_projects ); ?></span>
                    <span class="text-[10.5px] text-zinc-500 font-sans truncate"><?php echo esc_html( $published_pct ); ?>% Live</span>
                </div>
            </div>
        </div>

        <!-- 3. Client Testimonials Proof -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 sm:p-3.5 flex items-center gap-3 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300 shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block truncate">Client Social Proof</span>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-sm sm:text-base font-bold font-mono text-zinc-950 dark:text-zinc-50"><?php echo esc_html( $testimonials_count ); ?></span>
                    <span class="text-[10.5px] text-zinc-500 font-sans truncate">verified quote<?php echo $testimonials_count === 1 ? '' : 's'; ?></span>
                </div>
            </div>
        </div>

        <!-- 4. Active Capabilities & Showcase Link -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 sm:p-3.5 flex items-center justify-between gap-3 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300 shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block truncate">Capabilities</span>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <span class="text-sm sm:text-base font-bold font-mono text-zinc-950 dark:text-zinc-50"><?php echo esc_html( $services_count ); ?></span>
                        <span class="text-[10.5px] text-zinc-500 font-sans truncate">services</span>
                    </div>
                </div>
            </div>
            <a href="<?php echo esc_url( $frontend_base . '/portfolio' ); ?>" target="_blank" class="shrink-0 p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" title="Open live showcase">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            </a>
        </div>
    </div>



    <!-- 4. NOTION / SHOPIFY MONOCHROMATIC DATA TABLE (View Mode: Table) -->
    <div id="cora-projects-table-view" class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-2xs">
        <!-- DESKTOP INTERACTIVE DATA TABLE (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-zinc-500 dark:text-zinc-400 font-mono text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4 font-medium">Case Study & Client</th>
                        <th class="py-3 px-4 font-medium">Industry</th>
                        <th class="py-3 px-4 font-medium">Core Capabilities</th>
                        <th class="py-3 px-4 font-medium">Primary Outcome</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                        <th class="py-3 px-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="cora-projects-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <?php if ( empty( $projects_raw ) ) : ?>
                    <tr id="cora-projects-empty-row">
                        <td colspan="6" class="py-16 text-center text-zinc-500">
                            <div class="max-w-md mx-auto space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 mx-auto flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                </div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">No portfolio projects in this workspace</h3>
                                <p class="text-xs text-zinc-500 leading-relaxed">
                                    Create and showcase client transformation narratives to prove results and drive high-intent consultations.
                                </p>
                                <div class="flex items-center justify-center gap-2 pt-2">
                                    <button type="button" onclick="coraOpenProjectDrawerForCreate()" class="px-3.5 py-1.5 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 text-xs font-semibold hover:bg-zinc-800 transition cursor-pointer">
                                        + New Project
                                    </button>
                                    <button type="button" onclick="coraLoadStarterProjects()" class="px-3.5 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-medium hover:bg-zinc-200 transition border border-zinc-200/80 dark:border-zinc-700 cursor-pointer">
                                        Load Starter Samples
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php else : ?>
                    <?php foreach ( $projects_raw as $proj ) : 
                        $services = json_decode( $proj['services'] ?? '[]', true ) ?: array();
                        $kpis = json_decode( $proj['kpis'] ?? '[]', true ) ?: array();
                        $primary_kpi = ! empty( $kpis[0] ) ? $kpis[0] : null;
                        $is_pub = ( $proj['status'] ?? 'draft' ) === 'published';
                    ?>
                    <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors cora-project-row" data-id="<?php echo esc_attr( $proj['id'] ); ?>" data-status="<?php echo esc_attr( $proj['status'] ); ?>">
                        
                        <!-- Title & Client -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <?php if ( ! empty( $proj['hero_image_url'] ) ) : ?>
                                <img src="<?php echo esc_url( $proj['hero_image_url'] ); ?>" class="w-10 h-10 rounded-xl object-cover border border-zinc-200 dark:border-zinc-700 shrink-0" alt="" />
                                <?php else : ?>
                                <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-mono font-bold text-zinc-500 shrink-0">
                                    <?php echo esc_html( substr( $proj['client_name'] ?? 'P', 0, 1 ) ); ?>
                                </div>
                                <?php endif; ?>

                                <div>
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100 block line-clamp-1 hover:text-zinc-600 dark:hover:text-zinc-300 cursor-pointer" onclick="coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>)">
                                        <?php echo esc_html( $proj['title'] ); ?>
                                    </span>
                                    <div class="flex items-center gap-1.5 text-[11px] text-zinc-500 mt-0.5">
                                        <span><?php echo esc_html( $proj['client_name'] ); ?></span>
                                        <?php if ( ! empty( $proj['is_confidential'] ) ) : ?>
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">Stealth</span>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $proj['is_featured'] ) ) : ?>
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-semibold">Featured</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Industry -->
                        <td class="py-3.5 px-4 text-zinc-600 dark:text-zinc-400 font-mono text-[11px]">
                            <?php echo esc_html( $proj['client_industry'] ); ?>
                        </td>

                        <!-- Services -->
                        <td class="py-3.5 px-4">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                <?php foreach ( array_slice( $services, 0, 2 ) as $s ) : ?>
                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-medium border border-zinc-200/60 dark:border-zinc-700/60"><?php echo esc_html( $s ); ?></span>
                                <?php endforeach; ?>
                                <?php if ( count( $services ) > 2 ) : ?>
                                <span class="text-[10px] text-zinc-400 font-mono">+<?php echo esc_html( count( $services ) - 2 ); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Primary KPI (Rule 4: Monochromatic Notion Palette) -->
                        <td class="py-3.5 px-4">
                            <?php if ( $primary_kpi ) : ?>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-zinc-100/90 dark:bg-zinc-800/80 border border-zinc-200/70 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 font-mono">
                                <span class="font-bold"><?php echo esc_html( $primary_kpi['value'] ); ?></span>
                                <span class="text-[10px] text-zinc-500 font-sans"><?php echo esc_html( $primary_kpi['label'] ); ?></span>
                            </div>
                            <?php else : ?>
                            <span class="text-zinc-400 font-mono text-[11px]">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Status Toggle Pill (Rule 4: Monochromatic standards) -->
                        <td class="py-3.5 px-4">
                            <button
                                type="button"
                                onclick="coraToggleProjectPublish(<?php echo esc_attr( $proj['id'] ); ?>)"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium transition cursor-pointer <?php echo $is_pub ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 shadow-2xs' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-700'; ?>"
                                title="Click to toggle status"
                            >
                                <span class="w-1.5 h-1.5 rounded-full <?php echo $is_pub ? 'bg-white dark:bg-zinc-900' : 'bg-zinc-400'; ?>"></span>
                                <span id="status-pill-text-<?php echo esc_attr( $proj['id'] ); ?>"><?php echo $is_pub ? 'Published' : 'Draft'; ?></span>
                            </button>
                        </td>

                        <!-- Row Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <!-- Copy Public Link -->
                                <button
                                    type="button"
                                    onclick="coraCopyProjectUrl('<?php echo esc_js( $proj['slug'] ); ?>')"
                                    class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                                    title="Copy Public Link"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                </button>

                                <!-- View Live -->
                                <a
                                    href="<?php echo esc_url( $frontend_base . '/portfolio/' . $proj['slug'] ); ?>"
                                    target="_blank"
                                    class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                                    title="View on Frontend"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>

                                <!-- Edit Drawer -->
                                <button
                                    type="button"
                                    onclick="coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>)"
                                    class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                                    title="Edit Project"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </button>

                                <!-- Duplicate -->
                                <button
                                    type="button"
                                    onclick="coraDuplicateProject(<?php echo esc_attr( $proj['id'] ); ?>)"
                                    class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                                    title="Duplicate Project"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                </button>

                                <!-- Delete -->
                                <button
                                    type="button"
                                    onclick="coraDeleteProject(<?php echo esc_attr( $proj['id'] ); ?>)"
                                    class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                                    title="Delete Project"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- MOBILE RESPONSIVE ZERO-HORIZONTAL-SCROLL CARDS (Level 03 Organism) -->
        <div class="block md:hidden divide-y divide-zinc-100 dark:divide-zinc-800" id="cora-projects-mobile-list">
            <?php if ( empty( $projects_raw ) ) : ?>
            <div class="p-8 text-center text-zinc-500 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 mx-auto flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">No portfolio projects</h3>
                <p class="text-xs text-zinc-500">Create client case studies to prove results and drive bookings.</p>
                <button type="button" onclick="coraOpenProjectDrawerForCreate()" class="px-4 py-2 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 text-xs font-semibold">
                    + New Project
                </button>
            </div>
            <?php else : ?>
            <?php foreach ( $projects_raw as $proj ) : 
                $services = json_decode( $proj['services'] ?? '[]', true ) ?: array();
                $kpis = json_decode( $proj['kpis'] ?? '[]', true ) ?: array();
                $primary_kpi = ! empty( $kpis[0] ) ? $kpis[0] : null;
                $is_pub = ( $proj['status'] ?? 'draft' ) === 'published';
            ?>
            <div class="p-4 space-y-3 cora-project-row" data-id="<?php echo esc_attr( $proj['id'] ); ?>" data-status="<?php echo esc_attr( $proj['status'] ); ?>">
                <!-- Card Top Row: Thumbnail + Title + Client + Status -->
                <div class="flex items-start gap-3">
                    <?php if ( ! empty( $proj['hero_image_url'] ) ) : ?>
                    <img src="<?php echo esc_url( $proj['hero_image_url'] ); ?>" class="w-12 h-12 rounded-xl object-cover border border-zinc-200 dark:border-zinc-700 shrink-0" alt="" />
                    <?php else : ?>
                    <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-mono font-bold text-zinc-600 dark:text-zinc-300 text-sm shrink-0">
                        <?php echo esc_html( substr( $proj['client_name'] ?? 'P', 0, 1 ) ); ?>
                    </div>
                    <?php endif; ?>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 truncate"><?php echo esc_html( $proj['client_name'] ); ?></span>
                            <?php if ( ! empty( $proj['is_featured'] ) ) : ?>
                            <span class="px-1.5 py-0.2 rounded text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-semibold">Featured</span>
                            <?php endif; ?>
                            <?php if ( ! empty( $proj['is_confidential'] ) ) : ?>
                            <span class="px-1.5 py-0.2 rounded text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-500">Stealth</span>
                            <?php endif; ?>
                        </div>
                        <h4 class="text-xs font-bold text-zinc-950 dark:text-zinc-50 leading-snug line-clamp-2 cursor-pointer hover:text-zinc-600 dark:hover:text-zinc-300" onclick="coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>)">
                            <?php echo esc_html( $proj['title'] ); ?>
                        </h4>
                        <p class="text-[11px] text-zinc-500 mt-0.5"><?php echo esc_html( $proj['client_industry'] ); ?></p>
                    </div>

                    <!-- Status Pill (Tap to Toggle) -->
                    <button
                        type="button"
                        onclick="coraToggleProjectPublish(<?php echo esc_attr( $proj['id'] ); ?>)"
                        class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-medium transition cursor-pointer <?php echo $is_pub ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-700'; ?>"
                        title="Click to toggle publish"
                    >
                        <span class="w-1.5 h-1.5 rounded-full <?php echo $is_pub ? 'bg-white dark:bg-zinc-900' : 'bg-zinc-400'; ?>"></span>
                        <span><?php echo $is_pub ? 'Live' : 'Draft'; ?></span>
                    </button>
                </div>

                <!-- Capability Tags -->
                <?php if ( ! empty( $services ) ) : ?>
                <div class="flex flex-wrap gap-1 pt-0.5">
                    <?php foreach ( array_slice( $services, 0, 3 ) as $s ) : ?>
                    <span class="px-2 py-0.5 rounded-md text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-medium border border-zinc-200/60 dark:border-zinc-700/60"><?php echo esc_html( $s ); ?></span>
                    <?php endforeach; ?>
                    <?php if ( count( $services ) > 3 ) : ?>
                    <span class="text-[10px] text-zinc-400 font-mono self-center">+<?php echo esc_html( count( $services ) - 3 ); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Primary Outcome (if set) -->
                <?php if ( $primary_kpi ) : ?>
                <div class="flex items-center justify-between p-2 rounded-xl bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-100 dark:border-zinc-800/80">
                    <span class="text-[11px] text-zinc-500 font-medium"><?php echo esc_html( $primary_kpi['label'] ); ?></span>
                    <span class="text-xs font-bold font-mono text-zinc-900 dark:text-white"><?php echo esc_html( $primary_kpi['value'] ); ?></span>
                </div>
                <?php endif; ?>

                <!-- Quick Actions Toolbar -->
                <div class="flex items-center gap-2 pt-1 border-t border-zinc-100 dark:border-zinc-800/80">
                    <button
                        type="button"
                        onclick="coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>)"
                        class="flex-1 py-1.5 px-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-900 text-xs font-semibold text-center transition cursor-pointer"
                    >
                        Edit Details
                    </button>
                    <a
                        href="<?php echo esc_url( $frontend_base . '/portfolio/' . $proj['slug'] ); ?>"
                        target="_blank"
                        class="px-2.5 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 text-xs font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                    >
                        Preview
                    </a>
                    <button
                        type="button"
                        onclick="coraDuplicateProject(<?php echo esc_attr( $proj['id'] ); ?>)"
                        class="p-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition cursor-pointer"
                        title="Duplicate"
                    >
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    </button>
                    <button
                        type="button"
                        onclick="coraDeleteProject(<?php echo esc_attr( $proj['id'] ); ?>)"
                        class="p-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition cursor-pointer"
                        title="Delete"
                    >
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- 5. VISUAL PORTFOLIO CARD GRID (View Mode: Grid, Hidden by default) -->
    <div id="cora-projects-grid-view" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ( $projects_raw as $proj ) : 
            $services = json_decode( $proj['services'] ?? '[]', true ) ?: array();
            $kpis = json_decode( $proj['kpis'] ?? '[]', true ) ?: array();
            $primary_kpi = ! empty( $kpis[0] ) ? $kpis[0] : null;
            $is_pub = ( $proj['status'] ?? 'draft' ) === 'published';
        ?>
        <div class="cora-project-row bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 flex flex-col justify-between gap-3 shadow-2xs hover:shadow-md hover:border-zinc-300 dark:hover:border-zinc-700 transition group cursor-pointer" data-id="<?php echo esc_attr( $proj['id'] ); ?>" data-status="<?php echo esc_attr( $proj['status'] ); ?>" onclick="coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>)">
            <div>
                <!-- Hero Image Preview -->
                <div class="aspect-video w-full rounded-xl bg-zinc-100 dark:bg-zinc-800 overflow-hidden relative border border-zinc-200/60 dark:border-zinc-800">
                    <?php if ( ! empty( $proj['hero_image_url'] ) ) : ?>
                    <img src="<?php echo esc_url( $proj['hero_image_url'] ); ?>" class="w-full h-full object-cover group-hover:scale-102 transition duration-300" alt="" />
                    <?php else : ?>
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 font-mono text-xs">
                        No Visual Asset
                    </div>
                    <?php endif; ?>

                    <div class="absolute top-2.5 right-2.5">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium <?php echo $is_pub ? 'bg-zinc-950/80 text-white backdrop-blur-xs' : 'bg-zinc-100/90 text-zinc-700 border border-zinc-200/80'; ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo $is_pub ? 'bg-white' : 'bg-zinc-400'; ?>"></span>
                            <?php echo $is_pub ? 'Published' : 'Draft'; ?>
                        </span>
                    </div>
                </div>

                <!-- Title & Meta -->
                <div class="mt-3 space-y-1">
                    <div class="flex items-center justify-between text-[11px] text-zinc-500">
                        <span><?php echo esc_html( $proj['client_name'] ); ?></span>
                        <span class="font-mono"><?php echo esc_html( $proj['client_industry'] ); ?></span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 line-clamp-2 leading-snug">
                        <?php echo esc_html( $proj['title'] ); ?>
                    </h4>
                </div>
            </div>

            <!-- Footer: Primary KPI & Actions -->
            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                <?php if ( $primary_kpi ) : ?>
                <div class="font-mono text-xs text-zinc-900 dark:text-zinc-100">
                    <span class="font-bold"><?php echo esc_html( $primary_kpi['value'] ); ?></span>
                    <span class="text-[10px] text-zinc-500 font-sans"><?php echo esc_html( $primary_kpi['label'] ); ?></span>
                </div>
                <?php else : ?>
                <span class="text-[11px] text-zinc-400 font-mono">—</span>
                <?php endif; ?>

                <div class="flex items-center gap-1" onclick="event.stopPropagation()">
                    <button type="button" onclick="coraCopyProjectUrl('<?php echo esc_js( $proj['slug'] ); ?>')" class="p-1 rounded-md text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition" title="Copy Link">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    </button>
                    <a href="<?php echo esc_url( $frontend_base . '/portfolio/' . $proj['slug'] ); ?>" target="_blank" class="p-1 rounded-md text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition" title="View Live">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- =========================================================================
     6. MAIN-AREA DOCKED VIEW PANEL (GLOBAL CORA STANDARD)
     ========================================================================= -->
<aside id="cora-project-drawer" class="collapsed">
    
    <!-- Drawer Header (Shopify-Inspired Main Area Header with Expand & Close) -->
    <div class="px-6 sm:px-8 py-3.5 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-1.5 py-0.5 rounded text-[9.5px] font-mono font-bold tracking-wider uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">Showcase Studio</span>
                <span id="cora-proj-header-badge" class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">Published</span>
            </div>
            <h2 class="text-base font-bold text-zinc-950 dark:text-zinc-50 leading-tight" id="cora-proj-drawer-title">New Project</h2>
            <p class="text-[11px] text-zinc-500 mt-0.5">Configure deliverables, transformation metrics, and narrative acts.</p>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            <!-- Fullscreen / Maximize Toggle Button (Shopify Expand ⤢) -->
            <button 
                type="button" 
                onclick="coraToggleProjectDrawerMaximize()"
                id="cora-proj-maximize-btn"
                class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0"
                title="Expand / Restore Canvas"
            >
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
            </button>

            <!-- Close Button (Esc) -->
            <button 
                type="button" 
                onclick="coraCloseProjectDrawer()"
                class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0"
                title="Close Drawer (Esc)"
            >
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
    </div>

    <!-- Drawer Sub-Navigation Tabs (Segmented Control Group - Level 01 Atom) -->
    <div class="px-6 sm:px-8 py-2.5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-950/70 shrink-0">
        <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 overflow-x-auto select-none" id="cora-proj-drawer-tabs">
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer whitespace-nowrap cora-proj-tab-btn" data-target="proj-tab-overview">Overview</button>
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-tab-btn" data-target="proj-tab-narrative">Narrative Story</button>
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-tab-btn" data-target="proj-tab-kpis">Impact KPIs</button>
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-tab-btn" data-target="proj-tab-media">Media & Visuals</button>
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-tab-btn" data-target="proj-tab-testimonial">Endorsement</button>
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-tab-btn" data-target="proj-tab-seo">SEO & CTA</button>
        </div>
    </div>

    <!-- Drawer Form Body (Scrollable with Structured Design Surface Cards) -->
    <form id="cora-project-form" class="flex-1 overflow-y-auto px-6 sm:px-8 py-5 space-y-4 text-xs text-zinc-800 dark:text-zinc-200" onsubmit="coraSubmitProjectForm(event)">
        <input type="hidden" id="proj-field-id" name="id" value="0" />
        <input type="hidden" id="proj-field-agency-id" name="agency_id" value="<?php echo esc_attr( $agency_id ); ?>" />

        <!-- TAB 1: OVERVIEW -->
        <div id="proj-tab-overview" class="cora-proj-tab-panel active space-y-4">
            
            <!-- Surface Card: Core Scope -->
            <div class="p-4 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-3.5">
                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Headline / Project Title *</label>
                    <input 
                        type="text" 
                        id="proj-field-title" 
                        name="title" 
                        required 
                        placeholder="e.g. Master Production Showcase or Client Transformation"
                        class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Client Name *</label>
                        <input 
                            type="text" 
                            id="proj-field-client-name" 
                            name="client_name" 
                            required 
                            placeholder="e.g. Acme Enterprise"
                            class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                        />
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Industry Sector</label>
                        <input 
                            type="text" 
                            id="proj-field-client-industry" 
                            name="client_industry" 
                            placeholder="e.g. Photography & Editorial, Real Estate, Technology"
                            class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Project Duration</label>
                        <input 
                            type="text" 
                            id="proj-field-duration" 
                            name="project_duration" 
                            placeholder="e.g. 3-Day Sprint or Q3 2026"
                            class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                        />
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Canonical Slug</label>
                        <input 
                            type="text" 
                            id="proj-field-slug" 
                            name="slug" 
                            placeholder="e.g. flagship-showcase-project"
                            class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs font-mono"
                        />
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Core Capability Tags (comma separated)</label>
                    <input 
                        type="text" 
                        id="proj-field-services" 
                        name="services" 
                        placeholder="e.g. Creative Direction, Visual Stills, 3D Architecture, Brand Strategy"
                        class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                    />
                </div>

                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Subtitle / Tagline</label>
                    <input 
                        type="text" 
                        id="proj-field-subtitle" 
                        name="subtitle" 
                        placeholder="Short 1-sentence synopsis of scope and platform"
                        class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                    />
                </div>
            </div>

            <!-- Surface Card: Visibility & Privacy (Rule 13 Tonal Fills) -->
            <div class="p-4 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-3">
                <span class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block">Visibility & Governance</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center gap-3 cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-700 transition">
                        <input type="checkbox" id="proj-field-confidential" name="is_confidential" value="1" class="rounded text-zinc-900 focus:ring-0" />
                        <div>
                            <span class="text-xs text-zinc-900 dark:text-white font-semibold block">Under NDA / Stealth</span>
                            <span class="text-[10px] text-zinc-400 block">Obfuscates public client identity</span>
                        </div>
                    </label>

                    <label class="p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center gap-3 cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-700 transition">
                        <input type="checkbox" id="proj-field-featured" name="is_featured" value="1" class="rounded text-zinc-900 focus:ring-0" />
                        <div>
                            <span class="text-xs text-zinc-900 dark:text-white font-semibold block">Pin as Featured</span>
                            <span class="text-[10px] text-zinc-400 block">Elevates to hero showcase position</span>
                        </div>
                    </label>
                </div>
            </div>

        </div>

        <!-- TAB 2: NARRATIVE STORY -->
        <div id="proj-tab-narrative" class="cora-proj-tab-panel space-y-4">
            
            <div class="p-4 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-3.5">
                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Executive Summary (2-3 sentences)</label>
                    <textarea 
                        id="proj-field-exec-summary" 
                        name="executive_summary" 
                        rows="3" 
                        placeholder="High-level synopsis of the problem, agency intervention, and ultimate result."
                        class="w-full p-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs leading-relaxed"
                    ></textarea>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Act I: The Challenge & Bottleneck</label>
                    <textarea 
                        id="proj-field-challenge" 
                        name="challenge_content" 
                        rows="3" 
                        placeholder="What structural or technical obstacles were slowing the client down before engaging your team?"
                        class="w-full p-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs leading-relaxed"
                    ></textarea>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Act II: Strategic Solution & Methodology</label>
                    <textarea 
                        id="proj-field-solution" 
                        name="solution_content" 
                        rows="3" 
                        placeholder="Detail the execution sprint, technical setup, and deliverables produced."
                        class="w-full p-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs leading-relaxed"
                    ></textarea>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Deliverables & Artifacts (one per line)</label>
                    <textarea 
                        id="proj-field-deliverables" 
                        name="deliverables" 
                        rows="3" 
                        placeholder="Core Strategic Deliverable 1&#10;Key Production Asset or Deliverable 2&#10;Final Release or Deliverable 3"
                        class="w-full p-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs font-mono leading-relaxed"
                    ></textarea>
                </div>
            </div>

        </div>

        <!-- TAB 3: IMPACT KPIS (Rule 4 & Rule 13: Clean Monochromatic Styling) -->
        <div id="proj-tab-kpis" class="cora-proj-tab-panel space-y-4">
            <p class="text-xs text-zinc-500">Display up to 3 bold quantitative metrics that prove business impact.</p>

            <!-- Metric 1 Card -->
            <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5">
                <span class="text-[11px] font-mono text-zinc-500 font-bold uppercase tracking-wider block">Primary Proof Metric 1</span>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" id="kpi-val-1" placeholder="Value: e.g. 1.2M" class="h-9.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 font-mono font-bold text-zinc-900 dark:text-white outline-none focus:border-zinc-900" />
                    <input type="text" id="kpi-lbl-1" placeholder="Label: e.g. Impressions" class="h-9.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none focus:border-zinc-900" />
                </div>
                <input type="text" id="kpi-ctx-1" placeholder="Context: e.g. Organic social and print reach within 30 days" class="h-9 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-500 outline-none" />
            </div>

            <!-- Metric 2 Card -->
            <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5">
                <span class="text-[11px] font-mono text-zinc-500 font-bold uppercase tracking-wider block">Secondary Proof Metric 2</span>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" id="kpi-val-2" placeholder="Value: e.g. 48h" class="h-9.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 font-mono font-bold text-zinc-900 dark:text-white outline-none focus:border-zinc-900" />
                    <input type="text" id="kpi-lbl-2" placeholder="Label: e.g. Turnaround" class="h-9.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none focus:border-zinc-900" />
                </div>
                <input type="text" id="kpi-ctx-2" placeholder="Context: e.g. Same-week release ahead of schedule" class="h-9 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-500 outline-none" />
            </div>

            <!-- Metric 3 Card -->
            <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5">
                <span class="text-[11px] font-mono text-zinc-500 font-bold uppercase tracking-wider block">Tertiary Proof Metric 3</span>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" id="kpi-val-3" placeholder="Value: e.g. 100%" class="h-9.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 font-mono font-bold text-zinc-900 dark:text-white outline-none focus:border-zinc-900" />
                    <input type="text" id="kpi-lbl-3" placeholder="Label: e.g. On-Time Delivery" class="h-9.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none focus:border-zinc-900" />
                </div>
                <input type="text" id="kpi-ctx-3" placeholder="Context: e.g. Delivered ahead of international launch" class="h-9 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-500 outline-none" />
            </div>
        </div>

        <!-- TAB 4: MEDIA & VISUALS -->
        <div id="proj-tab-media" class="cora-proj-tab-panel space-y-4">
            <div class="p-4 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-3.5">
                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Hero Image Asset URL *</label>
                    <input 
                        type="url" 
                        id="proj-field-hero-image" 
                        name="hero_image_url" 
                        placeholder="https://images.unsplash.com/... or media asset URL"
                        class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                    />
                </div>

                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Live Project / Showcase URL</label>
                    <input 
                        type="url" 
                        id="proj-field-live-url" 
                        name="live_url" 
                        placeholder="https://clientbrand.com"
                        class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                    />
                </div>

                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Gallery Image URLs (one per line)</label>
                    <textarea 
                        id="proj-field-gallery" 
                        name="gallery_images" 
                        rows="3" 
                        placeholder="https://images.unsplash.com/photo-1...&#10;https://images.unsplash.com/photo-2..."
                        class="w-full p-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs font-mono leading-relaxed"
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- TAB 5: TESTIMONIAL (Rule 3: Privacy & Fictitious Names) -->
        <div id="proj-tab-testimonial" class="cora-proj-tab-panel space-y-4">
            <div class="p-4 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-3.5">
                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Client Endorsement Quote</label>
                    <textarea 
                        id="proj-field-test-quote" 
                        name="testimonial_quote" 
                        rows="3" 
                        placeholder="Working with the team was seamless and the execution exceeded our highest expectations..."
                        class="w-full p-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs leading-relaxed"
                    ></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Stakeholder Name</label>
                        <input 
                            type="text" 
                            id="proj-field-test-author" 
                            name="testimonial_author" 
                            placeholder="e.g. Kavya Patel"
                            class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                        />
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Title & Position</label>
                        <input 
                            type="text" 
                            id="proj-field-test-role" 
                            name="testimonial_role" 
                            placeholder="e.g. Managing Partner or Executive Director"
                            class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                        />
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">Company Name</label>
                    <input 
                        type="text" 
                        id="proj-field-test-company" 
                        name="testimonial_company" 
                        placeholder="e.g. Acme Enterprise"
                        class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none focus:border-zinc-900 dark:focus:border-zinc-300 transition-all shadow-2xs"
                    />
                </div>
            </div>
        </div>

        <!-- TAB 6: SEO & CTA -->
        <div id="proj-tab-seo" class="cora-proj-tab-panel space-y-4">
            <!-- CTA Surface Card -->
            <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200/80 dark:border-zinc-800/80 space-y-3">
                <span class="text-[11px] font-mono text-zinc-500 font-bold uppercase tracking-wider block">Showcase Conversion CTA Bridge</span>
                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">CTA Headline</label>
                    <input type="text" id="proj-field-cta-title" name="cta_title" value="Planning an Editorial Production?" class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none focus:border-zinc-900" />
                </div>
                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">CTA Subtitle</label>
                    <input type="text" id="proj-field-cta-subtitle" name="cta_subtitle" value="Review our production calendar and commission custom shoot dates." class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none focus:border-zinc-900" />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" id="proj-field-cta-button" name="cta_button_text" value="Book Creative Consultation" class="h-9.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none" />
                    <input type="text" id="proj-field-cta-url" name="cta_button_url" value="/contact" class="h-9.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none" />
                </div>
            </div>

            <!-- SEO Card -->
            <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200/80 dark:border-zinc-800/80 space-y-3">
                <span class="text-[11px] font-mono text-zinc-500 font-bold uppercase tracking-wider block">Search Engine & Social Discovery</span>
                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">SEO Title Tag</label>
                    <input 
                        type="text" 
                        id="proj-field-meta-title" 
                        name="meta_title" 
                        placeholder="[Client] Showcase | Cora Studio Portfolio"
                        class="h-9.5 w-full px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none focus:border-zinc-900"
                    />
                </div>
                <div>
                    <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block mb-1">SEO Meta Description</label>
                    <textarea 
                        id="proj-field-meta-desc" 
                        name="meta_description" 
                        rows="2" 
                        placeholder="Short summary for Google search snippets and social preview cards."
                        class="w-full p-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none focus:border-zinc-900"
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Sticky Drawer Footer Controls -->
        <div class="px-6 sm:px-8 py-4 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 shadow-xs">
            <!-- Monochromatic Status Segmented Toggle -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-zinc-500 font-medium">Status:</span>
                <div class="inline-flex items-center p-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-xl border border-zinc-200/70 dark:border-zinc-700" id="proj-status-toggle-group">
                    <button type="button" onclick="coraSetDrawerStatus('published')" id="status-btn-published" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer transition-all">Published</button>
                    <button type="button" onclick="coraSetDrawerStatus('draft')" id="status-btn-draft" class="px-2.5 py-1 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer transition-all">Draft</button>
                </div>
                <input type="hidden" id="proj-field-status" name="status" value="published" />
            </div>

            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    onclick="coraCloseProjectDrawer()" 
                    class="px-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition cursor-pointer"
                >
                    Cancel
                </button>

                <button 
                    type="submit" 
                    id="cora-proj-save-btn"
                    class="px-5 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-900 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold transition shadow-xs cursor-pointer active:scale-98 flex items-center gap-1.5"
                >
                    Save Project
                </button>
            </div>
        </div>

    </form>
</aside>

<!-- CLIENT JAVASCRIPT CONTROLLER -->
<script>
window.coraProjectsData = <?php echo json_encode( array_map( array( 'Cora_Projects_Module', 'format_project_for_api' ), $projects_raw ) ); ?>;
window.coraActiveAgencyId = "<?php echo esc_js( $agency_id ); ?>";

// VIEW MODE SWITCHER (Table vs Grid)
function coraSetViewMode(mode) {
    var tableView = document.getElementById('cora-projects-table-view');
    var gridView = document.getElementById('cora-projects-grid-view');
    var tableBtn = document.getElementById('view-mode-table-btn');
    var gridBtn = document.getElementById('view-mode-grid-btn');

    if (mode === 'grid') {
        if (tableView) tableView.classList.add('hidden');
        if (gridView) gridView.classList.remove('hidden');
        if (tableBtn) {
            tableBtn.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-900', 'dark:text-white', 'shadow-2xs');
            tableBtn.classList.add('text-zinc-500');
        }
        if (gridBtn) {
            gridBtn.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-900', 'dark:text-white', 'shadow-2xs');
            gridBtn.classList.remove('text-zinc-500');
        }
    } else {
        if (tableView) tableView.classList.remove('hidden');
        if (gridView) gridView.classList.add('hidden');
        if (tableBtn) {
            tableBtn.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-900', 'dark:text-white', 'shadow-2xs');
            tableBtn.classList.remove('text-zinc-500');
        }
        if (gridBtn) {
            gridBtn.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-900', 'dark:text-white', 'shadow-2xs');
            gridBtn.classList.add('text-zinc-500');
        }
    }
}

// TAB SWITCHING IN DRAWER (Pill Segmented Control)
document.querySelectorAll('.cora-proj-tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        var targetId = this.getAttribute('data-target');
        
        document.querySelectorAll('.cora-proj-tab-btn').forEach(function(b) {
            b.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
            b.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
        });
        this.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
        this.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');

        document.querySelectorAll('.cora-proj-tab-panel').forEach(function(p) {
            p.classList.remove('active');
        });
        var activePanel = document.getElementById(targetId);
        if (activePanel) activePanel.classList.add('active');
    });
});

// DRAWER FOOTER STATUS SWITCHER
function coraSetDrawerStatus(status) {
    var statusInput = document.getElementById('proj-field-status');
    var pubBtn = document.getElementById('status-btn-published');
    var dftBtn = document.getElementById('status-btn-draft');
    var badge = document.getElementById('cora-proj-header-badge');

    if (statusInput) statusInput.value = status;

    if (status === 'published') {
        if (pubBtn) {
            pubBtn.className = "px-2.5 py-1 rounded-lg text-xs font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer transition-all";
        }
        if (dftBtn) {
            dftBtn.className = "px-2.5 py-1 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer transition-all";
        }
        if (badge) {
            badge.innerText = "Published";
            badge.className = "px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-900 text-white dark:bg-white dark:text-zinc-900";
        }
    } else {
        if (pubBtn) {
            pubBtn.className = "px-2.5 py-1 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer transition-all";
        }
        if (dftBtn) {
            dftBtn.className = "px-2.5 py-1 rounded-lg text-xs font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer transition-all";
        }
        if (badge) {
            badge.innerText = "Draft";
            badge.className = "px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700";
        }
    }
}

// ACTIVE FILTER STATE
var coraActiveStatusFilter = 'all';

// HEADER SUB-TAB FILTER ROUTER
function coraSwitchTabFilter(status, tabBtn) {
    coraActiveStatusFilter = status || 'all';

    // Update active tab button styles
    document.querySelectorAll('.cora-sub-tabs-container .cora-sub-tab').forEach(function(btn) {
        btn.classList.remove('active', 'border-zinc-950', 'text-zinc-900', 'dark:border-white', 'dark:text-white', 'font-semibold');
        btn.classList.add('border-transparent', 'text-zinc-500', 'dark:text-zinc-400');
    });

    if (tabBtn) {
        tabBtn.classList.remove('border-transparent', 'text-zinc-500', 'dark:text-zinc-400');
        tabBtn.classList.add('active', 'border-zinc-950', 'text-zinc-900', 'dark:border-white', 'dark:text-white', 'font-semibold');
    }

    coraApplyProjectsFilter();
}

// SEARCH & STATUS COMBINED FILTER
function coraApplyProjectsFilter() {
    var queryInput = document.getElementById('cora-proj-search-input');
    var query = queryInput ? (queryInput.value || '').toLowerCase().trim() : '';
    var status = coraActiveStatusFilter || 'all';

    var rows = document.querySelectorAll('.cora-project-row');
    rows.forEach(function(row) {
        var rowStatus = row.getAttribute('data-status');
        var text = row.innerText.toLowerCase();

        var matchesStatus = (status === 'all' || rowStatus === status);
        var matchesSearch = (!query || text.indexOf(query) !== -1);

        if (matchesStatus && matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function coraFilterProjectsList() {
    coraApplyProjectsFilter();
}

// MOBILE EXPANDABLE SEARCH TOGGLE (< 640px)
function coraToggleMobileSearch(open) {
    var wrapper = document.getElementById('cora-proj-search-wrapper');
    var triggerBtn = document.getElementById('cora-proj-search-trigger-btn');
    var input = document.getElementById('cora-proj-search-input');
    
    if (open) {
        if (wrapper) wrapper.classList.add('mobile-expanded');
        if (triggerBtn) triggerBtn.classList.add('hidden');
        if (input) {
            input.focus();
        }
    } else {
        if (wrapper) wrapper.classList.remove('mobile-expanded');
        if (triggerBtn) triggerBtn.classList.remove('hidden');
        if (input) {
            input.value = '';
            input.blur();
        }
        coraFilterProjectsList();
    }
}

// OPEN DRAWER FOR CREATE
function coraOpenProjectDrawerForCreate() {
    document.getElementById('cora-project-form').reset();
    document.getElementById('proj-field-id').value = '0';
    document.getElementById('proj-field-agency-id').value = window.coraActiveAgencyId || '1';
    document.getElementById('cora-proj-drawer-title').innerText = 'New Project';
    coraSetDrawerStatus('published');
    
    // Reset KPIs
    document.getElementById('kpi-val-1').value = '';
    document.getElementById('kpi-lbl-1').value = '';
    document.getElementById('kpi-ctx-1').value = '';
    document.getElementById('kpi-val-2').value = '';
    document.getElementById('kpi-lbl-2').value = '';
    document.getElementById('kpi-ctx-2').value = '';
    document.getElementById('kpi-val-3').value = '';
    document.getElementById('kpi-lbl-3').value = '';
    document.getElementById('kpi-ctx-3').value = '';

    // Switch to first tab
    var firstTab = document.querySelector('.cora-proj-tab-btn[data-target="proj-tab-overview"]');
    if (firstTab) firstTab.click();

    coraShowProjectDrawer();
}

// EDIT PROJECT
function coraEditProject(id) {
    var project = (window.coraProjectsData || []).find(function(p) { return String(p.id) === String(id); });
    if (!project) return;

    document.getElementById('proj-field-id').value = project.id;
    document.getElementById('proj-field-agency-id').value = project.agencyId || window.coraActiveAgencyId || '1';
    document.getElementById('cora-proj-drawer-title').innerText = 'Edit Project';
    document.getElementById('proj-field-title').value = project.title || '';
    document.getElementById('proj-field-client-name').value = project.clientName || '';
    document.getElementById('proj-field-client-industry').value = project.clientIndustry || '';
    document.getElementById('proj-field-duration').value = project.projectDuration || '';
    document.getElementById('proj-field-slug').value = project.slug || '';
    document.getElementById('proj-field-services').value = (project.services || []).join(', ');
    document.getElementById('proj-field-subtitle').value = project.subtitle || '';
    document.getElementById('proj-field-confidential').checked = !!project.isConfidential;
    document.getElementById('proj-field-featured').checked = !!project.isFeatured;
    coraSetDrawerStatus(project.status || 'published');

    // Narrative
    document.getElementById('proj-field-exec-summary').value = project.executiveSummary || '';
    document.getElementById('proj-field-challenge').value = project.challengeContent || '';
    document.getElementById('proj-field-solution').value = project.solutionContent || '';
    document.getElementById('proj-field-deliverables').value = (project.deliverables || []).join('\n');

    // KPIs
    var kpis = project.kpis || [];
    document.getElementById('kpi-val-1').value = kpis[0] ? kpis[0].value : '';
    document.getElementById('kpi-lbl-1').value = kpis[0] ? kpis[0].label : '';
    document.getElementById('kpi-ctx-1').value = kpis[0] ? (kpis[0].context || '') : '';
    document.getElementById('kpi-val-2').value = kpis[1] ? kpis[1].value : '';
    document.getElementById('kpi-lbl-2').value = kpis[1] ? kpis[1].label : '';
    document.getElementById('kpi-ctx-2').value = kpis[1] ? (kpis[1].context || '') : '';
    document.getElementById('kpi-val-3').value = kpis[2] ? kpis[2].value : '';
    document.getElementById('kpi-lbl-3').value = kpis[2] ? kpis[2].label : '';
    document.getElementById('kpi-ctx-3').value = kpis[2] ? (kpis[2].context || '') : '';

    // Media
    document.getElementById('proj-field-hero-image').value = project.heroImageUrl || '';
    document.getElementById('proj-field-live-url').value = project.liveUrl || '';
    var galleryUrls = (project.galleryImages || []).map(function(g) { return g.url || g; });
    document.getElementById('proj-field-gallery').value = galleryUrls.join('\n');

    // Testimonial
    if (project.testimonial) {
        document.getElementById('proj-field-test-quote').value = project.testimonial.quote || '';
        document.getElementById('proj-field-test-author').value = project.testimonial.author || '';
        document.getElementById('proj-field-test-role').value = project.testimonial.role || '';
        document.getElementById('proj-field-test-company').value = project.testimonial.company || '';
    } else {
        document.getElementById('proj-field-test-quote').value = '';
        document.getElementById('proj-field-test-author').value = '';
        document.getElementById('proj-field-test-role').value = '';
        document.getElementById('proj-field-test-company').value = '';
    }

    // CTA & SEO
    document.getElementById('proj-field-cta-title').value = project.ctaTitle || 'Planning an Editorial Production?';
    document.getElementById('proj-field-cta-subtitle').value = project.ctaSubtitle || '';
    document.getElementById('proj-field-cta-button').value = project.ctaButtonText || 'Book Creative Consultation';
    document.getElementById('proj-field-cta-url').value = project.ctaButtonUrl || '/contact';
    document.getElementById('proj-field-meta-title').value = project.metaTitle || '';
    document.getElementById('proj-field-meta-desc').value = project.metaDescription || '';

    // Switch to first tab
    var firstTab = document.querySelector('.cora-proj-tab-btn[data-target="proj-tab-overview"]');
    if (firstTab) firstTab.click();

    coraShowProjectDrawer();
}

// DYNAMIC BOUNDS SYNCHRONIZER (BOUND TO AVAILABLE MAIN WORKSPACE AREA)
function coraSyncMainAreaBounds() {
    var drawer = document.getElementById('cora-project-drawer');
    var sidebarEl = document.querySelector('.cora-sidebar');
    var isMobile = window.innerWidth < 1024;
    var isSidebarCollapsed = sidebarEl && sidebarEl.classList.contains('collapsed-sidebar');

    if (drawer) {
        // Reflect collapsed state class on drawer element directly
        if (isSidebarCollapsed) {
            drawer.classList.add('sidebar-collapsed');
        } else {
            drawer.classList.remove('sidebar-collapsed');
        }

        // If drawer is currently maximized (fullscreen ⤢), keep full viewport bounds
        if (drawer.classList.contains('maximized')) {
            drawer.style.left = '0px';
            drawer.style.top = '52px';
            drawer.style.width = '100vw';
            drawer.style.maxWidth = '100vw';
            drawer.style.height = (window.innerHeight - 52) + 'px';
            drawer.style.maxHeight = (window.innerHeight - 52) + 'px';
            return;
        }

        // Calculate available main canvas bounds directly against sidebar boundary
        var topbarEl = document.getElementById('cora-global-topbar');
        var actualTop = topbarEl ? Math.round(topbarEl.getBoundingClientRect().bottom) : 52;
        if (actualTop < 0 || isNaN(actualTop)) actualTop = 52;

        var targetLeft = 0;
        if (!isMobile && sidebarEl) {
            var sRect = sidebarEl.getBoundingClientRect();
            // Use precise right boundary of the sidebar so drawer seamlessly docks with 0 gap
            targetLeft = Math.round(sRect.right);
            if (targetLeft <= 0 || isNaN(targetLeft)) {
                targetLeft = isSidebarCollapsed ? 64 : 256;
            }
        }
        var targetWidth = window.innerWidth - targetLeft;
        var targetHeight = window.innerHeight - actualTop;

        document.documentElement.style.setProperty('--cora-main-left', targetLeft + 'px');
        document.documentElement.style.setProperty('--cora-main-top', actualTop + 'px');
        document.documentElement.style.setProperty('--cora-main-width', targetWidth + 'px');
        document.documentElement.style.setProperty('--cora-main-height', targetHeight + 'px');

        // Set responsive inline bounds dynamically
        drawer.style.left = targetLeft + 'px';
        drawer.style.top = actualTop + 'px';
        drawer.style.width = targetWidth + 'px';
        drawer.style.maxWidth = targetWidth + 'px';
        drawer.style.height = targetHeight + 'px';
        drawer.style.maxHeight = targetHeight + 'px';
    }
}

// Continuously sync bounds over a duration (e.g. during a 250ms CSS animation)
function coraAnimateSyncBounds(durationMs) {
    var start = Date.now();
    var duration = durationMs || 280;
    function frame() {
        coraSyncMainAreaBounds();
        if (Date.now() - start < duration) {
            requestAnimationFrame(frame);
        } else {
            coraSyncMainAreaBounds();
        }
    }
    requestAnimationFrame(frame);
}

window.addEventListener('resize', coraSyncMainAreaBounds);
window.addEventListener('cora:sidebar-toggle', function() {
    coraAnimateSyncBounds(300);
});

// Observe sidebar changes (expand/collapse) to sync available space in real time
if (window.MutationObserver) {
    var coraSidebarObserver = new MutationObserver(function() {
        coraAnimateSyncBounds(250);
    });
    var sidebarEl = document.querySelector('.cora-sidebar');
    if (sidebarEl) {
        coraSidebarObserver.observe(sidebarEl, { attributes: true, attributeFilter: ['class', 'style'] });
        sidebarEl.addEventListener('transitionend', function(e) {
            coraSyncMainAreaBounds();
        });
    }
    coraSidebarObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });
}

// SHOW MAIN-AREA DOCKED DRAWER PANEL
function coraShowProjectDrawer() {
    var drawer = document.getElementById('cora-project-drawer');
    var mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');

    // Body Portaling: Guarantee drawer sits at document.body level with no clipping or layout offset
    if (drawer && drawer.parentNode !== document.body) {
        document.body.appendChild(drawer);
    }

    if (drawer) {
        drawer.classList.remove('collapsed');
        drawer.classList.add('open');

        // Immediately sync bounds to sidebar edge
        coraSyncMainAreaBounds();

        // Lock ONLY main content area scroll; SIDEBAR REMAINS UNLOCKED & FULLY ACCESSIBLE
        if (mainEl) {
            mainEl.classList.add('overflow-hidden');
        }
    }
}

// TOGGLE EXPAND / MAXIMIZE CANVAS (SHOPIFY EXPAND ⤢ MODE)
function coraToggleProjectDrawerMaximize() {
    var drawer = document.getElementById('cora-project-drawer');
    var maxBtn = document.getElementById('cora-proj-maximize-btn');
    if (!drawer) return;

    var isMax = drawer.classList.toggle('maximized');
    if (isMax) {
        drawer.style.left = '0px';
        drawer.style.width = '100vw';
        drawer.style.maxWidth = '100vw';
        if (maxBtn) {
            maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14h6v6"></path><path d="M20 10h-6V4"></path><path d="M14 10l7-7"></path><path d="M3 21l7-7"></path></svg>';
            maxBtn.title = "Restore Docked Canvas";
        }
    } else {
        if (maxBtn) {
            maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>';
            maxBtn.title = "Expand / Fullscreen Canvas";
        }
        coraSyncMainAreaBounds();
    }
}

// HIDE MAIN-AREA DOCKED DRAWER PANEL & RESTORE MAIN SCROLL
function coraCloseProjectDrawer() {
    var drawer = document.getElementById('cora-project-drawer');
    var mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');

    if (drawer) {
        drawer.classList.remove('open');
        drawer.classList.add('collapsed');
        drawer.classList.remove('maximized');

        // Restore main content area scroll
        if (mainEl) {
            mainEl.classList.remove('overflow-hidden');
        }
    }
}

// ESC KEY LISTENER TO CLOSE DRAWER
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
        coraCloseProjectDrawer();
    }
});

// SUBMIT PROJECT FORM
function coraSubmitProjectForm(e) {
    e.preventDefault();
    var saveBtn = document.getElementById('cora-proj-save-btn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerText = 'Saving...';
    }

    var formData = new FormData(document.getElementById('cora-project-form'));
    formData.append('action', 'cora_ajax_save_project');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    // Build KPIs array
    var kpis = [];
    var val1 = document.getElementById('kpi-val-1').value.trim();
    if (val1) {
        kpis.push({ value: val1, label: document.getElementById('kpi-lbl-1').value.trim() || 'Key Metric', context: document.getElementById('kpi-ctx-1').value.trim() });
    }
    var val2 = document.getElementById('kpi-val-2').value.trim();
    if (val2) {
        kpis.push({ value: val2, label: document.getElementById('kpi-lbl-2').value.trim() || 'Secondary Metric', context: document.getElementById('kpi-ctx-2').value.trim() });
    }
    var val3 = document.getElementById('kpi-val-3').value.trim();
    if (val3) {
        kpis.push({ value: val3, label: document.getElementById('kpi-lbl-3').value.trim() || 'Tertiary Metric', context: document.getElementById('kpi-ctx-3').value.trim() });
    }
    formData.append('kpis', JSON.stringify(kpis));

    // Build Deliverables array
    var delivText = document.getElementById('proj-field-deliverables').value;
    var deliverables = delivText.split('\n').map(function(s) { return s.trim(); }).filter(Boolean);
    formData.append('deliverables', JSON.stringify(deliverables));

    // Build Gallery Images array
    var galText = document.getElementById('proj-field-gallery').value;
    var gallery = galText.split('\n').map(function(url) {
        url = url.trim();
        return url ? { url: url } : null;
    }).filter(Boolean);
    formData.append('gallery_images', JSON.stringify(gallery));

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Project';
        }

        if (data.success) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message || 'Project saved successfully.', 'success');
            }
            coraCloseProjectDrawer();
            setTimeout(function() { window.location.reload(); }, 600);
        } else {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast((data.data && data.data.message) ? data.data.message : 'Error saving project', 'error');
            }
        }
    })
    .catch(function(err) {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Project';
        }
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Network error while saving.', 'error');
        }
    });
}

// TOGGLE PUBLISH STATUS
function coraToggleProjectPublish(id) {
    var formData = new FormData();
    formData.append('action', 'cora_ajax_toggle_project_publish');
    formData.append('id', id);
    formData.append('agency_id', window.coraActiveAgencyId || '1');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            var textElem = document.getElementById('status-pill-text-' + id);
            if (textElem) {
                textElem.innerText = data.data.new_status === 'published' ? 'Published' : 'Draft';
            }
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message, 'success');
            }
            setTimeout(function() { window.location.reload(); }, 500);
        } else {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message || 'Failed to toggle status.', 'error');
            }
        }
    })
    .catch(function() {
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Network error toggling status.', 'error');
        }
    });
}

// DUPLICATE PROJECT
function coraDuplicateProject(id) {
    var formData = new FormData();
    formData.append('action', 'cora_ajax_duplicate_project');
    formData.append('id', id);
    formData.append('agency_id', window.coraActiveAgencyId || '1');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message, 'success');
            }
            setTimeout(function() { window.location.reload(); }, 500);
        }
    });
}

// DELETE PROJECT
function coraDeleteProject(id) {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Deleting project...', 'info');
    }

    var formData = new FormData();
    formData.append('action', 'cora_ajax_delete_project');
    formData.append('id', id);
    formData.append('agency_id', window.coraActiveAgencyId || '1');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message, 'success');
            }
            var rows = document.querySelectorAll('.cora-project-row[data-id="' + id + '"]');
            rows.forEach(function(r) { r.remove(); });
            setTimeout(function() { window.location.reload(); }, 600);
        }
    });
}

// LOAD STARTER PROJECTS (On-Demand Seeding)
function coraLoadStarterProjects() {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Loading workspace starter projects...', 'info');
    }

    var formData = new FormData();
    formData.append('action', 'cora_ajax_seed_starter_projects');
    formData.append('agency_id', window.coraActiveAgencyId || '1');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Starter projects loaded successfully.', 'success');
            }
            setTimeout(function() { window.location.reload(); }, 500);
        }
    });
}

// COPY PUBLIC URL
function coraCopyProjectUrl(slug) {
    var url = '<?php echo esc_js( $frontend_base ); ?>/portfolio/' + slug;
    navigator.clipboard.writeText(url).then(function() {
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Public portfolio link copied to clipboard.', 'success');
        }
    });
}
</script>
