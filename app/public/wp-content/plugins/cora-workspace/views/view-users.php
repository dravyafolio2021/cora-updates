<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_user_id = get_current_user_id();
$current_role = ! empty( wp_get_current_user()->roles ) ? wp_get_current_user()->roles[0] : '';
$current_agency = cora_get_current_user_agency_id();
$current_branch = cora_get_current_user_branch_id();

// Build user roles labels dynamically (including custom roles)
$role_labels = cora_get_all_roles();

// Fetch all users in active agency (multi-tenant scope)
$user_query_args = array();
if ( $current_agency !== 'super' ) {
    $user_query_args['meta_query'] = array(
        array(
            'key'     => 'cora_agency_id',
            'value'   => $current_agency,
            'compare' => '='
        )
    );
}
$all_wp_users = get_users( $user_query_args );

// Filter by branch if current user is branch scoped
$users = array();
foreach ( $all_wp_users as $u ) {
    $u_branch = get_user_meta( $u->ID, 'cora_branch_id', true );
    if ( ! empty( $current_branch ) && $u_branch !== $current_branch ) {
        continue;
    }
    // Set default status if missing
    $status = get_user_meta( $u->ID, 'cora_user_status', true );
    if ( empty( $status ) ) {
        update_user_meta( $u->ID, 'cora_user_status', 'active' );
    }
    $users[] = $u;
}

// Fetch invitations
$pending_invites = cora_db_get_invitations();

// Fetch branches
$branches = cora_db_get_branches();
$agency_branches = $branches;

// Permissions Matrix options
$cora_permissions = get_option( 'cora_role_permissions', array() );
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="cora-page-header flex items-center gap-3">
            <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <div>
                <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">User Management</h1>
                <p class="cora-section-desc text-xs text-zinc-500 mt-1">Add brokerage team members, manage active user accounts, and control workspace permissions.</p>
            </div>
        </div>
        
        <?php if ( cora_is_super_owner() || current_user_can( 'manage_options' ) || in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin', 'cora_manager', 'cora_branch_manager', 'cora_re_broker_owner', 'cora_re_managing_agent', 'cora_studio_owner', 'cora_studio_manager' ) ) ) : ?>
            <button onclick="openInviteDrawer()" class="bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs px-4 py-2 rounded-lg transition-colors cursor-pointer active:scale-95 shadow-sm flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Invite User
            </button>
        <?php endif; ?>
    </div>

    <!-- Sub Navigation Tabs -->
    <div class="cora-sub-tabs-container border-b border-zinc-200 dark:border-zinc-800 flex items-center gap-1.5 overflow-x-auto pb-px shrink-0 select-none no-scrollbar mb-4">
        <button class="cora-sub-tab active flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-semibold border-b-2 border-zinc-950 dark:border-zinc-150 text-zinc-950 dark:text-zinc-150 transition-all cursor-pointer whitespace-nowrap animate-none" data-target="tab-active-members">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            Active Members
        </button>
        <button class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-medium border-b-2 border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap" data-target="tab-pending-invites">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            Pending Invitations
        </button>
        <button class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-medium border-b-2 border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap" data-target="tab-permissions-matrix">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
            Permissions Matrix
        </button>
        <button class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-medium border-b-2 border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap" data-target="tab-attendance-logs">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Attendance Logs
        </button>
        <?php if ( cora_is_super_owner() || current_user_can( 'manage_options' ) || in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin', 'cora_re_broker_owner', 'cora_studio_owner' ) ) ) : ?>
            <button class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-medium border-b-2 border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap" data-target="tab-custom-roles">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Custom Roles
            </button>
        <?php endif; ?>
    </div>

    <!-- TAB 1: ACTIVE MEMBERS -->
    <div id="tab-active-members" class="cora-tab-content space-y-4">
        <!-- Filters Toolbar -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
            <div class="flex flex-row flex-wrap gap-3 items-center flex-1">
                <!-- Search bar -->
                <div class="relative flex-1 min-w-[160px] max-w-xs">
                    <input type="text" id="member-search" oninput="filterActiveMembers()" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 pl-9 pr-3 text-xs bg-white dark:bg-zinc-950 focus:border-zinc-400 focus:outline-none focus:ring-0 text-zinc-900 dark:text-zinc-100 transition-colors" placeholder="Search by name or email...">
                    <div class="absolute left-3 top-0 bottom-0 flex items-center pointer-events-none text-zinc-400">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                </div>
                <!-- Role Filter -->
                <select id="filter-role" onchange="filterActiveMembers()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer focus:border-zinc-400 focus:ring-0 w-32 transition-colors">
                    <option value="">All Roles</option>
                    <?php foreach ( $role_labels as $key => $lbl ) : ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($lbl); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Branch Filter -->
                <select id="filter-branch" onchange="filterActiveMembers()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer focus:border-zinc-400 focus:ring-0 w-32 transition-colors">
                    <option value="">All Branches</option>
                    <?php foreach ( $agency_branches as $b_id => $b ) : ?>
                        <option value="<?php echo esc_attr($b_id); ?>"><?php echo esc_html($b['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Status Filter -->
                <select id="filter-status" onchange="filterActiveMembers()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer focus:border-zinc-400 focus:ring-0 w-32 transition-colors">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex items-center justify-end shrink-0">
                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider" id="member-count-badge"><?php echo count($users); ?> members total</span>
            </div>
        </div>

        <!-- Members Table -->
        <div class="bg-white border border-zinc-200/85 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left" id="active-members-table">
                    <thead class="bg-zinc-50/50">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Email Address</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Role</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Branch</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Joined Date</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        <?php foreach ( $users as $u ) :
                            $u_role = ! empty( $u->roles ) ? $u->roles[0] : 'subscriber';
                            $u_role_lbl = isset( $role_labels[$u_role] ) ? $role_labels[$u_role] : $u_role;
                            $u_branch_id = get_user_meta( $u->ID, 'cora_branch_id', true );
                            $u_branch_lbl = isset( $agency_branches[$u_branch_id] ) ? $agency_branches[$u_branch_id]['name'] : '—';
                            $u_status = get_user_meta( $u->ID, 'cora_user_status', true ) ?: 'active';
                            $u_joined = date( 'd M Y', strtotime( $u->user_registered ) );
                            $avatar = get_user_meta( $u->ID, 'cora_avatar_url', true );
                            
                            $name_initials = '';
                            $words = explode( ' ', $u->display_name );
                            foreach ( $words as $w ) $name_initials .= strtoupper( substr( $w, 0, 1 ) );
                            $name_initials = substr( $name_initials, 0, 2 );
                            $name_color = '#' . substr( md5( $u->display_name ), 0, 6 );
                        ?>
                            <tr class="hover:bg-zinc-50/20 active-member-row" data-name="<?php echo esc_attr(strtolower($u->display_name)); ?>" data-email="<?php echo esc_attr(strtolower($u->user_email)); ?>" data-role="<?php echo esc_attr($u_role); ?>" data-branch="<?php echo esc_attr($u_branch_id); ?>" data-status="<?php echo esc_attr($u_status); ?>">
                                <td class="px-5 py-3 flex items-center gap-3">
                                    <?php if ( ! empty( $avatar ) ) : ?>
                                        <img src="<?php echo esc_url($avatar); ?>" class="w-8 h-8 rounded-full object-cover border border-zinc-200">
                                    <?php else : ?>
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs border border-zinc-200" style="background-color: <?php echo esc_attr($name_color); ?>">
                                            <?php echo esc_html( $name_initials ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="font-bold text-zinc-900"><?php echo esc_html( $u->display_name ); ?></span>
                                </td>
                                <td class="px-5 py-3 text-zinc-500 font-medium"><?php echo esc_html( $u->user_email ); ?></td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 text-zinc-700 whitespace-nowrap select-none">
                                        <?php echo esc_html($u_role_lbl); ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-semibold text-zinc-800"><?php echo esc_html($u_branch_lbl); ?></td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-md select-none <?php echo $u_status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'; ?>">
                                        <?php echo esc_html(ucfirst($u_status)); ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-zinc-400 font-medium"><?php echo esc_html($u_joined); ?></td>
                                <td class="px-5 py-3 text-right">
                                    <button onclick="openEditUserDrawer(<?php echo esc_attr( $u->ID ); ?>, '<?php echo esc_attr($u->display_name); ?>', '<?php echo esc_attr($u_role); ?>', '<?php echo esc_attr($u_branch_id); ?>', '<?php echo esc_attr($u_status); ?>')" class="px-2.5 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 cursor-pointer shadow-sm transition-colors">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: PENDING INVITATIONS -->
    <div id="tab-pending-invites" class="cora-tab-content space-y-4 hidden">
        <div class="bg-white border border-zinc-200/85 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                    <thead class="bg-zinc-50/50">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Email Address</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Role</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Branch</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Expiry</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        <?php if ( empty( $pending_invites ) ) : ?>
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-zinc-400 font-medium">No pending invitations.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $pending_invites as $tok => $inv ) :
                                $invite_role_lbl = isset( $role_labels[$inv['role']] ) ? $role_labels[$inv['role']] : $inv['role'];
                                $invite_branch_lbl = isset( $agency_branches[$inv['branch_id']] ) ? $agency_branches[$inv['branch_id']]['name'] : '—';
                                $expired = time() > intval( $inv['expires_at'] );
                                $status = $inv['status'];
                                if ( $status === 'pending' && $expired ) {
                                    $status = 'expired';
                                }
                                $expiry_date = date( 'd M Y, H:i', $inv['expires_at'] );
                            ?>
                                <tr class="hover:bg-zinc-50/10">
                                    <td class="px-5 py-3 font-bold text-zinc-900"><?php echo esc_html( $inv['first_name'] . ' ' . $inv['last_name'] ); ?></td>
                                    <td class="px-5 py-3 text-zinc-500 font-medium"><?php echo esc_html( $inv['email'] ); ?></td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 text-zinc-700 whitespace-nowrap select-none">
                                            <?php echo esc_html($invite_role_lbl); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 font-semibold text-zinc-800"><?php echo esc_html($invite_branch_lbl); ?></td>
                                    <td class="px-5 py-3 text-zinc-400 font-medium"><?php echo esc_html($expiry_date); ?></td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-md <?php
                                            if ( $status === 'accepted' ) echo 'bg-emerald-50 text-emerald-700';
                                            elseif ( $status === 'pending' ) echo 'bg-amber-50 text-amber-700';
                                            else echo 'bg-zinc-100 text-zinc-500';
                                        ?>">
                                            <?php echo esc_html(ucfirst($status)); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <?php if ( $status === 'pending' ) : ?>
                                                <button onclick="coraResendVerification('<?php echo esc_attr($inv['email']); ?>')" class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 hover:bg-zinc-50 cursor-pointer">Resend</button>
                                                <button onclick="coraCancelInvitation('<?php echo esc_attr($tok); ?>', this)" class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-red-600 hover:bg-red-50 hover:border-red-200 cursor-pointer transition-all">Cancel</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: PERMISSIONS MATRIX -->
    <div id="tab-permissions-matrix" class="cora-tab-content space-y-4 hidden">
        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                <h3 class="text-sm font-bold text-zinc-900">Granular Role Permissions Matrix</h3>
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-zinc-550 select-none">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Sync Active
                </div>
            </div>
            <p class="text-[11px] text-zinc-400 -mt-2 leading-relaxed">Determine dashboard screen visibilities for each workspace role. Super Admin permissions are locked globally.</p>
            
            <?php
            $active_industry = cora_get_active_industry();
            if ( $active_industry === 'photography_studio' ) {
                $matrix_columns = array(
                    'dashboard'   => 'Dashboard',
                    'bookings'    => 'Shoots',
                    'team-roles'  => 'Team & Roles',
                    'equipment'   => 'Camera Gear',
                    'financials'  => 'Financials',
                    'portfolio'   => 'Portfolio',
                    'leads'       => 'Client Leads',
                    'settings'    => 'Settings'
                );
            } else {
                $matrix_columns = array(
                    'dashboard'   => 'Dashboard',
                    'bookings'    => 'Showings CRM',
                    'feature-hub' => 'Feature Hub',
                    'team-roles'  => 'Team & Roles',
                    'equipment'   => 'Equipment',
                    'financials'  => 'Financials',
                    'settings'    => 'Settings'
                );
            }
            ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left" id="cora-permissions-matrix-table">
                    <thead>
                        <tr class="bg-zinc-50/50">
                            <th class="px-4 py-2.5 font-bold text-zinc-550 uppercase tracking-wider text-[10px]">Role Title</th>
                            <?php foreach ( $matrix_columns as $col_key => $col_lbl ) : ?>
                                <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center"><?php echo esc_html( $col_lbl ); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-150">
                        <!-- Super Admin row (Locked) -->
                        <tr class="hover:bg-zinc-50/30">
                            <td class="px-4 py-3 font-semibold text-zinc-900">Super Admin</td>
                            <?php foreach ( $matrix_columns as $col_key => $col_lbl ) : ?>
                                <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                            <?php endforeach; ?>
                        </tr>
                        <!-- Custom & Industry roles -->
                        <?php 
                        $all_roles = cora_get_all_roles();
                        $target_roles = array();
                        foreach ( $all_roles as $rk => $rl ) {
                            if ( $rk !== 'administrator' && $rk !== 'cora_shruti' && $rk !== 'cora_super_admin' ) {
                                $target_roles[$rk] = $rl;
                            }
                        }
                        
                        foreach ($target_roles as $role_key => $role_name): 
                            $allowed_features = isset($cora_permissions[$role_key]) ? $cora_permissions[$role_key] : array();
                        ?>
                        <tr class="hover:bg-zinc-50/30 cora-matrix-row" data-role="<?php echo esc_attr($role_key); ?>">
                            <td class="px-4 py-3 font-semibold text-zinc-800"><?php echo esc_html($role_name); ?></td>
                            <?php foreach ($matrix_columns as $feature_key => $feature_label): 
                                $checked = in_array($feature_key, $allowed_features) ? 'checked' : '';
                            ?>
                            <td class="text-center">
                                <input type="checkbox" <?php echo $checked; ?> data-feature="<?php echo esc_attr($feature_key); ?>" class="cora-permission-checkbox accent-zinc-950 cursor-pointer">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: CUSTOM ROLES -->
    <div id="tab-custom-roles" class="cora-tab-content space-y-4 hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Create custom role form -->
            <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4 h-fit">
                <div class="border-b border-zinc-100 pb-2">
                    <h3 class="text-sm font-bold text-zinc-900">Define Custom Role</h3>
                    <p class="text-[11px] text-zinc-400 mt-0.5">Add a tailored role for your brokerage or studio departments.</p>
                </div>
                <form id="create-custom-role-form" onsubmit="handleCreateCustomRole(event)" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-550 uppercase tracking-wider mb-1.5">Role Display Name</label>
                        <input type="text" id="custom-role-name" required placeholder="e.g. Social Media Specialist" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <button type="submit" id="create-role-submit-btn" class="w-full bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs py-2.5 rounded-lg transition-colors cursor-pointer active:scale-95 shadow-sm">
                        Create Role
                    </button>
                </form>
            </div>

            <!-- Custom roles list table -->
            <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4 md:col-span-2">
                <div class="border-b border-zinc-100 pb-2">
                    <h3 class="text-sm font-bold text-zinc-900">Active Custom Roles</h3>
                    <p class="text-[11px] text-zinc-400 mt-0.5">Roles currently registered in this workspace environment.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                        <thead>
                            <tr class="bg-zinc-50/50">
                                <th class="px-4 py-2.5 font-bold text-zinc-550 uppercase tracking-wider text-[10px]">Role Name</th>
                                <th class="px-4 py-2.5 font-bold text-zinc-550 uppercase tracking-wider text-[10px]">System Identifier</th>
                                <th class="px-4 py-2.5 font-bold text-zinc-550 uppercase tracking-wider text-[10px] text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-150">
                            <?php
                            $my_custom_roles = get_option( 'cora_custom_roles', array() );
                            if ( empty( $my_custom_roles ) ) :
                            ?>
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-zinc-400">No custom roles defined yet.</td>
                            </tr>
                            <?php else : ?>
                                <?php foreach ( $my_custom_roles as $cr ) : ?>
                                <tr class="hover:bg-zinc-50/30">
                                    <td class="px-4 py-3 font-semibold text-zinc-800"><?php echo esc_html( $cr['role_name'] ); ?></td>
                                    <td class="px-4 py-3"><code class="text-zinc-500 font-mono"><?php echo esc_html( $cr['role_key'] ); ?></code></td>
                                    <td class="px-4 py-3 text-right">
                                        <button onclick="handleDeleteCustomRole('<?php echo esc_attr( $cr['role_key'] ); ?>', this)" class="text-red-650 hover:text-red-700 font-bold hover:underline cursor-pointer transition-all">Delete</button>
                                    </td>
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

<!-- TAB 5: ATTENDANCE LOGS -->
<div id="tab-attendance-logs" class="cora-tab-content space-y-6 hidden">
    <?php
    $is_attendance_admin = in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin', 'cora_manager' ) );
    
    // Retrieve office location settings
    $office_loc = get_option( 'cora_attendance_office_location', array(
        'lat' => '',
        'lng' => '',
        'address' => '',
        'radius' => 500
    ) );
    ?>

    <?php if ( $is_attendance_admin ) : ?>
        <!-- ANALYTICS CARDS (Admin only) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Active Today -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Active Today</span>
                    <h3 class="text-xl font-extrabold text-zinc-900 dark:text-zinc-100" id="stat-active-today">0</h3>
                </div>
                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 rounded-lg text-zinc-650 dark:text-zinc-350">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                </div>
            </div>
            
            <!-- Card 2: Late Check-ins -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Late Check-ins</span>
                    <h3 class="text-xl font-extrabold text-zinc-900 dark:text-zinc-100" id="stat-late-punches">0</h3>
                </div>
                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 rounded-lg text-zinc-650 dark:text-zinc-350">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
            </div>

            <!-- Card 3: Geofence radius Status -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Geofence Limit</span>
                    <h3 class="text-xs font-bold text-zinc-800 dark:text-zinc-250 truncate max-w-[150px]" id="stat-geofence-status">
                        <?php echo !empty($office_loc['lat']) ? esc_html($office_loc['radius']) . 'm Enforced' : 'Not Configured'; ?>
                    </h3>
                </div>
                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 rounded-lg text-zinc-650 dark:text-zinc-350">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path></svg>
                </div>
            </div>

            <!-- Card 4: Action / Email Test -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div class="space-y-1 flex-1">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Cron Automations</span>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <button onclick="triggerCronAction('admin_report')" class="text-[9px] px-2 py-0.5 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded font-bold text-zinc-750 dark:text-zinc-350 cursor-pointer">Daily Summary</button>
                        <button onclick="triggerCronAction('morning_reminder')" class="text-[9px] px-2 py-0.5 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded font-bold text-zinc-750 dark:text-zinc-350 cursor-pointer">Reminder</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Workspace Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Left Side: User Punch In/Out & Office Settings -->
        <div class="space-y-6">
            
            <!-- Card 1: Log Punch (Available to all users) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-5 shadow-sm space-y-4">
                <div>
                    <h2 class="text-sm font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Log Punch</h2>
                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">Register attendance with verified browser GPS verification.</p>
                </div>
                
                <div class="space-y-2.5">
                    <button id="cora-user-punch-in-btn" class="w-full bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-150 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 py-2.5 rounded-lg text-xs font-semibold shadow-sm transition-all cursor-pointer active:scale-95 flex items-center justify-center gap-2">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        Punch In
                    </button>
                    <button id="cora-user-punch-out-btn" class="w-full bg-white hover:bg-zinc-50 border border-zinc-200 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:bg-zinc-850 text-zinc-900 dark:text-zinc-100 py-2.5 rounded-lg text-xs font-semibold shadow-sm transition-all cursor-pointer active:scale-95 flex items-center justify-center gap-2">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Punch Out
                    </button>
                </div>
                
                <div id="cora-user-punch-status" class="text-[10px] text-center text-zinc-550 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-900 rounded-lg p-2.5 hidden"></div>
            </div>

            <?php if ( $is_attendance_admin ) : ?>
                <!-- Card 2: Office Geofencing Settings (Admin only) -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-5 shadow-sm space-y-4">
                    <div>
                        <h2 class="text-sm font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Office Geofencing</h2>
                        <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">Paste a Google Maps link to automatically calculate coordinates and enforce a 500m geofence.</p>
                    </div>
                    
                    <form onsubmit="handleSaveOfficeLocation(event)" class="space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Google Maps URL</label>
                            <input type="url" id="office-maps-url" required placeholder="https://maps.app.goo.gl/... or google.com/maps..." class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none focus:ring-0">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Latitude</label>
                                <input type="text" id="office-lat" readonly value="<?php echo esc_attr( $office_loc['lat'] ); ?>" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs bg-zinc-50 dark:bg-zinc-900 text-zinc-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Longitude</label>
                                <input type="text" id="office-lng" readonly value="<?php echo esc_attr( $office_loc['lng'] ); ?>" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs bg-zinc-50 dark:bg-zinc-900 text-zinc-500 focus:outline-none">
                            </div>
                        </div>
                        <button type="submit" id="save-office-loc-btn" class="w-full bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-150 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 py-2 rounded-lg text-xs font-semibold shadow-sm transition-all cursor-pointer flex items-center justify-center gap-1.5">
                            Verify & Update Location
                        </button>
                    </form>
                    
                    <?php if ( ! empty( $office_loc['lat'] ) ) : ?>
                        <div class="text-[10px] text-zinc-500 border-t border-zinc-100 dark:border-zinc-800 pt-3 flex items-start gap-1">
                            <svg class="w-3.5 h-3.5 text-zinc-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <div>
                                <span class="font-bold text-zinc-700 dark:text-zinc-300">Enforced Location:</span>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($office_loc['lat'] . ',' . $office_loc['lng']); ?>" target="_blank" class="underline hover:text-zinc-800 dark:hover:text-zinc-100 block mt-0.5">
                                    <?php echo esc_html( $office_loc['lat'] . ', ' . $office_loc['lng'] ); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
        
        <!-- Right Side: Recent Attendance History Logs -->
        <div class="lg:col-span-2 space-y-4">
            
            <!-- Search & Control Header -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
                <div class="relative w-full sm:w-64">
                    <input type="text" id="attendance-log-search" oninput="filterAttendanceLogs()" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 pl-9 pr-3 text-xs bg-white dark:bg-zinc-950 focus:border-zinc-400 focus:outline-none focus:ring-0 text-zinc-900 dark:text-zinc-100 transition-colors" placeholder="Filter by employee name...">
                    <div class="absolute left-3 top-0 bottom-0 flex items-center pointer-events-none text-zinc-400">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <?php if ( $is_attendance_admin ) : ?>
                        <button onclick="exportAttendanceReport()" class="h-9 px-3 border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg text-xs font-bold text-zinc-850 dark:text-zinc-300 transition-colors flex items-center gap-1.5 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Export CSV
                        </button>
                    <?php endif; ?>
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Punch Log History</span>
                </div>
            </div>
            
            <!-- Table card wrapper -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/85 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-xs text-left">
                        <thead class="bg-zinc-50/50 dark:bg-zinc-950">
                            <tr>
                                <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">User Name</th>
                                <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Date & Time</th>
                                <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Event Type</th>
                                <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">GPS Coordinates</th>
                                <?php if ( $is_attendance_admin ) : ?>
                                    <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Geofence Status</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="cora-user-attendance-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800 text-zinc-700 dark:text-zinc-300">
                            <tr>
                                <td colspan="<?php echo $is_attendance_admin ? '5' : '4'; ?>" class="px-5 py-8 text-center text-zinc-400 dark:text-zinc-500 font-medium">Loading punch records...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
        
    </div>
</div>

<!-- ═══ INVITE USER DRAWER SHEET ═════════════════════════════════════════════ -->
<div id="drawer-invite-user" class="fixed inset-0 z-[99999] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="absolute inset-0 bg-zinc-950/40" onclick="closeInviteDrawer()"></div>
    <div class="relative z-10 bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-invite-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-bold text-zinc-900">Invite Brokerage Member</h3>
            <button class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeInviteDrawer()">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <form onsubmit="handleSendInvite(event)" class="flex-1 overflow-y-auto p-6 space-y-5">
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1.5">First Name</label>
                <input type="text" id="invite-first-name" required placeholder="e.g. Vikas" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1.5">Last Name</label>
                <input type="text" id="invite-last-name" required placeholder="e.g. Mehta" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1.5">Email Address</label>
                <input type="email" id="invite-email" required placeholder="name@agency.com" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1.5">Operational Role</label>
                <select id="invite-role" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <?php
                    $active_industry = get_option( 'cora_workspace_industry', 'real_estate' );
                    $module = Cora_Module_Registry::get_module( $active_industry );
                    $roles_list = $module ? $module->get_industry_roles() : array();
                    
                    if ( $current_role === 'administrator' || $current_role === 'cora_manager' ) {
                        echo '<option value="cora_branch_manager">Branch Manager</option>';
                    }
                    foreach ( $roles_list as $role_key => $role_label ) {
                        if ( in_array( $role_key, array( 'administrator', 'cora_manager' ) ) ) {
                            continue;
                        }
                        echo '<option value="' . esc_attr( $role_key ) . '">' . esc_html( $role_label ) . '</option>';
                    }
                    ?>
                    <option value="cora_viewer">Viewer</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1.5">Assign Branch</label>
                <select id="invite-branch" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer" <?php echo ! empty( $current_branch ) ? 'disabled' : ''; ?>>
                    <?php foreach ( $agency_branches as $b_id => $b ) : ?>
                        <option value="<?php echo esc_attr($b_id); ?>" <?php selected( $current_branch, $b_id ); ?>><?php echo esc_html($b['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="pt-4">
                <button type="submit" id="send-invite-btn" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Send Invitation Link</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ EDIT USER DRAWER SHEET ═══════════════════════════════════════════════ -->
<div id="drawer-edit-user" class="fixed inset-0 z-[99999] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="absolute inset-0 bg-zinc-950/40" onclick="closeEditUserDrawer()"></div>
    <div class="relative z-10 bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-edit-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-bold text-zinc-900">Edit Crew Member</h3>
            <button class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeEditUserDrawer()">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <form onsubmit="handleSaveEditUser(event)" class="flex-1 overflow-y-auto p-6 space-y-5">
            <input type="hidden" id="edit-user-id">
            
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1.5">Display Name</label>
                <input type="text" id="edit-display-name" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1.5">Operational Role</label>
                <select id="edit-role" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <?php
                    $active_industry = get_option( 'cora_workspace_industry', 'real_estate' );
                    $module = Cora_Module_Registry::get_module( $active_industry );
                    $roles_list = $module ? $module->get_industry_roles() : array();
                    
                    if ( $current_role === 'administrator' || $current_role === 'cora_manager' ) {
                        echo '<option value="cora_branch_manager">Branch Manager</option>';
                    }
                    foreach ( $roles_list as $role_key => $role_label ) {
                        if ( in_array( $role_key, array( 'administrator', 'cora_manager' ) ) ) {
                            continue;
                        }
                        echo '<option value="' . esc_attr( $role_key ) . '">' . esc_html( $role_label ) . '</option>';
                    }
                    ?>
                    <option value="cora_viewer">Viewer</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1.5">Assign Branch</label>
                <select id="edit-branch" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <?php foreach ( $agency_branches as $b_id => $b ) : ?>
                        <option value="<?php echo esc_attr($b_id); ?>"><?php echo esc_html($b['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Active / Inactive Status Switch -->
            <div class="flex items-center justify-between border-t border-zinc-100 pt-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-800">Account Status</label>
                    <p class="text-[10px] text-zinc-400 mt-0.5">Deactivating instantly terminates all active sessions.</p>
                </div>
                <div class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="edit-status-toggle" onchange="handleStatusToggleChange(this)" class="sr-only peer">
                    <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950"></div>
                </div>
            </div>

            <!-- Leads Reassignment Panel (Conditionally displayed when deactivating a user with leads) -->
            <div id="leads-reassignment-panel" class="hidden bg-[#fafaf9] border border-zinc-200/80 rounded-xl p-4 space-y-3">
                <div class="flex items-start gap-2 text-zinc-900">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <div>
                        <h4 class="text-xs font-bold" id="leads-warning-title">Active Leads Pending Reassignment</h4>
                        <p class="text-[10px] text-zinc-550 mt-0.5">This agent currently manages <span id="leads-count-warning" class="font-bold">0</span> open leads. Choose a teammate to reassign them to.</p>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Reassign To</label>
                    <select id="reassign-leads-to" class="w-full border border-zinc-200 rounded-lg px-2 py-1 text-xs bg-white text-zinc-800 outline-none">
                        <option value="">Leave Unassigned (Mark as Unassigned)</option>
                        <?php foreach ( $users as $u ) : ?>
                            <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="pt-4">
                <button type="submit" id="save-edit-btn" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tab switching
    $('.cora-sub-tab').on('click', function() {
        $('.cora-sub-tab')
            .removeClass('active border-zinc-950 dark:border-zinc-150 text-zinc-950 dark:text-zinc-150 font-semibold')
            .addClass('border-transparent text-zinc-550 dark:text-zinc-400 font-medium');
        $(this)
            .addClass('active border-zinc-950 dark:border-zinc-150 text-zinc-950 dark:text-zinc-150 font-semibold')
            .removeClass('border-transparent text-zinc-550 dark:text-zinc-400 font-medium');
        
        $('.cora-tab-content').addClass('hidden');
        $('#' + $(this).data('target')).removeClass('hidden');
    });

    function filterActiveMembers() {
        var q = $('#member-search').val().toLowerCase();
        var role = $('#filter-role').val();
        var branch = $('#filter-branch').val();
        var status = $('#filter-status').val();
        
        var count = 0;
        $('.active-member-row').each(function() {
            var rowName = $(this).data('name') || '';
            var rowEmail = $(this).data('email') || '';
            var rowRole = $(this).data('role') || '';
            var rowBranch = $(this).data('branch') || '';
            var rowStatus = $(this).data('status') || '';
            
            var matchQ = !q || rowName.indexOf(q) !== -1 || rowEmail.indexOf(q) !== -1;
            var matchRole = !role || rowRole === role;
            var matchBranch = !branch || rowBranch === branch;
            var matchStatus = !status || rowStatus === status;
            
            if (matchQ && matchRole && matchBranch && matchStatus) {
                $(this).show();
                count++;
            } else {
                $(this).hide();
            }
        });
        $('#member-count-badge').text(count + ' members total');
    }

    // Invite user drawer
    function openInviteDrawer() {
        $('#drawer-invite-user').removeClass('opacity-0 pointer-events-none');
        $('#drawer-invite-user').css({'opacity': '1', 'pointer-events': 'auto'});
        $('#drawer-invite-card').removeClass('translate-x-full').addClass('translate-x-0');
    }

    function closeInviteDrawer() {
        $('#drawer-invite-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            $('#drawer-invite-user').addClass('opacity-0 pointer-events-none');
            $('#drawer-invite-user').css({'opacity': '0', 'pointer-events': 'none'});
        }, 300);
        $('#invite-first-name').val('');
        $('#invite-last-name').val('');
        $('#invite-email').val('');
    }

    function handleSendInvite(e) {
        e.preventDefault();
        var fname = $('#invite-first-name').val().trim();
        var lname = $('#invite-last-name').val().trim();
        var email = $('#invite-email').val().trim();
        var role = $('#invite-role').val();
        var branch = $('#invite-branch').val();

        $('#send-invite-btn').prop('disabled', true).text('Sending invitation...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_send_invitation',
            first_name: fname,
            last_name: lname,
            email: email,
            role: role,
            branch_id: branch,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Invitation sent successfully.');
                closeInviteDrawer();
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(res.data.message || 'Failed to send invitation.');
                $('#send-invite-btn').prop('disabled', false).text('Send Invitation Link');
            }
        }).fail(function() {
            window.coraShowToast('Network error.');
            $('#send-invite-btn').prop('disabled', false).text('Send Invitation Link');
        });
    }

    // Edit user drawer
    var currentEditingStatus = 'active';
    
    function openEditUserDrawer(userId, name, role, branch, status) {
        $('#edit-user-id').val(userId);
        $('#edit-display-name').val(name);
        $('#edit-role').val(role);
        $('#edit-branch').val(branch);
        
        currentEditingStatus = status;
        var checked = (status === 'active');
        $('#edit-status-toggle').prop('checked', checked);
        $('#leads-reassignment-panel').addClass('hidden');

        // Hide self-deactivation option
        if (userId === <?php echo $current_user_id; ?>) {
            $('#edit-status-toggle').prop('disabled', true);
        } else {
            $('#edit-status-toggle').prop('disabled', false);
        }

        $('#drawer-edit-user').removeClass('opacity-0 pointer-events-none');
        $('#drawer-edit-user').css({'opacity': '1', 'pointer-events': 'auto'});
        $('#drawer-edit-card').removeClass('translate-x-full').addClass('translate-x-0');
    }

    function closeEditUserDrawer() {
        $('#drawer-edit-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            $('#drawer-edit-user').addClass('opacity-0 pointer-events-none');
            $('#drawer-edit-user').css({'opacity': '0', 'pointer-events': 'none'});
        }, 300);
    }

    function handleStatusToggleChange(el) {
        var active = $(el).is(':checked');
        var userId = $('#edit-user-id').val();
        
        if (!active) {
            // Check if user has open leads
            $.post(coraREData.ajaxUrl, {
                action: 'cora_ajax_get_user_leads_count',
                user_id: userId,
                nonce: coraREData.ajaxNonce
            }, function(res) {
                if (res.success && res.data.leads_count > 0) {
                    $('#leads-count-warning').text(res.data.leads_count);
                    // Filter dropdown to hide current editing user
                    $('#reassign-leads-to option').show();
                    $('#reassign-leads-to option[value="' + userId + '"]').hide();
                    $('#leads-reassignment-panel').removeClass('hidden');
                } else {
                    $('#leads-reassignment-panel').addClass('hidden');
                }
            });
        } else {
            $('#leads-reassignment-panel').addClass('hidden');
        }
    }

    function handleSaveEditUser(e) {
        e.preventDefault();
        var userId = $('#edit-user-id').val();
        var name = $('#edit-display-name').val().trim();
        var role = $('#edit-role').val();
        var branch = $('#edit-branch').val();
        var active = $('#edit-status-toggle').is(':checked');
        var status = active ? 'active' : 'inactive';
        var reassignTo = $('#reassign-leads-to').val();

        $('#save-edit-btn').prop('disabled', true).text('Saving shifts...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_user_changes',
            user_id: userId,
            display_name: name,
            role: role,
            branch_id: branch,
            status: status,
            reassign_to: reassignTo,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('User updated successfully.');
                closeEditUserDrawer();
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(res.data.message || 'Failed to update user.');
                $('#save-edit-btn').prop('disabled', false).text('Save Changes');
            }
        });
    }

    var coraPendingCancels = {};
    function coraCancelInvitation(token, btn) {
        if (!coraPendingCancels[token]) {
            coraPendingCancels[token] = true;
            if (btn) $(btn).text('Confirm Cancel').addClass('bg-red-50 text-red-700 border-red-300');
            window.coraShowToast('Click Confirm Cancel to revoke this invitation.', 'info');
            setTimeout(function() {
                coraPendingCancels[token] = false;
                if (btn) $(btn).text('Cancel').removeClass('bg-red-50 text-red-700 border-red-300');
            }, 4000);
            return;
        }

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_cancel_invitation',
            token: token,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Invitation cancelled successfully.');
                setTimeout(function() { window.location.reload(); }, 800);
            }
        });
    }

    function coraResendVerification(email) {
        window.coraShowToast('Resending invitation link...');
        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_resend_verification',
            email: email,
            nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Invitation link resent.');
            } else {
                window.coraShowToast(res.data.message);
            }
        });
    }

    // Live Sync Permissions Matrix
    $('.cora-permission-checkbox').on('change', function() {
        var tr = $(this).closest('.cora-matrix-row');
        var role = tr.data('role');
        
        var allowed = [];
        tr.find('.cora-permission-checkbox:checked').each(function() {
            allowed.push($(this).data('feature'));
        });

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_role_permissions',
            role_key: role,
            features: allowed,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Role permissions synchronized.');
            }
        });
    });

    // Custom Roles Handlers
    function handleCreateCustomRole(e) {
        e.preventDefault();
        var name = $('#custom-role-name').val().trim();
        if (!name) return;

        var btn = $('#create-role-submit-btn');
        btn.prop('disabled', true).text('Creating role...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_create_custom_role',
            role_name: name,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Custom role created successfully.');
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                window.coraShowToast(res.data.message || 'Failed to create role.');
                btn.prop('disabled', false).text('Create Role');
            }
        });
    }

    var coraPendingRoleDeletes = {};
    function handleDeleteCustomRole(roleKey, btn) {
        if (!coraPendingRoleDeletes[roleKey]) {
            coraPendingRoleDeletes[roleKey] = true;
            if (btn) $(btn).text('Confirm Delete').addClass('text-red-700 underline font-extrabold');
            window.coraShowToast('Click Confirm Delete to remove this custom role.', 'info');
            setTimeout(function() {
                coraPendingRoleDeletes[roleKey] = false;
                if (btn) $(btn).text('Delete').removeClass('text-red-700 underline font-extrabold');
            }, 4000);
            return;
        }

        $.post(coraREData.ajaxUrl, {
            action: 'cora_delete_custom_role',
            role_key: roleKey,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Custom role deleted.');
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                window.coraShowToast(res.data.message || 'Failed to delete role.');
            }
        });
    }

    // ==========================================
    // ATTENDANCE LOGS TAB LOGIC
    // ==========================================
    var isAttendanceAdmin = <?php echo $is_attendance_admin ? 'true' : 'false'; ?>;
    var loggedInUserName = <?php echo json_encode( wp_get_current_user()->display_name ); ?>;

    function fetchAttendanceLogs() {
        $.post(coraREData.ajaxUrl, { 
            action: 'cora_fetch_attendance', 
            nonce: coraREData.ajaxNonce 
        }, function(res) {
            if (res.success && res.data.logs) {
                var tbody = $('#cora-user-attendance-table-body');
                tbody.empty();
                
                // Filter logs client-side if the user is not an admin/manager (privacy protection)
                var displayLogs = res.data.logs;
                if (!isAttendanceAdmin) {
                    displayLogs = res.data.logs.filter(function(log) {
                        return (log.user || '').toLowerCase() === loggedInUserName.toLowerCase();
                    });
                }
                
                if (displayLogs.length === 0) {
                    var colCount = isAttendanceAdmin ? 5 : 4;
                    tbody.append('<tr><td colspan="' + colCount + '" class="px-5 py-8 text-center text-zinc-400 dark:text-zinc-500">No attendance records found.</td></tr>');
                } else {
                    displayLogs.slice().reverse().forEach(function(log) {
                        var dateObj = new Date(log.timestamp);
                        var timeStr = dateObj.toLocaleString();
                        var typeLabel = log.type === 'in' 
                            ? '<span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 rounded-md text-[9px] font-bold uppercase select-none">Punch In</span>' 
                            : '<span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-400 rounded-md text-[9px] font-bold uppercase select-none">Punch Out</span>';
                        
                        var locLink = 'Unknown';
                        if (log.lat && log.lng) {
                            var mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' + log.lat + ',' + log.lng;
                            locLink = '<a href="' + mapsUrl + '" target="_blank" class="hover:underline flex items-center gap-1 text-zinc-500 hover:text-zinc-850 dark:hover:text-zinc-100"><svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg> ' + parseFloat(log.lat).toFixed(4) + ', ' + parseFloat(log.lng).toFixed(4) + '</a>';
                        }
                        
                        var geofenceCell = '';
                        if (isAttendanceAdmin) {
                            var geofenceLabel = '—';
                            if (log.geofence === 'verified') {
                                geofenceLabel = '<span class="px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 rounded text-[9px] font-bold">Verified (' + (log.distance || '') + ')</span>';
                            } else if (log.geofence === 'disabled') {
                                geofenceLabel = '<span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 rounded text-[9px] font-medium">Standard</span>';
                            }
                            geofenceCell = '<td class="px-5 py-3">' + geofenceLabel + '</td>';
                        }
                        
                        tbody.append(`
                            <tr class="hover:bg-zinc-50/30 dark:hover:bg-zinc-850/30 transition-colors cora-attendance-row" data-user="${(log.user || '').toLowerCase()}">
                                <td class="px-5 py-3 font-bold text-zinc-900 dark:text-zinc-100">${log.user}</td>
                                <td class="px-5 py-3 text-zinc-500 dark:text-zinc-400 font-medium">${timeStr}</td>
                                <td class="px-5 py-3">${typeLabel}</td>
                                <td class="px-5 py-3 font-semibold">${locLink}</td>
                                ${geofenceCell}
                            </tr>
                        `);
                    });
                }
                
                // Compute analytics metrics dynamically for admins/managers
                if (isAttendanceAdmin) {
                    var uniqueUsersToday = {};
                    var latePunchesToday = 0;
                    var todayStart = new Date();
                    todayStart.setHours(0,0,0,0);
                    var todayEnd = new Date();
                    todayEnd.setHours(23,59,59,999);
                    
                    res.data.logs.forEach(function(log) {
                        var logTime = new Date(log.timestamp);
                        if (logTime >= todayStart && logTime <= todayEnd) {
                            uniqueUsersToday[log.user] = true;
                            
                            if (log.type === 'in') {
                                var hours = logTime.getHours();
                                var minutes = logTime.getMinutes();
                                // Checked in after 9:30 AM
                                if (hours > 9 || (hours === 9 && minutes > 30)) {
                                    latePunchesToday++;
                                }
                            }
                        }
                    });
                    
                    $('#stat-active-today').text(Object.keys(uniqueUsersToday).length);
                    $('#stat-late-punches').text(latePunchesToday);
                }
            }
        });
    }

    function logUserPunch(type) {
        var statusDiv = $('#cora-user-punch-status');
        statusDiv.removeClass('hidden text-red-500 dark:text-red-400').addClass('text-zinc-500 dark:text-zinc-400').text('Acquiring browser GPS location...').show();
        
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
                
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_save_attendance',
                    nonce: coraREData.ajaxNonce,
                    log: JSON.stringify(logData)
                }, function(res) {
                    if (res.success) {
                        window.coraShowToast("Punch logged successfully");
                        statusDiv.hide();
                        fetchAttendanceLogs();
                    } else {
                        statusDiv.removeClass('text-zinc-500').addClass('text-red-500 dark:text-red-400').text(res.data.message || 'Failed to save punch.');
                    }
                });
            }, function(error) {
                statusDiv.removeClass('text-zinc-500').addClass('text-red-500 dark:text-red-400').text('Location access denied or unavailable.');
            });
        } else {
            statusDiv.removeClass('text-zinc-500').addClass('text-red-500 dark:text-red-400').text('Geolocation is not supported by your browser.');
        }
    }

    function filterAttendanceLogs() {
        var q = $('#attendance-log-search').val().toLowerCase();
        $('.cora-attendance-row').each(function() {
            var user = $(this).data('user') || '';
            if (!q || user.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Admin Geofence Settings Submit
    function handleSaveOfficeLocation(e) {
        e.preventDefault();
        var url = $('#office-maps-url').val().trim();
        if (!url) return;
        
        var btn = $('#save-office-loc-btn');
        btn.prop('disabled', true).text('Resolving coordinates...');
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_office_location',
            maps_url: url,
            radius: 500,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            btn.prop('disabled', false).text('Verify & Update Location');
            if (res.success) {
                window.coraShowToast(res.data.message);
                $('#office-lat').val(res.data.office_location.lat);
                $('#office-lng').val(res.data.office_location.lng);
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                window.coraShowToast(res.data.message || 'Failed to update location.');
            }
        });
    }

    // Dynamic crons debugger tool action trigger
    function triggerCronAction(action) {
        window.coraShowToast("Triggering email automation...");
        $.post(coraREData.ajaxUrl, {
            action: 'cora_trigger_attendance_automation',
            cron_action: action,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message);
            } else {
                window.coraShowToast(res.data.message || "Failed to trigger cron.");
            }
        });
    }

    // Export Table Records to CSV
    function exportAttendanceReport() {
        $.post(coraREData.ajaxUrl, {
            action: 'cora_fetch_attendance',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success && res.data.logs) {
                var csv = 'User Name,Date & Time,Event Type,Latitude,Longitude,Geofence Status,Enforced Distance\n';
                res.data.logs.forEach(function(log) {
                    var dateObj = new Date(log.timestamp);
                    var dateStr = dateObj.toLocaleString().replace(/,/g, '');
                    var geofence = log.geofence || 'disabled';
                    var dist = log.distance || '—';
                    csv += [
                        '"' + (log.user || '').replace(/"/g, '""') + '"',
                        '"' + dateStr + '"',
                        '"' + (log.type === 'in' ? 'Punch In' : 'Punch Out') + '"',
                        log.lat || '',
                        log.lng || '',
                        '"' + geofence.toUpperCase() + '"',
                        '"' + dist + '"'
                    ].join(',') + '\n';
                });
                
                var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement("a");
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", "cora_attendance_report_" + new Date().toISOString().slice(0, 10) + ".csv");
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.coraShowToast("Report exported successfully.");
            }
        });
    }

    $('#cora-user-punch-in-btn').on('click', function() { logUserPunch('in'); });
    $('#cora-user-punch-out-btn').on('click', function() { logUserPunch('out'); });

    // Hook tab switches to fetch logs dynamically
    $('.cora-sub-tab').on('click', function() {
        if ($(this).data('target') === 'tab-attendance-logs') {
            fetchAttendanceLogs();
        }
    });
</script>
