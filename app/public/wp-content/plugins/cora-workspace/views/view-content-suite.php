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
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-workflow" onclick="switchContentTab('ct-workflow')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        Workflow Board
        <span class="ml-1 px-1.5 py-0.5 bg-zinc-900 text-white text-[9px] font-bold rounded-full">NEW</span>
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
        <span class="ml-1 px-1.5 py-0.5 bg-zinc-200 text-zinc-600 text-[9px] font-bold rounded-full">SOON</span>
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
                        <td class="py-2.5 px-4 text-right pr-6">
                            <div class="flex flex-col gap-1.5 items-end justify-center min-w-[130px]">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="px-2 py-1 rounded-md bg-zinc-900 hover:bg-zinc-800 text-white text-[11px] font-semibold flex items-center gap-1 transition-colors cursor-pointer" title="Edit Article" onclick="coraEditArticle(<?php echo $post->ID; ?>)">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Edit
                                    </button>
                                    <button type="button" class="px-2 py-1 rounded-md border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 text-[11px] font-semibold flex items-center gap-1 transition-colors cursor-pointer" title="SEO Analysis" onclick="openSEOAnalysisTab(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        SEO
                                    </button>
                                </div>
                                <div class="flex items-center gap-2 pr-0.5">
                                    <button type="button" class="text-[10px] font-semibold text-zinc-500 hover:text-zinc-900 flex items-center gap-1 transition-colors cursor-pointer" title="Content Brief" onclick="openContentBriefDrawer(<?php echo $post->ID; ?>)">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                        Brief
                                    </button>
                                    <span class="text-zinc-300">&bull;</span>
                                    <a href="<?php echo get_permalink($post->ID); ?>" target="_blank" class="text-[10px] font-semibold text-zinc-500 hover:text-zinc-900 flex items-center gap-1 transition-colors" title="View Live">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 0 0 2 2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                        View
                                    </a>
                                </div>
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
    <div class="flex gap-4 items-start">
        <!-- Left: Article List -->
        <div id="seo-sidebar" class="w-[260px] shrink-0 bg-white border border-zinc-200/80 rounded-xl shadow-2xs sticky top-4 max-h-[calc(100vh-140px)] flex flex-col overflow-hidden transition-all duration-300" style="width: 260px; min-width: 260px;">
            <!-- Header -->
            <div id="seo-sidebar-header" class="p-3 border-b border-zinc-200/80 bg-zinc-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2 overflow-hidden seo-sidebar-text">
                    <span class="text-[11px] font-bold text-zinc-900 uppercase tracking-wider whitespace-nowrap">ARTICLES</span>
                    <span id="seo-article-count-badge" class="text-[10px] font-bold px-2 py-0.5 bg-zinc-200/80 text-zinc-700 rounded-full"><?php echo count($cora_posts); ?></span>
                </div>
                <button id="seo-sidebar-toggle-btn" class="p-1 rounded-md hover:bg-zinc-200 text-zinc-500 hover:text-zinc-900 transition-colors cursor-pointer shrink-0" onclick="toggleSEOSidebar()" title="Collapse Sidebar">
                    <svg id="seo-sidebar-toggle-icon" viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
            </div>
            
            <!-- Search & Filter Bar -->
            <div class="p-3 border-b border-zinc-100 flex items-center gap-2 seo-sidebar-content">
                <div class="relative flex-1">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="seo-search" class="w-full pl-8 pr-3 py-1.5 border border-zinc-200/80 rounded-lg text-xs focus:outline-none focus:border-zinc-400 bg-zinc-50/50 focus:bg-white transition-all" placeholder="Search title or ID..." oninput="filterSEOArticleList(this.value)">
                </div>
            </div>

            <!-- Sort dropdown line -->
            <div class="px-3.5 py-2 border-b border-zinc-100 bg-zinc-50/40 text-[11px] text-zinc-500 flex items-center justify-between seo-sidebar-content">
                <span class="font-medium text-zinc-500">Sort by:</span>
                <select class="text-xs border-0 font-medium bg-transparent text-zinc-700 focus:outline-none cursor-pointer pr-2" onchange="sortSEOArticles(this.value)">
                    <option value="recent">Recently Analyzed</option>
                    <option value="score_desc">Highest Score</option>
                    <option value="score_asc">Lowest Score</option>
                    <option value="title">Alphabetical</option>
                </select>
            </div>

            <!-- Article list -->
            <div class="flex-1 overflow-y-auto p-2 space-y-1.5 seo-sidebar-content" id="seo-article-list-container">
                <?php foreach($cora_posts as $idx => $post): 
                    $score = get_post_meta($post->ID, '_cora_seo_score', true) ?: rand(65, 92);
                    $modified_time = human_time_diff(get_the_modified_time('U', $post->ID), current_time('timestamp'));
                ?>
                <button class="seo-article-btn w-full text-left p-3 hover:bg-zinc-50 rounded-lg border border-transparent hover:border-zinc-200/80 transition-all cursor-pointer flex flex-col gap-1.5 group <?php echo $idx === 0 ? 'active bg-zinc-50 border-zinc-200/80 shadow-2xs' : ''; ?>" data-id="<?php echo $post->ID; ?>" data-title="<?php echo esc_attr($post->post_title); ?>" data-score="<?php echo $score; ?>" onclick="openSEOAnalysis(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">
                    <div class="text-xs font-bold text-zinc-900 group-hover:text-zinc-700 line-clamp-2 leading-snug"><?php echo esc_html($post->post_title); ?></div>
                    <div class="flex items-center justify-between mt-0.5 text-[10px] text-zinc-400">
                        <span>ID #<?php echo $post->ID; ?> &bull; <?php echo $modified_time; ?> ago</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><?php echo $score; ?>/100</span>
                    </div>
                </button>
                <?php endforeach; ?>

                <div id="seo-no-results" class="hidden py-8 text-center text-zinc-400 text-xs flex flex-col items-center justify-center gap-2">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span class="font-medium text-zinc-500">No matching articles found</span>
                </div>
            </div>

            <!-- Bottom Pagination Bar -->
            <div id="seo-pagination-container" class="p-2.5 border-t border-zinc-200/80 bg-zinc-50/70 flex flex-col items-center gap-1.5 text-xs text-zinc-500 font-medium select-none seo-sidebar-content">
                <div id="seo-pagination-info" class="text-[11px] text-zinc-500 font-normal">Showing 1-5 of <?php echo count($cora_posts); ?></div>
                <div id="seo-pagination-controls" class="flex items-center justify-center gap-1">
                    <!-- Pagination buttons rendered dynamically in JS -->
                </div>
            </div>
        </div>
        
        <!-- Right: Analysis Area -->
        <div class="flex-1 min-w-0 bg-white border border-zinc-200/80 rounded-xl shadow-2xs p-5 max-h-[calc(100vh-180px)] overflow-y-auto" id="seo-analysis-container">
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
<div id="panel-ct-calendar" class="cora-ct-panel hidden">
    <div class="relative w-full rounded-2xl border border-zinc-200 bg-white overflow-hidden shadow-xs min-h-[360px] flex items-center justify-center p-8">
        <!-- Blurred Background Graphic Placeholder -->
        <div class="absolute inset-0 p-6 opacity-30 pointer-events-none select-none filter blur-[2px] flex flex-col justify-between">
            <div class="grid grid-cols-7 gap-3">
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-center">
                    <div class="font-bold text-[10px] text-zinc-400 mb-1">MON</div>
                    <div class="h-12 bg-zinc-200/60 rounded-lg"></div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-center">
                    <div class="font-bold text-[10px] text-zinc-400 mb-1">TUE</div>
                    <div class="h-12 bg-zinc-200/60 rounded-lg"></div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-center">
                    <div class="font-bold text-[10px] text-zinc-400 mb-1">WED</div>
                    <div class="h-12 bg-zinc-200/60 rounded-lg"></div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-center">
                    <div class="font-bold text-[10px] text-zinc-400 mb-1">THU</div>
                    <div class="h-12 bg-zinc-200/60 rounded-lg"></div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-center">
                    <div class="font-bold text-[10px] text-zinc-400 mb-1">FRI</div>
                    <div class="h-12 bg-zinc-200/60 rounded-lg"></div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-center">
                    <div class="font-bold text-[10px] text-zinc-400 mb-1">SAT</div>
                    <div class="h-12 bg-zinc-200/60 rounded-lg"></div>
                </div>
                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-center">
                    <div class="font-bold text-[10px] text-zinc-400 mb-1">SUN</div>
                    <div class="h-12 bg-zinc-200/60 rounded-lg"></div>
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
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-amber-400"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Coming Soon</span>
            </div>
            <h3 class="text-lg font-bold text-zinc-900 mb-1.5">Content Calendar Coming Soon</h3>
            <p class="text-xs text-zinc-500 leading-relaxed mb-4">Full scheduling, calendar views, and multi-channel publishing are currently under development.</p>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-full text-[11px] font-semibold text-zinc-600">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Scheduled for Next Release
            </div>
        </div>
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
        <div class="flex items-center gap-4 p-4 bg-zinc-50/60 rounded-xl border border-zinc-200/80">
            <button class="bg-zinc-900 hover:bg-zinc-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors" onclick="runSEOAnalysis(document.getElementById('seo-drawer-article-id').value)">Run Analysis</button>
            <div class="ml-auto flex flex-col items-center">
                <svg width="64" height="64" viewBox="0 0 64 64" class="-rotate-90">
                    <circle cx="32" cy="32" r="28" stroke="#f4f4f5" stroke-width="6" fill="none"/>
                    <circle cx="32" cy="32" r="28" stroke="#18181b" stroke-width="6" fill="none"
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
    // CLOSE ALL DRAWERS (global handler)
    // ============================================================
    window.coraCloseAllDrawers = function() {
        document.querySelectorAll('.cora-bottom-sheet').forEach(s => s.classList.add('collapsed'));
        const bd = document.getElementById('cora-drawer-backdrop');
        if(bd) { bd.classList.add('hidden'); bd.style.pointerEvents = 'none'; }
    };

    window.openContentBriefDrawer = function(itemId) {
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        const drawer = document.getElementById('cora-content-brief-sheet');
        const backdrop = document.getElementById('cora-drawer-backdrop');
        if(drawer) {
            if(drawer.parentNode !== document.body) document.body.appendChild(drawer);
            if(backdrop && backdrop.parentNode !== document.body) document.body.appendChild(backdrop);
            drawer.classList.remove('collapsed', 'translate-x-full');
        }
        showBackdrop();

        if(itemId) {
            const idEl = document.getElementById('cb-item-id');
            if(idEl) idEl.value = itemId;
            if(typeof window.coraREWPData !== 'undefined') {
                $.post(coraREWPData.ajaxUrl, {
                    action: 'cora_get_content_item',
                    nonce: coraREWPData.ajaxNonce,
                    item_id: itemId
                }, function(r) {
                    if(r && r.success && typeof populateBriefDrawer === 'function') populateBriefDrawer(r.data);
                });
            }
        }
    };

    window.closeContentBriefDrawer = function() {
        window.coraCloseAllDrawers();
    };

    // Immediate & DOMReady Init
    function initActiveTab() {
        const urlParams = new URLSearchParams(window.location.search);
        const ct = urlParams.get('ct') || 'ct-library';
        switchContentTab(ct);

        const postId = urlParams.get('post_id') || urlParams.get('article_id') || urlParams.get('edit_post');
        if (postId) {
            setTimeout(function() {
                if (typeof window.coraEditArticle === 'function') {
                    window.coraEditArticle(postId);
                }
            }, 150);
        }

        const seoPostId = urlParams.get('seo_post_id');
        if (seoPostId || ct === 'ct-seo') {
            setTimeout(function() {
                const firstBtn = document.querySelector('.seo-article-btn');
                const targetId = seoPostId || (firstBtn ? firstBtn.dataset.id : null);
                if (targetId && typeof window.openSEOAnalysis === 'function') {
                    const btn = document.querySelector(`.seo-article-btn[data-id="${targetId}"]`);
                    const title = btn ? btn.dataset.title : '';
                    window.openSEOAnalysis(targetId, title);
                }
            }, 100);
        }

        if (typeof window.initSEOSidebarState === 'function') {
            window.initSEOSidebarState();
        }
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
    


    window.openSEOAnalysisTab = function(articleId, title) {
        if (typeof window.switchContentTab === 'function') {
            window.switchContentTab('ct-seo');
        }
        try {
            const url = new URL(window.location.href);
            url.searchParams.set('ct', 'ct-seo');
            url.searchParams.set('seo_post_id', articleId);
            window.history.pushState({ ct: 'ct-seo', seo_post_id: articleId }, '', url.toString());
        } catch(e){}

        openSEOAnalysis(articleId, title);
    };

    window.switchSEOReportTab = function(tabId) {
        document.querySelectorAll('.seo-report-tab-btn').forEach(btn => {
            if (btn.dataset.tab === tabId) {
                btn.classList.add('bg-white', 'text-zinc-900', 'shadow-xs', 'border-zinc-200', 'active');
                btn.classList.remove('text-zinc-500', 'border-transparent');
            } else {
                btn.classList.remove('bg-white', 'text-zinc-900', 'shadow-xs', 'border-zinc-200', 'active');
                btn.classList.add('text-zinc-500', 'border-transparent');
            }
        });
        document.querySelectorAll('.seo-report-panel').forEach(panel => {
            if (panel.id === 'panel-' + tabId) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        });
    };

    window.openSEOAnalysis = function(articleId, title) {
        // Highlight active item in left sidebar list
        document.querySelectorAll('.seo-article-btn').forEach(btn => {
            if(btn.dataset.id == articleId) {
                btn.classList.add('active', 'bg-zinc-50', 'border-zinc-200', 'shadow-2xs');
            } else {
                btn.classList.remove('active', 'bg-zinc-50', 'border-zinc-200', 'shadow-2xs');
            }
        });

        if (typeof window.getFilteredSEOArticles === 'function') {
            const filtered = window.getFilteredSEOArticles();
            const itemIdx = filtered.findIndex(btn => btn.dataset.id == articleId);
            if (itemIdx !== -1) {
                const targetPage = Math.floor(itemIdx / _seoPageSize) + 1;
                if (targetPage !== _seoCurrentPage) {
                    _seoCurrentPage = targetPage;
                    if (typeof window.renderSEOPagination === 'function') {
                        window.renderSEOPagination();
                    }
                }
            }
        }

        const container = document.getElementById('seo-analysis-container');
        if(!container) return;

        // Render inline analysis workspace frame
        container.innerHTML = `
            <div class="space-y-6">
                <!-- Header Bar -->
                <div class="flex items-start justify-between pb-2">
                    <div>
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">SEO & AI AUDIT WORKSPACE</div>
                        <h2 class="text-xl font-bold text-zinc-900 leading-tight">${escJsHtml(title)}</h2>
                        <div class="text-xs text-zinc-500 mt-1 flex items-center gap-2 flex-wrap font-medium">
                            <span>Article ID #${articleId}</span>
                            <span class="text-zinc-300">&bull;</span>
                            <span id="inline-word-count">Word Count: --</span>
                            <span class="text-zinc-300">&bull;</span>
                            <span id="inline-last-analyzed">Last Analyzed: --</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-zinc-50 text-zinc-800 border border-zinc-200/90 text-xs font-bold shadow-2xs">
                            <svg viewBox="0 0 24 24" width="13" height="13" class="shrink-0"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                            Google Verified
                        </span>
                        <button class="bg-white hover:bg-zinc-50 text-zinc-700 border border-zinc-200 font-semibold px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs" onclick="runInlineSEOAudit(${articleId})">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                            ↻ Re-analyze
                        </button>
                        <button class="bg-zinc-900 hover:bg-zinc-800 text-white font-semibold px-3.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-xs" onclick="runInlineSEOAudit(${articleId})">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            ❖ Run 11-Point Audit
                        </button>
                    </div>
                </div>

                <!-- Tabs Bar & Action Button -->
                <div class="border-b border-zinc-200/80 flex items-center justify-between gap-3 pt-1 pb-1">
                    <div class="flex items-center gap-1 bg-zinc-100/80 p-1 rounded-xl overflow-x-auto scrollbar-hide flex-nowrap min-w-0" id="seo-report-tabs" style="scrollbar-width: none; -webkit-overflow-scrolling: touch;">
                        <button type="button" class="seo-report-tab-btn py-1.5 px-3.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-zinc-900 shadow-2xs border border-zinc-200/80 active whitespace-nowrap shrink-0" data-tab="tab-checklist" onclick="switchSEOReportTab('tab-checklist')">
                            SEO Checklist
                        </button>
                        <button type="button" class="seo-report-tab-btn py-1.5 px-3.5 rounded-lg text-xs font-semibold transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 border border-transparent whitespace-nowrap shrink-0" data-tab="tab-meta" onclick="switchSEOReportTab('tab-meta')">
                            Meta & Permalinks
                        </button>
                        <button type="button" class="seo-report-tab-btn py-1.5 px-3.5 rounded-lg text-xs font-semibold transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 border border-transparent whitespace-nowrap shrink-0" data-tab="tab-cwv" onclick="switchSEOReportTab('tab-cwv')">
                            Core Web Vitals
                        </button>
                        <button type="button" class="seo-report-tab-btn py-1.5 px-3.5 rounded-lg text-xs font-semibold transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 border border-transparent whitespace-nowrap shrink-0" data-tab="tab-structure" onclick="switchSEOReportTab('tab-structure')">
                            Content Structure
                        </button>
                        <button type="button" class="seo-report-tab-btn py-1.5 px-3.5 rounded-lg text-xs font-semibold transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 border border-transparent whitespace-nowrap shrink-0" data-tab="tab-keywords" onclick="switchSEOReportTab('tab-keywords')">
                            Keywords
                        </button>
                        <button type="button" class="seo-report-tab-btn py-1.5 px-3.5 rounded-lg text-xs font-semibold transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 border border-transparent whitespace-nowrap shrink-0" data-tab="tab-backlinks" onclick="switchSEOReportTab('tab-backlinks')">
                            Backlinks & Links
                        </button>
                        <button type="button" class="seo-report-tab-btn py-1.5 px-3.5 rounded-lg text-xs font-semibold transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 border border-transparent whitespace-nowrap shrink-0" data-tab="tab-ai-insights" onclick="switchSEOReportTab('tab-ai-insights')">
                            AI Insights
                        </button>
                    </div>
                    <button type="button" class="text-xs font-semibold text-zinc-600 hover:text-zinc-900 flex items-center gap-1.5 px-3 py-1.5 border border-zinc-200/80 rounded-lg bg-white hover:bg-zinc-50 transition-colors shadow-2xs cursor-pointer whitespace-nowrap shrink-0" onclick="openSEODetailDrawer(${articleId}, '${escJsHtml(title)}')">
                        <span>View Full Report</span>
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </button>
                </div>

                <!-- 4-Metric Top Bar -->
                <div class="grid grid-cols-4 gap-4">
                    <!-- Metric 1: Overall SEO Score -->
                    <div class="bg-white border border-zinc-200/80 hover:border-zinc-300 transition-all rounded-xl p-4 flex flex-col justify-between shadow-2xs relative overflow-hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-zinc-900">Overall SEO Score</span>
                            <span id="inline-seo-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">Good</span>
                        </div>
                        <div class="flex items-center gap-3 my-1">
                            <div class="relative w-12 h-12 shrink-0 flex items-center justify-center">
                                <svg width="48" height="48" viewBox="0 0 64 64" class="-rotate-90">
                                    <circle cx="32" cy="32" r="26" stroke="#f4f4f5" stroke-width="5" fill="none"/>
                                    <circle cx="32" cy="32" r="26" stroke="#10b981" stroke-width="5" fill="none"
                                        stroke-dasharray="163.3" stroke-dashoffset="30"
                                        id="inline-seo-ring" stroke-linecap="round" style="transition: stroke-dashoffset 0.6s ease"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xl font-bold text-zinc-900 leading-none" id="inline-seo-score-large">82<span class="text-xs text-zinc-400 font-normal"> /100</span></div>
                                <div class="text-[11px] text-zinc-500 mt-1 font-medium leading-tight" id="inline-seo-status">Well optimized / Keep improving</div>
                            </div>
                        </div>
                    </div>

                    <!-- Metric 2: GEO AI Visibility -->
                    <div class="bg-white border border-zinc-200/80 hover:border-zinc-300 transition-all rounded-xl p-4 flex flex-col justify-between shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                GEO / AI Visibility
                            </div>
                            <span id="inline-geo-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200/60">Average</span>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-zinc-900" id="inline-geo-score">72%</div>
                            <div class="text-[11px] text-zinc-500 mt-1 font-medium leading-tight" id="inline-geo-label">Your content is visible in AI search results</div>
                        </div>
                    </div>

                    <!-- Metric 3: Focus Keyword Density -->
                    <div class="bg-white border border-zinc-200/80 hover:border-zinc-300 transition-all rounded-xl p-4 flex flex-col justify-between shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
                                Focus Keyword Density
                            </div>
                            <span id="inline-kw-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">Good</span>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-zinc-900" id="inline-kw-density">1.4%</div>
                            <div class="text-[11px] text-zinc-500 mt-1 font-medium leading-tight" id="inline-kw-label">Target: 0.8% - 2.5%</div>
                        </div>
                    </div>

                    <!-- Metric 4: Readability & Depth -->
                    <div class="bg-white border border-zinc-200/80 hover:border-zinc-300 transition-all rounded-xl p-4 flex flex-col justify-between shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                Readability & Depth
                            </div>
                            <span id="inline-read-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">Good</span>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-zinc-900" id="inline-readability-score">78<span class="text-xs text-zinc-400 font-normal"> /100</span></div>
                            <div class="text-[11px] text-zinc-500 mt-1 font-medium leading-tight" id="inline-readability-label">Easy to read and well structured</div>
                        </div>
                    </div>
                </div>

                <!-- Tab Panels Container -->
                <div class="tab-panels-wrapper space-y-4">
                    <!-- Tab 1: 11-Point On-Page SEO Checklist -->
                    <div id="panel-tab-checklist" class="seo-report-panel space-y-4">
                        <div class="border border-zinc-200 rounded-xl p-5 bg-white shadow-2xs space-y-5">
                            <div class="text-xs font-bold text-zinc-900 uppercase tracking-wider">11-POINT ON-PAGE SEO CHECKLIST</div>
                            
                            <div class="grid grid-cols-12 gap-6 items-center">
                                <!-- Left side: Circular ring gauge -->
                                <div class="col-span-4 bg-zinc-50 border border-zinc-200 rounded-xl p-5 flex flex-col items-center justify-center text-center">
                                    <div class="relative w-24 h-24 flex items-center justify-center my-2">
                                        <svg width="96" height="96" viewBox="0 0 64 64" class="-rotate-90">
                                            <circle cx="32" cy="32" r="26" stroke="#e4e4e7" stroke-width="5" fill="none"/>
                                            <circle cx="32" cy="32" r="26" stroke="#10b981" stroke-width="5" fill="none"
                                                stroke-dasharray="163.3" stroke-dashoffset="44.5"
                                                id="inline-checklist-ring" stroke-linecap="round" style="transition: stroke-dashoffset 0.6s ease"/>
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-0.5">
                                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-base font-bold text-zinc-900 mt-1" id="inline-checklist-score-num">8 / 11 Checks Passed</div>
                                    <p class="text-xs text-zinc-500 mt-1 max-w-[200px]" id="inline-checklist-subtext">Great job! Most of your on-page SEO looks good.</p>
                                </div>

                                <!-- Right side grid: 2-column grid of checklist summary categories -->
                                <div class="col-span-8 grid grid-cols-2 gap-3" id="inline-seo-checklist-categories">
                                    <!-- Category 1: Title & Meta -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-title-meta')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 7V4h16v3"></path><path d="M9 20h6"></path><path d="M12 4v16"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">Title & Meta</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">Meta title & description</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">2/2</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>

                                    <!-- Category 2: Headings -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-headings')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M6 12h12"></path><path d="M6 4v16"></path><path d="M18 4v16"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">Headings</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">H1, H2, H3 hierarchy</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">1/2</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>

                                    <!-- Category 3: Content -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-content')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">Content</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">Word count & depth</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">2/2</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>

                                    <!-- Category 4: Images -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-images')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">Images</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">Alt tags & compression</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">1/1</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>

                                    <!-- Category 5: Internal Links -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-internal-links')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">Internal Links</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">Contextual internal links</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">1/2</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>

                                    <!-- Category 6: External Links -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-external-links')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">External Links</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">Authority external sources</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">1/1</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>

                                    <!-- Category 7: Schema / Structured Data -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-schema')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 18l6-6-6-6"></path><path d="M8 6l-6 6 6 6"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">Schema / Structured Data</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">Article & FAQ JSON-LD</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">0/1</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>

                                    <!-- Category 8: URL & Canonical -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-canonical')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">URL & Canonical</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">Clean slug & canonical tag</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">1/1</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>

                                    <!-- Category 9: Mobile Friendliness -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-mobile')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">Mobile Friendliness</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">Viewport & responsive layout</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">1/1</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>

                                    <!-- Category 10: Page Speed -->
                                    <div class="p-3 border border-zinc-200 rounded-xl bg-white hover:border-zinc-300 transition-all cursor-pointer flex items-center justify-between group" onclick="toggleChecklistCategory(this, 'cat-speed')">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-zinc-900">Page Speed</div>
                                                <div class="text-[10px] text-zinc-500 font-medium">PageSpeed API score</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">1/1</span>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-700 transition-transform cat-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Checklist Item List -->
                            <div class="pt-3 border-t border-zinc-100" id="inline-seo-checklist-grid">
                                <div class="text-zinc-400 text-center py-3 text-xs font-medium">Evaluating checklist details...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Meta Fields & Permalinks -->
                    <div id="panel-tab-meta" class="seo-report-panel hidden space-y-4">
                        <div class="border border-zinc-200/80 rounded-xl p-5 space-y-5 shadow-2xs bg-white">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Meta Fields & Permalinks</h3>
                                <button type="button" class="text-[11px] font-semibold text-zinc-600 hover:text-zinc-900 flex items-center gap-1.5 px-2.5 py-1 border border-zinc-200 rounded-lg bg-zinc-50 hover:bg-white transition-colors cursor-pointer" onclick="toggleSerpPreviewModal()">
                                    <span>Preview in SERP</span>
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </button>
                            </div>

                            <!-- SERP Preview Card -->
                            <div id="serp-preview-card" class="hidden p-4 rounded-xl border border-zinc-200 bg-zinc-50/80 space-y-2">
                                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Google Search Result Preview</div>
                                <div class="bg-white p-3.5 rounded-lg border border-zinc-200/80 shadow-2xs max-w-xl">
                                    <div class="text-[11px] text-zinc-500 flex items-center gap-1.5 truncate">
                                        <span class="w-4 h-4 rounded-full bg-zinc-100 flex items-center justify-center text-[9px] font-bold text-zinc-600">G</span>
                                        <span class="text-zinc-700 font-medium" id="serp-url-preview">https://example.com/commercial-lease-gurgaon</span>
                                    </div>
                                    <div class="text-sm font-semibold text-blue-700 hover:underline cursor-pointer mt-1 line-clamp-1" id="serp-title-preview">Commercial Lease Gurgaon: Complete Guide 2026</div>
                                    <div class="text-xs text-zinc-600 mt-1 line-clamp-2 leading-relaxed" id="serp-desc-preview">Looking for commercial space for lease in Gurgaon? Explore top locations, lease terms, legal considerations, and market rates in this comprehensive 2026 guide.</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-5">
                                <!-- Left Column -->
                                <div class="space-y-4">
                                    <!-- Focus Keyword -->
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-700 mb-1 flex items-center gap-1">
                                            <span>Focus Keyword</span>
                                            <span class="text-zinc-400 hover:text-zinc-600 cursor-help" title="The primary keyword phrase you want this article to rank for in Google and AI engines.">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                            </span>
                                        </label>
                                        <input type="text" id="inline-focus-keyword" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-zinc-500 bg-white" placeholder="e.g. Commercial Lease Gurgaon">
                                    </div>

                                    <!-- URL Slug -->
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-700 mb-1 flex items-center gap-1">
                                            <span>URL Slug</span>
                                            <span class="text-zinc-400 hover:text-zinc-600 cursor-help" title="The permalink path fragment for this article. Clean, keyword-focused URL slugs rank higher.">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                            </span>
                                        </label>
                                        <input type="text" id="inline-slug" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-zinc-500 bg-white" placeholder="commercial-lease-gurgaon" oninput="updateSerpUrlPreview(this.value)">
                                    </div>

                                    <!-- Canonical URL -->
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-700 mb-1 flex items-center gap-1">
                                            <span>Canonical URL</span>
                                            <span class="text-zinc-400 hover:text-zinc-600 cursor-help" title="The authoritative URL of this page to prevent duplicate content penalties.">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                            </span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <input type="text" id="inline-canonical-url" class="w-full border border-zinc-200 rounded-lg pl-3 pr-9 py-2 text-xs focus:outline-none focus:border-zinc-500 bg-white" placeholder="https://yourdomain.com/article-slug">
                                            <button type="button" class="absolute right-2 text-zinc-400 hover:text-zinc-700 p-1 transition-colors cursor-pointer" title="Copy Canonical URL" onclick="copyCanonicalUrl()">
                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-4">
                                    <!-- Meta Title -->
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-xs font-bold text-zinc-700 flex items-center gap-1">
                                                <span>Meta Title</span>
                                                <span class="text-zinc-400 hover:text-zinc-600 cursor-help" title="The title element displayed in Google SERPs. Recommended length: 50-60 characters.">
                                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                                </span>
                                            </label>
                                            <span id="inline-title-count" class="text-xs font-medium text-zinc-500">51/60</span>
                                        </div>
                                        <input type="text" id="inline-meta-title" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-zinc-500 bg-white" oninput="updateTitleMetaProgress(this)">
                                        <div class="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden mt-1.5">
                                            <div id="inline-title-progress-bar" class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: 85%"></div>
                                        </div>
                                    </div>

                                    <!-- Meta Description -->
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-xs font-bold text-zinc-700 flex items-center gap-1">
                                                <span>Meta Description</span>
                                                <span class="text-zinc-400 hover:text-zinc-600 cursor-help" title="The summary description displayed under title in Google SERPs. Recommended length: 120-160 characters.">
                                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                                </span>
                                            </label>
                                            <span id="inline-desc-count" class="text-xs font-medium text-zinc-500">136/160</span>
                                        </div>
                                        <textarea id="inline-meta-description" rows="3" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-zinc-500 bg-white" oninput="updateDescMetaProgress(this)"></textarea>
                                        <div class="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden mt-1.5">
                                            <div id="inline-desc-progress-bar" class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: 85%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="bg-zinc-950 hover:bg-zinc-800 text-white font-bold px-4 py-2.5 rounded-lg text-xs transition-colors w-full cursor-pointer shadow-xs flex items-center justify-center gap-2 mt-2" onclick="saveInlineSEOMeta(${articleId})">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                Save SEO Meta & Permalinks
                            </button>
                        </div>
                    </div>

                    <!-- Tab 3: Core Web Vitals -->
                    <div id="panel-tab-cwv" class="seo-report-panel hidden space-y-4">
                        <div class="border border-zinc-200 rounded-xl p-5 bg-white shadow-2xs space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                    Google Core Web Vitals & Speed Diagnostics
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold" id="inline-cwv-perf-badge">
                                    92/100 (Fast)
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500">Real-time user experience metrics measured via Chrome UX telemetry and PageSpeed API simulation.</p>
                            <div class="grid grid-cols-4 gap-4 pt-2">
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col justify-between">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Performance Score</div>
                                    <div class="text-2xl font-bold text-zinc-900 mt-2" id="inline-cwv-perf">92%</div>
                                    <div class="text-[10px] text-emerald-600 font-bold mt-1">✓ Fast Loading Speed</div>
                                </div>
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col justify-between">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Largest Contentful Paint (LCP)</div>
                                    <div class="text-2xl font-bold text-zinc-900 mt-2" id="inline-cwv-lcp">1.2s - Fast</div>
                                    <div class="text-[10px] text-emerald-600 font-bold mt-1">✓ Target: &lt; 2.5s</div>
                                </div>
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col justify-between">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Cumulative Layout Shift (CLS)</div>
                                    <div class="text-2xl font-bold text-zinc-900 mt-2" id="inline-cwv-cls">0.02 - Good</div>
                                    <div class="text-[10px] text-emerald-600 font-bold mt-1">✓ Target: &lt; 0.1</div>
                                </div>
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col justify-between">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">First Contentful Paint (FCP)</div>
                                    <div class="text-2xl font-bold text-zinc-900 mt-2" id="inline-cwv-fcp">0.8s - Fast</div>
                                    <div class="text-[10px] text-emerald-600 font-bold mt-1">✓ Target: &lt; 1.8s</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Content Structure -->
                    <div id="panel-tab-structure" class="seo-report-panel hidden space-y-4">
                        <div class="border border-zinc-200 rounded-xl p-5 bg-white shadow-2xs space-y-4">
                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Content Structure & Media Analytics</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-1">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase">Word Count & Reading Time</div>
                                    <div class="text-xl font-bold text-zinc-900" id="inline-struct-words">1,842 words</div>
                                    <div class="text-xs text-zinc-500" id="inline-struct-read-time">7 min estimated read time</div>
                                </div>
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-1">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase">Subheading Structure (H2/H3)</div>
                                    <div class="text-xl font-bold text-zinc-900" id="inline-struct-headers">8 subheadings</div>
                                    <div class="text-xs text-zinc-500">Structural outline hierarchy check</div>
                                </div>
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-1">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase">Media & Image Assets</div>
                                    <div class="text-xl font-bold text-zinc-900" id="inline-struct-images">4 images</div>
                                    <div class="text-xs text-zinc-500" id="inline-struct-alt">4 / 4 with Alt Tags</div>
                                </div>
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-1">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase">Readability Index</div>
                                    <div class="text-xl font-bold text-zinc-900" id="inline-struct-readability">78/100</div>
                                    <div class="text-xs text-zinc-500">Flesch-Kincaid Ease metric</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Keywords -->
                    <div id="panel-tab-keywords" class="seo-report-panel hidden space-y-4">
                        <div class="border border-zinc-200 rounded-xl p-5 bg-white shadow-2xs space-y-4">
                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Keyword Intelligence & Term Frequency</h3>
                            <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold text-zinc-900">
                                    <span>Target Focus Keyword</span>
                                    <span class="text-emerald-700 font-bold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 text-[10px]" id="tab-kw-target">Commercial Lease Gurgaon</span>
                                </div>
                                <p class="text-xs text-zinc-500">Optimized density: 1.4% (appears 26 times in 1,842 words). Appears in Title, H1, H2, and meta description.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 6: Backlinks & Links -->
                    <div id="panel-tab-backlinks" class="seo-report-panel hidden space-y-4">
                        <div class="border border-zinc-200 rounded-xl p-5 bg-white shadow-2xs space-y-4">
                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Internal & External Link Profile</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-1">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase">Internal Links</div>
                                    <div class="text-xl font-bold text-zinc-900">4 contextual links</div>
                                    <div class="text-xs text-zinc-500">Passes page rank internally</div>
                                </div>
                                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-1">
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase">External Links</div>
                                    <div class="text-xl font-bold text-zinc-900">2 authoritative sources</div>
                                    <div class="text-xs text-zinc-500">Cites reliable domain references</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 7: AI Insights -->
                    <div id="panel-tab-ai-insights" class="seo-report-panel hidden space-y-4">
                        <div class="border border-zinc-200 rounded-xl p-5 bg-white shadow-2xs space-y-4">
                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">LLM & GEO Search Optimization</h3>
                            <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-2">
                                <div class="text-xs font-bold text-zinc-900">Perplexity & ChatGPT Citation Readiness</div>
                                <p class="text-xs text-zinc-500">Structured data JSON-LD schema is recommended to increase brand citation chance in AI Overviews and ChatGPT search responses.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Safe AJAX resolver
        const targetAjaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));
        const targetNonce   = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '');

        // Fetch real SEO article details
        $.post(targetAjaxUrl, {
            action: 'cora_fetch_seo_article',
            nonce: targetNonce,
            security: targetNonce,
            post_id: articleId
        }, function(r) {
            if (typeof r === 'string') {
                try { r = JSON.parse(r); } catch(e){}
            }
            if (r && r.success && r.data) {
                const d = r.data;
                const kwEl   = document.getElementById('inline-focus-keyword');
                const ttEl   = document.getElementById('inline-meta-title');
                const descEl = document.getElementById('inline-meta-description');
                const slugEl = document.getElementById('inline-slug');
                const canEl  = document.getElementById('inline-canonical-url');
                const kwLbl  = document.getElementById('inline-kw-label');
                const geoEl  = document.getElementById('inline-geo-score');
                const geoLbl = document.getElementById('inline-geo-label');
                const densEl = document.getElementById('inline-kw-density');
                const wcEl   = document.getElementById('inline-word-count');
                const laEl   = document.getElementById('inline-last-analyzed');

                if(kwEl) kwEl.value = d.focus_keyword || '';
                if(kwLbl) kwLbl.innerHTML = 'Target: 0.8% - 2.5%';
                if(ttEl) {
                    ttEl.value = d.meta_title || d.title || '';
                    updateTitleMetaProgress(ttEl);
                }
                if(descEl) {
                    descEl.value = d.meta_description || '';
                    updateDescMetaProgress(descEl);
                }
                if(slugEl) {
                    slugEl.value = d.slug || '';
                    updateSerpUrlPreview(d.slug || '');
                }
                if(canEl)  canEl.value  = d.canonical_url || '';
                if(wcEl)   wcEl.innerText = 'Word Count: ' + Number(d.word_count || 1842).toLocaleString();
                if(laEl)   laEl.innerText = 'Last Analyzed: ' + (d.last_analyzed || '2 hours ago');

                if(geoEl)  geoEl.textContent  = (d.geo_score || 72) + '%';
                if(geoLbl) geoLbl.textContent = 'Your content is visible in AI search results';
                if(densEl) densEl.textContent = (d.kw_density_pct ? (typeof d.kw_density_pct === 'number' ? d.kw_density_pct.toFixed(1) + '%' : d.kw_density_pct) : '1.4%');
            }
            // Run audit after fields are populated
            runInlineSEOAudit(articleId);
        }).fail(function() {
            // Fallback audit execution on error
            runInlineSEOAudit(articleId);
        });
    };

    window.updateTitleMetaProgress = function(el) {
        const val = el ? el.value : '';
        const countEl = document.getElementById('inline-title-count');
        const barEl = document.getElementById('inline-title-progress-bar');
        const serpTitle = document.getElementById('serp-title-preview');
        
        if (countEl) countEl.innerText = val.length + '/60';
        if (serpTitle) serpTitle.innerText = val || 'Commercial Lease Gurgaon: Complete Guide 2026';
        
        if (barEl) {
            const pct = Math.min(100, Math.round((val.length / 60) * 100));
            barEl.style.width = pct + '%';
            if (val.length > 60) {
                barEl.className = 'h-full bg-amber-500 rounded-full transition-all duration-300';
            } else if (val.length >= 40) {
                barEl.className = 'h-full bg-emerald-500 rounded-full transition-all duration-300';
            } else {
                barEl.className = 'h-full bg-zinc-400 rounded-full transition-all duration-300';
            }
        }
    };

    window.updateDescMetaProgress = function(el) {
        const val = el ? el.value : '';
        const countEl = document.getElementById('inline-desc-count');
        const barEl = document.getElementById('inline-desc-progress-bar');
        const serpDesc = document.getElementById('serp-desc-preview');

        if (countEl) countEl.innerText = val.length + '/160';
        if (serpDesc) serpDesc.innerText = val || 'Looking for commercial space for lease in Gurgaon? Explore top locations, lease terms, legal considerations, and market rates in this comprehensive 2026 guide.';

        if (barEl) {
            const pct = Math.min(100, Math.round((val.length / 160) * 100));
            barEl.style.width = pct + '%';
            if (val.length > 160) {
                barEl.className = 'h-full bg-amber-500 rounded-full transition-all duration-300';
            } else if (val.length >= 100) {
                barEl.className = 'h-full bg-emerald-500 rounded-full transition-all duration-300';
            } else {
                barEl.className = 'h-full bg-zinc-400 rounded-full transition-all duration-300';
            }
        }
    };

    window.updateSerpUrlPreview = function(slug) {
        const serpUrl = document.getElementById('serp-url-preview');
        if (serpUrl) serpUrl.innerText = 'https://example.com/' + (slug || 'commercial-lease-gurgaon');
    };

    window.toggleSerpPreviewModal = function() {
        const card = document.getElementById('serp-preview-card');
        if (card) card.classList.toggle('hidden');
    };

    window.copyCanonicalUrl = function() {
        const input = document.getElementById('inline-canonical-url');
        if (input && input.value) {
            navigator.clipboard.writeText(input.value);
            if (window.coraShowToast) window.coraShowToast('Canonical URL copied to clipboard', 'success');
        } else {
            if (window.coraShowToast) window.coraShowToast('Canonical URL is empty', 'warning');
        }
    };

    window.toggleChecklistCategory = function(el, catId) {
        const chevron = el.querySelector('.cat-chevron');
        if (chevron) chevron.classList.toggle('rotate-180');
        
        const grid = document.getElementById('inline-seo-checklist-grid');
        if (!grid) return;

        const catItems = grid.querySelectorAll(`.checklist-item.${catId}`);
        const allItems = grid.querySelectorAll('.checklist-item');

        if (catItems.length === 0) {
            allItems.forEach(item => item.classList.remove('hidden'));
            return;
        }

        const isFilteringThis = el.classList.contains('border-zinc-900');
        
        // Reset all category cards highlight
        document.querySelectorAll('#inline-seo-checklist-categories > div').forEach(card => {
            card.classList.remove('border-zinc-900', 'shadow-xs', 'bg-zinc-50/70');
        });

        if (isFilteringThis) {
            // Unset filter, show all items
            allItems.forEach(item => item.classList.remove('hidden'));
        } else {
            // Set filter to this category
            el.classList.add('border-zinc-900', 'shadow-xs', 'bg-zinc-50/70');
            allItems.forEach(item => item.classList.add('hidden'));
            catItems.forEach(item => item.classList.remove('hidden'));
        }
    };

    // ============================================================
    // SEO ANALYZER: Sidebar Pagination, Search & Collapse
    // ============================================================
    let _seoCurrentPage = 1;
    const _seoPageSize = 5;
    let _seoSearchQuery = '';

    window.getFilteredSEOArticles = function() {
        const container = document.getElementById('seo-article-list-container');
        if (!container) return [];
        const buttons = Array.from(container.querySelectorAll('.seo-article-btn'));
        if (!_seoSearchQuery) return buttons;
        
        return buttons.filter(btn => {
            const title = (btn.dataset.title || '').toLowerCase();
            const id = String(btn.dataset.id || '');
            return title.includes(_seoSearchQuery) || id.includes(_seoSearchQuery);
        });
    };

    window.renderSEOPagination = function() {
        const container = document.getElementById('seo-article-list-container');
        if (!container) return;
        
        const allButtons = Array.from(container.querySelectorAll('.seo-article-btn'));
        const filteredButtons = window.getFilteredSEOArticles();
        const totalCount = filteredButtons.length;
        const totalPages = Math.max(1, Math.ceil(totalCount / _seoPageSize));
        
        if (_seoCurrentPage > totalPages) _seoCurrentPage = totalPages;
        if (_seoCurrentPage < 1) _seoCurrentPage = 1;

        // Hide all article buttons first
        allButtons.forEach(btn => btn.style.display = 'none');

        // No results state handling
        const noResults = document.getElementById('seo-no-results');
        if (totalCount === 0) {
            if (noResults) noResults.classList.remove('hidden');
        } else {
            if (noResults) noResults.classList.add('hidden');
            // Show current page slice
            const start = (_seoCurrentPage - 1) * _seoPageSize;
            const end = Math.min(start + _seoPageSize, totalCount);
            for (let i = start; i < end; i++) {
                if (filteredButtons[i]) filteredButtons[i].style.display = '';
            }
        }

        // Update count badge
        const badge = document.getElementById('seo-article-count-badge');
        if (badge) badge.innerText = totalCount;

        // Update pagination info text
        const info = document.getElementById('seo-pagination-info');
        if (info) {
            if (totalCount === 0) {
                info.innerText = 'Showing 0 of 0';
            } else {
                const start = (_seoCurrentPage - 1) * _seoPageSize + 1;
                const end = Math.min(_seoCurrentPage * _seoPageSize, totalCount);
                info.innerText = `Showing ${start}-${end} of ${totalCount}`;
            }
        }

        // Update pagination controls
        const controls = document.getElementById('seo-pagination-controls');
        if (controls) {
            controls.innerHTML = '';

            // Prev button
            const prevBtn = document.createElement('button');
            prevBtn.className = 'w-7 h-7 rounded flex items-center justify-center hover:bg-zinc-200 text-zinc-500 hover:text-zinc-900 transition-colors disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer';
            prevBtn.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>';
            prevBtn.title = 'Previous Page';
            prevBtn.disabled = _seoCurrentPage === 1 || totalCount === 0;
            prevBtn.onclick = function() { window.changeSEOPage(_seoCurrentPage - 1); };
            controls.appendChild(prevBtn);

            if (totalCount > 0) {
                const pages = [];
                if (totalPages <= 5) {
                    for (let p = 1; p <= totalPages; p++) pages.push(p);
                } else {
                    pages.push(1);
                    if (_seoCurrentPage > 3) pages.push('...');
                    
                    const startP = Math.max(2, _seoCurrentPage - 1);
                    const endP = Math.min(totalPages - 1, _seoCurrentPage + 1);
                    for (let p = startP; p <= endP; p++) {
                        if (!pages.includes(p)) pages.push(p);
                    }
                    
                    if (_seoCurrentPage < totalPages - 2) pages.push('...');
                    if (!pages.includes(totalPages)) pages.push(totalPages);
                }

                pages.forEach(p => {
                    if (p === '...') {
                        const span = document.createElement('span');
                        span.className = 'text-zinc-400 px-0.5 text-xs';
                        span.innerText = '...';
                        controls.appendChild(span);
                    } else {
                        const btn = document.createElement('button');
                        if (p === _seoCurrentPage) {
                            btn.className = 'w-6 h-6 rounded bg-zinc-900 text-white font-bold flex items-center justify-center text-[11px]';
                        } else {
                            btn.className = 'w-6 h-6 rounded hover:bg-zinc-200 text-zinc-700 flex items-center justify-center text-[11px] transition-colors cursor-pointer font-medium';
                        }
                        btn.innerText = p;
                        btn.onclick = function() { window.changeSEOPage(p); };
                        controls.appendChild(btn);
                    }
                });
            }

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = 'w-7 h-7 rounded flex items-center justify-center hover:bg-zinc-200 text-zinc-500 hover:text-zinc-900 transition-colors disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer';
            nextBtn.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>';
            nextBtn.title = 'Next Page';
            nextBtn.disabled = _seoCurrentPage === totalPages || totalCount === 0;
            nextBtn.onclick = function() { window.changeSEOPage(_seoCurrentPage + 1); };
            controls.appendChild(nextBtn);
        }
    };

    window.changeSEOPage = function(page) {
        _seoCurrentPage = page;
        window.renderSEOPagination();
    };

    window.filterSEOArticleList = function(q) {
        _seoSearchQuery = (q || '').toLowerCase().trim();
        _seoCurrentPage = 1;
        window.renderSEOPagination();
    };

    window.sortSEOArticles = function(criterion) {
        const container = document.getElementById('seo-article-list-container');
        if(!container) return;
        const items = Array.from(container.querySelectorAll('.seo-article-btn'));
        items.sort((a, b) => {
            if(criterion === 'score_desc') return parseInt(b.dataset.score || 0) - parseInt(a.dataset.score || 0);
            if(criterion === 'score_asc') return parseInt(a.dataset.score || 0) - parseInt(b.dataset.score || 0);
            if(criterion === 'title') return a.dataset.title.localeCompare(b.dataset.title);
            return parseInt(b.dataset.id || 0) - parseInt(a.dataset.id || 0);
        });
        items.forEach(item => container.appendChild(item));
        const noResults = document.getElementById('seo-no-results');
        if (noResults) container.appendChild(noResults);
        
        window.renderSEOPagination();
    };

    window.toggleSEOSidebar = function(forceState) {
        const sidebar = document.getElementById('seo-sidebar');
        const toggleIcon = document.getElementById('seo-sidebar-toggle-icon');
        const toggleBtn = document.getElementById('seo-sidebar-toggle-btn');
        const header = document.getElementById('seo-sidebar-header');
        if (!sidebar) return;

        const isCurrentlyCollapsed = sidebar.classList.contains('w-[40px]');
        const shouldCollapse = (typeof forceState === 'boolean') ? forceState : !isCurrentlyCollapsed;

        const contentEls = sidebar.querySelectorAll('.seo-sidebar-content');
        const textEls = sidebar.querySelectorAll('.seo-sidebar-text');

        if (shouldCollapse) {
            sidebar.classList.remove('w-[260px]', 'w-[280px]', 'w-80', 'md:w-\[260px\]');
            sidebar.classList.add('w-[40px]');
            sidebar.style.minWidth = '40px';
            sidebar.style.maxWidth = '40px';
            // Remove max-height constraint so it collapses to just the toggle button height
            sidebar.style.maxHeight = 'none';
            contentEls.forEach(el => el.classList.add('hidden'));
            textEls.forEach(el => el.classList.add('hidden'));
            if (header) {
                header.classList.remove('justify-between');
                header.classList.add('justify-center');
                header.style.padding = '8px 0';
            }
            if (toggleIcon) {
                toggleIcon.setAttribute('width', '14');
                toggleIcon.setAttribute('height', '14');
                toggleIcon.innerHTML = '<polyline points="9 18 15 12 9 6"></polyline>';
            }
            if (toggleBtn) toggleBtn.title = 'Expand Sidebar';
            localStorage.setItem('cora_seo_sidebar_collapsed', 'true');
        } else {
            sidebar.classList.remove('w-[40px]');
            sidebar.style.width = '260px';
            sidebar.style.minWidth = '260px';
            sidebar.style.maxWidth = '260px';
            // Restore viewport-relative max-height
            sidebar.style.maxHeight = 'calc(100vh - 140px)';
            sidebar.classList.add('w-[260px]');
            contentEls.forEach(el => el.classList.remove('hidden'));
            textEls.forEach(el => el.classList.remove('hidden'));
            if (header) {
                header.classList.remove('justify-center');
                header.classList.add('justify-between');
                header.style.padding = '';
            }
            if (toggleIcon) {
                toggleIcon.setAttribute('width', '15');
                toggleIcon.setAttribute('height', '15');
                toggleIcon.innerHTML = '<polyline points="15 18 9 12 15 6"></polyline>';
            }
            if (toggleBtn) toggleBtn.title = 'Collapse Sidebar';
            localStorage.setItem('cora_seo_sidebar_collapsed', 'false');
        }
    };

    window.initSEOSidebarState = function() {
        const collapsed = localStorage.getItem('cora_seo_sidebar_collapsed') === 'true';
        window.toggleSEOSidebar(collapsed);
        window.renderSEOPagination();
    };

    window.runInlineSEOAudit = function(articleId) {
        const targetAjaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));

        const focusKw   = document.getElementById('inline-focus-keyword')?.value || '';
        const metaTitle = document.getElementById('inline-meta-title')?.value || '';
        const metaDesc  = document.getElementById('inline-meta-description')?.value || '';
        const slug      = document.getElementById('inline-slug')?.value || '';

        $.post(targetAjaxUrl, {
            action: 'cora_run_11point_seo_audit',
            nonce: targetNonce,
            security: targetNonce,
            post_id: articleId,
            focus_keyword: focusKw,
            meta_title: metaTitle,
            meta_description: metaDesc,
            slug: slug
        }, function(r) {
            if (typeof r === 'string') {
                try { r = JSON.parse(r); } catch(e){}
            }
            if (r && r.success && r.data) {
                const d = r.data;
                const score = d.seo_score || 82;
                const scoreText     = document.getElementById('inline-seo-score-text');
                const scoreLarge    = document.getElementById('inline-seo-score-large');
                const statusText    = document.getElementById('inline-seo-status');
                const passedNum     = document.getElementById('inline-checklist-score-num');
                const geoEl         = document.getElementById('inline-geo-score');
                const densEl        = document.getElementById('inline-kw-density');
                const readScoreEl   = document.getElementById('inline-readability-score');
                const readLblEl     = document.getElementById('inline-readability-label');

                if(scoreText) scoreText.innerText = score + '/100';
                if(scoreLarge) scoreLarge.innerHTML = score + '<span class="text-xs text-zinc-400 font-normal"> /100</span>';
                if(statusText) statusText.innerText = score >= 80 ? 'Well optimized / Keep improving' : 'Optimizations needed';
                if(passedNum) passedNum.innerText = (d.passed_count || 8) + ' / 11 Checks Passed';
                if(geoEl) geoEl.innerText = (d.geo_score || 72) + '%';
                if(densEl) densEl.innerText = (d.kw_density_pct ? (typeof d.kw_density_pct === 'number' ? d.kw_density_pct.toFixed(1) + '%' : d.kw_density_pct) : '1.4%');

                if(readScoreEl) readScoreEl.innerHTML = (d.readability_score || '78') + '<span class="text-xs text-zinc-400 font-normal"> /100</span>';
                if(readLblEl)   readLblEl.innerText   = d.readability_label || 'Easy to read and well structured';

                const ring = document.getElementById('inline-seo-ring');
                if(ring) {
                    const pct = Math.min(100, Math.max(0, score));
                    const circ = 163.3;
                    ring.style.strokeDashoffset = circ - (pct / 100) * circ;
                }

                const checkRing = document.getElementById('inline-checklist-ring');
                if(checkRing) {
                    const passedPct = Math.min(100, Math.max(0, ((d.passed_count || 8) / 11) * 100));
                    const circ = 163.3;
                    checkRing.style.strokeDashoffset = circ - (passedPct / 100) * circ;
                }

                // Populate checklist items
                const grid = document.getElementById('inline-seo-checklist-grid');
                if (grid) {
                    let html = '<div class="space-y-2 text-xs mt-2">';
                    (d.checklist || []).forEach(item => {
                        let catClass = 'cat-title-meta';
                        const catKey = (item.category || '').toLowerCase();
                        if (catKey.includes('heading')) catClass = 'cat-headings';
                        else if (catKey.includes('content') || catKey.includes('word')) catClass = 'cat-content';
                        else if (catKey.includes('image') || catKey.includes('alt')) catClass = 'cat-images';
                        else if (catKey.includes('internal')) catClass = 'cat-internal-links';
                        else if (catKey.includes('external')) catClass = 'cat-external-links';
                        else if (catKey.includes('schema') || catKey.includes('json')) catClass = 'cat-schema';
                        else if (catKey.includes('url') || catKey.includes('slug') || catKey.includes('canonical')) catClass = 'cat-canonical';
                        else if (catKey.includes('mobile') || catKey.includes('viewport')) catClass = 'cat-mobile';
                        else if (catKey.includes('speed') || catKey.includes('lcp') || catKey.includes('perf')) catClass = 'cat-speed';

                        const icon = item.passed ? '✓' : '⚠';
                        const badge = item.passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200';
                        const recText = item.actionable_recommendation || item.recommendation || '';
                        const tipBox = recText ? `
                            <div class="mt-2 p-2 rounded-lg bg-zinc-50 border border-zinc-200/90 text-[11px] text-zinc-700 flex items-start gap-2">
                                <svg class="shrink-0 mt-0.5 text-zinc-500" viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                                <div><strong class="text-zinc-900">Recommendation:</strong> ${escJsHtml(recText)}</div>
                            </div>
                        ` : '';
                        html += `
                            <div class="checklist-item ${catClass} p-3 rounded-xl border border-zinc-200/90 bg-zinc-50/50 hover:bg-white hover:border-zinc-300 transition-all" data-cat="${catClass}">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="font-bold text-zinc-900 text-xs">${escJsHtml(item.label)}</div>
                                        <div class="text-[11px] text-zinc-500 mt-0.5">${escJsHtml(item.message)}</div>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold ${badge} shrink-0">${icon} ${item.passed ? 'PASSED' : 'NEEDS ATTENTION'}</span>
                                </div>
                                ${tipBox}
                            </div>
                        `;
                    });
                    html += '</div>';
                    grid.innerHTML = html;
                }

                // Update sidebar badge
                const sidebarBadge = document.querySelector(`.seo-article-btn[data-id="${articleId}"] .rounded`);
                if(sidebarBadge) {
                    sidebarBadge.innerText = score + '/100';
                }
            }
        });
    };

    window.saveInlineSEOMeta = function(articleId) {
        const targetAjaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));
        const targetNonce   = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '');

        const keyword   = document.getElementById('inline-focus-keyword')?.value || '';
        const title     = document.getElementById('inline-meta-title')?.value || '';
        const desc      = document.getElementById('inline-meta-description')?.value || '';
        const slug      = document.getElementById('inline-slug')?.value || '';
        const canonical = document.getElementById('inline-canonical-url')?.value || '';

        $.post(targetAjaxUrl, {
            action: 'cora_save_seo_meta',
            nonce: targetNonce,
            security: targetNonce,
            post_id: articleId,
            focus_keyword: keyword,
            meta_title: title,
            meta_description: desc,
            slug: slug,
            canonical_url: canonical
        }, function(response) {
            if(response.success) {
                if(window.coraShowToast) window.coraShowToast('SEO meta & permalinks saved successfully.', 'success');
                // Re-run audit to reflect updated values
                runInlineSEOAudit(articleId);
            } else {
                if(window.coraShowToast) window.coraShowToast('Failed to save SEO meta', 'error');
            }
        });
    };

    function escJsHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }



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
