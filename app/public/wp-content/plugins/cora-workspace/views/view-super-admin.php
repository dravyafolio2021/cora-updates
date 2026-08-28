<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Security: Double-check that only a super owner has access
if ( ! cora_is_super_owner() ) {
    echo '<div class="p-6 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-700 font-bold">Access Denied: Platform Admin only.</div>';
    return;
}

// Pre-fetch dynamic roles list to pass to JavaScript
$roles_list = cora_get_all_roles();

// Parse active sub-page to show correct tab
$active_sub_page = isset( $GLOBALS['sub_page'] ) ? $GLOBALS['sub_page'] : ( isset( $_GET['sub_page'] ) ? sanitize_key( $_GET['sub_page'] ) : 'super-admin' );
if ( empty( $active_sub_page ) || $active_sub_page === 'dashboard' ) {
    $active_sub_page = 'super-admin';
}
?>

<!-- Platform Admin View Container -->
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="cora-page-header flex items-center gap-3">
            <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </span>
            <div>
                <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900 ">Platform Control Panel</h1>
                <p class="cora-section-desc text-xs text-zinc-500 mt-1">Manage tenant workspaces, configure plan assignments, update user accounts, and trigger secure impersonation sessions.</p>
            </div>
        </div>
    </div>

    <!-- Sub Navigation Tabs -->
    <div class="cora-sub-tabs border-b border-zinc-200 flex gap-4 text-xs font-bold text-zinc-500 select-none pb-0.5">
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-admin' ? 'active border-zinc-950 text-zinc-950 ' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-workspaces">
            Workspaces
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-users' ? 'active border-zinc-950 text-zinc-950 ' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-users">
            Users
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-appeals' ? 'active border-zinc-950 text-zinc-950 ' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none flex items-center gap-1.5" data-target="tab-super-appeals">
            Reactivation Appeals <span id="super-appeals-badge" class="px-1.5 py-0.5 rounded-full text-[9.5px] font-bold bg-amber-100 text-amber-800 hidden">0</span>
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-governance' ? 'active border-zinc-950 text-zinc-950 ' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-governance">
            Attendance & Governance
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-announcements' ? 'active border-zinc-950 text-zinc-950 ' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-announcements">
            Broadcast Console
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-health' ? 'active border-zinc-950 text-zinc-950 ' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-health">
            System Health & Metrics
        </button>
    </div>

    <!-- TAB 1: WORKSPACES -->
    <div id="tab-super-workspaces" class="cora-tab-content space-y-4 <?php echo $active_sub_page === 'super-admin' ? '' : 'hidden'; ?>">
        <!-- Filters Toolbar -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-3 items-center flex-1 max-w-2xl">
                <!-- Search bar -->
                <div class="relative w-64">
                    <input type="text" id="workspace-search" oninput="filterWorkspaces()" class="w-full border border-zinc-200 rounded-lg py-1.5 pl-8 pr-3 text-xs bg-white focus:border-zinc-400 focus:outline-none text-zinc-900 " placeholder="Search by name, slug, or owner email...">
                    <span class="absolute left-2.5 top-2.5 text-zinc-450 ">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                </div>
                <!-- Plan Filter -->
                <select id="workspace-filter-plan" onchange="filterWorkspaces()" class="border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Plans</option>
                    <option value="beta">Beta</option>
                    <option value="starter">Starter</option>
                    <option value="pro">Pro</option>
                    <option value="enterprise">Enterprise</option>
                </select>
                <!-- Industry Filter -->
                <select id="workspace-filter-industry" onchange="filterWorkspaces()" class="border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Industries</option>
                    <option value="real_estate">Real Estate</option>
                    <option value="photography_studio">Photography Studio</option>
                </select>
                <!-- Status Filter -->
                <select id="workspace-filter-status" onchange="filterWorkspaces()" class="border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider" id="workspace-count-badge">0 workspaces</span>
                <button onclick="openCreateWorkspaceDrawer()" class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer select-none">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    New Workspace
                </button>
            </div>
        </div>

        <!-- Workspaces Table -->
        <div class="bg-white border border-zinc-200/85 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                    <thead class="bg-zinc-50/50 ">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Workspace Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Subdomain/Slug</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Plan</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Industry</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Owner (email)</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Created Date</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="workspaces-table-body" class="divide-y divide-zinc-100 ">
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-zinc-450 ">Loading workspaces...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: USERS -->
    <div id="tab-super-users" class="cora-tab-content space-y-4 <?php echo $active_sub_page === 'super-users' ? '' : 'hidden'; ?>">
        <!-- Filters Toolbar -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-3 items-center flex-1 max-w-2xl">
                <!-- Search bar -->
                <div class="relative w-64">
                    <input type="text" id="user-search" oninput="filterUsers()" class="w-full border border-zinc-200 rounded-lg py-1.5 pl-8 pr-3 text-xs bg-white focus:border-zinc-400 focus:outline-none text-zinc-900 " placeholder="Search by name, login, or email...">
                    <span class="absolute left-2.5 top-2.5 text-zinc-450 ">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                </div>
                <!-- Role Filter -->
                <select id="user-filter-role" onchange="filterUsers()" class="border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Roles</option>
                    <?php foreach ( $roles_list as $role_key => $role_label ) : ?>
                        <option value="<?php echo esc_attr( $role_key ); ?>"><?php echo esc_html( $role_label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Status Filter -->
                <select id="user-filter-status" onchange="filterUsers()" class="border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider" id="user-count-badge">0 users</span>
        </div>

        <!-- Users Table -->
        <div class="bg-white border border-zinc-200/85 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                    <thead class="bg-zinc-50/50 ">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">User Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Email</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Workspace (Agency name)</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Role</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body" class="divide-y divide-zinc-100 ">
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-zinc-450 ">Loading users...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: REACTIVATION APPEALS -->
    <div id="tab-super-appeals" class="cora-tab-content space-y-4 <?php echo $active_sub_page === 'super-appeals' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-zinc-900 ">Suspension Reactivation Appeals</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Review and manage workspace reactivation requests submitted by suspended users.</p>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider" id="appeals-count-badge">0 appeals</span>
        </div>

        <div class="bg-white border border-zinc-200/85 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                    <thead class="bg-zinc-50/50 ">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Account Email</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Workspace Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Contact Phone</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Reason / Message</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Submitted Date</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="appeals-table-body" class="divide-y divide-zinc-100 ">
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-zinc-450 ">Loading appeals...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: ATTENDANCE & GOVERNANCE -->
    <div id="tab-super-governance" class="cora-tab-content space-y-6 <?php echo $active_sub_page === 'super-governance' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-zinc-200/80 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-zinc-900 ">Cross-Tenant Daily Reports & Automation Controls</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Manually trigger automated end-of-day attendance reports to Workspace Owners or manage global automation triggers.</p>
                </div>
                <button onclick="dispatchSuperDailyReports()" class="px-4 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                    Dispatch Daily Reports Now
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-zinc-100 ">
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-lg space-y-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Scheduled Dispatch</div>
                    <div class="text-sm font-bold text-zinc-900 ">Every Day at 8:00 PM</div>
                    <div class="text-xs text-zinc-500">Automated WP Cron active</div>
                </div>
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-lg space-y-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">SMTP Relay Status</div>
                    <div class="text-sm font-bold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Hostinger Business SMTP Active
                    </div>
                    <div class="text-xs text-zinc-500">heycora@claraverse.in (Port 465 SSL)</div>
                </div>
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-lg space-y-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Geofence Distance Enforcement</div>
                    <div class="text-sm font-bold text-zinc-900 ">Strict Haversine Verified</div>
                    <div class="text-xs text-zinc-500">Real-time GPS coordinate validation</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 5: BROADCAST ANNOUNCEMENTS -->
    <div id="tab-super-announcements" class="cora-tab-content space-y-6 <?php echo $active_sub_page === 'super-announcements' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-zinc-200/80 rounded-xl p-6 shadow-sm space-y-4">
            <div>
                <h2 class="text-base font-bold text-zinc-900 ">Global Platform Broadcast Console</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Publish top-bar message banners across all tenant workspaces to communicate system updates, maintenance alerts, or notifications.</p>
            </div>
            
            <div class="space-y-4 pt-4 border-t border-zinc-100 max-w-2xl">
                <!-- Announcement text -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Broadcast Text Message</label>
                    <textarea id="cora-broadcast-text" rows="3" class="w-full border border-zinc-200 rounded-xl p-3 text-xs bg-white focus:border-zinc-400 focus:outline-none text-zinc-900 font-medium" placeholder="Enter broadcast announcement text..."><?php echo esc_textarea( get_option( 'cora_announcement_text', '' ) ); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Banner style type -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Visual Alert Theme</label>
                        <select id="cora-broadcast-type" class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                            <?php $curr_type = get_option( 'cora_announcement_type', 'info' ); ?>
                            <option value="info" <?php echo $curr_type === 'info' ? 'selected' : ''; ?>>Monochromatic Zinc (Information)</option>
                            <option value="warning" <?php echo $curr_type === 'warning' ? 'selected' : ''; ?>>Warm Amber Accent (System Alert)</option>
                            <option value="success" <?php echo $curr_type === 'success' ? 'selected' : ''; ?>>Sleek Emerald Accent (Success/Feature Release)</option>
                        </select>
                    </div>

                    <!-- Active status checkbox -->
                    <div class="space-y-1.5 flex flex-col justify-end">
                        <div class="flex items-center gap-2.5 py-2">
                            <input type="checkbox" id="cora-broadcast-active" value="1" <?php checked( get_option( 'cora_announcement_active', '0' ), '1' ); ?> class="w-4 h-4 rounded border-zinc-300 text-zinc-950 focus:ring-zinc-950 cursor-pointer">
                            <label for="cora-broadcast-active" class="text-xs font-bold text-zinc-700 cursor-pointer select-none">Enable Public Broadcasting</label>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button onclick="saveGlobalAnnouncement()" class="px-4 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer">
                        Save and Broadcast Banner
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 6: SYSTEM HEALTH & METRICS -->
    <div id="tab-super-health" class="cora-tab-content space-y-6 <?php echo $active_sub_page === 'super-health' ? '' : 'hidden'; ?>">
        <!-- Live Platform Monitor Card -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-6 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-zinc-900 flex items-center gap-2">
                        Live Platform Monitor
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    </h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Real-time user presence, activity heatmap, and platform usage feed.</p>
                </div>
                <button onclick="loadLiveMonitor()" class="p-2 text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 rounded-lg transition-colors border border-transparent hover:border-zinc-200 " title="Refresh Live Data">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-lg border border-zinc-100 bg-zinc-50/50 ">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-500 mb-1">Users Online</div>
                    <div class="text-2xl font-bold text-zinc-900 " id="live-stat-online">0</div>
                    <div class="text-[10px] text-zinc-500 mt-1">Currently connected</div>
                </div>
                <div class="p-4 rounded-lg border border-zinc-100 bg-zinc-50/50 ">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-500 mb-1">Active Now</div>
                    <div class="text-2xl font-bold text-zinc-900 " id="live-stat-active">0</div>
                    <div class="text-[10px] text-zinc-500 mt-1">Interacting &lt; 60s</div>
                </div>
                <div class="p-4 rounded-lg border border-zinc-100 bg-zinc-50/50 ">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-500 mb-1">Actions Today</div>
                    <div class="text-2xl font-bold text-zinc-900 " id="live-stat-actions">0</div>
                    <div class="text-[10px] text-zinc-500 mt-1">Total logged events</div>
                </div>
                <div class="p-4 rounded-lg border border-zinc-100 bg-zinc-50/50 ">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-500 mb-1">Peak Hour</div>
                    <div class="text-2xl font-bold text-zinc-900 " id="live-stat-peak">--</div>
                    <div class="text-[10px] text-zinc-500 mt-1">Highest activity window</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Online Users & Heatmap -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-500 mb-3">Online Users</h3>
                        <div class="border border-zinc-200 rounded-lg overflow-hidden bg-white ">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-zinc-50 border-b border-zinc-200 ">
                                            <th class="px-4 py-2.5 font-medium text-zinc-500 ">User</th>
                                            <th class="px-4 py-2.5 font-medium text-zinc-500 ">Workspace</th>
                                            <th class="px-4 py-2.5 font-medium text-zinc-500 ">Current Screen</th>
                                            <th class="px-4 py-2.5 font-medium text-zinc-500 ">Status</th>
                                            <th class="px-4 py-2.5 font-medium text-zinc-500 ">Last Seen</th>
                                        </tr>
                                    </thead>
                                    <tbody id="live-online-users-body" class="divide-y divide-zinc-100 ">
                                        <tr>
                                            <td colspan="5" class="px-4 py-6 text-center text-zinc-400">No users currently online</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-500 mb-3">24-Hour Activity Heatmap</h3>
                        <div id="live-heatmap-container" class="space-y-1 max-h-[320px] overflow-y-auto pr-2 custom-scrollbar">
                            <!-- Populated via JS -->
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div>
                    <h3 class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-500 mb-3">Recent Activity Feed</h3>
                    <div id="live-activity-feed" class="max-h-[320px] overflow-y-auto pr-2 custom-scrollbar space-y-1">
                        <div class="text-center text-zinc-400 py-6 text-xs">No recent activity recorded.</div>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="border-zinc-200 my-8">
        <div class="bg-white border border-zinc-200/80 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-zinc-900 ">Platform Specs & Resource Auditing</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Real-time computation of hosting infrastructure size, dynamic table indexes, and workspace attachments usage.</p>
                </div>
                <button onclick="loadHealthMetrics()" class="px-4 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                    Refresh Health Metrics
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-4 border-t border-zinc-100 ">
                <!-- DB Size widget -->
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">MySQL Database Footprint</div>
                    <div id="metric-db-size" class="text-2xl font-bold text-zinc-900 font-mono">-- MB</div>
                    <div class="text-[10px] text-zinc-500">Total data and index allocation</div>
                </div>
                <!-- File Storage widget -->
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">Media Vault Allocation</div>
                    <div id="metric-storage-size" class="text-2xl font-bold text-zinc-900 font-mono">-- MB</div>
                    <div class="text-[10px] text-zinc-500">Tenant media library uploads</div>
                </div>
                <!-- Workspaces count -->
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">Active Workspaces</div>
                    <div id="metric-workspaces" class="text-2xl font-bold text-zinc-900 font-mono">--</div>
                    <div class="text-[10px] text-zinc-500">Provisioned multi-tenant directories</div>
                </div>
                <!-- Users count -->
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">Registered Users</div>
                    <div id="metric-users" class="text-2xl font-bold text-zinc-900 font-mono">--</div>
                    <div class="text-[10px] text-zinc-500">Across all platform organizations</div>
                </div>
                <!-- PHP version info -->
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">PHP Runtime</div>
                    <div id="metric-php-version" class="text-2xl font-bold text-zinc-900 font-mono">PHP --</div>
                    <div class="text-[10px] text-zinc-500">Active server engine specifications</div>
                </div>
                <!-- WordPress core info -->
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">WordPress Core</div>
                    <div id="metric-wp-version" class="text-2xl font-bold text-zinc-900 font-mono">WP --</div>
                    <div class="text-[10px] text-zinc-500">System core framework version</div>
                </div>
                <!-- Server Disk space info -->
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">Server Disk partition</div>
                    <div id="metric-disk-usage" class="text-xs font-bold text-zinc-900 font-mono py-1.5 truncate">--</div>
                    <div class="text-[10px] text-zinc-500">Partition capacity usage details</div>
                </div>
                <!-- PHP memory footprint -->
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">PHP Memory Limit</div>
                    <div id="metric-memory-usage" class="text-xs font-bold text-zinc-900 font-mono py-1.5 truncate">--</div>
                    <div class="text-[10px] text-zinc-500">Peak request vs PHP maximum limit</div>
                </div>
                <!-- CPU load and OS -->
                <div class="p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">Server Load & OS</div>
                    <div id="metric-system-software" class="text-xs font-bold text-zinc-900 font-mono py-1.5 truncate">--</div>
                    <div id="metric-load-avg" class="text-[10px] text-zinc-500 font-semibold mt-0.5">Load Average: --</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Expose dynamic labels directly to JS
const coraRoleLabels = <?php echo json_encode( $roles_list ); ?>;

jQuery(document).ready(function($) {
    let rawWorkspaces = [];
    let rawUsers = [];
    let rawAppeals = [];
    let activeAppealId = null;

    // Tab Navigation Logic
    $('.cora-sub-tabs button').on('click', function() {
        const target = $(this).data('target');
        if (!target) return;
        const pageSlug = target.replace('tab-super-', 'super-');
        const finalSlug = pageSlug === 'super-workspaces' ? 'super-admin' : pageSlug;
        const wsSlug = coraREData.activeWorkspace ? coraREData.activeWorkspace.slug : 'super';
        window.location.href = coraREData.siteUrl + '/' + wsSlug + '/' + finalSlug;
    });

    // Helper: Escape HTML strings for safety
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Helper: Safe date formatter matching standard Cora typography
    function formatDate(dateStr) {
        if (!dateStr || dateStr === '0000-00-00 00:00:00') return '—';
        try {
            const d = new Date(dateStr.replace(/-/g, "/"));
            if (isNaN(d.getTime())) return dateStr;
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const day = String(d.getDate()).padStart(2, '0');
            const month = months[d.getMonth()];
            const year = d.getFullYear();
            return `${day} ${month} ${year}`;
        } catch(e) {
            return dateStr;
        }
    }

    // Load dynamic data on initiation
    function loadPlatformData() {
        // Load Workspaces list
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_workspaces',
            security: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                rawWorkspaces = res.data.workspaces || [];
                renderWorkspaces();
            } else {
                const errorMsg = res.data || 'Failed to load platform workspaces.';
                $('#workspaces-table-body').html(`<tr><td colspan="7" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 border-t border-zinc-100 ">${escapeHtml(errorMsg)}</td></tr>`);
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
            }
        }).fail(function() {
            $('#workspaces-table-body').html('<tr><td colspan="7" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 border-t border-zinc-100 ">Connection error: Could not retrieve workspaces.</td></tr>');
        });

        // Load Users list
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_users',
            security: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                rawUsers = res.data.users || [];
                renderUsers();
            } else {
                const errorMsg = res.data || 'Failed to load platform users.';
                $('#users-table-body').html(`<tr><td colspan="6" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 border-t border-zinc-100 ">${escapeHtml(errorMsg)}</td></tr>`);
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
            }
        }).fail(function() {
            $('#users-table-body').html('<tr><td colspan="6" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 border-t border-zinc-100 ">Connection error: Could not retrieve users.</td></tr>');
        });

        // Load Appeals list
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_appeals',
            security: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                rawAppeals = res.data.appeals || [];
                renderAppeals();
                renderWorkspaces();
            }
        });
    }

    // Render Appeals Tab Content
    window.renderAppeals = function() {
        window.rawAppealsList = rawAppeals;
        const pendingAppeals = rawAppeals.filter(a => a.status === 'pending');
        if (pendingAppeals.length > 0) {
            $('#super-appeals-badge').text(pendingAppeals.length).removeClass('hidden');
        } else {
            $('#super-appeals-badge').addClass('hidden');
        }
        $('#appeals-count-badge').text(`${rawAppeals.length} appeal${rawAppeals.length === 1 ? '' : 's'}`);

        if (rawAppeals.length === 0) {
            $('#appeals-table-body').html('<tr><td colspan="7" class="px-5 py-8 text-center text-zinc-400 bg-zinc-50/20 ">No reactivation appeals submitted yet.</td></tr>');
            return;
        }

        let html = '';
        rawAppeals.forEach(a => {
            let statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-amber-50 text-amber-700 ">Pending</span>';
            if (a.status === 'approved') {
                statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-emerald-50 text-emerald-700 ">Approved</span>';
            } else if (a.status === 'declined') {
                statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 text-zinc-600 ">Declined</span>';
            }

            html += `
                <tr class="hover:bg-zinc-50/20 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-zinc-900 ">${escapeHtml(a.email)}</td>
                    <td class="px-5 py-3.5 text-zinc-700 font-medium">${escapeHtml(a.workspace_name || '—')}</td>
                    <td class="px-5 py-3.5 text-zinc-500 font-mono text-[11px]">${escapeHtml(a.phone || '—')}</td>
                    <td class="px-5 py-3.5 text-zinc-600 max-w-xs truncate">${escapeHtml(a.reason)}</td>
                    <td class="px-5 py-3.5">${statusBadge}</td>
                    <td class="px-5 py-3.5 text-zinc-400 font-medium">${formatDate(a.created_at)}</td>
                    <td class="px-5 py-3.5 text-right">
                        <button onclick="openAppealReviewDrawer('${a.id}')" class="px-2.5 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 cursor-pointer shadow-xs active:scale-95 transition-all">
                            Review Appeal →
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#appeals-table-body').html(html);
    };

    // Render Workspaces Tab Content
    window.renderWorkspaces = function() {
        const query = $('#workspace-search').val().toLowerCase();
        const planFilter = $('#workspace-filter-plan').val();
        const industryFilter = $('#workspace-filter-industry').val();
        const statusFilter = $('#workspace-filter-status').val();

        const filtered = rawWorkspaces.filter(ws => {
            const matchesQuery = !query || 
                (ws.name || '').toLowerCase().includes(query) || 
                (ws.slug || '').toLowerCase().includes(query) || 
                (ws.owner_email || '').toLowerCase().includes(query);
            
            const matchesPlan = !planFilter || ws.plan === planFilter;
            const matchesStatus = !statusFilter || ws.status === statusFilter;
            const wsInd = ws.industry === 'photography' ? 'photography_studio' : (ws.industry || 'real_estate');
            const matchesIndustry = !industryFilter || wsInd === industryFilter;
            
            return matchesQuery && matchesPlan && matchesStatus && matchesIndustry;
        });

        $('#workspace-count-badge').text(`${filtered.length} workspace${filtered.length === 1 ? '' : 's'}`);

        if (filtered.length === 0) {
            $('#workspaces-table-body').html('<tr><td colspan="8" class="px-5 py-8 text-center text-zinc-400 bg-zinc-50/20 ">No workspaces matching filters found.</td></tr>');
            return;
        }

        let html = '';
        filtered.forEach(ws => {
            const planBadge = ws.plan 
                ? `<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 text-zinc-700 uppercase tracking-wide">${escapeHtml(ws.plan)}</span>` 
                : '<span class="text-zinc-400">—</span>';
            
            let statusBadge = ws.status === 'active'
                ? '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-emerald-50 text-emerald-700 select-none">Active</span>'
                : '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-red-50 text-red-700 select-none">Suspended</span>';

            const pendingAppeal = rawAppeals.find(a => a.status === 'pending' && (
                (ws.owner_email && a.email && a.email.toLowerCase() === ws.owner_email.toLowerCase()) ||
                (ws.name && a.workspace_name && a.workspace_name.toLowerCase() === ws.name.toLowerCase()) ||
                (ws.slug && a.workspace_name && a.workspace_name.toLowerCase() === ws.slug.toLowerCase())
            ));

            if (ws.status === 'suspended' && pendingAppeal) {
                statusBadge += `<button onclick="openAppealReviewDrawer('${pendingAppeal.id}')" class="ml-1.5 px-2 py-0.5 text-[9px] font-bold rounded-md bg-amber-100 text-amber-800 border border-amber-200 hover:bg-amber-200 cursor-pointer select-none inline-flex items-center gap-1"><span>📩</span> Appeal Pending</button>`;
            }

            const toggleLabel = ws.status === 'active' ? 'Suspend' : 'Activate';
            const toggleClass = ws.status === 'active'
                ? 'text-red-600 hover:text-red-700 border-zinc-200 hover:bg-red-50 '
                : 'text-emerald-600 hover:text-emerald-700 border-zinc-200 hover:bg-emerald-50 ';

            const currInd = ws.industry === 'photography' ? 'photography_studio' : (ws.industry || 'real_estate');

            const indIcon = currInd === 'photography_studio'
                ? `<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>`
                : `<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>`;

            html += `
                <tr class="hover:bg-zinc-50/20 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-zinc-900 ">${escapeHtml(ws.name)}</td>
                    <td class="px-5 py-3.5 text-zinc-550 font-mono text-[11px]">${escapeHtml(ws.slug)}</td>
                    <td class="px-5 py-3.5">${planBadge}</td>
                    <td class="px-5 py-3.5">
                        <div class="inline-flex items-center gap-1.5 px-2 py-1 border border-zinc-200 rounded-lg bg-white shadow-xs">
                            <span class="text-zinc-500 shrink-0">${indIcon}</span>
                            <select onchange="changeWorkspaceIndustry(${ws.id}, this.value)" class="text-[10px] font-bold text-zinc-700 bg-transparent outline-none cursor-pointer">
                                <option value="real_estate" ${currInd === 'real_estate' ? 'selected' : ''}>Real Estate</option>
                                <option value="photography_studio" ${currInd === 'photography_studio' ? 'selected' : ''}>Studio</option>
                            </select>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">${statusBadge}</td>
                    <td class="px-5 py-3.5 text-zinc-500 font-medium">${escapeHtml(ws.owner_email || '—')}</td>
                    <td class="px-5 py-3.5 text-zinc-400 font-medium">${formatDate(ws.created_at)}</td>
                    <td class="px-4 py-3.5 text-right">
                        <div class="flex items-center justify-end flex-wrap gap-1.5">
                            <button onclick="toggleWorkspaceStatus(${ws.id}, '${ws.status === 'active' ? 'suspended' : 'active'}')" class="px-2.5 py-1 border rounded-lg text-[10px] font-bold bg-white hover:bg-zinc-50 cursor-pointer shadow-sm active:scale-95 transition-all ${toggleClass}">
                                ${toggleLabel}
                            </button>
                            
                            <select onchange="changeWorkspacePlan(${ws.id}, this.value)" class="px-2 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white outline-none cursor-pointer shadow-sm">
                                <option value="beta" ${ws.plan === 'beta' ? 'selected' : ''}>Beta</option>
                                <option value="starter" ${ws.plan === 'starter' ? 'selected' : ''}>Starter</option>
                                <option value="pro" ${ws.plan === 'pro' ? 'selected' : ''}>Pro</option>
                                <option value="enterprise" ${ws.plan === 'enterprise' ? 'selected' : ''}>Enterprise</option>
                            </select>

                            <button onclick="openManageWorkspaceDrawer(${ws.id})" class="px-2.5 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 cursor-pointer shadow-sm active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                Settings
                            </button>

                            ${ws.owner_user_id ? `
                            <button onclick="impersonateUser(${ws.owner_user_id})" class="px-2.5 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 cursor-pointer shadow-sm active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                Impersonate
                            </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });
        $('#workspaces-table-body').html(html);
    }

    // Render Users Tab Content
    window.renderUsers = function() {
        const query = $('#user-search').val().toLowerCase();
        const roleFilter = $('#user-filter-role').val();
        const statusFilter = $('#user-filter-status').val();

        const filtered = rawUsers.filter(u => {
            const matchesQuery = !query || 
                (u.display_name || '').toLowerCase().includes(query) || 
                (u.user_login || '').toLowerCase().includes(query) || 
                (u.user_email || '').toLowerCase().includes(query);
            
            const matchesRole = !roleFilter || u.role === roleFilter;
            const matchesStatus = !statusFilter || u.status === statusFilter;
            
            return matchesQuery && matchesRole && matchesStatus;
        });

        $('#user-count-badge').text(`${filtered.length} user${filtered.length === 1 ? '' : 's'}`);

        if (filtered.length === 0) {
            $('#users-table-body').html('<tr><td colspan="6" class="px-5 py-8 text-center text-zinc-400 bg-zinc-50/20 ">No users matching filters found.</td></tr>');
            return;
        }

        let html = '';
        filtered.forEach(u => {
            const roleLabel = coraRoleLabels[u.role] || u.role;
            
            const statusBadge = u.status === 'active'
                ? '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-emerald-50 text-emerald-700 select-none">Active</span>'
                : '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-red-50 text-red-700 select-none">Inactive</span>';

            const toggleLabel = u.status === 'active' ? 'Deactivate' : 'Activate';
            const toggleClass = u.status === 'active'
                ? 'text-red-600 hover:text-red-700 border-zinc-200 hover:bg-red-50 '
                : 'text-emerald-600 hover:text-emerald-700 border-zinc-200 hover:bg-emerald-50 ';

            // Build select options for roles list (Super Admin only for Claraverse / Heycora domains)
            let roleOptions = '';
            const uEmail = (u.user_email || '').toLowerCase().trim();
            const isSuperDomain = uEmail.endsWith('@claraverse.in') || uEmail.endsWith('@heycora.in') || uEmail.endsWith('@cora.local') || uEmail === 'dravya.shs@gmail.com' || uEmail === 'dravya.shravya@gmail.com' || uEmail === 'admin@cora.local';

            Object.keys(coraRoleLabels).forEach(rKey => {
                const isSuperRole = (rKey === 'administrator' || rKey === 'cora_shruti' || rKey === 'super_admin');
                if (isSuperRole && !isSuperDomain) {
                    return; // Skip Super Admin option for non-super domain users
                }
                roleOptions += `<option value="${rKey}" ${u.role === rKey ? 'selected' : ''}>${escapeHtml(coraRoleLabels[rKey])}</option>`;
            });

            html += `
                <tr class="hover:bg-zinc-50/20 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-zinc-900 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full ${u.status === 'active' ? 'bg-emerald-500' : 'bg-zinc-300 '}"></span>
                        ${escapeHtml(u.display_name || u.user_login || '')}
                    </td>
                    <td class="px-5 py-3.5 text-zinc-500 font-medium">${escapeHtml(u.user_email || '—')}</td>
                    <td class="px-5 py-3.5 font-semibold text-zinc-800 ">${escapeHtml(u.agency_name || '—')}</td>
                    <td class="px-5 py-3.5">
                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 text-zinc-700 uppercase tracking-wide">
                            ${escapeHtml(roleLabel)}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">${statusBadge}</td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="toggleUserStatus(${u.id}, '${u.status === 'active' ? 'inactive' : 'active'}')" class="px-2.5 py-1 border rounded-lg text-[10px] font-bold bg-white hover:bg-zinc-50 cursor-pointer shadow-sm active:scale-95 transition-all ${toggleClass}">
                                ${toggleLabel}
                            </button>

                            <select onchange="changeUserRole(${u.id}, this.value)" class="px-2 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white outline-none cursor-pointer shadow-sm">
                                ${roleOptions}
                            </select>

                            <button onclick="impersonateUser(${u.wp_user_id})" class="px-2.5 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 cursor-pointer shadow-sm active:scale-95 transition-all inline-flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                Impersonate User
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        $('#users-table-body').html(html);
    }

    // Trigger filters on input/change
    window.filterWorkspaces = function() {
        renderWorkspaces();
    }

    window.filterUsers = function() {
        renderUsers();
    }

    // POST: Update Workspace Status (Toggle)
    window.toggleWorkspaceStatus = function(workspaceId, newStatus) {
        const ws = rawWorkspaces.find(w => w.id == workspaceId);
        if (!ws) return;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_update_workspace',
            security: coraREData.ajaxNonce,
            workspace_id: workspaceId,
            name: ws.name,
            slug: ws.slug,
            status: newStatus,
            plan: ws.plan,
            industry: ws.industry,
            owner_email: ws.owner_email
        }, function(res) {
            if (res.success) {
                ws.status = newStatus;
                renderWorkspaces();
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || `Workspace status updated to ${newStatus.toUpperCase()}.`, 'success');
                }
            } else {
                const errorMsg = (res.data && res.data.message) ? res.data.message : (res.data || 'Failed to toggle workspace status.');
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
                renderWorkspaces();
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while toggling status.', 'error');
            }
            renderWorkspaces();
        });
    }

    // POST: Update Workspace Industry
    window.changeWorkspaceIndustry = function(workspaceId, newIndustry) {
        const ws = rawWorkspaces.find(w => w.id == workspaceId);
        if (!ws) return;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_update_workspace',
            security: coraREData.ajaxNonce,
            workspace_id: workspaceId,
            name: ws.name,
            slug: ws.slug,
            status: ws.status,
            plan: ws.plan,
            industry: newIndustry,
            owner_email: ws.owner_email
        }, function(res) {
            if (res.success) {
                ws.industry = newIndustry;
                renderWorkspaces();
                if (window.coraShowToast) {
                    const label = newIndustry === 'photography_studio' ? 'Photography Studio' : 'Real Estate';
                    window.coraShowToast(res.data.message || `Workspace industry updated to ${label}.`, 'success');
                }
            } else {
                const errorMsg = (res.data && res.data.message) ? res.data.message : (res.data || 'Failed to update workspace industry.');
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
                renderWorkspaces();
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while changing workspace industry.', 'error');
            }
            renderWorkspaces();
        });
    }

    // POST: Update Workspace Plan
    window.changeWorkspacePlan = function(workspaceId, newPlan) {
        const ws = rawWorkspaces.find(w => w.id == workspaceId);
        if (!ws) return;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_update_workspace',
            security: coraREData.ajaxNonce,
            workspace_id: workspaceId,
            name: ws.name,
            slug: ws.slug,
            status: ws.status,
            plan: newPlan,
            industry: ws.industry,
            owner_email: ws.owner_email
        }, function(res) {
            if (res.success) {
                ws.plan = newPlan;
                renderWorkspaces();
                if (window.coraShowToast) {
                    const planLabel = newPlan.charAt(0).toUpperCase() + newPlan.slice(1);
                    window.coraShowToast(res.data.message || `Workspace plan changed to ${planLabel}.`, 'success');
                }
            } else {
                const errorMsg = (res.data && res.data.message) ? res.data.message : (res.data || 'Failed to update workspace plan.');
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
                renderWorkspaces();
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while changing workspace plan.', 'error');
            }
            renderWorkspaces();
        });
    }

    // POST: Update User Status (Toggle)
    window.toggleUserStatus = function(userId, newStatus) {
        const u = rawUsers.find(usr => usr.id == userId);
        if (!u) return;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_update_user',
            security: coraREData.ajaxNonce,
            user_id: userId,
            status: newStatus,
            role: u.role
        }, function(res) {
            if (res.success) {
                u.status = newStatus;
                renderUsers();
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || 'User status updated successfully.', 'success');
                }
            } else {
                const errorMsg = res.data || 'Failed to update user status.';
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while updating user status.', 'error');
            }
        });
    }

    // POST: Update User Role
    window.changeUserRole = function(userId, newRole) {
        const u = rawUsers.find(usr => usr.id == userId);
        if (!u) return;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_update_user',
            security: coraREData.ajaxNonce,
            user_id: userId,
            status: u.status,
            role: newRole
        }, function(res) {
            if (res.success) {
                u.role = newRole;
                renderUsers();
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || 'User role changed successfully.', 'success');
                }
            } else {
                const errorMsg = res.data || 'Failed to update user role.';
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
                renderUsers();
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while updating user role.', 'error');
            }
            renderUsers();
        });
    }

    // POST: Impersonate Target User
    window.impersonateUser = function(wpUserId) {
        if (window.coraShowToast) {
            window.coraShowToast('Initiating impersonation session... please wait.', 'info');
        }

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_impersonate_user',
            security: coraREData.ajaxNonce,
            target_user_id: wpUserId
        }, function(res) {
            if (res.success && res.data.redirect_url) {
                if (window.coraShowToast) {
                    window.coraShowToast('Session switched successfully. Redirecting...', 'success');
                }
                setTimeout(function() {
                    window.location.href = res.data.redirect_url;
                }, 800);
            } else {
                const errorMsg = res.data || 'Failed to switch to target user.';
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error during impersonation request.', 'error');
            }
        });
    }

    // POST: Save Global Announcement settings
    window.saveGlobalAnnouncement = function() {
        const text = $('#cora-broadcast-text').val().trim();
        const type = $('#cora-broadcast-type').val();
        const active = $('#cora-broadcast-active').is(':checked') ? '1' : '0';

        if (window.coraShowToast) {
            window.coraShowToast('Saving announcement settings...', 'info');
        }

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_save_announcement',
            security: coraREData.ajaxNonce,
            announcement_active: active,
            announcement_text: text,
            announcement_type: type
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || 'Announcement settings published successfully.', 'success');
                }
            } else {
                const errorMsg = res.data || 'Failed to save announcement settings.';
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while saving announcement settings.', 'error');
            }
        });
    };

    // GET: Retrieve Platform Health & Metrics
    window.loadHealthMetrics = function() {
        // Reset display values to loader skeletons / indicators
        $('#metric-db-size').text('-- MB');
        $('#metric-storage-size').text('-- MB');
        $('#metric-workspaces').text('--');
        $('#metric-users').text('--');
        $('#metric-php-version').text('PHP --');
        $('#metric-wp-version').text('WP --');
        $('#metric-disk-usage').text('--');
        $('#metric-memory-usage').text('--');
        $('#metric-system-software').text('--');
        $('#metric-load-avg').text('Load Average: --');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_metrics',
            security: coraREData.ajaxNonce
        }, function(res) {
            if (res.success && res.data) {
                $('#metric-db-size').text(res.data.db_size_mb + ' MB');
                $('#metric-storage-size').text(res.data.storage_size_mb + ' MB');
                $('#metric-workspaces').text(res.data.total_workspaces);
                $('#metric-users').text(res.data.total_users);
                $('#metric-php-version').text('PHP ' + res.data.php_version);
                $('#metric-wp-version').text('WP ' + res.data.wp_version);
                $('#metric-disk-usage').text(res.data.disk_usage || 'Not available');
                $('#metric-memory-usage').text(res.data.memory_usage || 'Not available');
                $('#metric-system-software').text(res.data.system_software || '--');
                $('#metric-load-avg').text('Load Average: ' + (res.data.load_avg || '--'));
            } else {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data || 'Failed to load system metrics.', 'error');
                }
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while loading system metrics.', 'error');
            }
        });
    };

    // ── Live Platform Monitor ──────────────────────────────────
    let liveMonitorInterval = null;

    window.loadLiveMonitor = function() {
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_live_monitor',
            security: coraREData.ajaxNonce
        }, function(res) {
            if (!res.success || !res.data) return;
            const d = res.data;
            
            // Summary cards
            $('#live-stat-online').text(d.summary.total_online);
            $('#live-stat-active').text(d.summary.total_active);
            $('#live-stat-actions').text(d.summary.total_actions_today);
            $('#live-stat-peak').text(d.summary.peak_hour || '--');
            
            // Online users table
            if (d.online_users && d.online_users.length > 0) {
                let html = '';
                d.online_users.forEach(u => {
                    const statusDot = u.status === 'active' 
                        ? '<span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>'
                        : '<span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-400"></span>';
                    const statusLabel = u.status === 'active' ? 'Active' : 'Idle';
                    const timeSince = u.last_seen ? formatTimeAgo(u.last_seen) : '—';
                    html += `<tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="px-4 py-2.5 font-semibold text-zinc-900 ">${escapeHtml(u.display_name || '—')}</td>
                        <td class="px-4 py-2.5 text-zinc-500 font-mono text-[10px]">${escapeHtml(u.agency_name || '—')}</td>
                        <td class="px-4 py-2.5 text-zinc-600 ">${escapeHtml(u.current_screen || 'Dashboard')}</td>
                        <td class="px-4 py-2.5"><span class="inline-flex items-center gap-1.5 text-zinc-600 ">${statusDot} ${statusLabel}</span></td>
                        <td class="px-4 py-2.5 text-zinc-400 ">${timeSince}</td>
                    </tr>`;
                });
                $('#live-online-users-body').html(html);
            } else {
                $('#live-online-users-body').html('<tr><td colspan="5" class="px-4 py-6 text-center text-zinc-400">No users currently online</td></tr>');
            }
            
            // Heatmap
            renderHeatmap(d.heatmap || {});
            
            // Activity feed
            renderActivityFeed(d.recent_activity || []);
        });
    };

    function renderHeatmap(heatmap) {
        const container = $('#live-heatmap-container');
        const currentHour = new Date().getHours();
        let maxCount = 0;
        for (let h = 0; h < 24; h++) {
            const key = String(h).padStart(2, '0');
            const val = parseInt(heatmap[key]) || 0;
            if (val > maxCount) maxCount = val;
        }
        if (maxCount === 0) maxCount = 1; // prevent div by zero
        
        let html = '';
        for (let h = 0; h < 24; h++) {
            const key = String(h).padStart(2, '0');
            const count = parseInt(heatmap[key]) || 0;
            const pct = Math.round((count / maxCount) * 100);
            const isCurrent = h === currentHour;
            const hourLabel = h === 0 ? '12 AM' : h < 12 ? h + ' AM' : h === 12 ? '12 PM' : (h-12) + ' PM';
            const highlight = isCurrent ? 'bg-zinc-100/80 rounded' : '';
            html += `<div class="flex items-center gap-2 py-0.5 px-1 ${highlight}">
                <span class="text-[10px] font-mono text-zinc-400 w-12 shrink-0 text-right">${hourLabel}</span>
                <div class="flex-1 h-3.5 bg-zinc-100 rounded-sm overflow-hidden">
                    <div class="h-full bg-zinc-700 rounded-sm transition-all" style="width:${pct}%"></div>
                </div>
                <span class="text-[10px] font-mono text-zinc-400 w-6 text-right">${count}</span>
            </div>`;
        }
        container.html(html);
    }

    function renderActivityFeed(activities) {
        const container = $('#live-activity-feed');
        if (!activities || activities.length === 0) {
            container.html('<div class="text-center text-zinc-400 py-6 text-xs">No recent activity recorded.</div>');
            return;
        }
        let html = '';
        activities.forEach(a => {
            html += `<div class="flex items-start gap-3 py-2 border-b border-zinc-100 last:border-0">
                <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                <div class="flex-1 min-w-0">
                    <span class="font-semibold text-zinc-800 ">${escapeHtml(a.user_display_name || 'System')}</span>
                    <span class="text-zinc-500 ml-1">${escapeHtml(a.description || a.action_type)}</span>
                    <span class="ml-1.5 text-[10px] font-mono text-zinc-400 ">${escapeHtml(a.agency_name || '')}</span>
                </div>
                <span class="text-[10px] text-zinc-400 shrink-0 whitespace-nowrap">${a.time_ago || '—'}</span>
            </div>`;
        });
        container.html(html);
    }

    function formatTimeAgo(dateStr) {
        if (!dateStr) return '—';
        try {
            const d = new Date(dateStr.replace(/-/g, '/'));
            const now = new Date();
            const diffSec = Math.round((now - d) / 1000);
            if (diffSec < 30) return 'Just now';
            if (diffSec < 60) return diffSec + 's ago';
            if (diffSec < 3600) return Math.floor(diffSec / 60) + 'm ago';
            if (diffSec < 86400) return Math.floor(diffSec / 3600) + 'h ago';
            return Math.floor(diffSec / 86400) + 'd ago';
        } catch(e) { return '—'; }
    }

    // Auto-refresh controller with visibility API
    function startLiveMonitorRefresh() {
        if (liveMonitorInterval) clearInterval(liveMonitorInterval);
        liveMonitorInterval = setInterval(function() {
            if (document.visibilityState === 'visible') {
                loadLiveMonitor();
            }
        }, 30000);
    }

    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible' && coraREData.currentPage === 'super-health') {
            loadLiveMonitor();
        }
    });

    // Run dynamic retrieval on mount
    loadPlatformData();
    if (coraREData.currentPage === 'super-health') {
        loadHealthMetrics();
        loadLiveMonitor();
        startLiveMonitorRefresh();
    }
});

window.openAppealReviewDrawer = function(appealId) {
    if (!appealId) return;
    const appeal = window.rawAppealsList ? window.rawAppealsList.find(a => a.id === appealId) : null;
    
    // Fallback search in rawAppeals if attached to window or jQuery scope
    $('#review-appeal-id').text(appealId);
    $('#review-appeal-email').text(appeal ? appeal.email : '—');
    $('#review-appeal-workspace').text(appeal ? (appeal.workspace_name || '—') : '—');
    $('#review-appeal-phone').text(appeal ? (appeal.phone || '—') : '—');
    $('#review-appeal-date').text(appeal ? (appeal.created_at || '—') : '—');
    $('#review-appeal-reason').text(appeal ? (appeal.reason || '—') : '—');
    $('#review-appeal-notes').val(appeal ? (appeal.notes || '') : '');

    window.activeAppealId = appealId;

    $('#cora-appeal-review-drawer').removeClass('translate-x-full');
    $('#cora-appeal-review-overlay').removeClass('hidden');
};

window.closeAppealReviewDrawer = function() {
    $('#cora-appeal-review-drawer').addClass('translate-x-full');
    $('#cora-appeal-review-overlay').addClass('hidden');
    window.activeAppealId = null;
};

window.processAppealAction = function(action) {
    if (!window.activeAppealId) {
        if (window.coraShowToast) window.coraShowToast('No active appeal selected.', 'error');
        return;
    }
    const notes = $('#review-appeal-notes').val().trim();

    $.post(coraREData.ajaxUrl, {
        action: 'cora_super_handle_appeal',
        security: coraREData.ajaxNonce,
        appeal_id: window.activeAppealId,
        appeal_action: action,
        notes: notes
    }, function(res) {
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Appeal processed successfully!', 'success');
            closeAppealReviewDrawer();
            if (typeof loadPlatformData === 'function') loadPlatformData();
            setTimeout(function() { window.location.reload(); }, 600);
        } else {
            const errorMsg = (res.data && res.data.message) ? res.data.message : 'Failed to process appeal.';
            if (window.coraShowToast) window.coraShowToast(errorMsg, 'error');
        }
    }).fail(function() {
        if (window.coraShowToast) window.coraShowToast('Network error while processing appeal.', 'error');
    });
};

window.openCreateWorkspaceDrawer = function() {
    $('#new-ws-name').val('');
    $('#new-ws-slug').val('');
    $('#new-ws-owner-email').val('');
    $('#new-ws-plan').val('starter');
    $('#new-ws-industry').val('real_estate');
    $('#cora-add-workspace-drawer').removeClass('translate-x-full');
    $('#cora-add-workspace-overlay').removeClass('hidden');
};

window.closeCreateWorkspaceDrawer = function() {
    $('#cora-add-workspace-drawer').addClass('translate-x-full');
    $('#cora-add-workspace-overlay').addClass('hidden');
};

window.autoSlugifyWorkspace = function(name) {
    const slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    $('#new-ws-slug').val(slug);
};

window.submitNewWorkspace = function() {
    const name = $('#new-ws-name').val().trim();
    const slug = $('#new-ws-slug').val().trim();
    const plan = $('#new-ws-plan').val();
    const industry = $('#new-ws-industry').val();
    const ownerEmail = $('#new-ws-owner-email').val().trim();

    if (!name || !slug) {
        if (window.coraShowToast) window.coraShowToast('Workspace name and slug are required.', 'error');
        return;
    }

    $.post(coraREData.ajaxUrl, {
        action: 'cora_super_create_workspace',
        security: coraREData.ajaxNonce,
        name: name,
        slug: slug,
        plan: plan,
        industry: industry,
        owner_email: ownerEmail
    }, function(res) {
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Workspace created successfully!', 'success');
            closeCreateWorkspaceDrawer();
            if (typeof loadPlatformData === 'function') loadPlatformData();
        } else {
            const errorMsg = (res.data && res.data.message) ? res.data.message : 'Failed to create workspace.';
            if (window.coraShowToast) window.coraShowToast(errorMsg, 'error');
        }
    }).fail(function() {
        if (window.coraShowToast) window.coraShowToast('Network error while creating workspace.', 'error');
    });
};

window.dispatchSuperDailyReports = function() {
    $.post(coraREData.ajaxUrl, {
        action: 'cora_super_dispatch_daily_report',
        nonce: coraREData.nonce
    }, function(res) {
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Daily reports dispatched to all workspace owners!', 'success');
        } else {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Failed to dispatch reports.', 'error');
        }
    }).fail(function() {
        if (window.coraShowToast) window.coraShowToast('Network error while dispatching reports.', 'error');
    });
};

let currentManagingWorkspaceId = null;

window.openManageWorkspaceDrawer = function(workspaceId) {
    const ws = rawWorkspaces.find(w => w.id === workspaceId);
    if (!ws) {
        if (window.coraShowToast) window.coraShowToast('Workspace data not found.', 'error');
        return;
    }

    currentManagingWorkspaceId = workspaceId;

    // Set Header
    $('#manage-ws-title').text(ws.name);
    $('#manage-ws-slug-info').text('app.heycora.in/' + ws.slug);

    // Set Quotas
    $('#manage-ws-max-users').val(ws.max_users_limit || 5);
    $('#manage-ws-storage-mb').val(ws.storage_limit_mb || 1024);
    $('#manage-ws-max-emails').val(ws.max_emails_limit || 200);
    $('#manage-ws-rag-token-quota').val(ws.rag_token_quota || 100000);

    // Set Toggles
    $('#manage-ws-enable-leads').prop('checked', ws.enable_leads !== false);
    $('#manage-ws-enable-clients').prop('checked', ws.enable_clients !== false);
    $('#manage-ws-enable-properties').prop('checked', ws.enable_properties !== false);
    $('#manage-ws-enable-bookings').prop('checked', ws.enable_bookings !== false);
    $('#manage-ws-enable-ledger').prop('checked', ws.enable_ledger !== false);
    $('#manage-ws-enable-documents').prop('checked', ws.enable_documents !== false);

    // Open Drawer
    $('#cora-manage-workspace-drawer').removeClass('translate-x-full');
    $('#cora-manage-workspace-overlay').removeClass('hidden');
};

window.closeManageWorkspaceDrawer = function() {
    $('#cora-manage-workspace-drawer').addClass('translate-x-full');
    $('#cora-manage-workspace-overlay').addClass('hidden');
    currentManagingWorkspaceId = null;
};

window.saveWorkspaceSettings = function() {
    if (!currentManagingWorkspaceId) return;

    const maxUsers = $('#manage-ws-max-users').val();
    const storageLimit = $('#manage-ws-storage-mb').val();
    const maxEmails = $('#manage-ws-max-emails').val();
    const ragTokenQuota = $('#manage-ws-rag-token-quota').val();

    const enableLeads = $('#manage-ws-enable-leads').is(':checked');
    const enableClients = $('#manage-ws-enable-clients').is(':checked');
    const enableProperties = $('#manage-ws-enable-properties').is(':checked');
    const enableBookings = $('#manage-ws-enable-bookings').is(':checked');
    const enableLedger = $('#manage-ws-enable-ledger').is(':checked');
    const enableDocuments = $('#manage-ws-enable-documents').is(':checked');

    const saveBtn = $('#manage-ws-save-btn');
    const originalHtml = saveBtn.html();
    saveBtn.prop('disabled', true).html('Saving...');

    $.post(coraREData.ajaxUrl, {
        action: 'cora_super_update_workspace',
        security: coraREData.ajaxNonce,
        id: currentManagingWorkspaceId,
        max_users_limit: maxUsers,
        storage_limit_mb: storageLimit,
        max_emails_limit: maxEmails,
        rag_token_quota: ragTokenQuota,
        enable_leads: enableLeads,
        enable_clients: enableClients,
        enable_properties: enableProperties,
        enable_bookings: enableBookings,
        enable_ledger: enableLedger,
        enable_documents: enableDocuments
    }, function(res) {
        saveBtn.prop('disabled', false).html(originalHtml);
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast('Workspace settings saved successfully.', 'success');
            closeManageWorkspaceDrawer();
            if (typeof loadPlatformData === 'function') loadPlatformData();
        } else {
            const errorMsg = (res.data && res.data.message) ? res.data.message : 'Failed to save settings.';
            if (window.coraShowToast) window.coraShowToast(errorMsg, 'error');
        }
    }).fail(function() {
        saveBtn.prop('disabled', false).html(originalHtml);
        if (window.coraShowToast) window.coraShowToast('Network error while saving settings.', 'error');
    });
};
</script>

<!-- Create Workspace Right-Sliding Drawer Overlay & Panel -->
<div id="cora-add-workspace-overlay" onclick="closeCreateWorkspaceDrawer()" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-add-workspace-drawer" class="fixed top-0 right-0 h-full w-full sm:w-112 bg-white border-l border-zinc-200 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Drawer Header -->
    <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-zinc-100 rounded-lg text-zinc-900 ">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-zinc-900 ">Create Independent Workspace</h3>
                <p class="text-xs text-zinc-500 font-mono">app.heycora.in/{{slug}}</p>
            </div>
        </div>
        <button onclick="closeCreateWorkspaceDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Drawer Content Form -->
    <div class="flex-1 overflow-y-auto p-6 space-y-5">
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Workspace Name *</label>
            <input type="text" id="new-ws-name" oninput="autoSlugifyWorkspace(this.value)" class="w-full border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900 " placeholder="e.g. Apex Realty Studio">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Workspace Slug / URL *</label>
            <div class="flex items-center border border-zinc-200 rounded-xl overflow-hidden bg-white focus-within:border-zinc-400">
                <span class="px-3 py-2.5 text-xs font-mono text-zinc-400 bg-zinc-50 border-r border-zinc-200 shrink-0">app.heycora.in/</span>
                <input type="text" id="new-ws-slug" class="w-full px-3 py-2.5 text-xs font-mono bg-transparent outline-none text-zinc-900 " placeholder="apex-realty">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Subscription Plan</label>
            <select id="new-ws-plan" class="w-full border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs bg-white outline-none cursor-pointer text-zinc-900 ">
                <option value="starter">Starter Plan</option>
                <option value="pro">Pro Plan</option>
                <option value="enterprise" selected>Enterprise Plan</option>
                <option value="beta">Beta Plan</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Industry Profile *</label>
            <select id="new-ws-industry" class="w-full border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs bg-white outline-none cursor-pointer text-zinc-900 ">
                <option value="real_estate" selected>Real Estate Agency</option>
                <option value="photography_studio">Photography Studio</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Owner Account Email</label>
            <input type="email" id="new-ws-owner-email" class="w-full border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900 " placeholder="owner@agency.com">
            <p class="text-[11px] text-zinc-400 mt-1">If the email matches an existing user, they will be assigned as workspace owner.</p>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="p-6 border-t border-zinc-100 bg-zinc-50/50 flex items-center justify-end gap-3 shrink-0">
        <button onclick="closeCreateWorkspaceDrawer()" class="px-4 py-2 text-xs font-bold text-zinc-600 hover:text-zinc-900 transition-colors cursor-pointer">
            Cancel
        </button>
        <button onclick="submitNewWorkspace()" class="px-5 py-2.5 bg-zinc-950 text-white font-bold text-xs rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition-all cursor-pointer shadow-sm">
            Create Workspace
        </button>
    </div>
</div>

<!-- Review Suspension Appeal Right-Sliding Drawer -->
<div id="cora-appeal-review-overlay" onclick="closeAppealReviewDrawer()" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-appeal-review-drawer" class="fixed top-0 right-0 h-full w-full sm:w-120 bg-white border-l border-zinc-200 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-zinc-900 ">Review Reactivation Appeal</h3>
                <p class="text-xs text-zinc-500 font-mono" id="review-appeal-id">appeal_...</p>
            </div>
        </div>
        <button onclick="closeAppealReviewDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto p-6 space-y-4">
        <div class="p-4 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-3">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Account Email</span>
                <p class="text-xs font-bold text-zinc-900 " id="review-appeal-email">—</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Workspace / Agency</span>
                <p class="text-xs font-bold text-zinc-900 " id="review-appeal-workspace">—</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Contact Phone</span>
                <p class="text-xs font-bold text-zinc-900 " id="review-appeal-phone">—</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Submitted Date</span>
                <p class="text-xs text-zinc-600 font-mono" id="review-appeal-date">—</p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Applicant Reason / Explanation</label>
            <div class="p-3.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 leading-relaxed whitespace-pre-wrap" id="review-appeal-reason">—</div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Administrator Response Notes (Sent to User)</label>
            <textarea id="review-appeal-notes" rows="3" class="w-full border border-zinc-200 rounded-xl p-3 text-xs bg-white outline-none text-zinc-900 " placeholder="Optional explanation included in the confirmation email..."></textarea>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-6 border-t border-zinc-100 bg-zinc-50/50 flex items-center justify-between gap-3 shrink-0">
        <button onclick="processAppealAction('decline')" class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 font-bold text-xs rounded-xl hover:bg-red-100 cursor-pointer transition-all">
            Decline Appeal
        </button>
        <button onclick="processAppealAction('approve')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl active:scale-[0.98] transition-all cursor-pointer shadow-sm flex items-center gap-1.5">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Approve & Reactivate Workspace
        </button>
    </div>
</div>

<!-- Manage Workspace Settings Right-Sliding Drawer -->
<div id="cora-manage-workspace-overlay" onclick="closeManageWorkspaceDrawer()" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-manage-workspace-drawer" class="fixed top-0 right-0 h-full w-full sm:w-120 bg-white border-l border-zinc-200 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-zinc-100 rounded-lg text-zinc-900 ">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-zinc-900 " id="manage-ws-title">Manage Workspace</h3>
                <p class="text-xs text-zinc-500 font-mono" id="manage-ws-slug-info">app.heycora.in/slug</p>
            </div>
        </div>
        <button onclick="closeManageWorkspaceDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Body Form -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        <!-- Resource Quotas Section -->
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3.5">Resource Quotas & Limits</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-zinc-600 mb-1">Max Users</label>
                    <input type="number" id="manage-ws-max-users" min="1" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900 font-mono" placeholder="5">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-zinc-600 mb-1">Storage Limit (MB)</label>
                    <input type="number" id="manage-ws-storage-mb" min="10" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900 font-mono" placeholder="1024">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-zinc-600 mb-1">Max Emails / Month</label>
                    <input type="number" id="manage-ws-max-emails" min="0" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900 font-mono" placeholder="200">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-zinc-600 mb-1">RAG Token Limit</label>
                    <input type="number" id="manage-ws-rag-token-quota" min="1000" step="50000" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900 font-mono" placeholder="100000">
                </div>
            </div>
        </div>

        <!-- Feature Flags Section -->
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3.5">Feature Governance Flags</h4>
            <div class="space-y-2.5">
                <!-- Leads -->
                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-xl bg-zinc-50/20 ">
                    <div>
                        <h5 class="text-xs font-bold text-zinc-800 ">Leads Management</h5>
                        <p class="text-[10px] text-zinc-400 ">Enable leads table, details inspection, and client funnels.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="manage-ws-enable-leads" class="sr-only peer">
                        <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950 peer-checked:after:border-transparent"></div>
                    </label>
                </div>

                <!-- Clients -->
                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-xl bg-zinc-50/20 ">
                    <div>
                        <h5 class="text-xs font-bold text-zinc-800 ">Clients Directory</h5>
                        <p class="text-[10px] text-zinc-400 ">Enable client records database and workspace accounts.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="manage-ws-enable-clients" class="sr-only peer">
                        <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950 peer-checked:after:border-transparent"></div>
                    </label>
                </div>

                <!-- Portfolios / Properties -->
                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-xl bg-zinc-50/20 ">
                    <div>
                        <h5 class="text-xs font-bold text-zinc-800 ">Portfolios & Properties</h5>
                        <p class="text-[10px] text-zinc-400 ">Enable properties portfolio catalog and listing manager.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="manage-ws-enable-properties" class="sr-only peer">
                        <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950 peer-checked:after:border-transparent"></div>
                    </label>
                </div>

                <!-- Bookings -->
                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-xl bg-zinc-50/20 ">
                    <div>
                        <h5 class="text-xs font-bold text-zinc-800 ">Booking Engine</h5>
                        <p class="text-[10px] text-zinc-400 ">Enable shoot schedules and calendar event slots.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="manage-ws-enable-bookings" class="sr-only peer">
                        <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950 peer-checked:after:border-transparent"></div>
                    </label>
                </div>

                <!-- Financials / Ledger -->
                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-xl bg-zinc-50/20 ">
                    <div>
                        <h5 class="text-xs font-bold text-zinc-800 ">Financials & Ledger</h5>
                        <p class="text-[10px] text-zinc-400 ">Enable invoice listings, GST math breakdowns, and ledgers.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="manage-ws-enable-ledger" class="sr-only peer">
                        <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950 peer-checked:after:border-transparent"></div>
                    </label>
                </div>

                <!-- Documents Vault -->
                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-xl bg-zinc-50/20 ">
                    <div>
                        <h5 class="text-xs font-bold text-zinc-800 ">Document Vault & E-Sign</h5>
                        <p class="text-[10px] text-zinc-400 ">Enable contract uploads, agreements registry, and e-signatures.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="manage-ws-enable-documents" class="sr-only peer">
                        <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950 peer-checked:after:border-transparent"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-6 border-t border-zinc-100 bg-zinc-50/50 flex items-center justify-end gap-3 shrink-0">
        <button onclick="closeManageWorkspaceDrawer()" class="px-4 py-2 text-xs font-bold text-zinc-600 hover:text-zinc-900 transition-colors cursor-pointer">
            Cancel
        </button>
        <button id="manage-ws-save-btn" onclick="saveWorkspaceSettings()" class="px-5 py-2.5 bg-zinc-950 text-white font-bold text-xs rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition-all cursor-pointer shadow-sm">
            Save Settings
        </button>
    </div>
</div>

