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
            <span class="cora-page-emoji text-zinc-900 dark:text-zinc-150 flex shrink-0">
                <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </span>
            <div>
                <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Platform Control Panel</h1>
                <p class="cora-section-desc text-xs text-zinc-500 mt-1">Manage tenant workspaces, configure plan assignments, update user accounts, and trigger secure impersonation sessions.</p>
            </div>
        </div>
    </div>

    <!-- Sub Navigation Tabs -->
    <div class="cora-sub-tabs border-b border-zinc-200 dark:border-zinc-800 flex gap-4 text-xs font-bold text-zinc-500 select-none pb-0.5">
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-admin' ? 'active border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-workspaces">
            Workspaces
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-users' ? 'active border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-users">
            Users
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-appeals' ? 'active border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none flex items-center gap-1.5" data-target="tab-super-appeals">
            Reactivation Appeals <span id="super-appeals-badge" class="px-1.5 py-0.5 rounded-full text-[9.5px] font-bold bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 hidden">0</span>
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-governance' ? 'active border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-governance">
            Attendance & Governance
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-announcements' ? 'active border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-announcements">
            Broadcast Console
        </button>
        <button class="cora-sub-tab <?php echo $active_sub_page === 'super-health' ? 'active border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50' : 'border-transparent text-zinc-500'; ?> pb-2 border-b-2 cursor-pointer focus:outline-none" data-target="tab-super-health">
            System Health & Metrics
        </button>
    </div>

    <!-- TAB 1: WORKSPACES -->
    <div id="tab-super-workspaces" class="cora-tab-content space-y-4 <?php echo $active_sub_page === 'super-admin' ? '' : 'hidden'; ?>">
        <!-- Filters Toolbar -->
        <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-3 items-center flex-1 max-w-2xl">
                <!-- Search bar -->
                <div class="relative w-64">
                    <input type="text" id="workspace-search" oninput="filterWorkspaces()" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg py-1.5 pl-8 pr-3 text-xs bg-white dark:bg-zinc-950 focus:border-zinc-400 dark:focus:border-zinc-600 focus:outline-none text-zinc-900 dark:text-zinc-100" placeholder="Search by name, slug, or owner email...">
                    <span class="absolute left-2.5 top-2.5 text-zinc-450 dark:text-zinc-550">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                </div>
                <!-- Plan Filter -->
                <select id="workspace-filter-plan" onchange="filterWorkspaces()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                    <option value="">All Plans</option>
                    <option value="beta">Beta</option>
                    <option value="starter">Starter</option>
                    <option value="pro">Pro</option>
                    <option value="enterprise">Enterprise</option>
                </select>
                <!-- Industry Filter -->
                <select id="workspace-filter-industry" onchange="filterWorkspaces()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                    <option value="">All Industries</option>
                    <option value="real_estate">Real Estate</option>
                    <option value="photography_studio">Photography Studio</option>
                </select>
                <!-- Status Filter -->
                <select id="workspace-filter-status" onchange="filterWorkspaces()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider" id="workspace-count-badge">0 workspaces</span>
                <button onclick="openCreateWorkspaceDrawer()" class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-lg text-xs font-bold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors shadow-xs cursor-pointer select-none">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    New Workspace
                </button>
            </div>
        </div>

        <!-- Workspaces Table -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/85 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-xs text-left">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-900/80">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Workspace Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Subdomain/Slug</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Plan</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Industry</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Owner (email)</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Created Date</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="workspaces-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-zinc-450 dark:text-zinc-500">Loading workspaces...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: USERS -->
    <div id="tab-super-users" class="cora-tab-content space-y-4 <?php echo $active_sub_page === 'super-users' ? '' : 'hidden'; ?>">
        <!-- Filters Toolbar -->
        <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-3 items-center flex-1 max-w-2xl">
                <!-- Search bar -->
                <div class="relative w-64">
                    <input type="text" id="user-search" oninput="filterUsers()" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg py-1.5 pl-8 pr-3 text-xs bg-white dark:bg-zinc-950 focus:border-zinc-400 dark:focus:border-zinc-600 focus:outline-none text-zinc-900 dark:text-zinc-100" placeholder="Search by name, login, or email...">
                    <span class="absolute left-2.5 top-2.5 text-zinc-450 dark:text-zinc-550">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                </div>
                <!-- Role Filter -->
                <select id="user-filter-role" onchange="filterUsers()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                    <option value="">All Roles</option>
                    <?php foreach ( $roles_list as $role_key => $role_label ) : ?>
                        <option value="<?php echo esc_attr( $role_key ); ?>"><?php echo esc_html( $role_label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Status Filter -->
                <select id="user-filter-status" onchange="filterUsers()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider" id="user-count-badge">0 users</span>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/85 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-xs text-left">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-900/80">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">User Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Email</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Workspace (Agency name)</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Role</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-zinc-450 dark:text-zinc-500">Loading users...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: REACTIVATION APPEALS -->
    <div id="tab-super-appeals" class="cora-tab-content space-y-4 <?php echo $active_sub_page === 'super-appeals' ? '' : 'hidden'; ?>">
        <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Suspension Reactivation Appeals</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Review and manage workspace reactivation requests submitted by suspended users.</p>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider" id="appeals-count-badge">0 appeals</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/85 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-xs text-left">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-900/80">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Account Email</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Workspace Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Contact Phone</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Reason / Message</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Submitted Date</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px] text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="appeals-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-zinc-450 dark:text-zinc-500">Loading appeals...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: ATTENDANCE & GOVERNANCE -->
    <div id="tab-super-governance" class="cora-tab-content space-y-6 <?php echo $active_sub_page === 'super-governance' ? '' : 'hidden'; ?>">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Cross-Tenant Daily Reports & Automation Controls</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Manually trigger automated end-of-day attendance reports to Workspace Owners or manage global automation triggers.</p>
                </div>
                <button onclick="dispatchSuperDailyReports()" class="px-4 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-lg text-xs font-bold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors shadow-xs cursor-pointer flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                    Dispatch Daily Reports Now
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <div class="p-4 bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/80 rounded-lg space-y-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Scheduled Dispatch</div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Every Day at 8:00 PM</div>
                    <div class="text-xs text-zinc-500">Automated WP Cron active</div>
                </div>
                <div class="p-4 bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/80 rounded-lg space-y-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">SMTP Relay Status</div>
                    <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Hostinger Business SMTP Active
                    </div>
                    <div class="text-xs text-zinc-500">heycora@claraverse.in (Port 465 SSL)</div>
                </div>
                <div class="p-4 bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/80 rounded-lg space-y-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Geofence Distance Enforcement</div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Strict Haversine Verified</div>
                    <div class="text-xs text-zinc-500">Real-time GPS coordinate validation</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 5: BROADCAST ANNOUNCEMENTS -->
    <div id="tab-super-announcements" class="cora-tab-content space-y-6 <?php echo $active_sub_page === 'super-announcements' ? '' : 'hidden'; ?>">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-6 shadow-sm space-y-4">
            <div>
                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Global Platform Broadcast Console</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Publish top-bar message banners across all tenant workspaces to communicate system updates, maintenance alerts, or notifications.</p>
            </div>
            
            <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 max-w-2xl">
                <!-- Announcement text -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Broadcast Text Message</label>
                    <textarea id="cora-broadcast-text" rows="3" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-xl p-3 text-xs bg-white dark:bg-zinc-950 focus:border-zinc-400 dark:focus:border-zinc-600 focus:outline-none text-zinc-900 dark:text-zinc-100 font-medium" placeholder="Enter broadcast announcement text..."><?php echo esc_textarea( get_option( 'cora_announcement_text', '' ) ); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Banner style type -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Visual Alert Theme</label>
                        <select id="cora-broadcast-type" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
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
                            <label for="cora-broadcast-active" class="text-xs font-bold text-zinc-700 dark:text-zinc-300 cursor-pointer select-none">Enable Public Broadcasting</label>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button onclick="saveGlobalAnnouncement()" class="px-4 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-lg text-xs font-bold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors shadow-xs cursor-pointer">
                        Save and Broadcast Banner
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 6: SYSTEM HEALTH & METRICS -->
    <div id="tab-super-health" class="cora-tab-content space-y-6 <?php echo $active_sub_page === 'super-health' ? '' : 'hidden'; ?>">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Platform Specs & Resource Auditing</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Real-time computation of hosting infrastructure size, dynamic table indexes, and workspace attachments usage.</p>
                </div>
                <button onclick="loadHealthMetrics()" class="px-4 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-lg text-xs font-bold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors shadow-xs cursor-pointer flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                    Refresh Health Metrics
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <!-- DB Size widget -->
                <div class="p-4 bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/80 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">MySQL Database Footprint</div>
                    <div id="metric-db-size" class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-mono">-- MB</div>
                    <div class="text-[10px] text-zinc-500">Total data and index allocation</div>
                </div>
                <!-- File Storage widget -->
                <div class="p-4 bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/80 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">Media Vault Allocation</div>
                    <div id="metric-storage-size" class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-mono">-- MB</div>
                    <div class="text-[10px] text-zinc-500">Tenant media library uploads</div>
                </div>
                <!-- Workspaces count -->
                <div class="p-4 bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/80 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">Active Workspaces</div>
                    <div id="metric-workspaces" class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-mono">--</div>
                    <div class="text-[10px] text-zinc-500">Provisioned multi-tenant directories</div>
                </div>
                <!-- Users count -->
                <div class="p-4 bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/80 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">Registered Users</div>
                    <div id="metric-users" class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-mono">--</div>
                    <div class="text-[10px] text-zinc-500">Across all platform organizations</div>
                </div>
                <!-- PHP version info -->
                <div class="p-4 bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/80 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">PHP Runtime</div>
                    <div id="metric-php-version" class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-mono">PHP --</div>
                    <div class="text-[10px] text-zinc-500">Active server engine specifications</div>
                </div>
                <!-- WordPress core info -->
                <div class="p-4 bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/80 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400">WordPress Core</div>
                    <div id="metric-wp-version" class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-mono">WP --</div>
                    <div class="text-[10px] text-zinc-500">System core framework version</div>
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
                $('#workspaces-table-body').html(`<tr><td colspan="7" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 dark:bg-red-950/10 border-t border-zinc-100 dark:border-zinc-800">${escapeHtml(errorMsg)}</td></tr>`);
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
            }
        }).fail(function() {
            $('#workspaces-table-body').html('<tr><td colspan="7" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 dark:bg-red-950/10 border-t border-zinc-100 dark:border-zinc-800">Connection error: Could not retrieve workspaces.</td></tr>');
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
                $('#users-table-body').html(`<tr><td colspan="6" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 dark:bg-red-950/10 border-t border-zinc-100 dark:border-zinc-800">${escapeHtml(errorMsg)}</td></tr>`);
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
            }
        }).fail(function() {
            $('#users-table-body').html('<tr><td colspan="6" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 dark:bg-red-950/10 border-t border-zinc-100 dark:border-zinc-800">Connection error: Could not retrieve users.</td></tr>');
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
            $('#appeals-table-body').html('<tr><td colspan="7" class="px-5 py-8 text-center text-zinc-400 dark:text-zinc-500 bg-zinc-50/20 dark:bg-zinc-900/10">No reactivation appeals submitted yet.</td></tr>');
            return;
        }

        let html = '';
        rawAppeals.forEach(a => {
            let statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Pending</span>';
            if (a.status === 'approved') {
                statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Approved</span>';
            } else if (a.status === 'declined') {
                statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Declined</span>';
            }

            html += `
                <tr class="hover:bg-zinc-50/20 dark:hover:bg-zinc-800/15 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">${escapeHtml(a.email)}</td>
                    <td class="px-5 py-3.5 text-zinc-700 dark:text-zinc-300 font-medium">${escapeHtml(a.workspace_name || '—')}</td>
                    <td class="px-5 py-3.5 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">${escapeHtml(a.phone || '—')}</td>
                    <td class="px-5 py-3.5 text-zinc-600 dark:text-zinc-400 max-w-xs truncate">${escapeHtml(a.reason)}</td>
                    <td class="px-5 py-3.5">${statusBadge}</td>
                    <td class="px-5 py-3.5 text-zinc-400 dark:text-zinc-500 font-medium">${formatDate(a.created_at)}</td>
                    <td class="px-5 py-3.5 text-right">
                        <button onclick="openAppealReviewDrawer('${a.id}')" class="px-2.5 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer shadow-xs active:scale-95 transition-all">
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
            $('#workspaces-table-body').html('<tr><td colspan="8" class="px-5 py-8 text-center text-zinc-400 dark:text-zinc-500 bg-zinc-50/20 dark:bg-zinc-900/10">No workspaces matching filters found.</td></tr>');
            return;
        }

        let html = '';
        filtered.forEach(ws => {
            const planBadge = ws.plan 
                ? `<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">${escapeHtml(ws.plan)}</span>` 
                : '<span class="text-zinc-400">—</span>';
            
            let statusBadge = ws.status === 'active'
                ? '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 select-none">Active</span>'
                : '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 select-none">Suspended</span>';

            const pendingAppeal = rawAppeals.find(a => a.status === 'pending' && (
                (ws.owner_email && a.email && a.email.toLowerCase() === ws.owner_email.toLowerCase()) ||
                (ws.name && a.workspace_name && a.workspace_name.toLowerCase() === ws.name.toLowerCase()) ||
                (ws.slug && a.workspace_name && a.workspace_name.toLowerCase() === ws.slug.toLowerCase())
            ));

            if (ws.status === 'suspended' && pendingAppeal) {
                statusBadge += `<button onclick="openAppealReviewDrawer('${pendingAppeal.id}')" class="ml-1.5 px-2 py-0.5 text-[9px] font-bold rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 hover:bg-amber-200 cursor-pointer select-none inline-flex items-center gap-1"><span>📩</span> Appeal Pending</button>`;
            }

            const toggleLabel = ws.status === 'active' ? 'Suspend' : 'Activate';
            const toggleClass = ws.status === 'active'
                ? 'text-red-600 hover:text-red-700 border-zinc-200 hover:bg-red-50 dark:border-zinc-800 dark:hover:bg-red-950/20'
                : 'text-emerald-600 hover:text-emerald-700 border-zinc-200 hover:bg-emerald-50 dark:border-zinc-800 dark:hover:bg-emerald-950/20';

            const currInd = ws.industry === 'photography' ? 'photography_studio' : (ws.industry || 'real_estate');

            const indIcon = currInd === 'photography_studio'
                ? `<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>`
                : `<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>`;

            html += `
                <tr class="hover:bg-zinc-50/20 dark:hover:bg-zinc-800/15 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">${escapeHtml(ws.name)}</td>
                    <td class="px-5 py-3.5 text-zinc-550 dark:text-zinc-400 font-mono text-[11px]">${escapeHtml(ws.slug)}</td>
                    <td class="px-5 py-3.5">${planBadge}</td>
                    <td class="px-5 py-3.5">
                        <div class="inline-flex items-center gap-1.5 px-2 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-900 shadow-xs">
                            <span class="text-zinc-500 dark:text-zinc-400 shrink-0">${indIcon}</span>
                            <select onchange="changeWorkspaceIndustry(${ws.id}, this.value)" class="text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-transparent outline-none cursor-pointer">
                                <option value="real_estate" ${currInd === 'real_estate' ? 'selected' : ''}>Real Estate</option>
                                <option value="photography_studio" ${currInd === 'photography_studio' ? 'selected' : ''}>Studio</option>
                            </select>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">${statusBadge}</td>
                    <td class="px-5 py-3.5 text-zinc-500 dark:text-zinc-400 font-medium">${escapeHtml(ws.owner_email || '—')}</td>
                    <td class="px-5 py-3.5 text-zinc-400 dark:text-zinc-500 font-medium">${formatDate(ws.created_at)}</td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="toggleWorkspaceStatus(${ws.id}, '${ws.status === 'active' ? 'suspended' : 'active'}')" class="px-2.5 py-1 border rounded-lg text-[10px] font-bold bg-white dark:bg-zinc-900 hover:bg-zinc-50 cursor-pointer shadow-sm active:scale-95 transition-all ${toggleClass}">
                                ${toggleLabel}
                            </button>
                            
                            <select onchange="changeWorkspacePlan(${ws.id}, this.value)" class="px-2 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 outline-none cursor-pointer shadow-sm">
                                <option value="beta" ${ws.plan === 'beta' ? 'selected' : ''}>Beta</option>
                                <option value="starter" ${ws.plan === 'starter' ? 'selected' : ''}>Starter</option>
                                <option value="pro" ${ws.plan === 'pro' ? 'selected' : ''}>Pro</option>
                                <option value="enterprise" ${ws.plan === 'enterprise' ? 'selected' : ''}>Enterprise</option>
                            </select>

                            ${ws.owner_user_id ? `
                            <button onclick="impersonateUser(${ws.owner_user_id})" class="px-2.5 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer shadow-sm active:scale-95 transition-all inline-flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                Impersonate Owner
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
            $('#users-table-body').html('<tr><td colspan="6" class="px-5 py-8 text-center text-zinc-400 dark:text-zinc-500 bg-zinc-50/20 dark:bg-zinc-900/10">No users matching filters found.</td></tr>');
            return;
        }

        let html = '';
        filtered.forEach(u => {
            const roleLabel = coraRoleLabels[u.role] || u.role;
            
            const statusBadge = u.status === 'active'
                ? '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 select-none">Active</span>'
                : '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 select-none">Inactive</span>';

            const toggleLabel = u.status === 'active' ? 'Deactivate' : 'Activate';
            const toggleClass = u.status === 'active'
                ? 'text-red-600 hover:text-red-700 border-zinc-200 hover:bg-red-50 dark:border-zinc-800 dark:hover:bg-red-950/20'
                : 'text-emerald-600 hover:text-emerald-700 border-zinc-200 hover:bg-emerald-50 dark:border-zinc-800 dark:hover:bg-emerald-950/20';

            // Build select options for roles list
            let roleOptions = '';
            Object.keys(coraRoleLabels).forEach(rKey => {
                roleOptions += `<option value="${rKey}" ${u.role === rKey ? 'selected' : ''}>${escapeHtml(coraRoleLabels[rKey])}</option>`;
            });

            html += `
                <tr class="hover:bg-zinc-50/20 dark:hover:bg-zinc-800/15 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full ${u.status === 'active' ? 'bg-emerald-500' : 'bg-zinc-300 dark:bg-zinc-600'}"></span>
                        ${escapeHtml(u.display_name || u.user_login || '')}
                    </td>
                    <td class="px-5 py-3.5 text-zinc-500 dark:text-zinc-400 font-medium">${escapeHtml(u.user_email || '—')}</td>
                    <td class="px-5 py-3.5 font-semibold text-zinc-800 dark:text-zinc-250">${escapeHtml(u.agency_name || '—')}</td>
                    <td class="px-5 py-3.5">
                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-350 uppercase tracking-wide">
                            ${escapeHtml(roleLabel)}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">${statusBadge}</td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="toggleUserStatus(${u.id}, '${u.status === 'active' ? 'inactive' : 'active'}')" class="px-2.5 py-1 border rounded-lg text-[10px] font-bold bg-white dark:bg-zinc-900 hover:bg-zinc-50 cursor-pointer shadow-sm active:scale-95 transition-all ${toggleClass}">
                                ${toggleLabel}
                            </button>

                            <select onchange="changeUserRole(${u.id}, this.value)" class="px-2 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 outline-none cursor-pointer shadow-sm">
                                ${roleOptions}
                            </select>

                            <button onclick="impersonateUser(${u.wp_user_id})" class="px-2.5 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer shadow-sm active:scale-95 transition-all inline-flex items-center gap-1.5">
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

    // Run dynamic retrieval on mount
    loadPlatformData();
    if (coraREData.currentPage === 'super-health') {
        loadHealthMetrics();
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
</script>

<!-- Create Workspace Right-Sliding Drawer Overlay & Panel -->
<div id="cora-add-workspace-overlay" onclick="closeCreateWorkspaceDrawer()" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-add-workspace-drawer" class="fixed top-0 right-0 h-full w-full sm:w-112 bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Drawer Header -->
    <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-850 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-zinc-100 dark:bg-zinc-900 rounded-lg text-zinc-900 dark:text-zinc-100">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Create Independent Workspace</h3>
                <p class="text-xs text-zinc-500 font-mono">app.heycora.in/{{slug}}</p>
            </div>
        </div>
        <button onclick="closeCreateWorkspaceDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Drawer Content Form -->
    <div class="flex-1 overflow-y-auto p-6 space-y-5">
        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Workspace Name *</label>
            <input type="text" id="new-ws-name" oninput="autoSlugifyWorkspace(this.value)" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3.5 py-2.5 text-xs bg-white dark:bg-zinc-900 focus:border-zinc-400 outline-none text-zinc-900 dark:text-zinc-100" placeholder="e.g. Apex Realty Studio">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Workspace Slug / URL *</label>
            <div class="flex items-center border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden bg-white dark:bg-zinc-900 focus-within:border-zinc-400">
                <span class="px-3 py-2.5 text-xs font-mono text-zinc-400 dark:text-zinc-500 bg-zinc-50 dark:bg-zinc-950 border-r border-zinc-200 dark:border-zinc-800 shrink-0">app.heycora.in/</span>
                <input type="text" id="new-ws-slug" class="w-full px-3 py-2.5 text-xs font-mono bg-transparent outline-none text-zinc-900 dark:text-zinc-100" placeholder="apex-realty">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Subscription Plan</label>
            <select id="new-ws-plan" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3.5 py-2.5 text-xs bg-white dark:bg-zinc-900 outline-none cursor-pointer text-zinc-900 dark:text-zinc-100">
                <option value="starter">Starter Plan</option>
                <option value="pro">Pro Plan</option>
                <option value="enterprise" selected>Enterprise Plan</option>
                <option value="beta">Beta Plan</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Industry Profile *</label>
            <select id="new-ws-industry" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3.5 py-2.5 text-xs bg-white dark:bg-zinc-900 outline-none cursor-pointer text-zinc-900 dark:text-zinc-100">
                <option value="real_estate" selected>Real Estate Agency</option>
                <option value="photography_studio">Photography Studio</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Owner Account Email</label>
            <input type="email" id="new-ws-owner-email" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3.5 py-2.5 text-xs bg-white dark:bg-zinc-900 focus:border-zinc-400 outline-none text-zinc-900 dark:text-zinc-100" placeholder="owner@agency.com">
            <p class="text-[11px] text-zinc-400 mt-1">If the email matches an existing user, they will be assigned as workspace owner.</p>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="p-6 border-t border-zinc-100 dark:border-zinc-850 bg-zinc-50/50 dark:bg-zinc-900/40 flex items-center justify-end gap-3 shrink-0">
        <button onclick="closeCreateWorkspaceDrawer()" class="px-4 py-2 text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors cursor-pointer">
            Cancel
        </button>
        <button onclick="submitNewWorkspace()" class="px-5 py-2.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold text-xs rounded-xl hover:bg-zinc-800 dark:hover:bg-zinc-100 active:scale-[0.98] transition-all cursor-pointer shadow-sm">
            Create Workspace
        </button>
    </div>
</div>

<!-- Review Suspension Appeal Right-Sliding Drawer -->
<div id="cora-appeal-review-overlay" onclick="closeAppealReviewDrawer()" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-appeal-review-drawer" class="fixed top-0 right-0 h-full w-full sm:w-120 bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-850 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-amber-50 dark:bg-amber-950/40 text-amber-600 rounded-lg">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Review Reactivation Appeal</h3>
                <p class="text-xs text-zinc-500 font-mono" id="review-appeal-id">appeal_...</p>
            </div>
        </div>
        <button onclick="closeAppealReviewDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto p-6 space-y-4">
        <div class="p-4 bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-3">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Account Email</span>
                <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100" id="review-appeal-email">—</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Workspace / Agency</span>
                <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100" id="review-appeal-workspace">—</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Contact Phone</span>
                <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100" id="review-appeal-phone">—</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Submitted Date</span>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 font-mono" id="review-appeal-date">—</p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Applicant Reason / Explanation</label>
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 leading-relaxed whitespace-pre-wrap" id="review-appeal-reason">—</div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Administrator Response Notes (Sent to User)</label>
            <textarea id="review-appeal-notes" rows="3" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-xl p-3 text-xs bg-white dark:bg-zinc-900 outline-none text-zinc-900 dark:text-zinc-100" placeholder="Optional explanation included in the confirmation email..."></textarea>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-6 border-t border-zinc-100 dark:border-zinc-850 bg-zinc-50/50 dark:bg-zinc-900/40 flex items-center justify-between gap-3 shrink-0">
        <button onclick="processAppealAction('decline')" class="px-4 py-2 bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 font-bold text-xs rounded-xl hover:bg-red-100 cursor-pointer transition-all">
            Decline Appeal
        </button>
        <button onclick="processAppealAction('approve')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl active:scale-[0.98] transition-all cursor-pointer shadow-sm flex items-center gap-1.5">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Approve & Reactivate Workspace
        </button>
    </div>
</div>
