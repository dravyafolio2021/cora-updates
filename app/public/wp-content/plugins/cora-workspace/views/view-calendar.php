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

<div class="space-y-6 font-sans text-zinc-900 select-none max-w-[1700px] mx-auto pb-12">
    <!-- ═══ 1. STANDARDIZED PAGE HEADER & CTA ACTION BAR ═════════════════════════════════ -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 w-full">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-zinc-950 leading-snug">Workspace Calendar</h1>
            <p class="text-xs sm:text-sm text-zinc-500 mt-1.5 leading-relaxed">Manage corporate schedules, client photo shoot bookings, and team shifts.</p>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap sm:flex-nowrap">
            <button onclick="if(window.coraShowToast) window.coraShowToast('Calendar sync updated.', 'success')" class="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 text-xs font-bold rounded-full transition-all shadow-2xs cursor-pointer flex items-center justify-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 shrink-0"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                Sync External
            </button>
            <button onclick="if(window.coraShowToast) window.coraShowToast('Add Event drawer coming soon!', 'info')" class="w-full sm:w-auto px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-full transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Event
            </button>
        </div>
    </div>

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
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">1</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">2</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">3</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">4</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">5</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">6</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">7</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">8</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">9</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">10</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">11</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">12</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">13</div>
                    <!-- Today Active Day -->
                    <div class="p-2 bg-zinc-950 text-white font-mono font-bold relative flex flex-col justify-between">
                        <span>14</span>
                        <div class="hidden sm:block space-y-1">
                            <div class="px-1.5 py-0.5 bg-emerald-500 text-[8px] font-sans font-bold text-white rounded-md truncate">Shoot (2)</div>
                        </div>
                    </div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all relative flex flex-col justify-between">
                        <span>15</span>
                        <div class="hidden sm:block space-y-1">
                            <div class="px-1.5 py-0.5 bg-zinc-900 text-[8px] font-sans font-bold text-white rounded-md truncate">Drone (1)</div>
                        </div>
                    </div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">16</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">17</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all relative flex flex-col justify-between">
                        <span>18</span>
                        <div class="hidden sm:block space-y-1">
                            <div class="px-1.5 py-0.5 bg-amber-500 text-[8px] font-sans font-bold text-white rounded-md truncate">Portrait (1)</div>
                        </div>
                    </div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">19</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">20</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all relative flex flex-col justify-between">
                        <span>21</span>
                        <div class="hidden sm:block space-y-1">
                            <div class="px-1.5 py-0.5 bg-zinc-900 text-[8px] font-sans font-bold text-white rounded-md truncate">Audit (1)</div>
                        </div>
                    </div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">22</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">23</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">24</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">25</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">26</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">27</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">28</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">29</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">30</div>
                    <div class="p-2 bg-white font-mono font-bold hover:bg-zinc-50/80 transition-all">31</div>
                </div>
            </div>
        </div>

        <!-- 1/4 Width: Sidebar Upcoming List -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Today's Schedule</span>
                    <span class="text-[10px] font-extrabold font-mono text-zinc-400 bg-zinc-50 px-2 py-0.5 rounded-full">Day 14</span>
                </div>

                <div class="space-y-3">
                    <?php foreach ( $calendar_events as $event ) : 
                        if ($event['day'] === 14) :
                            $st_color = ($event['status_color'] === 'emerald') ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800';
                    ?>
                        <div class="p-3.5 bg-zinc-50/50 hover:bg-zinc-50 rounded-xl border border-zinc-150 transition-all space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 bg-white border border-zinc-200 rounded-md text-[9px] font-bold text-zinc-500 uppercase"><?php echo esc_html($event['type']); ?></span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold <?php echo $st_color; ?>"><?php echo esc_html($event['status']); ?></span>
                            </div>
                            <h4 class="text-xs font-bold text-zinc-950"><?php echo esc_html($event['title']); ?></h4>
                            <p class="text-[11px] text-zinc-500">Client: <strong><?php echo esc_html($event['client']); ?></strong></p>
                            <p class="text-[10px] font-mono text-zinc-400 flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?php echo esc_html($event['time']); ?>
                            </p>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-2xs space-y-4">
                <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider block">Upcoming Events</span>
                <div class="space-y-3">
                    <?php foreach ( $calendar_events as $event ) : 
                        if ($event['day'] !== 14) :
                            $st_color = 'bg-zinc-100 text-zinc-800';
                            if ($event['status_color'] === 'amber') {
                                $st_color = 'bg-amber-100 text-amber-800';
                            }
                    ?>
                        <div class="p-3 bg-zinc-50/50 hover:bg-zinc-50 rounded-xl border border-zinc-150 transition-all flex items-center justify-between gap-2">
                            <div>
                                <h4 class="text-xs font-bold text-zinc-900"><?php echo esc_html($event['title']); ?></h4>
                                <p class="text-[10px] text-zinc-500">Aug <?php echo $event['day']; ?> · <?php echo esc_html($event['client']); ?></p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold <?php echo $st_color; ?>"><?php echo esc_html($event['status']); ?></span>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
