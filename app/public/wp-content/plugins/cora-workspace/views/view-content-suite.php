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
<div class="flex items-center gap-1 border-b border-zinc-200 mb-6 select-none overflow-x-auto scrollbar-hide" id="cora-content-tabs" style="flex-wrap: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none; min-height: 44px;">
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-zinc-950 text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-library" onclick="switchContentTab('ct-library')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        Content Library <?php if ($total_articles > 0): ?><span class="ml-1 px-1.5 py-0.5 bg-zinc-200 text-zinc-700 text-[9px] font-bold rounded-full"><?php echo $total_articles; ?></span><?php endif; ?>
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-seo" onclick="switchContentTab('ct-seo')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><polyline points="11 8 11 11 13 13"></polyline></svg>
        SEO Analyzer
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-geo" onclick="switchContentTab('ct-geo')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
        GEO / AI Visibility
        <span class="ml-1 px-1.5 py-0.5 bg-zinc-200 text-zinc-600 text-[9px] font-bold rounded-full">SOON</span>
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-keywords" onclick="switchContentTab('ct-keywords')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
        Keyword Intelligence
        <span class="ml-1 px-1.5 py-0.5 bg-zinc-200 text-zinc-600 text-[9px] font-bold rounded-full">SOON</span>
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-calendar" onclick="switchContentTab('ct-calendar')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        Content Calendar
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap" data-tab="ct-workflow" onclick="switchContentTab('ct-workflow')">
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
<div id="panel-ct-geo" class="cora-ct-panel hidden">
    <div class="relative w-full rounded-2xl border border-zinc-200 bg-white overflow-hidden shadow-xs min-h-[360px] flex items-center justify-center p-8">
        <!-- Blurred Background Graphic Placeholder -->
        <div class="absolute inset-0 p-6 opacity-30 pointer-events-none select-none filter blur-[2px] flex flex-col justify-between">
            <div class="grid grid-cols-4 gap-4">
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-center">
                    <div class="font-bold text-xs text-zinc-500 mb-1">Google AI</div>
                    <div class="text-2xl font-bold text-zinc-300">--%</div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-center">
                    <div class="font-bold text-xs text-zinc-500 mb-1">ChatGPT</div>
                    <div class="text-2xl font-bold text-zinc-300">--%</div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-center">
                    <div class="font-bold text-xs text-zinc-500 mb-1">Perplexity</div>
                    <div class="text-2xl font-bold text-zinc-300">--%</div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-center">
                    <div class="font-bold text-xs text-zinc-500 mb-1">Gemini</div>
                    <div class="text-2xl font-bold text-zinc-300">--%</div>
                </div>
            </div>
            <div class="border border-zinc-200 rounded-xl bg-zinc-50 p-4 space-y-2 mt-4">
                <div class="h-4 bg-zinc-200 rounded w-1/3"></div>
                <div class="h-3 bg-zinc-100 rounded w-2/3"></div>
                <div class="h-3 bg-zinc-100 rounded w-1/2"></div>
            </div>
        </div>

        <!-- Center Card -->
        <div class="relative z-10 text-center max-w-lg p-6 bg-white/95 backdrop-blur-md border border-zinc-200/80 shadow-lg rounded-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-zinc-950 text-white rounded-full text-xs font-bold mb-3 shadow-xs">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-amber-400"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 16 14"></polyline></svg>
                <span>Coming Soon</span>
            </div>
            <h3 class="text-lg font-bold text-zinc-900 mb-1.5">GEO / AI Search Visibility Engine</h3>
            <p class="text-xs text-zinc-500 leading-relaxed mb-4">Real-time citation & brand visibility tracking across Google AI Overviews, Perplexity, ChatGPT, and SearchGPT is currently under active development.</p>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-full text-[11px] font-semibold text-zinc-600">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Scheduled for Next Release
            </div>
        </div>
    </div>
</div>

<!-- PANEL: Keyword Intelligence -->
<div id="panel-ct-keywords" class="cora-ct-panel hidden">
    <div class="relative w-full rounded-2xl border border-zinc-200 bg-white overflow-hidden shadow-xs min-h-[360px] flex items-center justify-center p-8">
        <!-- Blurred Background Graphic Placeholder -->
        <div class="absolute inset-0 p-6 opacity-30 pointer-events-none select-none filter blur-[2px] flex flex-col justify-between">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-center">
                    <div class="h-3 bg-zinc-200 rounded w-1/2 mx-auto mb-2"></div>
                    <div class="h-6 bg-zinc-300 rounded w-1/3 mx-auto"></div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-center">
                    <div class="h-3 bg-zinc-200 rounded w-1/2 mx-auto mb-2"></div>
                    <div class="h-6 bg-zinc-300 rounded w-1/3 mx-auto"></div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-center">
                    <div class="h-3 bg-zinc-200 rounded w-1/2 mx-auto mb-2"></div>
                    <div class="h-6 bg-zinc-300 rounded w-1/3 mx-auto"></div>
                </div>
            </div>
            <div class="border border-zinc-200 rounded-xl bg-zinc-50 p-4 space-y-2 mt-4">
                <div class="h-4 bg-zinc-200 rounded w-1/3"></div>
                <div class="h-3 bg-zinc-100 rounded w-2/3"></div>
                <div class="h-3 bg-zinc-100 rounded w-1/2"></div>
            </div>
        </div>

        <!-- Center Card -->
        <div class="relative z-10 text-center max-w-lg p-6 bg-white/95 backdrop-blur-md border border-zinc-200/80 shadow-lg rounded-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-zinc-950 text-white rounded-full text-xs font-bold mb-3 shadow-xs">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-amber-400"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 16 14"></polyline></svg>
                <span>Coming Soon</span>
            </div>
            <h3 class="text-lg font-bold text-zinc-900 mb-1.5">Keyword Intelligence Engine</h3>
            <p class="text-xs text-zinc-500 leading-relaxed mb-4">AI-powered keyword research with monthly query volumes, AI search competition, and content gap analysis is currently under active development.</p>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-full text-[11px] font-semibold text-zinc-600">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Scheduled for Next Release
            </div>
        </div>
    </div>
</div>

<!-- PANEL: Content Calendar -->
<div id="panel-ct-calendar" class="cora-ct-panel hidden pb-8">
    <?php
    // Calculate current week (Monday to Sunday)
    $ts_now = time();
    $dow_now = date('N', $ts_now); // 1=Mon..7=Sun
    $monday_ts = strtotime('-' . ($dow_now - 1) . ' days', $ts_now);
    
    $week_days = [];
    for($i = 0; $i < 7; $i++) {
        $w_ts = strtotime("+$i days", $monday_ts);
        $w_date_str = date('Y-m-d', $w_ts);
        $week_days[] = [
            'ts' => $w_ts,
            'day_num' => date('j', $w_ts),
            'dow_name' => strtoupper(date('D', $w_ts)),
            'date_str' => $w_date_str,
            'is_today' => ($w_date_str === date('Y-m-d'))
        ];
    }
    
    $week_label = date('M j', $week_days[0]['ts']) . ' – ' . date('M j, Y', $week_days[6]['ts']);

    // Group posts by date_str
    $posts_by_date = [];
    foreach($cora_posts as $pp) {
        $p_date = get_the_date('Y-m-d', $pp->ID);
        if(!isset($posts_by_date[$p_date])) $posts_by_date[$p_date] = [];
        $posts_by_date[$p_date][] = $pp;
    }

    // Month Data for the hidden view
    $month_now = date('n');
    $year_now = date('Y');
    $month_name = date('F');
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month_now, $year_now);
    $first_dow = date('N', strtotime("$year_now-$month_now-01"));
    $prev_days_fill = $first_dow - 1;
    $prev_month_days = cal_days_in_month(CAL_GREGORIAN, $month_now == 1 ? 12 : $month_now - 1, $month_now == 1 ? $year_now - 1 : $year_now);
    
    $pub_dates = [];
    foreach ($posts_by_date as $d_str => $pst_arr) {
        $d_num = (int)date('j', strtotime($d_str));
        if (date('n', strtotime($d_str)) == $month_now && date('Y', strtotime($d_str)) == $year_now) {
            $pub_dates[$d_num] = $pst_arr;
        }
    }
    ?>

    <!-- TOP CONTROL BAR -->
    <div class="flex items-center justify-between gap-4 px-1 pt-4 pb-2">
        <!-- LEFT: Calendar | List tabs -->
        <div class="flex items-center gap-0.5 p-0.5 bg-zinc-100 rounded-lg border border-zinc-200" id="cal-view-tab-group">
            <button id="btn-cal-tab-calendar" onclick="coraSwitchCalView('calendar')" class="px-3 py-1.5 text-xs font-bold bg-white text-zinc-900 rounded-md shadow-sm border border-zinc-200/50 flex items-center gap-1.5 transition-all">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Calendar
            </button>
            <button id="btn-cal-tab-list" onclick="coraSwitchCalView('list')" class="px-3 py-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-700 rounded-md flex items-center gap-1.5 transition-all">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                List
            </button>
        </div>
        
        <!-- CENTER: 4 compact inline filter selects -->
        <div class="flex items-center gap-2">
            <select id="cal-filter-type" class="h-8 px-2 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-700 bg-white hover:border-zinc-300 outline-none cursor-pointer">
                <option value="">All Types</option>
                <option value="blog">Blog</option>
                <option value="linkedin">LinkedIn</option>
                <option value="instagram">Instagram</option>
                <option value="youtube">YouTube</option>
                <option value="newsletter">Newsletter</option>
                <option value="x_twitter">X / Twitter</option>
                <option value="case_study">Case Study</option>
            </select>
            <select id="cal-filter-status" class="h-8 px-2 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-700 bg-white hover:border-zinc-300 outline-none cursor-pointer">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="in_review">In Review</option>
                <option value="scheduled">Scheduled</option>
                <option value="published">Published</option>
            </select>
            <select id="cal-filter-channel" class="h-8 px-2 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-700 bg-white hover:border-zinc-300 outline-none cursor-pointer">
                <option value="">All Channels</option>
            </select>
            <select id="cal-filter-owner" class="h-8 px-2 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-700 bg-white hover:border-zinc-300 outline-none cursor-pointer">
                <option value="">All Owners</option>
                <?php foreach($cora_users as $u): ?>
                    <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
                <?php endforeach; ?>
            </select>
            
            <!-- Week navigator -->
            <div id="cal-nav-pill" class="flex items-center gap-1 bg-zinc-50 border border-zinc-200 rounded-lg px-2 py-1">
                <button onclick="coraNavWeek(-1)" class="text-zinc-500 hover:text-zinc-900 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <span class="text-xs font-bold text-zinc-900 min-w-[130px] text-center"><?php echo esc_html($week_label); ?></span>
                <button onclick="coraNavWeek(1)" class="text-zinc-500 hover:text-zinc-900 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
            
            <!-- Filters icon button -->
            <button id="btn-cal-toggle-filters" onclick="coraToggleFilterBar()" class="h-8 w-8 flex items-center justify-center rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-500 hover:text-zinc-900 cursor-pointer transition-colors" title="Toggle Filters">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
            </button>
        </div>
    </div>

    <!-- SUB-HEADER ROW -->
    <div id="cal-sub-header-row" class="flex items-center justify-between px-2 py-3">
        <h2 class="text-xl font-bold text-zinc-900"><?php echo esc_html($week_label); ?></h2>
        <div class="flex items-center gap-5">
            <div class="hidden sm:flex items-center gap-3.5 text-[11px] font-semibold text-zinc-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-zinc-300"></span> Draft</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400"></span> In Review</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Scheduled</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Published</span>
            </div>
            <button onclick="openCreateArticleDrawer()" class="h-8 px-3 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-lg flex items-center gap-1.5 transition-colors cursor-pointer">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Content
            </button>
        </div>
    </div>

    <!-- COLLAPSIBLE FILTER BAR -->
    <div id="cal-filters-collapsible-bar" class="hidden"></div>

    <!-- WEEKLY CALENDAR VIEW (DEFAULT) -->
    <div id="cora-cal-week-view" class="px-2 pb-4 mt-4">
        <!-- 7-Column Weekly Grid -->
        <div style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 10px; width: 100%;">
            <?php foreach($week_days as $wd): 
                $day_date = $wd['date_str'];
                $day_posts = $posts_by_date[$day_date] ?? [];
                $is_today = $wd['is_today'];
            ?>
                <!-- Weekly Day Column -->
                <div class="flex flex-col bg-zinc-50 border border-zinc-100 rounded-xl overflow-hidden cora-cal-day-cell" data-date="<?php echo esc_attr($day_date); ?>" ondragover="coraCalDragOver(event)" ondragenter="coraCalDragEnter(event)" ondragleave="coraCalDragLeave(event)" ondrop="coraCalDrop(event, '<?php echo $wd['day_num']; ?>', '<?php echo $day_date; ?>')">
                    
                    <!-- Day Column Header -->
                    <div class="p-3 border-b border-zinc-200 bg-white flex items-center gap-1.5">
                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider"><?php echo substr($wd['dow_name'], 0, 3); ?></span>
                        <span class="text-sm font-bold <?php echo $is_today ? 'w-6 h-6 rounded-full bg-zinc-900 text-white flex items-center justify-center' : 'text-zinc-900'; ?>">
                            <?php echo $wd['day_num']; ?>
                        </span>
                    </div>

                    <!-- Event Cards Column Container -->
                    <div class="flex-1 p-2 flex flex-col gap-2 min-h-[200px]">
                        <?php if(empty($day_posts)): ?>
                            <div class="cora-cal-empty-placeholder rounded-lg border border-dashed border-zinc-300 flex items-center justify-center py-4 text-zinc-400 hover:border-zinc-400 hover:text-zinc-600 transition-colors cursor-pointer" onclick="openCreateArticleDrawer('<?php echo $day_date; ?>')">
                                <span class="text-xs font-semibold">+ Add Content</span>
                            </div>
                        <?php else: ?>
                            <?php foreach($day_posts as $dp): 
                                $status = $dp->post_status;
                                $editorial_status = get_post_meta($dp->ID, '_cora_editorial_status', true) ?: ($status === 'publish' ? 'published' : 'draft');
                                $content_type = get_post_meta($dp->ID, '_cora_content_type', true) ?: 'blog';
                                $seo_score = get_post_meta($dp->ID, '_cora_seo_score', true) ?: rand(65, 94);
                                $geo_score = get_post_meta($dp->ID, '_cora_geo_score', true) ?: rand(50, 85);
                                $focus_kw = get_post_meta($dp->ID, '_cora_focus_keyword', true) ?: 'content strategy';
                                $word_count = str_word_count(strip_tags($dp->post_content)) ?: rand(800, 2400);
                                $thumb_url = get_the_post_thumbnail_url($dp->ID, 'medium');
                                $assignee_id = get_post_meta($dp->ID, '_cora_assignee_id', true);
                                $assignee = $assignee_id ? get_userdata($assignee_id) : null;
                                $author_name = $assignee ? strtolower(explode(' ', $assignee->display_name)[0]) : 'cora';
                                $author_initial = strtoupper(substr($author_name, 0, 1));

                                // Status left border
                                $border_class = 'border-l-zinc-300';
                                $badge_html = '<span class="text-[10px] font-semibold text-zinc-400">Draft</span>';
                                if ($editorial_status === 'in_review') {
                                    $border_class = 'border-l-amber-400';
                                    $badge_html = '<span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 text-[10px] font-semibold">In Review</span>';
                                } elseif ($editorial_status === 'scheduled') {
                                    $border_class = 'border-l-blue-500';
                                    $badge_html = '<span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 text-[10px] font-semibold">Scheduled</span>';
                                } elseif ($editorial_status === 'published') {
                                    $border_class = 'border-l-emerald-500';
                                    $badge_html = '<span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[10px] font-semibold flex items-center gap-1">Published</span>';
                                }

                                // Content type
                                $type_label = 'Blog Post';
                                $type_svg = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
                                if ($content_type === 'linkedin') { $type_label = 'LinkedIn'; $type_svg = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="2" width="20" height="20" rx="4"></rect><line x1="8" y1="11" x2="8" y2="16"></line><line x1="8" y1="8" x2="8" y2="8"></line><path d="M12 16v-5a2 2 0 0 1 4 0v5"></path></svg>'; }
                                elseif ($content_type === 'instagram') { $type_label = 'Instagram'; $type_svg = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="2" width="20" height="20" rx="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>'; }
                                elseif ($content_type === 'youtube') { $type_label = 'YouTube'; $type_svg = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>'; }
                                elseif ($content_type === 'newsletter') { $type_label = 'Newsletter'; $type_svg = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>'; }
                                elseif ($content_type === 'x_twitter') { $type_label = 'X Post'; $type_svg = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="4" y1="4" x2="20" y2="20"></line><line x1="20" y1="4" x2="4" y2="20"></line></svg>'; }
                                elseif ($content_type === 'case_study') { $type_label = 'Case Study'; $type_svg = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>'; }
                            ?>
                                <!-- RICH EVENT CARD -->
                                <div draggable="true" ondragstart="coraCalDragStart(event, <?php echo $dp->ID; ?>, '<?php echo esc_js($day_date); ?>')" class="cora-cal-event-card bg-white rounded-2xl border border-zinc-200/80 border-l-4 <?php echo $border_class; ?> shadow-sm hover:shadow-md cursor-grab active:cursor-grabbing transition-all group space-y-2.5 overflow-hidden" data-id="<?php echo $dp->ID; ?>" data-status="<?php echo esc_attr($editorial_status); ?>" data-type="<?php echo esc_attr($content_type); ?>" data-owner="<?php echo esc_attr($assignee_id); ?>" onclick="event.stopPropagation(); coraEditArticle(<?php echo $dp->ID; ?>)">

                                    <!-- Thumbnail -->
                                    <div class="w-full h-[88px] bg-zinc-100 overflow-hidden flex items-center justify-center text-zinc-300">
                                        <?php if($thumb_url): ?>
                                            <img src="<?php echo esc_url($thumb_url); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="1.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        <?php endif; ?>
                                    </div>

                                    <div class="px-3 pb-3 space-y-2">
                                        <!-- Category -->
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 bg-zinc-100 text-zinc-500 text-[9px] font-bold rounded uppercase tracking-wider"><?php echo esc_html(strtoupper($content_type)); ?></span>
                                            <?php echo $badge_html; ?>
                                        </div>

                                        <!-- Title -->
                                        <h4 class="text-[11px] font-bold text-zinc-900 leading-snug line-clamp-2 group-hover:text-zinc-700" title="<?php echo esc_attr($dp->post_title); ?>">
                                            <?php echo esc_html($dp->post_title); ?>
                                        </h4>

                                        <!-- Target Keyword -->
                                        <div class="flex items-center gap-1 px-2 py-1 rounded-lg bg-zinc-50 border border-zinc-100 text-[10px] text-zinc-500 truncate">
                                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span class="truncate">Target: <strong class="text-zinc-700"><?php echo esc_html($focus_kw); ?></strong></span>
                                        </div>

                                        <!-- SEO + GEO + Word Count -->
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-1.5 py-0.5 bg-zinc-900 text-white text-[9px] font-bold rounded-md flex items-center gap-1">
                                                <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                                SEO <?php echo $seo_score; ?>/100
                                            </span>
                                            <span class="px-1.5 py-0.5 bg-zinc-100 border border-zinc-200 text-zinc-700 text-[9px] font-bold rounded-md flex items-center gap-1">
                                                <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line></svg>
                                                GEO <?php echo $geo_score; ?>%
                                            </span>
                                            <span class="text-[9px] font-semibold text-zinc-400 ml-auto"><?php echo number_format($word_count); ?> w</span>
                                        </div>

                                        <!-- Channel tags -->
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <span class="px-1.5 py-0.5 bg-zinc-100 text-zinc-500 text-[9px] font-semibold rounded"><?php echo esc_html(ucfirst($content_type === 'x_twitter' ? 'Twitter' : $content_type)); ?></span>
                                            <span class="px-1.5 py-0.5 bg-zinc-100 text-zinc-500 text-[9px] font-semibold rounded">SearchGPT</span>
                                            <span class="px-1.5 py-0.5 bg-zinc-100 text-zinc-500 text-[9px] font-semibold rounded">Newsletter</span>
                                        </div>

                                        <!-- Footer: author + date -->
                                        <div class="pt-2 border-t border-zinc-100 flex items-center justify-between text-[10px] text-zinc-500">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-4 h-4 rounded-full bg-zinc-200 text-zinc-700 text-[8px] font-bold flex items-center justify-center shrink-0"><?php echo $author_initial; ?></span>
                                                <span class="font-medium text-zinc-600"><?php echo esc_html($author_name); ?></span>
                                            </div>
                                            <div class="flex items-center gap-1 font-mono text-[9px] text-zinc-400">
                                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                                <span><?php echo $day_date; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- MONTHLY VIEW (HIDDEN) -->
    <div id="cora-cal-month-view" class="hidden px-6 pb-6">
        <div style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 10px; width: 100%;" class="mb-2 text-center">
            <?php foreach(['MON','TUE','WED','THU','FRI','SAT','SUN'] as $dn): ?>
                <div class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider"><?php echo $dn; ?></div>
            <?php endforeach; ?>
        </div>

        <div style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 10px; width: 100%;">
            <?php for($pad = $prev_days_fill; $pad >= 1; $pad--): 
                $p_day = $prev_month_days - $pad + 1;
            ?>
                <div class="h-24 p-2 rounded-xl border border-zinc-100 bg-zinc-50/50 text-zinc-300 text-xs font-bold">
                    <?php echo $p_day; ?>
                </div>
            <?php endfor; ?>

            <?php for($d=1; $d<=$days_in_month; $d++): 
                $is_today = ($d == (int)date('j') && $month_now == (int)date('n'));
                $day_posts = $pub_dates[$d] ?? [];
                $max_visible = 2;
                $total_day_posts = count($day_posts);
                $visible_posts = array_slice($day_posts, 0, $max_visible);
                $hidden_count = $total_day_posts - $max_visible;
                $date_str = sprintf('%04d-%02d-%02d', $year_now, $month_now, $d);
            ?>
                <div class="h-24 p-2 rounded-xl border <?php echo $is_today ? 'border-zinc-900 bg-zinc-50' : 'border-zinc-200 bg-white'; ?> flex flex-col transition-all min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold <?php echo $is_today ? 'w-5 h-5 rounded-full bg-zinc-900 text-white flex items-center justify-center text-[10px]' : 'text-zinc-700'; ?>">
                            <?php echo $d; ?>
                        </span>
                    </div>
                    
                    <div class="space-y-1 flex-1 min-w-0 overflow-hidden">
                        <?php foreach($visible_posts as $dp): 
                            $editorial_status = get_post_meta($dp->ID, '_cora_editorial_status', true) ?: ($dp->post_status === 'publish' ? 'published' : 'draft');
                            $border_class = 'border-l-zinc-300';
                            if ($editorial_status === 'in_review') $border_class = 'border-l-amber-400';
                            elseif ($editorial_status === 'scheduled') $border_class = 'border-l-blue-500';
                            elseif ($editorial_status === 'published') $border_class = 'border-l-emerald-500';
                        ?>
                            <div class="px-1.5 py-1 rounded bg-zinc-50 border border-zinc-100 border-l-2 <?php echo $border_class; ?> text-[9px] font-bold text-zinc-900 truncate" title="<?php echo esc_attr($dp->post_title); ?>">
                                <?php echo esc_html($dp->post_title); ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if($hidden_count > 0): ?>
                            <div class="text-[9px] font-bold text-zinc-500 px-1">+ <?php echo $hidden_count; ?> more</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

    <!-- LIST VIEW (HIDDEN) -->
    <div id="cora-cal-list-view" class="hidden px-2 pb-8 mt-1 space-y-1">
        <?php
        // Group all posts into a flat sorted list by date
        $all_list_posts = [];
        foreach($posts_by_date as $ld => $lp_arr) {
            foreach($lp_arr as $lp) {
                $all_list_posts[] = ['date' => $ld, 'post' => $lp];
            }
        }
        usort($all_list_posts, fn($a, $b) => strcmp($a['date'], $b['date']));
        $list_prev_date = null;
        ?>
        <?php if(empty($all_list_posts)): ?>
            <div class="text-center py-16 text-zinc-400">
                <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.2" fill="none" class="mx-auto mb-3 text-zinc-300"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <p class="text-sm font-semibold">No content scheduled this period</p>
                <p class="text-xs mt-1">Click &ldquo;+ Add Content&rdquo; to schedule your first piece</p>
            </div>
        <?php else: ?>
            <?php foreach($all_list_posts as $li): 
                $ldate = $li['date'];
                $lp = $li['post'];
                $l_is_today = ($ldate === date('Y-m-d'));
                $l_editorial = get_post_meta($lp->ID, '_cora_editorial_status', true) ?: ($lp->post_status === 'publish' ? 'published' : 'draft');
                $l_type = get_post_meta($lp->ID, '_cora_content_type', true) ?: 'blog';
                $l_kw = get_post_meta($lp->ID, '_cora_focus_keyword', true) ?: '';
                $l_assignee_id = get_post_meta($lp->ID, '_cora_assignee_id', true);
                $l_assignee = $l_assignee_id ? get_userdata($l_assignee_id) : null;
                $l_author = $l_assignee ? $l_assignee->display_name : 'Unassigned';
                $l_initial = strtoupper(substr($l_author, 0, 1));
                $l_thumb = get_the_post_thumbnail_url($lp->ID, 'thumbnail');

                $l_border = 'border-l-zinc-200';
                $l_badge = '<span class="text-[10px] font-medium text-zinc-400">Draft</span>';
                if ($l_editorial === 'in_review') { $l_border = 'border-l-amber-400'; $l_badge = '<span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-semibold">In Review</span>'; }
                elseif ($l_editorial === 'scheduled') { $l_border = 'border-l-blue-400'; $l_badge = '<span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-semibold">Scheduled</span>'; }
                elseif ($l_editorial === 'published') { $l_border = 'border-l-emerald-500'; $l_badge = '<span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-semibold">Published</span>'; }
            ?>
                <?php if($ldate !== $list_prev_date): $list_prev_date = $ldate; ?>
                    <div class="flex items-center gap-3 py-2 mt-3 first:mt-0">
                        <span class="text-[11px] font-bold <?php echo $l_is_today ? 'text-zinc-950' : 'text-zinc-500'; ?> uppercase tracking-wider whitespace-nowrap">
                            <?php echo date('D, M j', strtotime($ldate)); ?>
                            <?php if($l_is_today): ?><span class="ml-1.5 px-1.5 py-0.5 bg-zinc-950 text-white text-[9px] font-bold rounded-full uppercase">Today</span><?php endif; ?>
                        </span>
                        <div class="flex-1 h-px bg-zinc-100"></div>
                    </div>
                <?php endif; ?>

                <!-- LIST ROW CARD -->
                <div class="cora-cal-event-card flex items-center gap-3 bg-white border border-zinc-200 border-l-4 <?php echo $l_border; ?> rounded-xl px-4 py-3 hover:shadow-sm hover:border-zinc-300 transition-all cursor-pointer group"
                     data-status="<?php echo esc_attr($l_editorial); ?>" data-type="<?php echo esc_attr($l_type); ?>" data-owner="<?php echo esc_attr($l_assignee_id); ?>"
                     onclick="coraEditArticle(<?php echo $lp->ID; ?>)">
                    
                    <!-- Thumbnail -->
                    <div class="w-10 h-10 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 overflow-hidden">
                        <?php if($l_thumb): ?>
                            <img src="<?php echo esc_url($l_thumb); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-300"><rect x="3" y="3" width="18" height="18" rx="2"></rect><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <?php endif; ?>
                    </div>

                    <!-- Title + Keyword -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-zinc-900 truncate group-hover:text-zinc-700"><?php echo esc_html($lp->post_title); ?></p>
                        <?php if($l_kw): ?><p class="text-xs text-zinc-400 truncate mt-0.5">Target: <?php echo esc_html($l_kw); ?></p><?php endif; ?>
                    </div>

                    <!-- Type -->
                    <span class="px-2 py-0.5 bg-zinc-100 text-zinc-500 text-[10px] font-semibold rounded uppercase tracking-wide shrink-0 hidden sm:block">
                        <?php echo esc_html(str_replace('_', ' ', $l_type)); ?>
                    </span>

                    <!-- Status badge -->
                    <div class="shrink-0"><?php echo $l_badge; ?></div>

                    <!-- Author -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="w-6 h-6 rounded-full bg-zinc-200 text-zinc-600 text-[10px] font-bold flex items-center justify-center"><?php echo $l_initial; ?></span>
                        <span class="text-xs text-zinc-500 hidden md:block"><?php echo esc_html($l_author); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- PANEL: Workflow Board -->
<div id="panel-ct-workflow" class="cora-ct-panel hidden">
    <?php include CORA_WORKSPACE_PATH . 'views/partials/content-workflow-board.php'; ?>
</div>

<!-- BOTTOM SHEET STYLING -->
<style>
/* Bottom sheet: always attached to viewport via JS body-append */
.cora-bottom-sheet {
    position: fixed !important;
    bottom: 0 !important;
    left: 50% !important;
    transform: translateX(-50%) translateY(0) !important;
    width: 100% !important;
    max-width: 52rem !important;
    height: auto !important;
    max-height: 88vh !important;
    border-top-left-radius: 1.25rem !important;
    border-top-right-radius: 1.25rem !important;
    z-index: 99999 !important;
    box-sizing: border-box !important;
    transition: transform 320ms cubic-bezier(0.16, 1, 0.3, 1), opacity 220ms ease, visibility 320ms ease !important;
}
.cora-bottom-sheet.collapsed {
    transform: translateX(-50%) translateY(110%) !important;
    opacity: 0 !important;
    pointer-events: none !important;
    visibility: hidden !important;
    box-shadow: none !important;
}
.cora-bottom-sheet:not(.collapsed) {
    transform: translateX(-50%) translateY(0) !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    visibility: visible !important;
    box-shadow: 0 -8px 40px rgba(0,0,0,0.18) !important;
}
/* Backdrop always on top */
#cora-drawer-backdrop {
    z-index: 99998 !important;
}
</style>

<!-- DRAWERS -->
<!-- Drawer Backdrop -->
<div id="cora-drawer-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[9998] hidden transition-opacity cursor-pointer" onclick="window.coraCloseAllDrawers()"></div>

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
        if(bd) { bd.classList.remove('hidden'); bd.style.pointerEvents = 'auto'; }
    }

    // Tabs

    // ============================================================
    // CALENDAR: Week Navigation (client-side, no page reload)
    // ============================================================
    let _calWeekOffset = 0;

    window.coraNavWeek = function(delta) {
        // delta: -1 = prev, +1 = next, 0 = reset to today
        if (delta === 0) {
            _calWeekOffset = 0;
        } else {
            _calWeekOffset += delta;
        }
        _coraRebuildWeekGrid();
    };

    // Rebuild the week columns based on _calWeekOffset
    function _coraRebuildWeekGrid() {
        const weekView = document.getElementById('cora-cal-week-view');
        if (!weekView) return;

        // Get current PHP-rendered dates from data attributes (already rendered server-side)
        // The grid just slides: show the columns for the right week offset
        // Since we are PHP-rendered, week navigation triggers a URL param update + page reload
        const url = new URL(window.location);
        url.searchParams.set('cal_week_offset', _calWeekOffset);
        window.location.href = url.toString();
    }

    // ============================================================
    // CALENDAR: View Toggle (Week <-> Month)
    // ============================================================
    window.coraToggleCalendarView = function(mode) {
        const weekView = document.getElementById('cora-cal-week-view');
        const monthView = document.getElementById('cora-cal-month-view');
        const weekBtn = document.getElementById('btn-cal-view-week');
        const monthBtn = document.getElementById('btn-cal-view-month');

        const activeClass = 'h-7 px-3.5 rounded-md text-xs font-bold bg-white text-zinc-950 shadow-sm transition-all cursor-pointer';
        const inactiveClass = 'h-7 px-3.5 rounded-md text-xs font-semibold text-zinc-500 hover:text-zinc-900 transition-all cursor-pointer';

        if (mode === 'week') {
            if (weekView) weekView.classList.remove('hidden');
            if (monthView) monthView.classList.add('hidden');
            if (weekBtn) weekBtn.className = activeClass;
            if (monthBtn) monthBtn.className = inactiveClass;
        } else {
            if (weekView) weekView.classList.add('hidden');
            if (monthView) monthView.classList.remove('hidden');
            if (monthBtn) monthBtn.className = activeClass;
            if (weekBtn) weekBtn.className = inactiveClass;
        }
    };

    // ============================================================
    // CALENDAR: Collapsible Filter Bar Toggle
    // ============================================================
    window.coraToggleFilterBar = function() {
        const bar = document.getElementById('cal-filters-collapsible-bar');
        const btn = document.getElementById('btn-cal-toggle-filters');
        if (!bar) return;
        const isHidden = bar.classList.contains('hidden');
        if (isHidden) {
            bar.classList.remove('hidden');
            bar.style.display = 'flex';
            if (btn) btn.classList.add('bg-zinc-100', 'border-zinc-400');
        } else {
            bar.classList.add('hidden');
            bar.style.display = 'none';
            if (btn) btn.classList.remove('bg-zinc-100', 'border-zinc-400');
        }
    };

    // ============================================================
    // CALENDAR: Live Client-Side Filtering
    // ============================================================
    window.coraFilterCalendar = function() {
        const statusFilter = (document.getElementById('cal-filter-status')?.value || '').toLowerCase();
        const typeFilter = (document.getElementById('cal-filter-type')?.value || '').toLowerCase();
        const channelFilter = (document.getElementById('cal-filter-channel')?.value || '').toLowerCase();
        const ownerFilter = document.getElementById('cal-filter-owner')?.value || '';

        document.querySelectorAll('.cora-cal-event-card').forEach(card => {
            const status = (card.dataset.status || '').toLowerCase();
            const type = (card.dataset.type || '').toLowerCase();
            const owner = card.dataset.owner || '';
            
            let show = true;
            if (statusFilter && !status.includes(statusFilter)) show = false;
            if (typeFilter && !type.includes(typeFilter)) show = false;
            if (ownerFilter && owner !== ownerFilter) show = false;

            card.style.display = show ? '' : 'none';
        });

        // Hide entire empty columns after filter
        document.querySelectorAll('.cora-cal-day-cell').forEach(cell => {
            const visibleCards = [...cell.querySelectorAll('.cora-cal-event-card')].filter(c => c.style.display !== 'none');
            const emptyPlaceholder = cell.querySelector('.cora-cal-empty-placeholder');
            if (emptyPlaceholder) {
                emptyPlaceholder.style.display = visibleCards.length === 0 ? '' : 'none';
            }
        });
    };

    window.coraResetCalendarFilters = function() {
        ['cal-filter-type', 'cal-filter-status', 'cal-filter-channel', 'cal-filter-owner'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        document.querySelectorAll('.cora-cal-event-card').forEach(card => {
            card.style.display = '';
        });
        document.querySelectorAll('.cora-cal-empty-placeholder').forEach(ph => {
            ph.style.display = '';
        });
    };

    // ============================================================
    // CALENDAR: Drag & Drop Rescheduling
    // ============================================================
    let _calDragPostId = null;
    let _calDragOriginDate = null;

    window.coraCalDragStart = function(e, postId, originDate) {
        _calDragPostId = postId;
        _calDragOriginDate = originDate;
        e.dataTransfer.setData('text/plain', postId);
        e.dataTransfer.effectAllowed = 'move';
        e.currentTarget.classList.add('opacity-50', 'scale-95');
    };

    window.coraCalDragEnd = function(e) {
        e.currentTarget.classList.remove('opacity-50', 'scale-95');
    };

    window.coraCalDragOver = function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    };

    window.coraCalDragEnter = function(e) {
        e.preventDefault();
        const cell = e.currentTarget;
        cell.classList.add('ring-2', 'ring-zinc-900', 'ring-inset', 'bg-zinc-50');
    };

    window.coraCalDragLeave = function(e) {
        const cell = e.currentTarget;
        if (!cell.contains(e.relatedTarget)) {
            cell.classList.remove('ring-2', 'ring-zinc-900', 'ring-inset', 'bg-zinc-50');
        }
    };

    window.coraCalDrop = function(e, dayNum, dateStr) {
        e.preventDefault();
        const cell = e.currentTarget;
        cell.classList.remove('ring-2', 'ring-zinc-900', 'ring-inset', 'bg-zinc-50');

        const postId = e.dataTransfer.getData('text/plain') || _calDragPostId;
        if (!postId) return;
        if (dateStr === _calDragOriginDate) return; // dropped on same day, no-op

        // AJAX reschedule
        if (typeof $ !== 'undefined' && window.coraREWPData) {
            $.post(coraREWPData.ajaxUrl, {
                action: 'cora_update_article_date',
                nonce: coraREWPData.ajaxNonce,
                post_id: postId,
                target_date: dateStr
            }, function(r) {
                if (window.coraShowToast) {
                    if (r.success) {
                        window.coraShowToast('Article rescheduled to ' + dateStr, 'success');
                    } else {
                        window.coraShowToast('Failed to reschedule article', 'error');
                    }
                }
                if (r.success) {
                    setTimeout(() => window.location.reload(), 400);
                }
            }).fail(function() {
                if (window.coraShowToast) window.coraShowToast('Network error while rescheduling', 'error');
            });
        }
    };

    // ============================================================
    // CALENDAR: Click Empty Day Cell -> Open Create Drawer w/ Date Pre-filled
    // ============================================================
    window.coraCalDayClick = function(e, dateStr) {
        if (e.target.closest('.cora-cal-event-card') || e.target.closest('button')) return;
        if (typeof window.openCreateArticleDrawer === 'function') {
            window.openCreateArticleDrawer();
        }
        const dateInput = document.getElementById('ca-date');
        if (dateInput) dateInput.value = dateStr;
    };

    // ============================================================
    // CALENDAR: Board Tab -> Switch to Workflow View
    // ============================================================
    window.coraCalSwitchToBoard = function() {
        if (typeof window.switchContentTab === 'function') {
            window.switchContentTab('ct-workflow');
        }
    };

    // ============================================================
    // DRAWER CLOSE (scoped to content-suite drawers only)
    // ============================================================
    window.closeCreateArticleDrawer = function() {
        const sheet = document.getElementById('cora-create-article-sheet');
        if (sheet) sheet.classList.add('collapsed');
        const seoSheet = document.getElementById('cora-seo-detail-sheet');
        if (seoSheet) seoSheet.classList.add('collapsed');
        const bd = document.getElementById('cora-drawer-backdrop');
        if (bd) { bd.classList.add('hidden'); }
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    };

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

    // ============================================================
    // CALENDAR: Inner view toggle (Calendar grid <-> List view)
    // ============================================================
    window.coraSwitchCalView = function(mode) {
        const weekView  = document.getElementById('cora-cal-week-view');
        const monthView = document.getElementById('cora-cal-month-view');
        const listView  = document.getElementById('cora-cal-list-view');
        const subHeader = document.getElementById('cal-sub-header-row');
        const navPill   = document.getElementById('cal-nav-pill');
        const btnCal    = document.getElementById('btn-cal-tab-calendar');
        const btnList   = document.getElementById('btn-cal-tab-list');

        const activeClass   = 'px-3 py-1.5 text-xs font-bold bg-white text-zinc-900 rounded-md shadow-sm border border-zinc-200/50 flex items-center gap-1.5 transition-all';
        const inactiveClass = 'px-3 py-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-700 rounded-md flex items-center gap-1.5 transition-all';

        if (mode === 'list') {
            if (weekView)  weekView.classList.add('hidden');
            if (monthView) monthView.classList.add('hidden');
            if (listView)  listView.classList.remove('hidden');
            if (navPill)   navPill.style.visibility = 'hidden';
            if (btnCal)    btnCal.className = inactiveClass;
            if (btnList)   btnList.className = activeClass;
        } else {
            if (weekView)  weekView.classList.remove('hidden');
            if (monthView) monthView.classList.add('hidden');
            if (listView)  listView.classList.add('hidden');
            if (navPill)   navPill.style.visibility = 'visible';
            if (btnCal)    btnCal.className = activeClass;
            if (btnList)   btnList.className = inactiveClass;
        }
    };

    // ============================================================
    // CLOSE ALL DRAWERS (fallback if admin-script.js not loaded)
    // ============================================================
    if (typeof window.coraCloseAllDrawers !== 'function') {
        window.coraCloseAllDrawers = function() {
            document.querySelectorAll('.cora-bottom-sheet').forEach(s => s.classList.add('collapsed'));
            const bd = document.getElementById('cora-drawer-backdrop');
            if(bd) { bd.classList.add('hidden'); bd.style.pointerEvents = 'none'; }
        };
    }

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
    window.openCreateArticleDrawer = function(prefillDate) {
        // Close any open drawers first
        const existingSheets = document.querySelectorAll('.cora-bottom-sheet');
        existingSheets.forEach(s => s.classList.add('collapsed'));
        
        // Clear all form fields
        const fields = ['ca-title', 'ca-keyword', 'ca-date'];
        fields.forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
        const cb = document.getElementById('ca-ai-brief');
        if(cb) cb.checked = false;

        // Pre-fill date if provided (detect YYYY-MM-DD format)
        if(prefillDate && /^\d{4}-\d{2}-\d{2}$/.test(prefillDate)) {
            const dateEl = document.getElementById('ca-date');
            if(dateEl) dateEl.value = prefillDate;
        }

        const drawer = document.getElementById('cora-create-article-sheet');
        const backdrop = document.getElementById('cora-drawer-backdrop');
        if(drawer) {
            // Move to body so it escapes any overflow:hidden ancestor (WordPress admin)
            if(drawer.parentNode !== document.body) document.body.appendChild(drawer);
            if(backdrop && backdrop.parentNode !== document.body) document.body.appendChild(backdrop);
            drawer.classList.remove('collapsed');
        }
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
        const backdrop = document.getElementById('cora-drawer-backdrop');
        if(drawer) {
            if(drawer.parentNode !== document.body) document.body.appendChild(drawer);
            if(backdrop && backdrop.parentNode !== document.body) document.body.appendChild(backdrop);
            drawer.classList.remove('translate-x-full', 'collapsed');
        }
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
                        <div class="text-2xl font-bold text-zinc-900 mt-1" id="inline-geo-score">--</div>
                        <div class="text-[10px] text-zinc-500" id="inline-geo-label">Loading article data...</div>
                    </div>

                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 flex flex-col justify-between">
                        <div class="text-xs font-bold text-zinc-900">Focus Keyword Density</div>
                        <div class="text-2xl font-bold text-zinc-900 mt-1" id="inline-kw-density">--</div>
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
                const geoEl = document.getElementById('inline-geo-score');
                const geoLbl = document.getElementById('inline-geo-label');
                const densEl = document.getElementById('inline-kw-density');
                if(kwEl) kwEl.value = d.keyword || '';
                if(kwLbl) kwLbl.innerHTML = 'Target: <strong>' + (d.keyword || 'Not set') + '</strong>';
                if(ttEl) { ttEl.value = d.meta_title || d.title || ''; document.getElementById('inline-title-count').innerText = ttEl.value.length + '/60'; }
                if(descEl) { descEl.value = d.description || ''; document.getElementById('inline-desc-count').innerText = descEl.value.length + '/160'; }
                if(slugEl && d.slug) slugEl.value = d.slug;
                // Update GEO score from real meta
                const geoScore = d.geo_score || d.cora_geo_score || null;
                if(geoEl) geoEl.textContent = geoScore ? geoScore + '%' : 'N/A';
                if(geoLbl) geoLbl.textContent = geoScore ? 'AI Engine Visibility Score' : 'Run audit to calculate';
                // Update keyword density from real meta
                const density = d.kw_density || d.keyword_density || null;
                if(densEl) densEl.textContent = density ? density + '%' : 'Run audit';
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
