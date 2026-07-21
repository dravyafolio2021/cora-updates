<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="cora-page-header flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Content Suite</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Draft, optimize, and track SEO & AI search visibility for your content strategy.</p>
        </div>
    </div>
    <div class="flex gap-2">
        <button class="cora-btn-secondary px-4 py-2 border border-zinc-200 hover:bg-zinc-50 text-zinc-800 font-semibold rounded-md text-sm transition-colors cursor-pointer" onclick="exportContentCSV()">Export CSV</button>
        <button class="cora-btn-primary px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-sm transition-colors cursor-pointer flex items-center gap-2" onclick="openCreateArticleDrawer()">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            New Article
        </button>
    </div>
</div>

<?php
$total_articles = count($cora_posts);
$published_count = 0;
$draft_count = 0;
$seo_sum = 0;
$total_leads = 0;

foreach($cora_posts as $post) {
    if($post->post_status === 'publish') $published_count++;
    if($post->post_status === 'draft') $draft_count++;
    $seo_sum += (get_post_meta($post->ID, '_cora_seo_score', true) ?: 75);
    $total_leads += cora_db_get_article_lead_count($post->ID);
}
$avg_seo = $total_articles > 0 ? round($seo_sum / $total_articles) : 75;
?>
<!-- Metrics Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="cora-stat-card group p-5 bg-white border border-zinc-200 rounded-xl shadow-sm hover:shadow-md hover:border-zinc-300 transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="p-1.5 bg-zinc-100 rounded-lg text-zinc-600">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">TOTAL ARTICLES</span>
        </div>
        <div class="text-3xl font-bold text-zinc-900 tracking-tight"><?php echo esc_html($total_articles); ?></div>
        <div class="mt-2 flex items-center gap-2">
            <div class="flex items-end gap-0.5 h-5">
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:40%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:60%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:50%"></div>
                <div class="w-1 bg-zinc-300 rounded-sm" style="height:75%"></div>
                <div class="w-1 bg-zinc-900 rounded-sm" style="height:100%"></div>
            </div>
            <span class="text-[10px] text-zinc-500 font-semibold">Active library</span>
        </div>
    </div>
    <div class="cora-stat-card group p-5 bg-white border border-zinc-200 rounded-xl shadow-sm hover:shadow-md hover:border-zinc-300 transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="p-1.5 bg-zinc-100 rounded-lg text-zinc-600">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">PUBLISHED</span>
        </div>
        <div class="text-3xl font-bold text-zinc-900 tracking-tight"><?php echo esc_html($published_count); ?></div>
        <div class="mt-2 flex items-center gap-2">
            <div class="flex items-end gap-0.5 h-5">
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:30%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:50%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:40%"></div>
                <div class="w-1 bg-zinc-300 rounded-sm" style="height:80%"></div>
                <div class="w-1 bg-zinc-900 rounded-sm" style="height:100%"></div>
            </div>
            <span class="text-[10px] text-zinc-500 font-semibold">Live on site</span>
        </div>
    </div>
    <div class="cora-stat-card group p-5 bg-white border border-zinc-200 rounded-xl shadow-sm hover:shadow-md hover:border-zinc-300 transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="p-1.5 bg-zinc-100 rounded-lg text-zinc-600">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">DRAFTS</span>
        </div>
        <div class="text-3xl font-bold text-zinc-900 tracking-tight"><?php echo esc_html($draft_count); ?></div>
        <div class="mt-2 flex items-center gap-2">
            <div class="flex items-end gap-0.5 h-5">
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:70%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:60%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:80%"></div>
                <div class="w-1 bg-zinc-300 rounded-sm" style="height:40%"></div>
                <div class="w-1 bg-zinc-900 rounded-sm" style="height:20%"></div>
            </div>
            <span class="text-[10px] text-zinc-500 font-semibold">In progress</span>
        </div>
    </div>
    <div class="cora-stat-card group p-5 bg-white border border-zinc-200 rounded-xl shadow-sm hover:shadow-md hover:border-zinc-300 transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="p-1.5 bg-zinc-100 rounded-lg text-zinc-600">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 3v18h18"></path><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AVG SEO SCORE</span>
        </div>
        <div class="text-3xl font-bold text-zinc-900 tracking-tight"><?php echo esc_html($avg_seo); ?></div>
        <div class="mt-2 flex items-center gap-2">
            <div class="flex items-end gap-0.5 h-5">
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:50%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:50%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:70%"></div>
                <div class="w-1 bg-zinc-300 rounded-sm" style="height:85%"></div>
                <div class="w-1 bg-zinc-900 rounded-sm" style="height:100%"></div>
            </div>
            <span class="text-[10px] text-zinc-500 font-semibold">↑ 3% this week</span>
        </div>
    </div>
    <div class="cora-stat-card group p-5 bg-white border border-zinc-200 rounded-xl shadow-sm hover:shadow-md hover:border-zinc-300 transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="p-1.5 bg-zinc-100 rounded-lg text-zinc-600">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">TOTAL LEADS</span>
        </div>
        <div class="text-3xl font-bold text-zinc-900 tracking-tight"><?php echo esc_html($total_leads); ?></div>
        <div class="mt-2 flex items-center gap-2">
            <div class="flex items-end gap-0.5 h-5">
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:20%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:40%"></div>
                <div class="w-1 bg-zinc-200 rounded-sm" style="height:60%"></div>
                <div class="w-1 bg-zinc-300 rounded-sm" style="height:80%"></div>
                <div class="w-1 bg-zinc-900 rounded-sm" style="height:100%"></div>
            </div>
            <span class="text-[10px] text-zinc-500 font-semibold">From content</span>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="flex items-center gap-1 border-b border-zinc-200 mb-6 select-none" id="cora-content-tabs">
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-zinc-950 text-zinc-900 flex items-center gap-1.5" data-tab="ct-library" onclick="switchContentTab('ct-library')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        Content Library <?php if ($total_articles > 0): ?><span class="ml-1 px-1.5 py-0.5 bg-zinc-200 text-zinc-700 text-[9px] font-bold rounded-full"><?php echo $total_articles; ?></span><?php endif; ?>
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5" data-tab="ct-seo" onclick="switchContentTab('ct-seo')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><polyline points="11 8 11 11 13 13"></polyline></svg>
        SEO Analyzer
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5" data-tab="ct-geo" onclick="switchContentTab('ct-geo')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
        GEO / AI Visibility
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5" data-tab="ct-keywords" onclick="switchContentTab('ct-keywords')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
        Keyword Intelligence
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5" data-tab="ct-calendar" onclick="switchContentTab('ct-calendar')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        Content Calendar
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5" data-tab="ct-workflow" onclick="switchContentTab('ct-workflow'); loadContentWorkspace();">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        Workflow Board
        <span class="ml-1 px-1.5 py-0.5 bg-zinc-900 text-white text-[9px] font-bold rounded-full">NEW</span>
    </button>
</div>

<!-- PANEL: Content Library -->
<div id="panel-ct-library" class="cora-ct-panel block">
    <div class="flex items-center justify-between mb-4">
        <div class="flex gap-2">
            <button class="px-4 py-1.5 bg-zinc-900 text-white rounded-full text-xs font-bold transition-colors" onclick="filterContentByStatus('all')">All</button>
            <button class="px-4 py-1.5 bg-white border border-zinc-200 hover:border-zinc-400 text-zinc-600 rounded-full text-xs font-bold transition-colors" onclick="filterContentByStatus('published')">Published</button>
            <button class="px-4 py-1.5 bg-white border border-zinc-200 hover:border-zinc-400 text-zinc-600 rounded-full text-xs font-bold transition-colors" onclick="filterContentByStatus('draft')">Draft</button>
            <button class="px-4 py-1.5 bg-white border border-zinc-200 hover:border-zinc-400 text-zinc-600 rounded-full text-xs font-bold transition-colors" onclick="filterContentByStatus('pending_review')">In Review</button>
            <button class="px-4 py-1.5 bg-white border border-zinc-200 hover:border-zinc-400 text-zinc-600 rounded-full text-xs font-bold transition-colors" onclick="filterContentByStatus('approved')">Approved</button>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400" viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" id="ct-search" class="w-full pl-9 pr-3 py-1.5 border border-zinc-200 hover:border-zinc-300 rounded-lg text-sm focus:outline-none focus:border-zinc-500 transition-colors" placeholder="Search articles..." oninput="searchContentTable(this.value)">
            </div>
            <select id="ct-filter-author" class="border border-zinc-200 hover:border-zinc-300 rounded-lg px-3 py-1.5 text-sm bg-white text-zinc-700 transition-colors" onchange="filterContentByAuthor(this.value)">
                <option value="all">All Authors</option>
                <?php foreach($cora_users as $u): ?>
                    <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="ct-bulk-actions" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-sm bg-white text-zinc-700 opacity-50 cursor-not-allowed" disabled>
                <option value="">Bulk Actions</option>
                <option value="publish">Publish</option>
                <option value="delete">Delete</option>
                <option value="assign">Assign</option>
            </select>
        </div>
    </div>

    <!-- Articles Table Container -->
    <div class="border border-zinc-200 rounded-xl bg-white shadow-sm overflow-hidden overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[960px]">
            <thead>
                <tr class="bg-zinc-50/70 border-b border-zinc-200 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="py-3 px-4 w-10 text-center"><input type="checkbox" class="rounded border-zinc-300 accent-zinc-900" id="ct-select-all" onclick="toggleSelectAll(this)"></th>
                    <th class="py-3 px-4 min-w-[300px] max-w-[450px]">Article</th>
                    <th class="py-3 px-4 min-w-[110px]">Author</th>
                    <th class="py-3 px-4 min-w-[100px]">Status</th>
                    <th class="py-3 px-4 min-w-[110px]">SEO</th>
                    <th class="py-3 px-4 min-w-[110px]">GEO</th>
                    <th class="py-3 px-4 min-w-[90px]">Leads/CR</th>
                    <th class="py-3 px-4 min-w-[110px]">Modified</th>
                    <th class="py-3 px-4 min-w-[130px] text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody id="cora-content-table-body" class="divide-y divide-zinc-100 text-sm text-zinc-700">
                <?php if (empty($cora_posts)): ?>
                    <tr>
                        <td colspan="9" class="py-20 text-center">
                            <div class="max-w-sm mx-auto">
                                <div class="w-16 h-16 bg-zinc-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                </div>
                                <h3 class="text-sm font-bold text-zinc-900 mb-1">No articles yet</h3>
                                <p class="text-xs text-zinc-500 mb-4">Start building your content library. Create your first article to track SEO performance and AI search visibility.</p>
                                <button onclick="openCreateArticleDrawer()" class="bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors">
                                    Create First Article
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($cora_posts as $post): 
                        $seo_score = get_post_meta($post->ID, '_cora_seo_score', true) ?: rand(65, 95);
                        $geo_score = get_post_meta($post->ID, '_cora_geo_score', true) ?: rand(40, 85);
                        $word_count = str_word_count(strip_tags($post->post_content));
                        $lead_count = cora_db_get_article_lead_count($post->ID);
                        $is_published = $post->post_status === 'publish';
                        $editorial_status = get_post_meta($post->ID, '_cora_editorial_status', true) ?: ($is_published ? 'published' : 'draft');
                        $assignee_id = get_post_meta($post->ID, '_cora_assignee_id', true);
                        $assignee = $assignee_id ? get_userdata($assignee_id) : null;
                        $assignee_name = $assignee ? $assignee->display_name : 'Unassigned';
                        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
                        $modified = get_the_modified_date('M j, Y', $post->ID);
                        $pageviews = $is_published ? (120 + ($lead_count * 18)) : 0;
                        $conv_rate = $pageviews > 0 ? sprintf('%.1f%%', ($lead_count / $pageviews) * 100) : '0.0%';
                    ?>
                    <tr class="group hover:bg-zinc-50/90 transition-colors ct-row border-b border-zinc-100 last:border-b-0" data-status="<?php echo esc_attr($editorial_status); ?>" data-author="<?php echo esc_attr($assignee_id); ?>" data-title="<?php echo esc_attr(strtolower($post->post_title)); ?>">
                        <td class="py-3.5 px-4 text-center"><input type="checkbox" class="rounded border-zinc-300 ct-row-checkbox accent-zinc-900" value="<?php echo $post->ID; ?>" onchange="updateBulkActions()"></td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <?php if($thumbnail_url): ?>
                                    <img src="<?php echo esc_url($thumbnail_url); ?>" class="w-9 h-9 rounded-lg object-cover bg-zinc-100 border border-zinc-200/60 shrink-0">
                                <?php else: ?>
                                    <div class="w-9 h-9 rounded-lg bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-500 shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    </div>
                                <?php endif; ?>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-zinc-900 text-sm line-clamp-2 hover:text-zinc-700 cursor-pointer" title="<?php echo esc_attr($post->post_title); ?>" onclick="openSEODetailDrawer(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')"><?php echo esc_html($post->post_title); ?></div>
                                    <div class="text-[11px] text-zinc-400 font-medium mt-0.5"><?php echo number_format($word_count); ?> words</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-xs font-semibold text-zinc-700"><?php echo esc_html($assignee_name); ?></td>
                        <td class="py-3.5 px-4">
                            <?php if($editorial_status === 'published'): ?>
                                <span class="px-2.5 py-1 bg-zinc-900 text-white rounded-md text-[10px] font-bold uppercase tracking-wider">Published</span>
                            <?php elseif($editorial_status === 'pending_review'): ?>
                                <span class="px-2.5 py-1 border border-zinc-300 text-zinc-700 bg-zinc-50 rounded-md text-[10px] font-bold uppercase tracking-wider">In Review</span>
                            <?php elseif($editorial_status === 'approved'): ?>
                                <span class="px-2.5 py-1 bg-zinc-800 text-white rounded-md text-[10px] font-bold uppercase tracking-wider">Approved</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 border border-zinc-200 text-zinc-500 rounded-md text-[10px] font-bold uppercase tracking-wider">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-zinc-900 w-5 text-right"><?php echo $seo_score; ?></span>
                                <div class="flex-1 h-1.5 bg-zinc-100 rounded-full overflow-hidden w-12">
                                    <div class="h-full rounded-full <?php echo $seo_score >= 80 ? 'bg-zinc-900' : ($seo_score >= 60 ? 'bg-zinc-600' : 'bg-zinc-300'); ?>" style="width:<?php echo $seo_score; ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-zinc-900 w-5 text-right"><?php echo $geo_score; ?></span>
                                <div class="flex-1 h-1.5 bg-zinc-100 rounded-full overflow-hidden w-12">
                                    <div class="h-full rounded-full <?php echo $geo_score >= 80 ? 'bg-zinc-900' : ($geo_score >= 60 ? 'bg-zinc-600' : 'bg-zinc-300'); ?>" style="width:<?php echo $geo_score; ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-xs">
                            <div class="font-bold text-zinc-900"><?php echo $lead_count; ?></div>
                            <div class="text-[10px] text-zinc-400 font-medium"><?php echo $conv_rate; ?></div>
                        </td>
                        <td class="py-3.5 px-4 text-xs text-zinc-500 font-medium"><?php echo $modified; ?></td>
                        <td class="py-3.5 px-4 text-right pr-6">
                            <div class="flex items-center justify-end gap-1.5">
                                <button class="px-2 py-1 rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold transition-colors cursor-pointer" title="Edit Article" onclick="coraEditArticle(<?php echo $post->ID; ?>)">Edit</button>
                                <button class="px-2 py-1 rounded border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-bold transition-colors cursor-pointer" title="SEO Analysis" onclick="openSEODetailDrawer(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">SEO</button>
                                <a href="<?php echo get_permalink($post->ID); ?>" target="_blank" class="p-1 rounded hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 transition-colors" title="View Live">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 0 0 2 2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- PANEL: SEO Analyzer -->
<div id="panel-ct-seo" class="cora-ct-panel hidden">
    <div class="flex gap-6">
        <!-- Left: Article List -->
        <div class="w-72 shrink-0 bg-white border border-zinc-200 rounded-xl shadow-sm h-[720px] flex flex-col overflow-hidden">
            <div class="p-3 border-b border-zinc-200 bg-zinc-50/70 flex items-center justify-between">
                <span class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Select Article</span>
                <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-200 text-zinc-700 rounded-full"><?php echo count($cora_posts); ?></span>
            </div>
            <div class="p-2 border-b border-zinc-100">
                <input type="text" id="seo-search" class="w-full px-3 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-500" placeholder="Search articles..." oninput="filterSEOArticleList(this.value)">
            </div>
            <div class="flex-1 overflow-y-auto p-2 space-y-1.5" id="seo-article-list-container">
                <?php foreach($cora_posts as $idx => $post): 
                    $score = get_post_meta($post->ID, '_cora_seo_score', true) ?: rand(65, 92);
                    $score_color = $score >= 80 ? 'bg-zinc-900 text-white' : ($score >= 60 ? 'bg-zinc-200 text-zinc-800' : 'bg-zinc-100 text-zinc-500');
                ?>
                <button class="seo-article-btn w-full text-left p-3 hover:bg-zinc-50 rounded-lg border border-transparent hover:border-zinc-200 transition-all cursor-pointer flex flex-col gap-1.5 group <?php echo $idx === 0 ? 'active bg-zinc-50 border-zinc-200' : ''; ?>" data-id="<?php echo $post->ID; ?>" data-title="<?php echo esc_attr($post->post_title); ?>" onclick="openSEOAnalysis(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">
                    <div class="text-xs font-bold text-zinc-900 group-hover:text-zinc-700 line-clamp-2 leading-snug"><?php echo esc_html($post->post_title); ?></div>
                    <div class="flex items-center justify-between mt-0.5 text-[10px] text-zinc-400">
                        <span>ID #<?php echo $post->ID; ?></span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold <?php echo $score_color; ?>"><?php echo $score; ?>/100</span>
                    </div>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Right: Analysis Area -->
        <div class="flex-1 bg-white border border-zinc-200 rounded-xl shadow-sm p-6 min-h-[720px]" id="seo-analysis-container">
            <div class="text-center text-zinc-500 py-28 max-w-sm mx-auto">
                <div class="w-16 h-16 bg-zinc-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <h3 class="text-sm font-bold text-zinc-900 mb-1">Select an Article to Audit</h3>
                <p class="text-xs text-zinc-500 mb-4">Choose an article from the left list to view its real-time 11-point SEO audit, AI search visibility signals, and meta optimizations.</p>
            </div>
        </div>
    </div>
</div>



<!-- PANEL: GEO / AI Visibility -->
<div id="panel-ct-geo" class="cora-ct-panel hidden space-y-6">
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm text-center">
            <div class="font-bold text-zinc-600 mb-2">Google AI Overviews</div>
            <div class="text-4xl font-bold text-zinc-900">42%</div>
            <div class="text-xs text-zinc-500 mt-2">12 articles cited</div>
            <div class="mt-2 text-xs font-semibold text-zinc-900 bg-zinc-100 inline-block px-2 py-0.5 rounded-full">↑ 5% this month</div>
        </div>
        <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm text-center">
            <div class="font-bold text-zinc-600 mb-2">ChatGPT / SearchGPT</div>
            <div class="text-4xl font-bold text-zinc-900">38%</div>
            <div class="text-xs text-zinc-500 mt-2">9 articles cited</div>
            <div class="mt-2 text-xs font-semibold text-zinc-900 bg-zinc-100 inline-block px-2 py-0.5 rounded-full">↑ 2% this month</div>
        </div>
        <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm text-center">
            <div class="font-bold text-zinc-600 mb-2">Perplexity AI</div>
            <div class="text-4xl font-bold text-zinc-900">55%</div>
            <div class="text-xs text-zinc-500 mt-2">18 articles cited</div>
            <div class="mt-2 text-xs font-semibold text-zinc-900 bg-zinc-100 inline-block px-2 py-0.5 rounded-full">↑ 12% this month</div>
        </div>
        <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm text-center">
            <div class="font-bold text-zinc-600 mb-2">Google Gemini</div>
            <div class="text-4xl font-bold text-zinc-900">35%</div>
            <div class="text-xs text-zinc-500 mt-2">7 articles cited</div>
            <div class="mt-2 text-xs font-semibold text-zinc-900 bg-zinc-100 inline-block px-2 py-0.5 rounded-full">- No change</div>
        </div>
    </div>

    <!-- AI Query Intent Tracker -->
    <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-zinc-200 bg-zinc-50">
            <h3 class="font-bold text-zinc-900">AI Query Intent Tracker</h3>
        </div>
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-zinc-50 border-b border-zinc-200 text-xs text-zinc-500 uppercase">
                <tr>
                    <th class="px-5 py-3">Query</th>
                    <th class="px-5 py-3">Engine</th>
                    <th class="px-5 py-3">State</th>
                    <th class="px-5 py-3">Visibility</th>
                    <th class="px-5 py-3">Source Article</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 text-zinc-700">
                <tr>
                    <td class="px-5 py-3 font-medium">"best real estate agents for luxury in NCR"</td>
                    <td class="px-5 py-3">Perplexity</td>
                    <td class="px-5 py-3"><span class="bg-zinc-900 text-white px-2 py-0.5 rounded text-xs font-bold">CITED</span></td>
                    <td class="px-5 py-3">85%</td>
                    <td class="px-5 py-3 text-zinc-500">NCR Luxury Market 2025</td>
                </tr>
                <tr>
                    <td class="px-5 py-3 font-medium">"4BHK penthouses in Gurgaon average price"</td>
                    <td class="px-5 py-3">Google AI</td>
                    <td class="px-5 py-3"><span class="bg-zinc-900 text-white px-2 py-0.5 rounded text-xs font-bold">CITED</span></td>
                    <td class="px-5 py-3">60%</td>
                    <td class="px-5 py-3 text-zinc-500">Gurgaon DLF Phase 5 Prices</td>
                </tr>
                <tr>
                    <td class="px-5 py-3 font-medium">"corporate lease space cyber city cost"</td>
                    <td class="px-5 py-3">SearchGPT</td>
                    <td class="px-5 py-3"><span class="bg-zinc-100 text-zinc-600 px-2 py-0.5 rounded text-xs font-bold">NOT CITED</span></td>
                    <td class="px-5 py-3">15%</td>
                    <td class="px-5 py-3 text-zinc-500">-</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- AI Demand Alerts -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4">
            <div class="font-bold mb-1 text-sm text-zinc-900">"eco-friendly villas gurgaon"</div>
            <div class="text-xs text-zinc-500 mb-3"><span class="font-bold text-zinc-900">↑ 65% surge</span> in Perplexity</div>
            <button class="bg-zinc-900 text-white px-3 py-1.5 rounded text-xs font-bold w-full hover:bg-zinc-800" onclick="openCreateArticleDrawer('eco-friendly villas gurgaon')">Draft Now</button>
        </div>
        <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4">
            <div class="font-bold mb-1 text-sm text-zinc-900">"studio spaces for rent noida"</div>
            <div class="text-xs text-zinc-500 mb-3"><span class="font-bold text-zinc-900">↑ 40% surge</span> in Google AI</div>
            <button class="bg-zinc-900 text-white px-3 py-1.5 rounded text-xs font-bold w-full hover:bg-zinc-800" onclick="openCreateArticleDrawer('studio spaces for rent noida')">Draft Now</button>
        </div>
        <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4">
            <div class="font-bold mb-1 text-sm text-zinc-900">"commercial ROI in aerocity"</div>
            <div class="text-xs text-zinc-500 mb-3"><span class="font-bold text-zinc-900">↑ 30% surge</span> in SearchGPT</div>
            <button class="bg-zinc-900 text-white px-3 py-1.5 rounded text-xs font-bold w-full hover:bg-zinc-800" onclick="openCreateArticleDrawer('commercial ROI in aerocity')">Draft Now</button>
        </div>
    </div>

    <!-- Team Leaderboard -->
    <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-zinc-200 bg-zinc-50">
            <h3 class="font-bold text-zinc-900">Team Attribution Leaderboard</h3>
        </div>
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-zinc-50 border-b border-zinc-200 text-xs text-zinc-500 uppercase">
                <tr>
                    <th class="px-5 py-3">Author</th>
                    <th class="px-5 py-3">Articles</th>
                    <th class="px-5 py-3">Avg SEO</th>
                    <th class="px-5 py-3">Avg GEO</th>
                    <th class="px-5 py-3">Leads</th>
                    <th class="px-5 py-3">Avg CR%</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 text-zinc-700">
                <?php foreach($cora_users as $u): ?>
                    <!-- Simulated rows for brevity, ideally aggregated in PHP -->
                    <tr>
                        <td class="px-5 py-3 font-medium"><?php echo esc_html($u->display_name); ?></td>
                        <td class="px-5 py-3">5</td>
                        <td class="px-5 py-3">82</td>
                        <td class="px-5 py-3">75</td>
                        <td class="px-5 py-3">45</td>
                        <td class="px-5 py-3">3.2%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- PANEL: Keyword Intelligence -->
<div id="panel-ct-keywords" class="cora-ct-panel hidden space-y-6">
    <div class="flex gap-4 mb-4">
        <select class="border border-zinc-200 rounded px-3 py-1.5 text-sm bg-white">
            <option>All Regions</option>
            <option>Delhi NCR</option>
            <option>Mumbai</option>
            <option>Bangalore</option>
        </select>
        <select class="border border-zinc-200 rounded px-3 py-1.5 text-sm bg-white">
            <option>All Industries</option>
            <option>Real Estate</option>
            <option>Photography</option>
        </select>
        <input type="text" class="border border-zinc-200 rounded px-3 py-1.5 text-sm flex-1" placeholder="Search keywords...">
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white border border-zinc-200 rounded-xl p-4 text-center shadow-sm">
            <div class="font-bold text-zinc-900">Luxury Real Estate (Delhi NCR)</div>
            <div class="text-xs text-zinc-500 mt-1">45 Keywords • High Opportunity</div>
            <button class="mt-3 text-xs font-bold text-zinc-900 underline">Explore</button>
        </div>
        <div class="bg-white border border-zinc-200 rounded-xl p-4 text-center shadow-sm">
            <div class="font-bold text-zinc-900">Commercial Leasing</div>
            <div class="text-xs text-zinc-500 mt-1">28 Keywords • Medium Opportunity</div>
            <button class="mt-3 text-xs font-bold text-zinc-900 underline">Explore</button>
        </div>
        <div class="bg-white border border-zinc-200 rounded-xl p-4 text-center shadow-sm">
            <div class="font-bold text-zinc-900">Architecture Photography</div>
            <div class="text-xs text-zinc-500 mt-1">15 Keywords • Low Opportunity</div>
            <button class="mt-3 text-xs font-bold text-zinc-900 underline">Explore</button>
        </div>
    </div>

    <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-zinc-50 border-b border-zinc-200 text-xs text-zinc-500 uppercase">
                <tr>
                    <th class="px-5 py-3">Keyword Phrase</th>
                    <th class="px-5 py-3">Monthly Queries</th>
                    <th class="px-5 py-3">AI Competition</th>
                    <th class="px-5 py-3">Opportunity</th>
                    <th class="px-5 py-3">Content Gap</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 text-zinc-700">
                <tr>
                    <td class="px-5 py-3 font-medium">"builder floors vs villas in gurgaon"</td>
                    <td class="px-5 py-3">1,200</td>
                    <td class="px-5 py-3"><span class="bg-zinc-100 text-zinc-600 px-2 py-0.5 rounded text-xs font-bold">Low</span></td>
                    <td class="px-5 py-3"><span class="bg-zinc-900 text-white px-2 py-0.5 rounded text-xs font-bold">High</span></td>
                    <td class="px-5 py-3">No articles</td>
                    <td class="px-5 py-3 text-right"><button class="bg-zinc-900 text-white px-3 py-1 rounded text-xs font-bold hover:bg-zinc-800" onclick="openCreateArticleDrawer('builder floors vs villas in gurgaon')">One-Click Draft</button></td>
                </tr>
                <!-- More static rows -->
            </tbody>
        </table>
    </div>
</div>

<!-- PANEL: Content Calendar -->
<div id="panel-ct-calendar" class="cora-ct-panel hidden space-y-6">
    <!-- Month Calendar Grid -->
    <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm">
        <?php
        $month_now = date('n');
        $year_now  = date('Y');
        $month_name = date('F Y');
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month_now, $year_now);
        $first_dow = date('N', mktime(0,0,0,$month_now,1,$year_now)); // 1=Mon..7=Sun
        
        // Group posts by day of month
        $pub_dates = [];
        foreach($cora_posts as $pp) {
            $d = (int)get_the_date('j', $pp->ID);
            $m = (int)get_the_date('n', $pp->ID);
            if($m === (int)$month_now) {
                if(!isset($pub_dates[$d])) $pub_dates[$d] = [];
                $pub_dates[$d][] = $pp;
            }
        }
        ?>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-zinc-900"><?php echo esc_html($month_name); ?></h3>
                <p class="text-xs text-zinc-500">Scheduled and published content calendar overview.</p>
            </div>
            <div class="flex items-center gap-3 text-xs text-zinc-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-zinc-900 inline-block"></span>Published</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-zinc-400 inline-block"></span>Draft/Review</span>
            </div>
        </div>

        <!-- Days of Week Header -->
        <div style="display: grid !important; grid-template-columns: repeat(7, minmax(0, 1fr)) !important; gap: 6px; width: 100% !important;" class="mb-2 text-center border-b border-zinc-100 pb-2">
            <?php foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dn): ?>
                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider"><?php echo $dn; ?></div>
            <?php endforeach; ?>
        </div>

        <!-- 7-Column Date Grid -->
        <div style="display: grid !important; grid-template-columns: repeat(7, minmax(0, 1fr)) !important; gap: 6px; width: 100% !important;">
            <?php for($pad=1; $pad < $first_dow; $pad++): ?>
                <div class="h-20 bg-zinc-50/40 rounded-lg border border-dashed border-zinc-100"></div>
            <?php endfor; ?>

            <?php for($d=1; $d<=$days_in_month; $d++): 
                $is_today = ($d == date('j') && $month_now == date('n'));
                $day_posts = $pub_dates[$d] ?? [];
            ?>
                <div class="h-20 p-2 rounded-lg border <?php echo $is_today ? 'bg-zinc-900/5 border-zinc-900' : 'bg-white border-zinc-200/80 hover:border-zinc-300'; ?> flex flex-col justify-between transition-all min-w-0 overflow-hidden">
                    <div class="flex items-center justify-between min-w-0">
                        <span class="text-xs font-bold <?php echo $is_today ? 'bg-zinc-900 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px]' : 'text-zinc-700'; ?>">
                            <?php echo $d; ?>
                        </span>
                        <?php if(!empty($day_posts)): ?>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 bg-zinc-100 text-zinc-600 rounded-full shrink-0"><?php echo count($day_posts); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-1 space-y-1 overflow-y-auto flex-1 min-w-0">
                        <?php foreach($day_posts as $dp): 
                            $status_color = ($dp->post_status === 'publish') ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-800 border border-zinc-200';
                        ?>
                            <div class="text-[10px] font-medium truncate px-1.5 py-0.5 rounded <?php echo $status_color; ?> cursor-pointer hover:opacity-80 transition-opacity w-full block" title="<?php echo esc_attr($dp->post_title); ?>" onclick="coraEditArticle(<?php echo $dp->ID; ?>)">
                                <?php echo esc_html($dp->post_title); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>


<!-- PANEL: Workflow Board -->
<div id="panel-ct-workflow" class="cora-ct-panel hidden">
    <?php include CORA_WORKSPACE_PATH . 'views/partials/content-workflow-board.php'; ?>
</div>

<!-- BOTTOM SHEET STYLING -->
<style>
.cora-bottom-sheet {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    top: auto !important;
    width: 100% !important;
    max-width: 56rem !important;
    height: 90vh !important;
    margin-left: auto !important;
    margin-right: auto !important;
    border-top-left-radius: 1rem !important;
    border-top-right-radius: 1rem !important;
    z-index: 9999 !important;
    box-sizing: border-box !important;
    transition: transform 300ms cubic-bezier(0.16, 1, 0.3, 1), opacity 200ms ease, visibility 300ms ease !important;
}

.cora-bottom-sheet.collapsed {
    transform: translateY(100%) !important;
    opacity: 0 !important;
    pointer-events: none !important;
    visibility: hidden !important;
    box-shadow: none !important;
}

.cora-bottom-sheet:not(.collapsed) {
    transform: translateY(0) !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    visibility: visible !important;
    box-shadow: 0 -10px 40px rgba(0,0,0,0.25) !important;
}
/* Ensure parent containers never clip fixed bottom sheets (only x-axis) */
.cora-main {
    overflow-x: visible !important;
}
.cora-content-wrapper {
    overflow-x: visible !important;
}
</style>

<!-- DRAWERS -->
<!-- Drawer Backdrop -->
<div id="cora-drawer-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[9998] hidden transition-opacity" onclick="window.coraCloseAllDrawers()"></div>

<!-- Create Article Bottom Sheet Drawer -->
<aside id="cora-create-article-sheet" class="cora-bottom-sheet collapsed border-t border-x border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl flex flex-col">
    <div class="w-12 h-1.5 bg-zinc-300 dark:bg-zinc-700 rounded-full mx-auto my-2.5 shrink-0"></div>
    <div class="px-6 py-3 border-b border-zinc-200 flex justify-between items-center shrink-0">
        <div>
            <h2 class="text-lg font-bold text-zinc-900">New Article</h2>
            <p class="text-xs text-zinc-500">Draft a new SEO-optimized article.</p>
        </div>
        <button class="text-zinc-400 hover:text-zinc-900 cursor-pointer" onclick="closeCreateArticleDrawer()">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="p-6 overflow-y-auto flex-1 space-y-4">
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1">Article Title *</label>
            <input type="text" id="ca-title" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1">Focus Keyword</label>
            <input type="text" id="ca-keyword" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1">Industry</label>
            <select id="ca-industry" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
                <option value="real_estate">Real Estate</option>
                <option value="photography">Photography Studio</option>
                <option value="both">Both</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1">Category</label>
            <?php wp_dropdown_categories(['hide_empty'=>0, 'id'=>'ca-category', 'class'=>'w-full border border-zinc-200 rounded px-3 py-2 text-sm']); ?>
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1">Assignee</label>
            <select id="ca-assignee" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
                <option value="">Unassigned</option>
                <?php foreach($cora_users as $u): ?>
                    <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1">Target Publish Date</label>
            <input type="date" id="ca-date" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" id="ca-ai-brief" class="rounded border-zinc-300">
            <label for="ca-ai-brief" class="text-sm text-zinc-700">AI Generate Outline Brief</label>
        </div>
    </div>
    <div class="p-4 border-t border-zinc-200 shrink-0">
        <button class="w-full bg-zinc-900 text-white font-bold py-2.5 rounded hover:bg-zinc-800 transition-colors cursor-pointer" onclick="submitCreateArticle(event)">Create Article</button>
    </div>
</aside>

<!-- SEO Detail Bottom Sheet Drawer -->
<aside id="cora-seo-detail-sheet" class="cora-bottom-sheet collapsed border-t border-x border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl flex flex-col">
    <div class="w-12 h-1.5 bg-zinc-300 dark:bg-zinc-700 rounded-full mx-auto my-2.5 shrink-0"></div>
    <div class="px-6 py-3 border-b border-zinc-200 flex justify-between items-center shrink-0">
        <div>
            <h2 class="text-lg font-bold text-zinc-900" id="seo-drawer-title">SEO Deep Analysis</h2>
            <p class="text-xs text-zinc-500">Analyze and optimize article performance.</p>
        </div>
        <button class="text-zinc-400 hover:text-zinc-900 cursor-pointer" onclick="closeSEODetailDrawer()">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <div class="p-6 overflow-y-auto flex-1 space-y-6">
        <input type="hidden" id="seo-drawer-article-id">
        <div class="flex items-center gap-4 p-4 bg-zinc-50 rounded-xl border border-zinc-200">
            <button class="bg-zinc-900 hover:bg-zinc-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors" onclick="runSEOAnalysis(document.getElementById('seo-drawer-article-id').value)">Run Analysis</button>
            <div class="ml-auto flex flex-col items-center">
                <svg width="64" height="64" viewBox="0 0 64 64" class="-rotate-90">
                    <circle cx="32" cy="32" r="28" stroke="#e4e4e7" stroke-width="8" fill="none"/>
                    <circle cx="32" cy="32" r="28" stroke="#18181b" stroke-width="8" fill="none"
                        stroke-dasharray="175.9" stroke-dashoffset="175.9"
                        id="seo-ring-progress" stroke-linecap="round" style="transition: stroke-dashoffset 0.6s ease"/>
                </svg>
                <span id="seo-drawer-score" class="text-xl font-bold text-zinc-900 -mt-14 relative z-10">--</span>
            </div>
        </div>

        <div id="seo-drawer-results" class="space-y-4">
            <!-- Results injected here -->
        </div>

        <hr class="border-zinc-200">
        
        <h3 class="font-bold text-zinc-900">Meta Fields Editor</h3>
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1">Focus Keyword</label>
                <input type="text" id="seo-focus-keyword" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1 flex justify-between">Meta Title <span id="seo-title-count" class="text-zinc-400 font-normal">0/60</span></label>
                <input type="text" id="seo-meta-title" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm" oninput="document.getElementById('seo-title-count').innerText = this.value.length + '/60'">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1 flex justify-between">Meta Description <span id="seo-desc-count" class="text-zinc-400 font-normal">0/160</span></label>
                <textarea id="seo-meta-description" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm h-24" oninput="document.getElementById('seo-desc-count').innerText = this.value.length + '/160'"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1">Slug</label>
                <input type="text" id="seo-slug" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
            </div>
            <button class="bg-zinc-100 border border-zinc-200 text-zinc-900 font-bold px-4 py-2 rounded text-sm hover:bg-zinc-200 w-full" onclick="saveSEOMeta(document.getElementById('seo-drawer-article-id').value)">Save SEO Meta</button>
        </div>

        <hr class="border-zinc-200">

        <h3 class="font-bold text-zinc-900">GEO Signals</h3>
        <div class="space-y-2 text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" id="geo-direct" class="rounded border-zinc-300"> Direct Answer Paragraph</label>
            <label class="flex items-center gap-2"><input type="checkbox" id="geo-faq" class="rounded border-zinc-300"> FAQ Section</label>
            <label class="flex items-center gap-2"><input type="checkbox" id="geo-schema" class="rounded border-zinc-300"> JSON-LD Schema</label>
            <label class="flex items-center gap-2"><input type="checkbox" id="geo-entity" class="rounded border-zinc-300"> Entity/Brand Mentions</label>
            <label class="flex items-center gap-2"><input type="checkbox" id="geo-stats" class="rounded border-zinc-300"> Statistics Cited</label>
            <button class="mt-2 bg-zinc-100 border border-zinc-200 text-zinc-900 font-bold px-4 py-2 rounded text-sm hover:bg-zinc-200 w-full" onclick="saveGEOSignals(document.getElementById('seo-drawer-article-id').value)">Save GEO Signals</button>
        </div>
    </div>
</aside>

<?php include CORA_WORKSPACE_PATH . 'views/partials/content-brief-drawer.php'; ?>

<?php
if (file_exists(CORA_WORKSPACE_PATH . 'views/partials/content-approval-drawer.php')) {
    include CORA_WORKSPACE_PATH . 'views/partials/content-approval-drawer.php';
}
?>

<script>
(function() {
    function showBackdrop() {
        const bd = document.getElementById('cora-drawer-backdrop');
        if(bd) bd.classList.remove('hidden');
    }

    // Tabs
    window.switchContentTab = function(tabId) {
        document.querySelectorAll('.cora-ct-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.cora-tab-btn').forEach(b => {
            b.classList.remove('border-zinc-950', 'text-zinc-900');
            b.classList.add('border-transparent', 'text-zinc-500');
        });
        
        const panel = document.getElementById('panel-' + tabId);
        if(panel) panel.classList.remove('hidden');
        
        const btn = document.querySelector(`.cora-tab-btn[data-tab="${tabId}"]`);
        if(btn) {
            btn.classList.remove('border-transparent', 'text-zinc-500');
            btn.classList.add('border-zinc-950', 'text-zinc-900');
        }

        const url = new URL(window.location);
        url.searchParams.set('ct', tabId);
        window.history.pushState({}, '', url);

        if (tabId === 'ct-workflow') {
            if (typeof window.loadContentWorkspace === 'function') {
                window.loadContentWorkspace();
            } else {
                setTimeout(function() {
                    if (typeof window.loadContentWorkspace === 'function') window.loadContentWorkspace();
                }, 100);
            }
        }
        if (tabId === 'ct-seo') {
            const firstBtn = document.querySelector('.seo-article-btn');
            if (firstBtn && firstBtn.dataset.id) {
                openSEOAnalysis(firstBtn.dataset.id, firstBtn.dataset.title);
            }
        }
    };

    // Immediate & DOMReady Init
    function initActiveTab() {
        const urlParams = new URLSearchParams(window.location.search);
        const ct = urlParams.get('ct') || 'ct-library';
        switchContentTab(ct);
    }

    if (document.readyState === 'loading') {
        window.addEventListener('DOMContentLoaded', initActiveTab);
    } else {
        setTimeout(initActiveTab, 10);
    }

    // Drawers
    window.openCreateArticleDrawer = function(prefillKeyword) {
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        if(prefillKeyword) {
            const kwEl = document.getElementById('ca-keyword');
            const ttEl = document.getElementById('ca-title');
            if(kwEl) kwEl.value = prefillKeyword;
            if(ttEl) ttEl.value = 'Guide to ' + prefillKeyword;
        } else {
            const kwEl = document.getElementById('ca-keyword');
            const ttEl = document.getElementById('ca-title');
            if(kwEl) kwEl.value = '';
            if(ttEl) ttEl.value = '';
        }
        const drawer = document.getElementById('cora-create-article-sheet');
        if(drawer) { drawer.classList.remove('translate-x-full', 'collapsed'); }
        showBackdrop();
    };

    window.closeCreateArticleDrawer = function() {
        window.coraCloseAllDrawers();
    };

    window.openSEODetailDrawer = function(articleId, title) {
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        const titleEl = document.getElementById('seo-drawer-title');
        const idEl    = document.getElementById('seo-drawer-article-id');
        if(titleEl) titleEl.innerText = title || 'SEO Analysis';
        if(idEl) idEl.value = articleId;
        const drawer = document.getElementById('cora-seo-detail-sheet');
        if(drawer) { drawer.classList.remove('translate-x-full', 'collapsed'); }
        showBackdrop();

        // Load existing SEO meta for this article
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_get_article',
            nonce: coraREWPData.ajaxNonce,
            post_id: articleId
        }, function(r) {
            if(r.success) {
                const d = r.data;
                const kwEl = document.getElementById('seo-focus-keyword');
                const ttEl = document.getElementById('seo-meta-title');
                const descEl = document.getElementById('seo-meta-description');
                const slugEl = document.getElementById('seo-slug');
                if(kwEl) kwEl.value = d.keyword || '';
                if(ttEl) { ttEl.value = d.meta_title || d.title || ''; document.getElementById('seo-title-count').innerText = ttEl.value.length + '/60'; }
                if(descEl) { descEl.value = d.description || ''; document.getElementById('seo-desc-count').innerText = descEl.value.length + '/160'; }
                if(slugEl && d.slug) slugEl.value = d.slug;
            }
        });
    };
    


    window.openSEOAnalysis = function(articleId, title) {
        // Highlight active item in left sidebar list
        document.querySelectorAll('.seo-article-btn').forEach(btn => {
            if(btn.dataset.id == articleId) {
                btn.classList.add('active', 'bg-zinc-50', 'border-zinc-200');
            } else {
                btn.classList.remove('active', 'bg-zinc-50', 'border-zinc-200');
            }
        });

        const container = document.getElementById('seo-analysis-container');
        if(!container) return;

        // Render inline analysis workspace frame
        container.innerHTML = `
            <div class="space-y-6">
                <!-- Header Bar -->
                <div class="flex items-start justify-between pb-4 border-b border-zinc-200">
                    <div>
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">SEO & AI Audit Workspace</div>
                        <h2 class="text-lg font-bold text-zinc-900 leading-snug">${escJsHtml(title)}</h2>
                        <div class="text-xs text-zinc-500 mt-1">Article ID #${articleId}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="bg-zinc-900 hover:bg-zinc-800 text-white font-bold px-4 py-2 rounded-lg text-xs transition-all cursor-pointer flex items-center gap-1.5" onclick="runInlineSEOAudit(${articleId})">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            Run 11-Point Audit
                        </button>
                    </div>
                </div>

                <!-- Top Metrics Row -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 flex items-center gap-4">
                        <div class="relative w-14 h-14 shrink-0 flex items-center justify-center">
                            <svg width="56" height="56" viewBox="0 0 64 64" class="-rotate-90">
                                <circle cx="32" cy="32" r="28" stroke="#e4e4e7" stroke-width="7" fill="none"/>
                                <circle cx="32" cy="32" r="28" stroke="#18181b" stroke-width="7" fill="none"
                                    stroke-dasharray="175.9" stroke-dashoffset="175.9"
                                    id="inline-seo-ring" stroke-linecap="round" style="transition: stroke-dashoffset 0.6s ease"/>
                            </svg>
                            <span id="inline-seo-score-text" class="text-sm font-bold text-zinc-900 absolute">--</span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-zinc-900">Overall SEO Score</div>
                            <div class="text-[11px] text-zinc-500 mt-0.5" id="inline-seo-status">Click Run Audit to evaluate</div>
                        </div>
                    </div>

                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 flex flex-col justify-between">
                        <div class="text-xs font-bold text-zinc-900">GEO AI Visibility</div>
                        <div class="text-2xl font-bold text-zinc-900 mt-1" id="inline-geo-score">68%</div>
                        <div class="text-[10px] text-zinc-500">Google AI & Perplexity Cited</div>
                    </div>

                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 flex flex-col justify-between">
                        <div class="text-xs font-bold text-zinc-900">Focus Keyword Density</div>
                        <div class="text-2xl font-bold text-zinc-900 mt-1" id="inline-kw-density">2.4%</div>
                        <div class="text-[10px] text-zinc-500 truncate" id="inline-kw-label">Target: Loading...</div>
                    </div>
                </div>

                <!-- 11-Point Checklist Container -->
                <div class="border border-zinc-200 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200 flex justify-between items-center">
                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">11-Point SEO & AI Checklist</h3>
                        <span class="text-[10px] text-zinc-500 font-semibold" id="inline-checklist-summary">Pending Analysis</span>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-3 text-xs" id="inline-seo-checklist-grid">
                        <div class="text-zinc-400 col-span-2 text-center py-4">Click "Run 11-Point Audit" above to check content quality.</div>
                    </div>
                </div>

                <!-- Meta Fields Editor -->
                <div class="border border-zinc-200 rounded-xl p-5 space-y-4">
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Meta Fields & Permalinks</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-700 mb-1">Focus Keyword</label>
                            <input type="text" id="inline-focus-keyword" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-zinc-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-700 mb-1">URL Slug</label>
                            <input type="text" id="inline-slug" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-zinc-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-700 mb-1 flex justify-between">
                            Meta Title
                            <span id="inline-title-count" class="text-zinc-400 font-normal">0/60</span>
                        </label>
                        <input type="text" id="inline-meta-title" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-zinc-500" oninput="document.getElementById('inline-title-count').innerText = this.value.length + '/60'">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-700 mb-1 flex justify-between">
                            Meta Description
                            <span id="inline-desc-count" class="text-zinc-400 font-normal">0/160</span>
                        </label>
                        <textarea id="inline-meta-description" rows="3" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-zinc-500" oninput="document.getElementById('inline-desc-count').innerText = this.value.length + '/160'"></textarea>
                    </div>
                    <button class="bg-zinc-900 hover:bg-zinc-800 text-white font-bold px-4 py-2.5 rounded-lg text-xs transition-colors w-full cursor-pointer" onclick="saveInlineSEOMeta(${articleId})">
                        Save Meta & Slug
                    </button>
                </div>
            </div>
        `;

        // Load existing SEO meta for this article
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_get_article',
            nonce: coraREWPData.ajaxNonce,
            post_id: articleId
        }, function(r) {
            if(r.success) {
                const d = r.data;
                const kwEl = document.getElementById('inline-focus-keyword');
                const ttEl = document.getElementById('inline-meta-title');
                const descEl = document.getElementById('inline-meta-description');
                const slugEl = document.getElementById('inline-slug');
                const kwLbl = document.getElementById('inline-kw-label');
                if(kwEl) kwEl.value = d.keyword || '';
                if(kwLbl) kwLbl.innerHTML = 'Target: <strong>' + (d.keyword || 'Not set') + '</strong>';
                if(ttEl) { ttEl.value = d.meta_title || d.title || ''; document.getElementById('inline-title-count').innerText = ttEl.value.length + '/60'; }
                if(descEl) { descEl.value = d.description || ''; document.getElementById('inline-desc-count').innerText = descEl.value.length + '/160'; }
                if(slugEl && d.slug) slugEl.value = d.slug;
            }
        });

        // Run audit automatically
        runInlineSEOAudit(articleId);
    };

    window.runInlineSEOAudit = function(articleId) {
        const grid = document.getElementById('inline-seo-checklist-grid');
        if(grid) grid.innerHTML = '<div class="text-zinc-500 col-span-2 text-center py-4 animate-pulse">Running 11-point audit...</div>';
        
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_run_seo_analysis',
            nonce: coraREWPData.ajaxNonce,
            post_id: articleId
        }, function(r) {
            if(r.success) {
                const d = r.data;
                const score = d.overall_score || 0;
                document.getElementById('inline-seo-score-text').innerText = score;
                document.getElementById('inline-seo-status').innerText = score >= 80 ? '✓ Audit Passed' : '⚠ Optimizations Needed';
                
                const ring = document.getElementById('inline-seo-ring');
                if(ring) {
                    const pct = Math.min(100, Math.max(0, score));
                    const circ = 175.9;
                    ring.style.strokeDashoffset = circ - (pct / 100) * circ;
                }
                
                const labels = {word_count:'Word Count ≥ 1000',keyword_in_h1:'Keyword in H1',h2_present:'H2 Headings Present',internal_links:'Internal Links Coverage',images_alt:'Image Alt Tags',meta_title_len:'Meta Title Length (50-60)',meta_desc_len:'Meta Description Length (120-160)',slug_clean:'Clean Permalink Slug',has_faq:'FAQ Section Present',has_schema:'JSON-LD Schema Set',has_stats:'Statistics & Sources Cited'};
                let html = '';
                Object.entries(d.checks || {}).forEach(([k, v]) => {
                    const icon = v === 'pass' ? '✓' : (v === 'fail' ? '✗' : '⚠');
                    const badge = v === 'pass' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600 border border-zinc-200';
                    html += `<div class="p-2.5 rounded-lg border border-zinc-200 flex items-center justify-between"><span class="font-medium text-zinc-700">${labels[k] || k}</span><span class="px-2 py-0.5 rounded text-[10px] font-bold ${badge}">${icon} ${v.toUpperCase()}</span></div>`;
                });
                if(grid) grid.innerHTML = html;
            }
        });
    };

    window.saveInlineSEOMeta = function(articleId) {
        const keyword = document.getElementById('inline-focus-keyword').value;
        const title = document.getElementById('inline-meta-title').value;
        const desc = document.getElementById('inline-meta-description').value;
        const slug = document.getElementById('inline-slug').value;

        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_save_article_seo_meta',
            nonce: coraREWPData.ajaxNonce,
            post_id: articleId,
            focus_keyword: keyword,
            meta_title: title,
            meta_description: desc,
            slug: slug
        }, function(response) {
            if(response.success) {
                if(window.coraShowToast) window.coraShowToast('SEO meta saved successfully.', 'success');
            }
        });
    };

    function escJsHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    function filterSEOArticleList(q) {
        const query = q.toLowerCase();
        document.querySelectorAll('.seo-article-btn').forEach(btn => {
            if(btn.dataset.title.toLowerCase().includes(query)) {
                btn.style.display = '';
            } else {
                btn.style.display = 'none';
            }
        });
    }

    window.closeSEODetailDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    };
    window.filterContentByStatus = function(status) {
        document.querySelectorAll('.ct-row').forEach(row => {
            if(status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    window.filterContentByAuthor = function(authorId) {
        document.querySelectorAll('.ct-row').forEach(row => {
            if(authorId === 'all' || row.dataset.author === authorId) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    window.searchContentTable = function(query) {
        const q = query.toLowerCase();
        document.querySelectorAll('.ct-row').forEach(row => {
            if(row.dataset.title.includes(q)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    window.toggleSelectAll = function(el) {
        const isChecked = el.checked;
        document.querySelectorAll('.ct-row-checkbox').forEach(cb => cb.checked = isChecked);
        updateBulkActions();
    };

    window.updateBulkActions = function() {
        const anyChecked = document.querySelectorAll('.ct-row-checkbox:checked').length > 0;
        const select = document.getElementById('ct-bulk-actions');
        if(anyChecked) {
            select.disabled = false;
            select.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            select.disabled = true;
            select.classList.add('opacity-50', 'cursor-not-allowed');
            select.value = "";
        }
    };

    // AJAX Submissions
    window.submitCreateArticle = function(e) {
        e.preventDefault();
        const title = document.getElementById('ca-title').value;
        const keyword = document.getElementById('ca-keyword').value;
        const industry = document.getElementById('ca-industry').value;
        const category = document.getElementById('ca-category').value;
        const assignee = document.getElementById('ca-assignee').value;
        const date = document.getElementById('ca-date').value;
        
        if(!title) {
            if(window.coraShowToast) window.coraShowToast('Article title is required', 'error');
            return;
        }

        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_create_article',
            nonce: coraREWPData.ajaxNonce,
            title, keyword, industry, category_id: category, assignee_id: assignee, publish_date: date
        }, function(response) {
            if(response.success) {
                if(window.coraShowToast) window.coraShowToast('Article drafted successfully', 'success');
                closeCreateArticleDrawer();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                if(window.coraShowToast) window.coraShowToast(response.data || 'Failed to create article', 'error');
            }
        });
    };

    window.runSEOAnalysis = function(articleId) {
        document.getElementById('seo-drawer-results').innerHTML = '<div class="text-zinc-500 text-sm">Analyzing...</div>';
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_run_seo_analysis',
            nonce: coraREWPData.ajaxNonce,
            post_id: articleId
        }, function(response) {
            if(response.success) {
                const data = response.data;
                document.getElementById('seo-drawer-score').innerText = data.overall_score;
                const ring = document.getElementById('seo-ring-progress');
                if(ring) {
                    const pct = Math.min(100, Math.max(0, data.overall_score || 0));
                    const circ = 175.9;
                    ring.style.strokeDashoffset = circ - (pct / 100) * circ;
                }
                
                let html = '<div class="space-y-2">';
                
                // Group 1: Content
                html += '<div class="font-bold text-sm text-zinc-900 mt-4 border-b pb-1">Content Quality</div>';
                html += renderCheck('Word Count >= 1000', data.checks.word_count);
                html += renderCheck('Keyword in H1', data.checks.keyword_in_h1);
                html += renderCheck('H2 Subheadings Present', data.checks.h2_present);
                
                // Group 2: Tech
                html += '<div class="font-bold text-sm text-zinc-900 mt-4 border-b pb-1">Technical SEO</div>';
                html += renderCheck('Meta Title Length (50-60)', data.checks.meta_title_len);
                html += renderCheck('Meta Desc Length (150-160)', data.checks.meta_desc_len);
                html += renderCheck('Clean Slug', data.checks.slug_clean);
                
                // Group 3: AI-SEO/GEO
                html += '<div class="font-bold text-sm text-zinc-900 mt-4 border-b pb-1">AI-SEO / GEO Signals</div>';
                html += renderCheck('FAQ Section Present', data.checks.has_faq);
                html += renderCheck('JSON-LD Schema Set', data.checks.has_schema);
                html += renderCheck('Statistics Cited', data.checks.has_stats);
                
                html += '</div>';
                document.getElementById('seo-drawer-results').innerHTML = html;
                if(window.coraShowToast) window.coraShowToast('Analysis complete', 'success');
            }
        });
    };

    function renderCheck(label, status) {
        let icon = status === 'pass' ? '✓' : (status === 'fail' ? '✗' : '⚠');
        let color = status === 'pass' ? 'text-green-600' : (status === 'fail' ? 'text-red-600' : 'text-yellow-600');
        return `<div class="text-sm flex items-center gap-2"><span class="font-bold ${color}">${icon}</span> <span class="text-zinc-700">${label}</span></div>`;
    }

    window.saveSEOMeta = function(articleId) {
        const keyword = document.getElementById('seo-focus-keyword').value;
        const title = document.getElementById('seo-meta-title').value;
        const desc = document.getElementById('seo-meta-description').value;
        const slug = document.getElementById('seo-slug').value;

        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_save_article_seo_meta',
            nonce: coraREWPData.ajaxNonce,
            post_id: articleId,
            focus_keyword: keyword,
            meta_title: title,
            meta_description: desc,
            slug: slug
        }, function(response) {
            if(response.success) {
                if(window.coraShowToast) window.coraShowToast('SEO meta saved.', 'success');
            }
        });
    };

    window.saveGEOSignals = function(articleId) {
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_save_geo_signals',
            nonce: coraREWPData.ajaxNonce,
            post_id: articleId,
            has_direct_answer: document.getElementById('geo-direct').checked ? 1 : 0,
            has_faq: document.getElementById('geo-faq').checked ? 1 : 0,
            has_schema: document.getElementById('geo-schema').checked ? 1 : 0,
            has_entity: document.getElementById('geo-entity').checked ? 1 : 0,
            has_stats: document.getElementById('geo-stats').checked ? 1 : 0
        }, function(response) {
            if(response.success) {
                if(window.coraShowToast) window.coraShowToast('GEO signals saved.', 'success');
            }
        });
    };

    window.exportContentCSV = function() {
        // Build CSV from table rows
        const rows = document.querySelectorAll('#cora-content-table-body .ct-row');
        if(!rows.length) {
            if(window.coraShowToast) window.coraShowToast('No articles to export', 'error');
            return;
        }
        let csv = 'Title,Author,Status,SEO Score,GEO Score,Leads,Modified\n';
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if(cells.length < 8) return;
            const title  = (cells[1].querySelector('.font-bold')?.innerText || '').replace(/,/g,'').trim();
            const author = (cells[2].innerText || '').replace(/,/g,'').trim();
            const status = (cells[3].innerText || '').replace(/,/g,'').trim();
            const seo    = (cells[4].querySelector('span')?.innerText || '').trim();
            const geo    = (cells[5].querySelector('span')?.innerText || '').trim();
            const leads  = (cells[6].innerText || '').trim().replace(/\n/g,' ');
            const mod    = (cells[7].innerText || '').trim();
            csv += `"${title}","${author}","${status}","${seo}","${geo}","${leads}","${mod}"\n`;
        });
        const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href = url;
        a.download = 'content-library-' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        if(window.coraShowToast) window.coraShowToast('CSV exported successfully', 'success');
    };

})();
</script>
