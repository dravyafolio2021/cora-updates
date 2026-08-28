<?php
/**
 * Cora Workspace - Workspace Calendar (Impactful Minimalist Preview & 2-Way Sync)
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

<div class="max-w-5xl mx-auto py-8 sm:py-12 px-4 space-y-10 font-sans">
    
    <!-- Hero Header Section -->
    <div class="text-center space-y-4 max-w-2xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-800 border border-zinc-200/60">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Early Access Opening Soon</span>
        </div>

        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-zinc-950 tracking-tight m-0 leading-tight">
            Unified Calendar &amp; Scheduling
        </h2>
        <p class="text-xs sm:text-sm text-zinc-500 leading-relaxed font-normal m-0 max-w-lg mx-auto">
            Bidirectional 2-way synchronization with your primary calendar tools. Seamless client booking links, buffer rules, and conflict-free crew scheduling.
        </p>

        <!-- Waitlist Intake Bar -->
        <div id="cora-calendar-intent-container" class="pt-2 max-w-md mx-auto">
            <?php if ( $already_joined ) : ?>
            <div class="p-3.5 bg-zinc-100/70 rounded-2xl text-center space-y-1">
                <div class="flex items-center justify-center gap-2 text-xs font-bold text-zinc-900">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-600"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Priority Access Confirmed</span>
                </div>
                <p class="text-[11.5px] text-zinc-500 m-0">We will notify <?php echo esc_html( $user_email ); ?> as soon as beta invites roll out.</p>
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
                        class="w-full px-4 py-3 bg-zinc-100/80 hover:bg-zinc-100 focus:bg-white border-0 rounded-2xl text-xs text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-950 transition-all font-medium shadow-3xs"
                    />
                </div>
                <button 
                    type="submit" 
                    id="cora-calendar-submit-btn" 
                    class="w-full sm:w-auto shrink-0 px-6 py-3 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-2xl transition-all cursor-pointer border-none flex items-center justify-center gap-2 active:scale-98 shadow-xs"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    <span>Request Access</span>
                </button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Official Platform Sync Badges Row -->
        <div class="pt-3 flex items-center justify-center gap-4 sm:gap-6 flex-wrap select-none">
            <!-- Google Calendar -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-50 hover:bg-zinc-100/80 transition-colors border border-zinc-200/50" title="Google Calendar & Workspace (2-Way Sync)">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <rect width="24" height="24" rx="5" fill="#FFFFFF"/>
                    <path d="M19 4H18V2H16V4H8V2H6V4H5C3.89 4 3.01 4.9 3.01 6L3 20C3 21.1 3.89 22 5 22H19C20.1 22 21 21.1 21 20V6C21 4.9 20.1 4 19 4Z" fill="#1A73E8"/>
                    <path d="M19 20H5V8H19V20Z" fill="#FFFFFF"/>
                    <path d="M7 10H17V12H7V10ZM7 14H14V16H7V14Z" fill="#1A73E8"/>
                    <rect x="5" y="4" width="14" height="3" fill="#EA4335"/>
                </svg>
                <span class="text-xs font-semibold text-zinc-800">Google Calendar</span>
            </div>

            <!-- Microsoft Outlook -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-50 hover:bg-zinc-100/80 transition-colors border border-zinc-200/50" title="Microsoft Outlook & Office 365 (2-Way Sync)">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <rect width="24" height="24" rx="5" fill="#0078D4"/>
                    <path d="M3 7l9 6 9-6v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z" fill="#005A9E"/>
                    <path d="M21 7L12 13 3 7V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v1z" fill="#28A8EA"/>
                    <circle cx="8" cy="14" r="3.5" fill="#FFFFFF" fill-opacity="0.95"/>
                    <text x="8" y="16.5" font-size="6.5" font-family="sans-serif" font-weight="900" fill="#005A9E" text-anchor="middle">O</text>
                </svg>
                <span class="text-xs font-semibold text-zinc-800">Outlook</span>
            </div>

            <!-- Apple Calendar -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-50 hover:bg-zinc-100/80 transition-colors border border-zinc-200/50" title="Apple Calendar & iCloud (CalDAV)">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <rect width="24" height="24" rx="5" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="0.8"/>
                    <path d="M0 5C0 2.238 2.238 0 5 0h14c2.762 0 5 2.238 5 5v3H0V5z" fill="#FF3B30"/>
                    <text x="12" y="18.5" font-size="10" font-family="-apple-system, BlinkMacSystemFont, sans-serif" font-weight="700" fill="#1C1C1E" text-anchor="middle">17</text>
                </svg>
                <span class="text-xs font-semibold text-zinc-800">Apple Calendar</span>
            </div>

            <!-- Cal.com & Calendly -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-50 hover:bg-zinc-100/80 transition-colors border border-zinc-200/50" title="Cal.com & Calendly (Booking Links)">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <rect width="24" height="24" rx="5" fill="#006BFF"/>
                    <path d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0z" stroke="#FFFFFF" stroke-width="1.8"/>
                    <circle cx="12" cy="12" r="1.5" fill="#FFFFFF"/>
                </svg>
                <span class="text-xs font-semibold text-zinc-800">Cal.com &amp; Calendly</span>
            </div>
        </div>
    </div>

    <!-- Impactful Interactive Calendar Workspace Showcase Card (Linear / Notion Aesthetic) -->
    <div class="bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-7 shadow-xs space-y-6">
        
        <!-- Showcase Toolbar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-zinc-150">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-zinc-100 text-xs font-bold text-zinc-900">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span>August 2026</span>
                </div>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    2-Way Live Sync Active
                </span>
            </div>

            <!-- View Mode Switcher Preview -->
            <div class="flex items-center gap-1 bg-zinc-100 p-1 rounded-xl text-xs font-semibold text-zinc-600">
                <button type="button" class="px-3 py-1 rounded-lg bg-white text-zinc-950 shadow-3xs border-none cursor-pointer">Week</button>
                <button type="button" class="px-3 py-1 rounded-lg text-zinc-500 hover:text-zinc-900 border-none bg-transparent cursor-pointer">Month</button>
                <button type="button" class="px-3 py-1 rounded-lg text-zinc-500 hover:text-zinc-900 border-none bg-transparent cursor-pointer">Agenda</button>
            </div>
        </div>

        <!-- Visual Interactive Schedule Stream -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Event 1: Commercial Shoot -->
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/60 flex flex-col justify-between gap-3 hover:bg-zinc-100/70 transition-all">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10.5px] font-mono font-bold text-zinc-400">TODAY • 10:00 AM - 01:00 PM</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            Google Synced
                        </span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-950 m-0">Commercial Architecture Shoot</h4>
                    <p class="text-[11px] text-zinc-500 m-0 leading-relaxed">Apex Realty Group • Cyber City HQ • 4K Video Walkthrough &amp; Still HDR</p>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-200/40 text-[11px]">
                    <span class="font-semibold text-zinc-700">₹1,50,000 + GST</span>
                    <span class="text-emerald-600 font-bold">Confirmed</span>
                </div>
            </div>

            <!-- Event 2: Client Consultation Booking Link -->
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/60 flex flex-col justify-between gap-3 hover:bg-zinc-100/70 transition-all">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10.5px] font-mono font-bold text-zinc-400">TOMORROW • 02:30 PM</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                            Cal.com Link
                        </span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-950 m-0">Wedding Photography Consultation</h4>
                    <p class="text-[11px] text-zinc-500 m-0 leading-relaxed">Arjun &amp; Priya • Video Call • Automated WhatsApp &amp; Email Reminders Dispatched</p>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-200/40 text-[11px]">
                    <span class="font-semibold text-zinc-700">45 Min Discovery</span>
                    <span class="text-purple-600 font-bold">Self-Booked</span>
                </div>
            </div>

            <!-- Event 3: Crew & Gear Shift -->
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/60 flex flex-col justify-between gap-3 hover:bg-zinc-100/70 transition-all">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10.5px] font-mono font-bold text-zinc-400">THURSDAY • 11:00 AM</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            Crew Locked
                        </span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-950 m-0">Delhi Fashion Week 2026 Coverage</h4>
                    <p class="text-[11px] text-zinc-500 m-0 leading-relaxed">Assigned to Kavya Patel • Reserved Gear: Sony FX3 + DJI Mavic 3 Pro</p>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-200/40 text-[11px]">
                    <span class="font-semibold text-zinc-700">Zero Overlaps</span>
                    <span class="text-amber-700 font-bold">Shift Assigned</span>
                </div>
            </div>

        </div>

        <!-- Capability Highlights Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-zinc-150 text-left">
            <div class="space-y-1">
                <h5 class="text-xs font-bold text-zinc-950 m-0 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"></path></svg>
                    <span>Instant 2-Way Sync</span>
                </h5>
                <p class="text-[11px] text-zinc-500 leading-normal m-0">Personal events automatically block client booking availability in real time.</p>
            </div>

            <div class="space-y-1">
                <h5 class="text-xs font-bold text-zinc-950 m-0 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    <span>Branded Booking Pages</span>
                </h5>
                <p class="text-[11px] text-zinc-500 leading-normal m-0">Custom scheduling URLs with deposit collection and questionnaire fields.</p>
            </div>

            <div class="space-y-1">
                <h5 class="text-xs font-bold text-zinc-950 m-0 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>Crew &amp; Gear Protection</span>
                </h5>
                <p class="text-[11px] text-zinc-500 leading-normal m-0">Prevent double booking photographers, drone kits, and studio bays.</p>
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
                    window.coraShowToast(response.data.message || 'Priority access confirmed!', 'success');
                }
                var container = document.getElementById('cora-calendar-intent-container');
                if (container) {
                    container.innerHTML = '<div class="p-3.5 bg-zinc-100/70 rounded-2xl text-center space-y-1 animate-fade-in">' +
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
