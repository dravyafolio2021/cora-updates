<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── 1. Table Creation and Schema ──────────────────────────────────────────

if ( ! function_exists( 'cora_create_docs_tables' ) ) {
function cora_create_docs_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    $t_versions = $wpdb->prefix . 'cora_docs_versions';
    $t_changelog = $wpdb->prefix . 'cora_docs_changelog';
    $t_api = $wpdb->prefix . 'cora_docs_api_endpoints';

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Table 1: Documentation Pages
    $sql_pages = "CREATE TABLE {$t_pages} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        slug varchar(255) NOT NULL,
        title varchar(255) NOT NULL,
        content longtext DEFAULT NULL,
        category varchar(50) NOT NULL,
        module_key varchar(50) DEFAULT NULL,
        status varchar(50) NOT NULL DEFAULT 'draft',
        created_by bigint(20) unsigned NOT NULL,
        updated_by bigint(20) unsigned NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY slug (slug)
    ) $charset_collate;";

    // Table 2: Version History
    $sql_versions = "CREATE TABLE {$t_versions} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        page_id bigint(20) unsigned NOT NULL,
        content longtext DEFAULT NULL,
        version_label varchar(50) NOT NULL,
        created_by bigint(20) unsigned NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY page_id (page_id)
    ) $charset_collate;";

    // Table 3: Changelog
    $sql_changelog = "CREATE TABLE {$t_changelog} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        module_key varchar(50) DEFAULT NULL,
        version varchar(50) NOT NULL,
        status varchar(50) NOT NULL DEFAULT 'released',
        title varchar(255) NOT NULL,
        description longtext DEFAULT NULL,
        ticket_id varchar(100) DEFAULT NULL,
        author_id bigint(20) unsigned NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Table 4: API Endpoints Reference
    $sql_api = "CREATE TABLE {$t_api} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        method varchar(15) NOT NULL,
        path varchar(255) NOT NULL,
        description text DEFAULT NULL,
        required_permissions varchar(255) DEFAULT NULL,
        request_schema longtext DEFAULT NULL,
        response_schema longtext DEFAULT NULL,
        example longtext DEFAULT NULL,
        permission_level varchar(50) DEFAULT 'admin',
        mcp_compatible tinyint(1) DEFAULT 0,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY method_path (method,path)
    ) $charset_collate;";

    dbDelta( $sql_pages );
    dbDelta( $sql_versions );
    dbDelta( $sql_changelog );
    dbDelta( $sql_api );

    // Seed default data if pages table is empty
    cora_seed_docs_data();
}
}
add_action( 'init', 'cora_create_docs_tables' );

// ── 2. Data Seeding ───────────────────────────────────────────────────────

if ( ! function_exists( 'cora_seed_docs_data' ) ) {
function cora_seed_docs_data() {
    global $wpdb;
    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    $t_versions = $wpdb->prefix . 'cora_docs_versions';
    $t_changelog = $wpdb->prefix . 'cora_docs_changelog';
    $t_api = $wpdb->prefix . 'cora_docs_api_endpoints';

    $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$t_pages}");
    if ( $count > 0 ) {
        return; // Already seeded
    }

    $author_id = 1; // Default Admin user ID

    // Seed 1: Platform Overview Page
    $wpdb->insert( $t_pages, array(
        'slug' => 'platform-overview',
        'title' => 'Platform Overview',
        'category' => 'overview',
        'content' => "# Platform Overview\n\nCora is a modular SaaS platform built for Indian service businesses (salons, dental clinics, gyms, restaurants). It leverages WordPress as a foundational backend and wraps it in a modern, lightweight, isolated multi-tenant architecture designed to scale to the first 100 enterprise customers.\n\n## Core Features\n- **Decoupled Architecture**: Isolation of workspaces and databases.\n- **Universal Auto-Save**: Background debounce auto-save utilizing WordPress transient filters.\n- **MCP Ready**: Seamless connection of external LLM tools with inherited role permissions.\n- **Native AI RAG Layer**: Localized knowledge base synchronization per tenant workspace.\n\n## Tech Stack\n- **Backend**: WordPress 6.x foundation\n- **Client UI**: Vanilla CSS & HTML, Tailwind CSS overlays, and React custom integrations\n- **AI Infrastructure**: Gemini 3.5 Flash and Claude 3.5 Sonnet RAG context agents\n- **MCP Gateway**: Secure JSON-RPC over WebSockets connection portal",
        'status' => 'draft',
        'created_by' => $author_id,
        'updated_by' => $author_id,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    ));
    $page_id = $wpdb->insert_id;
    $wpdb->insert( $t_versions, array(
        'page_id' => $page_id,
        'content' => "# Platform Overview\n\nCora is a modular SaaS platform built for Indian service businesses (salons, dental clinics, gyms, restaurants)...",
        'version_label' => 'v1.0.0',
        'created_by' => $author_id,
        'created_at' => current_time('mysql')
    ));

    // Seed 2: Workspace Config Guide Page
    $wpdb->insert( $t_pages, array(
        'slug' => 'workspace-configuration',
        'title' => 'Workspace Configuration Guide',
        'category' => 'guides',
        'content' => "# Workspace Configuration Guide\n\nLearn how to combine individual modules to create custom industry-scoped tenant workspaces.\n\n## Pre-built Industry Templates\n- **Salon Workspace**: Bundles User Management, Forms & Reviews, Media Module, and WhatsApp Automations.\n- **Dental Clinic Workspace**: Bundles User Management, Client Scheduling, Document Studio Vault, and E-Sign registry.\n- **Content Agency Workspace**: Bundles User Management, CRM Lead Funnels, AI Content Suite, and SMTP outbound drip queues.\n\n## Combination Logic\nWorkspaces are configured at provisioning time. Modules are loaded dynamically based on active metadata query flags (e.g. `?industry=photography_studio`).\n\n## AI Quota Tiers\n- **Beta**: 500 AI RAG tokens / month\n- **Starter**: 10,000 AI RAG tokens / month\n- **Pro**: 100,000 AI RAG tokens / month\n- **Enterprise**: Custom unmetered limits",
        'status' => 'draft',
        'created_by' => $author_id,
        'updated_by' => $author_id,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    ));
    $page_id2 = $wpdb->insert_id;
    $wpdb->insert( $t_versions, array(
        'page_id' => $page_id2,
        'content' => "# Workspace Configuration Guide...",
        'version_label' => 'v1.0.0',
        'created_by' => $author_id,
        'created_at' => current_time('mysql')
    ));

    // Seed 3: Roadmap Page
    $wpdb->insert( $t_pages, array(
        'slug' => 'roadmap',
        'title' => 'Roadmap',
        'category' => 'roadmap',
        'content' => "# Roadmap & Milestones\n\nTrack the implementation phases and module development updates across Cora Platform.\n\n## Platform Milestones\n### Phase 1: Core Platform Foundation\n- **Status**: SHIPPED\n- **Items**: Core DB architecture, JWT authentication, and tenant workspace routing.\n\n### Phase 2: AI Integration & Leads Funnel\n- **Status**: IN PROGRESS\n- **Items**: Dynamic Kanban lead board, debounced AI auto-save engine, and Google Business profile sync.\n\n### Phase 3: Document Vault & E-Sign\n- **Status**: NEXT\n- **Items**: 5-step Document Studio Wizard, GST verification API, and watermarked PDF renderer.\n\n### Phase 4: Decentralized Decoupling\n- **Status**: BACKLOG\n- **Items**: Migrating core schemas into standalone microservices, WebSocket RPC proxies.",
        'status' => 'draft',
        'created_by' => $author_id,
        'updated_by' => $author_id,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    ));

    // Seed 4: Module 01 User Management Documentation Page
    $wpdb->insert( $t_pages, array(
        'slug' => 'user-management-and-auth',
        'title' => 'Module 01: User Management & Auth',
        'category' => 'modules',
        'module_key' => 'user-management',
        'content' => "# Module 01: User Management & Auth\n\n## Overview\nThis module authenticates platform users and enforces role-based permissions across standard and custom roles (Super Owner, Owner, Administrator, Member).\n\n## Features List\n- **Secure Authenticator**: Password hashing with login rate-limiting (DONE).\n- **Magic Links**: Passwordless login sequences (IN PROGRESS).\n- **Dual Theme Support**: Persisting dark/light UI preference across workspace navigation (DONE).\n\n## Mobile-First Optimization Layout Rules\n- **Sub-navigation Tab**: Mobile tab navigation dropdown selection triggers are borderless and clean. Parent layout is padding-free (pb-0) to align the active border indicators flush with the horizontal baseline.\n- **Accordion Permissions Matrix**: On mobile, Permissions are displayed in a clean, vertical card list with nested accordion folders per capability category.\n- **Horizontal Edge Boundaries**: Outer containers of sub-menus and mobile headers must use px-0 layout alignments so content sits flush on narrow viewport boundaries.\n\n## Role & Permission Reference\n- `cora_super_admin`: Full admin access across all workspace environments.\n- `administrator`: Full tenant dashboard read/write rights.\n- `cora_member`: Scoped workspace feature execution (no settings or backups).\n\n## AI Layer Integration\nMonitors authentication logs in real-time, detecting access anomalies and generating notification summaries on security triggers.",
        'status' => 'draft',
        'created_by' => $author_id,
        'updated_by' => $author_id,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    ));

    // Seed Changelog Entries
    $wpdb->insert( $t_changelog, array(
        'module_key' => null,
        'version' => '2.2.1',
        'status' => 'released',
        'title' => 'Platform Navigation Shift',
        'description' => 'Shifted Emails navigation item into the unified Sales Channel menu grouping inside sidebar container to streamline CRM layouts.',
        'ticket_id' => 'CORA-882',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.26',
        'status' => 'released',
        'title' => 'Desktop Header Refined & AI Stack Restored',
        'description' => 'Removed the page title user icon container in the desktop view, aligning the "User Management" title cleanly with the left edge of the page. Restored the overlapping AI brand platform stack next to the "Invite User" desktop button wrapper.',
        'ticket_id' => 'CORA-916',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.25',
        'status' => 'released',
        'title' => 'Mobile AI Shortcuts Stack Integrated',
        'description' => 'Added the compact overlapping AI brand stack shortcuts next to the Invite button in the mobile header, keeping the interface visual system unified without crowding the text fields or breaking the grid layout.',
        'ticket_id' => 'CORA-915',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.24',
        'status' => 'released',
        'title' => 'Mobile Header Padding Optimization',
        'description' => 'Removed horizontal edge padding (px-4 to px-0) from the mobile view header container to ensure content aligns perfectly flush with page margins.',
        'ticket_id' => 'CORA-914',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.23',
        'status' => 'released',
        'title' => 'Mobile Tabs Padding Optimization',
        'description' => 'Removed horizontal edge padding (px-4 to px-0) from the mobile sub-navigation tabs container, ensuring full-width layout boundaries on narrow viewports.',
        'ticket_id' => 'CORA-913',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.22',
        'status' => 'released',
        'title' => 'Mobile Tabs Baseline Alignment',
        'description' => 'Fixed vertical misalignment of mobile tabs by removing the bottom-padding helper pb-2, allowing the active bottom accent border to sit flush on the horizontal menu divider.',
        'ticket_id' => 'CORA-912',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.21',
        'status' => 'released',
        'title' => 'Mobile Tabs Outline & Background Removal',
        'description' => 'Completely removed the heavy box outline borders and grey background from the mobile sub-navigation dropdown button, rendering it fully borderless.',
        'ticket_id' => 'CORA-911',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.20',
        'status' => 'released',
        'title' => 'Attendance Logs 2-Column Mobile Grid',
        'description' => 'Optimized mobile analytics cards by converting the 4-cards stack into a 2x2 grid (grid-cols-2) on mobile, preserving screen real estate.',
        'ticket_id' => 'CORA-910',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.18',
        'status' => 'released',
        'title' => 'Attendance Logs Mobile Redesign',
        'description' => 'Redesigned Attendance Logs stat cards and overhauled the mobile punch list into premium card layouts with navigation chevron indicators.',
        'ticket_id' => 'CORA-908',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.17',
        'status' => 'released',
        'title' => 'Attendance Logs Cards Uniformity',
        'description' => 'Standardized attendance cards with consistent border weights, box shadow elevations, and strict minimum height bounds.',
        'ticket_id' => 'CORA-907',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '3.2.15',
        'status' => 'released',
        'title' => 'Permissions Matrix Mobile Redesign',
        'description' => 'Re-engineered the mobile Permissions Matrix tab into a premium accordion-based card layout with collapsible category rows and custom SVG status indicators.',
        'ticket_id' => 'CORA-905',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_changelog, array(
        'module_key' => 'user-management',
        'version' => '1.0.0',
        'status' => 'released',
        'title' => 'Initial Module Release',
        'description' => 'Completed User Management & Auth module base layer, including password recovery, login logs, and session handlers.',
        'ticket_id' => 'CORA-010',
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));

    // Seed API Endpoints
    $wpdb->insert( $t_api, array(
        'method' => 'POST',
        'path' => '/api/v1/auth/login',
        'description' => 'Authenticates a user using username and password, returning active JWT credentials.',
        'required_permissions' => 'public',
        'request_schema' => "{\n  \"username\": \"string\",\n  \"password\": \"string\"\n}",
        'response_schema' => "{\n  \"token\": \"string\",\n  \"expires_in\": \"int\"\n}",
        'example' => "Request:\nPOST /api/v1/auth/login\n{\n  \"username\": \"admin\",\n  \"password\": \"pass\"\n}\n\nResponse:\n200 OK\n{\n  \"token\": \"jwt_abc123\",\n  \"expires_in\": 3600\n}",
        'permission_level' => 'public',
        'mcp_compatible' => 1,
        'updated_at' => current_time('mysql')
    ));

    $wpdb->insert( $t_api, array(
        'method' => 'GET',
        'path' => '/api/v1/workspaces',
        'description' => 'Returns a list of all active workspaces mapped to the logged-in administrator.',
        'required_permissions' => 'read_workspaces',
        'request_schema' => '{}',
        'response_schema' => "[\n  {\n    \"id\": \"int\",\n    \"name\": \"string\",\n    \"slug\": \"string\",\n    \"plan\": \"string\"\n  }\n]",
        'example' => "Request:\nGET /api/v1/workspaces\n\nResponse:\n200 OK\n[\n  {\n    \"id\": 1,\n    \"name\": \"Apex Realty Group\",\n    \"slug\": \"apex-realty\",\n    \"plan\": \"enterprise\"\n  }\n]",
        'permission_level' => 'owner',
        'mcp_compatible' => 1,
        'updated_at' => current_time('mysql')
    ));
}
}

// ── 3. Simple Markdown Parser ─────────────────────────────────────────────

if ( ! function_exists( 'cora_markdown_to_html' ) ) {
function cora_markdown_to_html( $markdown ) {
    if ( empty( $markdown ) ) return '';
    
    // Safely encode characters to prevent issues
    $html = esc_html( $markdown );
    
    // Decode basic tags and markdown formatters
    $html = preg_replace('/^\#\#\# (.*?)$/m', '<h3 class="text-sm font-bold text-zinc-950 mt-6 mb-2 flex items-center gap-1">$1</h3>', $html);
    $html = preg_replace('/^\#\# (.*?)$/m', '<h2 class="text-base font-bold text-zinc-950 border-b border-zinc-200/80 pb-1 mt-7 mb-3 flex items-center gap-1.5">$1</h2>', $html);
    $html = preg_replace('/^\# (.*?)$/m', '<h1 class="text-xl font-extrabold text-zinc-950 tracking-tight mt-4 mb-4 flex items-center gap-2">$1</h1>', $html);
    
    // Strong & Emphasis
    $html = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-bold text-zinc-900">$1</strong>', $html);
    $html = preg_replace('/\*(.*?)\*/', '<em class="italic text-zinc-800">$1</em>', $html);
    
    // Code Blocks
    $html = preg_replace('/\`\`\`(.*?)\n([\s\S]*?)\`\`\`/m', '<pre class="bg-zinc-955 text-zinc-50 p-4 rounded-xl font-mono text-[11px] overflow-x-auto my-4 border border-zinc-900">$2</pre>', $html);
    $html = preg_replace('/\`(.*?)\`/', '<code class="bg-zinc-100 border border-zinc-200/60 text-zinc-800 px-1.5 py-0.5 rounded font-mono text-[10.5px]">$1</code>', $html);
    
    // GitHub block style alerts
    $html = preg_replace('/^&gt; \[!IMPORTANT\]\n&gt; (.*?)$/m', '<div class="my-4 p-4 bg-zinc-50 border border-zinc-950 rounded-lg text-xs text-zinc-800"><span class="font-bold uppercase tracking-wider block mb-1 text-[10px]">Important</span>$1</div>', $html);
    $html = preg_replace('/^&gt; \[!NOTE\]\n&gt; (.*?)$/m', '<div class="my-4 p-4 bg-zinc-150/40 border border-zinc-300 rounded-lg text-xs text-zinc-850"><span class="font-bold uppercase tracking-wider block mb-1 text-[10px] text-zinc-500">Note</span>$1</div>', $html);
    $html = preg_replace('/^&gt; (.*?)$/m', '<blockquote class="border-l-2 border-zinc-400 pl-4 py-0.5 my-4 italic text-zinc-500 text-xs">$1</blockquote>', $html);
    
    // List elements
    $html = preg_replace('/^\- (.*?)$/m', '<li class="ml-4 list-disc text-xs text-zinc-700 my-1">$1</li>', $html);
    
    // Assemble text lines
    $lines = explode("\n", $html);
    $processed = array();
    $in_list = false;
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (empty($trimmed)) {
            if ($in_list) {
                $processed[] = '</ul>';
                $in_list = false;
            }
            continue;
        }
        
        if (strpos($trimmed, '<li') === 0) {
            if (!$in_list) {
                $processed[] = '<ul class="space-y-1 my-3">';
                $in_list = true;
            }
            $processed[] = $line;
        } else {
            if ($in_list) {
                $processed[] = '</ul>';
                $in_list = false;
            }
            
            if (strpos($trimmed, '<h') === 0 || strpos($trimmed, '<div') === 0 || strpos($trimmed, '<pre') === 0 || strpos($trimmed, '<blockquote') === 0 || strpos($trimmed, '<table') === 0 || strpos($trimmed, '<ul') === 0) {
                $processed[] = $line;
            } else {
                $processed[] = '<p class="text-xs leading-relaxed text-zinc-700 my-2.5">' . $line . '</p>';
            }
        }
    }
    if ($in_list) {
        $processed[] = '</ul>';
    }
    
    return implode("\n", $processed);
}
}

// ── 4. AJAX Endpoints ─────────────────────────────────────────────────────

/**
 * AJAX: Save Documentation Page
 */
if ( ! function_exists( 'cora_ajax_save_doc_page' ) ) {
function cora_ajax_save_doc_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! cora_is_super_owner() ) {
        wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
    }

    global $wpdb;
    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    $t_versions = $wpdb->prefix . 'cora_docs_versions';

    $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
    $slug = sanitize_title( $_POST['slug'] ?? '' );
    $title = sanitize_text_field( $_POST['title'] ?? '' );
    $content = wp_kses_post( $_POST['content'] ?? '' );
    $category = sanitize_key( $_POST['category'] ?? '' );
    $module_key = isset( $_POST['module_key'] ) && ! empty( $_POST['module_key'] ) ? sanitize_key( $_POST['module_key'] ) : null;
    $status = sanitize_key( $_POST['status'] ?? 'draft' );
    $author_id = get_current_user_id();

    if ( empty( $slug ) || empty( $title ) || empty( $category ) ) {
        wp_send_json_error( array( 'message' => 'Please fill in all required parameters.' ) );
    }

    if ( $id > 0 ) {
        // Fetch old content to check for diff/version creation
        $old_content = $wpdb->get_var( $wpdb->prepare( "SELECT content FROM {$t_pages} WHERE id = %d", $id ) );
        
        $wpdb->update( $t_pages, array(
            'slug' => $slug,
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'module_key' => $module_key,
            'status' => $status,
            'updated_by' => $author_id,
            'updated_at' => current_time('mysql')
        ), array( 'id' => $id ) );

        // If content changed, create version backup
        if ( $old_content !== $content ) {
            $wpdb->insert( $t_versions, array(
                'page_id' => $id,
                'content' => $old_content,
                'version_label' => 'Backup ' . date('M j, Y H:i'),
                'created_by' => $author_id,
                'created_at' => current_time('mysql')
            ));
        }
        $saved_id = $id;
    } else {
        $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$t_pages} WHERE slug = %s", $slug ) );
        if ( $exists ) {
            wp_send_json_error( array( 'message' => 'Slug already exists. Choose a unique slug.' ) );
        }

        $wpdb->insert( $t_pages, array(
            'slug' => $slug,
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'module_key' => $module_key,
            'status' => $status,
            'created_by' => $author_id,
            'updated_by' => $author_id,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ));
        $saved_id = $wpdb->insert_id;
    }

    wp_send_json_success( array( 'message' => 'Page saved successfully.', 'id' => $saved_id ) );
}
}
add_action( 'wp_ajax_cora_save_doc_page', 'cora_ajax_save_doc_page' );

/**
 * AJAX: Retrieve Version History
 */
if ( ! function_exists( 'cora_ajax_get_doc_history' ) ) {
function cora_ajax_get_doc_history() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! cora_is_super_owner() ) {
        wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
    }

    global $wpdb;
    $t_versions = $wpdb->prefix . 'cora_docs_versions';
    $page_id = isset( $_GET['page_id'] ) ? intval( $_GET['page_id'] ) : 0;

    $history = $wpdb->get_results( $wpdb->prepare(
        "SELECT v.id, v.version_label, v.created_at, u.display_name 
         FROM {$t_versions} v 
         LEFT JOIN {$wpdb->users} u ON v.created_by = u.ID 
         WHERE v.page_id = %d 
         ORDER BY v.created_at DESC",
        $page_id
    ), ARRAY_A );

    wp_send_json_success( $history );
}
}
add_action( 'wp_ajax_cora_get_doc_history', 'cora_ajax_get_doc_history' );

/**
 * AJAX: Revert Page Version
 */
if ( ! function_exists( 'cora_ajax_revert_doc_page' ) ) {
function cora_ajax_revert_doc_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! cora_is_super_owner() ) {
        wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
    }

    global $wpdb;
    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    $t_versions = $wpdb->prefix . 'cora_docs_versions';

    $version_id = isset( $_POST['version_id'] ) ? intval( $_POST['version_id'] ) : 0;
    
    // Fetch version data
    $version = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t_versions} WHERE id = %d", $version_id ), ARRAY_A );
    if ( ! $version ) {
        wp_send_json_error( array( 'message' => 'Version backup not found.' ) );
    }

    // Get current content before reverting, to save it as backup
    $current_page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t_pages} WHERE id = %d", $version['page_id'] ), ARRAY_A );
    
    $author_id = get_current_user_id();

    // Revert target page
    $wpdb->update( $t_pages, array(
        'content' => $version['content'],
        'updated_by' => $author_id,
        'updated_at' => current_time('mysql')
    ), array( 'id' => $version['page_id'] ) );

    // Backup current before reverting
    $wpdb->insert( $t_versions, array(
        'page_id' => $version['page_id'],
        'content' => $current_page['content'],
        'version_label' => 'Backup before revert (' . date('M j H:i') . ')',
        'created_by' => $author_id,
        'created_at' => current_time('mysql')
    ));

    wp_send_json_success( array( 'message' => 'Reverted version successfully.', 'content' => $version['content'] ) );
}
}
add_action( 'wp_ajax_cora_revert_doc_page', 'cora_ajax_revert_doc_page' );

/**
 * AJAX: Publish / Change Status
 */
if ( ! function_exists( 'cora_ajax_publish_doc_page' ) ) {
function cora_ajax_publish_doc_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! cora_is_super_owner() ) {
        wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
    }

    global $wpdb;
    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
    $status = sanitize_key( $_POST['status'] ?? '' );

    if ( ! in_array( $status, array( 'draft', 'internal_review', 'approved_internal', 'staged_public', 'public_live' ) ) ) {
        wp_send_json_error( array( 'message' => 'Invalid publish status.' ) );
    }

    $wpdb->update( $t_pages, array(
        'status' => $status,
        'updated_by' => get_current_user_id(),
        'updated_at' => current_time('mysql')
    ), array( 'id' => $id ) );

    wp_send_json_success( array( 'message' => "Publish status updated to: " . str_replace('_', ' ', $status) ) );
}
}
add_action( 'wp_ajax_cora_publish_doc_page', 'cora_ajax_publish_doc_page' );

/**
 * AJAX: Save/Edit Changelog entry
 */
if ( ! function_exists( 'cora_ajax_save_changelog' ) ) {
function cora_ajax_save_changelog() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! cora_is_super_owner() ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    global $wpdb;
    $t_changelog = $wpdb->prefix . 'cora_docs_changelog';

    $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
    $module_key = isset( $_POST['module_key'] ) && ! empty( $_POST['module_key'] ) ? sanitize_key( $_POST['module_key'] ) : null;
    $version = sanitize_text_field( $_POST['version'] ?? '1.0.0' );
    $status = sanitize_key( $_POST['status'] ?? 'released' );
    $title = sanitize_text_field( $_POST['title'] ?? '' );
    $description = wp_kses_post( $_POST['description'] ?? '' );
    $ticket_id = sanitize_text_field( $_POST['ticket_id'] ?? '' );
    $author_id = get_current_user_id();

    if ( empty( $title ) ) {
        wp_send_json_error( array( 'message' => 'Changelog title is required.' ) );
    }

    $data = array(
        'module_key' => $module_key,
        'version' => $version,
        'status' => $status,
        'title' => $title,
        'description' => $description,
        'ticket_id' => $ticket_id,
        'author_id' => $author_id
    );

    if ( $id > 0 ) {
        $wpdb->update( $t_changelog, $data, array( 'id' => $id ) );
    } else {
        $data['created_at'] = current_time('mysql');
        $wpdb->insert( $t_changelog, $data );
    }

    wp_send_json_success( array( 'message' => 'Changelog entry saved successfully.' ) );
}
}
add_action( 'wp_ajax_cora_save_changelog', 'cora_ajax_save_changelog' );

/**
 * AJAX: Save/Edit API Registry Endpoint
 */
if ( ! function_exists( 'cora_ajax_save_api_endpoint' ) ) {
function cora_ajax_save_api_endpoint() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! cora_is_super_owner() ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    global $wpdb;
    $t_api = $wpdb->prefix . 'cora_docs_api_endpoints';

    $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
    $method = sanitize_text_field( $_POST['method'] ?? 'GET' );
    $path = sanitize_text_field( $_POST['path'] ?? '' );
    $description = sanitize_textarea_field( $_POST['description'] ?? '' );
    $required_permissions = sanitize_text_field( $_POST['required_permissions'] ?? '' );
    $request_schema = $_POST['request_schema'] ?? '';
    $response_schema = $_POST['response_schema'] ?? '';
    $example = $_POST['example'] ?? '';
    $permission_level = sanitize_key( $_POST['permission_level'] ?? 'admin' );
    $mcp_compatible = isset( $_POST['mcp_compatible'] ) ? intval( $_POST['mcp_compatible'] ) : 0;

    if ( empty( $path ) || empty( $method ) ) {
        wp_send_json_error( array( 'message' => 'Method and Path are required parameters.' ) );
    }

    $data = array(
        'method' => strtoupper( $method ),
        'path' => $path,
        'description' => $description,
        'required_permissions' => $required_permissions,
        'request_schema' => $request_schema,
        'response_schema' => $response_schema,
        'example' => $example,
        'permission_level' => $permission_level,
        'mcp_compatible' => $mcp_compatible,
        'updated_at' => current_time('mysql')
    );

    if ( $id > 0 ) {
        $wpdb->update( $t_api, $data, array( 'id' => $id ) );
    } else {
        $wpdb->insert( $t_api, $data );
    }

    wp_send_json_success( array( 'message' => 'API endpoint reference saved successfully.' ) );
}
}
add_action( 'wp_ajax_cora_save_api_endpoint', 'cora_ajax_save_api_endpoint' );

// ── 5. Auto-Update Event Hooks ────────────────────────────────────────────

/**
 * Triggered when a module status is updated
 */
if ( ! function_exists( 'cora_docs_module_status_trigger' ) ) {
function cora_docs_module_status_trigger( $module_key, $old_status, $new_status, $version ) {
    global $wpdb;
    $t_changelog = $wpdb->prefix . 'cora_docs_changelog';
    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    
    $author_id = get_current_user_id() ?: 1;
    $title = "Module " . esc_html($module_key) . " status updated to " . esc_html($new_status);
    $desc = "The status of the " . esc_html($module_key) . " module was updated from " . esc_html($old_status) . " to " . esc_html($new_status) . " (Version " . esc_html($version) . ").";
    
    // Create draft changelog entry
    $wpdb->insert( $t_changelog, array(
        'module_key' => $module_key,
        'version' => $version,
        'status' => 'released',
        'title' => $title,
        'description' => $desc,
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));
    
    // Automatically set page back to draft for super admin review/updating
    $wpdb->update( $t_pages, 
        array( 'status' => 'draft', 'updated_at' => current_time('mysql') ), 
        array( 'module_key' => $module_key )
    );
}
}
add_action( 'cora_module_status_changed', 'cora_docs_module_status_trigger', 10, 4 );

/**
 * Triggered when a feature is marked as complete
 */
if ( ! function_exists( 'cora_docs_feature_completed_trigger' ) ) {
function cora_docs_feature_completed_trigger( $module_key, $feature_name, $version ) {
    global $wpdb;
    $t_changelog = $wpdb->prefix . 'cora_docs_changelog';
    
    $author_id = get_current_user_id() ?: 1;
    $wpdb->insert( $t_changelog, array(
        'module_key' => $module_key,
        'version' => $version,
        'status' => 'released',
        'title' => "Feature Shipped: " . esc_html($feature_name),
        'description' => "Successfully integrated and verified feature: " . esc_html($feature_name) . " in version " . esc_html($version) . ".",
        'author_id' => $author_id,
        'created_at' => current_time('mysql')
    ));
}
}
add_action( 'cora_module_feature_completed', 'cora_docs_feature_completed_trigger', 10, 3 );

/**
 * Triggered when a new module is registered/scaffolded
 */
if ( ! function_exists( 'cora_docs_module_registered_trigger' ) ) {
function cora_docs_module_registered_trigger( $module_key, $title, $version ) {
    global $wpdb;
    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    
    $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$t_pages} WHERE module_key = %s", $module_key ) );
    if ( ! $exists ) {
        $slug = sanitize_title( $module_key );
        $content = "# " . esc_html( $title ) . "\n\nWelcome to the module documentation page for " . esc_html( $title ) . " (v" . esc_html( $version ) . ").\n\n## Module Overview\nDescribe module purpose here.\n\n## Features List\n- Feature 1 (Planned)\n\n## Permissions Reference\n- `administrator`: Full access\n\n## AI Layer Description\nRAG context details.";
        
        $wpdb->insert( $t_pages, array(
            'slug' => $slug,
            'title' => $title,
            'content' => $content,
            'category' => 'modules',
            'module_key' => $module_key,
            'status' => 'draft',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ));
    }
}
}
add_action( 'cora_module_registered', 'cora_docs_module_registered_trigger', 10, 3 );

/**
 * Triggered when an API endpoint changes
 */
if ( ! function_exists( 'cora_docs_api_endpoint_trigger' ) ) {
function cora_docs_api_endpoint_trigger( $method, $path, $description, $required_permissions, $request_schema, $response_schema, $example, $mcp_compatible ) {
    global $wpdb;
    $t_api = $wpdb->prefix . 'cora_docs_api_endpoints';
    
    $wpdb->replace( $t_api, array(
        'method' => strtoupper( $method ),
        'path' => $path,
        'description' => $description,
        'required_permissions' => $required_permissions,
        'request_schema' => $request_schema,
        'response_schema' => $response_schema,
        'example' => $example,
        'permission_level' => 'admin',
        'mcp_compatible' => $mcp_compatible ? 1 : 0,
        'updated_at' => current_time('mysql')
    ));
}
}
add_action( 'cora_api_endpoint_changed', 'cora_docs_api_endpoint_trigger', 10, 8 );

/**
 * Intercept /docs/ requests to display public facing documentation
 */
if ( ! function_exists( 'cora_docs_handle_public_route' ) ) {
function cora_docs_handle_public_route() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path = parse_url( home_url(), PHP_URL_PATH ) ?: '';
    $path = substr( $request_uri, strlen( $home_path ) );
    $path = trim( parse_url( $path, PHP_URL_PATH ), '/' );

    $path_parts = explode( '/', $path );

    if ( isset( $path_parts[0] ) && 'docs' === $path_parts[0] ) {
        global $wpdb;
        $t_pages = $wpdb->prefix . 'cora_docs_pages';
        $t_changelog = $wpdb->prefix . 'cora_docs_changelog';
        $t_api = $wpdb->prefix . 'cora_docs_api_endpoints';

        // Check if viewing a specific slug
        // e.g. /docs/v1/user-management or /docs/user-management or /docs
        $sub_slug = isset( $path_parts[1] ) ? sanitize_title( $path_parts[1] ) : '';
        
        $version = '';
        if ( preg_match( '/^v[0-9]+$/', $sub_slug ) ) {
            $version = $sub_slug;
            $sub_slug = isset( $path_parts[2] ) ? sanitize_title( $path_parts[2] ) : '';
        }

        // Fetch all pages (including drafts, since we want a single unified public docs portal)
        $pages = $wpdb->get_results(
            "SELECT * FROM {$t_pages} ORDER BY category, title",
            ARRAY_A
        );

        // Fetch changelogs
        $changelogs = $wpdb->get_results(
            "SELECT * FROM {$t_changelog} WHERE status = 'released' ORDER BY created_at DESC",
            ARRAY_A
        );

        // Fetch APIs
        $apis = $wpdb->get_results(
            "SELECT * FROM {$t_api} ORDER BY path, method",
            ARRAY_A
        );

        $active_page = null;
        if ( ! empty( $sub_slug ) ) {
            foreach ( $pages as $p ) {
                if ( $p['slug'] === $sub_slug ) {
                    $active_page = $p;
                    break;
                }
            }
        }
        
        if ( ! $active_page && ! empty( $pages ) ) {
            // Find platform-overview or overview category page
            foreach ( $pages as $p ) {
                if ( $p['slug'] === 'platform-overview' || $p['category'] === 'overview' ) {
                    $active_page = $p;
                    break;
                }
            }
            if ( ! $active_page ) {
                $active_page = $pages[0];
            }
        }

        nocache_headers();
        status_header( 200 );
        
        // Include HTML layout
        include CORA_WORKSPACE_PATH . 'views/view-public-docs.php';
        exit;
    }
}
}

/**
 * AJAX: Simulate Documentation Update triggers
 */
if ( ! function_exists( 'cora_ajax_simulate_doc_trigger' ) ) {
function cora_ajax_simulate_doc_trigger() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! cora_is_super_owner() ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    $event = sanitize_key( $_POST['event'] ?? '' );

    if ( $event === 'register_module' ) {
        do_action( 'cora_module_registered', 'billing-engine', 'Billing & Invoicing Engine', 'v1.0.0' );
        wp_send_json_success( array( 'message' => 'Simulated: cora_module_registered hook triggered for billing-engine.' ) );
    } elseif ( $event === 'status_changed' ) {
        do_action( 'cora_module_status_changed', 'user-management', 'in_build', 'released', 'v1.2.0' );
        wp_send_json_success( array( 'message' => 'Simulated: cora_module_status_changed hook triggered for user-management.' ) );
    } elseif ( $event === 'feature_completed' ) {
        do_action( 'cora_module_feature_completed', 'user-management', 'SMS OTP verification gateway', 'v1.2.1' );
        wp_send_json_success( array( 'message' => 'Simulated: cora_module_feature_completed hook triggered.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Invalid event key.' ) );
    }
}
}
add_action( 'wp_ajax_cora_simulate_doc_trigger', 'cora_ajax_simulate_doc_trigger' );

/**
 * AJAX: Fetch page content for public view
 */
if ( ! function_exists( 'cora_ajax_public_get_page' ) ) {
function cora_ajax_public_get_page() {
    $slug = sanitize_title( $_GET['slug'] ?? '' );
    if ( empty( $slug ) ) {
        wp_send_json_error( array( 'message' => 'Missing slug.' ) );
    }

    global $wpdb;
    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    $page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t_pages} WHERE slug = %s", $slug ), ARRAY_A );

    if ( ! $page ) {
        wp_send_json_error( array( 'message' => 'Page not found.' ) );
    }

    // Convert markdown to HTML
    $html = cora_markdown_to_html( $page['content'] );

    wp_send_json_success( array(
        'title' => $page['title'],
        'category' => $page['category'],
        'updated_at' => date( 'M j, Y H:i', strtotime( $page['updated_at'] ) ),
        'html' => $html
    ) );
}
}
add_action( 'wp_ajax_cora_public_get_page', 'cora_ajax_public_get_page' );
add_action( 'wp_ajax_nopriv_cora_public_get_page', 'cora_ajax_public_get_page' );



