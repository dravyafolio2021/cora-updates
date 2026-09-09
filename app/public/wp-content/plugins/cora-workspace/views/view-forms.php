<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<style>#cora-forms-module { position: relative; } @keyframes spin { to { transform: rotate(360deg); } }</style>

<div id="cora-forms-module" class="w-full flex-1 min-h-0 flex flex-col overflow-hidden" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <!-- STATE 1: FORMS LIST VIEW -->
    <div id="forms-list-state" class="flex-1 flex flex-col overflow-y-auto p-4 sm:p-6 md:p-8 pb-48 md:pb-64 gap-4">
<?php
$forms_header_args = array(
    'title'            => 'Cora Forms',
    'description'      => 'Design and share Notion-style interactive forms. Automatically collect leads into your CRM database.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="9"></line><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="15" y2="17"></line></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'id'          => 'btn-create-form',
        'text'        => 'Create form',
        'mobile_text' => 'AI Create',
        'onclick'     => "if(window.innerWidth < 640){ window.coraPromptFormAI('', 'Create a new Notion-style lead capture form'); } else { if(typeof createNewForm==='function'){ createNewForm(); } }",
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'visible'     => true,
        'class'       => '',
    ),
    'tabs'             => array(
        array(
            'id'           => 'list',
            'dom_id'       => 'tab-forms-list',
            'label'        => 'Forms List',
            'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
            'active'       => true,
            'onclick'      => "window.location.hash='#list'",
        ),
        array(
            'id'           => 'funnel',
            'dom_id'       => 'tab-funnel-analytics',
            'label'        => 'Funnel Analytics',
            'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
            'active'       => false,
            'onclick'      => "window.location.hash='#funnel'",
        ),
        array(
            'id'           => 'audit-log',
            'dom_id'       => 'tab-audit-logs',
            'label'        => 'Compliance Audit Log',
            'mobile_label' => 'Audit Log',
            'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
            'active'       => false,
            'onclick'      => "window.location.hash='#audit-log'",
        ),
        array(
            'id'           => 'settings',
            'dom_id'       => 'tab-forms-settings',
            'label'        => 'Settings & Flows',
            'mobile_label' => 'Settings',
            'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
            'active'       => false,
            'onclick'      => "window.location.hash='#settings'",
        ),
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $forms_header_args );
}
?>

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
    </div>

    <!-- TAB CONTENT: EFFORTLESS CONVERSION DOCTOR & FUNNEL INTELLIGENCE -->
    <div id="forms-funnel-tab-content" class="hidden flex-col gap-4">
        <!-- Header Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200/60 pb-3">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">Conversion Health & Recommendations</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Understand how visitors turn into leads and see simple, 1-click improvements.</p>
            </div>
            <!-- Form Selector Dropdown & Quick Edit Action -->
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-zinc-500 hidden sm:inline">Form:</span>
                <select id="funnel-form-selector" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-800 outline-none focus:border-zinc-400 w-52 sm:w-60 cursor-pointer shadow-2xs">
                    <option value="all">All Forms (Overall)</option>
                </select>
                <button id="btn-funnel-edit-form" type="button" class="h-9 px-3 rounded-lg bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs border-0">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    <span>Edit Form</span>
                </button>
            </div>
        </div>

        <!-- 1. ONE-GLANCE HERO DECISION CARD -->
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4 flex-1">
                <div id="hero-decision-icon" class="w-11 h-11 rounded-xl bg-zinc-950 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
                </div>
                <div class="flex flex-col gap-1.5 min-w-0">
                    <div class="flex items-center gap-2">
                        <span id="hero-decision-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border bg-zinc-100 text-zinc-700 border-zinc-200 inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span> Assessing Form Health...
                        </span>
                    </div>
                    <h4 id="hero-decision-title" class="text-base sm:text-lg font-bold text-zinc-950 tracking-tight leading-snug">
                        Loading conversion summary...
                    </h4>
                    <p id="hero-decision-desc" class="text-xs text-zinc-500 max-w-2xl leading-relaxed">
                        Gathering visitor and submission insights...
                    </p>
                </div>
            </div>
            
            <div id="hero-decision-actions" class="shrink-0 flex items-center gap-2">
                <!-- Action button populated dynamically -->
            </div>
        </div>

        <!-- 2. VISUAL 3-STEP CUSTOMER JOURNEY (Clean, spacious cards, zero overlaps) -->
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wide">Customer Progression Journey</h4>
                    <p class="text-[10px] text-zinc-450 mt-0.5">Where visitors move smoothly vs where they drop off.</p>
                </div>
                <span id="journey-total-summary" class="text-xs font-semibold text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-lg">
                    0% Conversion
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                <!-- Step 1: Views -->
                <div class="bg-zinc-50/70 border border-zinc-200/80 rounded-xl p-4.5 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Step 1 • Landed</span>
                        <span class="text-xs font-semibold text-zinc-400">100%</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span id="funnel-metric-views" class="text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">0</span>
                        <span class="text-xs text-zinc-500 font-medium">visitors</span>
                    </div>
                    <div class="h-1.5 w-full bg-zinc-200 rounded-full overflow-hidden mt-1">
                        <div class="h-full bg-zinc-900 rounded-full w-full"></div>
                    </div>
                    <p class="text-[11px] text-zinc-400 mt-1">Total people who opened the form link.</p>
                </div>

                <!-- Step 2: Started -->
                <div class="bg-zinc-50/70 border border-zinc-200/80 rounded-xl p-4.5 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Step 2 • Started</span>
                        <span id="funnel-metric-started-pct" class="text-xs font-bold text-zinc-800">0%</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span id="funnel-metric-started" class="text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">0</span>
                        <span class="text-xs text-zinc-500 font-medium">started typing</span>
                    </div>
                    <div class="h-1.5 w-full bg-zinc-200 rounded-full overflow-hidden mt-1">
                        <div id="funnel-started-bar" class="h-full bg-zinc-700 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <p id="funnel-metric-started-sub" class="text-[11px] text-zinc-500 mt-1">0 left before typing.</p>
                </div>

                <!-- Step 3: Completed -->
                <div class="bg-zinc-50/70 border border-zinc-200/80 rounded-xl p-4.5 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Step 3 • Completed</span>
                        <span id="funnel-metric-completed-pct" class="text-xs font-bold text-emerald-700">0%</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span id="funnel-metric-completed" class="text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">0</span>
                        <span class="text-xs text-zinc-500 font-medium">leads collected</span>
                    </div>
                    <div class="h-1.5 w-full bg-zinc-200 rounded-full overflow-hidden mt-1">
                        <div id="funnel-completed-bar" class="h-full bg-emerald-600 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <p id="funnel-metric-completed-sub" class="text-[11px] text-zinc-500 mt-1">Finalized submissions saved to CRM.</p>
                </div>
            </div>
        </div>

        <!-- 3. TWO COLUMNS: "What to Do Next" & "Question Health" -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Recommended Next Actions (Plain English) -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wide">Actionable Improvements</h4>
                        <p class="text-[10px] text-zinc-450 mt-0.5">Simple tweaks to get higher response rates.</p>
                    </div>
                    <span class="text-[10px] font-semibold text-zinc-400 uppercase">AI Doctor</span>
                </div>
                
                <div id="funnel-ai-actions-list" class="space-y-3">
                    <!-- Action Cards -->
                </div>
            </div>

            <!-- Right: Question Friction Health -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 sm:p-6 shadow-2xs flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wide">Question-by-Question Health</h4>
                        <p class="text-[10px] text-zinc-450 mt-0.5">Check if any question is causing hesitation.</p>
                    </div>
                    <span id="field-health-status-summary" class="text-[10px] font-semibold text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded-full">
                        Checking...
                    </span>
                </div>
                
                <div id="funnel-friction-list" class="space-y-2">
                    <!-- Dynamic question health items -->
                </div>
            </div>
        </div>

        <!-- Bottom scroll buffer so content is easily reachable above navigation -->
        <div class="h-24 sm:h-32 shrink-0"></div>
    </div>

    <!-- Hidden Clause Library Content (Temporarily disabled for MVP focus) -->
    <div id="forms-clauses-tab-content" class="hidden"></div>

        <!-- TAB CONTENT: COMPLIANCE AUDIT LOG -->
        <div id="forms-audit-tab-content" class="hidden flex-col gap-4">
            <div class="flex items-center justify-between border-b border-zinc-200/60 pb-3">
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

            <!-- Bottom scroll buffer so content is easily reachable above navigation -->
            <div class="h-24 sm:h-36 shrink-0 w-full" aria-hidden="true"></div>
        </div>

        <!-- TAB CONTENT: GLOBAL & PER-FORM SETTINGS, NOTIFICATIONS & FLOWS -->
        <div id="forms-settings-tab-content" class="hidden flex-col gap-6 pb-20 md:pb-32">
            <!-- SCOPE & ACTION BAR -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900 shrink-0">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-bold text-zinc-950">Form Automation & Notifications</h3>
                            <span id="cora-settings-scope-badge" class="px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-800 text-[10px] font-bold border border-zinc-200">Global Defaults</span>
                        </div>
                        <p class="text-xs text-zinc-500 mt-0.5">Control email autoresponders, admin alerts, templates, and delivery pipelines.</p>
                    </div>
                </div>

                <!-- Scope Dropdown & Action Buttons -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <div class="relative">
                        <select id="cora-forms-settings-scope" class="h-9 pl-3 pr-8 rounded-xl border border-zinc-200 bg-white text-zinc-800 text-xs font-semibold focus:border-zinc-400 focus:outline-none cursor-pointer shadow-2xs appearance-none">
                            <option value="global">Global Defaults (All Forms)</option>
                            <optgroup id="cora-scope-forms-optgroup" label="Custom Form Overrides">
                                <!-- Populated dynamically -->
                            </optgroup>
                        </select>
                        <svg class="w-3.5 h-3.5 text-zinc-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>

                    <button id="btn-open-test-notification" type="button" class="h-9 px-3.5 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        <span>Send Test</span>
                    </button>

                    <button id="btn-save-forms-settings" type="button" class="h-9 px-4 rounded-xl bg-zinc-900 hover:bg-black text-white text-xs font-semibold transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                        <svg id="save-settings-spinner" class="hidden animate-spin h-3.5 w-3.5 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span id="save-settings-text">Save Settings</span>
                    </button>
                </div>
            </div>

            <!-- INTERACTIVE AUTOMATION FLOW STEPPER -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Active Submission Pipeline Flow</h4>
                    </div>
                    <span class="text-[11px] text-zinc-400 font-mono">Real-time Multi-Channel Dispatch</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 relative">
                    <!-- Step 1: Trigger -->
                    <div class="p-3.5 rounded-xl bg-zinc-50 border border-zinc-200/80 flex flex-col justify-between gap-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Trigger 01</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-zinc-900">Form Submitted</div>
                            <div class="text-[10px] text-zinc-500 mt-0.5">Instant validation & Honeypot guard</div>
                        </div>
                        <div class="text-[9.5px] font-mono text-zinc-400">Delay: 0ms</div>
                    </div>

                    <!-- Step 2: Admin Alert -->
                    <div id="flow-node-admin" class="p-3.5 rounded-xl bg-white border border-zinc-200 flex flex-col justify-between gap-2.5 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Action 02</span>
                            <span id="flow-node-admin-badge" class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold">Active</span>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-zinc-900">Notify Admin</div>
                            <div class="text-[10px] text-zinc-500 mt-0.5">Email + In-App Push + WhatsApp</div>
                        </div>
                        <div class="text-[9.5px] font-mono text-zinc-400">Priority: High</div>
                    </div>

                    <!-- Step 3: Respondent Autoresponder -->
                    <div id="flow-node-submitter" class="p-3.5 rounded-xl bg-white border border-zinc-200 flex flex-col justify-between gap-2.5 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Action 03</span>
                            <span id="flow-node-submitter-badge" class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold">Active</span>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-zinc-900">Autoresponder</div>
                            <div class="text-[10px] text-zinc-500 mt-0.5">Send custom receipt & answers</div>
                        </div>
                        <div class="text-[9.5px] font-mono text-zinc-400">Channel: Email</div>
                    </div>

                    <!-- Step 4: CRM Sync -->
                    <div class="p-3.5 rounded-xl bg-white border border-zinc-200 flex flex-col justify-between gap-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Action 04</span>
                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold">Active</span>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-zinc-900">CRM Lead Sync</div>
                            <div class="text-[10px] text-zinc-500 mt-0.5">Auto-tag, branch route, pipeline</div>
                        </div>
                        <div class="text-[9.5px] font-mono text-zinc-400">Status: Hot Lead</div>
                    </div>

                    <!-- Step 5: Webhook -->
                    <div id="flow-node-webhook" class="p-3.5 rounded-xl bg-white border border-zinc-200 flex flex-col justify-between gap-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Action 05</span>
                            <span id="flow-node-webhook-badge" class="px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-600 text-[9px] font-bold">Optional</span>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-zinc-900">Webhook Dispatch</div>
                            <div class="text-[10px] text-zinc-500 mt-0.5">Async JSON payload to endpoint</div>
                        </div>
                        <div class="text-[9.5px] font-mono text-zinc-400">Format: REST POST</div>
                    </div>
                </div>
            </div>

            <!-- MAIN 2-COLUMN SETTINGS & PREVIEW GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- LEFT COLUMN: CONTROLS & TEMPLATE STUDIO (7 COLS) -->
                <div class="lg:col-span-7 flex flex-col gap-6">
                    
                    <!-- CARD 1: ADMIN NOTIFICATIONS (COLLAPSIBLE, CLOSED BY DEFAULT) -->
                    <div class="cora-accordion-card bg-white border border-zinc-200/80 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-4 sm:p-5 flex items-center justify-between gap-3 text-left cursor-pointer hover:bg-zinc-50/70 transition-colors select-none" onclick="coraToggleFormSettingsAccordion(this)">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-800 shrink-0">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wider truncate">1. Admin & Team Alerts</h4>
                                    <p class="text-[11px] text-zinc-500 truncate">Notify your team immediately when a response arrives.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span id="admin-alerts-channel-count" class="hidden sm:inline-flex px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-600 text-[10px] font-semibold border border-zinc-200">Email & Push</span>
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200 shrink-0"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>

                        <!-- Email Notifications Switch & Fields -->
                        <div class="cora-accordion-body hidden px-5 pb-5 pt-4 border-t border-zinc-100 space-y-5">
                            <div class="space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-xs font-bold text-zinc-900 block">Email Admin Notification</label>
                                        <span class="text-[11px] text-zinc-400">Sends detailed intake breakdown upon submission.</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="setting-admin-email-enable" class="sr-only peer" checked>
                                        <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-900"></div>
                                    </label>
                                </div>

                                <div id="setting-admin-email-fields" class="space-y-3 pt-1">
                                    <div>
                                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Recipient Email(s)</label>
                                        <input type="text" id="setting-admin-email-to" class="w-full h-9 px-3 rounded-xl border border-zinc-200 text-xs bg-white text-zinc-900 placeholder:text-zinc-300 focus:border-zinc-400 focus:outline-none font-sans" placeholder="Leave blank to use default workspace admin email, or enter comma-separated emails" />
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Email Subject Template</label>
                                        <input type="text" id="setting-admin-email-subject" class="w-full h-9 px-3 rounded-xl border border-zinc-200 text-xs bg-white text-zinc-900 placeholder:text-zinc-300 focus:border-zinc-400 focus:outline-none font-sans" placeholder="New Submission: {form_title} from {submitter_name}" />
                                    </div>
                                </div>
                            </div>

                            <!-- Push & WhatsApp Channels -->
                            <div class="pt-3 border-t border-zinc-100 space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-xs font-bold text-zinc-900 block">In-App & Browser Push Alerts</label>
                                        <span class="text-[11px] text-zinc-400">Triggers real-time notification toast & PWA badge.</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="setting-admin-push-enable" class="sr-only peer" checked>
                                        <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-900"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-xs font-bold text-zinc-900 block">WhatsApp Instant Lead Notification</label>
                                        <span class="text-[11px] text-zinc-400">Forwards summary directly to agency WhatsApp number.</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="setting-admin-wa-enable" class="sr-only peer">
                                        <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-900"></div>
                                    </label>
                                </div>

                                <div id="setting-admin-wa-fields" class="hidden space-y-2 pt-1">
                                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">WhatsApp Recipient Number</label>
                                    <input type="text" id="setting-admin-wa-to" class="w-full h-9 px-3 rounded-xl border border-zinc-200 text-xs bg-white text-zinc-900 placeholder:text-zinc-300 focus:border-zinc-400 focus:outline-none font-mono" placeholder="+91 98765 43210 (Default: Connected Agency WhatsApp)" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2: RESPONDENT CONFIRMATION (AUTORESPONDER) (COLLAPSIBLE, CLOSED BY DEFAULT) -->
                    <div class="cora-accordion-card bg-white border border-zinc-200/80 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-4 sm:p-5 flex items-center justify-between gap-3 text-left cursor-pointer hover:bg-zinc-50/70 transition-colors select-none" onclick="coraToggleFormSettingsAccordion(this)">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-800 shrink-0">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wider truncate">2. Respondent Confirmation (User Email)</h4>
                                    <p class="text-[11px] text-zinc-500 truncate">Auto-reply to the person who filled and submitted your form.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <label class="relative inline-flex items-center cursor-pointer" onclick="event.stopPropagation()">
                                    <input type="checkbox" id="setting-submitter-email-enable" class="sr-only peer" checked>
                                    <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-900"></div>
                                </label>
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200 shrink-0"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>

                        <div class="cora-accordion-body hidden px-5 pb-5 pt-4 border-t border-zinc-100 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Sender Display Name</label>
                                    <input type="text" id="setting-submitter-sender-name" class="w-full h-9 px-3 rounded-xl border border-zinc-200 text-xs bg-white text-zinc-900 placeholder:text-zinc-300 focus:border-zinc-400 focus:outline-none font-sans" placeholder="e.g. Studio Director" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Reply-To Email</label>
                                    <input type="email" id="setting-submitter-reply-to" class="w-full h-9 px-3 rounded-xl border border-zinc-200 text-xs bg-white text-zinc-900 placeholder:text-zinc-300 focus:border-zinc-400 focus:outline-none font-sans" placeholder="contact@yourbusiness.com" />
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Confirmation Email Subject</label>
                                <input type="text" id="setting-submitter-subject" class="w-full h-9 px-3 rounded-xl border border-zinc-200 text-xs bg-white text-zinc-900 placeholder:text-zinc-300 focus:border-zinc-400 focus:outline-none font-sans" placeholder="Thank you for your submission: {form_title}" />
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Custom Message / Opening Note</label>
                                <textarea id="setting-submitter-message" rows="3" class="w-full p-3 rounded-xl border border-zinc-200 text-xs bg-white text-zinc-900 placeholder:text-zinc-300 focus:border-zinc-400 focus:outline-none font-sans leading-relaxed" placeholder="Thank you for reaching out! We have received your details and our team will review and get back to you within 24 hours. A copy of your submitted answers is below."></textarea>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-zinc-100">
                                <div>
                                    <label class="text-xs font-semibold text-zinc-900 block">Include Form Answers Table</label>
                                    <span class="text-[11px] text-zinc-400">Appends the clean monochromatic table of responses to the email.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="setting-submitter-include-answers" class="sr-only peer" checked>
                                    <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-900"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 3: TEMPLATE STUDIO & TOKEN PALETTE (COLLAPSIBLE, CLOSED BY DEFAULT) -->
                    <div class="cora-accordion-card bg-white border border-zinc-200/80 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-4 sm:p-5 flex items-center justify-between gap-3 text-left cursor-pointer hover:bg-zinc-50/70 transition-colors select-none" onclick="coraToggleFormSettingsAccordion(this)">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-800 shrink-0">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wider truncate">3. Curated Presets & Dynamic Tokens</h4>
                                    <p class="text-[11px] text-zinc-500 truncate">Apply ready-made templates or insert live variable tags.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200 shrink-0"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>

                        <div class="cora-accordion-body hidden px-5 pb-5 pt-4 border-t border-zinc-100 space-y-4">
                            <!-- Presets Selector Pills -->
                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-2">Apply 1-Click Preset Template</label>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" class="btn-apply-template-preset px-3 py-1.5 rounded-lg border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-800 text-xs font-semibold transition-all cursor-pointer" data-preset="lead_confirmation">
                                        Instant Lead Confirmation
                                    </button>
                                    <button type="button" class="btn-apply-template-preset px-3 py-1.5 rounded-lg border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-800 text-xs font-semibold transition-all cursor-pointer" data-preset="vip_intake">
                                        VIP Executive Intake
                                    </button>
                                    <button type="button" class="btn-apply-template-preset px-3 py-1.5 rounded-lg border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-800 text-xs font-semibold transition-all cursor-pointer" data-preset="booking_receipt">
                                        Booking & Consultation
                                    </button>
                                    <button type="button" class="btn-apply-template-preset px-3 py-1.5 rounded-lg border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-800 text-xs font-semibold transition-all cursor-pointer" data-preset="survey_receipt">
                                        Compliance Survey Receipt
                                    </button>
                                </div>
                            </div>

                            <!-- Dynamic Token Inserter -->
                            <div class="pt-3 border-t border-zinc-100">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1.5">Click to Insert Dynamic Tokens</label>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <button type="button" class="btn-insert-token px-2.5 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-mono transition-colors cursor-pointer" data-token="{form_title}">{form_title}</button>
                                    <button type="button" class="btn-insert-token px-2.5 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-mono transition-colors cursor-pointer" data-token="{submitter_name}">{submitter_name}</button>
                                    <button type="button" class="btn-insert-token px-2.5 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-mono transition-colors cursor-pointer" data-token="{submitter_email}">{submitter_email}</button>
                                    <button type="button" class="btn-insert-token px-2.5 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-mono transition-colors cursor-pointer" data-token="{submitter_phone}">{submitter_phone}</button>
                                    <button type="button" class="btn-insert-token px-2.5 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-mono transition-colors cursor-pointer" data-token="{submission_id}">{submission_id}</button>
                                    <button type="button" class="btn-insert-token px-2.5 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-mono transition-colors cursor-pointer" data-token="{submission_date}">{submission_date}</button>
                                    <button type="button" class="btn-insert-token px-2.5 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-mono transition-colors cursor-pointer" data-token="{workspace_name}">{workspace_name}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: LIVE MONOCHROMATIC PREVIEW CANVAS (5 COLS) -->
                <div class="lg:col-span-5 flex flex-col gap-4 sticky top-6">
                    <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-zinc-900"></span>
                                <h4 class="text-xs font-bold text-zinc-950 uppercase tracking-wider">Live Monochromatic Preview</h4>
                            </div>
                            <div class="flex items-center gap-1 bg-zinc-100 p-0.5 rounded-lg border border-zinc-200">
                                <button type="button" id="btn-preview-mode-submitter" class="px-2.5 py-1 rounded-md bg-white text-zinc-900 text-[11px] font-bold shadow-2xs cursor-pointer">Respondent</button>
                                <button type="button" id="btn-preview-mode-admin" class="px-2.5 py-1 rounded-md text-zinc-500 hover:text-zinc-900 text-[11px] font-semibold transition-colors cursor-pointer">Admin Alert</button>
                            </div>
                        </div>

                        <!-- Email Preview Canvas Container -->
                        <div class="bg-zinc-50 rounded-xl p-3 border border-zinc-200/60 overflow-hidden">
                            <div class="bg-white rounded-xl border border-zinc-200/90 p-5 shadow-sm space-y-4 font-sans text-xs">
                                <!-- Email Mock Header -->
                                <div class="border-b border-zinc-100 pb-3 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span id="preview-mock-sender" class="font-bold text-zinc-900 text-xs">Studio Director &lt;contact@yourbusiness.com&gt;</span>
                                        <span class="text-[10px] text-zinc-400">Just now</span>
                                    </div>
                                    <div id="preview-mock-subject" class="text-xs text-zinc-700 font-semibold">Thank you for your submission: Creative Intake</div>
                                </div>

                                <!-- Email Mock Body -->
                                <div class="space-y-3">
                                    <h2 id="preview-mock-title" class="text-base font-bold text-zinc-950 tracking-tight">Creative Intake</h2>
                                    <p id="preview-mock-message" class="text-zinc-600 leading-relaxed text-xs">
                                        Thank you for reaching out! We have received your details and our team will review and get back to you within 24 hours. A copy of your submitted answers is below.
                                    </p>
                                </div>

                                <!-- Email Mock Table -->
                                <div id="preview-mock-table-container" class="pt-2">
                                    <table class="w-full border-collapse text-left text-xs">
                                        <thead>
                                            <tr class="border-b-2 border-zinc-200 text-zinc-400 text-[10px] font-bold uppercase">
                                                <th class="py-2 pr-2">Field</th>
                                                <th class="py-2 pl-2">Response</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 text-zinc-700">
                                            <tr>
                                                <td class="py-2 pr-2 font-semibold text-zinc-800 w-2/5">Full Name</td>
                                                <td class="py-2 pl-2 text-zinc-900">Aarav Mehta</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2 pr-2 font-semibold text-zinc-800">Email</td>
                                                <td class="py-2 pl-2 text-zinc-900">aarav.mehta@example.com</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2 pr-2 font-semibold text-zinc-800">Project Scope</td>
                                                <td class="py-2 pl-2 text-zinc-900">Full Brand Identity & Website</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Email Mock Footer -->
                                <div class="pt-3 border-t border-zinc-100 text-center text-[10px] text-zinc-400 font-mono">
                                    Powered by Cora Forms &middot; Security Verified
                                </div>
                            </div>
                        </div>

                        <div class="text-[11px] text-zinc-400 text-center flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                            <span>100% Monochromatic &middot; High-contrast HTML rendering</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom scroll runway buffer so content is easily reachable above navigation -->
            <div class="h-28 sm:h-36 md:h-48 shrink-0 w-full" aria-hidden="true"></div>
        </div>
    </div>

    <!-- MODAL: SEND TEST NOTIFICATION -->
    <div id="cora-test-notification-modal" class="fixed inset-0 z-[999999] bg-zinc-950/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-md shadow-2xl p-6 space-y-5 animate-in fade-in zoom-in duration-150">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-800">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900">Send Test Notification</h3>
                        <p class="text-[11px] text-zinc-500">Dispatch a live test email using current settings.</p>
                    </div>
                </div>
                <button type="button" id="btn-close-test-modal" class="text-zinc-400 hover:text-zinc-700 p-1 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <div class="space-y-3.5">
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Destination Email Address</label>
                    <input type="email" id="test-notification-email-to" class="w-full h-9 px-3 rounded-xl border border-zinc-200 text-xs bg-white text-zinc-900 focus:border-zinc-400 focus:outline-none" placeholder="your-email@example.com" />
                </div>
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Test Template Mode</label>
                    <select id="test-notification-type" class="w-full h-9 px-3 rounded-xl border border-zinc-200 text-xs bg-white text-zinc-900 focus:border-zinc-400 focus:outline-none cursor-pointer">
                        <option value="submitter">Respondent Autoresponder (Client Confirmation)</option>
                        <option value="admin">Admin New Lead Alert</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-100">
                <button type="button" id="btn-cancel-test-modal" class="h-9 px-4 rounded-xl border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold transition-colors cursor-pointer">Cancel</button>
                <button type="button" id="btn-dispatch-test-email" class="h-9 px-4 rounded-xl bg-zinc-900 hover:bg-black text-white text-xs font-semibold transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                    <svg id="dispatch-test-spinner" class="hidden animate-spin h-3.5 w-3.5 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span id="dispatch-test-text">Send Test Email</span>
                </button>
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

            <!-- Center: History Controls -->
            <div class="hidden md:flex items-center gap-1 text-zinc-400">
                <button id="btn-editor-undo" type="button" class="h-7 w-7 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-400 hover:text-zinc-700 transition-colors border-0 bg-transparent cursor-pointer" title="Undo">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                </button>
                <button id="btn-editor-redo" type="button" class="h-7 w-7 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-400 hover:text-zinc-700 transition-colors border-0 bg-transparent cursor-pointer" title="Redo">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.13-9.36L23 10"></path></svg>
                </button>
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
                <div class="px-2.5 py-2 border-b border-zinc-200/80 flex items-center bg-white shrink-0">
                    <div id="left-panel-tabs" class="flex-1 grid grid-cols-5 p-1 bg-zinc-100 rounded-xl gap-0.5 select-none">
                        <button id="btn-left-tab-fields" type="button" class="py-1.5 px-0.5 rounded-lg text-[10px] font-bold bg-white text-zinc-950 shadow-2xs flex flex-col items-center justify-center gap-1 cursor-pointer transition-all border-0 outline-none" title="Add New Fields">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                            <span class="leading-none text-[9.5px]">Add</span>
                        </button>
                        <button id="btn-left-tab-settings" type="button" class="py-1.5 px-0.5 rounded-lg text-[10px] font-medium text-zinc-500 hover:text-zinc-900 flex flex-col items-center justify-center gap-1 cursor-pointer transition-all bg-transparent border-0 outline-none" title="Inspect Selected Field">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                            <span class="leading-none text-[9.5px]">Inspect</span>
                        </button>
                        <button id="btn-left-tab-style" type="button" class="py-1.5 px-0.5 rounded-lg text-[10px] font-medium text-zinc-500 hover:text-zinc-900 flex flex-col items-center justify-center gap-1 cursor-pointer transition-all bg-transparent border-0 outline-none" title="Form Theme & Styling">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                            <span class="leading-none text-[9.5px]">Style</span>
                        </button>
                        <button id="btn-left-tab-form" type="button" class="py-1.5 px-0.5 rounded-lg text-[10px] font-medium text-zinc-500 hover:text-zinc-900 flex flex-col items-center justify-center gap-1 cursor-pointer transition-all bg-transparent border-0 outline-none" title="Form Settings & Logic">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span class="leading-none text-[9.5px]">Settings</span>
                        </button>
                        <button id="btn-left-tab-integ" type="button" class="py-1.5 px-0.5 rounded-lg text-[10px] font-medium text-zinc-500 hover:text-zinc-900 flex flex-col items-center justify-center gap-1 cursor-pointer transition-all bg-transparent border-0 outline-none" title="Integrations & Webhooks">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            <span class="leading-none text-[9.5px]">Integ</span>
                        </button>
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

                    <!-- Placeholder Input -->
                    <div id="inspector-placeholder-wrapper" class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Placeholder Text</label>
                        <input id="inspector-field-placeholder" type="text" placeholder="Enter placeholder..." class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-900 outline-none focus:border-zinc-400 w-full" />
                    </div>

                    <!-- Slider Configuration -->
                    <div id="inspector-slider-wrapper" class="space-y-2 pt-2 border-t border-zinc-100 hidden">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Slider Range & Step</label>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <span class="text-[9px] text-zinc-400 font-bold block mb-1">Min</span>
                                <input id="inspector-slider-min" type="number" value="0" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs font-mono w-full" />
                            </div>
                            <div>
                                <span class="text-[9px] text-zinc-400 font-bold block mb-1">Max</span>
                                <input id="inspector-slider-max" type="number" value="100" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs font-mono w-full" />
                            </div>
                            <div>
                                <span class="text-[9px] text-zinc-400 font-bold block mb-1">Step</span>
                                <input id="inspector-slider-step" type="number" value="1" class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs font-mono w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Field Parameter Mapping -->
                    <div id="inspector-hidden-wrapper" class="space-y-2 pt-2 border-t border-zinc-100 hidden">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">URL Parameter Key</label>
                        <input id="inspector-hidden-param" type="text" placeholder="e.g. ref, utm_source" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-mono text-zinc-900 outline-none focus:border-zinc-400 w-full" />
                        <span class="text-[9px] text-zinc-400 block">Auto-captured from URL query string or defaults on load</span>
                    </div>

                    <!-- Rich Text Content Editor -->
                    <div id="inspector-rich-text-wrapper" class="space-y-1 pt-2 border-t border-zinc-100 hidden">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Rich Text Content / HTML</label>
                        <textarea id="inspector-rich-text-content" rows="4" placeholder="Enter rich text instructions or announcement..." class="p-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none focus:border-zinc-400 w-full resize-none font-mono"></textarea>
                    </div>

                    <!-- Matrix Rows & Columns Editor -->
                    <div id="inspector-matrix-wrapper" class="space-y-3 pt-2 border-t border-zinc-100 hidden">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Matrix Rows (Questions)</label>
                                <button id="btn-add-matrix-row" type="button" class="text-[10px] font-bold text-zinc-900 hover:underline cursor-pointer border-none bg-transparent">+ Add Row</button>
                            </div>
                            <div id="inspector-matrix-rows-container" class="space-y-1.5"></div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Matrix Columns (Options)</label>
                                <button id="btn-add-matrix-col" type="button" class="text-[10px] font-bold text-zinc-900 hover:underline cursor-pointer border-none bg-transparent">+ Add Column</button>
                            </div>
                            <div id="inspector-matrix-cols-container" class="space-y-1.5"></div>
                        </div>
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

                <!-- 2.5. #left-tab-style: Visual Styling & Design Studio -->
                <div id="left-tab-style" class="hidden flex-1 overflow-y-auto p-4 space-y-5 font-sans">
                    <!-- Section: Header / Preset Title -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Visual Theme Preset</span>
                            <span class="text-[9px] font-semibold text-zinc-400">Real-time sync</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 pt-1">
                            <button type="button" class="btn-theme-preset p-2.5 rounded-xl border border-zinc-950 bg-zinc-950 text-white text-left flex flex-col gap-1 transition-all cursor-pointer shadow-xs" data-preset="light">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold">Notion Minimal</span>
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                </div>
                                <span class="text-[9px] text-zinc-400">Clean light surface</span>
                            </button>
                            <button type="button" class="btn-theme-preset p-2.5 rounded-xl border border-zinc-200 bg-zinc-900 text-white text-left flex flex-col gap-1 transition-all cursor-pointer hover:border-zinc-400" data-preset="dark">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold">Obsidian Dark</span>
                                    <span class="w-2 h-2 rounded-full bg-zinc-500"></span>
                                </div>
                                <span class="text-[9px] text-zinc-400">Deep neutral black</span>
                            </button>
                            <button type="button" class="btn-theme-preset p-2.5 rounded-xl border border-zinc-200 bg-[#FAF7F2] text-zinc-900 text-left flex flex-col gap-1 transition-all cursor-pointer hover:border-zinc-400" data-preset="cream">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold">Claude Cream</span>
                                    <span class="w-2 h-2 rounded-full bg-[#D4CEB8]"></span>
                                </div>
                                <span class="text-[9px] text-zinc-500">Warm Anthropic cream</span>
                            </button>
                            <button type="button" class="btn-theme-preset p-2.5 rounded-xl border border-zinc-200 bg-slate-50 text-slate-900 text-left flex flex-col gap-1 transition-all cursor-pointer hover:border-zinc-400" data-preset="slate">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold">Slate Minimal</span>
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                </div>
                                <span class="text-[9px] text-slate-500">Cool gray tones</span>
                            </button>
                        </div>
                    </div>

                    <!-- Section: Background & Embed Transparency -->
                    <div class="space-y-3 pt-3 border-t border-zinc-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Embed & Canvas Background</span>
                                <span class="text-[10px] text-zinc-500">Blend form with external landing pages</span>
                            </div>
                        </div>

                        <!-- Transparent Toggle -->
                        <div class="p-3 rounded-xl border border-zinc-200 bg-white flex items-center justify-between shadow-2xs">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-zinc-900">Transparent Background</span>
                                <span class="text-[10px] text-zinc-400">Inherit host page/document background</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="style-bg-transparent" class="sr-only peer">
                                <div class="w-8 h-4 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-zinc-950"></div>
                            </label>
                        </div>

                        <!-- Solid Background Custom Pickers -->
                        <div id="style-solid-bg-wrapper" class="grid grid-cols-2 gap-2">
                            <div class="space-y-1">
                                <label class="text-[9px] font-bold text-zinc-400 uppercase">Page Canvas</label>
                                <div class="flex items-center gap-1.5 h-8 px-2 border border-zinc-200 rounded-lg bg-white">
                                    <input type="color" id="style-bg-color-picker" value="#FAFAFA" class="w-4 h-4 rounded border-0 p-0 cursor-pointer outline-none" />
                                    <input type="text" id="style-bg-color-hex" value="#FAFAFA" class="text-[10px] font-mono text-zinc-800 uppercase outline-none w-full bg-transparent" />
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-bold text-zinc-400 uppercase">Card Surface</label>
                                <div class="flex items-center gap-1.5 h-8 px-2 border border-zinc-200 rounded-lg bg-white">
                                    <input type="color" id="style-card-bg-picker" value="#FFFFFF" class="w-4 h-4 rounded border-0 p-0 cursor-pointer outline-none" />
                                    <input type="text" id="style-card-bg-hex" value="#FFFFFF" class="text-[10px] font-mono text-zinc-800 uppercase outline-none w-full bg-transparent" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Brand Accent Color -->
                    <div class="space-y-2 pt-3 border-t border-zinc-100">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Accent & Action Color</label>
                        <div class="flex items-center gap-2">
                            <button type="button" class="btn-accent-swatch w-7 h-7 rounded-full bg-zinc-950 ring-2 ring-zinc-950 ring-offset-2 transition-all cursor-pointer border-0" data-color="#09090B" title="Pure Black"></button>
                            <button type="button" class="btn-accent-swatch w-7 h-7 rounded-full bg-zinc-700 hover:ring-2 hover:ring-zinc-400 ring-offset-2 transition-all cursor-pointer border-0" data-color="#27272A" title="Charcoal"></button>
                            <button type="button" class="btn-accent-swatch w-7 h-7 rounded-full bg-slate-800 hover:ring-2 hover:ring-slate-400 ring-offset-2 transition-all cursor-pointer border-0" data-color="#1E293B" title="Midnight Slate"></button>
                            <button type="button" class="btn-accent-swatch w-7 h-7 rounded-full bg-emerald-800 hover:ring-2 hover:ring-emerald-400 ring-offset-2 transition-all cursor-pointer border-0" data-color="#065F46" title="Forest Emerald"></button>
                            <div class="flex-1 flex items-center gap-1.5 h-7 px-2 border border-zinc-200 rounded-lg bg-white ml-1">
                                <input type="color" id="style-accent-picker" value="#09090B" class="w-4 h-4 rounded border-0 p-0 cursor-pointer outline-none" />
                                <input type="text" id="style-accent-hex" value="#09090B" class="text-[10px] font-mono text-zinc-800 uppercase outline-none w-full bg-transparent" />
                            </div>
                        </div>
                    </div>

                    <!-- Section: Card Framing & Embed Container -->
                    <div class="space-y-2 pt-3 border-t border-zinc-100">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Container Framing</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button type="button" class="btn-card-style py-2 px-1 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10.5px] font-bold transition-all cursor-pointer shadow-2xs" data-style="bordered">
                                Bordered
                            </button>
                            <button type="button" class="btn-card-style py-2 px-1 rounded-lg border border-zinc-200 bg-white text-zinc-700 hover:text-zinc-950 text-center text-[10.5px] font-medium transition-all cursor-pointer" data-style="borderless">
                                Borderless
                            </button>
                            <button type="button" class="btn-card-style py-2 px-1 rounded-lg border border-zinc-200 bg-white text-zinc-700 hover:text-zinc-950 text-center text-[10.5px] font-medium transition-all cursor-pointer" data-style="elevated">
                                Elevated
                            </button>
                        </div>
                    </div>

                    <!-- Section: Corner Radius -->
                    <div class="space-y-2 pt-3 border-t border-zinc-100">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Corner Radius</label>
                        <div class="grid grid-cols-4 gap-1">
                            <button type="button" class="btn-radius py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10px] font-medium transition-all cursor-pointer" data-radius="none">Sharp 0</button>
                            <button type="button" class="btn-radius py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10px] font-medium transition-all cursor-pointer" data-radius="sm">Subtle 8</button>
                            <button type="button" class="btn-radius py-1.5 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10px] font-bold transition-all cursor-pointer" data-radius="md">Modern 16</button>
                            <button type="button" class="btn-radius py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10px] font-medium transition-all cursor-pointer" data-radius="pill">Pill 28</button>
                        </div>
                    </div>

                    <!-- Section: Typography Stack -->
                    <div class="space-y-2 pt-3 border-t border-zinc-100">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Typography</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button type="button" class="btn-font py-2 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10.5px] font-bold transition-all cursor-pointer" data-font="sans">Modern Sans</button>
                            <button type="button" class="btn-font py-2 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10.5px] font-mono font-medium transition-all cursor-pointer" data-font="mono">Mono</button>
                            <button type="button" class="btn-font py-2 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10.5px] font-serif font-medium transition-all cursor-pointer" data-font="serif">Serif</button>
                        </div>
                    </div>

                    <!-- Section: Field Density -->
                    <div class="space-y-2 pt-3 border-t border-zinc-100">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Spacing Density</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button type="button" class="btn-density py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10.5px] font-medium transition-all cursor-pointer" data-density="compact">Compact</button>
                            <button type="button" class="btn-density py-1.5 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10.5px] font-bold transition-all cursor-pointer" data-density="normal">Standard</button>
                            <button type="button" class="btn-density py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10.5px] font-medium transition-all cursor-pointer" data-density="spacious">Spacious</button>
                        </div>
                    </div>

                    <!-- Section: Workspace Branding Header Toggle -->
                    <div class="pt-3 border-t border-zinc-100">
                        <div class="p-3 rounded-xl border border-zinc-200 bg-white flex items-center justify-between shadow-2xs">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-zinc-900">Workspace Header Badge</span>
                                <span class="text-[10px] text-zinc-400">Show 'Verified Intake' branding header</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="style-show-branding" class="sr-only peer" checked>
                                <div class="w-8 h-4 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-zinc-950"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Section: Custom CSS Editor -->
                    <div class="space-y-1.5 pt-3 border-t border-zinc-100">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Custom CSS Injections</label>
                            <span class="text-[9px] font-mono text-zinc-400">Scoped</span>
                        </div>
                        <textarea id="style-custom-css" rows="3" placeholder="/* Custom CSS overrides for inputs and layout */" class="p-2.5 font-mono text-[10px] rounded-lg border border-zinc-200 bg-white text-zinc-900 outline-none focus:border-zinc-400 w-full resize-none"></textarea>
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

    <!-- BACKDROP FOR SUBMISSIONS BOTTOM SHEET -->
    <div id="cora-submissions-backdrop" onclick="closeSubmissionsDrawer()" class="hidden fixed inset-0 bg-zinc-950/45 backdrop-blur-xs z-[90] transition-opacity duration-300 opacity-0 pointer-events-none cursor-pointer"></div>

    <!-- STATE 3: SUBMISSIONS LIST BOTTOM SHEET DASHBOARD -->
    <div id="cora-submissions-drawer" class="hidden fixed bottom-0 left-0 right-0 w-full max-w-6xl mx-auto h-[82vh] max-h-[85vh] bg-white shadow-2xl rounded-t-3xl border-t border-x border-zinc-200/80 z-[100] transform translate-y-full transition-transform duration-350 ease-[cubic-bezier(0.16,1,0.3,1)] flex flex-col overflow-hidden font-sans">
        <!-- Drag Handle Indicator -->
        <div class="flex items-center justify-center pt-2.5 pb-1 shrink-0 select-none cursor-grab">
            <div class="w-10 h-1 rounded-full bg-zinc-300"></div>
        </div>

        <!-- Dashboard Header Bar -->
        <div class="px-5 sm:px-6 py-3.5 sm:py-4 border-b border-zinc-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3.5 shrink-0 bg-white">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg viewBox="0 0 24 24" width="17" height="17" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h3 class="text-sm sm:text-[15px] font-bold text-zinc-950 tracking-tight" id="drawer-form-title">Form Submissions Dashboard</h3>
                        <span id="drawer-responses-count" class="px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-700 text-[11px] font-semibold shrink-0">0 Entries</span>
                    </div>
                    <p class="text-[11px] sm:text-[12px] text-zinc-500 mt-0.5 font-normal" id="drawer-form-meta">View, filter, and export user response entries for this form.</p>
                </div>
            </div>

            <!-- Action Controls -->
            <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                <!-- Search Input -->
                <div class="relative">
                    <input id="submissions-search-input" type="text" placeholder="Search entries..." class="h-8 pl-8 pr-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none focus:border-zinc-400 w-36 sm:w-48" />
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>

                <!-- Export CSV Button -->
                <button id="btn-export-submissions-csv" class="h-8 px-3 rounded-lg bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all flex items-center gap-1.5 cursor-pointer shadow-xs shrink-0 border-0">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export CSV
                </button>

                <!-- Connect Google Sheets Button (Locked) -->
                <button id="btn-connect-google-sheets" class="h-8 px-2.5 rounded-lg bg-white border border-zinc-200 text-zinc-700 text-xs font-semibold hover:bg-zinc-50 transition-all flex items-center gap-1.5 cursor-pointer shrink-0">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    <span>Google Sheets</span>
                    <span class="px-1.5 py-0.2 bg-zinc-900 text-white text-[8.5px] font-mono font-bold rounded uppercase tracking-wider">Soon</span>
                </button>

                <!-- Close Button -->
                <button id="btn-close-submissions" onclick="closeSubmissionsDrawer()" class="h-8 w-8 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-500 hover:text-zinc-900 cursor-pointer transition-colors border-0 bg-transparent shrink-0" title="Close">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>

        <!-- Submissions Entries Content (Data Table / Grid) -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 relative" id="submissions-drawer-content">
            <!-- Dynamic entries table goes here -->
        </div>

        <!-- RIGHT SLIDE-OVER ENTRY INSPECTOR DRAWER -->
        <div id="cora-entry-inspector" class="hidden absolute top-0 right-0 bottom-0 w-full sm:w-[480px] bg-white border-l border-zinc-200 shadow-2xl z-30 flex flex-col font-sans rounded-tr-3xl overflow-hidden transform translate-x-full transition-transform duration-300">
            <!-- Inspector Header -->
            <div class="px-5 sm:px-6 py-3.5 sm:py-4 border-b border-zinc-200/80 flex items-center justify-between bg-zinc-50/70 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-zinc-950 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs sm:text-sm font-bold text-zinc-950" id="inspector-entry-id">Entry #000</h4>
                            <span id="inspector-status-badge" class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700">Completed</span>
                        </div>
                        <p class="text-[10px] sm:text-[10.5px] text-zinc-400 font-mono mt-0.5" id="inspector-submitted-at">Submitted --</p>
                    </div>
                </div>
                <button onclick="closeEntryInspector()" class="h-7 w-7 rounded-lg hover:bg-zinc-200/60 flex items-center justify-center text-zinc-500 hover:text-zinc-900 cursor-pointer border-0 bg-transparent">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <!-- Inspector Body -->
            <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4" id="inspector-body-content">
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

    <!-- UNIVERSAL CONNECT & EMBED EVERYWHERE STUDIO BOTTOM DRAWER -->
    <div id="cora-embed-drawer-backdrop" class="fixed inset-0 z-[99998] hidden bg-zinc-950/50 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>
    <div id="cora-embed-drawer" class="fixed bottom-0 left-0 right-0 w-full rounded-t-2xl bg-white shadow-2xl border-t border-zinc-200 z-[99999] transform translate-y-full transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] flex flex-col overflow-hidden font-sans" style="height: 80vh !important; max-height: 80vh !important; min-height: 80vh !important;">
        <!-- Drag indicator handle -->
        <div class="flex items-center justify-center pt-2.5 pb-1 shrink-0 select-none">
            <div class="w-10 h-1 rounded-full bg-zinc-300"></div>
        </div>
        <!-- Top Drawer Header -->
        <div class="px-8 py-3.5 border-b border-zinc-200/80 flex items-center justify-between shrink-0 bg-white">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><polyline points="16 6 12 2 8 6"></polyline><line x1="12" y1="2" x2="12" y2="15"></line></svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-sm font-bold text-zinc-950 tracking-tight" id="embed-drawer-title">Connect &amp; Embed Everywhere</h3>
                        <span id="embed-drawer-key-badge" class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-zinc-100 text-zinc-700">frm_...</span>
                    </div>
                    <p class="text-[11px] text-zinc-400 mt-0.5">Distribute via responsive iFrames, Webflow, WordPress, HTML, PDFs, QR code, or direct link</p>
                </div>
            </div>
            <button id="btn-close-embed-drawer" type="button" class="h-8 w-8 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-400 hover:text-zinc-700 transition-colors border-0 cursor-pointer">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Embed Customization & Presets Bar -->
        <div class="px-8 py-2.5 bg-zinc-50 border-b border-zinc-200/80 shrink-0 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Embed Preset:</span>
                <select id="embed-preset-selector" class="h-7 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-800 outline-none cursor-pointer">
                    <option value="landing">Clean Landing Page (Transparent &amp; Borderless)</option>
                    <option value="card">Framed Card (Standard)</option>
                    <option value="dark">Dark Mode Theme</option>
                    <option value="whitelabel">Pure White-Label (No Branding)</option>
                </select>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium text-zinc-700">
                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                    <input type="checkbox" id="embed-opt-transparent" checked class="w-3.5 h-3.5 rounded accent-zinc-950 cursor-pointer">
                    <span class="text-[11px]">Transparent BG</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                    <input type="checkbox" id="embed-opt-borderless" checked class="w-3.5 h-3.5 rounded accent-zinc-950 cursor-pointer">
                    <span class="text-[11px]">Borderless</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                    <input type="checkbox" id="embed-opt-hide-header" checked class="w-3.5 h-3.5 rounded accent-zinc-950 cursor-pointer">
                    <span class="text-[11px]">Hide Header</span>
                </label>
            </div>
        </div>

        <!-- Channel Navigation Tabs -->
        <div class="px-8 border-b border-zinc-200 shrink-0 flex items-center gap-1 bg-white overflow-x-auto whitespace-nowrap scrollbar-none">
            <button id="tab-embed-link" class="cora-embed-tab py-2.5 px-3 text-xs font-bold text-zinc-950 border-b-2 border-zinc-950 -mb-px flex items-center gap-1.5 cursor-pointer bg-transparent border-t-0 border-l-0 border-r-0 outline-none">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                <span>Direct Link &amp; PDF QR</span>
            </button>
            <button id="tab-embed-iframe" class="cora-embed-tab py-2.5 px-3 text-xs font-medium text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent -mb-px flex items-center gap-1.5 cursor-pointer bg-transparent border-t-0 border-l-0 border-r-0 outline-none">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                <span>Responsive iFrame</span>
            </button>
            <button id="tab-embed-widget" class="cora-embed-tab py-2.5 px-3 text-xs font-medium text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent -mb-px flex items-center gap-1.5 cursor-pointer bg-transparent border-t-0 border-l-0 border-r-0 outline-none">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                <span>JS Widget / Popup</span>
            </button>
            <button id="tab-embed-headless" class="cora-embed-tab py-2.5 px-3 text-xs font-medium text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent -mb-px flex items-center gap-1.5 cursor-pointer bg-transparent border-t-0 border-l-0 border-r-0 outline-none">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                <span>HTML Connect Code</span>
            </button>
            <button id="tab-embed-sandbox" class="cora-embed-tab py-2.5 px-3 text-xs font-medium text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent -mb-px flex items-center gap-1.5 cursor-pointer bg-transparent border-t-0 border-l-0 border-r-0 outline-none">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                <span>Live Sandbox</span>
            </button>
        </div>

        <!-- Tab Contents Area -->
        <div class="flex-1 min-h-0 overflow-y-auto p-8 space-y-7 w-full max-w-5xl mx-auto">
            <!-- TAB 1: Direct Link & PDF QR -->
            <div id="embed-content-link" class="space-y-6">
                <!-- Hosted Link Box -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Hosted Direct Form URL</label>
                    <div class="flex items-center gap-2">
                        <input id="embed-url-input" type="text" readonly class="h-9 px-3 rounded-xl border border-zinc-200 bg-zinc-50 text-xs text-zinc-800 font-mono flex-1 outline-none select-all" />
                        <button id="btn-embed-copy-url" type="button" class="h-9 px-4 rounded-xl bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 shrink-0 transition-all border-0 cursor-pointer flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            Copy Link
                        </button>
                        <button id="btn-embed-open-live" type="button" class="h-9 px-3 rounded-xl border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold shrink-0 transition-all flex items-center gap-1 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            Open
                        </button>
                    </div>
                </div>

                <!-- PDF & Document Hyperlink Generator -->
                <div class="p-4 rounded-2xl border border-zinc-200 bg-zinc-50/50 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-zinc-200 text-zinc-700 flex items-center justify-center"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></span>
                            <span class="text-xs font-bold text-zinc-900">PDF, Invoice &amp; Document Hyperlink</span>
                        </div>
                        <button id="btn-embed-copy-markdown" type="button" class="text-[10px] font-bold text-zinc-900 hover:underline cursor-pointer border-0 bg-transparent">Copy Markdown</button>
                    </div>
                    <p class="text-[10.5px] text-zinc-500">Ready to paste into Canva, Adobe Acrobat, Word, Google Docs, Notion proposals, or emails.</p>
                    <input id="embed-markdown-input" type="text" readonly class="h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-[11px] text-zinc-700 font-mono w-full outline-none select-all" />
                </div>

                <!-- Dynamic QR Code Generator for Documents & Print -->
                <div class="p-5 rounded-2xl border border-zinc-200 bg-white shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-zinc-950">Dynamic QR Code (Print &amp; PDF Proposals)</h4>
                            <p class="text-[10.5px] text-zinc-500 mt-0.5">Scannable on any smartphone camera. Ideal for PDF invoices, flyers, and client presentations.</p>
                        </div>
                        <button id="btn-embed-download-qr" type="button" class="h-8 px-3 rounded-lg bg-zinc-950 text-white text-[11px] font-bold hover:bg-zinc-800 transition-all flex items-center gap-1.5 cursor-pointer border-0">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Download PNG
                        </button>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-5 pt-1">
                        <div class="p-2 rounded-xl border border-zinc-200 bg-white shadow-xs shrink-0 flex items-center justify-center w-36 h-36">
                            <img id="embed-qr-image" src="" alt="Form QR Code" class="w-32 h-32 object-contain" />
                        </div>
                        <div class="space-y-2 text-left">
                            <div class="text-[11px] text-zinc-600 leading-relaxed">
                                Users who scan this will immediately open your personalized intake form with mobile-optimized touch snappiness.
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">Live Resolving</span>
                                <span class="text-[10px] text-zinc-400 font-mono">240 × 240 px</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Responsive iFrame -->
            <div id="embed-content-iframe" class="hidden space-y-5">
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Responsive Auto-Height iFrame Snippet</label>
                        <button id="btn-embed-copy-iframe" type="button" class="text-[10px] font-bold text-zinc-950 hover:underline cursor-pointer border-0 bg-transparent flex items-center gap-1">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            Copy Embed Code
                        </button>
                    </div>
                    <textarea id="embed-iframe-code" rows="5" readonly class="p-3 font-mono text-[11px] leading-relaxed rounded-xl border border-zinc-200 bg-zinc-50 text-zinc-800 w-full outline-none select-all resize-none"></textarea>
                </div>

                <div class="p-4 rounded-xl border border-zinc-150 bg-zinc-50/50 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-zinc-900">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Zero Double-Scrollbars Guarantee</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 leading-relaxed">
                        This snippet loads the lightweight <code class="text-[10px] font-mono bg-zinc-200 px-1 py-0.5 rounded text-zinc-900">cora-form-embed.js</code> engine which listens for height changes via postMessage and resizes the container dynamically as respondents navigate form steps.
                    </p>
                </div>

                <!-- Platform Integration Chips -->
                <div class="space-y-1.5 pt-1">
                    <span class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider block">Compatible With:</span>
                    <div class="flex items-center gap-2 flex-wrap text-[10.5px] font-semibold text-zinc-600">
                        <span class="px-2.5 py-1 rounded-lg border border-zinc-200 bg-white">Webflow</span>
                        <span class="px-2.5 py-1 rounded-lg border border-zinc-200 bg-white">WordPress / Elementor</span>
                        <span class="px-2.5 py-1 rounded-lg border border-zinc-200 bg-white">Squarespace</span>
                        <span class="px-2.5 py-1 rounded-lg border border-zinc-200 bg-white">Carrd</span>
                        <span class="px-2.5 py-1 rounded-lg border border-zinc-200 bg-white">Shopify</span>
                        <span class="px-2.5 py-1 rounded-lg border border-zinc-200 bg-white">Notion Embeds</span>
                    </div>
                </div>
            </div>

            <!-- TAB 3: JS Widget / Popup -->
            <div id="embed-content-widget" class="hidden space-y-6">
                <!-- Option A: Inline Container -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Option A: Drop-in Inline Widget</label>
                        <button id="btn-embed-copy-widget-inline" type="button" class="text-[10px] font-bold text-zinc-950 hover:underline cursor-pointer border-0 bg-transparent">Copy Snippet</button>
                    </div>
                    <textarea id="embed-widget-inline-code" rows="4" readonly class="p-3 font-mono text-[11px] leading-relaxed rounded-xl border border-zinc-200 bg-zinc-50 text-zinc-800 w-full outline-none select-all resize-none"></textarea>
                </div>

                <!-- Option B: Floating Trigger / Drawer -->
                <div class="space-y-2 pt-2 border-t border-zinc-100">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Option B: Floating Popup / Drawer Button</label>
                        <button id="btn-embed-copy-widget-popup" type="button" class="text-[10px] font-bold text-zinc-950 hover:underline cursor-pointer border-0 bg-transparent">Copy Snippet</button>
                    </div>
                    <textarea id="embed-widget-popup-code" rows="4" readonly class="p-3 font-mono text-[11px] leading-relaxed rounded-xl border border-zinc-200 bg-zinc-50 text-zinc-800 w-full outline-none select-all resize-none"></textarea>
                    <p class="text-[10.5px] text-zinc-500">Clicking this button triggers a smooth right-sliding drawer sheet directly on the host website with backdrop blur.</p>
                </div>
            </div>

            <!-- TAB 4: Headless HTML Connect Form -->
            <div id="embed-content-headless" class="hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-950">Raw HTML Connect Form (Headless API)</h4>
                        <p class="text-[10.5px] text-zinc-500 mt-0.5">Paste directly into your custom HTML landing page. Submissions route to Cora leads &amp; webhooks.</p>
                    </div>
                    <button id="btn-embed-copy-headless" type="button" class="h-8 px-3 rounded-lg bg-zinc-950 text-white text-[11px] font-bold hover:bg-zinc-800 transition-all flex items-center gap-1.5 cursor-pointer border-0">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        Copy HTML Form
                    </button>
                </div>
                <textarea id="embed-headless-code" rows="8" readonly class="p-3 font-mono text-[10.5px] leading-relaxed rounded-xl border border-zinc-200 bg-zinc-50 text-zinc-800 w-full outline-none select-all resize-none"></textarea>
                <div class="text-[10.5px] text-zinc-400">
                    Includes Honeypot spam defense, CORS authorization, and an unobtrusive 10-line fetch handler.
                </div>
            </div>

            <!-- TAB 5: Live Interactive Sandbox -->
            <div id="embed-content-sandbox" class="hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Simulate Host Page Background:</span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" class="btn-sandbox-bg w-6 h-6 rounded-md bg-white border border-zinc-300 shadow-2xs cursor-pointer" data-bg="#ffffff" title="White"></button>
                            <button type="button" class="btn-sandbox-bg w-6 h-6 rounded-md bg-[#09090B] border border-zinc-700 shadow-2xs cursor-pointer" data-bg="#09090b" title="Dark"></button>
                            <button type="button" class="btn-sandbox-bg w-6 h-6 rounded-md bg-[#FBFaf7] border border-stone-300 shadow-2xs cursor-pointer" data-bg="#FBFaf7" title="Claude Cream"></button>
                            <button type="button" class="btn-sandbox-bg w-6 h-6 rounded-md bg-zinc-100 border border-zinc-200 shadow-2xs cursor-pointer flex items-center justify-center" data-bg="checkerboard" title="Transparent Checkerboard"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></button>
                        </div>
                    </div>
                    <span class="text-[10.5px] font-semibold text-zinc-500" id="sandbox-viewport-label">Embedded Sandbox Preview</span>
                </div>

                <div id="sandbox-frame-wrapper" class="w-full rounded-2xl border border-zinc-200 p-6 min-h-[420px] flex items-center justify-center transition-colors duration-200" style="background-color: #ffffff;">
                    <iframe id="embed-sandbox-iframe" src="about:blank" class="w-full max-w-lg rounded-xl transition-all" style="min-height: 380px; border: none; background: transparent;"></iframe>
                </div>
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
if ( ! empty( $forms_db ) ) {
    $form_ids = array_column( $forms_db, 'id' );
    $in_sql = implode( ',', array_map( 'intval', $form_ids ) );

    // Batch fetch all blocks in 1 query
    $all_blocks = $wpdb->get_results( "SELECT form_id, blocks_json, logic_json FROM {$wpdb->prefix}cora_form_blocks WHERE form_id IN ($in_sql)", ARRAY_A );
    $blocks_by_form = array();
    if ( is_array( $all_blocks ) ) {
        foreach ( $all_blocks as $blk ) {
            $blocks_by_form[ intval( $blk['form_id'] ) ] = $blk;
        }
    }

    // Batch fetch all submission counts in 1 query
    $all_counts = $wpdb->get_results( "SELECT form_id, COUNT(*) as cnt FROM {$wpdb->prefix}cora_form_submissions WHERE form_id IN ($in_sql) GROUP BY form_id", ARRAY_A );
    $counts_by_form = array();
    if ( is_array( $all_counts ) ) {
        foreach ( $all_counts as $c ) {
            $counts_by_form[ intval( $c['form_id'] ) ] = intval( $c['cnt'] );
        }
    }

    foreach ( $forms_db as $form ) {
        $fid = intval( $form['id'] );
        if ( empty( $form['form_key'] ) ) {
            $form['form_key'] = 'frm_' . substr( md5( $form['id'] . $form['title'] ), 0, 8 );
            $wpdb->update( $wpdb->prefix . 'cora_forms', array( 'form_key' => $form['form_key'] ), array( 'id' => $form['id'] ) );
        }
        $form['styling'] = json_decode( $form['styling'], true ) ?: array();
        $form['settings'] = json_decode( $form['settings'], true ) ?: array();
        
        $blocks_row = isset( $blocks_by_form[ $fid ] ) ? $blocks_by_form[ $fid ] : null;
        $form['blocks'] = $blocks_row ? (json_decode( $blocks_row['blocks_json'], true ) ?: array()) : array();
        $form['logic'] = $blocks_row ? (json_decode( $blocks_row['logic_json'], true ) ?: array()) : array();
        $form['submission_count'] = isset( $counts_by_form[ $fid ] ) ? $counts_by_form[ $fid ] : 0;
        
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
    const tabFormsSettings = document.getElementById('tab-forms-settings');
    
    const listTabContent = document.getElementById('forms-list-tab-content');
    const funnelTabContent = document.getElementById('forms-funnel-tab-content');
    const clausesTabContent = document.getElementById('forms-clauses-tab-content');
    const auditTabContent = document.getElementById('forms-audit-tab-content');
    const settingsTabContent = document.getElementById('forms-settings-tab-content');

    if(tabFormsList) tabFormsList.addEventListener('click', function() { window.location.hash = '#list'; });
    if(tabFunnel) tabFunnel.addEventListener('click', function() { window.location.hash = '#funnel'; });
    if(tabClauses) tabClauses.addEventListener('click', function() { window.location.hash = '#clauses'; });
    if(tabAuditLogs) tabAuditLogs.addEventListener('click', function() { window.location.hash = '#audit-log'; });
    if(tabFormsSettings) tabFormsSettings.addEventListener('click', function() { window.location.hash = '#settings'; });

    jQuery(document).on('click', '#btn-funnel-edit-form', function(e) {
        e.preventDefault();
        const sel = document.getElementById('funnel-form-selector');
        const selId = sel ? sel.value : 'all';
        if (selId && selId !== 'all') {
            window.location.hash = '#edit/' + selId;
        } else if (formsData && formsData.length > 0) {
            window.location.hash = '#edit/' + formsData[0].id;
        } else {
            window.location.hash = '#new';
        }
    });

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
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch(e) { response = []; }
                }
                formsData = Array.isArray(response) ? response : [];
                formsData.forEach(f => {
                    if (f && typeof f === 'object') {
                        if (typeof f.settings === 'string') { try { f.settings = JSON.parse(f.settings); } catch(e) { f.settings = {}; } }
                        if (!f.settings || typeof f.settings !== 'object') f.settings = {};
                        if (!Array.isArray(f.settings.steps) || f.settings.steps.length === 0) f.settings.steps = ['Step 1'];
                        if (typeof f.blocks === 'string') { try { f.blocks = JSON.parse(f.blocks); } catch(e) { f.blocks = []; } }
                        if (!Array.isArray(f.blocks)) f.blocks = [];
                        if (typeof f.logic === 'string') { try { f.logic = JSON.parse(f.logic); } catch(e) { f.logic = []; } }
                        if (!Array.isArray(f.logic)) f.logic = [];
                        if (typeof f.styling === 'string') { try { f.styling = JSON.parse(f.styling); } catch(e) { f.styling = {}; } }
                        if (!f.styling || typeof f.styling !== 'object') f.styling = {};
                    }
                });
                renderFormsList();
                updateMetrics();
                if (window.location.hash === '#funnel') {
                    populateFunnelSelector();
                    updateAdvancedFunnelData();
                }
            },
            error: function(err) {
                console.error('Cora: Failed to load forms', err);
                const body = document.getElementById('forms-list-body');
                if (body) {
                    body.innerHTML = `
                        <div class="col-span-full py-16 text-center">
                            <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.5" fill="none" class="mx-auto text-zinc-300 mb-3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <p class="text-xs text-zinc-500 mb-2">Could not load forms. Please refresh the page.</p>
                            <button onclick="fetchForms()" class="px-3 py-1.5 text-[11px] font-semibold text-white bg-zinc-950 rounded-lg hover:bg-zinc-800 transition-colors border-0 cursor-pointer">Retry</button>
                        </div>`;
                }
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

function updateFunnelLossBadge(elementId, originalVal, targetVal, stageLabel) {
        const badge = document.getElementById(elementId);
        if (!badge) return;
        
        if (originalVal <= 0) {
            badge.className = "px-2.5 py-1 rounded-md bg-zinc-50 text-zinc-500 text-[10px] font-medium border border-zinc-200/80 flex items-center gap-1.5 transition-all";
            badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span> 0% drop-off (awaiting traffic)`;
            return;
        }

        const lossCount = Math.max(0, originalVal - targetVal);
        const lossPct = Math.round((lossCount / originalVal) * 100);
        
        if (lossPct > 0) {
            badge.className = "px-2.5 py-1 rounded-md bg-amber-50/80 text-amber-800 text-[10px] font-medium border border-amber-200/80 flex items-center gap-1.5 transition-all";
            badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> -${lossPct}% drop-off (${lossCount} ${stageLabel || 'abandoned'})`;
        } else {
            badge.className = "px-2.5 py-1 rounded-md bg-emerald-50/80 text-emerald-700 text-[10px] font-medium border border-emerald-200/80 flex items-center gap-1.5 transition-all";
            badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 100% progressed smoothly (0 lost)`;
        }
    }

function renderCoraFunnelInsights(data) {
    const { views, started, completed, bounceRate, midFormDropoff, overallConversion, fieldStats, isAggregate, selectedId } = data;

    const heroBadge = document.getElementById('hero-decision-badge');
    const heroTitle = document.getElementById('hero-decision-title');
    const heroDesc = document.getElementById('hero-decision-desc');
    const heroActions = document.getElementById('hero-decision-actions');
    const actionsListEl = document.getElementById('funnel-ai-actions-list');
    const journeySummary = document.getElementById('journey-total-summary');
    const fieldHealthSummary = document.getElementById('field-health-status-summary');
    const frictionContainer = document.getElementById('funnel-friction-list');

    if (journeySummary) {
        journeySummary.textContent = `${overallConversion}% View-to-Lead`;
    }

    let badgeText = '';
    let badgeClass = '';
    let titleText = '';
    let descText = '';
    let ctaBtnHtml = '';
    let actionCards = [];

    const targetEditId = (selectedId && selectedId !== 'all') ? selectedId : (formsData && formsData.length > 0 ? formsData[0].id : null);
    const editHash = targetEditId ? `#edit/${targetEditId}` : '#new';

    if (views === 0 && started === 0) {
        badgeText = 'Awaiting Traffic';
        badgeClass = 'bg-zinc-100 text-zinc-700 border-zinc-200';
        titleText = 'Ready to collect client inquiries.';
        descText = 'Share your public link or embed this form on your website. Once visitors open it, you will see real-time lead conversion metrics here.';
        ctaBtnHtml = `<a href="${editHash}" class="px-3.5 py-2 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all inline-flex items-center gap-1.5 no-underline shadow-2xs"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Customize Form</a>`;

        actionCards = [
            {
                title: "Embed On Your Main Website",
                desc: "Place the form on your primary landing page or contact section for maximum visibility.",
                actionText: "Share / Embed",
                actionFn: "if(typeof openEmbedStudioDrawer==='function') openEmbedStudioDrawer();"
            },
            {
                title: "Keep Opening Screen Friendly",
                desc: "Ensure the form title is welcoming and the first question requires minimal effort to answer.",
                actionText: "Edit Title",
                actionFn: `window.location.hash='${editHash}';`
            },
            {
                title: "Test In Live Preview",
                desc: "Submit a sample test entry to verify your email notifications and CRM pipeline connection.",
                actionText: "Test Form",
                actionFn: "window.location.hash='#list';"
            }
        ];
    } else if (bounceRate > 50) {
        const bouncedCount = Math.max(0, views - started);
        badgeText = `Needs Attention • ${bounceRate}% Pre-Start Drop`;
        badgeClass = 'bg-amber-50 text-amber-900 border-amber-200/80';
        titleText = `${bouncedCount} out of ${views} visitors left before answering question #1.`;
        descText = `Visitors are opening your link but leaving immediately. Making your headline punchier or clarifying what they receive upon completion will bring you more responses.`;
        ctaBtnHtml = `<a href="${editHash}" class="px-3.5 py-2 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all inline-flex items-center gap-1.5 no-underline shadow-2xs"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Edit Opening Screen</a>`;

        actionCards = [
            {
                title: "Shorten Opening Headline",
                desc: "Keep the form title under 6 words so visitors immediately understand what the form is for.",
                actionText: "Edit Headline",
                actionFn: `window.location.hash='${editHash}';`
            },
            {
                title: "Make 1st Question Effortless",
                desc: "Start with a 1-click choice chip or dropdown instead of requiring a long text paragraph.",
                actionText: "Check Questions",
                actionFn: `window.location.hash='${editHash}';`
            },
            {
                title: "Split Into 2 Simple Steps",
                desc: "Group contact info into Step 2 so the first screen feels lightweight and inviting.",
                actionText: "Enable Steps",
                actionFn: `window.location.hash='${editHash}';`
            }
        ];
    } else if (midFormDropoff > 30) {
        const lostInForm = Math.max(0, started - completed);
        badgeText = `Mid-Form Drop-off • ${midFormDropoff}% Loss`;
        badgeClass = 'bg-amber-50 text-amber-900 border-amber-200/80';
        titleText = `${lostInForm} people started typing but abandoned before submitting.`;
        descText = `Visitors are interested, but they dropped off halfway through. Making secondary fields optional or turning on the step-by-step progress bar will recover these leads.`;
        ctaBtnHtml = `<a href="${editHash}" class="px-3.5 py-2 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all inline-flex items-center gap-1.5 no-underline shadow-2xs"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Streamline Questions</a>`;

        actionCards = [
            {
                title: "Make Non-Essential Fields Optional",
                desc: "Only require name and email/phone. Mark optional questions clearly so visitors aren't blocked.",
                actionText: "Review Required",
                actionFn: `window.location.hash='${editHash}';`
            },
            {
                title: "Turn On 2-Step Progress Stepper",
                desc: "A visual progress bar reassures respondents they are only 1 step away from finishing.",
                actionText: "Configure Steps",
                actionFn: `window.location.hash='${editHash}';`
            },
            {
                title: "Add Privacy Reassurance",
                desc: "Add a subtle note: 'We never spam or share your contact details.'",
                actionText: "Add Micro-copy",
                actionFn: `window.location.hash='${editHash}';`
            }
        ];
    } else {
        badgeText = `Converting Smoothly • ${overallConversion}% Conversion`;
        badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200/80';
        titleText = (completed === started && started > 0) ? '100% of respondents finished and submitted the form!' : `${completed} client leads collected efficiently.`;
        descText = `Your form is converting smoothly with near-zero hesitation. Scale your traffic to collect even more leads.`;
        ctaBtnHtml = `<button type="button" onclick="if(typeof openEmbedStudioDrawer==='function') openEmbedStudioDrawer();" class="px-3.5 py-2 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all inline-flex items-center gap-1.5 cursor-pointer shadow-2xs border-0"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><polyline points="16 6 12 2 8 6"></polyline><line x1="12" y1="2" x2="12" y2="15"></line></svg> Share / Embed Link</button>`;

        actionCards = [
            {
                title: "Share In Email Signatures & Bios",
                desc: "Your form converts reliably. Place the link in your team's email signatures and Instagram bio.",
                actionText: "Share Form",
                actionFn: "if(typeof openEmbedStudioDrawer==='function') openEmbedStudioDrawer();"
            },
            {
                title: "Test Value-Focused CTA Button",
                desc: "Try button text like 'Get My Custom Proposal' instead of generic 'Submit' to increase clicks.",
                actionText: "Edit Button",
                actionFn: `window.location.hash='${editHash}';`
            },
            {
                title: "Set Up Instant CRM Webhook",
                desc: "Receive instant notifications in Slack, WhatsApp, or Zapier whenever a new lead arrives.",
                actionText: "Webhooks",
                actionFn: `window.location.hash='${editHash}';`
            }
        ];
    }

    if (heroBadge) {
        heroBadge.className = `px-2.5 py-0.5 rounded-full text-[10px] font-bold border inline-flex items-center gap-1.5 ${badgeClass}`;
        heroBadge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-current"></span> ${badgeText}`;
    }
    if (heroTitle) heroTitle.textContent = titleText;
    if (heroDesc) heroDesc.textContent = descText;
    if (heroActions) heroActions.innerHTML = ctaBtnHtml;

    if (actionsListEl) {
        actionsListEl.innerHTML = actionCards.map(act => `
            <div class="bg-zinc-50/70 border border-zinc-200/70 rounded-xl p-3.5 flex items-center justify-between gap-3 shadow-2xs">
                <div class="flex flex-col gap-0.5 min-w-0 flex-1">
                    <span class="text-xs font-semibold text-zinc-900 truncate">${act.title}</span>
                    <p class="text-[11px] text-zinc-500 leading-relaxed">${act.desc}</p>
                </div>
                <button type="button" onclick="${act.actionFn}" class="shrink-0 h-8 px-3 rounded-lg bg-white border border-zinc-200 hover:bg-zinc-100 text-zinc-800 text-[11px] font-semibold transition-all cursor-pointer shadow-2xs whitespace-nowrap">
                    ${act.actionText}
                </button>
            </div>
        `).join('');
    }

    if (fieldHealthSummary && frictionContainer) {
        const genericTypes = ['short text', 'rich text', 'phone', 'checkboxes', 'dropdown', 'radio buttons', 'file upload', 'rating', 'scale', 'date', 'number', 'long text', 'question', 'text'];
        let displayStats = (fieldStats || []).filter(f => {
            const lower = (f.label || '').trim().toLowerCase();
            return !genericTypes.includes(lower);
        });
        if (displayStats.length === 0) displayStats = fieldStats || [];

        if (started === 0 || displayStats.length === 0) {
            fieldHealthSummary.textContent = 'Awaiting Responses';
            fieldHealthSummary.className = 'text-[10px] font-semibold text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded-full';
            frictionContainer.innerHTML = `
                <div class="text-center py-8 px-4 bg-zinc-50/50 rounded-xl border border-zinc-200/60">
                    <p class="text-xs font-semibold text-zinc-700 mb-0.5">No Question Hesitation Recorded</p>
                    <p class="text-[11px] text-zinc-400">Once visitors begin answering questions, individual completion rates will display here.</p>
                </div>`;
        } else if (midFormDropoff === 0 || completed === started) {
            fieldHealthSummary.textContent = 'All 100% Smooth';
            fieldHealthSummary.className = 'text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full';
            
            let html = `
                <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/70 mb-3 flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] shrink-0 font-bold">✓</span>
                    <p class="text-xs text-emerald-900 font-medium leading-relaxed">
                        <strong>Zero Question Friction!</strong> 100% of respondents who started finished every required question.
                    </p>
                </div>
            `;

            const maxVisible = 4;
            const topStats = displayStats.slice(0, maxVisible);
            const remainingStats = displayStats.slice(maxVisible);

            html += `<div class="space-y-2">`;
            html += topStats.map(fStat => `
                <div class="bg-zinc-50/70 border border-zinc-200/60 p-2.5 sm:p-3 rounded-xl flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[9px] shrink-0 font-bold">✓</span>
                        <span class="text-xs font-semibold text-zinc-800 truncate" title="${fStat.label}">${fStat.label}</span>
                    </div>
                    <span class="text-[11px] font-semibold text-emerald-700 shrink-0">100% finished</span>
                </div>
            `).join('');

            if (remainingStats.length > 0) {
                html += `
                    <div id="remaining-questions-list" class="hidden space-y-2 pt-1">
                        ${remainingStats.map(fStat => `
                            <div class="bg-zinc-50/70 border border-zinc-200/60 p-2.5 sm:p-3 rounded-xl flex items-center justify-between shadow-2xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[9px] shrink-0 font-bold">✓</span>
                                    <span class="text-xs font-semibold text-zinc-800 truncate" title="${fStat.label}">${fStat.label}</span>
                                </div>
                                <span class="text-[11px] font-semibold text-emerald-700 shrink-0">100% finished</span>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" id="btn-toggle-funnel-questions" onclick="window.coraToggleMoreFunnelQuestions(${remainingStats.length})" class="w-full py-2 px-3 mt-1.5 rounded-xl border border-zinc-200/80 bg-zinc-50/80 hover:bg-zinc-100 text-[11px] font-semibold text-zinc-700 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                        <span id="funnel-questions-toggle-text">+ ${remainingStats.length} more questions</span>
                        <svg id="funnel-questions-toggle-icon" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                `;
            }
            html += `</div>`;
            frictionContainer.innerHTML = html;
        } else {
            fieldHealthSummary.textContent = 'Drop-offs Detected';
            fieldHealthSummary.className = 'text-[10px] font-semibold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full';
            
            const maxVisible = 4;
            const topStats = displayStats.slice(0, maxVisible);
            const remainingStats = displayStats.slice(maxVisible);

            const renderDropoffItem = (fStat) => {
                const isFriction = fStat.rate < 70;
                const statusBadge = isFriction 
                    ? `<span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-50 border border-amber-200 text-amber-800 shrink-0">Hesitation Point (${100 - fStat.rate}% drop)</span>`
                    : `<span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-emerald-50 border border-emerald-100 text-emerald-700 shrink-0">Smooth (${fStat.rate}%)</span>`;

                return `
                    <div class="bg-zinc-50/70 border border-zinc-200/60 p-2.5 sm:p-3 rounded-xl flex flex-col gap-1.5 shadow-2xs">
                        <div class="flex items-center justify-between text-xs font-semibold text-zinc-800 gap-2">
                            <span class="truncate" title="${fStat.label}">${fStat.label}</span>
                            ${statusBadge}
                        </div>
                        <div class="h-1.5 w-full bg-zinc-200 rounded-full overflow-hidden">
                            <div class="h-full ${isFriction ? 'bg-amber-500' : 'bg-zinc-800'} transition-all duration-500" style="width: ${fStat.rate}%"></div>
                        </div>
                    </div>
                `;
            };

            let html = `<div class="space-y-2">`;
            html += topStats.map(renderDropoffItem).join('');

            if (remainingStats.length > 0) {
                html += `
                    <div id="remaining-questions-list" class="hidden space-y-2 pt-1">
                        ${remainingStats.map(renderDropoffItem).join('')}
                    </div>
                    <button type="button" id="btn-toggle-funnel-questions" onclick="window.coraToggleMoreFunnelQuestions(${remainingStats.length})" class="w-full py-2 px-3 mt-1.5 rounded-xl border border-zinc-200/80 bg-zinc-50/80 hover:bg-zinc-100 text-[11px] font-semibold text-zinc-700 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                        <span id="funnel-questions-toggle-text">+ ${remainingStats.length} more questions</span>
                        <svg id="funnel-questions-toggle-icon" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                `;
            }
            html += `</div>`;
            frictionContainer.innerHTML = html;
        }
    }
}

window.coraToggleMoreFunnelQuestions = function(count) {
    const list = document.getElementById('remaining-questions-list');
    const text = document.getElementById('funnel-questions-toggle-text');
    const icon = document.getElementById('funnel-questions-toggle-icon');
    if (!list) return;
    
    const isHidden = list.classList.contains('hidden');
    if (isHidden) {
        list.classList.remove('hidden');
        if (text) text.textContent = 'Show fewer questions';
        if (icon) icon.style.transform = 'rotate(180deg)';
    } else {
        list.classList.add('hidden');
        if (text) text.textContent = `+ ${count} more questions`;
        if (icon) icon.style.transform = 'rotate(0deg)';
    }
};

function updateAdvancedFunnelData() {
    const selector = document.getElementById('funnel-form-selector');
    if (!selector) return;
    const selectedId = selector.value;
    
    let totalSubmissions = 0;
    (formsData || []).forEach(f => {
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
                if (typeof submissions === 'string') {
                    try { submissions = JSON.parse(submissions); } catch(e) { submissions = []; }
                }
                if (!Array.isArray(submissions)) submissions = [];

                const started = submissions.length;
                const completed = submissions.filter(s => s.is_partial == '0').length;
                
                const views = totalSubmissions > 0 
                    ? Math.round(Math.max((formsData || []).length * 15, totalSubmissions * 1.5))
                    : (started > 0 ? Math.round(started * 1.5) : 0);

                const startedPct = views > 0 ? Math.round((started / views) * 100) : (started > 0 ? 100 : 0);
                const completedPct = started > 0 ? Math.round((completed / started) * 100) : 0;
                const overallConversion = views > 0 ? Math.round((completed / views) * 100) : (started > 0 ? completedPct : 0);
                const bounceRate = views > 0 ? Math.round(((views - started) / views) * 100) : 0;
                const midFormDropoff = started > 0 ? Math.round(((started - completed) / started) * 100) : 0;
                
                // Step 1
                const vEl = document.getElementById('funnel-metric-views');
                if (vEl) vEl.textContent = views;

                // Step 2
                const sEl = document.getElementById('funnel-metric-started');
                if (sEl) sEl.textContent = started;
                const sPctEl = document.getElementById('funnel-metric-started-pct');
                if (sPctEl) sPctEl.textContent = startedPct + '%';
                const sBar = document.getElementById('funnel-started-bar');
                if (sBar) sBar.style.width = startedPct + '%';
                const sSubEl = document.getElementById('funnel-metric-started-sub');
                if (sSubEl) sSubEl.textContent = (views - started > 0) ? `${views - started} left without starting.` : 'All visitors started typing.';

                // Step 3
                const cEl = document.getElementById('funnel-metric-completed');
                if (cEl) cEl.textContent = completed;
                const cPctEl = document.getElementById('funnel-metric-completed-pct');
                if (cPctEl) cPctEl.textContent = completedPct + '% finished';
                const cBar = document.getElementById('funnel-completed-bar');
                if (cBar) cBar.style.width = completedPct + '%';
                const cSubEl = document.getElementById('funnel-metric-completed-sub');
                if (cSubEl) cSubEl.textContent = (started - completed > 0) ? `${started - completed} abandoned before finish.` : '100% finished successfully!';

                // Field Stats
                let allInputBlocks = [];
                (formsData || []).forEach(form => {
                    const inputs = (form.blocks || []).filter(b => 
                        b.type !== 'header' && b.type !== 'paragraph' && b.type !== 'divider' && b.type !== 'page_break' && b.type !== 'stripe_payment'
                    );
                    allInputBlocks = allInputBlocks.concat(inputs);
                });

                const genericTypes = ['short text', 'rich text', 'phone', 'checkboxes', 'dropdown', 'radio buttons', 'file upload', 'rating', 'scale', 'date', 'number', 'long text', 'question', 'text'];
                let uniqueLabels = [...new Set(allInputBlocks.map(b => (b.label || '').trim()).filter(l => l))];
                let nonGenericLabels = uniqueLabels.filter(l => !genericTypes.includes(l.toLowerCase()));
                if (nonGenericLabels.length > 0) {
                    uniqueLabels = nonGenericLabels;
                }

                const fieldStats = uniqueLabels.map(label => {
                    let fillCount = 0;
                    let relevantForms = (formsData || []).filter(form => {
                        return (form.blocks || []).some(b => (b.label || '').trim() === label);
                    }).map(f => f.id);

                    let relevantSubmissions = submissions.filter(sub => relevantForms.includes(String(sub.form_id)) || relevantForms.includes(Number(sub.form_id)));
                    relevantSubmissions.forEach(sub => {
                        const val = sub.submitted_data ? sub.submitted_data[label] : undefined;
                        if (val !== undefined && val !== null && val !== '') fillCount++;
                    });
                    const relStarted = relevantSubmissions.length;
                    const rate = relStarted > 0 ? Math.round((fillCount / relStarted) * 100) : 100;
                    return { label, count: fillCount, rate, started: relStarted };
                });

                fieldStats.sort((a, b) => a.rate - b.rate);

                renderCoraFunnelInsights({ views, started, completed, bounceRate, midFormDropoff, overallConversion, fieldStats, isAggregate: true, selectedId: 'all' });
            }
        });
    } else {
        const formObj = (formsData || []).find(f => f.id == selectedId);
        if (!formObj) return;
        
        jQuery.ajax({
            url: getCoraRestUrl(`cora/v1/forms/${selectedId}/submissions`),
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(submissions) {
                if (typeof submissions === 'string') {
                    try { submissions = JSON.parse(submissions); } catch(e) { submissions = []; }
                }
                if (!Array.isArray(submissions)) submissions = [];

                const started = submissions.length;
                const completed = submissions.filter(s => s.is_partial == '0').length;
                const views = started > 0 ? Math.round(Math.max(12, started * 1.5)) : 0;
                
                const startedPct = views > 0 ? Math.round((started / views) * 100) : (started > 0 ? 100 : 0);
                const completedPct = started > 0 ? Math.round((completed / started) * 100) : 0;
                const overallConversion = views > 0 ? Math.round((completed / views) * 100) : (started > 0 ? completedPct : 0);
                const bounceRate = views > 0 ? Math.round(((views - started) / views) * 100) : 0;
                const midFormDropoff = started > 0 ? Math.round(((started - completed) / started) * 100) : 0;
                
                // Step 1
                const vEl = document.getElementById('funnel-metric-views');
                if (vEl) vEl.textContent = views;

                // Step 2
                const sEl = document.getElementById('funnel-metric-started');
                if (sEl) sEl.textContent = started;
                const sPctEl = document.getElementById('funnel-metric-started-pct');
                if (sPctEl) sPctEl.textContent = startedPct + '%';
                const sBar = document.getElementById('funnel-started-bar');
                if (sBar) sBar.style.width = startedPct + '%';
                const sSubEl = document.getElementById('funnel-metric-started-sub');
                if (sSubEl) sSubEl.textContent = (views - started > 0) ? `${views - started} left without starting.` : 'All visitors started typing.';

                // Step 3
                const cEl = document.getElementById('funnel-metric-completed');
                if (cEl) cEl.textContent = completed;
                const cPctEl = document.getElementById('funnel-metric-completed-pct');
                if (cPctEl) cPctEl.textContent = completedPct + '% finished';
                const cBar = document.getElementById('funnel-completed-bar');
                if (cBar) cBar.style.width = completedPct + '%';
                const cSubEl = document.getElementById('funnel-metric-completed-sub');
                if (cSubEl) cSubEl.textContent = (started - completed > 0) ? `${started - completed} abandoned before finish.` : '100% finished successfully!';

                // Field Stats
                const inputBlocks = (formObj.blocks || []).filter(b => 
                    b.type !== 'header' && b.type !== 'paragraph' && b.type !== 'divider' && b.type !== 'page_break' && b.type !== 'stripe_payment'
                );

                const fieldStats = inputBlocks.map(b => {
                    let fillCount = 0;
                    submissions.forEach(sub => {
                        const val = sub.submitted_data ? sub.submitted_data[b.label] : undefined;
                        if (val !== undefined && val !== null && val !== '') fillCount++;
                    });
                    const rate = started > 0 ? Math.round((fillCount / started) * 100) : 100;
                    return { label: b.label || 'Question', count: fillCount, rate, started };
                });

                fieldStats.sort((a, b) => a.rate - b.rate);

                renderCoraFunnelInsights({ views, started, completed, bounceRate, midFormDropoff, overallConversion, fieldStats, isAggregate: false, selectedId });
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

    let currentSubmissionsList = [];
    let currentSubmissionsFormTitle = 'Form Submissions';

    function renderSubmissionsTable(filterText = '') {
        const content = document.getElementById('submissions-drawer-content');
        if (!content) return;

        let filtered = currentSubmissionsList;
        if (filterText && filterText.trim() !== '') {
            const q = filterText.toLowerCase().trim();
            filtered = currentSubmissionsList.filter(sub => {
                if ((sub.ip_address || '').toLowerCase().includes(q)) return true;
                if ((sub.created_at || '').toLowerCase().includes(q)) return true;
                const dataStr = JSON.stringify(sub.submitted_data || {}).toLowerCase();
                return dataStr.includes(q);
            });
        }

        if (filtered.length === 0) {
            content.innerHTML = `<div class="text-xs text-zinc-400 text-center py-16">${filterText ? 'No matching submission entries found.' : 'No submissions recorded for this form yet.'}</div>`;
            return;
        }

        let html = `
            <div class="overflow-x-auto rounded-xl border border-zinc-200/80 bg-white">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-zinc-400 font-semibold bg-zinc-50/70">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">IP Address</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Submitted At</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
        `;

        filtered.forEach((sub, idx) => {
            const originalIdx = currentSubmissionsList.indexOf(sub);
            const label = sub.is_partial == '1' ? 'Partial' : 'Completed';
            const badgeClass = sub.is_partial == '1' 
                ? 'bg-zinc-100 text-zinc-650' 
                : 'bg-emerald-50 text-emerald-700';
            
            html += `
                <tr class="hover:bg-zinc-50/60 transition-all">
                    <td class="px-4 py-3.5 font-semibold text-zinc-900">Entry #${currentSubmissionsList.length - (originalIdx !== -1 ? originalIdx : idx)}</td>
                    <td class="px-4 py-3.5 font-mono text-zinc-500">${sub.ip_address || 'Unknown'}</td>
                    <td class="px-4 py-3.5">
                        <span class="px-2.5 py-0.5 rounded text-[9px] font-bold uppercase ${badgeClass}">${label}</span>
                    </td>
                    <td class="px-4 py-3.5 text-zinc-500">${sub.created_at}</td>
                    <td class="px-4 py-3.5 text-right">
                        <button class="btn-inspect-entry h-7 px-2.5 rounded-lg border border-zinc-200 hover:border-zinc-300 bg-white text-zinc-600 hover:text-zinc-950 cursor-pointer transition-all" data-idx="${originalIdx !== -1 ? originalIdx : idx}">
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

        content.innerHTML = html;

        // Attach entry inspectors click
        jQuery('.btn-inspect-entry').off('click').on('click', function() {
            const idx = jQuery(this).data('idx');
            const sub = currentSubmissionsList[idx];
            if (sub) {
                openEntryInspector(sub, currentSubmissionsList.length - idx);
            }
        });
    }

    window.openSubmissionsDrawer = function(formId) {
        const formObj = formsData.find(f => f.id == formId);
        if (!formObj) return;

        currentSubmissionsFormTitle = formObj.title;
        const titleEl = document.getElementById('drawer-form-title');
        if (titleEl) titleEl.textContent = formObj.title;
        
        const searchInput = document.getElementById('submissions-search-input');
        if (searchInput) searchInput.value = '';

        // Show backdrop and bottom sheet drawer
        const backdrop = document.getElementById('cora-submissions-backdrop');
        const drawer = document.getElementById('cora-submissions-drawer');
        if (backdrop) {
            backdrop.classList.remove('hidden', 'pointer-events-none');
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100', 'pointer-events-auto');
            });
        }
        if (drawer) {
            drawer.classList.remove('hidden', 'pointer-events-none');
            drawer.classList.add('pointer-events-auto');
            drawer.offsetHeight;
            drawer.classList.remove('translate-y-full');
            drawer.classList.add('translate-y-0');
        }

        const content = document.getElementById('submissions-drawer-content');
        if (content) content.innerHTML = '<div class="text-xs text-zinc-400 text-center py-16">Loading submissions...</div>';

        jQuery.ajax({
            url: getCoraRestUrl(`cora/v1/forms/${formId}/submissions`),
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpNonce);
            },
            success: function(submissions) {
                currentSubmissionsList = submissions || [];
                const countEl = document.getElementById('drawer-responses-count');
                if (countEl) countEl.textContent = currentSubmissionsList.length + ' Entries';
                renderSubmissionsTable('');
            },
            error: function() {
                if (content) content.innerHTML = '<div class="text-xs text-red-500 text-center py-16">Failed to load submissions.</div>';
            }
        });
    };

    window.closeSubmissionsDrawer = function() {
        const backdrop = document.getElementById('cora-submissions-backdrop');
        const drawer = document.getElementById('cora-submissions-drawer');
        const inspector = document.getElementById('cora-entry-inspector');
        
        if (drawer) {
            drawer.classList.remove('translate-y-0', 'pointer-events-auto');
            drawer.classList.add('translate-y-full', 'pointer-events-none');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 350);
        }
        if (backdrop) {
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                backdrop.classList.add('hidden');
            }, 300);
        }
        if (inspector) {
            inspector.classList.remove('pointer-events-auto');
            inspector.classList.add('translate-x-full', 'pointer-events-none');
            setTimeout(() => {
                inspector.classList.add('hidden');
            }, 300);
        }
    };

    // Live search in submissions
    jQuery(document).on('input', '#submissions-search-input', function() {
        renderSubmissionsTable(jQuery(this).val());
    });

    // Export CSV handler
    jQuery(document).on('click', '#btn-export-submissions-csv', function() {
        if (!currentSubmissionsList || currentSubmissionsList.length === 0) {
            if (window.coraShowToast) window.coraShowToast('No submissions available to export.', 'info');
            return;
        }

        const allFieldKeys = new Set();
        currentSubmissionsList.forEach(s => {
            if (s.submitted_data && typeof s.submitted_data === 'object') {
                Object.keys(s.submitted_data).forEach(k => allFieldKeys.add(k));
            }
        });
        const fieldKeyArr = Array.from(allFieldKeys);

        const headers = ['Entry ID', 'Status', 'Submitted At', 'IP Address', ...fieldKeyArr];
        const rows = [headers];

        currentSubmissionsList.forEach((sub, idx) => {
            const entryId = `Entry #${currentSubmissionsList.length - idx}`;
            const status = sub.is_partial == '1' ? 'Partial' : 'Completed';
            const submittedAt = sub.created_at || '';
            const ip = sub.ip_address || '';
            const data = sub.submitted_data || {};
            const fieldVals = fieldKeyArr.map(k => {
                let v = data[k];
                if (v === undefined || v === null) return '';
                if (typeof v === 'object') return JSON.stringify(v);
                return String(v).replace(/"/g, '""');
            });
            rows.push([entryId, status, submittedAt, ip, ...fieldVals]);
        });

        const csvContent = "data:text/csv;charset=utf-8," + rows.map(r => r.map(c => `"${c}"`).join(",")).join("\n");
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        const safeTitle = (currentSubmissionsFormTitle || 'form').replace(/[^a-z0-9]/gi, '_').toLowerCase();
        link.setAttribute("download", `${safeTitle}_submissions_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        if (window.coraShowToast) window.coraShowToast('Exported submissions to CSV successfully.', 'success');
    });

    // Global ESC key listener for modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const drawer = document.getElementById('cora-submissions-drawer');
            if (drawer && !drawer.classList.contains('hidden') && !drawer.classList.contains('translate-y-full')) {
                closeSubmissionsDrawer();
            }
        }
    });

    window.openEntryInspector = function(sub, entryNumber) {
        const idEl = document.getElementById('inspector-entry-id');
        if (idEl) idEl.textContent = `Entry #${entryNumber}`;
        const timeEl = document.getElementById('inspector-submitted-at');
        if (timeEl) timeEl.textContent = `Submitted ${sub.created_at} (${sub.ip_address || 'no IP'})`;
        
        const badge = document.getElementById('inspector-status-badge');
        if (badge) {
            if (sub.is_partial == '1') {
                badge.textContent = 'Partial';
                badge.className = 'px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-zinc-100 text-zinc-650';
            } else {
                badge.textContent = 'Completed';
                badge.className = 'px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700';
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
            setTimeout(() => {
                inspector.classList.add('hidden');
            }, 300);
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
                    <button class="btn-edit-ai-mobile sm:hidden h-8 flex-1 rounded-lg bg-zinc-950 text-white text-[11px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer border-0" data-id="${form.id}" title="Edit Form with AI">
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
        jQuery('.btn-edit-ai-mobile').on('click', function() {
            const id = jQuery(this).data('id');
            const formObj = (formsData || []).find(f => f.id == id);
            const title = (formObj && formObj.title) ? formObj.title : 'Form #' + id;
            if (typeof window.coraPromptFormAI === 'function') {
                window.coraPromptFormAI(id, title);
            }
        });
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
            if (formObj) {
                openEmbedStudioDrawer(formObj);
            }
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
        if (settingsTabContent) { settingsTabContent.classList.add('hidden'); settingsTabContent.classList.remove('flex'); }
        
        const activeTabKey = hash.replace('#', '') || 'list';
        document.querySelectorAll('.cora-sub-tabs-container .cora-sub-tab').forEach(t => {
            const isTarget = t.getAttribute('data-target') === activeTabKey;
            const isDropdownItem = t.closest('#mobile-tabs-more-dropdown');
            if (isDropdownItem) {
                if (isTarget) {
                    t.classList.add('active', 'bg-zinc-50', 'text-zinc-950', 'font-semibold');
                    t.classList.remove('text-zinc-650', 'hover:bg-zinc-50', 'font-medium');
                } else {
                    t.classList.remove('active', 'bg-zinc-50', 'text-zinc-950', 'font-semibold');
                    t.classList.add('text-zinc-650', 'hover:bg-zinc-50', 'font-medium');
                }
            } else {
                if (isTarget) {
                    t.classList.add('active', 'border-zinc-950', 'text-zinc-950', 'font-semibold');
                    t.classList.remove('border-transparent', 'text-zinc-550', 'hover:text-zinc-900', 'font-medium');
                } else {
                    t.classList.remove('active', 'border-zinc-950', 'text-zinc-950', 'font-semibold');
                    t.classList.add('border-transparent', 'text-zinc-550', 'hover:text-zinc-900', 'font-medium');
                }
            }
        });

        if (hash === '#list') {
            if (listTabContent) { listTabContent.classList.remove('hidden'); listTabContent.classList.add('flex'); }
            if (listState) { listState.classList.remove('hidden'); listState.classList.add('flex'); }
            fetchForms();
        } else if (hash === '#funnel') {
            if (funnelTabContent) { funnelTabContent.classList.remove('hidden'); funnelTabContent.classList.add('flex'); }
            if (listState) { listState.classList.remove('hidden'); listState.classList.add('flex'); }
            if (!formsData || formsData.length === 0) {
                fetchForms();
            } else {
                populateFunnelSelector();
                updateAdvancedFunnelData();
            }
        } else if (hash === '#clauses') {
            window.location.hash = '#list';
            return;
        } else if (hash === '#audit-log') {
            if (auditTabContent) { auditTabContent.classList.remove('hidden'); auditTabContent.classList.add('flex'); }
            if (listState) { listState.classList.remove('hidden'); listState.classList.add('flex'); }
            fetchAuditLogs();
        } else if (hash === '#settings') {
            if (settingsTabContent) { settingsTabContent.classList.remove('hidden'); settingsTabContent.classList.add('flex'); }
            if (listState) { listState.classList.remove('hidden'); listState.classList.add('flex'); }
            if (typeof loadFormsGlobalSettings === 'function') {
                loadFormsGlobalSettings();
            }
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
        if (!container || !currentEditingForm) return;
        if (!Array.isArray(currentEditingForm.blocks)) currentEditingForm.blocks = [];
        if (!currentEditingForm.settings) currentEditingForm.settings = {};
        
        // Compute steps dynamically from page_break blocks
        const pageBreaks = currentEditingForm.blocks.filter(b => b.type === 'page_break');
        const stepCount = pageBreaks.length + 1;
        const steps = [];
        for (let i = 1; i <= stepCount; i++) {
            steps.push(`Step ${i}`);
        }
        currentEditingForm.settings.steps = steps;

        let html = '';
        steps.forEach((step, idx) => {
            const active = (currentEditingForm.currentStepIndex || 0) === idx;
            const borderClasses = active 
                ? 'border-2 border-zinc-950 bg-white' 
                : 'border border-zinc-200 bg-white hover:border-zinc-400';
            
            html += `
                <div class="step-tab-wrapper inline-flex items-center rounded-lg ${borderClasses} bg-white shadow-2xs overflow-hidden transition-all shrink-0">
                    <button type="button" class="step-tab-btn h-8 pl-3 pr-2.5 text-xs flex items-center gap-1.5 cursor-pointer bg-transparent border-0 outline-none select-none" data-step-idx="${idx}">
                        <span class="w-4 h-4 rounded ${active ? 'bg-zinc-950 text-white' : 'bg-zinc-100 text-zinc-700'} text-[10px] flex items-center justify-center font-bold">${idx + 1}</span>
                        <span class="${active ? 'font-bold text-zinc-950' : 'font-medium text-zinc-600'}">${step}</span>
                    </button>
                    ${idx > 0 ? `
                        <button type="button" class="step-delete-btn h-8 w-7 text-zinc-400 hover:text-red-600 hover:bg-red-50 text-xs font-bold cursor-pointer transition-colors bg-transparent border-0 border-l border-zinc-200 outline-none flex items-center justify-center" data-step-idx="${idx}" title="Remove Step ${idx + 1}">✕</button>
                    ` : ''}
                </div>
            `;
        });
        html += `
            <button id="btn-add-step" type="button" class="h-8 px-3 rounded-lg border border-dashed border-zinc-300 text-zinc-500 hover:text-zinc-900 hover:border-zinc-400 text-xs font-semibold flex items-center gap-1 shrink-0 cursor-pointer transition-all bg-transparent outline-none">
                <span>+</span> Add Step
            </button>
        `;
        container.innerHTML = html;

        // Use direct delegated event handling on container for 100% click reliability
        container.onclick = function(e) {
            const delBtn = e.target.closest('.step-delete-btn');
            if (delBtn) {
                e.preventDefault();
                e.stopPropagation();
                const deleteIdx = parseInt(delBtn.dataset.stepIdx);
                if (isNaN(deleteIdx) || deleteIdx <= 0) return;

                // Find the deleteIdx-th page_break block in currentEditingForm.blocks
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
                } else {
                    // Fallback: remove the last page_break if count was somehow out of sync
                    for (let i = currentEditingForm.blocks.length - 1; i >= 0; i--) {
                        if (currentEditingForm.blocks[i].type === 'page_break') {
                            currentEditingForm.blocks.splice(i, 1);
                            break;
                        }
                    }
                }

                // Recalculate remaining steps
                const remainingBreaks = currentEditingForm.blocks.filter(b => b.type === 'page_break');
                const remainingStepsCount = remainingBreaks.length + 1;
                currentEditingForm.settings.steps = Array.from({ length: remainingStepsCount }, (_, i) => `Step ${i + 1}`);

                if (currentEditingForm.currentStepIndex >= remainingStepsCount) {
                    currentEditingForm.currentStepIndex = Math.max(0, remainingStepsCount - 1);
                } else if (currentEditingForm.currentStepIndex >= deleteIdx) {
                    currentEditingForm.currentStepIndex = Math.max(0, currentEditingForm.currentStepIndex - 1);
                }

                renderStepsBar();
                renderEditorBlocks();
                triggerAutoSave();
                window.coraShowToast && window.coraShowToast(`Step ${deleteIdx + 1} removed.`, 'info');
                return;
            }

            const tabBtn = e.target.closest('.step-tab-btn');
            if (tabBtn) {
                e.preventDefault();
                const stepIdx = parseInt(tabBtn.dataset.stepIdx);
                if (!isNaN(stepIdx)) {
                    currentEditingForm.currentStepIndex = stepIdx;
                    renderStepsBar();
                    renderEditorBlocks();
                }
                return;
            }

            const addBtn = e.target.closest('#btn-add-step');
            if (addBtn) {
                e.preventDefault();
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
                window.coraShowToast && window.coraShowToast(`Step ${pBreaks.length + 1} added`, 'success');
                return;
            }
        };
    }

    function switchLeftTab(tab) {
        const tabs = ['fields', 'settings', 'style', 'form', 'integ'];
        tabs.forEach(t => {
            const contentEl = document.getElementById(`left-tab-${t}`);
            const btnEl = document.getElementById(`btn-left-tab-${t}`);
            if (t === tab) {
                if (contentEl) contentEl.classList.remove('hidden');
                if (btnEl) {
                    btnEl.className = "py-1.5 px-0.5 rounded-lg text-[10px] font-bold bg-white text-zinc-950 shadow-2xs flex flex-col items-center justify-center gap-1 cursor-pointer transition-all border-0 outline-none";
                }
            } else {
                if (contentEl) contentEl.classList.add('hidden');
                if (btnEl) {
                    btnEl.className = "py-1.5 px-0.5 rounded-lg text-[10px] font-medium text-zinc-500 hover:text-zinc-900 flex flex-col items-center justify-center gap-1 cursor-pointer transition-all bg-transparent border-0 outline-none";
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
                if (typeof form === 'string') {
                    try { form = JSON.parse(form); } catch(e) { form = null; }
                }
                if (!form || typeof form !== 'object' || form.code) {
                    window.coraShowToast && window.coraShowToast((form && form.message) || "Form not found or failed to load.", "error");
                    const overlay = document.getElementById('forms-loading-overlay');
                    if (overlay) overlay.remove();
                    if (listState) { listState.classList.remove('hidden'); }
                    if (editorState) { editorState.classList.add('hidden'); editorState.classList.remove('flex'); }
                    window.location.hash = '#list';
                    return;
                }

                if (typeof form.settings === 'string') {
                    try { form.settings = JSON.parse(form.settings); } catch(e) { form.settings = {}; }
                }
                if (!form.settings || typeof form.settings !== 'object') form.settings = {};

                if (typeof form.blocks === 'string') {
                    try { form.blocks = JSON.parse(form.blocks); } catch(e) { form.blocks = []; }
                }
                if (!Array.isArray(form.blocks)) form.blocks = [];

                if (typeof form.logic === 'string') {
                    try { form.logic = JSON.parse(form.logic); } catch(e) { form.logic = []; }
                }
                if (!Array.isArray(form.logic)) form.logic = [];

                if (typeof form.styling === 'string') {
                    try { form.styling = JSON.parse(form.styling); } catch(e) { form.styling = {}; }
                }
                if (!form.styling || typeof form.styling !== 'object') form.styling = {};

                if (!Array.isArray(form.settings.steps) || form.settings.steps.length === 0) {
                    form.settings.steps = ['Step 1'];
                }

                currentEditingForm = form;
                currentEditingForm.currentStepIndex = 0;

                // ── Remove loading overlay immediately so UI never gets stuck ──
                const loadingOverlay = document.getElementById('forms-loading-overlay');
                if (loadingOverlay) loadingOverlay.remove();

                // Transition: hide list, show editor
                if (listState) listState.classList.add('hidden');
                if (editorState) { editorState.classList.remove('hidden'); editorState.classList.add('flex'); }

                syncUIToForm(currentEditingForm);

                // Render editor – wrapped so a crash doesn't leave user stuck
                try {
                    if (!checkAndRestoreFormBuilderDraft(id)) {
                        renderCoverImage();
                        renderEditorBlocks();
                        renderStepsBar();
                        switchEditorView('build');
                        switchLeftTab('fields');
                        if (typeof renderLogicRules === 'function') renderLogicRules();
                    } else {
                        switchEditorView('build');
                        switchLeftTab('fields');
                    }
                    window._formIsDirty = false;
                    updatePublishButtonState(false);
                } catch(renderErr) {
                    console.error('Render error:', renderErr);
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

                // Sync styling controls to UI and canvas
                syncStylingControlsToUI(form.styling || {});
    }

    function syncStylingControlsToUI(styling) {
        if (!styling) styling = {};
        const s = Object.assign({
            theme: 'light',
            transparent_bg: false,
            bg_color: '#FAFAFA',
            card_bg_color: '#FFFFFF',
            accent_color: '#09090B',
            card_style: 'bordered',
            border_radius: 'md',
            font_family: 'sans',
            density: 'normal',
            show_branding: true,
            custom_css: ''
        }, styling);

        // Update active preset button
        document.querySelectorAll('.btn-theme-preset').forEach(btn => {
            const p = btn.dataset.preset;
            if (p === s.theme) {
                btn.className = "btn-theme-preset p-2.5 rounded-xl border border-zinc-950 bg-zinc-950 text-white text-left flex flex-col gap-1 transition-all cursor-pointer shadow-xs";
            } else {
                let bgClass = 'bg-white text-zinc-900';
                if (p === 'dark') bgClass = 'bg-zinc-900 text-white';
                else if (p === 'cream') bgClass = 'bg-[#FAF7F2] text-zinc-900';
                else if (p === 'slate') bgClass = 'bg-slate-50 text-slate-900';
                btn.className = `btn-theme-preset p-2.5 rounded-xl border border-zinc-200 ${bgClass} text-left flex flex-col gap-1 transition-all cursor-pointer hover:border-zinc-400`;
            }
        });

        // Transparent toggle
        const transInp = document.getElementById('style-bg-transparent');
        if (transInp) transInp.checked = !!s.transparent_bg;
        const solidBgWrap = document.getElementById('style-solid-bg-wrapper');
        if (solidBgWrap) {
            if (s.transparent_bg) solidBgWrap.classList.add('opacity-40', 'pointer-events-none');
            else solidBgWrap.classList.remove('opacity-40', 'pointer-events-none');
        }

        // Color pickers & hex inputs
        const bgPicker = document.getElementById('style-bg-color-picker');
        const bgHex = document.getElementById('style-bg-color-hex');
        if (bgPicker) bgPicker.value = s.bg_color || '#FAFAFA';
        if (bgHex) bgHex.value = (s.bg_color || '#FAFAFA').toUpperCase();

        const cardPicker = document.getElementById('style-card-bg-picker');
        const cardHex = document.getElementById('style-card-bg-hex');
        if (cardPicker) cardPicker.value = s.card_bg_color || '#FFFFFF';
        if (cardHex) cardHex.value = (s.card_bg_color || '#FFFFFF').toUpperCase();

        const accentPicker = document.getElementById('style-accent-picker');
        const accentHex = document.getElementById('style-accent-hex');
        if (accentPicker) accentPicker.value = s.accent_color || '#09090B';
        if (accentHex) accentHex.value = (s.accent_color || '#09090B').toUpperCase();

        // Card style buttons
        document.querySelectorAll('.btn-card-style').forEach(btn => {
            const cs = btn.dataset.style;
            if (cs === s.card_style) {
                btn.className = "btn-card-style py-2 px-1 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10.5px] font-bold transition-all cursor-pointer shadow-2xs";
            } else {
                btn.className = "btn-card-style py-2 px-1 rounded-lg border border-zinc-200 bg-white text-zinc-700 hover:text-zinc-950 text-center text-[10.5px] font-medium transition-all cursor-pointer";
            }
        });

        // Radius buttons
        document.querySelectorAll('.btn-radius').forEach(btn => {
            const r = btn.dataset.radius;
            if (r === s.border_radius) {
                btn.className = "btn-radius py-1.5 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10px] font-bold transition-all cursor-pointer";
            } else {
                btn.className = "btn-radius py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10px] font-medium transition-all cursor-pointer";
            }
        });

        // Font buttons
        document.querySelectorAll('.btn-font').forEach(btn => {
            const f = btn.dataset.font;
            const fontClass = f === 'mono' ? 'font-mono' : (f === 'serif' ? 'font-serif' : 'font-sans');
            if (f === s.font_family) {
                btn.className = `btn-font py-2 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10.5px] font-bold ${fontClass} transition-all cursor-pointer`;
            } else {
                btn.className = `btn-font py-2 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10.5px] ${fontClass} font-medium transition-all cursor-pointer`;
            }
        });

        // Density buttons
        document.querySelectorAll('.btn-density').forEach(btn => {
            const d = btn.dataset.density;
            if (d === s.density) {
                btn.className = "btn-density py-1.5 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10.5px] font-bold transition-all cursor-pointer";
            } else {
                btn.className = "btn-density py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10.5px] font-medium transition-all cursor-pointer";
            }
        });

        // Branding toggle
        const brandInp = document.getElementById('style-show-branding');
        if (brandInp) brandInp.checked = s.show_branding !== false;

        // Custom CSS
        const cssInp = document.getElementById('style-custom-css');
        if (cssInp) cssInp.value = s.custom_css || '';

        applyStylingToCanvas();
    }

    function applyStylingToCanvas() {
        if (!currentEditingForm) return;
        const s = Object.assign({
            theme: 'light',
            transparent_bg: false,
            bg_color: '#FAFAFA',
            card_bg_color: '#FFFFFF',
            accent_color: '#09090B',
            card_style: 'bordered',
            border_radius: 'md',
            font_family: 'sans',
            density: 'normal',
            show_branding: true,
            custom_css: ''
        }, currentEditingForm.styling || {});

        const canvasCenter = document.getElementById('editor-center-canvas');
        const canvasSheet = document.getElementById('editor-document-sheet');
        const submitBtn = document.getElementById('canvas-submit-btn');

        // Font family
        if (canvasSheet) {
            canvasSheet.style.fontFamily = s.font_family === 'mono' 
                ? 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace'
                : (s.font_family === 'serif' ? 'ui-serif, Georgia, Cambria, "Times New Roman", Times, serif' : 'inherit');
        }

        // Canvas container background
        if (canvasCenter) {
            if (s.transparent_bg) {
                canvasCenter.style.backgroundImage = 'linear-gradient(45deg, #e4e4e7 25%, transparent 25%), linear-gradient(-45deg, #e4e4e7 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e4e4e7 75%), linear-gradient(-45deg, transparent 75%, #e4e4e7 75%)';
                canvasCenter.style.backgroundSize = '20px 20px';
                canvasCenter.style.backgroundPosition = '0 0, 0 10px, 10px -10px, -10px 0px';
                canvasCenter.style.backgroundColor = '#f4f4f5';
            } else {
                canvasCenter.style.backgroundImage = 'none';
                canvasCenter.style.backgroundColor = s.bg_color || '#FAFAFA';
            }
        }

        // Canvas sheet card styling
        if (canvasSheet) {
            // Card background
            if (s.transparent_bg) {
                canvasSheet.style.backgroundColor = 'transparent';
            } else {
                canvasSheet.style.backgroundColor = s.card_bg_color || '#FFFFFF';
            }

            // Framing / Border / Shadow
            if (s.card_style === 'borderless') {
                canvasSheet.style.border = 'none';
                canvasSheet.style.boxShadow = 'none';
            } else if (s.card_style === 'elevated') {
                canvasSheet.style.border = 'none';
                canvasSheet.style.boxShadow = '0 10px 30px -5px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04)';
            } else {
                // bordered
                canvasSheet.style.border = s.theme === 'dark' ? '1px solid #27272a' : '1px solid #e4e4e7';
                canvasSheet.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
            }

            // Corner radius
            if (s.border_radius === 'none') {
                canvasSheet.style.borderRadius = '0px';
            } else if (s.border_radius === 'sm') {
                canvasSheet.style.borderRadius = '8px';
            } else if (s.border_radius === 'pill') {
                canvasSheet.style.borderRadius = '24px';
            } else {
                canvasSheet.style.borderRadius = '16px';
            }

            // Text colors for dark vs light
            const formName = document.getElementById('canvas-form-name');
            const formSub = document.getElementById('canvas-form-subtitle');
            if (s.theme === 'dark') {
                if (formName) formName.style.color = '#FFFFFF';
                if (formSub) formSub.style.color = '#A1A1AA';
            } else {
                if (formName) formName.style.color = '#09090B';
                if (formSub) formSub.style.color = '#71717A';
            }
        }

        // Primary submit button accent color & radius
        if (submitBtn) {
            submitBtn.style.backgroundColor = s.accent_color || '#09090B';
            submitBtn.style.color = '#FFFFFF';
            if (s.border_radius === 'none') submitBtn.style.borderRadius = '0px';
            else if (s.border_radius === 'sm') submitBtn.style.borderRadius = '6px';
            else if (s.border_radius === 'pill') submitBtn.style.borderRadius = '9999px';
            else submitBtn.style.borderRadius = '12px';
        }
    }

    function checkAndRestoreFormBuilderDraft(targetId) {
        if (typeof window.coraAutoSave !== 'undefined') {
            const draftStr = localStorage.getItem('cora_draft_form_builder_draft');
            if (draftStr) {
                try {
                    const draft = JSON.parse(draftStr);
                    let draftForm = typeof draft.data === 'string' ? JSON.parse(draft.data) : draft.data;
                    if (draftForm && draftForm.id == targetId) {
                        if (typeof draftForm.settings === 'string') { try { draftForm.settings = JSON.parse(draftForm.settings); } catch(e) { draftForm.settings = {}; } }
                        if (!draftForm.settings || typeof draftForm.settings !== 'object') draftForm.settings = {};
                        if (!Array.isArray(draftForm.settings.steps) || draftForm.settings.steps.length === 0) draftForm.settings.steps = ['Step 1'];
                        if (typeof draftForm.blocks === 'string') { try { draftForm.blocks = JSON.parse(draftForm.blocks); } catch(e) { draftForm.blocks = []; } }
                        if (!Array.isArray(draftForm.blocks)) draftForm.blocks = [];
                        if (typeof draftForm.logic === 'string') { try { draftForm.logic = JSON.parse(draftForm.logic); } catch(e) { draftForm.logic = []; } }
                        if (!Array.isArray(draftForm.logic)) draftForm.logic = [];
                        if (typeof draftForm.styling === 'string') { try { draftForm.styling = JSON.parse(draftForm.styling); } catch(e) { draftForm.styling = {}; } }
                        if (!draftForm.styling || typeof draftForm.styling !== 'object') draftForm.styling = {};

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
            rows: type === 'matrix' ? ['Service Quality', 'Speed of Service', 'Overall Value'] : undefined,
            columns: type === 'matrix' ? ['Poor', 'Average', 'Excellent'] : undefined,
            items: type === 'repeatable' ? [''] : undefined,
            content: type === 'rich_text' ? 'Enter rich text instructions or announcement...' : undefined,
            min: type === 'slider' ? 0 : undefined,
            max: type === 'slider' ? 100 : undefined,
            step: type === 'slider' ? 1 : undefined,
            default_value: type === 'slider' ? 50 : (type === 'hidden' ? '' : undefined),
            param_name: type === 'hidden' ? 'ref' : undefined,
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
            if (['text','email','phone','number'].includes(block.type)) previewHtml = `<input type="text" class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50/50 text-xs" placeholder="${block.placeholder || 'Placeholder...'}" disabled />`;
            else if (block.type === 'hidden') previewHtml = `
                <div class="flex items-center gap-2 p-2.5 rounded-xl border border-dashed border-zinc-300 bg-zinc-50/70 text-zinc-500 text-xs">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    <span>Hidden Field &bull; Param: <code class="font-mono text-zinc-700 bg-zinc-200/60 px-1 py-0.5 rounded text-[10px]">${block.param_name || 'ref'}</code> (Not visible to end user)</span>
                </div>
            `;
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
            else if (block.type === 'rich_text') previewHtml = `
                <div class="border border-zinc-200 rounded-xl bg-white overflow-hidden">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-zinc-50 border-b border-zinc-200 text-zinc-400 text-[10px] select-none font-bold">
                        <span class="text-zinc-700">B</span>
                        <span class="italic font-serif text-zinc-700">I</span>
                        <span class="underline text-zinc-700">U</span>
                        <span class="text-zinc-300">|</span>
                        <span>Link</span>
                        <span>List</span>
                    </div>
                    <div class="p-3 text-xs text-zinc-700 leading-relaxed">
                        <p>${block.content || 'Rich text instructions or formatted announcement...'}</p>
                    </div>
                </div>
            `;
            else if (block.type === 'matrix') {
                const mRows = block.rows || ['Service Quality', 'Speed of Service', 'Overall Value'];
                const mCols = block.columns || ['Poor', 'Average', 'Excellent'];
                let matrixTbl = `<div class="overflow-x-auto border border-zinc-200 rounded-xl bg-white text-xs"><table class="w-full text-left border-collapse">`;
                matrixTbl += `<thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-400 font-bold uppercase text-[9px]"><tr><th class="p-2"></th>`;
                mCols.forEach(col => { matrixTbl += `<th class="p-2 text-center text-zinc-600">${col}</th>`; });
                matrixTbl += `</tr></thead><tbody>`;
                mRows.slice(0, 3).forEach(row => {
                    matrixTbl += `<tr class="border-b border-zinc-100"><td class="p-2 font-medium text-zinc-700">${row}</td>`;
                    mCols.forEach(() => { matrixTbl += `<td class="p-2 text-center"><input type="radio" class="accent-zinc-950" disabled /></td>`; });
                    matrixTbl += `</tr>`;
                });
                matrixTbl += `</tbody></table></div>`;
                previewHtml = matrixTbl;
            }
            else if (block.type === 'repeatable') previewHtml = `
                <div class="flex flex-col gap-2 p-3 bg-zinc-50/50 border border-zinc-200 rounded-xl">
                    <div class="flex items-center gap-2">
                        <input type="text" class="flex-1 h-8 px-3 rounded-lg border border-zinc-200 bg-white text-xs" placeholder="${block.placeholder || 'Type item...'}" disabled />
                        <button class="w-8 h-8 rounded-lg border border-zinc-200 bg-white text-zinc-400 text-xs flex items-center justify-center font-bold" disabled>✕</button>
                    </div>
                    <button class="text-[10px] font-bold text-zinc-600 flex items-center gap-1 mt-0.5" disabled>+ Add Item</button>
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

        const placeholderWrapper = document.getElementById('inspector-placeholder-wrapper');
        const placeholderInp = document.getElementById('inspector-field-placeholder');
        if (placeholderWrapper && placeholderInp) {
            if (['text','input','email','phone','number','long_text','textarea','repeatable'].includes(block.type)) {
                placeholderWrapper.classList.remove('hidden');
                placeholderInp.value = block.placeholder || '';
            } else {
                placeholderWrapper.classList.add('hidden');
            }
        }

        // Slider Settings
        const sliderWrapper = document.getElementById('inspector-slider-wrapper');
        if (sliderWrapper) {
            if (block.type === 'slider') {
                sliderWrapper.classList.remove('hidden');
                const minEl = document.getElementById('inspector-slider-min');
                const maxEl = document.getElementById('inspector-slider-max');
                const stepEl = document.getElementById('inspector-slider-step');
                if (minEl) minEl.value = block.min !== undefined ? block.min : 0;
                if (maxEl) maxEl.value = block.max !== undefined ? block.max : 100;
                if (stepEl) stepEl.value = block.step !== undefined ? block.step : 1;
            } else {
                sliderWrapper.classList.add('hidden');
            }
        }

        // Hidden Field Settings
        const hiddenWrapper = document.getElementById('inspector-hidden-wrapper');
        if (hiddenWrapper) {
            if (block.type === 'hidden') {
                hiddenWrapper.classList.remove('hidden');
                const paramEl = document.getElementById('inspector-hidden-param');
                if (paramEl) paramEl.value = block.param_name || 'ref';
            } else {
                hiddenWrapper.classList.add('hidden');
            }
        }

        // Rich Text Content
        const richTextWrapper = document.getElementById('inspector-rich-text-wrapper');
        if (richTextWrapper) {
            if (block.type === 'rich_text') {
                richTextWrapper.classList.remove('hidden');
                const contentEl = document.getElementById('inspector-rich-text-content');
                if (contentEl) contentEl.value = block.content || '';
            } else {
                richTextWrapper.classList.add('hidden');
            }
        }

        // Matrix Settings (Rows & Columns)
        const matrixWrapper = document.getElementById('inspector-matrix-wrapper');
        if (matrixWrapper) {
            if (block.type === 'matrix') {
                matrixWrapper.classList.remove('hidden');
                if (!block.rows) block.rows = ['Service Quality', 'Speed of Service', 'Overall Value'];
                if (!block.columns) block.columns = ['Poor', 'Average', 'Excellent'];

                const rowsContainer = document.getElementById('inspector-matrix-rows-container');
                const colsContainer = document.getElementById('inspector-matrix-cols-container');

                if (rowsContainer) {
                    rowsContainer.innerHTML = '';
                    block.rows.forEach((r, rIdx) => {
                        const rowEl = document.createElement('div');
                        rowEl.className = 'flex items-center gap-1.5';
                        rowEl.innerHTML = `
                            <input type="text" class="inspector-matrix-row-inp flex-1 h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none" value="${r}" data-ridx="${rIdx}">
                            <button type="button" class="btn-del-matrix-row text-zinc-400 hover:text-red-500 p-1 text-xs border-0 bg-transparent cursor-pointer" data-ridx="${rIdx}">✕</button>
                        `;
                        rowsContainer.appendChild(rowEl);
                    });
                    rowsContainer.querySelectorAll('.inspector-matrix-row-inp').forEach(inp => {
                        inp.addEventListener('input', (e) => {
                            const ri = parseInt(e.target.dataset.ridx);
                            block.rows[ri] = e.target.value;
                            triggerAutoSave();
                            renderEditorBlocks();
                        });
                    });
                    rowsContainer.querySelectorAll('.btn-del-matrix-row').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const ri = parseInt(e.target.dataset.ridx);
                            block.rows.splice(ri, 1);
                            triggerAutoSave();
                            renderEditorBlocks();
                            populateInspectorSettings(selectedBlockIndex);
                        });
                    });
                }

                if (colsContainer) {
                    colsContainer.innerHTML = '';
                    block.columns.forEach((c, cIdx) => {
                        const colEl = document.createElement('div');
                        colEl.className = 'flex items-center gap-1.5';
                        colEl.innerHTML = `
                            <input type="text" class="inspector-matrix-col-inp flex-1 h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-none" value="${c}" data-cidx="${cIdx}">
                            <button type="button" class="btn-del-matrix-col text-zinc-400 hover:text-red-500 p-1 text-xs border-0 bg-transparent cursor-pointer" data-cidx="${cIdx}">✕</button>
                        `;
                        colsContainer.appendChild(colEl);
                    });
                    colsContainer.querySelectorAll('.inspector-matrix-col-inp').forEach(inp => {
                        inp.addEventListener('input', (e) => {
                            const ci = parseInt(e.target.dataset.cidx);
                            block.columns[ci] = e.target.value;
                            triggerAutoSave();
                            renderEditorBlocks();
                        });
                    });
                    colsContainer.querySelectorAll('.btn-del-matrix-col').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const ci = parseInt(e.target.dataset.cidx);
                            block.columns.splice(ci, 1);
                            triggerAutoSave();
                            renderEditorBlocks();
                            populateInspectorSettings(selectedBlockIndex);
                        });
                    });
                }
            } else {
                matrixWrapper.classList.add('hidden');
            }
        }

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

    // Matrix add row/column listeners
    document.getElementById('btn-add-matrix-row')?.addEventListener('click', () => {
        if (selectedBlockIndex !== null && currentEditingForm.blocks[selectedBlockIndex]) {
            const block = currentEditingForm.blocks[selectedBlockIndex];
            if (!block.rows) block.rows = [];
            block.rows.push(`Question #${block.rows.length + 1}`);
            triggerAutoSave();
            renderEditorBlocks();
            populateInspectorSettings(selectedBlockIndex);
        }
    });

    document.getElementById('btn-add-matrix-col')?.addEventListener('click', () => {
        if (selectedBlockIndex !== null && currentEditingForm.blocks[selectedBlockIndex]) {
            const block = currentEditingForm.blocks[selectedBlockIndex];
            if (!block.columns) block.columns = [];
            block.columns.push(`Option #${block.columns.length + 1}`);
            triggerAutoSave();
            renderEditorBlocks();
            populateInspectorSettings(selectedBlockIndex);
        }
    });

    // Slider inputs
    document.getElementById('inspector-slider-min')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null && currentEditingForm.blocks[selectedBlockIndex]) {
            currentEditingForm.blocks[selectedBlockIndex].min = parseFloat(e.target.value) || 0;
            triggerAutoSave();
        }
    });
    document.getElementById('inspector-slider-max')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null && currentEditingForm.blocks[selectedBlockIndex]) {
            currentEditingForm.blocks[selectedBlockIndex].max = parseFloat(e.target.value) || 100;
            triggerAutoSave();
        }
    });
    document.getElementById('inspector-slider-step')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null && currentEditingForm.blocks[selectedBlockIndex]) {
            currentEditingForm.blocks[selectedBlockIndex].step = parseFloat(e.target.value) || 1;
            triggerAutoSave();
        }
    });

    // Hidden field param
    document.getElementById('inspector-hidden-param')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null && currentEditingForm.blocks[selectedBlockIndex]) {
            currentEditingForm.blocks[selectedBlockIndex].param_name = e.target.value;
            triggerAutoSave();
            renderEditorBlocks();
        }
    });

    // Rich text content
    document.getElementById('inspector-rich-text-content')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null && currentEditingForm.blocks[selectedBlockIndex]) {
            currentEditingForm.blocks[selectedBlockIndex].content = e.target.value;
            triggerAutoSave();
            renderEditorBlocks();
        }
    });

    // Placeholder input
    document.getElementById('inspector-field-placeholder')?.addEventListener('input', (e) => {
        if (selectedBlockIndex !== null && currentEditingForm.blocks[selectedBlockIndex]) {
            currentEditingForm.blocks[selectedBlockIndex].placeholder = e.target.value;
            triggerAutoSave();
            renderEditorBlocks();
        }
    });

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

    // --- Universal Connect & Embed Everywhere Studio Drawer ---
    let activeEmbedForm = null;

    function openEmbedStudioDrawer(formObj = null) {
        const formToUse = formObj || currentEditingForm;
        if (!formToUse) return;

        const populateAndOpen = (f) => {
            activeEmbedForm = f;
            const formKey = f.form_key || f.id;

            // Header title & badge
            const titleEl = document.getElementById('embed-drawer-title');
            if (titleEl) titleEl.textContent = `Connect: ${f.title || 'Untitled Form'}`;
            const keyBadge = document.getElementById('embed-drawer-key-badge');
            if (keyBadge) keyBadge.textContent = formKey;

            // Generate all channel codes
            generateEmbedCodes(f);

            // Open bottom drawer sheet
            const drawer = document.getElementById('cora-embed-drawer');
            const backdrop = document.getElementById('cora-embed-drawer-backdrop');

            if (backdrop) {
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                }, 10);
            }
            if (drawer) {
                drawer.classList.remove('translate-y-full');
                drawer.classList.add('translate-y-0');
            }
        };

        if (formToUse.id && (formToUse.form_key || formToUse.id) && !window._formIsDirty) {
            populateAndOpen(formToUse);
        } else {
            window.coraShowToast && window.coraShowToast("Publishing form to generate embed keys...", "info");
            saveFormInternal(true, (res) => {
                if (typeof res === 'string') {
                    try { res = JSON.parse(res); } catch(e) {}
                }
                const savedObj = (res && (res.form_key || res.id)) ? res : currentEditingForm;
                if (savedObj && (savedObj.form_key || savedObj.id)) {
                    populateAndOpen(savedObj);
                }
            });
        }
    }

    function closeEmbedStudioDrawer() {
        const drawer = document.getElementById('cora-embed-drawer');
        const backdrop = document.getElementById('cora-embed-drawer-backdrop');
        
        if (backdrop) {
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            setTimeout(() => backdrop.classList.add('hidden'), 300);
        }
        if (drawer) {
            drawer.classList.remove('translate-y-0');
            drawer.classList.add('translate-y-full');
        }
        activeEmbedForm = null;
    }

    function generateEmbedCodes(form) {
        if (!form) form = activeEmbedForm;
        if (!form) return;

        let siteUrl = (typeof coraREData !== 'undefined' && coraREData.siteUrl) ? coraREData.siteUrl : window.location.origin;
        if (siteUrl.endsWith('/')) siteUrl = siteUrl.slice(0, -1);
        const formKey = form.form_key || form.id;

        // Embed Options Checkboxes
        const isTransparent = document.getElementById('embed-opt-transparent')?.checked;
        const isBorderless = document.getElementById('embed-opt-borderless')?.checked;
        const isHideHeader = document.getElementById('embed-opt-hide-header')?.checked;

        // Query parameters
        const params = new URLSearchParams();
        params.set('embed', '1');
        if (isTransparent) params.set('transparent', '1');
        if (isBorderless) params.set('borderless', '1');
        if (isHideHeader) params.set('hide_branding', '1');

        // Check if form has custom theme or accent
        const st = form.styling || {};
        if (st.theme && st.theme !== 'light') params.set('theme', st.theme);
        if (st.accent_color && st.accent_color !== '#09090B') params.set('accent', st.accent_color);

        const hostedUrl = `${siteUrl}/shared-form/${formKey}`;
        const embedUrl = `${hostedUrl}?${params.toString()}`;
        const embedScriptUrl = `${siteUrl}/wp-content/plugins/cora-workspace/assets/js/cora-form-embed.js`;

        // 1. Direct Hosted URL
        const urlInput = document.getElementById('embed-url-input');
        if (urlInput) urlInput.value = hostedUrl;

        // PDF Markdown / Hyperlink
        const mdInput = document.getElementById('embed-markdown-input');
        if (mdInput) mdInput.value = `[Open ${form.title || 'Intake Form'}](${hostedUrl})`;

        // Dynamic QR Code (High-res QR API)
        const qrImg = document.getElementById('embed-qr-image');
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=8&data=${encodeURIComponent(hostedUrl)}`;
        if (qrImg) qrImg.src = qrUrl;

        // 2. Responsive Auto-Height iFrame
        const iframeCode = `<!-- Cora Forms: Responsive Embed Container -->
<div id="cora-form-container-${formKey}" style="width: 100%; max-width: 720px; margin: 0 auto;">
    <iframe
        src="${embedUrl}"
        id="cora-form-${formKey}"
        width="100%"
        height="560"
        frameborder="0"
        scrolling="no"
        allow="camera; microphone; payment; clipboard-write"
        style="width: 100%; border: none; overflow: hidden; display: block; background: transparent; transition: height 0.2s ease;">
    </iframe>
</div>
<script src="${embedScriptUrl}" async><\/script>`;

        const iframeTextarea = document.getElementById('embed-iframe-code');
        if (iframeTextarea) iframeTextarea.value = iframeCode;

        // 3. JS Drop-In Widget & Popup Button
        const widgetInlineCode = `<!-- Cora Forms: Drop-in Inline Container -->
<div class="cora-form-embed" data-form="${formKey}" data-transparent="${isTransparent ? '1' : '0'}" data-hide-header="${isHideHeader ? '1' : '0'}"></div>
<script src="${embedScriptUrl}" async><\/script>`;
        const widgetInlineTextarea = document.getElementById('embed-widget-inline-code');
        if (widgetInlineTextarea) widgetInlineTextarea.value = widgetInlineCode;

        const widgetPopupCode = `<!-- Cora Forms: Slide-Out Drawer / Popup Button -->
<button type="button" class="cora-form-trigger" data-cora-form="${formKey}" style="padding: 12px 24px; background: #09090b; color: #ffffff; border-radius: 12px; font-weight: 600; font-size: 14px; border: none; cursor: pointer;">
    Fill Out ${form.title || 'Form'}
</button>
<script src="${embedScriptUrl}" async><\/script>`;
        const widgetPopupTextarea = document.getElementById('embed-widget-popup-code');
        if (widgetPopupTextarea) widgetPopupTextarea.value = widgetPopupCode;

        // 4. Headless HTML Connect Form
        const restSubmitUrl = `${siteUrl}/wp-json/cora/v1/forms/${formKey}/submit`;
        const blocks = form.blocks || [];
        let formFieldsHtml = '';
        blocks.forEach(b => {
            if (b.type === 'header' || b.type === 'paragraph' || b.type === 'divider' || b.type === 'spacer' || b.type === 'page_break') return;
            const fieldId = b.id || b.name || 'field_' + Math.random().toString(36).substr(2, 5);
            const label = b.label || 'Field';
            const reqAttr = b.required ? ' required' : '';
            const reqStar = b.required ? ' <span style="color:#ef4444;">*</span>' : '';
            
            if (b.type === 'textarea' || b.type === 'long_text') {
                formFieldsHtml += `
    <div style="margin-bottom: 16px;">
        <label for="${fieldId}" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #18181b;">${label}${reqStar}</label>
        <textarea id="${fieldId}" name="${fieldId}" rows="3" placeholder="${b.placeholder || ''}"${reqAttr} style="width: 100%; padding: 10px 12px; border: 1px solid #e4e4e7; border-radius: 8px; font-size: 14px; box-sizing: border-box;"></textarea>
    </div>`;
            } else if (b.type === 'dropdown' || b.type === 'select') {
                const options = Array.isArray(b.options) ? b.options : (b.choices || []);
                let optHtml = `<option value="">Select an option...</option>`;
                options.forEach(o => {
                    const val = typeof o === 'object' ? (o.label || o.value) : o;
                    optHtml += `<option value="${val}">${val}</option>`;
                });
                formFieldsHtml += `
    <div style="margin-bottom: 16px;">
        <label for="${fieldId}" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #18181b;">${label}${reqStar}</label>
        <select id="${fieldId}" name="${fieldId}"${reqAttr} style="width: 100%; padding: 10px 12px; border: 1px solid #e4e4e7; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            ${optHtml}
        </select>
    </div>`;
            } else {
                let inputType = 'text';
                if (b.type === 'email') inputType = 'email';
                else if (b.type === 'phone' || b.type === 'tel') inputType = 'tel';
                else if (b.type === 'number') inputType = 'number';
                else if (b.type === 'date') inputType = 'date';

                formFieldsHtml += `
    <div style="margin-bottom: 16px;">
        <label for="${fieldId}" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #18181b;">${label}${reqStar}</label>
        <input type="${inputType}" id="${fieldId}" name="${fieldId}" placeholder="${b.placeholder || ''}"${reqAttr} style="width: 100%; padding: 10px 12px; border: 1px solid #e4e4e7; border-radius: 8px; font-size: 14px; box-sizing: border-box;" />
    </div>`;
            }
        });

        const headlessCode = `<!-- Cora Forms: Headless HTML Connect Form -->
<form id="cora-connect-form-${formKey}" action="${restSubmitUrl}" method="POST" style="max-width: 480px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, sans-serif;">
    <!-- Anti-spam honeypot (keep hidden) -->
    <input type="text" name="_cora_hp" style="display:none !important;" tabindex="-1" autocomplete="off" />
    <input type="hidden" name="form_id" value="${form.id || ''}" />
${formFieldsHtml}
    <button type="submit" style="width: 100%; padding: 12px; background: #09090b; color: #ffffff; border-radius: 8px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; transition: opacity 0.2s;">
        Submit Inquiry
    </button>
    <div id="cora-form-status-${formKey}" style="margin-top: 12px; font-size: 13px; text-align: center; display: none;"></div>
</form>

\<script>
document.getElementById('cora-connect-form-${formKey}').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var statusEl = document.getElementById('cora-form-status-${formKey}');
    var btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.style.opacity = '0.7';

    var formData = new FormData(form);
    var payload = {};
    formData.forEach(function(val, key) { payload[key] = val; });

    fetch('${restSubmitUrl}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.style.opacity = '1';
        statusEl.style.display = 'block';
        if (data.success) {
            statusEl.style.color = '#15803d';
            statusEl.textContent = 'Thank you! Your submission was received.';
            form.reset();
        } else {
            statusEl.style.color = '#b91c1c';
            statusEl.textContent = data.message || 'Submission failed. Please try again.';
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.style.opacity = '1';
        statusEl.style.display = 'block';
        statusEl.style.color = '#b91c1c';
        statusEl.textContent = 'Network error. Please try again later.';
    });
});
<\/script>`;
        const headlessTextarea = document.getElementById('embed-headless-code');
        if (headlessTextarea) headlessTextarea.value = headlessCode;

        // 5. Live Sandbox iFrame preview
        const sandboxIframe = document.getElementById('embed-sandbox-iframe');
        if (sandboxIframe) {
            sandboxIframe.src = embedUrl + (embedUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
        }
    }

    // Embed Drawer UI Listeners
    document.getElementById('btn-share-editor')?.addEventListener('click', () => openEmbedStudioDrawer(currentEditingForm));
    document.getElementById('btn-close-embed-drawer')?.addEventListener('click', closeEmbedStudioDrawer);
    document.getElementById('cora-embed-drawer-backdrop')?.addEventListener('click', closeEmbedStudioDrawer);

    // Embed channel tab switching
    const embedTabs = ['link', 'iframe', 'widget', 'headless', 'sandbox'];
    embedTabs.forEach(t => {
        const btn = document.getElementById(`tab-embed-${t}`);
        const content = document.getElementById(`embed-content-${t}`);
        btn?.addEventListener('click', () => {
            embedTabs.forEach(ot => {
                const obtn = document.getElementById(`tab-embed-${ot}`);
                const ocontent = document.getElementById(`embed-content-${ot}`);
                if (ot === t) {
                    obtn?.classList.add('border-zinc-950', 'text-zinc-950', 'font-bold');
                    obtn?.classList.remove('border-transparent', 'text-zinc-500', 'font-medium');
                    ocontent?.classList.remove('hidden');
                } else {
                    obtn?.classList.remove('border-zinc-950', 'text-zinc-950', 'font-bold');
                    obtn?.classList.add('border-transparent', 'text-zinc-500', 'font-medium');
                    ocontent?.classList.add('hidden');
                }
            });
            if (t === 'sandbox' && activeEmbedForm) {
                generateEmbedCodes(activeEmbedForm);
            }
        });
    });

    // Embed options toggle changes
    ['embed-opt-transparent', 'embed-opt-borderless', 'embed-opt-hide-header'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', () => {
            if (activeEmbedForm) generateEmbedCodes(activeEmbedForm);
        });
    });

    // Preset selector changes
    document.getElementById('embed-preset-selector')?.addEventListener('change', (e) => {
        const val = e.target.value;
        const optTrans = document.getElementById('embed-opt-transparent');
        const optBord = document.getElementById('embed-opt-borderless');
        const optHide = document.getElementById('embed-opt-hide-header');

        if (val === 'landing') {
            if (optTrans) optTrans.checked = true;
            if (optBord) optBord.checked = true;
            if (optHide) optHide.checked = true;
        } else if (val === 'card') {
            if (optTrans) optTrans.checked = false;
            if (optBord) optBord.checked = false;
            if (optHide) optHide.checked = false;
        } else if (val === 'whitelabel') {
            if (optTrans) optTrans.checked = false;
            if (optBord) optBord.checked = false;
            if (optHide) optHide.checked = true;
        } else if (val === 'dark') {
            if (optTrans) optTrans.checked = false;
            if (optBord) optBord.checked = false;
            if (optHide) optHide.checked = false;
            if (activeEmbedForm && !activeEmbedForm.styling) activeEmbedForm.styling = {};
            if (activeEmbedForm) activeEmbedForm.styling.theme = 'dark';
        }
        if (activeEmbedForm) generateEmbedCodes(activeEmbedForm);
    });

    // Copy action listeners
    document.getElementById('btn-embed-copy-url')?.addEventListener('click', () => {
        const val = document.getElementById('embed-url-input')?.value;
        if (val) coraCopyTextToClipboard(val);
    });
    document.getElementById('btn-embed-open-live')?.addEventListener('click', () => {
        const val = document.getElementById('embed-url-input')?.value;
        if (val) window.open(val, '_blank');
    });
    document.getElementById('btn-embed-copy-markdown')?.addEventListener('click', () => {
        const val = document.getElementById('embed-markdown-input')?.value;
        if (val) coraCopyTextToClipboard(val);
    });
    document.getElementById('btn-embed-download-qr')?.addEventListener('click', () => {
        const img = document.getElementById('embed-qr-image');
        if (img && img.src) {
            const a = document.createElement('a');
            a.href = img.src;
            a.download = `form-qr-${activeEmbedForm ? (activeEmbedForm.form_key || activeEmbedForm.id) : 'code'}.png`;
            a.target = '_blank';
            a.click();
            window.coraShowToast && window.coraShowToast("QR code downloaded!", "success");
        }
    });
    document.getElementById('btn-embed-copy-iframe')?.addEventListener('click', () => {
        const val = document.getElementById('embed-iframe-code')?.value;
        if (val) coraCopyTextToClipboard(val);
    });
    document.getElementById('btn-embed-copy-widget-inline')?.addEventListener('click', () => {
        const val = document.getElementById('embed-widget-inline-code')?.value;
        if (val) coraCopyTextToClipboard(val);
    });
    document.getElementById('btn-embed-copy-widget-popup')?.addEventListener('click', () => {
        const val = document.getElementById('embed-widget-popup-code')?.value;
        if (val) coraCopyTextToClipboard(val);
    });
    document.getElementById('btn-embed-copy-headless')?.addEventListener('click', () => {
        const val = document.getElementById('embed-headless-code')?.value;
        if (val) coraCopyTextToClipboard(val);
    });

    // Sandbox background color simulation
    document.querySelectorAll('.btn-sandbox-bg').forEach(btn => {
        btn.addEventListener('click', () => {
            const bg = btn.dataset.bg;
            const wrap = document.getElementById('sandbox-frame-wrapper');
            if (!wrap) return;
            if (bg === 'checkerboard') {
                wrap.style.backgroundImage = 'linear-gradient(45deg, #e4e4e7 25%, transparent 25%), linear-gradient(-45deg, #e4e4e7 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e4e4e7 75%), linear-gradient(-45deg, transparent 75%, #e4e4e7 75%)';
                wrap.style.backgroundSize = '20px 20px';
                wrap.style.backgroundPosition = '0 0, 0 10px, 10px -10px, -10px 0px';
                wrap.style.backgroundColor = '#f4f4f5';
            } else {
                wrap.style.backgroundImage = 'none';
                wrap.style.backgroundColor = bg;
            }
        });
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
    document.getElementById('btn-left-tab-style')?.addEventListener('click', () => switchLeftTab('style'));
    document.getElementById('btn-left-tab-form')?.addEventListener('click', () => switchLeftTab('form'));
    document.getElementById('btn-left-tab-integ')?.addEventListener('click', () => switchLeftTab('integ'));

    // --- Visual Styling Studio Event Listeners ---
    function ensureStylingObject() {
        if (!currentEditingForm) return null;
        if (!currentEditingForm.styling) currentEditingForm.styling = {};
        return currentEditingForm.styling;
    }

    // Theme preset buttons
    document.querySelectorAll('.btn-theme-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            const st = ensureStylingObject();
            if (!st) return;
            const preset = btn.dataset.preset;
            st.theme = preset;
            if (preset === 'dark') {
                st.bg_color = '#09090B';
                st.card_bg_color = '#18181B';
                st.accent_color = '#FFFFFF';
            } else if (preset === 'cream') {
                st.bg_color = '#F9F6F0';
                st.card_bg_color = '#FAF7F2';
                st.accent_color = '#27272A';
            } else if (preset === 'slate') {
                st.bg_color = '#F1F5F9';
                st.card_bg_color = '#FFFFFF';
                st.accent_color = '#0F172A';
            } else {
                // light
                st.bg_color = '#FAFAFA';
                st.card_bg_color = '#FFFFFF';
                st.accent_color = '#09090B';
            }
            syncStylingControlsToUI(st);
            triggerAutoSave();
        });
    });

    // Transparent background toggle
    document.getElementById('style-bg-transparent')?.addEventListener('change', (e) => {
        const st = ensureStylingObject();
        if (!st) return;
        st.transparent_bg = e.target.checked;
        const solidBgWrap = document.getElementById('style-solid-bg-wrapper');
        if (solidBgWrap) {
            if (st.transparent_bg) solidBgWrap.classList.add('opacity-40', 'pointer-events-none');
            else solidBgWrap.classList.remove('opacity-40', 'pointer-events-none');
        }
        applyStylingToCanvas();
        triggerAutoSave();
    });

    // Page canvas background color
    document.getElementById('style-bg-color-picker')?.addEventListener('input', (e) => {
        const st = ensureStylingObject();
        if (!st) return;
        st.bg_color = e.target.value;
        const hex = document.getElementById('style-bg-color-hex');
        if (hex) hex.value = e.target.value.toUpperCase();
        applyStylingToCanvas();
        triggerAutoSave();
    });
    document.getElementById('style-bg-color-hex')?.addEventListener('change', (e) => {
        const st = ensureStylingObject();
        if (!st) return;
        st.bg_color = e.target.value;
        const p = document.getElementById('style-bg-color-picker');
        if (p) p.value = e.target.value;
        applyStylingToCanvas();
        triggerAutoSave();
    });

    // Card surface color
    document.getElementById('style-card-bg-picker')?.addEventListener('input', (e) => {
        const st = ensureStylingObject();
        if (!st) return;
        st.card_bg_color = e.target.value;
        const hex = document.getElementById('style-card-bg-hex');
        if (hex) hex.value = e.target.value.toUpperCase();
        applyStylingToCanvas();
        triggerAutoSave();
    });
    document.getElementById('style-card-bg-hex')?.addEventListener('change', (e) => {
        const st = ensureStylingObject();
        if (!st) return;
        st.card_bg_color = e.target.value;
        const p = document.getElementById('style-card-bg-picker');
        if (p) p.value = e.target.value;
        applyStylingToCanvas();
        triggerAutoSave();
    });

    // Accent color swatches & picker
    document.querySelectorAll('.btn-accent-swatch').forEach(btn => {
        btn.addEventListener('click', () => {
            const st = ensureStylingObject();
            if (!st) return;
            const color = btn.dataset.color;
            st.accent_color = color;
            const picker = document.getElementById('style-accent-picker');
            if (picker) picker.value = color;
            const hex = document.getElementById('style-accent-hex');
            if (hex) hex.value = color.toUpperCase();
            applyStylingToCanvas();
            triggerAutoSave();
        });
    });
    document.getElementById('style-accent-picker')?.addEventListener('input', (e) => {
        const st = ensureStylingObject();
        if (!st) return;
        st.accent_color = e.target.value;
        const hex = document.getElementById('style-accent-hex');
        if (hex) hex.value = e.target.value.toUpperCase();
        applyStylingToCanvas();
        triggerAutoSave();
    });
    document.getElementById('style-accent-hex')?.addEventListener('change', (e) => {
        const st = ensureStylingObject();
        if (!st) return;
        st.accent_color = e.target.value;
        const p = document.getElementById('style-accent-picker');
        if (p) p.value = e.target.value;
        applyStylingToCanvas();
        triggerAutoSave();
    });

    // Card framing style
    document.querySelectorAll('.btn-card-style').forEach(btn => {
        btn.addEventListener('click', () => {
            const st = ensureStylingObject();
            if (!st) return;
            st.card_style = btn.dataset.style;
            document.querySelectorAll('.btn-card-style').forEach(b => {
                if (b.dataset.style === st.card_style) {
                    b.className = "btn-card-style py-2 px-1 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10.5px] font-bold transition-all cursor-pointer shadow-2xs";
                } else {
                    b.className = "btn-card-style py-2 px-1 rounded-lg border border-zinc-200 bg-white text-zinc-700 hover:text-zinc-950 text-center text-[10.5px] font-medium transition-all cursor-pointer";
                }
            });
            applyStylingToCanvas();
            triggerAutoSave();
        });
    });

    // Corner radius
    document.querySelectorAll('.btn-radius').forEach(btn => {
        btn.addEventListener('click', () => {
            const st = ensureStylingObject();
            if (!st) return;
            st.border_radius = btn.dataset.radius;
            document.querySelectorAll('.btn-radius').forEach(b => {
                if (b.dataset.radius === st.border_radius) {
                    b.className = "btn-radius py-1.5 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10px] font-bold transition-all cursor-pointer";
                } else {
                    b.className = "btn-radius py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10px] font-medium transition-all cursor-pointer";
                }
            });
            applyStylingToCanvas();
            triggerAutoSave();
        });
    });

    // Typography
    document.querySelectorAll('.btn-font').forEach(btn => {
        btn.addEventListener('click', () => {
            const st = ensureStylingObject();
            if (!st) return;
            st.font_family = btn.dataset.font;
            document.querySelectorAll('.btn-font').forEach(b => {
                const f = b.dataset.font;
                const fontClass = f === 'mono' ? 'font-mono' : (f === 'serif' ? 'font-serif' : 'font-sans');
                if (f === st.font_family) {
                    b.className = `btn-font py-2 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10.5px] font-bold ${fontClass} transition-all cursor-pointer`;
                } else {
                    b.className = `btn-font py-2 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10.5px] ${fontClass} font-medium transition-all cursor-pointer`;
                }
            });
            applyStylingToCanvas();
            triggerAutoSave();
        });
    });

    // Density
    document.querySelectorAll('.btn-density').forEach(btn => {
        btn.addEventListener('click', () => {
            const st = ensureStylingObject();
            if (!st) return;
            st.density = btn.dataset.density;
            document.querySelectorAll('.btn-density').forEach(b => {
                if (b.dataset.density === st.density) {
                    b.className = "btn-density py-1.5 rounded-lg border border-zinc-950 bg-zinc-950 text-white text-center text-[10.5px] font-bold transition-all cursor-pointer";
                } else {
                    b.className = "btn-density py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-center text-[10.5px] font-medium transition-all cursor-pointer";
                }
            });
            applyStylingToCanvas();
            triggerAutoSave();
        });
    });

    // Branding header toggle
    document.getElementById('style-show-branding')?.addEventListener('change', (e) => {
        const st = ensureStylingObject();
        if (!st) return;
        st.show_branding = e.target.checked;
        triggerAutoSave();
    });

    // Custom CSS
    document.getElementById('style-custom-css')?.addEventListener('input', (e) => {
        const st = ensureStylingObject();
        if (!st) return;
        st.custom_css = e.target.value;
        triggerAutoSave();
    });

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

    // =========================================================================
    // FORMS GLOBAL & PER-FORM SETTINGS, NOTIFICATIONS & FLOWS CONTROLLER
    // =========================================================================
    let formsGlobalSettingsData = {
        admin_email_enable: true,
        admin_email_to: '',
        admin_email_subject: 'New Submission: {form_title} from {submitter_name}',
        admin_push_enable: true,
        admin_wa_enable: false,
        admin_wa_to: '',
        submitter_email_enable: true,
        submitter_sender_name: 'Studio Director',
        submitter_reply_to: '',
        submitter_subject: 'Thank you for your submission: {form_title}',
        submitter_message: 'Thank you for reaching out! We have received your details and our team will review and get back to you within 24 hours. A copy of your submitted answers is below.',
        submitter_include_answers: true
    };
    let activeSettingsScope = 'global';
    let lastFocusedSettingsInput = null;
    let previewMode = 'submitter'; // 'submitter' or 'admin'

    const PRESET_TEMPLATES = {
        lead_confirmation: {
            subject: 'Thank you for reaching out: {form_title}',
            message: 'Hi {submitter_name},\n\nThank you for reaching out to us. We have received your inquiry regarding {form_title}. Our team is reviewing your requirements and will reach out to you within 24 hours.\n\nA full record of your submitted answers is attached below for your reference.'
        },
        vip_intake: {
            subject: 'Priority Intake Received: {form_title} [VIP Ref #{submission_id}]',
            message: 'Dear {submitter_name},\n\nYour executive intake has been received and escalated to our Senior Account Director with high priority. We are currently preparing your customized proposal and strategy briefing.\n\nPlease find your verified submission record below.'
        },
        booking_receipt: {
            subject: 'Booking & Consultation Confirmed: {form_title}',
            message: 'Hello {submitter_name},\n\nYour consultation request for {form_title} is confirmed. A calendar invitation and video link will be dispatched shortly to {submitter_email}.\n\nBelow is the summary of your consultation schedule preferences.'
        },
        survey_receipt: {
            subject: 'Compliance & Survey Receipt: {form_title} (ID #{submission_id})',
            message: 'Thank you {submitter_name} for completing the {form_title}.\n\nYour response has been securely encrypted, logged, and timestamped on {submission_date} in accordance with workspace compliance standards.\n\nSummary of submitted metrics is below.'
        }
    };

    function loadFormsGlobalSettings() {
        populateSettingsScopeSelector();
        
        // Fetch from backend
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'cora_forms_get_settings',
                _ajax_nonce: '<?php echo wp_create_nonce( "cora_forms_nonce" ); ?>'
            },
            success: function(res) {
                if (res.success && res.data) {
                    if (res.data.global) {
                        formsGlobalSettingsData = Object.assign({}, formsGlobalSettingsData, res.data.global);
                    }
                    applySettingsToUI(activeSettingsScope);
                }
            },
            error: function() {
                applySettingsToUI(activeSettingsScope);
            }
        });
    }

    function populateSettingsScopeSelector() {
        const optgroup = document.getElementById('cora-scope-forms-optgroup');
        if (!optgroup) return;
        optgroup.innerHTML = '';

        if (Array.isArray(formsData) && formsData.length > 0) {
            formsData.forEach(form => {
                const opt = document.createElement('option');
                opt.value = form.id;
                opt.textContent = `${form.title || 'Untitled Form'} (ID: ${form.id})`;
                optgroup.appendChild(opt);
            });
        }
    }

    function applySettingsToUI(scope) {
        let currentConfig = Object.assign({}, formsGlobalSettingsData);

        const badge = document.getElementById('cora-settings-scope-badge');
        if (scope === 'global') {
            if (badge) {
                badge.textContent = 'Global Defaults';
                badge.className = 'px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-800 text-[10px] font-bold border border-zinc-200';
            }
        } else {
            const form = Array.isArray(formsData) ? formsData.find(f => String(f.id) === String(scope)) : null;
            if (badge) {
                badge.textContent = `Form Override: ${form ? form.title : 'Form #' + scope}`;
                badge.className = 'px-2 py-0.5 rounded-full bg-blue-50 text-blue-800 text-[10px] font-bold border border-blue-200';
            }
            if (form && form.settings) {
                try {
                    const formSettings = typeof form.settings === 'string' ? JSON.parse(form.settings) : form.settings;
                    if (formSettings.notifications) {
                        currentConfig = Object.assign({}, currentConfig, formSettings.notifications);
                    }
                } catch(e) {}
            }
        }

        // Hydrate Admin Settings
        const adminEmailEnable = document.getElementById('setting-admin-email-enable');
        const adminEmailTo = document.getElementById('setting-admin-email-to');
        const adminEmailSubject = document.getElementById('setting-admin-email-subject');
        const adminPushEnable = document.getElementById('setting-admin-push-enable');
        const adminWaEnable = document.getElementById('setting-admin-wa-enable');
        const adminWaTo = document.getElementById('setting-admin-wa-to');
        const adminWaFields = document.getElementById('setting-admin-wa-fields');

        if (adminEmailEnable) adminEmailEnable.checked = currentConfig.admin_email_enable !== false;
        if (adminEmailTo) adminEmailTo.value = currentConfig.admin_email_to || '';
        if (adminEmailSubject) adminEmailSubject.value = currentConfig.admin_email_subject || 'New Submission: {form_title} from {submitter_name}';
        if (adminPushEnable) adminPushEnable.checked = currentConfig.admin_push_enable !== false;
        if (adminWaEnable) {
            adminWaEnable.checked = Boolean(currentConfig.admin_wa_enable);
            if (adminWaFields) {
                if (adminWaEnable.checked) adminWaFields.classList.remove('hidden');
                else adminWaFields.classList.add('hidden');
            }
        }
        if (adminWaTo) adminWaTo.value = currentConfig.admin_wa_to || '';

        // Hydrate Submitter Settings
        const submitterEmailEnable = document.getElementById('setting-submitter-email-enable');
        const submitterSenderName = document.getElementById('setting-submitter-sender-name');
        const submitterReplyTo = document.getElementById('setting-submitter-reply-to');
        const submitterSubject = document.getElementById('setting-submitter-subject');
        const submitterMessage = document.getElementById('setting-submitter-message');
        const submitterIncludeAnswers = document.getElementById('setting-submitter-include-answers');

        if (submitterEmailEnable) submitterEmailEnable.checked = currentConfig.submitter_email_enable !== false;
        if (submitterSenderName) submitterSenderName.value = currentConfig.submitter_sender_name || 'Studio Director';
        if (submitterReplyTo) submitterReplyTo.value = currentConfig.submitter_reply_to || '';
        if (submitterSubject) submitterSubject.value = currentConfig.submitter_subject || 'Thank you for your submission: {form_title}';
        if (submitterMessage) submitterMessage.value = currentConfig.submitter_message || 'Thank you for reaching out! We have received your details and our team will review and get back to you within 24 hours. A copy of your submitted answers is below.';
        if (submitterIncludeAnswers) submitterIncludeAnswers.checked = currentConfig.submitter_include_answers !== false;

        updatePipelineFlowBadges();
        updateSettingsLivePreview();
    }

    window.coraToggleFormSettingsAccordion = function(headerEl) {
        if (!headerEl) return;
        const card = headerEl.closest('.cora-accordion-card');
        if (!card) return;
        const body = card.querySelector('.cora-accordion-body');
        const icon = card.querySelector('.cora-accordion-icon');
        if (!body) return;

        const isHidden = body.classList.contains('hidden');
        if (isHidden) {
            body.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        } else {
            body.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
        }
    };

    function updatePipelineFlowBadges() {
        const adminEmail = document.getElementById('setting-admin-email-enable')?.checked;
        const adminPush = document.getElementById('setting-admin-push-enable')?.checked;
        const adminWa = document.getElementById('setting-admin-wa-enable')?.checked;
        const submitterEmail = document.getElementById('setting-submitter-email-enable')?.checked;

        const adminBadge = document.getElementById('flow-node-admin-badge');
        const submitterBadge = document.getElementById('flow-node-submitter-badge');
        const channelCountBadge = document.getElementById('admin-alerts-channel-count');

        if (channelCountBadge) {
            const activeChannels = [];
            if (adminEmail) activeChannels.push('Email');
            if (adminPush) activeChannels.push('Push');
            if (adminWa) activeChannels.push('WhatsApp');
            channelCountBadge.textContent = activeChannels.length > 0 ? activeChannels.join(', ') : 'Muted';
            channelCountBadge.className = activeChannels.length > 0 ? 'hidden sm:inline-flex px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-600 text-[10px] font-semibold border border-zinc-200' : 'hidden sm:inline-flex px-2 py-0.5 rounded-full bg-zinc-50 text-zinc-400 text-[10px] font-semibold border border-zinc-200/60';
        }

        if (adminBadge) {
            const hasAdmin = adminEmail || adminPush || adminWa;
            adminBadge.textContent = hasAdmin ? 'Active' : 'Disabled';
            adminBadge.className = hasAdmin ? 'px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold' : 'px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-500 text-[9px] font-bold';
        }
        if (submitterBadge) {
            submitterBadge.textContent = submitterEmail ? 'Active' : 'Disabled';
            submitterBadge.className = submitterEmail ? 'px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold' : 'px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-500 text-[9px] font-bold';
        }
    }

    function updateSettingsLivePreview() {
        const previewSender = document.getElementById('preview-mock-sender');
        const previewSubject = document.getElementById('preview-mock-subject');
        const previewTitle = document.getElementById('preview-mock-title');
        const previewMessage = document.getElementById('preview-mock-message');
        const previewTableContainer = document.getElementById('preview-mock-table-container');

        if (previewMode === 'admin') {
            const subject = document.getElementById('setting-admin-email-subject')?.value || 'New Submission: {form_title} from {submitter_name}';
            if (previewSender) previewSender.textContent = 'Cora Platform <notifications@heycora.in>';
            if (previewSubject) previewSubject.textContent = subject.replace('{form_title}', 'Creative Intake').replace('{submitter_name}', 'Aarav Mehta');
            if (previewTitle) previewTitle.textContent = 'New Submission Received';
            if (previewMessage) previewMessage.innerHTML = 'A new lead response has been submitted to <strong>Creative Intake</strong>. Full intake details and client answers are below:';
            if (previewTableContainer) previewTableContainer.classList.remove('hidden');
        } else {
            const senderName = document.getElementById('setting-submitter-sender-name')?.value || 'Studio Director';
            const replyTo = document.getElementById('setting-submitter-reply-to')?.value || 'contact@yourbusiness.com';
            const subject = document.getElementById('setting-submitter-subject')?.value || 'Thank you for your submission: {form_title}';
            const msg = document.getElementById('setting-submitter-message')?.value || '';
            const incTable = document.getElementById('setting-submitter-include-answers')?.checked;

            if (previewSender) previewSender.textContent = `${senderName} <${replyTo}>`;
            if (previewSubject) previewSubject.textContent = subject.replace('{form_title}', 'Creative Intake').replace('{submitter_name}', 'Aarav Mehta');
            if (previewTitle) previewTitle.textContent = 'Creative Intake';
            if (previewMessage) previewMessage.textContent = msg.replace('{form_title}', 'Creative Intake').replace('{submitter_name}', 'Aarav Mehta').replace('{submission_id}', '1042').replace('{submission_date}', 'Today, 8:00 PM');
            
            if (previewTableContainer) {
                if (incTable) previewTableContainer.classList.remove('hidden');
                else previewTableContainer.classList.add('hidden');
            }
        }
    }

    // Event Listeners for Settings Tab
    jQuery(document).on('change', '#cora-forms-settings-scope', function() {
        activeSettingsScope = this.value;
        applySettingsToUI(activeSettingsScope);
    });

    jQuery(document).on('change', '#setting-admin-wa-enable', function() {
        const fields = document.getElementById('setting-admin-wa-fields');
        if (fields) {
            if (this.checked) fields.classList.remove('hidden');
            else fields.classList.add('hidden');
        }
    });

    jQuery(document).on('input change', '#setting-admin-email-enable, #setting-admin-email-to, #setting-admin-email-subject, #setting-submitter-email-enable, #setting-submitter-sender-name, #setting-submitter-reply-to, #setting-submitter-subject, #setting-submitter-message, #setting-submitter-include-answers', function() {
        updatePipelineFlowBadges();
        updateSettingsLivePreview();
    });

    // Track focused input for token insertion
    jQuery(document).on('focus', '#setting-submitter-subject, #setting-submitter-message, #setting-admin-email-subject', function() {
        lastFocusedSettingsInput = this;
    });

    // Token Inserter Button
    jQuery(document).on('click', '.btn-insert-token', function(e) {
        e.preventDefault();
        const token = this.getAttribute('data-token');
        if (!token) return;

        const target = lastFocusedSettingsInput || document.getElementById('setting-submitter-message');
        if (target) {
            const start = target.selectionStart || target.value.length;
            const end = target.selectionEnd || target.value.length;
            target.value = target.value.substring(0, start) + token + target.value.substring(end);
            target.focus();
            target.setSelectionRange(start + token.length, start + token.length);
            updateSettingsLivePreview();
        }
    });

    // Apply Template Presets
    jQuery(document).on('click', '.btn-apply-template-preset', function(e) {
        e.preventDefault();
        const presetKey = this.getAttribute('data-preset');
        const preset = PRESET_TEMPLATES[presetKey];
        if (!preset) return;

        const subjInput = document.getElementById('setting-submitter-subject');
        const msgInput = document.getElementById('setting-submitter-message');

        if (subjInput) subjInput.value = preset.subject;
        if (msgInput) msgInput.value = preset.message;

        updateSettingsLivePreview();
        window.coraShowToast && window.coraShowToast(`Applied preset: ${this.textContent.trim()}`, "success");
    });

    // Preview Mode Switcher (Respondent vs Admin)
    jQuery(document).on('click', '#btn-preview-mode-submitter', function(e) {
        e.preventDefault();
        previewMode = 'submitter';
        this.className = 'px-2.5 py-1 rounded-md bg-white text-zinc-900 text-[11px] font-bold shadow-2xs cursor-pointer';
        const adminBtn = document.getElementById('btn-preview-mode-admin');
        if (adminBtn) adminBtn.className = 'px-2.5 py-1 rounded-md text-zinc-500 hover:text-zinc-900 text-[11px] font-semibold transition-colors cursor-pointer';
        updateSettingsLivePreview();
    });

    jQuery(document).on('click', '#btn-preview-mode-admin', function(e) {
        e.preventDefault();
        previewMode = 'admin';
        this.className = 'px-2.5 py-1 rounded-md bg-white text-zinc-900 text-[11px] font-bold shadow-2xs cursor-pointer';
        const subBtn = document.getElementById('btn-preview-mode-submitter');
        if (subBtn) subBtn.className = 'px-2.5 py-1 rounded-md text-zinc-500 hover:text-zinc-900 text-[11px] font-semibold transition-colors cursor-pointer';
        updateSettingsLivePreview();
    });

    // Save Settings
    jQuery(document).on('click', '#btn-save-forms-settings', function(e) {
        e.preventDefault();
        const btn = this;
        const spinner = document.getElementById('save-settings-spinner');
        const text = document.getElementById('save-settings-text');

        if (spinner) spinner.classList.remove('hidden');
        if (text) text.textContent = 'Saving...';
        btn.disabled = true;

        const payload = {
            admin_email_enable: document.getElementById('setting-admin-email-enable')?.checked,
            admin_email_to: document.getElementById('setting-admin-email-to')?.value.trim(),
            admin_email_subject: document.getElementById('setting-admin-email-subject')?.value.trim(),
            admin_push_enable: document.getElementById('setting-admin-push-enable')?.checked,
            admin_wa_enable: document.getElementById('setting-admin-wa-enable')?.checked,
            admin_wa_to: document.getElementById('setting-admin-wa-to')?.value.trim(),
            submitter_email_enable: document.getElementById('setting-submitter-email-enable')?.checked,
            submitter_sender_name: document.getElementById('setting-submitter-sender-name')?.value.trim(),
            submitter_reply_to: document.getElementById('setting-submitter-reply-to')?.value.trim(),
            submitter_subject: document.getElementById('setting-submitter-subject')?.value.trim(),
            submitter_message: document.getElementById('setting-submitter-message')?.value.trim(),
            submitter_include_answers: document.getElementById('setting-submitter-include-answers')?.checked
        };

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'cora_forms_save_settings',
                scope: activeSettingsScope,
                settings: JSON.stringify(payload),
                _ajax_nonce: '<?php echo wp_create_nonce( "cora_forms_nonce" ); ?>'
            },
            success: function(res) {
                if (spinner) spinner.classList.add('hidden');
                if (text) text.textContent = 'Save Settings';
                btn.disabled = false;

                if (res.success) {
                    if (activeSettingsScope === 'global') {
                        formsGlobalSettingsData = Object.assign({}, formsGlobalSettingsData, payload);
                    } else {
                        // Update in local formsData
                        const form = Array.isArray(formsData) ? formsData.find(f => String(f.id) === String(activeSettingsScope)) : null;
                        if (form) {
                            const curSettings = form.settings ? (typeof form.settings === 'string' ? JSON.parse(form.settings) : form.settings) : {};
                            curSettings.notifications = payload;
                            form.settings = curSettings;
                        }
                    }
                    window.coraShowToast && window.coraShowToast(res.data?.message || "Notification settings saved successfully!", "success");
                } else {
                    window.coraShowToast && window.coraShowToast(res.data?.message || "Failed to save settings.", "error");
                }
            },
            error: function() {
                if (spinner) spinner.classList.add('hidden');
                if (text) text.textContent = 'Save Settings';
                btn.disabled = false;
                window.coraShowToast && window.coraShowToast("Server communication error while saving settings.", "error");
            }
        });
    });

    // Test Notification Modal Handlers
    jQuery(document).on('click', '#btn-open-test-notification', function(e) {
        e.preventDefault();
        const modal = document.getElementById('cora-test-notification-modal');
        if (modal) modal.classList.remove('hidden');
    });

    jQuery(document).on('click', '#btn-close-test-modal, #btn-cancel-test-modal', function(e) {
        e.preventDefault();
        const modal = document.getElementById('cora-test-notification-modal');
        if (modal) modal.classList.add('hidden');
    });

    jQuery(document).on('click', '#btn-dispatch-test-email', function(e) {
        e.preventDefault();
        const destEmail = document.getElementById('test-notification-email-to')?.value.trim();
        const testType = document.getElementById('test-notification-type')?.value || 'submitter';

        if (!destEmail) {
            window.coraShowToast && window.coraShowToast("Please enter a destination email address.", "error");
            return;
        }

        const btn = this;
        const spinner = document.getElementById('dispatch-test-spinner');
        const text = document.getElementById('dispatch-test-text');

        if (spinner) spinner.classList.remove('hidden');
        if (text) text.textContent = 'Sending...';
        btn.disabled = true;

        const payload = {
            admin_email_enable: document.getElementById('setting-admin-email-enable')?.checked,
            admin_email_to: document.getElementById('setting-admin-email-to')?.value.trim(),
            admin_email_subject: document.getElementById('setting-admin-email-subject')?.value.trim(),
            submitter_email_enable: document.getElementById('setting-submitter-email-enable')?.checked,
            submitter_sender_name: document.getElementById('setting-submitter-sender-name')?.value.trim(),
            submitter_reply_to: document.getElementById('setting-submitter-reply-to')?.value.trim(),
            submitter_subject: document.getElementById('setting-submitter-subject')?.value.trim(),
            submitter_message: document.getElementById('setting-submitter-message')?.value.trim(),
            submitter_include_answers: document.getElementById('setting-submitter-include-answers')?.checked
        };

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'cora_forms_send_test_notification',
                destination_email: destEmail,
                test_type: testType,
                settings: JSON.stringify(payload),
                _ajax_nonce: '<?php echo wp_create_nonce( "cora_forms_nonce" ); ?>'
            },
            success: function(res) {
                if (spinner) spinner.classList.add('hidden');
                if (text) text.textContent = 'Send Test Email';
                btn.disabled = false;

                if (res.success) {
                    const modal = document.getElementById('cora-test-notification-modal');
                    if (modal) modal.classList.add('hidden');
                    window.coraShowToast && window.coraShowToast(`Test notification sent to ${destEmail}!`, "success");
                } else {
                    window.coraShowToast && window.coraShowToast(res.data?.message || "Failed to dispatch test notification.", "error");
                }
            },
            error: function() {
                if (spinner) spinner.classList.add('hidden');
                if (text) text.textContent = 'Send Test Email';
                btn.disabled = false;
                window.coraShowToast && window.coraShowToast("Server error dispatching test email.", "error");
            }
        });
    });

});
</script>
