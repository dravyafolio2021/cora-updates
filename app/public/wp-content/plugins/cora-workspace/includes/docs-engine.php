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

    $author_id = 1; // Default Admin user ID

    $user_mgmt_content = "# Module 01: User Management, Roles & AI Permissions\n\n## 1. Multi-Tenant Role Architecture\nCora manages user authentication and authorization at the tenant workspace level. The platform defines four standard system roles and supports custom role extensions:\n- **Super Admin (`cora_super_admin`)**: Globally locked. Enforces absolute control over backups, system configurations, billing, and plugin installations.\n- **Tenant Owner (`owner`)**: Dedicated manager of the workspace tenant. Can create and modify custom roles, save security permissions matrices, and authorize third-party keys.\n- **Administrator (`administrator`)**: Complete read/write access to operations and data modules (CRM Leads, Showings, Invoices) but restricted from altering core tenant database settings.\n- **Workspace Manager / Crew (`cora_member` / `cora_manager`)**: Scoped access tailored to operational tasks. Can only perform actions allowed by the Active Security Matrix.\n\n## 2. Security & Permissions Matrix\nThe workspace owner can save granular capability permissions across all operational sections:\n- **Core Navigation**: Dashboard, Showings CRM, Portfolio.\n- **Operational Modules**: Team & Roles, Listings/Gear.\n- **Sales Channels**: Canvas Web Creator, Forms & Contracts, SMTP Outbound Drips, Review Acquisition.\n- **Administrative Rights**: Financials, Audit Logs, and Developer Settings.\n\n## 3. AI Native Capabilities & MCP Gateway\nCora integrates a local Model Context Protocol (MCP) gateway to bind external LLM execution contexts:\n- **Bearer Token Authorization**: MCP servers connect securely using cryptographically generated REST tokens.\n- **Dynamic Token Regeneration**: Administrators can rotate security tokens instantly from the User Settings interface to revoke or refresh API access.\n- **Live Sync Indicator**: Visual indicator badge displays the state of dynamic RAG synchronizations and MCP connections in real-time.";

    $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$t_pages}");
    if ( $count > 0 ) {
        // Upsert to ensure existing database page is updated to detailed description
        $existing_page_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$t_pages} WHERE slug = %s", 'user-management-and-auth' ) );
        if ( $existing_page_id ) {
            $wpdb->update( $t_pages, array(
                'title'      => 'Module 01: User Management & AI Permissions',
                'content'    => $user_mgmt_content,
                'category'   => 'modules',
                'updated_at' => current_time('mysql')
            ), array( 'id' => $existing_page_id ) );
        }

        // ── Upsert all new pages added in v3.2.46 ─────────────────────────
        $new_pages = cora_docs_get_new_pages_v3246();
        foreach ( $new_pages as $p ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$t_pages} WHERE slug = %s", $p['slug'] ) );
            if ( ! $exists ) {
                $wpdb->insert( $t_pages, array_merge( $p, array(
                    'status'     => 'draft',
                    'created_by' => $author_id,
                    'updated_by' => $author_id,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql'),
                ) ) );
            } else {
                // Update content if page exists but was created with placeholder text
                $wpdb->update( $t_pages, array(
                    'title'      => $p['title'],
                    'content'    => $p['content'],
                    'category'   => $p['category'],
                    'updated_at' => current_time('mysql'),
                ), array( 'slug' => $p['slug'] ) );
            }
        }

        // ── Upsert v3.2.46 changelog entries ──────────────────────────────
        $v3246_exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$t_changelog} WHERE version = %s AND title = %s",
            '3.2.46', 'PWA Install Prompt & Push Notifications'
        ) );
        if ( ! $v3246_exists ) {
            $pwa_changelog_entries = cora_docs_get_pwa_changelog_entries();
            foreach ( $pwa_changelog_entries as $entry ) {
                $wpdb->insert( $t_changelog, array_merge( $entry, array(
                    'author_id'  => $author_id,
                    'created_at' => current_time('mysql'),
                ) ) );
            }
        }

        return; // Already seeded, upserts done
    }


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
        'title' => 'Module 01: User Management & AI Permissions',
        'category' => 'modules',
        'module_key' => 'user-management',
        'content' => $user_mgmt_content,
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

/**
 * AJAX: Public search documentation pages
 */
if ( ! function_exists( 'cora_ajax_public_search_docs' ) ) {
function cora_ajax_public_search_docs() {
    global $wpdb;
    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    
    $term = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '';
    if ( strlen( $term ) < 2 ) {
        wp_send_json_success( array() );
        exit;
    }
    
    $like = '%' . $wpdb->esc_like( $term ) . '%';
    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT id, slug, title, content, category 
         FROM {$t_pages} 
         WHERE title LIKE %s OR content LIKE %s 
         ORDER BY category, title ASC LIMIT 20",
        $like, $like
    ), ARRAY_A );
    
    $grouped = array();
    foreach ( $results as $row ) {
        $category = $row['category'];
        if ( ! isset( $grouped[$category] ) ) {
            $grouped[$category] = array();
        }
        
        // Generate snippet
        $snippet = '';
        $content = strip_tags( $row['content'] );
        $pos = stripos( $content, $term );
        if ( $pos !== false ) {
            $start = max( 0, $pos - 30 );
            $length = 80;
            $snippet = substr( $content, $start, $length );
            if ( $start > 0 ) $snippet = '...' . $snippet;
            if ( strlen( $content ) > $start + $length ) $snippet .= '...';
        } else {
            $snippet = substr( $content, 0, 80 );
            if ( strlen( $content ) > 80 ) $snippet .= '...';
        }
        
        $grouped[$category][] = array(
            'id' => $row['id'],
            'slug' => $row['slug'],
            'title' => $row['title'],
            'snippet' => esc_html( trim( $snippet ) )
        );
    }
    
    wp_send_json_success( $grouped );
    exit;
}
}
add_action( 'wp_ajax_cora_public_search_docs', 'cora_ajax_public_search_docs' );
add_action( 'wp_ajax_nopriv_cora_public_search_docs', 'cora_ajax_public_search_docs' );

/**
 * AJAX: Public query RAG knowledge base for Cora AI chatbot
 */
if ( ! function_exists( 'cora_ajax_public_query_rag' ) ) {
function cora_ajax_public_query_rag() {
    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
    if ( ! wp_verify_nonce( $nonce, 'cora_public_docs_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security validation failed.' ) );
    }

    $question = isset( $_POST['question'] ) ? sanitize_text_field( wp_unslash( $_POST['question'] ) ) : '';
    if ( empty( $question ) ) {
        wp_send_json_error( array( 'message' => 'Question is required.' ) );
    }

    global $wpdb;
    $table = $wpdb->prefix . 'cora_rag_knowledge';
    $agency_id = cora_db_get_agency_id();
    
    $context_text = '';
    
    if ( cora_table_exists( $table ) ) {
        $keywords = explode( ' ', $question );
        $like_conditions = array();
        $params = array( $agency_id );
        foreach ( $keywords as $kw ) {
            $kw = trim( $kw );
            if ( strlen( $kw ) > 2 ) {
                $like_conditions[] = "(title LIKE %s OR content LIKE %s)";
                $params[] = '%' . $wpdb->esc_like( $kw ) . '%';
                $params[] = '%' . $wpdb->esc_like( $kw ) . '%';
            }
        }
        
        if ( ! empty( $like_conditions ) ) {
            $sql = "SELECT title, content FROM {$table} WHERE agency_id = %d AND (" . implode( ' OR ', $like_conditions ) . ") LIMIT 4";
            $results = $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );
            if ( $results ) {
                foreach ( $results as $row ) {
                    $context_text .= "Resource: " . $row['title'] . "\nContent: " . wp_strip_all_tags( $row['content'] ) . "\n\n";
                }
            }
        }
        
        if ( empty( $context_text ) ) {
            $results = $wpdb->get_results( $wpdb->prepare( "SELECT title, content FROM {$table} WHERE agency_id = %d ORDER BY id DESC LIMIT 2", $agency_id ), ARRAY_A );
            if ( $results ) {
                foreach ( $results as $row ) {
                    $context_text .= "Resource: " . $row['title'] . "\nContent: " . wp_strip_all_tags( $row['content'] ) . "\n\n";
                }
            }
        }
    }
    
    $system_prompt = "You are Cora AI, an intelligent, helpful developer assistant for the Cora Platform. 
Your objective is to answer technical and general questions about the Cora Platform and its workspace modules.
If you are provided with retrieved knowledge base context below, use it to accurately address the query.
Otherwise, use your general knowledge of the Cora Platform (which features dynamic bookings, crew schedule boards, lead generation pipelines, CGST/SGST ledger sheets, e-signing vault registry, etc.) to give a clear and professional answer.
Always respond in short, clear, and bulleted or formatted Markdown. Avoid conversational fluff.

Retrieved Context:
" . ( $context_text ? $context_text : "No specific documentation context was found in the RAG database." );

    $reply = '';
    
    if ( function_exists( 'cora_rag_call_ai_api' ) ) {
        $reply = cora_rag_call_ai_api( "Question: " . $question, $system_prompt );
    }
    
    if ( empty( $reply ) ) {
        $reply = "I parsed the context but could not reach the LLM provider. The Cora platform features active modules: Bookings (calendar & client slots), Leads (Kanban board), Financials (CGST/SGST ledger invoice splits), Document Vault (e-sign contracts), and CRM. Please configure your Gemini API Key in the RAG Knowledge base settings to enable full chatbot interactivity.";
    }

    wp_send_json_success( array(
        'reply' => $reply
    ) );
    exit;
}
}
add_action( 'wp_ajax_cora_public_query_rag', 'cora_ajax_public_query_rag' );
add_action( 'wp_ajax_nopriv_cora_public_query_rag', 'cora_ajax_public_query_rag' );

// ── 7. New Pages Registry (v3.2.46) ──────────────────────────────────────

function cora_docs_get_new_pages_v3246() {
    return array(

        // ── Overview ──────────────────────────────────────────────────────
        array(
            'slug'     => 'architecture-overview',
            'title'    => 'Architecture & Stack',
            'category' => 'overview',
            'content'  => "# Architecture & Stack\n\nCora is a single-plugin WordPress SaaS platform. All tenant workspaces, modules, and REST routes live inside a single plugin (`cora-workspace`) deployed on a shared Hostinger VPS.\n\n## Routing Layer\nAll inbound HTTP requests pass through `cora-workspace.php`'s `template_redirect` hook. Routes are matched by inspecting the request URI path segments:\n- `/workspace/**` → Workspace SPA shell\n- `/docs/**` → Public developer documentation\n- `/cora-manifest.json` → Dynamic PWA manifest\n- `/cora-service-worker.js` → Service Worker served at root scope\n- `/api/v1/**` → JSON REST endpoints\n\n## Database Layer\nCora uses custom `\$wpdb` tables with the `cora_` prefix. All queries are tenant-scoped by `agency_id` to enforce multi-tenant data isolation.\n\n## AI Layer\n- **RAG**: Retrieval-Augmented Generation via `cora_rag_knowledge` table. Syncs agency documents for in-context retrieval.\n- **LLM**: Google Gemini Flash and Anthropic Claude Sonnet via REST API calls from PHP.\n- **MCP**: JSON-RPC 2.0 over WebSockets, allowing external LLM agents to call Cora AJAX actions with inherited role permissions.\n\n## PWA Layer (v3.2.46)\n- **Manifest**: Dynamically generated via `/cora-manifest.json` with agency-branded icons.\n- **Service Worker**: Registered at root scope. Handles offline caching (Cache-First for assets, Network-First for API), background push notification delivery, and click-to-focus routing.\n- **VAPID**: ES256 self-signed JWT push authorization for all Web Push subscriptions."
        ),
        array(
            'slug'     => 'workspace-roles',
            'title'    => 'Roles & Permissions',
            'category' => 'overview',
            'content'  => "# Roles & Permissions\n\nCora enforces a 4-tier role hierarchy across all workspace tenants.\n\n## Role Levels\n- **`cora_super_admin`**: Global super administrator. Access to all workspaces, billing, system settings, and plugin configurations. Cannot be restricted.\n- **`owner`**: Tenant workspace owner. Creates and manages roles, security matrix, API keys, and authorizes third-party integrations.\n- **`administrator`**: Full read/write on all operational modules (CRM, Financials, Media, Crew). Cannot alter core tenant DB settings or billing.\n- **`cora_member` / `cora_manager`**: Scoped per the active Security Permissions Matrix set by the owner.\n- **`cora_branch_manager`**: Read-only across all modules. Cannot create, edit, or delete any records.\n\n## Security Permissions Matrix\nOwners define capability grants per module section:\n\n| Section | Capabilities |\n|---|---|\n| CRM Leads | view_leads, edit_leads, delete_leads |\n| Financials | view_invoices, create_invoices, mark_paid |\n| Crew Scheduler | view_crew, assign_shifts, manage_crew |\n| Document Vault | upload_docs, sign_docs, share_docs |\n| Canvas Builder | view_canvas, publish_theme |\n\n## Access Control Implementation\nAll AJAX handlers gate access using `cora_is_super_owner()`, `current_user_can()`, and agency-level `agency_id` filtering on all database queries."
        ),

        // ── CRM ────────────────────────────────────────────────────────────
        array(
            'slug'     => 'crm-client-tasks',
            'title'    => 'Client Task Manager',
            'category' => 'crm',
            'content'  => "# Client Task Manager\n\nThe Client Task Manager enables agencies to assign, track, and communicate deliverables directly to clients through a dedicated task board integrated into the CRM.\n\n## Overview\nTasks are structured as deliverables linked to a CRM lead or contact. Each task has a priority level, deadline, assignee, and real-time status.\n\n## Task Lifecycle\n1. **Created**: Task assigned by admin, visible to client in portal.\n2. **In Progress**: Assignee marks task as started.\n3. **Review**: Submitted for client approval or internal review.\n4. **Completed**: Signed off by admin or client.\n5. **Overdue**: Auto-flagged when deadline passes without completion.\n\n## Push Notifications (v3.2.46)\nHigh-priority task updates (status change, new assignments, deadline approaching) now trigger Web Push notifications to subscribed agency staff via the VAPID push system.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_get_client_tasks` | `nonce`, `lead_id` | Returns all tasks for a lead |\n| `cora_ajax_create_client_task` | `nonce`, `lead_id`, `title`, `priority`, `due_date` | Creates a new task |\n| `cora_ajax_update_task_status` | `nonce`, `task_id`, `status` | Updates task status and triggers push notification |\n\n## Database Schema\n### `cora_client_tasks`\n| Column | Type | Description |\n|---|---|---|\n| `id` | bigint | Primary Key |\n| `agency_id` | bigint | Tenant isolation |\n| `lead_id` | bigint | Associated CRM lead |\n| `title` | varchar(255) | Task title |\n| `status` | varchar(50) | pending/in_progress/completed/overdue |\n| `priority` | varchar(20) | low/medium/high/urgent |\n| `due_date` | date | Deadline |\n| `assigned_to` | bigint | WordPress user ID |"
        ),

        // ── Finance ────────────────────────────────────────────────────────
        array(
            'slug'     => 'finance-reports',
            'title'    => 'Financial Reports',
            'category' => 'finance',
            'content'  => "# Financial Reports\n\nThe Financials module provides a comprehensive P&L, cash flow, and tax summary dashboard for agency administrators.\n\n## Report Types\n- **Revenue Summary**: Total invoiced vs. collected vs. outstanding by date range.\n- **GST Ledger**: CGST/SGST/IGST split with HSN code breakdowns for compliance.\n- **Expense Tracker**: Manual expense entries categorized by vendor and type.\n- **Profit & Loss Statement**: Auto-calculated from invoices and expenses.\n\n## Date Filters\nAll reports support arbitrary date range filtering (custom, last 30 days, current quarter, financial year).\n\n## Export\nReports can be exported as CSV or PDF using the built-in renderer.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_get_financial_summary` | `nonce`, `from`, `to` | Returns revenue & GST summary for date range |\n| `cora_ajax_get_expense_list` | `nonce`, `from`, `to` | Returns expense records |\n| `cora_ajax_export_financials` | `nonce`, `format`, `from`, `to` | Exports report as CSV/PDF |"
        ),

        // ── Operations ────────────────────────────────────────────────────
        array(
            'slug'     => 'ops-crew-scheduler',
            'title'    => 'Crew Scheduler',
            'category' => 'operations',
            'content'  => "# Crew Scheduler\n\nThe Crew Scheduler is a visual shift management board for scheduling and tracking agency team members across bookings and events.\n\n## Architecture\nThe scheduler is built as a custom calendar grid rendering crew member availability across days/weeks. Shifts are stored in `cora_crew_shifts` and linked to both a user and a booking/event record.\n\n## Features\n- **Drag-and-drop shift assignment**: Assign crew members to bookings visually.\n- **Availability matrix**: Set crew member working hours and days off.\n- **Conflict detection**: Prevents double-booking of crew across overlapping shifts.\n- **Mobile-first view**: Card-based compact view on mobile.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_get_crew_schedule` | `nonce`, `week_start` | Returns all shifts for a given week |\n| `cora_ajax_assign_crew_shift` | `nonce`, `booking_id`, `user_id`, `date`, `time_start`, `time_end` | Assigns a shift |\n| `cora_ajax_remove_crew_shift` | `nonce`, `shift_id` | Removes a shift assignment |\n\n## Database Schema\n### `cora_crew_shifts`\n| Column | Type | Description |\n|---|---|---|\n| `id` | bigint | Primary Key |\n| `agency_id` | bigint | Tenant isolation |\n| `user_id` | bigint | Assigned crew member |\n| `booking_id` | bigint | Linked booking |\n| `shift_date` | date | Date of shift |\n| `time_start` | time | Shift start time |\n| `time_end` | time | Shift end time |\n| `role_label` | varchar(100) | Crew role for this shift |"
        ),
        array(
            'slug'     => 'ops-equipment',
            'title'    => 'Equipment Management',
            'category' => 'operations',
            'content'  => "# Equipment Management\n\nThe Equipment module tracks physical gear inventory, assignments, condition logs, and maintenance schedules.\n\n## Features\n- **Inventory Registry**: Full gear catalog with serial numbers, purchase dates, and current condition.\n- **Assignment Tracking**: Link equipment items to bookings and shoots.\n- **Maintenance Logs**: Record service events, repairs, and replacements.\n- **Availability Calendar**: Visual view of which items are booked vs. available.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_get_equipment_list` | `nonce` | Returns all equipment records |\n| `cora_ajax_add_equipment` | `nonce`, `name`, `serial`, `condition`, `category` | Adds new equipment item |\n| `cora_ajax_assign_equipment` | `nonce`, `equipment_id`, `booking_id` | Assigns equipment to a booking |\n| `cora_ajax_log_maintenance` | `nonce`, `equipment_id`, `note`, `date` | Adds maintenance log entry |\n\n## Database Schema\n### `cora_equipment`\n| Column | Type | Description |\n|---|---|---|\n| `id` | bigint | Primary Key |\n| `agency_id` | bigint | Tenant isolation |\n| `name` | varchar(255) | Equipment name |\n| `serial_number` | varchar(100) | Serial/model number |\n| `category` | varchar(100) | camera/lens/lighting/audio/etc. |\n| `condition` | varchar(50) | excellent/good/fair/damaged |\n| `purchase_date` | date | Date of purchase |\n| `notes` | text | Additional notes |"
        ),
        array(
            'slug'     => 'ops-event-timeline',
            'title'    => 'Event Timeline',
            'category' => 'operations',
            'content'  => "# Event Timeline\n\nThe Event Timeline module provides a chronological view of all bookings, shoots, and milestones for the agency, enabling high-level scheduling awareness.\n\n## Features\n- **Gantt-style timeline**: Visualize overlapping bookings across a date range.\n- **Filter by status**: View confirmed, tentative, completed, or cancelled events.\n- **Quick preview**: Click any event to see client, crew, equipment, and financial summary.\n\n## Integration\nTimeline data is pulled from the `cora_bookings` table and cross-referenced with crew shifts and equipment assignments to provide a complete operational picture per event."
        ),

        // ── Media & Assets ────────────────────────────────────────────────
        array(
            'slug'     => 'media-library',
            'title'    => 'Media Library',
            'category' => 'media',
            'content'  => "# Media Library\n\nThe Cora Media Library is a workspace-scoped asset management system that replaces the native WordPress media library with a tenant-isolated, AI-enhanced file management interface.\n\n## Features\n- **Folder Organization**: Hierarchical folder tree for organizing shoots, projects, and brands.\n- **AI Tagging**: Auto-generated semantic tags on uploaded images using the Gemini Vision API.\n- **Client Sharing**: Generate time-limited secure share links for client gallery delivery.\n- **Bulk Operations**: Select multiple files for batch download, delete, or move.\n- **Search**: Full-text and tag-based search across the entire media library.\n\n## Architecture\nMedia metadata is stored in `cora_media_assets`. Actual files are stored in WordPress uploads. The library renders as a responsive grid SPA inside the workspace sidebar navigation.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_get_media_library` | `nonce`, `folder_id`, `page` | Paginated media list |\n| `cora_ajax_upload_media` | `nonce`, `file`, `folder_id` | Handles file upload |\n| `cora_ajax_delete_media` | `nonce`, `media_id` | Permanently deletes a file |\n| `cora_ajax_share_media` | `nonce`, `media_id`, `expiry_days` | Generates secure share link |\n\n## Database Schema\n### `cora_media_assets`\n| Column | Type | Description |\n|---|---|---|\n| `id` | bigint | Primary Key |\n| `agency_id` | bigint | Tenant isolation |\n| `wp_attachment_id` | bigint | WordPress attachment ID |\n| `folder_id` | bigint | Parent folder |\n| `title` | varchar(255) | Display name |\n| `tags` | text | JSON array of AI-generated tags |\n| `file_type` | varchar(50) | image/video/pdf/raw |\n| `file_size` | bigint | File size in bytes |"
        ),
        array(
            'slug'     => 'media-editor',
            'title'    => 'Media Editor',
            'category' => 'media',
            'content'  => "# Media Editor\n\nThe Media Editor provides in-browser image editing and annotation capabilities for agency-uploaded files.\n\n## Features\n- **Crop & Resize**: Aspect-ratio-locked crop presets (1:1, 4:3, 16:9) or freeform.\n- **Rotate & Flip**: 90-degree rotations, horizontal/vertical flip.\n- **Annotation Canvas**: Draw text labels, arrows, and highlights for feedback and delivery.\n- **Export**: Save edits as a new copy or overwrite the original.\n\n## Implementation\nThe editor is built on the HTML5 Canvas API with a custom rendering pipeline. It is accessible from within the Media Library by clicking any image asset."
        ),
        array(
            'slug'     => 'media-gallery',
            'title'    => 'Public Gallery Portal',
            'category' => 'media',
            'content'  => "# Public Gallery Portal\n\nThe Public Gallery Portal is a client-facing hosted gallery page that allows agencies to share curated photo/video collections with their clients via a secure URL.\n\n## Features\n- **Password Protection**: Optional PIN or password access to gallery.\n- **Download Control**: Toggle whether clients can download individual files or albums.\n- **Branded Landing Page**: Agency logo, header, and watermark configuration.\n- **Review & Approval**: Clients can mark favorite/approved selects directly in the gallery.\n\n## URL Structure\nGallery URLs are served at `/gallery/{share-hash}`, resolved through the custom routing layer in `cora-workspace.php`."
        ),

        // ── Client Portal ─────────────────────────────────────────────────
        array(
            'slug'     => 'portal-client-tasks',
            'title'    => 'Client Task Manager (Portal)',
            'category' => 'client-portal',
            'content'  => "# Client Task Manager — Portal View\n\nThe client-facing task interface allows external clients to view their assigned deliverables and provide approvals or comments without needing a full workspace login.\n\n## Access\nClients access their task portal via a magic-link URL generated by the agency. No password is required — authentication uses a one-time URL token.\n\n## Capabilities\n- View all tasks assigned to their project\n- Mark tasks as reviewed/approved\n- Leave inline comments per task\n- Download attached deliverables"
        ),
        array(
            'slug'     => 'portal-forms',
            'title'    => 'Forms Builder',
            'category' => 'client-portal',
            'content'  => "# Forms Builder\n\nThe Forms Builder is a no-code drag-and-drop form creation system for lead capture, client onboarding questionnaires, and contract agreements.\n\n## Field Types\n- Text, Email, Phone, Number\n- Dropdown Select, Multi-checkbox, Radio\n- Date Picker, File Upload\n- Signature Pad\n- GST Calculation Block\n\n## Features\n- **Multi-step Forms**: Group fields into tabbed steps with progress indicators.\n- **Conditional Logic**: Show/hide fields based on previous answers.\n- **E-Sign Integration**: Embed a signature pad and pipe submissions directly into the Document Vault.\n- **CRM Mapping**: Auto-create a CRM lead from a form submission.\n- **Webhook Export**: POST form responses to external endpoints (Zapier, Make, custom APIs).\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_get_forms_list` | `nonce` | Returns all forms for the agency |\n| `cora_ajax_create_form` | `nonce`, `title`, `fields_json` | Creates a new form |\n| `cora_ajax_get_form_submissions` | `nonce`, `form_id` | Returns submissions for a form |\n\n## Database Schema\n### `cora_forms`\n| Column | Type | Description |\n|---|---|---|\n| `id` | bigint | Primary Key |\n| `agency_id` | bigint | Tenant isolation |\n| `title` | varchar(255) | Form title |\n| `fields` | longtext | JSON schema of all fields |\n| `settings` | longtext | JSON settings (redirects, webhooks, CRM mapping) |\n| `status` | varchar(50) | draft/active/archived |"
        ),
        array(
            'slug'     => 'portal-reviews',
            'title'    => 'Review Acquisition',
            'category' => 'client-portal',
            'content'  => "# Review Acquisition\n\nThe Review Acquisition module automates post-service review campaigns to collect Google, Facebook, and platform-native testimonials from clients.\n\n## Workflow\n1. **Trigger**: Admin marks a booking as completed.\n2. **Delay**: Configurable wait period (1–7 days) before sending review request.\n3. **Channel**: Email or WhatsApp review invitation sent automatically.\n4. **Redirect**: Positive sentiment (4–5 stars) redirects to Google Business Profile. Negative sentiment (1–3 stars) routes to an internal feedback form.\n5. **Dashboard**: Aggregated review scores and testimonial cards in the workspace.\n\n## Integration\n- Syncs collected reviews to **Google Business Profile** via the GBP Review API.\n- Displays public testimonials on agency's Canvas-built website via shortcode.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_send_review_request` | `nonce`, `booking_id`, `channel` | Sends review invitation |\n| `cora_ajax_get_review_stats` | `nonce` | Returns aggregate review scores |"
        ),
        array(
            'slug'     => 'portal-comments',
            'title'    => 'Comments System',
            'category' => 'client-portal',
            'content'  => "# Comments System\n\nThe Comments module provides threaded inline commenting across deliverables, tasks, and document vault items, enabling async collaboration between agency teams and clients.\n\n## Features\n- **Inline Comments**: Attach comments to specific media files, tasks, or document sections.\n- **@Mentions**: Tag specific workspace users for notifications.\n- **Resolved State**: Mark comment threads as resolved to maintain clean audit trails.\n- **Client Access**: Clients can comment via the portal without a WordPress login.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_post_comment` | `nonce`, `entity_type`, `entity_id`, `message` | Posts a comment |\n| `cora_ajax_resolve_comment` | `nonce`, `comment_id` | Marks comment as resolved |\n| `cora_ajax_get_comments` | `nonce`, `entity_type`, `entity_id` | Returns all comments for an entity |"
        ),

        // ── Document Vault ────────────────────────────────────────────────
        array(
            'slug'     => 'vault-overview',
            'title'    => 'Vault Overview',
            'category' => 'vault',
            'content'  => "# Document Vault — Overview\n\nThe Document Vault is a secure, agency-scoped document management and e-signature registry. It is the centerpiece of Cora's legal and compliance workflow.\n\n## Architecture\nVault documents are stored as WordPress attachments with metadata in `cora_workspace_vault_docs`. Each document has a full lifecycle from draft through e-signature to archived.\n\n## Document Lifecycle\n1. **Draft**: Created, edited, or uploaded by agency.\n2. **Staged**: Shared with client for review via a secure share link.\n3. **Signed**: Client applies digital signature through the e-sign pad.\n4. **Archived**: Final signed copy stored with a PDF watermark and timestamp.\n\n## Document Types\n- **Contracts**: Service agreements, NDAs, model releases.\n- **Invoices**: Linked to the Financials module.\n- **GST Certificates**: Generated from the GST Math Engine.\n- **Custom PDFs**: Upload any PDF for client e-signature.\n\n## Multi-tenant Isolation\nAll vault queries filter by `agency_id`. Documents from one tenant are never accessible to another."
        ),
        array(
            'slug'     => 'vault-esign',
            'title'    => 'E-Sign Registry',
            'category' => 'vault',
            'content'  => "# E-Sign Registry\n\nThe E-Sign Registry implements a 5-step guided signing workflow with GST verification, legally-compliant digital signature capture, and PDF rendering.\n\n## 5-Step Wizard\n1. **Details**: Document title, parties, and description.\n2. **Terms**: Full document preview with scroll-to-bottom acknowledgment requirement.\n3. **GST Math**: Real-time CGST/SGST/IGST breakdown verification.\n4. **E-Sign**: Canvas-based signature pad with typed name alternative.\n5. **Complete**: Timestamped signed PDF generated and stored.\n\n## Signature Capture\nSignatures are captured as base64 PNG via the HTML5 Canvas API and embedded into a server-rendered PDF alongside the document body.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_sign_document` | `nonce`, `doc_id`, `signature_data`, `signer_name` | Records e-signature and generates PDF |\n| `cora_ajax_get_vault_documents` | `nonce` | Returns all vault documents for the agency |\n| `cora_ajax_share_document_email` | `nonce`, `doc_id`, `recipient_email` | Emails secure sign link to client |\n\n## Database Schema\n### `cora_workspace_vault_docs`\n| Column | Type | Description |\n|---|---|---|\n| `id` | varchar(36) | UUID Primary Key |\n| `agency_id` | bigint | Tenant isolation |\n| `title` | varchar(255) | Document title |\n| `status` | varchar(50) | draft/staged/signed/archived |\n| `file_url` | varchar(500) | Signed PDF URL |\n| `signed_at` | datetime | Signature timestamp |\n| `signer_name` | varchar(255) | Signatory full name |\n| `secured_shares` | longtext | JSON array of share tokens |"
        ),
        array(
            'slug'     => 'vault-storage',
            'title'    => 'Document Storage',
            'category' => 'vault',
            'content'  => "# Document Storage\n\nThe Document Storage layer handles all file persistence, retrieval, and secure access control within the Vault.\n\n## Storage Backend\nFiles are stored in WordPress uploads (`/wp-content/uploads/cora-vault/{agency_id}/`). Each file is inaccessible directly — all downloads are routed through a PHP proxy that verifies agency_id scope before serving the binary.\n\n## Features\n- **Versioning**: Every re-upload creates a versioned snapshot.\n- **Encryption at Rest**: Sensitive PDFs can be optionally AES-256 encrypted.\n- **Expiring Links**: Secure share URLs include a hash and expiry timestamp.\n- **Audit Log**: All access events (view, download, sign) are logged to `cora_vault_audit_log`."
        ),

        // ── Communications ────────────────────────────────────────────────
        array(
            'slug'     => 'comms-email-studio',
            'title'    => 'Email Studio',
            'category' => 'communications',
            'content'  => "# Email Studio\n\nThe Email Studio is an SMTP-backed outbound email marketing and transactional messaging system for agencies.\n\n## Features\n- **Template Builder**: Drag-and-drop HTML email builder with agency branding.\n- **Sequences**: Multi-step drip campaigns with configurable delays.\n- **Transactional Triggers**: Auto-send emails on booking confirmation, invoice creation, and review requests.\n- **Personalization Tokens**: `{{client_name}}`, `{{booking_date}}`, `{{invoice_amount}}`.\n- **Open & Click Tracking**: Pixel-based open tracking and link click analytics.\n\n## SMTP Configuration\nEmail is sent via the SMTP server configured in Workspace Settings. Supports Gmail, Outlook, SendGrid, and Mailgun relay.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_send_email` | `nonce`, `to`, `subject`, `template_id`, `variables` | Sends a templated email |\n| `cora_ajax_get_email_templates` | `nonce` | Returns all email templates |\n| `cora_ajax_create_email_sequence` | `nonce`, `name`, `steps_json` | Creates a drip sequence |"
        ),
        array(
            'slug'     => 'comms-comments',
            'title'    => 'Comments Thread',
            'category' => 'communications',
            'content'  => "# Comments Thread\n\nSee **Comments System** in the Client Portal section for full documentation. The Communications module exposes the same comment infrastructure from the agency's internal workflow perspective.\n\n## Additional Features for Agency Staff\n- **Notification digests**: Daily or real-time digest emails for unread comments.\n- **Team assignment**: Route comment threads to specific team members.\n- **Internal notes**: Private comment mode visible only to agency staff, hidden from clients."
        ),
        array(
            'slug'     => 'comms-push-notifications',
            'title'    => 'Push Notifications (VAPID)',
            'category' => 'communications',
            'content'  => "# Push Notifications — VAPID Implementation\n\nShipped in **v3.2.46**, Cora's Web Push system delivers real-time alerts to subscribed agency staff on desktop and mobile browsers without requiring the app to be open.\n\n## Architecture\nCora implements the W3C Web Push Protocol using Voluntary Application Server Identification (VAPID) for authentication.\n\n## Flow\n1. **Key Generation**: On plugin activation, a VAPID ES256 key pair is generated and stored in `wp_options`.\n2. **Subscription**: The service worker registers a push subscription with the browser's push service (FCM for Chrome, Mozilla for Firefox). The subscription endpoint + auth keys are stored via the REST API.\n3. **Notification Dispatch**: PHP constructs a JWT signed with the private VAPID key, sends an HTTP POST to the browser's push service endpoint.\n4. **Service Worker**: Receives the `push` event, fetches notification details from the REST API, displays the notification.\n5. **Click Handler**: `notificationclick` event focuses the workspace or navigates to the relevant module.\n\n## REST API Endpoints\n| Method | Path | Description |\n|---|---|---|\n| POST | `/wp-json/cora-pwa/v1/save-subscription` | Stores a new push subscription |\n| GET | `/wp-json/cora-pwa/v1/get-notification` | Returns pending notification data for service worker |\n\n## Trigger Events\nPush notifications are automatically dispatched on:\n- New CRM lead saved\n- Booking/showing record created\n- High-priority client task updated\n\n## VAPID Signature (PHP)\nCora uses a self-contained ASN.1 DER-encoded ES256 JWT generator without requiring any external PHP extension."
        ),

        // ── Content & SEO ─────────────────────────────────────────────────
        array(
            'slug'     => 'content-suite',
            'title'    => 'Content Suite',
            'category' => 'content',
            'content'  => "# Content Suite\n\nThe Content Suite is an AI-powered content creation and publishing hub for agencies managing social media, blog posts, and marketing copy.\n\n## Features\n- **AI Content Generation**: Generate captions, blog posts, and email copy using the configured LLM (Gemini/Claude).\n- **Content Calendar**: Visual monthly publishing calendar.\n- **Platform Publishing**: Direct publish to Instagram (via Meta API), LinkedIn, and blog posts via WordPress.\n- **Brand Voice**: Per-agency brand voice configuration saved to the RAG layer for consistent AI output tone.\n- **Hashtag Intelligence**: AI-suggested hashtag clusters based on content topic and agency niche.\n\n## AJAX API Reference\n| Action | Parameters | Description |\n|---|---|---|\n| `cora_ajax_generate_content` | `nonce`, `type`, `prompt`, `brand_voice` | Generates AI content |\n| `cora_ajax_schedule_post` | `nonce`, `content_id`, `platform`, `publish_at` | Schedules a post |\n| `cora_ajax_get_content_calendar` | `nonce`, `month`, `year` | Returns publishing calendar |"
        ),
        array(
            'slug'     => 'content-seo-geo',
            'title'    => 'SEO & GEO Inspector',
            'category' => 'content',
            'content'  => "# SEO & GEO Inspector\n\nThe SEO & GEO Inspector audits agency website pages for both traditional Search Engine Optimization (SEO) and Generative Engine Optimization (GEO) — optimizing content for retrieval by AI language model search engines.\n\n## SEO Audit Features\n- Meta title and description validation\n- H1/H2/H3 hierarchy checker\n- Image alt text coverage\n- Page load performance score\n- Schema.org structured data detection\n\n## GEO Optimization Features\n- Answer-style content density scoring\n- Entity recognition and knowledge graph alignment\n- Citation-friendly paragraph formatting suggestions\n- FAQ schema generator for voice and AI search\n\n## Implementation\nAudit scans are run via server-side curl requests against the agency's Canvas-published pages. Results are stored per-page in `cora_seo_audits`."
        ),
        array(
            'slug'     => 'content-google-profile',
            'title'    => 'Google Business Profile',
            'category' => 'content',
            'content'  => "# Google Business Profile\n\nThe Google Business Profile module connects the agency's Google Business listing directly to the Cora workspace, enabling centralized management of business info, posts, Q&A, and review responses.\n\n## Features\n- **Profile Sync**: Pull current business name, address, hours, and categories.\n- **Post Publisher**: Create Google Business Posts (offers, events, updates) directly from Cora.\n- **Review Monitor**: View new reviews with AI-suggested response drafts.\n- **Photo Upload**: Upload photos to the Google listing from the Media Library.\n- **Q&A Manager**: Answer questions from the Google Maps listing panel.\n\n## OAuth Integration\nConnection uses Google's OAuth 2.0 flow with Business Profile API scopes. Tokens are stored encrypted in `wp_options`."
        ),

        // ── AI & Automation ────────────────────────────────────────────────
        array(
            'slug'     => 'ai-rag-knowledge-base',
            'title'    => 'RAG Knowledge Base',
            'category' => 'ai',
            'content'  => "# RAG Knowledge Base\n\nThe RAG (Retrieval-Augmented Generation) Knowledge Base stores agency-specific reference material that is automatically injected into AI context windows for accurate, personalized responses.\n\n## How It Works\n1. **Ingestion**: Agency documents, SOPs, FAQs, and client notes are uploaded or synced into `cora_rag_knowledge`.\n2. **Chunking**: Long documents are split into 512-token chunks with overlapping windows.\n3. **Retrieval**: On each AI query, the top-N most relevant chunks are fetched using keyword overlap scoring.\n4. **Augmentation**: Retrieved context is prepended to the system prompt before the LLM call.\n\n## Sync Sources\n- Document Vault items (contracts, SOPs)\n- Manual knowledge entries (FAQs, pricing, policies)\n- Google Business Profile reviews and Q&A\n\n## Database Schema\n### `cora_rag_knowledge`\n| Column | Type | Description |\n|---|---|---|\n| `id` | bigint | Primary Key |\n| `agency_id` | bigint | Tenant isolation |\n| `title` | varchar(255) | Document title |\n| `content` | longtext | Full text content |\n| `source` | varchar(100) | vault/manual/google_profile |\n| `indexed_at` | datetime | Last indexed timestamp |"
        ),

        // ── Settings & Tools ──────────────────────────────────────────────
        array(
            'slug'     => 'settings-audit-panel',
            'title'    => 'Audit Panel',
            'category' => 'settings',
            'content'  => "# Audit Panel\n\nThe Audit Panel provides a complete, tamper-evident log of all user actions and system events across the workspace.\n\n## Logged Events\n- User logins and session terminations\n- CRM record creates, updates, and deletes\n- Document vault access, downloads, and signatures\n- Financial record mutations\n- Permission matrix changes\n- API token rotations\n- MCP gateway connections\n\n## Features\n- **Date range filtering**: Narrow logs by custom date range.\n- **User filter**: View audit trail for a specific workspace user.\n- **Action type filter**: Filter by module or action category.\n- **Export**: Download full audit log as CSV for compliance reporting.\n\n## Database Schema\n### `cora_audit_log`\n| Column | Type | Description |\n|---|---|---|\n| `id` | bigint | Primary Key |\n| `agency_id` | bigint | Tenant isolation |\n| `user_id` | bigint | Acting user |\n| `action` | varchar(100) | Action label |\n| `entity_type` | varchar(50) | lead/document/invoice/etc. |\n| `entity_id` | bigint | Affected record |\n| `meta` | longtext | JSON details |\n| `created_at` | datetime | Event timestamp |"
        ),
        array(
            'slug'     => 'settings-roles',
            'title'    => 'Role Management',
            'category' => 'settings',
            'content'  => "# Role Management\n\nThe Role Management view allows workspace owners to define custom roles and assign fine-grained capability permissions to each role.\n\n## Built-in Roles\n- `cora_super_admin` — Global admin (immutable)\n- `owner` — Tenant owner (immutable)\n- `administrator` — Full workspace access\n- `cora_manager` — Configurable by owner\n- `cora_member` — Configurable by owner\n- `cora_branch_manager` — Read-only across all modules\n\n## Custom Roles\nOwners can create additional roles (e.g. `junior_editor`, `accounts_team`) with custom capability subsets.\n\n## Capability Matrix\nEach role is configured against a permissions matrix that maps capabilities to modules. Changes are saved to `cora_role_permissions` and immediately enforced on all AJAX actions via `current_user_can()` checks."
        ),

        // ── PWA & Mobile ──────────────────────────────────────────────────
        array(
            'slug'     => 'pwa-setup',
            'title'    => 'PWA Setup & Install',
            'category' => 'pwa',
            'content'  => "# PWA Setup & Install\n\nCora ships as a full Progressive Web App (PWA), enabling one-tap installation to the home screen on both Android and iOS devices.\n\n## Technical Requirements\n- HTTPS connection (required by all browsers)\n- Valid `cora-manifest.json` served at root scope\n- Service Worker registered at `/cora-service-worker.js`\n\n## Manifest Configuration\nThe PWA manifest is dynamically generated at `/cora-manifest.json` with:\n- `name`: Cora Workspace\n- `short_name`: Cora\n- `start_url`: `/workspace/dashboard`\n- `display`: `standalone`\n- `icons`: 192x192 and 512x512 PNG icons from the plugin assets\n\n## Install Prompt Banner (v3.2.46)\nThe workspace now shows a slide-in install prompt banner on first visit (bottom-right corner) with three options:\n- **Install App**: Triggers the browser `BeforeInstallPromptEvent`\n- **Later**: Dismisses for the current session\n- **Don't ask again**: Sets `localStorage.cora_pwa_never_prompt = true` permanently\n\nThe banner does not appear if:\n- The app is already running in `standalone` display mode\n- The user has previously selected 'Don't ask again'\n- The browser does not support PWA installation\n\n## iOS Install\nSafari on iOS does not fire `BeforeInstallPromptEvent`. The banner detects iOS and shows manual instructions ('Tap the Share icon → Add to Home Screen')."
        ),
        array(
            'slug'     => 'pwa-push-notifications',
            'title'    => 'Push Notifications (VAPID)',
            'category' => 'pwa',
            'content'  => "# Push Notifications — VAPID\n\nSee **Communications → Push Notifications (VAPID)** for the full technical reference. This page covers the PWA-specific setup and subscription management.\n\n## Enabling Push on a Device\n1. Open the workspace on the target device.\n2. When prompted for notification permission, click **Allow**.\n3. The service worker registers a Push subscription and posts it to `/wp-json/cora-pwa/v1/save-subscription`.\n\n## Subscription Storage\nSubscriptions are stored per-user in `wp_options` under `cora_pwa_subscriptions_{user_id}`. Multiple subscriptions (across different devices/browsers) are supported per user.\n\n## Managing Subscriptions\nUsers can revoke push permission at any time via browser notification settings. The service worker detects failed pushes and automatically cleans up stale subscriptions."
        ),
        array(
            'slug'     => 'pwa-service-worker',
            'title'    => 'Service Worker',
            'category' => 'pwa',
            'content'  => "# Service Worker\n\nThe Cora Service Worker (`/cora-service-worker.js`) is the backbone of the PWA layer, handling offline caching, background sync, and push notification delivery.\n\n## Registration\nThe service worker is registered at root scope (`/`) to intercept all workspace requests:\n```javascript\nnavigator.serviceWorker.register('/cora-service-worker.js', { scope: '/' });\n```\n\n## Caching Strategy\n- **Cache-First**: Static assets (JS, CSS, images) served from cache with background revalidation.\n- **Network-First**: AJAX/REST API requests always attempt network first, falling back to cache.\n- **Offline Page**: Uncached navigation requests fall back to `/cora-offline.html`.\n\n## Push Event Handler\nOn receiving a push event, the service worker:\n1. Fetches notification details from `/wp-json/cora-pwa/v1/get-notification?token={token}`.\n2. Displays the system notification with title, body, icon, and a `data.url` click action.\n\n## notificationclick Handler\nClicking the notification:\n1. Closes the notification.\n2. Attempts to focus an existing workspace window if open.\n3. Otherwise opens a new tab to the `data.url` destination."
        ),

        // ── Modules ───────────────────────────────────────────────────────
        array(
            'slug'     => 'module-vault',
            'title'    => 'Module: Document Vault',
            'category' => 'modules',
            'content'  => "# Module: Document Vault\n\nThe Document Vault module is Cora's legal and compliance layer. It provides e-signature workflows, secure document storage, GST verification, and client sharing for Indian service businesses.\n\n## Key Capabilities\n- **5-Step E-Sign Wizard**: Guided signing with GST math, terms acknowledgment, and canvas signature.\n- **PDF Renderer**: Server-side PDF generation from document templates.\n- **Secure Sharing**: Time-limited signed URLs for external document access.\n- **Agency Isolation**: All documents scoped by `agency_id`.\n\n## Integration Points\n- **CRM**: Automatically generates contracts from CRM lead data.\n- **Financials**: Invoice PDFs are archived directly into the Vault.\n- **RAG Layer**: Vault documents are indexed into the agency's RAG knowledge base.\n\n## Access Control\n- `administrator` / `owner`: Full CRUD on all vault documents.\n- `cora_branch_manager`: Read-only view of signed documents.\n- Clients: Access via secure share URL only (no workspace login required).\n\nSee **Document Vault** section in the sidebar for detailed sub-module documentation."
        ),
        array(
            'slug'     => 'module-content-suite',
            'title'    => 'Module: Content Suite',
            'category' => 'modules',
            'content'  => "# Module: Content Suite\n\nThe Content Suite module consolidates all content creation, publishing, SEO optimization, and Google Business Profile management into a single workspace panel.\n\n## Sub-Modules\n- **AI Content Generator**: LLM-powered caption, blog, and email copy creation.\n- **Publishing Calendar**: Visual scheduling across social platforms.\n- **SEO & GEO Inspector**: Page audit for traditional SEO and AI-search optimization.\n- **Google Business Profile**: Centralized GBP post, photo, and review management.\n\n## Industry Contexts\n- **Photography Studio**: Generates shoot portfolios, behind-the-scenes reels captions, and booking promo posts.\n- **Real Estate**: Generates property listing descriptions, neighborhood highlights, and open house announcements.\n\nSee the **Content & SEO** section in the sidebar for detailed sub-module documentation."
        ),
        array(
            'slug'     => 'module-forms',
            'title'    => 'Module: Forms Builder',
            'category' => 'modules',
            'content'  => "# Module: Forms Builder\n\nThe Forms Builder module enables agencies to create, deploy, and analyze custom forms for lead capture, onboarding, contracts, and feedback.\n\n## Key Capabilities\n- **No-code form builder** with drag-and-drop field layout\n- **Conditional logic** for dynamic form flows\n- **E-Sign integration** piping directly to Document Vault\n- **CRM lead creation** from form submissions\n- **Webhook export** for third-party automation\n- **Multi-step forms** with animated progress stepper\n\nSee the **Client Portal → Forms Builder** section in the sidebar for the full AJAX API and database schema reference."
        ),
        array(
            'slug'     => 'module-visual-builder',
            'title'    => 'Module: Visual Builder',
            'category' => 'modules',
            'content'  => "# Module: Visual Builder\n\nThe Visual Builder module provides a standalone page and layout design environment built on top of Elementor, with Cora-branded controls and white-labeling.\n\n## Relationship to Canvas\nThe Visual Builder is a simplified entry-point into the Canvas & Frontend Module. While Canvas manages full themes, Canvas Pages, and menus, the Visual Builder focuses on the in-browser drag-and-drop editing experience.\n\n## Features\n- Custom Cora 2-row toolbar overlaid on Elementor's editor\n- White-labeled exit to eliminate Elementor branding\n- Git integration drawer for version-controlled commits\n- Save Draft / Publish split button\n- Theme settings panel for global CSS and colors\n\nSee **Canvas & Frontend Module** in the sidebar for full technical documentation."
        ),
        array(
            'slug'     => 'module-pwa',
            'title'    => 'Module: PWA & Mobile Shell',
            'category' => 'modules',
            'content'  => "# Module: PWA & Mobile Shell\n\nShipped in **v3.2.46**, the PWA & Mobile Shell module transforms the Cora workspace into a fully installable Progressive Web App with offline support and real-time push notifications.\n\n## Components\n- **PWA Manifest**: Dynamic `/cora-manifest.json` with branded icons.\n- **Service Worker**: Caching, offline fallback, and push notification delivery.\n- **VAPID Push**: ES256 self-signed JWT push authorization.\n- **Install Prompt Banner**: Slide-in UI prompting users to install the app, with persistent 'never remind' option.\n- **REST API**: `/wp-json/cora-pwa/v1/` endpoints for subscription management and notification dispatch.\n\n## Access Control\n- Push subscription registration: Any authenticated workspace user.\n- Notification dispatch: Triggered server-side by CRM, Bookings, and Task Manager event hooks.\n- VAPID key management: Super Admin only.\n\nSee **PWA & Mobile** in the sidebar for detailed sub-module documentation."
        ),

        // ── Guides ────────────────────────────────────────────────────────
        array(
            'slug'     => 'guide-onboarding',
            'title'    => 'Onboarding Flow',
            'category' => 'guides',
            'content'  => "# Onboarding Flow\n\nThe Cora onboarding flow guides new workspace tenants through initial configuration in a step-by-step wizard.\n\n## Steps\n1. **Business Profile**: Agency name, logo, address, GSTIN, and industry selection.\n2. **Team Setup**: Invite initial team members and assign roles.\n3. **Workspace Configuration**: Activate modules relevant to the selected industry.\n4. **SMTP Configuration**: Connect outbound email (Gmail/SendGrid).\n5. **First Content**: Create first form, lead pipeline stage, or document template.\n6. **Go Live**: Confirm setup and land on the dashboard.\n\n## Industry Presets\nSelecting an industry at step 1 automatically activates the relevant module set:\n- **Photography Studio**: Crew Scheduler, Equipment, Media Library, Document Vault, Forms.\n- **Real Estate**: CRM Leads, Property Listings, Document Vault, Canvas Builder, Review Acquisition.\n- **General Agency**: CRM, Forms, Email Studio, Content Suite, Financials.\n\n## Implementation\nThe onboarding wizard is rendered at `/workspace/onboarding` and driven by `views/onboarding.php`."
        ),
        array(
            'slug'     => 'guide-pwa-install',
            'title'    => 'PWA Install Guide',
            'category' => 'guides',
            'content'  => "# PWA Install Guide\n\nThis guide explains how to install the Cora workspace as a Progressive Web App on your device for a native app-like experience.\n\n## Android (Chrome)\n1. Open `app.heycora.in` in Chrome.\n2. Look for the **Install Cora** banner at the bottom-right of the screen.\n3. Tap **Install App**.\n4. Confirm in the system dialog.\n5. Cora will appear on your home screen and app drawer.\n\n## iOS (Safari)\n1. Open `app.heycora.in` in Safari.\n2. Tap the **Share** button (rectangle with arrow).\n3. Scroll down and tap **Add to Home Screen**.\n4. Confirm with **Add** in the top-right.\n\n## Desktop (Chrome/Edge)\n1. Open `app.heycora.in` in Chrome or Edge.\n2. Click the install icon in the address bar (or use the **Install Cora** banner).\n3. Click **Install** in the dialog.\n\n## Troubleshooting\n- If you dismissed the banner and selected 'Don't ask again', clear `cora_pwa_never_prompt` from LocalStorage in DevTools to reset.\n- PWA installation requires HTTPS. The app will not install on `http://` connections.\n- If the banner never appears, verify the Service Worker is registered at `https://app.heycora.in/cora-service-worker.js`."
        ),
    );
}

// ── 8. PWA Changelog Entries (v3.2.46) ───────────────────────────────────

if ( ! function_exists( 'cora_docs_get_pwa_changelog_entries' ) ) {
function cora_docs_get_pwa_changelog_entries() {
    return array(
        array(
            'module_key'  => 'pwa',
            'version'     => '3.2.46',
            'status'      => 'released',
            'title'       => 'PWA Install Prompt & Push Notifications',
            'description' => 'Shipped the complete PWA layer. Service Worker now registered at root scope with Cache-First/Network-First strategies and offline fallback. VAPID ES256 key pair auto-generated on activation. Push subscription REST endpoints added. Slide-in install prompt banner implemented with localStorage-backed dismiss persistence.',
            'ticket_id'   => 'CORA-1001',
        ),
        array(
            'module_key'  => 'pwa',
            'version'     => '3.2.46',
            'status'      => 'released',
            'title'       => 'VAPID Backend Key System',
            'description' => 'Implemented self-contained ES256 ASN.1 DER VAPID JWT generator in PHP with no external library dependencies. VAPID public/private key pair stored in wp_options. Push subscription save and notification retrieval REST endpoints registered under /wp-json/cora-pwa/v1/.',
            'ticket_id'   => 'CORA-1002',
        ),
        array(
            'module_key'  => 'pwa',
            'version'     => '3.2.46',
            'status'      => 'released',
            'title'       => 'CRM & Bookings Push Triggers',
            'description' => 'Hooked push notification dispatch to CRM lead save, bookings/showings database insertion, and high-priority client task status updates. All triggers call cora_pwa_notify_agency() which fans out to all subscribed users.',
            'ticket_id'   => 'CORA-1003',
        ),
        array(
            'module_key'  => 'deploy',
            'version'     => '3.2.46',
            'status'      => 'released',
            'title'       => 'Deployment Pipeline CageFS Fix',
            'description' => 'Resolved unzip failures on Hostinger shared hosting caused by CageFS virtual /tmp isolation. Changed REMOTE_TMP path in deploy.sh from /tmp/ to /home/u484406462/ to guarantee write access across all SSH sessions.',
            'ticket_id'   => 'CORA-1004',
        ),
        array(
            'module_key'  => 'docs',
            'version'     => '3.2.46',
            'status'      => 'released',
            'title'       => 'Public Docs — Full Module Coverage',
            'description' => 'Expanded the public developer documentation to cover all 11 platform categories and 11 module pages. Added comprehensive architecture overviews, AJAX API reference tables, database schema documentation, and access control notes for Media, Operations, Client Portal, Document Vault, Communications, Content & SEO, and PWA modules.',
            'ticket_id'   => 'CORA-1005',
        ),
    );
}
}
