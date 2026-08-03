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
$text_count = 0;
$url_count = 0;
$article_count = 0;
$sync_count = 0;

foreach ( $resources as $res ) {
    $total_tokens += intval( $res['token_count'] );
    if ( $res['source_type'] === 'text' ) {
        $text_count++;
    } elseif ( $res['source_type'] === 'url' ) {
        $url_count++;
    } elseif ( $res['source_type'] === 'article' ) {
        $article_count++;
    } elseif ( $res['source_type'] === 'platform_sync' ) {
        $sync_count++;
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

<!-- RAG Knowledge Base Container -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-200 dark:border-zinc-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-2.5">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-900 dark:text-zinc-50"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                RAG Knowledge Base
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Configure files, snippets, and blog posts to contextually enrich Cora Studio's AI generation pipelines.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openRagResourceDrawer(0)" class="inline-flex items-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-200 dark:text-zinc-950 text-xs font-semibold px-4 py-2 rounded-lg transition-colors cursor-pointer shadow-xs">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Resource
            </button>
        </div>
    </div>

    <!-- Hero Automation Card -->
    <div class="p-6 border border-zinc-200 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-900/10 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-xs relative overflow-hidden">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Workspace Knowledge Auto-Sync</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold <?php echo $sync_interval !== 'disabled' ? 'bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 border border-green-200/50' : 'bg-zinc-150 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700'; ?>">
                        <?php echo $sync_interval !== 'disabled' ? 'Active' : 'Disabled'; ?>
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed max-w-xl">
                    Cora updates this workspace's second brain on a <strong class="font-semibold text-zinc-900 dark:text-zinc-100"><?php echo esc_html($sync_interval !== 'disabled' ? $sync_interval : 'manual'); ?></strong> cycle using connected AI models.
                </p>
                <div class="flex flex-wrap gap-x-4 gap-y-1.5 mt-3 text-[11px] text-zinc-450">
                    <span class="flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Last Sync: <strong class="font-bold text-zinc-755 dark:text-zinc-300"><?php echo $last_sync_at ? esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($last_sync_at))) : 'Never'; ?></strong>
                    </span>
                    <span class="flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                        Cycle budget: <strong class="font-bold text-zinc-755 dark:text-zinc-300"><?php echo $sync_budget_pct; ?>%</strong> of quota
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <button onclick="openSyncSettingsDrawer()" class="p-2.5 border border-zinc-200 dark:border-zinc-800 text-zinc-600 hover:text-zinc-900 dark:hover:text-zinc-50 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all cursor-pointer" title="Sync Settings">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </button>
            <button onclick="openSyncPreviewDrawer()" class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 text-zinc-800 hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-zinc-100 text-xs font-semibold rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all cursor-pointer">
                Preview Update
            </button>
            <button onclick="triggerImmediateSync(this)" class="inline-flex items-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-950 text-xs font-bold px-4 py-2 rounded-xl transition-all cursor-pointer shadow-xs">
                Sync Now
            </button>
        </div>
    </div>

    <!-- Telemetry Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Card 1: Total Resources -->
        <div class="p-5 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-2xl flex flex-col justify-between min-h-[120px] shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Total Resources</span>
                <span class="p-1.5 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-black text-zinc-900 dark:text-zinc-50 font-mono tracking-tight"><?php echo number_format($total_resources); ?></h3>
                <p class="text-[10px] text-zinc-400 mt-1">Ingested items indexing for AI context lookup</p>
            </div>
        </div>

        <!-- Card 2: Quota & Token Usage -->
        <div class="p-5 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-2xl flex flex-col justify-between min-h-[120px] shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">RAG Token Quota Usage</span>
                <span class="text-[10px] font-bold font-mono px-2 py-0.5 bg-zinc-100 dark:bg-zinc-850 text-zinc-650 dark:text-zinc-300 rounded-md">
                    <?php echo $usage_percent; ?>% Used
                </span>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-xs font-bold text-zinc-900 dark:text-zinc-50 font-mono mb-1.5">
                    <span><?php echo number_format($total_tokens); ?> <span class="text-zinc-400 font-normal">tokens</span></span>
                    <span>/ <?php echo number_format($quota); ?></span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-850 rounded-full overflow-hidden">
                    <div class="h-full bg-zinc-950 dark:bg-white rounded-full transition-all duration-500" style="width: <?php echo $usage_percent; ?>%;"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Ingestion Channels -->
        <div class="p-5 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-2xl flex flex-col justify-between min-h-[120px] shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Ingestion Channels</span>
                <span class="p-1.5 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                </span>
            </div>
            <div class="grid grid-cols-4 gap-1.5 text-center mt-3">
                <div class="bg-zinc-50 dark:bg-zinc-900/50 p-2 rounded-xl border border-zinc-100 dark:border-zinc-900/40">
                    <span class="block text-[9px] text-zinc-400 font-bold uppercase">Text</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-50 font-mono"><?php echo $text_count; ?></span>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-900/50 p-2 rounded-xl border border-zinc-100 dark:border-zinc-900/40">
                    <span class="block text-[9px] text-zinc-400 font-bold uppercase">URLs</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-50 font-mono"><?php echo $url_count; ?></span>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-900/50 p-2 rounded-xl border border-zinc-100 dark:border-zinc-900/40">
                    <span class="block text-[9px] text-zinc-400 font-bold uppercase">Blogs</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-50 font-mono"><?php echo $article_count; ?></span>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-900/50 p-2 rounded-xl border border-zinc-100 dark:border-zinc-900/40" title="AI Synced Summaries">
                    <span class="block text-[9px] text-zinc-400 font-bold uppercase">Syncs</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-50 font-mono"><?php echo $sync_count; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync History collapsible list (capped at last 5) -->
    <?php if ( ! empty( $sync_history ) ) : ?>
        <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-2xl shadow-xs overflow-hidden">
            <details class="group">
                <summary class="flex justify-between items-center px-5 py-4 cursor-pointer font-bold text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition-all select-none list-none">
                    <div class="flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M12 8v4l3 3"></path><circle cx="12" cy="12" r="10"></circle></svg>
                        <span>Sync Activity History Logs</span>
                    </div>
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 group-open:rotate-180 transition-transform"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </summary>
                <div class="px-5 pb-5 pt-1 divide-y divide-zinc-100 dark:divide-zinc-850">
                    <?php foreach ( $sync_history as $log ) : 
                        $status_badge = 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
                        if ( $log['status'] === 'success' ) {
                            $status_badge = 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400';
                        } elseif ( $log['status'] === 'error' ) {
                            $status_badge = 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400';
                        }
                        ?>
                        <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-zinc-850 dark:text-zinc-100">
                                        <?php echo date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime($log['timestamp']) ); ?>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider <?php echo $status_badge; ?>">
                                        <?php echo esc_html($log['status']); ?>
                                    </span>
                                </div>
                                <p class="text-zinc-500 dark:text-zinc-400"><?php echo esc_html($log['message']); ?></p>
                            </div>
                            <div class="text-right shrink-0">
                                <?php if ( ! empty($log['categories_updated']) ) : ?>
                                    <div class="text-[10px] text-zinc-450 truncate max-w-xs mb-0.5">
                                        Updated: <?php echo esc_html(implode(', ', $log['categories_updated'])); ?>
                                    </div>
                                <?php endif; ?>
                                <span class="font-mono text-[10px] text-zinc-400">
                                    Used: <?php echo number_format($log['tokens_consumed']); ?> tokens
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </details>
        </div>
    <?php endif; ?>

    <!-- Interactive Search and Data Table Container -->
    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-2xl shadow-xs overflow-hidden">
        <!-- Table Toolbar -->
        <div class="px-5 py-4 border-b border-zinc-150 dark:border-zinc-850 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-zinc-50/20 dark:bg-zinc-950/20">
            <div class="relative max-w-xs w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="rag-table-search" oninput="filterRagResourcesTable()" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg pl-9 pr-3 py-1.5 text-xs bg-white dark:bg-zinc-900 focus:border-zinc-400 dark:focus:border-zinc-600 outline-none text-zinc-900 dark:text-zinc-100" placeholder="Search resources...">
            </div>
            <div class="text-[10px] text-zinc-400 font-mono">
                Showing <span id="rag-showing-count"><?php echo count($resources); ?></span> of <span id="rag-total-count"><?php echo count($resources); ?></span> resources
            </div>
        </div>

        <!-- Notion-Style Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="rag-resources-table">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-850 bg-zinc-50/50 dark:bg-zinc-950/40 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                        <th class="px-6 py-3.5">Title</th>
                        <th class="px-6 py-3.5">Source Type</th>
                        <th class="px-6 py-3.5">Tokens size</th>
                        <th class="px-6 py-3.5">Last updated</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-850">
                    <?php if ( empty($resources) ) : ?>
                        <tr id="rag-empty-row">
                            <td colspan="5" class="px-6 py-12 text-center text-zinc-400 text-xs">
                                No RAG resources index found. Add your first resource snippet or link above.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $resources as $res ) : 
                            $raw_content = $res['content'];
                            ?>
                            <tr class="rag-resource-row hover:bg-zinc-50/40 dark:hover:bg-zinc-900/10 transition-colors" data-id="<?php echo $res['id']; ?>" data-title="<?php echo esc_attr($res['title']); ?>" data-content="<?php echo esc_attr($raw_content); ?>" data-source-type="<?php echo esc_attr($res['source_type']); ?>" data-source-id="<?php echo esc_attr($res['source_id']); ?>">
                                <td class="px-6 py-4">
                                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-50 truncate max-w-sm" title="<?php echo esc_attr($res['title']); ?>">
                                        <?php echo esc_html($res['title']); ?>
                                    </div>
                                    <?php if ($res['source_type'] === 'url' && !empty($res['content'])) : ?>
                                        <div class="text-[10px] text-zinc-400 font-mono mt-0.5 truncate max-w-sm">
                                            <?php echo esc_html(wp_trim_words($res['content'], 10)); ?>
                                        </div>
                                    <?php elseif ($res['source_type'] === 'platform_sync' && !empty($res['content'])) : ?>
                                        <div class="text-[10px] text-zinc-400 font-mono mt-0.5 truncate max-w-sm">
                                            <?php echo esc_html(wp_trim_words($res['content'], 12)); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ( $res['source_type'] === 'text' ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800/80 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                            <span class="w-1 h-1 bg-zinc-500 rounded-full"></span>
                                            Text Snippet
                                        </span>
                                    <?php elseif ( $res['source_type'] === 'url' ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800/80 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                            <span class="w-1 h-1 bg-zinc-500 rounded-full"></span>
                                            Web Link
                                        </span>
                                    <?php elseif ( $res['source_type'] === 'article' ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800/80 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                            <span class="w-1 h-1 bg-zinc-500 rounded-full"></span>
                                            Blog Post
                                        </span>
                                    <?php elseif ( $res['source_type'] === 'platform_sync' ) : ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400 border border-green-200/50">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                            Platform Sync
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-zinc-850 dark:text-zinc-300">
                                    <?php echo number_format($res['token_count']); ?>
                                </td>
                                <td class="px-6 py-4 text-xs text-zinc-450">
                                    <?php echo date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime($res['updated_at']) ); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <?php if ($res['source_type'] === 'platform_sync') : ?>
                                            <button onclick="triggerImmediateSync(this)" class="p-1.5 border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors cursor-pointer" title="Force Sync Platform Summaries">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                                            </button>
                                        <?php elseif ($res['source_type'] !== 'article') : ?>
                                            <button onclick="openRagResourceDrawer(<?php echo $res['id']; ?>)" class="p-1.5 border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors cursor-pointer" title="Edit Resource">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            </button>
                                        <?php else : ?>
                                            <a href="<?php echo get_edit_post_link($res['source_id']); ?>" class="p-1.5 border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors cursor-pointer" target="_blank" title="Edit Blog in WordPress">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                            </a>
                                        <?php endif; ?>
                                        <button onclick="deleteRagResource(<?php echo $res['id']; ?>)" class="p-1.5 border border-zinc-200 dark:border-zinc-800 text-zinc-400 hover:text-red-500 rounded-lg hover:bg-red-50/30 dark:hover:bg-red-950/20 hover:border-red-200 dark:hover:border-red-900/50 transition-colors cursor-pointer" title="Delete Resource">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
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

<!-- Add / Edit RAG Resource Right-Sliding Drawer -->
<div id="cora-rag-resource-overlay" onclick="closeRagResourceDrawer()" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-rag-resource-drawer" class="fixed top-0 right-0 h-full w-full sm:w-120 bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-850 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-zinc-100 dark:bg-zinc-900 rounded-lg text-zinc-900 dark:text-zinc-100">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50" id="rag-drawer-title">Ingest Resource</h3>
                <p class="text-xs text-zinc-400" id="rag-drawer-subtitle">Index content to RAG knowledge pool</p>
            </div>
        </div>
        <button onclick="closeRagResourceDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Body Form -->
    <div class="flex-1 overflow-y-auto p-6 space-y-5">
        <!-- Resource ID (Hidden) -->
        <input type="hidden" id="rag-field-id" value="0">

        <!-- Title -->
        <div>
            <label class="block text-xs font-bold text-zinc-650 dark:text-zinc-400 mb-1.5">Resource Name / Title</label>
            <input type="text" id="rag-field-title" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 text-xs bg-white dark:bg-zinc-900 focus:border-zinc-400 outline-none text-zinc-900 dark:text-zinc-100" placeholder="e.g. Luxury Real Estate Marketing Pitch">
        </div>

        <!-- Source Type -->
        <div>
            <label class="block text-xs font-bold text-zinc-650 dark:text-zinc-400 mb-1.5">Ingestion Format</label>
            <select id="rag-field-source-type" onchange="toggleRagSourceFields()" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 text-xs bg-white dark:bg-zinc-900 focus:border-zinc-400 outline-none text-zinc-900 dark:text-zinc-100">
                <option value="text">Raw Text Snippet / Notes</option>
                <option value="url">External Web Link (URL)</option>
            </select>
        </div>

        <!-- Content Area -->
        <div id="rag-container-content">
            <label class="block text-xs font-bold text-zinc-650 dark:text-zinc-400 mb-1.5">Content Text</label>
            <textarea id="rag-field-content" rows="12" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 text-xs bg-white dark:bg-zinc-900 focus:border-zinc-400 outline-none text-zinc-900 dark:text-zinc-100 font-sans" placeholder="Paste or type content resource here..."></textarea>
        </div>

        <!-- URL Area -->
        <div id="rag-container-url" class="hidden">
            <label class="block text-xs font-bold text-zinc-650 dark:text-zinc-400 mb-1.5">Source URL Address</label>
            <input type="url" id="rag-field-url" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 text-xs bg-white dark:bg-zinc-900 focus:border-zinc-400 outline-none text-zinc-900 dark:text-zinc-100 font-mono" placeholder="https://example.com/resource-article">
            <p class="text-[10px] text-zinc-400 mt-1">Cora will crawl, index metadata and text from this URL.</p>
        </div>
    </div>

    <!-- Footer Controls -->
    <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-850 flex items-center justify-end gap-2.5 shrink-0 bg-zinc-50/20 dark:bg-zinc-950/20">
        <button onclick="closeRagResourceDrawer()" class="px-4 py-2 text-xs font-bold text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors cursor-pointer">
            Cancel
        </button>
        <button onclick="saveRagResource()" id="rag-save-btn" class="inline-flex items-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-200 dark:text-zinc-950 text-xs font-semibold px-4 py-2 rounded-lg transition-colors cursor-pointer shadow-xs">
            Ingest Content
        </button>
    </div>
</div>

<!-- Sync Settings Drawer (Right-sliding sheet) -->
<div id="cora-rag-settings-overlay" onclick="closeSyncSettingsDrawer()" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-rag-settings-drawer" class="fixed top-0 right-0 h-full w-full sm:w-120 bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-850 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-zinc-100 dark:bg-zinc-900 rounded-lg text-zinc-900 dark:text-zinc-100">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Sync Settings</h3>
                <p class="text-xs text-zinc-400">Configure auto-sync intervals & budget guards</p>
            </div>
        </div>
        <button onclick="closeSyncSettingsDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        <div>
            <label class="block text-xs font-bold text-zinc-650 dark:text-zinc-400 mb-1.5">Automated Sync Interval</label>
            <select id="rag-settings-interval" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 text-xs bg-white dark:bg-zinc-900 focus:border-zinc-400 outline-none text-zinc-900 dark:text-zinc-100">
                <option value="twicedaily" <?php selected( $sync_interval, 'twicedaily' ); ?>>Every 12 Hours</option>
                <option value="daily" <?php selected( $sync_interval, 'daily' ); ?>>Every 24 Hours (Default)</option>
                <option value="weekly" <?php selected( $sync_interval, 'weekly' ); ?>>Every 7 Days</option>
                <option value="disabled" <?php selected( $sync_interval, 'disabled' ); ?>>Disabled (Manual sync only)</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-650 dark:text-zinc-400 mb-1.5">Token Budget Safety Cap</label>
            <div class="flex items-center gap-4">
                <input type="range" id="rag-settings-budget" min="5" max="50" step="5" value="<?php echo $sync_budget_pct; ?>" class="w-full h-1 bg-zinc-200 dark:bg-zinc-800 rounded-lg appearance-none cursor-pointer accent-zinc-950 dark:accent-white" oninput="document.getElementById('rag-settings-budget-label').innerText = this.value + '%'">
                <span id="rag-settings-budget-label" class="font-mono text-xs font-bold text-zinc-850 dark:text-zinc-200 min-w-[35px] text-right"><?php echo $sync_budget_pct; ?>%</span>
            </div>
            <p class="text-[10px] text-zinc-400 mt-2 leading-relaxed">
                Prevents automated cycles from executing if the delta analysis estimate exceeds this percentage of your total quota limit.
            </p>
        </div>
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-850 flex items-center justify-end gap-2.5 shrink-0 bg-zinc-50/20 dark:bg-zinc-950/20">
        <button onclick="closeSyncSettingsDrawer()" class="px-4 py-2 text-xs font-bold text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors cursor-pointer">
            Cancel
        </button>
        <button onclick="saveRagSyncSettings()" id="rag-settings-save-btn" class="bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-200 dark:text-zinc-950 text-xs font-bold px-4 py-2 rounded-lg transition-colors cursor-pointer shadow-xs">
            Save Settings
        </button>
    </div>
</div>

<!-- Sync Preview Drawer (Right-sliding sheet) -->
<div id="cora-rag-sync-preview-overlay" onclick="closeSyncPreviewDrawer()" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-rag-sync-preview-drawer" class="fixed top-0 right-0 h-full w-full sm:w-120 bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-850 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-zinc-100 dark:bg-zinc-900 rounded-lg text-zinc-900 dark:text-zinc-100">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Sync Preview & Delta</h3>
                <p class="text-xs text-zinc-400">Platform activity changed since last sync</p>
            </div>
        </div>
        <button onclick="closeSyncPreviewDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        <div class="space-y-3.5" id="rag-sync-preview-list">
            <!-- Dynamically populated categories -->
        </div>

        <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-900 space-y-4">
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Estimated sync footprint:</span>
                <span class="font-mono font-bold text-zinc-800 dark:text-zinc-100"><span id="rag-preview-total-est">0</span> tokens</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Remaining workspace quota:</span>
                <span class="font-mono font-bold text-zinc-855 dark:text-zinc-200"><span id="rag-preview-quota-left">0</span> / <span id="rag-preview-quota-total">0</span></span>
            </div>

            <!-- Double-layered Progress Bar representing Used + Estimated Sync Size -->
            <div class="w-full h-2 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden relative">
                <!-- Quota Used -->
                <div id="rag-preview-progress-bar-used" class="absolute left-0 top-0 h-full bg-zinc-950 dark:bg-white rounded-full transition-all duration-300"></div>
                <!-- Quota Estimated addition -->
                <div id="rag-preview-progress-bar-est" class="absolute top-0 h-full bg-green-500/80 rounded-full transition-all duration-300" style="left: 0%;"></div>
            </div>
            <div class="flex justify-end gap-3 text-[9px] text-zinc-400">
                <span class="flex items-center gap-1"><span class="w-2 h-2 bg-zinc-950 dark:bg-white rounded-full"></span> Current Usage</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 bg-green-500/80 rounded-full"></span> Estimated Sync Addition</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-850 flex items-center justify-end gap-2.5 shrink-0 bg-zinc-50/20 dark:bg-zinc-950/20">
        <button onclick="closeSyncPreviewDrawer()" class="px-4 py-2 text-xs font-bold text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors cursor-pointer">
            Wait for Schedule
        </button>
        <button onclick="triggerImmediateSync(this)" class="bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-200 dark:text-zinc-950 text-xs font-bold px-4 py-2 rounded-lg transition-colors cursor-pointer shadow-xs">
            Confirm & Sync Now
        </button>
    </div>
</div>

<!-- Custom Confirmation Modal (replaces browser confirm) -->
<div id="cora-rag-confirm-drawer" class="fixed inset-0 z-[9999] flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeRagConfirm()"></div>
    <div class="relative w-full max-w-sm mx-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden p-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50" id="rag-confirm-title">Remove Resource?</h4>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed" id="rag-confirm-message">
                    Are you sure you want to permanently remove this resource? This action cannot be undone.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <button onclick="closeRagConfirm()" class="flex-1 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-xl cursor-pointer">
                Cancel
            </button>
            <button id="rag-confirm-ok-btn" class="flex-1 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl cursor-pointer">
                Delete
            </button>
        </div>
    </div>
</div>

<script>
// Filter resources table client-side
function filterRagResourcesTable() {
    const searchVal = document.getElementById('rag-table-search').value.toLowerCase();
    const rows = document.querySelectorAll('.rag-resource-row');
    let showingCount = 0;

    rows.forEach(row => {
        const title = row.getAttribute('data-title').toLowerCase();
        const type = row.getAttribute('data-source-type').toLowerCase();
        
        if (title.includes(searchVal) || type.includes(searchVal)) {
            row.style.display = '';
            showingCount++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('rag-showing-count').innerText = showingCount;

    // Handle empty row display
    const emptyRow = document.getElementById('rag-empty-row');
    if (showingCount === 0) {
        if (!emptyRow) {
            const tbody = document.querySelector('#rag-resources-table tbody');
            const tr = document.createElement('tr');
            tr.id = 'rag-empty-row';
            tr.innerHTML = '<td colspan="5" class="px-6 py-12 text-center text-zinc-400 text-xs">No matching resources found.</td>';
            tbody.appendChild(tr);
        } else {
            emptyRow.style.display = '';
            emptyRow.querySelector('td').innerText = 'No matching resources found.';
        }
    } else if (emptyRow) {
        emptyRow.style.display = 'none';
    }
}

// Toggle drawer field view based on source format
function toggleRagSourceFields() {
    const type = document.getElementById('rag-field-source-type').value;
    const contentContainer = document.getElementById('rag-container-content');
    const urlContainer = document.getElementById('rag-container-url');

    if (type === 'text') {
        contentContainer.classList.remove('hidden');
        urlContainer.classList.add('hidden');
    } else {
        contentContainer.classList.add('hidden');
        urlContainer.classList.remove('hidden');
    }
}

// Open Drawer (Add or Edit mode)
function openRagResourceDrawer(resourceId = 0) {
    // Reset fields
    document.getElementById('rag-field-id').value = resourceId;
    document.getElementById('rag-field-title').value = '';
    document.getElementById('rag-field-source-type').value = 'text';
    document.getElementById('rag-field-content').value = '';
    document.getElementById('rag-field-url').value = '';
    
    toggleRagSourceFields();

    if (resourceId > 0) {
        // Edit mode - fetch existing row attributes
        const row = document.querySelector(`.rag-resource-row[data-id="${resourceId}"]`);
        if (row) {
            const title = row.getAttribute('data-title');
            const type = row.getAttribute('data-source-type');
            const content = row.getAttribute('data-content');

            document.getElementById('rag-drawer-title').innerText = 'Update Resource';
            document.getElementById('rag-drawer-subtitle').innerText = 'Modify index metrics for ' + title;
            document.getElementById('rag-field-title').value = title;
            document.getElementById('rag-field-source-type').value = type;

            toggleRagSourceFields();

            if (type === 'text') {
                document.getElementById('rag-field-content').value = content;
            } else {
                document.getElementById('rag-field-url').value = content;
            }
            document.getElementById('rag-save-btn').innerText = 'Update Resource';
        }
    } else {
        document.getElementById('rag-drawer-title').innerText = 'Ingest Resource';
        document.getElementById('rag-drawer-subtitle').innerText = 'Index content to RAG knowledge pool';
        document.getElementById('rag-save-btn').innerText = 'Ingest Content';
    }

    // Open animations
    document.getElementById('cora-rag-resource-drawer').classList.remove('translate-x-full');
    document.getElementById('cora-rag-resource-overlay').classList.remove('hidden');
}

// Close Drawer
function closeRagResourceDrawer() {
    document.getElementById('cora-rag-resource-drawer').classList.add('translate-x-full');
    document.getElementById('cora-rag-resource-overlay').classList.add('hidden');
}

// Save Resource Action
function saveRagResource() {
    const id = document.getElementById('rag-field-id').value;
    const title = document.getElementById('rag-field-title').value;
    const type = document.getElementById('rag-field-source-type').value;
    const contentVal = (type === 'text') 
        ? document.getElementById('rag-field-content').value 
        : document.getElementById('rag-field-url').value;

    if (!title.trim()) {
        if (window.coraShowToast) window.coraShowToast('Please enter a resource title.', 'error');
        return;
    }
    if (!contentVal.trim()) {
        const errorMsg = (type === 'text') ? 'Please provide the text content.' : 'Please enter the resource URL.';
        if (window.coraShowToast) window.coraShowToast(errorMsg, 'error');
        return;
    }

    const saveBtn = document.getElementById('rag-save-btn');
    const originalText = saveBtn.innerText;
    saveBtn.disabled = true;
    saveBtn.innerText = 'Saving...';

    // Fire AJAX request
    jQuery.post(coraREData.ajaxUrl, {
        action: 'cora_save_rag_resource',
        nonce: coraREData.ajaxNonce,
        id: id,
        title: title,
        content: contentVal,
        source_type: type
    }, function(res) {
        saveBtn.disabled = false;
        saveBtn.innerText = originalText;

        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Resource saved successfully.', 'success');
            closeRagResourceDrawer();
            setTimeout(() => {
                location.reload();
            }, 800);
        } else {
            const errorMsg = (res.data && res.data.message) ? res.data.message : 'An error occurred while saving.';
            if (window.coraShowToast) window.coraShowToast(errorMsg, 'error');
        }
    }).fail(function() {
        saveBtn.disabled = false;
        saveBtn.innerText = originalText;
        if (window.coraShowToast) window.coraShowToast('Network error while saving resource.', 'error');
    });
}

let pendingDeleteId = null;

// Delete Resource Trigger
function deleteRagResource(resourceId) {
    pendingDeleteId = resourceId;
    document.getElementById('cora-rag-confirm-drawer').classList.remove('hidden');
    document.getElementById('rag-confirm-ok-btn').onclick = function() {
        executeDeleteRagResource();
    };
}

// Close Confirm Dialog
function closeRagConfirm() {
    document.getElementById('cora-rag-confirm-drawer').classList.add('hidden');
    pendingDeleteId = null;
}

// Execute deletion API request
function executeDeleteRagResource() {
    if (!pendingDeleteId) return;
    closeRagConfirm();

    if (window.coraShowToast) window.coraShowToast('Deleting resource...', 'info');

    jQuery.post(coraREData.ajaxUrl, {
        action: 'cora_delete_rag_resource',
        nonce: coraREData.ajaxNonce,
        id: pendingDeleteId
    }, function(res) {
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Resource removed successfully.', 'success');
            setTimeout(() => {
                location.reload();
            }, 800);
        } else {
            const errorMsg = (res.data && res.data.message) ? res.data.message : 'An error occurred while deleting.';
            if (window.coraShowToast) window.coraShowToast(errorMsg, 'error');
        }
    }).fail(function() {
        if (window.coraShowToast) window.coraShowToast('Network error while removing resource.', 'error');
    });
}

// Sync settings drawer toggle
function openSyncSettingsDrawer() {
    document.getElementById('cora-rag-settings-drawer').classList.remove('translate-x-full');
    document.getElementById('cora-rag-settings-overlay').classList.remove('hidden');
}

// Close Sync Settings Drawer
function closeSyncSettingsDrawer() {
    document.getElementById('cora-rag-settings-drawer').classList.add('translate-x-full');
    document.getElementById('cora-rag-settings-overlay').classList.add('hidden');
}

// Sync preview drawer toggle
function openSyncPreviewDrawer() {
    if (window.coraShowToast) window.coraShowToast('Loading delta analysis preview...', 'info');
    
    jQuery.post(coraREData.ajaxUrl, {
        action: 'cora_rag_sync_preview',
        nonce: coraREData.ajaxNonce
    }, function(res) {
        if (res.success) {
            const data = res.data;
            let html = '';
            
            data.categories.forEach(cat => {
                const badgeClass = cat.changes > 0 ? 'bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400' : 'bg-zinc-50 dark:bg-zinc-900 text-zinc-400';
                html += `
                <div class="p-3.5 border border-zinc-150 dark:border-zinc-850 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-zinc-850 dark:text-zinc-200">${cat.name}</span>
                        <span class="block text-[10px] text-zinc-450 mt-0.5">Estimated payload footprint: ${cat.token_estimate.toLocaleString()} tokens</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeClass}">
                        ${cat.changes} changes
                    </span>
                </div>
                `;
            });

            document.getElementById('rag-sync-preview-list').innerHTML = html;
            document.getElementById('rag-preview-total-est').innerText = data.total_estimated_tokens.toLocaleString();
            document.getElementById('rag-preview-quota-left').innerText = (data.quota_total - data.quota_used).toLocaleString();
            document.getElementById('rag-preview-quota-total').innerText = data.quota_total.toLocaleString();

            const quotaUsedPct = Math.round((data.quota_used / data.quota_total) * 100);
            const estUsedPct = Math.min(100 - quotaUsedPct, Math.round((data.total_estimated_tokens / data.quota_total) * 100));
            
            document.getElementById('rag-preview-progress-bar-used').style.width = quotaUsedPct + '%';
            document.getElementById('rag-preview-progress-bar-est').style.width = estUsedPct + '%';
            document.getElementById('rag-preview-progress-bar-est').style.left = quotaUsedPct + '%';

            // Open sync preview drawer
            document.getElementById('cora-rag-sync-preview-drawer').classList.remove('translate-x-full');
            document.getElementById('cora-rag-sync-preview-overlay').classList.remove('hidden');
        } else {
            if (window.coraShowToast) window.coraShowToast('Failed to load sync preview data.', 'error');
        }
    }).fail(function() {
        if (window.coraShowToast) window.coraShowToast('Network error loading sync preview.', 'error');
    });
}

// Close Sync Preview Drawer
function closeSyncPreviewDrawer() {
    document.getElementById('cora-rag-sync-preview-drawer').classList.add('translate-x-full');
    document.getElementById('cora-rag-sync-preview-overlay').classList.add('hidden');
}

// Trigger manual immediate sync
function triggerImmediateSync(btn) {
    if (btn.disabled) return;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-zinc-650" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Syncing...
    `;

    if (window.coraShowToast) window.coraShowToast('Compiling workspace update summaries...', 'info');

    jQuery.post(coraREData.ajaxUrl, {
        action: 'cora_rag_sync_execute',
        nonce: coraREData.ajaxNonce
    }, function(res) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Workspace RAG sync completed successfully!', 'success');
            closeSyncPreviewDrawer();
            setTimeout(() => {
                location.reload();
            }, 800);
        } else {
            const errorMsg = (res.data && res.data.message) ? res.data.message : 'An error occurred during sync.';
            if (window.coraShowToast) window.coraShowToast(errorMsg, 'error');
        }
    }).fail(function() {
        btn.disabled = false;
        btn.innerHTML = originalText;
        if (window.coraShowToast) window.coraShowToast('Network error during RAG sync execution.', 'error');
    });
}

// Save RAG update settings
function saveRagSyncSettings() {
    const interval = document.getElementById('rag-settings-interval').value;
    const budget = document.getElementById('rag-settings-budget').value;
    const saveBtn = document.getElementById('rag-settings-save-btn');
    
    saveBtn.disabled = true;
    saveBtn.innerText = 'Saving...';

    jQuery.post(coraREData.ajaxUrl, {
        action: 'cora_rag_sync_settings',
        nonce: coraREData.ajaxNonce,
        interval: interval,
        budget: budget
    }, function(res) {
        saveBtn.disabled = false;
        saveBtn.innerText = 'Save Settings';
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Settings saved successfully.', 'success');
            closeSyncSettingsDrawer();
            setTimeout(() => {
                location.reload();
            }, 800);
        } else {
            if (window.coraShowToast) window.coraShowToast('Failed to save settings.', 'error');
        }
    }).fail(function() {
        saveBtn.disabled = false;
        saveBtn.innerText = 'Save Settings';
        if (window.coraShowToast) window.coraShowToast('Network error while saving settings.', 'error');
    });
}
</script>
