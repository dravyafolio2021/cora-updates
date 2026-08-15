<?php
/**
 * Cora Workspace - Google Business Profile (Coming Soon / Intent Capture)
 * File: views/view-google-profile.php
 * Monochromatic high-converting early access intent-capture screen.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$current_user = wp_get_current_user();
$user_email   = $current_user ? $current_user->user_email : '';
$agency_id    = function_exists( 'cora_get_current_user_agency_id' ) ? cora_get_current_user_agency_id() : 'default';

// Check if user already joined waitlist
$waitlist_entries = get_option( 'cora_feature_waitlist_gbp', array() );
$already_joined   = false;
if ( is_array( $waitlist_entries ) && ! empty( $user_email ) ) {
    foreach ( $waitlist_entries as $entry ) {
        if ( isset( $entry['email'] ) && strtolower( $entry['email'] ) === strtolower( $user_email ) ) {
            $already_joined = true;
            break;
        }
    }
}

$gbp_header_args = array(
    'title'            => 'Google Profile',
    'description'      => 'Autonomous Google Business Profile sync, AI review responses, and Maps rank monitoring.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0" style="stroke: none !important; fill: none !important;"><circle cx="12" cy="12" r="11" fill="#ffffff" style="fill: #ffffff !important; stroke: #e4e4e7 !important; stroke-width: 0.8px !important;"></circle><g transform="matrix(0.55 0 0 0.55 5.4 5.4)"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" style="fill: #4285F4 !important; stroke: none !important;"></path><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" style="fill: #34A853 !important; stroke: none !important;"></path><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05" style="fill: #FBBC05 !important; stroke: none !important;"></path><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335" style="fill: #EA4335 !important; stroke: none !important;"></path></g></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'Notify Me on Launch',
        'mobile_text' => 'Notify Me',
        'onclick'     => "document.getElementById('cora-gbp-waitlist-email')?.focus()",
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
        'visible'     => true,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $gbp_header_args );
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
                    Google Business Profile & Maps AI Autopilot
                </h2>
                <p class="text-sm md:text-base text-zinc-500 leading-relaxed font-normal">
                    Turn your Google Maps presence into an automatic inbound client magnet. Cora is building a unified reputation co-pilot to manage multi-location listings, publish localized updates, and draft instant 5-star review replies with AI.
                </p>
            </div>

            <!-- Waitlist / Intent Capture Form -->
            <div id="cora-gbp-intent-container" class="pt-2">
                <?php if ( $already_joined ) : ?>
                <div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 text-center space-y-2">
                    <div class="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center mx-auto shadow-xs">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h4 class="text-sm font-bold text-zinc-900">You're on the Early Access Priority List</h4>
                    <p class="text-xs text-zinc-500 max-w-md mx-auto leading-relaxed">We have registered your workspace (<?php echo esc_html( $user_email ); ?>). You will receive direct private beta access when the Google Profile AI suite is released.</p>
                </div>
                <?php else : ?>
                <form id="cora-gbp-waitlist-form" onsubmit="coraSubmitGbpWaitlist(event)" class="space-y-4">
                    
                    <!-- Feature Interest Pills -->
                    <div class="space-y-2 text-left max-w-lg mx-auto">
                        <label class="block text-xs font-semibold text-zinc-700">Which Google Profile features do you need most?</label>
                        <div class="flex flex-wrap gap-2">
                            <label class="cora-gbp-pill cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 bg-white text-xs font-medium text-zinc-700 hover:border-zinc-300 transition-all">
                                <input type="checkbox" name="cora_gbp_tools[]" value="AI Review Responses" class="hidden" onchange="coraToggleGbpPill(this)" checked>
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                <span>AI Review Reply Autopilot</span>
                            </label>
                            <label class="cora-gbp-pill cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 bg-white text-xs font-medium text-zinc-700 hover:border-zinc-300 transition-all">
                                <input type="checkbox" name="cora_gbp_tools[]" value="Maps Posts Scheduler" class="hidden" onchange="coraToggleGbpPill(this)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                <span>Google Maps Post Publisher</span>
                            </label>
                            <label class="cora-gbp-pill cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 bg-white text-xs font-medium text-zinc-700 hover:border-zinc-300 transition-all">
                                <input type="checkbox" name="cora_gbp_tools[]" value="Multi-Location Sync" class="hidden" onchange="coraToggleGbpPill(this)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span>Multi-Branch / Multi-Location Sync</span>
                            </label>
                            <label class="cora-gbp-pill cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 bg-white text-xs font-medium text-zinc-700 hover:border-zinc-300 transition-all">
                                <input type="checkbox" name="cora_gbp_tools[]" value="Local SEO & Rankings" class="hidden" onchange="coraToggleGbpPill(this)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                <span>Local SEO & Map-Pack Rankings</span>
                            </label>
                        </div>
                    </div>

                    <!-- Email & Submit Row -->
                    <div class="flex flex-col sm:flex-row items-center gap-2.5 max-w-lg mx-auto">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </div>
                            <input 
                                type="email" 
                                id="cora-gbp-waitlist-email" 
                                name="email" 
                                value="<?php echo esc_attr( $user_email ); ?>" 
                                required 
                                placeholder="Enter your work email..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 hover:bg-zinc-100/50 focus:bg-white border border-zinc-200 rounded-xl text-xs text-zinc-900 placeholder-zinc-400 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                            />
                        </div>
                        <button 
                            type="submit" 
                            id="cora-gbp-submit-btn" 
                            class="w-full sm:w-auto shrink-0 px-6 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer border-none flex items-center justify-center gap-2"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            <span>Notify Me on Launch</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-zinc-450">We will notify your workspace administrator directly when beta testing opens.</p>
                </form>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Feature Architecture Preview Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Feature 1: AI Review Autopilot -->
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 space-y-3.5 shadow-2xs hover:border-zinc-300 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900">AI Review Reply Autopilot</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">
                    Never leave a review unanswered. Cora drafts personalized, brand-aligned responses to positive and negative reviews within seconds.
                </p>
            </div>
            <div class="pt-2 flex items-center gap-2 text-[11px] font-semibold text-zinc-400">
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                <span>Context-Aware Sentiment AI</span>
            </div>
        </div>

        <!-- Feature 2: Maps Post & Offer Publisher -->
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 space-y-3.5 shadow-2xs hover:border-zinc-300 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900">Maps Post Publisher</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">
                    Broadcast new property listings, studio photoshoot offers, and business hours changes directly to your Google Maps pin in one click.
                </p>
            </div>
            <div class="pt-2 flex items-center gap-2 text-[11px] font-semibold text-zinc-400">
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                <span>Scheduled Auto-Broadcasts</span>
            </div>
        </div>

        <!-- Feature 3: Local Search & Citation Rankings -->
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 space-y-3.5 shadow-2xs hover:border-zinc-300 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900">GEO Search & Rank Heatmaps</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">
                    Monitor how high your listing ranks when customers search for real estate agents or photo studios in your city's prime neighborhoods.
                </p>
            </div>
            <div class="pt-2 flex items-center gap-2 text-[11px] font-semibold text-zinc-400">
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                <span>Local Map-Pack Analytics</span>
            </div>
        </div>

    </div>

</div>

<script>
function coraToggleGbpPill(checkbox) {
    var label = checkbox.closest('.cora-gbp-pill');
    if (!label) return;
    if (checkbox.checked) {
        label.classList.add('bg-zinc-950', 'text-white', 'border-zinc-950');
        label.classList.remove('bg-white', 'text-zinc-700', 'border-zinc-200');
    } else {
        label.classList.remove('bg-zinc-950', 'text-white', 'border-zinc-950');
        label.classList.add('bg-white', 'text-zinc-700', 'border-zinc-200');
    }
}

// Initialize active pills
document.querySelectorAll('.cora-gbp-pill input[type="checkbox"]').forEach(function(cb) {
    if (cb.checked) {
        coraToggleGbpPill(cb);
    }
});

function coraSubmitGbpWaitlist(e) {
    e.preventDefault();
    var btn = document.getElementById('cora-gbp-submit-btn');
    var emailInput = document.getElementById('cora-gbp-waitlist-email');
    if (!emailInput || !emailInput.value.trim()) {
        if (window.coraShowToast) window.coraShowToast('Please enter a valid email address.', 'error');
        return;
    }

    var selectedTools = [];
    document.querySelectorAll('.cora-gbp-pill input[type="checkbox"]:checked').forEach(function(cb) {
        selectedTools.push(cb.value);
    });

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Submitting...';
    }

    var ajaxUrl = window.cora_vars ? window.cora_vars.ajax_url : '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = window.cora_vars ? window.cora_vars.nonce : '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_capture_feature_intent',
            security: nonce,
            feature: 'gbp',
            email: emailInput.value.trim(),
            tools: selectedTools
        },
        success: function(response) {
            if (response && response.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(response.data.message || 'You have joined the early access waitlist for Google Profile!', 'success');
                }
                var container = document.getElementById('cora-gbp-intent-container');
                if (container) {
                    container.innerHTML = '<div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 text-center space-y-2 animate-fade-in">' +
                        '<div class="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center mx-auto shadow-xs">' +
                        '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                        '</div>' +
                        '<h4 class="text-sm font-bold text-zinc-900">You\'re on the Early Access Priority List</h4>' +
                        '<p class="text-xs text-zinc-500 max-w-md mx-auto leading-relaxed">Thank you for your interest! We will notify ' + emailInput.value.trim() + ' as soon as the Google Profile AI Suite launches.</p>' +
                        '</div>';
                }
            } else {
                var err = (response && response.data && response.data.message) ? response.data.message : 'Submission failed. Please try again.';
                if (window.coraShowToast) window.coraShowToast(err, 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span>Notify Me on Launch</span>';
                }
            }
        },
        error: function() {
            if (window.coraShowToast) window.coraShowToast('Network error. Please try again.', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span>Notify Me on Launch</span>';
            }
        }
    });
}
</script>
