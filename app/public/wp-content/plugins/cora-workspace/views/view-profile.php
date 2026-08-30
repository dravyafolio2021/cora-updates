<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user = wp_get_current_user();
$first_name = get_user_meta( $user->ID, 'first_name', true );
$last_name  = get_user_meta( $user->ID, 'last_name', true );
$phone      = get_user_meta( $user->ID, 'cora_phone', true );
$avatar_url = get_user_meta( $user->ID, 'cora_avatar_url', true );

// Custom Status Meta
$custom_status = get_user_meta( $user->ID, 'cora_custom_status', true ) ?: 'Available';
$custom_status_msg = get_user_meta( $user->ID, 'cora_custom_status_message', true ) ?: '';

// Authentication Provider Info (Google SSO vs Email & Password)
$google_id = get_user_meta( $user->ID, 'cora_google_id', true );
$auth_provider = get_user_meta( $user->ID, 'cora_auth_provider', true );
$is_google_user = ( ! empty( $google_id ) || $auth_provider === 'google' );

// Generate initials fallback background color based on name hash
$full_name = trim( $user->display_name );
if ( empty( $full_name ) ) {
    $full_name = $user->user_login;
}
$initials = '';
$words = explode( ' ', $full_name );
foreach ( $words as $w ) {
    $initials .= strtoupper( substr( $w, 0, 1 ) );
}
$initials = substr( $initials, 0, 2 );

// Simple name hash to consistent hex color
$hash = md5( $full_name );
$color_hex = '#' . substr( $hash, 0, 6 );

// Work Info
$role = ! empty( $user->roles ) ? $user->roles[0] : 'subscriber';
$map = array(
    'administrator' => 'Super Admin',
    'cora_manager' => 'Agency Owner',
    'cora_branch_manager' => 'Branch Manager',
    'cora_photographer' => 'Senior Agent',
    'cora_videographer' => 'Agent',
    'cora_drone_pilot' => 'Telecaller',
    'cora_editor' => 'Back Office',
    'cora_viewer' => 'Viewer'
);
$role_label = isset( $map[$role] ) ? $map[$role] : $role;

$is_admin = current_user_can('manage_options') || in_array( $role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner' ) );

$agency_id = get_user_meta( $user->ID, 'cora_agency_id', true );
$agencies  = cora_db_get_agencies();
$agency_name = isset( $agencies[$agency_id] ) ? $agencies[$agency_id]['name'] : 'Default Agency';

$branch_id = get_user_meta( $user->ID, 'cora_branch_id', true );
$branches  = cora_db_get_branches();
$branch_name = isset( $branches[$branch_id] ) ? $branches[$branch_id]['name'] : 'Main Branch';

$joined_on = $user->user_registered;
$joined_formatted = date( 'd/m/Y', strtotime( $joined_on ) );

// Fetch active sessions
$session_tokens = WP_Session_Tokens::get_instance( $user->ID );
$sessions = $session_tokens->get_all();
$session_count = count( $sessions );

// Filter Attendance logs for current user
$attendance_logs = get_option( 'cora_workspace_attendance_logs', array() );
$user_attendance_logs = array();
$today_punched_in = false;
$today_punched_out = false;
$today_date = date('Y-m-d');
$user_email_lower = strtolower($user->user_email);
$user_display_name_lower = strtolower($user->display_name);

if ( is_array( $attendance_logs ) ) {
    foreach ( $attendance_logs as $log ) {
        $log_email = isset( $log['user_email'] ) ? strtolower($log['user_email']) : '';
        $log_user = isset( $log['user'] ) ? strtolower($log['user']) : '';
        if ( $log_email === $user_email_lower || $log_user === $user_display_name_lower ) {
            $user_attendance_logs[] = $log;
            
            $timestamp = isset( $log['timestamp'] ) ? intval( $log['timestamp'] ) : 0;
            if ($timestamp > 10000000000) {
                $timestamp = intval($timestamp / 1000);
            }
            if ( date('Y-m-d', $timestamp) === $today_date ) {
                if ( isset($log['type']) && $log['type'] === 'in' ) {
                    $today_punched_in = true;
                } elseif ( isset($log['type']) && $log['type'] === 'out' ) {
                    $today_punched_out = true;
                }
            }
        }
    }
}
usort($user_attendance_logs, function($a, $b) {
    $ta = isset( $a['timestamp'] ) ? intval( $a['timestamp'] ) : 0;
    $tb = isset( $b['timestamp'] ) ? intval( $b['timestamp'] ) : 0;
    return $tb - $ta;
});
$user_attendance_count = count( $user_attendance_logs );

// Group user attendance logs by date for rendering check-in/out records
$grouped_attendance = array();
foreach ( $user_attendance_logs as $log ) {
    $timestamp = isset( $log['timestamp'] ) ? intval( $log['timestamp'] ) : 0;
    if ($timestamp > 10000000000) {
        $timestamp = intval($timestamp / 1000);
    }
    $date_key = date('Y-m-d', $timestamp);
    if ( ! isset( $grouped_attendance[$date_key] ) ) {
        $grouped_attendance[$date_key] = array(
            'date' => $date_key,
            'in' => '—',
            'out' => '—',
            'geofence' => '—',
            'distance' => '—'
        );
    }
    $time_str = date('H:i:s', $timestamp);
    if ( isset($log['type']) && $log['type'] === 'in' ) {
        $grouped_attendance[$date_key]['in'] = $time_str;
        $grouped_attendance[$date_key]['geofence'] = $log['geofence'] ?? '—';
        $grouped_attendance[$date_key]['distance'] = $log['distance'] ?? '—';
    } elseif ( isset($log['type']) && $log['type'] === 'out' ) {
        $grouped_attendance[$date_key]['out'] = $time_str;
    }
}

// Smart Activity Logger Engine (Last 7 Days, Deduplicated Major Actions)
$all_activity_logs = function_exists('cora_db_get_activity_logs') ? cora_db_get_activity_logs() : array();
$user_activity_logs = array();
$seven_days_ago = time() - ( 7 * DAY_IN_SECONDS );

if ( is_array( $all_activity_logs ) ) {
    foreach ( $all_activity_logs as $log ) {
        if ( isset( $log['user_id'] ) && intval( $log['user_id'] ) === intval( $user->ID ) ) {
            $ts = isset( $log['timestamp'] ) ? intval( $log['timestamp'] ) : 0;
            if ( $ts > 10000000000 ) {
                $ts = intval( $ts / 1000 );
            }
            
            // Only keep events from the last 7 days
            if ( $ts >= $seven_days_ago ) {
                $user_activity_logs[] = $log;
            }
        }
    }
}

// Sort logs newest first
usort($user_activity_logs, function($a, $b) {
    $ta = isset( $a['timestamp'] ) ? intval( $a['timestamp'] ) : 0;
    $tb = isset( $b['timestamp'] ) ? intval( $b['timestamp'] ) : 0;
    return $tb - $ta;
});

// Deduplicate consecutive identical actions within 1 hour (Periodic filter)
$filtered_logs = array();
$last_seen = null;
foreach ( $user_activity_logs as $log ) {
    $ts = isset( $log['timestamp'] ) ? intval( $log['timestamp'] ) : 0;
    if ($ts > 10000000000) {
        $ts = intval($ts / 1000);
    }
    
    $desc = $log['description'] ?? '';
    $action = $log['action_type'] ?? '';
    
    if ( $last_seen && $last_seen['action_type'] === $action && $last_seen['description'] === $desc ) {
        $last_ts = $last_seen['timestamp'];
        if ($last_ts > 10000000000) {
            $last_ts = intval($last_ts / 1000);
        }
        // Deduplicate events occurring within the same hour
        if ( abs( $ts - $last_ts ) < HOUR_IN_SECONDS ) {
            continue;
        }
    }
    $filtered_logs[] = $log;
    $last_seen = $log;
}
$user_activity_logs = $filtered_logs;
$user_activity_count = count( $user_activity_logs );

// Filter Client Tasks assigned to this user
$all_tasks = get_option('cora_workspace_client_tasks', array());
$user_tasks = array();
if ( is_array( $all_tasks ) ) {
    foreach ( $all_tasks as $task ) {
        $assignee_id = isset( $task['assignee_id'] ) ? $task['assignee_id'] : '';
        $assignee_name = isset( $task['assignee_name'] ) ? strtolower( $task['assignee_name'] ) : '';
        
        $is_assigned = false;
        if ( $assignee_id === 'u1' && ( in_array('administrator', $user->roles) || in_array('cora_super_admin', $user->roles) ) ) {
            $is_assigned = true;
        } elseif ( strpos( $assignee_name, $user_display_name_lower ) !== false || 
                  ( ! empty($first_name) && strpos( $assignee_name, strtolower($first_name) ) !== false ) ) {
            $is_assigned = true;
        }
        
        if ( $is_assigned ) {
            $user_tasks[] = $task;
        }
    }
}
$user_task_count = count($user_tasks);

// Filter Crew Shifts for current user
$cora_crew_shifts = get_option( 'cora_crew_shifts', array() );
$user_shifts = array();
if ( is_array( $cora_crew_shifts ) ) {
    foreach ( $cora_crew_shifts as $shift ) {
        $crew = isset( $shift['crew'] ) ? $shift['crew'] : array();
        $is_member = false;
        foreach ( $crew as $cw ) {
            $cw_lower = strtolower($cw);
            if ( strpos( $cw_lower, $user_display_name_lower ) !== false || 
                ( ! empty($first_name) && strpos( $cw_lower, strtolower($first_name) ) !== false ) ) {
                $is_member = true;
                break;
            }
        }
        if ( $is_member ) {
            $user_shifts[] = $shift;
        }
    }
}
$user_shift_count = count($user_shifts);

// Fetch Leave Requests
$leave_requests = get_option( 'cora_workspace_leave_requests', array() );
$my_leaves = array();
$team_pending_leaves = array();
if ( is_array( $leave_requests ) ) {
    foreach ( $leave_requests as $leave ) {
        if ( intval( $leave['user_id'] ) === intval( $user->ID ) ) {
            $my_leaves[] = $leave;
        }
        if ( $is_admin && $leave['status'] === 'pending' ) {
            $team_pending_leaves[] = $leave;
        }
    }
}
?>

<style>
    .profile-tab-btn.tab-active {
        border-color: #09090b !important;
        color: #09090b !important;
    }
    .dark .profile-tab-btn.tab-active {
        border-color: #f4f4f5 !important;
        color: #f4f4f5 !important;
    }
    .profile-pane {
        display: none;
    }
    .profile-pane.pane-active {
        display: block;
        animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .cora-input {
        background-color: #ffffff;
        border: 1px solid #e4e4e7;
        transition: all 0.2s ease;
    }
    .dark .cora-input {
        background-color: #18181b;
        border: 1px solid #27272a;
        color: #f4f4f5;
    }
    .cora-input:focus {
        border-color: #09090b;
        box-shadow: 0 0 0 2px rgba(9, 9, 11, 0.05);
    }
    .dark .cora-input:focus {
        border-color: #f4f4f5;
        box-shadow: 0 0 0 2px rgba(244, 244, 245, 0.1);
    }
    .cora-glass-card {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        transition: all 0.25s ease;
    }
    .dark .cora-glass-card {
        background: #18181b;
        border: 1px solid #27272a;
    }
    .cora-glass-card:hover {
        border-color: #d4d4d8;
    }
    .dark .cora-glass-card:hover {
        border-color: #3f3f46;
    }
    .cora-status-dot {
        box-shadow: 0 0 8px currentColor;
    }

    /* Bulletproof SaaS Drawer Engine CSS (Desktop Side Drawer + Mobile Bottom Sheet) */
    .cora-drawer-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background-color: rgba(9, 9, 11, 0.45) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        z-index: 999999 !important;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: stretch !important;
        opacity: 0 !important;
        pointer-events: none !important;
        transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .cora-drawer-overlay.drawer-open {
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .cora-drawer-sheet {
        background-color: #ffffff !important;
        height: 100% !important;
        width: 100% !important;
        max-width: 460px !important;
        box-shadow: -10px 0 25px -5px rgba(0, 0, 0, 0.12), -8px 0 10px -6px rgba(0, 0, 0, 0.08) !important;
        display: flex !important;
        flex-direction: column !important;
        transform: translateX(100%) !important;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        border-left: 1px solid #e4e4e7 !important;
        pointer-events: auto !important;
        user-select: text !important;
        -webkit-user-select: text !important;
    }

    .dark .cora-drawer-sheet {
        background-color: #18181b !important;
        border-left: 1px solid #27272a !important;
    }

    .cora-drawer-overlay.drawer-open .cora-drawer-sheet {
        transform: translateX(0) !important;
    }

    /* Mobile Bottom-Up Slide Sheet SOP (Rule 1 & Rule 12) */
    @media (max-width: 767px) {
        .cora-drawer-overlay {
            justify-content: center !important;
            align-items: flex-end !important;
        }
        .cora-drawer-sheet {
            height: auto !important;
            max-height: 85vh !important;
            max-width: 100% !important;
            border-left: 0 !important;
            border-top: 1px solid #e4e4e7 !important;
            border-radius: 24px 24px 0 0 !important;
            transform: translateY(100%) !important;
            padding-bottom: env(safe-area-inset-bottom, 16px) !important;
        }
        .dark .cora-drawer-sheet {
            border-top: 1px solid #27272a !important;
            border-left: 0 !important;
        }
        .cora-drawer-overlay.drawer-open .cora-drawer-sheet {
            transform: translateY(0) !important;
        }
    }

    /* Form Controls Touch & Typing Snappiness */
    .cora-input, 
    #cora-password-drawer-sheet input, 
    #cora-avatar-crop-dlg input,
    #cora-password-drawer-sheet button,
    #cora-avatar-crop-dlg button {
        user-select: text !important;
        -webkit-user-select: text !important;
        touch-action: manipulation !important;
        -webkit-tap-highlight-color: transparent !important;
        pointer-events: auto !important;
    }

    /* Hide scrollbars for chrome/safari/firefox */
    .scrollbar-hide::-webkit-scrollbar {
        display: none !important;
    }
    .scrollbar-hide {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }

    /* Mobile Responsive Tables to Cards Transformation */
    @media (max-width: 767px) {
        .responsive-table thead {
            display: none !important;
        }
        .responsive-table tbody,
        .responsive-table tr,
        .responsive-table td {
            display: block !important;
            width: 100% !important;
        }
        .responsive-table tr {
            background-color: #fafafa !important;
            border: 1px solid #f4f4f5 !important;
            border-radius: 12px !important;
            padding: 12px 14px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.01) !important;
        }
        .dark .responsive-table tr {
            background-color: #202023 !important;
            border: 1px solid #2d2d30 !important;
        }
        .responsive-table td {
            border: 0 !important;
            padding: 6px 0 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            text-align: right !important;
        }
        .responsive-table td::before {
            content: attr(data-label) !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            font-size: 9px !important;
            color: #71717a !important;
            text-align: left !important;
            display: inline-block !important;
        }
        .dark .responsive-table td::before {
            color: #a1a1aa !important;
        }
        .responsive-table tr.cora-empty-state-row {
            display: block !important;
            width: 100% !important;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .responsive-table tr.cora-empty-state-row td {
            display: block !important;
            width: 100% !important;
            text-align: center !important;
            justify-content: center !important;
            padding: 32px 16px !important;
            border: 0 !important;
        }
        .responsive-table tr.cora-empty-state-row td::before {
            display: none !important;
            content: none !important;
        }
    }
</style>

<div class="space-y-6">
    <!-- Redesigned Profile Header - 10X Premium Style -->
    <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xs relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left z-10 w-full md:w-auto">
            <!-- Premium Avatar with Edit Trigger -->
            <div class="relative group cursor-pointer shrink-0">
                <?php if ( ! empty( $avatar_url ) ) : ?>
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar" id="profile-avatar-img" class="w-24 h-24 rounded-full object-cover border-2 border-zinc-200 shadow-sm transition-transform duration-300 group-hover:scale-105" loading="lazy">
                <?php else : ?>
                    <div id="profile-avatar-fallback" class="w-24 h-24 rounded-full flex items-center justify-center text-white font-extrabold text-3xl border-2 border-zinc-200 shadow-sm transition-transform duration-300 group-hover:scale-105" style="background-color: <?php echo esc_attr($color_hex); ?>">
                        <?php echo esc_html( $initials ); ?>
                    </div>
                <?php endif; ?>
                
                <div class="absolute inset-0 bg-black/45 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300" onclick="document.getElementById('avatar-input').click()">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="#fff" stroke-width="2.2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                </div>
                <input type="file" id="avatar-input" accept="image/*" style="display:none" onchange="loadAvatarCrop(event)">
            </div>
            
            <div class="space-y-2">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                    <h1 class="text-2xl font-extrabold text-zinc-900 tracking-tight"><?php echo esc_html($full_name); ?></h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-zinc-900 text-white shadow-sm">
                        <?php echo esc_html($role_label); ?>
                    </span>
                    
                    <!-- Dynamic Status Badge -->
                    <?php
                    $status_dot_color = 'text-zinc-400';
                    $status_bg_color = 'bg-zinc-50 text-zinc-500';
                    if ( $custom_status === 'Available' ) {
                        $status_dot_color = 'text-emerald-500';
                        $status_bg_color = 'bg-emerald-50/50 text-emerald-700 border border-emerald-100/50 ';
                    } elseif ( in_array($custom_status, array('In a Shoot', 'Editing')) ) {
                        $status_dot_color = 'text-amber-500';
                        $status_bg_color = 'bg-amber-50/50 text-amber-700 border border-amber-100/50 ';
                    } elseif ( $custom_status === 'Do Not Disturb' ) {
                        $status_dot_color = 'text-red-500';
                        $status_bg_color = 'bg-red-50/50 text-red-700 border border-red-100/50 ';
                    }
                    ?>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase <?php echo $status_bg_color; ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?php echo $status_dot_color; ?> bg-current cora-status-dot"></span>
                        <?php echo esc_html($custom_status); ?>
                    </span>
                </div>
                
                <?php if ( ! empty($custom_status_msg) ) : ?>
                    <p class="text-xs text-zinc-600 italic">"<?php echo esc_html($custom_status_msg); ?>"</p>
                <?php endif; ?>
                
                <p class="text-xs text-zinc-500 font-medium"><?php echo esc_html($branch_name); ?> · Office Workspace Roster · Joined <?php echo esc_html($joined_formatted); ?></p>
            </div>
        </div>

        <!-- Premium Metric Blocks -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full md:w-auto z-10">
            <div class="bg-zinc-50/30 border border-zinc-200/80 rounded-xl p-3.5 text-center sm:text-left min-w-[110px]">
                <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-widest block">Attendance</span>
                <span class="text-2xl font-black text-zinc-900 tracking-tight block mt-1"><?php echo $user_attendance_count; ?> <span class="text-xs text-zinc-400 font-bold">days</span></span>
            </div>
            <div class="bg-zinc-50/30 border border-zinc-200/80 rounded-xl p-3.5 text-center sm:text-left min-w-[110px]">
                <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-widest block">My Tasks</span>
                <span class="text-2xl font-black text-zinc-900 tracking-tight block mt-1"><?php echo $user_task_count; ?> <span class="text-xs text-zinc-400 font-bold">active</span></span>
            </div>
            <div class="bg-zinc-50/30 border border-zinc-200/80 rounded-xl p-3.5 text-center sm:text-left min-w-[110px]">
                <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-widest block">Crew Shifts</span>
                <span class="text-2xl font-black text-zinc-900 tracking-tight block mt-1"><?php echo $user_shift_count; ?> <span class="text-xs text-zinc-400 font-bold">roster</span></span>
            </div>
            <div class="bg-zinc-50/30 border border-zinc-200/80 rounded-xl p-3.5 text-center sm:text-left min-w-[110px]">
                <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-widest block">Audit Trail</span>
                <span class="text-2xl font-black text-zinc-900 tracking-tight block mt-1"><?php echo $user_activity_count; ?> <span class="text-xs text-zinc-400 font-bold">events</span></span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-zinc-200 flex flex-nowrap overflow-x-auto gap-1 scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0">
        <button onclick="switchProfileTab('tab-info')" id="btn-tab-info" class="profile-tab-btn shrink-0 border-b-2 border-transparent px-4 py-3 text-xs font-extrabold text-zinc-400 hover:text-zinc-900 transition-all flex items-center gap-2 cursor-pointer bg-transparent">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            Personal & Work Info
        </button>
        <button onclick="switchProfileTab('tab-status')" id="btn-tab-status" class="profile-tab-btn shrink-0 border-b-2 border-transparent px-4 py-3 text-xs font-extrabold text-zinc-400 hover:text-zinc-900 transition-all flex items-center gap-2 cursor-pointer bg-transparent">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            Duty Status & Leaves
        </button>
        <button onclick="switchProfileTab('tab-attendance')" id="btn-tab-attendance" class="profile-tab-btn shrink-0 border-b-2 border-transparent px-4 py-3 text-xs font-extrabold text-zinc-400 hover:text-zinc-900 transition-all flex items-center gap-2 cursor-pointer bg-transparent">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Daily Attendance
        </button>
        <button onclick="switchProfileTab('tab-tasks')" id="btn-tab-tasks" class="profile-tab-btn shrink-0 border-b-2 border-transparent px-4 py-3 text-xs font-extrabold text-zinc-400 hover:text-zinc-900 transition-all flex items-center gap-2 cursor-pointer bg-transparent">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            Tasks & Shifts
        </button>
        <button onclick="switchProfileTab('tab-activity')" id="btn-tab-activity" class="profile-tab-btn shrink-0 border-b-2 border-transparent px-4 py-3 text-xs font-extrabold text-zinc-400 hover:text-zinc-900 transition-all flex items-center gap-2 cursor-pointer bg-transparent">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            Activity Logs
        </button>
    </div>

    <!-- Tab 1: Profile & Work Info -->
    <div id="tab-info" class="profile-pane pane-active space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal details form -->
                <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-6">
                    <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">Personal Details</h3>
                    <form id="profile-info-form" onsubmit="coraSaveProfileInfo(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">First Name</label>
                            <input type="text" id="profile-first-name" value="<?php echo esc_attr( $first_name ); ?>" required class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">Last Name</label>
                            <input type="text" id="profile-last-name" value="<?php echo esc_attr( $last_name ); ?>" required class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">Email Address</label>
                            <input type="email" value="<?php echo esc_attr( $user->user_email ); ?>" disabled class="cora-input w-full px-3 py-2.5 text-xs rounded-xl bg-zinc-50/50 text-zinc-500 cursor-not-allowed border-dashed">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">Phone Number</label>
                            <input type="text" id="profile-phone" value="<?php echo esc_attr( $phone ); ?>" class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none" placeholder="+91 99999 99999">
                        </div>
                        <div class="sm:col-span-2 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-extrabold rounded-xl text-xs transition-colors cursor-pointer border-0 shadow-2xs">Save Changes</button>
                        </div>
                    </form>
                </div>

                <!-- SaaS Upgraded Security & Password Card -->
                <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                    <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">Security &amp; Authentication</h3>
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 py-2">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <?php if ( $is_google_user ) : ?>
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" class="shrink-0"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/></svg>
                                <span class="text-xs font-extrabold text-zinc-800">Google SSO Authentication</span>
                                <span class="text-[9.5px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Connected</span>
                                <?php else : ?>
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                <span class="text-xs font-extrabold text-zinc-800">Account Password Protection</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-[10px] text-zinc-500">
                                <?php if ( $is_google_user ) : ?>
                                Connected via Google Account (<?php echo esc_html( $user->user_email ); ?>). You can also configure an optional direct password.
                                <?php else : ?>
                                Encrypted with industry-standard hashing. Last active session audit is active.
                                <?php endif; ?>
                            </p>
                        </div>
                        <button type="button" onclick="window.openPasswordDrawer()" class="px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-extrabold rounded-xl text-xs transition-all cursor-pointer border-0 shadow-xs whitespace-nowrap" style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;">
                            <?php echo $is_google_user ? 'Set Direct Password' : 'Update Account Password'; ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Work Information and Sessions Sidebar -->
            <div class="space-y-6">
                <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                    <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">Work Information</h3>
                    <div class="space-y-3.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-zinc-500 ">Role</span>
                            <span class="font-bold text-zinc-900 "><?php echo esc_html($role_label); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 ">Branch</span>
                            <span class="font-bold text-zinc-900 "><?php echo esc_html($branch_name); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 ">Agency</span>
                            <span class="font-bold text-zinc-900 "><?php echo esc_html($agency_name); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 ">Joined On</span>
                            <span class="font-bold text-zinc-900 "><?php echo esc_html($joined_formatted); ?></span>
                        </div>
                    </div>
                </div>

                <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h3 class="text-sm font-extrabold text-zinc-900 uppercase tracking-wider text-[11px] text-zinc-400">Active Sessions</h3>
                        <button onclick="coraLogOutOtherSessions()" class="text-zinc-600 hover:text-zinc-900 font-extrabold text-[9px] border border-zinc-200 px-2 py-1 rounded-lg bg-white hover:bg-zinc-50 transition-colors shadow-2xs border-0 cursor-pointer">Logout Others</button>
                    </div>
                    
                    <div class="divide-y divide-zinc-100 ">
                        <?php
                        $current_token = wp_get_session_token();
                        foreach ( $sessions as $token_key => $sess ) :
                            $is_current = ( $token_key === $current_token );
                            $login_time = date( 'd M H:i', $sess['login'] );
                            $ua = $sess['ua'];
                            $device = 'Browser';
                            if ( strpos( $ua, 'Chrome' ) !== false ) $device = 'Chrome';
                            elseif ( strpos( $ua, 'Firefox' ) !== false ) $device = 'Firefox';
                            elseif ( strpos( $ua, 'Safari' ) !== false ) $device = 'Safari';
                            
                            $platform = 'OS';
                            if ( strpos( $ua, 'Macintosh' ) !== false ) $platform = 'macOS';
                            elseif ( strpos( $ua, 'Windows' ) !== false ) $platform = 'Windows';
                            elseif ( strpos( $ua, 'iPhone' ) !== false ) $platform = 'iOS';
                            elseif ( strpos( $ua, 'Android' ) !== false ) $platform = 'Android';
                        ?>
                            <div class="py-3 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="text-zinc-500 ">
                                        <?php if ( strpos( $ua, 'iPhone' ) !== false || strpos( $ua, 'Android' ) !== false ) : ?>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="5" y="2" width="14" height="20" rx="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                                        <?php else : ?>
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-zinc-800 ">
                                            <?php echo esc_html( "$device on $platform" ); ?>
                                            <?php if ($is_current) : ?>
                                                <span class="text-[8px] font-extrabold bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded-full border border-emerald-100/50 ml-1">Current</span>
                                            <?php endif; ?>
                                        </p>
                                        <p class="text-[9px] text-zinc-400 font-medium"><?php echo esc_html($sess['ip']); ?> · <?php echo esc_html($login_time); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Duty Status & Leaves -->
    <div id="tab-status" class="profile-pane space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Duty Status Manager Card -->
            <div class="lg:col-span-1 space-y-6">
                <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-5">
                    <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">Duty Status Settings</h3>
                    
                    <form id="profile-status-form" onsubmit="coraUpdateUserStatus(event)" class="space-y-4">
                        <div>
                            <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">Availability Status</label>
                            <select id="user-status-select" class="cora-input w-full px-3 py-2 text-xs rounded-xl focus:outline-none font-bold">
                                <option value="Available" <?php selected($custom_status, 'Available'); ?>>Available / Active</option>
                                <option value="In a Shoot" <?php selected($custom_status, 'In a Shoot'); ?>>In a Shoot</option>
                                <option value="Editing" <?php selected($custom_status, 'Editing'); ?>>Editing / Productive</option>
                                <option value="In a Meeting" <?php selected($custom_status, 'In a Meeting'); ?>>In a Meeting</option>
                                <option value="Do Not Disturb" <?php selected($custom_status, 'Do Not Disturb'); ?>>Do Not Disturb</option>
                                <option value="On Leave" <?php selected($custom_status, 'On Leave'); ?>>On Leave / Away</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">Status Description message</label>
                            <input type="text" id="user-status-msg" value="<?php echo esc_attr($custom_status_msg); ?>" class="cora-input w-full px-3 py-2 text-xs rounded-xl focus:outline-none" placeholder="e.g. Scouting Udaipur wedding site...">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-extrabold rounded-xl text-xs transition-colors cursor-pointer border-0 shadow-sm flex items-center justify-center gap-2">
                            Update Status Broadcast
                        </button>
                    </form>
                </div>

                <!-- Request Leave Request Card -->
                <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                    <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">Request Leave Facility</h3>
                    
                    <form id="leave-request-form" onsubmit="coraRequestLeave(event)" class="space-y-4">
                        <div>
                            <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">Leave Type</label>
                            <select id="leave-type" class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none">
                                <option value="Casual Leave">Casual Leave</option>
                                <option value="Medical Leave">Medical Leave / Sick</option>
                                <option value="Earned Leave">Earned Leave</option>
                                <option value="Unpaid Leave">Unpaid Leave</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">Start Date</label>
                                <input type="date" id="leave-start" required class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">End Date</label>
                                <input type="date" id="leave-end" required class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-zinc-700 mb-1.5">Reason for request</label>
                            <textarea id="leave-reason" required rows="2" class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none resize-none" placeholder="State reason for leave request..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-extrabold rounded-xl text-xs transition-colors cursor-pointer border-0 shadow-sm">
                            Submit Request
                        </button>
                    </form>
                </div>
            </div>

            <!-- Leaves Board Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Administrative Approvals Board (Only for Admins) -->
                <?php if ( $is_admin ) : ?>
                    <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                            <h3 class="text-sm font-extrabold text-zinc-900 uppercase tracking-wider text-[11px] text-zinc-400">Team Leave Approvals</h3>
                            <span class="bg-amber-100 text-amber-800 text-[9px] font-black uppercase px-2 py-0.5 rounded-full border border-amber-200 ">
                                <?php echo count($team_pending_leaves); ?> Pending
                            </span>
                        </div>
                        
                        <div class="space-y-3">
                            <?php if ( empty($team_pending_leaves) ) : ?>
                                <p class="text-xs text-zinc-400 py-4 text-center">Zero pending leave requests to approve.</p>
                            <?php else : ?>
                                <?php foreach ( $team_pending_leaves as $request ) : 
                                    $sub_time = date('d M Y, H:i', $request['submitted_at']);
                                ?>
                                    <div class="p-4 bg-zinc-50/50 border border-zinc-200/80 rounded-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-extrabold text-zinc-900 "><?php echo esc_html($request['user_name']); ?></h4>
                                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded bg-zinc-200 text-zinc-700 "><?php echo esc_html($request['type']); ?></span>
                                            </div>
                                            <p class="text-[10px] text-zinc-555 font-medium">Dates: <?php echo esc_html($request['start_date']); ?> to <?php echo esc_html($request['end_date']); ?></p>
                                            <p class="text-xs text-zinc-600 italic mt-1 font-medium">"<?php echo esc_html($request['reason']); ?>"</p>
                                            <span class="text-[8px] text-zinc-400 block pt-1">Submitted: <?php echo $sub_time; ?></span>
                                        </div>
                                        
                                        <div class="flex gap-2 w-full sm:w-auto shrink-0 font-extrabold">
                                            <button onclick="coraUpdateLeaveStatus('<?php echo $request['id']; ?>', 'rejected')" class="flex-1 sm:flex-none px-3.5 py-2 border border-zinc-200 bg-white hover:bg-zinc-50 text-red-655 rounded-xl text-[10px] transition-colors cursor-pointer border-0 font-extrabold">Reject</button>
                                            <button onclick="coraUpdateLeaveStatus('<?php echo $request['id']; ?>', 'approved')" class="flex-1 sm:flex-none px-3.5 py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded-xl text-[10px] transition-colors cursor-pointer border-0 font-extrabold">Approve</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Personal Leave Requests History -->
                <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                    <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">My Leave Applications</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs responsive-table">
                            <thead>
                                <tr class="bg-zinc-50 text-zinc-500 font-bold border-b border-zinc-200 ">
                                    <th class="p-3">Type</th>
                                    <th class="p-3">Duration</th>
                                    <th class="p-3">Reason</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Applied</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 ">
                                <?php if ( empty($my_leaves) ) : ?>
                                    <tr class="cora-empty-state-row">
                                        <td colspan="5" class="p-4 text-center text-zinc-400 ">No leave requests submitted yet.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ( $my_leaves as $leave ) : 
                                        $applied_at = date('d M Y', $leave['submitted_at']);
                                        $lbl_status = 'Pending';
                                        $status_style = 'bg-amber-50 text-amber-700 border border-amber-100 ';
                                        if ( $leave['status'] === 'approved' ) {
                                            $lbl_status = 'Approved';
                                            $status_style = 'bg-emerald-50 text-emerald-700 border border-emerald-100 ';
                                        } elseif ( $leave['status'] === 'rejected' ) {
                                            $lbl_status = 'Rejected';
                                            $status_style = 'bg-red-50 text-red-700 border border-red-100 ';
                                        }
                                    ?>
                                        <tr class="hover:bg-zinc-50/50 text-zinc-700 ">
                                            <td class="p-3 font-semibold" data-label="Type"><?php echo esc_html($leave['type']); ?></td>
                                            <td class="p-3 whitespace-nowrap font-medium" data-label="Duration"><?php echo esc_html($leave['start_date']); ?> to <?php echo esc_html($leave['end_date']); ?></td>
                                            <td class="p-3 max-w-[200px] truncate" data-label="Reason"><?php echo esc_html($leave['reason']); ?></td>
                                            <td class="p-3" data-label="Status">
                                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase <?php echo $status_style; ?>">
                                                    <?php echo $lbl_status; ?>
                                                </span>
                                            </td>
                                            <td class="p-3 text-zinc-500 " data-label="Applied"><?php echo $applied_at; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 3: Daily Attendance Pane -->
    <div id="tab-attendance" class="profile-pane space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Punch Widget -->
            <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400 font-extrabold font-bold">Today's Attendance</h3>
                
                <div class="space-y-4 py-2">
                    <div class="flex items-center gap-3">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 <?php echo $today_punched_in && !$today_punched_out ? 'bg-emerald-400' : 'bg-zinc-300 '; ?>"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 <?php echo $today_punched_in && !$today_punched_out ? 'bg-emerald-500' : 'bg-zinc-400'; ?>"></span>
                        </span>
                        <div>
                            <p class="text-xs font-bold text-zinc-800 ">
                                <?php if ($today_punched_out) : ?>
                                    Duty Completed
                                <?php elseif ($today_punched_in) : ?>
                                    Active Duty (Punched In)
                                <?php else : ?>
                                    Not Punched In
                                <?php endif; ?>
                            </p>
                            <p class="text-[9px] text-zinc-500 ">GPS geofenced coordinates punch validation is active</p>
                        </div>
                    </div>
                    
                    <div class="pt-2 flex flex-col gap-2">
                        <?php if ( ! $today_punched_in ) : ?>
                            <button onclick="coraPunchAttendance('in')" class="w-full py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-extrabold rounded-xl text-xs transition-colors cursor-pointer border-0 shadow-sm flex items-center justify-center gap-2">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                                Punch Clock In
                            </button>
                        <?php elseif ( ! $today_punched_out ) : ?>
                            <button onclick="coraPunchAttendance('out')" class="w-full py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-extrabold rounded-xl text-xs transition-colors cursor-pointer border-0 shadow-sm flex items-center justify-center gap-2">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                Punch Clock Out
                            </button>
                        <?php else : ?>
                            <button disabled class="w-full py-2.5 bg-zinc-100 text-zinc-400 font-extrabold rounded-xl text-xs cursor-not-allowed border-0">
                                Punch Card Completed
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <div id="punch-status-text" class="text-[9px] text-zinc-500 font-bold text-center hidden"></div>
                </div>
            </div>
            
            <!-- Attendance History -->
            <div class="lg:col-span-2 cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">Monthly Punch Card Records</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs responsive-table">
                        <thead>
                            <tr class="bg-zinc-50 text-zinc-500 font-bold border-b border-zinc-200 ">
                                <th class="p-3">Date</th>
                                <th class="p-3">Punched In</th>
                                <th class="p-3">Punched Out</th>
                                <th class="p-3">GPS Geofence</th>
                                <th class="p-3">Distance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 ">
                            <?php if ( empty($grouped_attendance) ) : ?>
                                <tr class="cora-empty-state-row">
                                    <td colspan="5" class="p-4 text-center text-zinc-400 ">No punch card records found for this month.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ( $grouped_attendance as $record ) : 
                                    $rec_date = date('d M Y', strtotime($record['date']));
                                ?>
                                    <tr class="hover:bg-zinc-50/50 text-zinc-700 ">
                                        <td class="p-3 font-semibold" data-label="Date"><?php echo $rec_date; ?></td>
                                        <td class="p-3 text-emerald-600 font-bold" data-label="Punched In"><?php echo esc_html($record['in']); ?></td>
                                        <td class="p-3 text-zinc-500 " data-label="Punched Out"><?php echo esc_html($record['out']); ?></td>
                                        <td class="p-3" data-label="GPS Geofence">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase bg-zinc-100 border border-zinc-200 text-zinc-800 ">
                                                <?php echo esc_html($record['geofence']); ?>
                                            </span>
                                        </td>
                                        <td class="p-3 text-zinc-500 " data-label="Distance"><?php echo esc_html($record['distance']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 4: Tasks & Shifts -->
    <div id="tab-tasks" class="profile-pane space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tasks Column -->
            <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">My Tasks</h3>
                
                <div class="space-y-3">
                    <?php if ( empty($user_tasks) ) : ?>
                        <p class="text-xs text-zinc-400 py-4 text-center">No active tasks assigned to you.</p>
                    <?php else : ?>
                        <?php foreach ( $user_tasks as $task ) : 
                            $priority = $task['priority'] ?? 'medium';
                            $status = $task['status'] ?? 'todo';
                            $due = !empty($task['due_date']) ? date('d M Y', strtotime($task['due_date'])) : 'No due date';
                            
                            $prio_class = 'bg-zinc-50 text-zinc-650 border-zinc-200 ';
                            if ( $priority === 'high' || $priority === 'urgent' ) {
                                $prio_class = 'bg-zinc-100 text-zinc-800 font-extrabold border-zinc-300 ';
                            }
                            
                            $status_label = 'To Do';
                            $status_class = 'bg-zinc-100 text-zinc-700 ';
                            if ( $status === 'in_progress' || $status === 'inprogress' ) {
                                $status_label = 'In Progress';
                                $status_class = 'bg-amber-50 text-amber-700 border border-amber-100 ';
                            } elseif ( $status === 'client_review' || $status === 'review' ) {
                                $status_label = 'Review';
                                $status_class = 'bg-blue-50 text-blue-700 border border-blue-100 ';
                            } elseif ( $status === 'done' ) {
                                $status_label = 'Completed';
                                $status_class = 'bg-emerald-50 text-emerald-700 border border-emerald-100 ';
                            } elseif ( $status === 'blocked' ) {
                                $status_label = 'Blocked';
                                $status_class = 'bg-red-50 text-red-700 border border-red-100 ';
                            }
                        ?>
                            <div class="p-3.5 bg-zinc-50/50 border border-zinc-200/80 rounded-xl space-y-2 hover:border-zinc-300 transition-all">
                                <div class="flex justify-between items-start gap-3">
                                    <h4 class="text-xs font-bold text-zinc-900 leading-tight"><?php echo esc_html($task['title']); ?></h4>
                                    <span class="inline-flex shrink-0 px-2 py-0.5 rounded text-[8px] font-extrabold uppercase border <?php echo $prio_class; ?>"><?php echo esc_html($priority); ?></span>
                                </div>
                                <p class="text-[10px] text-zinc-500 line-clamp-2"><?php echo esc_html($task['desc'] ?? 'No description provided.'); ?></p>
                                <div class="flex justify-between items-center text-[10px] text-zinc-500 pt-1">
                                    <span>Due: <?php echo $due; ?></span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Shifts Column -->
            <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
                <h3 class="text-sm font-extrabold text-zinc-900 border-b border-zinc-100 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">My Shifts Roster</h3>
                
                <div class="space-y-3">
                    <?php if ( empty($user_shifts) ) : ?>
                        <p class="text-xs text-zinc-400 py-4 text-center">No shifts scheduled for your user profile.</p>
                    <?php else : ?>
                        <?php foreach ( $user_shifts as $shift ) : 
                            $day_name = esc_html($shift['day'] ?? 'Monday');
                            $activity = esc_html($shift['activity'] ?? 'General Duty');
                            $start = esc_html($shift['start'] ?? '10:00 AM');
                            $end = esc_html($shift['end'] ?? '06:00 PM');
                            $venue = esc_html($shift['venue'] ?? 'Office / Main Branch');
                        ?>
                            <div class="p-3.5 bg-zinc-50/50 border border-zinc-200/80 rounded-xl space-y-2">
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 border border-zinc-200 "><?php echo $day_name; ?></span>
                                    <span class="text-[10px] font-mono text-zinc-500 "><?php echo "$start - $end"; ?></span>
                                </div>
                                <h4 class="text-xs font-bold text-zinc-900 mt-1"><?php echo $activity; ?></h4>
                                <div class="flex items-center gap-1 text-[9px] text-zinc-500 mt-1.5">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <span class="truncate"><?php echo $venue; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 5: Smart Activity Logs (Piggy Nation & 7 Days Windowed) -->
    <div id="tab-activity" class="profile-pane space-y-6">
        <div class="cora-glass-card rounded-2xl p-6 shadow-2xs space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-zinc-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-zinc-900 uppercase tracking-wider text-[11px] text-zinc-400">My Activities & Logs</h3>
                    <p class="text-[9px] text-zinc-450 mt-0.5 uppercase tracking-wider font-extrabold text-emerald-600 ">Displaying major actions within a 7-day rolling window</p>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs responsive-table">
                    <thead>
                        <tr class="bg-zinc-50 text-zinc-500 font-bold border-b border-zinc-200 ">
                            <th class="p-3">Time</th>
                            <th class="p-3">Event Type</th>
                            <th class="p-3">Description</th>
                            <th class="p-3">Source IP</th>
                            <th class="p-3">Device / Platform</th>
                        </tr>
                    </thead>
                    <tbody id="activity-logs-table-body" class="divide-y divide-zinc-100 ">
                        <?php if ( empty($user_activity_logs) ) : ?>
                            <tr class="cora-empty-state-row">
                                <td colspan="5" class="p-4 text-center text-zinc-400 ">No activity events logged for your user profile in the last 7 days.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $user_activity_logs as $log ) : 
                                $log_time = isset($log['timestamp']) ? date('d M Y, H:i', $log['timestamp']) : '—';
                                $action_type = esc_html($log['action_type'] ?? 'Action');
                                $desc = esc_html($log['description'] ?? '');
                                $ip = esc_html($log['ip'] ?? '127.0.0.1');
                                
                                $device_raw = $log['device'] ?? '';
                                $short_device = 'Browser';
                                if (strpos($device_raw, 'Chrome') !== false) $short_device = 'Chrome';
                                elseif (strpos($device_raw, 'Safari') !== false) $short_device = 'Safari';
                                elseif (strpos($device_raw, 'Firefox') !== false) $short_device = 'Firefox';
                                
                                $short_os = 'OS';
                                if (strpos($device_raw, 'Mac') !== false) $short_os = 'macOS';
                                elseif (strpos($device_raw, 'Win') !== false) $short_os = 'Windows';
                                elseif (strpos($device_raw, 'Android') !== false) $short_os = 'Android';
                                elseif (strpos($device_raw, 'iPhone') !== false) $short_os = 'iOS';
                            ?>
                                <tr class="hover:bg-zinc-50/55 text-zinc-700 ">
                                    <td class="p-3 whitespace-nowrap" data-label="Time"><?php echo $log_time; ?></td>
                                    <td class="p-3 whitespace-nowrap" data-label="Event Type">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-zinc-100 border border-zinc-200 text-zinc-800 ">
                                            <?php echo $action_type; ?>
                                        </span>
                                    </td>
                                    <td class="p-3" data-label="Description"><?php echo $desc; ?></td>
                                    <td class="p-3 font-mono text-[10px]" data-label="Source IP"><?php echo $ip; ?></td>
                                    <td class="p-3 whitespace-nowrap text-zinc-500 " data-label="Device/OS"><?php echo "$short_device on $short_os"; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Dynamic Piggy Nation Footer -->
            <div id="activity-pagination-container" class="pt-4 border-t border-zinc-100 flex items-center justify-between text-xs text-zinc-500 ">
                <span id="activity-pagination-info">Showing 1-10 of 10 entries</span>
                <div class="flex items-center gap-2 font-extrabold">
                    <button onclick="changeActivityPage(-1)" id="btn-activity-prev" class="px-3 py-1.5 border border-zinc-200 bg-white hover:bg-zinc-50 rounded-lg transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed text-zinc-755 border-0">Previous</button>
                    <button onclick="changeActivityPage(1)" id="btn-activity-next" class="px-3 py-1.5 border border-zinc-200 bg-white hover:bg-zinc-50 rounded-lg transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed text-zinc-755 border-0">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ PREMIUM SAAS MULTI-STEP PASSWORD DRAWER SHEET ═════════════════════════ -->
<div id="cora-password-drawer-sheet" class="cora-drawer-overlay" onclick="if(event.target===this) window.closePasswordDrawer();" style="touch-action: manipulation; pointer-events: none;">
    <div class="cora-drawer-sheet" id="password-drawer-card" onclick="event.stopPropagation();" style="touch-action: manipulation; user-select: text !important; -webkit-user-select: text !important; pointer-events: auto !important;">
        <!-- Mobile Bottom Sheet Drag Handle -->
        <div class="flex sm:hidden items-center justify-center pt-3 pb-1 cursor-pointer select-none" onclick="window.closePasswordDrawer()">
            <div class="w-10 h-1 rounded-full bg-zinc-300"></div>
        </div>

        <!-- Header -->
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <div>
                <h3 class="text-sm font-extrabold text-zinc-900"><?php echo $is_google_user ? 'Set Direct Account Password' : 'Update Account Password'; ?></h3>
                <p class="text-[9px] text-zinc-450 mt-0.5 uppercase tracking-wider font-extrabold"><?php echo $is_google_user ? 'Google SSO · Credentials Setup' : 'Identity Verification & Reset'; ?></p>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors bg-transparent border-0" onclick="window.closePasswordDrawer()">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <!-- Steps Container -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <?php if ( ! $is_google_user ) : ?>
            <!-- Step 1: Current Password Verification (Email & Password Users Only) -->
            <div id="pass-step-1" class="space-y-4">
                <div class="space-y-1.5">
                    <span class="text-[9px] font-black uppercase text-zinc-455 tracking-wider">Step 01 of 02</span>
                    <h4 class="text-xs font-black text-zinc-900">Verify Your Current Identity</h4>
                    <p class="text-[10px] text-zinc-500 leading-relaxed">To ensure secure updates, confirm your current password before configuring new credentials.</p>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-[10px] font-extrabold text-zinc-700 mb-1.5 uppercase tracking-wider">Current Password</label>
                        <input type="password" id="drawer-curr-pass" onkeydown="if(event.key==='Enter') window.verifyCurrentPasswordStep();" class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none" placeholder="Enter active password..." style="user-select: text !important; -webkit-user-select: text !important; pointer-events: auto !important; cursor: text !important;">
                    </div>
                    
                    <button type="button" onclick="window.verifyCurrentPasswordStep()" id="btn-verify-identity" class="w-full py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-extrabold rounded-xl text-xs transition-colors cursor-pointer border-0 shadow-sm flex items-center justify-center gap-2">
                        Verify Identity Credentials
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Step 2: New Password Setup -->
            <div id="pass-step-2" class="space-y-4" style="<?php echo $is_google_user ? '' : 'display: none;'; ?>">
                <div class="space-y-1.5">
                    <span class="text-[9px] font-black uppercase text-emerald-600 tracking-wider"><?php echo $is_google_user ? 'Google SSO Account · Direct Password' : 'Step 02 of 02 · Identity Verified'; ?></span>
                    <h4 class="text-xs font-black text-zinc-900"><?php echo $is_google_user ? 'Configure Your Direct Login Password' : 'Set Up Your New Credentials'; ?></h4>
                    <p class="text-[10px] text-zinc-500 leading-relaxed"><?php echo $is_google_user ? 'Setting a password allows you to sign in with either your Google account or email & password.' : 'Choose a strong, unique password to replace your active workspace credentials.'; ?></p>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-zinc-700 mb-1.5 uppercase tracking-wider">New Password</label>
                        <input type="password" id="drawer-new-pass" oninput="window.validatePasswordStrength()" class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none" placeholder="At least 8 characters..." style="user-select: text !important; -webkit-user-select: text !important; pointer-events: auto !important; cursor: text !important;">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-zinc-700 mb-1.5 uppercase tracking-wider">Confirm New Password</label>
                        <input type="password" id="drawer-confirm-pass" oninput="window.validatePasswordStrength()" onkeydown="if(event.key==='Enter') window.saveNewPasswordAction();" class="cora-input w-full px-3 py-2.5 text-xs rounded-xl focus:outline-none" placeholder="Re-type new password..." style="user-select: text !important; -webkit-user-select: text !important; pointer-events: auto !important; cursor: text !important;">
                    </div>
                    
                    <!-- Real-time Password Strength Indicator Bar -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center text-[9px] font-extrabold uppercase">
                            <span class="text-zinc-450">Strength Indicator</span>
                            <span id="strength-label" class="text-zinc-400">Weak</span>
                        </div>
                        <div class="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                            <div id="strength-progress" class="w-1/4 h-full bg-red-500 transition-all duration-300"></div>
                        </div>
                    </div>

                    <!-- Criteria Checklist -->
                    <div class="space-y-2 py-2 bg-zinc-50/50 rounded-xl p-3 border border-zinc-100">
                        <p class="text-[9px] font-black uppercase text-zinc-455 tracking-wider mb-1.5">Requirements Checklist</p>
                        <div class="grid grid-cols-2 gap-2 text-[10px] font-bold text-zinc-500">
                            <div class="flex items-center gap-1.5" id="req-len">
                                <span class="chk-icon text-zinc-350">•</span> At least 8 characters
                            </div>
                            <div class="flex items-center gap-1.5" id="req-up">
                                <span class="chk-icon text-zinc-350">•</span> One uppercase letter
                            </div>
                            <div class="flex items-center gap-1.5" id="req-lo">
                                <span class="chk-icon text-zinc-350">•</span> One lowercase letter
                            </div>
                            <div class="flex items-center gap-1.5" id="req-num">
                                <span class="chk-icon text-zinc-350">•</span> One digit number
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" onclick="window.saveNewPasswordAction()" id="btn-save-new-pass" disabled class="w-full py-2.5 bg-zinc-100 text-zinc-400 font-extrabold rounded-xl text-xs transition-colors cursor-not-allowed border-0 shadow-sm flex items-center justify-center gap-2">
                        <?php echo $is_google_user ? 'Save Account Password' : 'Apply Security Update'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ AVATAR CROP DRAWER SHEET ═════════════════════════════════════════════ -->
<div id="cora-avatar-crop-dlg" class="cora-drawer-overlay" onclick="if(event.target===this) window.closeAvatarCrop();" style="touch-action: manipulation; pointer-events: none;">
    <div class="cora-drawer-sheet" id="avatar-crop-card" onclick="event.stopPropagation();" style="touch-action: manipulation; user-select: text !important; -webkit-user-select: text !important; pointer-events: auto !important;">
        <!-- Mobile Bottom Sheet Drag Handle -->
        <div class="flex sm:hidden items-center justify-center pt-3 pb-1 cursor-pointer select-none" onclick="window.closeAvatarCrop()">
            <div class="w-10 h-1 rounded-full bg-zinc-300"></div>
        </div>

        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-extrabold text-zinc-900">Crop Profile Photo</h3>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors bg-transparent border-0" onclick="window.closeAvatarCrop()">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 flex flex-col items-center justify-center bg-zinc-50/20">
            <div class="relative w-72 h-72 border border-dashed border-zinc-300 rounded-lg overflow-hidden flex items-center justify-center bg-zinc-100">
                <canvas id="crop-canvas" class="max-w-full max-h-full"></canvas>
            </div>
            <p class="text-[10px] text-zinc-400 mt-4 text-center">Drag inside the canvas selection window to adjust position before saving.</p>
        </div>
        
        <div class="p-5 border-t border-zinc-200 bg-zinc-50/50 flex items-center justify-end gap-3 font-extrabold">
            <button type="button" onclick="window.closeAvatarCrop()" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 rounded-lg text-xs transition-colors cursor-pointer shadow-xs border-0">Cancel</button>
            <button type="button" onclick="window.saveCroppedAvatar()" class="px-5 py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded-lg text-xs transition-colors cursor-pointer shadow-xs border-0">Apply Crop</button>
        </div>
    </div>
</div>

<script>
    var isGoogleUser = <?php echo $is_google_user ? 'true' : 'false'; ?>;
    var origImageSrc = null;
    var cropImg = new Image();

    function getCoraAjaxData() {
        return {
            ajaxUrl: (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : (window.ajaxurl || '/wp-admin/admin-ajax.php'),
            ajaxNonce: (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ''
        };
    }

    // Custom Profile Tab Switcher
    window.switchProfileTab = function(tabId) {
        if (typeof jQuery !== 'undefined') {
            jQuery('.profile-pane').removeClass('pane-active');
            jQuery('#' + tabId).addClass('pane-active');
            
            jQuery('.profile-tab-btn').removeClass('tab-active');
            jQuery('#btn-' + tabId).addClass('tab-active');
        }
        
        try { localStorage.setItem('cora_profile_active_tab', tabId); } catch(e) {}
        
        if (tabId === 'tab-activity' && window.paginateActivityLogs) {
            window.paginateActivityLogs();
        }
    };

    // ─── DYNAMIC PAGINATION ENGINE FOR ACTIVITY LOGS ──────────────────────────
    var activityCurrentPage = 1;
    var activityRowsPerPage = 10;
    
    window.paginateActivityLogs = function() {
        if (typeof jQuery === 'undefined') return;
        var rows = jQuery('#activity-logs-table-body tr');
        var totalRows = rows.length;
        
        if (totalRows <= 1 && rows.find('td').attr('colspan') !== undefined) {
            jQuery('#activity-pagination-container').hide();
            return;
        }
        
        jQuery('#activity-pagination-container').show();
        var totalPages = Math.ceil(totalRows / activityRowsPerPage);
        if (totalPages < 1) totalPages = 1;
        
        if (activityCurrentPage < 1) activityCurrentPage = 1;
        if (activityCurrentPage > totalPages) activityCurrentPage = totalPages;
        
        rows.hide();
        var start = (activityCurrentPage - 1) * activityRowsPerPage;
        var end = start + activityRowsPerPage;
        
        rows.slice(start, end).show();
        
        var showingStart = start + 1;
        var showingEnd = Math.min(end, totalRows);
        jQuery('#activity-pagination-info').text('Showing ' + showingStart + '-' + showingEnd + ' of ' + totalRows + ' entries (Last 7 Days)');
        
        jQuery('#btn-activity-prev').prop('disabled', activityCurrentPage === 1);
        jQuery('#btn-activity-next').prop('disabled', activityCurrentPage === totalPages);
    };
    
    window.changeActivityPage = function(dir) {
        activityCurrentPage += dir;
        window.paginateActivityLogs();
    };

    // ─── UPGRADED SAAS PASSWORD RESET ENGINE ─────────────────────────────────
    window.openPasswordDrawer = function() {
        if (typeof jQuery !== 'undefined') {
            if (isGoogleUser) {
                jQuery('#pass-step-1').hide();
                jQuery('#pass-step-2').show();
            } else {
                jQuery('#pass-step-1').show();
                jQuery('#pass-step-2').hide();
            }
            jQuery('#drawer-curr-pass').val('');
            jQuery('#drawer-new-pass').val('');
            jQuery('#drawer-confirm-pass').val('');
            
            jQuery('#strength-label').text('Weak').removeClass('text-amber-500 text-emerald-500').addClass('text-zinc-400');
            jQuery('#strength-progress').css('width', '25%').removeClass('bg-amber-500 bg-emerald-500').addClass('bg-red-500');
            jQuery('.chk-icon').text('•').removeClass('text-emerald-500').addClass('text-zinc-350');
            jQuery('#btn-save-new-pass').prop('disabled', true).removeClass('bg-zinc-900 hover:bg-zinc-800 text-white').addClass('bg-zinc-100 text-zinc-400 cursor-not-allowed');
        }

        var drawer = document.getElementById('cora-password-drawer-sheet');
        if (drawer) {
            drawer.classList.add('drawer-open');
        }
        setTimeout(function() {
            var inputToFocus = isGoogleUser ? document.getElementById('drawer-new-pass') : document.getElementById('drawer-curr-pass');
            if (inputToFocus) {
                inputToFocus.focus();
            }
        }, 300);
    };

    window.closePasswordDrawer = function() {
        var drawer = document.getElementById('cora-password-drawer-sheet');
        if (drawer) {
            drawer.classList.remove('drawer-open');
        }
    };

    window.verifyCurrentPasswordStep = function() {
        var curr = (document.getElementById('drawer-curr-pass') || {}).value || '';
        if (!curr) {
            if (window.coraShowToast) window.coraShowToast('Enter your current password.', 'warning');
            return;
        }

        var btn = jQuery('#btn-verify-identity');
        btn.prop('disabled', true).text('Verifying Credentials...');

        var cfg = getCoraAjaxData();
        jQuery.post(cfg.ajaxUrl, {
            action: 'cora_ajax_verify_current_password',
            current_pass: curr,
            nonce: cfg.ajaxNonce
        }, function(res) {
            btn.prop('disabled', false).text('Verify Identity Credentials');
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast('Identity verified successfully.', 'success');
                jQuery('#pass-step-1').fadeOut(200, function() {
                    jQuery('#pass-step-2').fadeIn(200);
                    setTimeout(function() {
                        var p = document.getElementById('drawer-new-pass');
                        if (p) p.focus();
                    }, 250);
                });
            } else {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Incorrect password.', 'error');
            }
        }).fail(function() {
            btn.prop('disabled', false).text('Verify Identity Credentials');
            if (window.coraShowToast) window.coraShowToast('Network error while verifying password.', 'error');
        });
    };

    window.validatePasswordStrength = function() {
        var pass = (document.getElementById('drawer-new-pass') || {}).value || '';
        var confirm = (document.getElementById('drawer-confirm-pass') || {}).value || '';

        var lenVal = pass.length >= 8;
        window.updateCheckmark('req-len', lenVal);

        var upVal = /[A-Z]/.test(pass);
        window.updateCheckmark('req-up', upVal);

        var loVal = /[a-z]/.test(pass);
        window.updateCheckmark('req-lo', loVal);

        var numVal = /[0-9]/.test(pass);
        window.updateCheckmark('req-num', numVal);

        var score = 0;
        if (lenVal) score++;
        if (upVal) score++;
        if (loVal) score++;
        if (numVal) score++;

        var strengthProgress = jQuery('#strength-progress');
        var strengthLabel = jQuery('#strength-label');

        if (score <= 1) {
            strengthLabel.text('Weak').removeClass('text-amber-500 text-emerald-500').addClass('text-zinc-400');
            strengthProgress.css('width', '25%').removeClass('bg-amber-500 bg-emerald-500').addClass('bg-red-500');
        } else if (score < 4) {
            strengthLabel.text('Medium').removeClass('text-zinc-400 text-emerald-500').addClass('text-amber-500');
            strengthProgress.css('width', '60%').removeClass('bg-red-500 bg-emerald-500').addClass('bg-amber-500');
        } else {
            strengthLabel.text('Strong').removeClass('text-zinc-400 text-amber-500').addClass('text-emerald-500');
            strengthProgress.css('width', '100%').removeClass('bg-red-500 bg-amber-500').addClass('bg-emerald-500');
        }

        var isMatch = pass === confirm && pass.length > 0;
        var isValid = lenVal && upVal && loVal && numVal && isMatch;

        var saveBtn = jQuery('#btn-save-new-pass');
        if (isValid) {
            saveBtn.prop('disabled', false)
                .removeClass('bg-zinc-100 text-zinc-400 cursor-not-allowed')
                .addClass('bg-zinc-900 hover:bg-zinc-800 text-white cursor-pointer');
        } else {
            saveBtn.prop('disabled', true)
                .removeClass('bg-zinc-900 hover:bg-zinc-800 text-white cursor-pointer')
                .addClass('bg-zinc-100 text-zinc-400 cursor-not-allowed');
        }
    };

    window.updateCheckmark = function(id, isValid) {
        var el = jQuery('#' + id);
        var chk = el.find('.chk-icon');
        if (isValid) {
            chk.text('✓').removeClass('text-zinc-350').addClass('text-emerald-500');
            el.removeClass('text-zinc-500').addClass('text-zinc-800');
        } else {
            chk.text('•').removeClass('text-emerald-500').addClass('text-zinc-350');
            el.removeClass('text-zinc-800').addClass('text-zinc-500');
        }
    };

    window.saveNewPasswordAction = function() {
        var curr = isGoogleUser ? 'google_oauth_bypass' : (document.getElementById('drawer-curr-pass') || {}).value || '';
        var pass = (document.getElementById('drawer-new-pass') || {}).value || '';
        var btn = jQuery('#btn-save-new-pass');

        btn.prop('disabled', true).text('Updating Security Credentials...');
        if (window.coraShowToast) window.coraShowToast('Updating your workspace password...', 'info');

        var cfg = getCoraAjaxData();
        jQuery.post(cfg.ajaxUrl, {
            action: 'cora_ajax_change_password',
            current_pass: curr,
            new_pass: pass,
            nonce: cfg.ajaxNonce
        }, function(res) {
            btn.prop('disabled', false).text(isGoogleUser ? 'Save Account Password' : 'Apply Security Update');
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Password updated successfully.', 'success');
                window.closePasswordDrawer();
            } else {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Failed to update password.', 'error');
            }
        }).fail(function() {
            btn.prop('disabled', false).text(isGoogleUser ? 'Save Account Password' : 'Apply Security Update');
            if (window.coraShowToast) window.coraShowToast('Network error while updating password.', 'error');
        });
    };

    // Update Status Broadcast Handler
    window.coraUpdateUserStatus = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        var status = jQuery('#user-status-select').val();
        var msg = jQuery('#user-status-msg').val().trim();
        
        if (window.coraShowToast) window.coraShowToast('Broadcasting status update...', 'info');
        
        var cfg = getCoraAjaxData();
        jQuery.post(cfg.ajaxUrl, {
            action: 'cora_ajax_update_user_custom_status',
            status: status,
            message: msg,
            nonce: cfg.ajaxNonce
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast('Status broadcast updated successfully.', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Failed to update status.', 'error');
            }
        });
    };

    // Submit Leave Request Facility Handler
    window.coraRequestLeave = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        var type = jQuery('#leave-type').val();
        var start = jQuery('#leave-start').val();
        var end = jQuery('#leave-end').val();
        var reason = jQuery('#leave-reason').val().trim();
        
        if (window.coraShowToast) window.coraShowToast('Submitting leave request...', 'info');
        
        var cfg = getCoraAjaxData();
        jQuery.post(cfg.ajaxUrl, {
            action: 'cora_ajax_request_leave',
            type: type,
            start_date: start,
            end_date: end,
            reason: reason,
            nonce: cfg.ajaxNonce
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast('Leave request submitted successfully.', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Failed to submit leave request.', 'error');
            }
        });
    };

    // Approve/Reject Leave Status Updates (Admins Only)
    window.coraUpdateLeaveStatus = function(leaveId, status) {
        if (window.coraShowToast) window.coraShowToast('Updating leave request status...', 'info');
        
        var cfg = getCoraAjaxData();
        jQuery.post(cfg.ajaxUrl, {
            action: 'cora_ajax_update_leave_status',
            leave_id: leaveId,
            status: status,
            nonce: cfg.ajaxNonce
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast('Leave request updated successfully.', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Failed to update leave.', 'error');
            }
        });
    };

    // Clock In/Out punches with geofencing validation
    window.coraPunchAttendance = function(type) {
        var statusDiv = jQuery('#punch-status-text');
        statusDiv.removeClass('text-red-500 text-emerald-500').addClass('text-zinc-500').text('Acquiring verified location...').show();
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var logData = {
                    type: type,
                    timestamp: Date.now(),
                    lat: lat,
                    lng: lng,
                    user: 'Current User'
                };
                
                var cfg = getCoraAjaxData();
                jQuery.post(cfg.ajaxUrl, {
                    action: 'cora_save_attendance',
                    nonce: cfg.ajaxNonce,
                    log: JSON.stringify(logData)
                }, function(res) {
                    if (res.success) {
                        if (window.coraShowToast) window.coraShowToast("Punch logged successfully", "success");
                        statusDiv.removeClass('text-zinc-500').addClass('text-emerald-500').text('Logged punch successfully. Reloading...');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        statusDiv.removeClass('text-zinc-500').addClass('text-red-500 ').text(res.data.message || 'Failed to save punch.');
                    }
                });
            }, function(error) {
                statusDiv.removeClass('text-zinc-500').addClass('text-red-500 ').text('Location access denied or unavailable.');
            });
        } else {
            statusDiv.removeClass('text-zinc-500').addClass('text-red-500 ').text('Geolocation is not supported by your browser.');
        }
    };

    window.loadAvatarCrop = function(e) {
        var file = e.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            if (window.coraShowToast) window.coraShowToast('File is too large. Max size is 2MB.', 'error');
            return;
        }

        var reader = new FileReader();
        reader.onload = function(event) {
            origImageSrc = event.target.result;
            cropImg.onload = function() {
                var canvas = document.getElementById('crop-canvas');
                if (canvas) {
                    canvas.width = 300;
                    canvas.height = 300;
                    var ctx = canvas.getContext('2d');
                    if (ctx) {
                        var size = Math.min(cropImg.width, cropImg.height);
                        var x = (cropImg.width - size) / 2;
                        var y = (cropImg.height - size) / 2;
                        ctx.drawImage(cropImg, x, y, size, size, 0, 0, 300, 300);
                    }
                }
                var drawer = document.getElementById('cora-avatar-crop-dlg');
                if (drawer) {
                    drawer.classList.add('drawer-open');
                }
            };
            cropImg.src = origImageSrc;
        };
        reader.readAsDataURL(file);
    };

    window.closeAvatarCrop = function() {
        var drawer = document.getElementById('cora-avatar-crop-dlg');
        if (drawer) {
            drawer.classList.remove('drawer-open');
        }
        var inp = document.getElementById('avatar-input');
        if (inp) inp.value = '';
    };

    window.saveCroppedAvatar = function() {
        var canvas = document.getElementById('crop-canvas');
        if (!canvas) return;
        var dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        window.closeAvatarCrop();
        if (window.coraShowToast) window.coraShowToast('Saving profile photo...', 'info');

        var cfg = getCoraAjaxData();
        jQuery.post(cfg.ajaxUrl, {
            action: 'cora_ajax_update_avatar',
            avatar_data: dataUrl,
            nonce: cfg.ajaxNonce
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast('Profile photo updated.', 'success');
                jQuery('#profile-avatar-img').attr('src', res.data.url).show();
                jQuery('#profile-avatar-fallback').hide();
                jQuery('.cora-user-profile img').attr('src', res.data.url);
            } else {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Failed to save avatar.', 'error');
            }
        });
    };

    window.coraSaveProfileInfo = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        var fname = jQuery('#profile-first-name').val().trim();
        var lname = jQuery('#profile-last-name').val().trim();
        var phone = jQuery('#profile-phone').val().trim();

        var cfg = getCoraAjaxData();
        jQuery.post(cfg.ajaxUrl, {
            action: 'cora_ajax_save_profile_info',
            first_name: fname,
            last_name: lname,
            phone: phone,
            nonce: cfg.ajaxNonce
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast('Profile updated successfully.', 'success');
            } else {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Failed to update profile.', 'error');
            }
        });
    };

    window.coraLogOutOtherSessions = function() {
        const performLogout = function() {
            if (window.coraShowToast) window.coraShowToast('Logging out other devices...', 'info');
            var cfg = getCoraAjaxData();
            jQuery.post(cfg.ajaxUrl, {
                action: 'cora_ajax_logout_other_sessions',
                nonce: cfg.ajaxNonce
            }, function(res) {
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast('Successfully logged out other devices.', 'success');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (window.coraShowToast) window.coraShowToast('Logout failed.', 'error');
                }
            });
        };

        if (window.coraConfirmAction) {
            window.coraConfirmAction(
                'Log Out Other Sessions',
                'Are you sure you want to log out all other devices?',
                performLogout
            );
        } else {
            performLogout();
        }
    };

    jQuery(document).ready(function($) {
        var activeTab = 'tab-info';
        try {
            activeTab = localStorage.getItem('cora_profile_active_tab') || 'tab-info';
        } catch(e) {}
        if (window.switchProfileTab) {
            window.switchProfileTab(activeTab);
        }

        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('force_password_change') && !isGoogleUser) {
            setTimeout(function() {
                if (window.coraShowToast) {
                    window.coraShowToast('For security reasons, you must change your password to continue.', 'error');
                }
                if (typeof window.openPasswordDrawer === 'function') {
                    window.openPasswordDrawer();
                }
            }, 500);
        }
    });
</script>
