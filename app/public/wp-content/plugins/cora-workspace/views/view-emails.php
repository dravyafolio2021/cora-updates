<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="flex-1 flex flex-col min-h-0 bg-zinc-50 dark:bg-zinc-950 p-6 md:p-8" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-2.5">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-800 dark:text-zinc-200"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                Email Module
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Manage official communications, reusable email templates, automated sequences, outbox logs, and Hostinger SMTP settings.</p>
        </div>
        
        <!-- Connection Badge & Quick Actions -->
        <div class="flex items-center gap-3 shrink-0">
            <div class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 px-3.5 py-2 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400" id="smtp-status-badge">Hostinger Business SMTP Connected</span>
            </div>
            
            <button onclick="coraSwitchEmailSubTab('email-tab-compose')" 
                    class="h-9 px-4 rounded-xl bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-white text-white dark:text-zinc-950 text-xs font-bold flex items-center gap-2 transition-all shadow-xs border-0 cursor-pointer">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 5v14M5 12h14"/></svg>
                New Email
            </button>

            <button onclick="openSmtpTestDrawer()" 
                    class="h-9 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold flex items-center gap-1.5 transition-all cursor-pointer">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                SMTP Test
            </button>
        </div>
    </div>

    <!-- Top KPI Summary Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 shrink-0">
        <!-- Stat Card 1: Total Emails Sent -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">Total Emails Sent</span>
                <div class="text-xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1" id="stat-total-sent">--</div>
                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 font-medium" id="stat-month-sent">-- sent this month</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300 shrink-0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
            </div>
        </div>

        <!-- Stat Card 2: Delivery Success Rate -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">Delivery Success</span>
                <div class="text-xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1" id="stat-success-rate">99.4%</div>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">Hostinger Relay Active</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/50 dark:border-emerald-800/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>
            </div>
        </div>

        <!-- Stat Card 3: Email Templates -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex items-center justify-between cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-700 transition-all" onclick="coraSwitchEmailSubTab('email-tab-templates')">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">Active Templates</span>
                <div class="text-xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-1" id="stat-active-templates">6 Active</div>
                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 font-medium">System & Custom Presets</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300 shrink-0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
        </div>

        <!-- Stat Card 4: SMTP Health -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex items-center justify-between cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-700 transition-all" onclick="coraSwitchEmailSubTab('email-tab-settings')">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">SMTP Provider</span>
                <div class="text-sm font-bold text-zinc-900 dark:text-zinc-50 mt-1 truncate" id="stat-smtp-provider">Hostinger Business</div>
                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono">Port 587 / TLS SSL</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300 shrink-0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
            </div>
        </div>
    </div>

    <!-- Sub-Tab Navigation Bar -->
    <div class="flex items-center gap-1 border-b border-zinc-200 dark:border-zinc-800 mt-6 shrink-0 overflow-x-auto">
        <button id="tab-btn-email-tab-compose" onclick="coraSwitchEmailSubTab('email-tab-compose')"
                class="cora-email-sub-tab px-4 py-2.5 text-xs font-bold text-zinc-900 dark:text-white border-b-2 border-zinc-950 dark:border-white transition-all whitespace-nowrap cursor-pointer">
            Compose & Send
        </button>
        <button id="tab-btn-email-tab-templates" onclick="coraSwitchEmailSubTab('email-tab-templates')"
                class="cora-email-sub-tab px-4 py-2.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer">
            Email Templates
        </button>
        <button id="tab-btn-email-tab-sequences" onclick="coraSwitchEmailSubTab('email-tab-sequences')"
                class="cora-email-sub-tab px-4 py-2.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer">
            Automated Drip Sequences
        </button>
        <button id="tab-btn-email-tab-outbox" onclick="coraSwitchEmailSubTab('email-tab-outbox')"
                class="cora-email-sub-tab px-4 py-2.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer">
            Outbox & Sent Logs
        </button>
        <button id="tab-btn-email-tab-settings" onclick="coraSwitchEmailSubTab('email-tab-settings')"
                class="cora-email-sub-tab px-4 py-2.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 border-b-2 border-transparent transition-all whitespace-nowrap cursor-pointer">
            SMTP & Provider Settings
        </button>
    </div>

    <!-- MAIN SUB-TAB CONTENT PANELS CONTAINER -->
    <div class="flex-1 mt-6 min-h-0 relative">
        
        <!-- SUB-TAB 1: COMPOSE & SEND -->
        <div id="email-tab-compose" class="cora-email-tab-content flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 h-full min-h-0">
            <!-- Left Form Panel (7 Cols) -->
            <div class="lg:col-span-7 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 shadow-xs overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        New Email Communication
                    </h3>
                    <button type="button" onclick="resetEmailComposeForm()" class="text-[11px] font-medium text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 bg-transparent border-0 cursor-pointer">Clear Form</button>
                </div>
                
                <form id="cora-email-compose-form" class="space-y-4 mt-4 flex-1 flex flex-col">
                    <!-- Recipient Selection Group -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Select CRM Lead / Client</label>
                            <select id="compose-recipient-picker" onchange="onRecipientPickerChange(this)"
                                    class="w-full h-10 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden focus:border-zinc-400 dark:focus:border-zinc-500 transition-all shadow-3xs cursor-pointer">
                                <option value="">-- Choose from Leads / Clients --</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Or Manual Recipient Email *</label>
                            <input type="email" id="email-to" placeholder="client@example.com" required
                                   class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden focus:border-zinc-400 dark:focus:border-zinc-500 transition-all shadow-3xs" />
                        </div>
                    </div>

                    <!-- Template Preset Selector -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Load Saved Template Preset</label>
                        <select id="compose-template-selector" onchange="loadTemplateIntoComposer(this.value)"
                                class="w-full h-10 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950/60 text-xs font-semibold text-zinc-800 dark:text-zinc-200 outline-hidden focus:border-zinc-400 dark:focus:border-zinc-500 transition-all shadow-3xs cursor-pointer">
                            <option value="">-- Select Template to Pre-fill --</option>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Subject Line *</label>
                        <input type="text" id="email-subject" placeholder="Your appointment/service confirmation" required oninput="updateLivePreview()"
                               class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden focus:border-zinc-400 dark:focus:border-zinc-500 transition-all shadow-3xs" />
                    </div>

                    <!-- Variable Tags Pill Bar -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider flex items-center justify-between">
                            <span>Insert Dynamic Variable Tags</span>
                            <span class="text-[9px] font-normal normal-case text-zinc-400">Click a tag to insert into message</span>
                        </label>
                        <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                            <button type="button" onclick="insertVariableTag('{client_name}')" class="px-2.5 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-750 text-[10px] font-mono font-semibold text-zinc-700 dark:text-zinc-300 border-0 cursor-pointer transition-all">{client_name}</button>
                            <button type="button" onclick="insertVariableTag('{event_name}')" class="px-2.5 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-750 text-[10px] font-mono font-semibold text-zinc-700 dark:text-zinc-300 border-0 cursor-pointer transition-all">{event_name}</button>
                            <button type="button" onclick="insertVariableTag('{event_date}')" class="px-2.5 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-750 text-[10px] font-mono font-semibold text-zinc-700 dark:text-zinc-300 border-0 cursor-pointer transition-all">{event_date}</button>
                            <button type="button" onclick="insertVariableTag('{portal_url}')" class="px-2.5 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-750 text-[10px] font-mono font-semibold text-zinc-700 dark:text-zinc-300 border-0 cursor-pointer transition-all">{portal_url}</button>
                            <button type="button" onclick="insertVariableTag('{invoice_amount}')" class="px-2.5 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-750 text-[10px] font-mono font-semibold text-zinc-700 dark:text-zinc-300 border-0 cursor-pointer transition-all">{invoice_amount}</button>
                            <button type="button" onclick="insertVariableTag('{studio_name}')" class="px-2.5 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-750 text-[10px] font-mono font-semibold text-zinc-700 dark:text-zinc-300 border-0 cursor-pointer transition-all">{studio_name}</button>
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div class="space-y-1 flex-1 flex flex-col min-h-48">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Message Content *</label>
                        <textarea id="email-message" placeholder="Write your professional email message here..." required oninput="updateLivePreview()"
                                  class="w-full flex-1 min-h-52 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden focus:border-zinc-400 dark:focus:border-zinc-500 transition-all resize-none shadow-3xs leading-relaxed"></textarea>
                    </div>

                    <!-- Attachment Link Simulator -->
                    <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 shrink-0">
                        <div class="flex items-center gap-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                            <span>Attach Document Vault / Media Link</span>
                        </div>
                        <button type="button" onclick="attachVaultDocumentSim()" class="text-[11px] font-bold text-zinc-900 dark:text-zinc-100 hover:underline bg-transparent border-0 cursor-pointer">
                            + Select Vault Asset
                        </button>
                    </div>

                    <!-- Action Toolbar -->
                    <div class="flex items-center justify-between pt-2 shrink-0">
                        <span class="text-[10px] text-zinc-400 font-mono" id="compose-sender-info">Sent officially via Hostinger SMTP</span>
                        <button type="submit" id="btn-send-email"
                                class="h-10 px-6 rounded-xl bg-zinc-950 dark:bg-white hover:bg-zinc-900 dark:hover:bg-zinc-50 text-white dark:text-zinc-950 text-xs font-bold flex items-center gap-2 cursor-pointer transition-all border-none shadow-xs">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            Send Email Now
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Live Preview Card (5 Cols) -->
            <div class="lg:col-span-5 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 shadow-xs overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        Live Email Preview
                    </h3>
                    <span class="text-[10px] font-mono text-zinc-400">Notion/Shopify Theme</span>
                </div>

                <div class="mt-4 p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 flex-1 flex flex-col font-sans">
                    <!-- Simulated Email Header -->
                    <div class="border-b border-zinc-200/80 dark:border-zinc-800 pb-3 mb-4 space-y-1.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-zinc-400 font-bold uppercase">To:</span>
                            <span class="font-mono text-zinc-800 dark:text-zinc-200 font-semibold" id="preview-to">client@example.com</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-zinc-400 font-bold uppercase">From:</span>
                            <span class="font-mono text-zinc-500 dark:text-zinc-400" id="preview-from"><?php echo esc_html(get_option('admin_email')); ?></span>
                        </div>
                        <div class="flex items-start justify-between text-[11px] pt-1">
                            <span class="text-zinc-400 font-bold uppercase shrink-0">Subject:</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 text-right truncate pl-2" id="preview-subject">(No Subject Specified)</span>
                        </div>
                    </div>

                    <!-- Email Body Container Card -->
                    <div class="flex-1 bg-white dark:bg-zinc-900 rounded-xl p-5 border border-zinc-200/60 dark:border-zinc-800 shadow-3xs flex flex-col justify-between">
                        <div id="preview-body" class="text-xs text-zinc-800 dark:text-zinc-200 leading-relaxed space-y-3 whitespace-pre-wrap font-medium">
                            <span class="text-zinc-400 italic text-[11px]">Message body preview will render here in real-time as you type...</span>
                        </div>

                        <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-center text-[10px] text-zinc-400 dark:text-zinc-500 font-sans">
                            Official Communication via <?php echo esc_html(get_bloginfo('name')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUB-TAB 2: EMAIL TEMPLATES -->
        <div id="email-tab-templates" class="cora-email-tab-content hidden flex-1 flex-col space-y-5">
            <!-- Templates Toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs">
                <div class="flex items-center gap-3 flex-1">
                    <input type="text" id="template-search-input" placeholder="Search templates..." oninput="renderEmailTemplates()"
                           class="h-9 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs text-zinc-900 dark:text-zinc-100 outline-hidden w-full sm:w-64" />
                    <select id="template-category-filter" onchange="renderEmailTemplates()"
                            class="h-9 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs font-semibold text-zinc-800 dark:text-zinc-200 outline-hidden cursor-pointer">
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
                        class="h-9 px-4 rounded-xl bg-zinc-950 dark:bg-white hover:bg-zinc-900 dark:hover:bg-zinc-50 text-white dark:text-zinc-950 text-xs font-bold flex items-center gap-2 border-0 cursor-pointer shadow-xs shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    + Create Template
                </button>
            </div>

            <!-- Templates Card Grid Container -->
            <div id="templates-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Dynamically populated via renderEmailTemplates() -->
            </div>
        </div>

        <!-- SUB-TAB 3: AUTOMATED DRIP SEQUENCES -->
        <div id="email-tab-sequences" class="cora-email-tab-content hidden flex-1 flex-col space-y-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Automated Client Drip Sequences</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Automate timely follow-ups, proofing notifications, and review acquisition drips.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">3 Workflows Active</span>
                </div>

                <div class="space-y-6 mt-5">
                    <!-- Sequence 1: Lead Inquiry Follow-up -->
                    <div class="p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-zinc-900 text-white flex items-center justify-center text-xs font-bold">01</span>
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">New Inquiry Nurturing Sequence (4 Steps)</h4>
                                    <p class="text-[10px] text-zinc-500">Triggers automatically upon new website inquiry submission.</p>
                                </div>
                            </div>
                            <button class="h-7 px-3 rounded-lg bg-zinc-900 text-white text-[10px] font-bold border-0 cursor-pointer hover:bg-zinc-800">Active</button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 pt-2">
                            <div class="p-2.5 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200/60 dark:border-zinc-800 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 1 • Immediate</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate block mt-0.5">Thank You & Vision Review</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200/60 dark:border-zinc-800 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 2 • Day 1 Delay</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate block mt-0.5">Portfolio & Visual Showcase</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200/60 dark:border-zinc-800 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 3 • Day 3 Delay</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate block mt-0.5">Consultation Booking Call</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200/60 dark:border-zinc-800 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 4 • Day 5 Delay</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate block mt-0.5">Final Follow-up Notice</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sequence 2: Post-Shoot Gallery Delivery -->
                    <div class="p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-zinc-900 text-white flex items-center justify-center text-xs font-bold">02</span>
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Post-Shoot Gallery Delivery Drip (3 Steps)</h4>
                                    <p class="text-[10px] text-zinc-500">Triggers when shoot status updates to Completed.</p>
                                </div>
                            </div>
                            <button class="h-7 px-3 rounded-lg bg-zinc-900 text-white text-[10px] font-bold border-0 cursor-pointer hover:bg-zinc-800">Active</button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2">
                            <div class="p-2.5 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200/60 dark:border-zinc-800 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 1 • Immediate</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate block mt-0.5">Proofing Underway Notice</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200/60 dark:border-zinc-800 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 2 • Day 2 Delay</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate block mt-0.5">High-Res Gallery Passcode Link</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200/60 dark:border-zinc-800 text-[11px]">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase block">Step 3 • Day 7 Delay</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate block mt-0.5">Google Business Review Request</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUB-TAB 4: SENT LOGS & OUTBOX -->
        <div id="email-tab-outbox" class="cora-email-tab-content hidden flex-1 flex-col space-y-4">
            <!-- Filter Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs">
                <div class="flex items-center gap-3 flex-1">
                    <input type="text" id="outbox-search-input" placeholder="Search by recipient or subject..." oninput="filterOutboxLogs()"
                           class="h-9 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs text-zinc-900 dark:text-zinc-100 outline-hidden w-full sm:w-72" />
                    <select id="outbox-status-filter" onchange="filterOutboxLogs()"
                            class="h-9 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs font-semibold text-zinc-800 dark:text-zinc-200 outline-hidden cursor-pointer">
                        <option value="">All Delivery Statuses</option>
                        <option value="delivered">Delivered</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <button onclick="exportOutboxLogsCSV()" class="h-9 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export CSV
                </button>
            </div>

            <!-- Outbox Log Table Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                                <th class="py-3 px-4">Recipient</th>
                                <th class="py-3 px-4">Subject</th>
                                <th class="py-3 px-4">Sent Timestamp</th>
                                <th class="py-3 px-4">Delivery Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="outbox-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                            <!-- Populated dynamically via renderEmailLogs() -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SUB-TAB 5: SMTP & PROVIDER SETTINGS -->
        <div id="email-tab-settings" class="cora-email-tab-content hidden flex-1 flex-col space-y-6">
            <div class="max-w-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs">
                <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4 mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                            Hostinger Business SMTP Credentials & Relay
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Configure your official Hostinger SMTP mail relay details for guaranteed inbox deliverability.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-semibold shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600 dark:text-emerald-400"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        System Locked
                    </span>
                </div>

                <form id="smtp-settings-form" class="space-y-4" onsubmit="event.preventDefault();">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">SMTP Server Host *</label>
                            <input type="text" id="smtp-host" value="<?php echo esc_attr(get_option('cora_smtp_host', 'smtp.hostinger.com')); ?>" readonly
                                   class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-xs font-semibold text-zinc-500 dark:text-zinc-400 cursor-not-allowed outline-hidden" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">SMTP Port *</label>
                            <input type="text" id="smtp-port" value="<?php echo esc_attr(get_option('cora_smtp_port', '465')); ?>" readonly
                                   class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-xs font-semibold text-zinc-500 dark:text-zinc-400 cursor-not-allowed outline-hidden" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Encryption Method</label>
                            <?php $sec = get_option('cora_smtp_secure', 'ssl'); ?>
                            <select id="smtp-secure" disabled
                                    class="w-full h-10 px-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-xs font-semibold text-zinc-500 dark:text-zinc-400 cursor-not-allowed outline-hidden">
                                <option value="ssl" <?php selected($sec, 'ssl'); ?>>SSL (Port 465 Recommended)</option>
                                <option value="tls" <?php selected($sec, 'tls'); ?>>TLS / STARTTLS (Port 587)</option>
                                <option value="none" <?php selected($sec, 'none'); ?>>None (Plaintext)</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">SMTP Username *</label>
                            <input type="email" id="smtp-username" value="<?php echo esc_attr(get_option('cora_smtp_username', 'heycora@claraverse.in')); ?>" readonly
                                   class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-xs font-semibold text-zinc-500 dark:text-zinc-400 cursor-not-allowed outline-hidden" />
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">SMTP Password</label>
                        <input type="password" id="smtp-password" value="••••••••••••••••" readonly
                               class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-xs font-semibold text-zinc-500 dark:text-zinc-400 cursor-not-allowed outline-hidden" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Default From Name</label>
                            <input type="text" id="smtp-from-name" value="<?php echo esc_attr(get_option('cora_from_name', 'Cora')); ?>" readonly
                                   class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-xs font-semibold text-zinc-500 dark:text-zinc-400 cursor-not-allowed outline-hidden" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Default From Email</label>
                            <input type="email" id="smtp-from-email" value="<?php echo esc_attr(get_option('cora_from_email', 'heycora@claraverse.in')); ?>" readonly
                                   class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-xs font-semibold text-zinc-500 dark:text-zinc-400 cursor-not-allowed outline-hidden" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <button type="button" onclick="openSmtpTestDrawer()" class="h-10 px-4 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            Run Connection Test
                        </button>
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            Managed by System Administrator
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- RIGHT-SLIDING SIDE DRAWERS (Notion/Shopify Rule Compliance) -->
<!-- ========================================================================= -->

<!-- 1. TEMPLATE CREATOR / EDITOR DRAWER -->
<div id="cora-email-template-drawer" class="fixed inset-y-0 right-0 w-full sm:max-w-lg bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[150] flex flex-col translate-x-full transition-transform duration-300 ease-in-out">
    <div class="flex items-center justify-between p-5 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider" id="template-drawer-title">Create Email Template</h3>
        <button onclick="closeEmailTemplateDrawer()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white border-0 bg-transparent cursor-pointer text-sm font-bold">✕ Close</button>
    </div>
    
    <form id="template-editor-form" onsubmit="saveEmailTemplate(event)" class="flex-1 flex flex-col overflow-y-auto p-6 space-y-4">
        <input type="hidden" id="tpl-editor-id" value="" />
        
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Template Title *</label>
            <input type="text" id="tpl-editor-name" placeholder="e.g. Shoot Confirmation" required
                   class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden" />
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Category</label>
            <select id="tpl-editor-category" class="w-full h-10 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden">
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
            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Default Subject Line *</label>
            <input type="text" id="tpl-editor-subject" placeholder="Subject with {variable} support" required
                   class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden" />
        </div>

        <div class="space-y-1 flex-1 flex flex-col min-h-48">
            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Template Body Content *</label>
            <textarea id="tpl-editor-body" placeholder="Write template body with {client_name}, {event_name}, {portal_url}..." required
                      class="w-full flex-1 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden leading-relaxed resize-none"></textarea>
        </div>

        <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end gap-2 shrink-0">
            <button type="button" onclick="closeEmailTemplateDrawer()" class="h-9 px-4 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-600 dark:text-zinc-400 bg-transparent cursor-pointer">Cancel</button>
            <button type="submit" class="h-9 px-5 rounded-xl bg-zinc-950 dark:bg-white hover:bg-zinc-900 dark:hover:bg-zinc-50 text-white dark:text-zinc-950 text-xs font-bold border-0 cursor-pointer shadow-xs">Save Template</button>
        </div>
    </form>
</div>

<!-- 2. SENT EMAIL DETAIL DRAWER -->
<div id="cora-email-detail-drawer" class="fixed inset-y-0 right-0 w-full sm:max-w-md bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[150] flex flex-col translate-x-full transition-transform duration-300 ease-in-out">
    <div class="flex items-center justify-between p-5 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
        <h3 class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Sent Communication Details</h3>
        <button onclick="closeEmailDetailDrawer()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white border-0 bg-transparent cursor-pointer text-sm font-bold">✕ Close</button>
    </div>
    <div class="flex-1 overflow-y-auto p-5 space-y-4">
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 font-bold uppercase">To Recipient</div>
            <div id="drawer-email-to" class="text-xs font-semibold text-zinc-900 dark:text-zinc-100"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 font-bold uppercase">Sent At</div>
            <div id="drawer-email-date" class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 font-bold uppercase">Subject</div>
            <div id="drawer-email-subject" class="text-xs font-bold text-zinc-900 dark:text-zinc-100"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 font-bold uppercase">Message Payload</div>
            <div id="drawer-email-message" class="text-xs text-zinc-800 dark:text-zinc-200 bg-zinc-50 dark:bg-zinc-900 p-4 rounded-xl whitespace-pre-wrap leading-relaxed border border-zinc-200 dark:border-zinc-800 font-medium"></div>
        </div>
        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
            <button type="button" id="btn-resend-drawer-email" onclick="resendDrawerEmail()"
                    class="w-full h-10 rounded-xl bg-zinc-950 dark:bg-white hover:bg-zinc-900 dark:hover:bg-zinc-50 text-white dark:text-zinc-950 text-xs font-bold flex items-center justify-center gap-2 cursor-pointer border-0 shadow-xs">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                Resend This Email
            </button>
        </div>
    </div>
</div>

<!-- 3. SMTP DIAGNOSTIC TEST DRAWER -->
<div id="cora-smtp-test-drawer" class="fixed inset-y-0 right-0 w-full sm:max-w-md bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[150] flex flex-col translate-x-full transition-transform duration-300 ease-in-out">
    <div class="flex items-center justify-between p-5 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
        <h3 class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Hostinger SMTP Relay Diagnostic</h3>
        <button onclick="closeSmtpTestDrawer()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white border-0 bg-transparent cursor-pointer text-sm font-bold">✕ Close</button>
    </div>
    
    <div class="flex-1 overflow-y-auto p-5 space-y-4">
        <p class="text-xs text-zinc-500 dark:text-zinc-400">Send an instant test email to verify host connection, port 587 handshakes, and SSL/TLS authentication status.</p>
        
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Test Recipient Email *</label>
            <input type="email" id="smtp-test-recipient" value="<?php echo esc_attr(get_option('admin_email')); ?>" required
                   class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden" />
        </div>

        <button type="button" id="btn-run-smtp-test" onclick="runSmtpDiagnosticTest()"
                class="w-full h-10 rounded-xl bg-zinc-950 dark:bg-white hover:bg-zinc-900 dark:hover:bg-zinc-50 text-white dark:text-zinc-950 text-xs font-bold flex items-center justify-center gap-2 cursor-pointer border-0 shadow-xs">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
            Send Diagnostic Test Email
        </button>

        <div class="space-y-1.5 pt-2">
            <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase">Diagnostic Output Log</div>
            <div id="smtp-test-console" class="bg-zinc-900 text-zinc-200 p-4 rounded-xl font-mono text-[11px] min-h-40 overflow-y-auto leading-relaxed border border-zinc-800">
                Ready to execute diagnostic ping to smtp.hostinger.com:587...
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
        $('.cora-email-sub-tab').removeClass('text-zinc-900 dark:text-white border-zinc-950 dark:border-white font-bold').addClass('text-zinc-500 dark:text-zinc-400 border-transparent font-semibold');
        $('#tab-btn-' + tabId).removeClass('text-zinc-500 dark:text-zinc-400 border-transparent font-semibold').addClass('text-zinc-900 dark:text-white border-zinc-950 dark:border-white font-bold');

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

    function updateDashboardKPIs() {
        const stats = emailData.stats || {};
        $('#stat-total-sent').text(stats.total_sent || 0);
        $('#stat-month-sent').text((stats.month_sent || 0) + ' sent this month');
        $('#stat-success-rate').text((stats.success_rate || 99.4) + '%');
        $('#stat-active-templates').text((stats.active_templates || 6) + ' Active');
        $('#stat-smtp-provider').text(stats.from_email || 'Hostinger Business');
    }

    function populateRecipientsDropdown() {
        const $picker = $('#compose-recipient-picker');
        $picker.html('<option value="">-- Choose from Leads / Clients --</option>');
        (emailData.recipients || []).forEach(r => {
            $picker.append(`<option value="${escapeHtml(r.email)}" data-name="${escapeHtml(r.name)}">${escapeHtml(r.name)} (${escapeHtml(r.email)})</option>`);
        });
    }

    window.onRecipientPickerChange = function(select) {
        const email = $(select).val();
        if (email) {
            $('#email-to').val(email);
            updateLivePreview();
        }
    };

    function populateTemplatesDropdown() {
        const $tplSel = $('#compose-template-selector');
        $tplSel.html('<option value="">-- Select Template to Pre-fill --</option>');
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

        // Sample Variable Replacements for Preview
        let processedBody = rawBody
            .replace(/\{client_name\}/g, 'Aarav Sharma')
            .replace(/\{event_name\}/g, 'Pre-Wedding Documentary')
            .replace(/\{event_date\}/g, 'August 14, 2026')
            .replace(/\{event_location\}/g, 'Taj West End, Bengaluru')
            .replace(/\{portal_url\}/g, 'https://heycora.in/portal/view')
            .replace(/\{invoice_amount\}/g, '₹45,000')
            .replace(/\{due_amount\}/g, '₹15,000')
            .replace(/\{studio_name\}/g, 'Cora Studio');

        let processedSubject = subject
            .replace(/\{client_name\}/g, 'Aarav Sharma')
            .replace(/\{event_name\}/g, 'Pre-Wedding Documentary')
            .replace(/\{studio_name\}/g, 'Cora Studio');

        $('#preview-to').text(to);
        $('#preview-subject').text(processedSubject);
        $('#preview-body').text(processedBody);
    };

    window.resetEmailComposeForm = function() {
        $('#cora-email-compose-form')[0].reset();
        $('#compose-recipient-picker').val('');
        $('#compose-template-selector').val('');
        updateLivePreview();
    };

    window.attachVaultDocumentSim = function() {
        const sampleUrl = 'https://heycora.in/vault/document/' + Math.floor(Math.random()*10000);
        insertVariableTag(`\n\nDocument Vault Link:\n${sampleUrl}\n`);
        window.coraShowToast && window.coraShowToast("Vault document link attached to body! ✓", "success");
    };

    // RENDER TEMPLATES IN TAB 2
    window.renderEmailTemplates = function() {
        const $grid = $('#templates-grid-container');
        const query = ($('#template-search-input').val() || '').toLowerCase();
        const catFilter = $('#template-category-filter').val() || '';

        const filtered = (emailData.templates || []).filter(tpl => {
            const matchesQuery = tpl.name.toLowerCase().includes(query) || tpl.subject.toLowerCase().includes(query);
            const matchesCat = !catFilter || tpl.category === catFilter;
            return matchesQuery && matchesCat;
        });

        if (filtered.length === 0) {
            $grid.html('<div class="col-span-full py-12 text-center text-zinc-400 dark:text-zinc-500 italic text-xs">No email templates found matching filters.</div>');
            return;
        }

        $grid.html('');
        filtered.forEach(tpl => {
            const card = $(`
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 shadow-3xs flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[10px] font-bold text-zinc-700 dark:text-zinc-300">${escapeHtml(tpl.category)}</span>
                            ${tpl.is_system ? '<span class="text-[9px] font-mono text-zinc-400">System Preset</span>' : '<span class="text-[9px] font-mono text-emerald-600 dark:text-emerald-400">Custom Template</span>'}
                        </div>
                        <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">${escapeHtml(tpl.name)}</h4>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-semibold truncate">Subject: ${escapeHtml(tpl.subject)}</p>
                        <p class="text-[10px] text-zinc-400 line-clamp-2 leading-relaxed font-sans">${escapeHtml(tpl.body)}</p>
                    </div>

                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <button onclick="loadTemplateIntoComposer('${tpl.id}'); coraSwitchEmailSubTab('email-tab-compose');" class="text-[11px] font-bold text-zinc-900 dark:text-zinc-100 hover:underline bg-transparent border-0 cursor-pointer">
                            Use Template →
                        </button>
                        <div class="flex items-center gap-1.5">
                            <button onclick="editEmailTemplate('${tpl.id}')" class="px-2 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-[10px] font-semibold text-zinc-700 dark:text-zinc-300 border-0 cursor-pointer">Edit</button>
                            ${!tpl.is_system ? `<button onclick="deleteEmailTemplate('${tpl.id}')" class="px-2 py-1 rounded-lg bg-red-50 dark:bg-red-950/50 hover:bg-red-100 text-[10px] font-semibold text-red-600 dark:text-red-400 border-0 cursor-pointer">Delete</button>` : ''}
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
        logs.forEach((log, idx) => {
            const dateStr = log.sent_at ? new Date(log.sent_at.replace(/-/g, '/')).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Just now';
            const row = $(`
                <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-850/50 transition-all cursor-pointer">
                    <td class="py-3 px-4 font-bold text-zinc-900 dark:text-zinc-100">${escapeHtml(log.to)}</td>
                    <td class="py-3 px-4 font-medium text-zinc-700 dark:text-zinc-300 max-w-xs truncate">${escapeHtml(log.subject)}</td>
                    <td class="py-3 px-4 font-mono text-[11px] text-zinc-400">${dateStr}</td>
                    <td class="py-3 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            ${escapeHtml(log.status || 'Delivered')}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <button onclick='openEmailDetailDrawer(${JSON.stringify(log)})' class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-[10px] font-semibold text-zinc-800 dark:text-zinc-200 border-0 cursor-pointer">
                            View Details
                        </button>
                    </td>
                </tr>
            `);
            $tbody.append(row);
        });
    }

    window.filterOutboxLogs = renderEmailLogs;

    // DRAWER OPEN / CLOSE CONTROLLERS (Rule Compliance with coraCloseAllDrawers)
    window.openEmailTemplateDrawer = function(tplId = null) {
        window.coraCloseAllDrawers && window.coraCloseAllDrawers();
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
            $('#template-drawer-title').text('Create Custom Template');
        }

        $('#cora-email-template-drawer').removeClass('translate-x-full pointer-events-none');
        $('#cora-drawer-backdrop').removeClass('hidden').css({'display': 'block', 'pointer-events': 'auto'});
        $('body').addClass('cora-drawer-open overflow-hidden');
    };

    window.closeEmailTemplateDrawer = function() {
        window.coraCloseAllDrawers && window.coraCloseAllDrawers();
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
                    window.coraShowToast && window.coraShowToast(res.data.message, "success");
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
                        window.coraShowToast && window.coraShowToast(res.data.message, "success");
                        emailData.templates = res.data.templates || [];
                        renderEmailTemplates();
                        populateTemplatesDropdown();
                    } else {
                        window.coraShowToast && window.coraShowToast(res.data.message, "error");
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

    // SENT EMAIL DETAIL DRAWER CONTROLLER
    window.openEmailDetailDrawer = function(log) {
        window.coraCloseAllDrawers && window.coraCloseAllDrawers();
        activeLogForDetail = log;

        const dateStr = log.sent_at ? new Date(log.sent_at.replace(/-/g, '/')).toLocaleString([], { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Just now';

        $('#drawer-email-to').text(log.to);
        $('#drawer-email-date').text(dateStr);
        $('#drawer-email-subject').text(log.subject);
        $('#drawer-email-message').text(log.message);

        $('#cora-email-detail-drawer').removeClass('translate-x-full pointer-events-none');
        $('#cora-drawer-backdrop').removeClass('hidden').css({'display': 'block', 'pointer-events': 'auto'});
        $('body').addClass('cora-drawer-open overflow-hidden');
    };

    window.closeEmailDetailDrawer = function() {
        window.coraCloseAllDrawers && window.coraCloseAllDrawers();
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
                    window.coraShowToast && window.coraShowToast(res.data.message, "success");
                    emailData.sent_logs = res.data.sent_logs || [];
                    renderEmailLogs();
                    closeEmailDetailDrawer();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Resend failed.", "error");
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                    Resend This Email
                `);
            }
        });
    };

    // SMTP SETTINGS FORM & DIAGNOSTICS
    function populateSmtpForm() {
        const smtp = emailData.smtp || {};
        if (smtp.smtp_host) $('#smtp-host').val(smtp.smtp_host);
        if (smtp.smtp_port) $('#smtp-port').val(smtp.smtp_port);
        if (smtp.smtp_secure) $('#smtp-secure').val(smtp.smtp_secure);
        if (smtp.smtp_username) $('#smtp-username').val(smtp.smtp_username);
        if (smtp.from_name) $('#smtp-from-name').val(smtp.from_name);
        if (smtp.from_email) $('#smtp-from-email').val(smtp.from_email);
    }

    window.saveSmtpSettings = function(e) {
        e.preventDefault();
        const $btn = $('#btn-save-smtp');
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: getAjaxEndpoint(),
            method: 'POST',
            data: {
                action: 'cora_save_smtp_settings',
                nonce: getAjaxNonce(),
                security: getAjaxNonce(),
                smtp_host: $('#smtp-host').val().trim(),
                smtp_port: $('#smtp-port').val().trim(),
                smtp_secure: $('#smtp-secure').val(),
                smtp_username: $('#smtp-username').val().trim(),
                smtp_password: $('#smtp-password').val().trim(),
                from_name: $('#smtp-from-name').val().trim(),
                from_email: $('#smtp-from-email').val().trim()
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast && window.coraShowToast(res.data.message, "success");
                    emailData.smtp = res.data.smtp || {};
                    updateDashboardKPIs();
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Failed to save SMTP settings.", "error");
                }
            },
            complete: function() {
                $btn.prop('disabled', false).text('Save SMTP Settings');
            }
        });
    };

    window.openSmtpTestDrawer = function() {
        window.coraCloseAllDrawers && window.coraCloseAllDrawers();
        $('#cora-smtp-test-drawer').removeClass('translate-x-full pointer-events-none');
        $('#cora-drawer-backdrop').removeClass('hidden').css({'display': 'block', 'pointer-events': 'auto'});
        $('body').addClass('cora-drawer-open overflow-hidden');
    };

    window.closeSmtpTestDrawer = function() {
        window.coraCloseAllDrawers && window.coraCloseAllDrawers();
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
        $console.html(`[PING] Connecting to smtp.hostinger.com:587...\n[AUTH] Verifying SSL/TLS handshake for ${testRecipient}...`);

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
                    $console.html(`[SUCCESS] Connected to ${diag.host}:${diag.port}\n[TLS] Handshake OK (${diag.encryption})\n[RECP] Sent test packet to ${diag.recipient}\n[TIME] ${diag.sent_at}\n[STATUS] ${diag.status}`);
                    window.coraShowToast && window.coraShowToast(res.data.message, "success");
                    loadEmailDashboardData();
                } else {
                    const diag = res.data.diagnostic || {};
                    $console.html(`[ERROR] Connection failed to ${diag.host}:${diag.port}\n[FAIL] ${diag.error || 'SMTP Relay Authentication Error'}`);
                    window.coraShowToast && window.coraShowToast(res.data.message, "error");
                }
            },
            error: function() {
                $console.html(`[FATAL] Network error connecting to WordPress AJAX relay.`);
                window.coraShowToast && window.coraShowToast("SMTP diagnostic failed to execute.", "error");
            },
            complete: function() {
                $btn.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                    Send Diagnostic Test Email
                `);
            }
        });
    };

    // DIRECT COMPOSE FORM SUBMISSION
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
            <svg class="animate-spin shrink-0" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M22 12a10 10 0 0 1-10 10"></path></svg>
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
                    window.coraShowToast && window.coraShowToast(res.data.message || "Email sent officially via Hostinger SMTP! ✓", "success");
                    resetEmailComposeForm();
                    if (res.data.sent_logs) {
                        emailData.sent_logs = res.data.sent_logs;
                        renderEmailLogs();
                        updateDashboardKPIs();
                    } else {
                        loadEmailDashboardData();
                    }
                } else {
                    window.coraShowToast && window.coraShowToast(res.data.message || "Failed to send email. Check Hostinger SMTP settings.", "error");
                }
            },
            error: function(err) {
                const errMsg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : "Failed to send email. Check Hostinger SMTP settings.";
                window.coraShowToast && window.coraShowToast(errMsg, "error");
            },
            complete: function() {
                $btn.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
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
</script>
