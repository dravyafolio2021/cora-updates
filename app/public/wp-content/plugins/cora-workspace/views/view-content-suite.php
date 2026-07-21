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
    <div class="cora-stat-card p-4 bg-white border border-zinc-200 rounded-xl shadow-sm">
        <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Total Articles</div>
        <div class="text-2xl font-bold text-zinc-900"><?php echo esc_html($total_articles); ?></div>
    </div>
    <div class="cora-stat-card p-4 bg-white border border-zinc-200 rounded-xl shadow-sm">
        <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Published</div>
        <div class="text-2xl font-bold text-zinc-900"><?php echo esc_html($published_count); ?></div>
    </div>
    <div class="cora-stat-card p-4 bg-white border border-zinc-200 rounded-xl shadow-sm">
        <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Drafts</div>
        <div class="text-2xl font-bold text-zinc-900"><?php echo esc_html($draft_count); ?></div>
    </div>
    <div class="cora-stat-card p-4 bg-white border border-zinc-200 rounded-xl shadow-sm">
        <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Avg SEO Score</div>
        <div class="text-2xl font-bold text-zinc-900"><?php echo esc_html($avg_seo); ?></div>
    </div>
    <div class="cora-stat-card p-4 bg-white border border-zinc-200 rounded-xl shadow-sm">
        <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Total Leads Captured</div>
        <div class="text-2xl font-bold text-zinc-900"><?php echo esc_html($total_leads); ?></div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="flex items-center gap-1 border-b border-zinc-200 mb-6 select-none" id="cora-content-tabs">
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-zinc-950 text-zinc-900 flex items-center gap-1.5" data-tab="ct-library" onclick="switchContentTab('ct-library')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        Content Library
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
</div>

<!-- PANEL: Content Library -->
<div id="panel-ct-library" class="cora-ct-panel block">
    <div class="flex items-center gap-3 mb-4">
        <div class="flex gap-2">
            <button class="px-3 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 rounded text-sm font-medium" onclick="filterContentByStatus('all')">All</button>
            <button class="px-3 py-1 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-600 rounded text-sm font-medium" onclick="filterContentByStatus('published')">Published</button>
            <button class="px-3 py-1 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-600 rounded text-sm font-medium" onclick="filterContentByStatus('draft')">Draft</button>
            <button class="px-3 py-1 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-600 rounded text-sm font-medium" onclick="filterContentByStatus('pending_review')">In Review</button>
            <button class="px-3 py-1 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-600 rounded text-sm font-medium" onclick="filterContentByStatus('approved')">Approved</button>
        </div>
        <select id="ct-filter-author" class="border border-zinc-200 rounded px-2 py-1 text-sm bg-white" onchange="filterContentByAuthor(this.value)">
            <option value="all">All Authors</option>
            <?php foreach($cora_users as $u): ?>
                <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
            <?php endforeach; ?>
        </select>
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-2.5 top-2 text-zinc-400" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="ct-search" class="w-full pl-9 pr-3 py-1.5 border border-zinc-200 rounded text-sm focus:outline-none focus:border-zinc-500" placeholder="Search articles..." oninput="searchContentTable(this.value)">
        </div>
        <select id="ct-bulk-actions" class="border border-zinc-200 rounded px-2 py-1 text-sm bg-white ml-auto opacity-50 cursor-not-allowed" disabled>
            <option value="">Bulk Actions</option>
            <option value="publish">Publish</option>
            <option value="delete">Delete</option>
            <option value="assign">Assign</option>
        </select>
    </div>

    <div class="border border-zinc-200 rounded-lg overflow-hidden bg-white shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-50 border-b border-zinc-200 text-xs font-bold text-zinc-500 uppercase tracking-wider">
                    <th class="py-3 px-4 w-10"><input type="checkbox" class="rounded border-zinc-300" id="ct-select-all" onclick="toggleSelectAll(this)"></th>
                    <th class="py-3 px-4">Article</th>
                    <th class="py-3 px-4">Author</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">SEO</th>
                    <th class="py-3 px-4">GEO</th>
                    <th class="py-3 px-4">Leads/CR</th>
                    <th class="py-3 px-4">Modified</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="cora-content-table-body" class="divide-y divide-zinc-100 text-sm text-zinc-700">
                <?php if (empty($cora_posts)): ?>
                    <tr>
                        <td colspan="9" class="py-12 text-center text-zinc-500">
                            <svg class="mx-auto mb-3 text-zinc-300" viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="1.5" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                            <p>No articles found. Click "New Article" to start.</p>
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
                        
                        $seo_color = $seo_score >= 80 ? 'bg-zinc-900' : ($seo_score >= 60 ? 'bg-zinc-600' : 'bg-zinc-400');
                        $geo_color = $geo_score >= 80 ? 'bg-zinc-900' : ($geo_score >= 60 ? 'bg-zinc-600' : 'bg-zinc-400');
                    ?>
                    <tr class="hover:bg-zinc-50 transition-colors ct-row" data-status="<?php echo esc_attr($editorial_status); ?>" data-author="<?php echo esc_attr($assignee_id); ?>" data-title="<?php echo esc_attr(strtolower($post->post_title)); ?>">
                        <td class="py-3 px-4"><input type="checkbox" class="rounded border-zinc-300 ct-row-checkbox" value="<?php echo $post->ID; ?>" onchange="updateBulkActions()"></td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <?php if($thumbnail_url): ?>
                                    <img src="<?php echo esc_url($thumbnail_url); ?>" class="w-8 h-8 rounded object-cover bg-zinc-100">
                                <?php else: ?>
                                    <div class="w-8 h-8 rounded bg-zinc-100 flex items-center justify-center text-zinc-400">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="font-bold text-zinc-900 text-sm line-clamp-1"><?php echo esc_html($post->post_title); ?></div>
                                    <div class="text-xs text-zinc-500"><?php echo number_format($word_count); ?> words</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-xs font-medium"><?php echo esc_html($assignee_name); ?></td>
                        <td class="py-3 px-4">
                            <?php if($editorial_status === 'published'): ?>
                                <span class="px-2 py-0.5 bg-zinc-900 text-white rounded text-[10px] font-bold uppercase tracking-wide">Published</span>
                            <?php elseif($editorial_status === 'pending_review'): ?>
                                <span class="px-2 py-0.5 bg-zinc-200 text-zinc-800 rounded text-[10px] font-bold uppercase tracking-wide">In Review</span>
                            <?php elseif($editorial_status === 'approved'): ?>
                                <span class="px-2 py-0.5 bg-zinc-800 text-white rounded text-[10px] font-bold uppercase tracking-wide">Approved</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-zinc-100 text-zinc-600 rounded text-[10px] font-bold uppercase tracking-wide">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs"><?php echo $seo_score; ?></span>
                                <div class="w-16 h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                                    <div class="h-full <?php echo $seo_color; ?>" style="width: <?php echo $seo_score; ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs"><?php echo $geo_score; ?></span>
                                <div class="w-16 h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                                    <div class="h-full <?php echo $geo_color; ?>" style="width: <?php echo $geo_score; ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <div class="font-medium"><?php echo $lead_count; ?></div>
                            <div class="text-zinc-500"><?php echo $conv_rate; ?></div>
                        </td>
                        <td class="py-3 px-4 text-xs text-zinc-500"><?php echo $modified; ?></td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-3 text-xs font-medium">
                                <button class="text-zinc-600 hover:text-zinc-900 cursor-pointer" onclick="coraEditArticle(<?php echo $post->ID; ?>)">Edit</button>
                                <button class="text-zinc-600 hover:text-zinc-900 cursor-pointer" onclick="openSEODetailDrawer(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">SEO</button>
                                <button class="text-zinc-400 hover:text-zinc-900 cursor-pointer" title="More actions">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
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

<!-- PANEL: SEO Analyzer -->
<div id="panel-ct-seo" class="cora-ct-panel hidden">
    <div class="flex gap-6">
        <!-- Left: Article List -->
        <div class="w-64 shrink-0 bg-white border border-zinc-200 rounded-lg shadow-sm h-[600px] flex flex-col overflow-hidden">
            <div class="p-3 border-b border-zinc-200 bg-zinc-50">
                <input type="text" id="seo-search" class="w-full px-2 py-1.5 border border-zinc-200 rounded text-sm focus:outline-none" placeholder="Search...">
            </div>
            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                <?php foreach($cora_posts as $post): 
                    $score = get_post_meta($post->ID, '_cora_seo_score', true) ?: 75;
                ?>
                <button class="w-full text-left p-2 hover:bg-zinc-50 rounded flex justify-between items-center cursor-pointer transition-colors" onclick="openSEOAnalysis(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">
                    <div class="truncate pr-2 text-sm text-zinc-700 font-medium"><?php echo esc_html($post->post_title); ?></div>
                    <span class="shrink-0 px-1.5 py-0.5 bg-zinc-100 rounded text-[10px] font-bold text-zinc-600"><?php echo $score; ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Right: Analysis Area -->
        <div class="flex-1 bg-white border border-zinc-200 rounded-lg shadow-sm p-6" id="seo-analysis-container">
            <div class="text-center text-zinc-500 py-20">
                <svg class="mx-auto mb-3" viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="1.5" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                <p>Select an article from the left to analyze.</p>
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
    <div class="flex gap-4 overflow-x-auto pb-4">
        <?php
        // Group articles into kanban columns by editorial status
        $cal_draft = $cal_review = $cal_scheduled = $cal_published = [];
        foreach($cora_posts as $p) {
            $es = get_post_meta($p->ID, '_cora_editorial_status', true) ?: ($p->post_status === 'publish' ? 'published' : 'draft');
            $p->_cal_status = $es;
            $p->_cal_thumb = get_the_post_thumbnail_url($p->ID, 'thumbnail');
            $p->_cal_date  = get_the_modified_date('M j', $p->ID);
            $p->_cal_assignee = get_post_meta($p->ID, '_cora_assignee_id', true);
            $p->_cal_assignee_name = $p->_cal_assignee ? (get_userdata($p->_cal_assignee)->display_name ?? 'Unassigned') : 'Unassigned';
            $p->_cal_seo = get_post_meta($p->ID, '_cora_seo_score', true) ?: 75;
            if ($es === 'published') $cal_published[] = $p;
            elseif ($es === 'pending_review') $cal_review[] = $p;
            elseif ($es === 'approved') $cal_scheduled[] = $p;
            else $cal_draft[] = $p;
        }
        $kanban_cols = [
            ['id'=>'draft','label'=>'Drafts','articles'=>$cal_draft],
            ['id'=>'review','label'=>'In Review','articles'=>$cal_review],
            ['id'=>'scheduled','label'=>'Scheduled','articles'=>$cal_scheduled],
            ['id'=>'published','label'=>'Published','articles'=>$cal_published],
        ];
        foreach($kanban_cols as $col):
        ?>
        <div class="w-72 shrink-0 flex flex-col bg-zinc-50 border border-zinc-200 rounded-xl">
            <div class="p-3 border-b border-zinc-200 font-bold text-xs text-zinc-700 uppercase tracking-wider flex justify-between items-center">
                <?php echo esc_html($col['label']); ?>
                <span class="text-zinc-400 text-[10px] font-semibold"><?php echo count($col['articles']); ?></span>
                <button class="text-zinc-400 hover:text-zinc-900 ml-auto" onclick="openCreateArticleDrawer()">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
            </div>
            <div class="p-2 space-y-2 overflow-y-auto" style="max-height:480px">
                <?php if(empty($col['articles'])): ?>
                <div class="text-center text-zinc-400 text-xs py-8">No articles</div>
                <?php else: foreach($col['articles'] as $ca): ?>
                <div class="bg-white border border-zinc-200 rounded-lg p-3 shadow-sm cursor-pointer hover:border-zinc-400 transition-colors" onclick="coraEditArticle(<?php echo $ca->ID; ?>)">
                    <?php if($ca->_cal_thumb): ?>
                    <img src="<?php echo esc_url($ca->_cal_thumb); ?>" class="w-full h-20 object-cover rounded mb-2">
                    <?php endif; ?>
                    <div class="text-xs font-bold text-zinc-900 line-clamp-2 mb-1"><?php echo esc_html($ca->post_title); ?></div>
                    <div class="flex items-center justify-between mt-1.5">
                        <span class="text-[10px] text-zinc-500"><?php echo esc_html($ca->_cal_assignee_name); ?></span>
                        <span class="text-[10px] font-bold text-zinc-500"><?php echo esc_html($ca->_cal_date); ?></span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 bg-zinc-100 rounded text-zinc-700"><?php echo (int)$ca->_cal_seo; ?></span>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Month Calendar Grid -->
    <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm">
        <?php
        $month_now = date('n');
        $year_now  = date('Y');
        $month_name = date('F Y');
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month_now, $year_now);
        $first_dow = date('N', mktime(0,0,0,$month_now,1,$year_now)); // 1=Mon..7=Sun
        // Build publish date map
        $pub_dates = [];
        foreach($cora_posts as $pp) {
            $d = get_the_date('j', $pp->ID);
            $m = get_the_date('n', $pp->ID);
            if((int)$m === (int)$month_now) $pub_dates[(int)$d] = true;
        }
        ?>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-zinc-900"><?php echo esc_html($month_name); ?></h3>
            <div class="flex gap-2 text-xs text-zinc-500">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-zinc-900 inline-block"></span>Published</span>
            </div>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center">
            <?php foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dn): ?>
            <div class="text-[10px] font-bold text-zinc-400 uppercase py-1"><?php echo $dn; ?></div>
            <?php endforeach; ?>
            <?php for($pad=1; $pad < $first_dow; $pad++): ?>
            <div></div>
            <?php endfor; ?>
            <?php for($d=1; $d<=$days_in_month; $d++): ?>
            <div class="relative py-1.5 rounded text-xs <?php echo $d == date('j') ? 'bg-zinc-900 text-white font-bold' : 'text-zinc-700 hover:bg-zinc-50'; ?> cursor-default">
                <?php echo $d; ?>
                <?php if(isset($pub_dates[$d])): ?>
                <span class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-zinc-400 <?php echo $d == date('j') ? 'bg-white' : ''; ?>"></span>
                <?php endif; ?>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- DRAWERS -->
<!-- Drawer Backdrop -->
<div id="cora-drawer-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[9998] hidden transition-opacity" onclick="window.coraCloseAllDrawers()"></div>

<!-- Create Article Drawer -->
<aside id="cora-create-article-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[480px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="px-6 py-4 border-b border-zinc-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-zinc-900">New Article</h2>
            <p class="text-xs text-zinc-500">Draft a new SEO-optimized article.</p>
        </div>
        <button class="text-zinc-400 hover:text-zinc-900" onclick="closeCreateArticleDrawer()">
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
    <div class="p-4 border-t border-zinc-200">
        <button class="w-full bg-zinc-900 text-white font-bold py-2.5 rounded hover:bg-zinc-800 transition-colors" onclick="submitCreateArticle(event)">Create Article</button>
    </div>
</aside>

<!-- SEO Detail Drawer -->
<aside id="cora-seo-detail-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[500px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="px-6 py-4 border-b border-zinc-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-zinc-900" id="seo-drawer-title">SEO Deep Analysis</h2>
            <p class="text-xs text-zinc-500">Analyze and optimize article performance.</p>
        </div>
        <button class="text-zinc-400 hover:text-zinc-900" onclick="closeSEODetailDrawer()">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <div class="p-6 overflow-y-auto flex-1 space-y-6">
        <input type="hidden" id="seo-drawer-article-id">
        <div class="flex justify-between items-center">
            <button class="bg-zinc-900 text-white px-3 py-1.5 rounded text-sm font-bold" onclick="runSEOAnalysis(document.getElementById('seo-drawer-article-id').value)">Run Analysis</button>
            <div class="w-16 h-16 rounded-full border-4 border-zinc-900 flex items-center justify-center text-xl font-bold" id="seo-drawer-score">--</div>
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
    };

    // On Load
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const ct = urlParams.get('ct') || 'ct-library';
        switchContentTab(ct);
    });

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
        const drawer = document.getElementById('cora-create-article-drawer');
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
        const drawer = document.getElementById('cora-seo-detail-drawer');
        if(drawer) { drawer.classList.remove('translate-x-full', 'collapsed'); }
        showBackdrop();
    };
    
    window.openSEOAnalysis = function(articleId, title) {
        openSEODetailDrawer(articleId, title);
    };

    window.closeSEODetailDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    };

    // Library Filtering
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
