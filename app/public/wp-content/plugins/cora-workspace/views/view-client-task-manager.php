<?php
/**
 * Cora Platform — CRM Work & Task Manager
 *
 * Dedicated work management and operational task board for client deliverables,
 * team assignments, and milestone tracking. Features clean flat styling (no hover jumping,
 * no colored outlines), bidirectional client filtering, team assignee filtering, and timeframe view switcher.
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Detect Active Workspace & Industry Context
$active_industry = function_exists( 'cora_get_active_industry' ) ? cora_get_active_industry() : 'photography_studio';
if ( isset( $_GET['industry'] ) ) {
    $active_industry = sanitize_key( $_GET['industry'] );
}

$active_ws_ctx = function_exists( 'cora_get_current_workspace_context' ) ? cora_get_current_workspace_context() : null;
$ws_slug = $active_ws_ctx['slug'] ?? ( $active_ws_ctx['id'] ?? ( isset( $_GET['industry'] ) ? sanitize_title( $_GET['industry'] ) : 'studio' ) );

// 2. Industry-Specific Task Terminology Dictionaries
$industry_configs = array(
    'photography_studio' => array(
        'title'        => 'Task Manager',
        'description'  => 'Track, assign, and manage studio deliverables, retouching sprints, shoots, and client proofing tasks.',
        'metric_1'     => array('label' => 'Total Tasks', 'suffix' => 'across workflow', 'icon' => 'grid'),
        'metric_2'     => array('label' => 'In Execution', 'suffix' => 'active tasks', 'color' => 'amber'),
        'metric_3'     => array('label' => 'Under Review', 'suffix' => 'pending approval', 'color' => 'violet'),
        'metric_4'     => array('label' => 'Completed', 'suffix' => 'finished', 'color' => 'emerald'),
        'stages'       => array(
            'todo'        => array('label' => 'To Do / Backlog'),
            'in_progress' => array('label' => 'In Execution'),
            'review'      => array('label' => 'Client Review & QA'),
            'done'        => array('label' => 'Completed'),
        ),
        'categories'   => array('PHOTOSHOOT', 'POST_PRODUCTION', 'RETOUCHING', 'COLOR_GRADING', 'PORTAL_UPLOAD', 'ADMIN'),
    ),
    'real_estate' => array(
        'title'        => 'Property & Task Manager',
        'description'  => 'Track property milestones, site visits, client due diligence checklists, and closing documentation.',
        'metric_1'     => array('label' => 'Total Tasks', 'suffix' => 'across workflow', 'icon' => 'grid'),
        'metric_2'     => array('label' => 'In Progress', 'suffix' => 'active milestones', 'color' => 'amber'),
        'metric_3'     => array('label' => 'Due Diligence', 'suffix' => 'in verification', 'color' => 'violet'),
        'metric_4'     => array('label' => 'Completed', 'suffix' => 'cleared', 'color' => 'emerald'),
        'stages'       => array(
            'todo'        => array('label' => 'To Do / Scheduled'),
            'in_progress' => array('label' => 'In Progress'),
            'review'      => array('label' => 'Due Diligence & Legal'),
            'done'        => array('label' => 'Completed & Closed'),
        ),
        'categories'   => array('SITE_INSPECTION', 'ESCROW', 'LEGAL_VERIFICATION', 'CLIENT_TOUR', 'VALUATION', 'CLOSING'),
    ),
    'marketing_agency' => array(
        'title'        => 'Campaign & Task Manager',
        'description'  => 'Track creative sprints, ad campaign setup, client copy reviews, and retainer deliverables.',
        'metric_1'     => array('label' => 'Total Tasks', 'suffix' => 'across workflow', 'icon' => 'grid'),
        'metric_2'     => array('label' => 'Creative Sprint', 'suffix' => 'in execution', 'color' => 'amber'),
        'metric_3'     => array('label' => 'Client Sign-off', 'suffix' => 'awaiting review', 'color' => 'violet'),
        'metric_4'     => array('label' => 'Completed', 'suffix' => 'delivered', 'color' => 'emerald'),
        'stages'       => array(
            'todo'        => array('label' => 'Backlog / Scoping'),
            'in_progress' => array('label' => 'In Execution'),
            'review'      => array('label' => 'Client Review & QA'),
            'done'        => array('label' => 'Delivered'),
        ),
        'categories'   => array('AD_CREATIVE', 'COPYWRITING', 'SEO_AUDIT', 'SOCIAL_CALENDAR', 'ANALYTICS', 'BRAND_ASSETS'),
    ),
    'professional_services' => array(
        'title'        => 'Client Task Manager',
        'description'  => 'Manage client advisory tasks, legal filings, audit checklists, and document reviews.',
        'metric_1'     => array('label' => 'Total Tasks', 'suffix' => 'across workflow', 'icon' => 'grid'),
        'metric_2'     => array('label' => 'In Execution', 'suffix' => 'advisory tasks', 'color' => 'amber'),
        'metric_3'     => array('label' => 'Compliance Review', 'suffix' => 'in audit', 'color' => 'violet'),
        'metric_4'     => array('label' => 'Completed', 'suffix' => 'settled', 'color' => 'emerald'),
        'stages'       => array(
            'todo'        => array('label' => 'To Do / Intake'),
            'in_progress' => array('label' => 'In Execution'),
            'review'      => array('label' => 'Compliance & Audit'),
            'done'        => array('label' => 'Completed & Filed'),
        ),
        'categories'   => array('AUDIT', 'COMPLIANCE', 'TAX_FILING', 'LEGAL_BRIEF', 'CLIENT_ADVISORY', 'CONTRACT_REVIEW'),
    ),
    'custom' => array(
        'title'        => 'Task Manager',
        'description'  => 'Track workflows, milestones, team assignments, and deliverables across your workspace.',
        'metric_1'     => array('label' => 'Total Tasks', 'suffix' => 'across workflow', 'icon' => 'grid'),
        'metric_2'     => array('label' => 'In Progress', 'suffix' => 'ongoing', 'color' => 'amber'),
        'metric_3'     => array('label' => 'Under Review', 'suffix' => 'pending approval', 'color' => 'violet'),
        'metric_4'     => array('label' => 'Completed', 'suffix' => 'finished', 'color' => 'emerald'),
        'stages'       => array(
            'todo'        => array('label' => 'To Do / Backlog'),
            'in_progress' => array('label' => 'In Progress'),
            'review'      => array('label' => 'Review'),
            'done'        => array('label' => 'Completed'),
        ),
        'categories'   => array('GENERAL', 'DEVELOPMENT', 'DESIGN', 'MARKETING', 'OPERATIONS', 'DOCUMENTATION'),
    ),
);

$curr_cfg = $industry_configs[$active_industry] ?? $industry_configs['photography_studio'];

// 3. Client Work Tasks Dataset
$tasks_raw = get_option( 'cora_workspace_client_tasks', array() );
if ( empty( $tasks_raw ) || ! is_array( $tasks_raw ) ) {
    $tasks_raw = array(
        array(
            'id'            => 'task-101',
            'client_id'     => 1,
            'client_name'   => 'Rohan Verma',
            'title'         => 'Export 4K RAW Color Grading & Master Cuts',
            'category'      => 'POST_PRODUCTION',
            'status'        => 'in_progress',
            'priority'      => 'urgent',
            'assignee'      => 'Studio Admin',
            'assignee_init' => 'SA',
            'progress'      => 65,
            'subtasks'      => '3/5 done',
            'subtasks_pct'  => 60,
            'due_date'      => date( 'Y-m-d' ), // Today
            'phone'         => '+91 98201 45892',
            'email'         => 'rohan.verma@enterprise.com',
            'notes'         => 'Apply Davinci Resolve cinema LUTs and export final 4K ProRes masters for client review.',
        ),
        array(
            'id'            => 'task-102',
            'client_id'     => 2,
            'client_name'   => 'Kavya Patel',
            'title'         => 'Review & Sign NDA and Architectural Staging Agreement',
            'category'      => 'LEGAL_VERIFICATION',
            'status'        => 'review',
            'priority'      => 'high',
            'assignee'      => 'Studio Admin',
            'assignee_init' => 'SA',
            'progress'      => 80,
            'subtasks'      => '4/5 done',
            'subtasks_pct'  => 80,
            'due_date'      => date( 'Y-m-d', strtotime( '+1 day' ) ), // Tomorrow
            'phone'         => '+91 97112 34567',
            'email'         => 'kavya.patel@designstudio.in',
            'notes'         => 'Uploaded agreement draft to portal. Awaiting client signature on section 4.2.',
        ),
        array(
            'id'            => 'task-103',
            'client_id'     => 3,
            'client_name'   => 'Aarav Mehta',
            'title'         => 'Turntable Motorized Rig Calibration & 40 SKU Setup',
            'category'      => 'PHOTOSHOOT',
            'status'        => 'todo',
            'priority'      => 'medium',
            'assignee'      => 'Rohan Verma',
            'assignee_init' => 'RV',
            'progress'      => 20,
            'subtasks'      => '1/4 done',
            'subtasks_pct'  => 25,
            'due_date'      => date( 'Y-m-d', strtotime( '+3 days' ) ), // This week
            'phone'         => '+91 98334 78901',
            'email'         => 'aarav.mehta@lumina.co',
            'notes'         => 'Calibrate 360 spin turntable speed and adjust dual softbox overhead lighting angles.',
        ),
        array(
            'id'            => 'task-104',
            'client_id'     => 1,
            'client_name'   => 'Rohan Verma',
            'title'         => 'Executive Headshots Retouching Batch Sign-off',
            'category'      => 'RETOUCHING',
            'status'        => 'done',
            'priority'      => 'low',
            'assignee'      => 'Studio Admin',
            'assignee_init' => 'SA',
            'progress'      => 100,
            'subtasks'      => '5/5 done',
            'subtasks_pct'  => 100,
            'due_date'      => date( 'Y-m-d', strtotime( '-2 days' ) ), // Completed
            'phone'         => '+91 98201 45892',
            'email'         => 'rohan.verma@enterprise.com',
            'notes'         => 'All 12 executive portraits approved and delivered via client portal gallery.',
        ),
        array(
            'id'            => 'task-105',
            'client_id'     => 2,
            'client_name'   => 'Kavya Patel',
            'title'         => 'Drone Airspace Permit & Sunset Shoot Schedule',
            'category'      => 'SITE_INSPECTION',
            'status'        => 'todo',
            'priority'      => 'urgent',
            'assignee'      => 'Rohan Verma',
            'assignee_init' => 'RV',
            'progress'      => 10,
            'subtasks'      => '1/5 done',
            'subtasks_pct'  => 20,
            'due_date'      => date( 'Y-m-d', strtotime( '+7 days' ) ), // This month
            'phone'         => '+91 97112 34567',
            'email'         => 'kavya.patel@designstudio.in',
            'notes'         => 'File local municipal DGCA drone airspace permit for coastal high-rise exterior captures.',
        ),
    );
    update_option( 'cora_workspace_client_tasks', $tasks_raw, false );
}

$cora_task_ajax_nonce = wp_create_nonce( 'cora_ajax_nonce' );
$cora_task_ajax_url   = admin_url( 'admin-ajax.php' );

// Build Unique Client and Assignee Lists for Filter Dropdowns
$all_clients_list = array();
$all_assignees_list = array();
foreach ( $tasks_raw as $t_item ) {
    $cname = trim( $t_item['client_name'] ?? '' );
    if ( ! empty( $cname ) && ! in_array( $cname, $all_clients_list ) ) {
        $all_clients_list[] = $cname;
    }
    $aname = trim( $t_item['assignee'] ?? '' );
    if ( ! empty( $aname ) && ! in_array( $aname, $all_assignees_list ) ) {
        $all_assignees_list[] = $aname;
    }
}

// Task stage configurations with Pastel Tints & Solid Circle Icons
$task_stages = array(
    'todo' => array(
        'key'         => 'todo',
        'label'       => $curr_cfg['stages']['todo']['label'] ?? 'To Do / Backlog',
        'bg'          => '#f0fdf4',
        'border'      => '#bbf7d0',
        'icon_bg'     => 'bg-emerald-600',
        'text_accent' => 'text-emerald-700',
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="9"></circle></svg>',
    ),
    'in_progress' => array(
        'key'         => 'in_progress',
        'label'       => $curr_cfg['stages']['in_progress']['label'] ?? 'In Execution',
        'bg'          => '#fffbeb',
        'border'      => '#fde68a',
        'icon_bg'     => 'bg-amber-500',
        'text_accent' => 'text-amber-800',
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="9"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
    ),
    'review' => array(
        'key'         => 'review',
        'label'       => $curr_cfg['stages']['review']['label'] ?? 'Client Review & QA',
        'bg'          => '#f5f3ff',
        'border'      => '#ddd6fe',
        'icon_bg'     => 'bg-purple-600',
        'text_accent' => 'text-purple-800',
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
    ),
    'done' => array(
        'key'         => 'done',
        'label'       => $curr_cfg['stages']['done']['label'] ?? 'Completed',
        'bg'          => '#eff6ff',
        'border'      => '#bfdbfe',
        'icon_bg'     => 'bg-blue-600',
        'text_accent' => 'text-blue-800',
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    ),
);

$total_tasks_count = count( $tasks_raw );
$todo_count        = count( array_filter( $tasks_raw, function($t) { return ( $t['status'] ?? 'todo' ) === 'todo'; } ) );
$in_prog_count     = count( array_filter( $tasks_raw, function($t) { return ( $t['status'] ?? '' ) === 'in_progress'; } ) );
$review_count      = count( array_filter( $tasks_raw, function($t) { return ( $t['status'] ?? '' ) === 'review'; } ) );
$done_count        = count( array_filter( $tasks_raw, function($t) { return ( $t['status'] ?? '' ) === 'done'; } ) );

// Check URL param for pre-selected client
$initial_selected_client = isset( $_GET['client_name'] ) ? sanitize_text_field( $_GET['client_name'] ) : ( isset( $_GET['client_id'] ) ? sanitize_text_field( $_GET['client_id'] ) : 'all' );
?>

<style>
/* Clean Flat Task Kanban Board with Pastel Tints */
.cora-task-kanban-board {
    display: flex;
    gap: 0.875rem;
    overflow-x: auto;
    padding-bottom: 1.25rem;
    min-height: calc(100vh - 270px);
}
.cora-task-kanban-column {
    min-width: 300px;
    max-width: 310px;
    flex-shrink: 0;
    border-radius: 1.25rem;
    transition: all 0.2s ease;
}
.cora-task-card {
    background-color: #ffffff;
    border: 1px solid #ededf2;
    border-radius: 0.75rem;
    cursor: pointer;
    user-select: none;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: box-shadow 0.15s ease, transform 0.15s ease;
}
.cora-task-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
}
.cora-task-cards-container {
    min-height: 240px;
}
.cora-task-cards-container::-webkit-scrollbar {
    width: 4px;
}
.cora-task-cards-container::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.08);
    border-radius: 4px;
}

/* Responsive Drawers & Bottom Sheets (Rule 12 SOP) */
.cora-task-drawer {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    max-width: 460px;
    background: #ffffff;
    box-shadow: -4px 0 25px rgba(0, 0, 0, 0.12);
    z-index: 9999;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
@media (max-width: 639px) {
    .cora-task-drawer {
        top: auto;
        left: 0;
        right: 0;
        bottom: 0;
        max-width: 100%;
        max-height: 90vh;
        border-radius: 1.5rem 1.5rem 0 0;
        box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.15);
    }
    .cora-task-drawer.collapsed,
    .cora-task-drawer:not(.open) {
        transform: translateY(100%) !important;
    }
    .cora-task-drawer.open:not(.collapsed) {
        transform: translateY(0) !important;
        visibility: visible !important;
        display: flex !important;
        pointer-events: auto !important;
    }
}
@media (min-width: 640px) {
    .cora-task-drawer.collapsed,
    .cora-task-drawer:not(.open) {
        transform: translateX(100%) !important;
    }
    .cora-task-drawer.open:not(.collapsed) {
        transform: translateX(0) !important;
        visibility: visible !important;
        display: flex !important;
        pointer-events: auto !important;
    }
}
#cora-task-drawer-backdrop:not(.open) {
    pointer-events: none !important;
    visibility: hidden !important;
    display: none !important;
    opacity: 0 !important;
}
#cora-task-drawer-backdrop.open {
    visibility: visible !important;
    display: block !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}
</style>

<div id="cora-tasks-module" class="w-full flex-1 min-h-0 flex flex-col" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div class="flex-1 flex flex-col gap-2.5">

        <?php
        $tasks_header_args = array(
            'title'            => $curr_cfg['title'] ?? 'Task Manager',
            'description'      => $curr_cfg['description'] ?? 'Track, assign, and manage operational tasks and client deliverables across your team.',
            'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>',
            'ai_stack'         => true,
            'cta'              => array(
                'id'          => 'btn-add-task',
                'text'        => 'New Task',
                'mobile_text' => 'Task',
                'onclick'     => "window.openCreateTaskDrawer('todo')",
                'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
                'visible'     => true,
            ),
            'extra_actions_html' => '
                <button type="button" class="h-9 px-3.5 text-xs font-semibold text-zinc-800 bg-white hover:bg-zinc-50 border border-zinc-200/80 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs shrink-0 active:scale-95" onclick="window.coraExportTasksCSV()">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Export CSV</span>
                </button>
            ',
        );
        cora_render_workspace_header( $tasks_header_args );
        ?>

        <!-- Dynamic Work Management KPI Metric Chips -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
            <div class="bg-white border border-zinc-200/80 rounded-xl px-3 py-2 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider block" id="kpi-label-1"><?php echo esc_html( $curr_cfg['metric_1']['label'] ?? 'Total Tasks' ); ?></span>
                    <span class="text-base sm:text-lg font-black text-zinc-950 font-mono"><span id="metric-kpi-total-val"><?php echo esc_html( $total_tasks_count ); ?></span> <span class="text-[10px] font-medium text-zinc-400 font-sans"><?php echo esc_html( $curr_cfg['metric_1']['suffix'] ?? 'across workflow' ); ?></span></span>
                </div>
                <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-500">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>
                </div>
            </div>

            <div class="bg-white border border-zinc-200/80 rounded-xl px-3 py-2 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[9.5px] font-bold text-amber-600 uppercase tracking-wider block" id="kpi-label-2"><?php echo esc_html( $curr_cfg['metric_2']['label'] ?? 'In Execution' ); ?></span>
                    <span class="text-base sm:text-lg font-black text-amber-700 font-mono"><span id="metric-kpi-inprog-val"><?php echo esc_html( $in_prog_count ); ?></span> <span class="text-[10px] font-medium text-zinc-400 font-sans"><?php echo esc_html( $curr_cfg['metric_2']['suffix'] ?? 'active tasks' ); ?></span></span>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            </div>

            <div class="bg-white border border-zinc-200/80 rounded-xl px-3 py-2 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[9.5px] font-bold text-violet-600 uppercase tracking-wider block" id="kpi-label-3"><?php echo esc_html( $curr_cfg['metric_3']['label'] ?? 'Under Review' ); ?></span>
                    <span class="text-base sm:text-lg font-black text-violet-700 font-mono"><span id="metric-kpi-review-val"><?php echo esc_html( $review_count ); ?></span> <span class="text-[10px] font-medium text-zinc-400 font-sans"><?php echo esc_html( $curr_cfg['metric_3']['suffix'] ?? 'pending sign-off' ); ?></span></span>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
            </div>

            <div class="bg-white border border-zinc-200/80 rounded-xl px-3 py-2 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[9.5px] font-bold text-emerald-600 uppercase tracking-wider block" id="kpi-label-4"><?php echo esc_html( $curr_cfg['metric_4']['label'] ?? 'Completed' ); ?></span>
                    <span class="text-base sm:text-lg font-black text-emerald-700 font-mono"><span id="metric-kpi-done-count"><?php echo esc_html( $done_count ); ?></span> <span class="text-[10px] font-medium text-zinc-400 font-sans"><?php echo esc_html( $curr_cfg['metric_4']['suffix'] ?? 'finished' ); ?></span></span>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            </div>
        </div>

        <!-- Task Management Multi-Filter Toolbar -->
        <div class="flex flex-col gap-2 bg-white p-2.5 rounded-2xl border border-zinc-200/80 shadow-2xs">
            <!-- Row 1: Timeframe Segmented Controls & Dropdowns -->
            <div class="flex flex-wrap items-center justify-between gap-2">
                <!-- Timeframe View Switcher (This Week default, Today, Tomorrow, This Month, All Time, Custom) -->
                <div class="flex items-center gap-1 bg-zinc-100/80 p-0.5 rounded-xl border border-zinc-200/70 overflow-x-auto no-scrollbar">
                    <button type="button" onclick="window.coraSetTaskTimeframe('week', this)" class="task-timeframe-btn active px-3 py-1 rounded-lg text-xs font-bold transition-all bg-zinc-950 text-white shadow-2xs cursor-pointer border-0 whitespace-nowrap shrink-0" data-timeframe="week">
                        This Week
                    </button>
                    <button type="button" onclick="window.coraSetTaskTimeframe('today', this)" class="task-timeframe-btn px-2.5 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-white/80 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-timeframe="today">
                        Today
                    </button>
                    <button type="button" onclick="window.coraSetTaskTimeframe('tomorrow', this)" class="task-timeframe-btn px-2.5 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-white/80 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-timeframe="tomorrow">
                        Tomorrow
                    </button>
                    <button type="button" onclick="window.coraSetTaskTimeframe('month', this)" class="task-timeframe-btn px-2.5 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-white/80 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-timeframe="month">
                        This Month
                    </button>
                    <button type="button" onclick="window.coraSetTaskTimeframe('all', this)" class="task-timeframe-btn px-2.5 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-white/80 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-timeframe="all">
                        All Time
                    </button>
                    <button type="button" onclick="window.coraToggleCustomDatePicker(this)" id="btn-custom-timeframe" class="task-timeframe-btn px-2.5 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-white/80 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0 flex items-center gap-1" data-timeframe="custom">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>Custom Date</span>
                    </button>
                </div>

                <!-- Dropdown Selectors: Client & Assignee -->
                <div class="flex items-center gap-2">
                    <!-- Client Selector -->
                    <div class="relative flex items-center">
                        <label for="task-filter-client" class="text-[11px] font-bold text-zinc-400 mr-1.5 hidden sm:inline-block">Client:</label>
                        <select id="task-filter-client" onchange="window.coraFilterTaskClient(this.value)" class="h-8 pl-2.5 pr-7 text-xs font-medium bg-zinc-50 border border-zinc-200/90 rounded-xl text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer transition-all appearance-none shadow-2xs">
                            <option value="all">All Clients (Workspace)</option>
                            <?php foreach ( $all_clients_list as $cl_name ) : ?>
                                <option value="<?php echo esc_attr( strtolower( $cl_name ) ); ?>" <?php selected( strtolower( $initial_selected_client ), strtolower( $cl_name ) ); ?>>
                                    <?php echo esc_html( $cl_name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute right-2 text-zinc-400 pointer-events-none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>

                    <!-- Assignee Selector -->
                    <div class="relative flex items-center">
                        <label for="task-filter-assignee" class="text-[11px] font-bold text-zinc-400 mr-1.5 hidden sm:inline-block">Assignee:</label>
                        <select id="task-filter-assignee" onchange="window.coraFilterTaskAssignee(this.value)" class="h-8 pl-2.5 pr-7 text-xs font-medium bg-zinc-50 border border-zinc-200/90 rounded-xl text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer transition-all appearance-none shadow-2xs">
                            <option value="all">All Team Members</option>
                            <?php foreach ( $all_assignees_list as $as_name ) : ?>
                                <option value="<?php echo esc_attr( strtolower( $as_name ) ); ?>">
                                    <?php echo esc_html( $as_name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute right-2 text-zinc-400 pointer-events-none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>
            </div>

            <!-- Custom Date Range Drawer Tray (Revealed on Custom Date click) -->
            <div id="task-custom-date-tray" class="hidden pt-2 border-t border-zinc-100 flex flex-wrap items-center gap-3 text-xs">
                <span class="font-bold text-zinc-600">Select Date Window:</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-zinc-400 text-[11px]">From</span>
                    <input type="date" id="task-custom-start-date" class="h-7 px-2 bg-zinc-50 border border-zinc-200 rounded-lg text-xs text-zinc-800 outline-none">
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-zinc-400 text-[11px]">To</span>
                    <input type="date" id="task-custom-end-date" class="h-7 px-2 bg-zinc-50 border border-zinc-200 rounded-lg text-xs text-zinc-800 outline-none">
                </div>
                <button type="button" onclick="window.coraApplyCustomDateRange()" class="h-7 px-3 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 transition-all cursor-pointer">
                    Apply Filter
                </button>
            </div>

            <!-- Row 2: Priority Filters & Global Search -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-zinc-100">
                <!-- Priority Badges -->
                <div class="flex items-center gap-1 overflow-x-auto no-scrollbar">
                    <button type="button" onclick="window.coraFilterTaskPriority('all', this)" class="task-priority-filter-btn active px-2.5 py-1 rounded-md text-xs font-semibold transition-all bg-zinc-950 text-white shadow-2xs cursor-pointer border-0 whitespace-nowrap shrink-0" data-filter="all">
                        All Tasks (<span id="cora-tasks-total-pill"><?php echo esc_html( $total_tasks_count ); ?></span>)
                    </button>
                    <button type="button" onclick="window.coraFilterTaskPriority('urgent', this)" class="task-priority-filter-btn px-2.5 py-1 rounded-md text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-filter="urgent">
                        🔥 Urgent
                    </button>
                    <button type="button" onclick="window.coraFilterTaskPriority('high', this)" class="task-priority-filter-btn px-2.5 py-1 rounded-md text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-filter="high">
                        High Priority
                    </button>
                    <button type="button" onclick="window.coraFilterTaskPriority('medium', this)" class="task-priority-filter-btn px-2.5 py-1 rounded-md text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-filter="medium">
                        Medium
                    </button>
                </div>

                <!-- Global Live Task Search -->
                <div class="relative flex-1 max-w-xs">
                    <input type="text" id="task-search-input" oninput="window.coraSearchTasks(this.value)" placeholder="Search tasks, clients, deliverables..." class="w-full h-8 pl-8 pr-3 text-xs bg-zinc-50 border border-zinc-200/90 rounded-xl text-zinc-800 placeholder-zinc-400 outline-none focus:border-zinc-400 focus:bg-white transition-all shadow-2xs">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400 pointer-events-none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
            </div>

            <!-- Active Filter Status Bar -->
            <div id="cora-task-active-filter-chips" class="hidden flex items-center gap-1.5 pt-1 text-[11px] text-zinc-500 overflow-x-auto">
                <span class="font-bold text-zinc-400 text-[10px] uppercase">Active Filters:</span>
                <span id="chip-filter-client" class="hidden inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-zinc-900 text-white font-medium text-[10px]">
                    <span id="chip-filter-client-text">Client</span>
                    <button type="button" onclick="window.coraClearClientFilter()" class="hover:text-red-300 cursor-pointer bg-transparent border-0 text-white p-0">✕</button>
                </span>
                <span id="chip-filter-timeframe" class="hidden inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-800 font-medium text-[10px] border border-zinc-200">
                    <span id="chip-filter-timeframe-text">This Week</span>
                </span>
                <button type="button" onclick="window.coraResetAllTaskFilters()" class="text-zinc-500 hover:text-zinc-950 font-semibold underline text-[10px] ml-auto bg-transparent border-0 cursor-pointer">Reset All Filters</button>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════
             KANBAN WORKFLOW BOARD (4 PASTEL TINTED STAGES)
             ═══════════════════════════════════════════════════════════════════ -->
        <div class="cora-task-kanban-board" id="cora-task-kanban-board">
            <?php foreach ( $task_stages as $stage_key => $stage ) : 
                $col_tasks = array_filter( $tasks_raw, function($t) use ($stage_key) {
                    return ( $t['status'] ?? 'todo' ) === $stage_key;
                });
            ?>
            <div class="cora-task-kanban-column flex flex-col p-3"
                 style="background: <?php echo esc_attr( $stage['bg'] ); ?>; border: 1.5px solid <?php echo esc_attr( $stage['border'] ); ?>; border-radius: 1.25rem;"
                 data-status="<?php echo esc_attr( $stage_key ); ?>"
                 ondragover="window.coraTaskDragOver(event, this)"
                 ondrop="window.coraTaskDrop(event, this)">

                <!-- Column Header -->
                <div class="flex items-center justify-between gap-1.5 pb-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 <?php echo esc_attr( $stage['icon_bg'] ); ?> text-white shadow-xs">
                            <?php echo $stage['icon']; ?>
                        </div>
                        <span class="text-[11px] font-bold text-zinc-900 uppercase tracking-wider truncate">
                            <?php echo esc_html( $stage['label'] ); ?>
                        </span>
                        <span class="task-col-count w-5 h-5 rounded-full bg-white text-zinc-800 font-bold text-[11px] shadow-2xs flex items-center justify-center border border-zinc-100 shrink-0">
                            <?php echo count( $col_tasks ); ?>
                        </span>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" 
                                onclick="window.openCreateTaskDrawer('<?php echo esc_js( $stage_key ); ?>')"
                                title="Add Task"
                                class="w-6 h-6 rounded-lg bg-white/80 hover:bg-white text-zinc-600 hover:text-zinc-950 flex items-center justify-center cursor-pointer transition-all border border-zinc-200/50 shadow-2xs">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>
                </div>

                <!-- Sub-bar: Work Deliverables & Metric count -->
                <div class="flex items-center justify-between text-[10.5px] text-zinc-500 font-medium pt-1.5 pb-2 border-t border-zinc-200/60 mb-1">
                    <span>Work Deliverables</span>
                    <span class="<?php echo esc_attr( $stage['text_accent'] ); ?> font-bold">
                        <span class="task-col-metric-count"><?php echo count( $col_tasks ); ?></span> Tasks
                    </span>
                </div>

                <!-- Task Cards Scrollable Container -->
                <div class="cora-task-cards-container flex-1 flex flex-col gap-2.5 overflow-y-auto pr-0.5" id="col-container-<?php echo esc_attr( $stage_key ); ?>">
                    <?php if ( empty( $col_tasks ) ) : ?>
                        <div class="task-empty-placeholder py-8 px-3 text-center rounded-xl bg-white/60 border border-dashed border-zinc-200/80 flex flex-col items-center justify-center gap-1 text-zinc-400 select-none">
                            <span class="text-xs font-semibold text-zinc-500">No tasks</span>
                            <span class="text-[10px]">Click + to add a task</span>
                        </div>
                    <?php else : ?>
                        <?php foreach ( $col_tasks as $task ) : 
                            $priority = strtolower( $task['priority'] ?? 'medium' );
                            $badge_bg = 'bg-zinc-100 text-zinc-600';
                            if ( $priority === 'urgent' ) $badge_bg = 'bg-red-50 text-red-700 font-bold';
                            if ( $priority === 'high' )   $badge_bg = 'bg-amber-50 text-amber-800 font-bold';
                            if ( $priority === 'low' )    $badge_bg = 'bg-zinc-100 text-zinc-500';

                            $due_date = $task['due_date'] ?? date('Y-m-d');
                            $due_label = date( 'M j', strtotime( $due_date ) );
                            $is_today = ( $due_date === date( 'Y-m-d' ) );
                            $is_overdue = ( $due_date < date( 'Y-m-d' ) && ( $task['status'] ?? '' ) !== 'done' );
                            if ( $is_today ) $due_label = 'Today';
                            if ( $is_overdue ) $due_label = 'Overdue (' . date( 'M j', strtotime( $due_date ) ) . ')';
                        ?>
                            <div class="cora-task-card bg-white rounded-xl p-3.5 shadow-2xs border border-zinc-100 hover:shadow-xs flex flex-col gap-2 relative group overflow-hidden"
                                 draggable="true"
                                 data-id="<?php echo esc_attr( $task['id'] ); ?>"
                                 data-client="<?php echo esc_attr( $task['client_name'] ?? '' ); ?>"
                                 data-title="<?php echo esc_attr( $task['title'] ?? '' ); ?>"
                                 data-category="<?php echo esc_attr( $task['category'] ?? 'GENERAL' ); ?>"
                                 data-status="<?php echo esc_attr( $task['status'] ?? 'todo' ); ?>"
                                 data-priority="<?php echo esc_attr( $priority ); ?>"
                                 data-assignee="<?php echo esc_attr( $task['assignee'] ?? '' ); ?>"
                                 data-due-date="<?php echo esc_attr( $due_date ); ?>"
                                 data-progress="<?php echo esc_attr( $task['progress'] ?? 0 ); ?>"
                                 data-notes="<?php echo esc_attr( $task['notes'] ?? '' ); ?>"
                                 data-phone="<?php echo esc_attr( $task['phone'] ?? '' ); ?>"
                                 data-email="<?php echo esc_attr( $task['email'] ?? '' ); ?>"
                                 ondragstart="window.coraTaskDragStart(event, this)"
                                 ondragend="window.coraTaskDragEnd(event, this)"
                                 onclick="window.openTaskDrawer('<?php echo esc_js( $task['id'] ); ?>')">

                                <!-- Card Header: Client & Priority -->
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[10.5px] font-semibold text-zinc-500 truncate flex items-center gap-1">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <?php echo esc_html( $task['client_name'] ?? 'Client' ); ?>
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] uppercase <?php echo esc_attr( $badge_bg ); ?>">
                                        <?php echo esc_html( $priority ); ?>
                                    </span>
                                </div>

                                <!-- Card Title & Category Tag -->
                                <div>
                                    <h4 class="text-xs font-semibold text-zinc-900 leading-snug line-clamp-2">
                                        <?php echo esc_html( $task['title'] ?? 'Task' ); ?>
                                    </h4>
                                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                        <span class="px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-500 text-[8.5px] font-mono font-medium uppercase">
                                            <?php echo esc_html( $task['category'] ?? 'GENERAL' ); ?>
                                        </span>
                                        <span class="text-[9.5px] font-medium <?php echo $is_overdue ? 'text-red-600 font-bold' : ( $is_today ? 'text-amber-700 font-semibold' : 'text-zinc-400' ); ?> flex items-center gap-1">
                                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                            <?php echo esc_html( $due_label ); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Subtasks / Checklist Micro-Progress -->
                                <?php if ( ! empty( $task['subtasks'] ) ) : ?>
                                    <div class="w-full space-y-1">
                                        <div class="flex items-center justify-between text-[9px] text-zinc-400">
                                            <span>Subtasks: <?php echo esc_html( $task['subtasks'] ); ?></span>
                                            <span class="font-mono"><?php echo intval( $task['progress'] ?? 0 ); ?>%</span>
                                        </div>
                                        <div class="w-full h-1 bg-zinc-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-zinc-900 rounded-full" style="width: <?php echo intval( $task['progress'] ?? 0 ); ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Card Footer: Assignee Avatar & Quick Actions -->
                                <div class="flex items-center justify-between pt-2 border-t border-zinc-100 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-zinc-900 text-white font-bold text-[8.5px] flex items-center justify-center shrink-0">
                                            <?php echo esc_html( $task['assignee_init'] ?? 'SA' ); ?>
                                        </div>
                                        <span class="text-[10px] text-zinc-600 font-medium truncate max-w-[100px]">
                                            <?php echo esc_html( $task['assignee'] ?? 'Studio Admin' ); ?>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1" onclick="event.stopPropagation()">
                                        <?php if ( ! empty( $task['phone'] ) ) : ?>
                                            <a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $task['phone'] ) ); ?>" target="_blank" title="WhatsApp Client" class="w-5 h-5 rounded text-zinc-400 hover:text-emerald-600 flex items-center justify-center transition-colors">
                                                <svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor"><path d="M17.472 14.382c-.301-.15-1.78-.878-2.056-.979-.275-.1-.475-.15-.675.15-.2.3-.775.979-.95 1.18-.175.2-.35.225-.65.075-.3-.15-1.267-.467-2.414-1.489-.893-.796-1.496-1.78-1.671-2.08-.175-.3-.019-.462.131-.611.135-.134.3-.35.45-.525.15-.175.2-.3.3-.5.1-.2.05-.375-.025-.525-.075-.15-.675-1.625-.925-2.225-.244-.584-.492-.505-.675-.514-.175-.009-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.025-1.05 2.5s1.075 2.898 1.225 3.1c.15.2 2.115 3.23 5.125 4.53.716.31 1.275.495 1.71.633.72.228 1.375.196 1.893.118.577-.087 1.78-.727 2.03-1.428.25-.7.25-1.3.175-1.428-.075-.128-.275-.203-.575-.353zM12.04 2C6.516 2 2.022 6.49 2.022 12c0 1.954.563 3.78 1.541 5.334L2 22l4.81-1.523A9.972 9.972 0 0 0 12.04 22c5.523 0 10.018-4.49 10.018-10S17.563 2 12.04 2zm0 18.232c-1.62 0-3.13-.48-4.404-1.312l-.316-.208-2.854.903.92-2.78-.205-.327A8.212 8.212 0 0 1 3.822 12c0-4.53 3.687-8.216 8.218-8.216 4.53 0 8.218 3.686 8.218 8.216 0 4.53-3.688 8.232-8.218 8.232z"/></svg>
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" onclick="window.openTaskDrawer('<?php echo esc_js( $task['id'] ); ?>')" title="Task Details" class="w-5 h-5 rounded text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-colors border-0 bg-transparent cursor-pointer">
                                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Sticky Quick Add Task Footer -->
                <button type="button" 
                        onclick="window.openCreateTaskDrawer('<?php echo esc_js( $stage_key ); ?>')"
                        class="w-full mt-2.5 py-2 px-2 rounded-xl text-zinc-600 hover:text-zinc-950 bg-white/70 hover:bg-white text-[11px] font-semibold transition-all flex items-center justify-center gap-1.5 cursor-pointer border border-zinc-200/50 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Add Task</span>
                </button>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     RESPONSIVE TASK DETAILS DRAWER (Bottom Sheet on Mobile)
     ═══════════════════════════════════════════════════════════════════ -->
<div id="cora-task-drawer-backdrop" onclick="window.closeTaskDrawer()" class="fixed inset-0 bg-black/30 z-[9990] transition-opacity duration-200 opacity-0 pointer-events-none"></div>

<aside id="cora-task-drawer" class="cora-task-drawer collapsed flex flex-col overflow-hidden pointer-events-none">
    <!-- Mobile Drag Handle -->
    <div class="sm:hidden w-10 h-1 bg-zinc-300 rounded-full mx-auto mt-2.5 mb-1 shrink-0"></div>

    <!-- Drawer Header -->
    <div class="h-14 sm:h-16 px-4 sm:px-6 border-b border-zinc-200/90 flex items-center justify-between shrink-0 bg-white">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-600 font-mono font-bold text-[10px]" id="drawer-task-id">#TASK-101</span>
            <h3 id="drawer-task-title" class="text-xs sm:text-sm font-bold text-zinc-950 truncate">Task Title</h3>
        </div>
        <button id="btn-close-task-drawer" onclick="window.closeTaskDrawer()" class="w-8 h-8 rounded-lg hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 flex items-center justify-center cursor-pointer transition-colors border-0 bg-transparent">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Drawer Content -->
    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-5">
        <!-- Client & Context Card -->
        <div class="bg-zinc-50 border border-zinc-200/80 rounded-2xl p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider block">Assigned Client</span>
                    <span id="drawer-task-client" class="text-sm font-bold text-zinc-950">Rohan Verma</span>
                </div>
                <div class="text-right">
                    <span class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider block">Category</span>
                    <span id="drawer-task-category" class="px-2 py-0.5 rounded-md bg-zinc-200/70 text-zinc-800 font-bold text-[9px] uppercase font-mono">POST_PRODUCTION</span>
                </div>
            </div>
            <div class="flex items-center justify-between text-xs pt-2 border-t border-zinc-200/60">
                <span class="text-zinc-600 font-medium">Due Date: <strong id="drawer-task-due-date" class="text-zinc-950 font-mono">2026-09-18</strong></span>
                <span class="text-zinc-600 font-medium">Priority: <strong id="drawer-task-priority" class="text-zinc-950 uppercase">HIGH</strong></span>
            </div>
        </div>

        <!-- Progress Tracker -->
        <div class="space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-zinc-700">Subtask Execution Progress</span>
                <span id="drawer-task-progress-pct" class="font-mono font-bold text-zinc-900">65%</span>
            </div>
            <div class="w-full h-2 bg-zinc-100 rounded-full overflow-hidden">
                <div id="drawer-task-progress-bar" class="h-full bg-zinc-950 rounded-full transition-all" style="width: 65%;"></div>
            </div>
        </div>

        <!-- Task Stage Switcher -->
        <div class="space-y-2">
            <label class="block text-xs font-bold text-zinc-700">Workflow Stage</label>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" onclick="window.coraUpdateTaskStage('todo')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-400 text-left font-semibold cursor-pointer">
                    <span class="block text-[10px] text-zinc-400">STAGE 1</span>
                    To Do / Backlog
                </button>
                <button type="button" onclick="window.coraUpdateTaskStage('in_progress')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-400 text-left font-semibold cursor-pointer">
                    <span class="block text-[10px] text-amber-500">STAGE 2</span>
                    In Execution
                </button>
                <button type="button" onclick="window.coraUpdateTaskStage('review')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-400 text-left font-semibold cursor-pointer">
                    <span class="block text-[10px] text-violet-500">STAGE 3</span>
                    Review & QA
                </button>
                <button type="button" onclick="window.coraUpdateTaskStage('done')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-400 text-left font-semibold cursor-pointer">
                    <span class="block text-[10px] text-emerald-500">STAGE 4</span>
                    Completed
                </button>
            </div>
        </div>

        <!-- Description & Instructions -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-zinc-700">Task Notes & Scope</label>
            <div id="drawer-task-notes" class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50/60 text-xs text-zinc-700 leading-relaxed min-h-[70px]">
                No notes provided.
            </div>
        </div>

        <!-- Direct Outreach to Client -->
        <div class="space-y-2">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Client Communications</span>
            <div class="flex items-center gap-2">
                <a id="drawer-task-wa-btn" href="#" target="_blank" class="flex-1 py-2.5 rounded-xl border border-zinc-200 hover:bg-zinc-50 text-zinc-900 text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-decoration-none">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" class="text-emerald-600"><path d="M17.472 14.382c-.301-.15-1.78-.878-2.056-.979-.275-.1-.475-.15-.675.15-.2.3-.775.979-.95 1.18-.175.2-.35.225-.65.075-.3-.15-1.267-.467-2.414-1.489-.893-.796-1.496-1.78-1.671-2.08-.175-.3-.019-.462.131-.611.135-.134.3-.35.45-.525.15-.175.2-.3.3-.5.1-.2.05-.375-.025-.525-.075-.15-.675-1.625-.925-2.225-.244-.584-.492-.505-.675-.514-.175-.009-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.025-1.05 2.5s1.075 2.898 1.225 3.1c.15.2 2.115 3.23 5.125 4.53.716.31 1.275.495 1.71.633.72.228 1.375.196 1.893.118.577-.087 1.78-.727 2.03-1.428.25-.7.25-1.3.175-1.428-.075-.128-.275-.203-.575-.353zM12.04 2C6.516 2 2.022 6.49 2.022 12c0 1.954.563 3.78 1.541 5.334L2 22l4.81-1.523A9.972 9.972 0 0 0 12.04 22c5.523 0 10.018-4.49 10.018-10S17.563 2 12.04 2zm0 18.232c-1.62 0-3.13-.48-4.404-1.312l-.316-.208-2.854.903.92-2.78-.205-.327A8.212 8.212 0 0 1 3.822 12c0-4.53 3.687-8.216 8.218-8.216 4.53 0 8.218 3.686 8.218 8.216 0 4.53-3.688 8.232-8.218 8.232z"/></svg>
                    <span>WhatsApp Client</span>
                </a>
                <a id="drawer-task-call-btn" href="#" class="flex-1 py-2.5 rounded-xl border border-zinc-200 hover:bg-zinc-50 text-zinc-900 text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-decoration-none">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    <span>Call Client</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Drawer Footer Actions -->
    <div class="p-4 sm:p-6 border-t border-zinc-200/90 bg-zinc-50/80 flex items-center justify-between gap-2 shrink-0">
        <button type="button" onclick="window.coraDeleteActiveTask()" class="px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-xl transition-colors border border-transparent hover:border-red-200 cursor-pointer">
            Delete Task
        </button>
        <button type="button" onclick="window.closeTaskDrawer()" class="px-4 py-2 text-xs font-bold text-white bg-zinc-950 hover:bg-zinc-800 rounded-xl transition-all cursor-pointer shadow-2xs">
            Done
        </button>
    </div>
</aside>

<!-- ═══════════════════════════════════════════════════════════════════
     NEW TASK CREATION DRAWER SHEET (Bottom Sheet on Mobile)
     ═══════════════════════════════════════════════════════════════════ -->
<aside id="cora-create-task-drawer" class="cora-task-drawer collapsed flex flex-col overflow-hidden pointer-events-none">
    <!-- Mobile Drag Handle -->
    <div class="sm:hidden w-10 h-1 bg-zinc-300 rounded-full mx-auto mt-2.5 mb-1 shrink-0"></div>

    <div class="h-14 sm:h-16 px-4 sm:px-6 border-b border-zinc-200/90 flex items-center justify-between shrink-0 bg-white">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-zinc-950 text-white flex items-center justify-center font-bold text-xs">
                +
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-zinc-950">New Client Task</h3>
                <p class="text-[10px] text-zinc-400">Assign operational work item to team</p>
            </div>
        </div>
        <button id="btn-close-create-task" onclick="window.closeCreateTaskDrawer()" class="w-8 h-8 rounded-lg hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 flex items-center justify-center cursor-pointer transition-colors border-0 bg-transparent">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-create-task-form" onsubmit="window.coraCreateTaskSubmit(event)" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 text-xs">
        <input type="hidden" id="create-task-stage" value="todo">

        <div>
            <label class="block font-bold text-zinc-700 mb-1">Client Name *</label>
            <input type="text" id="create-task-client" required placeholder="e.g. Rohan Verma, Kavya Patel" class="w-full h-9 px-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 outline-none focus:border-zinc-400 focus:bg-white transition-all">
        </div>

        <div>
            <label class="block font-bold text-zinc-700 mb-1">Task Title & Scope *</label>
            <input type="text" id="create-task-title" required placeholder="e.g. Export final 4K masters and color grading" class="w-full h-9 px-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 outline-none focus:border-zinc-400 focus:bg-white transition-all">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-700 mb-1">Category</label>
                <select id="create-task-category" class="w-full h-9 px-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer">
                    <?php foreach ( ( $curr_cfg['categories'] ?? array('GENERAL', 'DELIVERABLE', 'REVIEW') ) as $cat ) : ?>
                        <option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block font-bold text-zinc-700 mb-1">Priority</label>
                <select id="create-task-priority" class="w-full h-9 px-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer">
                    <option value="urgent">🔥 Urgent</option>
                    <option value="high" selected>High Priority</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-700 mb-1">Assignee</label>
                <select id="create-task-assignee" class="w-full h-9 px-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer">
                    <option value="Studio Admin">Studio Admin</option>
                    <option value="Rohan Verma">Rohan Verma</option>
                    <option value="Kavya Patel">Kavya Patel</option>
                    <option value="Aarav Mehta">Aarav Mehta</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-zinc-700 mb-1">Due Date</label>
                <input type="date" id="create-task-due-date" value="<?php echo date('Y-m-d'); ?>" class="w-full h-9 px-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer">
            </div>
        </div>

        <div>
            <label class="block font-bold text-zinc-700 mb-1">Detailed Instructions & Notes</label>
            <textarea id="create-task-notes" rows="3" placeholder="Provide checklist requirements, format specifications, and deliverables..." class="w-full p-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 outline-none focus:border-zinc-400 focus:bg-white transition-all resize-none"></textarea>
        </div>

        <div class="pt-3 border-t border-zinc-100 flex items-center justify-end gap-2">
            <button type="button" onclick="window.closeCreateTaskDrawer()" class="px-3.5 py-2 rounded-xl border border-zinc-200 text-zinc-600 hover:bg-zinc-50 font-bold transition-all cursor-pointer">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 rounded-xl bg-zinc-950 text-white font-bold hover:bg-zinc-800 transition-all cursor-pointer shadow-2xs">
                Create Task
            </button>
        </div>
    </form>
</aside>

<script>
window.coraTasksData = <?php echo json_encode( array_values( $tasks_raw ) ); ?>;
window.coraActiveTask = null;
window.coraActiveDragCard = null;
window.coraAjaxUrl = <?php echo json_encode( $cora_task_ajax_url ); ?>;
window.coraAjaxNonce = <?php echo json_encode( $cora_task_ajax_nonce ); ?>;

// Multi-Dimensional Task Filter State
window.coraTaskFilterState = {
    timeframe: 'week',
    customStart: '',
    customEnd: '',
    client: '<?php echo esc_js( strtolower( $initial_selected_client ) ); ?>',
    assignee: 'all',
    priority: 'all',
    search: ''
};

// 1. Dynamic Column Placeholder Helper
window.coraSyncColumnPlaceholders = function() {
    document.querySelectorAll('.cora-task-kanban-column').forEach(function(col) {
        const container = col.querySelector('.cora-task-cards-container');
        if (!container) return;
        const visibleCards = container.querySelectorAll('.cora-task-card:not([style*="display: none"])');
        const existingPlaceholder = container.querySelector('.task-empty-placeholder');
        if (visibleCards.length === 0) {
            if (!existingPlaceholder) {
                const placeholder = document.createElement('div');
                placeholder.className = 'task-empty-placeholder py-8 px-3 text-center rounded-xl bg-white/60 border border-dashed border-zinc-200/80 flex flex-col items-center justify-center gap-1 text-zinc-400 select-none';
                placeholder.innerHTML = '<span class="text-xs font-semibold text-zinc-500">No tasks in this stage</span><span class="text-[10px]">Click + to add a task</span>';
                container.appendChild(placeholder);
            }
        } else {
            if (existingPlaceholder) existingPlaceholder.remove();
        }
    });
};

// 2. Drag & Drop Handlers with AJAX Persistence
window.coraTaskDragStart = function(e, card) {
    window.coraActiveDragCard = card;
    card.classList.add('opacity-40');
    if (e.dataTransfer) {
        e.dataTransfer.setData('text/plain', card.getAttribute('data-id') || '');
        e.dataTransfer.effectAllowed = 'move';
    }
};

window.coraTaskDragOver = function(e, column) {
    e.preventDefault();
    if (e.dataTransfer) e.dataTransfer.dropEffect = 'move';
    column.classList.add('ring-1', 'ring-zinc-400');
};

window.coraTaskDragLeave = function(e, column) {
    column.classList.remove('ring-1', 'ring-zinc-400');
};

window.coraTaskDrop = function(e, column) {
    e.preventDefault();
    column.classList.remove('ring-1', 'ring-zinc-400');
    
    const card = window.coraActiveDragCard;
    if (!card) return;

    const targetStatus = column.getAttribute('data-status');
    const prevStatus = card.getAttribute('data-status');
    const taskId = card.getAttribute('data-id');

    if (targetStatus && targetStatus !== prevStatus) {
        const sourceCol = document.querySelector(`.cora-task-kanban-column[data-status="${prevStatus}"]`);
        const sourceContainer = sourceCol ? sourceCol.querySelector('.cora-task-cards-container') : null;
        const targetContainer = column.querySelector('.cora-task-cards-container');

        // Optimistic DOM Update
        card.setAttribute('data-status', targetStatus);
        if (targetContainer) {
            const placeholder = targetContainer.querySelector('.task-empty-placeholder');
            if (placeholder) placeholder.remove();
            targetContainer.appendChild(card);
        }

        // Sync internal array store
        if (Array.isArray(window.coraTasksData)) {
            const tObj = window.coraTasksData.find(t => String(t.id) === String(taskId));
            if (tObj) tObj.status = targetStatus;
        }

        window.coraSyncColumnPlaceholders();
        window.coraApplyTaskFilters();

        // Dispatch background AJAX update
        const ajaxUrl = window.coraAjaxUrl || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxUrl) || '/wp-admin/admin-ajax.php';
        const nonce = window.coraAjaxNonce || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxNonce) || '';

        const formData = new URLSearchParams();
        formData.append('action', 'cora_update_task_status');
        formData.append('nonce', nonce);
        formData.append('task_id', taskId);
        formData.append('status', targetStatus);

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                if (window.coraShowToast) {
                    window.coraShowToast('Task moved to ' + targetStatus.replace('_', ' ').toUpperCase(), 'success');
                }
            } else {
                throw new Error((data && data.data) ? data.data : 'Status update failed');
            }
        })
        .catch(err => {
            console.error('Task status update failed:', err);
            // Rollback optimistic update on server error
            card.setAttribute('data-status', prevStatus);
            if (sourceContainer) {
                sourceContainer.appendChild(card);
            }
            if (Array.isArray(window.coraTasksData)) {
                const tObj = window.coraTasksData.find(t => String(t.id) === String(taskId));
                if (tObj) tObj.status = prevStatus;
            }
            window.coraSyncColumnPlaceholders();
            window.coraApplyTaskFilters();
            if (window.coraShowToast) {
                window.coraShowToast('Could not save task position. Reverted.', 'error');
            }
        });
    }
};

window.coraTaskDragEnd = function(e, card) {
    card.classList.remove('opacity-40');
    document.querySelectorAll('.cora-task-kanban-column').forEach(c => c.classList.remove('ring-1', 'ring-zinc-400'));
    window.coraActiveDragCard = null;
    window.coraSyncColumnPlaceholders();
};

// 2. Comprehensive Multi-Filter Engine
window.coraApplyTaskFilters = function() {
    const st = window.coraTaskFilterState;
    const now = new Date();
    const todayStr = now.toISOString().split('T')[0];
    
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];

    // Calculate Week boundaries (Mon to Sun)
    const dayOfWeek = now.getDay();
    const diffToMon = (dayOfWeek === 0 ? -6 : 1) - dayOfWeek;
    const monDate = new Date(now);
    monDate.setDate(now.getDate() + diffToMon);
    monDate.setHours(0,0,0,0);
    const sunDate = new Date(monDate);
    sunDate.setDate(monDate.getDate() + 6);
    sunDate.setHours(23,59,59,999);

    // Calculate Month boundaries
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
    const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59, 999);

    let totalVisible = 0;
    let inProgVisible = 0;
    let reviewVisible = 0;
    let doneVisible = 0;

    document.querySelectorAll('.cora-task-kanban-column').forEach(function(col) {
        const colStatus = col.getAttribute('data-status');
        const cards = col.querySelectorAll('.cora-task-card');
        let colVisibleCount = 0;

        cards.forEach(function(card) {
            const cardPri = (card.getAttribute('data-priority') || 'medium').toLowerCase();
            const cardClient = (card.getAttribute('data-client') || '').toLowerCase();
            const cardAssignee = (card.getAttribute('data-assignee') || '').toLowerCase();
            const cardDueDate = card.getAttribute('data-due-date') || todayStr;
            const cardText = (card.textContent || '').toLowerCase();

            let matches = true;

            // 1. Timeframe Filter
            if (st.timeframe === 'today') {
                if (cardDueDate !== todayStr) matches = false;
            } else if (st.timeframe === 'tomorrow') {
                if (cardDueDate !== tomorrowStr) matches = false;
            } else if (st.timeframe === 'week' || st.timeframe === 'this_week') {
                const cDate = new Date(cardDueDate + 'T00:00:00');
                if (cDate < monDate || cDate > sunDate) matches = false;
            } else if (st.timeframe === 'month' || st.timeframe === 'this_month') {
                const cDate = new Date(cardDueDate + 'T00:00:00');
                if (cDate < startOfMonth || cDate > endOfMonth) matches = false;
            } else if (st.timeframe === 'custom') {
                if (st.customStart && cardDueDate < st.customStart) matches = false;
                if (st.customEnd && cardDueDate > st.customEnd) matches = false;
            }

            // 2. Client Filter
            if (st.client && st.client !== 'all') {
                if (!cardClient.includes(st.client)) matches = false;
            }

            // 3. Assignee Filter
            if (st.assignee && st.assignee !== 'all') {
                if (!cardAssignee.includes(st.assignee)) matches = false;
            }

            // 4. Priority Filter
            if (st.priority === 'urgent') {
                if (cardPri !== 'urgent') matches = false;
            } else if (st.priority === 'high') {
                if (cardPri !== 'high' && cardPri !== 'urgent') matches = false;
            } else if (st.priority === 'medium') {
                if (cardPri !== 'medium') matches = false;
            }

            // 5. Search Filter
            if (st.search && !cardText.includes(st.search)) {
                matches = false;
            }

            if (matches) {
                card.style.display = '';
                colVisibleCount++;
                totalVisible++;

                if (colStatus === 'in_progress') inProgVisible++;
                if (colStatus === 'review') reviewVisible++;
                if (colStatus === 'done') doneVisible++;
            } else {
                card.style.display = 'none';
            }
        });

        const countPill = col.querySelector('.task-col-count');
        if (countPill) countPill.textContent = colVisibleCount;
        const metricCount = col.querySelector('.task-col-metric-count');
        if (metricCount) metricCount.textContent = colVisibleCount;
    });

    // Update Toolbar Total Count
    const totalPill = document.getElementById('cora-tasks-total-pill');
    if (totalPill) totalPill.textContent = totalVisible;

    // Update Top Dynamic Task KPI Cards
    const kpiTotal = document.getElementById('metric-kpi-total-val');
    if (kpiTotal) kpiTotal.textContent = totalVisible;

    const kpiInProg = document.getElementById('metric-kpi-inprog-val');
    if (kpiInProg) kpiInProg.textContent = inProgVisible;

    const kpiReview = document.getElementById('metric-kpi-review-val');
    if (kpiReview) kpiReview.textContent = reviewVisible;

    const kpiDone = document.getElementById('metric-kpi-done-count');
    if (kpiDone) kpiDone.textContent = doneVisible;

    // Update Active Filter Chips Bar
    window.coraRenderActiveFilterChips();
};

// 3. Timeframe Switching
window.coraSetTaskTimeframe = function(timeframe, btn) {
    document.querySelectorAll('.task-timeframe-btn').forEach(b => {
        b.classList.remove('active', 'bg-zinc-950', 'text-white');
        b.classList.add('text-zinc-600');
    });
    if (btn) {
        btn.classList.add('active', 'bg-zinc-950', 'text-white');
        btn.classList.remove('text-zinc-600');
    }

    const tray = document.getElementById('task-custom-date-tray');
    if (tray && timeframe !== 'custom') tray.classList.add('hidden');

    window.coraTaskFilterState.timeframe = timeframe;
    window.coraApplyTaskFilters();
};

window.coraToggleCustomDatePicker = function(btn) {
    const tray = document.getElementById('task-custom-date-tray');
    if (!tray) return;
    tray.classList.toggle('hidden');
    if (!tray.classList.contains('hidden')) {
        window.coraSetTaskTimeframe('custom', btn);
    }
};

window.coraApplyCustomDateRange = function() {
    const startInput = document.getElementById('task-custom-start-date');
    const endInput = document.getElementById('task-custom-end-date');
    window.coraTaskFilterState.customStart = startInput ? startInput.value : '';
    window.coraTaskFilterState.customEnd = endInput ? endInput.value : '';
    window.coraTaskFilterState.timeframe = 'custom';
    window.coraApplyTaskFilters();
    if (window.coraShowToast) window.coraShowToast('Custom date filter applied', 'success');
};

// 4. Client Selector (Bidirectional)
window.coraFilterTaskClient = function(clientName) {
    window.coraTaskFilterState.client = (clientName || 'all').toLowerCase();
    window.coraApplyTaskFilters();
};

window.coraClearClientFilter = function() {
    window.coraTaskFilterState.client = 'all';
    const sel = document.getElementById('task-filter-client');
    if (sel) sel.value = 'all';
    window.coraApplyTaskFilters();
};

// 5. Assignee Filter
window.coraFilterTaskAssignee = function(assigneeName) {
    window.coraTaskFilterState.assignee = (assigneeName || 'all').toLowerCase();
    window.coraApplyTaskFilters();
};

// 6. Priority Filter
window.coraFilterTaskPriority = function(priority, btn) {
    document.querySelectorAll('.task-priority-filter-btn').forEach(b => {
        b.classList.remove('active', 'bg-zinc-950', 'text-white');
        b.classList.add('text-zinc-600');
    });
    if (btn) {
        btn.classList.add('active', 'bg-zinc-950', 'text-white');
        btn.classList.remove('text-zinc-600');
    }
    window.coraTaskFilterState.priority = priority;
    window.coraApplyTaskFilters();
};

// 7. Search Filter
window.coraSearchTasks = function(query) {
    window.coraTaskFilterState.search = (query || '').trim().toLowerCase();
    window.coraApplyTaskFilters();
};

// 8. Active Filter Chips Display
window.coraRenderActiveFilterChips = function() {
    const st = window.coraTaskFilterState;
    const bar = document.getElementById('cora-task-active-filter-chips');
    if (!bar) return;

    let hasActiveFilters = false;

    // Client Chip
    const clientChip = document.getElementById('chip-filter-client');
    const clientChipText = document.getElementById('chip-filter-client-text');
    if (clientChip && clientChipText) {
        if (st.client && st.client !== 'all') {
            clientChipText.textContent = 'Client: ' + st.client.toUpperCase();
            clientChip.classList.remove('hidden');
            hasActiveFilters = true;
        } else {
            clientChip.classList.add('hidden');
        }
    }

    // Timeframe Chip
    const timeChip = document.getElementById('chip-filter-timeframe');
    const timeChipText = document.getElementById('chip-filter-timeframe-text');
    if (timeChip && timeChipText) {
        if (st.timeframe !== 'week') {
            let label = st.timeframe.replace('_', ' ').toUpperCase();
            if (st.timeframe === 'custom') label = (st.customStart || 'Start') + ' → ' + (st.customEnd || 'End');
            timeChipText.textContent = 'Range: ' + label;
            timeChip.classList.remove('hidden');
            hasActiveFilters = true;
        } else {
            timeChip.classList.add('hidden');
        }
    }

    if (hasActiveFilters) {
        bar.classList.remove('hidden');
    } else {
        bar.classList.add('hidden');
    }
};

// 9. Reset All Filters
window.coraResetAllTaskFilters = function() {
    window.coraTaskFilterState = {
        timeframe: 'week',
        customStart: '',
        customEnd: '',
        client: 'all',
        assignee: 'all',
        priority: 'all',
        search: ''
    };

    const clientSel = document.getElementById('task-filter-client');
    if (clientSel) clientSel.value = 'all';

    const assignSel = document.getElementById('task-filter-assignee');
    if (assignSel) assignSel.value = 'all';

    const searchInp = document.getElementById('task-search-input');
    if (searchInp) searchInp.value = '';

    const weekBtn = document.querySelector('.task-timeframe-btn[data-timeframe="week"]');
    if (weekBtn) window.coraSetTaskTimeframe('week', weekBtn);

    const allPriBtn = document.querySelector('.task-priority-filter-btn[data-filter="all"]');
    if (allPriBtn) window.coraFilterTaskPriority('all', allPriBtn);

    if (window.coraShowToast) window.coraShowToast('All filters reset to default', 'success');
};

// 10. Task Details Drawer
window.openTaskDrawer = function(taskId) {
    const taskCard = document.querySelector(`.cora-task-card[data-id="${taskId}"]`);
    if (!taskCard) return;

    window.coraActiveTask = {
        id: taskId,
        client: taskCard.getAttribute('data-client'),
        title: taskCard.getAttribute('data-title'),
        category: taskCard.getAttribute('data-category'),
        status: taskCard.getAttribute('data-status'),
        priority: taskCard.getAttribute('data-priority'),
        assignee: taskCard.getAttribute('data-assignee'),
        dueDate: taskCard.getAttribute('data-due-date'),
        notes: taskCard.getAttribute('data-notes'),
        progress: taskCard.getAttribute('data-progress'),
        phone: taskCard.getAttribute('data-phone') || '+91 98201 45892',
        email: taskCard.getAttribute('data-email') || 'client@example.com'
    };

    document.getElementById('drawer-task-id').textContent = '#' + taskId.toUpperCase();
    document.getElementById('drawer-task-title').textContent = window.coraActiveTask.title;
    document.getElementById('drawer-task-client').textContent = window.coraActiveTask.client;
    document.getElementById('drawer-task-category').textContent = window.coraActiveTask.category;
    document.getElementById('drawer-task-due-date').textContent = window.coraActiveTask.dueDate;
    document.getElementById('drawer-task-priority').textContent = window.coraActiveTask.priority.toUpperCase();
    document.getElementById('drawer-task-notes').textContent = window.coraActiveTask.notes || 'No extra notes provided.';
    document.getElementById('drawer-task-progress-pct').textContent = window.coraActiveTask.progress + '%';
    document.getElementById('drawer-task-progress-bar').style.width = window.coraActiveTask.progress + '%';

    const cleanPhone = window.coraActiveTask.phone.replace(/[^0-9]/g, '');
    document.getElementById('drawer-task-wa-btn').href = 'https://wa.me/' + cleanPhone;
    document.getElementById('drawer-task-call-btn').href = 'tel:' + window.coraActiveTask.phone;

    const drawer = document.getElementById('cora-task-drawer');
    drawer.classList.remove('pointer-events-none', 'collapsed');
    drawer.classList.add('open');

    const backdrop = document.getElementById('cora-task-drawer-backdrop');
    backdrop.classList.remove('pointer-events-none');
    backdrop.classList.add('open');
};

window.closeTaskDrawer = function() {
    const drawer = document.getElementById('cora-task-drawer');
    drawer.classList.add('pointer-events-none', 'collapsed');
    drawer.classList.remove('open');

    const backdrop = document.getElementById('cora-task-drawer-backdrop');
    backdrop.classList.add('pointer-events-none');
    backdrop.classList.remove('open');
    window.coraActiveTask = null;
};

// 11. Create Task Drawer Sheet
window.openCreateTaskDrawer = function(stage) {
    document.getElementById('create-task-stage').value = stage || 'todo';
    document.getElementById('cora-create-task-form').reset();
    const drawer = document.getElementById('cora-create-task-drawer');
    drawer.classList.remove('pointer-events-none', 'collapsed');
    drawer.classList.add('open');

    const backdrop = document.getElementById('cora-task-drawer-backdrop');
    backdrop.classList.remove('pointer-events-none');
    backdrop.classList.add('open');
};

window.closeCreateTaskDrawer = function() {
    const drawer = document.getElementById('cora-create-task-drawer');
    drawer.classList.add('pointer-events-none', 'collapsed');
    drawer.classList.remove('open');

    const backdrop = document.getElementById('cora-task-drawer-backdrop');
    backdrop.classList.add('pointer-events-none');
    backdrop.classList.remove('open');
};

window.coraCreateTaskSubmit = function(e) {
    e.preventDefault();
    const client = document.getElementById('create-task-client').value.trim();
    const title = document.getElementById('create-task-title').value.trim();
    const category = document.getElementById('create-task-category').value;
    const priority = document.getElementById('create-task-priority').value;
    const assignee = document.getElementById('create-task-assignee').value;
    const dueDate = document.getElementById('create-task-due-date').value || '<?php echo date('Y-m-d'); ?>';
    const notes = document.getElementById('create-task-notes').value.trim();
    const stage = document.getElementById('create-task-stage').value || 'todo';

    const newId = 'task-' + Date.now().toString().slice(-4);
    const taskPayload = {
        id: newId,
        client_name: client,
        title: title,
        category: category,
        status: stage,
        priority: priority,
        assignee_name: assignee,
        assignee: assignee,
        due_date: dueDate,
        notes: notes,
        progress: 0,
        phone: '+91 98201 45892',
        email: 'client@example.com'
    };

    const targetCol = document.querySelector(`.cora-task-kanban-column[data-status="${stage}"]`);
    if (targetCol) {
        const container = targetCol.querySelector('.cora-task-cards-container');
        if (container) {
            const placeholder = container.querySelector('.task-empty-placeholder');
            if (placeholder) placeholder.remove();

            const card = document.createElement('div');
            card.className = 'cora-task-card bg-white rounded-xl p-3.5 shadow-2xs border border-zinc-100 hover:shadow-xs flex flex-col gap-2 relative group overflow-hidden';
            card.draggable = true;
            card.setAttribute('data-id', newId);
            card.setAttribute('data-client', client);
            card.setAttribute('data-title', title);
            card.setAttribute('data-category', category);
            card.setAttribute('data-status', stage);
            card.setAttribute('data-priority', priority);
            card.setAttribute('data-assignee', assignee);
            card.setAttribute('data-due-date', dueDate);
            card.setAttribute('data-progress', '0');
            card.setAttribute('data-notes', notes);
            card.setAttribute('data-phone', '+91 98201 45892');
            card.setAttribute('data-email', 'client@example.com');
            card.setAttribute('ondragstart', 'window.coraTaskDragStart(event, this)');
            card.setAttribute('ondragend', 'window.coraTaskDragEnd(event, this)');
            card.setAttribute('onclick', `window.openTaskDrawer('${newId}')`);

            let badgeBg = 'bg-zinc-100 text-zinc-600';
            if (priority === 'urgent') badgeBg = 'bg-red-50 text-red-700 font-bold';
            if (priority === 'high') badgeBg = 'bg-amber-50 text-amber-800 font-bold';
            if (priority === 'low') badgeBg = 'bg-zinc-100 text-zinc-500';

            const initials = assignee.split(' ').map(n => n[0]).join('').slice(0,2).toUpperCase();

            card.innerHTML = `
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10.5px] font-semibold text-zinc-500 truncate flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        ${client}
                    </span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] uppercase ${badgeBg}">
                        ${priority}
                    </span>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-zinc-900 leading-snug line-clamp-2">${title}</h4>
                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                        <span class="px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-500 text-[8.5px] font-mono font-medium uppercase">${category}</span>
                        <span class="text-[9.5px] font-medium text-zinc-400 flex items-center gap-1">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                            ${dueDate}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-zinc-100 text-xs">
                    <div class="flex items-center gap-1.5">
                        <div class="w-5 h-5 rounded-full bg-zinc-900 text-white font-bold text-[8.5px] flex items-center justify-center shrink-0">${initials}</div>
                        <span class="text-[10px] text-zinc-600 font-medium truncate max-w-[100px]">${assignee}</span>
                    </div>
                </div>
            `;

            container.appendChild(card);
        }
    }

    if (Array.isArray(window.coraTasksData)) {
        window.coraTasksData.push(taskPayload);
    }

    window.closeCreateTaskDrawer();
    window.coraSyncColumnPlaceholders();
    window.coraApplyTaskFilters();

    // Persist to Server via AJAX
    const ajaxUrl = window.coraAjaxUrl || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxUrl) || '/wp-admin/admin-ajax.php';
    const nonce = window.coraAjaxNonce || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxNonce) || '';

    const formData = new URLSearchParams();
    formData.append('action', 'cora_save_client_task');
    formData.append('nonce', nonce);
    formData.append('task', JSON.stringify(taskPayload));

    fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) {
            if (window.coraShowToast) window.coraShowToast('New task created and saved', 'success');
        } else {
            throw new Error((data && data.data) ? data.data : 'Server rejected task creation');
        }
    })
    .catch(err => {
        console.error('Task creation save failed:', err);
    });
};

// 12. Update Task Stage from Drawer with AJAX Persistence
window.coraUpdateTaskStage = function(newStage) {
    if (!window.coraActiveTask) return;
    const taskId = window.coraActiveTask.id;
    const card = document.querySelector(`.cora-task-card[data-id="${taskId}"]`);
    if (!card) return;

    const prevStage = card.getAttribute('data-status');
    const targetCol = document.querySelector(`.cora-task-kanban-column[data-status="${newStage}"]`);
    if (targetCol) {
        const container = targetCol.querySelector('.cora-task-cards-container');
        if (container) {
            const placeholder = container.querySelector('.task-empty-placeholder');
            if (placeholder) placeholder.remove();
            card.setAttribute('data-status', newStage);
            container.appendChild(card);
        }
    }

    if (Array.isArray(window.coraTasksData)) {
        const tObj = window.coraTasksData.find(t => String(t.id) === String(taskId));
        if (tObj) tObj.status = newStage;
    }

    window.closeTaskDrawer();
    window.coraSyncColumnPlaceholders();
    window.coraApplyTaskFilters();

    // Persist to Server via AJAX
    const ajaxUrl = window.coraAjaxUrl || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxUrl) || '/wp-admin/admin-ajax.php';
    const nonce = window.coraAjaxNonce || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxNonce) || '';

    const formData = new URLSearchParams();
    formData.append('action', 'cora_update_task_status');
    formData.append('nonce', nonce);
    formData.append('task_id', taskId);
    formData.append('status', newStage);

    fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) {
            if (window.coraShowToast) window.coraShowToast('Task moved to ' + newStage.toUpperCase(), 'success');
        } else {
            throw new Error((data && data.data) ? data.data : 'Server rejected status update');
        }
    })
    .catch(err => {
        console.error('Task stage update failed:', err);
        if (window.coraShowToast) window.coraShowToast('Failed to save stage change', 'error');
    });
};

// 13. Delete Active Task with AJAX Persistence
window.coraDeleteActiveTask = function() {
    if (!window.coraActiveTask) return;
    const taskId = window.coraActiveTask.id;
    const card = document.querySelector(`.cora-task-card[data-id="${taskId}"]`);
    if (card) card.remove();

    if (Array.isArray(window.coraTasksData)) {
        window.coraTasksData = window.coraTasksData.filter(t => String(t.id) !== String(taskId));
    }

    window.closeTaskDrawer();
    window.coraSyncColumnPlaceholders();
    window.coraApplyTaskFilters();

    // Persist to Server via AJAX
    const ajaxUrl = window.coraAjaxUrl || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxUrl) || '/wp-admin/admin-ajax.php';
    const nonce = window.coraAjaxNonce || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxNonce) || '';

    const formData = new URLSearchParams();
    formData.append('action', 'cora_delete_client_task');
    formData.append('nonce', nonce);
    formData.append('task_id', taskId);

    fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) {
            if (window.coraShowToast) window.coraShowToast('Task removed from board', 'success');
        }
    })
    .catch(err => {
        console.error('Task deletion failed:', err);
    });
};

// 14. CSV Exporter
window.coraExportTasksCSV = function() {
    const cards = document.querySelectorAll('.cora-task-card:not([style*="display: none"])');
    let csv = "ID,Client,Title,Category,Status,Priority,Assignee,DueDate\r\n";
    cards.forEach(c => {
        csv += `"${c.getAttribute('data-id')}","${c.getAttribute('data-client')}","${c.getAttribute('data-title')}","${c.getAttribute('data-category')}","${c.getAttribute('data-status')}","${c.getAttribute('data-priority')}","${c.getAttribute('data-assignee')}","${c.getAttribute('data-due-date')}"\r\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'cora-tasks-export.csv';
    a.click();
    if (window.coraShowToast) window.coraShowToast('Tasks exported to CSV', 'success');
};

// Auto-hydrate initial filter state on load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const clientParam = urlParams.get('client_name') || urlParams.get('client_id');
    if (clientParam) {
        const clientSel = document.getElementById('task-filter-client');
        if (clientSel) {
            clientSel.value = clientParam.toLowerCase();
            window.coraFilterTaskClient(clientParam);
        }
    }
});
</script>
