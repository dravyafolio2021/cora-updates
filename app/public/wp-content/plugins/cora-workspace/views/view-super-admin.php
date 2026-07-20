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
        <button class="cora-sub-tab active pb-2 border-b-2 border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50 cursor-pointer focus:outline-none" data-target="tab-super-workspaces">
            Workspaces
        </button>
        <button class="cora-sub-tab pb-2 border-b-2 border-transparent hover:text-zinc-900 dark:hover:text-zinc-200 text-zinc-500 cursor-pointer focus:outline-none" data-target="tab-super-users">
            Users
        </button>
    </div>

    <!-- TAB 1: WORKSPACES -->
    <div id="tab-super-workspaces" class="cora-tab-content space-y-4">
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
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Owner (email)</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Created Date</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="workspaces-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-zinc-450 dark:text-zinc-500">Loading workspaces...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: USERS -->
    <div id="tab-super-users" class="cora-tab-content space-y-4 hidden">
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
</div>

<script>
// Expose dynamic labels directly to JS
const coraRoleLabels = <?php echo json_encode( $roles_list ); ?>;

jQuery(document).ready(function($) {
    let rawWorkspaces = [];
    let rawUsers = [];

    // Tab Navigation Logic
    $('.cora-sub-tabs button').on('click', function() {
        const target = $(this).data('target');
        if (!target) return;
        
        $('.cora-sub-tabs button').removeClass('active border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50')
                                 .addClass('border-transparent text-zinc-500');
        $(this).addClass('active border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50')
               .removeClass('border-transparent text-zinc-500');
        
        $('.cora-tab-content').addClass('hidden');
        $('#' + target).removeClass('hidden');
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
            // Replace dashes with slashes for unified cross-browser Safari support
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
    }

    // Render Workspaces Tab Content
    window.renderWorkspaces = function() {
        const query = $('#workspace-search').val().toLowerCase();
        const planFilter = $('#workspace-filter-plan').val();
        const statusFilter = $('#workspace-filter-status').val();

        const filtered = rawWorkspaces.filter(ws => {
            const matchesQuery = !query || 
                (ws.name || '').toLowerCase().includes(query) || 
                (ws.slug || '').toLowerCase().includes(query) || 
                (ws.owner_email || '').toLowerCase().includes(query);
            
            const matchesPlan = !planFilter || ws.plan === planFilter;
            const matchesStatus = !statusFilter || ws.status === statusFilter;
            
            return matchesQuery && matchesPlan && matchesStatus;
        });

        $('#workspace-count-badge').text(`${filtered.length} workspace${filtered.length === 1 ? '' : 's'}`);

        if (filtered.length === 0) {
            $('#workspaces-table-body').html('<tr><td colspan="7" class="px-5 py-8 text-center text-zinc-400 dark:text-zinc-500 bg-zinc-50/20 dark:bg-zinc-900/10">No workspaces matching filters found.</td></tr>');
            return;
        }

        let html = '';
        filtered.forEach(ws => {
            const planBadge = ws.plan 
                ? `<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">${escapeHtml(ws.plan)}</span>` 
                : '<span class="text-zinc-400">—</span>';
            
            const statusBadge = ws.status === 'active'
                ? '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 select-none">Active</span>'
                : '<span class="px-2 py-0.5 text-[9px] font-bold rounded-md bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 select-none">Suspended</span>';

            const toggleLabel = ws.status === 'active' ? 'Suspend' : 'Activate';
            const toggleClass = ws.status === 'active'
                ? 'text-red-600 hover:text-red-700 border-zinc-200 hover:bg-red-50 dark:border-zinc-800 dark:hover:bg-red-950/20'
                : 'text-emerald-600 hover:text-emerald-700 border-zinc-200 hover:bg-emerald-50 dark:border-zinc-800 dark:hover:bg-emerald-950/20';

            html += `
                <tr class="hover:bg-zinc-50/20 dark:hover:bg-zinc-800/15 transition-colors">
                    <td class="px-5 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">${escapeHtml(ws.name)}</td>
                    <td class="px-5 py-3.5 text-zinc-550 dark:text-zinc-400 font-mono text-[11px]">${escapeHtml(ws.slug)}</td>
                    <td class="px-5 py-3.5">${planBadge}</td>
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
            status: newStatus,
            plan: ws.plan
        }, function(res) {
            if (res.success) {
                ws.status = newStatus;
                renderWorkspaces();
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || 'Workspace status updated successfully.', 'success');
                }
            } else {
                const errorMsg = res.data || 'Failed to toggle workspace status.';
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while toggling status.', 'error');
            }
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
            status: ws.status,
            plan: newPlan
        }, function(res) {
            if (res.success) {
                ws.plan = newPlan;
                renderWorkspaces();
                if (window.coraShowToast) {
                    window.coraShowToast(res.data.message || 'Workspace plan changed successfully.', 'success');
                }
            } else {
                const errorMsg = res.data || 'Failed to update workspace plan.';
                if (window.coraShowToast) {
                    window.coraShowToast(errorMsg, 'error');
                }
                // Reset select dropdown value to actual model value
                renderWorkspaces();
            }
        }).fail(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Network error while changing plan.', 'error');
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

    // Run dynamic retrieval on mount
    loadPlatformData();
});

window.openCreateWorkspaceDrawer = function() {
    $('#new-ws-name').val('');
    $('#new-ws-slug').val('');
    $('#new-ws-owner-email').val('');
    $('#new-ws-plan').val('starter');
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
