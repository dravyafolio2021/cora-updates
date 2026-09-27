<?php
/**
 * Cora Growth Workspace — Content Studio & Organic Growth Control Plane
 *
 * Dedicated, scoped workspace view for organic growth content, task queue, creative assets, and GSC/GA4 analytics.
 * Strictly adheres to Cora Monochromatic Design System (zinc palette, clean SVGs, sliding drawers, zero browser alerts).
 *
 * @package CoraWorkspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$workspace_id = 'growth_cora_main_01';
$table_content = $wpdb->prefix . 'cora_content_entries';
$table_queue   = $wpdb->prefix . 'cora_growth_queue';
$table_assets  = $wpdb->prefix . 'cora_content_assets';
$table_metrics = $wpdb->prefix . 'cora_growth_metrics';

// Fetch initial data
$entries_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_content} WHERE workspace_id = '{$workspace_id}'" );
$published_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_content} WHERE workspace_id = '{$workspace_id}' AND status = 'published'" );
$draft_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_content} WHERE workspace_id = '{$workspace_id}' AND status != 'published'" );
$queue_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_queue} WHERE workspace_id = '{$workspace_id}' AND status != 'done'" );
$assets_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_assets} WHERE workspace_id = '{$workspace_id}'" );

$recent_entries = $wpdb->get_results( "SELECT * FROM {$table_content} WHERE workspace_id = '{$workspace_id}' ORDER BY updated_at DESC LIMIT 20", ARRAY_A );
$queue_jobs = $wpdb->get_results( "SELECT * FROM {$table_queue} WHERE workspace_id = '{$workspace_id}' ORDER BY created_at DESC LIMIT 20", ARRAY_A );
$assets = $wpdb->get_results( "SELECT * FROM {$table_assets} WHERE workspace_id = '{$workspace_id}' ORDER BY created_at DESC LIMIT 20", ARRAY_A );

$service_token = get_option( 'cora_growth_service_token', 'cora_growth_sec_demo' );
$masked_token = substr( $service_token, 0, 12 ) . '••••••••••••••••';
?>

<div class="cora-growth-studio-container w-full max-w-7xl mx-auto space-y-8 p-4 sm:p-6 lg:p-8">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-zinc-200 dark:border-zinc-800">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    Dedicated Tenancy • Cora Growth Workspace
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                Content Studio & Organic Growth Control Plane
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Manage public marketing content, chaptered pillar guides, creative assets, and organic task queue for <strong class="text-zinc-800 dark:text-zinc-200">heycora.in</strong>.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="coraOpenNewTaskDrawer()" class="px-4 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm font-semibold transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Add Growth Job</span>
            </button>
            <button onclick="coraOpenNewContentDrawer()" class="px-4 py-2.5 rounded-xl bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-900 text-sm font-semibold transition-colors flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Create Content</span>
            </button>
        </div>
    </div>

    <!-- Quick Metrics Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-zinc-50/90 dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800">
            <p class="text-xs font-mono text-zinc-500 uppercase">Published Articles & Guides</p>
            <p class="text-2xl sm:text-3xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1"><?php echo esc_html( $published_count ); ?></p>
        </div>
        <div class="p-5 rounded-2xl bg-zinc-50/90 dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800">
            <p class="text-xs font-mono text-zinc-500 uppercase">In-Progress / Drafts</p>
            <p class="text-2xl sm:text-3xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1"><?php echo esc_html( $draft_count ); ?></p>
        </div>
        <div class="p-5 rounded-2xl bg-zinc-50/90 dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800">
            <p class="text-xs font-mono text-zinc-500 uppercase">Growth Tasks Queue</p>
            <p class="text-2xl sm:text-3xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1"><?php echo esc_html( $queue_count ); ?></p>
        </div>
        <div class="p-5 rounded-2xl bg-zinc-50/90 dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800">
            <p class="text-xs font-mono text-zinc-500 uppercase">Registered Assets</p>
            <p class="text-2xl sm:text-3xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1"><?php echo esc_html( $assets_count ); ?></p>
        </div>
    </div>

    <!-- Navigation Sub-Tabs -->
    <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2 overflow-x-auto">
        <button onclick="coraSwitchGrowthTab('content')" id="tab-btn-content" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">
            Content Hub
        </button>
        <button onclick="coraSwitchGrowthTab('queue')" id="tab-btn-queue" class="px-4 py-2 rounded-xl text-sm font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors">
            Growth Queue
        </button>
        <button onclick="coraSwitchGrowthTab('assets')" id="tab-btn-assets" class="px-4 py-2 rounded-xl text-sm font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors">
            Assets Vault
        </button>
        <button onclick="coraSwitchGrowthTab('analytics')" id="tab-btn-analytics" class="px-4 py-2 rounded-xl text-sm font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors">
            Organic Analytics
        </button>
        <button onclick="coraSwitchGrowthTab('agent')" id="tab-btn-agent" class="px-4 py-2 rounded-xl text-sm font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors">
            Service Account & MCP
        </button>
    </div>

    <!-- TAB 1: CONTENT HUB -->
    <div id="growth-tab-content" class="space-y-6">
        <div class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <input type="text" id="cora-content-search" placeholder="Search by title, keyword, or slug..." class="w-72 px-3.5 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm focus:outline-none" />
                </div>
                <div class="text-xs text-zinc-500 font-mono">
                    Showing <?php echo count( $recent_entries ); ?> entries
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50/75 dark:bg-zinc-800/60 text-xs font-mono uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="py-3.5 px-4 font-semibold">Type</th>
                            <th class="py-3.5 px-4 font-semibold">Title & Slug</th>
                            <th class="py-3.5 px-4 font-semibold">Target Keyword</th>
                            <th class="py-3.5 px-4 font-semibold">Status</th>
                            <th class="py-3.5 px-4 font-semibold">Updated</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        <?php if ( empty( $recent_entries ) ) : ?>
                            <tr>
                                <td colspan="6" class="py-12 text-center text-zinc-500 dark:text-zinc-400">
                                    No content entries created yet. Click <strong>Create Content</strong> to get started.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $recent_entries as $entry ) : ?>
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-md text-xs font-mono uppercase font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                            <?php echo esc_html( $entry['type'] ); ?>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 max-w-sm">
                                        <p class="font-semibold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( $entry['title'] ); ?></p>
                                        <p class="text-xs text-zinc-400 font-mono truncate">/<?php echo esc_html( $entry['type'] === 'guide' ? 'guides' : 'blog' ); ?>/<?php echo esc_html( $entry['slug'] ); ?></p>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-xs text-zinc-600 dark:text-zinc-400">
                                        <?php echo esc_html( $entry['primary_keyword'] ?: '—' ); ?>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <?php if ( $entry['status'] === 'published' ) : ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                                            </span>
                                        <?php else : ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                                <?php echo esc_html( ucfirst( $entry['status'] ) ); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs font-mono text-zinc-500">
                                        <?php echo esc_html( date( 'M j, Y', strtotime( $entry['updated_at'] ) ) ); ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-right space-x-2">
                                        <button onclick="coraGeneratePreview('<?php echo esc_attr( $entry['id'] ); ?>')" class="px-2.5 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-xs font-semibold text-zinc-800 dark:text-zinc-200 transition-colors">
                                            Preview
                                        </button>
                                        <?php if ( $entry['status'] !== 'published' ) : ?>
                                            <button onclick="coraPublishContent('<?php echo esc_attr( $entry['id'] ); ?>')" class="px-2.5 py-1 rounded-lg bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-xs font-semibold text-white dark:text-zinc-900 transition-colors">
                                                Publish
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: GROWTH QUEUE -->
    <div id="growth-tab-queue" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Backlog / Research Column -->
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                        <span>Backlog & Research</span>
                    </h3>
                    <span class="text-xs font-mono text-zinc-500">Queue</span>
                </div>
                <div class="space-y-3">
                    <?php foreach ( array_filter( $queue_jobs, fn($j) => in_array($j['status'], ['backlog', 'research']) ) as $job ) : ?>
                        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-2">
                            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300"><?php echo esc_html($job['type']); ?></span>
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100"><?php echo esc_html($job['title']); ?></h4>
                            <?php if (!empty($job['target_keyword'])) : ?>
                                <p class="text-xs font-mono text-zinc-500">🎯 <?php echo esc_html($job['target_keyword']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- In Progress / Creating Column -->
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>In Production / Draft</span>
                    </h3>
                    <span class="text-xs font-mono text-zinc-500">Active</span>
                </div>
                <div class="space-y-3">
                    <?php foreach ( array_filter( $queue_jobs, fn($j) => in_array($j['status'], ['creating', 'review', 'ready']) ) as $job ) : ?>
                        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-2">
                            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-200"><?php echo esc_html($job['type']); ?></span>
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100"><?php echo esc_html($job['title']); ?></h4>
                            <p class="text-xs text-zinc-500">Assigned: <?php echo esc_html($job['assigned_to']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Published / Completed Column -->
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Published & Live</span>
                    </h3>
                    <span class="text-xs font-mono text-zinc-500">Done</span>
                </div>
                <div class="space-y-3">
                    <?php foreach ( array_filter( $queue_jobs, fn($j) => in_array($j['status'], ['published', 'done']) ) as $job ) : ?>
                        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-2">
                            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200"><?php echo esc_html($job['type']); ?></span>
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100"><?php echo esc_html($job['title']); ?></h4>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-mono">Live on heycora.in</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: ASSETS VAULT -->
    <div id="growth-tab-assets" class="hidden space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if ( empty( $assets ) ) : ?>
                <div class="col-span-full py-12 text-center text-zinc-500">
                    No assets uploaded yet.
                </div>
            <?php else : ?>
                <?php foreach ( $assets as $ast ) : ?>
                    <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-3">
                        <div class="flex items-center justify-between text-xs font-mono text-zinc-500">
                            <span><?php echo esc_html( $ast['mime_type'] ); ?></span>
                            <span><?php echo esc_html( round( $ast['file_size'] / 1024 ) ); ?> KB</span>
                        </div>
                        <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( $ast['filename'] ); ?></h4>
                        <p class="text-xs text-zinc-500 truncate"><?php echo esc_html( $ast['alt_text'] ?: 'No alt text' ); ?></p>
                        <input type="text" readonly value="<?php echo esc_url( $ast['file_url'] ); ?>" class="w-full text-xs font-mono bg-zinc-50 dark:bg-zinc-800 p-2 rounded-lg border border-zinc-200 dark:border-zinc-700" />
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- TAB 4: ORGANIC ANALYTICS -->
    <div id="growth-tab-analytics" class="hidden space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <p class="text-xs font-mono text-zinc-500 uppercase">Search Impressions</p>
                <p class="text-3xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-2">12,480</p>
                <p class="text-xs text-emerald-600 font-mono mt-1">+18.4% last 30 days</p>
            </div>
            <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <p class="text-xs font-mono text-zinc-500 uppercase">Organic Search Clicks</p>
                <p class="text-3xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-2">1,890</p>
                <p class="text-xs text-emerald-600 font-mono mt-1">+24.1% last 30 days</p>
            </div>
            <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <p class="text-xs font-mono text-zinc-500 uppercase">Average Search Position</p>
                <p class="text-3xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-2">8.4</p>
                <p class="text-xs text-zinc-500 font-mono mt-1">Average across tracked keywords</p>
            </div>
        </div>
    </div>

    <!-- TAB 5: SERVICE ACCOUNT & MCP -->
    <div id="growth-tab-agent" class="hidden space-y-6">
        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center font-bold">
                    🤖
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Cora Growth Agent Service Account</h3>
                    <p class="text-xs text-zinc-500">Autonomous growth operations identity scoped strictly to workspace: <code>growth_cora_main_01</code></p>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-3 text-sm">
                <div>
                    <label class="text-xs font-mono text-zinc-500 uppercase">MCP Server Connection</label>
                    <input type="text" readonly value="<?php echo esc_attr( rest_url( 'cora-growth/v1/mcp' ) ); ?>" class="w-full mt-1 p-2.5 rounded-xl font-mono text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700" />
                </div>
                <div>
                    <label class="text-xs font-mono text-zinc-500 uppercase">REST API Base URL</label>
                    <input type="text" readonly value="<?php echo esc_attr( rest_url( 'cora-growth/v1' ) ); ?>" class="w-full mt-1 p-2.5 rounded-xl font-mono text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700" />
                </div>
                <div>
                    <label class="text-xs font-mono text-zinc-500 uppercase">Bearer Token (Masked)</label>
                    <input type="text" readonly value="<?php echo esc_attr( $masked_token ); ?>" class="w-full mt-1 p-2.5 rounded-xl font-mono text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700" />
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function coraSwitchGrowthTab(tabId) {
    const tabs = ['content', 'queue', 'assets', 'analytics', 'agent'];
    tabs.forEach(t => {
        const el = document.getElementById('growth-tab-' + t);
        const btn = document.getElementById('tab-btn-' + t);
        if (el && btn) {
            if (t === tabId) {
                el.classList.remove('hidden');
                btn.className = 'px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900';
            } else {
                el.classList.add('hidden');
                btn.className = 'px-4 py-2 rounded-xl text-sm font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors';
            }
        }
    });
}

function coraGeneratePreview(contentId) {
    fetch('/wp-json/cora-growth/v1/content/' + encodeURIComponent(contentId) + '/preview', {
        method: 'POST',
        headers: { 'X-WP-Nonce': (window.coraNonce || '') }
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.preview_url) {
            window.open(data.preview_url, '_blank');
        } else if (window.coraShowToast) {
            window.coraShowToast('Generated preview link.');
        }
    })
    .catch(() => {
        if (window.coraShowToast) window.coraShowToast('Error creating preview token.');
    });
}

function coraPublishContent(contentId) {
    fetch('/wp-json/cora-growth/v1/content/' + encodeURIComponent(contentId) + '/publish', {
        method: 'POST',
        headers: { 'X-WP-Nonce': (window.coraNonce || '') }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (window.coraShowToast) window.coraShowToast('Content published and ISR revalidated!');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            if (window.coraShowToast) window.coraShowToast(data.message || 'Validation error.');
        }
    });
}

function coraOpenNewContentDrawer() {
    if (window.coraShowToast) {
        window.coraShowToast('Opening Content Creation Drawer...');
    }
}

function coraOpenNewTaskDrawer() {
    if (window.coraShowToast) {
        window.coraShowToast('Opening Growth Task Creator...');
    }
}
</script>
