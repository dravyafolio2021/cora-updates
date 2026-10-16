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

$agency_id = function_exists( 'cora_get_current_tenant_agency_id' ) ? cora_get_current_tenant_agency_id() : 0;
$mcp_server_url = home_url( '/wp-json/cora-mcp/v1' );
$rest_base_url  = home_url( '/wp-json/cora/v1' );
?>

<aside id="cora-workspace-developer-gateway-drawer" class="cora-docked-drawer collapsed" aria-label="Workspace Developer API and MCP Gateway">
    <!-- Top Header Bar (3-Part Alignment) -->
    <div class="px-4 sm:px-6 py-2.5 sm:py-3 border-b border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
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
                 TAB 2: CONNECT AI AGENT (CLAUDE DESKTOP & CURSOR)
                 ============================================================ -->
            <div id="cora-ws-pane-connect" class="cora-ws-pane hidden space-y-5">
                <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2">
                    <h3 class="text-sm font-bold text-zinc-950 dark:text-white">Connect Workspace Account to AI in 30 Seconds</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Add the configuration below to your AI client. Once connected, your AI assistant can browse your content pipeline, create drafts, execute 11-point SEO/GEO audits, manage leads, and retrieve metrics with zero manual context switching.
                    </p>
                </div>

                <!-- Config Card 1: Claude Desktop -->
                <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                    <div class="px-5 py-3 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
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

                <!-- Config Card 2: Cursor AI -->
                <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 shadow-2xs">
                    <div class="px-5 py-3 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
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
</aside>

<script>
(function() {
    let _activeTab = 'keys';
    let _activeScope = 'full';
    let _wsApiKeys = [];

    // Global opener
    window.coraOpenWorkspaceDeveloperGateway = function(defaultTab) {
        const drawer = document.getElementById('cora-workspace-developer-gateway-drawer');
        if (!drawer) {
            console.error("Workspace Developer Gateway Drawer not found in DOM");
            return;
        }

        // Dynamically compute exact available main canvas space
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

        coraRefreshWorkspaceKeys();
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
})();
</script>
