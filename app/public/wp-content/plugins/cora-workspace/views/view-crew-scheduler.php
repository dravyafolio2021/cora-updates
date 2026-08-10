<?php
/**
 * Cora Workspace - Unified Operations Scheduler
 * File: views/view-crew-scheduler.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'cora_resolve_staff_display_name' ) ) {
    function cora_resolve_staff_display_name($val) {
        if ( is_numeric($val) || intval($val) > 0 ) {
            $u_info = get_userdata(intval($val));
            if ( $u_info ) {
                return trim($u_info->display_name ?: $u_info->user_login);
            }
            return 'Team Member';
        }
        if ( !empty($val) && is_string($val) && !is_numeric($val) ) {
            return trim($val);
        }
        return 'Team Member';
    }
}

if ( ! function_exists( 'cora_get_valid_import_leads' ) ) {
    function cora_get_valid_import_leads( $active_industry ) {
        $raw = function_exists('cora_db_get_leads') ? cora_db_get_leads() : array();
        $valid = array();
        if ( ! empty( $raw ) && is_array( $raw ) ) {
            foreach ( $raw as $ld ) {
                $fn = trim( $ld['first_name'] ?? '' );
                $ln = trim( $ld['last_name'] ?? '' );
                $name = trim( $fn . ' ' . $ln );
                if ( ! empty( $name ) ) {
                    $valid[] = $ld;
                }
            }
        }
        if ( empty( $valid ) ) {
            if ( $active_industry === 'photography_studio' ) {
                $valid = array(
                    array(
                        'id' => 'lead_101',
                        'first_name' => 'Aarav',
                        'last_name' => 'Kapoor',
                        'property_type' => 'Commercial 4K Video Shoot',
                        'preferred_locations' => 'DLF Cyber Park, Gurugram',
                        'phone' => '9811001122'
                    )
                );
            } else {
                $valid = array(
                    array(
                        'id' => 'lead_101',
                        'first_name' => 'Sunil',
                        'last_name' => 'Bansal',
                        'property_type' => 'Luxury Villa 4BHK Shoot',
                        'preferred_locations' => 'DLF Phase 5, Gurugram',
                        'phone' => '9811001122'
                    )
                );
            }
        }
        return $valid;
    }
}

// --- EQUIPMENT ---
$cora_studio_gear = get_option( 'cora_studio_gear', array() );
$cora_gear_checkouts = get_option( 'cora_gear_checkouts', array() );

// --- CREW SHIFTS ---
$active_industry = cora_get_active_industry();
$shifts_option_key = 'cora_crew_shifts_' . $active_industry;

// Define dynamic terminology based on active industry
$is_studio = ( $active_industry === 'photography_studio' );
$header_title    = $is_studio ? 'Technical Crew & Shoot Assignment Matrix' : 'Field Staff & Property Inspection Matrix';
$header_subtitle = $is_studio ? 'Assign production staff, schedule client shoots, and audit shift labor payouts' : 'Assign field agents, schedule site visits, and audit inspection fees';
$label_project   = $is_studio ? 'Shoot / Production Project' : 'Listing / Property Project';
$label_role      = $is_studio ? 'Production Role' : 'Field Staff Role';
$label_venue     = $is_studio ? 'Studio / Location Venue' : 'Property Address / Location';
$label_payout    = $is_studio ? 'Production Payout' : 'Shift Fee';

// Fetch shifts from WP options or fallback to dynamic CRM bookings + industry sample data
$cora_crew_shifts = get_option( $shifts_option_key, array() );

// Check if shifts exist, or attempt to derive shifts from real CRM Bookings in DB
if ( empty( $cora_crew_shifts ) || ! is_array( $cora_crew_shifts ) ) {
    $db_bookings = function_exists('cora_db_get_bookings') ? cora_db_get_bookings() : array();
    $generated_shifts = array();

    if ( ! empty( $db_bookings ) ) {
        foreach ( $db_bookings as $b_idx => $b ) {
            $crew_list = ! empty( $b['crew'] ) && is_array( $b['crew'] ) ? $b['crew'] : array($b['assigned_agent'] ?: 'Karan Malhotra');
            $pkg_val = intval( preg_replace('/[^0-9]/', '', $b['package_value'] ?? '15000') ) ?: 15000;
            
            foreach ( $crew_list as $c_idx => $staff_name_raw ) {
                $resolved_staff = cora_resolve_staff_display_name( $staff_name_raw );
                $role = $is_studio ? ( $c_idx % 2 === 0 ? 'Director of Photography (DoP)' : 'Lead Commercial Editor' ) : ( $c_idx % 2 === 0 ? 'Real Estate Photographer' : 'Field Property Inspector' );
                $generated_shifts[] = array(
                    'id'            => 'shift_db_' . ($b['id'] ?? ($b_idx + 1)) . '_' . $c_idx,
                    'staff_name'    => $resolved_staff,
                    'staff_role'    => $role,
                    'staff_phone'   => '9876543210',
                    'property_title'=> ($is_studio ? 'Studio Shoot: ' : 'Property Visit: ') . ($b['deal_type'] ?: 'Client Project #' . ($b['id'] ?? 1)),
                    'venue'         => 'Cora Workspace Studio 1, Gurugram',
                    'date'          => $b['date'] ?: date('Y-m-d'),
                    'time_start'    => $b['time'] ?: '09:00 AM',
                    'time_end'      => '05:00 PM',
                    'shift_type'    => 'Standard (8h)',
                    'day_rate'      => $pkg_val,
                    'overtime_pay'  => 0,
                    'total_payout'  => $pkg_val,
                    'status'        => ($b['status'] === 'Completed' || $b['status'] === 'Confirmed') ? $b['status'] : 'Scheduled'
                );
            }
        }
    }

    if ( ! empty( $generated_shifts ) ) {
        $cora_crew_shifts = $generated_shifts;
    } else {
        // Industry-aligned sample fallback data
        if ( $is_studio ) {
            $cora_crew_shifts = array(
                array(
                    'id'            => 'shift_301',
                    'staff_name'    => 'Karan Malhotra',
                    'staff_role'    => 'Director of Photography (DoP)',
                    'staff_phone'   => '9876543210',
                    'property_title'=> 'Studio Shoot: Vogue Brand Campaign',
                    'venue'         => 'Cora Workspace Studio 1, Gurugram',
                    'date'          => '2026-07-23',
                    'time_start'    => '09:00 AM',
                    'time_end'      => '05:00 PM',
                    'shift_type'    => 'Standard (8h)',
                    'day_rate'      => 25000,
                    'overtime_pay'  => 0,
                    'total_payout'  => 25000,
                    'status'        => 'Confirmed'
                )
            );
        } else {
            // Real Estate Mode
            $cora_crew_shifts = array(
                array(
                    'id'            => 'shift_301',
                    'staff_name'    => 'Karan Malhotra',
                    'staff_role'    => 'Real Estate Photographer',
                    'staff_phone'   => '9876543210',
                    'property_title'=> 'Luxury Villa HDR Listing Shoot',
                    'venue'         => 'DLF Phase 5 Villa 42, Gurugram',
                    'date'          => '2026-07-23',
                    'time_start'    => '09:00 AM',
                    'time_end'      => '05:00 PM',
                    'shift_type'    => 'Standard (8h)',
                    'day_rate'      => 25000,
                    'overtime_pay'  => 0,
                    'total_payout'  => 25000,
                    'status'        => 'Confirmed'
                )
            );
        }
    }
    update_option( $shifts_option_key, $cora_crew_shifts );
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
$timelines_option_key = 'cora_event_timelines_' . $active_industry;
$cora_event_timelines = get_option( $timelines_option_key, array() );

// Clean up legacy options if mixed real-estate terms leaked into photography studio mode
if ( $is_studio && ! empty( $cora_event_timelines ) && is_array( $cora_event_timelines ) ) {
    $first_title = $cora_event_timelines[0]['title'] ?? '';
    $first_act = $cora_event_timelines[0]['blocks'][0]['activity'] ?? '';
    if ( strpos( $first_title, 'DLF Cyber' ) !== false || strpos( $first_act, 'DLF Cyber Park' ) !== false || strpos( $first_title, 'Real Estate' ) !== false ) {
        $cora_event_timelines = array(); // Force re-initialization to pure studio dataset
    }
}

// Prepend dynamic DB bookings if present
$db_bookings = function_exists('cora_db_get_bookings') ? cora_db_get_bookings() : array();
if ( ! empty( $db_bookings ) && is_array( $db_bookings ) ) {
    $db_timelines = array();
    foreach ( $db_bookings as $b_idx => $b ) {
        $client_name = $b['client_name'] ?? 'Client Project';
        $title = ! empty($b['property_title']) ? $b['property_title'] : (! empty($b['package_name']) ? $b['package_name'] : ($client_name . ' Campaign Shoot'));
        $venue = ! empty($b['venue']) ? $b['venue'] : (! empty($b['location']) ? $b['location'] : ($is_studio ? 'Cora Production Studio Suite 1, Gurugram' : 'DLF Cyber City, Gurugram'));
        $crew_members = ! empty( $b['crew'] ) && is_array( $b['crew'] ) ? $b['crew'] : array($b['assigned_agent'] ?? 'Karan Malhotra (DoP)');
        
        $db_timelines[] = array(
            'id'            => 'tl_db_' . ($b['id'] ?? ($b_idx + 1)),
            'title'         => $title,
            'category'      => $is_studio ? 'Commercial Production' : 'Real Estate Tour',
            'client_name'   => $client_name,
            'client_phone'  => $b['client_phone'] ?? '9811001122',
            'total_days'    => 3,
            'status'        => 'Active Live',
            'token'         => 'tl_token_db_' . ($b['id'] ?? ($b_idx + 1)),
            'created_at'    => date('Y-m-d'),
            'blocks'        => array(
                array(
                    'day'            => 1,
                    'day_title'      => 'Day 1: Production & Initial Setup',
                    'time_start'     => '10:00 AM',
                    'time_end'       => '01:00 PM',
                    'activity'       => $title . ' - Primary Shoot',
                    'venue'          => $venue,
                    'gps_url'        => 'https://maps.google.com/?q=' . urlencode($venue),
                    'type_tag'       => $is_studio ? 'Studio Production' : 'Site Inspection',
                    'duration_tag'   => '3.0 Hrs',
                    'dist_tag'       => '3.2 km',
                    'crew'           => array_map('cora_resolve_staff_display_name', $crew_members),
                    'status'         => 'Completed'
                ),
                array(
                    'day'            => 1,
                    'day_title'      => 'Day 1: Production & Initial Setup',
                    'time_start'     => '02:30 PM',
                    'time_end'       => '05:30 PM',
                    'activity'       => $title . ' - Drone & Aerial B-Roll',
                    'venue'          => $venue,
                    'gps_url'        => 'https://maps.google.com/?q=' . urlencode($venue),
                    'type_tag'       => 'Drone & B-Roll',
                    'duration_tag'   => '3.0 Hrs',
                    'dist_tag'       => '4.1 km',
                    'crew'           => array_map('cora_resolve_staff_display_name', $crew_members),
                    'status'         => 'In Progress'
                ),
                array(
                    'day'            => 2,
                    'day_title'      => 'Day 2: Client Review & Executive Briefing',
                    'time_start'     => '11:00 AM',
                    'time_end'       => '02:00 PM',
                    'activity'       => $title . ' - Executive Briefing',
                    'venue'          => $venue,
                    'gps_url'        => 'https://maps.google.com/?q=' . urlencode($venue),
                    'type_tag'       => 'Review Session',
                    'duration_tag'   => '3.0 Hrs',
                    'dist_tag'       => '5.0 km',
                    'crew'           => array_map('cora_resolve_staff_display_name', $crew_members),
                    'status'         => 'Upcoming'
                )
            )
        );
    }
    if ( ! empty( $db_timelines ) && empty( $cora_event_timelines ) ) {
        $cora_event_timelines = $db_timelines;
    }
}

if ( empty( $cora_event_timelines ) || ! is_array( $cora_event_timelines ) ) {
    if ( $active_industry === 'photography_studio' ) {
        $cora_event_timelines = array(
            array(
                'id'            => 'tl_201',
                'title'         => 'Vogue Commercial Fashion & Product Media Campaign',
                'category'      => 'Commercial Production',
                'client_name'   => 'Aarav Kapoor (Vogue India)',
                'client_phone'  => '9811001122',
                'total_days'    => 3,
                'status'        => 'Active Live',
                'token'         => 'tl_token_x9918a',
                'created_at'    => '2026-07-20',
                'blocks'        => array(
                    array(
                        'day'            => 1,
                        'day_title'      => 'Day 1: Fashion Lookbook & Main Stage Production',
                        'time_start'     => '10:00 AM',
                        'time_end'       => '01:00 PM',
                        'activity'       => 'Main Stage Fashion Lookbook & 4K Studio Production',
                        'venue'          => 'Cora Production Studio Suite 1, Gurugram',
                        'gps_url'        => 'https://maps.google.com/?q=DLF+Cyber+City+Gurugram',
                        'type_tag'       => 'Studio Production',
                        'duration_tag'   => '3.0 Hrs',
                        'dist_tag'       => '2.4 km',
                        'crew'           => array('Karan Malhotra (DoP)', 'Anil Kumar (Assistant DoP)'),
                        'status'         => 'Completed'
                    ),
                    array(
                        'day'            => 1,
                        'day_title'      => 'Day 1: Fashion Lookbook & Main Stage Production',
                        'time_start'     => '02:30 PM',
                        'time_end'       => '05:30 PM',
                        'activity'       => 'Commercial Product B-Roll & High-Speed Phantom Reel',
                        'venue'          => 'Cora Production Studio Suite 2, Gurugram',
                        'gps_url'        => 'https://maps.google.com/?q=One+Horizon+Center+Gurugram',
                        'type_tag'       => 'Drone & Product Reel',
                        'duration_tag'   => '3.0 Hrs',
                        'dist_tag'       => '4.1 km',
                        'crew'           => array('Rohan Verma (Drone Pilot)', 'Vikram Singh (Spotter)'),
                        'status'         => 'In Progress'
                    ),
                    array(
                        'day'            => 2,
                        'day_title'      => 'Day 2: Corporate Executive Portraiture & Interviews',
                        'time_start'     => '11:00 AM',
                        'time_end'       => '02:00 PM',
                        'activity'       => 'Executive Portraiture and Core Value Video Interviews',
                        'venue'          => 'Apex Corporate Studio Suite, New Delhi',
                        'gps_url'        => 'https://maps.google.com/?q=Noida',
                        'type_tag'       => 'Studio Shoot',
                        'duration_tag'   => '3.0 Hrs',
                        'dist_tag'       => '12.2 km',
                        'crew'           => array('Karan Malhotra (DoP)', 'Rajesh Sharma (Director)'),
                        'status'         => 'Upcoming'
                    ),
                    array(
                        'day'            => 3,
                        'day_title'      => 'Day 3: Editing, Color Grading & Audio Mastering',
                        'time_start'     => '04:00 PM',
                        'time_end'       => '07:00 PM',
                        'activity'       => 'Post-production Editorial Review & 4K Color Grading',
                        'venue'          => 'Cora Edit Suite 3, New Delhi',
                        'gps_url'        => 'https://maps.google.com/?q=New+Delhi',
                        'type_tag'       => 'Post-Production',
                        'duration_tag'   => '3.0 Hrs',
                        'dist_tag'       => '5.8 km',
                        'crew'           => array('Rajesh Sharma (Lead Editor)', 'Sound Design Team'),
                        'status'         => 'Upcoming'
                    )
                )
            )
        );
    } else {
        // Real Estate Mode
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
    }
    update_option( $timelines_option_key, $cora_event_timelines );
}

$active_timeline_id = $_GET['cora_tl'] ?? $_GET['timeline_id'] ?? '';
$active_timeline = null;
if ( ! empty( $active_timeline_id ) ) {
    foreach ( $cora_event_timelines as $tl ) {
        if ( $tl['id'] === $active_timeline_id ) {
            $active_timeline = $tl;
            break;
        }
    }
}
if ( empty( $active_timeline ) ) {
    $active_timeline = $cora_event_timelines[0] ?? array();
}
$timeline_blocks = $active_timeline['blocks'] ?? array();
$total_timelines = count( $cora_event_timelines );
$total_blocks    = count( $timeline_blocks );

$created_date_str = ! empty( $active_timeline['created_at'] ) ? $active_timeline['created_at'] : '2026-07-20';
$base_start_timestamp = strtotime( $created_date_str );
if ( false === $base_start_timestamp || $base_start_timestamp <= 0 ) {
    $base_start_timestamp = time();
}

$unique_days = array();
foreach ( $timeline_blocks as $blk ) {
    $d = intval( $blk['day'] ?? 1 );
    $day_offset = max( 0, $d - 1 );
    $day_ts = strtotime( "+{$day_offset} days", $base_start_timestamp );
    if ( false === $day_ts ) {
        $day_ts = $base_start_timestamp;
    }
    $date_formatted = date( 'd M', $day_ts );
    $unique_days[$d] = array(
        'title' => $blk['day_title'] ?? ($date_formatted . " Production"),
        'date'  => $date_formatted
    );
}
ksort( $unique_days );

$stat_total_days = count( $unique_days );
$stat_total_events = count( $timeline_blocks );
$stat_completed = 0;
$stat_in_progress = 0;
$stat_upcoming = 0;
foreach ( $timeline_blocks as $blk ) {
    $st = $blk['status'] ?? 'Upcoming';
    if ( $st === 'Completed' ) {
        $stat_completed++;
    } elseif ( $st === 'In Progress' ) {
        $stat_in_progress++;
    } else {
        $stat_upcoming++;
    }
}


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
    
    <!-- STANDARD PAGE HEADER -->
    <div class="flex flex-row items-start justify-between gap-4 w-full">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 ">Team Scheduler</h1>
            <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5 sm:mt-1">Manage event timelines and crew shifts in one place.</p>
        </div>
        
        <!-- Header Actions: Timeline Context (visible only when active tab is timeline) -->
        <div id="header-actions-timeline" class="flex items-center gap-1.5 sm:gap-2 shrink-0 <?php echo $active_tab === 'timeline' ? '' : 'hidden'; ?>">
            <button onclick="coraExportTimelineICal()" class="p-2 bg-white border border-zinc-200/80 hover:text-zinc-950 rounded-xl transition-all shadow-2xs cursor-pointer flex items-center justify-center" title="Sync to Calendar">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </button>
            <button onclick="coraOpenShareTimelineDrawer('<?php echo esc_js( $active_timeline['id'] ?? '' ); ?>')" class="px-2.5 py-2 bg-white text-zinc-800 hover:bg-zinc-50 font-semibold rounded-xl text-[10px] sm:text-xs transition-all flex items-center gap-1.5 cursor-pointer border border-zinc-200/80 shadow-2xs">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                <span class="hidden sm:inline">Share Client Link</span>
            </button>
            <button onclick="coraOpenAddTimelineBlockDrawer()" class="px-3 py-2 bg-zinc-950 text-white font-bold rounded-xl text-[10px] sm:text-xs hover:bg-zinc-800 transition-all flex items-center gap-1.5 cursor-pointer shadow-xs">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Add Time Block</span>
            </button>
        </div>
 
        <!-- Header Actions: Roster Context (visible only when active tab is roster) -->
        <div id="header-actions-roster" class="flex items-center gap-1.5 sm:gap-2 shrink-0 <?php echo $active_tab === 'roster' ? '' : 'hidden'; ?>">
            <button onclick="coraOpenAddShiftDrawer()" class="px-3 py-2 bg-zinc-950 text-white font-bold rounded-xl text-[10px] sm:text-xs hover:bg-zinc-800 transition-all flex items-center gap-1.5 cursor-pointer shadow-xs">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Assign Shift</span>
            </button>
        </div>
    </div>

    <!-- OPTIMIZED SEGMENTED TOOLBAR -->
    <div class="bg-white rounded-2xl border border-zinc-200/80 p-2.5 flex flex-wrap items-center justify-between gap-3 shadow-2xs">
        <!-- Left: Tab Switcher pills -->
        <div class="flex items-center gap-1 bg-zinc-100/80 p-1 rounded-xl w-full md:w-auto justify-between shrink-0 max-w-full overflow-x-auto">
            <button onclick="window.coraSwitchSchedulerTab('timeline')" id="tab-btn-timeline" class="cora-scheduler-tab-btn flex-1 md:flex-initial text-center shrink-0 px-3.5 py-1.5 text-xs rounded-lg transition-all cursor-pointer <?php echo $active_tab === 'timeline' ? 'active bg-white text-zinc-950 shadow-2xs font-bold border border-zinc-200/80 ' : 'text-zinc-500 hover:text-zinc-900 font-medium hover:bg-zinc-200/50 '; ?>">Itinerary Timeline</button>
            <button onclick="window.coraSwitchSchedulerTab('roster')" id="tab-btn-roster" class="cora-scheduler-tab-btn flex-1 md:flex-initial text-center shrink-0 px-3.5 py-1.5 text-xs rounded-lg transition-all cursor-pointer <?php echo $active_tab === 'roster' ? 'active bg-white text-zinc-950 shadow-2xs font-bold border border-zinc-200/80 ' : 'text-zinc-500 hover:text-zinc-900 font-medium hover:bg-zinc-200/50 '; ?>">Crew Shift Roster</button>
        </div>
 
        <!-- Right side: active page selectors & info -->
        <!-- Timeline Controls (visible only when timeline view is active) -->
        <div id="toolbar-controls-timeline" class="w-full md:w-auto flex flex-nowrap items-center gap-2.5 justify-start md:justify-end overflow-x-auto max-w-full pb-0.5 md:pb-0 scrollbar-none <?php echo $active_tab === 'timeline' ? '' : 'hidden'; ?>">
            <!-- Duration Span -->
            <div class="flex items-center justify-start gap-1.5 bg-zinc-50 border border-zinc-200/60 rounded-xl px-2.5 py-1 shrink-0" title="Duration Span">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <select id="cora-timeline-span-select" onchange="coraHandleSpanSelectChange(this.value)" class="bg-transparent border-0 outline-none text-xs font-bold focus:outline-none cursor-pointer text-zinc-900 max-w-[68px] sm:max-w-[80px] md:max-w-none py-0.5 w-full">
                    <option value="3" selected>3 Days</option>
                    <option value="6">6 Days</option>
                    <option value="7">7 Days</option>
                    <option value="14">14 Days</option>
                    <option value="30">30 Days</option>
                    <option value="custom">Custom...</option>
                </select>
                <input type="number" id="cora-timeline-custom-span-input" min="1" max="90" placeholder="Days" class="hidden border-0 bg-transparent text-xs font-bold w-12 focus:outline-none text-zinc-900 py-0.5" onchange="coraChangeTimelineSpan(this.value)" onkeyup="coraChangeTimelineSpan(this.value)">
            </div>
            <!-- Active Project -->
            <div class="flex items-center justify-start gap-1.5 bg-zinc-50 border border-zinc-200/60 rounded-xl px-2.5 py-1 shrink-0" title="Active Project">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 shrink-0"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                <select onchange="coraSwitchTimelineProject(this.value)" class="bg-transparent border-0 outline-none text-xs font-bold focus:outline-none cursor-pointer text-zinc-900 max-w-[85px] sm:max-w-[200px] md:max-w-none py-0.5 truncate w-full">
                    <?php foreach ( $cora_event_timelines as $tl ) : ?>
                        <option value="<?php echo esc_attr( $tl['id'] ); ?>" <?php selected( $tl['id'], $active_timeline['id'] ); ?>>
                            <?php echo esc_html( $tl['title'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Client Info Badge -->
            <div id="header-client-badge" class="text-xs font-semibold text-zinc-550 shrink-0 flex items-center justify-start gap-1.5 bg-zinc-50 px-2.5 py-1.5 rounded-xl border border-zinc-200/60 " title="Client Details">
                <div class="flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <strong class="text-zinc-900 font-bold truncate max-w-[65px] sm:max-w-[110px]"><?php echo esc_html( $active_timeline['client_name'] ); ?></strong>
                </div>
                <div class="hidden sm:flex items-center gap-1.5">
                    <span class="text-zinc-300 ">|</span>
                    <span class="font-mono text-zinc-650 "><?php echo esc_html( $active_timeline['client_phone'] ); ?></span>
                </div>
            </div>
        </div>

        <!-- Roster Controls (visible only when roster view is active) -->
        <div id="toolbar-controls-roster" class="w-full md:w-auto flex flex-wrap items-center gap-2.5 justify-start md:justify-end <?php echo $active_tab === 'roster' ? '' : 'hidden'; ?>">
            <div class="text-[11px] font-bold text-zinc-500 bg-zinc-50 px-3 py-1.5 rounded-xl border border-zinc-200/60 flex items-center justify-center md:justify-start gap-1.5 w-full md:w-auto" title="Active View">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 shrink-0"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                <span>Active View: <strong class="text-zinc-900 font-bold">Field Staff Allocation</strong></span>
            </div>
        </div>
    </div>

    <!-- PANEL 1: ITINERARY TIMELINE VIEW -->
    <div id="panel-view-timeline" class="<?php echo $active_tab === 'timeline' ? '' : 'hidden'; ?> space-y-6 flex flex-col">
        
        <!-- Timeline 4-KPI Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 order-2 md:order-none">
            <!-- Card 1: Total Days -->
            <div class="p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200 flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0">
                <div class="flex items-center justify-between text-zinc-500 ">
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 truncate">Total Days</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                </div>
                <div>
                    <div id="kpi-stat-total-days" class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 truncate"><?php echo $stat_total_days; ?> Day<?php echo $stat_total_days === 1 ? '' : 's'; ?></div>
                    <div class="mt-1 hidden sm:block">
                        <span class="text-[9.5px] sm:text-[10px] font-bold text-zinc-650 bg-zinc-50 px-2.5 py-0.5 rounded-full border border-zinc-200/60 inline-flex items-center gap-1.5 truncate max-w-full">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                            Project span
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Events -->
            <div class="p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200 flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0">
                <div class="flex items-center justify-between text-zinc-500 ">
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 truncate">Total Events</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><circle cx="3" cy="6" r="1"></circle><circle cx="3" cy="12" r="1"></circle><circle cx="3" cy="18" r="1"></circle></svg>
                    </div>
                </div>
                <div>
                    <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 truncate"><?php echo $stat_total_events; ?> Events</div>
                    <div class="mt-1 hidden sm:block">
                        <span class="text-[9.5px] sm:text-[10px] font-bold text-zinc-650 bg-zinc-50 px-2.5 py-0.5 rounded-full border border-zinc-200/60 inline-flex items-center gap-1.5 truncate max-w-full">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><circle cx="3" cy="6" r="1"></circle><circle cx="3" cy="12" r="1"></circle></svg>
                            Itinerary checkpoints
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Completed -->
            <div class="p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200 flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0">
                <div class="flex items-center justify-between text-zinc-500 ">
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 truncate">Completed</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                </div>
                <div>
                    <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 truncate"><?php echo $stat_completed; ?> Done</div>
                    <div class="mt-1 hidden sm:block">
                        <span class="text-[9.5px] sm:text-[10px] font-bold text-emerald-700 bg-emerald-50/50 px-2.5 py-0.5 rounded-full border border-emerald-200/50 inline-flex items-center gap-1.5 truncate max-w-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span> Shoot progress
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Remaining -->
            <div class="p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200 flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0">
                <div class="flex items-center justify-between text-zinc-500 ">
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 truncate">Remaining</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                </div>
                <div>
                    <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 truncate"><?php echo $stat_upcoming; ?> Pending</div>
                    <div class="mt-1 hidden sm:block">
                        <span class="text-[9.5px] sm:text-[10px] font-bold text-zinc-650 bg-zinc-50 px-2.5 py-0.5 rounded-full border border-zinc-200/60 inline-flex items-center gap-1.5 truncate max-w-full">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Awaiting execution
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TIMELINE FEED BLOCK CARD CONTAINER -->
        <div class="bg-white rounded-2xl border border-zinc-200/80 p-3 sm:p-5 shadow-xs space-y-4 sm:space-y-6 order-1 md:order-none">
            
            <!-- Day Selector Pills Row -->
            <div class="space-y-3.5 border-b border-zinc-100 pb-4">
                <!-- Row 1: Section Title -->
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 ">Event Itinerary Blocks</h3>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Project checkpoints, locations, and assigned crew schedules</p>
                </div>
                
                <!-- Row 2: Horizontal Scrolling Day Pills -->
                <div class="flex items-center gap-1.5 bg-zinc-100/80 p-1 rounded-xl overflow-x-auto w-fit max-w-full border border-zinc-200/60 shadow-2xs" id="cora-day-pills-container">
                    <button onclick="coraFilterTimelineDay('all', this)" data-day-val="all" class="tl-day-pill px-3 py-1.5 text-xs rounded-lg transition-all cursor-pointer font-bold bg-white text-zinc-950 shadow-2xs border border-zinc-200/80 shrink-0 whitespace-nowrap">
                        All Days
                    </button>
                    <?php foreach ( $unique_days as $day_num => $day_info ) : ?>
                        <button onclick="coraFilterTimelineDay(<?php echo esc_attr( $day_num ); ?>, this)" data-day-val="<?php echo esc_attr( $day_num ); ?>" title="<?php echo esc_attr( $day_info['title'] ); ?>" class="tl-day-pill px-3 py-1.5 text-xs rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 font-semibold hover:bg-zinc-200/50 shrink-0 whitespace-nowrap">
                            <?php echo esc_html( $day_info['date'] ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

        <!-- TIMELINE FEED WITH BULLETPROOF VISIBILITY -->
        <div class="space-y-6 relative" id="cora-timeline-blocks-feed">
            <?php foreach ( $timeline_blocks as $idx => $blk ) : 
                $day_num = $blk['day'] ?? 1;
                $status = $blk['status'] ?? 'Upcoming';
                $is_completed = ($status === 'Completed');
                $is_in_progress = ($status === 'In Progress');

                $node_bg_class = 'bg-zinc-400 ';
                $status_classes = 'bg-zinc-100 text-zinc-700 border border-zinc-200/50 ';

                if ( $is_completed ) {
                    $node_bg_class = 'bg-zinc-950 ';
                    $status_classes = 'bg-emerald-500/10 text-emerald-600 border border-emerald-200 ';
                } elseif ( $is_in_progress ) {
                    $node_bg_class = 'bg-amber-500';
                    $status_classes = 'bg-amber-500/10 text-amber-600 border border-amber-200 ';
                }
            ?>
            
            <div class="cora-tl-block-card flex items-start gap-4 sm:gap-6 group" data-day="<?php echo $day_num; ?>">
                
                <!-- Left Time Slot Box -->
                <div class="hidden sm:block p-3 rounded-2xl text-center shrink-0 w-28 sm:w-32 space-y-0.5 border border-zinc-200/80 shadow-2xs bg-zinc-50 ">
                    <div class="flex items-center justify-center gap-1 text-[9px] font-extrabold uppercase tracking-wider text-zinc-450 ">
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>TIME SLOT</span>
                    </div>
                    <div class="font-mono font-extrabold text-sm text-zinc-900 "><?php echo esc_html( $blk['time_start'] ); ?></div>
                    <div class="text-[10px] font-mono text-zinc-450 ">to <?php echo esc_html( $blk['time_end'] ); ?></div>
                </div>

                <!-- Vertical Axis Node Dot & Connecting Line -->
                <div class="hidden sm:flex relative flex-col items-center self-stretch shrink-0">
                    <div class="w-3.5 h-3.5 rounded-full z-10 my-4 shadow-xs border-2 border-white <?php echo $node_bg_class; ?>"></div>
                    <div class="w-0.5 flex-1 -my-2 bg-zinc-200 "></div>
                </div>

                <!-- Right Card Container -->
                <div class="flex-1 rounded-2xl p-4 sm:p-5 transition-all flex flex-col md:flex-row md:items-start justify-between gap-4 bg-white border border-zinc-200/80 shadow-2xs hover:shadow-xs min-w-0 overflow-hidden">
                    
                    <!-- Activity Info & Attribute Badges -->
                    <div class="space-y-3 flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <?php $blk_date_str = date('d M', strtotime("+ " . ($day_num - 1) . " days", $base_start_timestamp)); ?>
                            <span class="px-2.5 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wider bg-zinc-150 text-zinc-650 border border-zinc-200/60 shrink-0"><?php echo esc_html(strtoupper($blk_date_str)); ?></span>
                            <span class="inline-flex sm:hidden items-center gap-1 px-2.5 py-0.5 rounded-md text-[9px] font-mono font-bold bg-zinc-100 text-zinc-650 border border-zinc-200/50 shrink-0">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?php echo esc_html( $blk['time_start'] ); ?> - <?php echo esc_html( $blk['time_end'] ); ?>
                            </span>
                            <?php $block_id = $blk['id'] ?? ('blk_' . $idx); ?>
                            <div class="flex items-center gap-2">
                                <select onchange="coraChangeBlockStatus('<?php echo esc_js($block_id); ?>', this.value)" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border outline-none cursor-pointer transition-all <?php echo $status_classes; ?>">
                                    <option value="Completed" <?php selected( $status, 'Completed' ); ?>>✓ Completed</option>
                                    <option value="In Progress" <?php selected( $status, 'In Progress' ); ?>>● In Progress</option>
                                    <option value="Upcoming" <?php selected( $status, 'Upcoming' ); ?>>● Upcoming</option>
                                </select>
                                <button onclick="coraDeleteBlockItem('<?php echo esc_js($block_id); ?>')" title="Delete Block" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs transition-colors cursor-pointer border border-rose-200 flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </div>

                        <h3 class="text-base sm:text-lg font-bold tracking-tight text-zinc-900 break-words min-w-0 leading-snug"><?php echo esc_html( $blk['activity'] ); ?></h3>
                        
                        <p class="text-xs flex items-center gap-1.5 flex-wrap font-medium text-zinc-500 ">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <span class="font-bold text-zinc-800 "><?php echo esc_html( $blk['venue'] ); ?></span>
                            <span class="text-zinc-300 ">·</span>
                            <a href="<?php echo esc_url( $blk['gps_url'] ); ?>" target="_blank" class="hover:underline font-bold text-[11px] text-zinc-950 flex items-center gap-1">
                                Google Maps GPS →
                            </a>
                        </p>

                        <!-- Attribute Badges Row -->
                        <div class="flex items-center gap-2 pt-1 text-[10px] font-bold">
                            <span class="px-2.5 py-1 rounded-lg flex items-center gap-1.5 bg-zinc-50 text-zinc-700 border border-zinc-200 ">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 21h18"></path><path d="M9 8h1"></path><path d="M9 12h1"></path><path d="M9 16h1"></path><path d="M14 8h1"></path><path d="M14 12h1"></path><path d="M14 16h1"></path><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path></svg>
                                <span><?php echo esc_html( $blk['type_tag'] ?? 'Site Visit' ); ?></span>
                            </span>

                            <span class="px-2.5 py-1 rounded-lg flex items-center gap-1.5 bg-zinc-50 text-zinc-700 border border-zinc-200 ">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span><?php echo esc_html( $blk['duration_tag'] ?? '2.5 Hrs' ); ?></span>
                            </span>

                            <span class="px-2.5 py-1 rounded-lg flex items-center gap-1.5 bg-zinc-50 text-zinc-700 border border-zinc-200 ">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                <span><?php echo esc_html( $blk['dist_tag'] ?? '12.4 km' ); ?></span>
                            </span>
                        </div>
                    </div>

                    <!-- Right Assigned Crew & Actions -->
                    <div class="flex flex-col md:items-end justify-between gap-3 text-right shrink-0">
                        <div class="flex items-center justify-between w-full md:w-auto">
                            <span class="text-[9.5px] font-extrabold uppercase tracking-wider text-zinc-400 ">ASSIGNED TEAM CREW</span>
                        </div>

                        <div class="flex flex-col gap-1.5 items-end">
                            <?php foreach ( $blk['crew'] as $cw ) : 
                                $cw_initials = strtoupper(substr($cw, 0, 1));
                            ?>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold flex items-center gap-1.5 bg-zinc-50 text-zinc-900 border border-zinc-200 ">
                                    <div class="w-4 h-4 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-[8px] border border-zinc-200/50 shrink-0">
                                        <?php echo esc_html($cw_initials); ?>
                                    </div>
                                    <?php echo esc_html( $cw ); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <!-- WhatsApp Dispatch Button -->
                        <button onclick="coraShareBlockWhatsApp('<?php echo esc_js( $blk['activity'] ); ?>', '<?php echo esc_js( $blk['venue'] ); ?>', '<?php echo esc_js( $blk['time_start'] ); ?>')" title="WhatsApp Dispatch" class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer border border-zinc-200/80 ">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" class="text-emerald-600 "><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.99c-.002 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            <span>Dispatch WhatsApp</span>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div id="panel-view-roster" class="<?php echo $active_tab === 'roster' ? '' : 'hidden'; ?> space-y-6 flex flex-col">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 order-2 md:order-none">
            <!-- Card 1: Scheduled Shifts -->
            <div class="p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0 hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200">
                <div class="flex items-center justify-between text-zinc-500 ">
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 truncate">Scheduled Shifts</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    </div>
                </div>
                <div>
                    <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 truncate"><?php echo $total_shifts; ?> Shifts</div>
                    <div class="mt-1 hidden sm:block">
                        <span class="text-[9.5px] sm:text-[10px] font-bold text-zinc-650 bg-zinc-50 px-2.5 py-0.5 rounded-full border border-zinc-200/60 inline-flex items-center gap-1.5 truncate max-w-full">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            Field staff count
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 2: On-Site Now -->
            <div class="p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0 hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200">
                <div class="flex items-center justify-between text-zinc-500 ">
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 truncate">On-Site Now</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    </div>
                </div>
                <div>
                    <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 truncate"><?php echo $on_site_count; ?> Active</div>
                    <div class="mt-1 hidden sm:block">
                        <span class="text-[9.5px] sm:text-[10px] font-bold text-emerald-700 bg-emerald-50/50 px-2.5 py-0.5 rounded-full border border-emerald-200/60 inline-flex items-center gap-1.5 truncate max-w-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span> GPS Verified
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Conflict Shield -->
            <div class="p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0 hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200">
                <div class="flex items-center justify-between text-zinc-500 ">
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 truncate">Conflict Shield</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-base sm:text-2xl font-black tracking-tight text-emerald-600 truncate">100% Safe</div>
                    <div class="mt-1 hidden sm:block">
                        <span class="text-[9.5px] sm:text-[10px] font-bold text-emerald-700 bg-emerald-50/50 px-2.5 py-0.5 rounded-full border border-emerald-200/60 inline-flex items-center gap-1.5 truncate max-w-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span> Double-booking check
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Labor Payouts -->
            <div class="p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0 hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200">
                <div class="flex items-center justify-between text-zinc-500 ">
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 truncate">Labor Payouts</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center font-extrabold text-xs shrink-0 select-none">
                        ₹
                    </div>
                </div>
                <div>
                    <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 truncate">₹<?php echo number_format( $total_payout_sum ); ?></div>
                    <div class="mt-1 hidden sm:block">
                        <span class="text-[9.5px] sm:text-[10px] font-bold text-zinc-650 bg-zinc-50 px-2.5 py-0.5 rounded-full border border-zinc-200/60 inline-flex items-center gap-1.5 truncate max-w-full">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect><line x1="12" y1="4" x2="12" y2="20"></line></svg>
                            Synced to financials
                        </span>
                    </div>
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

    <!-- SHIFT ROSTER CARDS GRID -->
    <div id="cora-shift-roster-cards-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 order-1 md:order-none">
        <?php foreach ( $cora_crew_shifts as $sh ) : 
            $sh_id = $sh['id'] ?? '';
            $name = cora_resolve_staff_display_name($sh['staff_name'] ?? '');
            $initials = strtoupper(substr($name, 0, 1));
            $role = $sh['staff_role'] ?? 'On-Site Crew';
            $phone = $sh['staff_phone'] ?? '9876543210';
            $project = $sh['property_title'] ?? '';
            $venue = $sh['venue'] ?? '';
            $date = $sh['date'] ?? '';
            $timeStart = $sh['time_start'] ?? '09:00 AM';
            $timeEnd = $sh['time_end'] ?? '05:00 PM';
            $rate = $sh['day_rate'] ?? 25000;
            $status = $sh['status'] ?? 'Confirmed';
            
            // Re-apply status class
            $status_bg = 'bg-zinc-500/10 text-zinc-650 border border-zinc-200 ';
            if ( $status === 'On-Site' || $status === 'In Progress' ) {
                $status_bg = 'bg-amber-500/10 text-amber-600 border border-amber-200 ';
            } elseif ( $status === 'Confirmed' || $status === 'Completed' ) {
                $status_bg = 'bg-emerald-500/10 text-emerald-600 border border-emerald-200 ';
            } elseif ( $status === 'Cancelled' ) {
                $status_bg = 'bg-rose-500/10 text-rose-600 border border-rose-200 ';
            }
            
            $sh_json = htmlspecialchars( json_encode( $sh ), ENT_QUOTES, 'UTF-8' );
        ?>
        <div id="shift-row-<?php echo esc_attr($sh_id); ?>" class="sh-card p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between space-y-4 min-w-0">
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-zinc-100 ">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-bold text-sm shrink-0 border border-zinc-200/50 sh-card-staff-initials">
                        <?php echo esc_html($initials); ?>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-zinc-900 text-sm truncate sh-card-staff-name"><?php echo esc_html($name); ?></h4>
                        <p class="text-[11px] font-semibold text-zinc-450 truncate sh-card-staff-role"><?php echo esc_html($role); ?></p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-zinc-450 block">CALL TIME</span>
                    <span class="text-[11px] font-mono font-bold text-zinc-700 sh-card-time"><?php echo esc_html($timeStart); ?> - <?php echo esc_html($timeEnd); ?></span>
                </div>
            </div>
            <div class="space-y-1.5 min-w-0">
                <div class="text-[9.5px] font-extrabold uppercase tracking-widest text-zinc-450 ">SHOOT / PRODUCTION PROJECT</div>
                <h3 class="font-bold text-zinc-900 text-sm sm:text-base tracking-tight leading-snug break-words sh-card-project-title"><?php echo esc_html($project); ?></h3>
                <div class="flex items-center gap-1.5 text-xs text-zinc-500 font-medium truncate pt-1">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span class="truncate sh-card-venue"><?php echo esc_html($venue ?: 'Location TBD'); ?></span>
                </div>
            </div>
            <div class="pt-3 border-t border-zinc-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-mono font-bold text-zinc-650 sh-card-date"><?php echo esc_html($date); ?></span>
                    <span class="text-xs font-extrabold text-zinc-900 font-mono sh-card-payout">₹<?php echo number_format($rate); ?></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <select onchange="coraQuickUpdateShiftStatus('<?php echo esc_js($sh_id); ?>', this.value)" class="px-2.5 py-1 rounded-full text-[9px] font-bold border outline-none cursor-pointer sh-card-status <?php echo esc_attr($status_bg); ?>">
                        <option value="Confirmed" <?php selected($status, 'Confirmed'); ?>>Confirmed</option>
                        <option value="On-Site" <?php selected($status, 'On-Site'); ?>>On-Site</option>
                        <option value="Scheduled" <?php selected($status, 'Scheduled'); ?>>Scheduled</option>
                        <option value="Completed" <?php selected($status, 'Completed'); ?>>Completed</option>
                        <option value="Cancelled" <?php selected($status, 'Cancelled'); ?>>Cancelled</option>
                    </select>
                    <button data-shift="<?php echo esc_attr($sh_json); ?>" onclick="coraOpenEditShiftDrawerFromBtn(this)" title="Edit / Reassign Shift" class="p-1.5 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 rounded-lg text-xs transition-colors cursor-pointer border border-zinc-200 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M11 4H4a2 2 0 0 1-2 2v14a2 2 0 0 1 2 2h14a2 2 0 0 1 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </button>
                    <button onclick="coraShareBlockWhatsApp('<?php echo esc_js($role); ?>', '<?php echo esc_js($venue); ?>', '<?php echo esc_js($timeStart); ?>', 'https://maps.google.com/?q=<?php echo urlencode($venue); ?>', '<?php echo esc_js($phone); ?>')" title="WhatsApp Dispatch" class="p-1.5 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 rounded-lg text-xs transition-colors cursor-pointer border border-zinc-200 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="text-emerald-600 shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.99c-.002 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </button>
                    <button onclick="coraDeleteShiftRow('<?php echo esc_js($sh_id); ?>')" title="Delete Shift" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs transition-colors cursor-pointer border border-rose-200 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

        <!-- WORKSPACE OWNER OPERATIONAL GUIDANCE BANNER -->
        <div class="bg-zinc-50 border border-zinc-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs space-y-3 mt-6 order-3 md:order-none">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-zinc-950 text-white flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Workspace Owner Crew Management</h4>
                        <p class="text-[11px] text-zinc-500 font-medium">How to manage technical crew allocations, auto-import CRM bookings, and track live payouts</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-zinc-200/60 text-zinc-700 border border-zinc-300/40 ">Operational Guide</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-[11px] pt-1">
                <div class="p-3 bg-white rounded-xl border border-zinc-200/60 space-y-1">
                    <div class="font-bold text-zinc-900 flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded-full bg-zinc-100 text-zinc-800 flex items-center justify-center font-mono text-[9px] font-extrabold">1</span>
                        Assign Staff & Crew
                    </div>
                    <p class="text-zinc-500 text-[10.5px]">Click <strong>"+ Assign Shift"</strong> in the top toolbar to assign DoPs, Drone Pilots, or Editors to shoots.</p>
                </div>
                <div class="p-3 bg-white rounded-xl border border-zinc-200/60 space-y-1">
                    <div class="font-bold text-zinc-900 flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded-full bg-zinc-100 text-zinc-800 flex items-center justify-center font-mono text-[9px] font-extrabold">2</span>
                        Auto-Fill CRM Bookings
                    </div>
                    <p class="text-zinc-500 text-[10.5px]">Use the <strong>Auto-Fill dropdown</strong> inside the drawer to instantly populate venue, date, and client shoot info from active CRM leads.</p>
                </div>
                <div class="p-3 bg-white rounded-xl border border-zinc-200/60 space-y-1">
                    <div class="font-bold text-zinc-900 flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded-full bg-zinc-100 text-zinc-800 flex items-center justify-center font-mono text-[9px] font-extrabold">3</span>
                        Status & WhatsApp Dispatch
                    </div>
                    <p class="text-zinc-500 text-[10.5px]">Track <strong>Confirmed → On-Site → Completed</strong> progress, and send shift briefing alerts directly to WhatsApp.</p>
                </div>
            </div>
        </div>
    </div>
    <aside id="cora-add-shift-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[460px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="p-5 border-b border-zinc-200 bg-zinc-50/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-950 ">Assign Field Staff Shift</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">3-step wizard with CRM auto-fill & conflict check.</p>
            </div>
            <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
        </div>

        <!-- Step Progress Indicator -->
        <div class="px-6 py-3 border-b border-zinc-200/80 bg-white flex items-center justify-between text-[11px] font-bold">
            <div id="cora-step-indicator-1" class="flex items-center gap-1.5 text-zinc-950 font-bold">
                <span class="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold">1</span>
                <span>Crew & Lead</span>
            </div>
            <div class="h-px bg-zinc-200 flex-1 mx-2"></div>
            <div id="cora-step-indicator-2" class="flex items-center gap-1.5 text-zinc-400 ">
                <span class="w-5 h-5 rounded-full bg-zinc-100 text-zinc-600 flex items-center justify-center text-[10px] font-bold">2</span>
                <span>Project & Venue</span>
            </div>
            <div class="h-px bg-zinc-200 flex-1 mx-2"></div>
            <div id="cora-step-indicator-3" class="flex items-center gap-1.5 text-zinc-400 ">
                <span class="w-5 h-5 rounded-full bg-zinc-100 text-zinc-600 flex items-center justify-center text-[10px] font-bold">3</span>
                <span>Time & Payout</span>
            </div>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <?php
            $cora_leads_for_import = cora_get_valid_import_leads( $active_industry );
            ?>

            <!-- STEP 1: CREW & LEAD AUTO-FILL -->
            <div id="cora-shift-step-1" class="space-y-4">
                <div class="bg-zinc-50 p-3.5 rounded-2xl border border-zinc-200/60 shadow-2xs">
                    <label class="font-bold text-zinc-800 mb-1 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-amber-500 shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        <span>Auto-Fill details from CRM Leads</span>
                    </label>
                    <select id="sh-lead-import-select" onchange="coraAutoFillShiftFromLead(this)" class="w-full border border-zinc-200 bg-white rounded-xl p-2.5 outline-none font-semibold text-zinc-900 cursor-pointer text-xs mt-1 shadow-3xs">
                        <option value="">-- Select Active CRM Lead to Import --</option>
                        <?php foreach ( $cora_leads_for_import as $ld ) : 
                            $lead_fn = trim($ld['first_name'] ?? '');
                            $lead_ln = trim($ld['last_name'] ?? '');
                            $lead_name = trim($lead_fn . ' ' . $lead_ln) ?: 'Client Lead';
                            $lead_prop = $ld['property_type'] ?? '';
                            $lead_loc = $ld['preferred_locations'] ?? '';
                            
                            $lead_label = $lead_name;
                            if ($lead_prop) $lead_label .= ' - ' . $lead_prop;
                            if ($lead_loc) $lead_label .= ' (' . $lead_loc . ')';
                            
                            if ($active_industry === 'photography_studio') {
                                $mock_title = $lead_name . ' Brand Media Production';
                                $mock_venue = $lead_loc ? $lead_loc : 'Cora Studio, Gurugram';
                            } else {
                                $mock_title = $lead_prop ? ($lead_prop . ' Site Visit') : ($lead_name . ' Property Tour');
                                $mock_venue = $lead_loc ? $lead_loc : 'Block B, Sector 15, Gurugram';
                            }
                        ?>
                            <option value="<?php echo esc_attr($ld['id']); ?>" 
                                    data-title="<?php echo esc_attr($mock_title); ?>"
                                    data-venue="<?php echo esc_attr($mock_venue); ?>">
                                <?php echo esc_html($lead_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="font-bold text-zinc-800 ">Staff Member *</label>
                        <button type="button" onclick="coraToggleInlineAddUserForm()" class="text-[11px] font-bold text-zinc-950 hover:underline flex items-center gap-1 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Create New Staff</span>
                        </button>
                    </div>
                    <select id="sh-staff-select" onchange="coraOnStaffSelectChange(this)" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold text-zinc-900 cursor-pointer">
                        <option value="">-- Select Available Staff Member --</option>
                        <?php 
                        $wp_users_list = get_users( array( 'number' => 50, 'orderby' => 'display_name' ) );
                        if ( ! empty( $wp_users_list ) ) :
                            foreach ( $wp_users_list as $wp_u ) :
                                $u_name = trim( $wp_u->display_name ?: $wp_u->user_login );
                                $u_role = 'Production Specialist';
                                if ( in_array( 'administrator', (array) $wp_u->roles ) ) {
                                    $u_role = 'Workspace Admin';
                                } elseif ( in_array( 'editor', (array) $wp_u->roles ) ) {
                                    $u_role = 'Lead Video Editor';
                                }
                        ?>
                            <option value="<?php echo esc_attr( $u_name ); ?>" data-role="<?php echo esc_attr( $u_role ); ?>" data-phone="9876543210" data-rate="25000">
                                ★ <?php echo esc_html( $u_name ); ?> (<?php echo esc_html( $u_role ); ?> - System User)
                            </option>
                        <?php 
                            endforeach; 
                        endif; 
                        ?>
                        <?php if ( $active_industry === 'photography_studio' ) : ?>
                            <option value="Karan Malhotra" data-role="Director of Photography (DoP)" data-phone="9876543210" data-rate="25000">Karan Malhotra (Director of Photography)</option>
                            <option value="Rohan Verma" data-role="Certified Drone Pilot" data-phone="9811223344" data-rate="18000">Rohan Verma (Certified Drone Pilot)</option>
                            <option value="Rajesh Sharma" data-role="Lead Video Editor" data-phone="9988776655" data-rate="20000">Rajesh Sharma (Lead Video Editor)</option>
                            <option value="Anita Roy" data-role="Creative Director" data-phone="9711002233" data-rate="15000">Anita Roy (Creative Director)</option>
                            <option value="Vikram Singh" data-role="Production Assistant" data-phone="9871122334" data-rate="12000">Vikram Singh (Production Assistant)</option>
                        <?php else : ?>
                            <option value="Karan Malhotra" data-role="Real Estate Photographer" data-phone="9876543210" data-rate="25000">Karan Malhotra (Real Estate Photographer)</option>
                            <option value="Rohan Verma" data-role="Certified Drone Pilot" data-phone="9811223344" data-rate="18000">Rohan Verma (Certified Drone Pilot)</option>
                            <option value="Rajesh Sharma" data-role="Senior Listing Agent" data-phone="9988776655" data-rate="20000">Rajesh Sharma (Senior Listing Agent)</option>
                            <option value="Anita Roy" data-role="Field Property Inspector" data-phone="9711002233" data-rate="15000">Anita Roy (Field Property Inspector)</option>
                            <option value="Vikram Singh" data-role="Chauffeur / Driver" data-phone="9871122334" data-rate="12000">Vikram Singh (Chauffeur / Driver)</option>
                        <?php endif; ?>
                    </select>
                    <input type="hidden" id="sh-staff-name" value="">
                </div>

                <!-- Inline Quick Add User Form (Collapsible) -->
                <div id="cora-inline-add-user-box" class="hidden border border-zinc-200 bg-zinc-50 rounded-xl p-3.5 space-y-3 my-2 shadow-2xs">
                    <div class="flex items-center justify-between border-b border-zinc-200 pb-2">
                        <span class="font-bold text-zinc-900 text-xs flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="17" y1="11" x2="23" y2="11"></line></svg>
                            <span>Create New Field Staff / Agent User</span>
                        </span>
                        <button type="button" onclick="coraToggleInlineAddUserForm()" class="text-zinc-400 hover:text-zinc-700 text-xs cursor-pointer">✕</button>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Full Name *</label>
                        <input type="text" id="new-user-fullname" placeholder="e.g. Vikram Malhotra" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none text-zinc-900 ">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Primary Role</label>
                            <select id="new-user-role" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none text-zinc-900 ">
                                <?php if ( $active_industry === 'photography_studio' ) : ?>
                                    <option value="Director of Photography (DoP)">DoP / Camera Lead</option>
                                    <option value="Certified Drone Pilot">Drone Pilot</option>
                                    <option value="Lead Video Editor">Video Editor</option>
                                    <option value="Creative Director">Creative Director</option>
                                    <option value="Production Assistant">Production Assistant</option>
                                <?php else : ?>
                                    <option value="Real Estate Photographer">Real Estate Photographer</option>
                                    <option value="Real Estate Drone Pilot">Real Estate Drone Pilot</option>
                                    <option value="Senior Listing Agent">Senior Listing Agent</option>
                                    <option value="Field Property Inspector">Field Inspector</option>
                                    <option value="Chauffeur / Driver">Chauffeur / Driver</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">WhatsApp Phone</label>
                            <input type="text" id="new-user-phone" placeholder="9876500112" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-mono outline-none text-zinc-900 ">
                        </div>
                    </div>
                    <button type="button" onclick="coraQuickCreateStaffUser()" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition-all cursor-pointer shadow-xs flex items-center justify-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Save & Auto-Select Staff Member</span>
                    </button>
                </div>

                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Staff Role</label>
                    <select id="sh-staff-role" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold text-zinc-900 ">
                        <?php if ( $active_industry === 'photography_studio' ) : ?>
                            <option value="Director of Photography (DoP)">Director of Photography (DoP)</option>
                            <option value="Certified Drone Pilot">Certified Drone Pilot</option>
                            <option value="Lead Video Editor">Lead Video Editor</option>
                            <option value="Creative Director">Creative Director</option>
                            <option value="Production Assistant">Production Assistant</option>
                        <?php else : ?>
                            <option value="Real Estate Photographer">Real Estate Photographer</option>
                            <option value="Real Estate Drone Pilot">Real Estate Drone Pilot</option>
                            <option value="Senior Listing Agent">Senior Listing Agent</option>
                            <option value="Field Property Inspector">Field Property Inspector</option>
                            <option value="Chauffeur / Driver">Chauffeur / Driver</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Staff WhatsApp Phone</label>
                    <input type="text" id="sh-staff-phone" placeholder="9876543210" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none bg-white text-zinc-900 ">
                </div>
            </div>

            <!-- STEP 2: SHOOT & LOCATION -->
            <div id="cora-shift-step-2" class="hidden space-y-4">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1"><?php echo esc_html($label_project); ?> *</label>
                    <input type="text" id="sh-project-title" placeholder="e.g. Commercial Media Campaign Shoot..." class="w-full border border-zinc-200 rounded-xl p-3 outline-none bg-white text-zinc-900 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-zinc-800 mb-1"><?php echo esc_html($label_venue); ?></label>
                    <input type="text" id="sh-venue" placeholder="e.g. Cora Studio 1, DLF Cyber City..." class="w-full border border-zinc-200 rounded-xl p-3 outline-none bg-white text-zinc-900 ">
                </div>
            </div>

            <!-- STEP 3: SCHEDULE & PAYOUT FINANCIALS -->
            <div id="cora-shift-step-3" class="hidden space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-zinc-800 mb-1">Shift Date *</label>
                        <input type="date" id="sh-date" class="w-full border border-zinc-200 rounded-xl p-3 outline-none font-semibold text-zinc-900 bg-white ">
                    </div>
                    <div>
                        <label class="block font-bold text-zinc-800 mb-1">Shift Type</label>
                        <select id="sh-type" class="w-full border border-zinc-200 rounded-xl p-3 outline-none font-semibold bg-white text-zinc-900 ">
                            <option value="Standard (8h)">Standard (8h)</option>
                            <option value="Half-Day (4h)">Half-Day (4h)</option>
                            <option value="Overtime (12h)">Overtime (12h)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-zinc-800 mb-1">Start Time</label>
                        <input type="text" id="sh-time-start" value="09:00 AM" placeholder="09:00 AM" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none bg-white text-zinc-900 ">
                    </div>
                    <div>
                        <label class="block font-bold text-zinc-800 mb-1">End Time</label>
                        <input type="text" id="sh-time-end" value="05:00 PM" placeholder="05:00 PM" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none bg-white text-zinc-900 ">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Day-Rate Payout (₹)</label>
                    <input type="number" id="sh-day-rate" value="25000" class="w-full border border-zinc-200 rounded-xl p-3 font-mono font-bold outline-none bg-white text-zinc-900 ">
                </div>
            </div>
        </div>

        <!-- Wizard Navigation Footer -->
        <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between">
            <button type="button" id="cora-btn-shift-back" onclick="coraShiftWizardStep(-1)" class="hidden px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-100 cursor-pointer">
                ← Back
            </button>
            <button type="button" id="cora-btn-shift-cancel" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-100 cursor-pointer">
                Cancel
            </button>

            <button type="button" id="cora-btn-shift-next" onclick="coraShiftWizardStep(1)" class="px-5 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 cursor-pointer flex items-center gap-1.5 shadow-xs">
                <span>Next: Project & Location</span>
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>

            <button type="button" id="cora-btn-shift-submit" onclick="coraSubmitAddShift()" class="hidden px-5 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 cursor-pointer shadow-xs">
                Assign Shift
            </button>
        </div>
    </div>
</aside>

<aside id="cora-edit-shift-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-200 bg-zinc-50/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-950 ">Edit & Reassign Field Shift</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Reassign staff member, update call-times, day-rate or shift status.</p>
            </div>
            <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <input type="hidden" id="edit-sh-id">

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-bold text-zinc-800 ">Reassign Staff Member *</label>
                    <button type="button" onclick="coraToggleEditInlineAddUserForm()" class="text-[11px] font-bold text-zinc-950 hover:underline flex items-center gap-1 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Create New User</span>
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
                    <input type="text" id="edit-new-user-fullname" placeholder="e.g. Sameer Kapoor" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none text-zinc-900 ">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Role</label>
                        <select id="edit-new-user-role" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none text-zinc-900 ">
                            <option value="Director of Photography (DoP)">DoP / Camera Lead</option>
                            <option value="Certified Drone Pilot">Drone Pilot</option>
                            <option value="Senior Listing Agent">Senior Listing Agent</option>
                            <option value="Field Property Inspector">Field Inspector</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">WhatsApp Phone</label>
                        <input type="text" id="edit-new-user-phone" placeholder="9876500999" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-mono outline-none text-zinc-900 ">
                    </div>
                </div>
                <button type="button" onclick="coraQuickCreateEditStaffUser()" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition-all cursor-pointer shadow-xs">
                    + Save & Auto-Select Staff Member
                </button>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff Role</label>
                <select id="edit-sh-staff-role" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold text-zinc-900 ">
                    <option value="Director of Photography (DoP)">Director of Photography (DoP)</option>
                    <option value="Certified Drone Pilot">Certified Drone Pilot</option>
                    <option value="Senior Listing Agent">Senior Listing Agent</option>
                    <option value="Field Property Inspector">Field Property Inspector</option>
                    <option value="Chauffeur / Driver">Chauffeur / Driver</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff WhatsApp Phone</label>
                <input type="text" id="edit-sh-staff-phone" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none bg-white text-zinc-900 ">
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Property Listing / Project</label>
                <input type="text" id="edit-sh-project-title" class="w-full border border-zinc-200 rounded-xl p-3 outline-none font-semibold bg-white text-zinc-900 ">
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Venue / Location</label>
                <input type="text" id="edit-sh-venue" class="w-full border border-zinc-200 rounded-xl p-3 outline-none font-semibold bg-white text-zinc-900 ">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Shift Date</label>
                    <input type="date" id="edit-sh-date" class="w-full border border-zinc-200 rounded-xl p-3 outline-none font-semibold bg-white text-zinc-900 ">
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Shift Status</label>
                    <select id="edit-sh-status" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-bold text-zinc-900 ">
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
                    <input type="text" id="edit-sh-time-start" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none bg-white text-zinc-900 ">
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">End Time</label>
                    <input type="text" id="edit-sh-time-end" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none bg-white text-zinc-900 ">
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Day-Rate Payout (₹)</label>
                <input type="number" id="edit-sh-day-rate" class="w-full border border-zinc-200 rounded-xl p-3 font-mono font-bold outline-none bg-white text-zinc-900 ">
            </div>
        </div>

        <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between gap-3">
            <button onclick="coraDeleteEditShift()" class="px-4 py-2 border border-rose-200 text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-bold transition-all cursor-pointer">Delete Shift</button>
            <div class="flex items-center gap-2">
                <button onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white ">Cancel</button>
                <button onclick="coraSaveEditShift()" class="px-5 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 cursor-pointer">Save Changes</button>
            </div>
        </div>
    </div>
</aside>

<aside id="cora-add-timeline-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-5 border-b border-zinc-200 bg-zinc-50 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 ">Add Time Block</h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Add a site visit, due diligence audit, or photo shoot session.</p>
        </div>
        <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
    </div>

    <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
        <div class="bg-zinc-50 p-3.5 rounded-2xl border border-zinc-200/60 shadow-2xs">
            <label class="font-bold text-zinc-800 mb-1 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-amber-500 shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                <span>Auto-Fill details from CRM Leads</span>
            </label>
            <select id="blk-lead-import-select" onchange="coraAutoFillBlockFromLead(this)" class="w-full border border-zinc-200 bg-white rounded-xl p-2.5 outline-none font-semibold text-zinc-900 cursor-pointer text-xs mt-1 shadow-3xs">
                <option value="">-- Select Active CRM Lead to Import --</option>
                <?php foreach ( $cora_leads_for_import as $ld ) : 
                    $lead_name = trim(($ld['first_name'] ?? '') . ' ' . ($ld['last_name'] ?? ''));
                    $lead_prop = $ld['property_type'] ?? '';
                    $lead_loc = $ld['preferred_locations'] ?? '';
                    $lead_label = $lead_name;
                    if ($lead_prop) $lead_label .= ' - ' . $lead_prop;
                    if ($lead_loc) $lead_label .= ' (' . $lead_loc . ')';
                    
                    // Generate mock values
                    if ($active_industry === 'photography_studio') {
                        $mock_activity = $lead_name . ' Production Shoot - Stage 1';
                        $mock_venue = $lead_loc ? $lead_loc : 'Studio A, Noida';
                    } else {
                        $mock_activity = $lead_name . ' Initial Property Inspection';
                        $mock_venue = $lead_loc ? $lead_loc : 'Block C, Sector 62, Noida';
                    }
                ?>
                    <option value="<?php echo esc_attr($ld['id']); ?>" 
                            data-activity="<?php echo esc_attr($mock_activity); ?>"
                            data-venue="<?php echo esc_attr($mock_venue); ?>">
                        <?php echo esc_html($lead_label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block font-bold text-zinc-800 mb-1">Select Day</label>
            <select id="blk-day-select" class="w-full bg-zinc-50 border-0 rounded-xl p-3 text-xs font-semibold text-zinc-900 focus:outline-none">
                <option value="1">Day 1 (Site Visits & Discovery)</option>
                <option value="2">Day 2 (Due Diligence & Audits)</option>
                <option value="3">Day 3 (Contract & Closing Banquet)</option>
            </select>
        </div>

        <div>
            <label class="block font-bold text-zinc-800 mb-1">Activity Title *</label>
            <input type="text" id="blk-activity-title" placeholder="e.g. DLF Cyber Park Tower A Inspection..." class="w-full bg-zinc-50 border-0 rounded-xl p-3 text-xs font-semibold text-zinc-900 focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-800 mb-1">Start Time</label>
                <input type="text" id="blk-time-start" placeholder="10:00 AM" class="w-full bg-zinc-50 border-0 rounded-xl p-3 font-mono text-xs text-zinc-900 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-zinc-800 mb-1">End Time</label>
                <input type="text" id="blk-time-end" placeholder="01:00 PM" class="w-full bg-zinc-50 border-0 rounded-xl p-3 font-mono text-xs text-zinc-900 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block font-bold text-zinc-800 mb-1">Venue Address & GPS Location</label>
            <input type="text" id="blk-venue-address" placeholder="e.g. DLF Cyber City, Gurugram" class="w-full bg-zinc-50 border-0 rounded-xl p-3 text-xs font-medium text-zinc-900 focus:outline-none">
        </div>

        <div>
            <label class="block font-bold text-zinc-800 mb-1">Assigned Crew Member</label>
            <input type="text" id="blk-crew-member" placeholder="e.g. Rajesh Sharma (Lead Broker)" class="w-full bg-zinc-50 border-0 rounded-xl p-3 text-xs font-medium text-zinc-900 focus:outline-none">
        </div>
    </div>

    <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between">
        <button onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 rounded-xl text-xs font-bold text-zinc-700 ">Cancel</button>
        <button onclick="coraSubmitAddTimelineBlock()" class="px-5 py-2 text-white rounded-xl text-xs font-bold cursor-pointer" style="background-color: #09090b !important;">Add Schedule Block</button>
    </div>
</aside>

<aside id="cora-share-timeline-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-5 border-b border-zinc-200 bg-zinc-50 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 ">Share Client Link</h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Send live mobile itinerary link to client or VIP guest.</p>
        </div>
        <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
    </div>

    <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
        <div>
            <label class="block font-bold text-zinc-800 mb-1">Live Mobile Itinerary URL</label>
            <div class="flex gap-2">
                <input type="text" id="cora-timeline-share-url" readonly class="flex-1 bg-zinc-50 border-0 rounded-xl p-3 font-mono text-xs text-zinc-700 focus:outline-none">
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
        var headerActions = document.getElementById('header-actions-' + t);
        var toolbarControls = document.getElementById('toolbar-controls-' + t);
        
        if (panel) panel.classList.add('hidden');
        if (headerActions) headerActions.classList.add('hidden');
        if (toolbarControls) toolbarControls.classList.add('hidden');
        if (btn) {
            btn.classList.remove('active', 'bg-white', '', 'text-zinc-950', '', 'shadow-2xs', 'font-bold', 'border', 'border-zinc-200/80', '');
            btn.classList.add('text-zinc-500', 'hover:text-zinc-900', '', '', 'font-medium', 'hover:bg-zinc-200/50', '');
        }
    });

    var p = document.getElementById('panel-view-' + tabName);
    var b = document.getElementById('tab-btn-' + tabName);
    var ha = document.getElementById('header-actions-' + tabName);
    var tc = document.getElementById('toolbar-controls-' + tabName);
    
    if (p) p.classList.remove('hidden');
    if (ha) ha.classList.remove('hidden');
    if (tc) tc.classList.remove('hidden');
    if (b) {
        b.classList.remove('text-zinc-500', 'hover:text-zinc-900', '', '', 'font-medium', 'hover:bg-zinc-200/50', '');
        b.classList.add('active', 'bg-white', '', 'text-zinc-950', '', 'shadow-2xs', 'font-bold', 'border', 'border-zinc-200/80', '');
    }

    var clientBadge = document.getElementById('header-client-badge');
    var rosterBadge = document.getElementById('header-roster-badge');
    if (clientBadge) clientBadge.classList.toggle('hidden', tabName !== 'timeline');
    if (rosterBadge) rosterBadge.classList.toggle('hidden', tabName !== 'roster');

    try {
        var u = new URL(window.location.href);
        u.searchParams.set('view', tabName);
        window.history.replaceState(null, '', u.toString());
    } catch(e) {}
};

window.coraAutoFillShiftFromLead = function(selectEl) {
    if (!selectEl) return;
    var opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !opt.value) return;
    
    var title = opt.getAttribute('data-title');
    var venue = opt.getAttribute('data-venue');
    
    var titleInput = document.getElementById('sh-project-title');
    var venueInput = document.getElementById('sh-venue');
    var dateInput = document.getElementById('sh-date');
    
    if (titleInput) titleInput.value = title || '';
    if (venueInput) venueInput.value = venue || '';
    if (dateInput) {
        var today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
    }
    
    if (window.coraShowToast) {
        window.coraShowToast("Imported details from Lead: " + opt.text.split(' - ')[0], "success");
    }
};

window.coraAutoFillBlockFromLead = function(selectEl) {
    if (!selectEl) return;
    var opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !opt.value) return;
    
    var activity = opt.getAttribute('data-activity');
    var venue = opt.getAttribute('data-venue');
    
    var activityInput = document.getElementById('blk-activity-title');
    var venueInput = document.getElementById('blk-venue-address');
    
    if (activityInput) activityInput.value = activity || '';
    if (venueInput) venueInput.value = venue || '';
    
    if (window.coraShowToast) {
        window.coraShowToast("Imported details from Lead: " + opt.text.split(' - ')[0], "success");
    }
};

window.coraCloseAllDrawers = function() {
    var bds = document.querySelectorAll('#cora-drawer-backdrop');
    bds.forEach(function(bd) { bd.classList.add('hidden'); });
    document.querySelectorAll('aside[id*="drawer"]').forEach(function(a){ a.classList.add('collapsed'); });
};

window.coraOpenDrawer = function(dr) {
    if (!dr) return;
    if (window.coraDrawerCloseTimer) clearTimeout(window.coraDrawerCloseTimer);
    dr.classList.remove('collapsed', 'hidden', 'translate-x-full', 'pointer-events-none');
    dr.style.visibility = 'visible';
    dr.style.transform = 'translateX(0)';
    dr.style.display = 'flex';
    if (window.jQuery) {
        window.jQuery(dr).removeClass('collapsed hidden translate-x-full pointer-events-none');
    }
};

window.coraCurrentShiftStep = 1;

window.coraShiftWizardStep = function(dir) {
    var target = window.coraCurrentShiftStep + dir;
    if (target < 1 || target > 3) return;

    if (dir > 0 && window.coraCurrentShiftStep === 1) {
        var staffSelect = document.getElementById('sh-staff-select');
        var staffName = document.getElementById('sh-staff-name') ? document.getElementById('sh-staff-name').value : (staffSelect ? staffSelect.value : '');
        if (!staffName) {
            if (typeof window.coraShowToast === 'function') window.coraShowToast('Please select a staff member before proceeding.');
            return;
        }
    }

    window.coraCurrentShiftStep = target;

    var step1 = document.getElementById('cora-shift-step-1');
    var step2 = document.getElementById('cora-shift-step-2');
    var step3 = document.getElementById('cora-shift-step-3');

    if (step1) step1.classList.toggle('hidden', target !== 1);
    if (step2) step2.classList.toggle('hidden', target !== 2);
    if (step3) step3.classList.toggle('hidden', target !== 3);

    for (var i = 1; i <= 3; i++) {
        var ind = document.getElementById('cora-step-indicator-' + i);
        if (!ind) continue;
        var badge = ind.querySelector('span:first-child');
        if (i === target) {
            ind.className = 'flex items-center gap-1.5 text-zinc-950 font-bold';
            if (badge) badge.className = 'w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold';
        } else if (i < target) {
            ind.className = 'flex items-center gap-1.5 text-emerald-600 font-bold';
            if (badge) badge.className = 'w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold';
        } else {
            ind.className = 'flex items-center gap-1.5 text-zinc-400 ';
            if (badge) badge.className = 'w-5 h-5 rounded-full bg-zinc-100 text-zinc-600 flex items-center justify-center text-[10px] font-bold';
        }
    }

    var btnBack = document.getElementById('cora-btn-shift-back');
    var btnCancel = document.getElementById('cora-btn-shift-cancel');
    var btnNext = document.getElementById('cora-btn-shift-next');
    var btnSubmit = document.getElementById('cora-btn-shift-submit');

    if (btnBack) btnBack.classList.toggle('hidden', target === 1);
    if (btnCancel) btnCancel.classList.toggle('hidden', target !== 1);
    if (btnSubmit) btnSubmit.classList.toggle('hidden', target !== 3);

    if (btnNext) {
        btnNext.classList.toggle('hidden', target === 3);
        var nextSpan = btnNext.querySelector('span');
        if (nextSpan) {
            nextSpan.textContent = target === 1 ? 'Next: Location & Details →' : 'Next: Call-Time & Payout →';
        }
    }
};

window.coraOpenAddShiftDrawer = function() {
    window.coraCurrentShiftStep = 1;
    if (typeof coraShiftWizardStep === 'function') coraShiftWizardStep(0);
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
        window.coraOpenDrawer(dr);
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
    var name = document.getElementById('sh-staff-name') ? document.getElementById('sh-staff-name').value : '';
    if (!name && selectEl) name = selectEl.value;
    if (!name) { 
        if (typeof window.coraShowToast === 'function') window.coraShowToast('Please select or create a staff member.', 'error');
        return; 
    }
    
    var role = document.getElementById('sh-staff-role') ? document.getElementById('sh-staff-role').value : 'Production Staff';
    var phone = document.getElementById('sh-staff-phone') ? document.getElementById('sh-staff-phone').value : '9876543210';
    var project = document.getElementById('sh-project-title') ? document.getElementById('sh-project-title').value : 'Media Campaign Shoot';
    var venue = document.getElementById('sh-venue') ? document.getElementById('sh-venue').value : 'Cora Workspace Studio 1, Gurugram';
    var date = document.getElementById('sh-date') ? document.getElementById('sh-date').value : new Date().toISOString().split('T')[0];
    var timeStart = document.getElementById('sh-time-start') ? document.getElementById('sh-time-start').value : '09:00 AM';
    var timeEnd = document.getElementById('sh-time-end') ? document.getElementById('sh-time-end').value : '05:00 PM';
    var rate = document.getElementById('sh-day-rate') ? document.getElementById('sh-day-rate').value : '20000';
    
    var newId = 'shift_dyn_' + Date.now();
    var grid = document.getElementById('cora-shift-roster-cards-grid');
    if (grid) {
        var card = document.createElement('div');
        card.id = 'shift-row-' + newId;
        card.className = 'sh-card p-3.5 sm:p-5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between space-y-4 min-w-0';
        
        var initials = name.charAt(0).toUpperCase();
        var shiftObj = {
            id: newId,
            staff_name: name,
            staff_role: role,
            staff_phone: phone,
            property_title: project,
            venue: venue,
            date: date,
            time_start: timeStart,
            time_end: timeEnd,
            day_rate: rate,
            total_payout: rate,
            status: 'Confirmed'
        };
        var shiftJson = JSON.stringify(shiftObj).replace(/"/g, '&quot;');
        
        card.innerHTML = '\
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-zinc-100 ">\
                <div class="flex items-center gap-3 min-w-0">\
                    <div class="w-8 h-8 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-bold text-sm shrink-0 border border-zinc-200/50 sh-card-staff-initials">\
                        ' + initials + '\
                    </div>\
                    <div class="min-w-0">\
                        <h4 class="font-bold text-zinc-900 text-sm truncate sh-card-staff-name">' + name + '</h4>\
                        <p class="text-[11px] font-semibold text-zinc-450 truncate sh-card-staff-role">' + role + '</p>\
                    </div>\
                </div>\
                <div class="text-right shrink-0">\
                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-zinc-400 block">CALL TIME</span>\
                    <span class="text-[11px] font-mono font-bold text-zinc-700 sh-card-time">' + timeStart + ' - ' + timeEnd + '</span>\
                </div>\
            </div>\
            <div class="space-y-1.5 min-w-0">\
                <div class="text-[9.5px] font-extrabold uppercase tracking-widest text-zinc-400 ">SHOOT / PRODUCTION PROJECT</div>\
                <h3 class="font-bold text-zinc-900 text-sm sm:text-base tracking-tight leading-snug break-words sh-card-project-title">' + project + '</h3>\
                <div class="flex items-center gap-1.5 text-xs text-zinc-500 font-medium truncate pt-1">\
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>\
                    <span class="truncate sh-card-venue">' + venue + '</span>\
                </div>\
            </div>\
            <div class="pt-3 border-t border-zinc-100 flex items-center justify-between gap-3">\
                <div class="flex items-center gap-3">\
                    <span class="text-xs font-mono font-bold text-zinc-650 sh-card-date">' + date + '</span>\
                    <span class="text-xs font-extrabold text-zinc-900 font-mono sh-card-payout">₹' + parseInt(rate).toLocaleString() + '</span>\
                </div>\
                <div class="flex items-center gap-1.5">\
                    <select onchange="coraQuickUpdateShiftStatus(\'' + newId + '\', this.value)" class="px-2.5 py-1 rounded-full text-[9px] font-bold border outline-none cursor-pointer sh-card-status bg-emerald-500/10 text-emerald-600 border-emerald-200 ">\
                        <option value="Confirmed" selected>Confirmed</option>\
                        <option value="On-Site">On-Site</option>\
                        <option value="Scheduled">Scheduled</option>\
                        <option value="Completed">Completed</option>\
                        <option value="Cancelled">Cancelled</option>\
                    </select>\
                    <button data-shift="' + shiftJson + '" onclick="coraOpenEditShiftDrawerFromBtn(this)" title="Edit / Reassign Shift" class="p-1.5 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 rounded-lg text-xs transition-colors cursor-pointer border border-zinc-200 flex items-center justify-center">\
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M11 4H4a2 2 0 0 1-2 2v14a2 2 0 0 1 2 2h14a2 2 0 0 1 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>\
                    </button>\
                    <button onclick="coraShareBlockWhatsApp(\'' + role + '\', \'' + venue + '\', \'' + timeStart + '\', \'https://maps.google.com/?q=' + encodeURIComponent(venue) + '\', \'' + phone + '\')" title="WhatsApp Dispatch" class="p-1.5 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 rounded-lg text-xs transition-colors cursor-pointer border border-zinc-200 flex items-center justify-center">\
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="text-emerald-600 shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.99c-.002 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>\
                    </button>\
                    <button onclick="coraDeleteShiftRow(\'' + newId + '\')" title="Delete Shift" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs transition-colors cursor-pointer border border-rose-200 flex items-center justify-center">\
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>\
                    </button>\
                </div>\
            </div>\
        ';
        grid.prepend(card);
    }
    
    if (typeof _coraAjaxPost === 'function') {
        _coraAjaxPost('cora_ajax_save_crew_shift', {
            shift_id: newId,
            staff_name: name,
            staff_role: role,
            staff_phone: phone,
            project_title: project,
            venue: venue,
            date: date,
            time_start: timeStart,
            time_end: timeEnd,
            day_rate: rate,
            status: 'Confirmed'
        });
    }
    
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Shift assigned and added to canvas live!', 'success');
    }
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
    var elVenue = document.getElementById('edit-sh-venue'); if (elVenue) elVenue.value = shift.venue || '';
    var elDate = document.getElementById('edit-sh-date'); if (elDate) elDate.value = shift.date || '2026-07-23';
    var elStart = document.getElementById('edit-sh-time-start'); if (elStart) elStart.value = shift.time_start || '09:00 AM';
    var elEnd = document.getElementById('edit-sh-time-end'); if (elEnd) elEnd.value = shift.time_end || '05:00 PM';
    var elRate = document.getElementById('edit-sh-day-rate'); if (elRate) elRate.value = shift.day_rate || shift.total_payout || 25000;
    var elStat = document.getElementById('edit-sh-status'); if (elStat) elStat.value = shift.status || 'Confirmed';
    
    var dr = document.getElementById('cora-edit-shift-drawer');
    if (dr) {
        window.coraOpenDrawer(dr);
    }
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
        var venue = document.getElementById('edit-sh-venue') ? document.getElementById('edit-sh-venue').value : '';
        var date = document.getElementById('edit-sh-date').value;
        var tStart = document.getElementById('edit-sh-time-start').value;
        var tEnd = document.getElementById('edit-sh-time-end').value;
        var rate = document.getElementById('edit-sh-day-rate').value;
        var status = document.getElementById('edit-sh-status').value;
        
        // Update Card UI elements if present
        var cardStaffName = row.querySelector('.sh-card-staff-name');
        if (cardStaffName) cardStaffName.textContent = staffName;
        
        var cardStaffInit = row.querySelector('.sh-card-staff-initials');
        if (cardStaffInit && staffName) cardStaffInit.textContent = staffName.charAt(0).toUpperCase();
        
        var cardStaffRole = row.querySelector('.sh-card-staff-role');
        if (cardStaffRole) cardStaffRole.textContent = role;
        
        var cardProj = row.querySelector('.sh-card-project-title');
        if (cardProj) cardProj.textContent = project;
        
        var cardVenue = row.querySelector('.sh-card-venue');
        if (cardVenue) cardVenue.textContent = venue || 'Location TBD';
        
        var cardDate = row.querySelector('.sh-card-date span:last-child') || row.querySelector('.sh-card-date');
        if (cardDate) cardDate.textContent = date;
        
        var cardTime = row.querySelector('.sh-card-time');
        if (cardTime) cardTime.textContent = tStart + ' - ' + tEnd;
        
        var cardPayout = row.querySelector('.sh-card-payout');
        if (cardPayout) cardPayout.textContent = '₹' + parseInt(rate).toLocaleString();
        
        var selectStatus = row.querySelector('.sh-card-status');
        if (selectStatus) {
            selectStatus.value = status;
            
            // Re-apply status class
            selectStatus.className = selectStatus.className.replace(/bg-\w+-500\/10 text-\w+-\d+.*border-\w+-\d+/, '');
            var status_bg = 'bg-zinc-500/10 text-zinc-650 border border-zinc-200 ';
            if ( status === 'On-Site' || status === 'In Progress' ) {
                status_bg = 'bg-amber-500/10 text-amber-600 border border-amber-200 ';
            } else if ( status === 'Confirmed' || status === 'Completed' ) {
                status_bg = 'bg-emerald-500/10 text-emerald-600 border border-emerald-200 ';
            } else if ( status === 'Cancelled' ) {
                status_bg = 'bg-rose-500/10 text-rose-600 border border-rose-200 ';
            }
            selectStatus.className = 'px-2.5 py-1 rounded-full text-[9px] font-bold border outline-none cursor-pointer sh-card-status ' + status_bg;
        }
        
        // Fallback for old table layouts if present
        try {
            var td2_div = row.querySelector('td:nth-child(2) div:first-child');
            if (td2_div && td2_div.childNodes[2]) td2_div.childNodes[2].nodeValue = ' ' + staffName;
            var td2_role = row.querySelector('td:nth-child(2) div:last-child');
            if (td2_role) td2_role.textContent = role;
            var td3_title = row.querySelector('td:nth-child(3) div:first-child');
            if (td3_title) td3_title.textContent = project;
            var td4_date = row.querySelector('td:nth-child(4) div:first-child');
            if (td4_date) td4_date.textContent = date;
            var td4_time = row.querySelector('td:nth-child(4) div:last-child');
            if (td4_time) td4_time.textContent = tStart + ' - ' + tEnd;
            var td6 = row.querySelector('td:nth-child(6)');
            if (td6) td6.textContent = '₹' + parseInt(rate).toLocaleString();
            var tblSelect = row.querySelector('td:nth-child(7) select');
            if (tblSelect) tblSelect.value = status;
        } catch (e) {
            // Ignore table errors if not in table mode
        }
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
    var status_bg = 'bg-zinc-500/10 text-zinc-650 border border-zinc-200 ';
    if ( newStatus === 'On-Site' || newStatus === 'In Progress' ) {
        status_bg = 'bg-amber-500/10 text-amber-600 border border-amber-200 ';
    } else if ( newStatus === 'Confirmed' || newStatus === 'Completed' ) {
        status_bg = 'bg-emerald-500/10 text-emerald-600 border border-emerald-200 ';
    } else if ( newStatus === 'Cancelled' ) {
        status_bg = 'bg-rose-500/10 text-rose-600 border border-rose-200 ';
    }
    
    var row = document.getElementById('shift-row-' + shiftId);
    if (row) {
        var select = row.querySelector('.sh-card-status');
        if (select) {
            select.className = 'px-2.5 py-1 rounded-full text-[9px] font-bold border outline-none cursor-pointer sh-card-status ' + status_bg;
        }
    }

    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Shift status updated to "' + newStatus + '".');
    }
};

window.coraToggleShiftCardSelection = function(shiftId) {
    var row = document.getElementById('shift-row-' + shiftId);
    if (!row) return;
    var cb = row.querySelector('.shift-row-checkbox');
    if (!cb) return;

    cb.checked = !cb.checked;
    coraUpdateCardSelectionVisuals(row, cb.checked);
    coraUpdateBulkBarVisibility();
};

window.coraUpdateCardSelectionVisuals = function(row, isChecked) {
    var btn = row.querySelector('.sh-card-select-btn');
    var checkSvg = row.querySelector('.sh-card-select-check');
    var ring = row.querySelector('.sh-card-select-ring');
    var textSpan = row.querySelector('.sh-card-select-text');

    if (isChecked) {
        row.classList.add('ring-2', 'ring-zinc-950', '', 'border-zinc-950', '');
        if (btn) {
            btn.className = 'sh-card-select-btn px-2.5 py-1 rounded-lg text-[9.5px] font-extrabold uppercase tracking-wider border transition-all cursor-pointer flex items-center gap-1.5 bg-zinc-950 text-white border-zinc-950 shadow-xs';
        }
        if (ring) ring.className = 'sh-card-select-ring w-3.5 h-3.5 rounded-full border border-white flex items-center justify-center transition-all bg-white ';
        if (checkSvg) {
            checkSvg.classList.remove('hidden');
            checkSvg.className = 'sh-card-select-check text-zinc-950 ';
        }
        if (textSpan) textSpan.textContent = 'Selected';
    } else {
        row.classList.remove('ring-2', 'ring-zinc-950', '', 'border-zinc-950', '');
        if (btn) {
            btn.className = 'sh-card-select-btn px-2.5 py-1 rounded-lg text-[9.5px] font-extrabold uppercase tracking-wider border transition-all cursor-pointer flex items-center gap-1.5 bg-zinc-50 hover:bg-zinc-100 text-zinc-500 border-zinc-200/80 ';
        }
        if (ring) ring.className = 'sh-card-select-ring w-3.5 h-3.5 rounded-full border border-zinc-300 flex items-center justify-center transition-all bg-white ';
        if (checkSvg) checkSvg.classList.add('hidden');
        if (textSpan) textSpan.textContent = 'Select';
    }
};

window.coraToggleSelectAllShifts = function() {
    var checkboxes = document.querySelectorAll('.shift-row-checkbox');
    var masterCb = document.getElementById('shift-select-all');
    
    var anyUnchecked = false;
    checkboxes.forEach(function(cb) {
        if (!cb.checked) anyUnchecked = true;
    });
    
    var targetState = anyUnchecked;
    if (masterCb) masterCb.checked = targetState;

    checkboxes.forEach(function(cb) {
        cb.checked = targetState;
        var row = cb.closest('[id^="shift-row-"]');
        if (row) coraUpdateCardSelectionVisuals(row, targetState);
    });

    coraUpdateBulkBarVisibility();
};

window.coraUpdateBulkBarVisibility = function() {
    var checked = document.querySelectorAll('.shift-row-checkbox:checked');
    var allCbs = document.querySelectorAll('.shift-row-checkbox');
    var bar = document.getElementById('cora-bulk-shift-bar');
    var countEl = document.getElementById('bulk-selected-count');
    
    var masterCheck = document.getElementById('cora-select-all-check');
    var masterIcon = document.getElementById('cora-select-all-icon');
    var masterLabel = document.getElementById('cora-select-all-label');
    
    var allSelected = (allCbs.length > 0 && checked.length === allCbs.length);

    if (allSelected) {
        if (masterCheck) masterCheck.classList.remove('hidden');
        if (masterIcon) masterIcon.className = 'w-3.5 h-3.5 rounded-md border border-zinc-950 flex items-center justify-center transition-all bg-zinc-950 ';
        if (masterLabel) masterLabel.textContent = 'Deselect All';
    } else {
        if (masterCheck) masterCheck.classList.add('hidden');
        if (masterIcon) masterIcon.className = 'w-3.5 h-3.5 rounded-md border border-zinc-400 flex items-center justify-center transition-all bg-white ';
        if (masterLabel) masterLabel.textContent = 'Select All';
    }

    if (checked.length > 0) {
        if (bar) bar.classList.remove('hidden');
        if (countEl) countEl.textContent = checked.length + (checked.length === 1 ? ' shift selected' : ' shifts selected');
    } else {
        if (bar) bar.classList.add('hidden');
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
        b.classList.remove('active', 'bg-white', '', 'text-zinc-950', '', 'shadow-2xs', 'font-bold', 'border', 'border-zinc-200/80', '');
        b.classList.add('text-zinc-500', 'hover:text-zinc-900', '', '', 'font-medium', 'hover:bg-zinc-200/50', '');
        b.style.backgroundColor = '';
        b.style.color = '';
    });

    if (btnEl) {
        btnEl.classList.remove('text-zinc-500', 'hover:text-zinc-900', '', '', 'font-medium', 'hover:bg-zinc-200/50', '');
        btnEl.classList.add('active', 'bg-white', '', 'text-zinc-950', '', 'shadow-2xs', 'font-bold', 'border', 'border-zinc-200/80', '');
        btnEl.style.backgroundColor = '';
        btnEl.style.color = '';
    }

    document.querySelectorAll('.cora-tl-block-card').forEach(function(card){
        if (dayNum === 'all' || card.dataset.day == dayNum) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });

    try {
        var u = new URL(window.location.href);
        u.searchParams.set('day', dayNum);
        window.history.replaceState(null, '', u.toString());
    } catch(e) {}
};

window.coraHandleSpanSelectChange = function(val) {
    var customInput = document.getElementById('cora-timeline-custom-span-input');
    if (val === 'custom') {
        if (customInput) {
            customInput.classList.remove('hidden');
            customInput.focus();
            if (customInput.value) {
                window.coraChangeTimelineSpan(customInput.value);
            }
        }
    } else {
        if (customInput) {
            customInput.classList.add('hidden');
        }
        window.coraChangeTimelineSpan(val);
    }
};

window.coraChangeTimelineSpan = function(spanDays) {
    spanDays = parseInt(spanDays, 10) || 3;
    
    var selectEl = document.getElementById('cora-timeline-span-select');
    var customInput = document.getElementById('cora-timeline-custom-span-input');
    if (selectEl) {
        var options = ['3', '6', '7', '14', '30'];
        if (options.includes(spanDays.toString())) {
            selectEl.value = spanDays.toString();
            if (customInput) customInput.classList.add('hidden');
        } else {
            selectEl.value = 'custom';
            if (customInput) {
                customInput.classList.remove('hidden');
                customInput.value = spanDays;
            }
        }
    }

    var container = document.getElementById('cora-day-pills-container');
    if (container) {
        var baseDate = new Date(2026, 6, 20); // Jul 20, 2026
        var html = '<button onclick="coraFilterTimelineDay(\'all\', this)" data-day-val="all" class="tl-day-pill px-3 py-1.5 text-xs rounded-lg transition-all cursor-pointer font-bold bg-white text-zinc-950 shadow-2xs border border-zinc-200/80 shrink-0 whitespace-nowrap">All Days</button>';
        
        for (var i = 1; i <= spanDays; i++) {
            var dObj = new Date(baseDate.getTime());
            dObj.setDate(baseDate.getDate() + (i - 1));
            var dayNumStr = dObj.getDate();
            var monthStr = dObj.toLocaleString('en-US', { month: 'short' });
            var dateStr = dayNumStr + ' ' + monthStr;
            
            html += '<button onclick="coraFilterTimelineDay(' + i + ', this)" data-day-val="' + i + '" title="Day ' + i + ' (' + dateStr + ')" class="tl-day-pill px-3 py-1.5 text-xs rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 font-semibold hover:bg-zinc-200/50 shrink-0 whitespace-nowrap">' + dateStr + '</button>';
        }
        container.innerHTML = html;
    }
    
    var kpiDays = document.getElementById('kpi-stat-total-days');
    if (kpiDays) kpiDays.textContent = spanDays + ' Days';
    
    try {
        var u = new URL(window.location.href);
        u.searchParams.set('span', spanDays);
        window.history.replaceState(null, '', u.toString());
    } catch(e) {}

    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Timeline span expanded to ' + spanDays + ' Days!', 'info');
    }
};

window.coraChangeBlockStatus = function(blockId, newStatus) {
    var select = event ? event.target : null;
    if (select) {
        select.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold border outline-none cursor-pointer transition-all ';
        if (newStatus === 'Completed') {
            select.className += 'bg-emerald-500/10 text-emerald-600 border border-emerald-200 ';
        } else if (newStatus === 'In Progress') {
            select.className += 'bg-amber-500/10 text-amber-600 border border-amber-200 ';
        } else {
            select.className += 'bg-zinc-100 text-zinc-700 border border-zinc-200/50 ';
        }
    }

    if (typeof _coraAjaxPost === 'function') {
        _coraAjaxPost('cora_ajax_update_timeline_status', {
            block_id: blockId,
            status: newStatus
        });
    }

    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Timeline checkpoint status updated to "' + newStatus + '".', 'success');
    }
};

window.coraDeleteBlockItem = function(blockId) {
    var card = document.getElementById('tl-block-' + blockId);
    if (!card && event) {
        card = event.target.closest('.cora-tl-block-card');
    }
    if (card) {
        card.style.transition = 'all 0.3s ease';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(function() { card.remove(); }, 300);
    }
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Timeline checkpoint removed.', 'info');
    }
};

document.addEventListener('DOMContentLoaded', function() {
    try {
        var params = new URLSearchParams(window.location.search);
        var vParam = params.get('view');
        var dParam = params.get('day');
        var sParam = params.get('span');
        
        if (vParam && (vParam === 'timeline' || vParam === 'roster')) {
            window.coraSwitchSchedulerTab(vParam);
        }
        
        if (sParam) {
            var selectEl = document.getElementById('cora-timeline-span-select');
            if (selectEl) selectEl.value = sParam;
            window.coraChangeTimelineSpan(sParam);
        }
        
        if (dParam) {
            var targetPill = document.querySelector('.tl-day-pill[data-day-val="' + dParam + '"]');
            window.coraFilterTimelineDay(dParam === 'all' ? 'all' : parseInt(dParam, 10), targetPill);
        }
    } catch(e) {}
});

window.coraSwitchTimelineProject = function(val) {
    if (window.coraShowToast) window.coraShowToast('Loading selected project itinerary...', 'info');
    try {
        var u = new URL(window.location.href);
        u.searchParams.set('timeline_id', val);
        u.searchParams.set('cora_tl', val);
        window.location.href = u.toString();
    } catch(e) {
        window.location.reload();
    }
};

window.coraOpenAddTimelineBlockDrawer = function() {
    window.coraEnsureDrawersInBody();
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var bd = window.coraGetOrCreateBackdrop();
    if (bd) bd.classList.remove('hidden');
    var dr = document.getElementById('cora-add-timeline-drawer');
    if (dr) {
        window.coraOpenDrawer(dr);
    }
};

window.coraSubmitAddTimelineBlock = function() {
    var titleInput = document.getElementById('blk-activity-title');
    var title = titleInput ? titleInput.value.trim() : '';
    if (!title) { 
        if (typeof window.coraShowToast === 'function') window.coraShowToast('Please enter an activity title.', 'error'); 
        return; 
    }
    var day = document.getElementById('blk-day-number') ? document.getElementById('blk-day-number').value : 1;
    var venue = document.getElementById('blk-venue-address') ? document.getElementById('blk-venue-address').value : 'Cora Workspace Studio';
    var timeStart = document.getElementById('blk-time-start') ? document.getElementById('blk-time-start').value : '10:00 AM';
    var timeEnd = document.getElementById('blk-time-end') ? document.getElementById('blk-time-end').value : '01:00 PM';
    var crew = document.getElementById('blk-crew-member') ? document.getElementById('blk-crew-member').value : 'Karan Malhotra (DoP)';
    
    var blockId = 'blk_dyn_' + Date.now();
    var feed = document.getElementById('cora-timeline-blocks-feed');
    if (feed) {
        var card = document.createElement('div');
        card.id = 'tl-block-' + blockId;
        card.className = 'cora-tl-block-card flex items-start gap-4 sm:gap-6 group';
        card.setAttribute('data-day', day);
        
        card.innerHTML = '\
            <div class="hidden sm:block p-3 rounded-2xl text-center shrink-0 w-28 sm:w-32 space-y-0.5 border border-zinc-200/80 shadow-2xs bg-zinc-50 ">\
                <div class="flex items-center justify-center gap-1 text-[9px] font-extrabold uppercase tracking-wider text-zinc-450 ">\
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>\
                    <span>TIME SLOT</span>\
                </div>\
                <div class="font-mono font-extrabold text-sm text-zinc-900 ">' + timeStart + '</div>\
                <div class="text-[10px] font-mono text-zinc-450 ">to ' + timeEnd + '</div>\
            </div>\
            <div class="hidden sm:flex relative flex-col items-center self-stretch shrink-0">\
                <div class="w-3.5 h-3.5 rounded-full z-10 my-4 shadow-xs border-2 border-white bg-amber-500"></div>\
                <div class="w-0.5 flex-1 -my-2 bg-zinc-200 "></div>\
            </div>\
            <div class="flex-1 rounded-2xl p-4 sm:p-5 transition-all flex flex-col md:flex-row md:items-start justify-between gap-4 bg-white border border-zinc-200/80 shadow-2xs hover:shadow-xs min-w-0 overflow-hidden">\
                <div class="space-y-3 flex-1 min-w-0">\
                    <div class="flex items-center gap-2 flex-wrap">\
                        <span class="px-2.5 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wider bg-zinc-150 text-zinc-650 border border-zinc-200/60 shrink-0">DAY ' + day + '</span>\
                        <span class="inline-flex sm:hidden items-center gap-1 px-2.5 py-0.5 rounded-md text-[9px] font-mono font-bold bg-zinc-100 text-zinc-650 border border-zinc-200/50 shrink-0">\
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>\
                            ' + timeStart + ' - ' + timeEnd + '\
                        </span>\
                        <div class="flex items-center gap-2">\
                            <select onchange="coraChangeBlockStatus(\'' + blockId + '\', this.value)" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border outline-none cursor-pointer transition-all bg-amber-500/10 text-amber-600 border-amber-200 ">\
                                <option value="Completed">✓ Completed</option>\
                                <option value="In Progress" selected>● In Progress</option>\
                                <option value="Upcoming">● Upcoming</option>\
                            </select>\
                            <button onclick="coraDeleteBlockItem(\'' + blockId + '\')" title="Delete Block" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs transition-colors cursor-pointer border border-rose-200 flex items-center justify-center">\
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>\
                            </button>\
                        </div>\
                    </div>\
                    <h3 class="text-base sm:text-lg font-bold tracking-tight text-zinc-900 break-words min-w-0 leading-snug">' + title + '</h3>\
                    <p class="text-xs flex items-center gap-1.5 flex-wrap font-medium text-zinc-500 ">\
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>\
                        <span class="font-bold text-zinc-800 ">' + venue + '</span>\
                    </p>\
                </div>\
                <div class="flex flex-col md:items-end justify-between gap-3 text-right shrink-0">\
                    <span class="text-[9.5px] font-extrabold uppercase tracking-wider text-zinc-400 ">ASSIGNED TEAM CREW</span>\
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold flex items-center gap-1.5 bg-zinc-50 text-zinc-900 border border-zinc-200 ">\
                        ' + crew + '\
                    </span>\
                    <button onclick="coraShareBlockWhatsApp(\'' + title + '\', \'' + venue + '\', \'' + timeStart + '\')" title="WhatsApp Dispatch" class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer border border-zinc-200/80 ">\
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" class="text-emerald-600 "><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.99c-.002 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>\
                        <span>Dispatch WhatsApp</span>\
                    </button>\
                </div>\
            </div>\
        ';
        feed.prepend(card);
    }
    
    if (typeof _coraAjaxPost === 'function') {
        _coraAjaxPost('cora_ajax_save_timeline_block', {
            block_id: blockId,
            day: day,
            activity: title,
            venue: venue,
            time_start: timeStart,
            time_end: timeEnd,
            crew: crew
        });
    }
    
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Itinerary time block added to canvas live!', 'success');
    }
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
    if (dr) {
        window.coraOpenDrawer(dr);
    }
};

window.coraCopyTimelineShareUrl = function() {
    var url = document.getElementById('cora-timeline-share-url').value;
    navigator.clipboard.writeText(url);
    if (window.coraShowToast) window.coraShowToast('Mobile Itinerary URL copied!', 'success');
};

window.coraShareBlockWhatsApp = function(role, venue, time, gpsUrl, phone) {
    var text = encodeURIComponent('Call Sheet Dispatch:\n\nRole: ' + role + '\nCall Time: ' + time + '\nVenue: ' + venue + '\nGPS Navigation: ' + (gpsUrl || 'N/A') + '\n\nPlease acknowledge receipt.');
    var targetPhone = phone || '919811223344';
    window.open('https://wa.me/' + (targetPhone.length === 10 ? '91' + targetPhone : targetPhone) + '?text=' + text, '_blank');
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
            staff_role: document.getElementById('sh-staff-role') ? document.getElementById('sh-staff-role').value : 'On-Site Crew',
            staff_phone: document.getElementById('sh-staff-phone') ? document.getElementById('sh-staff-phone').value : '9876543210',
            date: document.getElementById('sh-date') ? document.getElementById('sh-date').value : '2026-07-23',
            time_start: document.getElementById('sh-time-start') ? document.getElementById('sh-time-start').value : '09:00 AM',
            time_end: document.getElementById('sh-time-end') ? document.getElementById('sh-time-end').value : '05:00 PM',
            property_title: document.getElementById('sh-project-title') ? document.getElementById('sh-project-title').value : '',
            venue: document.getElementById('sh-venue') ? document.getElementById('sh-venue').value : '',
            shift_type: 'On-Site',
            total_payout: document.getElementById('sh-day-rate') ? parseFloat(document.getElementById('sh-day-rate').value) : 25000,
            day_rate: document.getElementById('sh-day-rate') ? parseFloat(document.getElementById('sh-day-rate').value) : 25000,
            status: 'Confirmed'
        };
        
        var hasConflict = window.coraCheckScheduleConflicts('staff', newShift, window.cora_crew_shifts_data);
        const saveShiftFn = function() {
            window.cora_crew_shifts_data.push(newShift);
            _coraAjaxPost('cora_ajax_save_crew_shifts_list', window.cora_crew_shifts_data).then(() => {
                original_coraSubmitAddShift();
            });
        };

        if (hasConflict) {
            if (window.coraConfirmAction) {
                window.coraConfirmAction(
                    'Scheduling Conflict',
                    'There is a scheduling conflict. Do you still want to save?',
                    saveShiftFn
                );
            } else {
                saveShiftFn();
            }
        } else {
            saveShiftFn();
        }
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
            staff_role: document.getElementById('edit-sh-staff-role') ? document.getElementById('edit-sh-staff-role').value : '',
            staff_phone: document.getElementById('edit-sh-staff-phone') ? document.getElementById('edit-sh-staff-phone').value : '',
            date: document.getElementById('edit-sh-date') ? document.getElementById('edit-sh-date').value : '',
            time_start: document.getElementById('edit-sh-time-start') ? document.getElementById('edit-sh-time-start').value : '',
            time_end: document.getElementById('edit-sh-time-end') ? document.getElementById('edit-sh-time-end').value : '',
            property_title: document.getElementById('edit-sh-project-title') ? document.getElementById('edit-sh-project-title').value : '',
            venue: document.getElementById('edit-sh-venue') ? document.getElementById('edit-sh-venue').value : '',
            day_rate: document.getElementById('edit-sh-day-rate') ? parseFloat(document.getElementById('edit-sh-day-rate').value) : 25000,
            total_payout: document.getElementById('edit-sh-day-rate') ? parseFloat(document.getElementById('edit-sh-day-rate').value) : 25000,
            status: document.getElementById('edit-sh-status') ? document.getElementById('edit-sh-status').value : 'Confirmed'
        };
        
        var hasConflict = window.coraCheckScheduleConflicts('staff', editShift, window.cora_crew_shifts_data);
        const saveEditShiftFn = function() {
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

        if (hasConflict) {
            if (window.coraConfirmAction) {
                window.coraConfirmAction(
                    'Scheduling Conflict',
                    'There is a scheduling conflict. Do you still want to save?',
                    saveEditShiftFn
                );
            } else {
                saveEditShiftFn();
            }
        } else {
            saveEditShiftFn();
        }
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
        
        var timeStart = document.getElementById('blk-time-start') ? document.getElementById('blk-time-start').value.trim() : '10:00 AM';
        var timeEnd = document.getElementById('blk-time-end') ? document.getElementById('blk-time-end').value.trim() : '12:00 PM';
        var venue = document.getElementById('blk-venue-address') ? document.getElementById('blk-venue-address').value.trim() : '';
        var crewVal = document.getElementById('blk-crew-member') ? document.getElementById('blk-crew-member').value.trim() : '';
        var dayVal = document.getElementById('blk-day-select') ? parseInt(document.getElementById('blk-day-select').value) : 1;
        
        var crewArray = crewVal ? crewVal.split(',').map(item => item.trim()) : [];
        
        var newBlock = {
            id: 'blk_' + Date.now(),
            day: dayVal,
            day_title: 'Day ' + dayVal + ': Scheduled Activity',
            time_start: timeStart,
            time_end: timeEnd,
            activity: title,
            venue: venue,
            gps_url: 'https://maps.google.com/?q=' + encodeURIComponent(venue),
            type_tag: 'Production',
            duration_tag: '1.5 Hrs',
            dist_tag: '0.0 km',
            crew: crewArray,
            status: 'Upcoming'
        };
        
        var activeTlId = window.cora_active_timeline_id || (window.cora_event_timelines_data[0] ? window.cora_event_timelines_data[0].id : '');
        var activeTimeline = null;
        for (var i = 0; i < window.cora_event_timelines_data.length; i++) {
            if (window.cora_event_timelines_data[i].id === activeTlId) {
                activeTimeline = window.cora_event_timelines_data[i];
                break;
            }
        }
        if (!activeTimeline) activeTimeline = window.cora_event_timelines_data[0];
        if (activeTimeline) {
            if (!activeTimeline.blocks) activeTimeline.blocks = [];
            activeTimeline.blocks.push(newBlock);
        }
        
        _coraAjaxPost('cora_ajax_save_event_timelines_list', window.cora_event_timelines_data).then(() => {
            original_coraSubmitAddTimelineBlock();
            var feed = document.getElementById('cora-timeline-blocks-feed');
            if (feed) {
                var html = '<div class="cora-tl-block-card flex items-start gap-4 sm:gap-6 group" data-day="' + newBlock.day + '">' +
                    '<div class="hidden sm:block p-3 rounded-2xl text-center shrink-0 w-28 sm:w-32 space-y-0.5 border border-zinc-200/80 shadow-2xs bg-zinc-50 ">' +
                    '<div class="flex items-center justify-center gap-1 text-[9px] font-extrabold uppercase tracking-wider text-zinc-450 ">' +
                    '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>' +
                    '<span>TIME SLOT</span></div>' +
                    '<div class="font-mono font-extrabold text-sm text-zinc-900 ">' + newBlock.time_start + '</div>' +
                    '<div class="text-[10px] font-mono text-zinc-450 ">to ' + newBlock.time_end + '</div></div>' +
                    '<div class="hidden sm:flex relative flex-col items-center self-stretch shrink-0">' +
                    '<div class="w-3.5 h-3.5 rounded-full z-10 my-4 shadow-xs border-2 border-white bg-amber-500"></div>' +
                    '<div class="w-0.5 flex-1 -my-2 bg-zinc-200 "></div></div>' +
                    '<div class="flex-1 rounded-2xl p-4 sm:p-5 transition-all flex flex-col md:flex-row md:items-start justify-between gap-4 bg-white border border-zinc-200/80 shadow-2xs hover:shadow-xs">' +
                    '<div class="space-y-3 flex-1">' +
                    '<div class="flex items-center gap-2 flex-wrap">' +
                    '<span class="px-2.5 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wider bg-zinc-150 text-zinc-650 border border-zinc-200/60 shrink-0">DAY ' + newBlock.day + '</span>' +
                    '<span class="inline-flex sm:hidden items-center gap-1 px-2.5 py-0.5 rounded-md text-[9px] font-mono font-bold bg-zinc-100 text-zinc-650 border border-zinc-200/50 shrink-0">' +
                    '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>' + newBlock.time_start + ' - ' + newBlock.time_end + '</span>' +
                    '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border bg-amber-500/10 text-amber-600 border-amber-200">● Upcoming</span></div>' +
                    '<h3 class="text-base sm:text-lg font-bold tracking-tight text-zinc-900 ">' + newBlock.activity + '</h3>' +
                    '<p class="text-xs flex items-center gap-1.5 flex-wrap font-medium text-zinc-500 ">' +
                    '<span class="font-bold text-zinc-800 ">' + newBlock.venue + '</span></p></div></div></div>';
                feed.insertAdjacentHTML('beforeend', html);
            }
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
