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
        
        <?php if ( in_array( $current_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) : ?>
            <button onclick="openInviteDrawer()" class="bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs px-4 py-2 rounded-lg transition-colors cursor-pointer active:scale-95 shadow-sm flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Invite User
            </button>
        <?php endif; ?>
    </div>

    <!-- Sub Navigation Tabs -->
        <button class="cora-sub-tab active pb-2 border-b-2 border-zinc-950 text-zinc-950 cursor-pointer" data-target="tab-active-members">Active Members</button>
        <button class="cora-sub-tab pb-2 border-b-2 border-transparent hover:text-zinc-900 cursor-pointer" data-target="tab-pending-invites">Pending Invitations</button>
        <button class="cora-sub-tab pb-2 border-b-2 border-transparent hover:text-zinc-900 cursor-pointer" data-target="tab-permissions-matrix">Permissions Matrix</button>
        <?php if ( in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin' ) ) ) : ?>
            <button class="cora-sub-tab pb-2 border-b-2 border-transparent hover:text-zinc-900 cursor-pointer" data-target="tab-custom-roles">Custom Roles</button>
        <?php endif; ?>
    </div>

    <!-- TAB 1: ACTIVE MEMBERS -->
    <div id="tab-active-members" class="cora-tab-content space-y-4">
        <!-- Filters Toolbar -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-3 items-center flex-1 max-w-2xl">
                <!-- Search bar -->
                <div class="relative w-64">
                    <input type="text" id="member-search" oninput="filterActiveMembers()" class="w-full border border-zinc-200 rounded-lg py-1.5 pl-8 pr-3 text-xs bg-white focus:border-zinc-400 focus:outline-none text-zinc-900" placeholder="Search by name or email...">
                    <span class="absolute left-2.5 top-2.5 text-zinc-400">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                </div>
                <!-- Role Filter -->
                <select id="filter-role" onchange="filterActiveMembers()" class="border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Roles</option>
                    <?php foreach ( $role_labels as $key => $lbl ) : ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($lbl); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Branch Filter -->
                <select id="filter-branch" onchange="filterActiveMembers()" class="border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Branches</option>
                    <?php foreach ( $agency_branches as $b_id => $b ) : ?>
                        <option value="<?php echo esc_attr($b_id); ?>"><?php echo esc_html($b['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Status Filter -->
                <select id="filter-status" onchange="filterActiveMembers()" class="border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider" id="member-count-badge"><?php echo count($users); ?> members total</span>
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
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 text-zinc-700 select-none">
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
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 text-zinc-700">
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
                                                <button onclick="coraCancelInvitation('<?php echo esc_attr($tok); ?>')" class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-red-600 hover:bg-red-50 hover:border-red-200 cursor-pointer">Cancel</button>
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
            <p class="text-[11px] text-zinc-400 -mt-2 leading-relaxed">Determine dashboard screen visibilities for each brokerage role. Super Admin permissions are locked globally.</p>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left" id="cora-permissions-matrix-table">
                    <thead>
                        <tr class="bg-zinc-50/50">
                            <th class="px-4 py-2.5 font-bold text-zinc-550 uppercase tracking-wider text-[10px]">Agent Role</th>
                            <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Dashboard</th>
                            <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Showings CRM</th>
                            <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Feature Hub</th>
                            <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Team & Roles</th>
                            <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Equipment</th>
                            <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Financials</th>
                            <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Settings</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-150">
                        <!-- Super Admin row (Locked) -->
                        <tr class="hover:bg-zinc-50/30">
                            <td class="px-4 py-3 font-semibold text-zinc-900">Super Admin</td>
                            <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                            <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                            <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                            <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                            <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                            <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                            <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                        </tr>
                        <!-- Custom roles -->
                        <?php 
                        $all_roles = cora_get_all_roles();
                        $target_roles = array();
                        foreach ( $all_roles as $rk => $rl ) {
                            if ( $rk !== 'administrator' && $rk !== 'cora_shruti' ) {
                                $target_roles[$rk] = $rl;
                            }
                        }
                        $features = array('dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'settings');
                        
                        foreach ($target_roles as $role_key => $role_name): 
                            $allowed_features = isset($cora_permissions[$role_key]) ? $cora_permissions[$role_key] : array();
                        ?>
                        <tr class="hover:bg-zinc-50/30 cora-matrix-row" data-role="<?php echo esc_attr($role_key); ?>">
                            <td class="px-4 py-3 font-semibold text-zinc-800"><?php echo esc_html($role_name); ?></td>
                            <?php foreach ($features as $feature): 
                                $checked = in_array($feature, $allowed_features) ? 'checked' : '';
                            ?>
                            <td class="text-center">
                                <input type="checkbox" <?php echo $checked; ?> data-feature="<?php echo esc_attr($feature); ?>" class="cora-permission-checkbox accent-zinc-950 cursor-pointer">
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
                    <p class="text-[11px] text-zinc-400 mt-0.5">Add a tailored role for your brokerage departments.</p>
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
                                        <button onclick="handleDeleteCustomRole('<?php echo esc_attr( $cr['role_key'] ); ?>')" class="text-red-650 hover:text-red-700 font-bold hover:underline cursor-pointer">Delete</button>
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

<!-- ═══ INVITE USER DRAWER SHEET ═════════════════════════════════════════════ -->
<div id="drawer-invite-user" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-invite-card">
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
                    <?php if ( $current_role === 'administrator' || $current_role === 'cora_manager' ) : ?>
                        <option value="cora_branch_manager">Branch Manager</option>
                    <?php endif; ?>
                    <option value="cora_photographer">Managing Agent</option>
                    <option value="cora_videographer">Showing Assistant</option>
                    <option value="cora_drone_pilot">Property Valuer</option>
                    <option value="cora_editor">Listing Coordinator</option>
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
<div id="drawer-edit-user" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-edit-card">
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
                    <?php if ( $current_role === 'administrator' || $current_role === 'cora_manager' ) : ?>
                        <option value="cora_branch_manager">Branch Manager</option>
                    <?php endif; ?>
                    <option value="cora_photographer">Managing Agent</option>
                    <option value="cora_videographer">Showing Assistant</option>
                    <option value="cora_drone_pilot">Property Valuer</option>
                    <option value="cora_editor">Listing Coordinator</option>
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
        $('.cora-sub-tab').removeClass('active border-zinc-950 text-zinc-950').addClass('border-transparent text-zinc-550');
        $(this).addClass('active border-zinc-950 text-zinc-950').removeClass('border-transparent text-zinc-550');
        
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

    function coraCancelInvitation(token) {
        if (!confirm('Are you sure you want to cancel this invitation?')) return;
        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_cancel_invitation',
            token: token,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Invitation cancelled.');
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

    function handleDeleteCustomRole(roleKey) {
        if (!confirm('Are you sure you want to delete this custom role? Users assigned to this role will lose access.')) return;

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
</script>
