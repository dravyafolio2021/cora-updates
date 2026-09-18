<?php
/**
 * View: App Modules & Feature Hub
 * Allows workspace owners to dynamically enable or disable modules at the workspace tenant level.
 * Features customizable switches with an explicit Save Changes workflow, batch controls, and instant layout synchronization.
 * Follows the precise Notion/Shopify monochromatic card specification.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cora_industry = ! empty( $_GET['industry'] )
    ? sanitize_text_field( $_GET['industry'] )
    : ( function_exists( 'cora_get_active_industry' ) ? cora_get_active_industry() : 'real_estate' );
$is_studio = ( strpos( strtolower( $cora_industry ), 'photo' ) !== false || strpos( strtolower( $cora_industry ), 'studio' ) !== false );
$is_agency = ( strpos( strtolower( $cora_industry ), 'agency' ) !== false || strpos( strtolower( $cora_industry ), 'professional' ) !== false || strpos( strtolower( $cora_industry ), 'consult' ) !== false || strpos( strtolower( $cora_industry ), 'legal' ) !== false || strpos( strtolower( $cora_industry ), 'tax' ) !== false );
$enabled = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();

// ── Complete Platform Modules Matrix (All Categories & Operating Layers) ──
$features_list = array(
    'Platform Core & Foundation' => array(
        'blogs' => array(
            'title' => 'Content Suite & CMS',
            'desc'  => 'Create and publish custom blog articles, editorial stories, SEO posts, and marketing materials.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
        ),
        'financials' => array(
            'title' => 'Financials & GST Invoicing',
            'desc'  => 'Track workspace revenue, GST SAC 9983 tax invoices, payment links, and cashflow projections.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
        ),
        'team-roles' => array(
            'title' => 'User & Role Governance',
            'desc'  => 'Configure team members, custom permissions, role hierarchies, and staff management.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
        ),
        'media' => array(
            'title' => 'Media Manager',
            'desc'  => 'Manage images, video assets, photo shoots, proofs, delivery galleries, and client approvals.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>'
        ),
        'vault' => array(
            'title' => 'File & Document Vault',
            'desc'  => 'Secure encrypted file storage for client contracts, signed NDAs, deliverables, and RAW files.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>'
        ),
        'calendar' => array(
            'title' => 'Consolidated Calendar',
            'desc'  => 'Master unified calendar for client appointments, production bookings, and team schedules.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
        ),
        'activity-timeline' => array(
            'title' => 'Activity Timeline & Audit',
            'desc'  => 'Multi-day chronological audit stream of all workspace actions, changes, and operational logs.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
        ),
        'automations' => array(
            'title' => 'Automations & Workflows',
            'desc'  => 'Configure event triggers, status transitions, webhooks, and automated background loops.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>'
        ),
        'inbox' => array(
            'title' => 'Unified Inbox Hub',
            'desc'  => 'Central communication hub integrating WhatsApp Cloud API, email threads, and client queries.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>'
        ),
        'analytics' => array(
            'title' => 'Analytics & Telemetry',
            'desc'  => 'Executive intelligence charts, revenue velocity graphs, conversion funnels, and KPIs.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
        )
    ),
    'Operations & Fulfillment' => array(
        'leads' => array(
            'title' => 'Leads CRM Pipeline',
            'desc'  => 'Kanban CRM stages to capture, qualify, track, and convert client inquiries.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>'
        ),
        'crew_scheduler' => array(
            'title' => 'Team & Staff Scheduler',
            'desc'  => 'Manage team shifts, availability calendars, and assign staff members to projects.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
        ),
        'equipment' => array(
            'title' => 'Asset & Equipment Registry',
            'desc'  => 'Track camera bodies, gear, custody checkouts, hardware assets, and condition audits.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>'
        ),
        'properties' => array(
            'title' => 'Property Listings & Showings',
            'desc'  => 'Real estate building inventory, geocoded map coordinates, showing dispatch, and unit availability.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'
        ),
        'tasks' => array(
            'title' => 'Client Task Manager',
            'desc'  => 'Collaborative task checklists, milestones, dependencies, and shared client action items.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>'
        ),
        'plant_inventory' => array(
            'title' => 'Plant & Consignment Inventory',
            'desc'  => 'Stock management, dynamic mobile van consignment allocations, live GPS tracking, and daily reconciliation.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'
        )
    ),
    'Sales Channels & Growth' => array(
        'canvas' => array(
            'title' => 'Canvas Website Builder',
            'desc'  => 'Visual landing page and interactive proposal builder with live draft previewing.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>'
        ),
        'forms' => array(
            'title' => 'Forms & Intake Manager',
            'desc'  => 'Build customer intake forms, payment collection links, and embedded contract signatures.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 15l2 2 4-4"></path></svg>'
        ),
        'emails' => array(
            'title' => 'Emails Studio & Broadcasts',
            'desc'  => 'Create and schedule custom SMTP email broadcasts, newsletters, and automated notifications.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>'
        ),
        'review_acquisition' => array(
            'title' => 'Reviews & Reputation Manager',
            'desc'  => 'Automate Google Places review collection campaigns and client satisfaction responses.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>'
        ),
        'social-meta' => array(
            'title' => 'Social Media & Ads Suite',
            'desc'  => 'Social media integration suite to preview scheduled campaigns, grids, and ad metrics.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>'
        )
    ),
    'AI Intelligence & Marketing' => array(
        'gbp' => array(
            'title' => 'Google Business Profile',
            'desc'  => 'Sync Google Business Profile listings, automate local citation reviews, and boost local SEO.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0" style="stroke: none !important; fill: none !important;"><circle cx="12" cy="12" r="11" fill="#ffffff" style="fill: #ffffff !important; stroke: #e4e4e7 !important; stroke-width: 0.8px !important;"></circle><g transform="matrix(0.55 0 0 0.55 5.4 5.4)"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" style="fill: #4285F4 !important; stroke: none !important;"></path><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" style="fill: #34A853 !important; stroke: none !important;"></path><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05" style="fill: #FBBC05 !important; stroke: none !important;"></path><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335" style="fill: #EA4335 !important; stroke: none !important;"></path></g></svg>'
        ),
        'mcp' => array(
            'title' => 'AI Tools MCP Gateway',
            'desc'  => 'Manage user-specific Model Context Protocol gateways and extensible AI developer tools.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="15" x2="23" y2="15"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="15" x2="4" y2="15"></line></svg>'
        ),
        'knowledge-base' => array(
            'title' => 'RAG Knowledge Base',
            'desc'  => 'Upload PDFs and documents to vectorize semantic search context for AI client copilot queries.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>'
        )
    ),
    'Agency & Professional Services Operating Suite' => array(
        'agency_setup' => array(
            'title' => 'Agency Setup & Profile',
            'desc'  => 'Master agency profile, logo branding, rate cards, packages, GST invoicing & working hours.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>'
        ),
        'clients' => array(
            'title' => 'Client Management & CRM Directory',
            'desc'  => 'Client directory, multiple brands, stakeholder contacts, SLA alerts, NPS satisfaction score & LTV tracking.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
        ),
        'proposals' => array(
            'title' => 'Services, Estimates & Proposals',
            'desc'  => 'Service catalogue, rate cards, package pricing, scope generator & 1-click proposal acceptance.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
        ),
        'client_portal' => array(
            'title' => 'Client Portal, Contracts & Approvals',
            'desc'  => 'Mobile-first magic link portal, digital SOW e-sign, deposit requests, 1-tap deliverable proofing & GST invoice payments.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>'
        ),
        'operating_economics' => array(
            'title' => 'Operating Economics & Margins',
            'desc'  => 'Real-time project revenue vs contractor payouts, internal cost rates & gross margin telemetry.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>'
        ),
        'partner_hub' => array(
            'title' => 'Agency Partner & Referral Network',
            'desc'  => 'Referral links, wholesale client workspace creation, partner credits & co-branded sales collateral.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>'
        ),

        'maintenance_care' => array(
            'title' => 'Web & CRO Care Plans',
            'desc'  => 'Launch QA checklists, uptime monitoring, traffic & conversion telemetry & recurring care retainers.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>'
        ),
        'enterprise_controls' => array(
            'title' => 'Enterprise Controls & Multi-Entity',
            'desc'  => 'Custom white-label domain, custom sender identity, SSO, multi-entity & multi-currency governance.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>'
        ),
        'monetisation' => array(
            'title' => 'Partner Monetisation',
            'desc'  => 'Commission tracking, wholesale workspace bundles, reseller tiers & payout transaction ledger.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>'
        ),
        'custom_workflow_builder' => array(
            'title' => 'Custom Workflow Builder',
            'desc'  => 'Visual condition triggers, branching logic, custom webhook actions & multi-app routing engine.',
            'icon'  => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>'
        )
    )
);

// Count total available modules
$total_modules_count = 0;
foreach ( $features_list as $cat => $items ) {
    $total_modules_count += count( $items );
}
$active_modules_count = 0;
foreach ( $features_list as $cat => $items ) {
    foreach ( $items as $slug => $data ) {
        if ( in_array( $slug, $enabled, true ) || ( empty( $enabled ) && in_array( $slug, array( 'agency_setup', 'team-roles', 'clients', 'tasks', 'vault', 'client_portal', 'financials', 'activity-timeline', 'leads', 'proposals', 'contracts', 'analytics', 'knowledge-base', 'automations', 'partner_hub', 'blogs', 'canvas', 'forms', 'emails', 'crew_scheduler', 'review_acquisition', 'gbp', 'mcp', 'media' ), true ) ) ) {
            $active_modules_count++;
        }
    }
}
?>

<div class="cora-fh-container" style="user-select: none; max-width: 1240px; margin: 0 auto; padding-bottom: 96px;">
    <?php
    $modules_header_args = array(
        'title'            => 'App Modules & Feature Customizer',
        'description'      => 'Enable or disable modules to tailor your workspace. Changes adapt sidebar navigation, AI Agent context, and role permissions upon saving.',
        'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect></svg>',
        'ai_stack'         => true,
        'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    );

    if ( function_exists( 'cora_render_workspace_header' ) ) {
        cora_render_workspace_header( $modules_header_args );
    }
    ?>

    <!-- Top Action Toolbar -->
    <div style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 16px; padding: 14px 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span id="cora-fh-counter-badge" style="background: #09090b; color: #ffffff; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 7px; letter-spacing: 0.01em;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #22c55e; display: inline-block;"></span>
                    <span><span id="cora-fh-active-count"><?php echo intval( $active_modules_count ); ?></span> / <?php echo intval( $total_modules_count ); ?> Active</span>
                </span>
                <span id="cora-fh-unsaved-pill" style="display: none; background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 8px;">
                    Unsaved changes
                </span>
            </div>

            <div style="width: 1px; height: 20px; background: #e4e4e7; margin: 0 4px;" class="cora-fh-divider"></div>

            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                <button type="button" id="cora-fh-select-all" class="cora-btn-batch">
                    Select All
                </button>
                <button type="button" id="cora-fh-deselect-all" class="cora-btn-batch">
                    Deselect All
                </button>
                <button type="button" id="cora-fh-reset-defaults" class="cora-btn-batch">
                    Reset Defaults
                </button>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <div style="position: relative; min-width: 200px;">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #a1a1aa; pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" id="cora-fh-search-input" placeholder="Search modules..." style="width: 100%; padding: 6px 12px 6px 30px; font-size: 12px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 8px; color: #18181b; outline: none; transition: all 0.15s; box-sizing: border-box;" onfocus="this.style.borderColor='#18181b'; this.style.background='#ffffff';" onblur="this.style.borderColor='#e4e4e7'; this.style.background='#fafafa';" />
            </div>
            <button type="button" id="cora-fh-save-top-btn" class="cora-fh-save-btn cora-btn-primary">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="cora-save-icon" style="flex-shrink: 0;">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span class="cora-save-text">Save Changes</span>
            </button>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: none;" class="cora-fh-filter-bar">
        <button type="button" class="cora-fh-cat-filter active" data-cat="all" style="padding: 7px 14px; font-size: 12px; font-weight: 700; border-radius: 9999px; background: #09090b; color: #ffffff; border: 1px solid #09090b; cursor: pointer; white-space: nowrap; transition: all 0.15s;">
            All Modules (<?php echo intval( $total_modules_count ); ?>)
        </button>
        <?php foreach ( $features_list as $category => $items ) : ?>
            <button type="button" class="cora-fh-cat-filter" data-cat="<?php echo esc_attr( sanitize_title( $category ) ); ?>" style="padding: 7px 14px; font-size: 12px; font-weight: 600; border-radius: 9999px; background: #ffffff; color: #52525b; border: 1px solid #e4e4e7; cursor: pointer; white-space: nowrap; transition: all 0.15s;">
                <?php echo esc_html( $category ); ?> (<?php echo count( $items ); ?>)
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Modules Grid Container -->
    <div style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 20px; padding: 28px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.03); box-sizing: border-box; width: 100%;">
        <form id="cora-custom-features-form" onsubmit="event.preventDefault();" style="display: flex; flex-direction: column; gap: 32px;">
            <!-- No Results Matching State -->
            <div id="cora-fh-no-results" style="display: none; padding: 48px 16px; text-align: center; flex-direction: column; align-items: center; justify-content: center; user-select: none;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #f4f4f5; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; color: #71717a;">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #18181b; margin-bottom: 4px;">No modules found</div>
                <p style="font-size: 11px; color: #71717a; margin: 0 0 14px; max-width: 280px; line-height: 1.4;">There are no results matching to the query.</p>
                <button type="button" id="cora-fh-clear-search-btn" class="cora-btn-batch" style="padding: 5px 12px;">Clear search</button>
            </div>

            <?php foreach ( $features_list as $category => $items ) : 
                $cat_slug = sanitize_title( $category );
            ?>
                <div class="cora-fh-category-block" data-cat-slug="<?php echo esc_attr( $cat_slug ); ?>" style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #f4f4f5;">
                        <h3 style="font-size: 12px; font-weight: 800; color: #71717a; text-transform: uppercase; letter-spacing: 0.06em; margin: 0;">
                            <?php echo esc_html( $category ); ?>
                        </h3>
                        <span style="font-size: 11px; font-weight: 500; color: #a1a1aa;">
                            <?php echo count( $items ); ?> modules available
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
                        <?php foreach ( $items as $slug => $data ) :
                            $is_active = in_array( $slug, $enabled, true ) || ( empty( $enabled ) && in_array( $slug, array( 'agency_setup', 'team-roles', 'clients', 'tasks', 'vault', 'client_portal', 'financials', 'activity-timeline', 'leads', 'proposals', 'contracts', 'analytics', 'knowledge-base', 'automations', 'partner_hub', 'blogs', 'canvas', 'forms', 'emails', 'crew_scheduler', 'review_acquisition', 'gbp', 'mcp', 'media' ), true ) );
                        ?>
                            <div class="cora-feature-card" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 14px; padding: 16px; display: flex; align-items: center; justify-content: space-between; gap: 14px; box-sizing: border-box; transition: border-color 0.2s, box-shadow 0.2s;">
                                <div style="display: flex; align-items: center; gap: 14px; min-width: 0; flex: 1;">
                                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #f4f4f5; display: flex; align-items: center; justify-content: center; color: #18181b; flex-shrink: 0;">
                                        <?php echo $data['icon']; ?>
                                    </div>
                                    <div style="min-width: 0; flex: 1; display: flex; flex-direction: column; gap: 3px;">
                                        <div style="font-size: 13px; font-weight: 700; color: #09090b; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            <span class="cora-feature-title" style="line-height: 1.35;"><?php echo esc_html( $data['title'] ); ?></span>
                                            <span class="cora-feature-badge" style="<?php echo $is_active ? 'display: inline-block;' : 'display: none;'; ?> font-size: 9px; font-weight: 700; background: #f4f4f5; color: #27272a; padding: 1px 6px; border-radius: 4px; border: 1px solid #e4e4e7;">
                                                Active
                                            </span>
                                        </div>
                                        <div class="cora-feature-desc" style="font-size: 11px; color: #71717a; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?php echo esc_html( $data['desc'] ); ?>
                                        </div>
                                    </div>
                                </div>

                                <div style="flex-shrink: 0; display: flex; align-items: center;">
                                    <label class="cora-switch">
                                        <input type="checkbox" name="features[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $is_active ); ?> class="cora-feature-checkbox" onchange="checkModuleDependencies('<?php echo esc_js($slug); ?>', this.checked)">
                                        <span class="cora-slider"></span>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </div>

    <!-- Floating Bottom Unsaved Changes Bar -->
    <div id="cora-fh-floating-bar" style="display: none; position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); z-index: 999; background: rgba(9, 9, 11, 0.96); backdrop-filter: blur(8px); color: #ffffff; padding: 10px 18px; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); border: 1px solid #27272a; align-items: center; gap: 16px; box-sizing: border-box;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #fbbf24; display: inline-block;"></span>
            <span style="font-size: 12px; font-weight: 600; color: #f4f4f5;">You have unsaved module changes</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="button" id="cora-fh-discard-btn" style="padding: 5px 12px; font-size: 12px; font-weight: 600; background: #27272a; color: #e4e4e7; border: 1px solid #3f3f46; border-radius: 8px; cursor: pointer; transition: all 0.15s;">
                Discard
            </button>
            <button type="button" id="cora-fh-save-bottom-btn" class="cora-fh-save-btn" style="padding: 6px 14px; font-size: 12px; font-weight: 700; background: #ffffff; color: #09090b; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.2); transition: all 0.15s;">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="cora-save-icon">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span class="cora-save-text">Save Changes</span>
            </button>
        </div>
    </div>
</div>

<!-- Recommended Companion Modules Sheet -->
<div id="cora-fh-recommendation-sheet" class="fixed inset-0 z-50 pointer-events-none transition-all duration-300">
    <div id="cora-fh-rec-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="coraCloseRecSheet()"></div>
    <div id="cora-fh-rec-drawer" class="absolute bottom-0 inset-x-0 bg-white rounded-t-3xl border-t border-zinc-200 shadow-2xl p-6 sm:p-8 max-w-lg mx-auto transform translate-y-full transition-transform duration-300 pointer-events-auto" style="box-shadow: 0 -10px 40px rgba(0,0,0,0.15);">
        <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto mb-5"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-zinc-950 text-white flex items-center justify-center font-bold text-xs">
                    ⚡
                </div>
                <h3 class="text-base font-bold text-zinc-950 tracking-tight">Recommended Modules</h3>
            </div>
            <button type="button" onclick="coraCloseRecSheet()" class="text-zinc-400 hover:text-zinc-700 text-sm font-bold p-1">✕</button>
        </div>
        <p class="text-xs text-zinc-600 mb-4 leading-relaxed">
            Activating <strong id="cora-fh-rec-module-name" class="text-zinc-900">Module</strong> works best with the following companion features:
        </p>
        <div id="cora-fh-rec-list" class="space-y-2.5 mb-6">
            <!-- Dynamic items rendered via JS -->
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="coraCloseRecSheet()" class="flex-1 py-2.5 rounded-xl border border-zinc-300 text-xs font-bold text-zinc-700 hover:bg-zinc-50 transition-all">
                Skip for now
            </button>
            <button type="button" id="cora-fh-rec-activate-btn" class="flex-1 py-2.5 rounded-xl bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all shadow-sm">
                <span id="cora-fh-rec-btn-text">Activate All Recommended</span>
            </button>
        </div>
    </div>
</div>

<style>
/* Scoped Switch Styling for Feature Hub */
.cora-switch {
    position: relative;
    display: inline-block;
    width: 38px;
    height: 22px;
    flex-shrink: 0;
    margin: 0;
    cursor: pointer;
    vertical-align: middle;
}
.cora-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
    margin: 0;
    padding: 0;
}
.cora-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #e4e4e7;
    transition: background-color 0.22s ease-in-out;
    border-radius: 9999px;
    box-sizing: border-box;
}
.cora-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: #ffffff;
    transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.18);
}
.cora-switch input:checked + .cora-slider {
    background-color: #09090b !important;
}
.cora-switch input:checked + .cora-slider:before {
    transform: translateX(16px);
}

/* Card & Button Styles */
.cora-feature-card:hover {
    border-color: #d4d4d8 !important;
    box-shadow: 0 4px 14px -3px rgba(0, 0, 0, 0.04);
}
.cora-btn-batch {
    background: #ffffff;
    color: #3f3f46;
    border: 1px solid #e4e4e7;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}
.cora-btn-batch:hover {
    background: #f4f4f5;
    color: #09090b;
    border-color: #d4d4d8;
}
.cora-btn-batch:active {
    transform: scale(0.96);
}
.cora-btn-primary {
    background: #09090b;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    padding: 8px 18px;
    border-radius: 10px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.15s ease-in-out;
}
.cora-btn-primary:hover {
    background: #27272a;
}
.cora-btn-primary:active {
    transform: scale(0.97);
}

@media (max-width: 640px) {
    .cora-fh-divider {
        display: none !important;
    }
}
</style>

<script>
(function($) {
    'use strict';

    // Store initial checked state to track modifications
    const getCheckedSlugs = function() {
        const checked = [];
        $('#cora-custom-features-form input[name="features[]"]:checked').each(function() {
            checked.push($(this).val());
        });
        return checked.sort();
    };

    let initialChecked = getCheckedSlugs();
    const totalCount = <?php echo intval( $total_modules_count ); ?>;

    const defaultSlugs = [
        'blogs', 'financials', 'team-roles', 'vault', 'calendar', 'activity-timeline', 'automations', 'inbox', 'analytics',
        'leads', 'crew_scheduler', 'equipment', 'tasks', 'plant_inventory',
        'canvas', 'forms', 'emails', 'review_acquisition', 'social-meta',
        'gbp', 'mcp', 'knowledge-base',
        'agency_setup', 'clients', 'proposals', 'contracts', 'client_portal', 'client_health', 'operating_economics', 'partner_hub', 'maintenance_care', 'enterprise_controls', 'monetisation', 'custom_workflow_builder',
        'media', 'properties'
    ];

    // Category Filter Pills Handler
    $('.cora-fh-cat-filter').on('click', function() {
        const selectedCat = $(this).attr('data-cat');
        $('.cora-fh-cat-filter').css({
            'background': '#ffffff',
            'color': '#52525b',
            'border-color': '#e4e4e7',
            'font-weight': '600'
        }).removeClass('active');
        $(this).css({
            'background': '#09090b',
            'color': '#ffffff',
            'border-color': '#09090b',
            'font-weight': '700'
        }).addClass('active');

        if (selectedCat === 'all') {
            $('.cora-fh-category-block').show();
        } else {
            $('.cora-fh-category-block').hide();
            $('.cora-fh-category-block[data-cat-slug="' + selectedCat + '"]').show();
        }
        $('#cora-fh-no-results').hide();
    });

    // Refresh UI elements (counter, badges, floating bar)
    const updateUIState = function() {
        const currentChecked = getCheckedSlugs();
        const activeCount = currentChecked.length;

        $('#cora-fh-active-count').text(activeCount);

        // Update card badges
        $('#cora-custom-features-form input[name="features[]"]').each(function() {
            const card = $(this).closest('.cora-feature-card');
            const badge = card.find('.cora-feature-badge');
            if ($(this).is(':checked')) {
                badge.show();
            } else {
                badge.hide();
            }
        });

        // Determine if dirty
        const isDirty = (initialChecked.join(',') !== currentChecked.join(','));
        if (isDirty) {
            $('#cora-fh-unsaved-pill').show();
            $('#cora-fh-floating-bar').css('display', 'flex');
        } else {
            $('#cora-fh-unsaved-pill').hide();
            $('#cora-fh-floating-bar').hide();
        }
    };

    // Smart Module Dependency Matrix
    const moduleDependencies = {
        'tasks': {
            name: 'Engagement Delivery',
            recommended: [
                { slug: 'vault', name: 'Deliverables & Approvals', reason: 'Managing versioned deliverables and client review links' },
                { slug: 'financials', name: 'Commercial Flow & Billing', reason: 'Linking milestone completions directly to billing draws' }
            ]
        },
        'client_portal': {
            name: 'Simple Client Portal',
            recommended: [
                { slug: 'tasks', name: 'Engagement Delivery', reason: 'Providing clients real-time visibility into active deliverables' },
                { slug: 'vault', name: 'Deliverables & Approvals', reason: 'Allowing 1-tap client approval of project files' }
            ]
        },
        'leads': {
            name: 'Leads, Discovery & Briefs',
            recommended: [
                { slug: 'forms', name: 'Forms Manager', reason: 'Capturing intake responses into pipeline cards' },
                { slug: 'canvas', name: 'Canvas Website Builder', reason: 'Delivering interactive SOW proposal pitches' }
            ]
        },
        'knowledge-base': {
            name: 'Reusable Knowledge & Cora AI',
            recommended: [
                { slug: 'mcp', name: 'AI Tools MCP', reason: 'Connecting vector RAG context to AI copilots' }
            ]
        }
    };

    let pendingRecommendedSlugs = [];

    window.checkModuleDependencies = function(slug, isChecked) {
        if (!isChecked || !moduleDependencies[slug]) {
            return;
        }

        const dep = moduleDependencies[slug];
        const currentChecked = getCheckedSlugs();
        const unactivated = dep.recommended.filter(r => currentChecked.indexOf(r.slug) === -1);

        if (unactivated.length === 0) {
            return;
        }

        pendingRecommendedSlugs = unactivated.map(r => r.slug);

        $('#cora-fh-rec-module-name').text(dep.name);
        let listHtml = '';
        unactivated.forEach(r => {
            listHtml += `
                <div style="padding: 10px 14px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 12px; display: flex; align-items: flex-start; gap: 10px;">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #22c55e; margin-top: 5px; flex-shrink: 0;"></span>
                    <div style="flex: 1;">
                        <div style="font-size: 12px; font-weight: 700; color: #18181b;">${r.name}</div>
                        <div style="font-size: 11px; color: #71717a; margin-top: 1px;">${r.reason}</div>
                    </div>
                </div>
            `;
        });
        $('#cora-fh-rec-list').html(listHtml);
        $('#cora-fh-rec-btn-text').text(`Activate All Recommended (${unactivated.length})`);

        // Open Bottom Slide-Up Sheet
        $('#cora-fh-recommendation-sheet').removeClass('pointer-events-none');
        $('#cora-fh-rec-backdrop').css('opacity', '1');
        $('#cora-fh-rec-drawer').css('transform', 'translateY(0)');
    };

    window.coraCloseRecSheet = function() {
        $('#cora-fh-rec-drawer').css('transform', 'translateY(100%)');
        $('#cora-fh-rec-backdrop').css('opacity', '0');
        setTimeout(() => $('#cora-fh-recommendation-sheet').addClass('pointer-events-none'), 300);
    };

    $('#cora-fh-rec-activate-btn').on('click', function() {
        pendingRecommendedSlugs.forEach(slug => {
            $(`#cora-custom-features-form input[name="features[]"][value="${slug}"]`).prop('checked', true);
        });
        coraCloseRecSheet();
        updateUIState();
    });

    // Checkbox change listener
    $('#cora-custom-features-form').on('change', 'input[name="features[]"]', function() {
        updateUIState();
    });

    // Batch Actions
    $('#cora-fh-select-all').on('click', function() {
        $('#cora-custom-features-form input[name="features[]"]').prop('checked', true);
        updateUIState();
    });

    $('#cora-fh-deselect-all').on('click', function() {
        $('#cora-custom-features-form input[name="features[]"]').prop('checked', false);
        updateUIState();
    });

    $('#cora-fh-reset-defaults').on('click', function() {
        $('#cora-custom-features-form input[name="features[]"]').each(function() {
            const val = $(this).val();
            $(this).prop('checked', defaultSlugs.indexOf(val) !== -1);
        });
        updateUIState();
    });

    $('#cora-fh-discard-btn').on('click', function() {
        $('#cora-custom-features-form input[name="features[]"]').each(function() {
            const val = $(this).val();
            $(this).prop('checked', initialChecked.indexOf(val) !== -1);
        });
        updateUIState();
    });

    // Search Filtering
    $('#cora-fh-search-input').on('input', function() {
        const query = $(this).val().toLowerCase().trim();
        let matchCount = 0;

        if (!query) {
            $('.cora-fh-category-block').show();
            $('.cora-feature-card').show();
            $('#cora-fh-no-results').hide();
            return;
        }

        $('.cora-fh-category-block').each(function() {
            let catMatches = 0;
            $(this).find('.cora-feature-card').each(function() {
                const title = $(this).find('.cora-feature-title').text().toLowerCase();
                const desc = $(this).find('.cora-feature-desc').text().toLowerCase();
                if (title.includes(query) || desc.includes(query)) {
                    $(this).show();
                    catMatches++;
                    matchCount++;
                } else {
                    $(this).hide();
                }
            });

            if (catMatches > 0) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        if (matchCount === 0) {
            $('#cora-fh-no-results').css('display', 'flex');
        } else {
            $('#cora-fh-no-results').hide();
        }
    });

    $('#cora-fh-clear-search-btn').on('click', function() {
        $('#cora-fh-search-input').val('').trigger('input');
    });

    // AJAX Save Workflow
    $('.cora-fh-save-btn').on('click', function() {
        const btn = $(this);
        const features = getCheckedSlugs();
        const origText = btn.find('.cora-save-text').text();

        btn.prop('disabled', true).find('.cora-save-text').text('Saving...');

        var ajaxUrl = (typeof coraData !== 'undefined' && coraData.ajax_url) ? coraData.ajax_url : ((typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php');
        var nonce = (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : ((typeof coraREData !== 'undefined' && coraREData.nonce) ? coraREData.nonce : '');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_save_custom_features',
                security: nonce,
                nonce: nonce,
                features: features
            },

            success: function(resp) {
                btn.prop('disabled', false).find('.cora-save-text').text(origText);
                if (resp && resp.success) {
                    initialChecked = getCheckedSlugs();
                    updateUIState();
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast('Modules updated successfully. Refreshing navigation...', 'success');
                    }
                    setTimeout(function() {
                        window.location.reload();
                    }, 600);
                } else {
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast((resp && resp.data && resp.data.message) ? resp.data.message : 'Error saving modules.', 'error');
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false).find('.cora-save-text').text(origText);
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast('Server connection error. Please try again.', 'error');
                }
            }
        });
    });

    // Initialize state
    updateUIState();

})(jQuery);
</script>
