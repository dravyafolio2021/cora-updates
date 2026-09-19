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
            'progress'      => 60,
            'due_date'      => date( 'Y-m-d' ), // Today
            'notes'         => 'Apply Davinci Resolve cinema LUTs and export final 4K ProRes masters for client review.',
            'subtasks'      => array(
                array('id' => 'st-1', 'title' => 'Import and verify 4K RAW cinema footage', 'completed' => true),
                array('id' => 'st-2', 'title' => 'Apply Davinci cinema LUTs & highlight roll-off', 'completed' => true),
                array('id' => 'st-3', 'title' => 'Audio leveling and master sound mix', 'completed' => true),
                array('id' => 'st-4', 'title' => 'Export ProRes 422 HQ master cuts', 'completed' => false),
                array('id' => 'st-5', 'title' => 'Upload watermarked proxy to client gallery', 'completed' => false),
            ),
            'comments'      => array(
                array('id' => 'c-1', 'author' => 'Studio Admin', 'initials' => 'SA', 'time' => '2 hours ago', 'text' => 'LUT adjustments applied. Client requested warmer tone on highlights.'),
                array('id' => 'c-2', 'author' => 'Rohan Verma', 'initials' => 'RV', 'time' => '35 mins ago', 'text' => 'ProRes master export in progress. Ready for review shortly.'),
            ),
            'links'         => array(
                array('title' => 'RAW 4K Footage Drive', 'url' => 'https://drive.google.com'),
                array('title' => 'Frame.io Review Workspace', 'url' => 'https://frame.io'),
            ),
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
            'due_date'      => date( 'Y-m-d', strtotime( '+1 day' ) ), // Tomorrow
            'notes'         => 'Uploaded agreement draft to portal. Awaiting client signature on section 4.2.',
            'subtasks'      => array(
                array('id' => 'st-1', 'title' => 'Draft commercial terms & usage rights clause', 'completed' => true),
                array('id' => 'st-2', 'title' => 'GST tax calculation and billing schedule check', 'completed' => true),
                array('id' => 'st-3', 'title' => 'Send E-Sign request packet via portal', 'completed' => true),
                array('id' => 'st-4', 'title' => 'Client legal review on section 4.2', 'completed' => true),
                array('id' => 'st-5', 'title' => 'Countersign and archive executed PDF in vault', 'completed' => false),
            ),
            'comments'      => array(
                array('id' => 'c-1', 'author' => 'Studio Admin', 'initials' => 'SA', 'time' => '1 day ago', 'text' => 'Sent e-sign notification to Kavya Patel.'),
            ),
            'links'         => array(
                array('title' => 'Client Vault Agreement PDF', 'url' => 'https://cora.local/workspace/docs'),
            ),
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
            'progress'      => 25,
            'due_date'      => date( 'Y-m-d', strtotime( '+3 days' ) ), // This week
            'notes'         => 'Calibrate 360 spin turntable speed and adjust dual softbox overhead lighting angles.',
            'subtasks'      => array(
                array('id' => 'st-1', 'title' => 'Clean product sample surfaces and inspect glare', 'completed' => true),
                array('id' => 'st-2', 'title' => 'Setup motorized turntable step angles (36 frames/rev)', 'completed' => false),
                array('id' => 'st-3', 'title' => 'Position Godox overhead softboxes with grid diffusers', 'completed' => false),
                array('id' => 'st-4', 'title' => 'Tether test captures into Capture One Studio', 'completed' => false),
            ),
            'comments'      => array(
                array('id' => 'c-1', 'author' => 'Rohan Verma', 'initials' => 'RV', 'time' => '3 hours ago', 'text' => 'Turntable speed checked, gearing lubricated.'),
            ),
            'links'         => array(
                array('title' => 'Product SKU Shotlist Sheet', 'url' => 'https://docs.google.com'),
            ),
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
            'due_date'      => date( 'Y-m-d', strtotime( '-2 days' ) ), // Completed
            'notes'         => 'All 12 executive portraits approved and delivered via client portal gallery.',
            'subtasks'      => array(
                array('id' => 'st-1', 'title' => 'Frequency separation skin retouching', 'completed' => true),
                array('id' => 'st-2', 'title' => 'Eye & teeth clean enhancement', 'completed' => true),
                array('id' => 'st-3', 'title' => 'Background seamless gradient clean', 'completed' => true),
                array('id' => 'st-4', 'title' => 'Export high-res TIFF & web-optimized JPEG', 'completed' => true),
                array('id' => 'st-5', 'title' => 'Deliver proofing gallery and notify client', 'completed' => true),
            ),
            'comments'      => array(
                array('id' => 'c-1', 'author' => 'Studio Admin', 'initials' => 'SA', 'time' => '2 days ago', 'text' => 'Final delivery cleared. Client left a 5-star review!'),
            ),
            'links'         => array(
                array('title' => 'Delivered Gallery Portal', 'url' => 'https://cora.local/workspace/portals'),
            ),
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
            'progress'      => 20,
            'due_date'      => date( 'Y-m-d', strtotime( '+7 days' ) ), // This month
            'notes'         => 'File local municipal DGCA drone airspace permit for coastal high-rise exterior captures.',
            'subtasks'      => array(
                array('id' => 'st-1', 'title' => 'Coordinate GPS coordinates & flight path map', 'completed' => true),
                array('id' => 'st-2', 'title' => 'Submit DigitalSky drone flight permission application', 'completed' => false),
                array('id' => 'st-3', 'title' => 'Check weather forecast & golden hour sunset timing', 'completed' => false),
                array('id' => 'st-4', 'title' => 'Secure rooftop access clearance with building HOA', 'completed' => false),
                array('id' => 'st-5', 'title' => 'Prepare DJI Inspire battery charging station', 'completed' => false),
            ),
            'comments'      => array(
                array('id' => 'c-1', 'author' => 'Rohan Verma', 'initials' => 'RV', 'time' => '4 hours ago', 'text' => 'Submitted DigitalSky permit. Expecting approval in 48h.'),
            ),
            'links'         => array(
                array('title' => 'Flight Path Map PDF', 'url' => 'https://drive.google.com'),
            ),
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

/* Responsive Drawers & Bottom Sheets (Rule 12 SOP) */
:root {
    --cora-task-drawer-width: 480px;
}
.cora-task-drawer {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    max-width: var(--cora-task-drawer-width, 480px);
    background: #ffffff;
    border-left: 1px solid #e4e4e7;
    box-shadow: -8px 0 35px rgba(0, 0, 0, 0.08);
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
        border-left: none;
        border-top: 1px solid #e4e4e7;
        box-shadow: 0 -8px 35px rgba(0, 0, 0, 0.1);
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
    aside#cora-task-drawer,
    aside.cora-task-drawer,
    #cora-task-drawer {
        width: var(--cora-task-drawer-width, 480px) !important;
        max-width: 90vw !important;
        min-width: 380px !important;
        overflow: visible !important;
    }
    aside#cora-task-drawer.collapsed,
    aside#cora-task-drawer:not(.open) {
        transform: translateX(100%) !important;
    }
    aside#cora-task-drawer.open:not(.collapsed) {
        transform: translateX(0) !important;
        visibility: visible !important;
        display: flex !important;
        pointer-events: auto !important;
    }
}

/* Left-Edge Resizer Drag Handle */
.cora-drawer-resizer {
    position: absolute;
    left: -7px;
    top: 0;
    bottom: 0;
    width: 14px;
    cursor: col-resize;
    cursor: ew-resize;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    touch-action: none;
    user-select: none;
    pointer-events: auto !important;
}
.cora-drawer-resizer::after {
    content: '';
    width: 4px;
    height: 40px;
    background: #d4d4d8;
    border-radius: 9999px;
    transition: background-color 0.15s, height 0.15s, width 0.15s;
}
.cora-drawer-resizer:hover::after,
.cora-drawer-resizer.dragging::after {
    background: #18181b;
    height: 60px;
    width: 5px;
}
#cora-task-drawer-backdrop {
    background: transparent !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
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
    opacity: 0 !important;
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
                                        <button type="button" onclick="window.openTaskDrawer('<?php echo esc_js( $task['id'] ); ?>')" title="Task Details" class="w-5 h-5 rounded hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-colors border-0 bg-transparent cursor-pointer">
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
     RESPONSIVE TASK DETAILS & WORK MANAGEMENT DRAWER
     ═══════════════════════════════════════════════════════════════════ -->
<div id="cora-task-drawer-backdrop" onclick="window.closeTaskDrawer(); window.closeCreateTaskDrawer();" class="fixed inset-0 bg-transparent z-[9990] opacity-0 pointer-events-none"></div>

<aside id="cora-task-drawer" class="cora-task-drawer collapsed flex flex-col pointer-events-none">
    <!-- Desktop Left-Edge Drag-to-Resize Handle -->
    <div id="cora-task-drawer-resizer" class="hidden sm:flex cora-drawer-resizer" title="Drag to resize drawer"></div>

    <!-- Mobile Drag Handle -->
    <div class="sm:hidden w-10 h-1 bg-zinc-300 rounded-full mx-auto mt-2.5 mb-1 shrink-0"></div>

    <!-- Drawer Header Toolbar -->
    <div class="h-14 sm:h-16 px-4 sm:px-6 border-b border-zinc-200/90 flex items-center justify-between shrink-0 bg-white gap-2">
        <div class="flex items-center gap-2 min-w-0">
            <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-600 font-mono font-bold text-[10px]" id="drawer-task-id">#TASK-101</span>
            
            <!-- Quick Status Selector -->
            <select id="drawer-task-status-select" onchange="window.coraDrawerStatusChange(this.value)" class="h-7 px-2 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-800 outline-none cursor-pointer hover:bg-zinc-100 transition-colors">
                <option value="todo">⚪ To Do / Backlog</option>
                <option value="in_progress">🟡 In Execution</option>
                <option value="review">🟣 Review & QA</option>
                <option value="done">🟢 Completed</option>
            </select>

            <!-- Quick Priority Selector -->
            <select id="drawer-task-priority-select" onchange="window.coraDrawerPriorityChange(this.value)" class="h-7 px-2 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-800 outline-none cursor-pointer hover:bg-zinc-100 transition-colors">
                <option value="urgent">🔥 Urgent</option>
                <option value="high">High Priority</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
        </div>

        <button id="btn-close-task-drawer" onclick="window.closeTaskDrawer()" class="w-8 h-8 rounded-lg hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 flex items-center justify-center cursor-pointer transition-colors border-0 bg-transparent shrink-0">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Drawer Body -->
    <div class="flex-1 overflow-y-auto flex flex-col text-xs">
        
        <!-- Top Fixed Details Block (Title & Properties Matrix) -->
        <div class="p-4 sm:p-6 space-y-4 border-b border-zinc-100 bg-white shrink-0">
            <!-- Editable Title & Category -->
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span id="drawer-task-category-badge" class="px-2 py-0.5 rounded bg-zinc-100 text-zinc-600 font-mono font-bold text-[9px] uppercase">POST_PRODUCTION</span>
                    <span class="text-[10px] text-zinc-400 font-medium">Click title to edit</span>
                </div>
                <input type="text" id="drawer-task-title-input" onblur="window.coraSaveTaskField('title', this.value)" class="w-full text-base sm:text-lg font-bold text-zinc-950 bg-transparent border-b border-transparent hover:border-zinc-300 focus:border-zinc-900 focus:bg-white px-1 py-1 rounded outline-none transition-all" placeholder="Task title...">
            </div>

            <!-- Notion/Linear Style Properties Matrix -->
            <div class="bg-zinc-50/80 border border-zinc-200/70 rounded-2xl p-3.5 space-y-2.5">
                <div class="grid grid-cols-2 gap-3">
                    <!-- Assignee -->
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Assignee</label>
                        <select id="drawer-task-assignee-select" onchange="window.coraSaveTaskField('assignee', this.value)" class="w-full h-8 px-2 bg-white border border-zinc-200 rounded-lg text-xs font-medium text-zinc-800 outline-none cursor-pointer">
                            <option value="Studio Admin">Studio Admin</option>
                            <option value="Rohan Verma">Rohan Verma</option>
                            <option value="Kavya Patel">Kavya Patel</option>
                            <option value="Aarav Mehta">Aarav Mehta</option>
                        </select>
                    </div>

                    <!-- Client -->
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Client</label>
                        <input type="text" id="drawer-task-client-input" onblur="window.coraSaveTaskField('client', this.value)" class="w-full h-8 px-2 bg-white border border-zinc-200 rounded-lg text-xs font-medium text-zinc-800 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-zinc-200/50">
                    <!-- Due Date -->
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Due Date</label>
                        <input type="date" id="drawer-task-due-date-input" onchange="window.coraSaveTaskField('due_date', this.value)" class="w-full h-8 px-2 bg-white border border-zinc-200 rounded-lg text-xs font-medium text-zinc-800 outline-none cursor-pointer">
                    </div>

                    <!-- Deliverable Category -->
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Category</label>
                        <select id="drawer-task-category-select" onchange="window.coraSaveTaskField('category', this.value)" class="w-full h-8 px-2 bg-white border border-zinc-200 rounded-lg text-xs font-medium text-zinc-800 outline-none cursor-pointer">
                            <?php foreach ( ( $curr_cfg['categories'] ?? array('PHOTOSHOOT', 'POST_PRODUCTION', 'RETOUCHING', 'COLOR_GRADING', 'PORTAL_UPLOAD', 'ADMIN') ) as $cat ) : ?>
                                <option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Segmented 4-Tab Navigation Bar -->
        <div class="sticky top-0 z-10 bg-white/95 backdrop-blur border-b border-zinc-200/80 px-4 sm:px-6 flex items-center gap-1 overflow-x-auto no-scrollbar shrink-0">
            <button type="button" onclick="window.coraSetTaskDrawerTab('checklist')" id="task-tab-btn-checklist" class="task-drawer-tab-btn active px-3 py-2.5 text-xs font-semibold text-zinc-950 border-b-2 border-zinc-950 transition-all flex items-center gap-1.5 cursor-pointer bg-transparent border-0 border-b-2 whitespace-nowrap">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <span>Checklist</span>
                <span id="tab-badge-checklist-count" class="px-1.5 py-0.2 rounded-full bg-zinc-100 text-zinc-700 text-[10px] font-mono font-bold">0</span>
            </button>
            <button type="button" onclick="window.coraSetTaskDrawerTab('scope')" id="task-tab-btn-scope" class="task-drawer-tab-btn px-3 py-2.5 text-xs font-medium text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all flex items-center gap-1.5 cursor-pointer bg-transparent border-0 border-b-2 whitespace-nowrap">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                <span>Scope & Specs</span>
            </button>
            <button type="button" onclick="window.coraSetTaskDrawerTab('assets')" id="task-tab-btn-assets" class="task-drawer-tab-btn px-3 py-2.5 text-xs font-medium text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all flex items-center gap-1.5 cursor-pointer bg-transparent border-0 border-b-2 whitespace-nowrap">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                <span>Assets & Links</span>
                <span id="tab-badge-assets-count" class="px-1.5 py-0.2 rounded-full bg-zinc-100 text-zinc-700 text-[10px] font-mono font-bold">0</span>
            </button>
            <button type="button" onclick="window.coraSetTaskDrawerTab('activity')" id="task-tab-btn-activity" class="task-drawer-tab-btn px-3 py-2.5 text-xs font-medium text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all flex items-center gap-1.5 cursor-pointer bg-transparent border-0 border-b-2 whitespace-nowrap">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span>Activity & Notes</span>
                <span id="tab-badge-activity-count" class="px-1.5 py-0.2 rounded-full bg-zinc-100 text-zinc-700 text-[10px] font-mono font-bold">0</span>
            </button>
        </div>

        <!-- Tab Panels Container -->
        <div class="p-4 sm:p-6 flex-1 flex flex-col">
            
            <!-- ── TAB 1: CHECKLIST ── -->
            <div id="task-tab-panel-checklist" class="task-drawer-tab-panel space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                        <span class="font-bold text-zinc-900 text-xs">Subtasks & Execution Checklist</span>
                    </div>
                    <span id="drawer-subtask-progress-label" class="font-mono text-[11px] font-bold text-zinc-700">3 of 5 (60%)</span>
                </div>

                <!-- Animated Progress Bar -->
                <div class="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                    <div id="drawer-subtask-progress-bar" class="h-full bg-zinc-950 rounded-full transition-all duration-300" style="width: 60%;"></div>
                </div>

                <!-- Subtask Items List -->
                <div id="drawer-subtasks-list" class="space-y-2">
                    <!-- Dynamically rendered checkable subtask items -->
                </div>

                <!-- Add Subtask Input Form -->
                <form onsubmit="window.coraAddSubtask(event)" class="flex items-center gap-2 pt-2">
                    <input type="text" id="drawer-new-subtask-input" placeholder="+ Add a subtask checklist item (press Enter)..." class="flex-1 h-9 px-3 bg-zinc-50 hover:bg-white focus:bg-white border border-dashed border-zinc-300 focus:border-zinc-500 rounded-xl text-xs outline-none transition-all">
                    <button type="submit" class="px-3 h-9 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-xl text-xs cursor-pointer transition-colors border-0 shadow-2xs">Add Item</button>
                </form>
            </div>

            <!-- ── TAB 2: SCOPE & SPECS ── -->
            <div id="task-tab-panel-scope" class="task-drawer-tab-panel hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span class="font-bold text-zinc-900 text-xs">Technical Scope & Requirements</span>
                    </div>
                    <span class="text-[10px] text-zinc-400 font-mono">Auto-saved</span>
                </div>

                <div class="space-y-2">
                    <label class="block text-[11px] font-semibold text-zinc-600">Work Instructions & Deliverable Specifications</label>
                    <textarea id="drawer-task-notes-input" onblur="window.coraSaveTaskField('notes', this.value)" rows="8" placeholder="Provide checklist requirements, format specifications (4K ProRes, JPG, sRGB), camera angles, color grading LUT references, and deliverable notes..." class="w-full p-3.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 outline-none focus:border-zinc-400 focus:bg-white transition-all resize-none leading-relaxed"></textarea>
                </div>

                <div class="p-3 bg-zinc-50/80 rounded-xl border border-zinc-200/60 text-[11px] text-zinc-500 space-y-1">
                    <span class="font-bold text-zinc-700 block">Pro Tip for Deliverables:</span>
                    <p class="leading-relaxed">Keep instructions structured with clear deliverable resolutions, color profiles, and client feedback notes.</p>
                </div>
            </div>

            <!-- ── TAB 3: ASSETS & LINKS ── -->
            <div id="task-tab-panel-assets" class="task-drawer-tab-panel hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        <span class="font-bold text-zinc-900 text-xs">Deliverable Asset Links & Galleries</span>
                    </div>
                </div>

                <!-- Asset Links List -->
                <div id="drawer-asset-links-list" class="space-y-2">
                    <!-- Dynamically rendered asset links -->
                </div>

                <!-- Inline Add Link Form (No browser prompt) -->
                <div class="p-3.5 bg-zinc-50 rounded-2xl border border-zinc-200 space-y-2.5">
                    <span class="font-bold text-zinc-800 text-[11px] block">+ Attach Deliverable Asset URL</span>
                    <div class="space-y-2">
                        <input type="text" id="drawer-new-link-title" placeholder="Link Title (e.g. Master Drive, Frame.io Review, RAW Proofs)" class="w-full h-8 px-3 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-400">
                        <input type="url" id="drawer-new-link-url" placeholder="URL (e.g. https://drive.google.com/...)" class="w-full h-8 px-3 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-400">
                        <button type="button" onclick="window.coraAddAssetLinkSubmit()" class="w-full h-8 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer border-0 shadow-2xs">
                            Add Asset Link
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── TAB 4: ACTIVITY & NOTES ── -->
            <div id="task-tab-panel-activity" class="task-drawer-tab-panel hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <span class="font-bold text-zinc-900 text-xs">Team Activity & Collaboration Feed</span>
                    </div>
                    <span id="drawer-comments-count" class="text-[10px] text-zinc-400 font-mono">0 updates</span>
                </div>

                <!-- Comments Feed -->
                <div id="drawer-comments-feed" class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    <!-- Dynamically rendered comments -->
                </div>

                <!-- Post New Comment Input Form -->
                <form onsubmit="window.coraAddComment(event)" class="space-y-2 pt-2 border-t border-zinc-100">
                    <textarea id="drawer-new-comment-input" rows="2" placeholder="Write an update, note, or collaboration comment..." class="w-full p-2.5 bg-zinc-50 focus:bg-white border border-zinc-200 focus:border-zinc-400 rounded-xl text-xs outline-none transition-all resize-none"></textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="px-3.5 h-8 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-xl text-xs cursor-pointer transition-colors border-0 shadow-2xs">
                            Post Update
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Drawer Footer Actions -->
    <div class="p-4 sm:p-5 border-t border-zinc-200/90 bg-zinc-50/90 flex items-center justify-between gap-2 shrink-0">
        <button type="button" onclick="window.coraDeleteActiveTask()" class="px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-xl transition-colors border border-transparent hover:border-red-200 cursor-pointer">
            Delete Task
        </button>
        <div class="flex items-center gap-2">
            <button type="button" id="drawer-advance-stage-btn" onclick="window.coraAdvanceTaskStage()" class="px-3.5 py-2 text-xs font-bold text-zinc-800 bg-white hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-all cursor-pointer shadow-2xs">
                Advance Stage →
            </button>
            <button type="button" onclick="window.closeTaskDrawer()" class="px-4 py-2 text-xs font-bold text-white bg-zinc-950 hover:bg-zinc-800 rounded-xl transition-all cursor-pointer shadow-2xs">
                Done
            </button>
        </div>
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

    // Open Drawer
    const drawer = document.getElementById('cora-task-drawer');
    drawer.classList.remove('pointer-events-none', 'collapsed');
    drawer.classList.add('open');

    const backdrop = document.getElementById('cora-task-drawer-backdrop');
    backdrop.classList.remove('pointer-events-none');
    backdrop.classList.add('open');

    if (window.coraInitDrawerResizer) {
        window.coraInitDrawerResizer();
    }
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

// 11. Tab Switching Engine for Task Details Drawer
window.coraActiveTaskDrawerTab = 'checklist';
window.coraSetTaskDrawerTab = function(tabKey) {
    window.coraActiveTaskDrawerTab = tabKey;
    
    document.querySelectorAll('.task-drawer-tab-btn').forEach(btn => {
        btn.classList.remove('active', 'text-zinc-950', 'border-zinc-950', 'font-semibold');
        btn.classList.add('text-zinc-500', 'border-transparent', 'font-medium');
    });
    
    const activeBtn = document.getElementById('task-tab-btn-' + tabKey);
    if (activeBtn) {
        activeBtn.classList.add('active', 'text-zinc-950', 'border-zinc-950', 'font-semibold');
        activeBtn.classList.remove('text-zinc-500', 'border-transparent', 'font-medium');
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
    else if (task.status === 'review') btn.textContent = 'Mark Completed ✓';
    else btn.textContent = 'Completed ✓';
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

// 15. Draggable Drawer Resizing Engine
(function() {
    let isResizing = false;
    let startX = 0;
    let startWidth = 480;

    // Load persisted width
    try {
        const savedWidth = localStorage.getItem('cora_task_drawer_width');
        if (savedWidth && window.innerWidth >= 640) {
            const parsed = parseInt(savedWidth, 10);
            if (parsed >= 380 && parsed <= Math.min(1200, window.innerWidth * 0.92)) {
                document.documentElement.style.setProperty('--cora-task-drawer-width', parsed + 'px');
            }
        }
    } catch(e) {}

    window.coraInitDrawerResizer = function() {
        const resizer = document.getElementById('cora-task-drawer-resizer');
        const drawer = document.getElementById('cora-task-drawer');
        if (!resizer || !drawer) return;
        if (resizer.dataset.initialized === 'true') return;
        resizer.dataset.initialized = 'true';

        const onStart = function(e) {
            if (window.innerWidth < 640) return;
            if (isResizing) return;
            isResizing = true;
            if (e.preventDefault) e.preventDefault();

            startX = (e.touches && e.touches[0]) ? e.touches[0].clientX : e.clientX;
            startWidth = drawer.getBoundingClientRect().width;
            
            resizer.classList.add('dragging');
            document.body.classList.add('select-none');
            document.body.style.cursor = 'col-resize';
            drawer.style.transition = 'none';

            const onMove = function(moveEvent) {
                if (!isResizing) return;
                const currentX = (moveEvent.touches && moveEvent.touches[0]) ? moveEvent.touches[0].clientX : moveEvent.clientX;
                const deltaX = startX - currentX; // Dragging left increases width
                let newWidth = startWidth + deltaX;
                const minWidth = 380;
                const maxWidth = Math.min(1200, window.innerWidth * 0.92);
                if (newWidth < minWidth) newWidth = minWidth;
                if (newWidth > maxWidth) newWidth = maxWidth;

                document.documentElement.style.setProperty('--cora-task-drawer-width', newWidth + 'px');
                drawer.style.setProperty('--cora-task-drawer-width', newWidth + 'px');
                drawer.style.width = newWidth + 'px';
                drawer.style.maxWidth = newWidth + 'px';
            };

            const onEnd = function() {
                if (!isResizing) return;
                isResizing = false;
                resizer.classList.remove('dragging');
                document.body.classList.remove('select-none');
                document.body.style.cursor = '';
                drawer.style.transition = '';

                const finalWidth = drawer.getBoundingClientRect().width;
                try {
                    localStorage.setItem('cora_task_drawer_width', Math.round(finalWidth));
                } catch(e) {}

                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onEnd);
                document.removeEventListener('pointermove', onMove);
                document.removeEventListener('pointerup', onEnd);
                document.removeEventListener('touchmove', onMove);
                document.removeEventListener('touchend', onEnd);
            };

            document.addEventListener('mousemove', onMove, { passive: false });
            document.addEventListener('mouseup', onEnd);
            document.addEventListener('pointermove', onMove, { passive: false });
            document.addEventListener('pointerup', onEnd);
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onEnd);
        };

        resizer.addEventListener('pointerdown', onStart);
        resizer.addEventListener('mousedown', onStart);
        resizer.addEventListener('touchstart', onStart, { passive: false });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.coraInitDrawerResizer);
    } else {
        setTimeout(window.coraInitDrawerResizer, 50);
    }
})();

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
