<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!-- Mobile Backdrop -->
<div id="cora-docs-mobile-backdrop" class="fixed inset-0 bg-zinc-950/20 backdrop-blur-sm z-40 hidden md:hidden transition-opacity opacity-0" onclick="coraToggleMobileSidebar()"></div>

<aside id="cora-docs-mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-zinc-200 transform -translate-x-full transition-transform duration-300 md:translate-x-0 md:static md:w-64 md:border-r-0 md:bg-transparent flex-shrink-0 flex flex-col gap-4 md:sticky md:top-20 md:h-[calc(100vh-5.5rem)] h-full overflow-y-auto px-4 md:px-0 pt-6 md:pt-1 pr-2 pb-10 select-none shadow-2xl md:shadow-none z-30">
    
    <!-- Mobile Close Button -->
    <div class="flex items-center justify-between md:hidden mb-2 px-1.5">
        <span class="font-bold text-sm tracking-tight font-display text-zinc-950">Menu</span>
        <button onclick="coraToggleMobileSidebar()" class="text-zinc-500 hover:text-zinc-900 p-1.5 bg-zinc-50 rounded-md border border-zinc-200 focus:outline-none cursor-pointer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <!-- PAGE CATEGORIES COLLAPSIBLE GROUPS -->
    <div class="space-y-2">
        <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 select-none px-1.5 mb-2">Categories</h3>
        
        <!-- Category: Overview -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <rect x="3" y="3" width="7" height="9"></rect>
                        <rect x="14" y="3" width="7" height="5"></rect>
                        <rect x="14" y="12" width="7" height="9"></rect>
                        <rect x="3" y="16" width="7" height="5"></rect>
                    </svg>
                    <span>Overview</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'platform-overview', this)" data-slug="platform-overview" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Platform Overview</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'architecture-overview', this)" data-slug="architecture-overview" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Architecture & Stack</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'workspace-roles', this)" data-slug="workspace-roles" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Roles & Permissions</a>
            </div>
        </div>

        <!-- Category: CRM -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>CRM</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'crm-leads-funnel', this)" data-slug="crm-leads-funnel" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Leads Funnel</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'crm-pipeline-settings', this)" data-slug="crm-pipeline-settings" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Pipeline Settings</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'crm-client-tasks', this)" data-slug="crm-client-tasks" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Client Task Manager</a>
            </div>
        </div>

        <!-- Category: Finance -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    <span>Finance</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'finance-invoices', this)" data-slug="finance-invoices" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Invoices Engine</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'finance-gst-engine', this)" data-slug="finance-gst-engine" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">GST Tax Math</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'finance-reports', this)" data-slug="finance-reports" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Financial Reports</a>
            </div>
        </div>

        <!-- Category: Operations -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Operations</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'ops-crew-scheduler', this)" data-slug="ops-crew-scheduler" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Crew Scheduler</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'ops-equipment', this)" data-slug="ops-equipment" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Equipment Management</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'ops-event-timeline', this)" data-slug="ops-event-timeline" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Event Timeline</a>
            </div>
        </div>

        <!-- Category: Media & Assets -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <span>Media & Assets</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 0px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'media-library', this)" data-slug="media-library" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Media Library</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'media-editor', this)" data-slug="media-editor" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Media Editor</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'media-gallery', this)" data-slug="media-gallery" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Public Gallery Portal</a>
            </div>
        </div>

        <!-- Category: Client Portal -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    <span>Client Portal</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 0px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'portal-client-tasks', this)" data-slug="portal-client-tasks" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Client Task Manager</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'portal-forms', this)" data-slug="portal-forms" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Forms Builder</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'portal-reviews', this)" data-slug="portal-reviews" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Review Acquisition</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'portal-comments', this)" data-slug="portal-comments" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Comments System</a>
            </div>
        </div>

        <!-- Category: Document Vault -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span>Document Vault</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 0px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'vault-overview', this)" data-slug="vault-overview" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Vault Overview</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'vault-esign', this)" data-slug="vault-esign" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">E-Sign Registry</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'vault-storage', this)" data-slug="vault-storage" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Document Storage</a>
            </div>
        </div>

        <!-- Category: Communications -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span>Communications</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 0px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'comms-email-studio', this)" data-slug="comms-email-studio" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Email Studio</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'comms-comments', this)" data-slug="comms-comments" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Comments Thread</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'comms-push-notifications', this)" data-slug="comms-push-notifications" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Push Notifications <span class="ml-1 text-[9px] font-bold px-1 py-0.5 rounded bg-zinc-100 border border-zinc-200 text-zinc-500">NEW</span></a>
            </div>
        </div>

        <!-- Category: Content & SEO -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <polyline points="4 17 10 11 4 5"></polyline>
                        <line x1="12" y1="19" x2="20" y2="19"></line>
                    </svg>
                    <span>Content & SEO</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 0px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'content-suite', this)" data-slug="content-suite" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Content Suite</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'content-seo-geo', this)" data-slug="content-seo-geo" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">SEO & GEO Inspector</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'content-google-profile', this)" data-slug="content-google-profile" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Google Business Profile</a>
            </div>
        </div>

        <!-- Category: AI & Automation -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span>AI & Automation</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 0px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'ai-summaries-registry', this)" data-slug="ai-summaries-registry" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">AI Summaries</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'ai-automation-workflows', this)" data-slug="ai-automation-workflows" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Automated Workflows</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'ai-rag-knowledge-base', this)" data-slug="ai-rag-knowledge-base" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">RAG Knowledge Base</a>
            </div>
        </div>

        <!-- Category: Settings & Tools -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <span>Settings & Tools</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 0px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'settings-mcp-gateway', this)" data-slug="settings-mcp-gateway" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">MCP Gateway</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'settings-mcp-profiles', this)" data-slug="settings-mcp-profiles" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Server Profiles</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'settings-audit-panel', this)" data-slug="settings-audit-panel" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Audit Panel</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'settings-roles', this)" data-slug="settings-roles" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Role Management</a>
            </div>
        </div>

        <!-- Category: PWA & Mobile -->
        <div class="cora-sidebar-group">
            <button onclick="coraToggleSidebarGroup(this)" class="w-full flex items-center justify-between text-xs font-semibold text-zinc-750 hover:text-zinc-950 py-1 px-1.5 rounded hover:bg-zinc-100/50 focus:outline-none transition-colors duration-150">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 ">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                    </svg>
                    <span>PWA & Mobile</span>
                    <span class="text-[9px] font-bold px-1 py-0.5 rounded bg-zinc-950 text-white border border-zinc-900">NEW</span>
                </div>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron-icon text-zinc-400 transition-transform duration-200 rotate-180">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="cora-sidebar-group-content overflow-hidden transition-all duration-300 pl-6 pr-1 pt-1 space-y-1" style="max-height: 500px;">
                <a href="#" onclick="coraPublicLoadPage(event, 'pwa-setup', this)" data-slug="pwa-setup" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">PWA Setup & Install</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'pwa-push-notifications', this)" data-slug="pwa-push-notifications" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Push Notifications (VAPID)</a>
                <a href="#" onclick="coraPublicLoadPage(event, 'pwa-service-worker', this)" data-slug="pwa-service-worker" class="cora-nav-link block text-xs py-1 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">Service Worker</a>
            </div>
        </div>

    </div>

    <!-- Divider -->
    <hr class="border-t border-zinc-200/60 my-1" />

    <!-- MODULES LIST -->
    <div class="space-y-1.5">
        <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 select-none px-1.5 mb-2">Modules</h3>
        <div class="space-y-1">
            <!-- Billing & Subscriptions -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-billing', this)" data-slug="module-billing" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                <span>Billing & Subscriptions</span>
            </a>
            <!-- User Management & AI Profiles -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-users', this)" data-slug="module-users" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <path d="M12 2a5 5 0 1 0 0 10 5 5 0 0 0 0-10z"></path>
                    <path d="M12 14c-5.33 0-8 2.67-8 8h16c0-5.33-2.67-8-8-8z"></path>
                </svg>
                <span>User Management & AI Profiles</span>
            </a>
            <!-- Photography Studio -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-studio', this)" data-slug="module-studio" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                    <circle cx="12" cy="13" r="4"></circle>
                </svg>
                <span>Photography Studio</span>
            </a>
            <!-- Real Estate Agency -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-realestate', this)" data-slug="module-realestate" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Real Estate Agency</span>
            </a>
            <!-- Canvas & Frontend Module -->
            <a href="#" onclick="coraPublicLoadPage(event, 'canvas-and-frontend-module', this)" data-slug="canvas-and-frontend-module" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
                <span>Canvas & Frontend Module</span>
            </a>
            <!-- Document Vault -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-vault', this)" data-slug="module-vault" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>Document Vault</span>
            </a>
            <!-- Content Suite -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-content-suite', this)" data-slug="module-content-suite" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
                <span>Content Suite</span>
            </a>
            <!-- Forms Builder -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-forms', this)" data-slug="module-forms" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
                <span>Forms Builder</span>
            </a>
            <!-- Visual Builder -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-visual-builder', this)" data-slug="module-visual-builder" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                <span>Visual Builder</span>
            </a>
            <!-- PWA Module -->
            <a href="#" onclick="coraPublicLoadPage(event, 'module-pwa', this)" data-slug="module-pwa" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                </svg>
                <span>PWA & Mobile Shell <span class="ml-1 text-[9px] font-bold px-1 py-0.5 rounded bg-zinc-100 border border-zinc-200 text-zinc-500">NEW</span></span>
            </a>
        </div>
    </div>

    <!-- Divider -->
    <hr class="border-t border-zinc-200/60 my-1" />

    <!-- REFERENCE LIST -->
    <div class="space-y-1.5">
        <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 select-none px-1.5 mb-2">Reference</h3>
        <div class="space-y-1">
            <!-- API Endpoint Registry -->
            <button onclick="coraPublicShowSection('api')" class="w-full flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors bg-transparent border-none text-left cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <polyline points="16 18 22 12 16 6"></polyline>
                    <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
                <span>API Endpoint Registry</span>
            </button>
            <!-- Changelog Feed -->
            <button onclick="coraPublicShowSection('changelog')" class="w-full flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors bg-transparent border-none text-left cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Changelog Feed</span>
                <span class="ml-auto text-[9px] font-bold px-1 py-0.5 rounded bg-zinc-950 text-white border border-zinc-900">v3.2.46</span>
            </button>
        </div>
    </div>

    <!-- Divider -->
    <hr class="border-t border-zinc-200/60 my-1" />

    <!-- GUIDES LIST -->
    <div class="space-y-1.5">
        <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 select-none px-1.5 mb-2">Guides</h3>
        <div class="space-y-1">
            <!-- Workspace Configuration Guide -->
            <a href="#" onclick="coraPublicLoadPage(event, 'workspace-configuration', this)" data-slug="workspace-configuration" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span>Workspace Configuration</span>
            </a>
            <!-- Onboarding Guide -->
            <a href="#" onclick="coraPublicLoadPage(event, 'guide-onboarding', this)" data-slug="guide-onboarding" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                    <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                    <line x1="6" y1="1" x2="6" y2="4"></line>
                    <line x1="10" y1="1" x2="10" y2="4"></line>
                    <line x1="14" y1="1" x2="14" y2="4"></line>
                </svg>
                <span>Onboarding Flow</span>
            </a>
            <!-- PWA Install Guide -->
            <a href="#" onclick="coraPublicLoadPage(event, 'guide-pwa-install', this)" data-slug="guide-pwa-install" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>PWA Install Guide <span class="ml-1 text-[9px] font-bold px-1 py-0.5 rounded bg-zinc-100 border border-zinc-200 text-zinc-500">NEW</span></span>
            </a>
            <!-- Roadmap -->
            <a href="#" onclick="coraPublicLoadPage(event, 'roadmap', this)" data-slug="roadmap" class="cora-nav-link flex items-center gap-2.5 text-xs py-1.5 px-2 rounded-md text-zinc-650 hover:text-zinc-950 hover:bg-zinc-100/70 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-450 ">
                    <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon>
                    <line x1="9" y1="3" x2="9" y2="18"></line>
                    <line x1="15" y1="6" x2="15" y2="21"></line>
                </svg>
                <span>Roadmap</span>
            </a>
        </div>
    </div>

    <!-- HELP BANNER CARD -->
    <div class="mt-auto pt-2">
        <div class="p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50 text-center space-y-3">
            <div class="text-[11px] leading-relaxed text-zinc-500 ">
                Need help building with Cora? Ask Cora AI for instant answers from our documentation.
            </div>
            <button onclick="window.coraToggleAiSidebar(true)" class="w-full text-center py-1.5 px-2 bg-zinc-950 hover:bg-zinc-900 text-white font-bold rounded-lg transition-colors text-[11px] cursor-pointer select-none border border-zinc-900 ">
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

// Add Mobile Toggle Logic
function coraToggleMobileSidebar() {
    const sidebar = document.getElementById('cora-docs-mobile-sidebar');
    const backdrop = document.getElementById('cora-docs-mobile-backdrop');
    
    if (sidebar.classList.contains('-translate-x-full')) {
        // Open
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        // small delay for transition
        setTimeout(() => { backdrop.classList.remove('opacity-0'); backdrop.classList.add('opacity-100'); }, 10);
    } else {
        // Close
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        setTimeout(() => { backdrop.classList.add('hidden'); }, 300);
    }
}

// Intercept link clicks to close the sidebar on mobile
document.addEventListener('DOMContentLoaded', () => {
    const sidebarLinks = document.querySelectorAll('#cora-docs-mobile-sidebar a.cora-nav-link, #cora-docs-mobile-sidebar button[onclick^="coraPublicShowSection"]');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                coraToggleMobileSidebar();
            }
        });
    });
});
</script>

