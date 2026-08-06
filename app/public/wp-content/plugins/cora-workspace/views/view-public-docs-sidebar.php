<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<aside class="w-64 flex-shrink-0 flex flex-col gap-6 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto pr-2 scrollbar-thin select-none">
    
    <!-- PAGE CATEGORIES COLLAPSIBLE GROUPS -->
    <div class="space-y-3">
        <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-550 select-none px-1.5 mb-2">Categories</h3>
        
        <!-- Category: Overview -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 dark:text-zinc-350 hover:text-zinc-950 dark:hover:text-zinc-50 py-1 px-1.5 rounded hover:bg-zinc-100/50 dark:hover:bg-zinc-900/40 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <!-- Overview icon -->
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 dark:text-zinc-550">
                        <rect x="3" y="3" width="7" height="9"></rect>
                        <rect x="14" y="3" width="7" height="5"></rect>
                        <rect x="14" y="12" width="7" height="9"></rect>
                        <rect x="3" y="16" width="7" height="5"></rect>
                    </svg>
                    <span>Overview</span>
                </div>
                <!-- Chevron -->
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 dark:text-zinc-550 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'platform-overview', this)" data-slug="platform-overview" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">Platform Overview</a>
            </div>
        </div>

        <!-- Category: CRM -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 dark:text-zinc-350 hover:text-zinc-950 dark:hover:text-zinc-50 py-1 px-1.5 rounded hover:bg-zinc-100/50 dark:hover:bg-zinc-900/40 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <!-- CRM icon -->
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 dark:text-zinc-550">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>CRM</span>
                </div>
                <!-- Chevron -->
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 dark:text-zinc-550 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'crm-leads-funnel', this)" data-slug="crm-leads-funnel" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">Leads Funnel</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'crm-pipeline-settings', this)" data-slug="crm-pipeline-settings" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">Pipeline Settings</a>
            </div>
        </div>

        <!-- Category: Finance -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 dark:text-zinc-350 hover:text-zinc-950 dark:hover:text-zinc-50 py-1 px-1.5 rounded hover:bg-zinc-100/50 dark:hover:bg-zinc-900/40 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <!-- Finance icon -->
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 dark:text-zinc-550">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    <span>Finance</span>
                </div>
                <!-- Chevron -->
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 dark:text-zinc-550 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'finance-invoices', this)" data-slug="finance-invoices" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">Invoices Engine</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'finance-gst-engine', this)" data-slug="finance-gst-engine" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">GST Tax Math</a>
            </div>
        </div>

        <!-- Category: AI & Automation -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 dark:text-zinc-350 hover:text-zinc-950 dark:hover:text-zinc-50 py-1 px-1.5 rounded hover:bg-zinc-100/50 dark:hover:bg-zinc-900/40 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <!-- AI & Automation icon -->
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 dark:text-zinc-550">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span>AI & Automation</span>
                </div>
                <!-- Chevron -->
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 dark:text-zinc-550 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'ai-summaries-registry', this)" data-slug="ai-summaries-registry" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">AI Summaries</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'ai-automation-workflows', this)" data-slug="ai-automation-workflows" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">Automated Workflows</a>
            </div>
        </div>

        <!-- Category: Settings & Tools -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 dark:text-zinc-350 hover:text-zinc-950 dark:hover:text-zinc-50 py-1 px-1.5 rounded hover:bg-zinc-100/50 dark:hover:bg-zinc-900/40 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <!-- Settings & Tools icon -->
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 dark:text-zinc-550">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <span>Settings & Tools</span>
                </div>
                <!-- Chevron -->
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 dark:text-zinc-550 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'settings-mcp-gateway', this)" data-slug="settings-mcp-gateway" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">MCP Gateway</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'settings-mcp-profiles', this)" data-slug="settings-mcp-profiles" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">Server Profiles</a>
            </div>
        </div>
    </div>

    <!-- Divider -->
    <hr class="border-t border-zinc-200/60 dark:border-zinc-800/80 my-1" />

    <!-- MODULES LIST -->
    <div class="space-y-1.5">
        <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-550 select-none px-1.5 mb-2">Modules</h3>
        <div class="space-y-1">
            <!-- Billing & Subscriptions -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-billing', this)" data-slug="module-billing" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 dark:text-zinc-550">
                    <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                <span>Billing & Subscriptions</span>
            </a>
            <!-- User Management & AI Profiles -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-users', this)" data-slug="module-users" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 dark:text-zinc-555">
                    <path d="M12 2a5 5 0 1 0 0 10 5 5 0 0 0 0-10z"></path>
                    <path d="M12 14c-5.33 0-8 2.67-8 8h16c0-5.33-2.67-8-8-8z"></path>
                </svg>
                <span>User Management & AI Profiles</span>
            </a>
            <!-- Photography Studio -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-studio', this)" data-slug="module-studio" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 dark:text-zinc-550">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                    <circle cx="12" cy="13" r="4"></circle>
                </svg>
                <span>Photography Studio</span>
            </a>
            <!-- Real Estate Agency -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-realestate', this)" data-slug="module-realestate" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 dark:text-zinc-550">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Real Estate Agency</span>
            </a>
            <!-- Canvas & Frontend Module -->
            <a href="#" onclick="coraPublicLoadPage(event, 'canvas-and-frontend-module', this)" data-slug="canvas-and-frontend-module" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 dark:text-zinc-550">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
                <span>Canvas & Frontend Module</span>
            </a>
        </div>
    </div>

    <!-- Divider -->
    <hr class="border-t border-zinc-200/60 dark:border-zinc-800/80 my-1" />

    <!-- REFERENCE LIST -->
    <div class="space-y-1.5">
        <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-550 select-none px-1.5 mb-2">Reference</h3>
        <div class="space-y-1">
            <!-- API Endpoint Registry -->
            <button onclick="coraPublicShowSection('api')" class="w-full flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors bg-transparent border-none text-left cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 dark:text-zinc-500">
                    <polyline points="16 18 22 12 16 6"></polyline>
                    <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
                <span>API Endpoint Registry</span>
            </button>
            <!-- Changelog Feed -->
            <button onclick="coraPublicShowSection('changelog')" class="w-full flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors bg-transparent border-none text-left cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 dark:text-zinc-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Changelog Feed</span>
            </button>
        </div>
    </div>

    <!-- Divider -->
    <hr class="border-t border-zinc-200/60 dark:border-zinc-800/80 my-1" />

    <!-- GUIDES LIST -->
    <div class="space-y-1.5">
        <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-550 select-none px-1.5 mb-2">Guides</h3>
        <div class="space-y-1">
            <!-- Workspace Configuration Guide -->
            <a href="#" onclick="coraPublicLoadPage(event, 'workspace-configuration', this)" data-slug="workspace-configuration" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 dark:text-zinc-550">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span>Workspace Configuration Guide</span>
            </a>
            <!-- Roadmap -->
            <a href="#" onclick="coraPublicLoadPage(event, 'roadmap', this)" data-slug="roadmap" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:text-zinc-50 dark:hover:bg-zinc-900/60 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 dark:text-zinc-550">
                    <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon>
                    <line x1="9" y1="3" x2="9" y2="18"></line>
                    <line x1="15" y1="6" x2="15" y2="21"></line>
                </svg>
                <span>Roadmap</span>
            </a>
        </div>
    </div>

    <!-- HELP BANNER CARD -->
    <div class="mt-auto pt-4">
        <div class="p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-900/20 text-center space-y-3">
            <div class="text-[11px] leading-relaxed text-zinc-500 dark:text-zinc-400">
                Need help building with Cora? Ask Cora AI for instant answers from our documentation.
            </div>
            <button onclick="coraPublicShowSection('cora-ai')" class="w-full text-center py-1.5 px-2 bg-zinc-950 dark:bg-zinc-50 hover:bg-zinc-900 dark:hover:bg-white text-white dark:text-zinc-950 font-bold rounded-lg transition-colors text-[11px] cursor-pointer select-none border border-zinc-900 dark:border-zinc-100">
                Ask Cora AI
            </button>
        </div>
    </div>

</aside>

<!-- Accordion logic & active link styling integration -->
<script>
function coraToggleSidebarGroup(button) {
    const content = button.nextElementSibling;
    const chevron = button.querySelector('.cora-chevron-icon');
    if (content.classList.contains('hidden') || content.style.maxHeight === '0px') {
        content.classList.remove('hidden');
        content.style.maxHeight = content.scrollHeight + 'px';
        chevron.classList.add('rotate-180');
    } else {
        content.style.maxHeight = '0px';
        chevron.classList.remove('rotate-180');
        // Let it settle then add hidden for accessibility
        setTimeout(() => {
            if (content.style.maxHeight === '0px') {
                content.classList.add('hidden');
            }
        }, 300);
    }
}
</script>

