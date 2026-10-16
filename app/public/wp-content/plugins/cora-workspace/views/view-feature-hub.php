<?php
/**
 * View: App Modules & Extensibility Engine (Plugins, Modules, Skills)
 *
 * Foundation-level vertical architecture unifying:
 * 1. Plugins — Workspace-level dynamic plugins, webhook connectors & MCP tool bridges
 * 2. Modules — Core platform & industry foundation module toggles (24 customizable modules)
 * 3. Skills  — Custom AI Agent skills engine with behavioral system prompts & bound MCP tools
 *
 * Open for external REST APIs & Model Context Protocol (MCP) 2.0.
 * Follows the precise Notion/Shopify monochromatic design system specification (SOP 15).
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$agency_id = function_exists( 'cora_db_get_agency_id' ) ? ( cora_db_get_agency_id() ?: '1' ) : '1';

if ( class_exists( 'Cora_Plugins_Skills_Engine' ) ) {
    $workspace_plugins = Cora_Plugins_Skills_Engine::get_workspace_plugins( $agency_id );
    $workspace_skills  = Cora_Plugins_Skills_Engine::get_workspace_skills( $agency_id );
    $workspace_keys    = Cora_Plugins_Skills_Engine::get_api_keys( $agency_id );
} else {
    $workspace_plugins = array();
    $workspace_skills  = array();
    $workspace_keys    = array();
}

$cora_industry = ! empty( $_GET['industry'] )
    ? sanitize_text_field( $_GET['industry'] )
    : ( function_exists( 'cora_get_active_industry' ) ? cora_get_active_industry() : 'real_estate' );
$is_studio = ( strpos( strtolower( $cora_industry ), 'photo' ) !== false || strpos( strtolower( $cora_industry ), 'studio' ) !== false );
$enabled = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();

// ── Complete Platform Modules Matrix (All 24 Active Modules) ──
$foundation_slugs = array( 'blogs', 'forms', 'team-roles', 'media', 'vault' );

$features_list = array(
    'Platform Core & Foundation' => array(
        'blogs' => array(
            'title'      => 'Content Suite & CMS',
            'desc'       => 'Publish blogs, SEO articles, editorial stories, and marketing copy.',
            'icon'       => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>',
            'foundation' => true,
        ),
        'forms' => array(
            'title'      => 'Forms & Intake',
            'desc'       => 'Build customer intake forms, payment links, and embedded e-signatures.',
            'icon'       => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 15l2 2 4-4"></path></svg>',
            'foundation' => true,
        ),
        'team-roles' => array(
            'title'      => 'User & Role Governance',
            'desc'       => 'Configure team permissions, role hierarchies, and staff management.',
            'icon'       => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
            'foundation' => true,
        ),
        'media' => array(
            'title'      => 'Media Manager',
            'desc'       => 'Manage images, video assets, proofs, delivery galleries, and approvals.',
            'icon'       => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',
            'foundation' => true,
        ),
        'vault' => array(
            'title'      => 'File & Document Vault',
            'desc'       => 'Secure encrypted file storage for client contracts, NDAs, and RAW files.',
            'icon'       => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>',
            'foundation' => true,
        ),
        'financials' => array(
            'title' => 'Financials & Invoicing',
            'desc'  => 'Track workspace revenue, GST SAC 9983 invoices, and payment links.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
        ),
        'calendar' => array(
            'title' => 'Consolidated Calendar',
            'desc'  => 'Unified calendar for client appointments, shoots, and team schedules.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
        ),
        'activity-timeline' => array(
            'title' => 'Activity Timeline & Audit',
            'desc'  => 'Chronological audit stream of workspace actions and operational logs.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
        ),
        'automations' => array(
            'title' => 'Automations & Workflows',
            'desc'  => 'Configure event triggers, status transitions, webhooks, and background loops.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>'
        ),
        'inbox' => array(
            'title' => 'Unified Inbox Hub',
            'desc'  => 'Communication hub integrating WhatsApp Cloud API, email threads, and queries.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>'
        ),
        'analytics' => array(
            'title' => 'Analytics & Telemetry',
            'desc'  => 'Executive graphs, revenue velocity, conversion funnels, and KPIs.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
        )
    ),
    'Operations & Fulfillment' => array(
        'leads' => array(
            'title' => 'Leads CRM Pipeline',
            'desc'  => 'Kanban CRM stages to capture, qualify, track, and convert client inquiries.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>'
        ),
        'crew_scheduler' => array(
            'title' => 'Team & Staff Scheduler',
            'desc'  => 'Manage team shifts, availability calendars, and project assignments.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
        ),
        'equipment' => array(
            'title' => 'Asset & Equipment',
            'desc'  => 'Track hardware gear, camera bodies, checkouts, and condition audits.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>'
        ),
        'properties' => array(
            'title' => 'Property Listings',
            'desc'  => 'Real estate inventory, geocoded maps, showings dispatch, and units.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'
        ),
        'tasks' => array(
            'title' => 'Client Task Manager',
            'desc'  => 'Collaborative task checklists, milestones, dependencies, and deliverables.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>'
        ),
        'plant_inventory' => array(
            'title' => 'Inventory & Van Sales',
            'desc'  => 'Stock management, dynamic van consignment allocations, and daily reconciliation.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline></svg>'
        )
    ),
    'Sales Channels & Growth' => array(
        'canvas' => array(
            'title' => 'Canvas Site Builder',
            'desc'  => 'Visual landing page and interactive proposal builder with live previewing.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>'
        ),
        'emails' => array(
            'title' => 'Emails & Broadcasts',
            'desc'  => 'Create and schedule SMTP email broadcasts, newsletters, and notifications.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>'
        ),
        'review_acquisition' => array(
            'title' => 'Reviews & Reputation',
            'desc'  => 'Automate Google Places review collection campaigns and feedback responses.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>'
        ),
        'projects' => array(
            'title' => 'Portfolio',
            'desc'  => 'Curate agency portfolio transformation stories, proof KPIs, and publish directly to the frontend.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="14" height="14" rx="2"></rect><path d="M7 3h12a2 2 0 0 1 2 2v12"></path></svg>'
        ),
        'social-meta' => array(
            'title' => 'Social Media & Ads',
            'desc'  => 'Preview scheduled social campaigns, feeds, grids, and ad metrics.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="2" width="20" height="20" rx="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path></svg>'
        )
    ),
    'AI Intelligence & Marketing' => array(
        'gbp' => array(
            'title' => 'Google Business Profile',
            'desc'  => 'Sync Google Business Profile listings and automate local SEO citations.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v8"></path><path d="M8 12h8"></path></svg>'
        ),
        'mcp' => array(
            'title' => 'AI Tools MCP Gateway',
            'desc'  => 'Manage Model Context Protocol gateways and AI developer tools.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line></svg>'
        ),
        'knowledge-base' => array(
            'title' => 'RAG Knowledge Base',
            'desc'  => 'Upload PDFs & docs to vectorize semantic search context for AI copilots.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>'
        )
    )
);

// Counters
$total_modules_count = 0;
foreach ( $features_list as $cat => $items ) {
    $total_modules_count += count( $items );
}
$active_modules_count = 0;
foreach ( $features_list as $cat => $items ) {
    foreach ( $items as $slug => $data ) {
        $is_found = ! empty( $data['foundation'] ) || in_array( $slug, $foundation_slugs, true );
        if ( $is_found || in_array( $slug, $enabled, true ) || ( empty( $enabled ) && in_array( $slug, array( 'team-roles', 'tasks', 'vault', 'financials', 'activity-timeline', 'leads', 'analytics', 'knowledge-base', 'automations', 'blogs', 'canvas', 'forms', 'emails', 'crew_scheduler', 'review_acquisition', 'gbp', 'mcp', 'media', 'plant_inventory', 'calendar', 'equipment', 'properties', 'inbox', 'social-meta', 'projects' ), true ) ) ) {
            $active_modules_count++;
        }
    }
}

$active_plugins_count = count( array_filter( $workspace_plugins, function($p) { return $p['status'] === 'active'; } ) );
$total_plugins_count  = count( $workspace_plugins );
$active_skills_count  = count( array_filter( $workspace_skills, function($s) { return $s['status'] === 'active'; } ) );
$total_skills_count   = count( $workspace_skills );

$initial_tab = ! empty( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'plugins';
if ( ! in_array( $initial_tab, array( 'plugins', 'modules', 'skills' ), true ) ) {
    $initial_tab = 'plugins';
}
?>

<div class="cora-fh-container" style="max-width: 1240px; margin: 0 auto; padding: 0 4px 96px; box-sizing: border-box;">
    <?php
    $modules_header_args = array(
        'title'              => 'App Modules & Extensibility Engine',
        'mobile_title'       => 'App Modules',
        'description'        => 'Dynamic workspace plugins, core modular features, and custom AI skills. Open architecture for REST APIs & Model Context Protocol (MCP) 2.0.',
        'mobile_description' => 'Plugins, Modules & AI Skills Engine.',
        'icon'               => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>',
        'ai_stack'           => true,
        'tutorial_onclick'   => "window.open('https://www.youtube.com/@heycora', '_blank')",
    );

    if ( function_exists( 'cora_render_workspace_header' ) ) {
        cora_render_workspace_header( $modules_header_args );
    }
    ?>

    <!-- ── Level 01 Segmented Control: Plugins, Modules, Skills ──────────────── -->
    <div style="margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <div class="cora-level-01-tabs" style="display: inline-flex; align-items: center; padding: 4px; background: #f4f4f5; border: 1px solid #e4e4e7; border-radius: 12px; gap: 3px;">
            <button type="button" class="cora-main-tab-btn <?php echo $initial_tab === 'plugins' ? 'active' : ''; ?>" data-target="plugins" style="padding: 6px 14px; font-size: 12px; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; transition: all 0.18s ease; display: inline-flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                <span>Plugins</span>
                <span class="cora-tab-count" style="background: rgba(0,0,0,0.06); padding: 1px 6px; border-radius: 6px; font-size: 10.5px;"><?php echo intval( $active_plugins_count ); ?>/<?php echo intval( $total_plugins_count ); ?></span>
            </button>
            <button type="button" class="cora-main-tab-btn <?php echo $initial_tab === 'modules' ? 'active' : ''; ?>" data-target="modules" style="padding: 6px 14px; font-size: 12px; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; transition: all 0.18s ease; display: inline-flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Modules</span>
                <span class="cora-tab-count" style="background: rgba(0,0,0,0.06); padding: 1px 6px; border-radius: 6px; font-size: 10.5px;"><?php echo intval( $active_modules_count ); ?>/<?php echo intval( $total_modules_count ); ?></span>
            </button>
            <button type="button" class="cora-main-tab-btn <?php echo $initial_tab === 'skills' ? 'active' : ''; ?>" data-target="skills" style="padding: 6px 14px; font-size: 12px; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; transition: all 0.18s ease; display: inline-flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <span>Skills</span>
                <span class="cora-tab-count" style="background: rgba(0,0,0,0.06); padding: 1px 6px; border-radius: 6px; font-size: 10.5px;"><?php echo intval( $active_skills_count ); ?>/<?php echo intval( $total_skills_count ); ?></span>
            </button>
        </div>

        <!-- Global Action Group: API Keys & MCP Protocol Specs -->
        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="button" id="cora-open-api-keys-btn" class="cora-btn-secondary" style="height: 34px; padding: 0 12px; font-size: 11.5px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; background: #ffffff; border: 1px solid #e4e4e7; color: #18181b; cursor: pointer;">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 2l-2 2m-2-2l2 2m-2 2l2-2m-7 7l7-7-3-3-7 7v3h3z"></path><circle cx="7.5" cy="16.5" r="4.5"></circle></svg>
                <span>API & MCP Gateway</span>
            </button>
            <a href="<?php echo esc_url( site_url( '/wp-json/cora/v1/workspace/openapi' ) ); ?>" target="_blank" class="cora-btn-secondary" style="height: 34px; padding: 0 10px; font-size: 11px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px; background: #fafafa; border: 1px solid #e4e4e7; color: #71717a; text-decoration: none;">
                <span>OpenAPI 3.0</span>
                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            </a>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════
         TAB 1: WORKSPACE PLUGINS
         ══════════════════════════════════════════════════════════════════════════ -->
    <div id="cora-fh-tab-plugins" class="cora-main-tab-content <?php echo $initial_tab === 'plugins' ? 'active' : ''; ?>" style="display: <?php echo $initial_tab === 'plugins' ? 'block' : 'none'; ?>;">
        <!-- Control Bar: Search & Action Buttons -->
        <div class="cora-fh-control-bar" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 14px; padding: 10px 12px; margin-bottom: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 8px; width: 100%; flex-wrap: wrap;">
                <div style="position: relative; flex: 1; min-width: 200px;">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #71717a; pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="cora-plugins-search-input" placeholder="Search <?php echo intval( $total_plugins_count ); ?> workspace plugins..." style="width: 100%; height: 34px; padding: 0 12px 0 28px; font-size: 12px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 8px; color: #18181b; outline: none; box-sizing: border-box;" />
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <button type="button" id="cora-open-plugin-upload-btn" class="cora-btn-secondary" style="height: 34px; padding: 0 12px; font-size: 11.5px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; background: #ffffff; border: 1px solid #e4e4e7; color: #18181b; cursor: pointer;">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <span>Upload Plugin</span>
                    </button>
                    <button type="button" id="cora-open-plugin-ai-gen-btn" class="cora-btn-primary" style="height: 34px; padding: 0 12px; font-size: 11.5px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; background: #09090b; color: #ffffff; border: none; cursor: pointer;">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                        <span>Generate with AI</span>
                    </button>
                    <button type="button" id="cora-open-plugin-builder-btn" class="cora-btn-secondary" style="height: 34px; padding: 0 12px; font-size: 11.5px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; background: #ffffff; border: 1px solid #e4e4e7; color: #18181b; cursor: pointer;">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Custom Plugin</span>
                    </button>
                </div>
            </div>

            <!-- Plugin Metric Pills -->
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px; padding-top: 4px; border-top: 1px solid #f4f4f5;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="background: #f4f4f5; color: #18181b; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; border: 1px solid #e4e4e7; display: inline-flex; align-items: center; gap: 5px;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #22c55e;"></span>
                        <span><?php echo intval( $active_plugins_count ); ?> Active</span>
                    </span>
                    <span style="font-size: 11px; color: #71717a;"><?php echo intval( $total_plugins_count ); ?> Total Installed</span>
                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #18181b; background: #fafafa; border: 1px solid #e4e4e7; padding: 2px 7px; border-radius: 6px;">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #22c55e;"></span>
                        Workspace Level Only
                    </span>
                </div>
                <div class="cora-plugin-cat-pills" style="display: flex; align-items: center; gap: 4px;">
                    <button type="button" class="cora-filter-pill active" data-filter="all">All</button>
                    <button type="button" class="cora-filter-pill" data-filter="Integrations">Integrations</button>
                    <button type="button" class="cora-filter-pill" data-filter="Financials">Financials</button>
                    <button type="button" class="cora-filter-pill" data-filter="Operations">Operations</button>
                    <button type="button" class="cora-filter-pill" data-filter="Productivity">Productivity</button>
                </div>
            </div>
        </div>

        <!-- Plugins Grid Container -->
        <div class="cora-plugins-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 12px;">
            <?php foreach ( $workspace_plugins as $plugin ) :
                $is_active = ( $plugin['status'] === 'active' );
                $tools_arr = json_decode( $plugin['mcp_tools_json'], true ) ?: array();
                $routes_arr = json_decode( $plugin['api_routes_json'], true ) ?: array();
            ?>
                <div class="cora-plugin-card <?php echo $is_active ? 'is-active' : 'is-inactive'; ?>" data-category="<?php echo esc_attr( $plugin['category'] ); ?>" data-slug="<?php echo esc_attr( $plugin['plugin_slug'] ); ?>" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 12px; padding: 14px; display: flex; flex-direction: column; justify-content: space-between; gap: 12px; transition: all 0.15s ease;">
                    <div>
                        <!-- Header Row: Icon + Title + Toggle -->
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: <?php echo $is_active ? '#09090b' : '#f4f4f5'; ?>; color: <?php echo $is_active ? '#ffffff' : '#71717a'; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <?php echo ! empty( $plugin['icon_svg'] ) ? $plugin['icon_svg'] : '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>'; ?>
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-size: 13px; font-weight: 700; color: #09090b; display: flex; align-items: center; gap: 6px; line-height: 1.25;">
                                        <span class="cora-plugin-name" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo esc_html( $plugin['name'] ); ?></span>
                                        <span style="font-size: 10px; font-weight: 600; color: #a1a1aa; background: #fafafa; border: 1px solid #e4e4e7; padding: 1px 4px; border-radius: 4px;">v<?php echo esc_html( $plugin['version'] ); ?></span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 3px; flex-wrap: wrap;">
                                        <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #71717a; background: #f4f4f5; padding: 1px 6px; border-radius: 4px;"><?php echo esc_html( $plugin['category'] ); ?></span>
                                        <?php if ( ( $plugin['type'] ?? '' ) === 'uploaded' || ( $plugin['created_by_model'] ?? '' ) === 'workspace_upload' ) : ?>
                                            <span style="font-size: 9.5px; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #18181b; background: #fafafa; border: 1px solid #e4e4e7; padding: 1px 5px; border-radius: 4px;">Uploaded by Workspace</span>
                                        <?php elseif ( ( $plugin['created_by_model'] ?? '' ) === 'system_seed' || ( $plugin['type'] ?? '' ) === 'built_in' ) : ?>
                                            <span style="font-size: 9.5px; font-family: 'JetBrains Mono', monospace; color: #71717a; background: #f4f4f5; padding: 1px 5px; border-radius: 4px;">Workspace Starter</span>
                                        <?php else : ?>
                                            <span style="font-size: 9.5px; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #18181b; background: #fafafa; border: 1px solid #e4e4e7; padding: 1px 5px; border-radius: 4px;">Created by Workspace</span>
                                        <?php endif; ?>
                                        <span style="font-size: 10.5px; color: #a1a1aa;">by <?php echo esc_html( $plugin['author'] ); ?></span>
                                    </div>
                                </div>
                            </div>

                            <label class="cora-switch" style="flex-shrink: 0; margin-left: 6px;" title="Toggle plugin status">
                                <input type="checkbox" class="cora-plugin-toggle" data-slug="<?php echo esc_attr( $plugin['plugin_slug'] ); ?>" <?php checked( $is_active ); ?>>
                                <span class="cora-slider"></span>
                            </label>
                        </div>

                        <!-- Description -->
                        <p class="cora-plugin-desc" style="font-size: 11.5px; color: #71717a; line-height: 1.4; margin: 0 0 10px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo esc_html( $plugin['description'] ); ?>
                        </p>

                        <!-- Technical Specs -->
                        <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                            <span style="font-size: 10px; font-weight: 600; color: #18181b; background: #f4f4f5; padding: 2px 6px; border-radius: 4px; border: 1px solid #e4e4e7;">
                                <?php echo count( $tools_arr ); ?> MCP Tool<?php echo count( $tools_arr ) === 1 ? '' : 's'; ?>
                            </span>
                            <?php if ( ! empty( $routes_arr ) ) : ?>
                                <span style="font-size: 10px; font-weight: 600; color: #71717a; background: #fafafa; padding: 2px 6px; border-radius: 4px; border: 1px solid #e4e4e7;">
                                    <?php echo count( $routes_arr ); ?> API Endpoint<?php echo count( $routes_arr ) === 1 ? '' : 's'; ?>
                                </span>
                            <?php endif; ?>
                            <span style="font-size: 10px; font-weight: 600; color: #71717a; background: #fafafa; padding: 2px 6px; border-radius: 4px; border: 1px solid #e4e4e7;">
                                Type: <?php echo esc_html( ucfirst( $plugin['type'] ) ); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 10px; border-top: 1px solid #f4f4f5; margin-top: 6px;">
                        <button type="button" class="cora-configure-plugin-btn cora-btn-micro" data-id="<?php echo esc_attr( $plugin['id'] ); ?>" data-slug="<?php echo esc_attr( $plugin['plugin_slug'] ); ?>" style="padding: 4px 8px; font-size: 10.5px; font-weight: 600; background: #f4f4f5; border: 1px solid #e4e4e7; border-radius: 6px; cursor: pointer;">
                            Configure
                        </button>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <?php if ( $plugin['type'] !== 'built_in' && $plugin['created_by_model'] !== 'system_seed' ) : ?>
                                <button type="button" class="cora-delete-plugin-btn" data-slug="<?php echo esc_attr( $plugin['plugin_slug'] ); ?>" style="background: none; border: none; font-size: 10.5px; color: #ef4444; cursor: pointer; padding: 3px 6px; border-radius: 4px;">
                                    Delete
                                </button>
                            <?php else : ?>
                                <span style="font-size: 10px; color: #a1a1aa;">System Core</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════
         TAB 2: CORE MODULES MATRIX (24 Modular Platform Features)
         ══════════════════════════════════════════════════════════════════════════ -->
    <div id="cora-fh-tab-modules" class="cora-main-tab-content <?php echo $initial_tab === 'modules' ? 'active' : ''; ?>" style="display: <?php echo $initial_tab === 'modules' ? 'block' : 'none'; ?>;">
        <!-- Platform Admin Governance Notice Banner -->
        <div style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 14px; padding: 12px 14px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; gap: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #09090b; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                </div>
                <div style="min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span style="font-size: 13px; font-weight: 700; color: #09090b;">Core Platform Modules</span>
                        <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 600; color: #71717a; background: #f4f4f5; border: 1px solid #e4e4e7; padding: 2px 7px; border-radius: 6px; font-family: 'JetBrains Mono', monospace;">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            Published by Platform Admin Only
                        </span>
                    </div>
                    <p style="font-size: 11px; color: #71717a; margin: 2px 0 0; line-height: 1.4;">
                        Core platform modules are engineered and published exclusively by Cora Platform Admin. Active modules below can be toggled to configure operations for this workspace.
                    </p>
                </div>
            </div>
            <div style="font-size: 10.5px; font-family: 'JetBrains Mono', monospace; color: #a1a1aa; flex-shrink: 0;">
                Workspace Config Scope
            </div>
        </div>

        <!-- Compact Unified Action Toolbar -->
        <div class="cora-fh-control-bar" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 14px; padding: 10px 12px; margin-bottom: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 8px;">
            <!-- Row 1: Search & Save -->
            <div style="display: flex; align-items: center; gap: 8px; width: 100%;">
                <div style="position: relative; flex: 1; min-width: 0;">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #71717a; pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="cora-fh-search-input" placeholder="Search <?php echo intval( $total_modules_count ); ?> modules..." style="width: 100%; height: 34px; padding: 0 28px 0 28px; font-size: 12px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 8px; color: #18181b; outline: none; transition: all 0.15s; box-sizing: border-box;" onfocus="this.style.borderColor='#18181b'; this.style.background='#ffffff';" onblur="this.style.borderColor='#e4e4e7'; this.style.background='#fafafa';" />
                    <button type="button" id="cora-fh-clear-search-icon" style="display: none; position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 2px 4px; color: #a1a1aa; cursor: pointer; font-size: 11px; line-height: 1;">✕</button>
                </div>
                <button type="button" id="cora-fh-save-top-btn" class="cora-fh-save-btn cora-btn-primary" style="height: 34px; padding: 0 13px; font-size: 11.5px; font-weight: 700; border-radius: 8px; white-space: nowrap; flex-shrink: 0; display: inline-flex; align-items: center; gap: 5px; background: #09090b; color: #ffffff; border: none; cursor: pointer;">
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
                                $is_foundation = ! empty( $data['foundation'] ) || in_array( $slug, $foundation_slugs, true );
                                $is_active = $is_foundation || in_array( $slug, $enabled, true ) || ( empty( $enabled ) && in_array( $slug, array( 'team-roles', 'tasks', 'vault', 'financials', 'activity-timeline', 'leads', 'analytics', 'knowledge-base', 'automations', 'blogs', 'canvas', 'forms', 'emails', 'crew_scheduler', 'review_acquisition', 'gbp', 'mcp', 'media', 'plant_inventory', 'calendar', 'equipment', 'properties', 'inbox', 'social-meta' ), true ) );
                            ?>
                                <div class="cora-feature-card <?php echo $is_active ? 'is-active' : 'is-inactive'; ?> <?php echo $is_foundation ? 'is-foundation' : ''; ?>" data-slug="<?php echo esc_attr( $slug ); ?>" <?php if ( $is_foundation ) : ?>style="cursor: default;" title="Foundation Module: Permanently active across your workspace"<?php endif; ?>>
                                    <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                                        <div class="cora-feature-icon-wrap" style="width: 32px; height: 32px; border-radius: 8px; background: <?php echo $is_active ? '#09090b' : '#f4f4f5'; ?>; color: <?php echo $is_active ? '#ffffff' : '#71717a'; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.18s ease;">
                                            <?php echo $data['icon']; ?>
                                        </div>
                                        <div style="min-width: 0; flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 2px;">
                                            <div style="font-size: 12px; font-weight: 700; color: #09090b; display: flex; align-items: center; gap: 5px; line-height: 1.25; flex-wrap: wrap;">
                                                <span class="cora-feature-title" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo esc_html( $data['title'] ); ?></span>
                                                <?php if ( $is_foundation ) : ?>
                                                    <span class="cora-foundation-badge" style="display: inline-flex; align-items: center; gap: 3px; font-size: 9px; font-weight: 700; color: #71717a; background: #f4f4f5; border: 1px solid #e4e4e7; padding: 1px 5px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.04em;">
                                                        <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                        Foundation
                                                    </span>
                                                <?php else : ?>
                                                    <span class="cora-platform-pub-badge" style="display: inline-flex; align-items: center; gap: 3px; font-size: 9px; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #71717a; background: #fafafa; border: 1px solid #e4e4e7; padding: 1px 5px; border-radius: 4px;">
                                                        Platform Published
                                                    </span>
                                                    <span class="cora-feature-badge" style="<?php echo $is_active ? 'display: inline-block;' : 'display: none;'; ?> width: 6px; height: 6px; border-radius: 50%; background: #22c55e; flex-shrink: 0;"></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="cora-feature-desc" style="font-size: 10.5px; color: #71717a; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                <?php echo esc_html( $data['desc'] ); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="flex-shrink: 0; display: flex; align-items: center; margin-left: 8px;">
                                        <?php if ( $is_foundation ) : ?>
                                            <label class="cora-switch" style="cursor: not-allowed; opacity: 0.75;" title="Foundation module - Always active" onclick="event.stopPropagation();">
                                                <input type="checkbox" checked disabled class="cora-feature-checkbox is-foundation-cb">
                                                <input type="hidden" name="features[]" value="<?php echo esc_attr( $slug ); ?>">
                                                <span class="cora-slider" style="background: #09090b !important;"></span>
                                            </label>
                                        <?php else : ?>
                                            <label class="cora-switch" onclick="event.stopPropagation();">
                                                <input type="checkbox" name="features[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $is_active ); ?> class="cora-feature-checkbox" onchange="checkModuleDependencies('<?php echo esc_js($slug); ?>', this.checked)">
                                                <span class="cora-slider"></span>
                                            </label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </form>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════
         TAB 3: AI SKILLS ENGINE
         ══════════════════════════════════════════════════════════════════════════ -->
    <div id="cora-fh-tab-skills" class="cora-main-tab-content <?php echo $initial_tab === 'skills' ? 'active' : ''; ?>" style="display: <?php echo $initial_tab === 'skills' ? 'block' : 'none'; ?>;">
        <!-- Control Bar: Search & Action Buttons -->
        <div class="cora-fh-control-bar" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 14px; padding: 10px 12px; margin-bottom: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 8px; width: 100%; flex-wrap: wrap;">
                <div style="position: relative; flex: 1; min-width: 200px;">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #71717a; pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="cora-skills-search-input" placeholder="Search <?php echo intval( $total_skills_count ); ?> AI agent skills..." style="width: 100%; height: 34px; padding: 0 12px 0 28px; font-size: 12px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 8px; color: #18181b; outline: none; box-sizing: border-box;" />
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <button type="button" id="cora-open-skill-upload-btn" class="cora-btn-secondary" style="height: 34px; padding: 0 12px; font-size: 11.5px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; background: #ffffff; border: 1px solid #e4e4e7; color: #18181b; cursor: pointer;">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <span>Upload Skill</span>
                    </button>
                    <button type="button" id="cora-open-skill-ai-gen-btn" class="cora-btn-primary" style="height: 34px; padding: 0 12px; font-size: 11.5px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; background: #09090b; color: #ffffff; border: none; cursor: pointer;">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span>Generate Skill with AI</span>
                    </button>
                    <button type="button" id="cora-open-skill-builder-btn" class="cora-btn-secondary" style="height: 34px; padding: 0 12px; font-size: 11.5px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; background: #ffffff; border: 1px solid #e4e4e7; color: #18181b; cursor: pointer;">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Custom Skill</span>
                    </button>
                </div>
            </div>

            <!-- Skills Metric Pills -->
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px; padding-top: 4px; border-top: 1px solid #f4f4f5;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="background: #f4f4f5; color: #18181b; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; border: 1px solid #e4e4e7; display: inline-flex; align-items: center; gap: 5px;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #22c55e;"></span>
                        <span><?php echo intval( $active_skills_count ); ?> Active</span>
                    </span>
                    <span style="font-size: 11px; color: #71717a;"><?php echo intval( $total_skills_count ); ?> Total Registered</span>
                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #18181b; background: #fafafa; border: 1px solid #e4e4e7; padding: 2px 7px; border-radius: 6px;">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #22c55e;"></span>
                        Workspace Level Only
                    </span>
                </div>
                <div class="cora-skill-cat-pills" style="display: flex; align-items: center; gap: 4px;">
                    <button type="button" class="cora-filter-pill active" data-filter="all">All</button>
                    <button type="button" class="cora-filter-pill" data-filter="Client Operations">Client Ops</button>
                    <button type="button" class="cora-filter-pill" data-filter="Financials & Tax">Financials</button>
                    <button type="button" class="cora-filter-pill" data-filter="CRM & Growth">CRM</button>
                    <button type="button" class="cora-filter-pill" data-filter="Production Operations">Production</button>
                    <button type="button" class="cora-filter-pill" data-filter="Marketing & Showcase">Marketing</button>
                </div>
            </div>
        </div>

        <!-- Skills Grid Container -->
        <div class="cora-skills-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 12px;">
            <?php foreach ( $workspace_skills as $skill ) :
                $is_active = ( $skill['status'] === 'active' );
                $tools_arr = json_decode( $skill['bound_mcp_tools'], true ) ?: array();
                $triggers_arr = array_filter( array_map( 'trim', explode( ',', $skill['trigger_phrases'] ?? '' ) ) );
            ?>
                <div class="cora-skill-card <?php echo $is_active ? 'is-active' : 'is-inactive'; ?>" data-category="<?php echo esc_attr( $skill['category'] ); ?>" data-slug="<?php echo esc_attr( $skill['skill_slug'] ); ?>" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 12px; padding: 14px; display: flex; flex-direction: column; justify-content: space-between; gap: 12px; transition: all 0.15s ease;">
                    <div>
                        <!-- Header Row: Icon + Title + Toggle -->
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: <?php echo $is_active ? '#09090b' : '#f4f4f5'; ?>; color: <?php echo $is_active ? '#ffffff' : '#71717a'; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <?php echo ! empty( $skill['icon_svg'] ) ? $skill['icon_svg'] : '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>'; ?>
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-size: 13px; font-weight: 700; color: #09090b; display: flex; align-items: center; gap: 6px; line-height: 1.25;">
                                        <span class="cora-skill-name" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo esc_html( $skill['name'] ); ?></span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 3px; flex-wrap: wrap;">
                                        <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #71717a; background: #f4f4f5; padding: 1px 6px; border-radius: 4px;"><?php echo esc_html( $skill['category'] ); ?></span>
                                        <?php if ( ( $skill['created_by_model'] ?? '' ) === 'workspace_upload' ) : ?>
                                            <span style="font-size: 9.5px; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #18181b; background: #fafafa; border: 1px solid #e4e4e7; padding: 1px 5px; border-radius: 4px;">Uploaded by Workspace</span>
                                        <?php elseif ( ( $skill['created_by_model'] ?? '' ) === 'system_seed' ) : ?>
                                            <span style="font-size: 9.5px; font-family: 'JetBrains Mono', monospace; color: #71717a; background: #f4f4f5; padding: 1px 5px; border-radius: 4px;">Workspace Starter</span>
                                        <?php else : ?>
                                            <span style="font-size: 9.5px; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #18181b; background: #fafafa; border: 1px solid #e4e4e7; padding: 1px 5px; border-radius: 4px;">Created by Workspace</span>
                                        <?php endif; ?>
                                        <span style="font-size: 10px; color: #a1a1aa; background: #fafafa; border: 1px solid #e4e4e7; padding: 1px 4px; border-radius: 4px;">Model: <?php echo esc_html( ucfirst( $skill['model_preference'] ?: 'auto' ) ); ?></span>
                                    </div>
                                </div>
                            </div>

                            <label class="cora-switch" style="flex-shrink: 0; margin-left: 6px;" title="Toggle skill status">
                                <input type="checkbox" class="cora-skill-toggle" data-slug="<?php echo esc_attr( $skill['skill_slug'] ); ?>" <?php checked( $is_active ); ?>>
                                <span class="cora-slider"></span>
                            </label>
                        </div>

                        <!-- Description -->
                        <p class="cora-skill-desc" style="font-size: 11.5px; color: #71717a; line-height: 1.4; margin: 0 0 10px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo esc_html( $skill['description'] ); ?>
                        </p>

                        <!-- Trigger Intents Pills -->
                        <?php if ( ! empty( $triggers_arr ) ) : ?>
                            <div style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-bottom: 8px;">
                                <span style="font-size: 9.5px; color: #a1a1aa;">Triggers:</span>
                                <?php foreach ( array_slice( $triggers_arr, 0, 3 ) as $tr ) : ?>
                                    <span style="font-size: 9.5px; font-weight: 600; color: #18181b; background: #fafafa; border: 1px solid #e4e4e7; padding: 1px 5px; border-radius: 4px;">#<?php echo esc_html( $tr ); ?></span>
                                <?php endforeach; ?>
                                <?php if ( count( $triggers_arr ) > 3 ) : ?>
                                    <span style="font-size: 9.5px; color: #a1a1aa;">+<?php echo count( $triggers_arr ) - 3; ?> more</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Bound Tools -->
                        <div style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
                            <span style="font-size: 9.5px; color: #a1a1aa;">Tools:</span>
                            <?php foreach ( $tools_arr as $bt ) : ?>
                                <span style="font-size: 9.5px; font-family: 'JetBrains Mono', monospace; color: #71717a; background: #f4f4f5; padding: 1px 5px; border-radius: 4px;"><?php echo esc_html( $bt ); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 10px; border-top: 1px solid #f4f4f5; margin-top: 6px;">
                        <button type="button" class="cora-run-skill-simulator-btn cora-btn-micro" data-slug="<?php echo esc_attr( $skill['skill_slug'] ); ?>" data-name="<?php echo esc_attr( $skill['name'] ); ?>" style="padding: 4px 9px; font-size: 11px; font-weight: 700; background: #09090b; color: #ffffff; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            <span>Run Simulator</span>
                        </button>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <button type="button" class="cora-edit-skill-btn" data-id="<?php echo esc_attr( $skill['id'] ); ?>" data-slug="<?php echo esc_attr( $skill['skill_slug'] ); ?>" style="background: none; border: none; font-size: 11px; color: #71717a; cursor: pointer; text-decoration: underline;">
                                Edit
                            </button>
                            <?php if ( $skill['created_by_model'] !== 'system_seed' ) : ?>
                                <button type="button" class="cora-delete-skill-btn" data-slug="<?php echo esc_attr( $skill['skill_slug'] ); ?>" style="background: none; border: none; font-size: 11px; color: #ef4444; cursor: pointer;">
                                    Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ── Floating Unsaved Changes Bar for Modules Tab ──────────────────────── -->
    <div id="cora-fh-floating-bar" style="display: none; position: fixed; bottom: 84px; left: 50%; transform: translateX(-50%); z-index: 100040; background: rgba(9, 9, 11, 0.96); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); color: #ffffff; padding: 8px 14px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.35); border: 1px solid #27272a; align-items: center; gap: 12px; box-sizing: border-box; max-width: 90vw;">
        <div style="display: flex; align-items: center; gap: 6px;">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #fbbf24; display: inline-block; flex-shrink: 0;"></span>
            <span style="font-size: 11px; font-weight: 600; color: #f4f4f5; white-space: nowrap;">Unsaved changes</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px;">
            <button type="button" id="cora-fh-discard-btn" style="padding: 4px 9px; font-size: 11px; font-weight: 600; background: #27272a; color: #e4e4e7; border: 1px solid #3f3f46; border-radius: 6px; cursor: pointer;">
                Discard
            </button>
            <button type="button" id="cora-fh-save-bottom-btn" class="cora-fh-save-btn" style="padding: 5px 12px; font-size: 11px; font-weight: 700; background: #ffffff; color: #09090b; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="cora-save-icon">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span class="cora-save-text">Save Changes</span>
            </button>
        </div>
    </div>

    <!-- ── Recommendation Bottom Sheet ──────────────────────────────────────── -->
    <div id="cora-fh-recommendation-sheet" class="pointer-events-none" style="display: none; position: fixed; inset: 0; z-index: 100060; align-items: flex-end; justify-content: center; box-sizing: border-box;">
        <div id="cora-fh-rec-backdrop" onclick="coraCloseRecSheet()" style="position: absolute; inset: 0; background: rgba(9, 9, 11, 0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); opacity: 0; transition: opacity 0.25s ease;"></div>
        <div id="cora-fh-rec-drawer" style="position: relative; width: 100%; max-width: 520px; background: #ffffff; border-radius: 20px 20px 0 0; padding: 18px 20px 28px; box-shadow: 0 -10px 40px rgba(0,0,0,0.18); transform: translate(-50%, 100%); left: 50%; transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1); box-sizing: border-box; display: flex; flex-direction: column; gap: 14px; z-index: 10;">
            <div style="width: 36px; height: 4px; background: #e4e4e7; border-radius: 999px; margin: 0 auto -4px; flex-shrink: 0;"></div>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; border-radius: 7px; background: #f4f4f5; display: flex; align-items: center; justify-content: center; color: #18181b;">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                    </div>
                    <div>
                        <h4 style="font-size: 13px; font-weight: 800; color: #18181b; margin: 0; line-height: 1.2;">Recommended Companion Modules</h4>
                        <span style="font-size: 11px; color: #71717a;">Activating <strong id="cora-fh-rec-module-name" style="color: #18181b;"></strong> works best with these modules:</span>
                    </div>
                </div>
                <button type="button" onclick="coraCloseRecSheet()" style="background: none; border: none; padding: 4px; color: #71717a; cursor: pointer; font-size: 16px;">✕</button>
            </div>
            <div id="cora-fh-rec-list" style="display: flex; flex-direction: column; gap: 8px; max-height: 240px; overflow-y: auto;"></div>
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; padding-top: 4px; border-top: 1px solid #f4f4f5;">
                <button type="button" onclick="coraCloseRecSheet()" class="cora-btn-micro" style="padding: 7px 12px; font-size: 11.5px; font-weight: 600; background: #f4f4f5; border: 1px solid #e4e4e7; border-radius: 8px;">Skip</button>
                <button type="button" id="cora-fh-rec-activate-btn" class="cora-btn-primary" style="padding: 7px 14px; font-size: 11.5px; font-weight: 700; background: #09090b; color: #ffffff; border: none; border-radius: 8px; cursor: pointer;">
                    <span id="cora-fh-rec-btn-text">Activate All</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════
         SHOPIFY-INSPIRED MAIN-AREA DOCKED PANELS (SOP 15)
         ══════════════════════════════════════════════════════════════════════════ -->

    <!-- DRAWER 1: PLUGIN STUDIO (CREATE / EDIT PLUGIN) -->
    <aside id="cora-plugin-drawer" class="cora-docked-drawer collapsed">
        <div class="cora-drawer-header px-3 sm:px-6 py-2 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
            <div class="flex flex-wrap lg:flex-nowrap items-center min-h-[38px] lg:min-h-[44px] w-full gap-y-2">
                <!-- Left: Icon, Title & Pill -->
                <div class="flex items-center gap-2 sm:gap-2.5 cora-drawer-header-left min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[11px] sm:text-xs shrink-0 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    </div>
                    <div class="min-w-0 flex items-center gap-2">
                        <h2 class="text-xs sm:text-sm font-bold text-zinc-950 dark:text-zinc-50 truncate tracking-tight" id="cora-plugin-drawer-title">Plugin Studio</h2>
                        <span id="cora-plugin-drawer-badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span id="cora-plugin-badge-text">Workspace Extension</span>
                        </span>
                    </div>
                </div>

                <!-- Center: Level 01 Segmented Pill Control -->
                <div class="flex items-center justify-start sm:justify-center overflow-x-auto cora-no-scrollbar py-0.5 cora-drawer-header-center">
                    <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 sm:gap-1.5 select-none shrink-0" id="cora-plugin-tab-bar">
                        <button type="button" onclick="coraSwitchPluginDrawerTab('visual')" id="cora-plugin-tab-btn-visual" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                            <span>Visual Builder</span>
                        </button>
                        <button type="button" onclick="coraSwitchPluginDrawerTab('manifest')" id="cora-plugin-tab-btn-manifest" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                            <span>JSON Manifest & MCP</span>
                        </button>
                        <button type="button" onclick="coraSwitchPluginDrawerTab('upload')" id="cora-plugin-tab-btn-upload" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                            <span>Upload Package</span>
                        </button>
                    </div>
                </div>

                <!-- Right: Maximize & Close -->
                <div class="flex items-center gap-1.5 shrink-0 cora-drawer-header-right">
                    <button type="button" onclick="coraToggleDrawerMaximize('cora-plugin-drawer', 'cora-plugin-maximize-btn')" id="cora-plugin-maximize-btn" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0" title="Expand / Fullscreen Canvas">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                    </button>
                    <button type="button" onclick="coraCloseDockedDrawer('cora-plugin-drawer')" id="cora-plugin-close-btn" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0" title="Close Drawer (Esc)">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>

        <form id="cora-plugin-form" class="flex flex-col flex-1 h-full min-h-0 m-0 p-0">
            <input type="hidden" id="cora-plugin-input-id" name="id" value="">
            <input type="hidden" id="cora-plugin-input-category" name="category" value="Integrations">

            <div class="cora-drawer-body flex-1 overflow-y-auto px-4 sm:px-8 py-6 bg-zinc-50/50 dark:bg-zinc-950/50">
                <div class="max-w-4xl mx-auto space-y-6">

                    <!-- TAB 1: VISUAL BUILDER -->
                    <div id="cora-plugin-tab-panel-visual" class="space-y-5">
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Core Identity</h3>
                            
                            <div>
                                <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Plugin Display Name</label>
                                <input type="text" id="cora-plugin-input-name" name="name" required placeholder="e.g. Notion Client Lead Pipeline Sync" class="w-full h-9 px-3 text-xs bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Plugin Slug (Unique API Identifier)</label>
                                    <input type="text" id="cora-plugin-input-slug" name="plugin_slug" required placeholder="e.g. notion-sync" class="w-full h-9 px-3 text-xs font-mono bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Category</label>
                                    <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex flex-wrap gap-1" id="cora-plugin-cat-pills">
                                        <?php foreach ( array( 'Integrations', 'Financials', 'Operations', 'Productivity', 'Custom' ) as $c_opt ) : ?>
                                            <button type="button" onclick="coraSetPluginCategory('<?php echo esc_js($c_opt); ?>')" data-cat="<?php echo esc_attr($c_opt); ?>" class="cora-cat-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-medium transition cursor-pointer border-0 <?php echo $c_opt === 'Integrations' ? 'bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-bold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 bg-transparent hover:text-zinc-950 dark:hover:text-white'; ?>">
                                                <?php echo esc_html($c_opt); ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Description & Intent</label>
                                <textarea id="cora-plugin-input-desc" name="description" rows="2" placeholder="Brief summary of what this plugin performs, webhook events listened, and systems integrated..." class="w-full p-3 text-xs bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition"></textarea>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Connection & Webhook Parameters</h3>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Configuration Settings (JSON Object)</label>
                                <textarea id="cora-plugin-input-config" name="config_json" rows="3" placeholder='{"webhook_url": "https://api.external.com/hook", "sync_interval_mins": 15}' class="w-full p-3 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: JSON MANIFEST & MCP -->
                    <div id="cora-plugin-tab-panel-manifest" class="space-y-5" style="display: none;">
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Model Context Protocol (MCP) Tools Export</h3>
                                <span class="text-[11px] font-mono text-zinc-400">cora_plugin_{slug}_{action}</span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Declare the JSON Schema of tools this plugin exposes to connected AI agents (Gemini, Claude, OpenAI):</p>
                            <textarea id="cora-plugin-input-mcp" name="mcp_tools_json" rows="8" placeholder='[{"name": "notion_create_page", "description": "Creates page in Notion", "parameters": {"type": "object", "properties": {"title": {"type": "string"}}}}]' class="w-full p-3 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition"></textarea>
                        </div>
                    </div>

                    <!-- TAB 3: UPLOAD PACKAGE -->
                    <div id="cora-plugin-tab-panel-upload" class="space-y-5" style="display: none;">
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Upload Workspace Plugin Package</h3>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">Workspace Isolated</span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Upload a Plugin JSON Manifest package or paste its JSON specification directly. Uploaded plugins are installed strictly at the current workspace level and will never affect other tenants.
                            </p>

                            <!-- Dropzone -->
                            <div id="cora-plugin-dropzone" class="border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-300 rounded-2xl p-6 sm:p-8 flex flex-col items-center justify-center text-center cursor-pointer transition bg-zinc-50/50 dark:bg-zinc-950/50">
                                <input type="file" id="cora-plugin-file-upload-input" accept=".json,application/json" class="hidden">
                                <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center mb-3">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                </div>
                                <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100 mb-1" id="cora-plugin-dropzone-label">
                                    Click to browse or drop a .json plugin manifest here
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 max-w-sm">
                                    Supports standard Cora Plugin Manifests containing <code class="font-mono text-[10px] text-zinc-700 dark:text-zinc-300">name</code>, <code class="font-mono text-[10px] text-zinc-700 dark:text-zinc-300">plugin_slug</code>, and optional MCP tools.
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Or Paste Plugin Manifest JSON</label>
                                <textarea id="cora-plugin-paste-manifest" rows="6" placeholder='{
  "name": "Custom Workspace Sync",
  "plugin_slug": "custom-ws-sync",
  "description": "Tenant-scoped integration",
  "category": "Integrations",
  "config": {},
  "mcp_tools": []
}' class="w-full p-3 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" id="cora-submit-upload-plugin-btn" class="px-4 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold transition cursor-pointer border-0 shadow-2xs inline-flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <span>Upload & Install into Workspace</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- STICKY BOTTOM FOOTER -->
            <div class="px-4 sm:px-6 lg:px-8 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 sticky bottom-0 z-30">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10.5px] font-mono bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span id="cora-plugin-drawer-status-pill">Tenant Isolated Plugin</span>
                    </span>
                </div>
                <div class="hidden md:flex items-center gap-2 text-xs text-zinc-400 font-mono">
                    <span>Esc to close</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="coraCloseDockedDrawer('cora-plugin-drawer')" class="px-3.5 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold transition cursor-pointer border-0 shadow-2xs">
                        Save Plugin
                    </button>
                </div>
            </div>
        </form>
    </aside>

    <!-- DRAWER 2: SKILL STUDIO (CREATE / EDIT AI SKILL) -->
    <aside id="cora-skill-drawer" class="cora-docked-drawer collapsed">
        <div class="cora-drawer-header px-3 sm:px-6 py-2 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
            <div class="flex flex-wrap lg:flex-nowrap items-center min-h-[38px] lg:min-h-[44px] w-full gap-y-2">
                <!-- Left: Icon, Title & Pill -->
                <div class="flex items-center gap-2 sm:gap-2.5 cora-drawer-header-left min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[11px] sm:text-xs shrink-0 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <div class="min-w-0 flex items-center gap-2">
                        <h2 class="text-xs sm:text-sm font-bold text-zinc-950 dark:text-zinc-50 truncate tracking-tight" id="cora-skill-drawer-title">AI Skill Studio</h2>
                        <span id="cora-skill-drawer-badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span id="cora-skill-badge-text">Agent Behavioral Runtime</span>
                        </span>
                    </div>
                </div>

                <!-- Center: Level 01 Segmented Pill Control -->
                <div class="flex items-center justify-start sm:justify-center overflow-x-auto cora-no-scrollbar py-0.5 cora-drawer-header-center">
                    <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 sm:gap-1.5 select-none shrink-0" id="cora-skill-tab-bar">
                        <button type="button" onclick="coraSwitchSkillDrawerTab('instructions')" id="cora-skill-tab-btn-instructions" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                            <span>System Instructions</span>
                        </button>
                        <button type="button" onclick="coraSwitchSkillDrawerTab('triggers')" id="cora-skill-tab-btn-triggers" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                            <span>Triggers & Bound Tools</span>
                        </button>
                        <button type="button" onclick="coraSwitchSkillDrawerTab('upload')" id="cora-skill-tab-btn-upload" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                            <span>Upload Definition</span>
                        </button>
                    </div>
                </div>

                <!-- Right: Maximize & Close -->
                <div class="flex items-center gap-1.5 shrink-0 cora-drawer-header-right">
                    <button type="button" onclick="coraToggleDrawerMaximize('cora-skill-drawer', 'cora-skill-maximize-btn')" id="cora-skill-maximize-btn" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0" title="Expand / Fullscreen Canvas">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                    </button>
                    <button type="button" onclick="coraCloseDockedDrawer('cora-skill-drawer')" id="cora-skill-close-btn" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0" title="Close Drawer (Esc)">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>

        <form id="cora-skill-form" class="flex flex-col flex-1 h-full min-h-0 m-0 p-0">
            <input type="hidden" id="cora-skill-input-id" name="id" value="">
            <input type="hidden" id="cora-skill-input-category" name="category" value="Client Operations">

            <div class="cora-drawer-body flex-1 overflow-y-auto px-4 sm:px-8 py-6 bg-zinc-50/50 dark:bg-zinc-950/50">
                <div class="max-w-4xl mx-auto space-y-6">

                    <!-- TAB 1: SYSTEM INSTRUCTIONS -->
                    <div id="cora-skill-tab-panel-instructions" class="space-y-5">
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Skill Identity & Categorization</h3>
                            
                            <div>
                                <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Skill Display Title</label>
                                <input type="text" id="cora-skill-input-name" name="name" required placeholder="e.g. Wedding Photography Retainer Validator" class="w-full h-9 px-3 text-xs bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Skill Slug (API & MCP Identifier)</label>
                                    <input type="text" id="cora-skill-input-slug" name="skill_slug" required placeholder="e.g. retainer-validator" class="w-full h-9 px-3 text-xs font-mono bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Category</label>
                                    <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex flex-wrap gap-1" id="cora-skill-cat-pills">
                                        <?php foreach ( array( 'Client Operations', 'Financials & Tax', 'CRM & Growth', 'Production Operations', 'Marketing & Showcase', 'General' ) as $s_opt ) : ?>
                                            <button type="button" onclick="coraSetSkillCategory('<?php echo esc_js($s_opt); ?>')" data-cat="<?php echo esc_attr($s_opt); ?>" class="cora-skill-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-medium transition cursor-pointer border-0 <?php echo $s_opt === 'Client Operations' ? 'bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-bold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 bg-transparent hover:text-zinc-950 dark:hover:text-white'; ?>">
                                                <?php echo esc_html($s_opt); ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Behavioral System Prompt (Core Instructions)</h3>
                                <span class="text-[11px] font-mono text-zinc-400">Injected into LLM context</span>
                            </div>
                            <textarea id="cora-skill-input-prompt" name="instruction_prompt" rows="7" required placeholder="You are the Retainer Specialist for this studio. When invoked, verify that 50% deposit has cleared prior to date confirmation..." class="w-full p-3.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition leading-relaxed"></textarea>
                        </div>
                    </div>

                    <!-- TAB 2: TRIGGERS & TOOLS -->
                    <div id="cora-skill-tab-panel-triggers" class="space-y-5" style="display: none;">
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Agent Triggers & Intent Matching</h3>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Trigger Intent Phrases (Comma-separated)</label>
                                <input type="text" id="cora-skill-input-triggers" name="trigger_phrases" placeholder="check retainer, verify deposit, booking confirmation, invoice status" class="w-full h-9 px-3 text-xs bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition">
                                <p class="text-[11px] text-zinc-400 mt-1">Autonomous agents match user messages against these intent keywords to route tasks here.</p>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Bound Model Context Protocol (MCP) Tools</h3>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Bound MCP Tools (JSON Array)</label>
                                <input type="text" id="cora-skill-input-tools" name="bound_mcp_tools" placeholder='["cora_list_bookings", "cora_list_invoices", "cora_record_financial_transaction"]' class="w-full h-9 px-3 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition">
                                <p class="text-[11px] text-zinc-400 mt-1">Tools the LLM is granted permission to call during execution of this skill.</p>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: UPLOAD DEFINITION -->
                    <div id="cora-skill-tab-panel-upload" class="space-y-5" style="display: none;">
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Upload Workspace Skill Definition</h3>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">Workspace Isolated</span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Upload an AI Skill file (Antigravity <code class="font-mono text-[10px] text-zinc-700 dark:text-zinc-300">SKILL.md</code> with YAML frontmatter or <code class="font-mono text-[10px] text-zinc-700 dark:text-zinc-300">.json</code> specification) or paste its content directly. Uploaded skills are available strictly within this workspace runtime.
                            </p>

                            <!-- Dropzone -->
                            <div id="cora-skill-dropzone" class="border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-300 rounded-2xl p-6 sm:p-8 flex flex-col items-center justify-center text-center cursor-pointer transition bg-zinc-50/50 dark:bg-zinc-950/50">
                                <input type="file" id="cora-skill-file-upload-input" accept=".md,.markdown,.json,text/markdown,application/json" class="hidden">
                                <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center mb-3">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                </div>
                                <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100 mb-1" id="cora-skill-dropzone-label">
                                    Click to browse or drop a SKILL.md or .json spec here
                                </div>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 max-w-sm">
                                    Supports Antigravity Markdown with YAML frontmatter (<code class="font-mono text-[10px] text-zinc-700 dark:text-zinc-300">name</code>, <code class="font-mono text-[10px] text-zinc-700 dark:text-zinc-300">trigger_phrases</code>, <code class="font-mono text-[10px] text-zinc-700 dark:text-zinc-300">bound_mcp_tools</code>) or JSON schemas.
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">Or Paste SKILL.md / JSON Content</label>
                                <textarea id="cora-skill-paste-spec" rows="7" placeholder="---
name: Client Booking Specialist
skill_slug: booking-specialist
description: Handles appointment confirmations
trigger_phrases: book date, confirm shoot, schedule
bound_mcp_tools:
  - cora_list_bookings
---

You are the client booking coordinator for this studio..." class="w-full p-3 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" id="cora-submit-upload-skill-btn" class="px-4 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold transition cursor-pointer border-0 shadow-2xs inline-flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <span>Upload & Register into Workspace</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- STICKY BOTTOM FOOTER -->
            <div class="px-4 sm:px-6 lg:px-8 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 sticky bottom-0 z-30">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10.5px] font-mono bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span id="cora-skill-drawer-status-pill">Tenant Isolated AI Skill</span>
                    </span>
                </div>
                <div class="hidden md:flex items-center gap-2 text-xs text-zinc-400 font-mono">
                    <span>Esc to close</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="coraCloseDockedDrawer('cora-skill-drawer')" class="px-3.5 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold transition cursor-pointer border-0 shadow-2xs">
                        Save Skill
                    </button>
                </div>
            </div>
        </form>
    </aside>

    <!-- DRAWER 3: API & MCP GATEWAY DRAWER -->
    <aside id="cora-api-keys-drawer" class="cora-docked-drawer collapsed">
        <div class="cora-drawer-header px-3 sm:px-6 py-2 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
            <div class="flex flex-wrap lg:flex-nowrap items-center min-h-[38px] lg:min-h-[44px] w-full gap-y-2">
                <!-- Left: Icon, Title & Pill -->
                <div class="flex items-center gap-2 sm:gap-2.5 cora-drawer-header-left min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[11px] sm:text-xs shrink-0 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 2l-2 2m-2-2l2 2m-2 2l2-2m-7 7l7-7-3-3-7 7v3h3z"></path><circle cx="7.5" cy="16.5" r="4.5"></circle></svg>
                    </div>
                    <div class="min-w-0 flex items-center gap-2">
                        <h2 class="text-xs sm:text-sm font-bold text-zinc-950 dark:text-zinc-50 truncate tracking-tight">Workspace API & MCP Gateway</h2>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Live MCP 2.0</span>
                        </span>
                    </div>
                </div>

                <!-- Center: Level 01 Segmented Pill Control -->
                <div class="flex items-center justify-start sm:justify-center overflow-x-auto cora-no-scrollbar py-0.5 cora-drawer-header-center">
                    <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 sm:gap-1.5 select-none shrink-0" id="cora-api-tab-bar">
                        <button type="button" onclick="coraSwitchApiDrawerTab('keys')" id="cora-api-tab-btn-keys" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                            <span>Workspace API Keys</span>
                        </button>
                        <button type="button" onclick="coraSwitchApiDrawerTab('clients')" id="cora-api-tab-btn-clients" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                            <span>Client Setup & Config</span>
                        </button>
                    </div>
                </div>

                <!-- Right: Maximize & Close -->
                <div class="flex items-center gap-1.5 shrink-0 cora-drawer-header-right">
                    <button type="button" onclick="coraToggleDrawerMaximize('cora-api-keys-drawer', 'cora-api-maximize-btn')" id="cora-api-maximize-btn" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0" title="Expand / Fullscreen Canvas">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                    </button>
                    <button type="button" onclick="coraCloseDockedDrawer('cora-api-keys-drawer')" id="cora-api-close-btn" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0" title="Close Drawer (Esc)">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="cora-drawer-body flex-1 overflow-y-auto px-4 sm:px-8 py-6 bg-zinc-50/50 dark:bg-zinc-950/50">
            <div class="max-w-4xl mx-auto space-y-6">

                <!-- TAB 1: WORKSPACE API KEYS -->
                <div id="cora-api-tab-panel-keys" class="space-y-6">
                    <!-- Create Key Card -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-950 dark:text-zinc-50">Generate Workspace Scoped API Key</h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Use this key to authenticate external agents, scripts, or MCP clients with tenant isolation.</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2.5">
                            <input type="text" id="cora-new-key-name" placeholder="Key description (e.g. Claude Desktop Live Integration)" class="flex-1 h-9 px-3 text-xs bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition">
                            <button type="button" id="cora-create-key-submit" class="px-4 py-2 text-xs font-bold bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 rounded-xl transition cursor-pointer border-0 shadow-2xs whitespace-nowrap">
                                Generate Key
                            </button>
                        </div>

                        <div id="cora-generated-key-box" style="display: none;" class="p-3.5 bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-xl space-y-2">
                            <div class="text-xs font-bold text-amber-900 dark:text-amber-200">Copy your secret key now. It will not be shown again:</div>
                            <div class="flex items-center gap-2">
                                <input type="text" id="cora-secret-key-display" readonly class="flex-1 h-8 px-2.5 font-mono text-xs bg-white dark:bg-zinc-900 border border-amber-200 dark:border-amber-800 rounded-lg select-all">
                                <button type="button" onclick="navigator.clipboard.writeText($('#cora-secret-key-display').val()); coraShowToast('API Key copied to clipboard!', 'success');" class="px-3 h-8 text-xs font-bold bg-amber-700 hover:bg-amber-800 text-white rounded-lg transition cursor-pointer border-0">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Active Keys Registry Table -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Active Workspace Keys</h3>
                            <span class="text-xs font-mono text-zinc-400">Scoped to Agency <?php echo esc_html($agency_id); ?></span>
                        </div>

                        <div id="cora-api-keys-table-wrap" class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400">
                                        <th class="py-2 px-3 font-semibold">Name</th>
                                        <th class="py-2 px-3 font-semibold">Prefix</th>
                                        <th class="py-2 px-3 font-semibold">Status</th>
                                        <th class="py-2 px-3 font-semibold">Created</th>
                                        <th class="py-2 px-3 font-semibold text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cora-api-keys-tbody" class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                                    <?php if ( empty( $workspace_keys ) ) : ?>
                                        <tr><td colspan="5" class="py-6 text-center text-zinc-400">No API keys generated yet. Click "Generate Key" above to create one.</td></tr>
                                    <?php else : ?>
                                        <?php foreach ( $workspace_keys as $k ) : ?>
                                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition">
                                                <td class="py-2.5 px-3 font-medium text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $k['key_name'] ); ?></td>
                                                <td class="py-2.5 px-3 font-mono text-zinc-500 text-[11px]"><?php echo esc_html( $k['key_prefix'] ); ?>••••••••</td>
                                                <td class="py-2.5 px-3">
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10.5px] font-medium <?php echo $k['status'] === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400'; ?>">
                                                        <span class="w-1.5 h-1.5 rounded-full <?php echo $k['status'] === 'active' ? 'bg-emerald-500' : 'bg-rose-500'; ?>"></span>
                                                        <span><?php echo esc_html( ucfirst( $k['status'] ) ); ?></span>
                                                    </span>
                                                </td>
                                                <td class="py-2.5 px-3 text-zinc-400"><?php echo esc_html( substr( $k['created_at'], 0, 10 ) ); ?></td>
                                                <td class="py-2.5 px-3 text-right">
                                                    <?php if ( $k['status'] === 'active' ) : ?>
                                                        <button type="button" class="cora-revoke-key-btn text-rose-600 hover:text-rose-700 text-xs font-semibold cursor-pointer border-0 bg-transparent" data-id="<?php echo esc_attr( $k['id'] ); ?>">Revoke</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: CLIENT SETUP & CONFIG -->
                <div id="cora-api-tab-panel-clients" class="space-y-6" style="display: none;">
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                        <h3 class="text-sm font-bold text-zinc-950 dark:text-zinc-50">Connect with Claude Desktop / Cursor / Antigravity</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Paste this configuration into your <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-[11px]">claude_desktop_config.json</code> or MCP settings file:</p>
                        <pre class="p-4 bg-zinc-950 text-zinc-100 rounded-xl font-mono text-xs overflow-x-auto leading-relaxed border border-zinc-800">{
  "mcpServers": {
    "cora-workspace": {
      "url": "<?php echo esc_url( site_url( '/mcp' ) ); ?>",
      "headers": {
        "Authorization": "Bearer YOUR_WORKSPACE_API_KEY"
      }
    }
  }
}</pre>
                    </div>

                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                        <h3 class="text-sm font-bold text-zinc-950 dark:text-zinc-50">Direct REST API Header Authentication</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Pass either <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-[11px]">X-Cora-API-Key: &lt;YOUR_KEY&gt;</code> or <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-[11px]">Authorization: Bearer &lt;YOUR_KEY&gt;</code>:</p>
                        <pre class="p-4 bg-zinc-950 text-zinc-100 rounded-xl font-mono text-xs overflow-x-auto leading-relaxed border border-zinc-800">curl -X GET "<?php echo esc_url( site_url( '/wp-json/cora/v1/workspace/plugins' ) ); ?>" \
     -H "X-Cora-API-Key: YOUR_WORKSPACE_API_KEY" \
     -H "Accept: application/json"</pre>
                    </div>
                </div>

            </div>
        </div>

        <!-- STICKY BOTTOM FOOTER -->
        <div class="px-4 sm:px-6 lg:px-8 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 sticky bottom-0 z-30">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10.5px] font-mono bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>REST & MCP Protocol Ready</span>
                </span>
            </div>
            <div class="hidden md:flex items-center gap-2 text-xs text-zinc-400 font-mono">
                <span>Esc to close</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="coraCloseDockedDrawer('cora-api-keys-drawer')" class="px-4 py-1.5 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold transition cursor-pointer border-0 shadow-2xs">
                    Done
                </button>
            </div>
        </div>
    </aside>

    <!-- DRAWER 4: SKILL SIMULATOR RUNNER SHEET -->
    <aside id="cora-skill-simulator-drawer" class="cora-docked-drawer collapsed">
        <div class="cora-drawer-header px-3 sm:px-6 py-2 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
            <div class="flex flex-wrap lg:flex-nowrap items-center min-h-[38px] lg:min-h-[44px] w-full gap-y-2">
                <!-- Left: Icon, Title & Pill -->
                <div class="flex items-center gap-2 sm:gap-2.5 cora-drawer-header-left min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[11px] sm:text-xs shrink-0 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    </div>
                    <div class="min-w-0 flex items-center gap-2">
                        <h2 class="text-xs sm:text-sm font-bold text-zinc-950 dark:text-zinc-50 truncate tracking-tight" id="cora-simulator-title">Skill Simulator & Test Console</h2>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Live Context Sandbox</span>
                        </span>
                    </div>
                </div>

                <!-- Center: Stepper/Context hint -->
                <div class="flex items-center justify-start sm:justify-center overflow-x-auto cora-no-scrollbar py-0.5 cora-drawer-header-center">
                    <div class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-[11px] font-mono text-zinc-600 dark:text-zinc-400">
                        <span>Runtime: Dynamic LLM Context Injection</span>
                    </div>
                </div>

                <!-- Right: Maximize & Close -->
                <div class="flex items-center gap-1.5 shrink-0 cora-drawer-header-right">
                    <button type="button" onclick="coraToggleDrawerMaximize('cora-skill-simulator-drawer', 'cora-simulator-maximize-btn')" id="cora-simulator-maximize-btn" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0" title="Expand / Fullscreen Canvas">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                    </button>
                    <button type="button" onclick="coraCloseDockedDrawer('cora-skill-simulator-drawer')" id="cora-simulator-close-btn" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0" title="Close Drawer (Esc)">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="cora-drawer-body flex-1 overflow-y-auto px-4 sm:px-8 py-6 bg-zinc-50/50 dark:bg-zinc-950/50">
            <div class="max-w-4xl mx-auto space-y-6">
                <input type="hidden" id="cora-simulator-slug" value="">

                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Simulation Input Parameters</h3>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mb-1.5">User Prompt or Action Arguments</label>
                        <textarea id="cora-simulator-input" rows="4" placeholder="Enter message prompt to simulate skill invocation (e.g. 'Audit client retainer deposit status for upcoming shoot on Saturday')..." class="w-full p-3.5 text-xs bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:border-zinc-900 dark:focus:border-white focus:outline-none transition leading-relaxed"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="cora-run-simulation-btn" class="px-4 py-2 text-xs font-bold bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 rounded-xl transition cursor-pointer border-0 shadow-2xs flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            <span>Execute Simulation</span>
                        </button>
                    </div>
                </div>

                <!-- Simulation Output -->
                <div id="cora-simulator-output-box" style="display: none;" class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Execution Output & Telemetry</h3>
                        <span id="cora-simulator-time-pill" class="text-[10.5px] font-mono text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md border border-zinc-200 dark:border-zinc-700"></span>
                    </div>
                    <pre id="cora-simulator-result" class="p-4 bg-zinc-950 text-zinc-100 rounded-xl font-mono text-xs overflow-x-auto leading-relaxed border border-zinc-800 max-h-[380px] overflow-y-auto whitespace-pre-wrap"></pre>
                </div>

            </div>
        </div>

        <!-- STICKY BOTTOM FOOTER -->
        <div class="px-4 sm:px-6 lg:px-8 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 sticky bottom-0 z-30">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10.5px] font-mono bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Simulator Sandbox</span>
                </span>
            </div>
            <div class="hidden md:flex items-center gap-2 text-xs text-zinc-400 font-mono">
                <span>Esc to close</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="coraCloseDockedDrawer('cora-skill-simulator-drawer')" class="px-4 py-1.5 rounded-xl bg-zinc-950 hover:bg-zinc-850 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold transition cursor-pointer border-0 shadow-2xs">
                    Close Simulator
                </button>
            </div>
        </div>
    </aside>

</div>

<!-- ── STYLES ─────────────────────────────────────────────────────────────── -->
<style>
/* Main Tabs Level 01 Segmented Button */
.cora-main-tab-btn {
    background: transparent;
    color: #71717a;
}
.cora-main-tab-btn.active {
    background: #ffffff !important;
    color: #09090b !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.dark .cora-main-tab-btn.active {
    background: #27272a !important;
    color: #ffffff !important;
}

/* Category Filter Pills */
.cora-filter-pill, .cora-fh-cat-filter {
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 6px;
    border: 1px solid #e4e4e7;
    background: #ffffff;
    color: #71717a;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.cora-filter-pill:hover, .cora-fh-cat-filter:hover {
    background: #f4f4f5;
    color: #18181b;
}
.cora-filter-pill.active, .cora-fh-cat-filter.active {
    background: #09090b !important;
    color: #ffffff !important;
    border-color: #09090b !important;
}

/* Modules Grid */
.cora-fh-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 8px;
}
@media (max-width: 640px) {
    .cora-fh-grid, .cora-plugins-grid, .cora-skills-grid {
        grid-template-columns: 1fr !important;
    }
}

/* Feature Card */
.cora-feature-card {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.15s ease;
}
.cora-feature-card.is-active {
    background: #ffffff;
    border-color: #e4e4e7;
}
.cora-feature-card.is-inactive {
    background: #fafafa;
    border-color: #f4f4f5;
    opacity: 0.85;
}

/* Monochromatic Switch */
.cora-switch {
    position: relative;
    display: inline-block;
    width: 32px;
    height: 18px;
}
.cora-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.cora-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #e4e4e7;
    transition: .2s;
    border-radius: 18px;
}
.cora-slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .2s;
    border-radius: 50%;
}
.cora-switch input:checked + .cora-slider {
    background-color: #09090b;
}
.cora-switch input:checked + .cora-slider:before {
    transform: translateX(14px);
}

/* ==========================================================================
   RULE 15 SOP: SHOPIFY-INSPIRED MAIN-AREA DOCKED VIEW PANELS
   Universal Bottom-Slide Motion (translateY(0)), Below-Header Docking,
   Zero Backdrop / Overlay, Sidebar Remains 100% Accessible & Interactive
   ========================================================================== */
#cora-plugin-drawer,
#cora-skill-drawer,
#cora-api-keys-drawer,
#cora-skill-simulator-drawer,
.cora-docked-drawer {
    position: fixed !important;
    top: var(--cora-main-top, 52px) !important;
    bottom: 0 !important;
    left: var(--cora-main-left, 16rem) !important;
    right: 0 !important;
    width: var(--cora-main-width, calc(100vw - 16rem)) !important;
    max-width: var(--cora-main-width, calc(100vw - 16rem)) !important;
    height: var(--cora-main-height, calc(100vh - 52px)) !important;
    max-height: var(--cora-main-height, calc(100vh - 52px)) !important;
    margin: 0 !important;
    padding: 0 !important;
    border-radius: 0 !important;
    border-top: 1px solid #e4e4e7 !important;
    border-left: 1px solid #e4e4e7 !important;
    border-right: none !important;
    border-bottom: none !important;
    background-color: #ffffff !important;
    z-index: 40 !important; /* Kept strictly below sidebar (z-index: 60) and topbar (z-index: 9999) */
    box-shadow: none !important;
    -webkit-box-shadow: none !important;
    transition: left 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                width 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                max-width 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                opacity 0.15s ease !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}

.dark #cora-plugin-drawer,
.dark #cora-skill-drawer,
.dark #cora-api-keys-drawer,
.dark #cora-skill-simulator-drawer,
.dark .cora-docked-drawer {
    background-color: #18181b !important;
    border-top: 1px solid #27272a !important;
    border-left: 1px solid #27272a !important;
}

/* SIDEBAR COLLAPSED: Dynamically adjust to 4rem (64px / w-16) on desktop */
@media (min-width: 1024px) {
    body:has(.cora-sidebar.collapsed-sidebar) .cora-docked-drawer:not(.maximized),
    body.cora-sidebar-collapsed .cora-docked-drawer:not(.maximized),
    .cora-sidebar.collapsed-sidebar ~ .cora-main .cora-docked-drawer:not(.maximized),
    .cora-docked-drawer.sidebar-collapsed:not(.maximized) {
        left: 4rem !important;
        width: calc(100vw - 4rem) !important;
        max-width: calc(100vw - 4rem) !important;
    }
}

/* MAXIMIZED FULLSCREEN VIEW (Shopify Expand ⤢ Mode) */
.cora-docked-drawer.maximized {
    left: 0 !important;
    width: 100vw !important;
    max-width: 100vw !important;
    border-left: none !important;
    z-index: 70 !important;
}

/* MOBILE / TABLET (< 1024px): Full viewport width */
@media (max-width: 1023px) {
    .cora-docked-drawer,
    body:has(.cora-sidebar.collapsed-sidebar) .cora-docked-drawer,
    body.cora-sidebar-collapsed .cora-docked-drawer,
    .cora-docked-drawer.sidebar-collapsed {
        left: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        border-left: none !important;
        z-index: 10000 !important;
    }
    body:has(.cora-docked-drawer.open) #cora-mobile-floating-island,
    body:has(.cora-docked-drawer:not(.collapsed)) #cora-mobile-floating-island {
        transform: translateY(150%) !important;
        opacity: 0 !important;
        pointer-events: none !important;
        display: none !important;
    }
}

/* OPEN & COLLAPSED STATES (UNIVERSAL BOTTOM SLIDE MOTION) */
.cora-docked-drawer.open:not(.collapsed) {
    transform: translateY(0) !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}

.cora-docked-drawer.collapsed,
.cora-docked-drawer:not(.open) {
    transform: translateY(100%) !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
    display: none !important;
}

/* Header 3-Part Alignment: Left Title, Center Tabs, Extreme Right Buttons */
@media (max-width: 1023px) {
    .cora-drawer-header-left {
        order: 1 !important;
        flex: 1 1 auto !important;
        min-width: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    .cora-drawer-header-right {
        order: 2 !important;
        margin-left: auto !important;
        flex-shrink: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    .cora-drawer-header-center {
        order: 3 !important;
        width: 100% !important;
        flex: 0 0 100% !important;
    }
}
@media (min-width: 1024px) {
    .cora-drawer-header-left {
        order: 1 !important;
        flex: 1 1 0% !important;
        min-width: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
    }
    .cora-drawer-header-center {
        order: 2 !important;
        flex: 0 0 auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto !important;
    }
    .cora-drawer-header-right {
        order: 3 !important;
        flex: 1 1 0% !important;
        min-width: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        margin-left: auto !important;
    }
}
</style>

<!-- ── JAVASCRIPT LOGIC ─────────────────────────────────────────────────── -->
<script>
(function($) {
    'use strict';

    var ajaxUrl = (typeof coraData !== 'undefined' && coraData.ajax_url) ? coraData.ajax_url : ((typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php');
    var nonce = (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : ((typeof coraREData !== 'undefined' && coraREData.nonce) ? coraREData.nonce : '');

    // ── 1. Top Level 01 Tab Switching ─────────────────────────────────────────
    $('.cora-main-tab-btn').on('click', function() {
        const target = $(this).data('target');
        $('.cora-main-tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.cora-main-tab-content').hide();
        $('#cora-fh-tab-' + target).show();

        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('tab', target);
            window.history.replaceState({}, '', url);
        }
    });

    // ── 2. Docked Drawer Management (Rule 15 SOP) ──────────────────────────
    function coraSyncDockedDrawerBounds(specificDrawerId) {
        var sidebarEl = document.querySelector('.cora-sidebar');
        var isMobile = window.innerWidth < 1024;
        var isSidebarCollapsed = sidebarEl && sidebarEl.classList.contains('collapsed-sidebar');

        var topbarEl = document.getElementById('cora-global-topbar');
        var actualTop = topbarEl ? Math.round(topbarEl.getBoundingClientRect().bottom) : 52;
        if (actualTop < 0 || isNaN(actualTop)) actualTop = 52;

        var targetLeft = 0;
        if (!isMobile && sidebarEl) {
            var sRect = sidebarEl.getBoundingClientRect();
            targetLeft = Math.round(sRect.right);
            if (targetLeft <= 0 || isNaN(targetLeft)) {
                targetLeft = isSidebarCollapsed ? 64 : 256;
            }
        }
        var targetWidth = window.innerWidth - targetLeft;
        var targetHeight = window.innerHeight - actualTop;

        document.documentElement.style.setProperty('--cora-main-left', targetLeft + 'px');
        document.documentElement.style.setProperty('--cora-main-top', actualTop + 'px');
        document.documentElement.style.setProperty('--cora-main-width', targetWidth + 'px');
        document.documentElement.style.setProperty('--cora-main-height', targetHeight + 'px');

        var drawers = specificDrawerId ? [document.getElementById(specificDrawerId)] : document.querySelectorAll('.cora-docked-drawer');
        drawers.forEach(function(drawer) {
            if (!drawer) return;
            if (isSidebarCollapsed) {
                drawer.classList.add('sidebar-collapsed');
            } else {
                drawer.classList.remove('sidebar-collapsed');
            }

            if (drawer.classList.contains('maximized')) {
                drawer.style.left = '0px';
                drawer.style.top = actualTop + 'px';
                drawer.style.width = '100vw';
                drawer.style.maxWidth = '100vw';
                drawer.style.height = targetHeight + 'px';
                drawer.style.maxHeight = targetHeight + 'px';
            } else {
                drawer.style.left = targetLeft + 'px';
                drawer.style.top = actualTop + 'px';
                drawer.style.width = targetWidth + 'px';
                drawer.style.maxWidth = targetWidth + 'px';
                drawer.style.height = targetHeight + 'px';
                drawer.style.maxHeight = targetHeight + 'px';
            }
        });
    }

    function coraAnimateDockedDrawerBounds(durationMs) {
        var start = Date.now();
        var duration = durationMs || 280;
        function frame() {
            coraSyncDockedDrawerBounds();
            if (Date.now() - start < duration) {
                requestAnimationFrame(frame);
            } else {
                coraSyncDockedDrawerBounds();
            }
        }
        requestAnimationFrame(frame);
    }

    window.addEventListener('resize', function() {
        coraSyncDockedDrawerBounds();
    });

    window.addEventListener('cora:sidebar-toggle', function() {
        coraAnimateDockedDrawerBounds(300);
    });

    if (window.MutationObserver) {
        var coraSidebarObs = new MutationObserver(function() {
            coraAnimateDockedDrawerBounds(250);
        });
        var sbEl = document.querySelector('.cora-sidebar');
        if (sbEl) {
            coraSidebarObs.observe(sbEl, { attributes: true, attributeFilter: ['class', 'style'] });
            sbEl.addEventListener('transitionend', function() {
                coraSyncDockedDrawerBounds();
            });
        }
        coraSidebarObs.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    }

    // Universal Docked Drawer Show (Rule 15 SOP)
    window.coraShowDockedDrawer = function(drawerId) {
        var drawer = document.getElementById(drawerId);
        var mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');
        if (!drawer) return;

        // Body Portaling: Guarantee drawer sits at document.body level with no clipping or layout offset
        if (drawer.parentNode !== document.body) {
            document.body.appendChild(drawer);
        }

        drawer.style.display = 'flex';
        drawer.classList.remove('collapsed', 'pointer-events-none');
        drawer.classList.add('open');

        // Hide mobile floating bottom bar so it never overlaps or intercepts drawer touches
        var island = document.getElementById('cora-mobile-floating-island');
        if (island) {
            island.classList.add('cora-island-hidden');
        }

        // Immediately sync bounds to sidebar edge
        coraSyncDockedDrawerBounds(drawerId);

        // Lock ONLY main content area scroll; SIDEBAR REMAINS UNLOCKED & FULLY ACCESSIBLE
        if (mainEl) {
            mainEl.classList.add('overflow-hidden');
        }
    };

    // Universal Docked Drawer Close (Rule 15 SOP)
    window.coraCloseDockedDrawer = function(drawerId) {
        var drawer = document.getElementById(drawerId);
        if (!drawer) return;

        drawer.classList.remove('open', 'maximized');
        drawer.classList.add('collapsed');

        setTimeout(function() {
            if (drawer.classList.contains('collapsed')) {
                drawer.style.display = 'none';
            }
        }, 280);

        // Restore mobile island and main scroll if all docked drawers are closed
        setTimeout(function() {
            var anyOpen = document.querySelectorAll('.cora-docked-drawer.open:not(.collapsed)').length > 0;
            if (!anyOpen) {
                var island = document.getElementById('cora-mobile-floating-island');
                if (island) {
                    island.classList.remove('cora-island-hidden');
                }
                var mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');
                if (mainEl) {
                    mainEl.classList.remove('overflow-hidden');
                }
            }
        }, 50);
    };

    // Toggle Maximize (Shopify Expand ⤢ Mode)
    window.coraToggleDrawerMaximize = function(drawerId, maxBtnId) {
        var drawer = document.getElementById(drawerId);
        var maxBtn = document.getElementById(maxBtnId);
        if (!drawer) return;

        var isMax = drawer.classList.toggle('maximized');
        if (isMax) {
            drawer.style.left = '0px';
            drawer.style.width = '100vw';
            drawer.style.maxWidth = '100vw';
            if (maxBtn) {
                maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14h6v6"></path><path d="M20 10h-6V4"></path><path d="M14 10l7-7"></path><path d="M3 21l7-7"></path></svg>';
                maxBtn.title = 'Restore Docked Canvas';
            }
        } else {
            if (maxBtn) {
                maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>';
                maxBtn.title = 'Expand / Fullscreen Canvas';
            }
            coraSyncDockedDrawerBounds(drawerId);
        }
    };

    // Level 01 Segmented Pill Tab Switchers
    window.coraSwitchPluginDrawerTab = function(tab) {
        var isVisual = tab === 'visual';
        var isManifest = tab === 'manifest';
        var isUpload = tab === 'upload';

        $('#cora-plugin-tab-panel-visual').toggle(isVisual);
        $('#cora-plugin-tab-panel-manifest').toggle(isManifest);
        $('#cora-plugin-tab-panel-upload').toggle(isUpload);

        var activeClass = 'bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs font-bold';
        var inactiveClass = 'text-zinc-600 dark:text-zinc-400 bg-transparent hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 font-medium';

        $('#cora-plugin-tab-btn-visual').removeClass(activeClass).addClass(inactiveClass);
        $('#cora-plugin-tab-btn-manifest').removeClass(activeClass).addClass(inactiveClass);
        $('#cora-plugin-tab-btn-upload').removeClass(activeClass).addClass(inactiveClass);

        if (isVisual) {
            $('#cora-plugin-tab-btn-visual').removeClass(inactiveClass).addClass(activeClass);
        } else if (isManifest) {
            $('#cora-plugin-tab-btn-manifest').removeClass(inactiveClass).addClass(activeClass);
        } else if (isUpload) {
            $('#cora-plugin-tab-btn-upload').removeClass(inactiveClass).addClass(activeClass);
        }
    };

    window.coraSwitchSkillDrawerTab = function(tab) {
        var isInst = tab === 'instructions';
        var isTriggers = tab === 'triggers';
        var isUpload = tab === 'upload';

        $('#cora-skill-tab-panel-instructions').toggle(isInst);
        $('#cora-skill-tab-panel-triggers').toggle(isTriggers);
        $('#cora-skill-tab-panel-upload').toggle(isUpload);

        var activeClass = 'bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs font-bold';
        var inactiveClass = 'text-zinc-600 dark:text-zinc-400 bg-transparent hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 font-medium';

        $('#cora-skill-tab-btn-instructions').removeClass(activeClass).addClass(inactiveClass);
        $('#cora-skill-tab-btn-triggers').removeClass(activeClass).addClass(inactiveClass);
        $('#cora-skill-tab-btn-upload').removeClass(activeClass).addClass(inactiveClass);

        if (isInst) {
            $('#cora-skill-tab-btn-instructions').removeClass(inactiveClass).addClass(activeClass);
        } else if (isTriggers) {
            $('#cora-skill-tab-btn-triggers').removeClass(inactiveClass).addClass(activeClass);
        } else if (isUpload) {
            $('#cora-skill-tab-btn-upload').removeClass(inactiveClass).addClass(activeClass);
        }
    };

    window.coraSwitchApiDrawerTab = function(tab) {
        var isKeys = tab === 'keys';
        $('#cora-api-tab-panel-keys').toggle(isKeys);
        $('#cora-api-tab-panel-clients').toggle(!isKeys);

        var activeClass = 'bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs font-bold';
        var inactiveClass = 'text-zinc-600 dark:text-zinc-400 bg-transparent hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 font-medium';

        if (isKeys) {
            $('#cora-api-tab-btn-keys').removeClass(inactiveClass).addClass(activeClass);
            $('#cora-api-tab-btn-clients').removeClass(activeClass).addClass(inactiveClass);
        } else {
            $('#cora-api-tab-btn-clients').removeClass(inactiveClass).addClass(activeClass);
            $('#cora-api-tab-btn-keys').removeClass(activeClass).addClass(inactiveClass);
        }
    };

    // Category Pill Setters
    window.coraSetPluginCategory = function(cat) {
        $('#cora-plugin-input-category').val(cat);
        $('#cora-plugin-cat-pills button').each(function() {
            var isMatch = $(this).data('cat') === cat;
            if (isMatch) {
                $(this).addClass('bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-bold shadow-2xs')
                       .removeClass('text-zinc-600 dark:text-zinc-400 bg-transparent hover:text-zinc-950 dark:hover:text-white font-medium');
            } else {
                $(this).removeClass('bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-bold shadow-2xs')
                       .addClass('text-zinc-600 dark:text-zinc-400 bg-transparent hover:text-zinc-950 dark:hover:text-white font-medium');
            }
        });
    };

    window.coraSetSkillCategory = function(cat) {
        $('#cora-skill-input-category').val(cat);
        $('#cora-skill-cat-pills button').each(function() {
            var isMatch = $(this).data('cat') === cat;
            if (isMatch) {
                $(this).addClass('bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-bold shadow-2xs')
                       .removeClass('text-zinc-600 dark:text-zinc-400 bg-transparent hover:text-zinc-950 dark:hover:text-white font-medium');
            } else {
                $(this).removeClass('bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-bold shadow-2xs')
                       .addClass('text-zinc-600 dark:text-zinc-400 bg-transparent hover:text-zinc-950 dark:hover:text-white font-medium');
            }
        });
    };

    // Concrete Drawer Helpers (Backward Compatible & Entry Points)
    window.coraOpenPluginDrawer = function(pluginData) {
        if (pluginData) {
            $('#cora-plugin-drawer-title').text('Configure Plugin: ' + (pluginData.name || ''));
            $('#cora-plugin-input-id').val(pluginData.id || '');
            $('#cora-plugin-input-name').val(pluginData.name || '');
            $('#cora-plugin-input-slug').val(pluginData.plugin_slug || '').prop('readonly', true);
            coraSetPluginCategory(pluginData.category || 'Integrations');
            $('#cora-plugin-input-desc').val(pluginData.description || '');
            $('#cora-plugin-input-config').val(pluginData.config_json || '{}');
            $('#cora-plugin-input-mcp').val(pluginData.mcp_tools_json || '[]');
            $('#cora-plugin-drawer-status-pill').text('Configuring: ' + (pluginData.plugin_slug || 'Plugin'));
        } else {
            $('#cora-plugin-drawer-title').text('Create Custom Plugin');
            $('#cora-plugin-form')[0].reset();
            $('#cora-plugin-input-id').val('');
            $('#cora-plugin-input-slug').prop('readonly', false);
            coraSetPluginCategory('Integrations');
            $('#cora-plugin-input-config').val('{}');
            $('#cora-plugin-input-mcp').val('[]');
            $('#cora-plugin-drawer-status-pill').text('Tenant Isolated Plugin');
        }
        coraSwitchPluginDrawerTab('visual');
        coraShowDockedDrawer('cora-plugin-drawer');
    };

    window.coraClosePluginDrawer = function() {
        coraCloseDockedDrawer('cora-plugin-drawer');
    };

    window.coraOpenSkillDrawer = function(skillData) {
        if (skillData) {
            $('#cora-skill-drawer-title').text('Edit Skill: ' + (skillData.name || ''));
            $('#cora-skill-input-id').val(skillData.id || '');
            $('#cora-skill-input-name').val(skillData.name || '');
            $('#cora-skill-input-slug').val(skillData.skill_slug || '').prop('readonly', true);
            coraSetSkillCategory(skillData.category || 'Client Operations');
            $('#cora-skill-input-prompt').val(skillData.instruction_prompt || '');
            $('#cora-skill-input-triggers').val(skillData.trigger_phrases || '');
            $('#cora-skill-input-tools').val(skillData.bound_mcp_tools || '[]');
            $('#cora-skill-drawer-status-pill').text('Configuring: ' + (skillData.skill_slug || 'Skill'));
        } else {
            $('#cora-skill-drawer-title').text('Define Custom AI Skill');
            $('#cora-skill-form')[0].reset();
            $('#cora-skill-input-id').val('');
            $('#cora-skill-input-slug').prop('readonly', false);
            coraSetSkillCategory('Client Operations');
            $('#cora-skill-input-tools').val('[]');
            $('#cora-skill-drawer-status-pill').text('Tenant Isolated AI Skill');
        }
        coraSwitchSkillDrawerTab('instructions');
        coraShowDockedDrawer('cora-skill-drawer');
    };

    window.coraCloseSkillDrawer = function() {
        coraCloseDockedDrawer('cora-skill-drawer');
    };

    window.coraOpenApiDrawer = function() {
        coraSwitchApiDrawerTab('keys');
        coraShowDockedDrawer('cora-api-keys-drawer');
    };

    window.coraCloseApiDrawer = function() {
        coraCloseDockedDrawer('cora-api-keys-drawer');
    };

    window.coraOpenSimulatorDrawer = function(slug, name) {
        $('#cora-simulator-title').text('Simulator: ' + name);
        $('#cora-simulator-slug').val(slug);
        $('#cora-simulator-input').val('');
        $('#cora-simulator-output-box').hide();
        coraShowDockedDrawer('cora-skill-simulator-drawer');
    };

    window.coraCloseSimulatorDrawer = function() {
        coraCloseDockedDrawer('cora-skill-simulator-drawer');
    };

    // Global Escape Key listener
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            coraClosePluginDrawer();
            coraCloseSkillDrawer();
            coraCloseApiDrawer();
            coraCloseSimulatorDrawer();
            if (typeof coraCloseRecSheet === 'function') coraCloseRecSheet();
        }
    });

    // ── 3. Plugins Actions ────────────────────────────────────────────────────
    $('#cora-open-plugin-upload-btn').on('click', function() {
        coraOpenPluginDrawer(null);
        coraSwitchPluginDrawerTab('upload');
    });

    $('#cora-open-plugin-builder-btn').on('click', function() {
        coraOpenPluginDrawer(null);
        coraSwitchPluginDrawerTab('visual');
    });

    var selectedPluginFile = null;
    $('#cora-plugin-dropzone').on('click', function(e) {
        if (e.target !== document.getElementById('cora-plugin-file-upload-input')) {
            $('#cora-plugin-file-upload-input').trigger('click');
        }
    });

    $('#cora-plugin-dropzone').on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('border-zinc-950 dark:border-zinc-100 bg-zinc-100/80 dark:bg-zinc-900/80');
    }).on('dragleave dragend drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('border-zinc-950 dark:border-zinc-100 bg-zinc-100/80 dark:bg-zinc-900/80');
    });

    $('#cora-plugin-dropzone').on('drop', function(e) {
        var files = e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : null;
        if (files && files.length > 0) {
            handlePluginFile(files[0]);
        }
    });

    $('#cora-plugin-file-upload-input').on('change', function() {
        if (this.files && this.files.length > 0) {
            handlePluginFile(this.files[0]);
        }
    });

    function handlePluginFile(file) {
        selectedPluginFile = file;
        $('#cora-plugin-dropzone-label').text('Selected: ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB)');
        var reader = new FileReader();
        reader.onload = function(evt) {
            $('#cora-plugin-paste-manifest').val(evt.target.result);
        };
        reader.readAsText(file);
    }

    $('#cora-submit-upload-plugin-btn').on('click', function() {
        var pastedManifest = $('#cora-plugin-paste-manifest').val().trim();
        if (!selectedPluginFile && !pastedManifest) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Please select a JSON manifest file or paste manifest content.', 'error');
            }
            return;
        }

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html('<span>Installing into workspace...</span>');

        var formData = new FormData();
        formData.append('action', 'cora_ajax_upload_workspace_plugin');
        formData.append('security', nonce);
        formData.append('nonce', nonce);

        if (selectedPluginFile) {
            formData.append('plugin_file', selectedPluginFile);
        } else {
            formData.append('plugin_manifest', pastedManifest);
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                btn.prop('disabled', false).html(originalText);
                if (resp && resp.success) {
                    coraClosePluginDrawer();
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast('Plugin installed successfully into workspace!', 'success');
                    }
                    setTimeout(function() { window.location.reload(); }, 600);
                } else {
                    var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Failed to upload plugin.';
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast(msg, 'error');
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalText);
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast('Network error while uploading plugin.', 'error');
                }
            }
        });
    });

    $('#cora-open-api-keys-btn').on('click', function() {
        coraOpenApiDrawer();
    });

    // Configure Plugin
    $(document).on('click', '.cora-configure-plugin-btn', function() {
        const slug = $(this).data('slug');
        const card = $(this).closest('.cora-plugin-card');
        const data = {
            id: $(this).data('id'),
            plugin_slug: slug,
            name: card.find('.cora-plugin-name').text(),
            description: card.find('.cora-plugin-desc').text(),
            category: card.data('category'),
            config_json: '{}',
            mcp_tools_json: '[]'
        };
        coraOpenPluginDrawer(data);
    });

    // Toggle Plugin Status
    $(document).on('change', '.cora-plugin-toggle', function() {
        const slug = $(this).data('slug');
        const isChecked = this.checked;
        const status = isChecked ? 'active' : 'inactive';
        const card = $(this).closest('.cora-plugin-card');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_ajax_toggle_workspace_plugin',
                security: nonce,
                nonce: nonce,
                plugin_slug: slug,
                status: status
            },
            success: function(resp) {
                if (resp && resp.success) {
                    card.toggleClass('is-active', isChecked).toggleClass('is-inactive', !isChecked);
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast(`Plugin '${slug}' set to ${status}.`, 'success');
                    }
                }
            }
        });
    });

    // Save Plugin Form
    $('#cora-plugin-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const data = form.serialize() + '&action=cora_ajax_save_workspace_plugin&security=' + nonce + '&nonce=' + nonce;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: data,
            success: function(resp) {
                if (resp && resp.success) {
                    coraClosePluginDrawer();
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast('Plugin saved successfully. Updating workspace...', 'success');
                    }
                    setTimeout(function() { window.location.reload(); }, 600);
                }
            }
        });
    });

    // Delete Plugin
    $(document).on('click', '.cora-delete-plugin-btn', function() {
        const slug = $(this).data('slug');
        const card = $(this).closest('.cora-plugin-card');
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast(`Deleting plugin '${slug}'...`, 'info');
        }
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_ajax_delete_workspace_plugin',
                security: nonce,
                nonce: nonce,
                plugin_slug: slug
            },
            success: function(resp) {
                if (resp && resp.success) {
                    card.fadeOut(200, function() { $(this).remove(); });
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast(`Plugin '${slug}' removed.`, 'success');
                    }
                }
            }
        });
    });

    // AI Plugin Generator Trigger
    $('#cora-open-plugin-ai-gen-btn').on('click', function() {
        coraOpenPluginDrawer(null);
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Plugin Studio opened. Define identity & manifest.', 'info');
        }
    });

    // ── 4. Skills Actions ─────────────────────────────────────────────────────
    $('#cora-open-skill-upload-btn').on('click', function() {
        coraOpenSkillDrawer(null);
        coraSwitchSkillDrawerTab('upload');
    });

    $('#cora-open-skill-builder-btn').on('click', function() {
        coraOpenSkillDrawer(null);
        coraSwitchSkillDrawerTab('instructions');
    });

    var selectedSkillFile = null;
    $('#cora-skill-dropzone').on('click', function(e) {
        if (e.target !== document.getElementById('cora-skill-file-upload-input')) {
            $('#cora-skill-file-upload-input').trigger('click');
        }
    });

    $('#cora-skill-dropzone').on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('border-zinc-950 dark:border-zinc-100 bg-zinc-100/80 dark:bg-zinc-900/80');
    }).on('dragleave dragend drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('border-zinc-950 dark:border-zinc-100 bg-zinc-100/80 dark:bg-zinc-900/80');
    });

    $('#cora-skill-dropzone').on('drop', function(e) {
        var files = e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : null;
        if (files && files.length > 0) {
            handleSkillFile(files[0]);
        }
    });

    $('#cora-skill-file-upload-input').on('change', function() {
        if (this.files && this.files.length > 0) {
            handleSkillFile(this.files[0]);
        }
    });

    function handleSkillFile(file) {
        selectedSkillFile = file;
        $('#cora-skill-dropzone-label').text('Selected: ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB)');
        var reader = new FileReader();
        reader.onload = function(evt) {
            $('#cora-skill-paste-spec').val(evt.target.result);
        };
        reader.readAsText(file);
    }

    $('#cora-submit-upload-skill-btn').on('click', function() {
        var pastedSpec = $('#cora-skill-paste-spec').val().trim();
        if (!selectedSkillFile && !pastedSpec) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Please select a skill file (.md / .json) or paste skill definition.', 'error');
            }
            return;
        }

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html('<span>Registering in workspace...</span>');

        var formData = new FormData();
        formData.append('action', 'cora_ajax_upload_workspace_skill');
        formData.append('security', nonce);
        formData.append('nonce', nonce);

        if (selectedSkillFile) {
            formData.append('skill_file', selectedSkillFile);
        } else {
            formData.append('skill_definition', pastedSpec);
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                btn.prop('disabled', false).html(originalText);
                if (resp && resp.success) {
                    coraCloseSkillDrawer();
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast('Skill registered successfully into workspace!', 'success');
                    }
                    setTimeout(function() { window.location.reload(); }, 600);
                } else {
                    var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Failed to upload skill.';
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast(msg, 'error');
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalText);
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast('Network error while uploading skill.', 'error');
                }
            }
        });
    });

    // Save Skill Form
    $('#cora-skill-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const data = form.serialize() + '&action=cora_ajax_save_workspace_skill&security=' + nonce + '&nonce=' + nonce;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: data,
            success: function(resp) {
                if (resp && resp.success) {
                    coraCloseSkillDrawer();
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast('AI Skill saved successfully. Updating workspace...', 'success');
                    }
                    setTimeout(function() { window.location.reload(); }, 600);
                }
            }
        });
    });

    // Delete Skill
    $(document).on('click', '.cora-delete-skill-btn', function() {
        const slug = $(this).data('slug');
        const card = $(this).closest('.cora-skill-card');
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast(`Deleting skill '${slug}'...`, 'info');
        }
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_ajax_delete_workspace_skill',
                security: nonce,
                nonce: nonce,
                skill_slug: slug
            },
            success: function(resp) {
                if (resp && resp.success) {
                    card.fadeOut(200, function() { $(this).remove(); });
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast(`Skill '${slug}' removed.`, 'success');
                    }
                }
            }
        });
    });

    // Toggle Skill Status
    $(document).on('change', '.cora-skill-toggle', function() {
        const slug = $(this).data('slug');
        const isChecked = this.checked;
        const status = isChecked ? 'active' : 'inactive';
        const card = $(this).closest('.cora-skill-card');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_ajax_toggle_workspace_skill',
                security: nonce,
                nonce: nonce,
                skill_slug: slug,
                status: status
            },
            success: function(resp) {
                if (resp && resp.success) {
                    card.toggleClass('is-active', isChecked).toggleClass('is-inactive', !isChecked);
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast(`Skill '${slug}' set to ${status}.`, 'success');
                    }
                }
            }
        });
    });

    // Run Simulator Click
    $(document).on('click', '.cora-run-skill-simulator-btn', function() {
        const slug = $(this).data('slug');
        const name = $(this).data('name');
        coraOpenSimulatorDrawer(slug, name);
    });

    // Execute Simulator
    $('#cora-run-simulation-btn').on('click', function() {
        const slug = $('#cora-simulator-slug').val();
        const inputVal = $('#cora-simulator-input').val();
        const btn = $(this);
        btn.prop('disabled', true).text('Executing...');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_ajax_execute_workspace_skill',
                security: nonce,
                nonce: nonce,
                skill_slug: slug,
                parameters: inputVal
            },
            success: function(resp) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg><span>Execute Skill</span>');
                if (resp && resp.success) {
                    $('#cora-simulator-output-box').show();
                    $('#cora-simulator-time-pill').text((resp.data.duration_ms || 0) + 'ms');
                    $('#cora-simulator-result').text(resp.data.output || JSON.stringify(resp.data, null, 2));
                } else {
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast((resp && resp.data && resp.data.message) ? resp.data.message : 'Execution error.', 'error');
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<span>Execute Skill</span>');
            }
        });
    });

    // AI Skill Generator Trigger
    $('#cora-open-skill-ai-gen-btn').on('click', function() {
        coraOpenSkillDrawer(null);
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Skill Studio opened. Define system instructions & triggers.', 'info');
        }
    });

    // ── 5. API Keys Actions ───────────────────────────────────────────────────
    $('#cora-create-key-submit').on('click', function() {
        const name = $('#cora-new-key-name').val() || 'Workspace API Key';
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_ajax_create_workspace_api_key',
                security: nonce,
                nonce: nonce,
                key_name: name
            },
            success: function(resp) {
                if (resp && resp.success && resp.data.api_key) {
                    $('#cora-generated-key-box').show();
                    $('#cora-secret-key-display').val(resp.data.api_key);
                    $('#cora-new-key-name').val('');
                }
            }
        });
    });

    $(document).on('click', '.cora-revoke-key-btn', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Revoking API key...', 'info');
        }
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_ajax_revoke_workspace_api_key',
                security: nonce,
                nonce: nonce,
                key_id: id
            },
            success: function(resp) {
                if (resp && resp.success) {
                    row.find('td:nth-child(3)').html('<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10.5px] font-medium bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span><span>Revoked</span></span>');
                    row.find('.cora-revoke-key-btn').remove();
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast('API Key revoked successfully.', 'success');
                    }
                }
            }
        });
    });

    // ── 6. Filter Pills & Search ──────────────────────────────────────────────
    $('.cora-plugin-cat-pills .cora-filter-pill').on('click', function() {
        $('.cora-plugin-cat-pills .cora-filter-pill').removeClass('active');
        $(this).addClass('active');
        const cat = $(this).data('filter');
        $('.cora-plugin-card').each(function() {
            if (cat === 'all' || $(this).data('category') === cat) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('#cora-plugins-search-input').on('input', function() {
        const q = $(this).val().toLowerCase().trim();
        $('.cora-plugin-card').each(function() {
            const name = $(this).find('.cora-plugin-name').text().toLowerCase();
            const desc = $(this).find('.cora-plugin-desc').text().toLowerCase();
            if (!q || name.indexOf(q) !== -1 || desc.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('.cora-skill-cat-pills .cora-filter-pill').on('click', function() {
        $('.cora-skill-cat-pills .cora-filter-pill').removeClass('active');
        $(this).addClass('active');
        const cat = $(this).data('filter');
        $('.cora-skill-card').each(function() {
            if (cat === 'all' || $(this).data('category') === cat) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('#cora-skills-search-input').on('input', function() {
        const q = $(this).val().toLowerCase().trim();
        $('.cora-skill-card').each(function() {
            const name = $(this).find('.cora-skill-name').text().toLowerCase();
            const desc = $(this).find('.cora-skill-desc').text().toLowerCase();
            if (!q || name.indexOf(q) !== -1 || desc.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // ── 7. Core Modules Logic (Retained & Synchronized) ────────────────────────
    let initialChecked = getCheckedSlugs();

    function getCheckedSlugs() {
        return $('#cora-custom-features-form input[name="features[]"]:checked').map(function() {
            return this.value;
        }).get();
    }

    function updateUIState() {
        const currentChecked = getCheckedSlugs();
        $('#cora-fh-active-count').text(currentChecked.length);

        const isDirty = JSON.stringify(currentChecked.sort()) !== JSON.stringify(initialChecked.slice().sort());
        if (isDirty) {
            $('#cora-fh-unsaved-pill').show();
            $('#cora-fh-floating-bar').css('display', 'flex');
        } else {
            $('#cora-fh-unsaved-pill').hide();
            $('#cora-fh-floating-bar').hide();
        }

        $('#cora-custom-features-form .cora-feature-card').each(function() {
            const cb = $(this).find('.cora-feature-checkbox');
            const isActive = cb.prop('checked');
            $(this).toggleClass('is-active', isActive).toggleClass('is-inactive', !isActive);
            $(this).find('.cora-feature-icon-wrap').css({
                'background': isActive ? '#09090b' : '#f4f4f5',
                'color': isActive ? '#ffffff' : '#71717a'
            });
            $(this).find('.cora-feature-badge').css('display', isActive ? 'inline-block' : 'none');
        });
    }

    $(document).on('change', '.cora-feature-checkbox:not(.is-foundation-cb)', function() {
        updateUIState();
    });

    $('#cora-fh-select-all').on('click', function() {
        $('#cora-custom-features-form input[name="features[]"]:not(:disabled)').prop('checked', true);
        updateUIState();
    });

    $('#cora-fh-deselect-all').on('click', function() {
        $('#cora-custom-features-form input[name="features[]"]:not(:disabled)').prop('checked', false);
        updateUIState();
    });

    $('#cora-fh-reset-defaults').on('click', function() {
        const defaults = ['blogs', 'forms', 'team-roles', 'media', 'vault', 'financials', 'calendar', 'activity-timeline', 'automations', 'leads', 'crew_scheduler', 'equipment', 'tasks', 'canvas', 'emails', 'projects', 'mcp', 'knowledge-base'];
        $('#cora-custom-features-form input[name="features[]"]:not(:disabled)').each(function() {
            $(this).prop('checked', defaults.indexOf(this.value) !== -1);
        });
        updateUIState();
    });

    $('#cora-fh-discard-btn').on('click', function() {
        $('#cora-custom-features-form input[name="features[]"]:not(:disabled)').each(function() {
            $(this).prop('checked', initialChecked.indexOf(this.value) !== -1);
        });
        updateUIState();
    });

    // Modules Category Filtering
    $('.cora-fh-cat-filter').on('click', function() {
        $('.cora-fh-cat-filter').removeClass('active');
        $(this).addClass('active');
        const cat = $(this).data('cat');

        if (cat === 'all') {
            $('.cora-fh-category-block').show();
        } else {
            $('.cora-fh-category-block').each(function() {
                if ($(this).data('cat-slug') === cat) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Modules Search
    $('#cora-fh-search-input').on('input', function() {
        const q = $(this).val().toLowerCase().trim();
        $('#cora-fh-clear-search-icon').toggle(!!q);

        let visibleCount = 0;
        $('#cora-custom-features-form .cora-feature-card').each(function() {
            const title = $(this).find('.cora-feature-title').text().toLowerCase();
            const desc = $(this).find('.cora-feature-desc').text().toLowerCase();
            if (!q || title.indexOf(q) !== -1 || desc.indexOf(q) !== -1) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        $('.cora-fh-category-block').each(function() {
            const hasVisible = $(this).find('.cora-feature-card:visible').length > 0;
            $(this).toggle(hasVisible);
        });

        $('#cora-fh-no-results').toggle(visibleCount === 0);
    });

    $('#cora-fh-clear-search-icon, #cora-fh-clear-search-btn').on('click', function() {
        $('#cora-fh-search-input').val('').trigger('input');
    });

    // Modules Save Workflow
    $('.cora-fh-save-btn').on('click', function() {
        const btn = $(this);
        const features = getCheckedSlugs();
        const origText = btn.find('.cora-save-text').text();

        btn.prop('disabled', true).find('.cora-save-text').text('Saving...');

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
                        window.coraShowToast('Workspace modules updated successfully. Refreshing navigation...', 'success');
                    }
                    setTimeout(function() { window.location.reload(); }, 600);
                }
            },
            error: function() {
                btn.prop('disabled', false).find('.cora-save-text').text(origText);
            }
        });
    });

    // Recommendation logic
    const moduleDependencies = {
        'forms': {
            name: 'Forms & Intake',
            recommended: [
                { slug: 'leads', name: 'Leads CRM Pipeline', reason: 'Convert incoming client form responses automatically into pipeline deals' },
                { slug: 'emails', name: 'Emails & Broadcasts', reason: 'Dispatch instant confirmation emails upon form submission' }
            ]
        },
        'leads': {
            name: 'Leads CRM Pipeline',
            recommended: [
                { slug: 'financials', name: 'Financials & Invoicing', reason: 'Generate milestone invoices directly from won CRM leads' },
                { slug: 'calendar', name: 'Consolidated Calendar', reason: 'Schedule client meetings and shoot bookings from lead stages' }
            ]
        }
    };
    let pendingRecommendedSlugs = [];

    window.checkModuleDependencies = function(slug, isChecked) {
        if (!isChecked || !moduleDependencies[slug]) return;
        const dep = moduleDependencies[slug];
        const currentChecked = getCheckedSlugs();
        const unactivated = dep.recommended.filter(function(r) { return currentChecked.indexOf(r.slug) === -1; });
        if (unactivated.length === 0) return;

        pendingRecommendedSlugs = unactivated.map(function(r) { return r.slug; });
        $('#cora-fh-rec-module-name').text(dep.name);
        let listHtml = '';
        unactivated.forEach(function(r) {
            listHtml += `
                <div style="padding: 10px 12px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 10px; display: flex; align-items: flex-start; gap: 10px;">
                    <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #22c55e; margin-top: 5px; flex-shrink: 0;"></span>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 12px; font-weight: 700; color: #18181b; line-height: 1.25;">${r.name}</div>
                        <div style="font-size: 11px; color: #71717a; margin-top: 2px; line-height: 1.35;">${r.reason}</div>
                    </div>
                </div>
            `;
        });
        $('#cora-fh-rec-list').html(listHtml);
        $('#cora-fh-rec-btn-text').text(`Activate All (${unactivated.length})`);
        $('#cora-fh-recommendation-sheet').css('display', 'flex').removeClass('pointer-events-none');
        $('#cora-fh-rec-backdrop').css('opacity', '1');
        $('#cora-fh-rec-drawer').css('transform', 'translate(-50%, 0)');
    };

    window.coraCloseRecSheet = function() {
        $('#cora-fh-rec-drawer').css('transform', 'translate(-50%, 100%)');
        $('#cora-fh-rec-backdrop').css('opacity', '0');
        $('#cora-fh-recommendation-sheet').addClass('pointer-events-none');
        setTimeout(function() { $('#cora-fh-recommendation-sheet').css('display', 'none'); }, 300);
    };

    $('#cora-fh-rec-activate-btn').on('click', function() {
        pendingRecommendedSlugs.forEach(function(slug) {
            $(`#cora-custom-features-form input[name="features[]"][value="${slug}"]`).prop('checked', true);
        });
        coraCloseRecSheet();
        updateUIState();
    });

    // Portal floating elements to body
    $(function() {
        if ($('#cora-fh-floating-bar').length && $('#cora-fh-floating-bar').parent()[0] !== document.body) {
            $('body').append($('#cora-fh-floating-bar'));
        }
        if ($('#cora-fh-recommendation-sheet').length && $('#cora-fh-recommendation-sheet').parent()[0] !== document.body) {
            $('body').append($('#cora-fh-recommendation-sheet'));
        }
    });

    updateUIState();

})(jQuery);
</script>
