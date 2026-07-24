<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div id="cora-forms-module" class="w-full h-full flex flex-col gap-6" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <!-- STATE 1: FORMS LIST VIEW -->
    <div id="forms-list-state" class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-zinc-200/60 pb-5">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-zinc-900">Cora Forms</h1>
                <p class="text-xs text-zinc-500 mt-1">Design and share Notion-style interactive forms. Automatically collect leads into your CRM database.</p>
            </div>
            <button id="btn-create-form" class="h-9 px-4 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-medium transition-all flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Create form
            </button>
        </div>

        <!-- Sub-page Tab Bar -->
        <div class="flex items-center gap-1 border-b border-zinc-200/60 pb-px mb-4">
            <button id="tab-forms-list" class="cora-forms-tab px-4 py-2.5 text-xs font-semibold border-b-2 border-zinc-950 text-zinc-950 -mb-px transition-all border-none bg-transparent cursor-pointer">
                Forms List
            </button>
            <button id="tab-funnel-analytics" class="cora-forms-tab px-4 py-2.5 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 -mb-px transition-all border-none bg-transparent cursor-pointer">
                Funnel Analytics
            </button>
            <button id="tab-clauses-library" class="cora-forms-tab px-4 py-2.5 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 -mb-px transition-all border-none bg-transparent cursor-pointer">
                Clause Library
            </button>
            <button id="tab-audit-logs" class="cora-forms-tab px-4 py-2.5 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 -mb-px transition-all border-none bg-transparent cursor-pointer">
                Compliance Audit Log
            </button>
        </div>

        <!-- TAB CONTENT: FORMS LIST -->
        <div id="forms-list-tab-content" class="flex flex-col gap-6">
            <!-- Metrics Dashboard Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border border-zinc-200/80 rounded-xl p-4 flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Forms</span>
                    <span id="metric-total-forms" class="text-2xl font-bold text-zinc-900">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-4 flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Views</span>
                    <span id="metric-total-views" class="text-2xl font-bold text-zinc-900">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-4 flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Submissions</span>
                    <span id="metric-total-submissions" class="text-2xl font-bold text-zinc-900">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-4 flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Completion Rate</span>
                    <span id="metric-completion-rate" class="text-2xl font-bold text-zinc-900">0%</span>
                </div>
            </div>

        <!-- Table Container -->
        <div class="bg-white border border-zinc-200/80 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-200/80">
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Form Title</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Status</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Responses</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Created At</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="forms-list-body" class="divide-y divide-zinc-100">
                        <!-- Loading placeholder / Dynamic rows injection -->
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-xs text-zinc-400">Loading forms list...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB CONTENT: ADVANCED FUNNEL ANALYTICS -->
        <div id="forms-funnel-tab-content" class="hidden flex-col gap-6">
            <!-- Header Controls -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200/60 pb-5">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950">Conversion Funnel & Drop-off Diagnostics</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5">Analyze user friction, abandonment rates, and field-level drop-offs.</p>
                </div>
                <!-- Form Selector Dropdown -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-zinc-500">Select Form:</span>
                    <select id="funnel-form-selector" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-700 outline-none focus:border-zinc-300 w-56 cursor-pointer">
                        <option value="all">All Forms (Aggregate)</option>
                    </select>
                </div>
            </div>

            <!-- Funnel Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border border-zinc-200/80 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Total Form Views</span>
                    <span id="funnel-metric-views" class="text-2xl font-bold text-zinc-900">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Started Filling</span>
                    <span id="funnel-metric-started" class="text-2xl font-bold text-zinc-900">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Submissions</span>
                    <span id="funnel-metric-completed" class="text-2xl font-bold text-zinc-900">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Abandonment Rate</span>
                    <span id="funnel-metric-abandonment" class="text-2xl font-bold text-zinc-900">0%</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <!-- Left 2 Cols: Funnel Steps -->
                <div class="lg:col-span-2 bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm flex flex-col gap-5">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wide">Conversion Funnel Visualizer</h4>
                        <p class="text-[10px] text-zinc-450 mt-0.5">Stages from initial landing to successful submission.</p>
                    </div>
                    
                    <div class="space-y-6 py-2">
                        <!-- Funnel Item 1 -->
                        <div class="flex items-center gap-4">
                            <div class="w-24 shrink-0 text-xs font-bold text-zinc-500 uppercase">1. Views</div>
                            <div class="flex-1">
                                <div class="flex justify-between text-[11px] font-semibold text-zinc-700 mb-1">
                                    <span id="funnel-views-count">0</span>
                                    <span>100%</span>
                                </div>
                                <div class="h-6 w-full bg-zinc-100 rounded-lg overflow-hidden border border-zinc-200/30">
                                    <div id="funnel-views-progress" class="h-full bg-zinc-900 flex items-center pl-3 text-[10px] font-bold text-white transition-all duration-500" style="width: 100%">100%</div>
                                </div>
                            </div>
                        </div>

                        <!-- Visual Connector 1 -->
                        <div class="flex items-center gap-4 -my-4">
                            <div class="w-24 shrink-0 flex justify-center">
                                <div class="h-8 border-l-2 border-dotted border-zinc-350"></div>
                            </div>
                            <div class="flex-1 flex items-center pl-4">
                                <span id="funnel-loss-1" class="px-2 py-0.5 rounded bg-zinc-50 text-zinc-500 text-[9px] font-bold border border-zinc-200/80 flex items-center gap-0.5 transition-all">
                                </span>
                            </div>
                        </div>

                        <!-- Funnel Item 2 -->
                        <div class="flex items-center gap-4">
                            <div class="w-24 shrink-0 text-xs font-bold text-zinc-500 uppercase">2. Started</div>
                            <div class="flex-1">
                                <div class="flex justify-between text-[11px] font-semibold text-zinc-700 mb-1">
                                    <span id="funnel-started-count">0</span>
                                    <span id="funnel-started-pct">0%</span>
                                </div>
                                <div class="h-6 w-full bg-zinc-100 rounded-lg overflow-hidden border border-zinc-200/30">
                                    <div id="funnel-started-progress" class="h-full bg-zinc-650 flex items-center pl-3 text-[10px] font-bold text-white transition-all duration-500" style="width: 0%">0%</div>
                                </div>
                            </div>
                        </div>

                        <!-- Visual Connector 2 -->
                        <div class="flex items-center gap-4 -my-4">
                            <div class="w-24 shrink-0 flex justify-center">
                                <div class="h-8 border-l-2 border-dotted border-zinc-350"></div>
                            </div>
                            <div class="flex-1 flex items-center pl-4">
                                <span id="funnel-loss-2" class="px-2 py-0.5 rounded bg-zinc-50 text-zinc-500 text-[9px] font-bold border border-zinc-200/80 flex items-center gap-0.5 transition-all">
                                </span>
                            </div>
                        </div>

                        <!-- Funnel Item 3 -->
                        <div class="flex items-center gap-4">
                            <div class="w-24 shrink-0 text-xs font-bold text-zinc-500 uppercase">3. Submitted</div>
                            <div class="flex-1">
                                <div class="flex justify-between text-[11px] font-semibold text-zinc-700 mb-1">
                                    <span id="funnel-completed-count">0</span>
                                    <span id="funnel-completed-pct">0%</span>
                                </div>
                                <div class="h-6 w-full bg-zinc-100 rounded-lg overflow-hidden border border-zinc-200/30">
                                    <div id="funnel-completed-progress" class="h-full bg-zinc-400 flex items-center pl-3 text-[10px] font-bold text-white transition-all duration-500" style="width: 0%">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right 1 Col: Field Level Abandonment -->
                <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm flex flex-col gap-4">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wide">Friction Analysis</h4>
                        <p class="text-[10px] text-zinc-450 mt-0.5">Which specific fields lead to abandonment? (Lower fill rates represent higher friction).</p>
                    </div>
                    
                    <div id="funnel-friction-list" class="space-y-4 max-h-80 overflow-y-auto pr-1">
                        <!-- Dynamic field friction bars go here -->
                        <div class="text-[10px] text-zinc-400 text-center py-4">Select a specific form to view field friction analytics.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: CLAUSE LIBRARY -->
        <div id="forms-clauses-tab-content" class="hidden flex-col gap-6">
            <div class="flex items-center justify-between border-b border-zinc-200/60 pb-5">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950">Clause Library & Automation Templates</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5">Manage legal clauses and conditional text blocks to compile contract PDFs.</p>
                </div>
                <button id="btn-create-clause" class="h-8 px-3 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-[10px] font-medium transition-all flex items-center gap-1.5 cursor-pointer">
                    <span>+</span> Add Clause
                </button>
            </div>
            
            <div class="bg-white border border-zinc-200/80 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-200/80">
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Key / Identifier</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Clause Title</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Snippet Content</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="clauses-list-body" class="divide-y divide-zinc-100 text-xs">
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-zinc-400">No clauses created yet. Click "+ Add Clause" to start.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB CONTENT: COMPLIANCE AUDIT LOG -->
        <div id="forms-audit-tab-content" class="hidden flex-col gap-6">
            <div class="flex items-center justify-between border-b border-zinc-200/60 pb-5">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950">GDPR Compliance & Field Audit Trail</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5">Immutable record of data reads, exports, and verification checksum checks.</p>
                </div>
            </div>
            
            <div class="bg-white border border-zinc-200/80 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-200/80">
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Timestamp</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">User / Reviewer</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Action Type</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">Accessed / Target</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-zinc-500">IP Address</th>
                        </tr>
                    </thead>
                    <tbody id="audit-logs-body" class="divide-y divide-zinc-100 text-xs">
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-zinc-400">Loading audit log list...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- STATE 2: FULL-PAGE INTERACTIVE FORM BUILDER VIEW -->
    <div id="form-editor-state" class="hidden flex-col h-[calc(100vh-85px)] min-h-[600px] border-0 rounded-none bg-white dark:bg-zinc-950 overflow-hidden font-sans">
        <!-- TOP TOOLBAR HEADER -->
        <div class="px-5 py-3 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between gap-4 shrink-0 bg-white dark:bg-zinc-950">
            <!-- Left: Back & Title -->
            <div class="flex items-center gap-3 min-w-0">
                <button id="btn-back-to-list" class="h-8 w-8 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                </button>
                <div class="flex items-center gap-2.5 min-w-0">
                    <input id="editor-form-title" type="text" placeholder="Untitled Form" value="Security & AI E2E Form 5450" class="text-sm font-bold text-zinc-950 dark:text-zinc-50 bg-transparent border-b border-transparent hover:border-zinc-200 focus:border-zinc-400 outline-none p-0.5 truncate w-48 md:w-72" />
                    <span class="text-zinc-400 text-xs">✎</span>
                    <span id="editor-save-status" class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 text-[10px] font-bold flex items-center gap-1 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Saved
                    </span>
                </div>
            </div>

            <!-- Center: Viewport Switcher & History Controls -->
            <div class="hidden md:flex items-center gap-3">
                <div class="flex items-center p-0.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400">
                    <button class="h-7 px-2.5 rounded-md text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-950 dark:text-zinc-50 shadow-2xs flex items-center gap-1 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    </button>
                    <button class="h-7 px-2.5 rounded-md text-xs font-semibold hover:text-zinc-900 flex items-center gap-1 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                    </button>
                    <button class="h-7 px-2.5 rounded-md text-xs font-semibold hover:text-zinc-900 flex items-center gap-1 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                    </button>
                </div>
                <div class="flex items-center gap-1 text-zinc-400">
                    <button class="h-7 w-7 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                    </button>
                    <button class="h-7 w-7 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.13-9.36L23 10"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Right: Publish & Status Controls -->
            <div class="flex items-center gap-2 shrink-0">
                <button id="btn-editor-view-live" class="hidden h-8 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition-all items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    View Form
                </button>
                <button id="btn-editor-share" class="hidden h-8 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition-all items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                    Share
                </button>
                <select id="editor-form-status" class="h-8 px-2.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-semibold text-zinc-700 dark:text-zinc-300 outline-none">
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="closed">Closed</option>
                </select>
                <button id="btn-save-form" class="h-8 px-4 rounded-lg bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-xs">
                    Publish Form
                </button>
            </div>
        </div>

        <!-- 2-COLUMN WORKSPACE BODY -->
        <div class="flex-1 flex overflow-hidden min-h-0">

            <!-- COLUMN 1: UNIFIED DYNAMIC LEFT SIDEBAR -->
            <div id="editor-left-panel" class="w-[320px] shrink-0 border-r border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-950 flex flex-col font-sans transition-all duration-300 ease-in-out" style="width:320px;">
                <!-- Top Header Tabs -->
                <div class="px-3 py-2.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center gap-1 bg-white dark:bg-zinc-900 shrink-0">
                    <div id="left-panel-tabs" class="flex-1 flex items-center gap-1 overflow-x-auto no-scrollbar">
                        <button id="btn-left-tab-fields" class="py-1.5 px-2 rounded-md text-xs font-bold text-zinc-950 dark:text-zinc-50 border-b-2 border-zinc-950 dark:border-zinc-50 whitespace-nowrap cursor-pointer">Add Fields</button>
                        <button id="btn-left-tab-settings" class="py-1.5 px-2 rounded-md text-xs font-semibold text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-b-2 border-transparent whitespace-nowrap cursor-pointer">Field Settings</button>
                        <button id="btn-left-tab-form" class="py-1.5 px-2 rounded-md text-xs font-semibold text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-b-2 border-transparent whitespace-nowrap cursor-pointer">Form Settings</button>
                        <button id="btn-left-tab-integ" class="py-1.5 px-2 rounded-md text-xs font-semibold text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-b-2 border-transparent whitespace-nowrap cursor-pointer">Integrations</button>
                    </div>
                </div>

                <!-- 1. #left-tab-content-fields: Add Fields Palette -->
                <div id="left-tab-fields" class="flex-1 flex flex-col overflow-hidden">
                    <!-- Palette Search -->
                    <div id="left-panel-search" class="p-3 pb-2">
                        <div class="relative">
                            <input id="palette-search-input" type="text" placeholder="Search fields..." class="h-8 pl-8 pr-8 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none w-full" />
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400 pointer-events-none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                    </div>

                    <!-- Component Palette Sections -->
                    <div id="left-panel-content" class="flex-1 overflow-y-auto px-3 py-2 space-y-5">
                        <!-- BASIC FIELDS -->
                        <div>
                            <div class="text-[9.5px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Basic Fields</div>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="text">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg>
                                    Short Text
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="long_text">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="12" x2="3" y2="12"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                                    Long Text
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="email">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    Email
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="phone">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.59 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.78a16 16 0 0 0 6.29 6.29l1.13-.93a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    Phone
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="number">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                                    Number
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="dropdown">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    Dropdown
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="multiple_choice">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg>
                                    Multiple Choice
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="checkbox">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                    Checkboxes
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="date">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    Date
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="file">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    File Upload
                                </button>
                            </div>
                        </div>

                        <!-- ADVANCED FIELDS -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[9.5px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Advanced Fields</span>
                                <span class="px-1.5 py-0.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 text-[8px] font-bold rounded font-mono uppercase">PRO</span>
                            </div>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="signature">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    Signature
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="rating">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                    Rating
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="slider">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                                    Slider
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="payment">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                    Stripe Payment
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="rich_text">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    Rich Text
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="matrix">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                                    Matrix Field
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="repeatable">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                    Repeatable List
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="hidden">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                    Hidden Field
                                </button>
                            </div>
                        </div>

                        <!-- LAYOUT ELEMENTS -->
                        <div>
                            <div class="text-[9.5px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Layout Elements</div>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-pointer text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="header">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M6 12h12"></path><path d="M6 4h18"></path><path d="M6 20h18"></path></svg>
                                    Heading
                                </button>
                                <button class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-pointer text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="columns">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="3" y="3" width="8" height="18" rx="1"></rect><rect x="13" y="3" width="8" height="18" rx="1"></rect></svg>
                                    Columns
                                </button>
                                <button class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-pointer text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="divider">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    Divider
                                </button>
                                <button class="p-2 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-pointer text-[11px] font-medium text-zinc-800 dark:text-zinc-200 transition-all" data-add-type="spacer">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polyline points="5 9 2 12 5 15"></polyline><polyline points="9 5 12 2 15 5"></polyline><polyline points="15 19 12 22 9 19"></polyline><polyline points="19 9 22 12 19 15"></polyline><line x1="2" y1="12" x2="22" y2="12"></line><line x1="12" y1="2" x2="12" y2="22"></line></svg>
                                    Spacer
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="left-panel-footer" class="p-3 border-t border-zinc-200/80 dark:border-zinc-800 shrink-0">
                        <button class="w-full h-8 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 hover:border-zinc-500 text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer transition-all">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Add Custom Field
                        </button>
                    </div>
                </div>

                <!-- 2. #left-tab-content-settings: Dynamic Inspector for Selected Field -->
                <div id="left-tab-settings" class="hidden flex-1 overflow-y-auto p-4 space-y-4">
                    <!-- Selected Field Banner -->
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-base" id="inspector-field-icon">💳</span>
                            <div>
                                <h4 class="text-xs font-bold text-zinc-950 dark:text-zinc-50" id="inspector-field-type-title">Field Settings</h4>
                                <span class="text-[9.5px] text-zinc-400 font-mono" id="inspector-field-id">Select a field on canvas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Label Input -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Field Label</label>
                        <input id="inspector-field-label" type="text" placeholder="Field Label" class="h-9 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-medium text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 w-full" />
                    </div>

                    <!-- Description Input -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Description</label>
                        <textarea id="inspector-field-desc" rows="2" placeholder="Add a description..." class="p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 w-full resize-none"></textarea>
                    </div>

                    <!-- Required Toggle -->
                    <div class="flex items-center justify-between py-2 border-t border-b border-zinc-100 dark:border-zinc-800">
                        <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Required Field</span>
                        <input type="checkbox" id="inspector-field-required" class="w-4 h-4 rounded accent-zinc-950 cursor-pointer" />
                    </div>

                    <!-- Choices Editor with Scores -->
                    <div class="space-y-2 pt-1" id="inspector-choices-wrapper">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Choices & Option Scores</label>
                            <button id="btn-add-choice-item" class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 hover:underline cursor-pointer border-none bg-transparent">+ Add Choice</button>
                        </div>
                        <div id="inspector-field-choices-container" class="space-y-2">
                            <!-- Dynamic choices inputs with scores injected here -->
                        </div>
                    </div>

                    <!-- AI Purpose Input -->
                    <div class="space-y-1 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AI Context & Purpose</label>
                        <textarea id="inspector-field-ai-purpose" rows="2" placeholder="Describe field purpose for AI auto-fill and validation..." class="p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 w-full resize-none"></textarea>
                    </div>

                    <!-- Price Configuration (for payment fields) -->
                    <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Price Configuration</label>
                        <div class="flex items-center gap-2">
                            <input id="inspector-price-amount" type="number" value="100" class="h-9 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-none w-full" />
                            <select class="h-9 px-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-bold text-zinc-800 dark:text-zinc-200">
                                <option>INR (₹)</option>
                                <option>USD ($)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Conditional Logic Section -->
                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Conditional Logic Rules</span>
                            <button id="btn-add-logic-rule" class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 hover:underline cursor-pointer border-none bg-transparent">+ Add Rule</button>
                        </div>
                        <div id="settings-logic-rules-container" class="space-y-2">
                            <!-- Rule cards injected here -->
                        </div>
                    </div>
                </div>

                <!-- 3. #left-tab-content-form: Form Level Settings -->
                <div id="left-tab-content-form" class="hidden flex-1 overflow-y-auto p-4 space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Form Title</label>
                        <input id="settings-form-title" type="text" placeholder="Form Title" class="h-9 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none w-full" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Subtitle / Instructions</label>
                        <input id="settings-form-subtitle" type="text" placeholder="Form Subtitle" class="h-9 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none w-full" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Cover Image URL</label>
                        <input id="settings-cover-url" type="text" placeholder="https://example.com/cover.jpg" class="h-9 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none w-full" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Confirmation / Success Message</label>
                        <textarea id="settings-success-msg" rows="3" placeholder="Thank you for submitting!" class="p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none w-full resize-none"></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Redirect URL</label>
                        <input id="settings-redirect-url" type="text" placeholder="https://example.com/thank-you" class="h-9 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none w-full" />
                    </div>
                    <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 uppercase">Approval Stages Planner</label>
                            <button id="btn-add-approval-stage" class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 hover:underline cursor-pointer border-none bg-transparent">+ Add Stage</button>
                        </div>
                        <div id="settings-approvals-container" class="space-y-2 max-h-48 overflow-y-auto"></div>
                    </div>
                    <div class="space-y-1 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Custom CSS</label>
                        <textarea id="settings-custom-css" rows="3" placeholder="/* Custom CSS styling overrides */" class="p-3 font-mono rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-[10px] text-zinc-900 dark:text-zinc-100 outline-none w-full resize-none"></textarea>
                    </div>
                    <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 uppercase">Document PDF Auto-Compiler</label>
                            <button id="btn-add-clause-rule" class="text-[9px] font-bold text-zinc-500 hover:underline cursor-pointer border-none bg-transparent">+ Add Clause</button>
                        </div>
                        <textarea id="settings-pdf-template" rows="3" placeholder="Agreement template..." class="p-2 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 w-full resize-none"></textarea>
                        <div id="pdf-clauses-rules-container" class="space-y-1"></div>
                    </div>
                </div>

                <!-- 4. #left-tab-content-integ: Integrations Settings -->
                <div id="left-tab-content-integ" class="hidden flex-1 overflow-y-auto p-4 space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Webhook Endpoint URL</label>
                        <input id="settings-webhook-url" type="text" placeholder="https://yourdomain.com/webhook" class="h-9 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none w-full" />
                    </div>
                    <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Stripe Payments API</span>
                            <span class="px-1.5 py-0.5 rounded text-[8.5px] font-bold uppercase bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Active</span>
                        </div>
                        <p class="text-[10.5px] text-zinc-500">Collect payments directly through embedded forms.</p>
                        <input id="settings-stripe-key" type="password" placeholder="pk_test_..." class="h-8 px-2.5 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 w-full" />
                    </div>
                    <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <label class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 uppercase">CRM Field Sync Mappings</label>
                        <div class="space-y-1.5">
                            <input id="map-crm-name" type="text" placeholder="Name field key" class="h-8 px-2.5 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 w-full" />
                            <input id="map-crm-email" type="text" placeholder="Email field key" class="h-8 px-2.5 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 w-full" />
                            <input id="map-crm-phone" type="text" placeholder="Phone field key" class="h-8 px-2.5 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 w-full" />
                            <input id="map-crm-notes" type="text" placeholder="Notes field key" class="h-8 px-2.5 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 w-full" />
                        </div>
                    </div>
                    <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Google Sheets Sync</span>
                            <span class="px-1.5 py-0.5 rounded text-[8.5px] font-bold uppercase bg-zinc-900 text-white font-mono">Soon</span>
                        </div>
                        <p class="text-[10.5px] text-zinc-500">Auto-export form entries to live spreadsheets.</p>
                    </div>
                </div>
            </div>
            <!-- /LEFT PANEL -->

            <!-- COLUMN 2: MAIN CONTENT AREA (FLEX-1) -->
            <div class="flex-1 flex flex-col overflow-hidden">

                <!-- BUILD VIEW -->
                <div id="editor-build-view" class="flex-1 flex flex-col overflow-hidden">
                    <!-- Steps Bar -->
                    <div id="editor-steps-bar" class="flex items-center gap-2 px-6 pt-4 pb-2 overflow-x-auto shrink-0"></div>

                    <!-- Canvas Scroll Area -->
                    <div id="editor-center-canvas" class="flex-1 bg-zinc-50 dark:bg-zinc-950 overflow-y-auto p-6 flex flex-col items-center">
                        <!-- CANVAS SHEET -->
                        <div id="editor-document-sheet" class="w-full max-w-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex flex-col overflow-hidden relative min-h-[600px]">
                            <!-- Cover Banner Dropzone -->
                            <div class="w-full h-36 border-b border-dashed border-zinc-200 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-900/50 flex flex-col items-center justify-center gap-2 cursor-pointer hover:bg-zinc-100/60 transition-all" id="editor-header-dropzone">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                <span class="text-xs font-semibold text-zinc-500">Add header logo or cover image</span>
                                <span class="text-[10px] text-zinc-400">Recommended: 1200 x 400px</span>
                            </div>

                            <!-- Form Header Info -->
                            <div class="px-10 pt-6 pb-2 space-y-1">
                                <h2 class="text-2xl font-bold text-zinc-950 dark:text-zinc-50 outline-none border-none bg-transparent" contenteditable="true" id="canvas-form-name">Cora Survey Form</h2>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 outline-none" contenteditable="true" id="canvas-form-subtitle">Fill out details below to submit request.</p>
                            </div>

                            <!-- Block List Container -->
                            <div class="px-6 pb-4 pt-2 flex flex-col gap-3 min-h-[200px]" id="editor-blocks-container">
                                <!-- Dynamic blocks injected here -->
                            </div>

                            <!-- Bottom Add Dropzone -->
                            <div id="editor-drop-zone" class="mx-6 mb-6 py-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/40 dark:bg-zinc-900/30 flex flex-col items-center justify-center gap-2">
                                <span class="text-xs font-medium text-zinc-400">+ Drag &amp; drop a field here</span>
                                <span class="text-[10px] text-zinc-300">or</span>
                                <button id="btn-add-element-bottom" class="h-8 px-4 rounded-lg bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 text-xs font-bold hover:bg-zinc-800 cursor-pointer transition-all">
                                    + Add Form Field
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /BUILD VIEW -->

                <!-- SUBMISSIONS VIEW -->
                <div id="editor-submissions-state" class="hidden flex-1 flex overflow-hidden">
                    <!-- Left Summary Panel -->
                    <div class="w-72 shrink-0 border-r border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex flex-col overflow-y-auto p-5 gap-5">
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Submission</h3>
                            <p class="text-xs text-zinc-500 mt-1">View and manage all form submissions.</p>
                        </div>
                        <!-- Summary Stats -->
                        <div class="space-y-1">
                            <div class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Summary</div>
                            <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                <span class="text-xs text-zinc-600 dark:text-zinc-400">Total Submissions</span>
                                <span id="sub-stat-total" class="text-xs font-bold text-zinc-900 dark:text-zinc-50">—</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                <span class="text-xs text-zinc-600 dark:text-zinc-400">This Month</span>
                                <span id="sub-stat-month" class="text-xs font-bold text-zinc-900 dark:text-zinc-50">—</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                <span class="text-xs text-zinc-600 dark:text-zinc-400">Today</span>
                                <span id="sub-stat-today" class="text-xs font-bold text-zinc-900 dark:text-zinc-50">—</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-xs text-zinc-600 dark:text-zinc-400">Unread</span>
                                <span id="sub-stat-unread" class="text-xs font-bold text-zinc-900 dark:text-zinc-50">—</span>
                            </div>
                        </div>
                        <!-- Filters -->
                        <div class="space-y-2">
                            <div class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider">Filters</div>
                            <div class="relative">
                                <input id="submissions-search" type="text" placeholder="Search submissions..." class="h-8 w-full pl-8 pr-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs outline-none focus:border-zinc-400" />
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </div>
                            <select id="submissions-step-filter" class="h-8 w-full px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs outline-none">
                                <option value="">All Steps</option>
                            </select>
                            <select id="submissions-status-filter" class="h-8 w-full px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs outline-none">
                                <option value="">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="in_progress">In Progress</option>
                                <option value="incomplete">Incomplete</option>
                            </select>
                            <button class="h-8 w-full rounded-lg border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-600 hover:bg-zinc-50 transition-all">Clear Filters</button>
                        </div>
                        <!-- Export -->
                        <div class="space-y-2">
                            <div class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider">Export</div>
                            <button id="submissions-export-btn" class="h-8 w-full flex items-center justify-center gap-2 rounded-lg border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-all cursor-pointer">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Export Submissions
                            </button>
                        </div>
                    </div>
                    <!-- Right Table -->
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Submissions <span id="submissions-count-label" class="text-zinc-400 font-normal">(0)</span></h3>
                                <p class="text-xs text-zinc-500 mt-0.5">Here are all the responses collected from your form.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button id="submissions-refresh-btn" class="h-8 w-8 rounded-lg border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-500 hover:bg-zinc-50 cursor-pointer">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex-1 overflow-auto">
                            <table class="w-full border-collapse text-left">
                                <thead class="sticky top-0 bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-wider w-8"><input type="checkbox" id="submissions-select-all" class="rounded"></th>
                                        <th class="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Submitted On</th>
                                        <th class="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Submitted By</th>
                                        <th class="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Current Step</th>
                                        <th class="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="submissions-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                                    <tr><td colspan="7" class="px-4 py-10 text-center text-zinc-400 text-xs">Loading submissions...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /SUBMISSIONS VIEW -->

                <!-- TEMPLATES VIEW -->
                <div id="editor-templates-state" class="hidden flex-1 flex overflow-hidden">
                    <!-- Left Category Panel -->
                    <div class="w-60 shrink-0 border-r border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex flex-col overflow-hidden">
                        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 shrink-0">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Templates</h3>
                            <p class="text-xs text-zinc-500 mt-1">Choose a template to get started quickly.</p>
                        </div>
                        <div class="p-3">
                            <div class="relative">
                                <input id="templates-search" type="text" placeholder="Search templates..." class="h-8 w-full pl-8 pr-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs outline-none focus:border-zinc-400" />
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </div>
                        </div>
                        <div id="templates-category-list" class="flex-1 overflow-y-auto px-3 pb-3 space-y-0.5"></div>
                        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800 shrink-0">
                            <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Start from Scratch</p>
                            <p class="text-[10px] text-zinc-500 mb-2">Create a blank form and build it your way.</p>
                            <button onclick="createNewForm()" class="w-full h-8 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:border-zinc-500 transition-all">+ Blank Form</button>
                        </div>
                    </div>
                    <!-- Right Templates Grid -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">All Templates</h3>
                                <p class="text-xs text-zinc-500 mt-0.5">Select a template to pre-fill your form with relevant fields.</p>
                            </div>
                        </div>
                        <div id="templates-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4"></div>
                    </div>
                </div>
                <!-- /TEMPLATES VIEW -->

                <!-- INTEGRATIONS VIEW -->
                <div id="editor-integrations-state" class="hidden flex-1 flex overflow-hidden">
                    <!-- Left Category Panel -->
                    <div class="w-60 shrink-0 border-r border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex flex-col overflow-hidden">
                        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 shrink-0">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Integrations</h3>
                            <p class="text-xs text-zinc-500 mt-1">Connect your form with the tools you use.</p>
                        </div>
                        <div id="integrations-category-list" class="flex-1 overflow-y-auto p-3 space-y-0.5"></div>
                        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800 shrink-0">
                            <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Missing an integration?</p>
                                <p class="text-[10px] text-zinc-500 mb-2">Let us know which tool you'd like to connect with.</p>
                                <button class="w-full h-8 rounded-lg border border-zinc-300 dark:border-zinc-600 text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:border-zinc-500 transition-all">Request Integration</button>
                            </div>
                        </div>
                    </div>
                    <!-- Right Integrations Grid -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">All Integrations</h3>
                                <p class="text-xs text-zinc-500 mt-0.5">Connect your form with the tools you use to automate workflows and sync data.</p>
                            </div>
                            <div class="relative">
                                <input id="integrations-search" type="text" placeholder="Search integrations..." class="h-8 w-44 pl-8 pr-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs outline-none" />
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </div>
                        </div>
                        <div id="integrations-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"></div>
                    </div>
                </div>
                <!-- /INTEGRATIONS VIEW -->

            </div>
            <!-- /MAIN CONTENT AREA -->
        </div>
    </div>

    <!-- BACKDROP FOR SUBMISSIONS BOTTOM DRAWER -->
    <div id="cora-submissions-backdrop" onclick="closeSubmissionsDrawer()" class="hidden fixed top-[52px] bottom-0 left-0 lg:left-64 right-0 bg-black/40 backdrop-blur-xs z-40 transition-all duration-200 cursor-pointer"></div>

    <!-- STATE 3: SUBMISSIONS LIST BOTTOM DRAWER SHEET & DASHBOARD -->
    <div id="cora-submissions-drawer" class="hidden fixed top-[160px] bottom-0 left-0 lg:left-64 right-0 lg:right-5 max-h-[calc(100vh-160px)] h-[calc(100vh-160px)] bg-white dark:bg-zinc-950 shadow-[0_-12px_32px_rgba(0,0,0,0.07)] border-t border-l border-r border-zinc-200/80 dark:border-zinc-800 z-45 transform translate-y-full transition-all duration-300 ease-in-out flex flex-col rounded-t-[28px] overflow-hidden font-sans">
        <!-- Dashboard Header Bar -->
        <div class="px-6 py-4.5 border-b border-zinc-200/80 dark:border-zinc-800/80 flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0 bg-white dark:bg-zinc-950">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-11 h-11 rounded-2xl bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center shrink-0 shadow-xs">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-[15px] font-bold text-zinc-950 dark:text-zinc-50 tracking-tight" id="drawer-form-title">Form Submissions Dashboard</h3>
                        <span id="drawer-responses-count" class="px-3 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold shrink-0">0 Entries</span>
                    </div>
                    <p class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1 font-normal" id="drawer-form-meta">View, filter, and export user response entries for this form.</p>
                </div>
            </div>

            <!-- Action Controls -->
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <!-- Search Input -->
                <div class="relative">
                    <input id="submissions-search-input" type="text" placeholder="Search entries..." class="h-8 pl-8 pr-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-400 w-40 sm:w-52" />
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>

                <!-- Export CSV Button -->
                <button id="btn-export-submissions-csv" class="h-8 px-3 rounded-lg bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-semibold hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all flex items-center gap-1.5 cursor-pointer shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export CSV
                </button>

                <!-- Connect Google Sheets Button (Locked) -->
                <button id="btn-connect-google-sheets" class="h-8 px-3 rounded-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold hover:bg-zinc-50 dark:hover:bg-zinc-850 transition-all flex items-center gap-1.5 cursor-pointer shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600 dark:text-emerald-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    <span>Google Sheets</span>
                    <span class="px-1.5 py-0.2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 text-[8.5px] font-mono font-bold rounded uppercase tracking-wider">Soon</span>
                </button>

                <!-- Close Button -->
                <button id="btn-close-submissions" onclick="closeSubmissionsDrawer()" class="h-8 w-8 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 cursor-pointer transition-colors">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>

        <!-- Submissions Entries Content (Data Table / Grid) -->
        <div class="flex-1 overflow-y-auto p-6" id="submissions-drawer-content">
            <!-- Dynamic entries table goes here -->
        </div>

        <!-- RIGHT SLIDE-OVER ENTRY INSPECTOR DRAWER -->
        <div id="cora-entry-inspector" class="hidden absolute top-0 right-0 bottom-0 w-full md:w-[480px] bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-50 flex flex-col font-sans transform translate-x-full transition-transform duration-300">
            <!-- Inspector Header -->
            <div class="px-6 py-4 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/60 dark:bg-zinc-900/40 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-zinc-950 dark:text-zinc-50" id="inspector-entry-id">Entry #000</h4>
                            <span id="inspector-status-badge" class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700">Completed</span>
                        </div>
                        <p class="text-[10.5px] text-zinc-400 font-mono mt-0.5" id="inspector-submitted-at">Submitted --</p>
                    </div>
                </div>
                <button onclick="closeEntryInspector()" class="h-7 w-7 rounded-lg hover:bg-zinc-200/60 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-500 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Inspector Body -->
            <div class="flex-1 overflow-y-auto p-6 space-y-5" id="inspector-body-content">
                <!-- Question & Answer Notion Cards -->
            </div>
        </div>
    </div>

    <!-- CLAUSE EDITOR OVERLAY DRAWER -->
    <div id="cora-clause-drawer" class="fixed inset-y-0 right-0 w-full md:w-[450px] bg-white shadow-2xl border-l border-zinc-200 z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900">Add Library Clause</h3>
                <p class="text-[10px] text-zinc-400 mt-0.5">Define a reusable clause block</p>
            </div>
            <button id="btn-close-clause-drawer" class="h-8 w-8 rounded-lg hover:bg-zinc-50 flex items-center justify-center text-zinc-500">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 flex flex-col gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Clause Key Identifier (lowercase, no spaces)</label>
                <input id="drawer-clause-key" type="text" placeholder="e.g. swiss_aml_statement" class="h-9 px-3 rounded-lg border border-zinc-200 text-xs outline-none focus:border-zinc-300 w-full font-mono" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Clause Title</label>
                <input id="drawer-clause-title" type="text" placeholder="Swiss AML Compliance" class="h-9 px-3 rounded-lg border border-zinc-200 text-xs outline-none focus:border-zinc-300 w-full font-medium" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Clause Content Text</label>
                <textarea id="drawer-clause-text" rows="8" placeholder="Enter clause legal text here..." class="p-3 rounded-lg border border-zinc-200 text-xs outline-none focus:border-zinc-300 w-full resize-none"></textarea>
            </div>
            <button id="btn-save-drawer-clause" class="h-9 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold transition-all">
                Save Clause
            </button>
        </div>
    </div>

    <!-- GENERIC CONFIRMATION DRAWER -->
    <div id="cora-confirm-drawer" class="fixed inset-y-0 right-0 w-full md:w-[350px] bg-white shadow-2xl border-l border-zinc-200 z-50 transform translate-x-full transition-transform duration-300 flex flex-col font-sans">
        <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wide">Confirm Action</h3>
            <button id="btn-close-confirm" class="h-8 w-8 rounded-lg hover:bg-zinc-50 flex items-center justify-center text-zinc-500">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 p-5 flex flex-col justify-between gap-6">
            <div class="space-y-2">
                <p id="confirm-message-text" class="text-xs text-zinc-650 leading-relaxed">Are you sure you want to proceed with this action?</p>
            </div>
            <div class="flex flex-col gap-2">
                <button id="btn-confirm-action" class="h-9 w-full rounded-lg bg-zinc-950 text-white text-xs font-semibold cursor-pointer">Confirm</button>
                <button id="btn-cancel-confirm" class="h-9 w-full rounded-lg border border-zinc-200 text-zinc-600 text-xs font-semibold cursor-pointer">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Floating Command Menu / Slash Command Selector -->
    <div id="editor-slash-menu" class="hidden absolute bg-white border border-zinc-200 shadow-xl rounded-xl w-60 py-2 z-40 max-h-60 overflow-y-auto border border-zinc-200/80">
        <div class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Input Blocks</div>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="text">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg></span> Short Text Input
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="number">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg></span> Number Input
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="email">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></span> Email Address
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="phone">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.62 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></span> Phone Number
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="long_text">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="13" y2="18"></line></svg></span> Long Text Area
        </button>
        
        <div class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-2 border-t border-zinc-100 pt-2">Choices</div>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="dropdown">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg></span> Dropdown Selection
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="checkbox">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg></span> Multiple Checkboxes
        </button>

        <div class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-2 border-t border-zinc-100 pt-2">Layout Elements</div>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="header">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 12h16M4 6h16M4 18h8"></path></svg></span> Section Header
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="paragraph">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M13 4v16M9 4h8a4 4 0 0 1 0 8H9"></path></svg></span> Paragraph
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="page_break">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="3" y1="12" x2="21" y2="12"></line><polyline points="15 6 21 12 15 18"></polyline></svg></span> Page Break (Next Step)
        </button>
        <div class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-2 border-t border-zinc-100 pt-2">Checkout & Payments</div>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="stripe_payment">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></span> Stripe Checkout Card
        </button>

        <div class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-2 border-t border-zinc-100 pt-2">Calculations</div>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="formula">
            <span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="19" y1="5" x2="5" y2="19"></line><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg></span> Calculated Formula
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let formsData = [];
    let currentEditingForm = null;
    let selectedBlockIndex = null;
    let autoSaveTimer = null;

    const wpNonce = (typeof coraREData !== 'undefined' && coraREData.nonce) ? coraREData.nonce : ((typeof wpApiSettings !== 'undefined') ? wpApiSettings.nonce : '');

    // --- Type Meta ---
    const TYPE_META = {
        text:           { label: 'Short Text',     badge: 'Text',     icon: 'T' },
        input:          { label: 'Short Text',     badge: 'Text',     icon: 'T' },
        long_text:      { label: 'Long Text',      badge: 'Text',     icon: '\u2261' },
        textarea:       { label: 'Long Text',      badge: 'Text',     icon: '\u2261' },
        email:          { label: 'Email',          badge: 'Email',    icon: '\u2709' },
        phone:          { label: 'Phone',          badge: 'Phone',    icon: '\u260e' },
        tel:            { label: 'Phone',          badge: 'Phone',    icon: '\u260e' },
        number:         { label: 'Number',         badge: 'Number',   icon: '#' },
        dropdown:       { label: 'Dropdown',       badge: 'Dropdown', icon: '\u2228' },
        select:         { label: 'Dropdown',       badge: 'Dropdown', icon: '\u2228' },
        multiple_choice:{ label: 'Multiple Choice',badge: 'Choice',   icon: '\u25ce' },
        radio:          { label: 'Multiple Choice',badge: 'Choice',   icon: '\u25ce' },
        multiselect:    { label: 'Multi-Select',   badge: 'Choice',   icon: '\u25ce' },
        checkbox:       { label: 'Checkboxes',     badge: 'Check',    icon: '\u2611' },
        date:           { label: 'Date',           badge: 'Date',     icon: '\ud83d\udcc5' },
        file:           { label: 'File Upload',    badge: 'File',     icon: '\u2191' },
        signature:      { label: 'Signature',      badge: 'Sign',     icon: '\u270d' },
        rating:         { label: 'Rating',         badge: 'Rating',   icon: '\u2605' },
        slider:         { label: 'Slider',         badge: 'Slider',   icon: '\u27fa' },
        payment:        { label: 'Stripe Payment', badge: 'Payment',  icon: '\ud83d\udcb3' },
        stripe_payment: { label: 'Stripe Payment', badge: 'Payment',  icon: '\ud83d\udcb3' },
        rich_text:      { label: 'Rich Text',      badge: 'RTE',      icon: '\u270e' },
        matrix:         { label: 'Matrix Field',   badge: 'Matrix',   icon: '\u229e' },
        repeatable:     { label: 'Repeatable',     badge: 'Repeat',   icon: '\u2261' },
        hidden:         { label: 'Hidden Field',   badge: 'Hidden',   icon: '\u25ce' },
        header:         { label: 'Heading',        badge: 'Layout',   icon: 'H' },
        paragraph:      { label: 'Paragraph',      badge: 'Layout',   icon: '\u00b6' },
        page_break:     { label: 'Page Break',     badge: 'Layout',   icon: '\u2014' },
        columns:        { label: 'Columns',        badge: 'Layout',   icon: '\u229f' },
        divider:        { label: 'Divider',        badge: 'Layout',   icon: '\u2014' },
        spacer:         { label: 'Spacer',         badge: 'Layout',   icon: '\u283f' },
        formula:        { label: 'Formula',        badge: 'Calc',     icon: '\u0192' },
    };


    const FORM_TEMPLATES = [
        {
            id: 'contact_us',
            name: 'Contact Us',
            category: 'Business',
            description: 'Allow visitors to get in touch with your team.',
            fieldCount: 5,
            fields: [
                { type: 'text', label: 'Full Name', required: true },
                { type: 'email', label: 'Email Address', required: true },
                { type: 'phone', label: 'Phone Number', required: false },
                { type: 'dropdown', label: 'Subject', required: true, choices: [{label:'General Inquiry'},{label:'Support'},{label:'Sales'}] },
                { type: 'long_text', label: 'Message', required: true }
            ]
        },
        {
            id: 'request_quote',
            name: 'Request a Quote',
            category: 'Business',
            description: 'Get project details and requirements.',
            fieldCount: 6,
            fields: [
                { type: 'text', label: 'Full Name', required: true },
                { type: 'email', label: 'Email', required: true },
                { type: 'dropdown', label: 'Project Type', required: true, choices: [{label:'Web'},{label:'Mobile'},{label:'Other'}] },
                { type: 'long_text', label: 'Project Details', required: true },
                { type: 'slider', label: 'Budget', required: false },
                { type: 'text', label: 'Company', required: false }
            ]
        },
        {
            id: 'event_reg',
            name: 'Event Registration',
            category: 'Event',
            description: 'Register attendees for your upcoming event.',
            fieldCount: 5,
            fields: [
                { type: 'text', label: 'Full Name', required: true },
                { type: 'email', label: 'Email Address', required: true },
                { type: 'dropdown', label: 'Event Type', required: true, choices: [{label:'Conference'},{label:'Workshop'},{label:'Webinar'}] },
                { type: 'number', label: 'Number of Tickets', required: true },
                { type: 'long_text', label: 'Special Requirements', required: false }
            ]
        },
        {
            id: 'job_app',
            name: 'Job Application',
            category: 'HR',
            description: 'Collect applications from prospective candidates.',
            fieldCount: 7,
            fields: [
                { type: 'text', label: 'Full Name', required: true },
                { type: 'email', label: 'Email Address', required: true },
                { type: 'dropdown', label: 'Position Applied', required: true, choices: [{label:'Engineering'},{label:'Design'},{label:'Marketing'}] },
                { type: 'text', label: 'Experience', required: true },
                { type: 'text', label: 'Skills', required: true },
                { type: 'file', label: 'Upload Resume', required: true },
                { type: 'long_text', label: 'Cover Letter', required: false }
            ]
        },
        {
            id: 'customer_feedback',
            name: 'Customer Feedback',
            category: 'Feedback',
            description: 'Gather feedback on your products or services.',
            fieldCount: 3,
            fields: [
                { type: 'rating', label: 'Rating', required: true },
                { type: 'long_text', label: 'What did you like most?', required: true },
                { type: 'long_text', label: 'Any suggestions?', required: false }
            ]
        },
        {
            id: 'newsletter_signup',
            name: 'Newsletter Signup',
            category: 'Business',
            description: 'Simple form to grow your mailing list.',
            fieldCount: 3,
            fields: [
                { type: 'text', label: 'Full Name', required: true },
                { type: 'email', label: 'Email Address', required: true },
                { type: 'checkbox', label: 'Consent', required: true, choices: [{label:'I agree to receive marketing emails'}] }
            ]
        },
        {
            id: 'support_ticket',
            name: 'Support Ticket',
            category: 'Customer Service',
            description: 'Allow customers to log support requests.',
            fieldCount: 5,
            fields: [
                { type: 'text', label: 'Full Name', required: true },
                { type: 'email', label: 'Email Address', required: true },
                { type: 'dropdown', label: 'Issue Type', required: true, choices: [{label:'Billing'},{label:'Technical'},{label:'Other'}] },
                { type: 'long_text', label: 'Description', required: true },
                { type: 'dropdown', label: 'Priority', required: true, choices: [{label:'Low'},{label:'Medium'},{label:'High'}] }
            ]
        },
        {
            id: 'appt_booking',
            name: 'Appointment Booking',
            category: 'Business',
            description: 'Schedule appointments with your clients.',
            fieldCount: 6,
            fields: [
                { type: 'text', label: 'Full Name', required: true },
                { type: 'email', label: 'Email Address', required: true },
                { type: 'dropdown', label: 'Service Type', required: true, choices: [{label:'Consultation'},{label:'Follow-up'},{label:'Review'}] },
                { type: 'date', label: 'Preferred Date', required: true },
                { type: 'text', label: 'Preferred Time', required: true },
                { type: 'long_text', label: 'Notes', required: false }
            ]
        }
    ];

    const INTEGRATIONS = [
        { id: 'mailchimp', name: 'Mailchimp', category: 'Email Marketing', description: 'Sync contacts...', connected: false },
        { id: 'activecampaign', name: 'ActiveCampaign', category: 'Email Marketing', description: 'Sync contacts...', connected: false },
        { id: 'brevo', name: 'Brevo', category: 'Email Marketing', description: 'Sync contacts...', connected: false },
        { id: 'mailerlite', name: 'Mailerlite', category: 'Email Marketing', description: 'Sync contacts...', connected: false },
        { id: 'convertkit', name: 'ConvertKit', category: 'Email Marketing', description: 'Sync contacts...', connected: false },
        
        { id: 'hubspot', name: 'HubSpot', category: 'CRM', description: 'Sync contacts...', connected: false },
        { id: 'salesforce', name: 'Salesforce', category: 'CRM', description: 'Sync contacts...', connected: false },
        { id: 'notion', name: 'Notion', category: 'CRM', description: 'Sync contacts...', connected: false },
        { id: 'airtable', name: 'Airtable', category: 'CRM', description: 'Sync contacts...', connected: false },
        
        { id: 'zapier', name: 'Zapier', category: 'Automation', description: 'Automate workflows...', connected: false },
        { id: 'slack', name: 'Slack', category: 'Automation', description: 'Automate workflows...', connected: false },
        { id: 'webhook', name: 'Webhook', category: 'Automation', description: 'Automate workflows...', connected: false },
        
        { id: 'ga4', name: 'Google Analytics 4', category: 'Analytics', description: 'Track analytics...', connected: false },
        { id: 'fb_pixel', name: 'Facebook Pixel', category: 'Analytics', description: 'Track analytics...', connected: false },
        
        { id: 'dropbox', name: 'Dropbox', category: 'Storage', description: 'Store files...', connected: false },
        { id: 'stripe', name: 'Stripe', category: 'Payments', description: 'Accept payments...', connected: false }
    ];

    // --- Extracted Functions placeholder ---
    
    // View States selectors
    const listState = document.getElementById('forms-list-state');
    const editorState = document.getElementById('form-editor-state');
    
    jQuery('#funnel-form-selector').on('change', updateAdvancedFunnelData);

    const tabFormsList = document.getElementById('tab-forms-list');
    const tabFunnel = document.getElementById('tab-funnel-analytics');
    const tabClauses = document.getElementById('tab-clauses-library');
    const tabAuditLogs = document.getElementById('tab-audit-logs');
    
    const listTabContent = document.getElementById('forms-list-tab-content');
    const funnelTabContent = document.getElementById('forms-funnel-tab-content');
    const clausesTabContent = document.getElementById('forms-clauses-tab-content');
    const auditTabContent = document.getElementById('forms-audit-tab-content');

    if(tabFormsList) tabFormsList.addEventListener('click', function() { window.location.hash = '#list'; });
    if(tabFunnel) tabFunnel.addEventListener('click', function() { window.location.hash = '#funnel'; });
    if(tabClauses) tabClauses.addEventListener('click', function() { window.location.hash = '#clauses'; });
    if(tabAuditLogs) tabAuditLogs.addEventListener('click', function() { window.location.hash = '#audit-log'; });


function coraCopyTextToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                window.coraShowToast && window.coraShowToast("Form link copied to clipboard!");
            }).catch(function() {
                coraCopyFallback(text);
            });
        } else {
            coraCopyFallback(text);
        }
    }

function coraCopyFallback(text) {
        const el = document.createElement('textarea');
        el.value = text;
        el.style.position = 'fixed';
        el.style.opacity = '0';
        document.body.appendChild(el);
        el.select();
        try {
            document.execCommand('copy');
            window.coraShowToast && window.coraShowToast("Form link copied to clipboard!");
        } catch (err) {
            console.error('Fallback copy failed', err);
            window.coraShowToast && window.coraShowToast("Failed to copy. URL: " + text);
        }
        document.body.removeChild(el);
    }

function fetchForms() {
        jQuery.ajax({
            url: '/wp-json/cora/v1/forms',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(response) {
                formsData = response;
                renderFormsList();
                updateMetrics();
                handleRouting();
            },
            error: function(err) {
                console.error("Error loading forms:", err);
            }
        });
    }

function updateMetrics() {
        document.getElementById('metric-total-forms').textContent = formsData.length;
        let totalSubmissions = 0;
        formsData.forEach(f => {
            totalSubmissions += f.submission_count || 0;
        });
        
        const totalViews = Math.round(Math.max(formsData.length * 15, totalSubmissions * 1.6));
        
        document.getElementById('metric-total-submissions').textContent = totalSubmissions;
        document.getElementById('metric-total-views').textContent = totalViews;
        
        const completionRate = totalViews > 0 ? Math.round((totalSubmissions / totalViews) * 100) : 0;
        document.getElementById('metric-completion-rate').textContent = completionRate + "%";
        
        populateFunnelSelector();
        updateAdvancedFunnelData();
    }

function populateFunnelSelector() {
        const selector = document.getElementById('funnel-form-selector');
        if (!selector) return;
        
        const savedVal = selector.value;
        selector.innerHTML = '<option value="all">All Forms (Aggregate)</option>';
        formsData.forEach(form => {
            const opt = document.createElement('option');
            opt.value = form.id;
            opt.textContent = form.title;
            selector.appendChild(opt);
        });
        
        if (savedVal && Array.from(selector.options).some(o => o.value == savedVal)) {
            selector.value = savedVal;
        }
    }

function updateFunnelLossBadge(elementId, originalVal, targetVal) {
        const badge = document.getElementById(elementId);
        if (!badge) return;
        
        const lossCount = originalVal - targetVal;
        const lossPct = originalVal > 0 ? Math.round((lossCount / originalVal) * 100) : 0;
        
        if (lossPct > 0) {
            badge.className = "px-2 py-0.5 rounded bg-red-50 text-red-650 text-[9px] font-bold border border-red-100 flex items-center gap-0.5 transition-all";
            badge.innerHTML = `${lossPct}% drop-off (${lossCount} abandoned)`;
        } else {
            badge.className = "px-2 py-0.5 rounded bg-zinc-50 text-zinc-500 text-[9px] font-bold border border-zinc-200/80 flex items-center gap-0.5 transition-all";
            badge.innerHTML = `100% retention (0 abandoned)`;
        }
    }

function updateAdvancedFunnelData() {
        const selector = document.getElementById('funnel-form-selector');
        if (!selector) return;
        const selectedId = selector.value;
        
        let totalSubmissions = 0;
        formsData.forEach(f => {
            totalSubmissions += f.submission_count || 0;
        });

        if (selectedId === 'all') {
            const views = Math.round(Math.max(formsData.length * 15, totalSubmissions * 1.6));
            const started = Math.round(Math.max(formsData.length * 8, totalSubmissions * 1.2));
            const completed = totalSubmissions;
            const abRate = started > 0 ? Math.round(((started - completed) / started) * 100) : 0;
            
            document.getElementById('funnel-metric-views').textContent = views;
            document.getElementById('funnel-metric-started').textContent = started;
            document.getElementById('funnel-metric-completed').textContent = completed;
            document.getElementById('funnel-metric-abandonment').textContent = abRate + "%";
            
            document.getElementById('funnel-views-count').textContent = views;
            document.getElementById('funnel-started-count').textContent = started;
            
            const startedPct = views > 0 ? Math.round((started / views) * 100) : 0;
            document.getElementById('funnel-started-pct').textContent = startedPct + "%";
            document.getElementById('funnel-started-progress').style.width = startedPct + "%";
            document.getElementById('funnel-started-progress').textContent = startedPct + "%";
            
            document.getElementById('funnel-completed-count').textContent = completed;
            const completedPct = views > 0 ? Math.round((completed / views) * 100) : 0;
            document.getElementById('funnel-completed-pct').textContent = completedPct + "%";
            document.getElementById('funnel-completed-progress').style.width = completedPct + "%";
            document.getElementById('funnel-completed-progress').textContent = completedPct + "%";
            
            // Loss badges
            updateFunnelLossBadge('funnel-loss-1', views, started);
            updateFunnelLossBadge('funnel-loss-2', started, completed);

            document.getElementById('funnel-friction-list').innerHTML = `
                <div class="text-[10px] text-zinc-400 text-center py-4">Select a specific form to view field friction analytics.</div>
            `;
        } else {
            const formObj = formsData.find(f => f.id == selectedId);
            if (!formObj) return;
            
            jQuery.ajax({
                url: `/wp-json/cora/v1/forms/${selectedId}/submissions`,
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wpNonce);
                },
                success: function(submissions) {
                    const started = submissions.length;
                    const completed = submissions.filter(s => s.is_partial == '0').length;
                    const views = Math.round(Math.max(15, started * 1.6));
                    const abRate = started > 0 ? Math.round(((started - completed) / started) * 100) : 0;
                    
                    document.getElementById('funnel-metric-views').textContent = views;
                    document.getElementById('funnel-metric-started').textContent = started;
                    document.getElementById('funnel-metric-completed').textContent = completed;
                    document.getElementById('funnel-metric-abandonment').textContent = abRate + "%";
                    
                    document.getElementById('funnel-views-count').textContent = views;
                    document.getElementById('funnel-started-count').textContent = started;
                    
                    const startedPct = views > 0 ? Math.round((started / views) * 100) : 0;
                    document.getElementById('funnel-started-pct').textContent = startedPct + "%";
                    document.getElementById('funnel-started-progress').style.width = startedPct + "%";
                    document.getElementById('funnel-started-progress').textContent = startedPct + "%";
                    
                    document.getElementById('funnel-completed-count').textContent = completed;
                    const completedPct = views > 0 ? Math.round((completed / views) * 100) : 0;
                    document.getElementById('funnel-completed-pct').textContent = completedPct + "%";
                    document.getElementById('funnel-completed-progress').style.width = completedPct + "%";
                    document.getElementById('funnel-completed-progress').textContent = completedPct + "%";
                    
                    // Loss badges
                    updateFunnelLossBadge('funnel-loss-1', views, started);
                    updateFunnelLossBadge('funnel-loss-2', started, completed);

                    // Friction Analysis
                    const frictionContainer = document.getElementById('funnel-friction-list');
                    
                    const inputBlocks = (formObj.blocks || []).filter(b => 
                        b.type !== 'header' && b.type !== 'paragraph' && b.type !== 'divider' && b.type !== 'page_break' && b.type !== 'stripe_payment'
                    );
                    
                    if (inputBlocks.length === 0) {
                        frictionContainer.innerHTML = `<div class="text-[10px] text-zinc-400 text-center py-4">No input fields in this form structure.</div>`;
                        return;
                    }
                    
                    const fieldStats = inputBlocks.map(b => {
                        let fillCount = 0;
                        submissions.forEach(sub => {
                            const val = sub.submitted_data[b.label];
                            if (val !== undefined && val !== null && val !== '') {
                                fillCount++;
                            }
                        });
                        const rate = started > 0 ? Math.round((fillCount / started) * 100) : 0;
                        return {
                            label: b.label || 'Unnamed Field',
                            count: fillCount,
                            rate: rate
                        };
                    });
                    
                    // Sort by completion rate ascending
                    fieldStats.sort((a, b) => a.rate - b.rate);
                    
                    frictionContainer.innerHTML = '';
                    fieldStats.forEach(fStat => {
                        const row = document.createElement('div');
                        row.className = 'space-y-1 bg-zinc-50/50 border border-zinc-200/60 p-2.5 rounded-xl flex flex-col gap-1';
                        
                        const frictionLabel = fStat.rate < 60 
                            ? `<span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-red-50 border border-red-100 text-red-650 uppercase leading-none">High Loss</span>`
                            : `<span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-zinc-100 border border-zinc-200 text-zinc-500 uppercase leading-none">Stable</span>`;
                        
                        row.innerHTML = `
                            <div class="flex items-center justify-between text-[11px] font-semibold text-zinc-800">
                                <span class="truncate max-w-[110px]">${fStat.label}</span>
                                <span>${fStat.rate}% filled</span>
                            </div>
                            <div class="h-2 w-full bg-zinc-100 rounded-full overflow-hidden">
                                <div class="h-full ${fStat.rate < 60 ? 'bg-zinc-850' : 'bg-zinc-500'} transition-all" style="width: ${fStat.rate}%"></div>
                            </div>
                            <div class="flex items-center justify-between mt-0.5">
                                <span class="text-[9px] text-zinc-400 font-medium">${fStat.count} of ${started} respondents</span>
                                ${frictionLabel}
                            </div>
                        `;
                        frictionContainer.appendChild(row);
                    });
                }
            });
        }
    }

function fetchClauses() {
        jQuery.ajax({
            url: '/wp-json/cora/v1/forms/clauses',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(clauses) {
                renderClausesList(clauses);
            }
        });
    }

function renderClausesList(clauses) {
        const body = document.getElementById('clauses-list-body');
        if (clauses.length === 0) {
            body.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-zinc-400">No clauses created yet. Click "+ Add Clause" to start.</td>
                </tr>`;
            return;
        }
        
        body.innerHTML = '';
        clauses.forEach(c => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-zinc-50/50';
            tr.innerHTML = `
                <td class="px-6 py-4 font-mono text-zinc-650">${c.clause_key}</td>
                <td class="px-6 py-4 font-semibold text-zinc-800">${c.title}</td>
                <td class="px-6 py-4 text-zinc-500 max-w-xs truncate">${c.content_text}</td>
                <td class="px-6 py-4 text-right">
                    <button class="btn-delete-db-clause h-7 px-2 rounded-lg border border-zinc-200 hover:border-red-200 text-zinc-500 hover:text-red-650 bg-white cursor-pointer" data-id="${c.id}">Delete</button>
                </td>
            `;
            body.appendChild(tr);
        });
        
        jQuery('.btn-delete-db-clause').off('click').on('click', function() {
            const cId = jQuery(this).data('id');
            jQuery.ajax({
                url: `/wp-json/cora/v1/forms/clauses/${cId}`,
                method: 'DELETE',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wpNonce);
                },
                success: function() {
                    window.coraShowToast && window.coraShowToast("Clause deleted successfully.");
                    fetchClauses();
                }
            });
        });
    }

function fetchAuditLogs() {
        jQuery.ajax({
            url: '/wp-json/cora/v1/forms/audit-log',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(logs) {
                renderAuditLogs(logs);
            }
        });
    }

function renderAuditLogs(logs) {
        const body = document.getElementById('audit-logs-body');
        if (logs.length === 0) {
            body.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-zinc-400">No audit log entries recorded.</td>
                </tr>`;
            return;
        }
        
        body.innerHTML = '';
        logs.forEach(l => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-zinc-50/50';
            tr.innerHTML = `
                <td class="px-6 py-4 text-zinc-500">${l.created_at}</td>
                <td class="px-6 py-4 font-semibold text-zinc-800">${l.display_name}</td>
                <td class="px-6 py-4"><span class="px-1.5 py-0.5 rounded font-mono text-[9px] bg-zinc-100 text-zinc-650">${l.action_type}</span></td>
                <td class="px-6 py-4 text-zinc-500">${l.field_label || 'Submission #' + l.submission_id}</td>
                <td class="px-6 py-4 font-mono text-zinc-400">${l.ip_address}</td>
            `;
            body.appendChild(tr);
        });
    }

function coraConfirmAction(message, onConfirm) {
        document.getElementById('confirm-message-text').textContent = message;
        confirmCallback = onConfirm;
        confirmDrawer.classList.remove('translate-x-full');
    }

function deleteForm(id) {
        jQuery.ajax({
            url: `/wp-json/cora/v1/forms/${id}`,
            method: 'DELETE',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function() {
                window.coraShowToast && window.coraShowToast("Form deleted successfully.", "success");
                fetchForms();
            }
        });
    }

function renderFormsList() {
        const body = document.getElementById('forms-list-body');
        if (formsData.length === 0) {
            body.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-xs text-zinc-400">No forms found. Create one to get started.</td>
                </tr>`;
            return;
        }

        body.innerHTML = '';
        formsData.forEach(form => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-zinc-50/50';
            tr.innerHTML = `
                <td class="px-6 py-4 text-xs font-medium text-zinc-900">${form.title}</td>
                <td class="px-6 py-4 text-xs">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold ${form.status === 'published' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-500'}">
                        ${form.status.toUpperCase()}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-zinc-500">${form.submission_count || 0} responses</td>
                <td class="px-6 py-4 text-xs text-zinc-500">${form.created_at}</td>
                <td class="px-6 py-4 text-xs text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button class="btn-view-live h-7 w-7 rounded-lg border border-zinc-200 hover:border-zinc-300 bg-white hover:bg-zinc-50 flex items-center justify-center text-zinc-500 hover:text-zinc-950 transition-all cursor-pointer" data-id="${form.id}" title="View Live Form">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <button class="btn-share-form h-7 w-7 rounded-lg border border-zinc-200 hover:border-zinc-300 bg-white hover:bg-zinc-50 flex items-center justify-center text-zinc-500 hover:text-zinc-950 transition-all cursor-pointer" data-id="${form.id}" title="Copy Share Link">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        </button>
                        <button class="btn-edit-form h-7 w-7 rounded-lg border border-zinc-200 hover:border-zinc-300 bg-white hover:bg-zinc-50 flex items-center justify-center text-zinc-500 hover:text-zinc-950 transition-all cursor-pointer" data-id="${form.id}" title="Edit Form">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <button class="btn-view-subs h-7 w-7 rounded-lg border border-zinc-200 hover:border-zinc-300 bg-white hover:bg-zinc-50 flex items-center justify-center text-zinc-500 hover:text-zinc-950 transition-all cursor-pointer" data-id="${form.id}" title="View Submissions">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                        </button>
                        <button class="btn-delete-form h-7 w-7 rounded-lg border border-zinc-200 hover:border-red-200 bg-white hover:bg-red-50/50 flex items-center justify-center text-zinc-500 hover:text-red-650 transition-all cursor-pointer" data-id="${form.id}" title="Delete Form">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </button>
                    </div>
                </td>
            `;
            body.appendChild(tr);
        });

        // Attach listeners
        jQuery('.btn-view-live').on('click', function() {
            const id = jQuery(this).data('id');
            let siteUrl = coraREData.siteUrl || '';
            if (siteUrl.endsWith('/')) {
                siteUrl = siteUrl.slice(0, -1);
            }
            window.open(siteUrl + '/shared-form/' + id, '_blank');
        });

        jQuery('.btn-share-form').on('click', function() {
            const id = jQuery(this).data('id');
            let siteUrl = coraREData.siteUrl || '';
            if (siteUrl.endsWith('/')) {
                siteUrl = siteUrl.slice(0, -1);
            }
            const shareUrl = siteUrl + '/shared-form/' + id;
            coraCopyTextToClipboard(shareUrl);
        });

        jQuery('.btn-edit-form').on('click', function() {
            const id = jQuery(this).data('id');
            window.location.hash = '#edit/' + id;
        });

        jQuery('.btn-view-subs').on('click', function() {
            const id = jQuery(this).data('id');
            openSubmissionsDrawer(id);
        });

        jQuery('.btn-delete-form').on('click', function() {
            const id = jQuery(this).data('id');
            coraConfirmAction("Are you sure you want to delete this form and all responses? This action is permanent.", function() {
                deleteForm(id);
            });
        });
    }
    
    // --- Routing ---
    function handleRouting() {
        const hash = window.location.hash || '#list';
        
        if (listState) { listState.classList.add('hidden'); listState.classList.remove('flex'); }
        if (editorState) { editorState.classList.add('hidden'); editorState.classList.remove('flex'); }
        
        if (listTabContent) { listTabContent.classList.add('hidden'); listTabContent.classList.remove('flex'); }
        if (funnelTabContent) { funnelTabContent.classList.add('hidden'); funnelTabContent.classList.remove('flex'); }
        if (clausesTabContent) { clausesTabContent.classList.add('hidden'); clausesTabContent.classList.remove('flex'); }
        if (auditTabContent) { auditTabContent.classList.add('hidden'); auditTabContent.classList.remove('flex'); }
        
        const tabs = [tabFormsList, tabFunnel, tabClauses, tabAuditLogs];
        tabs.forEach(t => {
            if (t) {
                t.classList.remove('font-semibold', 'border-zinc-950', 'text-zinc-950');
                t.classList.add('font-medium', 'border-transparent', 'text-zinc-500');
            }
        });

        if (hash === '#list') {
            if (tabFormsList) {
                tabFormsList.classList.add('font-semibold', 'border-zinc-950', 'text-zinc-950');
                tabFormsList.classList.remove('font-medium', 'border-transparent', 'text-zinc-500');
            }
            if (listTabContent) { listTabContent.classList.remove('hidden'); listTabContent.classList.add('flex'); }
            if (listState) listState.classList.remove('hidden');
        } else if (hash === '#funnel') {
            if (tabFunnel) {
                tabFunnel.classList.add('font-semibold', 'border-zinc-950', 'text-zinc-950');
                tabFunnel.classList.remove('font-medium', 'border-transparent', 'text-zinc-500');
            }
            if (funnelTabContent) { funnelTabContent.classList.remove('hidden'); funnelTabContent.classList.add('flex'); }
            if (listState) listState.classList.remove('hidden');
            populateFunnelSelector();
            updateAdvancedFunnelData();
        } else if (hash === '#clauses') {
            if (tabClauses) {
                tabClauses.classList.add('font-semibold', 'border-zinc-950', 'text-zinc-950');
                tabClauses.classList.remove('font-medium', 'border-transparent', 'text-zinc-500');
            }
            if (clausesTabContent) { clausesTabContent.classList.remove('hidden'); clausesTabContent.classList.add('flex'); }
            if (listState) listState.classList.remove('hidden');
            fetchClauses();
        } else if (hash === '#audit-log') {
            if (tabAuditLogs) {
                tabAuditLogs.classList.add('font-semibold', 'border-zinc-950', 'text-zinc-950');
                tabAuditLogs.classList.remove('font-medium', 'border-transparent', 'text-zinc-500');
            }
            if (auditTabContent) { auditTabContent.classList.remove('hidden'); auditTabContent.classList.add('flex'); }
            if (listState) listState.classList.remove('hidden');
            fetchAuditLogs();
        } else if (hash.startsWith('#edit/')) {
            const id = hash.split('/')[1];
            loadFormIntoEditor(id);
        } else if (hash === '#new') {
            createNewForm();
        } else {
            // Default to list
            if (listState) listState.classList.remove('hidden');
        }
    }

    window.addEventListener('hashchange', handleRouting);
    
    // Initial fetch
    if (window.location.hash === '' || window.location.hash === '#list') {
        fetchForms();
    } else {
        fetchForms(); // fetch forms anyway to populate list when going back
    }

    // --- New Builder Code ---
    function switchEditorView(view) {
        const views = ['build', 'submissions', 'templates', 'integrations'];
        views.forEach(v => {
            const el = document.getElementById(`editor-${v}-state`) || (v === 'build' ? document.getElementById('editor-build-view') : null);
            if (el) {
                if (v === view) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            }
        });

        // Update nav icons
        const navIcons = ['build', 'submissions', 'templates', 'integrations'];
        navIcons.forEach(v => {
            const btn = document.getElementById(`left-nav-${v}`);
            if (btn) {
                if (v === view) {
                    btn.classList.add('bg-zinc-100', 'text-zinc-900');
                    btn.classList.remove('text-zinc-500', 'hover:bg-zinc-50', 'hover:text-zinc-900');
                } else {
                    btn.classList.remove('bg-zinc-100', 'text-zinc-900');
                    btn.classList.add('text-zinc-500', 'hover:bg-zinc-50', 'hover:text-zinc-900');
                }
            }
        });

        if (view === 'submissions') loadSubmissions();
        if (view === 'templates') renderTemplatesGrid('all');
        if (view === 'integrations') renderIntegrationsGrid('all');
    }


    // --- Cover Image & Multi-Step Helpers ---
    function renderCoverImage() {
        const dz = document.getElementById('editor-header-dropzone');
        if (!dz) return;
        const imgUrl = currentEditingForm.settings?.cover_image;
        if (imgUrl) {
            dz.style.backgroundImage = `url('${imgUrl}')`;
            dz.style.backgroundSize = 'cover';
            dz.style.backgroundPosition = 'center';
            dz.classList.add('relative');
            dz.innerHTML = `
                <div class="absolute top-2 right-2 flex gap-1 opacity-90 hover:opacity-100 transition-opacity">
                    <button id="btn-change-cover" class="px-2.5 py-1 bg-black/70 text-white rounded-lg text-[10px] font-semibold backdrop-blur-xs hover:bg-black cursor-pointer">Change Cover</button>
                    <button id="btn-remove-cover" class="px-2.5 py-1 bg-red-600/80 text-white rounded-lg text-[10px] font-semibold backdrop-blur-xs hover:bg-red-700 cursor-pointer">Remove</button>
                </div>
            `;
        } else {
            dz.style.backgroundImage = 'none';
            dz.classList.remove('relative');
            dz.innerHTML = `
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                <span class="text-xs font-semibold text-zinc-500">Add header logo or cover image</span>
                <span class="text-[10px] text-zinc-400">Recommended: 1200 x 400px</span>
            `;
        }
    }

    function openCoverImagePicker() {
        if (typeof wp !== 'undefined' && wp.media) {
            const customUploader = wp.media({
                title: 'Select Cover Image',
                button: { text: 'Use Cover Image' },
                multiple: false
            });
            customUploader.on('select', function() {
                const attachment = customUploader.state().get('selection').first().toJSON();
                if (!currentEditingForm.settings) currentEditingForm.settings = {};
                currentEditingForm.settings.cover_image = attachment.url;
                renderCoverImage();
                triggerAutoSave();
            });
            customUploader.open();
        } else {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        if (!currentEditingForm.settings) currentEditingForm.settings = {};
                        currentEditingForm.settings.cover_image = evt.target.result;
                        renderCoverImage();
                        triggerAutoSave();
                    };
                    reader.readAsDataURL(file);
                }
            };
            fileInput.click();
        }
    }

    function renderStepsBar() {
        const container = document.getElementById('editor-steps-bar');
        if (!container) return;
        const steps = currentEditingForm.settings?.steps || ['Step 1'];
        let html = '';
        steps.forEach((step, idx) => {
            const active = (currentEditingForm.currentStepIndex || 0) === idx;
            const activeClasses = active ? 'border-2 border-zinc-950 dark:border-zinc-100 bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-50 font-bold' : 'border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 font-medium hover:border-zinc-400';
            html += `
                <button class="step-tab-btn h-8 px-3 rounded-lg ${activeClasses} text-xs flex items-center gap-2 shrink-0 cursor-pointer" data-step-idx="${idx}">
                    <span class="w-4 h-4 rounded bg-zinc-100 dark:bg-zinc-800 text-[10px] flex items-center justify-center">${idx + 1}</span>
                    <span>${step}</span>
                </button>
            `;
        });
        html += `
            <button id="btn-add-step" class="h-8 px-3 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700 text-zinc-500 hover:text-zinc-900 text-xs font-semibold flex items-center gap-1 shrink-0 cursor-pointer">
                <span>+</span> Add Step
            </button>
        `;
        container.innerHTML = html;

        container.querySelectorAll('.step-tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                currentEditingForm.currentStepIndex = parseInt(btn.dataset.stepIdx);
                renderStepsBar();
            });
        });

        document.getElementById('btn-add-step')?.addEventListener('click', () => {
            if (!currentEditingForm.settings) currentEditingForm.settings = {};
            if (!currentEditingForm.settings.steps) currentEditingForm.settings.steps = ['Step 1'];
            currentEditingForm.settings.steps.push(`Step ${currentEditingForm.settings.steps.length + 1}`);
            currentEditingForm.currentStepIndex = currentEditingForm.settings.steps.length - 1;
            renderStepsBar();
            triggerAutoSave();
        });
    }

    function switchLeftTab(tab) {
        const tabs = ['fields', 'settings', 'form', 'integ'];
        tabs.forEach(t => {
            const contentEl = document.getElementById(`left-tab-${t}`);
            const btnEl = document.getElementById(`btn-left-tab-${t}`);
            if (t === tab) {
                if (contentEl) contentEl.classList.remove('hidden');
                if (btnEl) {
                    btnEl.classList.add('text-zinc-950', 'dark:text-zinc-50', 'border-zinc-950', 'dark:border-zinc-50', 'font-bold');
                    btnEl.classList.remove('text-zinc-400', 'border-transparent', 'font-semibold');
                }
            } else {
                if (contentEl) contentEl.classList.add('hidden');
                if (btnEl) {
                    btnEl.classList.remove('text-zinc-950', 'dark:text-zinc-50', 'border-zinc-950', 'dark:border-zinc-50', 'font-bold');
                    btnEl.classList.add('text-zinc-400', 'border-transparent', 'font-semibold');
                }
            }
        });
    }

    function createNewForm() {
        currentEditingForm = {
            id: 0,
            title: 'Untitled Form',
            status: 'draft',
            settings: { steps: ['Step 1'] },
            blocks: []
        };
        const titleInp = document.getElementById('editor-form-title');
        if (titleInp) titleInp.value = currentEditingForm.title;

        const canvasName = document.getElementById('canvas-form-name');
        if (canvasName) canvasName.innerText = 'Untitled Form';

        const canvasSub = document.getElementById('canvas-form-subtitle');
        if (canvasSub) canvasSub.innerText = 'Fill out details below to submit request.';
        
        renderCoverImage();
        renderEditorBlocks();
        renderStepsBar();
        switchEditorView('build');
        switchLeftTab('fields');
        
        if (listState) listState.classList.add('hidden');
        if (editorState) { editorState.classList.remove('hidden'); editorState.classList.add('flex'); }
    }

    function loadFormIntoEditor(id) {
        jQuery.ajax({
            url: `/wp-json/cora/v1/forms/${id}`,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(form) {
                currentEditingForm = form;
                if (!currentEditingForm.settings) currentEditingForm.settings = {};
                if (!currentEditingForm.blocks) currentEditingForm.blocks = [];
                if (!currentEditingForm.settings.steps) currentEditingForm.settings.steps = ['Step 1'];

                const titleInp = document.getElementById('editor-form-title');
                if (titleInp) titleInp.value = form.title || '';

                const canvasName = document.getElementById('canvas-form-name');
                if (canvasName) canvasName.innerText = form.title || 'Untitled Form';

                const canvasSub = document.getElementById('canvas-form-subtitle');
                if (canvasSub) canvasSub.innerText = form.description || form.subtitle || 'Fill out details below to submit request.';
                
                const statusSel = document.getElementById('editor-form-status');
                if (statusSel) statusSel.value = form.status || 'draft';

                renderCoverImage();
                renderEditorBlocks();
                renderStepsBar();
                switchEditorView('build');
                switchLeftTab('fields');
                
                if (listState) listState.classList.add('hidden');
                if (editorState) { editorState.classList.remove('hidden'); editorState.classList.add('flex'); }
            },
            error: function() {
                window.coraShowToast && window.coraShowToast("Failed to load form.", "error");
            }
        });
    }

    function setAutoSaveStatus(status) {
        const statusEl = document.getElementById('editor-save-status');
        if (!statusEl) return;
        
        if (status === 'saving') {
            statusEl.innerHTML = `<svg class="animate-spin h-3 w-3 text-zinc-400 mr-1" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...`;
        } else if (status === 'saved') {
            statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Saved`;
        } else if (status === 'error') {
            statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Error`;
        }
    }

    function triggerAutoSave() {
        clearTimeout(autoSaveTimer);
        setAutoSaveStatus('saving');
        autoSaveTimer = setTimeout(() => {
            saveFormInternal();
        }, 1500);
    }

    function saveFormInternal(publish = false) {
        if (!currentEditingForm) return;

        const titleInp = document.getElementById('editor-form-title');
        if (titleInp) currentEditingForm.title = titleInp.value;
        
        const statusSel = document.getElementById('editor-form-status');
        if (statusSel) currentEditingForm.status = statusSel.value;

        jQuery.ajax({
            url: '/wp-json/cora/v1/forms',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            data: JSON.stringify(currentEditingForm),
            contentType: 'application/json',
            success: function(res) {
                if (res && res.id) {
                    currentEditingForm.id = res.id;
                }
                setAutoSaveStatus('saved');
                if (publish) {
                    window.coraShowToast && window.coraShowToast("Form saved and published!", "success");
                    fetchForms();
                }
            },
            error: function(err) {
                setAutoSaveStatus('error');
                if (publish) {
                    window.coraShowToast && window.coraShowToast("Failed to save form.", "error");
                }
            }
        });
    }

    function addFieldToForm(type, insertAfterIdx = null) {
        if (!currentEditingForm) return;
        if (!currentEditingForm.blocks) currentEditingForm.blocks = [];
        
        const meta = TYPE_META[type] || { label: type };
        const newBlock = {
            id: 'field_' + Math.random().toString(36).substr(2, 6),
            type: type,
            label: meta.label,
            description: '',
            required: false,
            visibility: 'always',
            choices: ['dropdown', 'multiple_choice', 'checkbox'].includes(type) ? [{label:'Option 1'}, {label:'Option 2'}] : undefined,
            price: type === 'payment' || type === 'stripe_payment' ? 100 : undefined,
            currency: 'INR'
        };

        if (insertAfterIdx !== null && insertAfterIdx >= 0) {
            currentEditingForm.blocks.splice(insertAfterIdx + 1, 0, newBlock);
            selectedBlockIndex = insertAfterIdx + 1;
        } else {
            currentEditingForm.blocks.push(newBlock);
            selectedBlockIndex = currentEditingForm.blocks.length - 1;
        }

        renderEditorBlocks();
        selectBlock(selectedBlockIndex);
        triggerAutoSave();
    }

    function renderEditorBlocks() {
        const container = document.getElementById('editor-blocks-container');
        if (!container) return;
        container.innerHTML = '';

        (currentEditingForm.blocks || []).forEach((block, idx) => {
            const meta = TYPE_META[block.type] || { label: block.type, badge: 'Unknown' };
            const div = document.createElement('div');
            
            let classStr = "group relative bg-white border border-zinc-200 rounded-xl p-4 cursor-pointer hover:border-zinc-300 hover:shadow-sm transition-all mb-4";
            if (idx === selectedBlockIndex) {
                classStr = "group relative bg-white border ring-2 ring-zinc-950 border-zinc-950 rounded-xl p-4 cursor-pointer transition-all mb-4";
            }
            div.className = classStr;
            div.dataset.index = idx;
            div.draggable = true;

            let previewHtml = '';
            if (['text','email','phone','number','hidden'].includes(block.type)) previewHtml = `<input type="text" class="w-full text-xs p-2 bg-zinc-50 border border-zinc-200 rounded" placeholder="Placeholder..." disabled />`;
            else if (block.type === 'long_text') previewHtml = `<textarea class="w-full text-xs p-2 bg-zinc-50 border border-zinc-200 rounded" placeholder="Enter text..." disabled rows="2"></textarea>`;
            else if (['dropdown'].includes(block.type)) previewHtml = `<select class="w-full text-xs p-2 bg-zinc-50 border border-zinc-200 rounded" disabled><option>Select option</option></select>`;
            else if (['multiple_choice', 'checkbox'].includes(block.type)) {
                previewHtml = `<div class="flex gap-2">` + (block.choices || []).slice(0,3).map(c => `<span class="px-2 py-1 bg-zinc-100 rounded text-[10px] text-zinc-600 border border-zinc-200">${c.label}</span>`).join('') + `</div>`;
            } else if (block.type === 'date') previewHtml = `<input type="date" class="w-full text-xs p-2 bg-zinc-50 border border-zinc-200 rounded" disabled />`;
            else if (block.type === 'file') previewHtml = `<button class="px-3 py-1.5 bg-zinc-100 rounded text-xs border border-zinc-200 text-zinc-600" disabled>Upload File</button>`;
            else if (block.type === 'signature') previewHtml = `<div class="w-full h-16 border border-dashed border-zinc-300 bg-zinc-50 flex items-center justify-center text-xs text-zinc-400">Draw Signature</div>`;
            else if (block.type === 'rating') previewHtml = `<div class="flex gap-1 text-zinc-300">★★★★★</div>`;
            else if (block.type === 'slider') previewHtml = `<input type="range" class="w-full" disabled />`;
            else if (block.type === 'payment' || block.type === 'stripe_payment') previewHtml = `<div class="flex items-center gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg> <span class="text-sm font-semibold">${block.price || 100} ${block.currency || 'INR'}</span></div>`;
            else if (block.type === 'header') previewHtml = `<h3 class="text-base font-bold text-zinc-900">${block.content || block.label || 'Section Heading'}</h3>`;
            else if (block.type === 'paragraph') previewHtml = `<p class="text-xs text-zinc-500 leading-relaxed">${block.content || block.label || 'Paragraph text...'}</p>`;
            else if (block.type === 'page_break') previewHtml = `<div class="flex items-center gap-2"><div class="flex-1 border-t-2 border-dashed border-zinc-300"></div><span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-2">Page Break</span><div class="flex-1 border-t-2 border-dashed border-zinc-300"></div></div>`;
            else if (block.type === 'divider') previewHtml = `<hr class="border-zinc-300" />`;
            else if (block.type === 'spacer') previewHtml = `<div class="h-8 bg-zinc-50 rounded border border-dashed border-zinc-200 flex items-center justify-center"><span class="text-[10px] text-zinc-300">Spacer</span></div>`;
            else if (block.type === 'formula') previewHtml = `<div class="text-xs font-mono text-zinc-500 bg-zinc-50 rounded px-2 py-1.5 border border-zinc-200">${block.formula || '= sum(field_a, field_b)'}</div>`;
            else if (block.type === 'input') {
                const iType = block.inputType || 'text';
                if (iType === 'textarea') {
                    previewHtml = `<textarea class="w-full text-xs p-2 bg-zinc-50 border border-zinc-200 rounded resize-none" placeholder="${block.placeholder || 'Enter text...'}" disabled rows="2"></textarea>`;
                } else {
                    previewHtml = `<input type="${iType}" class="w-full text-xs p-2 bg-zinc-50 border border-zinc-200 rounded" placeholder="${block.placeholder || 'Enter ' + (block.label || 'value')}" disabled />`;
                }
            } else previewHtml = `<input type="text" class="w-full text-xs p-2 bg-zinc-50 border border-zinc-200 rounded" placeholder="${block.placeholder || block.label || 'Enter value...'}" disabled />`;

            div.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="text-zinc-300 cursor-grab hover:text-zinc-500 drag-handle" draggable="true">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="none" fill="currentColor"><circle cx="8" cy="6" r="1.5"></circle><circle cx="8" cy="12" r="1.5"></circle><circle cx="8" cy="18" r="1.5"></circle><circle cx="14" cy="6" r="1.5"></circle><circle cx="14" cy="12" r="1.5"></circle><circle cx="14" cy="18" r="1.5"></circle></svg>
                        </div>
                        <span class="text-sm font-medium text-zinc-800">${block.label}</span>
                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-zinc-100 text-zinc-600">${meta.badge}</span>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="btn-duplicate-block h-7 w-7 rounded-lg border border-zinc-200 hover:bg-zinc-50 flex items-center justify-center text-zinc-400 hover:text-zinc-700">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        </button>
                        <button class="btn-block-settings h-7 w-7 rounded-lg border border-zinc-200 hover:bg-zinc-50 flex items-center justify-center text-zinc-400 hover:text-zinc-700">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </button>
                        <button class="btn-delete-block h-7 w-7 rounded-lg border border-zinc-200 hover:bg-red-50 hover:border-red-200 flex items-center justify-center text-zinc-400 hover:text-red-500">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="content-preview">
                    ${previewHtml}
                </div>
            `;

            div.addEventListener('click', (e) => {
                if (e.target.closest('button')) return;
                selectBlock(idx);
            });

            div.querySelector('.btn-delete-block').addEventListener('click', (e) => {
                e.stopPropagation();
                currentEditingForm.blocks.splice(idx, 1);
                if (selectedBlockIndex === idx) selectedBlockIndex = null;
                else if (selectedBlockIndex > idx) selectedBlockIndex--;
                renderEditorBlocks();
                switchLeftTab('fields');
                triggerAutoSave();
            });

            div.querySelector('.btn-duplicate-block').addEventListener('click', (e) => {
                e.stopPropagation();
                const clone = JSON.parse(JSON.stringify(block));
                clone.id = 'field_' + Math.random().toString(36).substr(2, 6);
                currentEditingForm.blocks.splice(idx + 1, 0, clone);
                selectBlock(idx + 1);
                triggerAutoSave();
            });

            div.querySelector('.btn-block-settings').addEventListener('click', (e) => {
                e.stopPropagation();
                selectBlock(idx);
            });

            div.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', 'reorder:' + idx);
            });

            div.addEventListener('dragover', (e) => {
                e.preventDefault();
                div.classList.add('border-zinc-500', 'shadow-sm');
            });

            div.addEventListener('dragleave', () => {
                div.classList.remove('border-zinc-500', 'shadow-sm');
            });

            div.addEventListener('drop', (e) => {
                e.preventDefault();
                div.classList.remove('border-blue-400');
                const data = e.dataTransfer.getData('text/plain');
                if (data.startsWith('reorder:')) {
                    const fromIdx = parseInt(data.replace('reorder:', ''));
                    const toIdx = idx;
                    if (fromIdx !== toIdx) {
                        const moved = currentEditingForm.blocks.splice(fromIdx, 1)[0];
                        currentEditingForm.blocks.splice(toIdx, 0, moved);
                        if (selectedBlockIndex === fromIdx) selectedBlockIndex = toIdx;
                        else if (selectedBlockIndex === toIdx) selectedBlockIndex = fromIdx;
                        renderEditorBlocks();
                        triggerAutoSave();
                    }
                } else if (data.startsWith('new:')) {
                    const type = data.replace('new:', '');
                    addFieldToForm(type, idx);
                }
            });

            container.appendChild(div);
        });

        // Dropzone at end
        const dropEnd = document.getElementById('editor-drop-zone');
        if (dropEnd) {
            dropEnd.addEventListener('dragover', (e) => e.preventDefault());
            dropEnd.addEventListener('drop', (e) => {
                const data = e.dataTransfer.getData('text/plain');
                if (data.startsWith('new:')) {
                    const type = data.replace('new:', '');
                    addFieldToForm(type, currentEditingForm.blocks.length - 1);
                }
            });
        }
    }

    function selectBlock(idx) {
        selectedBlockIndex = idx;
        renderEditorBlocks();
        switchLeftTab('settings');
        populateInspectorSettings(idx);
    }

    function populateInspectorSettings(idx) {
        if (idx === null || !currentEditingForm.blocks[idx]) return;
        const block = currentEditingForm.blocks[idx];
        const meta = TYPE_META[block.type] || { label: block.type };

        const typeTitle = document.getElementById('inspector-field-type-title');
        const idInp = document.getElementById('inspector-field-id');
        const labelInp = document.getElementById('inspector-field-label');
        const descInp = document.getElementById('inspector-field-desc');
        const reqInp = document.getElementById('inspector-field-required');
        
        const priceContainer = document.getElementById('inspector-price-container');
        const priceAmount = document.getElementById('inspector-price-amount');
        const priceCurrency = document.getElementById('inspector-price-currency');

        const choicesWrapper = document.getElementById('inspector-choices-wrapper');

        if (typeTitle) typeTitle.textContent = meta.label + ' Settings';
        if (idInp) idInp.value = block.id || '';
        if (labelInp) labelInp.value = block.label || '';
        if (descInp) descInp.value = block.description || '';
        if (reqInp) reqInp.checked = !!block.required;

        if (priceContainer) {
            if (block.type === 'payment' || block.type === 'stripe_payment') {
                priceContainer.classList.remove('hidden');
                if (priceAmount) priceAmount.value = block.price || 100;
                if (priceCurrency) priceCurrency.value = block.currency || 'INR';
            } else {
                priceContainer.classList.add('hidden');
            }
        }

        if (choicesWrapper) {
            if (['dropdown', 'multiple_choice', 'checkbox'].includes(block.type)) {
                choicesWrapper.classList.remove('hidden');
                let choicesHtml = '';
                (block.choices || []).forEach((c, cIdx) => {
                    choicesHtml += `
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" class="inspector-choice-input flex-1 text-xs px-2 py-1.5 border border-zinc-200 rounded" value="${c.label}" data-cidx="${cIdx}">
                            <button class="btn-delete-choice p-1 text-zinc-400 hover:text-red-500" data-cidx="${cIdx}">✕</button>
                        </div>
                    `;
                });
                choicesHtml += `<button id="btn-add-choice" class="text-xs text-blue-600 hover:underline">+ Add Choice</button>`;
                choicesWrapper.innerHTML = choicesHtml;

                choicesWrapper.querySelectorAll('.inspector-choice-input').forEach(inp => {
                    inp.addEventListener('input', (e) => {
                        const ci = parseInt(e.target.dataset.cidx);
                        currentEditingForm.blocks[selectedBlockIndex].choices[ci].label = e.target.value;
                        triggerAutoSave();
                        renderEditorBlocks();
                    });
                });
                choicesWrapper.querySelectorAll('.btn-delete-choice').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const ci = parseInt(e.target.dataset.cidx);
                        currentEditingForm.blocks[selectedBlockIndex].choices.splice(ci, 1);
                        triggerAutoSave();
                        renderEditorBlocks();
                        populateInspectorSettings(selectedBlockIndex);
                    });
                });
                document.getElementById('btn-add-choice')?.addEventListener('click', () => {
                    currentEditingForm.blocks[selectedBlockIndex].choices.push({label: 'New Option'});
                    triggerAutoSave();
                    renderEditorBlocks();
                    populateInspectorSettings(selectedBlockIndex);
                });

            } else {
                choicesWrapper.classList.add('hidden');
            }
        }
    }

    // Canvas Events
    document.getElementById('inspector-field-label')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null) {
            currentEditingForm.blocks[selectedBlockIndex].label = e.target.value;
            triggerAutoSave();
            renderEditorBlocks();
        }
    });

    document.getElementById('inspector-field-desc')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null) {
            currentEditingForm.blocks[selectedBlockIndex].description = e.target.value;
            triggerAutoSave();
        }
    });

    document.getElementById('inspector-field-required')?.addEventListener('change', (e) => {
        if (selectedBlockIndex !== null) {
            currentEditingForm.blocks[selectedBlockIndex].required = e.target.checked;
            triggerAutoSave();
        }
    });

    document.getElementById('inspector-price-amount')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null) {
            currentEditingForm.blocks[selectedBlockIndex].price = parseFloat(e.target.value) || 0;
            triggerAutoSave();
            renderEditorBlocks();
        }
    });
    
    document.getElementById('inspector-price-currency')?.addEventListener('change', (e) => {
        if (selectedBlockIndex !== null) {
            currentEditingForm.blocks[selectedBlockIndex].currency = e.target.value;
            triggerAutoSave();
            renderEditorBlocks();
        }
    });

    document.getElementById('btn-delete-field')?.addEventListener('click', () => {
        if (selectedBlockIndex !== null) {
            currentEditingForm.blocks.splice(selectedBlockIndex, 1);
            selectedBlockIndex = null;
            triggerAutoSave();
            renderEditorBlocks();
            switchLeftTab('fields');
        }
    });

    // Title & Subtitle Sync
    document.getElementById('editor-form-title')?.addEventListener('input', (e) => {
        const val = e.target.value;
        if (currentEditingForm) currentEditingForm.title = val;
        const cn = document.getElementById('canvas-form-name');
        if (cn) cn.innerText = val;
        triggerAutoSave();
    });

    document.getElementById('canvas-form-name')?.addEventListener('input', (e) => {
        const val = e.target.innerText.trim();
        if (currentEditingForm) currentEditingForm.title = val;
        const ti = document.getElementById('editor-form-title');
        if (ti) ti.value = val;
        triggerAutoSave();
    });

    document.getElementById('canvas-form-subtitle')?.addEventListener('input', (e) => {
        const val = e.target.innerText.trim();
        if (currentEditingForm) currentEditingForm.description = val;
        triggerAutoSave();
    });

    // Cover Image Header Dropzone & Picker
    const dropzone = document.getElementById('editor-header-dropzone');
    if (dropzone) {
        dropzone.addEventListener('click', (e) => {
            if (e.target.id === 'btn-remove-cover') {
                e.stopPropagation();
                if (currentEditingForm.settings) delete currentEditingForm.settings.cover_image;
                renderCoverImage();
                triggerAutoSave();
                return;
            }
            openCoverImagePicker();
        });
        dropzone.addEventListener('dragover', (e) => e.preventDefault());
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files && files.length > 0 && files[0].type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    if (!currentEditingForm.settings) currentEditingForm.settings = {};
                    currentEditingForm.settings.cover_image = evt.target.result;
                    renderCoverImage();
                    triggerAutoSave();
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }

    document.getElementById('btn-save-form')?.addEventListener('click', () => {
        saveFormInternal(true);
    });

    document.getElementById('btn-back-to-list')?.addEventListener('click', () => {
        window.location.hash = '#list';
    });

    // Palette Items (Add & Drag)
    document.querySelectorAll('[data-add-type]').forEach(item => {
        item.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', 'new:' + item.dataset.addType);
        });
        item.addEventListener('click', () => {
            addFieldToForm(item.dataset.addType);
        });
    });

    // Palette Search
    document.getElementById('palette-search-input')?.addEventListener('input', (e) => {
        const q = e.target.value.toLowerCase().trim();
        document.querySelectorAll('[data-add-type]').forEach(btn => {
            const text = btn.innerText.toLowerCase();
            if (!q || text.includes(q)) btn.classList.remove('hidden');
            else btn.classList.add('hidden');
        });
    });

    // Bottom Add Button
    document.getElementById('btn-add-element-bottom')?.addEventListener('click', () => {
        switchLeftTab('fields');
        addFieldToForm('text');
    });

    // Left Tab Listeners
    document.getElementById('btn-left-tab-fields')?.addEventListener('click', () => switchLeftTab('fields'));
    document.getElementById('btn-left-tab-settings')?.addEventListener('click', () => switchLeftTab('settings'));
    document.getElementById('btn-left-tab-form')?.addEventListener('click', () => switchLeftTab('form'));
    document.getElementById('btn-left-tab-integ')?.addEventListener('click', () => switchLeftTab('integ'));

    // Nav View Switchers
    document.getElementById('left-nav-build')?.addEventListener('click', () => switchEditorView('build'));
    document.getElementById('left-nav-submissions')?.addEventListener('click', () => switchEditorView('submissions'));
    document.getElementById('left-nav-templates')?.addEventListener('click', () => switchEditorView('templates'));
    document.getElementById('left-nav-integrations')?.addEventListener('click', () => switchEditorView('integrations'));

    // --- Submissions View ---
    function loadSubmissions() {
        const tbody = document.getElementById('submissions-table-body');
        if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center p-8 text-zinc-500">Loading...</td></tr>`;
        
        if (!currentEditingForm || !currentEditingForm.id) {
            if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center p-8 text-zinc-500">Save the form first to view submissions.</td></tr>`;
            return;
        }

        jQuery.ajax({
            url: `/wp-json/cora/v1/forms/${currentEditingForm.id}/submissions`,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(res) {
                renderSubmissionsTable(res || []);
            },
            error: function() {
                if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center p-8 text-zinc-500">Failed to load submissions.</td></tr>`;
            }
        });
    }

    function renderSubmissionsTable(submissions) {
        const tbody = document.getElementById('submissions-table-body');
        const countLabel = document.getElementById('submissions-count-label');
        if (countLabel) countLabel.textContent = `${submissions.length} entries`;

        if (!tbody) return;
        if (submissions.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center p-8 text-zinc-500">No submissions found.</td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        submissions.forEach(sub => {
            const data = sub.submitted_data || {};
            let name = data['Full Name'] || data['Name'] || 'Anonymous';
            let email = data['Email'] || data['Email Address'] || '';
            let date = new Date(sub.created_at).toLocaleDateString();
            let time = new Date(sub.created_at).toLocaleTimeString();
            let step = 'Completed';
            let statusClass = sub.is_partial == '1' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700';
            let statusLabel = sub.is_partial == '1' ? 'In Progress' : 'Completed';

            const tr = document.createElement('tr');
            tr.className = "border-b border-zinc-100 hover:bg-zinc-50";
            tr.innerHTML = `
                <td class="px-4 py-3.5"><input type="checkbox" class="submission-checkbox"></td>
                <td class="px-4 py-3.5 text-sm font-mono text-zinc-500">#${sub.id}</td>
                <td class="px-4 py-3.5">
                    <div class="text-xs font-medium text-zinc-700">${date}</div>
                    <div class="text-[10px] text-zinc-400">${time}</div>
                </td>
                <td class="px-4 py-3.5">
                    <div class="text-sm font-medium text-zinc-800">${name}</div>
                    <div class="text-xs text-zinc-400">${email}</div>
                </td>
                <td class="px-4 py-3.5">
                    <span class="text-xs text-zinc-600 bg-zinc-100 px-2 py-1 rounded-md font-medium">${step}</span>
                </td>
                <td class="px-4 py-3.5">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${statusClass}">${statusLabel}</span>
                </td>
                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-1">
                        <button class="btn-view-submission h-7 w-7 rounded-lg border border-zinc-200 hover:bg-zinc-50 flex items-center justify-center text-zinc-400 hover:text-zinc-700" data-subid="${sub.id}">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // --- Templates View ---
    function renderTemplatesGrid(category = 'all') {
        const grid = document.getElementById('templates-grid');
        if (!grid) return;
        grid.innerHTML = '';

        FORM_TEMPLATES.forEach(tpl => {
            const div = document.createElement('div');
            div.className = "bg-white border border-zinc-200 rounded-xl p-5 hover:border-zinc-300 hover:shadow-sm transition-all";
            div.innerHTML = `
                <div class="text-xs font-bold text-blue-600 mb-2">${tpl.category}</div>
                <h4 class="text-sm font-bold text-zinc-900 mb-1">${tpl.name}</h4>
                <p class="text-xs text-zinc-500 mb-4 h-8">${tpl.description}</p>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-zinc-400">${tpl.fieldCount} fields</span>
                    <button class="btn-use-template px-3 py-1.5 bg-zinc-900 text-white text-xs font-bold rounded-lg hover:bg-zinc-800" data-tpl="${tpl.id}">Use Template</button>
                </div>
            `;
            div.querySelector('.btn-use-template').addEventListener('click', () => {
                useTemplate(tpl.id);
            });
            grid.appendChild(div);
        });
    }

    function useTemplate(tplId) {
        const tpl = FORM_TEMPLATES.find(t => t.id === tplId);
        if (!tpl) return;

        currentEditingForm.blocks = tpl.fields.map(f => {
            const block = { ...f, id: 'field_' + Math.random().toString(36).substr(2, 6) };
            return block;
        });
        
        switchEditorView('build');
        switchLeftTab('fields');
        renderEditorBlocks();
        triggerAutoSave();
        window.coraShowToast && window.coraShowToast("Template applied!", "success");
    }

    // --- Integrations View ---
    function renderIntegrationsGrid(category = 'all') {
        const grid = document.getElementById('integrations-grid');
        if (!grid) return;
        grid.innerHTML = '';

        INTEGRATIONS.forEach(intg => {
            const div = document.createElement('div');
            div.className = "bg-white border border-zinc-200 rounded-xl p-5 flex items-center justify-between hover:border-zinc-300 hover:shadow-sm transition-all";
            div.innerHTML = `
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded bg-zinc-100 flex items-center justify-center font-bold text-zinc-400 text-lg">${intg.name[0]}</div>
                    <div>
                        <h4 class="text-sm font-bold text-zinc-900">${intg.name}</h4>
                        <div class="text-xs text-zinc-500">${intg.category}</div>
                    </div>
                </div>
                <button class="btn-connect-integration px-3 py-1.5 border border-zinc-200 text-zinc-700 text-xs font-bold rounded-lg hover:bg-zinc-50" data-id="${intg.id}">Connect</button>
            `;
            div.querySelector('.btn-connect-integration').addEventListener('click', () => {
                connectIntegration(intg.name);
            });
            grid.appendChild(div);
        });
    }

    function connectIntegration(name) {
        window.coraShowToast && window.coraShowToast(`Connecting to ${name}... (Coming Soon)`, "info");
    }

});
</script>
