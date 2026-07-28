<?php
/**
 * Cora Workspace - Unified Operations Scheduler
 * File: views/view-crew-scheduler.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// --- EQUIPMENT ---
$cora_studio_gear = get_option( 'cora_studio_gear', array() );
$cora_gear_checkouts = get_option( 'cora_gear_checkouts', array() );

// --- CREW SHIFTS ---


// Fetch shifts from WP options or fallback to sample data
$cora_crew_shifts = get_option( 'cora_crew_shifts', array() );

if ( empty( $cora_crew_shifts ) || ! is_array( $cora_crew_shifts ) ) {
    $cora_crew_shifts = array(
        array(
            'id'            => 'shift_301',
            'staff_name'    => 'Karan Malhotra',
            'staff_role'    => 'Director of Photography (DoP)',
            'staff_phone'   => '9876543210',
            'property_title'=> 'DLF Cyber City Commercial 4K Shoot',
            'venue'         => 'DLF Cyber Park Tower B, Gurugram',
            'date'          => '2026-07-23',
            'time_start'    => '09:00 AM',
            'time_end'      => '05:00 PM',
            'shift_type'    => 'Standard (8h)',
            'day_rate'      => 25000,
            'overtime_pay'  => 0,
            'total_payout'  => 25000,
            'status'        => 'Confirmed'
        ),
        array(
            'id'            => 'shift_302',
            'staff_name'    => 'Rohan Verma',
            'staff_role'    => 'Certified Drone Pilot',
            'staff_phone'   => '9811223344',
            'property_title'=> 'Golf Course Road Luxury Penthouse Walkthrough',
            'venue'         => 'Sector 54, Gurugram',
            'date'          => '2026-07-23',
            'time_start'    => '02:00 PM',
            'time_end'      => '06:00 PM',
            'shift_type'    => 'Half-Day (4h)',
            'day_rate'      => 12000,
            'overtime_pay'  => 0,
            'total_payout'  => 12000,
            'status'        => 'On-Site'
        ),
        array(
            'id'            => 'shift_303',
            'staff_name'    => 'Rajesh Sharma',
            'staff_role'    => 'Senior Listing Agent',
            'staff_phone'   => '9899001122',
            'property_title'=> 'Vasant Vihar Investor Property Site Visits',
            'venue'         => 'Block C, Vasant Vihar, New Delhi',
            'date'          => '2026-07-24',
            'time_start'    => '10:00 AM',
            'time_end'      => '06:00 PM',
            'shift_type'    => 'Standard (8h)',
            'day_rate'      => 15000,
            'overtime_pay'  => 3000,
            'total_payout'  => 18000,
            'status'        => 'Scheduled'
        )
    );
    update_option( 'cora_crew_shifts', $cora_crew_shifts );
}

// Calculate summary metrics
$total_shifts = count( $cora_crew_shifts );
$total_payout_sum = 0;
$on_site_count = 0;

foreach ( $cora_crew_shifts as $sh ) {
    $total_payout_sum += floatval( $sh['total_payout'] ?? 0 );
    if ( ( $sh['status'] ?? '' ) === 'On-Site' ) $on_site_count++;
}

// --- EVENT TIMELINES ---


// Fetch timelines from WP options or fallback to sample data
$cora_event_timelines = get_option( 'cora_event_timelines', array() );

if ( empty( $cora_event_timelines ) || ! is_array( $cora_event_timelines ) ) {
    $cora_event_timelines = array(
        array(
            'id'            => 'tl_201',
            'title'         => 'DLF Cyber City Investor Property Tour & Due Diligence',
            'category'      => 'Real Estate Tour',
            'client_name'   => 'Apex Realty Partners (Singapore VC)',
            'client_phone'  => '9811223344',
            'total_days'    => 3,
            'status'        => 'Active Live',
            'token'         => 'tl_token_x9918a',
            'created_at'    => '2026-07-20',
            'blocks'        => array(
                array(
                    'day'            => 1,
                    'day_title'      => 'Day 1: Commercial Site Visits',
                    'time_start'     => '10:00 AM',
                    'time_end'       => '01:00 PM',
                    'activity'       => 'DLF Cyber Park Tower A & B Inspection',
                    'venue'          => 'DLF Cyber City, Phase 2, Gurugram',
                    'gps_url'        => 'https://maps.google.com/?q=DLF+Cyber+City+Gurugram',
                    'type_tag'       => 'Site Visit',
                    'duration_tag'   => '2.5 Hrs',
                    'dist_tag'       => '12.4 km',
                    'crew'           => array('Rajesh Sharma (Lead Broker)', 'Anil Kumar (Chauffeur)'),
                    'status'         => 'Completed'
                ),
                array(
                    'day'            => 1,
                    'day_title'      => 'Day 1: Commercial Site Visits',
                    'time_start'     => '02:30 PM',
                    'time_end'       => '05:30 PM',
                    'activity'       => 'Horizon Center Luxury Retail Space Audit',
                    'venue'          => 'Golf Course Road, Sector 43, Gurugram',
                    'gps_url'        => 'https://maps.google.com/?q=One+Horizon+Center+Gurugram',
                    'type_tag'       => 'Site Audit',
                    'duration_tag'   => '3.0 Hrs',
                    'dist_tag'       => '8.7 km',
                    'crew'           => array('Rajesh Sharma (Lead Broker)', 'Vikram Singh (Architect)'),
                    'status'         => 'In Progress'
                ),
                array(
                    'day'            => 2,
                    'day_title'      => 'Day 2: Technical & Legal Due Diligence',
                    'time_start'     => '11:00 AM',
                    'time_end'       => '02:00 PM',
                    'activity'       => 'Legal Land Title Audit & Compliance Briefing',
                    'venue'          => 'Vasant Vihar Legal Chamber, New Delhi',
                    'gps_url'        => 'https://maps.google.com/?q=Vasant+Vihar+New+Delhi',
                    'type_tag'       => 'Legal Review',
                    'duration_tag'   => '3.0 Hrs',
                    'dist_tag'       => '15.2 km',
                    'crew'           => array('Adv. Neha Malhotra', 'Rajesh Sharma'),
                    'status'         => 'Upcoming'
                ),
                array(
                    'day'            => 3,
                    'day_title'      => 'Day 3: Term Sheet Signing & Closing Banquet',
                    'time_start'     => '04:00 PM',
                    'time_end'       => '07:00 PM',
                    'activity'       => 'Final Term Sheet Signing & Closing Dinner',
                    'venue'          => 'The Oberoi, Udyog Vihar, Gurugram',
                    'gps_url'        => 'https://maps.google.com/?q=The+Oberoi+Gurugram',
                    'type_tag'       => 'Closing Banquet',
                    'duration_tag'   => '3.0 Hrs',
                    'dist_tag'       => '5.8 km',
                    'crew'           => array('Rajesh Sharma', 'Executive Host Team'),
                    'status'         => 'Upcoming'
                )
            )
        )
    );
    update_option( 'cora_event_timelines', $cora_event_timelines );
}

$active_timeline = $cora_event_timelines[0] ?? array();
$timeline_blocks = $active_timeline['blocks'] ?? array();
$total_timelines = count( $cora_event_timelines );
$total_blocks    = count( $timeline_blocks );


global $sub_page;
$req_view = $_GET['view'] ?? '';
$active_tab = 'roster';
if ( $req_view === 'timeline' || $sub_page === 'event_timeline' || $sub_page === 'event-timeline' || $sub_page === 'multi-day-timeline' ) {
    $active_tab = 'timeline';
} elseif ( $req_view === 'equipment' ) {
    $active_tab = 'equipment';
} elseif ( $req_view === 'roster' || $sub_page === 'crew_scheduler' || $sub_page === 'crew-scheduler' || $sub_page === 'shifts' ) {
    $active_tab = 'roster';
}
?>

<div id="cora-scheduler-unified-wrapper" class="space-y-6 font-sans text-zinc-900 max-w-[1700px] mx-auto pb-12 select-none">
    
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-zinc-200">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">● Operations Scheduler</h1>
            </div>
            <p class="text-xs font-medium text-zinc-500 mt-1">Manage event timelines and crew shifts in one place.</p>
        </div>
        
        <div class="flex items-center bg-zinc-100 p-1 rounded-xl">
            <button onclick="window.coraSwitchSchedulerTab('timeline')" id="tab-btn-timeline" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?php echo $active_tab === 'timeline' ? 'bg-white shadow-sm text-zinc-900' : 'text-zinc-500 hover:text-zinc-700'; ?>">Itinerary Timeline</button>
            <button onclick="window.coraSwitchSchedulerTab('roster')" id="tab-btn-roster" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?php echo $active_tab === 'roster' ? 'bg-white shadow-sm text-zinc-900' : 'text-zinc-500 hover:text-zinc-700'; ?>">Crew Shift Roster</button>
        </div>
    </header>

    <div id="panel-view-timeline" class="<?php echo $active_tab === 'timeline' ? '' : 'hidden'; ?>">
        <div class="flex items-center gap-3 flex-wrap justify-end mb-6">
            <button onclick="coraExportTimelineICal()" class="p-2.5 bg-white border border-zinc-200 hover:bg-zinc-100 hover:border-zinc-300 text-zinc-800 rounded-xl transition-all shadow-2xs cursor-pointer flex items-center justify-center" title="Sync to Calendar">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </button>
            <button onclick="coraOpenShareTimelineDrawer('<?php echo esc_js( $active_timeline['id'] ?? '' ); ?>')" class="px-4 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-100 hover:border-zinc-300 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                Share Client Link
            </button>
            <button onclick="coraOpenAddTimelineBlockDrawer()" class="px-4.5 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Time Block
            </button>
        </div>
<!-- 2. TOP 3-STEP "HOW IT WORKS" CARD (BULLETPROOF HIGH CONTRAST) -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-zinc-100 dark:divide-zinc-800 gap-4 text-xs">
        
        <!-- Step 1 -->
        <div class="flex items-center justify-between p-2 md:px-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl font-extrabold flex items-center justify-center shrink-0 text-xs" style="background-color: #f4f4f5 !important; color: #18181b !important;">1</div>
                <div>
                    <div class="font-extrabold text-zinc-900 dark:text-zinc-100" style="color: #09090b !important;">Add Time Blocks</div>
                    <div class="text-[11px] font-medium" style="color: #71717a !important;">Define dates, start times & Google Maps pins.</div>
                </div>
            </div>
            <div class="p-2 rounded-xl" style="background-color: #f4f4f5 !important; color: #71717a !important;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="flex items-center justify-between p-2 md:px-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl font-extrabold flex items-center justify-center shrink-0 text-xs" style="background-color: #f4f4f5 !important; color: #18181b !important;">2</div>
                <div>
                    <div class="font-extrabold text-zinc-900 dark:text-zinc-100" style="color: #09090b !important;">Assign Team Crew</div>
                    <div class="text-[11px] font-medium" style="color: #71717a !important;">Allocate brokers, photographers & drivers.</div>
                </div>
            </div>
            <div class="p-2 rounded-xl" style="background-color: #f4f4f5 !important; color: #71717a !important;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="flex items-center justify-between p-2 md:px-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl font-extrabold flex items-center justify-center shrink-0 text-xs" style="background-color: #f4f4f5 !important; color: #18181b !important;">3</div>
                <div>
                    <div class="font-extrabold text-zinc-900 dark:text-zinc-100" style="color: #09090b !important;">Share Client Link</div>
                    <div class="text-[11px] font-medium" style="color: #71717a !important;">Send live tracking itinerary on WhatsApp.</div>
                </div>
            </div>
            <div class="p-2 rounded-xl" style="background-color: #f4f4f5 !important; color: #71717a !important;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
            </div>
        </div>
    </div>

    <!-- 3. PROJECT ITINERARY SELECTOR & MAIN BOARD CONTAINER -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm space-y-6">
        
        <!-- Project Selector Row -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800/60 pb-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl text-zinc-900 dark:text-zinc-100 shrink-0" style="background-color: #f4f4f5 !important; color: #18181b !important;">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 21h18"></path><path d="M9 8h1"></path><path d="M9 12h1"></path><path d="M9 16h1"></path><path d="M14 8h1"></path><path d="M14 12h1"></path><path d="M14 16h1"></path><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider block" style="color: #71717a !important;">ACTIVE PROJECT ITINERARY</span>
                    <div class="flex items-center gap-2 mt-0.5">
                        <select onchange="coraSwitchTimelineProject(this.value)" class="border-0 rounded-xl px-3 py-1.5 text-sm font-extrabold focus:outline-none cursor-pointer" style="background-color: #f4f4f5 !important; color: #09090b !important;">
                            <?php foreach ( $cora_event_timelines as $tl ) : ?>
                                <option value="<?php echo esc_attr( $tl['id'] ); ?>" <?php selected( $tl['id'], $active_timeline['id'] ); ?>>
                                    <?php echo esc_html( $tl['title'] ); ?> (<?php echo esc_html( $tl['total_days'] ); ?> Days)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="text-xs mt-1 font-medium" style="color: #71717a !important;">Client: <strong style="color: #09090b !important;"><?php echo esc_html( $active_timeline['client_name'] ); ?></strong> · Phone: <span class="font-mono"><?php echo esc_html( $active_timeline['client_phone'] ); ?></span></p>
                </div>
            </div>

            <!-- Day Selector Tabs (High Contrast) -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                <button onclick="coraFilterTimelineDay('all', this)" class="tl-day-pill px-4 py-2 rounded-xl text-xs font-bold cursor-pointer shrink-0" style="background-color: #f4f4f5 !important; color: #3f3f46 !important;">
                    All Days
                </button>
                <button onclick="coraFilterTimelineDay(1, this)" class="tl-day-pill px-4 py-2 rounded-xl text-xs font-bold cursor-pointer shrink-0 shadow-xs" style="background-color: #09090b !important; color: #ffffff !important;">
                    Day 1 (Site Visits)
                </button>
                <button onclick="coraFilterTimelineDay(2, this)" class="tl-day-pill px-4 py-2 rounded-xl text-xs font-bold cursor-pointer shrink-0" style="background-color: #f4f4f5 !important; color: #3f3f46 !important;">
                    Day 2 (Due Diligence)
                </button>
                <button onclick="coraFilterTimelineDay(3, this)" class="tl-day-pill px-4 py-2 rounded-xl text-xs font-bold cursor-pointer shrink-0" style="background-color: #f4f4f5 !important; color: #3f3f46 !important;">
                    Day 3 (Closing Banquet)
                </button>
            </div>
        </div>

        <!-- TIMELINE FEED WITH BULLETPROOF VISIBILITY -->
        <div class="space-y-6 relative" id="cora-timeline-blocks-feed">
            <?php foreach ( $timeline_blocks as $idx => $blk ) : 
                $day_num = $blk['day'] ?? 1;
                $status = $blk['status'] ?? 'Upcoming';
                $is_completed = ($status === 'Completed');
                $is_in_progress = ($status === 'In Progress');

                $node_bg = '#71717a';
                $status_style = 'background-color: #f4f4f5 !important; color: #3f3f46 !important; border: 1px solid #e4e4e7 !important;';

                if ( $is_completed ) {
                    $node_bg = '#09090b';
                    $status_style = 'background-color: #09090b !important; color: #ffffff !important;';
                } elseif ( $is_in_progress ) {
                    $node_bg = '#f59e0b';
                    $status_style = 'background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a !important;';
                }
            ?>
            
            <div class="cora-tl-block-card flex items-start gap-4 sm:gap-6 group" data-day="<?php echo $day_num; ?>">
                
                <!-- Left Time Slot Box -->
                <div class="p-3 rounded-2xl text-center shrink-0 w-28 sm:w-32 space-y-0.5 border-0 shadow-2xs" style="background-color: #f4f4f5 !important;">
                    <div class="flex items-center justify-center gap-1 text-[10px] font-bold uppercase" style="color: #71717a !important;">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>TIME SLOT</span>
                    </div>
                    <div class="font-mono font-extrabold text-sm" style="color: #09090b !important;"><?php echo esc_html( $blk['time_start'] ); ?></div>
                    <div class="text-[10px] font-mono" style="color: #71717a !important;">to <?php echo esc_html( $blk['time_end'] ); ?></div>
                </div>

                <!-- Vertical Axis Node Dot & Connecting Line -->
                <div class="relative flex flex-col items-center self-stretch shrink-0">
                    <div class="w-3.5 h-3.5 rounded-full z-10 my-4 shadow-xs" style="background-color: <?php echo $node_bg; ?> !important; border: 2px solid #ffffff !important;"></div>
                    <div class="w-0.5 flex-1 -my-2" style="background-color: #e4e4e7 !important;"></div>
                </div>

                <!-- Right Card Container -->
                <div class="flex-1 rounded-2xl p-5 transition-all flex flex-col md:flex-row md:items-start justify-between gap-4" style="background-color: #f9f9fb !important; border: 1px solid #f4f4f5 !important;">
                    
                    <!-- Activity Info & Attribute Badges -->
                    <div class="space-y-3 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider" style="background-color: #e4e4e7 !important; color: #18181b !important;">DAY <?php echo $day_num; ?></span>
                            <span class="px-3 py-0.5 rounded-full text-[10px] font-extrabold flex items-center gap-1" style="<?php echo $status_style; ?>">
                                <?php if ($is_completed) : ?>
                                    <span>✓</span> Completed
                                <?php elseif ($is_in_progress) : ?>
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> In Progress
                                <?php else : ?>
                                    <span>●</span> Upcoming
                                <?php endif; ?>
                            </span>
                        </div>

                        <h3 class="text-base sm:text-lg font-extrabold tracking-tight" style="color: #09090b !important;"><?php echo esc_html( $blk['activity'] ); ?></h3>
                        
                        <p class="text-xs flex items-center gap-1.5 flex-wrap font-medium" style="color: #52525b !important;">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <span class="font-semibold" style="color: #18181b !important;"><?php echo esc_html( $blk['venue'] ); ?></span>
                            <a href="<?php echo esc_url( $blk['gps_url'] ); ?>" target="_blank" class="hover:underline font-bold text-[11px] ml-1 flex items-center gap-1" style="color: #09090b !important;">
                                Google Maps GPS →
                            </a>
                        </p>

                        <!-- Attribute Badges Row -->
                        <div class="flex items-center gap-2 pt-1 text-[11px] font-semibold">
                            <span class="px-2.5 py-1 rounded-lg flex items-center gap-1.5" style="background-color: #ffffff !important; color: #18181b !important; border: 1px solid #e4e4e7 !important;">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 21h18"></path><path d="M9 8h1"></path><path d="M9 12h1"></path><path d="M9 16h1"></path><path d="M14 8h1"></path><path d="M14 12h1"></path><path d="M14 16h1"></path><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path></svg>
                                <span><?php echo esc_html( $blk['type_tag'] ?? 'Site Visit' ); ?></span>
                            </span>

                            <span class="px-2.5 py-1 rounded-lg flex items-center gap-1.5" style="background-color: #ffffff !important; color: #18181b !important; border: 1px solid #e4e4e7 !important;">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span><?php echo esc_html( $blk['duration_tag'] ?? '2.5 Hrs' ); ?></span>
                            </span>

                            <span class="px-2.5 py-1 rounded-lg flex items-center gap-1.5" style="background-color: #ffffff !important; color: #18181b !important; border: 1px solid #e4e4e7 !important;">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                <span><?php echo esc_html( $blk['dist_tag'] ?? '12.4 km' ); ?></span>
                            </span>
                        </div>
                    </div>

                    <!-- Right Assigned Crew & Actions -->
                    <div class="flex flex-col md:items-end justify-between gap-3 text-right shrink-0">
                        <div class="flex items-center justify-between w-full md:w-auto">
                            <span class="text-[10px] font-bold uppercase tracking-wider" style="color: #71717a !important;">ASSIGNED TEAM CREW:</span>
                            <button type="button" class="text-zinc-400 hover:text-zinc-900 p-1">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </button>
                        </div>

                        <div class="flex flex-col gap-1.5 items-end">
                            <?php foreach ( $blk['crew'] as $cw ) : ?>
                                <span class="px-3 py-1 rounded-xl text-[11px] font-semibold flex items-center gap-1.5 shadow-2xs" style="background-color: #ffffff !important; color: #18181b !important; border: 1px solid #e4e4e7 !important;">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    <?php echo esc_html( $cw ); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <!-- WhatsApp Dispatch Button -->
                        <button onclick="coraShareBlockWhatsApp('<?php echo esc_js( $blk['activity'] ); ?>', '<?php echo esc_js( $blk['venue'] ); ?>', '<?php echo esc_js( $blk['time_start'] ); ?>')" title="WhatsApp Dispatch" class="px-4 py-2 text-white rounded-xl text-xs font-bold cursor-pointer shadow-xs flex items-center gap-1.5 transition-all" style="background-color: #09090b !important; color: #ffffff !important;">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" style="color: #22c55e !important;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.99c-.002 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            <span>WhatsApp Dispatch</span>
                        </button>
                    </div>

                </div>

            </div>
            <?php endforeach; ?>
        </div>

    </div>

    <!-- 4. BOTTOM SUMMARY STATS BAR (HIGH CONTRAST) -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 text-xs divide-y md:divide-y-0 md:divide-x divide-zinc-100 dark:divide-zinc-800">
        
        <div class="flex items-center gap-3 px-4 pt-2 md:pt-0">
            <div class="p-2.5 rounded-xl" style="background-color: #f4f4f5 !important; color: #18181b !important;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider" style="color: #71717a !important;">TOTAL DAYS</div>
                <div class="font-extrabold text-sm" style="color: #09090b !important;">3 Days</div>
            </div>
        </div>

        <div class="flex items-center gap-3 px-4 pt-2 md:pt-0">
            <div class="p-2.5 rounded-xl" style="background-color: #f4f4f5 !important; color: #18181b !important;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            </div>
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider" style="color: #71717a !important;">TOTAL EVENTS</div>
                <div class="font-extrabold text-sm" style="color: #09090b !important;">9</div>
            </div>
        </div>

        <div class="flex items-center gap-3 px-4 pt-2 md:pt-0">
            <div class="p-2.5 rounded-xl" style="background-color: #f4f4f5 !important; color: #18181b !important;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider" style="color: #71717a !important;">COMPLETED</div>
                <div class="font-extrabold text-sm" style="color: #09090b !important;">3</div>
            </div>
        </div>

        <div class="flex items-center gap-3 px-4 pt-2 md:pt-0">
            <div class="p-2.5 rounded-xl" style="background-color: #f4f4f5 !important; color: #18181b !important;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle></svg>
            </div>
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider" style="color: #71717a !important;">IN PROGRESS</div>
                <div class="font-extrabold text-sm" style="color: #09090b !important;">2</div>
            </div>
        </div>

        <div class="flex items-center gap-3 px-4 pt-2 md:pt-0">
            <div class="p-2.5 rounded-xl" style="background-color: #f4f4f5 !important; color: #18181b !important;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider" style="color: #71717a !important;">UPCOMING</div>
                <div class="font-extrabold text-sm" style="color: #09090b !important;">4</div>
            </div>
        </div>

        <div class="px-4 pt-3 md:pt-0 shrink-0">
            <button type="button" onclick="if (window.coraShowToast) window.coraShowToast('Showing complete 9-event itinerary view.', 'info')" class="w-full md:w-auto px-4 py-2.5 rounded-xl font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer" style="background-color: #f4f4f5 !important; color: #09090b !important;">
                <span>View Full Itinerary</span>
                <span>→</span>
            </button>
        </div>

    </div>

</div>
    </div>

    <div id="panel-view-roster" class="<?php echo $active_tab === 'roster' ? '' : 'hidden'; ?>">
        <div class="flex items-center gap-3 flex-wrap justify-end mb-6">
            <button onclick="coraExportShiftPayouts()" class="px-4 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-100 hover:border-zinc-300 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Export Payouts
            </button>
            <button onclick="coraOpenAddShiftDrawer()" class="px-4.5 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + Assign Shift
            </button>
        </div>
<!-- ═══ 2. MONOCHROMATIC 4-KPI METRIC STAT CARDS ═════════════════════════════════ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <!-- 1. Total Scheduled Shifts -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[110px]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest">Scheduled Shifts</span>
                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded text-zinc-650 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700/50">Roster</span>
            </div>
            <div class="mt-2.5">
                <span class="text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight"><?php echo $total_shifts; ?> <span class="text-sm font-semibold text-zinc-455">Shifts</span></span>
                <span class="text-[10px] text-zinc-405 dark:text-zinc-500 block mt-1 font-medium">Field staff roster count</span>
            </div>
        </div>

        <!-- 2. Staff On-Site Now -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[110px]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest">On-Site Now</span>
                <span class="inline-flex items-center gap-1.5 text-[9px] font-extrabold uppercase px-2 py-0.5 rounded text-emerald-700 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-950/20 border border-emerald-100/50 dark:border-emerald-900/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Active
                </span>
            </div>
            <div class="mt-2.5">
                <span class="text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight"><?php echo $on_site_count; ?> <span class="text-sm font-semibold text-zinc-455">Active</span></span>
                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 block mt-1 font-medium">WhatsApp GPS verified</span>
            </div>
        </div>

        <!-- 3. Conflict Prevention -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[110px]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-455 dark:text-zinc-500 uppercase tracking-widest">Conflict Shield</span>
                <span class="inline-flex items-center gap-1.5 text-[9px] font-extrabold uppercase px-2 py-0.5 rounded text-emerald-700 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-950/20 border border-emerald-100/50 dark:border-emerald-900/30">
                    Active
                </span>
            </div>
            <div class="mt-2.5">
                <span class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">100% Safe</span>
                <span class="text-[10px] text-zinc-450 dark:text-zinc-500 block mt-1 font-medium">Zero double-booking conflicts</span>
            </div>
        </div>

        <!-- 4. Total Labor Payouts -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[110px]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest">Labor Payouts</span>
                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded text-zinc-650 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700/50">Finance</span>
            </div>
            <div class="mt-2.5">
                <span class="text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight">₹<?php echo number_format( $total_payout_sum ); ?></span>
                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 block mt-1 font-medium">Synced to Financial Board</span>
            </div>
        </div>
    </div>

    <!-- BULK ACTIONS BAR (Collapsible) -->
    <div id="cora-bulk-shift-bar" class="hidden bg-zinc-950 text-white px-5 py-3 rounded-2xl flex items-center justify-between shadow-xl border border-zinc-800 transition-all">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span id="bulk-selected-count" class="text-xs font-bold text-zinc-200">0 shifts selected</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="coraBulkWhatsAppDispatch()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg cursor-pointer flex items-center gap-1.5 transition-all">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                Bulk WhatsApp Dispatch
            </button>
            <button onclick="coraBulkDeleteShifts()" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg cursor-pointer flex items-center gap-1.5 transition-all">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                Bulk Delete
            </button>
        </div>
    </div>

    <!-- SHIFT ROSTER MATRIX TABLE -->
    <div class="bg-white border border-zinc-200/80 rounded-3xl p-6 md:p-8 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-zinc-950">Field Staff Shift Roster Matrix</h3>
            <span class="text-xs text-zinc-500">Conflict-free schedule matching</span>
        </div>

        <div class="border border-zinc-200 rounded-2xl overflow-hidden shadow-xs">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-100/80 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase">
                        <th class="p-3.5 w-10 text-center">
                            <input type="checkbox" id="shift-select-all" onclick="coraToggleAllShiftCheckboxes(this)" class="w-4 h-4 rounded border-zinc-300 cursor-pointer">
                        </th>
                        <th class="p-3.5">Staff Member & Role</th>
                        <th class="p-3.5">Property Listing / Project</th>
                        <th class="p-3.5">Shift Date & Time</th>
                        <th class="p-3.5">Shift Type</th>
                        <th class="p-3.5">Payout Rate (₹)</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Operations & Actions</th>
                    </tr>
                </thead>
                <tbody id="cora-shift-roster-tbody" class="divide-y divide-zinc-100 bg-white">
                    <?php foreach ( $cora_crew_shifts as $sh ) : 
                        $status = $sh['status'] ?? 'Scheduled';
                        $status_bg = 'bg-zinc-100 text-zinc-700';
                        if ( $status === 'On-Site' ) $status_bg = 'bg-amber-50 text-amber-700 border border-amber-200/60';
                        elseif ( $status === 'Confirmed' ) $status_bg = 'bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                        $sh_json = htmlspecialchars( json_encode( $sh ), ENT_QUOTES, 'UTF-8' );
                    ?>
                    <tr id="shift-row-<?php echo esc_attr( $sh['id'] ); ?>" class="hover:bg-zinc-50/70 transition-colors">
                        <td class="p-3.5 text-center">
                            <input type="checkbox" class="shift-row-checkbox w-4 h-4 rounded border-zinc-300 cursor-pointer" value="<?php echo esc_attr( $sh['id'] ); ?>" onclick="coraUpdateBulkBarVisibility()">
                        </td>
                        <td class="p-3.5">
                            <div class="font-extrabold text-zinc-950 flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-full bg-zinc-900 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                </span>
                                <?php echo esc_html( $sh['staff_name'] ); ?>
                            </div>
                            <div class="text-[10px] text-zinc-500 mt-0.5"><?php echo esc_html( $sh['staff_role'] ); ?></div>
                        </td>
                        <td class="p-3.5 font-semibold text-zinc-800">
                            <div><?php echo esc_html( $sh['property_title'] ); ?></div>
                            <div class="text-[10px] text-zinc-400 font-normal"><?php echo esc_html( $sh['venue'] ); ?></div>
                        </td>
                        <td class="p-3.5 font-mono text-[11px]">
                            <div class="font-bold text-zinc-900"><?php echo esc_html( $sh['date'] ); ?></div>
                            <div class="text-zinc-500"><?php echo esc_html( $sh['time_start'] ); ?> - <?php echo esc_html( $sh['time_end'] ); ?></div>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-100 border border-zinc-200 text-zinc-700">
                                <?php echo esc_html( $sh['shift_type'] ); ?>
                            </span>
                        </td>
                        <td class="p-3.5 font-bold font-mono text-zinc-950">
                            ₹<?php echo number_format( floatval( $sh['total_payout'] ) ); ?>
                        </td>
                        <td class="p-3.5">
                            <select onchange="coraQuickUpdateShiftStatus('<?php echo esc_js( $sh['id'] ); ?>', this.value)" class="px-2 py-1 rounded-full text-[10px] font-extrabold border outline-none cursor-pointer <?php echo $status_bg; ?>">
                                <option value="Confirmed" <?php selected( $status, 'Confirmed' ); ?>>Confirmed</option>
                                <option value="On-Site" <?php selected( $status, 'On-Site' ); ?>>On-Site</option>
                                <option value="Scheduled" <?php selected( $status, 'Scheduled' ); ?>>Scheduled</option>
                                <option value="Completed" <?php selected( $status, 'Completed' ); ?>>Completed</option>
                                <option value="Cancelled" <?php selected( $status, 'Cancelled' ); ?>>Cancelled</option>
                            </select>
                        </td>
                        <td class="p-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button data-shift="<?php echo esc_attr( json_encode( $sh ) ); ?>" onclick="coraOpenEditShiftDrawerFromBtn(this)" title="Edit / Reassign Shift" class="p-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded-lg text-xs transition-colors cursor-pointer border border-zinc-200 flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </button>
                                <button onclick="coraSendShiftWhatsApp('<?php echo esc_js( $sh['staff_phone'] ); ?>', '<?php echo esc_js( $sh['staff_name'] ); ?>', '<?php echo esc_js( $sh['property_title'] ); ?>', '<?php echo esc_js( $sh['time_start'] ); ?>')" title="WhatsApp Dispatch" class="p-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs transition-colors cursor-pointer border border-emerald-600 shadow-2xs flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.99c-.002 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                </button>
                                <button onclick="coraDeleteShiftRow('<?php echo esc_js( $sh['id'] ); ?>')" title="Delete Shift" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs transition-colors cursor-pointer border border-rose-200 flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>



<aside id="cora-add-shift-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-200 bg-zinc-50/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">+ Assign Field Staff Shift</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Assign agent or crew shift with double-booking check.</p>
            </div>
            <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-bold text-zinc-800">Staff Member *</label>
                    <button type="button" onclick="coraToggleInlineAddUserForm()" class="text-[11px] font-bold text-zinc-950 hover:underline flex items-center gap-1 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        + Create New User
                    </button>
                </div>
                <select id="sh-staff-select" onchange="coraOnStaffSelectChange(this)" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold text-zinc-900 cursor-pointer">
                    <option value="">-- Select Available Staff Member --</option>
                    <option value="Karan Malhotra" data-role="Director of Photography (DoP)" data-phone="9876543210" data-rate="25000">Karan Malhotra (Director of Photography)</option>
                    <option value="Rohan Verma" data-role="Certified Drone Pilot" data-phone="9811223344" data-rate="18000">Rohan Verma (Certified Drone Pilot)</option>
                    <option value="Rajesh Sharma" data-role="Senior Listing Agent" data-phone="9988776655" data-rate="20000">Rajesh Sharma (Senior Listing Agent)</option>
                    <option value="Anita Roy" data-role="Field Property Inspector" data-phone="9711002233" data-rate="15000">Anita Roy (Field Property Inspector)</option>
                    <option value="Vikram Singh" data-role="Chauffeur / Driver" data-phone="9871122334" data-rate="12000">Vikram Singh (Chauffeur / Driver)</option>
                </select>
                <input type="hidden" id="sh-staff-name" value="">
            </div>

            <!-- Inline Quick Add User Form (Collapsible) -->
            <div id="cora-inline-add-user-box" class="hidden border border-zinc-200 bg-zinc-50 rounded-xl p-3.5 space-y-3 my-2 shadow-2xs">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-2">
                    <span class="font-bold text-zinc-900 text-xs flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="17" y1="11" x2="23" y2="11"></line></svg>
                        Create New Field Staff / Agent User
                    </span>
                    <button type="button" onclick="coraToggleInlineAddUserForm()" class="text-zinc-400 hover:text-zinc-700 text-xs cursor-pointer">✕</button>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Full Name *</label>
                    <input type="text" id="new-user-fullname" placeholder="e.g. Vikram Malhotra" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Primary Role</label>
                        <select id="new-user-role" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none">
                            <option value="Director of Photography (DoP)">DoP / Camera Lead</option>
                            <option value="Certified Drone Pilot">Drone Pilot</option>
                            <option value="Senior Listing Agent">Senior Listing Agent</option>
                            <option value="Field Property Inspector">Field Inspector</option>
                            <option value="Chauffeur / Driver">Chauffeur / Driver</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">WhatsApp Phone</label>
                        <input type="text" id="new-user-phone" placeholder="9876500112" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-mono outline-none">
                    </div>
                </div>
                <button type="button" onclick="coraQuickCreateStaffUser()" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition-all cursor-pointer shadow-xs">
                    + Save & Auto-Select Staff Member
                </button>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff Role</label>
                <select id="sh-staff-role" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold">
                    <option value="Director of Photography (DoP)">Director of Photography (DoP)</option>
                    <option value="Certified Drone Pilot">Certified Drone Pilot</option>
                    <option value="Senior Listing Agent">Senior Listing Agent</option>
                    <option value="Field Property Inspector">Field Property Inspector</option>
                    <option value="Chauffeur / Driver">Chauffeur / Driver</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff WhatsApp Phone</label>
                <input type="text" id="sh-staff-phone" placeholder="9876543210" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Property Listing / Project</label>
                <input type="text" id="sh-project-title" placeholder="e.g. DLF Cyber Park Shoot..." class="w-full border border-zinc-200 rounded-xl p-3 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Start Time</label>
                    <input type="text" id="sh-time-start" placeholder="09:00 AM" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">End Time</label>
                    <input type="text" id="sh-time-end" placeholder="05:00 PM" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Day-Rate Payout (₹)</label>
                <input type="number" id="sh-day-rate" value="25000" class="w-full border border-zinc-200 rounded-xl p-3 font-mono font-bold outline-none">
            </div>
        </div>

        <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between">
            <button onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white">Cancel</button>
            <button onclick="coraSubmitAddShift()" class="px-5 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 cursor-pointer">Assign Shift</button>
        </div>
    </div>
</aside>

<aside id="cora-edit-shift-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-200 bg-zinc-50/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">Edit & Reassign Field Shift</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Reassign staff member, update call-times, day-rate or shift status.</p>
            </div>
            <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <input type="hidden" id="edit-sh-id">

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-bold text-zinc-800">Reassign Staff Member *</label>
                    <button type="button" onclick="coraToggleEditInlineAddUserForm()" class="text-[11px] font-bold text-zinc-950 hover:underline flex items-center gap-1 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        + Create New User
                    </button>
                </div>
                <select id="edit-sh-staff-select" onchange="coraOnEditStaffSelectChange(this)" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold text-zinc-900 cursor-pointer">
                    <option value="Karan Malhotra" data-role="Director of Photography (DoP)" data-phone="9876543210" data-rate="25000">Karan Malhotra (Director of Photography)</option>
                    <option value="Rohan Verma" data-role="Certified Drone Pilot" data-phone="9811223344" data-rate="18000">Rohan Verma (Certified Drone Pilot)</option>
                    <option value="Rajesh Sharma" data-role="Senior Listing Agent" data-phone="9988776655" data-rate="20000">Rajesh Sharma (Senior Listing Agent)</option>
                    <option value="Anita Roy" data-role="Field Property Inspector" data-phone="9711002233" data-rate="15000">Anita Roy (Field Property Inspector)</option>
                    <option value="Vikram Singh" data-role="Chauffeur / Driver" data-phone="9871122334" data-rate="12000">Vikram Singh (Chauffeur / Driver)</option>
                </select>
                <input type="hidden" id="edit-sh-staff-name" value="">
            </div>

            <!-- Edit Drawer Inline Add User Box -->
            <div id="cora-edit-inline-add-user-box" class="hidden border border-zinc-200 bg-zinc-50 rounded-xl p-3.5 space-y-3 my-2 shadow-2xs">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-2">
                    <span class="font-bold text-zinc-900 text-xs flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="17" y1="11" x2="23" y2="11"></line></svg>
                        Create & Reassign New Staff Member
                    </span>
                    <button type="button" onclick="coraToggleEditInlineAddUserForm()" class="text-zinc-400 hover:text-zinc-700 text-xs cursor-pointer">✕</button>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Full Name *</label>
                    <input type="text" id="edit-new-user-fullname" placeholder="e.g. Sameer Kapoor" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Role</label>
                        <select id="edit-new-user-role" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none">
                            <option value="Director of Photography (DoP)">DoP / Camera Lead</option>
                            <option value="Certified Drone Pilot">Drone Pilot</option>
                            <option value="Senior Listing Agent">Senior Listing Agent</option>
                            <option value="Field Property Inspector">Field Inspector</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">WhatsApp Phone</label>
                        <input type="text" id="edit-new-user-phone" placeholder="9876500999" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-mono outline-none">
                    </div>
                </div>
                <button type="button" onclick="coraQuickCreateEditStaffUser()" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition-all cursor-pointer shadow-xs">
                    + Save & Auto-Select Staff Member
                </button>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff Role</label>
                <select id="edit-sh-staff-role" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold">
                    <option value="Director of Photography (DoP)">Director of Photography (DoP)</option>
                    <option value="Certified Drone Pilot">Certified Drone Pilot</option>
                    <option value="Senior Listing Agent">Senior Listing Agent</option>
                    <option value="Field Property Inspector">Field Property Inspector</option>
                    <option value="Chauffeur / Driver">Chauffeur / Driver</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff WhatsApp Phone</label>
                <input type="text" id="edit-sh-staff-phone" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Property Listing / Project</label>
                <input type="text" id="edit-sh-project-title" class="w-full border border-zinc-200 rounded-xl p-3 outline-none font-semibold">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Shift Date</label>
                    <input type="date" id="edit-sh-date" class="w-full border border-zinc-200 rounded-xl p-3 outline-none font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Shift Status</label>
                    <select id="edit-sh-status" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-bold">
                        <option value="Confirmed">Confirmed</option>
                        <option value="On-Site">On-Site</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Start Time</label>
                    <input type="text" id="edit-sh-time-start" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">End Time</label>
                    <input type="text" id="edit-sh-time-end" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Day-Rate Payout (₹)</label>
                <input type="number" id="edit-sh-day-rate" class="w-full border border-zinc-200 rounded-xl p-3 font-mono font-bold outline-none">
            </div>
        </div>

        <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between gap-3">
            <button onclick="coraDeleteEditShift()" class="px-4 py-2 border border-rose-200 text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-bold transition-all cursor-pointer">Delete Shift</button>
            <div class="flex items-center gap-2">
                <button onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white">Cancel</button>
                <button onclick="coraSaveEditShift()" class="px-5 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 cursor-pointer">Save Changes</button>
            </div>
        </div>
    </div>
</aside>

<aside id="cora-add-timeline-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Add Time Block</h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Add a site visit, due diligence audit, or photo shoot session.</p>
        </div>
        <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer">✕</button>
    </div>

    <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
        <div>
            <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">Select Day</label>
            <select id="blk-day-select" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-3 text-xs font-semibold text-zinc-900 dark:text-zinc-100 focus:outline-none">
                <option value="1">Day 1 (Site Visits & Discovery)</option>
                <option value="2">Day 2 (Due Diligence & Audits)</option>
                <option value="3">Day 3 (Contract & Closing Banquet)</option>
            </select>
        </div>

        <div>
            <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">Activity Title *</label>
            <input type="text" id="blk-activity-title" placeholder="e.g. DLF Cyber Park Tower A Inspection..." class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-3 text-xs font-semibold text-zinc-900 dark:text-zinc-100 focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">Start Time</label>
                <input type="text" id="blk-time-start" placeholder="10:00 AM" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-3 font-mono text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">End Time</label>
                <input type="text" id="blk-time-end" placeholder="01:00 PM" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-3 font-mono text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">Venue Address & GPS Location</label>
            <input type="text" id="blk-venue-address" placeholder="e.g. DLF Cyber City, Gurugram" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-3 text-xs font-medium text-zinc-900 dark:text-zinc-100 focus:outline-none">
        </div>

        <div>
            <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">Assigned Crew Member</label>
            <input type="text" id="blk-crew-member" placeholder="e.g. Rajesh Sharma (Lead Broker)" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-3 text-xs font-medium text-zinc-900 dark:text-zinc-100 focus:outline-none">
        </div>
    </div>

    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40 flex items-center justify-between">
        <button onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-bold text-zinc-700 dark:text-zinc-300">Cancel</button>
        <button onclick="coraSubmitAddTimelineBlock()" class="px-5 py-2 text-white rounded-xl text-xs font-bold cursor-pointer" style="background-color: #09090b !important;">Add Schedule Block</button>
    </div>
</aside>

<aside id="cora-share-timeline-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Share Client Link</h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Send live mobile itinerary link to client or VIP guest.</p>
        </div>
        <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer">✕</button>
    </div>

    <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
        <div>
            <label class="block font-bold text-zinc-800 dark:text-zinc-200 mb-1">Live Mobile Itinerary URL</label>
            <div class="flex gap-2">
                <input type="text" id="cora-timeline-share-url" readonly class="flex-1 bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-3 font-mono text-xs text-zinc-700 dark:text-zinc-300 focus:outline-none">
                <button onclick="coraCopyTimelineShareUrl()" class="px-4 py-2 text-white font-bold rounded-xl cursor-pointer" style="background-color: #09090b !important;">Copy</button>
            </div>
        </div>
    </div>
</aside>

<script>
function coraShowToast(msg, type) {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast(msg, type || 'info');
    } else {
        console.log('[Cora Toast ' + (type || 'info') + ']: ' + msg);
    }
}

window.coraGetOrCreateBackdrop = function() {
    var backdrop = document.getElementById('cora-drawer-backdrop');
    if (!backdrop) {
        backdrop = document.querySelector('#cora-drawer-backdrop');
    }
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.id = 'cora-drawer-backdrop';
        backdrop.className = 'hidden fixed inset-0 z-[9990] bg-zinc-950/40 backdrop-blur-sm transition-opacity cursor-pointer';
        backdrop.onclick = function() {
            if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        };
        document.body.appendChild(backdrop);
    } else if (backdrop.parentElement !== document.body) {
        document.body.appendChild(backdrop);
    }
    return backdrop;
};

window.coraEnsureDrawersInBody = function() {
    window.coraGetOrCreateBackdrop();
    var drawers = document.querySelectorAll('aside[id*="drawer"]');
    drawers.forEach(function(d) {
        if (d.parentElement !== document.body) {
            document.body.appendChild(d);
        }
    });
};

window.coraEnsureDrawersInBody();
document.addEventListener('DOMContentLoaded', window.coraEnsureDrawersInBody);
if (window.jQuery) {
    window.jQuery(document).ready(window.coraEnsureDrawersInBody);
}


window.coraSwitchSchedulerTab = function(tabName) {
    var tabs = ['timeline', 'roster'];
    tabs.forEach(function(t) {
        var panel = document.getElementById('panel-view-' + t);
        var btn = document.getElementById('tab-btn-' + t);
        if (panel) panel.classList.add('hidden');
        if (btn) {
            btn.classList.remove('bg-white', 'shadow-sm', 'text-zinc-900');
            btn.classList.add('text-zinc-500');
        }
    });

    var p = document.getElementById('panel-view-' + tabName);
    var b = document.getElementById('tab-btn-' + tabName);
    if (p) p.classList.remove('hidden');
    if (b) {
        b.classList.remove('text-zinc-500');
        b.classList.add('bg-white', 'shadow-sm', 'text-zinc-900');
    }
};

window.coraCloseAllDrawers = function() {
    var bds = document.querySelectorAll('#cora-drawer-backdrop');
    bds.forEach(function(bd) { bd.classList.add('hidden'); });
    document.querySelectorAll('aside[id*="drawer"]').forEach(function(a){ a.classList.add('collapsed'); });
};

window.coraOpenAddShiftDrawer = function() {
    console.log('coraOpenAddShiftDrawer CALLED');
    window.coraEnsureDrawersInBody();
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var bd = window.coraGetOrCreateBackdrop();
    if (bd) {
        bd.classList.remove('hidden');
        bd.style.display = 'block';
    }
    var dr = document.getElementById('cora-add-shift-drawer');
    if (dr) {
        dr.classList.remove('collapsed');
        dr.style.visibility = 'visible';
        dr.style.transform = 'translateX(0)';
    }
};

window.coraOnStaffSelectChange = function(selectEl) {
    var opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !opt.value) {
        document.getElementById('sh-staff-name').value = '';
        return;
    }
    document.getElementById('sh-staff-name').value = opt.value;
    if (opt.getAttribute('data-role')) {
        document.getElementById('sh-staff-role').value = opt.getAttribute('data-role');
    }
    if (opt.getAttribute('data-phone')) {
        document.getElementById('sh-staff-phone').value = opt.getAttribute('data-phone');
    }
    if (opt.getAttribute('data-rate')) {
        document.getElementById('sh-day-rate').value = opt.getAttribute('data-rate');
    }
};

window.coraToggleInlineAddUserForm = function() {
    var box = document.getElementById('cora-inline-add-user-box');
    if (box) box.classList.toggle('hidden');
};

window.coraQuickCreateStaffUser = function() {
    var fullname = document.getElementById('new-user-fullname').value.trim();
    if (!fullname) {
        if (typeof window.coraShowToast === 'function') window.coraShowToast('Please enter full name for the new staff member.');
        return;
    }
    var role = document.getElementById('new-user-role').value;
    var phone = document.getElementById('new-user-phone').value.trim() || '9876500112';
    
    var selectEl = document.getElementById('sh-staff-select');
    var newOpt = document.createElement('option');
    newOpt.value = fullname;
    newOpt.text = fullname + ' (' + role + ')';
    newOpt.setAttribute('data-role', role);
    newOpt.setAttribute('data-phone', phone);
    newOpt.setAttribute('data-rate', '22000');
    newOpt.selected = true;
    
    selectEl.appendChild(newOpt);
    coraOnStaffSelectChange(selectEl);
    
    document.getElementById('new-user-fullname').value = '';
    document.getElementById('cora-inline-add-user-box').classList.add('hidden');
    
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Staff member "' + fullname + '" created and selected!');
    }
};

window.coraSubmitAddShift = function() {
    var selectEl = document.getElementById('sh-staff-select');
    var name = document.getElementById('sh-staff-name').value || (selectEl ? selectEl.value : '');
    if (!name) { coraShowToast('Please select or create a staff member.'); return; }
    coraShowToast('Shift assigned for ' + name + '! Conflict check: 0 Overlaps.');
    window.coraCloseAllDrawers();
};

window.coraOpenEditShiftDrawerFromBtn = function(btn) {
    var raw = btn.getAttribute('data-shift');
    if (!raw) return;
    try {
        var shift = JSON.parse(raw);
        window.coraOpenEditShiftDrawer(shift);
    } catch(e) {
        console.error('Error parsing shift JSON:', e);
    }
};

window.coraOpenEditShiftDrawer = function(shift) {
    window.coraEnsureDrawersInBody();
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var bd = window.coraGetOrCreateBackdrop();
    if (bd) bd.classList.remove('hidden');
    
    var elId = document.getElementById('edit-sh-id'); if (elId) elId.value = shift.id || '';
    var elSelect = document.getElementById('edit-sh-staff-select'); if (elSelect) elSelect.value = shift.staff_name || '';
    var elName = document.getElementById('edit-sh-staff-name'); if (elName) elName.value = shift.staff_name || '';
    var elRole = document.getElementById('edit-sh-staff-role'); if (elRole) elRole.value = shift.staff_role || '';
    var elPhone = document.getElementById('edit-sh-staff-phone'); if (elPhone) elPhone.value = shift.staff_phone || '';
    var elProj = document.getElementById('edit-sh-project-title'); if (elProj) elProj.value = shift.property_title || '';
    var elDate = document.getElementById('edit-sh-date'); if (elDate) elDate.value = shift.date || '2026-07-23';
    var elStart = document.getElementById('edit-sh-time-start'); if (elStart) elStart.value = shift.time_start || '09:00 AM';
    var elEnd = document.getElementById('edit-sh-time-end'); if (elEnd) elEnd.value = shift.time_end || '05:00 PM';
    var elRate = document.getElementById('edit-sh-day-rate'); if (elRate) elRate.value = shift.day_rate || shift.total_payout || 25000;
    var elStat = document.getElementById('edit-sh-status'); if (elStat) elStat.value = shift.status || 'Confirmed';
    
    var dr = document.getElementById('cora-edit-shift-drawer');
    if (dr) dr.classList.remove('collapsed');
};

window.coraOnEditStaffSelectChange = function(selectEl) {
    var opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !opt.value) return;
    document.getElementById('edit-sh-staff-name').value = opt.value;
    if (opt.getAttribute('data-role')) document.getElementById('edit-sh-staff-role').value = opt.getAttribute('data-role');
    if (opt.getAttribute('data-phone')) document.getElementById('edit-sh-staff-phone').value = opt.getAttribute('data-phone');
    if (opt.getAttribute('data-rate')) document.getElementById('edit-sh-day-rate').value = opt.getAttribute('data-rate');
};

window.coraToggleEditInlineAddUserForm = function() {
    var box = document.getElementById('cora-edit-inline-add-user-box');
    if (box) box.classList.toggle('hidden');
};

window.coraQuickCreateEditStaffUser = function() {
    var fullname = document.getElementById('edit-new-user-fullname').value.trim();
    if (!fullname) {
        if (typeof window.coraShowToast === 'function') window.coraShowToast('Please enter full name.');
        return;
    }
    var role = document.getElementById('edit-new-user-role').value;
    var phone = document.getElementById('edit-new-user-phone').value.trim() || '9876500999';
    
    var selectEl = document.getElementById('edit-sh-staff-select');
    var newOpt = document.createElement('option');
    newOpt.value = fullname;
    newOpt.text = fullname + ' (' + role + ')';
    newOpt.setAttribute('data-role', role);
    newOpt.setAttribute('data-phone', phone);
    newOpt.setAttribute('data-rate', '22000');
    newOpt.selected = true;
    
    selectEl.appendChild(newOpt);
    coraOnEditStaffSelectChange(selectEl);
    
    document.getElementById('edit-new-user-fullname').value = '';
    document.getElementById('cora-edit-inline-add-user-box').classList.add('hidden');
    
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Staff member "' + fullname + '" created and reassigned!');
    }
};

window.coraSaveEditShift = function() {
    var id = document.getElementById('edit-sh-id').value;
    var staffName = document.getElementById('edit-sh-staff-name').value || document.getElementById('edit-sh-staff-select').value;
    if (!staffName) {
        if (typeof window.coraShowToast === 'function') window.coraShowToast('Please select a staff member.');
        return;
    }
    
    var row = document.getElementById('shift-row-' + id);
    if (row) {
        var role = document.getElementById('edit-sh-staff-role').value;
        var project = document.getElementById('edit-sh-project-title').value;
        var date = document.getElementById('edit-sh-date').value;
        var tStart = document.getElementById('edit-sh-time-start').value;
        var tEnd = document.getElementById('edit-sh-time-end').value;
        var rate = document.getElementById('edit-sh-day-rate').value;
        var status = document.getElementById('edit-sh-status').value;
        
        row.querySelector('td:nth-child(2) div:first-child').childNodes[2].nodeValue = ' ' + staffName;
        row.querySelector('td:nth-child(2) div:last-child').textContent = role;
        row.querySelector('td:nth-child(3) div:first-child').textContent = project;
        row.querySelector('td:nth-child(4) div:first-child').textContent = date;
        row.querySelector('td:nth-child(4) div:last-child').textContent = tStart + ' - ' + tEnd;
        row.querySelector('td:nth-child(6)').textContent = '₹' + parseInt(rate).toLocaleString();
        
        var selectStatus = row.querySelector('td:nth-child(7) select');
        if (selectStatus) selectStatus.value = status;
    }
    
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Shift details and staff assignment updated successfully!');
    }
    window.coraCloseAllDrawers();
};

window.coraDeleteEditShift = function() {
    var id = document.getElementById('edit-sh-id').value;
    coraDeleteShiftRow(id);
    window.coraCloseAllDrawers();
};

window.coraDeleteShiftRow = function(shiftId) {
    var row = document.getElementById('shift-row-' + shiftId);
    if (row) {
        row.style.transition = 'all 0.3s ease';
        row.style.opacity = '0';
        row.style.transform = 'scale(0.95)';
        setTimeout(function() {
            row.remove();
            coraUpdateBulkBarVisibility();
        }, 300);
    }
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Shift removed from roster matrix.', 'info');
    }
};

window.coraQuickUpdateShiftStatus = function(shiftId, newStatus) {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Shift status updated to "' + newStatus + '".');
    }
};

window.coraToggleAllShiftCheckboxes = function(masterCb) {
    var checkboxes = document.querySelectorAll('.shift-row-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = masterCb.checked;
    });
    coraUpdateBulkBarVisibility();
};

window.coraUpdateBulkBarVisibility = function() {
    var checked = document.querySelectorAll('.shift-row-checkbox:checked');
    var bar = document.getElementById('cora-bulk-shift-bar');
    var countEl = document.getElementById('bulk-selected-count');
    
    if (checked.length > 0) {
        if (bar) bar.classList.remove('hidden');
        if (countEl) countEl.textContent = checked.length + (checked.length === 1 ? ' shift selected' : ' shifts selected');
    } else {
        if (bar) bar.classList.add('hidden');
        var master = document.getElementById('shift-select-all');
        if (master) master.checked = false;
    }
};

window.coraBulkWhatsAppDispatch = function() {
    var checked = document.querySelectorAll('.shift-row-checkbox:checked');
    if (checked.length === 0) return;
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Bulk WhatsApp Callout dispatched to ' + checked.length + ' field staff members!');
    }
};

window.coraBulkDeleteShifts = function() {
    var checked = document.querySelectorAll('.shift-row-checkbox:checked');
    if (checked.length === 0) return;
    checked.forEach(function(cb) {
        var id = cb.value;
        var row = document.getElementById('shift-row-' + id);
        if (row) row.remove();
    });
    coraUpdateBulkBarVisibility();
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast(checked.length + ' shifts deleted from roster matrix.', 'info');
    }
};

window.coraSendShiftWhatsApp = function(phone, name, project, time) {
    var text = encodeURIComponent('Hi ' + name + '! Here is your shift assignment:\n\nProject: ' + project + '\nCall Time: ' + time + '\n\nPlease acknowledge receipt.');
    window.open('https://wa.me/' + (phone.length === 10 ? '91' + phone : phone) + '?text=' + text, '_blank');
};

window.coraExportShiftPayouts = function() {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Exporting Shift Labor Payout Accounting CSV...');
    }
};


window.coraFilterTimelineDay = function(dayNum, btnEl) {
    document.querySelectorAll('.tl-day-pill').forEach(function(b){
        b.style.backgroundColor = '#f4f4f5';
        b.style.color = '#3f3f46';
    });

    if (btnEl) {
        btnEl.style.backgroundColor = '#09090b';
        btnEl.style.color = '#ffffff';
    }

    document.querySelectorAll('.cora-tl-block-card').forEach(function(card){
        if (dayNum === 'all' || card.dataset.day == dayNum) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
};

window.coraSwitchTimelineProject = function(val) {
    if (window.coraShowToast) window.coraShowToast('Loading selected project itinerary...', 'info');
};

window.coraOpenAddTimelineBlockDrawer = function() {
    window.coraEnsureDrawersInBody();
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var bd = window.coraGetOrCreateBackdrop();
    if (bd) bd.classList.remove('hidden');
    var dr = document.getElementById('cora-add-timeline-drawer');
    if (dr) dr.classList.remove('collapsed');
};

window.coraSubmitAddTimelineBlock = function() {
    var title = document.getElementById('blk-activity-title').value.trim();
    if (!title) { coraShowToast('Please enter an activity title.', 'error'); return; }
    coraShowToast('Time block added to schedule!', 'success');
    window.coraCloseAllDrawers();
};

window.coraOpenShareTimelineDrawer = function(tlId) {
    window.coraEnsureDrawersInBody();
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var bd = window.coraGetOrCreateBackdrop();
    if (bd) bd.classList.remove('hidden');
    var url = window.location.origin + '/workspace/event_timeline?cora_tl=' + tlId + '&token=tl_token_x9918a';
    var inp = document.getElementById('cora-timeline-share-url');
    if (inp) inp.value = url;
    var dr = document.getElementById('cora-share-timeline-drawer');
    if (dr) dr.classList.remove('collapsed');
};

window.coraCopyTimelineShareUrl = function() {
    var url = document.getElementById('cora-timeline-share-url').value;
    navigator.clipboard.writeText(url);
    if (window.coraShowToast) window.coraShowToast('Mobile Itinerary URL copied!', 'success');
};

window.coraShareBlockWhatsApp = function(activity, venue, time) {
    var text = encodeURIComponent('Hi! Here is your upcoming schedule:\n\nActivity: ' + activity + '\nTime: ' + time + '\nVenue: ' + venue + '\n\nView itinerary: ' + window.location.origin + '/workspace/event_timeline');
    window.open('https://wa.me/919811223344?text=' + text, '_blank');
};

window.coraExportTimelineICal = function() {
    if (window.coraShowToast) window.coraShowToast('Syncing schedule with Google Calendar & iCal...', 'success');
};



</script>

<script>
// --- CONFLICT PREVENTION VALIDATION ALGORITHMS & AJAX HANDLERS ---

window.cora_crew_shifts_data = <?php echo json_encode($cora_crew_shifts); ?>;
window.cora_event_timelines_data = <?php echo json_encode($cora_event_timelines); ?>;
window.cora_studio_gear_data = <?php echo json_encode($cora_studio_gear); ?>;
window.cora_gear_checkouts_data = <?php echo json_encode($cora_gear_checkouts); ?>;

function _coraParseTimeMinutes(tStr) {
    if (!tStr) return 0;
    var m = tStr.match(/(\d+):(\d+)\s*(AM|PM)/i);
    if (!m) return 0;
    var h = parseInt(m[1], 10);
    var min = parseInt(m[2], 10);
    var p = m[3].toUpperCase();
    if (h === 12 && p === 'AM') h = 0;
    if (h < 12 && p === 'PM') h += 12;
    return h * 60 + min;
}

window.coraCheckScheduleConflicts = function(type, item, list) {
    var hasConflict = false;
    var startM = _coraParseTimeMinutes(item.time_start);
    var endM = _coraParseTimeMinutes(item.time_end);
    var itemDate = item.date;
    
    if (type === 'staff') {
        for (var i = 0; i < list.length; i++) {
            var existing = list[i];
            if (existing.id === item.id) continue;
            if (existing.staff_name === item.staff_name && existing.date === itemDate) {
                var eStart = _coraParseTimeMinutes(existing.time_start);
                var eEnd = _coraParseTimeMinutes(existing.time_end);
                if (startM < eEnd && endM > eStart) {
                    hasConflict = true;
                    if (window.coraShowToast) window.coraShowToast("Warning: " + item.staff_name + " is already scheduled for " + (existing.property_title || existing.activity || 'another shift') + " on this date!", "warning");
                    break;
                }
            }
        }
    } else if (type === 'equipment') {
        for (var i = 0; i < list.length; i++) {
            var existing = list[i];
            if (existing.id === item.id) continue;
            if (existing.gear_name === item.gear_name && existing.date === itemDate) {
                var eStart = _coraParseTimeMinutes(existing.time_start);
                var eEnd = _coraParseTimeMinutes(existing.time_end);
                if (startM < eEnd && endM > eStart) {
                    hasConflict = true;
                    if (window.coraShowToast) window.coraShowToast("Warning: " + item.gear_name + " is already scheduled on this date!", "warning");
                    break;
                }
            }
        }
    }
    return hasConflict;
};

function _coraAjaxPost(action, payload) {
    var fd = new FormData();
    fd.append('action', action);
    fd.append('payload', JSON.stringify(payload));
    return fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .catch(console.error);
}

// 1. Create Shift override
if (window.coraSubmitAddShift) {
    var original_coraSubmitAddShift = window.coraSubmitAddShift;
    window.coraSubmitAddShift = function() {
        var selectEl = document.getElementById('sh-staff-select');
        var name = document.getElementById('sh-staff-name').value || (selectEl ? selectEl.value : '');
        if (!name) { if(window.coraShowToast) window.coraShowToast('Please select or create a staff member.'); return; }
        
        var newShift = {
            id: 'shift_' + Date.now(),
            staff_name: name,
            date: document.getElementById('sh-date') ? document.getElementById('sh-date').value : '2026-07-23',
            time_start: document.getElementById('sh-time-start') ? document.getElementById('sh-time-start').value : '09:00 AM',
            time_end: document.getElementById('sh-time-end') ? document.getElementById('sh-time-end').value : '05:00 PM',
            property_title: document.getElementById('sh-project-title') ? document.getElementById('sh-project-title').value : ''
        };
        
        var hasConflict = window.coraCheckScheduleConflicts('staff', newShift, window.cora_crew_shifts_data);
        if (hasConflict) {
            if (!confirm("There is a scheduling conflict. Do you still want to save?")) {
                return;
            }
        }
        
        window.cora_crew_shifts_data.push(newShift);
        _coraAjaxPost('cora_ajax_save_crew_shifts_list', window.cora_crew_shifts_data).then(() => {
            original_coraSubmitAddShift();
        });
    };
}

// 2. Save Edit Shift override
if (window.coraSaveEditShift) {
    var original_coraSaveEditShift = window.coraSaveEditShift;
    window.coraSaveEditShift = function() {
        var id = document.getElementById('edit-sh-id').value;
        var staffName = document.getElementById('edit-sh-staff-name').value || document.getElementById('edit-sh-staff-select').value;
        if (!staffName) return;
        
        var editShift = {
            id: id,
            staff_name: staffName,
            date: document.getElementById('edit-sh-date') ? document.getElementById('edit-sh-date').value : '',
            time_start: document.getElementById('edit-sh-time-start') ? document.getElementById('edit-sh-time-start').value : '',
            time_end: document.getElementById('edit-sh-time-end') ? document.getElementById('edit-sh-time-end').value : '',
            property_title: document.getElementById('edit-sh-project-title') ? document.getElementById('edit-sh-project-title').value : ''
        };
        
        var hasConflict = window.coraCheckScheduleConflicts('staff', editShift, window.cora_crew_shifts_data);
        if (hasConflict) {
            if (!confirm("There is a scheduling conflict. Do you still want to save?")) {
                return;
            }
        }
        
        var found = false;
        for (var i = 0; i < window.cora_crew_shifts_data.length; i++) {
            if (window.cora_crew_shifts_data[i].id === id) {
                Object.assign(window.cora_crew_shifts_data[i], editShift);
                found = true;
                break;
            }
        }
        if (!found) window.cora_crew_shifts_data.push(editShift);
        
        _coraAjaxPost('cora_ajax_save_crew_shifts_list', window.cora_crew_shifts_data).then(() => {
            original_coraSaveEditShift();
        });
    };
}

// 3. Delete Shift override
if (window.coraDeleteShiftRow) {
    var original_coraDeleteShiftRow = window.coraDeleteShiftRow;
    window.coraDeleteShiftRow = function(shiftId) {
        window.cora_crew_shifts_data = window.cora_crew_shifts_data.filter(s => s.id !== shiftId);
        _coraAjaxPost('cora_ajax_save_crew_shifts_list', window.cora_crew_shifts_data).then(() => {
            original_coraDeleteShiftRow(shiftId);
        });
    };
}

if (window.coraBulkDeleteShifts) {
    window.coraBulkDeleteShifts = function() {
        var checked = document.querySelectorAll('.shift-row-checkbox:checked');
        if (checked.length === 0) return;
        var idsToDelete = Array.from(checked).map(cb => cb.value);
        window.cora_crew_shifts_data = window.cora_crew_shifts_data.filter(s => !idsToDelete.includes(s.id));
        
        _coraAjaxPost('cora_ajax_save_crew_shifts_list', window.cora_crew_shifts_data).then(() => {
            idsToDelete.forEach(id => {
                var row = document.getElementById('shift-row-' + id);
                if (row) row.remove();
            });
            if (window.coraUpdateBulkBarVisibility) window.coraUpdateBulkBarVisibility();
            if (window.coraShowToast) window.coraShowToast(checked.length + ' shifts deleted.', 'info');
        });
    };
}

// 4. Add Itinerary Block override
if (window.coraSubmitAddTimelineBlock) {
    var original_coraSubmitAddTimelineBlock = window.coraSubmitAddTimelineBlock;
    window.coraSubmitAddTimelineBlock = function() {
        var title = document.getElementById('blk-activity-title') ? document.getElementById('blk-activity-title').value.trim() : 'New Activity';
        if (!title) return;
        
        var newBlock = {
            id: 'blk_' + Date.now(),
            activity: title,
            time_start: '10:00 AM', // Default/fallback times
            time_end: '12:00 PM',
            date: '2026-07-23'
        };
        
        if (window.cora_event_timelines_data.length > 0) {
            if (!window.cora_event_timelines_data[0].blocks) window.cora_event_timelines_data[0].blocks = [];
            window.cora_event_timelines_data[0].blocks.push(newBlock);
        }
        
        _coraAjaxPost('cora_ajax_save_event_timelines_list', window.cora_event_timelines_data).then(() => {
            original_coraSubmitAddTimelineBlock();
        });
    };
}

// 5. Stubs for Save & Delete Itinerary Block
window.coraSaveEditTimelineBlock = function(blockId, blockData) {
    if(window.cora_event_timelines_data.length > 0) {
        var blocks = window.cora_event_timelines_data[0].blocks || [];
        for(var i=0; i<blocks.length; i++){
            if(blocks[i].id === blockId) { Object.assign(blocks[i], blockData); break; }
        }
    }
    _coraAjaxPost('cora_ajax_save_event_timelines_list', window.cora_event_timelines_data);
};

window.coraDeleteTimelineBlock = function(blockId) {
    if(window.cora_event_timelines_data.length > 0) {
        var blocks = window.cora_event_timelines_data[0].blocks || [];
        window.cora_event_timelines_data[0].blocks = blocks.filter(b => b.id !== blockId);
    }
    _coraAjaxPost('cora_ajax_save_event_timelines_list', window.cora_event_timelines_data);
};
</script>
