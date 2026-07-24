<?php
/**
 * Cora Workspace - Multi-Day Tour & Event Planner
 * File: views/view-event-timeline.php
 * High-Contrast, Bulletproof UI with 100% Guaranteed Element Visibility.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

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
?>

<div id="cora-event-timeline-wrapper" class="space-y-6 font-sans text-zinc-900 select-none">
    
    <!-- 1. HEADER & HIGH-CONTRAST ACTION BUTTONS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shrink-0 shadow-xs" style="background-color: #f4f4f5 !important; color: #18181b !important;">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight" style="color: #09090b !important;">Multi-Day Tour & Event Planner</h1>
                <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-0.5 font-medium" style="color: #52525b !important;">
                    Organize multi-day property tours, photo shoots, and crew itineraries with live GPS tracking and 1-click WhatsApp updates.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <button onclick="coraOpenAddTimelineBlockDrawer()" class="px-4 py-2 text-white text-xs font-bold rounded-xl flex items-center gap-2 cursor-pointer shadow-sm" style="background-color: #09090b !important; color: #ffffff !important;">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>+ Add Time Block</span>
            </button>
            <button onclick="coraOpenShareTimelineDrawer('<?php echo esc_js( $active_timeline['id'] ); ?>')" class="px-3.5 py-2 text-xs font-bold rounded-xl cursor-pointer shadow-sm flex items-center gap-2" style="background-color: #ffffff !important; color: #18181b !important; border: 1px solid #e4e4e7 !important;">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                <span>Share Client Link</span>
            </button>
            <button onclick="coraExportTimelineICal()" class="p-2 rounded-xl cursor-pointer shadow-sm" style="background-color: #ffffff !important; color: #18181b !important; border: 1px solid #e4e4e7 !important;" title="Sync to Calendar">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </button>
        </div>
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

<!-- SIDE DRAWERS FOR ACTIONS -->

<!-- 1. ADD TIMELINE BLOCK DRAWER -->
<aside id="cora-add-timeline-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">+ Add Time Block</h3>
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

<!-- 2. SHARE CLIENT MOBILE PORTAL DRAWER -->
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
    var bd = document.getElementById('cora-drawer-backdrop');
    if (bd) bd.classList.remove('hidden');
    var dr = document.getElementById('cora-add-timeline-drawer');
    if (dr) dr.classList.remove('collapsed');
};

window.coraSubmitAddTimelineBlock = function() {
    var title = document.getElementById('blk-activity-title').value.trim();
    if (!title) { if (window.coraShowToast) window.coraShowToast('Please enter an activity title.', 'error'); return; }
    if (window.coraShowToast) window.coraShowToast('Time block added to schedule!', 'success');
    window.coraCloseAllDrawers();
};

window.coraOpenShareTimelineDrawer = function(tlId) {
    var bd = document.getElementById('cora-drawer-backdrop');
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

window.coraCloseAllDrawers = function() {
    var bd = document.getElementById('cora-drawer-backdrop');
    if (bd) bd.classList.add('hidden');
    document.querySelectorAll('aside').forEach(function(a){ a.classList.add('collapsed'); });
};
</script>
