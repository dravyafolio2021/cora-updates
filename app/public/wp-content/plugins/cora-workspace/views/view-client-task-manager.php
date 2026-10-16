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

// 3. Client Work Tasks Dataset with Rich Operational Subtasks & Work Logs
$tasks_raw = get_option( 'cora_workspace_client_tasks', array() );
if ( empty( $tasks_raw ) || ! is_array( $tasks_raw ) ) {
    $tasks_raw = array();
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
    transform: none !important;
    transition: box-shadow 0.15s ease;
}
.cora-task-card:hover {
    transform: none !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
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

/* =========================================================================
   SHOPIFY-INSPIRED MAIN-AREA DOCKED VIEW PANEL (GLOBAL CORA STANDARD - RULE 15)
   - Universal bottom-slide motion: translateY(100%) -> translateY(0)
   - Zero overlay or floating blur: no backdrop element (backdrop: false)
   - Docks directly below header: top: var(--cora-main-top, 52px); height: calc(100vh - 52px);
   - Occupies EXACT available workspace width based on sidebar:
     * Desktop Sidebar Expanded: left: 16rem; width: calc(100vw - 16rem);
     * Desktop Sidebar Collapsed: left: 4rem; width: calc(100vw - 4rem);
     * Mobile / Tablet (< 1024px): left: 0; width: 100vw;
   - Zero shadow, square corners (border-radius: 0 !important; box-shadow: none !important;)
   - Clear formal structural borders: border-left: 1px solid #e4e4e7; border-top: 1px solid #e4e4e7;
   - Sidebar remains 100% visible, clickable, and accessible at all times (z-index: 40)
   - Scoped scroll lock strictly on main.cora-main (leaving body & sidebar unlocked)
   ========================================================================= */

:root {
    --cora-main-left: 16rem;
    --cora-main-top: 52px;
    --cora-main-width: calc(100vw - 16rem);
    --cora-main-height: calc(100vh - 52px);
}

/* MAIN-AREA DOCKED TASK DETAILS & TASK CREATOR PANELS (RULE 15 SOP) */
#cora-task-drawer,
#cora-create-task-drawer,
aside.cora-task-drawer {
    position: fixed !important;
    top: var(--cora-main-top, 52px) !important;
    bottom: 0 !important;
    left: var(--cora-main-left, 16rem) !important;
    right: 0 !important;
    width: var(--cora-main-width, calc(100vw - 16rem)) !important;
    max-width: var(--cora-main-width, calc(100vw - 16rem)) !important;
    height: var(--cora-main-height, calc(100vh - 52px)) !important;
    max-height: var(--cora-main-height, calc(100vh - 52px)) !important;
    margin: 0 !important;
    padding: 0 !important;
    border-radius: 0 !important;
    border-top: 1px solid #e4e4e7 !important;
    border-left: 1px solid #e4e4e7 !important;
    border-right: none !important;
    border-bottom: none !important;
    background-color: #ffffff !important;
    z-index: 40 !important; /* Kept strictly below sidebar (z-index: 60) and topbar (z-index: 9999) */
    box-shadow: none !important;
    -webkit-box-shadow: none !important;
    transition: left 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                width 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                max-width 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                opacity 0.15s ease !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}

.dark #cora-task-drawer,
.dark #cora-create-task-drawer,
.dark aside.cora-task-drawer {
    background-color: #18181b !important;
    border-top: 1px solid #27272a !important;
    border-left: 1px solid #27272a !important;
}

#cora-task-drawer.collapsed,
#cora-create-task-drawer.collapsed,
#cora-task-drawer:not(.open),
#cora-create-task-drawer:not(.open) {
    transform: translateY(100%) !important;
    pointer-events: none !important;
    visibility: hidden !important;
}

#cora-task-drawer.open:not(.collapsed),
#cora-create-task-drawer.open:not(.collapsed) {
    transform: translateY(0) !important;
    pointer-events: auto !important;
    visibility: visible !important;
}

/* Maximized Canvas Mode (⤢ Fullscreen) */
#cora-task-drawer.maximized,
#cora-create-task-drawer.maximized {
    left: 0 !important;
    width: 100vw !important;
    max-width: 100vw !important;
    z-index: 70 !important;
    border-left: none !important;
}

/* Mobile & Tablet Overrides (< 1024px) */
@media (max-width: 1023px) {
    #cora-task-drawer,
    #cora-create-task-drawer,
    aside.cora-task-drawer {
        left: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        border-left: none !important;
    }
}

/* Zero Backdrop Overlay per Rule 15 */
#cora-task-drawer-backdrop {
    display: none !important;
    visibility: hidden !important;
    pointer-events: none !important;
}


/* Mobile Filter Bottom Sheet per Rule 1 & Rule 12 */
@media (min-width: 768px) {
    #cora-task-mobile-filter-backdrop,
    #cora-task-mobile-filter-sheet {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
        opacity: 0 !important;
        transform: translateY(100%) !important;
    }
}
@media (max-width: 767px) {
    #cora-task-mobile-filter-backdrop {
        position: fixed !important;
        inset: 0 !important;
        background: rgba(9, 9, 11, 0.45) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 999998 !important;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        display: none !important;
    }
    #cora-task-mobile-filter-backdrop.open {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        display: block !important;
    }
    #cora-task-mobile-filter-sheet {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 999999 !important;
        background: #ffffff !important;
        border-top-left-radius: 1.5rem !important;
        border-top-right-radius: 1.5rem !important;
        border-top: 1px solid #e4e4e7 !important;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.2) !important;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s ease !important;
        transform: translateY(100%) !important;
        --tw-translate-y: 100% !important;
        visibility: hidden !important;
        max-height: 85vh !important;
        display: none !important;
        flex-direction: column !important;
        pointer-events: none !important;
    }
    #cora-task-mobile-filter-sheet.open {
        transform: translateY(0) !important;
        --tw-translate-y: 0px !important;
        visibility: visible !important;
        display: flex !important;
        pointer-events: auto !important;
    }
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

        <!-- Task Management Multi-Filter Toolbar (Sleek Compact Bar) -->
        <div class="bg-white p-2 sm:px-3 sm:py-2 rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-wrap items-center justify-between gap-2.5">
            <!-- Left Group: Search & Desktop Quick Filter Selectors -->
            <div class="flex flex-1 items-center gap-2 min-w-0">
                <!-- Global Live Task Search -->
                <div class="relative flex-1 sm:flex-initial sm:w-60 min-w-[150px]">
                    <input type="text" id="task-search-input" oninput="window.coraSearchTasks(this.value)" placeholder="Search tasks, clients..." class="w-full h-8 pl-8 pr-7 text-xs bg-zinc-50 border border-zinc-200/90 rounded-xl text-zinc-800 placeholder-zinc-400 outline-none focus:border-zinc-400 focus:bg-white transition-all shadow-2xs">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400 pointer-events-none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <button type="button" id="task-search-clear-btn" onclick="window.coraClearSearch()" class="hidden absolute right-2.5 top-2 text-zinc-400 hover:text-zinc-700 bg-transparent border-0 cursor-pointer text-xs leading-none">✕</button>
                </div>

                <!-- Desktop Filter Dropdown Selectors (Hidden on Mobile) -->
                <div class="hidden md:flex items-center gap-2 shrink-0">
                    <!-- Smart Timeframe / Date Selector -->
                    <div class="relative">
                        <select id="task-filter-timeframe" onchange="window.coraOnTimeframeSelectChange(this.value)" class="h-8 pl-2.5 pr-7 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100/80 border border-zinc-200/90 rounded-xl text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer transition-all appearance-none shadow-2xs">
                            <option value="week" selected>This Week</option>
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="month">This Month</option>
                            <option value="all">All Time</option>
                            <option value="custom">Custom Range...</option>
                        </select>
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute right-2 top-2.5 text-zinc-400 pointer-events-none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>

                    <!-- Priority Selector -->
                    <div class="relative">
                        <select id="task-filter-priority" onchange="window.coraFilterTaskPriority(this.value)" class="h-8 pl-2.5 pr-7 text-xs font-medium bg-zinc-50 hover:bg-zinc-100/80 border border-zinc-200/90 rounded-xl text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer transition-all appearance-none shadow-2xs">
                            <option value="all" selected>All Priorities</option>
                            <option value="urgent">Urgent</option>
                            <option value="high">High Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="low">Low Priority</option>
                        </select>
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute right-2 top-2.5 text-zinc-400 pointer-events-none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>

                    <!-- Client Selector -->
                    <div class="relative">
                        <select id="task-filter-client" onchange="window.coraFilterTaskClient(this.value)" class="h-8 pl-2.5 pr-7 text-xs font-medium bg-zinc-50 hover:bg-zinc-100/80 border border-zinc-200/90 rounded-xl text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer transition-all appearance-none shadow-2xs max-w-[160px] truncate">
                            <option value="all">All Clients (Workspace)</option>
                            <?php foreach ( $all_clients_list as $cl_name ) : ?>
                                <option value="<?php echo esc_attr( strtolower( $cl_name ) ); ?>" <?php selected( strtolower( $initial_selected_client ), strtolower( $cl_name ) ); ?>>
                                    <?php echo esc_html( $cl_name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute right-2 top-2.5 text-zinc-400 pointer-events-none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>

                    <!-- Assignee Selector -->
                    <div class="relative">
                        <select id="task-filter-assignee" onchange="window.coraFilterTaskAssignee(this.value)" class="h-8 pl-2.5 pr-7 text-xs font-medium bg-zinc-50 hover:bg-zinc-100/80 border border-zinc-200/90 rounded-xl text-zinc-800 outline-none focus:border-zinc-400 cursor-pointer transition-all appearance-none shadow-2xs max-w-[150px] truncate">
                            <option value="all">All Team Members</option>
                            <?php foreach ( $all_assignees_list as $as_name ) : ?>
                                <option value="<?php echo esc_attr( strtolower( $as_name ) ); ?>">
                                    <?php echo esc_html( $as_name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute right-2 top-2.5 text-zinc-400 pointer-events-none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>
            </div>

            <!-- Right Group: Reset Filter Action & Mobile Bottom Sheet Trigger -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- Reset Filter Action Button (Active only when filters applied) -->
                <button type="button" id="cora-task-reset-btn" onclick="window.coraResetAllTaskFilters()" class="hidden h-8 px-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-semibold items-center gap-1.5 transition-all border border-zinc-200 cursor-pointer shadow-2xs">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                    <span>Reset</span>
                    <span id="cora-task-active-filter-badge" class="w-4 h-4 rounded-full bg-zinc-900 text-white text-[10px] font-bold flex items-center justify-center">1</span>
                </button>

                <!-- Mobile Filter Toggle Button -->
                <button type="button" id="btn-mobile-task-filters" onclick="window.coraOpenMobileFiltersSheet()" class="md:hidden h-8 px-3 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-semibold flex items-center gap-1.5 transition-all border border-zinc-200/90 cursor-pointer shadow-2xs shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filters</span>
                    <span id="mobile-filter-badge" class="hidden w-4 h-4 rounded-full bg-zinc-950 text-white text-[10px] font-bold flex items-center justify-center">0</span>
                </button>
            </div>

            <!-- Custom Date Range Drawer Tray (Revealed on Custom Date selection) -->
            <div id="task-custom-date-tray" class="hidden w-full pt-2 border-t border-zinc-100 flex flex-wrap items-center gap-3 text-xs">
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
                                 oncontextmenu="window.coraHandleTaskContextMenu(event, this)"
                                 onclick="window.openTaskDrawer('<?php echo esc_js( $task['id'] ); ?>')">

                                <!-- Card Header: Client & Priority -->
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[10.5px] font-semibold text-zinc-500 truncate flex items-center gap-1">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span class="task-card-client"><?php echo esc_html( $task['client_name'] ?? 'Client' ); ?></span>
                                    </span>
                                    <span class="task-card-priority-badge px-1.5 py-0.5 rounded text-[9px] uppercase <?php echo esc_attr( $badge_bg ); ?>">
                                        <?php echo esc_html( $priority ); ?>
                                    </span>
                                </div>

                                <!-- Card Title & Category Tag -->
                                <div>
                                    <h4 class="task-card-title text-xs font-semibold text-zinc-900 leading-snug line-clamp-2"><?php echo esc_html( $task['title'] ?? 'Task' ); ?></h4>
                                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                        <span class="task-card-category px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-500 text-[8.5px] font-mono font-medium uppercase">
                                            <?php echo esc_html( $task['category'] ?? 'GENERAL' ); ?>
                                        </span>
                                        <span class="text-[9.5px] font-medium <?php echo $is_overdue ? 'text-red-600 font-bold' : ( $is_today ? 'text-amber-700 font-semibold' : 'text-zinc-400' ); ?> flex items-center gap-1">
                                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                            <span class="task-card-due-label"><?php echo esc_html( $due_label ); ?></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Subtasks / Checklist Micro-Progress -->
                                <?php 
                                $subtasks_raw = $task['subtasks'] ?? array();
                                $sub_total = is_array($subtasks_raw) ? count($subtasks_raw) : 0;
                                $sub_completed = is_array($subtasks_raw) ? count(array_filter($subtasks_raw, function($s) { return !empty($s['completed']); })) : 0;
                                $sub_pct = $sub_total > 0 ? round(($sub_completed / $sub_total) * 100) : intval($task['progress'] ?? 0);
                                $sub_label = $sub_total > 0 ? "{$sub_completed}/{$sub_total} done" : (is_string($subtasks_raw) ? $subtasks_raw : '');
                                ?>
                                <?php if ( ! empty( $sub_label ) || $sub_total > 0 ) : ?>
                                    <div class="w-full space-y-1">
                                        <div class="flex items-center justify-between text-[9px] text-zinc-400">
                                            <span class="task-card-subtask-count font-medium text-zinc-500">Subtasks: <?php echo esc_html( $sub_label ); ?></span>
                                            <span class="task-card-progress-val font-mono font-bold text-zinc-700"><?php echo intval( $sub_pct ); ?>%</span>
                                        </div>
                                        <div class="w-full h-1 bg-zinc-100 rounded-full overflow-hidden">
                                            <div class="task-card-progress-bar h-full bg-zinc-900 rounded-full transition-all duration-300" style="width: <?php echo intval( $sub_pct ); ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Card Footer: Assignee Avatar & Quick Actions -->
                                <div class="flex items-center justify-between pt-2 border-t border-zinc-100 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <div class="task-card-avatar w-5 h-5 rounded-full bg-zinc-900 text-white font-bold text-[8.5px] flex items-center justify-center shrink-0">
                                            <?php echo esc_html( $task['assignee_init'] ?? 'SA' ); ?>
                                        </div>
                                        <span class="task-card-assignee text-[10px] text-zinc-600 font-medium truncate max-w-[110px]">
                                            <?php echo esc_html( $task['assignee'] ?? 'Studio Admin' ); ?>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <button type="button" onclick="event.stopPropagation(); window.coraHandleTaskContextMenu(event, this.closest('.cora-task-card'))" title="Task Options (Right-Click)" class="w-5 h-5 rounded hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-colors border-0 bg-transparent cursor-pointer">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
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
     6. MAIN-AREA DOCKED TASK DETAILS VIEW PANEL (GLOBAL CORA STANDARD - RULE 15)
     ═══════════════════════════════════════════════════════════════════ -->
<aside id="cora-task-drawer" class="cora-task-drawer collapsed">
    
    <!-- 1. UNIFIED ULTRA-COMPACT DOCKED HEADER (52px Desktop / Responsive Mobile) -->
    <div class="cora-drawer-header px-3 sm:px-6 py-2 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
        <div class="flex flex-wrap lg:flex-nowrap items-center min-h-[38px] lg:min-h-[44px] w-full gap-y-2 justify-between">
            
            <!-- Left: Task ID, Category Badge, Status Selector & Priority Selector -->
            <div class="flex items-center gap-2 sm:gap-2.5 cora-drawer-header-left min-w-0">
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold tracking-wider uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 shrink-0 border border-zinc-200/60 dark:border-zinc-700/60" id="drawer-task-id">#TASK-101</span>
                
                <span id="drawer-task-category-badge" class="hidden sm:inline-block px-2 py-0.5 rounded text-[9.5px] font-mono font-bold tracking-wider uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 shrink-0">POST_PRODUCTION</span>

                <!-- Quick Status Selector -->
                <select id="drawer-task-status-select" onchange="window.coraDrawerStatusChange(this.value)" class="h-7 px-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 outline-none cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors shadow-2xs">
                    <option value="todo">To Do / Backlog</option>
                    <option value="in_progress">In Execution</option>
                    <option value="review">Review & QA</option>
                    <option value="done">Completed</option>
                </select>

                <!-- Quick Priority Selector -->
                <select id="drawer-task-priority-select" onchange="window.coraDrawerPriorityChange(this.value)" class="h-7 px-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 outline-none cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors shadow-2xs">
                    <option value="urgent">Urgent</option>
                    <option value="high">High Priority</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <!-- Center: Level 01 Segmented Pill Tabs (Dead Center on Desktop / Responsive Horizontal Scroll on Mobile) -->
            <div class="flex items-center justify-start sm:justify-center overflow-x-auto no-scrollbar py-0.5 cora-drawer-header-center">
                <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 sm:gap-1.5 select-none shrink-0" id="task-drawer-tabs-bar">
                    <button type="button" onclick="window.coraSetTaskDrawerTab('checklist')" id="task-tab-btn-checklist" class="task-drawer-tab-btn active px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                        <span>Checklist</span>
                        <span id="tab-badge-checklist-count" class="px-1.5 py-0.2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[9.5px] font-mono font-bold">0</span>
                    </button>
                    <button type="button" onclick="window.coraSetTaskDrawerTab('scope')" id="task-tab-btn-scope" class="task-drawer-tab-btn px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span>Scope & Specs</span>
                    </button>
                    <button type="button" onclick="window.coraSetTaskDrawerTab('assets')" id="task-tab-btn-assets" class="task-drawer-tab-btn px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        <span>Assets & Links</span>
                        <span id="tab-badge-assets-count" class="px-1.5 py-0.2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[9.5px] font-mono font-bold">0</span>
                    </button>
                    <button type="button" onclick="window.coraSetTaskDrawerTab('activity')" id="task-tab-btn-activity" class="task-drawer-tab-btn px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <span>Activity & Notes</span>
                        <span id="tab-badge-activity-count" class="px-1.5 py-0.2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[9.5px] font-mono font-bold">0</span>
                    </button>
                </div>
            </div>

            <!-- Right: Maximize Toggle & Close Buttons Pinned Right -->
            <div class="flex items-center gap-1.5 shrink-0 cora-drawer-header-right">
                <!-- Maximize / Restore Toggle Button -->
                <button 
                    type="button" 
                    onclick="window.coraToggleTaskDrawerMaximize()"
                    id="cora-task-maximize-btn"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0"
                    title="Expand / Fullscreen Canvas"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                </button>

                <!-- Close Button (Esc) -->
                <button 
                    type="button" 
                    onclick="window.closeTaskDrawer()"
                    id="btn-close-task-drawer"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0"
                    title="Close Drawer (Esc)"
                >
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- 2. Drawer Body (Full Canvas Responsive Scrollable Content - 2-Column SaaS Architecture) -->
    <div class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 text-xs text-zinc-800 dark:text-zinc-200">
        <div class="max-w-7xl mx-auto w-full space-y-5">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Column (5 cols): Title, Properties Matrix & Progress Overview -->
                <div class="lg:col-span-5 space-y-4">
                    
                    <!-- Task Title Card -->
                    <div class="p-4 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Task Title & Scope</span>
                            <span class="text-[10px] text-zinc-400 font-medium">Click to edit</span>
                        </div>
                        <input type="text" id="drawer-task-title-input" onblur="window.coraSaveTaskField('title', this.value)" class="w-full text-base sm:text-lg font-bold text-zinc-950 dark:text-white bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-3.5 py-2.5 outline-none focus:border-zinc-400 dark:focus:border-zinc-600 transition-all" placeholder="Enter task title...">
                    </div>

                    <!-- Notion/Linear Style Properties Matrix -->
                    <div class="p-4 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-3.5">
                        <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Operational Metadata</span>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Assignee -->
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Assignee</label>
                                <select id="drawer-task-assignee-select" onchange="window.coraSaveTaskField('assignee', this.value)" class="w-full h-9 px-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-medium text-zinc-800 dark:text-zinc-200 outline-none cursor-pointer">
                                    <option value="Studio Admin">Studio Admin</option>
                                    <option value="Rohan Verma">Rohan Verma</option>
                                    <option value="Kavya Patel">Kavya Patel</option>
                                    <option value="Aarav Mehta">Aarav Mehta</option>
                                </select>
                            </div>

                            <!-- Client -->
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Client</label>
                                <input type="text" id="drawer-task-client-input" onblur="window.coraSaveTaskField('client', this.value)" class="w-full h-9 px-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-medium text-zinc-800 dark:text-zinc-200 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2.5 border-t border-zinc-200/60 dark:border-zinc-800">
                            <!-- Due Date -->
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Due Date</label>
                                <input type="date" id="drawer-task-due-date-input" onchange="window.coraSaveTaskField('due_date', this.value)" class="w-full h-9 px-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-medium text-zinc-800 dark:text-zinc-200 outline-none cursor-pointer">
                            </div>

                            <!-- Deliverable Category -->
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Category</label>
                                <select id="drawer-task-category-select" onchange="window.coraSaveTaskField('category', this.value)" class="w-full h-9 px-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-medium text-zinc-800 dark:text-zinc-200 outline-none cursor-pointer">
                                    <?php foreach ( ( $curr_cfg['categories'] ?? array('PHOTOSHOOT', 'POST_PRODUCTION', 'RETOUCHING', 'COLOR_GRADING', 'PORTAL_UPLOAD', 'ADMIN') ) as $cat ) : ?>
                                        <option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Execution Progress Card -->
                    <div class="p-4 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Execution Health & Completion</span>
                            <span id="drawer-subtask-progress-label" class="font-mono text-[11px] font-bold text-zinc-800 dark:text-zinc-200">3 of 5 (60%)</span>
                        </div>
                        <div class="w-full h-2 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div id="drawer-subtask-progress-bar" class="h-full bg-zinc-950 dark:bg-white rounded-full transition-all duration-300" style="width: 60%;"></div>
                        </div>
                    </div>

                </div>

                <!-- Right Column (7 cols): Active Tab Panel (Checklist, Scope, Assets, Activity) -->
                <div class="lg:col-span-7">
                    <div class="p-5 sm:p-6 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 min-h-[440px]">
                        
                        <!-- TAB 1: CHECKLIST -->
                        <div id="task-tab-panel-checklist" class="task-drawer-tab-panel space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                                <div class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                    <span class="font-bold text-zinc-900 dark:text-white text-xs">Subtasks & Execution Checklist</span>
                                </div>
                            </div>

                            <!-- Subtask Items List -->
                            <div id="drawer-subtasks-list" class="space-y-2">
                                <!-- Dynamically rendered checkable subtask items -->
                            </div>

                            <!-- Add Subtask Input Form -->
                            <form onsubmit="window.coraAddSubtask(event)" class="flex items-center gap-2 pt-3 border-t border-zinc-200/60 dark:border-zinc-800">
                                <input type="text" id="drawer-new-subtask-input" placeholder="+ Add a subtask checklist item (press Enter)..." class="flex-1 h-9 px-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 focus:border-zinc-400 dark:focus:border-zinc-600 rounded-xl text-xs outline-none transition-all">
                                <button type="submit" class="px-3.5 h-9 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 font-bold rounded-xl text-xs cursor-pointer transition-colors border-0 shadow-2xs">Add Item</button>
                            </form>
                        </div>

                        <!-- TAB 2: SCOPE & SPECS -->
                        <div id="task-tab-panel-scope" class="task-drawer-tab-panel hidden space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                                <div class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    <span class="font-bold text-zinc-900 dark:text-white text-xs">Technical Scope & Requirements</span>
                                </div>
                                <span class="text-[10px] text-zinc-400 font-mono">Auto-saved</span>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Work Instructions & Deliverable Specifications</label>
                                <textarea id="drawer-task-notes-input" onblur="window.coraSaveTaskField('notes', this.value)" rows="10" placeholder="Provide checklist requirements, format specifications (4K ProRes, JPG, sRGB), camera angles, color grading LUT references, and deliverable notes..." class="w-full p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 dark:focus:border-zinc-600 transition-all resize-none leading-relaxed"></textarea>
                            </div>

                            <div class="p-3 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 text-[11px] text-zinc-500 space-y-1">
                                <span class="font-bold text-zinc-700 dark:text-zinc-300 block">Pro Tip for Deliverables:</span>
                                <p class="leading-relaxed">Keep instructions structured with clear deliverable resolutions, color profiles, and client feedback notes.</p>
                            </div>
                        </div>

                        <!-- TAB 3: ASSETS & LINKS -->
                        <div id="task-tab-panel-assets" class="task-drawer-tab-panel hidden space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                                <div class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                    <span class="font-bold text-zinc-900 dark:text-white text-xs">Deliverable Asset Links & Proofs</span>
                                </div>
                            </div>

                            <!-- Asset Links List -->
                            <div id="drawer-asset-links-list" class="space-y-2">
                                <!-- Dynamically rendered asset links -->
                            </div>

                            <!-- Inline Add Link Form -->
                            <div class="p-3.5 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 space-y-2.5">
                                <span class="font-bold text-zinc-800 dark:text-zinc-200 text-[11px] block">+ Attach Deliverable Asset URL</span>
                                <div class="space-y-2">
                                    <input type="text" id="drawer-new-link-title" placeholder="Link Title (e.g. Master Drive, Frame.io Review, RAW Proofs)" class="w-full h-8 px-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs outline-none focus:border-zinc-400">
                                    <input type="url" id="drawer-new-link-url" placeholder="URL (e.g. https://drive.google.com/...)" class="w-full h-8 px-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs outline-none focus:border-zinc-400">
                                    <button type="button" onclick="window.coraAddAssetLinkSubmit()" class="w-full h-8 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 font-bold rounded-lg text-xs transition-colors cursor-pointer border-0 shadow-2xs">
                                        Add Asset Link
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: ACTIVITY & NOTES -->
                        <div id="task-tab-panel-activity" class="task-drawer-tab-panel hidden space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                                <div class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                    <span class="font-bold text-zinc-900 dark:text-white text-xs">Team Activity & Collaboration Feed</span>
                                </div>
                                <span id="drawer-comments-count" class="text-[10px] text-zinc-400 font-mono">0 updates</span>
                            </div>

                            <!-- Comments Feed -->
                            <div id="drawer-comments-feed" class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                                <!-- Dynamically rendered comments -->
                            </div>

                            <!-- Post New Comment Input Form -->
                            <form onsubmit="window.coraAddComment(event)" class="space-y-2 pt-2 border-t border-zinc-200/60 dark:border-zinc-800">
                                <textarea id="drawer-new-comment-input" rows="2" placeholder="Write an update, note, or collaboration comment..." class="w-full p-2.5 bg-white dark:bg-zinc-900 focus:bg-white border border-zinc-200 dark:border-zinc-800 focus:border-zinc-400 rounded-xl text-xs outline-none transition-all resize-none"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-3.5 h-8 bg-zinc-950 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 font-bold rounded-xl text-xs cursor-pointer transition-colors border-0 shadow-2xs">
                                        Post Update
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- 3. Drawer Sticky Footer Actions -->
    <div class="px-4 sm:px-8 py-3.5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 flex items-center justify-between gap-3 shrink-0">
        <button type="button" onclick="window.coraDeleteActiveTask()" class="px-3.5 py-2 text-xs font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl transition-colors border border-transparent hover:border-red-200 cursor-pointer">
            Delete Task
        </button>
        <div class="flex items-center gap-2.5">
            <button type="button" id="drawer-advance-stage-btn" onclick="window.coraAdvanceTaskStage()" class="px-4 py-2 text-xs font-bold text-zinc-800 dark:text-zinc-200 bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 rounded-xl transition-all cursor-pointer shadow-2xs">
                Advance Stage &rarr;
            </button>
            <button type="button" onclick="window.closeTaskDrawer()" class="px-5 py-2 text-xs font-bold text-white dark:text-zinc-950 bg-zinc-950 dark:bg-white hover:bg-zinc-800 dark:hover:bg-zinc-100 rounded-xl transition-all cursor-pointer shadow-2xs">
                Done
            </button>
        </div>
    </div>
</aside>

<!-- ═══════════════════════════════════════════════════════════════════
     7. MAIN-AREA DOCKED NEW TASK CREATION PANEL (RULE 15 SOP)
     ═══════════════════════════════════════════════════════════════════ -->
<aside id="cora-create-task-drawer" class="cora-task-drawer collapsed">
    
    <!-- 1. Header Toolbar -->
    <div class="cora-drawer-header px-3 sm:px-6 py-2 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
        <div class="flex items-center justify-between min-h-[38px] lg:min-h-[44px] w-full">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-sm shadow-2xs">
                    +
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-zinc-950 dark:text-white">New Operational Task</h3>
                    <p class="text-[10px] text-zinc-400">Assign deliverable or milestone to team</p>
                </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Maximize / Restore Toggle Button -->
                <button 
                    type="button" 
                    onclick="window.coraToggleCreateTaskDrawerMaximize()"
                    id="cora-create-task-maximize-btn"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0"
                    title="Expand / Fullscreen Canvas"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                </button>

                <!-- Close Button (Esc) -->
                <button 
                    type="button" 
                    onclick="window.closeCreateTaskDrawer()"
                    id="btn-close-create-task"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0"
                    title="Close Drawer (Esc)"
                >
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- 2. Form Body -->
    <form id="cora-create-task-form" onsubmit="window.coraCreateTaskSubmit(event)" class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 text-xs text-zinc-800 dark:text-zinc-200 flex flex-col justify-between">
        <input type="hidden" id="create-task-stage" value="todo">

        <div class="max-w-3xl mx-auto w-full p-6 sm:p-8 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-4">
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Client Name *</label>
                <input type="text" id="create-task-client" required placeholder="e.g. Rohan Verma, Kavya Patel" class="w-full h-9 px-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Task Title & Scope *</label>
                <input type="text" id="create-task-title" required placeholder="e.g. Export final 4K masters and color grading" class="w-full h-9 px-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 focus:bg-white transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Category</label>
                    <select id="create-task-category" class="w-full h-9 px-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 cursor-pointer">
                        <?php foreach ( ( $curr_cfg['categories'] ?? array('GENERAL', 'DELIVERABLE', 'REVIEW') ) as $cat ) : ?>
                            <option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Priority</label>
                    <select id="create-task-priority" class="w-full h-9 px-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 cursor-pointer">
                        <option value="urgent">Urgent</option>
                        <option value="high" selected>High Priority</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Assignee</label>
                    <select id="create-task-assignee" class="w-full h-9 px-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 cursor-pointer">
                        <option value="Studio Admin">Studio Admin</option>
                        <option value="Rohan Verma">Rohan Verma</option>
                        <option value="Kavya Patel">Kavya Patel</option>
                        <option value="Aarav Mehta">Aarav Mehta</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Due Date</label>
                    <input type="date" id="create-task-due-date" value="<?php echo date('Y-m-d'); ?>" class="w-full h-9 px-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 cursor-pointer">
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Detailed Instructions & Notes</label>
                <textarea id="create-task-notes" rows="4" placeholder="Provide checklist requirements, format specifications, and deliverables..." class="w-full p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-800 dark:text-zinc-200 outline-none focus:border-zinc-400 focus:bg-white transition-all resize-none"></textarea>
            </div>
        </div>

        <!-- 3. Sticky Footer Actions -->
        <div class="px-4 sm:px-8 py-3.5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 flex items-center justify-end gap-3 shrink-0 mt-6">
            <button type="button" onclick="window.closeCreateTaskDrawer()" class="px-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold transition-all cursor-pointer">
                Cancel
            </button>
            <button type="submit" class="px-5 py-2 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all cursor-pointer shadow-2xs">
                Create Task
            </button>
        </div>
    </form>
</aside>

<!-- ═══════════════════════════════════════════════════════════════════
     MOBILE FILTER BOTTOM-UP SLIDE SHEET (Per Rule 1 & Rule 12)
     ═══════════════════════════════════════════════════════════════════ -->
<div id="cora-task-mobile-filter-backdrop" class="fixed inset-0 bg-zinc-950/45 backdrop-blur-xs z-99990 transition-opacity duration-300 opacity-0 pointer-events-none hidden md:!hidden" onclick="window.coraCloseMobileFiltersSheet()"></div>

<div id="cora-task-mobile-filter-sheet" class="fixed bottom-0 left-0 right-0 z-99995 bg-white rounded-t-3xl border-t border-zinc-200/80 shadow-2xl transition-transform duration-300 ease-out translate-y-full max-h-[85vh] flex flex-col pointer-events-none hidden md:!hidden">
    <!-- Drag Indicator Handle -->
    <div class="pt-3 pb-1 flex justify-center cursor-grab active:cursor-grabbing" onclick="window.coraCloseMobileFiltersSheet()">
        <div class="w-10 h-1 rounded-full bg-zinc-300"></div>
    </div>

    <!-- Sheet Header -->
    <div class="px-5 py-3 border-b border-zinc-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
            <h3 class="text-sm font-bold text-zinc-900">Task Filters & Range</h3>
        </div>
        <button type="button" onclick="window.coraCloseMobileFiltersSheet()" class="w-7 h-7 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-500 flex items-center justify-center cursor-pointer border-0">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Sheet Scrollable Body -->
    <div class="p-5 overflow-y-auto flex-1 flex flex-col gap-4 text-xs">
        <!-- 1. Timeframe & Date Window Chips -->
        <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-400 mb-2">Timeframe Window</label>
            <div class="grid grid-cols-3 gap-1.5" id="mobile-timeframe-grid">
                <button type="button" onclick="window.coraSetMobileTimeframe('week', this)" class="mobile-time-btn active py-2 px-2 text-center rounded-xl font-bold bg-zinc-950 text-white border border-transparent cursor-pointer text-xs" data-time="week">This Week</button>
                <button type="button" onclick="window.coraSetMobileTimeframe('today', this)" class="mobile-time-btn py-2 px-2 text-center rounded-xl font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200/60 cursor-pointer text-xs" data-time="today">Today</button>
                <button type="button" onclick="window.coraSetMobileTimeframe('tomorrow', this)" class="mobile-time-btn py-2 px-2 text-center rounded-xl font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200/60 cursor-pointer text-xs" data-time="tomorrow">Tomorrow</button>
                <button type="button" onclick="window.coraSetMobileTimeframe('month', this)" class="mobile-time-btn py-2 px-2 text-center rounded-xl font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200/60 cursor-pointer text-xs" data-time="month">This Month</button>
                <button type="button" onclick="window.coraSetMobileTimeframe('all', this)" class="mobile-time-btn py-2 px-2 text-center rounded-xl font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200/60 cursor-pointer text-xs" data-time="all">All Time</button>
                <button type="button" onclick="window.coraSetMobileTimeframe('custom', this)" class="mobile-time-btn py-2 px-2 text-center rounded-xl font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200/60 cursor-pointer text-xs" data-time="custom">Custom...</button>
            </div>

            <!-- Mobile Custom Date Picker Tray -->
            <div id="mobile-custom-date-box" class="hidden mt-2 p-3 bg-zinc-50 border border-zinc-200 rounded-xl flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500 font-medium text-[11px]">From</span>
                    <input type="date" id="mobile-custom-start-date" class="h-8 px-2 bg-white border border-zinc-200 rounded-lg text-xs">
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500 font-medium text-[11px]">To</span>
                    <input type="date" id="mobile-custom-end-date" class="h-8 px-2 bg-white border border-zinc-200 rounded-lg text-xs">
                </div>
            </div>
        </div>

        <!-- 2. Priority Segmented Chips -->
        <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-400 mb-2">Priority Level</label>
            <div class="grid grid-cols-2 gap-1.5" id="mobile-priority-grid">
                <button type="button" onclick="window.coraSetMobilePriority('all', this)" class="mobile-pri-btn active py-2 px-3 text-center rounded-xl font-bold bg-zinc-950 text-white border border-transparent cursor-pointer text-xs" data-pri="all">All Priorities</button>
                <button type="button" onclick="window.coraSetMobilePriority('urgent', this)" class="mobile-pri-btn py-2 px-3 text-center rounded-xl font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200/60 cursor-pointer text-xs" data-pri="urgent">Urgent Only</button>
                <button type="button" onclick="window.coraSetMobilePriority('high', this)" class="mobile-pri-btn py-2 px-3 text-center rounded-xl font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200/60 cursor-pointer text-xs" data-pri="high">High Priority</button>
                <button type="button" onclick="window.coraSetMobilePriority('medium', this)" class="mobile-pri-btn py-2 px-3 text-center rounded-xl font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200/60 cursor-pointer text-xs" data-pri="medium">Medium Priority</button>
            </div>
        </div>

        <!-- 3. Client Selector -->
        <div>
            <label for="mobile-filter-client" class="block text-[11px] font-bold uppercase tracking-wider text-zinc-400 mb-1.5">Client Filter</label>
            <div class="relative">
                <select id="mobile-filter-client" class="w-full h-10 px-3 pr-8 text-xs font-medium bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-800 outline-none appearance-none">
                    <option value="all">All Clients (Workspace)</option>
                    <?php foreach ( $all_clients_list as $cl_name ) : ?>
                        <option value="<?php echo esc_attr( strtolower( $cl_name ) ); ?>" <?php selected( strtolower( $initial_selected_client ), strtolower( $cl_name ) ); ?>>
                            <?php echo esc_html( $cl_name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute right-3 top-3 text-zinc-400 pointer-events-none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </div>

        <!-- 4. Assignee Selector -->
        <div>
            <label for="mobile-filter-assignee" class="block text-[11px] font-bold uppercase tracking-wider text-zinc-400 mb-1.5">Assignee Filter</label>
            <div class="relative">
                <select id="mobile-filter-assignee" class="w-full h-10 px-3 pr-8 text-xs font-medium bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-800 outline-none appearance-none">
                    <option value="all">All Team Members</option>
                    <?php foreach ( $all_assignees_list as $as_name ) : ?>
                        <option value="<?php echo esc_attr( strtolower( $as_name ) ); ?>">
                            <?php echo esc_html( $as_name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute right-3 top-3 text-zinc-400 pointer-events-none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </div>
    </div>

    <!-- Sheet Sticky Footer -->
    <div class="p-4 border-t border-zinc-100 bg-zinc-50/80 flex items-center justify-between gap-3">
        <button type="button" onclick="window.coraResetMobileFilters()" class="h-10 px-4 rounded-xl text-xs font-bold text-zinc-600 hover:text-zinc-950 bg-white border border-zinc-200 cursor-pointer shadow-2xs">
            Reset All
        </button>
        <button type="button" onclick="window.coraApplyMobileFilters()" class="flex-1 h-10 px-4 rounded-xl text-xs font-bold text-white bg-zinc-950 hover:bg-zinc-800 cursor-pointer shadow-2xs flex items-center justify-center gap-2">
            <span>Apply Filters</span>
        </button>
    </div>
</div>

<!-- ========================================================================= -->
<!-- RIGHT-CLICK COMMAND MENU (KANBAN TASK FLOATING CONTEXT MENU)              -->
<!-- ========================================================================= -->
<div id="cora-task-command-menu" class="hidden fixed z-[999999] w-72 bg-white rounded-2xl border border-zinc-200/90 shadow-2xl p-1.5 font-sans text-xs select-none space-y-1 transition-all duration-150 animate-in fade-in zoom-in-95 backdrop-blur-md">
    <!-- Header: Quick Task Meta Pill -->
    <div class="px-3 py-2 bg-zinc-50/90 rounded-xl border border-zinc-100 flex items-center justify-between gap-2">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-1.5 mb-0.5">
                <span id="cora-cmd-task-key" class="text-[9.5px] font-mono font-bold text-zinc-500 bg-white px-1.5 py-0.5 rounded border border-zinc-200/70 shrink-0">TSK</span>
                <span id="cora-cmd-task-client" class="text-[10px] font-semibold text-zinc-600 truncate">Client Name</span>
            </div>
            <div id="cora-cmd-task-title" class="font-bold text-xs text-zinc-950 leading-snug truncate">Task Title</div>
        </div>
        <span id="cora-cmd-task-status-badge" class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase shrink-0 bg-zinc-100 text-zinc-700 border border-zinc-200">
            To Do
        </span>
    </div>

    <!-- Primary Quick Actions -->
    <div class="py-1 space-y-0.5">
        <button type="button" class="w-full px-2.5 py-1.5 rounded-lg text-left text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer border-0 bg-transparent" onclick="window.coraTaskCmdAction('view')">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 shrink-0"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <span class="flex-1">Open Deliverable Details</span>
            <span class="text-[10px] text-zinc-400 font-mono">↵</span>
        </button>

        <button type="button" id="cora-cmd-btn-advance" class="w-full px-2.5 py-1.5 rounded-lg text-left text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer border-0 bg-transparent" onclick="window.coraTaskCmdAction('advance')">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 shrink-0"><polyline points="9 18 15 12 9 6"></polyline></svg>
            <span class="flex-1" id="cora-cmd-advance-label">Move to Next Stage</span>
            <span class="text-[10px] text-zinc-400 font-mono">→</span>
        </button>

        <button type="button" class="w-full px-2.5 py-1.5 rounded-lg text-left text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer border-0 bg-transparent" onclick="window.coraTaskCmdAction('duplicate')">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 shrink-0"><rect x="8" y="8" width="13" height="13" rx="2" ry="2"></rect><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path></svg>
            <span class="flex-1">Duplicate Task</span>
            <span class="text-[9.5px] text-zinc-400 font-mono">⌘D</span>
        </button>

        <button type="button" class="w-full px-2.5 py-1.5 rounded-lg text-left text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer border-0 bg-transparent" onclick="window.coraTaskCmdAction('add_subtask')">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 shrink-0"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            <span class="flex-1">Add Checklist Item</span>
        </button>

        <button type="button" class="w-full px-2.5 py-1.5 rounded-lg text-left text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer border-0 bg-transparent" onclick="window.coraTaskCmdAction('attach_link')">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 shrink-0"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            <span class="flex-1">Attach Asset Link</span>
        </button>

        <button type="button" class="w-full px-2.5 py-1.5 rounded-lg text-left text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer border-0 bg-transparent" onclick="window.coraTaskCmdAction('copy')">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 shrink-0"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            <span class="flex-1">Copy Task Summary</span>
        </button>
    </div>

    <!-- Priority Intent Level -->
    <div class="pt-1.5 pb-1 border-t border-zinc-100 space-y-1">
        <div class="px-2 text-[9px] font-extrabold text-zinc-400 uppercase tracking-wider flex items-center gap-1">
            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            <span>Priority Level</span>
        </div>
        <div class="grid grid-cols-4 gap-1 px-1">
            <button type="button" class="py-1 px-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 border border-red-200/80 font-bold text-[9.5px] text-center cursor-pointer transition-colors" onclick="window.coraTaskCmdAction('priority', 'urgent')">
                Urgent
            </button>
            <button type="button" class="py-1 px-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/80 font-bold text-[9.5px] text-center cursor-pointer transition-colors" onclick="window.coraTaskCmdAction('priority', 'high')">
                High
            </button>
            <button type="button" class="py-1 px-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-200/80 font-semibold text-[9.5px] text-center cursor-pointer transition-colors" onclick="window.coraTaskCmdAction('priority', 'medium')">
                Medium
            </button>
            <button type="button" class="py-1 px-1 rounded-lg bg-zinc-50 hover:bg-zinc-100 text-zinc-500 border border-zinc-200/80 font-medium text-[9.5px] text-center cursor-pointer transition-colors" onclick="window.coraTaskCmdAction('priority', 'low')">
                Low
            </button>
        </div>
    </div>

    <!-- Destructive: Delete Task -->
    <div class="pt-1 border-t border-zinc-100">
        <button type="button" class="w-full px-2.5 py-1.5 rounded-lg text-left text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-semibold flex items-center gap-2 transition-colors cursor-pointer border-0 bg-transparent" onclick="window.coraTaskCmdAction('delete')">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-rose-500 shrink-0"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            <span class="flex-1">Delete Task</span>
        </button>
    </div>
</div>

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

    // Update Active Filter Counters & Badges
    window.coraUpdateFilterBadges();
};

// 3. Timeframe & Date Range Controls
window.coraOnTimeframeSelectChange = function(val) {
    const tray = document.getElementById('task-custom-date-tray');
    if (tray) {
        if (val === 'custom') {
            tray.classList.remove('hidden');
        } else {
            tray.classList.add('hidden');
        }
    }
    window.coraTaskFilterState.timeframe = val;
    if (val !== 'custom') {
        window.coraApplyTaskFilters();
    }
};

window.coraSetTaskTimeframe = function(timeframe, btn) {
    const select = document.getElementById('task-filter-timeframe');
    if (select) select.value = timeframe;
    window.coraOnTimeframeSelectChange(timeframe);
};

window.coraToggleCustomDatePicker = function(btn) {
    const tray = document.getElementById('task-custom-date-tray');
    if (!tray) return;
    tray.classList.toggle('hidden');
    if (!tray.classList.contains('hidden')) {
        const select = document.getElementById('task-filter-timeframe');
        if (select) select.value = 'custom';
        window.coraTaskFilterState.timeframe = 'custom';
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
    const sel = document.getElementById('task-filter-client');
    if (sel && sel.value !== window.coraTaskFilterState.client) sel.value = window.coraTaskFilterState.client;
    const mobSel = document.getElementById('mobile-filter-client');
    if (mobSel && mobSel.value !== window.coraTaskFilterState.client) mobSel.value = window.coraTaskFilterState.client;
    window.coraApplyTaskFilters();
};

window.coraClearClientFilter = function() {
    window.coraFilterTaskClient('all');
};

// 5. Assignee Filter
window.coraFilterTaskAssignee = function(assigneeName) {
    window.coraTaskFilterState.assignee = (assigneeName || 'all').toLowerCase();
    const sel = document.getElementById('task-filter-assignee');
    if (sel && sel.value !== window.coraTaskFilterState.assignee) sel.value = window.coraTaskFilterState.assignee;
    const mobSel = document.getElementById('mobile-filter-assignee');
    if (mobSel && mobSel.value !== window.coraTaskFilterState.assignee) mobSel.value = window.coraTaskFilterState.assignee;
    window.coraApplyTaskFilters();
};

// 6. Priority Filter
window.coraFilterTaskPriority = function(priority, btn) {
    window.coraTaskFilterState.priority = (priority || 'all').toLowerCase();
    const sel = document.getElementById('task-filter-priority');
    if (sel && sel.value !== window.coraTaskFilterState.priority) sel.value = window.coraTaskFilterState.priority;
    window.coraApplyTaskFilters();
};

// 7. Search Filter & Clear
window.coraSearchTasks = function(query) {
    window.coraTaskFilterState.search = (query || '').trim().toLowerCase();
    const clearBtn = document.getElementById('task-search-clear-btn');
    if (clearBtn) {
        if (window.coraTaskFilterState.search.length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }
    window.coraApplyTaskFilters();
};

window.coraClearSearch = function() {
    const input = document.getElementById('task-search-input');
    if (input) input.value = '';
    window.coraSearchTasks('');
};

// 8. Active Filter Counters & Reset Badges
window.coraUpdateFilterBadges = function() {
    const st = window.coraTaskFilterState;
    let activeCount = 0;

    if (st.timeframe && st.timeframe !== 'week') activeCount++;
    if (st.priority && st.priority !== 'all') activeCount++;
    if (st.client && st.client !== 'all' && st.client !== '') activeCount++;
    if (st.assignee && st.assignee !== 'all' && st.assignee !== '') activeCount++;
    if (st.search && st.search !== '') activeCount++;

    const resetBtn = document.getElementById('cora-task-reset-btn');
    const resetBadge = document.getElementById('cora-task-active-filter-badge');
    const mobBadge = document.getElementById('mobile-filter-badge');

    if (activeCount > 0) {
        if (resetBtn) {
            resetBtn.classList.remove('hidden');
            resetBtn.classList.add('flex');
        }
        if (resetBadge) resetBadge.textContent = activeCount;
        if (mobBadge) {
            mobBadge.textContent = activeCount;
            mobBadge.classList.remove('hidden');
            mobBadge.classList.add('flex');
        }
    } else {
        if (resetBtn) {
            resetBtn.classList.add('hidden');
            resetBtn.classList.remove('flex');
        }
        if (mobBadge) {
            mobBadge.classList.add('hidden');
            mobBadge.classList.remove('flex');
        }
    }
};

window.coraRenderActiveFilterChips = window.coraUpdateFilterBadges;

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

    const tfSel = document.getElementById('task-filter-timeframe');
    if (tfSel) tfSel.value = 'week';

    const priSel = document.getElementById('task-filter-priority');
    if (priSel) priSel.value = 'all';

    const clientSel = document.getElementById('task-filter-client');
    if (clientSel) clientSel.value = 'all';

    const assignSel = document.getElementById('task-filter-assignee');
    if (assignSel) assignSel.value = 'all';

    const searchInp = document.getElementById('task-search-input');
    if (searchInp) searchInp.value = '';

    const clearBtn = document.getElementById('task-search-clear-btn');
    if (clearBtn) clearBtn.classList.add('hidden');

    const tray = document.getElementById('task-custom-date-tray');
    if (tray) tray.classList.add('hidden');

    // Sync mobile controls
    const mobClient = document.getElementById('mobile-filter-client');
    if (mobClient) mobClient.value = 'all';
    const mobAssign = document.getElementById('mobile-filter-assignee');
    if (mobAssign) mobAssign.value = 'all';
    
    document.querySelectorAll('.mobile-time-btn').forEach(b => {
        b.classList.remove('active', 'bg-zinc-950', 'text-white');
        b.classList.add('bg-zinc-100', 'text-zinc-700');
        if (b.getAttribute('data-time') === 'week') {
            b.classList.add('active', 'bg-zinc-950', 'text-white');
            b.classList.remove('bg-zinc-100', 'text-zinc-700');
        }
    });

    document.querySelectorAll('.mobile-pri-btn').forEach(b => {
        b.classList.remove('active', 'bg-zinc-950', 'text-white');
        b.classList.add('bg-zinc-100', 'text-zinc-700');
        if (b.getAttribute('data-pri') === 'all') {
            b.classList.add('active', 'bg-zinc-950', 'text-white');
            b.classList.remove('bg-zinc-100', 'text-zinc-700');
        }
    });

    const mobDateBox = document.getElementById('mobile-custom-date-box');
    if (mobDateBox) mobDateBox.classList.add('hidden');

    window.coraApplyTaskFilters();
    if (window.coraShowToast) window.coraShowToast('All filters reset to default', 'success');
};

// 10. Mobile Filter Bottom-Up Slide Sheet Engine (Rule 1 & Rule 12)
window.coraOpenMobileFiltersSheet = function() {
    if (window.innerWidth >= 768) return; // Strict: Never trigger on desktop
    const backdrop = document.getElementById('cora-task-mobile-filter-backdrop');
    const sheet = document.getElementById('cora-task-mobile-filter-sheet');
    if (!sheet) return;

    // Attach to document.body to avoid parent overflow clipping
    if (sheet.parentElement !== document.body) {
        if (backdrop) document.body.appendChild(backdrop);
        document.body.appendChild(sheet);
    }

    // Sync current state to mobile inputs
    const st = window.coraTaskFilterState;
    document.querySelectorAll('.mobile-time-btn').forEach(b => {
        b.classList.remove('active', 'bg-zinc-950', 'text-white');
        b.classList.add('bg-zinc-100', 'text-zinc-700');
        if (b.getAttribute('data-time') === st.timeframe) {
            b.classList.add('active', 'bg-zinc-950', 'text-white');
            b.classList.remove('bg-zinc-100', 'text-zinc-700');
        }
    });

    document.querySelectorAll('.mobile-pri-btn').forEach(b => {
        b.classList.remove('active', 'bg-zinc-950', 'text-white');
        b.classList.add('bg-zinc-100', 'text-zinc-700');
        if (b.getAttribute('data-pri') === st.priority) {
            b.classList.add('active', 'bg-zinc-950', 'text-white');
            b.classList.remove('bg-zinc-100', 'text-zinc-700');
        }
    });

    const mobClient = document.getElementById('mobile-filter-client');
    if (mobClient) mobClient.value = st.client || 'all';

    const mobAssign = document.getElementById('mobile-filter-assignee');
    if (mobAssign) mobAssign.value = st.assignee || 'all';

    const mobDateBox = document.getElementById('mobile-custom-date-box');
    if (mobDateBox) {
        if (st.timeframe === 'custom') {
            mobDateBox.classList.remove('hidden');
            const sInp = document.getElementById('mobile-custom-start-date');
            const eInp = document.getElementById('mobile-custom-end-date');
            if (sInp) sInp.value = st.customStart || '';
            if (eInp) eInp.value = st.customEnd || '';
        } else {
            mobDateBox.classList.add('hidden');
        }
    }

    if (backdrop) {
        backdrop.style.display = 'block';
        backdrop.classList.remove('hidden', 'pointer-events-none');
        requestAnimationFrame(() => {
            backdrop.classList.add('open');
        });
    }
    sheet.style.display = 'flex';
    sheet.classList.remove('hidden', 'pointer-events-none', 'translate-y-full');
    requestAnimationFrame(() => {
        sheet.classList.add('open', 'translate-y-0');
        sheet.style.transform = 'translateY(0)';
    });
    document.body.style.overflow = 'hidden';
};

window.coraCloseMobileFiltersSheet = function() {
    const backdrop = document.getElementById('cora-task-mobile-filter-backdrop');
    const sheet = document.getElementById('cora-task-mobile-filter-sheet');
    if (backdrop) {
        backdrop.classList.remove('open');
        backdrop.classList.add('pointer-events-none');
        setTimeout(() => {
            if (!backdrop.classList.contains('open')) {
                backdrop.classList.add('hidden');
                backdrop.style.display = 'none';
            }
        }, 300);
    }
    if (sheet) {
        sheet.classList.remove('open', 'translate-y-0');
        sheet.classList.add('pointer-events-none', 'translate-y-full');
        sheet.style.transform = 'translateY(100%)';
        setTimeout(() => {
            if (!sheet.classList.contains('open')) {
                sheet.classList.add('hidden');
                sheet.style.display = 'none';
            }
        }, 300);
    }
    document.body.style.overflow = '';
};

window.coraSetMobileTimeframe = function(timeframe, btn) {
    document.querySelectorAll('.mobile-time-btn').forEach(b => {
        b.classList.remove('active', 'bg-zinc-950', 'text-white');
        b.classList.add('bg-zinc-100', 'text-zinc-700');
    });
    if (btn) {
        btn.classList.add('active', 'bg-zinc-950', 'text-white');
        btn.classList.remove('bg-zinc-100', 'text-zinc-700');
    }
    const box = document.getElementById('mobile-custom-date-box');
    if (box) {
        if (timeframe === 'custom') {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }
};

window.coraSetMobilePriority = function(pri, btn) {
    document.querySelectorAll('.mobile-pri-btn').forEach(b => {
        b.classList.remove('active', 'bg-zinc-950', 'text-white');
        b.classList.add('bg-zinc-100', 'text-zinc-700');
    });
    if (btn) {
        btn.classList.add('active', 'bg-zinc-950', 'text-white');
        btn.classList.remove('bg-zinc-100', 'text-zinc-700');
    }
};

window.coraApplyMobileFilters = function() {
    const activeTimeBtn = document.querySelector('.mobile-time-btn.active');
    const timeframe = activeTimeBtn ? activeTimeBtn.getAttribute('data-time') : 'week';

    const activePriBtn = document.querySelector('.mobile-pri-btn.active');
    const priority = activePriBtn ? activePriBtn.getAttribute('data-pri') : 'all';

    const mobClient = document.getElementById('mobile-filter-client');
    const client = mobClient ? mobClient.value : 'all';

    const mobAssign = document.getElementById('mobile-filter-assignee');
    const assignee = mobAssign ? mobAssign.value : 'all';

    window.coraTaskFilterState.timeframe = timeframe;
    window.coraTaskFilterState.priority = priority;
    window.coraTaskFilterState.client = (client || 'all').toLowerCase();
    window.coraTaskFilterState.assignee = (assignee || 'all').toLowerCase();

    if (timeframe === 'custom') {
        const sInp = document.getElementById('mobile-custom-start-date');
        const eInp = document.getElementById('mobile-custom-end-date');
        window.coraTaskFilterState.customStart = sInp ? sInp.value : '';
        window.coraTaskFilterState.customEnd = eInp ? eInp.value : '';
    } else {
        window.coraTaskFilterState.customStart = '';
        window.coraTaskFilterState.customEnd = '';
    }

    // Sync desktop selectors
    const tfSel = document.getElementById('task-filter-timeframe');
    if (tfSel) tfSel.value = timeframe;

    const priSel = document.getElementById('task-filter-priority');
    if (priSel) priSel.value = priority;

    const clSel = document.getElementById('task-filter-client');
    if (clSel) clSel.value = window.coraTaskFilterState.client;

    const asSel = document.getElementById('task-filter-assignee');
    if (asSel) asSel.value = window.coraTaskFilterState.assignee;

    window.coraApplyTaskFilters();
    window.coraCloseMobileFiltersSheet();
    if (window.coraShowToast) window.coraShowToast('Filters applied', 'success');
};

window.coraResetMobileFilters = function() {
    window.coraResetAllTaskFilters();
    window.coraCloseMobileFiltersSheet();
};

// 10. Task Details & Work Management Drawer Engine
window.openTaskDrawer = function(taskId) {
    let taskObj = null;
    if (Array.isArray(window.coraTasksData)) {
        taskObj = window.coraTasksData.find(t => String(t.id) === String(taskId));
    }
    
    // Fallback read from DOM card if not in array
    const taskCard = document.querySelector(`.cora-task-card[data-id="${taskId}"]`);
    if (!taskObj && taskCard) {
        taskObj = {
            id: taskId,
            client_name: taskCard.getAttribute('data-client') || 'Client',
            title: taskCard.getAttribute('data-title') || 'Task Title',
            category: taskCard.getAttribute('data-category') || 'GENERAL',
            status: taskCard.getAttribute('data-status') || 'todo',
            priority: taskCard.getAttribute('data-priority') || 'medium',
            assignee: taskCard.getAttribute('data-assignee') || 'Studio Admin',
            assignee_name: taskCard.getAttribute('data-assignee') || 'Studio Admin',
            due_date: taskCard.getAttribute('data-due-date') || '<?php echo date('Y-m-d'); ?>',
            notes: taskCard.getAttribute('data-notes') || '',
            progress: parseInt(taskCard.getAttribute('data-progress') || '0', 10),
            subtasks: [],
            comments: [],
            links: []
        };
        if (Array.isArray(window.coraTasksData)) {
            window.coraTasksData.push(taskObj);
        }
    }

    if (!taskObj) return;
    window.coraActiveTask = taskObj;

    // Ensure subtasks array exists
    if (!Array.isArray(taskObj.subtasks) || taskObj.subtasks.length === 0) {
        taskObj.subtasks = [
            { id: 'st-1', title: 'Initial scope intake & asset check', completed: true },
            { id: 'st-2', title: 'Execution sprint deliverables', completed: taskObj.status === 'done' || taskObj.status === 'review' },
            { id: 'st-3', title: 'Quality assurance and client sign-off', completed: taskObj.status === 'done' }
        ];
    }
    if (!Array.isArray(taskObj.comments)) taskObj.comments = [];
    if (!Array.isArray(taskObj.links)) taskObj.links = [];

    // Populate Header & Title
    document.getElementById('drawer-task-id').textContent = '#' + String(taskId).toUpperCase();
    document.getElementById('drawer-task-status-select').value = taskObj.status || 'todo';
    document.getElementById('drawer-task-priority-select').value = (taskObj.priority || 'medium').toLowerCase();
    document.getElementById('drawer-task-category-badge').textContent = taskObj.category || 'GENERAL';
    document.getElementById('drawer-task-title-input').value = taskObj.title || '';

    // Populate Properties Grid
    const assignSelect = document.getElementById('drawer-task-assignee-select');
    if (assignSelect) assignSelect.value = taskObj.assignee || taskObj.assignee_name || 'Studio Admin';

    const clientInput = document.getElementById('drawer-task-client-input');
    if (clientInput) clientInput.value = taskObj.client_name || taskObj.client || '';

    const dueInput = document.getElementById('drawer-task-due-date-input');
    if (dueInput) dueInput.value = taskObj.due_date || '<?php echo date('Y-m-d'); ?>';

    const catSelect = document.getElementById('drawer-task-category-select');
    if (catSelect) catSelect.value = taskObj.category || 'GENERAL';

    // Populate Scope Notes
    const notesInput = document.getElementById('drawer-task-notes-input');
    if (notesInput) notesInput.value = taskObj.notes || '';

    // Reset to checklist tab on open
    window.coraSetTaskDrawerTab('checklist');

    // Render Subtasks, Links, Comments
    window.coraRenderDrawerSubtasks();
    window.coraRenderDrawerLinks();
    window.coraRenderDrawerComments();
    window.coraUpdateAdvanceButtonLabel();

    // Open Main-Area Docked Task Drawer (Rule 15 SOP)
    const drawer = document.getElementById('cora-task-drawer');
    const mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');

    // Body Portaling: Guarantee drawer sits at document.body level with no clipping or layout offset
    if (drawer && drawer.parentNode !== document.body) {
        document.body.appendChild(drawer);
    }

    if (drawer) {
        drawer.classList.remove('pointer-events-none', 'collapsed');
        drawer.classList.add('open');

        // Hide mobile floating bottom bar so it never overlaps or intercepts drawer touches
        var island = document.getElementById('cora-mobile-floating-island');
        if (island) {
            island.classList.add('cora-island-hidden');
        }

        // Immediately sync bounds to sidebar edge
        if (window.coraSyncTaskDrawerBounds) {
            window.coraSyncTaskDrawerBounds();
        }

        // Lock ONLY main content area scroll; SIDEBAR REMAINS UNLOCKED & FULLY ACCESSIBLE
        if (mainEl) {
            mainEl.classList.add('overflow-hidden');
        }
    }
};

window.closeTaskDrawer = function() {
    const drawer = document.getElementById('cora-task-drawer');
    const mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');

    if (drawer) {
        drawer.classList.add('pointer-events-none', 'collapsed');
        drawer.classList.remove('open', 'maximized');

        // Restore mobile floating bottom bar
        var island = document.getElementById('cora-mobile-floating-island');
        if (island) {
            island.classList.remove('cora-island-hidden');
        }

        // Restore main content area scroll
        if (mainEl) {
            mainEl.classList.remove('overflow-hidden');
        }
    }
    window.coraActiveTask = null;
};

// 11. Tab Switching Engine for Task Details Drawer (Level 01 Segmented Pill Tabs)
window.coraActiveTaskDrawerTab = 'checklist';
window.coraSetTaskDrawerTab = function(tabKey) {
    window.coraActiveTaskDrawerTab = tabKey;
    
    document.querySelectorAll('.task-drawer-tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        btn.classList.add('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
    });
    
    const activeBtn = document.getElementById('task-tab-btn-' + tabKey);
    if (activeBtn) {
        activeBtn.classList.add('active', 'bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'shadow-2xs', 'font-bold');
        activeBtn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'font-medium', 'bg-transparent');
    }
    
    document.querySelectorAll('.task-drawer-tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });
    
    const activePanel = document.getElementById('task-tab-panel-' + tabKey);
    if (activePanel) {
        activePanel.classList.remove('hidden');
    }
};

// Subtask Checklist Renderer
window.coraRenderDrawerSubtasks = function() {
    const task = window.coraActiveTask;
    if (!task) return;

    const list = document.getElementById('drawer-subtasks-list');
    if (!list) return;

    const subtasks = task.subtasks || [];
    const total = subtasks.length;
    const completed = subtasks.filter(s => s.completed).length;
    const pct = total > 0 ? Math.round((completed / total) * 100) : 0;
    task.progress = pct;

    // Update Progress Indicators & Tab Badge
    const label = document.getElementById('drawer-subtask-progress-label');
    if (label) label.textContent = `${completed} of ${total} (${pct}%)`;

    const bar = document.getElementById('drawer-subtask-progress-bar');
    if (bar) bar.style.width = pct + '%';

    const tabBadge = document.getElementById('tab-badge-checklist-count');
    if (tabBadge) tabBadge.textContent = total;

    // Sync card on Kanban board
    const card = document.querySelector(`.cora-task-card[data-id="${task.id}"]`);
    if (card) {
        card.setAttribute('data-progress', pct);
        const cardProgressVal = card.querySelector('.task-card-progress-val');
        if (cardProgressVal) cardProgressVal.textContent = pct + '%';
        const cardProgressBar = card.querySelector('.task-card-progress-bar');
        if (cardProgressBar) cardProgressBar.style.width = pct + '%';
        const cardSubtaskCount = card.querySelector('.task-card-subtask-count');
        if (cardSubtaskCount) cardSubtaskCount.textContent = `${completed}/${total} done`;
    }

    if (total === 0) {
        list.innerHTML = '<p class="text-zinc-400 text-[11px] italic py-2">No subtasks yet. Add one below.</p>';
        return;
    }

    list.innerHTML = subtasks.map(st => `
        <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-zinc-50/70 hover:bg-zinc-100/80 border border-zinc-200/60 transition-colors group">
            <label class="flex items-center gap-2.5 flex-1 min-w-0 cursor-pointer select-none">
                <input type="checkbox" ${st.completed ? 'checked' : ''} onchange="window.coraToggleSubtask('${st.id}')" class="w-4 h-4 rounded text-zinc-900 focus:ring-0 cursor-pointer accent-zinc-950">
                <span class="text-xs text-zinc-800 truncate ${st.completed ? 'line-through text-zinc-400' : 'font-medium'}">${escapeHtml(st.title)}</span>
            </label>
            <button type="button" onclick="window.coraDeleteSubtask('${st.id}')" title="Delete subtask" class="w-5 h-5 rounded hover:bg-zinc-200 text-zinc-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center cursor-pointer border-0 bg-transparent">
                ✕
            </button>
        </div>
    `).join('');
};

window.coraToggleSubtask = function(subtaskId) {
    const task = window.coraActiveTask;
    if (!task || !Array.isArray(task.subtasks)) return;

    const st = task.subtasks.find(s => String(s.id) === String(subtaskId));
    if (st) {
        st.completed = !st.completed;
        window.coraRenderDrawerSubtasks();
        window.coraPersistActiveTaskToServer('Subtask checklist updated');
    }
};

window.coraAddSubtask = function(e) {
    e.preventDefault();
    const input = document.getElementById('drawer-new-subtask-input');
    if (!input) return;
    const title = input.value.trim();
    if (!title) return;

    const task = window.coraActiveTask;
    if (!task) return;
    if (!Array.isArray(task.subtasks)) task.subtasks = [];

    const newSubtask = {
        id: 'st-' + Date.now().toString().slice(-4),
        title: title,
        completed: false
    };
    task.subtasks.push(newSubtask);
    input.value = '';

    window.coraRenderDrawerSubtasks();
    window.coraPersistActiveTaskToServer('Subtask added');
};

window.coraDeleteSubtask = function(subtaskId) {
    const task = window.coraActiveTask;
    if (!task || !Array.isArray(task.subtasks)) return;

    task.subtasks = task.subtasks.filter(s => String(s.id) !== String(subtaskId));
    window.coraRenderDrawerSubtasks();
    window.coraPersistActiveTaskToServer('Subtask removed');
};

// Direct Field Updater
window.coraSaveTaskField = function(fieldName, value) {
    const task = window.coraActiveTask;
    if (!task) return;

    const card = document.querySelector(`.cora-task-card[data-id="${task.id}"]`);

    if (fieldName === 'title') {
        task.title = value;
        if (card) {
            card.setAttribute('data-title', value);
            const titleEl = card.querySelector('.task-card-title');
            if (titleEl) titleEl.textContent = value;
        }
    } else if (fieldName === 'assignee') {
        task.assignee = value;
        task.assignee_name = value;
        if (card) {
            card.setAttribute('data-assignee', value);
            const assignEl = card.querySelector('.task-card-assignee');
            if (assignEl) assignEl.textContent = value;
            const initEl = card.querySelector('.task-card-avatar');
            if (initEl) initEl.textContent = value.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase();
        }
    } else if (fieldName === 'client') {
        task.client_name = value;
        task.client = value;
        if (card) {
            card.setAttribute('data-client', value);
            const clientEl = card.querySelector('.task-card-client');
            if (clientEl) clientEl.textContent = value;
        }
    } else if (fieldName === 'due_date') {
        task.due_date = value;
        if (card) {
            card.setAttribute('data-due-date', value);
            const dueEl = card.querySelector('.task-card-due-label');
            if (dueEl) dueEl.textContent = value;
        }
    } else if (fieldName === 'category') {
        task.category = value;
        document.getElementById('drawer-task-category-badge').textContent = value;
        if (card) {
            card.setAttribute('data-category', value);
            const catEl = card.querySelector('.task-card-category');
            if (catEl) catEl.textContent = value;
        }
    } else if (fieldName === 'notes') {
        task.notes = value;
        if (card) card.setAttribute('data-notes', value);
    }

    window.coraPersistActiveTaskToServer('Task updated');
};

window.coraDrawerStatusChange = function(newStatus) {
    const task = window.coraActiveTask;
    if (!task) return;
    task.status = newStatus;
    
    // Move card in Kanban DOM
    const card = document.querySelector(`.cora-task-card[data-id="${task.id}"]`);
    const targetCol = document.querySelector(`.cora-task-kanban-column[data-status="${newStatus}"]`);
    if (card && targetCol) {
        card.setAttribute('data-status', newStatus);
        const container = targetCol.querySelector('.cora-task-cards-container');
        if (container) {
            const placeholder = container.querySelector('.task-empty-placeholder');
            if (placeholder) placeholder.remove();
            container.appendChild(card);
        }
    }

    window.coraSyncColumnPlaceholders();
    window.coraApplyTaskFilters();
    window.coraUpdateAdvanceButtonLabel();
    window.coraPersistActiveTaskToServer('Status set to ' + newStatus.toUpperCase());
};

window.coraDrawerPriorityChange = function(newPriority) {
    const task = window.coraActiveTask;
    if (!task) return;
    task.priority = newPriority;

    const card = document.querySelector(`.cora-task-card[data-id="${task.id}"]`);
    if (card) {
        card.setAttribute('data-priority', newPriority);
        const badge = card.querySelector('.task-card-priority-badge');
        if (badge) {
            badge.textContent = newPriority;
            let badgeBg = 'bg-zinc-100 text-zinc-600';
            if (newPriority === 'urgent') badgeBg = 'bg-red-50 text-red-700 font-bold';
            if (newPriority === 'high') badgeBg = 'bg-amber-50 text-amber-800 font-bold';
            if (newPriority === 'low') badgeBg = 'bg-zinc-100 text-zinc-500';
            badge.className = `task-card-priority-badge px-1.5 py-0.5 rounded text-[9px] uppercase ${badgeBg}`;
        }
    }

    window.coraPersistActiveTaskToServer('Priority updated');
};

window.coraAdvanceTaskStage = function() {
    const task = window.coraActiveTask;
    if (!task) return;
    const stages = ['todo', 'in_progress', 'review', 'done'];
    const currIdx = stages.indexOf(task.status || 'todo');
    if (currIdx < stages.length - 1) {
        const nextStage = stages[currIdx + 1];
        document.getElementById('drawer-task-status-select').value = nextStage;
        window.coraDrawerStatusChange(nextStage);
    }
};

window.coraUpdateAdvanceButtonLabel = function() {
    const task = window.coraActiveTask;
    const btn = document.getElementById('drawer-advance-stage-btn');
    if (!btn || !task) return;
    if (task.status === 'todo') btn.textContent = 'Start Execution →';
    else if (task.status === 'in_progress') btn.textContent = 'Submit for Review →';
    else if (task.status === 'review') btn.textContent = 'Mark Completed';
    else btn.textContent = 'Completed';
};

// Links Renderer & Adder (Monochromatic Form - No native prompts)
window.coraRenderDrawerLinks = function() {
    const task = window.coraActiveTask;
    const container = document.getElementById('drawer-asset-links-list');
    const tabBadge = document.getElementById('tab-badge-assets-count');
    if (!container || !task) return;
    const links = task.links || [];

    if (tabBadge) tabBadge.textContent = links.length;

    if (links.length === 0) {
        container.innerHTML = '<p class="text-zinc-400 text-[11px] italic py-2">No attached deliverable links yet.</p>';
        return;
    }

    container.innerHTML = links.map((lnk, idx) => `
        <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-zinc-50 border border-zinc-200/60 hover:bg-zinc-100 transition-colors">
            <a href="${escapeHtml(lnk.url)}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 text-zinc-900 hover:text-zinc-950 font-medium text-xs truncate text-decoration-none">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                <span class="truncate">${escapeHtml(lnk.title || lnk.url)}</span>
            </a>
            <button type="button" onclick="window.coraDeleteLink(${idx})" title="Remove link" class="text-zinc-400 hover:text-red-600 text-xs border-0 bg-transparent cursor-pointer p-1">✕</button>
        </div>
    `).join('');
};

window.coraAddAssetLinkSubmit = function() {
    const task = window.coraActiveTask;
    if (!task) return;
    const titleInput = document.getElementById('drawer-new-link-title');
    const urlInput = document.getElementById('drawer-new-link-url');
    if (!urlInput) return;
    const url = urlInput.value.trim();
    if (!url) {
        if (window.coraShowToast) window.coraShowToast('Please enter a valid deliverable URL', 'error');
        return;
    }
    const title = (titleInput && titleInput.value.trim()) ? titleInput.value.trim() : url;
    if (!Array.isArray(task.links)) task.links = [];
    task.links.push({ title: title, url: url });

    if (titleInput) titleInput.value = '';
    urlInput.value = '';

    window.coraRenderDrawerLinks();
    window.coraPersistActiveTaskToServer('Deliverable asset link attached');
};

window.coraDeleteLink = function(idx) {
    const task = window.coraActiveTask;
    if (!task || !Array.isArray(task.links)) return;
    task.links.splice(idx, 1);
    window.coraRenderDrawerLinks();
    window.coraPersistActiveTaskToServer('Asset link removed');
};

// Comments Renderer
window.coraRenderDrawerComments = function() {
    const task = window.coraActiveTask;
    const container = document.getElementById('drawer-comments-feed');
    const countEl = document.getElementById('drawer-comments-count');
    const tabBadge = document.getElementById('tab-badge-activity-count');
    if (!container || !task) return;
    const comments = task.comments || [];

    if (countEl) countEl.textContent = `${comments.length} update${comments.length === 1 ? '' : 's'}`;
    if (tabBadge) tabBadge.textContent = comments.length;

    if (comments.length === 0) {
        container.innerHTML = '<p class="text-zinc-400 text-[11px] italic py-2">No activity logged yet.</p>';
        return;
    }

    container.innerHTML = comments.map(c => `
        <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-zinc-50 border border-zinc-200/60">
            <div class="w-6 h-6 rounded-full bg-zinc-950 text-white font-bold text-[9px] flex items-center justify-center shrink-0">
                ${escapeHtml(c.initials || 'SA')}
            </div>
            <div class="flex-1 min-w-0 space-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-zinc-900 text-[11px]">${escapeHtml(c.author || 'Studio Admin')}</span>
                    <span class="text-[9.5px] text-zinc-400 font-mono">${escapeHtml(c.time || 'Just now')}</span>
                </div>
                <p class="text-xs text-zinc-700 leading-relaxed">${escapeHtml(c.text)}</p>
            </div>
        </div>
    `).join('');
};

window.coraAddComment = function(e) {
    e.preventDefault();
    const input = document.getElementById('drawer-new-comment-input');
    if (!input) return;
    const text = input.value.trim();
    if (!text) return;

    const task = window.coraActiveTask;
    if (!task) return;
    if (!Array.isArray(task.comments)) task.comments = [];

    const newComment = {
        id: 'c-' + Date.now().toString().slice(-4),
        author: 'Studio Admin',
        initials: 'SA',
        time: 'Just now',
        text: text
    };
    task.comments.unshift(newComment);
    input.value = '';

    window.coraRenderDrawerComments();
    window.coraPersistActiveTaskToServer('Work log comment posted');
};

// Generic Server Persister Helper
window.coraPersistActiveTaskToServer = function(toastMessage) {
    const task = window.coraActiveTask;
    if (!task) return;

    const ajaxUrl = window.coraAjaxUrl || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxUrl) || '/wp-admin/admin-ajax.php';
    const nonce = window.coraAjaxNonce || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxNonce) || '';

    const formData = new URLSearchParams();
    formData.append('action', 'cora_save_client_task');
    formData.append('nonce', nonce);
    formData.append('task', JSON.stringify(task));

    fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) {
            if (toastMessage && window.coraShowToast) {
                window.coraShowToast(toastMessage, 'success');
            }
        }
    })
    .catch(err => {
        console.error('Task persistence failed:', err);
    });
};

function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// 11. Create Task Main-Area Docked Panel (Rule 15 SOP)
window.openCreateTaskDrawer = function(stage) {
    document.getElementById('create-task-stage').value = stage || 'todo';
    document.getElementById('cora-create-task-form').reset();
    
    const drawer = document.getElementById('cora-create-task-drawer');
    const mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');

    // Body Portaling: Guarantee drawer sits at document.body level with no clipping or layout offset
    if (drawer && drawer.parentNode !== document.body) {
        document.body.appendChild(drawer);
    }

    if (drawer) {
        drawer.classList.remove('pointer-events-none', 'collapsed');
        drawer.classList.add('open');

        // Hide mobile floating bottom bar so it never overlaps or intercepts drawer touches
        var island = document.getElementById('cora-mobile-floating-island');
        if (island) {
            island.classList.add('cora-island-hidden');
        }

        // Immediately sync bounds to sidebar edge
        if (window.coraSyncTaskDrawerBounds) {
            window.coraSyncTaskDrawerBounds();
        }

        // Lock ONLY main content area scroll; SIDEBAR REMAINS UNLOCKED & FULLY ACCESSIBLE
        if (mainEl) {
            mainEl.classList.add('overflow-hidden');
        }
    }
};

window.closeCreateTaskDrawer = function() {
    const drawer = document.getElementById('cora-create-task-drawer');
    const mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');

    if (drawer) {
        drawer.classList.add('pointer-events-none', 'collapsed');
        drawer.classList.remove('open', 'maximized');

        // Restore mobile floating bottom bar
        var island = document.getElementById('cora-mobile-floating-island');
        if (island) {
            island.classList.remove('cora-island-hidden');
        }

        // Restore main content area scroll
        if (mainEl) {
            mainEl.classList.remove('overflow-hidden');
        }
    }
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
            card.setAttribute('oncontextmenu', 'window.coraHandleTaskContextMenu(event, this)');
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
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="event.stopPropagation(); window.coraHandleTaskContextMenu(event, this.closest('.cora-task-card'))" title="Task Options (Right-Click)" class="w-5 h-5 rounded hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-colors border-0 bg-transparent cursor-pointer">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                        </button>
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
    window.coraMoveTaskToStage(taskId, newStage);
    window.closeTaskDrawer();
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

// 15. Task Object Persistence Helper
window.coraPersistTaskObject = function(taskObj, toastMessage) {
    if (!taskObj) return;
    const ajaxUrl = window.coraAjaxUrl || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxUrl) || '/wp-admin/admin-ajax.php';
    const nonce = window.coraAjaxNonce || (window.coraWorkspaceConfig && window.coraWorkspaceConfig.ajaxNonce) || '';

    const formData = new URLSearchParams();
    formData.append('action', 'cora_save_client_task');
    formData.append('nonce', nonce);
    formData.append('task', JSON.stringify(taskObj));

    fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) {
            if (toastMessage && window.coraShowToast) {
                window.coraShowToast(toastMessage, 'success');
            }
        }
    })
    .catch(err => {
        console.error('Task persistence failed:', err);
    });
};

// 16. Move Task To Stage Engine
window.coraMoveTaskToStage = function(taskId, newStage) {
    const card = document.querySelector(`.cora-task-card[data-id="${taskId}"]`);
    const targetCol = document.querySelector(`.cora-task-kanban-column[data-status="${newStage}"]`);
    
    if (card && targetCol) {
        card.setAttribute('data-status', newStage);
        const container = targetCol.querySelector('.cora-task-cards-container');
        if (container) {
            const placeholder = container.querySelector('.task-empty-placeholder');
            if (placeholder) placeholder.remove();
            container.appendChild(card);
        }
    }

    if (Array.isArray(window.coraTasksData)) {
        const tObj = window.coraTasksData.find(t => String(t.id) === String(taskId));
        if (tObj) tObj.status = newStage;
    }

    window.coraSyncColumnPlaceholders();
    window.coraApplyTaskFilters();

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
            if (window.coraShowToast) window.coraShowToast('Moved task to ' + newStage.toUpperCase(), 'success');
        }
    })
    .catch(err => {
        console.error('Task status update failed:', err);
    });
};

// 17. Right-Click Command Menu Controller
window.coraActiveTaskCmdData = null;

window.coraHandleTaskContextMenu = function(e, cardEl) {
    if (e && e.preventDefault) e.preventDefault();
    if (e && e.stopPropagation) e.stopPropagation();
    if (!cardEl) return;

    let clientX = e.clientX;
    let clientY = e.clientY;

    if (!clientX && !clientY) {
        if (e.touches && e.touches[0]) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            const rect = cardEl.getBoundingClientRect();
            clientX = rect.left + rect.width / 2;
            clientY = rect.top + rect.height / 2;
        }
    }

    window.coraOpenTaskCommandMenu(cardEl, clientX, clientY);
};

window.coraOpenTaskCommandMenu = function(cardEl, x, y) {
    const menu = document.getElementById('cora-task-command-menu');
    if (!menu) return;

    // Close any other open popovers/menus
    if (typeof window.coraCloseLeadCommandMenu === 'function') window.coraCloseLeadCommandMenu();

    const taskId = cardEl.getAttribute('data-id') || '';
    let task = null;
    if (Array.isArray(window.coraTasksData)) {
        task = window.coraTasksData.find(t => String(t.id) === String(taskId));
    }
    if (!task) {
        task = {
            id: taskId,
            title: cardEl.getAttribute('data-title') || 'Task',
            client_name: cardEl.getAttribute('data-client') || 'Client',
            category: cardEl.getAttribute('data-category') || 'GENERAL',
            status: cardEl.getAttribute('data-status') || 'todo',
            priority: cardEl.getAttribute('data-priority') || 'medium',
            assignee: cardEl.getAttribute('data-assignee') || 'Studio Admin',
            due_date: cardEl.getAttribute('data-due-date') || '<?php echo date('Y-m-d'); ?>',
            notes: cardEl.getAttribute('data-notes') || '',
            progress: parseInt(cardEl.getAttribute('data-progress') || '0', 10)
        };
    }

    window.coraActiveTaskCmdData = {
        element: cardEl,
        task: task
    };

    // Update Header Pill in Menu
    const keyEl = document.getElementById('cora-cmd-task-key');
    const clientEl = document.getElementById('cora-cmd-task-client');
    const titleEl = document.getElementById('cora-cmd-task-title');
    const statusBadge = document.getElementById('cora-cmd-task-status-badge');
    const advanceLabel = document.getElementById('cora-cmd-advance-label');

    if (keyEl) keyEl.textContent = task.id.toUpperCase();
    if (clientEl) clientEl.textContent = task.client_name || task.client || 'Client';
    if (titleEl) titleEl.textContent = task.title || 'Task';

    const stageMap = {
        'todo': { label: 'To Do', next: 'In Execution', bg: 'bg-emerald-50 text-emerald-800 border-emerald-200' },
        'in_progress': { label: 'In Execution', next: 'Client Review', bg: 'bg-amber-50 text-amber-800 border-amber-200' },
        'review': { label: 'Client Review', next: 'Completed', bg: 'bg-purple-50 text-purple-800 border-purple-200' },
        'done': { label: 'Completed', next: 'Completed', bg: 'bg-blue-50 text-blue-800 border-blue-200' }
    };

    const currentStageInfo = stageMap[task.status] || stageMap['todo'];
    if (statusBadge) {
        statusBadge.className = `px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase shrink-0 border ${currentStageInfo.bg}`;
        statusBadge.textContent = currentStageInfo.label;
    }

    if (advanceLabel) {
        if (task.status === 'done') {
            advanceLabel.textContent = 'Already Completed';
        } else {
            advanceLabel.textContent = `Move to ${currentStageInfo.next}`;
        }
    }

    // Measure and Position Viewport-Clamped Menu
    menu.classList.remove('hidden');
    const menuWidth = menu.offsetWidth || 288;
    const menuHeight = menu.offsetHeight || 380;

    let posX = x;
    let posY = y;

    if (posX + menuWidth > window.innerWidth - 12) {
        posX = window.innerWidth - menuWidth - 12;
    }
    if (posY + menuHeight > window.innerHeight - 12) {
        posY = window.innerHeight - menuHeight - 12;
    }
    if (posX < 12) posX = 12;
    if (posY < 12) posY = 12;

    menu.style.left = posX + 'px';
    menu.style.top = posY + 'px';
};

window.coraCloseTaskCommandMenu = function() {
    const menu = document.getElementById('cora-task-command-menu');
    if (menu) menu.classList.add('hidden');
    window.coraActiveTaskCmdData = null;
};

window.coraTaskCmdAction = function(action, param) {
    if (!window.coraActiveTaskCmdData) return;
    const data = window.coraActiveTaskCmdData;
    const task = data.task;
    const cardEl = data.element || document.querySelector(`.cora-task-card[data-id="${task.id}"]`);

    window.coraCloseTaskCommandMenu();

    if (action === 'view') {
        window.openTaskDrawer(task.id);
    } else if (action === 'advance') {
        const stages = ['todo', 'in_progress', 'review', 'done'];
        const currIdx = stages.indexOf(task.status || 'todo');
        if (currIdx < stages.length - 1) {
            const nextStage = stages[currIdx + 1];
            window.coraMoveTaskToStage(task.id, nextStage);
        } else {
            if (window.coraShowToast) window.coraShowToast('Task is already completed', 'info');
        }
    } else if (action === 'stage') {
        const newStage = param;
        if (!newStage || newStage === task.status) return;
        window.coraMoveTaskToStage(task.id, newStage);
    } else if (action === 'priority') {
        const newPriority = param;
        if (!newPriority || newPriority === task.priority) return;
        task.priority = newPriority;
        if (cardEl) {
            cardEl.setAttribute('data-priority', newPriority);
            const badge = cardEl.querySelector('.task-card-priority-badge');
            if (badge) {
                let badgeBg = 'bg-zinc-100 text-zinc-600';
                if (newPriority === 'urgent') badgeBg = 'bg-red-50 text-red-700 font-bold';
                if (newPriority === 'high') badgeBg = 'bg-amber-50 text-amber-800 font-bold';
                if (newPriority === 'low') badgeBg = 'bg-zinc-100 text-zinc-500';
                badge.className = `task-card-priority-badge px-1.5 py-0.5 rounded text-[9px] uppercase ${badgeBg}`;
                badge.textContent = newPriority;
            }
        }
        window.coraPersistTaskObject(task, 'Priority set to ' + newPriority.toUpperCase());
    } else if (action === 'assignee') {
        const newAssignee = param;
        if (!newAssignee) return;
        task.assignee = newAssignee;
        task.assignee_name = newAssignee;
        if (cardEl) {
            cardEl.setAttribute('data-assignee', newAssignee);
            const assignEl = cardEl.querySelector('.task-card-assignee');
            if (assignEl) assignEl.textContent = newAssignee;
            const initEl = cardEl.querySelector('.task-card-avatar');
            if (initEl) initEl.textContent = newAssignee.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase();
        }
        window.coraPersistTaskObject(task, 'Assigned to ' + newAssignee);
    } else if (action === 'duplicate') {
        const copyTitle = task.title + ' (Copy)';
        const newId = 'task-' + Date.now().toString().slice(-4);
        const duplicatedTask = JSON.parse(JSON.stringify(task));
        duplicatedTask.id = newId;
        duplicatedTask.title = copyTitle;

        if (Array.isArray(window.coraTasksData)) {
            window.coraTasksData.push(duplicatedTask);
        }

        const targetStage = duplicatedTask.status || 'todo';
        const targetCol = document.querySelector(`.cora-task-kanban-column[data-status="${targetStage}"]`);
        if (targetCol) {
            const container = targetCol.querySelector('.cora-task-cards-container');
            if (container) {
                const placeholder = container.querySelector('.task-empty-placeholder');
                if (placeholder) placeholder.remove();

                const newCard = document.createElement('div');
                newCard.className = 'cora-task-card bg-white rounded-xl p-3.5 shadow-2xs border border-zinc-100 hover:shadow-xs flex flex-col gap-2 relative group overflow-hidden';
                newCard.draggable = true;
                newCard.setAttribute('data-id', newId);
                newCard.setAttribute('data-client', duplicatedTask.client_name || duplicatedTask.client || 'Client');
                newCard.setAttribute('data-title', copyTitle);
                newCard.setAttribute('data-category', duplicatedTask.category || 'GENERAL');
                newCard.setAttribute('data-status', targetStage);
                newCard.setAttribute('data-priority', duplicatedTask.priority || 'medium');
                newCard.setAttribute('data-assignee', duplicatedTask.assignee || 'Studio Admin');
                newCard.setAttribute('data-due-date', duplicatedTask.due_date || '<?php echo date('Y-m-d'); ?>');
                newCard.setAttribute('data-progress', String(duplicatedTask.progress || 0));
                newCard.setAttribute('data-notes', duplicatedTask.notes || '');
                newCard.setAttribute('data-phone', duplicatedTask.phone || '+91 98201 45892');
                newCard.setAttribute('data-email', duplicatedTask.email || 'client@example.com');
                newCard.setAttribute('ondragstart', 'window.coraTaskDragStart(event, this)');
                newCard.setAttribute('ondragend', 'window.coraTaskDragEnd(event, this)');
                newCard.setAttribute('oncontextmenu', 'window.coraHandleTaskContextMenu(event, this)');
                newCard.setAttribute('onclick', `window.openTaskDrawer('${newId}')`);

                let badgeBg = 'bg-zinc-100 text-zinc-600';
                if (duplicatedTask.priority === 'urgent') badgeBg = 'bg-red-50 text-red-700 font-bold';
                if (duplicatedTask.priority === 'high') badgeBg = 'bg-amber-50 text-amber-800 font-bold';
                if (duplicatedTask.priority === 'low') badgeBg = 'bg-zinc-100 text-zinc-500';

                const initials = (duplicatedTask.assignee || 'SA').split(' ').map(n => n[0]).join('').slice(0,2).toUpperCase();

                newCard.innerHTML = `
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[10.5px] font-semibold text-zinc-500 truncate flex items-center gap-1">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span class="task-card-client">${escapeHtml(duplicatedTask.client_name || duplicatedTask.client || 'Client')}</span>
                        </span>
                        <span class="task-card-priority-badge px-1.5 py-0.5 rounded text-[9px] uppercase ${badgeBg}">
                            ${escapeHtml(duplicatedTask.priority || 'medium')}
                        </span>
                    </div>
                    <div>
                        <h4 class="task-card-title text-xs font-semibold text-zinc-900 leading-snug line-clamp-2">${escapeHtml(copyTitle)}</h4>
                        <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                            <span class="task-card-category px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-500 text-[8.5px] font-mono font-medium uppercase">${escapeHtml(duplicatedTask.category || 'GENERAL')}</span>
                            <span class="text-[9.5px] font-medium text-zinc-400 flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                <span class="task-card-due-label">${escapeHtml(duplicatedTask.due_date || '')}</span>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-zinc-100 text-xs">
                        <div class="flex items-center gap-1.5">
                            <div class="task-card-avatar w-5 h-5 rounded-full bg-zinc-900 text-white font-bold text-[8.5px] flex items-center justify-center shrink-0">${initials}</div>
                            <span class="task-card-assignee text-[10px] text-zinc-600 font-medium truncate max-w-[110px]">${escapeHtml(duplicatedTask.assignee || 'Studio Admin')}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="event.stopPropagation(); window.coraHandleTaskContextMenu(event, this.closest('.cora-task-card'))" title="Task Options (Right-Click)" class="w-5 h-5 rounded hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-colors border-0 bg-transparent cursor-pointer">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(newCard);
            }
        }

        window.coraSyncColumnPlaceholders();
        window.coraApplyTaskFilters();
        window.coraPersistTaskObject(duplicatedTask, 'Task duplicated: ' + copyTitle);
    } else if (action === 'copy') {
        const summary = `Task: ${task.title} | Client: ${task.client_name || task.client || 'Client'} | Stage: ${task.status} | Priority: ${task.priority} | Due: ${task.due_date} | Assignee: ${task.assignee || 'Studio Admin'}`;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(summary).then(function() {
                if (window.coraShowToast) window.coraShowToast('Task details copied to clipboard', 'success');
            }).catch(function() {
                if (window.coraShowToast) window.coraShowToast('Task details copied', 'success');
            });
        } else {
            if (window.coraShowToast) window.coraShowToast('Task details copied: ' + task.title, 'success');
        }
    } else if (action === 'add_subtask') {
        window.openTaskDrawer(task.id);
        window.coraSetTaskDrawerTab('checklist');
        setTimeout(() => {
            const input = document.getElementById('drawer-new-subtask-input');
            if (input) input.focus();
        }, 250);
    } else if (action === 'attach_link') {
        window.openTaskDrawer(task.id);
        window.coraSetTaskDrawerTab('assets');
        setTimeout(() => {
            const input = document.getElementById('drawer-new-link-url');
            if (input) input.focus();
        }, 250);
    } else if (action === 'delete') {
        const taskId = task.id;
        if (cardEl) cardEl.remove();

        if (Array.isArray(window.coraTasksData)) {
            window.coraTasksData = window.coraTasksData.filter(t => String(t.id) !== String(taskId));
        }

        window.coraSyncColumnPlaceholders();
        window.coraApplyTaskFilters();

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
    }
};

// Global Listeners for Task Command Menu Dismissal & Delegation
document.addEventListener('click', function(e) {
    const menu = document.getElementById('cora-task-command-menu');
    if (menu && !menu.classList.contains('hidden')) {
        if (!menu.contains(e.target)) {
            window.coraCloseTaskCommandMenu();
        }
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.coraCloseTaskCommandMenu();
    }
});

window.addEventListener('scroll', function() {
    window.coraCloseTaskCommandMenu();
}, { passive: true });

document.addEventListener('contextmenu', function(e) {
    const card = e.target.closest('.cora-task-card');
    if (card) {
        e.preventDefault();
        window.coraHandleTaskContextMenu(e, card);
    }
});

// =========================================================================
// 18. SHOPIFY-INSPIRED MAIN-AREA DOCKED VIEW PANEL SYNCHRONIZER (RULE 15 SOP)
// =========================================================================

// DYNAMIC BOUNDS SYNCHRONIZER (BOUND TO AVAILABLE MAIN WORKSPACE AREA)
window.coraSyncTaskDrawerBounds = function() {
    var drawer = document.getElementById('cora-task-drawer');
    var createDrawer = document.getElementById('cora-create-task-drawer');
    var sidebarEl = document.querySelector('.cora-sidebar');
    var isMobile = window.innerWidth < 1024;
    var isSidebarCollapsed = sidebarEl && sidebarEl.classList.contains('collapsed-sidebar');

    [drawer, createDrawer].forEach(function(d) {
        if (!d) return;

        // Reflect collapsed state class on drawer element directly
        if (isSidebarCollapsed) {
            d.classList.add('sidebar-collapsed');
        } else {
            d.classList.remove('sidebar-collapsed');
        }

        // If drawer is currently maximized (fullscreen ⤢), keep full viewport bounds
        if (d.classList.contains('maximized')) {
            d.style.left = '0px';
            d.style.top = '52px';
            d.style.width = '100vw';
            d.style.maxWidth = '100vw';
            d.style.height = (window.innerHeight - 52) + 'px';
            d.style.maxHeight = (window.innerHeight - 52) + 'px';
            return;
        }

        // Calculate available main canvas bounds directly against sidebar boundary
        var topbarEl = document.getElementById('cora-global-topbar');
        var actualTop = topbarEl ? Math.round(topbarEl.getBoundingClientRect().bottom) : 52;
        if (actualTop < 0 || isNaN(actualTop)) actualTop = 52;

        var targetLeft = 0;
        if (!isMobile && sidebarEl) {
            var sRect = sidebarEl.getBoundingClientRect();
            targetLeft = Math.round(sRect.right);
            if (targetLeft <= 0 || isNaN(targetLeft)) {
                targetLeft = isSidebarCollapsed ? 64 : 256;
            }
        }
        var targetWidth = window.innerWidth - targetLeft;
        var targetHeight = window.innerHeight - actualTop;

        document.documentElement.style.setProperty('--cora-main-left', targetLeft + 'px');
        document.documentElement.style.setProperty('--cora-main-top', actualTop + 'px');
        document.documentElement.style.setProperty('--cora-main-width', targetWidth + 'px');
        document.documentElement.style.setProperty('--cora-main-height', targetHeight + 'px');

        d.style.left = targetLeft + 'px';
        d.style.top = actualTop + 'px';
        d.style.width = targetWidth + 'px';
        d.style.maxWidth = targetWidth + 'px';
        d.style.height = targetHeight + 'px';
        d.style.maxHeight = targetHeight + 'px';
    });
};

// Continuously sync bounds over a duration (e.g. during a 250ms CSS animation)
window.coraAnimateTaskDrawerBounds = function(durationMs) {
    var start = Date.now();
    var duration = durationMs || 280;
    function frame() {
        window.coraSyncTaskDrawerBounds();
        if (Date.now() - start < duration) {
            requestAnimationFrame(frame);
        } else {
            window.coraSyncTaskDrawerBounds();
        }
    }
    requestAnimationFrame(frame);
};

// TOGGLE EXPAND / MAXIMIZE CANVAS (SHOPIFY EXPAND ⤢ MODE)
window.coraToggleTaskDrawerMaximize = function() {
    var drawer = document.getElementById('cora-task-drawer');
    var maxBtn = document.getElementById('cora-task-maximize-btn');
    if (!drawer) return;

    var isMax = drawer.classList.toggle('maximized');
    if (isMax) {
        drawer.style.left = '0px';
        drawer.style.width = '100vw';
        drawer.style.maxWidth = '100vw';
        if (maxBtn) {
            maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14h6v6"></path><path d="M20 10h-6V4"></path><path d="M14 10l7-7"></path><path d="M3 21l7-7"></path></svg>';
            maxBtn.title = "Restore Docked Canvas";
        }
    } else {
        if (maxBtn) {
            maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>';
            maxBtn.title = "Expand / Fullscreen Canvas";
        }
        window.coraSyncTaskDrawerBounds();
    }
};

window.coraToggleCreateTaskDrawerMaximize = function() {
    var drawer = document.getElementById('cora-create-task-drawer');
    var maxBtn = document.getElementById('cora-create-task-maximize-btn');
    if (!drawer) return;

    var isMax = drawer.classList.toggle('maximized');
    if (isMax) {
        drawer.style.left = '0px';
        drawer.style.width = '100vw';
        drawer.style.maxWidth = '100vw';
        if (maxBtn) {
            maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14h6v6"></path><path d="M20 10h-6V4"></path><path d="M14 10l7-7"></path><path d="M3 21l7-7"></path></svg>';
            maxBtn.title = "Restore Docked Canvas";
        }
    } else {
        if (maxBtn) {
            maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>';
            maxBtn.title = "Expand / Fullscreen Canvas";
        }
        window.coraSyncTaskDrawerBounds();
    }
};

// Window & Sidebar Event Listeners for Dynamic Main-Area Docking
window.addEventListener('cora:sidebar-toggle', function() {
    window.coraAnimateTaskDrawerBounds(300);
});

// Observe sidebar changes (expand/collapse) to sync available space in real time
if (window.MutationObserver) {
    var coraTaskSidebarObserver = new MutationObserver(function() {
        window.coraAnimateTaskDrawerBounds(250);
    });
    var sidebarEl = document.querySelector('.cora-sidebar');
    if (sidebarEl) {
        coraTaskSidebarObserver.observe(sidebarEl, { attributes: true, attributeFilter: ['class', 'style'] });
        sidebarEl.addEventListener('transitionend', function() {
            window.coraSyncTaskDrawerBounds();
        });
    }
    coraTaskSidebarObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });
}

// Global ESC Key Listener to Dismiss Docked Drawers per Rule 15 SOP
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
        var createDrawer = document.getElementById('cora-create-task-drawer');
        if (createDrawer && createDrawer.classList.contains('open')) {
            window.closeCreateTaskDrawer();
            return;
        }
        var taskDrawer = document.getElementById('cora-task-drawer');
        if (taskDrawer && taskDrawer.classList.contains('open')) {
            window.closeTaskDrawer();
            return;
        }
    }
});

// Auto-hydrate initial filter state on load & ensure mobile filter sheet is cleanly portaled and hidden
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

    const sheet = document.getElementById('cora-task-mobile-filter-sheet');
    const backdrop = document.getElementById('cora-task-mobile-filter-backdrop');
    if (sheet) {
        sheet.style.display = 'none';
        sheet.classList.add('hidden');
        if (sheet.parentElement !== document.body) {
            if (backdrop) document.body.appendChild(backdrop);
            document.body.appendChild(sheet);
        }
    }
    if (backdrop) {
        backdrop.style.display = 'none';
        backdrop.classList.add('hidden');
    }
});

window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
        window.coraCloseMobileFiltersSheet();
    }
});
</script>
