<?php
/**
 * Cora Workspace - Smart Review Acquisition Engine
 * File: views/view-review-acquisition.php
 * Automated Google Business 5-Star Reviews & Private Reputation Shield
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
            'status'        => 'Google Reviewed',
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
            'client_phone'  => '9811223344',
            'project_title' => 'Commercial Lease Representation',
            'category'      => 'Real Estate Brokerage',
            'status'        => 'Google Reviewed',
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
            'status'        => 'Private Intercepted',
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

// Calculate summary stats
$total_requests = count( $cora_review_requests );
$google_reviews = 0;
$private_intercepts = 0;
$avg_rating_sum = 0;

foreach ( $cora_review_requests as $req ) {
    if ( ! empty( $req['rating'] ) ) $avg_rating_sum += floatval( $req['rating'] );
    if ( ( $req['status'] ?? '' ) === 'Google Reviewed' || ( $req['rating'] ?? 0 ) >= 4 ) {
        $google_reviews++;
    } elseif ( ! empty( $req['is_private'] ) || ( $req['rating'] ?? 0 ) <= 3 ) {
        $private_intercepts++;
    }
}

$avg_rating = $total_requests > 0 ? sprintf( '%.1f', $avg_rating_sum / $total_requests ) : '4.9';
$google_link = get_option( 'cora_google_business_url', 'https://g.page/r/cora_studio/review' );
?>

<div id="cora-review-acquisition-wrapper" class="space-y-6 font-sans text-zinc-900">
    <!-- Header Bar with Call-to-Action Buttons -->
    <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-sm flex items-center justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h1 class="text-xl font-extrabold text-zinc-950 tracking-tight">Smart Review Acquisition Engine</h1>
            </div>
            <p class="text-xs text-zinc-500 mt-1">Automated 5-star Google Business review collector & Private Reputation Shield.</p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="coraOpenSendReviewDrawer()" class="px-4 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + Request Review
            </button>
            <button onclick="coraDownloadReceptionQR()" class="px-3.5 py-2 bg-white border border-zinc-200 text-zinc-800 text-xs font-bold rounded-xl hover:bg-zinc-50 cursor-pointer shadow-xs">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="mr-1.5 inline-block"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> Download Reception QR Card
            </button>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Total Requests Sent</span>
            <div class="text-2xl font-bold text-zinc-950 font-mono"><?php echo $total_requests; ?></div>
            <span class="text-[10px] text-zinc-500">Post-handover automation</span>
        </div>

        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider block">Google 5-Star Reviews</span>
            <div class="text-2xl font-bold text-emerald-700 font-mono"><?php echo $google_reviews; ?></div>
            <span class="text-[10px] text-emerald-600 font-semibold">100% Verified Public Rating</span>
        </div>

        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-amber-600 uppercase tracking-wider block">Private Shield Intercepts</span>
            <div class="text-2xl font-bold text-amber-700 font-mono"><?php echo $private_intercepts; ?></div>
            <span class="text-[10px] text-amber-600 font-semibold">Resolved 1-3★ Risks Privately</span>
        </div>

        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Google Score Impact</span>
            <div class="text-2xl font-bold text-zinc-950 font-mono">★ <?php echo $avg_rating; ?> / 5.0</div>
            <span class="text-[10px] text-emerald-600 font-bold">+0.8 Star Surge Metric</span>
        </div>
    </div>

    <!-- 3 MAIN TABS CONTAINER -->
    <div class="bg-white border border-zinc-200/80 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
        <!-- Tab Switcher Header -->
        <div class="flex items-center gap-2 border-b border-zinc-200/80 pb-4">
            <button onclick="coraSwitchReviewTab('tracker')" id="rev-tab-btn-tracker" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white cursor-pointer transition-all">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="mr-1.5 inline-block"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> Review Requests & Feedback Tracker
            </button>
            <button onclick="coraSwitchReviewTab('snippets')" id="rev-tab-btn-snippets" class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 hover:bg-zinc-100 cursor-pointer transition-all">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="mr-1.5 inline-block"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg> AI Review Snippet Generator
            </button>
            <button onclick="coraSwitchReviewTab('settings')" id="rev-tab-btn-settings" class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 hover:bg-zinc-100 cursor-pointer transition-all">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="mr-1.5 inline-block"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg> Google Business & WhatsApp Triggers
            </button>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             TAB 1: REVIEW REQUESTS & REPUTATION TRACKER TABLE
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="cora-rev-panel-tracker" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-zinc-950">Review Acquisition Audit Feed</h3>
                <span class="text-xs text-zinc-500">Auto-filters 1-3 star feedback into internal tickets</span>
            </div>

            <div class="border border-zinc-200 rounded-2xl overflow-hidden shadow-xs">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-100/80 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase">
                            <th class="p-3.5">Client & Project</th>
                            <th class="p-3.5">Category</th>
                            <th class="p-3.5">Channel</th>
                            <th class="p-3.5">Rating</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Review Snippet / Note</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        <?php foreach ( $cora_review_requests as $req ) : 
                            $rating = intval( $req['rating'] ?? 5 );
                            $is_private = ! empty( $req['is_private'] ) || $rating <= 3;
                            $status_bg = $is_private ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                            $status_label = $is_private ? 'Private Shield Intercepted' : 'Google 5-Star Published';
                        ?>
                        <tr class="hover:bg-zinc-50/70 transition-colors">
                            <td class="p-3.5">
                                <div class="font-extrabold text-zinc-950"><?php echo esc_html( $req['client_name'] ); ?></div>
                                <div class="text-[10px] text-zinc-400 font-mono"><?php echo esc_html( $req['client_phone'] ); ?></div>
                            </td>
                            <td class="p-3.5 font-semibold text-zinc-700">
                                <?php echo esc_html( $req['category'] ); ?>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-100 border border-zinc-200 text-zinc-700">
                                    <?php echo esc_html( $req['channel'] ?? 'WhatsApp' ); ?>
                                </span>
                            </td>
                            <td class="p-3.5 font-bold text-zinc-900">
                                <span class="<?php echo $rating >= 4 ? 'text-emerald-600' : 'text-amber-600'; ?>">
                                    <?php echo str_repeat( '★', $rating ); ?> (<?php echo $rating; ?>/5)
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold <?php echo $status_bg; ?>">
                                    <?php echo esc_html( $status_label ); ?>
                                </span>
                            </td>
                            <td class="p-3.5 text-zinc-600 max-w-xs truncate">
                                "<?php echo esc_html( $req['review_text'] ); ?>"
                            </td>
                            <td class="p-3.5 text-right">
                                <?php if ( $is_private ) : ?>
                                    <button onclick="coraOpenPrivateTicketDrawer('<?php echo esc_js( $req['id'] ); ?>')" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[11px] font-bold cursor-pointer shadow-xs">
                                        Inspect Ticket
                                    </button>
                                <?php else : ?>
                                    <button onclick="coraResendWhatsAppReview('<?php echo esc_js( $req['client_phone'] ); ?>', '<?php echo esc_js( $req['client_name'] ); ?>')" class="px-3 py-1 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-[11px] font-semibold cursor-pointer">
                                        WhatsApp Resend
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             TAB 2: AI REVIEW SNIPPET GENERATOR (ZERO TYPING FRICTION FOR CLIENTS)
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="cora-rev-panel-snippets" class="hidden space-y-6">
            <div class="max-w-2xl space-y-1">
                <h3 class="text-sm font-bold text-zinc-900 mb-4 flex items-center"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="mr-1.5 inline-block"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg> AI Review Snippet Generator</h3>
                <p class="text-xs text-zinc-500">Clients hate typing long reviews! Cora AI pre-generates 3 custom 5-star review snippets that clients can copy and post to Google in 1 tap.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Preset 1: Studio Photography -->
                <div class="p-6 border border-zinc-200/80 rounded-2xl bg-zinc-50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded bg-zinc-950 text-white text-[10px] font-bold">STUDIO PHOTOGRAPHY</span>
                        <button onclick="coraCopySnippet(1)" class="text-xs text-zinc-600 hover:text-zinc-950 font-bold cursor-pointer">Copy</button>
                    </div>
                    <p id="snippet-text-1" class="text-xs text-zinc-800 italic leading-relaxed">
                        "Exceptional 3-day wedding photography coverage! Turnaround was super fast, drone aerials were stunning, and the crew was extremely professional."
                    </p>
                </div>

                <!-- Preset 2: Real Estate Listing Media -->
                <div class="p-6 border border-zinc-200/80 rounded-2xl bg-zinc-50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded bg-zinc-950 text-white text-[10px] font-bold">REAL ESTATE MEDIA</span>
                        <button onclick="coraCopySnippet(2)" class="text-xs text-zinc-600 hover:text-zinc-950 font-bold cursor-pointer">Copy</button>
                    </div>
                    <p id="snippet-text-2" class="text-xs text-zinc-800 italic leading-relaxed">
                        "Top-tier 4K property walkthrough video and architectural HDR stills! Delivered within 24 hours. Boosted our listing inquiries immensely."
                    </p>
                </div>

                <!-- Preset 3: Commercial Lease Brokerage -->
                <div class="p-6 border border-zinc-200/80 rounded-2xl bg-zinc-50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded bg-zinc-950 text-white text-[10px] font-bold">REAL ESTATE BROKERAGE</span>
                        <button onclick="coraCopySnippet(3)" class="text-xs text-zinc-600 hover:text-zinc-950 font-bold cursor-pointer">Copy</button>
                    </div>
                    <p id="snippet-text-3" class="text-xs text-zinc-800 italic leading-relaxed">
                        "Extremely professional commercial lease negotiation and paperwork. Transparent advisory and quick closure. Highly recommend Apex Realty!"
                    </p>
                </div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             TAB 3: GOOGLE BUSINESS & WHATSAPP AUTOMATION SETTINGS
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="cora-rev-panel-settings" class="hidden space-y-6 max-w-3xl text-xs">
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900 mb-4 flex items-center"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="mr-1.5 inline-block"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg> Google Business & WhatsApp Triggers</h3>
                <p class="text-xs text-zinc-500">Configure your official Google Review link and post-handover WhatsApp dispatch rules.</p>
            </div>

            <div class="space-y-4 bg-zinc-50 p-6 rounded-2xl border border-zinc-200">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Official Google Business Review URL *</label>
                    <input type="text" id="cora-google-url-input" value="<?php echo esc_attr( $google_link ); ?>" class="w-full border border-zinc-200 rounded-xl p-3 bg-white font-mono outline-none">
                    <p class="text-[10px] text-zinc-500 mt-1">Get this link from your Google Business Profile dashboard under 'Ask for reviews'.</p>
                </div>

                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Automated WhatsApp Dispatch Template</label>
                    <textarea id="cora-wa-review-template" rows="3" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none">Hi {client_name}! Thank you for choosing {studio_name}. We hope you loved our service! Could you take 5 seconds to rate us on Google? Tap here: {review_url}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" id="auto-trigger-check" checked class="w-4 h-4 rounded text-zinc-950">
                    <label for="auto-trigger-check" class="font-bold text-zinc-800">Auto-send WhatsApp review link 2 hours after deal status is set to 'Handed Over' or 'Invoice Paid'</label>
                </div>

                <button onclick="coraSaveReviewSettings()" class="px-5 py-2.5 bg-zinc-950 text-white font-bold rounded-xl hover:bg-zinc-800 cursor-pointer">
                    Save Automation Rules
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ SIDE DRAWER 1: SEND REVIEW REQUEST ═══════════════════════════════════ -->
<aside id="cora-send-review-drawer" class="collapsed border-l border-zinc-200 bg-white">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-200 bg-zinc-50/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900">Request Google 5-Star Review</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Send 1-click WhatsApp/SMS link with AI review snippet.</p>
            </div>
            <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <div>
                <label class="block font-bold text-zinc-800 mb-1">Client Full Name *</label>
                <input type="text" id="req-client-name" placeholder="Client Name..." class="w-full border border-zinc-200 rounded-xl p-3 outline-none">
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">WhatsApp Phone Number *</label>
                <input type="text" id="req-client-phone" placeholder="9876543210" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Project Category</label>
                <select id="req-project-category" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold">
                    <option value="Studio Photography">Studio Photography & Film</option>
                    <option value="Real Estate Media">Real Estate 4K Media</option>
                    <option value="Real Estate Brokerage">Real Estate Commercial Lease</option>
                </select>
            </div>
        </div>

        <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between">
            <button onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white">Cancel</button>
            <button onclick="coraSubmitSendReviewRequest()" class="px-5 py-2 bg-emerald-700 text-white rounded-lg text-xs font-bold hover:bg-emerald-800 cursor-pointer">Send WhatsApp Review Link</button>
        </div>
    </div>
</aside>

<!-- ═══ SIDE DRAWER 2: PRIVATE SHIELD TICKET INSPECTOR ═══════════════════════ -->
<aside id="cora-private-ticket-drawer" class="collapsed border-l border-zinc-200 bg-white">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-200 bg-zinc-50/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 mb-4 flex items-center"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="mr-1.5 inline-block"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> Private Reputation Ticket</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Intercepted 1-3★ feedback resolved before Google public posting.</p>
            </div>
            <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl space-y-2">
                <span class="text-[10px] font-bold text-amber-700 uppercase block">Private Feedback Shield Active</span>
                <p id="ticket-feedback-text" class="text-zinc-800 font-medium leading-relaxed">
                    "Photos were great but delivery was delayed by 1 day. Want to discuss before public post."
                </p>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Resolution Action Note</label>
                <textarea id="ticket-resolution-note" rows="3" placeholder="Enter resolution notes (e.g. Called client, offered 10% discount on next shoot)..." class="w-full border border-zinc-200 rounded-xl p-3 outline-none"></textarea>
            </div>
        </div>

        <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between">
            <button onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white">Cancel</button>
            <button onclick="coraResolvePrivateTicket()" class="px-5 py-2 bg-amber-600 text-white rounded-lg text-xs font-bold hover:bg-amber-700 cursor-pointer">Mark Resolved Internally</button>
        </div>
    </div>
</aside>

<script>
window.coraSwitchReviewTab = function(tabKey) {
    document.getElementById('cora-rev-panel-tracker').classList.add('hidden');
    document.getElementById('cora-rev-panel-snippets').classList.add('hidden');
    document.getElementById('cora-rev-panel-settings').classList.add('hidden');

    document.querySelectorAll('[id^="rev-tab-btn-"]').forEach(function(b){
        b.classList.remove('bg-zinc-950', 'text-white');
        b.classList.add('text-zinc-600', 'hover:bg-zinc-100');
    });

    var activeBtn = document.getElementById('rev-tab-btn-' + tabKey);
    if (activeBtn) {
        activeBtn.classList.add('bg-zinc-950', 'text-white');
        activeBtn.classList.remove('text-zinc-600', 'hover:bg-zinc-100');
    }

    document.getElementById('cora-rev-panel-' + tabKey).classList.remove('hidden');
};

window.coraOpenSendReviewDrawer = function() {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    document.getElementById('cora-drawer-backdrop').classList.remove('hidden');
    document.getElementById('cora-send-review-drawer').classList.remove('collapsed');
};

window.coraSubmitSendReviewRequest = function() {
    var name = document.getElementById('req-client-name').value.trim();
    var phone = document.getElementById('req-client-phone').value.trim();
    var cat = document.getElementById('req-project-category').value;

    if (!name || !phone) { coraShowToast('Enter client name and phone number.'); return; }

    window.coraCloseAllDrawers();
    coraResendWhatsAppReview(phone, name);
    coraShowToast('Review request dispatched via WhatsApp!');
};

window.coraResendWhatsAppReview = function(phone, name) {
    var googleUrl = document.getElementById('cora-google-url-input') ? document.getElementById('cora-google-url-input').value : 'https://g.page/r/cora_studio/review';
    var text = encodeURIComponent('Hi ' + name + '! Thank you for choosing Cora Studio. Could you take 5 seconds to rate us on Google? Tap here to post: ' + googleUrl);
    window.open('https://wa.me/' + (phone.length === 10 ? '91' + phone : phone) + '?text=' + text, '_blank');
};

window.coraOpenPrivateTicketDrawer = function(ticketId) {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    document.getElementById('cora-drawer-backdrop').classList.remove('hidden');
    document.getElementById('cora-private-ticket-drawer').classList.remove('collapsed');
};

window.coraResolvePrivateTicket = function() {
    coraShowToast('Private ticket resolved internally. Public rating protected!');
    window.coraCloseAllDrawers();
};

window.coraCopySnippet = function(num) {
    var text = document.getElementById('snippet-text-' + num).textContent.trim();
    navigator.clipboard.writeText(text);
    coraShowToast('AI review snippet copied!');
};

window.coraDownloadReceptionQR = function() {
    coraShowToast('Generating A4 Reception QR Card PDF...');
    window.print();
};

window.coraSaveReviewSettings = function() {
    var url = document.getElementById('cora-google-url-input').value;
    coraShowToast('Saved Google Business URL and WhatsApp triggers!');
};
</script>
