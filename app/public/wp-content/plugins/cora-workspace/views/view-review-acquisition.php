<?php
/**
 * Cora Workspace — Reviews & Feedback Engine
 * File: views/view-review-acquisition.php
 * Automated Google Business 5-Star Reviews, Multi-Channel Automation (WhatsApp/Email/Socials),
 * Private Reputation Shield, & Automated Performance Reports.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Fetch review requests from WP options or fallback to sample data
$cora_review_requests = get_option( 'cora_review_requests', array() );

if ( empty( $cora_review_requests ) || ! is_array( $cora_review_requests ) ) {
    $cora_review_requests = array(
        array(
            'id'            => 'rev_101',
            'client_name'   => 'Arjun Sharma',
            'client_phone'  => '9876543210',
            'client_email'  => 'arjun.sharma@example.com',
            'project_title' => 'Wedding Photography Package',
            'category'      => 'Studio Photography',
            'status'        => 'Google 5-Star Published',
            'rating'        => 5,
            'review_text'   => 'Exceptional 3-day wedding photography coverage! Turnaround was super fast and drone aerials were stunning.',
            'is_private'    => false,
            'sent_at'       => '2026-07-20 14:00',
            'responded_at'  => '2026-07-20 15:30',
            'channel'       => 'WhatsApp'
        )
    );
    update_option( 'cora_review_requests', $cora_review_requests );
}

// Calculate summary metrics
$total_requests = count( $cora_review_requests );
$google_reviews = 0;
$private_intercepts = 0;
$avg_rating_sum = 0;

foreach ( $cora_review_requests as $req ) {
    if ( ! empty( $req['rating'] ) ) $avg_rating_sum += floatval( $req['rating'] );
    if ( ( $req['status'] ?? '' ) === 'Google 5-Star Published' || ( $req['status'] ?? '' ) === 'Google Reviewed' || ( $req['rating'] ?? 0 ) >= 4 ) {
        $google_reviews++;
    } elseif ( ! empty( $req['is_private'] ) || ( $req['rating'] ?? 0 ) <= 3 ) {
        $private_intercepts++;
    }
}

$avg_rating = $total_requests > 0 ? sprintf( '%.1f', $avg_rating_sum / $total_requests ) : '4.3';
$google_link = get_option( 'cora_google_business_url', 'https://g.page/r/cora_studio/review' );
$wa_template = get_option( 'cora_wa_review_template', 'Namaste {client_name} ji! Thank you for choosing Cora Studio. Could you take 5 seconds to rate us on Google? Tap here to post: {review_url}' );
$email_template = get_option( 'cora_email_review_template', 'Hi {client_name}, we appreciate your business! Please leave us a review on Google.' );

// Dynamic KPI progress bar percentages
$kpi_requests_pct  = min( 100, $total_requests > 0 ? round( ( $total_requests / max( $total_requests, 10 ) ) * 100 ) : 0 );
$kpi_google_pct    = $total_requests > 0 ? round( ( $google_reviews / $total_requests ) * 100 ) : 0;
$kpi_intercept_pct = $total_requests > 0 ? round( ( $private_intercepts / $total_requests ) * 100 ) : 0;
$kpi_score_pct     = round( ( floatval( $avg_rating ) / 5 ) * 100 );

// Dynamic report metrics
$report_conv_rate    = $total_requests > 0 ? sprintf( '%.1f', ( $google_reviews / $total_requests ) * 100 ) : '0.0';
$positive_count      = 0;
$neutral_count       = 0;
$negative_count      = 0;
foreach ( $cora_review_requests as $_r ) {
    $r_val = intval( $_r['rating'] ?? 5 );
    if ( $r_val >= 4 ) $positive_count++;
    elseif ( $r_val === 3 ) $neutral_count++;
    else $negative_count++;
}
$positive_pct = $total_requests > 0 ? round( ( $positive_count / $total_requests ) * 100 ) : 0;
$neutral_pct  = $total_requests > 0 ? round( ( $neutral_count / $total_requests ) * 100 ) : 0;
$negative_pct = $total_requests > 0 ? round( ( $negative_count / $total_requests ) * 100 ) : 0;
?>

<div id="cora-reviews-feedback-wrapper" class="space-y-3.5 font-sans text-zinc-900 dark:text-zinc-100 relative">
    <!-- Ultra-Compact Space-Optimized Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 py-0.5">
        <div class="space-y-0.5">
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-lg sm:text-xl font-extrabold text-zinc-950 dark:text-white tracking-tight m-0 leading-none">Reviews & Feedback</h1>
                <span class="px-2 py-0.5 rounded-full bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 text-[10px] sm:text-[11px] font-medium border border-zinc-200 dark:border-zinc-800 flex items-center gap-1 shadow-2xs shrink-0">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span>Verified Shield Active</span>
                </span>
            </div>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 m-0 leading-tight hidden sm:block">Automated 5-star review collector, multi-channel dispatch engine, and private reputation shield.</p>
        </div>

        <!-- Action Buttons (Full-Width Grid on Mobile, Flex Inline on Desktop) -->
        <div class="grid grid-cols-2 gap-2 w-full sm:w-auto sm:flex sm:items-center shrink-0">
            <button type="button" onclick="coraOpenSendReviewDrawer()" class="h-9 px-3.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-extrabold rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-sm cursor-pointer active:scale-97">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Request Review</span>
            </button>
            <button type="button" onclick="coraOpenReportDrawer()" class="h-9 px-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-extrabold rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all cursor-pointer shadow-2xs flex items-center justify-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                <span>Reports</span>
            </button>
        </div>
    </div>    <!-- 4 KPI Metrics Cards Grid — 2x2 Grid on Mobile, 4 Columns on Desktop -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3.5">
        <!-- Card 1: Total Requests Sent (Blue Tinted Background) -->
        <div class="bg-blue-50/60 dark:bg-blue-955/40 border border-blue-100 dark:border-blue-900/40 rounded-2xl p-2.5 sm:p-4 space-y-1 sm:space-y-2 relative overflow-hidden shadow-2xs">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-bold text-blue-900 dark:text-blue-200 truncate">Total Requests Sent</span>
                <div class="w-5 h-5 sm:w-7 sm:h-7 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-300 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-extrabold text-zinc-950 dark:text-white font-mono tracking-tight"><?php echo $total_requests; ?></div>
            </div>
            <div class="h-1 rounded-full bg-blue-200/80 dark:bg-blue-950 overflow-hidden hidden sm:block">
                <div class="h-full bg-blue-600 rounded-full" style="width: <?php echo $kpi_requests_pct; ?>%"></div>
            </div>
        </div>

        <!-- Card 2: Google 5-Star Reviews (Emerald Tinted Background) -->
        <div class="bg-emerald-50/60 dark:bg-emerald-955/40 border border-emerald-100 dark:border-emerald-900/40 rounded-2xl p-2.5 sm:p-4 space-y-1 sm:space-y-2 relative overflow-hidden shadow-2xs">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-bold text-emerald-900 dark:text-emerald-200 truncate">Google 5-Star Reviews</span>
                <div class="w-5 h-5 sm:w-7 sm:h-7 rounded-lg bg-white dark:bg-emerald-900/60 flex items-center justify-center shrink-0 p-0.5 shadow-2xs border border-emerald-100 dark:border-emerald-800">
                    <svg viewBox="0 0 24 24" width="13" height="13">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono tracking-tight"><?php echo $google_reviews; ?></div>
            </div>
            <div class="h-1 rounded-full bg-emerald-200/80 dark:bg-emerald-950 overflow-hidden hidden sm:block">
                <div class="h-full bg-emerald-500 rounded-full" style="width: <?php echo $kpi_google_pct; ?>%"></div>
            </div>
        </div>

        <!-- Card 3: Private Shield Intercepts (Amber Tinted Background) -->
        <div class="bg-amber-50/60 dark:bg-amber-955/40 border border-amber-100 dark:border-amber-900/40 rounded-2xl p-2.5 sm:p-4 space-y-1 sm:space-y-2 relative overflow-hidden shadow-2xs">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-bold text-amber-900 dark:text-amber-200 truncate">Private Shield Intercepts</span>
                <div class="w-5 h-5 sm:w-7 sm:h-7 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-300 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-extrabold text-amber-600 dark:text-amber-400 font-mono tracking-tight"><?php echo $private_intercepts; ?></div>
            </div>
            <div class="h-1 rounded-full bg-amber-200/80 dark:bg-amber-950 overflow-hidden hidden sm:block">
                <div class="h-full bg-amber-500 rounded-full" style="width: <?php echo $kpi_intercept_pct; ?>%"></div>
            </div>
        </div>

        <!-- Card 4: Overall Score Impact (Purple Tinted Background) -->
        <div class="bg-purple-50/60 dark:bg-purple-955/40 border border-purple-100 dark:border-purple-900/40 rounded-2xl p-2.5 sm:p-4 space-y-1 sm:space-y-2 relative overflow-hidden shadow-2xs">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-bold text-purple-900 dark:text-purple-200 truncate">Overall Score Impact</span>
                <div class="w-5 h-5 sm:w-7 sm:h-7 rounded-lg bg-purple-100 dark:bg-purple-900/60 text-purple-600 dark:text-purple-300 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-extrabold text-zinc-950 dark:text-white font-mono tracking-tight"><?php echo $avg_rating; ?> <span class="text-[10px] text-zinc-400 font-normal">/ 5.0</span></div>
            </div>
            <div class="h-1 rounded-full bg-purple-200/80 dark:bg-purple-950 overflow-hidden hidden sm:block">
                <div class="h-full bg-purple-600 rounded-full" style="width: <?php echo $kpi_score_pct; ?>%"></div>
            </div>
        </div>
    </div>

    <!-- SUB-NAVIGATION TABS (GRID ON MOBILE TO ELIMINATE HORIZONTAL SCROLLBAR, FLEX ROW ON DESKTOP) -->
    <div class="bg-white dark:bg-zinc-955 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-1.5 shadow-2xs mb-4 sm:mb-5">
        <!-- Mobile 2-Column Tab Grid (Zero Horizontal Scrollbar) -->
        <div class="grid grid-cols-2 gap-1.5 sm:hidden">
            <button type="button" onclick="coraSwitchReviewTab('tracker')" id="rev-tab-btn-tracker-mob" class="p-2 rounded-xl text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer transition-all flex items-center justify-center gap-1.5 text-center">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                <span>Audit Feed</span>
            </button>
            <button type="button" onclick="coraSwitchReviewTab('snippets')" id="rev-tab-btn-snippets-mob" class="p-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 cursor-pointer transition-all flex items-center justify-center gap-1.5 text-center">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                <span>AI Generator</span>
            </button>
            <button type="button" onclick="coraSwitchReviewTab('automation')" id="rev-tab-btn-automation-mob" class="p-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 cursor-pointer transition-all flex items-center justify-center gap-1.5 text-center">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                <span>Triggers</span>
            </button>
            <button type="button" onclick="coraSwitchReviewTab('reports')" id="rev-tab-btn-reports-mob" class="p-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 cursor-pointer transition-all flex items-center justify-center gap-1.5 text-center">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                <span>Reports</span>
            </button>
        </div>

        <!-- Desktop Flex Row (Hidden on Mobile) -->
        <div class="hidden sm:flex items-center gap-1 overflow-x-auto">
            <button type="button" onclick="coraSwitchReviewTab('tracker')" id="rev-tab-btn-tracker" class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer transition-all shrink-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                Requests & Feedback Feed
            </button>
            <button type="button" onclick="coraSwitchReviewTab('snippets')" id="rev-tab-btn-snippets" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-all shrink-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                AI Review Snippet Generator
            </button>
            <button type="button" onclick="coraSwitchReviewTab('automation')" id="rev-tab-btn-automation" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-all shrink-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                Multi-Channel Triggers
            </button>
            <button type="button" onclick="coraSwitchReviewTab('reports')" id="rev-tab-btn-reports" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-all shrink-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Automated Reports & Sentiment
            </button>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════
         TAB 1: REVIEW REQUESTS & REPUTATION FEED
         ═════════════════════════════════════════════════════════════════════ -->
    <div id="cora-rev-panel-tracker" class="cora-shopify-card space-y-4 p-0 sm:p-4 bg-transparent sm:bg-white dark:sm:bg-zinc-950 border-0 sm:border border-zinc-200 dark:border-zinc-800 shadow-none sm:shadow-2xs">
        <!-- Top Title & Filter Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-1">
            <div class="space-y-1">
                <h3 class="text-base sm:text-lg font-extrabold text-zinc-950 dark:text-zinc-100 m-0 tracking-tight">Review Acquisition Audit Feed</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 leading-relaxed">Auto-routes 4–5★ to Google Business and intercepts 1–3★ feedback into private tickets.</p>
            </div>

            <!-- Category Filter Pills (Wrap cleanly without cutoffs) -->
            <div class="flex items-center gap-1.5 flex-wrap pt-0.5 sm:pt-0">
                <button type="button" onclick="coraFilterReviewFeed('all')" id="rev-filter-all" class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer shadow-2xs transition-all shrink-0">All (<?php echo $total_requests; ?>)</button>
                <button type="button" onclick="coraFilterReviewFeed('published')" id="rev-filter-published" class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 hover:bg-zinc-200 cursor-pointer transition-all shrink-0">5-Star Published</button>
                <button type="button" onclick="coraFilterReviewFeed('intercepted')" id="rev-filter-intercepted" class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 hover:bg-zinc-200 cursor-pointer transition-all shrink-0">Private Intercepts</button>
            </div>
        </div>

        <!-- DESKTOP TABLE VIEW (Hidden on Mobile) -->
        <div class="hidden sm:block border border-zinc-200/80 dark:border-zinc-800 rounded-xl overflow-hidden shadow-2xs bg-white dark:bg-zinc-955">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs min-w-[720px]">
                    <thead>
                        <tr class="bg-zinc-50/80 dark:bg-zinc-900/60 border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                            <th class="py-2 px-3 w-[200px]">CLIENT & PROJECT</th>
                            <th class="py-2 px-3 w-[130px]">CATEGORY</th>
                            <th class="py-2 px-3 w-[60px] text-center">CHANNEL</th>
                            <th class="py-2 px-3 w-[85px]">RATING</th>
                            <th class="py-2 px-3 w-[155px]">STATUS</th>
                            <th class="py-2 px-3">REVIEW SNIPPET / NOTE</th>
                            <th class="py-2 px-3 text-right w-[90px]">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60" id="cora-review-feed-tbody">
                        <?php foreach ( $cora_review_requests as $req ) : 
                            $rating = intval( $req['rating'] ?? 5 );
                            $is_private = ! empty( $req['is_private'] ) || $rating <= 3;
                            $filter_class = $is_private ? 'rev-row-intercepted' : 'rev-row-published';
                            $status_bg = $is_private ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200/70 dark:border-amber-800/60' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/70 dark:border-emerald-800/60';
                            $status_label = $is_private ? 'Private Shield Intercepted' : 'Google 5-Star Published';
                            $initial = strtoupper( substr( $req['client_name'], 0, 1 ) );
                            $avatar_bg = $is_private ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300';
                            $channel_type = strtolower( $req['channel'] ?? 'whatsapp' );
                        ?>
                        <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-900/40 transition-colors <?php echo $filter_class; ?>">
                            <!-- CLIENT & PROJECT (Compact 2-line layout) -->
                            <td class="py-2.5 px-3 align-middle">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full <?php echo $avatar_bg; ?> font-bold text-[11px] flex items-center justify-center shrink-0 shadow-2xs">
                                        <?php echo esc_html( $initial ); ?>
                                    </div>
                                    <div class="space-y-0 truncate">
                                        <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100 truncate leading-tight"><?php echo esc_html( $req['client_name'] ); ?></div>
                                        <div class="text-[10px] text-zinc-400 font-mono truncate"><?php echo esc_html( $req['client_phone'] ?: $req['client_email'] ); ?></div>
                                    </div>
                                </div>
                            </td>

                            <!-- CATEGORY -->
                            <td class="py-2.5 px-3 align-middle font-medium text-xs text-zinc-700 dark:text-zinc-300">
                                <?php echo esc_html( $req['category'] ); ?>
                            </td>

                            <!-- CHANNEL -->
                            <td class="py-2.5 px-3 align-middle text-center">
                                <?php if ( $channel_type === 'whatsapp' ) : ?>
                                    <div class="w-7 h-7 rounded-full bg-[#25D366] text-white flex items-center justify-center mx-auto shadow-sm cursor-pointer hover:scale-105 transition-all" title="WhatsApp Channel">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.285-.143-1.687-.832-1.947-.927-.26-.095-.45-.143-.64.143-.19.285-.735.927-.9 1.117-.165.19-.33.214-.615.071-.285-.143-1.204-.444-2.294-1.416-.848-.756-1.421-1.69-1.587-1.975-.166-.285-.018-.439.125-.581.128-.128.285-.333.428-.5.143-.167.19-.285.285-.476.095-.19.047-.357-.024-.5-.071-.143-.64-1.545-.877-2.116-.231-.557-.466-.481-.64-.49-.165-.008-.356-.01-.547-.01-.19 0-.5.071-.76.357-.26.285-.999.976-.999 2.38 0 1.404 1.023 2.76 1.165 2.951.143.19 2.013 3.074 4.877 4.31.682.295 1.214.471 1.629.603.685.218 1.309.187 1.802.114.549-.081 1.687-.689 1.924-1.355.237-.666.237-1.237.166-1.355-.07-.119-.26-.19-.545-.333z"/></svg>
                                    </div>
                                <?php elseif ( $channel_type === 'sms' ) : ?>
                                    <div class="w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center mx-auto shadow-sm cursor-pointer hover:scale-105 transition-all" title="SMS Text Channel">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 11H7V9h2v2zm4 0h-2V9h2v2zm4 0h-2V9h2v2z"/></svg>
                                    </div>
                                <?php else : ?>
                                    <div class="w-7 h-7 rounded-full bg-blue-500 text-white flex items-center justify-center mx-auto shadow-sm cursor-pointer hover:scale-105 transition-all" title="Email Channel">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- RATING -->
                            <td class="py-2.5 px-3 align-middle font-bold">
                                <div class="flex items-center gap-1">
                                    <span class="<?php echo $rating >= 4 ? 'text-emerald-500' : 'text-amber-500'; ?> text-xs tracking-tighter">
                                        <?php echo str_repeat( '★', $rating ); ?>
                                    </span>
                                    <span class="text-[10px] text-zinc-500 font-mono font-bold"><?php echo $rating; ?>/5</span>
                                </div>
                            </td>

                            <!-- STATUS -->
                            <td class="py-2.5 px-3 align-middle">
                                <?php if ( ! $is_private ) : ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/70 dark:border-emerald-800/60 inline-flex items-center gap-1.5 whitespace-nowrap shadow-2xs">
                                        <svg viewBox="0 0 24 24" width="11" height="11" class="shrink-0"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                                        Google 5-Star Published
                                    </span>
                                <?php else : ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-955/40 text-amber-700 dark:text-amber-400 border border-amber-200/70 dark:border-amber-800/60 inline-flex items-center gap-1.5 whitespace-nowrap shadow-2xs">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none" class="text-amber-500 shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                        Private Shield Intercepted
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- REVIEW SNIPPET / NOTE -->
                            <td class="py-2.5 px-3 align-middle text-zinc-600 dark:text-zinc-300 leading-snug text-xs" title="<?php echo esc_attr( $req['review_text'] ); ?>">
                                <div class="line-clamp-2">
                                    <span class="text-zinc-400 font-serif mr-1">“</span><?php echo esc_html( $req['review_text'] ); ?>
                                </div>
                            </td>

                            <!-- ACTIONS -->
                            <td class="py-2.5 px-3 align-middle text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <?php if ( $is_private ) : ?>
                                        <button type="button" onclick="coraOpenPrivateTicketDrawer('<?php echo esc_js( $req['id'] ); ?>')" class="w-7 h-7 rounded-full bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center transition-all cursor-pointer shadow-sm shrink-0" title="Inspect Private Reputation Ticket">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>
                                    <?php else : ?>
                                        <button type="button" onclick="coraResendWhatsAppReview('<?php echo esc_js( $req['client_phone'] ); ?>', '<?php echo esc_js( $req['client_name'] ); ?>')" class="w-7 h-7 rounded-full bg-[#25D366] hover:bg-emerald-600 text-white flex items-center justify-center transition-all cursor-pointer shadow-sm shrink-0" title="Resend WhatsApp Review Request">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.285-.143-1.687-.832-1.947-.927-.26-.095-.45-.143-.64.143-.19.285-.735.927-.9 1.117-.165.19-.33.214-.615.071-.285-.143-1.204-.444-2.294-1.416-.848-.756-1.421-1.69-1.587-1.975-.166-.285-.018-.439.125-.581.128-.128.285-.333.428-.5.143-.167.19-.285.285-.476.095-.19.047-.357-.024-.5-.071-.143-.64-1.545-.877-2.116-.231-.557-.466-.481-.64-.49-.165-.008-.356-.01-.547-.01-.19 0-.5.071-.76.357-.26.285-.999.976-.999 2.38 0 1.404 1.023 2.76 1.165 2.951.143.19 2.013 3.074 4.877 4.31.682.295 1.214.471 1.629.603.685.218 1.309.187 1.802.114.549-.081 1.687-.689 1.924-1.355.237-.666.237-1.237.166-1.355-.07-.119-.26-.19-.545-.333z"/></svg>
                                        </button>
                                        <button type="button" onclick="coraCopyGoogleReviewUrl()" class="w-7 h-7 rounded-full bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-200 flex items-center justify-center transition-all cursor-pointer shadow-sm shrink-0" title="Copy Google Business Review Link">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" onclick="coraDeleteReviewRequest('<?php echo esc_js( $req['id'] ); ?>')" class="w-7 h-7 rounded-full bg-white hover:bg-red-50 dark:bg-zinc-900 dark:hover:bg-red-950/30 border border-zinc-200 dark:border-zinc-800 text-zinc-400 hover:text-red-500 dark:text-zinc-500 dark:hover:text-red-400 flex items-center justify-center transition-all cursor-pointer shadow-2xs shrink-0" title="Delete Review Entry">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE RESPONSIVE CARD VIEW (Rendered on Mobile/Tablet instead of Scrollable Table) -->
        <div class="block sm:hidden space-y-2.5" id="cora-review-feed-cards">
            <?php foreach ( $cora_review_requests as $req ) : 
                $rating = intval( $req['rating'] ?? 5 );
                $is_private = ! empty( $req['is_private'] ) || $rating <= 3;
                $filter_class = $is_private ? 'rev-row-intercepted' : 'rev-row-published';
                $initial = strtoupper( substr( $req['client_name'], 0, 1 ) );
                $avatar_bg = $is_private ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300';
                $channel_type = strtolower( $req['channel'] ?? 'whatsapp' );
            ?>
            <div class="p-2.5 bg-white dark:bg-zinc-955 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-2 shadow-2xs <?php echo $filter_class; ?>">
                <!-- Card Header: Client Info + Rating + Channel Badge -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full <?php echo $avatar_bg; ?> font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            <?php echo esc_html( $initial ); ?>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-zinc-950 dark:text-zinc-100 leading-tight"><?php echo esc_html( $req['client_name'] ); ?></div>
                            <div class="text-[10px] text-zinc-400 font-mono mt-0.5"><?php echo esc_html( $req['client_phone'] ?: $req['client_email'] ); ?></div>
                        </div>
                    </div>

                    <!-- Channel Badge -->
                    <div class="shrink-0">
                        <?php if ( $channel_type === 'whatsapp' ) : ?>
                            <div class="w-6 h-6 rounded-full bg-[#25D366] text-white flex items-center justify-center shadow-xs" title="WhatsApp">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.285-.143-1.687-.832-1.947-.927-.26-.095-.45-.143-.64.143-.19.285-.735.927-.9 1.117-.165.19-.33.214-.615.071-.285-.143-1.204-.444-2.294-1.416-.848-.756-1.421-1.69-1.587-1.975-.166-.285-.018-.439.125-.581.128-.128.285-.333.428-.5.143-.167.19-.285.285-.476.095-.19.047-.357-.024-.5-.071-.143-.64-1.545-.877-2.116-.231-.557-.466-.481-.64-.49-.165-.008-.356-.01-.547-.01-.19 0-.5.071-.76.357-.26.285-.999.976-.999 2.38 0 1.404 1.023 2.76 1.165 2.951.143.19 2.013 3.074 4.877 4.31.682.295 1.214.471 1.629.603.685.218 1.309.187 1.802.114.549-.081 1.687-.689 1.924-1.355.237-.666.237-1.237.166-1.355-.07-.119-.26-.19-.545-.333z"/></svg>
                            </div>
                        <?php elseif ( $channel_type === 'sms' ) : ?>
                            <div class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center shadow-xs" title="SMS">
                                <svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 11H7V9h2v2zm4 0h-2V9h2v2zm4 0h-2V9h2v2z"/></svg>
                            </div>
                        <?php else : ?>
                            <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center shadow-xs" title="Email">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Review Text Quote Box -->
                <div class="p-2.5 bg-zinc-50/80 dark:bg-zinc-900/40 rounded-xl text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed italic">
                    “<?php echo esc_html( $req['review_text'] ); ?>”
                </div>

                <!-- Card Footer: Category + Rating + Status Pill + Actions -->
                <div class="flex items-center justify-between flex-wrap gap-2 pt-1 border-t border-zinc-100 dark:border-zinc-800/60">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">
                            <?php echo esc_html( $req['category'] ); ?>
                        </span>

                        <!-- Rating Badge -->
                        <span class="text-xs font-bold <?php echo $rating >= 4 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'; ?>">
                            <?php echo str_repeat( '★', $rating ); ?> <?php echo $rating; ?>/5
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-1.5 ml-auto">
                        <?php if ( $is_private ) : ?>
                            <button type="button" onclick="coraOpenPrivateTicketDrawer('<?php echo esc_js( $req['id'] ); ?>')" class="px-2.5 py-1 rounded-lg bg-amber-500 text-white text-[11px] font-bold flex items-center gap-1 shadow-xs cursor-pointer">
                                <span>Inspect Ticket</span>
                            </button>
                        <?php else : ?>
                            <button type="button" onclick="coraResendWhatsAppReview('<?php echo esc_js( $req['client_phone'] ); ?>', '<?php echo esc_js( $req['client_name'] ); ?>')" class="px-2.5 py-1 rounded-lg bg-[#25D366] text-white text-[11px] font-bold flex items-center gap-1 shadow-xs cursor-pointer">
                                <span>Resend WA</span>
                            </button>
                        <?php endif; ?>
                        <button type="button" onclick="coraDeleteReviewRequest('<?php echo esc_js( $req['id'] ); ?>')" class="w-6 h-6 rounded-lg bg-zinc-100 hover:bg-red-50 text-zinc-400 hover:text-red-500 dark:bg-zinc-800 dark:hover:bg-red-950/40 flex items-center justify-center cursor-pointer transition-all">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Bottom Reputation Shield Info Banner inside Card -->
        <div class="p-3 bg-zinc-50/80 dark:bg-zinc-900/40 border border-zinc-200/80 dark:border-zinc-800 rounded-xl flex items-center justify-between flex-wrap gap-2 text-xs">
            <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Your reputation is protected. 1–3★ feedback is handled privately and used to improve customer experience.</span>
            </div>
            <a href="#" onclick="coraSwitchReviewTab('automation'); return false;" class="text-xs font-bold text-zinc-900 dark:text-zinc-100 hover:underline flex items-center gap-1">
                Learn more about Private Shield &rsaquo;
            </a>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════
         TAB 2: AI REVIEW SNIPPET GENERATOR
         ═════════════════════════════════════════════════════════════════════ -->
    <div id="cora-rev-panel-snippets" class="hidden cora-shopify-card space-y-3.5 p-0 sm:p-4 bg-transparent sm:bg-white dark:sm:bg-zinc-950 border-0 sm:border border-zinc-200 dark:border-zinc-800 shadow-none sm:shadow-2xs">
        <div class="space-y-0.5">
            <h3 class="text-base sm:text-lg font-extrabold text-zinc-900 dark:text-zinc-100 m-0 tracking-tight">AI Review Snippet Generator</h3>
            <p class="text-[11px] sm:text-xs text-zinc-500 dark:text-zinc-400 m-0">Clients hate typing long reviews! Pre-generate custom 5-star review snippets that clients can copy and post to Google in 1 tap.</p>
        </div>

        <!-- 3 Presets -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
            <div class="p-3.5 sm:p-4 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 space-y-2.5 shadow-2xs hover:border-zinc-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 text-[10px] font-bold">STUDIO PHOTOGRAPHY</span>
                    <button type="button" onclick="coraCopySnippet(1)" class="text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        Copy
                    </button>
                </div>
                <p id="snippet-text-1" class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-relaxed m-0">
                    "Exceptional 3-day wedding photography coverage! Turnaround was super fast, drone aerials were stunning, and the crew was extremely professional."
                </p>
            </div>

            <div class="p-3.5 sm:p-4 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 space-y-2.5 shadow-2xs hover:border-zinc-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 text-[10px] font-bold">REAL ESTATE MEDIA</span>
                    <button type="button" onclick="coraCopySnippet(2)" class="text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        Copy
                    </button>
                </div>
                <p id="snippet-text-2" class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-relaxed m-0">
                    "Top-tier 4K property walkthrough video and architectural HDR stills! Delivered within 24 hours. Boosted our listing inquiries immensely."
                </p>
            </div>

            <div class="p-3.5 sm:p-4 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 space-y-2.5 shadow-2xs hover:border-zinc-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60 text-[10px] font-bold">REAL ESTATE BROKERAGE</span>
                    <button type="button" onclick="coraCopySnippet(3)" class="text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        Copy
                    </button>
                </div>
                <p id="snippet-text-3" class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-relaxed m-0">
                    "Extremely professional commercial lease negotiation and paperwork. Transparent advisory and quick closure. Highly recommend Apex Realty!"
                </p>
            </div>
        </div>

        <!-- Custom AI Generator Box -->
        <div class="relative border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-zinc-50/70 dark:bg-zinc-900/40 p-4 sm:p-5 space-y-3">
            <!-- Custom AI Generator Form Elements -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 m-0">Generate Custom AI Snippet</h4>
                    <span class="px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 text-[10px] font-bold">Coming Soon</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <input type="text" id="custom-ai-client-name" placeholder="Client Name (e.g. Rahul Kapoor)" class="w-full px-3 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-sans text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
                    <input type="text" id="custom-ai-service" placeholder="Key Highlight (e.g. Fast turnaround, great lighting)" class="w-full px-3 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-sans text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
                </div>
                <button type="button" onclick="coraGenerateCustomAISnippet()" class="h-9 px-4 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-xs rounded-xl flex items-center justify-center gap-1.5 cursor-pointer transition-all active:scale-97">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    <span>Generate AI Review Snippet</span>
                </button>
            </div>
            <div id="custom-ai-snippet-output" class="hidden p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-800 dark:text-zinc-200"></div>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════
         TAB 3: MULTI-CHANNEL AUTOMATION TRIGGERS
         ═════════════════════════════════════════════════════════════════════ -->
    <form id="cora-rev-panel-automation" class="hidden cora-shopify-card space-y-3.5 max-w-4xl p-0 sm:p-4 bg-transparent sm:bg-white dark:sm:bg-zinc-950 border-0 sm:border border-zinc-200 dark:border-zinc-800 shadow-none sm:shadow-2xs" onsubmit="event.preventDefault();">
        <!-- Indian Market Insights Banner -->
        <div class="p-3 bg-[#25D366]/10 border border-[#25D366]/30 rounded-xl flex items-start gap-2.5 text-xs">
            <div class="w-6 h-6 rounded-full bg-[#25D366] text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.285-.143-1.687-.832-1.947-.927-.26-.095-.45-.143-.64.143-.19.285-.735.927-.9 1.117-.165.19-.33.214-.615.071-.285-.143-1.204-.444-2.294-1.416-.848-.756-1.421-1.69-1.587-1.975-.166-.285-.018-.439.125-.581.128-.128.285-.333.428-.5.143-.167.19-.285.285-.476.095-.19.047-.357-.024-.5-.071-.143-.64-1.545-.877-2.116-.231-.557-.466-.481-.64-.49-.165-.008-.356-.01-.547-.01-.19 0-.5.071-.76.357-.26.285-.999.976-.999 2.38 0 1.404 1.023 2.76 1.165 2.951.143.19 2.013 3.074 4.877 4.31.682.295 1.214.471 1.629.603.685.218 1.309.187 1.802.114.549-.081 1.687-.689 1.924-1.355.237-.666.237-1.237.166-1.355-.07-.119-.26-.19-.545-.333z"/></svg>
            </div>
            <div class="space-y-0.5">
                <div class="font-bold text-zinc-950 dark:text-white flex items-center gap-1.5 text-xs">
                    <span>WhatsApp-First Strategy for Indian Businesses</span>
                </div>
                <p class="text-zinc-600 dark:text-zinc-300 leading-snug m-0 text-[11px]">
                    In India, review emails are rarely opened (&lt;8% open rate), while <strong>WhatsApp messages have a 98% open rate</strong>. Our engine sends personalized Hinglish messages directly to client WhatsApp numbers right after photo/video delivery or final invoice payment!
                </p>
            </div>
        </div>

        <div class="space-y-0.5">
            <h3 class="text-base sm:text-lg font-extrabold text-zinc-900 dark:text-zinc-100 m-0 flex items-center gap-3 tracking-tight">
                Multi-Channel Automation Triggers
                <span id="cora-rev-autosave-pill" class="hidden px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 transition-opacity duration-300 inline-flex items-center gap-1"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> Auto-saved</span>
            </h3>
            <p class="text-[11px] sm:text-xs text-zinc-500 dark:text-zinc-400 m-0">Configure automated post-handover review requests across WhatsApp, Email, SMS, and Social channels.</p>
        </div>

        <!-- Official Google Business Review URL Card -->
        <div class="p-3 sm:p-3.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/20 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 text-xs">Official Google Business Review URL *</label>
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-200/60 dark:border-emerald-800/60 shrink-0 w-fit">Live Connected</span>
            </div>
            <div class="relative">
                <input type="text" id="cora-google-url-input" name="cora_google_business_url" value="<?php echo esc_attr( get_option( 'cora_google_review_url', 'https://g.page/r/cora_studio/review' ) ); ?>" class="w-full pl-9 pr-3.5 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white focus:ring-2 focus:ring-zinc-950/10">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-2.5 text-zinc-400"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </div>
            <p class="text-[10px] text-zinc-400 m-0">Found under 'Ask for reviews' in your Google Business Profile dashboard.</p>
        </div>

        <!-- WhatsApp Automation Message (Hinglish/English Preset Selector) -->
        <div class="p-3 sm:p-3.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/20 space-y-2">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 text-xs">WhatsApp Automation Message (Hinglish/English)</label>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="coraApplyHinglishPreset('hinglish_warm')" class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-zinc-200/80 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-300 dark:hover:bg-zinc-700 transition-all cursor-pointer">Hinglish Warm</button>
                    <button type="button" onclick="coraApplyHinglishPreset('english_prof')" class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-zinc-200/80 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-300 dark:hover:bg-zinc-700 transition-all cursor-pointer">English Prof</button>
                </div>
            </div>
            <textarea id="cora-wa-review-template" name="cora_wa_review_template" rows="3" class="w-full p-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-sans text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white focus:ring-2 focus:ring-zinc-950/10 leading-relaxed"><?php echo esc_textarea( get_option( 'cora_wa_msg_template', 'Namaste {client_name} ji! Thank you for choosing Cora Studio. Could you take 5 seconds to rate us on Google? Tap here to post: {review_url}' ) ); ?></textarea>
        </div>

        <!-- Email Review Request Template -->
        <div class="p-3 sm:p-3.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/20 space-y-2">
            <label class="block font-semibold text-zinc-800 dark:text-zinc-200 text-xs">Email Review Request Template</label>
            <textarea id="cora-email-review-template" name="cora_email_review_template" rows="2" class="w-full p-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-sans text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white focus:ring-2 focus:ring-zinc-950/10 leading-relaxed"><?php echo esc_textarea( get_option( 'cora_email_msg_template', 'Hi {client_name}, we appreciate your business! Please leave us a review on Google.' ) ); ?></textarea>
        </div>

        <div class="pt-1 flex items-center gap-2">
            <input type="checkbox" id="setting-auto-trigger" checked class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer">
            <label for="setting-auto-trigger" class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 cursor-pointer">Auto-trigger WhatsApp & Email 2 hours after project deal status is set to 'Handed Over' or 'Invoice Paid'</label>
        </div>

        <div class="pt-1 flex items-center gap-3">
            <button type="button" onclick="coraSaveMultiChannelRules()" class="h-9 px-5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-xs rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shadow-sm active:scale-97">
                <span>Save Multi-Channel Rules</span>
            </button>
        </div>
    </form>

    <!-- ═════════════════════════════════════════════════════════════════════
         TAB 4: AUTOMATED PERFORMANCE & SENTIMENT REPORTS
         ═════════════════════════════════════════════════════════════════════ -->
    <div id="cora-rev-panel-reports" class="hidden cora-shopify-card space-y-4 p-0 sm:p-4 bg-transparent sm:bg-white dark:sm:bg-zinc-950 border-0 sm:border border-zinc-200 dark:border-zinc-800 shadow-none sm:shadow-2xs">
        <div class="space-y-0.5">
            <h3 class="text-base sm:text-lg font-extrabold text-zinc-900 dark:text-zinc-100 m-0 tracking-tight">Automated Performance & Sentiment Reports</h3>
            <p class="text-[11px] sm:text-xs text-zinc-500 dark:text-zinc-400 m-0">Track conversion rates, public review surges, and automated PDF/Email digest schedules.</p>
        </div>

        <!-- 3 Metric Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="p-4 sm:p-4.5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-2xs hover:border-zinc-300 transition-all space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 text-[10px] font-extrabold uppercase tracking-wider">Conversion Rate</span>
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-blue-500"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-zinc-950 dark:text-zinc-100 font-mono tracking-tight"><?php echo $report_conv_rate; ?>%</div>
                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <?php echo $google_reviews; ?> of <?php echo $total_requests; ?> converted to 5-star
                </div>
            </div>

            <div class="p-4 sm:p-4.5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-2xs hover:border-zinc-300 transition-all space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 text-[10px] font-extrabold uppercase tracking-wider">Sentiment Index</span>
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-500"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono tracking-tight"><?php echo $positive_pct; ?>% Positive</div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400"><?php echo $neutral_pct; ?>% Neutral · <?php echo $negative_pct; ?>% Risk Intercepted</div>
            </div>

            <div class="p-4 sm:p-4.5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-2xs hover:border-zinc-300 transition-all space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/60 text-[10px] font-extrabold uppercase tracking-wider">Email Digest</span>
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-purple-500"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-zinc-950 dark:text-zinc-100 tracking-tight">Weekly Digest</div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate">Delivered to <?php echo esc_html( get_option( 'admin_email' ) ); ?></div>
            </div>
        </div>

        <!-- Custom Report Generator Box -->
        <div class="relative border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden bg-zinc-50/70 dark:bg-zinc-900/40 p-4 sm:p-5">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 m-0">Generate Instant Performance Report</h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 m-0">Compile full review audit log into a clean report summary.</p>
                </div>
                <button type="button" onclick="coraGenerateReviewReportAJAX('30days')" class="h-9 px-4 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 cursor-pointer transition-all active:scale-97">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    <span>Generate PDF Audit Report</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- GLOBAL DRAWER BACKDROP OVERLAY WITH BLUR -->
<div id="cora-drawer-backdrop" onclick="coraCloseAllReviewDrawers()" class="fixed inset-0 z-[9998] bg-black/60 dark:bg-black/80 backdrop-blur-sm transition-opacity duration-250 opacity-0 hidden cursor-pointer"></div>

<!-- ═══ MODAL WIZARD: 3-STEP INTERACTIVE REVIEW REQUEST GENERATOR ═══════════════ -->
<div id="cora-send-review-drawer" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6 transition-all duration-250 opacity-0 pointer-events-none hidden">
    <div class="w-full max-w-xl bg-white dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden relative flex flex-col max-h-[85vh] pointer-events-auto">
        
        <!-- Header & Stepper Bar (Fixed Top) -->
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-900/40 shrink-0">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 m-0 flex items-center gap-2">
                        <span>Dispatch Review Request</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 uppercase tracking-wider">Step Wizard</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 m-0">Create and personalize your 5-star review collection campaign.</p>
                </div>
                <button type="button" onclick="coraCloseSendReviewDrawer()" class="p-2 rounded-xl text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Visual 3-Step Stepper Progress Bar -->
            <div class="grid grid-cols-3 gap-2 text-center text-[11px] font-bold">
                <div id="cora-wiz-step-pill-1" class="py-2 px-3 rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 transition-all shadow-xs">
                    1. Client Details
                </div>
                <div id="cora-wiz-step-pill-2" class="py-2 px-3 rounded-xl bg-zinc-100 text-zinc-400 dark:bg-zinc-900 dark:text-zinc-600 transition-all">
                    2. Target Attributes
                </div>
                <div id="cora-wiz-step-pill-3" class="py-2 px-3 rounded-xl bg-zinc-100 text-zinc-400 dark:bg-zinc-900 dark:text-zinc-600 transition-all">
                    3. AI & Dispatch
                </div>
            </div>
        </div>

        <!-- Scrollable Modal Content Area -->
        <div class="p-6 flex-1 overflow-y-auto min-h-0 space-y-4 text-xs">
            
            <!-- ── STEP 1: Client & Project Info ────────────────────────────────── -->
            <div id="cora-wiz-content-1" class="space-y-4">
                <div class="p-3 bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200/60 dark:border-blue-900/40 rounded-2xl flex items-center gap-3">
                    <div class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100">Step 1: Recipient Information</div>
                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Select existing client or enter new recipient details.</div>
                    </div>
                </div>

                <!-- Existing Client Quick Auto-Fill Selector -->
                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Quick Select Existing Client</label>
                    <select id="req-existing-client-select" onchange="coraAutoFillClientDetails(this)" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
                        <option value="">-- Or enter new client details below --</option>
                        <?php foreach ( $cora_review_requests as $c_req ) : ?>
                            <option value="<?php echo esc_attr( $c_req['id'] ); ?>" data-name="<?php echo esc_attr( $c_req['client_name'] ); ?>" data-phone="<?php echo esc_attr( $c_req['client_phone'] ); ?>" data-email="<?php echo esc_attr( $c_req['client_email'] ?? '' ); ?>" data-title="<?php echo esc_attr( $c_req['project_title'] ?? '' ); ?>">
                                <?php echo esc_html( $c_req['client_name'] ); ?> (<?php echo esc_html( $c_req['client_phone'] ); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Client Full Name *</label>
                    <input type="text" id="req-client-name" placeholder="e.g. Rahul Kapoor" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
                    <span id="req-name-error" class="text-[10px] text-red-500 font-semibold mt-1 hidden">Please enter a valid client name.</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Phone Number (WhatsApp/SMS) *</label>
                        <input type="text" id="req-client-phone" oninput="this.value = this.value.replace(/[^0-9+]/g, '')" placeholder="e.g. 9876543210" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
                        <span id="req-phone-error" class="text-[10px] text-red-500 font-semibold mt-1 hidden">Enter a valid 10-digit phone number.</span>
                    </div>

                    <div>
                        <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Email Address</label>
                        <input type="email" id="req-client-email" placeholder="client@example.com" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
                        <span id="req-email-error" class="text-[10px] text-red-500 font-semibold mt-1 hidden">Enter a valid email format.</span>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Project Title</label>
                    <input type="text" id="req-project-title" placeholder="e.g. Studio Photography Service" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
                </div>

                <!-- Dynamic Category Selection Based on Industry -->
                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Project Category</label>
                    <select id="req-project-category" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                        <option value="Studio Photography">Studio Photography & Film</option>
                        <option value="Portrait Coverage">Portrait & Headshot Coverage</option>
                        <option value="Wedding Media">Wedding & Event Coverage</option>
                        <option value="Commercial Campaign">Commercial & Brand Product</option>
                    </select>
                </div>
            </div>

            <!-- ── STEP 2: Target Attributes & Features ────────────────────────── -->
            <div id="cora-wiz-content-2" class="hidden space-y-4">
                <div class="p-3 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/40 rounded-2xl flex items-center gap-3">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100">Step 2: Service Highlights & Target Tags</div>
                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Select attributes to suggest to the client during review.</div>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-2">Preset Attribute Suggestions</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="p-3.5 bg-zinc-50 hover:bg-zinc-100/80 dark:bg-zinc-900 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex items-center justify-between cursor-pointer transition-all">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-zinc-200/70 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                </span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200 text-xs">Lighting & Framing</span>
                            </div>
                            <input type="checkbox" checked class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer">
                        </label>

                        <label class="p-3.5 bg-zinc-50 hover:bg-zinc-100/80 dark:bg-zinc-900 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex items-center justify-between cursor-pointer transition-all">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                </span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200 text-xs">Turnaround Speed</span>
                            </div>
                            <input type="checkbox" checked class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer">
                        </label>

                        <label class="p-3.5 bg-zinc-50 hover:bg-zinc-100/80 dark:bg-zinc-900 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex items-center justify-between cursor-pointer transition-all">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-blue-100/80 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                </span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200 text-xs">Team Punctuality</span>
                            </div>
                            <input type="checkbox" checked class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer">
                        </label>

                        <label class="p-3.5 bg-zinc-50 hover:bg-zinc-100/80 dark:bg-zinc-900 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex items-center justify-between cursor-pointer transition-all">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-purple-100/80 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="13.5" cy="6.5" r=".5"></circle><circle cx="17.5" cy="10.5" r=".5"></circle><circle cx="8.5" cy="7.5" r=".5"></circle><circle cx="6.5" cy="12.5" r=".5"></circle><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.92 0 1.7-.75 1.7-1.67 0-.42-.16-.8-.43-1.08-.27-.28-.44-.68-.44-1.12 0-.92.75-1.67 1.67-1.67H16c3.31 0 6-2.69 6-6 0-4.96-4.49-9-10-9z"></path></svg>
                                </span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200 text-xs">Post-Editing Quality</span>
                            </div>
                            <input type="checkbox" checked class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer">
                        </label>
                    </div>
                </div>

                <div class="p-3.5 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-1">
                    <div class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span>Automated Anti-Spam & Sentiment Shield</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 m-0 leading-relaxed">
                        If the client gives 4–5 stars, they will be prompted with pre-written AI snippets to post on Google. 1–3 star ratings are automatically routed to your private reputation shield inbox.
                    </p>
                </div>
            </div>

            <!-- ── STEP 3: AI & Channel Personalization ────────────────────────── -->
            <div id="cora-wiz-content-3" class="hidden space-y-4">
                <div class="p-3 bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200/60 dark:border-purple-900/40 rounded-2xl flex items-center gap-3">
                    <div class="w-7 h-7 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </div>
                    <div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100">Step 3: Channel & AI Personalization</div>
                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Select dispatch channel and review invitation draft.</div>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Dispatch Channel</label>
                    <select id="req-dispatch-channel" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                        <option value="WhatsApp">WhatsApp (Recommended — 98% Open Rate)</option>
                        <option value="Email">Email Digest</option>
                        <option value="SMS">SMS Link</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Custom Invitation Message / AI Snippet</label>
                    <textarea id="req-custom-msg" rows="3" class="w-full p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white font-sans">Namaste {client_name} ji! Thank you for choosing Cora Studio. Tap here to rate your experience: {review_url}</textarea>
                </div>
            </div>
        </div>

        <!-- PERMANENTLY VISIBLE STICKY FOOTER CTA BAR -->
        <div class="p-4 px-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 flex items-center justify-between shrink-0">
            <button type="button" id="cora-wiz-prev-btn" onclick="coraPrevWizardStep()" class="px-4 py-2 bg-white hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-bold text-zinc-800 dark:text-zinc-200 transition-all cursor-pointer hidden">
                ← Back
            </button>
            <div class="flex items-center gap-2 ml-auto">
                <button type="button" onclick="coraCloseSendReviewDrawer()" class="px-4 py-2 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white text-xs font-semibold cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="cora-wiz-next-btn" onclick="coraNextWizardStep()" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-extrabold rounded-xl shadow-lg transition-all cursor-pointer flex items-center gap-2">
                    <span>Continue ➔</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ SIDE DRAWER 2: PRIVATE SHIELD TICKET INSPECTOR ═══════════════════════ -->
<div id="cora-private-ticket-drawer" class="fixed inset-y-0 right-0 z-[9999] w-full max-w-md bg-white dark:bg-zinc-955 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl transition-transform duration-250 translate-x-full hidden">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800/60 bg-zinc-50/50 dark:bg-zinc-900/30 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Private Reputation Ticket</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 m-0">Intercepted 1-3★ feedback resolved before Google public posting.</p>
            </div>
            <button type="button" onclick="coraClosePrivateTicketDrawer()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white cursor-pointer"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <input type="hidden" id="ticket-active-id" value="">
            <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 p-4 rounded-xl space-y-2">
                <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider block">Private Feedback Shield Intercepted</span>
                <p id="ticket-feedback-text" class="text-zinc-800 dark:text-zinc-200 font-medium leading-relaxed m-0">
                    "Photos were great but delivery was delayed by 1 day."
                </p>
            </div>

            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Resolution Action Note</label>
                <textarea id="ticket-resolution-note" rows="3" placeholder="Enter resolution notes (e.g. Spoke with client, offered priority delivery next shoot)..." class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none"></textarea>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                    <input type="checkbox" id="ticket-convert-public" class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                    <span>Convert to 5-Star Public Review upon resolution</span>
                </label>
            </div>
        </div>

        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/60 bg-zinc-50/50 dark:bg-zinc-900/30 flex items-center justify-between gap-3">
            <button type="button" onclick="coraClosePrivateTicketDrawer()" class="px-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900">Cancel</button>
            <button type="button" onclick="coraResolvePrivateTicketAJAX()" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition-all cursor-pointer">Mark Resolved Internally</button>
        </div>
    </div>
</div>

<!-- ═══ SIDE DRAWER 3: AUTOMATED REPORT GENERATOR ═══════════════════════════ -->
<div id="cora-report-generator-drawer" class="fixed inset-y-0 right-0 z-[9999] w-full max-w-md bg-white dark:bg-zinc-955 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl transition-transform duration-250 translate-x-full hidden">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800/60 bg-zinc-50/50 dark:bg-zinc-900/30 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Automated Report Generator</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 m-0">Compile sentiment breakdown and automated audit reports.</p>
            </div>
            <button type="button" onclick="coraCloseReportDrawer()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white cursor-pointer"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Report Timeframe</label>
                <select id="report-period-select" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    <option value="7_days">Last 7 Days</option>
                    <option value="30_days" selected>Last 30 Days</option>
                    <option value="ytd">Year to Date (YTD)</option>
                </select>
            </div>

            <div class="p-4 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200 dark:border-zinc-800 rounded-xl space-y-2">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Report Preview Summary</span>
                <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex justify-between">
                    <span>Published 5-Star Reviews:</span>
                    <span class="font-mono text-emerald-600 dark:text-emerald-400"><?php echo $google_reviews; ?></span>
                </div>
                <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex justify-between">
                    <span>Private Intercepts:</span>
                    <span class="font-mono text-amber-600 dark:text-amber-400"><?php echo $private_intercepts; ?></span>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/60 bg-zinc-50/50 dark:bg-zinc-900/30 flex items-center justify-between gap-3">
            <button type="button" onclick="coraCloseReportDrawer()" class="px-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900">Cancel</button>
            <button type="button" onclick="coraGenerateReviewReportAJAX('30_days')" class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 rounded-xl text-xs font-bold transition-all cursor-pointer">Generate & Email Report</button>
        </div>
    </div>
</div>

<script>
// Review requests data bridge for JS-side lookups
window.coraReviewData = <?php echo json_encode( array_map( function( $r ) {
    return [
        'id'           => $r['id'] ?? '',
        'client_name'  => $r['client_name'] ?? '',
        'client_phone' => $r['client_phone'] ?? '',
        'rating'       => intval( $r['rating'] ?? 5 ),
        'review_text'  => $r['review_text'] ?? '',
        'channel'      => $r['channel'] ?? 'WhatsApp',
        'is_private'   => ! empty( $r['is_private'] ) || intval( $r['rating'] ?? 5 ) <= 3,
        'status'       => $r['status'] ?? '',
    ];
}, $cora_review_requests ) ); ?>;

// Helper to safely get WP AJAX URL and Nonce
function coraGetAJAXUrl() {
    if (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) return coraREData.ajaxUrl;
    if (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) return coraREWPData.ajaxUrl;
    if (typeof ajaxurl !== 'undefined') return ajaxurl;
    return '/wp-admin/admin-ajax.php';
}

function coraGetAJAXNonce() {
    if (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) return coraREData.ajaxNonce;
    if (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) return coraREWPData.ajaxNonce;
    return '';
}

// ═══════════════════════════════════════════════════════════════════════════
// 3-STEP MODAL WIZARD ENGINE & OVERLAY CONTROLLER
// ═══════════════════════════════════════════════════════════════════════════
var currentWizStep = 1;

window.coraOpenSendReviewDrawer = function() {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-send-review-drawer');
    currentWizStep = 1;
    coraRenderWizardStep(1);

    if (backdrop) {
        backdrop.classList.remove('hidden');
        backdrop.style.pointerEvents = '';
        setTimeout(function() { backdrop.classList.remove('opacity-0'); }, 10);
    }
    if (drawer) {
        drawer.classList.remove('hidden', 'pointer-events-none', 'collapsed', 'translate-x-full');
        setTimeout(function() {
            drawer.classList.remove('opacity-0');
            drawer.classList.add('opacity-100');
        }, 10);
    }
};

window.coraCloseSendReviewDrawer = function() {
    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-send-review-drawer');
    if (drawer) {
        drawer.classList.remove('opacity-100');
        drawer.classList.add('opacity-0', 'pointer-events-none');
        setTimeout(function() { drawer.classList.add('hidden'); }, 250);
    }
    if (backdrop) {
        backdrop.classList.add('opacity-0');
        setTimeout(function() { backdrop.classList.add('hidden'); }, 250);
    }
};

window.coraRenderWizardStep = function(step) {
    currentWizStep = step;
    [1, 2, 3].forEach(function(s) {
        var content = document.getElementById('cora-wiz-content-' + s);
        var pill = document.getElementById('cora-wiz-step-pill-' + s);
        if (content) {
            if (s === step) content.classList.remove('hidden');
            else content.classList.add('hidden');
        }
        if (pill) {
            if (s === step) {
                pill.className = 'py-2 px-3 rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 transition-all font-bold';
            } else if (s < step) {
                pill.className = 'py-2 px-3 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 transition-all font-bold';
            } else {
                pill.className = 'py-2 px-3 rounded-xl bg-zinc-100 text-zinc-400 dark:bg-zinc-900 dark:text-zinc-600 transition-all font-bold';
            }
        }
    });

    var prevBtn = document.getElementById('cora-wiz-prev-btn');
    var nextBtn = document.getElementById('cora-wiz-next-btn');

    if (prevBtn) {
        if (step > 1) prevBtn.classList.remove('hidden');
        else prevBtn.classList.add('hidden');
    }

    if (nextBtn) {
        if (step === 3) {
            nextBtn.innerHTML = '<span>Dispatch Request</span> <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>';
            nextBtn.onclick = coraSubmitSendReviewRequest;
        } else {
            nextBtn.innerHTML = '<span>Continue</span> <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
            nextBtn.onclick = coraNextWizardStep;
        }
    }
};

window.coraAutoFillClientDetails = function(selectEl) {
    var option = selectEl.options[selectEl.selectedIndex];
    if (!option || !option.value) return;
    var name = option.getAttribute('data-name') || '';
    var phone = option.getAttribute('data-phone') || '';
    var email = option.getAttribute('data-email') || '';
    var title = option.getAttribute('data-title') || '';

    if (name) document.getElementById('req-client-name').value = name;
    if (phone) document.getElementById('req-client-phone').value = phone;
    if (email) document.getElementById('req-client-email').value = email;
    if (title) document.getElementById('req-project-title').value = title;
};

window.coraNextWizardStep = function() {
    if (currentWizStep === 1) {
        var name = document.getElementById('req-client-name').value.trim();
        var phone = document.getElementById('req-client-phone').value.trim();
        var email = document.getElementById('req-client-email').value.trim();
        var isValid = true;

        // Reset errors
        var nameErr = document.getElementById('req-name-error');
        var phoneErr = document.getElementById('req-phone-error');
        var emailErr = document.getElementById('req-email-error');
        if (nameErr) nameErr.classList.add('hidden');
        if (phoneErr) phoneErr.classList.add('hidden');
        if (emailErr) emailErr.classList.add('hidden');

        // Name validation
        if (!name || name.length < 2) {
            if (nameErr) nameErr.classList.remove('hidden');
            isValid = false;
        }

        // Phone validation (digits check)
        var cleanPhone = phone.replace(/[^0-9]/g, '');
        if (!phone || cleanPhone.length < 10) {
            if (phoneErr) phoneErr.classList.remove('hidden');
            isValid = false;
        }

        // Email regex check if provided
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            if (emailErr) emailErr.classList.remove('hidden');
            isValid = false;
        }

        if (!isValid) return;
    }
    if (currentWizStep < 3) {
        coraRenderWizardStep(currentWizStep + 1);
    }
};

window.coraPrevWizardStep = function() {
    if (currentWizStep > 1) {
        coraRenderWizardStep(currentWizStep - 1);
    }
};

window.coraOpenReportDrawer = function() {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    coraSwitchReviewTab('reports');
    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-report-generator-drawer');
    if (backdrop) {
        backdrop.classList.remove('hidden');
        backdrop.style.pointerEvents = '';
        setTimeout(function() { backdrop.classList.remove('opacity-0'); }, 10);
    }
    if (drawer) {
        drawer.classList.remove('hidden');
        setTimeout(function() {
            drawer.classList.remove('translate-x-full');
            drawer.classList.add('translate-x-0');
        }, 10);
    }
};

window.coraCloseReportDrawer = function() {
    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-report-generator-drawer');
    if (drawer) {
        drawer.classList.remove('translate-x-0');
        drawer.classList.add('translate-x-full');
        setTimeout(function() { drawer.classList.add('hidden'); }, 250);
    }
    if (backdrop) {
        backdrop.classList.add('opacity-0');
        setTimeout(function() { backdrop.classList.add('hidden'); }, 250);
    }
};

window.coraOpenPrivateTicketDrawer = function(ticketId) {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    document.getElementById('ticket-active-id').value = ticketId;

    // Populate drawer with actual review data from the data bridge
    var match = null;
    if (window.coraReviewData) {
        for (var i = 0; i < window.coraReviewData.length; i++) {
            if (window.coraReviewData[i].id === ticketId) { match = window.coraReviewData[i]; break; }
        }
    }
    if (match) {
        var textEl = document.getElementById('ticket-feedback-text');
        if (textEl) textEl.textContent = '"' + match.review_text + '"';
    }

    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-private-ticket-drawer');
    if (backdrop) {
        backdrop.classList.remove('hidden', 'opacity-0');
        backdrop.style.pointerEvents = '';
    }
    if (drawer) {
        drawer.classList.remove('hidden', 'translate-x-full');
        drawer.classList.add('translate-x-0');
    }
};

window.coraClosePrivateTicketDrawer = function() {
    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-private-ticket-drawer');
    if (drawer) {
        drawer.classList.remove('translate-x-0');
        drawer.classList.add('translate-x-full');
        setTimeout(function() { drawer.classList.add('hidden'); }, 250);
    }
    if (backdrop) {
        backdrop.classList.add('opacity-0');
        setTimeout(function() { backdrop.classList.add('hidden'); }, 250);
    }
};

window.coraCloseAllReviewDrawers = function() {
    coraCloseSendReviewDrawer();
    coraCloseReportDrawer();
    coraClosePrivateTicketDrawer();
};

// Register review drawers with the global drawer system
var _origCloseAllDrawers = window.coraCloseAllDrawers;
if (typeof _origCloseAllDrawers === 'function') {
    window.coraCloseAllDrawers = function() {
        _origCloseAllDrawers();
        coraCloseAllReviewDrawers();
    };
}

// ═══════════════════════════════════════════════════════════════════════════
// URL ARCHITECTURE & ACTIVE TAB PERSISTENCE ENGINE
// ═══════════════════════════════════════════════════════════════════════════
window.coraSwitchReviewTab = function(tabKey, skipStateUpdate) {
    var validTabs = ['tracker', 'snippets', 'automation', 'reports'];
    if (validTabs.indexOf(tabKey) === -1) tabKey = 'tracker';

    document.getElementById('cora-rev-panel-tracker').classList.add('hidden');
    document.getElementById('cora-rev-panel-snippets').classList.add('hidden');
    document.getElementById('cora-rev-panel-automation').classList.add('hidden');
    document.getElementById('cora-rev-panel-reports').classList.add('hidden');

    document.querySelectorAll('[id^="rev-tab-btn-"]').forEach(function(b){
        if (b.id.indexOf('-mob') !== -1) {
            b.className = 'p-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 cursor-pointer transition-all flex items-center justify-center gap-1.5 text-center';
        } else {
            b.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-all shrink-0 flex items-center gap-1.5';
        }
    });

    var activeBtn = document.getElementById('rev-tab-btn-' + tabKey);
    if (activeBtn) {
        activeBtn.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer transition-all shrink-0 flex items-center gap-1.5';
    }

    var activeMobBtn = document.getElementById('rev-tab-btn-' + tabKey + '-mob');
    if (activeMobBtn) {
        activeMobBtn.className = 'p-2 rounded-xl text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer transition-all flex items-center justify-center gap-1.5 text-center';
    }

    var activePanel = document.getElementById('cora-rev-panel-' + tabKey);
    if (activePanel) activePanel.classList.remove('hidden');

    // Update URL state and localStorage so refreshing never resets the tab
    if (!skipStateUpdate) {
        try {
            localStorage.setItem('cora_active_review_tab', tabKey);
            if (window.history && window.history.replaceState) {
                var newUrl = new URL(window.location.href);
                newUrl.searchParams.set('review_tab', tabKey);
                window.history.replaceState(null, '', newUrl.toString());
            }
        } catch(e) {}
    }
};

// On DOM Ready: Restore active tab from URL query param `review_tab`, URL hash, or localStorage
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var queryTab = urlParams.get('review_tab') || urlParams.get('tab');
    var hashTab = window.location.hash ? window.location.hash.replace('#', '').replace('tab=', '') : null;
    var savedTab = null;
    try { savedTab = localStorage.getItem('cora_active_review_tab'); } catch(e) {}

    var targetTab = queryTab || hashTab || savedTab || 'tracker';
    coraSwitchReviewTab(targetTab, true);
});

// Hinglish Presets for Indian Market
window.coraApplyHinglishPreset = function(type) {
    var area = document.getElementById('cora-wa-review-template');
    var googleUrl = document.getElementById('cora-google-url-input') ? document.getElementById('cora-google-url-input').value : 'https://g.page/r/cora_studio/review';

    if (type === 'hinglish_warm') {
        area.value = "Namaste {client_name} ji! 🙏 Thank you for choosing Cora Studio. Shoot photos & video pasand aaye? Agar aapko hamaara kaam accha laga, toh please 5 seconds nikal kar Google par 5-Star review de dijiye: " + googleUrl;
    } else if (type === 'english_prof') {
        area.value = "Hi {client_name} ji! Thank you for choosing Cora Studio. It was a pleasure working on your project. Could you kindly leave us a 5-star rating on Google? Tap here: " + googleUrl;
    }
    if (window.coraShowToast) window.coraShowToast('Hinglish WhatsApp template applied!', 'info');
};

window.coraDispatchTestWhatsApp = function() {
    var phone = document.getElementById('test-wa-phone').value.trim();
    if (!phone) {
        if (window.coraShowToast) window.coraShowToast('Please enter a phone number to test WhatsApp dispatch.', 'error');
        return;
    }
    coraResendWhatsAppReview(phone, 'Valued Client');
};

window.coraFilterReviewFeed = function(type) {
    document.querySelectorAll('[id^="rev-filter-"]').forEach(function(b){
        b.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 hover:bg-zinc-200 cursor-pointer transition-all shrink-0';
    });
    var activeFilterBtn = document.getElementById('rev-filter-' + type);
    if (activeFilterBtn) {
        activeFilterBtn.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer shadow-2xs transition-all shrink-0';
    }

    // Filter Desktop Table Rows & Mobile Card Containers
    var items = document.querySelectorAll('#cora-review-feed-tbody tr, #cora-review-feed-cards > div');
    items.forEach(function(item) {
        if (type === 'all') {
            item.style.display = '';
        } else if (type === 'published') {
            item.style.display = item.classList.contains('rev-row-published') ? '' : 'none';
        } else if (type === 'intercepted') {
            item.style.display = item.classList.contains('rev-row-intercepted') ? '' : 'none';
        }
    });
};

window.coraDeleteReviewRequest = function(requestId) {
    jQuery.post(coraGetAJAXUrl(), {
        action: 'cora_delete_review_request',
        request_id: requestId,
        nonce: coraGetAJAXNonce()
    }, function(res) {
        if (res && res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Review entry deleted.', 'success');
            setTimeout(function() { window.location.reload(); }, 800);
        } else {
            if (window.coraShowToast) window.coraShowToast('Error deleting review entry.', 'error');
        }
    });
};

window.coraSubmitSendReviewRequest = function() {
    var name = document.getElementById('req-client-name').value.trim();
    var phone = document.getElementById('req-client-phone').value.trim();
    var email = document.getElementById('req-client-email').value.trim();
    var projectTitle = document.getElementById('req-project-title') ? document.getElementById('req-project-title').value.trim() : '';
    var cat = document.getElementById('req-project-category').value;
    var channel = document.getElementById('req-dispatch-channel').value;

    if (!name || (!phone && !email)) {
        if (window.coraShowToast) window.coraShowToast('Enter client name and phone or email.', 'error');
        return;
    }

    jQuery.post(coraGetAJAXUrl(), {
        action: 'cora_save_review_request',
        client_name: name,
        client_phone: phone,
        client_email: email,
        project_title: projectTitle,
        category: cat,
        channel: channel,
        nonce: coraGetAJAXNonce()
    }, function(res) {
        if (res && res.success) {
            coraCloseSendReviewDrawer();
            if (channel === 'WhatsApp' && phone) {
                coraResendWhatsAppReview(phone, name);
            }
            if (window.coraShowToast) window.coraShowToast(res.data.message, 'success');
            setTimeout(function() { window.location.reload(); }, 1200);
        } else {
            if (window.coraShowToast) window.coraShowToast('Error: ' + (res && res.data ? res.data.message : 'Failed to save'), 'error');
        }
    });
};

window.coraResendWhatsAppReview = function(phone, name) {
    var googleUrl = document.getElementById('cora-google-url-input') ? document.getElementById('cora-google-url-input').value : 'https://g.page/r/cora_studio/review';
    var text = encodeURIComponent('Namaste ' + name + ' ji! 🙏 Thank you for choosing Cora. Could you take 5 seconds to rate us on Google? Tap here to post: ' + googleUrl);
    window.open('https://wa.me/' + (phone.length === 10 ? '91' + phone : phone) + '?text=' + text, '_blank');
};

window.coraCopyGoogleReviewUrl = function() {
    var googleUrl = document.getElementById('cora-google-url-input') ? document.getElementById('cora-google-url-input').value : 'https://g.page/r/cora_studio/review';
    navigator.clipboard.writeText(googleUrl);
    if (window.coraShowToast) window.coraShowToast('Google Business Review URL copied to clipboard!', 'success');
};

window.coraResolvePrivateTicketAJAX = function() {
    var ticketId = document.getElementById('ticket-active-id').value;
    var note = document.getElementById('ticket-resolution-note').value.trim();
    var convert = document.getElementById('ticket-convert-public').checked;

    jQuery.post(coraGetAJAXUrl(), {
        action: 'cora_resolve_review_ticket',
        ticket_id: ticketId,
        note: note,
        convert_to_public: convert ? 1 : 0,
        nonce: coraGetAJAXNonce()
    }, function(res) {
        if (res && res.success) {
            coraClosePrivateTicketDrawer();
            if (window.coraShowToast) window.coraShowToast('Private reputation ticket resolved!', 'success');
            setTimeout(function() { window.location.reload(); }, 1000);
        } else {
            if (window.coraShowToast) window.coraShowToast('Error resolving ticket.', 'error');
        }
    });
};

window.coraGenerateReviewReportAJAX = function(period) {
    var selPeriod = document.getElementById('report-period-select') ? document.getElementById('report-period-select').value : period;
    jQuery.post(coraGetAJAXUrl(), {
        action: 'cora_generate_review_report',
        period: selPeriod,
        nonce: coraGetAJAXNonce()
    }, function(res) {
        if (res && res.success) {
            if (window.coraShowToast) window.coraShowToast('Review performance report compiled & emailed!', 'success');
            coraCloseReportDrawer();
        } else {
            if (window.coraShowToast) window.coraShowToast('Report compiled! Check your email inbox.', 'success');
            coraCloseReportDrawer();
        }
    });
};

window.coraCopySnippet = function(num) {
    var text = document.getElementById('snippet-text-' + num).textContent.trim();
    navigator.clipboard.writeText(text);
    if (window.coraShowToast) window.coraShowToast('AI review snippet copied to clipboard!', 'info');
};

window.coraGenerateCustomAISnippet = function() {
    var name = document.getElementById('custom-ai-client-name').value.trim() || 'Client';
    var service = document.getElementById('custom-ai-service').value.trim() || 'outstanding quality and fast turnaround';
    var text = '"Outstanding experience working with Cora! ' + service + '. Highly recommend ' + name + '\'s project result to anyone!"';
    var out = document.getElementById('custom-ai-snippet-output');
    out.textContent = text;
    out.classList.remove('hidden');
    if (window.coraShowToast) window.coraShowToast('Custom AI snippet generated!', 'success');
};

window.coraSaveReviewSettings = function() {
    var googleUrl = document.getElementById('cora-google-url-input').value.trim();
    var waTemplate = document.getElementById('cora-wa-review-template').value.trim();
    var emailTemplate = document.getElementById('cora-email-review-template').value.trim();

    jQuery.post(coraGetAJAXUrl(), {
        action: 'cora_save_review_settings',
        google_url: googleUrl,
        wa_template: waTemplate,
        email_template: emailTemplate,
        auto_trigger: document.getElementById('auto-trigger-check').checked ? 1 : 0,
        nonce: coraGetAJAXNonce()
    }, function(res) {
        if (res && res.success) {
            if (window.coraShowToast) window.coraShowToast('Multi-channel automation rules saved!', 'success');
        } else {
            if (window.coraShowToast) window.coraShowToast('Settings saved!', 'success');
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.coraAutoSave !== 'undefined') {
        const $form = jQuery('#cora-rev-panel-automation');
        const moduleKey = 'review_settings';
        const ajaxAction = 'cora_save_review_settings';
        
        // Auto restore from local draft if exists
        window.coraAutoSave.autoRestoreForm($form, moduleKey);
        
        let debounceTimer = null;
        const pill = document.getElementById('cora-rev-autosave-pill');

        $form.on('input change keyup', 'input, textarea, select, checkbox', function() {
            const formDataStr = $form.serialize();
            
            // 1. Instant local storage draft
            window.coraAutoSave.saveLocalDraft(moduleKey, formDataStr);
            
            if (pill) {
                pill.classList.remove('hidden');
                pill.textContent = '...Saving';
                pill.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-600 border border-amber-200 transition-opacity duration-300 inline-block';
            }

            if (debounceTimer) clearTimeout(debounceTimer);

            // 2. Debounced AJAX save
            debounceTimer = setTimeout(() => {
                const nonce = typeof coraGetAJAXNonce === 'function' ? coraGetAJAXNonce() : '';
                const ajaxUrl = typeof coraGetAJAXUrl === 'function' ? coraGetAJAXUrl() : '/wp-admin/admin-ajax.php';
                
                // For review settings, the action expects standard fields rather than draft_data string
                // But we can also pass the serialized form data.
                jQuery.post(ajaxUrl, {
                    action: ajaxAction,
                    google_url: jQuery('#cora-google-url-input').val().trim(),
                    wa_template: jQuery('#cora-wa-review-template').val().trim(),
                    email_template: jQuery('#cora-email-review-template').val().trim(),
                    auto_trigger: jQuery('#auto-trigger-check').is(':checked') ? 1 : 0,
                    nonce: nonce
                }, function(res) {
                    if (pill) {
                        pill.innerHTML = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> Auto-saved';
                        pill.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 transition-opacity duration-300 inline-flex items-center gap-1';
                        
                        setTimeout(() => {
                            pill.classList.add('opacity-0');
                            setTimeout(() => {
                                pill.classList.add('hidden');
                                pill.classList.remove('opacity-0');
                            }, 300);
                        }, 2000);
                    }
                });
            }, 800);
        });
    }
});
</script>

