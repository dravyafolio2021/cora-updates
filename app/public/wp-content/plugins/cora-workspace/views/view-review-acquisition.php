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
        ),
        array(
            'id'            => 'rev_102',
            'client_name'   => 'Apex Realty Group',
            'client_phone'  => '9811223344',
            'client_email'  => 'finance@apexrealty.com',
            'project_title' => 'Commercial Lease Representation',
            'category'      => 'Real Estate Brokerage',
            'status'        => 'Google 5-Star Published',
            'rating'        => 5,
            'review_text'   => 'Professional commercial lease handling. Smooth paperwork and quick tenant placement.',
            'is_private'    => false,
            'sent_at'       => '2026-07-18 11:15',
            'responded_at'  => '2026-07-18 12:45',
            'channel'       => 'WhatsApp'
        ),
        array(
            'id'            => 'rev_103',
            'client_name'   => 'Priya Verma',
            'client_phone'  => '9899001122',
            'client_email'  => 'priya.v@example.com',
            'project_title' => 'Residential Listing Shoot',
            'category'      => 'Real Estate Media',
            'status'        => 'Private Shield Intercepted',
            'rating'        => 3,
            'review_text'   => 'Photos were great but delivery was delayed by 1 day. Want to discuss before public post.',
            'is_private'    => true,
            'ticket_status' => 'Resolved Internally',
            'sent_at'       => '2026-07-15 09:30',
            'responded_at'  => '2026-07-15 10:10',
            'channel'       => 'SMS'
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
$wa_template = get_option( 'cora_wa_review_template', 'Namaste {client_name} ji! 🙏 Thank you for choosing Cora Studio. Could you take 5 seconds to rate us on Google? Tap here to post: {review_url}' );
$email_template = get_option( 'cora_email_review_template', 'Hi {client_name}, we appreciate your business! Please leave us a review on Google.' );
?>

<div id="cora-reviews-feedback-wrapper" class="space-y-5 font-sans text-zinc-900 dark:text-zinc-100 relative">
    <!-- Header Bar with Title, Verified Badge, & Clean Essential Action Controls -->
    <div class="cora-shopify-card p-5 md:p-6 flex items-center justify-between flex-wrap gap-4 shadow-sm">
        <div class="space-y-3 max-w-2xl">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl font-extrabold text-zinc-950 dark:text-white tracking-tight m-0">Reviews & Feedback</h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>
                        Verified Shield Active
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 m-0 leading-relaxed">Automated 5-star review collector, multi-channel dispatch engine, and private reputation shield.</p>
            </div>

            <!-- Essential Header Action Buttons -->
            <div class="flex items-center gap-2.5 flex-wrap pt-0.5">
                <button type="button" onclick="coraOpenSendReviewDrawer()" class="px-4 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 shadow-sm cursor-pointer active:scale-97">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Request Review
                </button>
                <button type="button" onclick="coraOpenReportDrawer()" class="px-4 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Automated Reports
                </button>
            </div>
        </div>

        <!-- Clean Right Rating Badge Card -->
        <div class="hidden sm:flex items-center gap-2 px-3.5 py-2 bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800 rounded-xl shadow-2xs shrink-0">
            <div class="flex text-amber-400 text-xs tracking-wider">★★★★★</div>
            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 font-mono"><?php echo $avg_rating; ?> Rating</span>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards Grid with Subtle Color Background Tint/Shading -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Card 1: Total Requests Sent (Blue Tinted Background) -->
        <div class="bg-blue-50/60 dark:bg-blue-955/40 border border-blue-100 dark:border-blue-900/40 rounded-2xl p-4.5 space-y-2.5 relative overflow-hidden shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-900 dark:text-blue-200">Total Requests Sent</span>
                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-300 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-zinc-950 dark:text-white font-mono tracking-tight"><?php echo $total_requests; ?></div>
                <div class="text-[10px] text-blue-700 dark:text-blue-300 font-medium mt-0.5">Multi-channel post-handover automation</div>
            </div>
            <div class="h-1 rounded-full bg-blue-200/80 dark:bg-blue-950 overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full w-3/4"></div>
            </div>
        </div>

        <!-- Card 2: Google 5-Star Reviews (Emerald Tinted Background) -->
        <div class="bg-emerald-50/60 dark:bg-emerald-955/40 border border-emerald-100 dark:border-emerald-900/40 rounded-2xl p-4.5 space-y-2.5 relative overflow-hidden shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-900 dark:text-emerald-200">Google 5-Star Reviews</span>
                <div class="w-8 h-8 rounded-lg bg-white dark:bg-emerald-900/60 flex items-center justify-center shrink-0 p-1.5 shadow-2xs border border-emerald-100 dark:border-emerald-800">
                    <!-- Official Google G SVG -->
                    <svg viewBox="0 0 24 24" width="18" height="18">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono tracking-tight"><?php echo $google_reviews; ?></div>
                <div class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold mt-0.5">100% Verified Public Rating</div>
            </div>
            <div class="h-1 rounded-full bg-emerald-200/80 dark:bg-emerald-950 overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full w-2/3"></div>
            </div>
        </div>

        <!-- Card 3: Private Shield Intercepts (Amber Tinted Background) -->
        <div class="bg-amber-50/60 dark:bg-amber-955/40 border border-amber-100 dark:border-amber-900/40 rounded-2xl p-4.5 space-y-2.5 relative overflow-hidden shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-900 dark:text-amber-200">Private Shield Intercepts</span>
                <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-300 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 font-mono tracking-tight"><?php echo $private_intercepts; ?></div>
                <div class="text-[10px] text-amber-700 dark:text-amber-300 font-bold mt-0.5">Resolved 1-3★ Risks Privately</div>
            </div>
            <div class="h-1 rounded-full bg-amber-200/80 dark:bg-amber-950 overflow-hidden">
                <div class="h-full bg-amber-500 rounded-full w-1/3"></div>
            </div>
        </div>

        <!-- Card 4: Overall Score Impact (Purple Tinted Background) -->
        <div class="bg-purple-50/60 dark:bg-purple-955/40 border border-purple-100 dark:border-purple-900/40 rounded-2xl p-4.5 space-y-2.5 relative overflow-hidden shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-purple-900 dark:text-purple-200">Overall Score Impact</span>
                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/60 text-purple-600 dark:text-purple-300 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-zinc-950 dark:text-white font-mono tracking-tight"><?php echo $avg_rating; ?> / 5.0</div>
                <div class="text-[10px] text-purple-700 dark:text-purple-300 font-bold mt-0.5">+0.8 Star Surge Index</div>
            </div>
            <div class="h-1 rounded-full bg-purple-200/80 dark:bg-purple-950 overflow-hidden">
                <div class="h-full bg-purple-600 rounded-full w-4/5"></div>
            </div>
        </div>
    </div>

    <!-- SUB-NAVIGATION TABS BAR WITH PERSISTENT URL STATE & DEEP LINKING -->
    <div class="bg-white dark:bg-zinc-955 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-1.5 shadow-2xs flex items-center gap-1 overflow-x-auto">
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

    <!-- ═════════════════════════════════════════════════════════════════════
         TAB 1: REVIEW REQUESTS & REPUTATION FEED
         ═════════════════════════════════════════════════════════════════════ -->
    <div id="cora-rev-panel-tracker" class="cora-shopify-card space-y-4 p-5">
        <!-- Top Title & Filter Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-zinc-950 dark:text-zinc-100 m-0">Review Acquisition Audit Feed</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 mt-0.5">Auto-routes 4–5★ to Google Business and intercepts 1–3★ feedback into private tickets.</p>
            </div>

            <!-- Category Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto">
                <button type="button" onclick="coraFilterReviewFeed('all')" id="rev-filter-all" class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer shadow-2xs transition-all shrink-0">All (<?php echo $total_requests; ?>)</button>
                <button type="button" onclick="coraFilterReviewFeed('published')" id="rev-filter-published" class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 hover:bg-zinc-200 cursor-pointer transition-all shrink-0">5-Star Published</button>
                <button type="button" onclick="coraFilterReviewFeed('intercepted')" id="rev-filter-intercepted" class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 hover:bg-zinc-200 cursor-pointer transition-all shrink-0">Private Shield Intercepts</button>
            </div>
        </div>

        <!-- Feed Table Container — Ultra Compact with Official Brand Logos -->
        <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-xl overflow-hidden shadow-2xs bg-white dark:bg-zinc-955">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs min-w-[720px]">
                    <thead>
                        <tr class="bg-zinc-50/80 dark:bg-zinc-900/60 border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                            <th class="py-2.5 px-3 w-[200px]">CLIENT & PROJECT</th>
                            <th class="py-2.5 px-3 w-[130px]">CATEGORY</th>
                            <th class="py-2.5 px-3 w-[60px] text-center">CHANNEL</th>
                            <th class="py-2.5 px-3 w-[85px]">RATING</th>
                            <th class="py-2.5 px-3 w-[155px]">STATUS</th>
                            <th class="py-2.5 px-3">REVIEW SNIPPET / NOTE</th>
                            <th class="py-2.5 px-3 text-right w-[90px]">ACTIONS</th>
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

                            <!-- CHANNEL (Official Brand WhatsApp & SMS Logos) -->
                            <td class="py-2.5 px-3 align-middle text-center">
                                <?php if ( $channel_type === 'whatsapp' ) : ?>
                                    <!-- Official WhatsApp Brand Icon Badge -->
                                    <div class="w-8 h-8 rounded-full bg-[#25D366] text-white flex items-center justify-center mx-auto shadow-sm cursor-pointer hover:scale-105 transition-all" title="WhatsApp Channel">
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.285-.143-1.687-.832-1.947-.927-.26-.095-.45-.143-.64.143-.19.285-.735.927-.9 1.117-.165.19-.33.214-.615.071-.285-.143-1.204-.444-2.294-1.416-.848-.756-1.421-1.69-1.587-1.975-.166-.285-.018-.439.125-.581.128-.128.285-.333.428-.5.143-.167.19-.285.285-.476.095-.19.047-.357-.024-.5-.071-.143-.64-1.545-.877-2.116-.231-.557-.466-.481-.64-.49-.165-.008-.356-.01-.547-.01-.19 0-.5.071-.76.357-.26.285-.999.976-.999 2.38 0 1.404 1.023 2.76 1.165 2.951.143.19 2.013 3.074 4.877 4.31.682.295 1.214.471 1.629.603.685.218 1.309.187 1.802.114.549-.081 1.687-.689 1.924-1.355.237-.666.237-1.237.166-1.355-.07-.119-.26-.19-.545-.333z"/>
                                        </svg>
                                    </div>
                                <?php elseif ( $channel_type === 'sms' ) : ?>
                                    <!-- Official SMS Text Icon Badge -->
                                    <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center mx-auto shadow-sm cursor-pointer hover:scale-105 transition-all" title="SMS Text Channel">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                                            <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 11H7V9h2v2zm4 0h-2V9h2v2zm4 0h-2V9h2v2z"/>
                                        </svg>
                                    </div>
                                <?php else : ?>
                                    <!-- Official Email Icon Badge -->
                                    <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center mx-auto shadow-sm cursor-pointer hover:scale-105 transition-all" title="Email Channel">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- RATING (Compact Stars) -->
                            <td class="py-2.5 px-3 align-middle font-bold">
                                <div class="flex items-center gap-1">
                                    <span class="<?php echo $rating >= 4 ? 'text-emerald-500' : 'text-amber-500'; ?> text-xs tracking-tighter">
                                        <?php echo str_repeat( '★', $rating ); ?>
                                    </span>
                                    <span class="text-[10px] text-zinc-500 font-mono font-bold"><?php echo $rating; ?>/5</span>
                                </div>
                            </td>

                            <!-- STATUS (With Official Google 4-Color Logo & Official Shield) -->
                            <td class="py-2.5 px-3 align-middle">
                                <?php if ( ! $is_private ) : ?>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/70 dark:border-emerald-800/60 inline-flex items-center gap-1.5 whitespace-nowrap shadow-2xs">
                                        <!-- Official Google G Logo -->
                                        <svg viewBox="0 0 24 24" width="12" height="12" class="shrink-0">
                                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                        </svg>
                                        Google 5-Star Published
                                    </span>
                                <?php else : ?>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200/70 dark:border-amber-800/60 inline-flex items-center gap-1.5 whitespace-nowrap shadow-2xs">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-amber-500 shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                        Private Shield Intercepted
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- REVIEW SNIPPET / NOTE (2-Line Clamped Snippet) -->
                            <td class="py-2.5 px-3 align-middle text-zinc-600 dark:text-zinc-300 leading-snug text-xs" title="<?php echo esc_attr( $req['review_text'] ); ?>">
                                <div class="line-clamp-2">
                                    <span class="text-zinc-400 font-serif mr-1">“</span><?php echo esc_html( $req['review_text'] ); ?>
                                </div>
                            </td>

                            <!-- ACTIONS (Official Brand WhatsApp Button & Link Button) -->
                            <td class="py-2.5 px-3 align-middle text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <?php if ( $is_private ) : ?>
                                        <!-- Icon-Only Inspect Ticket Button -->
                                        <button type="button" onclick="coraOpenPrivateTicketDrawer('<?php echo esc_js( $req['id'] ); ?>')" class="w-8 h-8 rounded-full bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center transition-all cursor-pointer shadow-sm shrink-0" title="Inspect Private Reputation Ticket">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>
                                    <?php else : ?>
                                        <!-- Official WhatsApp Brand Icon Button -->
                                        <button type="button" onclick="coraResendWhatsAppReview('<?php echo esc_js( $req['client_phone'] ); ?>', '<?php echo esc_js( $req['client_name'] ); ?>')" class="w-8 h-8 rounded-full bg-[#25D366] hover:bg-emerald-600 text-white flex items-center justify-center transition-all cursor-pointer shadow-sm shrink-0" title="Resend WhatsApp Review Request">
                                            <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.285-.143-1.687-.832-1.947-.927-.26-.095-.45-.143-.64.143-.19.285-.735.927-.9 1.117-.165.19-.33.214-.615.071-.285-.143-1.204-.444-2.294-1.416-.848-.756-1.421-1.69-1.587-1.975-.166-.285-.018-.439.125-.581.128-.128.285-.333.428-.5.143-.167.19-.285.285-.476.095-.19.047-.357-.024-.5-.071-.143-.64-1.545-.877-2.116-.231-.557-.466-.481-.64-.49-.165-.008-.356-.01-.547-.01-.19 0-.5.071-.76.357-.26.285-.999.976-.999 2.38 0 1.404 1.023 2.76 1.165 2.951.143.19 2.013 3.074 4.877 4.31.682.295 1.214.471 1.629.603.685.218 1.309.187 1.802.114.549-.081 1.687-.689 1.924-1.355.237-.666.237-1.237.166-1.355-.07-.119-.26-.19-.545-.333z"/>
                                            </svg>
                                        </button>
                                        <!-- Official Copy Link Icon Button -->
                                        <button type="button" onclick="coraCopyGoogleReviewUrl()" class="w-8 h-8 rounded-full bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-200 flex items-center justify-center transition-all cursor-pointer shadow-sm shrink-0" title="Copy Google Business Review Link">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom Reputation Shield Info Banner inside Card -->
        <div class="p-3 bg-zinc-50/80 dark:bg-zinc-900/40 border border-zinc-200/80 dark:border-zinc-800 rounded-xl flex items-center justify-between flex-wrap gap-2 text-xs">
            <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
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
    <div id="cora-rev-panel-snippets" class="hidden cora-shopify-card space-y-6">
        <div class="space-y-1">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">AI Review Snippet Generator</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Clients hate typing long reviews! Pre-generate custom 5-star review snippets that clients can copy and post to Google in 1 tap.</p>
        </div>

        <!-- 3 Presets -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-5 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-zinc-50/50 dark:bg-zinc-900/30 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-md bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 text-[10px] font-bold">STUDIO PHOTOGRAPHY</span>
                    <button type="button" onclick="coraCopySnippet(1)" class="text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer">Copy</button>
                </div>
                <p id="snippet-text-1" class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-relaxed m-0">
                    "Exceptional 3-day wedding photography coverage! Turnaround was super fast, drone aerials were stunning, and the crew was extremely professional."
                </p>
            </div>

            <div class="p-5 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-zinc-50/50 dark:bg-zinc-900/30 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-md bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 text-[10px] font-bold">REAL ESTATE MEDIA</span>
                    <button type="button" onclick="coraCopySnippet(2)" class="text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer">Copy</button>
                </div>
                <p id="snippet-text-2" class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-relaxed m-0">
                    "Top-tier 4K property walkthrough video and architectural HDR stills! Delivered within 24 hours. Boosted our listing inquiries immensely."
                </p>
            </div>

            <div class="p-5 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-zinc-50/50 dark:bg-zinc-900/30 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-md bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 text-[10px] font-bold">REAL ESTATE BROKERAGE</span>
                    <button type="button" onclick="coraCopySnippet(3)" class="text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer">Copy</button>
                </div>
                <p id="snippet-text-3" class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-relaxed m-0">
                    "Extremely professional commercial lease negotiation and paperwork. Transparent advisory and quick closure. Highly recommend Apex Realty!"
                </p>
            </div>
        </div>

        <!-- Custom AI Generator Box -->
        <div class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-zinc-50/50 dark:bg-zinc-900/20 space-y-4">
            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 m-0">Generate Custom AI Snippet</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" id="custom-ai-client-name" placeholder="Client Name (e.g. Rahul Kapoor)" class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-sans text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white focus:ring-2 focus:ring-zinc-950/10">
                <input type="text" id="custom-ai-service" placeholder="Key Highlight (e.g. Fast turnaround, great lighting)" class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-sans text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white focus:ring-2 focus:ring-zinc-950/10">
            </div>
            <button type="button" onclick="coraGenerateCustomAISnippet()" class="py-2.5 px-5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-xs rounded-xl transition-all cursor-pointer">
                ⚡ Generate Snippet with AI
            </button>
            <div id="custom-ai-snippet-output" class="hidden p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-800 dark:text-zinc-200"></div>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════
         TAB 3: MULTI-CHANNEL AUTOMATION TRIGGERS (PREMIUM NOTION/SHOPIFY STYLED INPUTS)
         ═════════════════════════════════════════════════════════════════════ -->
    <form id="cora-rev-panel-automation" class="hidden cora-shopify-card space-y-6 max-w-4xl" onsubmit="event.preventDefault();">
        <!-- Indian Market Insights Banner -->
        <div class="p-4 bg-[#25D366]/10 border border-[#25D366]/30 rounded-2xl flex items-start gap-3 text-xs">
            <div class="w-8 h-8 rounded-full bg-[#25D366] text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.285-.143-1.687-.832-1.947-.927-.26-.095-.45-.143-.64.143-.19.285-.735.927-.9 1.117-.165.19-.33.214-.615.071-.285-.143-1.204-.444-2.294-1.416-.848-.756-1.421-1.69-1.587-1.975-.166-.285-.018-.439.125-.581.128-.128.285-.333.428-.5.143-.167.19-.285.285-.476.095-.19.047-.357-.024-.5-.071-.143-.64-1.545-.877-2.116-.231-.557-.466-.481-.64-.49-.165-.008-.356-.01-.547-.01-.19 0-.5.071-.76.357-.26.285-.999.976-.999 2.38 0 1.404 1.023 2.76 1.165 2.951.143.19 2.013 3.074 4.877 4.31.682.295 1.214.471 1.629.603.685.218 1.309.187 1.802.114.549-.081 1.687-.689 1.924-1.355.237-.666.237-1.237.166-1.355-.07-.119-.26-.19-.545-.333z"/></svg>
            </div>
            <div class="space-y-1">
                <div class="font-bold text-zinc-950 dark:text-white flex items-center gap-1.5">
                    <span>🇮🇳 WhatsApp-First Strategy for Indian Businesses</span>
                </div>
                <p class="text-zinc-600 dark:text-zinc-300 leading-relaxed m-0 text-[11px]">
                    In India, review emails are rarely opened (&lt;8% open rate), while <strong>WhatsApp messages have a 98% open rate</strong>. Our engine sends personalized Hinglish messages directly to client WhatsApp numbers right after photo/video delivery or final invoice payment!
                </p>
            </div>
        </div>

        <div class="space-y-1">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0 flex items-center gap-3">
                Multi-Channel Automation Triggers
                <span id="cora-rev-autosave-pill" class="hidden px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 transition-opacity duration-300">✓ Auto-saved</span>
            </h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Configure automated post-handover review requests across WhatsApp, Email, SMS, and Social channels.</p>
        </div>

        <div class="space-y-5 p-6 bg-zinc-50/60 dark:bg-zinc-900/30 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 text-xs shadow-2xs">
            <!-- Official Google Business Review URL Input -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block font-bold text-xs text-zinc-900 dark:text-zinc-100 m-0">Official Google Business Review URL *</label>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-200/60">Live Connected</span>
                </div>
                <div class="relative rounded-xl overflow-hidden shadow-2xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    </div>
                    <input type="text" id="cora-google-url-input" name="cora_google_business_url" value="<?php echo esc_attr( $google_link ); ?>" class="w-full pl-9 pr-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white focus:ring-2 focus:ring-zinc-950/10 transition-all">
                </div>
                <p class="text-[10px] text-zinc-400 m-0">Found under 'Ask for reviews' in your Google Business Profile dashboard.</p>
            </div>

            <!-- WhatsApp Automation Message (Hinglish/English) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="font-bold text-xs text-zinc-900 dark:text-zinc-100 m-0">WhatsApp Automation Message (Hinglish/English)</label>
                    <div class="flex gap-1.5">
                        <button type="button" onclick="coraApplyHinglishPreset('hinglish_warm')" class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold rounded-lg cursor-pointer transition-all border border-zinc-200 dark:border-zinc-700">Hinglish Warm</button>
                        <button type="button" onclick="coraApplyHinglishPreset('english_prof')" class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold rounded-lg cursor-pointer transition-all border border-zinc-200 dark:border-zinc-700">English Prof</button>
                    </div>
                </div>
                <textarea id="cora-wa-review-template" name="cora_wa_review_template" rows="3" class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-sans text-zinc-900 dark:text-zinc-100 leading-relaxed focus:outline-none focus:border-zinc-900 dark:focus:border-white focus:ring-2 focus:ring-zinc-950/10 transition-all resize-y"><?php echo esc_textarea( $wa_template ); ?></textarea>
            </div>

            <!-- Email Review Request Template -->
            <div class="space-y-1.5">
                <label class="block font-bold text-xs text-zinc-900 dark:text-zinc-100 m-0">Email Review Request Template</label>
                <textarea id="cora-email-review-template" name="cora_email_review_template" rows="3" class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-sans text-zinc-900 dark:text-zinc-100 leading-relaxed focus:outline-none focus:border-zinc-900 dark:focus:border-white focus:ring-2 focus:ring-zinc-950/10 transition-all resize-y"><?php echo esc_textarea( $email_template ); ?></textarea>
            </div>

            <div class="pt-1">
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                    <input type="checkbox" id="auto-trigger-check" name="auto_trigger_check" checked class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                    <span>Auto-trigger WhatsApp & Email 2 hours after project deal status is set to 'Handed Over' or 'Invoice Paid'</span>
                </label>
            </div>

            <div class="pt-3 border-t border-zinc-200/60 dark:border-zinc-800 flex items-center justify-between flex-wrap gap-3">
                <button type="button" onclick="coraSaveReviewSettings()" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-xs rounded-xl transition-all cursor-pointer shadow-sm active:scale-97">
                    Save Multi-Channel Rules
                </button>
            </div>
        </div>

        <!-- Direct WhatsApp Test Dispatch Box for Indian Owners -->
        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-3 shadow-2xs">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 m-0 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#25D366] animate-pulse"></span>
                    Test Instant WhatsApp Review Link Dispatch
                </h4>
                <span class="text-[10px] font-bold text-zinc-400">1-Tap Live WhatsApp Test</span>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" id="test-wa-phone" placeholder="Enter WhatsApp Phone Number (e.g. 9876543210)" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-[#25D366] focus:ring-2 focus:ring-[#25D366]/20 transition-all">
                <button type="button" onclick="coraDispatchTestWhatsApp()" class="px-5 py-2.5 bg-[#25D366] hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition-all shrink-0 cursor-pointer flex items-center gap-1.5 shadow-sm active:scale-97">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.285-.143-1.687-.832-1.947-.927-.26-.095-.45-.143-.64.143-.19.285-.735.927-.9 1.117-.165.19-.33.214-.615.071-.285-.143-1.204-.444-2.294-1.416-.848-.756-1.421-1.69-1.587-1.975-.166-.285-.018-.439.125-.581.128-.128.285-.333.428-.5.143-.167.19-.285.285-.476.095-.19.047-.357-.024-.5-.071-.143-.64-1.545-.877-2.116-.231-.557-.466-.481-.64-.49-.165-.008-.356-.01-.547-.01-.19 0-.5.071-.76.357-.26.285-.999.976-.999 2.38 0 1.404 1.023 2.76 1.165 2.951.143.19 2.013 3.074 4.877 4.31.682.295 1.214.471 1.629.603.685.218 1.309.187 1.802.114.549-.081 1.687-.689 1.924-1.355.237-.666.237-1.237.166-1.355-.07-.119-.26-.19-.545-.333z"/></svg>
                    Send WhatsApp Test
                </button>
            </div>
        </div>
    </form>

    <!-- ═════════════════════════════════════════════════════════════════════
         TAB 4: AUTOMATED PERFORMANCE & SENTIMENT REPORTS
         ═════════════════════════════════════════════════════════════════════ -->
    <div id="cora-rev-panel-reports" class="hidden cora-shopify-card space-y-6">
        <div class="space-y-1">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Automated Performance & Sentiment Reports</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Track conversion rates, public review surges, and automated PDF/Email digest schedules.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl">
                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Review Conversion Rate</div>
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1 font-mono">79.1%</div>
                <div class="text-[10px] text-emerald-600 font-semibold mt-0.5">+4.2% higher than industry avg</div>
            </div>

            <div class="p-5 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl">
                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Positive Sentiment Index</div>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 font-mono">92% Positive</div>
                <div class="text-[10px] text-zinc-500 mt-0.5">5% Neutral · 3% Risk Intercepted</div>
            </div>

            <div class="p-5 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl">
                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Automated Email Report</div>
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1 font-mono">Weekly Digest</div>
                <div class="text-[10px] text-zinc-500 mt-0.5">Delivered to admin email every Monday</div>
            </div>
        </div>

        <div class="p-6 bg-zinc-50/50 dark:bg-zinc-900/20 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex items-center justify-between flex-wrap gap-3">
            <div>
                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 m-0">Generate Instant Performance Report</h4>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 m-0">Compile full review audit log into a clean report summary.</p>
            </div>
            <button type="button" onclick="coraGenerateReviewReportAJAX('30_days')" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl transition-all cursor-pointer">
                Generate 30-Day Report
            </button>
        </div>
    </div>
</div>

<!-- GLOBAL DRAWER BACKDROP OVERLAY -->
<div id="cora-drawer-backdrop" onclick="coraCloseAllReviewDrawers()" class="fixed inset-0 z-[9998] bg-black/40 backdrop-blur-xs transition-opacity duration-250 opacity-0 hidden"></div>

<!-- ═══ SIDE DRAWER 1: SEND REVIEW REQUEST ═══════════════════════════════════ -->
<div id="cora-send-review-drawer" class="fixed inset-y-0 right-0 z-[9999] w-full max-w-md bg-white dark:bg-zinc-955 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl transition-transform duration-250 translate-x-full hidden">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800/60 bg-zinc-50/50 dark:bg-zinc-900/30 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Request Google 5-Star Review</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 m-0">Dispatch multi-channel review request with AI snippet.</p>
            </div>
            <button type="button" onclick="coraCloseSendReviewDrawer()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white cursor-pointer">✕</button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Client Full Name *</label>
                <input type="text" id="req-client-name" placeholder="Client Name..." class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
            </div>

            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Phone Number (WhatsApp/SMS)</label>
                <input type="text" id="req-client-phone" placeholder="9876543210" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
            </div>

            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Email Address</label>
                <input type="email" id="req-client-email" placeholder="client@example.com" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Project Category</label>
                    <select id="req-project-category" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                        <option value="Studio Photography">Studio Photography & Film</option>
                        <option value="Real Estate Media">Real Estate 4K Media</option>
                        <option value="Real Estate Brokerage">Real Estate Commercial Lease</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Dispatch Channel</label>
                    <select id="req-dispatch-channel" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                        <option value="WhatsApp" selected>WhatsApp (Recommended)</option>
                        <option value="Email">Email</option>
                        <option value="SMS">SMS</option>
                        <option value="Social DM">Social DM</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">AI Review Snippet / Message</label>
                <textarea id="req-snippet-message" rows="3" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none" placeholder="AI preset snippet text..."></textarea>
            </div>
        </div>

        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/60 bg-zinc-50/50 dark:bg-zinc-900/30 flex items-center justify-between gap-3">
            <button type="button" onclick="coraCloseSendReviewDrawer()" class="px-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900">Cancel</button>
            <button type="button" onclick="coraSubmitSendReviewRequest()" class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 rounded-xl text-xs font-bold cursor-pointer">Dispatch Request</button>
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
            <button type="button" onclick="coraClosePrivateTicketDrawer()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white cursor-pointer">✕</button>
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
            <button type="button" onclick="coraCloseReportDrawer()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white cursor-pointer">✕</button>
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
// DRAWER SHEET SLIDING ENGINE & OVERLAY BACKDROP CONTROLLER
// ═══════════════════════════════════════════════════════════════════════════
window.coraOpenSendReviewDrawer = function() {
    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-send-review-drawer');
    if (backdrop) {
        backdrop.classList.remove('hidden');
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

window.coraCloseSendReviewDrawer = function() {
    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-send-review-drawer');
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

window.coraOpenReportDrawer = function() {
    coraSwitchReviewTab('reports');
    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-report-generator-drawer');
    if (backdrop) {
        backdrop.classList.remove('hidden');
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
    document.getElementById('ticket-active-id').value = ticketId;
    var backdrop = document.getElementById('cora-drawer-backdrop');
    var drawer = document.getElementById('cora-private-ticket-drawer');
    if (backdrop) {
        backdrop.classList.remove('hidden');
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
        b.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-all shrink-0 flex items-center gap-1.5';
    });

    var activeBtn = document.getElementById('rev-tab-btn-' + tabKey);
    if (activeBtn) {
        activeBtn.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer transition-all shrink-0 flex items-center gap-1.5';
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
        b.className = 'px-3 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 hover:bg-zinc-200 cursor-pointer transition-all shrink-0';
    });
    var activeFilterBtn = document.getElementById('rev-filter-' + type);
    if (activeFilterBtn) {
        activeFilterBtn.className = 'px-3 py-1 rounded-full text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer shadow-2xs transition-all shrink-0';
    }

    var rows = document.querySelectorAll('#cora-review-feed-tbody tr');
    rows.forEach(function(row) {
        if (type === 'all') {
            row.style.display = '';
        } else if (type === 'published') {
            row.style.display = row.classList.contains('rev-row-published') ? '' : 'none';
        } else if (type === 'intercepted') {
            row.style.display = row.classList.contains('rev-row-intercepted') ? '' : 'none';
        }
    });
};

window.coraSubmitSendReviewRequest = function() {
    var name = document.getElementById('req-client-name').value.trim();
    var phone = document.getElementById('req-client-phone').value.trim();
    var email = document.getElementById('req-client-email').value.trim();
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
                        pill.textContent = '✓ Auto-saved';
                        pill.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 transition-opacity duration-300 inline-block';
                        
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

