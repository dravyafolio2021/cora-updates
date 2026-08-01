<?php
/**
 * Cora Real Estate CRM - Module 6: System Settings Complete Suite
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<script>
if (typeof window.coraREData === 'undefined') {
    window.coraREData = {
        ajaxUrl: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
        ajaxNonce: '<?php echo esc_js( wp_create_nonce( 'cora_ajax_nonce' ) ); ?>'
    };
}
</script>
<?php

$active_tab = isset( $_GET['settings_tab'] ) ? sanitize_text_field( $_GET['settings_tab'] ) : 'general';
if ( $active_tab === 'activity' ) {
    $active_tab = 'audit';
}
// Only fetch real, human-created published pages — exclude WP auto-generated/system pages
$pages = get_pages( array(
    'post_status'    => 'publish',
    'sort_column'    => 'post_title',
    'sort_order'     => 'ASC',
    'exclude_tree'   => array(),
    'meta_query'     => array(
        array(
            'key'     => '_wp_page_template',
            'compare' => 'EXISTS',
        ),
    ),
) );
// Filter out WP auto-generated pages (comments, menu, etc.) by name pattern
$pages = array_filter( $pages, function( $p ) {
    $skip_patterns = array( ' Comments Page ', ' Menu Page ', ' Front Page ', 'Coming Soon' );
    foreach ( $skip_patterns as $pattern ) {
        if ( strpos( $p->post_title, $pattern ) !== false ) return false;
    }
    return true;
} );
$categories = get_categories();
$roles      = wp_roles()->get_names();

$cora_settings_tabs = array(
    'general'    => array(
        'label' => 'General Settings',
        'desc'  => 'Workspace details & identity',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'
    ),
    'pwd-policy' => array(
        'label' => 'Password Policy',
        'desc'  => 'Enforce security parameters',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>'
    ),
    'branches'   => array(
        'label' => 'Branches',
        'desc'  => 'Brokerage physical offices',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>'
    ),
    'brand'      => array(
        'label' => 'Branding & APIs',
        'desc'  => 'Favicon, logos, integrations',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>'
    ),
    'reading'    => array(
        'label' => 'Content & SEO',
        'desc'  => 'Pages, writing, URLs & indexing',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>'
    ),
    'privacy'    => array(
        'label' => 'Privacy',
        'desc'  => 'Compliance terms page',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
    ),
    'git-sync'   => array(
        'label' => 'Git Sync',
        'desc'  => 'Lovable & GitHub Integrations',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="18" r="3"></circle><path d="M18 15V9a4 4 0 0 0-4-4h-4a4 4 0 0 0-4 4v6"></path><circle cx="12" cy="5" r="1"></circle></svg>'
    ),
    'audit'      => array(
        'label' => 'Audit & Logs',
        'desc'  => 'System activity & cost analysis',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
    ),
    'onboarding' => array(
        'label' => 'User Onboarding',
        'desc'  => 'Registration, Google OAuth & access',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>'
    ),
    'backup'     => array(
        'label' => 'Backup & Recovery',
        'desc'  => 'Local server & Google Drive exports',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>'
    ),
    'updates'    => array(
        'label' => 'Updates & Platform',
        'desc'  => 'Click-to-update & GitHub sync',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>'
    )
);
?>

<style>
/* Shopify-style Complete Settings Suite Stylesheet */
.cora-shopify-settings-theme {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
}
.cora-shopify-settings-theme label:not(.cora-label-raw) {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #52525b !important; /* zinc-600 */
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    margin-bottom: 6px !important;
}
.dark .cora-shopify-settings-theme label:not(.cora-label-raw) {
    color: #a1a1aa !important; /* zinc-400 */
}
.cora-shopify-settings-theme input[type="text"],
.cora-shopify-settings-theme input[type="email"],
.cora-shopify-settings-theme input[type="password"],
.cora-shopify-settings-theme input[type="number"],
.cora-shopify-settings-theme input[type="url"],
.cora-shopify-settings-theme select {
    width: 100% !important;
    background-color: #ffffff !important;
    border: 1px solid #e4e4e7 !important; /* zinc-200 */
    border-radius: 8px !important;
    padding: 8px 12px !important;
    font-size: 13px !important;
    color: #18181b !important; /* zinc-900 */
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
    transition: all 0.15s ease !important;
}
.cora-shopify-settings-theme input[type="text"]:focus,
.cora-shopify-settings-theme input[type="email"]:focus,
.cora-shopify-settings-theme input[type="password"]:focus,
.cora-shopify-settings-theme input[type="number"]:focus,
.cora-shopify-settings-theme input[type="url"]:focus,
.cora-shopify-settings-theme select:focus {
    outline: none !important;
    border-color: #09090b !important; /* zinc-950 */
    box-shadow: 0 0 0 2px rgba(9, 9, 11, 0.06), 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
}
/* Dark Mode Override */
.dark .cora-shopify-settings-theme input[type="text"],
.dark .cora-shopify-settings-theme input[type="email"],
.dark .cora-shopify-settings-theme input[type="password"],
.dark .cora-shopify-settings-theme input[type="number"],
.dark .cora-shopify-settings-theme input[type="url"],
.dark .cora-shopify-settings-theme select {
    background-color: #09090b !important; /* zinc-955 */
    border-color: #27272a !important; /* zinc-800 */
    color: #f4f4f5 !important; /* zinc-100 */
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.15) !important;
}
.dark .cora-shopify-settings-theme input[type="text"]:focus,
.dark .cora-shopify-settings-theme input[type="email"]:focus,
.dark .cora-shopify-settings-theme input[type="password"]:focus,
.dark .cora-shopify-settings-theme input[type="number"]:focus,
.dark .cora-shopify-settings-theme input[type="url"]:focus,
.dark .cora-shopify-settings-theme select:focus {
    border-color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.2) !important;
}
/* Custom Checkboxes */
.cora-shopify-settings-theme input[type="checkbox"] {
    appearance: none !important;
    -webkit-appearance: none !important;
    width: 16px !important;
    height: 16px !important;
    border: 1px solid #d4d4d8 !important;
    border-radius: 4px !important;
    outline: none !important;
    background-color: #ffffff !important;
    cursor: pointer !important;
    position: relative !important;
    display: inline-block !important;
    vertical-align: middle !important;
    box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.03) !important;
    transition: all 0.15s ease !important;
}
.cora-shopify-settings-theme input[type="checkbox"]:checked {
    background-color: #09090b !important;
    border-color: #09090b !important;
}
.cora-shopify-settings-theme input[type="checkbox"]:checked::after {
    content: '' !important;
    position: absolute !important;
    left: 5px !important;
    top: 2px !important;
    width: 4px !important;
    height: 8px !important;
    border: solid #ffffff !important;
    border-width: 0 2px 2px 0 !important;
    transform: rotate(45deg) !important;
}
.dark .cora-shopify-settings-theme input[type="checkbox"] {
    background-color: #09090b !important;
    border-color: #27272a !important;
}
.dark .cora-shopify-settings-theme input[type="checkbox"]:checked {
    background-color: #ffffff !important;
    border-color: #ffffff !important;
}
.dark .cora-shopify-settings-theme input[type="checkbox"]:checked::after {
    border-color: #09090b !important;
}
/* Shopify discrete card blocks */
.cora-shopify-card {
    background-color: #ffffff !important;
    border: 1px solid #e4e4e7 !important;
    border-radius: 8px !important;
    padding: 0 !important; /* Remove card wrapper padding */
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02), 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
    overflow: hidden !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.cora-shopify-card.cora-metric-card {
    padding: 16px 20px !important;
}
.dark .cora-shopify-card {
    background-color: #18181b !important; /* zinc-900 */
    border-color: #27272a !important; /* zinc-800 */
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.1) !important;
}
/* Mobile UI/UX Responsiveness Overrides */
@media (max-width: 639px) {
    .cora-shopify-card {
        padding: 0 !important;
    }
}
.scrollbar-none::-webkit-scrollbar {
    display: none !important;
}
.scrollbar-none {
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}
/* Left sidebar items */
.cora-settings-nav-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 7px 12px !important;
    border-radius: 6px !important;
    font-size: 12px !important;
    font-weight: 550 !important;
    color: #4b5563 !important; /* gray-600 */
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
    transition: all 0.15s ease !important;
}
.cora-settings-nav-item:hover {
    background-color: #f4f4f5 !important; /* zinc-100 */
    color: #111827 !important; /* gray-900 */
}
.cora-settings-nav-item.active {
    background-color: #f4f4f5 !important; /* zinc-100 */
    color: #111827 !important; /* gray-900 */
    font-weight: 600 !important;
}
.dark .cora-settings-nav-item {
    color: #9ca3af !important; /* gray-400 */
}
.dark .cora-settings-nav-item:hover {
    background-color: #27272a !important; /* zinc-800 */
    color: #f9fafb !important; /* gray-50 */
}
.dark .cora-settings-nav-item.active {
    background-color: #27272a !important; /* zinc-800 */
    color: #f9fafb !important; /* gray-50 */
}
/* Shopify-style Actions Bar container */
.cora-shopify-actions-bar {
    position: relative !important;
    background: #ffffff !important;
    border: 1px solid #e4e4e7 !important;
    border-radius: 8px !important;
    padding: 12px 20px !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-top: 32px !important;
    max-width: 48rem !important;
}
.dark .cora-shopify-actions-bar {
    background: #18181b !important; /* zinc-900 */
    border-color: #27272a !important; /* zinc-800 */
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2) !important;
}
/* Collapsible settings cards */
.cora-shopify-card-header {
    cursor: pointer !important;
    user-select: none !important;
    padding: 16px 20px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    border-bottom: 0px solid transparent !important;
    transition: background-color 0.2s ease, border-color 0.2s ease, border-bottom-width 0.2s ease !important;
}
@media (max-width: 639px) {
    .cora-shopify-card-header {
        padding: 12px 14px !important;
    }
}
.cora-shopify-card-header:hover {
    background-color: #fafafa !important;
}
.dark .cora-shopify-card-header:hover {
    background-color: #1f1f23 !important;
}
/* Highlight expanded cards */
.cora-shopify-card.expanded {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02) !important;
}
.cora-shopify-card.expanded .cora-shopify-card-header {
    border-bottom: 1px solid #f4f4f5 !important;
}
.dark .cora-shopify-card.expanded .cora-shopify-card-header {
    border-bottom: 1px solid #27272a !important;
}

/* Card Body spacing and defaults */
.cora-shopify-card-body {
    display: none; /* hidden by default */
    padding: 20px !important;
    animation: coraFadeIn 0.2s ease-out;
}
@media (max-width: 639px) {
    .cora-shopify-card-body {
        padding: 14px !important;
    }
}
/* Class-specific body padding overrides */
.cora-shopify-card-body.pt-0 {
    padding-top: 0 !important;
}
.cora-shopify-card-body.pb-0 {
    padding-bottom: 0 !important;
}
.cora-shopify-card-body.px-0 {
    padding-left: 0 !important;
    padding-right: 0 !important;
}
.cora-shopify-card-body.p-0 {
    padding: 0 !important;
}

@keyframes coraFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

.cora-card-chevron {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transform: rotate(180deg) !important; /* Point down by default when collapsed */
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.cora-card-chevron.active {
    transform: rotate(0deg) !important; /* Point up when expanded */
}
.cora-credential-input {
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
}
</style>

<div class="cora-shopify-settings-theme"><div class="cora-page-header flex flex-row items-center justify-between gap-4 border-b border-zinc-150/70 dark:border-zinc-800/40 pb-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-lg sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white m-0">System Settings Complete Suite</h1>
            <p class="text-xs text-zinc-500 mt-0.5 hidden sm:block">Global network parameters, reading/writing defaults, discussion moderation rules, and SEO permalinks.</p>
        </div>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <button type="button" class="p-2 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-350 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-3xs cursor-pointer" onclick="coraClearSystemCache()" title="Clear Cache">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
        </button>
        <button type="button" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-sm active:scale-97" onclick="coraSaveSystemSettingsSuite()">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            <span class="hidden sm:inline">Save All Settings</span>
            <span class="sm:hidden">Save All</span>
        </button>
    </div>
</div>

<!-- Mobile Horizontal Tab Strip (Hidden on Desktop) -->
<div class="lg:hidden flex overflow-x-auto gap-5 pb-0.5 mb-4 scrollbar-none border-b border-zinc-200/50 dark:border-zinc-800/40 w-full mt-4" style="-webkit-overflow-scrolling: touch;">
    <?php
    $tabs = $cora_settings_tabs;
    foreach ( $tabs as $tab_key => $tab ) :
        $is_active = ( $active_tab === $tab_key );
    ?>
    <a href="#" onclick="window.coraSwitchSettingsTab('<?php echo esc_js( $tab_key ); ?>'); return false;" data-settings-tab-mobile="<?php echo esc_attr( $tab_key ); ?>" class="cora-settings-nav-mobile flex items-center gap-1.5 pb-2 border-b-2 whitespace-nowrap transition-all shrink-0 <?php echo $is_active ? 'border-zinc-950 text-zinc-950 dark:border-white dark:text-white font-bold active-tab' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-850 dark:hover:text-zinc-250'; ?>">
        <span class="shrink-0">
            <?php echo $tab['icon']; ?>
        </span>
        <span class="text-xs"><?php echo esc_html( $tab['label'] ); ?></span>
    </a>
    <?php endforeach; ?>
</div>

<!-- Settings Sidebar Grid Layout -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mt-6">
    <!-- Left Column: Navigation Sidebar (Hidden on Mobile) -->
    <div class="hidden lg:block lg:col-span-1 space-y-1.5 pb-20">
        <?php
        foreach ( $tabs as $tab_key => $tab ) :
            $is_active = ( $active_tab === $tab_key );
        ?>
        <a href="#" onclick="window.coraSwitchSettingsTab('<?php echo esc_js( $tab_key ); ?>'); return false;" data-settings-tab="<?php echo esc_attr( $tab_key ); ?>" class="cora-settings-nav-item <?php echo $is_active ? 'active' : ''; ?>">
            <div class="<?php echo $is_active ? 'text-zinc-800 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-555 group-hover:text-zinc-700'; ?> shrink-0">
                <?php echo $tab['icon']; ?>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] font-bold leading-tight <?php echo $is_active ? 'text-zinc-900 dark:text-white' : 'text-zinc-750 dark:text-zinc-350'; ?>"><?php echo esc_html( $tab['label'] ); ?></div>
                <div class="text-[9px] mt-0.5 truncate <?php echo $is_active ? 'text-zinc-500 dark:text-zinc-400' : 'text-zinc-400 dark:text-zinc-500'; ?>"><?php echo esc_html( $tab['desc'] ); ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Right Column: Settings Form Content (Shopify-Style Discrete Cards) -->
    <div class="lg:col-span-3 pb-24">
        <form id="cora-settings-suite-form" onsubmit="event.preventDefault(); coraSaveSystemSettingsSuite();" class="space-y-6">
            <input type="hidden" name="active_tab" value="<?php echo esc_attr( $active_tab ); ?>">

        <!-- TAB 1: GENERAL SETTINGS -->
        <div id="cora-settings-panel-general" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'general' ? '' : 'hidden'; ?>">
            <!-- Card 1: General Site Configuration -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">General Site Configuration</h3>
                        <p class="text-xs text-zinc-500 m-0">Core identity and default user registration parameters.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center gap-1.5" title="This title appears on your browser tab and platform header.">
                            Site Title
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </label>
                        <?php
                        $current_industry = ! empty( $_COOKIE['cora_workspace_industry'] ) 
                            ? $_COOKIE['cora_workspace_industry'] 
                            : get_option( 'cora_workspace_industry', 'real_estate' );
                        $current_industry_clean = str_replace( '_', '-', strtolower( trim( $current_industry ) ) );
                        $is_studio = ( $current_industry_clean === 'photography' || $current_industry_clean === 'studio' || $current_industry_clean === 'photography-studio' );

                        $title_real_estate = get_option( 'cora_site_title_real_estate', '' );
                        $title_studio      = get_option( 'cora_site_title_studio', '' );
                        $active_site_title = $is_studio ? $title_studio : $title_real_estate;

                        $desc_real_estate = get_option( 'cora_tagline_real_estate', '' );
                        $desc_studio      = get_option( 'cora_tagline_studio', '' );
                        $active_tagline    = $is_studio ? $desc_studio : $desc_real_estate;
                        ?>
                        <input type="text" name="blogname" id="cora-site-title-input" value="<?php echo esc_attr( $active_site_title ); ?>" placeholder="Cora">
                    </div>
                    <div>
                        <label class="flex items-center gap-1.5" title="This title appears on your browser tab and platform header.">
                            Tagline / Subtitle
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </label>
                        <input type="text" name="blogdescription" id="cora-site-tagline-input" value="<?php echo esc_attr( $active_tagline ); ?>" placeholder="Luxury Properties & Studio Suite">
                    </div>
                    <div>
                        <label class="flex items-center gap-1.5" title="Changes the top-left sidebar title text">
                            Sidebar Brand Title
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </label>
                        <input type="text" name="cora_sidebar_title" value="<?php echo esc_attr( get_option('cora_sidebar_title', 'Cora') ); ?>">
                    </div>
                    <div>
                        <label>Administration Email Address</label>
                        <input type="email" name="admin_email" value="<?php echo esc_attr( get_option('admin_email') ); ?>">
                    </div>
                    <div>
                        <label>New User Default Role</label>
                        <?php $default_role_val = get_option('default_role'); ?>
                        <select name="default_role" id="cora-default-role-select">
                            <?php if ( ! $is_studio ) : ?>
                                <optgroup label="Real Estate Industry Roles" class="cora-role-optgroup-real_estate">
                                    <option value="cora_workspace_owner" <?php selected( $default_role_val, 'cora_workspace_owner' ); ?>>Workspace Owner</option>
                                    <option value="cora_manager" <?php selected( $default_role_val, 'cora_manager' ); ?>>Manager</option>
                                    <option value="cora_branch_manager" <?php selected( $default_role_val, 'cora_branch_manager' ); ?>>Branch Manager</option>
                                    <option value="cora_re_agent" <?php selected( $default_role_val, 'cora_re_agent' ); ?>>Real Estate Agent</option>
                                    <option value="cora_lead_coordinator" <?php selected( $default_role_val, 'cora_lead_coordinator' ); ?>>Lead Coordinator</option>
                                </optgroup>
                            <?php else : ?>
                                <optgroup label="Studio & Media Roles" class="cora-role-optgroup-photography">
                                    <option value="cora_photographer" <?php selected( $default_role_val, 'cora_photographer' ); ?>>Photographer</option>
                                    <option value="cora_videographer" <?php selected( $default_role_val, 'cora_videographer' ); ?>>Videographer</option>
                                    <option value="cora_drone_pilot" <?php selected( $default_role_val, 'cora_drone_pilot' ); ?>>Drone Pilot</option>
                                    <option value="cora_editor" <?php selected( $default_role_val, 'cora_editor' ); ?>>Photo / Video Editor</option>
                                    <option value="cora_studio_manager" <?php selected( $default_role_val, 'cora_studio_manager' ); ?>>Studio Manager</option>
                                    <option value="cora_workspace_owner" <?php selected( $default_role_val, 'cora_workspace_owner' ); ?>>Workspace Owner</option>
                                </optgroup>
                            <?php endif; ?>
                            <optgroup label="Core & Standard Roles">
                                <option value="subscriber" <?php selected( $default_role_val, 'subscriber' ); ?>>Subscriber (Client / Portal)</option>
                                <option value="administrator" <?php selected( $default_role_val, 'administrator' ); ?>>Administrator</option>
                                <option value="editor" <?php selected( $default_role_val, 'editor' ); ?>>Editor</option>
                                <option value="author" <?php selected( $default_role_val, 'author' ); ?>>Author</option>
                                <option value="contributor" <?php selected( $default_role_val, 'contributor' ); ?>>Contributor</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="pt-2">
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="users_can_register" value="1" <?php checked( get_option('users_can_register'), 1 ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                        <span class="cora-label-raw">Membership: Anyone can register for an account</span>
                    </label>
                </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>

            <!-- Card 2: Workspace Details Section -->
            <div class="cora-shopify-card expanded">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">General Workspace Settings</h3>
                        <p class="text-xs text-zinc-500 m-0">Corporate identity, localized workspace address, and billing tax descriptors.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center gap-1.5" title="This title appears on your browser tab and platform header.">
                            Workspace Name
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </label>
                        <input type="text" name="cora_workspace_name" value="<?php echo esc_attr( get_option('cora_workspace_name', 'Cora Studio') ); ?>" placeholder="e.g. Mumbai Main Office">
                    </div>
                    <div>
                        <label>Tax Registration Details</label>
                        <input type="text" name="cora_workspace_tax_details" value="<?php echo esc_attr( get_option('cora_workspace_tax_details', 'GSTIN: 27AAAAA1111A1Z1') ); ?>" placeholder="e.g. VAT / GSTIN / PAN details">
                    </div>
                    <div>
                        <label>Workspace Industry Profile</label>
                        <select name="cora_workspace_industry" id="cora-settings-industry-select" onchange="coraFilterRolesByIndustry(this.value);" style="width: 100%; padding: 10px 14px; font-size: 14px; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); outline: none; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit;">
                            <?php $industry = $current_industry; ?>
                            <option value="real_estate" <?php selected( $industry, 'real_estate' ); ?>>Real Estate Agency</option>
                            <option value="photography_studio" <?php selected( $industry, 'photography_studio' ); ?>>Photography Studio</option>
                        </select>
                    </div>

                    <script>
                    window.coraBrandingValues = {
                        titleRE: <?php echo json_encode( $title_real_estate ); ?>,
                        titleST: <?php echo json_encode( $title_studio ); ?>,
                        descRE: <?php echo json_encode( $desc_real_estate ); ?>,
                        descST: <?php echo json_encode( $desc_studio ); ?>
                    };

                    function coraFilterRolesByIndustry(industry) {
                        if (!industry) {
                            industry = $('#cora-settings-industry-select').val() || 'real_estate';
                        }
                        if (industry === 'studio' || industry === 'photography') industry = 'photography_studio';
                        
                        const select = $('#cora-default-role-select');
                        const siteTitleInput = $('#cora-site-title-input');
                        const taglineInput   = $('#cora-site-tagline-input');
                        
                        const titleRE = window.coraBrandingValues.titleRE;
                        const titleST = window.coraBrandingValues.titleST;
                        const descRE  = window.coraBrandingValues.descRE;
                        const descST  = window.coraBrandingValues.descST;
                        
                        if (industry === 'real_estate') {
                            if (siteTitleInput.length && (!siteTitleInput.data('user-edited'))) siteTitleInput.val(titleRE);
                            if (taglineInput.length && (!taglineInput.data('user-edited'))) taglineInput.val(descRE);
                        } else {
                            if (siteTitleInput.length && (!siteTitleInput.data('user-edited'))) siteTitleInput.val(titleST);
                            if (taglineInput.length && (!taglineInput.data('user-edited'))) taglineInput.val(descST);
                        }

                        if (!select.length) return;
                        const currentVal = select.val();

                        const realEstateRoles = `
                            <optgroup label="Real Estate Industry Roles" class="cora-role-optgroup-real_estate">
                                <option value="cora_workspace_owner">Workspace Owner</option>
                                <option value="cora_manager">Manager</option>
                                <option value="cora_branch_manager">Branch Manager</option>
                                <option value="cora_re_agent">Real Estate Agent</option>
                                <option value="cora_lead_coordinator">Lead Coordinator</option>
                            </optgroup>
                        `;

                        const studioRoles = `
                            <optgroup label="Studio & Media Roles" class="cora-role-optgroup-photography">
                                <option value="cora_photographer">Photographer</option>
                                <option value="cora_videographer">Videographer</option>
                                <option value="cora_drone_pilot">Drone Pilot</option>
                                <option value="cora_editor">Photo / Video Editor</option>
                                <option value="cora_studio_manager">Studio Manager</option>
                                <option value="cora_workspace_owner">Workspace Owner</option>
                            </optgroup>
                        `;

                        const standardRoles = `
                            <optgroup label="Core & Standard Roles">
                                <option value="subscriber">Subscriber (Client / Portal)</option>
                                <option value="administrator">Administrator</option>
                                <option value="editor">Editor</option>
                                <option value="author">Author</option>
                                <option value="contributor">Contributor</option>
                            </optgroup>
                        `;

                        if (industry === 'real_estate') {
                            select.html(realEstateRoles + standardRoles);
                        } else {
                            select.html(studioRoles + standardRoles);
                        }

                        if (currentVal && select.find('option[value="' + currentVal + '"]').length) {
                            select.val(currentVal);
                        }

                        if (window.coraApplyBrandingLive) window.coraApplyBrandingLive();
                    }
                    (function($) {
                        $('#cora-site-title-input, #cora-site-tagline-input').on('input', function() {
                            $(this).data('user-edited', true);
                        });
                        const currentInd = $('#cora-settings-industry-select').val() || '<?php echo esc_js($is_studio ? "photography_studio" : "real_estate"); ?>';
                        coraFilterRolesByIndustry(currentInd);
                    })(jQuery);
                    </script>
                    <div>
                        <label>Platform Language</label>
                        <select id="cora-settings-suite-language-select" name="cora_workspace_language" class="cora-language-selector" style="width: 100%; padding: 10px 14px; font-size: 14px; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); outline: none; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit;" onchange="if(window.coraSetLanguage) window.coraSetLanguage(this.value, true);">
                            <?php $lang = get_option('cora_workspace_language', 'en'); ?>
                            <option value="en" <?php selected( $lang, 'en' ); ?>>English</option>
                            <option value="hi" <?php selected( $lang, 'hi' ); ?>>Hindi (हिन्दी)</option>
                            <option value="es" <?php selected( $lang, 'es' ); ?>>Spanish (Español)</option>
                            <option value="fr" <?php selected( $lang, 'fr' ); ?>>French (Français)</option>
                            <option value="de" <?php selected( $lang, 'de' ); ?>>German (Deutsch)</option>
                            <option value="bn" <?php selected( $lang, 'bn' ); ?>>Bengali (বাংলা)</option>
                            <option value="te" <?php selected( $lang, 'te' ); ?>>Telugu (తెలుగు)</option>
                            <option value="mr" <?php selected( $lang, 'mr' ); ?>>Marathi (मराठी)</option>
                            <option value="ta" <?php selected( $lang, 'ta' ); ?>>Tamil (தமிழ்)</option>
                            <option value="gu" <?php selected( $lang, 'gu' ); ?>>Gujarati (ગુજરાતી)</option>
                            <option value="kn" <?php selected( $lang, 'kn' ); ?>>Kannada (ಕನ್ನಡ)</option>
                            <option value="ml" <?php selected( $lang, 'ml' ); ?>>Malayalam (മലയാളം)</option>
                            <option value="pa" <?php selected( $lang, 'pa' ); ?>>Punjabi (ਪੰਜਾਬੀ)</option>
                            <option value="or" <?php selected( $lang, 'or' ); ?>>Odia (ଓଡ଼ିଆ)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label>Workspace Address</label>
                        <input type="text" name="cora_workspace_address" value="<?php echo esc_attr( get_option('cora_workspace_address', '101, BKC Road, Bandra East, Mumbai') ); ?>" placeholder="Full physical office location">
                    </div>
                    <div class="sm:col-span-2">
                        <label>Activity Log Auto-Archive Threshold</label>
                        <select name="cora_activity_logs_retention">
                            <?php $retention = get_option('cora_activity_logs_retention', 0); ?>
                            <option value="0" <?php selected( $retention, 0 ); ?>>Never (Keep all logs)</option>
                            <option value="30" <?php selected( $retention, 30 ); ?>>30 Days</option>
                            <option value="90" <?php selected( $retention, 90 ); ?>>90 Days</option>
                            <option value="180" <?php selected( $retention, 180 ); ?>>180 Days</option>
                            <option value="365" <?php selected( $retention, 365 ); ?>>1 Year</option>
                        </select>
                        <p class="text-[10px] text-zinc-400 mt-1">Prune system activity log events older than the selection to optimize database performance.</p>
                    </div>
                </div>
                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800/40 mt-4">
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="cora_workspace_allow_tours" value="1" <?php checked( get_option('cora_workspace_allow_tours', 1), 1 ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                        <span class="cora-label-raw">Enable Workspace Interactive Tour guides for first-time logins</span>
                    </label>
                </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>

            <!-- Card 3: Database Clean Up Section -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-red-650 m-0">Database Optimization</h3>
                        <p class="text-xs text-zinc-500 m-0">Clean up legacy key-value storage once you have verified custom database tables are fully working.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                    <div class="p-4 border border-red-200 dark:border-red-900/60 bg-red-50/50 dark:bg-red-950/10 rounded-lg space-y-3">
                    <p class="text-xs text-zinc-700 dark:text-zinc-300">
                        Purging legacy data removes redundant options storage and clears temporary system caches.
                        <strong>Note:</strong> Make sure you have verified data integrity before purging.
                    </p>
                    <button type="button" id="cora-purge-legacy-options" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        Purge Old System Cache
                    </button>
                </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>
        </div>

        <!-- TAB 2: PASSWORD POLICY SETTINGS -->
        <div id="cora-settings-panel-pwd-policy" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'pwd-policy' ? '' : 'hidden'; ?>">
            <!-- Card: Password Policy -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Workspace Password Policy</h3>
                        <p class="text-xs text-zinc-500 m-0">Enforce minimum complexity guidelines for passwords across logins, setups, and resets.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                    <div class="space-y-4">
                    <div class="w-48">
                        <label>Minimum Password Length</label>
                        <input type="number" min="6" max="32" name="cora_pwd_policy_min_len" value="<?php echo esc_attr( get_option('cora_pwd_policy_min_len', 8) ); ?>">
                    </div>

                    <div class="pt-2 space-y-3">
                        <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                            <input type="checkbox" name="cora_pwd_policy_numbers" value="1" <?php checked( get_option('cora_pwd_policy_numbers', 0), 1 ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                            <span class="cora-label-raw">Require at least one number (0-9)</span>
                        </label>

                        <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                            <input type="checkbox" name="cora_pwd_policy_uppercase" value="1" <?php checked( get_option('cora_pwd_policy_uppercase', 0), 1 ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                            <span class="cora-label-raw">Require at least one uppercase letter (A-Z)</span>
                        </label>

                        <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                            <input type="checkbox" name="cora_pwd_policy_special" value="1" <?php checked( get_option('cora_pwd_policy_special', 0), 1 ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                            <span class="cora-label-raw">Require at least one special character (e.g. !, @, #, $, %, etc.)</span>
                        </label>
                    </div>
                </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>
        </div>

        <?php
            $agency_id = cora_get_current_user_agency_id();
            $branches  = cora_db_get_branches();
            $filtered_branches = $branches;

            // Count active agents per branch
            $all_wp_users = get_users();
            $branch_agent_counts = array();
            foreach ( $all_wp_users as $u ) {
                $u_branch = get_user_meta( $u->ID, 'cora_branch_id', true );
                if ( ! empty( $u_branch ) ) {
                    if ( ! isset( $branch_agent_counts[$u_branch] ) ) {
                        $branch_agent_counts[$u_branch] = 0;
                    }
                    $branch_agent_counts[$u_branch]++;
                }
            }

            // Find all potential branch managers in this agency
            $manager_query_args = array(
                'role__in' => array( 'cora_branch_manager', 'cora_manager', 'administrator' )
            );
            if ( $agency_id !== 'super' ) {
                $manager_query_args['meta_query'] = array(
                    array(
                        'key'     => 'cora_agency_id',
                        'value'   => function_exists('cora_get_agency_identifiers') ? cora_get_agency_identifiers( $agency_id ) : $agency_id,
                        'compare' => 'IN'
                    )
                );
            }
            $potential_managers = get_users( $manager_query_args );

            // Find currently assigned managers for 1:1 check
            $assigned_managers = array();
            foreach ( $filtered_branches as $b_id => $b ) {
                if ( ! empty( $b['manager_id'] ) ) {
                    $assigned_managers[ intval( $b['manager_id'] ) ] = $b_id;
                }
            }
        ?>
        <!-- TAB 3: BRANCH MANAGEMENT -->
        <div id="cora-settings-panel-branches" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'branches' ? '' : 'hidden'; ?>">
            <div class="cora-shopify-card p-0 overflow-hidden">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 p-4 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Brokerage Branches</h3>
                        <p class="text-xs text-zinc-500 m-0">Manage multiple physical offices, assign localized managers, and monitor regional agent counts.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="event.stopPropagation(); openCreateBranchDrawer()" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            New Branch
                        </button>
                        <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                        </span>
                    </div>
                </div>
                <div class="cora-shopify-card-body pt-0">
                    <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                        <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                            <thead class="bg-zinc-50/50">
                                <tr>
                                    <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Branch Name</th>
                                    <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Location / Address</th>
                                    <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Branch Manager</th>
                                    <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Active Crew</th>
                                    <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                <?php if ( empty( $filtered_branches ) ) : ?>
                                    <tr>
                                        <td colspan="5" class="px-5 py-8 text-center text-zinc-400 font-medium">No branches configured.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ( $filtered_branches as $b_id => $b ) :
                                        $mgr = ! empty( $b['manager_id'] ) ? get_userdata( $b['manager_id'] ) : null;
                                        $mgr_name = $mgr ? $mgr->display_name : 'Unassigned';
                                        $crew_count = $branch_agent_counts[$b_id] ?? 0;
                                    ?>
                                        <tr class="hover:bg-zinc-50/10 dark:hover:bg-zinc-800/10">
                                            <td class="px-5 py-3.5 font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $b['name'] ); ?></td>
                                            <td class="px-5 py-3.5 text-zinc-500 dark:text-zinc-400 font-semibold"><?php echo esc_html( $b['city'] . ' / ' . $b['address'] ); ?></td>
                                            <td class="px-5 py-3.5 font-semibold text-zinc-700 dark:text-zinc-300">
                                                <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 text-[9px] font-bold">
                                                    <?php echo esc_html( $mgr_name ); ?>
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $crew_count ); ?> Agents</td>
                                            <td class="px-5 py-3.5 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button" onclick="openEditBranchDrawer('<?php echo esc_attr($b_id); ?>', '<?php echo esc_attr($b['name']); ?>', '<?php echo esc_attr($b['city']); ?>', '<?php echo esc_attr($b['address']); ?>', '<?php echo esc_attr($b['manager_id'] ?? ''); ?>')" class="px-2.5 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer shadow-sm transition-colors">Edit</button>
                                                    <button type="button" onclick="deleteBranch('<?php echo esc_attr($b_id); ?>', <?php echo $crew_count; ?>)" class="px-2.5 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-bold text-red-650 dark:text-red-400 bg-white dark:bg-zinc-900 hover:bg-red-50 dark:hover:bg-red-950/30 hover:border-red-200 dark:hover:border-red-800 cursor-pointer shadow-sm transition-colors">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>

        <!-- ═══ CREATE BRANCH DRAWER SHEET ══════════════════════════════════════════ -->
        <div id="drawer-create-branch" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-create-branch-card">
                <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
                    <h3 class="text-sm font-bold text-zinc-900">Configure New Branch</h3>
                    <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeCreateBranchDrawer()">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Branch Office Name</label>
                        <input type="text" id="new-branch-name" required placeholder="e.g. Westside HQ" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">City</label>
                        <input type="text" id="new-branch-city" required placeholder="e.g. Mumbai" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Office Address</label>
                        <input type="text" id="new-branch-address" required placeholder="e.g. 402, Bandra Kurla Complex" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Assign Branch Manager</label>
                        <select id="new-branch-manager" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                            <option value="">— Unassigned —</option>
                            <?php foreach ( $potential_managers as $pm ) :
                                $already_assigned = isset($assigned_managers[$pm->ID]);
                                $label_suffix = $already_assigned ? ' (Already managing another branch)' : '';
                            ?>
                                <option value="<?php echo esc_attr( $pm->ID ); ?>" <?php if ($already_assigned) echo 'disabled style="color:#a1a1aa;"'; ?>><?php echo esc_html( $pm->display_name . $label_suffix ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="pt-4">
                        <button type="button" onclick="handleCreateBranch(event)" id="create-branch-btn" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Initialize Branch</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ EDIT BRANCH DRAWER SHEET ════════════════════════════════════════════ -->
        <div id="drawer-edit-branch" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-edit-branch-card">
                <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
                    <h3 class="text-sm font-bold text-zinc-900">Modify Branch Details</h3>
                    <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeEditBranchDrawer()">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-5">
                    <input type="hidden" id="edit-branch-id">
                    
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Branch Office Name</label>
                        <input type="text" id="edit-branch-name" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">City</label>
                        <input type="text" id="edit-branch-city" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Office Address</label>
                        <input type="text" id="edit-branch-address" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Assign Branch Manager</label>
                        <select id="edit-branch-manager" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                            <option value="">— Unassigned —</option>
                            <?php foreach ( $potential_managers as $pm ) :
                                $already_assigned = isset($assigned_managers[$pm->ID]);
                                $current_assigned_to_this = ($already_assigned && $assigned_managers[$pm->ID] === 'this_placeholder'); // Will be updated dynamically in JS
                            ?>
                                <option value="<?php echo esc_attr( $pm->ID ); ?>" data-assigned-to="<?php echo esc_attr( $assigned_managers[$pm->ID] ?? '' ); ?>"><?php echo esc_html( $pm->display_name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="pt-4">
                        <button type="button" onclick="handleEditBranch(event)" id="edit-branch-btn" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Save Shifts</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openCreateBranchDrawer() {
                $('#drawer-create-branch').removeClass('opacity-0 pointer-events-none');
                $('#drawer-create-branch').css({'opacity': '1', 'pointer-events': 'auto'});
                $('#drawer-create-branch-card').removeClass('translate-x-full').addClass('translate-x-0');
            }
            function closeCreateBranchDrawer() {
                $('#drawer-create-branch-card').removeClass('translate-x-0').addClass('translate-x-full');
                setTimeout(function() {
                    $('#drawer-create-branch').addClass('opacity-0 pointer-events-none');
                    $('#drawer-create-branch').css({'opacity': '0', 'pointer-events': 'none'});
                }, 300);
                $('#new-branch-name').val('');
                $('#new-branch-city').val('');
                $('#new-branch-address').val('');
                $('#new-branch-manager').val('');
            }

            function openEditBranchDrawer(id, name, city, address, managerId) {
                $('#edit-branch-id').val(id);
                $('#edit-branch-name').val(name);
                $('#edit-branch-city').val(city);
                $('#edit-branch-address').val(address);
                
                // Set manager dropdown options and disable managers assigned to OTHER branches
                $('#edit-branch-manager option').each(function() {
                    var assignedBranch = $(this).data('assigned-to') || '';
                    if (assignedBranch !== '' && assignedBranch !== id) {
                        $(this).prop('disabled', true).text($(this).text().split(' (Already')[0] + ' (Already managing another branch)').css('color', '#a1a1aa');
                    } else {
                        $(this).prop('disabled', false).text($(this).text().split(' (Already')[0]).css('color', '');
                    }
                });

                $('#edit-branch-manager').val(managerId);

                $('#drawer-edit-branch').removeClass('opacity-0 pointer-events-none');
                $('#drawer-edit-branch').css({'opacity': '1', 'pointer-events': 'auto'});
                $('#drawer-edit-branch-card').removeClass('translate-x-full').addClass('translate-x-0');
            }

            function closeEditBranchDrawer() {
                $('#drawer-edit-branch-card').removeClass('translate-x-0').addClass('translate-x-full');
                setTimeout(function() {
                    $('#drawer-edit-branch').addClass('opacity-0 pointer-events-none');
                    $('#drawer-edit-branch').css({'opacity': '0', 'pointer-events': 'none'});
                }, 300);
            }

            function handleCreateBranch(e) {
                if (e && e.preventDefault) e.preventDefault();
                var name = $('#new-branch-name').val().trim();
                var city = $('#new-branch-city').val().trim();
                var address = $('#new-branch-address').val().trim();
                var manager = $('#new-branch-manager').val();

                if (!name || !city || !address) {
                    window.coraShowToast('Please fill all required fields.');
                    return;
                }

                $('#create-branch-btn').prop('disabled', true).text('Initializing branch...');

                $.post(coraREData.ajaxUrl, {
                    action: 'cora_ajax_save_branch',
                    branch_name: name,
                    city: city,
                    address: address,
                    manager_id: manager,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res.success) {
                        window.coraShowToast('Branch saved successfully.');
                        closeCreateBranchDrawer();
                        setTimeout(function() { window.location.reload(); }, 1000);
                    } else {
                        window.coraShowToast(res.data.message || 'Failed to initialize branch.');
                        $('#create-branch-btn').prop('disabled', false).text('Initialize Branch');
                    }
                });
            }

            function handleEditBranch(e) {
                if (e && e.preventDefault) e.preventDefault();
                var id = $('#edit-branch-id').val();
                var name = $('#edit-branch-name').val().trim();
                var city = $('#edit-branch-city').val().trim();
                var address = $('#edit-branch-address').val().trim();
                var manager = $('#edit-branch-manager').val();

                if (!name || !city || !address) {
                    window.coraShowToast('Please fill all required fields.');
                    return;
                }

                $('#edit-branch-btn').prop('disabled', true).text('Saving shifts...');

                $.post(coraREData.ajaxUrl, {
                    action: 'cora_ajax_save_branch',
                    branch_id: id,
                    branch_name: name,
                    city: city,
                    address: address,
                    manager_id: manager,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res.success) {
                        window.coraShowToast('Branch saved successfully.');
                        closeEditBranchDrawer();
                        setTimeout(function() { window.location.reload(); }, 1000);
                    } else {
                        window.coraShowToast(res.data.message || 'Failed to save branch.');
                        $('#edit-branch-btn').prop('disabled', false).text('Save Shifts');
                    }
                });
            }

            function deleteBranch(id, crewCount) {
                if (crewCount > 0) {
                    window.coraShowToast('You cannot delete a branch with active team members. Reassign all members first.');
                    return;
                }

                window.coraConfirmAction(
                    'Confirm Deletion',
                    'Are you sure you want to delete this branch?',
                    function() {
                        window.coraShowToast('Deleting branch...');
                        $.post(coraREData.ajaxUrl, {
                            action: 'cora_ajax_delete_branch',
                            branch_id: id,
                            nonce: coraREData.ajaxNonce
                        }, function(res) {
                            if (res.success) {
                                window.coraShowToast('Branch deleted successfully.');
                                setTimeout(function() { window.location.reload(); }, 800);
                            } else {
                                window.coraShowToast(res.data.message || 'Failed to delete branch.');
                            }
                        });
                    }
                );
            }
        </script>

        </div>
        <!-- TAB 4: BRANDING & API KEYS -->
        <div id="cora-settings-panel-brand" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'brand' ? '' : 'hidden'; ?>">
            <!-- Card 1: Brand Assets -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Brand Assets</h3>
                        <p class="text-xs text-zinc-500 m-0">Configure your agency's logo and browser favicon.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                
                <!-- Agency Logo Settings -->
                <div class="p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/40 space-y-3">
                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300">Agency Logo</label>
                    <input type="hidden" id="cora-brand-logo-url-suite" name="cora_brand_logo_url" value="<?php echo esc_url( get_option('cora_brand_logo_url', '') ); ?>">
                    
                    <div class="flex items-center gap-5">
                        <!-- Preview Box / Upload Dropzone -->
                        <div id="cora-suite-logo-dropzone" class="w-full max-w-sm h-32 border-2 border-dashed border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-700 rounded-2xl bg-white dark:bg-zinc-950 flex flex-col items-center justify-center p-4 cursor-pointer transition-all group" onclick="coraOpenMediaSelector('cora-brand-logo-url-suite')">
                            <?php $logo_url = get_option('cora_brand_logo_url', ''); ?>
                            <div id="cora-suite-logo-preview" class="w-full h-full flex flex-col items-center justify-center overflow-hidden">
                                <?php if ( ! empty( $logo_url ) ) : ?>
                                    <img src="<?php echo esc_url( $logo_url ); ?>" class="max-h-full max-w-full object-contain transition-transform group-hover:scale-102" alt="Logo Preview">
                                <?php else : ?>
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-650 dark:group-hover:text-zinc-300 transition-colors mb-1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <span class="text-xs font-bold text-zinc-850 dark:text-zinc-200">Upload Agency Logo</span>
                                    <span class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Recommended size: 250x80px (PNG/JPG)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Control Actions -->
                        <div class="space-y-2">
                            <button type="button" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-xs rounded-xl shadow-2xs transition-all cursor-pointer flex items-center gap-1.5" onclick="coraOpenMediaSelector('cora-brand-logo-url-suite')">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                Choose File
                            </button>
                            <button type="button" id="cora-suite-logo-clear" class="px-3.5 py-2 bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800/60 text-red-500 hover:text-red-650 font-semibold text-xs rounded-xl transition-all cursor-pointer <?php echo empty($logo_url) ? 'hidden' : ''; ?>" onclick="document.getElementById('cora-brand-logo-url-suite').value=''; jQuery('#cora-brand-logo-url-suite').trigger('change');">
                                Remove Logo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Custom Favicon Settings -->
                <div class="p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/40 space-y-3">
                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300">Custom Favicon (32x32 / 64x64 PNG)</label>
                    <input type="hidden" id="cora-brand-favicon-url-suite" name="cora_brand_favicon_url" value="<?php echo esc_url( get_option('cora_brand_favicon_url', '') ); ?>">
                    
                    <div class="flex items-center gap-5">
                        <!-- Preview Box / Upload Dropzone -->
                        <div id="cora-suite-favicon-dropzone" class="w-24 h-24 border-2 border-dashed border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-700 rounded-2xl bg-white dark:bg-zinc-950 flex flex-col items-center justify-center p-3 cursor-pointer transition-all group shrink-0" onclick="coraOpenMediaSelector('cora-brand-favicon-url-suite')">
                            <?php 
                            $favicon_url = get_option('cora_brand_favicon_url', ''); 
                            if ( empty( $favicon_url ) ) {
                                $favicon_url = CORA_WORKSPACE_URL . 'assets/images/cora-favicon.png';
                            }
                            ?>
                            <div id="cora-suite-favicon-preview" class="w-full h-full flex flex-col items-center justify-center overflow-hidden">
                                <?php if ( ! empty( $favicon_url ) ) : ?>
                                    <img src="<?php echo esc_url( $favicon_url ); ?>" class="w-8 h-8 object-contain transition-transform group-hover:scale-105" alt="Favicon Preview">
                                <?php else : ?>
                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-650 dark:group-hover:text-zinc-350 transition-colors mb-1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <span class="text-[9px] text-zinc-400 font-bold uppercase tracking-wider">Upload</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Control Actions -->
                        <div class="space-y-1.5">
                            <div class="flex gap-2">
                                <button type="button" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-[11px] rounded-lg shadow-2xs transition-all cursor-pointer flex items-center gap-1" onclick="coraOpenMediaSelector('cora-brand-favicon-url-suite')">
                                    Choose Icon
                                </button>
                                <button type="button" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px] rounded-lg transition-colors cursor-pointer" onclick="coraSetDefaultPremiumFavicon()">
                                    Use Premium Monogram
                                </button>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" id="cora-suite-favicon-clear" class="px-2.5 py-1 text-red-500 hover:text-red-650 font-semibold text-[10px] rounded transition-all cursor-pointer <?php echo empty(get_option('cora_brand_favicon_url', '')) ? 'hidden' : ''; ?>" onclick="document.getElementById('cora-brand-favicon-url-suite').value=''; jQuery('#cora-brand-favicon-url-suite').trigger('change');">
                                    Reset to Default
                                </button>
                                <button type="button" class="px-2.5 py-1 text-zinc-600 dark:text-zinc-400 hover:text-zinc-850 dark:hover:text-zinc-200 font-semibold text-[10px] rounded transition-all cursor-pointer" onclick="if(window.coraApplyBrandingLive) window.coraApplyBrandingLive();">
                                    Apply Live
                                </button>
                            </div>
                        </div>
                    </div>
                </div> <!-- close custom favicon settings -->
                </div> <!-- close cora-shopify-card-body -->
            </div> <!-- close cora-shopify-card -->

                <!-- Browser Tab & Sidebar Title Settings -->
                <div class="cora-shopify-card">
                    <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Browser Tab &amp; Sidebar Title Settings</h3>
                            <p class="text-xs text-zinc-500 mt-0.5 m-0">Control how your site name appears in the browser tab and sidebar.</p>
                        </div>
                        <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                        </span>
                    </div>
                    <div class="cora-shopify-card-body pt-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label>Site Title</label>
                            <input type="text" name="blogname" value="<?php echo esc_attr( get_option('blogname') ); ?>" placeholder="Cora">
                        </div>
                        <div>
                            <label>Sidebar Brand Title</label>
                            <input type="text" name="cora_sidebar_title" value="<?php echo esc_attr( get_option('cora_sidebar_title', 'cora') ); ?>" placeholder="Cora">
                        </div>
                        <div class="sm:col-span-2">
                            <label>Browser Tab Title Template</label>
                            <input type="text" disabled value="[Page Name] – <?php echo esc_attr( get_option('blogname') ); ?>" class="bg-zinc-100 dark:bg-zinc-800 text-zinc-500 cursor-not-allowed">
                            <p class="text-[11px] text-zinc-400 mt-1.5">This is how your tab titles will appear across the workspace.</p>
                        </div>
                    </div> <!-- close cora-shopify-card-body -->
                </div>
            </div>

            <!-- Card 2: Developer Keys & API Integrations -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Third-Party Developer Keys</h3>
                        <p class="text-xs text-zinc-500 m-0">Provide map keys and CRM notification credentials.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label>Google Maps API Key</label>
                        <?php $maps_key = get_option('cora_gbp_maps_api_key', ''); ?>
                        <input type="password" name="cora_gbp_maps_api_key" value="<?php echo esc_attr( $maps_key ? str_repeat('•', 24) : '' ); ?>" placeholder="AIzaSy..." class="cora-credential-input" oncopy="return false;" oncut="return false;" ondragstart="return false;" ondrop="return false;" autocomplete="off">
                        <p class="text-[10px] text-zinc-400 mt-1">Required for geolocating listing properties.</p>
                    </div>
                    <div>
                        <label>System Currency Layout</label>
                        <select name="cora_currency_format">
                            <?php $curr_format = get_option('cora_currency_format', 'INR_LAKHS'); ?>
                            <option value="INR_LAKHS" <?php selected( $curr_format, 'INR_LAKHS' ); ?>>Indian Rupees (Lakhs/Crores - e.g. ₹1.80 L / ₹4.50 Cr)</option>
                            <option value="INR_STANDARD" <?php selected( $curr_format, 'INR_STANDARD' ); ?>>Indian Rupees Standard (Comma separated - e.g. ₹1,80,000)</option>
                            <option value="USD" <?php selected( $curr_format, 'USD' ); ?>>US Dollars (Standard - e.g. $180,000)</option>
                        </select>
                        <p class="text-[10px] text-zinc-400 mt-1">Determines how prices are formatted in Ledger.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-zinc-100 dark:border-zinc-800/40 pt-4">
                    <div class="relative">
                        <div class="flex items-center gap-2 mb-1.5">
                            <label class="!mb-0 text-zinc-400 dark:text-zinc-600">WhatsApp Cloud API Token</label>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border border-zinc-200 dark:border-zinc-700">
                                <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                Coming Soon
                            </span>
                        </div>
                        <input type="text" disabled placeholder="EAAW..." class="bg-zinc-50 dark:bg-zinc-900 text-zinc-300 dark:text-zinc-700 cursor-not-allowed border-zinc-200 dark:border-zinc-800 placeholder:text-zinc-300 dark:placeholder:text-zinc-700">
                    </div>
                    <div class="relative">
                        <div class="flex items-center gap-2 mb-1.5">
                            <label class="!mb-0 text-zinc-400 dark:text-zinc-600">WhatsApp Business Phone ID</label>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border border-zinc-200 dark:border-zinc-700">
                                <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                Coming Soon
                            </span>
                        </div>
                        <input type="text" disabled placeholder="e.g. 1093847291039" class="bg-zinc-50 dark:bg-zinc-900 text-zinc-300 dark:text-zinc-700 cursor-not-allowed border-zinc-200 dark:border-zinc-800 placeholder:text-zinc-300 dark:placeholder:text-zinc-700">
                    </div>
                </div> <!-- close Grid 2 -->
                </div> <!-- close cora-shopify-card-body -->
            </div> <!-- close cora-shopify-card -->
        </div> <!-- close cora-settings-panel-brand -->

        <!-- TAB 5: READING & SEO SETTINGS -->
        <div id="cora-settings-panel-reading" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'reading' ? '' : 'hidden'; ?>">

            <!-- SEO Health Banner -->
            <?php $is_indexed = get_option('blog_public', 1); ?>
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border <?php echo $is_indexed ? 'bg-emerald-50/50 border-emerald-200 dark:bg-emerald-950/10 dark:border-emerald-900/40' : 'bg-red-50/50 border-red-200 dark:bg-red-950/10 dark:border-red-900/40'; ?>">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 <?php echo $is_indexed ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-red-100 dark:bg-red-900/40'; ?>">
                    <?php if ( $is_indexed ) : ?>
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600 dark:text-emerald-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <?php else : ?>
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" class="text-red-600 dark:text-red-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold <?php echo $is_indexed ? 'text-emerald-800 dark:text-emerald-300' : 'text-red-800 dark:text-red-300'; ?>">
                        <?php echo $is_indexed ? 'Site is publicly visible to search engines' : '⚠ Site is hidden from search engines — this affects lead generation'; ?>
                    </p>
                    <p class="text-[11px] text-zinc-500 mt-0.5"><?php echo $is_indexed ? 'Google, Bing, and other crawlers can index your listings and pages.' : 'Robots.txt is blocking crawlers. Change this immediately to restore organic traffic.'; ?></p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider flex-shrink-0 whitespace-nowrap <?php echo $is_indexed ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'; ?>"><?php echo $is_indexed ? 'Live' : 'Blocked'; ?></span>
            </div>

            <!-- Card 1: Homepage Routing -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Homepage &amp; Blog Routing</h3>
                        <p class="text-xs text-zinc-500 mt-0.5 m-0">Controls which page Google surfaces first when someone searches your agency name.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 flex-shrink-0">
                            <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            SEO Critical
                        </span>
                        <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                        </span>
                    </div>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-5">

                <!-- Display Mode Toggle -->
                <div>
                    <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-3">What should visitors see at your root URL?</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-start gap-3 p-3.5 border rounded-lg cursor-pointer transition-all <?php echo get_option('show_on_front') === 'posts' ? 'border-zinc-900 dark:border-zinc-100 bg-zinc-50 dark:bg-zinc-900/60' : 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-300'; ?>">
                            <input type="radio" name="show_on_front" value="posts" <?php checked( get_option('show_on_front'), 'posts' ); ?> class="mt-0.5 text-zinc-900 focus:ring-zinc-900">
                            <div>
                                <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block">Blog Feed</span>
                                <span class="text-[11px] text-zinc-500 mt-0.5 block">Latest posts / property news</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3.5 border rounded-lg cursor-pointer transition-all <?php echo get_option('show_on_front') === 'page' ? 'border-zinc-900 dark:border-zinc-100 bg-zinc-50 dark:bg-zinc-900/60' : 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-300'; ?>">
                            <input type="radio" name="show_on_front" value="page" <?php checked( get_option('show_on_front'), 'page' ); ?> class="mt-0.5 text-zinc-900 focus:ring-zinc-900">
                            <div>
                                <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block">Static Landing Page</span>
                                <span class="text-[11px] text-zinc-500 mt-0.5 block">Dedicated hero/conversion page</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Page Pickers -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1 border-t border-zinc-100 dark:border-zinc-800/40">
                    <div>
                        <label>Static Homepage
                            <span class="ml-1 text-[10px] font-normal text-zinc-400">— shown at yourdomain.com</span>
                        </label>
                        <select name="page_on_front">
                            <option value="0">— Select Page —</option>
                            <?php foreach ( $pages as $p ) : ?>
                                <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('page_on_front'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Blog / News Archive
                            <span class="ml-1 text-[10px] font-normal text-zinc-400">— property news & updates</span>
                        </label>
                        <select name="page_for_posts">
                            <option value="0">— Select Page —</option>
                            <?php foreach ( $pages as $p ) : ?>
                                <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('page_for_posts'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Growth Tip -->
                <div class="flex items-start gap-2.5 p-3 bg-zinc-50 dark:bg-zinc-900/40 rounded-lg border border-zinc-100 dark:border-zinc-800">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 flex-shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <p class="text-[11px] text-zinc-500 leading-relaxed"><strong class="text-zinc-700 dark:text-zinc-300">Growth tip:</strong> Set your homepage to a dedicated landing page with a lead capture form and property search. Agencies using a static conversion page typically see 2–3× more inquiry submissions than blog-first layouts.</p>
                </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>

            <!-- Card 2: Search Engine Visibility -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Search Engine Crawler Visibility</h3>
                        <p class="text-xs text-zinc-500 m-0">Control whether Google and Bing can discover and rank your listings pages.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                <div class="p-4 border border-red-200 dark:border-red-900/40 bg-red-50/40 dark:bg-red-950/10 rounded-lg">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="blog_public" value="0" <?php checked( get_option('blog_public'), 0 ); ?> class="rounded border-zinc-300 text-red-600 focus:ring-red-500 mt-0.5 flex-shrink-0">
                        <div>
                            <span class="text-xs font-bold text-red-700 dark:text-red-400 block">Hide site from search engines (robots.txt noindex)</span>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 leading-relaxed">When checked, Google cannot index your pages. <strong class="text-zinc-700 dark:text-zinc-300">Leave unchecked</strong> in production to maintain organic lead flow. Use only during staging or site rebuilds.</p>
                        </div>
                    </label>
                </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>

            <!-- Card 3: Writing & Content Defaults -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Writing &amp; Content Defaults</h3>
                        <p class="text-xs text-zinc-500 m-0">Default category and format for new posts and articles.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
                    <div>
                        <label>Default Post Category</label>
                        <select name="default_category">
                            <?php foreach ( $categories as $cat ) : ?>
                                <option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( get_option('default_category'), $cat->term_id ); ?>><?php echo esc_html( $cat->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Default Post Format</label>
                        <select name="default_post_format">
                            <option value="0" <?php selected( get_option('default_post_format'), '0' ); ?>>Standard</option>
                            <option value="gallery" <?php selected( get_option('default_post_format'), 'gallery' ); ?>>Gallery</option>
                            <option value="video" <?php selected( get_option('default_post_format'), 'video' ); ?>>Video</option>
                            <option value="quote" <?php selected( get_option('default_post_format'), 'quote' ); ?>>Quote</option>
                        </select>
                    </div>
                </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>

            <!-- Card 4: SEO URL Permalinks -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">URL Permalink Structure</h3>
                        <p class="text-xs text-zinc-500 m-0">Choose clean, human-readable URL schemas for better search engine rankings.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                <div class="space-y-2">
                    <?php $current_permalink = get_option('permalink_structure'); ?>
                    <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/50 dark:hover:bg-zinc-900/60 cursor-pointer transition-colors gap-2">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="permalink_structure" value="" <?php checked( $current_permalink, '' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Plain</span>
                        </div>
                        <code class="text-[10px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/?p=123') ); ?></code>
                    </label>
                    <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/50 dark:hover:bg-zinc-900/60 cursor-pointer transition-colors gap-2">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="permalink_structure" value="/%year%/%monthnum%/%day%/%postname%/" <?php checked( $current_permalink, '/%year%/%monthnum%/%day%/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Day and name</span>
                        </div>
                        <code class="text-[10px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/2026/07/08/sample-post/') ); ?></code>
                    </label>
                    <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/50 dark:hover:bg-zinc-900/60 cursor-pointer transition-colors gap-2">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="permalink_structure" value="/%year%/%monthnum%/%postname%/" <?php checked( $current_permalink, '/%year%/%monthnum%/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Month and name</span>
                        </div>
                        <code class="text-[10px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/2026/07/sample-post/') ); ?></code>
                    </label>
                    <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-zinc-900 dark:border-zinc-100 rounded-lg bg-zinc-900/5 dark:bg-zinc-100/5 hover:bg-zinc-900/10 dark:hover:bg-zinc-100/10 cursor-pointer transition-colors gap-2">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="permalink_structure" value="/%postname%/" <?php checked( $current_permalink, '/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <div>
                                <span class="text-xs font-bold text-zinc-900 dark:text-white">Post name</span>
                                <span class="ml-2 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Recommended SEO</span>
                            </div>
                        </div>
                        <code class="text-[10px] text-zinc-900 dark:text-white font-bold font-mono truncate break-all"><?php echo esc_url( home_url('/sample-post/') ); ?></code>
                    </label>
                </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>

            <!-- Card 5: Comment Moderation -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Comment &amp; Discussion</h3>
                        <p class="text-xs text-zinc-500 m-0">Moderation policies and spam filtering for blog and listing comments.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                <div class="cora-shopify-card-body pt-4 space-y-4">
                <div class="space-y-3">
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="default_pingback_flag" value="1" <?php checked( get_option('default_pingback_flag'), 1 ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                        <span>Allow pingbacks and trackbacks from other blogs</span>
                    </label>
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="default_comment_status" value="open" <?php checked( get_option('default_comment_status'), 'open' ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                        <span>Allow comments on new articles</span>
                    </label>
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="comment_moderation" value="1" <?php checked( get_option('comment_moderation'), 1 ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                        <span>Comments must be manually approved before publishing</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-zinc-100 dark:border-zinc-800/40 pt-4">
                    <div>
                        <label>Moderation Queue Keywords</label>
                        <textarea name="moderation_keys" rows="3" placeholder="One word or URL per line..." class="w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-900 dark:text-zinc-100 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900 shadow-3xs"><?php echo esc_textarea( get_option('moderation_keys') ); ?></textarea>
                        <p class="text-[10px] text-zinc-400 mt-1">Comments with these words are held for review.</p>
                    </div>
                    <div>
                        <label class="text-red-700 dark:text-red-400">Disallowed Keys (Auto-Trash)</label>
                        <textarea name="disallowed_keys" rows="3" placeholder="One word or URL per line..." class="w-full bg-white dark:bg-zinc-950 border border-red-200 dark:border-red-900/60 rounded-lg p-2.5 text-xs text-zinc-900 dark:text-zinc-100 font-mono focus:outline-none focus:ring-1 focus:ring-red-500 shadow-3xs"><?php echo esc_textarea( get_option('disallowed_keys') ); ?></textarea>
                        <p class="text-[10px] text-zinc-400 mt-1">Matching comments are instantly trashed.</p>
                    </div>
                </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>
        </div>

        <!-- TAB 9: PRIVACY POLICY -->
        <div id="cora-settings-panel-privacy" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'privacy' ? '' : 'hidden'; ?>">
            <!-- Card: Privacy Policy -->
            <div class="cora-shopify-card">
                <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Privacy Policy Page Assignment</h3>
                        <p class="text-xs text-zinc-500 m-0">Designate an official privacy policy page for legal compliance and user transparency.</p>
                    </div>
                    <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                </div>
                
                <div class="cora-shopify-card-body pt-4 space-y-4">
                    <div class="space-y-4 max-w-md">
                    <div>
                        <label>Select Privacy Policy Page</label>
                        <div class="flex gap-2">
                            <select name="wp_page_for_privacy_policy" class="flex-1">
                                <option value="0">— Select Page —</option>
                                <?php foreach ( $pages as $p ) : ?>
                                    <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('wp_page_for_privacy_policy'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a href="?page=cora-workspace&sub=pages" class="px-3.5 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-semibold text-xs rounded-lg transition-colors flex items-center gap-1 shadow-3xs cursor-pointer">Create Page</a>
                        </div>
                    </div>
                    </div>
                </div> <!-- close cora-shopify-card-body -->
            </div>
        </div>

        <!-- TAB 10: GIT SYNC (LOVABLE & GITHUB) -->
        <div id="cora-settings-panel-git-sync" class="cora-settings-panel max-w-full <?php echo $active_tab === 'git-sync' ? '' : 'hidden'; ?> grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            <!-- Left side: Form Settings Card -->
            <div class="xl:col-span-7 space-y-6">
                <!-- Card 1: GitHub Connection -->
                <div class="cora-shopify-card">
                    <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-4 flex items-center justify-between cursor-pointer select-none">
                        <div class="flex items-center gap-3">
                            <!-- GitHub Logo SVG (official mark) -->
                            <div class="w-9 h-9 rounded-xl bg-zinc-950 dark:bg-zinc-900 border border-zinc-800 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg viewBox="0 0 98 96" width="20" height="20" fill="#ffffff" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">GitHub Connection</h3>
                                <p class="text-xs text-zinc-500 mt-0.5 m-0">Sync your website code from a GitHub repository.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <?php $git_token = get_option('cora_git_sync_token', ''); ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold whitespace-nowrap <?php echo $git_token ? 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 border border-green-200 dark:border-green-900/55' : 'bg-zinc-100 text-zinc-650 dark:bg-zinc-850 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800'; ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo $git_token ? 'bg-green-500 animate-pulse' : 'bg-zinc-400'; ?>"></span>
                                <?php echo $git_token ? 'Connected' : 'Not Connected'; ?>
                            </span>
                            <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            </span>
                        </div>
                    </div>
                    <div class="cora-shopify-card-body pt-4 space-y-4">
                    <?php if ( empty( $git_token ) ) : ?>
                        <div class="space-y-4">
                            <!-- Token prompt hint box -->
                            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800/80">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400 mt-0.5 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Generate a <strong class="text-zinc-700 dark:text-zinc-300">Personal Access Token</strong> from <a href="https://github.com/settings/tokens" target="_blank" class="underline underline-offset-2 text-zinc-700 dark:text-zinc-300 hover:text-zinc-900">GitHub Developer Settings</a> with <code class="text-[9px] bg-zinc-200 dark:bg-zinc-800 px-1 py-0.5 rounded font-mono">repo</code> scope enabled.</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest mb-1.5">Personal Access Token</label>
                                <input type="password" id="cora-git-token-input" placeholder="ghp_xxxxxxxxxxxxxxxxxxxx" class="w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-950 dark:focus:ring-zinc-100 transition-all shadow-3xs font-mono tracking-widest" oncopy="return false;" oncut="return false;" ondragstart="return false;" ondrop="return false;" autocomplete="off">
                            </div>
                            <button type="button" onclick="coraConnectGitHub()" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-white text-xs font-bold rounded-xl transition-colors cursor-pointer shadow-3xs">
                                <svg viewBox="0 0 98 96" width="13" height="13" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z"/></svg>
                                Connect GitHub Account
                            </button>
                        </div>
                    <?php else : ?>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest mb-1.5">GitHub Repository</label>
                                    <?php $saved_repo = get_option('cora_git_sync_repo', ''); ?>
                                    <div class="relative" id="cora-git-repo-searchable-select-container" data-saved-url="<?php echo esc_url($saved_repo); ?>">
                                        <!-- Trigger -->
                                        <div id="cora-repo-select-trigger" class="flex items-center justify-between gap-2 px-3.5 py-2 text-xs bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-700 transition-all select-none text-zinc-800 dark:text-zinc-250">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <svg viewBox="0 0 98 96" width="12" height="12" class="fill-zinc-500 dark:fill-zinc-450 shrink-0"><path fill-rule="evenodd" clip-rule="evenodd" d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z"/></svg>
                                                <span id="cora-repo-select-display-text" class="text-zinc-450 dark:text-zinc-500 truncate">Loading repositories...</span>
                                            </div>
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" id="cora-repo-select-arrow" class="text-zinc-400 dark:text-zinc-500 shrink-0 transition-transform"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>

                                        <!-- Dropdown Panel -->
                                        <div id="cora-repo-select-dropdown" class="absolute left-0 right-0 z-[100] mt-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-lg dark:shadow-black/40 overflow-hidden" style="display:none;">
                                            <!-- Search -->
                                            <div class="p-2 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/20">
                                                <input type="text" id="cora-git-repo-search-input" placeholder="Search repositories..." class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-850 rounded-lg px-2.5 py-1.5 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:ring-1 focus:ring-zinc-950 dark:focus:ring-zinc-100 placeholder:text-zinc-400" autocomplete="off">
                                            </div>
                                            <!-- List -->
                                            <div id="cora-repo-options-list" class="max-h-[220px] overflow-y-auto py-1 divide-y divide-zinc-50 dark:divide-zinc-900/40">
                                                <div class="px-3 py-2 text-xs text-zinc-400 dark:text-zinc-500 italic">Loading...</div>
                                            </div>
                                        </div>

                                        <!-- Manual entry (hidden) -->
                                        <div id="cora-git-repo-manual-container" class="mt-2" style="display:none;">
                                            <input type="text" id="cora-git-repo-manual-input" name="cora_git_sync_repo" value="<?php echo esc_attr( $saved_repo ); ?>" placeholder="https://github.com/user/repo" class="w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-950 dark:focus:ring-zinc-100 transition-all shadow-3xs">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <?php $saved_branch = get_option('cora_git_sync_branch', 'main'); ?>
                                    <label class="block text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest mb-1.5">Branch</label>
                                    <div class="relative" id="cora-git-branch-searchable-select-container" data-saved-branch="<?php echo esc_attr($saved_branch); ?>">
                                        <!-- Trigger -->
                                        <div id="cora-branch-select-trigger" class="flex items-center justify-between gap-2 px-3.5 py-2 text-xs bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-700 transition-all select-none text-zinc-800 dark:text-zinc-250">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" class="text-zinc-500 dark:text-zinc-450 shrink-0"><line x1="6" y1="3" x2="6" y2="15"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>
                                                <span id="cora-branch-select-display-text" class="text-zinc-450 dark:text-zinc-500 truncate">Select branch...</span>
                                            </div>
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" id="cora-branch-select-arrow" class="text-zinc-400 dark:text-zinc-500 shrink-0 transition-transform"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                        <!-- Dropdown -->
                                        <div id="cora-branch-select-dropdown" class="absolute left-0 right-0 z-[100] mt-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-lg dark:shadow-black/40 overflow-hidden" style="display:none;">
                                            <div class="p-2 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/20">
                                                <input type="text" id="cora-git-branch-search-input" placeholder="Search branches..." class="w-full bg-white dark:bg-zinc-900 border border-zinc-250 rounded-lg px-2.5 py-1.5 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:ring-1 focus:ring-zinc-950 dark:focus:ring-zinc-100 placeholder:text-zinc-400" autocomplete="off">
                                            </div>
                                            <div id="cora-branch-options-list" class="max-h-[200px] overflow-y-auto py-1 divide-y divide-zinc-50 dark:divide-zinc-900/40">
                                                <div class="px-3 py-2 text-xs text-zinc-400 dark:text-zinc-500 italic">Select a repository first...</div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="cora-git-branch-value" name="cora_git_sync_branch" value="<?php echo esc_attr($saved_branch); ?>">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="cora_git_sync_token" value="<?php echo esc_attr( $git_token ); ?>">
                            
                            <button type="button" onclick="coraDisconnectGitHub()" class="inline-flex items-center gap-1.5 px-3 py-2 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl transition-all cursor-pointer shadow-3xs">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                Disconnect GitHub
                            </button>
                        </div>
                    <?php endif; ?>
                    </div> <!-- close cora-shopify-card-body -->
                </div>

                <!-- Card 2: Lovable Integration -->
                <div class="cora-shopify-card">
                    <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-4 flex items-center justify-between cursor-pointer select-none">
                        <div class="flex items-center gap-3">
                            <!-- Official Lovable Logo Mark (Pure Native SVG) -->
                            <div class="w-10 h-10 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 100 100" width="34" height="34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="lovable-official-logo-grad" x1="20%" y1="0%" x2="80%" y2="100%">
                                            <stop offset="0%" stop-color="#FF6A00" />
                                            <stop offset="35%" stop-color="#FF257E" />
                                            <stop offset="70%" stop-color="#8B5CF6" />
                                            <stop offset="100%" stop-color="#3B82F6" />
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#lovable-official-logo-grad)" d="M 28,2 C 43.468,2 56,14.532 56,30 L 56,44 L 70,44 C 85.468,44 98,56.532 98,72 C 98,87.468 85.468,100 70,100 L 30,100 C 14.532,100 2,87.468 2,72 L 2,30 C 2,14.532 14.532,2 28,2 Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 m-0">Lovable Integration</h3>
                                <p class="text-xs text-zinc-500 mt-0.5 m-0">Connect your live Lovable URL to preview and sync updates.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <?php $live_url = get_option('cora_git_sync_live_url', ''); ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold whitespace-nowrap <?php echo $live_url ? 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 border border-green-200 dark:border-green-900/55' : 'bg-zinc-100 text-zinc-650 dark:bg-zinc-850 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800'; ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo $live_url ? 'bg-green-500 animate-pulse' : 'bg-zinc-400'; ?>"></span>
                                <?php echo $live_url ? 'Connected &amp; Live' : 'Not Connected'; ?>
                            </span>
                            <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            </span>
                        </div>
                    </div>
                    <div class="cora-shopify-card-body pt-4 space-y-4">
                        <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest mb-1.5">Lovable Live URL</label>
                            <input type="text" name="cora_git_sync_live_url" id="cora-lovable-live-url" value="<?php echo esc_attr( $live_url ); ?>" placeholder="https://your-app.lovable.app" class="w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-950 dark:focus:ring-zinc-100 transition-all shadow-3xs">
                            <p class="text-[10px] text-zinc-450 dark:text-zinc-550 mt-1.5 leading-relaxed">Optional. If your repository contains React source code rather than compiled static files, enter your live Lovable deployment URL here.</p>
                        </div>
                        <?php if ( ! empty( $live_url ) ) : ?>
                            <button type="button" onclick="coraDisconnectLovable()" class="inline-flex items-center gap-1.5 px-3 py-2 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl transition-all cursor-pointer shadow-3xs">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                Disconnect Lovable Project
                            </button>
                        <?php endif; ?>
                    </div>
                    </div> <!-- close cora-shopify-card-body -->
                </div>
            </div>

            <!-- Right side: Onboarding & Instructions Card -->
            <div class="xl:col-span-5 cora-shopify-card dark:bg-zinc-900/60 bg-zinc-50/50">
                <div class="p-5 space-y-5">
                    <div class="flex items-center gap-2.5 pb-2 border-b border-zinc-200 dark:border-zinc-800/40">
                        <span class="text-zinc-900 dark:text-zinc-100 shrink-0">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </span>
                        <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Lovable Integration Guide</h4>
                    </div>

                    <!-- Step-by-step tutorial list -->
                    <div class="space-y-5 text-xs text-zinc-700 dark:text-zinc-350">
                        <!-- Step 1 -->
                        <div class="flex gap-3">
                            <div class="w-5 h-5 rounded-full bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[10px] shrink-0">1</div>
                            <div class="space-y-1">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">Initiate on Lovable</span>
                                <p class="leading-relaxed">Build your real estate client site or application on <a href="https://lovable.dev" target="_blank" class="underline font-semibold text-zinc-950 dark:text-white hover:text-zinc-850">Lovable.dev</a> using plain English, and publish it to a GitHub repository.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex gap-3">
                            <div class="w-5 h-5 rounded-full bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[10px] shrink-0">2</div>
                            <div class="space-y-2">
                                <div>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100 block">Generate a GitHub Token</span>
                                    <p class="leading-relaxed">If your repository is Private, generate a security token so Cora can access the code.</p>
                                </div>
                                <a href="https://github.com/settings/tokens/new?scopes=repo&description=Cora%20Git%20Sync" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-white text-[10px] font-bold rounded-xl transition-all shadow-3xs cursor-pointer">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    Generate Token on GitHub
                                </a>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex gap-3">
                            <div class="w-5 h-5 rounded-full bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[10px] shrink-0">3</div>
                            <div class="space-y-1">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">Connect & Sync</span>
                                <p class="leading-relaxed">Connect your GitHub and Lovable accounts on the left, choose where to host the frontend, and click **Sync & Deploy Now**.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Alert box -->
                    <div class="p-3.5 bg-zinc-100 dark:bg-zinc-950 rounded-xl border border-zinc-200/50 dark:border-zinc-800/60 flex gap-3 mt-4">
                        <span class="text-zinc-650 dark:text-zinc-400 shrink-0 mt-0.5">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </span>
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-zinc-800 dark:text-zinc-200 uppercase tracking-wider">Dynamic Integration</span>
                            <p class="text-[10px] text-zinc-550 dark:text-zinc-450 leading-relaxed font-medium">Cora injects `window.CORA_API_URL` and `window.CORA_NONCE` automatically. Any form submissions or CRM requests made in your Lovable code will query this site's databases dynamically.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 12: USER ONBOARDING PANEL -->
        <div id="cora-settings-panel-onboarding" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'onboarding' ? '' : 'hidden'; ?>">
        <?php
        $ob_enabled_raw   = get_option( 'cora_onboarding_enabled', '1' );
        $ob_enabled       = ( $ob_enabled_raw !== '0' && $ob_enabled_raw !== 0 ) ? 1 : 0;

        $ob_google_raw    = get_option( 'cora_onboarding_google_enabled', '1' );
        $ob_google        = ( $ob_google_raw !== '0' && $ob_google_raw !== 0 ) ? 1 : 0;

        $ob_email_raw     = get_option( 'cora_onboarding_email_enabled', '1' );
        $ob_email         = ( $ob_email_raw !== '0' && $ob_email_raw !== 0 ) ? 1 : 0;

        $ob_verify_raw    = get_option( 'cora_onboarding_require_verification', '1' );
        $ob_verify        = ( $ob_verify_raw !== '0' && $ob_verify_raw !== 0 ) ? 1 : 0;

        $ob_role          = get_option( 'cora_onboarding_default_role', 'cora_super_admin' );
        $ob_duration      = get_option( 'cora_onboarding_account_duration', 0 );
        $ob_welcome       = get_option( 'cora_onboarding_welcome_message', '' );
        $ob_client_id     = get_option( 'cora_google_client_id', '' );
        $ob_client_secret = get_option( 'cora_google_client_secret', '' );
        $ob_redirect_uri  = home_url( '/workspace/auth/google/callback' );

        // Fetch self-registered users (auth_provider = email or google, not invited)
        $ob_users = get_users( array(
            'meta_query' => array(
                'relation' => 'OR',
                array( 'key' => 'cora_auth_provider', 'value' => 'email',  'compare' => '=' ),
                array( 'key' => 'cora_auth_provider', 'value' => 'google', 'compare' => '=' ),
            ),
            'number' => 100,
        ) );
        ?>
        <div class="space-y-6 max-w-4xl">

            <!-- Section: Registration Controls -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Registration Controls</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Control how new workspace owners sign up on your platform.</p>
                </div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <?php
                    $toggles = array(
                        array( 'key' => 'cora_onboarding_enabled',              'val' => $ob_enabled,  'label' => 'Enable Self-Registration',         'desc' => 'Allow new users to create their own workspace account.' ),
                        array( 'key' => 'cora_onboarding_google_enabled',       'val' => $ob_google,   'label' => 'Allow Google Sign-In',              'desc' => 'Show a "Continue with Google" button on the register page.' ),
                        array( 'key' => 'cora_onboarding_email_enabled',        'val' => $ob_email,    'label' => 'Allow Email + Password Sign-Up',    'desc' => 'Show the email registration form.' ),
                        array( 'key' => 'cora_onboarding_require_verification', 'val' => $ob_verify,   'label' => 'Require Email Verification',        'desc' => 'Users must click the email link before accessing the workspace.' ),
                    );
                    foreach ( $toggles as $toggle ) : ?>
                    <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200"><?php echo esc_html( $toggle['label'] ); ?></div>
                            <div class="text-xs text-zinc-500 mt-0.5"><?php echo esc_html( $toggle['desc'] ); ?></div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="<?php echo esc_attr( $toggle['key'] ); ?>" value="1" <?php checked( 1, intval( $toggle['val'] ) ); ?> class="sr-only peer">
                            <div class="relative w-9 h-5 bg-zinc-200 dark:bg-zinc-850 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 dark:after:border-zinc-800 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950 dark:peer-checked:bg-zinc-100"></div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Section: Google OAuth Credentials -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Google OAuth Credentials</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Required to enable "Continue with Google" sign-in.</p>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Google Client ID</label>
                            <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                Encrypted &amp; Masked
                            </span>
                        </div>
                        <input type="password" name="cora_google_client_id" value="<?php echo esc_attr( $ob_client_id ? '••••••••••••••••••••••••' : '' ); ?>" placeholder="Enter Google Client ID" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 font-mono" oncopy="return false;" oncut="return false;" ondragstart="return false;" autocomplete="new-password">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Google Client Secret</label>
                            <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                Encrypted &amp; Masked
                            </span>
                        </div>
                        <input type="password" name="cora_google_client_secret" value="<?php echo esc_attr( $ob_client_secret ? '••••••••••••••••••••••••' : '' ); ?>" placeholder="Enter Google Client Secret" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 font-mono" oncopy="return false;" oncut="return false;" ondragstart="return false;" autocomplete="new-password">
                    </div>
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <p class="text-[10px] font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-widest mb-1.5">Authorized Redirect URI</p>
                        <p class="text-[11px] text-zinc-500 mb-2">Copy this exactly into your Google Cloud Console → OAuth credentials → Authorized redirect URIs.</p>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 text-[11px] font-mono text-zinc-700 dark:text-zinc-300 break-all"><?php echo esc_html( $ob_redirect_uri ); ?></code>
                            <button type="button" onclick="navigator.clipboard.writeText('<?php echo esc_js( $ob_redirect_uri ); ?>').then(function(){ window.coraShowToast('Redirect URI copied.'); })" class="shrink-0 px-2.5 py-1.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[10px] font-bold rounded-lg cursor-pointer hover:opacity-80 transition-opacity">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Account Defaults -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Account Defaults</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Set the default role, access duration, and welcome message for new sign-ups.</p>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1.5">Default Role for New Users</label>
                        <select name="cora_onboarding_default_role" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400">
                            <?php
                            $cora_roles = array(
                                'cora_manager'        => 'Workspace Owner (cora_manager)',
                                'cora_branch_manager' => 'Branch Manager',
                                'cora_photographer'   => 'Photographer',
                                'cora_videographer'   => 'Videographer',
                                'cora_drone_pilot'    => 'Drone Pilot',
                                'cora_editor'         => 'Editor',
                                'cora_viewer'         => 'Viewer (Read-Only)',
                            );
                            foreach ( $cora_roles as $role_key => $role_label ) :
                            ?>
                            <option value="<?php echo esc_attr( $role_key ); ?>" <?php selected( $ob_role, $role_key ); ?>><?php echo esc_html( $role_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1.5">Account Duration (days)</label>
                        <input type="number" name="cora_onboarding_account_duration" value="<?php echo intval( $ob_duration ); ?>" min="0" placeholder="0 = Lifetime" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400">
                        <p class="text-[10px] text-zinc-400 mt-1">Set to 0 for unlimited (lifetime) access. Any positive number limits access to that many days from registration.</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1.5">Welcome Message (optional)</label>
                        <textarea name="cora_onboarding_welcome_message" rows="2" placeholder="Welcome to the platform! Here's how to get started..." class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 resize-none"><?php echo esc_textarea( $ob_welcome ); ?></textarea>
                        <p class="text-[10px] text-zinc-400 mt-1">Shown as a toast notification on first dashboard visit (when <code>?welcome=1</code> is in the URL).</p>
                    </div>
                </div>
            </div>

            <!-- Section: Registered Users Table -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Self-Registered Users</h3>
                        <p class="text-xs text-zinc-500 mt-0.5"><?php echo count( $ob_users ); ?> users registered via the onboarding flow.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <?php if ( empty( $ob_users ) ) : ?>
                    <div class="px-5 py-8 text-center text-xs text-zinc-400">
                        No self-registered users yet. Share your registration link: <code class="text-zinc-600 dark:text-zinc-300"><?php echo esc_html( home_url( '/workspace/register' ) ); ?></code>
                    </div>
                    <?php else : ?>
                    <table class="w-full" id="ob-users-table">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-zinc-500 uppercase tracking-widest">User</th>
                                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-zinc-500 uppercase tracking-widest hidden sm:table-cell">Provider</th>
                                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-zinc-500 uppercase tracking-widest hidden md:table-cell">Role</th>
                                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-zinc-500 uppercase tracking-widest hidden lg:table-cell">Verified</th>
                                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-zinc-500 uppercase tracking-widest hidden lg:table-cell">Expiry</th>
                                <th class="px-4 py-2.5 text-right text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        <?php foreach ( $ob_users as $ob_u ) :
                            $ob_verified  = get_user_meta( $ob_u->ID, 'cora_workspace_email_verified', true );
                            $ob_status    = get_user_meta( $ob_u->ID, 'cora_user_status',        true );
                            $ob_provider  = get_user_meta( $ob_u->ID, 'cora_auth_provider',      true );
                            $ob_exp       = get_user_meta( $ob_u->ID, 'cora_account_expires_at', true );
                            $ob_role_disp = implode( ', ', array_keys( $ob_u->roles ) );
                            $ob_exp_str   = ( $ob_exp && intval( $ob_exp ) > 0 ) ? date( 'M j, Y', intval( $ob_exp ) ) : 'Lifetime';
                            $ob_exp_cls   = ( $ob_exp && intval( $ob_exp ) > 0 && time() > intval( $ob_exp ) ) ? 'text-red-500' : 'text-zinc-500';
                        ?>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" data-uid="<?php echo $ob_u->ID; ?>">
                            <td class="px-4 py-3">
                                <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200"><?php echo esc_html( $ob_u->display_name ); ?></div>
                                <div class="text-[10px] text-zinc-400 mt-0.5"><?php echo esc_html( $ob_u->user_email ); ?></div>
                                <?php if ( $ob_status === 'inactive' ) : ?>
                                <span class="inline-block mt-0.5 text-[9px] font-bold bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 px-1.5 py-0.5 rounded">Deactivated</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="text-[10px] font-semibold text-zinc-500 capitalize"><?php echo esc_html( $ob_provider ?: 'email' ); ?></span>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="text-[10px] text-zinc-600 dark:text-zinc-400"><?php echo esc_html( $ob_role_disp ); ?></span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <?php if ( $ob_verified === '1' || $ob_provider === 'google' ) : ?>
                                <span class="text-[10px] font-bold text-green-600">✓ Verified</span>
                                <?php else : ?>
                                <span class="text-[10px] font-bold text-zinc-400">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <span class="text-[10px] <?php echo $ob_exp_cls; ?>"><?php echo esc_html( $ob_exp_str ); ?></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="obChangeRole(<?php echo $ob_u->ID; ?>)" class="px-2 py-1 text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">Role</button>
                                    <button type="button" onclick="obSetExpiry(<?php echo $ob_u->ID; ?>)" class="px-2 py-1 text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">Expiry</button>
                                    <?php if ( $ob_status === 'inactive' ) : ?>
                                    <button type="button" onclick="obToggleStatus(<?php echo $ob_u->ID; ?>, 'activate')" class="px-2 py-1 text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">Activate</button>
                                    <?php else : ?>
                                    <button type="button" onclick="obToggleStatus(<?php echo $ob_u->ID; ?>, 'deactivate')" class="px-2 py-1 text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">Deactivate</button>
                                    <?php endif; ?>
                                    <button type="button" onclick="obDeleteUser(<?php echo $ob_u->ID; ?>)" class="px-2 py-1 text-[10px] font-semibold bg-red-50 hover:bg-red-100 rounded text-red-600 transition-colors cursor-pointer">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Registration Link Card -->
            <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center gap-3">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                <div class="flex-1 min-w-0">
                    <div class="text-[10px] font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-widest mb-0.5">Registration Link</div>
                    <code class="text-[11px] text-zinc-700 dark:text-zinc-300 truncate block"><?php echo esc_html( home_url( '/workspace/register' ) ); ?></code>
                </div>
                <button type="button" onclick="navigator.clipboard.writeText('<?php echo esc_js( home_url( '/workspace/register' ) ); ?>').then(function(){ window.coraShowToast('Link copied!'); })" class="shrink-0 px-3 py-1.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[10px] font-bold rounded-lg cursor-pointer hover:opacity-80 transition-opacity">Copy</button>
            </div>
        </div>

        <script>
        function obAction(uid, action, extra) {
            extra = extra || {};
            var data = Object.assign({
                action: 'cora_onboarding_update_user',
                sub_action: action,
                target_user_id: uid,
                nonce: (typeof coraREData !== 'undefined') ? coraREData.ajaxNonce : ''
            }, extra);
            jQuery.post((typeof coraREData !== 'undefined') ? coraREData.ajaxUrl : '', data, function(res) {
                if (res.success) { window.coraShowToast(res.data.message); setTimeout(function(){ location.reload(); }, 1200); }
                else { window.coraShowToast(res.data.message || 'Something went wrong.'); }
            });
        }
        function obChangeRole(uid) {
            var roles = ['cora_manager','cora_branch_manager','cora_photographer','cora_videographer','cora_drone_pilot','cora_editor','cora_viewer'];
            var labels = ['Workspace Owner','Branch Manager','Photographer','Videographer','Drone Pilot','Editor','Viewer'];
            
            var options = roles.map(function(r, i) {
                return { value: r, label: labels[i] };
            });
            
            if (window.coraPrompt) {
                window.coraPrompt({
                    title: 'Select Role',
                    message: 'Select the new role for this user:',
                    type: 'select',
                    options: options
                }, function(role) {
                    if (role) {
                        obAction(uid, 'change_role', {new_role: role});
                    }
                });
            } else {
                window.coraShowToast('UI prompt not ready.');
            }
        }
        function obSetExpiry(uid) {
            if (window.coraPrompt) {
                window.coraPrompt({
                    title: 'Set Expiry',
                    message: 'Set account expiry duration (in days). Enter 0 for lifetime access:',
                    type: 'number',
                    defaultValue: '0'
                }, function(days) {
                    if (days !== null && days !== '') {
                        obAction(uid, 'set_expiry', {days: parseInt(days)||0});
                    }
                });
            } else {
                window.coraShowToast('UI prompt not ready.');
            }
        }
        function obToggleStatus(uid, action) { obAction(uid, action); }
        function obDeleteUser(uid) {
            const deleteFn = function() {
                obAction(uid, 'delete');
            };
            if (window.coraConfirm) {
                window.coraConfirm({
                    title: 'Delete User',
                    message: 'Permanently delete this user? This cannot be undone.',
                    danger: true,
                    okLabel: 'Delete'
                }, deleteFn);
            } else {
                deleteFn();
            }
        }
        </script>
        <?php // end onboarding tab ?>

        </div>
        <?php 
            $google_client_id     = get_option('cora_google_client_id', '');
            $google_client_secret = get_option('cora_google_client_secret', '');
            $google_refresh_token = get_option('cora_google_refresh_token', '');
            $google_account_email = get_option('cora_google_account_email', '');
            $google_folder_id     = get_option('cora_backup_google_folder_id', '');
            $google_has_creds     = (!empty($google_client_id) && !empty($google_client_secret));
            $is_google_connected  = (!empty($google_refresh_token)); // ONLY true if OAuth token exists
            $history = get_option( 'cora_workspace_backup_history', array() );
        ?>
        <!-- TAB 13: BACKUP & RECOVERY PANEL -->
        <div id="cora-settings-panel-backup" class="cora-settings-panel space-y-6 <?php echo $active_tab === 'backup' ? '' : 'hidden'; ?>">
            
            <!-- Top Metric & Status Summary Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-2 lg:mb-0">
                <div class="cora-shopify-card cora-metric-card border-l-2 border-l-emerald-500 shadow-2xs">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">System Health</div>
                    <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        100% Operational
                    </div>
                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">24h Automation Active</div>
                </div>

                <div class="cora-shopify-card cora-metric-card border-l-2 border-l-zinc-400 dark:border-l-zinc-600 shadow-2xs">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Last Snapshot Taken</div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">
                        <?php 
                        echo !empty($history) ? esc_html(date('M j, Y H:i', $history[0]['time'])) : 'No backups yet';
                        ?>
                    </div>
                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Full System (.zip) & SQL</div>
                </div>

                <div class="cora-shopify-card cora-metric-card border-l-2 <?php echo $is_google_connected ? 'border-l-emerald-500' : 'border-l-zinc-400 dark:border-l-zinc-600'; ?> shadow-2xs">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider mb-1">Google Drive Storage</div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                        <?php if ( $is_google_connected ) : ?>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-emerald-600 dark:text-emerald-400">Connected & Active</span>
                        <?php else : ?>
                            <span class="w-2 h-2 rounded-full bg-zinc-400 dark:bg-zinc-600"></span>
                            <span class="text-zinc-500 dark:text-zinc-400">Not Connected</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1 truncate" title="<?php echo esc_attr($google_folder_id ?: 'Click 1-Click Connect'); ?>">Folder: <?php echo esc_html($google_folder_id ?: 'Click 1-Click Connect'); ?></div>
                </div>

                <div class="cora-shopify-card cora-metric-card border-l-2 border-l-indigo-500 shadow-2xs">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Restore Guard</div>
                    <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        Auto Safety Point
                    </div>
                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Snapshot before restore</div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <!-- Left 2 Cols: Backup Configuration & System Snapshots -->
                <div class="xl:col-span-7 space-y-6">
                    
                    <!-- Card 1: Full System Snapshot & Export Generator -->
                    <div class="cora-shopify-card">
                        <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 m-0">Full System Snapshot & Export</h3>
                                    <span class="text-[10px] font-mono font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded-full shrink-0">v2.1.0 Ready</span>
                                    <!-- Info Icon with Tooltip Popover -->
                                    <div class="relative group cursor-pointer flex shrink-0">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 hover:text-zinc-650 dark:hover:text-zinc-300 transition-colors"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-zinc-950 dark:bg-zinc-900 border border-zinc-850 dark:border-zinc-800 text-white rounded-xl shadow-xl opacity-0 scale-95 pointer-events-none group-hover:opacity-100 group-hover:scale-100 transition-all z-50 text-[10px] leading-relaxed font-normal normal-case">
                                            <span class="font-bold text-zinc-100 block mb-1">Full System Snapshot (.zip)</span>
                                            Packages the complete database schema, row records, environment manifest, active module states, and asset indexes into a single compressed backup archive. Standard database export (.sql) exports table structures and rows only.
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Generate full system backup archives (.zip) or lightweight database SQL files.</p>
                            </div>
                            <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            </span>
                        </div>
                        <div class="cora-shopify-card-body pt-4 space-y-4">
                            <div class="flex flex-wrap gap-2.5 pt-1.5">
                                <!-- Primary: Full System Snapshot Zip Button -->
                                <button type="button" id="cora-trigger-full-snapshot-backup" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-sm active:scale-97">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    Generate Full Snapshot (.zip)
                                </button>
    
                                <!-- Secondary: Database Only SQL Button -->
                                <button type="button" id="cora-trigger-manual-db-backup" class="px-4 py-2.5 bg-transparent hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-sm">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                                    Export Database Only (.sql)
                                </button>
                            </div>
                        </div> <!-- close cora-shopify-card-body -->
                    </div>

                    <!-- Card 2: 24h Automated Google Drive Sync -->
                    <div class="cora-shopify-card" id="cora-google-drive-card">
                        <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                            <div>
                                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 m-0">24-Hour Automated Google Drive Sync</h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Automatically upload daily platform snapshots to your personal Google Drive.</p>
                            </div>
                            <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            </span>
                        </div>
                        <div class="cora-shopify-card-body pt-4 space-y-4">

                        <?php if ( $is_google_connected ) : ?>
                        <!-- STATE: Fully connected -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 bg-zinc-50/50 dark:bg-zinc-900/10 border border-zinc-150 dark:border-zinc-800/40 border-l-2 border-l-emerald-500 rounded-xl gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600 dark:text-emerald-400"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-white leading-none m-0">Google Drive — Connected</h4>
                                    <p class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-0.5 m-0"><?php echo esc_html($google_account_email ?: 'Google Account'); ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold whitespace-nowrap bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Connected & Active
                                </span>
                                <button type="button" id="cora-disconnect-google-drive" class="px-2.5 py-1 bg-white dark:bg-zinc-900 hover:bg-red-50 dark:hover:bg-red-950/40 text-zinc-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 text-[11px] font-semibold rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-red-200 dark:hover:border-red-800 transition-all flex items-center gap-1.5 cursor-pointer">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                    Disconnect
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-800 dark:text-zinc-200 mb-0.5">Automation Schedule</label>
                                <select name="cora_backup_schedule" style="width:100%;padding:10px 14px;font-size:13px;background:var(--input-bg);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);outline:none;font-family:inherit;">
                                    <?php $schedule = get_option('cora_backup_schedule', 'daily'); ?>
                                    <option value="daily" <?php selected($schedule, 'daily'); ?>>Every 24 Hours (Daily at 00:00 UTC)</option>
                                    <option value="weekly" <?php selected($schedule, 'weekly'); ?>>Every 7 Days (Weekly)</option>
                                    <option value="monthly" <?php selected($schedule, 'monthly'); ?>>Every 30 Days (Monthly)</option>
                                    <option value="manual" <?php selected($schedule, 'manual'); ?>>Manual Only</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-800 dark:text-zinc-200 mb-0.5">Drive Folder ID <span class="font-normal text-zinc-400">(optional)</span></label>
                                <p class="text-[10px] text-zinc-500 mb-1.5">Leave empty to save to your Drive root folder</p>
                                <input type="text" name="cora_backup_google_folder_id" value="<?php echo esc_attr($google_folder_id); ?>" placeholder="e.g. 1A2b3C4d5E_xyz..." class="w-full">
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                                <input type="checkbox" name="cora_backup_google_drive_enabled" value="1" <?php checked(get_option('cora_backup_google_drive_enabled', 1), 1); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                                <span>Automatically upload a 24-hour snapshot to your connected Drive</span>
                            </label>
                        </div>

                        <div class="pt-1">
                            <button type="button" id="cora-trigger-drive-sync-now" class="w-full py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                                Sync to Drive Now
                            </button>
                        </div>

                        <?php elseif ( $google_has_creds ) : ?>
                        <!-- STATE: Platform credentials configured — ready to connect -->
                        <div class="p-5 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/80 dark:border-zinc-800/60 rounded-xl space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-zinc-900 dark:bg-white flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="white" class="dark:stroke-zinc-900" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Connect your Google Drive</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">Click below and sign in with your Google account. Cora will request access to store backup snapshots in your Drive. No data is shared or read — only backups are written.</p>
                                </div>
                            </div>
                            <button type="button" id="cora-connect-google-drive" class="w-full py-3 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2.5 cursor-pointer shadow-sm">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                                Connect with Google
                            </button>
                            <p class="text-[10px] text-zinc-400 text-center">You will be redirected to Google to authorise access. You can disconnect at any time.</p>
                        </div>

                        <?php else : ?>
                        <!-- STATE: Coming Soon — platform credentials not yet configured -->
                        <div class="p-6 flex flex-col items-center justify-center text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 dark:text-zinc-500"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Google Drive Sync</p>
                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-[10px] font-bold rounded-full border border-zinc-200 dark:border-zinc-700">
                                    <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    Coming Soon
                                </span>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed">Automated 24-hour backup uploads directly to your Google Drive will be available in the next platform update.</p>
                            </div>
                        </div>
                        <?php endif; ?>

                        </div> <!-- close cora-shopify-card-body -->
                    </div>



                </div>

                <!-- Right 1 Col: Snapshot Logs & One-Click Restore Center -->
                <div class="xl:col-span-5 space-y-6">
                    <div class="cora-shopify-card">
                        <div class="cora-shopify-card-header border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between cursor-pointer select-none">
                            <div class="flex items-center justify-between flex-1 pr-4">
                                <div>
                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Backup Logs & Restore Center</h3>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Stored system restore points & archives.</p>
                                </div>
                                <span class="text-[10px] font-mono text-zinc-500 dark:text-zinc-400">cora-backups/</span>
                            </div>
                            <span class="cora-card-chevron text-zinc-400 dark:text-zinc-500 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            </span>
                        </div>
                        <div class="cora-shopify-card-body pt-4 space-y-4">

                        <div id="cora-backups-history-list" class="space-y-3">
                            <?php 
                            if ( ! empty( $history ) ) :
                                foreach ( $history as $item ) :
                                    $is_zip = ( strpos( $item['filename'], '.zip' ) !== false );
                                    $display_name = strlen($item['filename']) > 24 ? substr($item['filename'], 0, 24) . '...' : $item['filename'];
                            ?>
                                <div class="cora-shopify-card !p-3.5 flex flex-col gap-3 bg-white dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800/80 rounded-2xl shadow-3xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                                     <!-- Row 1: File Name and Format Badge -->
                                     <div class="flex items-center justify-between gap-2.5 min-w-0">
                                         <div class="flex items-center gap-2 min-w-0">
                                             <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 dark:text-zinc-550 shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                             <span class="font-bold text-xs text-zinc-850 dark:text-zinc-200 truncate" title="<?php echo esc_attr( $item['filename'] ); ?>"><?php echo esc_html( $display_name ); ?></span>
                                         </div>
                                         <span class="text-[8px] font-extrabold uppercase tracking-wider px-1.5 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-850 text-zinc-600 dark:text-zinc-455 border border-zinc-200/60 dark:border-zinc-800 shrink-0">
                                             <?php echo $is_zip ? '.zip' : 'SQL'; ?>
                                         </span>
                                     </div>
 
                                     <!-- Row 2: Date & Size (Meta Details) -->
                                     <div class="flex items-center justify-between text-[10px] text-zinc-500 dark:text-zinc-450 border-t border-zinc-50 dark:border-zinc-850/60 pt-2 font-medium gap-3">
                                         <span class="whitespace-nowrap"><?php echo esc_html( date( 'M j, Y H:i', $item['time'] ) ) . ' UTC'; ?></span>
                                         <span class="font-semibold text-zinc-650 dark:text-zinc-400 whitespace-nowrap"><?php echo esc_html( $item['size'] ); ?></span>
                                     </div>
 
                                     <!-- Row 3: Action Buttons -->
                                     <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
                                         <button type="button" onclick="coraRestoreBackup('<?php echo esc_js($item['filename']); ?>')" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 font-bold text-[10px] rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shadow-3xs active:scale-97">
                                             <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M3 2v6h6"></path><path d="M3 13a9 9 0 1 0 3-7.7L3 8"></path></svg>
                                             Restore
                                         </button>
                                         <div class="flex items-center gap-1.5">
                                             <a href="<?php echo admin_url('admin-ajax.php?action=cora_download_backup&file=' . urlencode( $item['filename'] ) . '&nonce=' . wp_create_nonce('cora_download_backup_nonce')); ?>" class="p-1.5 text-zinc-500 hover:text-zinc-850 dark:hover:text-zinc-250 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-750 rounded-lg border border-zinc-200/30 dark:border-zinc-850 transition-all" title="Download Archive">
                                                 <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                             </a>
                                             <button type="button" onclick="coraDeleteBackup('<?php echo esc_js($item['filename']); ?>')" class="p-1.5 text-red-500 hover:text-red-600 dark:hover:text-red-400 bg-red-50/50 hover:bg-red-55 dark:bg-red-950/20 dark:hover:bg-red-950/40 rounded-lg border border-red-100/30 dark:border-red-900/20 transition-all cursor-pointer" title="Delete Log">
                                                 <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                             </button>
                                         </div>
                                     </div>
                                </div>
                            <?php 
                                endforeach;
                            else :
                            ?>
                                <div class="p-8 text-center flex flex-col items-center justify-center space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 flex items-center justify-center text-zinc-300 dark:text-zinc-600">
                                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="font-bold text-sm text-zinc-900 dark:text-zinc-100 m-0">No snapshots yet</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Generate your first backup above.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        </div> <!-- close cora-shopify-card-body -->
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: AUDIT & LOGS -->
        <div id="cora-settings-panel-audit" class="cora-settings-panel space-y-6 max-w-full <?php echo $active_tab === 'audit' ? '' : 'hidden'; ?>">
            <?php include __DIR__ . '/view-audit-panel.php'; ?>
        </div>

        <!-- TAB: UPDATES & PLATFORM -->
        <style>
        .cora-update-card-accordion {
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .cora-update-card-accordion:hover {
            border-color: #d4d4d8;
        }
        .dark .cora-update-card-accordion:hover {
            border-color: #3f3f46;
        }
        .cora-update-accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
            opacity: 0;
        }
        .cora-update-accordion-content.open {
            max-height: 1200px;
            opacity: 1;
        }
        </style>

        <div id="cora-settings-panel-updates" class="cora-settings-panel space-y-6 max-w-3xl relative <?php echo $active_tab === 'updates' ? '' : 'hidden'; ?>">
            <div class="cora-shopify-card">
                <!-- Header -->
                <div class="cora-shopify-card-header border-b border-zinc-150 dark:border-zinc-800/40 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Software Updates</h3>
                        <p class="text-xs text-zinc-500 m-0">Manage system versions, release channels, and automated feature shipments.</p>
                    </div>
                    <div class="relative hidden sm:block">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 text-xs font-bold text-zinc-700 dark:text-zinc-300 select-none">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Production Stable
                        </span>
                    </div>
                </div>

                <div class="cora-shopify-card-body pt-5 relative">
                    <!-- Dynamic Status Container -->
                    <?php
                    $updater = Cora_Workspace_Updater::get_instance();
                    $info = $updater->fetch_remote_update_info();
                    $update_available = ( $info && version_compare( CORA_WORKSPACE_VERSION, $info['version'], '<' ) );
                    ?>
                    <div id="cora-updates-status-container" class="select-none mb-5">
                        
                        <!-- Default: Up-to-Date Card (No further updates available) -->
                        <div id="cora-updates-state-uptodate" class="<?php echo $update_available ? 'hidden' : ''; ?> p-5 bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800/80 rounded-2xl flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-3xs">
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 m-0 flex items-center gap-2">
                                    <span>You are operating on the latest version</span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-mono bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-500/20">v<?php echo esc_html( CORA_WORKSPACE_VERSION ); ?></span>
                                </h4>
                                <p class="text-xs text-zinc-600 dark:text-zinc-300 m-0 font-medium leading-relaxed">
                                    No further updates are available at this time. Your workspace platform features, schema models, and security definitions are completely up to date.
                                </p>
                                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-1 m-0">
                                    Last checked: <span id="cora-last-check-text" class="font-semibold text-zinc-600 dark:text-zinc-400"><?php 
                                        $last_checked = get_option( 'cora_workspace_last_update_check_time', 'Never' );
                                        if ( 'Never' !== $last_checked ) {
                                            $last_checked = mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_checked );
                                        }
                                        echo esc_html( $last_checked );
                                    ?></span>.
                                </p>
                            </div>
                        </div>

                        <!-- State: Checking for Updates -->
                        <div id="cora-updates-state-checking" class="hidden p-4 bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none" class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 m-0">Checking for Updates...</h4>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 m-0">Connecting to secure release shipment server.</p>
                            </div>
                        </div>

                        <!-- State: Update Available Feature Spotlight Card -->
                        <div id="cora-updates-state-available" class="<?php echo $update_available ? '' : 'hidden'; ?> w-full text-left animate-in fade-in duration-200">
                            <div class="border border-emerald-500/30 dark:border-emerald-500/20 rounded-2xl p-5 bg-gradient-to-br from-emerald-500/5 via-emerald-500/10 to-transparent dark:from-emerald-500/10 dark:via-emerald-500/15 dark:to-transparent space-y-3.5 shadow-xs">
                                <div class="flex items-center justify-between flex-wrap gap-3 pb-3 border-b border-emerald-500/15">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md">
                                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-[9px] font-extrabold bg-emerald-600 text-white px-2.5 py-0.5 rounded-full tracking-wide uppercase leading-none shadow-3xs">NEW VERSION AVAILABLE</span>
                                                <span class="text-sm font-black text-zinc-900 dark:text-white font-mono" id="cora-avail-ver-pill">v<?php echo esc_html( $info['version'] ?? CORA_WORKSPACE_VERSION ); ?></span>
                                            </div>
                                            <p class="text-xs text-zinc-600 dark:text-zinc-300 mt-1 m-0">
                                                Upgrade from <span class="font-semibold text-zinc-800 dark:text-zinc-200">v<?php echo esc_html( CORA_WORKSPACE_VERSION ); ?></span> to <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono" id="cora-avail-target-ver">v<?php echo esc_html( $info['version'] ?? CORA_WORKSPACE_VERSION ); ?></span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Quick Upgrade Action -->
                                    <button type="button" onclick="coraTriggerInAppUpgradeManual()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition-all cursor-pointer flex items-center gap-2 shadow-md active:scale-97">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                                        <span>Upgrade Workspace Now</span>
                                    </button>
                                </div>

                                <!-- What's New Highlights -->
                                <div id="cora-avail-highlights-box" class="space-y-2 pt-1">
                                    <h5 class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 m-0 flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                        <span>What's New in this Release:</span>
                                    </h5>
                                    <div id="cora-avail-highlights-content" class="text-xs text-zinc-700 dark:text-zinc-200 space-y-1.5 m-0 font-medium">
                                        <?php
                                        if ( ! empty( $info['sections']['changelog'] ) ) {
                                            preg_match( '/<ul>(.*?)<\/ul>/s', $info['sections']['changelog'], $matches );
                                            if ( ! empty( $matches[0] ) ) {
                                                echo $matches[0];
                                            } else {
                                                echo '<ul class="list-disc pl-4 space-y-1"><li>Performance optimizations, security patches, and workspace UI refinements.</li></ul>';
                                            }
                                        } else {
                                            echo '<ul class="list-disc pl-4 space-y-1"><li>Performance optimizations, security patches, and workspace UI refinements.</li></ul>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- State: Upgrade Progress Indicator -->
                        <div id="cora-updates-state-progress" class="hidden w-full text-left space-y-3 animate-in fade-in duration-200">
                            <div class="p-4 bg-zinc-50 dark:bg-zinc-900/60 rounded-xl border border-zinc-150 dark:border-zinc-800/60 space-y-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.5" fill="none" class="animate-spin text-zinc-500 shrink-0"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                        <span id="cora-upgrade-step-text">Initializing shipment...</span>
                                    </span>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100" id="cora-upgrade-percent-text">0%</span>
                                </div>
                                <div class="h-2 w-full bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                                    <div id="cora-upgrade-progress-bar" class="h-full bg-zinc-950 dark:bg-white w-0 transition-all duration-350 ease-out rounded-full"></div>
                                </div>
                                <p class="text-[10px] text-zinc-500 leading-normal m-0" id="cora-upgrade-desc-text">
                                    Please do not close or reload this page. Extracting workspace files.
                                </p>
                            </div>
                        </div>

                        <!-- State: Error State -->
                        <div id="cora-updates-state-error" class="hidden space-y-2 p-4 bg-red-500/5 dark:bg-red-500/10 border border-red-200 dark:border-red-800/30 rounded-xl">
                            <h4 class="text-xs font-bold text-red-600 dark:text-red-400 m-0">Connection Failed</h4>
                            <p id="cora-error-message-text" class="text-[11px] text-red-500 m-0">Failed to query release details from update server.</p>
                        </div>

                    </div>

                    <!-- Changelog Section (Hidden when on Latest Version) -->
                    <div id="cora-settings-changelog-section" class="<?php echo $update_available ? '' : 'hidden'; ?> space-y-3 pt-2">
                        <div class="flex items-center justify-between border-b border-zinc-150 dark:border-zinc-800/80 pb-2.5">
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider m-0">Changelog & Features</h4>
                            <button type="button" id="cora-settings-expand-btn" onclick="coraToggleSettingsExpandAll(this);" class="h-7 px-3 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-850 text-zinc-750 dark:text-zinc-300 rounded-lg text-[11px] font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-3xs">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                                <span>Expand All</span>
                            </button>
                        </div>

                        <!-- Dynamic Compact Version Cards -->
                        <div id="cora-settings-changelog-timeline" class="space-y-2.5 py-1"></div>

                        <!-- Hidden Raw Box Fallback -->
                        <div id="cora-update-changelog-box" class="hidden">
                            <?php echo $info['sections']['changelog'] ?? ''; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FLOATING STICKY BOTTOM CTA BAR -->
            <div class="sticky bottom-4 z-40 bg-white/95 dark:bg-zinc-950/95 backdrop-blur-md border border-zinc-200 dark:border-zinc-800/90 px-5 py-3.5 rounded-2xl flex items-center justify-between flex-wrap gap-4 select-none shadow-xl transition-all">
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 font-medium flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Shipment Channel: <strong class="text-zinc-800 dark:text-zinc-200">Production Stable (GitHub)</strong></span>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- Check Button -->
                    <button type="button" id="cora-btn-updates-check" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-sm active:scale-97 select-none" onclick="coraCheckForUpdatesManual()">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" id="cora-icon-updates-check" class="shrink-0"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                        <span id="cora-btn-updates-check-label">Check for Updates</span>
                    </button>

                    <!-- Upgrade Button (sticky CTA) -->
                    <button type="button" id="cora-btn-updates-upgrade" class="<?php echo $update_available ? '' : 'hidden'; ?> px-4.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-sm active:scale-97 select-none" onclick="coraTriggerInAppUpgradeManual()">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><polyline points="18 15 12 9 6 15"></polyline></svg>
                        <span>Upgrade Workspace Now</span>
                    </button>
                </div>
            </div>

            <script>
            function parseChangelogHTML(htmlString) {
                if (!htmlString) return [];
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlString, 'text/html');
                const h4s = doc.querySelectorAll('h4');
                const versions = [];
                h4s.forEach(h4 => {
                    const version = h4.textContent.trim().replace(/^v/, '');
                    const ul = h4.nextElementSibling;
                    const items = [];
                    if (ul && ul.tagName === 'UL') {
                        const lis = ul.querySelectorAll('li');
                        lis.forEach(li => {
                            const strong = li.querySelector('strong');
                            let title = '';
                            let description = li.textContent.trim();
                            if (strong) {
                                title = strong.textContent.replace(/:$/, '').trim();
                                description = description.replace(strong.textContent, '').replace(/^:\s*/, '').trim();
                            } else {
                                const parts = description.split(':');
                                if (parts.length > 1) {
                                    title = parts[0].trim();
                                    description = parts.slice(1).join(':').trim();
                                } else {
                                    title = 'Platform Update';
                                }
                            }
                            items.push({ title: title, desc: description });
                        });
                    }
                    versions.push({ version: version, items: items });
                });
                return versions;
            }

            function coraToggleVersionCard(rIdx) {
                const content = document.getElementById(`content-ver-${rIdx}`);
                const chevron = document.getElementById(`chevron-ver-${rIdx}`);
                if (content && chevron) {
                    if (content.classList.contains('open')) {
                        content.classList.remove('open');
                        chevron.style.transform = 'rotate(0deg)';
                    } else {
                        content.classList.add('open');
                        chevron.style.transform = 'rotate(180deg)';
                    }
                }
            }

            function coraToggleSettingsExpandAll(btn) {
                const span = btn.querySelector('span');
                const isExpand = span.innerText === 'Expand All';
                const contents = document.querySelectorAll('#cora-settings-changelog-timeline .cora-update-accordion-content');
                contents.forEach((content, index) => {
                    const chevron = document.getElementById(`chevron-ver-${index}`);
                    if (isExpand) {
                        content.classList.add('open');
                        if (chevron) chevron.style.transform = 'rotate(180deg)';
                    } else {
                        content.classList.remove('open');
                        if (chevron) chevron.style.transform = 'rotate(0deg)';
                    }
                });
                if (isExpand) {
                    span.innerText = 'Collapse All';
                    btn.querySelector('svg').innerHTML = '<path d="M4 14h6v6M20 10h-6V4M14 10l7-7M10 14l-7 7"/>';
                } else {
                    span.innerText = 'Expand All';
                    btn.querySelector('svg').innerHTML = '<path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>';
                }
            }

            function coraRenderSettingsUpdateTimeline() {
                const container = document.getElementById('cora-settings-changelog-timeline');
                if (!container) return;

                let releases = [];
                
                // Always parse server raw box first
                const rawBox = document.getElementById('cora-update-changelog-box');
                if (rawBox && rawBox.innerHTML && typeof parseChangelogHTML === 'function') {
                    releases = parseChangelogHTML(rawBox.innerHTML);
                }

                // Merge static releases if missing
                if (window.coraReleasesData && Array.isArray(window.coraReleasesData)) {
                    window.coraReleasesData.forEach(rel => {
                        if (!releases.some(r => r.version === rel.version)) {
                            releases.push(rel);
                        }
                    });
                }

                if (!releases || !releases.length) return;

                let html = '';
                releases.forEach((rel, rIdx) => {
                    const isLatest = rIdx === 0;
                    const mainTitle = rel.items[0] ? rel.items[0].title : 'Release Updates';
                    
                    html += `
                        <div class="cora-update-card-accordion border border-zinc-200 dark:border-zinc-800/80 rounded-xl bg-white dark:bg-zinc-900 overflow-hidden shadow-3xs">
                            <!-- Version Card Header -->
                            <div class="p-3 bg-zinc-50/80 dark:bg-zinc-900/80 hover:bg-zinc-100/70 dark:hover:bg-zinc-850 flex items-center justify-between cursor-pointer select-none" onclick="coraToggleVersionCard('${rIdx}')">
                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                    <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 shrink-0">v${rel.version}</span>
                                    ${isLatest ? '<span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wide bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shrink-0">LATEST</span>' : ''}
                                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 truncate">${mainTitle}</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500">${rel.items.length} ${rel.items.length === 1 ? 'change' : 'changes'}</span>
                                    <span id="chevron-ver-${rIdx}" class="text-zinc-400 transition-transform duration-200" style="display:inline-block; transform: ${isLatest ? 'rotate(180deg)' : 'rotate(0deg)'};">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"/></svg>
                                    </span>
                                </div>
                            </div>

                            <!-- Collapsible List Body -->
                            <div class="cora-update-accordion-content ${isLatest ? 'open' : ''}" id="content-ver-${rIdx}">
                                <div class="p-4 space-y-3 bg-white dark:bg-zinc-900 border-t border-zinc-150 dark:border-zinc-850">
                    `;

                    rel.items.forEach((item) => {
                        let svgIcon = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>`;
                        
                        const tLower = item.title.toLowerCase();
                        if (tLower.includes('lock') || tLower.includes('security') || tLower.includes('privilege') || tLower.includes('scoping')) {
                            svgIcon = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-amber-500"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`;
                        } else if (tLower.includes('ui') || tLower.includes('redesign') || tLower.includes('onboarding') || tLower.includes('screen') || tLower.includes('bar')) {
                            svgIcon = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-blue-500"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
                        } else if (tLower.includes('fix') || tLower.includes('bug') || tLower.includes('hotfix')) {
                            svgIcon = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-emerald-500"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>`;
                        } else if (tLower.includes('optimization') || tLower.includes('speed') || tLower.includes('performance')) {
                            svgIcon = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-purple-500"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`;
                        }

                        html += `
                            <div class="flex items-start gap-2.5 text-xs leading-relaxed">
                                <div class="w-6 h-6 rounded bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center shrink-0 mt-0.5 border border-zinc-200/60 dark:border-zinc-700/60">
                                    ${svgIcon}
                                </div>
                                <div class="flex-1">
                                    <strong class="font-bold text-zinc-900 dark:text-zinc-100 mr-1">${item.title}:</strong>
                                    <span class="text-zinc-600 dark:text-zinc-400">${item.desc}</span>
                                </div>
                            </div>
                        `;
                    });

                    html += `
                                </div>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;
            }

            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(coraRenderSettingsUpdateTimeline, 50);
            });
            </script>
            
            <!-- CUSTOM CONFIRMATION DIALOGUE OVERLAY -->
            <div id="cora-update-confirm-modal" class="fixed inset-0 bg-zinc-950/40 dark:bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-200 select-none">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-sm w-full p-6 shadow-xl transform scale-95 transition-transform duration-200 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-xl shrink-0">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Upgrade Workspace</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 leading-normal">Are you sure you want to upgrade Cora Workspace to the latest version? The screen will automatically reload once the shipment is installed.</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" id="cora-update-confirm-cancel" class="px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-850 text-zinc-700 dark:text-zinc-300 font-bold rounded-xl text-xs transition-all cursor-pointer" onclick="closeCoraUpdateConfirmModal()">
                            Cancel
                        </button>
                        <button type="button" id="cora-update-confirm-ok" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-850 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold rounded-xl text-xs transition-all cursor-pointer shadow-sm active:scale-97" onclick="confirmCoraUpdateAction()">
                            Yes, Upgrade
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function coraCheckForUpdatesManual() {
            var $btn = jQuery('#cora-btn-updates-check');
            var $icon = jQuery('#cora-icon-updates-check');
            var $label = jQuery('#cora-btn-updates-check-label');
            
            $btn.prop('disabled', true);
            $icon.addClass('animate-spin');
            $label.text('Checking...');
            
            jQuery('#cora-updates-state-uptodate, #cora-updates-state-available, #cora-updates-state-error').addClass('hidden');
            jQuery('#cora-updates-state-checking').removeClass('hidden');
            jQuery('#cora-btn-updates-upgrade').addClass('hidden');
            
            jQuery.post(coraREData.ajaxUrl, {
                action: 'cora_check_plugin_update',
                nonce: coraREData.ajaxNonce
            }, function(res) {
                $btn.prop('disabled', false);
                $icon.removeClass('animate-spin');
                $label.text('Check for Updates');
                jQuery('#cora-updates-state-checking').addClass('hidden');
                
                if (res.success) {
                    jQuery('#cora-last-check-text').text(res.data.last_checked);
                    
                    if (res.data.changelog) {
                        jQuery('#cora-update-changelog-box').html(res.data.changelog);
                    }
                    
                    coraRenderSettingsUpdateTimeline();
                    
                    if (res.data.update_available) {
                        jQuery('#cora-avail-ver-pill').text('v' + res.data.new_version);
                        jQuery('#cora-avail-target-ver').text('v' + res.data.new_version);
                        
                        if (res.data.changelog) {
                            var tempDiv = document.createElement('div');
                            tempDiv.innerHTML = res.data.changelog;
                            var firstUl = tempDiv.querySelector('ul');
                            if (firstUl) {
                                jQuery('#cora-avail-highlights-content').html(firstUl.outerHTML);
                            }
                        }
                        
                        jQuery('#cora-updates-state-available').removeClass('hidden');
                        jQuery('#cora-updates-state-uptodate').addClass('hidden');
                        jQuery('#cora-settings-changelog-section').removeClass('hidden');
                        jQuery('#cora-btn-updates-upgrade').removeClass('hidden');
                        
                        if (window.coraShowToast) window.coraShowToast('New update v' + res.data.new_version + ' is available!', 'success');
                    } else {
                        jQuery('#cora-updates-state-uptodate').removeClass('hidden');
                        jQuery('#cora-updates-state-available').addClass('hidden');
                        jQuery('#cora-settings-changelog-section').addClass('hidden');
                        jQuery('#cora-btn-updates-upgrade').addClass('hidden');
                        if (window.coraShowToast) window.coraShowToast('Your workspace is up to date on v' + res.data.current_version + '.');
                    }
                } else {
                        jQuery('#cora-updates-state-uptodate').removeClass('hidden');
                        if (window.coraShowToast) window.coraShowToast('Your workspace is already up to date.');
                    }
                } else {
                    jQuery('#cora-error-message-text').text(res.data.message || 'Failed to check updates.');
                    jQuery('#cora-updates-state-error').removeClass('hidden');
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Failed to check updates.', 'error');
                }
            }).fail(function() {
                $btn.prop('disabled', false);
                $icon.removeClass('animate-spin');
                $label.text('Check for Updates');
                jQuery('#cora-updates-state-checking').addClass('hidden');
                
                jQuery('#cora-error-message-text').text('Network connection failed. Could not query release server.');
                jQuery('#cora-updates-state-error').removeClass('hidden');
                if (window.coraShowToast) window.coraShowToast('Network error during updates check.', 'error');
            });
        }

        var confirmCoraUpdateCallback = null;

        function openCoraUpdateConfirmModal(callback) {
            confirmCoraUpdateCallback = callback;
            var $modal = jQuery('#cora-update-confirm-modal');
            $modal.removeClass('hidden');
            setTimeout(function() {
                $modal.removeClass('opacity-0').addClass('opacity-100');
                $modal.find('> div').removeClass('scale-95').addClass('scale-100');
            }, 10);
        }

        function closeCoraUpdateConfirmModal() {
            var $modal = jQuery('#cora-update-confirm-modal');
            $modal.removeClass('opacity-100').addClass('opacity-0');
            $modal.find('> div').removeClass('scale-100').addClass('scale-95');
            setTimeout(function() {
                $modal.addClass('hidden');
                confirmCoraUpdateCallback = null;
            }, 200);
        }

        function confirmCoraUpdateAction() {
            if (confirmCoraUpdateCallback) {
                confirmCoraUpdateCallback();
            }
            closeCoraUpdateConfirmModal();
        }

        function coraTriggerInAppUpgradeManual() {
            var $btn = jQuery('#cora-btn-updates-upgrade');
            var $checkBtn = jQuery('#cora-btn-updates-check');
            
            var upgradeAction = function() {
                // Disable upgrade actions
                $btn.prop('disabled', true).addClass('opacity-50 pointer-events-none');
                $checkBtn.prop('disabled', true).addClass('opacity-50 pointer-events-none');
                
                // Hide current state containers
                jQuery('#cora-updates-state-uptodate').addClass('hidden');
                jQuery('#cora-updates-state-available').addClass('hidden');
                
                // Show progress bar container
                var $progressContainer = jQuery('#cora-updates-state-progress');
                $progressContainer.removeClass('hidden');
                
                // Setup elements
                var $progressBar = jQuery('#cora-upgrade-progress-bar');
                var $percentText = jQuery('#cora-upgrade-percent-text');
                var $stepText = jQuery('#cora-upgrade-step-text');
                
                // Initialize display values
                $progressBar.css('width', '5%');
                $percentText.text('5%');
                $stepText.text('Connecting to GitHub updates server...');
                
                var progressInterval = setInterval(function() {
                    jQuery.post(coraREData.ajaxUrl, {
                        action: 'cora_get_upgrade_progress',
                        nonce: coraREData.ajaxNonce
                    }, function(res) {
                        if (res.success && res.data) {
                            var pct = parseInt(res.data.percent || 0);
                            var status = res.data.status || 'Upgrading...';
                            
                            $progressBar.css('width', pct + '%');
                            $percentText.text(pct + '%');
                            $stepText.text(status);
                            
                            if (pct >= 100) {
                                clearInterval(progressInterval);
                            }
                        }
                    });
                }, 1200);
                
                if (window.coraShowToast) window.coraShowToast('Initiating workspace upgrade shipment...', 'info');
                
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_trigger_in_app_update',
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    clearInterval(progressInterval);
                    if (res.success) {
                        $progressBar.css('width', '100%');
                        $percentText.text('100%');
                        $stepText.text('Upgrade completed successfully!');
                        if (window.coraShowToast) window.coraShowToast(res.data.message || 'Workspace upgraded successfully!', 'success');
                        setTimeout(function() { window.location.reload(); }, 2000);
                    } else {
                        $progressBar.css('width', '0%');
                        $percentText.text('0%');
                        $progressContainer.addClass('hidden');
                        
                        $btn.prop('disabled', false).removeClass('opacity-50 pointer-events-none');
                        $checkBtn.prop('disabled', false).removeClass('opacity-50 pointer-events-none');
                        jQuery('#cora-updates-state-available').removeClass('hidden');
                        
                        if (window.coraShowToast) window.coraShowToast('Upgrade Failed: ' + (res.data ? res.data.message : 'Unknown error'), 'error');
                    }
                }).fail(function() {
                    clearInterval(progressInterval);
                    
                    // Connection might terminate due to FPM/OPcache reload when plugin files are replaced.
                    // Wait 3.5 seconds and check if the upgrade actually completed successfully.
                    $stepText.text('Verifying upgrade status...');
                    if (window.coraShowToast) window.coraShowToast('Network connection recycled. Verifying upgrade...', 'info');
                    
                    setTimeout(function() {
                        var targetVer = jQuery('#cora-available-version-text').text().trim().replace(/^v/, '');
                        
                        // 1. Check if the upgrade progress option completed on the server
                        jQuery.post(coraREData.ajaxUrl, {
                            action: 'cora_get_upgrade_progress',
                            nonce: coraREData.ajaxNonce
                        }, function(progressRes) {
                            if (progressRes.success && progressRes.data && (parseInt(progressRes.data.percent) >= 95 || progressRes.data.step == 5)) {
                                $progressBar.css('width', '100%');
                                $percentText.text('100%');
                                $stepText.text('Upgrade completed successfully!');
                                if (window.coraShowToast) window.coraShowToast('Workspace upgraded successfully!', 'success');
                                setTimeout(function() { window.location.reload(); }, 1500);
                                return;
                            }
                            
                            // 2. Check the version to see if files were updated
                            jQuery.post(coraREData.ajaxUrl, {
                                action: 'cora_force_check_update',
                                nonce: coraREData.ajaxNonce
                            }, function(verRes) {
                                if (verRes.success && (verRes.data.up_to_date || verRes.data.version === targetVer || verRes.data.current_version === targetVer)) {
                                    $progressBar.css('width', '100%');
                                    $percentText.text('100%');
                                    $stepText.text('Upgrade completed successfully!');
                                    if (window.coraShowToast) window.coraShowToast('Workspace upgraded successfully!', 'success');
                                    setTimeout(function() { window.location.reload(); }, 1500);
                                } else {
                                    // Actual network failure
                                    $progressContainer.addClass('hidden');
                                    $btn.prop('disabled', false).removeClass('opacity-50 pointer-events-none');
                                    $checkBtn.prop('disabled', false).removeClass('opacity-50 pointer-events-none');
                                    jQuery('#cora-updates-state-available').removeClass('hidden');
                                    if (window.coraShowToast) window.coraShowToast('Network error during upgrade.', 'error');
                                }
                            }).fail(function() {
                                // Fallback: reload page
                                setTimeout(function() { window.location.reload(); }, 2000);
                            });
                        }).fail(function() {
                            // Fallback: reload page
                            setTimeout(function() { window.location.reload(); }, 2000);
                        });
                    }, 3500);
                });
            };

            openCoraUpdateConfirmModal(upgradeAction);
        }
        </script>


        <script>
        (function($) {
            // Full Snapshot Zip Generator
            $('#cora-trigger-full-snapshot-backup').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                $btn.prop('disabled', true).html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="animate-spin shrink-0"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Compiling Snapshot (.zip)...');
                
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_trigger_workspace_full_backup',
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res && res.success) {
                        if (window.coraShowToast) window.coraShowToast(res.data.message);
                        if (res.data.download_url) {
                            window.location.href = res.data.download_url;
                        }
                        setTimeout(function() { window.location.reload(); }, 1500);
                    } else {
                        if (window.coraShowToast) window.coraShowToast("Error: " + (res.data && res.data.message ? res.data.message : "Failed to compile snapshot."));
                        $btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg> Generate Full Snapshot (.zip)');
                    }
                }).fail(function() {
                    if (window.coraShowToast) window.coraShowToast("Network error while generating full system snapshot.");
                    $btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg> Generate Full Snapshot (.zip)');
                });
            });

            // Database Only SQL Export
            $('#cora-trigger-manual-db-backup').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                $btn.prop('disabled', true).html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="animate-spin shrink-0"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Exporting DB...');
                
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_trigger_workspace_backup',
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res && res.success) {
                        if (window.coraShowToast) window.coraShowToast(res.data.message);
                        if (res.data.download_url) {
                            window.location.href = res.data.download_url;
                        }
                        setTimeout(function() { window.location.reload(); }, 1500);
                    } else {
                        if (window.coraShowToast) window.coraShowToast("Error: " + (res.data && res.data.message ? res.data.message : "Failed to export DB."));
                        $btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg> Export Database Only (.sql)');
                    }
                }).fail(function() {
                    if (window.coraShowToast) window.coraShowToast("Network error while generating DB backup.");
                    $btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg> Export Database Only (.sql)');
                });
            });

            // Google Drive Manual Sync Button
            $('#cora-trigger-drive-sync-now').on('click', function(e) {
                e.preventDefault();
                var folderId = $('input[name="cora_backup_google_folder_id"]').val();
                var $btn = $(this);
                $btn.prop('disabled', true).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="animate-spin shrink-0"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Syncing...');
                
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_sync_google_drive_now',
                    folder_id: folderId,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    $btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg> Sync to Drive Now');
                    if (res && res.success) {
                        if (res.data.folder_id) {
                            $('input[name="cora_backup_google_folder_id"]').val(res.data.folder_id);
                        }
                        if (window.coraShowToast) window.coraShowToast(res.data.message, 'success');
                        setTimeout(function() { window.location.reload(); }, 1200);
                    } else {
                        if (window.coraShowToast) window.coraShowToast('Failed to sync to Google Drive.', 'error');
                    }
                }).fail(function() {
                    $btn.prop('disabled', false).html('Sync to Drive Now');
                    if (window.coraShowToast) window.coraShowToast('Network error while syncing to Google Drive.', 'error');
                });
            });

            // Google Drive: Save credentials and redirect to Google OAuth
            $('#cora-save-google-creds').on('click', function(e) {
                e.preventDefault();
                var clientId = $('#cora-google-client-id-input').val().trim();
                var clientSecret = $('#cora-google-client-secret-input').val().trim();
                if (!clientId || !clientSecret) {
                    if (window.coraShowToast) window.coraShowToast('Please enter both your Google Client ID and Client Secret.', 'error');
                    return;
                }
                var $btn = $(this);
                $btn.prop('disabled', true).text('Saving...');
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_get_google_auth_url',
                    client_id: clientId,
                    client_secret: clientSecret,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res && res.success && res.data.auth_url) {
                        if (window.coraShowToast) window.coraShowToast('Redirecting to Google sign-in...', 'info');
                        setTimeout(function() { window.location.href = res.data.auth_url; }, 600);
                    } else {
                        $btn.prop('disabled', false).text('Save Credentials & Connect Google Drive');
                        if (window.coraShowToast) window.coraShowToast(res.data ? res.data.message : 'Failed to generate auth URL.', 'error');
                    }
                }).fail(function() {
                    $btn.prop('disabled', false).text('Save Credentials & Connect Google Drive');
                    if (window.coraShowToast) window.coraShowToast('Network error. Please try again.', 'error');
                });
            });

            // Google Drive: Redirect to Google OAuth (State 2 button)
            $('#cora-connect-google-drive').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                $btn.prop('disabled', true).text('Redirecting to Google...');
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_get_google_auth_url',
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res && res.success && res.data.auth_url) {
                        window.location.href = res.data.auth_url;
                    } else {
                        $btn.prop('disabled', false).text('Connect with Google');
                        if (window.coraShowToast) window.coraShowToast('Failed to get auth URL. Check your credentials.', 'error');
                    }
                });
            });

            // Show toast on Google OAuth success/error redirect
            (function() {
                var urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('google_connected') === '1') {
                    if (window.coraShowToast) window.coraShowToast('Google Drive connected successfully!', 'success');
                } else if (urlParams.get('google_error')) {
                    if (window.coraShowToast) window.coraShowToast('Google Drive error: ' + decodeURIComponent(urlParams.get('google_error')), 'error');
                }
            })();

            // Disconnect Google Drive
            $('#cora-disconnect-google-drive').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                $btn.prop('disabled', true).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Disconnecting...');
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_disconnect_google_drive',
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res && res.success) {
                        if (window.coraShowToast) window.coraShowToast(res.data.message, 'success');
                        setTimeout(function() { window.location.reload(); }, 900);
                    } else {
                        $btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg> Disconnect');
                        if (window.coraShowToast) window.coraShowToast('Failed to disconnect Google Drive.', 'error');
                    }
                }).fail(function() {
                    $btn.prop('disabled', false).html('Disconnect');
                    if (window.coraShowToast) window.coraShowToast('Network error while disconnecting Google Drive.', 'error');
                });
            });
        })(jQuery);

        <!-- Custom Confirmation Drawer (replaces all browser confirm() dialogs) -->
        <div id="cora-confirm-drawer" class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center" style="display:none !important;">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="cora-confirm-backdrop"></div>
            <div class="relative w-full max-w-sm mx-4 mb-4 sm:mb-0 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden" style="animation: slideUpFade 0.18s ease;">
                <div class="p-5">
                    <div class="flex items-start gap-3 mb-4">
                        <div id="cora-confirm-icon-wrap" class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-zinc-100 dark:bg-zinc-800">
                            <svg id="cora-confirm-icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0" id="cora-confirm-title">Confirm Action</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed" id="cora-confirm-message"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="button" id="cora-confirm-cancel" class="flex-1 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-xl transition-all cursor-pointer">Cancel</button>
                        <button type="button" id="cora-confirm-ok" class="flex-1 py-2 text-xs font-bold text-white bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white rounded-xl transition-all cursor-pointer" id="cora-confirm-ok">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Custom Prompt Drawer (replaces all browser prompt() dialogs) -->
        <div id="cora-prompt-drawer" class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center" style="display:none !important;">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="cora-prompt-backdrop"></div>
            <div class="relative w-full max-w-sm mx-4 mb-4 sm:mb-0 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden" style="animation: slideUpFade 0.18s ease;">
                <div class="p-5">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-zinc-100 dark:bg-zinc-800">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0" id="cora-prompt-title">Input Required</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed" id="cora-prompt-message"></p>
                        </div>
                    </div>
                    <div class="mb-4" id="cora-prompt-input-container">
                        <!-- Dynamically populated select or input -->
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="button" id="cora-prompt-cancel" class="flex-1 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-xl transition-all cursor-pointer">Cancel</button>
                        <button type="button" id="cora-prompt-ok" class="flex-1 py-2 text-xs font-bold text-white bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white rounded-xl transition-all cursor-pointer">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <style>
        @keyframes slideUpFade { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        </style>
        <script>
        // Custom Confirm Utility — replaces all browser confirm() calls
        window.coraConfirm = function(opts, onConfirm) {
            var drawer = document.getElementById('cora-confirm-drawer');
            document.getElementById('cora-confirm-title').textContent   = opts.title   || 'Confirm';
            document.getElementById('cora-confirm-message').textContent = opts.message || '';
            var okBtn = document.getElementById('cora-confirm-ok');
            okBtn.textContent = opts.okLabel || 'Confirm';
            if (opts.danger) {
                okBtn.className = 'flex-1 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all cursor-pointer';
            } else {
                okBtn.className = 'flex-1 py-2 text-xs font-bold text-white bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white rounded-xl transition-all cursor-pointer';
            }
            drawer.style.cssText = 'display:flex !important; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center;';
            var close = function() { drawer.style.cssText = 'display:none !important;'; };
            document.getElementById('cora-confirm-cancel').onclick   = close;
            document.getElementById('cora-confirm-backdrop').onclick = close;
            okBtn.onclick = function() { close(); onConfirm(); };
        };

        // Custom Prompt Utility — replaces all browser prompt() calls
        window.coraPrompt = function(opts, onSubmit) {
            var drawer = document.getElementById('cora-prompt-drawer');
            document.getElementById('cora-prompt-title').textContent = opts.title || 'Input Required';
            document.getElementById('cora-prompt-message').textContent = opts.message || '';
            
            var container = document.getElementById('cora-prompt-input-container');
            container.innerHTML = '';
            
            var input;
            if (opts.type === 'select') {
                input = document.createElement('select');
                input.className = 'w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none';
                opts.options.forEach(function(o) {
                    var opt = document.createElement('option');
                    opt.value = o.value;
                    opt.textContent = o.label;
                    if (o.value === opts.defaultValue) opt.selected = true;
                    input.appendChild(opt);
                });
            } else {
                input = document.createElement('input');
                input.type = opts.type || 'text';
                input.value = opts.defaultValue || '';
                input.placeholder = opts.placeholder || '';
                input.className = 'w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none';
            }
            container.appendChild(input);
            
            drawer.style.cssText = 'display:flex !important; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center;';
            var close = function() { drawer.style.cssText = 'display:none !important;'; };
            document.getElementById('cora-prompt-cancel').onclick   = close;
            document.getElementById('cora-prompt-backdrop').onclick = close;
            
            document.getElementById('cora-prompt-ok').onclick = function() {
                var val = input.value;
                close();
                onSubmit(val);
            };
        };
        </script>

        <script>
        // Restore Backup Handler
        window.coraRestoreBackup = function(filename) {
            window.coraConfirm({
                title:   'Restore Platform Snapshot',
                message: 'Restore to "' + filename + '"? A pre-restore safety snapshot will be created automatically before restoring.',
                okLabel: 'Yes, Restore'
            }, function() {
                if (window.coraShowToast) window.coraShowToast('Restoring platform snapshot...', 'info');
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_restore_workspace_backup',
                    file: filename,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res && res.success) {
                        if (window.coraShowToast) window.coraShowToast(res.data.message, 'success');
                        setTimeout(function() { window.location.reload(); }, 2000);
                    } else {
                        if (window.coraShowToast) window.coraShowToast('Restore Error: ' + (res.data ? res.data.message : 'Unknown error'), 'error');
                    }
                });
            });
        };

        // Delete Backup Handler
        window.coraDeleteBackup = function(filename) {
            window.coraConfirm({
                title:   'Delete Snapshot',
                message: 'Permanently delete "' + filename + '"? This cannot be undone.',
                okLabel: 'Delete',
                danger:  true
            }, function() {
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_delete_workspace_backup',
                    file: filename,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res && res.success) {
                        if (window.coraShowToast) window.coraShowToast(res.data.message);
                        setTimeout(function() { window.location.reload(); }, 1000);
                    }
                });
            });
        };

        window.coraSetDefaultPremiumFavicon = function() {
            var url = "<?php echo esc_url( CORA_WORKSPACE_URL . 'assets/images/cora-favicon.png' ); ?>";
            jQuery('#cora-brand-favicon-url-suite').val(url).trigger('change');
            if (window.coraShowToast) window.coraShowToast("Premium Monogram Icon selected as Favicon.");
        };
        </script>

    </form>
</div>
</div>

<script>
(function($) {
    if (typeof coraAutoSave !== 'undefined') {
        coraAutoSave.attachForm('#cora-settings-suite-form', 'settings_suite', 'cora_save_system_settings_suite');
    }

    $('#cora-purge-legacy-options').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('Purging options...');
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_purge_options_data',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data || 'Old options cache database tables purged successfully!');
                setTimeout(function() { window.location.reload(); }, 1200);
            } else {
                window.coraShowToast(res.data || 'Failed to purge database options.');
                $btn.prop('disabled', false).text('Purge Old wp_options Cache');
            }
        }).fail(function() {
            window.coraShowToast('A system error occurred during purge.');
            $btn.prop('disabled', false).text('Purge Old wp_options Cache');
        });
    });
})(jQuery);
</script>
</div><!-- end cora-shopify-settings-theme -->
