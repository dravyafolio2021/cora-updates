<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<style>#cora-forms-module { position: relative; } @keyframes spin { to { transform: rotate(360deg); } }</style>

<div id="cora-forms-module" class="w-full flex-1 min-h-0 flex flex-col overflow-hidden" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <!-- STATE 1: FORMS LIST VIEW -->
    <div id="forms-list-state" class="flex-1 flex flex-col overflow-y-auto p-6 md:p-8 gap-6">
<?php
$forms_header_args = array(
    'title'            => 'Cora Forms',
    'description'      => 'Design and share Notion-style interactive forms. Automatically collect leads into your CRM database.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="9"></line><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="15" y2="17"></line></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'Create form',
        'mobile_text' => '✨ AI Create',
        'onclick'     => "if(window.innerWidth < 640){ window.coraPromptFormAI('', 'Create a new Notion-style lead capture form'); } else { if(typeof createNewForm==='function'){ createNewForm(); } }",
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'visible'     => true,
        'class'       => '',
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $forms_header_args );
}
?>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.cora-workspace-header button:not(.group)').forEach(function(btn) {
                btn.id = 'btn-create-form';
            });
        });
        </script>

        <!-- Sub-page Tab Bar -->
        <div class="flex items-center gap-1 border-b border-zinc-200/60 pb-px mb-4 overflow-x-auto whitespace-nowrap scrollbar-none py-1 shrink-0 min-h-[44px]">
            <button id="tab-forms-list" class="cora-forms-tab flex items-center gap-2 px-4 py-2.5 text-xs font-semibold border-b-2 border-zinc-950 text-zinc-950 -mb-px transition-all bg-transparent cursor-pointer shrink-0" style="display: inline-flex !important; align-items: center !important; height: 36px !important; min-height: 36px !important; max-height: 36px !important; padding-top: 0 !important; padding-bottom: 0 !important; padding-left: 1rem !important; padding-right: 1rem !important; line-height: 1 !important; border-top: none !important; border-left: none !important; border-right: none !important; background: transparent !important; box-sizing: border-box !important; margin-bottom: -1px !important;">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <span>Forms List</span>
            </button>
            <button id="tab-funnel-analytics" class="cora-forms-tab flex items-center gap-2 px-4 py-2.5 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 -mb-px transition-all bg-transparent cursor-pointer shrink-0" style="display: inline-flex !important; align-items: center !important; height: 36px !important; min-height: 36px !important; max-height: 36px !important; padding-top: 0 !important; padding-bottom: 0 !important; padding-left: 1rem !important; padding-right: 1rem !important; line-height: 1 !important; border-top: none !important; border-left: none !important; border-right: none !important; background: transparent !important; box-sizing: border-box !important; margin-bottom: -1px !important;">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                <span>Funnel Analytics</span>
            </button>
            <button id="tab-clauses-library" class="cora-forms-tab flex items-center gap-2 px-4 py-2.5 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 -mb-px transition-all bg-transparent cursor-pointer shrink-0" style="display: inline-flex !important; align-items: center !important; height: 36px !important; min-height: 36px !important; max-height: 36px !important; padding-top: 0 !important; padding-bottom: 0 !important; padding-left: 1rem !important; padding-right: 1rem !important; line-height: 1 !important; border-top: none !important; border-left: none !important; border-right: none !important; background: transparent !important; box-sizing: border-box !important; margin-bottom: -1px !important;">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Clause Library</span>
            </button>
            <button id="tab-audit-logs" class="cora-forms-tab flex items-center gap-2 px-4 py-2.5 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 -mb-px transition-all bg-transparent cursor-pointer shrink-0" style="display: inline-flex !important; align-items: center !important; height: 36px !important; min-height: 36px !important; max-height: 36px !important; padding-top: 0 !important; padding-bottom: 0 !important; padding-left: 1rem !important; padding-right: 1rem !important; line-height: 1 !important; border-top: none !important; border-left: none !important; border-right: none !important; background: transparent !important; box-sizing: border-box !important; margin-bottom: -1px !important;">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Compliance Audit Log</span>
            </button>
        </div>

        <!-- TAB CONTENT: FORMS LIST -->
        <div id="forms-list-tab-content" class="flex flex-col gap-6">
            <!-- Metrics Dashboard Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[9.5px] sm:text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Forms</span>
                    <span id="metric-total-forms" class="text-xl sm:text-2xl font-bold text-zinc-900 ">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[9.5px] sm:text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Views</span>
                    <span id="metric-total-views" class="text-xl sm:text-2xl font-bold text-zinc-900 ">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[9.5px] sm:text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Submissions</span>
                    <span id="metric-total-submissions" class="text-xl sm:text-2xl font-bold text-zinc-900 ">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[9.5px] sm:text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Completion Rate</span>
                    <span id="metric-completion-rate" class="text-xl sm:text-2xl font-bold text-zinc-900 ">0%</span>
                </div>
            </div>

        <!-- Cards Grid Container -->
        <div id="forms-list-body" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Loading placeholder / Dynamic cards injection -->
            <div class="col-span-full py-12 text-center text-xs text-zinc-400 ">
                Loading forms list...
            </div>
        </div>
    </div>    <!-- TAB CONTENT: ADVANCED FUNNEL ANALYTICS -->
        <div id="forms-funnel-tab-content" class="hidden flex-col gap-6">
            <!-- Header Controls -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200/60 pb-5">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950 ">Conversion Funnel & Drop-off Diagnostics</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5">Analyze user friction, abandonment rates, and field-level drop-offs.</p>
                </div>
                <!-- Form Selector Dropdown -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-zinc-500 ">Select Form:</span>
                    <select id="funnel-form-selector" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-750 outline-none focus:border-zinc-300 w-56 cursor-pointer">
                        <option value="all">All Forms (Aggregate)</option>
                    </select>
                </div>
            </div>

            <!-- Funnel Metrics Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[8.5px] sm:text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Total Form Views</span>
                    <span id="funnel-metric-views" class="text-xl sm:text-2xl font-bold text-zinc-900 ">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[8.5px] sm:text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Started Filling</span>
                    <span id="funnel-metric-started" class="text-xl sm:text-2xl font-bold text-zinc-900 ">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[8.5px] sm:text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Submissions</span>
                    <span id="funnel-metric-completed" class="text-xl sm:text-2xl font-bold text-zinc-900 ">0</span>
                </div>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 shadow-sm">
                    <span class="text-[8.5px] sm:text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Abandonment Rate</span>
                    <span id="funnel-metric-abandonment" class="text-xl sm:text-2xl font-bold text-zinc-900 ">0%</span>
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
                                    <div id="funnel-started-progress" class="h-full bg-zinc-600 flex items-center pl-3 text-[10px] font-bold text-white transition-all duration-500" style="width: 0%">0%</div>
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
                    <h3 class="text-sm font-bold text-zinc-950 ">Clause Library & Automation Templates</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5">Manage legal clauses and conditional text blocks to compile contract PDFs.</p>
                </div>
                <button id="btn-create-clause" class="h-8 px-3.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-[11px] font-semibold transition-all flex items-center gap-1.5 cursor-pointer border-0 whitespace-nowrap shrink-0">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Add Clause</span>
                </button>
            </div>
            
            <div id="clauses-list-body" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="col-span-full py-16 text-center">
                    <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.2" fill="none" class="mx-auto text-zinc-300 mb-3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    <p class="text-xs text-zinc-400 ">No clauses created yet. Click "+ Add Clause" to start.</p>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: COMPLIANCE AUDIT LOG -->
        <div id="forms-audit-tab-content" class="hidden flex-col gap-6">
            <div class="flex items-center justify-between border-b border-zinc-200/60 pb-5">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950 ">GDPR Compliance & Field Audit Trail</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5">Immutable record of data reads, exports, and verification checksum checks.</p>
                </div>
            </div>
            
            <div class="bg-white border border-zinc-200/80 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-zinc-200 text-zinc-400 font-semibold bg-zinc-50/50 ">
                                <th class="px-4 py-3">Activity</th>
                                <th class="px-4 py-3">User</th>
                                <th class="px-4 py-3">Target</th>
                                <th class="px-4 py-3">IP Address</th>
                                <th class="px-4 py-3">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody id="audit-logs-body" class="divide-y divide-zinc-100 ">
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-zinc-400 ">Loading audit log...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination controls -->
            <div id="audit-logs-pagination" class="flex items-center justify-between pt-4">
                <span id="audit-pagination-info" class="text-xs text-zinc-500 ">Showing page 1 of 1 (Total 0 logs)</span>
                <div class="flex items-center gap-2">
                    <button id="btn-audit-prev" class="h-8 px-3 rounded-lg border border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 disabled:opacity-50 disabled:pointer-events-none text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer" disabled>
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        Prev
                    </button>
                    <button id="btn-audit-next" class="h-8 px-3 rounded-lg border border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 disabled:opacity-50 disabled:pointer-events-none text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer" disabled>
                        Next
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- STATE 2: FULL-PAGE INTERACTIVE FORM BUILDER VIEW -->
    <div id="form-editor-state" class="hidden flex-col flex-1 h-full min-h-0 border-0 rounded-none bg-white overflow-hidden font-sans">
        <!-- TOP TOOLBAR HEADER -->
        <div class="px-5 py-3 border-b border-zinc-200/80 flex items-center justify-between gap-4 shrink-0 bg-white ">
            <!-- Left: Back & Title -->
            <div class="flex items-center gap-3 min-w-0">
                <button id="btn-back-to-list" class="h-8 w-8 rounded-lg border border-zinc-200 hover:bg-zinc-100 flex items-center justify-center text-zinc-600 transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                </button>
                <div class="flex items-center gap-2.5 min-w-0">
                    <input id="editor-form-title" type="text" placeholder="Untitled Form" value="Untitled Form" class="text-sm font-bold text-zinc-950 bg-transparent border-b border-transparent hover:border-zinc-200 focus:border-zinc-400 outline-none p-0.5 truncate w-48 md:w-72" />
                    <span class="text-zinc-400 text-xs">✎</span>
                    <span id="editor-save-status" class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold flex items-center gap-1 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Saved
                    </span>
                </div>
            </div>

            <!-- Center: Viewport Switcher & History Controls -->
            <div class="hidden md:flex items-center gap-3">
                <div class="flex items-center p-0.5 rounded-lg border border-zinc-200 bg-zinc-50 text-zinc-600 ">
                    <button class="h-7 px-2.5 rounded-md text-xs font-semibold bg-white text-zinc-950 shadow-2xs flex items-center gap-1 cursor-pointer">
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
                    <button class="h-7 w-7 rounded-lg hover:bg-zinc-100 flex items-center justify-center cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                    </button>
                    <button class="h-7 w-7 rounded-lg hover:bg-zinc-100 flex items-center justify-center cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.13-9.36L23 10"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Right: Publish & Share Controls -->
            <div class="flex items-center gap-2 shrink-0">
                <button id="btn-view-form" class="h-8 px-3 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    View
                </button>
                <button id="btn-share-editor" class="h-8 px-3 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><polyline points="16 6 12 2 8 6"></polyline><line x1="12" y1="2" x2="12" y2="15"></line></svg>
                    Share
                </button>
                <button id="btn-save-draft" class="h-8 px-3 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Save Draft
                </button>
                <button id="btn-save-form" class="h-8 px-4 rounded-lg bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all cursor-pointer shadow-xs border-0">
                    Publish Form
                </button>
            </div>
        </div>

        <!-- 2-COLUMN WORKSPACE BODY -->
        <div class="flex-1 flex overflow-hidden min-h-0">

            <!-- COLUMN 1: UNIFIED DYNAMIC LEFT SIDEBAR -->
            <div id="editor-left-panel" class="w-[320px] shrink-0 border-r border-zinc-200/80 bg-zinc-50/60 flex flex-col font-sans transition-all duration-300 ease-in-out" style="width:320px;">
                <!-- Top Header Tabs -->
                <div class="px-3 py-2 border-b border-zinc-200/80 flex items-center bg-white shrink-0">
                    <div id="left-panel-tabs" class="flex-1 flex items-center p-0.5 bg-zinc-100 rounded-lg gap-0.5">
                        <button id="btn-left-tab-fields" class="flex-1 py-1.5 px-1 rounded-md text-[10px] font-bold bg-white text-zinc-950 shadow-2xs whitespace-nowrap cursor-pointer transition-all border-0 outline-none">Add Fields</button>
                        <button id="btn-left-tab-settings" class="flex-1 py-1.5 px-1 rounded-md text-[10px] font-medium text-zinc-500 hover:text-zinc-900 whitespace-nowrap cursor-pointer transition-all bg-transparent border-0 outline-none">Fields</button>
                        <button id="btn-left-tab-form" class="flex-1 py-1.5 px-1 rounded-md text-[10px] font-medium text-zinc-500 hover:text-zinc-900 whitespace-nowrap cursor-pointer transition-all bg-transparent border-0 outline-none">Form</button>
                        <button id="btn-left-tab-integ" class="flex-1 py-1.5 px-1 rounded-md text-[10px] font-medium text-zinc-500 hover:text-zinc-900 whitespace-nowrap cursor-pointer transition-all bg-transparent border-0 outline-none">Integrations</button>
                    </div>
                </div>

                <!-- 1. #left-tab-content-fields: Add Fields Palette -->
                <div id="left-tab-fields" class="flex-1 flex flex-col overflow-hidden">
                    <!-- Palette Search -->
                    <div id="left-panel-search" class="p-3 pb-2">
                        <div class="relative">
                            <input id="palette-search-input" type="text" placeholder="Search fields..." class="h-8 pl-8 pr-8 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400 pointer-events-none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                    </div>

                    <!-- Component Palette Sections -->
                    <div id="left-panel-content" class="flex-1 overflow-y-auto px-3 py-2 space-y-5">
                        <!-- BASIC FIELDS -->
                        <div>
                            <div class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Basic Fields</div>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="text">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg>
                                    Short Text
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="long_text">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="12" x2="3" y2="12"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                                    Long Text
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="email">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    Email
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="phone">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.59 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.78a16 16 0 0 0 6.29 6.29l1.13-.93a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    Phone
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="number">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                                    Number
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="dropdown">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    Dropdown
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="multiple_choice">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg>
                                    Multiple Choice
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="checkbox">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                    Checkboxes
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="date">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    Date
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="file">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    File Upload
                                </button>
                            </div>
                        </div>

                        <!-- ADVANCED FIELDS -->
                        <div>
                            <div class="mb-2">
                                <span class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider">Advanced Fields</span>
                            </div>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="signature">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    Signature
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="rating">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                    Rating
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="slider">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                                    Slider
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="upi_id">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line><line x1="6" y1="15" x2="10" y2="15"></line></svg>
                                    UPI ID
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="upi_qr">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><path d="M14 14h1v1h-1zm3 0h1v1h-1zm0 3h1v1h-1zm-3 3h1v1h-1zm3 0h1v1h-1z"></path></svg>
                                    UPI QR
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="rich_text">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    Rich Text
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="matrix">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                                    Matrix Field
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="repeatable">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                    Repeatable List
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="hidden">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                    Hidden Field
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="booking">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    Booking Slots
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="address">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    Address Field
                                </button>
                                <button draggable="true" class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-grab active:cursor-grabbing text-[11px] font-medium text-zinc-800 transition-all" data-add-type="services_checklist">
                                    <span class="shrink-0 text-zinc-500 font-bold text-xs select-none">₹</span>
                                    Pricing List
                                </button>
                            </div>
                        </div>

                        <!-- LAYOUT ELEMENTS -->
                        <div>
                            <div class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Layout Elements</div>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-pointer text-[11px] font-medium text-zinc-800 transition-all" data-add-type="header">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><path d="M6 12h12"></path><path d="M6 4h18"></path><path d="M6 20h18"></path></svg>
                                    Heading
                                </button>
                                <button class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-pointer text-[11px] font-medium text-zinc-800 transition-all" data-add-type="columns">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><rect x="3" y="3" width="8" height="18" rx="1"></rect><rect x="13" y="3" width="8" height="18" rx="1"></rect></svg>
                                    Columns
                                </button>
                                <button class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-pointer text-[11px] font-medium text-zinc-800 transition-all" data-add-type="divider">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    Divider
                                </button>
                                <button class="p-2 rounded-lg border border-zinc-200/80 bg-white hover:border-zinc-400 hover:bg-zinc-50 text-left flex items-center gap-2 cursor-pointer text-[11px] font-medium text-zinc-800 transition-all" data-add-type="spacer">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500"><polyline points="5 9 2 12 5 15"></polyline><polyline points="9 5 12 2 15 5"></polyline><polyline points="15 19 12 22 9 19"></polyline><polyline points="19 9 22 12 19 15"></polyline><line x1="2" y1="12" x2="22" y2="12"></line><line x1="12" y1="2" x2="12" y2="22"></line></svg>
                                    Spacer
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="left-panel-footer" class="p-3 border-t border-zinc-200/80 shrink-0">
                        <button class="w-full h-8 rounded-lg border border-dashed border-zinc-300 text-zinc-600 hover:text-zinc-950 hover:border-zinc-500 text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer transition-all">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Add Custom Field
                        </button>
                    </div>
                </div>

                <!-- 2. #left-tab-content-settings: Dynamic Inspector for Selected Field -->
                <div id="left-tab-settings" class="hidden flex-1 overflow-y-auto p-4 space-y-4">
                    <!-- Selected Field Banner -->
                    <div class="p-3 rounded-xl bg-zinc-50 border border-zinc-200/80 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div id="inspector-field-icon" class="text-zinc-500 ">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="9"></line><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="13" y2="17"></line></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-zinc-950 " id="inspector-field-type-title">Field Settings</h4>
                                <span class="text-[9.5px] text-zinc-400 font-mono" id="inspector-field-id">Select a field on canvas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Label Input -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Field Label</label>
                        <input id="inspector-field-label" type="text" placeholder="Field Label" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-900 outline-none focus:border-zinc-400 w-full" />
                    </div>

                    <!-- Description Input -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Description</label>
                        <textarea id="inspector-field-desc" rows="2" placeholder="Add a description..." class="p-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none focus:border-zinc-400 w-full resize-none"></textarea>
                    </div>

                    <!-- Required Toggle -->
                    <div class="flex items-center justify-between py-2 border-t border-b border-zinc-100 ">
                        <span class="text-xs font-medium text-zinc-700 ">Required Field</span>
                        <input type="checkbox" id="inspector-field-required" class="w-4 h-4 rounded accent-zinc-950 cursor-pointer" />
                    </div>

                    <!-- Choices Editor with Scores -->
                    <div class="space-y-2 pt-1" id="inspector-choices-wrapper">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Choices & Option Scores</label>
                            <button id="btn-add-choice-item" class="text-[10px] font-bold text-zinc-900 hover:underline cursor-pointer border-none bg-transparent">+ Add Choice</button>
                        </div>
                        <div id="inspector-field-choices-container" class="space-y-2">
                            <!-- Dynamic choices inputs with scores injected here -->
                        </div>
                    </div>

                    <!-- AI Purpose Input -->
                    <div class="space-y-1 pt-2 border-t border-zinc-100 ">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AI Context & Purpose</label>
                        <textarea id="inspector-field-ai-purpose" rows="2" placeholder="Describe field purpose for AI auto-fill and validation..." class="p-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none focus:border-zinc-400 w-full resize-none"></textarea>
                    </div>

                    <!-- Price Configuration (for payment fields) -->
                    <div id="inspector-price-container" class="space-y-2 pt-2 border-t border-zinc-100 ">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Price Configuration</label>
                        <div class="flex items-center gap-2">
                            <input id="inspector-price-amount" type="number" value="100" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-none w-full" />
                            <select id="inspector-price-currency" class="h-9 px-2 rounded-lg border border-zinc-200 bg-white text-xs font-bold text-zinc-800 ">
                                <option value="INR">INR (₹)</option>
                                <option value="USD">USD ($)</option>
                            </select>
                        </div>
                        <div id="inspector-upi-container" class="space-y-1 mt-2">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">UPI ID / VPA</label>
                            <input id="inspector-upi-id-value" type="text" placeholder="yourname@paytm" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-900 outline-none focus:border-zinc-400 w-full" />
                        </div>
                    </div>

                    <!-- Conditional Logic Section -->
                    <div class="pt-3 border-t border-zinc-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Conditional Logic Rules</span>
                            <button id="btn-add-logic-rule" class="text-[10px] font-bold text-zinc-900 hover:underline cursor-pointer border-none bg-transparent">+ Add Rule</button>
                        </div>
                        <div id="settings-logic-rules-container" class="space-y-2">
                            <!-- Rule cards injected here -->
                        </div>
                    </div>
                </div>

                <!-- 3. #left-tab-content-form: Form Level Settings -->
                <div id="left-tab-form" class="hidden flex-1 overflow-y-auto p-4 space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Form Title</label>
                        <input id="settings-form-title" type="text" placeholder="Form Title" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Subtitle / Instructions</label>
                        <input id="settings-form-subtitle" type="text" placeholder="Form Subtitle" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Cover Image URL</label>
                        <input id="settings-cover-url" type="text" placeholder="https://example.com/cover.jpg" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                    </div>
                    <!-- Thank You / Completion Screen Customization -->
                    <div class="space-y-3 pt-3 border-t border-zinc-100 ">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Thank You Screen & CTA Destination</label>
                        
                        <div class="space-y-1">
                            <span class="text-[9.5px] font-bold text-zinc-500 uppercase">Thank You Heading</span>
                            <input id="settings-thankyou-title" type="text" placeholder="Response Submitted" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                        </div>

                        <div class="space-y-1">
                            <span class="text-[9.5px] font-bold text-zinc-500 uppercase">Confirmation Message</span>
                            <textarea id="settings-success-msg" rows="2" placeholder="Thank you for your response! We will be in touch shortly." class="p-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full resize-none"></textarea>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[9.5px] font-bold text-zinc-500 uppercase">Redirect URL (Optional)</span>
                            <input id="settings-redirect-url" type="text" placeholder="https://example.com/thank-you" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                        </div>

                        <div class="flex items-center justify-between py-1.5 border-t border-zinc-100 ">
                            <span class="text-xs font-medium text-zinc-700 ">Show Thank You CTA Button</span>
                            <input type="checkbox" id="settings-thankyou-cta-enable" class="w-4 h-4 rounded accent-zinc-950 cursor-pointer" />
                        </div>

                        <div id="settings-thankyou-cta-details" class="space-y-2 hidden">
                            <div class="space-y-1">
                                <span class="text-[9.5px] font-bold text-zinc-500 uppercase">CTA Button Text</span>
                                <input id="settings-thankyou-cta-text" type="text" placeholder="Visit Website / Book Call" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                            </div>
                            <div class="space-y-1">
                                <span class="text-[9.5px] font-bold text-zinc-500 uppercase">Destination Link URL</span>
                                <input id="settings-thankyou-cta-url" type="text" placeholder="https://yourdomain.com" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons (CTA) Customization -->
                    <div class="space-y-3 pt-3 border-t border-zinc-100 ">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Footer Actions (CTAs)</label>
                        
                        <div class="space-y-1">
                            <span class="text-[9.5px] font-bold text-zinc-500 uppercase">Primary CTA Text</span>
                            <input id="settings-submit-text" type="text" placeholder="Submit" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                        </div>

                        <div class="space-y-1">
                            <span class="text-[9.5px] font-bold text-zinc-500 uppercase">Submit Action</span>
                            <select id="settings-submit-action" class="h-9 px-2 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-700 outline-none w-full">
                                <option value="message">Show Success Message</option>
                                <option value="redirect">Redirect to URL</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-xs font-medium text-zinc-700 ">Show Secondary Button</span>
                            <input type="checkbox" id="settings-sec-show" class="w-4 h-4 rounded accent-zinc-950 cursor-pointer" />
                        </div>

                        <div id="settings-sec-text-wrapper" class="space-y-1">
                            <span class="text-[9.5px] font-bold text-zinc-500 uppercase">Secondary CTA Text</span>
                            <input id="settings-sec-text" type="text" placeholder="Save as draft" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                        </div>
                    </div>

                    <!-- Automated Email Notifications -->
                    <div class="space-y-3 pt-3 border-t border-zinc-100 ">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Email Notifications</label>
                        
                        <!-- Admin Notification -->
                        <div class="space-y-2 border border-zinc-150 rounded-xl p-3 bg-zinc-50/20">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-zinc-700 ">Admin Notification</span>
                                <input type="checkbox" id="settings-email-admin-enable" class="w-4 h-4 rounded accent-zinc-950 cursor-pointer" />
                            </div>
                            <div id="settings-email-admin-details" class="space-y-2 mt-1 hidden">
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-zinc-500 uppercase">Send to Email(s)</span>
                                    <input id="settings-email-admin-to" type="text" placeholder="admin@example.com" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-zinc-500 uppercase">Subject Line</span>
                                    <input id="settings-email-admin-subject" type="text" placeholder="New Submission: [Form Title]" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                                </div>
                            </div>
                        </div>

                        <!-- Submitter Confirmation -->
                        <div class="space-y-2 border border-zinc-150 rounded-xl p-3 bg-zinc-50/20">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-zinc-700 ">Submitter Receipt</span>
                                <input type="checkbox" id="settings-email-submitter-enable" class="w-4 h-4 rounded accent-zinc-950 cursor-pointer" />
                            </div>
                            <div id="settings-email-submitter-details" class="space-y-2 mt-1 hidden">
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-zinc-500 uppercase">Subject Line</span>
                                    <input id="settings-email-submitter-subject" type="text" placeholder="Submission Received: [Form Title]" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-zinc-500 uppercase">Body Header Message</span>
                                    <textarea id="settings-email-submitter-message" rows="2" placeholder="Thank you for your submission. A summary of your answers is below." class="p-2 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full resize-none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2 pt-2 border-t border-zinc-100 ">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-zinc-900 uppercase">Approval Stages Planner</label>
                            <button id="btn-add-approval-stage" class="text-[10px] font-bold text-zinc-900 hover:underline cursor-pointer border-none bg-transparent">+ Add Stage</button>
                        </div>
                        <div id="settings-approvals-container" class="space-y-2 max-h-48 overflow-y-auto"></div>
                    </div>
                    <div class="space-y-1 pt-2 border-t border-zinc-100 ">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Custom CSS</label>
                        <textarea id="settings-custom-css" rows="3" placeholder="/* Custom CSS styling overrides */" class="p-3 font-mono rounded-lg border border-zinc-200 bg-white text-[10px] text-zinc-900 outline-none w-full resize-none"></textarea>
                    </div>
                    <div class="space-y-2 pt-2 border-t border-zinc-100 ">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-zinc-900 uppercase">Document PDF Auto-Compiler</label>
                            <button id="btn-add-clause-rule" class="text-[9px] font-bold text-zinc-500 hover:underline cursor-pointer border-none bg-transparent">+ Add Clause</button>
                        </div>
                        <textarea id="settings-pdf-template" rows="3" placeholder="Agreement template..." class="p-2 rounded border border-zinc-200 bg-white text-xs text-zinc-900 w-full resize-none"></textarea>
                        <div id="pdf-clauses-rules-container" class="space-y-1"></div>
                    </div>
                </div>

                <!-- 4. #left-tab-content-integ: Integrations Settings -->
                <div id="left-tab-integ" class="hidden flex-1 overflow-y-auto p-4 space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Webhook Endpoint URL</label>
                        <input id="settings-webhook-url" type="text" placeholder="https://yourdomain.com/webhook" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none w-full" />
                    </div>
                    <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50/50 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600 "><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                <span class="text-xs font-bold text-zinc-900 ">UPI Instant Payments (India)</span>
                            </div>
                            <span class="px-1.5 py-0.5 rounded text-[8.5px] font-bold uppercase bg-emerald-50 text-emerald-700 ">Active</span>
                        </div>
                        <p class="text-[10.5px] text-zinc-500 leading-relaxed">Accept direct UPI payments (GPay, PhonePe, Paytm, BHIM) via UPI ID & QR Code blocks.</p>
                        <div class="space-y-1 pt-1">
                            <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">Workspace Default UPI ID / VPA</label>
                            <input id="settings-upi-id" type="text" placeholder="cora@upi or agency@paytm" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 w-full outline-none" />
                        </div>
                    </div>
                    <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50/50 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-900 ">Cora CRM Lead Capture</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="settings-crm-lead-capture-enable" class="sr-only peer" checked>
                                <div class="w-8 h-4 bg-zinc-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-zinc-950 "></div>
                            </label>
                        </div>
                        <p class="text-[10.5px] text-zinc-500 leading-relaxed">Choose whether submissions from this specific form auto-register as CRM Leads.</p>
                        
                        <div class="space-y-1 pt-1">
                            <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">Form Purpose & Classification</label>
                            <select id="settings-form-purpose" class="h-8 px-2 text-xs bg-white border border-zinc-200 rounded-lg text-zinc-900 w-full outline-none" onchange="if(this.value==='custom_campaign'){jQuery('#settings-custom-campaign-box').removeClass('hidden');}else{jQuery('#settings-custom-campaign-box').addClass('hidden');}">
                                <option value="lead_capture">Lead Capture / Inquiry Form (Creates CRM Lead)</option>
                                <option value="campaign_form">Campaign / Landing Page Form (Creates CRM Lead)</option>
                                <option value="contact_form">General Contact Form (Creates CRM Lead)</option>
                                <option value="custom_campaign">+ Custom Campaign / Custom Purpose Tag...</option>
                                <option value="internal_survey">Internal Survey / Feedback (Non-Lead / Skip CRM)</option>
                            </select>
                            <div id="settings-custom-campaign-box" class="pt-1.5 hidden">
                                <label class="text-[8.5px] font-bold text-zinc-400 uppercase tracking-wider block">Custom Campaign Name / Source Tag</label>
                                <input id="settings-custom-campaign-tag" type="text" placeholder="e.g. Summer Promo 2026, Instagram Reel" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 w-full outline-none mt-0.5" />
                            </div>
                        </div>
                    </div>
                    <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50/50 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-900 ">Google Sheets Sync</span>
                            <span class="px-1.5 py-0.5 rounded text-[8.5px] font-bold uppercase bg-zinc-900 text-white font-mono">Soon</span>
                        </div>
                        <p class="text-[10.5px] text-zinc-500">Auto-export form entries to live spreadsheets.</p>
                    </div>
                </div>
            </div>
            <!-- /LEFT PANEL -->

            <!-- COLUMN 2: MAIN CONTENT AREA (FLEX-1) -->
            <div class="flex-1 flex flex-col overflow-hidden min-h-0">

                <!-- BUILD VIEW -->
                <div id="editor-build-view" class="flex-1 flex flex-col overflow-hidden min-h-0">
                    <!-- Steps Bar -->
                    <div id="editor-steps-bar" class="flex items-center gap-2 px-6 pt-4 pb-2 overflow-x-auto shrink-0"></div>

                    <!-- Canvas Scroll Area -->
                    <div id="editor-center-canvas" class="flex-1 bg-zinc-50 overflow-y-auto p-6 flex flex-col items-center min-h-0">
                        <!-- CANVAS SHEET -->
                        <div id="editor-document-sheet" class="w-full max-w-3xl bg-white border border-zinc-200 rounded-2xl shadow-sm flex flex-col overflow-hidden relative h-full">


                            <!-- Form Header Info -->
                            <div class="px-8 pb-6 border-b border-zinc-100 shrink-0" style="padding-top: 60px !important;">
                                <h2 class="text-2xl font-bold text-zinc-950 outline-none border-none bg-transparent leading-tight" contenteditable="true" id="canvas-form-name">Cora Survey Form</h2>
                                <p class="text-sm text-zinc-500 outline-none mt-1" contenteditable="true" id="canvas-form-subtitle">Fill out details below to submit request.</p>
                            </div>

                            <!-- Block List Container -->
                            <div class="flex flex-col flex-1 overflow-y-auto min-h-0" id="editor-blocks-container">
                                <!-- Dynamic blocks injected here -->
                            </div>

                            <!-- Bottom Add Field Row -->
                            <div id="editor-drop-zone" class="border-t border-zinc-100 px-8 py-4 flex items-center gap-3 shrink-0"
                                ondragover="event.preventDefault()" ondrop="event.preventDefault(); const d=event.dataTransfer.getData('text/plain'); if(d.startsWith('new:')){addFieldToForm(d.replace('new:',''));}">
                                <button id="btn-add-element-bottom" class="flex items-center gap-2 h-8 px-4 rounded-lg border border-zinc-200 bg-white text-zinc-600 text-xs font-semibold hover:bg-zinc-50 hover:border-zinc-300 cursor-pointer transition-all">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    Add Field
                                </button>
                                <span class="text-[10px] text-zinc-400">or drag & drop from sidebar</span>
                            </div>

                            <!-- Bottom CTA Preview Row -->
                            <div id="canvas-cta-preview-row" class="border-t border-zinc-100 bg-zinc-50/20 px-8 py-5 flex items-center justify-between mt-auto shrink-0 cursor-pointer hover:bg-zinc-100/10 transition-all" title="Click to customize submit buttons">
                                <button id="canvas-sec-btn" type="button" class="h-9 px-4 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-600 hover:bg-zinc-50 cursor-pointer transition-all">
                                    Save as draft
                                </button>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="h-9 px-4 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-600 hover:bg-zinc-50 cursor-pointer transition-all">
                                        Schedule
                                    </button>
                                    <button id="canvas-submit-btn" type="button" class="h-9 px-5 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                                        Submit
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /BUILD VIEW -->

                <!-- SUBMISSIONS VIEW -->
                <div id="editor-submissions-state" class="hidden flex-1 flex overflow-hidden">
                    <!-- Left Summary Panel -->
                    <div class="w-72 shrink-0 border-r border-zinc-200 bg-white flex flex-col overflow-y-auto p-5 gap-5">
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 ">Submission</h3>
                            <p class="text-xs text-zinc-500 mt-1">View and manage all form submissions.</p>
                        </div>
                        <!-- Summary Stats -->
                        <div class="space-y-1">
                            <div class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Summary</div>
                            <div class="flex items-center justify-between py-2 border-b border-zinc-100 ">
                                <span class="text-xs text-zinc-600 ">Total Submissions</span>
                                <span id="sub-stat-total" class="text-xs font-bold text-zinc-900 ">—</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-zinc-100 ">
                                <span class="text-xs text-zinc-600 ">This Month</span>
                                <span id="sub-stat-month" class="text-xs font-bold text-zinc-900 ">—</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-zinc-100 ">
                                <span class="text-xs text-zinc-600 ">Today</span>
                                <span id="sub-stat-today" class="text-xs font-bold text-zinc-900 ">—</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-xs text-zinc-600 ">Unread</span>
                                <span id="sub-stat-unread" class="text-xs font-bold text-zinc-900 ">—</span>
                            </div>
                        </div>
                        <!-- Filters -->
                        <div class="space-y-2">
                            <div class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider">Filters</div>
                            <div class="relative">
                                <input id="submissions-search" type="text" placeholder="Search submissions..." class="h-8 w-full pl-8 pr-3 rounded-lg border border-zinc-200 bg-white text-xs outline-none focus:border-zinc-400" />
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </div>
                            <select id="submissions-step-filter" class="h-8 w-full px-3 rounded-lg border border-zinc-200 bg-white text-xs outline-none">
                                <option value="">All Steps</option>
                            </select>
                            <select id="submissions-status-filter" class="h-8 w-full px-3 rounded-lg border border-zinc-200 bg-white text-xs outline-none">
                                <option value="">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="in_progress">In Progress</option>
                                <option value="incomplete">Incomplete</option>
                            </select>
                            <button class="h-8 w-full rounded-lg border border-zinc-200 text-xs text-zinc-600 hover:bg-zinc-50 transition-all">Clear Filters</button>
                        </div>
                        <!-- Export -->
                        <div class="space-y-2">
                            <div class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider">Export</div>
                            <button id="submissions-export-btn" class="h-8 w-full flex items-center justify-center gap-2 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-all cursor-pointer">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Export Submissions
                            </button>
                        </div>
                    </div>
                    <!-- Right Table -->
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-200 flex items-center justify-between shrink-0">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 ">Submissions <span id="submissions-count-label" class="text-zinc-400 font-normal">(0)</span></h3>
                                <p class="text-xs text-zinc-500 mt-0.5">Here are all the responses collected from your form.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button id="submissions-refresh-btn" class="h-8 w-8 rounded-lg border border-zinc-200 flex items-center justify-center text-zinc-500 hover:bg-zinc-50 cursor-pointer">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex-1 overflow-auto">
                            <table class="w-full border-collapse text-left">
                                <thead class="sticky top-0 bg-zinc-50 border-b border-zinc-200 ">
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
                                <tbody id="submissions-table-body" class="divide-y divide-zinc-100 text-xs">
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
                    <div class="w-60 shrink-0 border-r border-zinc-200 bg-white flex flex-col overflow-hidden">
                        <div class="p-5 border-b border-zinc-100 shrink-0">
                            <h3 class="text-sm font-bold text-zinc-900 ">Templates</h3>
                            <p class="text-xs text-zinc-500 mt-1">Choose a template to get started quickly.</p>
                        </div>
                        <div class="p-3">
                            <div class="relative">
                                <input id="templates-search" type="text" placeholder="Search templates..." class="h-8 w-full pl-8 pr-3 rounded-lg border border-zinc-200 bg-white text-xs outline-none focus:border-zinc-400" />
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </div>
                        </div>
                        <div id="templates-category-list" class="flex-1 overflow-y-auto px-3 pb-3 space-y-0.5"></div>
                        <div class="p-4 border-t border-zinc-100 shrink-0">
                            <p class="text-xs font-semibold text-zinc-700 mb-1">Start from Scratch</p>
                            <p class="text-[10px] text-zinc-500 mb-2">Create a blank form and build it your way.</p>
                            <button onclick="createNewForm()" class="w-full h-8 rounded-lg border border-dashed border-zinc-300 text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:border-zinc-500 transition-all">+ Blank Form</button>
                        </div>
                    </div>
                    <!-- Right Templates Grid -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 ">All Templates</h3>
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
                    <div class="w-60 shrink-0 border-r border-zinc-200 bg-white flex flex-col overflow-hidden">
                        <div class="p-5 border-b border-zinc-100 shrink-0">
                            <h3 class="text-sm font-bold text-zinc-900 ">Integrations</h3>
                            <p class="text-xs text-zinc-500 mt-1">Connect your form with the tools you use.</p>
                        </div>
                        <div id="integrations-category-list" class="flex-1 overflow-y-auto p-3 space-y-0.5"></div>
                        <div class="p-4 border-t border-zinc-100 shrink-0">
                            <div class="p-3 rounded-xl bg-zinc-50 border border-zinc-200 ">
                                <p class="text-xs font-semibold text-zinc-700 mb-1">Missing an integration?</p>
                                <p class="text-[10px] text-zinc-500 mb-2">Let us know which tool you'd like to connect with.</p>
                                <button class="w-full h-8 rounded-lg border border-zinc-300 text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:border-zinc-500 transition-all">Request Integration</button>
                            </div>
                        </div>
                    </div>
                    <!-- Right Integrations Grid -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 ">All Integrations</h3>
                                <p class="text-xs text-zinc-500 mt-0.5">Connect your form with the tools you use to automate workflows and sync data.</p>
                            </div>
                            <div class="relative">
                                <input id="integrations-search" type="text" placeholder="Search integrations..." class="h-8 w-44 pl-8 pr-3 rounded-lg border border-zinc-200 bg-white text-xs outline-none" />
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

    <!-- BACKDROP FOR SUBMISSIONS RIGHT DRAWER -->
    <div id="cora-submissions-backdrop" onclick="closeSubmissionsDrawer()" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-40 transition-all duration-200 cursor-pointer"></div>

    <!-- STATE 3: SUBMISSIONS LIST RIGHT DRAWER SHEET & DASHBOARD -->
    <div id="cora-submissions-drawer" class="hidden fixed top-0 right-0 bottom-0 w-full sm:w-[680px] md:w-[820px] lg:w-[940px] max-w-full bg-white shadow-2xl border-l border-zinc-200/80 z-45 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col overflow-hidden font-sans">
        <!-- Dashboard Header Bar -->
        <div class="px-6 py-4.5 border-b border-zinc-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0 bg-white ">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-11 h-11 rounded-2xl bg-zinc-950 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-[15px] font-bold text-zinc-950 tracking-tight" id="drawer-form-title">Form Submissions Dashboard</h3>
                        <span id="drawer-responses-count" class="px-3 py-0.5 rounded-full bg-zinc-100 text-zinc-700 text-xs font-semibold shrink-0">0 Entries</span>
                    </div>
                    <p class="text-[12px] text-zinc-500 mt-1 font-normal" id="drawer-form-meta">View, filter, and export user response entries for this form.</p>
                </div>
            </div>

            <!-- Action Controls -->
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <!-- Search Input -->
                <div class="relative">
                    <input id="submissions-search-input" type="text" placeholder="Search entries..." class="h-8 pl-8 pr-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none focus:border-zinc-400 w-40 sm:w-52" />
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>

                <!-- Export CSV Button -->
                <button id="btn-export-submissions-csv" class="h-8 px-3 rounded-lg bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all flex items-center gap-1.5 cursor-pointer shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export CSV
                </button>

                <!-- Connect Google Sheets Button (Locked) -->
                <button id="btn-connect-google-sheets" class="h-8 px-3 rounded-lg bg-white border border-zinc-200 text-zinc-700 text-xs font-semibold hover:bg-zinc-50 transition-all flex items-center gap-1.5 cursor-pointer shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600 "><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    <span>Google Sheets</span>
                    <span class="px-1.5 py-0.2 bg-zinc-900 text-white text-[8.5px] font-mono font-bold rounded uppercase tracking-wider">Soon</span>
                </button>

                <!-- Close Button -->
                <button id="btn-close-submissions" onclick="closeSubmissionsDrawer()" class="h-8 w-8 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-500 cursor-pointer transition-colors">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>

        <!-- Submissions Entries Content (Data Table / Grid) -->
        <div class="flex-1 overflow-y-auto p-6" id="submissions-drawer-content">
            <!-- Dynamic entries table goes here -->
        </div>

        <!-- RIGHT SLIDE-OVER ENTRY INSPECTOR DRAWER -->
        <div id="cora-entry-inspector" class="hidden absolute top-0 right-0 bottom-0 w-full md:w-[480px] bg-white border-l border-zinc-200 shadow-2xl z-50 flex flex-col font-sans transform translate-x-full transition-transform duration-300">
            <!-- Inspector Header -->
            <div class="px-6 py-4 border-b border-zinc-200/80 flex items-center justify-between bg-zinc-50/60 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-zinc-950 text-white flex items-center justify-center font-bold text-xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-zinc-950 " id="inspector-entry-id">Entry #000</h4>
                            <span id="inspector-status-badge" class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700">Completed</span>
                        </div>
                        <p class="text-[10.5px] text-zinc-400 font-mono mt-0.5" id="inspector-submitted-at">Submitted --</p>
                    </div>
                </div>
                <button onclick="closeEntryInspector()" class="h-7 w-7 rounded-lg hover:bg-zinc-200/60 flex items-center justify-center text-zinc-500 cursor-pointer">
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
    <!-- Clause Drawer Backdrop -->
    <div id="cora-clause-drawer-backdrop" class="fixed inset-0 bg-zinc-950/30 backdrop-blur-xs z-[49] hidden transition-opacity duration-300 opacity-0 pointer-events-none"></div>

    <!-- Bottom Sheet (mobile) / Right Drawer (desktop) -->
    <div id="cora-clause-drawer" class="hidden fixed z-50 transition-transform duration-300 ease-out
        bg-white shadow-2xl border-zinc-200 flex flex-col">

        <!-- Mobile grab handle -->
        <div class="flex justify-center pt-3 pb-1 md:hidden">
            <div class="w-10 h-1 rounded-full bg-zinc-300 "></div>
        </div>

        <!-- Header -->
        <div class="px-5 pt-2 md:pt-5 pb-4 border-b border-zinc-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 ">Add Library Clause</h3>
                <p class="text-[10px] text-zinc-400 mt-0.5">Pick a template or write a custom clause</p>
            </div>
            <button id="btn-close-clause-drawer" class="h-8 w-8 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-500 transition-colors cursor-pointer border-0 bg-transparent">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Scrollable Body -->
        <div class="flex-1 overflow-y-auto px-5 pt-4 pb-2 flex flex-col gap-4">

            <!-- Quick-Select Template Chips -->
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Quick Templates</label>
                <div class="flex flex-wrap gap-1.5" id="clause-template-chips">
                    <button type="button" class="clause-chip h-7 px-2.5 rounded-full border border-zinc-200 bg-zinc-50 text-[10px] font-medium text-zinc-600 hover:bg-zinc-100 hover:border-zinc-300 transition-all cursor-pointer flex items-center gap-1"
                        data-key="gdpr_consent" data-title="GDPR Data Consent" data-text="By submitting this form, you consent to the processing of your personal data in accordance with the General Data Protection Regulation (EU) 2016/679. Your data will be processed solely for the stated purpose and will not be shared with third parties without your explicit consent.">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        GDPR Consent
                    </button>
                    <button type="button" class="clause-chip h-7 px-2.5 rounded-full border border-zinc-200 bg-zinc-50 text-[10px] font-medium text-zinc-600 hover:bg-zinc-100 hover:border-zinc-300 transition-all cursor-pointer flex items-center gap-1"
                        data-key="terms_acceptance" data-title="Terms & Conditions Acceptance" data-text="I have read and agree to the Terms and Conditions as outlined in the service agreement. I understand that my use of this service is governed by these terms and I accept all obligations therein.">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        Terms & Conditions
                    </button>
                    <button type="button" class="clause-chip h-7 px-2.5 rounded-full border border-zinc-200 bg-zinc-50 text-[10px] font-medium text-zinc-600 hover:bg-zinc-100 hover:border-zinc-300 transition-all cursor-pointer flex items-center gap-1"
                        data-key="nda_clause" data-title="Non-Disclosure Agreement" data-text="The receiving party agrees to hold all confidential information in strict confidence and not to disclose such information to any third party without prior written consent. This obligation survives termination of the agreement.">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        NDA
                    </button>
                    <button type="button" class="clause-chip h-7 px-2.5 rounded-full border border-zinc-200 bg-zinc-50 text-[10px] font-medium text-zinc-600 hover:bg-zinc-100 hover:border-zinc-300 transition-all cursor-pointer flex items-center gap-1"
                        data-key="liability_waiver" data-title="Limitation of Liability" data-text="In no event shall either party be liable for any indirect, incidental, special, consequential, or punitive damages arising out of or in connection with this agreement, regardless of the cause of action.">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        Liability Waiver
                    </button>
                    <button type="button" class="clause-chip h-7 px-2.5 rounded-full border border-zinc-200 bg-zinc-50 text-[10px] font-medium text-zinc-600 hover:bg-zinc-100 hover:border-zinc-300 transition-all cursor-pointer flex items-center gap-1"
                        data-key="data_retention" data-title="Data Retention Policy" data-text="Personal data collected through this form will be retained for a maximum period of 36 months from the date of submission. After this period, data will be securely deleted unless otherwise required by applicable law.">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Data Retention
                    </button>
                    <button type="button" class="clause-chip h-7 px-2.5 rounded-full border border-zinc-200 bg-zinc-50 text-[10px] font-medium text-zinc-600 hover:bg-zinc-100 hover:border-zinc-300 transition-all cursor-pointer flex items-center gap-1"
                        data-key="aml_compliance" data-title="AML / KYC Compliance" data-text="The undersigned confirms compliance with all applicable Anti-Money Laundering (AML) and Know Your Customer (KYC) regulations. All information provided is accurate and complete to the best of their knowledge.">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        AML / KYC
                    </button>
                </div>
            </div>

            <div class="h-px bg-zinc-100 -mx-1"></div>

            <!-- Clause Key -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Clause Key</label>
                <input id="drawer-clause-key" type="text" placeholder="e.g. swiss_aml_statement" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none focus:border-zinc-400 w-full font-mono placeholder:text-zinc-300 transition-colors" />
            </div>

            <!-- Clause Title -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Clause Title</label>
                <input id="drawer-clause-title" type="text" placeholder="e.g. GDPR Data Consent" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none focus:border-zinc-400 w-full font-medium placeholder:text-zinc-300 transition-colors" />
            </div>

            <!-- Clause Content -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Content</label>
                <textarea id="drawer-clause-text" rows="4" placeholder="Enter clause legal text here..." class="p-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none focus:border-zinc-400 w-full resize-none placeholder:text-zinc-300 transition-colors leading-relaxed"></textarea>
            </div>
        </div>

        <!-- Footer CTA — pb-20 on mobile gives clearance above the app nav -->
        <div class="px-5 pt-4 pb-5 md:pb-5 pb-20 border-t border-zinc-100 " style="padding-bottom: max(1.25rem, calc(env(safe-area-inset-bottom, 0px) + 5rem));">
            <button id="btn-save-drawer-clause" class="w-full h-10 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold transition-all cursor-pointer border-0 flex items-center justify-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Save Clause
            </button>
        </div>
    </div>

    <!-- GENERIC CONFIRMATION MODAL POPUP -->
    <div id="cora-confirm-modal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-zinc-950/40 backdrop-blur-xs transition-all duration-200">
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-2xl max-w-sm w-full space-y-4 relative mx-4 transform transition-all scale-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-50 border border-red-200 flex items-center justify-center text-red-600 shrink-0">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <h3 class="text-sm font-bold text-zinc-900 tracking-tight" id="confirm-modal-title">Delete Confirmation</h3>
                        <span class="text-[10px] font-semibold text-red-600 uppercase tracking-wider">Permanent Action</span>
                    </div>
                </div>
                <button id="btn-close-confirm" type="button" class="h-7 w-7 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-400 hover:text-zinc-600 transition-colors border-0 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <p id="confirm-message-text" class="text-xs text-zinc-600 leading-relaxed">Are you sure you want to delete this form and all responses? This action cannot be undone.</p>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-zinc-100 ">
                <button id="btn-cancel-confirm" type="button" class="h-9 px-4 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-700 transition-all cursor-pointer">
                    Cancel
                </button>
                <button id="btn-confirm-action" type="button" class="h-9 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer border-none flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    Delete Permanently
                </button>
            </div>
        </div>
    </div>

    <!-- SHARE FORM MODAL POPUP -->
    <div id="cora-share-modal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-zinc-950/40 backdrop-blur-xs transition-all duration-200">
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-2xl max-w-md w-full space-y-5 relative mx-4 transform transition-all scale-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-900 ">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><polyline points="16 6 12 2 8 6"></polyline><line x1="12" y1="2" x2="12" y2="15"></line></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 " id="share-modal-title">Share Form</h3>
                        <p class="text-[10px] text-zinc-400 font-medium">Distribute via direct link, WhatsApp, or email</p>
                    </div>
                </div>
                <button id="btn-close-share-modal" type="button" class="h-7 w-7 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-400 hover:text-zinc-600 transition-colors border-0 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Public Link Copy Box -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Public Shareable Link</label>
                <div class="flex items-center gap-2">
                    <input id="share-modal-url-input" type="text" readonly class="h-9 px-3 rounded-xl border border-zinc-200 bg-zinc-50 text-xs text-zinc-800 font-mono flex-1 outline-none select-all" />
                    <button id="btn-share-copy-link" type="button" class="h-9 px-4 rounded-xl bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 shrink-0 transition-all border-0 cursor-pointer flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        Copy Link
                    </button>
                </div>
            </div>

            <!-- Quick Share Options Grid -->
            <div class="grid grid-cols-2 gap-3 pt-1">
                <button id="btn-share-whatsapp" type="button" class="p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100/50 text-emerald-800 flex items-center gap-2.5 transition-all text-xs font-semibold cursor-pointer border-0">
                    <div class="w-7 h-7 rounded-lg bg-[#25D366] text-white flex items-center justify-center shrink-0 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.461c-1.854 0-3.674-.496-5.267-1.438l-.377-.223-3.916 1.027 1.045-3.816-.245-.39c-1.034-1.646-1.58-3.559-1.579-5.518.003-5.69 4.628-10.316 10.32-10.316 2.756.001 5.347 1.074 7.294 3.023 1.947 1.948 3.018 4.54 3.017 7.297-.003 5.692-4.628 10.317-10.32 10.317m0-21.728c-6.29 0-11.412 5.121-11.415 11.414-.002 2.01.52 3.972 1.511 5.694l-1.605 5.864 6.001-1.574c1.66 1.048 3.582 1.6 5.503 1.601h.005c6.289 0 11.412-5.122 11.415-11.414.002-3.048-1.182-5.914-3.332-8.064-2.15-2.15-5.015-3.334-8.066-3.335"/></svg>
                    </div>
                    <span>Share on WhatsApp</span>
                </button>

                <button id="btn-share-email" type="button" class="p-3 rounded-xl border border-blue-200 bg-blue-50/50 hover:bg-blue-100/50 text-blue-800 flex items-center gap-2.5 transition-all text-xs font-semibold cursor-pointer border-0">
                    <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <span>Send via Email</span>
                </button>
            </div>

            <!-- Embed Code Box -->
            <div class="space-y-1.5 pt-2 border-t border-zinc-100 ">
                <div class="flex items-center justify-between">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Embed Code (iFrame)</label>
                    <button id="btn-copy-embed-code" type="button" class="text-[10px] font-bold text-zinc-900 hover:underline cursor-pointer border-0 bg-transparent">Copy Code</button>
                </div>
                <input id="share-modal-embed-input" type="text" readonly class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-zinc-50 text-[11px] text-zinc-600 font-mono w-full outline-none select-all" />
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
<?php
global $wpdb;
$agency_id = cora_db_get_agency_id();
$forms_db = $wpdb->get_results( $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}cora_forms WHERE agency_id = %d ORDER BY id DESC",
    $agency_id
), ARRAY_A );
$prepopulated_forms = array();
if ( is_array( $forms_db ) ) {
    foreach ( $forms_db as $form ) {
        if ( empty( $form['form_key'] ) ) {
            $form['form_key'] = 'frm_' . substr( md5( $form['id'] . $form['title'] ), 0, 8 );
            $wpdb->update( $wpdb->prefix . 'cora_forms', array( 'form_key' => $form['form_key'] ), array( 'id' => $form['id'] ) );
        }
        $form['styling'] = json_decode( $form['styling'], true ) ?: array();
        $form['settings'] = json_decode( $form['settings'], true ) ?: array();
        
        // Fetch blocks
        $blocks_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_form_blocks WHERE form_id = %d", $form['id'] ), ARRAY_A );
        $form['blocks'] = $blocks_row ? (json_decode( $blocks_row['blocks_json'], true ) ?: array()) : array();
        $form['logic'] = $blocks_row ? (json_decode( $blocks_row['logic_json'], true ) ?: array()) : array();
        
        // Get response count
        $resp_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_form_submissions WHERE form_id = %d", $form['id'] ) );
        $form['submission_count'] = intval( $resp_count );
        
        $prepopulated_forms[] = $form;
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let formsData = <?php echo json_encode( $prepopulated_forms ); ?>;
    let initialLoad = true;
    let currentEditingForm = null;
    let selectedBlockIndex = null;
    let autoSaveTimer = null;
    let currentAuditPage = 1;

    const wpNonce = (typeof coraREData !== 'undefined' && coraREData.nonce) ? coraREData.nonce : ((typeof wpApiSettings !== 'undefined') ? wpApiSettings.nonce : '');

    function getCoraRestUrl(path) {
        let base = (typeof coraREData !== 'undefined' && coraREData.restUrl) ? coraREData.restUrl : '/wp-json/';
        if (!base.endsWith('/')) base += '/';
        if (path.startsWith('/')) path = path.slice(1);
        return base + path;
    }

    // --- Type Meta ---
    const TYPE_META = {
        text:           { label: 'Short Text',     badge: 'Text',     icon: 'T' },
        input:          { label: 'Short Text',     badge: 'Text',     icon: 'T' },
        long_text:      { label: 'Long Text',      badge: 'Text',     icon: '\u2261' },
        textarea:       { label: 'Long Text',      badge: 'Text',     icon: '\u2261' },
        email:          { label: 'Email',          badge: 'Email',    icon: '@' },
        phone:          { label: 'Phone',          badge: 'Phone',    icon: 'P' },
        tel:            { label: 'Phone',          badge: 'Phone',    icon: 'P' },
        number:         { label: 'Number',         badge: 'Number',   icon: '#' },
        dropdown:       { label: 'Dropdown',       badge: 'Dropdown', icon: 'v' },
        select:         { label: 'Dropdown',       badge: 'Dropdown', icon: 'v' },
        multiple_choice:{ label: 'Multiple Choice',badge: 'Choice',   icon: 'o' },
        radio:          { label: 'Multiple Choice',badge: 'Choice',   icon: 'o' },
        multiselect:    { label: 'Multi-Select',   badge: 'Choice',   icon: 'o' },
        checkbox:       { label: 'Checkboxes',     badge: 'Check',    icon: '[x]' },
        date:           { label: 'Date',           badge: 'Date',     icon: 'D' },
        file:           { label: 'File Upload',    badge: 'File',     icon: '^' },
        signature:      { label: 'Signature',      badge: 'Sign',     icon: 'S' },
        rating:         { label: 'Rating',         badge: 'Rating',   icon: '*' },
        slider:         { label: 'Slider',         badge: 'Slider',   icon: '-' },
        payment:        { label: 'UPI ID',         badge: 'Payment',  icon: '₹' },
        stripe_payment: { label: 'UPI ID',         badge: 'Payment',  icon: '₹' },
        upi_id:         { label: 'UPI ID',         badge: 'Payment',  icon: '₹' },
        upi_qr:         { label: 'UPI QR Code',    badge: 'Payment',  icon: '▦' },
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
        booking:        { label: 'Booking Slots',  badge: 'Booking',  icon: '\ud83d\udcc5' },
        address:        { label: 'Address Field',  badge: 'Address',  icon: '\ud83d\udccd' },
        services_checklist: { label: 'Pricing List',   badge: 'Pricing',  icon: '\ud83d\udcb0' },
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
    const confirmModal = document.getElementById('cora-confirm-modal');
    let confirmCallback = null;

    const closeConfirmModal = () => {
        if (confirmModal) {
            confirmModal.classList.remove('pointer-events-auto', 'flex');
            confirmModal.classList.add('hidden', 'pointer-events-none');
        }
    };

    document.getElementById('btn-close-confirm')?.addEventListener('click', closeConfirmModal);
    document.getElementById('btn-cancel-confirm')?.addEventListener('click', closeConfirmModal);
    document.getElementById('btn-confirm-action')?.addEventListener('click', () => {
        if (typeof confirmCallback === 'function') {
            confirmCallback();
        }
        closeConfirmModal();
    });
    
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

    jQuery(document).on('click', '#btn-audit-prev', function(e) {
        e.preventDefault();
        if (currentAuditPage > 1) {
            fetchAuditLogs(currentAuditPage - 1);
        }
    });

    jQuery(document).on('click', '#btn-audit-next', function(e) {
        e.preventDefault();
        fetchAuditLogs(currentAuditPage + 1);
    });

    // ── Clause Drawer: Open / Close / Save ──
    const isMdBreakpoint = () => window.matchMedia('(min-width: 768px)').matches;

    function openClauseDrawer() {
        const drawer = document.getElementById('cora-clause-drawer');
        const backdrop = document.getElementById('cora-clause-drawer-backdrop');
        
        if (!drawer) return;

        // Reset base positioning classes
        drawer.className = 'fixed z-50 transition-transform duration-300 ease-out bg-white shadow-2xl border-zinc-200 flex flex-col pointer-events-auto';

        if (isMdBreakpoint()) {
            // Desktop side drawer
            drawer.classList.add('inset-y-0', 'right-0', 'w-[450px]', 'border-l', 'translate-x-full');
        } else {
            // Mobile bottom sheet
            drawer.classList.add('inset-x-0', 'bottom-0', 'max-h-[85vh]', 'rounded-t-2xl', 'border-t', 'translate-y-full');
        }

        drawer.classList.remove('hidden');

        requestAnimationFrame(() => {
            if (isMdBreakpoint()) {
                drawer.classList.remove('translate-x-full');
                drawer.classList.add('translate-x-0');
            } else {
                drawer.classList.remove('translate-y-full');
                drawer.classList.add('translate-y-0');
            }
        });

        if (backdrop) {
            backdrop.classList.remove('hidden', 'pointer-events-none');
            backdrop.classList.add('pointer-events-auto');
            requestAnimationFrame(() => {
                backdrop.classList.add('opacity-100');
                backdrop.classList.remove('opacity-0');
            });
        }
    }

    function closeClauseDrawer() {
        const drawer = document.getElementById('cora-clause-drawer');
        const backdrop = document.getElementById('cora-clause-drawer-backdrop');

        if (drawer) {
            drawer.classList.remove('pointer-events-auto');
            drawer.classList.add('pointer-events-none');
            if (isMdBreakpoint()) {
                drawer.classList.add('translate-x-full');
                drawer.classList.remove('translate-x-0');
            } else {
                drawer.classList.add('translate-y-full');
                drawer.classList.remove('translate-y-0');
            }
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        }

        if (backdrop) {
            backdrop.classList.remove('pointer-events-auto');
            backdrop.classList.add('pointer-events-none', 'opacity-0');
            backdrop.classList.remove('opacity-100');
            setTimeout(() => {
                backdrop.classList.add('hidden');
            }, 300);
        }

        // Reset form fields and chip selection
        const keyInput = document.getElementById('drawer-clause-key');
        const titleInput = document.getElementById('drawer-clause-title');
        const textInput = document.getElementById('drawer-clause-text');
        if (keyInput) keyInput.value = '';
        if (titleInput) titleInput.value = '';
        if (textInput) textInput.value = '';
        document.querySelectorAll('.clause-chip').forEach(c => {
            c.classList.remove('bg-zinc-900', 'text-white', 'border-zinc-900');
            c.classList.add('bg-zinc-50', 'text-zinc-600', 'border-zinc-200');
        });
    }

    // Template chip click → auto-fill fields
    jQuery(document).on('click', '.clause-chip', function(e) {
        e.preventDefault();
        const chip = this;
        // Deselect all chips
        document.querySelectorAll('.clause-chip').forEach(c => {
            c.classList.remove('bg-zinc-900', 'text-white', 'border-zinc-900');
            c.classList.add('bg-zinc-50', 'text-zinc-600', 'border-zinc-200');
        });
        // Activate this chip
        chip.classList.remove('bg-zinc-50', 'text-zinc-600', 'border-zinc-200');
        chip.classList.add('bg-zinc-900', 'text-white', 'border-zinc-900');
        // Fill form
        const keyInput = document.getElementById('drawer-clause-key');
        const titleInput = document.getElementById('drawer-clause-title');
        const textInput = document.getElementById('drawer-clause-text');
        if (keyInput) keyInput.value = chip.dataset.key || '';
        if (titleInput) titleInput.value = chip.dataset.title || '';
        if (textInput) textInput.value = chip.dataset.text || '';
    });

    jQuery(document).on('click', '#btn-create-clause', function(e) {
        e.preventDefault();
        openClauseDrawer();
    });

    jQuery(document).on('click', '#btn-close-clause-drawer', function(e) {
        e.preventDefault();
        closeClauseDrawer();
    });

    jQuery(document).on('click', '#cora-clause-drawer-backdrop', function() {
        closeClauseDrawer();
    });

    jQuery(document).on('click', '#btn-save-drawer-clause', function(e) {
        e.preventDefault();
        const clauseKey = (document.getElementById('drawer-clause-key')?.value || '').trim();
        const clauseTitle = (document.getElementById('drawer-clause-title')?.value || '').trim();
        const clauseText = (document.getElementById('drawer-clause-text')?.value || '').trim();

        if (!clauseKey || !clauseTitle || !clauseText) {
            window.coraShowToast && window.coraShowToast("Please fill in all fields before saving.");
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full"></span> Saving...';

        jQuery.ajax({
            url: getCoraRestUrl('cora/v1/forms/clauses'),
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ clause_key: clauseKey, title: clauseTitle, content_text: clauseText }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function() {
                window.coraShowToast && window.coraShowToast("Clause saved successfully.");
                closeClauseDrawer();
                fetchClauses();
            },
            error: function() {
                window.coraShowToast && window.coraShowToast("Failed to save clause. Please try again.");
            },
            complete: function() {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> Save Clause';
            }
        });
    });


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
        if (initialLoad && formsData && formsData.length > 0) {
            initialLoad = false;
            renderFormsList();
            updateMetrics();
            return;
        }
        initialLoad = false;
        jQuery.ajax({
            url: getCoraRestUrl('cora/v1/forms'),
            method: 'GET',
            cache: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(response) {
                formsData = response;
                renderFormsList();
                updateMetrics();
            },
            error: function(err) {
                // Ignore silent load error
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
            jQuery.ajax({
                url: getCoraRestUrl('cora/v1/forms/submissions'),
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wpNonce);
                },
                success: function(submissions) {
                    const started = submissions.length;
                    const completed = submissions.filter(s => s.is_partial == '0').length;
                    const views = Math.round(Math.max(formsData.length * 15, totalSubmissions * 1.6));
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
                    
                    let allInputBlocks = [];
                    formsData.forEach(form => {
                        const inputs = (form.blocks || []).filter(b => 
                            b.type !== 'header' && b.type !== 'paragraph' && b.type !== 'divider' && b.type !== 'page_break' && b.type !== 'stripe_payment'
                        );
                        allInputBlocks = allInputBlocks.concat(inputs);
                    });

                    const uniqueLabels = [...new Set(allInputBlocks.map(b => b.label).filter(l => l))];

                    if (uniqueLabels.length === 0) {
                        frictionContainer.innerHTML = `<div class="text-[10px] text-zinc-400 text-center py-4">No input fields found across any forms.</div>`;
                        return;
                    }

                    const fieldStats = uniqueLabels.map(label => {
                        let fillCount = 0;
                        let relevantForms = formsData.filter(form => {
                            return (form.blocks || []).some(b => 
                                b.type !== 'header' && b.type !== 'paragraph' && b.type !== 'divider' && b.type !== 'page_break' && b.type !== 'stripe_payment' && b.label === label
                            );
                        }).map(f => f.id);

                        // Use string match or direct type match based on what form_id is
                        let relevantSubmissions = submissions.filter(sub => relevantForms.includes(String(sub.form_id)) || relevantForms.includes(Number(sub.form_id)));

                        relevantSubmissions.forEach(sub => {
                            const val = sub.submitted_data[label];
                            if (val !== undefined && val !== null && val !== '') {
                                fillCount++;
                            }
                        });
                        
                        const relevantStarted = relevantSubmissions.length;
                        const rate = relevantStarted > 0 ? Math.round((fillCount / relevantStarted) * 100) : 0;
                        return {
                            label: label,
                            count: fillCount,
                            rate: rate,
                            started: relevantStarted
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
                                <span class="text-[9px] text-zinc-400 font-medium">${fStat.count} of ${fStat.started} respondents</span>
                                ${frictionLabel}
                            </div>
                        `;
                        frictionContainer.appendChild(row);
                    });
                }
            });
        } else {
            const formObj = formsData.find(f => f.id == selectedId);
            if (!formObj) return;
            
            jQuery.ajax({
                url: getCoraRestUrl(`cora/v1/forms/${selectedId}/submissions`),
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
            url: getCoraRestUrl('cora/v1/forms/clauses'),
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
        if (!clauses || clauses.length === 0) {
            body.innerHTML = `
                <div class="col-span-full py-16 text-center">
                    <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.2" fill="none" class="mx-auto text-zinc-300 mb-3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    <p class="text-xs text-zinc-400 ">No clauses created yet. Click "+ Add Clause" to start.</p>
                </div>`;
            return;
        }

        body.innerHTML = '';
        clauses.forEach(c => {
            const card = document.createElement('div');
            card.className = 'bg-white border border-zinc-200/80 rounded-xl p-4 flex flex-col gap-3 shadow-sm hover:shadow-md hover:border-zinc-300 transition-all';

            const snippet = (c.content_text || '').length > 80 ? c.content_text.substring(0, 80) + '…' : (c.content_text || '—');

            card.innerHTML = `
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 "><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-[13px] font-semibold text-zinc-900 truncate leading-tight">${c.title}</h4>
                            <span class="font-mono text-[10px] text-zinc-400 ">${c.clause_key}</span>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-zinc-500 leading-relaxed line-clamp-3">${snippet}</p>
                <div class="flex items-center justify-end pt-2 border-t border-zinc-100 ">
                    <button class="btn-delete-db-clause h-7 px-2.5 rounded-lg bg-transparent hover:bg-red-50 text-zinc-400 hover:text-red-600 text-[10px] font-medium flex items-center gap-1 transition-all cursor-pointer border-0" data-id="${c.id}">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Delete
                    </button>
                </div>
            `;
            body.appendChild(card);
        });

        jQuery('.btn-delete-db-clause').off('click').on('click', function() {
            const cId = jQuery(this).data('id');
            coraConfirmAction("Are you sure you want to delete this clause? This action is permanent.", function() {
                jQuery.ajax({
                    url: getCoraRestUrl(`cora/v1/forms/clauses/${cId}`),
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
        });
    }

function fetchAuditLogs(page = 1) {
        currentAuditPage = page;
        jQuery.ajax({
            url: getCoraRestUrl(`cora/v1/forms/audit-log?page=${page}&per_page=10`),
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(response) {
                const logs = Array.isArray(response) ? response : (response.logs || response.data || []);
                const total = response.total !== undefined ? response.total : logs.length;
                const totalPages = response.total_pages !== undefined ? response.total_pages : 1;
                
                renderAuditLogs(logs);
                updateAuditPagination(total, totalPages);
            }
        });
    }

function renderAuditLogs(logs) {
        const body = document.getElementById('audit-logs-body');
        if (!body) return;
        if (!logs || logs.length === 0) {
            body.innerHTML = `
                <tr>
                    <td colspan="5" class="py-16 text-center">
                        <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.2" fill="none" class="mx-auto text-zinc-300 mb-3"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <p class="text-xs text-zinc-400 ">No audit log entries recorded.</p>
                    </td>
                </tr>`;
            return;
        }

        body.innerHTML = '';
        logs.forEach(l => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-zinc-50/50 transition-all';

            const reviewer = l.display_name || 'System';
            const target = l.field_label || (l.submission_id ? 'Submission #' + l.submission_id : 'All Data');
            const actionType = l.action_type || 'unknown';

            // Action icon mapping
            let actionIcon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
            if (actionType.includes('read') || actionType.includes('view')) {
                actionIcon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            } else if (actionType.includes('export') || actionType.includes('download')) {
                actionIcon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>';
            } else if (actionType.includes('verify') || actionType.includes('check')) {
                actionIcon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>';
            } else if (actionType.includes('submit')) {
                actionIcon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>';
            }

            row.innerHTML = `
                <td class="px-4 py-3 align-middle">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-500 shrink-0">
                            ${actionIcon}
                        </div>
                        <span class="px-1.5 py-0.5 rounded font-mono text-[9px] font-bold bg-zinc-100 text-zinc-650 uppercase tracking-wide whitespace-nowrap">${actionType}</span>
                    </div>
                </td>
                <td class="px-4 py-3 align-middle font-medium text-zinc-800 ">
                    ${reviewer}
                </td>
                <td class="px-4 py-3 align-middle text-zinc-600 ">
                    ${target}
                </td>
                <td class="px-4 py-3 align-middle font-mono text-zinc-450 whitespace-nowrap">
                    ${l.ip_address || '—'}
                </td>
                <td class="px-4 py-3 align-middle text-zinc-400 whitespace-nowrap">
                    ${l.created_at || '—'}
                </td>
            `;
            body.appendChild(row);
        });
    }

function updateAuditPagination(total, totalPages) {
        const info = document.getElementById('audit-pagination-info');
        if (info) {
            info.textContent = `Showing page ${currentAuditPage} of ${totalPages} (Total ${total} logs)`;
        }
        const btnPrev = document.getElementById('btn-audit-prev');
        const btnNext = document.getElementById('btn-audit-next');
        if (btnPrev) {
            btnPrev.disabled = currentAuditPage <= 1;
        }
        if (btnNext) {
            btnNext.disabled = currentAuditPage >= totalPages;
        }
    }

function coraConfirmAction(message, onConfirm) {
        const msgEl = document.getElementById('confirm-message-text');
        if (msgEl) msgEl.textContent = message;
        confirmCallback = onConfirm;
        const modal = document.getElementById('cora-confirm-modal');
        if (modal) {
            modal.classList.remove('hidden', 'pointer-events-none');
            modal.classList.add('flex', 'pointer-events-auto');
        }
    }

function deleteForm(id) {
        jQuery.ajax({
            url: getCoraRestUrl(`cora/v1/forms/${id}`),
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

    window.openSubmissionsDrawer = function(formId) {
        const formObj = formsData.find(f => f.id == formId);
        if (!formObj) return;

        const titleEl = document.getElementById('drawer-form-title');
        if (titleEl) titleEl.textContent = formObj.title;
        
        // Show backdrop and drawer
        const backdrop = document.getElementById('cora-submissions-backdrop');
        const drawer = document.getElementById('cora-submissions-drawer');
        if (backdrop) {
            backdrop.classList.remove('hidden', 'pointer-events-none');
            backdrop.classList.add('pointer-events-auto');
        }
        if (drawer) {
            drawer.classList.remove('hidden', 'pointer-events-none');
            drawer.classList.add('pointer-events-auto');
            // Force redraw/reflow for transition
            drawer.offsetHeight;
            drawer.classList.remove('translate-x-full');
        }

        const content = document.getElementById('submissions-drawer-content');
        if (content) content.innerHTML = '<div class="text-xs text-zinc-400 text-center py-8">Loading submissions...</div>';

        jQuery.ajax({
            url: getCoraRestUrl(`cora/v1/forms/${formId}/submissions`),
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(submissions) {
                const countEl = document.getElementById('drawer-responses-count');
                if (countEl) countEl.textContent = submissions.length + ' Entries';
                
                if (submissions.length === 0) {
                    if (content) content.innerHTML = '<div class="text-xs text-zinc-400 text-center py-8">No submissions recorded for this form yet.</div>';
                    return;
                }

                // Render submissions table
                let html = `
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-zinc-200 text-zinc-400 font-semibold bg-zinc-50 ">
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">IP Address</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Submitted At</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-150 ">
                `;

                submissions.forEach((sub, idx) => {
                    const label = sub.is_partial == '1' ? 'Partial' : 'Completed';
                    const badgeClass = sub.is_partial == '1' 
                        ? 'bg-zinc-100 text-zinc-650 ' 
                        : 'bg-emerald-50 text-emerald-700 ';
                    
                    html += `
                        <tr class="hover:bg-zinc-50/50 transition-all">
                            <td class="px-4 py-3.5 font-semibold text-zinc-900 ">Entry #${submissions.length - idx}</td>
                            <td class="px-4 py-3.5 font-mono text-zinc-500">${sub.ip_address || 'Unknown'}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded text-[9px] font-bold uppercase ${badgeClass}">${label}</span>
                            </td>
                            <td class="px-4 py-3.5 text-zinc-500">${sub.created_at}</td>
                            <td class="px-4 py-3.5 text-right">
                                <button class="btn-inspect-entry h-7 px-2.5 rounded-lg border border-zinc-200 hover:border-zinc-300 bg-white text-zinc-500 hover:text-zinc-950 cursor-pointer transition-all" data-idx="${idx}">
                                    Inspect
                                </button>
                            </td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                `;

                if (content) content.innerHTML = html;

                // Attach entry inspectors click
                jQuery('.btn-inspect-entry').on('click', function() {
                    const idx = jQuery(this).data('idx');
                    const sub = submissions[idx];
                    openEntryInspector(sub, submissions.length - idx);
                });
            },
            error: function() {
                if (content) content.innerHTML = '<div class="text-xs text-red-500 text-center py-8">Failed to load submissions.</div>';
            }
        });
    };

    window.closeSubmissionsDrawer = function() {
        const backdrop = document.getElementById('cora-submissions-backdrop');
        const drawer = document.getElementById('cora-submissions-drawer');
        const inspector = document.getElementById('cora-entry-inspector');
        
        if (drawer) {
            drawer.classList.remove('pointer-events-auto');
            drawer.classList.add('translate-x-full', 'pointer-events-none');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        }
        if (backdrop) {
            backdrop.classList.remove('pointer-events-auto');
            backdrop.classList.add('hidden', 'pointer-events-none');
        }
        if (inspector) {
            inspector.classList.remove('pointer-events-auto');
            inspector.classList.add('translate-x-full', 'pointer-events-none');
        }
    };

    window.openEntryInspector = function(sub, entryNumber) {
        const idEl = document.getElementById('inspector-entry-id');
        if (idEl) idEl.textContent = `Entry #${entryNumber}`;
        const timeEl = document.getElementById('inspector-submitted-at');
        if (timeEl) timeEl.textContent = `Submitted ${sub.created_at} (${sub.ip_address || 'no IP'})`;
        
        const badge = document.getElementById('inspector-status-badge');
        if (badge) {
            if (sub.is_partial == '1') {
                badge.textContent = 'Partial';
                badge.className = 'px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-zinc-100 text-zinc-650 ';
            } else {
                badge.textContent = 'Completed';
                badge.className = 'px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700 ';
            }
        }

        const bodyContent = document.getElementById('inspector-body-content');
        if (bodyContent) {
            bodyContent.innerHTML = '';
            
            const data = sub.submitted_data || {};
            const keys = Object.keys(data);
            
            if (keys.length === 0) {
                bodyContent.innerHTML = '<div class="text-xs text-zinc-400 text-center py-6">No data fields submitted in this entry.</div>';
            } else {
                keys.forEach(k => {
                    const val = data[k];
                    const card = document.createElement('div');
                    card.className = 'p-3.5 bg-zinc-50/50 border border-zinc-150 rounded-xl flex flex-col gap-1';
                    card.innerHTML = `
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">${k}</span>
                        <span class="text-xs font-medium text-zinc-800 leading-relaxed whitespace-pre-wrap">${val !== undefined && val !== null && val !== '' ? val : '<span class="text-zinc-300 italic">Empty</span>'}</span>
                    `;
                    bodyContent.appendChild(card);
                });
            }
        }

        const inspector = document.getElementById('cora-entry-inspector');
        if (inspector) {
            inspector.classList.remove('hidden', 'pointer-events-none');
            inspector.classList.add('pointer-events-auto');
            // Force redraw/reflow for transition
            inspector.offsetHeight;
            inspector.classList.remove('translate-x-full');
        }
    };

    window.closeEntryInspector = function() {
        const inspector = document.getElementById('cora-entry-inspector');
        if (inspector) {
            inspector.classList.remove('pointer-events-auto');
            inspector.classList.add('translate-x-full', 'pointer-events-none');
        }
    };

function getSelectedFormIds() {
    const ids = [];
    jQuery('.form-select-checkbox:checked').each(function() {
        const id = parseInt(jQuery(this).data('id'));
        if (id) ids.push(id);
    });
    return ids;
}

function updateBulkActionBarState() {
    const selectedIds = getSelectedFormIds();
    const bulkBar = document.getElementById('forms-bulk-actions-bar');
    const countEl = document.getElementById('bulk-selected-count');
    if (!bulkBar || !countEl) return;

    if (selectedIds.length > 0) {
        countEl.textContent = `${selectedIds.length} form${selectedIds.length === 1 ? '' : 's'} selected`;
        bulkBar.classList.remove('hidden');
        bulkBar.classList.add('flex');
    } else {
        bulkBar.classList.add('hidden');
        bulkBar.classList.remove('flex');
    }
}

function executeBulkFormAction(action) {
    const selectedIds = getSelectedFormIds();
    if (selectedIds.length === 0) return;

    const performRequest = () => {
        jQuery.ajax({
            url: getCoraRestUrl('cora/v1/forms/bulk'),
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            data: JSON.stringify({ action: action, ids: selectedIds }),
            contentType: 'application/json',
            success: function(res) {
                const count = selectedIds.length;
                let msg = '';
                if (action === 'delete') msg = `${count} form${count === 1 ? '' : 's'} deleted successfully!`;
                else if (action === 'publish') msg = `${count} form${count === 1 ? '' : 's'} published successfully!`;
                else if (action === 'draft') msg = `${count} form${count === 1 ? '' : 's'} set to draft!`;

                window.coraShowToast && window.coraShowToast(msg, "success");
                fetchForms();
            },
            error: function(err) {
                window.coraShowToast && window.coraShowToast("Failed to perform bulk operation.", "error");
            }
        });
    };

    if (action === 'delete') {
        const count = selectedIds.length;
        coraConfirmAction(`Are you sure you want to delete ${count} selected form${count === 1 ? '' : 's'} and all associated submissions? This action is permanent.`, performRequest);
    } else {
        performRequest();
    }
}

function renderFormsList() {
        const body = document.getElementById('forms-list-body');

        if (formsData.length === 0) {
            body.innerHTML = `
                <div class="col-span-full py-16 text-center">
                    <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" stroke-width="1.2" fill="none" class="mx-auto text-zinc-300 mb-3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <p class="text-xs text-zinc-400 ">No forms found. Create one to get started.</p>
                </div>`;
            return;
        }

        body.innerHTML = '';
        formsData.forEach(form => {
            const card = document.createElement('div');
            card.className = 'form-card bg-white border border-zinc-200/80 rounded-xl p-5 flex flex-col gap-4 shadow-sm hover:shadow-md hover:border-zinc-300 transition-all group';
            card.setAttribute('data-form-id', form.id);

            const statusClass = (form.status || 'draft') === 'published'
                ? 'bg-emerald-50 text-emerald-700 '
                : 'bg-zinc-100 text-zinc-500 ';
            const statusText = (form.status || 'draft').toUpperCase();
            const responses = form.submission_count || 0;
            const created = form.created_at || '—';

            card.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 "><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-[13px] font-semibold text-zinc-900 truncate leading-tight">${form.title}</h4>
                            <p class="text-[10px] text-zinc-400 mt-0.5">${created}</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold shrink-0 ${statusClass}">${statusText}</span>
                </div>

                <div class="flex items-center gap-4 text-[11px] text-zinc-500 ">
                    <div class="flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 "><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                        <span class="font-medium">${responses} response${responses !== 1 ? 's' : ''}</span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 pt-3 border-t border-zinc-100 ">
                    <!-- Desktop Edit Button (Opens visual customizer) -->
                    <button class="btn-edit-form hidden sm:flex h-8 flex-1 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-[11px] font-semibold items-center justify-center gap-1.5 transition-all cursor-pointer border-0" data-id="${form.id}" title="Edit Form in Customizer (Desktop)">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        Edit
                    </button>
                    <!-- Mobile AI Edit Button (Opens 2-way Agentic AI) -->
                    <button class="btn-edit-ai-mobile sm:hidden h-8 flex-1 rounded-lg bg-zinc-950 text-white text-[11px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer border-0" onclick="window.coraPromptFormAI('${form.id}', '${(form.title || 'Form').replace(/'/g, "\\'")}')">
                        <svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor"><path d="M11.04 19.32Q12 21.51 12 24q0-2.49.93-4.68.96-2.19 2.58-3.81t3.81-2.55Q21.51 12 24 12q-2.49 0-4.68-.93a12.3 12.3 0 0 1-3.81-2.58 12.3 12.3 0 0 1-2.58-3.81Q12 2.49 12 0q0 2.49-.96 4.68-.93 2.19-2.55 3.81a12.3 12.3 0 0 1-3.81 2.58Q2.49 12 0 12q2.49 0 4.68.96 2.19.93 3.81 2.55t2.55 3.81"/></svg>
                        Edit with AI
                    </button>
                    <button class="btn-view-subs h-8 flex-1 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 hover:text-zinc-950 text-[11px] font-medium flex items-center justify-center gap-1.5 transition-all cursor-pointer" data-id="${form.id}" title="View Submissions">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                        Responses
                    </button>
                    <button class="btn-view-live h-8 w-8 rounded-lg bg-transparent hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-all cursor-pointer shrink-0 border-0" data-id="${form.id}" title="View Live Form">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                    <button class="btn-share-form h-8 w-8 rounded-lg bg-transparent hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-all cursor-pointer shrink-0 border-0" data-id="${form.id}" title="Copy Share Link">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    </button>
                    <button class="btn-delete-form h-8 w-8 rounded-lg bg-transparent hover:bg-red-50 text-zinc-400 hover:text-red-600 flex items-center justify-center transition-all cursor-pointer shrink-0 border-0" data-id="${form.id}" title="Delete Form">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                </div>
            `;
            body.appendChild(card);
        });

        // Attach listeners
        jQuery('.btn-view-live').on('click', function() {
            const id = jQuery(this).data('id');
            const formObj = (formsData || []).find(f => f.id == id);
            const key = (formObj && formObj.form_key) ? formObj.form_key : id;
            let siteUrl = coraREData.siteUrl || '';
            if (siteUrl.endsWith('/')) siteUrl = siteUrl.slice(0, -1);
            window.open(siteUrl + '/shared-form/' + key, '_blank');
        });

        jQuery('.btn-share-form').on('click', function() {
            const id = jQuery(this).data('id');
            const formObj = (formsData || []).find(f => f.id == id);
            const key = (formObj && formObj.form_key) ? formObj.form_key : id;
            let siteUrl = coraREData.siteUrl || '';
            if (siteUrl.endsWith('/')) siteUrl = siteUrl.slice(0, -1);
            const shareUrl = siteUrl + '/shared-form/' + key;
            coraCopyTextToClipboard(shareUrl);
        });

        jQuery('.btn-edit-form').on('click', function() {
            const id = jQuery(this).data('id');
            const targetHash = '#edit/' + id;
            if (window.location.hash === targetHash) {
                loadFormIntoEditor(id);
            } else {
                window.location.hash = targetHash;
            }
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

        // Intercept mobile editor access (Desktop Only)
        if (window.innerWidth < 640 && (hash.startsWith('#edit/') || hash === '#new')) {
            window.location.hash = '#list';
            if (hash === '#new') {
                window.coraPromptFormAI('', 'Create a new Notion-style lead capture form');
            } else {
                const id = hash.split('/')[1];
                const formObj = (formsData || []).find(f => f.id == id);
                const title = formObj ? formObj.title : 'Form #' + id;
                window.coraPromptFormAI(id, title);
            }
            if (window.coraShowToast) {
                window.coraShowToast('Desktop customizer active on larger screens. Opened Form AI Assistant.', 'info');
            }
            return;
        }

        // Remove loading overlay if present when not editing
        if (!hash.startsWith('#edit/')) {
            const loadingOverlay = document.getElementById('forms-loading-overlay');
            if (loadingOverlay) loadingOverlay.remove();
        }
        
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
            fetchForms();
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
            // Show a subtle loading state while the form loads (prevents blank screen)
            // Show a non-destructive loading overlay (doesn't replace listState DOM)
            const existingOverlay = document.getElementById('forms-loading-overlay');
            if (!existingOverlay) {
                const overlay = document.createElement('div');
                overlay.id = 'forms-loading-overlay';
                overlay.style.cssText = 'position:absolute;inset:0;background:rgba(255,255,255,0.85);z-index:20;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;';
                overlay.innerHTML = '<div style="width:28px;height:28px;border:2px solid #e4e4e7;border-top-color:#18181b;border-radius:50%;animation:spin 0.7s linear infinite;"></div><span style="font-size:11px;color:#71717a;font-weight:500;">Loading form editor...</span>';
                const module = document.getElementById('cora-forms-module');
                if (module) { module.style.position = 'relative'; module.appendChild(overlay); }
            }
            loadFormIntoEditor(id);
        } else if (hash === '#new') {
            createNewForm();
        } else {
            // Default to list
            if (listState) listState.classList.remove('hidden');
        }
    }

    window.addEventListener('hashchange', handleRouting);
    handleRouting(); // Process initial hash on page load

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
        const formCoverInp = document.getElementById('settings-cover-url');
        if (formCoverInp) formCoverInp.value = imgUrl || '';
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
        
        // Compute steps dynamically from page_break blocks
        const pageBreaks = (currentEditingForm.blocks || []).filter(b => b.type === 'page_break');
        const stepCount = pageBreaks.length + 1;
        const steps = [];
        for (let i = 1; i <= stepCount; i++) {
            steps.push(`Step ${i}`);
        }

        let html = '';
        steps.forEach((step, idx) => {
            const active = (currentEditingForm.currentStepIndex || 0) === idx;
            const activeClasses = active 
                ? 'border-2 border-zinc-950 bg-white text-zinc-950 font-bold' 
                : 'border border-zinc-200 bg-white text-zinc-600 font-medium hover:border-zinc-400';
            
            // Delete button for step (except the first step!)
            const deleteBtn = idx > 0 ? `
                <span class="step-delete-btn hover:text-red-500 ml-1.5 cursor-pointer text-[10px]" data-step-idx="${idx}">✕</span>
            ` : '';

            html += `
                <button class="step-tab-btn h-8 px-3 rounded-lg ${activeClasses} text-xs flex items-center gap-2 shrink-0 cursor-pointer" data-step-idx="${idx}">
                    <span class="w-4 h-4 rounded bg-zinc-100 text-[10px] flex items-center justify-center">${idx + 1}</span>
                    <span>${step}</span>
                    ${deleteBtn}
                </button>
            `;
        });
        html += `
            <button id="btn-add-step" class="h-8 px-3 rounded-lg border border-dashed border-zinc-300 text-zinc-500 hover:text-zinc-900 text-xs font-semibold flex items-center gap-1 shrink-0 cursor-pointer">
                <span>+</span> Add Step
            </button>
        `;
        container.innerHTML = html;

        container.querySelectorAll('.step-tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (e.target.classList.contains('step-delete-btn')) return;
                currentEditingForm.currentStepIndex = parseInt(btn.dataset.stepIdx);
                renderStepsBar();
                renderEditorBlocks();
            });
        });

        container.querySelectorAll('.step-delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const deleteIdx = parseInt(btn.dataset.stepIdx);
                coraConfirmAction("Are you sure you want to delete this step? This will merge its fields with the previous step.", function() {
                    let pbCount = 0;
                    let targetBlockIdx = -1;
                    for (let i = 0; i < currentEditingForm.blocks.length; i++) {
                        if (currentEditingForm.blocks[i].type === 'page_break') {
                            pbCount++;
                            if (pbCount === deleteIdx) {
                                targetBlockIdx = i;
                                break;
                            }
                        }
                    }
                    if (targetBlockIdx !== -1) {
                        currentEditingForm.blocks.splice(targetBlockIdx, 1);
                        currentEditingForm.currentStepIndex = Math.max(0, deleteIdx - 1);
                        renderStepsBar();
                        renderEditorBlocks();
                        triggerAutoSave();
                    }
                });
            });
        });

        document.getElementById('btn-add-step')?.addEventListener('click', () => {
            if (!currentEditingForm.blocks) currentEditingForm.blocks = [];
            currentEditingForm.blocks.push({
                id: 'field_' + Math.random().toString(36).substr(2, 6),
                type: 'page_break',
                label: 'Page Break'
            });
            const pBreaks = currentEditingForm.blocks.filter(b => b.type === 'page_break');
            currentEditingForm.currentStepIndex = pBreaks.length;
            renderStepsBar();
            renderEditorBlocks();
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
                    btnEl.className = "flex-1 py-1.5 px-1 rounded-md text-[10px] font-bold bg-white text-zinc-950 shadow-2xs whitespace-nowrap cursor-pointer transition-all border-0 outline-none";
                }
            } else {
                if (contentEl) contentEl.classList.add('hidden');
                if (btnEl) {
                    btnEl.className = "flex-1 py-1.5 px-1 rounded-md text-[10px] font-medium text-zinc-500 hover:text-zinc-900 whitespace-nowrap cursor-pointer transition-all bg-transparent border-0 outline-none";
                }
            }
        });
    }

    function createNewForm() {
        currentEditingForm = {
            id: 0,
            title: 'Untitled Form',
            status: 'draft',
            currentStepIndex: 0,
            settings: { steps: ['Step 1'] },
            blocks: [],
            logic: []
        };
        const titleInp = document.getElementById('editor-form-title');
        if (titleInp) titleInp.value = currentEditingForm.title;

        const canvasName = document.getElementById('canvas-form-name');
        if (canvasName) canvasName.innerText = 'Untitled Form';

        const canvasSub = document.getElementById('canvas-form-subtitle');
        if (canvasSub) canvasSub.innerText = 'Fill out details below to submit request.';
        
        if (!checkAndRestoreFormBuilderDraft(0)) {
            renderCoverImage();
            renderEditorBlocks();
            renderStepsBar();
        }
        switchEditorView('build');
        switchLeftTab('fields');
        if (typeof renderLogicRules === 'function') renderLogicRules();
        
        window._formIsDirty = true;
        setAutoSaveStatus('unsaved');

        if (listState) listState.classList.add('hidden');
        if (editorState) { editorState.classList.remove('hidden'); editorState.classList.add('flex'); }
    }

    function loadFormIntoEditor(id) {
        jQuery.ajax({
            url: getCoraRestUrl(`cora/v1/forms/${id}`),
            method: 'GET',
            cache: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(form) {
                currentEditingForm = form;
                currentEditingForm.currentStepIndex = 0;
                if (!currentEditingForm.settings) currentEditingForm.settings = {};
                if (!currentEditingForm.blocks) currentEditingForm.blocks = [];
                if (!currentEditingForm.settings.steps) currentEditingForm.settings.steps = ['Step 1'];

                // ── Remove loading overlay immediately so UI never gets stuck ──
                const loadingOverlay = document.getElementById('forms-loading-overlay');
                if (loadingOverlay) loadingOverlay.remove();

                // Transition: hide list, show editor
                if (listState) listState.classList.add('hidden');
                if (editorState) { editorState.classList.remove('hidden'); editorState.classList.add('flex'); }

                syncUIToForm(form);

                // Render editor – wrapped so a crash doesn't leave user stuck
                try {
                    if (!checkAndRestoreFormBuilderDraft(id)) {
                        renderCoverImage();
                        renderEditorBlocks();
                        renderStepsBar();
                        switchEditorView('build');
                        switchLeftTab('fields');
                        if (!currentEditingForm.logic) currentEditingForm.logic = [];
                        if (typeof renderLogicRules === 'function') renderLogicRules();
                    } else {
                        switchEditorView('build');
                        switchLeftTab('fields');
                    }
                    window._formIsDirty = false;
                    updatePublishButtonState(false);
                } catch(renderErr) {
                    // Rendering failed but editor is still visible – show a non-blocking warning
                    window.coraShowToast && window.coraShowToast('Form loaded but some UI elements may not render correctly.', 'error');
                }
            },
            error: function(xhr) {
                window.coraShowToast && window.coraShowToast("Form not found or failed to load.", "error");
                // Remove loading overlay and fall back to list view
                const overlay = document.getElementById('forms-loading-overlay');
                if (overlay) overlay.remove();
                if (listState) { listState.classList.remove('hidden'); }
                if (editorState) { editorState.classList.add('hidden'); editorState.classList.remove('flex'); }
                window.location.hash = '#list';
            }
        });
    }


    function syncUIToForm(form) {
                // Populate all inputs
                const titleInp = document.getElementById('editor-form-title');
                if (titleInp) titleInp.value = form.title || '';
                const canvasName = document.getElementById('canvas-form-name');
                if (canvasName) canvasName.innerText = form.title || 'Untitled Form';
                const canvasSub = document.getElementById('canvas-form-subtitle');
                if (canvasSub) canvasSub.innerText = form.description || form.subtitle || 'Fill out details below to submit request.';
                const statusSel = document.getElementById('editor-form-status');
                if (statusSel) statusSel.value = form.status || 'draft';
                const formTitleInp = document.getElementById('settings-form-title');
                if (formTitleInp) formTitleInp.value = form.title || '';
                const formSubInp = document.getElementById('settings-form-subtitle');
                if (formSubInp) formSubInp.value = form.description || form.subtitle || '';
                const formCoverInp = document.getElementById('settings-cover-url');
                if (formCoverInp) formCoverInp.value = (form.settings && form.settings.cover_image) || '';
                const successMsgInp = document.getElementById('settings-success-msg');
                if (successMsgInp) successMsgInp.value = (form.settings && form.settings.success_message) || '';
                const redirectUrlInp = document.getElementById('settings-redirect-url');
                if (redirectUrlInp) redirectUrlInp.value = (form.settings && form.settings.redirect_url) || '';

                const thankyouTitleInp = document.getElementById('settings-thankyou-title');
                if (thankyouTitleInp) thankyouTitleInp.value = (form.settings && form.settings.thankyou_title) || 'Response Submitted';
                const thankyouCtaEnableInp = document.getElementById('settings-thankyou-cta-enable');
                if (thankyouCtaEnableInp) thankyouCtaEnableInp.checked = !!(form.settings && form.settings.thankyou_cta_enable);
                const thankyouCtaTextInp = document.getElementById('settings-thankyou-cta-text');
                if (thankyouCtaTextInp) thankyouCtaTextInp.value = (form.settings && form.settings.thankyou_cta_text) || 'Visit Website';
                const thankyouCtaUrlInp = document.getElementById('settings-thankyou-cta-url');
                if (thankyouCtaUrlInp) thankyouCtaUrlInp.value = (form.settings && form.settings.thankyou_cta_url) || '';

                const thankyouCtaDetails = document.getElementById('settings-thankyou-cta-details');
                if (thankyouCtaDetails) {
                    if (form.settings && form.settings.thankyou_cta_enable) thankyouCtaDetails.classList.remove('hidden');
                    else thankyouCtaDetails.classList.add('hidden');
                }

                const upiIdInp = document.getElementById('settings-upi-id');
                if (upiIdInp) upiIdInp.value = (form.settings && form.settings.upi_id) || 'cora@upi';


                // Populate CTA inputs
                if (!form.settings) form.settings = {};
                if (form.settings.submit_button_text === undefined) form.settings.submit_button_text = "Submit";
                if (form.settings.submit_button_action === undefined) form.settings.submit_button_action = "message";
                if (form.settings.secondary_button_show === undefined) form.settings.secondary_button_show = true;
                if (form.settings.secondary_button_text === undefined) form.settings.secondary_button_text = "Save as draft";

                const subTxtInp = document.getElementById('settings-submit-text');
                if (subTxtInp) subTxtInp.value = form.settings.submit_button_text;
                const subActInp = document.getElementById('settings-submit-action');
                if (subActInp) subActInp.value = form.settings.submit_button_action;
                const secShowInp = document.getElementById('settings-sec-show');
                if (secShowInp) secShowInp.checked = !!form.settings.secondary_button_show;
                const secTxtInp = document.getElementById('settings-sec-text');
                if (secTxtInp) secTxtInp.value = form.settings.secondary_button_text;

                // Sync CTA buttons on canvas
                const canvSubBtn = document.getElementById('canvas-submit-btn');
                if (canvSubBtn) canvSubBtn.innerHTML = `${form.settings.submit_button_text} <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>`;
                const canvSecBtn = document.getElementById('canvas-sec-btn');
                if (canvSecBtn) {
                    canvSecBtn.innerText = form.settings.secondary_button_text;
                    if (form.settings.secondary_button_show) canvSecBtn.classList.remove('hidden');
                    else canvSecBtn.classList.add('hidden');
                }
                const secTextWrap = document.getElementById('settings-sec-text-wrapper');
                if (secTextWrap) {
                    if (form.settings.secondary_button_show) secTextWrap.classList.remove('hidden');
                    else secTextWrap.classList.add('hidden');
                }

                // Populate Email settings
                const emailAdminEnableInp = document.getElementById('settings-email-admin-enable');
                if (emailAdminEnableInp) emailAdminEnableInp.checked = !!form.settings.email_admin_enable;
                const emailAdminToInp = document.getElementById('settings-email-admin-to');
                if (emailAdminToInp) emailAdminToInp.value = form.settings.email_admin_to || '';
                const emailAdminSubjectInp = document.getElementById('settings-email-admin-subject');
                if (emailAdminSubjectInp) emailAdminSubjectInp.value = form.settings.email_admin_subject || '';

                const emailSubmitterEnableInp = document.getElementById('settings-email-submitter-enable');
                if (emailSubmitterEnableInp) emailSubmitterEnableInp.checked = !!form.settings.email_submitter_enable;
                const emailSubmitterSubjectInp = document.getElementById('settings-email-submitter-subject');
                if (emailSubmitterSubjectInp) emailSubmitterSubjectInp.value = form.settings.email_submitter_subject || '';
                const emailSubmitterMessageInp = document.getElementById('settings-email-submitter-message');
                if (emailSubmitterMessageInp) emailSubmitterMessageInp.value = form.settings.email_submitter_message || '';

                // Expand/collapse email details accordingly
                const emailAdminDetails = document.getElementById('settings-email-admin-details');
                if (emailAdminDetails) {
                    if (form.settings.email_admin_enable) emailAdminDetails.classList.remove('hidden');
                    else emailAdminDetails.classList.add('hidden');
                }
                const emailSubmitterDetails = document.getElementById('settings-email-submitter-details');
                if (emailSubmitterDetails) {
                    if (form.settings.email_submitter_enable) emailSubmitterDetails.classList.remove('hidden');
                    else emailSubmitterDetails.classList.add('hidden');
                }


    }

    function checkAndRestoreFormBuilderDraft(targetId) {
        if (typeof window.coraAutoSave !== 'undefined') {
            const draftStr = localStorage.getItem('cora_draft_form_builder_draft');
            if (draftStr) {
                try {
                    const draft = JSON.parse(draftStr);
                    const draftForm = JSON.parse(draft.data);
                    if (draftForm && draftForm.id == targetId) {
                        currentEditingForm = draftForm;
                        syncUIToForm(draftForm);
                        renderCoverImage();
                        renderEditorBlocks();
                        renderStepsBar();
                        if (typeof renderLogicRules === 'function') renderLogicRules();
                        if (window.coraShowToast) window.coraShowToast('Restored unsaved draft from local storage!', 'success');
                        return true;
                    }
                } catch(e) {}
            }
        }
        return false;
    }

    function setAutoSaveStatus(status) {
        const statusEl = document.getElementById('editor-save-status');
        if (!statusEl) return;
        
        statusEl.className = "px-2.5 py-0.5 rounded-full text-[10px] font-semibold flex items-center gap-1.5 shrink-0 transition-all duration-300";
        
        if (status === 'saving') {
            statusEl.classList.add('bg-zinc-100', 'text-zinc-500');
            statusEl.innerHTML = `<svg class="animate-spin h-3.5 w-3.5 text-zinc-400 mr-0.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...`;
        } else if (status === 'saved') {
            statusEl.classList.add('bg-emerald-50', 'text-emerald-700');
            statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-0.5 animate-pulse"></span> Autosaved`;
        } else if (status === 'unsaved') {
            statusEl.classList.add('bg-zinc-100', 'text-zinc-600');
            statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-zinc-400 mr-0.5"></span> Unsaved Draft`;
        } else if (status === 'error') {
            statusEl.classList.add('bg-red-50', 'text-red-700');
            statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-0.5"></span> Error`;
        }
    }

    window._formIsDirty = false;

    function updatePublishButtonState(isDirty = false) {
        const btn = document.getElementById('btn-save-form');
        if (!btn || !currentEditingForm) return;

        if (currentEditingForm.status === 'published' && !isDirty) {
            btn.innerHTML = `Published <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="inline ml-1"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
            btn.className = 'h-8 px-3.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold transition-all shadow-none cursor-default';
        } else if (currentEditingForm.status === 'published' && isDirty) {
            btn.innerText = 'Publish Changes';
            btn.className = 'h-8 px-4 rounded-lg bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all cursor-pointer shadow-xs border-0';
        } else {
            btn.innerText = 'Publish Form';
            btn.className = 'h-8 px-4 rounded-lg bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all cursor-pointer shadow-xs border-0';
        }
    }

    function triggerAutoSave() {
        clearTimeout(autoSaveTimer);
        window._formIsDirty = true;
        updatePublishButtonState(true);
        setAutoSaveStatus('saving');
        
        if (typeof window.coraAutoSave !== 'undefined') {
            window.coraAutoSave.saveLocalDraft('form_builder_draft', JSON.stringify(currentEditingForm));
        }

        autoSaveTimer = setTimeout(() => {
            saveFormInternal();
        }, 1500);
    }

    function showSaveErrorToast(err, res = null) {
        let reason = "";
        if (res) {
            reason = res.message || res.error || (res.code ? `Code: ${res.code}` : "");
        }
        if (!reason && err) {
            if (err.responseJSON && err.responseJSON.message) {
                reason = err.responseJSON.message;
            } else if (err.responseJSON && err.responseJSON.code) {
                reason = `Code: ${err.responseJSON.code}`;
            } else if (err.responseText) {
                try {
                    const parsed = JSON.parse(err.responseText);
                    reason = parsed.message || parsed.error || (parsed.code ? `Code: ${parsed.code}` : "");
                } catch(e) {
                    if (err.responseText.length < 100) {
                        reason = err.responseText.trim();
                    }
                }
            }
            if (!reason && err.statusText) {
                reason = `${err.statusText} (${err.status})`;
            }
        }
        
        const fullMsg = reason ? `Failed to save form: ${reason}` : "Failed to save form.";
        window.coraShowToast && window.coraShowToast(fullMsg, "error");
    }

    function saveFormInternal(publish = false, callback = null) {
        clearTimeout(autoSaveTimer);
        if (!currentEditingForm) return;

        const titleInp = document.getElementById('editor-form-title');
        if (titleInp && titleInp.value && titleInp.value.trim() !== '') {
            currentEditingForm.title = titleInp.value.trim();
            const st = document.getElementById('settings-form-title');
            if (st) st.value = titleInp.value.trim();
        }
        
        if (publish) {
            currentEditingForm.status = 'published';
        }

        jQuery.ajax({
            url: getCoraRestUrl('cora/v1/forms'),
            method: 'POST',
            dataType: 'json',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            data: JSON.stringify(currentEditingForm),
            contentType: 'application/json',
            success: function(res) {
                if (typeof res === 'string') {
                    try { res = JSON.parse(res); } catch(e) {}
                }
                const isValidSave = res && res.id && !res.code;
                if (isValidSave) {
                    currentEditingForm.id = res.id;
                    if (res.form_key) currentEditingForm.form_key = res.form_key;
                    if (!formsData) formsData = [];
                    const existingIdx = formsData.findIndex(f => f.id == res.id);
                    const merged = Object.assign({}, currentEditingForm, res);
                    if (existingIdx !== -1) {
                        formsData[existingIdx] = merged;
                    } else {
                        formsData.unshift(merged);
                    }
                    renderFormsList();
                    if (typeof window.coraAutoSave !== 'undefined') {
                        window.coraAutoSave.clearLocalDraft('form_builder_draft');
                    }
                    setAutoSaveStatus('saved');
                    if (publish) {
                        window._formIsDirty = false;
                        updatePublishButtonState(false);
                        window.coraShowToast && window.coraShowToast("Form published successfully!", "success");
                        fetchForms();
                    }
                } else {
                    setAutoSaveStatus('error');
                    if (publish || callback) {
                        showSaveErrorToast(null, res);
                    }
                }
                if (typeof callback === 'function') {
                    callback(isValidSave ? res : null);
                }
            },
            error: function(err) {
                setAutoSaveStatus('error');
                if (publish || callback) {
                    showSaveErrorToast(err, null);
                }
                if (typeof callback === 'function') {
                    callback(null);
                }
            }
        });
    }

    function addFieldToForm(type, insertAfterIdx = null) {
        if (!currentEditingForm) return;
        if (!currentEditingForm.blocks) currentEditingForm.blocks = [];
        
        // If user clicked "+ Add field" inside a column slot, route field to that column
        if (window._addToColumn) {
            const { blockIdx, colIdx } = window._addToColumn;
            window._addToColumn = null;
            const colBlock = currentEditingForm.blocks[blockIdx];
            if (colBlock && colBlock.type === 'columns') {
                if (!colBlock.column_fields) colBlock.column_fields = [[], []];
                if (!colBlock.column_fields[colIdx]) colBlock.column_fields[colIdx] = [];
                const subMeta = TYPE_META[type] || { label: type };
                colBlock.column_fields[colIdx].push({ id: 'sf_' + Math.random().toString(36).substr(2,5), type, label: subMeta.label, required: false });
                renderEditorBlocks();
                triggerAutoSave();
                return; // Don't add to main form
            }
        }

        if (insertAfterIdx === null && typeof window._insertAfterIdx !== 'undefined' && window._insertAfterIdx !== null) {
            insertAfterIdx = window._insertAfterIdx;
            window._insertAfterIdx = null;
        }

        const meta = TYPE_META[type] || { label: type };
        const newBlock = {
            id: 'field_' + Math.random().toString(36).substr(2, 6),
            type: type,
            label: meta.label,
            description: '',
            required: false,
            visibility: 'always',
            choices: ['dropdown', 'multiple_choice', 'checkbox'].includes(type) ? [{label:'Option 1'}, {label:'Option 2'}] : (type === 'services_checklist' ? [{label:'Deep Cleaning', price: 1500}, {label:'Express Cleaning', price: 800}] : undefined),
            price: ['payment', 'stripe_payment', 'upi_id', 'upi_qr'].includes(type) ? 100 : undefined,
            upi_id_value: ['upi_id', 'upi_qr'].includes(type) ? 'yourname@upi' : undefined,
            currency: 'INR',
            // Columns-specific defaults
            columns_count: type === 'columns' ? 2 : undefined,
            column_fields: type === 'columns' ? [[], []] : undefined,
        };

        if (insertAfterIdx !== null && insertAfterIdx >= 0) {
            currentEditingForm.blocks.splice(insertAfterIdx + 1, 0, newBlock);
            selectedBlockIndex = insertAfterIdx + 1;
        } else {
            const activeStepIndex = currentEditingForm.currentStepIndex || 0;
            let stepCounter = 0;
            let insertIdx = currentEditingForm.blocks.length;
            for (let i = 0; i < currentEditingForm.blocks.length; i++) {
                if (currentEditingForm.blocks[i].type === 'page_break') {
                    if (stepCounter === activeStepIndex) {
                        insertIdx = i;
                        break;
                    }
                    stepCounter++;
                }
            }
            currentEditingForm.blocks.splice(insertIdx, 0, newBlock);
            selectedBlockIndex = insertIdx;
        }

        renderEditorBlocks();
        selectBlock(selectedBlockIndex);
        triggerAutoSave();
    }

    function renderEditorBlocks() {
        if (!currentEditingForm) return; // guard: don't crash if no form loaded
        const container = document.getElementById('editor-blocks-container');
        if (!container) return;
        container.innerHTML = '';

        const activeStepIndex = currentEditingForm.currentStepIndex || 0;
        let stepCounter = 0;
        const activeBlocksInfo = [];

        (currentEditingForm.blocks || []).forEach((block, idx) => {
            if (block.type === 'page_break') {
                stepCounter++;
            } else {
                if (stepCounter === activeStepIndex) {
                    activeBlocksInfo.push({ block, originalIndex: idx });
                }
            }
        });

        const dropEnd = document.getElementById('editor-drop-zone');
        if (activeBlocksInfo.length === 0) {
            if (dropEnd) dropEnd.classList.add('hidden');
            const emptyDiv = document.createElement('div');
            emptyDiv.className = "flex flex-col items-center justify-center px-8 py-12 border-2 border-dashed border-zinc-200 rounded-2xl bg-zinc-50/30 mx-8 my-6 text-center gap-3";
            emptyDiv.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 ">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-zinc-800 ">Click a field in the sidebar to add it</p>
                    <p class="text-[10px] text-zinc-400 mt-0.5">or drag & drop a field here</p>
                </div>
                <button id="btn-empty-add-field" class="h-8 px-4 bg-zinc-950 text-white rounded-lg text-xs font-semibold hover:bg-zinc-800 cursor-pointer shadow-sm">+ Add Form Field</button>
            `;
            container.appendChild(emptyDiv);

            emptyDiv.querySelector('#btn-empty-add-field')?.addEventListener('click', () => {
                switchLeftTab('fields');
            });

            // Make it drop-aware!
            emptyDiv.addEventListener('dragover', (e) => e.preventDefault());
            emptyDiv.addEventListener('drop', (e) => {
                e.preventDefault();
                const data = e.dataTransfer.getData('text/plain');
                if (data.startsWith('new:')) {
                    const type = data.replace('new:', '');
                    addFieldToForm(type);
                }
            });
            return;
        } else {
            if (dropEnd) dropEnd.classList.remove('hidden');
        }

        activeBlocksInfo.forEach(({ block, originalIndex }) => {
            const meta = TYPE_META[block.type] || { label: block.type, badge: 'Unknown' };

            // ── COLUMNS BLOCK: special two/three-column grid rendering ──
            if (block.type === 'columns') {
                const colCount = block.columns_count || 2;
                if (!block.column_fields) block.column_fields = Array.from({length: colCount}, () => []);
                while (block.column_fields.length < colCount) block.column_fields.push([]);

                const colDiv = document.createElement('div');
                const isSelected = originalIndex === selectedBlockIndex;
                colDiv.className = `group relative px-8 py-4 cursor-pointer transition-all border-b border-zinc-100 border-l-[3px] ${isSelected ? 'border-l-zinc-950 bg-zinc-50/50' : 'border-l-transparent hover:bg-zinc-50/30 hover:border-l-zinc-300'}`;
                colDiv.dataset.index = originalIndex;

                // Top action bar
                const actionBar = document.createElement('div');
                actionBar.className = `flex items-center justify-between mb-3`;
                actionBar.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-zinc-400">Layout — ${colCount} Columns</span>
                        <div class="flex items-center gap-1">
                            <button class="btn-col-set-2 text-[9px] font-bold px-1.5 h-4 rounded ${colCount===2?'bg-zinc-950 text-white':'bg-zinc-100 text-zinc-500 hover:bg-zinc-200'} border-0 cursor-pointer transition-all">2</button>
                            <button class="btn-col-set-3 text-[9px] font-bold px-1.5 h-4 rounded ${colCount===3?'bg-zinc-950 text-white':'bg-zinc-100 text-zinc-500 hover:bg-zinc-200'} border-0 cursor-pointer transition-all">3</button>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="btn-delete-col-block text-[9px] px-1.5 h-5 rounded bg-transparent hover:bg-red-50 text-zinc-400 hover:text-red-500 border-0 cursor-pointer transition-all flex items-center gap-1">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
                            Remove
                        </button>
                    </div>
                `;
                colDiv.appendChild(actionBar);

                // Grid of columns
                const grid = document.createElement('div');
                grid.className = `grid gap-3`;
                grid.style.gridTemplateColumns = `repeat(${colCount}, 1fr)`;

                block.column_fields.slice(0, colCount).forEach((colFields, colIdx) => {
                    const colSlot = document.createElement('div');
                    colSlot.className = 'rounded-xl border border-dashed border-zinc-200 bg-white min-h-[80px] flex flex-col overflow-hidden';
                    colSlot.dataset.colIdx = colIdx;

                    // Render each sub-field in this column
                    (colFields || []).forEach((subField, subIdx) => {
                        const subMeta = TYPE_META[subField.type] || { label: subField.type };
                        let subPreview = '';
                        if (['text','email','phone','number','hidden','input'].includes(subField.type)) {
                            subPreview = `<input type="text" class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-zinc-50/50 text-[11px]" placeholder="${subField.label}..." disabled />`;
                        } else if (subField.type === 'long_text' || subField.type === 'textarea') {
                            subPreview = `<textarea class="w-full text-[11px] p-2.5 bg-zinc-50/50 border border-zinc-200 rounded-lg resize-none" rows="2" disabled placeholder="${subField.label}..."></textarea>`;
                        } else if (subField.type === 'dropdown') {
                            subPreview = `<select class="w-full h-8 pl-2.5 pr-6 rounded-lg border border-zinc-200 bg-zinc-50/50 text-[11px] appearance-none" disabled><option>Select...</option></select>`;
                        } else if (subField.type === 'date') {
                            subPreview = `<input type="date" class="w-full h-8 px-2.5 border border-zinc-200 rounded-lg bg-zinc-50/50 text-[11px]" disabled />`;
                        } else {
                            subPreview = `<input type="text" class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-zinc-50/50 text-[11px]" placeholder="${subField.label}..." disabled />`;
                        }
                        const subDiv = document.createElement('div');
                        subDiv.className = 'px-3 py-2 border-b border-zinc-100 group/sub flex flex-col gap-1 relative';
                        subDiv.innerHTML = `
                            <div class="flex items-center justify-between mb-0.5">
                                <label class="text-[11px] font-semibold text-zinc-800 outline-none cursor-text border-b border-transparent hover:border-zinc-200 focus:border-zinc-400" contenteditable="true">${subField.label || subMeta.label}</label>
                                <button class="btn-del-subfld text-zinc-300 hover:text-red-500 border-0 bg-transparent cursor-pointer p-0.5 opacity-0 group-hover/sub:opacity-100 transition-opacity" data-col="${colIdx}" data-sub="${subIdx}">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </div>
                            <div class="pointer-events-none">${subPreview}</div>
                        `;
                        subDiv.querySelector('label[contenteditable]').addEventListener('input', (e) => {
                            const val = e.target.innerText.trim();
                            subField.label = val;
                            const inputPreview = subDiv.querySelector('input, textarea');
                            if (inputPreview) inputPreview.placeholder = val + '...';
                            triggerAutoSave();
                        });
                        subDiv.querySelector('.btn-del-subfld').addEventListener('click', (e) => {
                            e.stopPropagation();
                            block.column_fields[colIdx].splice(subIdx, 1);
                            renderEditorBlocks();
                            triggerAutoSave();
                        });
                        colSlot.appendChild(subDiv);
                    });

                    // + Add field to column button
                    const addBtn = document.createElement('button');
                    addBtn.className = 'text-[10px] font-semibold text-zinc-400 hover:text-zinc-700 hover:bg-zinc-50 flex items-center gap-1 px-3 py-2 border-0 bg-transparent cursor-pointer transition-all w-full text-left mt-auto';
                    addBtn.innerHTML = `<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Add field`;
                    addBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        window._addToColumn = { blockIdx: originalIndex, colIdx: colIdx };
                        switchLeftTab('fields');
                        window.coraShowToast && window.coraShowToast(`Select a field to add to Column ${colIdx + 1}`, 'info');
                    });

                    // Drop zone for this column slot
                    colSlot.addEventListener('dragover', (e) => { e.preventDefault(); colSlot.classList.add('border-zinc-400', 'bg-zinc-50'); });
                    colSlot.addEventListener('dragleave', () => colSlot.classList.remove('border-zinc-400', 'bg-zinc-50'));
                    colSlot.addEventListener('drop', (e) => {
                        e.preventDefault();
                        colSlot.classList.remove('border-zinc-400', 'bg-zinc-50');
                        const data = e.dataTransfer.getData('text/plain');
                        if (data.startsWith('new:')) {
                            const type = data.replace('new:', '');
                            const subMeta2 = TYPE_META[type] || { label: type };
                            if (!block.column_fields[colIdx]) block.column_fields[colIdx] = [];
                            block.column_fields[colIdx].push({ id: 'sf_' + Math.random().toString(36).substr(2,5), type, label: subMeta2.label, required: false });
                            renderEditorBlocks();
                            triggerAutoSave();
                        }
                    });

                    colSlot.appendChild(addBtn);
                    grid.appendChild(colSlot);
                });

                colDiv.appendChild(grid);

                // Column count buttons
                colDiv.querySelector('.btn-col-set-2').addEventListener('click', (e) => { e.stopPropagation(); block.columns_count = 2; renderEditorBlocks(); triggerAutoSave(); });
                colDiv.querySelector('.btn-col-set-3').addEventListener('click', (e) => { e.stopPropagation(); block.columns_count = 3; renderEditorBlocks(); triggerAutoSave(); });
                colDiv.querySelector('.btn-delete-col-block').addEventListener('click', (e) => { e.stopPropagation(); currentEditingForm.blocks.splice(originalIndex, 1); if (selectedBlockIndex === originalIndex) selectedBlockIndex = null; renderEditorBlocks(); triggerAutoSave(); });
                colDiv.addEventListener('click', () => selectBlock(originalIndex));

                container.appendChild(colDiv);

                // Insert row after column block
                const insertRow2 = document.createElement('div');
                insertRow2.className = 'insert-between-row relative flex items-center gap-2 px-8 opacity-0 hover:opacity-100 focus-within:opacity-100 transition-all h-4 group cursor-pointer';
                insertRow2.dataset.insertAfter = originalIndex;
                insertRow2.innerHTML = `<div class="flex-1 h-px bg-zinc-200"></div><button class="btn-insert-between text-[9px] font-bold bg-white border border-zinc-200 text-zinc-400 hover:text-zinc-700 hover:border-zinc-400 px-2.5 h-4 rounded-full cursor-pointer transition-all flex items-center gap-1">+ field</button><div class="flex-1 h-px bg-zinc-200"></div>`;
                insertRow2.querySelector('.btn-insert-between').addEventListener('click', (e) => { e.stopPropagation(); switchLeftTab('fields'); window._insertAfterIdx = originalIndex; window.coraShowToast && window.coraShowToast('Select a field type to insert here', 'info'); });
                container.appendChild(insertRow2);
                return; // Skip default block rendering for columns
            }

            // ── STANDARD BLOCK RENDERING ──
            const div = document.createElement('div');
            
            let classStr;
            if (originalIndex === selectedBlockIndex) {
                // Selected: left accent strip, soft highlight
                classStr = "group relative px-8 py-4 cursor-pointer transition-all border-b border-zinc-100 border-l-[3px] border-l-zinc-950 bg-zinc-50/50";
            } else {
                // Unselected: flat row with bottom separator only
                classStr = "group relative px-8 py-4 cursor-pointer transition-all border-b border-zinc-100 border-l-[3px] border-l-transparent hover:bg-zinc-50/40 hover:border-l-zinc-300";
            }
            div.className = classStr;
            div.dataset.index = originalIndex;
            div.draggable = true;

            let previewHtml = '';
            if (['text','email','phone','number','hidden'].includes(block.type)) previewHtml = `<input type="text" class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50/50 text-xs" placeholder="Placeholder..." disabled />`;
            else if (block.type === 'long_text') previewHtml = `<textarea class="w-full text-xs p-3 bg-zinc-50/50 border border-zinc-200 rounded-lg" placeholder="Enter text..." disabled rows="2"></textarea>`;
            else if (['dropdown'].includes(block.type)) previewHtml = `<div class="relative w-full"><select class="w-full h-9 pl-3 pr-8 rounded-lg border border-zinc-200 bg-zinc-50/50 text-xs appearance-none" disabled><option>Select option...</option></select><div class="absolute inset-y-0 right-3 flex items-center text-zinc-400 pointer-events-none"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg></div></div>`;
            else if (['multiple_choice', 'checkbox'].includes(block.type)) {
                previewHtml = `<div class="flex flex-col gap-1.5 text-xs font-semibold text-zinc-700">` + (block.choices || [{label: 'Option 1'}, {label: 'Option 2'}]).slice(0, 3).map(c => `
                    <div class="flex items-center gap-2 py-2 px-3 border border-zinc-200 rounded-lg bg-zinc-50/30">
                        <input type="checkbox" class="h-3.5 w-3.5 rounded border-zinc-300 accent-zinc-950" disabled />
                        <span>${c.label || c}</span>
                    </div>
                `).join('') + `</div>`;
            } else if (block.type === 'date') previewHtml = `<input type="date" class="w-full h-9 px-3 border border-zinc-200 rounded-lg bg-zinc-50/50 text-xs" disabled />`;
            else if (block.type === 'file') previewHtml = `
                <div class="border border-dashed border-zinc-200 rounded-2xl py-6 px-4 bg-white text-center flex flex-col items-center justify-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-zinc-50 flex items-center justify-center text-zinc-700 border border-zinc-200/60 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><polyline points="9 15 12 12 15 15"></polyline></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-zinc-900 leading-normal">Drag &amp; drop an image or video</p>
                        <p class="text-[9.5px] text-zinc-400 mt-0.5">or click to browse (4 MB max)</p>
                    </div>
                </div>
            `;
            else if (block.type === 'signature') previewHtml = `
                <div class="border border-zinc-200 rounded-xl p-2.5 bg-zinc-50/20 flex flex-col gap-2">
                    <div class="w-full h-12 bg-white border border-zinc-200 rounded-lg flex items-center justify-center text-[10px] text-zinc-300">Draw Signature</div>
                    <div class="flex justify-between items-center px-0.5">
                        <button type="button" class="px-2 h-5 rounded text-[8px] font-bold text-zinc-400 bg-white border border-zinc-200" disabled>Clear</button>
                        <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-wider">Sign here</span>
                    </div>
                </div>
            `;
            else if (block.type === 'rating') previewHtml = `<div class="flex items-center gap-1 py-1.5 px-3 border border-zinc-200 rounded-lg bg-zinc-50/20 w-fit text-lg text-zinc-300">★★★★★</div>`;
            else if (block.type === 'slider') previewHtml = `
                <div class="flex items-center gap-2 bg-zinc-50/30 border border-zinc-200 p-2 rounded-lg">
                    <input type="range" class="flex-1 accent-zinc-950" disabled />
                    <span class="text-[10px] font-mono font-bold text-zinc-500">50</span>
                </div>
            `;
            else if (block.type === 'payment' || block.type === 'stripe_payment') previewHtml = `<div class="flex items-center gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg> <span class="text-sm font-semibold">${block.price || 100} ${block.currency || 'INR'}</span></div>`;
            else if (block.type === 'upi_id') previewHtml = `<div class="flex items-center gap-2 py-1"><span class="text-xs font-mono bg-zinc-50 border border-zinc-200 rounded px-2 py-1 text-zinc-700 ">${block.upi_id_value || 'yourname@upi'}</span><span class="text-[10px] text-zinc-400">UPI ID (₹${block.price || 100})</span></div>`;
            else if (block.type === 'upi_qr') previewHtml = `<div class="flex items-center gap-2 py-1"><div class="w-10 h-10 bg-zinc-50 border border-zinc-200 rounded flex items-center justify-center"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></div><span class="text-[10px] text-zinc-400">UPI QR Code (₹${block.price || 100})</span></div>`;
            else if (block.type === 'booking') previewHtml = `
                <div class="flex flex-col gap-2 p-3 bg-zinc-50/50 border border-zinc-200 rounded-xl">
                    <div class="flex items-center gap-2 text-xs font-semibold text-zinc-650 ">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>Choose booking slot (Date & Time)</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5 mt-1">
                        <div class="py-1 px-2 border border-zinc-200 bg-white text-[10px] font-bold text-center text-zinc-400 rounded-lg">10:00 AM</div>
                        <div class="py-1 px-2 border border-zinc-200 bg-white text-[10px] font-bold text-center text-zinc-400 rounded-lg">12:30 PM</div>
                        <div class="py-1 px-2 border border-zinc-200 bg-white text-[10px] font-bold text-center text-zinc-400 rounded-lg">03:00 PM</div>
                    </div>
                </div>
            `;
            else if (block.type === 'address') previewHtml = `
                <div class="flex flex-col gap-2 p-3 bg-zinc-50/50 border border-zinc-200 rounded-xl">
                    <input type="text" class="w-full text-xs p-2.5 bg-white border border-zinc-200 rounded-lg" placeholder="Street Address" disabled />
                    <div class="grid grid-cols-3 gap-2">
                        <input type="text" class="text-xs p-2.5 bg-white border border-zinc-200 rounded-lg" placeholder="City" disabled />
                        <input type="text" class="text-xs p-2.5 bg-white border border-zinc-200 rounded-lg" placeholder="State" disabled />
                        <input type="text" class="text-xs p-2.5 bg-white border border-zinc-200 rounded-lg" placeholder="ZIP Code" disabled />
                    </div>
                </div>
            `;
            else if (block.type === 'services_checklist') {
                const choices = block.choices || [{label: 'Deep Cleaning', price: 1500}, {label: 'Express Cleaning', price: 800}];
                previewHtml = `<div class="flex flex-col gap-2">` + 
                    choices.map(c => `
                        <div class="flex items-center justify-between p-2.5 bg-white border border-zinc-200 rounded-xl text-xs font-semibold">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" class="h-3.5 w-3.5 accent-zinc-950 rounded" disabled />
                                <span class="text-zinc-800 ">${c.label}</span>
                            </div>
                            <span class="text-zinc-500 font-mono text-[11px]">₹${c.price || 0}</span>
                        </div>
                    `).join('') + `</div>`;
            }
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
                <!-- Floating Edit/Action Bar (visible on hover or when selected) -->
                <div class="absolute -top-3.5 right-3 flex items-center gap-1 opacity-0 ${originalIndex === selectedBlockIndex ? 'opacity-100' : 'group-hover:opacity-100'} transition-opacity bg-white p-1 rounded-lg border border-zinc-200 shadow-sm z-10 select-none">
                    <div class="text-zinc-400 hover:text-zinc-700 cursor-grab drag-handle p-1 flex items-center justify-center" draggable="true" title="Drag to reorder">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="none" fill="currentColor"><circle cx="8" cy="6" r="1.5"></circle><circle cx="8" cy="12" r="1.5"></circle><circle cx="8" cy="18" r="1.5"></circle><circle cx="14" cy="6" r="1.5"></circle><circle cx="14" cy="12" r="1.5"></circle><circle cx="14" cy="18" r="1.5"></circle></svg>
                    </div>
                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-zinc-100 text-zinc-500 uppercase tracking-wider">${meta.label}</span>
                    <div class="w-px h-3 bg-zinc-200 mx-0.5"></div>
                    <button class="btn-duplicate-block p-1 rounded hover:bg-zinc-100 text-zinc-500 hover:text-zinc-800 border-0 bg-transparent cursor-pointer flex items-center justify-center" title="Duplicate">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    </button>
                    <button class="btn-delete-block p-1 rounded hover:bg-red-50 text-zinc-500 hover:text-red-600 border-0 bg-transparent cursor-pointer flex items-center justify-center" title="Delete">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
                    </button>
                </div>
                
                <!-- Native-Looking Field Label -->
                <div class="mb-1.5 flex items-center justify-between">
                    <label class="text-xs font-bold text-zinc-800 ">${block.label || meta.label}</label>
                    ${block.required ? '<span class="text-[10px] text-red-500 font-bold">*</span>' : ''}
                </div>

                <!-- Input Preview Area -->
                <div class="content-preview pointer-events-none">
                    ${previewHtml}
                </div>
            `;

            div.addEventListener('click', (e) => {
                if (e.target.closest('button')) return;
                selectBlock(originalIndex);
            });

            div.querySelector('.btn-delete-block')?.addEventListener('click', (e) => {
                e.stopPropagation();
                currentEditingForm.blocks.splice(originalIndex, 1);
                if (selectedBlockIndex === originalIndex) selectedBlockIndex = null;
                else if (selectedBlockIndex > originalIndex) selectedBlockIndex--;
                renderEditorBlocks();
                switchLeftTab('fields');
                triggerAutoSave();
            });

            div.querySelector('.btn-duplicate-block')?.addEventListener('click', (e) => {
                e.stopPropagation();
                const clone = JSON.parse(JSON.stringify(block));
                clone.id = 'field_' + Math.random().toString(36).substr(2, 6);
                currentEditingForm.blocks.splice(originalIndex + 1, 0, clone);
                selectBlock(originalIndex + 1);
                triggerAutoSave();
            });

            div.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', 'reorder:' + originalIndex);
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
                    const toIdx = originalIndex;
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
                    addFieldToForm(type, originalIndex);
                }
            });

            container.appendChild(div);

            // Slim insertion divider — invisible by default, shown on hover as a hairline
            const insertRow = document.createElement('div');
            insertRow.className = "insert-between-row relative flex items-center gap-2 px-8 opacity-0 hover:opacity-100 focus-within:opacity-100 transition-all h-5 group cursor-pointer";
            insertRow.dataset.insertAfter = originalIndex;
            insertRow.innerHTML = `
                <div class="flex-1 h-px bg-zinc-200 group-hover:bg-zinc-300 transition-colors"></div>
                <button class="btn-insert-between text-[9px] font-bold bg-white border border-zinc-200 text-zinc-400 hover:text-zinc-700 hover:border-zinc-400 px-2.5 h-5 rounded-full cursor-pointer transition-all shadow-2xs flex items-center gap-1 whitespace-nowrap">+ field</button>
                <div class="flex-1 h-px bg-zinc-200 group-hover:bg-zinc-300 transition-colors"></div>
            `;
            insertRow.querySelector('.btn-insert-between').addEventListener('click', (e) => {
                e.stopPropagation();
                switchLeftTab('fields');
                window._insertAfterIdx = originalIndex;
                window.coraShowToast && window.coraShowToast("Select a field type in the sidebar to insert here", "info");
            });
            container.appendChild(insertRow);
        });

        // Dropzone at end
        if (dropEnd) {
            dropEnd.addEventListener('dragover', (e) => e.preventDefault());
            dropEnd.addEventListener('drop', (e) => {
                const data = e.dataTransfer.getData('text/plain');
                if (data.startsWith('new:')) {
                    const type = data.replace('new:', '');
                    addFieldToForm(type);
                } else if (data.startsWith('reorder:')) {
                    const fromIdx = parseInt(data.replace('reorder:', ''));
                    const activeStepIndex = currentEditingForm.currentStepIndex || 0;
                    let stepCounter = 0;
                    let targetIdx = currentEditingForm.blocks.length;
                    for (let i = 0; i < currentEditingForm.blocks.length; i++) {
                        if (currentEditingForm.blocks[i].type === 'page_break') {
                            if (stepCounter === activeStepIndex) {
                                targetIdx = i;
                                break;
                            }
                            stepCounter++;
                        }
                    }
                    if (fromIdx !== targetIdx && fromIdx !== targetIdx - 1) {
                        const moved = currentEditingForm.blocks.splice(fromIdx, 1)[0];
                        const destIdx = fromIdx < targetIdx ? targetIdx - 1 : targetIdx;
                        currentEditingForm.blocks.splice(destIdx, 0, moved);
                        selectedBlockIndex = destIdx;
                        renderEditorBlocks();
                        triggerAutoSave();
                    }
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

    function renderLogicRules() {
        if (!currentEditingForm) return; // guard: don't crash if no form loaded
        const container = document.getElementById('settings-logic-rules-container');
        if (!container) return;
        container.innerHTML = '';

        if (!currentEditingForm.logic) {
            currentEditingForm.logic = [];
        }

        const logic = currentEditingForm.logic;
        const blocks = currentEditingForm.blocks || [];
        const inputBlocks = blocks.filter(b => !['header','paragraph','divider','spacer','page_break'].includes(b.type));

        const buildFieldOptions = (selectedLabel = '') => 
            inputBlocks.map(b => `<option value="${b.label}" ${b.label === selectedLabel ? 'selected' : ''}>${b.label}</option>`).join('');

        if (logic.length === 0) {
            container.innerHTML = '<p class="text-[10px] text-zinc-400 italic">No rules yet. Click + Add Rule to create one.</p>';
            return;
        }

        logic.forEach((rule, ruleIdx) => {
            const card = document.createElement('div');
            card.className = 'bg-zinc-50 border border-zinc-200 rounded-xl p-3 space-y-2';
            card.dataset.ruleIdx = ruleIdx;
            card.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Rule ${ruleIdx + 1}</span>
                    <button class="btn-delete-logic-rule text-zinc-400 hover:text-red-500 text-xs border-0 bg-transparent cursor-pointer" data-rule-idx="${ruleIdx}">✕</button>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-1 text-[10px] text-zinc-500 font-semibold">IF</div>
                    <select class="logic-field-select w-full h-8 px-2 rounded-lg border border-zinc-200 bg-white text-[11px] font-medium text-zinc-800 outline-none" data-rule-idx="${ruleIdx}" data-field="field">
                        <option value="">-- Select field --</option>
                        ${buildFieldOptions(rule.field)}
                    </select>
                    <select class="logic-condition-select w-full h-8 px-2 rounded-lg border border-zinc-200 bg-white text-[11px] font-medium text-zinc-800 outline-none" data-rule-idx="${ruleIdx}" data-field="condition">
                        <option value="equals" ${rule.condition === 'equals' ? 'selected' : ''}>equals</option>
                        <option value="not_equals" ${rule.condition === 'not_equals' ? 'selected' : ''}>does not equal</option>
                        <option value="contains" ${rule.condition === 'contains' ? 'selected' : ''}>contains</option>
                        <option value="not_empty" ${rule.condition === 'not_empty' ? 'selected' : ''}>is not empty</option>
                        <option value="is_empty" ${rule.condition === 'is_empty' ? 'selected' : ''}>is empty</option>
                    </select>
                    <input type="text" class="logic-value-input w-full h-8 px-2 rounded-lg border border-zinc-200 bg-white text-[11px] font-medium text-zinc-800 outline-none" placeholder="Value..." value="${rule.value || ''}" data-rule-idx="${ruleIdx}" data-field="value" />
                    <div class="flex items-center gap-1 text-[10px] text-zinc-500 font-semibold mt-1">THEN</div>
                    <select class="logic-action-select w-full h-8 px-2 rounded-lg border border-zinc-200 bg-white text-[11px] font-medium text-zinc-800 outline-none" data-rule-idx="${ruleIdx}" data-field="action">
                        <option value="show" ${rule.action === 'show' ? 'selected' : ''}>Show field</option>
                        <option value="hide" ${rule.action === 'hide' ? 'selected' : ''}>Hide field</option>
                        <option value="require" ${rule.action === 'require' ? 'selected' : ''}>Make required</option>
                    </select>
                    <select class="logic-target-select w-full h-8 px-2 rounded-lg border border-zinc-200 bg-white text-[11px] font-medium text-zinc-800 outline-none" data-rule-idx="${ruleIdx}" data-field="target">
                        <option value="">-- Target field --</option>
                        ${buildFieldOptions(rule.target)}
                    </select>
                </div>
            `;
            container.appendChild(card);

            card.querySelectorAll('select, input').forEach(el => {
                el.addEventListener('change', updateLogicRule);
                el.addEventListener('input', updateLogicRule);
            });

            card.querySelector('.btn-delete-logic-rule').addEventListener('click', (e) => {
                const idx = parseInt(e.target.dataset.ruleIdx);
                currentEditingForm.logic.splice(idx, 1);
                renderLogicRules();
                triggerAutoSave();
            });
        });
    }

    function updateLogicRule(e) {
        const ruleIdx = parseInt(e.target.dataset.ruleIdx);
        const fieldKey = e.target.dataset.field;
        if (!currentEditingForm.logic || !currentEditingForm.logic[ruleIdx]) return;
        currentEditingForm.logic[ruleIdx][fieldKey] = e.target.value;
        triggerAutoSave();
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
            if (['payment', 'stripe_payment', 'upi_id', 'upi_qr'].includes(block.type)) {
                priceContainer.classList.remove('hidden');
                if (priceAmount) priceAmount.value = block.price || 100;
                if (priceCurrency) priceCurrency.value = block.currency || 'INR';

                const upiContainer = document.getElementById('inspector-upi-container');
                const upiInput = document.getElementById('inspector-upi-id-value');
                if (upiContainer) {
                    if (['upi_id', 'upi_qr'].includes(block.type)) {
                        upiContainer.classList.remove('hidden');
                        if (upiInput) upiInput.value = block.upi_id_value || 'yourname@upi';
                    } else {
                        upiContainer.classList.add('hidden');
                    }
                }
            } else {
                priceContainer.classList.add('hidden');
            }
        }

        if (choicesWrapper) {
            if (['dropdown', 'multiple_choice', 'checkbox', 'services_checklist'].includes(block.type)) {
                choicesWrapper.classList.remove('hidden');
                let choicesHtml = '';
                (block.choices || []).forEach((c, cIdx) => {
                    const priceInput = block.type === 'services_checklist' 
                        ? `<input type="number" class="inspector-choice-price w-20 text-xs px-2 py-1.5 border border-zinc-200 rounded font-semibold text-right" placeholder="Price" value="${c.price || 0}" data-cidx="${cIdx}">`
                        : '';
                    choicesHtml += `
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" class="inspector-choice-input flex-1 text-xs px-2 py-1.5 border border-zinc-200 rounded" value="${c.label}" data-cidx="${cIdx}">
                            ${priceInput}
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
                choicesWrapper.querySelectorAll('.inspector-choice-price').forEach(inp => {
                    inp.addEventListener('input', (e) => {
                        const ci = parseInt(e.target.dataset.cidx);
                        currentEditingForm.blocks[selectedBlockIndex].choices[ci].price = parseFloat(e.target.value) || 0;
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
                    const newOpt = block.type === 'services_checklist' ? {label: 'New Service', price: 100} : {label: 'New Option'};
                    currentEditingForm.blocks[selectedBlockIndex].choices.push(newOpt);
                    triggerAutoSave();
                    renderEditorBlocks();
                    populateInspectorSettings(selectedBlockIndex);
                });

            } else {
                choicesWrapper.classList.add('hidden');
            }
        }

        // Render logic rules for this field settings context
        if (typeof renderLogicRules === 'function') {
            renderLogicRules();
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

    document.getElementById('btn-add-logic-rule')?.addEventListener('click', () => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.logic) currentEditingForm.logic = [];
        currentEditingForm.logic.push({ field: '', condition: 'equals', value: '', action: 'show', target: '' });
        renderLogicRules();
        triggerAutoSave();
    });

    document.getElementById('inspector-upi-id-value')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null) {
            currentEditingForm.blocks[selectedBlockIndex].upi_id_value = e.target.value;
            triggerAutoSave();
            renderEditorBlocks();
        }
    });

    // Form Settings & Integrations Sync Binders
    const formSettingsBindings = [
        { id: 'settings-form-title', key: 'title', parent: 'root', syncTo: ['editor-form-title', 'canvas-form-name'] },
        { id: 'settings-form-subtitle', key: 'description', parent: 'root', syncTo: ['canvas-form-subtitle'] },
        { id: 'settings-cover-url', key: 'cover_image', parent: 'settings', syncAction: renderCoverImage },
        { id: 'settings-thankyou-title', key: 'thankyou_title', parent: 'settings' },
        { id: 'settings-success-msg', key: 'success_message', parent: 'settings' },
        { id: 'settings-redirect-url', key: 'redirect_url', parent: 'settings' },
        { id: 'settings-thankyou-cta-text', key: 'thankyou_cta_text', parent: 'settings' },
        { id: 'settings-thankyou-cta-url', key: 'thankyou_cta_url', parent: 'settings' },
        { id: 'settings-custom-css', key: 'custom_css', parent: 'styling' },
        { id: 'settings-webhook-url', key: 'webhook_url', parent: 'settings' },
        { id: 'settings-upi-id', key: 'upi_id', parent: 'settings' }
    ];

    formSettingsBindings.forEach(binding => {
        document.getElementById(binding.id)?.addEventListener('input', (e) => {
            if (!currentEditingForm) return;
            const val = e.target.value;
            
            if (binding.parent === 'root') {
                currentEditingForm[binding.key] = val;
            } else {
                if (!currentEditingForm[binding.parent]) currentEditingForm[binding.parent] = {};
                currentEditingForm[binding.parent][binding.key] = val;
            }

            if (binding.syncTo) {
                binding.syncTo.forEach(targetId => {
                    const el = document.getElementById(targetId);
                    if (el) {
                        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') el.value = val;
                        else el.innerText = val;
                    }
                });
            }

            if (binding.syncAction) {
                binding.syncAction();
            }

        });
    });

    document.getElementById('settings-thankyou-cta-enable')?.addEventListener('change', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        currentEditingForm.settings.thankyou_cta_enable = e.target.checked;
        const details = document.getElementById('settings-thankyou-cta-details');
        if (details) {
            if (e.target.checked) details.classList.remove('hidden');
            else details.classList.add('hidden');
        }
        triggerAutoSave();
    });

    // Title & Subtitle Sync
    document.getElementById('editor-form-title')?.addEventListener('input', (e) => {
        const val = e.target.value;
        if (currentEditingForm) currentEditingForm.title = val;
        const cn = document.getElementById('canvas-form-name');
        if (cn) cn.innerText = val;
        triggerAutoSave();
    });

    document.getElementById('editor-form-status')?.addEventListener('change', (e) => {
        if (currentEditingForm) {
            currentEditingForm.status = e.target.value;
            triggerAutoSave();
        }
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

    // Footer actions (CTA) bindings
    document.getElementById('settings-submit-text')?.addEventListener('input', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        const val = e.target.value || 'Submit';
        currentEditingForm.settings.submit_button_text = val;
        
        const btn = document.getElementById('canvas-submit-btn');
        if (btn) {
            btn.innerHTML = `${val} <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>`;
        }
        triggerAutoSave();
    });

    document.getElementById('settings-submit-action')?.addEventListener('change', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        currentEditingForm.settings.submit_button_action = e.target.value;
        triggerAutoSave();
    });

    document.getElementById('settings-sec-text')?.addEventListener('input', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        const val = e.target.value || 'Save as draft';
        currentEditingForm.settings.secondary_button_text = val;
        
        const btn = document.getElementById('canvas-sec-btn');
        if (btn) btn.innerText = val;
        triggerAutoSave();
    });

    document.getElementById('settings-sec-show')?.addEventListener('change', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        const show = e.target.checked;
        currentEditingForm.settings.secondary_button_show = show;
        
        const btn = document.getElementById('canvas-sec-btn');
        if (btn) {
            if (show) btn.classList.remove('hidden');
            else btn.classList.add('hidden');
        }
        const textWrapper = document.getElementById('settings-sec-text-wrapper');
        if (textWrapper) {
            if (show) textWrapper.classList.remove('hidden');
            else textWrapper.classList.add('hidden');
        }
        triggerAutoSave();
    });

    // Email Notifications settings listeners
    document.getElementById('settings-email-admin-enable')?.addEventListener('change', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        const enable = e.target.checked;
        currentEditingForm.settings.email_admin_enable = enable;
        
        const details = document.getElementById('settings-email-admin-details');
        if (details) {
            if (enable) details.classList.remove('hidden');
            else details.classList.add('hidden');
        }
        triggerAutoSave();
    });

    document.getElementById('settings-email-admin-to')?.addEventListener('input', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        currentEditingForm.settings.email_admin_to = e.target.value;
        triggerAutoSave();
    });

    document.getElementById('settings-email-admin-subject')?.addEventListener('input', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        currentEditingForm.settings.email_admin_subject = e.target.value;
        triggerAutoSave();
    });

    document.getElementById('settings-email-submitter-enable')?.addEventListener('change', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        const enable = e.target.checked;
        currentEditingForm.settings.email_submitter_enable = enable;
        
        const details = document.getElementById('settings-email-submitter-details');
        if (details) {
            if (enable) details.classList.remove('hidden');
            else details.classList.add('hidden');
        }
        triggerAutoSave();
    });

    document.getElementById('settings-email-submitter-subject')?.addEventListener('input', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        currentEditingForm.settings.email_submitter_subject = e.target.value;
        triggerAutoSave();
    });

    document.getElementById('settings-email-submitter-message')?.addEventListener('input', (e) => {
        if (!currentEditingForm) return;
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        currentEditingForm.settings.email_submitter_message = e.target.value;
        triggerAutoSave();
    });

    document.getElementById('canvas-cta-preview-row')?.addEventListener('click', () => {
        switchLeftTab('form');
        setTimeout(() => {
            document.getElementById('settings-submit-text')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            document.getElementById('settings-submit-text')?.focus();
        }, 100);
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

    document.getElementById('btn-save-draft')?.addEventListener('click', () => {
        saveFormInternal(false, (res) => {
            if (res) {
                window.coraShowToast && window.coraShowToast("Draft saved successfully!", "success");
            }
        });
    });

    document.getElementById('btn-save-form')?.addEventListener('click', () => {
        saveFormInternal(true);
    });

    document.getElementById('btn-view-form')?.addEventListener('click', () => {
        if (!currentEditingForm) return;

        let siteUrl = (typeof coraREData !== 'undefined' && coraREData.siteUrl) ? coraREData.siteUrl : '';
        if (siteUrl.endsWith('/')) siteUrl = siteUrl.slice(0, -1);

        // If form is already saved (has ID or form_key) and not dirty, view immediately in new tab
        if (currentEditingForm.id && (currentEditingForm.form_key || currentEditingForm.id) && !window._formIsDirty) {
            const formKey = currentEditingForm.form_key || currentEditingForm.id;
            window.open(siteUrl + '/shared-form/' + formKey, '_blank');
            return;
        }

        // Pre-open blank tab synchronously in direct response to user gesture to prevent popup blocking
        const win = window.open('about:blank', '_blank');

        window.coraShowToast && window.coraShowToast("Publishing form to generate preview...", "info");
        saveFormInternal(true, (res) => {
            if (typeof res === 'string') {
                try { res = JSON.parse(res); } catch(e) {}
            }
            const formKey = (res && (res.form_key || res.id)) || currentEditingForm.form_key || currentEditingForm.id;
            if (formKey) {
                const targetUrl = siteUrl + '/shared-form/' + formKey;
                if (win) {
                    win.location.href = targetUrl;
                } else {
                    window.open(targetUrl, '_blank');
                }
            } else {
                if (win) win.close();
                window.coraShowToast && window.coraShowToast("Could not generate form preview", "error");
            }
        });
    });

    function openShareModal() {
        if (!currentEditingForm) return;

        const populateAndShowModal = (f) => {
            let siteUrl = (typeof coraREData !== 'undefined' && coraREData.siteUrl) ? coraREData.siteUrl : '';
            if (siteUrl.endsWith('/')) siteUrl = siteUrl.slice(0, -1);
            const formKey = f.form_key || f.id;
            const shareUrl = siteUrl + '/shared-form/' + formKey;
            const embedCode = `<iframe src="${shareUrl}" width="100%" height="600" frameborder="0"></iframe>`;

            const titleEl = document.getElementById('share-modal-title');
            if (titleEl) titleEl.textContent = `Share: ${f.title || 'Untitled Form'}`;

            const urlInp = document.getElementById('share-modal-url-input');
            if (urlInp) urlInp.value = shareUrl;

            const embedInp = document.getElementById('share-modal-embed-input');
            if (embedInp) embedInp.value = embedCode;

            const modal = document.getElementById('cora-share-modal');
            if (modal) {
                modal.classList.remove('hidden', 'pointer-events-none');
                modal.classList.add('flex', 'pointer-events-auto');
            }
        };

        if (currentEditingForm.id && (currentEditingForm.form_key || currentEditingForm.id)) {
            populateAndShowModal(currentEditingForm);
        } else {
            window.coraShowToast && window.coraShowToast("Publishing form to generate share link...", "info");
            saveFormInternal(true, (res) => {
                if (typeof res === 'string') {
                    try { res = JSON.parse(res); } catch(e) {}
                }
                const formObj = (res && (res.form_key || res.id)) ? res : currentEditingForm;
                if (formObj && (formObj.form_key || formObj.id)) {
                    populateAndShowModal(formObj);
                }
            });
        }
    }

    function closeShareModal() {
        const modal = document.getElementById('cora-share-modal');
        if (modal) {
            modal.classList.remove('pointer-events-auto', 'flex');
            modal.classList.add('hidden', 'pointer-events-none');
        }
    }

    document.getElementById('btn-share-editor')?.addEventListener('click', openShareModal);
    document.getElementById('btn-close-share-modal')?.addEventListener('click', closeShareModal);

    document.getElementById('btn-share-copy-link')?.addEventListener('click', () => {
        const urlInp = document.getElementById('share-modal-url-input');
        if (urlInp && urlInp.value) {
            coraCopyTextToClipboard(urlInp.value);
        }
    });

    document.getElementById('btn-share-whatsapp')?.addEventListener('click', () => {
        const urlInp = document.getElementById('share-modal-url-input');
        if (urlInp && urlInp.value) {
            const title = currentEditingForm ? (currentEditingForm.title || 'Form') : 'Form';
            const waUrl = `https://wa.me/?text=${encodeURIComponent('Please fill out this form: ' + title + ' - ' + urlInp.value)}`;
            window.open(waUrl, '_blank');
        }
    });

    document.getElementById('btn-share-email')?.addEventListener('click', () => {
        const urlInp = document.getElementById('share-modal-url-input');
        if (urlInp && urlInp.value) {
            const title = currentEditingForm ? (currentEditingForm.title || 'Form Invite') : 'Form Invite';
            const mailUrl = `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent('Hi,\n\nPlease fill out this form at the following link:\n' + urlInp.value + '\n\nThank you!')}`;
            window.open(mailUrl, '_blank');
        }
    });

    document.getElementById('btn-copy-embed-code')?.addEventListener('click', () => {
        const embedInp = document.getElementById('share-modal-embed-input');
        if (embedInp && embedInp.value) {
            coraCopyTextToClipboard(embedInp.value);
        }
    });

    document.getElementById('btn-back-to-list')?.addEventListener('click', () => {
        if (window._formIsDirty || (currentEditingForm && !currentEditingForm.id)) {
            saveFormInternal(false, () => {
                fetchForms();
                window.location.hash = '#list';
            });
        } else {
            fetchForms();
            window.location.hash = '#list';
        }
    });

    // Use delegated event listeners on the stable module root so they survive listState.innerHTML replacement
    const formsModuleRoot = document.getElementById('cora-forms-module');
    if (formsModuleRoot) {
        formsModuleRoot.addEventListener('click', (e) => {
            const target = e.target.closest('#btn-create-form');
            if (target) {
                createNewForm();
                window.location.hash = '#new';
            }
        });
    }


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
        window.coraShowToast && window.coraShowToast('Select a field type from the sidebar to add', 'info');
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
            url: getCoraRestUrl(`cora/v1/forms/${currentEditingForm.id}/submissions`),
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
