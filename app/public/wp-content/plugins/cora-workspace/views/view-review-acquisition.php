<?php
/**
 * Cora Workspace - Reviews & Feedback (Coming Soon / Intent Capture)
 * File: views/view-review-acquisition.php
 * Monochromatic high-converting early access intent-capture screen.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$current_user = wp_get_current_user();
$user_email   = $current_user ? $current_user->user_email : '';
$agency_id    = function_exists( 'cora_get_current_user_agency_id' ) ? cora_get_current_user_agency_id() : 'default';

// Check if user already joined waitlist for reviews
$waitlist_entries = get_option( 'cora_feature_waitlist_reviews', array() );
$already_joined   = false;
if ( is_array( $waitlist_entries ) && ! empty( $user_email ) ) {
    foreach ( $waitlist_entries as $entry ) {
        if ( isset( $entry['email'] ) && strtolower( $entry['email'] ) === strtolower( $user_email ) ) {
            $already_joined = true;
            break;
        }
    }
}

$reviews_header_args = array(
    'title'            => 'Reviews & Feedback',
    'description'      => 'Automated Google 5-star review collection, private reputation protection, and AI review responses.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'Notify Me on Launch',
        'mobile_text' => 'Notify Me',
        'onclick'     => "document.getElementById('cora-reviews-waitlist-email')?.focus()",
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
        'visible'     => true,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $reviews_header_args );
}
?>

<div class="max-w-5xl mx-auto py-8 px-4 space-y-8 select-none">
    
    <!-- Hero Intent Banner Card -->
    <div class="bg-white border border-zinc-200 rounded-3xl p-8 md:p-12 shadow-xs relative overflow-hidden">
        
        <!-- Subtle corner glow -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-zinc-100 rounded-full blur-3xl pointer-events-none opacity-60"></div>

        <div class="relative z-10 max-w-2xl mx-auto text-center space-y-6">
            
            <!-- In Development Badge with Live Indicator Dot -->
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 border border-zinc-200/80 rounded-full text-xs font-semibold text-zinc-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>In Active Development • Early Access Opening Soon</span>
            </div>

            <div class="space-y-3">
                <h2 class="text-2xl md:text-3xl font-extrabold text-zinc-950 tracking-tight">
                    Automated Review Acquisition & Reputation Shield
                </h2>
                <p class="text-sm md:text-base text-zinc-500 leading-relaxed font-normal">
                    Turn every completed project into verified Google 5-star reviews on autopilot. Cora is building a unified reputation co-pilot that sends WhatsApp & SMS review requests, routes 4–5★ praise to Google Maps, and safely captures 1–3★ private feedback before it goes public.
                </p>
            </div>

            <!-- Waitlist / Intent Capture Form -->
            <div id="cora-reviews-intent-container" class="pt-2">
                <?php if ( $already_joined ) : ?>
                <div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 text-center space-y-2">
                    <div class="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center mx-auto shadow-xs">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h4 class="text-sm font-bold text-zinc-900">You're on the Early Access Priority List</h4>
                    <p class="text-xs text-zinc-500 max-w-md mx-auto leading-relaxed">We have registered your workspace (<?php echo esc_html( $user_email ); ?>). You will receive direct private beta access when the Reviews & Feedback module is released.</p>
                </div>
                <?php else : ?>
                <form id="cora-reviews-waitlist-form" onsubmit="coraSubmitReviewWaitlist(event)" class="max-w-md mx-auto space-y-3">
                    <div class="flex flex-col sm:flex-row items-center gap-2">
                        <div class="relative flex-1 w-full">
                            <input 
                                type="email" 
                                id="cora-reviews-waitlist-email" 
                                value="<?php echo esc_attr( $user_email ); ?>" 
                                placeholder="Enter your business email" 
                                required
                                class="w-full h-11 px-4 text-xs bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:border-zinc-950 focus:ring-0 focus:outline-none transition-all placeholder:text-zinc-400 text-zinc-900 font-medium"
                            >
                        </div>
                        <button 
                            type="submit" 
                            id="cora-reviews-submit-btn"
                            class="w-full sm:w-auto h-11 px-6 bg-zinc-950 hover:bg-zinc-800 active:scale-[0.98] text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs flex items-center justify-center gap-2 shrink-0 border-0"
                        >
                            <span>Get Early Access</span>
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    </div>
                    <p class="text-[11px] text-zinc-400">Zero spam. You'll only receive priority launch access and beta test invites.</p>
                </form>
                <?php endif; ?>
            </div>

            <!-- Interactive Feature Preferences Selection -->
            <div class="pt-4 border-t border-zinc-100 space-y-3">
                <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Vote for the features you need first:</p>
                <div class="flex flex-wrap justify-center gap-2" id="cora-reviews-features-tags">
                    <button type="button" onclick="coraToggleReviewFeature(this)" data-feature="whatsapp-invites" class="cora-rev-feature-tag px-3 py-1.5 rounded-full border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100 text-xs font-semibold text-zinc-700 transition-all cursor-pointer active:scale-95 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 feature-dot"></span>
                        <span>WhatsApp 1-Tap Invites</span>
                    </button>
                    <button type="button" onclick="coraToggleReviewFeature(this)" data-feature="reputation-shield" class="cora-rev-feature-tag px-3 py-1.5 rounded-full border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100 text-xs font-semibold text-zinc-700 transition-all cursor-pointer active:scale-95 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 feature-dot"></span>
                        <span>Private Shield (1–3★ Intercept)</span>
                    </button>
                    <button type="button" onclick="coraToggleReviewFeature(this)" data-feature="ai-replies" class="cora-rev-feature-tag px-3 py-1.5 rounded-full border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100 text-xs font-semibold text-zinc-700 transition-all cursor-pointer active:scale-95 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 feature-dot"></span>
                        <span>AI Review Auto-Replies</span>
                    </button>
                    <button type="button" onclick="coraToggleReviewFeature(this)" data-feature="video-proof" class="cora-rev-feature-tag px-3 py-1.5 rounded-full border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100 text-xs font-semibold text-zinc-700 transition-all cursor-pointer active:scale-95 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 feature-dot"></span>
                        <span>Video & Selfie Testimonials</span>
                    </button>
                    <button type="button" onclick="coraToggleReviewFeature(this)" data-feature="embed-widgets" class="cora-rev-feature-tag px-3 py-1.5 rounded-full border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100 text-xs font-semibold text-zinc-700 transition-all cursor-pointer active:scale-95 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 feature-dot"></span>
                        <span>Website Proof Carousels</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Feature Architecture Roadmap Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-zinc-900 tracking-tight">Upcoming Reputation Engine Capabilities</h3>
            <span class="text-xs font-semibold text-zinc-400">Roadmap Phase 1</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Card 1: Autonomous WhatsApp & SMS Review Drips -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs hover:shadow-xs transition-all space-y-3">
                <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-zinc-900">Autonomous WhatsApp & SMS Review Invites</h4>
                    <p class="text-xs text-zinc-500 leading-relaxed">Automatically dispatches personalized review invitations right after a shoot is delivered or deal is closed. Uses direct 1-tap Google Maps review links for maximum completion.</p>
                </div>
                <div class="pt-2 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-semibold text-zinc-600">~78% Open Rate</span>
                    <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-semibold text-zinc-600">Zero Client Friction</span>
                </div>
            </div>

            <!-- Card 2: Private Reputation Shield -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs hover:shadow-xs transition-all space-y-3">
                <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-zinc-900">Private Reputation Shield & Smart Sentiment</h4>
                    <p class="text-xs text-zinc-500 leading-relaxed">Smart sentiment routing detects rating intent. 4–5★ reviews are guided straight to your public Google Business profile, while 1–3★ complaints are captured privately for team resolution.</p>
                </div>
                <div class="pt-2 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-[10px] font-bold text-emerald-700 border border-emerald-200/50">100% Brand Protected</span>
                    <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-semibold text-zinc-600">Internal Inboxing</span>
                </div>
            </div>

            <!-- Card 3: AI Review Response Co-Pilot -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs hover:shadow-xs transition-all space-y-3">
                <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><path d="M8 10h.01M12 10h.01M16 10h.01"></path></svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-zinc-900">AI Review Response Co-Pilot</h4>
                    <p class="text-xs text-zinc-500 leading-relaxed">Draft authentic, personalized, SEO-rich responses to every public review in seconds. Naturally weaves in target local keywords to elevate your Google Maps rankings.</p>
                </div>
                <div class="pt-2 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-semibold text-zinc-600">&lt; 5s AI Drafts</span>
                    <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-semibold text-zinc-600">Local SEO Boost</span>
                </div>
            </div>

            <!-- Card 4: Video Testimonials & Embeddable Proof -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs hover:shadow-xs transition-all space-y-3">
                <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-zinc-900">Video Testimonials & Live Proof Widgets</h4>
                    <p class="text-xs text-zinc-500 leading-relaxed">Collect selfie video testimonials directly from mobile browsers. Seamlessly embed high-converting social proof walls and review carousels onto your website.</p>
                </div>
                <div class="pt-2 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-semibold text-zinc-600">Direct Video Upload</span>
                    <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-semibold text-zinc-600">Embeddable Widgets</span>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
window.coraSelectedReviewFeatures = [];

window.coraToggleReviewFeature = function(btn) {
    var feature = btn.getAttribute('data-feature');
    var dot = btn.querySelector('.feature-dot');
    
    if (window.coraSelectedReviewFeatures.indexOf(feature) === -1) {
        window.coraSelectedReviewFeatures.push(feature);
        btn.classList.remove('bg-zinc-50/50', 'text-zinc-700', 'border-zinc-200');
        btn.classList.add('bg-zinc-950', 'text-white', 'border-zinc-950');
        if (dot) {
            dot.classList.remove('bg-zinc-400');
            dot.classList.add('bg-emerald-400');
        }
    } else {
        window.coraSelectedReviewFeatures = window.coraSelectedReviewFeatures.filter(function(f) { return f !== feature; });
        btn.classList.add('bg-zinc-50/50', 'text-zinc-700', 'border-zinc-200');
        btn.classList.remove('bg-zinc-950', 'text-white', 'border-zinc-950');
        if (dot) {
            dot.classList.add('bg-zinc-400');
            dot.classList.remove('bg-emerald-400');
        }
    }
};

window.coraSubmitReviewWaitlist = function(e) {
    e.preventDefault();
    var emailInput = document.getElementById('cora-reviews-waitlist-email');
    var submitBtn = document.getElementById('cora-reviews-submit-btn');
    if (!emailInput || !emailInput.value.trim()) return;

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Joining...';
    }

    var formData = new FormData();
    formData.append('action', 'cora_capture_feature_intent');
    formData.append('security', (typeof cora_workspace_vars !== 'undefined' ? cora_workspace_vars.nonce : ''));
    formData.append('feature', 'reviews');
    formData.append('email', emailInput.value.trim());
    formData.append('tools', JSON.stringify(window.coraSelectedReviewFeatures));

    fetch(typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(function(res) { return res.json(); })
    .then(function(response) {
        if (response && response.success) {
            if (window.coraShowToast) {
                window.coraShowToast(response.data.message || 'You have joined the early access waitlist for Reviews & Feedback!', 'success');
            }
            var container = document.getElementById('cora-reviews-intent-container');
            if (container) {
                container.innerHTML = 
                    '<div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 text-center space-y-2 animate-in fade-in duration-200">' +
                        '<div class="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center mx-auto shadow-xs">' +
                            '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                        '</div>' +
                        '<h4 class="text-sm font-bold text-zinc-900">You\'re on the Early Access Priority List</h4>' +
                        '<p class="text-xs text-zinc-500 max-w-md mx-auto leading-relaxed">Thank you! We will notify ' + emailInput.value.trim() + ' as soon as the Reviews &amp; Feedback engine opens for early access.</p>' +
                    '</div>';
            }
        } else {
            if (window.coraShowToast) {
                window.coraShowToast((response && response.data && response.data.message) ? response.data.message : 'Registration failed. Please try again.', 'error');
            }
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Get Early Access</span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
            }
        }
    })
    .catch(function() {
        if (window.coraShowToast) {
            window.coraShowToast('Network error. Please try again.', 'error');
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Get Early Access</span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
        }
    });
};
</script>
