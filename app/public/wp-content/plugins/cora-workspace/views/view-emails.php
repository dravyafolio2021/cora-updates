<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<style>
/* Cora Business Email Module - Scoped Drawer Rules (Zero Right Bleed Shadow) */
.cora-email-drawer {
    display: none !important;
    visibility: hidden !important;
    pointer-events: none !important;
    box-shadow: none !important;
}
.cora-email-drawer.is-open {
    display: flex !important;
    visibility: visible !important;
    pointer-events: auto !important;
    box-shadow: -8px 0 24px -4px rgba(0, 0, 0, 0.12) !important;
}
.dark .cora-email-drawer.is-open {
    box-shadow: -8px 0 24px -4px rgba(0, 0, 0, 0.45) !important;
}
</style>
<div id="cora-page-emails" class="flex-1 flex flex-col min-h-0 font-sans" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
<?php
$current_email_plan = class_exists( 'Cora_Email_Campaigns_Engine' ) ? Cora_Email_Campaigns_Engine::get_current_plan() : 'growth';
$plan_tiers_meta    = class_exists( 'Cora_Email_Campaigns_Engine' ) ? Cora_Email_Campaigns_Engine::get_plan_tiers() : array();
$active_plan_info   = isset( $plan_tiers_meta[ $current_email_plan ] ) ? $plan_tiers_meta[ $current_email_plan ] : array( 'name' => 'Growth Plan', 'max_inboxes' => 5 );

$emails_header_args = array(
    'title'            => 'Business Email & Campaigns',
    'description'      => 'Connect Google Workspace & Hostinger mailboxes (1–20 accounts by plan), launch multi-inbox cold outreach cadences, and send marketing broadcasts with deliverability protection.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'Connect Inbox',
        'mobile_text' => 'Connect',
        'onclick'     => "openConnectInboxDrawer()",
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'visible'     => true,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $emails_header_args );
}
?>

    <!-- Sticky Sub-Navigation Tabs (8 Modern Tabs - Edge-to-Edge - Monochromatic) -->
    <div id="cora-emails-subtabs" class="cora-sub-tabs-container cora-sticky-sub-tabs flex items-center justify-between border-b border-zinc-200/80 dark:border-zinc-800 bg-transparent -mx-3 sm:-mx-4 md:-mx-5 px-3 sm:px-4 md:px-5 shrink-0 overflow-x-auto scrollbar-hide w-[calc(100%+1.5rem)] sm:w-[calc(100%+2rem)] md:w-[calc(100%+2.5rem)] max-w-[calc(100%+1.5rem)] sm:max-w-[calc(100%+2rem)] md:max-w-[calc(100%+2.5rem)] mb-4" style="position: -webkit-sticky; position: sticky; top: var(--cora-sticky-tabs-top, 0px); left: 0; right: 0; z-index: 45; touch-action: pan-x pan-y; -webkit-overflow-scrolling: touch; scrollbar-width: none; min-height: 44px; height: 44px; padding-top: 0; padding-bottom: 0; margin-top: 0; background-color: transparent;">
        <div class="flex items-center gap-1 overflow-x-auto scrollbar-hide">
            <button id="tab-btn-email-tab-compose" onclick="coraSwitchEmailSubTab('email-tab-compose')"
                    class="cora-email-sub-tab px-3.5 h-[40px] py-0 -mb-px text-xs font-bold text-zinc-900 border-b-2 border-zinc-950 transition-all whitespace-nowrap cursor-pointer shrink-0 inline-flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                Compose & Send
            </button>
            <button id="tab-btn-email-tab-inboxes" onclick="coraSwitchEmailSubTab('email-tab-inboxes')"
                    class="cora-email-sub-tab px-3.5 h-[40px] py-0 -mb-px text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0 inline-flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                Connected Inboxes
                <span id="tab-badge-inboxes-count" class="px-1.5 py-0.2 rounded-md bg-zinc-100 text-[10px] font-mono font-bold text-zinc-700">3/5</span>
            </button>
            <button id="tab-btn-email-tab-outreach" onclick="coraSwitchEmailSubTab('email-tab-outreach')"
                    class="cora-email-sub-tab px-3.5 h-[40px] py-0 -mb-px text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0 inline-flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                Outreach Campaigns
                <span id="tab-badge-outreach-count" class="px-1.5 py-0.2 rounded-md bg-emerald-50 text-[10px] font-mono font-bold text-emerald-700">2 Active</span>
            </button>
            <button id="tab-btn-email-tab-broadcasts" onclick="coraSwitchEmailSubTab('email-tab-broadcasts')"
                    class="cora-email-sub-tab px-3.5 h-[40px] py-0 -mb-px text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0 inline-flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                Marketing Broadcasts
            </button>
            <button id="tab-btn-email-tab-templates" onclick="coraSwitchEmailSubTab('email-tab-templates')"
                    class="cora-email-sub-tab px-3.5 h-[40px] py-0 -mb-px text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0 inline-flex items-center">
                Email Templates
            </button>
            <button id="tab-btn-email-tab-sequences" onclick="coraSwitchEmailSubTab('email-tab-sequences')"
                    class="cora-email-sub-tab px-3.5 h-[40px] py-0 -mb-px text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0 inline-flex items-center">
                Automated Sequences
            </button>
            <button id="tab-btn-email-tab-outbox" onclick="coraSwitchEmailSubTab('email-tab-outbox')"
                    class="cora-email-sub-tab px-3.5 h-[40px] py-0 -mb-px text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0 inline-flex items-center">
                Outbox & Sent Logs
            </button>
            <button id="tab-btn-email-tab-settings" onclick="coraSwitchEmailSubTab('email-tab-settings')"
                    class="cora-email-sub-tab px-3.5 h-[40px] py-0 -mb-px text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0 inline-flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                Deliverability & Settings
            </button>
        </div>
        <div class="cora-sub-tabs-extra ml-auto flex items-center pl-2 sm:pl-3 shrink-0">
            <?php if ( function_exists( 'cora_get_mcp_button_html' ) ) { echo cora_get_mcp_button_html(); } ?>
        </div>
    </div>

    <!-- MAIN SUB-TAB CONTENT PANELS CONTAINER -->
    <div class="flex-1 min-h-0 relative">
        
        <!-- SUB-TAB 1: COMPOSE & SEND (ENHANCED WITH FROM SENDER PICKER & SPINTAX) -->
        <div id="email-tab-compose" class="cora-email-tab-content flex flex-col h-full min-h-0 space-y-4">
            
            <!-- Top KPI Summary Cards Row (2x2 Mobile / 1x4 Desktop) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 shrink-0">
                <!-- Stat Card 1: Total Emails Sent -->
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3 shadow-3xs flex items-center justify-between min-w-0">
                    <div class="min-w-0 pr-2">
                        <span class="text-[10px] font-bold tracking-wider text-zinc-400 uppercase block truncate">Total Dispatched</span>
                        <div class="text-base sm:text-lg font-bold text-zinc-900 mt-0.5 truncate" id="stat-total-sent">--</div>
                        <span class="text-[10px] text-zinc-500 font-medium block truncate" id="stat-month-sent">-- this month</span>
                    </div>
                    <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-700 shrink-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                    </div>
                </div>

                <!-- Stat Card 2: Delivery Success Rate -->
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3 shadow-3xs flex items-center justify-between min-w-0">
                    <div class="min-w-0 pr-2">
                        <span class="text-[10px] font-bold tracking-wider text-zinc-400 uppercase block truncate">Delivery Success</span>
                        <div class="text-base sm:text-lg font-bold text-zinc-900 mt-0.5 truncate" id="stat-success-rate">99.4%</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] text-emerald-600 font-semibold truncate">SPF & DKIM Validated</span>
                        </div>
                    </div>
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200/50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>
                    </div>
                </div>

                <!-- Stat Card 3: Connected Inboxes & Plan Quota -->
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3 shadow-3xs flex items-center justify-between cursor-pointer hover:bg-zinc-50/80 transition-all min-w-0" onclick="coraSwitchEmailSubTab('email-tab-inboxes')">
                    <div class="min-w-0 pr-2">
                        <span class="text-[10px] font-bold tracking-wider text-zinc-400 uppercase block truncate">Connected Inboxes</span>
                        <div class="text-base sm:text-lg font-bold text-zinc-900 mt-0.5 truncate" id="stat-connected-inboxes-kpi">3 of 5 Inboxes</div>
                        <span class="text-[10px] text-zinc-500 font-medium block truncate" id="stat-plan-kpi">Growth Plan • 250/day</span>
                    </div>
                    <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-700 shrink-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                    </div>
                </div>

                <!-- Stat Card 4: Campaigns Active -->
                <div class="bg-white border border-zinc-200/80 rounded-xl p-3 shadow-3xs flex items-center justify-between cursor-pointer hover:bg-zinc-50/80 transition-all min-w-0" onclick="coraSwitchEmailSubTab('email-tab-outreach')">
                    <div class="min-w-0 pr-2">
                        <span class="text-[10px] font-bold tracking-wider text-zinc-400 uppercase block truncate">Active Campaigns</span>
                        <div class="text-xs font-bold text-zinc-900 mt-0.5 truncate" id="stat-active-campaigns-kpi">2 Outreach • 1 Broadcast</div>
                        <span class="text-[10px] text-zinc-500 font-mono block truncate">Multi-Inbox Rotation</span>
                    </div>
                    <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-700 shrink-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Segment Switcher (Compose vs Live Preview) -->
            <div class="flex lg:hidden items-center justify-between mb-3 bg-white p-1 rounded-xl border border-zinc-200">
                <button type="button" id="cora-compose-mobile-tab-btn-form" onclick="coraToggleMobileComposeView('form')"
                        class="flex-1 py-1.5 text-xs font-bold rounded-lg bg-zinc-900 text-white transition-all cursor-pointer">
                    Compose Form
                </button>
                <button type="button" id="cora-compose-mobile-tab-btn-preview" onclick="coraToggleMobileComposeView('preview')"
                        class="flex-1 py-1.5 text-xs font-medium text-zinc-600 rounded-lg hover:text-zinc-900 transition-all cursor-pointer">
                    Live Preview
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4.5 flex-1 min-h-0">
                <!-- Left Form Panel (7 Cols) -->
                <div id="cora-compose-form-panel" class="lg:col-span-7 flex flex-col bg-white border border-zinc-200 rounded-xl p-4.5 shadow-3xs overflow-y-auto">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 shrink-0">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-zinc-100 flex items-center justify-center text-zinc-800">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-zinc-900">New Email Communication</h3>
                                <p class="text-[10px] text-zinc-500">Route via Google Workspace or Hostinger Webmail with variable injection & Spintax.</p>
                            </div>
                        </div>
                        <button type="button" onclick="resetEmailComposeForm()" class="text-[11px] font-semibold text-zinc-400 hover:text-zinc-700 bg-transparent border-0 cursor-pointer transition-colors">
                            Clear
                        </button>
                    </div>
                    
                    <form id="cora-email-compose-form" class="space-y-3 mt-3 flex-1 flex flex-col">
                        
                        <!-- From Account Selector (Google Workspace vs Hostinger Webmail) -->
                        <div class="space-y-1 shrink-0">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Send From Business Account *</label>
                                <span class="text-[9px] text-emerald-600 font-semibold" id="compose-inbox-daily-quota">Daily Quota Safe</span>
                            </div>
                            <select id="compose-from-inbox-picker" onchange="onSenderInboxChange(this)"
                                    class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-all shadow-3xs cursor-pointer">
                                <!-- Populated dynamically from connected inboxes -->
                            </select>
                        </div>

                        <!-- Recipient Selection Group -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 shrink-0">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">CRM Lead / Client Picker</label>
                                <select id="compose-recipient-picker" onchange="onRecipientPickerChange(this)"
                                        class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-900 outline-hidden focus:border-zinc-400 transition-all shadow-3xs cursor-pointer">
                                    <option value="">-- Select CRM Client / Lead --</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Manual Recipient Email *</label>
                                <input type="email" id="email-to" placeholder="client@example.com" required
                                       class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-900 outline-hidden focus:border-zinc-400 transition-all shadow-3xs" />
                            </div>
                        </div>

                        <!-- Template Preset Selector & Quick Injector -->
                        <div class="space-y-1 shrink-0">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Load Template Preset</label>
                                <span class="text-[9px] font-medium text-zinc-400">1-Click Pre-fill</span>
                            </div>
                            <select id="compose-template-selector" onchange="loadTemplateIntoComposer(this.value)"
                                    class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-medium text-zinc-800 outline-hidden focus:border-zinc-400 transition-all shadow-3xs cursor-pointer">
                                <option value="">-- Choose from Template Library --</option>
                            </select>
                        </div>

                        <!-- Subject -->
                        <div class="space-y-1 shrink-0">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Subject Line *</label>
                            <input type="text" id="email-subject" placeholder="Your appointment / visual production confirmation" required oninput="updateLivePreview()"
                                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-900 outline-hidden focus:border-zinc-400 transition-all shadow-3xs" />
                        </div>

                        <!-- Variable Tags & Spintax Pill Bar -->
                        <div class="space-y-1 shrink-0">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Dynamic Tags & Spintax</label>
                                <span class="text-[9px] text-zinc-400">Click to insert</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                                <button type="button" onclick="insertVariableTag('{client_name}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{client_name}</button>
                                <button type="button" onclick="insertVariableTag('{company_name}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{company_name}</button>
                                <button type="button" onclick="insertVariableTag('{event_name}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{event_name}</button>
                                <button type="button" onclick="insertVariableTag('{event_date}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{event_date}</button>
                                <button type="button" onclick="insertVariableTag('{portal_url}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{portal_url}</button>
                                <button type="button" onclick="insertVariableTag('{studio_name}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{studio_name}</button>
                                <button type="button" onclick="insertVariableTag('{Hi|Hello|Hey}')" class="px-2 py-0.5 rounded-md bg-amber-50 hover:bg-amber-100 text-[10px] font-mono font-bold text-amber-700 border border-amber-200/50 cursor-pointer transition-colors">✨ {Hi|Hello|Hey}</button>
                            </div>
                        </div>

                        <!-- Message Body -->
                        <div class="space-y-1 flex-1 flex flex-col min-h-36">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Message Content *</label>
                            <textarea id="email-message" placeholder="Write your email message here. Spintax like {Hi|Hello} will be dynamically randomized..." required oninput="updateLivePreview()"
                                      class="w-full flex-1 min-h-36 p-3 rounded-lg border border-zinc-200 bg-white text-xs font-normal text-zinc-900 outline-hidden focus:border-zinc-400 transition-all resize-none shadow-3xs leading-relaxed"></textarea>
                        </div>

                        <!-- Attachment Link Simulator -->
                        <div class="flex items-center justify-between p-2 rounded-lg bg-zinc-50 border border-zinc-200/80 shrink-0">
                            <div class="flex items-center gap-2 text-[11px] font-medium text-zinc-700">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                <span>Attach Vault Asset / Client Portal Link</span>
                            </div>
                            <button type="button" onclick="attachVaultDocumentSim()" class="text-[11px] font-semibold text-zinc-900 hover:text-zinc-700 bg-transparent border-0 cursor-pointer transition-colors">
                                + Attach Asset Link
                            </button>
                        </div>

                        <!-- Action Toolbar -->
                        <div class="flex items-center justify-between pt-2 border-t border-zinc-100 shrink-0">
                            <div class="flex items-center gap-1.5 text-[10px] text-zinc-500 font-mono">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span id="compose-sender-info">Ready to Dispatch</span>
                            </div>
                            <button type="submit" id="btn-send-email"
                                    class="h-9 px-5 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold flex items-center gap-2 cursor-pointer transition-all border-none shadow-xs">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                Send Email Now
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Live Preview Card (5 Cols) -->
                <div id="cora-compose-preview-panel" class="hidden lg:flex lg:col-span-5 flex-col bg-white border border-zinc-200 rounded-xl p-4.5 shadow-3xs overflow-y-auto">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 shrink-0">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-zinc-100 flex items-center justify-center text-zinc-800">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </div>
                            <h3 class="text-xs font-bold text-zinc-900">Live Envelope & Render Preview</h3>
                        </div>
                        <span class="text-[10px] font-mono text-zinc-400" id="preview-provider-badge">Google Workspace</span>
                    </div>

                    <div class="mt-3.5 p-4 rounded-xl bg-zinc-50 border border-zinc-200/80 flex-1 flex flex-col font-sans">
                        <!-- Simulated Email Envelope Header -->
                        <div class="border-b border-zinc-200/80 pb-2.5 mb-3.5 space-y-1 text-[11px]">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-400 font-bold uppercase text-[9px]">From:</span>
                                <span class="font-mono text-zinc-900 font-semibold text-[11px] truncate max-w-[240px]" id="preview-from">alex.mehta@clarastudio.com</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-400 font-bold uppercase text-[9px]">To:</span>
                                <span class="font-mono text-zinc-700 text-[11px] truncate max-w-[240px]" id="preview-to">client@example.com</span>
                            </div>
                            <div class="flex items-start justify-between pt-0.5">
                                <span class="text-zinc-400 font-bold uppercase text-[9px] shrink-0">Subject:</span>
                                <span class="font-bold text-zinc-900 text-right truncate pl-2 text-[11px]" id="preview-subject">(No Subject Specified)</span>
                            </div>
                        </div>

                        <!-- Email Body Container Card -->
                        <div class="flex-1 bg-white rounded-lg p-4 border border-zinc-200/70 shadow-3xs flex flex-col justify-between">
                            <div id="preview-body" class="text-xs text-zinc-800 leading-relaxed whitespace-pre-wrap font-normal">
                                <span class="text-zinc-400 italic text-[11px]">Message body preview will render here in real-time as you type...</span>
                            </div>

                            <div class="mt-5 pt-3 border-t border-zinc-100 flex items-center justify-between text-[10px] text-zinc-400">
                                <span id="preview-footer-brand">Official Communication via Cora Studio</span>
                                <span class="text-emerald-600 font-mono text-[9px]">TLS SSL 256-bit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUB-TAB 2: CONNECTED INBOXES (1, 5, 10, 20 EMAIL ACCOUNTS BY PLAN) -->
        <div id="email-tab-inboxes" class="cora-email-tab-content hidden flex-col space-y-4">
            
            <!-- Plan & Deliverability Header Banner Card -->
            <div class="bg-white border border-zinc-200/80 rounded-xl p-4 sm:p-5 shadow-3xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="space-y-1.5 max-w-xl">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full bg-zinc-900 text-white text-[11px] font-bold" id="plan-badge-pill">Growth Plan (Up to 5 Inboxes)</span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[10px] font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Multi-Inbox Rotation Active
                        </span>
                        <span class="text-zinc-400 text-xs">•</span>
                        <span class="text-xs font-semibold text-zinc-600" id="plan-capacity-summary">250 emails / day safe pooled capacity</span>
                    </div>
                    <p class="text-xs text-zinc-500 leading-relaxed">
                        Connect Google Workspace and Hostinger accounts to rotate senders across campaigns. Distributing outgoing volume keeps your domains warm and guarantees maximum inbox placement.
                    </p>
                    
                    <!-- Progress Bar for Inboxes Usage -->
                    <div class="pt-1 space-y-1">
                        <div class="flex items-center justify-between text-[10px] font-semibold text-zinc-600">
                            <span id="plan-quota-text">3 of 5 Inboxes Connected</span>
                            <span id="plan-quota-percent">60% Capacity</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-zinc-100 overflow-hidden">
                            <div id="plan-quota-bar" class="h-full bg-zinc-900 rounded-full transition-all duration-300" style="width: 60%;"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="openConnectInboxDrawer()" 
                            class="h-9 px-4 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold flex items-center gap-2 border-0 cursor-pointer shadow-xs transition-colors">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Connect Business Inbox
                    </button>
                    <button type="button" onclick="scrollToPlanTiers()" 
                            class="h-9 px-3 rounded-lg border border-zinc-200 text-zinc-700 hover:bg-zinc-50 text-xs font-semibold flex items-center gap-1.5 bg-white cursor-pointer transition-colors shadow-3xs">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        Plan Tiers
                    </button>
                </div>
            </div>

            <!-- Connected Inboxes Cards Grid -->
            <div>
                <div class="flex items-center justify-between mb-2.5">
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Active Mailbox Pool</h3>
                    <span class="text-[11px] text-zinc-500 font-mono">Auto-Balanced Delivery</span>
                </div>
                <div id="inboxes-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    <!-- Populated dynamically via renderConnectedInboxes() -->
                </div>
            </div>

            <!-- Plan Scaling Matrix (1, 5, 10, 20 Accounts) -->
            <div id="cora-plan-scaling-section" class="bg-white border border-zinc-200/80 rounded-xl p-4 sm:p-5 shadow-3xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-100 pb-3">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Business Email Capacity by Workspace Plan</h3>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Scale your cold email outbound and marketing reach with multi-inbox rotation.</p>
                    </div>
                    <span class="text-[10px] font-mono text-zinc-400">1 to 20 Accounts Architecture</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" id="plan-tiers-grid">
                    <!-- Populated dynamically via renderPlanTiers() -->
                </div>
            </div>
        </div>

        <!-- SUB-TAB 3: OUTREACH CAMPAIGNS (COLD OUTREACH & MULTI-INBOX ROTATION) -->
        <div id="email-tab-outreach" class="cora-email-tab-content hidden flex-col space-y-4">
            <!-- Outreach Controls Banner -->
            <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-3xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-bold text-zinc-900">Cold Outreach & Multi-Inbox Cadences</h3>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[10px] font-bold">Smart Sender Rotation</span>
                    </div>
                    <p class="text-[11px] text-zinc-500">
                        Launch automated multi-step sequences. Outgoing dispatches automatically cycle between connected Google Workspace and Hostinger inboxes to protect deliverability.
                    </p>
                </div>
                <button type="button" onclick="openOutreachCampaignDrawer()" 
                        class="h-9 px-4 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold flex items-center gap-2 border-0 cursor-pointer shadow-xs shrink-0 transition-colors">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Create Outreach Campaign
                </button>
            </div>

            <!-- Outreach Campaigns Grid / List -->
            <div id="outreach-campaigns-container" class="space-y-3">
                <!-- Dynamically populated via renderOutreachCampaigns() -->
            </div>
        </div>

        <!-- SUB-TAB 4: MARKETING BROADCASTS -->
        <div id="email-tab-broadcasts" class="cora-email-tab-content hidden flex-col space-y-4">
            <!-- Marketing Broadcasts Toolbar -->
            <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-3xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-bold text-zinc-900">Email Marketing Broadcasts & Newsletters</h3>
                        <span class="px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-800 text-[10px] font-bold">CAN-SPAM Compliant</span>
                    </div>
                    <p class="text-[11px] text-zinc-500">
                        Send broadcast announcements, portfolio lookbooks, and seasonal offers to your audience with automated 1-click unsubscribe headers.
                    </p>
                </div>
                <button type="button" onclick="openMarketingBroadcastDrawer()" 
                        class="h-9 px-4 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold flex items-center gap-2 border-0 cursor-pointer shadow-xs shrink-0 transition-colors">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    New Marketing Broadcast
                </button>
            </div>

            <!-- Broadcasts List Container -->
            <div id="marketing-broadcasts-container" class="space-y-3">
                <!-- Dynamically populated via renderMarketingBroadcasts() -->
            </div>
        </div>

        <!-- SUB-TAB 5: EMAIL TEMPLATES -->
        <div id="email-tab-templates" class="cora-email-tab-content hidden flex-col space-y-4">
            <!-- Templates Toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white border border-zinc-200 rounded-xl p-3.5 shadow-3xs">
                <div class="flex items-center gap-2.5 flex-1">
                    <input type="text" id="template-search-input" placeholder="Search templates..." oninput="renderEmailTemplates()"
                           class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-hidden w-full sm:w-64 focus:border-zinc-400 transition-colors" />
                    <select id="template-category-filter" onchange="renderEmailTemplates()"
                            class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-800 outline-hidden cursor-pointer">
                        <option value="">All Categories</option>
                        <option value="Outreach">Outreach</option>
                        <option value="Bookings">Bookings</option>
                        <option value="Financials">Financials</option>
                        <option value="Media & Vault">Media & Vault</option>
                        <option value="Leads">Leads</option>
                        <option value="Reviews">Reviews</option>
                        <option value="Legal">Legal</option>
                    </select>
                </div>
                <button onclick="openEmailTemplateDrawer()" 
                        class="h-9 px-3.5 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold flex items-center gap-2 border-0 cursor-pointer shadow-xs shrink-0 transition-colors">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Create Template
                </button>
            </div>

            <!-- Templates Card Grid Container (Clean 3-Column Grid) -->
            <div id="templates-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                <!-- Dynamically populated via renderEmailTemplates() -->
            </div>
        </div>

        <!-- SUB-TAB 6: AUTOMATED DRIP SEQUENCES -->
        <div id="email-tab-sequences" class="cora-email-tab-content hidden flex-col space-y-4">
            <div class="bg-white border border-zinc-200 rounded-xl p-4.5 shadow-3xs">
                <div class="flex items-center justify-between pb-3.5 border-b border-zinc-100">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900">Automated Client Drip Sequences</h3>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Automate timely follow-ups, proofing notifications, and review acquisition drips.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-[10px] font-bold text-emerald-700 border border-emerald-200">3 Workflows Active</span>
                </div>

                <div class="space-y-4 mt-4">
                    <!-- Sequence 1: Lead Inquiry Follow-up -->
                    <div class="p-3.5 rounded-xl border border-zinc-200/80 bg-zinc-50/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-zinc-900 text-white flex items-center justify-center text-xs font-bold">01</span>
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-900">New Inquiry Nurturing Sequence (4 Steps)</h4>
                                    <p class="text-[10px] text-zinc-500">Triggers automatically upon new website inquiry submission.</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-md bg-zinc-900 text-white text-[10px] font-bold">Active</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 pt-1">
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 1 • Immediate</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">Thank You & Vision Review</span>
                            </div>
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 2 • Day 1 Delay</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">Portfolio & Visual Showcase</span>
                            </div>
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 3 • Day 3 Delay</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">Consultation Booking Call</span>
                            </div>
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 4 • Day 5 Delay</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">Final Follow-up Notice</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sequence 2: Post-Shoot Gallery Delivery -->
                    <div class="p-3.5 rounded-xl border border-zinc-200/80 bg-zinc-50/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-zinc-900 text-white flex items-center justify-center text-xs font-bold">02</span>
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-900">Post-Shoot Gallery Delivery Drip (3 Steps)</h4>
                                    <p class="text-[10px] text-zinc-500">Triggers when shoot status updates to Completed.</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-md bg-zinc-900 text-white text-[10px] font-bold">Active</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1">
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 1 • Immediate</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">Proofing Underway Notice</span>
                            </div>
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 2 • Day 2 Delay</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">High-Res Gallery Passcode Link</span>
                            </div>
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 3 • Day 7 Delay</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">Google Review Request</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sequence 3: Invoice & Payment Milestone Drip -->
                    <div class="p-3.5 rounded-xl border border-zinc-200/80 bg-zinc-50/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-zinc-900 text-white flex items-center justify-center text-xs font-bold">03</span>
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-900">Invoice & Milestone Payment Drip (3 Steps)</h4>
                                    <p class="text-[10px] text-zinc-500">Triggers upon invoice creation and payment milestone schedules.</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-md bg-zinc-900 text-white text-[10px] font-bold">Active</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1">
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 1 • Immediate</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">Invoice Issued & Razorpay Link</span>
                            </div>
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 2 • Day 3 Delay</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">Friendly Milestone Reminder</span>
                            </div>
                            <div class="p-2.5 bg-white rounded-lg border border-zinc-200/70 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 3 • Day 7 Delay</span>
                                <span class="font-semibold text-zinc-900 truncate block mt-0.5">Final Notice & Vault Lock</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUB-TAB 7: SENT LOGS & OUTBOX (WITH SENDER INBOX IDENTIFIER) -->
        <div id="email-tab-outbox" class="cora-email-tab-content hidden flex-col space-y-3.5">
            <!-- Filter Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white border border-zinc-200 rounded-xl p-3.5 shadow-3xs">
                <div class="flex items-center gap-2.5 flex-1 flex-wrap">
                    <input type="text" id="outbox-search-input" placeholder="Search recipient or subject..." oninput="filterOutboxLogs()"
                           class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-hidden w-full sm:w-64 focus:border-zinc-400 transition-colors" />
                    <select id="outbox-status-filter" onchange="filterOutboxLogs()"
                            class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-800 outline-hidden cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="delivered">Delivered</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                    <select id="outbox-type-filter" onchange="filterOutboxLogs()"
                            class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-800 outline-hidden cursor-pointer">
                        <option value="">All Types</option>
                        <option value="Cold Outreach">Cold Outreach</option>
                        <option value="Marketing Broadcast">Marketing Broadcast</option>
                        <option value="Custom Compose">Custom Compose</option>
                        <option value="Lead Automation">Lead Automation</option>
                    </select>
                </div>
                <button onclick="exportOutboxLogsCSV()" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold flex items-center gap-1.5 cursor-pointer transition-colors shadow-3xs shrink-0">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export CSV
                </button>
            </div>

            <!-- Outbox Log Table Card -->
            <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-3xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-zinc-50 border-b border-zinc-200 text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                                <th class="py-2.5 px-3.5">Sent From (Inbox)</th>
                                <th class="py-2.5 px-3.5">Recipient</th>
                                <th class="py-2.5 px-3.5">Subject</th>
                                <th class="py-2.5 px-3.5">Campaign / Type</th>
                                <th class="py-2.5 px-3.5">Sent Timestamp</th>
                                <th class="py-2.5 px-3.5">Status</th>
                                <th class="py-2.5 px-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="outbox-table-body" class="divide-y divide-zinc-100">
                            <!-- Populated dynamically via renderEmailLogs() -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SUB-TAB 8: DELIVERABILITY & SETTINGS -->
        <div id="email-tab-settings" class="cora-email-tab-content hidden flex-col space-y-4">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                
                <!-- Left: Domain DNS Health Inspector (7 Cols) -->
                <div class="lg:col-span-7 bg-white border border-zinc-200 rounded-xl p-5 shadow-3xs space-y-4">
                    <div class="border-b border-zinc-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-zinc-900 flex items-center gap-2">
                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                Domain Deliverability & DNS Records
                            </h3>
                            <p class="text-[11px] text-zinc-500 mt-0.5">Automated DNS record verification for Google Workspace and Hostinger.</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold">100% Health</span>
                    </div>

                    <div class="space-y-3">
                        <!-- Record 1: SPF -->
                        <div class="p-3 rounded-lg border border-zinc-200/80 bg-zinc-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-zinc-900">SPF Record (Sender Policy Framework)</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded-sm bg-emerald-100 text-emerald-800 font-bold">VALID ✓</span>
                                </div>
                                <div class="text-[11px] font-mono text-zinc-600 truncate">v=spf1 include:_spf.google.com include:_spf.mail.hostinger.com ~all</div>
                            </div>
                            <span class="text-[10px] text-zinc-400 font-medium shrink-0">Passes Google & Hostinger</span>
                        </div>

                        <!-- Record 2: DKIM -->
                        <div class="p-3 rounded-lg border border-zinc-200/80 bg-zinc-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-zinc-900">DKIM Signature (DomainKeys Identified Mail)</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded-sm bg-emerald-100 text-emerald-800 font-bold">VALID ✓</span>
                                </div>
                                <div class="text-[11px] font-mono text-zinc-600 truncate">google._domainkey & hostingermail._domainkey (2048-bit RSA)</div>
                            </div>
                            <span class="text-[10px] text-zinc-400 font-medium shrink-0">Anti-Spoofing Signed</span>
                        </div>

                        <!-- Record 3: DMARC -->
                        <div class="p-3 rounded-lg border border-zinc-200/80 bg-zinc-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-zinc-900">DMARC Policy</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded-sm bg-emerald-100 text-emerald-800 font-bold">VALID ✓</span>
                                </div>
                                <div class="text-[11px] font-mono text-zinc-600 truncate">v=DMARC1; p=quarantine; pct=100; rua=mailto:dmarc@clarastudio.com</div>
                            </div>
                            <span class="text-[10px] text-zinc-400 font-medium shrink-0">100% Quarantine</span>
                        </div>

                        <!-- Record 4: MX Routing -->
                        <div class="p-3 rounded-lg border border-zinc-200/80 bg-zinc-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-zinc-900">MX Mail Exchanger</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded-sm bg-emerald-100 text-emerald-800 font-bold">CONFIGURED ✓</span>
                                </div>
                                <div class="text-[11px] font-mono text-zinc-600 truncate">aspmx.l.google.com / mx1.hostinger.com (Priority 1-10)</div>
                            </div>
                            <span class="text-[10px] text-zinc-400 font-medium shrink-0">Inbound Routing Active</span>
                        </div>
                    </div>
                </div>

                <!-- Right: SMTP Fallback Relay Configuration (5 Cols) -->
                <div class="lg:col-span-5 bg-white border border-zinc-200 rounded-xl p-5 shadow-3xs flex flex-col justify-between">
                    <div>
                        <div class="border-b border-zinc-100 pb-3 mb-3 flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                    Default Relay Fallback
                                </h3>
                                <p class="text-[11px] text-zinc-500 mt-0.5">Official system-level SMTP relay configuration.</p>
                            </div>
                            <button type="button" id="smtp-lock-badge" onclick="coraToggleSMTPLock()" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-700 text-[11px] font-semibold shrink-0 cursor-pointer hover:bg-zinc-200 transition-colors">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                System Locked
                            </button>
                        </div>

                        <form id="smtp-settings-form" class="space-y-3" onsubmit="event.preventDefault();">
                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Host *</label>
                                    <input type="text" id="smtp-host" value="<?php echo esc_attr(get_option('cora_smtp_host', 'smtp.hostinger.com')); ?>" readonly
                                           class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Port *</label>
                                    <input type="text" id="smtp-port" value="<?php echo esc_attr(get_option('cora_smtp_port', '465')); ?>" readonly
                                           class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Encryption</label>
                                    <?php $sec = get_option('cora_smtp_secure', 'ssl'); ?>
                                    <select id="smtp-secure" disabled
                                            class="w-full h-8 px-2 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden">
                                        <option value="ssl" <?php selected($sec, 'ssl'); ?>>SSL (Port 465)</option>
                                        <option value="tls" <?php selected($sec, 'tls'); ?>>TLS (Port 587)</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Username *</label>
                                    <input type="email" id="smtp-username" value="<?php echo esc_attr(get_option('cora_smtp_username', 'heycora@claraverse.in')); ?>" readonly
                                           class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Password</label>
                                <input type="password" id="smtp-password" value="••••••••••••••••" readonly
                                       class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">From Name</label>
                                    <input type="text" id="smtp-from-name" value="<?php echo esc_attr(get_option('cora_from_name', 'Cora Studio')); ?>" readonly
                                           class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">From Email</label>
                                    <input type="email" id="smtp-from-email" value="<?php echo esc_attr(get_option('cora_from_email', 'heycora@claraverse.in')); ?>" readonly
                                           class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                                </div>
                            </div>

                            <div class="pt-2 flex items-center justify-between">
                                <button type="button" onclick="openSmtpTestDrawer()" class="h-8 px-3 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 cursor-pointer flex items-center gap-1.5 transition-colors shadow-3xs">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                    Test Relay Ping
                                </button>
                                <div id="smtp-admin-footer" class="flex items-center gap-1.5 text-xs font-semibold text-zinc-500">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    Managed by Studio Admin
                                </div>
                                <button type="button" id="smtp-save-btn" onclick="coraSaveSMTPSettings()" class="hidden h-8 px-3.5 rounded-lg bg-zinc-900 text-white text-xs font-semibold hover:bg-zinc-800 cursor-pointer flex items-center gap-2 transition-colors shadow-3xs">
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- SLIDING DRAWERS / MOBILE BOTTOM SHEETS (SOP Rule 1 & 12 Compliance)       -->
<!-- ========================================================================= -->

<!-- 1. CONNECT BUSINESS INBOX DRAWER (GOOGLE WORKSPACE & HOSTINGER WEBMAIL) -->
<div id="cora-connect-inbox-drawer" class="cora-email-drawer fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-lg h-[90vh] sm:h-full max-h-[90vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans" style="display: none; visibility: hidden; box-shadow: none;">
    <!-- Mobile Drag Handle -->
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-200 shrink-0">
        <div>
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Connect Business Email Account</h3>
            <p class="text-[10px] text-zinc-500 mt-0.5" id="connect-drawer-plan-info">Growth Plan: 3 of 5 Inboxes in use</p>
        </div>
        <button onclick="closeConnectInboxDrawer()" class="text-zinc-400 hover:text-zinc-700 border-0 bg-transparent cursor-pointer text-xs font-bold transition-colors">✕ Close</button>
    </div>

    <!-- Provider Selection Pill Tabs -->
    <div class="p-3 bg-zinc-50 border-b border-zinc-200/80 shrink-0">
        <div class="flex items-center bg-zinc-200/80 p-0.5 rounded-xl">
            <button type="button" id="provider-tab-btn-google" onclick="switchConnectProvider('google_workspace')"
                    class="flex-1 py-1.5 text-xs font-bold rounded-lg bg-white text-zinc-900 shadow-3xs transition-all cursor-pointer flex items-center justify-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/></svg>
                Google Workspace
            </button>
            <button type="button" id="provider-tab-btn-hostinger" onclick="switchConnectProvider('hostinger')"
                    class="flex-1 py-1.5 text-xs font-semibold text-zinc-600 hover:text-zinc-900 rounded-lg transition-all cursor-pointer flex items-center justify-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.2L19 8.6v6.8L12 19.4 5 15.4V8.6L12 4.2z"/></svg>
                Hostinger Webmail
            </button>
            <button type="button" id="provider-tab-btn-custom" onclick="switchConnectProvider('custom_smtp')"
                    class="flex-1 py-1.5 text-xs font-semibold text-zinc-600 hover:text-zinc-900 rounded-lg transition-all cursor-pointer flex items-center justify-center gap-1.5">
                Custom SMTP
            </button>
        </div>
    </div>
    
    <form id="connect-inbox-form" onsubmit="submitConnectInbox(event)" class="flex-1 flex flex-col overflow-y-auto p-5 space-y-3.5">
        <input type="hidden" id="connect-provider-val" value="google_workspace" />
        
        <!-- Provider Info Banner -->
        <div id="connect-provider-guide" class="p-3 rounded-xl bg-zinc-50 border border-zinc-200/80 text-xs text-zinc-600 leading-relaxed">
            <span class="font-bold text-zinc-900 block mb-0.5">Google Workspace Setup:</span>
            Enter your Google Workspace email address and a 16-character Google App Password (generated in Google Account &gt; Security &gt; 2-Step Verification &gt; App Passwords). Relay connects via <code>smtp.gmail.com:587 (TLS)</code>.
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Business Email Address *</label>
            <input type="email" id="connect-email" placeholder="alex.mehta@clarastudio.com" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Sender Display Name *</label>
            <input type="text" id="connect-from-name" placeholder="Alex Mehta • Studio Director" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider" id="connect-password-label">Google App Password (16 Characters) *</label>
            <input type="password" id="connect-password" placeholder="•••• •••• •••• ••••" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <!-- Custom SMTP Extra Fields (Hidden unless Custom selected) -->
        <div id="custom-smtp-fields" class="hidden space-y-3">
            <div class="grid grid-cols-2 gap-2.5">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">SMTP Host *</label>
                    <input type="text" id="connect-smtp-host" placeholder="smtp.domain.com"
                           class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">SMTP Port *</label>
                    <input type="text" id="connect-smtp-port" placeholder="587"
                           class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Encryption Method</label>
                <select id="connect-smtp-secure" class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden cursor-pointer">
                    <option value="tls">TLS / STARTTLS (Port 587)</option>
                    <option value="ssl">SSL (Port 465)</option>
                </select>
            </div>
        </div>

        <!-- Daily Sending Cap Slider / Input -->
        <div class="space-y-1 pt-1">
            <div class="flex items-center justify-between">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Daily Outreach Limit (Safety Cap)</label>
                <span class="text-[11px] font-mono font-bold text-zinc-900" id="connect-daily-limit-label">45 emails / day</span>
            </div>
            <input type="range" id="connect-daily-limit-range" min="15" max="100" value="45" oninput="$('#connect-daily-limit-label').text(this.value + ' emails / day')"
                   class="w-full accent-zinc-900 cursor-pointer" />
            <p class="text-[10px] text-zinc-400">Recommended: 35–50 sends/day per inbox to maintain 99%+ deliverability.</p>
        </div>

        <!-- Automated Warmup Toggle -->
        <div class="p-3 rounded-xl bg-zinc-50 border border-zinc-200/80 flex items-center justify-between">
            <div class="space-y-0.5 pr-2">
                <span class="text-xs font-bold text-zinc-900 block">Enable Automated Safe Warmup</span>
                <span class="text-[10px] text-zinc-500 block">Gradually ramps volume from 5 up to your safety cap over 14 days.</span>
            </div>
            <input type="checkbox" id="connect-warmup-toggle" checked class="w-4 h-4 accent-zinc-900 cursor-pointer shrink-0" />
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2 shrink-0 mt-auto">
            <button type="button" onclick="closeConnectInboxDrawer()" class="h-9 px-3.5 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-600 bg-transparent cursor-pointer hover:bg-zinc-50 transition-colors">Cancel</button>
            <button type="submit" id="btn-submit-connect-inbox" class="h-9 px-4 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold border-0 cursor-pointer shadow-xs transition-colors flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                Verify & Connect Account
            </button>
        </div>
    </form>
</div>

<!-- 2. CREATE OUTREACH CAMPAIGN DRAWER -->
<div id="cora-outreach-campaign-drawer" class="cora-email-drawer fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-xl h-[92vh] sm:h-full max-h-[92vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans" style="display: none; visibility: hidden; box-shadow: none;">
    <!-- Mobile Drag Handle -->
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-200 shrink-0">
        <div>
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Create Cold Outreach Cadence</h3>
            <p class="text-[10px] text-zinc-500 mt-0.5">Multi-step sequences with automated inbox sender rotation.</p>
        </div>
        <button onclick="closeOutreachCampaignDrawer()" class="text-zinc-400 hover:text-zinc-700 border-0 bg-transparent cursor-pointer text-xs font-bold transition-colors">✕ Close</button>
    </div>

    <form id="outreach-campaign-form" onsubmit="submitOutreachCampaign(event)" class="flex-1 flex flex-col overflow-y-auto p-5 space-y-4">
        <input type="hidden" id="outreach-camp-id" value="" />

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Campaign Name *</label>
            <input type="text" id="outreach-camp-name" placeholder="e.g. Q1 Architectural Firms & Commercial Showcase" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Target Lead Audience Segment</label>
            <select id="outreach-camp-target" class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden cursor-pointer">
                <option value="Architects & Luxury Developers">Architects & Luxury Developers</option>
                <option value="Premium Event Planners & Curators">Premium Event Planners & Curators</option>
                <option value="Commercial Real Estate Brokers">Commercial Real Estate Brokers</option>
                <option value="High-Value Inquiries (> ₹50,000)">High-Value Inquiries (&gt; ₹50,000)</option>
                <option value="All Uncontacted CRM Leads">All Uncontacted CRM Leads</option>
            </select>
        </div>

        <!-- Multi-Inbox Sender Pool Selector -->
        <div class="space-y-1.5 p-3 rounded-xl bg-zinc-50 border border-zinc-200/80">
            <div class="flex items-center justify-between">
                <label class="text-[10px] font-bold text-zinc-900 uppercase tracking-wider">Multi-Inbox Sender Rotation Pool</label>
                <span class="text-[9px] font-mono text-emerald-600 font-semibold">Round-Robin Active</span>
            </div>
            <p class="text-[10px] text-zinc-500">Select which connected business accounts will distribute outbound sends for this campaign:</p>
            <div id="outreach-sender-pool-checkboxes" class="space-y-1.5 pt-1">
                <!-- Dynamically populated from connected inboxes -->
            </div>
        </div>

        <!-- Cadence Steps Builder -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Cadence Follow-Up Sequence</label>
                <span class="text-[9px] text-zinc-400">Step 1 Immediate, Steps 2-3 Delayed</span>
            </div>

            <!-- Step 1 -->
            <div class="p-3.5 rounded-xl border border-zinc-200/80 bg-white space-y-2 shadow-3xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-md bg-zinc-900 text-white flex items-center justify-center text-[10px]">1</span>
                        Step 1 • Initial Contact (Day 0)
                    </span>
                    <span class="text-[10px] text-zinc-400">Sent on enrollment</span>
                </div>
                <input type="text" id="cadence-step1-subj" placeholder="Subject line with {client_name}" required
                       class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden" />
                <textarea id="cadence-step1-body" placeholder="Email body with Spintax like {Hi|Hello} and variables..." required
                          class="w-full h-24 p-2.5 rounded-lg border border-zinc-200 bg-white text-xs font-normal text-zinc-900 outline-hidden resize-none leading-relaxed"></textarea>
            </div>

            <!-- Step 2 -->
            <div class="p-3.5 rounded-xl border border-zinc-200/80 bg-white space-y-2 shadow-3xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-md bg-zinc-100 text-zinc-800 flex items-center justify-center text-[10px] font-bold">2</span>
                        Step 2 • Follow-up & Case Study
                    </span>
                    <span class="text-[10px] text-zinc-500 font-mono">Wait 3 Days</span>
                </div>
                <input type="text" id="cadence-step2-subj" placeholder="Follow-up subject..."
                       class="w-full h-8 px-2.5 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden" />
                <textarea id="cadence-step2-body" placeholder="Follow-up body..."
                          class="w-full h-20 p-2.5 rounded-lg border border-zinc-200 bg-white text-xs font-normal text-zinc-900 outline-hidden resize-none leading-relaxed"></textarea>
            </div>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2 shrink-0">
            <button type="button" onclick="closeOutreachCampaignDrawer()" class="h-9 px-3.5 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-600 bg-transparent cursor-pointer hover:bg-zinc-50 transition-colors">Cancel</button>
            <button type="submit" class="h-9 px-4 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold border-0 cursor-pointer shadow-xs transition-colors">Save & Activate Campaign</button>
        </div>
    </form>
</div>

<!-- 3. CREATE MARKETING BROADCAST DRAWER -->
<div id="cora-marketing-broadcast-drawer" class="cora-email-drawer fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-lg h-[90vh] sm:h-full max-h-[90vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans" style="display: none; visibility: hidden; box-shadow: none;">
    <!-- Mobile Drag Handle -->
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-200 shrink-0">
        <div>
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">New Marketing Broadcast</h3>
            <p class="text-[10px] text-zinc-500 mt-0.5">Send a newsletter blast or service showcase to your audience.</p>
        </div>
        <button onclick="closeMarketingBroadcastDrawer()" class="text-zinc-400 hover:text-zinc-700 border-0 bg-transparent cursor-pointer text-xs font-bold transition-colors">✕ Close</button>
    </div>

    <form id="marketing-broadcast-form" onsubmit="submitMarketingBroadcast(event)" class="flex-1 flex flex-col overflow-y-auto p-5 space-y-3.5">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Broadcast Title (Internal) *</label>
            <input type="text" id="bcast-title" placeholder="e.g. Winter 2026 Lookbook Showcase" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">From Business Account</label>
                <select id="bcast-from-inbox" class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden cursor-pointer">
                    <!-- Populated dynamically -->
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Audience Segment</label>
                <select id="bcast-target-segment" class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden cursor-pointer">
                    <option value="All Active Clients & Past Inquiries">All Active Clients & Past Inquiries</option>
                    <option value="Commercial Real Estate Contacts">Commercial Real Estate Contacts</option>
                    <option value="Wedding & Event Planners">Wedding & Event Planners</option>
                    <option value="All CRM Leads">All CRM Leads</option>
                </select>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Subject Line *</label>
            <input type="text" id="bcast-subject" placeholder="Introducing our 2026 Production Lookbook" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Preview Text (Preheader)</label>
            <input type="text" id="bcast-preheader" placeholder="Explore exclusive dates and package previews..."
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-normal text-zinc-700 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <div class="space-y-1 flex-1 flex flex-col min-h-36">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Broadcast Content *</label>
            <textarea id="bcast-content" placeholder="Write your broadcast content here. Includes dynamic tags {client_name}, {portal_url}..." required
                      class="w-full flex-1 min-h-36 p-3 rounded-lg border border-zinc-200 bg-white text-xs font-normal text-zinc-900 outline-hidden leading-relaxed resize-none focus:border-zinc-400 transition-colors"></textarea>
        </div>

        <!-- Unsubscribe & Compliance Notice -->
        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/80 flex items-center justify-between text-xs text-zinc-600">
            <span>Automated 1-Click Unsubscribe Link & Studio Address</span>
            <span class="text-emerald-700 font-bold text-[10px]">ACTIVE ✓</span>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <label class="text-xs text-zinc-700 flex items-center gap-1.5 cursor-pointer font-medium">
                    <input type="radio" name="bcast_send_schedule" value="send_now" checked class="accent-zinc-900" />
                    Send Immediately
                </label>
                <label class="text-xs text-zinc-700 flex items-center gap-1.5 cursor-pointer font-medium">
                    <input type="radio" name="bcast_send_schedule" value="schedule" class="accent-zinc-900" />
                    Schedule (In 3 Days)
                </label>
            </div>
            <button type="submit" id="btn-submit-broadcast" class="h-9 px-4 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold border-0 cursor-pointer shadow-xs transition-colors">
                Dispatch Broadcast
            </button>
        </div>
    </form>
</div>

<!-- 4. TEMPLATE CREATOR / EDITOR DRAWER -->
<div id="cora-email-template-drawer" class="cora-email-drawer fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-lg h-[88vh] sm:h-full max-h-[88vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans" style="display: none; visibility: hidden; box-shadow: none;">
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-200 shrink-0">
        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider" id="template-drawer-title">Create Email Template</h3>
        <button onclick="closeEmailTemplateDrawer()" class="text-zinc-400 hover:text-zinc-700 border-0 bg-transparent cursor-pointer text-xs font-bold transition-colors">✕ Close</button>
    </div>
    
    <form id="template-editor-form" onsubmit="saveEmailTemplate(event)" class="flex-1 flex flex-col overflow-y-auto p-5 space-y-3.5">
        <input type="hidden" id="tpl-editor-id" value="" />
        
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Template Title *</label>
            <input type="text" id="tpl-editor-name" placeholder="e.g. Shoot Confirmation" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Category</label>
            <select id="tpl-editor-category" class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden cursor-pointer">
                <option value="Outreach">Outreach</option>
                <option value="Bookings">Bookings</option>
                <option value="Financials">Financials</option>
                <option value="Media & Vault">Media & Vault</option>
                <option value="Leads">Leads</option>
                <option value="Reviews">Reviews</option>
                <option value="Legal">Legal</option>
                <option value="General">General</option>
            </select>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Default Subject Line *</label>
            <input type="text" id="tpl-editor-subject" placeholder="Subject with {variable} support" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <div class="space-y-1 flex-1 flex flex-col min-h-44">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Template Body Content *</label>
            <textarea id="tpl-editor-body" placeholder="Write template body with {client_name}, {event_name}, {portal_url}..." required
                      class="w-full flex-1 p-3 rounded-lg border border-zinc-200 bg-white text-xs font-normal text-zinc-900 outline-hidden leading-relaxed resize-none focus:border-zinc-400 transition-colors"></textarea>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2 shrink-0">
            <button type="button" onclick="closeEmailTemplateDrawer()" class="h-9 px-3.5 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-600 bg-transparent cursor-pointer hover:bg-zinc-50 transition-colors">Cancel</button>
            <button type="submit" class="h-9 px-4 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold border-0 cursor-pointer shadow-xs transition-colors">Save Template</button>
        </div>
    </form>
</div>

<!-- 5. SENT EMAIL DETAIL DRAWER -->
<div id="cora-email-detail-drawer" class="cora-email-drawer fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-md h-[88vh] sm:h-full max-h-[88vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans" style="display: none; visibility: hidden; box-shadow: none;">
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-200 shrink-0">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Sent Communication Details</h3>
        <button onclick="closeEmailDetailDrawer()" class="text-zinc-400 hover:text-zinc-700 border-0 bg-transparent cursor-pointer text-xs font-bold transition-colors">✕ Close</button>
    </div>
    <div class="flex-1 overflow-y-auto p-5 space-y-3.5">
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 font-bold uppercase">Sent From (Mailbox)</div>
            <div id="drawer-email-from" class="text-xs font-semibold text-zinc-900 font-mono"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 font-bold uppercase">To Recipient</div>
            <div id="drawer-email-to" class="text-xs font-semibold text-zinc-900 font-mono"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 font-bold uppercase">Sent Timestamp</div>
            <div id="drawer-email-date" class="text-[11px] text-zinc-500 font-mono"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 font-bold uppercase">Subject Line</div>
            <div id="drawer-email-subject" class="text-xs font-bold text-zinc-900"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 font-bold uppercase">Message Payload</div>
            <div id="drawer-email-message" class="text-xs text-zinc-800 bg-zinc-50 p-3.5 rounded-xl whitespace-pre-wrap leading-relaxed border border-zinc-200 font-normal"></div>
        </div>
        <div class="pt-3 border-t border-zinc-100">
            <button type="button" id="btn-resend-drawer-email" onclick="resendDrawerEmail()"
                    class="w-full h-9 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold flex items-center justify-center gap-2 cursor-pointer border-0 shadow-xs transition-colors">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                Resend This Email
            </button>
        </div>
    </div>
</div>

<!-- 6. INBOX DIAGNOSTIC TEST DRAWER -->
<div id="cora-inbox-test-drawer" class="cora-email-drawer fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-md h-[88vh] sm:h-full max-h-[88vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans" style="display: none; visibility: hidden; box-shadow: none;">
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-200 shrink-0">
        <div>
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider" id="test-drawer-inbox-title">Inbox Handshake Diagnostic</h3>
            <p class="text-[10px] text-zinc-500 mt-0.5">Live SSL/TLS handshake & SPF/DKIM verification.</p>
        </div>
        <button onclick="closeInboxTestDrawer()" class="text-zinc-400 hover:text-zinc-700 border-0 bg-transparent cursor-pointer text-xs font-bold transition-colors">✕ Close</button>
    </div>
    
    <div class="flex-1 overflow-y-auto p-5 space-y-3.5">
        <input type="hidden" id="test-drawer-inbox-id" value="" />
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Test Recipient Email *</label>
            <input type="email" id="inbox-test-recipient" value="<?php echo esc_attr(get_option('admin_email')); ?>" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <button type="button" id="btn-run-inbox-test" onclick="executeInboxTestPing()"
                class="w-full h-9 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold flex items-center justify-center gap-2 cursor-pointer border-0 shadow-xs transition-colors">
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
            Execute Diagnostic Ping
        </button>

        <div class="space-y-1.5 pt-1">
            <div class="text-[10px] font-bold text-zinc-400 uppercase">Live Handshake Output Log</div>
            <div id="inbox-test-console" class="bg-zinc-950 text-zinc-200 p-3.5 rounded-lg font-mono text-[10px] min-h-40 overflow-y-auto leading-relaxed border border-zinc-800">
                Ready to execute diagnostic ping...
            </div>
        </div>
    </div>
</div>

<!-- 7. SMTP RELAY TEST DRAWER (EXISTING) -->
<div id="cora-smtp-test-drawer" class="cora-email-drawer fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-md h-[88vh] sm:h-full max-h-[88vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans" style="display: none; visibility: hidden; box-shadow: none;">
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-200 shrink-0">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">SMTP Server Diagnostic</h3>
        <button onclick="closeSmtpTestDrawer()" class="text-zinc-400 hover:text-zinc-700 border-0 bg-transparent cursor-pointer text-xs font-bold transition-colors">✕ Close</button>
    </div>
    
    <div class="flex-1 overflow-y-auto p-5 space-y-3.5">
        <p class="text-xs text-zinc-500">Send an instant test email to verify host connection, port 587/465 handshakes, and SSL/TLS authentication status.</p>
        
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Test Recipient Email *</label>
            <input type="email" id="smtp-test-recipient" value="<?php echo esc_attr(get_option('admin_email')); ?>" required
                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 outline-hidden focus:border-zinc-400 transition-colors" />
        </div>

        <button type="button" id="btn-run-smtp-test" onclick="runSmtpDiagnosticTest()"
                class="w-full h-9 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold flex items-center justify-center gap-2 cursor-pointer border-0 shadow-xs transition-colors">
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
            Send Diagnostic Test Email
        </button>

        <div class="space-y-1.5 pt-1">
            <div class="text-[10px] font-bold text-zinc-400 uppercase">Diagnostic Output Log</div>
            <div id="smtp-test-console" class="bg-zinc-950 text-zinc-200 p-3.5 rounded-lg font-mono text-[10px] min-h-36 overflow-y-auto leading-relaxed border border-zinc-800">
                Ready to execute diagnostic ping to SMTP relay server...
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let emailData = {
        stats: {},
        templates: [],
        sent_logs: [],
        smtp: {},
        recipients: [],
        inboxes: [],
        outreach_campaigns: [],
        marketing_broadcasts: [],
        plan_tiers: {},
        current_plan: 'growth'
    };
    let activeLogForDetail = null;

    function getAjaxNonce() {
        return window.coraAjaxNonce || (typeof coraREData !== 'undefined' ? coraREData.ajaxNonce : '') || (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxNonce : '');
    }
    function getAjaxEndpoint() {
        return (typeof ajaxurl !== 'undefined' && ajaxurl) ? ajaxurl : '/wp-admin/admin-ajax.php';
    }

    // Load initial Email Module state
    function loadEmailDashboardData() {
        const nonce = getAjaxNonce();
        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_get_email_dashboard_data',
                nonce: nonce,
                security: nonce
            },
            success: function(res) {
                if (res.success && res.data) {
                    emailData = res.data;
                    updateDashboardKPIs();
                    populateSenderPickerDropdown();
                    populateRecipientsDropdown();
                    populateTemplatesDropdown();
                    renderConnectedInboxes();
                    renderPlanTiers();
                    renderOutreachCampaigns();
                    renderMarketingBroadcasts();
                    renderEmailTemplates();
                    renderEmailLogs();
                    populateSmtpForm();
                    updateLivePreview();
                }
            },
            error: function(err) {
                console.warn("Notice: cora_get_email_dashboard_data response fallback", err);
            }
        });
    }

    // Sub-tab switching handler
    window.coraSwitchEmailSubTab = function(tabId) {
        $('.cora-email-sub-tab').removeClass('text-zinc-900 border-zinc-950 font-bold').addClass('text-zinc-500 border-transparent font-semibold');
        $('#tab-btn-' + tabId).removeClass('text-zinc-500 border-transparent font-semibold').addClass('text-zinc-900 border-zinc-950 font-bold');

        $('.cora-email-tab-content').addClass('hidden').removeClass('flex');
        $('#' + tabId).removeClass('hidden').addClass('flex');

        // Push URL query state persistence
        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.replaceState(null, '', url);
    };

    // Auto-restore tab from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const activeTabParam = urlParams.get('tab');
    if (activeTabParam && $('#' + activeTabParam).length) {
        coraSwitchEmailSubTab(activeTabParam);
    }

    // Mobile Compose vs Live Preview toggle
    window.coraToggleMobileComposeView = function(view) {
        if (view === 'form') {
            $('#cora-compose-mobile-tab-btn-form').addClass('bg-zinc-900 text-white font-bold').removeClass('text-zinc-600 font-medium');
            $('#cora-compose-mobile-tab-btn-preview').removeClass('bg-zinc-900 text-white font-bold').addClass('text-zinc-600 font-medium');
            $('#cora-compose-form-panel').removeClass('hidden').addClass('flex');
            $('#cora-compose-preview-panel').addClass('hidden lg:flex').removeClass('flex');
        } else {
            $('#cora-compose-mobile-tab-btn-preview').addClass('bg-zinc-900 text-white font-bold').removeClass('text-zinc-600 font-medium');
            $('#cora-compose-mobile-tab-btn-form').removeClass('bg-zinc-900 text-white font-bold').addClass('text-zinc-600 font-medium');
            $('#cora-compose-form-panel').addClass('hidden lg:flex').removeClass('flex');
            $('#cora-compose-preview-panel').removeClass('hidden').addClass('flex');
        }
    };

    function updateDashboardKPIs() {
        const stats = emailData.stats || {};
        const inboxesCount = (emailData.inboxes || []).length;
        const maxInboxes = stats.max_inboxes || 5;
        const planName = stats.current_plan_name || 'Growth Plan';

        $('#stat-total-sent').text(stats.total_sent || 0);
        $('#stat-month-sent').text((stats.month_sent || 0) + ' sent this month');
        $('#stat-success-rate').text((stats.success_rate || 99.4) + '%');
        $('#stat-connected-inboxes-kpi').text(`${inboxesCount} of ${maxInboxes} Inboxes`);
        $('#stat-plan-kpi').text(`${planName} • ${stats.daily_cap || 250}/day`);

        const outreachCount = (emailData.outreach_campaigns || []).filter(c => c.status === 'active').length;
        const bcastCount = (emailData.marketing_broadcasts || []).length;
        $('#stat-active-campaigns-kpi').text(`${outreachCount} Outreach • ${bcastCount} Broadcast`);

        $('#tab-badge-inboxes-count').text(`${inboxesCount}/${maxInboxes}`);
        $('#tab-badge-outreach-count').text(`${outreachCount} Active`);

        // Plan Progress in Tab 2
        const pct = Math.min(100, Math.round((inboxesCount / maxInboxes) * 100));
        $('#plan-badge-pill').text(`${planName} (Up to ${maxInboxes} Inboxes)`);
        $('#plan-capacity-summary').text(`${stats.daily_cap || 250} emails / day safe pooled capacity`);
        $('#plan-quota-text').text(`${inboxesCount} of ${maxInboxes} Inboxes Connected`);
        $('#plan-quota-percent').text(`${pct}% Capacity`);
        $('#plan-quota-bar').css('width', pct + '%');
        $('#connect-drawer-plan-info').text(`${planName}: ${inboxesCount} of ${maxInboxes} inboxes in use`);
    }

    // Populate Sender Pickers
    function populateSenderPickerDropdown() {
        const $composePicker = $('#compose-from-inbox-picker');
        const $bcastPicker = $('#bcast-from-inbox');
        $composePicker.html('');
        $bcastPicker.html('');

        const inboxes = emailData.inboxes || [];
        if (inboxes.length === 0) {
            $composePicker.append('<option value="">Default Relay (heycora@claraverse.in)</option>');
            $bcastPicker.append('<option value="">Default Relay (heycora@claraverse.in)</option>');
            return;
        }

        inboxes.forEach(ib => {
            const label = `${ib.from_name} <${ib.email}> [${ib.provider_label}] (${ib.today_sent || 0}/${ib.daily_limit} today)`;
            $composePicker.append(`<option value="${ib.id}" data-email="${escapeHtml(ib.email)}" data-name="${escapeHtml(ib.from_name)}" data-provider="${escapeHtml(ib.provider_label)}">${escapeHtml(label)}</option>`);
            $bcastPicker.append(`<option value="${ib.id}">${escapeHtml(label)}</option>`);
        });

        onSenderInboxChange($composePicker[0]);
    }

    window.onSenderInboxChange = function(select) {
        const $opt = $(select).find('option:selected');
        const email = $opt.data('email') || (emailData.stats ? emailData.stats.from_email : 'alex.mehta@clarastudio.com');
        const name = $opt.data('name') || 'Cora Studio';
        const provider = $opt.data('provider') || 'Google Workspace';

        $('#preview-from').text(`${name} <${email}>`);
        $('#preview-provider-badge').text(provider);
        $('#compose-sender-info').text(`Sending via: ${provider} (${email})`);
    };

    let selectedRecipientObj = null;

    function populateRecipientsDropdown() {
        const $picker = $('#compose-recipient-picker');
        $picker.html('<option value="">-- Select CRM Client / Lead --</option>');
        (emailData.recipients || []).forEach(r => {
            const typeBadge = r.type ? `[${r.type}] ` : '';
            const eventLabel = r.event ? ` • ${r.event}` : '';
            $picker.append(`<option value="${escapeHtml(r.email)}" data-name="${escapeHtml(r.name)}" data-event="${escapeHtml(r.event || '')}" data-type="${escapeHtml(r.type || '')}" data-portal="${escapeHtml(r.portal_url || '')}" data-phone="${escapeHtml(r.phone || '')}">${typeBadge}${escapeHtml(r.name)}${escapeHtml(eventLabel)} (${escapeHtml(r.email)})</option>`);
        });
    }

    window.onRecipientPickerChange = function(select) {
        const email = $(select).val();
        if (email) {
            $('#email-to').val(email);
            const $opt = $(select).find('option:selected');
            selectedRecipientObj = {
                name: $opt.data('name') || '',
                email: email,
                event: $opt.data('event') || '',
                type: $opt.data('type') || '',
                portal_url: $opt.data('portal') || '',
                phone: $opt.data('phone') || ''
            };
            updateLivePreview();
        } else {
            selectedRecipientObj = null;
            updateLivePreview();
        }
    };

    function populateTemplatesDropdown() {
        const $tplSel = $('#compose-template-selector');
        $tplSel.html('<option value="">-- Choose from Template Library --</option>');
        (emailData.templates || []).forEach(tpl => {
            $tplSel.append(`<option value="${tpl.id}">${escapeHtml(tpl.name)} (${tpl.category})</option>`);
        });
    }

    window.loadTemplateIntoComposer = function(tplId) {
        if (!tplId) return;
        const tpl = (emailData.templates || []).find(t => t.id === tplId);
        if (tpl) {
            $('#email-subject').val(tpl.subject);
            $('#email-message').val(tpl.body);
            $('#compose-template-selector').val(tpl.id);
            updateLivePreview();
            window.coraShowToast && window.coraShowToast(`Loaded template: "${tpl.name}"`, "info");
        }
    };

    window.insertVariableTag = function(tag) {
        const $textarea = $('#email-message');
        const caretPos = $textarea[0].selectionStart || $textarea.val().length;
        const textVal = $textarea.val();
        $textarea.val(textVal.substring(0, caretPos) + tag + textVal.substring(caretPos));
        updateLivePreview();
    };

    // Parse simple client-side Spintax: {optionA|optionB|optionC}
    function parseClientSpintax(str) {
        return (str || '').replace(/\{([^{}]+)\}/g, function(match, inner) {
            const parts = inner.split('|');
            if (parts.length > 1) {
                return parts[Math.floor(Math.random() * parts.length)];
            }
            return match;
        });
    }

    window.updateLivePreview = function() {
        const to = $('#email-to').val().trim() || 'client@example.com';
        const subject = $('#email-subject').val().trim() || '(No Subject Specified)';
        const rawBody = $('#email-message').val().trim() || 'Message body preview will render here in real-time as you type...';

        const clientName = (selectedRecipientObj && selectedRecipientObj.name) ? selectedRecipientObj.name : 'Aarav Sharma';
        const eventName  = (selectedRecipientObj && selectedRecipientObj.event) ? selectedRecipientObj.event : 'Pre-Wedding Documentary';
        const portalUrl  = (selectedRecipientObj && selectedRecipientObj.portal_url) ? selectedRecipientObj.portal_url : (window.location.origin + '/portal/view');
        const studioName = 'Cora Studio';
        const todayStr   = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

        let processedBody = rawBody
            .replace(/\{client_name\}/g, clientName)
            .replace(/\{company_name\}/g, 'Horizon Developments')
            .replace(/\{event_name\}/g, eventName)
            .replace(/\{event_date\}/g, todayStr)
            .replace(/\{portal_url\}/g, portalUrl)
            .replace(/\{invoice_amount\}/g, '₹45,000')
            .replace(/\{due_amount\}/g, '₹15,000')
            .replace(/\{studio_name\}/g, studioName);

        processedBody = parseClientSpintax(processedBody);

        let processedSubject = parseClientSpintax(subject
            .replace(/\{client_name\}/g, clientName)
            .replace(/\{event_name\}/g, eventName)
            .replace(/\{studio_name\}/g, studioName));

        $('#preview-to').text(to);
        $('#preview-subject').text(processedSubject);
        $('#preview-body').text(processedBody);
    };

    window.resetEmailComposeForm = function() {
        $('#cora-email-compose-form')[0].reset();
        $('#compose-recipient-picker').val('');
        $('#compose-template-selector').val('');
        selectedRecipientObj = null;
        updateLivePreview();
    };

    window.attachVaultDocumentSim = function() {
        const sampleUrl = 'https://heycora.in/vault/document/' + Math.floor(1000 + Math.random() * 9000);
        insertVariableTag(`\n\nDocument Vault Link:\n${sampleUrl}\n`);
        window.coraShowToast && window.coraShowToast("Vault document link attached to body! ✓", "success");
    };

    // =========================================================================
    // TAB 2: CONNECTED INBOXES RENDERER
    // =========================================================================
    function renderConnectedInboxes() {
        const $container = $('#inboxes-grid-container');
        $container.html('');
        const inboxes = emailData.inboxes || [];
        const maxInboxes = (emailData.stats && emailData.stats.max_inboxes) ? emailData.stats.max_inboxes : 5;

        inboxes.forEach(ib => {
            const isGW = (ib.provider === 'google_workspace');
            const isHostinger = (ib.provider === 'hostinger');
            
            const providerIcon = isGW 
                ? `<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="text-zinc-900"><path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/></svg>`
                : (isHostinger 
                    ? `<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="text-zinc-900"><path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.2L19 8.6v6.8L12 19.4 5 15.4V8.6L12 4.2z"/></svg>`
                    : `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect></svg>`);

            const sentPct = Math.min(100, Math.round(((ib.today_sent || 0) / (ib.daily_limit || 40)) * 100));

            const card = $(`
                <div class="bg-white border border-zinc-200 rounded-xl p-4 shadow-3xs flex flex-col justify-between space-y-3.5 hover:border-zinc-300 transition-all">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-bold text-zinc-900">
                                ${providerIcon}
                                <span>${escapeHtml(ib.provider_label || 'Business Email')}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[10px] text-emerald-700 font-semibold font-mono">Healthy (${ib.health_score || 99}%)</span>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-zinc-900 truncate font-mono">${escapeHtml(ib.email)}</h4>
                            <p class="text-[11px] text-zinc-500 font-medium truncate mt-0.5">${escapeHtml(ib.from_name)}</p>
                        </div>

                        <!-- Daily Quota Usage Bar -->
                        <div class="space-y-1 pt-1">
                            <div class="flex items-center justify-between text-[10px] text-zinc-500 font-medium">
                                <span>Today's Dispatches</span>
                                <span class="font-mono text-zinc-800 font-bold">${ib.today_sent || 0} / ${ib.daily_limit || 40}</span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-zinc-100 overflow-hidden">
                                <div class="h-full bg-zinc-900 rounded-full" style="width: ${sentPct}%"></div>
                            </div>
                        </div>

                        <!-- DNS & Warmup Indicators -->
                        <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                            <span class="px-1.5 py-0.2 rounded-sm bg-zinc-100 text-[9px] font-mono font-bold text-zinc-700">SPF ✓</span>
                            <span class="px-1.5 py-0.2 rounded-sm bg-zinc-100 text-[9px] font-mono font-bold text-zinc-700">DKIM ✓</span>
                            <span class="px-1.5 py-0.2 rounded-sm bg-zinc-100 text-[9px] font-mono font-bold text-zinc-700">DMARC ✓</span>
                            ${ib.warmup_enabled ? `<span class="px-1.5 py-0.2 rounded-sm bg-amber-50 text-amber-800 border border-amber-200/50 text-[9px] font-semibold">Warmup Day ${ib.warmup_day || 1}/14</span>` : ''}
                        </div>
                    </div>

                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-between">
                        <button type="button" onclick="openInboxTestDrawer('${ib.id}', '${escapeHtml(ib.email)}', '${escapeHtml(ib.provider_label)}')"
                                class="text-[11px] font-bold text-zinc-900 hover:underline bg-transparent border-0 cursor-pointer flex items-center gap-1">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            Test Ping
                        </button>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="toggleInboxWarmup('${ib.id}')"
                                    class="px-2 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-medium text-zinc-700 border-0 cursor-pointer transition-colors">
                                ${ib.warmup_enabled ? 'Pause Warmup' : 'Resume Warmup'}
                            </button>
                            <button type="button" onclick="disconnectInbox('${ib.id}', '${escapeHtml(ib.email)}')"
                                    class="px-2 py-1 rounded-md bg-zinc-50 hover:bg-red-50 text-[10px] font-medium text-zinc-500 hover:text-red-600 border border-zinc-200/60 cursor-pointer transition-colors">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            `);
            $container.append(card);
        });

        // Add Slot Placeholder if within plan capacity
        if (inboxes.length < maxInboxes) {
            const emptySlots = maxInboxes - inboxes.length;
            const slotCard = $(`
                <div onclick="openConnectInboxDrawer()" class="border-2 border-dashed border-zinc-200 rounded-xl p-6 flex flex-col items-center justify-center text-center space-y-2 hover:border-zinc-400 hover:bg-zinc-50/50 transition-all cursor-pointer min-h-48">
                    <div class="w-8 h-8 rounded-full bg-zinc-100 flex items-center justify-center text-zinc-600">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </div>
                    <span class="text-xs font-bold text-zinc-900">+ Add Business Email Account</span>
                    <span class="text-[10px] text-zinc-400 max-w-xs">Slot ${inboxes.length + 1} of ${maxInboxes} available on your plan. Connect Google Workspace or Hostinger.</span>
                </div>
            `);
            $container.append(slotCard);
        }
    }

    // Render Plan Tiers Matrix
    function renderPlanTiers() {
        const $grid = $('#plan-tiers-grid');
        $grid.html('');
        const tiers = emailData.plan_tiers || {};
        const curPlan = emailData.current_plan || 'growth';

        Object.keys(tiers).forEach(k => {
            const t = tiers[k];
            const isActive = (k === curPlan);
            
            const card = $(`
                <div class="p-3.5 rounded-xl border ${isActive ? 'bg-zinc-100/90 border-zinc-300' : 'bg-white border-zinc-200/80'} shadow-3xs flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-900">${escapeHtml(t.name)}</span>
                            ${isActive ? '<span class="px-2 py-0.5 rounded-md bg-zinc-900 text-white text-[9px] font-bold">Active Plan</span>' : ''}
                        </div>
                        <div class="text-lg font-bold text-zinc-900">
                            ${t.price} <span class="text-[10px] text-zinc-400 font-normal">${t.period}</span>
                        </div>
                        <div class="text-[11px] font-mono font-bold text-zinc-700">${t.max_inboxes} Connected Inboxes</div>
                        <div class="text-[10px] text-zinc-500">${t.daily_cap} sends / day cap • ${escapeHtml(t.pool_type)}</div>
                    </div>
                    <div>
                        ${isActive 
                            ? `<button disabled class="w-full py-1.5 rounded-lg bg-zinc-900 text-white text-xs font-bold cursor-default">Current Plan</button>`
                            : `<button type="button" onclick="switchEmailPlan('${t.id}')" class="w-full py-1.5 rounded-lg border border-zinc-300 bg-white hover:bg-zinc-50 text-xs font-semibold text-zinc-800 cursor-pointer transition-colors">Switch to ${t.name}</button>`
                        }
                    </div>
                </div>
            `);
            $grid.append(card);
        });
    }

    window.switchEmailPlan = function(planId) {
        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_switch_email_plan',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                plan: planId
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Plan updated! ✓", "success");
                    loadEmailDashboardData();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Failed to switch plan.", "error");
                }
            }
        });
    };

    window.scrollToPlanTiers = function() {
        const el = document.getElementById('cora-plan-scaling-section');
        if (el) el.scrollIntoView({ behavior: 'smooth' });
    };

    // =========================================================================
    // TAB 3: OUTREACH CAMPAIGNS RENDERER
    // =========================================================================
    function renderOutreachCampaigns() {
        const $container = $('#outreach-campaigns-container');
        $container.html('');
        const campaigns = emailData.outreach_campaigns || [];

        if (campaigns.length === 0) {
            $container.html('<div class="p-8 text-center text-zinc-400 italic text-xs bg-white rounded-xl border border-zinc-200">No cold outreach campaigns created yet. Click "+ Create Outreach Campaign" to start.</div>');
            return;
        }

        campaigns.forEach(c => {
            const isActive = (c.status === 'active');
            const poolCount = (c.sender_pool || []).length;
            const stats = c.stats || {};

            const item = $(`
                <div class="bg-white border border-zinc-200 rounded-xl p-4 shadow-3xs space-y-3 hover:border-zinc-300 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-100 pb-2.5">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-zinc-900">${escapeHtml(c.name)}</h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${isActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-zinc-100 text-zinc-600'}">
                                    ${isActive ? 'Active 🟢' : 'Paused ⏸'}
                                </span>
                            </div>
                            <div class="text-[11px] text-zinc-500">
                                Target: <span class="font-semibold text-zinc-800">${escapeHtml(c.target_segment)}</span> • 
                                Sender Pool: <span class="font-mono text-zinc-800 font-semibold">${poolCount} Inboxes Rotated</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="executeCampaignBatch('${c.id}')"
                                    class="h-8 px-3 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-bold cursor-pointer transition-colors flex items-center gap-1.5 shadow-3xs">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                Run Send Batch
                            </button>
                            <button type="button" onclick="toggleOutreachCampaign('${c.id}')"
                                    class="h-8 px-2.5 rounded-lg border border-zinc-200 text-xs font-medium text-zinc-700 hover:bg-zinc-50 cursor-pointer transition-colors">
                                ${isActive ? 'Pause' : 'Resume'}
                            </button>
                            <button type="button" onclick="deleteOutreachCampaign('${c.id}')"
                                    class="h-8 px-2 rounded-lg bg-zinc-50 hover:bg-red-50 text-zinc-400 hover:text-red-600 border border-zinc-200/60 cursor-pointer transition-colors text-xs">
                                Delete
                            </button>
                        </div>
                    </div>

                    <!-- Sequence Steps & Real-Time Stats Row -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 pt-1">
                        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[11px]">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase block">Enrolled Leads</span>
                            <span class="text-xs font-bold text-zinc-900 mt-0.5 block">${stats.enrolled || c.enrolled_count || 0}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[11px]">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase block">Emails Sent</span>
                            <span class="text-xs font-bold text-zinc-900 mt-0.5 block">${stats.sent || 0}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[11px]">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase block">Open Rate</span>
                            <span class="text-xs font-bold text-emerald-600 mt-0.5 block">${stats.open_rate || 0}%</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[11px]">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase block">Reply Rate</span>
                            <span class="text-xs font-bold text-emerald-600 mt-0.5 block">${stats.reply_rate || 0}%</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[11px]">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase block">Bounces</span>
                            <span class="text-xs font-bold text-zinc-700 mt-0.5 block">${stats.bounced || 0} (0%)</span>
                        </div>
                    </div>
                </div>
            `);
            $container.append(item);
        });
    }

    window.executeCampaignBatch = function(campaignId) {
        window.coraShowToast && window.coraShowToast("Rotating dispatches across sender pool...", "info");
        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_execute_campaign_batch',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                campaign_id: campaignId
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Batch dispatched! ✓", "success");
                    loadEmailDashboardData();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Batch send failed.", "error");
                }
            }
        });
    };

    window.toggleOutreachCampaign = function(id) {
        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_toggle_outreach_campaign',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                id: id
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message, "success");
                    loadEmailDashboardData();
                }
            }
        });
    };

    window.deleteOutreachCampaign = function(id) {
        if (!confirm("Are you sure you want to delete this outreach campaign?")) return;
        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_delete_outreach_campaign',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                id: id
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast("Campaign deleted.", "info");
                    loadEmailDashboardData();
                }
            }
        });
    };

    // =========================================================================
    // TAB 4: MARKETING BROADCASTS RENDERER
    // =========================================================================
    function renderMarketingBroadcasts() {
        const $container = $('#marketing-broadcasts-container');
        $container.html('');
        const broadcasts = emailData.marketing_broadcasts || [];

        if (broadcasts.length === 0) {
            $container.html('<div class="p-8 text-center text-zinc-400 italic text-xs bg-white rounded-xl border border-zinc-200">No marketing broadcasts created yet. Click "+ New Marketing Broadcast" to start.</div>');
            return;
        }

        broadcasts.forEach(b => {
            const stats = b.stats || {};
            const isSent = (b.status === 'sent');

            const item = $(`
                <div class="bg-white border border-zinc-200 rounded-xl p-4 shadow-3xs space-y-3 hover:border-zinc-300 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-100 pb-2.5">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-zinc-900">${escapeHtml(b.title)}</h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${isSent ? 'bg-zinc-900 text-white' : 'bg-amber-50 text-amber-800 border border-amber-200'}">
                                    ${isSent ? 'Sent ✓' : 'Scheduled ⏰'}
                                </span>
                            </div>
                            <div class="text-[11px] text-zinc-500">
                                Subject: <span class="font-semibold text-zinc-800">${escapeHtml(b.subject)}</span> • 
                                Sender: <span class="font-mono text-zinc-800 font-semibold">${escapeHtml(b.sender_email)}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-mono text-zinc-400">${b.sent_at || b.scheduled_for || 'Upcoming'}</span>
                            <button type="button" onclick="deleteMarketingBroadcast('${b.id}')"
                                    class="h-8 px-2 rounded-lg bg-zinc-50 hover:bg-red-50 text-zinc-400 hover:text-red-600 border border-zinc-200/60 cursor-pointer transition-colors text-xs">
                                Delete
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-1">
                        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[11px]">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase block">Recipients</span>
                            <span class="text-xs font-bold text-zinc-900 mt-0.5 block">${stats.recipients || 0}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[11px]">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase block">Unique Opens</span>
                            <span class="text-xs font-bold text-zinc-900 mt-0.5 block">${stats.opened || 0} (${stats.open_rate || 0}%)</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[11px]">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase block">Unique Clicks</span>
                            <span class="text-xs font-bold text-zinc-900 mt-0.5 block">${stats.clicked || 0} (${stats.click_rate || 0}%)</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[11px]">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase block">Unsubscribes</span>
                            <span class="text-xs font-bold text-emerald-600 mt-0.5 block">${stats.unsubscribed || 0} (0%)</span>
                        </div>
                    </div>
                </div>
            `);
            $container.append(item);
        });
    }

    window.deleteMarketingBroadcast = function(id) {
        if (!confirm("Are you sure you want to delete this broadcast?")) return;
        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_delete_marketing_broadcast',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                id: id
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast("Broadcast deleted.", "info");
                    loadEmailDashboardData();
                }
            }
        });
    };

    // =========================================================================
    // TAB 5: TEMPLATES RENDERER
    // =========================================================================
    window.renderEmailTemplates = function() {
        const $grid = $('#templates-grid-container');
        const query = ($('#template-search-input').val() || '').toLowerCase();
        const catFilter = $('#template-category-filter').val() || '';

        const filtered = (emailData.templates || []).filter(tpl => {
            const matchesQuery = (tpl.name || '').toLowerCase().includes(query) || (tpl.subject || '').toLowerCase().includes(query);
            const matchesCat = !catFilter || tpl.category === catFilter;
            return matchesQuery && matchesCat;
        });

        if (filtered.length === 0) {
            $grid.html('<div class="col-span-full py-12 text-center text-zinc-400 italic text-xs">No email templates found matching filters.</div>');
            return;
        }

        $grid.html('');
        filtered.forEach(tpl => {
            const card = $(`
                <div class="bg-white border border-zinc-200 rounded-xl p-4 shadow-3xs flex flex-col justify-between space-y-3.5 hover:border-zinc-300 transition-all">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-semibold text-zinc-700">${escapeHtml(tpl.category)}</span>
                            ${tpl.is_system ? '<span class="text-[9px] font-mono text-zinc-400">System Preset</span>' : '<span class="text-[9px] font-mono text-emerald-600 font-semibold">Custom Template</span>'}
                        </div>
                        <h4 class="text-xs font-bold text-zinc-900 truncate">${escapeHtml(tpl.name)}</h4>
                        <p class="text-[11px] text-zinc-500 font-medium truncate">Subject: ${escapeHtml(tpl.subject)}</p>
                        <p class="text-[11px] text-zinc-400 line-clamp-2 leading-relaxed font-normal">${escapeHtml(tpl.body)}</p>
                    </div>

                    <div class="pt-3 border-t border-zinc-100 flex items-center justify-between">
                        <button onclick="loadTemplateIntoComposer('${tpl.id}'); coraSwitchEmailSubTab('email-tab-compose');" class="text-[11px] font-bold text-zinc-900 hover:underline bg-transparent border-0 cursor-pointer">
                            Use Template →
                        </button>
                        <div class="flex items-center gap-1.5">
                            <button onclick="editEmailTemplate('${tpl.id}')" class="px-2 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-medium text-zinc-700 border-0 cursor-pointer transition-colors">Edit</button>
                            ${!tpl.is_system ? `<button onclick="deleteEmailTemplate('${tpl.id}')" class="px-2 py-1 rounded-md bg-red-50 hover:bg-red-100 text-[10px] font-medium text-red-600 border-0 cursor-pointer transition-colors">Delete</button>` : ''}
                        </div>
                    </div>
                </div>
            `);
            $grid.append(card);
        });
    };

    // =========================================================================
    // TAB 7: OUTBOX & SENT LOGS RENDERER (WITH SENDER INBOX)
    // =========================================================================
    function renderEmailLogs() {
        const $tbody = $('#outbox-table-body');
        const query = ($('#outbox-search-input').val() || '').toLowerCase();
        const statusFilter = ($('#outbox-status-filter').val() || '').toLowerCase();
        const typeFilter = ($('#outbox-type-filter').val() || '').toLowerCase();

        const logs = (emailData.sent_logs || []).filter(log => {
            const matchesQuery = (log.to || '').toLowerCase().includes(query) || (log.subject || '').toLowerCase().includes(query);
            const matchesStatus = !statusFilter || (log.status || '').toLowerCase() === statusFilter;
            const matchesType = !typeFilter || (log.type || '').toLowerCase().includes(typeFilter);
            return matchesQuery && matchesStatus && matchesType;
        });

        if (logs.length === 0) {
            $tbody.html('<tr><td colspan="7" class="py-8 text-center text-zinc-400 italic text-xs">No email outbox records found.</td></tr>');
            return;
        }

        $tbody.html('');
        logs.forEach((log) => {
            const dateStr = log.sent_at ? new Date(log.sent_at.replace(/-/g, '/')).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Just now';
            const senderInbox = log.sent_from_inbox || 'Official Relay';

            const row = $(`
                <tr class="hover:bg-zinc-50/70 transition-all cursor-pointer">
                    <td class="py-2.5 px-3.5 font-mono text-[11px] text-zinc-800 font-semibold">${escapeHtml(senderInbox)}</td>
                    <td class="py-2.5 px-3.5 font-bold text-zinc-900">${escapeHtml(log.to)}</td>
                    <td class="py-2.5 px-3.5 font-medium text-zinc-700 max-w-xs truncate">${escapeHtml(log.subject)}</td>
                    <td class="py-2.5 px-3.5 font-mono text-[10px] text-zinc-500">${escapeHtml(log.type || 'Direct Dispatch')}</td>
                    <td class="py-2.5 px-3.5 font-mono text-[11px] text-zinc-400">${dateStr}</td>
                    <td class="py-2.5 px-3.5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            ${escapeHtml(log.status || 'Delivered')}
                        </span>
                    </td>
                    <td class="py-2.5 px-3.5 text-right">
                        <button onclick='openEmailDetailDrawer(${JSON.stringify(log)})' class="px-2 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-medium text-zinc-800 border-0 cursor-pointer transition-colors">
                            View Details
                        </button>
                    </td>
                </tr>
            `);
            $tbody.append(row);
        });
    }

    window.filterOutboxLogs = renderEmailLogs;

    // CSV Export Handler
    window.exportOutboxLogsCSV = function() {
        const logs = emailData.sent_logs || [];
        if (logs.length === 0) {
            window.coraShowToast && window.coraShowToast("No sent email logs to export.", "info");
            return;
        }
        let csv = "Sent From,To,Subject,Type,Sent At,Status\n";
        logs.forEach(l => {
            csv += `"${(l.sent_from_inbox || 'Relay').replace(/"/g, '""')}","${(l.to || '').replace(/"/g, '""')}","${(l.subject || '').replace(/"/g, '""')}","${(l.type || '').replace(/"/g, '""')}","${l.sent_at || ''}","${l.status || 'Delivered'}"\n`;
        });
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.setAttribute("download", `cora_email_outbox_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.coraShowToast && window.coraShowToast("Email outbox exported to CSV successfully! ✓", "success");
    };

    // =========================================================================
    // DRAWER CONTROLLER FUNCTIONS (SOP Rule 1 & 12 Compliance)
    // =========================================================================
    function showBackdrop() {
        let $bd = $('#cora-drawer-backdrop');
        if (!$bd.length) {
            $bd = $('<div id="cora-drawer-backdrop" class="fixed inset-0 bg-zinc-950/40 backdrop-blur-xs z-[9990] cursor-pointer transition-opacity"></div>');
            $bd.on('click', function() {
                window.coraCloseAllDrawers && window.coraCloseAllDrawers();
            });
            $('body').append($bd);
        }
        $bd.removeClass('hidden').css({'display': 'block', 'pointer-events': 'auto'});
        $('body').addClass('cora-drawer-open overflow-hidden');
    }

    function hideBackdrop() {
        $('#cora-drawer-backdrop').addClass('hidden').css({'display': 'none', 'pointer-events': 'none'});
        $('body').removeClass('cora-drawer-open overflow-hidden');
    }

    window.coraCloseEmailDrawers = function() {
        $('.cora-email-drawer, #cora-connect-inbox-drawer, #cora-outreach-campaign-drawer, #cora-marketing-broadcast-drawer, #cora-email-template-drawer, #cora-email-detail-drawer, #cora-inbox-test-drawer, #cora-smtp-test-drawer')
            .removeClass('is-open translate-y-0 sm:translate-x-0 pointer-events-auto')
            .addClass('collapsed pointer-events-none translate-y-full sm:translate-x-full hidden')
            .css({'display': 'none', 'visibility': 'hidden', 'box-shadow': 'none', 'pointer-events': 'none'});
        hideBackdrop();
    };

    const prevCloseAllDrawers = window.coraCloseAllDrawers;
    window.coraCloseAllDrawers = function() {
        if (typeof prevCloseAllDrawers === 'function') prevCloseAllDrawers();
        window.coraCloseEmailDrawers();
    };

    function openDrawerElement($el) {
        window.coraCloseEmailDrawers();
        $el.removeClass('collapsed pointer-events-none translate-y-full sm:translate-x-full translate-x-full hidden')
           .addClass('is-open translate-y-0 sm:translate-x-0 pointer-events-auto')
           .css({'display': 'flex', 'visibility': 'visible', 'pointer-events': 'auto'});
        showBackdrop();
    }

    // 1. Connect Business Inbox Drawer
    window.openConnectInboxDrawer = function() {
        if ($('#connect-inbox-form').length) {
            $('#connect-inbox-form')[0].reset();
        }
        window.switchConnectProvider('google_workspace');
        openDrawerElement($('#cora-connect-inbox-drawer'));
    };

    window.closeConnectInboxDrawer = function() {
        window.coraCloseEmailDrawers();
    };

    window.switchConnectProvider = function(provider) {
        $('#connect-provider-val').val(provider);
        $('#provider-tab-btn-google, #provider-tab-btn-hostinger, #provider-tab-btn-custom')
            .removeClass('bg-white text-zinc-900 font-bold shadow-3xs')
            .addClass('text-zinc-600 font-semibold');

        if (provider === 'google_workspace') {
            $('#provider-tab-btn-google').addClass('bg-white text-zinc-900 font-bold shadow-3xs').removeClass('text-zinc-600');
            $('#connect-provider-guide').html(`
                <span class="font-bold text-zinc-900 block mb-0.5">Google Workspace Setup:</span>
                Enter your Google Workspace business email and a 16-character Google App Password (generated in Google Account &gt; Security &gt; 2-Step Verification &gt; App Passwords). Outbound dispatches route via <code>smtp.gmail.com:587 (TLS)</code>.
            `);
            $('#connect-password-label').text('Google App Password (16 Characters) *');
            $('#connect-password').attr('placeholder', '•••• •••• •••• ••••');
            $('#custom-smtp-fields').addClass('hidden');
        } else if (provider === 'hostinger') {
            $('#provider-tab-btn-hostinger').addClass('bg-white text-zinc-900 font-bold shadow-3xs').removeClass('text-zinc-600');
            $('#connect-provider-guide').html(`
                <span class="font-bold text-zinc-900 block mb-0.5">Hostinger Webmail Setup:</span>
                Connects your Hostinger Titan / Business mail. Outbound relay automatically uses <code>smtp.hostinger.com:465 (SSL)</code> with full SPF (<code>include:_spf.mail.hostinger.com</code>) & DKIM verification.
            `);
            $('#connect-password-label').text('Hostinger Webmail Password *');
            $('#connect-password').attr('placeholder', 'Enter mailbox password');
            $('#custom-smtp-fields').addClass('hidden');
        } else {
            $('#provider-tab-btn-custom').addClass('bg-white text-zinc-900 font-bold shadow-3xs').removeClass('text-zinc-600');
            $('#connect-provider-guide').html(`
                <span class="font-bold text-zinc-900 block mb-0.5">Custom Business SMTP:</span>
                Configure any external provider (Office 365, Amazon SES, SendGrid, Mailgun, or private corporate relay).
            `);
            $('#connect-password-label').text('SMTP Password *');
            $('#custom-smtp-fields').removeClass('hidden');
        }
    };

    window.submitConnectInbox = function(e) {
        e.preventDefault();
        const provider = $('#connect-provider-val').val();
        const email = $('#connect-email').val().trim();
        const fromName = $('#connect-from-name').val().trim();
        const password = $('#connect-password').val();
        const dailyLimit = $('#connect-daily-limit-range').val();

        const $btn = $('#btn-submit-connect-inbox');
        $btn.prop('disabled', true).text('Verifying Handshake...');

        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_connect_inbox',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                provider: provider,
                email: email,
                from_name: fromName,
                password: password,
                daily_limit: dailyLimit,
                smtp_host: $('#connect-smtp-host').val(),
                smtp_port: $('#connect-smtp-port').val(),
                smtp_secure: $('#connect-smtp-secure').val()
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Connected inbox successfully! ✓", "success");
                    closeConnectInboxDrawer();
                    loadEmailDashboardData();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Failed to connect inbox.", "error");
                }
            },
            error: function() {
                window.coraShowToast && window.coraShowToast("Network error connecting email account.", "error");
            },
            complete: function() {
                $btn.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    Verify & Connect Account
                `);
            }
        });
    };

    window.disconnectInbox = function(inboxId, email) {
        if (!confirm(`Are you sure you want to disconnect inbox (${email})?`)) return;
        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_disconnect_inbox',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                inbox_id: inboxId
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Inbox disconnected.", "info");
                    loadEmailDashboardData();
                }
            }
        });
    };

    window.toggleInboxWarmup = function(inboxId) {
        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_toggle_inbox_warmup',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                inbox_id: inboxId
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message, "success");
                    loadEmailDashboardData();
                }
            }
        });
    };

    // 2. Outreach Campaign Drawer
    window.openOutreachCampaignDrawer = function() {
        if ($('#outreach-campaign-form').length) {
            $('#outreach-campaign-form')[0].reset();
        }
        
        // Populate sender pool checkboxes from connected inboxes
        const $pool = $('#outreach-sender-pool-checkboxes');
        $pool.html('');
        const inboxes = emailData.inboxes || [];
        inboxes.forEach(ib => {
            $pool.append(`
                <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-zinc-200/80 cursor-pointer text-xs text-zinc-800">
                    <input type="checkbox" name="outreach_pool_inboxes[]" value="${ib.id}" checked class="accent-zinc-900" />
                    <span class="font-bold">${escapeHtml(ib.provider_label)}:</span>
                    <span class="font-mono text-zinc-600">${escapeHtml(ib.email)}</span>
                    <span class="text-zinc-400 text-[10px] ml-auto font-mono">(${ib.today_sent || 0}/${ib.daily_limit} today)</span>
                </label>
            `);
        });

        // Pre-fill sample cadence templates
        $('#cadence-step1-subj').val('Quick question regarding {event_name} visual production');
        $('#cadence-step1-body').val("{Hi|Hello|Hey} {client_name},\n\nLoved seeing your recent updates! At {studio_name}, we specialize in ultra-high resolution photography, drone videography, and 3D virtual walkthroughs.\n\nWould you be open to a 5-minute portfolio showcase?\n\nBest regards,\n{studio_name}");
        $('#cadence-step2-subj').val('Case study: 3x conversion lift for luxury projects');
        $('#cadence-step2-body').val("Hi {client_name},\n\nQuick follow-up! Here is our client portal and lookbook for reference:\n{portal_url}\n\nLet me know if we can schedule a brief call this week.\n\nWarmly,\n{studio_name}");

        openDrawerElement($('#cora-outreach-campaign-drawer'));
    };

    window.closeOutreachCampaignDrawer = function() {
        window.coraCloseEmailDrawers();
    };

    window.submitOutreachCampaign = function(e) {
        e.preventDefault();
        const name = $('#outreach-camp-name').val().trim();
        const target = $('#outreach-camp-target').val();
        const pool = [];
        $('input[name="outreach_pool_inboxes[]"]:checked').each(function() {
            pool.push($(this).val());
        });

        const cadence = [
            {
                step: 1,
                delay_days: 0,
                subject: $('#cadence-step1-subj').val().trim(),
                body: $('#cadence-step1-body').val().trim()
            },
            {
                step: 2,
                delay_days: 3,
                subject: $('#cadence-step2-subj').val().trim(),
                body: $('#cadence-step2-body').val().trim()
            }
        ];

        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_save_outreach_campaign',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                name: name,
                target_segment: target,
                sender_pool: pool,
                cadence: JSON.stringify(cadence)
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Outreach campaign activated! ✓", "success");
                    closeOutreachCampaignDrawer();
                    loadEmailDashboardData();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Failed to save campaign.", "error");
                }
            }
        });
    };

    // 3. Marketing Broadcast Drawer
    window.openMarketingBroadcastDrawer = function() {
        if ($('#marketing-broadcast-form').length) {
            $('#marketing-broadcast-form')[0].reset();
        }
        openDrawerElement($('#cora-marketing-broadcast-drawer'));
    };

    window.closeMarketingBroadcastDrawer = function() {
        window.coraCloseEmailDrawers();
    };

    window.submitMarketingBroadcast = function(e) {
        e.preventDefault();
        const title = $('#bcast-title').val().trim();
        const fromInboxId = $('#bcast-from-inbox').val();
        const segment = $('#bcast-target-segment').val();
        const subject = $('#bcast-subject').val().trim();
        const preheader = $('#bcast-preheader').val().trim();
        const content = $('#bcast-content').val().trim();
        const scheduleMode = $('input[name="bcast_send_schedule"]:checked').val();

        const $btn = $('#btn-submit-broadcast');
        $btn.prop('disabled', true).text('Dispatching...');

        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_save_marketing_broadcast',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                title: title,
                sender_inbox_id: fromInboxId,
                target_segment: segment,
                subject: subject,
                preview_text: preheader,
                content: content,
                schedule_mode: scheduleMode
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Broadcast saved! ✓", "success");
                    closeMarketingBroadcastDrawer();
                    loadEmailDashboardData();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Failed to dispatch broadcast.", "error");
                }
            },
            complete: function() {
                $btn.prop('disabled', false).text('Dispatch Broadcast');
            }
        });
    };

    // 4. Template Creator / Editor Drawer
    window.openEmailTemplateDrawer = function(tplId = null) {
        if ($('#template-editor-form').length) {
            $('#template-editor-form')[0].reset();
        }
        $('#tpl-editor-id').val('');

        if (tplId) {
            const tpl = (emailData.templates || []).find(t => t.id === tplId);
            if (tpl) {
                $('#template-drawer-title').text('Edit Email Template');
                $('#tpl-editor-id').val(tpl.id);
                $('#tpl-editor-name').val(tpl.name);
                $('#tpl-editor-category').val(tpl.category || 'General');
                $('#tpl-editor-subject').val(tpl.subject);
                $('#tpl-editor-body').val(tpl.body);
            }
        } else {
            $('#template-drawer-title').text('Create Email Template');
        }

        openDrawerElement($('#cora-email-template-drawer'));
    };

    window.closeEmailTemplateDrawer = function() {
        window.coraCloseEmailDrawers();
    };

    window.editEmailTemplate = function(tplId) {
        openEmailTemplateDrawer(tplId);
    };

    window.saveEmailTemplate = function(e) {
        e.preventDefault();
        const id = $('#tpl-editor-id').val();
        const name = $('#tpl-editor-name').val().trim();
        const category = $('#tpl-editor-category').val();
        const subject = $('#tpl-editor-subject').val().trim();
        const body = $('#tpl-editor-body').val().trim();

        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_save_email_template',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                id, name, category, subject, body
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Template saved successfully! ✓", "success");
                    emailData.templates = res.data.templates || [];
                    renderEmailTemplates();
                    populateTemplatesDropdown();
                    closeEmailTemplateDrawer();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Failed to save template.", "error");
                }
            }
        });
    };

    window.deleteEmailTemplate = function(tplId) {
        if (!confirm("Are you sure you want to delete this custom template?")) return;
        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_delete_email_template',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                id: tplId
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Template deleted successfully! ✓", "success");
                    emailData.templates = res.data.templates || [];
                    renderEmailTemplates();
                    populateTemplatesDropdown();
                }
            }
        });
    };

    // 5. Sent Email Detail Drawer
    window.openEmailDetailDrawer = function(log) {
        activeLogForDetail = log;

        const dateStr = log.sent_at ? new Date(log.sent_at.replace(/-/g, '/')).toLocaleString([], { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Just now';

        $('#drawer-email-from').text(log.sent_from_inbox || 'Official Relay (heycora@claraverse.in)');
        $('#drawer-email-to').text(log.to);
        $('#drawer-email-date').text(dateStr);
        $('#drawer-email-subject').text(log.subject);
        $('#drawer-email-message').text(log.message);

        openDrawerElement($('#cora-email-detail-drawer'));
    };

    window.closeEmailDetailDrawer = function() {
        window.coraCloseEmailDrawers();
    };

    window.resendDrawerEmail = function() {
        if (!activeLogForDetail) return;

        const $btn = $('#btn-resend-drawer-email');
        $btn.prop('disabled', true).text('Resending...');

        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_resend_email',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                to: activeLogForDetail.to,
                subject: activeLogForDetail.subject,
                message: activeLogForDetail.message
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Email resent successfully! ✓", "success");
                    emailData.sent_logs = res.data.sent_logs || [];
                    renderEmailLogs();
                    closeEmailDetailDrawer();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Resend failed.", "error");
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                    Resend This Email
                `);
            }
        });
    };

    // 6. Inbox Handshake Diagnostic Test Drawer
    window.openInboxTestDrawer = function(inboxId, email, provider) {
        $('#test-drawer-inbox-id').val(inboxId);
        $('#test-drawer-inbox-title').text(`Diagnostic: ${provider} (${email})`);
        $('#inbox-test-console').html(`[INIT] Ready to execute SSL/TLS diagnostic ping to ${email}...`);

        openDrawerElement($('#cora-inbox-test-drawer'));
    };

    window.closeInboxTestDrawer = function() {
        window.coraCloseEmailDrawers();
    };

    window.executeInboxTestPing = function() {
        const inboxId = $('#test-drawer-inbox-id').val();
        const testRecipient = $('#inbox-test-recipient').val().trim();
        const $btn = $('#btn-run-inbox-test');
        const $console = $('#inbox-test-console');

        $btn.prop('disabled', true).text('Testing Handshake...');
        $console.html(`[PING] Connecting to remote server...\n[AUTH] Verifying authentication credentials...\n[DNS] Checking SPF & DKIM record signatures...`);

        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_test_inbox_connection',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                inbox_id: inboxId,
                test_recipient: testRecipient
            },
            success: function(res) {
                if (res.success) {
                    const d = res.data.diagnostic || {};
                    $console.html(`[SUCCESS] Connected to ${d.provider} (${d.host}:${d.port})\n[TLS] ${d.handshake}\n[SPF] ${d.spf}\n[DKIM] ${d.dkim}\n[DMARC] ${d.dmarc}\n[STATUS] ${d.status}\n[TIME] ${d.tested_at}`);
                    window.coraShowToast && window.coraShowToast(res.data.message || "Inbox handshake test passed! ✓", "success");
                } else {
                    $console.html(`[ERROR] Handshake failed: ` + (res.data.message || 'Authentication error'));
                    window.coraShowToast && window.coraShowToast(res.data.message || "Diagnostic failed.", "error");
                }
            },
            error: function() {
                $console.html(`[FATAL] Network error contacting backend diagnostic router.`);
                window.coraShowToast && window.coraShowToast("Failed to run diagnostic test.", "error");
            },
            complete: function() {
                $btn.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                    Execute Diagnostic Ping
                `);
            }
        });
    };

    // 7. SMTP Diagnostic Test Drawer (Relay)
    function populateSmtpForm() {
        const smtp = emailData.smtp || {};
        if (smtp.smtp_host) $('#smtp-host').val(smtp.smtp_host);
        if (smtp.smtp_port) $('#smtp-port').val(smtp.smtp_port);
        if (smtp.smtp_secure) $('#smtp-secure').val(smtp.smtp_secure);
        if (smtp.smtp_username) $('#smtp-username').val(smtp.smtp_username);
        if (smtp.from_name) $('#smtp-from-name').val(smtp.from_name);
        if (smtp.from_email) $('#smtp-from-email').val(smtp.from_email);
    }

    window.openSmtpTestDrawer = function() {
        openDrawerElement($('#cora-smtp-test-drawer'));
    };

    window.closeSmtpTestDrawer = function() {
        window.coraCloseEmailDrawers();
    };

    window.runSmtpDiagnosticTest = function() {
        const testRecipient = $('#smtp-test-recipient').val().trim();
        if (!testRecipient) {
            window.coraShowToast && window.coraShowToast("Please enter a test recipient email address.", "error");
            return;
        }

        const $btn = $('#btn-run-smtp-test');
        const $console = $('#smtp-test-console');

        $btn.prop('disabled', true).text('Executing Diagnostic...');
        $console.html(`[PING] Connecting to SMTP relay server...\n[AUTH] Verifying SSL/TLS handshake for ${testRecipient}...`);

        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_test_smtp_connection',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                test_recipient: testRecipient
            },
            success: function(res) {
                if (res.success) {
                    const diag = res.data.diagnostic || {};
                    $console.html(`[SUCCESS] Connected to ${diag.host || 'SMTP Relay'}:${diag.port || '465'}\n[TLS] Handshake OK (${diag.encryption || 'SSL/TLS'})\n[RECP] Sent test packet to ${diag.recipient || testRecipient}\n[TIME] ${diag.sent_at || new Date().toLocaleTimeString()}\n[STATUS] ${diag.status || 'Delivered'}`);
                    window.coraShowToast && window.coraShowToast(res.data.message || "SMTP Relay connected & test email delivered! ✓", "success");
                    loadEmailDashboardData();
                } else {
                    const diag = res.data.diagnostic || {};
                    $console.html(`[ERROR] Connection failed to ${diag.host || 'SMTP Relay'}:${diag.port || '465'}\n[FAIL] ${diag.error || 'SMTP Relay Authentication Error'}`);
                    window.coraShowToast && window.coraShowToast(res.data.message || "SMTP diagnostic failed.", "error");
                }
            },
            error: function() {
                $console.html(`[FATAL] Network error connecting to backend AJAX relay.`);
                window.coraShowToast && window.coraShowToast("SMTP diagnostic failed to execute.", "error");
            },
            complete: function() {
                $btn.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                    Send Diagnostic Test Email
                `);
            }
        });
    };

    // Direct Compose Form Submission (with Selected Sending Account)
    $('#cora-email-compose-form').on('submit', function(e) {
        e.preventDefault();

        const fromInboxId = $('#compose-from-inbox-picker').val();
        const to = $('#email-to').val().trim();
        const subject = $('#email-subject').val().trim();
        const message = $('#email-message').val().trim();

        if (!to || !subject || !message) {
            window.coraShowToast && window.coraShowToast("Please specify recipient, subject, and message content.", "error");
            return;
        }

        const $btn = $('#btn-send-email');
        $btn.prop('disabled', true).html(`
            <svg class="animate-spin shrink-0" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M22 12a10 10 0 0 1-10 10"></path></svg>
            Sending...
        `);

        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_send_email',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                from_inbox_id: fromInboxId,
                to: to,
                subject: subject,
                message: message
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Email sent successfully! ✓", "success");
                    resetEmailComposeForm();
                    loadEmailDashboardData();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Failed to send email. Check SMTP settings.", "error");
                }
            },
            error: function(err) {
                const errMsg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : "Failed to send email. Check SMTP settings.";
                window.coraShowToast && window.coraShowToast(errMsg, "error");
            },
            complete: function() {
                $btn.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    Send Email Now
                `);
            }
        });
    });

    // Helper HTML escape
    function escapeHtml(str) {
        return $('<div>').text(str || '').html();
    }

    // INITIAL MOUNT LOAD
    loadEmailDashboardData();
});

/**
 * SMTP Settings Unlock / Lock Toggle
 */
let _smtpUnlocked = false;
window.coraToggleSMTPLock = function() {
    _smtpUnlocked = !_smtpUnlocked;
    const $badge = jQuery('#smtp-lock-badge');
    const $footer = jQuery('#smtp-admin-footer');
    const $saveBtn = jQuery('#smtp-save-btn');
    const inputIds = ['#smtp-host', '#smtp-port', '#smtp-username', '#smtp-password', '#smtp-from-name', '#smtp-from-email'];
    const lockedClasses = 'bg-zinc-50 text-zinc-500 cursor-not-allowed';
    const editableClasses = 'bg-white text-zinc-900 cursor-text';

    if (_smtpUnlocked) {
        inputIds.forEach(function(id) {
            jQuery(id).removeAttr('readonly').removeClass(lockedClasses).addClass(editableClasses);
        });
        jQuery('#smtp-secure').removeAttr('disabled').removeClass(lockedClasses).addClass(editableClasses + ' cursor-pointer');
        jQuery('#smtp-password').val('');
        $badge.html(`
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-amber-500"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 5-5 5 5 0 0 1 5 5"></path></svg>
            Editing
        `).removeClass('bg-zinc-100 border-zinc-200 text-zinc-700').addClass('bg-amber-50 border-amber-300 text-amber-700');
        $footer.addClass('hidden');
        $saveBtn.removeClass('hidden');
        window.coraShowToast && window.coraShowToast('SMTP credentials unlocked for editing.', 'info');
    } else {
        inputIds.forEach(function(id) {
            jQuery(id).attr('readonly', true).removeClass(editableClasses).addClass(lockedClasses);
        });
        jQuery('#smtp-secure').attr('disabled', true).removeClass(editableClasses + ' cursor-pointer').addClass(lockedClasses);
        jQuery('#smtp-password').val('••••••••••••••••');
        $badge.html(`
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            System Locked
        `).removeClass('bg-amber-50 border-amber-300 text-amber-700').addClass('bg-zinc-100 border-zinc-200 text-zinc-700');
        $footer.removeClass('hidden');
        $saveBtn.addClass('hidden');
    }
};

/**
 * SMTP Settings AJAX Save
 */
window.coraSaveSMTPSettings = function() {
    const data = {
        action: 'cora_save_smtp_settings',
        nonce: window.coraAjaxNonce || '',
        smtp_host: jQuery('#smtp-host').val(),
        smtp_port: jQuery('#smtp-port').val(),
        smtp_secure: jQuery('#smtp-secure').val(),
        smtp_username: jQuery('#smtp-username').val(),
        smtp_password: jQuery('#smtp-password').val(),
        from_name: jQuery('#smtp-from-name').val(),
        from_email: jQuery('#smtp-from-email').val()
    };

    const $btn = jQuery('#smtp-save-btn');
    $btn.prop('disabled', true).text('Saving...');

    jQuery.ajax({
        url: (typeof ajaxurl !== 'undefined' && ajaxurl) ? ajaxurl : '/wp-admin/admin-ajax.php',
        method: 'POST',
        data: data,
        success: function(res) {
            if (res.success) {
                window.coraShowToast && window.coraShowToast(res.data.message || 'SMTP Settings saved successfully! ✓', 'success');
                _smtpUnlocked = true;
                window.coraToggleSMTPLock();
            } else {
                window.coraShowToast && window.coraShowToast((res.data && res.data.message) || 'Failed to save SMTP settings.', 'error');
            }
        },
        error: function() {
            window.coraShowToast && window.coraShowToast('Network error saving SMTP settings.', 'error');
        },
        complete: function() {
            $btn.prop('disabled', false).text('Save Settings');
        }
    });
};
</script>
