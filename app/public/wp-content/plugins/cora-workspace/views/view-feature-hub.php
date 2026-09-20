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

// ── Complete Platform Modules Matrix (All 24 Active Modules) ──
$features_list = array(
    'Platform Core & Foundation' => array(
        'blogs' => array(
            'title' => 'Content Suite & CMS',
            'desc'  => 'Publish blogs, SEO articles, editorial stories, and marketing copy.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
        ),
        'financials' => array(
            'title' => 'Financials & Invoicing',
            'desc'  => 'Track workspace revenue, GST SAC 9983 invoices, and payment links.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
        ),
        'team-roles' => array(
            'title' => 'User & Role Governance',
            'desc'  => 'Configure team permissions, role hierarchies, and staff management.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
        ),
        'media' => array(
            'title' => 'Media Manager',
            'desc'  => 'Manage images, video assets, proofs, delivery galleries, and approvals.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>'
        ),
        'vault' => array(
            'title' => 'File & Document Vault',
            'desc'  => 'Secure encrypted file storage for client contracts, NDAs, and RAW files.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>'
        ),
        'calendar' => array(
            'title' => 'Consolidated Calendar',
            'desc'  => 'Unified calendar for client appointments, shoots, and team schedules.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
        ),
        'activity-timeline' => array(
            'title' => 'Activity Timeline & Audit',
            'desc'  => 'Chronological audit stream of workspace actions and operational logs.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
        ),
        'automations' => array(
            'title' => 'Automations & Workflows',
            'desc'  => 'Configure event triggers, status transitions, webhooks, and background loops.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>'
        ),
        'inbox' => array(
            'title' => 'Unified Inbox Hub',
            'desc'  => 'Communication hub integrating WhatsApp Cloud API, email threads, and queries.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>'
        ),
        'analytics' => array(
            'title' => 'Analytics & Telemetry',
            'desc'  => 'Executive graphs, revenue velocity, conversion funnels, and KPIs.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
        )
    ),
    'Operations & Fulfillment' => array(
        'leads' => array(
            'title' => 'Leads CRM Pipeline',
            'desc'  => 'Kanban CRM stages to capture, qualify, track, and convert client inquiries.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>'
        ),
        'crew_scheduler' => array(
            'title' => 'Team & Staff Scheduler',
            'desc'  => 'Manage team shifts, availability calendars, and project assignments.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
        ),
        'equipment' => array(
            'title' => 'Asset & Equipment',
            'desc'  => 'Track hardware gear, camera bodies, checkouts, and condition audits.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>'
        ),
        'properties' => array(
            'title' => 'Property Listings',
            'desc'  => 'Real estate inventory, geocoded maps, showings dispatch, and units.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'
        ),
        'tasks' => array(
            'title' => 'Client Task Manager',
            'desc'  => 'Collaborative task checklists, milestones, dependencies, and deliverables.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>'
        ),
        'plant_inventory' => array(
            'title' => 'Inventory & Van Sales',
            'desc'  => 'Stock management, dynamic van consignment allocations, and daily reconciliation.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'
        )
    ),
    'Sales Channels & Growth' => array(
        'canvas' => array(
            'title' => 'Canvas Site Builder',
            'desc'  => 'Visual landing page and interactive proposal builder with live previewing.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>'
        ),
        'forms' => array(
            'title' => 'Forms & Intake',
            'desc'  => 'Build customer intake forms, payment links, and embedded e-signatures.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 15l2 2 4-4"></path></svg>'
        ),
        'emails' => array(
            'title' => 'Emails & Broadcasts',
            'desc'  => 'Create and schedule SMTP email broadcasts, newsletters, and notifications.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>'
        ),
        'review_acquisition' => array(
            'title' => 'Reviews & Reputation',
            'desc'  => 'Automate Google Places review collection campaigns and feedback responses.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>'
        ),
        'social-meta' => array(
            'title' => 'Social Media & Ads',
            'desc'  => 'Preview scheduled social campaigns, feeds, grids, and ad metrics.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>'
        )
    ),
    'AI Intelligence & Marketing' => array(
        'gbp' => array(
            'title' => 'Google Business Profile',
            'desc'  => 'Sync Google Business Profile listings and automate local SEO citations.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" class="shrink-0" style="stroke: none !important; fill: none !important;"><circle cx="12" cy="12" r="11" fill="#ffffff" style="fill: #ffffff !important; stroke: #e4e4e7 !important; stroke-width: 0.8px !important;"></circle><g transform="matrix(0.55 0 0 0.55 5.4 5.4)"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" style="fill: #4285F4 !important; stroke: none !important;"></path><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" style="fill: #34A853 !important; stroke: none !important;"></path><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05" style="fill: #FBBC05 !important; stroke: none !important;"></path><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335" style="fill: #EA4335 !important; stroke: none !important;"></path></g></svg>'
        ),
        'mcp' => array(
            'title' => 'AI Tools MCP Gateway',
            'desc'  => 'Manage Model Context Protocol gateways and AI developer tools.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="15" x2="23" y2="15"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="15" x2="4" y2="15"></line></svg>'
        ),
        'knowledge-base' => array(
            'title' => 'RAG Knowledge Base',
            'desc'  => 'Upload PDFs & docs to vectorize semantic search context for AI copilots.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>'
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
        if ( in_array( $slug, $enabled, true ) || ( empty( $enabled ) && in_array( $slug, array( 'team-roles', 'tasks', 'vault', 'financials', 'activity-timeline', 'leads', 'analytics', 'knowledge-base', 'automations', 'blogs', 'canvas', 'forms', 'emails', 'crew_scheduler', 'review_acquisition', 'gbp', 'mcp', 'media', 'plant_inventory', 'calendar', 'equipment', 'properties', 'inbox', 'social-meta' ), true ) ) ) {
            $active_modules_count++;
        }
    }
}
?>

<div class="cora-fh-container" style="user-select: none; max-width: 1240px; margin: 0 auto; padding: 0 4px 96px; box-sizing: border-box;">
    <?php
    $modules_header_args = array(
        'title'              => 'App Modules & Feature Customizer',
        'mobile_title'       => 'App Modules',
        'description'        => 'Enable or disable modules to tailor your workspace. Changes adapt sidebar navigation, AI Agent context, and role permissions upon saving.',
        'mobile_description' => 'Customize workspace modules and AI context.',
        'icon'               => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect></svg>',
        'ai_stack'           => true,
        'tutorial_onclick'   => "window.open('https://www.youtube.com/@heycora', '_blank')",
    );

    if ( function_exists( 'cora_render_workspace_header' ) ) {
        cora_render_workspace_header( $modules_header_args );
    }
    ?>

    <!-- Compact Unified Action Toolbar -->
    <div class="cora-fh-control-bar" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 14px; padding: 10px 12px; margin-bottom: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 8px;">
        <!-- Row 1: Search & Save -->
        <div style="display: flex; align-items: center; gap: 8px; width: 100%;">
            <div style="position: relative; flex: 1; min-width: 0;">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #71717a; pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" id="cora-fh-search-input" placeholder="Search 24 modules..." style="width: 100%; height: 34px; padding: 0 28px 0 28px; font-size: 12px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 8px; color: #18181b; outline: none; transition: all 0.15s; box-sizing: border-box;" onfocus="this.style.borderColor='#18181b'; this.style.background='#ffffff';" onblur="this.style.borderColor='#e4e4e7'; this.style.background='#fafafa';" />
                <button type="button" id="cora-fh-clear-search-icon" style="display: none; position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 2px 4px; color: #a1a1aa; cursor: pointer; font-size: 11px; line-height: 1;">✕</button>
            </div>
            <button type="button" id="cora-fh-save-top-btn" class="cora-fh-save-btn cora-btn-primary" style="height: 34px; padding: 0 13px; font-size: 11.5px; font-weight: 700; border-radius: 8px; white-space: nowrap; flex-shrink: 0; display: inline-flex; align-items: center; gap: 5px;">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="cora-save-icon" style="flex-shrink: 0;">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span class="cora-save-text">Save</span>
            </button>
        </div>

        <!-- Row 2: Active Count & Micro Batch Controls -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span id="cora-fh-counter-badge" style="background: #f4f4f5; color: #18181b; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; border: 1px solid #e4e4e7; display: inline-flex; align-items: center; gap: 5px;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #22c55e; display: inline-block;"></span>
                    <span><span id="cora-fh-active-count"><?php echo intval( $active_modules_count ); ?></span> / <?php echo intval( $total_modules_count ); ?> Active</span>
                </span>
                <span id="cora-fh-unsaved-pill" style="display: none; background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 6px;">
                    Unsaved
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 4px;">
                <span style="font-size: 10.5px; color: #a1a1aa; margin-right: 2px;">Batch:</span>
                <button type="button" id="cora-fh-select-all" class="cora-btn-micro">All</button>
                <button type="button" id="cora-fh-deselect-all" class="cora-btn-micro">None</button>
                <button type="button" id="cora-fh-reset-defaults" class="cora-btn-micro">Defaults</button>
            </div>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="cora-fh-filter-bar" style="display: flex; align-items: center; gap: 5px; margin-bottom: 12px; overflow-x: auto; padding: 1px 0 5px; scrollbar-width: none; -webkit-overflow-scrolling: touch;">
        <button type="button" class="cora-fh-cat-filter active" data-cat="all">
            All (<?php echo intval( $total_modules_count ); ?>)
        </button>
        <?php foreach ( $features_list as $category => $items ) : 
            $cat_short_title = $category;
            if ( $category === 'Platform Core & Foundation' ) {
                $cat_short_title = 'Platform Core';
            } elseif ( $category === 'Operations & Fulfillment' ) {
                $cat_short_title = 'Operations';
            } elseif ( $category === 'Sales Channels & Growth' ) {
                $cat_short_title = 'Growth & Sales';
            } elseif ( $category === 'AI Intelligence & Marketing' ) {
                $cat_short_title = 'AI & RAG';
            }
        ?>
            <button type="button" class="cora-fh-cat-filter" data-cat="<?php echo esc_attr( sanitize_title( $category ) ); ?>">
                <?php echo esc_html( $cat_short_title ); ?> (<?php echo count( $items ); ?>)
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Modules 24-Grid Container -->
    <div class="cora-fh-main-box" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 16px; padding: 12px; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.02); box-sizing: border-box; width: 100%;">
        <form id="cora-custom-features-form" onsubmit="event.preventDefault();" style="display: flex; flex-direction: column; gap: 20px;">
            <!-- No Results Matching State -->
            <div id="cora-fh-no-results" style="display: none; padding: 36px 16px; text-align: center; flex-direction: column; align-items: center; justify-content: center; user-select: none;">
                <div style="width: 38px; height: 38px; border-radius: 50%; background: #f4f4f5; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px; color: #71717a;">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <div style="font-size: 12px; font-weight: 700; color: #18181b; margin-bottom: 3px;">No modules found</div>
                <p style="font-size: 11px; color: #71717a; margin: 0 0 10px; max-width: 240px; line-height: 1.3;">No matching modules found for your search.</p>
                <button type="button" id="cora-fh-clear-search-btn" class="cora-btn-micro" style="padding: 4px 10px;">Clear search</button>
            </div>

            <?php foreach ( $features_list as $category => $items ) : 
                $cat_slug = sanitize_title( $category );
            ?>
                <div class="cora-fh-category-block" data-cat-slug="<?php echo esc_attr( $cat_slug ); ?>" style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 4px; border-bottom: 1px solid #f4f4f5;">
                        <h3 style="font-size: 11px; font-weight: 800; color: #71717a; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                            <?php echo esc_html( $category ); ?>
                        </h3>
                        <span style="font-size: 10.5px; font-weight: 600; color: #a1a1aa;">
                            <?php echo count( $items ); ?> modules
                        </span>
                    </div>

                    <div class="cora-fh-grid">
                        <?php foreach ( $items as $slug => $data ) :
                            $is_active = in_array( $slug, $enabled, true ) || ( empty( $enabled ) && in_array( $slug, array( 'team-roles', 'tasks', 'vault', 'financials', 'activity-timeline', 'leads', 'analytics', 'knowledge-base', 'automations', 'blogs', 'canvas', 'forms', 'emails', 'crew_scheduler', 'review_acquisition', 'gbp', 'mcp', 'media', 'plant_inventory', 'calendar', 'equipment', 'properties', 'inbox', 'social-meta' ), true ) );
                        ?>
                            <div class="cora-feature-card <?php echo $is_active ? 'is-active' : 'is-inactive'; ?>" data-slug="<?php echo esc_attr( $slug ); ?>">
                                <!-- Card Header: Icon + Toggle -->
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; width: 100%;">
                                    <div class="cora-feature-icon-wrap" style="width: 28px; height: 28px; border-radius: 7px; background: <?php echo $is_active ? '#09090b' : '#f4f4f5'; ?>; color: <?php echo $is_active ? '#ffffff' : '#71717a'; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.18s ease;">
                                        <?php echo $data['icon']; ?>
                                    </div>
                                    <div style="flex-shrink: 0; display: flex; align-items: center;">
                                        <label class="cora-switch" onclick="event.stopPropagation();">
                                            <input type="checkbox" name="features[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $is_active ); ?> class="cora-feature-checkbox" onchange="checkModuleDependencies('<?php echo esc_js($slug); ?>', this.checked)">
                                            <span class="cora-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Card Body: Title, active dot, description -->
                                <div style="min-width: 0; flex: 1; display: flex; flex-direction: column; justify-content: flex-start; gap: 2px;">
                                    <div style="font-size: 11.5px; font-weight: 700; color: #09090b; display: flex; align-items: center; gap: 4px; line-height: 1.25;">
                                        <span class="cora-feature-title" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo esc_html( $data['title'] ); ?></span>
                                        <span class="cora-feature-badge" style="<?php echo $is_active ? 'display: inline-block;' : 'display: none;'; ?> width: 5px; height: 5px; border-radius: 50%; background: #22c55e; flex-shrink: 0;"></span>
                                    </div>
                                    <div class="cora-feature-desc" style="font-size: 10px; color: #71717a; line-height: 1.25; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?php echo esc_html( $data['desc'] ); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </div>

    <!-- Floating Bottom Unsaved Changes Bar -->
    <div id="cora-fh-floating-bar" style="display: none; position: fixed; bottom: 84px; left: 50%; transform: translateX(-50%); z-index: 999; background: rgba(9, 9, 11, 0.96); backdrop-filter: blur(8px); color: #ffffff; padding: 8px 14px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); border: 1px solid #27272a; align-items: center; gap: 12px; box-sizing: border-box; max-width: 90vw;">
        <div style="display: flex; align-items: center; gap: 6px;">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #fbbf24; display: inline-block; flex-shrink: 0;"></span>
            <span style="font-size: 11px; font-weight: 600; color: #f4f4f5; white-space: nowrap;">Unsaved changes</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px;">
            <button type="button" id="cora-fh-discard-btn" style="padding: 4px 9px; font-size: 11px; font-weight: 600; background: #27272a; color: #e4e4e7; border: 1px solid #3f3f46; border-radius: 6px; cursor: pointer; transition: all 0.15s;">
                Discard
            </button>
            <button type="button" id="cora-fh-save-bottom-btn" class="cora-fh-save-btn" style="padding: 5px 12px; font-size: 11px; font-weight: 700; background: #ffffff; color: #09090b; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.2); transition: all 0.15s; white-space: nowrap;">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="cora-save-icon">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span class="cora-save-text">Save</span>
            </button>
        </div>
    </div>
</div>

<!-- Recommended Companion Modules Sheet -->
<div id="cora-fh-recommendation-sheet" class="fixed inset-0 z-50 pointer-events-none transition-all duration-300">
    <div id="cora-fh-rec-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="coraCloseRecSheet()"></div>
    <div id="cora-fh-rec-drawer" class="absolute bottom-0 inset-x-0 bg-white rounded-t-3xl border-t border-zinc-200 shadow-2xl p-5 sm:p-6 max-w-lg mx-auto transform translate-y-full transition-transform duration-300 pointer-events-auto" style="box-shadow: 0 -10px 40px rgba(0,0,0,0.15);">
        <div class="w-10 h-1 rounded-full bg-zinc-300 mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-zinc-950 text-white flex items-center justify-center font-bold text-xs">
                    ⚡
                </div>
                <h3 class="text-sm font-bold text-zinc-950 tracking-tight">Recommended Companion Modules</h3>
            </div>
            <button type="button" onclick="coraCloseRecSheet()" class="text-zinc-400 hover:text-zinc-700 text-sm font-bold p-1">✕</button>
        </div>
        <p class="text-xs text-zinc-600 mb-3 leading-relaxed">
            Activating <strong id="cora-fh-rec-module-name" class="text-zinc-900">Module</strong> works best with these companion features:
        </p>
        <div id="cora-fh-rec-list" class="space-y-2 mb-4">
            <!-- Dynamic items rendered via JS -->
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="coraCloseRecSheet()" class="flex-1 py-2 rounded-lg border border-zinc-300 text-xs font-bold text-zinc-700 hover:bg-zinc-50 transition-all">
                Skip for now
            </button>
            <button type="button" id="cora-fh-rec-activate-btn" class="flex-1 py-2 rounded-lg bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all shadow-sm">
                <span id="cora-fh-rec-btn-text">Activate All</span>
            </button>
        </div>
    </div>
</div>

<style>
/* Feature Hub Grid Layout */
.cora-fh-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
}
@media (min-width: 640px) {
    .cora-fh-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
}
@media (min-width: 860px) {
    .cora-fh-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
}
@media (min-width: 1100px) {
    .cora-fh-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }
}

/* Feature Card Primitive */
.cora-feature-card {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    padding: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 6px;
    box-sizing: border-box;
    transition: all 0.16s ease-in-out;
    cursor: pointer;
    min-height: 98px;
}
.cora-feature-card:hover {
    border-color: #a1a1aa !important;
    box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.05);
}
.cora-feature-card.is-inactive {
    background: #fafafa !important;
    border-color: #e4e4e7 !important;
    opacity: 0.72;
}
.cora-feature-card.is-inactive:hover {
    opacity: 1;
    border-color: #d4d4d8 !important;
}

/* Micro Switch */
.cora-switch {
    position: relative;
    display: inline-block;
    width: 32px;
    height: 18px;
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
    transition: background-color 0.2s ease-in-out;
    border-radius: 9999px;
    box-sizing: border-box;
}
.cora-slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 2px;
    bottom: 2px;
    background-color: #ffffff;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    border-radius: 50%;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.18);
}
.cora-switch input:checked + .cora-slider {
    background-color: #09090b !important;
}
.cora-switch input:checked + .cora-slider:before {
    transform: translateX(14px);
}

/* Micro Batch Buttons */
.cora-btn-micro {
    background: #ffffff;
    color: #52525b;
    border: 1px solid #e4e4e7;
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    line-height: 1.3;
}
.cora-btn-micro:hover {
    background: #09090b;
    color: #ffffff;
    border-color: #09090b;
}
.cora-btn-micro:active {
    transform: scale(0.95);
}

/* Primary Button */
.cora-btn-primary {
    background: #09090b;
    color: #ffffff;
    font-size: 11.5px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
    transition: all 0.15s ease-in-out;
}
.cora-btn-primary:hover {
    background: #27272a;
}
.cora-btn-primary:active {
    transform: scale(0.97);
}

/* Category Filter Tabs */
.cora-fh-cat-filter {
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 9999px;
    background: #ffffff;
    color: #52525b;
    border: 1px solid #e4e4e7;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s;
    line-height: 1.3;
}
.cora-fh-cat-filter.active {
    background: #09090b !important;
    color: #ffffff !important;
    border-color: #09090b !important;
    font-weight: 700 !important;
}
.cora-fh-cat-filter:hover:not(.active) {
    background: #f4f4f5;
    color: #09090b;
    border-color: #d4d4d8;
}

@media (min-width: 640px) {
    #cora-fh-floating-bar {
        bottom: 24px !important;
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
        'blogs', 'financials', 'team-roles', 'media', 'vault', 'calendar', 'activity-timeline', 'automations', 'inbox', 'analytics',
        'leads', 'crew_scheduler', 'equipment', 'properties', 'tasks', 'plant_inventory',
        'canvas', 'forms', 'emails', 'review_acquisition', 'social-meta',
        'gbp', 'mcp', 'knowledge-base'
    ];

    // Card click toggles the switch seamlessly
    $('#cora-custom-features-form').on('click', '.cora-feature-card', function(e) {
        if ($(e.target).closest('.cora-switch').length) {
            return; // let native checkbox click handle it
        }
        const checkbox = $(this).find('input[name="features[]"]');
        const newState = !checkbox.prop('checked');
        checkbox.prop('checked', newState).trigger('change');
    });

    // Category Filter Handler
    $('.cora-fh-cat-filter').on('click', function() {
        const selectedCat = $(this).attr('data-cat');
        $('.cora-fh-cat-filter').removeClass('active');
        $(this).addClass('active');

        if (selectedCat === 'all') {
            $('.cora-fh-category-block').show();
        } else {
            $('.cora-fh-category-block').hide();
            $('.cora-fh-category-block[data-cat-slug="' + selectedCat + '"]').show();
        }
        $('#cora-fh-no-results').hide();
    });

    // Refresh UI elements
    const updateUIState = function() {
        const currentChecked = getCheckedSlugs();
        const activeCount = currentChecked.length;

        $('#cora-fh-active-count').text(activeCount);

        // Update card visual states
        $('#cora-custom-features-form input[name="features[]"]').each(function() {
            const card = $(this).closest('.cora-feature-card');
            const badge = card.find('.cora-feature-badge');
            const iconWrap = card.find('.cora-feature-icon-wrap');
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                card.removeClass('is-inactive').addClass('is-active');
                badge.show();
                iconWrap.css({ 'background': '#09090b', 'color': '#ffffff' });
            } else {
                card.removeClass('is-active').addClass('is-inactive');
                badge.hide();
                iconWrap.css({ 'background': '#f4f4f5', 'color': '#71717a' });
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

        if (query.length > 0) {
            $('#cora-fh-clear-search-icon').show();
        } else {
            $('#cora-fh-clear-search-icon').hide();
        }

        if (!query) {
            const activeCat = $('.cora-fh-cat-filter.active').attr('data-cat') || 'all';
            if (activeCat === 'all') {
                $('.cora-fh-category-block').show();
            } else {
                $('.cora-fh-category-block').hide();
                $('.cora-fh-category-block[data-cat-slug="' + activeCat + '"]').show();
            }
            $('.cora-feature-card').show();
            $('#cora-fh-no-results').hide();
            return;
        }

        $('.cora-fh-category-block').each(function() {
            let catMatches = 0;
            $(this).find('.cora-feature-card').each(function() {
                const title = $(this).find('.cora-feature-title').text().toLowerCase();
                const desc = $(this).find('.cora-feature-desc').text().toLowerCase();
                const slug = $(this).attr('data-slug') || '';
                if (title.includes(query) || desc.includes(query) || slug.includes(query)) {
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

    $('#cora-fh-clear-search-icon, #cora-fh-clear-search-btn').on('click', function() {
        $('#cora-fh-search-input').val('').trigger('input');
    });

    // Smart Module Dependency Matrix
    const moduleDependencies = {
        'tasks': {
            name: 'Client Task Manager',
            recommended: [
                { slug: 'vault', name: 'File & Document Vault', reason: 'Managing versioned deliverables and client review links' },
                { slug: 'financials', name: 'Financials & Invoicing', reason: 'Linking milestone completions directly to billing draws' }
            ]
        },
        'leads': {
            name: 'Leads CRM Pipeline',
            recommended: [
                { slug: 'forms', name: 'Forms & Intake', reason: 'Capturing intake responses into pipeline cards' },
                { slug: 'canvas', name: 'Canvas Site Builder', reason: 'Delivering interactive SOW proposal pitches' }
            ]
        },
        'knowledge-base': {
            name: 'RAG Knowledge Base',
            recommended: [
                { slug: 'mcp', name: 'AI Tools MCP Gateway', reason: 'Connecting vector RAG context to AI copilots' }
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
                <div style="padding: 8px 12px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 10px; display: flex; align-items: flex-start; gap: 8px;">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #22c55e; margin-top: 5px; flex-shrink: 0;"></span>
                    <div style="flex: 1;">
                        <div style="font-size: 11.5px; font-weight: 700; color: #18181b;">${r.name}</div>
                        <div style="font-size: 10.5px; color: #71717a; margin-top: 1px;">${r.reason}</div>
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
