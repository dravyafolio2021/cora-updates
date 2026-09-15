<?php
/**
 * Cora Workspace - Stationery Manufacturing & Mobile Van Sales Inventory Engine
 * File: views/view-inventory-management.php
 * 
 * Single point of control for stationery plant stock, dynamic van consignment allocations (₹1k to ₹10L+),
 * live GPS route tracking, Multimodal AI invoice OCR scanning, and automated 24-hour daily audit reports.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$agency_id = function_exists( 'cora_get_current_user_agency_id' ) ? cora_get_current_user_agency_id() : 1;
$workspace_members = function_exists( 'cora_get_workspace_team_members' ) ? cora_get_workspace_team_members() : array();
$current_user = wp_get_current_user();
$current_user_roles = ! empty( $current_user->roles ) ? (array) $current_user->roles : array();
$is_driver_only = ( function_exists( 'cora_user_is_field_driver' ) && cora_user_is_field_driver( $current_user ) )
    || in_array( 'cora_field_vendor', $current_user_roles, true )
    || ( get_user_meta( $current_user->ID, 'cora_role', true ) === 'cora_field_vendor' )
    || isset( $_GET['driver_mode'] )
    || isset( $_GET['view_as_driver'] )
    || isset( $_GET['is_driver'] );

global $wpdb;
$table_products = $wpdb->prefix . 'cora_inventory_products';
$db_categories = array();
if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table_products ) ) {
    $db_categories = $wpdb->get_results( $wpdb->prepare(
        "SELECT category, COUNT(*) as count FROM {$table_products} WHERE agency_id = %d AND status != 'deleted' AND category IS NOT NULL AND category != '' GROUP BY category ORDER BY count DESC, category ASC",
        $agency_id
    ), ARRAY_A );
}

if ( ! function_exists( 'cora_format_category_name' ) ) {
    function cora_format_category_name( $slug ) {
        $map = array(
            'notebooks'           => 'Notebooks, Registers & Bahee',
            'school_supplies'     => 'School Essentials (Pencils/Erasers)',
            'paper_reams'         => 'Paper Reams & Sheets',
            'writing_instruments' => 'Writing Instruments & Pens',
            'office_supplies'     => 'Office Supplies & Binding',
            'art_kits'            => 'Art & Craft Supplies',
            'art_supplies'        => 'Art & Craft Supplies',
            'adhesives'           => 'Adhesives, Glue & Tapes',
            'files_folders'       => 'Files, Folders & Storage',
        );
        if ( isset( $map[ $slug ] ) ) {
            return $map[ $slug ];
        }
        return ucwords( str_replace( array( '_', '-' ), ' ', (string) $slug ) );
    }
}
?>

<style>
/* Scoped Product Drawer Stepper & Active Tab Styles */
#cora-inv-product-drawer .cora-step-tab {
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
#cora-inv-product-drawer .cora-step-tab.is-active {
    background-color: #18181b !important;
    color: #ffffff !important;
    border-color: #18181b !important;
    font-weight: 700 !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}
.dark #cora-inv-product-drawer .cora-step-tab.is-active {
    background-color: #f4f4f5 !important;
    color: #09090b !important;
    border-color: #f4f4f5 !important;
    font-weight: 700 !important;
}
#cora-inv-product-drawer .cora-step-tab.is-active:hover {
    color: #ffffff !important;
    background-color: #09090b !important;
}
.dark #cora-inv-product-drawer .cora-step-tab.is-active:hover {
    color: #09090b !important;
    background-color: #ffffff !important;
}
#cora-inv-product-drawer .cora-step-tab:not(.is-active) {
    background-color: transparent !important;
    color: #71717a !important;
    border-color: transparent !important;
    font-weight: 600 !important;
}
.dark #cora-inv-product-drawer .cora-step-tab:not(.is-active) {
    color: #a1a1aa !important;
}
#cora-inv-product-drawer .cora-step-tab:not(.is-active):hover {
    color: #18181b !important;
    background-color: #e4e4e7 !important;
}
.dark #cora-inv-product-drawer .cora-step-tab:not(.is-active):hover {
    color: #fafafa !important;
    background-color: #27272a !important;
}

/* Universal Driver Mode Overrides: Hides ALL admin chrome, header search/profile, mobile island, sidebar */
body.cora-driver-mode-active #cora-mobile-floating-island,
body.cora-driver-mode-active #cora-mobile-nav-drawer,
body.cora-driver-mode-active .cora-sidebar,
body.cora-driver-mode-active #cora-sidebar-backdrop,
body.cora-driver-mode-active #cora-header-profile-popover,
body.cora-driver-mode-active #cora-header-profile-backdrop,
body.cora-driver-mode-active .cora-topbar-desktop > *:not(.cora-driver-topbar-strip),
body.cora-driver-mode-active .cora-topbar-mobile > *:not(.cora-driver-topbar-strip),
body.cora-driver-mode-active #cora-inv-plant-container,
body.cora-driver-mode-active #cora-inv-perspective-switcher,
body.cora-driver-mode-active #cora-inv-plant-actions,
body.cora-driver-mode-active #cora-inv-plant-title-wrap {
    display: none !important;
}

body.cora-driver-mode-active .cora-driver-topbar-strip {
    display: flex !important;
}

body.cora-driver-mode-active #cora-inv-driver-title-wrap {
    display: block !important;
}

body.cora-driver-mode-active #cora-inv-vendor-actions {
    display: flex !important;
}

body.cora-driver-mode-active #cora-inv-vendor-container {
    display: block !important;
}

body.cora-driver-mode-active main.cora-main {
    margin-left: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    padding-bottom: 24px !important;
}
body.cora-driver-mode-active .cora-content-wrapper {
    padding-bottom: 24px !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* Ensure Mobile Floating Island is STRICTLY hidden on Desktop (>= 1024px) */
@media (min-width: 1024px) {
    #cora-mobile-floating-island,
    .cora-mobile-island-wrapper {
        display: none !important;
    }
}

/* ========================================================================= */
/* SINGLE-APP FOCUS MODE: HIDE OTHER SIDEBAR APPS TEMPORARILY                */
/* ========================================================================= */
body.cora-inventory-focus-mode #cora-sidebar-focus-banner {
    display: flex !important;
}

/* When sidebar is collapsed in collapsed mode, format banner cleanly as a compact icon pill */
.cora-sidebar.collapsed-sidebar #cora-sidebar-focus-banner,
.collapsed-sidebar #cora-sidebar-focus-banner,
body.collapsed-sidebar #cora-sidebar-focus-banner,
body.sidebar-collapsed #cora-sidebar-focus-banner,
aside.cora-sidebar.w-16 #cora-sidebar-focus-banner,
aside.cora-sidebar[style*="width: 64px"] #cora-sidebar-focus-banner,
aside.cora-sidebar[style*="width: 4rem"] #cora-sidebar-focus-banner {
    padding: 0 !important;
    margin: 6px auto !important;
    width: 36px !important;
    height: 36px !important;
    min-width: 36px !important;
    min-height: 36px !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    box-sizing: border-box !important;
}

.cora-sidebar.collapsed-sidebar #cora-sidebar-focus-banner p,
.cora-sidebar.collapsed-sidebar #cora-sidebar-focus-banner button,
.cora-sidebar.collapsed-sidebar #cora-sidebar-focus-banner > div > span:not(.animate-pulse),
.cora-sidebar.collapsed-sidebar #cora-sidebar-focus-banner > div > div > span:not(.animate-pulse),
.cora-sidebar.collapsed-sidebar #cora-sidebar-focus-banner > span,
.collapsed-sidebar #cora-sidebar-focus-banner p,
.collapsed-sidebar #cora-sidebar-focus-banner button,
.collapsed-sidebar #cora-sidebar-focus-banner span:not(.animate-pulse),
body.collapsed-sidebar #cora-sidebar-focus-banner p,
body.collapsed-sidebar #cora-sidebar-focus-banner button,
body.collapsed-sidebar #cora-sidebar-focus-banner span:not(.animate-pulse),
aside.cora-sidebar.w-16 #cora-sidebar-focus-banner p,
aside.cora-sidebar.w-16 #cora-sidebar-focus-banner button,
aside.cora-sidebar.w-16 #cora-sidebar-focus-banner span:not(.animate-pulse),
aside.cora-sidebar[style*="width: 64px"] #cora-sidebar-focus-banner p,
aside.cora-sidebar[style*="width: 64px"] #cora-sidebar-focus-banner button,
aside.cora-sidebar[style*="width: 64px"] #cora-sidebar-focus-banner span:not(.animate-pulse) {
    display: none !important;
}

.cora-sidebar.collapsed-sidebar #cora-sidebar-focus-banner .animate-pulse,
.collapsed-sidebar #cora-sidebar-focus-banner .animate-pulse,
body.collapsed-sidebar #cora-sidebar-focus-banner .animate-pulse,
aside.cora-sidebar.w-16 #cora-sidebar-focus-banner .animate-pulse,
aside.cora-sidebar[style*="width: 64px"] #cora-sidebar-focus-banner .animate-pulse {
    display: block !important;
    width: 8px !important;
    height: 8px !important;
    margin: 0 auto !important;
}

body.cora-inventory-focus-mode .cora-sidebar-nav .cora-nav-group {
    display: none !important;
}
body.cora-inventory-focus-mode .cora-sidebar-nav .cora-nav-group:has([data-target*="inventory"]),
body.cora-inventory-focus-mode .cora-sidebar-nav .cora-nav-group:has([data-target*="plant"]) {
    display: block !important;
}
body.cora-inventory-focus-mode .cora-sidebar-nav .cora-nav-group:has([data-target*="inventory"]) .cora-nav-list li:not([data-target*="inventory"]):not([data-target*="plant"]),
body.cora-inventory-focus-mode .cora-sidebar-nav .cora-nav-group:has([data-target*="plant"]) .cora-nav-list li:not([data-target*="inventory"]):not([data-target*="plant"]) {
    display: none !important;
}
body.cora-inventory-focus-mode .cora-sidebar-nav li:not([data-target*="inventory"]):not([data-target*="plant"]) {
    display: none !important;
}
body.cora-inventory-focus-mode .cora-sidebar-search {
    display: none !important;
}

/* Header Action & Toggle Pill Buttons Scoped Hover & Active States */
#cora-inv-focus-toggle-btn,
#cora-inv-analytics-toggle-btn {
    transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

/* Single-App Button: Inactive (Off) */
#cora-inv-focus-toggle-btn:not(.is-active) {
    background-color: #ffffff !important;
    color: #3f3f46 !important;
    border: 1px solid #e4e4e7 !important;
}
#cora-inv-focus-toggle-btn:not(.is-active):hover {
    background-color: #f4f4f5 !important;
    color: #09090b !important;
    border-color: #d4d4d8 !important;
}
.dark #cora-inv-focus-toggle-btn:not(.is-active) {
    background-color: #18181b !important;
    color: #d4d4d8 !important;
    border: 1px solid #27272a !important;
}
.dark #cora-inv-focus-toggle-btn:not(.is-active):hover {
    background-color: #27272a !important;
    color: #fafafa !important;
    border-color: #3f3f46 !important;
}

/* Single-App Button: Active State (Crisp, High-Contrast Black Pill) */
#cora-inv-focus-toggle-btn.is-active {
    background-color: #09090b !important;
    color: #ffffff !important;
    border: 1px solid #09090b !important;
}
#cora-inv-focus-toggle-btn.is-active * {
    color: #ffffff !important;
}
#cora-inv-focus-toggle-btn.is-active #cora-inv-focus-dot {
    background-color: #34d399 !important;
}
#cora-inv-focus-toggle-btn.is-active:hover {
    background-color: #27272a !important;
    color: #ffffff !important;
    border-color: #27272a !important;
}
#cora-inv-focus-toggle-btn.is-active:hover * {
    color: #ffffff !important;
}
.dark #cora-inv-focus-toggle-btn.is-active {
    background-color: #fafafa !important;
    color: #09090b !important;
    border: 1px solid #fafafa !important;
}
.dark #cora-inv-focus-toggle-btn.is-active * {
    color: #09090b !important;
}
.dark #cora-inv-focus-toggle-btn.is-active #cora-inv-focus-dot {
    background-color: #059669 !important;
}
.dark #cora-inv-focus-toggle-btn.is-active:hover {
    background-color: #e4e4e7 !important;
    color: #09090b !important;
    border-color: #e4e4e7 !important;
}
.dark #cora-inv-focus-toggle-btn.is-active:hover * {
    color: #09090b !important;
}

/* Analytics Toggle Button */
#cora-inv-analytics-toggle-btn:not(.is-hidden) {
    background-color: #ffffff !important;
    color: #3f3f46 !important;
    border: 1px solid #e4e4e7 !important;
}
#cora-inv-analytics-toggle-btn:not(.is-hidden):hover {
    background-color: #f4f4f5 !important;
    color: #09090b !important;
    border-color: #d4d4d8 !important;
}
.dark #cora-inv-analytics-toggle-btn:not(.is-hidden) {
    background-color: #18181b !important;
    color: #d4d4d8 !important;
    border: 1px solid #27272a !important;
}
.dark #cora-inv-analytics-toggle-btn:not(.is-hidden):hover {
    background-color: #27272a !important;
    color: #fafafa !important;
    border-color: #3f3f46 !important;
}

#cora-inv-analytics-toggle-btn.is-hidden {
    background-color: #f4f4f5 !important;
    color: #71717a !important;
    border: 1px solid #e4e4e7 !important;
}
#cora-inv-analytics-toggle-btn.is-hidden:hover {
    background-color: #e4e4e7 !important;
    color: #18181b !important;
    border-color: #d4d4d8 !important;
}
.dark #cora-inv-analytics-toggle-btn.is-hidden {
    background-color: #27272a !important;
    color: #a1a1aa !important;
    border: 1px solid #3f3f46 !important;
}
.dark #cora-inv-analytics-toggle-btn.is-hidden:hover {
    background-color: #3f3f46 !important;
    color: #fafafa !important;
    border-color: #52525b !important;
}
</style>

<div id="cora-inventory-module-root" class="w-full text-zinc-900 dark:text-zinc-100 font-sans pb-24" data-driver-only="<?php echo $is_driver_only ? '1' : '0'; ?>">
    
    <!-- Header & Platform Title (Clean, Minimalist & Friendly) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 mb-3 sm:mb-4 pb-3 sm:pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
        <div>
            <!-- Plant View Title Block -->
            <div id="cora-inv-plant-title-wrap" class="<?php echo $is_driver_only ? 'hidden' : ''; ?>">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold uppercase bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950">
                        Stationery Manufacturing
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-mono font-medium bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live
                    </span>
                    <!-- Single-App Dedicated Mode Toggle Button -->
                    <button type="button" id="cora-inv-focus-toggle-btn" onclick="CoraInventory.toggleFocusMode()" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-mono font-semibold cursor-pointer shadow-2xs" title="Toggle Single-App View (Hides other modules in sidebar)">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400" id="cora-inv-focus-dot"></span>
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><path d="M9 3v18"></path></svg>
                        <span id="cora-inv-focus-btn-text">Single-App: Off</span>
                    </button>
                    <!-- KPI Analytics Cards Visibility Toggle Button -->
                    <button type="button" id="cora-inv-analytics-toggle-btn" onclick="CoraInventory.toggleAnalyticsCards()" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-mono font-semibold cursor-pointer shadow-2xs" title="Show or hide summary metrics">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" id="cora-inv-analytics-icon"><path d="M18 20V10M12 20V4M6 20v-6"></path></svg>
                        <span id="cora-inv-analytics-btn-text">Analytics: Visible</span>
                    </button>
                </div>
                <h1 class="text-lg sm:text-xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">
                    Plant Inventory
                </h1>
            </div>

            <!-- Driver View Title Block -->
            <div id="cora-inv-driver-title-wrap" class="<?php echo $is_driver_only ? '' : 'hidden'; ?>">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950">
                        VAN POS
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-mono font-medium bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active
                    </span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">
                    Route Sales Ledger
                </h1>
            </div>
        </div>

        <!-- Right Side Header Controls (Context-Aware Action Suites) -->
        <div class="flex flex-wrap items-center justify-between sm:justify-end gap-1.5 sm:gap-2">
            <?php if ( ! $is_driver_only ) : ?>
                <!-- Perspective Switcher (Plant Inventory vs Mobile Van Sales) -->
                <div id="cora-inv-perspective-switcher" class="inline-flex items-center p-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-700/60 shrink-0">
                    <button type="button" id="cora-inv-btn-view-plant" onclick="CoraInventory.switchPerspective('plant')" class="px-2.5 sm:px-3 py-1 rounded-md text-xs font-semibold transition-all whitespace-nowrap bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs cursor-pointer">
                        Plant
                    </button>
                    <button type="button" id="cora-inv-btn-view-vendor" onclick="CoraInventory.switchPerspective('vendor')" class="px-2.5 sm:px-3 py-1 rounded-md text-xs font-semibold transition-all whitespace-nowrap text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer">
                        Van Sales
                    </button>
                </div>

                <!-- Suite A: Plant Command Center Actions (Shown ONLY in plant mode) -->
                <div id="cora-inv-plant-actions" class="items-center gap-1.5 sm:gap-2 flex flex-wrap">
                    <!-- Import -->
                    <button type="button" onclick="CoraInventory.openImportModal('csv')" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white hover:bg-zinc-50 text-zinc-800 dark:bg-zinc-900 dark:hover:bg-zinc-800/90 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 text-xs font-medium shadow-2xs transition-colors whitespace-nowrap cursor-pointer" title="Import Catalog">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Import</span>
                    </button>

                    <!-- Export -->
                    <button type="button" onclick="CoraInventory.openExportModal()" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white hover:bg-zinc-50 text-zinc-800 dark:bg-zinc-900 dark:hover:bg-zinc-800/90 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 text-xs font-medium shadow-2xs transition-colors whitespace-nowrap cursor-pointer" title="Export CSV / Print PDF">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <span>Export</span>
                    </button>

                    <!-- Dispatch Van -->
                    <button type="button" onclick="CoraInventory.openConsignmentModal()" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700 text-xs font-medium shadow-2xs transition-colors whitespace-nowrap cursor-pointer" title="Dispatch Van Consignment">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                        <span>Dispatch Van</span>
                    </button>

                    <!-- Add SKU -->
                    <button type="button" onclick="CoraInventory.openProductModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-semibold shadow-2xs transition-colors whitespace-nowrap cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 5v14M5 12h14"/></svg>
                        <span>Add SKU</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Suite B: Field Van Sales Actions (Shown in vendor mode) -->
            <div id="cora-inv-vendor-actions" class="items-center gap-1.5 sm:gap-2 <?php echo $is_driver_only ? 'flex' : 'hidden'; ?> shrink-0">
                <!-- Secondary: AI Bill Scanner -->
                <button type="button" onclick="CoraInventory.focusOCRUpload()" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700 text-xs font-semibold shadow-2xs transition-colors whitespace-nowrap cursor-pointer" title="Scan Physical Invoice with Multimodal AI">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                    <span>Scan Bill</span>
                </button>

                <!-- Primary: Quick Spot Sale -->
                <button type="button" onclick="CoraInventory.openSpotSaleSheet()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-bold shadow-2xs transition-colors whitespace-nowrap cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Spot Sale</span>
                </button>
            </div>
        </div>
    </div>

    <?php if ( ! $is_driver_only ) : ?>
    <!-- ========================================================================= -->
    <!-- 1. PLANT MANAGER COMMAND CENTER VIEW                                     -->
    <!-- ========================================================================= -->
    <div id="cora-inv-plant-container" class="space-y-3 sm:space-y-4">

        <!-- 4 Hero Metric Summary Cards (Toggleable & Responsive) -->
        <div id="cora-inv-kpis-container" class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 transition-all duration-300">
            
            <!-- KPI 1: Plant Inventory Worth -->
            <div class="p-3 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                <div>
                    <div class="flex items-center justify-between text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mb-1">
                        <span class="truncate">Plant Stock</span>
                        <span class="w-6 h-6 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        </span>
                    </div>
                    <div class="text-base sm:text-lg font-bold font-mono tracking-tight text-zinc-950 dark:text-zinc-50" id="cora-kpi-plant-val">
                        ₹0
                    </div>
                </div>
                <div class="flex items-center gap-1 mt-1 text-[10.5px] text-zinc-500 truncate">
                    <span class="font-mono font-semibold text-zinc-700 dark:text-zinc-300" id="cora-kpi-plant-units">0</span> units • <span class="font-semibold text-zinc-700 dark:text-zinc-300" id="cora-kpi-plant-skus">0</span> SKUs
                </div>
            </div>

            <!-- KPI 2: Live Stock on Wheels (In Transit / Van Allocations) -->
            <div class="p-3 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                <div>
                    <div class="flex items-center justify-between text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mb-1">
                        <span class="truncate">Stock on Wheels</span>
                        <span class="w-6 h-6 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                        </span>
                    </div>
                    <div class="text-base sm:text-lg font-bold font-mono tracking-tight text-zinc-950 dark:text-zinc-50" id="cora-kpi-wheels-val">
                        ₹0
                    </div>
                </div>
                <div class="flex items-center gap-1.5 mt-1 text-[10.5px] text-zinc-500 truncate">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="font-semibold text-zinc-700 dark:text-zinc-300" id="cora-kpi-active-consignments">0</span> active van routes
                </div>
            </div>

            <!-- KPI 3: Today's Realized Field Sales -->
            <div class="p-3 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                <div>
                    <div class="flex items-center justify-between text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mb-1">
                        <span class="truncate">Today's Sales</span>
                        <span class="w-6 h-6 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        </span>
                    </div>
                    <div class="text-base sm:text-lg font-bold font-mono tracking-tight text-zinc-950 dark:text-zinc-50" id="cora-kpi-today-sales">
                        ₹0
                    </div>
                </div>
                <div class="flex items-center gap-1 mt-1 text-[10.5px] text-zinc-500 truncate">
                    <span>Collections:</span>
                    <span class="font-mono font-bold text-zinc-800 dark:text-zinc-200" id="cora-kpi-today-collections">₹0</span>
                </div>
            </div>

            <!-- KPI 4: 24h Reconciliation Discrepancy & Health -->
            <div class="p-3 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs relative overflow-hidden flex flex-col justify-between hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                <div>
                    <div class="flex items-center justify-between text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mb-1">
                        <span class="truncate">Reconciliation</span>
                        <span class="w-6 h-6 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </span>
                    </div>
                    <div class="text-base sm:text-lg font-bold font-mono tracking-tight text-zinc-950 dark:text-zinc-50 flex items-center gap-1.5">
                        <span id="cora-kpi-recon-pct">99.8%</span>
                        <span class="text-[9.5px] px-1 py-0.2 rounded font-sans font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">Clean</span>
                    </div>
                </div>
                <div class="flex items-center gap-1 mt-1 text-[10.5px] text-zinc-500 truncate">
                    <span class="text-zinc-700 dark:text-zinc-300 font-semibold" id="cora-kpi-low-stock-alert">0 items</span> below reorder alert
                </div>
            </div>

        </div>

        <!-- 5-Segmented Sub-Tab Switcher (Clean Minimal Line) -->
        <div class="flex items-center gap-1 border-b border-zinc-200 dark:border-zinc-800 overflow-x-auto no-scrollbar pt-1">
            <button type="button" onclick="CoraInventory.switchSubtab('catalog')" id="cora-tab-btn-catalog" class="px-3.5 py-2 text-xs font-bold border-b-2 border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 transition-colors whitespace-nowrap flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>
                <span>Catalog</span>
            </button>

            <button type="button" onclick="CoraInventory.switchSubtab('consignments')" id="cora-tab-btn-consignments" class="px-3.5 py-2 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors whitespace-nowrap flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                <span>Van Consignments</span>
            </button>

            <button type="button" onclick="CoraInventory.switchSubtab('map')" id="cora-tab-btn-map" class="px-3.5 py-2 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors whitespace-nowrap flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                <span>Route GPS Map</span>
            </button>

            <button type="button" onclick="CoraInventory.switchSubtab('ocr')" id="cora-tab-btn-ocr" class="px-3.5 py-2 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors whitespace-nowrap flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                <span>Invoice OCR</span>
            </button>

            <button type="button" onclick="CoraInventory.switchSubtab('recon')" id="cora-tab-btn-recon" class="px-3.5 py-2 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors whitespace-nowrap flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                <span>Audit &amp; Recon</span>
            </button>
        </div>

        <!-- SUBTAB 1: Central Plant Catalog (Spreadsheet Grid Engine) -->
        <div id="cora-subtab-catalog" class="space-y-3">
            
            <!-- Compact, Unified Filter & Search Bar (Clean & Decluttered Single Row) -->
            <div class="flex flex-wrap items-center justify-between gap-2 p-2 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                
                <!-- Left: View Mode (Default vs Spreadsheet) + Item Type Dropdown (Replaces the 3 big buttons) -->
                <div class="flex flex-wrap items-center gap-1.5">
                    
                    <!-- View Mode Switcher -->
                    <div class="flex items-center p-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60">
                        <button type="button" id="cora-view-btn-default" onclick="CoraInventory.setViewMode('default')" class="cora-view-mode-btn px-2.5 py-1 rounded-md text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs cursor-pointer flex items-center gap-1" title="Default Catalog View">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                            <span>Default</span>
                        </button>
                        <button type="button" id="cora-view-btn-spreadsheet" onclick="CoraInventory.setViewMode('spreadsheet')" class="cora-view-mode-btn px-2.5 py-1 rounded-md text-xs font-medium transition-all text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer flex items-center gap-1" title="Spreadsheet Sheets View">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                            <span>Sheet</span>
                        </button>
                    </div>

                    <!-- Compact Pricing Type Dropdown (Uncluttered replacement for the wide 3-button panel) -->
                    <select id="cora-filter-pricing-select" onchange="CoraInventory.setPricingFilter(this.value)" class="px-2.5 py-1 text-xs font-medium rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none cursor-pointer text-zinc-800 dark:text-zinc-200">
                        <option value="all" selected>All Items (0)</option>
                        <option value="weight_based">Weight-Based (0)</option>
                        <option value="unit_based">Unit-Based (0)</option>
                    </select>

                    <!-- Dynamic Category Selector -->
                    <select id="cora-inv-cat-filter" onchange="CoraInventory.loadCatalog()" class="px-2.5 py-1 text-xs font-medium rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none cursor-pointer">
                        <option value="all">All Categories</option>
                        <?php if ( ! empty( $db_categories ) ) : ?>
                            <?php foreach ( $db_categories as $c ) : ?>
                                <option value="<?php echo esc_attr( $c['category'] ); ?>"><?php echo esc_html( cora_format_category_name( $c['category'] ) . ' (' . $c['count'] . ')' ); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Right: Search + Sort + Low Stock + Refresh -->
                <div class="flex flex-wrap items-center gap-1.5 flex-1 justify-end">
                    
                    <!-- Search Input -->
                    <div class="relative min-w-[140px] sm:min-w-[170px] max-w-xs">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="cora-inv-search" oninput="CoraInventory.debouncedSearch()" placeholder="Search..." class="w-full pl-7 pr-2.5 py-1 text-xs rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none focus:border-zinc-400 transition-colors">
                    </div>

                    <!-- Ordering Priority Dropdown -->
                    <select id="cora-inv-sort-filter" onchange="CoraInventory.loadCatalog()" class="px-2 py-1 text-xs font-medium rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none cursor-pointer">
                        <option value="default" selected>Latest Added</option>
                        <option value="weight_first">Weight First</option>
                        <option value="unit_first">Unit First</option>
                        <option value="price_desc">Price: High to Low</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="stock_desc">Stock: High to Low</option>
                        <option value="name_asc">Name (A-Z)</option>
                    </select>

                    <label class="inline-flex items-center gap-1 text-xs text-zinc-600 dark:text-zinc-400 cursor-pointer select-none px-1">
                        <input type="checkbox" id="cora-inv-low-stock-check" onchange="CoraInventory.loadCatalog()" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                        <span class="text-[11px]">Low Stock</span>
                    </label>

                    <button type="button" onclick="CoraInventory.loadCatalog()" class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" title="Refresh">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    </button>
                </div>
            </div>

            <!-- BATCH ACTION BAR (Shown when 1 or more items are selected) -->
            <div id="cora-inv-batch-bar" class="hidden rounded-2xl bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 px-4 py-2.5 shadow-xl flex flex-wrap items-center justify-between gap-3 transition-all duration-200">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-950 text-xs font-mono font-bold" id="cora-inv-batch-count">0</span>
                    <span class="text-xs font-semibold"><span id="cora-inv-batch-count-label">0 items</span> selected</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="CoraInventory.selectAllVisible()" class="px-2.5 py-1 rounded-xl bg-zinc-800 hover:bg-zinc-700 dark:bg-zinc-200 dark:hover:bg-zinc-300 text-white dark:text-zinc-900 text-xs font-semibold transition-colors cursor-pointer">
                        Select All (<span id="cora-inv-batch-total-visible">0</span>)
                    </button>
                    <button type="button" onclick="CoraInventory.clearSelection()" class="px-2.5 py-1 rounded-xl bg-transparent hover:bg-zinc-800 dark:hover:bg-zinc-200 text-zinc-300 dark:text-zinc-700 text-xs font-medium transition-colors cursor-pointer">
                        Deselect
                    </button>
                    <div class="h-4 w-px bg-zinc-700 dark:bg-zinc-300 mx-0.5"></div>
                    <button type="button" onclick="CoraInventory.openExportModal('selected')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 dark:bg-zinc-200 dark:hover:bg-zinc-300 text-white dark:text-zinc-950 text-xs font-semibold shadow-xs transition-colors cursor-pointer" title="Export Selected SKUs">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <span>Export Selected (<span id="cora-inv-batch-export-btn-count">0</span>)</span>
                    </button>
                    <button type="button" onclick="CoraInventory.openBulkDeleteModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition-colors cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        <span>Delete Selected (<span id="cora-inv-batch-del-btn-count">0</span>)</span>
                    </button>
                </div>
            </div>

            <!-- DESKTOP VIEW: Dual Mode Table (Default Notion View + Optional Spreadsheet Grid) -->
            <div class="hidden md:block rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <!-- 1. DEFAULT VIEW HEADER (Active by default, 7 Columns) -->
                        <thead id="cora-inv-thead-default">
                            <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-800/50 text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider font-mono">
                                <th class="py-3 px-3.5 w-12 text-center">
                                    <input type="checkbox" id="cora-inv-select-all" onchange="CoraInventory.toggleSelectAll(this)" class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900 cursor-pointer" title="Select All Items">
                                </th>
                                <th class="py-3 px-4 min-w-[200px] max-w-[280px]">Product / SKU</th>
                                <th class="py-3 px-4">Category &amp; Packaging</th>
                                <th class="py-3 px-4 font-mono">HSN &amp; Tax</th>
                                <th class="py-3 px-4 font-mono">Wholesale &amp; MRP</th>
                                <th class="py-3 px-4 font-mono">Stock Units</th>
                                <th class="py-3 px-4 text-center w-28">Actions</th>
                            </tr>
                        </thead>

                        <!-- 2. SPREADSHEET VIEW HEADER (Optional Excel/Sheets 9-Column Grid) -->
                        <thead id="cora-inv-thead-spreadsheet" class="hidden">
                            <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-100/80 dark:bg-zinc-800/60 text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider font-mono">
                                <th class="py-3 px-3 w-28 text-center border-r border-zinc-200/60 dark:border-zinc-800/80">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <input type="checkbox" id="cora-inv-select-all-sheet" onchange="CoraInventory.toggleSelectAll(this)" class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900 cursor-pointer" title="Select All Items">
                                        <span>1. Qty</span>
                                    </div>
                                </th>
                                <th class="py-3 px-4 min-w-[200px] border-r border-zinc-200/60 dark:border-zinc-800/80">2. Item Name</th>
                                <th class="py-3 px-3 w-24 text-center border-r border-zinc-200/60 dark:border-zinc-800/80">3. Pages</th>
                                <th class="py-3 px-3 w-32 text-center border-r border-zinc-200/60 dark:border-zinc-800/80">4. Weight (Unit)</th>
                                <th class="py-3 px-3 w-28 text-right font-mono border-r border-zinc-200/60 dark:border-zinc-800/80">5. Unit Price</th>
                                <th class="py-3 px-3 w-32 text-right font-mono border-r border-zinc-200/60 dark:border-zinc-800/80">6. Total Price</th>
                                <th class="py-3 px-3 w-36 border-r border-zinc-200/60 dark:border-zinc-800/80 font-mono">7. Code / HSN</th>
                                <th class="py-3 px-4 min-w-[190px] border-r border-zinc-200/60 dark:border-zinc-800/80 font-mono text-right">8. 18% GST (Incl.)</th>
                                <th class="py-3 px-3 w-28 text-center">9. Actions</th>
                            </tr>
                        </thead>

                        <tbody id="cora-inv-table-body" class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 font-sans">
                            <!-- Populated via AJAX -->
                            <tr>
                                <td colspan="8" class="py-8 text-center text-zinc-400">Loading catalog items...</td>
                            </tr>
                        </tbody>

                        <!-- Spreadsheet Totals Footer Strip (Visible only in Spreadsheet Mode) -->
                        <tfoot id="cora-inv-spreadsheet-footer" class="hidden border-t-2 border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 text-xs font-mono font-bold text-zinc-900 dark:text-zinc-100">
                            <tr>
                                <td class="py-3 px-3 text-center border-r border-zinc-200/60 dark:border-zinc-800/80" id="cora-ft-qty">
                                    0 units
                                </td>
                                <td class="py-3 px-4 border-r border-zinc-200/60 dark:border-zinc-800/80" id="cora-ft-name">
                                    Total <span id="cora-ft-skus-count">0</span> SKUs Filtered
                                </td>
                                <td class="py-3 px-3 text-center border-r border-zinc-200/60 dark:border-zinc-800/80 text-zinc-400" id="cora-ft-pages">
                                    —
                                </td>
                                <td class="py-3 px-3 text-center border-r border-zinc-200/60 dark:border-zinc-800/80 text-zinc-700 dark:text-zinc-300" id="cora-ft-weight">
                                    0.00 Kg
                                </td>
                                <td class="py-3 px-3 text-right border-r border-zinc-200/60 dark:border-zinc-800/80 text-zinc-400" id="cora-ft-unit-price">
                                    —
                                </td>
                                <td class="py-3 px-3 text-right border-r border-zinc-200/60 dark:border-zinc-800/80 text-zinc-950 dark:text-zinc-50" id="cora-ft-total-base">
                                    ₹0.00
                                </td>
                                <td class="py-3 px-3 border-r border-zinc-200/60 dark:border-zinc-800/80 text-[10px] text-zinc-500" id="cora-ft-code">
                                    Tax Rate: 18% GST
                                </td>
                                <td class="py-3 px-4 text-right border-r border-zinc-200/60 dark:border-zinc-800/80" id="cora-ft-gst-total">
                                    <div>₹0.00 <span class="text-[10px] font-normal text-zinc-500">GST</span></div>
                                    <div class="text-[11px] text-zinc-950 dark:text-zinc-50 font-bold">₹0.00 Total</div>
                                </td>
                                <td class="py-3 px-3 text-center text-[10px] text-zinc-400">
                                    Summary
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- MOBILE VIEW: Minimal Responsive Cards (ZERO Horizontal Scrolling) -->
            <div class="block md:hidden">
                <div id="cora-inv-mobile-cards-wrap" class="space-y-3">
                    <!-- Populated via AJAX with structured vertical cards -->
                    <div class="py-8 text-center text-zinc-400 text-xs">Loading items...</div>
                </div>
            </div>
        </div>

        <!-- SUBTAB 2: Van Consignments Hub -->
        <div id="cora-subtab-consignments" class="space-y-4 hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <div>
                    <h2 class="text-sm font-bold text-zinc-950 dark:text-zinc-50">Active Mobile Van Consignments</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Live dynamic stock on wheels allocated to field sales agents and routes.</p>
                </div>
                <button type="button" onclick="CoraInventory.openConsignmentModal()" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-bold shadow-sm transition-colors cursor-pointer w-full sm:w-auto shrink-0 whitespace-nowrap">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Dispatch Van</span>
                </button>
            </div>

            <div id="cora-consignments-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Populated via AJAX -->
            </div>
        </div>

        <!-- SUBTAB 3: Live Route & GPS Map -->
        <div id="cora-subtab-map" class="space-y-4 hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <div>
                    <h2 class="text-sm font-bold text-zinc-950 dark:text-zinc-50">Live Field Route &amp; Shop Check-in Map</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Real-time GPS telemetry showing active van locations, visited retailers, and spot sale waypoints.</p>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        GPS Telemetry Active
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-2xs bg-zinc-50">
                <div id="cora-inventory-map-view" class="w-full h-[450px] relative z-0"></div>
            </div>

            <!-- Route Stop Timeline -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3">Today's Verified Shop Check-ins</h3>
                <div id="cora-map-stops-timeline" class="space-y-2">
                    <!-- Populated via AJAX -->
                </div>
            </div>
        </div>

        <!-- SUBTAB 4: AI Invoice OCR Inspector -->
        <div id="cora-subtab-ocr" class="space-y-4 hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left: Upload / Snap Photo -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-1">Upload or Snap Paper Invoice / Challan</h2>
                        <p class="text-xs text-zinc-500 mb-4">Gemini 2.5 Flash Multimodal Vision extracts line items, quantities, and totals instantly.</p>
                        
                        <div id="cora-ocr-dropzone" onclick="document.getElementById('cora-ocr-file-input').click()" class="w-full border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-2xl p-6 text-center hover:border-zinc-500 transition-colors cursor-pointer bg-zinc-50/50 dark:bg-zinc-800/40">
                            <input type="file" id="cora-ocr-file-input" accept="image/*,application/pdf" onchange="CoraInventory.handleOCRFile(event)" class="hidden">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                </div>
                                <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Click to Snap Photo or Upload Bill</span>
                                <span class="text-[11px] text-zinc-400">Supports JPG, PNG, PDF receipts (Handwritten or Printed)</span>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div id="cora-ocr-preview-wrap" class="mt-4 hidden">
                            <div class="relative rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700">
                                <img id="cora-ocr-preview-img" src="" alt="Invoice Preview" class="w-full max-h-56 object-cover">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="font-semibold text-zinc-600 block mb-1 text-xs">Van Consignment Assignment</label>
                            <select id="cora-ocr-consignment-select" class="w-full px-3 py-2 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                                <option value="">-- Select Active Consignment --</option>
                            </select>
                        </div>

                        <button type="button" id="cora-ocr-process-btn" onclick="CoraInventory.processOCR()" class="w-full mt-3 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                            <span>Run AI Vision OCR Extraction</span>
                        </button>
                    </div>
                </div>

                <!-- Right: Parsed Results & SKU Deductions -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-zinc-100 dark:border-zinc-800">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">AI Parsed Line Items &amp; Price Match</h3>
                                <p class="text-xs text-zinc-400">Match handwritten items directly to van consignment stock.</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400" id="cora-ocr-confidence-badge">
                                Awaiting Upload
                            </span>
                        </div>

                        <div id="cora-ocr-results-container" class="space-y-3">
                            <div class="py-12 text-center text-zinc-400 text-xs select-none">
                                Upload a bill on the left to extract invoice line items.
                            </div>
                        </div>

                        <div id="cora-ocr-action-bar" class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between hidden">
                            <div class="text-xs">
                                <span class="text-zinc-500">Extracted Total: </span>
                                <span class="font-bold text-zinc-900 dark:text-zinc-100 font-mono text-sm" id="cora-ocr-parsed-total">₹0.00</span>
                            </div>
                            <button type="button" onclick="CoraInventory.confirmOCRSale()" class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-bold shadow-sm transition-colors cursor-pointer">
                                Confirm &amp; Deduct Van Stock
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- SUBTAB 5: 24h Daily Audit & Supply Recon -->
        <div id="cora-subtab-recon" class="space-y-4 hidden">
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">Executive Briefing</span>
                            <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">24-Hour Supply Recon &amp; Audit Engine</h2>
                        </div>
                        <p class="text-xs text-zinc-500 mt-0.5">End-of-day multi-channel reconciliation across all dispatched vans, cash collected, unsold returns &amp; loss prevention analysis.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <input type="date" id="cora-recon-date-picker" value="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>" class="px-3 py-1.5 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none font-mono text-zinc-800 dark:text-zinc-200">
                        
                        <button type="button" onclick="CoraInventory.generateDailyRecon()" class="px-3.5 py-1.5 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 text-xs font-semibold shadow-2xs hover:bg-black dark:hover:bg-white transition-colors cursor-pointer flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                            <span>Run Executive Audit</span>
                        </button>

                        <button type="button" id="cora-recon-pdf-btn" onclick="CoraInventory.exportDailyPDF()" class="px-3 py-1.5 rounded-xl bg-white text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-xs font-semibold shadow-2xs cursor-pointer flex items-center gap-1.5 transition-colors">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <span>Print / PDF Export</span>
                        </button>

                        <button type="button" id="cora-recon-share-btn" onclick="CoraInventory.openReconShareModal()" class="px-3 py-1.5 rounded-xl bg-white text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-xs font-semibold shadow-2xs cursor-pointer flex items-center gap-1.5 transition-colors">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                            <span>Share</span>
                        </button>
                    </div>
                </div>

                <!-- Structured Dynamic Recon Report Container -->
                <div id="cora-recon-report-container" class="mt-5 space-y-6">
                    <!-- Initial Placeholder State before run -->
                    <div class="py-16 text-center border border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl bg-zinc-50/50 dark:bg-zinc-900/30">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 mb-3">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-200">No Audit Run for Selected Date</h3>
                        <p class="text-xs text-zinc-500 mt-1 max-w-md mx-auto">Click "Run Executive Audit" above to aggregate today's 24-hour supply metrics, route cash collections, returns, and AI loss prevention diagnostics.</p>
                        <button type="button" onclick="CoraInventory.generateDailyRecon()" class="mt-4 px-4 py-2 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 text-xs font-semibold shadow-2xs hover:bg-black dark:hover:bg-white transition-colors cursor-pointer inline-flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                            <span>Run Audit Now</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php endif; ?>

    <!-- ========================================================================= -->
    <!-- 2. ULTRA-SIMPLIFIED MOBILE VENDOR VIEW (Non-Technical UI)                 -->
    <!-- ========================================================================= -->
    <div id="cora-inv-vendor-container" class="space-y-4 <?php echo $is_driver_only ? '' : 'hidden'; ?>">
        
        <!-- Active Consignment Status Banner (Dynamic) -->
        <div id="cora-vendor-consignment-banner-wrap">
            <div class="p-6 rounded-3xl bg-zinc-950 text-white shadow-lg relative overflow-hidden border border-zinc-800">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-900 text-zinc-400 border border-zinc-800 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span> Standby Mode
                    </span>
                    <span class="text-[11px] font-mono text-zinc-500">No Active Consignment</span>
                </div>
                <div class="text-2xl font-bold font-mono tracking-tight text-white mb-1">
                    ₹0 <span class="text-xs font-sans font-normal text-zinc-400">Stock on Wheels</span>
                </div>
                <p class="text-xs text-zinc-400 mt-2 leading-relaxed">No active consignment allocated for this shift. Factory dispatch will assign route stock to this terminal.</p>
                <div class="flex items-center justify-between pt-3 mt-4 border-t border-zinc-800/80 text-xs text-zinc-400">
                    <div>Sold Today: <span class="font-bold text-zinc-300 font-mono">₹0</span></div>
                    <div>Cash in Hand: <span class="font-bold text-zinc-300 font-mono">₹0</span></div>
                </div>
            </div>
        </div>

        <!-- 4 Large Touch Action Buttons for Non-Technical Field Agents -->
        <div class="grid grid-cols-2 gap-3">
            
            <!-- Button 1: Quick Spot Sale -->
            <button type="button" onclick="CoraInventory.openSpotSaleSheet()" class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center text-center gap-2 hover:bg-zinc-50 active:scale-98 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Quick Spot Sale</div>
                    <div class="text-[10px] text-zinc-500">Bill Shop &amp; Collect Cash</div>
                </div>
            </button>

            <!-- Button 2: Scan Paper Bill (AI OCR) -->
            <button type="button" onclick="CoraInventory.openOCRSheet()" class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center text-center gap-2 hover:bg-zinc-50 active:scale-98 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Snap Paper Bill</div>
                    <div class="text-[10px] text-zinc-500">AI Reads Handwritten Bill</div>
                </div>
            </button>

            <!-- Button 3: GPS Shop Check-in -->
            <button type="button" onclick="CoraInventory.openShopVisitSheet()" class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center text-center gap-2 hover:bg-zinc-50 active:scale-98 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">GPS Shop Check-in</div>
                    <div class="text-[10px] text-zinc-500">1-Tap Location Stamp</div>
                </div>
            </button>

            <!-- Button 4: Day-End Return Settlement -->
            <button type="button" onclick="CoraInventory.openReconcileSheet()" class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center text-center gap-2 hover:bg-zinc-50 active:scale-98 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Day-End Return</div>
                    <div class="text-[10px] text-zinc-500">Restock Unsold Goods</div>
                </div>
            </button>

        </div>

        <!-- Allotted Van Stock (Cargo on Wheels) -->
        <div id="cora-vendor-van-stock-card" class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Allotted Van Inventory</h3>
                </div>
                <span id="cora-vendor-van-stock-badge" class="px-2 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">0 SKUs</span>
            </div>
            <div id="cora-vendor-van-stock-list" class="space-y-2">
                <!-- Populated via AJAX -->
            </div>
        </div>

        <!-- Today's Live Sales Ledger for Vendor -->
        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3">Today's Spot Invoices</h3>
            <div id="cora-vendor-sales-list" class="space-y-2">
                <!-- Populated via AJAX -->
            </div>
        </div>

    </div>

</div>

<!-- ========================================================================= -->
<!-- MOBILE BOTTOM-UP SLIDE SHEETS (translate-y-full -> translate-y-0)         -->
<!-- ========================================================================= -->

<?php if ( ! $is_driver_only ) : ?>
<!-- 0. HIGH-VOLUME INGESTION: BULK CSV & STARTER KITS STUDIO DRAWER -->
<div id="cora-inv-import-sheet" class="fixed inset-x-0 bottom-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none; top: 48px; height: calc(100vh - 48px);">
    <div id="cora-inv-import-backdrop" onclick="CoraInventory.closeImportModal()" class="absolute inset-0 bg-white dark:bg-zinc-950 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-import-drawer" class="absolute inset-0 w-full h-full bg-white dark:bg-zinc-900 border-t border-zinc-200/80 dark:border-zinc-800 shadow-2xl transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto flex flex-col overflow-hidden z-10" style="top: 0; bottom: 0; height: 100% !important; min-height: 100% !important; max-height: 100% !important;">
        
        <!-- Sheet Header Bar (Full-Width Edge-to-Edge) -->
        <div class="px-6 md:px-12 lg:px-16 py-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm md:text-base font-bold text-zinc-950 dark:text-zinc-50 leading-tight">Add &amp; Ingest Stationery Inventory</h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                            <span>Bulk Catalog Hub</span>
                        </span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">High-speed batch import from Tally, Marg, Excel, or load 1-click curated starter kits.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="CoraInventory.closeImportModal()" class="py-1.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-xs font-semibold flex items-center gap-1.5 cursor-pointer" title="Exit Hub">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <span>Close</span>
                </button>
            </div>
        </div>

        <!-- Scrollable Content Body -->
        <div class="flex-1 overflow-y-auto px-6 md:px-12 lg:px-16 py-6 max-w-5xl mx-auto w-full flex flex-col justify-between">
            <div class="space-y-5">
                <!-- Method Switcher Tab Matrix -->
                <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-1">
                    <button type="button" onclick="CoraInventory.switchImportTab('csv')" id="cora-import-tab-btn-csv" class="px-4 py-2 text-xs font-bold border-b-2 border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 transition-colors flex items-center gap-1.5 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Bulk CSV / Excel Upload</span>
                    </button>
                    <button type="button" onclick="CoraInventory.switchImportTab('kits')" id="cora-import-tab-btn-kits" class="px-4 py-2 text-xs font-semibold border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors flex items-center gap-1.5 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        <span>1-Click Stationery Preset Kits</span>
                    </button>
                </div>

                <!-- TAB A: BULK CSV -->
                <div id="cora-import-pane-csv" class="space-y-4">
                    <div id="cora-csv-dropzone" onclick="document.getElementById('cora-csv-file-input').click()" class="border-2 border-dashed border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 rounded-2xl p-8 text-center transition-colors cursor-pointer bg-zinc-50/50 dark:bg-zinc-800/30">
                        <input type="file" id="cora-csv-file-input" accept=".csv,.tsv,.txt" onchange="CoraInventory.handleCSVFile(event)" class="hidden">
                        <div class="flex flex-col items-center justify-center gap-2.5">
                            <div class="w-12 h-12 rounded-2xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 shadow-2xs">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </div>
                            <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Click to Select or Drag &amp; Drop CSV File</span>
                            <span class="text-xs text-zinc-400">Compatible with Tally ERP, Busy, Marg, SAP, Zoho or standard Excel CSV sheets</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-700 text-xs">
                        <div class="flex items-center gap-2.5 text-zinc-600 dark:text-zinc-400">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            <span>Need a formatted master template?</span>
                        </div>
                        <button type="button" onclick="CoraInventory.downloadSampleCSV()" class="px-3 py-1.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 font-semibold text-zinc-800 dark:text-zinc-200 hover:text-zinc-950 text-xs shadow-2xs cursor-pointer flex items-center gap-1.5 transition-colors">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Download Sample CSV Template</span>
                        </button>
                    </div>

                    <!-- Staging Validation Table Preview -->
                    <div id="cora-csv-staging-wrap" class="hidden space-y-3">
                        <div class="flex items-center justify-between text-xs pb-1 border-b border-zinc-100 dark:border-zinc-800">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <span>Parsed Staging Grid</span>
                                <span id="cora-csv-parsed-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950">0 items</span>
                            </div>
                            <span class="text-[11px] text-zinc-400">Previewing first rows before import</span>
                        </div>

                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-x-auto max-h-64 shadow-2xs">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-500 font-semibold">
                                        <th class="py-2.5 px-3.5">SKU</th>
                                        <th class="py-2.5 px-3.5">Product Title</th>
                                        <th class="py-2.5 px-3.5">Category</th>
                                        <th class="py-2.5 px-3.5 font-mono">Wholesale ₹</th>
                                        <th class="py-2.5 px-3.5 font-mono">Opening Stock</th>
                                    </tr>
                                </thead>
                                <tbody id="cora-csv-staging-body" class="divide-y divide-zinc-100 dark:divide-zinc-800"></tbody>
                            </table>
                        </div>

                        <button type="button" id="cora-csv-commit-btn" onclick="CoraInventory.commitBulkImport()" class="w-full py-3 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm cursor-pointer transition-colors flex items-center justify-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M5 13l4 4L19 7"></path></svg>
                            <span>Confirm &amp; Import Products to Central Plant</span>
                        </button>
                    </div>
                </div>

                <!-- TAB B: STARTER KITS -->
                <div id="cora-import-pane-kits" class="space-y-4 hidden">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Inject pre-built stationery categories with standard HSN codes, GST tax rates, packaging UOMs, and opening stock in 1 click:</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Kit 1: School Essentials -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-700/80 flex flex-col justify-between gap-3.5 shadow-2xs">
                            <div>
                                <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        </span>
                                        <span>School Essentials</span>
                                    </div>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold">6 SKUs</span>
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed">2B/HB Pencils, Dust-free Erasers, Sharpeners, Rulers &amp; Geometry Boxes.</p>
                            </div>
                            <button type="button" onclick="CoraInventory.loadStarterKit('school_essentials')" class="w-full py-2 rounded-xl bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 font-semibold text-xs shadow-2xs cursor-pointer transition-colors">
                                + Inject School Kit
                            </button>
                        </div>

                        <!-- Kit 2: Notebooks & Registers -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-700/80 flex flex-col justify-between gap-3.5 shadow-2xs">
                            <div>
                                <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                        </span>
                                        <span>Registers &amp; Paper Reams</span>
                                    </div>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold">5 SKUs</span>
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed">Hardbound Registers, A5 Spiral Journals, Long Books &amp; A4 Copier Paper.</p>
                            </div>
                            <button type="button" onclick="CoraInventory.loadStarterKit('notebooks_registers')" class="w-full py-2 rounded-xl bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 font-semibold text-xs shadow-2xs cursor-pointer transition-colors">
                                + Inject Registers Kit
                            </button>
                        </div>

                        <!-- Kit 3: Writing Instruments -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-700/80 flex flex-col justify-between gap-3.5 shadow-2xs">
                            <div>
                                <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path><path d="M2 2l7.586 7.586"></path><circle cx="11" cy="11" r="2"></circle></svg>
                                        </span>
                                        <span>Writing &amp; Markers</span>
                                    </div>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold">4 SKUs</span>
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed">Gel Pens (0.7mm), Whiteboard Markers, Rollerball Pens &amp; Highlighters.</p>
                            </div>
                            <button type="button" onclick="CoraInventory.loadStarterKit('writing_instruments')" class="w-full py-2 rounded-xl bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 font-semibold text-xs shadow-2xs cursor-pointer transition-colors">
                                + Inject Writing Kit
                            </button>
                        </div>

                        <!-- Kit 4: Office & Adhesives -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-700/80 flex flex-col justify-between gap-3.5 shadow-2xs">
                            <div>
                                <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                        </span>
                                        <span>Office &amp; Adhesives</span>
                                    </div>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold">4 SKUs</span>
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed">Glue Sticks 15g, Heavy Duty Staplers + Pins, BOPP Tape &amp; Paper Clips.</p>
                            </div>
                            <button type="button" onclick="CoraInventory.loadStarterKit('office_adhesives')" class="w-full py-2 rounded-xl bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 font-semibold text-xs shadow-2xs cursor-pointer transition-colors">
                                + Inject Office Kit
                            </button>
                        </div>

                        <!-- Kit 5: Bahee Store & Kagzi Manufacturer Wholesale Order Kit (Unit-Based) -->
                        <div class="sm:col-span-2 p-4.5 rounded-2xl bg-white dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xs">
                            <div class="space-y-1.5">
                                <div class="font-bold text-xs text-zinc-950 dark:text-white flex items-center gap-2 flex-wrap">
                                    <span class="p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    </span>
                                    <span>Bahee Store &amp; Kagzi Wholesale Order Kit (Part I &amp; II)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-bold">25 Unit-Based SKUs</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 font-semibold">Per-Piece Pricing</span>
                                </div>
                                <p class="text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">Exact wholesale order catalog: Panawali, Perforating Registers (8 Maan), 80/100 GSM Ledgers, Cobra &amp; Index Files, Computer Paper 801, Maplitho 80 GSM &amp; Copier Reams.</p>
                            </div>
                            <button type="button" onclick="CoraInventory.loadStarterKit('bahee_kagzi_order_kit')" class="shrink-0 px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-900 font-semibold text-xs shadow-2xs cursor-pointer transition-colors">
                                + Inject Part I &amp; II Kit
                            </button>
                        </div>

                        <!-- Kit 6: Bahee Store Weight-Based Wholesale Order Kit (Part-III @ ₹401.25/Kg) -->
                        <div class="sm:col-span-2 p-4.5 rounded-2xl bg-white dark:bg-zinc-850 border-2 border-zinc-950 dark:border-zinc-400 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                            <div class="space-y-1.5">
                                <div class="font-bold text-xs text-zinc-950 dark:text-white flex items-center gap-2 flex-wrap">
                                    <span class="p-1.5 rounded-lg bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                    </span>
                                    <span>Bahee Store Weight-Based Order Kit (Part-III)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold">10 Weight SKUs (85.5 Kg)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 font-semibold border border-amber-200/80 dark:border-amber-800/60">Rate @ ₹401.25 / KG</span>
                                </div>
                                <p class="text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">Dynamic weight formula pricing: Gutka Rokad (1100g = ₹441.38), Gutka Khata (900g = ₹361.13), Copy Bahee (550g = ₹220.69), Register Bahee (1150g = ₹461.44), Copy/Register Rule 17×28, Parchi (450g), Kiro 22×29 (750g), Kiro 18×22 (500g), and Printed Talpat (1450g = ₹581.81). 10 units each.</p>
                            </div>
                            <button type="button" onclick="CoraInventory.loadStarterKit('bahee_weight_order_kit')" class="shrink-0 px-4.5 py-2.5 rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 shadow-sm cursor-pointer transition-all active:scale-95">
                                + Inject Weight Kit (₹401.25/Kg)
                            </button>
                        </div>

                        <!-- Kit 6: Complete Master Factory Catalog -->
                        <div class="sm:col-span-2 p-4 rounded-2xl bg-zinc-950 text-white dark:bg-zinc-900 dark:border-zinc-700 border border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-md">
                            <div>
                                <div class="font-bold text-xs text-white flex items-center gap-2">
                                    <span class="p-1 rounded bg-zinc-800 text-zinc-200">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                    </span>
                                    <span>Complete Master Stationery Catalog</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-zinc-800 text-zinc-300">22+ SKUs</span>
                                </div>
                                <p class="text-[11px] text-zinc-400 mt-1">Full comprehensive factory catalog spanning all categories with realistic opening stock.</p>
                            </div>
                            <button type="button" onclick="CoraInventory.loadStarterKit('complete_catalog')" class="shrink-0 px-4 py-2 rounded-xl bg-white text-zinc-950 font-bold text-xs hover:bg-zinc-100 shadow-sm cursor-pointer transition-colors">
                                + Inject Full Catalog
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Bottom Footer -->
            <div class="pt-4 mt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between gap-3 shrink-0">
                <button type="button" onclick="CoraInventory.closeImportModal()" class="py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="button" onclick="CoraInventory.closeImportModal()" class="py-2.5 px-5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors cursor-pointer">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 0B. EXPORT CATALOG SHEET (Excel / CSV + Horizontal PDF Printout) -->
<div id="cora-inv-export-sheet" class="fixed inset-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none;">
    <div id="cora-inv-export-backdrop" onclick="CoraInventory.closeExportModal()" class="absolute inset-0 bg-zinc-950/60 dark:bg-zinc-950/80 backdrop-blur-xs opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-export-drawer" class="absolute bottom-0 inset-x-0 md:inset-auto md:bottom-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 w-full md:max-w-2xl bg-white dark:bg-zinc-900 rounded-t-3xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xl transform translate-y-full md:translate-y-0 md:scale-95 transition-all duration-300 ease-out pointer-events-auto flex flex-col max-h-[90vh] overflow-hidden z-10">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                </span>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Export Catalog &amp; Sheets</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Export filtered stationery inventory or selected SKUs</p>
                </div>
            </div>
            <button type="button" onclick="CoraInventory.closeExportModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" title="Close">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto space-y-5">
            <!-- Scope Selector (All Filtered vs Selected) -->
            <div class="p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-850/60 border border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="space-y-0.5">
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Export Scope</span>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400" id="cora-export-scope-desc">Current filtered inventory view</p>
                </div>
                <div class="inline-flex p-1 rounded-xl bg-zinc-200/70 dark:bg-zinc-800 text-xs font-semibold shrink-0">
                    <button type="button" id="cora-export-scope-btn-filtered" onclick="CoraInventory.setExportScope('filtered')" class="px-3 py-1.5 rounded-lg bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs font-bold transition-all cursor-pointer">
                        Active Filter (<span id="cora-export-filtered-count">0</span>)
                    </button>
                    <button type="button" id="cora-export-scope-btn-selected" onclick="CoraInventory.setExportScope('selected')" class="px-3 py-1.5 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white font-medium transition-all cursor-pointer">
                        Selected (<span id="cora-export-selected-count">0</span>)
                    </button>
                </div>
            </div>

            <!-- Export Cards Grid (2 Options) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Option 1: Excel / CSV Sheet -->
                <div class="p-5 rounded-2xl bg-white dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-750 flex flex-col justify-between gap-4 shadow-2xs hover:border-zinc-400 dark:hover:border-zinc-600 transition-all">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                            </span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold">.CSV</span>
                        </div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Excel / CSV Spreadsheet</h4>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            UTF-8 Hindi-encoded spreadsheet. Includes SKU code, HSN, Quantity, Pages, Gram Weight, Base Unit Rate, Base Batch Price, and 18% GST calculations.
                        </p>
                    </div>
                    <button type="button" onclick="CoraInventory.triggerExportCSV()" class="w-full py-2.5 px-4 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-2xs cursor-pointer flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Download Excel / CSV</span>
                    </button>
                </div>

                <!-- Option 2: Horizontal / Landscape Sheet PDF -->
                <div class="p-5 rounded-2xl bg-white dark:bg-zinc-850 border-2 border-zinc-950 dark:border-zinc-400 flex flex-col justify-between gap-4 shadow-sm">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-9 h-9 rounded-xl bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            </span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold">Landscape PDF</span>
                        </div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Horizontal Sheet (PDF)</h4>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Horizontal print-ready invoice table with solid outer outline, dotted interior cell lines, Hindi item names, Quantity, Pages, Unit Weight, Rate, Code/HSN, and 18% GST Total.
                        </p>
                    </div>
                    <button type="button" onclick="CoraInventory.triggerExportPDF()" class="w-full py-2.5 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-950 font-bold text-xs shadow-sm cursor-pointer flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        <span>Open &amp; Print Landscape PDF</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end shrink-0 bg-zinc-50/50 dark:bg-zinc-900/50">
            <button type="button" onclick="CoraInventory.closeExportModal()" class="py-2 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>

<!-- 1. ADD / EDIT PRODUCT FULL-SCREEN 3-STEP VIEW (Zero Header Collision, Below Main Topbar) -->
<div id="cora-inv-product-sheet" class="fixed inset-x-0 bottom-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none; top: 48px; height: calc(100vh - 48px);">
    <div id="cora-inv-product-backdrop" onclick="CoraInventory.closeProductModal()" class="absolute inset-0 bg-white dark:bg-zinc-950 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-product-drawer" class="absolute inset-0 w-full h-full bg-white dark:bg-zinc-900 border-t border-zinc-200/80 dark:border-zinc-800 shadow-2xl transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto flex flex-col overflow-hidden z-10" style="top: 0; bottom: 0; height: 100% !important; min-height: 100% !important; max-height: 100% !important;">
        
        <!-- Sheet Header Bar (Full-Width Edge-to-Edge) -->
        <div class="px-6 md:px-12 lg:px-16 py-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm md:text-base font-bold text-zinc-950 dark:text-zinc-50 leading-tight" id="cora-inv-prod-title">Add Stationery SKU</h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Factory Master Catalog</span>
                        </span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5" id="cora-inv-prod-step-subtitle">Step 1 of 3: Product Identity &amp; Visual Media</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="CoraInventory.closeProductModal()" class="py-1.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-xs font-semibold flex items-center gap-1.5 cursor-pointer" title="Exit Editor">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <span>Close</span>
                </button>
            </div>
        </div>

        <!-- 3-Step Guided Stepper Bar (Non-Repeating Numbers, No Hover Color Conflict) -->
        <div class="px-6 md:px-12 lg:px-16 py-3 bg-zinc-50/90 dark:bg-zinc-800/40 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between gap-3 overflow-x-auto no-scrollbar shrink-0">
            <button type="button" onclick="CoraInventory.switchProductStep(1)" id="cora-prod-step-btn-1" class="cora-step-tab is-active flex-1 py-2.5 px-4 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                <span class="step-num w-5 h-5 rounded-full bg-white text-zinc-950 dark:bg-zinc-950 dark:text-zinc-100 text-[10px] font-mono flex items-center justify-center font-bold shrink-0">1</span>
                <span class="step-label truncate">Identity &amp; Media</span>
            </button>
            <button type="button" onclick="CoraInventory.switchProductStep(2)" id="cora-prod-step-btn-2" class="cora-step-tab flex-1 py-2.5 px-4 rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                <span class="step-num w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] font-mono flex items-center justify-center font-bold shrink-0">2</span>
                <span class="step-label truncate">Pricing &amp; Margins</span>
            </button>
            <button type="button" onclick="CoraInventory.switchProductStep(3)" id="cora-prod-step-btn-3" class="cora-step-tab flex-1 py-2.5 px-4 rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                <span class="step-num w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] font-mono flex items-center justify-center font-bold shrink-0">3</span>
                <span class="step-label truncate">Stock &amp; Logistics</span>
            </button>
        </div>

        <!-- Form Scrollable Body (Strict 80vh flex container) -->
        <form id="cora-inv-prod-form" onsubmit="CoraInventory.saveProduct(event)" class="flex-1 overflow-y-auto px-6 md:px-12 lg:px-16 py-5 flex flex-col justify-between" style="min-height: 0; flex: 1 1 auto;">
            <input type="hidden" id="cora-prod-id" value="0">
            <input type="hidden" id="cora-prod-image-url" value="">

            <!-- STEP 1 PANE: Identity & Media -->
            <div id="cora-prod-pane-1" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    <!-- Left: Identity Inputs (7 cols) -->
                    <div class="md:col-span-7 space-y-3.5">
                        <div>
                            <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Product Title / Name *</label>
                            <input type="text" id="cora-prod-name" required placeholder="e.g. Deluxe Hardbound Ruled Register 240 Pgs" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm focus:border-zinc-500 transition-colors">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="font-semibold text-zinc-700 dark:text-zinc-300 text-xs">SKU Code *</label>
                                    <button type="button" onclick="CoraInventory.autoGenerateSKU()" class="text-[10px] font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 px-2 py-0.5 rounded-md cursor-pointer transition-colors">✨ Auto Generate</button>
                                </div>
                                <input type="text" id="cora-prod-sku" required placeholder="e.g. STN-NB-103" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono focus:border-zinc-500 transition-colors uppercase">
                            </div>
                            <div>
                                <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Barcode / EAN-13</label>
                                <input type="text" id="cora-prod-barcode" placeholder="890123450..." class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono focus:border-zinc-500 transition-colors">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Stationery Category *</label>
                                <select id="cora-prod-cat" onchange="CoraInventory.onCategoryChange()" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm cursor-pointer">
                                    <?php if ( ! empty( $db_categories ) ) : ?>
                                        <?php foreach ( $db_categories as $c ) : ?>
                                            <option value="<?php echo esc_attr( $c['category'] ); ?>"><?php echo esc_html( cora_format_category_name( $c['category'] ) ); ?></option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="notebooks">Notebooks &amp; Registers</option>
                                        <option value="school_supplies">School Essentials (Pencils/Erasers)</option>
                                        <option value="writing_instruments">Writing Instruments &amp; Pens</option>
                                        <option value="paper_reams">Copier &amp; Printing Paper</option>
                                        <option value="office_supplies">Office Supplies &amp; Binding</option>
                                    <?php endif; ?>
                                    <option value="__custom__">+ Custom Category...</option>
                                </select>
                                <div id="cora-prod-custom-cat-wrap" class="hidden mt-2">
                                    <input type="text" id="cora-prod-custom-cat" placeholder="Enter custom category name..." class="w-full px-3.5 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs focus:border-zinc-500 transition-colors">
                                </div>
                            </div>
                            <div>
                                <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Pricing Format *</label>
                                <select id="cora-prod-pricing-type" onchange="CoraInventory.onPricingTypeChange()" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm cursor-pointer">
                                    <option value="unit_based">📦 Unit-Based (Per Piece / Pack / Box)</option>
                                    <option value="weight_based">⚖️ Weight-Based (Calculated by Grams / ₹401.25/Kg)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div>
                                <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Packaging Unit (UOM)</label>
                                <input type="text" id="cora-prod-uom" placeholder="e.g. Pcs / Box of 10" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm focus:border-zinc-500 transition-colors">
                            </div>
                            <div>
                                <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Unit Weight (Grams)</label>
                                <input type="number" step="1" id="cora-prod-weight" oninput="CoraInventory.onWeightGramsInput()" placeholder="e.g. 1100 (1.10 Kg)" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono focus:border-zinc-500 transition-colors">
                            </div>
                            <div>
                                <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Pages / Sheets</label>
                                <input type="number" step="1" id="cora-prod-pages" placeholder="e.g. 100, 200, 400" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono focus:border-zinc-500 transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Right: Product Image & Media (5 cols) -->
                    <div class="md:col-span-5 p-4 rounded-2xl bg-zinc-50/90 dark:bg-zinc-800/50 border border-zinc-200/80 dark:border-zinc-700 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="font-semibold text-zinc-800 dark:text-zinc-200 text-xs flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <span>Product Photo &amp; Media</span>
                                </label>
                                <span class="text-[10px] font-mono text-zinc-400 bg-white dark:bg-zinc-900 px-2 py-0.5 rounded-md border border-zinc-200/60 dark:border-zinc-800">PNG / JPG Max 2MB</span>
                            </div>

                            <!-- Live Preview & Dropzone -->
                            <div class="flex items-center gap-3.5">
                                <div id="cora-prod-img-preview-box" class="w-24 h-24 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center overflow-hidden shrink-0 relative group shadow-2xs">
                                    <img id="cora-prod-img-preview" src="" class="w-full h-full object-cover hidden" alt="SKU Preview">
                                    <div id="cora-prod-img-placeholder" class="text-zinc-300 dark:text-zinc-600 flex flex-col items-center">
                                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        <span class="text-[9px] mt-1 font-mono text-zinc-400">No Image</span>
                                    </div>
                                    <button type="button" id="cora-prod-img-remove-btn" onclick="CoraInventory.removeProductImage()" class="hidden absolute top-1.5 right-1.5 p-1.5 rounded-full bg-zinc-900/80 text-white hover:bg-zinc-950 transition-colors cursor-pointer" title="Remove image">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </button>
                                </div>

                                <div class="flex-1 space-y-2">
                                    <input type="file" id="cora-prod-img-file-input" accept="image/*" onchange="CoraInventory.handleProductImageUpload(event)" class="hidden">
                                    <button type="button" onclick="$('#cora-prod-img-file-input').click()" class="w-full py-2 px-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-700/80 shadow-2xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                        <span id="cora-prod-img-upload-btn-text">Upload Photo</span>
                                    </button>
                                    <input type="url" id="cora-prod-img-url-input" oninput="CoraInventory.onImageURLInput(this.value)" placeholder="Or paste image URL..." class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-[11px] outline-none font-mono">
                                </div>
                            </div>
                        </div>
                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-3 leading-relaxed">
                            Visual thumbnails render on field billing invoices, catalog printouts, and factory inventory logs.
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2 PANE: Pricing & Taxation (GST) -->
            <div id="cora-prod-pane-2" class="space-y-5 hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Wholesale / Dealer Rate (₹) *</label>
                        <input type="number" step="0.01" id="cora-prod-ws" oninput="CoraInventory.recalcProductPricingMargins()" required placeholder="e.g. 320.00" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono font-bold focus:border-zinc-500 transition-colors">
                        <span class="text-[10px] text-zinc-400 mt-0.5 block">Price charged to retailers / distributors</span>
                    </div>

                    <div>
                        <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Maximum Retail Price (MRP ₹) *</label>
                        <input type="number" step="0.01" id="cora-prod-mrp" oninput="CoraInventory.recalcProductPricingMargins()" required placeholder="e.g. 450.00" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono font-bold focus:border-zinc-500 transition-colors">
                        <span class="text-[10px] text-zinc-400 mt-0.5 block">Printed retail pack price on stationery</span>
                    </div>

                    <div>
                        <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Factory Production / Cost Price (₹)</label>
                        <input type="number" step="0.01" id="cora-prod-cost" oninput="CoraInventory.recalcProductPricingMargins()" placeholder="e.g. 210.00" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono focus:border-zinc-500 transition-colors">
                        <span class="text-[10px] text-zinc-400 mt-0.5 block">Raw material + manufacturing unit cost</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">HSN Classification Code</label>
                        <input type="text" id="cora-prod-hsn" value="4820" placeholder="e.g. 4820 (Notebooks), 9608 (Pens)" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono focus:border-zinc-500 transition-colors">
                    </div>

                    <div>
                        <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">GST Tax Slab Rate (%)</label>
                        <select id="cora-prod-gst" onchange="CoraInventory.recalcProductPricingMargins()" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm cursor-pointer">
                            <option value="12">12% GST (Notebooks, Registers, Scales, Pencils)</option>
                            <option value="18">18% GST (Ball Pens, Markers, Glues, Art Kits)</option>
                            <option value="5">5% GST (Standard Printing Items)</option>
                            <option value="0">0% GST (Exempt Supplies)</option>
                            <option value="28">28% GST (Luxury Specialty Goods)</option>
                        </select>
                    </div>
                </div>

                <!-- Live Tax & Margin Molecule Preview Card (Elevated Professional Telemetry) -->
                <div class="p-4 md:p-5 rounded-2xl bg-zinc-50/90 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-700/80 shadow-2xs">
                    <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-200 mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Live Mathematical Margin &amp; Tax Telemetry</span>
                        </div>
                        <span class="font-mono text-[10px] text-zinc-400 bg-white dark:bg-zinc-800 px-2 py-0.5 rounded-md border border-zinc-200/60 dark:border-zinc-700">Real-Time Yield</span>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 text-xs">
                        <!-- Factory Margin Card -->
                        <div class="p-3.5 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-800/60 flex flex-col justify-between transition-all">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] uppercase tracking-wider text-emerald-800 dark:text-emerald-300 font-bold font-mono">Factory Margin</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            </div>
                            <div class="mt-1">
                                <span id="cora-calc-factory-margin" class="font-mono font-extrabold text-sm md:text-base text-emerald-900 dark:text-emerald-200 block">+₹0.00</span>
                                <span id="cora-calc-factory-pct" class="inline-block text-[10px] font-mono font-semibold text-emerald-700 dark:text-emerald-300 mt-0.5">0.0% markup on cost</span>
                            </div>
                        </div>

                        <!-- Retailer Spread Card -->
                        <div class="p-3.5 rounded-xl bg-indigo-50/80 dark:bg-indigo-950/30 border border-indigo-200/80 dark:border-indigo-800/60 flex flex-col justify-between transition-all">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] uppercase tracking-wider text-indigo-800 dark:text-indigo-300 font-bold font-mono">Retailer Spread</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            </div>
                            <div class="mt-1">
                                <span id="cora-calc-retailer-spread" class="font-mono font-extrabold text-sm md:text-base text-indigo-900 dark:text-indigo-200 block">+₹0.00</span>
                                <span id="cora-calc-retailer-pct" class="inline-block text-[10px] font-mono font-semibold text-indigo-700 dark:text-indigo-300 mt-0.5">0.0% dealer margin</span>
                            </div>
                        </div>

                        <!-- GST Component Card -->
                        <div class="p-3.5 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/60 flex flex-col justify-between transition-all">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] uppercase tracking-wider text-amber-800 dark:text-amber-300 font-bold font-mono">GST Component</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            </div>
                            <div class="mt-1">
                                <span id="cora-calc-gst-amt" class="font-mono font-extrabold text-sm md:text-base text-amber-900 dark:text-amber-200 block">₹0.00 / unit</span>
                                <span id="cora-calc-gst-rate-label" class="inline-block text-[10px] font-mono font-semibold text-amber-700 dark:text-amber-300 mt-0.5">12% GST slab</span>
                            </div>
                        </div>

                        <!-- Base Excl. Tax Card -->
                        <div class="p-3.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700/80 flex flex-col justify-between transition-all shadow-2xs">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-bold font-mono">Net Base Price</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                            </div>
                            <div class="mt-1">
                                <span id="cora-calc-base-excl" class="font-mono font-extrabold text-sm md:text-base text-zinc-900 dark:text-zinc-100 block">₹0.00</span>
                                <span class="inline-block text-[10px] font-mono font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">Wholesale excl. tax</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3 PANE: Factory Stock & Logistics -->
            <div id="cora-prod-pane-3" class="space-y-5 hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Opening Physical Stock (Factory Units) *</label>
                        <input type="number" id="cora-prod-stock" value="500" required class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono font-bold focus:border-zinc-500 transition-colors">
                        <span class="text-[10px] text-zinc-400 mt-0.5 block">Physical units currently present in warehouse</span>
                    </div>

                    <div>
                        <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Low Stock Alert Threshold *</label>
                        <input type="number" id="cora-prod-threshold" value="50" required class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono focus:border-zinc-500 transition-colors">
                        <span class="text-[10px] text-zinc-400 mt-0.5 block">Triggers restock alert when stock falls below</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Batch / Manufacturing Lot ID</label>
                        <input type="text" id="cora-prod-batch" placeholder="e.g. BAT-2026-N03" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm font-mono focus:border-zinc-500 transition-colors">
                    </div>

                    <div>
                        <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Warehouse Bin / Shelf Location</label>
                        <input type="text" id="cora-prod-bin" placeholder="e.g. Rack B4 - Shelf 2" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm focus:border-zinc-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label class="font-semibold text-zinc-700 dark:text-zinc-300 block mb-1 text-xs">Production Notes &amp; Specifications</label>
                    <textarea id="cora-prod-desc" rows="3" placeholder="e.g. 70 GSM high bulk sunshine maplitho paper with section sewn binding..." class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none text-xs md:text-sm focus:border-zinc-500 transition-colors"></textarea>
                </div>
            </div>

            <!-- Sticky Stepper Navigation Footer -->
            <div class="pt-4 mt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="CoraInventory.closeProductModal()" class="py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" id="cora-prod-prev-btn" onclick="CoraInventory.prevProductStep()" class="py-2.5 px-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs hover:bg-zinc-50 shadow-2xs transition-colors cursor-pointer hidden">
                        ← Back
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" id="cora-prod-next-btn" onclick="CoraInventory.nextProductStep()" class="py-2.5 px-5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors cursor-pointer flex items-center gap-1.5">
                        <span>Next Step</span>
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                    <button type="submit" id="cora-prod-save-btn" class="py-2.5 px-5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors cursor-pointer hidden flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M5 13l4 4L19 7"></path></svg>
                        <span>Save Stationery Product</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 1.1 DELETE SKU CONFIRMATION BOTTOM SHEET -->
<div id="cora-inv-delete-sheet" class="fixed inset-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none;">
    <div id="cora-inv-delete-backdrop" onclick="CoraInventory.closeDeleteModal()" class="absolute inset-0 bg-black/40 dark:bg-black/60 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-delete-drawer" class="absolute bottom-0 inset-x-0 w-full max-w-lg mx-auto bg-white dark:bg-zinc-900 border-t border-x border-zinc-200/90 dark:border-zinc-800 rounded-t-[28px] shadow-2xl p-6 transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto">
        <div class="w-10 h-1 bg-zinc-300 dark:bg-zinc-700 rounded-full mx-auto mb-4"></div>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-900 dark:text-zinc-100 shrink-0 border border-zinc-200 dark:border-zinc-700">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </div>
            <div>
                <h3 class="text-sm md:text-base font-bold text-zinc-950 dark:text-zinc-50">Delete Product from Catalog?</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">This will archive the SKU from active plant stock.</p>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700 text-xs mb-5">
            <div class="font-bold text-zinc-900 dark:text-zinc-100" id="cora-del-prod-name">Product Name</div>
            <div class="text-[11px] font-mono text-zinc-400 mt-0.5">SKU: <span id="cora-del-prod-sku">STN-000</span></div>
        </div>

        <input type="hidden" id="cora-del-prod-id" value="0">

        <div class="flex items-center justify-end gap-2.5">
            <button type="button" onclick="CoraInventory.closeDeleteModal()" class="py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                Cancel / Keep SKU
            </button>
            <button type="button" id="cora-del-confirm-btn" onclick="CoraInventory.confirmDeleteProduct()" class="py-2.5 px-4 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors cursor-pointer flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                <span>Confirm Delete SKU</span>
            </button>
        </div>
    </div>
</div>

<!-- 1.2 BULK DELETE CONFIRMATION BOTTOM SHEET -->
<div id="cora-inv-bulk-delete-sheet" class="fixed inset-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none;">
    <div id="cora-inv-bulk-delete-backdrop" onclick="CoraInventory.closeBulkDeleteModal()" class="absolute inset-0 bg-black/40 dark:bg-black/60 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-bulk-delete-drawer" class="absolute bottom-0 inset-x-0 w-full max-w-lg mx-auto bg-white dark:bg-zinc-900 border-t border-x border-zinc-200/90 dark:border-zinc-800 rounded-t-[28px] shadow-2xl p-6 transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto">
        <div class="w-10 h-1 bg-zinc-300 dark:bg-zinc-700 rounded-full mx-auto mb-4"></div>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 border border-red-200/80 dark:border-red-800/60">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </div>
            <div>
                <h3 class="text-sm md:text-base font-bold text-zinc-950 dark:text-zinc-50">Bulk Delete Selected Products?</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">This will remove <span id="cora-bulk-del-count-text" class="font-bold font-mono text-zinc-900 dark:text-zinc-100">0 items</span> from your central catalog.</p>
            </div>
        </div>

        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700 text-xs mb-5 max-h-48 overflow-y-auto space-y-1.5" id="cora-bulk-del-preview-list">
            <!-- Selected items preview injected here -->
        </div>

        <div class="flex items-center justify-end gap-2.5">
            <button type="button" onclick="CoraInventory.closeBulkDeleteModal()" class="py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                Cancel / Keep Items
            </button>
            <button type="button" id="cora-bulk-del-confirm-btn" onclick="CoraInventory.confirmBulkDeleteProducts()" class="py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                <span>Confirm Bulk Delete</span>
            </button>
        </div>
    </div>
</div>

<!-- 2. DISPATCH CONSIGNMENT STUDIO DRAWER -->
<div id="cora-inv-consignment-sheet" class="fixed inset-x-0 bottom-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none; top: 48px; height: calc(100vh - 48px);">
    <div id="cora-inv-consignment-backdrop" onclick="CoraInventory.closeConsignmentModal()" class="absolute inset-0 bg-white dark:bg-zinc-950 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-consignment-drawer" class="absolute inset-0 w-full h-full bg-white dark:bg-zinc-900 border-t border-zinc-200/80 dark:border-zinc-800 shadow-2xl transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto flex flex-col overflow-hidden z-10" style="top: 0; bottom: 0; height: 100% !important; min-height: 100% !important; max-height: 100% !important;">
        
        <!-- Sheet Header Bar (Full-Width Edge-to-Edge) -->
        <div class="px-4 sm:px-6 md:px-12 py-3 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-8 h-8 rounded-xl bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-sm font-bold text-zinc-950 dark:text-zinc-50 leading-tight">Dispatch Van Consignment</h3>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Field Distribution</span>
                        </span>
                    </div>
                    <p class="text-[10.5px] text-zinc-500 dark:text-zinc-400 truncate mt-0.5">Assign field sales driver, territory route, and allocate stock.</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" onclick="CoraInventory.closeConsignmentModal()" class="py-1.5 px-2.5 sm:px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-xs font-semibold flex items-center gap-1 cursor-pointer" title="Exit Dispatch">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <span>Close</span>
                </button>
            </div>
        </div>

        <!-- Scrollable Content Form Body -->
        <form id="cora-inv-csn-form" onsubmit="CoraInventory.dispatchConsignment(event)" class="flex-1 overflow-hidden max-w-5xl mx-auto w-full flex flex-col justify-between" style="min-height: 0; flex: 1 1 auto;">
            
            <div class="flex-1 overflow-y-auto px-4 sm:px-6 md:px-12 py-4 space-y-4 -webkit-overflow-scrolling-touch pb-24">
                
                <!-- CARD 1: Driver & Vehicle Assignment -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-700/80 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-zinc-200/60 dark:border-zinc-700/60">
                        <div>
                            <span class="text-xs font-bold text-zinc-950 dark:text-zinc-50 block">1. Field Sales Driver &amp; Vehicle</span>
                            <span class="text-[10.5px] text-zinc-500 dark:text-zinc-400">Select team member or invite new driver</span>
                        </div>
                        
                        <!-- Driver Mode Switcher -->
                        <div class="inline-flex items-center p-0.5 rounded-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 self-start sm:self-auto shadow-2xs">
                            <button type="button" id="cora-csn-btn-mode-existing" onclick="CoraInventory.switchDriverMode('existing')" class="px-2.5 py-1 text-[10.5px] font-bold rounded-md bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 transition-all cursor-pointer">
                                Existing Member
                            </button>
                            <button type="button" id="cora-csn-btn-mode-new" onclick="CoraInventory.switchDriverMode('new')" class="px-2.5 py-1 text-[10.5px] font-semibold text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 rounded-md transition-all cursor-pointer">
                                + Invite New
                            </button>
                        </div>
                    </div>

                    <input type="hidden" id="cora-csn-driver-mode" value="existing">

                    <!-- Existing Driver Selection -->
                    <div id="cora-csn-pane-existing" class="space-y-2.5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Driver / Sales Agent *</label>
                                <select id="cora-csn-user-select" onchange="CoraInventory.onDriverSelectChange()" class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none cursor-pointer focus:border-zinc-400 shadow-2xs">
                                    <?php if ( ! empty( $workspace_members ) ) : ?>
                                        <?php foreach ( $workspace_members as $m ) : ?>
                                            <option value="<?php echo esc_attr( $m['id'] ); ?>" data-name="<?php echo esc_attr( $m['display_name'] ); ?>" data-email="<?php echo esc_attr( $m['email'] ); ?>" data-role="<?php echo esc_attr( $m['role_label'] ); ?>">
                                                <?php echo esc_html( $m['display_name'] . ' (' . $m['role_label'] . ')' ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="1" data-name="Rohan Verma" data-email="driver.van02@cora.local" data-role="Field Sales / Mobile Vendor">
                                            Rohan Verma (Field Sales / Mobile Vendor)
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Vehicle Registration No *</label>
                                <input type="text" id="cora-csn-vehicle" required value="DL-1V-5501" placeholder="e.g. DL-1V-5501" class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 shadow-2xs">
                            </div>
                        </div>

                        <!-- Selected User Badge Preview -->
                        <div id="cora-csn-selected-preview" class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200/70 dark:border-zinc-700 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-[9.5px] text-zinc-800 dark:text-zinc-200 shrink-0" id="cora-csn-preview-avatar">RV</div>
                                <div class="truncate">
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100" id="cora-csn-preview-name">Rohan Verma</span>
                                    <span class="text-[10px] text-zinc-400 ml-1.5 truncate" id="cora-csn-preview-email">driver.van02@cora.local</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9.5px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 shrink-0" id="cora-csn-preview-role">Field Sales</span>
                        </div>
                    </div>

                    <!-- Invite New Driver -->
                    <div id="cora-csn-pane-new" class="space-y-2.5 hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Driver Full Name *</label>
                                <input type="text" id="cora-csn-new-name" placeholder="e.g. Aarav Mehta" class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Driver Email Address *</label>
                                <input type="email" id="cora-csn-new-email" placeholder="aarav.driver@domain.com" class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Mobile Phone No *</label>
                                <input type="tel" id="cora-csn-new-phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 shadow-2xs">
                            </div>
                        </div>
                        <div class="p-2.5 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-800/40 text-[10.5px] text-emerald-800 dark:text-emerald-300 flex items-start gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            <span>Driver will receive an invitation to access the Field Van Sales POS terminal for this route.</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Territory Route & Coverage Cities -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-700/80 space-y-3">
                    <div>
                        <span class="text-xs font-bold text-zinc-950 dark:text-zinc-50 block">2. Assigned Route &amp; Target Territory</span>
                        <span class="text-[10.5px] text-zinc-500 dark:text-zinc-400">Specify territory route and coverage zones</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Route / Territory Name *</label>
                            <input type="text" id="cora-csn-route" value="Central Stationery Market &amp; University Belts" required placeholder="e.g. North Zone Retail Hub" class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Google Maps Route URL (Optional)</label>
                            <div class="relative">
                                <input type="url" id="cora-csn-gmaps-url" placeholder="https://maps.app.goo.gl/..." class="w-full pl-8 pr-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 shadow-2xs">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">Target Cities &amp; Coverage Zones *</label>
                            <span class="text-[9.5px] text-zinc-400">Tap chips to add</span>
                        </div>
                        <input type="text" id="cora-csn-target-cities" value="Delhi NCR, Noida, Greater Noida, Ghaziabad" required placeholder="e.g. Delhi NCR, Noida, Gurgaon" class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 shadow-2xs mb-2">
                        
                        <!-- 1-Tap Quick City Preset Chips -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[9.5px] font-semibold text-zinc-400 mr-0.5">Quick Add:</span>
                            <button type="button" onclick="CoraInventory.appendCityTag('Delhi NCR')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-medium border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer shadow-3xs">+ Delhi NCR</button>
                            <button type="button" onclick="CoraInventory.appendCityTag('Noida')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-medium border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer shadow-3xs">+ Noida</button>
                            <button type="button" onclick="CoraInventory.appendCityTag('Gurgaon')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-medium border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer shadow-3xs">+ Gurgaon</button>
                            <button type="button" onclick="CoraInventory.appendCityTag('Ghaziabad')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-medium border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer shadow-3xs">+ Ghaziabad</button>
                            <button type="button" onclick="CoraInventory.appendCityTag('Mumbai')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-medium border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer shadow-3xs">+ Mumbai</button>
                            <button type="button" onclick="CoraInventory.appendCityTag('Bangalore')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-medium border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer shadow-3xs">+ Bangalore</button>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Product Allocation Suite (Top 5 Suggestions + Multi-Select + Fast Search) -->
                <div class="rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                    
                    <!-- Allocation Header & Metrics Summary -->
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-xs text-zinc-900 dark:text-zinc-100">3. Allocated Van Stock</span>
                            <span id="cora-csn-item-count-badge" class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-zinc-200/80 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">5 SKUs</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span id="cora-csn-calc-units" class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 hidden sm:inline">5 units</span>
                            <div id="cora-csn-calc-val" class="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400 px-2.5 py-1 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-3xs">
                                Total: ₹0.00
                            </div>
                        </div>
                    </div>

                    <!-- Smart Suggestions & Multi-Product Toolbar -->
                    <div class="p-2.5 bg-zinc-50/50 dark:bg-zinc-900/60 border-b border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" onclick="CoraInventory.suggestTop5Products()" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-900 text-[11px] font-bold shadow-3xs transition-all cursor-pointer">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                <span>✦ Suggest Top 5 Fast-Selling</span>
                            </button>
                            <button type="button" onclick="CoraInventory.openMultiProductPicker()" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 text-[11px] font-semibold transition-all cursor-pointer shadow-3xs">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Browse &amp; Multi-Select Products</span>
                            </button>
                        </div>
                        <button type="button" onclick="CoraInventory.clearAllAllocations()" class="text-[10px] text-zinc-400 hover:text-rose-600 transition-colors cursor-pointer px-1 py-0.5">
                            Clear All
                        </button>
                    </div>

                    <!-- Instant Search Input -->
                    <div class="p-3 border-b border-zinc-200/60 dark:border-zinc-700/60 bg-white dark:bg-zinc-900 relative">
                        <div class="relative">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <input type="text" id="cora-csn-sku-search" oninput="CoraInventory.searchSKUsToAllocate(this.value)" placeholder="Search catalog SKUs by name, barcode, or SKU..." class="w-full pl-8 pr-3 py-2 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 transition-colors">
                        </div>

                        <!-- Live Search Results Dropdown -->
                        <div id="cora-csn-search-results" class="absolute left-3 right-3 top-full mt-1.5 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-xl z-30 max-h-60 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800 hidden">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Allocated Items List -->
                    <div id="cora-csn-alloc-list" class="p-3 space-y-2 max-h-96 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <!-- Populated dynamically with top 5 pre-selected items + searched items -->
                    </div>
                </div>

            </div>

            <!-- Sticky Bottom Navigation Footer (Elevated Above Screen Bottom / Floating Nav) -->
            <div class="sticky bottom-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md px-4 sm:px-6 md:px-12 py-3 sm:py-3.5 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between gap-3 shrink-0 z-30 shadow-[0_-4px_20px_rgba(0,0,0,0.06)]" style="padding-bottom: max(14px, env(safe-area-inset-bottom, 14px));">
                <button type="button" onclick="CoraInventory.closeConsignmentModal()" class="py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="py-2.5 px-5 sm:px-6 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors cursor-pointer flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                    <span>Confirm Van Dispatch &amp; Lock Stock</span>
                </button>
            </div>
        </form>

        <!-- MULTI-PRODUCT CATALOG PICKER MODAL (Layered over the drawer) -->
        <div id="cora-csn-multi-modal" class="fixed inset-0 z-[100000] flex items-center justify-center p-3 sm:p-6 bg-zinc-950/60 backdrop-blur-sm hidden" style="display:none;">
            <div class="relative w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col max-h-[85vh] overflow-hidden">
                
                <!-- Multi-Picker Header -->
                <div class="px-5 py-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-white dark:bg-zinc-900 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-800 dark:text-zinc-200">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Select Multiple Products for Van</h4>
                            <p class="text-[10.5px] text-zinc-400">Check products to add in one go and set dispatch quantities</p>
                        </div>
                    </div>
                    <button type="button" onclick="CoraInventory.closeMultiProductPicker()" class="w-7 h-7 rounded-lg flex items-center justify-center text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                <!-- Multi-Picker Filter Toolbar -->
                <div class="px-5 py-2.5 bg-zinc-50 dark:bg-zinc-900/60 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between gap-3 shrink-0">
                    <div class="relative flex-1">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="cora-multi-search-input" oninput="CoraInventory.filterMultiPicker(this.value)" placeholder="Filter catalog..." class="w-full pl-7 pr-3 py-1.5 text-xs rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400">
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="CoraInventory.toggleMultiPickerSelectAll(true)" class="px-2 py-1 rounded-md text-[10.5px] font-semibold bg-zinc-200/80 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 transition-colors cursor-pointer">
                            Select All In-Stock
                        </button>
                        <button type="button" onclick="CoraInventory.toggleMultiPickerSelectAll(false)" class="px-2 py-1 rounded-md text-[10.5px] font-semibold text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors cursor-pointer">
                            Deselect All
                        </button>
                    </div>
                </div>

                <!-- Multi-Picker Catalog List -->
                <div id="cora-multi-picker-list" class="flex-1 overflow-y-auto px-5 py-3 divide-y divide-zinc-100 dark:divide-zinc-800/60 space-y-1 max-h-[50vh]">
                    <!-- Populated dynamically via CoraInventory.renderMultiPickerList() -->
                </div>

                <!-- Multi-Picker Footer -->
                <div class="px-5 py-3 bg-zinc-50/80 dark:bg-zinc-900 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0">
                    <span id="cora-multi-selected-badge" class="text-xs font-mono font-semibold text-zinc-600 dark:text-zinc-400">
                        0 products selected
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="CoraInventory.closeMultiProductPicker()" class="py-1.5 px-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="button" onclick="CoraInventory.applyMultiProductSelection()" class="py-1.5 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors cursor-pointer">
                            Add Selected to Van
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>

<!-- 3. QUICK SPOT SALE STUDIO DRAWER -->
<div id="cora-inv-spot-sale-sheet" class="fixed inset-x-0 bottom-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none; top: 48px; height: calc(100vh - 48px);">
    <div id="cora-inv-spot-backdrop" onclick="CoraInventory.closeSpotSaleSheet()" class="absolute inset-0 bg-white dark:bg-zinc-950 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-spot-drawer" class="absolute inset-0 w-full h-full bg-white dark:bg-zinc-900 border-t border-zinc-200/80 dark:border-zinc-800 shadow-2xl transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto flex flex-col overflow-hidden z-10" style="top: 0; bottom: 0; height: 100% !important; min-height: 100% !important; max-height: 100% !important;">
        
        <!-- Sheet Header Bar (Full-Width Edge-to-Edge) -->
        <div class="px-6 md:px-12 lg:px-16 py-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm md:text-base font-bold text-zinc-950 dark:text-zinc-50 leading-tight">Quick Spot Sale &amp; Instant Billing</h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>POS Cash Collection</span>
                        </span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Sell directly to retail bookstores or stationary shops from active van inventory.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="CoraInventory.closeSpotSaleSheet()" class="py-1.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-xs font-semibold flex items-center gap-1.5 cursor-pointer" title="Exit Spot Sale">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <span>Close</span>
                </button>
            </div>
        </div>

        <!-- Scrollable Content Form Body -->
        <form id="cora-inv-spot-form" onsubmit="CoraInventory.submitSpotSale(event)" class="flex-1 overflow-y-auto px-6 md:px-12 lg:px-16 py-6 max-w-5xl mx-auto w-full flex flex-col justify-between" style="min-height: 0; flex: 1 1 auto;">
            <div class="space-y-6">
                <!-- Customer Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Retailer / Bookstore Name *</label>
                        <input type="text" id="cora-spot-customer" required placeholder="e.g. Standard Book Depot" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Retailer Phone Number</label>
                        <input type="text" id="cora-spot-phone" placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400">
                    </div>
                </div>

                <!-- Items Picker from Van Stock -->
                <div class="rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-xs text-zinc-900 dark:text-zinc-100">Select Items Sold from Van Stock</span>
                            <span class="text-[11px] text-zinc-500 dark:text-zinc-400">Specify quantities billed</span>
                        </div>
                        <div id="cora-spot-total-display" class="font-mono text-xs font-bold text-zinc-950 dark:text-zinc-50 px-3 py-1 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-2xs">Total: ₹0.00</div>
                    </div>
                    <div id="cora-spot-items-list" class="p-3 space-y-2 max-h-72 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- Payment Mode Selection -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Payment Collection Method</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-2 cursor-pointer bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 transition-colors">
                            <input type="radio" name="cora_spot_pay_mode" value="cash" checked class="text-zinc-900">
                            <span class="font-bold text-xs text-zinc-800 dark:text-zinc-200">Cash</span>
                        </label>
                        <label class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-2 cursor-pointer bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 transition-colors">
                            <input type="radio" name="cora_spot_pay_mode" value="upi" class="text-zinc-900">
                            <span class="font-bold text-xs text-zinc-800 dark:text-zinc-200">UPI / QR</span>
                        </label>
                        <label class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-2 cursor-pointer bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 transition-colors">
                            <input type="radio" name="cora_spot_pay_mode" value="credit" class="text-zinc-900">
                            <span class="font-bold text-xs text-zinc-800 dark:text-zinc-200">Credit Bill</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sticky Bottom Navigation Footer -->
            <div class="pt-4 mt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between gap-3 shrink-0">
                <button type="button" onclick="CoraInventory.closeSpotSaleSheet()" class="py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors cursor-pointer flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    <span>Confirm Sale &amp; Print Receipt</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 4. EDIT SPOT INVOICE STUDIO DRAWER -->
<div id="cora-inv-edit-sale-sheet" class="fixed inset-x-0 bottom-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none; top: 48px; height: calc(100vh - 48px);">
    <div id="cora-inv-edit-sale-backdrop" onclick="CoraInventory.closeEditSaleModal()" class="absolute inset-0 bg-white dark:bg-zinc-950 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-edit-sale-drawer" class="absolute inset-0 w-full h-full bg-white dark:bg-zinc-900 border-t border-zinc-200/80 dark:border-zinc-800 shadow-2xl transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto flex flex-col overflow-hidden z-10" style="top: 0; bottom: 0; height: 100% !important; min-height: 100% !important; max-height: 100% !important;">
        
        <!-- Sheet Header Bar -->
        <div class="px-6 md:px-12 lg:px-16 py-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm md:text-base font-bold text-zinc-950 dark:text-zinc-50 leading-tight">Edit Spot Sale Invoice</h3>
                        <span id="cora-edit-sale-no-badge" class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">INV-000</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Update customer details or payment collection method.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="CoraInventory.closeEditSaleModal()" class="py-1.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <span>Close</span>
                </button>
            </div>
        </div>

        <!-- Scrollable Content Form Body -->
        <form id="cora-inv-edit-sale-form" onsubmit="CoraInventory.submitUpdateSale(event)" class="flex-1 overflow-y-auto px-6 md:px-12 lg:px-16 py-6 max-w-4xl mx-auto w-full flex flex-col justify-between" style="min-height: 0; flex: 1 1 auto;">
            <input type="hidden" id="cora-edit-sale-id" value="0">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Retailer / Bookstore Name *</label>
                        <input type="text" id="cora-edit-sale-customer" required placeholder="e.g. Standard Book Depot" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Retailer Phone Number</label>
                        <input type="text" id="cora-edit-sale-phone" placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400">
                    </div>
                </div>

                <!-- Payment Mode Selection -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Payment Collection Method</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-2 cursor-pointer bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 transition-colors">
                            <input type="radio" name="cora_edit_sale_pay_mode" value="cash" class="text-zinc-900">
                            <span class="font-bold text-xs text-zinc-800 dark:text-zinc-200">Cash</span>
                        </label>
                        <label class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-2 cursor-pointer bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 transition-colors">
                            <input type="radio" name="cora_edit_sale_pay_mode" value="upi" class="text-zinc-900">
                            <span class="font-bold text-xs text-zinc-800 dark:text-zinc-200">UPI / QR</span>
                        </label>
                        <label class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-2 cursor-pointer bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 transition-colors">
                            <input type="radio" name="cora_edit_sale_pay_mode" value="credit" class="text-zinc-900">
                            <span class="font-bold text-xs text-zinc-800 dark:text-zinc-200">Credit Bill</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-4 mt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between gap-3 shrink-0">
                <button type="button" onclick="CoraInventory.closeEditSaleModal()" class="py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" id="cora-edit-sale-save-btn" class="py-2.5 px-6 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors cursor-pointer flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Save Invoice</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 5. DELETE SPOT INVOICE CONFIRMATION SHEET -->
<div id="cora-inv-delete-sale-sheet" class="fixed inset-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none;">
    <div id="cora-inv-delete-sale-backdrop" onclick="CoraInventory.closeDeleteSaleModal()" class="absolute inset-0 bg-black/40 dark:bg-black/60 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-delete-sale-drawer" class="absolute bottom-0 inset-x-0 w-full max-w-lg mx-auto bg-white dark:bg-zinc-900 border-t border-x border-zinc-200/90 dark:border-zinc-800 rounded-t-[28px] shadow-2xl p-6 transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto">
        <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-700 mx-auto mb-4"></div>
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center shrink-0 text-zinc-900 dark:text-zinc-100">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Delete Spot Invoice?</h4>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">
                    Are you sure you want to delete invoice <strong id="cora-delete-sale-no-display" class="font-mono text-zinc-900 dark:text-zinc-200"></strong>? This will restore the sold quantities back to active van stock and recalculate consignment totals.
                </p>
                <div class="mt-4 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="CoraInventory.closeDeleteSaleModal()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" id="cora-del-sale-confirm-btn" onclick="CoraInventory.confirmDeleteSale()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-zinc-900 hover:bg-black text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 transition-colors cursor-pointer flex items-center gap-1.5 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>Delete Invoice</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 6. EDIT CONSIGNMENT STUDIO DRAWER -->
<div id="cora-inv-edit-csn-sheet" class="fixed inset-x-0 bottom-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none; top: 48px; height: calc(100vh - 48px);">
    <div id="cora-inv-edit-csn-backdrop" onclick="CoraInventory.closeEditConsignmentModal()" class="absolute inset-0 bg-white dark:bg-zinc-950 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-edit-csn-drawer" class="absolute inset-0 w-full h-full bg-white dark:bg-zinc-900 border-t border-zinc-200/80 dark:border-zinc-800 shadow-2xl transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto flex flex-col overflow-hidden z-10" style="top: 0; bottom: 0; height: 100% !important; min-height: 100% !important; max-height: 100% !important;">
        
        <!-- Sheet Header Bar -->
        <div class="px-6 md:px-12 lg:px-16 py-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm md:text-base font-bold text-zinc-950 dark:text-zinc-50 leading-tight">Edit Dispatch Consignment</h3>
                        <span id="cora-edit-csn-no-badge" class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">CSN-000</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Update vehicle details, route name, or navigation stops for this dispatch.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="CoraInventory.closeEditConsignmentModal()" class="py-1.5 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <span>Close</span>
                </button>
            </div>
        </div>

        <!-- Scrollable Content Form Body -->
        <form id="cora-inv-edit-csn-form" onsubmit="CoraInventory.submitUpdateConsignment(event)" class="flex-1 overflow-y-auto px-6 md:px-12 lg:px-16 py-6 max-w-4xl mx-auto w-full flex flex-col justify-between" style="min-height: 0; flex: 1 1 auto;">
            <input type="hidden" id="cora-edit-csn-id" value="0">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Vehicle Number *</label>
                        <input type="text" id="cora-edit-csn-vehicleno" required placeholder="e.g. DL 01 AB 1234" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 uppercase font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Route / Territory Name *</label>
                        <input type="text" id="cora-edit-csn-routename" required placeholder="e.g. Central Delhi - Connaught Place Route" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Google Maps Route URL</label>
                    <input type="url" id="cora-edit-csn-gmaps" placeholder="https://maps.app.goo.gl/... or https://google.com/maps/dir/..." class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Target Cities / Stops (Comma separated)</label>
                    <input type="text" id="cora-edit-csn-cities" placeholder="e.g. New Delhi, Noida, Gurugram, Faridabad" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400">
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-4 mt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between gap-3 shrink-0">
                <button type="button" onclick="CoraInventory.closeEditConsignmentModal()" class="py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" id="cora-edit-csn-save-btn" class="py-2.5 px-6 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors cursor-pointer flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Save Consignment</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 7. DELETE CONSIGNMENT CONFIRMATION SHEET -->
<div id="cora-inv-delete-csn-sheet" class="fixed inset-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none;">
    <div id="cora-inv-delete-csn-backdrop" onclick="CoraInventory.closeDeleteConsignmentModal()" class="absolute inset-0 bg-black/40 dark:bg-black/60 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-delete-csn-drawer" class="absolute bottom-0 inset-x-0 w-full max-w-lg mx-auto bg-white dark:bg-zinc-900 border-t border-x border-zinc-200/90 dark:border-zinc-800 rounded-t-[28px] shadow-2xl p-6 transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto">
        <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-700 mx-auto mb-4"></div>
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center shrink-0 text-zinc-900 dark:text-zinc-100">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Delete Dispatch Consignment?</h4>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">
                    Are you sure you want to delete consignment <strong id="cora-delete-csn-no-display" class="font-mono text-zinc-900 dark:text-zinc-200"></strong>? Any unsold allocated items will be automatically restocked back to the Factory Catalog.
                </p>
                <div class="mt-4 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="CoraInventory.closeDeleteConsignmentModal()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" id="cora-del-csn-confirm-btn" onclick="CoraInventory.confirmDeleteConsignment()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-zinc-900 hover:bg-black text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 transition-colors cursor-pointer flex items-center gap-1.5 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>Delete Consignment</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 6. EXECUTIVE AUDIT & RECON SHARE STUDIO DRAWER -->
<div id="cora-recon-share-sheet" class="fixed inset-0 z-[99999] pointer-events-none transition-all duration-300 hidden" style="display:none;">
    <div id="cora-recon-share-backdrop" onclick="CoraInventory.closeReconShareModal()" class="absolute inset-0 bg-black/40 dark:bg-black/60 opacity-0 transition-opacity duration-300"></div>
    <div id="cora-recon-share-drawer" class="absolute bottom-0 inset-x-0 w-full max-w-lg mx-auto bg-white dark:bg-zinc-900 border-t border-x border-zinc-200/90 dark:border-zinc-800 rounded-t-[28px] shadow-2xl p-6 transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto max-h-[90vh] flex flex-col">
        <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-700 mx-auto mb-4 shrink-0"></div>
        
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-900 dark:text-zinc-100">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Share Daily Audit Report</h3>
                    <p class="text-[11px] text-zinc-500">Distribute executive briefing and print-ready PDF</p>
                </div>
            </div>
            <button type="button" onclick="CoraInventory.closeReconShareModal()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- 4 Fast Action Buttons -->
        <div class="grid grid-cols-2 gap-2.5 my-4 shrink-0">
            <!-- Share via WhatsApp -->
            <button type="button" onclick="CoraInventory.shareReconWhatsApp()" class="p-3 rounded-xl bg-zinc-900 hover:bg-black text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-semibold text-xs transition-colors flex items-center gap-2 cursor-pointer shadow-2xs">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                <span>Share to WhatsApp</span>
            </button>

            <!-- Copy Text Briefing -->
            <button type="button" onclick="CoraInventory.copyReconShareText()" class="p-3 rounded-xl bg-white hover:bg-zinc-50 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700 font-semibold text-xs transition-colors flex items-center gap-2 cursor-pointer shadow-2xs">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                <span>Copy Summary Text</span>
            </button>

            <!-- Copy PDF Link -->
            <button type="button" onclick="CoraInventory.copyReconPDFLink()" class="p-3 rounded-xl bg-white hover:bg-zinc-50 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700 font-semibold text-xs transition-colors flex items-center gap-2 cursor-pointer shadow-2xs">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                <span>Copy PDF Link</span>
            </button>

            <!-- Open Print-Ready PDF -->
            <button type="button" onclick="CoraInventory.exportDailyPDF()" class="p-3 rounded-xl bg-white hover:bg-zinc-50 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700 font-semibold text-xs transition-colors flex items-center gap-2 cursor-pointer shadow-2xs">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <span>Open Print PDF</span>
            </button>
        </div>

        <!-- Preview of Briefing -->
        <div class="flex-1 overflow-hidden flex flex-col min-h-0">
            <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5 block shrink-0">Briefing Preview</label>
            <div id="cora-recon-share-preview" class="flex-1 overflow-y-auto p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80 text-[11px] font-mono leading-relaxed text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap select-all">
                Run Audit to preview the executive summary.
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-end shrink-0">
            <button type="button" onclick="CoraInventory.closeReconShareModal()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">
                Done
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT CONTROLLER FOR INVENTORY MODULE                                -->
<!-- ========================================================================= -->
<script>
window.CoraInventory = (function($) {
    'use strict';
    $ = $ || window.jQuery || window.$;

    const isDriverOnly = <?php echo $is_driver_only ? 'true' : 'false'; ?>;
    let currentPerspective = isDriverOnly ? 'vendor' : 'plant';
    let currentSubtab = 'catalog';
    let catalogCache = [];
    let selectedProductIds = new Set();
    let consignmentsCache = [];
    let salesLedgerCache = [];
    let mapInstance = null;
    let mapMarkers = [];
    let activeConsignmentId = 0;
    let activeConsignmentData = null;
    let activeConsignmentItems = [];
    let ocrParsedResult = null;
    let deleteSaleId = 0;
    let deleteCsnId = 0;
    let isFocusModeActive = false;
    let isAnalyticsCardsVisible = true;

    function initAnalyticsCards() {
        const saved = localStorage.getItem('cora_inventory_analytics_visible');
        if (saved === '0') {
            setAnalyticsCards(false, false);
        } else {
            setAnalyticsCards(true, false);
        }
    }

    function toggleAnalyticsCards(forceState) {
        const newState = (forceState !== undefined) ? forceState : !isAnalyticsCardsVisible;
        setAnalyticsCards(newState, true);
    }

    function setAnalyticsCards(visible, showToast) {
        isAnalyticsCardsVisible = !!visible;
        try {
            localStorage.setItem('cora_inventory_analytics_visible', isAnalyticsCardsVisible ? '1' : '0');
        } catch(e) {}

        const container = $('#cora-inv-kpis-container');
        const btn = $('#cora-inv-analytics-toggle-btn');
        const txt = $('#cora-inv-analytics-btn-text');

        if (isAnalyticsCardsVisible) {
            container.removeClass('hidden').fadeIn(200);
            btn.removeClass('is-hidden');
            txt.text('Analytics: Visible');
            if (showToast && window.coraShowToast) {
                window.coraShowToast('Summary analytics metric cards shown.', 'info');
            }
        } else {
            container.addClass('hidden').hide();
            btn.addClass('is-hidden');
            txt.text('Analytics: Hidden');
            if (showToast && window.coraShowToast) {
                window.coraShowToast('Summary analytics cards hidden for maximum uncluttered space.', 'info');
            }
        }
    }

    function initFocusMode() {
        const saved = localStorage.getItem('cora_inventory_focus_mode');
        if (saved === '1') {
            setFocusMode(true, false);
        } else {
            setFocusMode(false, false);
        }
    }

    function toggleFocusMode(forceState) {
        const newState = (forceState !== undefined) ? forceState : !isFocusModeActive;
        setFocusMode(newState, true);
    }

    function setFocusMode(active, showToast) {
        isFocusModeActive = !!active;
        try {
            localStorage.setItem('cora_inventory_focus_mode', isFocusModeActive ? '1' : '0');
        } catch(e) {}

        const body = $('body');
        const btn = $('#cora-inv-focus-toggle-btn');
        const dot = $('#cora-inv-focus-dot');
        const txt = $('#cora-inv-focus-btn-text');

        if (isFocusModeActive) {
            body.addClass('cora-inventory-focus-mode');
            btn.addClass('is-active');
            dot.addClass('bg-emerald-400 animate-pulse').removeClass('bg-zinc-400');
            txt.text('Single-App Mode: Active (Click to Show All)');

            // Deterministic DOM hiding for 100% cross-browser reliability
            $('.cora-sidebar-nav .cora-nav-group').each(function() {
                const group = $(this);
                const hasInv = group.find('[data-target*="inventory"], [data-target*="plant"]').length > 0;
                if (hasInv) {
                    group.show();
                    group.find('.cora-nav-list li').each(function() {
                        const li = $(this);
                        const isInv = li.is('[data-target*="inventory"], [data-target*="plant"]') || li.find('[data-target*="inventory"], [data-target*="plant"]').length > 0;
                        if (isInv) {
                            li.show();
                        } else {
                            li.hide();
                        }
                    });
                } else {
                    group.hide();
                }
            });

            if (showToast && window.coraShowToast) {
                window.coraShowToast('Single-App Dedicated Mode active. All other sidebar modules are hidden.', 'success');
            }
        } else {
            body.removeClass('cora-inventory-focus-mode');
            btn.removeClass('is-active');
            dot.removeClass('bg-emerald-400 animate-pulse').addClass('bg-zinc-400');
            txt.text('Single-App Mode: Off');

            // Deterministic DOM restore for all sidebar groups & items
            $('.cora-sidebar-nav .cora-nav-group').show();
            $('.cora-sidebar-nav .cora-nav-list li').show();

            if (showToast && window.coraShowToast) {
                window.coraShowToast('Full workspace sidebar restored with all modules.', 'info');
            }
        }
    }

    function init() {
        initFocusMode();
        initAnalyticsCards();
        const urlParams = new URLSearchParams(window.location.search);
        const forceVendor = isDriverOnly || urlParams.get('driver_mode') === '1' || urlParams.get('view_as_driver') === '1' || urlParams.get('is_driver') === '1' || urlParams.get('view') === 'vendor' || urlParams.get('perspective') === 'vendor';

        if (forceVendor) {
            switchPerspective('vendor');
        } else {
            loadCatalog();
            loadConsignments();
            loadVendorDashboard();
        }
    }

    function switchPerspective(mode) {
        if (isDriverOnly && mode === 'plant') {
            return;
        }
        currentPerspective = mode;
        if (mode === 'plant') {
            $('body').removeClass('cora-driver-mode-active');
            $('#cora-inv-plant-container').removeClass('hidden');
            $('#cora-inv-vendor-container').addClass('hidden');
            $('#cora-inv-plant-actions').removeClass('hidden').addClass('flex');
            $('#cora-inv-vendor-actions').addClass('hidden').removeClass('flex');
            $('#cora-inv-perspective-switcher').removeClass('hidden');
            $('#cora-inv-plant-title-wrap').removeClass('hidden');
            $('#cora-inv-driver-title-wrap').addClass('hidden');
            $('#cora-inv-btn-view-plant').addClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs').removeClass('text-zinc-600 dark:text-zinc-400');
            $('#cora-inv-btn-view-vendor').removeClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs').addClass('text-zinc-600 dark:text-zinc-400');
            // Remove any leftover inline display styles so responsive CSS cascades cleanly
            $('#cora-mobile-floating-island').css('display', '');
            $('.cora-sidebar').css('display', '');
        } else {
            $('body').addClass('cora-driver-mode-active');
            $('#cora-inv-plant-container').addClass('hidden');
            $('#cora-inv-vendor-container').removeClass('hidden');
            $('#cora-inv-plant-actions').addClass('hidden').removeClass('flex');
            $('#cora-inv-vendor-actions').removeClass('hidden').addClass('flex');
            $('#cora-inv-perspective-switcher').addClass('hidden');
            $('#cora-inv-plant-title-wrap').addClass('hidden');
            $('#cora-inv-driver-title-wrap').removeClass('hidden');
            $('#cora-inv-btn-view-vendor').addClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs').removeClass('text-zinc-600 dark:text-zinc-400');
            $('#cora-inv-btn-view-plant').removeClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs').addClass('text-zinc-600 dark:text-zinc-400');
            loadVendorDashboard();
        }
    }

    function switchSubtab(tab) {
        currentSubtab = tab;
        $('[id^="cora-subtab-"]').addClass('hidden');
        $('#cora-subtab-' + tab).removeClass('hidden');

        $('[id^="cora-tab-btn-"]').removeClass('border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 font-bold')
            .addClass('border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 font-semibold');
        
        $('#cora-tab-btn-' + tab).addClass('border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 font-bold')
            .removeClass('border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 font-semibold');

        if (tab === 'map') {
            setTimeout(initLeafletMap, 150);
        }
    }

    let currentPricingFilter = 'all';

    function setPricingFilter(type) {
        currentPricingFilter = type || 'all';
        $('#cora-filter-pricing-select').val(currentPricingFilter);
        $('.cora-pricing-filter-btn').removeClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-bold shadow-2xs')
            .addClass('text-zinc-600 dark:text-zinc-400 font-semibold');
        
        if (currentPricingFilter === 'all') {
            $('#cora-filter-pricing-all').addClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-bold shadow-2xs').removeClass('text-zinc-600 dark:text-zinc-400 font-semibold');
        } else if (currentPricingFilter === 'weight_based') {
            $('#cora-filter-pricing-weight').addClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-bold shadow-2xs').removeClass('text-zinc-600 dark:text-zinc-400 font-semibold');
        } else if (currentPricingFilter === 'unit_based') {
            $('#cora-filter-pricing-unit').addClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-bold shadow-2xs').removeClass('text-zinc-600 dark:text-zinc-400 font-semibold');
        }
        loadCatalog();
    }

    function updateFilterCounts(kpis) {
        if (!kpis) return;
        const total = kpis.total_skus || 0;
        const weight = kpis.weight_count || 0;
        const unit = kpis.unit_count || 0;
        
        $('#cora-filter-pricing-select option[value="all"]').text('All Items (' + total + ')');
        $('#cora-filter-pricing-select option[value="weight_based"]').text('Weight-Based (' + weight + ')');
        $('#cora-filter-pricing-select option[value="unit_based"]').text('Unit-Based (' + unit + ')');
        
        $('#cora-filter-count-all').text(total);
        $('#cora-filter-count-weight').text(weight);
        $('#cora-filter-count-unit').text(unit);
    }

    function formatCategoryName(slug) {
        if (!slug) return 'Uncategorized';
        const map = {
            'notebooks': 'Notebooks, Registers & Bahee',
            'school_supplies': 'School Essentials (Pencils/Erasers)',
            'paper_reams': 'Paper Reams & Sheets',
            'writing_instruments': 'Writing Instruments & Pens',
            'office_supplies': 'Office Supplies & Binding',
            'art_kits': 'Art & Craft Supplies',
            'art_supplies': 'Art & Craft Supplies',
            'adhesives': 'Adhesives, Glue & Tapes',
            'files_folders': 'Files, Folders & Storage'
        };
        if (map[slug]) return map[slug];
        return slug.split(/[-_]/).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
    }

    function renderCategoryDropdowns(categories) {
        if (!Array.isArray(categories)) return;

        // 1. Update Toolbar Filter Dropdown (#cora-inv-cat-filter)
        const filterSelect = $('#cora-inv-cat-filter');
        const currentFilterVal = filterSelect.val() || 'all';
        let totalCount = 0;
        categories.forEach(c => { totalCount += parseInt(c.count || 0); });

        let filterHtml = `<option value="all">All Categories (${totalCount})</option>`;
        categories.forEach(c => {
            const isSelected = (c.category === currentFilterVal) ? 'selected' : '';
            filterHtml += `<option value="${escapeHtml(c.category)}" ${isSelected}>${escapeHtml(formatCategoryName(c.category))} (${c.count})</option>`;
        });
        filterSelect.html(filterHtml);
        if (currentFilterVal && filterSelect.find(`option[value="${currentFilterVal}"]`).length) {
            filterSelect.val(currentFilterVal);
        }

        // 2. Update SKU Drawer Category Dropdown (#cora-prod-cat)
        const prodCatSelect = $('#cora-prod-cat');
        const currentProdVal = prodCatSelect.val() || 'notebooks';
        let prodHtml = '';
        const seen = new Set();

        categories.forEach(c => {
            if (c.category && !seen.has(c.category)) {
                seen.add(c.category);
                prodHtml += `<option value="${escapeHtml(c.category)}">${escapeHtml(formatCategoryName(c.category))}</option>`;
            }
        });

        // Ensure default fallback options exist if DB categories are empty
        if (!seen.size) {
            ['notebooks', 'school_supplies', 'paper_reams', 'writing_instruments', 'office_supplies'].forEach(cat => {
                prodHtml += `<option value="${cat}">${formatCategoryName(cat)}</option>`;
            });
        }

        prodHtml += `<option value="__custom__">+ Custom Category...</option>`;
        prodCatSelect.html(prodHtml);
        if (currentProdVal && prodCatSelect.find(`option[value="${currentProdVal}"]`).length) {
            prodCatSelect.val(currentProdVal);
        }
    }

    function loadCatalog() {
        const search = $('#cora-inv-search').val();
        const category = $('#cora-inv-cat-filter').val();
        const sortOrder = $('#cora-inv-sort-filter').val() || 'default';
        const lowStock = $('#cora-inv-low-stock-check').is(':checked') ? 1 : 0;

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_get_catalog',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                search: search,
                category: category,
                pricing_type: currentPricingFilter,
                sort_order: sortOrder,
                low_stock: lowStock
            },
            success: function(res) {
                if (res.success) {
                    catalogCache = res.data.products || [];
                    renderCatalogTable(catalogCache);
                    renderKPIs(res.data.kpis || {});
                    updateFilterCounts(res.data.kpis || {});
                    if (res.data.categories) {
                        renderCategoryDropdowns(res.data.categories);
                    }
                }
            }
        });
    }

    function toggleSelectAll(masterCb) {
        const isChecked = $(masterCb).is(':checked');
        if (isChecked) {
            catalogCache.forEach(p => selectedProductIds.add(parseInt(p.id)));
        } else {
            selectedProductIds.clear();
        }
        $('.cora-inv-item-cb').prop('checked', isChecked);
        updateBatchBar();
    }

    function selectAllVisible() {
        catalogCache.forEach(p => selectedProductIds.add(parseInt(p.id)));
        $('.cora-inv-item-cb').prop('checked', true);
        $('#cora-inv-select-all').prop('checked', true).prop('indeterminate', false);
        updateBatchBar();
    }

    function onItemCheckboxChange(cb) {
        const id = parseInt($(cb).val());
        if ($(cb).is(':checked')) {
            selectedProductIds.add(id);
        } else {
            selectedProductIds.delete(id);
        }
        syncMasterCheckbox();
        updateBatchBar();
    }

    function syncMasterCheckbox() {
        const totalVisible = catalogCache.length;
        const selectedCount = selectedProductIds.size;
        const master = $('#cora-inv-select-all');
        if (totalVisible === 0 || selectedCount === 0) {
            master.prop('checked', false).prop('indeterminate', false);
        } else if (selectedCount >= totalVisible) {
            master.prop('checked', true).prop('indeterminate', false);
        } else {
            master.prop('checked', false).prop('indeterminate', true);
        }
    }

    function updateBatchBar() {
        const count = selectedProductIds.size;
        const bar = $('#cora-inv-batch-bar');
        $('#cora-inv-batch-count').text(count);
        $('#cora-inv-batch-count-label').text(count === 1 ? '1 item' : count + ' items');
        $('#cora-inv-batch-del-btn-count').text(count);
        $('#cora-inv-batch-export-btn-count').text(count);
        $('#cora-inv-batch-total-visible').text(catalogCache.length);

        if (count > 0) {
            bar.removeClass('hidden').addClass('flex');
        } else {
            bar.addClass('hidden').removeClass('flex');
        }
    }

    function clearSelection() {
        selectedProductIds.clear();
        $('.cora-inv-item-cb').prop('checked', false);
        $('#cora-inv-select-all').prop('checked', false).prop('indeterminate', false);
        updateBatchBar();
    }

    function openBulkDeleteModal() {
        if (!selectedProductIds.size) {
            if (window.coraShowToast) window.coraShowToast('Please select at least 1 item to delete.', 'info');
            return;
        }

        const count = selectedProductIds.size;
        $('#cora-bulk-del-count-text').text(count === 1 ? '1 product' : count + ' products');
        
        let listHtml = '';
        let previewCount = 0;
        catalogCache.forEach(p => {
            if (selectedProductIds.has(parseInt(p.id))) {
                if (previewCount < 10) {
                    listHtml += `
                        <div class="flex items-center justify-between py-1.5 px-2.5 rounded-lg bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100 truncate pr-2">${escapeHtml(p.name)}</div>
                            <span class="text-[10px] font-mono text-zinc-500 shrink-0 font-bold">${escapeHtml(p.sku)}</span>
                        </div>
                    `;
                }
                previewCount++;
            }
        });

        if (previewCount > 10) {
            listHtml += `
                <div class="py-1 text-center text-[11px] font-medium text-zinc-400">
                    + and ${previewCount - 10} more selected items
                </div>
            `;
        }

        $('#cora-bulk-del-preview-list').html(listHtml);
        $('#cora-inv-bulk-delete-sheet').removeClass('hidden').css('display', 'block');
        requestAnimationFrame(() => {
            $('#cora-inv-bulk-delete-sheet').removeClass('pointer-events-none');
            $('#cora-inv-bulk-delete-backdrop').removeClass('opacity-0').addClass('opacity-100');
            $('#cora-inv-bulk-delete-drawer').removeClass('translate-y-full').addClass('translate-y-0');
        });
    }

    function closeBulkDeleteModal() {
        $('#cora-inv-bulk-delete-drawer').removeClass('translate-y-0').addClass('translate-y-full');
        $('#cora-inv-bulk-delete-backdrop').removeClass('opacity-100').addClass('opacity-0');
        setTimeout(() => {
            $('#cora-inv-bulk-delete-sheet').addClass('pointer-events-none hidden').css('display', 'none');
        }, 300);
    }

    function confirmBulkDeleteProducts() {
        if (!selectedProductIds.size) return;

        const ids = Array.from(selectedProductIds);
        const btn = $('#cora-bulk-del-confirm-btn');
        btn.prop('disabled', true).text('Deleting ' + ids.length + ' products...');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_bulk_delete_products',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                ids: ids
            },
            success: function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> <span>Confirm Bulk Delete</span>');
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Selected products removed from catalog.', 'success');
                    closeBulkDeleteModal();
                    clearSelection();
                    loadCatalog();
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data || 'Failed to delete products', 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> <span>Confirm Bulk Delete</span>');
                if (window.coraShowToast) window.coraShowToast('Network error during bulk deletion.', 'error');
            }
        });
    }

    let currentViewMode = 'default';

    function setViewMode(mode) {
        currentViewMode = mode;
        $('.cora-view-mode-btn').removeClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-bold shadow-2xs')
            .addClass('text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 font-semibold');

        if (mode === 'default') {
            $('#cora-view-btn-default').addClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-bold shadow-2xs').removeClass('text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 font-semibold');
            $('#cora-inv-thead-default').removeClass('hidden');
            $('#cora-inv-thead-spreadsheet').addClass('hidden');
            $('#cora-inv-spreadsheet-footer').addClass('hidden');
        } else {
            $('#cora-view-btn-spreadsheet').addClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-bold shadow-2xs').removeClass('text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 font-semibold');
            $('#cora-inv-thead-default').addClass('hidden');
            $('#cora-inv-thead-spreadsheet').removeClass('hidden');
            $('#cora-inv-spreadsheet-footer').removeClass('hidden');
        }
        renderCatalogTable(catalogCache);
    }

    function renderCatalogTable(products) {
        const tbody = $('#cora-inv-table-body');
        const mobileWrap = $('#cora-inv-mobile-cards-wrap');

        const colSpanCount = (currentViewMode === 'spreadsheet') ? 9 : 7;

        if (!products.length) {
            const emptyHtml = `
                <div class="py-12 text-center select-none">
                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto px-4">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-3 text-zinc-400">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <span class="text-sm font-bold text-zinc-800 dark:text-zinc-200">No stationery products found</span>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Get started by importing a CSV sheet from Tally/Excel or load 1-click starter categories.</p>
                        <div class="flex items-center gap-2 mt-4">
                            <button type="button" onclick="CoraInventory.openImportModal('csv')" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-zinc-950 dark:bg-zinc-100 dark:text-zinc-950 rounded-xl shadow-2xs hover:bg-zinc-800 transition-colors cursor-pointer">
                                Import CSV
                            </button>
                            <button type="button" onclick="CoraInventory.openImportModal('kits')" class="px-3.5 py-1.5 text-xs font-semibold text-zinc-800 dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-2xs hover:bg-zinc-50 transition-colors cursor-pointer">
                                1-Click Kits
                            </button>
                        </div>
                    </div>
                </div>
            `;
            tbody.html(`<tr><td colspan="${colSpanCount}">${emptyHtml}</td></tr>`);
            mobileWrap.html(emptyHtml);
            $('#cora-ft-qty').text('0 units');
            $('#cora-ft-skus-count').text('0');
            $('#cora-ft-weight').text('0.00 Kg');
            $('#cora-ft-total-base').text('₹0.00');
            $('#cora-ft-gst-total').html('<div>₹0.00 <span class="text-[10px] font-normal text-zinc-500">18% GST</span></div><div class="text-[11px] text-zinc-950 dark:text-zinc-50 font-bold">₹0.00 Total</div>');
            syncMasterCheckbox();
            updateBatchBar();
            return;
        }

        let tableHtml = '';
        let mobileHtml = '';
        let visibleTotalUnits = 0;
        let visibleTotalWeightGrams = 0;
        let visibleTotalBasePrice = 0;
        let visibleTotalGst18 = 0;
        let visibleTotalPriceWithGst = 0;

        products.forEach(p => {
            const isSelected = selectedProductIds.has(parseInt(p.id));
            const isLow = parseInt(p.stock_quantity) <= parseInt(p.low_stock_threshold);
            const isWeight = (p.pricing_type === 'weight_based') || (p.uom && p.uom.includes('g)')) || (p.sku && p.sku.startsWith('KGZ-WGT'));
            
            const stockQty = parseInt(p.stock_quantity || 0);
            const unitPrice = parseFloat(p.wholesale_price || p.wholesale_rate || 0);
            const mrpPrice = parseFloat(p.mrp || 0);
            const totalBase = unitPrice * stockQty;
            const unitGst18 = unitPrice * 0.18;
            const unitPriceWithGst = unitPrice + unitGst18;
            const totalGst18 = totalBase * 0.18;
            const totalPriceWithGst = totalBase + totalGst18;

            // Weight calculation
            let unitWeightGrams = null;
            if (p.unit_weight_grams && parseFloat(p.unit_weight_grams) > 0) {
                unitWeightGrams = parseFloat(p.unit_weight_grams);
            } else if (isWeight) {
                const match = (p.uom || '').match(/(\d+(?:\.\d+)?)\s*g/i) || (p.description || '').match(/(\d+(?:\.\d+)?)\s*g/i);
                if (match) unitWeightGrams = parseFloat(match[1]);
            }

            if (unitWeightGrams && stockQty > 0) {
                visibleTotalWeightGrams += (unitWeightGrams * stockQty);
            }

            // Total weight in Kg for weight-based items
            let totalKg = null;
            let kgDisplay = null;
            if (isWeight && unitWeightGrams && unitWeightGrams > 0) {
                totalKg = (stockQty * unitWeightGrams) / 1000;
                kgDisplay = (totalKg % 1 === 0) ? totalKg : totalKg.toFixed(2);
            }

            // Pages
            let pagesCount = null;
            if (p.pages_count && parseInt(p.pages_count) > 0) {
                pagesCount = parseInt(p.pages_count);
            } else if (!isWeight) {
                const matchP = (p.name + ' ' + (p.description || '')).match(/(\d+)\s*(?:Pgs|Pages|शीट|पृष्ठ)/i);
                if (matchP) pagesCount = parseInt(matchP[1]);
            }

            visibleTotalUnits += stockQty;
            visibleTotalBasePrice += totalBase;
            visibleTotalGst18 += totalGst18;
            visibleTotalPriceWithGst += totalPriceWithGst;

            const lowBadge = isLow 
                ? `<span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[9px] font-bold font-mono whitespace-nowrap bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60"><span class="w-1 h-1 rounded-full bg-amber-500"></span>Low</span>`
                : '';

            const weightBadgeIcon = isWeight 
                ? `<span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[9px] font-mono font-bold tracking-tight shrink-0 bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 3v18M6 8l6-5 6 5M6 8l-3 7h6l-3-7zm12 0l-3 7h6l-3-7z"></path></svg> Weight</span>`
                : `<span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[9px] font-mono font-bold tracking-tight shrink-0 bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg> Unit</span>`;

            const thumbHtml = p.image_url 
                ? `<img src="${escapeHtml(p.image_url)}" class="w-8 h-8 rounded-lg object-cover border border-zinc-200 dark:border-zinc-700 shrink-0">`
                : `<div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-bold text-[10px] text-zinc-700 dark:text-zinc-300 shrink-0">${(p.name || 'P').substring(0, 2).toUpperCase()}</div>`;

            if (currentViewMode === 'default') {
                // 1. DEFAULT NOTION/SHOPIFY TABLE ROW (7 Columns - Clean, uncluttered layout)
                tableHtml += `
                    <tr class="hover:bg-zinc-50/90 dark:hover:bg-zinc-800/50 transition-colors ${isSelected ? 'bg-zinc-100/80 dark:bg-zinc-800/70' : ''}">
                        <!-- Col 1: Selection Checkbox -->
                        <td class="py-3 px-3.5 text-center">
                            <input type="checkbox" class="cora-inv-item-cb rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900 cursor-pointer" value="${p.id}" onchange="CoraInventory.onItemCheckboxChange(this)" ${isSelected ? 'checked' : ''}>
                        </td>

                        <!-- Col 2: Product Name & SKU Only (Uncluttered) -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2.5">
                                ${thumbHtml}
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <div class="font-bold text-zinc-950 dark:text-zinc-50 text-xs sm:text-sm leading-tight truncate">${escapeHtml(p.name)}</div>
                                        ${weightBadgeIcon}
                                    </div>
                                    <div class="mt-0.5 text-[11px] font-mono text-zinc-400">
                                        <span class="font-semibold text-zinc-600 dark:text-zinc-300">${escapeHtml(p.sku)}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Col 3: Category & UOM -->
                        <td class="py-3 px-4">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100 text-xs">${escapeHtml(formatCategoryName(p.category))}</div>
                            <div class="text-[11px] text-zinc-400 mt-0.5">${escapeHtml(p.uom || (isWeight ? 'Kg' : 'Pcs'))}</div>
                        </td>

                        <!-- Col 4: HSN & Tax (18% GST) -->
                        <td class="py-3 px-4 font-mono text-xs">
                            <div class="font-bold text-zinc-800 dark:text-zinc-200">HSN ${escapeHtml(p.hsn_code || '4820')}</div>
                            <div class="text-[11px] text-emerald-700 dark:text-emerald-400 font-medium">18% GST (+₹${unitGst18.toFixed(2)}/u)</div>
                        </td>

                        <!-- Col 5: Wholesale & MRP -->
                        <td class="py-3 px-4 font-mono text-xs">
                            <div class="font-bold text-zinc-950 dark:text-zinc-50 text-sm">₹${unitPrice.toFixed(2)}</div>
                            <div class="text-[11px] text-zinc-400">MRP ₹${mrpPrice.toFixed(2)} • <span class="text-zinc-600 dark:text-zinc-400 font-semibold">₹${unitPriceWithGst.toFixed(2)} incl.</span></div>
                        </td>

                        <!-- Col 6: Stock Units / Weight -->
                        <td class="py-3 px-4 font-mono">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-zinc-950 dark:text-zinc-50 text-sm">${stockQty.toLocaleString('en-IN')}</span>
                                <span class="text-xs text-zinc-400">units</span>
                            </div>
                            ${isWeight && kgDisplay !== null ? `
                                <div class="text-[11px] font-mono font-bold text-zinc-600 dark:text-zinc-300">(${kgDisplay} Kg)</div>
                            ` : ''}
                            ${lowBadge ? `<div class="mt-0.5">${lowBadge}</div>` : ''}
                        </td>

                        <!-- Col 7: Actions (Color Psychology Icon Primitives) -->
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Adjust Stock (Blue Psychology) -->
                                <button type="button" onclick="CoraInventory.quickAdjustStock(${p.id})" class="p-1.5 rounded-lg bg-zinc-100 hover:bg-blue-50 text-zinc-700 hover:text-blue-700 dark:bg-zinc-800 dark:hover:bg-blue-950/50 dark:text-zinc-300 dark:hover:text-blue-300 border border-zinc-200/80 dark:border-zinc-700/80 transition-colors cursor-pointer shadow-2xs" title="Adjust Physical Stock">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                                </button>
                                <!-- Edit Specs (Emerald Psychology) -->
                                <button type="button" onclick="CoraInventory.openProductModal(${p.id})" class="p-1.5 rounded-lg bg-zinc-100 hover:bg-emerald-50 text-zinc-700 hover:text-emerald-700 dark:bg-zinc-800 dark:hover:bg-emerald-950/50 dark:text-zinc-300 dark:hover:text-emerald-300 border border-zinc-200/80 dark:border-zinc-700/80 transition-colors cursor-pointer shadow-2xs" title="Edit Product Specs">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                </button>
                                <!-- Delete Product (Rose/Red Psychology) -->
                                <button type="button" onclick="CoraInventory.openDeleteModal(${p.id})" class="p-1.5 rounded-lg bg-zinc-100 hover:bg-rose-50 text-zinc-400 hover:text-rose-600 dark:bg-zinc-800 dark:hover:bg-rose-950/50 dark:hover:text-rose-400 border border-zinc-200/80 dark:border-zinc-700/80 transition-colors cursor-pointer shadow-2xs" title="Delete Product">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                // 2. SPREADSHEET TABLE ROW (9 Columns with borders)
                tableHtml += `
                    <tr class="hover:bg-zinc-50/90 dark:hover:bg-zinc-800/50 transition-colors ${isSelected ? 'bg-zinc-100/80 dark:bg-zinc-800/70' : ''}">
                        <!-- Col 1: Quantity / Weight & Checkbox -->
                        <td class="py-2.5 px-3 text-center border-r border-zinc-200/60 dark:border-zinc-800/80">
                            <div class="flex items-center justify-center gap-2">
                                <input type="checkbox" class="cora-inv-item-cb rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900 cursor-pointer" value="${p.id}" onchange="CoraInventory.onItemCheckboxChange(this)" ${isSelected ? 'checked' : ''}>
                                <div class="text-left font-mono">
                                    <span class="font-bold text-zinc-950 dark:text-zinc-50 text-xs">${stockQty.toLocaleString('en-IN')}</span>
                                    <span class="text-[10px] text-zinc-400">u</span>
                                    ${isWeight && kgDisplay !== null ? `
                                        <div class="text-[9.5px] font-bold text-zinc-600 dark:text-zinc-300">(${kgDisplay} Kg)</div>
                                    ` : ''}
                                    ${lowBadge}
                                </div>
                            </div>
                        </td>

                        <!-- Col 2: Item Name (Product Name) -->
                        <td class="py-2.5 px-4 border-r border-zinc-200/60 dark:border-zinc-800/80">
                            <div class="flex items-center gap-2">
                                <div class="font-bold text-zinc-950 dark:text-zinc-50 text-xs sm:text-sm leading-tight">${escapeHtml(p.name)}</div>
                                ${weightBadgeIcon}
                            </div>
                            <div class="flex items-center gap-2 mt-0.5 text-[10px] text-zinc-400 font-sans">
                                <span class="capitalize">${escapeHtml((p.category || '').replace(/_/g, ' '))}</span>
                            </div>
                        </td>

                        <!-- Col 3: Pages -->
                        <td class="py-2.5 px-3 text-center border-r border-zinc-200/60 dark:border-zinc-800/80">
                            ${pagesCount ? `<span class="font-mono font-bold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md text-[11px]">${pagesCount} Pgs</span>` : `<span class="text-zinc-300 dark:text-zinc-600 font-mono text-xs">—</span>`}
                        </td>

                        <!-- Col 4: Weight (Unit) -->
                        <td class="py-2.5 px-3 text-center border-r border-zinc-200/60 dark:border-zinc-800/80">
                            ${unitWeightGrams ? `
                                <div class="font-mono font-bold text-zinc-900 dark:text-zinc-100 text-xs">${unitWeightGrams} g</div>
                                <div class="text-[10px] font-mono text-zinc-400">(${(unitWeightGrams / 1000).toFixed(2)} Kg)</div>
                            ` : `<span class="text-zinc-300 dark:text-zinc-600 font-mono text-xs">—</span>`}
                        </td>

                        <!-- Col 5: Unit Price (Excl. GST) -->
                        <td class="py-2.5 px-3 text-right font-mono border-r border-zinc-200/60 dark:border-zinc-800/80">
                            <div class="font-bold text-zinc-950 dark:text-zinc-50 text-xs">₹${unitPrice.toFixed(2)}</div>
                            <div class="text-[10px] text-zinc-400">MRP ₹${mrpPrice.toFixed(2)}</div>
                        </td>

                        <!-- Col 6: Total Price (Excl. GST) -->
                        <td class="py-2.5 px-3 text-right font-mono border-r border-zinc-200/60 dark:border-zinc-800/80">
                            <div class="font-bold text-zinc-950 dark:text-zinc-50 text-xs">₹${totalBase.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                            <div class="text-[10px] text-zinc-400 font-mono">${stockQty} × ₹${unitPrice.toFixed(2)}</div>
                        </td>

                        <!-- Col 7: Codes / HSN -->
                        <td class="py-2.5 px-3 font-mono text-xs border-r border-zinc-200/60 dark:border-zinc-800/80">
                            <div class="font-bold text-zinc-800 dark:text-zinc-200">HSN ${escapeHtml(p.hsn_code || '4820')}</div>
                            <div class="text-[10px] text-zinc-500 font-semibold truncate" title="${escapeHtml(p.sku)}">${escapeHtml(p.sku)}</div>
                        </td>

                        <!-- Col 8: 18% GST (Incl.) -->
                        <td class="py-2.5 px-4 font-mono text-right border-r border-zinc-200/60 dark:border-zinc-800/80">
                            <div class="text-[10px] text-emerald-700 dark:text-emerald-400 font-medium">+18% GST: ₹${unitGst18.toFixed(2)}/u</div>
                            <div class="font-bold text-zinc-950 dark:text-zinc-50 text-xs">₹${unitPriceWithGst.toFixed(2)} <span class="text-[9px] font-normal text-zinc-400 font-sans">incl. GST</span></div>
                            <div class="text-[10px] text-zinc-500">Batch: ₹${totalPriceWithGst.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                        </td>

                        <!-- Col 9: Actions (Color Psychology Icon Primitives) -->
                        <td class="py-2.5 px-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="CoraInventory.quickAdjustStock(${p.id})" class="p-1.5 rounded-lg bg-zinc-100 hover:bg-blue-50 text-zinc-700 hover:text-blue-700 dark:bg-zinc-800 dark:hover:bg-blue-950/50 dark:text-zinc-300 dark:hover:text-blue-300 border border-zinc-200/80 dark:border-zinc-700/80 transition-colors cursor-pointer shadow-2xs" title="Adjust Physical Stock">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                                </button>
                                <button type="button" onclick="CoraInventory.openProductModal(${p.id})" class="p-1.5 rounded-lg bg-zinc-100 hover:bg-emerald-50 text-zinc-700 hover:text-emerald-700 dark:bg-zinc-800 dark:hover:bg-emerald-950/50 dark:text-zinc-300 dark:hover:text-emerald-300 border border-zinc-200/80 dark:border-zinc-700/80 transition-colors cursor-pointer shadow-2xs" title="Edit Product Specs">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                </button>
                                <button type="button" onclick="CoraInventory.openDeleteModal(${p.id})" class="p-1.5 rounded-lg bg-zinc-100 hover:bg-rose-50 text-zinc-400 hover:text-rose-600 dark:bg-zinc-800 dark:hover:bg-rose-950/50 dark:hover:text-rose-400 border border-zinc-200/80 dark:border-zinc-700/80 transition-colors cursor-pointer shadow-2xs" title="Delete Product">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }

            // MOBILE STRUCTURED CARD
            mobileHtml += `
                <div class="p-3.5 rounded-2xl bg-white dark:bg-zinc-900/90 border ${isSelected ? 'border-zinc-900 dark:border-zinc-100 ring-1 ring-zinc-900 dark:ring-zinc-100' : 'border-zinc-200/80 dark:border-zinc-800'} shadow-2xs space-y-2.5">
                    <!-- Row 1: Checkbox + Title + Pricing Type -->
                    <div class="flex items-start gap-2.5">
                        <div class="pt-0.5 shrink-0">
                            <input type="checkbox" class="cora-inv-item-cb rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900 cursor-pointer w-4 h-4" value="${p.id}" onchange="CoraInventory.onItemCheckboxChange(this)" ${isSelected ? 'checked' : ''}>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="font-bold text-zinc-950 dark:text-zinc-50 text-xs leading-snug break-words">${escapeHtml(p.name)}</div>
                                ${weightBadgeIcon}
                            </div>
                            <div class="flex items-center gap-2 mt-1 text-[10px] font-mono text-zinc-400">
                                <span class="font-bold text-zinc-600 dark:text-zinc-300">${escapeHtml(p.sku)}</span>
                                <span>• HSN ${escapeHtml(p.hsn_code || '4820')}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Structured 4-Cell Specs Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800 font-mono text-[11px]">
                        <div>
                            <span class="text-zinc-400 block text-[9px] uppercase tracking-wide">Stock / Pages</span>
                            <span class="font-bold text-zinc-950 dark:text-zinc-50">${stockQty} units</span>
                            ${pagesCount ? `<span class="text-zinc-500 block text-[10px]">${pagesCount} Pgs</span>` : ''}
                        </div>
                        <div>
                            <span class="text-zinc-400 block text-[9px] uppercase tracking-wide">Unit Weight</span>
                            <span class="font-bold text-zinc-950 dark:text-zinc-50">${unitWeightGrams ? `${unitWeightGrams}g (${(unitWeightGrams/1000).toFixed(2)}Kg)` : '—'}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 block text-[9px] uppercase tracking-wide">Base Rate (Excl.)</span>
                            <span class="font-bold text-zinc-950 dark:text-zinc-50">₹${unitPrice.toFixed(2)}</span>
                            <span class="text-zinc-400 block text-[10px]">Batch: ₹${totalBase.toFixed(0)}</span>
                        </div>
                        <div>
                            <span class="text-emerald-700 dark:text-emerald-400 block text-[9px] uppercase font-bold tracking-wide">18% GST (Incl.)</span>
                            <span class="font-bold text-zinc-950 dark:text-zinc-50">₹${unitPriceWithGst.toFixed(2)}/u</span>
                            <span class="text-zinc-500 block text-[10px]">Batch: ₹${totalPriceWithGst.toFixed(0)}</span>
                        </div>
                    </div>

                    <!-- Row 3: Action Buttons -->
                    <div class="flex items-center gap-2 pt-1 border-t border-zinc-100 dark:border-zinc-800/80">
                        <button type="button" onclick="CoraInventory.quickAdjustStock(${p.id})" class="flex-1 py-1.5 px-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold flex items-center justify-center gap-1 cursor-pointer transition-colors">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 5v14M5 12h14"/></svg>
                            <span>Adjust Stock</span>
                        </button>
                        <button type="button" onclick="CoraInventory.openProductModal(${p.id})" class="flex-1 py-1.5 px-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-semibold flex items-center justify-center gap-1 cursor-pointer shadow-2xs transition-colors">
                            <span>Edit Specs</span>
                        </button>
                        <button type="button" onclick="CoraInventory.openDeleteModal(${p.id})" class="p-1.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 text-zinc-400 hover:text-red-600 border border-zinc-200 dark:border-zinc-700 cursor-pointer transition-colors" title="Delete Product">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            `;
        });

        tbody.html(tableHtml);
        mobileWrap.html(mobileHtml);

        // Update Spreadsheet Summary Footer
        $('#cora-ft-qty').text(visibleTotalUnits.toLocaleString('en-IN') + ' units');
        $('#cora-ft-skus-count').text(products.length);
        $('#cora-ft-weight').text((visibleTotalWeightGrams / 1000).toFixed(2) + ' Kg');
        $('#cora-ft-total-base').text('₹' + visibleTotalBasePrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#cora-ft-gst-total').html(`
            <div>₹${visibleTotalGst18.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})} <span class="text-[10px] font-normal text-zinc-500">18% GST</span></div>
            <div class="text-[11px] text-zinc-950 dark:text-zinc-50 font-bold">₹${visibleTotalPriceWithGst.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})} Total</div>
        `);

        syncMasterCheckbox();
        updateBatchBar();
    }

    function renderKPIs(kpis) {
        $('#cora-kpi-plant-val').text('₹' + Number(kpis.total_inventory_val || 0).toLocaleString('en-IN'));
        $('#cora-kpi-plant-units').text(Number(kpis.total_stock_units || 0).toLocaleString('en-IN'));
        $('#cora-kpi-plant-skus').text(kpis.total_skus || 0);
        $('#cora-kpi-wheels-val').text('₹' + Number(kpis.total_val_on_wheels || 0).toLocaleString('en-IN'));
        $('#cora-kpi-active-consignments').text(kpis.active_consignments_count || 0);
        $('#cora-kpi-today-sales').text('₹' + Number(kpis.today_sales_val || 0).toLocaleString('en-IN'));
        $('#cora-kpi-today-collections').text('₹' + Number(kpis.today_collections || 0).toLocaleString('en-IN'));
        
        const lowCount = parseInt(kpis.low_stock_count || 0);
        if (lowCount > 0) {
            $('#cora-kpi-low-stock-alert').html(`<span class="text-amber-700 dark:text-amber-300 font-bold">${lowCount} items</span>`);
        } else {
            $('#cora-kpi-low-stock-alert').html(`<span class="text-zinc-700 dark:text-zinc-300 font-semibold">0 items</span>`);
        }
    }

    function loadConsignments() {
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_get_consignments',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
            },
            success: function(res) {
                if (res.success && res.data.consignments) {
                    renderConsignments(res.data.consignments);
                }
            }
        });
    }

    function renderConsignments(consignments) {
        consignmentsCache = consignments || [];
        const ocrSelect = $('#cora-ocr-consignment-select');
        if (ocrSelect.length) {
            let selectHtml = '<option value="">-- Select Active Consignment --</option>';
            if (consignments && consignments.length) {
                consignments.forEach(c => {
                    selectHtml += `<option value="${c.id}">${escapeHtml(c.consignment_no)} - ${escapeHtml(c.vendor_name)} (${escapeHtml(c.vehicle_no)})</option>`;
                });
            }
            ocrSelect.html(selectHtml);
        }

        const grid = $('#cora-consignments-grid');
        if (!consignments || !consignments.length) {
            grid.html(`
                <div class="col-span-full py-12 text-center select-none">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-2.5 text-zinc-400">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">No van consignments found</span>
                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">There are no active or past van allocations matching to the query.</p>
                    </div>
                </div>
            `);
            return;
        }
        let html = '';
        consignments.forEach(c => {
            const isReconciled = c.status === 'reconciled';
            const statusBadge = isReconciled
                ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">Reconciled</span>'
                : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Active Selling</span>';

            let emailBadge = '';
            if (c.email_status === 'opened') {
                emailBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-medium bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60" title="Opened ${c.email_time_ago || ''} (${c.email_open_count || 1}x)"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Email Opened ${c.email_time_ago ? '• ' + escapeHtml(c.email_time_ago) : ''}</span>`;
            } else if (c.email_status === 'sent') {
                emailBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-medium bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60" title="Sent ${c.email_time_ago || ''} • Unopened"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Email Sent • Unopened</span>`;
            } else {
                emailBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-medium bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700" title="Email not yet sent"><span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>Email Pending</span>`;
            }

            let citiesTagHtml = '';
            if (c.target_cities) {
                const cList = c.target_cities.split(',').map(x => x.trim()).filter(Boolean);
                if (cList.length) {
                    citiesTagHtml = `
                        <div class="flex items-center gap-1 flex-wrap pt-0.5">
                            ${cList.map(cty => `<span class="px-1.5 py-0.2 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200/70 dark:border-zinc-700/70 text-[9px] font-medium">${escapeHtml(cty)}</span>`).join('')}
                        </div>
                    `;
                }
            }

            html += `
                <div class="p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs space-y-2.5 sm:space-y-3 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-mono font-bold text-xs text-zinc-900 dark:text-zinc-100">${escapeHtml(c.consignment_no)}</span>
                            <div class="text-[11px] text-zinc-500">${escapeHtml(c.vendor_name)} • ${escapeHtml(c.vehicle_no)}${c.driver_email ? ' • <span class="font-mono text-[10px]">' + escapeHtml(c.driver_email) + '</span>' : ''}</div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" onclick="CoraInventory.openEditConsignmentModal(${c.id})" class="p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer" title="Edit Consignment">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" onclick="CoraInventory.openDeleteConsignmentModal(${c.id}, '${escapeHtml(c.consignment_no)}')" class="p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40 dark:hover:text-red-400 text-zinc-400 border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer" title="Delete Consignment">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Badges Row: Sales Status & Email Tracking Status -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        ${statusBadge}
                        ${emailBadge}
                    </div>

                    <div class="text-xs text-zinc-600 dark:text-zinc-300 space-y-1">
                        <div><span class="font-semibold">Route: </span>${escapeHtml(c.route_name)}</div>
                        ${c.google_maps_url ? `<a href="${escapeHtml(c.google_maps_url)}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[10px] text-zinc-600 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-100 font-medium"><span>Open Route Map</span> <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg></a>` : ''}
                        ${citiesTagHtml}
                    </div>

                    <!-- Driver Invite & Direct Access Action Buttons -->
                    <div class="p-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/70 dark:border-zinc-700/60 flex items-center justify-between gap-1 flex-wrap">
                        <div class="flex items-center gap-1 flex-wrap">
                            <button type="button" onclick="CoraInventory.copyDriverInviteLink('${escapeHtml(c.invite_link || '')}')" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-semibold border border-zinc-200/80 dark:border-zinc-700 shadow-2xs transition-colors cursor-pointer" title="Copy Direct Driver Invite & Access Link">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                <span>Copy Link</span>
                            </button>
                            <button type="button" onclick="CoraInventory.shareConsignmentWhatsApp(${c.id})" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 text-[10px] font-semibold border border-emerald-200/80 dark:border-emerald-800/60 shadow-2xs transition-colors cursor-pointer" title="Share Dispatch Assignment & Link via WhatsApp">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                <span>WhatsApp</span>
                            </button>
                            <button type="button" onclick="CoraInventory.resendConsignmentEmail(${c.id})" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-semibold border border-zinc-200/80 dark:border-zinc-700 shadow-2xs transition-colors cursor-pointer" title="Resend Dispatch Notification Email to Driver">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <span>Resend Email</span>
                            </button>
                        </div>
                        <button type="button" onclick="CoraInventory.viewAsDriver(${c.id})" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-900 text-[10px] font-bold shadow-2xs transition-colors cursor-pointer" title="Preview / Open Driver POS Terminal">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <span>Driver View</span>
                        </button>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] sm:text-[11px] text-zinc-500 font-mono">
                            <span>Sold: <strong class="text-zinc-900 dark:text-zinc-100">₹${Number(c.total_sold_val).toLocaleString('en-IN')}</strong></span>
                            <span>Dispatched: ₹${Number(c.total_dispatched_val).toLocaleString('en-IN')}</span>
                        </div>
                        <div class="w-full h-1.5 sm:h-2 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                            <div class="h-full bg-emerald-600 dark:bg-emerald-500 rounded-full transition-all" style="width: ${c.sales_progress_pct}%"></div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs">
                        <div class="text-[11px] text-zinc-500">
                            <span>Visits: <strong class="text-zinc-800 dark:text-zinc-200">${c.visits_count || 0}</strong></span>
                        </div>
                        ${!isReconciled ? `
                            <button type="button" onclick="CoraInventory.settleConsignment(${c.id})" class="px-3 py-1 rounded-lg bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold text-[11px] cursor-pointer">
                                Settle Day Return
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        grid.html(html);
    }

    function initLeafletMap() {
        const mapEl = document.getElementById('cora-inventory-map-view');
        if (!mapEl || typeof L === 'undefined') {
            return;
        }

        if (!mapInstance) {
            // Initialize at New Delhi Commercial Core
            mapInstance = L.map('cora-inventory-map-view', {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView([28.6315, 77.2167], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapInstance);
        } else {
            mapInstance.invalidateSize();
        }

        loadShopVisitsMap();
    }

    function loadShopVisitsMap() {
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_get_shop_visits',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
            },
            success: function(res) {
                if (!res.success) return;
                const visits = res.data.visits || [];

                // Clear previous markers
                if (mapMarkers && mapMarkers.length) {
                    mapMarkers.forEach(m => {
                        if (mapInstance) mapInstance.removeLayer(m);
                    });
                }
                mapMarkers = [];

                const timeline = $('#cora-map-stops-timeline');

                if (!visits.length) {
                    timeline.html(`
                        <div class="p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-dashed border-zinc-200 dark:border-zinc-700 text-center">
                            <div class="text-xs font-bold text-zinc-700 dark:text-zinc-300">No Retail Check-Ins Recorded Today</div>
                            <div class="text-[11px] text-zinc-400 mt-1">Field shop visits and spot sales will plot on the live route tracking map automatically.</div>
                        </div>
                    `);
                    return;
                }

                let timelineHtml = '';
                const bounds = [];

                visits.forEach(v => {
                    const lat = parseFloat(v.lat);
                    const lng = parseFloat(v.lng);

                    if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0 && mapInstance) {
                        bounds.push([lat, lng]);
                        const shopIcon = L.divIcon({
                            html: `<div style="background:#18181b;color:#ffffff;padding:4px 8px;border-radius:12px;font-size:10px;font-weight:bold;box-shadow:0 2px 6px rgba(0,0,0,0.3);border:1px solid #3f3f46;white-space:nowrap;">📍 ${escapeHtml(v.shop_name.substring(0, 18))}</div>`,
                            className: 'cora-custom-shop-pin',
                            iconSize: [80, 24]
                        });

                        const marker = L.marker([lat, lng], { icon: shopIcon }).addTo(mapInstance)
                            .bindPopup(`<b>${escapeHtml(v.shop_name)}</b><br>${escapeHtml(v.address || '')}<br>Order: ₹${Number(v.order_value || 0).toLocaleString('en-IN')} (${escapeHtml(v.payment_mode || 'cash').toUpperCase()})`);
                        mapMarkers.push(marker);
                    }

                    timelineHtml += `
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-zinc-700 dark:bg-zinc-300"></span>
                                <span class="font-bold text-zinc-800 dark:text-zinc-200">${escapeHtml(v.shop_name)}</span>
                                <span class="text-zinc-400">• ${escapeHtml(v.address ? v.address.substring(0, 20) : 'Spot Check-in')}</span>
                            </div>
                            <span class="font-mono font-bold text-zinc-900 dark:text-zinc-100">₹${Number(v.collected_amount || v.order_value || 0).toLocaleString('en-IN')} (${escapeHtml(v.payment_mode || 'cash').toUpperCase()})</span>
                        </div>
                    `;
                });

                timeline.html(timelineHtml);

                if (bounds.length > 0 && mapInstance) {
                    mapInstance.fitBounds(bounds, { padding: [30, 30], maxZoom: 15 });
                }
            }
        });
    }

    function handleOCRFile(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(evt) {
            $('#cora-ocr-preview-img').attr('src', evt.target.result);
            $('#cora-ocr-preview-wrap').removeClass('hidden');
            window.coraActiveOCRBase64 = evt.target.result;
        };
        reader.readAsDataURL(file);
    }

    function processOCR() {
        window.coraShowToast('Scanning invoice with Gemini Multimodal Vision...', 'info');
        $('#cora-ocr-process-btn').prop('disabled', true).text('Processing AI Vision...');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_ocr_invoice',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                image_base64: window.coraActiveOCRBase64 || '',
                consignment_id: $('#cora-ocr-consignment-select').val() || activeConsignmentId || 0
            },
            success: function(res) {
                $('#cora-ocr-process-btn').prop('disabled', false).html('<span>Run AI Vision OCR Extraction</span>');
                if (res.success && res.data.parsed_data) {
                    ocrParsedResult = res.data.parsed_data;
                    renderOCRResults(ocrParsedResult);
                    window.coraShowToast('AI extracted invoice items successfully!', 'success');
                } else {
                    window.coraShowToast(res.data || 'Failed to parse invoice.', 'error');
                }
            },
            error: function() {
                $('#cora-ocr-process-btn').prop('disabled', false).html('<span>Run AI Vision OCR Extraction</span>');
                window.coraShowToast('OCR connection failed.', 'error');
            }
        });
    }

    function renderOCRResults(data) {
        $('#cora-ocr-confidence-badge').text('Confidence: ' + (data.confidence || '98.5%'))
            .removeClass('bg-zinc-100 text-zinc-600').addClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950');
        
        let itemsHtml = `
            <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/60 dark:border-zinc-700/60">
                <div class="flex justify-between font-bold text-xs text-zinc-800 dark:text-zinc-200 mb-1">
                    <span>${escapeHtml(data.retailer_name || 'Retailer')}</span>
                    <span class="font-mono text-zinc-500">${escapeHtml(data.phone || '')}</span>
                </div>
            </div>
            <div class="space-y-2">
        `;

        (data.items || []).forEach(it => {
            itemsHtml += `
                <div class="p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-between text-xs">
                    <div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">${escapeHtml(it.name)}</div>
                        <div class="text-[11px] text-zinc-400 font-mono">Qty: ${it.quantity} × ₹${parseFloat(it.unit_price).toFixed(2)}</div>
                    </div>
                    <div class="font-mono font-bold text-zinc-900 dark:text-zinc-100">
                        ₹${parseFloat(it.line_total).toFixed(2)}
                    </div>
                </div>
            `;
        });

        itemsHtml += '</div>';
        $('#cora-ocr-results-container').html(itemsHtml);
        $('#cora-ocr-parsed-total').text('₹' + parseFloat(data.grand_total || 0).toFixed(2));
        $('#cora-ocr-action-bar').removeClass('hidden');
    }

    function confirmOCRSale() {
        if (!ocrParsedResult) return;
        window.coraShowToast('Recording spot bill and deducting stock...', 'info');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_record_spot_sale',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                consignment_id: $('#cora-ocr-consignment-select').val() || activeConsignmentId || 0,
                customer_name: ocrParsedResult.retailer_name,
                phone: ocrParsedResult.phone,
                items: JSON.stringify(ocrParsedResult.items),
                payment_mode: ocrParsedResult.payment_mode || 'cash',
                ocr_raw_data: JSON.stringify(ocrParsedResult)
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Invoice verified & stock deducted in real-time!', 'success');
                    loadCatalog();
                    loadConsignments();
                    loadVendorDashboard();
                    $('#cora-ocr-action-bar').addClass('hidden');
                    $('#cora-ocr-results-container').html('<div class="py-12 text-center text-zinc-900 dark:text-zinc-100 font-semibold text-xs">✓ Spot sale confirmed and consignment stock synchronized.</div>');
                }
            }
        });
    }

    let lastReconData = null;

    function renderReconReport(data) {
        if (!data || !data.summary) return;
        const s = data.summary;
        const consignments = data.consignments || [];
        const topSkus = data.top_skus || [];
        const insights = data.insights || [];

        // Update share preview text
        if (data.share_text) {
            $('#cora-recon-share-preview').text(data.share_text);
        }

        let html = '';

        // 1. Executive Metric Scorecards (4 Grid)
        html += `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80">
                    <div class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Total Dispatched Stock</div>
                    <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">₹${Number(s.total_dispatched || 0).toLocaleString('en-IN')}</div>
                    <div class="text-[11px] text-zinc-500 mt-1">${s.total_consignments || 0} active route vans dispatched</div>
                </div>

                <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80">
                    <div class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Spot Sales Recovery</div>
                    <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">₹${Number(s.total_sold || 0).toLocaleString('en-IN')}</div>
                    <div class="text-[11px] font-medium text-zinc-600 dark:text-zinc-400 mt-1">${s.recovery_rate || 0}% sell-through conversion</div>
                </div>

                <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80">
                    <div class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Collected Liquidity</div>
                    <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">₹${Number((s.total_cash || 0) + (s.total_upi || 0)).toLocaleString('en-IN')}</div>
                    <div class="text-[11px] font-mono text-zinc-500 mt-1">Cash ₹${Number(s.total_cash || 0).toLocaleString('en-IN')} • UPI ₹${Number(s.total_upi || 0).toLocaleString('en-IN')}</div>
                </div>

                <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80">
                    <div class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Restock / Unsold Value</div>
                    <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">₹${Number(s.total_unsold || 0).toLocaleString('en-IN')}</div>
                    <div class="text-[11px] text-zinc-500 mt-1">Variance: ₹${Number(s.total_discrepancy || 0).toLocaleString('en-IN')} • ${s.shops_visited || 0} stops</div>
                </div>
            </div>
        `;

        // 2. AI Diagnostic & Strategic Audit Findings (3 Insight Cards)
        if (insights && insights.length > 0) {
            html += `
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-zinc-500"></span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500">AI Diagnostic &amp; Loss Prevention Insights</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            `;
            insights.forEach((ins) => {
                html += `
                    <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/70 dark:border-zinc-700/70 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-[10px] font-mono font-bold uppercase px-2 py-0.5 rounded-md bg-zinc-200/80 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">${escapeHtml(ins.badge || 'AUDIT')}</span>
                            </div>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 mb-1.5">${escapeHtml(ins.title || '')}</h4>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">${escapeHtml(ins.description || '')}</p>
                        </div>
                    </div>
                `;
            });
            html += `
                    </div>
                </div>
            `;
        }

        // 3. Fleet Route Consignment Breakdown Table
        html += `
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-zinc-500"></span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500">Route Vans &amp; Consignment Settlement</h3>
                    </div>
                    <span class="text-[11px] font-mono text-zinc-400">${consignments.length} Active Fleet Allocations</span>
                </div>
                <div class="overflow-x-auto rounded-2xl border border-zinc-200/80 dark:border-zinc-800">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-zinc-50 dark:bg-zinc-800/80 border-b border-zinc-200/80 dark:border-zinc-800 text-[11px] font-bold text-zinc-500 uppercase tracking-wider">
                                <th class="py-2.5 px-3">Consignment &amp; Route</th>
                                <th class="py-2.5 px-3">Driver / Vehicle</th>
                                <th class="py-2.5 px-3 text-right">Dispatched</th>
                                <th class="py-2.5 px-3 text-right">Spot Sold</th>
                                <th class="py-2.5 px-3 text-right">Unsold Return</th>
                                <th class="py-2.5 px-3 text-right">Sell-Through</th>
                                <th class="py-2.5 px-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
        `;

        if (consignments.length === 0) {
            html += `
                <tr>
                    <td colspan="7" class="py-8 text-center text-zinc-400 text-xs">No active route consignments recorded for this date.</td>
                </tr>
            `;
        } else {
            consignments.forEach((c) => {
                const statusBadge = (c.status === 'settled' || c.status === 'closed')
                    ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">Settled</span>'
                    : '<span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950">In Field</span>';
                
                html += `
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="py-2.5 px-3">
                            <div class="font-mono font-bold text-zinc-900 dark:text-zinc-100">#${escapeHtml(c.consignment_no || '')}</div>
                            <div class="text-[11px] text-zinc-500 truncate">${escapeHtml(c.route_name || 'Direct Route')}</div>
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="font-medium text-zinc-800 dark:text-zinc-200">${escapeHtml(c.vendor_name || 'Driver')}</div>
                            <div class="text-[11px] font-mono text-zinc-400">${escapeHtml(c.vehicle_no || 'N/A')}</div>
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-medium text-zinc-900 dark:text-zinc-100">₹${Number(c.total_allocated_value || 0).toLocaleString('en-IN')}</td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-zinc-900 dark:text-zinc-100">₹${Number(c.total_sold_value || 0).toLocaleString('en-IN')}</td>
                        <td class="py-2.5 px-3 text-right font-mono text-zinc-500">₹${Number(c.total_unsold_value || 0).toLocaleString('en-IN')}</td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-zinc-800 dark:text-zinc-200">${c.sell_through_pct || 0}%</td>
                        <td class="py-2.5 px-3 text-center">${statusBadge}</td>
                    </tr>
                `;
            });
        }

        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        // 4. Top Moving SKU Velocity Table
        if (topSkus && topSkus.length > 0) {
            html += `
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-zinc-500"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500">Fast-Moving Velocity SKUs</h3>
                        </div>
                        <span class="text-[11px] font-mono text-zinc-400">Top Realized SKUs</span>
                    </div>
                    <div class="overflow-x-auto rounded-2xl border border-zinc-200/80 dark:border-zinc-800">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-zinc-50 dark:bg-zinc-800/80 border-b border-zinc-200/80 dark:border-zinc-800 text-[11px] font-bold text-zinc-500 uppercase tracking-wider">
                                    <th class="py-2 px-3">SKU Code</th>
                                    <th class="py-2 px-3">Product Name</th>
                                    <th class="py-2 px-3 text-right">Units Sold</th>
                                    <th class="py-2 px-3 text-right">Realized Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
            `;
            topSkus.forEach((sku) => {
                html += `
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="py-2 px-3 font-mono font-bold text-zinc-800 dark:text-zinc-200">${escapeHtml(sku.sku || '')}</td>
                        <td class="py-2 px-3 text-zinc-900 dark:text-zinc-100 font-medium">${escapeHtml(sku.item_name || 'Standard SKU')}</td>
                        <td class="py-2 px-3 text-right font-mono font-semibold text-zinc-800 dark:text-zinc-200">${sku.total_qty || 0} units</td>
                        <td class="py-2 px-3 text-right font-mono font-bold text-zinc-900 dark:text-zinc-100">₹${Number(sku.total_rev || 0).toLocaleString('en-IN')}</td>
                    </tr>
                `;
            });
            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }

        // 5. Loss Prevention Certification & Quick Actions Footer
        html += `
            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-zinc-500">
                    Audit Status: <span class="font-bold text-zinc-800 dark:text-zinc-200">Verified ${escapeHtml(s.date || '')}</span> • Loss Prevention Risk Index: <span class="font-bold text-zinc-800 dark:text-zinc-200">${escapeHtml(s.risk_score || 'LOW')}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="CoraInventory.openReconShareModal()" class="px-3 py-1.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-semibold cursor-pointer transition-colors flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                        <span>Share Report</span>
                    </button>
                    <button type="button" onclick="CoraInventory.exportDailyPDF()" class="px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-black text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-semibold cursor-pointer transition-colors flex items-center gap-1.5 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span>Export PDF Summary</span>
                    </button>
                </div>
            </div>
        `;

        $('#cora-recon-report-container').html(html);
    }

    function generateDailyRecon() {
        const date = $('#cora-recon-date-picker').val() || '<?php echo date("Y-m-d"); ?>';
        window.coraShowToast('Analyzing fleet dispatches, sales recovery and loss prevention metrics...', 'info');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_generate_daily_recon',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                date: date
            },
            success: function(res) {
                if (res.success && res.data) {
                    lastReconData = res.data;
                    renderReconReport(res.data);
                    window.coraShowToast('Executive supply audit and financial reconciliation complete!', 'success');
                } else {
                    window.coraShowToast('Failed to generate daily reconciliation.', 'error');
                }
            },
            error: function() {
                window.coraShowToast('Network error while running supply reconciliation.', 'error');
            }
        });
    }

    function exportDailyPDF() {
        const date = $('#cora-recon-date-picker').val() || '<?php echo date("Y-m-d"); ?>';
        window.coraShowToast('Opening print-ready Executive Audit PDF view...', 'info');
        const pdfUrl = (ajaxurl || '/wp-admin/admin-ajax.php') + '?action=cora_inventory_render_pdf_view&date=' + encodeURIComponent(date);
        window.open(pdfUrl, '_blank');
    }

    function openReconShareModal() {
        if (!lastReconData) {
            generateDailyRecon();
        }
        openStudioDrawer('#cora-recon-share-sheet', '#cora-recon-share-backdrop', '#cora-recon-share-drawer');
    }

    function closeReconShareModal() {
        closeStudioDrawer('#cora-recon-share-sheet', '#cora-recon-share-backdrop', '#cora-recon-share-drawer');
    }

    function copyReconShareText() {
        const text = (lastReconData && lastReconData.share_text) ? lastReconData.share_text : $('#cora-recon-share-preview').text();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                window.coraShowToast('Executive summary copied to clipboard!', 'success');
            }).catch(() => {
                window.coraShowToast('Failed to copy to clipboard.', 'error');
            });
        } else {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            window.coraShowToast('Executive summary copied to clipboard!', 'success');
        }
    }

    function copyReconPDFLink() {
        const date = $('#cora-recon-date-picker').val() || '<?php echo date("Y-m-d"); ?>';
        const pdfUrl = window.location.origin + (ajaxurl || '/wp-admin/admin-ajax.php') + '?action=cora_inventory_render_pdf_view&date=' + encodeURIComponent(date);
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(pdfUrl).then(() => {
                window.coraShowToast('Printable PDF Link copied to clipboard!', 'success');
            }).catch(() => {
                window.coraShowToast('Failed to copy link.', 'error');
            });
        } else {
            const textarea = document.createElement('textarea');
            textarea.value = pdfUrl;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            window.coraShowToast('Printable PDF Link copied to clipboard!', 'success');
        }
    }

    function shareReconWhatsApp() {
        const text = (lastReconData && lastReconData.share_text) ? lastReconData.share_text : $('#cora-recon-share-preview').text();
        const waUrl = 'https://wa.me/?text=' + encodeURIComponent(text);
        window.open(waUrl, '_blank');
    }

    let stagingParsedItems = [];

    function getTopbarOffset() {
        const topbar = document.getElementById('cora-global-topbar') || document.querySelector('.cora-topbar') || document.querySelector('header');
        return (topbar && topbar.offsetHeight) ? topbar.offsetHeight : (window.innerWidth < 768 ? 52 : 48);
    }

    function openStudioDrawer(sheetSel, backdropSel, drawerSel) {
        const sheetEl = document.querySelector(sheetSel);
        const backdropEl = document.querySelector(backdropSel);
        const drawerEl = document.querySelector(drawerSel);

        if (!sheetEl) {
            console.error('Cora Drawer Error: Sheet not found ->', sheetSel);
            return;
        }

        const topbarH = getTopbarOffset();

        // Lock background scroll
        if (typeof window.coraLockScroll === 'function') {
            window.coraLockScroll();
        }

        sheetEl.style.setProperty('display', 'block', 'important');
        sheetEl.style.top = topbarH + 'px';
        sheetEl.style.height = 'calc(100vh - ' + topbarH + 'px)';
        sheetEl.style.pointerEvents = 'auto';
        sheetEl.style.zIndex = '99999';
        sheetEl.classList.remove('hidden', 'pointer-events-none');

        // Force DOM reflow
        void sheetEl.offsetHeight;

        setTimeout(() => {
            if (backdropEl) {
                backdropEl.classList.remove('opacity-0');
                backdropEl.classList.add('opacity-100');
            }
            if (drawerEl) {
                drawerEl.classList.remove('translate-y-full');
                drawerEl.classList.add('translate-y-0');
                drawerEl.style.transform = 'translateY(0)';
                drawerEl.style.pointerEvents = 'auto';
            }
        }, 15);
    }

    function closeStudioDrawer(sheetSel, backdropSel, drawerSel) {
        const sheetEl = document.querySelector(sheetSel);
        const backdropEl = document.querySelector(backdropSel);
        const drawerEl = document.querySelector(drawerSel);

        if (!sheetEl) return;

        if (typeof window.coraUnlockScroll === 'function') {
            window.coraUnlockScroll();
        }

        if (backdropEl) {
            backdropEl.classList.remove('opacity-100');
            backdropEl.classList.add('opacity-0');
        }
        if (drawerEl) {
            drawerEl.classList.remove('translate-y-0');
            drawerEl.classList.add('translate-y-full');
            drawerEl.style.transform = 'translateY(100%)';
        }

        setTimeout(() => {
            sheetEl.style.setProperty('display', 'none', 'important');
            sheetEl.style.pointerEvents = 'none';
            sheetEl.classList.add('pointer-events-none', 'hidden');
        }, 300);
    }

    function openImportModal(initialTab) {
        if (initialTab) {
            switchImportTab(initialTab);
        } else {
            switchImportTab('csv');
        }
        openStudioDrawer('#cora-inv-import-sheet', '#cora-inv-import-backdrop', '#cora-inv-import-drawer');
    }

    function closeImportModal() {
        closeStudioDrawer('#cora-inv-import-sheet', '#cora-inv-import-backdrop', '#cora-inv-import-drawer');
    }

    function switchImportTab(tab) {
        if (tab === 'csv') {
            $('#cora-import-pane-csv').removeClass('hidden');
            $('#cora-import-pane-kits').addClass('hidden');
            $('#cora-import-tab-btn-csv').addClass('border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 font-bold').removeClass('border-transparent text-zinc-500 font-semibold');
            $('#cora-import-tab-btn-kits').removeClass('border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 font-bold').addClass('border-transparent text-zinc-500 font-semibold');
        } else {
            $('#cora-import-pane-csv').addClass('hidden');
            $('#cora-import-pane-kits').removeClass('hidden');
            $('#cora-import-tab-btn-kits').addClass('border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 font-bold').removeClass('border-transparent text-zinc-500 font-semibold');
            $('#cora-import-tab-btn-csv').removeClass('border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 font-bold').addClass('border-transparent text-zinc-500 font-semibold');
        }
    }

    function handleCSVFile(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(evt) {
            const content = evt.target.result;
            parseCSVContent(content);
        };
        reader.readAsText(file);
    }

    function parseCSVContent(text) {
        const lines = text.split(/\r\n|\n/).map(l => l.trim()).filter(l => l.length > 0);
        if (lines.length < 2) {
            window.coraShowToast('CSV file is empty or missing data rows.', 'error');
            return;
        }

        const headers = parseCSVLine(lines[0]);
        const items = [];

        for (let i = 1; i < lines.length; i++) {
            const values = parseCSVLine(lines[i]);
            if (!values.length || values.every(v => !v.trim())) continue;

            const rowObj = {};
            headers.forEach((h, idx) => {
                const cleanH = h.trim();
                rowObj[cleanH] = values[idx] !== undefined ? values[idx].trim() : '';
            });

            const name = rowObj['Product Name'] || rowObj['name'] || rowObj['Title'] || rowObj['product_name'] || rowObj['Item Name'] || values[2] || values[0] || '';
            if (!name) continue;

            const sku = rowObj['SKU'] || rowObj['sku'] || rowObj['Item Code'] || rowObj['item_code'] || values[0] || '';
            const category = rowObj['Category'] || rowObj['category'] || values[3] || 'notebooks';
            const wholesale = parseFloat(rowObj['Wholesale Price'] || rowObj['wholesale_price'] || rowObj['Wholesale Rate'] || rowObj['Rate'] || values[8] || 0);
            const mrp = parseFloat(rowObj['MRP'] || rowObj['mrp'] || rowObj['Retail Price'] || values[9] || (wholesale > 0 ? wholesale * 1.4 : 100));
            const stock = parseInt(rowObj['Stock Quantity'] || rowObj['stock_quantity'] || rowObj['Stock'] || rowObj['Opening Stock'] || values[10] || 500);
            const uom = rowObj['UOM'] || rowObj['uom'] || rowObj['Unit'] || rowObj['Packaging'] || values[4] || 'Pcs';
            const hsn = rowObj['HSN Code'] || rowObj['hsn_code'] || rowObj['HSN'] || values[5] || '4820';
            const gst = parseFloat(rowObj['GST Rate %'] || rowObj['gst_rate'] || rowObj['GST'] || values[6] || 12);
            const barcode = rowObj['Barcode'] || rowObj['barcode'] || rowObj['EAN'] || values[1] || '';
            const batch = rowObj['Batch No'] || rowObj['batch_no'] || rowObj['Batch'] || values[12] || '';

            items.push({
                name: name,
                sku: sku,
                category: category,
                uom: uom,
                hsn_code: hsn,
                gst_rate: gst,
                wholesale_price: wholesale,
                mrp: mrp,
                stock_quantity: stock,
                barcode: barcode,
                batch_no: batch
            });
        }

        stagingParsedItems = items;
        renderStagingGrid(items);
    }

    function parseCSVLine(line) {
        const values = [];
        let current = '';
        let inQuotes = false;
        for (let i = 0; i < line.length; i++) {
            const char = line[i];
            if (char === '"') {
                if (inQuotes && line[i + 1] === '"') {
                    current += '"';
                    i++;
                } else {
                    inQuotes = !inQuotes;
                }
            } else if ((char === ',' || char === '\t') && !inQuotes) {
                values.push(current);
                current = '';
            } else {
                current += char;
            }
        }
        values.push(current);
        return values;
    }

    function renderStagingGrid(items) {
        const wrap = $('#cora-csv-staging-wrap');
        const tbody = $('#cora-csv-staging-body');
        const badge = $('#cora-csv-parsed-badge');

        if (!items.length) {
            wrap.addClass('hidden');
            window.coraShowToast('No valid product records found in file.', 'error');
            return;
        }

        badge.text(items.length + ' ready');
        let html = '';
        const previewItems = items.slice(0, 10);

        previewItems.forEach((it) => {
            html += `
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                    <td class="py-2 px-3 font-mono font-bold text-zinc-800 dark:text-zinc-200">${escapeHtml(it.sku || 'AUTO-SKU')}</td>
                    <td class="py-2 px-3 text-zinc-900 dark:text-zinc-100 font-medium">${escapeHtml(it.name)}</td>
                    <td class="py-2 px-3 capitalize text-zinc-500">${escapeHtml(it.category.replace('_', ' '))}</td>
                    <td class="py-2 px-3 font-mono text-zinc-800 dark:text-zinc-200">₹${parseFloat(it.wholesale_price || 0).toFixed(2)}</td>
                    <td class="py-2 px-3 font-mono text-zinc-800 dark:text-zinc-200">${it.stock_quantity} ${escapeHtml(it.uom)}</td>
                </tr>
            `;
        });

        if (items.length > 10) {
            html += `
                <tr>
                    <td colspan="5" class="py-2 px-3 text-center text-zinc-400 font-mono text-[10px]">
                        + and ${items.length - 10} more rows ready for batch insert
                    </td>
                </tr>
            `;
        }

        tbody.html(html);
        wrap.removeClass('hidden');
    }

    function commitBulkImport() {
        if (!stagingParsedItems.length) {
            window.coraShowToast('No items staged for import.', 'error');
            return;
        }

        const btn = $('#cora-csv-commit-btn');
        btn.prop('disabled', true).text('Importing ' + stagingParsedItems.length + ' products...');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_bulk_import_csv',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                items: JSON.stringify(stagingParsedItems)
            },
            success: function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M5 13l4 4L19 7"></path></svg> <span>Confirm &amp; Import Products to Central Plant</span>');
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Products imported successfully!', 'success');
                    closeImportModal();
                    stagingParsedItems = [];
                    $('#cora-csv-staging-wrap').addClass('hidden');
                    $('#cora-csv-file-input').val('');
                    loadCatalog();
                } else {
                    window.coraShowToast(res.data || 'Failed to import products.', 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Confirm & Import Products to Central Plant');
                window.coraShowToast('Server connection error during import.', 'error');
            }
        });
    }

    function downloadSampleCSV() {
        const sampleHeaders = 'SKU,Barcode,Product Name,Category,UOM,HSN Code,GST Rate %,Cost Price,Wholesale Price,MRP,Stock Quantity,Low Stock Alert,Batch No,Description';
        const sampleRows = [
            'STN-PNC-101,8901234510011,"Deluxe Dark Graphite Pencil 2B (Pack of 10)",school_supplies,"Pack of 10",9609,12.00,30.00,45.00,70.00,4500,400,BAT-2026-P01,"Bonded lead for student writing"',
            'STN-ERS-201,8901234510035,"Dust-Free White Polymer Eraser (Box of 30)",school_supplies,"Box of 30",4016,18.00,60.00,90.00,150.00,3600,300,BAT-2026-E01,"Non-toxic dust-free eraser"',
            'STN-SHP-301,8901234510042,"Dual-Hole Precision Metal Sharpener (Display of 20)",school_supplies,"Box of 20",8214,18.00,75.00,110.00,180.00,2800,250,BAT-2026-S01,"High carbon steel blades"',
            'STN-NB-101,8901234500012,"Classic Hardbound Ruled Register (200 Pgs)",notebooks,"Pack of 6",4820,12.00,210.00,320.00,450.00,1450,150,BAT-2026-N01,"80 GSM durable sewn register"',
            'STN-NB-102,8901234500029,"A5 Executive Spiral Project Journal (160 Pgs)",notebooks,"Pack of 10",4820,12.00,340.00,520.00,750.00,980,100,BAT-2026-N02,"Frost cover project journal"',
            'STN-PPR-201,8901234500036,"A4 Ultra-White Copier Paper Ream (75 GSM / 500 Sheets)",paper_reams,"Box of 5 Reams",4802,12.00,950.00,1280.00,1650.00,2400,300,BAT-2026-P01,"Jam-free laser copier paper"',
            'STN-PEN-301,8901234500043,"Smoothflow Retractable Gel Pen 0.7mm (Box of 20)",writing_instruments,"Box of 20",9608,18.00,120.00,190.00,300.00,3200,250,BAT-2026-W01,"Waterproof Japanese ink"',
            'STN-PEN-302,8901234500050,"Dry-Wipe Magnetic Whiteboard Markers (Assorted 4-Pack)",writing_instruments,"Pack of 12 Sets",9608,18.00,280.00,440.00,660.00,1100,120,BAT-2026-W02,"Low odor bullet tip markers"',
            'STN-ADH-601,8901234500081,"Quick-Bond Glue Stick 15g (Display Dispenser)",adhesives,"Box of 24 Units",3506,18.00,180.00,280.00,480.00,1850,200,BAT-2026-G01,"Washable paper glue stick"',
            'STN-OFF-401,8901234500067,"Heavy-Duty Metal Stapler + 24/6 Pin Box Set",office_supplies,"Box of 10 Sets",8305,18.00,450.00,680.00,990.00,640,80,BAT-2026-O01,"30-sheet binding capacity"'
        ];

        const csvContent = sampleHeaders + '\n' + sampleRows.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'stationery_catalog_sample_template.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.coraShowToast('Sample Stationery CSV downloaded.', 'success');
    }

    let currentExportScope = 'filtered'; // 'filtered' or 'selected'

    function openExportModal(scope) {
        currentExportScope = (scope === 'selected' && selectedProductIds.size > 0) ? 'selected' : 'filtered';
        $('#cora-export-filtered-count').text(catalogCache.length);
        $('#cora-export-selected-count').text(selectedProductIds.size);
        setExportScope(currentExportScope);
        openStudioDrawer('#cora-inv-export-sheet', '#cora-inv-export-backdrop', '#cora-inv-export-drawer');
    }

    function closeExportModal() {
        closeStudioDrawer('#cora-inv-export-sheet', '#cora-inv-export-backdrop', '#cora-inv-export-drawer');
    }

    function setExportScope(scope) {
        if (scope === 'selected' && selectedProductIds.size === 0) {
            if (window.coraShowToast) window.coraShowToast('No products selected yet. Displaying current filtered list.', 'info');
            scope = 'filtered';
        }
        currentExportScope = scope;
        if (scope === 'filtered') {
            $('#cora-export-scope-btn-filtered').addClass('bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs font-bold').removeClass('text-zinc-600 dark:text-zinc-400 font-medium');
            $('#cora-export-scope-btn-selected').removeClass('bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs font-bold').addClass('text-zinc-600 dark:text-zinc-400 font-medium');
            $('#cora-export-scope-desc').text('Exporting ' + catalogCache.length + ' visible items according to current filters & search.');
        } else {
            $('#cora-export-scope-btn-selected').addClass('bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs font-bold').removeClass('text-zinc-600 dark:text-zinc-400 font-medium');
            $('#cora-export-scope-btn-filtered').removeClass('bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs font-bold').addClass('text-zinc-600 dark:text-zinc-400 font-medium');
            $('#cora-export-scope-desc').text('Exporting only ' + selectedProductIds.size + ' manually selected items.');
        }
    }

    function getExportParams() {
        const params = {
            security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>'
        };
        if (currentExportScope === 'selected' && selectedProductIds.size > 0) {
            params.ids = Array.from(selectedProductIds).join(',');
        } else {
            const search = $('#cora-inv-search-input').val();
            const cat = $('#cora-inv-filter-category').val();
            const pricingType = currentPricingFilter || 'all';
            const sort = $('#cora-inv-filter-sort').val();
            if (search) params.search = search;
            if (cat) params.category = cat;
            if (pricingType && pricingType !== 'all') params.pricing_type = pricingType;
            if (sort) params.sort_order = sort;
        }
        return params;
    }

    function triggerExportCSV() {
        const params = getExportParams();
        params.action = 'cora_inventory_export_csv';
        if (window.coraShowToast) window.coraShowToast('Generating Excel / CSV spreadsheet export...', 'info');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: params,
            success: function(res) {
                if (res.success && res.data.csv_content) {
                    const blob = new Blob([res.data.csv_content], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    const url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', res.data.filename || 'stationery_inventory_export.csv');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    if (window.coraShowToast) window.coraShowToast('Exported ' + res.data.total_count + ' items to CSV.', 'success');
                    closeExportModal();
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data?.message || 'Failed to export catalog.', 'error');
                }
            },
            error: function() {
                if (window.coraShowToast) window.coraShowToast('Network error while exporting CSV.', 'error');
            }
        });
    }

    function triggerExportPDF() {
        const params = getExportParams();
        params.action = 'cora_inventory_render_catalog_pdf';
        
        const queryStr = $.param(params);
        const pdfUrl = (ajaxurl || '/wp-admin/admin-ajax.php') + '?' + queryStr;
        
        if (window.coraShowToast) window.coraShowToast('Opening horizontal sheet printout...', 'info');
        window.open(pdfUrl, '_blank');
        closeExportModal();
    }

    function exportCatalogCSV() {
        openExportModal();
    }

    function loadStarterKit(kitKey) {
        window.coraShowToast('Injecting preset stationery category...', 'info');
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_load_starter_kit',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                kit_key: kitKey
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Starter kit loaded successfully!', 'success');
                    closeImportModal();
                    loadCatalog();
                } else {
                    window.coraShowToast(res.data || 'Failed to load starter kit.', 'error');
                }
            }
        });
    }

    let currentProdStep = 1;

    function switchProductStep(step) {
        currentProdStep = step;
        // Panes
        $('#cora-prod-pane-1').toggleClass('hidden', step !== 1);
        $('#cora-prod-pane-2').toggleClass('hidden', step !== 2);
        $('#cora-prod-pane-3').toggleClass('hidden', step !== 3);

        // Subtitle update
        const subtitles = {
            1: 'Step 1 of 3: Product Identity & Visual Media',
            2: 'Step 2 of 3: Pricing & GST Margin Calculation',
            3: 'Step 3 of 3: Factory Stock & Warehouse Logistics'
        };
        $('#cora-inv-prod-step-subtitle').text(subtitles[step] || 'Step ' + step + ' of 3');

        // Stepper Buttons - robust is-active class management
        for (let i = 1; i <= 3; i++) {
            const btn = $('#cora-prod-step-btn-' + i);
            const numCircle = btn.find('.step-num');
            if (i === step) {
                btn.addClass('is-active');
                numCircle.addClass('bg-white text-zinc-950 dark:bg-zinc-950 dark:text-zinc-100')
                         .removeClass('bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300');
            } else {
                btn.removeClass('is-active');
                numCircle.removeClass('bg-white text-zinc-950 dark:bg-zinc-950 dark:text-zinc-100')
                         .addClass('bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300');
            }
        }

        // Stepper Navigation Buttons
        if (step === 1) {
            $('#cora-prod-prev-btn').addClass('hidden');
            $('#cora-prod-next-btn').removeClass('hidden');
            $('#cora-prod-save-btn').addClass('hidden');
        } else if (step === 2) {
            $('#cora-prod-prev-btn').removeClass('hidden');
            $('#cora-prod-next-btn').removeClass('hidden');
            $('#cora-prod-save-btn').addClass('hidden');
            recalcProductPricingMargins();
        } else {
            $('#cora-prod-prev-btn').removeClass('hidden');
            $('#cora-prod-next-btn').addClass('hidden');
            $('#cora-prod-save-btn').removeClass('hidden');
        }
    }

    function prevProductStep() {
        if (currentProdStep > 1) {
            switchProductStep(currentProdStep - 1);
        }
    }

    function nextProductStep() {
        if (currentProdStep === 1) {
            const name = ($('#cora-prod-name').val() || '').trim();
            const sku = ($('#cora-prod-sku').val() || '').trim();
            if (!name) {
                window.coraShowToast('Please enter a product title.', 'error');
                $('#cora-prod-name').focus();
                return;
            }
            if (!sku) {
                window.coraShowToast('Please enter or generate an SKU code.', 'error');
                $('#cora-prod-sku').focus();
                return;
            }
            switchProductStep(2);
        } else if (currentProdStep === 2) {
            const ws = parseFloat($('#cora-prod-ws').val()) || 0;
            const mrp = parseFloat($('#cora-prod-mrp').val()) || 0;
            if (ws <= 0) {
                window.coraShowToast('Please enter a valid wholesale rate.', 'error');
                $('#cora-prod-ws').focus();
                return;
            }
            if (mrp <= 0) {
                window.coraShowToast('Please enter a valid MRP.', 'error');
                $('#cora-prod-mrp').focus();
                return;
            }
            switchProductStep(3);
        }
    }

    function autoGenerateSKU() {
        let cat = $('#cora-prod-cat').val() || 'STN';
        if (cat === '__custom__') {
            cat = ($('#cora-prod-custom-cat').val() || 'CST').trim();
        }
        const prefixMap = {
            'notebooks': 'NB',
            'school_supplies': 'SCH',
            'writing_instruments': 'PEN',
            'paper_reams': 'PPR',
            'office_supplies': 'OFF',
            'adhesives': 'GLU',
            'art_supplies': 'ART',
            'art_kits': 'ART',
            'files_folders': 'FLD'
        };
        let pfx = prefixMap[cat];
        if (!pfx) {
            pfx = cat.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase() || 'STN';
        }
        const randomNum = Math.floor(100 + Math.random() * 900);
        $('#cora-prod-sku').val('STN-' + pfx + '-' + randomNum);
    }

    function onCategoryChange() {
        const cat = $('#cora-prod-cat').val();
        if (cat === '__custom__') {
            $('#cora-prod-custom-cat-wrap').removeClass('hidden');
            $('#cora-prod-custom-cat').focus();
        } else {
            $('#cora-prod-custom-cat-wrap').addClass('hidden');
        }

        const currentSku = ($('#cora-prod-sku').val() || '').trim();
        if (!currentSku || currentSku.startsWith('STN-')) {
            autoGenerateSKU();
        }
    }

    function recalcProductPricingMargins() {
        const ws = parseFloat($('#cora-prod-ws').val()) || 0;
        const mrp = parseFloat($('#cora-prod-mrp').val()) || 0;
        const cost = parseFloat($('#cora-prod-cost').val()) || 0;
        const gstRate = parseFloat($('#cora-prod-gst').val()) || 12;

        // Factory Margin = Wholesale - Cost
        const factoryMargin = ws > 0 && cost > 0 ? (ws - cost) : 0;
        const factoryMarginPct = cost > 0 ? ((factoryMargin / cost) * 100).toFixed(1) : (ws > 0 ? '100' : '0');
        $('#cora-calc-factory-margin').text((factoryMargin >= 0 ? '+₹' : '-₹') + Math.abs(factoryMargin).toFixed(2));
        $('#cora-calc-factory-pct').text(factoryMarginPct + '% markup on cost');

        // Retailer Spread = MRP - Wholesale
        const retailerSpread = mrp > 0 && ws > 0 ? (mrp - ws) : 0;
        const retailerSpreadPct = ws > 0 ? ((retailerSpread / ws) * 100).toFixed(1) : '0';
        $('#cora-calc-retailer-spread').text('+₹' + retailerSpread.toFixed(2));
        $('#cora-calc-retailer-pct').text(retailerSpreadPct + '% dealer margin');

        // Base Excl. GST
        const baseExcl = ws > 0 ? (ws / (1 + (gstRate / 100))) : 0;
        $('#cora-calc-base-excl').text('₹' + baseExcl.toFixed(2));

        // GST Component
        const gstAmt = ws > 0 ? (ws - baseExcl) : 0;
        $('#cora-calc-gst-amt').text('₹' + gstAmt.toFixed(2) + ' / unit');
        $('#cora-calc-gst-rate-label').text(gstRate + '% GST slab');
    }

    function onImageURLInput(url) {
        const cleanUrl = (url || '').trim();
        $('#cora-prod-image-url').val(cleanUrl);
        if (cleanUrl) {
            $('#cora-prod-img-preview').attr('src', cleanUrl).removeClass('hidden');
            $('#cora-prod-img-placeholder').addClass('hidden');
            $('#cora-prod-img-remove-btn').removeClass('hidden');
        } else {
            removeProductImage();
        }
    }

    function removeProductImage() {
        $('#cora-prod-image-url').val('');
        $('#cora-prod-img-url-input').val('');
        $('#cora-prod-img-preview').attr('src', '').addClass('hidden');
        $('#cora-prod-img-placeholder').removeClass('hidden');
        $('#cora-prod-img-remove-btn').addClass('hidden');
        $('#cora-prod-img-file-input').val('');
    }

    function handleProductImageUpload(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            window.coraShowToast('Image size exceeds 2MB limit.', 'error');
            event.target.value = '';
            return;
        }

        const formData = new FormData();
        formData.append('action', 'cora_inventory_upload_product_image');
        formData.append('security', window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>');
        formData.append('image', file);
        formData.append('image_file', file);

        const uploadText = $('#cora-prod-img-upload-btn-text');
        uploadText.text('Uploading...');
        window.coraShowToast('Uploading product image...', 'info');

        $.ajax({
            url: (typeof ajaxurl !== 'undefined' && ajaxurl) ? ajaxurl : '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                uploadText.text('Upload Photo');
                $('#cora-prod-img-file-input').val('');
                if (res && res.success && res.data && res.data.image_url) {
                    onImageURLInput(res.data.image_url);
                    $('#cora-prod-img-url-input').val(res.data.image_url);
                    window.coraShowToast('Image uploaded successfully!', 'success');
                } else {
                    const msg = (res && res.data && typeof res.data === 'string') ? res.data : (res && res.data && res.data.message ? res.data.message : 'Failed to upload image.');
                    window.coraShowToast(msg, 'error');
                }
            },
            error: function(xhr) {
                uploadText.text('Upload Photo');
                $('#cora-prod-img-file-input').val('');
                let errMsg = 'Server error while uploading image.';
                if (xhr.responseJSON && xhr.responseJSON.data) {
                    errMsg = typeof xhr.responseJSON.data === 'string' ? xhr.responseJSON.data : (xhr.responseJSON.data.message || errMsg);
                }
                window.coraShowToast(errMsg, 'error');
            }
        });
    }

    function onPricingTypeChange(clearValues) {
        const type = $('#cora-prod-pricing-type').val() || 'unit_based';
        if (type === 'weight_based') {
            $('#cora-prod-weight-wrap').removeClass('hidden');
            $('#cora-prod-pages-wrap').addClass('hidden');
            if (clearValues) {
                $('#cora-prod-pages').val('');
            }
            $('#cora-prod-uom').val('Pcs');
            onWeightGramsInput();
        } else {
            $('#cora-prod-weight-wrap').addClass('hidden');
            $('#cora-prod-pages-wrap').removeClass('hidden');
            if (clearValues) {
                $('#cora-prod-weight').val('');
                $('#cora-prod-weight-formula-note').text('');
            }
        }
    }

    function onWeightGramsInput() {
        const type = $('#cora-prod-pricing-type').val();
        if (type !== 'weight_based') return;
        const grams = parseFloat($('#cora-prod-weight').val()) || 0;
        if (grams > 0) {
            const calculatedWholesale = (grams / 1000) * 401.25;
            const roundedWs = Math.round(calculatedWholesale * 100) / 100;
            const currentWs = parseFloat($('#cora-prod-ws').val()) || 0;
            if (!currentWs || $('#cora-prod-weight').is(':focus')) {
                $('#cora-prod-ws').val(roundedWs.toFixed(2));
                if (!$('#cora-prod-mrp').val() || parseFloat($('#cora-prod-mrp').val()) <= roundedWs) {
                    $('#cora-prod-mrp').val((Math.ceil(roundedWs * 1.3 / 10) * 10).toFixed(2));
                }
                recalcProductPricingMargins();
            }
            const kg = (grams / 1000).toFixed(2);
            $('#cora-prod-weight-formula-note').text(kg + ' Kg × ₹401.25/Kg = ₹' + roundedWs.toFixed(2));
        } else {
            $('#cora-prod-weight-formula-note').text('');
        }
    }

    function openProductModal(id, stepNumber) {
        switchProductStep(stepNumber || 1);

        if (id && parseInt(id) > 0) {
            const p = catalogCache.find(x => parseInt(x.id) === parseInt(id));
            if (p) {
                $('#cora-inv-prod-title').text('Edit Stationery SKU: ' + (p.sku || ''));
                $('#cora-prod-id').val(p.id);
                $('#cora-prod-sku').val(p.sku);
                $('#cora-prod-barcode').val(p.barcode || '');
                $('#cora-prod-name').val(p.name);
                $('#cora-prod-pricing-type').val(p.pricing_type || 'unit_based');
                $('#cora-prod-weight').val(p.unit_weight_grams || '');
                $('#cora-prod-pages').val(p.pages_count || '');
                onPricingTypeChange(false);

                // Dynamic Category option selection
                const catVal = p.category || 'notebooks';
                if (catVal && !$('#cora-prod-cat option[value="' + catVal + '"]').length) {
                    $('#cora-prod-cat option[value="__custom__"]').before(`<option value="${escapeHtml(catVal)}">${escapeHtml(formatCategoryName(catVal))}</option>`);
                }
                $('#cora-prod-cat').val(catVal);
                $('#cora-prod-custom-cat-wrap').addClass('hidden');
                $('#cora-prod-custom-cat').val('');

                $('#cora-prod-uom').val(p.uom || 'Pcs');
                $('#cora-prod-ws').val(p.wholesale_price || p.wholesale_rate || '');
                $('#cora-prod-mrp').val(p.mrp || '');
                $('#cora-prod-cost').val(p.cost_price || '');
                $('#cora-prod-hsn').val(p.hsn_code || '4820');
                $('#cora-prod-gst').val(p.gst_rate || 18);
                $('#cora-prod-stock').val(p.stock_quantity || 0);
                $('#cora-prod-threshold').val(p.low_stock_threshold || 50);
                $('#cora-prod-batch').val(p.batch_no || '');
                $('#cora-prod-bin').val(p.bin_location || '');
                $('#cora-prod-desc').val(p.description || '');

                if (p.image_url) {
                    onImageURLInput(p.image_url);
                    $('#cora-prod-img-url-input').val(p.image_url);
                } else {
                    removeProductImage();
                }
                recalcProductPricingMargins();
            }
        } else {
            $('#cora-inv-prod-title').text('Add Stationery SKU');
            $('#cora-prod-id').val(0);
            if ($('#cora-inv-prod-form').length) {
                $('#cora-inv-prod-form')[0].reset();
            }
            $('#cora-prod-pricing-type').val('unit_based');
            $('#cora-prod-weight').val('');
            $('#cora-prod-pages').val('');
            $('#cora-prod-custom-cat-wrap').addClass('hidden');
            $('#cora-prod-custom-cat').val('');
            onPricingTypeChange(true);
            $('#cora-prod-stock').val(500);
            $('#cora-prod-threshold').val(50);
            $('#cora-prod-hsn').val('4820');
            $('#cora-prod-gst').val(18);
            removeProductImage();
            autoGenerateSKU();
            recalcProductPricingMargins();
        }

        openStudioDrawer('#cora-inv-product-sheet', '#cora-inv-product-backdrop', '#cora-inv-product-drawer');
    }

    function closeProductModal() {
        closeStudioDrawer('#cora-inv-product-sheet', '#cora-inv-product-backdrop', '#cora-inv-product-drawer');
    }

    function saveProduct(e) {
        e.preventDefault();
        
        let prodCategory = $('#cora-prod-cat').val();
        if (prodCategory === '__custom__') {
            const customVal = ($('#cora-prod-custom-cat').val() || '').trim();
            if (!customVal) {
                window.coraShowToast('Please enter a custom category name.', 'error');
                $('#cora-prod-custom-cat').focus();
                return;
            }
            prodCategory = customVal.toLowerCase().replace(/[^a-z0-9_]/g, '_');
        }

        window.coraShowToast('Saving stationery product...', 'info');
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_save_product',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                id: $('#cora-prod-id').val(),
                sku: $('#cora-prod-sku').val(),
                barcode: $('#cora-prod-barcode').val(),
                name: $('#cora-prod-name').val(),
                pricing_type: $('#cora-prod-pricing-type').val() || 'unit_based',
                unit_weight_grams: $('#cora-prod-weight').val() || 0,
                pages_count: $('#cora-prod-pages').val() || 0,
                category: prodCategory || 'notebooks',
                uom: $('#cora-prod-uom').val(),
                wholesale_price: $('#cora-prod-ws').val(),
                mrp: $('#cora-prod-mrp').val(),
                cost_price: $('#cora-prod-cost').val(),
                hsn_code: $('#cora-prod-hsn').val(),
                gst_rate: $('#cora-prod-gst').val(),
                stock_quantity: $('#cora-prod-stock').val(),
                low_stock_threshold: $('#cora-prod-threshold').val(),
                batch_no: $('#cora-prod-batch').val(),
                bin_location: $('#cora-prod-bin').val(),
                description: $('#cora-prod-desc').val(),
                image_url: $('#cora-prod-image-url').val()
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Product saved successfully!', 'success');
                    closeProductModal();
                    loadCatalog();
                } else {
                    window.coraShowToast(res.data || 'Failed to save product.', 'error');
                }
            }
        });
    }

    function quickAdjustStock(id, name) {
        openProductModal(id, 3);
    }

    function openDeleteModal(id) {
        let p = catalogCache.find(x => parseInt(x.id) === parseInt(id));
        if (!p) {
            p = { id: id, name: 'Selected SKU', sku: 'STN-' + id };
        }

        $('#cora-del-prod-id').val(p.id);
        $('#cora-del-prod-name').text(p.name);
        $('#cora-del-prod-sku').text(p.sku);

        const sheet = $('#cora-inv-delete-sheet');
        sheet.removeClass('hidden pointer-events-none').css({
            'display': 'block',
            'pointer-events': 'auto'
        });
        setTimeout(() => {
            $('#cora-inv-delete-backdrop').removeClass('opacity-0').addClass('opacity-100');
            $('#cora-inv-delete-drawer').removeClass('translate-y-full').addClass('translate-y-0');
        }, 15);
    }

    function closeDeleteModal() {
        $('#cora-inv-delete-drawer').removeClass('translate-y-0').addClass('translate-y-full');
        $('#cora-inv-delete-backdrop').removeClass('opacity-100').addClass('opacity-0');
        setTimeout(() => {
            $('#cora-inv-delete-sheet').addClass('pointer-events-none hidden').css({
                'display': 'none',
                'pointer-events': 'none'
            });
        }, 300);
    }

    function confirmDeleteProduct() {
        const id = $('#cora-del-prod-id').val();
        if (!id || parseInt(id) <= 0) return;

        const btn = $('#cora-del-confirm-btn');
        btn.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_delete_product',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                id: id
            },
            success: function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Confirm Delete SKU</span>');
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Product deleted from catalog.', 'success');
                    closeDeleteModal();
                    loadCatalog();
                } else {
                    window.coraShowToast(res.data || 'Failed to delete product.', 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Confirm Delete SKU');
                window.coraShowToast('Server error while deleting product.', 'error');
            }
        });
    }

    let consignmentAllocations = [];
    let multiPickerSelections = {};

    function openConsignmentModal() {
        if (!consignmentAllocations || !consignmentAllocations.length) {
            initTop5ConsignmentAllocations();
        } else {
            renderConsignmentAllocList();
        }
        $('#cora-csn-sku-search').val('');
        $('#cora-csn-search-results').addClass('hidden').empty();
        openStudioDrawer('#cora-inv-consignment-sheet', '#cora-inv-consignment-backdrop', '#cora-inv-consignment-drawer');
    }

    function closeConsignmentModal() {
        closeStudioDrawer('#cora-inv-consignment-sheet', '#cora-inv-consignment-backdrop', '#cora-inv-consignment-drawer');
    }

    function initTop5ConsignmentAllocations() {
        consignmentAllocations = [];
        if (catalogCache && catalogCache.length) {
            let candidates = catalogCache.filter(p => parseInt(p.stock_quantity || 0) > 0);
            if (!candidates.length) {
                candidates = catalogCache.slice(0, 5);
            } else {
                candidates = candidates.slice(0, 5);
            }
            candidates.forEach(p => {
                consignmentAllocations.push({
                    product_id: parseInt(p.id),
                    name: p.name || 'Stationery Item',
                    sku: p.sku || 'SKU',
                    wholesale_price: parseFloat(p.wholesale_price || p.wholesale_rate || 0),
                    stock_quantity: parseInt(p.stock_quantity || 0),
                    image_url: p.image_url || '',
                    quantity: 1
                });
            });
        }
        renderConsignmentAllocList();
    }

    function suggestTop5Products() {
        initTop5ConsignmentAllocations();
        window.coraShowToast('Top 5 fast-selling products loaded into allocation.', 'success');
    }

    function openMultiProductPicker() {
        multiPickerSelections = {};
        // Pre-populate with current allocations
        consignmentAllocations.forEach(item => {
            multiPickerSelections[item.product_id] = item.quantity;
        });

        $('#cora-multi-search-input').val('');
        renderMultiPickerList('');
        $('#cora-csn-multi-modal').removeClass('hidden').css('display', 'flex');
    }

    function closeMultiProductPicker() {
        $('#cora-csn-multi-modal').addClass('hidden').css('display', 'none');
    }

    function renderMultiPickerList(filter) {
        const q = (filter || '').trim().toLowerCase();
        const container = $('#cora-multi-picker-list');
        const countBadge = $('#cora-multi-selected-badge');

        const items = (catalogCache || []).filter(p => {
            if (!q) return true;
            const name = (p.name || '').toLowerCase();
            const sku = (p.sku || '').toLowerCase();
            const cat = (p.category || '').toLowerCase();
            return name.includes(q) || sku.includes(q) || cat.includes(q);
        });

        const selectedPids = Object.keys(multiPickerSelections).map(Number);
        countBadge.text(selectedPids.length + ' products selected');

        if (!items.length) {
            container.html(`
                <div class="py-12 text-center text-xs text-zinc-400">
                    No products found matching "${escapeHtml(filter)}".
                </div>
            `);
            return;
        }

        let html = '';
        items.forEach(p => {
            const pid = parseInt(p.id);
            const isSelected = !!multiPickerSelections[pid];
            const qty = multiPickerSelections[pid] || 1;
            const stock = parseInt(p.stock_quantity || 0);
            const price = parseFloat(p.wholesale_price || p.wholesale_rate || 0);
            const thumbHtml = p.image_url 
                ? `<img src="${escapeHtml(p.image_url)}" class="w-9 h-9 rounded-lg object-cover border border-zinc-200 dark:border-zinc-700 shrink-0">`
                : `<div class="w-9 h-9 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-bold text-[10px] text-zinc-700 dark:text-zinc-300 shrink-0">${(p.name || 'P').substring(0, 2).toUpperCase()}</div>`;

            html += `
                <div class="flex items-center justify-between p-3 rounded-xl border transition-all ${isSelected ? 'bg-zinc-50 dark:bg-zinc-800/60 border-zinc-900 dark:border-zinc-100 shadow-2xs' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700'}">
                    <label class="flex items-center gap-3 min-w-0 flex-1 cursor-pointer select-none">
                        <input type="checkbox" ${isSelected ? 'checked' : ''} onchange="CoraInventory.onMultiPickerCheckboxToggle(${pid}, this.checked)" class="w-4 h-4 rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-0 cursor-pointer accent-zinc-900 dark:accent-zinc-100">
                        ${thumbHtml}
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100 leading-tight truncate">${escapeHtml(p.name)}</div>
                            <div class="text-[11px] font-mono text-zinc-400 mt-0.5 truncate">${escapeHtml(p.sku)} • Stock: <span class="font-semibold ${stock > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'}">${stock}</span> • ₹${price.toFixed(2)}/unit</div>
                        </div>
                    </label>
                    <div class="flex items-center gap-1.5 shrink-0 ml-3">
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase">Qty</span>
                        <input type="number" min="1" max="${stock > 0 ? stock : 9999}" value="${qty}" ${!isSelected ? 'disabled' : ''} onchange="CoraInventory.onMultiQtyChange(${pid}, this.value)" oninput="CoraInventory.onMultiQtyChange(${pid}, this.value)" class="w-16 px-2 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-right font-mono text-xs font-bold text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 disabled:opacity-30">
                    </div>
                </div>
            `;
        });
        container.html(html);
    }

    function filterMultiPicker(query) {
        renderMultiPickerList(query);
    }

    function toggleMultiPickerSelectAll(select) {
        if (select) {
            (catalogCache || []).forEach(p => {
                const pid = parseInt(p.id);
                if (!multiPickerSelections[pid]) {
                    multiPickerSelections[pid] = 1;
                }
            });
        } else {
            multiPickerSelections = {};
        }
        renderMultiPickerList($('#cora-multi-search-input').val());
    }

    function onMultiPickerCheckboxToggle(productId, isChecked) {
        const pid = parseInt(productId);
        if (isChecked) {
            multiPickerSelections[pid] = multiPickerSelections[pid] || 1;
        } else {
            delete multiPickerSelections[pid];
        }
        $('#cora-multi-selected-badge').text(Object.keys(multiPickerSelections).length + ' products selected');
        renderMultiPickerList($('#cora-multi-search-input').val());
    }

    function onMultiQtyChange(productId, qty) {
        const pid = parseInt(productId);
        let parsed = parseInt(qty);
        if (isNaN(parsed) || parsed < 1) parsed = 1;
        if (multiPickerSelections[pid]) {
            multiPickerSelections[pid] = parsed;
        }
    }

    function applyMultiProductSelection() {
        const newAllocations = [];
        const selectedPids = Object.keys(multiPickerSelections).map(Number);

        if (!selectedPids.length) {
            window.coraShowToast('No products selected.', 'info');
            return;
        }

        selectedPids.forEach(pid => {
            const p = (catalogCache || []).find(x => parseInt(x.id) === pid);
            if (p) {
                newAllocations.push({
                    product_id: pid,
                    name: p.name || 'Product',
                    sku: p.sku || 'SKU',
                    wholesale_price: parseFloat(p.wholesale_price || p.wholesale_rate || 0),
                    stock_quantity: parseInt(p.stock_quantity || 0),
                    image_url: p.image_url || '',
                    quantity: multiPickerSelections[pid] || 1
                });
            }
        });

        consignmentAllocations = newAllocations;
        renderConsignmentAllocList();
        closeMultiProductPicker();
        window.coraShowToast(`Loaded ${consignmentAllocations.length} product(s) into dispatch van.`, 'success');
    }

    function stepAllocQty(productId, delta) {
        const pid = parseInt(productId);
        const item = consignmentAllocations.find(it => it.product_id === pid);
        if (!item) return;
        const newQty = Math.max(1, (item.quantity || 1) + delta);
        onAllocQtyChange(pid, newQty);
        renderConsignmentAllocList();
    }

    function clearAllAllocations() {
        if (!consignmentAllocations.length) return;
        consignmentAllocations = [];
        renderConsignmentAllocList();
        window.coraShowToast('Cleared all product allocations.', 'info');
    }

    function renderConsignmentAllocList() {
        const container = $('#cora-csn-alloc-list');
        if (!consignmentAllocations.length) {
            container.html(`
                <div class="text-zinc-400 dark:text-zinc-500 py-8 text-center text-xs">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon></svg>
                    </div>
                    <div class="font-bold text-zinc-700 dark:text-zinc-300">No items currently allocated</div>
                    <div class="text-[11px] text-zinc-400 mt-0.5">Use Top 5 Suggestion or Browse Catalog to add products.</div>
                </div>
            `);
            recalcConsignmentVal();
            return;
        }

        let html = '';
        consignmentAllocations.forEach((item) => {
            const lineTotal = (item.quantity * item.wholesale_price);
            const thumbHtml = item.image_url 
                ? `<img src="${escapeHtml(item.image_url)}" class="w-10 h-10 rounded-xl object-cover border border-zinc-200 dark:border-zinc-700 shrink-0 shadow-2xs">`
                : `<div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-bold text-xs text-zinc-700 dark:text-zinc-300 shrink-0 shadow-2xs">${(item.name || 'P').substring(0, 2).toUpperCase()}</div>`;

            html += `
                <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-700/80 text-xs hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors gap-3">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        ${thumbHtml}
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 leading-tight truncate">${escapeHtml(item.name)}</div>
                            <div class="text-[11px] font-mono text-zinc-400 mt-0.5 truncate">${escapeHtml(item.sku)} • Stock: <span class="font-semibold text-zinc-700 dark:text-zinc-300">${item.stock_quantity}</span> • ₹${item.wholesale_price.toFixed(2)}/unit</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="text-right hidden sm:block mr-1">
                            <div class="text-[10px] text-zinc-400 uppercase font-semibold">Subtotal</div>
                            <div class="font-mono font-bold text-zinc-900 dark:text-zinc-100 text-xs" id="cora-csn-subtotal-${item.product_id}">₹${lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                        </div>
                        <div class="flex items-center rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-2xs">
                            <button type="button" onclick="CoraInventory.stepAllocQty(${item.product_id}, -1)" class="px-2 py-1.5 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer font-bold text-xs">－</button>
                            <input type="number" min="1" value="${item.quantity}" onchange="CoraInventory.onAllocQtyChange(${item.product_id}, this.value)" oninput="CoraInventory.onAllocQtyChange(${item.product_id}, this.value)" class="w-12 py-1.5 text-center font-mono text-xs font-bold text-zinc-900 dark:text-zinc-100 outline-none bg-transparent">
                            <button type="button" onclick="CoraInventory.stepAllocQty(${item.product_id}, 1)" class="px-2 py-1.5 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer font-bold text-xs">＋</button>
                        </div>
                        <button type="button" onclick="CoraInventory.removeAllocItem(${item.product_id})" class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer" title="Remove from Van">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                </div>
            `;
        });
        container.html(html);
        recalcConsignmentVal();
    }

    function searchSKUsToAllocate(query) {
        const q = (query || '').trim().toLowerCase();
        const resultsBox = $('#cora-csn-search-results');
        if (!q) {
            resultsBox.addClass('hidden').empty();
            return;
        }

        const matches = (catalogCache || []).filter(p => {
            const name = (p.name || '').toLowerCase();
            const sku = (p.sku || '').toLowerCase();
            const barcode = (p.barcode || '').toLowerCase();
            const category = (p.category || '').toLowerCase();
            return name.includes(q) || sku.includes(q) || barcode.includes(q) || category.includes(q);
        }).slice(0, 8);

        if (!matches.length) {
            resultsBox.html(`
                <div class="p-4 text-center text-xs text-zinc-400">
                    No matching SKUs found for "<span class="text-zinc-600 dark:text-zinc-300 font-semibold">${escapeHtml(query)}</span>"
                </div>
            `).removeClass('hidden');
            return;
        }

        let html = '';
        matches.forEach(p => {
            const price = parseFloat(p.wholesale_price || p.wholesale_rate || 0);
            const stock = parseInt(p.stock_quantity || 0);
            const isAlreadyAllocated = consignmentAllocations.some(it => it.product_id === parseInt(p.id));
            const thumbHtml = p.image_url 
                ? `<img src="${escapeHtml(p.image_url)}" class="w-8 h-8 rounded-lg object-cover border border-zinc-200 dark:border-zinc-700 shrink-0">`
                : `<div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-bold text-[10px] text-zinc-700 dark:text-zinc-300 shrink-0">${(p.name || 'P').substring(0, 2).toUpperCase()}</div>`;

            html += `
                <div class="flex items-center justify-between p-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors text-xs gap-2">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        ${thumbHtml}
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 leading-tight truncate">${escapeHtml(p.name)}</div>
                            <div class="text-[11px] font-mono text-zinc-400 truncate">${escapeHtml(p.sku)} • Stock: <span class="font-semibold text-zinc-700 dark:text-zinc-300">${stock}</span> • ₹${price.toFixed(2)}</div>
                        </div>
                    </div>
                    <button type="button" onclick="CoraInventory.addSKUToConsignment(${p.id})" class="px-2.5 py-1 rounded-lg ${isAlreadyAllocated ? 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200' : 'bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950'} text-[11px] font-bold shadow-2xs hover:opacity-90 transition-all shrink-0 cursor-pointer flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>${isAlreadyAllocated ? 'Add +1' : 'Add to Van'}</span>
                    </button>
                </div>
            `;
        });
        resultsBox.html(html).removeClass('hidden');
    }

    function addSKUToConsignment(productId) {
        const pid = parseInt(productId);
        const existing = consignmentAllocations.find(it => it.product_id === pid);
        if (existing) {
            existing.quantity += 1;
            window.coraShowToast(`Increased quantity for ${existing.name} (Qty: ${existing.quantity})`, 'info');
        } else {
            const p = (catalogCache || []).find(x => parseInt(x.id) === pid);
            if (p) {
                consignmentAllocations.unshift({
                    product_id: pid,
                    name: p.name || 'Product',
                    sku: p.sku || 'SKU',
                    wholesale_price: parseFloat(p.wholesale_price || p.wholesale_rate || 0),
                    stock_quantity: parseInt(p.stock_quantity || 0),
                    image_url: p.image_url || '',
                    quantity: 1
                });
                window.coraShowToast(`Added ${p.name} to van allocation.`, 'success');
            }
        }
        $('#cora-csn-sku-search').val('');
        $('#cora-csn-search-results').addClass('hidden').empty();
        renderConsignmentAllocList();
    }

    function removeAllocItem(productId) {
        const pid = parseInt(productId);
        consignmentAllocations = consignmentAllocations.filter(it => it.product_id !== pid);
        renderConsignmentAllocList();
    }

    function onAllocQtyChange(productId, val) {
        const pid = parseInt(productId);
        const item = consignmentAllocations.find(it => it.product_id === pid);
        if (!item) return;

        let parsed = parseInt(val);
        if (isNaN(parsed) || parsed < 1) {
            parsed = 1;
        }
        item.quantity = parsed;

        const subtotalEl = $(`#cora-csn-subtotal-${pid}`);
        if (subtotalEl.length) {
            const lineTotal = (item.quantity * item.wholesale_price);
            subtotalEl.text('₹' + lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }
        recalcConsignmentVal();
    }

    function recalcConsignmentVal() {
        let total = 0;
        let units = 0;
        consignmentAllocations.forEach(it => {
            const qty = parseInt(it.quantity || 0);
            total += (qty * it.wholesale_price);
            units += qty;
        });
        $('#cora-csn-calc-val').text('Total: ₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#cora-csn-sku-count-badge').text(`${consignmentAllocations.length} SKUs`);
        $('#cora-csn-units-count-badge').text(`${units} units`);
    }

    function appendCityTag(cityName) {
        const input = $('#cora-csn-target-cities');
        let current = input.val().trim();
        const cities = current ? current.split(',').map(c => c.trim()).filter(Boolean) : [];
        if (!cities.some(c => c.toLowerCase() === cityName.toLowerCase())) {
            cities.push(cityName);
            input.val(cities.join(', '));
            window.coraShowToast(`Added ${cityName} to target cities.`, 'info');
        }
    }

    function switchDriverMode(mode) {
        $('#cora-csn-driver-mode').val(mode);
        if (mode === 'existing') {
            $('#cora-csn-pane-existing').removeClass('hidden');
            $('#cora-csn-pane-new').addClass('hidden');
            $('#cora-csn-btn-mode-existing')
                .addClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 font-bold')
                .removeClass('text-zinc-500 font-semibold');
            $('#cora-csn-btn-mode-new')
                .removeClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 font-bold')
                .addClass('text-zinc-500 font-semibold');
        } else {
            $('#cora-csn-pane-existing').addClass('hidden');
            $('#cora-csn-pane-new').removeClass('hidden');
            $('#cora-csn-btn-mode-new')
                .addClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 font-bold')
                .removeClass('text-zinc-500 font-semibold');
            $('#cora-csn-btn-mode-existing')
                .removeClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 font-bold')
                .addClass('text-zinc-500 font-semibold');
        }
    }

    function onDriverSelectChange() {
        const opt = $('#cora-csn-user-select option:selected');
        const name = opt.data('name') || 'Selected Driver';
        const email = opt.data('email') || '';
        const role = opt.data('role') || 'Field Sales / Mobile Vendor';
        const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() || 'DR';
        $('#cora-csn-preview-name').text(name);
        $('#cora-csn-preview-email').text(email);
        $('#cora-csn-preview-role').text(role);
        $('#cora-csn-preview-avatar').text(initials);
    }

    function focusOCRUpload() {
        switchSubtab('ocr');
        switchPerspective('plant');
        const dropzone = $('#cora-ocr-dropzone');
        if (dropzone.length) {
            $('html, body').animate({ scrollTop: dropzone.offset().top - 100 }, 300);
        }
    }

    function dispatchConsignment(e) {
        e.preventDefault();

        if (!consignmentAllocations.length) {
            window.coraShowToast('Please allocate at least one product with 1 or more units.', 'error');
            return;
        }

        const invalidItem = consignmentAllocations.find(it => !it.quantity || it.quantity < 1);
        if (invalidItem) {
            window.coraShowToast('All allocated items must have a quantity of 1 or more units.', 'error');
            return;
        }

        const items = consignmentAllocations.map(it => ({
            product_id: it.product_id,
            quantity: it.quantity
        }));

        const driverMode = $('#cora-csn-driver-mode').val() || 'existing';
        let vendorUserId = 0;
        let vendorName = '';
        let newDriverName = '';
        let newDriverEmail = '';
        let newDriverPhone = '';

        let existingDriverEmail = '';
        let existingDriverPhone = '';
        if (driverMode === 'existing') {
            vendorUserId = parseInt($('#cora-csn-user-select').val()) || 0;
            const selOpt = $('#cora-csn-user-select option:selected');
            vendorName = selOpt.data('name') || selOpt.text();
            existingDriverEmail = selOpt.data('email') || '';
            existingDriverPhone = selOpt.data('phone') || '';
        } else {
            newDriverName = ($('#cora-csn-new-name').val() || '').trim();
            newDriverEmail = ($('#cora-csn-new-email').val() || '').trim();
            newDriverPhone = ($('#cora-csn-new-phone').val() || '').trim();

            if (!newDriverName) {
                window.coraShowToast('Please enter the driver full name.', 'error');
                return;
            }
            if (!newDriverEmail) {
                window.coraShowToast('Please enter the driver email address.', 'error');
                return;
            }
            if (!newDriverPhone) {
                window.coraShowToast('Please enter the driver mobile / phone number.', 'error');
                return;
            }
            vendorName = newDriverName;
        }

        const routeName = ($('#cora-csn-route').val() || '').trim();
        if (!routeName) {
            window.coraShowToast('Please specify the assigned route name.', 'error');
            return;
        }

        const targetCities = ($('#cora-csn-target-cities').val() || '').trim();
        if (!targetCities) {
            window.coraShowToast('Please specify target coverage cities or zones.', 'error');
            return;
        }

        const googleMapsUrl = ($('#cora-csn-gmaps-url').val() || '').trim();

        window.coraShowToast('Dispatching van consignment & syncing permissions...', 'info');
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_create_consignment',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                driver_mode: driverMode,
                vendor_user_id: vendorUserId,
                existing_driver_email: existingDriverEmail,
                existing_driver_phone: existingDriverPhone,
                new_driver_name: newDriverName,
                new_driver_email: newDriverEmail,
                new_driver_phone: newDriverPhone,
                vendor_name: vendorName,
                vehicle_no: $('#cora-csn-vehicle').val(),
                route_name: routeName,
                google_maps_url: googleMapsUrl,
                target_cities: targetCities,
                items: JSON.stringify(items)
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Consignment dispatched successfully!', 'success');
                    closeConsignmentModal();
                    loadCatalog();
                    loadConsignments();
                    loadVendorDashboard();
                } else {
                    window.coraShowToast(res.data || 'Failed to dispatch.', 'error');
                }
            },
            error: function() {
                window.coraShowToast('Network error while dispatching consignment.', 'error');
            }
        });
    }

    function openSpotSaleSheet() {
        renderSpotSaleItems();
        openStudioDrawer('#cora-inv-spot-sale-sheet', '#cora-inv-spot-backdrop', '#cora-inv-spot-drawer');
    }

    function closeSpotSaleSheet() {
        closeStudioDrawer('#cora-inv-spot-sale-sheet', '#cora-inv-spot-backdrop', '#cora-inv-spot-drawer');
    }

    function validateSpotQty(input) {
        const val = parseInt($(input).val()) || 0;
        const max = parseInt($(input).attr('data-max')) || 0;
        if (val < 0) {
            $(input).val(0);
        } else if (val > max) {
            $(input).val(max);
            if (window.coraShowToast) {
                window.coraShowToast('Quantity adjusted to available van stock (' + max + ' units available).', 'warning');
            }
        }
    }

    function renderSpotSaleItems() {
        let html = '';
        const itemsToRender = (activeConsignmentItems && activeConsignmentItems.length) ? activeConsignmentItems : [];
        
        if (!itemsToRender.length) {
            $('#cora-spot-items-list').html(`
                <div class="p-8 text-center rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-dashed border-zinc-200 dark:border-zinc-700/60">
                    <div class="w-10 h-10 mx-auto mb-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    </div>
                    <div class="text-xs font-bold text-zinc-800 dark:text-zinc-200">No Dispatched Stock in this Van</div>
                    <div class="text-[11px] text-zinc-400 mt-1 max-w-sm mx-auto">This van terminal does not have active allocated route stock. Allocate inventory from the plant dispatch dashboard first.</div>
                </div>
            `);
            $('#cora-spot-total-display').text('Total: ₹0.00');
            return;
        }

        itemsToRender.forEach(it => {
            const pid = parseInt(it.product_id || it.id);
            const name = it.product_name || it.name || 'Stationery Item';
            const sku = it.sku || '';
            const rate = parseFloat(it.unit_rate || it.wholesale_price || 0);
            const gst = parseFloat(it.gst_rate || 12);
            const dispatchedQty = parseInt(it.dispatched_qty || 0);
            const soldQty = parseInt(it.sold_qty || 0);
            const remainingQty = Math.max(0, parseInt(it.remaining_qty !== undefined ? it.remaining_qty : (dispatchedQty - soldQty)));
            const unit = it.unit || 'units';
            const isOutOfStock = remainingQty <= 0;

            html += `
                <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-700/80 text-xs hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors ${isOutOfStock ? 'opacity-60 bg-zinc-100/40 dark:bg-zinc-900/40' : ''}">
                    <div class="flex items-center gap-3 min-w-0 flex-1 pr-2">
                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-bold text-[10px] text-zinc-600 dark:text-zinc-300 shrink-0 shadow-2xs">
                            ${escapeHtml(name.substring(0, 2).toUpperCase())}
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 leading-tight truncate">${escapeHtml(name)}</div>
                            <div class="text-[11px] font-mono text-zinc-400 mt-0.5 flex items-center gap-2 flex-wrap">
                                <span>${escapeHtml(sku)} • Rate: ₹${rate.toFixed(2)}</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] ${remainingQty > 0 ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 font-semibold' : 'bg-zinc-200/80 dark:bg-zinc-800 text-zinc-500 font-medium'}">
                                    ${remainingQty > 0 ? 'In Van: ' + remainingQty + ' ' + escapeHtml(unit) : 'Sold Out on Route'}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <label class="text-[11px] font-semibold text-zinc-500 hidden sm:inline-block">Billed Qty:</label>
                        <input type="number" 
                            data-pid="${pid}" 
                            data-name="${escapeHtml(name)}" 
                            data-sku="${escapeHtml(sku)}" 
                            data-rate="${rate}" 
                            data-gst="${gst}" 
                            data-max="${remainingQty}" 
                            min="0" 
                            max="${remainingQty}" 
                            value="0" 
                            ${isOutOfStock ? 'disabled' : ''} 
                            oninput="CoraInventory.validateSpotQty(this); CoraInventory.recalcSpotTotal();" 
                            class="cora-spot-qty-input w-20 px-2.5 py-1.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-right font-mono text-xs font-bold text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 disabled:opacity-40 disabled:cursor-not-allowed">
                    </div>
                </div>
            `;
        });
        $('#cora-spot-items-list').html(html);
        recalcSpotTotal();
    }

    function recalcSpotTotal() {
        let subtotal = 0;
        $('.cora-spot-qty-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const rate = parseFloat($(this).data('rate')) || 0;
            subtotal += (qty * rate);
        });
        const totalWithTax = subtotal * 1.12;
        $('#cora-spot-total-display').text('Total: ₹' + totalWithTax.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    }

    function submitSpotSale(e) {
        e.preventDefault();
        const items = [];
        let hasExceeded = false;
        let exceedMsg = '';

        $('.cora-spot-qty-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const max = parseInt($(this).attr('data-max')) || 0;
            const pid = parseInt($(this).data('pid')) || 0;
            const name = $(this).data('name') || '';
            const sku = $(this).data('sku') || '';
            const rate = parseFloat($(this).data('rate')) || 0;
            const gst = parseFloat($(this).data('gst')) || 12;

            if (qty > max) {
                hasExceeded = true;
                exceedMsg = `Requested quantity ${qty} for "${name}" exceeds available van stock (${max}).`;
            }

            if (qty > 0 && pid > 0) {
                items.push({
                    product_id: pid,
                    product_name: name,
                    sku: sku,
                    quantity: qty,
                    unit_price: rate,
                    gst_rate: gst
                });
            }
        });

        if (hasExceeded) {
            window.coraShowToast(exceedMsg, 'error');
            return;
        }

        if (!items.length) {
            window.coraShowToast('Specify at least 1 unit sold from active van stock.', 'error');
            return;
        }

        window.coraShowToast('Recording field invoice...', 'info');
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_record_spot_sale',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                consignment_id: activeConsignmentId,
                customer_name: $('#cora-spot-customer').val(),
                phone: $('#cora-spot-phone').val(),
                payment_mode: $('input[name="cora_spot_pay_mode"]:checked').val() || 'cash',
                items: JSON.stringify(items)
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Spot bill created successfully!', 'success');
                    closeSpotSaleSheet();
                    loadCatalog();
                    loadConsignments();
                    loadVendorDashboard();
                } else {
                    window.coraShowToast(res.data || 'Failed to record spot sale.', 'error');
                }
            },
            error: function() {
                window.coraShowToast('Network error recording spot sale.', 'error');
            }
        });
    }

    function renderVendorVanStock(items) {
        const container = $('#cora-vendor-van-stock-list');
        const badge = $('#cora-vendor-van-stock-badge');
        if (!container.length) return;

        if (!items || !items.length) {
            badge.text('0 SKUs');
            container.html(`
                <div class="p-5 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-dashed border-zinc-200 dark:border-zinc-700 text-center">
                    <div class="text-xs font-bold text-zinc-700 dark:text-zinc-300">No Cargo Allocated</div>
                    <div class="text-[11px] text-zinc-400 mt-0.5">Route inventory dispatched by the factory plant will appear here.</div>
                </div>
            `);
            return;
        }

        badge.text(items.length + (items.length === 1 ? ' SKU' : ' SKUs'));
        let html = '';
        items.forEach(it => {
            const name = it.product_name || it.name || 'Stationery Item';
            const sku = it.sku || '';
            const rate = parseFloat(it.unit_rate || it.wholesale_price || 0);
            const dispatchedQty = parseInt(it.dispatched_qty || 0);
            const soldQty = parseInt(it.sold_qty || 0);
            const remainingQty = Math.max(0, parseInt(it.remaining_qty !== undefined ? it.remaining_qty : (dispatchedQty - soldQty)));
            const unit = it.unit || 'units';
            const valOnWheels = remainingQty * rate;

            html += `
                <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 text-xs border border-zinc-200/60 dark:border-zinc-700/60 gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate">${escapeHtml(name)}</div>
                        <div class="text-[11px] text-zinc-400 font-mono mt-0.5 truncate">${escapeHtml(sku)} • Rate: ₹${rate.toFixed(2)}</div>
                    </div>
                    <div class="text-right font-mono shrink-0">
                        <div class="font-bold text-zinc-900 dark:text-zinc-100">${remainingQty} <span class="font-sans font-normal text-[10px] text-zinc-400">/ ${dispatchedQty} ${escapeHtml(unit)}</span></div>
                        <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">₹${Number(valOnWheels.toFixed(2)).toLocaleString('en-IN')} on wheels</div>
                    </div>
                </div>
            `;
        });
        container.html(html);
    }

    function loadVendorDashboard() {
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_get_vendor_dashboard',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
            },
            success: function(res) {
                if (!res.success) return;
                const data = res.data;
                const csn = data.active_consignment;
                const bannerWrap = $('#cora-vendor-consignment-banner-wrap');

                if (csn) {
                    activeConsignmentId = parseInt(csn.id);
                    activeConsignmentData = csn;
                    activeConsignmentItems = Array.isArray(csn.items) ? csn.items : [];
                    renderVendorVanStock(activeConsignmentItems);
                    const dispatched = parseFloat(csn.total_dispatched_val || 0);
                    const sold = parseFloat(csn.total_sold_val || 0);
                    const onWheels = Math.max(0, dispatched - sold);
                    const cash = parseFloat(csn.cash_collected || 0);

                    let gmapsBtnHtml = '';
                    if (csn.google_maps_url) {
                        gmapsBtnHtml = `
                            <a href="${escapeHtml(csn.google_maps_url)}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-100 border border-zinc-700/80 text-[11px] font-semibold transition-all shadow-2xs cursor-pointer">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span>Open Route in Google Maps ↗</span>
                            </a>
                        `;
                    }

                    let citiesHtml = '';
                    if (csn.target_cities) {
                        const cityList = csn.target_cities.split(',').map(c => c.trim()).filter(Boolean);
                        if (cityList.length) {
                            citiesHtml = `
                                <div class="flex items-center gap-1.5 flex-wrap mt-2.5 pt-2.5 border-t border-zinc-800/60">
                                    <span class="text-[10px] uppercase font-mono text-zinc-500 font-semibold">Coverage:</span>
                                    ${cityList.map(city => `<span class="px-2 py-0.5 rounded-md bg-zinc-900/90 text-zinc-300 border border-zinc-800 text-[10px] font-medium">${escapeHtml(city)}</span>`).join('')}
                                </div>
                            `;
                        }
                    }

                    bannerWrap.html(`
                        <div class="p-5 rounded-3xl bg-zinc-950 text-white shadow-lg relative overflow-hidden border border-zinc-800 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-900 text-zinc-300 border border-zinc-800 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Active Consignment #${escapeHtml(csn.consignment_no)}
                                </span>
                                <span class="text-[11px] font-mono text-zinc-400">${escapeHtml(csn.vehicle_no || '')}</span>
                            </div>
                            <div class="text-3xl font-bold font-mono tracking-tight text-white mb-1">
                                ₹${Number(onWheels).toLocaleString('en-IN')} <span class="text-xs font-sans font-normal text-zinc-400">Stock on Wheels</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-zinc-400">
                                <div class="truncate">Route: <span class="text-zinc-200 font-semibold">${escapeHtml(csn.route_name || 'Assigned Territory')}</span></div>
                                ${gmapsBtnHtml}
                            </div>
                            ${citiesHtml}
                            <div class="flex items-center justify-between pt-3 border-t border-zinc-800 text-xs text-zinc-400">
                                <div>Sold Today: <span class="font-bold text-zinc-100 font-mono">₹${Number(sold).toLocaleString('en-IN')}</span></div>
                                <div>Cash in Hand: <span class="font-bold text-white font-mono">₹${Number(cash).toLocaleString('en-IN')}</span></div>
                            </div>
                        </div>
                    `);
                } else {
                    activeConsignmentId = 0;
                    activeConsignmentData = null;
                    activeConsignmentItems = [];
                    renderVendorVanStock([]);
                    bannerWrap.html(`
                        <div class="p-6 rounded-3xl bg-zinc-950 text-white shadow-lg relative overflow-hidden border border-zinc-800">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-900 text-zinc-400 border border-zinc-800 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span> Standby Mode
                                </span>
                                <span class="text-[11px] font-mono text-zinc-500">No Active Consignment</span>
                            </div>
                            <div class="text-2xl font-bold font-mono tracking-tight text-white mb-1">
                                ₹0 <span class="text-xs font-sans font-normal text-zinc-400">Stock on Wheels</span>
                            </div>
                            <p class="text-xs text-zinc-400 mt-2 leading-relaxed">No active consignment allocated for this shift. Factory dispatch will assign route stock to this terminal.</p>
                            <div class="flex items-center justify-between pt-3 mt-4 border-t border-zinc-800/80 text-xs text-zinc-400">
                                <div>Sold Today: <span class="font-bold text-zinc-300 font-mono">₹0</span></div>
                                <div>Cash in Hand: <span class="font-bold text-zinc-300 font-mono">₹0</span></div>
                            </div>
                        </div>
                    `);
                }

                // Render Today's Spot Sales Ledger
                const salesList = $('#cora-vendor-sales-list');
                const sales = data.sales || [];
                salesLedgerCache = sales;
                if (!sales.length) {
                    salesList.html(`
                        <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-dashed border-zinc-200/80 dark:border-zinc-700/60 text-center">
                            <div class="w-10 h-10 mx-auto mb-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            </div>
                            <div class="text-xs font-bold text-zinc-800 dark:text-zinc-200">No Spot Sales Logged Today</div>
                            <div class="text-[11px] text-zinc-400 mt-0.5">Use "Quick Spot Sale" or "Snap Paper Bill" to bill local retail shops on this route.</div>
                        </div>
                    `);
                } else {
                    let salesHtml = '';
                    sales.forEach(s => {
                        const isCash = (s.payment_mode || '').toLowerCase() === 'cash';
                        const badgeText = isCash ? 'Cash Received' : (s.payment_mode || 'UPI').toUpperCase() + ' Verified';
                        salesHtml += `
                            <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 text-xs border border-zinc-200/60 dark:border-zinc-700/60 gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate">${escapeHtml(s.customer_name || 'Retail Customer')}</div>
                                    <div class="text-[11px] text-zinc-400 truncate">${escapeHtml(s.invoice_no)} • ${escapeHtml(s.phone || 'Walk-in')}</div>
                                </div>
                                <div class="text-right font-mono shrink-0">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">₹${Number(s.grand_total || 0).toLocaleString('en-IN')}</div>
                                    <div class="text-[10px] text-zinc-400">${badgeText}</div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0 ml-1">
                                    <button type="button" onclick="CoraInventory.openEditSaleModal(${s.id})" class="p-1.5 rounded-lg bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer" title="Edit Invoice">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </button>
                                    <button type="button" onclick="CoraInventory.openDeleteSaleModal(${s.id}, '${escapeHtml(s.invoice_no)}')" class="p-1.5 rounded-lg bg-white dark:bg-zinc-800 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40 dark:hover:text-red-400 text-zinc-400 border border-zinc-200 dark:border-zinc-700 transition-colors cursor-pointer" title="Delete Invoice">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    salesList.html(salesHtml);
                }
            }
        });
    }

    function openEditSaleModal(saleId) {
        const s = (salesLedgerCache || []).find(x => parseInt(x.id) === parseInt(saleId));
        if (!s) return;

        $('#cora-edit-sale-id').val(s.id);
        $('#cora-edit-sale-inv-badge').text(s.invoice_no || 'INV');
        $('#cora-edit-sale-customer').val(s.customer_name || '');
        $('#cora-edit-sale-phone').val(s.phone || '');
        $('#cora-edit-sale-gstin').val(s.gstin || '');
        $('#cora-edit-sale-paymode').val((s.payment_mode || 'cash').toLowerCase());
        $('#cora-edit-sale-paystatus').val((s.payment_status || 'paid').toLowerCase());
        $('#cora-edit-sale-grandtotal').val(parseFloat(s.grand_total || 0).toFixed(2));
        $('#cora-edit-sale-paidamount').val(parseFloat(s.paid_amount || s.grand_total || 0).toFixed(2));

        openStudioDrawer('#cora-inv-edit-sale-sheet', '#cora-inv-edit-sale-backdrop', '#cora-inv-edit-sale-drawer');
    }

    function closeEditSaleModal() {
        closeStudioDrawer('#cora-inv-edit-sale-sheet', '#cora-inv-edit-sale-backdrop', '#cora-inv-edit-sale-drawer');
    }

    function submitUpdateSale(e) {
        if (e) e.preventDefault();
        const saleId = $('#cora-edit-sale-id').val();
        if (!saleId || parseInt(saleId) <= 0) return;

        const customer = $('#cora-edit-sale-customer').val().trim();
        const phone = $('#cora-edit-sale-phone').val().trim();
        const gstin = $('#cora-edit-sale-gstin').val().trim();
        const payMode = $('#cora-edit-sale-paymode').val();
        const payStatus = $('#cora-edit-sale-paystatus').val();
        const grandTotal = $('#cora-edit-sale-grandtotal').val();
        const paidAmount = $('#cora-edit-sale-paidamount').val();

        if (!customer) {
            window.coraShowToast('Please enter customer / store name', 'error');
            return;
        }

        const btn = $('#cora-edit-sale-save-btn');
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_update_spot_sale',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                sale_id: saleId,
                customer_name: customer,
                phone: phone,
                gstin: gstin,
                payment_mode: payMode,
                payment_status: payStatus,
                grand_total: grandTotal,
                paid_amount: paidAmount
            },
            success: function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Save Changes</span>');
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Invoice updated successfully.', 'success');
                    closeEditSaleModal();
                    loadVendorDashboard();
                    loadConsignments();
                } else {
                    window.coraShowToast(res.data.message || 'Failed to update invoice.', 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Save Changes</span>');
                window.coraShowToast('Network error while updating invoice.', 'error');
            }
        });
    }

    function openDeleteSaleModal(saleId, invoiceNo) {
        deleteSaleId = saleId;
        $('#cora-delete-sale-no-display').text(invoiceNo || ('#' + saleId));

        $('#cora-inv-delete-sale-sheet').removeClass('hidden').css('display', 'block');
        requestAnimationFrame(() => {
            $('#cora-inv-delete-sale-sheet').removeClass('pointer-events-none');
            $('#cora-inv-delete-sale-backdrop').removeClass('opacity-0').addClass('opacity-100');
            $('#cora-inv-delete-sale-drawer').removeClass('translate-y-full').addClass('translate-y-0');
        });
    }

    function closeDeleteSaleModal() {
        $('#cora-inv-delete-sale-drawer').removeClass('translate-y-0').addClass('translate-y-full');
        $('#cora-inv-delete-sale-backdrop').removeClass('opacity-100').addClass('opacity-0');
        setTimeout(() => {
            $('#cora-inv-delete-sale-sheet').addClass('pointer-events-none hidden').css('display', 'none');
        }, 300);
    }

    function confirmDeleteSale() {
        if (!deleteSaleId || parseInt(deleteSaleId) <= 0) return;

        const btn = $('#cora-del-sale-confirm-btn');
        btn.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_delete_spot_sale',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                sale_id: deleteSaleId
            },
            success: function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Delete Invoice</span>');
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Invoice deleted & van stock restored.', 'success');
                    closeDeleteSaleModal();
                    loadVendorDashboard();
                    loadConsignments();
                    loadCatalog();
                } else {
                    window.coraShowToast(res.data.message || 'Failed to delete invoice.', 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Delete Invoice</span>');
                window.coraShowToast('Network error while deleting invoice.', 'error');
            }
        });
    }

    function openEditConsignmentModal(csnId) {
        const c = (consignmentsCache || []).find(x => parseInt(x.id) === parseInt(csnId));
        if (!c) return;

        $('#cora-edit-csn-id').val(c.id);
        $('#cora-edit-csn-no-badge').text(c.consignment_no || 'CSN');
        $('#cora-edit-csn-vehicleno').val(c.vehicle_no || '');
        $('#cora-edit-csn-routename').val(c.route_name || '');
        $('#cora-edit-csn-gmaps').val(c.google_maps_url || '');
        $('#cora-edit-csn-cities').val(c.target_cities || '');

        openStudioDrawer('#cora-inv-edit-csn-sheet', '#cora-inv-edit-csn-backdrop', '#cora-inv-edit-csn-drawer');
    }

    function closeEditConsignmentModal() {
        closeStudioDrawer('#cora-inv-edit-csn-sheet', '#cora-inv-edit-csn-backdrop', '#cora-inv-edit-csn-drawer');
    }

    function submitUpdateConsignment(e) {
        if (e) e.preventDefault();
        const csnId = $('#cora-edit-csn-id').val();
        if (!csnId || parseInt(csnId) <= 0) return;

        const vehicleNo = $('#cora-edit-csn-vehicleno').val().trim();
        const routeName = $('#cora-edit-csn-routename').val().trim();
        const gmapsUrl = $('#cora-edit-csn-gmaps').val().trim();
        const targetCities = $('#cora-edit-csn-cities').val().trim();

        if (!vehicleNo || !routeName) {
            window.coraShowToast('Please specify vehicle number and route name.', 'error');
            return;
        }

        const btn = $('#cora-edit-csn-save-btn');
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_update_consignment',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                consignment_id: csnId,
                vehicle_no: vehicleNo,
                route_name: routeName,
                google_maps_url: gmapsUrl,
                target_cities: targetCities
            },
            success: function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Save Consignment</span>');
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Consignment updated successfully.', 'success');
                    closeEditConsignmentModal();
                    loadConsignments();
                    loadVendorDashboard();
                } else {
                    window.coraShowToast(res.data.message || 'Failed to update consignment.', 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Save Consignment</span>');
                window.coraShowToast('Network error while updating consignment.', 'error');
            }
        });
    }

    function openDeleteConsignmentModal(csnId, csnNo) {
        deleteCsnId = csnId;
        $('#cora-delete-csn-no-display').text(csnNo || ('#' + csnId));

        $('#cora-inv-delete-csn-sheet').removeClass('hidden').css('display', 'block');
        requestAnimationFrame(() => {
            $('#cora-inv-delete-csn-sheet').removeClass('pointer-events-none');
            $('#cora-inv-delete-csn-backdrop').removeClass('opacity-0').addClass('opacity-100');
            $('#cora-inv-delete-csn-drawer').removeClass('translate-y-full').addClass('translate-y-0');
        });
    }

    function closeDeleteConsignmentModal() {
        $('#cora-inv-delete-csn-drawer').removeClass('translate-y-0').addClass('translate-y-full');
        $('#cora-inv-delete-csn-backdrop').removeClass('opacity-100').addClass('opacity-0');
        setTimeout(() => {
            $('#cora-inv-delete-csn-sheet').addClass('pointer-events-none hidden').css('display', 'none');
        }, 300);
    }

    function confirmDeleteConsignment() {
        if (!deleteCsnId || parseInt(deleteCsnId) <= 0) return;

        const btn = $('#cora-del-csn-confirm-btn');
        btn.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_delete_consignment',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                consignment_id: deleteCsnId
            },
            success: function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Delete Consignment</span>');
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Consignment deleted & unsold items restocked.', 'success');
                    closeDeleteConsignmentModal();
                    loadConsignments();
                    loadCatalog();
                    loadVendorDashboard();
                } else {
                    window.coraShowToast(res.data.message || 'Failed to delete consignment.', 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Delete Consignment</span>');
                window.coraShowToast('Network error while deleting consignment.', 'error');
            }
        });
    }

    function copyDriverInviteLink(link) {
        if (!link) {
            window.coraShowToast('No invite link available for this consignment.', 'warning');
            return;
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(link).then(() => {
                window.coraShowToast('Driver invite & direct access link copied to clipboard!', 'success');
            }).catch(() => {
                const textarea = document.createElement('textarea');
                textarea.value = link;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                window.coraShowToast('Driver invite & direct access link copied to clipboard!', 'success');
            });
        } else {
            const textarea = document.createElement('textarea');
            textarea.value = link;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            window.coraShowToast('Driver invite & direct access link copied to clipboard!', 'success');
        }
    }

    function shareConsignmentWhatsApp(csnId) {
        const c = (consignmentsCache || []).find(x => parseInt(x.id) === parseInt(csnId));
        if (!c) return;

        const driverName = c.vendor_name || 'Driver';
        const link = c.invite_link || window.location.origin + '/workspace/dashboard?sub_page=plant_inventory';
        const msg = `📦 *Cora Dispatch Assignment: ${c.consignment_no}*\n` +
                    `👤 Driver: ${driverName}\n` +
                    `🚐 Vehicle: ${c.vehicle_no}\n` +
                    `🗺️ Route: ${c.route_name}\n` +
                    `💰 Dispatched Stock Value: ₹${Number(c.total_dispatched_val || 0).toLocaleString('en-IN')}\n\n` +
                    `🔗 *Access Field Van Terminal:*\n${link}`;

        const phoneDigits = c.driver_phone ? c.driver_phone.replace(/[^0-9]/g, '') : '';
        const waUrl = 'https://wa.me/' + (phoneDigits ? phoneDigits : '') + '?text=' + encodeURIComponent(msg);
        window.open(waUrl, '_blank', 'noopener,noreferrer');
    }

    function resendConsignmentEmail(csnId) {
        const c = (consignmentsCache || []).find(x => parseInt(x.id) === parseInt(csnId));
        const recipient = (c && c.driver_email) ? ` to ${c.driver_email}` : '';
        window.coraShowToast(`Sending dispatch notification email${recipient}...`, 'info');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_resend_consignment_email',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                consignment_id: csnId
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Dispatch notification email sent successfully!', 'success');
                    loadConsignments();
                } else {
                    window.coraShowToast(res.data.message || 'Failed to send email.', 'error');
                }
            },
            error: function() {
                window.coraShowToast('Network error while sending notification email.', 'error');
            }
        });
    }

    function viewAsDriver(csnId) {
        switchPerspective('vendor');
        if (csnId) {
            const vendorSelect = $('#cora-vendor-consignment-select');
            if (vendorSelect.length) {
                vendorSelect.val(csnId).trigger('change');
            }
        }
        window.coraShowToast('Switched to Field Van Sales Driver Terminal.', 'info');
    }

    function escapeHtml(str) {
        return (str || '').toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Initialize on DOM ready or immediate if already loaded
    if (typeof $ === 'function' && typeof $(document).ready === 'function') {
        $(document).ready(init);
    } else if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        setTimeout(init, 50);
    }

    return {
        init,
        switchPerspective,
        switchSubtab,
        loadCatalog,
        debouncedSearch: () => {
            clearTimeout(window._coraSearchTimer);
            window._coraSearchTimer = setTimeout(loadCatalog, 250);
        },
        openProductModal,
        closeProductModal,
        switchProductStep,
        prevProductStep,
        nextProductStep,
        autoGenerateSKU,
        onCategoryChange,
        recalcProductPricingMargins,
        onImageURLInput,
        removeProductImage,
        handleProductImageUpload,
        saveProduct,
        openDeleteModal,
        closeDeleteModal,
        confirmDeleteProduct,
        toggleSelectAll,
        selectAllVisible,
        onItemCheckboxChange,
        clearSelection,
        openBulkDeleteModal,
        closeBulkDeleteModal,
        confirmBulkDeleteProducts,
        quickAdjustStock: (id, name) => {
            openProductModal(id, 3);
        },
        openConsignmentModal,
        closeConsignmentModal,
        suggestTop5Products,
        openMultiProductPicker,
        closeMultiProductPicker,
        filterMultiPicker,
        toggleMultiPickerSelectAll,
        onMultiPickerCheckboxToggle,
        onMultiQtyChange,
        applyMultiProductSelection,
        stepAllocQty,
        clearAllAllocations,
        renderConsignmentAllocList,
        searchSKUsToAllocate,
        addSKUToConsignment,
        removeAllocItem,
        onAllocQtyChange,
        appendCityTag,
        switchDriverMode,
        onDriverSelectChange,
        focusOCRUpload,
        dispatchConsignment,
        recalcConsignmentVal,
        openEditConsignmentModal,
        closeEditConsignmentModal,
        submitUpdateConsignment,
        openDeleteConsignmentModal,
        closeDeleteConsignmentModal,
        confirmDeleteConsignment,
        copyDriverInviteLink,
        shareConsignmentWhatsApp,
        resendConsignmentEmail,
        viewAsDriver,
        openSpotSaleSheet,
        closeSpotSaleSheet,
        submitSpotSale,
        recalcSpotTotal,
        validateSpotQty,
        renderVendorVanStock,
        openEditSaleModal,
        closeEditSaleModal,
        submitUpdateSale,
        openDeleteSaleModal,
        closeDeleteSaleModal,
        confirmDeleteSale,
        openOCRSheet: () => {
            if (<?php echo $is_driver_only ? 'true' : 'false'; ?>) {
                openSpotSaleSheet();
                if (window.coraShowToast) window.coraShowToast('Attach invoice bill photo directly inside the spot sale sheet', 'info');
            } else {
                switchSubtab('ocr');
                switchPerspective('plant');
            }
        },
        handleOCRFile,
        processOCR,
        confirmOCRSale,
        openShopVisitSheet: () => {
            if (<?php echo $is_driver_only ? 'true' : 'false'; ?>) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            if (window.coraShowToast) window.coraShowToast('GPS Location recorded (' + pos.coords.latitude.toFixed(4) + ', ' + pos.coords.longitude.toFixed(4) + ')', 'success');
                        },
                        () => {
                            if (window.coraShowToast) window.coraShowToast('GPS active for your delivery route', 'info');
                        }
                    );
                } else {
                    if (window.coraShowToast) window.coraShowToast('Shop visit recorded', 'info');
                }
            } else {
                switchSubtab('map');
                switchPerspective('plant');
            }
        },
        openReconcileSheet: () => {
            if (<?php echo $is_driver_only ? 'true' : 'false'; ?>) {
                if (window.coraShowToast) window.coraShowToast('End-of-day return summary: Please hand over unsold stock at plant terminal.', 'info');
            } else {
                switchSubtab('recon');
                switchPerspective('plant');
            }
        },
        generateDailyRecon,
        exportDailyPDF,
        openReconShareModal,
        closeReconShareModal,
        copyReconShareText,
        copyReconPDFLink,
        shareReconWhatsApp,
        openImportModal,
        closeImportModal,
        switchImportTab,
        handleCSVFile,
        parseCSVContent,
        renderStagingGrid,
        commitBulkImport,
        downloadSampleCSV,
        exportCatalogCSV,
        openExportModal,
        closeExportModal,
        setExportScope,
        triggerExportCSV,
        triggerExportPDF,
        toggleFocusMode,
        setFocusMode,
        toggleAnalyticsCards,
        setAnalyticsCards,
        loadStarterKit,
        setPricingFilter,
        setViewMode,
        onPricingTypeChange,
        onWeightGramsInput,
        onCategoryChange,
        autoGenerateSKU,
        formatCategoryName,
        renderCategoryDropdowns,
        settleConsignment: (id) => {
            window.coraShowToast('Settling day-end returns for Consignment #' + id + '...', 'info');
            $.ajax({
                url: ajaxurl || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'cora_inventory_reconcile_consignment',
                    security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                    consignment_id: id
                },
                success: function(res) {
                    if (res.success) {
                        window.coraShowToast('Consignment settled & unsold stock restocked to factory.', 'success');
                        loadCatalog();
                        loadConsignments();
                        loadVendorDashboard();
                    }
                }
            });
        }
    };

})(window.jQuery || window.$ || {});
</script>
