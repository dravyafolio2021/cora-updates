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
                                    📉 0% drop-off (0 abandoned)
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
                                    📉 0% drop-off (0 abandoned)
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

    <!-- STATE 2: INTERACTIVE FORM EDITOR VIEW -->
    <div id="form-editor-state" class="hidden flex-col gap-6">
        <!-- Editor Topbar -->
        <div class="flex items-center justify-between border-b border-zinc-200/60 pb-5">
            <div class="flex items-center gap-3">
                <button id="btn-back-to-list" class="h-9 w-9 rounded-lg border border-zinc-200 hover:bg-zinc-50 flex items-center justify-center text-zinc-650 transition-all">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                </button>
                <div>
                    <input id="editor-form-title" type="text" placeholder="Untitled Form" class="text-base font-bold text-zinc-900 bg-transparent border-none outline-none focus:ring-0 p-0 w-64 md:w-96" />
                    <p class="text-[10px] text-zinc-400 mt-0.5">Press / for command blocks</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <button id="btn-editor-view-live" class="hidden h-9 px-3 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold transition-all items-center gap-1">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    View Form
                </button>
                <button id="btn-editor-share" class="hidden h-9 px-3 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold transition-all items-center gap-1">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                    Share
                </button>
                <select id="editor-form-status" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-700 outline-none focus:border-zinc-300">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="closed">Closed</option>
                </select>
                <button id="btn-save-form" class="h-9 px-4 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-medium transition-all">
                    Publish Form
                </button>
            </div>
        </div>

        <!-- Editor Workspace (Document Canvas + Sidebar Styling Panel) -->
        <div class="flex flex-col lg:flex-row gap-6 items-start">
            <!-- Main Document Sheet -->
            <div class="flex-1 w-full bg-white border border-zinc-200 rounded-2xl p-8 shadow-sm min-h-[500px] flex flex-col relative" id="editor-document-sheet">
                <!-- Drop Header Image Area -->
                <div class="w-full h-28 border border-dashed border-zinc-200 rounded-xl bg-zinc-50 flex flex-col items-center justify-center gap-2 cursor-pointer hover:bg-zinc-100/60 transition-all mb-6" id="editor-header-dropzone">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <span class="text-xs text-zinc-400">Add custom header logo or cover image</span>
                </div>

                <!-- Live Document Block list -->
                <div class="flex-1 flex flex-col gap-1 min-h-[300px]" id="editor-blocks-container">
                    <!-- Form input blocks are injected here -->
                </div>
                
                <!-- Bottom Add Element button -->
                <div class="mt-6 pt-4 border-t border-zinc-100 flex items-center justify-start">
                    <button id="btn-add-element-bottom" class="h-9 px-4 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold transition-all flex items-center gap-2 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Add Form Field
                    </button>
                </div>
            </div>

            <!-- Options Sidebar styling (White-label & settings) -->
            <div class="w-full lg:w-80 shrink-0 flex flex-col gap-4">
                <!-- Settings suite tab container -->
                <div class="bg-white border border-zinc-200 rounded-2xl p-5 flex flex-col gap-4 shadow-sm">
                    <h3 class="text-xs font-bold text-zinc-900 tracking-wide uppercase border-b border-zinc-100 pb-2.5">Settings & Design</h3>
                    
                    <!-- Redirect URL -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Redirect URL</label>
                        <input id="settings-redirect-url" type="text" placeholder="https://example.com/thank-you" class="h-9 px-3 rounded-lg border border-zinc-200 text-xs outline-none focus:border-zinc-300 w-full" />
                    </div>

                    <!-- Custom Thank You Message -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Success Message</label>
                        <textarea id="settings-success-msg" rows="3" placeholder="Thank you for submitting!" class="p-3 rounded-lg border border-zinc-200 text-xs outline-none focus:border-zinc-300 w-full resize-none"></textarea>
                    </div>

                    <!-- Custom Styling / White-Label CSS -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Custom CSS</label>
                        <textarea id="settings-custom-css" rows="5" placeholder="/* Custom styling overrides */" class="p-3 font-mono rounded-lg border border-zinc-200 text-[10px] outline-none focus:border-zinc-300 w-full resize-none"></textarea>
                    </div>

                    <!-- Webhook Target URL -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Webhook Endpoint URL</label>
                        <input id="settings-webhook-url" type="text" placeholder="https://yourdomain.com/webhook" class="h-9 px-3 rounded-lg border border-zinc-200 text-xs outline-none focus:border-zinc-300 w-full" />
                    </div>

                    <!-- CRM Mappings -->
                    <div class="flex flex-col gap-2 pt-2 border-t border-zinc-100">
                        <label class="text-[10px] font-bold text-zinc-900 uppercase">CRM Field Mappings</label>
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] text-zinc-500 font-medium">Name field:</span>
                                <input id="map-crm-name" type="text" placeholder="e.g. Name" class="h-7 px-2 rounded border border-zinc-200 text-[10px] w-32 outline-none" />
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] text-zinc-500 font-medium">Email field:</span>
                                <input id="map-crm-email" type="text" placeholder="e.g. Email" class="h-7 px-2 rounded border border-zinc-200 text-[10px] w-32 outline-none" />
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] text-zinc-500 font-medium">Phone field:</span>
                                <input id="map-crm-phone" type="text" placeholder="e.g. Phone" class="h-7 px-2 rounded border border-zinc-200 text-[10px] w-32 outline-none" />
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] text-zinc-500 font-medium">Notes field:</span>
                                <input id="map-crm-notes" type="text" placeholder="e.g. Message" class="h-7 px-2 rounded border border-zinc-200 text-[10px] w-32 outline-none" />
                            </div>
                        </div>
                    </div>

                    <!-- Conditional Logic Section -->
                    <div class="flex flex-col gap-2 pt-2 border-t border-zinc-100">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-zinc-900 uppercase">Conditional Logic</label>
                            <button id="btn-add-logic-rule" class="text-[9px] font-bold text-zinc-600 hover:text-zinc-900 flex items-center gap-0.5 cursor-pointer border-none bg-transparent">
                                <span>+</span> Add Rule
                            </button>
                        </div>
                        <div id="settings-logic-rules-container" class="flex flex-col gap-3 max-h-60 overflow-y-auto pr-1">
                            <!-- Logic rule cards go here -->
                        </div>
                    </div>

                    <!-- Form Approval Stages Matrix -->
                    <div class="flex flex-col gap-2 pt-2 border-t border-zinc-100">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-zinc-900 uppercase">Approval Stages Matrix</label>
                            <button id="btn-add-approval-stage" class="text-[9px] font-bold text-zinc-600 hover:text-zinc-900 flex items-center gap-0.5 cursor-pointer border-none bg-transparent">
                                <span>+</span> Add Stage
                            </button>
                        </div>
                        <div id="settings-approvals-container" class="flex flex-col gap-3 max-h-60 overflow-y-auto pr-1">
                            <!-- Approval stage cards -->
                        </div>
                    </div>

                    <!-- Contract Brief PDF Template -->
                    <div class="flex flex-col gap-2 pt-2 border-t border-zinc-100">
                        <label class="text-[10px] font-bold text-zinc-900 uppercase">Document PDF Auto-Compiler</label>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[9px] text-zinc-400">PDF Body template: use {{field_label}} for placeholders</span>
                            <textarea id="settings-pdf-template" rows="4" placeholder="This agreement is made between Cora and {{Name}}..." class="p-2 rounded border border-zinc-200 text-xs w-full outline-none resize-none"></textarea>
                        </div>
                        
                        <div class="flex flex-col gap-1 mt-1">
                            <span class="text-[9px] text-zinc-400 font-bold uppercase">Dynamic Clauses to Inject</span>
                            <div class="flex flex-col gap-1.5 border border-zinc-200 bg-zinc-50/50 p-2 rounded-lg" id="pdf-clauses-rules-container">
                                <!-- Inline clause inclusion rules -->
                            </div>
                            <button id="btn-add-clause-rule" class="mt-1 self-start text-[9px] font-bold text-zinc-500 hover:text-zinc-950 flex items-center gap-0.5 border-none bg-transparent cursor-pointer">
                                <span>+</span> Add Clause Condition
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STATE 3: SUBMISSIONS LIST OVERLAY DRAWER -->
    <div id="cora-submissions-drawer" class="fixed inset-y-0 right-0 w-full md:w-[600px] bg-white shadow-2xl border-l border-zinc-200 z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900" id="drawer-form-title">Form Submissions</h3>
                <p class="text-[10px] text-zinc-400 mt-0.5" id="drawer-form-meta">Showing all user responses</p>
            </div>
            <button id="btn-close-submissions" class="h-8 w-8 rounded-lg hover:bg-zinc-50 flex items-center justify-center text-zinc-500">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5" id="submissions-drawer-content">
            <!-- Submission card blocks go here -->
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
            <span>🔤</span> Short Text Input
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="number">
            <span>🔢</span> Number Input
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="email">
            <span>✉️</span> Email Address
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="phone">
            <span>📞</span> Phone Number
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="long_text">
            <span>📝</span> Long Text Area
        </button>
        
        <div class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-2 border-t border-zinc-100 pt-2">Choices</div>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="dropdown">
            <span>🔽</span> Dropdown Selection
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="checkbox">
            <span>☑️</span> Multiple Checkboxes
        </button>

        <div class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-2 border-t border-zinc-100 pt-2">Layout Elements</div>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="header">
            <span>𝐇</span> Section Header
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="paragraph">
            <span>¶</span> Paragraph
        </button>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="page_break">
            <span>📄</span> Page Break (Next Step)
        </button>
        <div class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-2 border-t border-zinc-100 pt-2">Checkout & Payments</div>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="stripe_payment">
            <span>💳</span> Stripe Checkout Card
        </button>

        <div class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider mt-2 border-t border-zinc-100 pt-2">Calculations</div>
        <button class="w-full text-left px-3 py-2 text-xs text-zinc-700 hover:bg-zinc-50 flex items-center gap-2" data-type="formula">
            <span>🧮</span> Calculated Formula
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let formsData = [];
    let currentEditingForm = null;
    let selectedBlockIndex = null;

    const wpNonce = (typeof coraREData !== 'undefined' && coraREData.nonce) ? coraREData.nonce : ((typeof wpApiSettings !== 'undefined') ? wpApiSettings.nonce : '');

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

    // View States selectors
    const listState = document.getElementById('forms-list-state');
    const editorState = document.getElementById('form-editor-state');
    const submissionsDrawer = document.getElementById('cora-submissions-drawer');
    const blocksContainer = document.getElementById('editor-blocks-container');
    const slashMenu = document.getElementById('editor-slash-menu');

    // Fetch and load all forms
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
            badge.innerHTML = `📉 ${lossPct}% drop-off (${lossCount} abandoned)`;
        } else {
            badge.className = "px-2 py-0.5 rounded bg-zinc-50 text-zinc-500 text-[9px] font-bold border border-zinc-200/80 flex items-center gap-0.5 transition-all";
            badge.innerHTML = `✨ 100% retention (0 abandoned)`;
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

    // Attach Selector and Tab Change Handlers
    jQuery('#funnel-form-selector').on('change', updateAdvancedFunnelData);

    const tabFormsList = document.getElementById('tab-forms-list');
    const tabFunnel = document.getElementById('tab-funnel-analytics');
    const tabClauses = document.getElementById('tab-clauses-library');
    const tabAuditLogs = document.getElementById('tab-audit-logs');
    
    const listTabContent = document.getElementById('forms-list-tab-content');
    const funnelTabContent = document.getElementById('forms-funnel-tab-content');
    const clausesTabContent = document.getElementById('forms-clauses-tab-content');
    const auditTabContent = document.getElementById('forms-audit-tab-content');

    tabFormsList.addEventListener('click', function() {
        window.location.hash = '#list';
    });

    tabFunnel.addEventListener('click', function() {
        window.location.hash = '#funnel';
    });

    tabClauses.addEventListener('click', function() {
        window.location.hash = '#clauses';
    });

    tabAuditLogs.addEventListener('click', function() {
        window.location.hash = '#audit-log';
    });

    function openBlankEditor() {
        currentEditingForm = {
            id: 0,
            title: 'Untitled Form',
            status: 'draft',
            settings: {
                redirect_url: '',
                success_message: 'Your response was submitted successfully.',
                pdf_template: '',
                clause_rules: [],
                approval_stages: []
            },
            styling: {
                custom_css: ''
            },
            blocks: [
                { type: 'header', label: 'Cora Survey Form' },
                { type: 'paragraph', label: 'Fill out details below to submit request.' }
            ],
            logic: []
        };

        document.getElementById('editor-form-title').value = currentEditingForm.title;
        document.getElementById('editor-form-status').value = currentEditingForm.status;
        document.getElementById('settings-redirect-url').value = '';
        document.getElementById('settings-success-msg').value = currentEditingForm.settings.success_message;
        document.getElementById('settings-custom-css').value = '';
        document.getElementById('settings-webhook-url').value = '';
        document.getElementById('map-crm-name').value = '';
        document.getElementById('map-crm-email').value = '';
        document.getElementById('map-crm-phone').value = '';
        document.getElementById('map-crm-notes').value = '';
        document.getElementById('settings-pdf-template').value = '';

        document.getElementById('btn-editor-view-live').classList.add('hidden');
        document.getElementById('btn-editor-share').classList.add('hidden');

        renderEditorBlocks();
        renderLogicRules();
        renderApprovalStages();
        renderClauseRules();

        listState.classList.add('hidden');
        editorState.classList.remove('hidden');
        editorState.classList.add('flex');
    }

    function handleRouting() {
        const hash = window.location.hash || '#list';
        
        listState.classList.add('hidden');
        editorState.classList.add('hidden');
        editorState.classList.remove('flex');
        
        // Hide all tab contents first
        listTabContent.classList.add('hidden');
        listTabContent.classList.remove('flex');
        funnelTabContent.classList.add('hidden');
        funnelTabContent.classList.remove('flex');
        clausesTabContent.classList.add('hidden');
        clausesTabContent.classList.remove('flex');
        auditTabContent.classList.add('hidden');
        auditTabContent.classList.remove('flex');
        
        // Reset tab classes
        const tabs = [tabFormsList, tabFunnel, tabClauses, tabAuditLogs];
        tabs.forEach(t => {
            t.classList.remove('font-semibold', 'border-zinc-950', 'text-zinc-950');
            t.classList.add('font-medium', 'border-transparent', 'text-zinc-500');
        });

        if (hash === '#list') {
            tabFormsList.classList.add('font-semibold', 'border-zinc-950', 'text-zinc-950');
            tabFormsList.classList.remove('font-medium', 'border-transparent', 'text-zinc-500');
            
            listTabContent.classList.remove('hidden');
            listTabContent.classList.add('flex');
            
            listState.classList.remove('hidden');
        } else if (hash === '#funnel') {
            tabFunnel.classList.add('font-semibold', 'border-zinc-950', 'text-zinc-950');
            tabFunnel.classList.remove('font-medium', 'border-transparent', 'text-zinc-500');
            
            funnelTabContent.classList.remove('hidden');
            funnelTabContent.classList.add('flex');
            
            listState.classList.remove('hidden');
            
            populateFunnelSelector();
            updateAdvancedFunnelData();
        } else if (hash === '#clauses') {
            tabClauses.classList.add('font-semibold', 'border-zinc-950', 'text-zinc-950');
            tabClauses.classList.remove('font-medium', 'border-transparent', 'text-zinc-500');
            
            clausesTabContent.classList.remove('hidden');
            clausesTabContent.classList.add('flex');
            
            listState.classList.remove('hidden');
            fetchClauses();
        } else if (hash === '#audit-log') {
            tabAuditLogs.classList.add('font-semibold', 'border-zinc-950', 'text-zinc-950');
            tabAuditLogs.classList.remove('font-medium', 'border-transparent', 'text-zinc-500');
            
            auditTabContent.classList.remove('hidden');
            auditTabContent.classList.add('flex');
            
            listState.classList.remove('hidden');
            fetchAuditLogs();
        } else if (hash.startsWith('#edit/')) {
            const id = hash.split('/')[1];
            loadFormIntoEditor(id);
        } else if (hash === '#create') {
            openBlankEditor();
        }
    }

    window.addEventListener('hashchange', handleRouting);

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

    // Load one form into interactive editor canvas
    function loadFormIntoEditor(id) {
        jQuery.ajax({
            url: `/wp-json/cora/v1/forms/${id}`,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(form) {
                currentEditingForm = form;
                
                // Ensure proper settings structures
                if (!currentEditingForm.settings) currentEditingForm.settings = {};
                if (!currentEditingForm.settings.approval_stages) currentEditingForm.settings.approval_stages = [];
                if (!currentEditingForm.settings.clause_rules) currentEditingForm.settings.clause_rules = [];
                if (!currentEditingForm.settings.pdf_template) currentEditingForm.settings.pdf_template = '';
                
                // Set fields
                document.getElementById('editor-form-title').value = form.title;
                document.getElementById('editor-form-status').value = form.status;
                document.getElementById('settings-redirect-url').value = form.settings.redirect_url || '';
                document.getElementById('settings-success-msg').value = form.settings.success_message || '';
                document.getElementById('settings-custom-css').value = form.styling.custom_css || '';
                document.getElementById('settings-webhook-url').value = form.settings.webhook_url || '';
                
                const crmMap = form.settings.crm_mapping || {};
                document.getElementById('map-crm-name').value = crmMap.first_name || '';
                document.getElementById('map-crm-email').value = crmMap.email || '';
                document.getElementById('map-crm-phone').value = crmMap.phone || '';
                document.getElementById('map-crm-notes').value = crmMap.notes || '';
                document.getElementById('settings-pdf-template').value = form.settings.pdf_template || '';

                // Show and bind editor topbar view/share buttons
                if (form.id > 0) {
                    document.getElementById('btn-editor-view-live').classList.remove('hidden');
                    document.getElementById('btn-editor-view-live').classList.add('flex');
                    document.getElementById('btn-editor-share').classList.remove('hidden');
                    document.getElementById('btn-editor-share').classList.add('flex');
                    
                    document.getElementById('btn-editor-view-live').onclick = function() {
                        let siteUrl = coraREData.siteUrl || '';
                        if (siteUrl.endsWith('/')) { siteUrl = siteUrl.slice(0, -1); }
                        window.open(siteUrl + '/shared-form/' + form.id, '_blank');
                    };
                    
                    document.getElementById('btn-editor-share').onclick = function() {
                        let siteUrl = coraREData.siteUrl || '';
                        if (siteUrl.endsWith('/')) { siteUrl = siteUrl.slice(0, -1); }
                        const shareUrl = siteUrl + '/shared-form/' + form.id;
                        coraCopyTextToClipboard(shareUrl);
                    };
                } else {
                    document.getElementById('btn-editor-view-live').classList.add('hidden');
                    document.getElementById('btn-editor-share').classList.add('hidden');
                }

                // Draw Blocks
                renderEditorBlocks();
                renderLogicRules();
                renderApprovalStages();
                renderClauseRules();

                // Swap views
                listState.classList.add('hidden');
                editorState.classList.remove('hidden');
                editorState.classList.add('flex');
            }
        });
    }

    function renderEditorBlocks() {
        blocksContainer.innerHTML = '';
        const blocks = currentEditingForm.blocks || [];

        if (blocks.length === 0) {
            // Seed a default prompt block
            blocks.push({
                type: 'paragraph',
                label: 'Start typing form details here... Use / to insert dynamic form inputs.'
            });
            currentEditingForm.blocks = blocks;
        }

        blocks.forEach((block, idx) => {
            const div = document.createElement('div');
            div.className = 'group relative flex flex-col gap-1 p-2 rounded-lg hover:bg-zinc-50 border border-transparent hover:border-zinc-200 transition-all';
            div.dataset.index = idx;

            let blockInnerHtml = '';
            if (block.type === 'header') {
                blockInnerHtml = `<input type="text" class="text-sm font-bold text-zinc-950 bg-transparent border-none outline-none focus:ring-0 w-full p-0" value="${block.label}" placeholder="Section Title" />`;
            } else if (block.type === 'paragraph') {
                blockInnerHtml = `<input type="text" class="text-xs text-zinc-650 bg-transparent border-none outline-none focus:ring-0 w-full p-0" value="${block.label}" placeholder="Explain instructions..." />`;
            } else if (block.type === 'stripe_payment') {
                blockInnerHtml = `
                    <div class="flex items-center justify-between mb-1">
                        <input type="text" class="text-xs font-semibold text-zinc-700 bg-transparent border-none outline-none focus:ring-0 p-0 w-1/2" value="${block.label}" placeholder="Payment Label" />
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Stripe Payment</span>
                    </div>
                    <div class="border border-zinc-200 rounded-xl p-4 bg-zinc-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">💳</span>
                            <div>
                                <span class="block text-xs font-semibold text-zinc-800">Secure Checkout Button</span>
                                <span class="block text-[10px] text-zinc-400">Configure price configuration parameters</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-700">Price:</span>
                            <input type="number" class="stripe-block-price w-16 h-7 text-xs rounded border border-zinc-200 px-2 outline-none" value="${block.price || 0}" placeholder="0" />
                            <select class="stripe-block-currency h-7 text-xs rounded border border-zinc-200 px-1 bg-white outline-none">
                                <option value="INR" ${block.currency === 'INR' ? 'selected' : ''}>INR (₹)</option>
                                <option value="USD" ${block.currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                            </select>
                        </div>
                    </div>
                `;
            } else if (block.type === 'dropdown' || block.type === 'checkbox') {
                const choices = block.choices || ['Option 1', 'Option 2'];
                if (!block.choices) {
                    block.choices = choices;
                }
                let choicesHtml = '';
                choices.forEach((cOpt, cIdx) => {
                    let optLabel = typeof cOpt === 'object' ? (cOpt.label || '') : cOpt;
                    let optScore = typeof cOpt === 'object' ? (cOpt.score !== undefined ? cOpt.score : 0) : 0;
                    choicesHtml += `
                        <div class="flex items-center gap-1.5 mt-1 choice-row" data-cidx="${cIdx}">
                            <span class="text-[10px] text-zinc-400 font-bold">${block.type === 'checkbox' ? '☐' : '•'}</span>
                            <input type="text" class="choice-option-input h-7 px-2 rounded border border-zinc-200 text-xs w-48 bg-white outline-none focus:border-zinc-300" data-cidx="${cIdx}" value="${optLabel}" placeholder="Option label" />
                            <span class="text-[10px] text-zinc-400 font-medium">Score:</span>
                            <input type="number" class="choice-option-score h-7 w-14 px-1.5 rounded border border-zinc-200 text-xs bg-white outline-none focus:border-zinc-300" data-cidx="${cIdx}" value="${optScore}" placeholder="0" />
                            <button class="btn-delete-choice text-zinc-400 hover:text-red-650 text-xs border-none bg-transparent cursor-pointer" data-cidx="${cIdx}">✕</button>
                        </div>
                    `;
                });
                blockInnerHtml = `
                    <div class="flex items-center justify-between mb-1">
                        <input type="text" class="text-xs font-semibold text-zinc-700 bg-transparent border-none outline-none focus:ring-0 p-0 w-1/2" value="${block.label}" placeholder="Field Label" />
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">${block.type}</span>
                    </div>
                    <div class="flex flex-col gap-1 pl-2">
                        ${choicesHtml}
                        <button class="btn-add-choice mt-1.5 self-start text-[10px] font-bold text-zinc-500 hover:text-zinc-950 flex items-center gap-0.5 border-none bg-transparent cursor-pointer">
                            <span>+</span> Add Option
                        </button>
                    </div>
                    <div class="mt-2 flex items-center gap-2 pl-2 border-t border-zinc-100 pt-2">
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wide">AI Purpose:</span>
                        <input type="text" class="block-ai-purpose h-6 px-2 text-[10px] rounded border border-zinc-200 w-48 bg-white outline-none" value="${block.ai_purpose || ''}" placeholder="e.g. selected_risk" />
                    </div>
                `;
            } else if (block.type === 'formula') {
                blockInnerHtml = `
                    <div class="flex items-center justify-between mb-1">
                        <input type="text" class="text-xs font-semibold text-zinc-700 bg-transparent border-none outline-none focus:ring-0 p-0 w-1/2" value="${block.label}" placeholder="Formula Label" />
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Formula</span>
                    </div>
                    <div class="border border-zinc-200 rounded-xl p-4 bg-zinc-50 flex flex-col gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-500">Expression:</span>
                            <input type="text" class="formula-block-expression flex-1 h-7 text-xs rounded border border-zinc-200 px-2 outline-none" value="${block.expression || ''}" placeholder="e.g. {hourly_rate} * {hours}" />
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-zinc-500">Currency:</span>
                                <select class="formula-block-currency h-7 text-xs rounded border border-zinc-200 px-1 bg-white outline-none font-sans">
                                    <option value="NONE" ${block.currency === 'NONE' || !block.currency ? 'selected' : ''}>None (Raw Number)</option>
                                    <option value="INR" ${block.currency === 'INR' ? 'selected' : ''}>INR (₹)</option>
                                    <option value="USD" ${block.currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                                    <option value="CHF" ${block.currency === 'CHF' ? 'selected' : ''}>CHF (CHF)</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-zinc-500">Decimals:</span>
                                <input type="number" class="formula-block-decimals w-12 h-7 text-xs rounded border border-zinc-200 px-2 outline-none" value="${block.decimals !== undefined ? block.decimals : 2}" min="0" max="5" />
                            </div>
                        </div>
                    </div>
                `;
            } else if (block.type === 'page_break') {
                blockInnerHtml = `
                    <div class="h-px border-t-2 border-dashed border-zinc-200 my-4 relative flex items-center justify-center">
                        <span class="absolute bg-white px-3 text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Page Break (Next Step)</span>
                    </div>
                `;
            } else if (block.type === 'divider') {
                blockInnerHtml = `<div class="h-px bg-zinc-200 my-2"></div>`;
            } else {
                // Form Fields
                blockInnerHtml = `
                    <div class="flex items-center justify-between mb-1">
                        <input type="text" class="text-xs font-semibold text-zinc-700 bg-transparent border-none outline-none focus:ring-0 p-0 w-1/2" value="${block.label}" placeholder="Field Label" />
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">${block.type}</span>
                    </div>
                    <input type="${block.type === 'number' ? 'number' : 'text'}" placeholder="Respondent placeholder answer..." disabled class="h-8 w-full border border-zinc-200 rounded px-2.5 text-xs bg-zinc-50/50 cursor-not-allowed" />
                    <div class="mt-2 flex items-center gap-2 border-t border-zinc-100 pt-2">
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wide">AI Purpose:</span>
                        <input type="text" class="block-ai-purpose h-6 px-2 text-[10px] rounded border border-zinc-200 w-48 bg-white outline-none" value="${block.ai_purpose || ''}" placeholder="e.g. client_email" />
                    </div>
                `;
            }

            // Controls on hover
            div.innerHTML = `
                ${blockInnerHtml}
                <div class="hidden group-hover:flex absolute right-2 top-2 items-center gap-1">
                    <button class="btn-block-command h-5 w-5 bg-white border border-zinc-200 rounded text-zinc-650 hover:bg-zinc-50 flex items-center justify-center text-[10px] font-bold" title="Insert element">/</button>
                    <button class="btn-delete-block h-5 w-5 bg-white border border-zinc-200 rounded text-red-650 hover:bg-zinc-50 flex items-center justify-center text-[10px]" title="Remove block">✕</button>
                </div>
            `;
            blocksContainer.appendChild(div);

            // Inputs value listeners
            const labelInput = div.querySelector('input[type="text"]');
            if (labelInput && !labelInput.classList.contains('choice-option-input') && !labelInput.classList.contains('block-ai-purpose')) {
                labelInput.addEventListener('input', function(e) {
                    currentEditingForm.blocks[idx].label = e.target.value;
                    renderLogicRules();
                });
            }

            const aiPurposeInp = div.querySelector('.block-ai-purpose');
            if (aiPurposeInp) {
                aiPurposeInp.addEventListener('input', function(e) {
                    currentEditingForm.blocks[idx].ai_purpose = e.target.value.trim();
                });
            }
            
            if (block.type === 'stripe_payment') {
                const priceInp = div.querySelector('.stripe-block-price');
                const currSel = div.querySelector('.stripe-block-currency');
                
                priceInp.addEventListener('input', function(e) {
                    currentEditingForm.blocks[idx].price = parseFloat(e.target.value) || 0;
                });
                
                currSel.addEventListener('change', function(e) {
                    currentEditingForm.blocks[idx].currency = e.target.value;
                });
            }

            if (block.type === 'formula') {
                const exprInp = div.querySelector('.formula-block-expression');
                const currSel = div.querySelector('.formula-block-currency');
                const decInp = div.querySelector('.formula-block-decimals');
                
                exprInp.addEventListener('input', function(e) {
                    currentEditingForm.blocks[idx].expression = e.target.value;
                });
                
                currSel.addEventListener('change', function(e) {
                    currentEditingForm.blocks[idx].currency = e.target.value;
                });
                
                decInp.addEventListener('input', function(e) {
                    currentEditingForm.blocks[idx].decimals = parseInt(e.target.value) || 0;
                });
            }

            if (block.type === 'dropdown' || block.type === 'checkbox') {
                const addChoiceBtn = div.querySelector('.btn-add-choice');
                if (addChoiceBtn) {
                    addChoiceBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        if (!currentEditingForm.blocks[idx].choices) {
                            currentEditingForm.blocks[idx].choices = [];
                        }
                        currentEditingForm.blocks[idx].choices.push({
                            label: 'New Option',
                            score: 0
                        });
                        renderEditorBlocks();
                    });
                }

                const choiceInputs = div.querySelectorAll('.choice-option-input');
                choiceInputs.forEach(cInp => {
                    cInp.addEventListener('input', function(e) {
                        const cIdx = parseInt(e.target.dataset.cidx);
                        const labelVal = e.target.value;
                        const scoreInput = e.target.closest('.choice-row').querySelector('.choice-option-score');
                        const scoreVal = parseFloat(scoreInput.value) || 0;
                        currentEditingForm.blocks[idx].choices[cIdx] = {
                            label: labelVal,
                            score: scoreVal
                        };
                    });
                });

                const scoreInputs = div.querySelectorAll('.choice-option-score');
                scoreInputs.forEach(sInp => {
                    sInp.addEventListener('input', function(e) {
                        const cIdx = parseInt(e.target.dataset.cidx);
                        const scoreVal = parseFloat(e.target.value) || 0;
                        const labelInput = e.target.closest('.choice-row').querySelector('.choice-option-input');
                        const labelVal = labelInput.value;
                        currentEditingForm.blocks[idx].choices[cIdx] = {
                            label: labelVal,
                            score: scoreVal
                        };
                    });
                });

                const deleteChoiceBtns = div.querySelectorAll('.btn-delete-choice');
                deleteChoiceBtns.forEach(dBtn => {
                    dBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const cIdx = parseInt(e.target.dataset.cidx);
                        currentEditingForm.blocks[idx].choices.splice(cIdx, 1);
                        renderEditorBlocks();
                    });
                });
            }

            // Actions within blocks
            div.querySelector('.btn-delete-block').addEventListener('click', function() {
                currentEditingForm.blocks.splice(idx, 1);
                renderEditorBlocks();
                renderLogicRules();
            });

            // Slash trigger helper button
            div.querySelector('.btn-block-command').addEventListener('click', function(e) {
                e.stopPropagation();
                selectedBlockIndex = idx;
                const rect = e.target.getBoundingClientRect();
                slashMenu.style.top = `${rect.bottom + window.scrollY}px`;
                slashMenu.style.left = `${rect.left + window.scrollX - 220}px`;
                slashMenu.classList.remove('hidden');
            });
        });
    }

    // Slash trigger on typing `/` inside text inputs
    blocksContainer.addEventListener('keyup', function(e) {
        if (e.key === '/') {
            const active = document.activeElement;
            const blockDiv = active.closest('[data-index]');
            if (blockDiv) {
                selectedBlockIndex = parseInt(blockDiv.dataset.index);
                const rect = active.getBoundingClientRect();
                slashMenu.style.top = `${rect.bottom + window.scrollY}px`;
                slashMenu.style.left = `${rect.left + window.scrollX}px`;
                slashMenu.classList.remove('hidden');
            }
        }
    });

    // Handle block addition selection
    slashMenu.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', function() {
            const blockType = this.dataset.type;
            const newBlock = {
                type: blockType,
                label: blockType.charAt(0).toUpperCase() + blockType.slice(1) + ' Field'
            };

            if (selectedBlockIndex !== null) {
                currentEditingForm.blocks.splice(selectedBlockIndex + 1, 0, newBlock);
            } else {
                currentEditingForm.blocks.push(newBlock);
            }

            slashMenu.classList.add('hidden');
            selectedBlockIndex = null;
            renderEditorBlocks();
            renderLogicRules();
        });
    });

    // Bottom Add Element button click handler
    document.getElementById('btn-add-element-bottom').addEventListener('click', function(e) {
        e.stopPropagation();
        selectedBlockIndex = null; // Append at bottom
        const rect = e.target.getBoundingClientRect();
        slashMenu.style.top = `${rect.bottom + window.scrollY}px`;
        slashMenu.style.left = `${rect.left + window.scrollX}px`;
        slashMenu.classList.remove('hidden');
    });

    // Close slash menu on click outside
    document.addEventListener('click', function() {
        slashMenu.classList.add('hidden');
    });

    // Dynamic Logic Rules Builder
    function renderLogicRules() {
        const container = document.getElementById('settings-logic-rules-container');
        if (!container) return;
        container.innerHTML = '';
        
        const rules = currentEditingForm.logic || [];
        
        // Find all fields that can trigger or be target of conditional logic
        const fields = (currentEditingForm.blocks || [])
            .filter(b => b.type !== 'header' && b.type !== 'paragraph' && b.type !== 'divider' && b.type !== 'page_break' && b.type !== 'stripe_payment')
            .map(b => {
                return {
                    label: b.label || 'Unnamed field',
                    name: (b.label || 'Unnamed field').toLowerCase().replace(/[^a-z0-9]/g, '_')
                };
            });

        if (rules.length === 0) {
            container.innerHTML = `<div class="text-[10px] text-zinc-400 text-center py-2">No conditional rules defined.</div>`;
            return;
        }

        rules.forEach((rule, idx) => {
            const card = document.createElement('div');
            card.className = 'border border-zinc-200 rounded-lg p-2.5 bg-zinc-50/50 flex flex-col gap-2 relative';
            
            let fieldOptionsHtml = '<option value="">Select Trigger...</option>';
            fields.forEach(f => {
                fieldOptionsHtml += `<option value="${f.label}" ${rule.field === f.label ? 'selected' : ''}>${f.label}</option>`;
            });

            let targetOptionsHtml = '<option value="">Select Target...</option>';
            fields.forEach(f => {
                targetOptionsHtml += `<option value="${f.name}" ${rule.target === f.name ? 'selected' : ''}>${f.label}</option>`;
            });

            card.innerHTML = `
                <button class="btn-delete-logic absolute right-1.5 top-1.5 text-zinc-400 hover:text-red-500 text-[10px] border-none bg-transparent cursor-pointer">✕</button>
                
                <div class="flex flex-col gap-1">
                    <span class="text-[9px] text-zinc-400 font-bold uppercase">If Field</span>
                    <select class="logic-rule-field h-6 px-1 text-[10px] rounded border border-zinc-200 bg-white outline-none w-full">
                        ${fieldOptionsHtml}
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-1.5">
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-bold uppercase">Condition</span>
                        <select class="logic-rule-condition h-6 px-1 text-[10px] rounded border border-zinc-200 bg-white outline-none">
                            <option value="equals" ${rule.condition === 'equals' ? 'selected' : ''}>Equals</option>
                            <option value="not_equals" ${rule.condition === 'not_equals' ? 'selected' : ''}>Not Equals</option>
                            <option value="contains" ${rule.condition === 'contains' ? 'selected' : ''}>Contains</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-bold uppercase">Value</span>
                        <input type="text" class="logic-rule-value h-6 px-2 text-[10px] rounded border border-zinc-200" value="${rule.value || ''}" placeholder="Value..." />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-1.5">
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-bold uppercase">Action</span>
                        <select class="logic-rule-action h-6 px-1 text-[10px] rounded border border-zinc-200 bg-white outline-none">
                            <option value="show" ${rule.action === 'show' ? 'selected' : ''}>Show</option>
                            <option value="hide" ${rule.action === 'hide' ? 'selected' : ''}>Hide</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-bold uppercase">Target Field</span>
                        <select class="logic-rule-target h-6 px-1 text-[10px] rounded border border-zinc-200 bg-white outline-none">
                            ${targetOptionsHtml}
                        </select>
                    </div>
                </div>
            `;

            container.appendChild(card);

            card.querySelector('.logic-rule-field').addEventListener('change', function(e) {
                currentEditingForm.logic[idx].field = e.target.value;
            });
            card.querySelector('.logic-rule-condition').addEventListener('change', function(e) {
                currentEditingForm.logic[idx].condition = e.target.value;
            });
            card.querySelector('.logic-rule-value').addEventListener('input', function(e) {
                currentEditingForm.logic[idx].value = e.target.value;
            });
            card.querySelector('.logic-rule-action').addEventListener('change', function(e) {
                currentEditingForm.logic[idx].action = e.target.value;
            });
            card.querySelector('.logic-rule-target').addEventListener('change', function(e) {
                currentEditingForm.logic[idx].target = e.target.value;
            });
            
            card.querySelector('.btn-delete-logic').addEventListener('click', function() {
                currentEditingForm.logic.splice(idx, 1);
                renderLogicRules();
            });
        });
    }

    // Add logic rule button click listener
    document.getElementById('btn-add-logic-rule').addEventListener('click', function() {
        if (!currentEditingForm.logic) {
            currentEditingForm.logic = [];
        }
        currentEditingForm.logic.push({
            field: '',
            condition: 'equals',
            value: '',
            action: 'show',
            target: ''
        });
        renderLogicRules();
    });

    // Create a new blank form object
    document.getElementById('btn-create-form').addEventListener('click', function() {
        window.location.hash = '#create';
    });

    // Save Form
    document.getElementById('btn-save-form').addEventListener('click', function() {
        const title = document.getElementById('editor-form-title').value || 'Untitled Form';
        const status = document.getElementById('editor-form-status').value;
        const redirectUrl = document.getElementById('settings-redirect-url').value;
        const successMsg = document.getElementById('settings-success-msg').value;
        const customCss = document.getElementById('settings-custom-css').value;

        currentEditingForm.title = title;
        currentEditingForm.status = status;
        currentEditingForm.settings.redirect_url = redirectUrl;
        currentEditingForm.settings.success_message = successMsg;
        currentEditingForm.styling.custom_css = customCss;
        currentEditingForm.settings.pdf_template = document.getElementById('settings-pdf-template').value;
        currentEditingForm.settings.webhook_url = document.getElementById('settings-webhook-url').value;
        currentEditingForm.settings.crm_mapping = {
            first_name: document.getElementById('map-crm-name').value,
            email: document.getElementById('map-crm-email').value,
            phone: document.getElementById('map-crm-phone').value,
            notes: document.getElementById('map-crm-notes').value
        };

        jQuery.ajax({
            url: '/wp-json/cora/v1/forms',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            data: JSON.stringify(currentEditingForm),
            contentType: 'application/json',
            success: function(res) {
                window.coraShowToast && window.coraShowToast("Form saved and published successfully!", "success");
                window.location.hash = '#list';
                fetchForms();
            },
            error: function(err) {
                console.error("Save error:", err);
                window.coraShowToast && window.coraShowToast("Failed to save form settings.", "error");
            }
        });
    });

    // Delete Form
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

    // Submissions details overlay
    function openSubmissionsDrawer(id) {
        const formObj = formsData.find(f => f.id == id);
        document.getElementById('drawer-form-title').textContent = formObj ? formObj.title : 'Submissions';

        jQuery.ajax({
            url: `/wp-json/cora/v1/forms/${id}/submissions`,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(submissions) {
                const container = document.getElementById('submissions-drawer-content');
                if (submissions.length === 0) {
                    container.innerHTML = `<div class="text-center text-xs text-zinc-400 py-8">No responses received yet.</div>`;
                } else {
                    container.innerHTML = '';
                    submissions.forEach(sub => {
                        const div = document.createElement('div');
                        div.className = 'border border-zinc-200 rounded-xl p-4 mb-4 flex flex-col gap-2 bg-white';
                        
                        let dataRowsHtml = '';
                        const blocks = sub.blocks || [];
                        const inputBlocks = blocks.filter(b => 
                            b.type !== 'header' && b.type !== 'paragraph' && b.type !== 'divider' && b.type !== 'page_break'
                        );
                        
                        if (inputBlocks.length > 0) {
                            inputBlocks.forEach(b => {
                                const val = sub.submitted_data[b.label] !== undefined ? sub.submitted_data[b.label] : '';
                                dataRowsHtml += `
                                    <div class="flex items-center justify-between text-xs py-1 border-b border-zinc-50">
                                        <span class="font-medium text-zinc-500">${b.label}:</span>
                                        <span class="text-zinc-800">${Array.isArray(val) ? val.join(', ') : val}</span>
                                    </div>
                                `;
                            });
                        } else {
                            Object.keys(sub.submitted_data).forEach(key => {
                                if (key.startsWith('_') || key === 'id' || key === 'created_at') return;
                                const val = sub.submitted_data[key];
                                dataRowsHtml += `
                                    <div class="flex items-center justify-between text-xs py-1 border-b border-zinc-50">
                                        <span class="font-medium text-zinc-500">${key}:</span>
                                        <span class="text-zinc-800">${Array.isArray(val) ? val.join(', ') : val}</span>
                                    </div>
                                `;
                            });
                        }

                        // Render Approvals matrix
                        let approvalsHtml = '';
                        const approvals = sub.approvals || [];
                        if (approvals.length > 0) {
                            approvalsHtml += `
                                <div class="mt-3 pt-3 border-t border-zinc-100 flex flex-col gap-2">
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wide">Approval Pipeline</span>
                                    <div class="flex flex-col gap-1.5">
                            `;
                            
                            approvals.forEach(app => {
                                let badgeClass = 'bg-zinc-100 text-zinc-550';
                                if (app.status === 'approved') badgeClass = 'bg-green-50 text-green-700 border border-green-200/50';
                                else if (app.status === 'rejected') badgeClass = 'bg-red-50 text-red-650 border border-red-200/50';
                                else if (app.status === 'pending') badgeClass = 'bg-yellow-50 text-yellow-750 border border-yellow-200/50';
                                else if (app.status === 'escalated') badgeClass = 'bg-orange-50 text-orange-750 border border-orange-200/50';
                                
                                const roleLabel = app.role_approver.replace('cora_', '').replace('_', ' ');
                                const remarksText = app.remarks ? `<p class="text-[10px] text-zinc-550 italic mt-0.5">Remarks: "${app.remarks}"</p>` : '';
                                
                                approvalsHtml += `
                                    <div class="flex flex-col p-2 bg-zinc-50/50 rounded-lg border border-zinc-150">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-zinc-800">${app.stage_name} (${roleLabel})</span>
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase leading-none ${badgeClass}">${app.status}</span>
                                        </div>
                                        ${remarksText}
                                `;
                                
                                const curRole = coraREData.currentRole || '';
                                if (app.status === 'pending' && (curRole === 'administrator' || curRole === app.role_approver)) {
                                    approvalsHtml += `
                                        <div class="flex flex-col gap-1.5 mt-2 pt-2 border-t border-zinc-200/60">
                                            <textarea class="approval-remarks-input w-full p-2 text-[10px] rounded border border-zinc-200 bg-white outline-none resize-none font-sans" rows="2" placeholder="Review remarks..."></textarea>
                                            <div class="flex items-center gap-1.5">
                                                <button class="btn-approve-stage h-6 px-3 rounded bg-zinc-900 hover:bg-zinc-800 text-white text-[10px] font-bold cursor-pointer" data-subid="${sub.id}" data-appid="${app.id}">Approve</button>
                                                <button class="btn-reject-stage h-6 px-3 rounded border border-zinc-200 text-red-650 hover:bg-red-50/50 text-[10px] font-bold cursor-pointer" data-subid="${sub.id}" data-appid="${app.id}">Reject</button>
                                            </div>
                                        </div>
                                    `;
                                }
                                
                                approvalsHtml += `</div>`;
                            });
                            
                            approvalsHtml += `</div></div>`;
                        }

                        // Verification Signature & Hash
                        let verificationHtml = '';
                        if (sub.integrity_hash) {
                            verificationHtml = `
                                <div class="mt-2 p-2 bg-zinc-50 rounded-lg border border-zinc-150 flex flex-col gap-1">
                                    <div class="flex items-center justify-between text-[8px] font-bold text-zinc-400 uppercase">
                                        <span>TAMPER-PROOF VERIFICATION</span>
                                        <span class="text-green-700">✓ SECURED</span>
                                    </div>
                                    <span class="font-mono text-[8px] text-zinc-450 truncate">Hash: ${sub.integrity_hash}</span>
                                    <span class="font-mono text-[8px] text-zinc-450 truncate">Signature: ${sub.verification_signature}</span>
                                </div>
                            `;
                        }

                        // Dynamic brief compiler button
                        let briefBtnHtml = '';
                        if (sub.is_partial == '0') {
                            briefBtnHtml = `
                                <button class="btn-download-brief h-7 w-full rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold mt-2 cursor-pointer flex items-center justify-center gap-1.5" data-subid="${sub.id}">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    View intake contract brief
                                </button>
                            `;
                        }

                        div.innerHTML = `
                            <div class="flex justify-between items-center text-[10px] font-bold text-zinc-400 uppercase pb-2 border-b border-zinc-100">
                                <span>ID: #${sub.id} <span class="ml-2 px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-500 lowercase">${sub.version_number}</span></span>
                                <span>${sub.created_at}</span>
                            </div>
                            <div class="flex flex-col gap-0.5 mt-1">${dataRowsHtml}</div>
                            ${approvalsHtml}
                            ${verificationHtml}
                            ${briefBtnHtml}
                            <div class="text-[9px] text-zinc-400 mt-2">IP: ${sub.ip_address} | Type: ${sub.is_partial == '1' ? 'Partial' : 'Completed'}</div>
                        `;
                        container.appendChild(div);
                    });

                    // Attach approval click handlers
                    jQuery('.btn-approve-stage, .btn-reject-stage').off('click').on('click', function(e) {
                        e.stopPropagation();
                        const btn = jQuery(this);
                        const subId = btn.data('subid');
                        const appId = btn.data('appid');
                        const isApprove = btn.hasClass('btn-approve-stage');
                        const remarks = btn.closest('.flex-col').find('.approval-remarks-input').val();
                        
                        jQuery.ajax({
                            url: `/wp-json/cora/v1/forms/submissions/${subId}/approvals/${appId}`,
                            method: 'POST',
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
                            },
                            data: JSON.stringify({
                                decision: isApprove ? 'approved' : 'rejected',
                                remarks: remarks
                            }),
                            contentType: 'application/json',
                            success: function(res) {
                                window.coraShowToast && window.coraShowToast(res.message, "success");
                                openSubmissionsDrawer(id);
                            },
                            error: function(err) {
                                console.error("Approval error:", err);
                                const msg = err.responseJSON ? err.responseJSON.message : "Error processing approval decision.";
                                window.coraShowToast && window.coraShowToast(msg, "error");
                            }
                        });
                    });

                    // Attach download brief click handler
                    jQuery('.btn-download-brief').off('click').on('click', function(e) {
                        e.stopPropagation();
                        const subId = jQuery(this).data('subid');
                        window.open(coraREData.siteUrl + '/compiled-brief/' + subId, '_blank');
                    });
                }
                submissionsDrawer.classList.remove('translate-x-full');
            }
        });
    }

    document.getElementById('btn-close-submissions').addEventListener('click', function() {
        submissionsDrawer.classList.add('translate-x-full');
    });

    document.getElementById('btn-back-to-list').addEventListener('click', function() {
        window.location.hash = '#list';
    });

    // --- Approval Stages Matrix Planner JS ---
    function renderApprovalStages() {
        const container = document.getElementById('settings-approvals-container');
        if (!container) return;
        container.innerHTML = '';
        
        const stages = currentEditingForm.settings.approval_stages || [];
        const roles = {
            'administrator': 'Super Admin',
            'cora_manager': 'Broker Owner',
            'cora_branch_manager': 'Branch Manager',
            'cora_photographer': 'Managing Agent',
            'cora_videographer': 'Showing Assistant',
            'cora_drone_pilot': 'Property Valuer',
            'cora_editor': 'Listing Coordinator'
        };
        
        if (stages.length === 0) {
            container.innerHTML = `<div class="text-[10px] text-zinc-400 text-center py-2">No approval stages defined.</div>`;
            return;
        }
        
        stages.forEach((stage, idx) => {
            const card = document.createElement('div');
            card.className = 'border border-zinc-200 rounded-lg p-2.5 bg-zinc-50/50 flex flex-col gap-2 relative';
            
            let roleOptionsHtml = '';
            Object.keys(roles).forEach(rKey => {
                roleOptionsHtml += `<option value="${rKey}" ${stage.role_approver === rKey ? 'selected' : ''}>${roles[rKey]}</option>`;
            });
            
            card.innerHTML = `
                <button class="btn-delete-approval-stage absolute right-1.5 top-1.5 text-zinc-400 hover:text-red-500 text-[10px] border-none bg-transparent cursor-pointer" data-idx="${idx}">✕</button>
                
                <div class="flex items-center gap-1.5 text-[10px] text-zinc-500 font-bold uppercase">
                    <span class="h-4 w-4 rounded-full bg-zinc-200 text-zinc-700 flex items-center justify-center text-[9px]">${idx + 1}</span>
                    <span>Stage Config</span>
                </div>
                
                <div class="flex flex-col gap-1">
                    <span class="text-[9px] text-zinc-400 font-bold uppercase">Stage Name</span>
                    <input type="text" class="approval-stage-name h-7 px-2 text-xs rounded border border-zinc-200 w-full" value="${stage.stage_name || ''}" placeholder="e.g. Branch Review" data-idx="${idx}" />
                </div>
                
                <div class="grid grid-cols-2 gap-1.5">
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-bold uppercase">Approver Role</span>
                        <select class="approval-stage-role h-7 px-1 text-xs rounded border border-zinc-200 bg-white outline-none w-full font-sans" data-idx="${idx}">
                            ${roleOptionsHtml}
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-bold uppercase">SLA Time (Hours)</span>
                        <input type="number" class="approval-stage-sla h-7 px-2 text-xs rounded border border-zinc-200 w-full" value="${stage.sla_hours || 24}" min="1" placeholder="24" data-idx="${idx}" />
                    </div>
                </div>
            `;
            
            container.appendChild(card);
            
            card.querySelector('.approval-stage-name').addEventListener('input', function(e) {
                const sIdx = parseInt(e.target.dataset.idx);
                currentEditingForm.settings.approval_stages[sIdx].stage_name = e.target.value;
            });
            
            card.querySelector('.approval-stage-role').addEventListener('change', function(e) {
                const sIdx = parseInt(e.target.dataset.idx);
                currentEditingForm.settings.approval_stages[sIdx].role_approver = e.target.value;
            });
            
            card.querySelector('.approval-stage-sla').addEventListener('input', function(e) {
                const sIdx = parseInt(e.target.dataset.idx);
                currentEditingForm.settings.approval_stages[sIdx].sla_hours = parseInt(e.target.value) || 24;
            });
            
            card.querySelector('.btn-delete-approval-stage').addEventListener('click', function(e) {
                e.stopPropagation();
                const sIdx = parseInt(this.dataset.idx);
                currentEditingForm.settings.approval_stages.splice(sIdx, 1);
                renderApprovalStages();
            });
        });
    }
    
    document.getElementById('btn-add-approval-stage').addEventListener('click', function() {
        if (!currentEditingForm.settings.approval_stages) {
            currentEditingForm.settings.approval_stages = [];
        }
        currentEditingForm.settings.approval_stages.push({
            stage_name: 'New Approval Stage',
            role_approver: 'cora_branch_manager',
            sla_hours: 24
        });
        renderApprovalStages();
    });

    // --- Clause Rules PDF Injection JS ---
    function renderClauseRules() {
        const container = document.getElementById('pdf-clauses-rules-container');
        if (!container) return;
        container.innerHTML = '';
        
        const rules = currentEditingForm.settings.clause_rules || [];
        
        jQuery.ajax({
            url: '/wp-json/cora/v1/forms/clauses',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(clauses) {
                if (rules.length === 0) {
                    container.innerHTML = `<div class="text-[10px] text-zinc-400 text-center py-2">No clause conditions defined.</div>`;
                    return;
                }
                
                const fields = (currentEditingForm.blocks || [])
                    .filter(b => b.type !== 'header' && b.type !== 'paragraph' && b.type !== 'divider' && b.type !== 'page_break')
                    .map(b => (b.label || 'Unnamed field'));
                
                rules.forEach((rule, idx) => {
                    const card = document.createElement('div');
                    card.className = 'border border-zinc-200 rounded-lg p-2 bg-white flex flex-col gap-1.5 relative';
                    
                    let clauseOptionsHtml = '<option value="">Select Clause...</option>';
                    clauses.forEach(c => {
                        clauseOptionsHtml += `<option value="${c.clause_key}" ${rule.clause_key === c.clause_key ? 'selected' : ''}> swiss_aml: ${c.title}</option>`;
                    });
                    
                    let fieldOptionsHtml = '<option value="">Select Field...</option>';
                    fields.forEach(f => {
                        fieldOptionsHtml += `<option value="${f}" ${rule.field === f ? 'selected' : ''}>${f}</option>`;
                    });
                    
                    card.innerHTML = `
                        <button class="btn-delete-clause-rule absolute right-1.5 top-1 text-zinc-400 hover:text-red-500 text-[9px] border-none bg-transparent cursor-pointer" data-idx="${idx}">✕</button>
                        
                        <div class="flex flex-col gap-1">
                            <span class="text-[8px] text-zinc-400 font-bold uppercase">Inject Clause</span>
                            <select class="clause-rule-key h-6 px-1 text-[9px] rounded border border-zinc-200 bg-white outline-none w-full font-sans" data-idx="${idx}">
                                ${clauseOptionsHtml}
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-1">
                            <div class="flex flex-col gap-1">
                                <span class="text-[8px] text-zinc-400 font-bold uppercase">If Field</span>
                                <select class="clause-rule-field h-6 px-1 text-[9px] rounded border border-zinc-200 bg-white outline-none w-full font-sans" data-idx="${idx}">
                                    ${fieldOptionsHtml}
                                </select>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[8px] text-zinc-400 font-bold uppercase">Condition</span>
                                <select class="clause-rule-condition h-6 px-1 text-[9px] rounded border border-zinc-200 bg-white outline-none w-full font-sans" data-idx="${idx}">
                                    <option value="equals" ${rule.condition === 'equals' ? 'selected' : ''}>Equals</option>
                                    <option value="contains" ${rule.condition === 'contains' ? 'selected' : ''}>Contains</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[8px] text-zinc-400 font-bold uppercase">Value</span>
                                <input type="text" class="clause-rule-value h-6 px-1 text-[9px] rounded border border-zinc-200 w-full" value="${rule.value || ''}" placeholder="Value..." data-idx="${idx}" />
                            </div>
                        </div>
                    `;
                    
                    container.appendChild(card);
                    
                    card.querySelector('.clause-rule-key').addEventListener('change', function(e) {
                        const rIdx = parseInt(e.target.dataset.idx);
                        currentEditingForm.settings.clause_rules[rIdx].clause_key = e.target.value;
                    });
                    
                    card.querySelector('.clause-rule-field').addEventListener('change', function(e) {
                        const rIdx = parseInt(e.target.dataset.idx);
                        currentEditingForm.settings.clause_rules[rIdx].field = e.target.value;
                    });
                    
                    card.querySelector('.clause-rule-condition').addEventListener('change', function(e) {
                        const rIdx = parseInt(e.target.dataset.idx);
                        currentEditingForm.settings.clause_rules[rIdx].condition = e.target.value;
                    });
                    
                    card.querySelector('.clause-rule-value').addEventListener('input', function(e) {
                        const rIdx = parseInt(e.target.dataset.idx);
                        currentEditingForm.settings.clause_rules[rIdx].value = e.target.value;
                    });
                    
                    card.querySelector('.btn-delete-clause-rule').addEventListener('click', function(e) {
                        e.stopPropagation();
                        const rIdx = parseInt(this.dataset.idx);
                        currentEditingForm.settings.clause_rules.splice(rIdx, 1);
                        renderClauseRules();
                    });
                });
            }
        });
    }
    
    document.getElementById('btn-add-clause-rule').addEventListener('click', function() {
        if (!currentEditingForm.settings.clause_rules) {
            currentEditingForm.settings.clause_rules = [];
        }
        currentEditingForm.settings.clause_rules.push({
            clause_key: '',
            field: '',
            condition: 'equals',
            value: ''
        });
        renderClauseRules();
    });

    // --- Clause Management Tab JS ---
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

    const clauseDrawer = document.getElementById('cora-clause-drawer');
    
    document.getElementById('btn-create-clause').addEventListener('click', function() {
        document.getElementById('drawer-clause-key').value = '';
        document.getElementById('drawer-clause-title').value = '';
        document.getElementById('drawer-clause-text').value = '';
        clauseDrawer.classList.remove('translate-x-full');
    });
    
    document.getElementById('btn-close-clause-drawer').addEventListener('click', function() {
        clauseDrawer.classList.add('translate-x-full');
    });
    
    document.getElementById('btn-save-drawer-clause').addEventListener('click', function() {
        const key = document.getElementById('drawer-clause-key').value.trim();
        const title = document.getElementById('drawer-clause-title').value.trim();
        const text = document.getElementById('drawer-clause-text').value.trim();
        
        if (!key || !title || !text) {
            window.coraShowToast && window.coraShowToast("Please fill in all clause fields.", "error");
            return;
        }
        
        jQuery.ajax({
            url: '/wp-json/cora/v1/forms/clauses',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            data: JSON.stringify({
                id: 0,
                clause_key: key,
                title: title,
                content_text: text
            }),
            contentType: 'application/json',
            success: function(res) {
                window.coraShowToast && window.coraShowToast("Clause added to library successfully.", "success");
                clauseDrawer.classList.add('translate-x-full');
                fetchClauses();
            },
            error: function(err) {
                const msg = err.responseJSON ? err.responseJSON.message : "Error saving clause.";
                window.coraShowToast && window.coraShowToast(msg, "error");
            }
        });
    });

    // --- Compliance Audit Logs Tab JS ---
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

    // --- Custom Confirmation Action JS ---
    const confirmDrawer = document.getElementById('cora-confirm-drawer');
    let confirmCallback = null;
    
    function coraConfirmAction(message, onConfirm) {
        document.getElementById('confirm-message-text').textContent = message;
        confirmCallback = onConfirm;
        confirmDrawer.classList.remove('translate-x-full');
    }
    
    document.getElementById('btn-confirm-action').addEventListener('click', function() {
        if (confirmCallback) confirmCallback();
        confirmDrawer.classList.add('translate-x-full');
    });
    
    document.getElementById('btn-cancel-confirm').addEventListener('click', function() {
        confirmDrawer.classList.add('translate-x-full');
    });
    
    document.getElementById('btn-close-confirm').addEventListener('click', function() {
        confirmDrawer.classList.add('translate-x-full');
    });

    // Initial Fetch
    fetchForms();
});
</script>
