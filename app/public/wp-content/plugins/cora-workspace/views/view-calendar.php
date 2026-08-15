<?php
/**
 * Cora Workspace - Workspace Calendar (Coming Soon / Intent Capture)
 * File: views/view-calendar.php
 * Monochromatic high-converting early access intent-capture screen.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$current_user = wp_get_current_user();
$user_email   = $current_user ? $current_user->user_email : '';
$agency_id    = function_exists( 'cora_get_current_user_agency_id' ) ? cora_get_current_user_agency_id() : 'default';

// Check if user already joined waitlist
$waitlist_entries = get_option( 'cora_feature_waitlist_calendar', array() );
$already_joined   = false;
if ( is_array( $waitlist_entries ) && ! empty( $user_email ) ) {
    foreach ( $waitlist_entries as $entry ) {
        if ( isset( $entry['email'] ) && strtolower( $entry['email'] ) === strtolower( $user_email ) ) {
            $already_joined = true;
            break;
        }
    }
}

$calendar_header_args = array(
    'title'            => 'Workspace Calendar',
    'description'      => 'Unified scheduling, 2-way calendar synchronization, and automated client booking links.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'Notify Me on Launch',
        'mobile_text' => 'Notify Me',
        'onclick'     => "document.getElementById('cora-calendar-waitlist-email')?.focus()",
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
        'visible'     => true,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $calendar_header_args );
}
?>

<div class="max-w-5xl mx-auto py-8 px-4 space-y-8 select-none">
    
    <!-- Hero Intent Banner Card -->
    <div class="bg-white border border-zinc-200 rounded-3xl p-8 md:p-12 shadow-xs relative overflow-hidden">
        
        <!-- Subtle corner glow/accent -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-zinc-100 rounded-full blur-3xl pointer-events-none opacity-60"></div>

        <div class="relative z-10 max-w-2xl mx-auto text-center space-y-6">
            
            <!-- In Development Badge with Live Indicator Dot -->
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 border border-zinc-200/80 rounded-full text-xs font-semibold text-zinc-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>In Active Development • Early Access Opening Soon</span>
            </div>

            <div class="space-y-3">
                <h2 class="text-2xl md:text-3xl font-extrabold text-zinc-950 tracking-tight">
                    Unified Workspace Calendar & Scheduling
                </h2>
                <p class="text-sm md:text-base text-zinc-500 leading-relaxed font-normal">
                    Eliminate double bookings and messy back-and-forth emails. Cora is building a dedicated 2-way sync scheduling engine crafted specifically for client shoots, property showings, and crew shifts.
                </p>
            </div>

            <!-- Waitlist / Intent Capture Form -->
            <div id="cora-calendar-intent-container" class="pt-2">
                <?php if ( $already_joined ) : ?>
                <div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 text-center space-y-2">
                    <div class="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center mx-auto shadow-xs">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h4 class="text-sm font-bold text-zinc-900">You're on the Early Access Priority List</h4>
                    <p class="text-xs text-zinc-500 max-w-md mx-auto leading-relaxed">We have registered your workspace (<?php echo esc_html( $user_email ); ?>). You will receive private beta access as soon as the calendar engine is ready.</p>
                </div>
                <?php else : ?>
                <form id="cora-calendar-waitlist-form" onsubmit="coraSubmitCalendarWaitlist(event)" class="space-y-4">
                    
                    <!-- Tool Selection Pills -->
                    <div class="space-y-2 text-left max-w-lg mx-auto">
                        <label class="block text-xs font-semibold text-zinc-700">Which calendar tools do you currently use?</label>
                        <div class="flex flex-wrap gap-2">
                            <label class="cora-tool-pill cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 bg-white text-xs font-medium text-zinc-700 hover:border-zinc-300 transition-all">
                                <input type="checkbox" name="cora_tools[]" value="Google Calendar" class="hidden" onchange="coraTogglePill(this)" checked>
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <span>Google Calendar</span>
                            </label>
                            <label class="cora-tool-pill cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 bg-white text-xs font-medium text-zinc-700 hover:border-zinc-300 transition-all">
                                <input type="checkbox" name="cora_tools[]" value="Microsoft Outlook" class="hidden" onchange="coraTogglePill(this)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 3v18"></path></svg>
                                <span>Microsoft Outlook</span>
                            </label>
                            <label class="cora-tool-pill cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 bg-white text-xs font-medium text-zinc-700 hover:border-zinc-300 transition-all">
                                <input type="checkbox" name="cora_tools[]" value="Apple Calendar" class="hidden" onchange="coraTogglePill(this)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v20M2 12h20"></path></svg>
                                <span>Apple Calendar</span>
                            </label>
                            <label class="cora-tool-pill cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 bg-white text-xs font-medium text-zinc-700 hover:border-zinc-300 transition-all">
                                <input type="checkbox" name="cora_tools[]" value="Calendly" class="hidden" onchange="coraTogglePill(this)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span>Calendly / Cal.com</span>
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
                                id="cora-calendar-waitlist-email" 
                                name="email" 
                                value="<?php echo esc_attr( $user_email ); ?>" 
                                required 
                                placeholder="Enter your work email..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 hover:bg-zinc-100/50 focus:bg-white border border-zinc-200 rounded-xl text-xs text-zinc-900 placeholder-zinc-400 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                            />
                        </div>
                        <button 
                            type="submit" 
                            id="cora-calendar-submit-btn" 
                            class="w-full sm:w-auto shrink-0 px-6 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer border-none flex items-center justify-center gap-2"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            <span>Notify Me on Launch</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-zinc-450">We will notify your workspace owner directly when beta testing opens.</p>
                </form>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Feature Architecture Preview Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Feature 1: 2-Way Sync Engine -->
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 space-y-3.5 shadow-2xs hover:border-zinc-300 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"></path></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900">2-Way Live Sync</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">
                    Connect Google Calendar and Microsoft 365. Block personal events automatically and sync booked shoots or client property visits bidirectionally.
                </p>
            </div>
            <div class="pt-2 flex items-center gap-2 text-[11px] font-semibold text-zinc-400">
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                <span>Google & Outlook OAuth</span>
            </div>
        </div>

        <!-- Feature 2: Client Self-Booking Pages -->
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 space-y-3.5 shadow-2xs hover:border-zinc-300 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900">Client Booking Links</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">
                    Generate branded booking links for clients. Send personalized links with pre-set durations, custom questions, and buffer times between appointments.
                </p>
            </div>
            <div class="pt-2 flex items-center gap-2 text-[11px] font-semibold text-zinc-400">
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                <span>Automated WhatsApp & Email Reminders</span>
            </div>
        </div>

        <!-- Feature 3: Smart Conflict Detector -->
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 space-y-3.5 shadow-2xs hover:border-zinc-300 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-zinc-900">Crew & Gear Roster</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">
                    Check gear reservations and crew shift availability in real-time. Prevents assigning double-booked photographers, drone kits, or brokers to overlapping shoots.
                </p>
            </div>
            <div class="pt-2 flex items-center gap-2 text-[11px] font-semibold text-zinc-400">
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                <span>Zero Roster Overlaps</span>
            </div>
        </div>

    </div>

</div>

<script>
function coraTogglePill(checkbox) {
    var label = checkbox.closest('.cora-tool-pill');
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
document.querySelectorAll('.cora-tool-pill input[type="checkbox"]').forEach(function(cb) {
    if (cb.checked) {
        coraTogglePill(cb);
    }
});

function coraSubmitCalendarWaitlist(e) {
    e.preventDefault();
    var btn = document.getElementById('cora-calendar-submit-btn');
    var emailInput = document.getElementById('cora-calendar-waitlist-email');
    if (!emailInput || !emailInput.value.trim()) {
        if (window.coraShowToast) window.coraShowToast('Please enter a valid email address.', 'error');
        return;
    }

    var selectedTools = [];
    document.querySelectorAll('.cora-tool-pill input[type="checkbox"]:checked').forEach(function(cb) {
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
            feature: 'calendar',
            email: emailInput.value.trim(),
            tools: selectedTools
        },
        success: function(response) {
            if (response && response.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(response.data.message || 'You have joined the early access waitlist!', 'success');
                }
                var container = document.getElementById('cora-calendar-intent-container');
                if (container) {
                    container.innerHTML = '<div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 text-center space-y-2 animate-fade-in">' +
                        '<div class="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center mx-auto shadow-xs">' +
                        '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                        '</div>' +
                        '<h4 class="text-sm font-bold text-zinc-900">You\'re on the Early Access Priority List</h4>' +
                        '<p class="text-xs text-zinc-500 max-w-md mx-auto leading-relaxed">Thank you for your interest! We will notify ' + emailInput.value.trim() + ' as soon as the Workspace Calendar launches.</p>' +
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
