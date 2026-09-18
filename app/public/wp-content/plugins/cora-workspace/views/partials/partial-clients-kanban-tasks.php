<?php
/**
 * Cora Platform — Clients Management Suite: Kanban Tasks Pipeline
 *
 * Implements interactive drag-and-drop Kanban workflow for client deliverables,
 * production shoots, retouching sprints, and milestone reviews.
 * Adheres strictly to the Cora master atomic design system & Leads Kanban standard.
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Fetch / initialize client tasks data
$tasks_raw = get_option( 'cora_workspace_client_tasks', array() );
if ( empty( $tasks_raw ) || ! is_array( $tasks_raw ) ) {
    $tasks_raw = array(
        array(
            'id'            => 'task-101',
            'client_id'     => 1,
            'client_name'   => 'Rohan Verma',
            'title'         => 'Commercial Brand Campaign Shoot',
            'scale'         => 'Brand Video & 4K Stills',
            'city'          => 'Mumbai',
            'price'         => 125000,
            'format'        => 'PHOTOSHOOT',
            'status'        => 'in_progress',
            'score'         => 'hot',
            'priority'      => 'high',
            'assignee'      => 'Studio Admin',
            'assignee_init' => 'SA',
            'progress'      => 65,
            'phone'         => '+91 98201 45892',
            'email'         => 'rohan.verma@enterprise.com',
            'notes'         => 'Full day studio production on Main Stage. 3-camera lighting setup.',
            'checklist'     => '3/5 (60%)',
            'checklist_pct' => 60,
        ),
        array(
            'id'            => 'task-102',
            'client_id'     => 2,
            'client_name'   => 'Kavya Patel',
            'title'         => 'Architecture Portfolio & Virtual Tour',
            'scale'         => 'HDR Bracketed Interior Walkthrough',
            'city'          => 'South Mumbai',
            'price'         => 85000,
            'format'        => 'RETOUCH',
            'status'        => 'review',
            'score'         => 'warm',
            'priority'      => 'medium',
            'assignee'      => 'Studio Admin',
            'assignee_init' => 'SA',
            'progress'      => 85,
            'phone'         => '+91 97112 34567',
            'email'         => 'kavya.patel@designstudio.in',
            'notes'         => 'Draft gallery uploaded to client proofing portal. Awaiting color grading approvals.',
            'checklist'     => '4/5 (80%)',
            'checklist_pct' => 80,
        ),
        array(
            'id'            => 'task-103',
            'client_id'     => 3,
            'client_name'   => 'Aarav Mehta',
            'title'         => 'E-Commerce Product Catalogs & 360 Spins',
            'scale'         => '40 SKU Turntable Captures',
            'city'          => 'White Bay Studio',
            'price'         => 95000,
            'format'        => 'VIDEO',
            'status'        => 'todo',
            'score'         => 'cold',
            'priority'      => 'medium',
            'assignee'      => 'Rohan Verma',
            'assignee_init' => 'RV',
            'progress'      => 25,
            'phone'         => '+91 98334 78901',
            'email'         => 'aarav.mehta@lumina.co',
            'notes'         => 'Equipment check & turntable motorized rig calibration.',
            'checklist'     => '1/4 (25%)',
            'checklist_pct' => 25,
        ),
        array(
            'id'            => 'task-104',
            'client_id'     => 1,
            'client_name'   => 'Rohan Verma',
            'title'         => 'Executive Portrait & Team Stills',
            'scale'         => 'Corporate Headshots & Keynote',
            'city'          => 'BKC Mumbai',
            'price'         => 45000,
            'format'        => 'PHOTOSHOOT',
            'status'        => 'done',
            'score'         => 'won',
            'priority'      => 'low',
            'assignee'      => 'Studio Admin',
            'assignee_init' => 'SA',
            'progress'      => 100,
            'phone'         => '+91 98201 45892',
            'email'         => 'rohan.verma@enterprise.com',
            'notes'         => 'High-res deliverables exported and client sign-off completed.',
            'checklist'     => '5/5 (100%)',
            'checklist_pct' => 100,
        ),
        array(
            'id'            => 'task-105',
            'client_id'     => 2,
            'client_name'   => 'Kavya Patel',
            'title'         => 'Luxury Penthouse Cinematic Walkthrough',
            'scale'         => '4K Drone & Interior Steadicam',
            'city'          => 'Worli Sea Face',
            'price'         => 150000,
            'format'        => 'ARCHITECTURAL',
            'status'        => 'todo',
            'score'         => 'hot',
            'priority'      => 'urgent',
            'assignee'      => 'Rohan Verma',
            'assignee_init' => 'RV',
            'progress'      => 20,
            'phone'         => '+91 97112 34567',
            'email'         => 'kavya.patel@designstudio.in',
            'notes'         => 'Pre-production location scouting and drone airspace clearance.',
            'checklist'     => '1/5 (20%)',
            'checklist_pct' => 20,
        ),
    );
}

// 2. Stages configuration with pastel tint psychology
$task_stages = array(
    'todo' => array(
        'key'         => 'todo',
        'label'       => 'New Brief / To Do',
        'palette'     => 'emerald',
        'icon'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
    ),
    'in_progress' => array(
        'key'         => 'in_progress',
        'label'       => 'In Production',
        'palette'     => 'amber',
        'icon'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>',
    ),
    'review' => array(
        'key'         => 'review',
        'label'       => 'Review & Proofing',
        'palette'     => 'violet',
        'icon'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
    ),
    'done' => array(
        'key'         => 'done',
        'label'       => 'Completed & Delivered',
        'palette'     => 'blue',
        'icon'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>',
    ),
);

$color_tint_palette = array(
    'emerald' => array('bg' => '#f0fdf4', 'border' => '#bbf7d0', 'bg_dark' => 'rgba(6, 78, 59, 0.18)', 'border_dark' => 'rgba(16, 185, 129, 0.3)', 'icon_bg' => 'bg-emerald-600', 'sum_text' => 'text-emerald-700 font-black'),
    'amber'   => array('bg' => '#fffbeb', 'border' => '#fde68a', 'bg_dark' => 'rgba(120, 53, 15, 0.18)', 'border_dark' => 'rgba(245, 158, 11, 0.3)', 'icon_bg' => 'bg-amber-600', 'sum_text' => 'text-amber-700 font-black'),
    'violet'  => array('bg' => '#f5f3ff', 'border' => '#ddd6fe', 'bg_dark' => 'rgba(91, 33, 182, 0.18)', 'border_dark' => 'rgba(139, 92, 246, 0.3)', 'icon_bg' => 'bg-violet-600', 'sum_text' => 'text-violet-700 font-black'),
    'blue'    => array('bg' => '#eff6ff', 'border' => '#bfdbfe', 'bg_dark' => 'rgba(30, 58, 138, 0.18)', 'border_dark' => 'rgba(59, 130, 246, 0.3)', 'icon_bg' => 'bg-blue-600', 'sum_text' => 'text-blue-700 font-black'),
    'zinc'    => array('bg' => '#f8f8fa', 'border' => '#e4e4e7', 'bg_dark' => 'rgba(24, 24, 27, 0.4)', 'border_dark' => 'rgba(63, 63, 70, 0.4)', 'icon_bg' => 'bg-zinc-700', 'sum_text' => 'text-zinc-700 font-black'),
);

$total_tasks_count = count( $tasks_raw );
?>

<style>
/* Exact Leads Kanban Style Token Mirror */
#cora-task-kanban-board {
    display: flex !important;
    gap: 1rem !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    height: calc(100vh - 175px) !important;
    max-height: calc(100vh - 175px) !important;
    min-height: 480px !important;
    padding-bottom: 8px !important;
    align-items: stretch !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
#cora-task-kanban-board::-webkit-scrollbar {
    display: none !important;
    width: 0px !important;
    height: 0px !important;
}
.cora-task-kanban-column {
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    max-height: 100% !important;
    min-height: 0 !important;
    width: 300px !important;
    min-width: 300px !important;
    max-width: 300px !important;
    box-sizing: border-box !important;
    background-color: var(--col-bg, #f8f8fa) !important;
    border: 1px solid var(--col-border, #e4e4e7) !important;
    border-radius: 1.5rem !important;
    padding: 0.875rem !important;
    position: relative !important;
    transition: all 0.2s ease !important;
}
.cora-task-cards-container {
    flex: 1 1 0% !important;
    min-height: 0 !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding-right: 0px !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
.cora-task-cards-container::-webkit-scrollbar {
    display: none !important;
    width: 0px !important;
    height: 0px !important;
}
.cora-task-col-footer-add {
    flex-shrink: 0 !important;
    margin-top: auto !important;
}
</style>

<!-- ═══════════════════════════════════════════════════════════════════
     TAB PANEL: KANBAN TASKS (PIPELINE BOARD)
     ═══════════════════════════════════════════════════════════════════ -->
<div id="clients-tab-content-tasks" class="cora-clients-tab-panel flex flex-col gap-4">
    
    <!-- Top Filter & Search Toolbar (Identical to Leads Toolbar) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3">
        <div class="flex items-center gap-1.5 bg-zinc-100/80 p-1 rounded-xl border border-zinc-200/70 overflow-x-auto no-scrollbar">
            <button type="button" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-zinc-950 text-white shadow-2xs cursor-pointer border-0 flex items-center gap-1.5 whitespace-nowrap shrink-0">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>
                <span>Kanban Pipeline</span>
                <span id="cora-tasks-total-pill" class="ml-1 opacity-70 bg-white/20 px-1.5 py-0.2 rounded-full text-[10px]"><?php echo esc_html( $total_tasks_count ); ?></span>
            </button>
            <button type="button" onclick="window.coraFilterTaskPriority('all', this)" class="task-priority-filter-btn active px-3 py-1.5 rounded-lg text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-white/80 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-filter="all">
                All Priorities
            </button>
            <button type="button" onclick="window.coraFilterTaskPriority('urgent', this)" class="task-priority-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-white/80 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-filter="urgent">
                🔥 Urgent / Hot
            </button>
            <button type="button" onclick="window.coraFilterTaskPriority('high', this)" class="task-priority-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all text-zinc-600 hover:text-zinc-950 hover:bg-white/80 cursor-pointer bg-transparent border-0 whitespace-nowrap shrink-0" data-filter="high">
                High Priority
            </button>
        </div>

        <div class="flex items-center gap-2">
            <div class="relative w-full sm:w-72">
                <input id="tasks-search-input" type="text" placeholder="Search tasks by title, client, city..." class="w-full h-9 pl-8 pr-3 text-xs bg-white border border-zinc-200/90 rounded-xl text-zinc-900 placeholder:text-zinc-400 outline-none focus:border-zinc-400 shadow-2xs transition-all" oninput="window.coraFilterGlobalTasks(this.value)" />
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-zinc-400">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
            </div>
            <button type="button" onclick="window.openCreateTaskDrawer('todo')" class="h-9 px-3.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer border-0 shadow-2xs shrink-0">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>New Task</span>
            </button>
        </div>
    </div>

    <!-- Viewport-Docked Kanban Board -->
    <div class="flex gap-4 overflow-x-auto pb-4 select-none" id="cora-task-kanban-board">
        <?php foreach ( $task_stages as $stage_key => $stage ) : 
            $palette = $color_tint_palette[$stage['palette']] ?? $color_tint_palette['zinc'];
            $col_tasks = array_filter( $tasks_raw, function($t) use ($stage_key) {
                return ( $t['status'] ?? 'todo' ) === $stage_key;
            });
            $col_value = 0;
            foreach ( $col_tasks as $ct ) {
                $col_value += floatval( $ct['price'] ?? 0 );
            }

            $col_inline_style = sprintf(
                '--col-bg: %s; --col-border: %s;',
                $palette['bg'],
                $palette['border']
            );
        ?>
        <div class="cora-task-kanban-column shrink-0"
             style="<?php echo esc_attr( $col_inline_style ); ?>"
             data-status="<?php echo esc_attr( $stage_key ); ?>"
             ondragover="window.coraTaskDragOver(event, this)"
             ondrop="window.coraTaskDrop(event, this)">
            
            <!-- Column Header (Exact Leads Assembly) -->
            <div class="mb-3.5 flex flex-col gap-2 shrink-0 px-0.5 pt-0.5">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 <?php echo $palette['icon_bg']; ?> text-white shadow-2xs">
                            <?php echo $stage['icon']; ?>
                        </div>
                        <span class="text-[11px] font-black text-zinc-900 uppercase tracking-wider truncate">
                            <?php echo esc_html( $stage['label'] ); ?>
                        </span>
                        <span class="text-[10px] text-zinc-600 font-bold bg-white/80 px-2 py-0.5 rounded-full task-col-count shrink-0 border border-zinc-200/50">
                            <?php echo count($col_tasks); ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-1 shrink-0 relative">
                        <button type="button" class="cora-col-search-btn w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-900 transition-all flex items-center justify-center cursor-pointer shrink-0" title="Search in Column" onclick="window.coraToggleTaskColumnSearch(this)">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                        <button type="button" class="w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-900 transition-all flex items-center justify-center cursor-pointer shrink-0" title="Quick Add Task" onclick="window.openCreateTaskDrawer('<?php echo esc_attr($stage_key); ?>')">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                        <button type="button" class="cora-col-menu-trigger w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-900 transition-all flex items-center justify-center cursor-pointer shrink-0" title="Sort Column" onclick="window.coraToggleTaskColumnMenu(this, event)">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="19" r="2"></circle></svg>
                        </button>

                        <!-- Column Sort Context Popover Menu -->
                        <div class="cora-task-col-menu hidden absolute right-0 top-full mt-1.5 w-48 bg-white border border-zinc-200/90 rounded-xl shadow-xl z-50 p-1.5 space-y-1 font-sans text-xs">
                            <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-zinc-400">Sort Tasks</div>
                            <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 hover:bg-zinc-100 font-medium flex items-center justify-between cursor-pointer" onclick="window.coraSortTaskColumn(this, 'value-desc')">
                                <span>₹ High → Low</span>
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 hover:bg-zinc-100 font-medium flex items-center justify-between cursor-pointer" onclick="window.coraSortTaskColumn(this, 'value-asc')">
                                <span>₹ Low → High</span>
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            </button>
                            <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 hover:bg-zinc-100 font-medium flex items-center justify-between cursor-pointer" onclick="window.coraSortTaskColumn(this, 'urgent-first')">
                                <span>🔥 Urgent First</span>
                            </button>
                            <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 hover:bg-zinc-100 font-medium flex items-center justify-between cursor-pointer" onclick="window.coraSortTaskColumn(this, 'name-asc')">
                                <span>Client: A → Z</span>
                            </button>
                            <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 hover:bg-zinc-100 font-medium flex items-center justify-between cursor-pointer border-t border-zinc-100 pt-1.5" onclick="window.coraSortTaskColumn(this, 'default')">
                                <span class="text-zinc-400">Default Order</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- In-Column Real-Time Search Box -->
                <div class="cora-task-col-search-box hidden pt-1">
                    <div class="relative flex items-center">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none" class="absolute left-2.5 text-zinc-400 pointer-events-none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" class="w-full pl-7 pr-7 py-1 bg-white/80 border border-zinc-200/80 rounded-xl text-[11px] text-zinc-900 font-medium placeholder-zinc-400 outline-none" placeholder="Search this column..." oninput="window.coraFilterTaskColumnCards(this)">
                    </div>
                </div>

                <div class="flex items-center justify-between text-[10.5px] text-zinc-500 font-medium pt-1.5 border-t border-zinc-200/60">
                    <span>Pipeline Value</span>
                    <span class="<?php echo $palette['sum_text']; ?> task-col-sum">
                        ₹<?php echo number_format( $col_value ); ?>
                    </span>
                </div>
            </div>

            <!-- Cards Container -->
            <div class="cora-task-cards-container space-y-3 pb-2 pr-0">
                <?php if ( empty( $col_tasks ) ) : ?>
                    <div class="task-empty-placeholder flex flex-col items-center justify-center p-6 my-1 border border-dashed border-zinc-200/90 rounded-2xl bg-white/50 text-center select-none min-h-[200px]">
                        <div class="mb-2 flex items-center justify-center w-10 h-10 rounded-full bg-zinc-100 border border-zinc-200/50 text-zinc-400">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.5" fill="none"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <h5 class="text-xs font-bold text-zinc-800 leading-tight">No tasks in stage</h5>
                        <p class="text-[9.5px] text-zinc-400 leading-normal max-w-[170px] mt-0.5">Drag tasks here or click Add Task below to start.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ( $col_tasks as $t ) : 
                    $t_score = strtolower( $t['score'] ?? ( $t['priority'] === 'urgent' ? 'hot' : 'warm' ) );
                    $t_price = floatval( $t['price'] ?? 75000 );
                    $t_client = $t['client_name'] ?? 'Rohan Verma';
                    $t_title = $t['title'] ?? 'Production Shoot';
                    $t_scale = $t['scale'] ?? 'Standard Shoot';
                    $t_city = $t['city'] ?? 'Mumbai';
                    $t_format = $t['format'] ?? 'PHOTOSHOOT';
                    $t_assignee = $t['assignee'] ?? 'Studio Admin';
                    $t_init = $t['assignee_init'] ?? 'SA';
                    $t_progress = intval( $t['progress'] ?? 50 );
                    $t_phone = $t['phone'] ?? '+91 98201 45892';
                    $t_email = $t['email'] ?? 'rohan.verma@enterprise.com';
                    $is_done = ( $stage_key === 'done' || $t['status'] === 'done' );

                    if ( $is_done ) {
                        $pill_class = 'bg-emerald-50 text-emerald-800 border border-emerald-200/80';
                        $score_label = 'Won';
                        $score_icon = '<svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                    } elseif ( $t_score === 'hot' || ( $t['priority'] ?? '' ) === 'urgent' ) {
                        $pill_class = 'bg-rose-50 text-rose-800 border border-rose-200/80';
                        $score_label = 'Hot';
                        $score_icon = '<svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg>';
                    } elseif ( $t_score === 'cold' ) {
                        $pill_class = 'bg-sky-50 text-sky-800 border border-sky-200/80';
                        $score_label = 'Cold';
                        $score_icon = '<svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M20 12H4M12 20V4"/></svg>';
                    } else {
                        $pill_class = 'bg-amber-50 text-amber-800 border border-amber-200/80';
                        $score_label = 'Warm';
                        $score_icon = '<svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line></svg>';
                    }
                ?>
                <div class="cora-task-card bg-white p-3 rounded-xl border border-zinc-200/90 hover:border-zinc-400 transition-all cursor-grab active:cursor-grabbing flex flex-col gap-2 relative group overflow-hidden select-none"
                     draggable="true"
                     data-id="<?php echo esc_attr( $t['id'] ); ?>"
                     data-client="<?php echo esc_attr( $t_client ); ?>"
                     data-title="<?php echo esc_attr( $t_title ); ?>"
                     data-scale="<?php echo esc_attr( $t_scale ); ?>"
                     data-city="<?php echo esc_attr( $t_city ); ?>"
                     data-price="<?php echo esc_attr( $t_price ); ?>"
                     data-status="<?php echo esc_attr( $stage_key ); ?>"
                     data-score="<?php echo esc_attr( $t_score ); ?>"
                     data-priority="<?php echo esc_attr( $t['priority'] ?? 'medium' ); ?>"
                     data-format="<?php echo esc_attr( $t_format ); ?>"
                     data-assignee="<?php echo esc_attr( $t_assignee ); ?>"
                     data-phone="<?php echo esc_attr( $t_phone ); ?>"
                     data-email="<?php echo esc_attr( $t_email ); ?>"
                     data-notes="<?php echo esc_attr( $t['notes'] ?? '' ); ?>"
                     data-progress="<?php echo esc_attr( $t_progress ); ?>"
                     ondragstart="window.coraTaskDragStart(event, this)"
                     ondragend="window.coraTaskDragEnd(event, this)"
                     onclick="window.openTaskDrawer('<?php echo esc_js( $t['id'] ); ?>')">
                     
                     <!-- LEVEL 1: Client Name & Temperature Badge -->
                     <div class="flex items-center justify-between gap-1.5">
                         <span class="font-bold text-[11px] text-zinc-900 uppercase tracking-wider truncate" title="<?php echo esc_attr( $t_client ); ?>">
                             <?php echo esc_html( $t_client ); ?>
                         </span>
                         <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[8.5px] font-bold shrink-0 <?php echo $pill_class; ?>">
                             <?php echo $score_icon; ?>
                             <?php echo esc_html( $score_label ); ?>
                         </span>
                     </div>

                     <!-- LEVEL 2: Project Scale, City, Price & Format Badge -->
                     <div class="flex flex-col gap-1 pt-0.5">
                         <div class="flex items-center justify-between gap-1 text-[11.5px]">
                             <span class="font-semibold text-zinc-800 truncate" title="<?php echo esc_attr( $t_scale ); ?>">
                                 <?php echo esc_html( $t_scale ); ?>
                             </span>
                             <span class="text-[10px] text-zinc-400 font-medium shrink-0 flex items-center gap-0.5">
                                 <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                 <?php echo esc_html( $t_city ); ?>
                             </span>
                         </div>
                         <div class="flex items-center justify-between gap-1.5">
                             <span class="font-black text-xs text-zinc-950 font-mono tracking-tight">
                                 ₹<?php echo number_format( $t_price ); ?>
                             </span>
                             <span class="px-1.5 py-0.2 rounded-md bg-zinc-100 border border-zinc-200/80 text-zinc-500 font-bold text-[8.5px] uppercase tracking-wider truncate max-w-[95px]">
                                 <?php echo esc_html( $t_format ); ?>
                             </span>
                         </div>
                     </div>

                     <!-- LEVEL 3: Single-Row Footer: Assignee + Micro Action Cluster -->
                     <div class="pt-2 border-t border-zinc-100 flex items-center justify-between gap-1">
                         <!-- Left: Assignee Team Member -->
                         <div class="flex items-center gap-1.5 min-w-0">
                             <div class="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-[8px] shrink-0 border border-zinc-200" title="Assigned to <?php echo esc_attr( $t_assignee ); ?>">
                                 <?php echo esc_html( $t_init ); ?>
                             </div>
                             <div class="min-w-0 flex items-center gap-1">
                                 <span class="font-bold text-zinc-800 text-[10px] leading-none truncate max-w-[65px]"><?php echo esc_html( $t_assignee ); ?></span>
                             </div>
                         </div>

                         <!-- Right: Micro-Actions & Mini CTA Pill -->
                         <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                             <!-- WhatsApp -->
                             <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $t_phone); ?>" target="_blank" class="w-5.5 h-5.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 flex items-center justify-center transition-colors" title="WhatsApp" onclick="event.stopPropagation()">
                                 <svg viewBox="0 0 24 24" width="10" height="10" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.67-1.616-.919-2.213-.242-.58-.487-.502-.67-.511l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg>
                             </a>
                             <!-- Call -->
                             <a href="tel:<?php echo esc_attr($t_phone); ?>" class="w-5.5 h-5.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 text-zinc-600 border border-zinc-200/80 flex items-center justify-center transition-colors" title="Call" onclick="event.stopPropagation()">
                                 <svg viewBox="0 0 24 24" width="9.5" height="9.5" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                             </a>
                             <!-- Email -->
                             <a href="mailto:<?php echo esc_attr($t_email); ?>" class="w-5.5 h-5.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 text-zinc-600 border border-zinc-200/80 flex items-center justify-center transition-colors" title="Email" onclick="event.stopPropagation()">
                                 <svg viewBox="0 0 24 24" width="9.5" height="9.5" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                             </a>
                             <!-- Details Pill -->
                             <button type="button" class="h-5.5 px-2 font-bold rounded-lg text-[9px] transition-all cursor-pointer flex items-center gap-1 shadow-2xs bg-zinc-950 text-white hover:bg-zinc-800" onclick="event.stopPropagation(); window.openTaskDrawer('<?php echo esc_js( $t['id'] ); ?>')">
                                 <span>Details</span>
                             </button>
                         </div>
                     </div>

                     <!-- Progress Line -->
                     <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-zinc-100 rounded-b-xl overflow-hidden">
                         <div class="h-full bg-emerald-500 transition-all" style="width: <?php echo esc_attr( $t_progress ); ?>%;"></div>
                     </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Column Footer Add Button (Permanently Fixed at Bottom) -->
            <div class="cora-task-col-footer-add shrink-0 pt-2 pb-0.5 mt-auto z-10" style="background-color: var(--col-bg);">
                <button type="button" class="w-full py-2 text-center text-xs font-bold rounded-xl bg-white/85 hover:bg-white text-zinc-700 hover:text-zinc-950 transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs border-0 outline-none backdrop-blur-sm" style="border: none !important; outline: none !important; box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;" onclick="window.openCreateTaskDrawer('<?php echo esc_attr($stage_key); ?>')">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Add Task</span>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     TASK DETAILS DRAWER SHEET
     ═══════════════════════════════════════════════════════════════════ -->
<div id="cora-task-drawer" class="cora-client-drawer-panel fixed z-[9999] bg-white border-l border-zinc-200 transition-all duration-300 flex flex-col shadow-2xl collapsed" style="top: 48px; right: 0; bottom: 0; height: calc(100vh - 48px); width: 500px; display: none;">
    <!-- Pull Handle Indicator (Mobile) -->
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <!-- Header -->
    <div class="p-5 border-b border-zinc-100 flex items-start justify-between gap-3 bg-zinc-50/50 shrink-0">
        <div class="space-y-1 min-w-0">
            <span id="task-drawer-client-name" class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block truncate">Rohan Verma</span>
            <h3 id="task-drawer-title" class="text-base font-bold text-zinc-950 truncate leading-tight">Commercial Brand Shoot</h3>
        </div>
        <button type="button" onclick="window.closeTaskDrawer()" class="p-1.5 rounded-lg hover:bg-zinc-200/80 text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer border-0 bg-transparent shrink-0" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto p-5 space-y-4">
        <!-- Status & Priority -->
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Stage Status</label>
                <select id="task-drawer-status" class="w-full h-9 px-2.5 text-xs font-semibold bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400">
                    <option value="todo">New Brief / To Do</option>
                    <option value="in_progress">In Production</option>
                    <option value="review">Review & Proofing</option>
                    <option value="done">Completed & Delivered</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Priority</label>
                <select id="task-drawer-priority" class="w-full h-9 px-2.5 text-xs font-semibold bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400">
                    <option value="urgent">🔥 Urgent / Hot</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
        </div>

        <!-- Assignee & Value -->
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Assigned To</label>
                <select id="task-drawer-assignee" class="w-full h-9 px-2.5 text-xs font-semibold bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400">
                    <option value="Studio Admin">Studio Admin</option>
                    <option value="Rohan Verma">Rohan Verma</option>
                    <option value="Kavya Patel">Kavya Patel</option>
                    <option value="Aarav Mehta">Aarav Mehta</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Budget / Value (₹)</label>
                <input id="task-drawer-price" type="number" class="w-full h-9 px-2.5 text-xs font-mono font-bold bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400" />
            </div>
        </div>

        <!-- Notes & Brief -->
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Production Brief & Notes</label>
            <textarea id="task-drawer-notes" rows="4" class="w-full p-2.5 text-xs bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400 text-zinc-800 placeholder:text-zinc-400"></textarea>
        </div>

        <!-- Checklist -->
        <div class="space-y-2">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Deliverable Milestones</span>
            <div id="task-drawer-checklist" class="space-y-2 bg-zinc-50/70 p-3 rounded-xl border border-zinc-100 text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked class="rounded text-zinc-950 focus:ring-0" />
                    <span class="text-zinc-800">Pre-production client brief alignment</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked class="rounded text-zinc-950 focus:ring-0" />
                    <span class="text-zinc-800">Studio lighting & shoot execution</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="rounded text-zinc-950 focus:ring-0" />
                    <span class="text-zinc-800">Color grading & sound mastering</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="rounded text-zinc-950 focus:ring-0" />
                    <span class="text-zinc-800">Client portal proofing approval</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-4 border-t border-zinc-100 flex items-center justify-between gap-2 bg-zinc-50 shrink-0">
        <button type="button" onclick="window.coraSaveTaskDetails()" class="flex-1 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold transition-all shadow-2xs border-0 cursor-pointer">
            Save Changes
        </button>
        <button type="button" onclick="window.closeTaskDrawer()" class="px-4 py-2.5 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-100 text-zinc-700 text-xs font-bold transition-all cursor-pointer">
            Cancel
        </button>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     NEW TASK CREATOR DRAWER
     ═══════════════════════════════════════════════════════════════════ -->
<div id="cora-new-task-drawer" class="cora-client-drawer-panel fixed z-[9999] bg-white border-l border-zinc-200 transition-all duration-300 flex flex-col shadow-2xl collapsed" style="top: 48px; right: 0; bottom: 0; height: calc(100vh - 48px); width: 500px; display: none;">
    <!-- Pull Handle Indicator (Mobile) -->
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <!-- Header -->
    <div class="p-5 border-b border-zinc-100 flex items-start justify-between gap-3 bg-zinc-50/50 shrink-0">
        <div class="space-y-1">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Client Task Pipeline</span>
            <h3 class="text-base font-bold text-zinc-950 leading-tight">Create New Task</h3>
        </div>
        <button type="button" onclick="window.closeCreateTaskDrawer()" class="p-1.5 rounded-lg hover:bg-zinc-200/80 text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer border-0 bg-transparent shrink-0">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Form Body -->
    <form id="cora-create-task-form" onsubmit="window.handleCreateTaskSubmit(event)" class="flex-1 overflow-y-auto p-5 space-y-4 flex flex-col justify-between">
        <div class="space-y-3.5">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Client Account *</label>
                <select id="new-task-client" required class="w-full h-9 px-2.5 text-xs font-semibold bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400">
                    <?php foreach ( $clients_raw as $c ) : 
                        $c_name = trim( ( $c['name'] ?? '' ) ?: ( ( $c['first_name'] ?? '' ) . ' ' . ( $c['last_name'] ?? '' ) ) );
                        if ( stripos( $c_name, 'shruti' ) !== false || empty( $c_name ) ) $c_name = 'Rohan Verma';
                    ?>
                        <option value="<?php echo esc_attr( $c_name ); ?>"><?php echo esc_html( $c_name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Task / Project Title *</label>
                <input id="new-task-title" type="text" required placeholder="e.g. Brand Video Production Shoot" class="w-full h-9 px-2.5 text-xs bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400 text-zinc-900 placeholder:text-zinc-400" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Initial Stage</label>
                    <select id="new-task-stage" class="w-full h-9 px-2.5 text-xs font-semibold bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400">
                        <option value="todo">New Brief / To Do</option>
                        <option value="in_progress">In Production</option>
                        <option value="review">Review & Proofing</option>
                        <option value="done">Completed & Delivered</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Priority</label>
                    <select id="new-task-priority" class="w-full h-9 px-2.5 text-xs font-semibold bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400">
                        <option value="urgent">🔥 Urgent / Hot</option>
                        <option value="high" selected>High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Contract Value (₹)</label>
                    <input id="new-task-price" type="number" value="75000" class="w-full h-9 px-2.5 text-xs font-mono font-bold bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400" />
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Category Format</label>
                    <select id="new-task-format" class="w-full h-9 px-2.5 text-xs font-semibold bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400">
                        <option value="PHOTOSHOOT">PHOTOSHOOT</option>
                        <option value="VIDEO">VIDEO</option>
                        <option value="RETOUCH">RETOUCH</option>
                        <option value="ARCHITECTURAL">ARCHITECTURAL</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Notes & Production Details</label>
                <textarea id="new-task-notes" rows="3" placeholder="Add location, gear notes, or shoot requirements..." class="w-full p-2.5 text-xs bg-white border border-zinc-200 rounded-xl outline-none focus:border-zinc-400 text-zinc-800 placeholder:text-zinc-400"></textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-zinc-100 flex items-center justify-between gap-2">
            <button type="submit" class="flex-1 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold transition-all shadow-2xs border-0 cursor-pointer">
                Create Task
            </button>
            <button type="button" onclick="window.closeCreateTaskDrawer()" class="px-4 py-2.5 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-100 text-zinc-700 text-xs font-bold transition-all cursor-pointer">
                Cancel
            </button>
        </div>
    </form>
</div>

<script>
window.coraTasksData = <?php echo json_encode( array_values( $tasks_raw ) ); ?>;
window.coraActiveTask = null;
window.coraDraggedTaskCard = null;

// Drag and drop handlers
window.coraTaskDragStart = function(e, cardEl) {
    window.coraDraggedTaskCard = cardEl;
    cardEl.classList.add('opacity-40');
    if (e.dataTransfer) {
        e.dataTransfer.setData('text/plain', cardEl.getAttribute('data-id'));
        e.dataTransfer.effectAllowed = 'move';
    }
};

window.coraTaskDragEnd = function(e, cardEl) {
    if (cardEl) cardEl.classList.remove('opacity-40');
    window.coraDraggedTaskCard = null;
};

window.coraTaskDragOver = function(e, colEl) {
    if (e.preventDefault) e.preventDefault();
    if (e.dataTransfer) e.dataTransfer.dropEffect = 'move';
};

window.coraTaskDrop = function(e, colEl) {
    if (e.preventDefault) e.preventDefault();
    if (e.stopPropagation) e.stopPropagation();

    var cardEl = window.coraDraggedTaskCard;
    if (!cardEl) return;

    var newStatus = colEl.getAttribute('data-status');
    var oldStatus = cardEl.getAttribute('data-status');

    if (newStatus === oldStatus) return;

    cardEl.setAttribute('data-status', newStatus);
    var cardsContainer = colEl.querySelector('.cora-task-cards-container');
    if (cardsContainer) {
        var placeholder = cardsContainer.querySelector('.task-empty-placeholder');
        if (placeholder) placeholder.style.display = 'none';
        cardsContainer.appendChild(cardEl);
    }

    // Recalculate column counters & totals
    window.coraRecalculateTaskColumns();

    if (window.coraShowToast) {
        var stageNames = { 'todo': 'To Do', 'in_progress': 'In Production', 'review': 'Review & Proofing', 'done': 'Completed & Delivered' };
        window.coraShowToast('✓ Task moved to ' + (stageNames[newStatus] || newStatus), 'success');
    }
};

// Recalculate column counters and pipeline sums
window.coraRecalculateTaskColumns = function() {
    var totalTasks = 0;
    document.querySelectorAll('.cora-task-kanban-column').forEach(function(col) {
        var status = col.getAttribute('data-status');
        var cards = col.querySelectorAll('.cora-task-card:not([style*="display: none"])');
        var count = cards.length;
        totalTasks += count;

        var countEl = col.querySelector('.task-col-count');
        if (countEl) countEl.textContent = count;

        var sumVal = 0;
        cards.forEach(function(c) {
            sumVal += parseFloat(c.getAttribute('data-price') || '0');
        });

        var sumEl = col.querySelector('.task-col-sum');
        if (sumEl) sumEl.textContent = '₹' + Math.round(sumVal).toLocaleString('en-IN');
    });

    var pill = document.getElementById('cora-tasks-total-pill');
    if (pill) pill.textContent = totalTasks;
};

// Open and Close Task Details Drawer
window.openTaskDrawer = function(taskId) {
    var task = window.coraTasksData.find(function(t) { return String(t.id) === String(taskId); });
    if (!task) {
        var card = document.querySelector('.cora-task-card[data-id="' + taskId + '"]');
        if (card) {
            task = {
                id: taskId,
                client_name: card.getAttribute('data-client'),
                title: card.getAttribute('data-title'),
                status: card.getAttribute('data-status'),
                priority: card.getAttribute('data-priority'),
                price: card.getAttribute('data-price'),
                assignee: card.getAttribute('data-assignee'),
                notes: card.getAttribute('data-notes')
            };
        }
    }
    if (!task) return;

    window.coraActiveTask = task;
    document.getElementById('task-drawer-client-name').textContent = task.client_name || 'Rohan Verma';
    document.getElementById('task-drawer-title').textContent = task.title || 'Production Shoot';
    document.getElementById('task-drawer-status').value = task.status || 'todo';
    document.getElementById('task-drawer-priority').value = task.priority || 'medium';
    document.getElementById('task-drawer-assignee').value = task.assignee || 'Studio Admin';
    document.getElementById('task-drawer-price').value = task.price || 75000;
    document.getElementById('task-drawer-notes').value = task.notes || '';

    var drawer = document.getElementById('cora-task-drawer');
    drawer.style.display = 'flex';
    drawer.classList.remove('collapsed');
    drawer.classList.add('open');

    var backdrop = document.getElementById('cora-client-drawer-backdrop');
    if (backdrop) {
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        backdrop.classList.add('opacity-100');
    }
};

window.closeTaskDrawer = function() {
    var drawer = document.getElementById('cora-task-drawer');
    if (drawer) {
        drawer.classList.add('collapsed');
        drawer.classList.remove('open');
        setTimeout(function() { drawer.style.display = 'none'; }, 280);
    }
    var backdrop = document.getElementById('cora-client-drawer-backdrop');
    if (backdrop) {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
    }
};

// Save task details
window.coraSaveTaskDetails = function() {
    if (!window.coraActiveTask) return;
    var newStatus = document.getElementById('task-drawer-status').value;
    var newPriority = document.getElementById('task-drawer-priority').value;
    var newAssignee = document.getElementById('task-drawer-assignee').value;
    var newPrice = document.getElementById('task-drawer-price').value;
    var newNotes = document.getElementById('task-drawer-notes').value;

    window.coraActiveTask.status = newStatus;
    window.coraActiveTask.priority = newPriority;
    window.coraActiveTask.assignee = newAssignee;
    window.coraActiveTask.price = newPrice;
    window.coraActiveTask.notes = newNotes;

    var card = document.querySelector('.cora-task-card[data-id="' + window.coraActiveTask.id + '"]');
    if (card) {
        card.setAttribute('data-status', newStatus);
        card.setAttribute('data-priority', newPriority);
        card.setAttribute('data-price', newPrice);
        card.setAttribute('data-assignee', newAssignee);
        card.setAttribute('data-notes', newNotes);

        var targetCol = document.querySelector('.cora-task-kanban-column[data-status="' + newStatus + '"]');
        if (targetCol) {
            var container = targetCol.querySelector('.cora-task-cards-container');
            if (container && card.parentElement !== container) {
                container.appendChild(card);
            }
        }
    }

    window.closeTaskDrawer();
    window.coraRecalculateTaskColumns();
    if (window.coraShowToast) window.coraShowToast('✓ Task updated successfully', 'success');
};

// New Task Drawer
window.openCreateTaskDrawer = function(initialStage) {
    if (initialStage) {
        var stageSel = document.getElementById('new-task-stage');
        if (stageSel) stageSel.value = initialStage;
    }
    var drawer = document.getElementById('cora-new-task-drawer');
    drawer.style.display = 'flex';
    drawer.classList.remove('collapsed');
    drawer.classList.add('open');

    var backdrop = document.getElementById('cora-client-drawer-backdrop');
    if (backdrop) {
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        backdrop.classList.add('opacity-100');
    }
};

window.closeCreateTaskDrawer = function() {
    var drawer = document.getElementById('cora-new-task-drawer');
    if (drawer) {
        drawer.classList.add('collapsed');
        drawer.classList.remove('open');
        setTimeout(function() { drawer.style.display = 'none'; }, 280);
    }
    var backdrop = document.getElementById('cora-client-drawer-backdrop');
    if (backdrop) {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
    }
};

window.handleCreateTaskSubmit = function(e) {
    e.preventDefault();
    var client = document.getElementById('new-task-client').value;
    var title = document.getElementById('new-task-title').value.trim();
    var stage = document.getElementById('new-task-stage').value;
    var priority = document.getElementById('new-task-priority').value;
    var price = parseFloat(document.getElementById('new-task-price').value) || 75000;
    var format = document.getElementById('new-task-format').value;
    var notes = document.getElementById('new-task-notes').value.trim();

    if (!title) {
        if (window.coraShowToast) window.coraShowToast('Please enter a task title', 'warning');
        return;
    }

    var newId = 'task-' + Date.now();
    var newTaskObj = {
        id: newId,
        client_name: client,
        title: title,
        scale: title,
        city: 'Mumbai',
        price: price,
        format: format,
        status: stage,
        score: priority === 'urgent' ? 'hot' : 'warm',
        priority: priority,
        assignee: 'Studio Admin',
        assignee_init: 'SA',
        progress: 15,
        phone: '+91 98201 45892',
        email: 'client@enterprise.com',
        notes: notes
    };

    window.coraTasksData.unshift(newTaskObj);

    var targetCol = document.querySelector('.cora-task-kanban-column[data-status="' + stage + '"]');
    if (targetCol) {
        var container = targetCol.querySelector('.cora-task-cards-container');
        var placeholder = targetCol.querySelector('.task-empty-placeholder');
        if (placeholder) placeholder.style.display = 'none';

        var cardHtml = `
            <div class="cora-task-card bg-white p-3 rounded-xl border border-zinc-200/90 hover:border-zinc-400 transition-all cursor-grab active:cursor-grabbing flex flex-col gap-2 relative group overflow-hidden select-none"
                 draggable="true"
                 data-id="${newId}"
                 data-client="${client}"
                 data-title="${title}"
                 data-scale="${title}"
                 data-city="Mumbai"
                 data-price="${price}"
                 data-status="${stage}"
                 data-score="${newTaskObj.score}"
                 data-priority="${priority}"
                 data-format="${format}"
                 data-assignee="Studio Admin"
                 data-notes="${notes}"
                 ondragstart="window.coraTaskDragStart(event, this)"
                 ondragend="window.coraTaskDragEnd(event, this)"
                 onclick="window.openTaskDrawer('${newId}')">
                 
                 <div class="flex items-center justify-between gap-1.5">
                     <span class="font-bold text-[11px] text-zinc-900 uppercase tracking-wider truncate">${client}</span>
                     <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[8.5px] font-bold shrink-0 bg-rose-50 text-rose-800 border border-rose-200/80">Hot</span>
                 </div>

                 <div class="flex flex-col gap-1 pt-0.5">
                     <div class="flex items-center justify-between gap-1 text-[11.5px]">
                         <span class="font-semibold text-zinc-800 truncate">${title}</span>
                         <span class="text-[10px] text-zinc-400 font-medium shrink-0">Mumbai</span>
                     </div>
                     <div class="flex items-center justify-between gap-1.5">
                         <span class="font-black text-xs text-zinc-950 font-mono">₹${Math.round(price).toLocaleString('en-IN')}</span>
                         <span class="px-1.5 py-0.2 rounded-md bg-zinc-100 border border-zinc-200 text-zinc-500 font-bold text-[8.5px] uppercase">${format}</span>
                     </div>
                 </div>

                 <div class="pt-2 border-t border-zinc-100 flex items-center justify-between gap-1">
                     <div class="flex items-center gap-1.5">
                         <div class="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-[8px]">SA</div>
                         <span class="font-bold text-zinc-800 text-[10px]">Studio Admin</span>
                     </div>
                     <button type="button" class="h-5.5 px-2 font-bold rounded-lg text-[9px] bg-zinc-950 text-white" onclick="event.stopPropagation(); window.openTaskDrawer('${newId}')">
                         Details
                     </button>
                 </div>
                 <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-zinc-100 rounded-b-xl overflow-hidden">
                     <div class="h-full bg-emerald-500" style="width: 15%;"></div>
                 </div>
            </div>`;

        container.insertAdjacentHTML('afterbegin', cardHtml);
    }

    window.closeCreateTaskDrawer();
    window.coraRecalculateTaskColumns();
    if (window.coraShowToast) window.coraShowToast('✓ New task created in ' + stage, 'success');
};

// Column in-search and sorting
window.coraToggleTaskColumnSearch = function(btn) {
    var col = btn.closest('.cora-task-kanban-column');
    var box = col.querySelector('.cora-task-col-search-box');
    if (box) {
        box.classList.toggle('hidden');
        var input = box.querySelector('input');
        if (!box.classList.contains('hidden') && input) input.focus();
    }
};

window.coraFilterTaskColumnCards = function(input) {
    var q = input.value.toLowerCase().trim();
    var col = input.closest('.cora-task-kanban-column');
    col.querySelectorAll('.cora-task-card').forEach(function(card) {
        var text = (card.getAttribute('data-client') + ' ' + card.getAttribute('data-title') + ' ' + card.getAttribute('data-city')).toLowerCase();
        card.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
};

window.coraToggleTaskColumnMenu = function(btn, e) {
    if (e) e.stopPropagation();
    document.querySelectorAll('.cora-task-col-menu').forEach(function(m) { m.classList.add('hidden'); });
    var col = btn.closest('.cora-task-kanban-column');
    var menu = col.querySelector('.cora-task-col-menu');
    if (menu) menu.classList.toggle('hidden');
};

document.addEventListener('click', function() {
    document.querySelectorAll('.cora-task-col-menu').forEach(function(m) { m.classList.add('hidden'); });
});

window.coraSortTaskColumn = function(btn, sortType) {
    var col = btn.closest('.cora-task-kanban-column');
    var container = col.querySelector('.cora-task-cards-container');
    var cards = Array.from(container.querySelectorAll('.cora-task-card'));

    cards.sort(function(a, b) {
        if (sortType === 'value-desc') return parseFloat(b.getAttribute('data-price') || 0) - parseFloat(a.getAttribute('data-price') || 0);
        if (sortType === 'value-asc') return parseFloat(a.getAttribute('data-price') || 0) - parseFloat(b.getAttribute('data-price') || 0);
        if (sortType === 'name-asc') return (a.getAttribute('data-client') || '').localeCompare(b.getAttribute('data-client') || '');
        if (sortType === 'urgent-first') {
            var aScore = (a.getAttribute('data-priority') === 'urgent' || a.getAttribute('data-score') === 'hot') ? 1 : 0;
            var bScore = (b.getAttribute('data-priority') === 'urgent' || b.getAttribute('data-score') === 'hot') ? 1 : 0;
            return bScore - aScore;
        }
        return 0;
    });

    cards.forEach(function(card) { container.appendChild(card); });
    document.querySelectorAll('.cora-task-col-menu').forEach(function(m) { m.classList.add('hidden'); });
};

// Global search filter
window.coraFilterGlobalTasks = function(q) {
    q = (q || '').toLowerCase().trim();
    document.querySelectorAll('.cora-task-card').forEach(function(card) {
        var text = (card.getAttribute('data-client') + ' ' + card.getAttribute('data-title') + ' ' + card.getAttribute('data-city')).toLowerCase();
        card.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
    window.coraRecalculateTaskColumns();
};

window.coraFilterTaskPriority = function(filterType, btnEl) {
    document.querySelectorAll('.task-priority-filter-btn').forEach(function(b) {
        b.classList.remove('active', 'bg-zinc-950', 'text-white', 'shadow-2xs');
        b.classList.add('text-zinc-600', 'bg-transparent');
    });
    if (btnEl) {
        btnEl.classList.add('active', 'bg-zinc-950', 'text-white', 'shadow-2xs');
        btnEl.classList.remove('text-zinc-600', 'bg-transparent');
    }

    document.querySelectorAll('.cora-task-card').forEach(function(card) {
        var p = card.getAttribute('data-priority');
        var s = card.getAttribute('data-score');
        if (filterType === 'all') {
            card.style.display = '';
        } else if (filterType === 'urgent') {
            card.style.display = (p === 'urgent' || s === 'hot') ? '' : 'none';
        } else if (filterType === 'high') {
            card.style.display = (p === 'high' || p === 'urgent') ? '' : 'none';
        }
    });
    window.coraRecalculateTaskColumns();
};
</script>
