<?php
/**
 * Cora Real Estate CRM - Agency Cost & System Activity Audit View
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_user_id = get_current_user_id();
$current_role = ! empty( wp_get_current_user()->roles ) ? wp_get_current_user()->roles[0] : '';
$current_agency = cora_get_current_user_agency_id();
$current_branch = cora_get_current_user_branch_id();

// Retrieve logs option
$all_logs = get_option( 'cora_activity_logs', array() );
$filtered_logs = array();

// Apply multi-tenant security isolation rules
if ( is_array( $all_logs ) ) {
    // Sort logs descending (newest first)
    usort( $all_logs, function( $a, $b ) {
        return ($b['timestamp'] ?? 0) - ($a['timestamp'] ?? 0);
    } );

    foreach ( $all_logs as $log ) {
        // Super Admin sees everything
        if ( $current_agency === 'super' ) {
            $filtered_logs[] = $log;
            continue;
        }

        // Agency Owner sees everything in their agency
        if ( $current_role === 'cora_manager' ) {
            if ( isset( $log['agency_id'] ) && $log['agency_id'] === $current_agency ) {
                $filtered_logs[] = $log;
            }
            continue;
        }

        // Branch Manager sees everything in their branch
        if ( $current_role === 'cora_branch_manager' ) {
            if ( isset( $log['agency_id'] ) && $log['agency_id'] === $current_agency && isset( $log['branch_id'] ) && $log['branch_id'] === $current_branch ) {
                $filtered_logs[] = $log;
            }
            continue;
        }
    }
}

// Map role display names
$role_labels = array(
    'administrator'       => 'Super Admin',
    'cora_manager'        => 'Agency Owner',
    'cora_branch_manager' => 'Branch Manager',
    'cora_photographer'   => 'Senior Agent',
    'cora_videographer'   => 'Agent',
    'cora_drone_pilot'    => 'Telecaller',
    'cora_editor'         => 'Back Office',
    'cora_viewer'         => 'Viewer',
    'guest'               => 'Guest / System'
);
?>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Audit & Analytics</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Track administrative updates, user access logs, and SaaS subscription metrics.</p>
        </div>
    </div>
</div>

<!-- Monochromatic Tabs Navigation -->
<div class="flex border-b border-zinc-200 gap-6 mt-6">
    <button id="tab-activity-btn" onclick="switchAuditTab('activity')" class="pb-3 text-sm font-bold border-b-2 border-zinc-900 text-zinc-900 transition-all cursor-pointer">
        System Activity Audit Logs
    </button>
    <button id="tab-cost-btn" onclick="switchAuditTab('cost')" class="pb-3 text-sm font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 transition-all cursor-pointer">
        SaaS Subscription Cost Simulator
    </button>
</div>

<!-- Tab Content: System Activity Audit Logs -->
<div id="cora-audit-activity-section" class="space-y-6 mt-6">
    <!-- Filter Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 flex-1">
            <!-- Search Actor Name -->
            <div class="relative">
                <input type="text" id="log-search" oninput="filterLogs()" placeholder="Search actor..." class="w-full pl-8 pr-3 py-1.5 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-500 transition-colors">
                <span class="absolute left-2.5 top-2.5 text-zinc-400">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
            </div>
            <!-- Event Type -->
            <div>
                <select id="log-type-filter" onchange="filterLogs()" class="w-full px-2.5 py-1.5 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-500 transition-colors">
                    <option value="">All Event Types</option>
                    <option value="Authentication">Authentication</option>
                    <option value="Invitation">Invitation</option>
                    <option value="User Management">User Management</option>
                    <option value="Branch Management">Branch Management</option>
                    <option value="Permissions">Permissions</option>
                    <option value="Branch">Branch</option>
                </select>
            </div>
            <!-- Start Date -->
            <div class="flex items-center gap-2">
                <span class="text-[10px] text-zinc-400 uppercase tracking-wider font-semibold">From</span>
                <input type="date" id="log-start-date" onchange="filterLogs()" class="w-full px-2 py-1 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-500 transition-colors">
            </div>
            <!-- End Date -->
            <div class="flex items-center gap-2">
                <span class="text-[10px] text-zinc-400 uppercase tracking-wider font-semibold">To</span>
                <input type="date" id="log-end-date" onchange="filterLogs()" class="w-full px-2 py-1 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-500 transition-colors">
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex items-center gap-2 shrink-0">
            <button onclick="exportLogsCSV()" class="cora-btn px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer active:scale-95 shadow-sm flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-xs text-zinc-800 min-w-[800px]">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 font-semibold">
                        <th class="py-3 px-4 w-48">Timestamp</th>
                        <th class="py-3 px-4 w-56">Actor</th>
                        <th class="py-3 px-4 w-44">Event Type</th>
                        <th class="py-3 px-4">Details</th>
                        <th class="py-3 px-4 w-36">IP Address</th>
                    </tr>
                </thead>
                <tbody id="logs-table-body" class="divide-y divide-zinc-100">
                    <!-- Dynamic rendering via JS -->
                </tbody>
            </table>
        </div>
        <div id="logs-empty-state" class="hidden p-8 text-center text-zinc-400">
            No matching log entries found for the selected criteria.
        </div>
    </div>
</div>

<!-- Tab Content: SaaS Subscription Cost Simulator -->
<div id="cora-audit-cost-section" class="space-y-6 mt-6 hidden">
    <!-- Interactive Sheet Container -->
    <div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden space-y-0">
        <div class="px-5 py-4 border-b border-zinc-150 bg-zinc-50/50 flex justify-between items-center">
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Indian Agency Tool Subscription Sheet (10-Agent Team)</h3>
            <span class="text-[11px] font-mono text-zinc-500 uppercase">Live Calculator Mode</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-xs text-zinc-800 min-w-[800px]">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 font-semibold">
                        <th class="py-2.5 px-4 w-12 text-center border-r border-zinc-200">#</th>
                        <th class="py-2.5 px-4 w-44 border-r border-zinc-200">Business Function</th>
                        <th class="py-2.5 px-4 w-48 border-r border-zinc-200">Popular Tool in India</th>
                        <th class="py-2.5 px-4 w-36 text-right border-r border-zinc-200">Monthly (INR)</th>
                        <th class="py-2.5 px-4 w-36 text-right border-r border-zinc-200">Annual (INR)</th>
                        <th class="py-2.5 px-4 border-r border-zinc-200">Pain Point / Friction</th>
                        <th class="py-2.5 px-4 w-52">Unified Solution Impact</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-zinc-50/40 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">01</td>
                        <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">CRM & Lead Pipelines</td>
                        <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">Sell.do / Salesforce</td>
                        <td class="py-3 px-4 border-r border-zinc-100">
                            <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                                <span>₹</span>
                                <input type="number" id="cora-audit-m-1" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="25000" oninput="calculateAuditRow(1)">
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-1">₹3,00,000</td>
                        <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">Too complex; requires extensive training; doesn't store direct check-in coordinates.</td>
                        <td class="py-3 px-4">
                            <span class="inline-block px-2 py-0.5 rounded bg-zinc-950 text-white text-[10px] font-bold uppercase tracking-wider mb-1">Cora Core CRM</span>
                            <p class="text-[11px] text-zinc-500">Zero learning curve; built for realtors.</p>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-zinc-50/40 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">02</td>
                        <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">Field Attendance</td>
                        <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">Keka HR / Spine HR</td>
                        <td class="py-3 px-4 border-r border-zinc-100">
                            <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                                <span>₹</span>
                                <input type="number" id="cora-audit-m-2" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="7000" oninput="calculateAuditRow(2)">
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-2">₹84,000</td>
                        <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">No integration with property coordinates; tracking is administrative, not operational.</td>
                        <td class="py-3 px-4">
                            <span class="inline-block px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 text-[10px] font-bold uppercase tracking-wider mb-1">GPS Geotagging</span>
                            <p class="text-[11px] text-zinc-500">Match check-ins with properties.</p>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-zinc-50/40 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">03</td>
                        <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">WhatsApp Automation</td>
                        <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">AiSensy / Wati / Interakt</td>
                        <td class="py-3 px-4 border-r border-zinc-100">
                            <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                                <span>₹</span>
                                <input type="number" id="cora-audit-m-3" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="2000" oninput="calculateAuditRow(3)">
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-3">₹24,000</td>
                        <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">Needs manual message configuration and copy-pasting to trigger API templates.</td>
                        <td class="py-3 px-4">
                            <span class="inline-block px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 text-[10px] font-bold uppercase tracking-wider mb-1">Auto Webhooks</span>
                            <p class="text-[11px] text-zinc-500">Direct coordinate & booking alerts.</p>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-zinc-50/40 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">04</td>
                        <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">Media Portal Storage</td>
                        <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">Google Drive / Dropbox</td>
                        <td class="py-3 px-4 border-r border-zinc-100">
                            <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                                <span>₹</span>
                                <input type="number" id="cora-audit-m-4" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="13000" oninput="calculateAuditRow(4)">
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-4">₹1,56,000</td>
                        <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">Unbranded links feel unprofessional; client requests constant download access.</td>
                        <td class="py-3 px-4">
                            <span class="inline-block px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 text-[10px] font-bold uppercase tracking-wider mb-1">Branded Portals</span>
                            <p class="text-[11px] text-zinc-500">Dedicated portfolio galleries.</p>
                        </td>
                    </tr>

                    <!-- Row 5 -->
                    <tr class="hover:bg-zinc-50/40 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">05</td>
                        <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">Social Scheduling</td>
                        <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">Hootsuite / Buffer</td>
                        <td class="py-3 px-4 border-r border-zinc-100">
                            <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                                <span>₹</span>
                                <input type="number" id="cora-audit-m-5" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="25000" oninput="calculateAuditRow(5)">
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-5">₹3,00,000</td>
                        <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">Requires manually downloading files and draft copies; high daily overhead.</td>
                        <td class="py-3 px-4">
                            <span class="inline-block px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 text-[10px] font-bold uppercase tracking-wider mb-1">AI Publisher</span>
                            <p class="text-[11px] text-zinc-500">Post properties directly via AI helper.</p>
                        </td>
                    </tr>

                    <!-- Total Row -->
                    <tr class="bg-zinc-50/80 font-bold border-t-2 border-zinc-900">
                        <td class="py-3.5 px-4 text-center border-r border-zinc-200 font-mono">-</td>
                        <td class="py-3.5 px-4 border-r border-zinc-200 text-zinc-900" colspan="2">TOTAL SAAS SUBSCRIPTION OUTFLOW</td>
                        <td class="py-3.5 px-4 text-right font-mono text-sm border-r border-zinc-200 text-zinc-900" id="cora-audit-m-total">₹72,000</td>
                        <td class="py-3.5 px-4 text-right font-mono text-sm border-r border-zinc-200 text-zinc-900" id="cora-audit-a-total">₹8,64,000</td>
                        <td class="py-3.5 px-4 text-zinc-500 font-normal leading-relaxed border-r border-zinc-200" colspan="2">
                            Indian real estate agencies waste approximately <strong class="text-zinc-900 font-bold" id="cora-audit-waste-text">₹8.64 Lakhs/year</strong> in fragmented subscriptions.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 bg-zinc-50 border-t border-zinc-150 text-[11px] text-zinc-500 flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>Interactive Mode Enabled. Modify the values in the <strong>Monthly (INR)</strong> column directly to see simulated savings calculations update.</span>
        </div>
    </div>

    <!-- Tips / Next Step Callout -->
    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <h4 class="text-sm font-bold text-zinc-900">💡 Video Presentation Simulation Tip</h4>
            <p class="text-xs text-zinc-500 leading-relaxed">Edit any subscription value above. Cora will automatically recalculate the values live on-screen, ideal for demonstration and presentations.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="<?php echo esc_url( home_url( '/cora-landing/video-planner.html' ) ); ?>" target="_blank" class="px-4 py-2 border border-zinc-250 hover:bg-zinc-100 text-zinc-800 font-semibold rounded-md text-xs transition-colors shadow-sm flex items-center gap-1.5 no-underline">
                View Video Script
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
            </a>
        </div>
    </div>
</div>

<script>
// Load dynamic logs data from PHP
const activityLogs = <?php echo json_encode( $filtered_logs ); ?>;
const roleLabels = <?php echo json_encode( $role_labels ); ?>;

// Switch tab contents
function switchAuditTab(tab) {
    const activitySec = document.getElementById('cora-audit-activity-section');
    const costSec = document.getElementById('cora-audit-cost-section');
    const activityBtn = document.getElementById('tab-activity-btn');
    const costBtn = document.getElementById('tab-cost-btn');

    if (tab === 'activity') {
        activitySec.classList.remove('hidden');
        costSec.classList.add('hidden');
        activityBtn.className = "pb-3 text-sm font-bold border-b-2 border-zinc-900 text-zinc-900 transition-all cursor-pointer";
        costBtn.className = "pb-3 text-sm font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 transition-all cursor-pointer";
    } else {
        activitySec.classList.add('hidden');
        costSec.classList.remove('hidden');
        activityBtn.className = "pb-3 text-sm font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 transition-all cursor-pointer";
        costBtn.className = "pb-3 text-sm font-bold border-b-2 border-zinc-900 text-zinc-900 transition-all cursor-pointer";
    }
}

// Format Unix timestamp (DD/MM/YYYY HH:MM:SS)
function formatLogDate(timestamp) {
    const d = new Date(timestamp * 1000);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const seconds = String(d.getSeconds()).padStart(2, '0');
    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
}

// Get event type classes for badge styling
function getEventTypeBadgeClass(actionType) {
    const base = "inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider ";
    switch (actionType) {
        case 'Authentication':
            return base + "bg-zinc-100 text-zinc-800";
        case 'Invitation':
            return base + "bg-zinc-100 text-zinc-800";
        case 'User Management':
            return base + "bg-zinc-100 text-zinc-800";
        case 'Branch Management':
            return base + "bg-zinc-100 text-zinc-800";
        case 'Permissions':
            return base + "bg-zinc-100 text-zinc-800";
        default:
            return base + "bg-zinc-100 text-zinc-800";
    }
}

// Filter and search logs client-side
function getFilteredLogs() {
    const searchVal = document.getElementById('log-search').value.toLowerCase().trim();
    const typeVal = document.getElementById('log-type-filter').value;
    const startDateVal = document.getElementById('log-start-date').value;
    const endDateVal = document.getElementById('log-end-date').value;

    let startTimestamp = 0;
    if (startDateVal) {
        startTimestamp = new Date(startDateVal).setHours(0,0,0,0) / 1000;
    }

    let endTimestamp = Infinity;
    if (endDateVal) {
        endTimestamp = new Date(endDateVal).setHours(23,59,59,999) / 1000;
    }

    return activityLogs.filter(log => {
        // Search Name
        if (searchVal && !log.user_name.toLowerCase().includes(searchVal)) {
            return false;
        }

        // Filter Type
        if (typeVal && log.action_type !== typeVal) {
            return false;
        }

        // Date Range
        if (log.timestamp < startTimestamp || log.timestamp > endTimestamp) {
            return false;
        }

        return true;
    });
}

// Render dynamic log rows
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
        const roleLabel = roleLabels[log.user_role] || log.user_role;
        const badgeClass = getEventTypeBadgeClass(log.action_type);

        html += `
            <tr class="hover:bg-zinc-50/40 transition-colors">
                <td class="py-3 px-4 text-zinc-500 font-mono text-[11px]">${formattedDate}</td>
                <td class="py-3 px-4">
                    <div class="font-bold text-zinc-900">${escapeHtml(log.user_name)}</div>
                    <div class="text-[10px] text-zinc-400 font-mono mt-0.5">${escapeHtml(roleLabel)}</div>
                </td>
                <td class="py-3 px-4">
                    <span class="${badgeClass}">${escapeHtml(log.action_type)}</span>
                </td>
                <td class="py-3 px-4 text-zinc-600 leading-relaxed max-w-[320px] break-words">${escapeHtml(log.description)}</td>
                <td class="py-3 px-4 text-zinc-500 font-mono text-[11px]">${escapeHtml(log.ip)}</td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;
}

// Simple HTML escaping helper
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, "&amp;")
              .replace(/</g, "&lt;")
              .replace(/>/g, "&gt;")
              .replace(/"/g, "&quot;")
              .replace(/'/g, "&#039;");
}

// Export filtered logs to CSV
function exportLogsCSV() {
    const filtered = getFilteredLogs();
    if (filtered.length === 0) {
        if (window.coraShowToast) {
            window.coraShowToast("No log entries to export.");
        }
        return;
    }

    const rows = [
        ["Timestamp", "Actor Name", "Actor Role", "Event Type", "Description", "IP Address"]
    ];

    filtered.forEach(log => {
        const dateStr = formatLogDate(log.timestamp);
        const role = roleLabels[log.user_role] || log.user_role;
        rows.push([
            dateStr,
            log.user_name,
            role,
            log.action_type,
            log.description,
            log.ip
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

// ── Existing SaaS Subscription Calculator Scripts ──
function calculateAuditRow(rowNum) {
    const monthlyInput = document.getElementById(`cora-audit-m-${rowNum}`);
    const annualCell = document.getElementById(`cora-audit-a-${rowNum}`);
    
    if (!monthlyInput || !annualCell) return;
    
    const val = parseFloat(monthlyInput.value) || 0;
    const annualVal = val * 12;
    
    annualCell.textContent = formatAuditCurrency(annualVal);
    recalculateAuditTotals();
}

function formatAuditCurrency(amount) {
    return '₹' + amount.toLocaleString('en-IN');
}

function recalculateAuditTotals() {
    let monthlyTotal = 0;
    for (let i = 1; i <= 5; i++) {
        const input = document.getElementById(`cora-audit-m-${i}`);
        if (input) {
            monthlyTotal += parseFloat(input.value) || 0;
        }
    }
    
    const annualTotal = monthlyTotal * 12;
    
    const mTotal = document.getElementById('cora-audit-m-total');
    const aTotal = document.getElementById('cora-audit-a-total');
    const wasteText = document.getElementById('cora-audit-waste-text');
    
    if (mTotal) mTotal.textContent = formatAuditCurrency(monthlyTotal);
    if (aTotal) aTotal.textContent = formatAuditCurrency(annualTotal);
    
    if (wasteText) {
        const lakhs = (annualTotal / 100000).toFixed(2);
        wasteText.innerHTML = `₹${lakhs} Lakhs/year`;
    }
}

function resetToMockup() {
    const defaults = [25000, 7000, 2000, 13000, 25000];
    for (let i = 1; i <= 5; i++) {
        const input = document.getElementById(`cora-audit-m-${i}`);
        if (input) {
            input.value = defaults[i-1];
            calculateAuditRow(i);
        }
    }
    if (window.coraShowToast) {
        window.coraShowToast("Subscription matrix values reset to defaults.");
    }
}

// Initial render
filterLogs();
recalculateAuditTotals();
</script>
