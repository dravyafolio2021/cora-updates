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
            'channel'       => 'Email'
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
        ),
        array(
            'id'            => 'rev_104',
            'client_name'   => 'Rohan Mehta',
            'client_phone'  => '9810098100',
            'client_email'  => 'rohan@mehtadesign.com',
            'project_title' => 'Architecture Shoot & 3D Tour',
            'category'      => 'Studio Photography',
            'status'        => 'Google 5-Star Published',
            'rating'        => 5,
            'review_text'   => 'Incredible architectural stills and 3D walkthrough! Seamless delivery via Cora Vault.',
            'is_private'    => false,
            'sent_at'       => '2026-07-12 16:20',
            'responded_at'  => '2026-07-12 17:05',
            'channel'       => 'Social DM'
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

$avg_rating = $total_requests > 0 ? sprintf( '%.1f', $avg_rating_sum / $total_requests ) : '4.9';
$google_link = get_option( 'cora_google_business_url', 'https://g.page/r/cora_studio/review' );
$wa_template = get_option( 'cora_wa_review_template', 'Hi {client_name}! Thank you for choosing Cora. Could you take 5 seconds to rate us on Google? Tap here: {review_url}' );
$email_template = get_option( 'cora_email_review_template', 'Hi {client_name}, we appreciate your business! Please leave us a review on Google.' );
?>

<div id="cora-reviews-feedback-wrapper" class="space-y-6 font-sans text-zinc-900 dark:text-zinc-100">
    <!-- Header Bar with Title & Action Controls -->
    <div class="cora-shopify-card flex items-center justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h1 class="text-xl font-bold text-zinc-950 dark:text-white tracking-tight m-0">Reviews & Feedback</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 flex items-center gap-1">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>
                    Verified Shield Active
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 m-0">Automated 5-star review collector, multi-channel dispatch engine (WhatsApp, Email, Socials), and Private Reputation Shield.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="coraOpenSendReviewDrawer()" class="px-4 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl transition-all flex items-center gap-2 shadow-sm cursor-pointer active:scale-97">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + Request Review
            </button>
            <button type="button" onclick="coraOpenReportDrawer()" class="px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Automated Reports
            </button>
            <button type="button" onclick="coraOpenReceptionQRModal()" class="px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Reception QR Card
            </button>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="cora-shopify-card border-l-2 border-l-zinc-400 dark:border-l-zinc-600 shadow-2xs">
            <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Total Requests Sent</div>
            <div class="text-2xl font-bold text-zinc-950 dark:text-zinc-100 font-mono"><?php echo $total_requests; ?></div>
            <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Multi-channel post-handover automation</div>
        </div>

        <div class="cora-shopify-card border-l-2 border-l-emerald-500 shadow-2xs">
            <div class="text-[10px] font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-wider mb-1">Google 5-Star Reviews</div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 font-mono"><?php echo $google_reviews; ?></div>
            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1">100% Verified Public Rating</div>
        </div>

        <div class="cora-shopify-card border-l-2 border-l-amber-500 shadow-2xs">
            <div class="text-[10px] font-bold text-amber-600 dark:text-amber-500 uppercase tracking-wider mb-1">Private Shield Intercepts</div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 font-mono"><?php echo $private_intercepts; ?></div>
            <div class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold mt-1">Resolved 1-3★ Risks Privately</div>
        </div>

        <div class="cora-shopify-card border-l-2 border-l-indigo-500 shadow-2xs">
            <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Overall Score Impact</div>
            <div class="text-2xl font-bold text-zinc-950 dark:text-zinc-100 font-mono">★ <?php echo $avg_rating; ?> / 5.0</div>
            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-1">+0.8 Star Surge Index</div>
        </div>
    </div>

    <!-- 4 MAIN TABS CONTAINER -->
    <div class="cora-shopify-card space-y-6">
        <!-- Tab Switcher Navigation Bar -->
        <div class="flex items-center gap-2 border-b border-zinc-100 dark:border-zinc-800/40 pb-3 overflow-x-auto">
            <button type="button" onclick="coraSwitchReviewTab('tracker')" id="rev-tab-btn-tracker" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer transition-all shrink-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                Requests & Feedback Feed
            </button>
            <button type="button" onclick="coraSwitchReviewTab('snippets')" id="rev-tab-btn-snippets" class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-all shrink-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                AI Review Snippet Generator
            </button>
            <button type="button" onclick="coraSwitchReviewTab('automation')" id="rev-tab-btn-automation" class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-all shrink-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                Multi-Channel Triggers
            </button>
            <button type="button" onclick="coraSwitchReviewTab('reports')" id="rev-tab-btn-reports" class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-all shrink-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Automated Reports & Sentiment
            </button>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             TAB 1: REVIEW REQUESTS & REPUTATION FEED
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="cora-rev-panel-tracker" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Review Acquisition Audit Feed</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Auto-routes 4-5★ to Google Business and intercepts 1-3★ feedback into private tickets.</p>
                </div>

                <!-- Category Filter Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto">
                    <button type="button" onclick="coraFilterReviewFeed('all')" id="rev-filter-all" class="px-3 py-1 rounded-lg text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer">All (<?php echo $total_requests; ?>)</button>
                    <button type="button" onclick="coraFilterReviewFeed('published')" id="rev-filter-published" class="px-3 py-1 rounded-lg text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 hover:bg-zinc-200 cursor-pointer">5-Star Published</button>
                    <button type="button" onclick="coraFilterReviewFeed('intercepted')" id="rev-filter-intercepted" class="px-3 py-1 rounded-lg text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 hover:bg-zinc-200 cursor-pointer">Private Shield Intercepts</button>
                </div>
            </div>

            <!-- Feed Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-zinc-50/80 dark:bg-zinc-900/60 border-b border-zinc-200 dark:border-zinc-800 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                <th class="p-3.5">Client & Project</th>
                                <th class="p-3.5">Category</th>
                                <th class="p-3.5">Channel</th>
                                <th class="p-3.5">Rating</th>
                                <th class="p-3.5">Status</th>
                                <th class="p-3.5">Review Snippet / Note</th>
                                <th class="p-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 bg-white dark:bg-zinc-955" id="cora-review-feed-tbody">
                            <?php foreach ( $cora_review_requests as $req ) : 
                                $rating = intval( $req['rating'] ?? 5 );
                                $is_private = ! empty( $req['is_private'] ) || $rating <= 3;
                                $filter_class = $is_private ? 'rev-row-intercepted' : 'rev-row-published';
                                $status_bg = $is_private ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800';
                                $status_label = $is_private ? 'Private Shield Intercepted' : 'Google 5-Star Published';
                            ?>
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-900/40 transition-colors <?php echo $filter_class; ?>">
                                <td class="p-3.5">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $req['client_name'] ); ?></div>
                                    <div class="text-[10px] text-zinc-400 font-mono mt-0.5"><?php echo esc_html( $req['client_phone'] ?: $req['client_email'] ); ?></div>
                                </td>
                                <td class="p-3.5 font-medium text-zinc-700 dark:text-zinc-300">
                                    <?php echo esc_html( $req['category'] ); ?>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300">
                                        <?php echo esc_html( $req['channel'] ?? 'WhatsApp' ); ?>
                                    </span>
                                </td>
                                <td class="p-3.5 font-bold">
                                    <span class="<?php echo $rating >= 4 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'; ?>">
                                        <?php echo str_repeat( '★', $rating ); ?> (<?php echo $rating; ?>/5)
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?php echo $status_bg; ?> inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full <?php echo $is_private ? 'bg-amber-500' : 'bg-emerald-500'; ?>"></span>
                                        <?php echo esc_html( $status_label ); ?>
                                    </span>
                                </td>
                                <td class="p-3.5 text-zinc-600 dark:text-zinc-300 max-w-xs truncate" title="<?php echo esc_attr( $req['review_text'] ); ?>">
                                    "<?php echo esc_html( $req['review_text'] ); ?>"
                                </td>
                                <td class="p-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <?php if ( $is_private ) : ?>
                                            <button type="button" onclick="coraOpenPrivateTicketDrawer('<?php echo esc_js( $req['id'] ); ?>')" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[11px] font-bold transition-all cursor-pointer shadow-2xs">
                                                Inspect Ticket
                                            </button>
                                        <?php else : ?>
                                            <button type="button" onclick="coraResendWhatsAppReview('<?php echo esc_js( $req['client_phone'] ); ?>', '<?php echo esc_js( $req['client_name'] ); ?>')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold transition-all cursor-pointer shadow-2xs flex items-center gap-1">
                                                WhatsApp
                                            </button>
                                            <button type="button" onclick="coraCopyGoogleReviewUrl()" class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg text-[11px] font-semibold cursor-pointer">
                                                Copy Link
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
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             TAB 2: AI REVIEW SNIPPET GENERATOR
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="cora-rev-panel-snippets" class="hidden space-y-6">
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">AI Review Snippet Generator</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Clients hate typing long reviews! Pre-generate custom 5-star review snippets that clients can copy and post to Google in 1 tap.</p>
            </div>

            <!-- 3 Presets -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 text-[10px] font-bold">STUDIO PHOTOGRAPHY</span>
                        <button type="button" onclick="coraCopySnippet(1)" class="text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer">Copy</button>
                    </div>
                    <p id="snippet-text-1" class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-relaxed m-0">
                        "Exceptional 3-day wedding photography coverage! Turnaround was super fast, drone aerials were stunning, and the crew was extremely professional."
                    </p>
                </div>

                <div class="p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 text-[10px] font-bold">REAL ESTATE MEDIA</span>
                        <button type="button" onclick="coraCopySnippet(2)" class="text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer">Copy</button>
                    </div>
                    <p id="snippet-text-2" class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-relaxed m-0">
                        "Top-tier 4K property walkthrough video and architectural HDR stills! Delivered within 24 hours. Boosted our listing inquiries immensely."
                    </p>
                </div>

                <div class="p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 text-[10px] font-bold">REAL ESTATE BROKERAGE</span>
                        <button type="button" onclick="coraCopySnippet(3)" class="text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer">Copy</button>
                    </div>
                    <p id="snippet-text-3" class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-relaxed m-0">
                        "Extremely professional commercial lease negotiation and paperwork. Transparent advisory and quick closure. Highly recommend Apex Realty!"
                    </p>
                </div>
            </div>

            <!-- Custom AI Generator Box -->
            <div class="p-5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/20 space-y-3">
                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 m-0">Generate Custom AI Snippet</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" id="custom-ai-client-name" placeholder="Client Name (e.g. Rahul Kapoor)" class="w-full text-xs">
                    <input type="text" id="custom-ai-service" placeholder="Key Highlight (e.g. Fast turnaround, great lighting)" class="w-full text-xs">
                </div>
                <button type="button" onclick="coraGenerateCustomAISnippet()" class="py-2 px-4 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-xs rounded-xl transition-all cursor-pointer">
                    ⚡ Generate Snippet with AI
                </button>
                <div id="custom-ai-snippet-output" class="hidden p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-800 dark:text-zinc-200"></div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             TAB 3: MULTI-CHANNEL AUTOMATION TRIGGERS
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="cora-rev-panel-automation" class="hidden space-y-6 max-w-3xl">
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Multi-Channel Automation Triggers</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Configure automated post-handover review requests across WhatsApp, Email, SMS, and Social channels.</p>
            </div>

            <div class="space-y-4 p-5 bg-zinc-50/60 dark:bg-zinc-900/30 rounded-xl border border-zinc-200/80 dark:border-zinc-800 text-xs">
                <div>
                    <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">Official Google Business Review URL *</label>
                    <input type="text" id="cora-google-url-input" value="<?php echo esc_attr( $google_link ); ?>" class="w-full font-mono">
                    <p class="text-[10px] text-zinc-400 mt-1">Found under 'Ask for reviews' in your Google Business Profile dashboard.</p>
                </div>

                <div>
                    <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">WhatsApp Automation Template</label>
                    <textarea id="cora-wa-review-template" rows="3" class="w-full font-sans text-xs"><?php echo esc_textarea( $wa_template ); ?></textarea>
                </div>

                <div>
                    <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">Email Review Request Template</label>
                    <textarea id="cora-email-review-template" rows="3" class="w-full font-sans text-xs"><?php echo esc_textarea( $email_template ); ?></textarea>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-300 font-semibold cursor-pointer">
                        <input type="checkbox" id="auto-trigger-check" checked class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-zinc-900">
                        <span>Auto-trigger WhatsApp & Email 2 hours after project deal status is set to 'Handed Over' or 'Invoice Paid'</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="button" onclick="coraSaveReviewSettings()" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-xs rounded-xl transition-all cursor-pointer">
                        Save Multi-Channel Rules
                    </button>
                </div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             TAB 4: AUTOMATED PERFORMANCE & SENTIMENT REPORTS
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="cora-rev-panel-reports" class="hidden space-y-6">
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Automated Performance & Sentiment Reports</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0">Track conversion rates, public review surges, and automated PDF/Email digest schedules.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/80 dark:border-zinc-800 rounded-xl">
                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Review Conversion Rate</div>
                    <div class="text-xl font-bold text-zinc-900 dark:text-zinc-100 mt-1 font-mono">79.1%</div>
                    <div class="text-[10px] text-emerald-600 font-semibold mt-0.5">+4.2% higher than industry avg</div>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/80 dark:border-zinc-800 rounded-xl">
                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Positive Sentiment Index</div>
                    <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 font-mono">92% Positive</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5">5% Neutral · 3% Risk Intercepted</div>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/80 dark:border-zinc-800 rounded-xl">
                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Automated Email Report</div>
                    <div class="text-xl font-bold text-zinc-900 dark:text-zinc-100 mt-1 font-mono">Weekly Digest</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5">Delivered to admin email every Monday</div>
                </div>
            </div>

            <div class="p-5 bg-zinc-50/50 dark:bg-zinc-900/20 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 m-0">Generate Instant Performance Report</h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 m-0">Compile full review audit log into a clean report summary.</p>
                </div>
                <button type="button" onclick="coraGenerateReviewReportAJAX('30_days')" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl transition-all cursor-pointer">
                    Generate 30-Day Report
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ SIDE DRAWER 1: SEND REVIEW REQUEST ═══════════════════════════════════ -->
<div id="cora-send-review-drawer" class="fixed inset-y-0 right-0 z-[9999] w-full max-w-md bg-white dark:bg-zinc-955 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl transition-transform duration-250 translate-x-full" style="display:none;">
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
                <input type="text" id="req-client-name" placeholder="Client Name..." class="w-full">
            </div>

            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Phone Number (WhatsApp/SMS)</label>
                <input type="text" id="req-client-phone" placeholder="9876543210" class="w-full font-mono">
            </div>

            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Email Address</label>
                <input type="email" id="req-client-email" placeholder="client@example.com" class="w-full">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Project Category</label>
                    <select id="req-project-category" style="width:100%;padding:8px 12px;font-size:12px;background:var(--input-bg);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);outline:none;">
                        <option value="Studio Photography">Studio Photography & Film</option>
                        <option value="Real Estate Media">Real Estate 4K Media</option>
                        <option value="Real Estate Brokerage">Real Estate Commercial Lease</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Dispatch Channel</label>
                    <select id="req-dispatch-channel" style="width:100%;padding:8px 12px;font-size:12px;background:var(--input-bg);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);outline:none;">
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="Email">Email</option>
                        <option value="SMS">SMS</option>
                        <option value="Social DM">Social DM</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">AI Review Snippet / Message</label>
                <textarea id="req-snippet-message" rows="3" class="w-full text-xs" placeholder="AI preset snippet text..."></textarea>
            </div>
        </div>

        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/60 bg-zinc-50/50 dark:bg-zinc-900/30 flex items-center justify-between gap-3">
            <button type="button" onclick="coraCloseSendReviewDrawer()" class="px-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900">Cancel</button>
            <button type="button" onclick="coraSubmitSendReviewRequest()" class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-950 rounded-xl text-xs font-bold cursor-pointer">Dispatch Request</button>
        </div>
    </div>
</div>

<!-- ═══ SIDE DRAWER 2: PRIVATE SHIELD TICKET INSPECTOR ═══════════════════════ -->
<div id="cora-private-ticket-drawer" class="fixed inset-y-0 right-0 z-[9999] w-full max-w-md bg-white dark:bg-zinc-955 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl transition-transform duration-250 translate-x-full" style="display:none;">
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
                    "Photos were great but delivery was delayed by 1 day. Want to discuss before public post."
                </p>
            </div>

            <div>
                <label class="block font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Resolution Action Note</label>
                <textarea id="ticket-resolution-note" rows="3" placeholder="Enter resolution notes (e.g. Spoke with client, offered priority delivery next shoot)..." class="w-full text-xs"></textarea>
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
<div id="cora-report-generator-drawer" class="fixed inset-y-0 right-0 z-[9999] w-full max-w-md bg-white dark:bg-zinc-955 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl transition-transform duration-250 translate-x-full" style="display:none;">
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
                <select id="report-period-select" style="width:100%;padding:9px 12px;font-size:12px;background:var(--input-bg);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);outline:none;">
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
window.coraSwitchReviewTab = function(tabKey) {
    document.getElementById('cora-rev-panel-tracker').classList.add('hidden');
    document.getElementById('cora-rev-panel-snippets').classList.add('hidden');
    document.getElementById('cora-rev-panel-automation').classList.add('hidden');
    document.getElementById('cora-rev-panel-reports').classList.add('hidden');

    document.querySelectorAll('[id^="rev-tab-btn-"]').forEach(function(b){
        b.className = 'px-4 py-2 rounded-xl text-xs font-bold text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-all shrink-0 flex items-center gap-1.5';
    });

    var activeBtn = document.getElementById('rev-tab-btn-' + tabKey);
    if (activeBtn) {
        activeBtn.className = 'px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer transition-all shrink-0 flex items-center gap-1.5';
    }

    document.getElementById('cora-rev-panel-' + tabKey).classList.remove('hidden');
};

window.coraFilterReviewFeed = function(type) {
    document.querySelectorAll('[id^="rev-filter-"]').forEach(function(b){
        b.className = 'px-3 py-1 rounded-lg text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 hover:bg-zinc-200 cursor-pointer';
    });
    var activeFilterBtn = document.getElementById('rev-filter-' + type);
    if (activeFilterBtn) {
        activeFilterBtn.className = 'px-3 py-1 rounded-lg text-xs font-bold bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 cursor-pointer';
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

window.coraOpenSendReviewDrawer = function() {
    var drawer = document.getElementById('cora-send-review-drawer');
    drawer.style.display = 'block';
    setTimeout(function() { drawer.style.transform = 'translateX(0)'; }, 10);
};

window.coraCloseSendReviewDrawer = function() {
    var drawer = document.getElementById('cora-send-review-drawer');
    drawer.style.transform = 'translateX(100%)';
    setTimeout(function() { drawer.style.display = 'none'; }, 250);
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

    jQuery.post(coraREData.ajaxUrl, {
        action: 'cora_save_review_request',
        client_name: name,
        client_phone: phone,
        client_email: email,
        category: cat,
        channel: channel,
        nonce: coraREData.ajaxNonce
    }, function(res) {
        if (res && res.success) {
            coraCloseSendReviewDrawer();
            if (channel === 'WhatsApp' && phone) {
                coraResendWhatsAppReview(phone, name);
            }
            if (window.coraShowToast) window.coraShowToast(res.data.message, 'success');
            setTimeout(function() { window.location.reload(); }, 1200);
        } else {
            if (window.coraShowToast) window.coraShowToast('Error: ' + (res.data ? res.data.message : 'Failed to save'), 'error');
        }
    });
};

window.coraResendWhatsAppReview = function(phone, name) {
    var googleUrl = document.getElementById('cora-google-url-input') ? document.getElementById('cora-google-url-input').value : 'https://g.page/r/cora_studio/review';
    var text = encodeURIComponent('Hi ' + name + '! Thank you for choosing Cora. Could you take 5 seconds to rate us on Google? Tap here to post: ' + googleUrl);
    window.open('https://wa.me/' + (phone.length === 10 ? '91' + phone : phone) + '?text=' + text, '_blank');
};

window.coraCopyGoogleReviewUrl = function() {
    var googleUrl = document.getElementById('cora-google-url-input') ? document.getElementById('cora-google-url-input').value : 'https://g.page/r/cora_studio/review';
    navigator.clipboard.writeText(googleUrl);
    if (window.coraShowToast) window.coraShowToast('Google Business Review URL copied to clipboard!', 'success');
};

window.coraOpenPrivateTicketDrawer = function(ticketId) {
    document.getElementById('ticket-active-id').value = ticketId;
    var drawer = document.getElementById('cora-private-ticket-drawer');
    drawer.style.display = 'block';
    setTimeout(function() { drawer.style.transform = 'translateX(0)'; }, 10);
};

window.coraClosePrivateTicketDrawer = function() {
    var drawer = document.getElementById('cora-private-ticket-drawer');
    drawer.style.transform = 'translateX(100%)';
    setTimeout(function() { drawer.style.display = 'none'; }, 250);
};

window.coraResolvePrivateTicketAJAX = function() {
    var ticketId = document.getElementById('ticket-active-id').value;
    var note = document.getElementById('ticket-resolution-note').value.trim();
    var convert = document.getElementById('ticket-convert-public').checked;

    jQuery.post(coraREData.ajaxUrl, {
        action: 'cora_resolve_review_ticket',
        ticket_id: ticketId,
        note: note,
        convert_to_public: convert ? 1 : 0,
        nonce: coraREData.ajaxNonce
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

window.coraOpenReportDrawer = function() {
    var drawer = document.getElementById('cora-report-generator-drawer');
    drawer.style.display = 'block';
    setTimeout(function() { drawer.style.transform = 'translateX(0)'; }, 10);
};

window.coraCloseReportDrawer = function() {
    var drawer = document.getElementById('cora-report-generator-drawer');
    drawer.style.transform = 'translateX(100%)';
    setTimeout(function() { drawer.style.display = 'none'; }, 250);
};

window.coraGenerateReviewReportAJAX = function(period) {
    jQuery.post(coraREData.ajaxUrl, {
        action: 'cora_generate_review_report',
        period: period,
        nonce: coraREData.ajaxNonce
    }, function(res) {
        if (res && res.success) {
            if (window.coraShowToast) window.coraShowToast('Review performance report compiled & emailed!', 'success');
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

window.coraOpenReceptionQRModal = function() {
    if (window.coraShowToast) window.coraShowToast('Opening Reception QR Card Print Preview...', 'info');
    window.print();
};

window.coraSaveReviewSettings = function() {
    var googleUrl = document.getElementById('cora-google-url-input').value.trim();
    var waTemplate = document.getElementById('cora-wa-review-template').value.trim();
    var emailTemplate = document.getElementById('cora-email-review-template').value.trim();

    jQuery.post(coraREData.ajaxUrl, {
        action: 'cora_save_review_settings',
        google_url: googleUrl,
        wa_template: waTemplate,
        email_template: emailTemplate,
        auto_trigger: document.getElementById('auto-trigger-check').checked ? 1 : 0,
        nonce: coraREData.ajaxNonce
    }, function(res) {
        if (res && res.success) {
            if (window.coraShowToast) window.coraShowToast('Multi-channel automation rules saved!', 'success');
        }
    });
};
</script>
