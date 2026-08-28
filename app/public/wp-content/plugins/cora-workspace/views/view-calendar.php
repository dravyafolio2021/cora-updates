<?php
/**
 * Cora Workspace - Workspace Calendar (Clean Minimalist Hub & 2-Way Sync)
 * File: views/view-calendar.php
 * Monochromatic, high-converting, professional scheduling engine.
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
    'description'      => 'Unified 2-way calendar sync, client self-booking links, and crew shift rosters.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'Priority Access',
        'mobile_text' => 'Join',
        'onclick'     => "document.getElementById('cora-calendar-waitlist-email')?.focus()",
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
        'visible'     => true,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $calendar_header_args );
}
?>

<div class="max-w-6xl mx-auto py-6 sm:py-8 px-4 space-y-6 sm:space-y-8 select-none font-sans">
    
    <!-- Hero Intent Banner Card (Minimalist Clean Aesthetic) -->
    <div class="bg-white border border-zinc-200/90 rounded-3xl p-6 sm:p-10 shadow-2xs relative overflow-hidden">
        <div class="max-w-2xl mx-auto text-center space-y-5">
            
            <!-- In Development Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100/80 border border-zinc-200/70 rounded-full text-xs font-semibold text-zinc-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Early Access Opening Soon</span>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-zinc-950 tracking-tight m-0">
                    Unified Calendar &amp; Scheduling
                </h2>
                <p class="text-xs sm:text-sm text-zinc-500 leading-relaxed font-medium m-0 max-w-lg mx-auto">
                    Bidirectional 2-way sync with your primary calendars. Seamless client booking links and conflict-free crew scheduling.
                </p>
            </div>

            <!-- Waitlist / Intent Form -->
            <div id="cora-calendar-intent-container" class="pt-2">
                <?php if ( $already_joined ) : ?>
                <div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-5 text-center space-y-2 max-w-md mx-auto">
                    <div class="w-9 h-9 rounded-xl bg-zinc-950 text-white flex items-center justify-center mx-auto shadow-xs">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900 m-0">Priority Access Confirmed</h4>
                    <p class="text-[11.5px] text-zinc-500 m-0">We will notify your workspace (<?php echo esc_html( $user_email ); ?>) as soon as beta invites go live.</p>
                </div>
                <?php else : ?>
                <form id="cora-calendar-waitlist-form" onsubmit="coraSubmitCalendarWaitlist(event)" class="space-y-4 max-w-lg mx-auto">
                    
                    <!-- Email & Submit Row -->
                    <div class="flex flex-col sm:flex-row items-center gap-2">
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
                                placeholder="Enter work email for priority access..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-zinc-50/80 hover:bg-zinc-100/50 focus:bg-white border border-zinc-200 rounded-xl text-xs text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-950 transition-all font-medium"
                            />
                        </div>
                        <button 
                            type="submit" 
                            id="cora-calendar-submit-btn" 
                            class="w-full sm:w-auto shrink-0 px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer border-none flex items-center justify-center gap-2 active:scale-98"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            <span>Request Access</span>
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Official Ecosystem Integrations Matrix -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-zinc-900 uppercase tracking-wider m-0">Supported Platforms &amp; 2-Way Sync</h3>
            <span class="text-[11px] font-mono font-semibold text-zinc-400">OAuth 2.0 Ready</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            
            <!-- 1. Google Calendar -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 transition-all flex flex-col justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-150 flex items-center justify-center shrink-0 shadow-3xs">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none">
                            <rect x="3" y="4" width="18" height="17" rx="3" fill="#ffffff" stroke="#4285F4" stroke-width="1.8"/>
                            <path d="M3 8h18" stroke="#4285F4" stroke-width="1.8"/>
                            <path d="M8 2v4M16 2v4" stroke="#EA4335" stroke-width="1.8" stroke-linecap="round"/>
                            <text x="12" y="16.5" fill="#1e293b" font-size="7.5" font-family="system-ui, sans-serif" font-weight="bold" text-anchor="middle">31</text>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 m-0">Google Calendar</h4>
                        <p class="text-[11px] text-zinc-500 m-0 mt-0.5 leading-normal">Bidirectional sync with Gmail &amp; Google Workspace.</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-100">
                    <span class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 2-Way Sync
                    </span>
                    <span class="text-[10px] font-mono text-zinc-400">v3 API</span>
                </div>
            </div>

            <!-- 2. Microsoft Outlook / 365 -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 transition-all flex flex-col justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-150 flex items-center justify-center shrink-0 shadow-3xs">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none">
                            <rect x="3" y="4" width="18" height="16" rx="3" fill="#0078D4"/>
                            <path d="M3 7l9 6 9-6" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="8" cy="14.5" r="3" fill="#ffffff" fill-opacity="0.25"/>
                            <path d="M6.5 13h3v3h-3z" fill="#ffffff"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 m-0">Microsoft Outlook</h4>
                        <p class="text-[11px] text-zinc-500 m-0 mt-0.5 leading-normal">Direct sync with Microsoft 365 &amp; Exchange.</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-100">
                    <span class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 2-Way Sync
                    </span>
                    <span class="text-[10px] font-mono text-zinc-400">Graph API</span>
                </div>
            </div>

            <!-- 3. Apple Calendar -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 transition-all flex flex-col justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-150 flex items-center justify-center shrink-0 shadow-3xs">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none">
                            <rect x="3" y="3" width="18" height="18" rx="4" fill="#ffffff" stroke="#e2e8f0" stroke-width="1.2"/>
                            <rect x="3" y="3" width="18" height="5" rx="3" fill="#FF3B30"/>
                            <text x="12" y="16.5" fill="#000000" font-size="8" font-family="-apple-system, sans-serif" font-weight="700" text-anchor="middle">17</text>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 m-0">Apple Calendar</h4>
                        <p class="text-[11px] text-zinc-500 m-0 mt-0.5 leading-normal">Native sync with macOS &amp; iOS devices.</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-100">
                    <span class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> CalDAV
                    </span>
                    <span class="text-[10px] font-mono text-zinc-400">iCloud</span>
                </div>
            </div>

            <!-- 4. Cal.com & Calendly -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 transition-all flex flex-col justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-150 flex items-center justify-center shrink-0 shadow-3xs">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none">
                            <circle cx="12" cy="12" r="9" fill="#006BFF"/>
                            <path d="M8 12a4 4 0 1 1 8 0 4 4 0 0 1-8 0z" stroke="#ffffff" stroke-width="1.8"/>
                            <circle cx="12" cy="12" r="1.5" fill="#ffffff"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 m-0">Cal.com &amp; Calendly</h4>
                        <p class="text-[11px] text-zinc-500 m-0 mt-0.5 leading-normal">Client booking links with custom buffers.</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-100">
                    <span class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Webhooks
                    </span>
                    <span class="text-[10px] font-mono text-zinc-400">REST API</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Interactive Calendar Architecture & Feature Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Card 1: 2-Way Conflict Elimination -->
        <div class="bg-white border border-zinc-200/90 rounded-2xl p-5 space-y-2.5 shadow-2xs">
            <div class="w-8 h-8 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-900">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"></path></svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-zinc-950 m-0">Zero Overlaps</h4>
                <p class="text-[11px] text-zinc-500 leading-normal m-0 mt-1">
                    Personal and team events automatically block client booking slots in real time.
                </p>
            </div>
        </div>

        <!-- Card 2: Client Booking Pages -->
        <div class="bg-white border border-zinc-200/90 rounded-2xl p-5 space-y-2.5 shadow-2xs">
            <div class="w-8 h-8 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-900">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-zinc-950 m-0">Branded Booking Links</h4>
                <p class="text-[11px] text-zinc-500 leading-normal m-0 mt-1">
                    Send personalized links with custom questionnaire fields and automated reminders.
                </p>
            </div>
        </div>

        <!-- Card 3: Crew & Gear Roster -->
        <div class="bg-white border border-zinc-200/90 rounded-2xl p-5 space-y-2.5 shadow-2xs">
            <div class="w-8 h-8 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-900">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-zinc-950 m-0">Crew &amp; Gear Roster</h4>
                <p class="text-[11px] text-zinc-500 leading-normal m-0 mt-1">
                    Assign photographers, drone kits, and team shifts without double-booking equipment.
                </p>
            </div>
        </div>

    </div>

</div>

<script>
function coraSubmitCalendarWaitlist(e) {
    e.preventDefault();
    var btn = document.getElementById('cora-calendar-submit-btn');
    var emailInput = document.getElementById('cora-calendar-waitlist-email');
    if (!emailInput || !emailInput.value.trim()) {
        if (window.coraShowToast) window.coraShowToast('Please enter a valid email address.', 'error');
        return;
    }

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin" viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path></svg> <span>Saving...</span>';
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
            tools: ['Google Calendar', 'Microsoft Outlook', 'Apple Calendar', 'Cal.com']
        },
        success: function(response) {
            if (response && response.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(response.data.message || 'You have joined the early access priority list!', 'success');
                }
                var container = document.getElementById('cora-calendar-intent-container');
                if (container) {
                    container.innerHTML = '<div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-5 text-center space-y-2 max-w-md mx-auto animate-fade-in">' +
                        '<div class="w-9 h-9 rounded-xl bg-zinc-950 text-white flex items-center justify-center mx-auto shadow-xs">' +
                        '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                        '</div>' +
                        '<h4 class="text-xs font-bold text-zinc-900 m-0">Priority Access Confirmed</h4>' +
                        '<p class="text-[11.5px] text-zinc-500 m-0">Thank you! We will notify ' + emailInput.value.trim() + ' as soon as early access opens.</p>' +
                        '</div>';
                }
            } else {
                var err = (response && response.data && response.data.message) ? response.data.message : 'Submission failed. Please try again.';
                if (window.coraShowToast) window.coraShowToast(err, 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span>Request Access</span>';
                }
            }
        },
        error: function() {
            if (window.coraShowToast) window.coraShowToast('Network error. Please try again.', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span>Request Access</span>';
            }
        }
    });
}
</script>
