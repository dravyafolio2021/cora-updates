<?php
/**
 * Cora Platform — Enterprise Lead Management Suite View
 *
 * Provides full CRM inquiry pipeline, interactive drag & drop Kanban board,
 * searchable directory table, funnel analytics, activity log timeline,
 * direct outreach, and right-sliding side drawer sheets.
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Fetch leads and initial datasets
$cora_leads_raw = cora_db_get_leads();
$cora_clients_raw = function_exists('cora_db_get_clients') ? cora_db_get_clients() : array();
$cora_users_list = get_users( array( 'fields' => array( 'ID', 'display_name', 'user_email' ) ) );

// Compute KPI Metrics
$total_leads_count = count( $cora_leads_raw );
$pipeline_total_value = 0;
$converted_count = 0;
$hot_leads_count = 0;

$default_stages = array(
    'New Lead'    => array( 'key' => 'New Lead', 'label' => 'New Inquiries', 'badge' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800', 'enabled' => true ),
    'Contacted'   => array( 'key' => 'Contacted', 'label' => 'Proposal Sent', 'badge' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800', 'enabled' => true ),
    'Site Visit'  => array( 'key' => 'Site Visit', 'label' => 'Site Visit / Viewing', 'badge' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800', 'enabled' => true ),
    'Negotiation' => array( 'key' => 'Negotiation', 'label' => 'Negotiation', 'badge' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800', 'enabled' => true ),
    'Converted'   => array( 'key' => 'Converted', 'label' => 'Converted', 'badge' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800', 'enabled' => true ),
    'Lost'        => array( 'key' => 'Lost', 'label' => 'Closed / Lost', 'badge' => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800', 'enabled' => true ),
);

$saved_stages = get_option( 'cora_workspace_lead_stages', array() );
if ( is_array( $saved_stages ) && ! empty( $saved_stages ) ) {
    $stages_config = $saved_stages;
} else {
    $stages_config = $default_stages;
}

$stages_summary = array();
foreach ( $stages_config as $s_key => $s_val ) {
    if ( isset( $s_val['enabled'] ) && ! $s_val['enabled'] ) {
        continue;
    }
    $stages_summary[$s_key] = array(
        'key'   => $s_key,
        'count' => 0,
        'value' => 0,
        'label' => $s_val['label'] ?? $s_key,
        'badge' => $s_val['badge'] ?? 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800',
    );
}

foreach ( $cora_leads_raw as $l ) {
    $numeric_price = (float) preg_replace( '/[^0-9.]/', '', $l['price'] ?? '0' );
    $pipeline_total_value += $numeric_price;

    $st = $l['status'] ?? 'New Lead';
    if ( isset( $stages_summary[$st] ) ) {
        $stages_summary[$st]['count']++;
        $stages_summary[$st]['value'] += $numeric_price;
    }

    if ( $st === 'Converted' || ! empty( $l['converted_to_client'] ) ) {
        $converted_count++;
    }

    if ( isset($l['score']) && strtolower($l['score']) === 'hot' ) {
        $hot_leads_count++;
    }
}

$conversion_rate = $total_leads_count > 0 ? round( ( $converted_count / $total_leads_count ) * 100, 1 ) : 0;
?>

<div id="cora-leads-module-container" class="space-y-6 select-none font-sans text-zinc-900 dark:text-zinc-100">

    <!-- TOP HEADER BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center shrink-0 shadow-md">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">Enterprise Lead Management</h1>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">CRM Suite v2</span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Nurture client inquiries, drag & drop deal stages, track funnel conversion, and close shoots.</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <div class="relative">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="cora-lead-search-input" placeholder="Search leads by name, email, city..." 
                       class="pl-9 pr-3 py-1.5 text-xs bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-1 focus:ring-zinc-950 dark:focus:ring-white w-48 lg:w-64 transition-all"
                       onkeyup="coraFilterLeadsList()">
            </div>

            <button type="button" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 font-semibold rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer" onclick="coraExportLeadsCSV()">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Export CSV
            </button>

            <button type="button" class="px-4 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-lg text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all flex items-center gap-2 cursor-pointer shadow-sm" onclick="coraOpenCreateLeadDrawer()">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Lead
            </button>
        </div>
    </div>

    <!-- TOP KPI STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Pipeline Value -->
        <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Pipeline Value</span>
                <div class="p-1.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center w-6 h-6">
                    <span class="text-xs font-extrabold leading-none select-none">₹</span>
                </div>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white">₹<?php echo number_format( $pipeline_total_value ); ?></span>
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">Active Deals</span>
            </div>
        </div>

        <!-- Card 2: Total Inquiries -->
        <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total Inquiries</span>
                <div class="p-1.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white"><?php echo $total_leads_count; ?></span>
                <span class="text-[10px] font-bold text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded"><?php echo $hot_leads_count; ?> Hot Deals</span>
            </div>
        </div>

        <!-- Card 3: Conversion Rate -->
        <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Conversion Rate</span>
                <div class="p-1.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white"><?php echo $conversion_rate; ?>%</span>
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded border border-emerald-200 dark:border-emerald-800"><?php echo $converted_count; ?> Converted</span>
            </div>
        </div>

        <!-- Card 4: Avg Response Time -->
        <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Avg Response Time</span>
                <div class="p-1.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white">18 mins</span>
                <span class="text-[10px] font-bold text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded">Target < 30m</span>
            </div>
        </div>
    </div>

    <!-- SUB-TAB NAVIGATION BAR -->
    <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
        <nav class="flex space-x-1" aria-label="Tabs">
            <button type="button" class="cora-lead-subtab-btn active px-3.5 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 shadow-sm" data-tab="kanban" onclick="coraSwitchLeadSubtab('kanban')">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>
                    <span>Kanban Pipeline</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-900 ml-0.5"><?php echo $total_leads_count; ?></span>
                </div>
            </button>
            <button type="button" class="cora-lead-subtab-btn px-3.5 py-2 text-xs font-semibold rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-tab="directory" onclick="coraSwitchLeadSubtab('directory')">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    <span>Leads Directory</span>
                </div>
            </button>
            <button type="button" class="cora-lead-subtab-btn px-3.5 py-2 text-xs font-semibold rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-tab="analytics" onclick="coraSwitchLeadSubtab('analytics')">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    <span>Funnel & Analytics</span>
                </div>
            </button>
            <button type="button" class="cora-lead-subtab-btn px-3.5 py-2 text-xs font-semibold rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-tab="activity" onclick="coraSwitchLeadSubtab('activity')">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Activity Log</span>
                </div>
            </button>
        </nav>

        <div class="flex items-center gap-2">
            <button type="button" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 font-bold rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer border border-zinc-200 dark:border-zinc-700 shadow-2xs" onclick="coraOpenManageStagesDrawer()">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                <span>Customize Columns</span>
            </button>

            <select id="cora-lead-stage-filter" class="px-2.5 py-1.5 text-xs bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg text-zinc-700 dark:text-zinc-300 focus:outline-none" onchange="coraFilterLeadsList()">
                <option value="all">All Stages</option>
                <?php foreach ( $stages_summary as $sk => $sd ) : ?>
                    <option value="<?php echo esc_attr( $sk ); ?>"><?php echo esc_html( $sd['label'] ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- SUB-TAB 1: KANBAN PIPELINE BOARD -->
    <div id="cora-lead-pane-kanban" class="cora-lead-tab-pane">
        <div class="flex overflow-x-auto gap-4 items-start pb-8 pt-1" style="scrollbar-width: thin;">
            <?php foreach ( $stages_summary as $stage_key => $stage_data ) : 
                $col_leads = array_filter( $cora_leads_raw, function($lead) use ($stage_key) {
                    $st = $lead['status'] ?? 'New Lead';
                    return $st === $stage_key;
                });
            ?>
            <div class="cora-kanban-column flex flex-col p-3.5 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs shrink-0 w-[380px] min-w-[380px] min-h-[600px] relative"
                 data-status="<?php echo esc_attr( $stage_key ); ?>"
                 ondragover="coraLeadDragOver(event)"
                 ondrop="coraLeadDrop(event)">
                
                <!-- Column Header -->
                <div class="mb-3 pb-2.5 border-b border-zinc-100 dark:border-zinc-800/80 px-1 pt-1 shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold border <?php echo $stage_data['badge']; ?>">
                                <?php echo esc_html( $stage_data['label'] ); ?>
                            </span>
                            <span class="text-[10px] text-zinc-500 font-bold bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded-full col-count"><?php echo count($col_leads); ?></span>
                        </div>
                        <button type="button" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors p-1 cursor-pointer" title="Quick Add Lead in Stage" onclick="coraOpenCreateLeadDrawer('<?php echo esc_attr($stage_key); ?>')">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-[10px] text-zinc-400 font-medium">
                        <span>Total Sum</span>
                        <span class="font-bold text-zinc-700 dark:text-zinc-300">₹<?php echo number_format($stage_data['value']); ?></span>
                    </div>
                </div>

                <!-- Cards Container -->
                <div class="cora-cards-container flex-1 space-y-2.5 overflow-y-auto pr-1 pb-10" style="max-height: 520px; scrollbar-width: none;">
                    <?php if ( empty($col_leads) ) : ?>
                        <div class="p-4 text-center border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl text-[11px] text-zinc-400 my-2 select-none">
                            No deals in this stage
                        </div>
                    <?php else : ?>
                        <?php foreach ( $col_leads as $lead ) : 
                            $score = isset($lead['score']) ? strtolower($lead['score']) : 'warm';
                            $score_badge = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800';
                            $score_label = 'Warm';
                            if ($score === 'hot') {
                                $score_badge = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800';
                                $score_label = 'Hot 🔥';
                            } else if ($score === 'cold') {
                                $score_badge = 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800';
                                $score_label = 'Cold ❄️';
                            }

                            $format_tag = $lead['format'] ?? 'Photoshoot';
                            $assignee_name = $lead['assignee_name'] ?? 'Shruti Sharma';
                            $assignee_role = $lead['assignee_role'] ?? 'Super Admin';
                            $assignee_init = $lead['assignee_init'] ?? strtoupper(substr($lead['names'], 0, 1));
                            $checklist = $lead['checklist'] ?? '1/2 (50%)';
                            $checklist_pct = $lead['checklist_pct'] ?? 50;
                        ?>
                        <div class="cora-lead-card bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 p-4 rounded-2xl shadow-2xs hover:shadow-md hover:border-zinc-300 dark:hover:border-zinc-700 transition-all cursor-grab active:cursor-grabbing flex flex-col gap-3 relative group"
                             draggable="true"
                             data-id="<?php echo esc_attr( $lead['id'] ); ?>"
                             data-name="<?php echo esc_attr( strtolower($lead['names']) ); ?>"
                             data-email="<?php echo esc_attr( strtolower($lead['email']) ); ?>"
                             data-status="<?php echo esc_attr( $stage_key ); ?>"
                             ondragstart="coraLeadDragStart(event)"
                             onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr( $lead['id'] ); ?>')">
                            
                            <!-- Header Row: Client Pill + Score Badge + Options -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-extrabold text-[10px] uppercase tracking-wider truncate max-w-[200px]">
                                    <?php echo esc_html( strtoupper($lead['names']) ); ?>
                                </span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-bold border <?php echo $score_badge; ?>">
                                        <?php echo $score_label; ?>
                                    </span>
                                    <button type="button" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white p-1 border-none bg-transparent cursor-pointer" onclick="event.stopPropagation(); coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Main Title & Shoot Subheading -->
                            <div>
                                <h4 class="font-extrabold text-zinc-950 dark:text-white text-xs tracking-tight leading-snug group-hover:text-zinc-900 dark:group-hover:text-zinc-100" title="<?php echo esc_attr( $lead['scale'] ); ?>">
                                    <?php echo esc_html( $lead['scale'] ?? 'Standard Shoot' ); ?>
                                </h4>
                                <p class="text-[10.5px] text-zinc-500 dark:text-zinc-400 font-medium truncate mt-0.5">
                                    Shoot: <?php echo esc_html( $lead['city'] ?? 'Mumbai' ); ?>
                                </p>
                            </div>

                            <!-- Tags & Deal Value Row -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-lg border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-bold bg-white dark:bg-zinc-900 shadow-2xs">
                                    <?php echo esc_html( $format_tag ); ?>
                                </span>
                                <span class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-950 dark:text-white text-[10.5px] font-black tracking-tight">
                                    <?php echo esc_html( $lead['price'] ?? '₹0' ); ?>
                                </span>
                            </div>

                            <!-- Progress Bar / Deliverables Checklist -->
                            <div class="space-y-1.5 pt-0.5">
                                <div class="flex items-center justify-between text-[10px] font-bold text-zinc-600 dark:text-zinc-400">
                                    <span>Checklist</span>
                                    <span><?php echo esc_html( $checklist ); ?></span>
                                </div>
                                <div class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-zinc-950 dark:bg-white rounded-full transition-all" style="width: <?php echo intval($checklist_pct); ?>%;"></div>
                                </div>
                            </div>

                            <!-- Bottom Row: Assignee Avatar + Action Button -->
                            <div class="pt-2.5 flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center font-extrabold text-xs shrink-0 border border-zinc-200/80 dark:border-zinc-700">
                                        <?php echo esc_html( $assignee_init ); ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-zinc-900 dark:text-white text-[11px] truncate leading-tight"><?php echo esc_html( $assignee_name ); ?></div>
                                        <div class="text-[9.5px] text-zinc-400 dark:text-zinc-500 truncate leading-tight"><?php echo esc_html( $assignee_role ); ?></div>
                                    </div>
                                </div>
                                <button type="button" class="px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-[10.5px] font-bold hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all flex items-center gap-1 cursor-pointer shrink-0 shadow-2xs" onclick="event.stopPropagation(); coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                    <span>Review</span>
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Bottom Add Button -->
                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <button type="button" class="w-full py-1.5 text-center text-xs font-semibold text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-all cursor-pointer flex items-center justify-center gap-1.5" onclick="coraOpenCreateLeadDrawer('<?php echo esc_attr($stage_key); ?>')">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Add to Stage</span>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SUB-TAB 2: LEADS DIRECTORY TABLE -->
    <div id="cora-lead-pane-directory" class="cora-lead-tab-pane hidden space-y-4">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600 dark:text-zinc-300">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px] border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="p-3.5 w-10 text-center">
                                <input type="checkbox" id="cora-leads-select-all" class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer" onchange="coraToggleSelectAllLeads(this)">
                            </th>
                            <th class="p-3.5">Lead / Client Name</th>
                            <th class="p-3.5">Contact Details</th>
                            <th class="p-3.5">Deal Stage</th>
                            <th class="p-3.5">Budget / Value</th>
                            <th class="p-3.5">Temperature</th>
                            <th class="p-3.5">Location</th>
                            <th class="p-3.5">Created Date</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cora-leads-table-body" class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        <?php if ( empty($cora_leads_raw) ) : ?>
                            <tr>
                                <td colspan="9" class="p-8 text-center text-zinc-400">No leads registered in workspace yet. Click "Add Lead" to create your first inquiry.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $cora_leads_raw as $lead ) : 
                                $st = $lead['status'] ?? 'New Lead';
                                $badge = $stages_summary[$st]['badge'] ?? 'bg-zinc-100 text-zinc-800';
                            ?>
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors cursor-pointer" onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                <td class="p-3.5 text-center" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="cora-lead-row-checkbox rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer" value="<?php echo esc_attr($lead['id']); ?>">
                                </td>
                                <td class="p-3.5 font-bold text-zinc-900 dark:text-white">
                                    <?php echo esc_html( $lead['names'] ); ?>
                                    <span class="block text-[10px] font-normal text-zinc-400 mt-0.5"><?php echo esc_html( $lead['scale'] ?? 'Standard Shoot' ); ?></span>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-medium text-zinc-800 dark:text-zinc-200"><?php echo esc_html($lead['email']); ?></div>
                                    <div class="text-[10px] text-zinc-400 mt-0.5"><?php echo esc_html($lead['phone'] ?? 'N/A'); ?></div>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?php echo $badge; ?>">
                                        <?php echo esc_html( $st ); ?>
                                    </span>
                                </td>
                                <td class="p-3.5 font-black text-zinc-900 dark:text-white">
                                    <?php echo esc_html( $lead['price'] ?? '₹0' ); ?>
                                </td>
                                <td class="p-3.5">
                                    <?php 
                                    $sc = strtolower($lead['score'] ?? 'warm');
                                    if ($sc === 'hot') echo '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">Hot 🔥</span>';
                                    else if ($sc === 'cold') echo '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">Cold ❄️</span>';
                                    else echo '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">Warm ☀️</span>';
                                    ?>
                                </td>
                                <td class="p-3.5 font-medium">
                                    <?php echo esc_html( $lead['city'] ?? 'Mumbai' ); ?>
                                </td>
                                <td class="p-3.5 text-zinc-400 font-medium">
                                    <?php echo esc_html( date( 'd M Y', $lead['created_at'] ) ); ?>
                                </td>
                                <td class="p-3.5 text-right space-x-1" onclick="event.stopPropagation()">
                                    <button type="button" class="px-2.5 py-1 text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-md transition-all cursor-pointer" onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                        View Deal
                                    </button>
                                    <button type="button" class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 rounded-md border border-emerald-200 dark:border-emerald-800 transition-all cursor-pointer" onclick="coraConvertLeadToClient('<?php echo esc_attr($lead['id']); ?>')">
                                        Convert
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 3: FUNNEL & REVENUE ANALYTICS -->
    <div id="cora-lead-pane-analytics" class="cora-lead-tab-pane hidden space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Stage Breakdown Bar -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-sm text-zinc-900 dark:text-white">Pipeline Conversion Funnel</h3>
                    <span class="text-xs text-zinc-400 font-medium"><?php echo $total_leads_count; ?> Total Deals</span>
                </div>

                <div class="space-y-3 pt-2">
                    <?php foreach ( $stages_summary as $k => $sd ) :
                        $pct = $total_leads_count > 0 ? round( ($sd['count'] / $total_leads_count) * 100, 1 ) : 0;
                    ?>
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="text-zinc-700 dark:text-zinc-300"><?php echo esc_html($sd['label']); ?></span>
                            <span class="text-zinc-400"><?php echo $sd['count']; ?> deals (<?php echo $pct; ?>%) &bull; ₹<?php echo number_format($sd['value']); ?></span>
                        </div>
                        <div class="w-full h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-zinc-950 dark:bg-white rounded-full transition-all duration-500" style="width: <?php echo $pct; ?>%;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Lead Channels & Sources -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-sm text-zinc-900 dark:text-white">Lead Acquisition Channels</h3>
                    <span class="text-xs text-zinc-400 font-medium">Source Tracking</span>
                </div>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200/60 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-900 text-white flex items-center justify-center font-bold text-xs">WEB</div>
                            <div>
                                <span class="font-bold text-xs block text-zinc-900 dark:text-white">Website Inquiry Forms</span>
                                <span class="text-[10px] text-zinc-400">Direct booking submissions</span>
                            </div>
                        </div>
                        <span class="font-black text-xs text-zinc-900 dark:text-white">45%</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200/60 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">WA</div>
                            <div>
                                <span class="font-bold text-xs block text-zinc-900 dark:text-white">WhatsApp Business</span>
                                <span class="text-[10px] text-zinc-400">Direct chat inquiries</span>
                            </div>
                        </div>
                        <span class="font-black text-xs text-zinc-900 dark:text-white">30%</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200/60 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">REF</div>
                            <div>
                                <span class="font-bold text-xs block text-zinc-900 dark:text-white">Client Referrals</span>
                                <span class="text-[10px] text-zinc-400">Word-of-mouth & agency recommendations</span>
                            </div>
                        </div>
                        <span class="font-black text-xs text-zinc-900 dark:text-white">25%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 4: ACTIVITY & OUTREACH LOG -->
    <div id="cora-lead-pane-activity" class="cora-lead-tab-pane hidden space-y-4">
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-zinc-900 dark:text-white">Recent Outreach & Stage Activity Timeline</h3>
            
            <div id="cora-lead-activity-timeline" class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-zinc-200 dark:before:bg-zinc-800">
                <div class="relative">
                    <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white dark:border-zinc-900"></div>
                    <div class="text-xs">
                        <span class="font-bold text-zinc-900 dark:text-white">Lead Converted:</span>
                        <span class="text-zinc-600 dark:text-zinc-300"> Rajesh Kumar was converted to active client record.</span>
                        <div class="text-[10px] text-zinc-400 mt-0.5">Today at 03:45 PM by Admin</div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-indigo-500 border-2 border-white dark:border-zinc-900"></div>
                    <div class="text-xs">
                        <span class="font-bold text-zinc-900 dark:text-white">Proposal Dispatched:</span>
                        <span class="text-zinc-600 dark:text-zinc-300"> Sent commercial quote ₹1,50,000 to Priya Sharma via Email Suite.</span>
                        <div class="text-[10px] text-zinc-400 mt-0.5">Yesterday at 11:20 AM</div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-amber-500 border-2 border-white dark:border-zinc-900"></div>
                    <div class="text-xs">
                        <span class="font-bold text-zinc-900 dark:text-white">New Lead Registered:</span>
                        <span class="text-zinc-600 dark:text-zinc-300"> New inquiry from Ananya Verma for Luxury Villa shoot.</span>
                        <div class="text-[10px] text-zinc-400 mt-0.5">2 days ago</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 1: LEAD DETAIL & DEAL SHEET DRAWER                    -->
<!-- ========================================================================= -->
<aside id="cora-lead-detail-drawer" class="cora-side-drawer fixed top-0 right-0 w-[520px] max-w-[90vw] h-full bg-white dark:bg-zinc-900 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 dark:border-zinc-800 flex flex-col font-sans">
    <!-- Header -->
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-50/50 dark:bg-zinc-850/50">
        <div>
            <div class="flex items-center gap-2">
                <h3 id="cora-drawer-lead-name" class="font-extrabold text-base text-zinc-900 dark:text-white">Lead Deal Panel</h3>
                <span id="cora-drawer-lead-score" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-500/10 text-amber-600 border border-amber-200">Warm</span>
            </div>
            <p id="cora-drawer-lead-email" class="text-xs text-zinc-400 mt-0.5">client@example.com</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-800 dark:hover:text-white p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" onclick="window.coraCloseAllDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Content Body -->
    <div class="p-6 overflow-y-auto flex-1 space-y-5">
        <!-- Quick Action CTA Bar -->
        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Current Stage</span>
                <select id="cora-drawer-stage-select" class="text-xs font-bold bg-transparent text-zinc-900 dark:text-white focus:outline-none cursor-pointer mt-0.5" onchange="coraUpdateLeadStageFromDrawer()">
                    <option value="New Lead">New Lead</option>
                    <option value="Contacted">Proposal Sent</option>
                    <option value="Site Visit">Site Visit / Viewing</option>
                    <option value="Negotiation">Negotiation</option>
                    <option value="Converted">Converted</option>
                    <option value="Lost">Closed / Lost</option>
                </select>
            </div>
            <button type="button" id="cora-convert-lead-btn" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition-all shadow-xs cursor-pointer flex items-center gap-1.5" onclick="coraConvertCurrentLeadToClient()">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Convert to Client
            </button>
        </div>

        <input type="hidden" id="cora-drawer-lead-id" value="">

        <!-- General Parameters Form -->
        <div class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Full Name</label>
                <input type="text" id="cora-drawer-input-names" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Email Address</label>
                    <input type="email" id="cora-drawer-input-email" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Phone / WhatsApp</label>
                    <input type="text" id="cora-drawer-input-phone" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Deal Value / Budget (₹)</label>
                    <input type="text" id="cora-drawer-input-price" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Temperature / Priority</label>
                    <select id="cora-drawer-input-score" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                        <option value="hot">Hot 🔥</option>
                        <option value="warm">Warm ☀️</option>
                        <option value="cold">Cold ❄️</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Location / Target City</label>
                <input type="text" id="cora-drawer-input-city" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
            </div>

            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Deal Notes & Requirements</label>
                <textarea id="cora-drawer-input-notes" rows="4" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none resize-none" placeholder="Client specifications, requested deliverables, shoot dates..."></textarea>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-50/50 dark:bg-zinc-850/50">
        <button type="button" class="px-3 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg text-xs font-semibold transition-all cursor-pointer" onclick="coraDeleteCurrentLead()">
            Delete Lead
        </button>
        <div class="flex items-center gap-2">
            <button type="button" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold rounded-lg text-xs cursor-pointer" onclick="window.coraCloseAllDrawers()">
                Cancel
            </button>
            <button type="button" class="px-4 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-lg text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer" onclick="coraSaveLeadFromDrawer()">
                Save Deal Changes
            </button>
        </div>
    </div>
</aside>

<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 2: CREATE / EDIT LEAD DRAWER                         -->
<!-- ========================================================================= -->
<aside id="cora-create-lead-drawer" class="cora-side-drawer fixed top-0 right-0 w-[500px] max-w-[90vw] h-full bg-white dark:bg-zinc-900 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 dark:border-zinc-800 flex flex-col font-sans">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-50/50 dark:bg-zinc-850/50">
        <div>
            <h3 class="font-extrabold text-base text-zinc-900 dark:text-white">Register New Lead Inquiry</h3>
            <p class="text-xs text-zinc-400 mt-0.5">Add a new client deal into your CRM pipeline.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-800 dark:hover:text-white p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" onclick="window.coraCloseAllDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-create-lead-form" class="p-6 overflow-y-auto flex-1 space-y-4 text-xs" onsubmit="event.preventDefault(); coraSubmitNewLeadForm();">
        <div>
            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Full Name / Client Name <span class="text-rose-500">*</span></label>
            <input type="text" id="cora-new-lead-names" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none" placeholder="e.g. Vikramaditya Singhania">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                <input type="email" id="cora-new-lead-email" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none" placeholder="vikram@singhania.com">
            </div>
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Phone / WhatsApp</label>
                <input type="tel" id="cora-new-lead-phone" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none" placeholder="+91 98765 43210" oninput="this.value = this.value.replace(/[^0-9+\-\s()]/g, '')">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Estimated Budget (₹)</label>
                <input type="text" id="cora-new-lead-price" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none" placeholder="e.g. 150000" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Initial Stage</label>
                <select id="cora-new-lead-stage" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                    <option value="New Lead">New Lead</option>
                    <option value="Contacted">Proposal Sent</option>
                    <option value="Site Visit">Site Visit / Viewing</option>
                    <option value="Negotiation">Negotiation</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Scope / Property Type</label>
                <input type="text" id="cora-new-lead-scale" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none" placeholder="e.g. Commercial Villa / Studio Shoot">
            </div>
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">City / Location</label>
                <input type="text" id="cora-new-lead-city" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none" placeholder="e.g. Mumbai, BKC">
            </div>
        </div>

        <div>
            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Temperature / Priority</label>
            <select id="cora-new-lead-score" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                <option value="warm">Warm ☀️ (Standard Interest)</option>
                <option value="hot">Hot 🔥 (High Intent / Urgency)</option>
                <option value="cold">Cold ❄️ (Low Priority)</option>
            </select>
        </div>

        <div>
            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Inquiry Notes</label>
            <textarea id="cora-new-lead-notes" rows="3" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none resize-none" placeholder="Add any background notes or specific requirements..."></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800">
            <button type="button" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold rounded-lg text-xs cursor-pointer" onclick="window.coraCloseAllDrawers()">
                Cancel
            </button>
            <button type="submit" class="px-5 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-lg text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-sm">
                Create Lead
            </button>
        </div>
    </form>
</aside>

<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 3: SCHEDULE FOLLOW-UP TASK                            -->
<!-- ========================================================================= -->
<aside id="cora-lead-schedule-drawer" class="cora-side-drawer fixed top-0 right-0 w-[450px] max-w-[90vw] h-full bg-white dark:bg-zinc-900 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 dark:border-zinc-800 flex flex-col font-sans">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-50/50 dark:bg-zinc-850/50">
        <div>
            <h3 class="font-extrabold text-base text-zinc-900 dark:text-white">Schedule Follow-Up Action</h3>
            <p class="text-xs text-zinc-400 mt-0.5">Set reminders or crew tasks for lead nurturing.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-800 dark:hover:text-white p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" onclick="window.coraCloseAllDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form class="p-6 overflow-y-auto flex-1 space-y-4 text-xs" onsubmit="event.preventDefault(); coraSubmitScheduleTask();">
        <input type="hidden" id="cora-task-lead-id" value="">

        <div>
            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Follow-Up Action Type</label>
            <select id="cora-task-action" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                <option value="call">Phone Call Nurture</option>
                <option value="proposal">Send Proposal / Estimate</option>
                <option value="viewing">Site Visit / Viewing</option>
                <option value="whatsapp">WhatsApp Quick Ping</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Target Date</label>
                <input type="date" id="cora-task-date" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Target Time</label>
                <input type="time" id="cora-task-time" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none" value="11:00">
            </div>
        </div>

        <div>
            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Assigned Team Member</label>
            <select id="cora-task-assignee" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                <option value="me">Assigned to Me</option>
                <?php foreach ($cora_users_list as $u) : ?>
                    <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Reminder Notes</label>
            <textarea id="cora-task-note" rows="3" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none resize-none" placeholder="Details for follow up..."></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800">
            <button type="button" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold rounded-lg text-xs cursor-pointer" onclick="window.coraCloseAllDrawers()">
                Cancel
            </button>
            <button type="submit" class="px-5 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-lg text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-sm">
                Save Task
            </button>
        </div>
    </form>
</aside>

<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 4: CUSTOMIZE PIPELINE STAGES & COLUMNS                 -->
<!-- ========================================================================= -->
<aside id="cora-lead-stages-drawer" class="cora-side-drawer fixed top-0 right-0 w-[540px] max-w-[90vw] h-full bg-white dark:bg-zinc-900 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 dark:border-zinc-800 flex flex-col font-sans">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-50/50 dark:bg-zinc-850/50">
        <div>
            <h3 class="font-extrabold text-base text-zinc-900 dark:text-white">Customize Pipeline Columns</h3>
            <p class="text-xs text-zinc-400 mt-0.5">Rename stage titles, toggle column visibility, or add custom stage columns.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-800 dark:hover:text-white p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" onclick="window.coraCloseAllDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-manage-stages-form" class="p-6 overflow-y-auto flex-1 space-y-4 text-xs" onsubmit="event.preventDefault(); coraSavePipelineStages();">
        <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
            <span class="font-bold text-zinc-800 dark:text-zinc-200">Pipeline Stage Workflow</span>
            <button type="button" class="text-xs font-bold text-zinc-900 dark:text-zinc-100 hover:underline cursor-pointer border-none bg-transparent" onclick="coraAddNewStageRow()">+ Add Stage Column</button>
        </div>

        <div id="cora-stages-list-container" class="space-y-3">
            <?php foreach ( $stages_config as $s_key => $s_val ) : ?>
            <div class="cora-stage-config-row p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/40 space-y-2 relative transition-all cursor-grab active:cursor-grabbing"
                 draggable="true"
                 data-key="<?php echo esc_attr($s_key); ?>"
                 ondragstart="coraStageRowDragStart(event)"
                 ondragover="coraStageRowDragOver(event)"
                 ondrop="coraStageRowDrop(event)"
                 ondragend="coraStageRowDragEnd(event)">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 flex-1">
                        <span class="text-zinc-400 cursor-grab font-bold text-xs select-none">⋮⋮</span>
                        <input type="text" class="cora-stage-label-input px-2.5 py-1.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg font-bold text-zinc-900 dark:text-zinc-100 text-xs w-full outline-none" value="<?php echo esc_attr($s_val['label'] ?? $s_key); ?>" placeholder="Stage Column Title">
                    </div>
                    <label class="flex items-center gap-1.5 text-[11px] text-zinc-500 font-semibold cursor-pointer">
                        <input type="checkbox" class="cora-stage-enable-checkbox accent-zinc-950 dark:accent-white" <?php echo ( ! isset($s_val['enabled']) || $s_val['enabled'] ) ? 'checked' : ''; ?>>
                        <span>Show</span>
                    </label>
                    <button type="button" class="text-zinc-400 hover:text-rose-600 transition-colors p-1 border-none bg-transparent cursor-pointer" onclick="jQuery(this).closest('.cora-stage-config-row').remove();">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="text-[10px] text-zinc-400 font-medium">Stage Key: <code class="font-mono text-zinc-600 dark:text-zinc-300"><?php echo esc_html($s_key); ?></code></span>
                    <select class="cora-stage-badge-select px-2 py-1 text-[10.5px] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded text-zinc-800 dark:text-zinc-200 outline-none">
                        <option value="bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800" <?php echo (strpos($s_val['badge'] ?? '', 'blue') !== false) ? 'selected' : ''; ?>>Blue Badge</option>
                        <option value="bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800" <?php echo (strpos($s_val['badge'] ?? '', 'amber') !== false) ? 'selected' : ''; ?>>Amber Badge</option>
                        <option value="bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800" <?php echo (strpos($s_val['badge'] ?? '', 'purple') !== false) ? 'selected' : ''; ?>>Purple Badge</option>
                        <option value="bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800" <?php echo (strpos($s_val['badge'] ?? '', 'indigo') !== false) ? 'selected' : ''; ?>>Indigo Badge</option>
                        <option value="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800" <?php echo (strpos($s_val['badge'] ?? '', 'emerald') !== false) ? 'selected' : ''; ?>>Emerald Badge</option>
                        <option value="bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800" <?php echo (strpos($s_val['badge'] ?? '', 'rose') !== false) ? 'selected' : ''; ?>>Rose Badge</option>
                        <option value="bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800" <?php echo (strpos($s_val['badge'] ?? '', 'zinc') !== false) ? 'selected' : ''; ?>>Zinc Badge</option>
                    </select>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="pt-4 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-800">
            <button type="button" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold rounded-lg text-xs cursor-pointer hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all" onclick="coraResetDefaultStages()">
                Reset to Default
            </button>
            <div class="flex items-center gap-2">
                <button type="button" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold rounded-lg text-xs cursor-pointer" onclick="window.coraCloseAllDrawers()">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-lg text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-sm">
                    Save Pipeline Columns
                </button>
            </div>
        </div>
    </form>
</aside>
