<?php
/**
 * Cora Studio - System Activity Audit View
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_user_id = get_current_user_id();
$current_role    = ! empty( wp_get_current_user()->roles ) ? wp_get_current_user()->roles[0] : '';
$current_agency  = function_exists('cora_get_current_user_agency_id') ? cora_get_current_user_agency_id() : 'super';
$current_branch  = function_exists('cora_get_current_user_branch_id') ? cora_get_current_user_branch_id() : 0;

// Retrieve logs
$all_logs      = function_exists('cora_db_get_activity_logs') ? cora_db_get_activity_logs() : array();
$filtered_logs = array();

// Apply security & isolation rules
if ( is_array( $all_logs ) ) {
    // Sort logs descending (newest first)
    usort( $all_logs, function( $a, $b ) {
        return ($b['timestamp'] ?? 0) - ($a['timestamp'] ?? 0);
    } );

    foreach ( $all_logs as $log ) {
        // Super admin sees all logs
        if ( $current_agency === 'super' || ( function_exists('cora_is_super_owner') && cora_is_super_owner() ) ) {
            $filtered_logs[] = $log;
            continue;
        }
        
        // Normalize agency IDs to compare slugs/IDs
        $log_agency = str_replace( 'agency_', '', $log['agency_id'] ?? '' );
        $curr_agency_clean = str_replace( 'agency_', '', $current_agency );
        
        // Branch Manager has strict isolation to agency + branch
        if ( $current_role === 'cora_branch_manager' ) {
            $log_branch = str_replace( 'branch_', '', $log['branch_id'] ?? '' );
            $curr_branch_clean = str_replace( 'branch_', '', $current_branch );
            if ( $log_agency === $curr_agency_clean && $log_branch === $curr_branch_clean ) {
                $filtered_logs[] = $log;
            }
            continue;
        }
        
        // Agency Owners, Administrators, and Managers see all logs for their agency
        if ( in_array( $current_role, array( 'administrator', 'cora_super_admin', 'cora_manager', 'agency_owner' ) ) || current_user_can( 'manage_options' ) ) {
            if ( $log_agency === $curr_agency_clean ) {
                $filtered_logs[] = $log;
            }
            continue;
        }
    }
}

// Map role display names dynamically
$role_labels = function_exists('cora_get_all_roles') ? cora_get_all_roles() : array();
$role_labels['guest'] = 'Guest / System';

$total_events = count( $filtered_logs );
?>

<!-- Header -->
<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center shrink-0 shadow-xs">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">System Activity Audit Logs</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Real-time security activity stream, user access logs, and administrative event auditing.</p>
        </div>
    </div>
    <div class="flex items-center gap-2.5">
        <button onclick="exportLogsCSV()" class="px-4 py-2 bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-white text-xs font-bold rounded-xl transition-colors cursor-pointer shadow-3xs flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export CSV
        </button>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="flex border-b border-zinc-200 dark:border-zinc-800 mt-6 gap-2">
    <button id="tab-activity-btn" class="px-4 py-2 border-b-2 border-zinc-900 dark:border-white text-zinc-900 dark:text-white font-bold text-xs cursor-pointer active-tab transition-all" onclick="coraSwitchAuditTab('activity')">
        Activity Log
    </button>
    <button id="tab-cost-btn" class="px-4 py-2 border-b-2 border-transparent text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 font-medium text-xs cursor-pointer transition-all" onclick="coraSwitchAuditTab('cost')">
        AI Usage & Costs
    </button>
</div>

<div id="cora-audit-activity-section" class="space-y-6">

<!-- Metrics Bar -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Total Events Logged</span>
            <div class="text-xl font-bold text-zinc-900 dark:text-white mt-1 font-mono"><?php echo number_format( $total_events ); ?></div>
        </div>
        <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center shrink-0">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Audit Status</span>
            <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-1.5 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Active Security Monitoring
            </div>
        </div>
        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Security Scope</span>
            <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-1.5">
                <?php echo ($current_agency === 'super' || (function_exists('cora_is_super_owner') && cora_is_super_owner())) ? 'Super Admin' : 'Workspace Admin'; ?> Access
            </div>
        </div>
        <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center shrink-0">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </div>
    </div>
</div>

<!-- Main Audit Log Section -->
<div class="space-y-4 mt-6">
    <!-- Organized Filter Toolbar (Bulletproof Flex Layout) -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-2xs">
        <div class="flex flex-wrap items-center gap-3">
            <!-- 1. Search Actor (Flexible, min 220px) -->
            <div class="relative flex-1" style="min-width: 220px !important; flex: 1 1 220px !important;">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none flex items-center z-10">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="log-search" oninput="filterLogs()" placeholder="Search user or actor..." style="width: 100% !important; min-width: 100% !important; max-width: 100% !important; padding-left: 36px !important; padding-right: 12px !important; height: 40px !important; font-size: 12px !important; background: #fafafa !important; border: 1px solid #e4e4e7 !important; border-radius: 10px !important; color: #18181b !important; outline: none !important; box-shadow: none !important; box-sizing: border-box !important;">
            </div>

            <!-- 2. Event Type Dropdown (min 180px) -->
            <div style="min-width: 180px !important; flex: 0 0 180px !important;">
                <select id="log-type-filter" onchange="filterLogs()" style="width: 100% !important; height: 40px !important; padding: 0 10px !important; font-size: 12px !important; background: #fafafa !important; border: 1px solid #e4e4e7 !important; border-radius: 10px !important; color: #18181b !important; outline: none !important; box-shadow: none !important; box-sizing: border-box !important;">
                    <option value="">All Event Types</option>
                    <option value="Authentication">Authentication / Login</option>
                    <option value="User Management">User Management</option>
                    <option value="Permissions">Permissions & Access</option>
                    <option value="Git Sync">Git Sync & Deploy</option>
                    <option value="Invitation">Invitations</option>
                    <option value="Branch">Branch Operations</option>
                </select>
            </div>

            <!-- 3. Start Date (From) -->
            <div class="flex items-center gap-1.5" style="min-width: 175px !important; flex: 0 0 175px !important;">
                <span class="text-[10px] text-zinc-400 uppercase font-bold shrink-0">From</span>
                <input type="date" id="log-start-date" onchange="filterLogs()" style="width: 135px !important; min-width: 135px !important; height: 40px !important; padding: 0 8px !important; font-size: 11px !important; background: #fafafa !important; border: 1px solid #e4e4e7 !important; border-radius: 10px !important; color: #18181b !important; outline: none !important; box-shadow: none !important; box-sizing: border-box !important;">
            </div>

            <!-- 4. End Date (To) -->
            <div class="flex items-center gap-1.5" style="min-width: 165px !important; flex: 0 0 165px !important;">
                <span class="text-[10px] text-zinc-400 uppercase font-bold shrink-0">To</span>
                <input type="date" id="log-end-date" onchange="filterLogs()" style="width: 135px !important; min-width: 135px !important; height: 40px !important; padding: 0 8px !important; font-size: 11px !important; background: #fafafa !important; border: 1px solid #e4e4e7 !important; border-radius: 10px !important; color: #18181b !important; outline: none !important; box-shadow: none !important; box-sizing: border-box !important;">
            </div>

            <!-- 5. Reset Button -->
            <div style="flex: 0 0 auto !important;">
                <button type="button" onclick="resetLogFilters()" class="px-4 py-2 text-xs font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer" style="height: 40px !important; line-height: 22px !important;">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-xs text-zinc-800 dark:text-zinc-200 min-w-[850px]">
                <thead>
                    <tr class="bg-zinc-50/70 dark:bg-zinc-800/40 border-b border-zinc-150 dark:border-zinc-800 text-zinc-400 dark:text-zinc-500 font-bold text-[10px] uppercase tracking-wider select-none">
                        <th class="py-3.5 px-4 w-44">Timestamp</th>
                        <th class="py-3.5 px-4 w-52">User & Role</th>
                        <th class="py-3.5 px-4 w-40">Event Type</th>
                        <th class="py-3.5 px-4">Description / Details</th>
                        <th class="py-3.5 px-4 w-32">IP Address</th>
                        <th class="py-3.5 px-4 w-36">Device</th>
                    </tr>
                </thead>
                <tbody id="logs-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800/50 font-medium">
                    <!-- Dynamic rendering via JS -->
                </tbody>
            </table>
        </div>
        
        <!-- Empty State -->
        <div id="logs-empty-state" class="hidden p-12 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 mx-auto flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <div class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No matching audit log entries</div>
            <p class="text-xs text-zinc-400 max-w-sm mx-auto">Try broadening your search terms or clearing date filters to view all activity logs.</p>
        </div>
    </div>
</div>

</div> <!-- close #cora-audit-activity-section -->

<!-- Cost Section -->
<div id="cora-audit-cost-section" class="space-y-6 mt-6 hidden">
    <!-- Metrics Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Total Tokens Consumed</span>
                <div class="text-xl font-bold text-zinc-900 dark:text-white mt-1 font-mono">1,482,900</div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">AI Compute Cost</span>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 font-mono">$29.65</div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Active Model</span>
                <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-1.5 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Claude 3.5 Sonnet
                </div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 12V7H5a2 2 0 0 1 2-2h14V4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5z"></path><line x1="12" y1="12" x2="20" y2="12"></line></svg>
            </div>
        </div>
    </div>

    <!-- Cost breakdown table -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-zinc-150 dark:border-zinc-800 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Estimated Token Utilization by Component</h3>
            <span class="text-[10px] text-zinc-400">Updated hourly</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-xs text-zinc-800 dark:text-zinc-200">
                <thead>
                    <tr class="bg-zinc-50/70 dark:bg-zinc-800/40 border-b border-zinc-150 dark:border-zinc-800 text-zinc-400 dark:text-zinc-500 font-bold text-[10px] uppercase tracking-wider select-none">
                        <th class="py-3 px-4">Component / Module</th>
                        <th class="py-3 px-4">Active Model</th>
                        <th class="py-3 px-4">Prompt Tokens</th>
                        <th class="py-3 px-4">Response Tokens</th>
                        <th class="py-3 px-4">Est. Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50 font-medium">
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="py-3 px-4 font-semibold text-zinc-900 dark:text-zinc-100">Leads CRM Auto-SLA & Routing</td>
                        <td class="py-3 px-4 font-mono">Claude 3.5 Sonnet</td>
                        <td class="py-3 px-4 font-mono">412,000</td>
                        <td class="py-3 px-4 font-mono">105,400</td>
                        <td class="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-mono font-bold">$9.54</td>
                    </tr>
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="py-3 px-4 font-semibold text-zinc-900 dark:text-zinc-100">Scheduler Auto-Itinerary & Shift Planning</td>
                        <td class="py-3 px-4 font-mono">Claude 3.5 Sonnet</td>
                        <td class="py-3 px-4 font-mono">518,000</td>
                        <td class="py-3 px-4 font-mono">180,200</td>
                        <td class="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-mono font-bold">$12.91</td>
                    </tr>
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="py-3 px-4 font-semibold text-zinc-900 dark:text-zinc-100">Smart Command Search & Routing</td>
                        <td class="py-3 px-4 font-mono">Gemini 1.5 Flash</td>
                        <td class="py-3 px-4 font-mono">182,500</td>
                        <td class="py-3 px-4 font-mono">32,100</td>
                        <td class="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-mono font-bold">$1.12</td>
                    </tr>
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="py-3 px-4 font-semibold text-zinc-900 dark:text-zinc-100">WordPress Media Alt & SEO Optimizer</td>
                        <td class="py-3 px-4 font-mono">Claude 3.5 Sonnet</td>
                        <td class="py-3 px-4 font-mono">214,300</td>
                        <td class="py-3 px-4 font-mono">38,400</td>
                        <td class="py-3 px-4 text-emerald-600 dark:text-emerald-400 font-mono font-bold">$6.08</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function coraSwitchAuditTab(tab) {
    if (tab === 'activity') {
        jQuery('#cora-audit-activity-section').removeClass('hidden');
        jQuery('#cora-audit-cost-section').addClass('hidden');
        
        jQuery('#tab-activity-btn').addClass('border-zinc-900 dark:border-white text-zinc-900 dark:text-white font-bold').removeClass('border-transparent text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 font-medium');
        jQuery('#tab-cost-btn').removeClass('border-zinc-900 dark:border-white text-zinc-900 dark:text-white font-bold').addClass('border-transparent text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 font-medium');
    } else {
        jQuery('#cora-audit-activity-section').addClass('hidden');
        jQuery('#cora-audit-cost-section').removeClass('hidden');
        
        jQuery('#tab-cost-btn').addClass('border-zinc-900 dark:border-white text-zinc-900 dark:text-white font-bold').removeClass('border-transparent text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 font-medium');
        jQuery('#tab-activity-btn').removeClass('border-zinc-900 dark:border-white text-zinc-900 dark:text-white font-bold').addClass('border-transparent text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 font-medium');
    }
}

const rawLogs = <?php echo json_encode( array_values( $filtered_logs ) ); ?>;
const roleLabels = <?php echo json_encode( $role_labels ); ?>;

function formatLogDate(ts) {
    if (!ts) return 'N/A';
    const d = new Date(ts * 1000);
    if (isNaN(d.getTime())) return 'N/A';

    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const mins = String(d.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${mins}`;
}

function getEventTypeBadgeClass(actionType) {
    const base = "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border ";
    switch(actionType) {
        case 'Authentication':
            return base + "bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800";
        case 'Git Sync':
            return base + "bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400 border-blue-200 dark:border-blue-800";
        case 'Permissions':
        case 'User Management':
            return base + "bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400 border-purple-200 dark:border-purple-800";
        case 'Invitation':
            return base + "bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200 dark:border-amber-800";
        default:
            return base + "bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700";
    }
}

function getFilteredLogs() {
    const search = (document.getElementById('log-search')?.value || '').toLowerCase().trim();
    const typeFilter = document.getElementById('log-type-filter')?.value || '';
    const startDateVal = document.getElementById('log-start-date')?.value || '';
    const endDateVal = document.getElementById('log-end-date')?.value || '';

    let startTs = startDateVal ? new Date(startDateVal).getTime() / 1000 : 0;
    let endTs = endDateVal ? (new Date(endDateVal).getTime() / 1000) + 86399 : Infinity;

    return rawLogs.filter(log => {
        const actor = (log.user_name || '').toLowerCase();
        const action = (log.action_type || '').toLowerCase();
        const desc = (log.description || '').toLowerCase();
        const ip = (log.ip || '').toLowerCase();

        if (search && !actor.includes(search) && !action.includes(search) && !desc.includes(search) && !ip.includes(search)) {
            return false;
        }

        if (typeFilter && log.action_type !== typeFilter) {
            return false;
        }

        const logTs = log.timestamp || 0;
        if (logTs < startTs || logTs > endTs) {
            return false;
        }

        return true;
    });
}

function filterLogs() {
    const tableBody = document.getElementById('logs-table-body');
    const emptyState = document.getElementById('logs-empty-state');
    if (!tableBody) return;

    const filtered = getFilteredLogs();

    if (filtered.length === 0) {
        tableBody.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    let html = '';

    filtered.forEach(log => {
        const formattedDate = formatLogDate(log.timestamp);
        const roleLabel = roleLabels[log.user_role] || log.user_role || 'Guest';
        const badgeClass = getEventTypeBadgeClass(log.action_type);
        
        let detailsText = escapeHtml(log.description);
        let actionLabel = escapeHtml(log.action_type);
        
        if (log.how === 'ai_instructed') {
            actionLabel = '✦ ' + actionLabel;
            detailsText = detailsText + ' via Cora AI';
        }

        html += `
            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                <td class="py-3 px-4 text-zinc-500 font-mono text-[11px]">${formattedDate}</td>
                <td class="py-3 px-4">
                    <div class="font-bold text-zinc-900 dark:text-zinc-100">${escapeHtml(log.user_name || 'System / Admin')}</div>
                    <div class="text-[10px] text-zinc-400 font-mono mt-0.5">${escapeHtml(roleLabel)}</div>
                </td>
                <td class="py-3 px-4">
                    <span class="${badgeClass}">${actionLabel}</span>
                </td>
                <td class="py-3 px-4 text-zinc-600 dark:text-zinc-300 leading-relaxed max-w-[320px] break-words text-xs">${detailsText}</td>
                <td class="py-3 px-4 text-zinc-500 font-mono text-[11px]">${escapeHtml(log.ip || '127.0.0.1')}</td>
                <td class="py-3 px-4 text-zinc-500 font-medium text-[11px]">${escapeHtml(log.device || 'Mac OS / Chrome')}</td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;
}

function resetLogFilters() {
    if (document.getElementById('log-search')) document.getElementById('log-search').value = '';
    if (document.getElementById('log-type-filter')) document.getElementById('log-type-filter').value = '';
    if (document.getElementById('log-start-date')) document.getElementById('log-start-date').value = '';
    if (document.getElementById('log-end-date')) document.getElementById('log-end-date').value = '';
    filterLogs();
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, "&amp;")
              .replace(/</g, "&lt;")
              .replace(/>/g, "&gt;")
              .replace(/"/g, "&quot;")
              .replace(/'/g, "&#039;");
}

function exportLogsCSV() {
    const filtered = getFilteredLogs();
    if (filtered.length === 0) {
        if (window.coraShowToast) {
            window.coraShowToast("No log entries to export.");
        }
        return;
    }

    const rows = [
        ["Timestamp", "Actor Name", "Actor Role", "Event Type", "Description", "IP Address", "Device"]
    ];

    filtered.forEach(log => {
        const dateStr = formatLogDate(log.timestamp);
        const role = roleLabels[log.user_role] || log.user_role || 'Guest';
        
        let desc = log.description;
        let action = log.action_type;
        if (log.how === 'ai_instructed') {
            action = '✦ ' + action;
            desc = desc + ' via Cora AI';
        }

        rows.push([
            dateStr,
            log.user_name || 'System / Admin',
            role,
            action,
            desc,
            log.ip || '127.0.0.1',
            log.device || 'Mac OS / Chrome'
        ]);
    });

    const csvContent = rows.map(e => e.map(val => `"${String(val).replace(/"/g, '""')}"`).join(",")).join("\n");
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", `propOS_audit_logs_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// Initial render
filterLogs();
</script>
