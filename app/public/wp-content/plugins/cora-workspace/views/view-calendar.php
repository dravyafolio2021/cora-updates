<?php
/**
 * Cora Workspace - Workspace Calendar
 * File: views/view-calendar.php
 * Premium, monochromatic high-fidelity interactive calendar dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Sample calendar data
$calendar_events = array(
    array(
        'id'          => 1,
        'title'       => 'Luxury Villa Shoot',
        'type'        => 'Shoot',
        'client'      => 'Vipul Malhotra',
        'time'        => '09:00 AM - 01:00 PM',
        'status'      => 'Confirmed',
        'status_color'=> 'emerald',
        'day'         => 14
    ),
    array(
        'id'          => 2,
        'title'       => 'Client Due Diligence Tour',
        'type'        => 'Showing',
        'client'      => 'Aria Group Singapore',
        'time'        => '02:30 PM - 05:00 PM',
        'status'      => 'In Progress',
        'status_color'=> 'blue',
        'day'         => 14
    ),
    array(
        'id'          => 3,
        'title'       => 'DLF CyberCity Drone Video',
        'type'        => 'Shoot',
        'client'      => 'Apex Capital',
        'time'        => '10:00 AM - 02:00 PM',
        'status'      => 'Scheduled',
        'status_color'=> 'zinc',
        'day'         => 15
    ),
    array(
        'id'          => 4,
        'title'       => 'Studio Portrait Session',
        'type'        => 'Portrait',
        'client'      => 'Rhea Kapoor',
        'time'        => '03:00 PM - 04:30 PM',
        'status'      => 'Pending',
        'status_color'=> 'amber',
        'day'         => 18
    ),
    array(
        'id'          => 5,
        'title'       => 'Commercial Office Audit',
        'type'        => 'Audit',
        'client'      => 'Knight Frank',
        'time'        => '11:00 AM - 03:00 PM',
        'status'      => 'Scheduled',
        'status_color'=> 'zinc',
        'day'         => 21
    )
);

$current_day = 14; // Mock active focus day
?>

<?php

$calendar_header_args = array(
    'title'            => 'Workspace Calendar',
    'description'      => 'Manage corporate schedules, client photo shoot bookings, and team shifts.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'Add Event',
        'mobile_text' => 'Add Event',
        'onclick'     => "window.openAddEventDrawer()",
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'visible'     => true,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $calendar_header_args );
}
?>

<div class="space-y-6 font-sans text-zinc-900 select-none max-w-[1700px] mx-auto pb-12">
    <!-- Main Workspace Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- 3/4 Width: Interactive Calendar Grid -->
        <div class="lg:col-span-3 space-y-4">
            <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs overflow-hidden">
                <!-- Month Controls Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-150">
                    <span class="text-sm font-bold text-zinc-800 tracking-tight font-mono">AUGUST 2026</span>
                    <div class="flex items-center gap-1">
                        <button class="p-1 border border-zinc-200 hover:bg-zinc-50 rounded-lg text-zinc-600 transition-all cursor-pointer">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <button class="p-1 border border-zinc-200 hover:bg-zinc-50 rounded-lg text-zinc-600 transition-all cursor-pointer">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    </div>
                </div>

                <!-- Weekday Labels -->
                <div class="grid grid-cols-7 border-b border-zinc-100 bg-zinc-50/50 text-[10px] font-bold text-zinc-400 text-center uppercase tracking-widest py-3">
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                    <div>Sun</div>
                </div>

                <!-- Days Grid -->
                <div class="grid grid-cols-7 auto-rows-[90px] md:auto-rows-[110px] divide-x divide-y divide-zinc-100 text-xs">
                    <!-- Week 1 Empty Days -->
                    <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">27</div>
                    <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">28</div>
                    <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">29</div>
                    <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">30</div>
                    <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">31</div>
                    <!-- August Days -->
                    <?php
                    for ($d = 1; $d <= 31; $d++) {
                        $is_today = ($d === 14);
                        $bg_class = $is_today ? 'bg-zinc-950 text-white font-mono font-bold' : 'bg-white text-zinc-950 font-mono font-bold hover:bg-zinc-50/80 hover:text-zinc-950 transition-all';
                        ?>
                        <div class="p-2 relative flex flex-col justify-between min-h-[90px] md:min-h-[110px] calendar-day-cell cursor-pointer <?php echo $bg_class; ?>" data-day="<?php echo $d; ?>" onclick="window.coraSelectDay(<?php echo $d; ?>)">
                            <div class="flex items-center justify-between">
                                <span><?php echo $d; ?></span>
                            </div>
                            <div class="calendar-day-events hidden sm:block space-y-1 mt-1" id="events-day-<?php echo $d; ?>">
                                <!-- Dynamic events injected here -->
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- 1/4 Width: Sidebar Upcoming List & Integration -->
        <div class="space-y-4">
            <!-- Google Calendar Sync Card -->
            <div class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M21.35 11.1H12v2.7h5.38c-.24 1.28-.96 2.37-2.07 3.12v2.6h3.33c1.95-1.8 3.07-4.45 3.07-7.62 0-.58-.05-1.15-.15-1.8z" fill="#4285F4"/><path d="M12 20.6c2.32 0 4.27-.77 5.7-2.08l-3.33-2.6c-.92.62-2.1.98-3.37.98-2.6 0-4.8-1.76-5.59-4.12H2.03v2.68C3.52 18.42 7.48 20.6 12 20.6z" fill="#34A853"/><path d="M6.41 12.78c-.2-.6-.31-1.25-.31-1.92s.11-1.32.31-1.92V6.26H2.03C1.3 7.72.88 9.38.88 11.15s.42 3.43 1.15 4.89l3.22-2.5c-.2-.6-.31-1.25-.31-1.92z" fill="#FBBC05"/><path d="M12 5.96c1.26 0 2.4.43 3.3 1.28l2.47-2.47C16.27 3.32 14.32 2.5 12 2.5 7.48 2.5 3.52 4.68 2.03 7.64l3.22 2.5c.79-2.36 2.99-4.12 5.59-4.12z" fill="#EA4335"/></svg>
                        Google Calendar
                    </span>
                    <span id="gcal-sidebar-badge" class="text-[9px] font-extrabold px-2 py-0.5 rounded-full text-zinc-500 bg-zinc-100 border border-zinc-200/50 uppercase">Disconnected</span>
                </div>
                <p class="text-xs text-zinc-500 leading-normal">Synchronize your bookings and shifts bi-directionally with Google Calendar.</p>
                <button type="button" onclick="window.openGoogleCalendarDrawer()" class="w-full px-4 py-2 border border-zinc-200 hover:bg-zinc-50 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Configure Integration
                </button>
            </div>

            <!-- Today's Schedule Card -->
            <div class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Today's Schedule</span>
                    <span id="cora-today-schedule-title" class="text-[10px] font-extrabold font-mono text-zinc-400 bg-zinc-50 px-2 py-0.5 rounded-full">Day 14</span>
                </div>

                <div id="cora-today-schedule-list" class="space-y-3">
                    <!-- Injected dynamically by JS -->
                </div>
            </div>

            <!-- Upcoming Events Card -->
            <div class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-4">
                <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider block">Upcoming Events</span>
                <div id="cora-upcoming-events-list" class="space-y-3">
                    <!-- Injected dynamically by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     GOOGLE CALENDAR-STYLE CENTERED EVENT MODAL
     ========================================================================= -->

<div id="cora-event-modal-backdrop" class="hidden fixed inset-0 bg-black/40 z-[9999] backdrop-blur-[1px] flex items-center justify-center p-4 transition-all duration-200" onclick="window.coraCloseEventModal()">
    <div id="cora-event-modal" class="bg-white rounded-2xl border border-zinc-200 shadow-2xl w-[520px] max-w-[95vw] flex flex-col p-6 space-y-4 cursor-default transform scale-95 opacity-0 transition-all duration-200" onclick="event.stopPropagation()">
        <!-- Header: Event Type Tabs -->
        <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
            <div class="flex items-center gap-1.5 select-none" id="modal-event-type-tabs">
                <button type="button" class="px-3 py-1 bg-zinc-950 text-white text-xs font-bold rounded-lg transition-colors cursor-pointer" onclick="window.coraSetEventType(this, 'Shoot')">Shoot</button>
                <button type="button" class="px-3 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-lg transition-colors cursor-pointer" onclick="window.coraSetEventType(this, 'Showing')">Showing</button>
                <button type="button" class="px-3 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-lg transition-colors cursor-pointer" onclick="window.coraSetEventType(this, 'Meeting')">Meeting</button>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1 rounded-full hover:bg-zinc-50 transition-colors" onclick="window.coraCloseEventModal()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Form content -->
        <form id="modal-event-modal-form" onsubmit="window.coraSubmitEventModal(event)" class="space-y-4">
            <!-- Google style borderless large title -->
            <div>
                <input type="text" id="modal-event-title" required placeholder="Add title" class="w-full text-lg font-bold border-b border-zinc-200 pb-1.5 focus:outline-none focus:border-zinc-950 text-zinc-950 bg-transparent placeholder-zinc-300">
            </div>

            <!-- Time & Day -->
            <div class="flex items-center gap-3 text-xs text-zinc-800 flex-wrap">
                <div class="flex items-center gap-1 bg-zinc-50 border border-zinc-200 px-3 py-1.5 rounded-xl">
                    <span class="text-zinc-450 font-bold">Aug</span>
                    <input type="number" id="modal-event-day" min="1" max="31" required class="w-12 bg-transparent text-center font-bold focus:outline-none text-zinc-950">
                </div>
                <div class="flex items-center gap-1.5 bg-zinc-50 border border-zinc-200 px-3 py-1.5 rounded-xl flex-1">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-450"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <input type="text" id="modal-event-time" required placeholder="10:00 AM - 02:00 PM" class="w-full bg-transparent focus:outline-none font-medium text-zinc-950">
                </div>
            </div>

            <!-- Google Calendar Sync inline Option -->
            <div class="p-3 bg-zinc-50 rounded-xl border border-zinc-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" class="text-zinc-850 shrink-0"><path d="M21.35 11.1H12v2.7h5.38c-.24 1.28-.96 2.37-2.07 3.12v2.6h3.33c1.95-1.8 3.07-4.45 3.07-7.62 0-.58-.05-1.15-.15-1.8z" fill="#4285F4"/><path d="M12 20.6c2.32 0 4.27-.77 5.7-2.08l-3.33-2.6c-.92.62-2.1.98-3.37.98-2.6 0-4.8-1.76-5.59-4.12H2.03v2.68C3.52 18.42 7.48 20.6 12 20.6z" fill="#34A853"/><path d="M6.41 12.78c-.2-.6-.31-1.25-.31-1.92s.11-1.32.31-1.92V6.26H2.03C1.3 7.72.88 9.38.88 11.15s.42 3.43 1.15 4.89l3.22-2.5c-.2-.6-.31-1.25-.31-1.92z" fill="#FBBC05"/><path d="M12 5.96c1.26 0 2.4.43 3.3 1.28l2.47-2.47C16.27 3.32 14.32 2.5 12 2.5 7.48 2.5 3.52 4.68 2.03 7.64l3.22 2.5c.79-2.36 2.99-4.12 5.59-4.12z" fill="#EA4335"/></svg>
                    <div class="space-y-0.5">
                        <span class="font-bold text-zinc-950 flex items-center gap-1.5">
                            Google Calendar Sync
                            <span id="modal-gcal-badge" class="px-1.5 py-0.2 bg-zinc-200 text-[8px] rounded-full text-zinc-600 uppercase font-mono tracking-tight">Disconnected</span>
                        </span>
                        <span id="modal-gcal-email" class="text-[10px] text-zinc-450 block">Authorize bidirectional calendar synchronization</span>
                    </div>
                </div>
                <!-- Dynamic connection trigger link -->
                <button type="button" id="modal-gcal-conn-btn" onclick="window.coraStartGcalAuthPopupFlow(event)" class="text-zinc-950 font-bold hover:underline cursor-pointer bg-transparent border-none p-0 focus:outline-none">
                    Connect
                </button>
            </div>

            <!-- Dynamic guest invite selection (Google-style) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-zinc-800">Add Guests / Invite Matrix</label>
                <div class="relative">
                    <div class="flex items-center border border-zinc-200 focus-within:border-zinc-950 rounded-xl px-3 py-1.5 bg-white">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 mr-2 shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <input type="text" id="modal-event-guests-input" placeholder="Type email or name to invite..." class="w-full bg-transparent focus:outline-none text-xs text-zinc-950" oninput="window.coraFilterGuests(this.value)" autocomplete="off">
                    </div>
                    <!-- Guests Dropdown suggestions -->
                    <div id="modal-guests-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white border border-zinc-200 rounded-xl shadow-lg max-h-48 overflow-y-auto p-1 font-sans space-y-0.5">
                        <!-- Dynamic list options -->
                    </div>
                </div>
                <!-- Selected Guest Pill Badges -->
                <div id="modal-selected-guests" class="flex flex-wrap gap-1.5 pt-1">
                    <!-- Dynamic pill badges go here -->
                </div>
            </div>

            <!-- Additional details: Client & Status -->
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Primary Client *</label>
                    <input type="text" id="modal-event-client" required placeholder="e.g. Vipul Malhotra" class="w-full px-3 py-2 border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Event Status *</label>
                    <select id="modal-event-status" required class="w-full px-3 py-2 border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                        <option value="Confirmed" data-color="emerald">Confirmed (Emerald)</option>
                        <option value="Scheduled" data-color="zinc">Scheduled (Zinc)</option>
                        <option value="In Progress" data-color="blue">In Progress (Blue)</option>
                        <option value="Pending" data-color="amber">Pending (Amber)</option>
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-2">
                <button type="button" onclick="window.coraCloseEventModal()" class="px-4 py-2 border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-50 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                    Save Event
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT WORKSPACE INTERACTIVE LOGIC
     ========================================================================= -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    window.coraCalendarEvents = <?php echo json_encode($calendar_events); ?>;
    window.coraActiveDay = 14;
    window.coraGoogleCalendarSynced = false;
    window.coraActiveEventType = "Shoot";

    // Guest suggestions database list
    window.coraAvailableGuests = [
        { name: "Vipul Malhotra", email: "vipul.m@apex.local" },
        { name: "Aria Group Singapore", email: "singapore.leads@aria.local" },
        { name: "Apex Capital", email: "deals@apex.local" },
        { name: "Rhea Kapoor", email: "rhea.kapoor@vogue.local" },
        { name: "Knight Frank", email: "inspections@knightfrank.local" },
        { name: "Karan Johar", email: "karan@dharma.local" },
        { name: "Rajesh Sharma", email: "rajesh@cora.local" },
        { name: "Anil Kumar", email: "anil.k@cora.local" },
        { name: "Neha Malhotra", email: "neha.m@cora.local" }
    ];
    window.coraSelectedGuests = [];

    // 1. Render Calendar Days state
    window.coraRenderCalendar = function() {
        // Clear all days
        document.querySelectorAll('.calendar-day-events').forEach(el => el.innerHTML = '');

        const todayList = document.getElementById('cora-today-schedule-list');
        const upcomingList = document.getElementById('cora-upcoming-events-list');

        if (todayList) todayList.innerHTML = '';
        if (upcomingList) upcomingList.innerHTML = '';

        const eventsByDay = {};
        const eventsForToday = [];
        const eventsForUpcoming = [];

        window.coraCalendarEvents.forEach(event => {
            const day = parseInt(event.day);
            if (!eventsByDay[day]) eventsByDay[day] = [];
            eventsByDay[day].push(event);

            if (day === window.coraActiveDay) {
                eventsForToday.push(event);
            } else {
                eventsForUpcoming.push(event);
            }
        });

        // Sort upcoming list by day ascending
        eventsForUpcoming.sort((a, b) => parseInt(a.day) - parseInt(b.day));

        // Inject day cell grid badges
        Object.keys(eventsByDay).forEach(dayStr => {
            const day = parseInt(dayStr);
            const container = document.getElementById('events-day-' + day);
            if (!container) return;

            const dayEvents = eventsByDay[day];
            const typedGroups = {};
            dayEvents.forEach(e => {
                if (!typedGroups[e.type]) {
                    typedGroups[e.type] = {
                        count: 0,
                        color: e.status_color || 'zinc'
                    };
                }
                typedGroups[e.type].count++;
            });

            Object.keys(typedGroups).forEach(type => {
                const group = typedGroups[type];
                let colorClass = 'bg-zinc-800';
                if (group.color === 'emerald') colorClass = 'bg-emerald-600';
                else if (group.color === 'blue') colorClass = 'bg-blue-600';
                else if (group.color === 'amber') colorClass = 'bg-amber-500';

                const badge = document.createElement('div');
                badge.className = 'px-1.5 py-0.5 ' + colorClass + ' text-[8px] font-sans font-bold text-white rounded-md truncate mb-0.5';
                badge.innerText = type + ' (' + group.count + ')';
                container.appendChild(badge);
            });
        });

        // Set sidebar schedule title
        const todayTitle = document.getElementById('cora-today-schedule-title');
        if (todayTitle) {
            todayTitle.innerText = 'Day ' + window.coraActiveDay;
        }

        // Render sidebar lists
        if (todayList) {
            if (eventsForToday.length === 0) {
                todayList.innerHTML = '<div class="p-6 text-center text-zinc-400 text-xs border border-dashed border-zinc-200 rounded-xl">No events scheduled.</div>';
            } else {
                eventsForToday.forEach(event => {
                    let stColor = 'bg-zinc-100 text-zinc-800';
                    if (event.status_color === 'emerald') stColor = 'bg-emerald-100 text-emerald-800';
                    else if (event.status_color === 'blue') stColor = 'bg-blue-100 text-blue-800';
                    else if (event.status_color === 'amber') stColor = 'bg-amber-100 text-amber-800';

                    const card = document.createElement('div');
                    card.className = 'p-3.5 bg-zinc-50/50 hover:bg-zinc-50 rounded-xl border border-zinc-150 transition-all space-y-2';
                    card.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 bg-white border border-zinc-200 rounded-md text-[9px] font-bold text-zinc-500 uppercase">${event.type}</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold ${stColor}">${event.status}</span>
                        </div>
                        <h4 class="text-xs font-bold text-zinc-950">${event.title}</h4>
                        <p class="text-[11px] text-zinc-500">Client: <strong>${event.client}</strong></p>
                        <p class="text-[10px] font-mono text-zinc-400 flex items-center gap-1">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            ${event.time}
                        </p>
                    `;
                    todayList.appendChild(card);
                });
            }
        }

        if (upcomingList) {
            if (eventsForUpcoming.length === 0) {
                upcomingList.innerHTML = '<div class="p-6 text-center text-zinc-400 text-xs border border-dashed border-zinc-200 rounded-xl">No upcoming events.</div>';
            } else {
                eventsForUpcoming.forEach(event => {
                    let stColor = 'bg-zinc-100 text-zinc-800';
                    if (event.status_color === 'emerald') stColor = 'bg-emerald-100 text-emerald-800';
                    else if (event.status_color === 'blue') stColor = 'bg-blue-100 text-blue-800';
                    else if (event.status_color === 'amber') stColor = 'bg-amber-100 text-amber-800';

                    const card = document.createElement('div');
                    card.className = 'p-3 bg-zinc-50/50 hover:bg-zinc-50 rounded-xl border border-zinc-150 transition-all flex items-center justify-between gap-2 cursor-pointer';
                    card.onclick = function() { window.coraSelectDay(event.day); };
                    card.innerHTML = `
                        <div>
                            <h4 class="text-xs font-bold text-zinc-900">${event.title}</h4>
                            <p class="text-[10px] text-zinc-500">Aug ${event.day} · ${event.client}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold ${stColor}">${event.status}</span>
                    `;
                    upcomingList.appendChild(card);
                });
            }
        }
    };

    // 2. Select Calendar Day handler
    window.coraSelectDay = function(day) {
        window.coraActiveDay = day;
        document.querySelectorAll('.calendar-day-cell').forEach(cell => {
            const cellDay = parseInt(cell.getAttribute('data-day'));
            if (cellDay === day) {
                cell.className = 'p-2 relative flex flex-col justify-between min-h-[90px] md:min-h-[110px] calendar-day-cell cursor-pointer bg-zinc-950 text-white font-mono font-bold';
            } else {
                cell.className = 'p-2 relative flex flex-col justify-between min-h-[90px] md:min-h-[110px] calendar-day-cell cursor-pointer bg-white text-zinc-950 font-mono font-bold hover:bg-zinc-50/80 hover:text-zinc-950 transition-all';
            }
        });
        window.coraRenderCalendar();
        
        // Google Calendar Style: clicking a day opens the create event modal automatically
        window.coraOpenEventModal(day);
    };

    // 3. Open Event Modal
    window.openAddEventDrawer = function() {
        // Maps Add Event header button to custom Centered Modal
        window.coraOpenEventModal();
    };

    window.coraOpenEventModal = function(day) {
        const backdrop = document.getElementById('cora-event-modal-backdrop');
        const modal = document.getElementById('cora-event-modal');
        if (!backdrop || !modal) return;

        window.coraSelectedGuests = [];
        window.coraRenderSelectedGuests();

        const targetDay = day || window.coraActiveDay || 14;
        document.getElementById('modal-event-day').value = targetDay;
        document.getElementById('modal-event-title').value = '';
        document.getElementById('modal-event-client').value = '';
        document.getElementById('modal-event-time').value = '10:00 AM - 01:00 PM';
        document.getElementById('modal-event-modal-form').reset();
        document.getElementById('modal-event-day').value = targetDay;

        // Reset tabs selection
        window.coraSetEventType(document.querySelector('#modal-event-type-tabs button'), 'Shoot');

        // Update modal's Google Calendar display state
        window.coraUpdateModalGcalState();

        backdrop.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('scale-95', 'opacity-0');
            modal.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        jQuery('body').addClass('overflow-hidden');
    };

    window.coraCloseEventModal = function() {
        const backdrop = document.getElementById('cora-event-modal-backdrop');
        const modal = document.getElementById('cora-event-modal');
        if (!backdrop || !modal) return;

        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            backdrop.classList.add('hidden');
            jQuery('body').removeClass('overflow-hidden');
        }, 200);
    };

    // Tab Type switcher
    window.coraSetEventType = function(btn, type) {
        window.coraActiveEventType = type;
        const tabs = document.querySelectorAll('#modal-event-type-tabs button');
        tabs.forEach(t => {
            t.className = 'px-3 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-lg transition-colors cursor-pointer';
        });
        btn.className = 'px-3 py-1 bg-zinc-950 text-white text-xs font-bold rounded-lg transition-colors cursor-pointer';
    };

    // Guest Invite Flow logic
    window.coraFilterGuests = function(query) {
        const dropdown = document.getElementById('modal-guests-dropdown');
        if (!dropdown) return;

        if (!query || query.trim() === '') {
            dropdown.classList.add('hidden');
            return;
        }

        const filtered = window.coraAvailableGuests.filter(g => {
            const matches = g.name.toLowerCase().includes(query.toLowerCase()) || g.email.toLowerCase().includes(query.toLowerCase());
            const alreadySelected = window.coraSelectedGuests.some(selected => selected.email === g.email);
            return matches && !alreadySelected;
        });

        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="px-3 py-2 text-xs text-zinc-400">No matching guests found</div>';
        } else {
            dropdown.innerHTML = '';
            filtered.forEach(g => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-100 rounded-lg flex items-center justify-between cursor-pointer border-none bg-transparent';
                btn.innerHTML = `
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-zinc-200 text-zinc-800 text-[10px] font-bold flex items-center justify-center">${g.name.charAt(0)}</div>
                        <div>
                            <span class="font-bold block text-zinc-950">${g.name}</span>
                            <span class="text-[10px] text-zinc-450 block">${g.email}</span>
                        </div>
                    </div>
                `;
                btn.onclick = function() {
                    window.coraAddGuest(g.name, g.email);
                };
                dropdown.appendChild(btn);
            });
        }
        dropdown.classList.remove('hidden');
    };

    window.coraAddGuest = function(name, email) {
        window.coraSelectedGuests.push({ name, email });
        window.coraRenderSelectedGuests();
        document.getElementById('modal-event-guests-input').value = '';
        document.getElementById('modal-guests-dropdown').classList.add('hidden');
    };

    window.coraRemoveGuest = function(email) {
        window.coraSelectedGuests = window.coraSelectedGuests.filter(g => g.email !== email);
        window.coraRenderSelectedGuests();
    };

    window.coraRenderSelectedGuests = function() {
        const container = document.getElementById('modal-selected-guests');
        if (!container) return;
        container.innerHTML = '';

        window.coraSelectedGuests.forEach(g => {
            const pill = document.createElement('div');
            pill.className = 'inline-flex items-center gap-1.5 px-2 py-1 bg-zinc-100 border border-zinc-200 text-zinc-800 text-[10px] font-bold rounded-lg select-none';
            pill.innerHTML = `
                <div class="w-4 h-4 rounded-full bg-zinc-300 text-zinc-900 text-[8px] flex items-center justify-center shrink-0 font-black">${g.name.charAt(0)}</div>
                <span>${g.name}</span>
                <button type="button" class="text-zinc-450 hover:text-zinc-900 cursor-pointer p-0 border-none bg-transparent flex items-center" onclick="window.coraRemoveGuest('${g.email}')">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            `;
            container.appendChild(pill);
        });
    };

    // Google Calendar dynamic authentication flow directly in popup
    window.coraStartGcalAuthPopupFlow = function(e) {
        e.preventDefault();
        const connBtn = document.getElementById('modal-gcal-conn-btn');
        if (!connBtn) return;

        if (window.coraGoogleCalendarSynced) {
            // Disconnect Flow
            window.coraDisconnectGoogleAccount();
            return;
        }

        connBtn.innerText = 'Connecting...';
        connBtn.setAttribute('disabled', 'true');

        // Simulate a smooth loading auth connection
        setTimeout(function() {
            window.coraGoogleCalendarSynced = true;
            window.coraUpdateModalGcalState();

            // Synced badge in sidebar
            const badge = document.getElementById('gcal-sidebar-badge');
            if (badge) {
                badge.className = 'text-[9px] font-extrabold px-2 py-0.5 rounded-full text-emerald-700 bg-emerald-50 border border-emerald-200/50 uppercase flex items-center gap-1';
                badge.innerHTML = '<span class="w-1 h-1 rounded-full bg-emerald-500"></span> Synced';
            }

            // Sync mock events bidirectionally
            const googleEvents = [
                {
                    id: 'gcal-1',
                    title: 'Google Sync: Strategy Board Sync',
                    type: 'Meeting',
                    client: 'Cora Board Directors',
                    time: '11:00 AM - 12:30 PM',
                    status: 'Confirmed',
                    status_color: 'blue',
                    day: 14,
                    is_google_event: true
                },
                {
                    id: 'gcal-2',
                    title: 'Google Sync: Team Sync & Retrospective',
                    type: 'Meeting',
                    client: 'Cora Studio Staff',
                    time: '04:00 PM - 05:00 PM',
                    status: 'Scheduled',
                    status_color: 'zinc',
                    day: 15,
                    is_google_event: true
                }
            ];

            window.coraCalendarEvents = window.coraCalendarEvents.filter(e => !e.is_google_event);
            window.coraCalendarEvents.push(...googleEvents);
            window.coraRenderCalendar();

            if (window.coraShowToast) {
                window.coraShowToast("Google Calendar integrated & bi-directionally synced!", "success");
            }
        }, 900);
    };

    window.coraDisconnectGoogleAccount = function() {
        window.coraGoogleCalendarSynced = false;
        window.coraUpdateModalGcalState();

        // Reset badge in sidebar
        const badge = document.getElementById('gcal-sidebar-badge');
        if (badge) {
            badge.className = 'text-[9px] font-extrabold px-2 py-0.5 rounded-full text-zinc-500 bg-zinc-100 border border-zinc-200/50 uppercase';
            badge.innerText = 'Disconnected';
        }

        // Filter out synced Google events
        window.coraCalendarEvents = window.coraCalendarEvents.filter(e => !e.is_google_event);
        window.coraRenderCalendar();

        if (window.coraShowToast) {
            window.coraShowToast("Google Calendar connection removed.", "info");
        }
    };

    window.coraUpdateModalGcalState = function() {
        const badge = document.getElementById('modal-gcal-badge');
        const email = document.getElementById('modal-gcal-email');
        const connBtn = document.getElementById('modal-gcal-conn-btn');

        if (!badge || !email || !connBtn) return;

        connBtn.removeAttribute('disabled');

        if (window.coraGoogleCalendarSynced) {
            badge.className = 'px-1.5 py-0.2 bg-emerald-50 text-[8px] rounded-full text-emerald-700 border border-emerald-200/50 uppercase font-mono tracking-tight flex items-center gap-0.5';
            badge.innerHTML = '<span class="w-1 h-1 rounded-full bg-emerald-500"></span> Synced';
            email.innerText = 'Synced with shrutian@cora.local';
            connBtn.innerText = 'Disconnect';
            connBtn.className = 'text-zinc-500 hover:text-zinc-950 font-bold hover:underline cursor-pointer bg-transparent border-none p-0 focus:outline-none';
        } else {
            badge.className = 'px-1.5 py-0.2 bg-zinc-200 text-[8px] rounded-full text-zinc-600 uppercase font-mono tracking-tight';
            badge.innerText = 'Disconnected';
            email.innerText = 'Authorize bidirectional calendar synchronization';
            connBtn.innerText = 'Connect';
            connBtn.className = 'text-zinc-950 font-bold hover:underline cursor-pointer bg-transparent border-none p-0 focus:outline-none';
        }
    };

    // Handle modal form submission
    window.coraSubmitEventModal = function(event) {
        event.preventDefault();
        const title = document.getElementById('modal-event-title').value;
        const day = parseInt(document.getElementById('modal-event-day').value);
        const client = document.getElementById('modal-event-client').value;
        const time = document.getElementById('modal-event-time').value;
        const statusSelect = document.getElementById('modal-event-status');
        const status = statusSelect.value;
        const color = statusSelect.options[statusSelect.selectedIndex].getAttribute('data-color') || 'zinc';

        const newEvent = {
            id: Date.now(),
            title: title,
            type: window.coraActiveEventType,
            client: client,
            time: time,
            status: status,
            status_color: color,
            day: day
        };

        window.coraCalendarEvents.push(newEvent);
        window.coraRenderCalendar();
        window.coraCloseEventModal();

        if (window.coraShowToast) {
            window.coraShowToast("Event successfully created.", "success");
        }
    };

    // Close modal on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            window.coraCloseEventModal();
        }
    });

    // Close guests dropdown on outside click
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('modal-guests-dropdown');
        const input = document.getElementById('modal-event-guests-input');
        if (dropdown && !dropdown.contains(e.target) && e.target !== input) {
            dropdown.classList.add('hidden');
        }
    });

    // Render calendar initially
    window.coraRenderCalendar();
});
</script>

