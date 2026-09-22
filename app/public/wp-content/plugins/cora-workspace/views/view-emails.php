<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="cora-page-emails" class="flex-1 flex flex-col min-h-0 bg-zinc-50 p-4 sm:p-6 md:p-8" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
<?php
$emails_header_args = array(
    'title'            => 'Email Module',
    'description'      => 'Manage official communications, reusable email templates, automated sequences, outbox logs, and SMTP server settings.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'New Email',
        'mobile_text' => 'New Email',
        'onclick'     => "coraSwitchEmailSubTab('email-tab-compose')",
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 5v14M5 12h14"/></svg>',
        'visible'     => true,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $emails_header_args );
}
?>

    <!-- Top KPI Summary Cards Row (2x2 Mobile / 1x4 Desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 mt-4 sm:mt-5 shrink-0">
        <!-- Stat Card 1: Total Emails Sent -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-3 shadow-3xs flex items-center justify-between min-w-0">
            <div class="min-w-0 pr-2">
                <span class="text-[10px] font-bold tracking-wider text-zinc-400 uppercase block truncate">Total Emails Sent</span>
                <div class="text-base sm:text-lg font-bold text-zinc-900 mt-0.5 truncate" id="stat-total-sent">--</div>
                <span class="text-[10px] text-zinc-500 font-medium block truncate" id="stat-month-sent">-- sent this month</span>
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
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] text-emerald-600 font-semibold truncate">SMTP Relay Active</span>
                </div>
            </div>
            <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200/50 flex items-center justify-center text-emerald-600 shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>
            </div>
        </div>

        <!-- Stat Card 3: Email Templates -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-3 shadow-3xs flex items-center justify-between cursor-pointer hover:bg-zinc-50/80 transition-all min-w-0" onclick="coraSwitchEmailSubTab('email-tab-templates')">
            <div class="min-w-0 pr-2">
                <span class="text-[10px] font-bold tracking-wider text-zinc-400 uppercase block truncate">Active Templates</span>
                <div class="text-base sm:text-lg font-bold text-zinc-900 mt-0.5 truncate" id="stat-active-templates">6 Active</div>
                <span class="text-[10px] text-zinc-500 font-medium block truncate">System & Presets</span>
            </div>
            <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-700 shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
        </div>

        <!-- Stat Card 4: SMTP Health -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-3 shadow-3xs flex items-center justify-between cursor-pointer hover:bg-zinc-50/80 transition-all min-w-0" onclick="coraSwitchEmailSubTab('email-tab-settings')">
            <div class="min-w-0 pr-2">
                <span class="text-[10px] font-bold tracking-wider text-zinc-400 uppercase block truncate">SMTP Provider</span>
                <div class="text-xs font-bold text-zinc-900 mt-0.5 truncate" id="stat-smtp-provider">SMTP Relay Active</div>
                <span class="text-[10px] text-zinc-500 font-mono block truncate">Port 587 / TLS SSL</span>
            </div>
            <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-700 shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
            </div>
        </div>
    </div>

    <!-- Sticky Sub-Navigation Tabs SOP (~36px Sleek Height - Edge-to-Edge) -->
    <div id="cora-emails-subtabs" class="cora-sub-tabs-container cora-sticky-sub-tabs sticky z-30 flex items-center gap-1 border-b border-zinc-200/80 bg-white/95 backdrop-blur-md -mx-3 sm:-mx-4 md:-mx-5 px-3 sm:px-4 md:px-5 mt-4 sm:mt-5 shrink-0 overflow-x-auto no-scrollbar" style="touch-action: pan-x; -webkit-overflow-scrolling: touch; scrollbar-width: none;">
        <button id="tab-btn-email-tab-compose" onclick="coraSwitchEmailSubTab('email-tab-compose')"
                class="cora-email-sub-tab px-3.5 py-2 text-xs font-bold text-zinc-900 border-b-2 border-zinc-950 transition-all whitespace-nowrap cursor-pointer shrink-0">
            Compose & Send
        </button>
        <button id="tab-btn-email-tab-templates" onclick="coraSwitchEmailSubTab('email-tab-templates')"
                class="cora-email-sub-tab px-3.5 py-2 text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0">
            Email Templates
        </button>
        <button id="tab-btn-email-tab-sequences" onclick="coraSwitchEmailSubTab('email-tab-sequences')"
                class="cora-email-sub-tab px-3.5 py-2 text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0">
            Automated Drip Sequences
        </button>
        <button id="tab-btn-email-tab-outbox" onclick="coraSwitchEmailSubTab('email-tab-outbox')"
                class="cora-email-sub-tab px-3.5 py-2 text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0">
            Outbox & Sent Logs
        </button>
        <button id="tab-btn-email-tab-settings" onclick="coraSwitchEmailSubTab('email-tab-settings')"
                class="cora-email-sub-tab px-3.5 py-2 text-xs font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer shrink-0">
            SMTP & Provider Settings
        </button>
    </div>

    <!-- MAIN SUB-TAB CONTENT PANELS CONTAINER -->
    <div class="flex-1 mt-4 min-h-0 relative">
        
        <!-- SUB-TAB 1: COMPOSE & SEND -->
        <div id="email-tab-compose" class="cora-email-tab-content flex-1 flex flex-col h-full min-h-0">
            
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
                                <p class="text-[10px] text-zinc-500">Craft personalized official messages with real-time variable injection.</p>
                            </div>
                        </div>
                        <button type="button" onclick="resetEmailComposeForm()" class="text-[11px] font-semibold text-zinc-400 hover:text-zinc-700 bg-transparent border-0 cursor-pointer transition-colors">
                            Clear
                        </button>
                    </div>
                    
                    <form id="cora-email-compose-form" class="space-y-3.5 mt-3.5 flex-1 flex flex-col">
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
                            <input type="text" id="email-subject" placeholder="Your appointment/service confirmation" required oninput="updateLivePreview()"
                                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-medium text-zinc-900 outline-hidden focus:border-zinc-400 transition-all shadow-3xs" />
                        </div>

                        <!-- Variable Tags Pill Bar -->
                        <div class="space-y-1 shrink-0">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Insert Dynamic Variable Tags</label>
                                <span class="text-[9px] text-zinc-400">Click tag to insert</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                                <button type="button" onclick="insertVariableTag('{client_name}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{client_name}</button>
                                <button type="button" onclick="insertVariableTag('{event_name}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{event_name}</button>
                                <button type="button" onclick="insertVariableTag('{event_date}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{event_date}</button>
                                <button type="button" onclick="insertVariableTag('{portal_url}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{portal_url}</button>
                                <button type="button" onclick="insertVariableTag('{invoice_amount}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{invoice_amount}</button>
                                <button type="button" onclick="insertVariableTag('{due_amount}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{due_amount}</button>
                                <button type="button" onclick="insertVariableTag('{studio_name}')" class="px-2 py-0.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[10px] font-mono font-medium text-zinc-700 border-0 cursor-pointer transition-colors">{studio_name}</button>
                            </div>
                        </div>

                        <!-- Message Body -->
                        <div class="space-y-1 flex-1 flex flex-col min-h-44">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Message Content *</label>
                            <textarea id="email-message" placeholder="Write your professional email message here..." required oninput="updateLivePreview()"
                                      class="w-full flex-1 min-h-44 p-3 rounded-lg border border-zinc-200 bg-white text-xs font-normal text-zinc-900 outline-hidden focus:border-zinc-400 transition-all resize-none shadow-3xs leading-relaxed"></textarea>
                        </div>

                        <!-- Attachment Link Simulator -->
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-zinc-50 border border-zinc-200/80 shrink-0">
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
                                <span id="compose-sender-info">Official Relay: SMTP Connected</span>
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
                            <h3 class="text-xs font-bold text-zinc-900">Live Render Preview</h3>
                        </div>
                        <span class="text-[10px] font-mono text-zinc-400">Notion / Minimal Theme</span>
                    </div>

                    <div class="mt-3.5 p-4 rounded-xl bg-zinc-50 border border-zinc-200/80 flex-1 flex flex-col font-sans">
                        <!-- Simulated Email Envelope Header -->
                        <div class="border-b border-zinc-200/80 pb-2.5 mb-3.5 space-y-1 text-[11px]">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-400 font-bold uppercase text-[9px]">To:</span>
                                <span class="font-mono text-zinc-800 font-semibold text-[11px] truncate max-w-[200px]" id="preview-to">client@example.com</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-400 font-bold uppercase text-[9px]">From:</span>
                                <span class="font-mono text-zinc-500 text-[10px] truncate max-w-[200px]" id="preview-from"><?php echo esc_html(get_option('admin_email')); ?></span>
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

                            <div class="mt-5 pt-3 border-t border-zinc-100 text-center text-[10px] text-zinc-400 font-sans">
                                Official Communication via <?php echo esc_html(get_bloginfo('name')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUB-TAB 2: EMAIL TEMPLATES -->
        <div id="email-tab-templates" class="cora-email-tab-content hidden flex-1 flex-col space-y-4">
            <!-- Templates Toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white border border-zinc-200 rounded-xl p-3.5 shadow-3xs">
                <div class="flex items-center gap-2.5 flex-1">
                    <input type="text" id="template-search-input" placeholder="Search templates..." oninput="renderEmailTemplates()"
                           class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-hidden w-full sm:w-64 focus:border-zinc-400 transition-colors" />
                    <select id="template-category-filter" onchange="renderEmailTemplates()"
                            class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-800 outline-hidden cursor-pointer">
                        <option value="">All Categories</option>
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
                    + Create Template
                </button>
            </div>

            <!-- Templates Card Grid Container (Clean 3-Column Grid) -->
            <div id="templates-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                <!-- Dynamically populated via renderEmailTemplates() -->
            </div>
        </div>

        <!-- SUB-TAB 3: AUTOMATED DRIP SEQUENCES -->
        <div id="email-tab-sequences" class="cora-email-tab-content hidden flex-1 flex-col space-y-4">
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

        <!-- SUB-TAB 4: SENT LOGS & OUTBOX -->
        <div id="email-tab-outbox" class="cora-email-tab-content hidden flex-1 flex-col space-y-3.5">
            <!-- Filter Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white border border-zinc-200 rounded-xl p-3.5 shadow-3xs">
                <div class="flex items-center gap-2.5 flex-1">
                    <input type="text" id="outbox-search-input" placeholder="Search recipient or subject..." oninput="filterOutboxLogs()"
                           class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs text-zinc-900 outline-hidden w-full sm:w-72 focus:border-zinc-400 transition-colors" />
                    <select id="outbox-status-filter" onchange="filterOutboxLogs()"
                            class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-800 outline-hidden cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="delivered">Delivered</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <button onclick="exportOutboxLogsCSV()" class="h-9 px-3 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold flex items-center gap-1.5 cursor-pointer transition-colors shadow-3xs">
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
                                <th class="py-2.5 px-3.5">Recipient</th>
                                <th class="py-2.5 px-3.5">Subject</th>
                                <th class="py-2.5 px-3.5">Sent Timestamp</th>
                                <th class="py-2.5 px-3.5">Delivery Status</th>
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

        <!-- SUB-TAB 5: SMTP & PROVIDER SETTINGS -->
        <div id="email-tab-settings" class="cora-email-tab-content hidden flex-1 flex-col space-y-4">
            <div class="max-w-2xl bg-white border border-zinc-200 rounded-xl p-5 shadow-3xs">
                <div class="border-b border-zinc-100 pb-3.5 mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                            SMTP Server Relay Configuration
                        </h3>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Configure official SMTP mail relay credentials for guaranteed high deliverability.</p>
                    </div>
                    <button type="button" id="smtp-lock-badge" onclick="coraToggleSMTPLock()" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-700 text-[11px] font-semibold shrink-0 cursor-pointer hover:bg-zinc-200 transition-colors">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        System Locked
                    </button>
                </div>

                <form id="smtp-settings-form" class="space-y-3.5" onsubmit="event.preventDefault();">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">SMTP Server Host *</label>
                            <input type="text" id="smtp-host" value="<?php echo esc_attr(get_option('cora_smtp_host', 'smtp.hostinger.com')); ?>" readonly
                                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">SMTP Port *</label>
                            <input type="text" id="smtp-port" value="<?php echo esc_attr(get_option('cora_smtp_port', '465')); ?>" readonly
                                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Encryption Method</label>
                            <?php $sec = get_option('cora_smtp_secure', 'ssl'); ?>
                            <select id="smtp-secure" disabled
                                    class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden">
                                <option value="ssl" <?php selected($sec, 'ssl'); ?>>SSL (Port 465 Recommended)</option>
                                <option value="tls" <?php selected($sec, 'tls'); ?>>TLS / STARTTLS (Port 587)</option>
                                <option value="none" <?php selected($sec, 'none'); ?>>None (Plaintext)</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">SMTP Username *</label>
                            <input type="email" id="smtp-username" value="<?php echo esc_attr(get_option('cora_smtp_username', 'heycora@claraverse.in')); ?>" readonly
                                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">SMTP Password</label>
                        <input type="password" id="smtp-password" value="••••••••••••••••" readonly
                               class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Default From Name</label>
                            <input type="text" id="smtp-from-name" value="<?php echo esc_attr(get_option('cora_from_name', 'Cora Studio')); ?>" readonly
                                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Default From Email</label>
                            <input type="email" id="smtp-from-email" value="<?php echo esc_attr(get_option('cora_from_email', 'heycora@claraverse.in')); ?>" readonly
                                   class="w-full h-9 px-3 rounded-lg border border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 cursor-not-allowed outline-hidden" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3.5 border-t border-zinc-100">
                        <button type="button" onclick="openSmtpTestDrawer()" class="h-9 px-3.5 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 cursor-pointer flex items-center gap-1.5 transition-colors shadow-3xs">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            Run Connection Test
                        </button>
                        <div id="smtp-admin-footer" class="flex items-center gap-1.5 text-xs font-semibold text-zinc-500">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            Managed by Studio Admin
                        </div>
                        <button type="button" id="smtp-save-btn" onclick="coraSaveSMTPSettings()" class="hidden h-9 px-4 rounded-lg bg-zinc-900 text-white text-xs font-semibold hover:bg-zinc-800 cursor-pointer flex items-center gap-2 transition-colors shadow-3xs">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- SLIDING DRAWERS / MOBILE BOTTOM SHEETS (SOP Rule 1 & 12 Compliance)       -->
<!-- ========================================================================= -->

<!-- 1. TEMPLATE CREATOR / EDITOR DRAWER -->
<div id="cora-email-template-drawer" class="fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-lg h-[88vh] sm:h-full max-h-[88vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none shadow-2xl z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans">
    <!-- Mobile Drag Handle -->
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

<!-- 2. SENT EMAIL DETAIL DRAWER -->
<div id="cora-email-detail-drawer" class="fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-md h-[88vh] sm:h-full max-h-[88vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none shadow-2xl z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans">
    <!-- Mobile Drag Handle -->
    <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto my-2.5 sm:hidden shrink-0"></div>

    <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-200 shrink-0">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Sent Communication Details</h3>
        <button onclick="closeEmailDetailDrawer()" class="text-zinc-400 hover:text-zinc-700 border-0 bg-transparent cursor-pointer text-xs font-bold transition-colors">✕ Close</button>
    </div>
    <div class="flex-1 overflow-y-auto p-5 space-y-3.5">
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

<!-- 3. SMTP DIAGNOSTIC TEST DRAWER -->
<div id="cora-smtp-test-drawer" class="fixed inset-x-0 bottom-0 sm:inset-y-0 sm:right-0 sm:left-auto w-full sm:max-w-md h-[88vh] sm:h-full max-h-[88vh] sm:max-h-none bg-white border-t sm:border-t-0 sm:border-l border-zinc-200 rounded-t-3xl sm:rounded-none shadow-2xl z-[9995] flex flex-col pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] translate-y-full sm:translate-y-0 sm:translate-x-full overflow-hidden font-sans">
    <!-- Mobile Drag Handle -->
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
        recipients: []
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
                    populateRecipientsDropdown();
                    populateTemplatesDropdown();
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
        $('#stat-total-sent').text(stats.total_sent || 0);
        $('#stat-month-sent').text((stats.month_sent || 0) + ' sent this month');
        $('#stat-success-rate').text((stats.success_rate || 99.4) + '%');
        $('#stat-active-templates').text((stats.active_templates || 6) + ' Active');
        $('#stat-smtp-provider').text(stats.from_email || 'SMTP Relay Active');
    }

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

    window.updateLivePreview = function() {
        const to = $('#email-to').val().trim() || 'client@example.com';
        const subject = $('#email-subject').val().trim() || '(No Subject Specified)';
        const rawBody = $('#email-message').val().trim() || 'Message body preview will render here in real-time as you type...';

        // Dynamic Variable Replacements from selected CRM contact or workspace context
        const clientName = (selectedRecipientObj && selectedRecipientObj.name) ? selectedRecipientObj.name : 'Aarav Sharma';
        const eventName  = (selectedRecipientObj && selectedRecipientObj.event) ? selectedRecipientObj.event : 'Pre-Wedding Documentary';
        const portalUrl  = (selectedRecipientObj && selectedRecipientObj.portal_url) ? selectedRecipientObj.portal_url : (window.location.origin + '/portal/view');
        const studioName = (emailData.stats && emailData.stats.from_name) ? emailData.stats.from_name : 'Cora Workspace';
        const todayStr   = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

        let processedBody = rawBody
            .replace(/\{client_name\}/g, clientName)
            .replace(/\{event_name\}/g, eventName)
            .replace(/\{event_date\}/g, todayStr)
            .replace(/\{event_location\}/g, 'Taj West End, Bengaluru')
            .replace(/\{portal_url\}/g, portalUrl)
            .replace(/\{invoice_amount\}/g, '₹45,000')
            .replace(/\{due_amount\}/g, '₹15,000')
            .replace(/\{studio_name\}/g, studioName);

        let processedSubject = subject
            .replace(/\{client_name\}/g, clientName)
            .replace(/\{event_name\}/g, eventName)
            .replace(/\{studio_name\}/g, studioName);

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

    // RENDER TEMPLATES IN TAB 2
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

    // RENDER OUTBOX LOGS IN TAB 4
    function renderEmailLogs() {
        const $tbody = $('#outbox-table-body');
        const query = ($('#outbox-search-input').val() || '').toLowerCase();
        const statusFilter = ($('#outbox-status-filter').val() || '').toLowerCase();

        const logs = (emailData.sent_logs || []).filter(log => {
            const matchesQuery = (log.to || '').toLowerCase().includes(query) || (log.subject || '').toLowerCase().includes(query);
            const matchesStatus = !statusFilter || (log.status || '').toLowerCase() === statusFilter;
            return matchesQuery && matchesStatus;
        });

        if (logs.length === 0) {
            $tbody.html('<tr><td colspan="5" class="py-8 text-center text-zinc-400 italic text-xs">No email outbox records found.</td></tr>');
            return;
        }

        $tbody.html('');
        logs.forEach((log) => {
            const dateStr = log.sent_at ? new Date(log.sent_at.replace(/-/g, '/')).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Just now';
            const row = $(`
                <tr class="hover:bg-zinc-50/70 transition-all cursor-pointer">
                    <td class="py-2.5 px-3.5 font-bold text-zinc-900">${escapeHtml(log.to)}</td>
                    <td class="py-2.5 px-3.5 font-medium text-zinc-700 max-w-xs truncate">${escapeHtml(log.subject)}</td>
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
        let csv = "To,Subject,Sent At,Status\n";
        logs.forEach(l => {
            csv += `"${(l.to || '').replace(/"/g, '""')}","${(l.subject || '').replace(/"/g, '""')}","${l.sent_at || ''}","${l.status || 'Delivered'}"\n`;
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

    // DRAWER CONTROLLER FUNCTIONS (SOP Rule 1 & 12 Compliance)
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

    window.coraCloseAllDrawers = function() {
        $('#cora-email-template-drawer, #cora-email-detail-drawer, #cora-smtp-test-drawer')
            .addClass('translate-y-full sm:translate-x-full pointer-events-none')
            .removeClass('translate-y-0 sm:translate-x-0 pointer-events-auto');
        hideBackdrop();
    };

    // 1. Template Creator / Editor Drawer
    window.openEmailTemplateDrawer = function(tplId = null) {
        window.coraCloseAllDrawers();
        $('#template-editor-form')[0].reset();
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

        $('#cora-email-template-drawer')
            .removeClass('translate-y-full sm:translate-x-full pointer-events-none')
            .addClass('translate-y-0 sm:translate-x-0 pointer-events-auto');
        showBackdrop();
    };

    window.closeEmailTemplateDrawer = function() {
        window.coraCloseAllDrawers();
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
        const performDelete = function() {
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
                    } else {
                        window.coraShowToast && window.coraShowToast(res.data.message || "Failed to delete template.", "error");
                    }
                }
            });
        };

        if (window.coraConfirmAction) {
            window.coraConfirmAction(
                'Delete Template',
                'Are you sure you want to delete this custom template?',
                performDelete
            );
        } else {
            performDelete();
        }
    };

    // 2. Sent Email Detail Drawer
    window.openEmailDetailDrawer = function(log) {
        window.coraCloseAllDrawers();
        activeLogForDetail = log;

        const dateStr = log.sent_at ? new Date(log.sent_at.replace(/-/g, '/')).toLocaleString([], { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Just now';

        $('#drawer-email-to').text(log.to);
        $('#drawer-email-date').text(dateStr);
        $('#drawer-email-subject').text(log.subject);
        $('#drawer-email-message').text(log.message);

        $('#cora-email-detail-drawer')
            .removeClass('translate-y-full sm:translate-x-full pointer-events-none')
            .addClass('translate-y-0 sm:translate-x-0 pointer-events-auto');
        showBackdrop();
    };

    window.closeEmailDetailDrawer = function() {
        window.coraCloseAllDrawers();
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

    // 3. SMTP Diagnostic Test Drawer
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
        window.coraCloseAllDrawers();
        $('#cora-smtp-test-drawer')
            .removeClass('translate-y-full sm:translate-x-full pointer-events-none')
            .addClass('translate-y-0 sm:translate-x-0 pointer-events-auto');
        showBackdrop();
    };

    window.closeSmtpTestDrawer = function() {
        window.coraCloseAllDrawers();
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
                $console.html(`[FATAL] Network error connecting to WordPress AJAX relay.`);
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

    // Direct Compose Form Submission
    $('#cora-email-compose-form').on('submit', function(e) {
        e.preventDefault();

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
                to: to,
                subject: subject,
                message: message
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Email sent officially via SMTP Relay! ✓", "success");
                    resetEmailComposeForm();
                    if (res.data.sent_logs) {
                        emailData.sent_logs = res.data.sent_logs;
                        renderEmailLogs();
                        updateDashboardKPIs();
                    } else {
                        loadEmailDashboardData();
                    }
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
 * Toggles all SMTP form inputs between readonly/disabled locked state and editable state.
 */
let _smtpUnlocked = false;
window.coraToggleSMTPLock = function() {
    _smtpUnlocked = !_smtpUnlocked;
    const $badge = $('#smtp-lock-badge');
    const $footer = $('#smtp-admin-footer');
    const $saveBtn = $('#smtp-save-btn');
    const inputIds = ['#smtp-host', '#smtp-port', '#smtp-username', '#smtp-password', '#smtp-from-name', '#smtp-from-email'];
    const lockedClasses = 'bg-zinc-50 text-zinc-500 cursor-not-allowed';
    const editableClasses = 'bg-white text-zinc-900 cursor-text';

    if (_smtpUnlocked) {
        // Unlock all inputs
        inputIds.forEach(function(id) {
            jQuery(id).removeAttr('readonly')
                 .removeClass(lockedClasses)
                 .addClass(editableClasses);
        });
        // Unlock select
        jQuery('#smtp-secure').removeAttr('disabled')
             .removeClass(lockedClasses)
             .addClass(editableClasses + ' cursor-pointer');
        // Clear password placeholder so user can type fresh
        jQuery('#smtp-password').val('');
        // Update badge to "Editing"
        $badge.html(`
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-amber-500"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 5-5 5 5 0 0 1 5 5"></path></svg>
            Editing
        `).removeClass('bg-zinc-100 border-zinc-200 text-zinc-700')
          .addClass('bg-amber-50 border-amber-300 text-amber-700');
        // Hide admin footer, show save button
        $footer.addClass('hidden');
        $saveBtn.removeClass('hidden');
        window.coraShowToast && window.coraShowToast('SMTP credentials unlocked for editing. Enter your details and click Save.', 'info');
    } else {
        // Re-lock all inputs
        inputIds.forEach(function(id) {
            jQuery(id).attr('readonly', true)
                 .removeClass(editableClasses)
                 .addClass(lockedClasses);
        });
        jQuery('#smtp-secure').attr('disabled', true)
             .removeClass(editableClasses + ' cursor-pointer')
             .addClass(lockedClasses);
        // Reset password to masked
        jQuery('#smtp-password').val('••••••••••••••••');
        // Restore badge
        $badge.html(`
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            System Locked
        `).removeClass('bg-amber-50 border-amber-300 text-amber-700')
          .addClass('bg-zinc-100 border-zinc-200 text-zinc-700');
        // Show admin footer, hide save button
        $footer.removeClass('hidden');
        $saveBtn.addClass('hidden');
    }
};

/**
 * SMTP Settings AJAX Save
 * Posts updated SMTP credentials to the backend and re-locks the form on success.
 */
window.coraSaveSMTPSettings = function() {
    const data = {
        action: 'cora_save_smtp_settings',
        nonce: (typeof getAjaxNonce === 'function') ? getAjaxNonce() : (window.coraAjax && window.coraAjax.nonce ? window.coraAjax.nonce : ''),
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
        url: (typeof getAjaxEndpoint === 'function') ? getAjaxEndpoint() : (window.coraAjax && window.coraAjax.url ? window.coraAjax.url : '/wp-admin/admin-ajax.php'),
        method: 'POST',
        data: data,
        success: function(res) {
            if (res.success) {
                window.coraShowToast && window.coraShowToast(res.data.message || 'SMTP Settings saved successfully! ✓', 'success');
                // Re-lock the form
                _smtpUnlocked = true; // set true so toggle flips to locked
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
