<?php
/**
 * Cora Workspace - Workspace Calendar (Frameless Minimalist UI & Official Integrations)
 * File: views/view-calendar.php
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

<div class="max-w-5xl mx-auto py-6 sm:py-10 px-4 space-y-10 font-sans">
    
    <!-- Hero Header Section (Clean Frameless Minimalist) -->
    <div class="text-center max-w-2xl mx-auto space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-800">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Early Access Opening Soon</span>
        </div>

        <h2 class="text-3xl sm:text-4xl font-extrabold text-zinc-950 tracking-tight m-0">
            Unified Calendar &amp; Scheduling
        </h2>
        <p class="text-sm text-zinc-500 leading-relaxed font-normal m-0 max-w-lg mx-auto">
            Bidirectional 2-way sync with your primary calendars. Seamless client booking links and conflict-free crew scheduling.
        </p>

        <!-- Waitlist Intake Bar -->
        <div id="cora-calendar-intent-container" class="pt-3 max-w-md mx-auto">
            <?php if ( $already_joined ) : ?>
            <div class="p-4 bg-zinc-100/70 rounded-2xl text-center space-y-1.5">
                <div class="flex items-center justify-center gap-2 text-xs font-bold text-zinc-900">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-600"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Priority Access Confirmed</span>
                </div>
                <p class="text-[11.5px] text-zinc-500 m-0">We will notify your workspace (<?php echo esc_html( $user_email ); ?>) as soon as invitations roll out.</p>
            </div>
            <?php else : ?>
            <form id="cora-calendar-waitlist-form" onsubmit="coraSubmitCalendarWaitlist(event)" class="flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full">
                    <input 
                        type="email" 
                        id="cora-calendar-waitlist-email" 
                        name="email" 
                        value="<?php echo esc_attr( $user_email ); ?>" 
                        required 
                        placeholder="Enter work email for priority access..." 
                        class="w-full px-4 py-3 bg-zinc-100/80 hover:bg-zinc-100 focus:bg-white border-0 rounded-2xl text-xs text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-950 transition-all font-medium"
                    />
                </div>
                <button 
                    type="submit" 
                    id="cora-calendar-submit-btn" 
                    class="w-full sm:w-auto shrink-0 px-6 py-3 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-2xl transition-all cursor-pointer border-none flex items-center justify-center gap-2 active:scale-98"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    <span>Request Access</span>
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Supported Platforms Grid (No Harsh Outlines · Official Brand Logos) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider m-0">Supported Platforms</h3>
            <span class="text-[11px] font-mono text-zinc-400">OAuth 2.0 Ready</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            
            <!-- 1. Google Calendar -->
            <div class="p-4 bg-zinc-50/70 hover:bg-zinc-100/70 rounded-2xl transition-all flex flex-col justify-between gap-3 group">
                <div class="flex items-start gap-3.5">
                    <div class="shrink-0">
                        <svg viewBox="0 0 192 192" width="32" height="32">
                            <rect width="192" height="192" rx="36" fill="#fff"/>
                            <path d="M144 48H48v96h96V48z" fill="#fff"/>
                            <path d="M144 48v24H48V48h24V32h16v16h32V32h16v16h8z" fill="#EA4335"/>
                            <path d="M144 72H48v72h96V72z" fill="#4285F4"/>
                            <path d="M48 72h24v72H48z" fill="#FBBC04"/>
                            <path d="M48 144h96v8a16 16 0 0 1-16 16H64a16 16 0 0 1-16-16v-8z" fill="#34A853"/>
                            <path d="M144 72v72h16a16 16 0 0 0 16-16V64a16 16 0 0 0-16-16h-16v24z" fill="#188038"/>
                            <text x="96" y="125" font-size="46" font-family="-apple-system, BlinkMacSystemFont, sans-serif" font-weight="700" fill="#1e293b" text-anchor="middle">31</text>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 m-0">Google Calendar</h4>
                        <p class="text-[11px] text-zinc-500 m-0 mt-0.5 leading-normal">Gmail &amp; Google Workspace</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 2-Way Sync
                    </span>
                    <span class="text-[10px] font-mono text-zinc-400">v3 API</span>
                </div>
            </div>

            <!-- 2. Microsoft Outlook -->
            <div class="p-4 bg-zinc-50/70 hover:bg-zinc-100/70 rounded-2xl transition-all flex flex-col justify-between gap-3 group">
                <div class="flex items-start gap-3.5">
                    <div class="shrink-0">
                        <svg viewBox="0 0 48 48" width="32" height="32">
                            <rect width="48" height="48" rx="10" fill="#0078D4"/>
                            <path fill="#0364B8" d="M4 14v22c0 2.2 1.8 4 4 4h32c2.2 0 4-1.8 4-4V14z"/>
                            <path fill="#28A8EA" d="M44 14L24 27 4 14V12c0-2.2 1.8-4 4-4h32c2.2 0 4 1.8 4 4v2z"/>
                            <rect width="18" height="18" x="6" y="15" fill="#005A9E" rx="4"/>
                            <text x="15" y="28" font-size="12" font-family="-apple-system, BlinkMacSystemFont, sans-serif" font-weight="800" fill="#fff" text-anchor="middle">O</text>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 m-0">Microsoft Outlook</h4>
                        <p class="text-[11px] text-zinc-500 m-0 mt-0.5 leading-normal">Microsoft 365 &amp; Exchange</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 2-Way Sync
                    </span>
                    <span class="text-[10px] font-mono text-zinc-400">Graph API</span>
                </div>
            </div>

            <!-- 3. Apple Calendar -->
            <div class="p-4 bg-zinc-50/70 hover:bg-zinc-100/70 rounded-2xl transition-all flex flex-col justify-between gap-3 group">
                <div class="flex items-start gap-3.5">
                    <div class="shrink-0">
                        <svg viewBox="0 0 48 48" width="32" height="32">
                            <rect width="48" height="48" rx="10" fill="#FFFFFF"/>
                            <path d="M0 10C0 4.477 4.477 0 10 0h28c5.523 0 10 4.477 10 10v6H0v-6z" fill="#FF3B30"/>
                            <text x="24" y="37" font-size="20" font-family="-apple-system, BlinkMacSystemFont, sans-serif" font-weight="700" fill="#1C1C1E" text-anchor="middle">17</text>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 m-0">Apple Calendar</h4>
                        <p class="text-[11px] text-zinc-500 m-0 mt-0.5 leading-normal">macOS &amp; iOS iCloud</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> CalDAV
                    </span>
                    <span class="text-[10px] font-mono text-zinc-400">iCloud</span>
                </div>
            </div>

            <!-- 4. Calendly & Cal.com -->
            <div class="p-4 bg-zinc-50/70 hover:bg-zinc-100/70 rounded-2xl transition-all flex flex-col justify-between gap-3 group">
                <div class="flex items-start gap-3.5">
                    <div class="shrink-0">
                        <svg viewBox="0 0 48 48" width="32" height="32">
                            <circle cx="24" cy="24" r="24" fill="#006BFF"/>
                            <path d="M33 24c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c2.35 0 4.5.9 6.1 2.38l-2.4 2.4A5.6 5.6 0 0 0 24 18.6c-2.98 0-5.4 2.42-5.4 5.4s2.42 5.4 5.4 5.4 5.4-2.42 5.4-5.4h3.6z" fill="#fff"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 m-0">Calendly / Cal.com</h4>
                        <p class="text-[11px] text-zinc-500 m-0 mt-0.5 leading-normal">Client Self-Booking Links</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Webhooks
                    </span>
                    <span class="text-[10px] font-mono text-zinc-400">REST API</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Core Capabilities (Clean Minimalist Frameless) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
        <div class="space-y-1.5">
            <h4 class="text-xs font-bold text-zinc-950 m-0">Conflict-Free Roster</h4>
            <p class="text-xs text-zinc-500 leading-relaxed m-0">
                Personal and workspace calendars continuously cross-check to prevent double bookings.
            </p>
        </div>

        <div class="space-y-1.5">
            <h4 class="text-xs font-bold text-zinc-950 m-0">Branded Booking Links</h4>
            <p class="text-xs text-zinc-500 leading-relaxed m-0">
                Custom scheduling pages with pre-set durations, questionnaires, and buffer rules.
            </p>
        </div>

        <div class="space-y-1.5">
            <h4 class="text-xs font-bold text-zinc-950 m-0">Automated Reminders</h4>
            <p class="text-xs text-zinc-500 leading-relaxed m-0">
                Instant confirmation emails and WhatsApp notifications to minimize no-shows.
            </p>
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
            tools: ['Google Calendar', 'Microsoft Outlook', 'Apple Calendar', 'Calendly']
        },
        success: function(response) {
            if (response && response.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(response.data.message || 'Priority access confirmed!', 'success');
                }
                var container = document.getElementById('cora-calendar-intent-container');
                if (container) {
                    container.innerHTML = '<div class="p-4 bg-zinc-100/70 rounded-2xl text-center space-y-1.5 animate-fade-in">' +
                        '<div class="flex items-center justify-center gap-2 text-xs font-bold text-zinc-900">' +
                        '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-600"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                        '<span>Priority Access Confirmed</span>' +
                        '</div>' +
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
