<?php
/**
 * Cora Workspace - Client Task Manager View
 * 
 * Benchmark Studio Workflow & Task Management System
 * Bottom Drawer Sheet (Canvas Scoped - 24px Sidebar Breathing Gap).
 * Zero emojis. Monochromatic slate/zinc palette with functional color accents.
 * Zero box shadow (box-shadow: none). Clean 1px border stroke.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<style>
/* ─── Bottom Sheet Drawer Engine (24px Sidebar Gap + No Shadow) ─────────────── */
.cora-bottom-drawer {
    position: fixed !important;
    bottom: 0 !important;
    right: 24px !important; /* 24px right canvas gap */
    left: 284px; /* Dynamic sidebar right edge + 24px gap */
    height: 84vh !important;
    max-height: 850px !important;
    padding: 0 !important;
    margin: 0 !important;
    background-color: #ffffff !important;
    border-top: 1px solid #e4e4e7 !important;
    border-left: 1px solid #e4e4e7 !important;
    border-right: 1px solid #e4e4e7 !important;
    border-top-left-radius: 24px !important;
    border-top-right-radius: 24px !important;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    box-shadow: none !important; /* Zero shadow as explicitly requested */
    z-index: 99999 !important;
    transform: translateY(100vh) !important; /* Translate 100vh completely off screen */
    visibility: hidden !important; /* Completely hidden so zero peeking handle bars */
    opacity: 0 !important;
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease, left 0.2s ease, visibility 0.35s !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    pointer-events: none;
}

/* Active Bottom Sheet State - Slides UP from bottom */
.cora-bottom-drawer.cora-drawer-open {
    transform: translateY(0) !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}
</style>

<div class="cora-task-manager-wrap bg-zinc-50 min-h-screen text-zinc-900 font-sans px-8 py-6 max-w-[1700px] mx-auto pb-20 relative">
    
    <!-- Top Header -->
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8 pb-6 border-b border-zinc-200/80">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">Client Task Manager</h1>
                <span class="px-2.5 py-0.5 text-xs font-bold bg-zinc-950 text-white rounded-md uppercase tracking-wider">Studio Engine</span>
            </div>
            <p class="text-xs font-medium text-zinc-500 max-w-2xl">Manage end-to-end client deliverable workflows, shoot checklists, and staff assignments for 50+ active studio projects.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="coraOpenTemplateDrawer(event)" class="px-4 py-2.5 bg-white border border-zinc-200 text-zinc-800 text-xs font-bold rounded-xl hover:bg-zinc-100 hover:border-zinc-300 transition-all shadow-2xs flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                Apply Workflow Template
            </button>
            <button onclick="coraOpenCreateTaskDrawer(event)" class="px-4.5 py-2.5 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                New Client Task
            </button>
        </div>
    </header>

    <!-- Top Metric KPI Bar -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-2">Total Active Tasks</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-zinc-950" id="kpi-total-active">0</span>
                <span class="text-xs font-semibold text-zinc-400">Queue</span>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-2">Pending Deliverables</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-zinc-950" id="kpi-pending-deliv">0</span>
                <span class="text-xs font-semibold text-amber-600">In Production</span>
            </div>
        </div>
        <div class="bg-rose-50/40 p-5 rounded-2xl border border-rose-200 shadow-2xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider mb-2">Overdue / At Risk</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-rose-600" id="kpi-overdue-count">0</span>
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Action Needed</span>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-2">Completed (Month)</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-zinc-950" id="kpi-completed-count">0</span>
                <span class="text-xs font-semibold text-emerald-600">Delivered</span>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-col justify-between col-span-2 md:col-span-1">
            <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-2">Team Capacity</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-zinc-950" id="kpi-team-capacity">0%</span>
                <span class="text-xs font-semibold text-zinc-500">Occupancy</span>
            </div>
        </div>
    </div>

    <!-- View Switcher & Filter Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 bg-white p-3 rounded-2xl border border-zinc-200/80 shadow-2xs">
        <div class="flex items-center gap-1.5 p-1 bg-zinc-100/80 rounded-xl border border-zinc-200/60 w-full md:w-auto">
            <button onclick="coraSwitchView('kanban')" id="btn-view-kanban" class="px-4 py-2 text-xs font-bold rounded-lg text-zinc-950 bg-white shadow-2xs border border-zinc-200 transition-all cursor-pointer">Kanban Board</button>
            <button onclick="coraSwitchView('matrix')" id="btn-view-matrix" class="px-4 py-2 text-xs font-bold rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-all cursor-pointer">Client Matrix</button>
            <button onclick="coraSwitchView('roster')" id="btn-view-roster" class="px-4 py-2 text-xs font-bold rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-all cursor-pointer">Team Roster</button>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto justify-end overflow-x-auto">
            <div class="relative min-w-[200px] flex-1 md:flex-initial">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path></svg>
                <input type="text" id="task-search-input" placeholder="Search task title or client..." class="pl-9 pr-3 py-2 text-xs font-medium border border-zinc-200 rounded-xl bg-white focus:outline-none focus:border-zinc-950 w-full text-zinc-900 placeholder-zinc-400" oninput="coraFilterTasks()">
            </div>
            <select id="task-filter-client" class="py-2 pl-3 pr-8 text-xs font-bold border border-zinc-200 rounded-xl bg-white focus:outline-none focus:border-zinc-950 text-zinc-700 cursor-pointer" onchange="coraFilterTasks()">
                <option value="">All Clients</option>
            </select>
            <select id="task-filter-category" class="py-2 pl-3 pr-8 text-xs font-bold border border-zinc-200 rounded-xl bg-white focus:outline-none focus:border-zinc-950 text-zinc-700 cursor-pointer" onchange="coraFilterTasks()">
                <option value="">All Deliverable Types</option>
                <option value="Photo Shoot Prep">Photo Shoot Prep</option>
                <option value="Video Production">Video Production</option>
                <option value="Post-Production &amp; Editing">Post-Production &amp; Editing</option>
                <option value="Client Deliverables &amp; Vault">Client Deliverables &amp; Vault</option>
                <option value="Client Communication">Client Communication</option>
            </select>
            <select id="task-filter-assignee" class="py-2 pl-3 pr-8 text-xs font-bold border border-zinc-200 rounded-xl bg-white focus:outline-none focus:border-zinc-950 text-zinc-700 cursor-pointer" onchange="coraFilterTasks()">
                <option value="">All Assignees</option>
            </select>
        </div>
    </div>

    <!-- View Panels Container -->
    <div class="relative">
        
        <!-- Panel 1: Horizontally Scrollable Kanban Board -->
        <div id="panel-view-kanban" class="flex gap-5 overflow-x-auto pb-8 pt-1 scrollbar-thin">
            
            <!-- Column: To Do -->
            <div class="kanban-col flex flex-col bg-zinc-100/80 rounded-2xl p-4 border border-zinc-200/80 w-[330px] shrink-0 min-h-[620px]">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-zinc-400"></span>
                        <h3 class="text-xs font-bold text-zinc-800 uppercase tracking-wider">To Do</h3>
                    </div>
                    <span class="text-[11px] font-bold bg-white border border-zinc-200/80 text-zinc-800 px-2.5 py-0.5 rounded-full shadow-2xs" id="count-kanban-todo">0</span>
                </div>
                <div class="space-y-3.5 flex-1 overflow-y-auto pr-0.5" id="kanban-todo">
                    <!-- Tasks rendered dynamically -->
                </div>
            </div>

            <!-- Column: In Progress -->
            <div class="kanban-col flex flex-col bg-amber-50/30 rounded-2xl p-4 border border-amber-200/60 w-[330px] shrink-0 min-h-[620px]">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider">In Progress</h3>
                    </div>
                    <span class="text-[11px] font-bold bg-white border border-amber-200 text-amber-900 px-2.5 py-0.5 rounded-full shadow-2xs" id="count-kanban-inprogress">0</span>
                </div>
                <div class="space-y-3.5 flex-1 overflow-y-auto pr-0.5" id="kanban-inprogress">
                    <!-- Tasks rendered dynamically -->
                </div>
            </div>

            <!-- Column: Client Review -->
            <div class="kanban-col flex flex-col bg-blue-50/30 rounded-2xl p-4 border border-blue-200/60 w-[330px] shrink-0 min-h-[620px]">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider">Client Review</h3>
                    </div>
                    <span class="text-[11px] font-bold bg-white border border-blue-200 text-blue-900 px-2.5 py-0.5 rounded-full shadow-2xs" id="count-kanban-review">0</span>
                </div>
                <div class="space-y-3.5 flex-1 overflow-y-auto pr-0.5" id="kanban-review">
                    <!-- Tasks rendered dynamically -->
                </div>
            </div>

            <!-- Column: Blocked -->
            <div class="kanban-col flex flex-col bg-rose-50/30 rounded-2xl p-4 border border-rose-200/60 w-[330px] shrink-0 min-h-[620px]">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wider">Blocked</h3>
                    </div>
                    <span class="text-[11px] font-bold bg-white border border-rose-200 text-rose-900 px-2.5 py-0.5 rounded-full shadow-2xs" id="count-kanban-blocked">0</span>
                </div>
                <div class="space-y-3.5 flex-1 overflow-y-auto pr-0.5" id="kanban-blocked">
                    <!-- Tasks rendered dynamically -->
                </div>
            </div>

            <!-- Column: Done -->
            <div class="kanban-col flex flex-col bg-emerald-50/30 rounded-2xl p-4 border border-emerald-200/60 w-[330px] shrink-0 min-h-[620px]">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h3 class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Done</h3>
                    </div>
                    <span class="text-[11px] font-bold bg-white border border-emerald-200 text-emerald-900 px-2.5 py-0.5 rounded-full shadow-2xs" id="count-kanban-done">0</span>
                </div>
                <div class="space-y-3.5 flex-1 overflow-y-auto pr-0.5" id="kanban-done">
                    <!-- Tasks rendered dynamically -->
                </div>
            </div>
        </div>

        <!-- Panel 2: Client Project Matrix (Hidden initially) -->
        <div id="panel-view-matrix" class="hidden grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Project Cards rendered dynamically -->
        </div>

        <!-- Panel 3: Team Roster Workload (Hidden initially) -->
        <div id="panel-view-roster" class="hidden grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Staff Cards rendered dynamically -->
        </div>
    </div>
</div>

<!-- BOTTOM SHEET DRAWER 1: Task Workspace & Checklist (24px Sidebar Gap) -->
<div id="drawer-task-details" class="cora-bottom-drawer">
    <!-- Pull Bar & Header -->
    <div class="pt-3 px-6 pb-5 border-b border-zinc-200 bg-zinc-50/90 rounded-t-3xl shrink-0">
        <div class="w-12 h-1.5 rounded-full bg-zinc-300 mx-auto mb-3"></div>
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-zinc-950 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-zinc-950">Task Workspace &amp; Checklist</h2>
                    <p class="text-[11px] font-medium text-zinc-500">Configure task parameters, checklist steps, deliverable drive links &amp; activity notes.</p>
                </div>
            </div>
            <button type="button" onclick="window.coraCloseAllDrawers()" class="p-2 rounded-xl hover:bg-zinc-200/80 text-zinc-400 hover:text-zinc-950 transition-colors cursor-pointer" title="Close Workspace Sheet (Esc)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Wide 2-Column Content Body -->
    <div class="flex-1 overflow-y-auto p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <input type="hidden" id="detail-task-id" value="">

        <!-- Left Column: Primary Task Parameters -->
        <div class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-1.5">Task Title *</label>
                <input type="text" id="detail-task-title" class="w-full text-lg font-bold text-zinc-950 border border-zinc-200 rounded-xl px-4 py-3 focus:outline-none focus:border-zinc-950 bg-zinc-50/50">
            </div>

            <!-- Project Badges -->
            <div class="flex items-center gap-2 flex-wrap" id="detail-project-badges">
                <!-- Client & booking pills injected via JS -->
            </div>

            <!-- Meta Grid (2x2) -->
            <div class="grid grid-cols-2 gap-4 bg-zinc-50 p-5 rounded-2xl border border-zinc-200/80">
                <div>
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Assigned Staff</label>
                    <select id="detail-task-assignee" class="w-full text-xs font-bold border-zinc-200 rounded-xl focus:ring-0 focus:border-zinc-950 bg-white py-2.5 px-3 cursor-pointer">
                        <option value="">Unassigned</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Status Stage</label>
                    <select id="detail-task-status" class="w-full text-xs font-bold border-zinc-200 rounded-xl focus:ring-0 focus:border-zinc-950 bg-white py-2.5 px-3 cursor-pointer">
                        <option value="todo">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="client_review">Client Review</option>
                        <option value="blocked">Blocked</option>
                        <option value="done">Done</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Due Date</label>
                    <input type="date" id="detail-task-due" class="w-full text-xs font-bold border-zinc-200 rounded-xl focus:ring-0 focus:border-zinc-950 bg-white py-2 px-3">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Priority Level</label>
                    <select id="detail-task-priority" class="w-full text-xs font-bold border-zinc-200 rounded-xl focus:ring-0 focus:border-zinc-950 bg-white py-2.5 px-3 cursor-pointer">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <!-- Deliverable Asset Drive/Proofing Link -->
            <div>
                <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-1.5">Deliverable Asset / Drive URL</label>
                <input type="url" id="detail-task-asset-url" placeholder="https://drive.google.com/... or https://proofing.studio/..." class="w-full text-xs font-medium border border-zinc-200 rounded-xl px-4 py-3 focus:outline-none focus:border-zinc-950 bg-zinc-50/50">
            </div>

            <!-- Email Alert Box -->
            <div class="bg-zinc-50 p-4 rounded-2xl border border-zinc-200/80 space-y-3">
                <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wider">Email Notifications &amp; Alerts</h4>
                <div class="flex items-center justify-between gap-3">
                    <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-zinc-800">
                        <input type="checkbox" id="detail-email-notify" checked class="w-4 h-4 rounded text-zinc-950 focus:ring-0 cursor-pointer">
                        <span>Send instant HTML email alert to assignee on save</span>
                    </label>
                    <button type="button" onclick="coraTriggerEmailReminder()" class="px-3.5 py-2 bg-white border border-zinc-200 hover:bg-zinc-100 text-zinc-800 text-xs font-bold rounded-xl transition-colors cursor-pointer shrink-0 shadow-2xs">
                        Send Email Alert Now
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Subtask Checklist & Activity Notes -->
        <div class="space-y-6">
            <!-- Subtasks Checklist Section -->
            <div class="bg-zinc-50 p-5 rounded-2xl border border-zinc-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-zinc-950 uppercase tracking-wider">Subtask Checklist</h3>
                    <span class="text-[11px] font-bold text-zinc-500" id="detail-subtasks-count">0 steps</span>
                </div>
                <div class="space-y-2 max-h-56 overflow-y-auto" id="detail-subtasks-list">
                    <!-- Checklist items injected dynamically -->
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-zinc-200/60">
                    <input type="text" id="detail-new-subtask-input" placeholder="Add checklist step (e.g. Sync audio &amp; video)..." class="flex-1 px-3.5 py-2 bg-white border border-zinc-200 rounded-xl text-xs font-medium focus:outline-none focus:border-zinc-950">
                    <button type="button" onclick="coraAddDetailSubtask()" class="px-4 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-colors cursor-pointer shrink-0">
                        Add Step
                    </button>
                </div>
            </div>

            <!-- Activity Log & Comments Feed -->
            <div class="bg-zinc-50 p-5 rounded-2xl border border-zinc-200/80 space-y-3">
                <h3 class="text-xs font-bold text-zinc-950 uppercase tracking-wider">Activity Log &amp; Team Notes</h3>
                <div class="space-y-2.5 max-h-48 overflow-y-auto" id="detail-comments-list">
                    <!-- Comments injected dynamically -->
                </div>
                <div class="space-y-2 pt-2 border-t border-zinc-200/60">
                    <textarea id="detail-comment-input" rows="2" placeholder="Post a progress update or technical note for the team..." class="w-full px-3.5 py-2.5 bg-white border border-zinc-200 rounded-xl text-xs font-medium text-zinc-900 focus:outline-none focus:border-zinc-950"></textarea>
                    <div class="flex justify-end">
                        <button type="button" onclick="coraPostTaskComment()" class="px-4 py-2 bg-zinc-900 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-colors cursor-pointer">
                            Post Note
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drawer Footer Actions -->
    <div class="p-5 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between shrink-0 rounded-b-none">
        <button type="button" onclick="coraDeleteTaskFromDrawer()" class="px-4 py-2.5 border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold rounded-xl transition-colors cursor-pointer">
            Delete Task
        </button>
        <div class="flex items-center gap-3">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-5 py-2.5 bg-white border border-zinc-200 text-zinc-800 text-xs font-bold rounded-xl hover:bg-zinc-100 transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="button" onclick="coraSaveTaskFromDrawer()" class="px-6 py-2.5 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-colors cursor-pointer shadow-sm">
                Save Task Changes
            </button>
        </div>
    </div>
</div>

<!-- BOTTOM SHEET DRAWER 2: Create Task (24px Sidebar Gap) -->
<div id="drawer-create-task" class="cora-bottom-drawer">
    <div class="pt-3 px-6 pb-5 border-b border-zinc-200 bg-zinc-50/90 rounded-t-3xl shrink-0">
        <div class="w-12 h-1.5 rounded-full bg-zinc-300 mx-auto mb-3"></div>
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-zinc-950 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-zinc-950">Create New Client Task</h2>
                    <p class="text-[11px] font-medium text-zinc-500">Tied directly to client CRM &amp; shoot booking projects.</p>
                </div>
            </div>
            <button type="button" onclick="window.coraCloseAllDrawers()" class="p-2 rounded-xl hover:bg-zinc-200/80 text-zinc-400 hover:text-zinc-950 transition-colors cursor-pointer" title="Close Workspace Sheet (Esc)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-1.5">Task Title *</label>
                <input type="text" id="create-task-title" placeholder="e.g. Export final 4K video reel" class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-bold text-zinc-950 focus:outline-none focus:border-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-1.5">Client &amp; Shoot Booking</label>
                <select id="create-task-booking" class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-bold text-zinc-950 focus:outline-none focus:border-zinc-950 cursor-pointer">
                    <option value="">General Project / Unlinked</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-1.5">Assign To Staff</label>
                <select id="create-task-assignee" class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-bold text-zinc-950 focus:outline-none focus:border-zinc-950 cursor-pointer">
                    <option value="">Unassigned</option>
                </select>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-1.5">Deliverable Type</label>
                    <select id="create-task-category" class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-bold text-zinc-950 focus:outline-none focus:border-zinc-950 cursor-pointer">
                        <option value="Photo Shoot Prep">Photo Shoot Prep</option>
                        <option value="Video Production">Video Production</option>
                        <option value="Post-Production &amp; Editing">Post-Production &amp; Editing</option>
                        <option value="Client Deliverables &amp; Vault">Client Deliverables &amp; Vault</option>
                        <option value="Client Communication">Client Communication</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-1.5">Priority Level</label>
                    <select id="create-task-priority" class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-bold text-zinc-950 focus:outline-none focus:border-zinc-950 cursor-pointer">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-1.5">Due Date</label>
                <input type="date" id="create-task-due" class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-bold text-zinc-950 focus:outline-none focus:border-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-1.5">Description (Optional)</label>
                <textarea id="create-task-desc" rows="3" placeholder="Specific guidelines or technical parameters for the team..." class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-medium text-zinc-950 focus:outline-none focus:border-zinc-950"></textarea>
            </div>
            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-zinc-800">
                    <input type="checkbox" id="create-task-email-notify" checked class="w-4 h-4 rounded text-zinc-950 focus:ring-0 cursor-pointer">
                    <span>Send HTML email notification to assigned staff member</span>
                </label>
            </div>
        </div>
    </div>

    <div class="p-5 border-t border-zinc-200 bg-zinc-50 flex items-center justify-end gap-3 shrink-0">
        <button type="button" onclick="window.coraCloseAllDrawers()" class="px-5 py-2.5 bg-white border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-100 transition-colors cursor-pointer">
            Cancel
        </button>
        <button type="button" onclick="coraSaveTask()" class="px-6 py-2.5 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-colors cursor-pointer shadow-sm">
            Create Task &amp; Dispatch
        </button>
    </div>
</div>

<!-- BOTTOM SHEET DRAWER 3: Workflow Template Picker (24px Sidebar Gap) -->
<div id="drawer-template-picker" class="cora-bottom-drawer">
    <div class="pt-3 px-6 pb-5 border-b border-zinc-200 bg-zinc-50/90 rounded-t-3xl shrink-0">
        <div class="w-12 h-1.5 rounded-full bg-zinc-300 mx-auto mb-3"></div>
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-zinc-950 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-zinc-950">Apply Studio Workflow Template</h2>
                    <p class="text-[11px] font-medium text-zinc-500">Auto-generate pre-configured deliverable task checklists.</p>
                </div>
            </div>
            <button type="button" onclick="window.coraCloseAllDrawers()" class="p-2 rounded-xl hover:bg-zinc-200/80 text-zinc-400 hover:text-zinc-950 transition-colors cursor-pointer" title="Close Workspace Sheet (Esc)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-2">1. Select Target Client Booking *</label>
            <select id="template-target-booking" class="w-full text-xs font-bold border-zinc-200 rounded-xl bg-zinc-50 focus:outline-none focus:border-zinc-950 p-3 cursor-pointer">
                <option value="">Choose booking project...</option>
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-zinc-700 uppercase tracking-wider mb-3">2. Choose Workflow Template</label>
            <div class="space-y-3">
                <label class="flex items-start gap-3 p-4 border border-zinc-200 rounded-2xl cursor-pointer hover:border-zinc-950 hover:bg-zinc-50 transition-colors has-[:checked]:border-zinc-950 has-[:checked]:bg-zinc-50 shadow-2xs">
                    <input type="radio" name="workflow_tpl" value="wedding_photo" checked class="mt-1 text-zinc-950 focus:ring-0 border-zinc-300">
                    <div>
                        <span class="block text-xs font-bold text-zinc-950">Wedding Photography Workflow</span>
                        <span class="block text-[11px] text-zinc-500 mt-0.5">7 tasks • Pre-shoot prep, Gear check, Shotlist signoff, RAW backup, Culling, Color grade, Proofing upload</span>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 border border-zinc-200 rounded-2xl cursor-pointer hover:border-zinc-950 hover:bg-zinc-50 transition-colors has-[:checked]:border-zinc-950 has-[:checked]:bg-zinc-50 shadow-2xs">
                    <input type="radio" name="workflow_tpl" value="commercial_video" class="mt-1 text-zinc-950 focus:ring-0 border-zinc-300">
                    <div>
                        <span class="block text-xs font-bold text-zinc-950">Commercial Video Production</span>
                        <span class="block text-[11px] text-zinc-500 mt-0.5">6 tasks • Storyboard, B-roll capture, Audio sync, Assembly cut, Sound mix, Final 4K export</span>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 border border-zinc-200 rounded-2xl cursor-pointer hover:border-zinc-950 hover:bg-zinc-50 transition-colors has-[:checked]:border-zinc-950 has-[:checked]:bg-zinc-50 shadow-2xs">
                    <input type="radio" name="workflow_tpl" value="drone_survey" class="mt-1 text-zinc-950 focus:ring-0 border-zinc-300">
                    <div>
                        <span class="block text-xs font-bold text-zinc-950">Drone &amp; Aerial Survey Mission</span>
                        <span class="block text-[11px] text-zinc-500 mt-0.5">5 tasks • Airspace clearance, Battery prep, 4K flight, Footage vault, LUT profile pass</span>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 border border-zinc-200 rounded-2xl cursor-pointer hover:border-zinc-950 hover:bg-zinc-50 transition-colors has-[:checked]:border-zinc-950 has-[:checked]:bg-zinc-50 shadow-2xs">
                    <input type="radio" name="workflow_tpl" value="client_onboarding" class="mt-1 text-zinc-950 focus:ring-0 border-zinc-300">
                    <div>
                        <span class="block text-xs font-bold text-zinc-950">Client Onboarding &amp; Contract</span>
                        <span class="block text-[11px] text-zinc-500 mt-0.5">4 tasks • Discovery call, Quote approval, Contract signature, Retainer deposit invoice</span>
                    </div>
                </label>
            </div>
        </div>
    </div>
    <div class="p-5 border-t border-zinc-200 bg-zinc-50 flex items-center justify-end gap-3 shrink-0">
        <button type="button" onclick="window.coraCloseAllDrawers()" class="px-5 py-2.5 bg-white border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-100 transition-colors cursor-pointer">
            Cancel
        </button>
        <button type="button" onclick="coraApplyTemplate()" class="px-6 py-2.5 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-colors cursor-pointer shadow-sm">
            Apply Template &amp; Dispatch Tasks
        </button>
    </div>
</div>

<!-- CLIENT TASK MANAGER SCRIPT ENGINE -->
<script>
// Dynamic Sidebar Width Calculator (Canvas Scoped Offset + 24px Gap)
function getSidebarWidthOffset() {
    var sidebar = document.querySelector('.cora-sidebar') || document.querySelector('#cora-sidebar') || document.querySelector('aside.cora-sidebar');
    if (sidebar) {
        var rect = sidebar.getBoundingClientRect();
        if (rect.width > 0 && rect.right > 0) {
            return (rect.right + 24) + 'px';
        }
    }
    if (document.body.classList.contains('collapsed-sidebar') || document.body.classList.contains('cora-sidebar-collapsed')) {
        return '94px'; // 70px + 24px
    }
    return '284px'; // 260px + 24px
}

// Pure Vanilla Global Drawer Sheet Closer (100% reliable)
window.coraCloseAllDrawers = function() {
    var drawers = document.querySelectorAll('.cora-bottom-drawer');
    drawers.forEach(function(el) {
        el.classList.remove('cora-drawer-open');
    });
};

// Global Escape Key Listener for Closing Drawers
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.coraCloseAllDrawers();
    }
});

// Click Outside Drawer Listener for Seamless Dismissal
document.addEventListener('click', function(e) {
    var openDrawer = document.querySelector('.cora-bottom-drawer.cora-drawer-open');
    if (openDrawer) {
        // If click is outside open drawer and not on trigger elements
        if (!openDrawer.contains(e.target) && !e.target.closest('button[onclick*="coraOpen"], div[onclick*="coraOpen"]')) {
            window.coraCloseAllDrawers();
        }
    }
});

(function() {
    // Custom Toast Notification system check
    if (typeof window.coraShowToast !== 'function') {
        window.coraShowToast = function(message) {
            const t = document.createElement('div');
            t.className = 'fixed bottom-5 left-1/2 transform -translate-x-1/2 bg-zinc-950 text-white px-4 py-2.5 rounded-xl shadow-2xl text-xs font-bold z-[10000] transition-all duration-300 border border-zinc-800';
            t.innerText = message;
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3000);
        };
    }

    // Dynamic Bottom Drawer Sheet Opener
    function coraOpenDrawer(event, drawerId) {
        if (event) event.stopPropagation();
        window.coraCloseAllDrawers();
        
        var offset = getSidebarWidthOffset();
        var el = document.getElementById(drawerId);
        if (el) {
            el.style.left = offset;
            setTimeout(function() {
                el.classList.add('cora-drawer-open');
            }, 10);
        }
    }

    window.coraOpenCreateTaskDrawer = function(event) {
        coraOpenDrawer(event, 'drawer-create-task');
    };

    window.coraOpenTemplateDrawer = function(event) {
        coraOpenDrawer(event, 'drawer-template-picker');
    };

    // View Switching
    window.coraSwitchView = function(viewName) {
        ['kanban', 'matrix', 'roster'].forEach(v => {
            $(`#btn-view-${v}`).removeClass('text-zinc-950 bg-white shadow-2xs border border-zinc-200').addClass('text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100');
            $(`#panel-view-${v}`).addClass('hidden').removeClass('flex grid');
        });
        $(`#btn-view-${viewName}`).addClass('text-zinc-950 bg-white shadow-2xs border border-zinc-200').removeClass('text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100');
        if (viewName === 'kanban') {
            $(`#panel-view-kanban`).removeClass('hidden').addClass('flex');
        } else {
            $(`#panel-view-${viewName}`).removeClass('hidden').addClass('grid');
        }
    };

    // Data Store
    let coraTaskState = {
        tasks: [],
        clients: [],
        bookings: [],
        teamMembers: [],
        templates: [],
        currentTaskSubtasks: [],
        currentTaskComments: []
    };

    // Load Tasks from WordPress Backend
    window.coraLoadClientTasks = function() {
        if (typeof coraREData === 'undefined') return;
        $.post(coraREData.ajaxUrl, {
            action: 'cora_fetch_client_tasks',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res && res.success) {
                coraTaskState.tasks = res.data.tasks || [];
                coraTaskState.clients = res.data.clients || [];
                coraTaskState.bookings = res.data.bookings || [];
                coraTaskState.teamMembers = res.data.team_members || [];
                coraTaskState.templates = res.data.templates || [];

                coraPopulateFilters();
                coraRenderTaskViews();
            }
        });
    };

    function coraPopulateFilters() {
        // Filter toolbar dropdowns
        let clientOpts = '<option value="">All Clients</option>';
        coraTaskState.clients.forEach(c => {
            clientOpts += `<option value="${c.id}">${escHtml(c.name)}</option>`;
        });
        $('#task-filter-client').html(clientOpts);

        let assigneeOpts = '<option value="">All Assignees</option>';
        coraTaskState.teamMembers.forEach(m => {
            assigneeOpts += `<option value="${m.id}">${escHtml(m.name)} (${escHtml(m.role)})</option>`;
        });
        $('#task-filter-assignee').html(assigneeOpts);

        // Drawer dropdowns
        let bookingSelectOpts = '<option value="">General Project / Unlinked</option>';
        coraTaskState.bookings.forEach(b => {
            bookingSelectOpts += `<option value="${b.id}">${escHtml(b.client_name || 'Client')} — ${escHtml(b.title)}</option>`;
        });
        $('#create-task-booking, #template-target-booking, #detail-task-booking').html(bookingSelectOpts);

        let staffSelectOpts = '<option value="">Unassigned</option>';
        coraTaskState.teamMembers.forEach(m => {
            staffSelectOpts += `<option value="${m.id}">${escHtml(m.name)} (${escHtml(m.role)})</option>`;
        });
        $('#create-task-assignee, #detail-task-assignee').html(staffSelectOpts);
    }

    function getFilteredTasks() {
        const query = ($('#task-search-input').val() || '').toLowerCase();
        const clientVal = $('#task-filter-client').val();
        const catVal = $('#task-filter-category').val();
        const assigneeVal = $('#task-filter-assignee').val();

        return coraTaskState.tasks.filter(t => {
            if (query && !t.title.toLowerCase().includes(query) && !(t.client_name || '').toLowerCase().includes(query)) return false;
            if (clientVal && String(t.client_id) !== String(clientVal)) return false;
            if (catVal && t.deliverable_type !== catVal) return false;
            if (assigneeVal && String(t.assignee_id) !== String(assigneeVal)) return false;
            return true;
        });
    }

    window.coraRenderTaskViews = function() {
        const tasks = getFilteredTasks();
        updateKPICounters(tasks);
        renderKanbanColumns(tasks);
        renderMatrixProjects(tasks);
        renderRosterTeam(tasks);
    };

    function updateKPICounters(tasks) {
        const active = tasks.filter(t => t.status !== 'done').length;
        const pendingDeliv = tasks.filter(t => t.deliverable_type && t.status !== 'done').length;
        
        const todayStr = new Date().toISOString().split('T')[0];
        const overdue = tasks.filter(t => t.due_date && t.due_date < todayStr && t.status !== 'done').length;
        const completed = tasks.filter(t => t.status === 'done').length;
        
        const assignedStaff = new Set(tasks.map(t => t.assignee_id).filter(Boolean)).size;
        const capacity = Math.min(100, Math.round((assignedStaff / (coraTaskState.teamMembers.length || 1)) * 100));

        $('#kpi-total-active').text(active);
        $('#kpi-pending-deliv').text(pendingDeliv);
        $('#kpi-overdue-count').text(overdue);
        $('#kpi-completed-count').text(completed);
        $('#kpi-team-capacity').text(capacity + '%');
    }

    function renderKanbanColumns(tasks) {
        const cols = {
            'todo': '#kanban-todo',
            'in_progress': '#kanban-inprogress',
            'client_review': '#kanban-review',
            'blocked': '#kanban-blocked',
            'done': '#kanban-done'
        };

        const todayStr = new Date().toISOString().split('T')[0];

        Object.keys(cols).forEach(statusKey => {
            const colTasks = tasks.filter(t => (t.status || 'todo') === statusKey);
            $(`#count-kanban-${statusKey}`).text(colTasks.length);

            let html = '';
            if (colTasks.length === 0) {
                html = '<div class="text-center text-zinc-400 text-xs py-12 font-medium">No tasks in this stage</div>';
            } else {
                colTasks.forEach(t => {
                    const subtasks = t.subtasks || [];
                    const doneSub = subtasks.filter(s => s.completed).length;

                    // Color Psychology Card Border & Accent Styling
                    let cardBorderClass = 'border-l-4 border-l-zinc-300 border-zinc-200/80 bg-white';

                    if (t.status === 'in_progress') {
                        cardBorderClass = 'border-l-4 border-l-amber-500 border-zinc-200/80 bg-white';
                    } else if (t.status === 'client_review') {
                        cardBorderClass = 'border-l-4 border-l-blue-500 border-zinc-200/80 bg-white';
                    } else if (t.status === 'blocked') {
                        cardBorderClass = 'border-l-4 border-l-rose-500 border-rose-200/80 bg-rose-50/20';
                    } else if (t.status === 'done') {
                        cardBorderClass = 'border-l-4 border-l-emerald-500 border-zinc-200/80 bg-white';
                    }

                    // Urgency calculation
                    let urgencyBadge = '';
                    if (t.due_date && t.status !== 'done') {
                        if (t.due_date < todayStr) {
                            urgencyBadge = '<span class="text-[9.5px] font-extrabold text-rose-700 bg-rose-100 border border-rose-200 px-2 py-0.5 rounded-md uppercase tracking-wider">OVERDUE</span>';
                        } else if (t.due_date === todayStr) {
                            urgencyBadge = '<span class="text-[9.5px] font-extrabold text-amber-800 bg-amber-100 border border-amber-200 px-2 py-0.5 rounded-md uppercase tracking-wider">DUE TODAY</span>';
                        } else {
                            urgencyBadge = `<span class="text-[9.5px] font-bold text-zinc-600 bg-zinc-100 border border-zinc-200/80 px-2 py-0.5 rounded-md">Due ${t.due_date}</span>`;
                        }
                    }

                    // Next Status Action Button
                    let nextActionBtn = '';
                    if (t.status === 'todo') {
                        nextActionBtn = `<button onclick="coraQuickMoveTask(event, '${t.id}', 'in_progress')" class="text-[10px] font-bold text-amber-700 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-2 py-1 rounded-md transition-colors cursor-pointer">Start &rarr;</button>`;
                    } else if (t.status === 'in_progress') {
                        nextActionBtn = `<button onclick="coraQuickMoveTask(event, '${t.id}', 'client_review')" class="text-[10px] font-bold text-blue-700 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2 py-1 rounded-md transition-colors cursor-pointer">Review &rarr;</button>`;
                    } else if (t.status === 'client_review') {
                        nextActionBtn = `<button onclick="coraQuickMoveTask(event, '${t.id}', 'done')" class="text-[10px] font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-1 rounded-md transition-colors cursor-pointer">&check; Complete</button>`;
                    }

                    html += `
                        <div class="p-4.5 rounded-2xl border shadow-2xs hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 cursor-pointer group space-y-3 ${cardBorderClass}" onclick="coraOpenTaskDetailsDrawer(event, '${t.id}')">
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-700 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200/80 truncate max-w-[140px]">${escHtml(t.client_name || 'Studio General')}</span>
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 ${t.priority === 'urgent' || t.priority === 'high' ? 'bg-rose-500' : 'bg-amber-400'}" title="Priority: ${t.priority}"></span>
                            </div>

                            <h4 class="text-xs font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors leading-snug ${t.status === 'done' ? 'line-through text-zinc-400' : ''}">${escHtml(t.title)}</h4>
                            
                            ${t.booking_title ? `
                                <div class="text-[10.5px] text-zinc-600 bg-zinc-50 px-2.5 py-1 rounded-md border border-zinc-200/60 font-medium truncate">
                                    Shoot: ${escHtml(t.booking_title)}
                                </div>
                            ` : ''}

                            <div class="flex items-center justify-between pt-0.5">
                                <span class="text-[10px] font-bold text-zinc-500 bg-zinc-100/70 px-2 py-0.5 rounded-md border border-zinc-200/60">${escHtml(t.deliverable_type || 'General')}</span>
                                ${urgencyBadge}
                            </div>

                            ${subtasks.length > 0 ? `
                                <div class="space-y-1 pt-1">
                                    <div class="flex justify-between items-center text-[10px] font-bold text-zinc-500">
                                        <span>Checklist</span>
                                        <span>${doneSub}/${subtasks.length} (${Math.round((doneSub/subtasks.length)*100)}%)</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-zinc-950 h-1.5 rounded-full transition-all" style="width:${Math.round((doneSub/subtasks.length)*100)}%"></div>
                                    </div>
                                </div>
                            ` : ''}

                            <div class="flex items-center justify-between pt-2.5 border-t border-zinc-100">
                                <div class="flex items-center gap-1.5 truncate">
                                    <div class="w-5.5 h-5.5 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[9px] font-extrabold shrink-0">${(t.assignee_name || 'A')[0].toUpperCase()}</div>
                                    <span class="text-[11px] text-zinc-700 font-bold truncate">${escHtml(t.assignee_name || 'Unassigned')}</span>
                                </div>
                                ${nextActionBtn}
                            </div>
                        </div>
                    `;
                });
            }
            $(cols[statusKey]).html(html);
        });
    }

    window.coraQuickMoveTask = function(event, taskId, newStatus) {
        event.stopPropagation();
        const t = coraTaskState.tasks.find(x => String(x.id) === String(taskId));
        if (!t) return;
        t.status = newStatus;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_client_task',
            nonce: coraREData.ajaxNonce,
            task: JSON.stringify(t)
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast(`Moved task to ${newStatus.replace('_', ' ').toUpperCase()}`);
                coraTaskState.tasks = res.data.tasks || coraTaskState.tasks;
                coraRenderTaskViews();
            }
        });
    };

    function renderMatrixProjects(tasks) {
        const grouped = {};
        tasks.forEach(t => {
            const key = t.client_name || 'General Studio Projects';
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(t);
        });

        let html = '';
        Object.keys(grouped).forEach(clientName => {
            const projTasks = grouped[clientName];
            const completed = projTasks.filter(t => t.status === 'done').length;
            const pct = Math.round((completed / projTasks.length) * 100);

            html += `
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-base font-bold text-zinc-950 mb-0.5">${escHtml(clientName)}</h3>
                            <p class="text-xs text-zinc-500 font-medium">${projTasks.length} active deliverable tasks</p>
                        </div>
                        <button onclick="coraOpenCreateTaskDrawer(event)" class="text-xs font-bold text-zinc-900 bg-zinc-100 hover:bg-zinc-200 px-3.5 py-2 rounded-xl transition-colors cursor-pointer">+ Add Task</button>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 bg-zinc-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-zinc-950 h-2 rounded-full transition-all" style="width: ${pct}%"></div>
                        </div>
                        <span class="text-xs font-bold text-zinc-700">${pct}% Complete</span>
                    </div>
                    <div class="space-y-2 pt-1">
                        ${projTasks.map(t => `
                            <div onclick="coraOpenTaskDetailsDrawer(event, '${t.id}')" class="flex items-center gap-3 p-3 bg-zinc-50 hover:bg-zinc-100/80 border border-zinc-200/60 rounded-xl cursor-pointer transition-all">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 ${t.status === 'done' ? 'bg-emerald-500' : 'bg-amber-500'}"></span>
                                <span class="flex-1 text-xs font-bold ${t.status === 'done' ? 'text-zinc-400 line-through' : 'text-zinc-900'} truncate">${escHtml(t.title)}</span>
                                <div class="w-5.5 h-5.5 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[9px] font-bold shrink-0">${(t.assignee_name || 'A')[0].toUpperCase()}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        });
        $('#panel-view-matrix').html(html || '<div class="col-span-2 text-center text-zinc-400 py-12 font-medium">No client projects found</div>');
    }

    function renderRosterTeam(tasks) {
        const staffMap = {};
        coraTaskState.teamMembers.forEach(m => {
            staffMap[m.id] = { name: m.name, role: m.role, tasks: [] };
        });

        tasks.forEach(t => {
            if (t.assignee_id && staffMap[t.assignee_id]) {
                staffMap[t.assignee_id].tasks.push(t);
            }
        });

        let html = '';
        Object.values(staffMap).forEach(s => {
            const active = s.tasks.filter(t => t.status !== 'done').length;

            html += `
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-zinc-950 text-white flex items-center justify-center text-sm font-bold shadow-2xs">${s.name[0].toUpperCase()}</div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-950">${escHtml(s.name)}</h3>
                            <p class="text-xs text-zinc-500 font-medium">${escHtml(s.role)}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 rounded-xl border border-zinc-200/60">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase text-zinc-500 font-bold">Active Queue</span>
                            <span class="text-base font-bold text-zinc-950">${active} Tasks</span>
                        </div>
                        <div class="flex flex-col text-right">
                            <span class="text-[10px] uppercase text-zinc-500 font-bold">Workload</span>
                            <span class="text-xs font-bold ${active > 3 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200'} px-2 py-0.5 rounded-md border">${active > 3 ? 'High Capacity' : 'Optimal'}</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-xs font-bold text-zinc-950">Assigned Deliverables</div>
                        ${s.tasks.length === 0 ? '<p class="text-xs text-zinc-400 italic py-2">No active tasks assigned</p>' : s.tasks.map(t => `
                            <div onclick="coraOpenTaskDetailsDrawer(event, '${t.id}')" class="p-3 border border-zinc-200/60 rounded-xl text-xs text-zinc-800 hover:bg-zinc-50 cursor-pointer font-bold transition-colors truncate">${escHtml(t.title)}</div>
                        `).join('')}
                    </div>
                </div>
            `;
        });
        $('#panel-view-roster').html(html);
    }

    // Open Task Details Bottom Drawer
    window.coraOpenTaskDetailsDrawer = function(event, taskId) {
        const t = coraTaskState.tasks.find(x => String(x.id) === String(taskId));
        if (!t) return;

        $('#detail-task-id').val(t.id);
        $('#detail-task-title').val(t.title);
        $('#detail-task-assignee').val(t.assignee_id || '');
        $('#detail-task-status').val(t.status || 'todo');
        $('#detail-task-due').val(t.due_date || '');
        $('#detail-task-priority').val(t.priority || 'medium');
        $('#detail-task-asset-url').val(t.asset_url || '');

        let badges = '';
        if (t.client_name) {
            badges += `<span class="px-3 py-1 text-xs font-bold bg-zinc-100 text-zinc-800 rounded-lg border border-zinc-200">Client: ${escHtml(t.client_name)}</span>`;
        }
        if (t.booking_title) {
            badges += `<span class="px-3 py-1 text-xs font-bold bg-zinc-950 text-white rounded-lg">Shoot: ${escHtml(t.booking_title)}</span>`;
        }
        $('#detail-project-badges').html(badges);

        coraTaskState.currentTaskSubtasks = (t.subtasks || []).map(s => ({ ...s }));
        coraTaskState.currentTaskComments = (t.comments || []).map(c => ({ ...c }));

        renderDetailSubtasks();
        renderDetailComments();

        coraOpenDrawer(event, 'drawer-task-details');
    };

    function renderDetailSubtasks() {
        const subtasks = coraTaskState.currentTaskSubtasks;
        $('#detail-subtasks-count').text(`${subtasks.length} steps`);

        let html = '';
        if (subtasks.length === 0) {
            html = '<p class="text-xs text-zinc-400 italic py-1">No checklist steps added yet.</p>';
        } else {
            subtasks.forEach((s, idx) => {
                html += `
                    <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-zinc-200/80 text-xs">
                        <label class="flex items-center gap-2.5 cursor-pointer min-w-0 flex-1">
                            <input type="checkbox" ${s.completed ? 'checked' : ''} onchange="coraToggleDetailSubtask(${idx})" class="w-4 h-4 rounded text-zinc-950 focus:ring-0 cursor-pointer">
                            <span class="font-bold text-zinc-800 truncate ${s.completed ? 'line-through text-zinc-400' : ''}">${escHtml(s.text)}</span>
                        </label>
                        <button type="button" onclick="coraRemoveDetailSubtask(${idx})" class="text-zinc-400 hover:text-red-600 transition-colors p-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                `;
            });
        }
        $('#detail-subtasks-list').html(html);
    }

    window.coraAddDetailSubtask = function() {
        const txt = $('#detail-new-subtask-input').val().trim();
        if (!txt) return;
        coraTaskState.currentTaskSubtasks.push({
            id: Date.now(),
            text: txt,
            completed: false
        });
        $('#detail-new-subtask-input').val('');
        renderDetailSubtasks();
    };

    window.coraToggleDetailSubtask = function(idx) {
        coraTaskState.currentTaskSubtasks[idx].completed = !coraTaskState.currentTaskSubtasks[idx].completed;
        renderDetailSubtasks();
    };

    window.coraRemoveDetailSubtask = function(idx) {
        coraTaskState.currentTaskSubtasks.splice(idx, 1);
        renderDetailSubtasks();
    };

    function renderDetailComments() {
        const comments = coraTaskState.currentTaskComments;
        let html = '';
        if (comments.length === 0) {
            html = '<p class="text-xs text-zinc-400 italic py-1">No notes or comments posted yet.</p>';
        } else {
            comments.forEach(c => {
                html += `
                    <div class="p-3.5 bg-white rounded-xl border border-zinc-200/80 text-xs space-y-1">
                        <div class="flex items-center justify-between text-[10px] text-zinc-500 font-bold">
                            <span>${escHtml(c.author || 'Team Member')}</span>
                            <span>${escHtml(c.time || 'Just now')}</span>
                        </div>
                        <p class="text-zinc-800 font-medium leading-relaxed">${escHtml(c.text)}</p>
                    </div>
                `;
            });
        }
        $('#detail-comments-list').html(html);
    }

    window.coraPostTaskComment = function() {
        const txt = $('#detail-comment-input').val().trim();
        if (!txt) return;
        coraTaskState.currentTaskComments.push({
            author: 'Studio Admin',
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            text: txt
        });
        $('#detail-comment-input').val('');
        renderDetailComments();
        window.coraShowToast("Note posted to task activity log.");
    };

    window.coraTriggerEmailReminder = function() {
        const taskId = $('#detail-task-id').val();
        $.post(coraREData.ajaxUrl, {
            action: 'cora_send_task_email_notification',
            nonce: coraREData.ajaxNonce,
            task_id: taskId
        }, function(res) {
            window.coraShowToast("Instant HTML Email Alert dispatched to assignee!");
        });
    };

    window.coraSaveTaskFromDrawer = function() {
        const taskId = $('#detail-task-id').val();
        const t = coraTaskState.tasks.find(x => String(x.id) === String(taskId));
        if (!t) return;

        t.title = $('#detail-task-title').val().trim();
        t.assignee_id = $('#detail-task-assignee').val();
        const assigneeObj = coraTaskState.teamMembers.find(m => String(m.id) === String(t.assignee_id));
        t.assignee_name = assigneeObj ? assigneeObj.name : 'Unassigned';
        t.status = $('#detail-task-status').val();
        t.due_date = $('#detail-task-due').val();
        t.priority = $('#detail-task-priority').val();
        t.asset_url = $('#detail-task-asset-url').val();
        t.subtasks = coraTaskState.currentTaskSubtasks;
        t.comments = coraTaskState.currentTaskComments;

        const sendEmail = $('#detail-email-notify').is(':checked');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_client_task',
            nonce: coraREData.ajaxNonce,
            task: JSON.stringify(t),
            notify_email: sendEmail ? '1' : '0'
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast("Task changes saved and synced!");
                coraTaskState.tasks = res.data.tasks || coraTaskState.tasks;
                coraRenderTaskViews();
                window.coraCloseAllDrawers();
            }
        });
    };

    window.coraDeleteTaskFromDrawer = function() {
        const taskId = $('#detail-task-id').val();
        if (!taskId) return;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_delete_client_task',
            nonce: coraREData.ajaxNonce,
            task_id: taskId
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast("Task deleted.");
                coraTaskState.tasks = res.data.tasks || [];
                coraRenderTaskViews();
                window.coraCloseAllDrawers();
            }
        });
    };

    window.coraSaveTask = function() {
        const title = $('#create-task-title').val().trim();
        if (!title) {
            window.coraShowToast("Please enter a task title.");
            return;
        }

        const bookingId = $('#create-task-booking').val();
        const bookingObj = coraTaskState.bookings.find(b => String(b.id) === String(bookingId));
        const assigneeId = $('#create-task-assignee').val();
        const assigneeObj = coraTaskState.teamMembers.find(m => String(m.id) === String(assigneeId));

        const taskData = {
            id: Date.now(),
            title: title,
            client_id: bookingObj ? bookingObj.client_id : '',
            client_name: bookingObj ? bookingObj.client_name : '',
            booking_id: bookingId,
            booking_title: bookingObj ? bookingObj.title : '',
            assignee_id: assigneeId,
            assignee_name: assigneeObj ? assigneeObj.name : 'Unassigned',
            deliverable_type: $('#create-task-category').val(),
            priority: $('#create-task-priority').val() || 'medium',
            status: 'todo',
            due_date: $('#create-task-due').val(),
            desc: $('#create-task-desc').val(),
            subtasks: [],
            comments: []
        };

        const sendEmail = $('#create-task-email-notify').is(':checked');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_client_task',
            nonce: coraREData.ajaxNonce,
            task: JSON.stringify(taskData),
            notify_email: sendEmail ? '1' : '0'
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast("Client task created & dispatched!");
                coraTaskState.tasks = res.data.tasks || [];
                coraRenderTaskViews();
                window.coraCloseAllDrawers();
                $('#create-task-title').val('');
                $('#create-task-desc').val('');
            }
        });
    };

    window.coraApplyTemplate = function() {
        const bookingId = $('#template-target-booking').val();
        const bookingObj = coraTaskState.bookings.find(b => String(b.id) === String(bookingId));
        const tplKey = $('input[name="workflow_tpl"]:checked').val() || 'wedding_photo';

        $.post(coraREData.ajaxUrl, {
            action: 'cora_apply_task_template',
            nonce: coraREData.ajaxNonce,
            template_key: tplKey,
            booking_id: bookingId,
            booking_title: bookingObj ? bookingObj.title : '',
            client_id: bookingObj ? bookingObj.client_id : '',
            client_name: bookingObj ? bookingObj.client_name : ''
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast(`Workflow template applied! Created ${res.data.count} linked tasks.`);
                coraTaskState.tasks = res.data.tasks || [];
                coraRenderTaskViews();
                window.coraCloseAllDrawers();
            }
        });
    };

    window.coraFilterTasks = function() {
        coraRenderTaskViews();
    };

    function escHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Initialize on ready
    $(document).ready(function() {
        window.coraLoadClientTasks();
    });
})();
</script>
