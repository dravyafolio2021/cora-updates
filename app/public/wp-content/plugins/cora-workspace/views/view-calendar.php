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
        'onclick'     => "window.coraOpenEventModal(null, false)",
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'visible'     => true,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $calendar_header_args );
}
?>

<!-- Custom CSS to support current time indicator & layout transitions -->
<style>
.cora-time-indicator {
    position: absolute;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #ef4444;
    z-index: 10;
}
.cora-time-indicator::before {
    content: '';
    position: absolute;
    left: -4px;
    top: -3px;
    width: 8px;
    height: 8px;
    border-radius: 9999px;
    background-color: #ef4444;
}
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

<div class="space-y-6 font-sans text-zinc-900 select-none max-w-[1700px] mx-auto pb-12">
    <!-- View Switcher & Period Controls Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-zinc-200 rounded-2xl p-4 shadow-2xs">
        <div class="flex items-center gap-1.5 bg-zinc-100 p-0.5 rounded-xl">
            <button id="view-btn-day" onclick="window.coraSwitchViewMode('day')" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-colors cursor-pointer text-zinc-500 hover:text-zinc-900 bg-transparent border-none">Day</button>
            <button id="view-btn-week" onclick="window.coraSwitchViewMode('week')" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-colors cursor-pointer text-zinc-500 hover:text-zinc-900 bg-transparent border-none">Week</button>
            <button id="view-btn-month" onclick="window.coraSwitchViewMode('month')" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-colors cursor-pointer text-zinc-950 bg-white shadow-2xs border-none">Month</button>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.coraGoToToday()" class="px-3.5 py-1.5 border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-800 text-xs font-bold rounded-xl transition-all cursor-pointer">Today</button>
            <div class="flex items-center gap-0.5 border border-zinc-200 bg-white rounded-xl p-0.5">
                <button onclick="window.coraPrevPeriod()" class="p-1.5 hover:bg-zinc-50 rounded-lg text-zinc-600 transition-all cursor-pointer border-none bg-transparent flex"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="15 18 9 12 15 6"/></svg></button>
                <button onclick="window.coraNextPeriod()" class="p-1.5 hover:bg-zinc-50 rounded-lg text-zinc-600 transition-all cursor-pointer border-none bg-transparent flex"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="9 18 15 12 9 6"/></svg></button>
            </div>
            <h2 id="calendar-period-title" class="text-sm font-bold text-zinc-950 font-mono tracking-tight ml-2 uppercase">August 2026</h2>
        </div>

        <div class="flex items-center gap-2 ml-auto sm:ml-0">
            <button id="calendar-filter-btn" onclick="if(window.coraShowToast) window.coraShowToast('Calendar filters opened', 'info')" class="px-3.5 py-1.5 border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-800 text-xs font-bold rounded-xl transition-all cursor-pointer flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filters
            </button>
        </div>
    </div>

    <!-- Main Workspace Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- 3/4 Width: Interactive Calendar Grid Views -->
        <div class="lg:col-span-3 space-y-4">
            
            <!-- A. MONTH VIEW CONTAINER -->
            <div id="cora-calendar-month-view" class="space-y-4">
                <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs overflow-hidden">
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
                        <!-- Trailing Days -->
                        <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">27</div>
                        <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">28</div>
                        <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">29</div>
                        <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">30</div>
                        <div class="bg-zinc-50/40 p-2 text-zinc-400 font-mono">31</div>
                        
                        <!-- Dynamic Month Days -->
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
                                    <!-- Badges injected dynamically by JS -->
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <!-- Color Legends & Export Options -->
                <div class="flex items-center justify-between flex-wrap gap-3 px-2">
                    <div class="flex items-center gap-4 text-[10px] font-bold text-zinc-400 tracking-wider">
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Shoot</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Showing</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Meeting</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Content</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Audit</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span> Other</span>
                    </div>
                    <button onclick="if(window.coraShowToast) window.coraShowToast('Exporting schedule...', 'success')" class="text-xs font-bold text-zinc-950 hover:underline cursor-pointer flex items-center gap-1.5 bg-transparent border-none p-0 focus:outline-none">
                        Export
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </button>
                </div>

                <!-- Month KPI Stats row -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
                    <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 flex items-center gap-4 shadow-2xs">
                        <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </span>
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Events This Month</span>
                            <span class="text-base font-bold text-zinc-950 font-mono" id="stat-total-events">14</span>
                        </div>
                    </div>
                    <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 flex items-center gap-4 shadow-2xs">
                        <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Total Scheduled</span>
                            <span class="text-base font-bold text-zinc-950 font-mono">28h 30m</span>
                        </div>
                    </div>
                    <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 flex items-center gap-4 shadow-2xs">
                        <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Client Meetings</span>
                            <span class="text-base font-bold text-zinc-950 font-mono">8</span>
                        </div>
                    </div>
                    <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 flex items-center gap-4 shadow-2xs">
                        <span class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </span>
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Schedule Efficiency</span>
                            <span class="text-base font-bold text-zinc-950 font-mono">92%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- B. WEEKLY VIEW CONTAINER (Hidden initially) -->
            <div id="cora-calendar-week-view" class="hidden space-y-4">
                <div class="border border-zinc-200 rounded-2xl bg-white shadow-2xs overflow-hidden flex flex-col">
                    <!-- Column Headers (Mon-Sun) -->
                    <div class="grid grid-cols-8 border-b border-zinc-100 bg-zinc-50/50 text-center py-2.5">
                        <div class="text-[10px] font-bold text-zinc-400 flex items-center justify-center uppercase tracking-widest">GMT+5:30</div>
                        <div class="text-[11px] font-bold text-zinc-950">Mon <span class="text-zinc-450 block text-[10px] font-mono mt-0.5">10 Aug</span></div>
                        <div class="text-[11px] font-bold text-zinc-950">Tue <span class="text-zinc-450 block text-[10px] font-mono mt-0.5">11 Aug</span></div>
                        <div class="text-[11px] font-bold text-zinc-950">Wed <span class="text-zinc-450 block text-[10px] font-mono mt-0.5">12 Aug</span></div>
                        <div class="text-[11px] font-bold text-zinc-950">Thu <span class="text-zinc-450 block text-[10px] font-mono mt-0.5">13 Aug</span></div>
                        <div class="text-[11px] font-bold bg-zinc-950 text-white rounded-lg py-1 px-1.5 select-none scale-105 shadow-md">Fri <span class="text-zinc-300 block text-[10px] font-mono mt-0.5">14 Aug</span></div>
                        <div class="text-[11px] font-bold text-zinc-950">Sat <span class="text-zinc-450 block text-[10px] font-mono mt-0.5">15 Aug</span></div>
                        <div class="text-[11px] font-bold text-zinc-950">Sun <span class="text-zinc-450 block text-[10px] font-mono mt-0.5">16 Aug</span></div>
                    </div>

                    <!-- Hourly Time Columns Grid -->
                    <div class="grid grid-cols-8 divide-x divide-zinc-100 relative h-[550px] overflow-y-auto no-scrollbar">
                        <!-- Current Time Indicator line at 10:15 AM (approx 135px from top) -->
                        <div class="cora-time-indicator" style="top: 135px;"></div>

                        <!-- Time block labels (Column 1) -->
                        <div class="divide-y divide-zinc-100 bg-zinc-50/20 text-right pr-3 text-[10px] font-bold text-zinc-400 select-none">
                            <?php for ($hour = 8; $hour <= 19; $hour++): 
                                $h_label = ($hour > 12) ? ($hour - 12) . ' PM' : (($hour === 12) ? '12 PM' : $hour . ' AM');
                            ?>
                                <div class="h-[50px] flex items-center justify-end"><?php echo $h_label; ?></div>
                            <?php endfor; ?>
                        </div>

                        <!-- Mon-Sun Columns (Columns 2-8) -->
                        <?php for ($col = 1; $col <= 7; $col++): 
                            $col_day = 9 + $col; // Aug 10 to Aug 16
                        ?>
                            <div class="relative divide-y divide-zinc-100 min-h-full" id="week-column-day-<?php echo $col_day; ?>">
                                <!-- Hour slot divisions -->
                                <?php for ($h = 8; $h <= 19; $h++): ?>
                                    <div class="h-[50px] hover:bg-zinc-50/30 transition-all cursor-pointer" onclick="window.coraOpenEventModal(<?php echo $col_day; ?>, false)"></div>
                                <?php endfor; ?>

                                <!-- Absolute positioned event cards go here dynamically -->
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Show weekends Switcher -->
                <div class="flex justify-end items-center gap-2 px-2 select-none">
                    <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest">Show weekends</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" checked class="sr-only peer" onchange="if(window.coraShowToast) window.coraShowToast('Weekend view toggled', 'info')">
                        <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:height-4 after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950"></div>
                    </label>
                </div>
            </div>

            <!-- C. DAILY VIEW CONTAINER (Hidden initially) -->
            <div id="cora-calendar-day-view" class="hidden space-y-5">
                <!-- Day Top Statistics Header -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <div class="bg-zinc-50 border border-zinc-200/80 rounded-2xl p-4 shadow-3xs text-center">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Events</span>
                        <span class="text-base font-bold text-zinc-950 font-mono mt-1 block" id="day-stat-count">8</span>
                    </div>
                    <div class="bg-zinc-50 border border-zinc-200/80 rounded-2xl p-4 shadow-3xs text-center">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Scheduled</span>
                        <span class="text-base font-bold text-zinc-950 font-mono mt-1 block">6h 30m</span>
                    </div>
                    <div class="bg-zinc-50 border border-zinc-200/80 rounded-2xl p-4 shadow-3xs text-center">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Meetings</span>
                        <span class="text-base font-bold text-zinc-950 font-mono mt-1 block">3</span>
                    </div>
                    <div class="bg-zinc-50 border border-zinc-200/80 rounded-2xl p-4 shadow-3xs text-center">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Day Efficiency</span>
                        <span class="text-base font-bold text-zinc-950 font-mono mt-1 block">92%</span>
                    </div>
                    <div class="bg-emerald-50/50 border border-emerald-100 rounded-2xl p-4 shadow-3xs flex items-center justify-between text-left col-span-2 sm:col-span-1">
                        <div>
                            <span class="text-[9px] font-bold text-emerald-800 uppercase tracking-wider block">Next Event</span>
                            <span class="text-[11px] font-bold text-zinc-900 block truncate mt-1">Luxury Villa Showing</span>
                            <span class="text-[10px] font-mono text-zinc-400 block mt-0.5">12:30 PM - 01:30 PM</span>
                        </div>
                    </div>
                </div>

                <!-- Daily Time Slots List view -->
                <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-2xs flex flex-col">
                    <div class="relative space-y-0.5 max-h-[500px] overflow-y-auto no-scrollbar">
                        <!-- Current Time Indicator line at 10:15 AM -->
                        <div class="cora-time-indicator" style="top: 135px;"></div>

                        <!-- Rows scale 8 AM to 7 PM -->
                        <?php for ($hour = 8; $hour <= 19; $hour++): 
                            $h_label = ($hour > 12) ? ($hour - 12) . ' PM' : (($hour === 12) ? '12 PM' : $hour . ' AM');
                        ?>
                            <div class="flex items-start gap-4 border-b border-zinc-100/50 py-3.5" id="day-row-hour-<?php echo $hour; ?>">
                                <div class="w-16 text-right text-[10px] font-bold text-zinc-400 font-mono select-none pt-0.5"><?php echo $h_label; ?></div>
                                <div class="flex-1 min-h-[36px] relative" id="day-events-hour-<?php echo $hour; ?>">
                                    <!-- Detailed cards go here -->
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- 1/4 Width: Sidebar Upcoming List & Integrations -->
        <div class="space-y-4">
            
            <!-- Sidebar Panel: Day/Week/Month Switchable Cards -->

            <!-- Card A: Sync & Integrations -->
            <div id="cora-sidebar-sync-card" class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-3 select-none">
                <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider block">Sync & Integrations</span>
                <div class="space-y-2.5">
                    <!-- Google Calendar Connection widget -->
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="flex items-center gap-1.5 font-bold text-zinc-800">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M21.35 11.1H12v2.7h5.38c-.24 1.28-.96 2.37-2.07 3.12v2.6h3.33c1.95-1.8 3.07-4.45 3.07-7.62 0-.58-.05-1.15-.15-1.8z" fill="#4285F4"/><path d="M12 20.6c2.32 0 4.27-.77 5.7-2.08l-3.33-2.6c-.92.62-2.1.98-3.37.98-2.6 0-4.8-1.76-5.59-4.12H2.03v2.68C3.52 18.42 7.48 20.6 12 20.6z" fill="#34A853"/><path d="M6.41 12.78c-.2-.6-.31-1.25-.31-1.92s.11-1.32.31-1.92V6.26H2.03C1.3 7.72.88 9.38.88 11.15s.42 3.43 1.15 4.89l3.22-2.5c-.2-.6-.31-1.25-.31-1.92z" fill="#FBBC05"/><path d="M12 5.96c1.26 0 2.4.43 3.3 1.28l2.47-2.47C16.27 3.32 14.32 2.5 12 2.5 7.48 2.5 3.52 4.68 2.03 7.64l3.22 2.5c.79-2.36 2.99-4.12 5.59-4.12z" fill="#EA4335"/></svg>
                            Google Calendar
                        </span>
                        <button type="button" onclick="window.coraOpenEventModal(null, false)" id="gcal-conn-badge" class="px-2 py-0.5 bg-zinc-100 border border-zinc-200 text-zinc-500 rounded-md text-[9px] font-bold uppercase cursor-pointer hover:bg-zinc-200/50 transition-all">Connect</button>
                    </div>
                    <!-- Outlook Calendar Connection widget -->
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="flex items-center gap-1.5 font-bold text-zinc-800">
                            <!-- Outlook Logo Icon -->
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-6h2v6zm0-8h-2V6h2v2z" fill="#0078d4"/></svg>
                            Outlook Calendar
                        </span>
                        <button type="button" onclick="if(window.coraShowToast) window.coraShowToast('Outlook integration coming soon!', 'info')" class="px-2.5 py-1 text-zinc-850 hover:underline border-none bg-transparent font-bold text-xs cursor-pointer">Connect</button>
                    </div>
                </div>
            </div>

            <!-- Card B: Quick Actions (Shown on Day view) -->
            <div id="cora-sidebar-actions-card" class="hidden bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-4">
                <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider block">Quick Actions</span>
                <div class="grid grid-cols-2 gap-2 text-center text-[10px] font-bold">
                    <button onclick="window.coraOpenEventModal(null, false)" class="p-3 bg-zinc-50/50 hover:bg-zinc-100/50 rounded-xl border border-zinc-200 flex flex-col items-center justify-center gap-1.5 cursor-pointer text-zinc-800">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add Event
                    </button>
                    <button onclick="if(window.coraShowToast) window.coraShowToast('Book Time block triggered', 'info')" class="p-3 bg-zinc-50/50 hover:bg-zinc-100/50 rounded-xl border border-zinc-200 flex flex-col items-center justify-center gap-1.5 cursor-pointer text-zinc-800">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Book Time
                    </button>
                    <button onclick="if(window.coraShowToast) window.coraShowToast('Share Availability modal coming soon!', 'info')" class="p-3 bg-zinc-50/50 hover:bg-zinc-100/50 rounded-xl border border-zinc-200 flex flex-col items-center justify-center gap-1.5 cursor-pointer text-zinc-800">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        Share Avail.
                    </button>
                    <button onclick="if(window.coraShowToast) window.coraShowToast('Focus Time block set.', 'success')" class="p-3 bg-zinc-50/50 hover:bg-zinc-100/50 rounded-xl border border-zinc-200 flex flex-col items-center justify-center gap-1.5 cursor-pointer text-zinc-800">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                        Focus Time
                    </button>
                </div>
            </div>

            <!-- Card C: Notes for Today (Checklist widget) -->
            <div id="cora-sidebar-notes-card" class="hidden bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Notes for Today</span>
                    <button onclick="window.coraAddNotesChecklistItem()" class="text-zinc-400 hover:text-zinc-900 border-none bg-transparent cursor-pointer p-0"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>
                </div>
                <div class="space-y-2.5 text-xs text-zinc-650" id="notes-checklist-container">
                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" checked class="rounded border-zinc-300 text-zinc-950 focus:ring-0 mt-0.5">
                        <span class="line-through text-zinc-400 leading-normal">Review villa shoot shot list before 9:00 AM</span>
                    </label>
                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" class="rounded border-zinc-300 text-zinc-950 focus:ring-0 mt-0.5" onchange="if(window.coraShowToast) window.coraShowToast('Checklist item updated', 'info')">
                        <span class="leading-normal">Send drafts contract to Aria Group Singapore</span>
                    </label>
                </div>
            </div>

            <!-- Card D: Week at a glance (Shown in Week view) -->
            <div id="cora-sidebar-glance-card" class="hidden bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-4 select-none">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Week at a glance</span>
                    <button onclick="if(window.coraShowToast) window.coraShowToast('Weekly report loading...', 'info')" class="text-[10px] font-bold text-zinc-450 hover:text-zinc-900 border-none bg-transparent cursor-pointer p-0">View report</button>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-2.5">
                        <span class="text-[14px] font-bold text-zinc-950 font-mono block">14</span>
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-0.5 block">Events</span>
                    </div>
                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-2.5">
                        <span class="text-[14px] font-bold text-zinc-950 font-mono block">28h 30m</span>
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-0.5 block">Scheduled</span>
                    </div>
                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-2.5">
                        <span class="text-[14px] font-bold text-zinc-950 font-mono block">8</span>
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-0.5 block">Meetings</span>
                    </div>
                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-2.5">
                        <span class="text-[14px] font-bold text-zinc-950 font-mono block">92%</span>
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-0.5 block">Efficiency</span>
                    </div>
                </div>
            </div>

            <!-- Card E: Today's Schedule Card -->
            <div id="cora-sidebar-schedule-card" class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider" id="cora-today-schedule-sidebar-title">Today's Schedule</span>
                    <span id="cora-today-schedule-title" class="text-[10px] font-extrabold font-mono text-zinc-400 bg-zinc-50 px-2 py-0.5 rounded-full">Day 14</span>
                </div>

                <div id="cora-today-schedule-list" class="space-y-3">
                    <!-- Injected dynamically by JS -->
                </div>
            </div>

            <!-- Card F: Upcoming Events Card -->
            <div id="cora-sidebar-upcoming-card" class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-4">
                <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider block">Upcoming Events</span>
                <div id="cora-upcoming-events-list" class="space-y-3">
                    <!-- Injected dynamically by JS -->
                </div>
            </div>

        </div>
    </div>
</div>

<!-- =========================================================================
     5-STEP INTERACTIVE EVENT WIZARD MODAL (CREATOR / EDITOR)
     ========================================================================= -->

<div id="cora-event-modal-backdrop" class="hidden fixed inset-0 bg-black/45 z-[9999] backdrop-blur-[1px] flex items-center justify-center p-4 transition-all duration-200" onclick="window.coraCloseEventModal()">
    <div id="cora-event-modal" class="bg-white rounded-2xl border border-zinc-200 shadow-2xl w-[530px] max-w-[95vw] flex flex-col p-6 space-y-4 cursor-default transform scale-95 opacity-0 transition-all duration-200" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
            <div class="space-y-0.5">
                <h3 id="modal-wizard-title" class="text-sm font-bold text-zinc-950">Add Event</h3>
                <!-- Stepper Progress Badges -->
                <div class="flex items-center gap-1 select-none pt-1" id="wizard-progress-steps">
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-950" id="step-dot-1"></span>
                    <span class="w-8 h-0.5 bg-zinc-200" id="step-line-1"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-250" id="step-dot-2"></span>
                    <span class="w-8 h-0.5 bg-zinc-200" id="step-line-2"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-250" id="step-dot-3"></span>
                    <span class="w-8 h-0.5 bg-zinc-200" id="step-line-3"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-250" id="step-dot-4"></span>
                </div>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1 rounded-full hover:bg-zinc-50 transition-colors" onclick="window.coraCloseEventModal()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- 5-STEP STEPS WRAPPER -->
        <div class="flex-1">

            <!-- STEP 1: EVENT DETAILS -->
            <div id="wizard-step-1" class="space-y-4">
                <!-- Large borderless title -->
                <div>
                    <input type="text" id="wizard-event-title" required placeholder="Event Title" class="w-full text-base font-bold border-b border-zinc-200 pb-2 focus:outline-none focus:border-zinc-950 text-zinc-950 bg-transparent placeholder-zinc-300">
                </div>

                <!-- Date & Time picker scale -->
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1">Date</label>
                        <div class="flex items-center gap-1.5 bg-zinc-50 border border-zinc-200 px-3 py-2.5 rounded-xl">
                            <span class="font-bold text-zinc-700">Aug</span>
                            <input type="number" id="wizard-event-day" min="1" max="31" required class="w-8 bg-transparent focus:outline-none font-bold text-zinc-950 text-center">
                            <span class="text-zinc-400">, 2026</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1">Time Block</label>
                        <div class="flex items-center gap-1 bg-zinc-50 border border-zinc-200 px-3 py-2.5 rounded-xl">
                            <input type="text" id="wizard-event-time" required placeholder="09:00 AM - 10:00 AM" class="w-full bg-transparent focus:outline-none font-bold text-zinc-950">
                        </div>
                    </div>
                </div>

                <!-- Primary Client & Status -->
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1">Primary Client *</label>
                        <input type="text" id="wizard-event-client" required placeholder="e.g. Vipul Malhotra" class="w-full px-3.5 py-2.5 border border-zinc-200 rounded-xl focus:border-zinc-950 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1">Event Status</label>
                        <select id="modal-event-status" required class="w-full px-3 py-2.5 border border-zinc-200 rounded-xl focus:border-zinc-950 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                            <option value="Confirmed" data-color="emerald">Confirmed</option>
                            <option value="Scheduled" data-color="zinc">Scheduled</option>
                            <option value="In Progress" data-color="blue">In Progress</option>
                            <option value="Pending" data-color="amber">Pending</option>
                        </select>
                    </div>
                </div>

                <!-- Event Type tabs -->
                <div>
                    <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1.5">Event Category / Type</label>
                    <div class="flex items-center gap-2 select-none" id="wizard-event-type-tabs">
                        <button type="button" class="px-3.5 py-1.5 bg-zinc-950 text-white text-xs font-bold rounded-lg border-none cursor-pointer" onclick="window.coraSetWizardType(this, 'Shoot')">Shoot</button>
                        <button type="button" class="px-3.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-lg border-none cursor-pointer" onclick="window.coraSetWizardType(this, 'Showing')">Showing</button>
                        <button type="button" class="px-3.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-lg border-none cursor-pointer" onclick="window.coraSetWizardType(this, 'Meeting')">Meeting</button>
                        <button type="button" class="px-3.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-lg border-none cursor-pointer" onclick="window.coraSetWizardType(this, 'Audit')">Audit</button>
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1">Location / Meeting Link</label>
                    <input type="text" id="wizard-event-location" placeholder="Add location or video link..." class="w-full px-3.5 py-2.5 text-xs border border-zinc-200 rounded-xl focus:border-zinc-950 focus:outline-none bg-white text-zinc-950">
                </div>

                <!-- Add Guests inputs -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-zinc-450 uppercase">Invite Guests / Clients</label>
                    <div class="relative">
                        <div class="flex items-center border border-zinc-200 focus-within:border-zinc-950 rounded-xl px-3 py-1.5 bg-white">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 mr-2 shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <input type="text" id="wizard-guests-search" placeholder="Search guests to add..." class="w-full bg-transparent focus:outline-none text-xs text-zinc-950" oninput="window.coraFilterGuests(this.value)" autocomplete="off">
                        </div>
                        <div id="wizard-guests-dropdown" class="hidden absolute left-0 right-0 top-full mt-1 z-50 bg-white border border-zinc-200 rounded-xl shadow-lg max-h-40 overflow-y-auto p-1 font-sans space-y-0.5">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                    <!-- Selection pills -->
                    <div id="wizard-selected-guests-container" class="flex flex-wrap gap-1.5 pt-1">
                        <!-- Guest chips -->
                    </div>
                </div>
            </div>

            <!-- STEP 2: DETAILS & OPTIONS -->
            <div id="wizard-step-2" class="hidden space-y-4">
                <!-- Description text area -->
                <div>
                    <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1">Description / Notes</label>
                    <textarea id="wizard-event-desc" rows="3" placeholder="Add event details, shoot briefs, agenda, or reminders..." class="w-full px-3.5 py-2.5 text-xs border border-zinc-200 rounded-xl focus:border-zinc-950 focus:outline-none bg-white text-zinc-950 resize-none"></textarea>
                </div>

                <!-- Google Calendar Connector Option -->
                <div class="p-3 bg-zinc-50 rounded-2xl border border-zinc-200 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21.35 11.1H12v2.7h5.38c-.24 1.28-.96 2.37-2.07 3.12v2.6h3.33c1.95-1.8 3.07-4.45 3.07-7.62 0-.58-.05-1.15-.15-1.8z" fill="#4285F4"/><path d="M12 20.6c2.32 0 4.27-.77 5.7-2.08l-3.33-2.6c-.92.62-2.1.98-3.37.98-2.6 0-4.8-1.76-5.59-4.12H2.03v2.68C3.52 18.42 7.48 20.6 12 20.6z" fill="#34A853"/><path d="M6.41 12.78c-.2-.6-.31-1.25-.31-1.92s.11-1.32.31-1.92V6.26H2.03C1.3 7.72.88 9.38.88 11.15s.42 3.43 1.15 4.89l3.22-2.5c-.2-.6-.31-1.25-.31-1.92z" fill="#FBBC05"/><path d="M12 5.96c1.26 0 2.4.43 3.3 1.28l2.47-2.47C16.27 3.32 14.32 2.5 12 2.5 7.48 2.5 3.52 4.68 2.03 7.64l3.22 2.5c.79-2.36 2.99-4.12 5.59-4.12z" fill="#EA4335"/></svg>
                        <div class="space-y-0.5">
                            <span class="font-bold text-zinc-950 flex items-center gap-1.5">
                                Google Calendar Sync
                                <span id="modal-wizard-gcal-badge" class="px-1.5 py-0.2 bg-zinc-200 text-[8px] rounded-full text-zinc-600 uppercase font-mono tracking-tight">Disconnected</span>
                            </span>
                            <span id="modal-wizard-gcal-desc" class="text-[10px] text-zinc-450 block">Bidirectional schedule synchronization</span>
                        </div>
                    </div>
                    <button type="button" id="modal-wizard-gcal-btn" onclick="window.coraTriggerWizardGoogleConnect(event)" class="text-zinc-950 font-bold hover:underline cursor-pointer border-none bg-transparent p-0 focus:outline-none">Connect</button>
                </div>

                <!-- Reminders & Repeat -->
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1">Reminder</label>
                        <select id="wizard-event-reminder" class="w-full px-3 py-2.5 border border-zinc-200 rounded-xl focus:border-zinc-950 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                            <option value="none">No reminder</option>
                            <option value="5m">5 minutes before</option>
                            <option value="15m" selected>15 minutes before</option>
                            <option value="1h">1 hour before</option>
                            <option value="1d">1 day before</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1">Repeat Cycle</label>
                        <select id="wizard-event-repeat" class="w-full px-3 py-2.5 border border-zinc-200 rounded-xl focus:border-zinc-950 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                            <option value="none" selected>Does not repeat</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>

                <!-- Attachments Dropzone -->
                <div>
                    <label class="block text-[11px] font-bold text-zinc-450 uppercase mb-1">Add Attachments</label>
                    <div onclick="if(window.coraShowToast) window.coraShowToast('File upload simulator triggered', 'info')" class="border border-dashed border-zinc-200 rounded-xl p-4 text-center hover:bg-zinc-50 transition-colors cursor-pointer select-none">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="mx-auto text-zinc-400 mb-1"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        <span class="text-[10px] text-zinc-450 font-bold block" id="wizard-attachment-label">Drop briefing document / PDF brief here</span>
                    </div>
                </div>
            </div>

            <!-- STEP 3: REVIEW DETAILS -->
            <div id="wizard-step-3" class="hidden space-y-4">
                <div class="bg-zinc-50 border border-zinc-200 p-4.5 rounded-2xl space-y-3.5 text-xs text-zinc-650">
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-lg bg-white border border-zinc-200 flex items-center justify-center shrink-0" id="review-icon-box">
                            <!-- Type Icon dynamically -->
                        </span>
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block" id="review-type-badge">Shoot</span>
                            <h4 class="text-sm font-bold text-zinc-950 mt-0.5" id="review-title">Luxury Villa Shoot</h4>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3.5 pt-2 border-t border-zinc-200/60">
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase block">Date & Time</span>
                            <span class="font-bold text-zinc-900 block mt-0.5" id="review-time">Fri, Aug 14 · 09:00 AM - 10:00 AM</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase block">Primary Client</span>
                            <span class="font-bold text-zinc-900 block mt-0.5" id="review-client">Vipul Malhotra</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-zinc-200/60">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase block">Invited Guests</span>
                        <div class="flex flex-wrap gap-1 mt-1" id="review-guests-badges">
                            <!-- Guest chips summary -->
                        </div>
                    </div>

                    <div class="pt-2 border-t border-zinc-200/60" id="review-desc-container">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase block">Description / Notes</span>
                        <p class="mt-0.5 leading-normal text-zinc-600" id="review-desc">Exterior + interior photo session for luxury villa project.</p>
                    </div>
                </div>
            </div>

            <!-- STEP 4: CONFIRMED SUCCESS -->
            <div id="wizard-step-4" class="hidden py-8 text-center space-y-4">
                <!-- Large dynamic checkmark -->
                <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-sm">
                    <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-zinc-950" id="confirmed-headline">Event Created!</h4>
                    <p class="text-xs text-zinc-500">Your schedule event is now booked and synced across modules.</p>
                </div>

                <div class="bg-zinc-50 border border-zinc-200/80 rounded-xl p-3 max-w-xs mx-auto text-xs text-left">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" id="confirmed-badge-dot"></span>
                        <strong class="text-zinc-950 font-bold truncate" id="confirmed-title">Luxury Villa Shoot</strong>
                    </div>
                    <span class="text-[10px] text-zinc-400 block font-mono mt-0.5" id="confirmed-time-recap">Aug 14, 2026 · 09:00 AM - 10:00 AM</span>
                </div>

                <div class="pt-2">
                    <button type="button" onclick="window.coraCloseEventModal()" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer w-32 border-none">
                        Done
                    </button>
                </div>
            </div>

        </div>

        <!-- Wizard Nav Actions bar (Step 1-3 only) -->
        <div class="pt-4 border-t border-zinc-100 flex items-center justify-between shrink-0" id="wizard-nav-actions">
            <!-- Left Side Actions for Edit Flow -->
            <div class="flex items-center gap-1.5" id="wizard-edit-actions">
                <button type="button" onclick="window.coraDeleteActiveEvent()" class="px-3 py-2 border border-red-200 hover:bg-red-50 text-red-600 text-xs font-bold rounded-xl transition-colors cursor-pointer border-none bg-transparent">
                    Delete
                </button>
                <button type="button" onclick="window.coraDuplicateActiveEvent()" class="px-3 py-2 border border-zinc-200 hover:bg-zinc-50 text-zinc-800 text-xs font-bold rounded-xl transition-colors cursor-pointer border-none bg-transparent">
                    Duplicate
                </button>
            </div>
            <div class="flex items-center gap-2 ml-auto">
                <button type="button" id="wizard-back-btn" onclick="window.coraWizardBack()" class="px-4 py-2 border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-50 transition-colors cursor-pointer">
                    Back
                </button>
                <button type="button" id="wizard-next-btn" onclick="window.coraWizardNext()" class="px-4.5 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT WORKSPACE INTERACTIVE LOGIC
     ========================================================================= -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Master data
    window.coraCalendarEvents = <?php echo json_encode($calendar_events); ?>;
    window.coraActiveDay = 14;
    window.coraActiveViewMode = 'month'; // 'day' | 'week' | 'month'
    window.coraGoogleCalendarSynced = false;
    
    // Wizard step state
    window.coraWizardStep = 1;
    window.coraWizardEditMode = false;
    window.coraWizardActiveEventId = null;
    window.coraWizardActiveType = "Shoot";

    // Guest search database list
    window.coraAvailableGuests = [
        { name: "Vipul Malhotra", email: "vipul.m@apex.local" },
        { name: "Aria Group Singapore", email: "singapore.leads@aria.local" },
        { name: "Apex Capital", email: "deals@apex.local" },
        { name: "Rhea Kapoor", email: "rhea.kapoor@vogue.local" },
        { name: "Knight Frank", email: "inspections@knightfrank.local" },
        { name: "Karan Johar", email: "karan@dharma.local" },
        { name: "Rajesh Sharma", email: "rajesh@cora.local" },
        { name: "Anil Kumar", email: "anil.k@cora.local" }
    ];
    window.coraSelectedGuests = [];

    // Checklist Notes items database list
    window.coraNotesChecklist = [
        { id: 1, text: "Review villa shoot shot list before 9:00 AM", checked: true },
        { id: 2, text: "Send drafts contract to Aria Group Singapore", checked: false },
        { id: 3, text: "Prepare DLF CyberCity raw footage links", checked: false }
    ];

    // Initialize layout tabs
    window.coraSwitchViewMode = function(mode) {
        window.coraActiveViewMode = mode;

        // Toggles active state classes on tabs switcher
        const modes = ['day', 'week', 'month'];
        modes.forEach(m => {
            const btn = document.getElementById('view-btn-' + m);
            const container = document.getElementById('cora-calendar-' + m + '-view');
            if (btn) {
                if (m === mode) {
                    btn.className = 'px-3.5 py-1.5 text-xs font-bold rounded-lg transition-colors cursor-pointer text-zinc-950 bg-white shadow-2xs border-none';
                } else {
                    btn.className = 'px-3.5 py-1.5 text-xs font-bold rounded-lg transition-colors cursor-pointer text-zinc-500 hover:text-zinc-900 bg-transparent border-none';
                }
            }
            if (container) {
                if (m === mode) {
                    container.classList.remove('hidden');
                } else {
                    container.classList.add('hidden');
                }
            }
        });

        // Hide/Show sidebar widgets contextually
        const syncCard = document.getElementById('cora-sidebar-sync-card');
        const actionsCard = document.getElementById('cora-sidebar-actions-card');
        const notesCard = document.getElementById('cora-sidebar-notes-card');
        const glanceCard = document.getElementById('cora-sidebar-glance-card');

        if (mode === 'month') {
            if (syncCard) syncCard.classList.remove('hidden');
            if (actionsCard) actionsCard.classList.add('hidden');
            if (notesCard) notesCard.classList.add('hidden');
            if (glanceCard) glanceCard.classList.add('hidden');
        } else if (mode === 'week') {
            if (syncCard) syncCard.classList.add('hidden');
            if (actionsCard) actionsCard.classList.add('hidden');
            if (notesCard) notesCard.classList.add('hidden');
            if (glanceCard) glanceCard.classList.remove('hidden');
        } else if (mode === 'day') {
            if (syncCard) syncCard.classList.add('hidden');
            if (actionsCard) actionsCard.classList.remove('hidden');
            if (notesCard) notesCard.classList.remove('hidden');
            if (glanceCard) glanceCard.classList.add('hidden');
        }

        // Update Title Header Text
        window.coraUpdatePeriodTitleText();
        window.coraRenderCalendar();
    };

    window.coraUpdatePeriodTitleText = function() {
        const titleEl = document.getElementById('calendar-period-title');
        if (!titleEl) return;

        if (window.coraActiveViewMode === 'month') {
            titleEl.innerText = "AUGUST 2026";
        } else if (window.coraActiveViewMode === 'week') {
            titleEl.innerText = "Aug 10 - Aug 16, 2026";
        } else if (window.coraActiveViewMode === 'day') {
            titleEl.innerText = "Friday, Aug 14, 2026";
        }
    };

    // Period navigators (mocked)
    window.coraGoToToday = function() {
        window.coraActiveDay = 14;
        window.coraSelectDay(14);
        if (window.coraShowToast) window.coraShowToast('Navigated to August 14, 2026', 'info');
    };
    window.coraPrevPeriod = function() {
        if (window.coraShowToast) window.coraShowToast('Previous period schedules loaded', 'info');
    };
    window.coraNextPeriod = function() {
        if (window.coraShowToast) window.coraShowToast('Next period schedules loaded', 'info');
    };

    // Render Master logic
    window.coraRenderCalendar = function() {
        // A. MONTH VIEW INJECTIONS
        // Clear all days
        document.querySelectorAll('.calendar-day-events').forEach(el => el.innerHTML = '');

        // B. WEEK VIEW INJECTIONS
        // Clear all weekly columns
        for (let col = 10; col <= 16; col++) {
            const container = document.getElementById('week-column-day-' + col);
            if (container) {
                // Keep only the grid helper rows (12 empty slot buttons)
                const buttons = container.querySelectorAll('div');
                container.innerHTML = '';
                buttons.forEach(btn => container.appendChild(btn));
            }
        }

        // C. DAY VIEW INJECTIONS
        // Clear all hourly rows
        for (let hour = 8; hour <= 19; hour++) {
            const container = document.getElementById('day-events-hour-' + hour);
            if (container) container.innerHTML = '';
        }

        // D. SIDEBAR LIST INJECTIONS
        const todayList = document.getElementById('cora-today-schedule-list');
        const upcomingList = document.getElementById('cora-upcoming-events-list');
        if (todayList) todayList.innerHTML = '';
        if (upcomingList) upcomingList.innerHTML = '';

        // E. CALCULATE KPIs
        let totalCount = window.coraCalendarEvents.length;
        const totalEl = document.getElementById('stat-total-events');
        if (totalEl) totalEl.innerText = totalCount;
        
        const dayCountEl = document.getElementById('day-stat-count');
        let todayCount = window.coraCalendarEvents.filter(e => parseInt(e.day) === window.coraActiveDay).length;
        if (dayCountEl) dayCountEl.innerText = todayCount;

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

        // Sort lists
        eventsForUpcoming.sort((a, b) => parseInt(a.day) - parseInt(b.day));
        eventsForToday.sort((a, b) => {
            const timeA = a.time.split(' ')[0];
            const timeB = b.time.split(' ')[0];
            return timeA.localeCompare(timeB);
        });

        // ── Render Month Cells ──────────────────────────────────────────────
        Object.keys(eventsByDay).forEach(dayStr => {
            const day = parseInt(dayStr);
            const container = document.getElementById('events-day-' + day);
            if (!container) return;

            const dayEvents = eventsByDay[day];
            const typedGroups = {};
            dayEvents.forEach(e => {
                if (!typedGroups[e.type]) {
                    typedGroups[e.type] = { count: 0, color: e.status_color || 'zinc' };
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

        // ── Render Week Column Blocks ───────────────────────────────────────
        window.coraCalendarEvents.forEach(event => {
            const day = parseInt(event.day);
            const container = document.getElementById('week-column-day-' + day);
            if (!container) return;

            // Calculate mock absolute height/top offset based on start time hour
            let startHour = 9; // Default fallback
            const timeText = event.time.split(' ')[0];
            const isPM = event.time.toLowerCase().includes('pm') && !event.time.startsWith('12:');
            const hourPart = parseInt(timeText.split(':')[0]);
            startHour = isPM ? (hourPart + 12) : hourPart;

            // Offset top (approx 50px per hour starting from 8 AM)
            const topOffset = (startHour - 8) * 50 + 6;
            
            let colorBorder = 'border-zinc-200 bg-zinc-50';
            let colorText = 'text-zinc-800';
            if (event.status_color === 'emerald') { colorBorder = 'border-emerald-200 bg-emerald-50/75'; colorText = 'text-emerald-950'; }
            else if (event.status_color === 'blue') { colorBorder = 'border-blue-200 bg-blue-50/75'; colorText = 'text-blue-950'; }
            else if (event.status_color === 'amber') { colorBorder = 'border-amber-200 bg-amber-50/75'; colorText = 'text-amber-950'; }

            const block = document.createElement('div');
            block.className = 'absolute left-1 right-1 p-2 rounded-xl border flex flex-col justify-between shadow-3xs cursor-pointer hover:scale-[1.02] transition-transform ' + colorBorder + ' ' + colorText;
            block.style.top = topOffset + 'px';
            block.style.height = '42px';
            block.onclick = function(e) {
                e.stopPropagation();
                window.coraOpenEventModal(event.id, true);
            };
            block.innerHTML = `
                <div class="text-[9px] font-extrabold truncate uppercase tracking-tight">${event.type}</div>
                <div class="text-[9.5px] font-bold truncate leading-tight">${event.title}</div>
            `;
            container.appendChild(block);
        });

        // ── Render Day View Detailed rows ───────────────────────────────────
        window.coraCalendarEvents.forEach(event => {
            const day = parseInt(event.day);
            if (day !== window.coraActiveDay) return;

            // Calculate start hour row
            let startHour = 9;
            const timeText = event.time.split(' ')[0];
            const isPM = event.time.toLowerCase().includes('pm') && !event.time.startsWith('12:');
            const hourPart = parseInt(timeText.split(':')[0]);
            startHour = isPM ? (hourPart + 12) : hourPart;

            const container = document.getElementById('day-events-hour-' + startHour);
            if (!container) return;

            let colorTheme = 'border-zinc-200 bg-zinc-50/50 text-zinc-900';
            let badgeStyle = 'bg-zinc-200 text-zinc-700';
            if (event.status_color === 'emerald') { colorTheme = 'border-emerald-200 bg-emerald-50/60 text-emerald-950'; badgeStyle = 'bg-emerald-100 text-emerald-800'; }
            else if (event.status_color === 'blue') { colorTheme = 'border-blue-200 bg-blue-50/60 text-blue-950'; badgeStyle = 'bg-blue-100 text-blue-800'; }
            else if (event.status_color === 'amber') { colorTheme = 'border-amber-200 bg-amber-50/60 text-amber-950'; badgeStyle = 'bg-amber-100 text-amber-800'; }

            const block = document.createElement('div');
            block.className = 'w-full p-3 rounded-2xl border flex items-center justify-between shadow-3xs cursor-pointer hover:scale-[1.01] transition-transform ' + colorTheme;
            block.onclick = function(e) {
                e.stopPropagation();
                window.coraOpenEventModal(event.id, true);
            };
            block.innerHTML = `
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs">${event.title}</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-tight ${badgeStyle}">${event.status}</span>
                    </div>
                    <span class="text-[10px] text-zinc-500">Client: <strong>${event.client}</strong> · ${event.time}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="p-1 text-zinc-400 hover:text-zinc-950 bg-transparent border-none cursor-pointer flex"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg></button>
                </div>
            `;
            container.appendChild(block);
        });

        // ── Render Today / Active Day Sidebar ───────────────────────────────
        const sidebarTitle = document.getElementById('cora-today-schedule-sidebar-title');
        if (sidebarTitle) {
            sidebarTitle.innerText = (window.coraActiveDay === 14) ? "Today's Schedule" : "Day " + window.coraActiveDay + " Schedule";
        }

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
                    card.className = 'p-3.5 bg-zinc-50/50 hover:bg-zinc-50 rounded-xl border border-zinc-150 transition-all space-y-2 cursor-pointer';
                    card.onclick = function() { window.coraOpenEventModal(event.id, true); };
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

        // ── Render Upcoming Events Sidebar ───────────────────────────────
        if (upcomingList) {
            if (eventsForUpcoming.length === 0) {
                upcomingList.innerHTML = '<div class="p-6 text-center text-zinc-400 text-xs border border-dashed border-zinc-200 rounded-xl">No upcoming bookings.</div>';
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

        // Render checklist notes items
        window.coraRenderChecklistNotes();
    };

    // Select Day cell handler
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
    };

    // Checklist Notes drawer/panel renderer
    window.coraRenderChecklistNotes = function() {
        const container = document.getElementById('notes-checklist-container');
        if (!container) return;
        container.innerHTML = '';

        window.coraNotesChecklist.forEach(item => {
            const label = document.createElement('label');
            label.className = 'flex items-start gap-2.5 cursor-pointer select-none';
            label.innerHTML = `
                <input type="checkbox" ${item.checked ? 'checked' : ''} class="rounded border-zinc-300 text-zinc-950 focus:ring-0 mt-0.5" onchange="window.coraToggleNoteCheck(${item.id})">
                <span class="${item.checked ? 'line-through text-zinc-400' : ''} leading-normal">${item.text}</span>
            `;
            container.appendChild(label);
        });
    };

    window.coraToggleNoteCheck = function(id) {
        const note = window.coraNotesChecklist.find(n => n.id === id);
        if (note) {
            note.checked = !note.checked;
            window.coraRenderChecklistNotes();
            if (window.coraShowToast) window.coraShowToast('Checklist item updated.', 'success');
        }
    };

    window.coraAddNotesChecklistItem = function() {
        const text = prompt("Add new note checklist task:");
        if (text && text.trim() !== '') {
            window.coraNotesChecklist.push({
                id: Date.now(),
                text: text.trim(),
                checked: false
            });
            window.coraRenderChecklistNotes();
            if (window.coraShowToast) window.coraShowToast('Checklist task added.', 'success');
        }
    };

    // ── 5-STEP WIZARD POPUP MODAL LOGIC ─────────────────────────────────────
    window.coraOpenEventModal = function(idOrNull, isEditMode) {
        const backdrop = document.getElementById('cora-event-modal-backdrop');
        const modal = document.getElementById('cora-event-modal');
        if (!backdrop || !modal) return;

        window.coraWizardStep = 1;
        window.coraWizardEditMode = !!isEditMode;
        window.coraWizardActiveEventId = idOrNull;

        const wizardTitle = document.getElementById('modal-wizard-title');
        const headline = document.getElementById('confirmed-headline');

        // Set edit/add actions visibility
        const editActions = document.getElementById('wizard-edit-actions');
        if (isEditMode) {
            if (editActions) editActions.classList.remove('hidden');
            if (wizardTitle) wizardTitle.innerText = "Edit Event";
            if (headline) headline.innerText = "Event Updated!";

            // Prefill with existing event
            const eventObj = window.coraCalendarEvents.find(e => e.id === idOrNull);
            if (eventObj) {
                document.getElementById('wizard-event-title').value = eventObj.title;
                document.getElementById('wizard-event-day').value = eventObj.day;
                document.getElementById('wizard-event-time').value = eventObj.time;
                document.getElementById('wizard-event-location').value = eventObj.location || 'HeyCora Studio HQ';
                document.getElementById('wizard-event-client').value = eventObj.client;
                document.getElementById('wizard-event-desc').value = eventObj.description || 'Mock shoot project agenda.';
                
                // Select category tab
                window.coraWizardActiveType = eventObj.type;
                const typeTabs = document.querySelectorAll('#wizard-event-type-tabs button');
                typeTabs.forEach(t => {
                    if (t.innerText.trim() === eventObj.type) {
                        t.className = 'px-3.5 py-1.5 bg-zinc-950 text-white text-xs font-bold rounded-lg border-none cursor-pointer';
                    } else {
                        t.className = 'px-3.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-lg border-none cursor-pointer';
                    }
                });

                // Prefill status
                const statusSelect = document.getElementById('modal-event-status');
                statusSelect.value = eventObj.status;

                // Load selected guests
                window.coraSelectedGuests = eventObj.guests || [
                    { name: "Vipul Malhotra", email: "vipul.m@apex.local" }
                ];
            }
        } else {
            if (editActions) editActions.classList.add('hidden');
            if (wizardTitle) wizardTitle.innerText = "Add Event";
            if (headline) headline.innerText = "Event Created!";

            // Clear defaults
            document.getElementById('wizard-event-title').value = '';
            document.getElementById('wizard-event-day').value = window.coraActiveDay;
            document.getElementById('wizard-event-time').value = '10:00 AM - 11:30 AM';
            document.getElementById('wizard-event-location').value = '';
            document.getElementById('wizard-event-client').value = '';
            document.getElementById('wizard-event-desc').value = '';
            window.coraSelectedGuests = [];
        }

        window.coraRenderSelectedGuests();
        window.coraUpdateWizardStepDisplay();

        // Update modal Google sync status
        window.coraUpdateWizardGcalStatus();

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

    // Stepper Nav
    window.coraUpdateWizardStepDisplay = function() {
        const maxSteps = 4; // 1: Details, 2: Options, 3: Review, 4: Confirmed
        
        // Hide all step panels
        for (let s = 1; s <= maxSteps; s++) {
            const panel = document.getElementById('wizard-step-' + s);
            if (panel) {
                if (s === window.coraWizardStep) panel.classList.remove('hidden');
                else panel.classList.add('hidden');
            }
        }

        // Update progress dots/lines
        for (let s = 1; s <= maxSteps; s++) {
            const dot = document.getElementById('step-dot-' + s);
            const line = document.getElementById('step-line-' + s);
            if (dot) {
                if (s <= window.coraWizardStep) {
                    dot.className = 'w-1.5 h-1.5 rounded-full bg-zinc-950';
                } else {
                    dot.className = 'w-1.5 h-1.5 rounded-full bg-zinc-250';
                }
            }
            if (line) {
                if (s < window.coraWizardStep) {
                    line.className = 'w-8 h-0.5 bg-zinc-950';
                } else {
                    line.className = 'w-8 h-0.5 bg-zinc-200';
                }
            }
        }

        // Actions buttons display
        const nextBtn = document.getElementById('wizard-next-btn');
        const backBtn = document.getElementById('wizard-back-btn');
        const navActions = document.getElementById('wizard-nav-actions');

        if (window.coraWizardStep === 4) {
            // Success state - nav actions completely hidden (uses bottom Done button)
            if (navActions) navActions.classList.add('hidden');
        } else {
            if (navActions) navActions.classList.remove('hidden');
            if (backBtn) {
                if (window.coraWizardStep === 1) backBtn.classList.add('hidden');
                else backBtn.classList.remove('hidden');
            }
            if (nextBtn) {
                if (window.coraWizardStep === 3) {
                    nextBtn.innerText = window.coraWizardEditMode ? "Confirm & Save" : "Create Event";
                } else {
                    nextBtn.innerText = "Next";
                }
            }
        }
    };

    window.coraWizardNext = function() {
        if (window.coraWizardStep === 1) {
            // Validate inputs
            const title = document.getElementById('wizard-event-title').value;
            const client = document.getElementById('wizard-event-client').value;
            if (!title || !client) {
                if (window.coraShowToast) window.coraShowToast("Event Title and Client Name are required", "critical");
                return;
            }
            window.coraWizardStep = 2;
        } else if (window.coraWizardStep === 2) {
            // Load review summaries
            window.coraLoadReviewSummaries();
            window.coraWizardStep = 3;
        } else if (window.coraWizardStep === 3) {
            // Save Event (Step 3 to 4)
            window.coraSaveWizardEventData();
            window.coraWizardStep = 4;
        }
        window.coraUpdateWizardStepDisplay();
    };

    window.coraWizardBack = function() {
        if (window.coraWizardStep > 1) {
            window.coraWizardStep--;
            window.coraUpdateWizardStepDisplay();
        }
    };

    window.coraLoadReviewSummaries = function() {
        document.getElementById('review-title').innerText = document.getElementById('wizard-event-title').value;
        document.getElementById('review-type-badge').innerText = window.coraWizardActiveType;
        
        const day = document.getElementById('wizard-event-day').value;
        const time = document.getElementById('wizard-event-time').value;
        document.getElementById('review-time').innerText = "Aug " + day + ", 2026 · " + time;
        document.getElementById('review-client').innerText = document.getElementById('wizard-event-client').value;

        // Render invited guests badges
        const badgesContainer = document.getElementById('review-guests-badges');
        if (badgesContainer) {
            badgesContainer.innerHTML = '';
            if (window.coraSelectedGuests.length === 0) {
                badgesContainer.innerHTML = '<span class="text-zinc-400">No guests invited</span>';
            } else {
                window.coraSelectedGuests.forEach(g => {
                    const badge = document.createElement('span');
                    badge.className = 'px-2 py-0.5 bg-zinc-150 border border-zinc-200 text-zinc-700 text-[10px] rounded-lg font-medium';
                    badge.innerText = g.name;
                    badgesContainer.appendChild(badge);
                });
            }
        }

        // Review description
        const descText = document.getElementById('wizard-event-desc').value;
        const descContainer = document.getElementById('review-desc-container');
        if (descContainer) {
            if (descText && descText.trim() !== '') {
                document.getElementById('review-desc').innerText = descText;
                descContainer.classList.remove('hidden');
            } else {
                descContainer.classList.add('hidden');
            }
        }

        // Set type icon dynamically
        const iconContainer = document.getElementById('review-icon-box');
        if (iconContainer) {
            if (window.coraWizardActiveType === 'Shoot') {
                iconContainer.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>';
            } else {
                iconContainer.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-blue-600"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
            }
        }
    };

    window.coraSaveWizardEventData = function() {
        const title = document.getElementById('wizard-event-title').value;
        const day = parseInt(document.getElementById('wizard-event-day').value);
        const client = document.getElementById('wizard-event-client').value;
        const time = document.getElementById('wizard-event-time').value;
        const location = document.getElementById('wizard-event-location').value;
        const description = document.getElementById('wizard-event-desc').value;
        const statusSelect = document.getElementById('modal-event-status');
        const status = statusSelect.value;
        const color = statusSelect.options[statusSelect.selectedIndex].getAttribute('data-color') || 'zinc';

        if (window.coraWizardEditMode) {
            // Edit flow
            const eventObj = window.coraCalendarEvents.find(e => e.id === window.coraWizardActiveEventId);
            if (eventObj) {
                eventObj.title = title;
                eventObj.day = day;
                eventObj.client = client;
                eventObj.time = time;
                eventObj.location = location;
                eventObj.description = description;
                eventObj.type = window.coraWizardActiveType;
                eventObj.status = status;
                eventObj.status_color = color;
                eventObj.guests = window.coraSelectedGuests;
            }
        } else {
            // Add flow
            const newEvent = {
                id: Date.now(),
                title: title,
                type: window.coraWizardActiveType,
                client: client,
                time: time,
                location: location,
                description: description,
                status: status,
                status_color: color,
                day: day,
                guests: window.coraSelectedGuests
            };
            window.coraCalendarEvents.push(newEvent);
        }

        window.coraRenderCalendar();

        // Populate Success step recap cards
        document.getElementById('confirmed-title').innerText = title;
        document.getElementById('confirmed-time-recap').innerText = "Aug " + day + ", 2026 · " + time;
        
        const badgeDot = document.getElementById('confirmed-badge-dot');
        if (badgeDot) {
            let dotColor = 'bg-zinc-400';
            if (color === 'emerald') dotColor = 'bg-emerald-500';
            else if (color === 'blue') dotColor = 'bg-blue-500';
            else if (color === 'amber') dotColor = 'bg-amber-500';
            badgeDot.className = 'w-1.5 h-1.5 rounded-full ' + dotColor;
        }

        if (window.coraShowToast) {
            const msg = window.coraWizardEditMode ? "Event successfully updated." : "Event successfully created.";
            window.coraShowToast(msg, "success");
        }
    };

    // Set wizard category tabs
    window.coraSetWizardType = function(btn, type) {
        window.coraWizardActiveType = type;
        const tabs = document.querySelectorAll('#wizard-event-type-tabs button');
        tabs.forEach(t => {
            t.className = 'px-3.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-lg border-none cursor-pointer';
        });
        btn.className = 'px-3.5 py-1.5 bg-zinc-950 text-white text-xs font-bold rounded-lg border-none cursor-pointer';
    };

    // Selected guests dynamic rendering
    window.coraRenderSelectedGuests = function() {
        const container = document.getElementById('wizard-selected-guests-container');
        if (!container) return;
        container.innerHTML = '';

        window.coraSelectedGuests.forEach(g => {
            const pill = document.createElement('div');
            pill.className = 'inline-flex items-center gap-1.5 px-2 py-1 bg-zinc-100 border border-zinc-200 text-zinc-800 text-[10px] font-bold rounded-lg select-none';
            pill.innerHTML = `
                <div class="w-4 h-4 rounded-full bg-zinc-300 text-zinc-900 text-[8px] flex items-center justify-center shrink-0 font-black">${g.name.charAt(0)}</div>
                <span>${g.name}</span>
                <button type="button" class="text-zinc-450 hover:text-zinc-900 cursor-pointer p-0 border-none bg-transparent flex items-center" onclick="window.coraRemoveWizardGuest('${g.email}')">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            `;
            container.appendChild(pill);
        });
    };

    window.coraRemoveWizardGuest = function(email) {
        window.coraSelectedGuests = window.coraSelectedGuests.filter(g => g.email !== email);
        window.coraRenderSelectedGuests();
    };

    // Filter dynamic suggestions
    window.coraFilterGuests = function(query) {
        const dropdown = document.getElementById('wizard-guests-dropdown');
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
                    window.coraSelectedGuests.push(g);
                    window.coraRenderSelectedGuests();
                    document.getElementById('wizard-guests-search').value = '';
                    dropdown.classList.add('hidden');
                };
                dropdown.appendChild(btn);
            });
        }
        dropdown.classList.remove('hidden');
    };

    // Google sync inside wizard connect
    window.coraTriggerWizardGoogleConnect = function(e) {
        e.preventDefault();
        const connBtn = document.getElementById('modal-wizard-gcal-btn');
        if (!connBtn) return;

        if (window.coraGoogleCalendarSynced) {
            // Disconnect sync
            window.coraGoogleCalendarSynced = false;
            window.coraUpdateWizardGcalStatus();

            // Sync sidebar badges
            const sidebarBadge = document.getElementById('gcal-sidebar-badge');
            if (sidebarBadge) {
                sidebarBadge.className = 'text-[9px] font-extrabold px-2 py-0.5 rounded-full text-zinc-500 bg-zinc-100 border border-zinc-200/50 uppercase';
                sidebarBadge.innerText = 'Disconnected';
            }

            // Filter out synced Google events
            window.coraCalendarEvents = window.coraCalendarEvents.filter(e => !e.is_google_event);
            window.coraRenderCalendar();

            if (window.coraShowToast) window.coraShowToast("Google Calendar connection removed.", "info");
            return;
        }

        connBtn.innerText = 'Connecting...';
        connBtn.setAttribute('disabled', 'true');

        setTimeout(function() {
            window.coraGoogleCalendarSynced = true;
            window.coraUpdateWizardGcalStatus();

            const sidebarBadge = document.getElementById('gcal-sidebar-badge');
            if (sidebarBadge) {
                sidebarBadge.className = 'text-[9px] font-extrabold px-2 py-0.5 rounded-full text-emerald-700 bg-emerald-50 border border-emerald-200/50 uppercase flex items-center gap-1';
                sidebarBadge.innerHTML = '<span class="w-1 h-1 rounded-full bg-emerald-500"></span> Synced';
            }

            // Simulated Google Calendar Events
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
        }, 850);
    };

    window.coraUpdateWizardGcalStatus = function() {
        const badge = document.getElementById('modal-wizard-gcal-badge');
        const desc = document.getElementById('modal-wizard-gcal-desc');
        const btn = document.getElementById('modal-wizard-gcal-btn');

        if (!badge || !desc || !btn) return;

        btn.removeAttribute('disabled');

        if (window.coraGoogleCalendarSynced) {
            badge.className = 'px-1.5 py-0.2 bg-emerald-50 text-[8px] rounded-full text-emerald-700 border border-emerald-200/50 uppercase font-mono tracking-tight flex items-center gap-0.5';
            badge.innerHTML = '<span class="w-1 h-1 rounded-full bg-emerald-500"></span> Synced';
            desc.innerText = 'Synced with shrutian@cora.local';
            btn.innerText = 'Disconnect';
            btn.className = 'text-zinc-500 hover:text-zinc-950 font-bold hover:underline cursor-pointer bg-transparent border-none p-0 focus:outline-none';
        } else {
            badge.className = 'px-1.5 py-0.2 bg-zinc-200 text-[8px] rounded-full text-zinc-600 uppercase font-mono tracking-tight';
            badge.innerText = 'Disconnected';
            desc.innerText = 'Bidirectional schedule synchronization';
            btn.innerText = 'Connect';
            btn.className = 'text-zinc-950 font-bold hover:underline cursor-pointer bg-transparent border-none p-0 focus:outline-none';
        }
    };

    // Delete flow inside wizard
    window.coraDeleteActiveEvent = function() {
        if (!window.coraWizardActiveEventId) return;
        
        if (confirm("Are you sure you want to delete this event from the calendar?")) {
            window.coraCalendarEvents = window.coraCalendarEvents.filter(e => e.id !== window.coraWizardActiveEventId);
            window.coraRenderCalendar();
            window.coraCloseEventModal();
            if (window.coraShowToast) window.coraShowToast("Event deleted successfully.", "success");
        }
    };

    // Duplicate event
    window.coraDuplicateActiveEvent = function() {
        if (!window.coraWizardActiveEventId) return;

        const eventObj = window.coraCalendarEvents.find(e => e.id === window.coraWizardActiveEventId);
        if (eventObj) {
            const duplicated = Object.assign({}, eventObj);
            duplicated.id = Date.now();
            duplicated.title += " (Copy)";
            
            window.coraCalendarEvents.push(duplicated);
            window.coraRenderCalendar();
            window.coraCloseEventModal();
            if (window.coraShowToast) window.coraShowToast("Event successfully duplicated.", "success");
        }
    };

    // Close guests dropdown on outside click
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('wizard-guests-dropdown');
        const input = document.getElementById('wizard-guests-search');
        if (dropdown && !dropdown.contains(e.target) && e.target !== input) {
            dropdown.classList.add('hidden');
        }
    });

    // Render calendar initially
    window.coraRenderCalendar();
});
</script>

