<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$agency_id = cora_db_get_agency_id();
$table = $wpdb->prefix . 'cora_rag_knowledge';

// Retrieve resources
$resources = array();
if ( cora_table_exists( $table ) ) {
    $resources = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE agency_id = %d ORDER BY id DESC", $agency_id ), ARRAY_A ) ?: array();
}

// Calculate telemetry metrics
$total_resources = count( $resources );
$total_tokens = 0;
$crm_count = 0;
$fin_count = 0;
$ops_count = 0;
$vault_count = 0;
$article_count = 0;
$other_count = 0;

foreach ( $resources as $res ) {
    $total_tokens += intval( $res['token_count'] );
    $st = $res['source_type'] ?? '';
    if ( $st === 'crm' ) {
        $crm_count++;
    } elseif ( $st === 'financials' ) {
        $fin_count++;
    } elseif ( $st === 'operations' ) {
        $ops_count++;
    } elseif ( $st === 'vault' ) {
        $vault_count++;
    } elseif ( in_array( $st, array( 'article', 'url', 'text' ) ) ) {
        $article_count++;
    } else {
        $other_count++;
    }
}

$quota = cora_get_agency_quota( $agency_id, 'rag_token_quota' );
$usage_percent = $quota > 0 ? min( 100, round( ($total_tokens / $quota) * 100, 1 ) ) : 0;

// Fetch sync settings
$sync_interval = get_option( "cora_rag_sync_interval_{$agency_id}", 'daily' );
$sync_budget_pct = intval( get_option( "cora_rag_sync_budget_{$agency_id}", 20 ) );
$last_sync_at = get_option( "cora_rag_last_sync_at_{$agency_id}", '' );
$sync_history = get_option( "cora_rag_sync_history_{$agency_id}", array() );
?>

<!-- Full-Width RAG Knowledge Base Container -->
<div class="space-y-6 w-full">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Contextually enriches Cora Studio AI and connects daily workspace events to your autonomous second brain.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openRagResourceDrawer(0)" class="inline-flex items-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all cursor-pointer shadow-xs">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Resource
            </button>
        </div>
    </div>

    <!-- 4-Card Wide Telemetry Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
        <!-- Card 1: Total Indexed Fragments -->
        <div class="p-5 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-2xl flex flex-col justify-between shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Indexed Fragments</span>
                <span class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-black text-zinc-900 dark:text-zinc-100 font-mono tracking-tight"><?php echo number_format($total_resources); ?></h3>
                <p class="text-[10px] text-zinc-400 mt-0.5">Live items powering AI queries & MCP</p>
            </div>
        </div>

        <!-- Card 2: Token Quota & Capacity Bar -->
        <div class="p-5 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-2xl flex flex-col justify-between shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Memory Quota</span>
                <span class="text-[10px] font-bold font-mono px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-md">
                    <?php echo $usage_percent; ?>% Used
                </span>
            </div>
            <div class="mt-3 space-y-1.5">
                <div class="flex justify-between text-xs font-bold text-zinc-900 dark:text-zinc-100 font-mono">
                    <span><?php echo number_format($total_tokens); ?> <span class="text-zinc-400 font-normal">tokens</span></span>
                    <span class="text-zinc-400 font-normal">/ <?php echo number_format($quota); ?></span>
                </div>
                <div class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-zinc-950 dark:bg-zinc-100 rounded-full transition-all duration-500" style="width: <?php echo max(1, $usage_percent); ?>%;"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Memory Distribution Channels -->
        <div class="p-5 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-2xl flex flex-col justify-between shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Domain Vectors</span>
                <span class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </span>
            </div>
            <div class="grid grid-cols-4 gap-1 text-center mt-2">
                <div class="bg-zinc-50 dark:bg-zinc-950 p-1.5 rounded-lg border border-zinc-150 dark:border-zinc-800">
                    <span class="block text-[9px] text-zinc-400 font-bold">CRM</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 font-mono"><?php echo $crm_count; ?></span>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-950 p-1.5 rounded-lg border border-zinc-150 dark:border-zinc-800">
                    <span class="block text-[9px] text-zinc-400 font-bold">FIN</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 font-mono"><?php echo $fin_count; ?></span>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-950 p-1.5 rounded-lg border border-zinc-150 dark:border-zinc-800">
                    <span class="block text-[9px] text-zinc-400 font-bold">OPS</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 font-mono"><?php echo $ops_count; ?></span>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-950 p-1.5 rounded-lg border border-zinc-150 dark:border-zinc-800">
                    <span class="block text-[9px] text-zinc-400 font-bold">DOCS</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 font-mono"><?php echo ($vault_count + $article_count); ?></span>
                </div>
            </div>
        </div>

        <!-- Card 4: Continuous Learning Sync -->
        <div class="p-5 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-2xl flex flex-col justify-between shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Learning Sync</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Active
                </span>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 block">Daily Automated Sweep</span>
                    <span class="text-[10px] text-zinc-400">Budget: <?php echo $sync_budget_pct; ?>% quota</span>
                </div>
                <button onclick="triggerImmediateSync(this)" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-[11px] font-bold rounded-lg transition-colors cursor-pointer">
                    Sync
                </button>
            </div>
        </div>
    </div>

    <!-- Interactive Search, Filter Tabs, and Data Table Container -->
    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-2xl shadow-xs overflow-hidden w-full">
        
        <!-- Table Toolbar & Category Filter Tabs -->
        <div class="px-5 py-4 border-b border-zinc-150 dark:border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-zinc-50/40 dark:bg-zinc-950/40">
            
            <!-- Left: Search Box -->
            <div class="relative max-w-sm w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="rag-table-search" oninput="filterRagResourcesTable()" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-xl pl-9 pr-3 py-2 text-xs bg-white dark:bg-zinc-900 focus:border-zinc-400 dark:focus:border-zinc-600 outline-none text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400" placeholder="Search knowledge fragments, deals, invoices, rules...">
            </div>

            <!-- Middle: Category Filter Pills -->
            <div class="flex flex-wrap gap-1.5 text-xs font-semibold">
                <button type="button" onclick="coraFilterRAGCategory('all')" id="rag-cat-all" class="px-3 py-1 rounded-lg bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 text-[11px] font-bold cursor-pointer transition-all">
                    All (<?php echo $total_resources; ?>)
                </button>
                <button type="button" onclick="coraFilterRAGCategory('crm')" id="rag-cat-crm" class="px-3 py-1 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 text-[11px] cursor-pointer transition-all">
                    CRM & Leads
                </button>
                <button type="button" onclick="coraFilterRAGCategory('financials')" id="rag-cat-financials" class="px-3 py-1 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 text-[11px] cursor-pointer transition-all">
                    Financials
                </button>
                <button type="button" onclick="coraFilterRAGCategory('operations')" id="rag-cat-operations" class="px-3 py-1 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 text-[11px] cursor-pointer transition-all">
                    Bookings & Tasks
                </button>
                <button type="button" onclick="coraFilterRAGCategory('vault')" id="rag-cat-vault" class="px-3 py-1 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 text-[11px] cursor-pointer transition-all">
                    Vault & Documents
                </button>
            </div>

            <!-- Right: Count indicator -->
            <div class="text-[11px] text-zinc-400 font-mono shrink-0">
                Showing <span id="rag-showing-count" class="font-bold text-zinc-800 dark:text-zinc-200"><?php echo count($resources); ?></span> of <span id="rag-total-count"><?php echo count($resources); ?></span>
            </div>
        </div>

        <!-- Notion-Style Wide Data Table -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse" id="rag-resources-table">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/50 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                        <th class="px-6 py-3.5 w-5/12">Resource Title & Preview</th>
                        <th class="px-6 py-3.5 w-2/12">Domain Category</th>
                        <th class="px-6 py-3.5 w-2/12">Token Footprint</th>
                        <th class="px-6 py-3.5 w-2/12">Last Updated</th>
                        <th class="px-6 py-3.5 text-right w-1/12">Actions</th>
                    </tr>
                </thead>
                
                <!-- Skeleton Loader Rows (Toggled during async actions) -->
                <tbody id="rag-skeleton-loader" class="divide-y divide-zinc-100 dark:divide-zinc-800" style="display: none;">
                    <?php for ( $i = 0; $i < 5; $i++ ) : ?>
                        <tr class="animate-pulse">
                            <td class="px-6 py-4">
                                <div class="h-3.5 bg-zinc-200 dark:bg-zinc-800 rounded-md w-3/4 mb-2"></div>
                                <div class="h-2.5 bg-zinc-100 dark:bg-zinc-850 rounded w-1/2"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-5 bg-zinc-200 dark:bg-zinc-800 rounded-full w-20"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded w-12 font-mono"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded w-24"></div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded w-10 ml-auto"></div>
                            </td>
                        </tr>
                    <?php endfor; ?>
                </tbody>

                <!-- Active Resource Rows -->
                <tbody id="rag-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <?php if ( empty($resources) ) : ?>
                        <tr id="rag-empty-row">
                            <td colspan="5" class="px-6 py-12 text-center text-zinc-400 text-xs">
                                No RAG resources index found. Add your first resource snippet or re-index your workspace.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $resources as $res ) : 
                            $raw_content = $res['content'];
                            $st = $res['source_type'];
                            ?>
                            <tr class="rag-resource-row hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition-colors" data-id="<?php echo $res['id']; ?>" data-title="<?php echo esc_attr($res['title']); ?>" data-content="<?php echo esc_attr($raw_content); ?>" data-source-type="<?php echo esc_attr($st); ?>" data-source-id="<?php echo esc_attr($res['source_id']); ?>">
                                <td class="px-6 py-4">
                                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate max-w-md" title="<?php echo esc_attr($res['title']); ?>">
                                        <?php echo esc_html($res['title']); ?>
                                    </div>
                                    <?php if ( ! empty($raw_content) ) : ?>
                                        <div class="text-[11px] text-zinc-400 font-mono mt-0.5 truncate max-w-md">
                                            <?php echo esc_html(wp_trim_words(strip_tags($raw_content), 12)); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ( $st === 'crm' ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                            CRM Deal
                                        </span>
                                    <?php elseif ( $st === 'financials' ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                            Financial
                                        </span>
                                    <?php elseif ( $st === 'operations' ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                            Operations
                                        </span>
                                    <?php elseif ( $st === 'vault' ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                            Vault Doc
                                        </span>
                                    <?php elseif ( $st === 'reviews' ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                            Review
                                        </span>
                                    <?php elseif ( in_array( $st, array( 'article', 'url', 'text' ) ) ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                            <?php echo ucfirst($st); ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                            Auto Sync
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-zinc-850 dark:text-zinc-200">
                                    <?php echo number_format($res['token_count']); ?> <span class="text-[10px] text-zinc-400 font-normal">tok</span>
                                </td>
                                <td class="px-6 py-4 text-[11px] text-zinc-500 dark:text-zinc-400">
                                    <?php echo date_i18n( get_option('date_format'), strtotime($res['updated_at'] ?: $res['created_at']) ); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" onclick="editRagResource(<?php echo $res['id']; ?>)" class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" title="Edit resource">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                        <button type="button" onclick="deleteRagResource(<?php echo $res['id']; ?>)" class="p-1.5 text-zinc-400 hover:text-red-600 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" title="Delete resource">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sliding Right Drawer: Add/Edit Resource -->
<div id="cora-rag-drawer-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 hidden transition-opacity" onclick="closeRagResourceDrawer()"></div>
<div id="cora-rag-resource-drawer" class="fixed inset-y-0 right-0 max-w-md w-full bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-50 translate-x-full transition-transform duration-300 flex flex-col">
    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
        <h3 id="rag-drawer-title" class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Add Resource Fragment</h3>
        <button type="button" onclick="closeRagResourceDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-rag-resource-form" onsubmit="handleSaveRagResource(event)" class="p-6 space-y-4 flex-1 overflow-y-auto">
        <input type="hidden" id="rag-resource-id" value="0">
        
        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">Source Category</label>
            <select id="rag-resource-type" class="w-full text-xs font-semibold bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 outline-none">
                <option value="text">Text Fragment / Domain Knowledge</option>
                <option value="vault">Vault Document / Contract Terms</option>
                <option value="crm">CRM Client / Lead Notes</option>
                <option value="financials">Financial / Pricing Terms</option>
                <option value="url">External Web Link Reference</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">Resource Title</label>
            <input type="text" id="rag-resource-title" required class="w-full text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 outline-none text-zinc-900 dark:text-zinc-100" placeholder="e.g. Commercial Photography Standard Deliverables">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">Knowledge Content</label>
            <textarea id="rag-resource-content" rows="8" required class="w-full text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 outline-none text-zinc-900 dark:text-zinc-100" placeholder="Enter text or guidelines for AI memory context..."></textarea>
        </div>

        <div class="pt-4 border-t border-zinc-150 dark:border-zinc-800 flex justify-end gap-2">
            <button type="button" onclick="closeRagResourceDrawer()" class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs rounded-xl cursor-pointer">Cancel</button>
            <button type="submit" id="rag-save-btn" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-xl transition-all cursor-pointer shadow-xs">Save Resource</button>
        </div>
    </form>
</div>

<script>
let coraActiveRAGCategory = 'all';

function coraFilterRAGCategory(cat) {
    coraActiveRAGCategory = cat;
    const catButtons = ['all', 'crm', 'financials', 'operations', 'vault'];
    catButtons.forEach(c => {
        const btn = document.getElementById('rag-cat-' + c);
        if (btn) {
            if (c === cat) {
                btn.className = 'px-3 py-1 rounded-lg bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 text-[11px] font-bold cursor-pointer transition-all';
            } else {
                btn.className = 'px-3 py-1 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 text-[11px] cursor-pointer transition-all';
            }
        }
    });
    filterRagResourcesTable();
}

function filterRagResourcesTable() {
    const searchVal = (document.getElementById('rag-table-search')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.rag-resource-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const title = (row.getAttribute('data-title') || '').toLowerCase();
        const content = (row.getAttribute('data-content') || '').toLowerCase();
        const sourceType = (row.getAttribute('data-source-type') || '').toLowerCase();

        let matchesCat = (coraActiveRAGCategory === 'all') || (sourceType === coraActiveRAGCategory) || (coraActiveRAGCategory === 'operations' && (sourceType === 'operations' || sourceType === 'tasks' || sourceType === 'bookings'));
        let matchesSearch = !searchVal || title.includes(searchVal) || content.includes(searchVal);

        if (matchesCat && matchesSearch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const countEl = document.getElementById('rag-showing-count');
    if (countEl) countEl.innerText = visibleCount;
}

function openRagResourceDrawer(id) {
    document.getElementById('rag-resource-id').value = id || 0;
    document.getElementById('rag-drawer-title').innerText = id ? 'Edit Knowledge Resource' : 'Add Resource Fragment';
    if (!id) {
        document.getElementById('rag-resource-title').value = '';
        document.getElementById('rag-resource-content').value = '';
        document.getElementById('rag-resource-type').value = 'text';
    }
    document.getElementById('cora-rag-resource-drawer').classList.remove('translate-x-full');
    document.getElementById('cora-rag-drawer-overlay').classList.remove('hidden');
}

function closeRagResourceDrawer() {
    document.getElementById('cora-rag-resource-drawer').classList.add('translate-x-full');
    document.getElementById('cora-rag-drawer-overlay').classList.add('hidden');
}

function editRagResource(id) {
    const row = document.querySelector(`.rag-resource-row[data-id="${id}"]`);
    if (!row) return;
    document.getElementById('rag-resource-id').value = id;
    document.getElementById('rag-resource-title').value = row.getAttribute('data-title') || '';
    document.getElementById('rag-resource-content').value = row.getAttribute('data-content') || '';
    document.getElementById('rag-resource-type').value = row.getAttribute('data-source-type') || 'text';
    document.getElementById('rag-drawer-title').innerText = 'Edit Knowledge Resource';
    document.getElementById('cora-rag-resource-drawer').classList.remove('translate-x-full');
    document.getElementById('cora-rag-drawer-overlay').classList.remove('hidden');
}

function deleteRagResource(id) {
    if (window.coraConfirmAction) {
        window.coraConfirmAction('Remove Knowledge Fragment', 'Are you sure you want to remove this resource fragment from the AI second brain?', function() {
            executeDeleteRagResource(id);
        });
    } else {
        executeDeleteRagResource(id);
    }
}

function executeDeleteRagResource(id) {
    jQuery.ajax({
        url: typeof cora_workspace_data !== 'undefined' ? cora_workspace_data.ajax_url : '<?php echo admin_url("admin-ajax.php"); ?>',
        type: 'POST',
        data: {
            action: 'cora_delete_rag_resource',
            id: id,
            nonce: typeof cora_workspace_data !== 'undefined' ? cora_workspace_data.nonce : '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>'
        },
        success: function(resp) {
            if (resp.success) {
                if (window.coraShowToast) window.coraShowToast(resp.data.message || 'Resource removed.');
                const row = document.querySelector(`.rag-resource-row[data-id="${id}"]`);
                if (row) row.remove();
                filterRagResourcesTable();
            } else {
                if (window.coraShowToast) window.coraShowToast(resp.data?.message || 'Failed to remove resource.');
            }
        }
    });
}

function handleSaveRagResource(e) {
    e.preventDefault();
    const id = document.getElementById('rag-resource-id').value;
    const title = document.getElementById('rag-resource-title').value;
    const content = document.getElementById('rag-resource-content').value;
    const sourceType = document.getElementById('rag-resource-type').value;
    const saveBtn = document.getElementById('rag-save-btn');

    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerText = 'Saving...';
    }

    jQuery.ajax({
        url: typeof cora_workspace_data !== 'undefined' ? cora_workspace_data.ajax_url : '<?php echo admin_url("admin-ajax.php"); ?>',
        type: 'POST',
        data: {
            action: 'cora_save_rag_resource',
            id: id,
            title: title,
            content: content,
            source_type: sourceType,
            nonce: typeof cora_workspace_data !== 'undefined' ? cora_workspace_data.nonce : '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>'
        },
        success: function(resp) {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Save Resource';
            }
            if (resp.success) {
                if (window.coraShowToast) window.coraShowToast(resp.data.message || 'Resource saved.');
                closeRagResourceDrawer();
                setTimeout(() => location.reload(), 1000);
            } else {
                if (window.coraShowToast) window.coraShowToast(resp.data?.message || 'Failed to save resource.');
            }
        },
        error: function() {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Save Resource';
            }
            if (window.coraShowToast) window.coraShowToast('Network error while saving resource.');
        }
    });
}

function triggerImmediateSync(btn) {
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block mr-1">⟳</span> Syncing...';
    }
    // Show skeleton loaders during sync
    const skeleton = document.getElementById('rag-skeleton-loader');
    const tableBody = document.getElementById('rag-table-body');
    if (skeleton && tableBody) {
        skeleton.style.display = '';
        tableBody.style.display = 'none';
    }
    if (window.coraShowToast) window.coraShowToast('Executing Living AI Memory sweep...', 'info');

    jQuery.ajax({
        url: typeof cora_workspace_data !== 'undefined' ? cora_workspace_data.ajax_url : '<?php echo admin_url("admin-ajax.php"); ?>',
        type: 'POST',
        data: {
            action: 'cora_reindex_living_memory',
            nonce: typeof cora_workspace_data !== 'undefined' ? cora_workspace_data.nonce : '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>'
        },
        success: function(res) {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Sync';
            }
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Living memory synchronized!');
                setTimeout(() => location.reload(), 1000);
            } else {
                if (skeleton && tableBody) {
                    skeleton.style.display = 'none';
                    tableBody.style.display = '';
                }
                if (window.coraShowToast) window.coraShowToast(res.data?.message || 'Sync failed.');
            }
        },
        error: function() {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Sync';
            }
            if (skeleton && tableBody) {
                skeleton.style.display = 'none';
                tableBody.style.display = '';
            }
            if (window.coraShowToast) window.coraShowToast('Network error during sync.');
        }
    });
}
</script>
