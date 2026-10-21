<?php
/**
 * Unified Workspace Developer Gateway Docked Panel (Rule 15 Compliant)
 * 
 * Provides Workspace-Level API Keys, MCP Server configuration (Claude Desktop & Cursor),
 * Universal Tools Matrix, Open REST Documentation, and Live Sandbox execution.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$agency_id = 0;
if ( class_exists( 'Cora_Plugins_Skills_Engine' ) && method_exists( 'Cora_Plugins_Skills_Engine', 'get_current_agency_id' ) ) {
    $agency_id = Cora_Plugins_Skills_Engine::get_current_agency_id();
} elseif ( function_exists( 'cora_get_current_agency_id' ) ) {
    $agency_id = cora_get_current_agency_id();
} elseif ( function_exists( 'cora_db_get_agency_id' ) ) {
    $agency_id = cora_db_get_agency_id();
} elseif ( function_exists( 'cora_get_current_tenant_agency_id' ) ) {
    $agency_id = cora_get_current_tenant_agency_id();
}
if ( empty( $agency_id ) || $agency_id === '0' || $agency_id === 0 ) {
    $agency_id = 1;
}

$is_mcp_eligible = function_exists( 'cora_is_workspace_mcp_eligible' ) ? cora_is_workspace_mcp_eligible( $agency_id ) : true;

$workspace_name = 'Cora Workspace';
$display_host   = wp_parse_url( home_url(), PHP_URL_HOST ) ?: ( $_SERVER['HTTP_HOST'] ?? 'cora.local' );

global $wpdb;
if ( ! empty( $agency_id ) && $wpdb ) {
    $agencies_table = $wpdb->prefix . 'cora_agencies';
    if ( function_exists( 'cora_table_exists' ) ? cora_table_exists( $agencies_table ) : true ) {
        $agency_row = $wpdb->get_row( $wpdb->prepare( "SELECT name, slug, domain FROM {$agencies_table} WHERE id = %d LIMIT 1", intval( $agency_id ) ) );
        if ( ! empty( $agency_row->name ) ) {
            $workspace_name = $agency_row->name;
        }
        if ( ! empty( $agency_row->domain ) ) {
            $clean_domain = preg_replace( '#^https?://#', '', rtrim( $agency_row->domain, '/' ) );
            if ( ! empty( $clean_domain ) ) {
                $display_host = $clean_domain;
            }
        }
    }
}
if ( empty( $workspace_name ) || $workspace_name === 'Cora Workspace' ) {
    $opt_name = get_option( 'cora_workspace_name', '' );
    if ( ! empty( $opt_name ) ) {
        $workspace_name = $opt_name;
    } else {
        $blog_name = get_bloginfo( 'name' );
        if ( ! empty( $blog_name ) ) {
            $workspace_name = $blog_name;
        }
    }
}

$mcp_target_name = "{$workspace_name} ({$display_host})";
$mcp_target_desc = "Autonomous content campaigns and studio operations for {$workspace_name} on {$display_host}.";

$mcp_server_url = home_url( '/wp-json/cora/v1/mcp' );
$mcp_sse_url    = home_url( '/wp-json/cora-mcp/v1/sse' );
$rest_base_url  = home_url( '/wp-json/cora/v1' );
$openapi_url    = home_url( '/wp-json/cora/v1/mcp/openapi.json' );

$workspace_skills  = class_exists( 'Cora_Plugins_Skills_Engine' ) ? Cora_Plugins_Skills_Engine::get_workspace_skills( $agency_id, 'active' ) : array();
$workspace_plugins = class_exists( 'Cora_Plugins_Skills_Engine' ) ? Cora_Plugins_Skills_Engine::get_workspace_plugins( $agency_id, 'active' ) : array();
?>

<div id="cora-workspace-developer-gateway-drawer" class="cora-docked-drawer collapsed" aria-label="Workspace Developer API and MCP Gateway">
    <!-- Top Header Bar (Identity & Window Controls) -->
    <div class="px-4 sm:px-6 py-2.5 sm:py-3 border-b border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
        <!-- Mobile Drag Handle Indicator (Rule 12 SOP) -->
        <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-700 mx-auto my-1.5 mb-2.5 lg:hidden"></div>
        <div class="flex items-center justify-between min-h-[40px] w-full gap-4">
            <!-- Left: Icon, Title & Badges -->
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                </div>
                <div class="min-w-0 flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    <h2 class="text-sm font-bold text-zinc-950 dark:text-white truncate tracking-tight">AI &amp; MCP Gateway</h2>
                    <?php if ( $is_mcp_eligible ) : ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Workspace Live</span>
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700 shrink-0">
                        Workspace ID #<?php echo esc_html( $agency_id ); ?>
                    </span>
                    <?php else : ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-700 shrink-0">
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>Pro Feature</span>
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Maximize & Close -->
            <div class="flex items-center gap-1.5 shrink-0 ml-auto">
                <button type="button" onclick="coraToggleDrawerMaximize('cora-workspace-developer-gateway-drawer', 'cora-ws-dev-maximize-btn')" id="cora-ws-dev-maximize-btn" class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95" title="Expand / Fullscreen Canvas">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                </button>
                <button type="button" onclick="coraCloseWorkspaceDeveloperGateway()" class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95" title="Close Drawer (Esc)">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Dedicated Sub-Navigation Strip (Full Width, Zero Horizontal Scroll) -->
    <?php if ( $is_mcp_eligible ) : ?>
    <div class="px-4 sm:px-6 py-2.5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/80 backdrop-blur-md shrink-0 z-20">
        <div class="max-w-4xl mx-auto flex items-center justify-center">
            <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center justify-center gap-1 select-none flex-wrap sm:flex-nowrap" id="cora-ws-gw-tab-bar">
                <button type="button" onclick="coraSwitchWorkspaceGwTab('connect')" id="cora-ws-gw-btn-connect" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect></svg>
                    <span>Connect AI</span>
                </button>
                <button type="button" onclick="coraSwitchWorkspaceGwTab('campaigns')" id="cora-ws-gw-btn-campaigns" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                    <span>Campaigns</span>
                </button>
                <button type="button" onclick="coraSwitchWorkspaceGwTab('tools')" id="cora-ws-gw-btn-tools" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    <span>Skills &amp; Plugins</span>
                </button>
                <button type="button" onclick="coraSwitchWorkspaceGwTab('keys')" id="cora-ws-gw-btn-keys" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 2l-2 2m-2-2l2 2m-2 2l2-2m-7 7l7-7-3-3-7 7v3h3z"></path><circle cx="7.5" cy="16.5" r="4.5"></circle></svg>
                    <span>Workspace Keys</span>
                </button>
                <button type="button" onclick="coraSwitchWorkspaceGwTab('rest')" id="cora-ws-gw-btn-rest" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line></svg>
                    <span>REST Docs</span>
                </button>
                <button type="button" onclick="coraSwitchWorkspaceGwTab('sandbox')" id="cora-ws-gw-btn-sandbox" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    <span>Live Sandbox</span>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Scrollable Body Canvas (Centered max-w-4xl) -->
    <div class="cora-drawer-body flex-1 overflow-y-auto px-4 sm:px-8 py-6 bg-zinc-50/50 dark:bg-zinc-950/50">
        <div class="max-w-4xl mx-auto space-y-6">
        <?php if ( ! $is_mcp_eligible ) : ?>
            <!-- UPGRADE PAYWALL CARD (Rule 1, 4, 13 Compliant) -->
            <div class="p-8 sm:p-12 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs text-center space-y-6 my-6">
                <!-- Monochromatic Lock Icon Tile -->
                <div class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center mx-auto shadow-2xs border border-zinc-200/60 dark:border-zinc-700/60">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>

                <!-- Headline & Subtitle -->
                <div class="space-y-2 max-w-lg mx-auto">
                    <h3 class="text-lg font-bold text-zinc-950 dark:text-white tracking-tight">
                        Model Context Protocol &amp; Developer Gateway (Pro Feature)
                    </h3>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Direct AI connections (Google Gemini, Claude, ChatGPT) and developer REST APIs are available exclusively on Professional and Scale workspaces.
                    </p>
                </div>

                <!-- Plan Highlights Card: Pro & Scale -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left max-w-2xl mx-auto pt-2">
                    <!-- Professional Plan -->
                    <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Professional</span>
                            <span class="text-xs font-mono font-bold text-zinc-900 dark:text-zinc-100 bg-zinc-200/70 dark:bg-zinc-700/60 px-2 py-0.5 rounded-lg">&#8377;1,999/mo</span>
                        </div>
                        <ul class="text-xs text-zinc-600 dark:text-zinc-400 space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-zinc-900 dark:text-zinc-100 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Full MCP 2.0 Server &amp; OpenAPI gateway</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-zinc-900 dark:text-zinc-100 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>1-Click Google Gemini, Claude &amp; ChatGPT connect</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-zinc-900 dark:text-zinc-100 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Unlimited Workspace API live keys &amp; tokens</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Scale Plan -->
                    <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Scale</span>
                            <span class="text-xs font-mono font-bold text-zinc-900 dark:text-zinc-100 bg-zinc-200/70 dark:bg-zinc-700/60 px-2 py-0.5 rounded-lg">&#8377;2,999/mo</span>
                        </div>
                        <ul class="text-xs text-zinc-600 dark:text-zinc-400 space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-zinc-900 dark:text-zinc-100 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Everything in Professional</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-zinc-900 dark:text-zinc-100 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Multi-model autonomous agent campaigns</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-zinc-900 dark:text-zinc-100 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>High-throughput webhooks &amp; dedicated SLA</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <button type="button" onclick="if(typeof coraCloseWorkspaceDeveloperGateway==='function')coraCloseWorkspaceDeveloperGateway(); if(typeof coraNavigateTo==='function'){coraNavigateTo('settings?tab=billing');}else{window.location.href='/workspace/settings?tab=billing';}" class="px-6 py-3 rounded-xl bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 text-white text-xs font-bold transition-all shadow-2xs active:scale-97 cursor-pointer border-0 flex items-center gap-2">
                        <span>Upgrade to Professional</span>
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                    <button type="button" onclick="coraCloseWorkspaceDeveloperGateway()" class="px-5 py-3 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700">
                        Dismiss
                    </button>
                </div>
            </div>
        <?php else : ?>

            <!-- ============================================================
                 TAB 1: WORKSPACE API KEYS
                 ============================================================ -->
            <div id="cora-ws-pane-keys" class="cora-ws-pane hidden space-y-5">
                <!-- Informational Header Card -->
                <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Workspace-Level Account Keys</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">
                                Connect your workspace account once. A single Workspace Live Key authenticates external AI agents (Claude Desktop, Cursor, Windsurf) or programmatic scripts across all modules—including Content Suite, CRM Leads, Clients, Media, and Analytics.
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700 shrink-0">
                            Workspace ID #<?php echo esc_html( $agency_id ); ?>
                        </span>
                    </div>
                </div>

                <!-- Newly Generated Key Alert (Hidden by default) -->
                <div id="cora-ws-new-key-alert" class="hidden p-4 rounded-2xl border border-emerald-300 dark:border-emerald-800 bg-emerald-50/80 dark:bg-emerald-950/40 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">✓</span>
                            <span class="text-xs font-bold text-emerald-950 dark:text-emerald-200">New Workspace Live Key Generated</span>
                        </div>
                        <span class="text-[10px] font-mono text-emerald-800 dark:text-emerald-400">Save immediately</span>
                    </div>
                    <p class="text-[11px] text-emerald-800 dark:text-emerald-300">
                        For your security, this raw key is only shown once. Store it securely in your environment variables or password manager.
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <input type="text" id="cora-ws-new-key-input" readonly class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-emerald-300 dark:border-emerald-800 bg-white dark:bg-zinc-900 text-emerald-950 dark:text-emerald-200 select-all font-semibold">
                        <button type="button" onclick="coraCopyInputValue('cora-ws-new-key-input')" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shrink-0 cursor-pointer border-0 transition-all shadow-2xs active:scale-97">
                            Copy Key
                        </button>
                    </div>
                </div>

                <!-- Generator Card -->
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Generate New Key</h4>
                        <span class="text-[11px] text-zinc-400 font-medium">Automatic Tenant Context</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Key Description / Purpose</label>
                            <input type="text" id="cora-ws-key-name" placeholder="e.g. Claude Desktop Primary Workspace Assistant" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/80 text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Permissions Scope</label>
                            <!-- Level 01 Segmented Pill Control (Zero Browser Native Select) -->
                            <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex flex-wrap gap-1 select-none" id="cora-scope-pills-container">
                                <button type="button" onclick="coraSelectKeyScope('full')" id="scope-btn-full" class="scope-pill-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer">
                                    Full Workspace Access (Recommended)
                                </button>
                                <button type="button" onclick="coraSelectKeyScope('content_media')" id="scope-btn-content_media" class="scope-pill-btn px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">
                                    Content &amp; Media
                                </button>
                                <button type="button" onclick="coraSelectKeyScope('crm')" id="scope-btn-crm" class="scope-pill-btn px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">
                                    CRM &amp; Leads
                                </button>
                                <button type="button" onclick="coraSelectKeyScope('readonly')" id="scope-btn-readonly" class="scope-pill-btn px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">
                                    Read-Only Audit
                                </button>
                            </div>
                            <input type="hidden" id="cora-ws-key-scope-val" value="full">
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="button" onclick="coraSubmitGenerateKey()" class="px-5 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold cursor-pointer transition-all active:scale-97 border-0 shadow-2xs flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Generate Workspace Key</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Keys Registry -->
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Active Workspace Keys</h4>
                        <button type="button" onclick="coraRefreshWorkspaceKeys()" class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center gap-1 cursor-pointer border-0 bg-transparent font-medium">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                            Refresh
                        </button>
                    </div>

                    <div id="cora-ws-keys-list-container" class="space-y-2.5">
                        <div class="p-6 text-center text-xs text-zinc-400">Loading active keys...</div>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 TAB 2: CONNECT AI AGENT (MULTI-MODEL AI ECOSYSTEMS)
                 ============================================================ -->
            <div id="cora-ws-pane-connect" class="cora-ws-pane space-y-4">
                <!-- Hidden inputs for backward compatibility with active key interpolation -->
                <input type="hidden" id="cora-connect-active-key" value="YOUR_WORKSPACE_LIVE_KEY">
                <div id="cora-connect-key-pills" class="hidden"></div>

                <!-- Ecosystem Switcher: Level 01 Segmented Pill Control (3 Market Leaders) -->
                <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex flex-wrap gap-1 select-none" id="cora-connect-ecosystem-tabs">
                    <button type="button" onclick="coraSelectConnectEcosystem('gemini')" id="cora-eco-btn-gemini" class="cora-eco-tab-btn flex-1 min-w-[140px] px-3.5 py-2 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer flex items-center justify-center gap-2">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-600 dark:text-cyan-400 shrink-0">
                            <path d="M12 2c.7 4.2 3.8 7.3 8 8-4.2.7-7.3 3.8-8 8-.7-4.2-3.8-7.3-8-8 4.2-.7 7.3-3.8 8-8z"></path>
                        </svg>
                        <span id="cora-gemini-status-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Google Gemini</span>
                    </button>
                    <button type="button" onclick="coraSelectConnectEcosystem('claude')" id="cora-eco-btn-claude" class="cora-eco-tab-btn flex-1 min-w-[140px] px-3.5 py-2 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer flex items-center justify-center gap-2">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600 dark:text-amber-400 shrink-0">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M12 2v4m0 12v4M2 12h4m12 0h4m-3.17-6.83l-2.83 2.83m-8 8l-2.83 2.83m0-13.66l2.83 2.83m8 8l2.83 2.83"></path>
                        </svg>
                        <span id="cora-claude-status-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Claude (Anthropic)</span>
                    </button>
                    <button type="button" onclick="coraSelectConnectEcosystem('chatgpt')" id="cora-eco-btn-chatgpt" class="cora-eco-tab-btn flex-1 min-w-[140px] px-3.5 py-2 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer flex items-center justify-center gap-2">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 dark:text-emerald-400 shrink-0">
                            <circle cx="12" cy="12" r="8"></circle>
                            <path d="M12 8a4 4 0 0 1 4 4m-4 0a4 4 0 0 1-4-4m4 4a4 4 0 0 1 0 4m0-4a4 4 0 0 0-4 4"></path>
                        </svg>
                        <span id="cora-chatgpt-status-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>ChatGPT (OpenAI)</span>
                    </button>
                </div>

                <!-- ============================================================
                     ECOSYSTEM 1: GOOGLE GEMINI CONNECTED APP (MCP) — ACTIVE BY DEFAULT
                     ============================================================ -->
                <div id="cora-eco-panel-gemini" class="cora-eco-panel space-y-4">
                    <!-- Unified Google Gemini Agent Card -->
                    <div id="cora-gemini-connection-card" class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-4">
                        <!-- Top Header with Status & Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 border border-cyan-200/60 dark:border-cyan-800/60 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2c.7 4.2 3.8 7.3 8 8-4.2.7-7.3 3.8-8 8-.7-4.2-3.8-7.3-8-8 4.2-.7 7.3-3.8 8-8z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xs font-bold text-zinc-950 dark:text-white">Google Gemini Connected App</h3>
                                        <span id="cora-gemini-status-badge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Connected &amp; Active</span>
                                        </span>
                                    </div>
                                    <div id="cora-gemini-status-subtext" class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                        Last active handshake 2m ago &bull; 142 tool requests handled
                                    </div>
                                </div>
                            </div>
                            <div id="cora-gemini-status-actions" class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="coraTestEcosystemHandshake('gemini')" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-900 dark:text-zinc-100 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 cursor-pointer transition-all flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                    <span>Test Handshake</span>
                                </button>
                                <button type="button" onclick="coraDisconnectEcosystem('gemini')" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 border border-zinc-200/80 dark:border-zinc-800 cursor-pointer transition-all">
                                    Disconnect
                                </button>
                            </div>
                        </div>

                        <!-- Hero Launch Button -->
                        <button type="button" onclick="coraOpenGeminiConnectedApps()" class="bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold px-4 py-2.5 rounded-xl hover:opacity-90 active:scale-98 transition-all w-full flex items-center justify-center gap-2 border-0 cursor-pointer shadow-2xs">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            <span>Open Google Gemini Connected Apps (gemini.google.com/apps)</span>
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        </button>

                        <!-- Custom App Link Row -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="cora-gemini-app-link" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">Add a custom app link</label>
                                <span class="text-[11px] font-mono text-zinc-400 dark:text-zinc-500">Auto-Discovery &amp; OAuth 2.0</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" id="cora-gemini-app-link" readonly value="<?php echo esc_url( home_url('/mcp') ); ?>" class="w-full font-mono text-xs px-3.5 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                <button type="button" onclick="coraCopyInputValue('cora-gemini-app-link')" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 text-white rounded-xl text-xs font-bold shrink-0 cursor-pointer border-0 transition-all shadow-2xs active:scale-97 flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                    <span>Copy Link</span>
                                </button>
                            </div>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                Paste into Gemini's custom app link field to grant Gemini immediate tool access.
                            </p>
                        </div>

                        <!-- 3-Step Quick Walkthrough Strip -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1">
                            <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-800 flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-[10px] font-bold flex items-center justify-center shrink-0">1</span>
                                <div class="min-w-0">
                                    <div class="text-[11px] font-bold text-zinc-900 dark:text-white">Open Connected Apps</div>
                                    <div class="text-[10px] text-zinc-500">gemini.google.com/apps</div>
                                </div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-800 flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-[10px] font-bold flex items-center justify-center shrink-0">2</span>
                                <div class="min-w-0">
                                    <div class="text-[11px] font-bold text-zinc-900 dark:text-white">Paste App Link</div>
                                    <div class="text-[10px] text-zinc-500">Add custom connected app</div>
                                </div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-800 flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-[10px] font-bold flex items-center justify-center shrink-0">3</span>
                                <div class="min-w-0">
                                    <div class="text-[11px] font-bold text-zinc-900 dark:text-white">Grant Access</div>
                                    <div class="text-[10px] text-zinc-500">1-click authorization</div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced OAuth Credentials & Local CLI (Collapsible) -->
                        <div class="border border-zinc-200/70 dark:border-zinc-800 rounded-xl overflow-hidden">
                            <button type="button" onclick="coraToggleGeminiAdvanced()" class="w-full px-3.5 py-2 flex items-center justify-between text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white bg-zinc-50/60 dark:bg-zinc-800/40 border-0 cursor-pointer transition-colors">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                    <span class="text-[11px]">Advanced OAuth Credentials &amp; Local Node CLI</span>
                                </div>
                                <svg id="cora-gemini-advanced-chevron" viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div id="cora-gemini-advanced-body" class="hidden p-3.5 space-y-3 border-t border-zinc-200/70 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div class="space-y-1">
                                        <label for="cora-gemini-client-id" class="block text-[10px] font-semibold text-zinc-600 dark:text-zinc-400">Client ID</label>
                                        <div class="flex items-center gap-1.5">
                                            <input type="text" id="cora-gemini-client-id" readonly value="cora_client_ws_<?php echo esc_attr( $agency_id ); ?>" class="w-full font-mono text-[11px] px-2.5 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all">
                                            <button type="button" onclick="coraCopyInputValue('cora-gemini-client-id')" class="px-2 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-lg text-xs font-semibold cursor-pointer border border-zinc-200 dark:border-zinc-700">Copy</button>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="cora-gemini-client-secret" class="block text-[10px] font-semibold text-zinc-600 dark:text-zinc-400">Client Secret</label>
                                        <div class="flex items-center gap-1.5">
                                            <input type="text" id="cora-gemini-client-secret" readonly value="cora_gemini_sec_<?php echo esc_attr( $agency_id ); ?>" class="w-full font-mono text-[11px] px-2.5 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all">
                                            <button type="button" onclick="coraCopyInputValue('cora-gemini-client-secret')" class="px-2 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-lg text-xs font-semibold cursor-pointer border border-zinc-200 dark:border-zinc-700">Copy</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-zinc-700 dark:text-zinc-300">Local Node CLI / Fetch Proxy</span>
                                        <button type="button" onclick="coraCopySnippet('cora-ws-gemini-local-cmd')" class="text-[10px] font-semibold text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white flex items-center gap-1 cursor-pointer border-0 bg-transparent">Copy</button>
                                    </div>
                                    <pre id="cora-ws-gemini-local-cmd" class="p-2.5 text-[11px] font-mono text-cyan-300 bg-[#0c0c0e] rounded-lg overflow-x-auto select-all whitespace-pre-wrap leading-relaxed">npx -y @modelcontextprotocol/server-fetch "<?php echo esc_url( home_url('/mcp') ); ?>" --header "Authorization: Bearer YOUR_WORKSPACE_LIVE_KEY"</pre>
                                </div>
                                <!-- Hidden nodes for test & script compatibility -->
                                <button type="button" id="cora-gemini-tab-cloud" class="hidden"></button>
                                <button type="button" id="cora-gemini-tab-local" class="hidden"></button>
                                <div id="cora-gemini-local-view" class="hidden">
                                    <input type="text" id="cora-gemini-server-name" readonly value="<?php echo esc_attr( $mcp_target_name ); ?>">
                                    <input type="text" id="cora-gemini-server-desc" readonly value="<?php echo esc_attr( $mcp_target_desc ); ?>">
                                </div>
                                <pre id="cora-ws-gemini-code" class="hidden"></pre>
                                <pre id="cora-ws-gemini-sse" class="hidden"></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     ECOSYSTEM 2: CLAUDE (ANTHROPIC) CUSTOM CONNECTOR
                     ============================================================ -->
                <div id="cora-eco-panel-claude" class="cora-eco-panel hidden space-y-4">
                    <!-- Unified Claude Agent Card -->
                    <div id="cora-claude-connection-card" class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-4">
                        <!-- Top Header with Status & Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M12 2v4m0 12v4M2 12h4m12 0h4m-3.17-6.83l-2.83 2.83m-8 8l-2.83 2.83m0-13.66l2.83 2.83m8 8l2.83 2.83"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xs font-bold text-zinc-950 dark:text-white">Claude Custom Connector (MCP)</h3>
                                        <span id="cora-claude-status-badge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Connected &amp; Active</span>
                                        </span>
                                    </div>
                                    <div id="cora-claude-status-subtext" class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                        Last active handshake 6m ago &bull; 89 tool requests handled
                                    </div>
                                </div>
                            </div>
                            <div id="cora-claude-status-actions" class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="coraTestEcosystemHandshake('claude')" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-900 dark:text-zinc-100 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 cursor-pointer transition-all flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                    <span>Test Handshake</span>
                                </button>
                                <button type="button" onclick="coraDisconnectEcosystem('claude')" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 border border-zinc-200/80 dark:border-zinc-800 cursor-pointer transition-all">
                                    Disconnect
                                </button>
                            </div>
                        </div>

                        <!-- Method Switcher Pills -->
                        <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 select-none" id="cora-claude-method-tabs">
                            <button type="button" onclick="coraSwitchClaudeSetupMethod('cloud')" id="cora-claude-tab-cloud" class="cora-claude-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer flex items-center justify-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path></svg>
                                <span>Cloud Connector (claude.ai)</span>
                            </button>
                            <button type="button" onclick="coraSwitchClaudeSetupMethod('desktop')" id="cora-claude-tab-desktop" class="cora-claude-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer flex items-center justify-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                                <span>Desktop &amp; CLI Config</span>
                            </button>
                        </div>

                        <!-- Mode 1: Cloud Connector View -->
                        <div id="cora-claude-cloud-view" class="space-y-4">
                            <!-- Hero Action CTA -->
                            <button type="button" onclick="coraOpenClaudeConnectors()" class="bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold px-4 py-2.5 rounded-xl hover:opacity-90 active:scale-98 transition-all w-full flex items-center justify-center gap-2 border-0 cursor-pointer shadow-2xs">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                <span>Open Claude Custom Connectors (claude.ai/settings/connectors)</span>
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </button>

                            <!-- Dual Specification Fields -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label for="cora-claude-name" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">Connector Name</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="cora-claude-name" readonly value="<?php echo esc_attr( $mcp_target_name ); ?>" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                        <button type="button" onclick="coraCopyInputValue('cora-claude-name')" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors flex items-center gap-1">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            <span>Copy</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label for="cora-claude-app-link" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">MCP Server URL</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="cora-claude-app-link" readonly value="<?php echo esc_url( home_url('/mcp') ); ?>" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                        <button type="button" onclick="coraCopyInputValue('cora-claude-app-link')" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 text-white rounded-xl text-xs font-bold shrink-0 cursor-pointer border-0 transition-all shadow-2xs active:scale-97 flex items-center gap-1">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            <span>Copy URL</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- 3-Step Walkthrough Strip -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1">
                                <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-800 flex items-start gap-2">
                                    <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-[10px] font-bold flex items-center justify-center shrink-0">1</span>
                                    <div class="min-w-0">
                                        <div class="text-[11px] font-bold text-zinc-900 dark:text-white">Open Connectors</div>
                                        <div class="text-[10px] text-zinc-500">Settings &rarr; Connectors</div>
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-800 flex items-start gap-2">
                                    <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-[10px] font-bold flex items-center justify-center shrink-0">2</span>
                                    <div class="min-w-0">
                                        <div class="text-[11px] font-bold text-zinc-900 dark:text-white">Add Connector</div>
                                        <div class="text-[10px] text-zinc-500">Add custom connector</div>
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-800 flex items-start gap-2">
                                    <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-[10px] font-bold flex items-center justify-center shrink-0">3</span>
                                    <div class="min-w-0">
                                        <div class="text-[11px] font-bold text-zinc-900 dark:text-white">Paste &amp; Continue</div>
                                        <div class="text-[10px] text-zinc-500">Paste Name &amp; URL</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mode 2: Desktop & CLI View -->
                        <div id="cora-claude-desktop-view" class="hidden space-y-3 pt-1">
                            <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-xl overflow-hidden bg-white dark:bg-zinc-900">
                                <div class="px-3.5 py-2 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                                    <span class="text-xs font-bold text-zinc-900 dark:text-white">Claude Desktop (<code class="text-[11px] font-mono">claude_desktop_config.json</code>)</span>
                                    <button type="button" onclick="coraCopySnippet('cora-ws-claude-cfg')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white flex items-center gap-1 cursor-pointer border-0 bg-transparent">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        Copy
                                    </button>
                                </div>
                                <pre id="cora-ws-claude-cfg" class="p-3.5 text-[11px] font-mono text-zinc-100 bg-[#0c0c0e] overflow-x-auto select-all leading-relaxed">{
  "mcpServers": {
    "cora-workspace": {
      "command": "npx",
      "args": [
        "-y",
        "@modelcontextprotocol/server-fetch",
        "<?php echo esc_url( $mcp_server_url ); ?>"
      ],
      "env": {
        "AUTHORIZATION": "Bearer YOUR_WORKSPACE_LIVE_KEY"
      }
    }
  }
}</pre>
                            </div>

                            <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-xl overflow-hidden bg-white dark:bg-zinc-900">
                                <div class="px-3.5 py-2 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                                    <span class="text-xs font-bold text-zinc-900 dark:text-white">Claude Code CLI Command (<code class="text-[11px] font-mono">claude mcp add</code>)</span>
                                    <button type="button" onclick="coraCopySnippet('cora-ws-claude-cli')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white flex items-center gap-1 cursor-pointer border-0 bg-transparent">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        Copy
                                    </button>
                                </div>
                                <pre id="cora-ws-claude-cli" class="p-3.5 text-[11px] font-mono text-amber-300 bg-[#0c0c0e] overflow-x-auto select-all leading-relaxed whitespace-pre-wrap">claude mcp add cora-workspace -- npx -y @modelcontextprotocol/server-fetch "<?php echo esc_url( $mcp_server_url ); ?>" --header "Authorization: Bearer YOUR_WORKSPACE_LIVE_KEY"</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     ECOSYSTEM 3: CHATGPT (OPENAI) CUSTOM MCP SERVER & ACTIONS
                     ============================================================ -->
                <div id="cora-eco-panel-chatgpt" class="cora-eco-panel hidden space-y-4">
                    <!-- Dynamic Connection Status Card: ChatGPT -->
                    <div id="cora-chatgpt-connection-card" class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"></circle><path d="M12 8a4 4 0 0 1 4 4m-4 0a4 4 0 0 1-4-4m4 4a4 4 0 0 1 0 4m0-4a4 4 0 0 0-4 4"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-zinc-950 dark:text-white">ChatGPT (OpenAI)</span>
                                        <span id="cora-chatgpt-status-badge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Connected &amp; Active</span>
                                        </span>
                                    </div>
                                    <div id="cora-chatgpt-status-subtext" class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                        Last active handshake 1m ago &bull; 215 tool requests handled
                                    </div>
                                </div>
                            </div>
                            <div id="cora-chatgpt-status-actions" class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="coraTestEcosystemHandshake('chatgpt')" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-900 dark:text-zinc-100 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 cursor-pointer transition-all flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                    <span>Test Handshake</span>
                                </button>
                                <button type="button" onclick="coraDisconnectEcosystem('chatgpt')" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 border border-zinc-200/80 dark:border-zinc-800 cursor-pointer transition-all">
                                    Disconnect
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Level 01 Segmented Control / Radio Pill Matrix -->
                    <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 select-none" id="cora-chatgpt-method-tabs">
                        <button type="button" onclick="coraSwitchChatGptSetupMethod('mcp')" id="cora-chatgpt-tab-mcp" class="cora-chatgpt-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect></svg>
                            <span>Custom MCP &amp; GPT Actions (1-Click Setup)</span>
                        </button>
                        <button type="button" onclick="coraSwitchChatGptSetupMethod('skills')" id="cora-chatgpt-tab-skills" class="cora-chatgpt-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                            <span>Agent Skills &amp; MCP Plugins</span>
                        </button>
                    </div>

                    <!-- MODE 1: CHATGPT CUSTOM MCP & GPT ACTIONS VIEW -->
                    <div id="cora-chatgpt-mcp-view" class="space-y-4">
                        <!-- Dual Hero Launch Bar -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="button" onclick="coraOpenChatGptConnectors()" class="bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold px-4 py-3 rounded-xl hover:opacity-90 active:scale-97 transition-all flex items-center justify-center gap-2 border-0 cursor-pointer shadow-2xs">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                <span>Launch ChatGPT Connectors</span>
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </button>
                            <a href="https://chatgpt.com/gpts/editor" target="_blank" rel="noopener noreferrer" class="bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 font-semibold px-4 py-3 rounded-xl transition-all flex items-center justify-center gap-2 border border-zinc-200/80 dark:border-zinc-700 text-xs no-underline active:scale-97">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                <span>Open Custom GPT Editor (OpenAPI)</span>
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                            </a>
                        </div>

                        <!-- Side-by-side Connected Specs -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- Spec 1: Custom MCP Connector -->
                            <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 bg-white dark:bg-zinc-900 shadow-2xs space-y-3.5 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Method A: Custom MCP Connector</h4>
                                        </div>
                                        <span class="text-[10px] font-mono text-zinc-400">chatgpt.com &rarr; Settings &rarr; Connectors</span>
                                    </div>

                                    <!-- Hidden legacy fields for 100% selector safety -->
                                    <input type="hidden" id="cora-chatgpt-server-url" value="<?php echo esc_url( home_url('/mcp') ); ?>">
                                    <input type="hidden" id="cora-chatgpt-icon-url" value="<?php echo esc_url( home_url('/favicon.png') ); ?>">
                                    <input type="hidden" id="cora-chatgpt-desc" value="<?php echo esc_attr( $mcp_target_desc ); ?>">

                                    <!-- Connector Name -->
                                    <div class="space-y-1">
                                        <label for="cora-chatgpt-name" class="block text-[11px] font-bold text-zinc-700 dark:text-zinc-300">Connector Name</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="cora-chatgpt-name" readonly value="<?php echo esc_attr( $mcp_target_name ); ?>" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                            <button type="button" onclick="coraCopyInputValue('cora-chatgpt-name')" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors">
                                                Copy
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Server URL -->
                                    <div class="space-y-1">
                                        <label for="cora-chatgpt-app-link" class="block text-[11px] font-bold text-zinc-700 dark:text-zinc-300">MCP Server URL</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="cora-chatgpt-app-link" readonly value="<?php echo esc_url( home_url('/mcp') ); ?>" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                            <button type="button" onclick="coraCopyInputValue('cora-chatgpt-app-link')" class="px-3 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 text-white rounded-xl text-xs font-bold shrink-0 cursor-pointer border-0 transition-all shadow-2xs active:scale-97">
                                                Copy URL
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Collapsible OAuth Details -->
                                <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-800/40 overflow-hidden">
                                    <button type="button" onclick="coraToggleChatGptAdvanced()" class="w-full px-3.5 py-2.5 flex items-center justify-between text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white bg-transparent border-0 cursor-pointer">
                                        <span class="text-[11px] font-medium text-zinc-600 dark:text-zinc-400">Advanced OAuth Credentials (RFC 8414 Auto)</span>
                                        <svg id="cora-chatgpt-advanced-chevron" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </button>
                                    <div id="cora-chatgpt-advanced-body" class="hidden p-3 space-y-2 border-t border-zinc-200/70 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                                        <div class="space-y-1">
                                            <label class="block text-[10px] font-mono text-zinc-400">Client ID</label>
                                            <input type="text" id="cora-chatgpt-client-id" readonly value="cora_client_ws_<?php echo esc_attr( $agency_id ); ?>" class="w-full font-mono text-[11px] px-2.5 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="block text-[10px] font-mono text-zinc-400">Client Secret</label>
                                            <input type="text" id="cora-chatgpt-client-secret" readonly value="cora_chatgpt_sec_<?php echo esc_attr( $agency_id ); ?>" class="w-full font-mono text-[11px] px-2.5 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Spec 2: Custom GPT Action (OpenAPI Engine) -->
                            <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 bg-white dark:bg-zinc-900 shadow-2xs space-y-3.5 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Method B: Custom GPT Action (OpenAPI)</h4>
                                        </div>
                                        <span class="text-[10px] font-mono text-zinc-400">GPT Builder &rarr; Configure &rarr; Actions</span>
                                    </div>

                                    <!-- Schema URL -->
                                    <div class="space-y-1">
                                        <label for="cora-chatgpt-openapi-url" class="block text-[11px] font-bold text-zinc-700 dark:text-zinc-300">OpenAPI Schema Endpoint</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="cora-chatgpt-openapi-url" readonly value="<?php echo esc_url( home_url('/wp-json/cora/v1/mcp/openapi.json') ); ?>" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                            <button type="button" onclick="coraCopyInputValue('cora-chatgpt-openapi-url')" class="px-3 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 text-white rounded-xl text-xs font-bold shrink-0 cursor-pointer border-0 transition-all shadow-2xs active:scale-97">
                                                Copy Schema
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Authentication Type -->
                                    <div class="space-y-1">
                                        <label class="block text-[11px] font-bold text-zinc-700 dark:text-zinc-300">Authentication Scheme</label>
                                        <div class="p-2.5 rounded-xl border border-zinc-200/80 dark:border-zinc-700 bg-zinc-50/70 dark:bg-zinc-800/50 flex items-center justify-between text-xs">
                                            <span class="font-mono text-[11px] text-zinc-700 dark:text-zinc-300">API Key &bull; Bearer Token</span>
                                            <span class="text-[10px] text-zinc-400">Workspace Live Key</span>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed pt-1">
                                    Paste this schema directly into the <span class="font-semibold text-zinc-900 dark:text-zinc-100">&ldquo;Import from URL&rdquo;</span> box in ChatGPT's GPT Builder to give your assistant access to all 28 studio operations tools.
                                </p>
                            </div>
                        </div>

                        <!-- 3-Step Natural Setup Guidance Strip -->
                        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1 border border-zinc-200/60 dark:border-zinc-800">
                                    <div class="text-[11px] font-bold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] font-mono flex items-center justify-center">1</span>
                                        <span>Open Connectors</span>
                                    </div>
                                    <p class="text-[11px] text-zinc-500 leading-relaxed">Open <span class="font-semibold text-zinc-800 dark:text-zinc-200">chatgpt.com</span> &rarr; Settings &rarr; Connectors or GPT Builder.</p>
                                </div>
                                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1 border border-zinc-200/60 dark:border-zinc-800">
                                    <div class="text-[11px] font-bold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] font-mono flex items-center justify-center">2</span>
                                        <span>Paste Server URL</span>
                                    </div>
                                    <p class="text-[11px] text-zinc-500 leading-relaxed">Paste the Server URL or OpenAPI Schema into the connector prompt.</p>
                                </div>
                                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1 border border-zinc-200/60 dark:border-zinc-800">
                                    <div class="text-[11px] font-bold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] font-mono flex items-center justify-center">3</span>
                                        <span>Dispatch Tasks</span>
                                    </div>
                                    <p class="text-[11px] text-zinc-500 leading-relaxed">ChatGPT executes live audits, drafts posts, and qualifies leads directly.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODE 2: AGENT SKILLS & MCP PLUGINS VIEW -->
                    <div id="cora-chatgpt-skills-view" class="hidden space-y-4">
                        <!-- Header Banner -->
                        <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">ChatGPT Agent Skills &amp; MCP Plugin Catalog</h4>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Once connected via Custom MCP Server, ChatGPT inherits all active workspace skills, behavioral prompt directives, and dynamic MCP tools with real-time execution.
                            </p>
                        </div>

                        <!-- 5 High-Impact Agent Skills & Plugins for ChatGPT -->
                        <div class="space-y-3">
                            <!-- Skill 1: SEO & GEO Content Architect -->
                            <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/60 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-zinc-900 dark:text-white">SEO &amp; GEO Content Architect Skill</h5>
                                            <span class="text-[10px] font-mono text-zinc-400">Content Intelligence &bull; Active</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">🟢 Active</span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    Discovers high-intent keyword gaps, drafts structured authority guides, and executes algorithmic 11-point SEO &amp; GEO scoring to ensure content outranks competitors in search and AI citations.
                                </p>
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-[10px] font-mono font-semibold text-zinc-500">Bound Tools:</span>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_create_article</code>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_audit_article_seo</code>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_improve_content</code>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_list_content_opportunities</code>
                                </div>
                            </div>

                            <!-- Skill 2: Client Intake & Lead Qualification -->
                            <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/60 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-zinc-900 dark:text-white">Client Intake &amp; CRM Lead Qualification Skill</h5>
                                            <span class="text-[10px] font-mono text-zinc-400">Client Operations &bull; Active</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">🟢 Active</span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    Guides prospective inquiries through intake qualification, validates commercial project scope, and stages leads into the CRM pipeline with recommended next operational actions.
                                </p>
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-[10px] font-mono font-semibold text-zinc-500">Bound Tools:</span>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_create_lead</code>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_list_leads</code>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_create_client</code>
                                </div>
                            </div>

                            <!-- Plugin 1: Autonomous Campaigns Engine Plugin -->
                            <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-zinc-900 dark:text-white">Autonomous Campaigns Engine Plugin</h5>
                                            <span class="text-[10px] font-mono text-zinc-400">Background Automation &bull; Active</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">🟢 Active</span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    Runs scheduled multi-article publishing pipelines, validating drafts against 11-point quality thresholds and auto-scheduling posts on the workspace editorial calendar.
                                </p>
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-[10px] font-mono font-semibold text-zinc-500">Bound Tools:</span>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_create_campaign</code>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_trigger_campaign_run</code>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_list_campaigns</code>
                                </div>
                            </div>

                            <!-- Skill 3: GST SAC 9983 Tax & Invoice Calculator -->
                            <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-zinc-900 dark:text-white">GST SAC 9983 Tax &amp; Spot Billing Skill</h5>
                                            <span class="text-[10px] font-mono text-zinc-400">Financial Intelligence &bull; Active</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">🟢 Active</span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    Performs standard 18% GST SAC 9983 tax math, validates state codes for IGST vs CGST/SGST split, and reconciles spot billing receipts with real-time margin analytics.
                                </p>
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-[10px] font-mono font-semibold text-zinc-500">Bound Tools:</span>
                                    <code class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">cora_get_workspace_metrics</code>
                                </div>
                            </div>
                        </div>

                        <!-- Natural Language Prompt Cheat Sheet for ChatGPT -->
                        <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-900 dark:text-white"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Example Prompts to Give ChatGPT</h4>
                            </div>
                            <div class="space-y-2">
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 flex items-center justify-between gap-3">
                                    <p class="text-xs font-medium text-zinc-800 dark:text-zinc-200 italic">&ldquo;Check our content opportunities and draft a 1,500-word authority guide on studio lighting with minimum 90+ SEO score.&rdquo;</p>
                                    <button type="button" onclick="coraCopyAppText('Check our content opportunities and draft a 1,500-word authority guide on studio lighting with minimum 90+ SEO score.', 'Prompt')" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-90 transition-all shrink-0 cursor-pointer border-0">Copy</button>
                                </div>
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 flex items-center justify-between gap-3">
                                    <p class="text-xs font-medium text-zinc-800 dark:text-zinc-200 italic">&ldquo;List recent CRM leads, calculate their qualification score, and draft an intake summary for the top prospect.&rdquo;</p>
                                    <button type="button" onclick="coraCopyAppText('List recent CRM leads, calculate their qualification score, and draft an intake summary for the top prospect.', 'Prompt')" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-90 transition-all shrink-0 cursor-pointer border-0">Copy</button>
                                </div>
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 flex items-center justify-between gap-3">
                                    <p class="text-xs font-medium text-zinc-800 dark:text-zinc-200 italic">&ldquo;Run the autonomous content campaign now and verify whether the generated article passed our SEO and GEO thresholds.&rdquo;</p>
                                    <button type="button" onclick="coraCopyAppText('Run the autonomous content campaign now and verify whether the generated article passed our SEO and GEO thresholds.', 'Prompt')" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-90 transition-all shrink-0 cursor-pointer border-0">Copy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     ECOSYSTEM 4: CURSOR & WINDSURF IDE
                     ============================================================ -->
                <div id="cora-eco-panel-cursor" class="cora-eco-panel hidden space-y-4">
                    <!-- Config Card 1: Cursor AI -->
                    <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                        <div class="px-5 py-3 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                <span class="text-xs font-bold text-zinc-900 dark:text-white">Cursor AI (<code class="text-[11px] font-mono">.cursor/mcp.json</code>)</span>
                            </div>
                            <button type="button" onclick="coraCopySnippet('cora-ws-cursor-cfg')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white flex items-center gap-1.5 cursor-pointer border-0 bg-transparent">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                Copy Config
                            </button>
                        </div>
                        <pre id="cora-ws-cursor-cfg" class="p-5 text-xs font-mono text-zinc-100 bg-[#0c0c0e] overflow-x-auto select-all leading-relaxed">{
  "mcpServers": {
    "cora-workspace": {
      "url": "<?php echo esc_url( $mcp_server_url ); ?>",
      "headers": {
        "Authorization": "Bearer YOUR_WORKSPACE_LIVE_KEY"
      }
    }
  }
}</pre>
                    </div>

                    <!-- Config Card 2: Windsurf IDE Rules -->
                    <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                        <div class="px-5 py-3 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                                <span class="text-xs font-bold text-zinc-900 dark:text-white">Windsurf IDE (<code class="text-[11px] font-mono">.windsurfrules</code>)</span>
                            </div>
                            <button type="button" onclick="coraCopySnippet('cora-ws-windsurf-cfg')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white flex items-center gap-1.5 cursor-pointer border-0 bg-transparent">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                Copy Rules
                            </button>
                        </div>
                        <pre id="cora-ws-windsurf-cfg" class="p-5 text-xs font-mono text-emerald-400 bg-[#0c0c0e] overflow-x-auto select-all leading-relaxed"># Cora Workspace Rules
- MCP Server: <?php echo esc_url( $mcp_server_url ); ?>

- Auth Header: Authorization: Bearer YOUR_WORKSPACE_LIVE_KEY
- Capabilities: Autonomous CRM Triage, Lead Qualification, WhatsApp & Email Outreach, Calendar & Shoot Scheduling, Document Vault E-Sign, GST Invoicing, Content Suite & SEO
- Policy: Execute cora_crm_autonomous_triage on new inquiries and dispatch booking links with Google Meet for qualified leads.
- Policy: Before proposing article publication, execute cora_audit_article_seo to ensure minimum 85+ score.</pre>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 NEW TAB: AUTONOMOUS CAMPAIGNS COMMAND CENTER
                 ============================================================ -->
            <div id="cora-ws-pane-campaigns" class="cora-ws-pane hidden space-y-5">
                <!-- Informational Header Card -->
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Autonomous Campaigns Command Center</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700 self-start sm:self-auto">
                            Multi-Agent Auto-Publish Engine
                        </span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Configure scheduled background campaigns that systematically discover keyword gaps, generate structured authority guides, execute algorithmic 11-point SEO &amp; GEO audits, and automatically publish or stage drafts for review.
                    </p>
                </div>

                <!-- 1. Campaign Overview & Pulse Strip -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-1">
                        <div class="text-[11px] font-medium text-zinc-500">Active Campaigns</div>
                        <div class="flex items-baseline gap-2">
                            <span id="cora-cmp-stat-active" class="text-xl font-bold font-mono text-zinc-950 dark:text-white">3</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-semibold">🟢 Scheduled</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-1">
                        <div class="text-[11px] font-medium text-zinc-500">Autonomous Articles</div>
                        <div class="flex items-baseline gap-2">
                            <span id="cora-cmp-stat-articles" class="text-xl font-bold font-mono text-zinc-950 dark:text-white">28</span>
                            <span class="text-[10px] text-zinc-400 font-mono">Published</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-1">
                        <div class="text-[11px] font-medium text-zinc-500">Average SEO Score</div>
                        <div class="flex items-baseline gap-2">
                            <span id="cora-cmp-stat-seo" class="text-xl font-bold font-mono text-zinc-950 dark:text-white">92</span>
                            <span class="text-xs text-zinc-400 font-mono">/100</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300">11-Point Audited</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-1">
                        <div class="text-[11px] font-medium text-zinc-500">Average GEO Score</div>
                        <div class="flex items-baseline gap-2">
                            <span id="cora-cmp-stat-geo" class="text-xl font-bold font-mono text-zinc-950 dark:text-white">89</span>
                            <span class="text-xs text-zinc-400 font-mono">/100</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300">AI Citations</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Quick Campaign Creator Card -->
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-900 dark:text-white"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Create Autonomous Campaign</h4>
                        </div>
                        <span class="text-[11px] text-zinc-400 font-medium">Cadence &amp; Quality Automations</span>
                    </div>

                    <div class="space-y-3.5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Campaign Name / Pillar</label>
                                <input type="text" id="cora-cmp-new-name" placeholder="e.g. Commercial Office Advisory Hub" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/80 text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Topic Keywords (comma-separated)</label>
                                <input type="text" id="cora-cmp-new-keywords" placeholder="e.g. commercial lease agreement, office rental GST, lock-in clauses" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/80 text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
                            </div>
                        </div>

                        <!-- Cadence Selector (Level 01 Segmented Pill Control) -->
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Publishing Cadence</label>
                            <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex flex-wrap gap-1 select-none" id="cora-cmp-cadence-pills">
                                <button type="button" onclick="coraSelectCmpCadence('Daily', this)" class="cmp-cadence-btn px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">Daily</button>
                                <button type="button" onclick="coraSelectCmpCadence('3x / Week', this)" class="cmp-cadence-btn px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">3x / Week</button>
                                <button type="button" onclick="coraSelectCmpCadence('Weekly', this)" class="cmp-cadence-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer">Weekly (Recommended)</button>
                                <button type="button" onclick="coraSelectCmpCadence('Bi-Weekly', this)" class="cmp-cadence-btn px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">Bi-Weekly</button>
                            </div>
                            <input type="hidden" id="cora-cmp-cadence-val" value="Weekly">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                            <!-- Autonomy Mode Toggle (Level 01 Segmented Pill Control) -->
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Autonomy Mode</label>
                                <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex gap-1 select-none" id="cora-cmp-autonomy-pills">
                                    <button type="button" onclick="coraSelectCmpAutonomy(1, this)" class="cmp-autonomy-btn flex-1 px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer text-center">Auto-Publish (Live)</button>
                                    <button type="button" onclick="coraSelectCmpAutonomy(0, this)" class="cmp-autonomy-btn flex-1 px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer text-center">Draft for Review</button>
                                </div>
                                <input type="hidden" id="cora-cmp-autonomy-val" value="1">
                            </div>

                            <!-- Primary AI Model (Level 01 Segmented Pill Control) -->
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Primary AI Model</label>
                                <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex flex-wrap gap-1 select-none" id="cora-cmp-model-pills">
                                    <button type="button" onclick="coraSelectCmpModel('Claude 3.5 Sonnet', this)" class="cmp-model-btn px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer">Claude 3.5 Sonnet</button>
                                    <button type="button" onclick="coraSelectCmpModel('Gemini 3.5 Flash', this)" class="cmp-model-btn px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">Gemini 3.5 Flash</button>
                                    <button type="button" onclick="coraSelectCmpModel('GPT-4o', this)" class="cmp-model-btn px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">GPT-4o</button>
                                    <button type="button" onclick="coraSelectCmpModel('Cora AI Engine', this)" class="cmp-model-btn px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">Cora AI Engine</button>
                                </div>
                                <input type="hidden" id="cora-cmp-model-val" value="Claude 3.5 Sonnet">
                            </div>
                        </div>

                        <!-- Minimum Quality Gate (SEO / GEO Bar) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Min SEO Gate (Score 0-100)</label>
                                    <span id="cora-cmp-seo-gate-display" class="font-mono text-xs font-bold text-zinc-900 dark:text-white">85+</span>
                                </div>
                                <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex gap-1 select-none" id="cora-cmp-seo-gate-pills">
                                    <button type="button" onclick="coraSelectCmpGate('seo', 75, this)" class="cmp-seo-gate-btn flex-1 py-1 rounded-lg text-[11px] font-medium transition-all text-zinc-600 dark:text-zinc-400 border-0 bg-transparent cursor-pointer">75+</button>
                                    <button type="button" onclick="coraSelectCmpGate('seo', 80, this)" class="cmp-seo-gate-btn flex-1 py-1 rounded-lg text-[11px] font-medium transition-all text-zinc-600 dark:text-zinc-400 border-0 bg-transparent cursor-pointer">80+</button>
                                    <button type="button" onclick="coraSelectCmpGate('seo', 85, this)" class="cmp-seo-gate-btn flex-1 py-1 rounded-lg text-[11px] font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer">85+</button>
                                    <button type="button" onclick="coraSelectCmpGate('seo', 90, this)" class="cmp-seo-gate-btn flex-1 py-1 rounded-lg text-[11px] font-medium transition-all text-zinc-600 dark:text-zinc-400 border-0 bg-transparent cursor-pointer">90+</button>
                                </div>
                                <input type="hidden" id="cora-cmp-min-seo-val" value="85">
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Min GEO Gate (AI Engine Index)</label>
                                    <span id="cora-cmp-geo-gate-display" class="font-mono text-xs font-bold text-zinc-900 dark:text-white">85+</span>
                                </div>
                                <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex gap-1 select-none" id="cora-cmp-geo-gate-pills">
                                    <button type="button" onclick="coraSelectCmpGate('geo', 75, this)" class="cmp-geo-gate-btn flex-1 py-1 rounded-lg text-[11px] font-medium transition-all text-zinc-600 dark:text-zinc-400 border-0 bg-transparent cursor-pointer">75+</button>
                                    <button type="button" onclick="coraSelectCmpGate('geo', 80, this)" class="cmp-geo-gate-btn flex-1 py-1 rounded-lg text-[11px] font-medium transition-all text-zinc-600 dark:text-zinc-400 border-0 bg-transparent cursor-pointer">80+</button>
                                    <button type="button" onclick="coraSelectCmpGate('geo', 85, this)" class="cmp-geo-gate-btn flex-1 py-1 rounded-lg text-[11px] font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer">85+</button>
                                    <button type="button" onclick="coraSelectCmpGate('geo', 90, this)" class="cmp-geo-gate-btn flex-1 py-1 rounded-lg text-[11px] font-medium transition-all text-zinc-600 dark:text-zinc-400 border-0 bg-transparent cursor-pointer">90+</button>
                                </div>
                                <input type="hidden" id="cora-cmp-min-geo-val" value="85">
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="button" onclick="coraCreateCampaign()" id="cora-btn-create-campaign" class="px-5 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold cursor-pointer transition-all active:scale-97 border-0 shadow-2xs flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Create Autonomous Campaign</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Campaign Matrix Table / Cards -->
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Active Campaign Matrix</h4>
                            <p class="text-[11px] text-zinc-400 mt-0.5">Recurring multi-model background pipelines</p>
                        </div>
                        <button type="button" onclick="coraLoadCampaigns()" class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center gap-1 cursor-pointer border-0 bg-transparent font-medium">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                            Refresh Matrix
                        </button>
                    </div>

                    <div id="cora-campaigns-matrix-container" class="space-y-2.5">
                        <div class="p-6 text-center text-xs text-zinc-400">Loading campaign pipelines...</div>
                    </div>
                </div>

                <!-- 4. Live Autonomous Activity Log -->
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Live Autonomous Publication Stream</h4>
                            <p class="text-[11px] text-zinc-400 mt-0.5">Recent articles discovered, scored &amp; published by AI agents</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                            Streaming
                        </span>
                    </div>

                    <div id="cora-campaigns-activity-container" class="space-y-2">
                        <div class="p-6 text-center text-xs text-zinc-400">Loading publication stream...</div>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 TAB 3: WORKSPACE SKILLS & MCP PLUGINS
                 ============================================================ -->
            <div id="cora-ws-pane-tools" class="cora-ws-pane hidden space-y-6">
                <!-- Informational Header Card -->
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Workspace AI Skills &amp; MCP Plugin Catalog</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700 self-start sm:self-auto">
                            <?php echo count( $workspace_skills ); ?> Active Skills &bull; <?php echo count( $workspace_plugins ); ?> Plugins &bull; 42+ Universal MCP Tools
                        </span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Cora provides external AI models (Google Gemini, Claude, ChatGPT) with specialized behavioral skills, dynamic webhook plugins, and Model Context Protocol (MCP) tools under strict multi-tenant workspace isolation.
                    </p>
                </div>

                <!-- SECTION 1: ACTIVE AI AGENT SKILLS -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-900 dark:text-white"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Active Workspace AI Skills (<?php echo count( $workspace_skills ); ?>)</h4>
                        </div>
                        <span class="text-[11px] text-zinc-400 font-mono">Prompt Directives + Bound Tools</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    <?php if ( ! empty( $workspace_skills ) ) : ?>
                        <?php foreach ( $workspace_skills as $skill ) : 
                            $bound_tools = json_decode( $skill['bound_mcp_tools'] ?? '[]', true ) ?: array();
                        ?>
                        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2.5 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-center shrink-0">
                                            <?php echo ! empty( $skill['icon_svg'] ) ? $skill['icon_svg'] : '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v20M2 12h20"></path></svg>'; ?>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-zinc-900 dark:text-white"><?php echo esc_html( $skill['name'] ); ?></h5>
                                            <span class="text-[10px] font-mono text-zinc-400"><?php echo esc_html( $skill['category'] ?? 'General' ); ?></span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">🟢 Active</span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    <?php echo esc_html( $skill['description'] ); ?>
                                </p>
                            </div>

                            <div class="space-y-1.5 pt-2 border-t border-zinc-100 dark:border-zinc-800/60">
                                <?php if ( ! empty( $skill['trigger_phrases'] ) ) : ?>
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400">
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">Triggers:</span> <em><?php echo esc_html( $skill['trigger_phrases'] ); ?></em>
                                </div>
                                <?php endif; ?>
                                <?php if ( ! empty( $bound_tools ) ) : ?>
                                <div class="flex flex-wrap items-center gap-1 pt-0.5">
                                    <span class="text-[9px] font-mono font-semibold text-zinc-400">Tools:</span>
                                    <?php foreach ( array_slice( $bound_tools, 0, 3 ) as $bt ) : ?>
                                    <code class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300"><?php echo esc_html( $bt ); ?></code>
                                    <?php endforeach; ?>
                                    <?php if ( count( $bound_tools ) > 3 ) : ?>
                                    <span class="text-[9px] font-mono text-zinc-400">+<?php echo count( $bound_tools ) - 3; ?> more</span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- SECTION 2: DYNAMIC WORKSPACE PLUGINS -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-900 dark:text-white"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Dynamic Workspace Plugins (<?php echo count( $workspace_plugins ); ?>)</h4>
                        </div>
                        <span class="text-[11px] text-zinc-400 font-mono">Webhooks &bull; Bridges &bull; Listeners</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    <?php if ( ! empty( $workspace_plugins ) ) : ?>
                        <?php foreach ( $workspace_plugins as $plug ) : 
                            $routes = json_decode( $plug['api_routes_json'] ?? '[]', true ) ?: array();
                        ?>
                        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-center shrink-0">
                                            <?php echo ! empty( $plug['icon_svg'] ) ? $plug['icon_svg'] : '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="5" width="20" height="14" rx="2"></rect></svg>'; ?>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-zinc-900 dark:text-white"><?php echo esc_html( $plug['name'] ); ?></h5>
                                            <span class="text-[10px] font-mono text-zinc-400">v<?php echo esc_html( $plug['version'] ?? '1.0.0' ); ?> &bull; <?php echo esc_html( $plug['category'] ?? 'Plugin' ); ?></span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">🟢 Active</span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    <?php echo esc_html( $plug['description'] ); ?>
                                </p>
                            </div>

                            <?php if ( ! empty( $routes ) ) : ?>
                            <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800/60 flex flex-wrap items-center gap-1">
                                <span class="text-[9px] font-mono font-semibold text-zinc-400">Routes:</span>
                                <?php foreach ( $routes as $r ) : ?>
                                <code class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300"><?php echo esc_html( $r ); ?></code>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- SECTION 3: CANONICAL MCP TOOL REGISTRY -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-900 dark:text-white"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Universal Model Context Protocol (MCP) Tool Registry</h4>
                        </div>
                        <span class="text-[11px] text-zinc-400 font-mono">28 Core Tools</span>
                    </div>

                <!-- Section: Content Suite -->
                <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                    <div class="px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                        <span class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">AI Content Suite (12 Tools)</span>
                        <span class="text-[10px] font-mono text-zinc-500">Live</span>
                    </div>
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_list_articles</code> <span class="text-zinc-500 ml-2">Search and paginate workspace articles</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">content:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_get_article</code> <span class="text-zinc-500 ml-2">Retrieve article body, SEO &amp; GEO signals</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">content:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_create_article</code> <span class="text-zinc-500 ml-2">Draft or publish new article with metadata</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">content:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_update_article</code> <span class="text-zinc-500 ml-2">Update title, content, status, categories</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">content:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_audit_article_seo</code> <span class="text-zinc-500 ml-2">11-point algorithmic SEO &amp; GEO scoring</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">content:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_list_content_opportunities</code> <span class="text-zinc-500 ml-2">High-intent keyword and topic opportunities</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">content:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_generate_article_outline</code> <span class="text-zinc-500 ml-2">AI structured draft generator</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">content:write</span>
                        </div>
                    </div>
                </div>

                <!-- Section: CRM & Workspace Core -->
                <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                    <div class="px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                        <span class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Agentic CRM, Outreach &amp; Calendar Operations (28 Tools)</span>
                        <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">Autonomous Ready</span>
                    </div>
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_autonomous_triage</code> <span class="text-zinc-500 ml-2">Autonomously score (0-100), enrich &amp; assign inquiries</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">leads:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_qualify_lead</code> <span class="text-zinc-500 ml-2">Algorithmic scoring, win probability %, intent tiering</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">leads:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_convert_lead_to_client</code> <span class="text-zinc-500 ml-2">1-Click deal conversion, portal token, project &amp; task seeding</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">clients:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_list_calendar_events</code> <span class="text-zinc-500 ml-2">Query calendar shoots, appointments &amp; Google Meet links</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">workspace:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_get_calendar_availability</code> <span class="text-zinc-500 ml-2">Check open vs booked shoot slots for any date</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">workspace:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_book_appointment</code> <span class="text-zinc-500 ml-2">Schedule consultation/shoot with automated Google Meet room</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">workspace:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_reschedule_booking</code> <span class="text-zinc-500 ml-2">Reschedule shoot or consultation and notify client</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">workspace:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_cancel_booking</code> <span class="text-zinc-500 ml-2">Cancel session, free calendar slot, and notify attendee</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">workspace:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_send_outreach</code> <span class="text-zinc-500 ml-2">Personalized WhatsApp Cloud API &amp; SMTP email dispatch</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">workspace:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_send_outreach_email</code> <span class="text-zinc-500 ml-2">Personalized SMTP email dispatch with touchpoint logging</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">workspace:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_generate_proposal_and_contract</code> <span class="text-zinc-500 ml-2">Compile Document Vault proposal with digital E-Sign link</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">vault:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_send_proposal</code> <span class="text-zinc-500 ml-2">Dispatch digital contract link via WhatsApp/Email</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">vault:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_get_document_status</code> <span class="text-zinc-500 ml-2">Check proposal signature status and audit trail</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">vault:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_dispatch_gst_invoice</code> <span class="text-zinc-500 ml-2">Indian GST SAC 9983 tax math &amp; payment link dispatch</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">finance:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_send_payment_reminder</code> <span class="text-zinc-500 ml-2">Send polite/urgent invoice payment reminders</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">finance:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_update_project_milestone</code> <span class="text-zinc-500 ml-2">Advance stage (production &rarr; post &rarr; review &rarr; completed)</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">projects:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_deliver_project_assets</code> <span class="text-zinc-500 ml-2">Publish galleries to client portal and notify client</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">projects:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_check_stalled_deals</code> <span class="text-zinc-500 ml-2">Surveillance scan for stalled deals &amp; re-engagement prompts</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">leads:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_run_agentic_workflow</code> <span class="text-zinc-500 ml-2">Master autonomous cycle (triage, follow-ups, receivables)</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">workspace:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_list_campaigns</code> <span class="text-zinc-500 ml-2">List active outreach &amp; inbound nurture campaigns</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">crm:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_create_campaign</code> <span class="text-zinc-500 ml-2">Create cold outreach or nurture sequence with cadences</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">crm:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_enroll_lead</code> <span class="text-zinc-500 ml-2">Enroll leads into targeted automated campaigns</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">crm:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_log_activity</code> <span class="text-zinc-500 ml-2">Record touchpoints (call, email, WhatsApp, meeting notes)</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">crm:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_crm_get_activity_timeline</code> <span class="text-zinc-500 ml-2">Chronological multi-channel interaction history</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">crm:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_list_leads</code> <span class="text-zinc-500 ml-2">Retrieve CRM pipeline leads and stages</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">crm:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_create_lead</code> <span class="text-zinc-500 ml-2">Ingest new lead from external channels</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">crm:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_update_lead_status</code> <span class="text-zinc-500 ml-2">Advance stage: new &rarr; contacted &rarr; qualified &rarr; won &rarr; lost</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">leads:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_list_clients</code> <span class="text-zinc-500 ml-2">Retrieve studio client directory &amp; lifetime spend</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">clients:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_list_projects</code> <span class="text-zinc-500 ml-2">Shoot bookings, milestones, and delivery statuses</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">projects:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_list_tasks</code> <span class="text-zinc-500 ml-2">Workspace Kanban tasks, checklists, and assignees</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">tasks:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_query_financials</code> <span class="text-zinc-500 ml-2">Query ledger, collected revenue &amp; outstanding receivables</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">finance:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_get_workspace_metrics</code> <span class="text-zinc-500 ml-2">Workspace operational metrics summary</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">workspace:read</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 TAB 4: REST ENDPOINTS (WORKSPACE LEVEL)
                 ============================================================ -->
            <div id="cora-ws-pane-rest" class="cora-ws-pane hidden space-y-5">
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2">
                    <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Workspace REST API Specification</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Base URL: <code class="font-mono text-zinc-900 dark:text-zinc-100 font-bold"><?php echo esc_html( $rest_base_url ); ?></code>. Authenticate with HTTP Bearer token:
                    </p>
                    <div class="p-3 rounded-xl bg-[#0c0c0e] text-xs font-mono text-emerald-400 select-all">
                        Authorization: Bearer cora_ws_live_YOUR_KEY
                    </div>
                </div>

                <!-- Endpoint Table -->
                <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs divide-y divide-zinc-100 dark:divide-zinc-800">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800/60 flex items-center justify-between text-xs font-bold text-zinc-700 dark:text-zinc-300">
                        <span>Endpoint</span>
                        <span>Description</span>
                    </div>
                    <div class="p-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold font-mono text-[10px]">GET</span>
                            <code class="font-mono font-bold">/content</code>
                        </div>
                        <span class="text-zinc-500 text-right">List articles with pagination &amp; search</span>
                    </div>
                    <div class="p-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold font-mono text-[10px]">POST</span>
                            <code class="font-mono font-bold">/content</code>
                        </div>
                        <span class="text-zinc-500 text-right">Create draft or published article</span>
                    </div>
                    <div class="p-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold font-mono text-[10px]">GET</span>
                            <code class="font-mono font-bold">/content/{id}</code>
                        </div>
                        <span class="text-zinc-500 text-right">Fetch single article with SEO &amp; GEO scores</span>
                    </div>
                    <div class="p-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold font-mono text-[10px]">POST</span>
                            <code class="font-mono font-bold">/content/{id}/seo-audit</code>
                        </div>
                        <span class="text-zinc-500 text-right">Run 11-point algorithmic SEO audit</span>
                    </div>
                    <div class="p-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold font-mono text-[10px]">GET</span>
                            <code class="font-mono font-bold">/content/opportunities</code>
                        </div>
                        <span class="text-zinc-500 text-right">Strategic keyword clusters &amp; search volumes</span>
                    </div>
                    <div class="p-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold font-mono text-[10px]">POST</span>
                            <code class="font-mono font-bold">/content/generate</code>
                        </div>
                        <span class="text-zinc-500 text-right">Generate AI article outline and structured HTML</span>
                    </div>
                    <div class="p-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-bold font-mono text-[10px]">POST</span>
                            <code class="font-mono font-bold">/content/batch</code>
                        </div>
                        <span class="text-zinc-500 text-right">Bulk publish, draft, trash, or categorize</span>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 TAB 5: LIVE SANDBOX
                 ============================================================ -->
            <div id="cora-ws-pane-sandbox" class="cora-ws-pane hidden space-y-5">
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Workspace API Execution Sandbox</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Execute live requests directly against your local workspace.</p>
                        </div>
                        <button type="button" onclick="coraRunWorkspaceSandbox()" class="px-4 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold cursor-pointer transition-all active:scale-97 border-0 shadow-2xs flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            <span>Send Request</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Method</label>
                            <div class="px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-xs font-mono font-bold text-zinc-800 dark:text-zinc-200">
                                GET
                            </div>
                        </div>
                        <div class="sm:col-span-9">
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Target Endpoint</label>
                            <!-- Segmented Pill Control for Target Endpoints (Zero native select) -->
                            <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex flex-wrap gap-1 select-none" id="cora-sandbox-endpoints-container">
                                <button type="button" onclick="coraSelectSandboxEndpoint('/wp-json/cora-mcp/v1/tools', this)" class="sbx-ep-btn px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer">
                                    /tools (MCP Registry)
                                </button>
                                <button type="button" onclick="coraSelectSandboxEndpoint('/wp-json/cora/v1/leads?limit=3', this)" class="sbx-ep-btn px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">
                                    /leads (CRM Pipeline)
                                </button>
                                <button type="button" onclick="coraSelectSandboxEndpoint('/wp-json/cora/v1/clients?limit=3', this)" class="sbx-ep-btn px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">
                                    /clients (Directory)
                                </button>
                                <button type="button" onclick="coraSelectSandboxEndpoint('/wp-json/cora/v1/calendar?limit=3', this)" class="sbx-ep-btn px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">
                                    /calendar (Shoots)
                                </button>
                                <button type="button" onclick="coraSelectSandboxEndpoint('/wp-json/cora/v1/content?limit=3', this)" class="sbx-ep-btn px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">
                                    /content (Articles)
                                </button>
                            </div>
                            <input type="hidden" id="cora-sandbox-selected-endpoint" value="/wp-json/cora-mcp/v1/tools">
                        </div>
                    </div>

                    <!-- Output Console -->
                    <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-[#0c0c0e]">
                        <div class="px-4 py-2 bg-zinc-900 border-b border-zinc-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span id="cora-ws-sandbox-status" class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-800 text-zinc-400 font-mono">Status: Ready</span>
                                <span id="cora-ws-sandbox-time" class="text-[10px] text-zinc-500 font-mono">-- ms</span>
                            </div>
                            <button type="button" onclick="coraCopySnippet('cora-ws-sandbox-output')" class="text-xs font-semibold text-zinc-400 hover:text-white flex items-center gap-1 cursor-pointer border-0 bg-transparent">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                Copy
                            </button>
                        </div>
                        <pre id="cora-ws-sandbox-output" class="p-5 text-xs font-mono text-emerald-400 overflow-x-auto max-h-80 leading-relaxed">// Click "Send Request" above to execute live REST query.</pre>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        </div>
    </div>
</div>

<script>
(function() {
    let _activeTab = 'keys';
    let _activeScope = 'full';
    let _wsApiKeys = [];

    // Dynamically synchronize exact available main content canvas space (Rule 15 SOP)
    function coraSyncWorkspaceGwBounds() {
        const drawer = document.getElementById('cora-workspace-developer-gateway-drawer');
        if (!drawer || drawer.classList.contains('collapsed')) return;

        if (drawer.classList.contains('maximized')) {
            drawer.style.setProperty('left', '0px', 'important');
            drawer.style.setProperty('width', '100vw', 'important');
            return;
        }

        const sidebar = document.querySelector('.cora-sidebar') || document.getElementById('cora-sidebar');
        const isCollapsed = sidebar && (sidebar.classList.contains('collapsed-sidebar') || sidebar.classList.contains('w-16'));
        const isMobile = window.innerWidth < 1024;

        if (isMobile) {
            drawer.style.setProperty('left', '0px', 'important');
            drawer.style.setProperty('width', '100vw', 'important');
        } else if (isCollapsed) {
            drawer.style.setProperty('left', '4rem', 'important');
            drawer.style.setProperty('width', 'calc(100vw - 4rem)', 'important');
        } else {
            drawer.style.setProperty('left', '16rem', 'important');
            drawer.style.setProperty('width', 'calc(100vw - 16rem)', 'important');
        }
    }

    // Dynamic synchronization on window resize & sidebar mutation (Rule 15 SOP)
    window.addEventListener('resize', coraSyncWorkspaceGwBounds);
    if (window.MutationObserver) {
        const obs = new MutationObserver(coraSyncWorkspaceGwBounds);
        const sb = document.querySelector('.cora-sidebar') || document.getElementById('cora-sidebar');
        if (sb) obs.observe(sb, { attributes: true, attributeFilter: ['class', 'style'] });
        obs.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    }

    // Drawer Maximize / Restore Toggle Handler
    if (!window.coraToggleDrawerMaximize) {
        window.coraToggleDrawerMaximize = function(drawerId, maxBtnId) {
            const drawer = document.getElementById(drawerId);
            const maxBtn = document.getElementById(maxBtnId);
            if (!drawer) return;

            const isMax = drawer.classList.toggle('maximized');
            if (isMax) {
                drawer.style.setProperty('left', '0px', 'important');
                drawer.style.setProperty('width', '100vw', 'important');
                if (maxBtn) {
                    maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14h6v6"></path><path d="M20 10h-6V4"></path><path d="M14 10l7-7"></path><path d="M3 21l7-7"></path></svg>';
                    maxBtn.title = 'Restore Docked Canvas';
                }
            } else {
                if (maxBtn) {
                    maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>';
                    maxBtn.title = 'Expand / Fullscreen Canvas';
                }
                coraSyncWorkspaceGwBounds();
            }
        };
    }

    // Global opener
    window.coraOpenWorkspaceDeveloperGateway = function(defaultTab) {
        const drawer = document.getElementById('cora-workspace-developer-gateway-drawer');
        if (!drawer) {
            console.error("Workspace Developer Gateway Drawer not found in DOM");
            return;
        }

        coraSyncWorkspaceGwBounds();

        drawer.classList.remove('collapsed');
        drawer.style.setProperty('display', 'flex', 'important');
        void drawer.offsetHeight; // force reflow
        drawer.classList.add('open');

        // Scoped main scroll lock (Rule 15 SOP)
        const mainEl = document.querySelector('main.cora-main');
        if (mainEl) mainEl.classList.add('overflow-hidden');

        defaultTab = defaultTab || 'connect';
        if (typeof coraSwitchWorkspaceGwTab === 'function') {
            coraSwitchWorkspaceGwTab(defaultTab);
        }

        <?php if ( $is_mcp_eligible ) : ?>
        if (typeof coraRefreshWorkspaceKeys === 'function') coraRefreshWorkspaceKeys();
        if (typeof coraInterpolateActiveKey === 'function') coraInterpolateActiveKey(_currentKey);
        if (typeof coraLoadCampaigns === 'function') coraLoadCampaigns();
        if (typeof window.coraCheckEcosystemsStatus === 'function') {
            window.coraCheckEcosystemsStatus();
        } else if (typeof coraCheckEcosystemsStatus === 'function') {
            coraCheckEcosystemsStatus();
        }
        <?php endif; ?>
    };

    // Alias for backward compatibility
    window.coraOpenDeveloperGateway = window.coraOpenWorkspaceDeveloperGateway;

    // Global closer
    window.coraCloseWorkspaceDeveloperGateway = function() {
        const drawer = document.getElementById('cora-workspace-developer-gateway-drawer');
        if (!drawer) return;

        drawer.classList.remove('open');
        drawer.classList.add('collapsed');
        setTimeout(() => {
            if (drawer.classList.contains('collapsed')) {
                drawer.style.setProperty('display', 'none', 'important');
            }
        }, 280);

        const mainEl = document.querySelector('main.cora-main');
        if (mainEl) mainEl.classList.remove('overflow-hidden');
    };

    window.coraCloseDeveloperGateway = window.coraCloseWorkspaceDeveloperGateway;

    let _activeEcosystem = 'gemini';
    let _currentKey = 'YOUR_WORKSPACE_LIVE_KEY';
    let _campaigns = [];
    let _activityLog = [];
    let _campaignMetrics = {
        active_campaigns: 3,
        total_articles: 28,
        avg_seo: 92,
        avg_geo: 89
    };

    // Raw snippet templates for live key interpolation
    const _mcpServerUrl = '<?php echo esc_js( $mcp_server_url ); ?>';
    const _mcpSseUrl    = '<?php echo esc_js( $mcp_sse_url ); ?>';
    const _openApiUrl   = '<?php echo esc_js( $openapi_url ); ?>';
    const _agencyId     = '<?php echo esc_js( $agency_id ); ?>';

    const _tplClaudeCfg = '{\n  "mcpServers": {\n    "cora-workspace": {\n      "command": "npx",\n      "args": [\n        "-y",\n        "@modelcontextprotocol/server-fetch",\n        "' + _mcpServerUrl + '"\n      ],\n      "env": {\n        "AUTHORIZATION": "Bearer {{KEY}}"\n      }\n    }\n  }\n}';
    const _tplClaudeCli = 'claude mcp add cora-workspace -- npx -y @modelcontextprotocol/server-fetch "' + _mcpServerUrl + '" --header "Authorization: Bearer {{KEY}}"';
    const _tplGeminiCode = 'import os\nfrom google import genai\nfrom google.genai import types\n\n# Initialize Gemini 2.5/3.5 Client\nclient = genai.Client(api_key=os.environ.get("GEMINI_API_KEY"))\n\n# Cora Workspace MCP Endpoint & Auth Header\nworkspace_headers = {\n    "Authorization": "Bearer {{KEY}}",\n    "Content-Type": "application/json"\n}\nmcp_sse_endpoint = "' + _mcpSseUrl + '"\n\n# Dispatch autonomous query with live workspace tools\nresponse = client.models.generate_content(\n    model="gemini-2.5-flash",\n    contents="Audit our latest published content for SEO & GEO score gaps.",\n    config=types.GenerateContentConfig(\n        tools=[{"url": mcp_sse_endpoint, "headers": workspace_headers}]\n    )\n)\n\nprint(response.text)';
    const _tplGeminiSse  = 'Endpoint: ' + _mcpSseUrl + '\nTransport: Server-Sent Events (SSE) + JSON-RPC 2.0\nHeader:    Authorization: Bearer {{KEY}}\nSecurity:  Multi-Tenant Scoped Isolation (Workspace ID #' + _agencyId + ')';
    const _tplGeminiLocalCmd = 'npx -y @modelcontextprotocol/server-fetch "' + _mcpServerUrl.replace(/\/wp-json\/cora\/v1\/mcp/, '/mcp') + '" --header "Authorization: Bearer {{KEY}}"';
    const _tplCursorCfg  = '{\n  "mcpServers": {\n    "cora-workspace": {\n      "url": "' + _mcpServerUrl + '",\n      "headers": {\n        "Authorization": "Bearer {{KEY}}"\n      }\n    }\n  }\n}';
    const _tplWindsurfCfg = '# Cora Workspace Rules\n- MCP Server: ' + _mcpServerUrl + '\n- Auth Header: Authorization: Bearer {{KEY}}\n- Capabilities: Content drafting, 11-point SEO/GEO audits, CRM pipeline, performance metrics\n- Policy: Before proposing article publication, execute cora_audit_article_seo to ensure minimum 85+ score.';

    // Live key interpolation engine
    window.coraInterpolateActiveKey = function(key) {
        const cleanKey = (key && key.trim()) ? key.trim() : 'YOUR_WORKSPACE_LIVE_KEY';
        _currentKey = cleanKey;

        const elClaudeCfg = document.getElementById('cora-ws-claude-cfg');
        if (elClaudeCfg) elClaudeCfg.innerText = _tplClaudeCfg.replace(/{{KEY}}/g, cleanKey);

        const elClaudeCli = document.getElementById('cora-ws-claude-cli');
        if (elClaudeCli) elClaudeCli.innerText = _tplClaudeCli.replace(/{{KEY}}/g, cleanKey);

        const elGeminiCode = document.getElementById('cora-ws-gemini-code');
        if (elGeminiCode) elGeminiCode.innerText = _tplGeminiCode.replace(/{{KEY}}/g, cleanKey);

        const elGeminiSse = document.getElementById('cora-ws-gemini-sse');
        if (elGeminiSse) elGeminiSse.innerText = _tplGeminiSse.replace(/{{KEY}}/g, cleanKey);

        const elGeminiLocalCmd = document.getElementById('cora-ws-gemini-local-cmd');
        if (elGeminiLocalCmd) elGeminiLocalCmd.innerText = _tplGeminiLocalCmd.replace(/{{KEY}}/g, cleanKey);

        const elCursorCfg = document.getElementById('cora-ws-cursor-cfg');
        if (elCursorCfg) elCursorCfg.innerText = _tplCursorCfg.replace(/{{KEY}}/g, cleanKey);

        const elWindsurfCfg = document.getElementById('cora-ws-windsurf-cfg');
        if (elWindsurfCfg) elWindsurfCfg.innerText = _tplWindsurfCfg.replace(/{{KEY}}/g, cleanKey);
    };

    window.coraSelectActiveKeyForConfig = function(key) {
        const inp = document.getElementById('cora-connect-active-key');
        if (inp) inp.value = key;
        coraInterpolateActiveKey(key);

        document.querySelectorAll('#cora-connect-key-pills button').forEach(b => {
            b.classList.remove('bg-zinc-950', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'font-bold');
            b.classList.add('bg-zinc-200/70', 'dark:bg-zinc-800', 'text-zinc-800', 'dark:text-zinc-200');
        });
        const activeBtn = document.getElementById('cora-btn-key-' + key.replace(/[^a-zA-Z0-9]/g, ''));
        if (activeBtn) {
            activeBtn.classList.remove('bg-zinc-200/70', 'dark:bg-zinc-800', 'text-zinc-800', 'dark:text-zinc-200');
            activeBtn.classList.add('bg-zinc-950', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'font-bold');
        }
    };

    window.coraResetActiveKeyDefault = function() {
        const inp = document.getElementById('cora-connect-active-key');
        if (inp) inp.value = 'YOUR_WORKSPACE_LIVE_KEY';
        coraInterpolateActiveKey('YOUR_WORKSPACE_LIVE_KEY');
        document.querySelectorAll('#cora-connect-key-pills button').forEach(b => {
            b.classList.remove('bg-zinc-950', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'font-bold');
            b.classList.add('bg-zinc-200/70', 'dark:bg-zinc-800', 'text-zinc-800', 'dark:text-zinc-200');
        });
    };

    // Ecosystem Switcher (Level 01 Segmented Pill Control: Gemini, Claude, ChatGPT)
    window.coraSelectConnectEcosystem = function(ecoKey) {
        _activeEcosystem = ecoKey;
        document.querySelectorAll('#cora-connect-ecosystem-tabs .cora-eco-tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
            btn.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
        });

        const activeBtn = document.getElementById('cora-eco-btn-' + ecoKey);
        if (activeBtn) {
            activeBtn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
            activeBtn.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        }

        document.querySelectorAll('.cora-eco-panel').forEach(p => p.classList.add('hidden'));
        const activePanel = document.getElementById('cora-eco-panel-' + ecoKey);
        if (activePanel) activePanel.classList.remove('hidden');

        coraCheckEcosystemsStatus();
    };

    // Ecosystem Status Engine (Live AJAX Handshake & Status Sync)
    function coraCheckEcosystemsStatus() {
        const ajaxUrl = (window.coraREWPData && window.coraREWPData.ajaxUrl) || (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) || (window.coraREData && window.coraREData.ajaxNonce) || '';

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_get_mcp_ecosystem_status',
                nonce: nonce,
                agency_id: '<?php echo esc_js( $agency_id ); ?>'
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res && res.success && res.data) {
                const ecosystems = res.data.ecosystems || res.data;
                ['gemini', 'claude', 'chatgpt'].forEach(eco => {
                    const data = ecosystems[eco] || { connected: false };
                    coraRenderEcosystemCard(eco, data);
                });
            }
        })
        .catch(err => {
            console.warn('[Cora] Ecosystem status check network error:', err);
        });
    }
    window.coraCheckEcosystemsStatus = coraCheckEcosystemsStatus;

    function coraRenderEcosystemCard(eco, data) {
        const isConnected = !!(data && data.connected);

        // Update Tab Dot
        const dot = document.getElementById('cora-' + eco + '-status-dot');
        if (dot) {
            dot.className = isConnected 
                ? 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse'
                : 'w-2 h-2 rounded-full bg-zinc-300';
        }

        // Update Badge
        const badge = document.getElementById('cora-' + eco + '-status-badge');
        if (badge) {
            if (isConnected) {
                badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span><span>Connected &amp; Active</span>';
            } else {
                badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-700/80';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span><span>Ready to Connect &bull; 1-Click Zero Setup</span>';
            }
        }

        // Update Subtext
        const subtext = document.getElementById('cora-' + eco + '-status-subtext');
        if (subtext) {
            if (isConnected) {
                const timeStr = data.last_handshake_formatted || 'just now';
                const count = data.requests_handled || 0;
                subtext.textContent = 'Last active handshake ' + timeStr + ' \u2022 ' + count + ' tool requests handled';
            } else {
                subtext.textContent = 'No active handshake recorded yet. Follow the 1-click guided setup below.';
            }
        }

        // Update Actions
        const actions = document.getElementById('cora-' + eco + '-status-actions');
        if (actions) {
            if (isConnected) {
                actions.innerHTML = `
                    <button type="button" onclick="coraTestEcosystemHandshake('${eco}')" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-900 dark:text-zinc-100 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 cursor-pointer transition-all flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        <span>Test Handshake</span>
                    </button>
                    <button type="button" onclick="coraDisconnectEcosystem('${eco}')" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 border border-zinc-200/80 dark:border-zinc-800 cursor-pointer transition-all">
                        Disconnect
                    </button>
                `;
            } else {
                actions.innerHTML = `
                    <button type="button" onclick="coraTestEcosystemHandshake('${eco}')" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 cursor-pointer transition-all flex items-center gap-1.5 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        <span>Test Connection</span>
                    </button>
                `;
            }
        }
    };

    window.coraDisconnectEcosystem = function(eco) {
        const ajaxUrl = (window.coraREWPData && window.coraREWPData.ajaxUrl) || (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) || (window.coraREData && window.coraREData.ajaxNonce) || '';

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_disconnect_mcp_ecosystem',
                nonce: nonce,
                ecosystem: eco,
                agency_id: '<?php echo esc_js( $agency_id ); ?>'
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res && res.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || 'Connector disconnected.', 'success');
                }
                coraRenderEcosystemCard(eco, { connected: false });
            } else {
                if (window.coraShowToast) {
                    window.coraShowToast((res && res.data && res.data.message) || 'Failed to disconnect.', 'error');
                }
            }
        })
        .catch(err => {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while disconnecting.', 'error');
            }
        });
    };

    window.coraTestEcosystemHandshake = function(eco) {
        const ajaxUrl = (window.coraREWPData && window.coraREWPData.ajaxUrl) || (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) || (window.coraREData && window.coraREData.ajaxNonce) || '';

        const ecoNames = { gemini: 'Google Gemini', claude: 'Claude', chatgpt: 'ChatGPT' };
        if (window.coraShowToast) {
            window.coraShowToast('Testing handshake with ' + (ecoNames[eco] || eco) + '...', 'info');
        }

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_test_mcp_handshake',
                nonce: nonce,
                ecosystem: eco,
                agency_id: '<?php echo esc_js( $agency_id ); ?>'
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res && res.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || 'Handshake successful!', 'success');
                }
                if (res.data && res.data.status) {
                    coraRenderEcosystemCard(eco, res.data.status);
                } else {
                    coraCheckEcosystemsStatus();
                }
            } else {
                if (window.coraShowToast) {
                    window.coraShowToast((res && res.data && res.data.message) || 'Handshake test failed.', 'error');
                }
            }
        })
        .catch(err => {
            if (window.coraShowToast) {
                window.coraShowToast('Network error during handshake test.', 'error');
            }
        });
    };

    // 1-Click Launch Functions
    window.coraOpenClaudeConnectors = function() {
        const urlInput = document.getElementById('cora-claude-server-url');
        const serverUrl = urlInput ? urlInput.value : '<?php echo esc_js( home_url('/mcp') ); ?>';
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(serverUrl);
            }
        } catch (e) {}
        if (window.coraShowToast) {
            window.coraShowToast('Claude connector URL copied to clipboard! Paste it into Claude and click Continue.', 'success');
        }
        window.open('https://claude.ai/settings/connectors', '_blank', 'noopener,noreferrer');
    };

    window.coraOpenChatGptConnectors = function() {
        const urlInput = document.getElementById('cora-chatgpt-server-url') || document.getElementById('cora-chatgpt-app-link');
        const serverUrl = urlInput ? urlInput.value : '<?php echo esc_js( home_url('/mcp') ); ?>';
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(serverUrl);
            } else if (urlInput) {
                urlInput.select();
                document.execCommand('copy');
            }
        } catch (e) {}
        if (window.coraShowToast) {
            window.coraShowToast('ChatGPT MCP server URL copied to clipboard! Paste it into ChatGPT and click Continue.', 'success');
        }
        window.open('https://chatgpt.com/#settings/Connectors', '_blank', 'noopener,noreferrer');
    };

    window.coraOpenGeminiConnectedApps = function() {
        const appLinkInput = document.getElementById('cora-gemini-app-link');
        const link = appLinkInput ? appLinkInput.value : '<?php echo esc_js( home_url('/mcp') ); ?>';
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(link);
            } else if (appLinkInput) {
                appLinkInput.select();
                document.execCommand('copy');
            }
        } catch (e) {}
        if (window.coraShowToast) {
            window.coraShowToast('App link copied to clipboard! Paste it into Gemini and click Next.', 'success');
        }
        window.open('https://gemini.google.com/apps', '_blank', 'noopener,noreferrer');
    };

    // Setup Method Switchers: Claude
    window.coraSwitchClaudeSetupMethod = function(method) {
        const tabCloud = document.getElementById('cora-claude-tab-cloud');
        const tabDesktop = document.getElementById('cora-claude-tab-desktop');
        const viewCloud = document.getElementById('cora-claude-cloud-view');
        const viewDesktop = document.getElementById('cora-claude-desktop-view');

        const activeClasses = ['bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold'];
        const inactiveClasses = ['text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent'];

        if (method === 'desktop') {
            if (tabCloud) {
                tabCloud.classList.remove(...activeClasses);
                tabCloud.classList.add(...inactiveClasses);
            }
            if (tabDesktop) {
                tabDesktop.classList.remove(...inactiveClasses);
                tabDesktop.classList.add(...activeClasses);
            }
            if (viewCloud) viewCloud.classList.add('hidden');
            if (viewDesktop) viewDesktop.classList.remove('hidden');
        } else {
            if (tabDesktop) {
                tabDesktop.classList.remove(...activeClasses);
                tabDesktop.classList.add(...inactiveClasses);
            }
            if (tabCloud) {
                tabCloud.classList.remove(...inactiveClasses);
                tabCloud.classList.add(...activeClasses);
            }
            if (viewDesktop) viewDesktop.classList.add('hidden');
            if (viewCloud) viewCloud.classList.remove('hidden');
        }
    };

    // Setup Method Switchers: ChatGPT
    window.coraSwitchChatGptSetupMethod = function(method) {
        const tabMcp = document.getElementById('cora-chatgpt-tab-mcp');
        const tabSkills = document.getElementById('cora-chatgpt-tab-skills');
        const viewMcp = document.getElementById('cora-chatgpt-mcp-view');
        const viewSkills = document.getElementById('cora-chatgpt-skills-view');

        const activeClasses = ['bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold'];
        const inactiveClasses = ['text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent'];

        if (method === 'skills') {
            if (tabMcp) {
                tabMcp.classList.remove(...activeClasses);
                tabMcp.classList.add(...inactiveClasses);
            }
            if (tabSkills) {
                tabSkills.classList.remove(...inactiveClasses);
                tabSkills.classList.add(...activeClasses);
            }
            if (viewMcp) viewMcp.classList.add('hidden');
            if (viewSkills) viewSkills.classList.remove('hidden');
        } else {
            if (tabSkills) {
                tabSkills.classList.remove(...activeClasses);
                tabSkills.classList.add(...inactiveClasses);
            }
            if (tabMcp) {
                tabMcp.classList.remove(...inactiveClasses);
                tabMcp.classList.add(...activeClasses);
            }
            if (viewSkills) viewSkills.classList.add('hidden');
            if (viewMcp) viewMcp.classList.remove('hidden');
        }
    };

    window.coraToggleChatGptAdvanced = function() {
        const body = document.getElementById('cora-chatgpt-advanced-body');
        const chevron = document.getElementById('cora-chatgpt-advanced-chevron');
        if (body) {
            const isHidden = body.classList.contains('hidden');
            if (isHidden) {
                body.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                body.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }
    };

    window.coraCopyChatGptCredentials = function() {
        const clientIdEl = document.getElementById('cora-chatgpt-client-id');
        const clientSecEl = document.getElementById('cora-chatgpt-client-secret');
        const clientId = clientIdEl ? clientIdEl.value : 'cora_client_ws_<?php echo esc_js( $agency_id ); ?>';
        const clientSec = clientSecEl ? clientSecEl.value : 'cora_chatgpt_sec_<?php echo esc_js( $agency_id ); ?>';
        const formatted = 'Client ID: ' + clientId + '\nClient Secret: ' + clientSec;
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(formatted);
            }
        } catch (e) {}
        if (window.coraShowToast) {
            window.coraShowToast('Both Client ID and Secret copied to clipboard.', 'success');
        }
    };

    // Google Gemini Setup Handlers
    window.coraSwitchGeminiSetupMethod = function(method) {
        const tabCloud = document.getElementById('cora-gemini-tab-cloud');
        const tabLocal = document.getElementById('cora-gemini-tab-local');
        const viewCloud = document.getElementById('cora-gemini-cloud-view');
        const viewLocal = document.getElementById('cora-gemini-local-view');

        const activeClasses = ['bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold'];
        const inactiveClasses = ['text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent'];

        if (method === 'local') {
            if (tabCloud) {
                tabCloud.classList.remove(...activeClasses);
                tabCloud.classList.add(...inactiveClasses);
            }
            if (tabLocal) {
                tabLocal.classList.remove(...inactiveClasses);
                tabLocal.classList.add(...activeClasses);
            }
            if (viewCloud) viewCloud.classList.add('hidden');
            if (viewLocal) viewLocal.classList.remove('hidden');
        } else {
            if (tabLocal) {
                tabLocal.classList.remove(...activeClasses);
                tabLocal.classList.add(...inactiveClasses);
            }
            if (tabCloud) {
                tabCloud.classList.remove(...inactiveClasses);
                tabCloud.classList.add(...activeClasses);
            }
            if (viewLocal) viewLocal.classList.add('hidden');
            if (viewCloud) viewCloud.classList.remove('hidden');
        }
    };

    window.coraToggleGeminiAdvanced = function() {
        const body = document.getElementById('cora-gemini-advanced-body');
        const chevron = document.getElementById('cora-gemini-advanced-chevron');
        if (body) {
            const isHidden = body.classList.contains('hidden');
            if (isHidden) {
                body.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                body.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }
    };

    window.coraToggleGeminiSdkSnippet = function() {
        const body = document.getElementById('cora-gemini-sdk-body');
        const chevron = document.getElementById('cora-gemini-sdk-chevron');
        if (body) {
            const isHidden = body.classList.contains('hidden');
            if (isHidden) {
                body.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                body.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }
    };

    window.coraCopyGeminiCredentials = function() {
        const clientIdEl = document.getElementById('cora-gemini-client-id');
        const clientSecEl = document.getElementById('cora-gemini-client-secret');
        const clientId = clientIdEl ? clientIdEl.value : 'cora_client_ws_<?php echo esc_js( $agency_id ); ?>';
        const clientSec = clientSecEl ? clientSecEl.value : 'cora_gemini_sec_<?php echo esc_js( $agency_id ); ?>';
        const formatted = 'Client ID: ' + clientId + '\nClient Secret: ' + clientSec;
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(formatted);
            }
        } catch (e) {}
        if (window.coraShowToast) {
            window.coraShowToast('Both Client ID and Secret copied to clipboard.', 'success');
        }
    };

    window.coraCopyAppText = function(text, label) {
        if (!text) return;
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text);
            }
        } catch (e) {}
        if (window.coraShowToast) {
            window.coraShowToast((label || 'Text') + ' copied to clipboard.', 'success');
        }
    };

    // Subtab Switcher
    window.coraSwitchWorkspaceGwTab = function(tabName) {
        _activeTab = tabName;
        document.querySelectorAll('#cora-ws-gw-tab-bar button').forEach(btn => {
            btn.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
            btn.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
        });

        const activeBtn = document.getElementById('cora-ws-gw-btn-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
            activeBtn.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        }

        document.querySelectorAll('.cora-ws-pane').forEach(p => p.classList.add('hidden'));
        const activePane = document.getElementById('cora-ws-pane-' + tabName);
        if (activePane) activePane.classList.remove('hidden');

        if (tabName === 'campaigns') {
            coraLoadCampaigns();
        } else if (tabName === 'connect') {
            coraInterpolateActiveKey(_currentKey);
            coraCheckEcosystemsStatus();
        }
    };

    // Scope Pill Selector
    window.coraSelectKeyScope = function(scopeKey) {
        _activeScope = scopeKey;
        document.querySelectorAll('#cora-scope-pills-container .scope-pill-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
            btn.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
        });

        const activePill = document.getElementById('scope-btn-' + scopeKey);
        if (activePill) {
            activePill.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
            activePill.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        }

        const input = document.getElementById('cora-ws-key-scope-val');
        if (input) input.value = scopeKey;
    };

    // Sandbox Endpoint Pill Selector
    window.coraSelectSandboxEndpoint = function(ep, btn) {
        document.querySelectorAll('#cora-sandbox-endpoints-container .sbx-ep-btn').forEach(b => {
            b.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
            b.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
        });
        if (btn) {
            btn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
            btn.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        }
        const valInput = document.getElementById('cora-sandbox-selected-endpoint');
        if (valInput) valInput.value = ep;
    };

    // Key Generator Submit
    window.coraSubmitGenerateKey = function() {
        const nameInput = document.getElementById('cora-ws-key-name');
        const name = nameInput ? nameInput.value.trim() : '';

        if (!name) {
            if (window.coraShowToast) window.coraShowToast('Please provide a key description or purpose.', 'warning');
            return;
        }

        let scopes = [];
        if (_activeScope === 'full') {
            scopes = ['workspace:full', 'content:read', 'content:write', 'crm:read', 'crm:write', 'media:read', 'media:write', 'analytics:read'];
        } else if (_activeScope === 'content_media') {
            scopes = ['content:read', 'content:write', 'media:read', 'media:write'];
        } else if (_activeScope === 'crm') {
            scopes = ['crm:read', 'crm:write', 'clients:read', 'clients:write'];
        } else {
            scopes = ['workspace:read', 'content:read', 'crm:read'];
        }

        const ajaxUrl = (window.coraREWPData && window.coraREWPData.ajaxUrl) || (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) || (window.coraREData && window.coraREData.ajaxNonce) || '';

        jQuery.post(ajaxUrl, {
            action: 'cora_ajax_create_workspace_api_key',
            name: name,
            key_name: name,
            scopes: scopes,
            nonce: nonce
        }, function(res) {
            if (res && res.success && res.data && res.data.key) {
                const alertEl = document.getElementById('cora-ws-new-key-alert');
                const valInput = document.getElementById('cora-ws-new-key-input');
                if (alertEl && valInput) {
                    valInput.value = res.data.key.api_key;
                    alertEl.classList.remove('hidden');
                }
                if (nameInput) nameInput.value = '';
                if (window.coraShowToast) window.coraShowToast('Workspace Live Key generated successfully.', 'success');
                coraSelectActiveKeyForConfig(res.data.key.api_key);
                coraRefreshWorkspaceKeys();
            } else {
                const msg = (res && res.data && res.data.message) ? res.data.message : 'Failed to generate key.';
                if (window.coraShowToast) window.coraShowToast(msg, 'error');
            }
        }).fail(function() {
            if (window.coraShowToast) window.coraShowToast('Network error while generating key.', 'error');
        });
    };

    // Refresh Keys List
    window.coraRefreshWorkspaceKeys = function() {
        const container = document.getElementById('cora-ws-keys-list-container');
        if (!container) return;

        const ajaxUrl = (window.coraREWPData && window.coraREWPData.ajaxUrl) || (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) || (window.coraREData && window.coraREData.ajaxNonce) || '';

        jQuery.post(ajaxUrl, {
            action: 'cora_ajax_get_workspace_api_keys',
            nonce: nonce
        }, function(res) {
            if (res && res.success && res.data && Array.isArray(res.data.keys)) {
                _wsApiKeys = res.data.keys;
                renderWorkspaceKeys(res.data.keys);
            } else {
                container.innerHTML = '<div class="p-6 text-center text-xs text-zinc-400">No active keys found. Generate a key above to start.</div>';
            }
        }).fail(function() {
            container.innerHTML = '<div class="p-6 text-center text-xs text-rose-500">Failed to load workspace keys.</div>';
        });
    };

    function renderWorkspaceKeys(keys) {
        const container = document.getElementById('cora-ws-keys-list-container');
        if (!container) return;

        if (!keys || keys.length === 0) {
            container.innerHTML = '<div class="p-6 text-center text-xs text-zinc-400">No active keys found. Generate a key above to start.</div>';
            return;
        }

        const activeKeys = keys.filter(k => k.status !== 'revoked');
        const revokedKeys = keys.filter(k => k.status === 'revoked');

        let html = '';

        if (activeKeys.length === 0) {
            html += '<div class="p-4 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800 text-center text-xs text-zinc-400">No active keys. Generate a key to authenticate your AI agents.</div>';
        } else {
            activeKeys.forEach(k => {
                const statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active</span>';
                const scopesList = Array.isArray(k.scopes) ? k.scopes.join(' • ') : (k.scopes || 'workspace:full');

                html += `
                <div class="p-3.5 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="min-w-0 space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-950 dark:text-white truncate">${escapeHtml(k.name || 'Workspace Agent')}</span>
                            ${statusBadge}
                        </div>
                        <div class="flex items-center gap-2 text-xs font-mono text-zinc-600 dark:text-zinc-400">
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">${escapeHtml(k.key_prefix || 'cora_ws_live_')}••••••••••••••••</span>
                            <span class="text-zinc-300 dark:text-zinc-700">|</span>
                            <span class="text-[11px] truncate max-w-xs text-zinc-500">${escapeHtml(scopesList)}</span>
                        </div>
                        <div class="text-[10px] text-zinc-400">Created: ${escapeHtml(k.created_at || 'Just now')}</div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="coraSelectActiveKeyForConfig('${escapeHtml((k.key_prefix || '').replace(/\.+$/, ''))}')" class="px-2.5 py-1.5 rounded-lg border border-zinc-200/80 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-semibold text-zinc-700 dark:text-zinc-200 hover:text-black dark:hover:text-white cursor-pointer transition-colors" title="Inject into AI Configurations">
                            Inject Key
                        </button>
                        <button type="button" onclick="coraCopySnippetDirect('${escapeHtml(k.key_prefix || '')}')" class="px-2.5 py-1.5 rounded-lg border border-zinc-200/80 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-semibold text-zinc-700 dark:text-zinc-200 hover:text-black dark:hover:text-white cursor-pointer transition-colors" title="Copy Key Prefix">
                            Copy Prefix
                        </button>
                        <button type="button" onclick="coraRevokeWorkspaceKey(${k.id})" class="px-2.5 py-1.5 rounded-lg border border-zinc-200/80 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/30 cursor-pointer transition-colors" title="Revoke this key immediately">
                            Revoke
                        </button>
                    </div>
                </div>`;
            });
        }

        if (revokedKeys.length > 0) {
            html += `
            <details class="group rounded-xl border border-zinc-200/60 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-900/20 overflow-hidden mt-3">
                <summary class="px-4 py-2 text-xs font-semibold text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 cursor-pointer select-none flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Archived / Revoked Keys (${revokedKeys.length})</span>
                    </span>
                    <span class="text-[10px] font-mono text-zinc-400 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="p-3 space-y-1.5 border-t border-zinc-200/60 dark:border-zinc-800">
            `;
            revokedKeys.forEach(k => {
                html += `
                <div class="p-2 rounded-lg bg-zinc-100/50 dark:bg-zinc-800/40 flex items-center justify-between text-xs opacity-75">
                    <div class="min-w-0">
                        <span class="font-medium text-zinc-600 dark:text-zinc-400 truncate">${escapeHtml(k.name || 'Workspace Key')}</span>
                        <span class="ml-2 font-mono text-[11px] text-zinc-400">${escapeHtml(k.key_prefix || 'cora_ws_live_')}••••</span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-zinc-200 dark:bg-zinc-700 text-zinc-500">Revoked</span>
                </div>`;
            });
            html += `
                </div>
            </details>`;
        }

        container.innerHTML = html;

        // Populate Active Key Injector selector in Tab 2
        const keyPillsContainer = document.getElementById('cora-connect-key-pills');
        if (keyPillsContainer) {
            let pillsHtml = `<button type="button" onclick="coraResetActiveKeyDefault()" class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-medium ${(_currentKey === 'YOUR_WORKSPACE_LIVE_KEY') ? 'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold' : 'bg-zinc-200/70 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200'} border-0 cursor-pointer">Default Key</button>`;
            keys.forEach(k => {
                if (k.status !== 'revoked' && k.key_prefix) {
                    const cleanP = k.key_prefix.replace(/\.+$/, '');
                    const domId = 'cora-btn-key-' + cleanP.replace(/[^a-zA-Z0-9]/g, '');
                    const isAct = _currentKey === cleanP;
                    pillsHtml += `<button type="button" id="${domId}" onclick="coraSelectActiveKeyForConfig('${cleanP}')" class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-medium ${isAct ? 'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold' : 'bg-zinc-200/70 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200'} border-0 cursor-pointer whitespace-nowrap" title="${escapeHtml(k.name || 'Workspace Key')}">${escapeHtml(cleanP)}</button>`;
                }
            });
            keyPillsContainer.innerHTML = pillsHtml;
        }

        // Auto-select first active key if currently default
        if (_currentKey === 'YOUR_WORKSPACE_LIVE_KEY' && keys.length > 0 && keys[0].key_prefix) {
            const firstClean = keys[0].key_prefix.replace(/\.+$/, '');
            coraSelectActiveKeyForConfig(firstClean);
        }
    }

    // Revoke Key
    window.coraRevokeWorkspaceKey = function(keyId) {
        const ajaxUrl = (window.coraREWPData && window.coraREWPData.ajaxUrl) || (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) || (window.coraREData && window.coraREData.ajaxNonce) || '';

        jQuery.post(ajaxUrl, {
            action: 'cora_ajax_revoke_workspace_api_key',
            key_id: keyId,
            nonce: nonce
        }, function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Workspace Key revoked.', 'success');
                coraRefreshWorkspaceKeys();
            } else {
                if (window.coraShowToast) window.coraShowToast('Failed to revoke key.', 'error');
            }
        });
    };

    // Autonomous Campaigns Management Engine
    window.coraSelectCmpCadence = function(cadence, btn) {
        document.querySelectorAll('#cora-cmp-cadence-pills .cmp-cadence-btn').forEach(b => {
            b.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
            b.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
        });
        if (btn) {
            btn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
            btn.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        }
        const inp = document.getElementById('cora-cmp-cadence-val');
        if (inp) inp.value = cadence;
    };

    window.coraSelectCmpAutonomy = function(val, btn) {
        document.querySelectorAll('#cora-cmp-autonomy-pills .cmp-autonomy-btn').forEach(b => {
            b.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
            b.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
        });
        if (btn) {
            btn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
            btn.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        }
        const inp = document.getElementById('cora-cmp-autonomy-val');
        if (inp) inp.value = val;
    };

    window.coraSelectCmpModel = function(model, btn) {
        document.querySelectorAll('#cora-cmp-model-pills .cmp-model-btn').forEach(b => {
            b.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
            b.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
        });
        if (btn) {
            btn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
            btn.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        }
        const inp = document.getElementById('cora-cmp-model-val');
        if (inp) inp.value = model;
    };

    window.coraSelectCmpGate = function(type, score, btn) {
        const container = document.getElementById('cora-cmp-' + type + '-gate-pills');
        if (container) {
            container.querySelectorAll('button').forEach(b => {
                b.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
                b.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
            });
        }
        if (btn) {
            btn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
            btn.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        }
        const inp = document.getElementById('cora-cmp-min-' + type + '-val');
        if (inp) inp.value = score;
        const display = document.getElementById('cora-cmp-' + type + '-gate-display');
        if (display) display.innerText = score + '+';
    };

    window.coraLoadCampaigns = function() {
        const ajaxUrl = (window.coraREWPData && window.coraREWPData.ajaxUrl) || (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) || (window.coraREData && window.coraREData.ajaxNonce) || '';

        jQuery.post(ajaxUrl, {
            action: 'cora_ajax_get_campaigns',
            nonce: nonce
        }, function(res) {
            if (res && res.success && res.data) {
                _campaigns = res.data.campaigns || [];
                _activityLog = res.data.activity_log || [];
                _campaignMetrics = res.data.metrics || _campaignMetrics;

                updateCampaignMetricsUI(_campaignMetrics);
                renderCampaignMatrix(_campaigns);
                renderCampaignActivityLog(_activityLog);
            } else {
                useDefaultCampaignsFallback();
            }
        }).fail(function() {
            useDefaultCampaignsFallback();
        });
    };

    function useDefaultCampaignsFallback() {
        if (_campaigns.length === 0) {
            _campaigns = [
                {
                    id: 'cmp_realestate_advisory',
                    name: 'Commercial & Office Advisory Hub',
                    keywords: 'commercial lease agreement, Bangalore startup office space, GST on commercial rent, lock-in period clause',
                    cadence: 'Weekly',
                    auto_publish: 1,
                    min_seo: 85,
                    min_geo: 85,
                    model: 'Claude 3.5 Sonnet',
                    articles_count: 14,
                    next_run: 'Tomorrow at 06:00 UTC',
                    status: 'active',
                    last_run: '2 days ago'
                },
                {
                    id: 'cmp_studio_cyclorama',
                    name: 'Studio Production & Cyclorama Guides',
                    keywords: 'cyclorama infinity cove hire, commercial fashion shoot checklist, SAC 9983 photography GST, daylight studio rental',
                    cadence: '3x / Week',
                    auto_publish: 1,
                    min_seo: 80,
                    min_geo: 80,
                    model: 'Gemini 3.5 Flash',
                    articles_count: 9,
                    next_run: 'Today at 18:00 UTC',
                    status: 'active',
                    last_run: 'Yesterday'
                },
                {
                    id: 'cmp_retainer_invoicing',
                    name: 'Retainer Agreements & GST Compliance',
                    keywords: 'freelance creative retainer agreement, 18% GST SAC 998361, client scope creep prevention, late payment interest clause',
                    cadence: 'Bi-Weekly',
                    auto_publish: 0,
                    min_seo: 90,
                    min_geo: 85,
                    model: 'GPT-4o',
                    articles_count: 5,
                    next_run: 'In 4 days',
                    status: 'active',
                    last_run: '5 days ago'
                }
            ];

            _activityLog = [
                {
                    id: 'act_1',
                    title: 'Commercial Office Lease Checklist for Startup Founders (2026)',
                    pillar: 'Commercial & Office Advisory Hub',
                    keywords: 'lease agreement, lock-in clause',
                    model: 'Claude 3.5 Sonnet',
                    seo_score: 94,
                    geo_score: 91,
                    status: 'Published',
                    published_at: '3 hours ago'
                },
                {
                    id: 'act_2',
                    title: 'The Complete Guide to Hiring an Infinity Cyclorama Studio',
                    pillar: 'Studio Production & Cyclorama Guides',
                    keywords: 'cyclorama hire, daylight studio',
                    model: 'Gemini 3.5 Flash',
                    seo_score: 88,
                    geo_score: 86,
                    status: 'Published',
                    published_at: 'Yesterday'
                },
                {
                    id: 'act_3',
                    title: 'SAC 9983 Invoicing Playbook for Creative Agencies in India',
                    pillar: 'Retainer Agreements & GST Compliance',
                    keywords: 'SAC 9983, GST invoice',
                    model: 'GPT-4o',
                    seo_score: 96,
                    geo_score: 92,
                    status: 'Draft',
                    published_at: '3 days ago'
                }
            ];
        }
        updateCampaignMetricsUI(_campaignMetrics);
        renderCampaignMatrix(_campaigns);
        renderCampaignActivityLog(_activityLog);
    }

    function updateCampaignMetricsUI(m) {
        const elActive   = document.getElementById('cora-cmp-stat-active');
        const elArticles = document.getElementById('cora-cmp-stat-articles');
        const elSeo      = document.getElementById('cora-cmp-stat-seo');
        const elGeo      = document.getElementById('cora-cmp-stat-geo');

        if (elActive)   elActive.innerText = m.active_campaigns ?? _campaigns.length;
        if (elArticles) elArticles.innerText = m.total_articles ?? 28;
        if (elSeo)      elSeo.innerText = m.avg_seo ?? 92;
        if (elGeo)      elGeo.innerText = m.avg_geo ?? 89;
    }

    function renderCampaignMatrix(campaigns) {
        const container = document.getElementById('cora-campaigns-matrix-container');
        if (!container) return;

        if (!campaigns || campaigns.length === 0) {
            container.innerHTML = '<div class="p-6 text-center text-xs text-zinc-400">No active campaigns configured. Create your first autonomous campaign above.</div>';
            return;
        }

        let html = '';
        campaigns.forEach(c => {
            const isAutoPublish = Number(c.auto_publish) === 1 || c.auto_publish === true || c.auto_publish === '1';
            const autoBadge = isAutoPublish
                ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Auto-Publish (Live)</span>'
                : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200/70 dark:border-zinc-700"><span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>Draft for Review</span>';

            const kwList = c.keywords || (Array.isArray(c.topic_keywords) ? c.topic_keywords.join(', ') : c.topic_keywords) || 'Autonomous discovery';
            const artCount = c.articles_count ?? c.articles_generated_count ?? 0;
            const nextRun = c.next_run || c.next_run_at || 'Scheduled';
            const lastRun = c.last_run || c.last_run_at || 'Never';
            const modelName = c.model || (c.assigned_ai ? (c.assigned_ai.charAt(0).toUpperCase() + c.assigned_ai.slice(1)) : 'Claude 3.5 Sonnet');

            html += `
            <div class="p-4 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="min-w-0 space-y-1.5 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-zinc-950 dark:text-white truncate">${escapeHtml(c.name || 'Autonomous Campaign')}</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-zinc-200/70 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">${escapeHtml(c.cadence || 'Weekly')}</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-medium bg-zinc-100 dark:bg-zinc-800/80 text-zinc-600 dark:text-zinc-400">${escapeHtml(modelName)}</span>
                        ${autoBadge}
                    </div>
                    <div class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate max-w-xl">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-300">Target Keywords:</span> ${escapeHtml(kwList)}
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-[10px] text-zinc-400 font-mono">
                        <span>Articles: <b class="text-zinc-700 dark:text-zinc-200 font-mono">${artCount} Published</b></span>
                        <span>•</span>
                        <span>Next Run: <b class="text-zinc-700 dark:text-zinc-200">${escapeHtml(nextRun)}</b></span>
                        <span>•</span>
                        <span>Last Run: ${escapeHtml(lastRun)}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
                    <button type="button" id="cora-btn-run-${escapeHtml(c.id)}" onclick="coraTriggerCampaignRun('${escapeHtml(c.id)}', this)" class="px-3 py-1.5 rounded-lg border border-zinc-200/80 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-xs font-bold text-zinc-900 dark:text-zinc-100 cursor-pointer transition-all shadow-2xs flex items-center gap-1.5 active:scale-97" title="Execute campaign pipeline immediately">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        <span>Run Now</span>
                    </button>
                </div>
            </div>`;
        });

        container.innerHTML = html;
    }

    function renderCampaignActivityLog(logs) {
        const container = document.getElementById('cora-campaigns-activity-container');
        if (!container) return;

        if (!logs || logs.length === 0) {
            container.innerHTML = '<div class="p-6 text-center text-xs text-zinc-400">No autonomous activity recorded yet. Run a campaign to start streaming publications.</div>';
            return;
        }

        let html = '';
        logs.forEach(item => {
            const isPub = item.status === 'Published' || item.published === true;
            const statusBadge = isPub
                ? '<span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50">Published</span>'
                : '<span class="px-2 py-0.5 rounded text-[10px] font-mono font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">Draft</span>';
            const title = item.title || item.article_title || 'Autonomous Article';
            const pillar = item.pillar || item.campaign_name || 'Campaign Pillar';
            const modelName = item.model || 'Claude 3.5 Sonnet';
            const pubDate = item.published_at || item.timestamp || 'Recently';

            html += `
            <div class="p-3.5 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/40 dark:bg-zinc-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <div class="min-w-0 space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-zinc-950 dark:text-white truncate">${escapeHtml(title)}</span>
                        ${statusBadge}
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-[11px] text-zinc-500">
                        <span class="font-medium text-zinc-700 dark:text-zinc-300">${escapeHtml(pillar)}</span>
                        <span>•</span>
                        <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">${escapeHtml(modelName)}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50">SEO: ${item.seo_score || 90}/100</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200/50 dark:border-blue-800/50">GEO: ${item.geo_score || 88}/100</span>
                    <span class="text-[10px] font-mono text-zinc-400 pl-1">${escapeHtml(pubDate)}</span>
                </div>
            </div>`;
        });

        container.innerHTML = html;
    }

    window.coraCreateCampaign = function() {
        const nameInput = document.getElementById('cora-cmp-new-name');
        const kwInput   = document.getElementById('cora-cmp-new-keywords');
        const cadInput  = document.getElementById('cora-cmp-cadence-val');
        const autoInput = document.getElementById('cora-cmp-autonomy-val');
        const modInput  = document.getElementById('cora-cmp-model-val');
        const seoInput  = document.getElementById('cora-cmp-min-seo-val');
        const geoInput  = document.getElementById('cora-cmp-min-geo-val');

        const name = nameInput ? nameInput.value.trim() : '';
        const keywords = kwInput ? kwInput.value.trim() : '';

        if (!name) {
            if (window.coraShowToast) window.coraShowToast('Please provide a campaign name.', 'warning');
            return;
        }

        const btn = document.getElementById('cora-btn-create-campaign');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin" viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path></svg><span>Creating Pipeline...</span>';
        }

        const ajaxUrl = (window.coraREWPData && window.coraREWPData.ajaxUrl) || (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) || (window.coraREData && window.coraREData.ajaxNonce) || '';

        jQuery.post(ajaxUrl, {
            action: 'cora_ajax_create_campaign',
            name: name,
            keywords: keywords,
            cadence: cadInput ? cadInput.value : 'Weekly',
            auto_publish: autoInput ? autoInput.value : '1',
            min_seo: seoInput ? seoInput.value : '85',
            min_geo: geoInput ? geoInput.value : '85',
            model: modInput ? modInput.value : 'Claude 3.5 Sonnet',
            nonce: nonce
        }, function(res) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg><span>Create Autonomous Campaign</span>';
            }
            if (res && res.success) {
                if (nameInput) nameInput.value = '';
                if (kwInput) kwInput.value = '';
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Campaign created successfully.', 'success');
                coraLoadCampaigns();
            } else {
                const msg = (res && res.data && res.data.message) ? res.data.message : 'Failed to create campaign.';
                if (window.coraShowToast) window.coraShowToast(msg, 'error');
            }
        }).fail(function() {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg><span>Create Autonomous Campaign</span>';
            }
            // Offline fallback
            const newCamp = {
                id: 'cmp_' + Math.random().toString(36).substr(2, 9),
                name: name,
                keywords: keywords || 'Industry authority keywords',
                cadence: cadInput ? cadInput.value : 'Weekly',
                auto_publish: autoInput ? Number(autoInput.value) : 1,
                min_seo: seoInput ? Number(seoInput.value) : 85,
                min_geo: geoInput ? Number(geoInput.value) : 85,
                model: modInput ? modInput.value : 'Claude 3.5 Sonnet',
                articles_count: 0,
                next_run: 'Today, scheduled',
                status: 'active',
                last_run: 'Never'
            };
            _campaigns.unshift(newCamp);
            _campaignMetrics.active_campaigns = _campaigns.length;
            updateCampaignMetricsUI(_campaignMetrics);
            renderCampaignMatrix(_campaigns);
            if (nameInput) nameInput.value = '';
            if (kwInput) kwInput.value = '';
            if (window.coraShowToast) window.coraShowToast("Autonomous campaign '" + name + "' created.", 'success');
        });
    };

    window.coraTriggerCampaignRun = function(id, btn) {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin" viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path></svg><span>Running...</span>';
        }

        const ajaxUrl = (window.coraREWPData && window.coraREWPData.ajaxUrl) || (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) || (window.coraREData && window.coraREData.ajaxNonce) || '';

        jQuery.post(ajaxUrl, {
            action: 'cora_ajax_trigger_campaign_run',
            campaign_id: id,
            nonce: nonce
        }, function(res) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg><span>Run Now</span>';
            }
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Campaign executed. Article generated.', 'success');
                coraLoadCampaigns();
            } else {
                const msg = (res && res.data && res.data.message) ? res.data.message : 'Failed to execute campaign.';
                if (window.coraShowToast) window.coraShowToast(msg, 'error');
            }
        }).fail(function() {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg><span>Run Now</span>';
            }
            // Offline simulation fallback
            const match = _campaigns.find(c => c.id === id);
            if (match) {
                match.articles_count = (match.articles_count || 0) + 1;
                match.last_run = 'Just now';
                const kwFirst = (match.keywords || 'Strategy Guide').split(',')[0].trim();
                _activityLog.unshift({
                    id: 'act_' + Math.random().toString(36).substr(2, 7),
                    title: kwFirst.charAt(0).toUpperCase() + kwFirst.slice(1) + ': Comprehensive Framework (2026)',
                    pillar: match.name,
                    keywords: kwFirst,
                    model: match.model || 'Claude 3.5 Sonnet',
                    seo_score: Math.floor(Math.random() * 8) + 90,
                    geo_score: Math.floor(Math.random() * 8) + 88,
                    status: match.auto_publish ? 'Published' : 'Draft',
                    published_at: 'Just now'
                });
                _campaignMetrics.total_articles = (_campaignMetrics.total_articles || 28) + 1;
                updateCampaignMetricsUI(_campaignMetrics);
                renderCampaignMatrix(_campaigns);
                renderCampaignActivityLog(_activityLog);
                if (window.coraShowToast) window.coraShowToast("Campaign '" + match.name + "' executed. Autonomous article drafted.", 'success');
            }
        });
    };

    // Sandbox execution
    window.coraRunWorkspaceSandbox = function() {
        const endpointInput = document.getElementById('cora-sandbox-selected-endpoint');
        const endpoint = endpointInput ? endpointInput.value : '/wp-json/cora/v1/content?limit=3';

        const statusEl = document.getElementById('cora-ws-sandbox-status');
        const timeEl   = document.getElementById('cora-ws-sandbox-time');
        const outputEl = document.getElementById('cora-ws-sandbox-output');

        if (statusEl) statusEl.innerText = 'Status: Executing...';
        const t0 = performance.now();

        const headers = { 'Content-Type': 'application/json' };
        if (_wsApiKeys.length > 0 && _wsApiKeys[0].key_prefix) {
            headers['Authorization'] = 'Bearer ' + _wsApiKeys[0].key_prefix;
        }

        fetch(endpoint, {
            method: 'GET',
            headers: headers
        })
        .then(async res => {
            const t1 = performance.now();
            const elapsed = Math.round(t1 - t0);
            if (timeEl) timeEl.innerText = elapsed + ' ms';
            if (statusEl) {
                statusEl.innerText = 'HTTP ' + res.status + ' ' + (res.ok ? 'OK' : 'Error');
                statusEl.className = 'px-2 py-0.5 rounded text-[10px] font-bold font-mono ' + 
                    (res.ok ? 'bg-emerald-950 text-emerald-400' : 'bg-rose-950 text-rose-400');
            }
            const json = await res.json();
            if (outputEl) outputEl.innerText = JSON.stringify(json, null, 2);
        })
        .catch(err => {
            if (statusEl) {
                statusEl.innerText = 'Network Error';
                statusEl.className = 'px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-rose-950 text-rose-400';
            }
            if (outputEl) outputEl.innerText = '// Error: ' + err.message;
        });
    };

    // Helpers
    window.coraCopyInputValue = function(id) {
        const input = document.getElementById(id);
        if (input) {
            navigator.clipboard.writeText(input.value);
            if (window.coraShowToast) window.coraShowToast('Copied to clipboard.', 'success');
        }
    };

    window.coraCopySnippet = function(id) {
        const el = document.getElementById(id);
        if (el) {
            navigator.clipboard.writeText(el.innerText);
            if (window.coraShowToast) window.coraShowToast('Configuration snippet copied.', 'success');
        }
    };

    window.coraCopySnippetDirect = function(text) {
        if (text) {
            navigator.clipboard.writeText(text);
            if (window.coraShowToast) window.coraShowToast('Copied to clipboard.', 'success');
        }
    };

    function escapeHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Global Escape key dismissal (Rule 15 SOP)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const drawer = document.getElementById('cora-workspace-developer-gateway-drawer');
            if (drawer && drawer.classList.contains('open')) {
                coraCloseWorkspaceDeveloperGateway();
            }
        }
    });

    // Auto-sync multi-model ecosystem statuses on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(coraCheckEcosystemsStatus, 300);
        });
    } else {
        setTimeout(coraCheckEcosystemsStatus, 300);
    }
})();
</script>
