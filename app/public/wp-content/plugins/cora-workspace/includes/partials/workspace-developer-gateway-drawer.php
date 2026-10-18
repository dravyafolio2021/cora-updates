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

$mcp_server_url = home_url( '/wp-json/cora/v1/mcp' );
$mcp_sse_url    = home_url( '/wp-json/cora-mcp/v1/sse' );
$rest_base_url  = home_url( '/wp-json/cora/v1' );
$openapi_url    = home_url( '/wp-json/cora/v1/mcp/openapi.json' );
?>

<div id="cora-workspace-developer-gateway-drawer" class="cora-docked-drawer collapsed" aria-label="Workspace Developer API and MCP Gateway">
    <!-- Top Header Bar (3-Part Alignment) -->
    <div class="px-4 sm:px-6 py-2.5 sm:py-3 border-b border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
        <!-- Mobile Drag Handle Indicator (Rule 12 SOP) -->
        <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-700 mx-auto my-1.5 mb-2.5 lg:hidden"></div>
        <div class="flex flex-wrap lg:flex-nowrap items-center min-h-[40px] w-full gap-y-2">
            <!-- Left: Icon, Title & Pill -->
            <div class="flex items-center gap-2.5 min-w-0 mr-4">
                <div class="w-8 h-8 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                </div>
                <div class="min-w-0 flex items-center gap-2">
                    <h2 class="text-sm font-bold text-zinc-950 dark:text-white truncate tracking-tight">Workspace API &amp; MCP Gateway</h2>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Workspace Live</span>
                    </span>
                </div>
            </div>

            <!-- Center: Level 01 Segmented Pill Control -->
            <div class="flex items-center justify-start lg:justify-center overflow-x-auto cora-no-scrollbar py-0.5 flex-1 min-w-0">
                <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 select-none shrink-0" id="cora-ws-gw-tab-bar">
                    <button type="button" onclick="coraSwitchWorkspaceGwTab('keys')" id="cora-ws-gw-btn-keys" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 2l-2 2m-2-2l2 2m-2 2l2-2m-7 7l7-7-3-3-7 7v3h3z"></path><circle cx="7.5" cy="16.5" r="4.5"></circle></svg>
                        <span>Workspace Keys</span>
                    </button>
                    <button type="button" onclick="coraSwitchWorkspaceGwTab('connect')" id="cora-ws-gw-btn-connect" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                        <span>Connect AI Agent</span>
                    </button>
                    <button type="button" onclick="coraSwitchWorkspaceGwTab('campaigns')" id="cora-ws-gw-btn-campaigns" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                        <span>Autonomous Campaigns</span>
                    </button>
                    <button type="button" onclick="coraSwitchWorkspaceGwTab('tools')" id="cora-ws-gw-btn-tools" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        <span>Universal Tools</span>
                    </button>
                    <button type="button" onclick="coraSwitchWorkspaceGwTab('rest')" id="cora-ws-gw-btn-rest" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        <span>REST Endpoints</span>
                    </button>
                    <button type="button" onclick="coraSwitchWorkspaceGwTab('sandbox')" id="cora-ws-gw-btn-sandbox" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        <span>Live Sandbox</span>
                    </button>
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

    <!-- Scrollable Body Canvas (Centered max-w-4xl) -->
    <div class="cora-drawer-body flex-1 overflow-y-auto px-4 sm:px-8 py-6 bg-zinc-50/50 dark:bg-zinc-950/50">
        <div class="max-w-4xl mx-auto space-y-6">

            <!-- ============================================================
                 TAB 1: WORKSPACE API KEYS
                 ============================================================ -->
            <div id="cora-ws-pane-keys" class="cora-ws-pane space-y-5">
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
            <div id="cora-ws-pane-connect" class="cora-ws-pane hidden space-y-5">
                <!-- Informational Header Card -->
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Connect Multi-Model AI Ecosystems</h3>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700 self-start sm:self-auto">
                            Universal MCP &amp; OpenAPI Gateway
                        </span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Connect Claude, Google Gemini, ChatGPT, Cursor, or Windsurf to your unified workspace. Once connected, your AI assistants can autonomously audit 11-point SEO/GEO scores, discover keyword gaps, draft articles, manage leads, and retrieve metrics with zero manual context switching.
                    </p>
                </div>

                <!-- Live Agent Heartbeat & Connection Status Card -->
                <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 divide-y sm:divide-y-0 sm:divide-x divide-zinc-100 dark:divide-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50 flex items-center justify-center shrink-0">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-zinc-950 dark:text-white truncate">Workspace MCP Endpoint Active</div>
                                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono font-medium flex items-center gap-1">
                                    <span>🟢 Live &amp; Ready</span>
                                    <span class="text-zinc-400 font-normal">(SSE / JSON-RPC)</span>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3 sm:pt-0 sm:pl-4 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-zinc-950 dark:text-white truncate">External Agent Handshakes</div>
                                <div class="text-[11px] text-zinc-500 font-mono">3 requests in last 24h</div>
                            </div>
                        </div>
                        <div class="pt-3 sm:pt-0 sm:pl-4 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-zinc-950 dark:text-white truncate">Local Gateway Latency</div>
                                <div class="text-[11px] text-zinc-500 font-mono">~14ms local response</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live API Key Injector Bar -->
                <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/60 shadow-2xs space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><path d="M21 2l-2 2m-2-2l2 2m-2 2l2-2m-7 7l7-7-3-3-7 7v3h3z"></path><circle cx="7.5" cy="16.5" r="4.5"></circle></svg>
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Live API Key Injector</span>
                            <span class="text-[10px] text-zinc-400">Auto-interpolates real key into all snippets below</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-zinc-500 font-medium">Select Active Key:</span>
                            <div id="cora-connect-key-pills" class="flex items-center gap-1 overflow-x-auto cora-no-scrollbar">
                                <button type="button" onclick="coraResetActiveKeyDefault()" class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-medium bg-zinc-200/70 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-0 cursor-pointer">Default Key</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <input type="text" id="cora-connect-active-key" value="YOUR_WORKSPACE_LIVE_KEY" oninput="coraInterpolateActiveKey(this.value)" placeholder="Enter or select active workspace key..." class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
                        </div>
                        <button type="button" onclick="coraResetActiveKeyDefault()" class="px-3 py-2 rounded-xl text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 cursor-pointer transition-colors shrink-0">
                            Reset Default
                        </button>
                    </div>
                </div>

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
                    <!-- Dynamic Connection Status Card: Google Gemini -->
                    <div id="cora-gemini-connection-card" class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 border border-cyan-200/60 dark:border-cyan-800/60 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2c.7 4.2 3.8 7.3 8 8-4.2.7-7.3 3.8-8 8-.7-4.2-3.8-7.3-8-8 4.2-.7 7.3-3.8 8-8z"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-zinc-950 dark:text-white">Google Gemini</span>
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
                    </div>

                    <!-- Header -->
                    <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-cyan-600 dark:text-cyan-400 border border-zinc-200/70 dark:border-zinc-700/70 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                    <!-- Gemini Sparkle Vector SVG with Cyan Accent -->
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2c.7 4.2 3.8 7.3 8 8-4.2.7-7.3 3.8-8 8-.7-4.2-3.8-7.3-8-8 4.2-.7 7.3-3.8 8-8z"></path>
                                        <path d="M19 17c.3 1.8 1.7 3.2 3.5 3.5-1.8.3-3.2 1.7-3.5 3.5-.3-1.8-1.7-3.2-3.5-3.5 1.8-.3 3.2-1.7 3.5-3.5z"></path>
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Google Gemini Connected App (MCP)</h3>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 border border-cyan-200/60 dark:border-cyan-800/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                                            <span>1-Click Ready</span>
                                        </span>
                                    </div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">Connect your workspace tools to Gemini with 1 click — seamless authorization just like Google or GitHub OAuth.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Level 01 Segmented Control / Radio Pill Matrix -->
                    <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 select-none" id="cora-gemini-method-tabs">
                        <button type="button" onclick="coraSwitchGeminiSetupMethod('cloud')" id="cora-gemini-tab-cloud" class="cora-gemini-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path></svg>
                            <span>Cloud MCP (Recommended &bull; 1-Click Zero Setup)</span>
                        </button>
                        <button type="button" onclick="coraSwitchGeminiSetupMethod('local')" id="cora-gemini-tab-local" class="cora-gemini-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                            <span>Local MCP (Desktop / Node CLI)</span>
                        </button>
                    </div>

                    <!-- MODE 1: CLOUD MCP PANE -->
                    <div id="cora-gemini-cloud-view" class="space-y-4">
                        <!-- Hero Action CTA -->
                        <button type="button" onclick="coraOpenGeminiConnectedApps()" class="bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold px-4 py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all w-full flex items-center justify-center gap-2 border-0 cursor-pointer shadow-2xs">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            <span>Open Google Gemini Connected Apps (gemini.google.com/apps)</span>
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        </button>

                        <!-- Custom App Link Card -->
                        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 bg-white dark:bg-zinc-900 shadow-2xs space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="cora-gemini-app-link" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">Add a custom app link</label>
                                <span class="text-[11px] font-mono text-zinc-400 dark:text-zinc-500">Auto-Discovery &amp; OAuth 2.0</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" id="cora-gemini-app-link" readonly value="<?php echo esc_url( home_url('/mcp') ); ?>" class="w-full font-mono text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                <button type="button" onclick="coraCopyInputValue('cora-gemini-app-link')" class="px-4 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 text-white rounded-xl text-xs font-bold shrink-0 cursor-pointer border-0 transition-all shadow-2xs active:scale-97 flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                    <span>Copy Link</span>
                                </button>
                            </div>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Paste this exact link into Gemini's &ldquo;Add a custom app link&rdquo; field. Gemini will automatically discover Cora's autonomous tools and OAuth endpoints.
                            </p>
                        </div>

                        <!-- Advanced Settings Collapsible -->
                        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-2xs overflow-hidden">
                            <button type="button" onclick="coraToggleGeminiAdvanced()" class="w-full px-4 py-3 flex items-center justify-between text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white bg-zinc-50/50 dark:bg-zinc-800/30 border-0 cursor-pointer transition-colors">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                    <span>Advanced settings (OAuth Credentials)</span>
                                </div>
                                <svg id="cora-gemini-advanced-chevron" viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div id="cora-gemini-advanced-body" class="hidden p-4 space-y-3 border-t border-zinc-200/70 dark:border-zinc-800">
                                <div class="space-y-1">
                                    <label for="cora-gemini-client-id" class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Client ID</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="cora-gemini-client-id" readonly value="cora_client_ws_<?php echo esc_attr( $agency_id ); ?>" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                        <button type="button" onclick="coraCopyInputValue('cora-gemini-client-id')" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label for="cora-gemini-client-secret" class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Client secret</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="cora-gemini-client-secret" readonly value="cora_gemini_sec_<?php echo esc_attr( $agency_id ); ?>" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                        <button type="button" onclick="coraCopyInputValue('cora-gemini-client-secret')" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                                <div class="pt-1 flex flex-wrap items-center justify-between gap-2">
                                    <button type="button" onclick="coraCopyGeminiCredentials()" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 cursor-pointer transition-colors flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <span>Copy Both Credentials</span>
                                    </button>
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed pt-1">
                                    Gemini supports automatic OAuth discovery. If Gemini prompts for Client ID &amp; Secret under Advanced settings, use these pre-authorized credentials.
                                </p>
                            </div>
                        </div>

                        <!-- Visual 3-Step Walkthrough Card -->
                        <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                                <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">How to connect Gemini in 3 simple steps</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1">
                                    <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[11px] font-bold flex items-center justify-center">1</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">Open Connected Apps</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Open <span class="font-semibold text-zinc-900 dark:text-zinc-100">gemini.google.com/apps</span> and click <span class="font-semibold text-zinc-900 dark:text-zinc-100">Set up a custom connected app</span>.</p>
                                </div>
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1">
                                    <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[11px] font-bold flex items-center justify-center">2</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">Paste App Link</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Select <span class="font-semibold text-zinc-900 dark:text-zinc-100">Cloud MCP</span>, paste the app link above, and click <span class="font-semibold text-zinc-900 dark:text-zinc-100">Next</span>.</p>
                                </div>
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1">
                                    <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[11px] font-bold flex items-center justify-center">3</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">1-Click Authorize</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Click <span class="font-semibold text-zinc-900 dark:text-zinc-100">Grant Access</span> on Cora's authorization screen. You're connected!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODE 2: LOCAL MCP PANE -->
                    <div id="cora-gemini-local-view" class="hidden space-y-4">
                        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-zinc-400"></span>
                                <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Local MCP Server Settings (CLI &amp; Local Node)</h4>
                            </div>

                            <!-- Field 1: Server name -->
                            <div class="space-y-1">
                                <label for="cora-gemini-server-name" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Server name</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="cora-gemini-server-name" readonly value="Cora Workspace" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-semibold">
                                    <button type="button" onclick="coraCopyInputValue('cora-gemini-server-name')" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors">
                                        Copy
                                    </button>
                                </div>
                            </div>

                            <!-- Field 2: Description -->
                            <div class="space-y-1">
                                <label for="cora-gemini-server-desc" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Description</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="cora-gemini-server-desc" readonly value="Autonomous content campaigns and studio operations." class="w-full text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-medium">
                                    <button type="button" onclick="coraCopyInputValue('cora-gemini-server-desc')" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors">
                                        Copy
                                    </button>
                                </div>
                            </div>

                            <!-- Field 3: Command -->
                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Command</label>
                                    <button type="button" onclick="coraCopySnippet('cora-ws-gemini-local-cmd')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white flex items-center gap-1 cursor-pointer border-0 bg-transparent">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        Copy Command
                                    </button>
                                </div>
                                <pre id="cora-ws-gemini-local-cmd" class="p-4 text-xs font-mono text-cyan-300 bg-[#0c0c0e] rounded-xl overflow-x-auto select-all leading-relaxed whitespace-pre-wrap">npx -y @modelcontextprotocol/server-fetch "<?php echo esc_url( home_url('/mcp') ); ?>" --header "Authorization: Bearer YOUR_WORKSPACE_LIVE_KEY"</pre>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                    Uses <code class="font-mono text-[11px]">@modelcontextprotocol/server-fetch</code> to proxy local Gemini desktop agents directly into your Cora workspace.
                                </p>
                            </div>
                        </div>

                        <!-- Developer SDK & Direct SSE Collapsible -->
                        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-2xs overflow-hidden">
                            <button type="button" onclick="coraToggleGeminiSdkSnippet()" class="w-full px-4 py-3 flex items-center justify-between text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white bg-zinc-50/50 dark:bg-zinc-800/30 border-0 cursor-pointer transition-colors">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-cyan-500"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                                    <span>Python SDK (google-genai) &amp; Direct SSE Runtime</span>
                                </div>
                                <svg id="cora-gemini-sdk-chevron" viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div id="cora-gemini-sdk-body" class="hidden p-4 space-y-4 border-t border-zinc-200/70 dark:border-zinc-800">
                                <!-- Python SDK Code -->
                                <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-xl overflow-hidden bg-white dark:bg-zinc-900">
                                    <div class="px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                                        <span class="text-xs font-bold text-zinc-900 dark:text-white">google-genai Python SDK</span>
                                        <button type="button" onclick="coraCopySnippet('cora-ws-gemini-code')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white flex items-center gap-1 cursor-pointer border-0 bg-transparent">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            Copy Code
                                        </button>
                                    </div>
                                    <pre id="cora-ws-gemini-code" class="p-4 text-xs font-mono text-zinc-100 bg-[#0c0c0e] overflow-x-auto select-all leading-relaxed">import os
from google import genai
from google.genai import types

# Initialize Gemini Client
client = genai.Client(api_key=os.environ.get("GEMINI_API_KEY"))

# Cora Workspace MCP Endpoint &amp; Auth Header
workspace_headers = {
    "Authorization": "Bearer YOUR_WORKSPACE_LIVE_KEY",
    "Content-Type": "application/json"
}
mcp_sse_endpoint = "<?php echo esc_url( $mcp_sse_url ); ?>"

# Dispatch autonomous query with live workspace tools
response = client.models.generate_content(
    model="gemini-2.5-flash",
    contents="Audit our latest published content for SEO &amp; GEO score gaps.",
    config=types.GenerateContentConfig(
        tools=[{"url": mcp_sse_endpoint, "headers": workspace_headers}]
    )
)

print(response.text)</pre>
                                </div>

                                <!-- Direct SSE Endpoint -->
                                <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-xl overflow-hidden bg-white dark:bg-zinc-900">
                                    <div class="px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                                        <span class="text-xs font-bold text-zinc-900 dark:text-white">Direct SSE URL Endpoint</span>
                                        <button type="button" onclick="coraCopySnippet('cora-ws-gemini-sse')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white flex items-center gap-1 cursor-pointer border-0 bg-transparent">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            Copy Endpoint
                                        </button>
                                    </div>
                                    <pre id="cora-ws-gemini-sse" class="p-4 text-xs font-mono text-cyan-300 bg-[#0c0c0e] overflow-x-auto select-all leading-relaxed">Endpoint: <?php echo esc_url( $mcp_sse_url ); ?>

Transport: Server-Sent Events (SSE) + JSON-RPC 2.0
Header:    Authorization: Bearer YOUR_WORKSPACE_LIVE_KEY
Security:  Multi-Tenant Scoped Isolation (Workspace ID #<?php echo esc_html( $agency_id ); ?>)</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     ECOSYSTEM 2: CLAUDE (ANTHROPIC) CUSTOM CONNECTOR
                     ============================================================ -->
                <div id="cora-eco-panel-claude" class="cora-eco-panel hidden space-y-4">
                    <!-- Dynamic Connection Status Card: Claude -->
                    <div id="cora-claude-connection-card" class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M12 2v4m0 12v4M2 12h4m12 0h4m-3.17-6.83l-2.83 2.83m-8 8l-2.83 2.83m0-13.66l2.83 2.83m8 8l2.83 2.83"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-zinc-950 dark:text-white">Claude (Anthropic)</span>
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
                    </div>

                    <!-- Header -->
                    <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M12 2v4m0 12v4M2 12h4m12 0h4m-3.17-6.83l-2.83 2.83m-8 8l-2.83 2.83m0-13.66l2.83 2.83m8 8l2.83 2.83"></path>
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Claude Custom Connector (MCP)</h3>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>1-Click Ready</span>
                                        </span>
                                    </div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">Connect Claude to your workspace tools and data in 1 click.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Level 01 Segmented Control / Radio Pill Matrix -->
                    <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 select-none" id="cora-claude-method-tabs">
                        <button type="button" onclick="coraSwitchClaudeSetupMethod('cloud')" id="cora-claude-tab-cloud" class="cora-claude-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path></svg>
                            <span>Cloud Connector (claude.ai) &mdash; Recommended</span>
                        </button>
                        <button type="button" onclick="coraSwitchClaudeSetupMethod('desktop')" id="cora-claude-tab-desktop" class="cora-claude-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                            <span>Desktop &amp; CLI (claude_desktop_config.json)</span>
                        </button>
                    </div>

                    <!-- MODE 1: CLAUDE CLOUD CONNECTOR VIEW (Image 4 Parity) -->
                    <div id="cora-claude-cloud-view" class="space-y-4">
                        <!-- Hero Action CTA -->
                        <button type="button" onclick="coraOpenClaudeConnectors()" class="bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold px-4 py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all w-full flex items-center justify-center gap-2 border-0 cursor-pointer shadow-2xs">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            <span>Open Claude Custom Connectors (claude.ai/settings/connectors)</span>
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        </button>

                        <!-- Add Custom Connector Dialog Mirror Card -->
                        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 bg-white dark:bg-zinc-900 shadow-2xs space-y-4">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Add Custom Connector Specification</h4>
                                    <span class="text-[11px] font-mono text-zinc-400">claude.ai &rarr; Settings &rarr; Connectors</span>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">
                                    Connect Claude to your data and tools. Paste these exact values into Claude's &ldquo;Add custom connector&rdquo; modal.
                                </p>
                            </div>

                            <!-- Field 1: Name -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="cora-claude-name" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">Name</label>
                                    <span class="text-[10px] text-zinc-400">Required</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="cora-claude-name" readonly value="Cora Workspace" class="w-full font-mono text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                    <button type="button" onclick="coraCopyInputValue('cora-claude-name')" class="px-3.5 py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <span>Copy</span>
                                    </button>
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Shown in the connectors list.</p>
                            </div>

                            <!-- Field 2: MCP server URL -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="cora-claude-app-link" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">MCP server URL</label>
                                    <span class="text-[10px] text-zinc-400">HTTPS Protocol</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="cora-claude-app-link" readonly value="<?php echo esc_url( home_url('/mcp') ); ?>" class="w-full font-mono text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                    <button type="button" onclick="coraCopyInputValue('cora-claude-app-link')" class="px-4 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 text-white rounded-xl text-xs font-bold shrink-0 cursor-pointer border-0 transition-all shadow-2xs active:scale-97 flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <span>Copy Link</span>
                                    </button>
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    The HTTPS address where the server accepts MCP requests, for example <code class="font-mono text-[11px]"><?php echo esc_url( home_url('/mcp') ); ?></code>.
                                </p>
                            </div>

                            <!-- Anthropic Security Note Mirror (from Image 4) -->
                            <div class="p-3.5 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/60 dark:border-zinc-800 text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Only use connectors from developers you trust. Anthropic does not control which tools developers make available and cannot verify that they will work as intended or that they won't change.
                            </div>
                        </div>

                        <!-- Visual 3-Step Walkthrough Card -->
                        <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">How to connect Claude in 3 simple steps</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1">
                                    <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[11px] font-bold flex items-center justify-center">1</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">Open Connectors</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Open <span class="font-semibold text-zinc-900 dark:text-zinc-100">claude.ai</span> &rarr; Settings &rarr; <span class="font-semibold text-zinc-900 dark:text-zinc-100">Connectors</span>.</p>
                                </div>
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1">
                                    <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[11px] font-bold flex items-center justify-center">2</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">Add Connector</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Click <span class="font-semibold text-zinc-900 dark:text-zinc-100">Add custom connector</span>.</p>
                                </div>
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1">
                                    <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[11px] font-bold flex items-center justify-center">3</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">Paste &amp; Continue</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Paste <span class="font-semibold text-zinc-900 dark:text-zinc-100">Name</span> &amp; <span class="font-semibold text-zinc-900 dark:text-zinc-100">MCP server URL</span>, then click Continue.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODE 2: CLAUDE DESKTOP & CLI VIEW -->
                    <div id="cora-claude-desktop-view" class="hidden space-y-4">
                        <!-- Config Card 1: claude_desktop_config.json -->
                        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                            <div class="px-5 py-3 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span class="text-xs font-bold text-zinc-900 dark:text-white">Claude Desktop (<code class="text-[11px] font-mono">claude_desktop_config.json</code>)</span>
                                </div>
                                <button type="button" onclick="coraCopySnippet('cora-ws-claude-cfg')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white flex items-center gap-1.5 cursor-pointer border-0 bg-transparent">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                    Copy Config
                                </button>
                            </div>
                            <pre id="cora-ws-claude-cfg" class="p-5 text-xs font-mono text-zinc-100 bg-[#0c0c0e] overflow-x-auto select-all leading-relaxed">{
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

                        <!-- Config Card 2: Claude Code CLI -->
                        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                            <div class="px-5 py-3 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-amber-600 dark:text-amber-400"><polyline points="4 17 10 11 4 5"></polyline><line x1="12" y1="19" x2="20" y2="19"></line></svg>
                                    <span class="text-xs font-bold text-zinc-900 dark:text-white">Claude Code CLI Command (<code class="text-[11px] font-mono">claude mcp add</code>)</span>
                                </div>
                                <button type="button" onclick="coraCopySnippet('cora-ws-claude-cli')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white flex items-center gap-1.5 cursor-pointer border-0 bg-transparent">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                    Copy Command
                                </button>
                            </div>
                            <pre id="cora-ws-claude-cli" class="p-5 text-xs font-mono text-amber-300 bg-[#0c0c0e] overflow-x-auto select-all leading-relaxed">claude mcp add cora-workspace -- npx -y @modelcontextprotocol/server-fetch "<?php echo esc_url( $mcp_server_url ); ?>" --header "Authorization: Bearer YOUR_WORKSPACE_LIVE_KEY"</pre>
                        </div>

                        <div class="p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-xs text-zinc-600 dark:text-zinc-400 flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 mt-1.5 shrink-0"></span>
                            <div class="leading-relaxed">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">Setup Location:</span> On macOS, place configuration in <code class="font-mono text-[11px]">~/Library/Application Support/Claude/claude_desktop_config.json</code>. On Windows, locate <code class="font-mono text-[11px]">%APPDATA%\Claude\claude_desktop_config.json</code>. Restart Claude Desktop after updating.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     ECOSYSTEM 3: CHATGPT (OPENAI) CUSTOM MCP SERVER
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

                    <!-- Header -->
                    <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="8"></circle>
                                        <path d="M12 8a4 4 0 0 1 4 4m-4 0a4 4 0 0 1-4-4m4 4a4 4 0 0 1 0 4m0-4a4 4 0 0 0-4 4"></path>
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-bold text-zinc-950 dark:text-white">ChatGPT Custom MCP Server</h3>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>1-Click Ready</span>
                                        </span>
                                    </div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">Connect ChatGPT directly to Cora's autonomous tools and campaigns.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Level 01 Segmented Control / Radio Pill Matrix -->
                    <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 select-none" id="cora-chatgpt-method-tabs">
                        <button type="button" onclick="coraSwitchChatGptSetupMethod('mcp')" id="cora-chatgpt-tab-mcp" class="cora-chatgpt-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect></svg>
                            <span>Custom MCP Server (Recommended &bull; 1-Click Setup)</span>
                        </button>
                        <button type="button" onclick="coraSwitchChatGptSetupMethod('action')" id="cora-chatgpt-tab-action" class="cora-chatgpt-tab-btn flex-1 py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                            <span>Custom GPT Action (OpenAPI 3.1)</span>
                        </button>
                    </div>

                    <!-- MODE 1: CHATGPT CUSTOM MCP SERVER VIEW (Image 3 Parity) -->
                    <div id="cora-chatgpt-mcp-view" class="space-y-4">
                        <!-- Hero Action CTA -->
                        <button type="button" onclick="coraOpenChatGptConnectors()" class="bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold px-4 py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all w-full flex items-center justify-center gap-2 border-0 cursor-pointer shadow-2xs">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            <span>Open ChatGPT Custom Connectors (chatgpt.com)</span>
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        </button>

                        <!-- Add Custom MCP Server Dialog Mirror Card -->
                        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 bg-white dark:bg-zinc-900 shadow-2xs space-y-4">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Add Custom MCP Server Specification</h4>
                                    <span class="text-[11px] font-mono text-zinc-400">chatgpt.com &rarr; Settings &rarr; Connectors</span>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">
                                    Connect ChatGPT directly to Cora's autonomous tools and studio operations via MCP.
                                </p>
                            </div>

                            <!-- Field 0: Icon (optional) -->
                            <div class="space-y-1.5">
                                <label for="cora-chatgpt-icon-url" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">Icon (optional)</label>
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="cora-chatgpt-icon-url" readonly value="<?php echo esc_url( home_url('/favicon.png') ); ?>" class="w-full font-mono text-xs px-3.5 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                            <button type="button" onclick="coraCopyInputValue('cora-chatgpt-icon-url')" class="px-3.5 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors flex items-center gap-1.5">
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                <span>Copy</span>
                                            </button>
                                        </div>
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">PNG only. Best results at 256 x 256 px or larger. Max file size: 10 KB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Field 1: Name -->
                            <div class="space-y-1.5">
                                <label for="cora-chatgpt-name" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">Name</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="cora-chatgpt-name" readonly value="Cora Workspace" class="w-full font-mono text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                    <button type="button" onclick="coraCopyInputValue('cora-chatgpt-name')" class="px-3.5 py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <span>Copy</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Field 2: Description (optional) -->
                            <div class="space-y-1.5">
                                <label for="cora-chatgpt-desc" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">Description (optional)</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="cora-chatgpt-desc" readonly value="Autonomous content campaigns and studio operations." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-medium">
                                    <button type="button" onclick="coraCopyInputValue('cora-chatgpt-desc')" class="px-3.5 py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <span>Copy</span>
                                    </button>
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Explain what it does in a few words</p>
                            </div>

                            <!-- Field 3: Connection -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="cora-chatgpt-app-link" class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">Connection</label>
                                    <div class="p-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg border border-zinc-200/70 dark:border-zinc-700 flex items-center gap-0.5 text-[10px]">
                                        <span class="px-2 py-0.5 rounded-md bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-bold shadow-2xs">Server URL</span>
                                        <span class="px-2 py-0.5 text-zinc-400">Tunnel</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="cora-chatgpt-app-link" readonly value="<?php echo esc_url( home_url('/mcp') ); ?>" class="w-full font-mono text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                    <button type="button" onclick="coraCopyInputValue('cora-chatgpt-app-link')" class="px-4 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 text-white rounded-xl text-xs font-bold shrink-0 cursor-pointer border-0 transition-all shadow-2xs active:scale-97 flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <span>Copy URL</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Field 4: Authentication -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-zinc-900 dark:text-zinc-100">Authentication</label>
                                <input type="text" readonly value="OAuth (Auto-Discovered RFC 8414)" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-semibold">
                            </div>

                            <!-- Field 5: Collapsible Advanced OAuth settings (from Image 3) -->
                            <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 shadow-2xs overflow-hidden">
                                <button type="button" onclick="coraToggleChatGptAdvanced()" class="w-full px-4 py-3 flex items-center justify-between text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white bg-zinc-50/50 dark:bg-zinc-800/30 border-0 cursor-pointer transition-colors">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-zinc-900 dark:text-zinc-100">Advanced OAuth settings</div>
                                        <div class="text-[11px] text-zinc-500 font-normal">Review discovered OAuth settings, or enter them manually, then choose a client setup method and configure default scopes</div>
                                    </div>
                                    <svg id="cora-chatgpt-advanced-chevron" viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 transition-transform duration-200 shrink-0 ml-2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div id="cora-chatgpt-advanced-body" class="hidden p-4 space-y-3 border-t border-zinc-200/70 dark:border-zinc-800">
                                    <div class="space-y-1">
                                        <label for="cora-chatgpt-client-id" class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Client ID</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="cora-chatgpt-client-id" readonly value="cora_client_ws_<?php echo esc_attr( $agency_id ); ?>" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                            <button type="button" onclick="coraCopyInputValue('cora-chatgpt-client-id')" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors">
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="cora-chatgpt-client-secret" class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Client Secret</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="cora-chatgpt-client-secret" readonly value="cora_chatgpt_sec_<?php echo esc_attr( $agency_id ); ?>" class="w-full font-mono text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                            <button type="button" onclick="coraCopyInputValue('cora-chatgpt-client-secret')" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-semibold shrink-0 cursor-pointer border border-zinc-200 dark:border-zinc-700 transition-colors">
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-1 flex flex-wrap items-center justify-between gap-2">
                                        <button type="button" onclick="coraCopyChatGptCredentials()" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 cursor-pointer transition-colors flex items-center gap-1.5">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            <span>Copy Both Credentials</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Risk & Security Warning (from Image 3) -->
                            <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs space-y-2">
                                <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400 font-bold">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                    <span>Custom MCP servers introduce risk.</span>
                                </div>
                                <p class="text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    Only connect to MCP servers you trust. Cora provides multi-tenant isolated tokens ensuring ChatGPT access is strictly scoped to your workspace.
                                </p>
                            </div>
                        </div>

                        <!-- Visual 3-Step Walkthrough Card -->
                        <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">How to connect ChatGPT in 3 simple steps</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1">
                                    <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[11px] font-bold flex items-center justify-center">1</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">Open Connectors</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Open <span class="font-semibold text-zinc-900 dark:text-zinc-100">chatgpt.com</span> &rarr; Settings &rarr; <span class="font-semibold text-zinc-900 dark:text-zinc-100">Connectors</span>.</p>
                                </div>
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1">
                                    <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[11px] font-bold flex items-center justify-center">2</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">Add MCP Server</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Click <span class="font-semibold text-zinc-900 dark:text-zinc-100">Add custom MCP server</span>.</p>
                                </div>
                                <div class="p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-1">
                                    <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[11px] font-bold flex items-center justify-center">3</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">Paste &amp; Continue</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Paste <span class="font-semibold text-zinc-900 dark:text-zinc-100">Name</span> &amp; <span class="font-semibold text-zinc-900 dark:text-zinc-100">Server URL</span>, then click Continue.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODE 2: CUSTOM GPT ACTION VIEW (OpenAPI 3.1) -->
                    <div id="cora-chatgpt-action-view" class="hidden space-y-4">
                        <!-- Step-by-Step Custom GPT Guide -->
                        <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <h4 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Custom GPT 3-Step Setup Instructions</h4>
                            </div>
                            <ol class="space-y-2 text-xs text-zinc-600 dark:text-zinc-400 list-decimal list-inside leading-relaxed">
                                <li>In ChatGPT, open <span class="font-bold text-zinc-900 dark:text-zinc-100">Explore GPTs</span> &rarr; click <span class="font-bold text-zinc-900 dark:text-zinc-100">+ Create</span> &rarr; select the <span class="font-bold text-zinc-900 dark:text-zinc-100">Configure</span> tab.</li>
                                <li>Scroll to <span class="font-bold text-zinc-900 dark:text-zinc-100">Actions</span> and click <span class="font-bold text-zinc-900 dark:text-zinc-100">Create new action</span>.</li>
                                <li>In the Schema box, click <span class="font-bold text-zinc-900 dark:text-zinc-100">Import from URL</span> and paste the Direct Import URL below (or click "Copy OpenAPI 3.1 Schema").</li>
                                <li>Under <span class="font-bold text-zinc-900 dark:text-zinc-100">Authentication</span>, select <span class="font-bold text-zinc-900 dark:text-zinc-100">API Key</span> &rarr; Auth Type: <span class="font-bold text-zinc-900 dark:text-zinc-100">Bearer</span> &rarr; enter your Workspace Live Key.</li>
                            </ol>
                        </div>

                        <!-- Direct Import URL Bar -->
                        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 bg-white dark:bg-zinc-900 shadow-2xs space-y-2">
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Direct OpenAPI Import URL for ChatGPT Action:</label>
                            <div class="flex items-center gap-2">
                                <input type="text" id="cora-ws-openapi-url" readonly value="<?php echo esc_url( $openapi_url ); ?>" class="w-full font-mono text-xs px-3 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 select-all font-semibold">
                                <button type="button" onclick="coraCopyInputValue('cora-ws-openapi-url')" class="px-4 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 text-white rounded-xl text-xs font-bold shrink-0 cursor-pointer border-0 transition-all shadow-2xs active:scale-97">
                                    Copy URL
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons: Download Schema & Copy Schema -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            <button type="button" onclick="coraDownloadOpenAPISchema()" class="px-4 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold cursor-pointer transition-all active:scale-97 border-0 shadow-2xs flex items-center gap-2">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Download OpenAPI 3.1 Schema</span>
                            </button>
                            <button type="button" onclick="coraCopyOpenAPISchema()" class="px-4 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-xs font-semibold cursor-pointer transition-all active:scale-97 border border-zinc-200/80 dark:border-zinc-700 flex items-center gap-2">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                <span>Copy Schema JSON</span>
                            </button>
                        </div>

                        <!-- Schema Preview Card -->
                        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                            <div class="px-5 py-3 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                                <span class="text-xs font-bold text-zinc-900 dark:text-white">OpenAPI 3.1 Specification Preview</span>
                                <span class="text-[10px] font-mono text-zinc-400">cora-openapi-3.1.json</span>
                            </div>
                            <pre id="cora-ws-openapi-preview" class="p-5 text-xs font-mono text-zinc-300 bg-[#0c0c0e] overflow-x-auto max-h-60 select-all leading-relaxed">{
  "openapi": "3.1.0",
  "info": {
    "title": "Cora Workspace Multi-Model Gateway API",
    "version": "4.9.386",
    "description": "Workspace-level actions for Content, SEO Audits, Leads, and CRM"
  },
  "servers": [
    { "url": "<?php echo esc_url( home_url( '/wp-json' ) ); ?>" }
  ],
  "paths": {
    "/cora/v1/content": { "get": { "operationId": "coraListArticles" } },
    "/cora/v1/content/{id}/seo-audit": { "post": { "operationId": "coraAuditArticleSeo" } },
    "/cora/v1/content/opportunities": { "get": { "operationId": "coraListContentOpportunities" } }
  }
}</pre>
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
- Capabilities: Content drafting, 11-point SEO/GEO audits, CRM pipeline, performance metrics
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
                 TAB 3: UNIVERSAL WORKSPACE MCP TOOLS
                 ============================================================ -->
            <div id="cora-ws-pane-tools" class="cora-ws-pane hidden space-y-5">
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Universal Tools Exposed at Workspace Level</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">18 Canonical Tools</span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Connecting your workspace provides your external AI with cross-module tools under a unified security perimeter:
                    </p>
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
                        <span class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">CRM, Clients &amp; Analytics (6 Tools)</span>
                        <span class="text-[10px] font-mono text-zinc-500">Live</span>
                    </div>
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_list_leads</code> <span class="text-zinc-500 ml-2">Retrieve CRM pipeline leads and stages</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">crm:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_create_lead</code> <span class="text-zinc-500 ml-2">Ingest new lead from external channels</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">crm:write</span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
                            <div><code class="font-bold text-zinc-900 dark:text-zinc-100">cora_list_clients</code> <span class="text-zinc-500 ml-2">Retrieve studio client directory</span></div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">clients:read</span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
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
                                <button type="button" onclick="coraSelectSandboxEndpoint('/wp-json/cora/v1/content?limit=3', this)" class="sbx-ep-btn px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer">
                                    /content (Articles)
                                </button>
                                <button type="button" onclick="coraSelectSandboxEndpoint('/wp-json/cora/v1/content/opportunities', this)" class="sbx-ep-btn px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white border-0 bg-transparent cursor-pointer">
                                    /opportunities (SEO Clusters)
                                </button>
                            </div>
                            <input type="hidden" id="cora-sandbox-selected-endpoint" value="/wp-json/cora/v1/content?limit=3">
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

        if (defaultTab) {
            coraSwitchWorkspaceGwTab(defaultTab);
        }

        if (typeof coraRefreshWorkspaceKeys === 'function') coraRefreshWorkspaceKeys();
        if (typeof coraInterpolateActiveKey === 'function') coraInterpolateActiveKey(_currentKey);
        if (typeof coraLoadCampaigns === 'function') coraLoadCampaigns();
        if (typeof window.coraCheckEcosystemsStatus === 'function') {
            window.coraCheckEcosystemsStatus();
        } else if (typeof coraCheckEcosystemsStatus === 'function') {
            coraCheckEcosystemsStatus();
        }
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
        const urlInput = document.getElementById('cora-chatgpt-server-url');
        const serverUrl = urlInput ? urlInput.value : '<?php echo esc_js( home_url('/mcp') ); ?>';
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(serverUrl);
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
        const tabAction = document.getElementById('cora-chatgpt-tab-action');
        const viewMcp = document.getElementById('cora-chatgpt-mcp-view');
        const viewAction = document.getElementById('cora-chatgpt-action-view');

        const activeClasses = ['bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold'];
        const inactiveClasses = ['text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent'];

        if (method === 'action') {
            if (tabMcp) {
                tabMcp.classList.remove(...activeClasses);
                tabMcp.classList.add(...inactiveClasses);
            }
            if (tabAction) {
                tabAction.classList.remove(...inactiveClasses);
                tabAction.classList.add(...activeClasses);
            }
            if (viewMcp) viewMcp.classList.add('hidden');
            if (viewAction) viewAction.classList.remove('hidden');
        } else {
            if (tabAction) {
                tabAction.classList.remove(...activeClasses);
                tabAction.classList.add(...inactiveClasses);
            }
            if (tabMcp) {
                tabMcp.classList.remove(...inactiveClasses);
                tabMcp.classList.add(...activeClasses);
            }
            if (viewAction) viewAction.classList.add('hidden');
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

    // OpenAPI 3.1 Schema Spec for ChatGPT Action
    const _openApiSchemaObj = {
        openapi: "3.1.0",
        info: {
            title: "Cora Workspace Multi-Model Gateway API",
            version: "<?php echo defined( 'CORA_WORKSPACE_VERSION' ) ? esc_js( CORA_WORKSPACE_VERSION ) : '4.9.386'; ?>",
            description: "Unified AI MCP & REST Gateway for Workspace Content, Leads, SEO Audits, and CRM Actions."
        },
        servers: [
            { url: "<?php echo esc_js( home_url( '/wp-json' ) ); ?>", description: "Cora Workspace Local Server" }
        ],
        paths: {
            "/cora/v1/content": {
                get: {
                    operationId: "coraListArticles",
                    summary: "Search and paginate workspace articles",
                    parameters: [
                        { name: "search", in: "query", schema: { type: "string" } },
                        { name: "status", in: "query", schema: { type: "string", default: "publish" } },
                        { name: "limit", in: "query", schema: { type: "integer", default: 10 } }
                    ],
                    responses: { "200": { description: "List of articles with SEO metrics" } }
                },
                post: {
                    operationId: "coraCreateArticle",
                    summary: "Draft or publish article with SEO/GEO metadata",
                    requestBody: {
                        required: true,
                        content: {
                            "application/json": {
                                schema: {
                                    type: "object",
                                    properties: {
                                        title: { type: "string" },
                                        content: { type: "string" },
                                        status: { type: "string", enum: ["draft", "publish"] },
                                        target_keyword: { type: "string" }
                                    },
                                    required: ["title", "content"]
                                }
                            }
                        }
                    },
                    responses: { "200": { description: "Article creation response" } }
                }
            },
            "/cora/v1/content/{id}": {
                get: {
                    operationId: "coraGetArticle",
                    summary: "Fetch article content with 11-point SEO & GEO scores",
                    parameters: [{ name: "id", in: "path", required: true, schema: { type: "integer" } }],
                    responses: { "200": { description: "Article details object" } }
                }
            },
            "/cora/v1/content/{id}/seo-audit": {
                post: {
                    operationId: "coraAuditArticleSeo",
                    summary: "Execute 11-point algorithmic SEO & GEO score audit",
                    parameters: [{ name: "id", in: "path", required: true, schema: { type: "integer" } }],
                    responses: { "200": { description: "Algorithmic audit breakdown" } }
                }
            },
            "/cora/v1/content/opportunities": {
                get: {
                    operationId: "coraListContentOpportunities",
                    summary: "Retrieve high-intent keyword gaps and search volumes",
                    responses: { "200": { description: "List of keyword clusters" } }
                }
            },
            "/cora/v1/leads": {
                get: {
                    operationId: "coraListLeads",
                    summary: "List CRM leads and pipeline stages",
                    responses: { "200": { description: "CRM leads array" } }
                },
                post: {
                    operationId: "coraCreateLead",
                    summary: "Ingest new CRM lead from external AI interactions",
                    requestBody: {
                        required: true,
                        content: {
                            "application/json": {
                                schema: {
                                    type: "object",
                                    properties: {
                                        name: { type: "string" },
                                        email: { type: "string" },
                                        phone: { type: "string" },
                                        notes: { type: "string" }
                                    },
                                    required: ["name"]
                                }
                            }
                        }
                    },
                    responses: { "200": { description: "Lead ingestion status" } }
                }
            }
        },
        components: {
            securitySchemes: {
                BearerAuth: {
                    type: "http",
                    scheme: "bearer",
                    bearerFormat: "JWT/ApiKey",
                    description: "Workspace Live API Key formatted as: Bearer cora_ws_live_..."
                }
            }
        },
        security: [{ BearerAuth: [] }]
    };

    window.coraDownloadOpenAPISchema = function() {
        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(_openApiSchemaObj, null, 2));
        const downloadAnchor = document.createElement('a');
        downloadAnchor.setAttribute("href", dataStr);
        downloadAnchor.setAttribute("download", "cora-openapi-3.1.json");
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        downloadAnchor.remove();
        if (window.coraShowToast) window.coraShowToast('OpenAPI 3.1 Schema downloaded.', 'success');
    };

    window.coraCopyOpenAPISchema = function() {
        navigator.clipboard.writeText(JSON.stringify(_openApiSchemaObj, null, 2));
        if (window.coraShowToast) window.coraShowToast('OpenAPI 3.1 Schema copied to clipboard.', 'success');
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

        let html = '';
        keys.forEach(k => {
            const isRevoked = k.status === 'revoked';
            const statusBadge = isRevoked 
                ? '<span class="px-2 py-0.5 rounded text-[10px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-500">Revoked</span>'
                : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active</span>';

            const scopesList = Array.isArray(k.scopes) ? k.scopes.join(' • ') : (k.scopes || 'workspace:full');

            html += `
            <div class="p-4 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
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

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="coraSelectActiveKeyForConfig('${escapeHtml((k.key_prefix || '').replace(/\.+$/, ''))}')" class="px-3 py-1.5 rounded-lg border border-zinc-200/80 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-semibold text-zinc-700 dark:text-zinc-200 hover:text-black dark:hover:text-white cursor-pointer transition-colors" title="Inject into AI Configurations">
                        Inject Key
                    </button>
                    <button type="button" onclick="coraCopySnippetDirect('${escapeHtml(k.key_prefix || '')}')" class="px-3 py-1.5 rounded-lg border border-zinc-200/80 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-semibold text-zinc-700 dark:text-zinc-200 hover:text-black dark:hover:text-white cursor-pointer transition-colors" title="Copy Key Prefix">
                        Copy Prefix
                    </button>
                    ${!isRevoked ? `
                    <button type="button" onclick="coraRevokeWorkspaceKey(${k.id})" class="px-3 py-1.5 rounded-lg border border-zinc-200/80 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/30 cursor-pointer transition-colors" title="Revoke this key immediately">
                        Revoke
                    </button>` : ''}
                </div>
            </div>`;
        });

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
