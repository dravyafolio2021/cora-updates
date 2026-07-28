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
$pages      = get_pages();
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
        'label' => 'Reading & SEO',
        'desc'  => 'Homepage and search engines',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>'
    ),
    'writing'    => array(
        'label' => 'Writing',
        'desc'  => 'Category & format variables',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>'
    ),
    'discussion' => array(
        'label' => 'Discussion',
        'desc'  => 'Moderation & blacklists',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>'
    ),
    'permalinks' => array(
        'label' => 'Permalinks',
        'desc'  => 'SEO URL structures',
        'icon'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>'
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
    )
);
?>

<style>
/* Shopify-style Complete Settings Suite Stylesheet */
.cora-shopify-settings-theme {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
}
.cora-shopify-settings-theme label:not(.flex):not(.cora-label-raw) {
    display: block !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #52525b !important; /* zinc-600 */
    text-transform: none !important;
    letter-spacing: normal !important;
    margin-bottom: 6px !important;
}
.dark .cora-shopify-settings-theme label:not(.flex):not(.cora-label-raw) {
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
    border: 1px solid #d4d4d8 !important; /* zinc-300 */
    border-radius: 6px !important;
    padding: 6px 12px !important;
    font-size: 12px !important;
    color: #18181b !important; /* zinc-900 */
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04) !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.cora-shopify-settings-theme input[type="text"]:focus,
.cora-shopify-settings-theme input[type="email"]:focus,
.cora-shopify-settings-theme input[type="password"]:focus,
.cora-shopify-settings-theme input[type="number"]:focus,
.cora-shopify-settings-theme input[type="url"]:focus,
.cora-shopify-settings-theme select:focus {
    outline: none !important;
    border-color: #18181b !important; /* zinc-900 */
    box-shadow: 0 0 0 1px #18181b, 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
}
/* Dark Mode Override */
.dark .cora-shopify-settings-theme input[type="text"],
.dark .cora-shopify-settings-theme input[type="email"],
.dark .cora-shopify-settings-theme input[type="password"],
.dark .cora-shopify-settings-theme input[type="number"],
.dark .cora-shopify-settings-theme input[type="url"],
.dark .cora-shopify-settings-theme select {
    background-color: #09090b !important; /* zinc-950 */
    border-color: #27272a !important; /* zinc-800 */
    color: #f4f4f5 !important; /* zinc-100 */
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2) !important;
}
.dark .cora-shopify-settings-theme input[type="text"]:focus,
.dark .cora-shopify-settings-theme input[type="email"]:focus,
.dark .cora-shopify-settings-theme input[type="password"]:focus,
.dark .cora-shopify-settings-theme input[type="number"]:focus,
.dark .cora-shopify-settings-theme input[type="url"]:focus,
.dark .cora-shopify-settings-theme select:focus {
    border-color: #f4f4f5 !important; /* zinc-100 */
    box-shadow: 0 0 0 1px #f4f4f5, 0 1px 2px 0 rgba(0, 0, 0, 0.25) !important;
}
/* Shopify discrete card blocks */
.cora-shopify-card {
    background-color: #ffffff !important;
    border: 1px solid #e4e4e7 !important;
    border-radius: 8px !important;
    padding: 20px 24px !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.01), 0 1px 2px 0 rgba(0, 0, 0, 0.02) !important;
}
.dark .cora-shopify-card {
    background-color: #18181b !important; /* zinc-900 */
    border-color: #27272a !important; /* zinc-800 */
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
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
</style>

<div class="cora-shopify-settings-theme">
<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">System Settings Complete Suite</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Global network parameters, reading/writing defaults, discussion moderation rules, and SEO permalinks.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button type="button" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold rounded-md text-xs transition-colors flex items-center gap-1.5 border border-zinc-200 dark:border-zinc-700 shadow-2xs cursor-pointer" onclick="coraClearSystemCache()">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
            Clear Cache
        </button>
        <button type="button" class="cora-btn-primary px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-xs transition-colors cursor-pointer flex items-center gap-2 shadow-sm" onclick="coraSaveSystemSettingsSuite()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save All Settings
        </button>
    </div>
</div>

<!-- Mobile Horizontal Tab Strip (Hidden on Desktop) -->
<div class="lg:hidden flex overflow-x-auto gap-2 pb-3 mb-2 scrollbar-none border-b border-zinc-200/50 dark:border-zinc-800/40 w-full" style="-webkit-overflow-scrolling: touch;">
    <?php
    $tabs = $cora_settings_tabs;
    foreach ( $tabs as $tab_key => $tab ) :
        $is_active = ( $active_tab === $tab_key );
    ?>
    <a href="#" onclick="window.coraSwitchSettingsTab('<?php echo esc_js( $tab_key ); ?>'); return false;" data-settings-tab-mobile="<?php echo esc_attr( $tab_key ); ?>" class="cora-settings-nav-mobile flex items-center gap-1.5 px-3.5 py-2.5 rounded-full text-xs font-bold whitespace-nowrap transition-colors shrink-0 <?php echo $is_active ? 'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 active-tab' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-850 dark:text-zinc-300 dark:hover:bg-zinc-800'; ?>">
        <span class="shrink-0 <?php echo $is_active ? 'text-white dark:text-zinc-950' : 'text-zinc-550 dark:text-zinc-400'; ?>">
            <?php echo $tab['icon']; ?>
        </span>
        <span><?php echo esc_html( $tab['label'] ); ?></span>
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
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">General Site Configuration</h3>
                    <p class="text-xs text-zinc-500">Core identity and default user registration parameters.</p>
                </div>
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
            </div>

            <!-- Card 2: Workspace Details Section -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">General Workspace Settings</h3>
                    <p class="text-xs text-zinc-500">Corporate identity, localized workspace address, and billing tax descriptors.</p>
                </div>
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
                    jQuery(document).ready(function($) {
                        $('#cora-site-title-input, #cora-site-tagline-input').on('input', function() {
                            $(this).data('user-edited', true);
                        });
                        const currentInd = $('#cora-settings-industry-select').val() || '<?php echo esc_js($is_studio ? "photography_studio" : "real_estate"); ?>';
                        coraFilterRolesByIndustry(currentInd);
                    });
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
            </div>

            <!-- Card 3: Database Clean Up Section -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-red-650">Database Optimization</h3>
                    <p class="text-xs text-zinc-500">Clean up legacy key-value storage once you have verified custom database tables are fully working.</p>
                </div>
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
            </div>
        </div>

        <!-- TAB 2: PASSWORD POLICY SETTINGS -->
        <div id="cora-settings-panel-pwd-policy" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'pwd-policy' ? '' : 'hidden'; ?>">
            <!-- Card: Password Policy -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Workspace Password Policy</h3>
                    <p class="text-xs text-zinc-500">Enforce minimum complexity guidelines for passwords across logins, setups, and resets.</p>
                </div>
                
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
                        'value'   => $agency_id,
                        'compare' => '='
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
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                <div>
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Brokerage Branches</h3>
                    <p class="text-xs text-zinc-500">Manage multiple physical offices, assign localized managers, and monitor regional agent counts.</p>
                </div>
                <button type="button" onclick="openCreateBranchDrawer()" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    New Branch
                </button>
            </div>

            <div class="cora-shopify-card p-0 overflow-hidden">
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
                                    <tr class="hover:bg-zinc-50/10">
                                        <td class="px-5 py-3.5 font-bold text-zinc-900"><?php echo esc_html( $b['name'] ); ?></td>
                                        <td class="px-5 py-3.5 text-zinc-500 font-semibold"><?php echo esc_html( $b['city'] . ' / ' . $b['address'] ); ?></td>
                                        <td class="px-5 py-3.5 font-semibold text-zinc-700">
                                            <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-800 text-[9px] font-bold">
                                                <?php echo esc_html( $mgr_name ); ?>
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 font-bold text-zinc-900"><?php echo esc_html( $crew_count ); ?> Agents</td>
                                        <td class="px-5 py-3.5 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" onclick="openEditBranchDrawer('<?php echo esc_attr($b_id); ?>', '<?php echo esc_attr($b['name']); ?>', '<?php echo esc_attr($b['city']); ?>', '<?php echo esc_attr($b['address']); ?>', '<?php echo esc_attr($b['manager_id'] ?? ''); ?>')" class="px-2.5 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 cursor-pointer shadow-sm transition-colors">Edit</button>
                                                <button type="button" onclick="deleteBranch('<?php echo esc_attr($b_id); ?>', <?php echo $crew_count; ?>)" class="px-2.5 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-red-600 bg-white hover:bg-red-50 hover:border-red-200 cursor-pointer shadow-sm transition-colors">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
                        <input type="text" id="new-branch-name" required placeholder="e.g. Westside HQ" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">City</label>
                        <input type="text" id="new-branch-city" required placeholder="e.g. Mumbai" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Office Address</label>
                        <input type="text" id="new-branch-address" required placeholder="e.g. 402, Bandra Kurla Complex" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
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
                        <input type="text" id="edit-branch-name" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">City</label>
                        <input type="text" id="edit-branch-city" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Office Address</label>
                        <input type="text" id="edit-branch-address" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
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
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Brand Assets</h3>
                    <p class="text-xs text-zinc-500">Configure your agency's logo and browser favicon.</p>
                </div>
                
                <!-- Agency Logo Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/40">
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label>Agency Logo URL</label>
                            <div class="flex gap-2">
                                <input type="url" id="cora-brand-logo-url-suite" name="cora_brand_logo_url" value="<?php echo esc_url( get_option('cora_brand_logo_url', '') ); ?>" placeholder="https://...">
                                <button type="button" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-semibold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraOpenMediaSelector('cora-brand-logo-url-suite')">Browse</button>
                            </div>
                        </div>
                        <p class="text-[11px] text-zinc-400">Upload your real estate group's official logo. This will be used on all shared portfolios and custom client portals.</p>
                    </div>
                    <div class="flex items-center justify-center border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-950 p-3 h-28 cursor-pointer hover:border-zinc-400 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900/60 transition-all group" onclick="coraOpenMediaSelector('cora-brand-logo-url-suite')" title="Click to upload logo">
                        <?php $logo_url = get_option('cora_brand_logo_url', ''); ?>
                        <div id="cora-suite-logo-preview" class="w-full h-full flex items-center justify-center overflow-hidden">
                            <?php if ( ! empty( $logo_url ) ) : ?>
                                <img src="<?php echo esc_url( $logo_url ); ?>" class="max-h-full max-w-full object-contain transition-transform group-hover:scale-105" alt="Logo Preview" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                <div class="hidden text-center space-y-1">
                                    <svg class="mx-auto h-5 w-5 text-zinc-400 group-hover:text-zinc-650 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <span class="block text-[9px] text-zinc-400 font-bold uppercase tracking-wider">Upload Logo</span>
                                </div>
                            <?php else : ?>
                                <div class="text-center space-y-1">
                                    <svg class="mx-auto h-5 w-5 text-zinc-400 group-hover:text-zinc-650 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <span class="block text-[9px] text-zinc-400 font-bold uppercase tracking-wider">Upload Logo</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Custom Favicon Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/40">
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label>Custom Favicon URL (32x32 / 64x64 PNG)</label>
                            <div class="flex gap-2">
                                <input type="url" id="cora-brand-favicon-url-suite" name="cora_brand_favicon_url" value="<?php echo esc_url( get_option('cora_brand_favicon_url', '') ); ?>" placeholder="https://...">
                                <button type="button" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-semibold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraOpenMediaSelector('cora-brand-favicon-url-suite')">Browse</button>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="px-2.5 py-1.5 border border-zinc-200 dark:border-zinc-850 hover:border-zinc-450 bg-white dark:bg-zinc-900 text-zinc-750 dark:text-zinc-300 font-semibold text-[10px] rounded transition-colors cursor-pointer" onclick="coraSetDefaultPremiumFavicon()">
                                Set to Premium Monogram Icon
                            </button>
                            <button type="button" class="px-2.5 py-1.5 border border-zinc-200 dark:border-zinc-850 hover:border-zinc-450 bg-white dark:bg-zinc-900 text-zinc-750 dark:text-zinc-300 font-semibold text-[10px] rounded transition-colors cursor-pointer" onclick="document.getElementById('cora-brand-favicon-url-suite').value='';">
                                Clear Favicon
                            </button>
                            <button type="button" class="px-2.5 py-1.5 border border-zinc-200 dark:border-zinc-850 hover:border-zinc-450 bg-zinc-950 text-white font-semibold text-[10px] rounded transition-colors cursor-pointer" onclick="if(window.coraApplyBrandingLive) window.coraApplyBrandingLive();">
                                Apply to Browser Tab Now
                            </button>
                        </div>
                        <script>
                            function coraSetDefaultPremiumFavicon() {
                                const url = "<?php echo esc_url( CORA_WORKSPACE_URL . 'assets/images/cora-favicon.png' ); ?>";
                                document.getElementById('cora-brand-favicon-url-suite').value = url;
                                const img = document.querySelector('#cora-suite-favicon-preview img');
                                if (img) {
                                    img.src = url;
                                } else {
                                    document.getElementById('cora-suite-favicon-preview').innerHTML = `<img src="${url}" class="w-10 h-10 object-contain" alt="Favicon Preview">`;
                                }
                                window.coraShowToast("Premium Monogram Icon selected as Favicon.");
                            }
                        </script>
                        <p class="text-[11px] text-zinc-400">Configure your website browser tab favicon.</p>
                    </div>
                    <div class="flex flex-col items-center justify-center border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-950 p-3 h-28 space-y-1.5 cursor-pointer hover:border-zinc-400 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900/60 transition-all group" onclick="coraOpenMediaSelector('cora-brand-favicon-url-suite')" title="Click to upload favicon">
                        <span class="text-[9px] text-zinc-450 dark:text-zinc-500 uppercase font-bold tracking-wider group-hover:text-zinc-700 dark:group-hover:text-zinc-300 transition-colors">Tab Favicon</span>
                        <?php 
                        $favicon_url = get_option('cora_brand_favicon_url', ''); 
                        if ( empty( $favicon_url ) ) {
                            $favicon_url = CORA_WORKSPACE_URL . 'assets/images/cora-favicon.png';
                        }
                        ?>
                        <div id="cora-suite-favicon-preview" class="w-12 h-12 flex items-center justify-center border border-zinc-100 dark:border-zinc-850 rounded-md bg-zinc-50 dark:bg-zinc-900 transition-transform group-hover:scale-105">
                            <img src="<?php echo esc_url( $favicon_url ); ?>" class="w-8 h-8 object-contain" alt="Favicon Preview">
                        </div>
                    </div>
                </div>

                <!-- Browser Tab & Sidebar Title Settings -->
                <div class="cora-shopify-card space-y-4">
                    <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Browser Tab &amp; Sidebar Title Settings</h3>
                        <p class="text-xs text-zinc-500 mt-0.5">Control how your site name appears in the browser tab and sidebar.</p>
                    </div>
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
                    </div>
                </div>
            </div>

            <!-- Card 2: Developer Keys & API Integrations -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Third-Party Developer Keys</h3>
                    <p class="text-xs text-zinc-500">Provide map keys and CRM notification credentials.</p>
                </div>
                
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
                    <div>
                        <label>WhatsApp Cloud API Token</label>
                        <?php $wa_token = get_option('cora_whatsapp_api_token', ''); ?>
                        <input type="password" name="cora_whatsapp_api_token" value="<?php echo esc_attr( $wa_token ? str_repeat('•', 24) : '' ); ?>" placeholder="EAAW..." class="cora-credential-input" oncopy="return false;" oncut="return false;" ondragstart="return false;" ondrop="return false;" autocomplete="off">
                    </div>
                    <div>
                        <label>WhatsApp Business Phone ID</label>
                        <input type="text" name="cora_whatsapp_phone_number" value="<?php echo esc_attr( get_option('cora_whatsapp_phone_number', '') ); ?>" placeholder="e.g. 1093847291039">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: READING & SEO SETTINGS -->
        <div id="cora-settings-panel-reading" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'reading' ? '' : 'hidden'; ?>">
            <!-- Card 1: Homepage Displays -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Reading & Search Engine Indexing</h3>
                    <p class="text-xs text-zinc-500">Control homepage display mode and static page assignments.</p>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label>Homepage Displays</label>
                        <div class="space-y-2.5 mt-2">
                            <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-medium cursor-pointer">
                                <input type="radio" name="show_on_front" value="posts" <?php checked( get_option('show_on_front'), 'posts' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                                <span class="cora-label-raw">Your latest blog posts feed</span>
                            </label>
                            <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-medium cursor-pointer">
                                <input type="radio" name="show_on_front" value="page" <?php checked( get_option('show_on_front'), 'page' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                                <span class="cora-label-raw">A static landing page</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label>Static Homepage</label>
                            <select name="page_on_front">
                                <option value="0">— Select Page —</option>
                                <?php foreach ( $pages as $p ) : ?>
                                    <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('page_on_front'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Posts Page (Blog Archive)</label>
                            <select name="page_for_posts">
                                <option value="0">— Select Page —</option>
                                <?php foreach ( $pages as $p ) : ?>
                                    <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('page_for_posts'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Crawler Visibility -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Search Engine Crawler Visibility</h3>
                    <p class="text-xs text-zinc-500">Configure robots.txt headers and search engine crawlers visibility permissions.</p>
                </div>
                <div class="p-4 border border-red-200 dark:border-red-950/60 bg-red-50/30 dark:bg-red-950/5 rounded-lg">
                    <label class="flex items-start gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="blog_public" value="0" <?php checked( get_option('blog_public'), 0 ); ?> class="rounded border-zinc-300 text-red-600 focus:ring-red-650 mt-0.5">
                        <div class="space-y-0.5">
                            <span class="text-red-700 dark:text-red-400 font-bold cora-label-raw">Discourage search engines from indexing this site</span>
                            <p class="text-[11px] text-zinc-550 dark:text-zinc-400 font-normal">Modifies robots.txt and meta tags. Note: It is up to search engines to honor this request.</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- TAB 6: WRITING DEFAULTS -->
        <div id="cora-settings-panel-writing" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'writing' ? '' : 'hidden'; ?>">
            <!-- Card: Writing Defaults -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Writing & Content Defaults</h3>
                    <p class="text-xs text-zinc-500">Configure default taxonomy labeling and publishing format presets.</p>
                </div>
                
                <div class="space-y-4 max-w-md">
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
            </div>
        </div>

        <!-- TAB 7: DISCUSSION & MODERATION -->
        <div id="cora-settings-panel-discussion" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'discussion' ? '' : 'hidden'; ?>">
            <!-- Card 1: Comment Moderation Policies -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Comment Moderation Policies</h3>
                    <p class="text-xs text-zinc-500">Enforce global submission guidelines for blog discussions and listing comments.</p>
                </div>
                
                <div class="space-y-3.5">
                    <label class="flex items-center gap-2.5 text-xs text-zinc-850 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="default_pingback_flag" value="1" <?php checked( get_option('default_pingback_flag'), 1 ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                        <span class="cora-label-raw">Allow link notifications from other blogs (pingbacks and trackbacks)</span>
                    </label>
                    
                    <label class="flex items-center gap-2.5 text-xs text-zinc-850 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="default_comment_status" value="open" <?php checked( get_option('default_comment_status'), 'open' ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                        <span class="cora-label-raw">Allow people to submit comments on new articles</span>
                    </label>
                    
                    <label class="flex items-center gap-2.5 text-xs text-zinc-850 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" name="comment_moderation" value="1" <?php checked( get_option('comment_moderation'), 1 ); ?> class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                        <span class="cora-label-raw">Comment must be manually approved before publishing</span>
                    </label>
                </div>
            </div>

            <!-- Card 2: Spam Filtering & Moderation Keywords -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Spam Filtering & Moderation Keywords</h3>
                    <p class="text-xs text-zinc-500">Automate comment holds and trash actions using exact keyword triggers.</p>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label>Comment Moderation Queue Keywords</label>
                        <textarea name="moderation_keys" rows="3" placeholder="One word, IP address, or URL per line..." class="w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-900 dark:text-zinc-100 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 shadow-3xs"><?php echo esc_textarea( get_option('moderation_keys') ); ?></textarea>
                        <p class="text-[10px] text-zinc-400 mt-1">When a comment contains any of these words, it will be held in the moderation queue.</p>
                    </div>
                    <div>
                        <label class="text-red-700 dark:text-red-400">Disallowed Comment Keys (Automatic Trash/Spam)</label>
                        <textarea name="disallowed_keys" rows="3" placeholder="One word, IP address, or URL per line..." class="w-full bg-white dark:bg-zinc-950 border border-red-200 dark:border-red-900/60 rounded-lg p-2.5 text-xs text-zinc-900 dark:text-zinc-100 font-mono focus:outline-none focus:ring-1 focus:ring-red-650 dark:focus:ring-red-650 shadow-3xs"><?php echo esc_textarea( get_option('disallowed_keys') ); ?></textarea>
                        <p class="text-[10px] text-zinc-400 mt-1">Comments matching these triggers will be instantly moved to trash.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 8: SEO PERMALINKS -->
        <div id="cora-settings-panel-permalinks" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'permalinks' ? '' : 'hidden'; ?>">
            <!-- Card: SEO URL Permalinks -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">SEO URL Permalinks Structure</h3>
                    <p class="text-xs text-zinc-500">Choose clean, human-readable URL routing schemas for better search engine rankings.</p>
                </div>
                
                <div class="space-y-3">
                    <?php $current_permalink = get_option('permalink_structure'); ?>
                    
                    <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/50 dark:hover:bg-zinc-900/60 cursor-pointer transition-colors gap-2">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="permalink_structure" value="" <?php checked( $current_permalink, '' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 cora-label-raw">Plain</span>
                        </div>
                        <code class="text-[10px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/?p=123') ); ?></code>
                    </label>

                    <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/50 dark:hover:bg-zinc-900/60 cursor-pointer transition-colors gap-2">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="permalink_structure" value="/%year%/%monthnum%/%day%/%postname%/" <?php checked( $current_permalink, '/%year%/%monthnum%/%day%/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 cora-label-raw">Day and name</span>
                        </div>
                        <code class="text-[10px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/2026/07/08/sample-post/') ); ?></code>
                    </label>

                    <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/50 dark:hover:bg-zinc-900/60 cursor-pointer transition-colors gap-2">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="permalink_structure" value="/%year%/%monthnum%/%postname%/" <?php checked( $current_permalink, '/%year%/%monthnum%/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 cora-label-raw">Month and name</span>
                        </div>
                        <code class="text-[10px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/2026/07/sample-post/') ); ?></code>
                    </label>

                    <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-zinc-900 dark:border-zinc-100 rounded-lg bg-zinc-900/5 dark:bg-zinc-100/5 hover:bg-zinc-900/10 dark:hover:bg-zinc-100/10 cursor-pointer transition-colors gap-2">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="permalink_structure" value="/%postname%/" <?php checked( $current_permalink, '/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <div>
                                <span class="text-xs font-bold text-zinc-900 dark:text-white cora-label-raw">Post name (Recommended SEO)</span>
                            </div>
                        </div>
                        <code class="text-[10px] text-zinc-900 dark:text-white font-bold font-mono truncate break-all"><?php echo esc_url( home_url('/sample-post/') ); ?></code>
                    </label>
                </div>
            </div>
        </div>

        <!-- TAB 9: PRIVACY POLICY -->
        <div id="cora-settings-panel-privacy" class="cora-settings-panel space-y-6 max-w-3xl <?php echo $active_tab === 'privacy' ? '' : 'hidden'; ?>">
            <!-- Card: Privacy Policy -->
            <div class="cora-shopify-card space-y-4">
                <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Privacy Policy Page Assignment</h3>
                    <p class="text-xs text-zinc-500">Designate an official privacy policy page for legal compliance and user transparency.</p>
                </div>
                
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
            </div>
        </div>
        <!-- TAB 10: GIT SYNC (LOVABLE & GITHUB) -->
        <div id="cora-settings-panel-git-sync" class="cora-settings-panel max-w-full <?php echo $active_tab === 'git-sync' ? '' : 'hidden'; ?> grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            <!-- Left side: Form Settings Card -->
            <div class="xl:col-span-7 space-y-6">
                <!-- Card 1: GitHub Connection -->
                <div class="cora-shopify-card space-y-4">
                    <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <!-- GitHub Logo SVG (official mark) -->
                            <div class="w-9 h-9 rounded-xl bg-zinc-950 dark:bg-zinc-900 border border-zinc-800 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg viewBox="0 0 98 96" width="20" height="20" fill="#ffffff" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">GitHub Connection</h3>
                                <p class="text-xs text-zinc-500 mt-0.5">Sync your website code from a GitHub repository.</p>
                            </div>
                        </div>
                        <?php $git_token = get_option('cora_git_sync_token', ''); ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold <?php echo $git_token ? 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400' : 'bg-zinc-100 text-zinc-650 dark:bg-zinc-850 dark:text-zinc-400'; ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo $git_token ? 'bg-green-500 animate-pulse' : 'bg-zinc-400'; ?>"></span>
                            <?php echo $git_token ? 'Connected' : 'Not Connected'; ?>
                        </span>
                    </div>

                    <?php if ( empty( $git_token ) ) : ?>
                        <div class="space-y-3">
                            <!-- Token prompt hint box -->
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400 mt-0.5 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <p class="text-[10px] text-zinc-500 leading-relaxed">Generate a <strong class="text-zinc-700 dark:text-zinc-300">Personal Access Token</strong> from <a href="https://github.com/settings/tokens" target="_blank" class="underline underline-offset-2 text-zinc-700 dark:text-zinc-300 hover:text-zinc-900">GitHub Developer Settings</a> with <code class="text-[9px] bg-zinc-200 dark:bg-zinc-800 px-1 py-0.5 rounded font-mono">repo</code> scope enabled.</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1.5">Personal Access Token</label>
                                <input type="password" id="cora-git-token-input" placeholder="ghp_xxxxxxxxxxxxxxxxxxxx" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 cora-credential-input font-mono tracking-widest" autocomplete="off">
                            </div>
                            <button type="button" onclick="coraConnectGitHub()" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-white text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-3xs">
                                <svg viewBox="0 0 98 96" width="13" height="13" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z"/></svg>
                                Connect GitHub Account
                            </button>
                        </div>
                    <?php else : ?>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1.5">GitHub Repository</label>
                                    <?php $saved_repo = get_option('cora_git_sync_repo', ''); ?>
                                    <div class="relative" id="cora-git-repo-searchable-select-container" data-saved-url="<?php echo esc_url($saved_repo); ?>">
                                        <!-- Trigger -->
                                        <div id="cora-repo-select-trigger" style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:7px 10px;font-size:11px;background:#fafafa;border:1px solid #e4e4e7;border-radius:8px;cursor:pointer;transition:border-color .15s;user-select:none;">
                                            <div style="display:flex;align-items:center;gap:6px;min-width:0;">
                                                <svg viewBox="0 0 98 96" width="12" height="12" fill="#71717a"><path fill-rule="evenodd" clip-rule="evenodd" d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z"/></svg>
                                                <span id="cora-repo-select-display-text" style="color:#a1a1aa;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">Loading repositories...</span>
                                            </div>
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="#a1a1aa" stroke-width="2" fill="none" id="cora-repo-select-arrow" style="flex-shrink:0;transition:transform .2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>

                                        <!-- Dropdown Panel -->
                                        <div id="cora-repo-select-dropdown" style="display:none;position:absolute;left:0;right:0;z-index:9999;margin-top:4px;background:#fff;border:1px solid #e4e4e7;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.04);overflow:hidden;">
                                            <!-- Search -->
                                            <div style="padding:8px;border-bottom:1px solid #f4f4f5;">
                                                <input type="text" id="cora-git-repo-search-input" placeholder="Search repositories..." style="width:100%;padding:6px 10px;font-size:11px;font-family:inherit;background:#fafafa;border:1px solid #e4e4e7;border-radius:6px;color:#18181b;outline:none;box-shadow:none;box-sizing:border-box;" autocomplete="off">
                                            </div>
                                            <!-- List -->
                                            <div id="cora-repo-options-list" style="max-height:220px;overflow-y:auto;padding:4px 0;">
                                                <div style="padding:8px 12px;font-size:11px;color:#a1a1aa;font-style:italic;">Loading...</div>
                                            </div>
                                        </div>

                                        <!-- Manual entry (hidden) -->
                                        <div id="cora-git-repo-manual-container" class="mt-2" style="display:none;">
                                            <input type="text" id="cora-git-repo-manual-input" name="cora_git_sync_repo" value="<?php echo esc_attr( $saved_repo ); ?>" placeholder="https://github.com/user/repo" style="width:100%;padding:7px 10px;font-size:11px;font-family:inherit;background:#fafafa;border:1px solid #e4e4e7;border-radius:8px;color:#18181b;outline:none;box-shadow:none;box-sizing:border-box;">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <?php $saved_branch = get_option('cora_git_sync_branch', 'main'); ?>
                                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1.5">Branch</label>
                                    <div class="relative" id="cora-git-branch-searchable-select-container" data-saved-branch="<?php echo esc_attr($saved_branch); ?>">
                                        <!-- Trigger -->
                                        <div id="cora-branch-select-trigger" style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:7px 10px;font-size:11px;background:#fafafa;border:1px solid #e4e4e7;border-radius:8px;cursor:pointer;transition:border-color .15s;user-select:none;">
                                            <div style="display:flex;align-items:center;gap:6px;min-width:0;">
                                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#a1a1aa" stroke-width="2"><line x1="6" y1="3" x2="6" y2="15"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>
                                                <span id="cora-branch-select-display-text" style="color:#a1a1aa;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">Select branch...</span>
                                            </div>
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="#a1a1aa" stroke-width="2" fill="none" id="cora-branch-select-arrow" style="flex-shrink:0;transition:transform .2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                        <!-- Dropdown -->
                                        <div id="cora-branch-select-dropdown" style="display:none;position:absolute;left:0;right:0;z-index:9999;margin-top:4px;background:#fff;border:1px solid #e4e4e7;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.04);overflow:hidden;">
                                            <div style="padding:8px;border-bottom:1px solid #f4f4f5;">
                                                <input type="text" id="cora-git-branch-search-input" placeholder="Search branches..." style="width:100%;padding:6px 10px;font-size:11px;font-family:inherit;background:#fafafa;border:1px solid #e4e4e7;border-radius:6px;color:#18181b;outline:none;box-shadow:none;box-sizing:border-box;" autocomplete="off">
                                            </div>
                                            <div id="cora-branch-options-list" style="max-height:200px;overflow-y:auto;padding:4px 0;">
                                                <div style="padding:8px 12px;font-size:11px;color:#a1a1aa;font-style:italic;">Select a repository first...</div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="cora-git-branch-value" name="cora_git_sync_branch" value="<?php echo esc_attr($saved_branch); ?>">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="cora_git_sync_token" value="<?php echo esc_attr( $git_token ); ?>">
                            
                            <button type="button" onclick="coraDisconnectGitHub()" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 text-[10px] font-semibold rounded-lg transition-colors cursor-pointer">
                                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Disconnect GitHub
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Card 2: Lovable Integration -->
                <div class="cora-shopify-card space-y-4">
                    <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-4 flex items-center justify-between">
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
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Lovable Integration</h3>
                                <p class="text-xs text-zinc-500 mt-0.5">Connect your live Lovable URL to preview and sync updates.</p>
                            </div>
                        </div>
                        <?php $live_url = get_option('cora_git_sync_live_url', ''); ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold <?php echo $live_url ? 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400' : 'bg-zinc-100 text-zinc-650 dark:bg-zinc-850 dark:text-zinc-400'; ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo $live_url ? 'bg-green-500 animate-pulse' : 'bg-zinc-400'; ?>"></span>
                            <?php echo $live_url ? 'Connected &amp; Live' : 'Not Connected'; ?>
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1.5">Lovable Live URL</label>
                            <input type="text" name="cora_git_sync_live_url" id="cora-lovable-live-url" value="<?php echo esc_attr( $live_url ); ?>" placeholder="https://your-app.lovable.app" style="width:100%;padding:7px 10px;font-size:11px;font-family:inherit;background:#fafafa;border:1px solid #e4e4e7;border-radius:8px;color:#18181b;outline:none;box-shadow:none;box-sizing:border-box;">
                            <p class="text-[10px] text-zinc-450 mt-1.5 leading-relaxed">Optional. If your repository contains React source code rather than compiled static files, enter your live Lovable deployment URL here.</p>
                        </div>
                        <?php if ( ! empty( $live_url ) ) : ?>
                            <button type="button" onclick="coraDisconnectLovable()" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 text-[10px] font-semibold rounded-lg transition-colors cursor-pointer">
                                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Disconnect Lovable Project
                            </button>
                        <?php endif; ?>
                    </div>
                </div>


                </div>

            <!-- Right side: Onboarding & Instructions Card -->
            <div class="xl:col-span-5 space-y-5 cora-shopify-card dark:bg-zinc-900/60 bg-zinc-50/50">
                <div class="flex items-center gap-2.5 pb-2 border-b border-zinc-200 dark:border-zinc-800/40">
                    <span class="text-zinc-900 dark:text-zinc-100 shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
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
                            <p class="leading-relaxed">Build your real estate client site or application on <a href="https://lovable.dev" target="_blank" class="underline font-semibold text-zinc-950 dark:text-white">Lovable.dev</a> using plain English, and publish it to a GitHub repository.</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex gap-3">
                        <div class="w-5 h-5 rounded-full bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[10px] shrink-0">2</div>
                        <div class="space-y-1">
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Generate a GitHub Token</span>
                            <p class="leading-relaxed mb-2">If your repository is Private, generate a security token so Cora can access the code.</p>
                            <a href="https://github.com/settings/tokens/new?scopes=repo&description=Cora%20Git%20Sync" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-white text-[10px] font-bold rounded-lg transition-colors shadow-3xs cursor-pointer">
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
                            <div class="w-9 h-5 bg-zinc-200 dark:bg-zinc-850 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 dark:after:border-zinc-800 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950 dark:peer-checked:bg-zinc-100"></div>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="cora-shopify-card border-l-2 border-l-emerald-500 shadow-2xs">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">System Health</div>
                    <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        100% Operational
                    </div>
                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">24h Automation Active</div>
                </div>

                <div class="cora-shopify-card border-l-2 border-l-zinc-400 dark:border-l-zinc-600 shadow-2xs">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Last Snapshot Taken</div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">
                        <?php 
                        echo !empty($history) ? esc_html(date('M j, Y H:i', $history[0]['time'])) : 'No backups yet';
                        ?>
                    </div>
                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Full System (.zip) & SQL</div>
                </div>

                <div class="cora-shopify-card border-l-2 <?php echo $is_google_connected ? 'border-l-emerald-500' : 'border-l-zinc-400 dark:border-l-zinc-600'; ?> shadow-2xs">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Google Drive Storage</div>
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

                <div class="cora-shopify-card border-l-2 border-l-indigo-500 shadow-2xs">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Restore Guard</div>
                    <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        Auto Safety Point
                    </div>
                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Snapshot before restore</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Backup Configuration & System Snapshots -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Card 1: Full System Snapshot & Export Generator -->
                    <div class="cora-shopify-card space-y-4">
                        <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                            <div class="flex items-center gap-2 mb-0.5">
                                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 m-0">Full System Snapshot & Export</h3>
                                <span class="text-[10px] font-mono font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded-full">v2.1.0 Ready</span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Generate full system backup archives (.zip) or lightweight database SQL files.</p>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-150 dark:border-zinc-800/50 rounded-xl space-y-3">
                            <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                                <strong class="text-zinc-900 dark:text-zinc-100">Full System Snapshot (.zip)</strong> packages the complete database schema, row records, environment manifest, active module states, and asset indexes into a single compressed backup archive. Standard database export (.sql) exports table structures and rows only.
                            </p>
                            <div class="border-t border-zinc-200/60 dark:border-zinc-700/60 pt-3 flex flex-wrap gap-2.5">
                                <!-- Primary: Full System Snapshot Zip Button -->
                                <button type="button" id="cora-trigger-full-snapshot-backup" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-sm active:scale-97">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    Generate Full Snapshot (.zip)
                                </button>

                                <!-- Secondary: Database Only SQL Button -->
                                <button type="button" id="cora-trigger-manual-db-backup" class="px-4 py-2.5 bg-transparent hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-sm">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                                    Export Database Only (.sql)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: 24h Automated Google Drive Sync -->
                    <div class="cora-shopify-card space-y-4" id="cora-google-drive-card">
                        <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3">
                            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 m-0">24-Hour Automated Google Drive Sync</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Automatically upload daily platform snapshots to your personal Google Drive.</p>
                        </div>

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
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5">
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

                    </div>



                </div>

                <!-- Right 1 Col: Snapshot Logs & One-Click Restore Center -->
                <div class="space-y-6">
                    <div class="cora-shopify-card space-y-4">
                        <div class="border-b border-zinc-100 dark:border-zinc-800/40 pb-3 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Backup Logs & Restore Center</h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Stored system restore points & archives.</p>
                            </div>
                            <span class="text-[10px] font-mono text-zinc-500 dark:text-zinc-400">cora-backups/</span>
                        </div>

                        <div id="cora-backups-history-list" class="space-y-3">
                            <?php 
                            if ( ! empty( $history ) ) :
                                foreach ( $history as $item ) :
                                    $is_zip = ( strpos( $item['filename'], '.zip' ) !== false );
                                    $display_name = strlen($item['filename']) > 24 ? substr($item['filename'], 0, 24) . '...' : $item['filename'];
                            ?>
                                <div class="cora-shopify-card !p-3 flex flex-col gap-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 text-zinc-600 dark:text-zinc-400">
                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-xs text-zinc-900 dark:text-zinc-100 truncate m-0" title="<?php echo esc_attr( $item['filename'] ); ?>"><?php echo esc_html( $display_name ); ?></p>
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <span class="text-[10px] text-zinc-500 dark:text-zinc-400"><?php echo esc_html( date( 'M j, Y &bull; H:i IST', $item['time'] ) ); ?></span>
                                                    <span class="text-[10px] text-zinc-400">&bull;</span>
                                                    <span class="text-[10px] text-zinc-500 dark:text-zinc-400 px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded"><?php echo esc_html( $item['size'] ); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-[9px] font-bold px-2 py-1 rounded-md shrink-0 <?php echo $is_zip ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300'; ?>">
                                            <?php echo $is_zip ? '.zip' : 'SQL'; ?>
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/60">
                                        <button type="button" onclick="coraRestoreBackup('<?php echo esc_js($item['filename']); ?>')" class="px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 font-bold text-[10px] rounded-lg transition-colors cursor-pointer flex items-center gap-1.5">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M3 2v6h6"></path><path d="M3 13a9 9 0 1 0 3-7.7L3 8"></path></svg>
                                            Restore
                                        </button>
                                        <div class="flex items-center gap-2">
                                            <a href="<?php echo admin_url('admin-ajax.php?action=cora_download_backup&file=' . urlencode( $item['filename'] ) . '&nonce=' . wp_create_nonce('cora_download_backup_nonce')); ?>" class="p-1.5 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/50 dark:hover:bg-zinc-800 rounded-md transition-colors" title="Download Archive">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            </a>
                                            <div class="w-[1px] h-3 bg-zinc-200 dark:bg-zinc-700"></div>
                                            <button type="button" onclick="coraDeleteBackup('<?php echo esc_js($item['filename']); ?>')" class="p-1.5 text-red-500 hover:text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-950/40 rounded-md transition-colors cursor-pointer" title="Delete Log">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
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
                    </div>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
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
        });

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
        function coraRestoreBackup(filename) {
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
        }

        // Delete Backup Handler
        function coraDeleteBackup(filename) {
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
        }
        </script>

    </form>
</div>
</div>

<script>
jQuery(document).ready(function($) {
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
});
</script>
</div><!-- end cora-shopify-settings-theme -->
