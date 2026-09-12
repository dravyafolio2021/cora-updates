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
$active_sub_page = isset( $sub_page ) && ! empty( $sub_page ) ? $sub_page : ( isset( $GLOBALS['sub_page'] ) && ! empty( $GLOBALS['sub_page'] ) ? $GLOBALS['sub_page'] : ( isset( $_GET['sub_page'] ) ? sanitize_key( $_GET['sub_page'] ) : ( isset( $_GET['sub'] ) ? sanitize_key( $_GET['sub'] ) : 'super-admin' ) ) );
if ( empty( $active_sub_page ) || $active_sub_page === 'dashboard' ) {
    $active_sub_page = 'super-admin';
}
// Dynamic Header Info based on active sub-page
$page_headers = array(
    'super-admin'         => array(
        'title' => 'Platform Control Panel',
        'desc'  => 'Manage tenant workspaces, configure plan assignments, and trigger secure impersonation sessions.'
    ),
    'super-users'         => array(
        'title' => 'Platform User Directory',
        'desc'  => 'Audit, inspect, and manage administrative and staff accounts across all tenant workspaces.'
    ),
    'super-finances'      => array(
        'title' => 'Platform Financials & Revenue Engine',
        'desc'  => 'Cross-tenant MRR run-rate, ARR projections, cashflow collections, GST tax ledger, and workspace unit economics.'
    ),
    'super-appeals'       => array(
        'title' => 'Suspension Reactivation Appeals',
        'desc'  => 'Review suspension appeals, governance inquiries, and restore tenant workspace access.'
    ),
    'super-governance'    => array(
        'title' => 'Workplace Attendance & Governance',
        'desc'  => 'Cross-tenant staff attendance records, shift timestamps, and compliance tracking.'
    ),
    'super-announcements' => array(
        'title' => 'System Broadcast Console',
        'desc'  => 'Publish real-time system alerts, maintenance bulletins, and platform announcements.'
    ),
    'super-health'        => array(
        'title' => 'System Health & Telemetry',
        'desc'  => 'Real-time telemetry, database health, API latency, and server diagnostic metrics.'
    ),
    'super-ai-tokens'     => array(
        'title' => 'AI Master Tokens & Quotas',
        'desc'  => 'Cross-tenant token consumption telemetry, model rate-limits, and quota management.'
    ),
    'super-feature-flags' => array(
        'title' => 'Feature Flags & Capabilities',
        'desc'  => 'Control platform modules, beta feature access, and tenant capability overrides.'
    ),
    'super-emergency'     => array(
        'title' => 'Emergency Command Center',
        'desc'  => 'Instant lockdown controls, session invalidations, and system maintenance switches.'
    ),
    'super-audit'         => array(
        'title' => 'Global Forensics & Audit Stream',
        'desc'  => 'Immutable chronological log stream of administrative actions and security events.'
    ),
);
$current_header = isset( $page_headers[$active_sub_page] ) ? $page_headers[$active_sub_page] : $page_headers['super-admin'];
?>

<!-- Platform Admin View Container -->
<div class="space-y-4 sm:space-y-6 pb-28 sm:pb-16">
    <div class="flex items-center justify-between gap-3">
        <div class="cora-page-header flex items-center gap-2.5 sm:gap-3 min-w-0">
            <span class="cora-page-emoji text-zinc-900 flex shrink-0 p-1.5 sm:p-2 bg-zinc-100 rounded-xl">
                <svg viewBox="0 0 24 24" width="20" height="20" class="sm:w-6 sm:h-6" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </span>
            <div class="min-w-0">
                <h1 class="cora-page-title text-base sm:text-2xl font-bold tracking-tight text-zinc-900 truncate"><?php echo esc_html( $current_header['title'] ); ?></h1>
                <p class="cora-section-desc text-[11px] sm:text-xs text-zinc-500 mt-0.5 line-clamp-1 sm:line-clamp-none"><?php echo esc_html( $current_header['desc'] ); ?></p>
            </div>
        </div>

        <?php if ( $active_sub_page === 'super-admin' ) : ?>
        <button onclick="openCreateWorkspaceDrawer()" class="shrink-0 flex items-center gap-1.5 px-3 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 transition-all shadow-xs cursor-pointer select-none active:scale-95">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span class="hidden xs:inline">New Workspace</span>
            <span class="xs:hidden">New</span>
        </button>
        <?php endif; ?>
    </div>

    <!-- TAB 1: WORKSPACES & DYNAMIC TRACKING -->
    <div id="tab-super-workspaces" class="cora-tab-content space-y-4 sm:space-y-5 <?php echo $active_sub_page === 'super-admin' ? '' : 'hidden'; ?>">
        <!-- Live Real-Time Telemetry Metrics Deck (2x2 on mobile, 4-col on desktop) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">
            <!-- Metric 1: Workspaces Health & Pulse -->
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[9.5px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider truncate">Active Instances</span>
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="stat-live-pulse-badge">0 Online</span>
                    </span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-lg sm:text-2xl font-black text-zinc-950 tracking-tight font-mono" id="stat-active-workspaces">0</span>
                    <span class="text-[10.5px] sm:text-xs font-semibold text-zinc-400">/ <span id="stat-total-workspaces">0</span></span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-400 truncate mt-0.5">Tenant workspaces</p>
            </div>

            <!-- Metric 2: AI Token Consumption -->
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[9.5px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider truncate">AI Token Burn</span>
                    <span class="text-[9px] sm:text-[10px] font-mono text-zinc-400 shrink-0 hidden xs:inline">Models</span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-lg sm:text-2xl font-black text-zinc-950 tracking-tight font-mono" id="stat-total-tokens">0</span>
                    <span class="text-[10.5px] sm:text-xs font-semibold text-zinc-400">burned</span>
                </div>
                <div class="w-full bg-zinc-100 rounded-full h-1 mt-1 overflow-hidden">
                    <div id="stat-token-burn-bar" class="bg-zinc-900 h-1 rounded-full transition-all duration-500" style="width: 15%"></div>
                </div>
            </div>

            <!-- Metric 3: Active Operators & Team Members -->
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[9.5px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider truncate">Team Capacity</span>
                    <span class="text-[9px] sm:text-[10px] text-zinc-400 shrink-0 hidden xs:inline">Staff</span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-lg sm:text-2xl font-black text-zinc-950 tracking-tight font-mono" id="stat-total-users">0</span>
                    <span class="text-[10.5px] sm:text-xs font-semibold text-zinc-400">members</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-400 truncate mt-0.5">Active operators</p>
            </div>

            <!-- Metric 4: Suspension & Appeals Monitor -->
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[9.5px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider truncate">Appeals</span>
                    <span id="stat-security-status-badge" class="px-1.5 py-0.2 rounded-full text-[9px] sm:text-[10px] font-bold bg-zinc-100 text-zinc-700 shrink-0">Nominal</span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-lg sm:text-2xl font-black text-zinc-950 tracking-tight font-mono" id="stat-appeals-summary">0</span>
                    <span class="text-[10.5px] sm:text-xs font-semibold text-zinc-400">pending</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-400 truncate mt-0.5"><span id="stat-suspended-count">0</span> suspended</p>
            </div>
        </div>

        <!-- Dynamic Quick-Filters & Search Toolbar -->
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-2.5 sm:p-4 shadow-xs space-y-2.5">
            <!-- Row 1: Segmented Control Filter Pills -->
            <div class="overflow-x-auto no-scrollbar flex items-center p-1 bg-zinc-100 rounded-xl text-xs font-bold text-zinc-600 gap-1 select-none flex-nowrap w-full -webkit-overflow-scrolling-touch">
                <button type="button" onclick="setWorkspaceFilterCategory('all')" id="btn-filter-all" class="px-3 py-1.5 rounded-lg bg-white text-zinc-950 shadow-xs cursor-pointer transition-all whitespace-nowrap shrink-0">
                    All (<span id="pill-count-all">0</span>)
                </button>
                <button type="button" onclick="setWorkspaceFilterCategory('active')" id="btn-filter-active" class="px-3 py-1.5 rounded-lg text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 cursor-pointer transition-all inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active (<span id="pill-count-active">0</span>)
                </button>
                <button type="button" onclick="setWorkspaceFilterCategory('real_estate')" id="btn-filter-re" class="px-3 py-1.5 rounded-lg text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 cursor-pointer transition-all inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg> Real Estate (<span id="pill-count-re">0</span>)
                </button>
                <button type="button" onclick="setWorkspaceFilterCategory('photography_studio')" id="btn-filter-studio" class="px-3 py-1.5 rounded-lg text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 cursor-pointer transition-all inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg> Studio (<span id="pill-count-studio">0</span>)
                </button>
                <button type="button" onclick="setWorkspaceFilterCategory('suspended')" id="btn-filter-suspended" class="px-3 py-1.5 rounded-lg text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 cursor-pointer transition-all inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspended (<span id="pill-count-suspended">0</span>)
                </button>
            </div>

            <!-- Row 2: Search, Sorters and View Switcher -->
            <div class="flex items-center justify-between gap-2 pt-1 border-t border-zinc-100">
                <div class="relative flex-1 min-w-0">
                    <input type="text" id="workspace-search" oninput="renderWorkspaces()" class="w-full border border-zinc-200 rounded-xl py-1.5 sm:py-2 pl-8 pr-3 text-xs bg-white focus:border-zinc-400 focus:outline-none text-zinc-900 placeholder-zinc-400 transition-colors" placeholder="Search workspaces...">
                    <span class="absolute left-2.5 top-2 sm:top-2.5 text-zinc-400">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <select id="workspace-sort-by" onchange="renderWorkspaces()" class="border border-zinc-200 rounded-xl px-2 py-1.5 sm:py-2 text-xs text-zinc-700 bg-white outline-none cursor-pointer max-w-[110px] sm:max-w-none truncate">
                        <option value="recent">Recent</option>
                        <option value="tokens">AI Tokens</option>
                        <option value="users">Team</option>
                        <option value="name">Name (A-Z)</option>
                    </select>

                    <div class="flex items-center bg-zinc-100 p-0.5 rounded-xl border border-zinc-200/80 shrink-0">
                        <button type="button" id="ws-view-grid-btn" onclick="setWorkspaceViewMode('grid')" class="px-2 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 bg-white text-zinc-950 shadow-xs cursor-pointer" title="Cards View">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect></svg>
                        </button>
                        <button type="button" id="ws-view-table-btn" onclick="setWorkspaceViewMode('table')" class="px-2 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 text-zinc-500 hover:text-zinc-900 cursor-pointer" title="Table View">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </button>
                    </div>

                    <span class="text-[10.5px] font-bold text-zinc-400 uppercase tracking-wider pl-2 border-l border-zinc-200 hidden md:inline" id="workspace-count-badge">0 workspaces</span>
                </div>
            </div>
        </div>

        <!-- Professional Workspaces Card Grid View (Primary Default) -->
        <div id="workspaces-grid-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4.5">
            <div class="col-span-full bg-white border border-zinc-200/80 rounded-2xl p-12 text-center text-zinc-400">
                <div class="w-8 h-8 mx-auto mb-2 text-zinc-300 animate-spin">
                    <svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="12"></circle></svg>
                </div>
                <p class="text-xs font-medium">Loading real-time workspace telemetry...</p>
            </div>
        </div>

        <!-- Dynamic Workspaces Table View (Alternative) -->
        <div id="workspaces-table-container" class="hidden bg-white border border-zinc-200/85 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                    <thead class="bg-zinc-50/70">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Workspace & Domain</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Owner / Contact</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Activity Pulse & Team</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">AI & Storage Burn</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Plan & Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Quick Controls</th>
                        </tr>
                    </thead>
                    <tbody id="workspaces-table-body" class="divide-y divide-zinc-100">
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-zinc-450">Loading real-time workspace telemetry...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: USERS -->
    <div id="tab-super-users" class="cora-tab-content space-y-4 <?php echo $active_sub_page === 'super-users' ? '' : 'hidden'; ?>">
        <!-- Filters Toolbar -->
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-2.5 sm:p-4 shadow-xs flex flex-wrap gap-2.5 items-center justify-between">
            <div class="flex flex-wrap gap-2 items-center flex-1 min-w-0">
                <!-- Search bar -->
                <div class="relative flex-1 min-w-[180px]">
                    <input type="text" id="user-search" oninput="filterUsers()" class="w-full border border-zinc-200 rounded-xl py-1.5 sm:py-2 pl-8 pr-3 text-xs bg-white focus:border-zinc-400 focus:outline-none text-zinc-900 transition-colors" placeholder="Search by name, login, or email...">
                    <span class="absolute left-2.5 top-2 sm:top-2.5 text-zinc-400">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                </div>
                <!-- Role Filter -->
                <select id="user-filter-role" onchange="filterUsers()" class="border border-zinc-200 rounded-xl px-2.5 py-1.5 sm:py-2 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Roles</option>
                    <?php foreach ( $roles_list as $role_key => $role_label ) : ?>
                        <option value="<?php echo esc_attr( $role_key ); ?>"><?php echo esc_html( $role_label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Status Filter -->
                <select id="user-filter-status" onchange="filterUsers()" class="border border-zinc-200 rounded-xl px-2.5 py-1.5 sm:py-2 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider hidden sm:inline" id="user-count-badge">0 users</span>
        </div>

        <!-- Users Table -->
        <div class="bg-white border border-zinc-200/85 rounded-xl sm:rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                    <thead class="bg-zinc-50/70">
                        <tr>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">User Name</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Email</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Workspace (Agency name)</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Role</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body" class="divide-y divide-zinc-100">
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-zinc-400">Loading users...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: PLATFORM FINANCIALS & REVENUE ENGINE -->
    <div id="tab-super-finances" class="cora-tab-content space-y-4 sm:space-y-6 <?php echo $active_sub_page === 'super-finances' ? '' : 'hidden'; ?>">
        <!-- 5-Metric Financial Deck (2-col on mobile, 3-col on tablet, 5-col on desktop) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-3.5">
            <!-- Metric 1: Monthly Recurring Revenue (MRR) -->
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[9.5px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider truncate">Platform MRR</span>
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 sm:py-0.5 rounded text-[9px] sm:text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono shrink-0">
                        Run-Rate
                    </span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-lg sm:text-2xl font-black text-zinc-950 font-mono tracking-tight" id="fin-stat-mrr">₹0</span>
                    <span class="text-[10.5px] sm:text-xs font-semibold text-zinc-400">/ mo</span>
                </div>
                <div class="mt-0.5 sm:mt-1 flex items-center justify-between text-[10px] sm:text-[11px] text-zinc-500">
                    <span>Base + Add-ons</span>
                    <span class="font-mono text-[9.5px] sm:text-[10px] text-zinc-400 truncate" id="fin-stat-base-mrr">₹0 base</span>
                </div>
            </div>

            <!-- Metric 2: Annual Recurring Revenue (ARR) -->
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[9.5px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider truncate">Annual ARR</span>
                    <span class="text-[9px] sm:text-[10px] font-bold text-zinc-500 font-mono shrink-0">12x MRR</span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-lg sm:text-2xl font-black text-zinc-950 font-mono tracking-tight" id="fin-stat-arr">₹0</span>
                    <span class="text-[10.5px] sm:text-xs font-semibold text-zinc-400">/ yr</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-400 truncate mt-0.5">Contract run-rate</p>
            </div>

            <!-- Metric 3: Realized Cashflow Collections -->
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[9.5px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider truncate">Cash Collected</span>
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 sm:py-0.5 rounded-full text-[9px] sm:text-[9.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Realized
                    </span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-lg sm:text-2xl font-black text-zinc-950 font-mono tracking-tight" id="fin-stat-cash">₹0</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-400 truncate mt-0.5">Total cash received</p>
            </div>

            <!-- Metric 4: Average Revenue Per User/Tenant (ARPU) -->
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[9.5px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider truncate">Blended ARPU</span>
                    <span class="text-[9px] sm:text-[10px] font-bold text-zinc-500 shrink-0">Per Tenant</span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-lg sm:text-2xl font-black text-zinc-950 font-mono tracking-tight" id="fin-stat-arpu">₹0</span>
                    <span class="text-[10.5px] sm:text-xs font-semibold text-zinc-400">/ mo</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-400 truncate mt-0.5">Average tenant yield</p>
            </div>

            <!-- Metric 5: 18% GST Compliance Ledger -->
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs flex flex-col justify-between col-span-2 sm:col-span-1">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[9.5px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider truncate">18% GST Ledger</span>
                    <span class="text-[9px] sm:text-[10px] font-bold text-zinc-500 font-mono shrink-0">CGST + SGST</span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-lg sm:text-2xl font-black text-zinc-950 font-mono tracking-tight" id="fin-stat-gst">₹0</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-400 truncate mt-0.5 font-mono">9% CGST + 9% SGST liability</p>
            </div>
        </div>

        <!-- 2-Column Analytics Distribution Panel -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3.5 sm:gap-5">
            <!-- Left Card: Revenue & Workspaces by Plan Tier -->
            <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900">Revenue by Plan Tier</h3>
                        <p class="text-xs text-zinc-500 mt-0.5">Workspace distribution and MRR yield per subscription tier</p>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-zinc-100 text-zinc-700">4 Tiers</span>
                </div>

                <div class="space-y-3.5">
                    <!-- Starter Tier -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-zinc-900">Starter</span>
                                <span class="text-[11px] text-zinc-400 font-mono">(₹999/mo · ₹833/mo ann)</span>
                            </div>
                            <div class="flex items-center gap-3 font-mono">
                                <span class="text-zinc-500" id="fin-tier-starter-count">0 tenants</span>
                                <span class="font-bold text-zinc-900" id="fin-tier-starter-mrr">₹0 /mo</span>
                            </div>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-2 overflow-hidden">
                            <div id="fin-tier-starter-bar" class="bg-zinc-900 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Professional Tier -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-zinc-900">Professional</span>
                                <span class="text-[11px] text-zinc-400 font-mono">(₹1,999/mo · ₹1,665/mo ann)</span>
                                <span class="px-1 py-0.2 rounded text-[9px] font-bold bg-zinc-900 text-white uppercase">Popular</span>
                            </div>
                            <div class="flex items-center gap-3 font-mono">
                                <span class="text-zinc-500" id="fin-tier-pro-count">0 tenants</span>
                                <span class="font-bold text-zinc-900" id="fin-tier-pro-mrr">₹0 /mo</span>
                            </div>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-2 overflow-hidden">
                            <div id="fin-tier-pro-bar" class="bg-zinc-900 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Scale Tier -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-zinc-900">Scale</span>
                                <span class="text-[11px] text-zinc-400 font-mono">(₹2,999/mo · ₹2,499/mo ann)</span>
                            </div>
                            <div class="flex items-center gap-3 font-mono">
                                <span class="text-zinc-500" id="fin-tier-scale-count">0 tenants</span>
                                <span class="font-bold text-zinc-900" id="fin-tier-scale-mrr">₹0 /mo</span>
                            </div>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-2 overflow-hidden">
                            <div id="fin-tier-scale-bar" class="bg-zinc-900 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- India Only Tier -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-zinc-900">India Only</span>
                                <span class="text-[11px] text-zinc-400 font-mono">(₹499/mo · ₹5,988/yr ann commit)</span>
                            </div>
                            <div class="flex items-center gap-3 font-mono">
                                <span class="text-zinc-500" id="fin-tier-india-count">0 tenants</span>
                                <span class="font-bold text-zinc-900" id="fin-tier-india-mrr">₹0 /mo</span>
                            </div>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-2 overflow-hidden">
                            <div id="fin-tier-india-bar" class="bg-zinc-900 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card: Revenue Streams & Add-on Unit Economics -->
            <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900">Revenue Composition & Unit Economics</h3>
                        <p class="text-xs text-zinc-500 mt-0.5">Core subscriptions vs recurring usage top-up add-ons</p>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-zinc-100 text-zinc-700">MRR Mix</span>
                </div>

                <div class="space-y-4">
                    <!-- Base Subscriptions Stream -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-zinc-700">Base Workspace Subscriptions</span>
                            <div class="flex items-center gap-2 font-mono">
                                <span class="text-zinc-400 text-[11px]" id="fin-stream-base-pct">0%</span>
                                <span class="font-bold text-zinc-900" id="fin-stream-base-mrr">₹0 /mo</span>
                            </div>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-2 overflow-hidden">
                            <div id="fin-stream-base-bar" class="bg-zinc-900 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- AI Runs Add-on Stream -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="font-semibold text-zinc-700">AI Runs Top-Up Add-ons</span>
                                <span class="text-[10px] font-mono text-zinc-400">(@ ₹100 / 1K runs)</span>
                            </div>
                            <div class="flex items-center gap-2 font-mono">
                                <span class="text-zinc-400 text-[11px]" id="fin-stream-ai-pct">0%</span>
                                <span class="font-bold text-zinc-900" id="fin-stream-ai-mrr">₹0 /mo</span>
                            </div>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-2 overflow-hidden">
                            <div id="fin-stream-ai-bar" class="bg-zinc-700 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Storage Add-on Stream -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="font-semibold text-zinc-700">Storage Expansion Volume</span>
                                <span class="text-[10px] font-mono text-zinc-400">(@ ₹10 / GB)</span>
                            </div>
                            <div class="flex items-center gap-2 font-mono">
                                <span class="text-zinc-400 text-[11px]" id="fin-stream-storage-pct">0%</span>
                                <span class="font-bold text-zinc-900" id="fin-stream-storage-mrr">₹0 /mo</span>
                            </div>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-2 overflow-hidden">
                            <div id="fin-stream-storage-bar" class="bg-zinc-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Cash Realization Health Card -->
                    <div class="p-3 bg-zinc-50 border border-zinc-200/70 rounded-lg flex items-center justify-between">
                        <div class="space-y-0.5">
                            <div class="text-[11px] font-bold text-zinc-700">Cash Realization Efficiency</div>
                            <div class="text-[10px] text-zinc-500">Collected cash vs Annualized commitment</div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold font-mono text-emerald-700" id="fin-cash-ratio-text">100% Realized</span>
                            <div class="text-[9.5px] text-zinc-400">Zero default rate</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workspace Financial Ledger Table -->
        <div class="space-y-3">
            <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs flex flex-wrap gap-2.5 items-center justify-between">
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-zinc-900">Tenant Financial & Unit Economics Ledger</h3>
                    <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Itemized revenue contributions, billing cycles, GST components, and invoice settlements.</p>
                </div>
                <div class="flex flex-wrap gap-2 items-center w-full sm:w-auto">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-52 min-w-[160px]">
                        <input type="text" id="fin-search" oninput="filterFinancialWorkspaces()" class="w-full border border-zinc-200 rounded-xl py-1.5 pl-8 pr-3 text-xs bg-white focus:border-zinc-400 focus:outline-none text-zinc-900" placeholder="Search tenant or owner...">
                        <span class="absolute left-2.5 top-2 text-zinc-400">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                    </div>

                    <!-- Tier Filter -->
                    <select id="fin-filter-plan" onchange="filterFinancialWorkspaces()" class="border border-zinc-200 rounded-xl px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer flex-1 sm:flex-none">
                        <option value="">All Plan Tiers</option>
                        <option value="starter">Starter</option>
                        <option value="professional">Professional</option>
                        <option value="scale">Scale</option>
                        <option value="india_only">India Only</option>
                    </select>

                    <!-- Cycle Filter -->
                    <select id="fin-filter-cycle" onchange="filterFinancialWorkspaces()" class="border border-zinc-200 rounded-xl px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer flex-1 sm:flex-none">
                        <option value="">All Cycles</option>
                        <option value="monthly">Monthly</option>
                        <option value="annual">Annual</option>
                    </select>

                    <!-- Status Filter -->
                    <select id="fin-filter-status" onchange="filterFinancialWorkspaces()" class="border border-zinc-200 rounded-xl px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer flex-1 sm:flex-none">
                        <option value="">All Statuses</option>
                        <option value="active">Active & Settled</option>
                        <option value="suspended">Overdue</option>
                    </select>
                </div>
            </div>

            <div class="bg-white border border-zinc-200/85 rounded-xl sm:rounded-2xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                        <thead class="bg-zinc-50/70">
                            <tr>
                                <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Workspace / Tenant</th>
                                <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Plan & Cycle</th>
                                <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">MRR Yield</th>
                                <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">ARR Run-Rate</th>
                                <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Cashflow Collected</th>
                                <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">18% GST</th>
                                <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Next Renewal</th>
                                <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                                <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="fin-table-body" class="divide-y divide-zinc-100">
                            <tr>
                                <td colspan="9" class="px-5 py-8 text-center text-zinc-400">Loading financial records...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: REACTIVATION APPEALS -->
    <div id="tab-super-appeals" class="cora-tab-content space-y-4 <?php echo $active_sub_page === 'super-appeals' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xs flex items-center justify-between gap-2">
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-zinc-900">Suspension Reactivation Appeals</h2>
                <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Review and manage workspace reactivation requests submitted by suspended users.</p>
            </div>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider shrink-0" id="appeals-count-badge">0 appeals</span>
        </div>

        <div class="bg-white border border-zinc-200/85 rounded-xl sm:rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                    <thead class="bg-zinc-50/70">
                        <tr>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Account Email</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Workspace Name</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Contact Phone</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Reason / Message</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Submitted Date</th>
                            <th class="px-4 sm:px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="appeals-table-body" class="divide-y divide-zinc-100">
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-zinc-400">Loading appeals...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: ATTENDANCE & GOVERNANCE -->
    <div id="tab-super-governance" class="cora-tab-content space-y-4 sm:space-y-6 <?php echo $active_sub_page === 'super-governance' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-zinc-900">Cross-Tenant Daily Reports & Automation Controls</h2>
                    <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Manually trigger automated end-of-day attendance reports to Workspace Owners or manage global automation triggers.</p>
                </div>
                <button onclick="dispatchSuperDailyReports()" class="self-start sm:self-auto px-3.5 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer flex items-center gap-2 shrink-0 select-none active:scale-95">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                    Dispatch Daily Reports Now
                </button>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 pt-4 border-t border-zinc-100">
                <div class="p-3.5 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] sm:text-[10px] font-bold uppercase tracking-wider text-zinc-400">Scheduled Dispatch</div>
                    <div class="text-xs sm:text-sm font-bold text-zinc-900">Every Day at 8:00 PM</div>
                    <div class="text-[10.5px] sm:text-xs text-zinc-500">Automated WP Cron active</div>
                </div>
                <div class="p-3.5 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] sm:text-[10px] font-bold uppercase tracking-wider text-zinc-400">SMTP Relay Status</div>
                    <div class="text-xs sm:text-sm font-bold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Hostinger SMTP Active
                    </div>
                    <div class="text-[10.5px] sm:text-xs text-zinc-500">heycora@claraverse.in (SSL 465)</div>
                </div>
                <div class="p-3.5 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] sm:text-[10px] font-bold uppercase tracking-wider text-zinc-400">Geofence Distance Enforcement</div>
                    <div class="text-xs sm:text-sm font-bold text-zinc-900">Strict Haversine Verified</div>
                    <div class="text-[10.5px] sm:text-xs text-zinc-500">Real-time GPS coordinate validation</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 5: BROADCAST ANNOUNCEMENTS -->
    <div id="tab-super-announcements" class="cora-tab-content space-y-4 sm:space-y-6 <?php echo $active_sub_page === 'super-announcements' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xs space-y-4">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-zinc-900">Global Platform Broadcast Console</h2>
                <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Publish top-bar message banners across all tenant workspaces to communicate system updates, maintenance alerts, or notifications.</p>
            </div>
            
            <div class="space-y-4 pt-4 border-t border-zinc-100 max-w-2xl">
                <!-- Announcement text -->
                <div class="space-y-1.5">
                    <label class="text-[10px] sm:text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Broadcast Text Message</label>
                    <textarea id="cora-broadcast-text" rows="3" class="w-full border border-zinc-200 rounded-xl p-3 text-xs bg-white focus:border-zinc-400 focus:outline-none text-zinc-900 font-medium" placeholder="Enter broadcast announcement text..."><?php echo esc_textarea( get_option( 'cora_announcement_text', '' ) ); ?></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <!-- Banner style type -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] sm:text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Visual Alert Theme</label>
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
                    <button onclick="saveGlobalAnnouncement()" class="w-full sm:w-auto px-4 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer select-none active:scale-95">
                        Save and Broadcast Banner
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 6: SYSTEM HEALTH & METRICS -->
    <div id="tab-super-health" class="cora-tab-content space-y-4 sm:space-y-6 <?php echo $active_sub_page === 'super-health' ? '' : 'hidden'; ?>">
        <!-- Live Platform Monitor Card -->
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xs space-y-4 sm:space-y-6">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 flex items-center gap-2">
                        Live Platform Monitor
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    </h2>
                    <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Real-time user presence, activity heatmap, and platform usage feed.</p>
                </div>
                <button onclick="loadLiveMonitor()" class="p-2 text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 rounded-xl transition-colors border border-transparent hover:border-zinc-200 cursor-pointer select-none active:scale-95 shrink-0" title="Refresh Live Data">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>

            <!-- Summary Cards (2x2 on mobile, 4-col on desktop) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
                <div class="p-3 sm:p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-400 mb-1">Users Online</div>
                    <div class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono" id="live-stat-online">0</div>
                    <div class="text-[10px] text-zinc-400 mt-0.5">Currently connected</div>
                </div>
                <div class="p-3 sm:p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-400 mb-1">Active Now</div>
                    <div class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono" id="live-stat-active">0</div>
                    <div class="text-[10px] text-zinc-400 mt-0.5">Interacting &lt; 60s</div>
                </div>
                <div class="p-3 sm:p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-400 mb-1">Actions Today</div>
                    <div class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono" id="live-stat-actions">0</div>
                    <div class="text-[10px] text-zinc-400 mt-0.5">Total logged events</div>
                </div>
                <div class="p-3 sm:p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-400 mb-1">Peak Hour</div>
                    <div class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono" id="live-stat-peak">--</div>
                    <div class="text-[10px] text-zinc-400 mt-0.5">Highest activity window</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Online Users & Heatmap -->
                <div class="space-y-4 sm:space-y-6">
                    <div>
                        <h3 class="text-[10px] uppercase tracking-wider font-bold text-zinc-400 mb-2">Online Users</h3>
                        <div class="border border-zinc-200/85 rounded-xl sm:rounded-2xl overflow-hidden bg-white shadow-xs">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-zinc-50/70 border-b border-zinc-200 text-zinc-400 font-bold text-[10px] uppercase tracking-wider">
                                            <th class="px-3.5 py-2.5">User</th>
                                            <th class="px-3.5 py-2.5">Workspace</th>
                                            <th class="px-3.5 py-2.5">Screen</th>
                                            <th class="px-3.5 py-2.5">Status</th>
                                            <th class="px-3.5 py-2.5">Last Seen</th>
                                        </tr>
                                    </thead>
                                    <tbody id="live-online-users-body" class="divide-y divide-zinc-100">
                                        <tr>
                                            <td colspan="5" class="px-4 py-6 text-center text-zinc-400">No users currently online</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-[10px] uppercase tracking-wider font-bold text-zinc-400 mb-2">24-Hour Activity Heatmap</h3>
                        <div id="live-heatmap-container" class="space-y-1 max-h-[280px] overflow-y-auto pr-2 custom-scrollbar">
                            <!-- Populated via JS -->
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div>
                    <h3 class="text-[10px] uppercase tracking-wider font-bold text-zinc-400 mb-2">Recent Activity Feed</h3>
                    <div id="live-activity-feed" class="max-h-[360px] overflow-y-auto pr-2 custom-scrollbar space-y-1">
                        <div class="text-center text-zinc-400 py-6 text-xs">No recent activity recorded.</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-zinc-900">Platform Specs & Resource Auditing</h2>
                    <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Real-time computation of hosting infrastructure size, dynamic table indexes, and workspace attachments usage.</p>
                </div>
                <button onclick="loadHealthMetrics()" class="self-start sm:self-auto px-3.5 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer flex items-center gap-1.5 shrink-0 select-none active:scale-95">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                    Refresh Health
                </button>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5 sm:gap-4 pt-4 border-t border-zinc-100">
                <!-- DB Size widget -->
                <div class="p-3 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 truncate">MySQL Footprint</div>
                    <div id="metric-db-size" class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono">-- MB</div>
                    <div class="text-[10px] text-zinc-500 truncate">Data + index allocation</div>
                </div>
                <!-- File Storage widget -->
                <div class="p-3 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 truncate">Media Vault</div>
                    <div id="metric-storage-size" class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono">-- MB</div>
                    <div class="text-[10px] text-zinc-500 truncate">Media uploads volume</div>
                </div>
                <!-- Workspaces count -->
                <div class="p-3 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 truncate">Active Workspaces</div>
                    <div id="metric-workspaces" class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono">--</div>
                    <div class="text-[10px] text-zinc-500 truncate">Multi-tenant instances</div>
                </div>
                <!-- Users count -->
                <div class="p-3 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 truncate">Registered Users</div>
                    <div id="metric-users" class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono">--</div>
                    <div class="text-[10px] text-zinc-500 truncate">Platform accounts</div>
                </div>
                <!-- PHP version info -->
                <div class="p-3 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 truncate">PHP Runtime</div>
                    <div id="metric-php-version" class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono">PHP --</div>
                    <div class="text-[10px] text-zinc-500 truncate">Server engine</div>
                </div>
                <!-- WordPress core info -->
                <div class="p-3 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 truncate">WordPress Core</div>
                    <div id="metric-wp-version" class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono">WP --</div>
                    <div class="text-[10px] text-zinc-500 truncate">Core framework</div>
                </div>
                <!-- Server Disk space info -->
                <div class="p-3 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1 col-span-2 md:col-span-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 truncate">Disk Partition</div>
                    <div id="metric-disk-usage" class="text-xs font-bold text-zinc-900 font-mono py-1 truncate">--</div>
                    <div class="text-[10px] text-zinc-500 truncate">Partition capacity</div>
                </div>
                <!-- PHP memory footprint -->
                <div class="p-3 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 truncate">PHP Memory Limit</div>
                    <div id="metric-memory-usage" class="text-xs font-bold text-zinc-900 font-mono py-1 truncate">--</div>
                    <div class="text-[10px] text-zinc-500 truncate">Peak vs max limit</div>
                </div>
                <!-- CPU load and OS -->
                <div class="p-3 sm:p-4 bg-zinc-50/70 border border-zinc-200/70 rounded-xl space-y-1">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 truncate">Server Load & OS</div>
                    <div id="metric-system-software" class="text-xs font-bold text-zinc-900 font-mono py-1 truncate">--</div>
                    <div id="metric-load-avg" class="text-[10px] text-zinc-500 font-semibold truncate">Load: --</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 7: AI MASTER TOKENS & QUOTAS -->
    <div id="tab-super-ai-tokens" class="cora-tab-content space-y-4 sm:space-y-6 <?php echo $active_sub_page === 'super-ai-tokens' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xs space-y-4 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 flex items-center gap-2">
                        AI Master Tokens & Quota Switchboard
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-900 text-white uppercase tracking-wider">Godmode Control</span>
                    </h2>
                    <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Live token burn monitoring, global LLM model routing, and per-workspace quota allocations.</p>
                </div>
                <button onclick="loadAiAnalytics()" class="self-start sm:self-auto px-3.5 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer flex items-center gap-1.5 shrink-0 select-none active:scale-95">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                    Refresh Telemetry
                </button>
            </div>

            <!-- Platform Burn KPIs (2x2 on mobile, 4-col on desktop) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
                <div class="p-3 sm:p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-400 mb-1 truncate">Platform Token Burn</div>
                    <div class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono" id="ai-stat-total-tokens">0</div>
                    <div class="text-[10px] text-zinc-400 mt-0.5 truncate">Aggregated consumption</div>
                </div>
                <div class="p-3 sm:p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-400 mb-1 truncate">Total AI Prompts</div>
                    <div class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono" id="ai-stat-total-requests">0</div>
                    <div class="text-[10px] text-zinc-400 mt-0.5 truncate">Copilot + Voice + Vision</div>
                </div>
                <div class="p-3 sm:p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-400 mb-1 truncate">Active Model Engines</div>
                    <div class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono">4 Providers</div>
                    <div class="text-[10px] text-zinc-400 mt-0.5 truncate">Gemini, Claude, GPT, Groq</div>
                </div>
                <div class="p-3 sm:p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50">
                    <div class="text-[9.5px] uppercase tracking-wider font-semibold text-zinc-400 mb-1 truncate">High-Burn Workspaces</div>
                    <div class="text-lg sm:text-2xl font-bold text-zinc-900 font-mono" id="ai-stat-high-burn">0</div>
                    <div class="text-[10px] text-zinc-400 mt-0.5 truncate">&gt; 80% allowance</div>
                </div>
            </div>

            <!-- Model Distribution Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3 pt-1">
                <div class="p-2.5 sm:p-3 rounded-xl border border-zinc-200/80 bg-white shadow-xs">
                    <div class="flex items-center justify-between text-xs font-semibold text-zinc-700 mb-1 gap-1">
                        <span class="truncate">Gemini 3.5</span>
                        <span class="text-[9px] px-1 py-0.2 rounded bg-emerald-50 text-emerald-700 font-mono shrink-0">320ms</span>
                    </div>
                    <div class="text-xs sm:text-sm font-bold text-zinc-900 font-mono truncate" id="ai-burn-gemini">0 tokens</div>
                    <div class="text-[9.5px] text-zinc-400 mt-0.5 truncate">Vision & Fast Copilot</div>
                </div>
                <div class="p-2.5 sm:p-3 rounded-xl border border-zinc-200/80 bg-white shadow-xs">
                    <div class="flex items-center justify-between text-xs font-semibold text-zinc-700 mb-1 gap-1">
                        <span class="truncate">Claude 3.5</span>
                        <span class="text-[9px] px-1 py-0.2 rounded bg-zinc-100 text-zinc-700 font-mono shrink-0">680ms</span>
                    </div>
                    <div class="text-xs sm:text-sm font-bold text-zinc-900 font-mono truncate" id="ai-burn-claude">0 tokens</div>
                    <div class="text-[9.5px] text-zinc-400 mt-0.5 truncate">Strategy & Invoicing</div>
                </div>
                <div class="p-2.5 sm:p-3 rounded-xl border border-zinc-200/80 bg-white shadow-xs">
                    <div class="flex items-center justify-between text-xs font-semibold text-zinc-700 mb-1 gap-1">
                        <span class="truncate">GPT-4o</span>
                        <span class="text-[9px] px-1 py-0.2 rounded bg-zinc-100 text-zinc-700 font-mono shrink-0">610ms</span>
                    </div>
                    <div class="text-xs sm:text-sm font-bold text-zinc-900 font-mono truncate" id="ai-burn-gpt4o">0 tokens</div>
                    <div class="text-[9.5px] text-zinc-400 mt-0.5 truncate">Multimodal Fallback</div>
                </div>
                <div class="p-2.5 sm:p-3 rounded-xl border border-zinc-200/80 bg-white shadow-xs">
                    <div class="flex items-center justify-between text-xs font-semibold text-zinc-700 mb-1 gap-1">
                        <span class="truncate">Groq 70B</span>
                        <span class="text-[9px] px-1 py-0.2 rounded bg-blue-50 text-blue-700 font-mono shrink-0">210ms</span>
                    </div>
                    <div class="text-xs sm:text-sm font-bold text-zinc-900 font-mono truncate" id="ai-burn-groq">0 tokens</div>
                    <div class="text-[9.5px] text-zinc-400 mt-0.5 truncate">Duplex Voice AI</div>
                </div>
            </div>

            <!-- Quota Allocation Table -->
            <div class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Per-Workspace AI Token Quota Allocations</h3>
                    <input type="text" id="ai-quota-search" oninput="filterAiQuotaTable()" placeholder="Search workspace..." class="border border-zinc-200 rounded-xl px-3 py-1.5 text-xs w-full sm:w-56 focus:outline-none focus:border-zinc-400">
                </div>
                <div class="border border-zinc-200/85 rounded-xl sm:rounded-2xl overflow-hidden bg-white shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-zinc-50/70 border-b border-zinc-200 text-zinc-400 font-bold text-[10px] uppercase tracking-wider">
                                    <th class="px-4 py-3">Workspace</th>
                                    <th class="px-4 py-3">Plan</th>
                                    <th class="px-4 py-3">Monthly Burn / Quota</th>
                                    <th class="px-4 py-3">Burn %</th>
                                    <th class="px-4 py-3">Godmode Override</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ai-quota-table-body" class="divide-y divide-zinc-100">
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-zinc-400">Loading AI token telemetry...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 8: FEATURE FLAGS MATRIX -->
    <div id="tab-super-feature-flags" class="cora-tab-content space-y-4 sm:space-y-6 <?php echo $active_sub_page === 'super-feature-flags' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xs space-y-4 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 flex items-center gap-2">
                        Tenant Feature Flags & Capability Matrix
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-900 text-white uppercase tracking-wider">Live Control</span>
                    </h2>
                    <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Dynamically enable or disable core platform modules per workspace regardless of subscription tier.</p>
                </div>
                <button onclick="loadFeatureFlags()" class="self-start sm:self-auto px-3.5 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer flex items-center gap-1.5 shrink-0 select-none active:scale-95">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                    Refresh Matrix
                </button>
            </div>

            <div class="border border-zinc-200/85 rounded-xl sm:rounded-2xl overflow-hidden bg-white shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-zinc-50/70 border-b border-zinc-200 text-zinc-600 font-bold text-[10px] uppercase tracking-wider">
                                <th class="px-4 py-3 min-w-[180px]">Workspace</th>
                                <th class="px-3 py-3 text-center" title="Canvas Dual Builder">Canvas</th>
                                <th class="px-3 py-3 text-center" title="Duplex Voice AI">Voice AI</th>
                                <th class="px-3 py-3 text-center" title="Vision OCR Roster Ingestion">OCR Roster</th>
                                <th class="px-3 py-3 text-center" title="WhatsApp Cloud API">WhatsApp</th>
                                <th class="px-3 py-3 text-center" title="Document Vault & E-Sign">Vault & E-Sign</th>
                                <th class="px-3 py-3 text-center" title="Content AI & GEO Suite">SEO / Content</th>
                                <th class="px-3 py-3 text-center" title="Model Context Protocol">MCP API</th>
                                <th class="px-3 py-3 text-center" title="GST Tax Engine & Ledger">GST Tax</th>
                                <th class="px-4 py-3 text-right">Quick Presets</th>
                            </tr>
                        </thead>
                        <tbody id="feature-flags-table-body" class="divide-y divide-zinc-100">
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-zinc-400">Loading feature flag matrix...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 9: EMERGENCY COMMAND CENTER -->
    <div id="tab-super-emergency" class="cora-tab-content space-y-4 sm:space-y-6 <?php echo $active_sub_page === 'super-emergency' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-red-200 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xs space-y-4 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-red-700 flex items-center gap-2">
                        Global Emergency Command Center
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-600 text-white uppercase tracking-wider">Level 0 Overrides</span>
                    </h2>
                    <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">High-impact maintenance switches, cache nukes, emergency read-only freeze, and global session terminations.</p>
                </div>
                <div class="self-start sm:self-auto flex items-center gap-2 px-3 py-1.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-xs font-bold" id="emergency-status-badge">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Platform Operational
                </div>
            </div>

            <!-- Maintenance Mode Configuration Box -->
            <div class="p-4 sm:p-5 rounded-xl border border-zinc-200 bg-zinc-50/60 space-y-3 sm:space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-zinc-900">Platform Maintenance Mode</h3>
                        <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Displays a global top banner to tenant users and restricts mutations while keeping super admins unlocked.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" id="emergency-maintenance-toggle" onchange="togglePlatformMaintenance(this.checked)" class="sr-only peer">
                        <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] sm:text-[11px] font-bold text-zinc-700 uppercase tracking-wider">Broadcast Maintenance Message</label>
                    <input type="text" id="emergency-maintenance-msg" value="Platform is undergoing brief scheduled maintenance. All services will resume shortly." class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-xs bg-white focus:outline-none focus:border-zinc-400">
                </div>
            </div>

            <!-- High Impact Quick Actions -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                <!-- Action 1: Cache Nuke -->
                <div class="p-4 rounded-xl border border-zinc-200/80 bg-white shadow-xs space-y-2.5 sm:space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-zinc-100 text-zinc-800 shrink-0">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                        </span>
                        <div>
                            <h4 class="text-xs font-bold text-zinc-900">Global Cache & OPcache Nuke</h4>
                            <p class="text-[10px] text-zinc-400">Purge micro-caches & OPcache</p>
                        </div>
                    </div>
                    <p class="text-[11px] sm:text-xs text-zinc-600 leading-relaxed">Instantly invalidates all in-memory micro-caches, WP object caches, and PHP OPcache bytecode caches.</p>
                    <button onclick="triggerEmergencyAction('nuke_all_caches')" class="w-full py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 transition-colors cursor-pointer select-none active:scale-95">
                        Execute Cache Nuke
                    </button>
                </div>

                <!-- Action 2: Session Invalidation -->
                <div class="p-4 rounded-xl border border-zinc-200/80 bg-white shadow-xs space-y-2.5 sm:space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-amber-50 text-amber-800 shrink-0">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                        </span>
                        <div>
                            <h4 class="text-xs font-bold text-zinc-900">Revoke Non-Admin Sessions</h4>
                            <p class="text-[10px] text-zinc-400">Force immediate re-login</p>
                        </div>
                    </div>
                    <p class="text-[11px] sm:text-xs text-zinc-600 leading-relaxed">Invalidates active login sessions across all tenants. Super Admin sessions remain active.</p>
                    <button onclick="triggerEmergencyAction('revoke_all_sessions')" class="w-full py-2 bg-amber-600 text-white rounded-xl text-xs font-bold hover:bg-amber-700 transition-colors cursor-pointer select-none active:scale-95">
                        Revoke All Sessions
                    </button>
                </div>

                <!-- Action 3: Read-Only Freeze -->
                <div class="p-4 rounded-xl border border-red-200 bg-red-50/30 space-y-2.5 sm:space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-red-100 text-red-800 shrink-0">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </span>
                        <div>
                            <h4 class="text-xs font-bold text-red-900">Emergency Read-Only Freeze</h4>
                            <p class="text-[10px] text-red-700">Block all data mutations</p>
                        </div>
                    </div>
                    <p class="text-[11px] sm:text-xs text-zinc-600 leading-relaxed">Locks the database in read-only mode to prevent write corruptions during migrations or outages.</p>
                    <button onclick="triggerEmergencyAction('emergency_freeze')" id="btn-emergency-freeze" class="w-full py-2 bg-red-700 text-white rounded-xl text-xs font-bold hover:bg-red-800 transition-colors cursor-pointer select-none active:scale-95">
                        Activate Read-Only Freeze
                    </button>
                </div>
            </div>

            <!-- Emergency Terminal Log -->
            <div class="space-y-1.5">
                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Emergency Console Execution Output</div>
                <div id="emergency-console-log" class="p-3.5 sm:p-4 rounded-xl bg-zinc-950 text-emerald-400 font-mono text-[11px] h-28 sm:h-32 overflow-y-auto space-y-1">
                    <div>[<?php echo date('Y-m-d H:i:s'); ?>] Platform Super Admin Console initialized. All emergency vectors standing by.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 10: FORENSICS & AUDIT STREAM -->
    <div id="tab-super-audit" class="cora-tab-content space-y-4 sm:space-y-6 <?php echo $active_sub_page === 'super-audit' ? '' : 'hidden'; ?>">
        <div class="bg-white border border-zinc-200/80 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xs space-y-4 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 flex items-center gap-2">
                        Global Forensics & Audit Trail Inspector
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-900 text-white uppercase tracking-wider">Immutable Feed</span>
                    </h2>
                    <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5">Centralized, tamper-evident activity stream across authentication, data mutations, financial transactions, and MCP execution.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="exportAuditLogsCsv()" class="px-3 py-1.5 border border-zinc-200 text-zinc-700 rounded-xl text-xs font-bold hover:bg-zinc-50 transition-colors shadow-xs cursor-pointer flex items-center gap-1.5 select-none active:scale-95">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Export CSV
                    </button>
                    <button onclick="loadAuditLogs()" class="px-3.5 py-1.5 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer flex items-center gap-1.5 select-none active:scale-95">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                        Refresh Logs
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2 sm:gap-3 items-center">
                <input type="text" id="audit-filter-keyword" oninput="filterAuditLogsTable()" placeholder="Search action or details..." class="border border-zinc-200 rounded-xl px-3 py-1.5 text-xs flex-1 sm:w-64 focus:outline-none focus:border-zinc-400">
                <select id="audit-filter-action" onchange="filterAuditLogsTable()" class="border border-zinc-200 rounded-xl px-3 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer flex-1 sm:flex-none">
                    <option value="">All Action Types</option>
                    <option value="AUTH">Authentication / Sessions</option>
                    <option value="AI">AI & Quotas</option>
                    <option value="SECURITY">Security & Caches</option>
                    <option value="FEATURE">Feature Flags</option>
                    <option value="GST">GST & E-Sign</option>
                    <option value="MCP">MCP Gateway</option>
                </select>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider ml-auto" id="audit-log-count-badge">0 events</span>
            </div>

            <!-- Audit Table -->
            <div class="border border-zinc-200 rounded-xl overflow-hidden bg-white">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 font-semibold">
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="px-4 py-3">Workspace</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Event Details</th>
                            <th class="px-4 py-3 text-right">IP Address</th>
                        </tr>
                    </thead>
                    <tbody id="audit-logs-table-body" class="divide-y divide-zinc-100">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-400">Loading forensics logs...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Expose dynamic labels directly to JS
const coraRoleLabels = <?php echo json_encode( $roles_list ); ?>;
window.rawWorkspaces = [];
window.rawUsers = [];
window.rawAppeals = [];
window.activeAppealId = null;

jQuery(document).ready(function($) {
    let rawWorkspaces = window.rawWorkspaces;
    let rawUsers = window.rawUsers;
    let rawAppeals = window.rawAppeals;
    let activeAppealId = null;

    // Helper: Escape HTML strings for safety
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/\\'/g, "'")
            .replace(/\\"/g, '"')
            .replace(/\\\\/g, "\\")
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

    let currentFilterCategory = 'all';

    // Filter pill selector helper
    window.setWorkspaceFilterCategory = function(cat) {
        currentFilterCategory = cat;
        const btnMap = {
            'all': '#btn-filter-all',
            'active': '#btn-filter-active',
            'real_estate': '#btn-filter-re',
            'photography_studio': '#btn-filter-studio',
            'suspended': '#btn-filter-suspended'
        };

        Object.values(btnMap).forEach(selector => {
            $(selector).removeClass('bg-white text-zinc-950 shadow-xs').addClass('text-zinc-600');
        });

        if (btnMap[cat]) {
            $(btnMap[cat]).removeClass('text-zinc-600').addClass('bg-white text-zinc-950 shadow-xs');
        }

        renderWorkspaces();
    };

    // Helper: Number / Token formatter (e.g. 53,400 -> 53.4k)
    function formatTokens(num) {
        num = Number(num) || 0;
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'k';
        return String(num);
    }

    // Helper: Update live summary metrics & pill badges
    function updateTelemetrySummary(summary, workspaces) {
        workspaces = workspaces || [];
        const total = summary?.total_workspaces ?? workspaces.length;
        const active = summary?.active_workspaces ?? workspaces.filter(w => w.status === 'active').length;
        const suspended = summary?.suspended_workspaces ?? workspaces.filter(w => w.status === 'suspended').length;
        const liveNow = summary?.live_active_now ?? workspaces.filter(w => w.is_live).length;
        const tokens = summary?.total_platform_tokens ?? workspaces.reduce((acc, w) => acc + (Number(w.used_tokens) || 0), 0);
        const users = summary?.total_platform_users ?? workspaces.reduce((acc, w) => acc + (Number(w.current_users_count) || 1), 0);
        const reCount = workspaces.filter(w => (w.industry || 'real_estate') === 'real_estate').length;
        const studioCount = workspaces.filter(w => w.industry === 'photography_studio' || w.industry === 'photography').length;

        $('#stat-active-workspaces').text(active);
        $('#stat-total-workspaces').text(total);
        $('#stat-live-pulse-badge').text(liveNow + ' Online Now');
        $('#stat-total-tokens').text(formatTokens(tokens));
        $('#stat-total-users').text(users);
        $('#stat-suspended-count').text(suspended);

        // Update token bar
        const burnBarWidth = Math.min(100, Math.max(8, Math.round((tokens / 5000000) * 100)));
        $('#stat-token-burn-bar').css('width', burnBarWidth + '%');

        // Pill counts
        $('#pill-count-all').text(total);
        $('#pill-count-active').text(active);
        $('#pill-count-re').text(reCount);
        $('#pill-count-studio').text(studioCount);
        $('#pill-count-suspended').text(suspended);

        const pendingAppeals = rawAppeals.filter(a => a.status === 'pending').length;
        $('#stat-appeals-summary').text(pendingAppeals);
        if (pendingAppeals > 0) {
            $('#stat-security-status-badge').text('Action Required').removeClass('bg-zinc-100 text-zinc-700').addClass('bg-amber-100 text-amber-800');
        } else {
            $('#stat-security-status-badge').text('Nominal').removeClass('bg-amber-100 text-amber-800').addClass('bg-zinc-100 text-zinc-700');
        }

        if (typeof window.renderFinancialsDashboard === 'function') {
            window.renderFinancialsDashboard(summary, workspaces);
        }
    }

    // Load dynamic data on initiation
    function loadPlatformData() {
        // Expose globally
        window.loadPlatformData = loadPlatformData;

        // Load Workspaces list with rich telemetry
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_workspaces',
            security: coraREData.ajaxNonce,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                rawWorkspaces = res.data.workspaces || [];
                window.rawWorkspaces = rawWorkspaces;
                updateTelemetrySummary(res.data.summary, rawWorkspaces);
                renderWorkspaces();
            } else {
                const errorMsg = (res.data && res.data.message) ? res.data.message : (res.data || 'Failed to load platform workspaces.');
                $('#workspaces-grid-container').html(`<div class="col-span-full bg-red-50/30 border border-red-200 rounded-2xl p-8 text-center text-red-600 font-semibold">${escapeHtml(errorMsg)}</div>`);
                $('#workspaces-table-body').html(`<tr><td colspan="6" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 border-t border-zinc-100">${escapeHtml(errorMsg)}</td></tr>`);
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
            }
        }).fail(function() {
            $('#workspaces-grid-container').html('<div class="col-span-full bg-red-50/30 border border-red-200 rounded-2xl p-8 text-center text-red-600 font-semibold">Connection error: Could not retrieve workspaces.</div>');
            $('#workspaces-table-body').html('<tr><td colspan="6" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 border-t border-zinc-100">Connection error: Could not retrieve workspaces.</td></tr>');
        });

        // Load Users list
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_users',
            security: coraREData.ajaxNonce,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                rawUsers = res.data.users || [];
                window.rawUsers = rawUsers;
                renderUsers();
            } else {
                const errorMsg = (res.data && res.data.message) ? res.data.message : 'Failed to load platform users.';
                $('#users-table-body').html(`<tr><td colspan="6" class="px-5 py-6 text-center text-red-600 font-semibold bg-red-50/20 border-t border-zinc-100">${escapeHtml(errorMsg)}</td></tr>`);
            }
        });

        // Load Appeals list
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_appeals',
            security: coraREData.ajaxNonce,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                rawAppeals = res.data.appeals || [];
                window.rawAppeals = rawAppeals;
                renderAppeals();
                if (rawWorkspaces.length > 0) {
                    renderWorkspaces();
                }
            }
        });
    }
    window.loadPlatformData = loadPlatformData;

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
        $('#stat-appeals-summary').text(pendingAppeals.length);

        if (rawAppeals.length === 0) {
            $('#appeals-table-body').html('<tr><td colspan="7" class="px-5 py-8 text-center text-zinc-400 bg-zinc-50/20">No reactivation appeals submitted yet.</td></tr>');
            return;
        }

        let html = '';
        rawAppeals.forEach(a => {
            let statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-amber-50 text-amber-700">Pending</span>';
            if (a.status === 'approved') {
                statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-emerald-50 text-emerald-700">Approved</span>';
            } else if (a.status === 'declined') {
                statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 text-zinc-600">Declined</span>';
            }

            html += `
                <tr class="hover:bg-zinc-50/20 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-zinc-900">${escapeHtml(a.email)}</td>
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

    let workspaceViewMode = 'grid'; // Default: Card Grid

    window.setWorkspaceViewMode = function(mode) {
        workspaceViewMode = mode;
        if (mode === 'grid') {
            $('#workspaces-grid-container').removeClass('hidden');
            $('#workspaces-table-container').addClass('hidden');
            $('#ws-view-grid-btn').addClass('bg-white text-zinc-950 shadow-xs').removeClass('text-zinc-500 hover:text-zinc-900');
            $('#ws-view-table-btn').removeClass('bg-white text-zinc-950 shadow-xs').addClass('text-zinc-500 hover:text-zinc-900');
        } else {
            $('#workspaces-grid-container').addClass('hidden');
            $('#workspaces-table-container').removeClass('hidden');
            $('#ws-view-table-btn').addClass('bg-white text-zinc-950 shadow-xs').removeClass('text-zinc-500 hover:text-zinc-900');
            $('#ws-view-grid-btn').removeClass('bg-white text-zinc-950 shadow-xs').addClass('text-zinc-500 hover:text-zinc-900');
        }
    };

    // Render Workspaces Tab Content with Professional Card Grid & Live Telemetry
    window.renderWorkspaces = function() {
        const query = ($('#workspace-search').val() || '').toLowerCase().trim();
        const sortBy = $('#workspace-sort-by').val() || 'recent';

        let filtered = rawWorkspaces.filter(ws => {
            const cleanWsName = (ws.name || '').replace(/\\'/g, "'").replace(/\\"/g, '"');
            const cleanSlug = (ws.slug || '').replace(/\\'/g, "'");
            const cleanOwner = (ws.owner_name || '').replace(/\\'/g, "'").replace(/\\"/g, '"');
            const cleanEmail = (ws.owner_email || '').replace(/\\'/g, "'");

            const nameMatch = cleanWsName.toLowerCase().includes(query);
            const slugMatch = cleanSlug.toLowerCase().includes(query);
            const emailMatch = cleanEmail.toLowerCase().includes(query);
            const ownerMatch = cleanOwner.toLowerCase().includes(query);
            const matchesQuery = !query || nameMatch || slugMatch || emailMatch || ownerMatch;

            const wsInd = ws.industry === 'photography' ? 'photography_studio' : (ws.industry || 'real_estate');
            
            let matchesCategory = true;
            if (currentFilterCategory === 'active') {
                matchesCategory = (ws.status === 'active');
            } else if (currentFilterCategory === 'suspended') {
                matchesCategory = (ws.status === 'suspended');
            } else if (currentFilterCategory === 'real_estate') {
                matchesCategory = (wsInd === 'real_estate');
            } else if (currentFilterCategory === 'photography_studio') {
                matchesCategory = (wsInd === 'photography_studio');
            }

            return matchesQuery && matchesCategory;
        });

        // Sorting
        filtered.sort((a, b) => {
            if (sortBy === 'tokens') {
                return (Number(b.used_tokens) || 0) - (Number(a.used_tokens) || 0);
            } else if (sortBy === 'users') {
                return (Number(b.current_users_count) || 0) - (Number(a.current_users_count) || 0);
            } else if (sortBy === 'name') {
                return (a.name || '').localeCompare(b.name || '');
            } else {
                // Default: Recently Active / ID desc
                return (Number(b.id) || 0) - (Number(a.id) || 0);
            }
        });

        $('#workspace-count-badge').text(`${filtered.length} workspace${filtered.length === 1 ? '' : 's'}`);

        if (filtered.length === 0) {
            $('#workspaces-grid-container').html('<div class="col-span-full bg-white border border-zinc-200/80 rounded-2xl p-12 text-center text-zinc-400 font-medium">No workspaces matching filters found.</div>');
            $('#workspaces-table-body').html('<tr><td colspan="6" class="px-5 py-8 text-center text-zinc-400 bg-zinc-50/20">No workspaces matching filters found.</td></tr>');
            return;
        }

        let gridHtml = '';
        let tableHtml = '';

        filtered.forEach(ws => {
            const cleanWsName = (ws.name || 'Workspace #' + ws.id).replace(/\\'/g, "'").replace(/\\"/g, '"');
            const cleanSlug = (ws.slug || '').replace(/\\'/g, "'");
            const cleanOwner = (ws.owner_name || 'Studio Owner').replace(/\\'/g, "'").replace(/\\"/g, '"');
            const cleanEmail = (ws.owner_email || '—').replace(/\\'/g, "'");

            const pKey = (ws.plan || 'starter').toLowerCase();
            let planBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md uppercase tracking-wider bg-zinc-100 text-zinc-700 border border-zinc-200/70 font-mono">Starter</span>';
            if (pKey === 'enterprise') {
                planBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md uppercase tracking-wider bg-zinc-950 text-white font-mono shadow-2xs">Enterprise</span>';
            } else if (pKey === 'pro' || pKey === 'studio_pro' || pKey === 'beta') {
                planBadge = '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md uppercase tracking-wider bg-zinc-900 text-zinc-100 font-mono shadow-2xs">Pro Studio</span>';
            }
            
            let statusBadge = ws.status === 'active'
                ? '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[9.5px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 select-none"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>'
                : '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[9.5px] font-bold rounded-full bg-red-50 text-red-700 border border-red-200/60 select-none"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspended</span>';

            const pendingAppeal = rawAppeals.find(a => a.status === 'pending' && (
                (cleanEmail && a.email && a.email.toLowerCase() === cleanEmail.toLowerCase()) ||
                (cleanWsName && a.workspace_name && a.workspace_name.toLowerCase() === cleanWsName.toLowerCase()) ||
                (cleanSlug && a.workspace_name && a.workspace_name.toLowerCase() === cleanSlug.toLowerCase())
            ));

            if (ws.status === 'suspended' && pendingAppeal) {
                statusBadge += `<button onclick="openAppealReviewDrawer('${pendingAppeal.id}')" class="ml-1.5 px-2 py-0.5 text-[9px] font-bold rounded-md bg-amber-100 text-amber-800 border border-amber-200 hover:bg-amber-200 cursor-pointer select-none inline-flex items-center gap-1"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> Appeal</button>`;
            }

            const toggleLabel = ws.status === 'active' ? 'Suspend' : 'Activate';
            const toggleClass = ws.status === 'active'
                ? 'text-red-600 hover:text-red-700 border-zinc-200 hover:bg-red-50'
                : 'text-emerald-600 hover:text-emerald-700 border-zinc-200 hover:bg-emerald-50';

            const currInd = ws.industry === 'photography' ? 'photography_studio' : (ws.industry || 'real_estate');
            const indLabel = currInd === 'photography_studio' ? 'Studio' : 'Real Estate';
            const indIcon = currInd === 'photography_studio'
                ? `<svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>`
                : `<svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>`;

            // Live Activity Pulse Badge
            const pulseBadge = ws.is_live
                ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Online</span>`
                : `<span class="text-zinc-400 text-[10px] font-mono">Active ${ws.last_active_human || 'recently'}</span>`;

            // Owner Initials
            const ownerInitial = (cleanOwner || 'W').charAt(0).toUpperCase();

            // AI Runs & Credits Telemetry (1 Run = 500 Tokens)
            const usedTokens = Number(ws.used_tokens) || 0;
            const usedRuns = Number(ws.ai_runs_used) || Math.round(usedTokens / 500);
            const baseRuns = Number(ws.ai_runs_base) || (ws.base_quota ? Math.round(ws.base_quota / 500) : 1000);
            const bonusRuns = Number(ws.ai_runs_bonus) || (ws.bonus_tokens ? Math.round(ws.bonus_tokens / 500) : 0);
            const recRuns = Number(ws.recurring_ai_runs) || 0;
            const totalRunsQuota = baseRuns + bonusRuns;
            const quotaRunsDisplay = ws.is_unlimited ? '∞ Unlimited' : totalRunsQuota.toLocaleString() + ' Runs';
            const burnPct = ws.is_unlimited ? 0 : Math.min(100, Math.max(2, Math.round((usedRuns / Math.max(1, totalRunsQuota)) * 100)));
            const storageLimitFormatted = (ws.storage_limit_mb >= 1024 ? (ws.storage_limit_mb/1024).toFixed(0) + ' GB' : (ws.storage_limit_mb || 1024) + ' MB');

            // Dashboard direct jump URL
            const dashUrl = ws.dashboard_url || `${coraREData.siteUrl}/workspace/dashboard?industry=${currInd}&agency_id=${ws.id}`;

            // Revenue metrics for card
            const wsMrrFormatted = '₹' + (Number(ws.mrr) || 0).toLocaleString('en-IN');
            const wsArrFormatted = '₹' + (Number(ws.arr) || 0).toLocaleString('en-IN');

            // --- 1. CARD GRID ITEM ---
            gridHtml += `
                <div class="bg-white border border-zinc-200/80 hover:border-zinc-350 rounded-2xl p-4 sm:p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group relative">
                    <div class="space-y-3.5">
                        <!-- Top Header: Industry, Plan & Status -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 flex-nowrap shrink-0">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-zinc-100 text-zinc-700 border border-zinc-200/60 shrink-0">
                                    ${indIcon} ${indLabel}
                                </span>
                                ${planBadge}
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                ${pulseBadge}
                                ${statusBadge}
                            </div>
                        </div>

                        <!-- Workspace Title & URL -->
                        <div class="space-y-0.5">
                            <a href="${dashUrl}" target="_blank" class="font-bold text-sm sm:text-base text-zinc-950 hover:text-zinc-700 transition-colors inline-flex items-center gap-1.5 group-hover:underline max-w-full">
                                <span class="truncate">${escapeHtml(cleanWsName)}</span>
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-950 transition-colors shrink-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                            <div class="text-[11px] font-mono text-zinc-400 truncate">
                                app.heycora.in/${escapeHtml(cleanSlug)}
                            </div>
                        </div>

                        <!-- Workspace Owner & Team Profile (Seamless integration, no inner boxed outline) -->
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-zinc-100">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-full bg-zinc-100 text-zinc-800 text-[11px] font-bold flex items-center justify-center shrink-0 border border-zinc-200 select-none">
                                    ${ownerInitial}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-zinc-900 text-xs truncate">${escapeHtml(cleanOwner)}</div>
                                    <div class="text-zinc-400 font-mono text-[10px] truncate">${escapeHtml(cleanEmail)}</div>
                                </div>
                            </div>
                            <div class="text-[10px] font-medium text-zinc-600 bg-zinc-50 px-2 py-0.5 rounded-md border border-zinc-200/70 shrink-0 flex items-center gap-1 select-none">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                <span>${ws.current_users_count || 1} team</span>
                            </div>
                        </div>

                        <!-- Telemetry & Quota Gauges -->
                        <div class="space-y-2.5 pt-2 border-t border-zinc-100">
                            <!-- AI Runs Quota Meter -->
                            <div class="space-y-1 cursor-pointer group/burn" onclick="openManageWorkspaceDrawer(${ws.id}, 'quota')" title="Click to adjust AI Runs & top-up credits">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-zinc-500 font-medium flex items-center gap-1 group-hover/burn:text-zinc-950 transition-colors">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                        AI Runs Quota
                                    </span>
                                    <div class="font-mono text-[10.5px]">
                                        <span class="font-bold text-zinc-900">${usedRuns.toLocaleString()}</span>
                                        <span class="text-zinc-400">/ ${quotaRunsDisplay}</span>
                                        ${bonusRuns > 0 ? `<span class="text-emerald-700 font-sans font-bold text-[9px] bg-emerald-50 px-1 py-0.2 rounded border border-emerald-200/60 ml-0.5">+${bonusRuns.toLocaleString()}</span>` : ''}
                                    </div>
                                </div>
                                <div class="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                                    <div class="bg-zinc-950 h-1.5 rounded-full transition-all duration-500" style="width: ${burnPct}%"></div>
                                </div>
                            </div>

                            <!-- Storage & Financial Run-Rate Row -->
                            <div class="flex items-center justify-between text-[11px] pt-0.5">
                                <div class="flex items-center gap-1 text-zinc-500 cursor-pointer group/stor" onclick="openManageWorkspaceDrawer(${ws.id}, 'storage')" title="Click to boost storage capacity">
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                                    <span class="font-mono text-[10.5px] text-zinc-700">
                                        <span class="font-bold text-zinc-900">${(ws.current_storage_mb || 0).toFixed(1)} MB</span>
                                        <span class="text-zinc-400">/ ${storageLimitFormatted}</span>
                                    </span>
                                </div>
                                <div class="font-mono text-[10.5px] font-bold text-zinc-900" title="Revenue Run-Rate">
                                    ${wsMrrFormatted}<span class="text-zinc-400 font-normal">/mo</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Action Controls -->
                    <div class="pt-3 mt-3.5 flex items-center justify-between gap-2 border-t border-zinc-100">
                        <button onclick="toggleWorkspaceStatus(${ws.id}, '${ws.status === 'active' ? 'suspended' : 'active'}')" class="px-2.5 py-1.5 border border-zinc-200 rounded-lg text-[10px] font-bold bg-white hover:bg-zinc-50 cursor-pointer shadow-2xs active:scale-95 transition-all ${toggleClass}">
                            ${toggleLabel}
                        </button>

                        <div class="flex items-center gap-1.5">
                            <button onclick="openManageWorkspaceDrawer(${ws.id}, 'quota')" title="Top Up AI Runs & Storage Add-Ons" class="px-2.5 py-1.5 border border-zinc-200 rounded-lg text-[10.5px] font-bold text-zinc-800 bg-zinc-50 hover:bg-zinc-950 hover:text-white cursor-pointer shadow-2xs active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                Top-Up
                            </button>

                            <button onclick="launchWorkspace(${ws.id})" title="Launch / Jump to Workspace" class="px-3 py-1.5 bg-zinc-950 text-white rounded-lg text-[10.5px] font-bold hover:bg-zinc-800 cursor-pointer shadow-xs active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                Launch
                            </button>

                            <button onclick="openManageWorkspaceDrawer(${ws.id}, 'settings')" title="Manage Plan & Quotas" class="p-1.5 border border-zinc-200 rounded-lg text-zinc-600 bg-white hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer shadow-2xs active:scale-95 transition-all" aria-label="Settings">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l-.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // --- 2. TABLE ROW ---
            tableHtml += `
                <tr class="hover:bg-zinc-50/40 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="space-y-1">
                            <a href="${dashUrl}" target="_blank" class="font-bold text-zinc-950 hover:underline inline-flex items-center gap-1.5 group">
                                <span>${escapeHtml(cleanWsName)}</span>
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-950 transition-colors"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[10px] text-zinc-600 bg-zinc-100 px-1.5 py-0.5 rounded border border-zinc-200/60">${escapeHtml(cleanSlug)}</span>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-zinc-50 text-zinc-600 border border-zinc-200/60">
                                    ${indIcon} ${indLabel}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-700 text-[11px] font-bold flex items-center justify-center shrink-0 select-none">
                                ${ownerInitial}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-zinc-900 text-xs truncate">${escapeHtml(cleanOwner)}</div>
                                <div class="text-zinc-500 font-mono text-[10.5px] truncate">${escapeHtml(cleanEmail)}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="space-y-1">
                            <div>${pulseBadge}</div>
                            <div class="text-[10px] text-zinc-500 font-medium flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                <span>${ws.current_users_count || 1} team member${(ws.current_users_count || 1) === 1 ? '' : 's'}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="space-y-1.5 cursor-pointer group" onclick="openManageWorkspaceDrawer(${ws.id}, 'quota')" title="Click to adjust quota & storage limits">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold text-zinc-900">${usedRuns.toLocaleString()} Runs</span>
                                <span class="text-zinc-400 text-[10px]">/ ${quotaRunsDisplay}</span>
                            </div>
                            <div class="w-full max-w-32 h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                                <div class="bg-zinc-950 h-1.5 rounded-full transition-all" style="width: ${burnPct}%"></div>
                            </div>
                            <div class="flex items-center justify-between gap-1 text-[10px] text-zinc-500 font-mono">
                                <span class="inline-flex items-center gap-1"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg> ${(ws.current_storage_mb || 0).toFixed(1)} MB / ${storageLimitFormatted}</span>
                                ${bonusRuns > 0 ? `<span class="text-emerald-700 font-bold font-sans text-[9px] bg-emerald-50 px-1 py-0.2 rounded border border-emerald-200/60">+${bonusRuns.toLocaleString()}</span>` : ''}
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-1.5">
                                ${planBadge}
                                ${statusBadge}
                            </div>
                            <button onclick="toggleWorkspaceStatus(${ws.id}, '${ws.status === 'active' ? 'suspended' : 'active'}')" class="px-2 py-0.5 border rounded text-[9.5px] font-bold bg-white hover:bg-zinc-50 cursor-pointer shadow-xs active:scale-95 transition-all ${toggleClass}">
                                ${toggleLabel} Workspace
                            </button>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5 flex-wrap">
                            <button onclick="openManageWorkspaceDrawer(${ws.id}, 'quota')" title="Top Up AI Runs & Storage" class="px-2 py-1.5 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-800 bg-zinc-50 hover:bg-zinc-950 hover:text-white cursor-pointer shadow-xs active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                Top-Up
                            </button>
                            ${ws.owner_user_id ? `
                            <button onclick="impersonateUser(${ws.owner_user_id})" title="Launch Shadow Session" class="px-2.5 py-1.5 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-800 bg-white hover:bg-zinc-950 hover:text-white cursor-pointer shadow-xs active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M15 3h6v6"></path><path d="M10 14L21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                Launch
                            </button>
                            ` : `
                            <a href="${dashUrl}" target="_blank" class="px-2.5 py-1.5 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-800 bg-white hover:bg-zinc-950 hover:text-white cursor-pointer shadow-xs active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                Open
                            </a>
                            `}
                            <button onclick="openManageWorkspaceDrawer(${ws.id})" title="Workspace Settings" class="px-2.5 py-1.5 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-100 cursor-pointer shadow-xs active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l-.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                Settings
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#workspaces-grid-container').html(gridHtml);
        $('#workspaces-table-body').html(tableHtml);
    };

    // =========================================================================
    // FINANCIALS & REVENUE ENGINE DASHBOARD RENDERER
    // =========================================================================
    window.lastFinancialSummary = null;

    window.renderFinancialsDashboard = function(summary, workspaces) {
        summary = summary || window.lastFinancialSummary || {};
        workspaces = workspaces || window.rawWorkspaces || [];
        window.lastFinancialSummary = summary;

        const totalMRR = Number(summary?.platform_mrr) || 0;
        const baseMRR = Number(summary?.platform_base_mrr) || 0;
        const totalARR = Number(summary?.platform_arr) || (totalMRR * 12);
        const totalCash = Number(summary?.platform_cash) || 0;
        const totalGST = Number(summary?.platform_gst) || 0;
        const arpu = Number(summary?.arpu) || (workspaces.length ? Math.round(totalMRR / Math.max(1, workspaces.length)) : 0);

        // Update Top Deck
        $('#fin-stat-mrr').text('₹' + totalMRR.toLocaleString('en-IN'));
        $('#fin-stat-base-mrr').text('₹' + baseMRR.toLocaleString('en-IN') + ' base');
        $('#fin-stat-arr').text('₹' + totalARR.toLocaleString('en-IN'));
        $('#fin-stat-cash').text('₹' + totalCash.toLocaleString('en-IN'));
        $('#fin-stat-arpu').text('₹' + arpu.toLocaleString('en-IN'));
        $('#fin-stat-gst').text('₹' + totalGST.toLocaleString('en-IN'));

        // Plan Tiers Distribution
        const planCounts = summary?.plan_counts || {};
        const planMRRs = summary?.plan_mrr || {};
        const mrrDivisor = Math.max(1, totalMRR);

        const tiers = [
            { key: 'starter', id: 'starter' },
            { key: 'professional', id: 'pro' },
            { key: 'scale', id: 'scale' },
            { key: 'india_only', id: 'india' }
        ];

        tiers.forEach(t => {
            const count = planCounts[t.key] || 0;
            const mrr = planMRRs[t.key] || 0;
            const pct = Math.min(100, Math.round((mrr / mrrDivisor) * 100));

            $(`#fin-tier-${t.id}-count`).text(`${count} tenant${count === 1 ? '' : 's'}`);
            $(`#fin-tier-${t.id}-mrr`).text(`₹${mrr.toLocaleString('en-IN')} /mo`);
            $(`#fin-tier-${t.id}-bar`).css('width', `${pct}%`);
        });

        // Revenue Stream Composition
        const basePct = Math.min(100, Math.round((baseMRR / mrrDivisor) * 100));
        $('#fin-stream-base-mrr').text(`₹${baseMRR.toLocaleString('en-IN')} /mo`);
        $('#fin-stream-base-pct').text(`${basePct}%`);
        $('#fin-stream-base-bar').css('width', `${basePct}%`);

        const aiAddonsMRR = workspaces.reduce((sum, w) => sum + (Number(w.addon_ai_price) || 0), 0);
        const aiPct = Math.min(100, Math.round((aiAddonsMRR / mrrDivisor) * 100));
        $('#fin-stream-ai-mrr').text(`₹${aiAddonsMRR.toLocaleString('en-IN')} /mo`);
        $('#fin-stream-ai-pct').text(`${aiPct}%`);
        $('#fin-stream-ai-bar').css('width', `${aiPct}%`);

        const storageAddonsMRR = workspaces.reduce((sum, w) => sum + (Number(w.addon_storage_price) || 0), 0);
        const storagePct = Math.min(100, Math.round((storageAddonsMRR / mrrDivisor) * 100));
        $('#fin-stream-storage-mrr').text(`₹${storageAddonsMRR.toLocaleString('en-IN')} /mo`);
        $('#fin-stream-storage-pct').text(`${storagePct}%`);
        $('#fin-stream-storage-bar').css('width', `${storagePct}%`);

        // Render Ledger Table
        window.filterFinancialWorkspaces();
    };

    window.filterFinancialWorkspaces = function() {
        const workspaces = window.rawWorkspaces || [];
        const query = ($('#fin-search').val() || '').toLowerCase().trim();
        const planFilter = $('#fin-filter-plan').val();
        const cycleFilter = $('#fin-filter-cycle').val();
        const statusFilter = $('#fin-filter-status').val();

        const filtered = workspaces.filter(ws => {
            const cleanWsName = (ws.name || '').replace(/\\'/g, "'").replace(/\\"/g, '"');
            const cleanSlug = (ws.slug || '').replace(/\\'/g, "'");
            const cleanOwner = (ws.owner_name || '').replace(/\\'/g, "'").replace(/\\"/g, '"');
            const cleanEmail = (ws.owner_email || '').replace(/\\'/g, "'");

            const matchesQuery = !query || 
                cleanWsName.toLowerCase().includes(query) ||
                cleanSlug.toLowerCase().includes(query) ||
                cleanOwner.toLowerCase().includes(query) ||
                cleanEmail.toLowerCase().includes(query);

            const pKey = (ws.plan || 'starter').toLowerCase();
            const matchesPlan = !planFilter || pKey === planFilter;
            const matchesCycle = !cycleFilter || ws.billing_cycle === cycleFilter;
            const matchesStatus = !statusFilter || ws.status === statusFilter;

            return matchesQuery && matchesPlan && matchesCycle && matchesStatus;
        });

        if (filtered.length === 0) {
            $('#fin-table-body').html('<tr><td colspan="9" class="px-5 py-8 text-center text-zinc-400 bg-zinc-50/20">No financial records matching current filters.</td></tr>');
            return;
        }

        let html = '';
        filtered.forEach(ws => {
            const cleanWsName = (ws.name || 'Workspace #' + ws.id).replace(/\\'/g, "'").replace(/\\"/g, '"');
            const cleanSlug = (ws.slug || '').replace(/\\'/g, "'");
            const cleanOwner = (ws.owner_name || 'Studio Owner').replace(/\\'/g, "'").replace(/\\"/g, '"');
            const cleanEmail = (ws.owner_email || '—').replace(/\\'/g, "'");
            const ownerInitial = (cleanOwner || 'W').charAt(0).toUpperCase();

            const pKey = (ws.plan || 'starter').toLowerCase();
            let planLabel = 'Starter';
            let planBadgeClass = 'bg-zinc-100 text-zinc-700 border-zinc-200/70';
            if (pKey === 'scale' || pKey === 'enterprise') {
                planLabel = 'Scale';
                planBadgeClass = 'bg-zinc-950 text-white border-zinc-950';
            } else if (pKey === 'professional' || pKey === 'pro' || pKey === 'studio_pro') {
                planLabel = 'Professional';
                planBadgeClass = 'bg-zinc-900 text-zinc-100 border-zinc-900';
            } else if (pKey === 'india_only') {
                planLabel = 'India Only';
                planBadgeClass = 'bg-zinc-100 text-zinc-800 border-zinc-300';
            }

            const isAnnual = (ws.billing_cycle === 'annual');
            const cycleBadge = isAnnual 
                ? '<span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-zinc-100 text-zinc-700 border border-zinc-200">Annual</span>'
                : '<span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-zinc-50 text-zinc-500 border border-zinc-200">Monthly</span>';

            const mrrFormatted = '₹' + Number(ws.mrr || 0).toLocaleString('en-IN');
            const arrFormatted = '₹' + Number(ws.arr || 0).toLocaleString('en-IN');
            const cashFormatted = '₹' + Number(ws.cash_collected || 0).toLocaleString('en-IN');
            const gstFormatted = '₹' + Number(ws.gst_collected || ws.gst_18 || 0).toLocaleString('en-IN');
            const renewalDate = ws.next_renewal_date || '—';

            const statusBadge = (ws.status === 'active')
                ? '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[9.5px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Settled</span>'
                : '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[9.5px] font-bold rounded-full bg-red-50 text-red-700 border border-red-200/60"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Overdue</span>';

            const currInd = ws.industry === 'photography' ? 'photography_studio' : (ws.industry || 'real_estate');
            const indLabel = currInd === 'photography_studio' ? 'Studio' : 'Real Estate';

            html += `
                <tr class="hover:bg-zinc-50/40 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-zinc-900 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                ${ownerInitial}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-zinc-950 text-xs truncate flex items-center gap-1.5">
                                    <span>${escapeHtml(cleanWsName)}</span>
                                    <span class="text-[9.5px] font-normal text-zinc-400 font-mono">/${escapeHtml(cleanSlug)}</span>
                                </div>
                                <div class="text-[11px] text-zinc-500 truncate flex items-center gap-1.5 mt-0.5">
                                    <span>${escapeHtml(cleanOwner)}</span>
                                    <span class="text-zinc-300">·</span>
                                    <span class="text-[10px] text-zinc-400 font-mono">${indLabel}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 text-[9.5px] font-bold rounded-md uppercase tracking-wider font-mono border ${planBadgeClass}">
                                    ${planLabel}
                                </span>
                            </div>
                            <div>${cycleBadge}</div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="font-mono font-bold text-zinc-950 text-xs">${mrrFormatted}</div>
                        <div class="text-[10px] text-zinc-400 font-mono">/ month</div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="font-mono font-semibold text-zinc-700 text-xs">${arrFormatted}</div>
                        <div class="text-[10px] text-zinc-400 font-mono">12x annualized</div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="font-mono font-bold text-emerald-700 text-xs">${cashFormatted}</div>
                        <div class="text-[10px] text-zinc-400">Total settled</div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="font-mono text-zinc-600 text-xs">${gstFormatted}</div>
                        <div class="text-[10px] text-zinc-400 font-mono">18% GST</div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="text-zinc-700 text-[11px] font-medium font-mono">${renewalDate}</div>
                        <div class="text-[10px] text-zinc-400">Auto-renewal</div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div>${statusBadge}</div>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <button onclick="openManageWorkspaceDrawer(${ws.id}, 'plan')" title="Configure Plan & Invoicing" class="px-2.5 py-1.5 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-800 bg-white hover:bg-zinc-950 hover:text-white cursor-pointer shadow-xs active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                Plan
                            </button>
                            <button onclick="openManageWorkspaceDrawer(${ws.id}, 'quota')" title="Top Up Quota Add-ons" class="px-2.5 py-1.5 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-800 bg-zinc-50 hover:bg-zinc-950 hover:text-white cursor-pointer shadow-xs active:scale-95 transition-all inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                Top-Up
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#fin-table-body').html(html);
    };

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
            nonce: coraREData.ajaxNonce,
            id: workspaceId,
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
                    const msg = (res.data && res.data.message) ? res.data.message : `Workspace status updated to ${newStatus.toUpperCase()}.`;
                    window.coraShowToast(msg, 'success');
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
            nonce: coraREData.ajaxNonce,
            id: workspaceId,
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
                    const msg = (res.data && res.data.message) ? res.data.message : `Workspace industry updated to ${label}.`;
                    window.coraShowToast(msg, 'success');
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
            nonce: coraREData.ajaxNonce,
            id: workspaceId,
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
                    const msg = (res.data && res.data.message) ? res.data.message : `Workspace plan changed to ${planLabel}.`;
                    window.coraShowToast(msg, 'success');
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
    } else if (coraREData.currentPage === 'super-ai-tokens') {
        loadAiAnalytics();
    } else if (coraREData.currentPage === 'super-feature-flags') {
        loadFeatureFlags();
    } else if (coraREData.currentPage === 'super-audit') {
        loadAuditLogs();
    }

    // =========================================================================
    // GOD-LEVEL CONTROLS: AI TOKENS, FEATURE FLAGS, EMERGENCY & AUDIT
    // =========================================================================
    let rawAiAnalytics = null;
    let rawFeatureFlagsData = null;
    let rawAuditLogsList = [];

    // --- TAB 7: AI TOKENS & QUOTAS ---
    window.loadAiAnalytics = function() {
        $('#ai-quota-table-body').html('<tr><td colspan="6" class="px-4 py-8 text-center text-zinc-400">Loading AI telemetry...</td></tr>');
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_ai_analytics',
            nonce: coraREData.ajaxNonce,
            security: coraREData.ajaxNonce
        }, function(res) {
            if (res.success && res.data) {
                rawAiAnalytics = res.data;
                $('#ai-stat-total-tokens').text(Number(res.data.total_tokens || 0).toLocaleString());
                $('#ai-stat-total-requests').text(Number(res.data.total_requests || 0).toLocaleString());
                
                const highBurnCount = (res.data.agencies || []).filter(a => a.burn_percentage >= 80 && !a.is_unlimited).length;
                $('#ai-stat-high-burn').text(highBurnCount);

                if (res.data.model_distribution) {
                    $('#ai-burn-gemini').text(Number(res.data.model_distribution.gemini_flash || 0).toLocaleString() + ' tokens');
                    $('#ai-burn-claude').text(Number(res.data.model_distribution.claude_sonnet || 0).toLocaleString() + ' tokens');
                    $('#ai-burn-gpt4o').text(Number(res.data.model_distribution.gpt4o || 0).toLocaleString() + ' tokens');
                    $('#ai-burn-groq').text(Number(res.data.model_distribution.groq_llama || 0).toLocaleString() + ' tokens');
                }

                renderAiQuotaTable(res.data.agencies || []);
            } else {
                $('#ai-quota-table-body').html('<tr><td colspan="6" class="px-4 py-8 text-center text-red-500">Failed to load AI analytics.</td></tr>');
            }
        }).fail(function() {
            $('#ai-quota-table-body').html('<tr><td colspan="6" class="px-4 py-8 text-center text-red-500">Network error loading AI analytics.</td></tr>');
        });
    };

    function renderAiQuotaTable(agencies) {
        if (!agencies || agencies.length === 0) {
            $('#ai-quota-table-body').html('<tr><td colspan="6" class="px-4 py-8 text-center text-zinc-400">No active workspaces found.</td></tr>');
            return;
        }

        let html = '';
        agencies.forEach(a => {
            const isUnlimited = a.is_unlimited;
            const burnPct = isUnlimited ? 0 : Math.min(100, a.burn_percentage || 0);
            const burnColor = burnPct > 85 ? 'bg-red-500' : (burnPct > 65 ? 'bg-amber-500' : 'bg-zinc-900');
            const burnBadge = isUnlimited 
                ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Unlimited Godmode</span>'
                : `<div class="w-24 bg-zinc-100 rounded-full h-1.5 overflow-hidden inline-block mr-2"><div class="${burnColor} h-1.5 rounded-full" style="width: ${burnPct}%"></div></div><span class="font-mono text-[11px]">${burnPct}%</span>`;

            html += `
            <tr class="hover:bg-zinc-50/50 transition-colors">
                <td class="px-4 py-3">
                    <div class="font-bold text-zinc-900">${escapeHtml(a.name)}</div>
                    <div class="text-[10px] text-zinc-400 font-mono">ID #${a.id} • ${escapeHtml(a.slug)}</div>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ${a.plan === 'pro' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700'}">${escapeHtml(a.plan)}</span>
                </td>
                <td class="px-4 py-3 font-mono text-xs">
                    <span class="font-bold text-zinc-900">${Number(a.used_tokens || 0).toLocaleString()}</span>
                    <span class="text-zinc-400"> / ${isUnlimited ? '∞' : Number(a.effective_quota || 0).toLocaleString()}</span>
                    ${a.bonus_tokens > 0 ? `<span class="ml-1 text-[10px] text-emerald-600 font-semibold">(+${Number(a.bonus_tokens).toLocaleString()} bonus)</span>` : ''}
                </td>
                <td class="px-4 py-3">
                    ${burnBadge}
                </td>
                <td class="px-4 py-3">
                    <button onclick="updateAgencyAiQuota(${a.id}, '${isUnlimited ? 'revoke_unlimited' : 'grant_unlimited'}', 0)" class="px-2.5 py-1 rounded text-[11px] font-bold border transition-colors cursor-pointer ${isUnlimited ? 'border-purple-200 bg-purple-50 text-purple-700 hover:bg-purple-100' : 'border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50'}">
                        ${isUnlimited ? 'Revoke Unlimited' : 'Grant Unlimited'}
                    </button>
                </td>
                <td class="px-4 py-3 text-right space-x-1">
                    <button onclick="updateAgencyAiQuota(${a.id}, 'grant_bonus', 100000)" class="px-2 py-1 rounded text-[10px] font-bold border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 transition-colors cursor-pointer" title="Add 100,000 bonus tokens">
                        +100k Tokens
                    </button>
                    <button onclick="updateAgencyAiQuota(${a.id}, 'reset_usage', 0)" class="px-2 py-1 rounded text-[10px] font-bold border border-zinc-200 bg-white text-zinc-700 hover:text-red-600 hover:border-red-200 transition-colors cursor-pointer" title="Reset monthly usage to 0">
                        Reset
                    </button>
                </td>
            </tr>`;
        });
        $('#ai-quota-table-body').html(html);
    }

    window.filterAiQuotaTable = function() {
        if (!rawAiAnalytics || !rawAiAnalytics.agencies) return;
        const query = ($('#ai-quota-search').val() || '').toLowerCase().trim();
        const filtered = rawAiAnalytics.agencies.filter(a => 
            (a.name && a.name.toLowerCase().includes(query)) ||
            (a.slug && a.slug.toLowerCase().includes(query)) ||
            (a.plan && a.plan.toLowerCase().includes(query))
        );
        renderAiQuotaTable(filtered);
    };

    window.updateAgencyAiQuota = function(agencyId, actionType, amount) {
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_update_ai_quota',
            nonce: coraREData.ajaxNonce,
            security: coraREData.ajaxNonce,
            agency_id: agencyId,
            action_type: actionType,
            amount: amount
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || 'AI quota updated successfully.', 'success');
                }
                loadAiAnalytics();
            } else {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data ? res.data.message : 'Failed to update quota.', 'error');
                }
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while updating AI quota.', 'error');
            }
        });
    };

    // --- TAB 8: FEATURE FLAGS MATRIX ---
    window.loadFeatureFlags = function() {
        $('#feature-flags-table-body').html('<tr><td colspan="10" class="px-4 py-8 text-center text-zinc-400">Loading feature flag matrix...</td></tr>');
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_feature_flags',
            nonce: coraREData.ajaxNonce,
            security: coraREData.ajaxNonce
        }, function(res) {
            if (res.success && res.data) {
                rawFeatureFlagsData = res.data;
                renderFeatureFlagsTable(res.data.agencies || [], res.data.flags_catalog || {});
            } else {
                $('#feature-flags-table-body').html('<tr><td colspan="10" class="px-4 py-8 text-center text-red-500">Failed to load feature flags.</td></tr>');
            }
        }).fail(function() {
            $('#feature-flags-table-body').html('<tr><td colspan="10" class="px-4 py-8 text-center text-red-500">Network error loading feature flags.</td></tr>');
        });
    };

    function renderFeatureFlagsTable(agencies, catalog) {
        if (!agencies || agencies.length === 0) {
            $('#feature-flags-table-body').html('<tr><td colspan="10" class="px-4 py-8 text-center text-zinc-400">No workspaces found.</td></tr>');
            return;
        }

        const flagKeys = ['canvas_builder', 'voice_ai', 'vision_ocr', 'whatsapp_gateway', 'vault_esign', 'seo_content_suite', 'mcp_gateway', 'finance_gst'];
        let html = '';

        agencies.forEach(a => {
            html += `
            <tr class="hover:bg-zinc-50/50 transition-colors" data-agency-id="${a.id}">
                <td class="px-4 py-3">
                    <div class="font-bold text-zinc-900">${escapeHtml(a.name)}</div>
                    <div class="text-[10px] text-zinc-400 font-mono">ID #${a.id} • ${escapeHtml(a.industry)}</div>
                </td>`;

            flagKeys.forEach(key => {
                const isChecked = a.flags && a.flags[key] !== false;
                html += `
                <td class="px-3 py-3 text-center">
                    <input type="checkbox" onchange="toggleAgencyFeatureFlag(${a.id}, '${key}', this.checked)" ${isChecked ? 'checked' : ''} class="w-4 h-4 rounded text-zinc-900 focus:ring-zinc-900 accent-zinc-900 cursor-pointer">
                </td>`;
            });

            html += `
                <td class="px-4 py-3 text-right space-x-1">
                    <button onclick="applyPresetFlags(${a.id}, 'all_on')" class="px-2 py-0.5 rounded text-[10px] font-bold border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 transition-colors cursor-pointer">All ON</button>
                    <button onclick="applyPresetFlags(${a.id}, 'essential')" class="px-2 py-0.5 rounded text-[10px] font-bold border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 transition-colors cursor-pointer">Core</button>
                </td>
            </tr>`;
        });

        $('#feature-flags-table-body').html(html);
    }

    window.toggleAgencyFeatureFlag = function(agencyId, flagKey, isChecked) {
        if (!rawFeatureFlagsData || !rawFeatureFlagsData.agencies) return;
        const agency = rawFeatureFlagsData.agencies.find(a => a.id == agencyId);
        if (!agency) return;

        agency.flags = agency.flags || {};
        agency.flags[flagKey] = isChecked;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_save_feature_flags',
            nonce: coraREData.ajaxNonce,
            security: coraREData.ajaxNonce,
            agency_id: agencyId,
            flags: agency.flags
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || 'Feature flag updated.', 'success');
                }
            } else {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data ? res.data.message : 'Failed to save flag.', 'error');
                }
            }
        });
    };

    window.applyPresetFlags = function(agencyId, presetType) {
        if (!rawFeatureFlagsData || !rawFeatureFlagsData.agencies) return;
        const agency = rawFeatureFlagsData.agencies.find(a => a.id == agencyId);
        if (!agency) return;

        const allKeys = ['canvas_builder', 'voice_ai', 'vision_ocr', 'whatsapp_gateway', 'vault_esign', 'seo_content_suite', 'mcp_gateway', 'finance_gst'];
        agency.flags = {};
        allKeys.forEach(k => {
            if (presetType === 'all_on') {
                agency.flags[k] = true;
            } else if (presetType === 'essential') {
                agency.flags[k] = ['vault_esign', 'finance_gst', 'canvas_builder'].includes(k);
            }
        });

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_save_feature_flags',
            nonce: coraREData.ajaxNonce,
            security: coraREData.ajaxNonce,
            agency_id: agencyId,
            flags: agency.flags
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) {
                    window.coraShowToast('Preset applied successfully.', 'success');
                }
                renderFeatureFlagsTable(rawFeatureFlagsData.agencies, rawFeatureFlagsData.flags_catalog);
            }
        });
    };

    // --- TAB 9: EMERGENCY COMMAND CENTER ---
    window.togglePlatformMaintenance = function(enabled) {
        const msg = $('#emergency-maintenance-msg').val() || '';
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_toggle_maintenance_mode',
            nonce: coraREData.ajaxNonce,
            security: coraREData.ajaxNonce,
            enabled: enabled,
            message: msg,
            read_only: enabled
        }, function(res) {
            if (res.success) {
                const now = new Date().toLocaleTimeString();
                const logEntry = `<div>[${now}] ${res.data.message}</div>`;
                $('#emergency-console-log').prepend(logEntry);
                
                if (enabled) {
                    $('#emergency-status-badge').removeClass('border-emerald-200 bg-emerald-50 text-emerald-800').addClass('border-amber-200 bg-amber-50 text-amber-800').html('<span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Maintenance Mode Active');
                } else {
                    $('#emergency-status-badge').removeClass('border-amber-200 bg-amber-50 text-amber-800').addClass('border-emerald-200 bg-emerald-50 text-emerald-800').html('<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Platform Operational');
                }

                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message, enabled ? 'warning' : 'success');
                }
            }
        });
    };

    window.triggerEmergencyAction = function(actionType) {
        const now = new Date().toLocaleTimeString();
        $('#emergency-console-log').prepend(`<div>[${now}] Initiating ${actionType}...</div>`);

        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_emergency_lockdown',
            nonce: coraREData.ajaxNonce,
            security: coraREData.ajaxNonce,
            action_type: actionType,
            freeze_state: true
        }, function(res) {
            const timeStr = new Date().toLocaleTimeString();
            if (res.success) {
                $('#emergency-console-log').prepend(`<div class="text-emerald-400">[${timeStr}] SUCCESS: ${res.data.message}</div>`);
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message, 'success');
                }
            } else {
                $('#emergency-console-log').prepend(`<div class="text-red-400">[${timeStr}] ERROR: ${res.data ? res.data.message : 'Action failed.'}</div>`);
                if (window.coraShowToast) {
                    window.coraShowToast(res.data ? res.data.message : 'Emergency action failed.', 'error');
                }
            }
        }).fail(function() {
            const timeStr = new Date().toLocaleTimeString();
            $('#emergency-console-log').prepend(`<div class="text-red-400">[${timeStr}] NETWORK ERROR executing ${actionType}.</div>`);
        });
    };

    // --- TAB 10: FORENSICS & AUDIT TRAIL ---
    window.loadAuditLogs = function() {
        $('#audit-logs-table-body').html('<tr><td colspan="6" class="px-4 py-8 text-center text-zinc-400">Loading forensics logs...</td></tr>');
        $.post(coraREData.ajaxUrl, {
            action: 'cora_super_get_audit_logs',
            nonce: coraREData.ajaxNonce,
            security: coraREData.ajaxNonce,
            limit: 50
        }, function(res) {
            if (res.success && res.data) {
                rawAuditLogsList = res.data.logs || [];
                $('#audit-log-count-badge').text((res.data.count || rawAuditLogsList.length) + ' events');
                renderAuditLogsTable(rawAuditLogsList);
            } else {
                $('#audit-logs-table-body').html('<tr><td colspan="6" class="px-4 py-8 text-center text-red-500">Failed to load audit trail.</td></tr>');
            }
        }).fail(function() {
            $('#audit-logs-table-body').html('<tr><td colspan="6" class="px-4 py-8 text-center text-red-500">Network error loading audit trail.</td></tr>');
        });
    };

    function renderAuditLogsTable(logs) {
        if (!logs || logs.length === 0) {
            $('#audit-logs-table-body').html('<tr><td colspan="6" class="px-4 py-8 text-center text-zinc-400">No forensics records found matching criteria.</td></tr>');
            return;
        }

        let html = '';
        logs.forEach(l => {
            let badgeClass = 'bg-zinc-100 text-zinc-700';
            if (l.action && l.action.includes('AUTH')) badgeClass = 'bg-blue-50 text-blue-700 border border-blue-200';
            if (l.action && l.action.includes('AI')) badgeClass = 'bg-purple-50 text-purple-700 border border-purple-200';
            if (l.action && l.action.includes('SECURITY')) badgeClass = 'bg-red-50 text-red-700 border border-red-200';
            if (l.action && l.action.includes('FEATURE')) badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
            if (l.action && l.action.includes('GST')) badgeClass = 'bg-amber-50 text-amber-800 border border-amber-200';

            html += `
            <tr class="hover:bg-zinc-50/50 transition-colors">
                <td class="px-4 py-2.5 font-mono text-[11px] text-zinc-500 whitespace-nowrap">${escapeHtml(l.created_at || '—')}</td>
                <td class="px-4 py-2.5 font-semibold text-zinc-900">${escapeHtml(l.agency_name || 'Global')}</td>
                <td class="px-4 py-2.5 text-zinc-700 font-mono text-[11px]">${escapeHtml(l.user_login || 'System')}</td>
                <td class="px-4 py-2.5"><span class="px-2 py-0.5 rounded text-[9.5px] font-bold ${badgeClass}">${escapeHtml(l.action || 'EVENT')}</span></td>
                <td class="px-4 py-2.5 text-zinc-600 max-w-xs truncate" title="${escapeHtml(l.details || '')}">${escapeHtml(l.details || '—')}</td>
                <td class="px-4 py-2.5 font-mono text-[11px] text-zinc-400 text-right">${escapeHtml(l.ip_address || '127.0.0.1')}</td>
            </tr>`;
        });
        $('#audit-logs-table-body').html(html);
    }

    window.filterAuditLogsTable = function() {
        const query = ($('#audit-filter-keyword').val() || '').toLowerCase().trim();
        const actionType = $('#audit-filter-action').val() || '';

        const filtered = rawAuditLogsList.filter(l => {
            const matchesQuery = !query || 
                (l.action && l.action.toLowerCase().includes(query)) ||
                (l.details && l.details.toLowerCase().includes(query)) ||
                (l.agency_name && l.agency_name.toLowerCase().includes(query));
            const matchesAction = !actionType || (l.action && l.action.includes(actionType));
            return matchesQuery && matchesAction;
        });

        renderAuditLogsTable(filtered);
    };

    window.exportAuditLogsCsv = function() {
        if (!rawAuditLogsList || rawAuditLogsList.length === 0) {
            if (window.coraShowToast) window.coraShowToast('No audit logs to export.', 'warning');
            return;
        }

        let csv = 'ID,Timestamp,Workspace,User,Action,Details,IP_Address\n';
        rawAuditLogsList.forEach(l => {
            const row = [
                l.id,
                `"${l.created_at || ''}"`,
                `"${(l.agency_name || '').replace(/"/g, '""')}"`,
                `"${(l.user_login || '').replace(/"/g, '""')}"`,
                `"${(l.action || '').replace(/"/g, '""')}"`,
                `"${(l.details || '').replace(/"/g, '""')}"`,
                `"${l.ip_address || ''}"`
            ];
            csv += row.join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', `cora_forensics_audit_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
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

    $('#cora-appeal-review-drawer').addClass('open').removeClass('translate-x-full translate-y-full');
    $('#cora-appeal-review-overlay').removeClass('hidden');
};

window.closeAppealReviewDrawer = function() {
    $('#cora-appeal-review-drawer').removeClass('open').addClass('translate-y-full sm:translate-y-0 sm:translate-x-full');
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
    $('#cora-add-workspace-drawer').addClass('open').removeClass('translate-x-full translate-y-full');
    $('#cora-add-workspace-overlay').removeClass('hidden');
};

window.closeCreateWorkspaceDrawer = function() {
    $('#cora-add-workspace-drawer').removeClass('open').addClass('translate-y-full sm:translate-y-0 sm:translate-x-full');
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

function findWorkspaceInStore(workspaceId) {
    const list = (window.rawWorkspaces && Array.isArray(window.rawWorkspaces) && window.rawWorkspaces.length > 0)
        ? window.rawWorkspaces
        : ((typeof rawWorkspaces !== 'undefined' && Array.isArray(rawWorkspaces)) ? rawWorkspaces : []);
    
    return list.find(w => String(w.id) === String(workspaceId) || Number(w.id) === Number(workspaceId));
}

window.selectedPlan = 'professional';
window.selectedBillingCycle = 'monthly';
window.aiTopUpMode = 'one_time';
window.storageBoostMode = 'one_time';

window.openManageWorkspaceDrawer = function(workspaceId, focusSection) {
    const ws = findWorkspaceInStore(workspaceId);

    if (!ws) {
        if (window.coraShowToast) window.coraShowToast('Workspace data not found.', 'error');
        return;
    }

    currentManagingWorkspaceId = ws.id;

    // Set Header Info
    $('#manage-ws-title').text(ws.name || 'Workspace #' + ws.id);
    $('#manage-ws-slug-info').text('app.heycora.in/' + (ws.slug || 'workspace'));

    // Set Billing Cycle
    const cycle = (ws.billing_cycle === 'annual') ? 'annual' : 'monthly';
    setDrawerBillingCycle(cycle, true);

    // Set Plan
    const pKey = (ws.plan || 'starter').toLowerCase();
    setDrawerPlan(pKey, true);

    // AI Telemetry & Quotas (1 AI Run = 1 credit = 500 Tokens)
    const usedTokens = Number(ws.used_tokens) || 0;
    const usedRuns = Number(ws.ai_runs_used) || Math.round(usedTokens / 500);
    const baseRuns = Number(ws.ai_runs_base) || (ws.base_quota ? Math.round(ws.base_quota / 500) : 2000);
    const bonusRuns = Number(ws.ai_runs_bonus) || (ws.bonus_tokens ? Math.round(ws.bonus_tokens / 500) : 0);
    const recAiRuns = Number(ws.recurring_ai_runs) || 0;
    const isUnlimited = !!ws.is_unlimited;

    $('#manage-ws-ai-used-display').text(usedRuns.toLocaleString());
    $('#manage-ws-ai-base-display').text(baseRuns.toLocaleString());
    $('#manage-ws-ai-recurring-display').text('+' + recAiRuns.toLocaleString());
    $('#manage-ws-ai-bonus-display').text('+' + bonusRuns.toLocaleString());
    
    if (isUnlimited) {
        $('#manage-ws-godmode-pill').removeClass('hidden');
    } else {
        $('#manage-ws-godmode-pill').addClass('hidden');
    }
    $('#manage-ws-unlimited-ai').prop('checked', isUnlimited);
    $('#manage-ws-ai-runs-base').val(baseRuns);
    $('#manage-ws-ai-topup-input').val('');
    updateAiCostCalc();

    // Storage Telemetry & Quotas (in GB)
    const currentStorMb = Number(ws.current_storage_mb) || 0;
    const storLimitMb = Number(ws.storage_limit_mb) || 2048;
    const storLimitGb = Math.round(storLimitMb / 1024) || 2;
    const recStorGb = Number(ws.recurring_storage_gb) || 0;

    $('#manage-ws-storage-used-display').text(currentStorMb >= 1024 ? (currentStorMb / 1024).toFixed(1) + ' GB' : currentStorMb.toFixed(1) + ' MB');
    $('#manage-ws-storage-base-display').text(storLimitGb + ' GB');
    $('#manage-ws-storage-recurring-display').text('+' + recStorGb + ' GB');
    $('#manage-ws-storage-gb-base').val(storLimitGb);
    $('#manage-ws-storage-boost-input').val('');
    updateStorageCostCalc();

    // Set Users & Emails Quotas
    $('#manage-ws-max-users').val(ws.max_users_limit || 1);
    $('#manage-ws-max-emails').val(ws.max_emails_limit || 200);

    // Set Feature Flags
    $('#manage-ws-enable-leads').prop('checked', ws.enable_leads !== false);
    $('#manage-ws-enable-clients').prop('checked', ws.enable_clients !== false);
    $('#manage-ws-enable-properties').prop('checked', ws.enable_properties !== false);
    $('#manage-ws-enable-bookings').prop('checked', ws.enable_bookings !== false);
    $('#manage-ws-enable-ledger').prop('checked', ws.enable_ledger !== false);
    $('#manage-ws-enable-documents').prop('checked', ws.enable_documents !== false);

    // Reset Top-Up / Boost Modes
    setAiTopUpMode('one_time');
    setStorageBoostMode('one_time');

    // Recalculate invoice simulator
    recalculateDrawerInvoice();

    // Open Bottom Sheet Drawer with active class
    $('#cora-manage-workspace-drawer').addClass('cora-sheet-active');
    $('#cora-manage-workspace-overlay').addClass('cora-sheet-active');

    // Auto focus appropriate input if requested
    if (focusSection === 'quota') {
        setTimeout(function() {
            $('#manage-ws-ai-topup-input').focus();
        }, 300);
    } else if (focusSection === 'storage') {
        setTimeout(function() {
            $('#manage-ws-storage-boost-input').focus();
        }, 300);
    } else if (focusSection === 'settings') {
        setTimeout(function() {
            $('#manage-ws-max-users').focus();
        }, 300);
    }
};

window.closeManageWorkspaceDrawer = function() {
    $('#cora-manage-workspace-drawer').removeClass('cora-sheet-active');
    $('#cora-manage-workspace-overlay').removeClass('cora-sheet-active');
    currentManagingWorkspaceId = null;
};

window.setDrawerBillingCycle = function(cycle, skipToast) {
    window.selectedBillingCycle = (cycle === 'annual') ? 'annual' : 'monthly';
    const isAnnual = (window.selectedBillingCycle === 'annual');

    if (isAnnual) {
        $('#drawer-cycle-btn-monthly').removeClass('bg-zinc-950 text-white shadow-xs font-bold').addClass('text-zinc-600 hover:text-zinc-900 font-medium');
        $('#drawer-cycle-btn-annual').removeClass('text-zinc-600 hover:text-zinc-900 font-medium').addClass('bg-zinc-950 text-white shadow-xs font-bold');
        $('#manage-ws-cycle-badge').text('Annual (1 Year)').removeClass('bg-zinc-100 text-zinc-700').addClass('bg-emerald-50 text-emerald-800 border border-emerald-200');

        // Update Card Prices for Annual
        $('#plan-price-starter').html('₹833<span>/mo</span>');
        $('#plan-sub-starter').text('₹9,990 / yr · Save ₹1,998');

        $('#plan-price-pro').html('₹1,665<span>/mo</span>');
        $('#plan-sub-pro').text('₹19,990 / yr · Save ₹3,998');

        $('#plan-price-scale').html('₹2,499<span>/mo</span>');
        $('#plan-sub-scale').text('₹29,990 / yr · Save ₹5,998');

        // Unlock India Only Card
        $('#plan-tier-india-only').removeClass('opacity-60 cursor-not-allowed').addClass('cursor-pointer hover:border-zinc-400');
        $('#plan-india-locked-tag').addClass('hidden');
    } else {
        $('#drawer-cycle-btn-annual').removeClass('bg-zinc-950 text-white shadow-xs font-bold').addClass('text-zinc-600 hover:text-zinc-900 font-medium');
        $('#drawer-cycle-btn-monthly').removeClass('text-zinc-600 hover:text-zinc-900 font-medium').addClass('bg-zinc-950 text-white shadow-xs font-bold');
        $('#manage-ws-cycle-badge').text('Monthly').removeClass('bg-emerald-50 text-emerald-800 border border-emerald-200').addClass('bg-zinc-100 text-zinc-700');

        // Update Card Prices for Monthly
        $('#plan-price-starter').html('₹999<span>/mo</span>');
        $('#plan-sub-starter').text('Billed monthly · Cancel anytime');

        $('#plan-price-pro').html('₹1,999<span>/mo</span>');
        $('#plan-sub-pro').text('Billed monthly · Cancel anytime');

        $('#plan-price-scale').html('₹2,999<span>/mo</span>');
        $('#plan-sub-scale').text('Billed monthly · Cancel anytime');

        // If currently on India Only and user selects monthly, auto switch to starter or prompt
        if (window.selectedPlan === 'india_only') {
            setDrawerPlan('starter', false);
            if (!skipToast && window.coraShowToast) {
                window.coraShowToast('India Only Plan is available on Annual Commitment only. Switched to Starter plan for monthly cycle.', 'info');
            }
        }

        // Show India Only Locked Notice
        $('#plan-tier-india-only').addClass('opacity-60 cursor-not-allowed').removeClass('hover:border-zinc-400');
        $('#plan-india-locked-tag').removeClass('hidden');
    }

    recalculateDrawerInvoice();
};

window.setDrawerPlan = function(planKey, skipOverride) {
    planKey = (planKey || 'starter').toLowerCase();
    
    // Normalize aliases
    if (planKey === 'pro' || planKey === 'studio_pro' || planKey === 'beta') {
        planKey = 'professional';
    } else if (planKey === 'enterprise') {
        planKey = 'scale';
    } else if (planKey !== 'scale' && planKey !== 'professional' && planKey !== 'india_only') {
        planKey = 'starter';
    }

    // If India Only selected while monthly, auto switch cycle to annual
    if (planKey === 'india_only' && window.selectedBillingCycle !== 'annual') {
        setDrawerBillingCycle('annual', true);
        if (window.coraShowToast) window.coraShowToast('Switched to Annual billing (India Only Plan requires 1-Year Commitment).', 'info');
    }

    window.selectedPlan = planKey;
    $('#manage-ws-plan').val(planKey);

    // Update Plan card visual highlights
    $('.plan-tier-card').removeClass('selected-plan');
    const cardId = `#plan-tier-${planKey === 'india_only' ? 'india-only' : (planKey === 'professional' ? 'pro' : planKey)}`;
    $(cardId).addClass('selected-plan');

    // Update Plan Badge in Header
    const planLabels = {
        'starter': 'Starter',
        'professional': 'Professional',
        'scale': 'Scale',
        'india_only': 'India Only'
    };
    $('#manage-ws-plan-badge').text(planLabels[planKey] || 'Starter');

    // Auto update defaults if user manually switched plan
    if (!skipOverride) {
        if (planKey === 'starter') {
            $('#manage-ws-ai-runs-base').val(2000);
            $('#manage-ws-storage-gb-base').val(2);
            $('#manage-ws-max-users').val(1);
            $('#manage-ws-max-emails').val(200);
        } else if (planKey === 'professional') {
            $('#manage-ws-ai-runs-base').val(10000);
            $('#manage-ws-storage-gb-base').val(10);
            $('#manage-ws-max-users').val(5);
            $('#manage-ws-max-emails').val(1000);
        } else if (planKey === 'scale') {
            $('#manage-ws-ai-runs-base').val(25000);
            $('#manage-ws-storage-gb-base').val(50);
            $('#manage-ws-max-users').val(15);
            $('#manage-ws-max-emails').val(5000);
        } else if (planKey === 'india_only') {
            $('#manage-ws-ai-runs-base').val(3500);
            $('#manage-ws-storage-gb-base').val(2);
            $('#manage-ws-max-users').val(1);
            $('#manage-ws-max-emails').val(200);
        }
    }

    recalculateDrawerInvoice();
};

window.setAiTopUpMode = function(mode) {
    window.aiTopUpMode = (mode === 'recurring') ? 'recurring' : 'one_time';
    
    if (window.aiTopUpMode === 'recurring') {
        $('#ai-mode-one-time-btn').removeClass('bg-zinc-950 text-white font-bold').addClass('bg-zinc-100 text-zinc-600 hover:text-zinc-900');
        $('#ai-mode-recurring-btn').removeClass('bg-zinc-100 text-zinc-600 hover:text-zinc-900').addClass('bg-zinc-950 text-white font-bold');
        $('#manage-ws-inject-ai-btn-text').text('Add Recurring Add-On');
        $('#manage-ws-ai-mode-desc').text('Monthly recurring add-ons permanently increase the workspace base allocation and will be billed on every subscription renewal.');
    } else {
        $('#ai-mode-recurring-btn').removeClass('bg-zinc-950 text-white font-bold').addClass('bg-zinc-100 text-zinc-600 hover:text-zinc-900');
        $('#ai-mode-one-time-btn').removeClass('bg-zinc-100 text-zinc-600 hover:text-zinc-900').addClass('bg-zinc-950 text-white font-bold');
        $('#manage-ws-inject-ai-btn-text').text('Add One-Time Top-Up');
        $('#manage-ws-ai-mode-desc').text('One-time top-ups are added to the current billing cycle immediately without altering future recurring invoices.');
    }
    
    updateAiCostCalc();
};

window.setStorageBoostMode = function(mode) {
    window.storageBoostMode = (mode === 'recurring') ? 'recurring' : 'one_time';
    
    if (window.storageBoostMode === 'recurring') {
        $('#storage-mode-one-time-btn').removeClass('bg-zinc-950 text-white font-bold').addClass('bg-zinc-100 text-zinc-600 hover:text-zinc-900');
        $('#storage-mode-recurring-btn').removeClass('bg-zinc-100 text-zinc-600 hover:text-zinc-900').addClass('bg-zinc-950 text-white font-bold');
        $('#manage-ws-boost-storage-btn-text').text('Add Recurring Storage');
        $('#manage-ws-storage-mode-desc').text('Monthly recurring storage add-ons permanently expand the workspace capacity ceiling and add to recurring invoices (@ ₹10/GB/mo).');
    } else {
        $('#storage-mode-recurring-btn').removeClass('bg-zinc-950 text-white font-bold').addClass('bg-zinc-100 text-zinc-600 hover:text-zinc-900');
        $('#storage-mode-one-time-btn').removeClass('bg-zinc-100 text-zinc-600 hover:text-zinc-900').addClass('bg-zinc-950 text-white font-bold');
        $('#manage-ws-boost-storage-btn-text').text('Add One-Time Boost');
        $('#manage-ws-storage-mode-desc').text('One-time extra storage provides an immediate temporary expansion without adding to future recurring invoices.');
    }
    
    updateStorageCostCalc();
};

window.setDrawerAiRunsPreset = function(runs) {
    $('#manage-ws-ai-runs-base').val(runs);
};

window.setAiTopUpPreset = function(runs) {
    $('#manage-ws-ai-topup-input').val(runs);
    updateAiCostCalc();
};

window.setDrawerStoragePresetGb = function(gb) {
    $('#manage-ws-storage-gb-base').val(gb);
};

window.setStorageBoostPreset = function(gb) {
    $('#manage-ws-storage-boost-input').val(gb);
    updateStorageCostCalc();
};

window.updateAiCostCalc = function(val) {
    const runs = parseInt(val !== undefined ? val : $('#manage-ws-ai-topup-input').val(), 10) || 0;
    const cost = Math.round((runs / 1000) * 100);
    const modeLabel = (window.aiTopUpMode === 'recurring') ? '/mo' : '';
    
    if (runs > 0) {
        $('#manage-ws-ai-topup-cost').text(`= ₹${cost.toLocaleString()}${modeLabel}`);
    } else {
        $('#manage-ws-ai-topup-cost').text(`= ₹100${modeLabel}`);
    }
};

window.updateStorageCostCalc = function(val) {
    const gb = parseInt(val !== undefined ? val : $('#manage-ws-storage-boost-input').val(), 10) || 0;
    const cost = gb * 10;
    const modeLabel = (window.storageBoostMode === 'recurring') ? '/mo' : '';
    
    if (gb > 0) {
        $('#manage-ws-storage-boost-cost').text(`= ₹${cost.toLocaleString()}${modeLabel}`);
    } else {
        $('#manage-ws-storage-boost-cost').text(`= ₹10${modeLabel}`);
    }
};

window.recalculateDrawerInvoice = function() {
    const ws = currentManagingWorkspaceId ? findWorkspaceInStore(currentManagingWorkspaceId) : null;
    const plan = window.selectedPlan || (ws ? ws.plan : 'starter');
    const isAnnual = (window.selectedBillingCycle === 'annual');

    let basePrice = 999;
    let planLabel = 'Starter Plan';

    if (plan === 'india_only') {
        basePrice = 5988; // Annual ₹5,988/yr
        planLabel = 'India Only Plan (Annual)';
    } else if (plan === 'scale') {
        basePrice = isAnnual ? 29990 : 2999;
        planLabel = isAnnual ? 'Scale Plan (Annual)' : 'Scale Plan (Monthly)';
    } else if (plan === 'professional') {
        basePrice = isAnnual ? 19990 : 1999;
        planLabel = isAnnual ? 'Professional Plan (Annual)' : 'Professional Plan (Monthly)';
    } else {
        basePrice = isAnnual ? 9990 : 999;
        planLabel = isAnnual ? 'Starter Plan (Annual)' : 'Starter Plan (Monthly)';
    }

    const recAiRuns = ws ? (Number(ws.recurring_ai_runs) || 0) : 0;
    const recStorGb = ws ? (Number(ws.recurring_storage_gb) || 0) : 0;

    const monthlyAiAddon = Math.round((recAiRuns / 1000) * 100);
    const monthlyStorAddon = recStorGb * 10;

    const cycleAiAddon = isAnnual ? (monthlyAiAddon * 12) : monthlyAiAddon;
    const cycleStorAddon = isAnnual ? (monthlyStorAddon * 12) : monthlyStorAddon;

    const subtotal = basePrice + cycleAiAddon + cycleStorAddon;
    const gst18 = Math.round(subtotal * 0.18 * 100) / 100;
    const total = Math.round((subtotal + gst18) * 100) / 100;

    const intervalLabel = isAnnual ? ' / year' : ' / month';

    $('#invoice-plan-name').text(planLabel);
    $('#invoice-base-price').text(`₹${basePrice.toLocaleString()}`);
    $('#invoice-ai-runs-count').text(recAiRuns.toLocaleString());
    $('#invoice-ai-price').text(`+₹${cycleAiAddon.toLocaleString()}`);
    $('#invoice-storage-gb-count').text(recStorGb.toLocaleString());
    $('#invoice-storage-price').text(`+₹${cycleStorAddon.toLocaleString()}`);
    $('#invoice-subtotal-price').text(`₹${subtotal.toLocaleString()}`);
    $('#invoice-gst-price').text(`₹${gst18.toFixed(2)}`);
    $('#invoice-total-price').text(`₹${total.toFixed(2)}${intervalLabel}`);
    $('#invoice-cycle-note').text(isAnnual ? 'Next Renewal: Annual Billing' : 'Next Renewal: Monthly Billing');
};

window.injectAiRunsDrawer = function() {
    if (!currentManagingWorkspaceId) return;
    const runs = parseInt($('#manage-ws-ai-topup-input').val(), 10);
    if (!runs || runs <= 0) {
        if (window.coraShowToast) window.coraShowToast('Please enter a valid positive number of AI runs to top up.', 'error');
        return;
    }

    const btn = $('#manage-ws-inject-ai-btn');
    const origHtml = btn.html();
    btn.prop('disabled', true).html('Processing...');

    $.post(coraREData.ajaxUrl, {
        action: 'cora_super_update_ai_quota',
        nonce: coraREData.ajaxNonce,
        security: coraREData.ajaxNonce,
        agency_id: currentManagingWorkspaceId,
        action_type: 'grant_ai_runs',
        amount: runs,
        billing_mode: window.aiTopUpMode || 'one_time'
    }, function(res) {
        btn.prop('disabled', false).html(origHtml);
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || `+${runs.toLocaleString()} AI Runs allocated!`, 'success');
            $('#manage-ws-ai-topup-input').val('');
            updateAiCostCalc();

            if (res.data && res.data.bonus_runs !== undefined) {
                $('#manage-ws-ai-bonus-display').text('+' + Number(res.data.bonus_runs).toLocaleString());
            }
            if (res.data && res.data.recurring_ai_runs !== undefined) {
                $('#manage-ws-ai-recurring-display').text('+' + Number(res.data.recurring_ai_runs).toLocaleString());
            }
            if (res.data && res.data.ai_runs_base !== undefined) {
                $('#manage-ws-ai-base-display').text(Number(res.data.ai_runs_base).toLocaleString());
            }
            if (typeof loadPlatformData === 'function') loadPlatformData();
            setTimeout(recalculateDrawerInvoice, 400);
        } else {
            const err = (res.data && res.data.message) ? res.data.message : 'Failed to top up AI Runs.';
            if (window.coraShowToast) window.coraShowToast(err, 'error');
        }
    }).fail(function() {
        btn.prop('disabled', false).html(origHtml);
        if (window.coraShowToast) window.coraShowToast('Network error while topping up AI runs.', 'error');
    });
};

window.injectExtraStorageDrawer = function() {
    if (!currentManagingWorkspaceId) return;
    const gb = parseInt($('#manage-ws-storage-boost-input').val(), 10);
    if (!gb || gb <= 0) {
        if (window.coraShowToast) window.coraShowToast('Please enter a valid GB amount for storage expansion.', 'error');
        return;
    }

    const btn = $('#manage-ws-boost-storage-btn');
    const origHtml = btn.html();
    btn.prop('disabled', true).html('Processing...');

    $.post(coraREData.ajaxUrl, {
        action: 'cora_super_update_ai_quota',
        nonce: coraREData.ajaxNonce,
        security: coraREData.ajaxNonce,
        agency_id: currentManagingWorkspaceId,
        action_type: 'grant_storage_gb',
        amount: gb,
        billing_mode: window.storageBoostMode || 'one_time'
    }, function(res) {
        btn.prop('disabled', false).html(origHtml);
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || `+${gb} GB storage allocated!`, 'success');
            $('#manage-ws-storage-boost-input').val('');
            updateStorageCostCalc();

            if (res.data && res.data.storage_limit_mb) {
                const newLim = res.data.storage_limit_mb;
                const newGb = Math.round(newLim / 1024);
                $('#manage-ws-storage-gb-base').val(newGb);
                $('#manage-ws-storage-base-display').text(newGb + ' GB');
            }
            if (res.data && res.data.recurring_storage_gb !== undefined) {
                $('#manage-ws-storage-recurring-display').text('+' + Number(res.data.recurring_storage_gb) + ' GB');
            }
            if (typeof loadPlatformData === 'function') loadPlatformData();
            setTimeout(recalculateDrawerInvoice, 400);
        } else {
            const err = (res.data && res.data.message) ? res.data.message : 'Failed to boost storage capacity.';
            if (window.coraShowToast) window.coraShowToast(err, 'error');
        }
    }).fail(function() {
        btn.prop('disabled', false).html(origHtml);
        if (window.coraShowToast) window.coraShowToast('Network error while boosting storage.', 'error');
    });
};

window.toggleDrawerGodmode = function(isUnlimited) {
    if (!currentManagingWorkspaceId) return;
    
    $.post(coraREData.ajaxUrl, {
        action: 'cora_super_update_ai_quota',
        nonce: coraREData.ajaxNonce,
        security: coraREData.ajaxNonce,
        agency_id: currentManagingWorkspaceId,
        action_type: isUnlimited ? 'grant_unlimited' : 'revoke_unlimited',
        amount: 0
    }, function(res) {
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast(res.data.message || (isUnlimited ? 'Unlimited AI quota granted!' : 'Unlimited AI quota revoked.'), 'success');
            if (isUnlimited) {
                $('#manage-ws-godmode-pill').removeClass('hidden');
            } else {
                $('#manage-ws-godmode-pill').addClass('hidden');
            }
            if (typeof loadPlatformData === 'function') loadPlatformData();
        } else {
            $('#manage-ws-unlimited-ai').prop('checked', !isUnlimited);
            if (window.coraShowToast) window.coraShowToast((res.data && res.data.message) ? res.data.message : 'Failed to toggle Unlimited status.', 'error');
        }
    }).fail(function() {
        $('#manage-ws-unlimited-ai').prop('checked', !isUnlimited);
        if (window.coraShowToast) window.coraShowToast('Network error while toggling Unlimited mode.', 'error');
    });
};

window.resetTokenUsageDrawer = function() {
    if (!currentManagingWorkspaceId) return;
    
    $.post(coraREData.ajaxUrl, {
        action: 'cora_super_update_ai_quota',
        nonce: coraREData.ajaxNonce,
        security: coraREData.ajaxNonce,
        agency_id: currentManagingWorkspaceId,
        action_type: 'reset_usage',
        amount: 0
    }, function(res) {
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast('Monthly AI usage counters reset to 0.', 'success');
            $('#manage-ws-ai-used-display').text('0');
            if (typeof loadPlatformData === 'function') loadPlatformData();
        } else {
            if (window.coraShowToast) window.coraShowToast((res.data && res.data.message) ? res.data.message : 'Failed to reset usage.', 'error');
        }
    }).fail(function() {
        if (window.coraShowToast) window.coraShowToast('Network error while resetting usage.', 'error');
    });
};

window.saveWorkspaceSettings = function() {
    if (!currentManagingWorkspaceId) return;

    const selectedPlan = window.selectedPlan || 'starter';
    const selectedCycle = window.selectedBillingCycle || 'monthly';
    const maxUsers = $('#manage-ws-max-users').val();
    const storageLimitGb = parseInt($('#manage-ws-storage-gb-base').val(), 10) || 2;
    const storageLimitMb = storageLimitGb * 1024;
    const maxEmails = $('#manage-ws-max-emails').val();
    const aiRunsQuota = parseInt($('#manage-ws-ai-runs-base').val(), 10) || 2000;
    const isUnlimited = $('#manage-ws-unlimited-ai').is(':checked');

    const enableLeads = $('#manage-ws-enable-leads').is(':checked');
    const enableClients = $('#manage-ws-enable-clients').is(':checked');
    const enableProperties = $('#manage-ws-enable-properties').is(':checked');
    const enableBookings = $('#manage-ws-enable-bookings').is(':checked');
    const enableLedger = $('#manage-ws-enable-ledger').is(':checked');
    const enableDocuments = $('#manage-ws-enable-documents').is(':checked');

    const saveBtn = $('#manage-ws-save-btn');
    const originalHtml = saveBtn.html();
    saveBtn.prop('disabled', true).html('Saving Configuration...');

    $.post(coraREData.ajaxUrl, {
        action: 'cora_super_update_workspace',
        security: coraREData.ajaxNonce,
        id: currentManagingWorkspaceId,
        plan: selectedPlan,
        billing_cycle: selectedCycle,
        max_users_limit: maxUsers,
        storage_limit_mb: storageLimitMb,
        max_emails_limit: maxEmails,
        ai_runs_quota: aiRunsQuota,
        rag_token_quota: aiRunsQuota * 500,
        is_unlimited: isUnlimited,
        enable_leads: enableLeads,
        enable_clients: enableClients,
        enable_properties: enableProperties,
        enable_bookings: enableBookings,
        enable_ledger: enableLedger,
        enable_documents: enableDocuments
    }, function(res) {
        saveBtn.prop('disabled', false).html(originalHtml);
        if (res.success) {
            if (window.coraShowToast) window.coraShowToast('Workspace subscription & configuration saved successfully.', 'success');
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

<!-- Create Workspace Right-Sliding Drawer / Mobile Bottom Sheet -->
<div id="cora-add-workspace-overlay" onclick="closeCreateWorkspaceDrawer()" class="hidden fixed inset-0 bg-zinc-950/45 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-add-workspace-drawer" class="cora-drawer-panel fixed bottom-0 sm:bottom-auto sm:top-0 right-0 left-0 sm:left-auto w-full sm:w-112 max-h-[90vh] sm:max-h-full sm:h-full bg-white rounded-t-3xl sm:rounded-none border-t sm:border-t-0 sm:border-l border-zinc-200 shadow-2xl z-[9999] transform translate-y-full sm:translate-y-0 sm:translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Drag Handle Indicator for Mobile -->
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto mt-2.5 mb-1 sm:hidden shrink-0"></div>

    <!-- Drawer Header -->
    <div class="px-5 sm:px-6 py-3.5 sm:py-4 border-b border-zinc-100 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-zinc-100 rounded-lg text-zinc-900">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            </span>
            <div>
                <h3 class="text-sm sm:text-base font-bold text-zinc-900">Create Independent Workspace</h3>
                <p class="text-[11px] text-zinc-400 font-mono">app.heycora.in/{{slug}}</p>
            </div>
        </div>
        <button onclick="closeCreateWorkspaceDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Drawer Content Form -->
    <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4">
        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Workspace Name *</label>
            <input type="text" id="new-ws-name" oninput="autoSlugifyWorkspace(this.value)" class="w-full border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900" placeholder="e.g. Apex Realty Studio">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Workspace Slug / URL *</label>
            <div class="flex items-center border border-zinc-200 rounded-xl overflow-hidden bg-white focus-within:border-zinc-400">
                <span class="px-3 py-2.5 text-xs font-mono text-zinc-400 bg-zinc-50 border-r border-zinc-200 shrink-0">app.heycora.in/</span>
                <input type="text" id="new-ws-slug" class="w-full px-3 py-2.5 text-xs font-mono bg-transparent outline-none text-zinc-900" placeholder="apex-realty">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Subscription Plan</label>
            <select id="new-ws-plan" class="w-full border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs bg-white outline-none cursor-pointer text-zinc-900">
                <option value="starter">Starter Plan</option>
                <option value="pro">Pro Plan</option>
                <option value="enterprise" selected>Enterprise Plan</option>
                <option value="beta">Beta Plan</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Industry Profile *</label>
            <select id="new-ws-industry" class="w-full border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs bg-white outline-none cursor-pointer text-zinc-900">
                <option value="real_estate" selected>Real Estate Agency</option>
                <option value="photography_studio">Photography Studio</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 mb-1.5">Owner Account Email</label>
            <input type="email" id="new-ws-owner-email" class="w-full border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900" placeholder="owner@agency.com">
            <p class="text-[11px] text-zinc-400 mt-1">If the email matches an existing user, they will be assigned as workspace owner.</p>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="p-4 sm:p-6 border-t border-zinc-100 bg-zinc-50/50 flex items-center justify-end gap-3 shrink-0">
        <button onclick="closeCreateWorkspaceDrawer()" class="px-4 py-2 text-xs font-bold text-zinc-600 hover:text-zinc-900 transition-colors cursor-pointer">
            Cancel
        </button>
        <button onclick="submitNewWorkspace()" class="px-5 py-2.5 bg-zinc-950 text-white font-bold text-xs rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition-all cursor-pointer shadow-sm">
            Create Workspace
        </button>
    </div>
</div>

<!-- Review Suspension Appeal Right-Sliding Drawer / Mobile Bottom Sheet -->
<div id="cora-appeal-review-overlay" onclick="closeAppealReviewDrawer()" class="hidden fixed inset-0 bg-zinc-950/45 backdrop-blur-xs z-[9990] transition-opacity duration-300"></div>

<div id="cora-appeal-review-drawer" class="cora-drawer-panel fixed bottom-0 sm:bottom-auto sm:top-0 right-0 left-0 sm:left-auto w-full sm:w-120 max-h-[90vh] sm:max-h-full sm:h-full bg-white rounded-t-3xl sm:rounded-none border-t sm:border-t-0 sm:border-l border-zinc-200 shadow-2xl z-[9999] transform translate-y-full sm:translate-y-0 sm:translate-x-full transition-transform duration-300 flex flex-col">
    <!-- Drag Handle Indicator for Mobile -->
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto mt-2.5 mb-1 sm:hidden shrink-0"></div>

    <!-- Header -->
    <div class="px-5 sm:px-6 py-3.5 sm:py-4 border-b border-zinc-100 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-2 bg-zinc-100 text-zinc-700 rounded-lg">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <div>
                <h3 class="text-sm sm:text-base font-bold text-zinc-900">Review Reactivation Appeal</h3>
                <p class="text-[11px] text-zinc-400 font-mono" id="review-appeal-id">appeal_...</p>
            </div>
        </div>
        <button onclick="closeAppealReviewDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4">
        <div class="p-4 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-3">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Account Email</span>
                <p class="text-xs font-bold text-zinc-900" id="review-appeal-email">—</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Workspace / Agency</span>
                <p class="text-xs font-bold text-zinc-900" id="review-appeal-workspace">—</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Contact Phone</span>
                <p class="text-xs font-bold text-zinc-900" id="review-appeal-phone">—</p>
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
            <textarea id="review-appeal-notes" rows="3" class="w-full border border-zinc-200 rounded-xl p-3 text-xs bg-white outline-none text-zinc-900" placeholder="Optional explanation included in the confirmation email..."></textarea>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-4 sm:p-6 border-t border-zinc-100 bg-zinc-50/50 flex items-center justify-between gap-3 shrink-0">
        <button onclick="processAppealAction('decline')" class="px-4 py-2 bg-zinc-100 text-zinc-700 border border-zinc-200 font-bold text-xs rounded-xl hover:bg-zinc-200 cursor-pointer transition-all">
            Decline Appeal
        </button>
        <button onclick="processAppealAction('approve')" class="px-5 py-2.5 bg-zinc-950 text-white font-bold text-xs rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition-all cursor-pointer shadow-sm flex items-center gap-1.5">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Approve & Reactivate
        </button>
    </div>
</div>

<!-- Manage Workspace Full-Width Bottom-Up Slide Sheet Styles -->
<style>
#cora-manage-workspace-drawer {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100vw !important;
    max-width: 100% !important;
    height: 80vh !important;
    max-height: 90vh !important;
    background: #ffffff !important;
    border-top: 1px solid #e4e4e7 !important;
    border-top-left-radius: 1.5rem !important;
    border-top-right-radius: 1.5rem !important;
    box-shadow: 0 -20px 45px -10px rgba(0, 0, 0, 0.25) !important;
    z-index: 99999 !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    transform: translateY(100%) !important;
    transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-sizing: border-box !important;
}
#cora-manage-workspace-drawer.cora-sheet-active {
    transform: translateY(0) !important;
}

#cora-manage-workspace-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(9, 9, 11, 0.45) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    z-index: 99998 !important;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease !important;
}
#cora-manage-workspace-overlay.cora-sheet-active {
    opacity: 1 !important;
    pointer-events: auto !important;
}

@media (max-width: 639px) {
    #cora-add-workspace-drawer.open,
    #cora-appeal-review-drawer.open {
        transform: translateY(0) !important;
    }
}
@media (min-width: 640px) {
    #cora-add-workspace-drawer.open,
    #cora-appeal-review-drawer.open {
        transform: translateX(0) !important;
    }
}

.cora-sheet-body-grid {
    flex: 1 1 0% !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 1.25rem 1.5rem !important;
    background: #fafafa !important;
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) !important;
    gap: 1.25rem !important;
    box-sizing: border-box !important;
    width: 100% !important;
}

@media (min-width: 900px) {
    .cora-sheet-body-grid {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 0.95fr) !important;
        gap: 1.25rem !important;
        align-items: start !important;
    }
}

/* Plan Cards Architecture & Selection State */
.plan-tier-card {
    border: 1px solid #e4e4e7 !important;
    background: #ffffff !important;
    border-radius: 1rem !important;
    padding: 0.85rem 1rem !important;
    cursor: pointer !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    box-sizing: border-box !important;
}
.plan-tier-card:hover {
    border-color: #71717a !important;
}
.plan-tier-card .plan-title {
    color: #09090b !important;
    font-weight: 800 !important;
    font-size: 0.8125rem !important;
}
.plan-tier-card .plan-desc {
    color: #71717a !important;
    font-size: 0.6875rem !important;
    line-height: 1.25 !important;
}
.plan-tier-card .plan-specs {
    color: #a1a1aa !important;
    font-size: 0.6875rem !important;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
}
.plan-tier-card .plan-price {
    color: #09090b !important;
    font-weight: 800 !important;
    font-size: 1.0625rem !important;
}
.plan-tier-card .plan-price span {
    color: #71717a !important;
    font-size: 0.6875rem !important;
    font-weight: 400 !important;
}
.plan-tier-card .plan-sub {
    color: #71717a !important;
    font-size: 0.625rem !important;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
}
.plan-tier-card .plan-check-icon {
    display: none !important;
}

/* Active / Selected Plan State */
.plan-tier-card.selected-plan {
    background: #09090b !important;
    border-color: #09090b !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18) !important;
}
.plan-tier-card.selected-plan .plan-title {
    color: #ffffff !important;
}
.plan-tier-card.selected-plan .plan-desc {
    color: #d4d4d8 !important;
}
.plan-tier-card.selected-plan .plan-specs {
    color: #a1a1aa !important;
}
.plan-tier-card.selected-plan .plan-price {
    color: #ffffff !important;
}
.plan-tier-card.selected-plan .plan-price span {
    color: #a1a1aa !important;
}
.plan-tier-card.selected-plan .plan-sub {
    color: #a1a1aa !important;
}
.plan-tier-card.selected-plan .plan-check-icon {
    display: inline-flex !important;
}

.cora-runs-metrics-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 0.25rem !important;
}

.cora-storage-metrics-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 0.25rem !important;
}

.cora-seats-grid {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0.625rem !important;
}
</style>

<!-- Manage Workspace Full-Width Bottom-Up Slide Sheet Overlay -->
<div id="cora-manage-workspace-overlay" onclick="closeManageWorkspaceDrawer()"></div>

<div id="cora-manage-workspace-drawer">
    <!-- Sheet Drag Handle Indicator -->
    <div class="pt-2.5 pb-1 flex justify-center shrink-0 cursor-pointer" onclick="closeManageWorkspaceDrawer()">
        <div class="w-12 h-1.5 bg-zinc-300 rounded-full hover:bg-zinc-400 transition-colors"></div>
    </div>

    <!-- Header Strip -->
    <div class="px-6 py-3.5 border-b border-zinc-150 flex items-center justify-between shrink-0 bg-white">
        <div class="flex items-center gap-3">
            <span class="p-2 bg-zinc-100 rounded-xl text-zinc-900 shrink-0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </span>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-base font-extrabold text-zinc-900" id="manage-ws-title">Manage Workspace</h3>
                    <span id="manage-ws-plan-badge" class="px-2 py-0.5 text-[9.5px] font-bold rounded-md bg-zinc-950 text-white font-mono uppercase tracking-wider">Professional</span>
                    <span id="manage-ws-cycle-badge" class="px-2 py-0.5 text-[9.5px] font-bold rounded-md bg-zinc-100 text-zinc-700 font-mono">Monthly</span>
                </div>
                <p class="text-xs text-zinc-400 font-mono mt-0.5" id="manage-ws-slug-info">app.heycora.in/slug</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="closeManageWorkspaceDrawer()" class="p-2 text-zinc-400 hover:text-zinc-700 rounded-xl hover:bg-zinc-100 transition-colors cursor-pointer" aria-label="Close">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
    </div>

    <!-- Body: Multi-Column Desktop Grid Layout -->
    <div class="cora-sheet-body-grid">
        <!-- ================================================================= -->
        <!-- COLUMN 1: Official Plan Matrix & Billing Cycle -->
        <!-- ================================================================= -->
        <div class="space-y-4">
            <!-- Header & Monthly/Annual Cycle Switch -->
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900">Operating Plan Tier</h4>
                    <p class="text-[11px] text-zinc-400">heycora.in/pricing official matrix</p>
                </div>

                <!-- Monthly vs Annual Toggle Switch -->
                <div class="flex items-center p-1 bg-zinc-100 rounded-xl border border-zinc-200/80">
                    <button type="button" id="drawer-cycle-btn-monthly" onclick="setDrawerBillingCycle('monthly')" class="px-2.5 py-1 text-xs rounded-lg transition-all cursor-pointer bg-zinc-950 text-white shadow-xs font-bold">
                        Monthly
                    </button>
                    <button type="button" id="drawer-cycle-btn-annual" onclick="setDrawerBillingCycle('annual')" class="px-2.5 py-1 text-xs rounded-lg transition-all cursor-pointer text-zinc-600 hover:text-zinc-900 font-medium flex items-center gap-1">
                        <span>Annual</span>
                        <span class="bg-emerald-600 text-white font-bold px-1.5 py-0.5 rounded text-[8.5px] uppercase tracking-wider">2 Mo. Free</span>
                    </button>
                </div>
            </div>

            <input type="hidden" id="manage-ws-plan" value="professional">

            <!-- 4 Plan Cards Matrix -->
            <div class="space-y-2.5">
                <!-- 1. Starter Plan Card -->
                <div id="plan-tier-starter" onclick="setDrawerPlan('starter')" class="plan-tier-card group">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            </span>
                            <span class="plan-title">Starter</span>
                        </div>
                        <p class="plan-desc">For independent operators establishing their brand</p>
                        <div class="plan-specs pt-1">2K AI Runs · 2 GB Storage · 1 Admin Seat</div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="plan-price" id="plan-price-starter">₹999<span>/mo</span></div>
                        <div class="plan-sub" id="plan-sub-starter">Billed monthly</div>
                        <span id="plan-check-starter" class="plan-check-icon mt-1 inline-flex items-center justify-center w-4 h-4 rounded-full bg-white text-zinc-950 text-[9px] font-bold">✓</span>
                    </div>
                </div>

                <!-- 2. Professional Plan Card (Recommended) -->
                <div id="plan-tier-pro" onclick="setDrawerPlan('professional')" class="plan-tier-card group relative overflow-hidden">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-lg bg-zinc-100 text-zinc-900 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            </span>
                            <span class="plan-title">Professional</span>
                            <span class="px-1.5 py-0.5 rounded text-[8.5px] font-bold bg-zinc-900 text-white uppercase tracking-wider">Recommended</span>
                        </div>
                        <p class="plan-desc">Autonomous backbone with advanced AI & WhatsApp</p>
                        <div class="plan-specs pt-1">10K AI Runs · 10 GB Storage · 5 Team Seats · UPI QR</div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="plan-price" id="plan-price-pro">₹1,999<span>/mo</span></div>
                        <div class="plan-sub" id="plan-sub-pro">Billed monthly</div>
                        <span id="plan-check-pro" class="plan-check-icon mt-1 inline-flex items-center justify-center w-4 h-4 rounded-full bg-white text-zinc-950 text-[9px] font-bold">✓</span>
                    </div>
                </div>

                <!-- 3. Scale Plan Card -->
                <div id="plan-tier-scale" onclick="setDrawerPlan('scale')" class="plan-tier-card group">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                            </span>
                            <span class="plan-title">Scale</span>
                        </div>
                        <p class="plan-desc">High-throughput infrastructure for agencies & teams</p>
                        <div class="plan-specs pt-1">25K AI Runs · 50 GB Storage · 15 Team Seats · 5K Emails</div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="plan-price" id="plan-price-scale">₹2,999<span>/mo</span></div>
                        <div class="plan-sub" id="plan-sub-scale">Billed monthly</div>
                        <span id="plan-check-scale" class="plan-check-icon mt-1 inline-flex items-center justify-center w-4 h-4 rounded-full bg-white text-zinc-950 text-[9px] font-bold">✓</span>
                    </div>
                </div>

                <!-- 4. India Only Plan Card (Annual Commitment Only) -->
                <div id="plan-tier-india-only" onclick="setDrawerPlan('india_only')" class="plan-tier-card group relative">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            </span>
                            <span class="plan-title">India Only Plan</span>
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-zinc-100 text-zinc-700 border border-zinc-200 uppercase tracking-wider">Annual Only</span>
                        </div>
                        <p class="plan-desc">Subsidized operating system for Indian solopreneurs</p>
                        <div class="plan-specs pt-1">3.5K AI Runs · 2 GB Storage · Free .in · GST & UPI QR</div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="plan-price" id="plan-price-india">₹499<span>/mo</span></div>
                        <div class="plan-sub">₹5,988 / 1-yr commit</div>
                        <span id="plan-check-india-only" class="plan-check-icon mt-1 inline-flex items-center justify-center w-4 h-4 rounded-full bg-white text-zinc-950 text-[9px] font-bold">✓</span>
                        <div id="plan-india-locked-tag" class="hidden text-[8.5px] text-zinc-600 font-medium mt-0.5">Click switches to Annual</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- COLUMN 2: Resource Quotas, Top-Ups & Add-Ons -->
        <!-- ================================================================= -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900">Capacity & Add-Ons</h4>
                <span class="text-[10px] font-mono text-zinc-400">₹0.10/Run · ₹10/GB/mo</span>
            </div>

            <!-- 1. AI Runs & Credits Quota Card -->
            <div class="p-4 border border-zinc-200 rounded-2xl bg-white space-y-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-zinc-950 text-white rounded-lg">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        </span>
                        <div>
                            <h5 class="text-xs font-bold text-zinc-900">AI Runs & Credits Quota</h5>
                            <p class="text-[10px] text-zinc-400">₹100 = 1,000 AI Runs (GEO audit, Prompts, RAG)</p>
                        </div>
                    </div>
                    <div id="manage-ws-godmode-pill" class="hidden">
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200 font-mono">∞ Unlimited</span>
                    </div>
                </div>

                <!-- Live Runs Telemetry -->
                <div class="cora-runs-metrics-grid p-2 bg-zinc-50 rounded-xl text-center border border-zinc-150">
                    <div>
                        <div class="text-[8.5px] text-zinc-400 font-medium">Used</div>
                        <div id="manage-ws-ai-used-display" class="font-bold font-mono text-[11px] text-zinc-900">0</div>
                    </div>
                    <div class="border-l border-zinc-200/80">
                        <div class="text-[8.5px] text-zinc-400 font-medium">Base</div>
                        <div id="manage-ws-ai-base-display" class="font-bold font-mono text-[11px] text-zinc-900">2,000</div>
                    </div>
                    <div class="border-l border-zinc-200/80">
                        <div class="text-[8.5px] text-zinc-400 font-medium">Recurring</div>
                        <div id="manage-ws-ai-recurring-display" class="font-bold font-mono text-[11px] text-zinc-900">+0</div>
                    </div>
                    <div class="border-l border-zinc-200/80">
                        <div class="text-[8.5px] text-zinc-400 font-medium">Bonus</div>
                        <div id="manage-ws-ai-bonus-display" class="font-bold font-mono text-[11px] text-emerald-700">+0</div>
                    </div>
                </div>

                <!-- Monthly Plan Base Runs Setting -->
                <div class="pt-1">
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[10.5px] font-bold text-zinc-700">Monthly Baseline Runs</label>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="setDrawerAiRunsPreset(2000)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-700">2K</button>
                            <button type="button" onclick="setDrawerAiRunsPreset(10000)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-700">10K</button>
                            <button type="button" onclick="setDrawerAiRunsPreset(25000)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-700">25K</button>
                        </div>
                    </div>
                    <input type="number" id="manage-ws-ai-runs-base" min="100" step="500" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs bg-zinc-50 focus:bg-white focus:border-zinc-400 outline-none text-zinc-900 font-mono" placeholder="2000">
                </div>

                <!-- Top-Up Injection Box -->
                <div class="p-3 bg-zinc-50/70 border border-dashed border-zinc-300 rounded-xl space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10.5px] font-bold text-zinc-800">Top-Up / Add Runs</span>
                        <!-- Segmented Mode -->
                        <div class="flex items-center p-0.5 bg-zinc-200/70 rounded-lg text-[9.5px]">
                            <button type="button" id="ai-mode-one-time-btn" onclick="setAiTopUpMode('one_time')" class="px-2 py-0.5 rounded-md bg-zinc-950 text-white font-bold transition-all cursor-pointer">One-Time</button>
                            <button type="button" id="ai-mode-recurring-btn" onclick="setAiTopUpMode('recurring')" class="px-2 py-0.5 rounded-md text-zinc-600 hover:text-zinc-900 transition-all cursor-pointer">Recurring</button>
                        </div>
                    </div>

                    <!-- Presets -->
                    <div class="flex items-center gap-1 flex-wrap">
                        <button type="button" onclick="setAiTopUpPreset(1000)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100">+1,000 (₹100)</button>
                        <button type="button" onclick="setAiTopUpPreset(2500)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100">+2,500 (₹250)</button>
                        <button type="button" onclick="setAiTopUpPreset(5000)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100">+5,000 (₹500)</button>
                        <button type="button" onclick="setAiTopUpPreset(10000)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100">+10K (₹1,000)</button>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <input type="number" id="manage-ws-ai-topup-input" oninput="updateAiCostCalc(this.value)" step="500" min="100" class="flex-1 border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900 font-mono" placeholder="e.g. 1000">
                        <span id="manage-ws-ai-topup-cost" class="px-2 py-1.5 bg-white border border-zinc-200 rounded-lg text-xs font-mono font-bold text-zinc-800 shrink-0">= ₹100</span>
                        <button type="button" id="manage-ws-inject-ai-btn" onclick="injectAiRunsDrawer()" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition-all cursor-pointer shrink-0 shadow-2xs active:scale-95">
                            <span id="manage-ws-inject-ai-btn-text">Add Top-Up</span>
                        </button>
                    </div>
                    <p class="text-[9.5px] text-zinc-400 leading-tight" id="manage-ws-ai-mode-desc">One-time top-ups are added to current quota immediately.</p>
                </div>

                <!-- Godmode & Reset Bar -->
                <div class="pt-1 flex items-center justify-between text-xs">
                    <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                        <input type="checkbox" id="manage-ws-unlimited-ai" onchange="toggleDrawerGodmode(this.checked)" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                        <span class="font-bold text-zinc-800 text-[10.5px]">Unlimited Godmode</span>
                    </label>
                    <button type="button" onclick="resetTokenUsageDrawer()" class="text-[10px] text-zinc-400 hover:text-red-600 font-medium transition-colors cursor-pointer">
                        Reset Usage
                    </button>
                </div>
            </div>

            <!-- 2. Storage Quota & Expansion Card -->
            <div class="p-4 border border-zinc-200 rounded-2xl bg-white space-y-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-zinc-950 text-white rounded-lg">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                        </span>
                        <div>
                            <h5 class="text-xs font-bold text-zinc-900">Storage Quota & Expansion</h5>
                            <p class="text-[10px] text-zinc-400">₹10 / GB / mo flat expansion rate</p>
                        </div>
                    </div>
                </div>

                <!-- Live Storage Telemetry -->
                <div class="cora-storage-metrics-grid p-2 bg-zinc-50 rounded-xl text-center border border-zinc-150">
                    <div>
                        <div class="text-[8.5px] text-zinc-400 font-medium">Used</div>
                        <div id="manage-ws-storage-used-display" class="font-bold font-mono text-[11px] text-zinc-900">0.0 MB</div>
                    </div>
                    <div class="border-x border-zinc-200/80">
                        <div class="text-[8.5px] text-zinc-400 font-medium">Base</div>
                        <div id="manage-ws-storage-base-display" class="font-bold font-mono text-[11px] text-zinc-900">2 GB</div>
                    </div>
                    <div>
                        <div class="text-[8.5px] text-zinc-400 font-medium">Add-on</div>
                        <div id="manage-ws-storage-recurring-display" class="font-bold font-mono text-[11px] text-zinc-900">+0 GB</div>
                    </div>
                </div>

                <!-- Monthly Base Storage Setting -->
                <div class="pt-1">
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[10.5px] font-bold text-zinc-700">Storage Ceiling (GB)</label>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="setDrawerStoragePresetGb(2)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-700">2 GB</button>
                            <button type="button" onclick="setDrawerStoragePresetGb(10)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-700">10 GB</button>
                            <button type="button" onclick="setDrawerStoragePresetGb(50)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-700">50 GB</button>
                        </div>
                    </div>
                    <input type="number" id="manage-ws-storage-gb-base" min="1" step="1" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs bg-zinc-50 focus:bg-white focus:border-zinc-400 outline-none text-zinc-900 font-mono" placeholder="2">
                </div>

                <!-- Storage Boost Box -->
                <div class="p-3 bg-zinc-50/70 border border-dashed border-zinc-300 rounded-xl space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10.5px] font-bold text-zinc-800">Add Extra Storage</span>
                        <!-- Segmented Mode -->
                        <div class="flex items-center p-0.5 bg-zinc-200/70 rounded-lg text-[9.5px]">
                            <button type="button" id="storage-mode-one-time-btn" onclick="setStorageBoostMode('one_time')" class="px-2 py-0.5 rounded-md bg-zinc-950 text-white font-bold transition-all cursor-pointer">One-Time</button>
                            <button type="button" id="storage-mode-recurring-btn" onclick="setStorageBoostMode('recurring')" class="px-2 py-0.5 rounded-md text-zinc-600 hover:text-zinc-900 transition-all cursor-pointer">Recurring</button>
                        </div>
                    </div>

                    <!-- Presets -->
                    <div class="flex items-center gap-1 flex-wrap">
                        <button type="button" onclick="setStorageBoostPreset(1)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100">+1 GB (₹10)</button>
                        <button type="button" onclick="setStorageBoostPreset(5)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100">+5 GB (₹50)</button>
                        <button type="button" onclick="setStorageBoostPreset(10)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100">+10 GB (₹100)</button>
                        <button type="button" onclick="setStorageBoostPreset(20)" class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100">+20 GB (₹200)</button>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <input type="number" id="manage-ws-storage-boost-input" oninput="updateStorageCostCalc(this.value)" step="1" min="1" class="flex-1 border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:border-zinc-400 outline-none text-zinc-900 font-mono" placeholder="e.g. 5">
                        <span id="manage-ws-storage-boost-cost" class="px-2 py-1.5 bg-white border border-zinc-200 rounded-lg text-xs font-mono font-bold text-zinc-800 shrink-0">= ₹10</span>
                        <button type="button" id="manage-ws-boost-storage-btn" onclick="injectExtraStorageDrawer()" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition-all cursor-pointer shrink-0 shadow-2xs active:scale-95">
                            <span id="manage-ws-boost-storage-btn-text">Add Storage</span>
                        </button>
                    </div>
                    <p class="text-[9.5px] text-zinc-400 leading-tight" id="manage-ws-storage-mode-desc">One-time extra storage provides an immediate temporary expansion.</p>
                </div>
            </div>

            <!-- 3. Users & Emails Row -->
            <div class="cora-seats-grid">
                <div class="p-3 border border-zinc-200 rounded-xl bg-white">
                    <label class="text-[10px] font-bold text-zinc-700 block mb-1">Max Users / Seats</label>
                    <input type="number" id="manage-ws-max-users" min="1" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1 text-xs bg-zinc-50 font-mono outline-none" placeholder="1">
                </div>
                <div class="p-3 border border-zinc-200 rounded-xl bg-white">
                    <label class="text-[10px] font-bold text-zinc-700 block mb-1">Max Emails / Mo</label>
                    <input type="number" id="manage-ws-max-emails" min="0" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1 text-xs bg-zinc-50 font-mono outline-none" placeholder="200">
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- COLUMN 3: Live Upcoming Invoice & Feature Flags -->
        <!-- ================================================================= -->
        <div class="space-y-4">
            <!-- 1. Live Upcoming Monthly / Annual Invoice Simulator -->
            <div class="p-4 border border-zinc-200 rounded-2xl bg-white space-y-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-zinc-900 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        Upcoming Invoice
                    </span>
                    <span id="invoice-cycle-note" class="text-[9.5px] font-mono text-zinc-400">Next Renewal</span>
                </div>

                <div class="bg-zinc-50 border border-zinc-200/80 rounded-xl p-3 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-zinc-600">
                        <span class="text-[11px]" id="invoice-plan-name">Starter Plan (Monthly)</span>
                        <span id="invoice-base-price" class="font-mono font-bold text-zinc-900">₹999</span>
                    </div>
                    <div class="flex items-center justify-between text-zinc-600">
                        <span class="text-[10.5px]">AI Runs Add-on (<span id="invoice-ai-runs-count">0</span> runs)</span>
                        <span id="invoice-ai-price" class="font-mono text-zinc-800">+₹0</span>
                    </div>
                    <div class="flex items-center justify-between text-zinc-600">
                        <span class="text-[10.5px]">Storage Add-on (<span id="invoice-storage-gb-count">0</span> GB)</span>
                        <span id="invoice-storage-price" class="font-mono text-zinc-800">+₹0</span>
                    </div>
                    <div class="pt-2 border-t border-zinc-200/60 flex items-center justify-between text-zinc-500 text-[10.5px]">
                        <span>Subtotal</span>
                        <span id="invoice-subtotal-price" class="font-mono font-medium text-zinc-700">₹999</span>
                    </div>
                    <div class="flex items-center justify-between text-zinc-500 text-[10.5px]">
                        <span>18% GST (CGST + SGST)</span>
                        <span id="invoice-gst-price" class="font-mono font-medium text-zinc-700">₹179.82</span>
                    </div>
                    <div class="pt-2 border-t border-zinc-200 flex items-center justify-between font-extrabold text-zinc-900">
                        <span class="text-[11.5px]">Total Due</span>
                        <span id="invoice-total-price" class="font-mono text-emerald-700 text-sm">₹1,178.82/mo</span>
                    </div>
                </div>
            </div>

            <!-- 2. Feature Governance Flags -->
            <div class="p-4 border border-zinc-200 rounded-2xl bg-white space-y-2.5 shadow-2xs">
                <h5 class="text-[11px] font-bold uppercase tracking-wider text-zinc-900">Module Governance</h5>
                
                <div class="space-y-1.5 text-xs">
                    <!-- Leads -->
                    <label class="flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 cursor-pointer">
                        <span class="text-zinc-700 font-medium">Leads Pipeline</span>
                        <input type="checkbox" id="manage-ws-enable-leads" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                    </label>
                    <!-- Clients -->
                    <label class="flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 cursor-pointer">
                        <span class="text-zinc-700 font-medium">Clients Directory</span>
                        <input type="checkbox" id="manage-ws-enable-clients" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                    </label>
                    <!-- Properties -->
                    <label class="flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 cursor-pointer">
                        <span class="text-zinc-700 font-medium">Portfolios & Listings</span>
                        <input type="checkbox" id="manage-ws-enable-properties" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                    </label>
                    <!-- Bookings -->
                    <label class="flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 cursor-pointer">
                        <span class="text-zinc-700 font-medium">Booking Calendar</span>
                        <input type="checkbox" id="manage-ws-enable-bookings" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                    </label>
                    <!-- Ledger -->
                    <label class="flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 cursor-pointer">
                        <span class="text-zinc-700 font-medium">GST Invoicing & Ledger</span>
                        <input type="checkbox" id="manage-ws-enable-ledger" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                    </label>
                    <!-- Vault -->
                    <label class="flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 cursor-pointer">
                        <span class="text-zinc-700 font-medium">Document Vault & E-Sign</span>
                        <input type="checkbox" id="manage-ws-enable-documents" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Bottom Sheet Footer Actions -->
    <div class="px-6 py-3.5 border-t border-zinc-200 bg-white flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2 text-xs text-zinc-500">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-medium">Direct billing synchronization with next payment cycle</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="closeManageWorkspaceDrawer()" class="px-4 py-2 text-xs font-bold text-zinc-600 hover:text-zinc-900 transition-colors cursor-pointer">
                Cancel
            </button>
            <button id="manage-ws-save-btn" onclick="saveWorkspaceSettings()" class="px-6 py-2.5 bg-zinc-950 text-white font-bold text-xs rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition-all cursor-pointer shadow-sm flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Save All Settings
            </button>
        </div>
    </div>
</div>


