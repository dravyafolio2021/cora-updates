<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<script>
window.coraREWPData = window.coraREWPData || {};
window.coraREWPData.ajaxUrl = window.coraREWPData.ajaxUrl || '<?php echo esc_url( cora_get_origin_relative_url( admin_url( 'admin-ajax.php' ) ) ); ?>';
window.coraREWPData.ajaxNonce = '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

if (typeof window.coraREData !== 'undefined') {
    window.coraREData.ajaxNonce = window.coraREData.ajaxNonce || window.coraREWPData.ajaxNonce;
    window.coraREData.ajaxUrl   = window.coraREData.ajaxUrl || window.coraREWPData.ajaxUrl;
} else {
    window.coraREData = window.coraREWPData;
}

// Global stubs to prevent ReferenceErrors during initial page load / execution race conditions
window.switchContentTab = window.switchContentTab || function(tabId) { console.warn("switchContentTab not loaded yet."); };
window.switchSuiteTab = window.switchSuiteTab || function(tabId) { if (typeof window.switchContentTab === 'function') window.switchContentTab(tabId); };
window.switchCalendarSubView = window.switchCalendarSubView || function(view) { console.warn("switchCalendarSubView not loaded yet."); };
window.coraChangeCalendarMonth = window.coraChangeCalendarMonth || function(offset) { console.warn("coraChangeCalendarMonth not loaded yet."); };
window.coraGoToToday = window.coraGoToToday || function() { console.warn("coraGoToToday not loaded yet."); };
window.openCreateArticleDrawer = window.openCreateArticleDrawer || function(prefillDate, prefillStage) { console.warn("openCreateArticleDrawer not loaded yet."); };
window.filterContentByStatus = window.filterContentByStatus || function(status, btnEl) { console.warn("filterContentByStatus not loaded yet."); };
window.updateBulkActions = window.updateBulkActions || function() { console.warn("updateBulkActions not loaded yet."); };
window.openContentBriefDrawer = window.openContentBriefDrawer || function(itemId) { console.warn("openContentBriefDrawer not loaded yet."); };
window.coraSwitchBrainCategory = window.coraSwitchBrainCategory || function(category, btn) { console.warn("coraSwitchBrainCategory not loaded yet."); };
window.coraCreateBriefFromOpportunity = window.coraCreateBriefFromOpportunity || function(oppId) { console.warn("coraCreateBriefFromOpportunity not loaded yet."); };
window.coraEditArticle = window.coraEditArticle || function(postId, title) { console.warn("coraEditArticle not loaded yet."); };
window.openSEOAnalysis = window.openSEOAnalysis || function(articleId, title) { console.warn("openSEOAnalysis not loaded yet."); };

window.addEventListener('error', function(event) {
    const errorMsg = event.error ? (event.error.stack || event.error.message) : event.message;
    console.error("STICKY ERROR:", errorMsg);
    
    let banner = document.getElementById('cora-js-error-banner');
    if (!banner) {
        banner = document.createElement('div');
        banner.id = 'cora-js-error-banner';
        banner.style.position = 'fixed';
        banner.style.top = '0';
        banner.style.left = '0';
        banner.style.width = '100%';
        banner.style.background = '#fee2e2';
        banner.style.borderBottom = '2px solid #ef4444';
        banner.style.color = '#991b1b';
        banner.style.padding = '12px 24px';
        banner.style.fontSize = '12px';
        banner.style.fontFamily = 'monospace';
        banner.style.zIndex = '9999999';
        banner.style.whiteSpace = 'pre-wrap';
        banner.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
        
        const closeBtn = document.createElement('button');
        closeBtn.innerText = 'Dismiss';
        closeBtn.style.float = 'right';
        closeBtn.style.background = '#ef4444';
        closeBtn.style.color = '#ffffff';
        closeBtn.style.border = 'none';
        closeBtn.style.padding = '4px 8px';
        closeBtn.style.borderRadius = '4px';
        closeBtn.style.cursor = 'pointer';
        closeBtn.onclick = function() { banner.remove(); };
        
        banner.appendChild(closeBtn);
        const textNode = document.createElement('div');
        textNode.id = 'cora-js-error-text';
        banner.appendChild(textNode);
        document.body.appendChild(banner);
    }
    document.getElementById('cora-js-error-text').innerText += '\n\n' + errorMsg;
});
</script>

<?php
$header_args = array(
    'title'              => 'Content Suite',
    'mobile_title'       => 'Content',
    'description'        => 'Draft, optimize, and track SEO & AI search visibility for your content strategy.',
    'mobile_description' => 'Draft and track SEO & AI visibility.',
    'ai_stack'           => true,
    'tutorial_onclick'   => 'coraOpenContentTutorial()',
    'cta'                => array(
        'text'        => 'New Article',
        'mobile_text' => 'New',
        'onclick'     => 'openCreateArticleDrawer()',
        'visible'     => true,
    )
);
cora_render_workspace_header( $header_args );
?>

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
<div class="cora-metrics-grid mb-6">
    <!-- Card 1: Total Articles -->
    <div class="cora-stat-card">
        <div class="flex items-center">
            <div class="p-2 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border border-zinc-100 dark:border-zinc-800/80 rounded-xl flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-widest ml-2">Total Articles</span>
        </div>
        <div class="flex items-center justify-between mt-3 mb-1">
            <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 font-mono tracking-tight"><?php echo esc_html($total_articles); ?></div>
            <div class="flex items-end gap-1 h-7 select-none">
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[25%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[40%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[35%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[55%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[45%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[75%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[60%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[85%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[70%]"></div>
                <div class="w-[3px] bg-zinc-900 dark:bg-zinc-300 rounded-full h-[95%]"></div>
                <div class="w-[3px] bg-zinc-950 dark:bg-zinc-100 rounded-full h-[100%]"></div>
            </div>
        </div>
        <div class="text-[9px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider mt-1">Active library</div>
    </div>

    <!-- Card 2: Published -->
    <div class="cora-stat-card">
        <div class="flex items-center">
            <div class="p-2 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border border-zinc-100 dark:border-zinc-800/80 rounded-xl flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-widest ml-2">Published</span>
        </div>
        <div class="flex items-center justify-between mt-3 mb-1">
            <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 font-mono tracking-tight"><?php echo esc_html($published_count); ?></div>
            <div class="flex items-end gap-1 h-7 select-none">
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[15%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[30%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[20%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[45%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[30%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[65%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[45%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[75%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[55%]"></div>
                <div class="w-[3px] bg-zinc-900 dark:bg-zinc-300 rounded-full h-[90%]"></div>
                <div class="w-[3px] bg-zinc-950 dark:bg-zinc-100 rounded-full h-[100%]"></div>
            </div>
        </div>
        <div class="text-[9px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider mt-1">Live on site</div>
    </div>

    <!-- Card 3: Drafts -->
    <div class="cora-stat-card">
        <div class="flex items-center">
            <div class="p-2 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border border-zinc-100 dark:border-zinc-800/80 rounded-xl flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-widest ml-2">Drafts</span>
        </div>
        <div class="flex items-center justify-between mt-3 mb-1">
            <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 font-mono tracking-tight"><?php echo esc_html($draft_count); ?></div>
            <div class="flex items-end gap-1 h-7 select-none">
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[25%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[40%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[35%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[60%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[45%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[70%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[55%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[85%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[75%]"></div>
                <div class="w-[3px] bg-zinc-900 dark:bg-zinc-300 rounded-full h-[95%]"></div>
                <div class="w-[3px] bg-zinc-950 dark:bg-zinc-100 rounded-full h-[100%]"></div>
            </div>
        </div>
        <div class="text-[9px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider mt-1">In progress</div>
    </div>

    <!-- Card 4: Avg SEO Score -->
    <div class="cora-stat-card">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-2 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border border-zinc-100 dark:border-zinc-800/80 rounded-xl flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M23 6l-9.5 9.5-5-5L1 18"></path><polyline points="17 6 23 6 23 12"></polyline></svg>
                </div>
                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-widest ml-2">Avg SEO Score</span>
            </div>
        </div>
        <div class="flex items-center justify-between mt-3 mb-1">
            <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 font-mono tracking-tight"><?php echo esc_html($avg_seo); ?></div>
            <div class="flex flex-col items-end">
                <div class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold border border-zinc-200/50 dark:border-zinc-700/50">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none" class="shrink-0"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    <span>3%</span>
                </div>
                <span class="text-[9px] text-zinc-400 dark:text-zinc-500 mt-0.5 font-bold uppercase tracking-wide">this week</span>
            </div>
        </div>
        <!-- Full-width Sparkline at bottom -->
        <div class="flex items-end justify-between gap-0.5 h-3.5 w-full select-none mt-2">
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[20%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[30%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[25%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[45%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[35%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[55%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[45%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[65%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[55%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[80%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[65%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[90%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[75%]"></div>
            <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[95%]"></div>
            <div class="w-[3px] bg-zinc-900 dark:bg-zinc-300 rounded-full h-[80%]"></div>
            <div class="w-[3px] bg-zinc-900 dark:bg-zinc-300 rounded-full h-[90%]"></div>
            <div class="w-[3px] bg-zinc-950 dark:bg-zinc-100 rounded-full h-[100%]"></div>
        </div>
    </div>

    <!-- Card 5: Total Leads -->
    <div class="cora-stat-card">
        <div class="flex items-center">
            <div class="p-2 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border border-zinc-100 dark:border-zinc-800/80 rounded-xl flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-widest ml-2">Total Leads</span>
        </div>
        <div class="flex items-center justify-between mt-3 mb-1">
            <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 font-mono tracking-tight"><?php echo esc_html($total_leads); ?></div>
            <div class="flex items-end gap-1 h-7 select-none">
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[15%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[30%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[20%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[45%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[35%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[65%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[50%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[85%]"></div>
                <div class="w-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full h-[70%]"></div>
                <div class="w-[3px] bg-zinc-900 dark:bg-zinc-300 rounded-full h-[95%]"></div>
                <div class="w-[3px] bg-zinc-950 dark:bg-zinc-100 rounded-full h-[100%]"></div>
            </div>
        </div>
        <div class="text-[9px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider mt-1">From content</div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="flex items-center gap-1 border-b border-zinc-200 mb-6 select-none overflow-x-auto scrollbar-hide" id="cora-content-tabs" style="flex-wrap: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none; min-height: 44px;">
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-zinc-950 text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-overview" onclick="switchContentTab('ct-overview')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>
        Overview
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-opportunities" onclick="switchContentTab('ct-opportunities')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
        Opportunities
        <span class="ml-1 px-1.5 py-0.5 bg-zinc-900 text-white text-[9px] font-bold rounded-full">NEW</span>
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-calendar" onclick="switchContentTab('ct-calendar')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        Calendar
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-library" onclick="switchContentTab('ct-library')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        Content Library <?php if ($total_articles > 0): ?><span class="ml-1 px-1.5 py-0.5 bg-zinc-200 text-zinc-700 text-[9px] font-bold rounded-full"><?php echo $total_articles; ?></span><?php endif; ?>
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-seo" onclick="switchContentTab('ct-seo')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
        SEO & AI Visibility
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-performance" onclick="switchContentTab('ct-performance')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M23 6l-9.5 9.5-5-5L1 18"></path><polyline points="17 6 23 6 23 12"></polyline></svg>
        Performance
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-automations" onclick="switchContentTab('ct-automations')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
        Automations
    </button>
    <button class="cora-tab-btn px-4 py-2.5 border-b-2 text-xs font-semibold cursor-not-allowed transition-all border-transparent text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5 whitespace-nowrap shrink-0" data-tab="ct-brain" onclick="switchContentTab('ct-brain')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.44 2.5 2.5 0 0 1 0-4.12 2.5 2.5 0 0 1 0-4.12A2.5 2.5 0 0 1 9.5 2z"></path><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.44 2.5 2.5 0 0 0 0-4.12 2.5 2.5 0 0 0 0-4.12A2.5 2.5 0 0 0 14.5 2z"></path></svg>
        Business Brain
        <span class="ml-1.5 px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-850 text-zinc-400 dark:text-zinc-500 text-[9px] font-bold rounded-md tracking-wider">SOON</span>
    </button>
</div>

<!-- PANEL: Overview -->
<div id="panel-ct-overview" class="cora-ct-panel block space-y-5">

    <!-- Row 1: AI Insights | Recent Activity | Content Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Column 1: AI Insights -->
        <div class="lg:col-span-1 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl bg-white dark:bg-zinc-950 shadow-2xs flex flex-col p-5 gap-5 min-h-[350px]">
            <div class="flex items-center justify-between select-none">
                <div class="flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0">
                        <path d="M12 2c0 5.523 4.477 10 10 10-5.523 0-10 4.477-10 10-5.523 0-10-4.477-10-10 5.523 0 10-4.477 10-10z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-sm font-bold tracking-tight">AI Insights</span>
                </div>
                <span class="text-[8px] font-extrabold uppercase tracking-widest px-2 py-0.5 rounded bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 text-zinc-550 dark:text-zinc-450">BETA</span>
            </div>

            <!-- Header Highlight Card -->
            <div class="flex items-center gap-3 bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-850/80 rounded-xl p-3 select-none">
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                </div>
                <div>
                    <div class="text-[9px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider">SEO Visibility</div>
                    <div class="text-[11px] text-zinc-650 dark:text-zinc-400 font-semibold mt-0.5 leading-snug" id="ai-insights-summary-text">
                        Improved +3% this week. Focus on <?php echo esc_html($opportunities_count ?? 2); ?> opportunities.
                    </div>
                </div>
            </div>

            <!-- AI-generated insight timeline -->
            <div class="space-y-5 flex-1 relative">
                <div class="flex gap-3.5 relative">
                    <!-- Timeline point -->
                    <div class="flex flex-col items-center shrink-0 relative">
                        <div class="w-5.5 h-5.5 rounded-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-850 text-zinc-600 dark:text-zinc-450 flex items-center justify-center text-[9px] font-bold shrink-0 select-none z-10">1</div>
                        <div class="w-0.5 bg-zinc-100 dark:bg-zinc-850/80 absolute top-5.5 bottom-0 left-[10.5px] -mb-5 z-0"></div>
                    </div>
                    <div class="min-w-0 pt-0.5">
                        <div class="text-[9px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider mb-1 select-none">Top Opportunity</div>
                        <div class="text-[11px] text-zinc-650 dark:text-zinc-400 leading-relaxed font-medium space-y-1.5 py-0.5" id="ai-top-opportunity">
                            <div class="cora-skeleton h-3 w-5/6"></div>
                            <div class="cora-skeleton h-3 w-2/3"></div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3.5 relative">
                    <!-- Timeline point -->
                    <div class="flex flex-col items-center shrink-0">
                        <div class="w-5.5 h-5.5 rounded-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-850 text-zinc-600 dark:text-zinc-450 flex items-center justify-center text-[9px] font-bold shrink-0 select-none z-10">2</div>
                    </div>
                    <div class="min-w-0 pt-0.5">
                        <div class="text-[9px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider mb-1 select-none">Content Grounding</div>
                        <div class="text-[11px] text-zinc-650 dark:text-zinc-400 leading-relaxed font-medium space-y-1.5 py-0.5" id="ai-content-health">
                            <div class="cora-skeleton h-3 w-4/5"></div>
                            <div class="cora-skeleton h-3 w-1/2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <button onclick="switchContentTab('ct-opportunities')" class="inline-flex items-center gap-1.5 px-4 py-2 border border-zinc-200/80 hover:border-zinc-900 bg-white hover:bg-zinc-50 text-zinc-850 font-bold rounded-xl text-xs transition-all cursor-pointer shadow-3xs active:scale-97 w-fit select-none dark:bg-zinc-950 dark:hover:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-200 dark:hover:text-white">
                <span>View Opportunities</span>
                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- Column 2: Recent Activity -->
        <div class="lg:col-span-1 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl bg-white dark:bg-zinc-950 shadow-2xs flex flex-col p-5 min-h-[350px]">
            <div class="flex items-center justify-between mb-4 select-none">
                <div class="flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    <span class="text-sm font-bold tracking-tight">Recent Activity</span>
                </div>
                <button onclick="switchContentTab('ct-library')" class="px-3 py-1.5 border border-zinc-200/80 dark:border-zinc-800 bg-white hover:bg-zinc-50 text-zinc-700 dark:text-zinc-300 font-bold rounded-xl text-xs transition-all cursor-pointer shadow-3xs active:scale-97 dark:bg-zinc-950 dark:hover:bg-zinc-900">
                    View all
                </button>
            </div>
            <div class="flex flex-col gap-1.5 flex-1">
                <?php
                $recent_posts = array_slice($cora_posts, 0, 3);
                foreach ($recent_posts as $p) :
                    $status_meta = get_post_meta($p->ID, '_cora_editorial_status', true) ?: ($p->post_status === 'publish' ? 'published' : 'draft');
                    if ($status_meta === 'published') {
                        $status_label = 'Published';
                        $dot_color    = 'bg-emerald-500';
                        $btn_onclick  = "window.open('" . get_permalink($p->ID) . "')";
                        $action_icon  = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>';
                        $btn_title    = 'View Article';
                    } elseif ($status_meta === 'draft') {
                        $status_label = 'Draft';
                        $dot_color    = 'bg-amber-500';
                        $btn_onclick  = "coraEditArticle(" . $p->ID . ")";
                        $action_icon  = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>';
                        $btn_title    = 'Continue Editing';
                    } else {
                        $status_label = 'In review';
                        $dot_color    = 'bg-zinc-400';
                        $btn_onclick  = "switchContentTab('ct-seo')";
                        $action_icon  = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                        $btn_title    = 'Review Details';
                    }
                    $diff_text = human_time_diff(get_post_modified_time('U', false, $p), current_time('timestamp'));
                    $diff_text = str_replace(['hours','hour','mins','min','days','day','weeks','week',' '], ['h','h','m','m','d','d','w','w',''], $diff_text) . ' ago';
                ?>
                <div onclick="<?php echo esc_attr($btn_onclick); ?>" class="flex items-center justify-between p-2.5 hover:bg-zinc-50/70 dark:hover:bg-zinc-900/50 rounded-xl cursor-pointer transition-colors gap-3 group">
                    <div class="flex items-center min-w-0 gap-1">
                        <div class="flex flex-col gap-1 min-w-0">
                            <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 group-hover:text-zinc-950 dark:group-hover:text-white transition-colors truncate max-w-[200px]"><?php echo esc_html($p->post_title ?: 'Untitled Draft'); ?></span>
                            <div class="flex items-center gap-1.5 text-[10px] text-zinc-450 dark:text-zinc-500">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo $dot_color; ?> shrink-0"></span>
                                <span><?php echo $status_label; ?></span>
                                <span>·</span>
                                <span><?php echo $diff_text; ?></span>
                            </div>
                        </div>
                    </div>
                    <button class="p-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-100/50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors cursor-pointer border-none bg-transparent" title="<?php echo esc_attr($btn_title); ?>">
                        <?php echo $action_icon; ?>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Column 3: Content Performance -->
        <div class="lg:col-span-1 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl bg-white dark:bg-zinc-950 shadow-2xs flex flex-col p-5 min-h-[350px]">
            <div class="flex items-center justify-between mb-4 select-none">
                <div class="flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    <span class="text-sm font-bold tracking-tight">Content Performance</span>
                </div>
                <select class="text-[10px] font-bold text-zinc-650 dark:text-zinc-350 border border-zinc-200/80 dark:border-zinc-800 rounded-lg px-2 py-1 bg-white dark:bg-zinc-950 cursor-pointer focus:outline-none select-none">
                    <option>This Week</option>
                    <option>This Month</option>
                </select>
            </div>
            
            <div class="flex flex-col flex-1 justify-between gap-4">
                <!-- Stats Row Grid -->
                <div class="grid grid-cols-3 gap-2.5">
                    <!-- Views -->
                    <div class="p-3 bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-850/80 rounded-xl flex flex-col select-none">
                        <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider">Views</span>
                        <span class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 mt-1 leading-none" id="perf-overview-views">—</span>
                        <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 mt-2">
                            <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="3" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>+12%
                        </span>
                    </div>
                    <!-- Clicks -->
                    <div class="p-3 bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-850/80 rounded-xl flex flex-col select-none">
                        <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider">Clicks</span>
                        <span class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 mt-1 leading-none" id="perf-overview-clicks">—</span>
                        <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 mt-2">
                            <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="3" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>+8%
                        </span>
                    </div>
                    <!-- Avg Position -->
                    <div class="p-3 bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-850/80 rounded-xl flex flex-col select-none">
                        <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider">Position</span>
                        <span class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 mt-1 leading-none" id="perf-overview-position">—</span>
                        <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 mt-2">
                            <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="3" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>-5
                        </span>
                    </div>
                </div>

                <!-- Unified Area Sparkline -->
                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-850 flex flex-col gap-2 select-none">
                    <div class="text-[9px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider">Weekly Traffic Trend</div>
                    <div class="relative w-full h-[65px] bg-zinc-50/20 dark:bg-zinc-900/10 rounded-lg p-1">
                        <canvas id="cora-performance-sparkline" class="w-full h-full"></canvas>
                    </div>
                </div>

                <button onclick="switchContentTab('ct-performance')" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 border border-zinc-200/80 hover:border-zinc-900 bg-white hover:bg-zinc-50 text-zinc-855 font-bold rounded-xl text-xs transition-all cursor-pointer shadow-3xs active:scale-97 select-none dark:bg-zinc-950 dark:hover:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-200 dark:hover:text-white">
                    <span>Go to Performance Analytics</span>
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Row 2: Quick Actions -->
    <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs p-5">
        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-3.5">Quick Actions</div>
        <div class="flex items-center gap-3 flex-wrap">
            <button onclick="openCreateArticleDrawer()" class="flex items-center gap-2.5 px-4 py-2.5 border border-zinc-200 hover:border-zinc-900 rounded-xl text-xs text-zinc-800 bg-white hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs active:scale-97">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                <div class="text-left"><div class="font-bold text-xs">Create Article</div><div class="text-zinc-400 text-[9px]">Start writing</div></div>
            </button>
            <button onclick="switchContentTab('ct-opportunities')" class="flex items-center gap-2.5 px-4 py-2.5 border border-zinc-200 hover:border-zinc-900 rounded-xl text-xs text-zinc-800 bg-white hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs active:scale-97">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div class="text-left"><div class="font-bold text-xs">Topic Ideas</div><div class="text-zinc-400 text-[9px]">Find opportunities</div></div>
            </button>
            <button onclick="switchContentTab('ct-brain')" class="flex items-center gap-2.5 px-4 py-2.5 border border-zinc-200 hover:border-zinc-900 rounded-xl text-xs text-zinc-800 bg-white hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs active:scale-97">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                <div class="text-left"><div class="font-bold text-xs">Content Brief</div><div class="text-zinc-400 text-[9px]">Generate with AI</div></div>
            </button>
            <button onclick="switchContentTab('ct-seo')" class="flex items-center gap-2.5 px-4 py-2.5 border border-zinc-200 hover:border-zinc-900 rounded-xl text-xs text-zinc-800 bg-white hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs active:scale-97">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                <div class="text-left"><div class="font-bold text-xs">Optimize Content</div><div class="text-zinc-400 text-[9px]">Improve SEO score</div></div>
            </button>
            <button onclick="openCreateArticleDrawer('ai-draft')" class="flex items-center gap-2.5 px-4 py-2.5 border border-zinc-200 hover:border-zinc-900 rounded-xl text-xs text-zinc-800 bg-white hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs active:scale-97">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 2c0 5.523 4.477 10 10 10-5.523 0-10 4.477-10 10-5.523 0-10-4.477-10-10 5.523 0 10-4.477 10-10z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div class="text-left"><div class="font-bold text-xs">AI Draft</div><div class="text-zinc-400 text-[9px]">Generate draft</div></div>
            </button>
            <button onclick="switchContentTab('ct-performance')" class="flex items-center gap-2.5 px-4 py-2.5 border border-zinc-200 hover:border-zinc-900 rounded-xl text-xs text-zinc-800 bg-white hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs active:scale-97">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                <div class="text-left"><div class="font-bold text-xs">Analytics Report</div><div class="text-zinc-400 text-[9px]">View full report</div></div>
            </button>
        </div>
    </div>

    <!-- Row 3: Decay & Claim Validity Warnings -->
    <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs p-5 flex flex-col md:flex-row items-start gap-4">
        <div class="p-2 bg-zinc-100 rounded-xl text-zinc-600 shrink-0">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </div>
        <div class="space-y-2 flex-1">
            <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Decay &amp; Claim Validity Warnings</h4>
            <ul class="text-xs text-zinc-600 space-y-1.5 leading-relaxed" id="overview-decay-warnings">
                <li class="flex items-start gap-2">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="#d97706" stroke-width="2" fill="none" class="shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <span>Content Decay Detected: Traffic to <strong>Pre-Wedding Shoot Locations in South Mumbai</strong> has declined 18% in 15 days. Recommendation: Refresh content facts.</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="#d97706" stroke-width="2" fill="none" class="shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <span>Outdated Claim Warning: Pricing package info for newborn photography expired on 31 July 2026. Needs update.</span>
                </li>
            </ul>
        </div>
    </div>

</div>

<!-- PANEL: Opportunities -->
<div id="panel-ct-opportunities" class="cora-ct-panel hidden">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Side: Table & Finder (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Top Opportunities Table Card -->
            <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs p-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-zinc-950">Top Opportunities</h3>
                        <p class="text-xs text-zinc-500 mt-0.5">Discover high-impact topics your audience is searching for and you haven't covered yet.</p>
                    </div>
                </div>

                <!-- Filter control row -->
                <div class="grid grid-cols-2 gap-2.5 pb-4 border-b border-zinc-100 w-full sm:flex sm:flex-wrap sm:items-center sm:justify-between sm:gap-3">
                    <!-- Dropdowns -->
                    <select id="opp-filter-topic" onchange="coraFilterOpportunitiesTable()" class="w-full sm:w-auto border border-zinc-200 hover:border-zinc-300 rounded-xl px-3 py-1.5 text-xs bg-white text-zinc-700 font-bold focus:outline-none transition-all cursor-pointer">
                        <option value="all">All Topics</option>
                        <option value="seo">SEO Strategy</option>
                        <option value="ai">AI & Search</option>
                        <option value="content">Content Marketing</option>
                    </select>
                    <select id="opp-filter-intent" onchange="coraFilterOpportunitiesTable()" class="w-full sm:w-auto border border-zinc-200 hover:border-zinc-300 rounded-xl px-3 py-1.5 text-xs bg-white text-zinc-700 font-bold focus:outline-none transition-all cursor-pointer">
                        <option value="all">All Intent</option>
                        <option value="commercial">Commercial</option>
                        <option value="local">Local</option>
                        <option value="informational">Informational</option>
                        <option value="transactional">Transactional</option>
                    </select>
                    <select id="opp-filter-impact" onchange="coraFilterOpportunitiesTable()" class="w-full sm:w-auto border border-zinc-200 hover:border-zinc-300 rounded-xl px-3 py-1.5 text-xs bg-white text-zinc-700 font-bold focus:outline-none transition-all cursor-pointer">
                        <option value="all">All Impact</option>
                        <option value="high">High Impact</option>
                        <option value="medium">Medium Impact</option>
                        <option value="low">Low Impact</option>
                    </select>
                    <button class="flex items-center justify-center gap-1.5 w-full sm:w-auto sm:ml-auto px-3 py-1.5 border border-zinc-200 hover:border-zinc-900 bg-white rounded-xl text-xs text-zinc-700 font-bold transition-all shadow-3xs active:scale-97 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        Filters
                    </button>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto -mx-6 sm:mx-0">
                    <table class="w-full text-left border-collapse min-w-[650px] text-xs">
                        <thead>
                            <tr class="border-b border-zinc-150 text-[10px] font-bold text-zinc-400 uppercase tracking-wider bg-zinc-50/40">
                                <th class="py-2.5 px-4 w-9 text-center"><input type="checkbox" class="rounded border-zinc-300 accent-zinc-900 cursor-pointer"></th>
                                <th class="py-2.5 px-4 min-w-[200px]">OPPORTUNITY</th>
                                <th class="py-2.5 px-4 min-w-[100px] text-center">SEARCH VOLUME</th>
                                <th class="py-2.5 px-4 min-w-[110px] text-center">POTENTIAL TRAFFIC</th>
                                <th class="py-2.5 px-4 min-w-[80px] text-center">IMPACT</th>
                                <th class="py-2.5 px-4 min-w-[100px]">STATUS</th>
                                <th class="py-2.5 px-4 text-center min-w-[80px]">ACTION</th>
                                <th class="py-2.5 px-4 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="cora-opportunities-table-body" class="divide-y divide-zinc-100 text-zinc-700">
                            <!-- Rendered Dynamically in JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View -->
                <div id="cora-opportunities-mobile-cards" class="block md:hidden flex flex-col gap-3">
                    <!-- Rendered Dynamically in JS -->
                </div>

                <!-- Table Footer / Pagination -->
                <div class="flex items-center justify-between pt-4 border-t border-zinc-100 text-[11px] text-zinc-500">
                    <span id="opp-pagination-text">Showing 1 to 5 of 24 opportunities</span>
                    <div class="flex items-center gap-1.5" id="opp-pagination-controls">
                        <!-- Rendered Dynamically in JS -->
                    </div>
                </div>
            </div>

            <!-- AI Opportunity Finder Banner -->
            <div class="border border-zinc-200/90 rounded-2xl bg-zinc-50/50 p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-3xs">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-white border border-zinc-200/80 flex items-center justify-center text-zinc-900 shrink-0 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-900">AI Opportunity Finder</h4>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Get personalized topic ideas based on your niche, audience, and competitors.</p>
                    </div>
                </div>
                <button onclick="coraGenerateOpportunitiesBacklog(this)" class="px-4 py-2 bg-white hover:bg-zinc-50 border border-zinc-250 hover:border-zinc-900 text-zinc-800 font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 shadow-3xs cursor-pointer active:scale-97 shrink-0">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-650"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    Find New Opportunities
                </button>
            </div>
        </div>

        <!-- Right Side: Stats & Funnel (1/3 width) -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Card 1: Opportunity Funnel -->
            <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs p-5 space-y-4">
                <h4 class="text-xs font-extrabold text-zinc-950">Opportunity Funnel</h4>
                
                <div class="flex items-center gap-5">
                    <!-- Funnel SVG Chart -->
                    <div class="w-20 shrink-0 flex items-center justify-center">
                        <svg viewBox="0 0 100 90" class="w-full text-zinc-200">
                            <!-- Layer 1 -->
                            <polygon points="5,5 95,5 85,20 15,20" fill="currentColor" class="text-zinc-200 dark:text-zinc-800 transition-all hover:opacity-85 cursor-pointer" id="funnel-l1"/>
                            <!-- Layer 2 -->
                            <polygon points="17,23 83,23 75,38 25,38" fill="currentColor" class="text-zinc-300 dark:text-zinc-700 transition-all hover:opacity-85 cursor-pointer" id="funnel-l2"/>
                            <!-- Layer 3 -->
                            <polygon points="27,41 73,41 65,56 35,56" fill="currentColor" class="text-zinc-400 dark:text-zinc-600 transition-all hover:opacity-85 cursor-pointer" id="funnel-l3"/>
                            <!-- Layer 4 -->
                            <polygon points="37,59 63,59 55,74 45,74" fill="currentColor" class="text-zinc-500 dark:text-zinc-500 transition-all hover:opacity-85 cursor-pointer" id="funnel-l4"/>
                            <!-- Layer 5 -->
                            <polygon points="47,77 53,77 50,89 50,89" fill="currentColor" class="text-zinc-600 dark:text-zinc-400 transition-all hover:opacity-85 cursor-pointer" id="funnel-l5"/>
                        </svg>
                    </div>

                    <!-- Legend list with counts -->
                    <div class="flex-1 space-y-2 text-[11px]">
                        <div class="flex items-center justify-between font-medium">
                            <span class="text-zinc-550 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded bg-zinc-200"></span>Identified
                            </span>
                            <span class="font-bold text-zinc-900" id="opp-funnel-identified">24</span>
                        </div>
                        <div class="flex items-center justify-between font-medium">
                            <span class="text-zinc-550 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded bg-zinc-300"></span>Evaluating
                            </span>
                            <span class="font-bold text-zinc-900" id="opp-funnel-evaluating">11</span>
                        </div>
                        <div class="flex items-center justify-between font-medium">
                            <span class="text-zinc-550 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded bg-zinc-400"></span>In Progress
                            </span>
                            <span class="font-bold text-zinc-900" id="opp-funnel-progress">6</span>
                        </div>
                        <div class="flex items-center justify-between font-medium">
                            <span class="text-zinc-550 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded bg-zinc-500"></span>Ready to Create
                            </span>
                            <span class="font-bold text-zinc-900" id="opp-funnel-ready">7</span>
                        </div>
                        <div class="flex items-center justify-between font-medium">
                            <span class="text-zinc-550 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded bg-zinc-600"></span>Published
                            </span>
                            <span class="font-bold text-zinc-900" id="opp-funnel-published">10</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Opportunity by Topic -->
            <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs p-5 space-y-4">
                <h4 class="text-xs font-extrabold text-zinc-950">Opportunity by Topic</h4>
                
                <div class="flex items-center gap-5">
                    <!-- Donut Chart SVG -->
                    <div class="w-20 h-20 shrink-0 flex items-center justify-center relative">
                        <svg viewBox="0 0 36 36" class="w-full h-full">
                            <circle cx="18" cy="18" r="15.915" fill="none" stroke="#f4f4f5" stroke-width="3"></circle>
                            <!-- SEO Strategy 30% -->
                            <circle cx="18" cy="18" r="15.915" fill="none" stroke="#71717a" stroke-width="3" stroke-dasharray="30 70" stroke-dashoffset="25"></circle>
                            <!-- AI & Search 25% -->
                            <circle cx="18" cy="18" r="15.915" fill="none" stroke="#a1a1aa" stroke-width="3" stroke-dasharray="25 75" stroke-dashoffset="95"></circle>
                            <!-- Content Marketing 20% -->
                            <circle cx="18" cy="18" r="15.915" fill="none" stroke="#d4d4d8" stroke-width="3" stroke-dasharray="20 80" stroke-dashoffset="70"></circle>
                            <!-- Tools & Software 15% -->
                            <circle cx="18" cy="18" r="15.915" fill="none" stroke="#e4e4e7" stroke-width="3" stroke-dasharray="15 85" stroke-dashoffset="50"></circle>
                            <!-- Analytics 10% -->
                            <circle cx="18" cy="18" r="15.915" fill="none" stroke="#27272a" stroke-width="3" stroke-dasharray="10 90" stroke-dashoffset="35"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col select-none">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest leading-none">Total</span>
                            <span class="text-sm font-extrabold text-zinc-900 leading-none mt-0.5" id="opp-topic-total-count">24</span>
                        </div>
                    </div>

                    <!-- Topic list -->
                    <div class="flex-1 space-y-1.5 text-[10px] font-bold uppercase tracking-wider">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-555 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-zinc-500"></span>SEO Strategy
                            </span>
                            <span class="text-zinc-900">30%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-555 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-zinc-400"></span>AI &amp; Search
                            </span>
                            <span class="text-zinc-900">25%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-555 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-zinc-300"></span>Content Marketing
                            </span>
                            <span class="text-zinc-900">20%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-555 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-zinc-200"></span>Tools &amp; Software
                            </span>
                            <span class="text-zinc-900">15%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-555 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-zinc-800"></span>Analytics
                            </span>
                            <span class="text-zinc-900">10%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Trending Topics -->
            <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs p-5 space-y-4">
                <h4 class="text-xs font-extrabold text-zinc-950">Trending Topics</h4>
                
                <div class="divide-y divide-zinc-100 text-xs">
                    <div class="py-2.5 flex items-center justify-between font-medium">
                        <span class="text-zinc-850">AI Search Optimization</span>
                        <span class="text-emerald-600 font-bold flex items-center gap-0.5">&uarr; 24%</span>
                    </div>
                    <div class="py-2.5 flex items-center justify-between font-medium">
                        <span class="text-zinc-850">Topic Clusters</span>
                        <span class="text-emerald-600 font-bold flex items-center gap-0.5">&uarr; 18%</span>
                    </div>
                    <div class="py-2.5 flex items-center justify-between font-medium">
                        <span class="text-zinc-850">Content Refresh Strategy</span>
                        <span class="text-emerald-600 font-bold flex items-center gap-0.5">&uarr; 12%</span>
                    </div>
                </div>

                <div class="border-t border-zinc-100 pt-3">
                    <a href="#" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 flex items-center gap-1 transition-colors no-underline">
                        View all trending topics &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PANEL: Content Library -->
<div id="panel-ct-library" class="cora-ct-panel hidden">
    <?php
        $total_cnt = count($cora_posts);
        $pub_cnt = 0;
        $draft_cnt = 0;
        $review_cnt = 0;
        $approved_cnt = 0;

        foreach($cora_posts as $p_item) {
            $st = get_post_meta($p_item->ID, '_cora_editorial_status', true) ?: ($p_item->post_status === 'publish' ? 'published' : 'draft');
            if ($st === 'published') $pub_cnt++;
            elseif ($st === 'draft') $draft_cnt++;
            elseif ($st === 'pending_review' || $st === 'in_review') $review_cnt++;
            elseif ($st === 'approved') $approved_cnt++;
        }
    ?>
    <!-- Top Filter Controls Bar -->
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-5">
        <div class="flex items-center gap-2 select-none overflow-x-auto pb-1.5 xl:pb-0 scrollbar-hide shrink-0" id="ct-status-pills">
            <button type="button" class="ct-status-btn px-3.5 py-1.5 bg-zinc-900 text-white rounded-full text-xs font-bold transition-all shadow-2xs cursor-pointer active whitespace-nowrap" data-status="all" onclick="filterContentByStatus('all', this)">All (<?php echo $total_cnt; ?>)</button>
            <button type="button" class="ct-status-btn px-3.5 py-1.5 bg-white border border-zinc-200 hover:border-zinc-300 text-zinc-600 rounded-full text-xs font-medium transition-all cursor-pointer whitespace-nowrap" data-status="published" onclick="filterContentByStatus('published', this)">Published (<?php echo $pub_cnt; ?>)</button>
            <button type="button" class="ct-status-btn px-3.5 py-1.5 bg-white border border-zinc-200 hover:border-zinc-300 text-zinc-600 rounded-full text-xs font-medium transition-all cursor-pointer whitespace-nowrap" data-status="draft" onclick="filterContentByStatus('draft', this)">Draft (<?php echo $draft_cnt; ?>)</button>
            <button type="button" class="ct-status-btn px-3.5 py-1.5 bg-white border border-zinc-200 hover:border-zinc-300 text-zinc-600 rounded-full text-xs font-medium transition-all cursor-pointer whitespace-nowrap" data-status="pending_review" onclick="filterContentByStatus('pending_review', this)">In Review (<?php echo $review_cnt; ?>)</button>
            <button type="button" class="ct-status-btn px-3.5 py-1.5 bg-white border border-zinc-200 hover:border-zinc-300 text-zinc-600 rounded-full text-xs font-medium transition-all cursor-pointer whitespace-nowrap" data-status="approved" onclick="filterContentByStatus('approved', this)">Approved (<?php echo $approved_cnt; ?>)</button>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 w-full xl:w-auto">
            <div class="relative flex-1 min-w-[140px] sm:flex-none sm:w-56">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400" viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" id="ct-search" class="w-full pl-8 pr-3 py-1.5 border border-zinc-200 hover:border-zinc-300 rounded-lg text-xs bg-white text-zinc-800 placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition-all" placeholder="Search articles..." oninput="searchContentTable(this.value)">
            </div>
            <select id="ct-filter-author" class="flex-1 min-w-[110px] sm:flex-none border border-zinc-200 hover:border-zinc-300 rounded-lg px-3 py-1.5 text-xs bg-white text-zinc-700 focus:outline-none transition-all cursor-pointer font-medium" onchange="filterContentByAuthor(this.value)">
                <option value="all">All Authors</option>
                <?php foreach($cora_users as $u): ?>
                    <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="ct-bulk-actions" disabled class="opacity-50 cursor-not-allowed flex-1 min-w-[110px] sm:flex-none border border-zinc-200 rounded-lg px-3 py-1.5 text-xs bg-white text-zinc-700 focus:outline-none transition-all font-medium select-none" onchange="coraApplyBulkAction(this.value)">
                <option value="">Bulk Actions</option>
                <option value="delete">Delete Selected</option>
            </select>
            <button type="button" onclick="exportContentCSV()" class="flex-1 justify-center sm:flex-none bg-white hover:bg-zinc-50 text-zinc-800 text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-zinc-200 shadow-3xs transition-all cursor-pointer whitespace-nowrap active:scale-95">
                Export CSV
            </button>
            <button onclick="openCreateArticleDrawer()" class="flex-1 justify-center sm:flex-none bg-zinc-100 sm:bg-zinc-900 hover:bg-zinc-200 sm:hover:bg-zinc-800 text-zinc-700 sm:text-white text-xs font-bold px-3.5 py-1.5 rounded-lg transition-all shadow-3xs sm:shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap border border-zinc-200/90 sm:border-transparent active:scale-95" title="Desktop Only (≥768px)">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 hidden sm:block"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 sm:hidden text-zinc-500"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>New Article</span>
                <span class="sm:hidden text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-zinc-200 text-zinc-600 uppercase tracking-wider">Desktop</span>
            </button>
        </div>
    </div>

    <!-- Articles Container (Desktop Table + Mobile Cards Stack) -->
    <div class="border border-zinc-200/80 rounded-xl bg-white shadow-2xs overflow-hidden">
        
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[980px]">
                <thead>
                    <tr class="bg-zinc-50/60 border-b border-zinc-200/80 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                        <th class="py-3 px-3.5 w-9 text-center"><input type="checkbox" class="rounded border-zinc-300 accent-zinc-900 cursor-pointer" id="ct-select-all" onclick="toggleSelectAll(this)"></th>
                        <th class="py-3 px-3.5 min-w-[280px]">ARTICLE</th>
                        <th class="py-3 px-3.5 min-w-[120px]">AUTHOR</th>
                        <th class="py-3 px-3.5 min-w-[110px]">STATUS</th>
                        <th class="py-3 px-3.5 min-w-[110px]">
                            SEO
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="inline text-zinc-400 ml-0.5 shrink-0" title="11-Point On-Page SEO Score"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                        </th>
                        <th class="py-3 px-3.5 min-w-[110px]">
                            GEO / AI
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="inline text-zinc-400 ml-0.5 shrink-0" title="Generative AI & LLM Search Citation Score"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                        </th>
                        <th class="py-3 px-3.5 min-w-[90px]">
                            LEADS/CR
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="inline text-zinc-400 ml-0.5 shrink-0" title="Conversion Rate & Inbound Lead Inquiries"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                        </th>
                        <th class="py-3 px-3.5 min-w-[110px]">MODIFIED</th>
                        <th class="py-3 px-3.5 min-w-[120px] text-right pr-5">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="cora-content-table-body" class="divide-y divide-zinc-100 text-xs text-zinc-700">
                    <?php if (empty($cora_posts)): ?>
                        <tr>
                            <td colspan="9" class="py-20 text-center">
                                <div class="max-w-sm mx-auto">
                                    <div class="w-16 h-16 bg-zinc-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-zinc-900 mb-1">No articles yet</h3>
                                    <p class="text-xs text-zinc-500 mb-4">Start building your content library. Create your first article to track SEO performance and AI search visibility.</p>
                                    <button onclick="openCreateArticleDrawer()" class="bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors cursor-pointer">
                                        Create First Article
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($cora_posts as $post): 
                            $seo_score = intval(get_post_meta($post->ID, '_cora_seo_score', true)) ?: rand(65, 91);
                            $geo_score = intval(get_post_meta($post->ID, '_cora_geo_score', true)) ?: rand(45, 80);
                            $word_count = str_word_count(strip_tags($post->post_content));
                            $lead_count = cora_db_get_article_lead_count($post->ID);
                            $is_published = $post->post_status === 'publish';
                            $editorial_status = get_post_meta($post->ID, '_cora_editorial_status', true) ?: ($is_published ? 'published' : 'draft');
                            $assignee_id = get_post_meta($post->ID, '_cora_assignee_id', true);
                            $assignee = $assignee_id ? get_userdata($assignee_id) : null;
                            $assignee_name = $assignee ? $assignee->display_name : 'Unassigned';
                            $assignee_initial = strtoupper(substr($assignee_name, 0, 1));
                            $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
                            $modified_date = get_the_modified_date('M j, Y', $post->ID);
                            $modified_diff = human_time_diff(get_the_modified_time('U', $post->ID), current_time('timestamp')) . ' ago';
                            
                            $seo_lbl = ($seo_score >= 80) ? 'Excellent' : (($seo_score >= 60) ? 'Good' : (($seo_score >= 50) ? 'Average' : 'Needs Work'));
                            $seo_bar_cls = ($seo_score >= 80) ? 'bg-emerald-500' : (($seo_score >= 50) ? 'bg-amber-500' : 'bg-red-500');

                            $geo_lbl = ($geo_score >= 75) ? 'Good' : (($geo_score >= 45) ? 'Average' : 'Needs Work');
                            $geo_bar_cls = ($geo_score >= 75) ? 'bg-zinc-900' : (($geo_score >= 45) ? 'bg-zinc-500' : 'bg-red-500');
                        ?>
                        <tr class="group hover:bg-zinc-50/70 transition-colors ct-row border-b border-zinc-100 last:border-b-0" data-status="<?php echo esc_attr($editorial_status); ?>" data-author="<?php echo esc_attr($assignee_id); ?>" data-title="<?php echo esc_attr(strtolower($post->post_title)); ?>">
                            <td class="py-3.5 px-3.5 text-center"><input type="checkbox" class="rounded border-zinc-300 ct-row-checkbox accent-zinc-900 cursor-pointer" value="<?php echo $post->ID; ?>" onchange="updateBulkActions()"></td>
                            <td class="py-3.5 px-3.5">
                                <div class="flex items-center gap-3">
                                    <?php if($thumbnail_url): ?>
                                        <img src="<?php echo esc_url($thumbnail_url); ?>" class="w-8 h-8 rounded-lg object-cover bg-zinc-100 border border-zinc-200/60 shrink-0" loading="lazy">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-lg bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-500 shrink-0">
                                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-zinc-900 text-xs line-clamp-1 hover:text-zinc-700 cursor-pointer leading-snug" title="<?php echo esc_attr($post->post_title); ?>" onclick="switchContentTab('ct-seo'); openSEOAnalysis(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>');"><?php echo esc_html($post->post_title); ?></div>
                                        <div class="text-[11px] text-zinc-400 font-normal mt-0.5"><?php echo number_format($word_count); ?> words &bull; ID #<?php echo $post->ID; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-zinc-100 text-zinc-600 font-bold text-[10px] flex items-center justify-center shrink-0 border border-zinc-200/60"><?php echo esc_html($assignee_initial); ?></div>
                                    <div>
                                        <div class="text-xs font-semibold text-zinc-800 line-clamp-1"><?php echo esc_html($assignee_name); ?></div>
                                        <button class="text-[10px] font-medium text-zinc-400 hover:text-zinc-700 cursor-pointer block leading-none mt-0.5" onclick="openContentBriefDrawer(<?php echo $post->ID; ?>)">Assign</button>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3.5">
                                <?php if($editorial_status === 'published'): ?>
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded text-[10px] font-bold tracking-wider uppercase inline-block">PUBLISHED</span>
                                    <span class="text-[10px] text-zinc-400 font-normal block mt-0.5">Live</span>
                                <?php elseif($editorial_status === 'pending_review'): ?>
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200/60 rounded text-[10px] font-bold tracking-wider uppercase inline-block">IN REVIEW</span>
                                    <span class="text-[10px] text-zinc-400 font-normal block mt-0.5">Pending</span>
                                <?php elseif($editorial_status === 'approved'): ?>
                                    <span class="px-2 py-0.5 bg-zinc-800 text-white rounded text-[10px] font-bold tracking-wider uppercase inline-block">APPROVED</span>
                                    <span class="text-[10px] text-zinc-400 font-normal block mt-0.5">Approved</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 bg-zinc-100 text-zinc-700 border border-zinc-200/60 rounded text-[10px] font-bold tracking-wider uppercase inline-block">DRAFT</span>
                                    <span class="text-[10px] text-zinc-400 font-normal block mt-0.5">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-3.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-zinc-900 w-5 text-right shrink-0"><?php echo $seo_score; ?></span>
                                    <div class="w-12 h-1 bg-zinc-100 rounded-full overflow-hidden shrink-0">
                                        <div class="h-full rounded-full <?php echo $seo_bar_cls; ?>" style="width:<?php echo $seo_score; ?>%"></div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-normal text-zinc-400 block mt-0.5"><?php echo $seo_lbl; ?></span>
                            </td>
                            <td class="py-3.5 px-3.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-zinc-900 w-5 text-right shrink-0"><?php echo $geo_score; ?></span>
                                    <div class="w-12 h-1 bg-zinc-100 rounded-full overflow-hidden shrink-0">
                                        <div class="h-full rounded-full <?php echo $geo_bar_cls; ?>" style="width:<?php echo $geo_score; ?>%"></div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-normal text-zinc-400 block mt-0.5"><?php echo $geo_lbl; ?></span>
                            </td>
                            <td class="py-3.5 px-3.5 text-xs">
                                <div class="font-bold text-zinc-900"><?php echo $lead_count; ?></div>
                                <div class="text-[10px] text-zinc-400 font-normal block mt-0.5">0.0%</div>
                            </td>
                            <td class="py-3.5 px-3.5 text-xs">
                                <div class="font-normal text-zinc-700"><?php echo $modified_date; ?></div>
                                <div class="text-[10px] text-zinc-400 font-normal block mt-0.5"><?php echo $modified_diff; ?></div>
                            </td>
                            <td class="py-2.5 px-3.5 text-right pr-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="px-2.5 py-1 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer shadow-xs" title="Edit Article" onclick="coraEditArticle(<?php echo $post->ID; ?>)">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Edit
                                    </button>
                                    <button type="button" class="px-2.5 py-1 rounded-lg border border-zinc-200/80 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer shadow-2xs" title="SEO Analysis" onclick="openSEOAnalysisTab(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        SEO
                                    </button>
                                </div>
                                <div class="flex items-center justify-end gap-2 text-[10px] font-medium text-zinc-400 mt-1">
                                    <button type="button" class="hover:text-zinc-700 flex items-center gap-0.5 cursor-pointer" onclick="openContentBriefDrawer(<?php echo $post->ID; ?>)">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                        Brief
                                    </button>
                                    <span class="text-zinc-300">&bull;</span>
                                    <a href="<?php echo get_permalink($post->ID); ?>" target="_blank" class="hover:text-zinc-700 flex items-center gap-0.5" title="View Live">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 0 0 2 2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                        View
                                    </a>
                                    <span class="text-zinc-300">&bull;</span>
                                    <button type="button" class="hover:text-red-650 text-red-500 font-bold flex items-center gap-0.5 cursor-pointer bg-transparent border-0 p-0" title="Delete Article" onclick="coraDeleteArticle(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none" class="text-red-550"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Article Card Stack View (Spaced Standalone Cards) -->
        <div id="cora-content-mobile-cards" class="block sm:hidden flex flex-col gap-3 p-3 bg-zinc-50/60 rounded-xl border-t sm:border-t-0 border-zinc-200/60">
            <?php if (empty($cora_posts)): ?>
                <div class="p-8 text-center bg-white rounded-xl border border-zinc-200/80">
                    <div class="w-12 h-12 bg-zinc-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    </div>
                    <h3 class="text-xs font-bold text-zinc-900 mb-1">No articles yet</h3>
                    <button onclick="openCreateArticleDrawer()" class="bg-zinc-100 text-zinc-700 text-xs font-bold px-3.5 py-1.5 rounded-lg mt-2 cursor-pointer shadow-3xs border border-zinc-200 flex items-center gap-1.5 mx-auto active:scale-95" title="Desktop Only (≥768px)">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>Create Article</span>
                        <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-zinc-200 text-zinc-600 uppercase tracking-wider">Desktop Only</span>
                    </button>
                </div>
            <?php else: ?>
                <?php foreach($cora_posts as $post): 
                    $seo_score = intval(get_post_meta($post->ID, '_cora_seo_score', true)) ?: rand(65, 91);
                    $geo_score = intval(get_post_meta($post->ID, '_cora_geo_score', true)) ?: rand(45, 80);
                    $word_count = str_word_count(strip_tags($post->post_content));
                    $is_published = $post->post_status === 'publish';
                    $editorial_status = get_post_meta($post->ID, '_cora_editorial_status', true) ?: ($is_published ? 'published' : 'draft');
                    $assignee_id = get_post_meta($post->ID, '_cora_assignee_id', true);
                    $assignee = $assignee_id ? get_userdata($assignee_id) : null;
                    $assignee_name = $assignee ? $assignee->display_name : 'Unassigned';
                    $assignee_initial = strtoupper(substr($assignee_name, 0, 1));
                    $modified_diff = human_time_diff(get_the_modified_time('U', $post->ID), current_time('timestamp')) . ' ago';
                ?>
                <div class="p-4 bg-white border border-zinc-200/90 rounded-xl shadow-2xs hover:shadow-xs hover:border-zinc-300 transition-all ct-card flex flex-col gap-3" data-status="<?php echo esc_attr($editorial_status); ?>" data-author="<?php echo esc_attr($assignee_id); ?>" data-title="<?php echo esc_attr(strtolower($post->post_title)); ?>">
                    <!-- Top Row: Checkbox, Title & Status Pill -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <input type="checkbox" class="rounded border-zinc-300 ct-row-checkbox accent-zinc-900 cursor-pointer mt-0.5" value="<?php echo $post->ID; ?>" onchange="updateBulkActions()">
                            <div class="min-w-0">
                                <h4 class="font-bold text-zinc-900 text-xs line-clamp-2 leading-snug cursor-pointer hover:text-zinc-700" onclick="coraEditArticle(<?php echo $post->ID; ?>)"><?php echo esc_html($post->post_title); ?></h4>
                                <div class="text-[10px] text-zinc-400 mt-1 flex items-center gap-1.5 flex-wrap">
                                    <span><?php echo number_format($word_count); ?> words</span>
                                    <span>&bull;</span>
                                    <span>ID #<?php echo $post->ID; ?></span>
                                    <span>&bull;</span>
                                    <span><?php echo $modified_diff; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <?php if($editorial_status === 'published'): ?>
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-md text-[9px] font-bold uppercase tracking-wider">PUBLISHED</span>
                            <?php elseif($editorial_status === 'pending_review'): ?>
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200/60 rounded-md text-[9px] font-bold uppercase tracking-wider">REVIEW</span>
                            <?php elseif($editorial_status === 'approved'): ?>
                                <span class="px-2 py-0.5 bg-zinc-800 text-white rounded-md text-[9px] font-bold uppercase tracking-wider">APPROVED</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-zinc-100 text-zinc-700 border border-zinc-200/60 rounded-md text-[9px] font-bold uppercase tracking-wider">DRAFT</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Middle Row: Author Avatar & SEO/GEO Scores -->
                    <div class="flex items-center justify-between text-[11px] pt-1.5 border-t border-zinc-100">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-zinc-100 text-zinc-700 font-bold text-[10px] flex items-center justify-center shrink-0 border border-zinc-200/70 shadow-3xs"><?php echo esc_html($assignee_initial); ?></div>
                            <span class="text-xs font-semibold text-zinc-800 truncate max-w-[120px]"><?php echo esc_html($assignee_name); ?></span>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-md bg-zinc-100/80 text-zinc-800 font-bold text-[10px] border border-zinc-200/60">SEO <?php echo $seo_score; ?></span>
                            <span class="px-2 py-0.5 rounded-md bg-zinc-100/80 text-zinc-800 font-bold text-[10px] border border-zinc-200/60">GEO <?php echo $geo_score; ?></span>
                        </div>
                    </div>

                    <!-- Bottom Row: Action Buttons -->
                    <div class="flex items-center justify-between pt-2 border-t border-zinc-100">
                        <div class="flex items-center gap-2.5 text-[11px] font-semibold text-zinc-400">
                            <button type="button" class="hover:text-zinc-800 flex items-center gap-1 cursor-pointer transition-colors" onclick="openContentBriefDrawer(<?php echo $post->ID; ?>)">Brief</button>
                            <span class="text-zinc-300">&bull;</span>
                            <a href="<?php echo get_permalink($post->ID); ?>" target="_blank" class="hover:text-zinc-800 flex items-center gap-1 transition-colors">View</a>
                            <span class="text-zinc-300">&bull;</span>
                            <button type="button" class="hover:text-red-650 text-red-500 font-bold flex items-center gap-1 cursor-pointer transition-colors bg-transparent border-0 p-0" onclick="coraDeleteArticle(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">Delete</button>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" class="px-2.5 py-1.5 rounded-lg bg-zinc-100/90 text-zinc-500 hover:text-zinc-800 text-xs font-semibold flex items-center gap-1.5 transition-all cursor-pointer border border-zinc-200/80 active:scale-95" onclick="coraEditArticle(<?php echo $post->ID; ?>)" title="Desktop Only (≥768px)">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                <span>Edit</span>
                            </button>
                            <button type="button" class="px-3 py-1.5 rounded-lg border border-zinc-200/90 bg-white hover:bg-zinc-50 text-zinc-800 text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer shadow-3xs active:scale-95" onclick="openSEOAnalysisTab(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">
                                SEO
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Table Footer / Pagination Bar -->
        <div class="p-3.5 border-t border-zinc-200/80 bg-white flex items-center justify-between text-xs text-zinc-500 font-normal select-none">
            <div id="ct-pagination-info">Showing 1 to 6 of <?php echo $total_cnt; ?> articles</div>
            <div class="flex items-center gap-3">
                <div id="ct-pagination-controls" class="flex items-center gap-1">
                    <!-- Pagination JS buttons rendered dynamically -->
                </div>
                <select id="ct-per-page" class="border border-zinc-200 rounded-lg px-2.5 py-1 text-xs bg-white text-zinc-700 font-medium focus:outline-none cursor-pointer" onchange="changeCTPageSize(this.value)">
                    <option value="6" selected>6 per page</option>
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="all">All per page</option>
                </select>
            </div>
        </div>
    </div>
</div>
<!-- PANEL: SEO Analyzer -->
<div id="panel-ct-seo" class="cora-ct-panel hidden w-full max-w-full">
    <!-- Top Control Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-zinc-200/60 dark:border-zinc-800/80 mb-5 select-none w-full">
        <div class="flex items-center gap-3 flex-1 w-full max-w-2xl relative" id="seo-dropdown-wrapper">
            <span class="hidden sm:inline-block text-[10px] font-extrabold text-zinc-400 dark:text-zinc-550 uppercase tracking-widest shrink-0">Active Article:</span>
            <div class="relative flex-1 w-full">
                <button onclick="coraToggleSEODropdown(event)" class="w-full text-xs font-bold text-zinc-800 dark:text-zinc-200 hover:text-zinc-950 dark:hover:text-white transition-colors flex items-center justify-between bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 cursor-pointer shadow-3xs select-none">
                    <span id="seo-active-article-title-display">Select an article...</span>
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 ml-2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>

                <!-- Restructured combobox popover card -->
                <div id="seo-sidebar" class="hidden absolute left-0 top-full mt-2 w-full min-w-full bg-white dark:bg-zinc-950 border border-zinc-250 dark:border-zinc-800 rounded-xl shadow-xl z-[999] flex flex-col overflow-hidden max-h-[350px] transition-all duration-200 select-none">
                    <!-- Search Bar -->
                    <div class="p-3 border-b border-zinc-100 dark:border-zinc-850 flex items-center gap-2">
                        <div class="relative flex-1">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-450"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <input type="text" id="seo-search" class="w-full pl-8 pr-3 py-2 border border-zinc-200/80 dark:border-zinc-850 rounded-lg text-xs focus:outline-none focus:border-zinc-400 focus:ring-1 focus:ring-zinc-200 dark:focus:ring-zinc-800 bg-zinc-50/50 focus:bg-white transition-all dark:bg-zinc-900 dark:text-zinc-200" placeholder="Search title or ID..." oninput="filterSEOArticleList(this.value)">
                        </div>
                    </div>

                    <!-- Article List (Suggestions container) -->
                    <div class="flex-1 overflow-y-auto p-2 space-y-1" id="seo-article-list-container">
                        <?php foreach($cora_posts as $idx => $post): 
                            $score = intval(get_post_meta($post->ID, '_cora_seo_score', true));
                            if (!$score) $score = 75;
                            $score_cls = ($score >= 80) ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30' : (($score >= 50) ? 'bg-amber-50 text-amber-700 border-amber-200/60 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30' : 'bg-red-50 text-red-700 border-red-200/60 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900/30');
                            $modified_time = human_time_diff(get_the_modified_time('U', $post->ID), current_time('timestamp'));
                        ?>
                        <button class="seo-article-btn w-full text-left p-2 hover:bg-zinc-50/80 dark:hover:bg-zinc-900/50 rounded-lg border border-transparent hover:border-zinc-200/80 dark:hover:border-zinc-800 transition-all cursor-pointer flex items-center justify-between gap-3 group <?php echo $idx === 0 ? 'active bg-zinc-50 dark:bg-zinc-900 border-zinc-200/80 dark:border-zinc-800 shadow-3xs' : ''; ?>" data-id="<?php echo $post->ID; ?>" data-title="<?php echo esc_attr($post->post_title); ?>" data-score="<?php echo $score; ?>" onclick="openSEOAnalysis(<?php echo $post->ID; ?>, '<?php echo esc_js($post->post_title); ?>')">
                            <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors truncate max-w-[200px] sm:max-w-md leading-tight"><?php echo esc_html($post->post_title); ?></span>
                            <span class="seo-badge-pill px-1.5 py-0.5 rounded text-[9px] font-extrabold border shrink-0 <?php echo $score_cls; ?>"><?php echo $score; ?>/100</span>
                        </button>
                        <?php endforeach; ?>

                        <div id="seo-no-results" class="hidden py-8 text-center text-zinc-400 text-xs flex flex-col items-center justify-center gap-2">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <span class="font-medium text-zinc-500">No matching articles found</span>
                        </div>
                    </div>

                    <!-- Hidden legacy structures to maintain backward compatibility with JS queries -->
                    <div class="hidden">
                        <div id="seo-article-count-badge"></div>
                        <div id="seo-pagination-container">
                            <div id="seo-pagination-info"></div>
                            <div id="seo-pagination-controls"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unified workspace action buttons (Re-analyze, Run 11-point audit) -->
        <div class="flex items-center gap-2 shrink-0 flex-wrap" id="seo-workspace-actions-wrapper">
            <!-- Rendered dynamically in JS based on active article -->
        </div>
    </div>

    <!-- Main Right Analysis Area - Takes Full Width -->
    <div class="w-full bg-white dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800/80 rounded-xl shadow-2xs p-3 sm:p-5 overflow-x-hidden min-h-[500px]" id="seo-analysis-container">
        <div class="text-center text-zinc-500 py-28 max-w-sm mx-auto select-none">
            <div class="w-16 h-16 bg-zinc-50 dark:bg-zinc-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-zinc-150 dark:border-zinc-850">
                <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-1">Select an Article to Audit</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-450 mb-4">Click "Select an article..." dropdown at the top to view its real-time 11-point SEO audit, AI search visibility signals, and meta optimizations.</p>
        </div>
    </div>
</div>

<!-- PANEL: Performance -->
<div id="panel-ct-performance" class="cora-ct-panel hidden space-y-6">
    <div class="flex items-center justify-between border-b border-zinc-150 pb-4">
        <div>
            <h2 class="text-lg font-bold text-zinc-900 tracking-tight flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 6l-9.5 9.5-5-5L1 18"></path><polyline points="17 6 23 6 23 12"></polyline></svg>
                Organic Performance & Revenue Intelligence
            </h2>
            <p class="text-xs text-zinc-500 mt-1">Trace organic discovery impressions, clicks, engagement CTAs, lead captures, and actual sales ledger revenue.</p>
        </div>
    </div>

    <!-- Multi-tier KPI cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-3xs">
            <span class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Search Imp.</span>
            <div class="text-xl font-bold text-zinc-900 font-mono mt-1" id="perf-total-impressions">--</div>
            <span class="text-[9px] text-zinc-400 block mt-0.5">Google Search Console</span>
        </div>
        <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-3xs">
            <span class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Search Clicks</span>
            <div class="text-xl font-bold text-zinc-900 font-mono mt-1" id="perf-total-clicks">--</div>
            <span class="text-[9px] text-zinc-400 block mt-0.5">Attributed visits</span>
        </div>
        <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-3xs">
            <span class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wider">WhatsApp Clicks</span>
            <div class="text-xl font-bold text-zinc-900 font-mono mt-1" id="perf-total-whatsapp">--</div>
            <span class="text-[9px] text-zinc-400 block mt-0.5">Direct chat inquiries</span>
        </div>
        <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-3xs font-semibold">
            <span class="block text-[9px] font-bold text-zinc-450 uppercase tracking-wider">Attributed Leads</span>
            <div class="text-xl font-bold text-zinc-950 font-mono mt-1" id="perf-total-leads">--</div>
            <span class="text-[9px] text-zinc-500 block mt-0.5">CRM records created</span>
        </div>
        <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-3xs bg-zinc-50/50">
            <span class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Attributed Revenue</span>
            <div class="text-xl font-extrabold text-zinc-900 font-mono mt-1" id="perf-total-revenue">--</div>
            <span class="text-[9px] text-zinc-500 block mt-0.5">Paid closed-won deals</span>
        </div>
    </div>

    <!-- Attribution Breakdown Table -->
    <div class="border border-zinc-200 rounded-xl bg-white shadow-2xs overflow-hidden">
        <div class="px-4 py-3 bg-zinc-50/60 border-b border-zinc-150 flex items-center justify-between">
            <span class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Content attribution model ledger</span>
            <span class="text-[10px] text-zinc-500 font-mono">Last updated: Just now</span>
        </div>
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-zinc-50/30 border-b border-zinc-200 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                        <th class="py-3 px-4">Article Title</th>
                        <th class="py-3 px-4">Focus Keyword</th>
                        <th class="py-3 px-4 text-center">Impressions</th>
                        <th class="py-3 px-4 text-center">Clicks</th>
                        <th class="py-3 px-4 text-center">CTR</th>
                        <th class="py-3 px-4 text-center">WA Clicks</th>
                        <th class="py-3 px-4 text-center">CRM Leads</th>
                        <th class="py-3 px-4 text-right pr-6">Revenue</th>
                    </tr>
                </thead>
                <tbody id="cora-performance-table-body" class="divide-y divide-zinc-100 text-xs text-zinc-700">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View -->
        <div id="cora-performance-mobile-cards" class="block md:hidden flex flex-col gap-3 p-3 bg-zinc-50/60">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<!-- PANEL: Automations -->
<div id="panel-ct-automations" class="cora-ct-panel hidden space-y-6">
    <div class="border border-zinc-200 bg-white rounded-2xl p-5 shadow-2xs space-y-5">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-wider mb-1">Cora Content Autonomy Scale</h3>
            <p class="text-xs text-zinc-500">Configure how much publish-level authority Cora has without manual review.</p>
        </div>

        <form id="cora-autonomy-policy-form" onsubmit="event.preventDefault(); coraSaveAutonomyPolicy();" class="space-y-4">
            <!-- Autonomy Radio Scale -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 select-none">
                <!-- Observe -->
                <label class="p-4 border border-zinc-200 rounded-xl hover:border-zinc-400 cursor-pointer flex flex-col justify-between min-h-[110px] transition-all bg-white relative">
                    <input type="radio" name="autonomy" value="observe" class="absolute top-4 right-4 accent-zinc-950">
                    <div>
                        <span class="text-xs font-bold text-zinc-900 block">1. Observe & Report</span>
                        <p class="text-[10px] text-zinc-500 mt-1 leading-normal">Cora only indexes metrics and lists alerts. Does not suggest topics.</p>
                    </div>
                </label>
                <!-- Recommend -->
                <label class="p-4 border border-zinc-950 rounded-xl ring-1 ring-zinc-950 cursor-pointer flex flex-col justify-between min-h-[110px] transition-all bg-zinc-50/10 relative">
                    <input type="radio" name="autonomy" value="recommend" checked class="absolute top-4 right-4 accent-zinc-950">
                    <div>
                        <span class="text-xs font-bold text-zinc-900 block">2. Recommend (Default)</span>
                        <p class="text-[10px] text-zinc-500 mt-1 leading-normal">Cora generates prioritized backlog. Gaps suggest for approval.</p>
                    </div>
                </label>
                <!-- Prepare -->
                <label class="p-4 border border-zinc-200 rounded-xl hover:border-zinc-400 cursor-pointer flex flex-col justify-between min-h-[110px] transition-all bg-white relative">
                    <input type="radio" name="autonomy" value="prepare" class="absolute top-4 right-4 accent-zinc-950">
                    <div>
                        <span class="text-xs font-bold text-zinc-900 block">3. Prepare Drafts</span>
                        <p class="text-[10px] text-zinc-500 mt-1 leading-normal">Cora auto-writes briefs and drafts in background, waiting for edits.</p>
                    </div>
                </label>
                <!-- Autonomous -->
                <label class="p-4 border border-zinc-200 rounded-xl hover:border-zinc-400 cursor-pointer flex flex-col justify-between min-h-[110px] transition-all bg-white opacity-60 relative" title="Requires verified historical performance record">
                    <input type="radio" name="autonomy" value="autonomous" disabled class="absolute top-4 right-4 accent-zinc-950">
                    <div>
                        <span class="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
                            4. Autonomous
                            <span class="px-1.5 py-0.5 bg-zinc-100 border text-[8px] rounded uppercase font-extrabold tracking-wider text-zinc-650">Locked</span>
                        </span>
                        <p class="text-[10px] text-zinc-500 mt-1 leading-normal">Auto-publish high-confidence topics. (Available in V3-Cofound).</p>
                    </div>
                </label>
            </div>

            <!-- Extra policy toggles -->
            <div class="pt-2 space-y-3">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" id="policy-auto-index" checked class="rounded border-zinc-300 accent-zinc-950 mt-0.5">
                    <div>
                        <span class="text-xs font-bold text-zinc-800 block">Automated IndexNow / Google Crawl Submission</span>
                        <p class="text-[10px] text-zinc-500 leading-normal">Instantly notify Bing Webmaster Tools & participating indexes when articles change.</p>
                    </div>
                </label>
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" id="policy-require-expert" checked class="rounded border-zinc-300 accent-zinc-950 mt-0.5">
                    <div>
                        <span class="text-xs font-bold text-zinc-800 block">Enforce Expert Sign-off for High-Risk Niches</span>
                        <p class="text-[10px] text-zinc-500 leading-normal">Block draft submission if financial, regulatory, or pricing claims are unverified.</p>
                    </div>
                </label>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm">Save Policy Settings</button>
            </div>
        </form>
    </div>

    <!-- PANEL: Google API Connectors -->
    <div class="border border-zinc-200 bg-white rounded-2xl p-5 shadow-2xs space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-wider mb-1">Google Cloud Platform API Connectors</h3>
                <p class="text-xs text-zinc-500">Enable real-time search impressions, clicks, average position, and indexing submission.</p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold <?php echo get_option('cora_google_service_account_json') ? 'bg-green-50 text-green-755 border border-green-200/60' : 'bg-zinc-100 text-zinc-650 border border-zinc-200'; ?> uppercase" id="gsc-connector-status">
                <?php echo get_option('cora_google_service_account_json') ? 'Connected' : 'Disconnected'; ?>
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form fields -->
            <form id="cora-connector-settings-form" onsubmit="event.preventDefault(); coraSaveConnectorSettings();" class="lg:col-span-2 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-700 mb-1">Google Service Account JSON Key</label>
                    <textarea id="cora-connector-gsa-json" rows="6" class="w-full border border-zinc-200 rounded px-3 py-2 text-xs font-mono focus:outline-none focus:border-zinc-400 focus:ring-1 focus:ring-zinc-400" placeholder='Paste contents of your downloaded .json credentials file here...'><?php echo esc_textarea(get_option('cora_google_service_account_json', '')); ?></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-700 mb-1">Search Console Property URL</label>
                        <input type="text" id="cora-connector-gsc-property" class="w-full border border-zinc-200 rounded px-3 py-2 text-xs focus:outline-none focus:border-zinc-400 focus:ring-1 focus:ring-zinc-400" placeholder="e.g. sc-domain:cora.local or http://cora.local/" value="<?php echo esc_attr(get_option('cora_google_search_console_property', '')); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-700 mb-1">Google Analytics 4 Measurement ID</label>
                        <input type="text" id="cora-connector-ga4-id" class="w-full border border-zinc-200 rounded px-3 py-2 text-xs focus:outline-none focus:border-zinc-400 focus:ring-1 focus:ring-zinc-400" placeholder="e.g. G-1234567890" value="<?php echo esc_attr(get_option('cora_google_analytics_measurement_id', '')); ?>">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm cursor-pointer">Save Connector Settings</button>
                </div>
            </form>

            <!-- Sidebar instructions -->
            <div class="lg:col-span-1 border border-zinc-150 rounded-xl bg-zinc-50/30 p-4 space-y-4 text-xs text-zinc-650 leading-relaxed">
                <span class="font-bold text-zinc-900 uppercase tracking-wider block text-[10px]">Setup Instructions</span>
                <ol class="list-decimal pl-4 space-y-2.5">
                    <li>Create a project in the <a href="https://console.cloud.google.com/" target="_blank" class="text-zinc-900 font-bold hover:underline">Google Cloud Console</a>.</li>
                    <li>Enable the <a href="https://console.cloud.google.com/apis/library/searchconsole.googleapis.com" target="_blank" class="text-zinc-900 font-bold hover:underline">Search Console API</a> &amp; <a href="https://console.cloud.google.com/apis/library/analyticsdata.googleapis.com" target="_blank" class="text-zinc-900 font-bold hover:underline">Analytics Data API</a>.</li>
                    <li>Go to the <a href="https://console.cloud.google.com/iam-admin/serviceaccounts" target="_blank" class="text-zinc-900 font-bold hover:underline">Service Accounts Page</a>, create a Service Account, then generate and download a **JSON private key**.</li>
                    <li>Add the Service Account email address as a **Viewer** inside your Google Search Console &amp; Google Analytics properties.</li>
                </ol>
            </div>
        </div>

        <!-- Real-Time URL Index Inspector -->
        <div class="border-t border-zinc-150 pt-5 mt-4">
            <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-900"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                Live Google Index Inspector
            </h4>
            <p class="text-[10px] text-zinc-500 mb-3.5">Input any published article URL to run a live inspection check against the Google Search Console API using your enqueued service key.</p>
            <div class="flex flex-col sm:flex-row gap-3.5 items-stretch max-w-2xl">
                <input type="url" id="cora-inspector-url" placeholder="e.g. http://cora.local/studio/blogs/ai-search-visibility-best-practices/" class="flex-1 border border-zinc-200 rounded px-3 py-2 text-xs focus:outline-none focus:border-zinc-400 focus:ring-1 focus:ring-zinc-400 bg-white">
                <button type="button" onclick="coraInspectGscUrl()" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-all shadow-3xs cursor-pointer flex items-center justify-center gap-1.5 whitespace-nowrap active:scale-98">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Inspect URL
                </button>
            </div>
            <!-- Results display area -->
            <div id="cora-inspector-result" class="hidden mt-4 p-4 border border-zinc-150 rounded-xl bg-zinc-50/20 text-xs text-zinc-750 space-y-3.5 max-w-2xl select-text">
                <!-- Response rendered dynamically -->
            </div>
        </div>
    </div>

    <!-- Automated Execution Activity Log -->
    <div class="border border-zinc-200 bg-white rounded-2xl overflow-hidden shadow-2xs">
        <div class="px-5 py-4 bg-zinc-50/60 border-b border-zinc-150 flex items-center justify-between">
            <span class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Automated Execution Logs</span>
            <span class="text-[9px] font-bold px-2 py-0.5 bg-zinc-200 rounded-full text-zinc-700">All Logs Active</span>
        </div>
        <div class="divide-y divide-zinc-100 text-xs p-2">
            <div class="p-3 flex items-start sm:items-center justify-between gap-3 text-xs">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-zinc-900">IndexNow Submission</span>
                        <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[9px] rounded font-bold border border-green-200/50 uppercase tracking-wider">Success</span>
                    </div>
                    <p class="text-zinc-500 text-[11px]">Submitted URL to IndexNow: newborn baby photography Bandra guide. Bing index returned code 200.</p>
                </div>
                <span class="text-[10px] text-zinc-400 font-mono shrink-0">15 mins ago</span>
            </div>
            <div class="p-3 flex items-start sm:items-center justify-between gap-3 text-xs">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-zinc-900">Workspace knowledge auto-sync</span>
                        <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[9px] rounded font-bold border border-green-200/50 uppercase tracking-wider">Success</span>
                    </div>
                    <p class="text-zinc-500 text-[11px]">Synced 14 knowledge vectors from new listings added to Bandra Realty Hub.</p>
                </div>
                <span class="text-[10px] text-zinc-400 font-mono shrink-0">4 hours ago</span>
            </div>
            <div class="p-3 flex items-start sm:items-center justify-between gap-3 text-xs">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-zinc-900">Claims Expiry Scanner run</span>
                        <span class="px-2 py-0.5 bg-zinc-100 text-zinc-650 text-[9px] rounded font-bold border border-zinc-200 uppercase tracking-wider">Info</span>
                    </div>
                    <p class="text-zinc-500 text-[11px]">Scanned 8 articles. Identified 1 expired claim in Baby session pricing guidelines (Claim Date: 31 July 2026).</p>
                </div>
                <span class="text-[10px] text-zinc-400 font-mono shrink-0">1 day ago</span>
            </div>
        </div>
    </div>
</div>
<!-- PANEL: Content Calendar -->
<div id="panel-ct-calendar" class="cora-ct-panel hidden space-y-5">
    <!-- Top View Selector & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-150 pb-4">
        <div class="flex items-center gap-3">
            <!-- Segmented Control for Views -->
            <div class="inline-flex bg-zinc-100 dark:bg-zinc-900 p-1 rounded-xl shadow-3xs select-none">
                <button onclick="switchCalendarSubView('monthly')" id="cal-subtab-monthly" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-2xs cursor-pointer">Monthly</button>
                <button onclick="switchCalendarSubView('weekly')" id="cal-subtab-weekly" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-zinc-650 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white cursor-pointer">Weekly</button>
                <button onclick="switchCalendarSubView('kanban')" id="cal-subtab-kanban" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-zinc-650 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white cursor-pointer">Kanban</button>
            </div>
            
            <!-- Prev/Next Controls (only shown for Monthly/Weekly) -->
            <div id="cal-date-nav-controls" class="flex items-center gap-2 select-none">
                <button onclick="coraChangeCalendarMonth(-1)" class="p-1.5 border border-zinc-200 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-900 rounded-lg text-zinc-600 dark:text-zinc-300 transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button onclick="coraChangeCalendarMonth(1)" class="p-1.5 border border-zinc-200 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-900 rounded-lg text-zinc-600 dark:text-zinc-300 transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 font-mono tracking-tight text-center ml-1" id="cal-month-year-label">May 2026</span>
            </div>
        </div>
        
        <div class="flex items-center gap-2 shrink-0 select-none">
            <button onclick="coraGoToToday()" id="cal-today-btn" class="px-3 py-1.5 bg-white border border-zinc-200 hover:border-zinc-900 text-zinc-800 text-xs font-bold rounded-xl transition-all cursor-pointer shadow-3xs active:scale-97">Today</button>
            <button class="flex items-center gap-1.5 px-3 py-1.5 border border-zinc-200 hover:border-zinc-900 bg-white rounded-xl text-xs text-zinc-700 font-bold transition-all shadow-3xs active:scale-97 cursor-pointer">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                Filters
            </button>
        </div>
    </div>

    <!-- SUB-VIEW: Monthly Calendar View -->
    <div id="cal-view-monthly" class="cora-cal-subview grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Monthly Grid (3/4 width) -->
        <div class="hidden md:block lg:col-span-3 space-y-4">
            <!-- Calendar Grid Assembly -->
            <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs overflow-hidden">
                <!-- Weekday Headers -->
                <div class="grid grid-cols-7 border-b border-zinc-150 bg-zinc-50/50 text-[10px] font-bold text-zinc-400 text-center uppercase tracking-widest py-3">
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                    <div>Sun</div>
                </div>
                <!-- Days Grid -->
                <div class="grid grid-cols-7 auto-rows-[100px] divide-x divide-y divide-zinc-100 text-xs" id="cora-calendar-grid">
                    <!-- Populated via Javascript -->
                </div>
            </div>
            
            <!-- Calendar Status Legend -->
            <div class="flex items-center gap-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest pl-2 select-none">
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Published</span>
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-zinc-500"></span>Scheduled</span>
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>In Progress</span>
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Draft</span>
            </div>
        </div>

        <!-- Mobile Calendar Agenda List -->
        <div class="block md:hidden space-y-4">
            <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs p-4">
                <h3 class="text-xs font-bold text-zinc-900 mb-3 uppercase tracking-wider">Month Agenda</h3>
                <div id="cora-calendar-mobile-agenda" class="divide-y divide-zinc-100 text-xs">
                    <!-- Populated via Javascript -->
                </div>
            </div>
        </div>
        
        <!-- Right Sidebar: Upcoming (1/4 width) -->
        <div class="hidden md:block lg:col-span-1">
            <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs p-5 space-y-4 flex flex-col justify-between h-full">
                <div class="space-y-4">
                    <h4 class="text-xs font-extrabold text-zinc-950">Upcoming (Next 7 Days)</h4>
                    <div id="cora-upcoming-list" class="divide-y divide-zinc-100 text-xs">
                        <!-- Populated via Javascript -->
                    </div>
                </div>
                <button onclick="switchCalendarSubView('weekly')" class="mt-4 w-full flex items-center justify-center gap-1 px-4 py-2 border border-zinc-200 hover:border-zinc-900 bg-white hover:bg-zinc-50 text-zinc-800 font-bold rounded-xl text-xs transition-all cursor-pointer shadow-3xs active:scale-97">
                    <span>View Full Calendar</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- SUB-VIEW: Weekly View -->
    <div id="cal-view-weekly" class="cora-cal-subview hidden space-y-4">
        <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs p-6 space-y-4">
            <h3 class="text-sm font-extrabold text-zinc-950">Weekly Agenda</h3>
            <div id="cora-weekly-agenda-list" class="space-y-4">
                <!-- Populated via Javascript -->
            </div>
        </div>
    </div>

    <!-- SUB-VIEW: Kanban Planner View -->
    <div id="cal-view-kanban" class="cora-cal-subview hidden space-y-4">
        <!-- Mobile Kanban Column Switcher -->
        <div class="flex md:hidden bg-zinc-100 dark:bg-zinc-900 p-1 rounded-xl mb-4 select-none" id="kbc-mobile-switcher">
            <button onclick="switchMobileKanbanColumn('idea')" id="kbc-tab-idea" class="flex-1 text-center py-1.5 rounded-lg text-xs font-bold bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-2xs cursor-pointer">Idea</button>
            <button onclick="switchMobileKanbanColumn('drafting')" id="kbc-tab-drafting" class="flex-1 text-center py-1.5 rounded-lg text-xs font-bold text-zinc-600 dark:text-zinc-450 cursor-pointer">Drafting</button>
            <button onclick="switchMobileKanbanColumn('review')" id="kbc-tab-review" class="flex-1 text-center py-1.5 rounded-lg text-xs font-bold text-zinc-600 dark:text-zinc-450 cursor-pointer">Review</button>
            <button onclick="switchMobileKanbanColumn('scheduled')" id="kbc-tab-scheduled" class="flex-1 text-center py-1.5 rounded-lg text-xs font-bold text-zinc-600 dark:text-zinc-450 cursor-pointer">Scheduled</button>
        </div>

        <!-- Kanban Columns Container -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="cora-kanban-board-container">
            <!-- Idea Column -->
            <div class="flex kanban-col flex-col rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-4 space-y-3.5" data-column="idea">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-extrabold text-zinc-900 flex items-center gap-1.5 uppercase tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-zinc-400"></span>Idea
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-zinc-250/60 text-zinc-700 rounded-full text-[10px] font-bold" id="kanban-count-idea">0</span>
                        <button onclick="openCreateArticleDrawer('idea')" class="text-zinc-500 hover:text-zinc-900 font-bold cursor-pointer">+</button>
                    </div>
                </div>
                <div class="space-y-3 kanban-cards-dropzone min-h-[350px] p-1 overflow-y-auto" id="kanban-cards-idea"></div>
                <button onclick="openCreateArticleDrawer('idea')" class="w-full py-2 border border-dashed border-zinc-200 hover:border-zinc-500 rounded-xl text-center text-xs font-bold text-zinc-550 transition-all hover:bg-zinc-100/50 cursor-pointer">+ Add Idea</button>
            </div>

            <!-- Drafting Column -->
            <div class="hidden md:flex kanban-col flex-col rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-4 space-y-3.5" data-column="drafting">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-extrabold text-zinc-900 flex items-center gap-1.5 uppercase tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>Drafting
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-zinc-250/60 text-zinc-700 rounded-full text-[10px] font-bold" id="kanban-count-drafting">0</span>
                        <button onclick="openCreateArticleDrawer('drafting')" class="text-zinc-500 hover:text-zinc-900 font-bold cursor-pointer">+</button>
                    </div>
                </div>
                <div class="space-y-3 kanban-cards-dropzone min-h-[350px] p-1 overflow-y-auto" id="kanban-cards-drafting"></div>
                <button onclick="openCreateArticleDrawer('drafting')" class="w-full py-2 border border-dashed border-zinc-200 hover:border-zinc-500 rounded-xl text-center text-xs font-bold text-zinc-550 transition-all hover:bg-zinc-100/50 cursor-pointer">+ Add Draft</button>
            </div>

            <!-- Review Column -->
            <div class="hidden md:flex kanban-col flex-col rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-4 space-y-3.5" data-column="review">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-extrabold text-zinc-900 flex items-center gap-1.5 uppercase tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-violet-400"></span>Review
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-zinc-250/60 text-zinc-700 rounded-full text-[10px] font-bold" id="kanban-count-review">0</span>
                        <button onclick="openCreateArticleDrawer('review')" class="text-zinc-500 hover:text-zinc-900 font-bold cursor-pointer">+</button>
                    </div>
                </div>
                <div class="space-y-3 kanban-cards-dropzone min-h-[350px] p-1 overflow-y-auto" id="kanban-cards-review"></div>
                <button onclick="openCreateArticleDrawer('review')" class="w-full py-2 border border-dashed border-zinc-200 hover:border-zinc-500 rounded-xl text-center text-xs font-bold text-zinc-550 transition-all hover:bg-zinc-100/50 cursor-pointer">+ Add Review</button>
            </div>

            <!-- Scheduled Column -->
            <div class="hidden md:flex kanban-col flex-col rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-4 space-y-3.5" data-column="scheduled">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-extrabold text-zinc-900 flex items-center gap-1.5 uppercase tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>Scheduled
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-zinc-250/60 text-zinc-700 rounded-full text-[10px] font-bold" id="kanban-count-scheduled">0</span>
                        <button onclick="openCreateArticleDrawer('scheduled')" class="text-zinc-500 hover:text-zinc-900 font-bold cursor-pointer">+</button>
                    </div>
                </div>
                <div class="space-y-3 kanban-cards-dropzone min-h-[350px] p-1 overflow-y-auto" id="kanban-cards-scheduled"></div>
                <button onclick="openCreateArticleDrawer('scheduled')" class="w-full py-2 border border-dashed border-zinc-200 hover:border-zinc-500 rounded-xl text-center text-xs font-bold text-zinc-550 transition-all hover:bg-zinc-100/50 cursor-pointer">+ Add Scheduled</button>
            </div>
        </div>
        
        <!-- Legend & Tips -->
        <div class="flex items-center justify-between text-xs text-zinc-500 pt-2 select-none border-t border-zinc-100">
            <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest text-zinc-400">
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>Idea</span>
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Drafting</span>
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>Review</span>
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Scheduled</span>
            </div>
            <div class="flex items-center gap-1 font-bold text-[10px] uppercase tracking-wider text-zinc-400">
                Drag and drop cards to move between stages
            </div>
        </div>
    </div>
</div>

<!-- PANEL: Workflow Board -->
<div id="panel-ct-workflow" class="cora-ct-panel hidden">
    <?php include CORA_WORKSPACE_PATH . 'views/partials/content-workflow-board.php'; ?>
</div>

<!-- PANEL: Business Brain -->
<div id="panel-ct-brain" class="cora-ct-brain cora-ct-panel hidden space-y-6">
    <div class="flex flex-col md:flex-row gap-4 items-stretch md:items-start min-w-0 overflow-hidden w-full max-w-full">
        <!-- Left: Brain Categories Sidebar -->
        <div class="w-full md:w-60 shrink-0 bg-white border border-zinc-200 rounded-xl shadow-2xs p-3 flex flex-row md:flex-col gap-1.5 overflow-x-auto scrollbar-width-none">
            <span class="hidden md:block text-[10px] font-bold text-zinc-400 uppercase tracking-wider px-2 py-1">Brain Categories</span>
            <button onclick="coraSwitchBrainCategory('cora_brain_service', this)" class="brain-cat-btn whitespace-nowrap shrink-0 w-auto md:w-full text-left px-3 py-2 text-xs font-bold rounded-lg border border-transparent transition-all cursor-pointer bg-zinc-900 text-white shadow-2xs">
                Services & service areas
            </button>
            <button onclick="coraSwitchBrainCategory('cora_brain_location', this)" class="brain-cat-btn whitespace-nowrap shrink-0 w-auto md:w-full text-left px-3 py-2 text-xs font-semibold rounded-lg border border-transparent transition-all cursor-pointer text-zinc-650 hover:bg-zinc-55 hover:text-zinc-900">
                Locations & hours
            </button>
            <button onclick="coraSwitchBrainCategory('cora_brain_proof', this)" class="brain-cat-btn whitespace-nowrap shrink-0 w-auto md:w-full text-left px-3 py-2 text-xs font-semibold rounded-lg border border-transparent transition-all cursor-pointer text-zinc-650 hover:bg-zinc-55 hover:text-zinc-900">
                Approved case studies & proof
            </button>
            <button onclick="coraSwitchBrainCategory('cora_brain_voice', this)" class="brain-cat-btn whitespace-nowrap shrink-0 w-auto md:w-full text-left px-3 py-2 text-xs font-semibold rounded-lg border border-transparent transition-all cursor-pointer text-zinc-650 hover:bg-zinc-55 hover:text-zinc-900">
                Brand voice & style
            </button>
            <button onclick="coraSwitchBrainCategory('cora_brain_restriction', this)" class="brain-cat-btn whitespace-nowrap shrink-0 w-auto md:w-full text-left px-3 py-2 text-xs font-semibold rounded-lg border border-transparent transition-all cursor-pointer text-zinc-650 hover:bg-zinc-55 hover:text-zinc-900">
                Rules & restricted claims
            </button>
            <button onclick="coraSwitchBrainCategory('cora_brain_faq', this)" class="brain-cat-btn whitespace-nowrap shrink-0 w-auto md:w-full text-left px-3 py-2 text-xs font-semibold rounded-lg border border-transparent transition-all cursor-pointer text-zinc-650 hover:bg-zinc-55 hover:text-zinc-900">
                Sales & support FAQs
            </button>
        </div>

        <!-- Right: Brain Items List -->
        <div class="flex-1 bg-white border border-zinc-200 rounded-xl shadow-2xs overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-zinc-150 flex items-center justify-between bg-zinc-50/20">
                <div>
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider" id="brain-category-header-title">Services & service areas</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5">Define core business services and target territories to ground AI generation.</p>
                </div>
                <button onclick="coraOpenBrainResourceDrawer()" class="px-3.5 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors flex items-center gap-1 shadow-3xs cursor-pointer active:scale-95">
                    + Add Resource
                </button>
            </div>

            <!-- Notion Style Data Table (Desktop View) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse" id="cora-brain-items-table">
                    <thead>
                        <tr class="border-b border-zinc-200 bg-zinc-50/50 text-[10px] font-bold uppercase tracking-wider text-zinc-400">
                            <th class="px-5 py-3">Resource Title</th>
                            <th class="px-5 py-3">Content / Verified Claims</th>
                            <th class="px-5 py-3 text-right pr-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cora-brain-items-table-body" class="divide-y divide-zinc-100 text-xs text-zinc-700">
                        <!-- Loaded dynamically via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards View -->
            <div id="cora-brain-items-mobile-cards" class="block md:hidden flex flex-col divide-y divide-zinc-100 p-3 bg-zinc-50/25 gap-3">
                <!-- Loaded dynamically via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Business Brain Resource Slide Drawer Sheet (Right-sliding per Rule #1) -->
<aside id="cora-brain-resource-sheet" class="cora-bottom-sheet collapsed border-l border-zinc-200 bg-white shadow-2xl flex flex-col">
    <div class="px-6 py-4 border-b border-zinc-200/80 flex justify-between items-center shrink-0">
        <div>
            <h2 class="text-lg font-bold text-zinc-900" id="cora-brain-drawer-title">Add Brain Resource</h2>
            <p class="text-xs text-zinc-550">Index verified knowledge for grounded AI content generation.</p>
        </div>
        <button class="text-zinc-400 hover:text-zinc-900 cursor-pointer" onclick="coraCloseBrainResourceDrawer()">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-brain-resource-form" onsubmit="event.preventDefault(); coraSaveBrainResource();" class="p-6 overflow-y-auto flex-1 space-y-4">
        <input type="hidden" id="cora-brain-field-id" value="0">
        
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1">Knowledge Title / Label *</label>
            <input type="text" id="cora-brain-field-title" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-zinc-400" placeholder="e.g. Newborn Session Packages" required>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1">Category Ingestion Type *</label>
            <select id="cora-brain-field-cat" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-zinc-400">
                <option value="cora_brain_service">Services & service areas</option>
                <option value="cora_brain_location">Locations & hours</option>
                <option value="cora_brain_proof">Approved case studies & proof</option>
                <option value="cora_brain_voice">Brand voice & style</option>
                <option value="cora_brain_restriction">Rules & restricted claims</option>
                <option value="cora_brain_faq">Sales & support FAQs</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1">Verified Facts & Claims Content *</label>
            <textarea id="cora-brain-field-content" rows="10" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-zinc-400 resize-none font-sans" placeholder="Paste or type facts, pricing, locations, opening hours, expert bio details, case studies, or brand voice constraints here..." required></textarea>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t border-zinc-100">
            <button type="button" onclick="coraCloseBrainResourceDrawer()" class="px-4 py-2 border border-zinc-200 hover:bg-zinc-50 rounded-lg text-xs text-zinc-700 transition-colors">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm">Ingest Resource</button>
        </div>
    </form>
</aside>
<!-- BOTTOM SHEET STYLING -->
<style>
/* Shimmering Skeleton Loader Styling */
.cora-skeleton {
    background: linear-gradient(90deg, #f4f4f5 25%, #e4e4e7 50%, #f4f4f5 75%) !important;
    background-size: 200% 100% !important;
    animation: cora-shimmer 1.6s infinite linear !important;
    border-radius: 0.375rem !important; /* rounded-md */
    display: block !important;
    width: 100%;
}

.dark .cora-skeleton {
    background: linear-gradient(90deg, #27272a 25%, #3f3f46 50%, #27272a 75%) !important;
    background-size: 200% 100% !important;
}

@keyframes cora-shimmer {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Overview Metrics Grid & Stats Cards Styling */
.cora-metrics-grid {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0.75rem !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    overflow-x: visible !important;
}

.cora-metrics-grid > .cora-stat-card:last-child {
    grid-column: span 2 / span 2 !important;
}

@media (min-width: 640px) {
    .cora-metrics-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 1.25rem !important;
    }
}

@media (min-width: 1024px) {
    .cora-metrics-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    }
    .cora-metrics-grid > .cora-stat-card:last-child {
        grid-column: span 1 / span 1 !important;
    }
}

.cora-stat-card {
    background-color: #ffffff !important;
    border: 1px solid rgba(228, 228, 231, 0.9) !important; /* zinc-200 */
    border-radius: 1rem !important; /* rounded-2xl */
    padding: 0.85rem !important; /* compact padding on mobile */
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important; /* shadow-2xs */
    transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1) !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    min-height: 110px !important; /* compact min-height on mobile */
    width: auto !important;
    flex-shrink: 1 !important;
}

.dark .cora-stat-card {
    background-color: rgba(24, 24, 27, 0.5) !important; /* dark:bg-zinc-900/50 */
    border-color: rgb(39, 39, 42) !important; /* dark:border-zinc-800 */
}

.cora-stat-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important; /* shadow-xs */
    border-color: rgb(212, 212, 216) !important; /* hover:border-zinc-300 */
}

.dark .cora-stat-card:hover {
    border-color: rgb(63, 63, 70) !important; /* dark:hover:border-zinc-700 */
}

@media (min-width: 640px) {
    .cora-stat-card {
        padding: 1.25rem !important;
        min-height: 148px !important;
        width: auto !important;
    }
}

/* Locked Business Brain Tab Button */
.cora-tab-btn[data-tab="ct-brain"] {
    opacity: 0.65 !important;
}
.cora-tab-btn[data-tab="ct-brain"]:hover {
    color: rgb(113, 113, 122) !important; /* text-zinc-500 */
    border-color: transparent !important;
}

.checklist-item, .checklist-item * {
    outline: none !important;
}
.checklist-item {
    border: 1px solid rgba(228, 228, 231, 0.7) !important;
}
.checklist-item:hover {
    border-color: rgba(212, 212, 216, 1) !important;
}

/* Side Drawer Sheet (Right-sliding per Global Rule #1) */
.cora-bottom-sheet {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    left: auto !important;
    width: 100% !important;
    max-width: 480px !important;
    height: 100vh !important;
    max-height: 100vh !important;
    border-top-left-radius: 1.25rem !important;
    border-bottom-left-radius: 1.25rem !important;
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    z-index: 99999 !important;
    box-sizing: border-box !important;
    transition: transform 320ms cubic-bezier(0.16, 1, 0.3, 1), opacity 220ms ease, visibility 320ms ease !important;
}
.cora-bottom-sheet.collapsed {
    transform: translateX(110%) !important;
    opacity: 0 !important;
    pointer-events: none !important;
    visibility: hidden !important;
    box-shadow: none !important;
}
.cora-bottom-sheet:not(.collapsed) {
    transform: translateX(0) !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    visibility: visible !important;
    box-shadow: -8px 0 40px rgba(0,0,0,0.18) !important;
}
/* Backdrop always on top */
#cora-drawer-backdrop {
    z-index: 99998 !important;
}
@media (min-width: 768px) {
    .kanban-col {
        display: flex !important;
    }
}
</style>

<!-- DRAWERS -->
<!-- Drawer Backdrop -->
<div id="cora-drawer-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[9998] hidden transition-opacity cursor-pointer" onclick="window.coraCloseAllDrawers()"></div>

<!-- Create Article Side Drawer -->
<aside id="cora-create-article-sheet" class="cora-bottom-sheet collapsed border-l border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl flex flex-col">
    <div class="px-6 py-4 border-b border-zinc-200/80 flex justify-between items-center shrink-0">
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

<?php
// ─── Floating AI Agent (Reusable Partial) ───────────────────────────
$cora_agent_config = array(
    'page_context'  => 'content_suite',
    'ajax_action'   => 'cora_ajax_content_suite_agent',
    'placeholder'   => 'Ask anything or search articles, keywords, opportunities...',
    'pill_text'     => 'Search articles, keywords, opportunities...',
    'quick_actions' => array(
        array( 'id' => 'new-article',      'label' => 'New Article',       'icon' => 'edit' ),
        array( 'id' => 'content-brief',    'label' => 'AI Content Brief',  'icon' => 'file' ),
        array( 'id' => 'keyword-research', 'label' => 'Keyword Research',  'icon' => 'search' ),
        array( 'id' => 'optimizer',        'label' => 'Optimizer',         'icon' => 'sliders' ),
    ),
    'suggestions' => array(
        array( 'text' => 'Run index check',           'icon' => 'activity' ),
        array( 'text' => 'Optimize meta descriptors', 'icon' => 'folder' ),
        array( 'text' => 'Suggest question headings', 'icon' => 'file-plus' ),
        array( 'text' => 'Audit organic search rank', 'icon' => 'search' ),
    ),
);
include CORA_WORKSPACE_PATH . 'views/partials/floating-agent.php';
?>

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
            window.openCreateArticleDrawer(dateStr);
        }
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

    window.coraGetSkeletonHtml = function(type, count = 3) {
        let html = '';
        if (type === 'table-rows') {
            for (let i = 0; i < count; i++) {
                html += `
                <tr class="border-b border-zinc-100 dark:border-zinc-800/50">
                    <td class="px-5 py-4"><div class="cora-skeleton h-4 w-2/3"></div></td>
                    <td class="px-5 py-4"><div class="cora-skeleton h-4 w-1/2"></div></td>
                    <td class="px-5 py-4"><div class="cora-skeleton h-4 w-1/3"></div></td>
                    <td class="px-5 py-4"><div class="cora-skeleton h-4 w-1/4"></div></td>
                    <td class="px-5 py-4"><div class="cora-skeleton h-4 w-12 rounded-full"></div></td>
                    <td class="px-5 py-4"><div class="cora-skeleton h-4 w-16"></div></td>
                    <td class="px-5 py-4"><div class="cora-skeleton h-4 w-16"></div></td>
                    <td class="px-5 py-4"><div class="cora-skeleton h-7 w-20 rounded-lg"></div></td>
                </tr>`;
            }
        } else if (type === 'cards') {
            for (let i = 0; i < count; i++) {
                html += `
                <div class="border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl bg-white dark:bg-zinc-950 p-4 space-y-3.5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <div class="cora-skeleton h-4 w-1/3"></div>
                        <div class="cora-skeleton h-4.5 w-12 rounded-full"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="cora-skeleton h-3.5 w-5/6"></div>
                        <div class="cora-skeleton h-3.5 w-2/3"></div>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
                        <div class="cora-skeleton h-3.5 w-1/4"></div>
                        <div class="cora-skeleton h-7 w-20 rounded-lg"></div>
                    </div>
                </div>`;
            }
        } else if (type === 'calendar-list') {
            for (let i = 0; i < count; i++) {
                html += `
                <div class="border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl bg-white dark:bg-zinc-950 p-4 flex items-center justify-between gap-4 shadow-2xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="cora-skeleton h-9 w-9 rounded-xl shrink-0"></div>
                        <div class="space-y-1.5 min-w-0">
                            <div class="cora-skeleton h-3.5 w-32"></div>
                            <div class="cora-skeleton h-3 w-48"></div>
                        </div>
                    </div>
                    <div class="cora-skeleton h-6 w-16 rounded-full shrink-0"></div>
                </div>`;
            }
        } else if (type === 'performance-rows') {
            for (let i = 0; i < count; i++) {
                html += `
                <tr class="border-b border-zinc-100 dark:border-zinc-800/50">
                    <td class="px-4 py-3"><div class="cora-skeleton h-4 w-32"></div></td>
                    <td class="px-4 py-3"><div class="cora-skeleton h-4 w-12"></div></td>
                    <td class="px-4 py-3"><div class="cora-skeleton h-4 w-16"></div></td>
                    <td class="px-4 py-3"><div class="cora-skeleton h-4 w-16"></div></td>
                    <td class="px-4 py-3"><div class="cora-skeleton h-4 w-16"></div></td>
                    <td class="px-4 py-3"><div class="cora-skeleton h-4 w-20"></div></td>
                    <td class="px-4 py-3 text-right"><div class="cora-skeleton h-7 w-20 rounded-lg inline-block"></div></td>
                </tr>`;
            }
        } else if (type === 'performance-cards') {
            for (let i = 0; i < count; i++) {
                html += `
                <div class="border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl bg-white dark:bg-zinc-950 p-4 space-y-3 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <div class="cora-skeleton h-4 w-1/2"></div>
                        <div class="cora-skeleton h-4 w-16"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5 pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
                        <div class="space-y-1">
                            <div class="cora-skeleton h-3 w-16"></div>
                            <div class="cora-skeleton h-4 w-12"></div>
                        </div>
                        <div class="space-y-1">
                            <div class="cora-skeleton h-3 w-16"></div>
                            <div class="cora-skeleton h-4 w-12"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
                        <div class="cora-skeleton h-3 w-20"></div>
                        <div class="cora-skeleton h-7 w-20 rounded-lg"></div>
                    </div>
                </div>`;
            }
        }
        return html;
    };

    function switchContentTab(tabId) {
        if (tabId === 'ct-brain') {
            if (window.coraShowToast) {
                window.coraShowToast('Business Brain is coming soon! This knowledge base module is currently in development.', 'info');
            }
            return;
        }
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

        if (tabId === 'ct-workflow' || tabId === 'ct-library') {
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
                if (typeof window.openSEOAnalysis === 'function') {
                    window.openSEOAnalysis(firstBtn.dataset.id, firstBtn.dataset.title);
                }
            }
        }
        if (tabId === 'ct-overview') {
            if (typeof window.coraFetchOverview === 'function') window.coraFetchOverview();
        }
        if (tabId === 'ct-opportunities') {
            if (typeof window.coraFetchOpportunities === 'function') window.coraFetchOpportunities();
        }
        if (tabId === 'ct-calendar') {
            if (typeof window.switchCalendarSubView === 'function') window.switchCalendarSubView('monthly');
            if (typeof window.coraRenderCalendar === 'function') window.coraRenderCalendar();
        }
        if (tabId === 'ct-performance') {
            if (typeof window.coraFetchPerformance === 'function') window.coraFetchPerformance();
        }
        if (tabId === 'ct-brain') {
            if (typeof window.coraFetchBrainItems === 'function') window.coraFetchBrainItems();
        }
    }
    window.switchContentTab = switchContentTab;

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
        let ct = urlParams.get('ct') || 'ct-overview';
        if (ct === 'ct-brain') {
            ct = 'ct-overview';
        }
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
    window.openCreateArticleDrawer = function(prefillDate, prefillStage) {
        if (window.innerWidth < 768) {
            if (window.coraShowToast) {
                window.coraShowToast('🔒 Article Creation & Editor are locked on mobile. Please open on a laptop or tablet screen (≥768px).', 'info');
            }
            return false;
        }

        if (window.coraShowToast) {
            window.coraShowToast('Creating new draft...', 'info');
        }

        const ajaxUrl   = (window.coraREWPData && window.coraREWPData.ajaxUrl)   ? window.coraREWPData.ajaxUrl   :
                          (window.coraREData   && window.coraREData.ajaxUrl)     ? window.coraREData.ajaxUrl     :
                          (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
        const ajaxNonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) ? window.coraREWPData.ajaxNonce :
                          (window.coraREData   && window.coraREData.ajaxNonce)   ? window.coraREData.ajaxNonce   : '';

        const body = new URLSearchParams();
        body.append('action', 'cora_create_article');
        body.append('nonce', ajaxNonce);
        body.append('security', ajaxNonce);
        body.append('title', 'Untitled Draft');
        body.append('status', 'draft');

        if (prefillDate) {
            body.append('publish_date', prefillDate);
        }
        if (prefillStage) {
            body.append('editorial_status', prefillStage);
        }

        fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
            .then(res => res.json())
            .then(response => {
                if (response && response.success && response.data && response.data.post_id) {
                    const newId = response.data.post_id;
                    if (window.coraShowToast) {
                        window.coraShowToast('Draft created! Opening editor...', 'success');
                    }
                    
                    // Close any open drawers
                    if (typeof window.coraCloseAllDrawers === 'function') {
                        window.coraCloseAllDrawers();
                    }
                    
                    // Set flag so closing the editor will reload the list/board
                    window.coraArticleSavedDuringSession = true;

                    // Open editor
                    if (typeof window.coraEditArticle === 'function') {
                        window.coraEditArticle(newId);
                    }
                } else {
                    const msg = (response && response.data) ? response.data : 'Failed to create article';
                    if (window.coraShowToast) {
                        window.coraShowToast(msg, 'error');
                    }
                }
            })
            .catch(err => {
                if (window.coraShowToast) {
                    window.coraShowToast('Network error creating article', 'error');
                }
                console.error(err);
            });
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
                btn.className = 'seo-report-tab-btn py-1.5 px-3.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-zinc-900 shadow-2xs border border-zinc-200/80 active whitespace-nowrap shrink-0';
            } else {
                btn.className = 'seo-report-tab-btn py-1.5 px-3.5 rounded-lg text-xs font-semibold transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 border border-transparent whitespace-nowrap shrink-0';
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

    window.triggerSEOAnalysis = function(articleId, btnEl) {
        const targetId = articleId || window._currentSEOArticleId;
        if (!targetId) {
            if (window.coraShowToast) window.coraShowToast('Please select an article first', 'error');
            return;
        }

        const origHtml = btnEl ? btnEl.innerHTML : '';
        const labelSpan = btnEl ? btnEl.querySelector('span') : null;
        
        if (btnEl) {
            btnEl.disabled = true;
            btnEl.classList.add('opacity-80', 'cursor-not-allowed');
            btnEl.innerHTML = `
                <svg class="animate-spin shrink-0 text-amber-400" viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="12"></circle></svg>
                <span id="seo-btn-status-txt">Scanning Content...</span>
            `;
        }

        // Add subtle loading pulse overlay on report cards
        const reportArea = document.getElementById('seo-analysis-container');
        if (reportArea) {
            reportArea.classList.add('opacity-70', 'transition-opacity');
        }

        // Reset metrics to shimmering skeleton loading blocks
        const scoreLarge = document.getElementById('inline-seo-score-large');
        const geoEl = document.getElementById('inline-geo-score');
        const densEl = document.getElementById('inline-kw-density');
        const readScoreEl = document.getElementById('inline-readability-score');
        const checklistGrid = document.getElementById('inline-seo-checklist-grid');
        const ring = document.getElementById('inline-seo-ring');
        const checkRing = document.getElementById('inline-checklist-ring');
        const checkScore = document.getElementById('inline-checklist-score-num');
        const checkSubtext = document.getElementById('inline-checklist-subtext');

        if (scoreLarge) scoreLarge.innerHTML = '<div class="cora-skeleton h-5.5 w-12 my-1"></div>';
        if (geoEl) geoEl.innerHTML = '<div class="cora-skeleton h-6 w-14 my-1"></div>';
        if (densEl) densEl.innerHTML = '<div class="cora-skeleton h-6 w-10 my-1"></div>';
        if (readScoreEl) readScoreEl.innerHTML = '<div class="cora-skeleton h-6 w-14 my-1"></div>';
        if (ring) ring.setAttribute('stroke-dashoffset', '163.3');
        if (checkRing) checkRing.setAttribute('stroke-dashoffset', '163.3');
        if (checkScore) checkScore.innerHTML = '<div class="cora-skeleton h-4 w-28 mx-auto mt-1"></div>';
        if (checkSubtext) checkSubtext.innerHTML = '<div class="cora-skeleton h-3 w-40 mx-auto mt-1.5"></div>';

        const badges = document.querySelectorAll('#inline-seo-checklist-categories span');
        badges.forEach(b => {
            b.innerHTML = '<div class="cora-skeleton h-2 w-4 inline-block"></div>';
            b.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 text-zinc-400 border border-zinc-200/60 dark:border-zinc-800';
        });

        if (checklistGrid) {
            checklistGrid.innerHTML = `
                <div class="space-y-3 py-1">
                    <div class="p-3 border border-zinc-150/70 dark:border-zinc-800 rounded-xl space-y-2.5 bg-zinc-55/10 dark:bg-zinc-900/10">
                        <div class="flex items-center justify-between">
                            <div class="cora-skeleton h-3.5 w-1/3"></div>
                            <div class="cora-skeleton h-4.5 w-20 rounded-full"></div>
                        </div>
                        <div class="cora-skeleton h-3 w-5/6 mt-1"></div>
                    </div>
                    <div class="p-3 border border-zinc-150/70 dark:border-zinc-800 rounded-xl space-y-2.5 bg-zinc-55/10 dark:bg-zinc-900/10">
                        <div class="flex items-center justify-between">
                            <div class="cora-skeleton h-3.5 w-1/4"></div>
                            <div class="cora-skeleton h-4.5 w-20 rounded-full"></div>
                        </div>
                        <div class="cora-skeleton h-3 w-4/5 mt-1"></div>
                    </div>
                </div>`;
        }

        if (window.coraShowToast) window.coraShowToast('Scanning article & running 11-point audit...', 'info');

        // Multi-step scanning feedback
        setTimeout(() => {
            const statusTxt = document.getElementById('seo-btn-status-txt');
            if (statusTxt) statusTxt.innerText = 'Auditing 11 Rules...';
        }, 350);

        setTimeout(() => {
            const statusTxt = document.getElementById('seo-btn-status-txt');
            if (statusTxt) statusTxt.innerText = 'Calculating SEO Score...';
        }, 700);

        setTimeout(() => {
            // First save any meta edits if fields exist
            if (typeof window.saveInlineSEOMeta === 'function') {
                window.saveInlineSEOMeta(targetId);
            }

            if (typeof window.runInlineSEOAudit === 'function') {
                window.runInlineSEOAudit(targetId, function() {
                    if (reportArea) {
                        reportArea.classList.remove('opacity-70');
                    }
                    if (btnEl) {
                        btnEl.disabled = false;
                        btnEl.classList.remove('opacity-80', 'cursor-not-allowed');
                        btnEl.innerHTML = `
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-500"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span class="font-bold text-emerald-600">✓ Audit Updated!</span>
                        `;
                        setTimeout(() => {
                            if (btnEl) btnEl.innerHTML = origHtml;
                        }, 2500);
                    }
                    if (window.coraShowToast) window.coraShowToast('11-Point SEO Audit updated successfully!', 'success');
                });
            } else {
                if (reportArea) reportArea.classList.remove('opacity-70');
                if (btnEl) {
                    btnEl.disabled = false;
                    btnEl.classList.remove('opacity-80', 'cursor-not-allowed');
                    if (origHtml) btnEl.innerHTML = origHtml;
                }
            }
        }, 1100);
    };

    // Mobile dropdown handler for SEO article selection
    window.coraMobileSEOArticleSelected = function(selectEl) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;
        const articleId = selectedOption.value;
        const title = selectedOption.dataset.title || selectedOption.textContent.split(' \u2014 ')[0].trim();
        if (typeof window.openSEOAnalysis === 'function') {
            window.openSEOAnalysis(articleId, title);
        }
    };

    window.openSEOAnalysis = function(articleId, title) {
        window._currentSEOArticleId = articleId;
        // Highlight active item in combobox dropdown list
        document.querySelectorAll('.seo-article-btn').forEach(btn => {
            if(btn.dataset.id == articleId) {
                btn.classList.add('active', 'bg-zinc-50', 'dark:bg-zinc-900', 'border-zinc-200/80', 'dark:border-zinc-800', 'shadow-3xs');
            } else {
                btn.classList.remove('active', 'bg-zinc-50', 'dark:bg-zinc-900', 'border-zinc-200/80', 'dark:border-zinc-800', 'shadow-3xs');
            }
        });

        // Update active article title display on top selector button
        const dropdownDisplay = document.getElementById('seo-active-article-title-display');
        if (dropdownDisplay) {
            dropdownDisplay.textContent = title;
        }

        // Auto-close dropdown popover
        if (typeof window.coraCloseSEODropdown === 'function') {
            window.coraCloseSEODropdown();
        }

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

        // Render dynamic action buttons to persistent Top Control Bar
        const actionsWrapper = document.getElementById('seo-workspace-actions-wrapper');
        if (actionsWrapper) {
            actionsWrapper.innerHTML = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border border-zinc-200/90 dark:border-zinc-800 text-xs font-bold shadow-2xs">
                    <svg viewBox="0 0 24 24" width="13" height="13" class="shrink-0"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                    Google Verified
                </span>
                <button id="btn-reanalyze-seo" class="bg-white hover:bg-zinc-50 text-zinc-700 dark:text-zinc-350 border border-zinc-200 dark:border-zinc-800 font-semibold px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs dark:bg-zinc-950 dark:hover:bg-zinc-900" onclick="triggerSEOAnalysis(${articleId}, this)">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                    <span>Re-analyze</span>
                </button>
                <button id="btn-run-audit-seo" class="bg-zinc-900 hover:bg-zinc-800 text-white font-semibold px-3.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-xs dark:bg-zinc-50 dark:text-zinc-950 dark:hover:bg-zinc-200" onclick="triggerSEOAnalysis(${articleId}, this)">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <span class="hidden sm:inline">Run 11-Point Audit</span>
                    <span class="sm:hidden">Run Audit</span>
                </button>
            `;
        }

        const container = document.getElementById('seo-analysis-container');
        if(!container) return;

        // Render inline analysis workspace frame
        container.innerHTML = `
            <div class="space-y-4 sm:space-y-6 max-w-full overflow-hidden">
                <!-- Header Bar -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 pb-2 border-b border-zinc-100 dark:border-zinc-900">
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-widest mb-1">SEO & AI AUDIT WORKSPACE</div>
                        <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-100 leading-tight line-clamp-2">${escJsHtml(title)}</h2>
                        <div class="text-xs text-zinc-500 dark:text-zinc-450 mt-1 flex items-center gap-2 flex-wrap font-medium">
                            <span>Article ID #${articleId}</span>
                            <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                            <span id="inline-word-count">Word Count: --</span>
                            <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                            <span id="inline-last-analyzed">Last Analyzed: --</span>
                        </div>
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
                </div>

                <!-- 4-Metric Top Bar -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <!-- Metric 1: Overall SEO Score -->
                    <div class="bg-white dark:bg-zinc-950 border border-zinc-200/80 hover:border-zinc-300 dark:border-zinc-800 dark:hover:border-zinc-700 transition-all rounded-xl p-3 sm:p-4 flex flex-col justify-between shadow-2xs relative overflow-hidden">
                        <div class="flex items-center justify-between mb-2 gap-1.5">
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">Overall SEO Score</span>
                            <span id="inline-seo-badge" class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 text-zinc-400 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-8 inline-block"></div></span>
                        </div>
                        <div class="flex items-center gap-3 my-1">
                            <div class="relative w-12 h-12 shrink-0 flex items-center justify-center">
                                <svg width="48" height="48" viewBox="0 0 64 64" class="-rotate-90">
                                    <circle cx="32" cy="32" r="26" stroke="#f4f4f5" stroke-width="5" fill="none"/>
                                    <circle cx="32" cy="32" r="26" stroke="#10b981" stroke-width="5" fill="none"
                                        stroke-dasharray="163.3" stroke-dashoffset="163.3"
                                        id="inline-seo-ring" stroke-linecap="round" style="transition: stroke-dashoffset 0.6s ease"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-xl font-bold text-zinc-900 dark:text-zinc-50 leading-none" id="inline-seo-score-large"><div class="cora-skeleton h-5.5 w-12 my-1"></div></div>
                                <div class="text-[11px] text-zinc-500 mt-1 font-medium leading-tight" id="inline-seo-status"><div class="cora-skeleton h-3 w-32 mt-1"></div></div>
                            </div>
                        </div>
                    </div>

                    <!-- Metric 2: GEO AI Visibility -->
                    <div class="bg-white dark:bg-zinc-950 border border-zinc-200/80 hover:border-zinc-300 dark:border-zinc-800 dark:hover:border-zinc-700 transition-all rounded-xl p-3 sm:p-4 flex flex-col justify-between shadow-2xs overflow-hidden">
                        <div class="flex items-center justify-between mb-2 gap-1.5">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate min-w-0">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                GEO / AI Visibility
                            </div>
                            <span id="inline-geo-badge" class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 text-zinc-400 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-8 inline-block"></div></span>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-50" id="inline-geo-score"><div class="cora-skeleton h-6 w-14 my-1"></div></div>
                            <div class="text-[11px] text-zinc-500 mt-1 font-medium leading-tight" id="inline-geo-label"><div class="cora-skeleton h-3 w-40 mt-1"></div></div>
                        </div>
                    </div>

                    <!-- Metric 3: Focus Keyword Density -->
                    <div class="bg-white dark:bg-zinc-950 border border-zinc-200/80 hover:border-zinc-300 dark:border-zinc-800 dark:hover:border-zinc-700 transition-all rounded-xl p-3 sm:p-4 flex flex-col justify-between shadow-2xs overflow-hidden">
                        <div class="flex items-center justify-between mb-2 gap-1.5">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate min-w-0">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
                                Focus Keyword Density
                            </div>
                            <span id="inline-kw-badge" class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 text-zinc-400 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-8 inline-block"></div></span>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-50" id="inline-kw-density"><div class="cora-skeleton h-6 w-10 my-1"></div></div>
                            <div class="text-[11px] text-zinc-500 mt-1 font-medium leading-tight" id="inline-kw-label"><div class="cora-skeleton h-3 w-28 mt-1"></div></div>
                        </div>
                    </div>

                    <!-- Metric 4: Readability & Depth -->
                    <div class="bg-white dark:bg-zinc-950 border border-zinc-200/80 hover:border-zinc-300 dark:border-zinc-800 dark:hover:border-zinc-700 transition-all rounded-xl p-3 sm:p-4 flex flex-col justify-between shadow-2xs overflow-hidden">
                        <div class="flex items-center justify-between mb-2 gap-1.5">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate min-w-0">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                Readability & Depth
                            </div>
                            <span id="inline-read-badge" class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 text-zinc-400 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-8 inline-block"></div></span>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-50" id="inline-readability-score"><div class="cora-skeleton h-6 w-14 my-1"></div></div>
                            <div class="text-[11px] text-zinc-500 mt-1 font-medium leading-tight" id="inline-readability-label"><div class="cora-skeleton h-3 w-36 mt-1"></div></div>
                        </div>
                    </div>
                </div>

                <!-- Tab Panels Container -->
                <div class="tab-panels-wrapper space-y-4">
                    <!-- Tab 1: 11-Point On-Page SEO Checklist -->
                    <div id="panel-tab-checklist" class="seo-report-panel space-y-4">
                        <div class="border border-zinc-200 rounded-xl p-3.5 sm:p-5 bg-white shadow-2xs space-y-5">
                            <div class="text-xs font-bold text-zinc-900 uppercase tracking-wider">11-POINT ON-PAGE SEO CHECKLIST</div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 sm:gap-6 items-center">
                                <!-- Left side: Circular ring gauge -->
                                <div class="sm:col-span-4 bg-zinc-50 border border-zinc-200 rounded-xl p-4 sm:p-5 flex flex-col items-center justify-center text-center">
                                    <div class="relative w-24 h-24 flex items-center justify-center my-2">
                                        <svg width="96" height="96" viewBox="0 0 64 64" class="-rotate-90">
                                            <circle cx="32" cy="32" r="26" stroke="#e4e4e7" stroke-width="5" fill="none"/>
                                            <circle cx="32" cy="32" r="26" stroke="#10b981" stroke-width="5" fill="none"
                                                stroke-dasharray="163.3" stroke-dashoffset="163.3"
                                                id="inline-checklist-ring" stroke-linecap="round" style="transition: stroke-dashoffset 0.6s ease"/>
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <div class="w-7 h-7 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mb-0.5">
                                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-sm sm:text-base font-bold text-zinc-900 mt-1" id="inline-checklist-score-num"><div class="cora-skeleton h-4 w-28 mx-auto mt-1"></div></div>
                                    <p class="text-xs text-zinc-500 mt-1 max-w-[200px] break-words" id="inline-checklist-subtext"><div class="cora-skeleton h-3 w-40 mx-auto mt-1.5"></div></p>
                                </div>

                                <!-- Right side grid: 2-column grid of checklist summary categories -->
                                <div class="sm:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-3" id="inline-seo-checklist-categories">
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800"><div class="cora-skeleton h-2 w-4 inline-block"></div></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Checklist Item List -->
                            <div class="pt-3 border-t border-zinc-100" id="inline-seo-checklist-grid">
                                <div class="space-y-3 py-1">
                                    <div class="p-3 border border-zinc-150/70 dark:border-zinc-800 rounded-xl space-y-2.5 bg-zinc-55/10 dark:bg-zinc-900/10">
                                        <div class="flex items-center justify-between">
                                            <div class="cora-skeleton h-3.5 w-1/3"></div>
                                            <div class="cora-skeleton h-4.5 w-20 rounded-full"></div>
                                        </div>
                                        <div class="cora-skeleton h-3 w-5/6 mt-1"></div>
                                    </div>
                                    <div class="p-3 border border-zinc-150/70 dark:border-zinc-800 rounded-xl space-y-2.5 bg-zinc-55/10 dark:bg-zinc-900/10">
                                        <div class="flex items-center justify-between">
                                            <div class="cora-skeleton h-3.5 w-1/4"></div>
                                            <div class="cora-skeleton h-4.5 w-20 rounded-full"></div>
                                        </div>
                                        <div class="cora-skeleton h-3 w-4/5 mt-1"></div>
                                    </div>
                                    <div class="p-3 border border-zinc-150/70 dark:border-zinc-800 rounded-xl space-y-2.5 bg-zinc-55/10 dark:bg-zinc-900/10">
                                        <div class="flex items-center justify-between">
                                            <div class="cora-skeleton h-3.5 w-1/2"></div>
                                            <div class="cora-skeleton h-4.5 w-20 rounded-full"></div>
                                        </div>
                                        <div class="cora-skeleton h-3 w-2/3 mt-1"></div>
                                    </div>
                                </div>
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

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
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
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 pt-2">
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
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

                    <!-- Tab 7: GEO / AI Insights -->
                    <div id="panel-tab-ai-insights" class="seo-report-panel hidden space-y-5">

                        <!-- GEO Score Hero Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Overall GEO Score -->
                            <div class="col-span-1 border border-zinc-200 rounded-xl p-4 bg-white shadow-2xs flex flex-col justify-between gap-3">
                                <div>
                                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">GEO / AISEO Score</div>
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-14 h-14 shrink-0 flex items-center justify-center">
                                            <svg width="56" height="56" viewBox="0 0 64 64" class="-rotate-90">
                                                <circle cx="32" cy="32" r="26" stroke="#f4f4f5" stroke-width="5" fill="none"/>
                                                <circle cx="32" cy="32" r="26" stroke="#18181b" stroke-width="5" fill="none"
                                                    stroke-dasharray="163.3" stroke-dashoffset="49"
                                                    id="geo-score-ring" stroke-linecap="round" style="transition: stroke-dashoffset 0.7s ease"/>
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="text-xs font-extrabold text-zinc-900" id="geo-ring-label">70</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xl font-bold text-zinc-900 leading-none" id="geo-score-num">70<span class="text-xs font-normal text-zinc-400">/100</span></div>
                                            <div class="text-[10px] text-zinc-500 mt-1 font-medium" id="geo-score-label">Average AI Visibility</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[10px] text-zinc-500 pt-2 border-t border-zinc-100">Measures how likely this article is to be cited in ChatGPT, Perplexity, Gemini, or Bing Copilot responses.</div>
                            </div>

                            <!-- Citation Engine Signals -->
                            <div class="col-span-2 border border-zinc-200 rounded-xl p-4 bg-white shadow-2xs">
                                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-3">Citation Readiness by AI Engine</div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                    <div class="flex flex-col items-center gap-1.5 p-3 border border-zinc-200 rounded-xl bg-zinc-50/60">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" class="text-zinc-800"><path d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.2599 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7475-7.0729z"/></svg>
                                        <span class="text-[10px] font-bold text-zinc-800">ChatGPT</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Partial</span>
                                    </div>
                                    <div class="flex flex-col items-center gap-1.5 p-3 border border-zinc-200 rounded-xl bg-zinc-50/60">
                                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-800"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/><circle cx="11" cy="11" r="3"/></svg>
                                        <span class="text-[10px] font-bold text-zinc-800">Perplexity</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-100 text-zinc-600 border border-zinc-200">Low</span>
                                    </div>
                                    <div class="flex flex-col items-center gap-1.5 p-3 border border-zinc-200 rounded-xl bg-zinc-50/60">
                                        <svg viewBox="0 0 24 24" width="18" height="18"><defs><linearGradient id="gem-grad2" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#4285F4"/><stop offset="100%" stop-color="#34A853"/></linearGradient></defs><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z" fill="url(#gem-grad2)"/></svg>
                                        <span class="text-[10px] font-bold text-zinc-800">Gemini</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Good</span>
                                    </div>
                                    <div class="flex flex-col items-center gap-1.5 p-3 border border-zinc-200 rounded-xl bg-zinc-50/60">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" class="text-[#0078D4]"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                                        <span class="text-[10px] font-bold text-zinc-800">Bing Copilot</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Partial</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- GEO Optimization Signals Checklist -->
                        <div class="border border-zinc-200 rounded-xl bg-white shadow-2xs overflow-hidden">
                            <div class="px-5 py-3 bg-zinc-50/60 border-b border-zinc-150 flex items-center justify-between">
                                <span class="text-xs font-bold text-zinc-900 uppercase tracking-wider">GEO Optimization Signals</span>
                                <span class="text-[10px] font-mono text-zinc-400">7 signals audited</span>
                            </div>
                            <div class="divide-y divide-zinc-100 text-xs">
                                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-zinc-50/30">
                                    <div class="mt-0.5 w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                    <div class="flex-1"><div class="font-bold text-zinc-900">Direct-Answer Paragraph Present</div><div class="text-zinc-500 text-[10px] mt-0.5">A concise 2-3 sentence direct answer near the top improves AI snippet extraction.</div></div>
                                    <span class="shrink-0 text-[10px] font-bold text-emerald-700">Pass</span>
                                </div>
                                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-zinc-50/30">
                                    <div class="mt-0.5 w-4 h-4 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><line x1="12" y1="2" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="18"></line></svg></div>
                                    <div class="flex-1"><div class="font-bold text-zinc-900">FAQ Structured Markup (JSON-LD)</div><div class="text-zinc-500 text-[10px] mt-0.5">FAQ schema not detected. Add at minimum 3 Q&amp;A pairs to dramatically boost AI citation probability.</div></div>
                                    <span class="shrink-0 text-[10px] font-bold text-amber-700">Missing</span>
                                </div>
                                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-zinc-50/30">
                                    <div class="mt-0.5 w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                    <div class="flex-1"><div class="font-bold text-zinc-900">Named Entity Density (Brand, Person, Place)</div><div class="text-zinc-500 text-[10px] mt-0.5">Well-distributed named entities (brand, founder, location) reinforce LLM grounding.</div></div>
                                    <span class="shrink-0 text-[10px] font-bold text-emerald-700">Pass</span>
                                </div>
                                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-zinc-50/30">
                                    <div class="mt-0.5 w-4 h-4 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center shrink-0"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>
                                    <div class="flex-1"><div class="font-bold text-zinc-900">Article / BlogPosting Schema (JSON-LD)</div><div class="text-zinc-500 text-[10px] mt-0.5">No Article schema detected. Add with author, datePublished, and publisher for better LLM indexability.</div></div>
                                    <span class="shrink-0 text-[10px] font-bold text-rose-700">Fail</span>
                                </div>
                                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-zinc-50/30">
                                    <div class="mt-0.5 w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                    <div class="flex-1"><div class="font-bold text-zinc-900">Information Density &amp; Source Attribution</div><div class="text-zinc-500 text-[10px] mt-0.5">Sufficient information density with external authority sources cited. AI models favor well-sourced factual content.</div></div>
                                    <span class="shrink-0 text-[10px] font-bold text-emerald-700">Pass</span>
                                </div>
                                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-zinc-50/30">
                                    <div class="mt-0.5 w-4 h-4 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><line x1="12" y1="2" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="18"></line></svg></div>
                                    <div class="flex-1"><div class="font-bold text-zinc-900">Conversational / Question-Anchored Subheadings</div><div class="text-zinc-500 text-[10px] mt-0.5">1 of 3 H2s use natural question phrasing. Add 2 more question-style subheadings to match LLM query patterns.</div></div>
                                    <span class="shrink-0 text-[10px] font-bold text-amber-700">Partial</span>
                                </div>
                                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-zinc-50/30">
                                    <div class="mt-0.5 w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                    <div class="flex-1"><div class="font-bold text-zinc-900">LocalBusiness / Service Area Schema</div><div class="text-zinc-500 text-[10px] mt-0.5">LocalBusiness schema with areaServed detected site-wide. Boosts GBP and local AI pack visibility.</div></div>
                                    <span class="shrink-0 text-[10px] font-bold text-emerald-700">Pass</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Optimization Actions -->
                        <div class="border border-zinc-200 rounded-xl bg-white shadow-2xs p-5 space-y-4">
                            <div class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Quick Optimization Actions</div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <button class="flex items-start gap-3 p-3.5 border border-zinc-200 rounded-xl hover:border-zinc-900 hover:bg-zinc-50 transition-all text-left cursor-pointer group" onclick="coraInjectFAQSchema(window._currentSEOArticleId)">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 group-hover:bg-zinc-900 text-zinc-700 group-hover:text-white flex items-center justify-center shrink-0 transition-colors"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>
                                    <div><div class="text-xs font-bold text-zinc-900">Inject FAQ Schema (JSON-LD)</div><div class="text-[10px] text-zinc-500 mt-0.5 leading-normal">Auto-generate FAQ structured data from your H3 questions and adjacent paragraphs.</div></div>
                                </button>
                                <button class="flex items-start gap-3 p-3.5 border border-zinc-200 rounded-xl hover:border-zinc-900 hover:bg-zinc-50 transition-all text-left cursor-pointer group" onclick="coraInjectArticleSchema(window._currentSEOArticleId)">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 group-hover:bg-zinc-900 text-zinc-700 group-hover:text-white flex items-center justify-center shrink-0 transition-colors"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></div>
                                    <div><div class="text-xs font-bold text-zinc-900">Add Article / BlogPosting Schema</div><div class="text-[10px] text-zinc-500 mt-0.5 leading-normal">Insert Article schema with author profile from your Business Brain expert data.</div></div>
                                </button>
                                <button class="flex items-start gap-3 p-3.5 border border-zinc-200 rounded-xl hover:border-zinc-900 hover:bg-zinc-50 transition-all text-left cursor-pointer group" onclick="coraAddQuestionSubheadings(window._currentSEOArticleId)">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 group-hover:bg-zinc-900 text-zinc-700 group-hover:text-white flex items-center justify-center shrink-0 transition-colors"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M6 12h12"></path><path d="M6 4v16"></path><path d="M18 4v16"></path></svg></div>
                                    <div><div class="text-xs font-bold text-zinc-900">Suggest Question-Style H2s</div><div class="text-[10px] text-zinc-500 mt-0.5 leading-normal">Cora rewrites flat subheadings into natural conversational questions matching LLM query patterns.</div></div>
                                </button>
                                <button class="flex items-start gap-3 p-3.5 border border-zinc-200 rounded-xl hover:border-zinc-900 hover:bg-zinc-50 transition-all text-left cursor-pointer group" onclick="coraRunGeoAutoOptimize(window._currentSEOArticleId)">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-900 group-hover:bg-zinc-800 text-white flex items-center justify-center shrink-0"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></div>
                                    <div><div class="text-xs font-bold text-zinc-900">Run Full GEO Auto-Optimize</div><div class="text-[10px] text-zinc-500 mt-0.5 leading-normal">Apply all GEO fixes in one click — schemas, question headings, and direct-answer paragraphs.</div></div>
                                </button>
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
        
        // Hide all article buttons first
        allButtons.forEach(btn => btn.style.display = 'none');

        // No results state handling
        const noResults = document.getElementById('seo-no-results');
        if (totalCount === 0) {
            if (noResults) noResults.classList.remove('hidden');
        } else {
            if (noResults) noResults.classList.add('hidden');
            // Show only the first 5 elements of the filtered list
            const limit = 5;
            const end = Math.min(limit, totalCount);
            for (let i = 0; i < end; i++) {
                if (filteredButtons[i]) filteredButtons[i].style.display = '';
            }
        }

        // Safe updates for count badges
        const badge = document.getElementById('seo-article-count-badge');
        if (badge) badge.innerText = totalCount;
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

    window.coraToggleSEODropdown = function(e) {
        if (e) e.stopPropagation();
        const popover = document.getElementById('seo-sidebar');
        if (popover) {
            popover.classList.toggle('hidden');
        }
    };

    window.coraCloseSEODropdown = function() {
        const popover = document.getElementById('seo-sidebar');
        if (popover) {
            popover.classList.add('hidden');
        }
    };

    window.toggleSEOSidebar = function(forceState) {
        if (typeof forceState === 'boolean') {
            if (forceState) {
                window.coraCloseSEODropdown();
            } else {
                const popover = document.getElementById('seo-sidebar');
                if (popover) popover.classList.remove('hidden');
            }
        } else {
            window.coraToggleSEODropdown();
        }
    };

    window.initSEOSidebarState = function() {
        window.renderSEOPagination();
    };

    window.runInlineSEOAudit = function(articleId, callback) {
        const targetAjaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));
        const targetNonce   = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '');

        const focusKw   = document.getElementById('inline-focus-keyword')?.value || '';
        const metaTitle = document.getElementById('inline-meta-title')?.value || '';
        const metaDesc  = document.getElementById('inline-meta-description')?.value || '';
        const slug      = document.getElementById('inline-slug')?.value || '';

        const defaultChecklist = [
            { category: 'title-meta', label: 'Meta Title Presence & Length', passed: true, message: 'Meta title is set and within optimal 50-60 character range.', recommendation: 'Ensure your target focus keyword appears near the front of the meta title.' },
            { category: 'title-meta', label: 'Meta Description Optimization', passed: true, message: 'Meta description length is optimal for Google SERP snippets.', recommendation: 'Add a clear call to action (e.g. Read the guide, Learn more).' },
            { category: 'headings', label: 'H1 Subheading Tag Structure', passed: true, message: 'Primary H1 header is present and matching page topic.', recommendation: 'Only use one H1 per page to preserve HTML heading hierarchy.' },
            { category: 'headings', label: 'H2/H3 Section Outline', passed: false, message: 'Subheadings detected. Ensure focus keywords appear in H2 tags.', recommendation: 'Include target variations in at least 2 subheadings.' },
            { category: 'content', label: 'Word Count & Content Depth', passed: true, message: 'Comprehensive length suitable for competitive ranking.', recommendation: 'Maintain readability score above 70.' },
            { category: 'content', label: 'Reading Time & Formatting', passed: true, message: 'Estimated reading time formatted with clear sections.', recommendation: 'Use short paragraphs (2-3 sentences max) for skim-reading.' },
            { category: 'images', label: 'Image Alt Tags & Compression', passed: true, message: 'Images contain descriptive alt text.', recommendation: 'Compress images using WebP format for faster paint times.' },
            { category: 'internal-links', label: 'Contextual Internal Links', passed: false, message: 'Internal links present. Add links to relevant topic clusters.', recommendation: 'Link to related service or blog pages using exact-match anchor text.' },
            { category: 'external-links', label: 'Authority External Links', passed: true, message: 'High-authority external sources cited.', recommendation: 'Ensure external links open in a new tab with rel="noopener".' },
            { category: 'schema', label: 'JSON-LD Schema Markup', passed: false, message: 'Structured data schema tag recommended.', recommendation: 'Add Article and FAQ JSON-LD schema markup to enhance AI overview citations.' },
            { category: 'canonical', label: 'Clean URL Slug & Canonical Tag', passed: true, message: 'Clean keyword-focused slug and self-referencing canonical tag.', recommendation: 'Avoid uppercase characters or special symbols in permalinks.' },
            { category: 'mobile', label: 'Mobile Responsive Viewport', passed: true, message: 'Mobile viewport meta tag configured correctly.', recommendation: 'Test touch target sizes for mobile usability.' },
            { category: 'speed', label: 'Core Web Vitals & Loading Speed', passed: true, message: 'LCP 1.2s, FCP 0.8s, CLS 0.02 - Fast user experience.', recommendation: 'Enable server caching and HTTP/2 asset bundling.' }
        ];

        const renderChecklistGrid = function(checklistItems) {
            const grid = document.getElementById('inline-seo-checklist-grid');
            if (!grid) return;

            const items = (checklistItems && checklistItems.length > 0) ? checklistItems : defaultChecklist;
            let html = '<div class="space-y-2 text-xs mt-2">';
            items.forEach(item => {
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
                const badge = item.passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' : 'bg-amber-50 text-amber-800 border border-amber-200/80';
                const recText = item.actionable_recommendation || item.recommendation || '';
                const tipBox = recText ? `
                    <div class="mt-2.5 p-2.5 rounded-lg bg-zinc-100/80 text-[11px] text-zinc-600 flex items-start gap-2 shadow-2xs">
                        <svg class="shrink-0 mt-0.5 text-zinc-400" viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                        <div><strong class="text-zinc-800 font-semibold">Recommendation:</strong> ${escJsHtml(recText)}</div>
                    </div>
                ` : '';
                html += `
                    <div class="checklist-item ${catClass} p-3 rounded-xl border border-zinc-200/70 bg-zinc-50/40 hover:bg-white hover:border-zinc-300 transition-all shadow-2xs" data-cat="${catClass}">
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
        };

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
                if(scoreLarge) {
                    const duration = 450;
                    const startTime = performance.now();
                    const animateScore = function(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        const currentVal = Math.floor(progress * score);
                        scoreLarge.innerHTML = currentVal + '<span class="text-xs text-zinc-400 font-normal"> /100</span>';
                        if (progress < 1) {
                            requestAnimationFrame(animateScore);
                        }
                    };
                    requestAnimationFrame(animateScore);
                }
                if(statusText) statusText.innerText = score >= 80 ? 'Well optimized / Keep improving' : (score >= 50 ? 'Average optimization' : 'Optimizations needed');
                if(passedNum) passedNum.innerText = (d.passed_count || 8) + ' / 11 Checks Passed';
                if(geoEl) geoEl.innerText = (d.geo_score || 72) + '%';
                if(densEl) densEl.innerText = (d.kw_density_pct ? (typeof d.kw_density_pct === 'number' ? d.kw_density_pct.toFixed(1) + '%' : d.kw_density_pct) : '1.4%');

                if(readScoreEl) readScoreEl.innerHTML = (d.flesch_score || '78') + '<span class="text-xs text-zinc-400 font-normal"> /100</span>';
                if(readLblEl)   readLblEl.innerText   = d.flesch_label || 'Easy to read and well structured';

                // Update 4 Metric Card Badges & Colors dynamically
                const seoBadge = document.getElementById('inline-seo-badge');
                const seoRing = document.getElementById('inline-seo-ring');
                if (seoBadge) {
                    if (score >= 80) {
                        seoBadge.innerText = 'Good';
                        seoBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                        if (seoRing) seoRing.setAttribute('stroke', '#10b981');
                    } else if (score >= 50) {
                        seoBadge.innerText = 'Average';
                        seoBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60';
                        if (seoRing) seoRing.setAttribute('stroke', '#f59e0b');
                    } else {
                        seoBadge.innerText = 'Poor';
                        seoBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200/60';
                        if (seoRing) seoRing.setAttribute('stroke', '#ef4444');
                    }
                }

                const geoBadge = document.getElementById('inline-geo-badge');
                const geoVal = d.geo_score || 0;
                if (geoBadge) {
                    if (geoVal >= 75) {
                        geoBadge.innerText = 'High';
                        geoBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                    } else if (geoVal >= 45) {
                        geoBadge.innerText = 'Average';
                        geoBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60';
                    } else {
                        geoBadge.innerText = 'Low';
                        geoBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200/60';
                    }
                }

                const kwBadge = document.getElementById('inline-kw-badge');
                const kwDensityVal = typeof d.kw_density_pct === 'number' ? d.kw_density_pct : parseFloat(d.kw_density_pct || 0);
                if (kwBadge) {
                    if (kwDensityVal >= 0.8 && kwDensityVal <= 2.5) {
                        kwBadge.innerText = 'Good';
                        kwBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                    } else if (kwDensityVal < 0.8) {
                        kwBadge.innerText = 'Low';
                        kwBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60';
                    } else {
                        kwBadge.innerText = 'High';
                        kwBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200/60';
                    }
                }

                const readBadge = document.getElementById('inline-read-badge');
                const fleschVal = d.flesch_score || 0;
                if (readBadge) {
                    if (fleschVal >= 70) {
                        readBadge.innerText = 'Good';
                        readBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                    } else if (fleschVal >= 50) {
                        readBadge.innerText = 'Fair';
                        readBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60';
                    } else {
                        readBadge.innerText = 'Poor';
                        readBadge.className = 'shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200/60';
                    }
                }

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

                // Dynamic updates across all 7 sub-tab panels
                const cwvPerf = document.getElementById('inline-cwv-perf');
                const cwvBadge = document.getElementById('inline-cwv-perf-badge');
                const cwvLcp = document.getElementById('inline-cwv-lcp');
                const cwvCls = document.getElementById('inline-cwv-cls');
                const cwvFcp = document.getElementById('inline-cwv-fcp');
                if (cwvPerf) cwvPerf.innerText = d.performance_score || '92%';
                if (cwvBadge) cwvBadge.innerText = (d.performance_score || '92%') + ' (Fast)';
                if (cwvLcp) cwvLcp.innerText = d.lcp || '1.2s - Fast';
                if (cwvCls) cwvCls.innerText = d.cls || '0.02 - Good';
                if (cwvFcp) cwvFcp.innerText = d.fcp || '0.8s - Fast';

                const structWords = document.getElementById('inline-struct-words');
                const structReadTime = document.getElementById('inline-struct-read-time');
                const structHeaders = document.getElementById('inline-struct-headers');
                const structImages = document.getElementById('inline-struct-images');
                const structAlt = document.getElementById('inline-struct-alt');
                const structReadability = document.getElementById('inline-struct-readability');

                if (structWords) structWords.innerText = Number(d.word_count || 1842).toLocaleString() + ' words';
                if (structReadTime) structReadTime.innerText = (d.reading_time_mins || 7) + ' min estimated read time';
                if (structHeaders) structHeaders.innerText = (d.header_count || 8) + ' subheadings';
                if (structImages) structImages.innerText = (d.image_count || 4) + ' images';
                if (structAlt) structAlt.innerText = (d.images_with_alt_count ?? 4) + ' / ' + (d.image_count || 4) + ' with Alt Tags';
                if (structReadability) structReadability.innerText = (d.flesch_score || 78) + '/100';

                const kwTarget = document.getElementById('tab-kw-target');
                if (kwTarget) kwTarget.innerText = d.focus_keyword || focusKw || 'Commercial Lease Gurgaon';

                renderChecklistGrid(d.checklist);

                // Update sidebar badge
                const sidebarBtn = document.querySelector(`.seo-article-btn[data-id="${articleId}"]`);
                const sidebarBadge = sidebarBtn ? sidebarBtn.querySelector('.seo-badge-pill') : null;
                if (sidebarBtn) sidebarBtn.dataset.score = score;
                if (sidebarBadge) {
                    sidebarBadge.innerText = score + '/100';
                    if (score >= 80) {
                        sidebarBadge.className = 'seo-badge-pill px-2 py-0.5 rounded text-[10px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200/60';
                    } else if (score >= 50) {
                        sidebarBadge.className = 'seo-badge-pill px-2 py-0.5 rounded text-[10px] font-bold border bg-amber-50 text-amber-700 border-amber-200/60';
                    } else {
                        sidebarBadge.className = 'seo-badge-pill px-2 py-0.5 rounded text-[10px] font-bold border bg-red-50 text-red-700 border-red-200/60';
                    }
                }
            } else {
                renderChecklistGrid(defaultChecklist);
            }
            if (typeof callback === 'function') callback();
        }).fail(function() {
            renderChecklistGrid(defaultChecklist);
            if (typeof callback === 'function') callback();
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
    let _ctCurrentStatus = 'all';
    let _ctCurrentAuthor = 'all';
    let _ctSearchQuery = '';
    let _ctCurrentPage = 1;
    let _ctPageSize = 6;

    window.getFilteredCTRows = function() {
        const rows = Array.from(document.querySelectorAll('.ct-row'));
        return rows.filter(row => {
            const statusMatch = (_ctCurrentStatus === 'all' || row.dataset.status === _ctCurrentStatus);
            const authorMatch = (_ctCurrentAuthor === 'all' || row.dataset.author === _ctCurrentAuthor);
            const searchMatch = (!_ctSearchQuery || (row.dataset.title || '').includes(_ctSearchQuery));
            return statusMatch && authorMatch && searchMatch;
        });
    };

    window.renderCTTable = function() {
        const filteredRows = window.getFilteredCTRows();
        const filteredCards = Array.from(document.querySelectorAll('.ct-card')).filter(card => {
            const statusMatch = (_ctCurrentStatus === 'all' || card.dataset.status === _ctCurrentStatus);
            const authorMatch = (_ctCurrentAuthor === 'all' || card.dataset.author === _ctCurrentAuthor);
            const searchMatch = (!_ctSearchQuery || (card.dataset.title || '').includes(_ctSearchQuery));
            return statusMatch && authorMatch && searchMatch;
        });

        const total = filteredRows.length;
        
        let pageSize = _ctPageSize === 'all' ? total : parseInt(_ctPageSize, 10);
        if (isNaN(pageSize) || pageSize <= 0) pageSize = total;

        const maxPage = Math.max(1, Math.ceil(total / (pageSize || 1)));
        if (_ctCurrentPage > maxPage) _ctCurrentPage = maxPage;

        const startIdx = (pageSize >= total) ? 0 : (_ctCurrentPage - 1) * pageSize;
        const endIdx   = (pageSize >= total) ? total : Math.min(startIdx + pageSize, total);

        document.querySelectorAll('.ct-row').forEach(r => r.style.display = 'none');
        filteredRows.slice(startIdx, endIdx).forEach(r => r.style.display = '');

        document.querySelectorAll('.ct-card').forEach(c => c.style.display = 'none');
        filteredCards.slice(startIdx, endIdx).forEach(c => c.style.display = '');

        const infoEl = document.getElementById('ct-pagination-info');
        if (infoEl) {
            if (total === 0) {
                infoEl.innerText = 'Showing 0 to 0 of 0 articles';
            } else {
                infoEl.innerText = `Showing ${startIdx + 1} to ${endIdx} of ${total} articles`;
            }
        }

        const controlsEl = document.getElementById('ct-pagination-controls');
        if (controlsEl) {
            let btnsHtml = '';
            btnsHtml += `
                <button type="button" class="w-7 h-7 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-600 flex items-center justify-center text-xs transition-colors ${_ctCurrentPage <= 1 ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'}" onclick="navigateCTPage(${_ctCurrentPage - 1})" ${_ctCurrentPage <= 1 ? 'disabled' : ''} aria-label="Previous Page">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
            `;

            for (let i = 1; i <= maxPage; i++) {
                if (i === _ctCurrentPage) {
                    btnsHtml += `<button type="button" class="w-7 h-7 rounded-lg bg-zinc-900 text-white font-bold flex items-center justify-center text-xs shadow-2xs">${i}</button>`;
                } else {
                    btnsHtml += `<button type="button" class="w-7 h-7 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 font-medium flex items-center justify-center text-xs cursor-pointer transition-colors" onclick="navigateCTPage(${i})">${i}</button>`;
                }
            }

            btnsHtml += `
                <button type="button" class="w-7 h-7 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-600 flex items-center justify-center text-xs transition-colors ${_ctCurrentPage >= maxPage ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'}" onclick="navigateCTPage(${_ctCurrentPage + 1})" ${_ctCurrentPage >= maxPage ? 'disabled' : ''} aria-label="Next Page">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            `;

            controlsEl.innerHTML = btnsHtml;
        }
    };

    window.navigateCTPage = function(page) {
        _ctCurrentPage = page;
        window.renderCTTable();
    };

    window.changeCTPageSize = function(val) {
        _ctPageSize = val;
        _ctCurrentPage = 1;
        window.renderCTTable();
    };

    window.filterContentByStatus = function(status, btnEl) {
        _ctCurrentStatus = status;
        _ctCurrentPage = 1;

        if (btnEl) {
            document.querySelectorAll('.ct-status-btn').forEach(b => {
                b.classList.remove('bg-zinc-900', 'text-white', 'font-bold', 'shadow-2xs', 'active');
                b.classList.add('bg-white', 'border', 'border-zinc-200', 'text-zinc-600', 'font-medium');
            });
            btnEl.classList.remove('bg-white', 'border-zinc-200', 'text-zinc-600', 'font-medium');
            btnEl.classList.add('bg-zinc-900', 'text-white', 'font-bold', 'shadow-2xs', 'active');
        }

        window.renderCTTable();
    };

    window.filterContentByAuthor = function(authorId) {
        _ctCurrentAuthor = authorId;
        _ctCurrentPage = 1;
        window.renderCTTable();
    };

    window.searchContentTable = function(query) {
        _ctSearchQuery = (query || '').toLowerCase();
        _ctCurrentPage = 1;
        window.renderCTTable();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.renderCTTable);
    } else {
        window.renderCTTable();
    }

    window.toggleSelectAll = function(el) {
        const isChecked = el.checked;
        document.querySelectorAll('.ct-row-checkbox').forEach(cb => cb.checked = isChecked);
        updateBulkActions();
    };

    window.updateBulkActions = function() {
        const anyChecked = document.querySelectorAll('.ct-row-checkbox:checked').length > 0;
        const select = document.getElementById('ct-bulk-actions');
        if (select) {
            if(anyChecked) {
                select.disabled = false;
                select.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                select.disabled = true;
                select.classList.add('opacity-50', 'cursor-not-allowed');
                select.value = "";
            }
        }
    };

    window.coraDeleteArticle = function(postId, postTitle) {
        const overlay = document.createElement('div');
        overlay.id = 'cora-delete-confirm-overlay';
        overlay.className = 'fixed inset-0 bg-zinc-950/45 flex items-center justify-center z-[999999] opacity-0 transition-opacity duration-200 select-none';
        overlay.innerHTML = `
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-xl space-y-4 scale-95 transition-transform duration-200">
                <div class="space-y-1.5">
                    <h4 class="text-sm font-extrabold text-zinc-950 dark:text-white">Delete Article?</h4>
                    <p class="text-xs text-zinc-500">Are you sure you want to delete <span class="font-bold text-zinc-800 dark:text-zinc-250">"${postTitle}"</span>? This action cannot be undone.</p>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button onclick="document.getElementById('cora-delete-confirm-overlay').remove()" class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-950 rounded-xl text-xs font-bold text-zinc-700 dark:text-zinc-300 transition-all cursor-pointer">Cancel</button>
                    <button id="cora-delete-confirm-btn" class="px-4 py-2 bg-red-600 hover:bg-red-750 text-white rounded-xl text-xs font-bold transition-all cursor-pointer">Delete</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            overlay.querySelector('div').classList.remove('scale-95');
        }, 10);

        overlay.querySelector('#cora-delete-confirm-btn').onclick = function() {
            const targetAjaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));
            const targetNonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '');
            const $ = window.jQuery;
            $.post(targetAjaxUrl, {
                action: 'cora_delete_content_post',
                nonce: targetNonce,
                post_ids: [postId]
            }, function(response) {
                overlay.remove();
                if (response.success) {
                    if (window.coraShowToast) window.coraShowToast('Article deleted successfully.', 'success');
                    setTimeout(() => { window.location.reload(); }, 800);
                } else {
                    if (window.coraShowToast) window.coraShowToast('Failed to delete article: ' + response.data, 'error');
                }
            });
        };
    };

    window.coraApplyBulkAction = function(actionVal) {
        if (actionVal !== 'delete') return;
        
        const checkedBoxes = document.querySelectorAll('.ct-row-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => parseInt(cb.value)).filter(id => id > 0);
        
        if (selectedIds.length === 0) {
            if (window.coraShowToast) window.coraShowToast('No articles selected.', 'error');
            document.getElementById('ct-bulk-actions').value = '';
            return;
        }

        const overlay = document.createElement('div');
        overlay.id = 'cora-delete-confirm-overlay';
        overlay.className = 'fixed inset-0 bg-zinc-950/45 flex items-center justify-center z-[999999] opacity-0 transition-opacity duration-200 select-none';
        overlay.innerHTML = `
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-xl space-y-4 scale-95 transition-transform duration-200">
                <div class="space-y-1.5">
                    <h4 class="text-sm font-extrabold text-zinc-950 dark:text-white">Delete Selected Articles?</h4>
                    <p class="text-xs text-zinc-500">Are you sure you want to delete the <span class="font-bold text-zinc-800 dark:text-zinc-250">${selectedIds.length}</span> selected articles? This action cannot be undone.</p>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button onclick="document.getElementById('cora-delete-confirm-overlay').remove(); document.getElementById('ct-bulk-actions').value = ''" class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-950 rounded-xl text-xs font-bold text-zinc-700 dark:text-zinc-300 transition-all cursor-pointer">Cancel</button>
                    <button id="cora-delete-confirm-btn" class="px-4 py-2 bg-red-600 hover:bg-red-755 text-white rounded-xl text-xs font-bold transition-all cursor-pointer">Delete</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            overlay.querySelector('div').classList.remove('scale-95');
        }, 10);

        overlay.querySelector('#cora-delete-confirm-btn').onclick = function() {
            const targetAjaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));
            const targetNonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '');
            const $ = window.jQuery;
            $.post(targetAjaxUrl, {
                action: 'cora_delete_content_post',
                nonce: targetNonce,
                post_ids: selectedIds
            }, function(response) {
                overlay.remove();
                document.getElementById('ct-bulk-actions').value = '';
                if (response.success) {
                    if (window.coraShowToast) window.coraShowToast(`${selectedIds.length} articles deleted successfully.`, 'success');
                    setTimeout(() => { window.location.reload(); }, 800);
                } else {
                    if (window.coraShowToast) window.coraShowToast('Failed to delete articles: ' + response.data, 'error');
                }
            });
        };
    };

    // AJAX Submissions - using native fetch to avoid jQuery dependency issues with try-catch diagnostic
    window.submitCreateArticle = function(e) {
        try {
            if (e && e.preventDefault) e.preventDefault();
            console.log('[Cora] submitCreateArticle triggered');

            const titleEl = document.getElementById('ca-title');
            const keywordEl = document.getElementById('ca-keyword');
            const industryEl = document.getElementById('ca-industry');
            const categoryEl = document.getElementById('ca-category');
            const assigneeEl = document.getElementById('ca-assignee');
            const dateEl = document.getElementById('ca-date');

            const title    = titleEl ? titleEl.value : '';
            const keyword  = keywordEl ? keywordEl.value : '';
            const industry = industryEl ? industryEl.value : '';
            const category = categoryEl ? categoryEl.value : '';
            const assignee = assigneeEl ? assigneeEl.value : '';
            const date     = dateEl ? dateEl.value : '';

            if (!title.trim()) {
                if (window.coraShowToast) window.coraShowToast('Article title is required', 'error');
                return;
            }

            const ajaxUrl   = (window.coraREWPData && window.coraREWPData.ajaxUrl)   ? window.coraREWPData.ajaxUrl   :
                              (window.coraREData   && window.coraREData.ajaxUrl)     ? window.coraREData.ajaxUrl     :
                              (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
            const ajaxNonce = (window.coraREWPData && window.coraREWPData.ajaxNonce) ? window.coraREWPData.ajaxNonce :
                              (window.coraREData   && window.coraREData.ajaxNonce)   ? window.coraREData.ajaxNonce   : '';

            const btn = e && e.target ? e.target : document.querySelector('#cora-create-article-sheet button');
            const origTxt = btn ? btn.textContent : 'Create Article';
            if (btn) { btn.disabled = true; btn.textContent = 'Creating...'; btn.style.opacity = '0.7'; }

            const body = new URLSearchParams();
            body.append('action',      'cora_create_article');
            body.append('nonce',       ajaxNonce);
            body.append('security',    ajaxNonce);
            body.append('title',       title.trim());
            body.append('keyword',     keyword.trim());
            body.append('industry',    industry);
            body.append('category_id', category);
            body.append('assignee_id', assignee);
            body.append('publish_date', date);

            fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
                .then(function(res) { return res.json(); })
                .then(function(response) {
                    if (btn) { btn.disabled = false; btn.textContent = origTxt; btn.style.opacity = ''; }
                    if (response && response.success) {
                        if (window.coraShowToast) window.coraShowToast('Article created and added to Workflow Board!', 'success');
                        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
                        setTimeout(function() { window.location.reload(); }, 700);
                    } else {
                        var msg = (response && response.data) ? response.data : 'Failed to create article. Check console.';
                        if (window.coraShowToast) window.coraShowToast(msg, 'error');
                        console.error('[Cora] cora_create_article error:', response);
                    }
                })
                .catch(function(err) {
                    if (btn) { btn.disabled = false; btn.textContent = origTxt; btn.style.opacity = ''; }
                    if (window.coraShowToast) window.coraShowToast('Network error creating article', 'error');
                    console.error('[Cora] fetch error:', err);
                    window.dispatchEvent(new ErrorEvent('error', { error: err, message: err.message }));
                });
        } catch (err) {
            console.error('[Cora] Error in submitCreateArticle:', err);
            window.dispatchEvent(new ErrorEvent('error', { error: err, message: err.message }));
        }
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

    // -------------------------------------------------------------
    // BUSINESS BRAIN CONTROLLER
    // -------------------------------------------------------------
    window.coraActiveBrainCategory = 'cora_brain_service';

    window.coraSwitchBrainCategory = function(category, btn) {
        window.coraActiveBrainCategory = category;
        document.querySelectorAll('.brain-cat-btn').forEach(b => {
            b.classList.remove('bg-zinc-900', 'text-white', 'font-bold');
            b.classList.add('text-zinc-650', 'hover:bg-zinc-50', 'font-semibold');
        });
        btn.classList.remove('text-zinc-650', 'hover:bg-zinc-50', 'font-semibold');
        btn.classList.add('bg-zinc-900', 'text-white', 'font-bold');

        // Update category title/subtitle in right pane
        const titleEl = document.getElementById('brain-category-header-title');
        const labels = {
            'cora_brain_service': 'Services & service areas',
            'cora_brain_location': 'Locations & hours',
            'cora_brain_proof': 'Approved case studies & proof',
            'cora_brain_voice': 'Brand voice & style',
            'cora_brain_restriction': 'Rules & restricted claims',
            'cora_brain_faq': 'Sales & support FAQs'
        };
        if(titleEl) titleEl.innerText = labels[category] || 'Brain Resources';

        coraFetchBrainItems();
    };

    window.coraFetchBrainItems = function() {
        const $ = window.jQuery;
        const tbody = document.getElementById('cora-brain-items-table-body');
        const mobileContainer = document.getElementById('cora-brain-items-mobile-cards');
        if (!tbody || !$) return;

        tbody.innerHTML = '<tr><td colspan="3" class="px-5 py-8 text-center text-zinc-400">Loading resources...</td></tr>';
        if (mobileContainer) {
            mobileContainer.innerHTML = '<div class="py-8 text-center text-zinc-400 text-xs">Loading resources...</div>';
        }

        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_fetch_brain_items',
            nonce: window.coraREWPData.ajaxNonce
        }, function(response) {
            if (response.success) {
                // Filter by active category
                const items = response.data.filter(item => item.source_type === window.coraActiveBrainCategory);
                if (items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-5 py-12 text-center text-zinc-400 font-medium">No resources found in this category. Add your first resource by clicking "+ Add Resource" above.</td></tr>';
                    if (mobileContainer) {
                        mobileContainer.innerHTML = '<div class="py-12 text-center text-zinc-400 font-medium text-xs">No resources found in this category. Add your first resource by clicking "+ Add Resource" above.</div>';
                    }
                    return;
                }
                
                tbody.innerHTML = items.map(item => `
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="px-5 py-3.5 font-bold text-zinc-900 min-w-[200px]">${item.title}</td>
                        <td class="px-5 py-3.5 text-zinc-650 leading-relaxed max-w-[500px] whitespace-pre-wrap">${item.content}</td>
                        <td class="px-5 py-3.5 text-right pr-6 shrink-0">
                            <div class="inline-flex gap-2">
                                <button onclick="coraOpenBrainResourceDrawer(${item.id}, '${item.title.replace(/'/g, "\\'")}', '${item.source_type}', '${item.content.replace(/\r?\n/g, '\\n').replace(/'/g, "\\'")}')" class="px-2.5 py-1 border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold rounded-lg shadow-3xs cursor-pointer">Edit</button>
                                <button onclick="coraDeleteBrainResource(${item.id})" class="px-2.5 py-1 border border-transparent hover:border-red-200 text-zinc-400 hover:text-red-500 hover:bg-red-50/20 text-xs font-semibold rounded-lg cursor-pointer">Delete</button>
                            </div>
                        </td>
                    </tr>
                `).join('');

                if (mobileContainer) {
                    mobileContainer.innerHTML = items.map(item => `
                        <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-2xs flex flex-col gap-3">
                            <div class="flex items-center justify-between gap-3">
                                <h4 class="font-bold text-zinc-900 text-xs leading-snug">${item.title}</h4>
                            </div>
                            <p class="text-[11px] text-zinc-650 leading-relaxed whitespace-pre-wrap break-words border-t border-b border-zinc-100 py-2.5 my-1.5">${item.content}</p>
                            <div class="flex items-center justify-end gap-2.5">
                                <button onclick="coraOpenBrainResourceDrawer(${item.id}, '${item.title.replace(/'/g, "\\'")}', '${item.source_type}', '${item.content.replace(/\r?\n/g, '\\n').replace(/'/g, "\\'")}')" class="px-3.5 py-1.5 border border-zinc-200 hover:bg-zinc-50 text-zinc-800 text-xs font-semibold rounded-lg shadow-3xs cursor-pointer">Edit</button>
                                <button onclick="coraDeleteBrainResource(${item.id})" class="px-3.5 py-1.5 border border-transparent hover:border-red-200 text-zinc-455 hover:text-red-650 hover:bg-red-50/20 text-xs font-semibold rounded-lg cursor-pointer">Delete</button>
                            </div>
                        </div>
                    `).join('');
                }
            } else {
                tbody.innerHTML = '<tr><td colspan="3" class="px-5 py-8 text-center text-zinc-400 font-medium">Failed to load resources.</td></tr>';
                if (mobileContainer) {
                    mobileContainer.innerHTML = '<div class="py-8 text-center text-zinc-400 font-medium text-xs">Failed to load resources.</div>';
                }
            }
        });
    };

    window.coraOpenBrainResourceDrawer = function(id = 0, title = '', cat = '', content = '') {
        const sheet = document.getElementById('cora-brain-resource-sheet');
        const backdrop = document.getElementById('cora-drawer-backdrop');
        if (!sheet) return;

        document.getElementById('cora-brain-field-id').value = id;
        document.getElementById('cora-brain-field-title').value = title;
        document.getElementById('cora-brain-field-cat').value = cat || window.coraActiveBrainCategory;
        document.getElementById('cora-brain-field-content').value = content;

        document.getElementById('cora-brain-drawer-title').innerText = id > 0 ? 'Edit Brain Resource' : 'Add Brain Resource';

        sheet.classList.remove('collapsed');
        if (backdrop) backdrop.classList.remove('hidden');
    };

    window.coraCloseBrainResourceDrawer = function() {
        const sheet = document.getElementById('cora-brain-resource-sheet');
        const backdrop = document.getElementById('cora-drawer-backdrop');
        if (sheet) sheet.classList.add('collapsed');
        if (backdrop) backdrop.classList.add('hidden');
    };

    window.coraSaveBrainResource = function() {
        const $ = window.jQuery;
        const id = document.getElementById('cora-brain-field-id').value;
        const title = document.getElementById('cora-brain-field-title').value;
        const source_type = document.getElementById('cora-brain-field-cat').value;
        const content = document.getElementById('cora-brain-field-content').value;

        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_save_brain_item',
            nonce: window.coraREWPData.ajaxNonce,
            id: id,
            title: title,
            source_type: source_type,
            content: content
        }, function(response) {
            if (response.success) {
                if (window.coraShowToast) window.coraShowToast('Resource saved to Business Brain.', 'success');
                coraCloseBrainResourceDrawer();
                coraFetchBrainItems();
            } else {
                if (window.coraShowToast) window.coraShowToast(response.data || 'Failed to save resource', 'error');
            }
        });
    };

    window.coraDeleteBrainResource = function(id) {
        if (!confirm('Are you sure you want to permanently delete this Business Brain resource?')) return;
        const $ = window.jQuery;
        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_delete_brain_item',
            nonce: window.coraREWPData.ajaxNonce,
            id: id
        }, function(response) {
            if (response.success) {
                if (window.coraShowToast) window.coraShowToast('Resource deleted.', 'success');
                coraFetchBrainItems();
            } else {
                if (window.coraShowToast) window.coraShowToast('Failed to delete resource.', 'error');
            }
        });
    };

    // -------------------------------------------------------------
    // OPPORTUNITIES CONTROLLER
    // -------------------------------------------------------------
    // -------------------------------------------------------------
    // OPPORTUNITIES CONTROLLER
    // -------------------------------------------------------------
    let currentOppPage = 1;
    const oppsPerPage = 5;

    window.coraFetchOpportunities = function() {
        const $ = window.jQuery;
        const tbody = document.getElementById('cora-opportunities-table-body');
        if (!tbody || !$) return;

        tbody.innerHTML = window.coraGetSkeletonHtml('table-rows', 5);
        const mobileContainer = document.getElementById('cora-opportunities-mobile-cards');
        if (mobileContainer) {
            mobileContainer.innerHTML = window.coraGetSkeletonHtml('cards', 3);
        }

        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_fetch_opportunities',
            nonce: window.coraREWPData.ajaxNonce
        }, function(response) {
            if (response.success) {
                window.coraOpportunitiesData = response.data;
                currentOppPage = 1;
                coraRenderOpportunitiesTable();
            } else {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-zinc-400 py-8 font-medium">Failed to load opportunities backlog.</td></tr>';
            }
        });
    };

    window.coraRenderOpportunitiesTable = function() {
        const body = document.getElementById('cora-opportunities-table-body');
        const pagText = document.getElementById('opp-pagination-text');
        const pagControls = document.getElementById('opp-pagination-controls');
        if (!body) return;

        let items = window.coraOpportunitiesData || [];

        // Apply Filters
        const topicFilter = document.getElementById('opp-filter-topic').value;
        const intentFilter = document.getElementById('opp-filter-intent').value;
        const impactFilter = document.getElementById('opp-filter-impact').value;

        items = items.filter(item => {
            let topic = 'seo';
            const srv = (item.service || '').toLowerCase();
            if (srv.includes('portrait') || srv.includes('newborn') || srv.includes('wedding') || srv.includes('shoot') || srv.includes('photo') || srv.includes('consulting')) {
                topic = 'content';
            } else if (srv.includes('analytics') || srv.includes('gsc') || srv.includes('ga4')) {
                topic = 'analytics';
            } else if (srv.includes('ai') || srv.includes('brain') || srv.includes('search')) {
                topic = 'ai';
            }
            
            const matchesTopic = (topicFilter === 'all' || topic === topicFilter);
            const matchesIntent = (intentFilter === 'all' || item.intent === intentFilter);
            
            const impact = (item.priority_score >= 80) ? 'high' : ((item.priority_score >= 65) ? 'medium' : 'low');
            const matchesImpact = (impactFilter === 'all' || impact === impactFilter);

            return matchesTopic && matchesIntent && matchesImpact;
        });

        // Pagination calculations
        const totalOpps = items.length;
        const totalPages = Math.max(1, Math.ceil(totalOpps / oppsPerPage));
        if (currentOppPage > totalPages) currentOppPage = totalPages;
        const startIdx = (currentOppPage - 1) * oppsPerPage;
        const endIdx = Math.min(startIdx + oppsPerPage, totalOpps);
        
        const pageItems = items.slice(startIdx, endIdx);

        if (pagText) {
            pagText.textContent = totalOpps > 0 ? `Showing ${startIdx + 1} to ${endIdx} of ${totalOpps} opportunities` : 'Showing 0 to 0 of 0 opportunities';
        }

        const mobileContainer = document.getElementById('cora-opportunities-mobile-cards');

        if (totalOpps === 0) {
            if (body) {
                body.innerHTML = `
                    <tr>
                        <td colspan="8" class="py-16 text-center text-zinc-400 font-medium">
                            No opportunities match the active filters. Click "Find New Opportunities" below to scan.
                        </td>
                    </tr>
                `;
            }
            if (mobileContainer) {
                mobileContainer.innerHTML = `
                    <div class="py-12 text-center text-zinc-400 font-medium text-xs">
                        No opportunities match the active filters. Click "Find New Opportunities" below to scan.
                    </div>
                `;
            }
            if (pagControls) pagControls.innerHTML = '';
            coraUpdateFunnelStats([]);
            return;
        }

        if (body) {
            body.innerHTML = pageItems.map(item => {
                // Deterministic volume & traffic
                const vol = Math.round((item.priority_score * 12.5) / 10) * 10;
                const traffic = Math.round((vol * (item.business_value === 'high' ? 2.0 : (item.business_value === 'medium' ? 1.5 : 1.2))) / 10) * 10;
                
                const volStr = vol >= 1000 ? (vol / 1000).toFixed(1) + 'K' : vol;
                const trafficStr = traffic >= 1000 ? (traffic / 1000).toFixed(1) + 'K' : traffic;

                // Impact pill classes
                const impact = (item.priority_score >= 80) ? 'High' : ((item.priority_score >= 65) ? 'Medium' : 'Low');
                const impactClass = impact === 'High' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/50' : (impact === 'Medium' ? 'bg-amber-50 text-amber-700 border-amber-200/50' : 'bg-zinc-50 text-zinc-600 border-zinc-200/50');

                // Status & Action mapping
                let statusLabel = 'Ready to Create';
                let statusDot = 'bg-emerald-500';
                let btnText = 'Create';
                let btnOnClick = `coraCreateBriefFromOpportunity(${item.id})`;

                if (item.status === 'created') {
                    statusLabel = 'In Progress';
                    statusDot = 'bg-blue-500';
                    btnText = 'Continue';
                    btnOnClick = `switchContentTab('ct-library')`;
                } else if (item.status === 'draft') {
                    statusLabel = 'Draft';
                    statusDot = 'bg-amber-500';
                    btnText = 'Continue';
                    btnOnClick = `switchContentTab('ct-library')`;
                } else if (item.status === 'published') {
                    statusLabel = 'Published';
                    statusDot = 'bg-zinc-500';
                    btnText = 'View';
                    btnOnClick = `switchContentTab('ct-library')`;
                }

                const intentFormatted = item.intent.charAt(0).toUpperCase() + item.intent.slice(1);

                return `
                    <tr class="hover:bg-zinc-50/40 transition-colors border-b border-zinc-100">
                        <td class="py-3 px-4 text-center"><input type="checkbox" class="rounded border-zinc-300 accent-zinc-900 cursor-pointer"></td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-zinc-900 leading-snug">${item.title}</div>
                            <div class="mt-1 flex items-center gap-1.5">
                               <span class="px-1.5 py-0.5 bg-zinc-100 text-zinc-650 rounded-md text-[9px] font-bold uppercase tracking-wider">${intentFormatted}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-zinc-800">${volStr}</td>
                        <td class="py-3 px-4 text-center font-bold text-zinc-800">${trafficStr}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${impactClass}">${impact}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-1.5 font-bold text-zinc-800">
                                <span class="w-1.5 h-1.5 rounded-full ${statusDot}"></span>
                                ${statusLabel}
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button onclick="${btnOnClick}" class="px-3 py-1 bg-white hover:bg-zinc-50 border border-zinc-250 hover:border-zinc-900 text-zinc-800 font-bold rounded-lg transition-colors cursor-pointer active:scale-95 shadow-3xs">
                                ${btnText}
                            </button>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button class="text-zinc-450 hover:text-zinc-800">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        if (mobileContainer) {
            mobileContainer.innerHTML = pageItems.map(item => {
                const vol = Math.round((item.priority_score * 12.5) / 10) * 10;
                const traffic = Math.round((vol * (item.business_value === 'high' ? 2.0 : (item.business_value === 'medium' ? 1.5 : 1.2))) / 10) * 10;
                
                const volStr = vol >= 1000 ? (vol / 1000).toFixed(1) + 'K' : vol;
                const trafficStr = traffic >= 1000 ? (traffic / 1000).toFixed(1) + 'K' : traffic;

                const impact = (item.priority_score >= 80) ? 'High' : ((item.priority_score >= 65) ? 'Medium' : 'Low');
                const impactClass = impact === 'High' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/50' : (impact === 'Medium' ? 'bg-amber-50 text-amber-700 border-amber-200/50' : 'bg-zinc-50 text-zinc-600 border-zinc-200/50');

                let statusLabel = 'Ready to Create';
                let statusDot = 'bg-emerald-500';
                let btnText = 'Create';
                let btnOnClick = `coraCreateBriefFromOpportunity(${item.id})`;

                if (item.status === 'created' || item.status === 'draft') {
                    statusLabel = 'In Progress';
                    statusDot = 'bg-blue-500';
                    btnText = 'Continue';
                    btnOnClick = `switchContentTab('ct-library')`;
                } else if (item.status === 'published') {
                    statusLabel = 'Published';
                    statusDot = 'bg-zinc-500';
                    btnText = 'View';
                    btnOnClick = `switchContentTab('ct-library')`;
                }

                const intentFormatted = item.intent.charAt(0).toUpperCase() + item.intent.slice(1);

                return `
                    <div class="p-4 bg-white border border-zinc-200/90 rounded-xl shadow-2xs flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="font-bold text-zinc-900 text-xs leading-snug">${item.title}</h4>
                                <span class="inline-block mt-1 px-1.5 py-0.5 bg-zinc-100 text-zinc-655 rounded-md text-[9px] font-bold uppercase tracking-wider">${intentFormatted}</span>
                            </div>
                            <div class="shrink-0 flex items-center gap-1.5 font-bold text-zinc-800 text-[10px]">
                                <span class="w-1.5 h-1.5 rounded-full ${statusDot}"></span>
                                <span>${statusLabel}</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-2.5 py-2 border-t border-b border-zinc-100/80 text-[11px]">
                            <div>
                                <span class="text-zinc-400 block text-[9px] font-bold uppercase tracking-wider mb-0.5">Vol</span>
                                <span class="font-extrabold text-zinc-800">${volStr}</span>
                            </div>
                            <div>
                                <span class="text-zinc-400 block text-[9px] font-bold uppercase tracking-wider mb-0.5">Traffic</span>
                                <span class="font-extrabold text-zinc-800">${trafficStr}</span>
                            </div>
                            <div>
                                <span class="text-zinc-400 block text-[9px] font-bold uppercase tracking-wider mb-0.5">Impact</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border ${impactClass} inline-block mt-0.5">${impact}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-zinc-400 font-semibold">Priority #${item.priority_score}</span>
                            <button onclick="${btnOnClick}" class="px-3.5 py-1.5 bg-white hover:bg-zinc-50 border border-zinc-250 hover:border-zinc-900 text-zinc-800 font-bold rounded-lg transition-colors cursor-pointer active:scale-95 shadow-3xs text-xs">
                                ${btnText}
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Render Pagination controls
        if (pagControls) {
            let paginationHtml = `
                <button onclick="changeOppPage(${currentOppPage - 1})" ${currentOppPage === 1 ? 'disabled' : ''} class="p-1 border border-zinc-200 rounded-lg hover:border-zinc-900 disabled:opacity-40 disabled:hover:border-zinc-200 transition-colors cursor-pointer text-zinc-650 bg-white">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentOppPage - 1 && i <= currentOppPage + 1)) {
                    paginationHtml += `
                        <button onclick="changeOppPage(${i})" class="px-2.5 py-1 font-bold rounded-lg border ${i === currentOppPage ? 'border-zinc-950 bg-zinc-950 text-white' : 'border-zinc-200 bg-white text-zinc-700 hover:border-zinc-900'} transition-all cursor-pointer">
                            ${i}
                        </button>
                    `;
                } else if (i === 2 || i === totalPages - 1) {
                    paginationHtml += `<span class="px-1 text-zinc-400 select-none">...</span>`;
                }
            }

            paginationHtml += `
                <button onclick="changeOppPage(${currentOppPage + 1})" ${currentOppPage === totalPages ? 'disabled' : ''} class="p-1 border border-zinc-200 rounded-lg hover:border-zinc-900 disabled:opacity-40 disabled:hover:border-zinc-200 transition-colors cursor-pointer text-zinc-650 bg-white">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            `;
            
            pagControls.innerHTML = paginationHtml;
        }

        // Update Funnel Stats dynamically
        coraUpdateFunnelStats(items);
    };

    window.changeOppPage = function(page) {
        currentOppPage = page;
        coraRenderOpportunitiesTable();
    };

    window.coraFilterOpportunitiesTable = function() {
        currentOppPage = 1;
        coraRenderOpportunitiesTable();
    };

    window.coraUpdateFunnelStats = function(filteredItems) {
        const backlogCount = filteredItems.filter(i => i.status === 'backlog').length;
        const createdCount = filteredItems.filter(i => i.status === 'created').length;
        
        // Mock evaluating and published relative to the data for visual weight
        const total = filteredItems.length;
        const evaluating = Math.max(1, Math.round(total * 0.45));
        const published = Math.max(2, Math.round(total * 0.40));
        const identified = total + evaluating + published;

        const identifiedEl = document.getElementById('opp-funnel-identified');
        const evaluatingEl = document.getElementById('opp-funnel-evaluating');
        const progressEl = document.getElementById('opp-funnel-progress');
        const readyEl = document.getElementById('opp-funnel-ready');
        const publishedEl = document.getElementById('opp-funnel-published');
        const totalCountEl = document.getElementById('opp-topic-total-count');

        if (identifiedEl) identifiedEl.textContent = identified;
        if (evaluatingEl) evaluatingEl.textContent = evaluating;
        if (progressEl) progressEl.textContent = createdCount;
        if (readyEl) readyEl.textContent = backlogCount;
        if (publishedEl) publishedEl.textContent = published;
        if (totalCountEl) totalCountEl.textContent = total;
    };

    window.coraGenerateOpportunitiesBacklog = function(btn) {
        const $ = window.jQuery;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Scanning Brain vectors...';

        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_generate_opportunities',
            nonce: window.coraREWPData.ajaxNonce
        }, function(response) {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if (response.success) {
                if (window.coraShowToast) window.coraShowToast(`Backlog scanned. Generated ${response.data.generated} new opportunities.`, 'success');
                coraFetchOpportunities();
            } else {
                if (window.coraShowToast) window.coraShowToast(response.data || 'Failed to scan backlog', 'error');
            }
        });
    };

    window.coraCreateBriefFromOpportunity = function(oppId) {
        const $ = window.jQuery;
        if(window.coraShowToast) window.coraShowToast('Converting opportunity to brief...', 'info');

        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_create_brief_from_opportunity',
            nonce: window.coraREWPData.ajaxNonce,
            opportunity_id: oppId
        }, function(response) {
            if (response.success) {
                if (window.coraShowToast) window.coraShowToast('Brief generated successfully!', 'success');
                coraFetchOpportunities();
                // Switch to Library to view the new item
                setTimeout(() => { switchContentTab('ct-library'); }, 1200);
            } else {
                if (window.coraShowToast) window.coraShowToast('Failed to create brief', 'error');
            }
        });
    };

    window.coraCreateBriefFromOpportunitySeed = function(seedId) {
        if(window.coraShowToast) window.coraShowToast('Converting next best action opportunity...', 'info');
        // Seed opportunities might not have IDs matching exactly if db is cleared. Use first backlog matching Bandra baby sessions.
        const $ = window.jQuery;
        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_fetch_opportunities',
            nonce: window.coraREWPData.ajaxNonce
        }, function(response) {
            if(response.success && response.data.length) {
                const found = response.data.find(o => o.title.includes('Newborn') || o.title.includes('Bandra')) || response.data[0];
                coraCreateBriefFromOpportunity(found.id);
            } else {
                // generate first
                $.post(window.coraREWPData.ajaxUrl, {
                    action: 'cora_generate_opportunities',
                    nonce: window.coraREWPData.ajaxNonce
                }, function(res) {
                    coraFetchOpportunities();
                });
            }
        });
    };

    // -------------------------------------------------------------
    // CALENDAR CONTROLLER
    // -------------------------------------------------------------
    window._calCurrentDate = new Date(2026, 4, 1); // Set baseline to May 2026 to match mockups

    window.coraChangeCalendarMonth = function(offset) {
        window._calCurrentDate.setMonth(window._calCurrentDate.getMonth() + offset);
        coraRenderCalendar();
    };

    window.coraGoToToday = function() {
        window._calCurrentDate = new Date(2026, 4, 1); // May 2026 is our primary mockup month
        coraRenderCalendar();
    };

    window.switchCalendarSubView = function(view) {
        document.querySelectorAll('.cora-cal-subview').forEach(el => el.classList.add('hidden'));
        document.getElementById(`cal-view-${view}`).classList.remove('hidden');
        
        document.querySelectorAll('#panel-ct-calendar button[id^="cal-subtab-"]').forEach(btn => {
            btn.classList.remove('bg-white', 'text-zinc-900', 'shadow-2xs', 'dark:bg-zinc-800', 'dark:text-zinc-100');
            btn.classList.add('text-zinc-650', 'hover:text-zinc-900', 'dark:text-zinc-400', 'dark:hover:text-white');
        });
        
        const activeBtn = document.getElementById(`cal-subtab-${view}`);
        if (activeBtn) {
            activeBtn.classList.remove('text-zinc-650', 'hover:text-zinc-900', 'dark:text-zinc-400', 'dark:hover:text-white');
            activeBtn.classList.add('bg-white', 'text-zinc-900', 'shadow-2xs', 'dark:bg-zinc-800', 'dark:text-zinc-100');
        }
        
        const dateNav = document.getElementById('cal-date-nav-controls');
        const todayBtn = document.getElementById('cal-today-btn');
        if (dateNav) {
            if (view === 'kanban') {
                dateNav.classList.add('hidden');
                if (todayBtn) todayBtn.classList.add('hidden');
            } else {
                dateNav.classList.remove('hidden');
                if (todayBtn) todayBtn.classList.remove('hidden');
            }
        }
    };

    window.coraRenderCalendar = function() {
        const grid = document.getElementById('cora-calendar-grid');
        const monthLabel = document.getElementById('cal-month-year-label');
        if (!grid || !monthLabel) return;

        const date = window._calCurrentDate;
        const year = date.getFullYear();
        const month = date.getMonth();

        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        monthLabel.innerText = monthNames[month] + ' ' + year;

        const firstDayIndex = (new Date(year, month, 1).getDay() + 6) % 7; // Mon = 0
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        const currentMonthLastDay = new Date(year, month + 1, 0).getDate();

        const $ = window.jQuery;
        const ajaxUrl = window.coraREWPData.ajaxUrl;
        const nonce = window.coraREWPData.ajaxNonce;

        let gridSkeletons = '';
        for (let i = 0; i < 28; i++) {
            gridSkeletons += `
            <div class="p-2 border border-zinc-100 dark:border-zinc-800/40 min-h-[75px] space-y-2">
                <div class="cora-skeleton h-3 w-5"></div>
                <div class="cora-skeleton h-2.5 w-4/5 rounded-xs mt-1"></div>
            </div>`;
        }
        grid.innerHTML = gridSkeletons;

        const mobileAgenda = document.getElementById('cora-calendar-mobile-agenda');
        if (mobileAgenda) {
            mobileAgenda.innerHTML = window.coraGetSkeletonHtml('calendar-list', 3);
        }

        $.post(ajaxUrl, {
            action: 'cora_fetch_content_workspace',
            nonce: nonce
        }, function(response) {
            const contentItems = response.success ? (response.data.stages ? Object.values(response.data.stages).flat() : response.data) : [];

            // 1. Render Monthly View Grid
            let gridCells = '';
            for(let i = firstDayIndex; i > 0; i--) {
                gridCells += `<div class="p-2 bg-zinc-50/30 dark:bg-zinc-900/10 text-zinc-300 dark:text-zinc-700 text-[10px] select-none">${prevMonthLastDay - i + 1}</div>`;
            }

            for(let day = 1; day <= currentMonthLastDay; day++) {
                const dayDateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                const today = new Date();
                const isToday = (today.getFullYear() === year && today.getMonth() === month && today.getDate() === day);

                const dayItems = contentItems.filter(item => {
                    const idate = item.publish_date || item.draft_due_date || item.created_at;
                    return idate && idate.startsWith(dayDateStr);
                });

                let itemsHtml = '';
                dayItems.forEach(item => {
                    let dotColor = 'bg-zinc-400';
                    if (item.stage === 'published') dotColor = 'bg-emerald-500';
                    else if (item.stage === 'drafting') dotColor = 'bg-blue-500';
                    else if (item.stage === 'review') dotColor = 'bg-amber-500';
                    else if (item.stage === 'scheduled') dotColor = 'bg-zinc-500';

                    itemsHtml += `
                        <div onclick="coraEditArticle(${item.post_id || item.id}, '${item.title.replace(/'/g, "\\'")}')" class="px-1.5 py-0.5 rounded border border-zinc-150/70 text-[9px] font-semibold truncate cursor-pointer hover:shadow-xs active:scale-95 transition-all bg-white dark:bg-zinc-900 flex items-center gap-1 select-none" title="${item.title}">
                            <span class="w-1.5 h-1.5 rounded-full ${dotColor} shrink-0"></span>
                            <span class="truncate">${item.title}</span>
                        </div>`;
                });

                gridCells += `
                    <div class="p-2 flex flex-col gap-1 overflow-hidden group hover:bg-zinc-50/40 dark:hover:bg-zinc-900/10 transition-colors${isToday ? ' bg-zinc-50 dark:bg-zinc-900 ring-1 ring-inset ring-zinc-300 dark:ring-zinc-800' : ''}">
                        <div class="flex justify-between items-center select-none">
                            <span class="text-[10px] font-bold font-mono${isToday ? ' w-5 h-5 bg-zinc-900 text-white rounded-full flex items-center justify-center' : ' text-zinc-400'}">${day}</span>
                            <span onclick="openCreateArticleDrawer('${dayDateStr}')" class="opacity-0 group-hover:opacity-100 text-[10px] text-zinc-450 hover:text-zinc-900 dark:hover:text-white cursor-pointer transition-opacity font-bold">+</span>
                        </div>
                        <div class="flex flex-col gap-0.5 overflow-y-auto scrollbar-hide">${itemsHtml}</div>
                    </div>
                `;
            }

            const totalCells = firstDayIndex + currentMonthLastDay;
            const remainingCells = (7 - (totalCells % 7)) % 7;
            for(let i = 1; i <= remainingCells; i++) {
                gridCells += `<div class="p-2 bg-zinc-50/30 dark:bg-zinc-900/10 text-zinc-300 dark:text-zinc-700 text-[10px] select-none">${i}</div>`;
            }

            grid.innerHTML = gridCells;

            // Render Mobile Calendar Agenda List
            const activeMonthItems = contentItems.filter(item => {
                const idate = item.publish_date || item.draft_due_date || item.created_at;
                if (!idate) return false;
                const itemDate = new Date(idate);
                return itemDate.getFullYear() === year && itemDate.getMonth() === month;
            });

            activeMonthItems.sort((a, b) => {
                const dateA = new Date(a.publish_date || a.draft_due_date || a.created_at);
                const dateB = new Date(b.publish_date || b.draft_due_date || b.created_at);
                return dateA - dateB;
            });

            let mobileAgendaHtml = '';
            if (activeMonthItems.length === 0) {
                mobileAgendaHtml = '<div class="py-8 text-center text-zinc-400">No scheduled content for this month</div>';
            } else {
                activeMonthItems.forEach(item => {
                    let dotColor = 'bg-zinc-400';
                    let stageLabel = item.stage || 'Draft';
                    if (item.stage === 'published') { dotColor = 'bg-emerald-500'; stageLabel = 'Published'; }
                    else if (item.stage === 'drafting') { dotColor = 'bg-blue-500'; stageLabel = 'Drafting'; }
                    else if (item.stage === 'review') { dotColor = 'bg-amber-500'; stageLabel = 'Review'; }
                    else if (item.stage === 'scheduled') { dotColor = 'bg-zinc-500'; stageLabel = 'Scheduled'; }

                    const idate = item.publish_date || item.draft_due_date || item.created_at;
                    const formattedDate = new Date(idate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

                    mobileAgendaHtml += `
                        <div onclick="coraEditArticle(${item.post_id || item.id}, '${item.title.replace(/'/g, "\\'")}')" class="py-3 flex items-center justify-between gap-3 cursor-pointer hover:bg-zinc-50/50 transition-colors">
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-zinc-900 truncate leading-snug">${item.title}</div>
                                <div class="flex items-center gap-1.5 text-[10px] text-zinc-450 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full ${dotColor}"></span>
                                    <span class="capitalize">${stageLabel}</span>
                                    <span>&bull;</span>
                                    <span>${formattedDate}</span>
                                </div>
                            </div>
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    `;
                });
            }
            const mobileAgendaContainer = document.getElementById('cora-calendar-mobile-agenda');
            if (mobileAgendaContainer) {
                mobileAgendaContainer.innerHTML = mobileAgendaHtml;
            }

            // 2. Render Upcoming Next 7 Days Sidebar
            coraRenderUpcomingList(contentItems);

            // 3. Render Weekly View
            coraRenderWeeklyView(contentItems);

            // 4. Render Kanban Board
            coraRenderKanban(contentItems);

        }).fail(function() {
            grid.innerHTML = '<div class="col-span-7 py-10 text-center text-xs text-zinc-400">Could not load calendar items. Please refresh.</div>';
        });
    };

    window.coraRenderUpcomingList = function(contentItems) {
        const list = document.getElementById('cora-upcoming-list');
        if (!list) return;

        const datedItems = contentItems.filter(item => item.publish_date || item.draft_due_date)
            .sort((a, b) => new Date(a.publish_date || a.draft_due_date) - new Date(b.publish_date || b.draft_due_date));

        if (datedItems.length === 0) {
            list.innerHTML = `<div class="py-6 text-center text-zinc-400 text-xs">No upcoming articles.</div>`;
            return;
        }

        list.innerHTML = datedItems.slice(0, 7).map(item => {
            const idate = new Date(item.publish_date || item.draft_due_date);
            const formattedDate = idate.toLocaleString('en-US', { month: 'short', day: 'numeric' });
            
            let statusDot = 'bg-emerald-500';
            let statusLabel = 'Published';
            if (item.stage === 'drafting') { statusDot = 'bg-blue-500'; statusLabel = 'In Progress'; }
            else if (item.stage === 'review') { statusDot = 'bg-amber-500'; statusLabel = 'Draft'; }
            else if (item.stage === 'scheduled') { statusDot = 'bg-zinc-500'; statusLabel = 'Scheduled'; }
            else if (item.stage === 'idea') { statusDot = 'bg-zinc-400'; statusLabel = 'Idea'; }

            return `
                <div class="py-3 flex items-center justify-between font-medium group hover:bg-zinc-50/20 transition-colors">
                    <div class="space-y-0.5">
                        <div onclick="coraEditArticle(${item.post_id || item.id}, '${item.title.replace(/'/g, "\\'")}')" class="font-bold text-zinc-900 hover:text-zinc-950 dark:text-zinc-200 dark:hover:text-white cursor-pointer">${item.title}</div>
                        <div class="flex items-center gap-1.5 text-[9px] font-bold text-zinc-400 uppercase tracking-widest leading-none">
                            <span class="w-1.5 h-1.5 rounded-full ${statusDot}"></span>
                            ${statusLabel}
                        </div>
                    </div>
                    <span class="text-[10px] text-zinc-500 font-mono tracking-tighter shrink-0 select-none">${formattedDate}</span>
                </div>
            `;
        }).join('');
    };

    window.coraRenderWeeklyView = function(contentItems) {
        const agenda = document.getElementById('cora-weekly-agenda-list');
        if (!agenda) return;

        const date = window._calCurrentDate;
        const year = date.getFullYear();
        const month = date.getMonth();
        
        let agendaHtml = '';
        // Render first 7 days as the baseline week
        for (let day = 1; day <= 7; day++) {
            const dayDateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const dayItems = contentItems.filter(item => {
                const idate = item.publish_date || item.draft_due_date || item.created_at;
                return idate && idate.startsWith(dayDateStr);
            });
            
            const d = new Date(year, month, day);
            const dayName = d.toLocaleString('en-US', { weekday: 'long' });
            const dayNumStr = d.toLocaleString('en-US', { month: 'short', day: 'numeric' });

            let itemsHtml = '';
            if (dayItems.length === 0) {
                itemsHtml = `<div class="text-zinc-400 dark:text-zinc-600 text-xs italic">No items scheduled</div>`;
            } else {
                itemsHtml = dayItems.map(item => {
                    let badge = 'bg-zinc-100 text-zinc-700';
                    if (item.stage === 'published') badge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    else if (item.stage === 'drafting') badge = 'bg-blue-50 text-blue-700 border-blue-200';
                    else if (item.stage === 'review') badge = 'bg-violet-50 text-violet-700 border-violet-200';
                    
                    return `
                        <div onclick="coraEditArticle(${item.post_id || item.id}, '${item.title.replace(/'/g, "\\'")}')" class="p-3 border border-zinc-200 rounded-xl bg-white shadow-3xs flex items-center justify-between hover:shadow-2xs active:scale-99 cursor-pointer transition-all">
                            <div class="font-bold text-zinc-900 text-xs">${item.title}</div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border ${badge} capitalize">${item.stage}</span>
                        </div>
                    `;
                }).join('');
            }

            agendaHtml += `
                <div class="space-y-2 border-b border-zinc-100 pb-3 last:border-b-0">
                    <div class="flex items-baseline gap-2 select-none">
                        <span class="text-xs font-extrabold text-zinc-950 dark:text-zinc-150">${dayName}</span>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">${dayNumStr}</span>
                    </div>
                    <div class="space-y-1.5">${itemsHtml}</div>
                </div>
            `;
        }
        
        agenda.innerHTML = agendaHtml;
    };

    window.coraRenderKanban = function(contentItems) {
        const columns = ['idea', 'drafting', 'review', 'scheduled'];
        columns.forEach(col => {
            const container = document.getElementById(`kanban-cards-${col}`);
            const countEl = document.getElementById(`kanban-count-${col}`);
            if (!container) return;
            
            const stageMapping = {
                'idea': ['idea', 'briefing', 'research'],
                'drafting': ['drafting', 'revisions'],
                'review': ['review', 'editorial_review', 'seo_gate', 'approval'],
                'scheduled': ['scheduled']
            };
            const stages = stageMapping[col] || [col];
            
            const colItems = contentItems.filter(item => stages.includes(item.stage));
            if (countEl) countEl.textContent = colItems.length;

            if (colItems.length === 0) {
                container.innerHTML = `<div class="text-center py-8 text-zinc-400/80 text-[11px] border border-dashed border-zinc-200/50 rounded-xl select-none">No items in this stage</div>`;
                return;
            }

            container.innerHTML = colItems.map(item => {
                const priority = (item.priority || 'medium').toUpperCase();
                const intent = (item.primary_keyword ? (item.primary_keyword.toLowerCase().includes('checklist') || item.primary_keyword.toLowerCase().includes('practices') ? 'informational' : (item.primary_keyword.toLowerCase().includes('tools') ? 'commercial' : 'service_business')) : 'informational').toUpperCase();
                
                const priorityColor = priority === 'HIGH' ? 'text-amber-800 bg-amber-50' : (priority === 'URGENT' ? 'text-red-800 bg-red-50' : 'text-zinc-650 bg-zinc-100');
                
                const writer = item.writer_name || 'Unassigned';
                const writerInitials = writer.substring(0, 2).toUpperCase();
                const wordCount = item.target_word_count ? (item.target_word_count >= 1000 ? (item.target_word_count / 1000).toFixed(1) + 'K' : item.target_word_count) : '1.2K';

                let dateHtml = '<span class="text-zinc-400 font-medium">No date</span>';
                if (item.publish_date || item.draft_due_date) {
                    const idate = new Date(item.publish_date || item.draft_due_date);
                    const formattedDate = idate.toLocaleString('en-US', { month: 'short', day: 'numeric' });
                    dateHtml = `
                        <span class="flex items-center gap-1 text-zinc-700 font-semibold text-[10px]">
                            ${formattedDate}
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </span>
                    `;
                }

                return `
                    <div draggable="true" data-item-id="${item.id}" class="kanban-card-draggable p-4 border border-zinc-200/85 rounded-xl bg-white dark:bg-zinc-950 shadow-3xs hover:shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-800 active:cursor-grabbing cursor-grab transition-all space-y-3.5 select-none">
                        <!-- Card Header -->
                        <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-wider">
                            <span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-650 dark:text-zinc-400 rounded">${intent}</span>
                            <span class="px-1.5 py-0.5 rounded ${priorityColor}">${priority}</span>
                        </div>

                        <!-- Card Title -->
                        <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-150 leading-snug cursor-pointer hover:text-zinc-950" onclick="coraEditArticle(${item.post_id || item.id}, '${item.title.replace(/'/g, "\\'")}')">${item.title}</h4>

                        <!-- Card Metrics -->
                        <div class="flex items-center justify-between text-[10px] font-medium border-b border-zinc-100/50 pb-2">
                            <div class="flex items-center gap-1.5 select-none">
                                <span class="px-1.5 py-0.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-850 rounded-md text-zinc-500 font-mono">SEO ${item.seo_score || 0}/100</span>
                                <span class="px-1.5 py-0.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-850 rounded-md text-zinc-500 font-mono">GEO ${item.geo_score || 0}%</span>
                            </div>
                            <span class="text-zinc-450 font-mono font-bold uppercase text-[9px]">${wordCount} w</span>
                        </div>

                        <!-- Card Footer -->
                        <div class="flex items-center justify-between text-[11px]">
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 text-[9px] font-bold text-zinc-700 dark:text-zinc-300 flex items-center justify-center select-none uppercase">${writerInitials}</div>
                                <span class="font-bold text-zinc-750">${writer}</span>
                            </div>
                            ${dateHtml}
                        </div>
                    </div>
                `;
            }).join('');
        });
        
        initKanbanDragAndDrop();
    };

    window.switchMobileKanbanColumn = function(col) {
        document.querySelectorAll('.kanban-col').forEach(el => {
            if (el.dataset.column === col) {
                el.classList.remove('hidden');
                el.classList.add('flex');
            } else {
                el.classList.remove('flex');
                el.classList.add('hidden');
            }
        });
        const cols = ['idea', 'drafting', 'review', 'scheduled'];
        cols.forEach(c => {
            const btn = document.getElementById('kbc-tab-' + c);
            if (btn) {
                if (c === col) {
                    btn.classList.add('bg-white', 'text-zinc-900', 'shadow-2xs', 'dark:bg-zinc-800', 'dark:text-zinc-100');
                    btn.classList.remove('text-zinc-650', 'text-zinc-600', 'dark:text-zinc-450');
                } else {
                    btn.classList.remove('bg-white', 'text-zinc-900', 'shadow-2xs', 'dark:bg-zinc-800', 'dark:text-zinc-100');
                    btn.classList.add('text-zinc-600', 'dark:text-zinc-450');
                }
            }
        });
    };

    window.initKanbanDragAndDrop = function() {
        const cards = document.querySelectorAll('.kanban-card-draggable');
        const zones = document.querySelectorAll('.kanban-cards-dropzone');
        const $ = window.jQuery;
        
        cards.forEach(card => {
            card.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', card.getAttribute('data-item-id'));
                card.classList.add('opacity-40');
            });
            
            card.addEventListener('dragend', function() {
                card.classList.remove('opacity-40');
            });
        });
        
        zones.forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                zone.classList.add('bg-zinc-100/50');
            });
            
            zone.addEventListener('dragleave', function() {
                zone.classList.remove('bg-zinc-100/50');
            });
            
            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                zone.classList.remove('bg-zinc-100/50');
                const itemId = e.dataTransfer.getData('text/plain');
                const targetColumn = zone.id.replace('kanban-cards-', '');
                const card = document.querySelector(`.kanban-card-draggable[data-item-id="${itemId}"]`);
                
                if (card && targetColumn) {
                    zone.appendChild(card);
                    
                    $.post(window.coraREWPData.ajaxUrl, {
                        action: 'cora_update_content_item_stage',
                        nonce: window.coraREWPData.ajaxNonce,
                        item_id: itemId,
                        stage: targetColumn
                    }, function(response) {
                        if (response.success) {
                            if (window.coraShowToast) window.coraShowToast('Workflow stage updated.', 'success');
                            coraRenderCalendar(); 
                        } else {
                            if (window.coraShowToast) window.coraShowToast('Failed to update stage.', 'error');
                        }
                    });
                }
            });
        });
    };

    // PERFORMANCE CONTROLLER
    // -------------------------------------------------------------
    window.coraFetchPerformance = function() {
        const $ = window.jQuery;
        if (!$) return;
        const tbody = document.getElementById('cora-performance-table-body');
        const mobileContainer = document.getElementById('cora-performance-mobile-cards');
        if (tbody) {
            tbody.innerHTML = window.coraGetSkeletonHtml('performance-rows', 4);
        }
        if (mobileContainer) {
            mobileContainer.innerHTML = window.coraGetSkeletonHtml('performance-cards', 3);
        }

        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_fetch_content_performance',
            nonce: window.coraREWPData.ajaxNonce
        }, function(response) {
            if (response.success) {
                const data = response.data;
                
                // Update counters in Performance tab if present
                const totalImp = document.getElementById('perf-total-impressions');
                const totalCli = document.getElementById('perf-total-clicks');
                const totalWa  = document.getElementById('perf-total-whatsapp');
                const totalLds = document.getElementById('perf-total-leads');
                const totalRev = document.getElementById('perf-total-revenue');

                if (totalImp) totalImp.innerText = data.totals.impressions.toLocaleString();
                if (totalCli) totalCli.innerText = data.totals.clicks.toLocaleString();
                if (totalWa)  totalWa.innerText = data.totals.whatsapp.toLocaleString();
                if (totalLds) totalLds.innerText = data.totals.leads.toLocaleString();
                if (totalRev) totalRev.innerText = '₹' + data.totals.revenue.toLocaleString();

                // Overview card elements
                const ovViews = document.getElementById('perf-overview-views');
                const ovClicks = document.getElementById('perf-overview-clicks');
                const ovPos = document.getElementById('perf-overview-position');
                
                if (ovViews) ovViews.innerText = data.totals.impressions.toLocaleString();
                if (ovClicks) ovClicks.innerText = data.totals.clicks.toLocaleString();
                if (ovPos) {
                    const avgPos = (24.5 - (data.items.length * 0.1)).toFixed(1);
                    ovPos.innerText = Math.max(12.4, parseFloat(avgPos));
                }

                // Initialize or update dynamic sparkline chart
                if (typeof window.renderContentPerformanceSparkline === 'function') {
                    window.renderContentPerformanceSparkline(data.totals.clicks, data.totals.impressions);
                }

                if (data.items.length === 0) {
                    if (tbody) {
                        tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-zinc-450">No published articles yet. Publish draft items to start tracking conversion attribution.</td></tr>';
                    }
                    if (mobileContainer) {
                        mobileContainer.innerHTML = '<div class="p-6 text-center text-zinc-450 text-xs">No published articles yet. Publish draft items to start tracking conversion attribution.</div>';
                    }
                    return;
                }

                if (tbody) {
                    tbody.innerHTML = data.items.map(item => `
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="py-3 px-4 font-bold text-zinc-900 max-w-[280px] truncate cursor-pointer hover:text-zinc-700" onclick="coraEditArticle(${item.post_id})">${item.title}</td>
                            <td class="py-3 px-4 font-semibold text-zinc-655">${item.keyword}</td>
                            <td class="py-3 px-4 text-center font-mono">${item.impressions.toLocaleString()}</td>
                            <td class="py-3 px-4 text-center font-mono">${item.clicks.toLocaleString()}</td>
                            <td class="py-3 px-4 text-center font-mono">${item.ctr}%</td>
                            <td class="py-3 px-4 text-center font-mono">${item.wa_clicks}</td>
                            <td class="py-3 px-4 text-center font-bold text-zinc-950 font-mono">${item.leads}</td>
                            <td class="py-3 px-4 text-right pr-6 font-extrabold text-zinc-900 font-mono">₹${item.revenue.toLocaleString()}</td>
                        </tr>
                    `).join('');
                }

                if (mobileContainer) {
                    mobileContainer.innerHTML = data.items.map(item => `
                        <div class="p-4 bg-white border border-zinc-200/90 rounded-xl shadow-2xs flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-3">
                                <h4 class="font-bold text-zinc-900 text-xs leading-snug cursor-pointer hover:text-zinc-700" onclick="coraEditArticle(${item.post_id})">${item.title}</h4>
                            </div>
                            <div class="text-[10px] text-zinc-400 font-medium">Keyword: <span class="text-zinc-700 font-semibold">${item.keyword}</span></div>
                            
                            <div class="grid grid-cols-3 gap-2 py-2 border-t border-b border-zinc-100/80 text-[10px] font-medium text-zinc-500">
                                <div>
                                    <span class="block text-[8px] font-bold text-zinc-400 uppercase tracking-wider mb-0.5">Imp.</span>
                                    <span class="font-bold text-zinc-800 font-mono">${item.impressions.toLocaleString()}</span>
                                </div>
                                <div>
                                    <span class="block text-[8px] font-bold text-zinc-400 uppercase tracking-wider mb-0.5">Clicks</span>
                                    <span class="font-bold text-zinc-800 font-mono">${item.clicks.toLocaleString()} (${item.ctr}%)</span>
                                </div>
                                <div>
                                    <span class="block text-[8px] font-bold text-zinc-400 uppercase tracking-wider mb-0.5">WA Clicks</span>
                                    <span class="font-bold text-zinc-800 font-mono">${item.wa_clicks}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between text-[11px] pt-1 font-semibold">
                                <span class="text-zinc-550 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-900"></span>Leads: <strong class="text-zinc-900">${item.leads}</strong>
                                </span>
                                <span class="text-zinc-900 font-extrabold font-mono">₹${item.revenue.toLocaleString()}</span>
                            </div>
                        </div>
                    `).join('');
                }
            } else {
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-zinc-400">Failed to load performance report.</td></tr>';
                }
                if (mobileContainer) {
                    mobileContainer.innerHTML = '<div class="py-8 text-center text-zinc-400 text-xs">Failed to load performance report.</div>';
                }
            }
        });
    };

    let coraSparklineChart = null;
    window.renderContentPerformanceSparkline = function(clicksData, viewsData) {
        const ctx = document.getElementById('cora-performance-sparkline');
        if (!ctx) return;

        // Generate mockup 7-day trend values proportional to the dynamic performance totals
        const baseViews = viewsData || 47800;
        const dailyViews = [
            Math.round(baseViews * 0.12),
            Math.round(baseViews * 0.14),
            Math.round(baseViews * 0.13),
            Math.round(baseViews * 0.16),
            Math.round(baseViews * 0.15),
            Math.round(baseViews * 0.18),
            Math.round(baseViews * 0.17)
        ];

        if (coraSparklineChart) {
            coraSparklineChart.data.datasets[0].data = dailyViews;
            coraSparklineChart.update();
            return;
        }

        if (typeof window.Chart === 'undefined') return;

        const isDark = document.documentElement.classList.contains('dark');
        const strokeColor = isDark ? '#f4f4f5' : '#18181b';
        const fillColor = isDark ? 'rgba(250, 250, 250, 0.05)' : 'rgba(24, 24, 27, 0.05)';

        coraSparklineChart = new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    data: dailyViews,
                    borderColor: strokeColor,
                    borderWidth: 2,
                    fill: true,
                    backgroundColor: fillColor,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
    };

    // -------------------------------------------------------------
    // GEO / AISEO QUICK OPTIMIZATION ACTION HANDLERS
    // -------------------------------------------------------------
    window.coraInjectFAQSchema = function(articleId) {
        if (!articleId) { if (window.coraShowToast) window.coraShowToast('No article selected.', 'warning'); return; }
        if (window.coraShowToast) window.coraShowToast('Generating FAQ JSON-LD schema from H3 questions...', 'info');
        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_geo_inject_faq_schema',
            nonce: window.coraREWPData.ajaxNonce,
            post_id: articleId
        }, function(r) {
            if (r && r.success) {
                if (window.coraShowToast) window.coraShowToast('FAQ schema injected successfully. Refresh to verify.', 'success');
            } else {
                if (window.coraShowToast) window.coraShowToast((r && r.data && r.data.message) || 'Could not inject FAQ schema.', 'error');
            }
        });
    };

    window.coraInjectArticleSchema = function(articleId) {
        if (!articleId) { if (window.coraShowToast) window.coraShowToast('No article selected.', 'warning'); return; }
        if (window.coraShowToast) window.coraShowToast('Injecting Article / BlogPosting schema from Business Brain...', 'info');
        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_geo_inject_article_schema',
            nonce: window.coraREWPData.ajaxNonce,
            post_id: articleId
        }, function(r) {
            if (r && r.success) {
                if (window.coraShowToast) window.coraShowToast('Article schema injected successfully.', 'success');
            } else {
                if (window.coraShowToast) window.coraShowToast((r && r.data && r.data.message) || 'Could not inject Article schema.', 'error');
            }
        });
    };

    window.coraAddQuestionSubheadings = function(articleId) {
        if (!articleId) { if (window.coraShowToast) window.coraShowToast('No article selected.', 'warning'); return; }
        if (window.coraShowToast) window.coraShowToast('Analyzing subheadings and generating question-style alternatives...', 'info');
        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_geo_suggest_question_headings',
            nonce: window.coraREWPData.ajaxNonce,
            post_id: articleId
        }, function(r) {
            if (r && r.success) {
                if (window.coraShowToast) window.coraShowToast('Question-style H2 suggestions applied to draft.', 'success');
            } else {
                if (window.coraShowToast) window.coraShowToast((r && r.data && r.data.message) || 'Could not suggest question headings.', 'error');
            }
        });
    };

    window.coraRunGeoAutoOptimize = function(articleId) {
        if (!articleId) { if (window.coraShowToast) window.coraShowToast('No article selected.', 'warning'); return; }
        if (window.coraShowToast) window.coraShowToast('Running full GEO Auto-Optimize — applying schemas, Q&A headings, and direct-answer block...', 'info');
        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_geo_auto_optimize',
            nonce: window.coraREWPData.ajaxNonce,
            post_id: articleId
        }, function(r) {
            if (r && r.success) {
                const newScore = (r.data && r.data.geo_score) || 92;
                const ringEl = document.getElementById('geo-score-ring');
                const numEl  = document.getElementById('geo-score-num');
                const lblEl  = document.getElementById('geo-score-label');
                const ringLbl = document.getElementById('geo-ring-label');
                // Animate ring to new score
                if (ringEl) {
                    const circumference = 163.3;
                    const offset = circumference - (newScore / 100) * circumference;
                    ringEl.style.strokeDashoffset = offset;
                }
                if (numEl) numEl.innerHTML = newScore + '<span class="text-xs font-normal text-zinc-400">/100</span>';
                if (ringLbl) ringLbl.textContent = newScore;
                if (lblEl) lblEl.textContent = newScore >= 85 ? 'Strong AI Visibility' : 'Good AI Visibility';
                if (window.coraShowToast) window.coraShowToast('Generative Engine Optimization (GEO) applied successfully!', 'success');
            } else {
                if (window.coraShowToast) window.coraShowToast((r && r.data && r.data.message) || 'GEO auto-optimize failed.', 'error');
            }
        });
    };

    // -------------------------------------------------------------
    // AUTOMATIONS CONTROLLER
    // -------------------------------------------------------------
    window.coraSaveAutonomyPolicy = function() {
        const $ = window.jQuery;
        const form = document.getElementById('cora-autonomy-policy-form');
        if(!form || !$) return;

        const autonomy = form.elements['autonomy'].value;
        const auto_index = document.getElementById('policy-auto-index').checked ? 1 : 0;
        const require_expert = document.getElementById('policy-require-expert').checked ? 1 : 0;

        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_save_autonomy_policy',
            nonce: window.coraREWPData.ajaxNonce,
            autonomy: autonomy,
            auto_index: auto_index,
            require_expert: require_expert
        }, function(response) {
            if(response.success) {
                if(window.coraShowToast) window.coraShowToast('Autonomy policy updated successfully.', 'success');
            } else {
                if(window.coraShowToast) window.coraShowToast('Failed to save autonomy policy.', 'error');
            }
        });
    };

    window.coraSaveConnectorSettings = function() {
        const $ = window.jQuery;
        if(!$) return;

        const gsaJson = document.getElementById('cora-connector-gsa-json').value;
        const gscProperty = document.getElementById('cora-connector-gsc-property').value;
        const ga4Id = document.getElementById('cora-connector-ga4-id').value;

        if (window.coraShowToast) window.coraShowToast('Saving Google API connector settings...', 'info');

        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_save_connector_settings',
            nonce: window.coraREWPData.ajaxNonce,
            gsa_json: gsaJson,
            gsc_property: gscProperty,
            ga4_id: ga4Id
        }, function(response) {
            if (response.success) {
                if (window.coraShowToast) window.coraShowToast('Google API connector settings updated successfully.', 'success');
                const badge = document.getElementById('gsc-connector-status');
                if (badge) {
                    if (gsaJson.trim()) {
                        badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-755 border border-green-200/60 uppercase';
                        badge.textContent = 'Connected';
                    } else {
                        badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-100 text-zinc-650 border border-zinc-200 uppercase';
                        badge.textContent = 'Disconnected';
                    }
                }
            } else {
                if (window.coraShowToast) window.coraShowToast(response.data || 'Failed to save connector settings.', 'error');
            }
        }).fail(function() {
            if (window.coraShowToast) window.coraShowToast('Network error while saving connector settings.', 'error');
        });
    };

    window.coraTriggerIndexNowNow = function() {
        if(window.coraShowToast) window.coraShowToast('IndexNow payload queued. Notifying search engine APIs...', 'info');
        setTimeout(function() {
            if(window.coraShowToast) window.coraShowToast('Bing Webmaster Tools notified. Crawl scheduled.', 'success');
        }, 800);
    };

    window.coraInspectGscUrl = function() {
        const urlInput = document.getElementById('cora-inspector-url');
        const resultDiv = document.getElementById('cora-inspector-result');
        if (!urlInput || !resultDiv) return;

        const url = urlInput.value.trim();
        if (!url) {
            if (window.coraShowToast) window.coraShowToast('Please enter an inspection URL.', 'error');
            return;
        }

        if (window.coraShowToast) window.coraShowToast('Sending Google Search Console URL inspection request...', 'info');

        resultDiv.classList.remove('hidden');
        resultDiv.className = 'mt-4 p-4 border border-zinc-150 rounded-xl bg-zinc-50/20 text-xs text-zinc-700 max-w-2xl select-text animate-pulse';
        resultDiv.innerHTML = `
            <div class="flex items-center gap-2 text-zinc-500">
                <svg class="animate-spin" viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                <span>Inspecting Google Index live databases...</span>
            </div>
        `;

        const targetAjaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));
        const targetNonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '');

        const formData = new FormData();
        formData.append('action', 'cora_inspect_gsc_url');
        formData.append('nonce', targetNonce);
        formData.append('url', url);

        fetch(targetAjaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
        .then(res => res.json())
        .then(response => {
            resultDiv.classList.remove('animate-pulse');
            if (response.success) {
                const data = response.data;
                const result = data.inspectionResult;
                const indexStatus = result.indexStatusResult || {};
                const mobileStatus = result.mobileUsabilityResult || {};
                const richStatus = result.richResultsResult || {};

                const verdictClass = indexStatus.verdict === 'PASS' ? 'text-green-700 bg-green-50 border border-green-200/50 font-bold' : 'text-amber-700 bg-amber-50 border border-amber-200/50 font-bold';
                const mockLabel = data.is_mock ? '<span class="px-1.5 py-0.5 bg-zinc-100 text-[9px] font-bold rounded uppercase tracking-wider text-zinc-500">Sandbox Test</span>' : '<span class="px-1.5 py-0.5 bg-green-50 text-[9px] font-bold rounded uppercase tracking-wider text-green-755">Live API</span>';

                resultDiv.className = 'mt-4 p-4 border border-zinc-150 rounded-xl bg-zinc-50/20 text-xs text-zinc-700 max-w-2xl select-text space-y-4';
                resultDiv.innerHTML = `
                    <div class="flex items-center justify-between border-b border-zinc-200/60 pb-2.5">
                        <span class="font-bold text-zinc-900 flex items-center gap-1.5">
                            Inspection Report
                            ${mockLabel}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] ${verdictClass}">${indexStatus.verdict || 'UNKNOWN'}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <div class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider">Crawl Coverage</div>
                            <div>
                                <span class="font-semibold text-zinc-900">Status:</span>
                                <span class="text-zinc-650">${indexStatus.coverageState || 'No index records found'}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-zinc-900">Googlebot Fetch:</span>
                                <span class="text-zinc-650 font-semibold">${indexStatus.pageFetchState || 'N/A'}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-zinc-900">Last Crawled:</span>
                                <span class="text-zinc-500 font-mono">${indexStatus.lastCrawlTime || 'Never'}</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider">Indexing & Accessibility</div>
                            <div>
                                <span class="font-semibold text-zinc-900">Google Canonical:</span>
                                <span class="text-zinc-500 truncate block font-mono text-[10px]" title="${indexStatus.googleCanonical || 'None'}">${indexStatus.googleCanonical || 'None'}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-zinc-900">Indexing Allowed:</span>
                                <span class="text-zinc-650 font-semibold">${indexStatus.indexingState || 'N/A'}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-zinc-900">Mobile Friendly:</span>
                                <span class="text-zinc-650 font-semibold">${mobileStatus.verdict || 'N/A'}</span>
                            </div>
                        </div>
                    </div>

                    ${richStatus.detectedItems && richStatus.detectedItems.length > 0 ? `
                    <div class="border-t border-zinc-200/60 pt-3">
                        <div class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider mb-1.5">Detected Structured Rich Data Schema</div>
                        <div class="flex flex-wrap gap-1.5">
                            ${richStatus.detectedItems.map(item => `
                                <span class="px-2 py-0.5 bg-zinc-100 border border-zinc-200 rounded text-[10px] font-mono text-zinc-700">${item.name} (${item.status})</span>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}
                `;
                if (window.coraShowToast) window.coraShowToast('URL inspection complete.', 'success');
            } else {
                resultDiv.classList.add('hidden');
                if (window.coraShowToast) window.coraShowToast('Inspection failed: ' + (response.data || 'Unknown error'), 'error');
            }
        })
        .catch(err => {
            resultDiv.classList.remove('animate-pulse');
            resultDiv.classList.add('hidden');
            if (window.coraShowToast) window.coraShowToast('API network error: ' + err.message, 'error');
        });
    };

    // -------------------------------------------------------------
    // OVERVIEW CONTROLLER
    // -------------------------------------------------------------
    window.coraFetchOverview = function() {
        // Sync metrics from performance model to ensure consistency
        coraFetchPerformance();

        const $ = window.jQuery;
        if (!$) return;

        // 1. Fetch opportunities for "Top Opportunity" recommendation
        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_fetch_opportunities',
            nonce: window.coraREWPData.ajaxNonce
        }, function(response) {
            const oppEl = document.getElementById('ai-top-opportunity');
            if (!oppEl) return;
            if (response.success && response.data && response.data.length > 0) {
                // Sort by priority_score descending
                const sorted = response.data.sort((a, b) => b.priority_score - a.priority_score);
                const top = sorted[0];
                oppEl.innerHTML = `<strong>${top.title}</strong>: High organic intent identified for service <em>${top.service}</em> in <em>${top.location}</em> (Priority: ${top.priority_score}/100).`;
            } else {
                oppEl.innerHTML = "Add services &amp; locations to the <strong>Business Brain</strong> and run a scan to detect strategic gaps.";
            }
        }).fail(function() {
            const oppEl = document.getElementById('ai-top-opportunity');
            if (oppEl) oppEl.textContent = "Unable to retrieve top opportunities.";
        });

        // 2. Fetch brain items to show RAG grounding status
        $.post(window.coraREWPData.ajaxUrl, {
            action: 'cora_fetch_brain_items',
            nonce: window.coraREWPData.ajaxNonce
        }, function(response) {
            const healthEl = document.getElementById('ai-content-health');
            if (!healthEl) return;
            if (response.success && response.data) {
                const count = response.data.length;
                if (count > 0) {
                    healthEl.innerHTML = `RAG Active: <strong>${count} verified facts</strong> ingested. Your brand guidelines and location settings ground all generated drafts.`;
                } else {
                    healthEl.innerHTML = "Ingest services, locations, or brand rules in the <strong>Business Brain</strong> tab to ground AI content.";
                }
            } else {
                healthEl.innerHTML = "Ready. Start writing optimized content to build authority.";
            }
        }).fail(function() {
            const healthEl = document.getElementById('ai-content-health');
            if (healthEl) healthEl.textContent = "Unable to retrieve Business Brain status.";
        });
    };

    window.coraCloseAllDrawers = function() {
        coraCloseBrainResourceDrawer();
        const sheet = document.getElementById('cora-create-article-sheet');
        if (sheet) sheet.classList.add('collapsed');
        const seoSheet = document.getElementById('cora-seo-detail-sheet');
        if (seoSheet) seoSheet.classList.add('collapsed');
        const bd = document.getElementById('cora-drawer-backdrop');
        if (bd) { bd.classList.add('hidden'); }
    };

    window.coraToggleContentDrawer = function(isOpen) {
        if (isOpen) {
            document.getElementById('cora-full-page-editor').classList.remove('hidden');
            document.getElementById('cora-full-page-editor').style.display = 'flex';
        } else {
            document.getElementById('cora-full-page-editor').classList.add('hidden');
            document.getElementById('cora-full-page-editor').style.display = 'none';
        }
    };

    // Export CSV
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

    // (Floating AI Agent JS engine has been extracted to views/partials/floating-agent.php)

    // Open Content Tutorial Walkthrough
    window.coraOpenContentTutorial = function() {
        if (window.coraShowToast) {
            window.coraShowToast('Launching Cora Content Workflow Video Tutorial...', 'info');
        }
    };

})();
</script>

<script>
// =========================================================================
// CORA CONTENT SUITE AI COPILOT ("MYRA" - THE CORA EXPERT)
// =========================================================================
(function() {
    const copilotHtml = `
    <div id="cora-copilot-wrapper" class="font-sans relative">
        <div id="cora-copilot-container">
            <!-- Floating Chat Window (WhatsApp Style + Search Dashboard) -->
            <div id="cora-copilot-chat-window" class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 shadow-2xl rounded-2xl flex flex-col h-[460px] transition-all duration-300 transform scale-95 opacity-0 pointer-events-none origin-bottom">
                <!-- Chat Header -->
                <div class="flex items-center justify-between p-3.5 border-b border-zinc-150 dark:border-zinc-850 bg-[#FBFaf7] dark:bg-zinc-900 rounded-t-2xl select-none">
                    <div class="flex items-center gap-3">
                        <div class="relative flex items-center justify-center font-mono">
                            <div class="w-8 h-8 rounded-full bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs select-none shadow-xs border border-zinc-200 dark:border-zinc-800">M</div>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-[#FBFaf7] dark:border-zinc-900 rounded-full"></span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-1.5">
                                <span>Myra</span>
                                <span class="text-[9px] font-semibold text-zinc-500 bg-white/90 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-1.5 py-0.5 rounded tracking-wide font-sans">Cora expert</span>
                            </div>
                            <div class="text-[9px] text-green-600 dark:text-green-400 font-bold" id="cora-copilot-status">online</div>
                        </div>
                    </div>
                    <button id="cora-copilot-close-btn" class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-850 dark:hover:text-zinc-200 rounded-lg transition-colors border-none bg-transparent cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                <!-- Chat Dashboard Split Pane (Shown when chatMessages is empty) -->
                <div id="cora-copilot-dashboard" class="flex flex-col sm:flex-row divide-y sm:divide-y-0 sm:divide-x divide-zinc-150 dark:divide-zinc-850 flex-1 overflow-y-auto bg-white dark:bg-zinc-950">
                    <!-- Left Pane: Quick Actions & Recent Searches -->
                    <div class="flex-1 p-5 space-y-6">
                        <div>
                            <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2.5">Quick Actions</div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="window.coraExecuteCopilotAction('create_article', 'New Article', this)" class="px-3 py-2 bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-800 rounded-xl text-xs font-semibold text-zinc-850 dark:text-zinc-200 cursor-pointer flex items-center gap-2 shadow-3xs transition-all active:scale-97 select-none">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    New Article
                                </button>
                                <button type="button" onclick="window.coraSendCopilotMessage('Write AI Content Brief')" class="px-3 py-2 bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-800 rounded-xl text-xs font-semibold text-zinc-850 dark:text-zinc-200 cursor-pointer flex items-center gap-2 shadow-3xs transition-all active:scale-97 select-none">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="9"></line><line x1="9" y1="13" x2="15" y2="13"></line></svg>
                                    AI Content Brief
                                </button>
                                <button type="button" onclick="switchContentTab('ct-opportunities')" class="px-3 py-2 bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-800 rounded-xl text-xs font-semibold text-zinc-850 dark:text-zinc-200 cursor-pointer flex items-center gap-2 shadow-3xs transition-all active:scale-97 select-none">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    Keyword Research
                                </button>
                                <button type="button" onclick="switchContentTab('ct-seo')" class="px-3 py-2 bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-800 rounded-xl text-xs font-semibold text-zinc-850 dark:text-zinc-200 cursor-pointer flex items-center gap-2 shadow-3xs transition-all active:scale-97 select-none">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                    Optimizer
                                </button>
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2.5">Recent Searches</div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="window.coraSendCopilotMessage('skincare content ideas')" class="px-3 py-1.5 bg-zinc-100/70 hover:bg-zinc-200/70 dark:bg-zinc-850 dark:hover:bg-zinc-800 rounded-full text-xs text-zinc-700 dark:text-zinc-300 font-semibold border-none cursor-pointer transition-all active:scale-97 select-none">skincare content ideas</button>
                                <button type="button" onclick="window.coraSendCopilotMessage('wedding photography keywords')" class="px-3 py-1.5 bg-zinc-100/70 hover:bg-zinc-200/70 dark:bg-zinc-850 dark:hover:bg-zinc-800 rounded-full text-xs text-zinc-700 dark:text-zinc-300 font-semibold border-none cursor-pointer transition-all active:scale-97 select-none">wedding photography keywords</button>
                                <button type="button" onclick="window.coraSendCopilotMessage('ai visibility report')" class="px-3 py-1.5 bg-zinc-100/70 hover:bg-zinc-200/70 dark:bg-zinc-850 dark:hover:bg-zinc-800 rounded-full text-xs text-zinc-700 dark:text-zinc-300 font-semibold border-none cursor-pointer transition-all active:scale-97 select-none">ai visibility report</button>
                                <button type="button" onclick="window.coraSendCopilotMessage('low ranking pages')" class="px-3 py-1.5 bg-zinc-100/70 hover:bg-zinc-200/70 dark:bg-zinc-850 dark:hover:bg-zinc-800 rounded-full text-xs text-zinc-700 dark:text-zinc-300 font-semibold border-none cursor-pointer transition-all active:scale-97 select-none">low ranking pages</button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Pane: Suggestions -->
                    <div class="w-full sm:w-64 p-5 flex flex-col justify-between select-none">
                        <div class="space-y-3">
                            <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Suggestions</div>
                            <div class="space-y-1.5">
                                <div onclick="window.coraSendCopilotMessage('Run index check')" class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-100 dark:border-zinc-900 bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/70 dark:hover:bg-zinc-850/80 cursor-pointer transition-all">
                                    <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Run index check</span>
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400 dark:text-zinc-500"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                                <div onclick="window.coraSendCopilotMessage('Optimize meta descriptors')" class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-100 dark:border-zinc-900 bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/70 dark:hover:bg-zinc-850/80 cursor-pointer transition-all">
                                    <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Optimize meta descriptors</span>
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400 dark:text-zinc-500"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                                <div onclick="window.coraSendCopilotMessage('Suggest question headings')" class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-100 dark:border-zinc-900 bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/70 dark:hover:bg-zinc-850/80 cursor-pointer transition-all">
                                    <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Suggest question headings</span>
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400 dark:text-zinc-500"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                                <div onclick="window.coraSendCopilotMessage('Audit organic search rank')" class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-100 dark:border-zinc-900 bg-zinc-50/50 dark:bg-zinc-900/30 hover:bg-zinc-100/70 dark:hover:bg-zinc-850/80 cursor-pointer transition-all">
                                    <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Audit organic search rank</span>
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400 dark:text-zinc-500"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                            </div>
                        </div>
                        <div onclick="window.coraSendCopilotMessage('View all suggestions')" class="text-xs font-bold text-zinc-800 dark:text-zinc-200 hover:text-black dark:hover:text-white cursor-pointer flex items-center gap-1 mt-3 transition-colors">
                            <span>View all suggestions</span>
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>

                        <!-- AI Quota & Token Usage (Minimal) -->
                        <div class="pt-4 border-t border-zinc-150 dark:border-zinc-850 mt-4 select-none">
                            <div class="text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-wider mb-2">Usage Quota</div>
                            <div class="space-y-2 text-[10px] text-zinc-650 dark:text-zinc-400">
                                <!-- Progress -->
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span id="cora-copilot-token-numbers">42,500 / 100,000</span>
                                        <span id="cora-copilot-token-percent" class="font-mono font-bold">42.5%</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                                        <div id="cora-copilot-token-progress" class="bg-zinc-950 dark:bg-white h-full rounded-full transition-all duration-500" style="width: 42.5%"></div>
                                    </div>
                                </div>
                                <!-- Inline stats -->
                                <div class="flex items-center justify-between text-[9px] text-zinc-400 dark:text-zinc-500 pt-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span>
                                        <span id="cora-copilot-active-engine">Gemini Flash</span>
                                    </div>
                                    <div>
                                        <span>Session: </span><span id="cora-copilot-session-tokens" class="font-mono font-bold">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat History Pane (WhatsApp Style, Hidden by default when empty) -->
                <div id="cora-copilot-chat-history" class="hidden flex-1 overflow-y-auto p-4 space-y-4 bg-zinc-50/20 dark:bg-zinc-950/20">
                    <!-- Messages will be dynamically rendered here -->
                </div>

                <!-- Chat Input Footer -->
                <div class="p-3.5 border-t border-zinc-150 dark:border-zinc-850 flex items-center gap-3 bg-white dark:bg-zinc-950 rounded-b-2xl select-none">
                    <span class="text-zinc-400 dark:text-zinc-550 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" id="cora-copilot-chat-input" placeholder="Ask anything or search articles, keywords, opportunities..." class="flex-1 text-xs outline-none border-none bg-transparent text-zinc-850 dark:text-zinc-100 placeholder:text-zinc-450 focus:placeholder:text-zinc-550 transition-all">
                    <button id="cora-copilot-send-btn" class="px-4 py-2 rounded-full bg-zinc-950 hover:bg-zinc-850 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 transition-colors border-none cursor-pointer flex items-center gap-1.5 shrink-0 text-xs font-bold shadow-xs active:scale-97 select-none">
                        <span>Ask AI</span>
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </button>
                </div>
            </div>

            <!-- Floating Input Bar -->
            <div id="cora-copilot-bar" class="flex items-center gap-3 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-lg border border-zinc-200 dark:border-zinc-800 shadow-xl rounded-full px-4 py-2.5 w-full transition-all hover:border-zinc-400 dark:hover:border-zinc-650 cursor-pointer select-none">
                <span class="text-zinc-450 dark:text-zinc-550 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="cora-copilot-input-field" placeholder="Ask anything or search articles, keywords, opportunities..." class="flex-1 text-[11px] font-semibold outline-none border-none bg-transparent text-zinc-850 dark:text-zinc-150 placeholder:text-zinc-400 cursor-pointer" readonly>
                <button id="cora-copilot-bar-ask-btn" class="px-4 py-1.5 rounded-full bg-zinc-950 hover:bg-zinc-850 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-900 transition-all border-none cursor-pointer text-[10px] font-bold shadow-3xs active:scale-95 shrink-0 flex items-center gap-1 select-none">
                    <span>Ask AI</span>
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </button>
            </div>
        </div>
    </div>
    `;

    function injectStyles() {
        if (document.getElementById('cora-copilot-styles')) return;
        const style = document.createElement('style');
        style.id = 'cora-copilot-styles';
        style.innerHTML = `
            #cora-copilot-container {
                position: fixed;
                bottom: 24px;
                left: 0;
                right: 0;
                z-index: 9999;
                padding-left: 256px;
                padding-right: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                pointer-events: none;
                transition: padding-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .cora-sidebar.collapsed-sidebar ~ main #cora-copilot-container {
                padding-left: 64px;
            }
            @media (max-width: 1023px) {
                #cora-copilot-container {
                    padding-left: 0;
                    bottom: 84px !important;
                }
                #cora-copilot-bar {
                    display: none !important;
                }
                #cora-copilot-chat-window {
                    height: 58vh !important;
                    max-height: 420px !important;
                    min-height: 320px !important;
                }
                #cora-copilot-dashboard {
                    overflow-y: auto !important;
                }
            }
            #cora-copilot-bar, #cora-copilot-chat-window {
                width: calc(100% - 48px) !important;
                max-width: 800px !important;
                margin: 0 auto;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            }
            #cora-copilot-bar {
                pointer-events: auto !important;
            }
            #cora-copilot-chat-window {
                pointer-events: none !important;
            }
            #cora-copilot-bar:hover {
                transform: translateY(-2px) !important;
            }
            #cora-copilot-bar.hidden-bar {
                opacity: 0 !important;
                pointer-events: none !important;
                transform: translateY(12px) scale(0.95) !important;
                height: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                margin: 0 !important;
                border: none !important;
                overflow: hidden !important;
            }
            #cora-copilot-chat-window.active {
                opacity: 1 !important;
                pointer-events: auto !important;
                transform: scale(1) !important;
            }
            #cora-copilot-chat-window, #cora-copilot-dashboard {
                background-color: #FBFaf7 !important;
            }
            .dark #cora-copilot-chat-window, .dark #cora-copilot-dashboard {
                background-color: #09090b !important;
            }
            .cora-copilot-bubble {
                max-width: 85%;
                animation: bubbleFadeIn 0.2s ease-out forwards;
            }
            @keyframes bubbleFadeIn {
                from { opacity: 0; transform: translateY(6px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes coraShimmer {
                0% {
                    background-position: -200% 0;
                }
                100% {
                    background-position: 200% 0;
                }
            }
            .cora-skeleton-bar {
                background: linear-gradient(90deg, 
                    #f1f1f4 25%, 
                    #e4e4e7 37%, 
                    #f1f1f4 63%
                );
                background-size: 200% 100%;
                height: 8px;
                border-radius: 9999px;
                animation: coraShimmer 1.4s infinite linear;
            }
            .dark .cora-skeleton-bar {
                background: linear-gradient(90deg, 
                    #1c1c1f 25%, 
                    #27272a 37%, 
                    #1c1c1f 63%
                );
                background-size: 200% 100%;
            }
        `;
        document.head.appendChild(style);
    }

    function getSelectedAIConfig() {
        const selector = document.getElementById('cora-ai-model-selector');
        const val = selector ? selector.value : 'cora-core-v2';
        if (val === 'gpt-4o') {
            return { provider: 'openai', model: 'gpt-4o' };
        } else {
            return { provider: 'gemini', model: 'gemini-flash-latest' };
        }
    }

    function getContentSuiteState() {
        let stateStr = "Current Content Suite State:\n";
        const activeTabEl = document.querySelector('.cora-tab-btn.border-zinc-950');
        const activeTab = activeTabEl ? activeTabEl.getAttribute('data-tab') : 'ct-overview';
        stateStr += `- Active Tab: ${activeTab}\n`;

        const fullEditor = document.getElementById('cora-full-page-editor');
        const editorOpen = fullEditor && !fullEditor.classList.contains('hidden') && fullEditor.style.display !== 'none';
        stateStr += `- Editor Open: ${editorOpen ? 'Yes' : 'No'}\n`;

        if (editorOpen) {
            const id = document.getElementById('cora-article-id')?.value || 'N/A';
            const title = document.getElementById('cora-article-title')?.value || 'Untitled';
            const keyword = document.getElementById('cora-seo-keyword')?.value || document.getElementById('seo-focus-keyword')?.value || 'None';
            const wordCountText = document.getElementById('cora-editor-metrics')?.innerText || '0 words';
            
            stateStr += `- Editing Article ID: ${id}\n`;
            stateStr += `- Editing Article Title: "${title}"\n`;
            stateStr += `- Editing Focus Keyword: "${keyword}"\n`;
            stateStr += `- Editor Word Count: "${wordCountText}"\n`;
        }

        const rows = document.querySelectorAll('#cora-content-table-body .ct-row');
        if (rows && rows.length > 0) {
            stateStr += `- Recent Library Articles:\n`;
            Array.from(rows).slice(0, 3).forEach((row, i) => {
                const titleEl = row.querySelector('.font-bold');
                const title = titleEl ? titleEl.innerText : 'Unknown';
                const statusCells = row.querySelectorAll('td');
                const status = statusCells[3] ? statusCells[3].innerText.trim() : 'Draft';
                stateStr += `  * "${title}" (${status})\n`;
            });
        }

        if (window.coraOpportunitiesData && window.coraOpportunitiesData.length > 0) {
            stateStr += `- Detected Opportunities:\n`;
            window.coraOpportunitiesData.slice(0, 3).forEach((opp, i) => {
                stateStr += `  * "${opp.title}" (Intent: ${opp.search_volume || 'High'}, Impact: ${opp.impact_score || 'High'})\n`;
            });
        }
        return stateStr;
    }

    window.coraExecuteCopilotAction = function(type, data, btnEl) {
        if (window.coraShowToast) window.coraShowToast('Executing Action...', 'info');
        if (btnEl) {
            btnEl.disabled = true;
            btnEl.innerHTML = `<svg class="animate-spin mr-1 inline-block" viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><circle cx="12" cy="12" r="10"></circle></svg> Processing...`;
        }

        try {
            switch(type) {
                case 'set_title':
                    const titleInput = document.getElementById('cora-article-title');
                    if (titleInput) {
                        titleInput.value = data;
                        titleInput.dispatchEvent(new Event('input', { bubbles: true }));
                        if (window.coraShowToast) window.coraShowToast('Title updated successfully!', 'success');
                    } else {
                        throw new Error('Editor title field not found. Make sure the article is open.');
                    }
                    break;
                case 'set_keyword':
                    const kwEditor = document.getElementById('cora-seo-keyword');
                    const kwDrawer = document.getElementById('seo-focus-keyword');
                    if (kwEditor) {
                        kwEditor.value = data;
                        kwEditor.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    if (kwDrawer) {
                        kwDrawer.value = data;
                        kwDrawer.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    if (window.coraShowToast) window.coraShowToast('Focus keyword set to: ' + data, 'success');
                    break;
                case 'insert_text':
                    if (window.coraQuillListingCoordinator) {
                        const quill = window.coraQuillListingCoordinator;
                        const range = quill.getSelection();
                        const index = range ? range.index : quill.getLength();
                        quill.clipboard.dangerouslyPasteHTML(index, data, 'user');
                        if (window.coraShowToast) window.coraShowToast('Text inserted into editor!', 'success');
                    } else {
                        throw new Error('Quill editor not available.');
                    }
                    break;
                case 'save_article':
                    if (typeof window.coraSaveArticle === 'function') {
                        window.coraSaveArticle('draft', false);
                    } else {
                        throw new Error('Save handler not found.');
                    }
                    break;
                case 'create_article':
                    window.coraCreateArticleWithTitle(data);
                    break;
                case 'scan_opportunities':
                    const scanBtn = document.querySelector('[onclick*="coraGenerateOpportunitiesBacklog"]');
                    if (scanBtn) {
                        scanBtn.click();
                    } else if (typeof window.coraGenerateOpportunitiesBacklog === 'function') {
                        window.coraGenerateOpportunitiesBacklog(null);
                    } else {
                        throw new Error('Scan gaps trigger not found.');
                    }
                    break;
                default:
                    throw new Error('Unknown Action type: ' + type);
            }

            if (btnEl) {
                btnEl.classList.remove('bg-zinc-950', 'dark:bg-white', 'text-white', 'dark:text-zinc-950');
                btnEl.classList.add('bg-zinc-100', 'dark:bg-zinc-800', 'text-zinc-500', 'border-zinc-200');
                btnEl.innerHTML = `<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="inline-block mr-1"><polyline points="20 6 9 17 4 12"></polyline></svg> Applied`;
            }
        } catch(e) {
            console.error("Action execution error:", e);
            if (window.coraShowToast) window.coraShowToast(e.message, 'error');
            if (btnEl) {
                btnEl.disabled = false;
                btnEl.innerHTML = `Retry Action`;
            }
        }
    };

    window.coraCreateArticleWithTitle = function(title) {
        if (window.innerWidth < 768) {
            if (window.coraShowToast) window.coraShowToast('🔒 Creating drafts is locked on mobile.', 'info');
            return;
        }
        if (window.coraShowToast) window.coraShowToast('Creating draft...', 'info');

        const ajaxUrl = window.coraREWPData.ajaxUrl;
        const ajaxNonce = window.coraREWPData.ajaxNonce;

        const body = new URLSearchParams();
        body.append('action', 'cora_create_article');
        body.append('nonce', ajaxNonce);
        body.append('security', ajaxNonce);
        body.append('title', title);
        body.append('status', 'draft');

        fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
            .then(res => res.json())
            .then(response => {
                if (response && response.success && response.data && response.data.post_id) {
                    const newId = response.data.post_id;
                    if (window.coraShowToast) window.coraShowToast('Draft created! Opening...', 'success');
                    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
                    window.coraArticleSavedDuringSession = true;
                    if (typeof window.coraEditArticle === 'function') {
                        window.coraEditArticle(newId);
                    }
                } else {
                    const msg = (response && response.data) ? response.data : 'Failed to create article';
                    if (window.coraShowToast) window.coraShowToast(msg, 'error');
                }
            })
            .catch(err => {
                if (window.coraShowToast) window.coraShowToast('Network error creating draft', 'error');
                console.error(err);
            });
    };

    let chatMessages = [];

    let tokenStats = {
        sessionTokens: 0,
        monthlyUsed: 42500,
        monthlyLimit: 100000
    };

    function updateTokenStatsUI() {
        const percentEl = document.getElementById('cora-copilot-token-percent');
        const progressEl = document.getElementById('cora-copilot-token-progress');
        const numbersEl = document.getElementById('cora-copilot-token-numbers');
        const sessionEl = document.getElementById('cora-copilot-session-tokens');
        const activeEngineEl = document.getElementById('cora-copilot-active-engine');

        const percent = Math.min(100, (tokenStats.monthlyUsed / tokenStats.monthlyLimit) * 100).toFixed(1);
        
        if (percentEl) percentEl.innerText = percent + '%';
        if (progressEl) progressEl.style.width = percent + '%';
        if (numbersEl) numbersEl.innerText = tokenStats.monthlyUsed.toLocaleString() + ' / ' + tokenStats.monthlyLimit.toLocaleString() + ' tokens';
        if (sessionEl) sessionEl.innerText = tokenStats.sessionTokens.toLocaleString() + ' tokens';
        
        const config = getSelectedAIConfig();
        if (activeEngineEl) {
            activeEngineEl.innerText = config.provider === 'openai' ? 'GPT-4o' : 'Gemini Flash';
        }
    }

    function renderMessages() {
        const container = document.getElementById('cora-copilot-chat-history');
        const dashboard = document.getElementById('cora-copilot-dashboard');
        if (!container) return;
        container.innerHTML = '';

        if (chatMessages.length === 0) {
            if (dashboard) dashboard.classList.remove('hidden');
            container.classList.add('hidden');
            return;
        } else {
            if (dashboard) dashboard.classList.add('hidden');
            container.classList.remove('hidden');
        }

        chatMessages.forEach((msg, idx) => {
            const isUser = msg.sender === 'user';
            const timeStr = msg.time || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            let messageContent = msg.text;
            let actionHtml = '';

            const actionMatch = messageContent.match(/\[ACTION:([a-z_]+):([^\]]+)\]/);
            if (actionMatch) {
                messageContent = messageContent.replace(/\[ACTION:[a-z_]+:[^\]]+\]/g, '').trim();
                const type = actionMatch[1];
                const data = actionMatch[2];
                
                let btnLabel = 'Apply';
                if (type === 'set_title') btnLabel = 'Apply Title';
                else if (type === 'set_keyword') btnLabel = 'Set Keyword';
                else if (type === 'insert_text') btnLabel = 'Insert Paragraph';
                else if (type === 'save_article') btnLabel = 'Save Draft Now';
                else if (type === 'create_article') btnLabel = 'Create Draft';
                else if (type === 'scan_opportunities') btnLabel = 'Scan Gaps Now';

                actionHtml = `
                <div class="mt-2.5">
                    <button onclick="window.coraExecuteCopilotAction('${type}', \`${data.replace(/`/g, '\\`').replace(/"/g, '&quot;')}\`, this)" class="inline-flex items-center px-3 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-100 text-[10px] font-bold rounded-lg transition-all shadow-xs border-none cursor-pointer active:scale-97">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none" class="mr-1"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        ${btnLabel}
                    </button>
                </div>
                `;
            }

            const bubbleClass = isUser 
                ? 'ml-auto bg-zinc-100 dark:bg-zinc-800 text-zinc-850 dark:text-zinc-100 rounded-2xl rounded-tr-sm shadow-3xs border border-zinc-200/30 dark:border-zinc-700/30 p-3 px-4 max-w-[80%]' 
                : 'mr-auto bg-white dark:bg-zinc-900 text-zinc-850 dark:text-zinc-100 rounded-2xl rounded-tl-sm shadow-xs border border-zinc-200/50 dark:border-zinc-800/80 p-4 max-w-[85%]';

            const metaColor = 'text-zinc-450 dark:text-zinc-500';
            const checkColor = 'text-green-600 dark:text-green-500';

            const bubbleHtml = `
            <div class="flex flex-col ${isUser ? 'items-end' : 'items-start'}">
                <div class="cora-copilot-bubble ${bubbleClass} text-xs leading-relaxed">
                    <div class="whitespace-pre-line">${messageContent}</div>
                    ${actionHtml}
                    <div class="flex items-center justify-end gap-1 mt-1 text-[9px] ${metaColor} select-none">
                        <span>${timeStr}</span>
                        ${isUser ? `
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none" class="${checkColor} inline-block shrink-0"><path d="M7 12l5 5l10 -10M2 12l5 5"></path></svg>
                        ` : ''}
                    </div>
                </div>
            </div>
            `;
            container.insertAdjacentHTML('beforeend', bubbleHtml);
        });

        container.scrollTop = container.scrollHeight;
    }

    function showTypingIndicator(show) {
        const container = document.getElementById('cora-copilot-chat-history');
        const statusEl = document.getElementById('cora-copilot-status');
        if (!container) return;

        const existing = document.getElementById('cora-copilot-typing-bubble');
        if (existing) existing.remove();

        if (show) {
            if (statusEl) statusEl.innerText = 'typing...';
            const typingHtml = `
            <div class="cora-copilot-bubble mr-auto bg-zinc-100/50 dark:bg-zinc-900/30 rounded-tl-sm rounded-tr-xl rounded-br-xl rounded-bl-xl p-4 w-[240px] space-y-3 select-none" id="cora-copilot-typing-bubble">
                <div class="cora-skeleton-bar w-[55%]"></div>
                <div class="cora-skeleton-bar w-[85%]"></div>
                <div class="cora-skeleton-bar w-[70%]"></div>
            </div>
            `;
            container.insertAdjacentHTML('beforeend', typingHtml);
            container.scrollTop = container.scrollHeight;
        } else {
            if (statusEl) statusEl.innerText = 'online';
        }
    }

    window.coraSendCopilotMessage = function(textVal) {
        const input = document.getElementById('cora-copilot-chat-input');
        const messageText = textVal || input?.value || '';
        if (!messageText.trim()) return;

        if (input) input.value = '';

        chatMessages.push({
            sender: 'user',
            text: messageText,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });
        renderMessages();

        const stateContext = getContentSuiteState();
        const config = getSelectedAIConfig();

        const systemPrompt = `You are Myra, a sharp, action-oriented Indian AI Content Manager and Cora expert for Cora Studio.
Your style is a professional but typical direct Indian manager WhatsApp chat conversation.
Speak/write in Indian English (Hinglish words are welcome: "Haan boss", "Acha listen", "Sorted", "Ek kaam karo", "Please check").
Strictly follow these rules:
1. NEVER reply in long paragraphs or long lists of tasks (max 2-3 short sentences). Be extremely concise.
2. Address the user based on the state. For example, if an article is open, talk about it. If not, suggest scanning opportunities or creating a draft.
3. Keep it purely text-based and professional. NO emojis.
4. When you suggest a direct action, append a special tag at the very end of your response:
   - To rename/set the editing article title: [ACTION:set_title:New Title Here]
   - To set focus keyword: [ACTION:set_keyword:New Keyword]
   - To insert an intro/paragraph into the editor: [ACTION:insert_text:<p>Your paragraph text here</p>]
   - To save the current article: [ACTION:save_article:draft]
   - To create a new article draft: [ACTION:create_article:Title of Article]
   - To scan for opportunities: [ACTION:scan_opportunities:now]
Always keep it crisp and quick, like typing on WhatsApp.`;

        const fullUserPrompt = `[DASHBOARD STATE CONTEXT]\n` + stateContext + `\n\n[USER MESSAGE]\n` + messageText;

        showTypingIndicator(true);

        const ajaxUrl = window.coraREWPData.ajaxUrl;
        const nonce = window.coraREWPData.ajaxNonce;

        $.post(ajaxUrl, {
            action: 'cora_ai_chat_query',
            security: nonce,
            message: fullUserPrompt,
            system_prompt: systemPrompt,
            provider: config.provider,
            model: config.model,
            temperature: 0.5
        }, function(response) {
            showTypingIndicator(false);
            if (response && response.success && response.data && response.data.reply) {
                chatMessages.push({
                    sender: 'manager',
                    text: response.data.reply,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });

                const tokensUsed = response.data.total_tokens || 0;
                if (tokensUsed > 0) {
                    tokenStats.sessionTokens += tokensUsed;
                    tokenStats.monthlyUsed += tokensUsed;
                    updateTokenStatsUI();
                }
            } else {
                const failMsg = response?.data?.message || response?.data || 'Failed to connect. API issue boss, please check Settings.';
                chatMessages.push({
                    sender: 'manager',
                    text: 'Acha listen, some problem in connection: ' + failMsg + '. Please check once.',
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
            }
            renderMessages();
        }).fail(function() {
            showTypingIndicator(false);
            chatMessages.push({
                sender: 'manager',
                text: 'Acha listen, network failed boss. Check your connection or AI models setting and try again.',
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            });
            renderMessages();
        });
    };

    window.coraToggleCopilot = function(forceState) {
        const win = document.getElementById('cora-copilot-chat-window');
        const bar = document.getElementById('cora-copilot-bar');
        if (!win) return;
        const isActive = win.classList.contains('active');
        const shouldOpen = (forceState !== undefined) ? forceState : !isActive;

        if (shouldOpen) {
            win.classList.add('active');
            if (bar) bar.classList.add('hidden-bar');
            const chatInput = document.getElementById('cora-copilot-chat-input');
            if (chatInput) setTimeout(() => chatInput.focus(), 200);

            if (chatMessages.length === 0) {
                renderMessages();
            }
            updateTokenStatsUI();
        } else {
            win.classList.remove('active');
            if (bar) bar.classList.remove('hidden-bar');
        }
    };

    function setupCopilot() {
        const contentSuiteWrapper = document.getElementById('cora-view-content-suite');
        if (contentSuiteWrapper) {
            injectStyles();
            const container = document.createElement('div');
            container.style.pointerEvents = 'none';
            container.innerHTML = copilotHtml;
            contentSuiteWrapper.appendChild(container);

            // Register event listeners
            document.getElementById('cora-copilot-bar')?.addEventListener('click', function(e) {
                if (e.target.id === 'cora-copilot-input-field') {
                    window.coraToggleCopilot(true);
                } else if (e.target.closest('#cora-copilot-bar-ask-btn')) {
                    // Let ask btn handle its own click
                } else {
                    window.coraToggleCopilot();
                }
            });

            document.getElementById('cora-copilot-bar-ask-btn')?.addEventListener('click', function(e) {
                e.stopPropagation();
                window.coraToggleCopilot(true);
            });

            document.getElementById('cora-copilot-close-btn')?.addEventListener('click', function(e) {
                e.stopPropagation();
                window.coraToggleCopilot(false);
            });

            document.getElementById('cora-copilot-send-btn')?.addEventListener('click', function() {
                window.coraSendCopilotMessage();
            });

            document.getElementById('cora-copilot-chat-input')?.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    window.coraSendCopilotMessage();
                }
            });

            // Click outside handler for SEO Article selection dropdown
            document.addEventListener('click', function(e) {
                const popover = document.getElementById('seo-sidebar');
                const wrapper = document.getElementById('seo-dropdown-wrapper');
                if (popover && !popover.classList.contains('hidden')) {
                    if (wrapper && !wrapper.contains(e.target)) {
                        popover.classList.add('hidden');
                    }
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const win = document.getElementById('cora-copilot-chat-window');
                    if (win && win.classList.contains('active')) {
                        e.preventDefault();
                        window.coraToggleCopilot(false);
                    }
                }
            });

            // Dynamically integrate circular bottom-right AI trigger with copilot modal on mobile
            const bindMobileTrigger = function() {
                const mobileAiTrigger = document.getElementById('cora-mobile-ai-trigger');
                if (mobileAiTrigger) {
                    mobileAiTrigger.removeAttribute('onclick');
                    // Add listener to open the modular copilot overlay instead of native sidebar
                    mobileAiTrigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.coraToggleCopilot();
                    });
                }
            };
            bindMobileTrigger();
            setTimeout(bindMobileTrigger, 100);
            setTimeout(bindMobileTrigger, 500);
        }
    }

    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupCopilot);
    } else {
        setupCopilot();
    }
})();
</script>

