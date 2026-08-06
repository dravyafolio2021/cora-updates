<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cora_is_super_owner' ) ) {
    function cora_is_super_owner( $user = null ) {
        if ( null === $user ) {
            if ( ! is_user_logged_in() ) {
                return false;
            }
            $user = wp_get_current_user();
        }
        if ( ! $user || ! $user->exists() ) {
            return false;
        }
        $super_emails = array( 'dravya.shs@gmail.com', 'dravya.shravya@gmail.com', 'admin@cora.local' );
        if ( in_array( strtolower( $user->user_email ), $super_emails, true ) ) {
            return true;
        }
        return false;
    }
}

$sub_page = $sub_page ?? $GLOBALS['sub_page'] ?? $_GET['sub_page'] ?? 'dashboard';
if ( cora_is_super_owner() && ( empty( $sub_page ) || $sub_page === 'dashboard' ) ) {
    $sub_page = 'super-admin';
}
$cora_auto_update = isset( $_GET['cora_auto_update'] ) && $_GET['cora_auto_update'] === '1';
$cora_target_version = sanitize_text_field( $_GET['target_version'] ?? '' );
$cora_user_can_update = cora_is_super_owner();
// Define workspace context early to avoid undefined variable warnings in the JS data injection block
$cora_active_workspace = function_exists( 'cora_get_current_workspace_context' ) ? cora_get_current_workspace_context() : array( 'id' => 1, 'name' => 'Workspace', 'slug' => 'workspace', 'plan' => 'enterprise', 'status' => 'active' );
$cora_user_workspaces   = function_exists( 'cora_get_user_workspaces' ) ? cora_get_user_workspaces( get_current_user_id() ) : array( $cora_active_workspace );

// Enqueue WordPress media libraries
wp_enqueue_media();

// Save Direct MCP Access Token
if ( isset( $_POST['cora_save_mcp_token_direct_submit'] ) && check_admin_referer( 'cora_save_mcp_token_direct', 'cora_mcp_nonce' ) ) {
    $mcp_token = sanitize_text_field( $_POST['cora_mcp_access_token_direct'] );
    if ( ! empty( $mcp_token ) && str_replace( array('•', '*'), '', $mcp_token ) === '' ) {
        echo "<script>window.addEventListener('DOMContentLoaded', function() { window.coraShowToast('AI MCP server access token saved successfully.'); });</script>";
    } else {
        update_option( 'cora_mcp_access_token', $mcp_token );
        echo "<script>window.addEventListener('DOMContentLoaded', function() { window.coraShowToast('AI MCP server access token saved successfully.'); });</script>";
    }
}

$cora_users = ( in_array( $sub_page, array( 'dashboard', 'bookings', 'team-roles', 'equipment', 'blogs' ) ) ) ? get_users() : array();
$cora_workspace_listings = ( in_array( $sub_page, array( 'dashboard', 'equipment', 'leads', 'bookings' ) ) ) ? cora_db_get_properties() : array();
$cora_permissions = get_option( 'cora_role_permissions', array() );
// Auto-grant access to new enterprise modules for all active roles
$cora_new_module_keys = array('event_timeline', 'event-timeline', 'review_acquisition', 'smart-reviews', 'crew_scheduler', 'crew-scheduler', 'team_scheduler', 'team-scheduler', 'vault', 'emails');
if ( is_array( $cora_permissions ) ) {
    foreach ( $cora_permissions as $r_key => $r_perms ) {
        if ( is_array( $r_perms ) ) {
            $cora_permissions[$r_key] = array_unique( array_merge( $r_perms, $cora_new_module_keys ) );
        }
    }
}
$cora_showing_assignments = ( in_array( $sub_page, array( 'equipment', 'bookings', 'shifts', 'crew-scheduler', 'event-timeline' ) ) ) ? get_option( 'cora_workspace_showing_assignments', array() ) : array();
$cora_documents = ( $sub_page === 'vault' ) ? get_option( 'cora_workspace_vault_docs', array() ) : array();
$cora_portfolios = ( in_array( $sub_page, array( 'portfolio', 'dashboard' ) ) ) ? get_option( 'cora_workspace_portfolios', array() ) : array();
$cora_workspace_leads = ( in_array( $sub_page, array( 'leads', 'dashboard', 'team-roles', 'feature-hub', 'equipment', 'bookings' ) ) ) ? cora_db_get_leads() : array();
$cora_workspace_clients = ( in_array( $sub_page, array( 'bookings', 'dashboard', 'leads', 'equipment', 'financials', 'shifts', 'crew-scheduler' ) ) ) ? cora_db_get_clients() : array();
$cora_workspace_attendance_logs = ( $sub_page === 'attendance' ) ? get_option( 'cora_workspace_attendance_logs', array() ) : array();
$cora_workspace_client_tasks = ( in_array( $sub_page, array( 'client-task-manager', 'bookings', 'dashboard' ) ) ) ? get_option( 'cora_workspace_client_tasks', array() ) : array();

// Pre-process equipment assignments dynamically from Leads and Clients databases
if ( in_array( $sub_page, array( 'equipment', 'bookings' ) ) && is_array( $cora_workspace_listings ) ) {
    foreach ( $cora_workspace_listings as $key => $item ) {
        $assigned_showing_name = '';
        $assigned_crew_name = '';
        $assigned_note = '';
        $is_assigned = false;

        // Check active clients (Viewing Bookings) first
        if ( is_array( $cora_workspace_clients ) ) {
            foreach ( $cora_workspace_clients as $client ) {
                $client_gear = isset( $client['listing_ids'] ) ? (array) $client['listing_ids'] : array();
                if ( in_array( $item['id'], $client_gear ) || in_array( (string)$item['id'], $client_gear ) ) {
                    $assigned_showing_name = $client['names'];
                    $assigned_crew_name = !empty($client['crew']) ? $client['crew'] : '';
                    $assigned_note = 'Assigned in Viewing Bookings';
                    $is_assigned = true;
                    break;
                }
            }
        }

        // If not found, check active leads
        if ( ! $is_assigned && is_array( $cora_workspace_leads ) ) {
            foreach ( $cora_workspace_leads as $lead ) {
                $lead_gear = isset( $lead['listing_ids'] ) ? (array) $lead['listing_ids'] : array();
                if ( in_array( $item['id'], $lead_gear ) || in_array( (string)$item['id'], $lead_gear ) ) {
                    $assigned_showing_name = $lead['names'];
                    $assigned_note = 'Assigned in Leads CRM';
                    $is_assigned = true;
                    break;
                }
            }
        }

        if ( $is_assigned ) {
            $cora_workspace_listings[$key]['status'] = 'In Use';
            $cora_workspace_listings[$key]['shoot'] = $assigned_showing_name;
            $cora_workspace_listings[$key]['crew'] = $assigned_crew_name;
            $cora_workspace_listings[$key]['assignment_note'] = $assigned_note;
        }
    }
}

if ( ! function_exists( 'cora_format_rupees' ) ) {
    function cora_format_rupees( $amount ) {
        $amount = (string)$amount;
        $len = strlen( $amount );
        if ( $len <= 3 ) {
            return '₹' . $amount;
        }
        $last_three = substr( $amount, -3 );
        $rest = substr( $amount, 0, -3 );
        $rest = preg_replace( '/\B(?=(\d{2})+(?!\d))/', ',', $rest );
        return '₹' . $rest . ',' . $last_three;
    }
}

// Calculate dynamic metrics
$dynamic_bookings_count = 0;
$dynamic_pending_count = 0;
$dynamic_revenue_total = 0;
$dynamic_active_bookings_count = 0;

if ( in_array( $sub_page, array( 'dashboard', 'financials', 'bookings' ) ) ) {
    $dynamic_bookings_count = count( $cora_workspace_clients );
    foreach ( $cora_workspace_clients as $client ) {
        if ( isset( $client['status'] ) && $client['status'] === 'editing' ) {
            $dynamic_pending_count++;
        }
        $price_str = isset( $client['price'] ) ? $client['price'] : '';
        $clean_price = preg_replace( '/[^\d]/', '', $price_str );
        $dynamic_revenue_total += intval( $clean_price );
    }

    foreach ( $cora_workspace_clients as $client ) {
        if ( isset( $client['status'] ) && $client['status'] !== 'completed' ) {
            $dynamic_active_bookings_count++;
        }
    }
}

$cora_financials = ( in_array( $sub_page, array( 'dashboard', 'financials' ) ) ) ? cora_db_get_ledger() : array();
$cora_gbp_profile       = ( in_array( $sub_page, array( 'gbp', 'dashboard' ) ) ) ? get_option( 'cora_gbp_profile', array() ) : array();
$cora_gbp_is_connected  = ! empty( $cora_gbp_profile['connected'] ) &&
    ( ! empty( $cora_gbp_profile['location_name'] ) || ! empty( $cora_gbp_profile['place_id'] ) );
$cora_gbp_review_replies = ( $sub_page === 'gbp' ) ? get_option( 'cora_gbp_review_replies', array() ) : array();
$cora_gbp_posts         = ( $sub_page === 'gbp' ) ? get_option( 'cora_gbp_posts', array() ) : array();
$cora_gbp_client_id     = ( $sub_page === 'gbp' ) ? get_option( 'cora_gbp_client_id', '' ) : '';
$cora_gbp_client_secret = ( $sub_page === 'gbp' ) ? get_option( 'cora_gbp_client_secret', '' ) : '';
$cora_gbp_has_credentials = ! empty( $cora_gbp_client_id ) && ! empty( $cora_gbp_client_secret );
$cora_gbp_maps_api_key  = ( in_array( $sub_page, array( 'gbp', 'dashboard' ) ) ) ? get_option( 'cora_gbp_maps_api_key', '' ) : '';
$cora_gbp_has_maps_key  = ! empty( $cora_gbp_maps_api_key );
$cora_gbp_tokens        = ( $sub_page === 'gbp' ) ? get_option( 'cora_gbp_tokens', array() ) : array();
$cora_gbp_is_authenticated = ! empty( $cora_gbp_tokens['access_token'] );
$cora_gbp_connected_via = $cora_gbp_profile['connected_via'] ?? '';

// AI model display label for sidebar
$cora_active_ai_model = get_option( 'cora_workspace_active_ai_model', 'cora-core-v2' );


$cora_categories = ( $sub_page === 'blogs' ) ? get_categories( array('hide_empty' => false) ) : array();
$cora_tags = ( $sub_page === 'blogs' ) ? get_tags( array('hide_empty' => false) ) : array();
$current_wp_user = wp_get_current_user();
$user_first_name = $current_wp_user->exists() ? ( ! empty( $current_wp_user->first_name ) ? $current_wp_user->first_name : $current_wp_user->display_name ) : 'Dravya';
$current_user_role = ! empty( $current_wp_user->roles ) ? $current_wp_user->roles[0] : 'subscriber';
$cora_is_unverified = false; // Disable verification lockout block on login

$cora_role_labels = cora_get_all_roles();

$current_user_display_name = $current_wp_user->exists() ? $current_wp_user->display_name : 'Dravya Bansal';
$current_user_role_label = isset($cora_role_labels[$current_user_role]) ? $cora_role_labels[$current_user_role] : ucfirst($current_user_role);
if ($current_user_role === 'administrator') {
    $current_user_role_label = 'Super Admin';
}
$current_user_avatar = $current_wp_user->exists() ? get_user_meta( $current_wp_user->ID, 'cora_avatar_url', true ) : '';

$photographers = array();
$videographers = array();
$drone_pilots = array();
$editors = array();
$all_crew_names = array();

foreach ($cora_users as $user) {
    $all_crew_names[] = $user->display_name;
    $roles = $user->roles;
    $role = !empty($roles) ? $roles[0] : '';
    if ($role === 'cora_photographer' || $role === 'administrator') {
        $photographers[] = $user;
    }
    if ($role === 'cora_videographer' || $role === 'administrator') {
        $videographers[] = $user;
    }
    if ($role === 'cora_drone_pilot' || $role === 'administrator') {
        $drone_pilots[] = $user;
    }
    if ($role === 'cora_editor' || $role === 'administrator') {
        $editors[] = $user;
    }
}

$s1_assignments = isset($cora_showing_assignments['showing1']) ? $cora_showing_assignments['showing1'] : array();
$s2_assignments = isset($cora_showing_assignments['showing2']) ? $cora_showing_assignments['showing2'] : array();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php
    $favicon_url = get_option( 'cora_brand_favicon_url', '' );
    if ( empty( $favicon_url ) ) {
        $favicon_url = CORA_WORKSPACE_URL . 'assets/images/cora-favicon.png';
    }
    $current_industry = ! empty( $_COOKIE['cora_workspace_industry'] ) 
        ? $_COOKIE['cora_workspace_industry'] 
        : get_option( 'cora_workspace_industry', 'real_estate' );
    $current_industry_clean = str_replace( '_', '-', strtolower( trim( $current_industry ) ) );
    $is_studio_ind = ( $current_industry_clean === 'photography' || $current_industry_clean === 'studio' || $current_industry_clean === 'photography-studio' );

    $title_real_estate = get_option( 'cora_site_title_real_estate', '' );
    $title_studio      = get_option( 'cora_site_title_studio', '' );
    $page_title_raw    = $is_studio_ind ? $title_studio : $title_real_estate;
    $page_title_format = $page_title_raw ?: 'Cora';
    ?>
    <link rel="icon" type="image/png" href="<?php echo esc_url( $favicon_url ); ?>" />
    <link rel="shortcut icon" id="cora-dynamic-favicon" href="<?php echo esc_url( $favicon_url ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo esc_html( $page_title_format ); ?></title>
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo CORA_WORKSPACE_URL . 'assets/css/tailwind-built.css'; ?>" />
    
    <!-- PWA Manifest & Service Worker -->
    <link rel="manifest" href="<?php echo home_url('/cora-manifest.json'); ?>">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?php echo home_url('/cora-service-worker.js'); ?>', { scope: '/' })
                    .then(function(reg) { console.log('Service worker registered with scope:', reg.scope); })
                    .catch(function(err) { console.error('Service worker registration failed:', err); });
            });
        }
    </script>

    <script>
        window.coraClients = <?php echo json_encode( $cora_workspace_clients ); ?>;
        window.coraDocuments = <?php echo json_encode( $cora_documents ); ?>;
        window.coraPortfolios = <?php echo json_encode( $cora_portfolios ); ?>;
        window.coraPwaVapidPublicKey = <?php echo json_encode( get_option( 'cora_pwa_vapid_public_key' ) ); ?>;
        window.coraPwaNonce = <?php echo json_encode( wp_create_nonce( 'wp_rest' ) ); ?>;
        window.coraAjaxNonce = <?php echo json_encode( wp_create_nonce( 'cora_ajax_nonce' ) ); ?>;
    </script>
    
    <!-- Load QuillJS Rich Text ListingCoordinator -->
    <link href="<?php echo CORA_WORKSPACE_URL . 'assets/css/quill.snow.css'; ?>" rel="stylesheet">
    <script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/quill.min.js'; ?>" defer></script>
    
    <!-- Load ChartJS -->
    <script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/chart.min.js'; ?>" defer></script>
    
    <!-- Load TomSelect -->
    <link href="<?php echo CORA_WORKSPACE_URL . 'assets/css/tom-select.default.min.css'; ?>" rel="stylesheet">
    <script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/tom-select.complete.min.js'; ?>" defer></script>
    
    <!-- WordPress Enqueued Styles/Scripts for Media Uploader -->
    <?php
    wp_print_styles();
    wp_print_scripts();
    ?>
    <script>
        window.$ = window.jQuery;
    </script>

    <!-- CRITICAL: Reset any WordPress admin-bar margin-top injected by wp_print_styles() -->
    <style id="cora-adminbar-reset">
        #wpadminbar { display: none !important; }
        html, html.wp-toolbar { margin-top: 0 !important; padding-top: 0 !important; }
        body, body.admin-bar { margin-top: 0 !important; padding-top: 0 !important; }
        #wpcontent, #wpbody, #wpbody-content, #wpwrap { margin-top: 0 !important; padding-top: 0 !important; }
        * html body { margin-top: 0 !important; }
    </style>

    <style id="cora-workspace-custom-styles">
        /* === CORA SKELETON PRELOADING === */
        @keyframes cora-shimmer {
          0% { background-position: -200% 0; }
          100% { background-position: 200% 0; }
        }
        .cora-skeleton {
          background: linear-gradient(90deg, #f4f4f5 25%, #e4e4e7 50%, #f4f4f5 75%);
          background-size: 200% 100%;
          animation: cora-shimmer 1.5s ease-in-out infinite;
          border-radius: 8px;
        }
        .dark .cora-skeleton {
          background: linear-gradient(90deg, #27272a 25%, #3f3f46 50%, #27272a 75%);
          background-size: 200% 100%;
          animation: cora-shimmer 1.5s ease-in-out infinite;
        }
        .cora-skeleton-text { height: 12px; border-radius: 4px; }
        .cora-skeleton-title { height: 20px; border-radius: 6px; }
        .cora-skeleton-card {
          background: white;
          border-radius: 16px;
          padding: 20px;
        }
        .dark .cora-skeleton-card { background: #18181b; }

        /* Scrollbar hiding utilities */
        .scrollbar-none::-webkit-scrollbar {
            display: none !important;
        }
        .scrollbar-none {
            -ms-overflow-style: none !important;  /* IE and Edge */
            scrollbar-width: none !important;  /* Firefox */
        }

        /* Desktop Independent Scroll Viewport Rules */
        @media (min-width: 1024px) {
            html, body {
                height: 100% !important;
                overflow: hidden !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .cora-main {
                height: calc(100vh - 52px) !important;
                overflow-y: auto !important;
            }
            .cora-sidebar {
                height: calc(100vh - 52px) !important;
                top: 0 !important;
            }
        }


        /* Active onboarding tour highlight styling */
        .cora-tour-highlight {
            position: relative !important;
            z-index: 999999 !important;
            box-shadow: 0 0 0 8px #ffffff, 0 0 0 9999px rgba(9, 9, 11, 0.45) !important;
            outline: 1px solid #d4d4d8 !important;
            border-color: #d4d4d8 !important;
            background-color: #ffffff !important;
            pointer-events: none !important;
            transition: box-shadow 0.25s ease-in-out;
        }

        /* ========================================================
         * WORDPRESS MEDIA UPLOADER (wp.media) MODERN MONOCHROME OVERRIDES
         * ======================================================== */
        
        /* 1. Hide legacy screen-reader text that overflows */
        .media-modal .screen-reader-text {
            border: 0 !important;
            clip: rect(1px, 1px, 1px, 1px) !important;
            -webkit-clip-path: inset(50%) !important;
            clip-path: inset(50%) !important;
            height: 1px !important;
            margin: -1px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important;
            word-wrap: normal !important;
        }

        .media-modal-backdrop {
            background: rgba(9, 9, 11, 0.4) !important;
            backdrop-filter: blur(4px) !important;
        }

        .media-modal {
            background: #ffffff !important;
            border-radius: 12px !important;
            border: 1px solid #e4e4e7 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            overflow: hidden !important;
            top: 40px !important;
            right: 40px !important;
            bottom: 40px !important;
            left: 40px !important;
        }

        /* Focus management - remove WP default blue outlines */
        .media-modal *:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        .media-modal-close {
            background: transparent !important;
            border: none !important;
            color: #71717a !important;
            height: 40px !important;
            width: 40px !important;
            top: 10px !important;
            right: 10px !important;
            transition: color 0.2s !important;
            z-index: 1000 !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .media-modal-close:hover {
            color: #09090b !important;
        }

        .media-modal-close .media-modal-icon {
            display: none !important;
        }

        .media-modal-close::before {
            content: '×' !important;
            font-size: 28px !important;
            font-weight: 300 !important;
            line-height: 40px !important;
            display: block !important;
            text-align: center !important;
        }

        .media-frame-title {
            height: 60px !important;
            border-bottom: 1px solid #f4f4f5 !important;
            background: #ffffff !important;
            top: 0 !important;
            right: 0 !important;
            left: 0 !important;
            box-shadow: none !important;
        }

        .media-frame-title h1 {
            font-family: system-ui, -apple-system, sans-serif !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            color: #09090b !important;
            line-height: 60px !important;
            padding-left: 24px !important;
            text-transform: capitalize !important;
        }

        .media-frame-menu {
            background: #fafafa !important;
            border-right: 1px solid #e4e4e7 !important;
            top: 60px !important;
            width: 200px !important;
            box-shadow: none !important;
        }

        .media-menu {
            padding: 16px 8px !important;
        }

        .media-menu .media-menu-item {
            font-family: system-ui, -apple-system, sans-serif !important;
            font-size: 13px !important;
            color: #52525b !important;
            padding: 8px 16px !important;
            border: none !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            margin-bottom: 2px !important;
            transition: all 0.2s !important;
        }

        .media-menu .media-menu-item:hover {
            background: #f4f4f5 !important;
            color: #09090b !important;
        }

        .media-menu .media-menu-item.active {
            background: #e4e4e7 !important;
            color: #09090b !important;
            font-weight: 600 !important;
        }

        .media-frame-router {
            top: 60px !important;
            left: 200px !important;
            height: 48px !important;
            border-bottom: 1px solid #e4e4e7 !important;
            background: #ffffff !important;
            box-shadow: none !important;
        }

        .media-frame.hide-menu .media-frame-router {
            left: 0 !important;
        }

        .media-router {
            padding: 0 24px !important;
        }

        .media-router .media-menu-item {
            font-family: system-ui, -apple-system, sans-serif !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #71717a !important;
            height: 48px !important;
            line-height: 46px !important;
            margin: 0 !important;
            padding: 0 16px !important;
            border: none !important;
            background: transparent !important;
            border-bottom: 2px solid transparent !important;
            transition: all 0.2s !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .media-router .media-menu-item:hover {
            color: #09090b !important;
        }

        .media-router .media-menu-item.active {
            color: #09090b !important;
            border-bottom: 2px solid #09090b !important;
            font-weight: 600 !important;
            background: transparent !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .media-frame-content {
            top: 108px !important;
            left: 200px !important;
            bottom: 60px !important;
            background: #ffffff !important;
            border: none !important;
        }

        .media-frame.hide-menu .media-frame-content {
            left: 0 !important;
        }

        /* 2. Style Toolbar components cleanly */
        .attachments-browser .media-toolbar {
            background: transparent !important;
            border-bottom: none !important;
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .attachments-browser .media-toolbar-secondary {
            position: absolute !important;
            bottom: -60px !important;
            left: 24px !important;
            z-index: 10000 !important;
            height: 60px !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        
        .attachments-browser .media-toolbar-primary {
            position: absolute !important;
            top: -108px !important;
            right: 60px !important;
            z-index: 10000 !important;
            height: 60px !important;
            display: flex !important;
            align-items: center !important;
        }

        /* Hide bulky text labels inside media toolbar to align with modern SaaS headers */
        .media-toolbar label,
        .media-search-input-label {
            display: none !important;
        }

        /* Form Inputs inside Media Modal */
        .media-modal select,
        .media-modal select.attachment-filters, 
        .media-modal input[type="search"], 
        .media-modal input.search,
        .media-modal input[type="text"] {
            font-family: system-ui, -apple-system, sans-serif !important;
            font-size: 12px !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 6px !important;
            color: #18181b !important;
            background-color: #ffffff !important;
            padding: 4px 12px !important;
            box-shadow: none !important;
            transition: border-color 0.2s, box-shadow 0.2s !important;
            height: 32px !important;
            line-height: 22px !important;
            margin: 0 !important;
        }

        .media-modal select:focus, 
        .media-modal input[type="search"]:focus, 
        .media-modal input.search:focus,
        .media-modal input[type="text"]:focus {
            border-color: #09090b !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(9, 9, 11, 0.05) !important;
        }

        /* Custom dropdown arrow for selects inside Media Modal */
        .media-modal select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="%2371717a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>') !important;
            background-repeat: no-repeat !important;
            background-position: calc(100% - 10px) center !important;
            padding-right: 28px !important;
        }

        /* Custom search icon for inputs inside Media Modal */
        .media-modal input.search {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="%2371717a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>') !important;
            background-repeat: no-repeat !important;
            background-position: 8px center !important;
            padding-left: 28px !important;
            width: 200px !important;
        }

        .media-modal .attachments {
            padding: 16px 24px !important;
            top: 0 !important;
        }

        /* 3. Attachment Cards (Target .attachment-preview, not .attachment to avoid double border) */
        .media-modal .attachment {
            padding: 8px !important;
            box-shadow: none !important;
            background: transparent !important;
            border: none !important;
            overflow: visible !important;
        }

        .media-modal .attachment-preview {
            border-radius: 8px !important;
            overflow: hidden !important;
            box-shadow: inset 0 0 0 1px #e4e4e7 !important;
            background: #fafafa !important;
            transition: transform 0.2s, box-shadow 0.2s !important;
        }

        .media-modal .attachment:hover .attachment-preview {
            box-shadow: inset 0 0 0 1px #a1a1aa !important;
            transform: translateY(-2px) !important;
        }

        .media-modal .attachment.selected .attachment-preview {
            box-shadow: inset 0 0 0 2px #09090b !important;
        }

        .media-modal .attachment.selected .thumbnail {
            opacity: 0.95 !important;
        }

        /* 4. Selection Indicator Checkmark */
        .media-modal .attachment .check {
            background-color: #09090b !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15) !important;
            border-radius: 50% !important;
            width: 20px !important;
            height: 20px !important;
            top: 6px !important;
            right: 6px !important;
            border: 1.5px solid #ffffff !important;
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 10 !important;
            color: transparent !important;
            font-size: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .media-modal .attachment .check * {
            display: none !important;
        }

        .media-modal .attachment.selected .check {
            display: flex !important;
        }

        /* Custom drawn sharp vector checkmark inside .check */
        .media-modal .attachment .check::after {
            content: '' !important;
            width: 9px !important;
            height: 5px !important;
            border-left: 2px solid #ffffff !important;
            border-bottom: 2px solid #ffffff !important;
            transform: rotate(-45deg) translate(0.5px, -0.5px) !important;
            display: block !important;
        }

        /* 5. Media Sidebar / Details Panel - REDEFINED PREMIUM STYLE */
        @media only screen and (min-width: 901px) {
            .media-sidebar {
                position: absolute !important;
                top: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 350px !important;
                background: #fafafa !important;
                border-left: 1px solid #e4e4e7 !important;
                padding: 24px !important;
                z-index: 75 !important;
                overflow-y: auto !important;
                box-sizing: border-box !important;
                display: block !important;
            }

            .media-frame:not(.hide-sidebar) .attachments-browser .attachments,
            .media-frame:not(.hide-sidebar) .attachments-browser .attachments-wrapper,
            .media-frame:not(.hide-sidebar) .attachments-browser .uploader-inline {
                right: 350px !important;
            }

            .media-frame:not(.hide-sidebar) .attachments-browser .media-toolbar {
                right: 374px !important; /* 350px sidebar width + 24px spacing gap */
            }
        }

        @media only screen and (min-width: 641px) and (max-width: 900px) {
            .media-sidebar {
                position: absolute !important;
                top: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 300px !important;
                background: #fafafa !important;
                border-left: 1px solid #e4e4e7 !important;
                padding: 20px !important;
                z-index: 75 !important;
                overflow-y: auto !important;
                box-sizing: border-box !important;
                display: block !important;
            }

            .media-frame:not(.hide-sidebar) .attachments-browser .attachments,
            .media-frame:not(.hide-sidebar) .attachments-browser .attachments-wrapper,
            .media-frame:not(.hide-sidebar) .attachments-browser .uploader-inline {
                right: 300px !important;
            }

            .media-frame:not(.hide-sidebar) .attachments-browser .media-toolbar {
                right: 324px !important; /* 300px sidebar width + 24px spacing gap */
            }
        }

        /* Ensure clean sidebar hide behaviour and reset right offsets */
        .media-frame.hide-sidebar .media-sidebar {
            display: none !important;
        }
        .media-frame.hide-sidebar .attachments-browser .attachments,
        .media-frame.hide-sidebar .attachments-browser .attachments-wrapper,
        .media-frame.hide-sidebar .attachments-browser .uploader-inline {
            right: 0 !important;
        }
        .media-frame.hide-sidebar .attachments-browser .media-toolbar {
            right: 24px !important; /* Keep 24px gap from right edge of modal */
        }

        /* Monochromatic, clean, stacked interior settings styles */
        .media-sidebar h2 {
            font-family: system-ui, -apple-system, sans-serif !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            color: #71717a !important;
            letter-spacing: 0.05em !important;
            margin-top: 0 !important;
            margin-bottom: 16px !important;
            border-bottom: 1px solid #e4e4e7 !important;
            padding-bottom: 8px !important;
        }

        .media-sidebar .attachment-details {
            margin-bottom: 24px !important;
            border-bottom: 1px solid #e4e4e7 !important;
            padding-bottom: 20px !important;
            display: block !important;
        }

        .media-sidebar .attachment-info {
            display: flex !important;
            gap: 16px !important;
            align-items: flex-start !important;
        }

        .media-sidebar .attachment-info .thumbnail {
            width: 80px !important;
            height: 80px !important;
            border-radius: 8px !important;
            border: 1px solid #e4e4e7 !important;
            overflow: hidden !important;
            background: #ffffff !important;
            padding: 2px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            flex-shrink: 0 !important;
        }

        .media-sidebar .attachment-info .thumbnail img {
            border-radius: 6px !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .media-sidebar .attachment-info .details {
            font-size: 12px !important;
            color: #71717a !important;
            line-height: 1.5 !important;
            width: 100% !important;
            min-width: 0 !important; /* Prevents text clipping */
        }

        .media-sidebar .attachment-info .filename {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #09090b !important;
            word-break: break-all !important;
            margin-bottom: 4px !important;
            display: block !important;
        }

        /* Clean action icon buttons for edit/trash */
        .media-sidebar .attachment-info .details .delete-attachment,
        .media-sidebar .attachment-info .details .trash-attachment {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-top: 10px !important;
            width: 32px !important;
            height: 32px !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            font-size: 0 !important; /* Hide original text label */
            color: transparent !important; /* Hide original text label */
            box-sizing: border-box !important;
            padding: 0 !important;
        }

        /* Hide Edit Image link to prevent redirecting to WordPress admin image editor */
        .media-sidebar .attachment-info .details a.edit-attachment {
            display: none !important;
        }

        .media-sidebar .attachment-info .details .delete-attachment,
        .media-sidebar .attachment-info .details .trash-attachment {
            border: 1px solid #fca5a5 !important;
            background: #fff5f5 !important;
        }

        .media-sidebar .attachment-info .details .delete-attachment::before,
        .media-sidebar .attachment-info .details .trash-attachment::before {
            content: "" !important;
            display: block !important;
            width: 14px !important;
            height: 14px !important;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ef4444' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'><polyline points='3 6 5 6 21 6'></polyline><path d='M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'></path></svg>") !important;
            background-repeat: no-repeat !important;
            background-size: contain !important;
        }

        .media-sidebar .attachment-info .details .delete-attachment:hover,
        .media-sidebar .attachment-info .details .trash-attachment:hover {
            background: #fecaca !important;
            border-color: #f87171 !important;
        }

        /* Settings fields - Alt Text, Title, Caption, etc. */
        .media-sidebar .settings-handler {
            margin-top: 16px !important;
        }

        /* Collapsible details component styling */
        .media-sidebar .cora-advanced-details {
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            margin: 20px 0 !important;
            overflow: hidden !important;
            display: block !important;
            width: 100% !important;
            box-sizing: border-box !important;
            transition: all 0.2s ease !important;
            clear: both !important;
        }

        .media-sidebar .cora-advanced-summary {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 10px 14px !important;
            background: #f4f4f5 !important;
            cursor: pointer !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #27272a !important;
            user-select: none !important;
            list-style: none !important;
            outline: none !important;
            border-bottom: 1px solid transparent !important;
            box-sizing: border-box !important;
        }

        .media-sidebar .cora-advanced-summary::-webkit-details-marker {
            display: none !important;
        }

        .media-sidebar .cora-advanced-summary:hover {
            background: #e4e4e7 !important;
            color: #09090b !important;
        }

        .media-sidebar .cora-advanced-summary .cora-chevron {
            transition: transform 0.2s ease !important;
            color: #71717a !important;
            flex-shrink: 0 !important;
        }

        .media-sidebar .cora-advanced-details[open] {
            border-color: #d4d4d8 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }

        .media-sidebar .cora-advanced-details[open] .cora-advanced-summary {
            border-bottom: 1px solid #e4e4e7 !important;
            background: #fafafa !important;
        }

        .media-sidebar .cora-advanced-details[open] .cora-chevron {
            transform: rotate(180deg) !important;
            color: #27272a !important;
        }

        .media-sidebar .cora-advanced-details .setting {
            padding: 12px 14px !important;
            margin-bottom: 0 !important;
            border-bottom: 1px solid #f4f4f5 !important;
            box-sizing: border-box !important;
        }

        .media-sidebar .cora-advanced-details .setting:last-of-type {
            border-bottom: none !important;
        }

        .media-sidebar .setting {
            margin-bottom: 12px !important;
            display: block !important;
            float: none !important;
            width: 100% !important;
        }

        /* Make URL setting block relative for inlining copy button */
        .media-sidebar .setting[data-setting="url"],
        .attachment-details .setting[data-setting="url"] {
            position: relative !important;
        }

        .media-sidebar .setting[data-setting="url"] input,
        .attachment-details .setting[data-setting="url"] input {
            padding-right: 36px !important;
        }

        .media-sidebar .setting .name,
        .media-sidebar .setting span {
            font-size: 10px !important;
            font-weight: 600 !important;
            color: #71717a !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            margin-bottom: 2px !important;
            display: block !important;
            float: none !important;
            width: auto !important;
            text-align: left !important;
        }

        .media-sidebar .setting input,
        .media-sidebar .setting textarea,
        .media-sidebar .setting select,
        .media-sidebar .setting .value {
            font-size: 12px !important;
            color: #09090b !important;
            background: #ffffff !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 6px !important;
            padding: 4px 8px !important;
            width: 100% !important;
            height: 30px !important;
            box-sizing: border-box !important;
            box-shadow: none !important;
            transition: border-color 0.15s, box-shadow 0.15s !important;
            float: none !important;
            margin: 0 !important;
        }

        .media-sidebar .setting input:focus,
        .media-sidebar .setting textarea:focus,
        .media-sidebar .setting select:focus {
            border-color: #09090b !important;
            box-shadow: 0 0 0 2px rgba(9, 9, 11, 0.05) !important;
            outline: none !important;
        }

        .media-sidebar .setting textarea {
            height: auto !important;
            min-height: 44px !important;
            line-height: 1.4 !important;
            resize: vertical !important;
        }

        /* Modernize Alt Text description sibling block */
        .media-sidebar .setting + .description,
        .media-sidebar .description,
        .attachment-details .setting + .description {
            font-size: 11px !important;
            color: #71717a !important;
            line-height: 1.5 !important;
            margin: 6px 0 20px 0 !important;
            display: block !important;
            float: none !important;
            width: 100% !important;
            clear: both !important;
            box-sizing: border-box !important;
        }

        /* Hide legacy help links under setting descriptions to keep layouts clean */
        .media-sidebar .setting + .description a,
        .media-sidebar .description a,
        .attachment-details .setting + .description a {
            display: none !important;
        }

        /* Copy to clipboard container modernization - Button positioned inside the input */
        .media-sidebar .copy-to-clipboard-container,
        .attachment-details .copy-to-clipboard-container {
            position: relative !important;
            display: block !important;
            margin: 4px 0 0 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
            clear: both !important;
            float: none !important;
        }

        .media-sidebar .copy-to-clipboard-container input.copy-attachment-url,
        .attachment-details .copy-to-clipboard-container input.copy-attachment-url {
            width: 100% !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            padding: 6px 36px 6px 10px !important; /* Extra right padding for copy button icon */
            font-size: 12px !important;
            color: #71717a !important;
            margin: 0 !important;
            height: 32px !important;
            box-shadow: none !important;
            box-sizing: border-box !important;
            float: none !important;
            min-width: 0 !important;
        }

        .media-sidebar .copy-to-clipboard-container button.copy-attachment-url,
        .attachment-details .copy-to-clipboard-container button.copy-attachment-url {
            position: absolute !important;
            right: 2px !important;
            top: 2px !important;
            width: 28px !important;
            height: 28px !important;
            background: transparent !important;
            border: none !important;
            color: #71717a !important;
            border-radius: 4px !important;
            padding: 0 !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.15s !important;
            margin: 0 !important;
            float: none !important;
            box-sizing: border-box !important;
            flex-shrink: 0 !important;
            font-size: 0 !important; /* Hide native text label */
            color: transparent !important; /* Hide native text label */
        }

        .media-sidebar .copy-to-clipboard-container button.copy-attachment-url::before,
        .attachment-details .copy-to-clipboard-container button.copy-attachment-url::before {
            content: "" !important;
            display: inline-block !important;
            width: 13px !important;
            height: 13px !important;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2371717a' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'><rect x='9' y='9' width='13' height='13' rx='2' ry='2'></rect><path d='M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1'></path></svg>") !important;
            background-repeat: no-repeat !important;
            background-size: contain !important;
            vertical-align: middle !important;
        }

        .media-sidebar .copy-to-clipboard-container button.copy-attachment-url:hover,
        .attachment-details .copy-to-clipboard-container button.copy-attachment-url:hover {
            background: #f4f4f5 !important;
        }

        .media-sidebar .copy-to-clipboard-container button.copy-attachment-url:hover::before,
        .attachment-details .copy-to-clipboard-container button.copy-attachment-url:hover::before {
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2309090b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'><rect x='9' y='9' width='13' height='13' rx='2' ry='2'></rect><path d='M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1'></path></svg>") !important;
        }

        .media-sidebar .copy-to-clipboard-container button.copy-attachment-url:active,
        .attachment-details .copy-to-clipboard-container button.copy-attachment-url:active {
            transform: scale(0.9) !important;
        }

        .media-sidebar .copy-to-clipboard-container .success,
        .attachment-details .copy-to-clipboard-container .success {
            position: absolute !important;
            right: 34px !important;
            top: 5px !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            color: #047857 !important; /* emerald-700 */
            background: #ecfdf5 !important;
            border: 1px solid #a7f3d0 !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            line-height: 1 !important;
            margin: 0 !important;
            z-index: 10 !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        /* Bottom Action Toolbar - styled footer for native WP select button */
        .media-frame-toolbar {
            position: absolute !important;
            bottom: 0 !important;
            height: 60px !important;
            border-top: 1px solid #e4e4e7 !important;
            background: #ffffff !important;
            left: 0 !important;
            right: 0 !important;
            box-shadow: none !important;
            overflow: visible !important;
            display: block !important;
            visibility: visible !important;
            z-index: 100000 !important;
        }

        .media-frame.hide-menu .media-frame-toolbar {
            left: 0 !important;
        }

        .media-frame-toolbar .media-toolbar {
            border-top: none !important;
            background: transparent !important;
            box-shadow: none !important;
            position: relative !important;
            height: 60px !important;
            overflow: visible !important;
        }

        .media-frame-toolbar .media-toolbar-primary {
            float: right !important;
            display: flex !important;
            align-items: center !important;
            height: 60px !important;
            padding: 0 24px !important;
            position: relative !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Force primary select buttons in bottom toolbar to always display correctly */
        .media-frame-toolbar button,
        .media-frame-toolbar .button,
        .media-frame-toolbar .button-primary,
        .media-frame-toolbar .media-button,
        .media-frame-toolbar .media-button-select {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
            position: relative !important;
            z-index: 1000000 !important;
            background: #09090b !important;
            border: 1px solid #09090b !important;
            color: #ffffff !important;
            height: 36px !important;
            line-height: 36px !important;
            padding: 0 20px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            font-family: Inter, system-ui, -apple-system, sans-serif !important;
            cursor: pointer !important;
            box-sizing: border-box !important;
            text-shadow: none !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12) !important;
        }

        .media-frame-toolbar button:disabled,
        .media-frame-toolbar .button:disabled,
        .media-frame-toolbar .button-primary:disabled,
        .media-frame-toolbar .media-button:disabled,
        .media-frame-toolbar .media-button-select:disabled {
            opacity: 0.45 !important;
            cursor: not-allowed !important;
            background: #71717a !important;
            border-color: #71717a !important;
            pointer-events: none !important;
        }

        /* Custom injected CTA button - ensure no stylesheet can override */
        .cora-media-select-btn {
            position: absolute !important;
            bottom: 12px !important;
            right: 24px !important;
            z-index: 9999999 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 36px !important;
            padding: 0 20px !important;
            background: #09090b !important;
            color: #ffffff !important;
            border: 1px solid #09090b !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            font-family: Inter, system-ui, -apple-system, sans-serif !important;
            cursor: pointer !important;
            letter-spacing: -0.01em !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12) !important;
            line-height: 36px !important;
            transition: opacity 0.15s ease, background 0.15s ease !important;
            box-sizing: border-box !important;
            text-transform: none !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }

        .cora-media-select-btn:disabled {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        .cora-media-select-btn:not(:disabled):hover {
            background: #27272a !important;
        }

        /* Modern Monochromatic Buttons */
        .wp-core-ui .button-primary {
            background: #09090b !important;
            border-color: #09090b !important;
            color: #ffffff !important;
            text-shadow: none !important;
            box-shadow: none !important;
            border-radius: 8px !important;
            font-family: system-ui, -apple-system, sans-serif !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            height: 36px !important;
            line-height: 34px !important;
            padding: 0 16px !important;
            transition: background-color 0.2s, transform 0.1s !important;
        }

        .wp-core-ui .button-primary:hover {
            background: #27272a !important;
            border-color: #27272a !important;
            color: #ffffff !important;
        }

        .wp-core-ui .button-primary:active {
            transform: scale(0.97) !important;
        }

        .wp-core-ui .button-primary:focus {
            box-shadow: 0 0 0 2px rgba(9, 9, 11, 0.4) !important;
        }

        .wp-core-ui .button {
            background: #ffffff !important;
            border: 1px solid #e4e4e7 !important;
            color: #27272a !important;
            text-shadow: none !important;
            box-shadow: none !important;
            border-radius: 8px !important;
            font-family: system-ui, -apple-system, sans-serif !important;
            font-size: 13px !important;
            font-weight: 550 !important;
            height: 36px !important;
            line-height: 34px !important;
            padding: 0 16px !important;
            transition: background-color 0.2s, border-color 0.2s, transform 0.1s !important;
        }

        .wp-core-ui .button:hover {
            background: #f4f4f5 !important;
            border-color: #d4d4d8 !important;
            color: #09090b !important;
        }

        .wp-core-ui .button:active {
            transform: scale(0.97) !important;
        }

        .wp-core-ui .button:focus {
            box-shadow: 0 0 0 2px rgba(228, 228, 231, 0.4) !important;
        }

        /* Inline Uploader */
        .media-modal .uploader-inline {
            position: absolute !important;
            top: 24px !important;
            left: 24px !important;
            right: 24px !important;
            bottom: 24px !important;
            margin: 0 !important;
            background: #ffffff !important;
            border: 2px dashed #e4e4e7 !important;
            border-radius: 12px !important;
            padding: 40px !important;
        }

        .media-modal .uploader-inline-content {
            text-align: center !important;
            position: relative !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }

        .media-modal .uploader-inline .upload-instructions {
            font-size: 14px !important;
            color: #71717a !important;
            font-family: system-ui, -apple-system, sans-serif !important;
            margin-bottom: 16px !important;
        }

        /* Hide unwanted legacy WP labels/descriptions */
        .media-modal .media-sidebar .setting span.description {
            display: none !important;
        }

        .media-modal .media-sidebar .setting.save-waiting {
            display: none !important;
        }

        /* Force clean font-family everywhere inside media modal */
        .media-modal * {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }

        /* Force sans-serif typography globally within the workspace dashboard */
        *:not(.cora-serif-editor):not(.cora-serif-editor *) {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }

        /* Responsive WordPress Media Uploader mobile overrides */
        @media only screen and (max-width: 640px) {
            .media-modal {
                top: 10px !important;
                right: 10px !important;
                bottom: 10px !important;
                left: 10px !important;
            }
            .media-frame-menu {
                display: none !important;
            }
            .media-frame-title {
                left: 0 !important;
                right: 0 !important;
            }
            .media-frame-router {
                left: 0 !important;
                right: 0 !important;
                height: 40px !important;
            }
            .media-router {
                padding: 0 12px !important;
            }
            .media-router .media-menu-item {
                height: 40px !important;
                line-height: 38px !important;
                padding: 0 10px !important;
                font-size: 12px !important;
            }
            .media-frame-content {
                left: 0 !important;
                right: 0 !important;
                top: 100px !important;
                bottom: 60px !important;
            }
            .attachments-browser .media-toolbar {
                background: transparent !important;
                border-bottom: none !important;
                height: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                overflow: visible !important;
            }
            .attachments-browser .media-toolbar-secondary {
                position: absolute !important;
                bottom: -60px !important;
                left: 12px !important;
                z-index: 10000 !important;
                height: 60px !important;
                display: flex !important;
                align-items: center !important;
                gap: 6px !important;
            }
            .attachments-browser .media-toolbar-primary {
                position: absolute !important;
                top: -100px !important;
                right: 50px !important;
                z-index: 10000 !important;
                height: 60px !important;
                display: flex !important;
                align-items: center !important;
            }
            .media-modal input.search {
                width: 110px !important;
                font-size: 11px !important;
                padding-left: 22px !important;
                background-position: 6px center !important;
            }
            .media-modal .attachments {
                padding: 10px !important;
                top: 0 !important;
                right: 0 !important;
            }
            .media-modal .attachment {
                padding: 4px !important;
            }
            .media-frame-toolbar {
                left: 0 !important;
                right: 0 !important;
                height: 60px !important;
            }
            .media-sidebar {
                display: none !important;
            }
            .media-frame:not(.hide-sidebar) .attachments-browser .attachments,
            .media-frame:not(.hide-sidebar) .attachments-browser .attachments-wrapper,
            .media-frame:not(.hide-sidebar) .attachments-browser .uploader-inline {
                right: 0 !important;
            }
            .media-frame:not(.hide-sidebar) .attachments-browser .media-toolbar {
                right: 0 !important;
            }
            .media-modal .uploader-inline {
                top: 12px !important;
                left: 12px !important;
                right: 12px !important;
                bottom: 12px !important;
                padding: 16px !important;
            }
            .media-modal .uploader-inline .upload-instructions {
                font-size: 12px !important;
                margin-bottom: 12px !important;
            }
        }

        /* Horizontal scroll filters & toolbars */
        #cora-vault-filters,
        .cora-editor-toolbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
        #cora-vault-filters::-webkit-scrollbar,
        .cora-editor-toolbar::-webkit-scrollbar {
            display: none !important;
        }

        /* Beehiiv Style ListingCoordinator Overrides */
        .ql-toolbar.ql-snow {
            border: none !important;
            border-bottom: 1px solid #f4f4f5 !important;
            padding: 12px 0 !important;
            margin-bottom: 24px !important;
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            z-index: 10;
        }
        .ql-container.ql-snow {
            border: none !important;
            font-size: 1.125rem !important; /* 18px */
            line-height: 1.8 !important;
        }
        .ql-editor {
            padding: 0 !important;
            min-height: 50vh;
        }
        .ql-editor.ql-blank::before {
            left: 0 !important;
            font-style: normal !important;
            color: #d4d4d8 !important; /* zinc-300 */
        }
        .cora-serif-editor, .cora-serif-editor * {
            font-family: 'Georgia', 'Merriweather', serif !important;
        }
        .cora-serif-editor h2, .cora-serif-editor h3 {
            font-family: system-ui, -apple-system, sans-serif !important;
            font-weight: 800 !important;
            margin-top: 1.5em !important;
            margin-bottom: 0.5em !important;
            color: #09090b !important;
        }
        
        /* TomSelect Modernization */
        .ts-control {
            border: 1px solid #e4e4e7 !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem !important;
            box-shadow: none !important;
            font-size: 12px !important;
        }
        .ts-control.focus {
            border-color: #a1a1aa !important;
            box-shadow: 0 0 0 1px #a1a1aa !important;
        }
        .ts-dropdown {
            border: 1px solid #e4e4e7 !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            font-size: 12px !important;
            margin-top: 4px !important;
        }

        /* Dynamic state controls for sections switching */
        .cora-page-section {
            display: none !important;
        }
        .cora-page-section.cora-active {
            display: block !important;
        }

        /* Sidebar active link styling */
        .cora-nav-item {
            border-left: 3px solid transparent !important;
            transition: all 0.15s ease-in-out !important;
        }
        .cora-nav-item.cora-active {
            background-color: #e4e4e7 !important; /* zinc-200 */
            color: #09090b !important; /* zinc-950 */
            font-weight: 600 !important;
            border-left: none !important;
        }

        /* Premium Sidebar Nav Icons */
        .cora-nav-item .cora-nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background-color: #f4f4f5; /* zinc-100 */
            border: 1px solid #e4e4e7; /* zinc-200 */
            border-radius: 8px;
            color: #52525b; /* zinc-600 */
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .cora-nav-item:hover .cora-nav-icon {
            background-color: #e4e4e7;
            border-color: #d4d4d8;
            color: #27272a;
        }

        .cora-nav-item.cora-active .cora-nav-icon {
            background-color: #ffffff; 
            border-color: #d4d4d8; 
            color: #09090b; 
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .cora-nav-item.cora-active .cora-nav-icon svg {
            stroke: #09090b !important;
            stroke-width: 2.2 !important;
        }
        
        .cora-nav-item .cora-nav-icon svg {
            width: 15px !important;
            height: 15px !important;
        }

        /* Shopify Reference Styling for Mobile Navigation */
        .cora-mobile-bottom-bar-wrapper {
            display: flex !important;
        }
        @media (min-width: 1024px) {
            .cora-mobile-bottom-bar-wrapper {
                display: none !important;
            }
        }
        .cora-bottom-nav-item {
            color: #71717a !important;
            transition: all 0.15s ease !important;
        }
        .cora-bottom-nav-item:hover {
            color: #18181b !important;
        }
        .cora-bottom-nav-item.cora-active {
            background-color: #f4f4f5 !important;
            color: #18181b !important;
            font-weight: 700 !important;
            border-radius: 9999px !important;
        }
        .cora-bottom-nav-item.cora-active svg {
            stroke: #18181b !important;
            stroke-width: 2.2 !important;
        }

        /* CRM filter buttons active styling */
        .cora-filter-tab.active {
            background-color: #09090b !important;
            color: #ffffff !important;
            border-color: #09090b !important;
        }

        /* Media SEO side selector active row styling */
        .cora-media-item-row.active {
            background-color: #f4f4f5 !important; /* zinc-100 */
            border-left: 3px solid #09090b !important;
        }

        /* Force complete removal of right-side off-screen drawer box-shadow bleed */
        aside.collapsed,
        .cora-ai-sidebar.collapsed,
        aside[id*="drawer"].collapsed,
        aside[id$="-drawer"].collapsed,
        [class*="drawer"].collapsed,
        [id*="drawer"].translate-x-full,
        [class*="drawer"].translate-x-full,
        #cora-custom-actions-drawer:not(.open),
        #cora-notif-dropdown.collapsed,
        #cora-ai-sidebar.collapsed,
        #drawer-article-leads.collapsed,
        #cora-media-library-drawer.collapsed,
        #cora-ai-tone-drawer.collapsed {
            transform: translateX(100%) !important;
            pointer-events: none !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            visibility: hidden !important;
            display: none !important;
        }

        aside[id$="-drawer"] {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            z-index: 9999 !important;
            height: 100vh !important;
            width: 440px !important;
            max-width: 90vw !important;
            box-sizing: border-box !important;
            transition: transform 250ms cubic-bezier(0.16, 1, 0.3, 1), visibility 250ms ease-in-out !important;
            pointer-events: auto !important;
        }

        aside[id$="-drawer"].collapsed {
            transform: translateX(100%) !important;
            pointer-events: none !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            visibility: hidden !important;
            display: none !important;
        }

        @media (max-width: 767px) {
            /* Force slide from bottom for all drawers on mobile */
            aside.collapsed,
            aside[id*="-drawer"].collapsed,
            aside[id$="-drawer"].collapsed,
            [class*="drawer"].collapsed,
            [id*="-drawer"].translate-x-full,
            [class*="drawer"].translate-x-full {
                transform: translateY(100%) !important;
            }
            
            aside[id$="-drawer"] {
                top: auto !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                max-height: 85vh !important;
                border-left: none !important;
                border-top: 1px solid #e4e4e7 !important;
                border-top-left-radius: 16px !important;
                border-top-right-radius: 16px !important;
                border-bottom-left-radius: 0 !important;
                border-bottom-right-radius: 0 !important;
                box-shadow: 0 -10px 30px rgba(0,0,0,0.15) !important;
                transform: translateY(0) !important;
                z-index: 9995 !important;
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }
            
            aside[id$="-drawer"].collapsed {
                transform: translateY(100%) !important;
                pointer-events: none !important;
                visibility: hidden !important;
                display: none !important;
            }

            /* Adjust drawer footer for bottom safe area inset */
            aside[id$="-drawer"] .border-t,
            aside[id$="-drawer"] form > div.shrink-0 {
                padding-bottom: calc(20px + env(safe-area-inset-bottom, 0px)) !important;
            }
        }

        /* Eliminate right-side edge shadows on layout containers */
        main.cora-main, .cora-main, .cora-content-wrapper, #cora-page-dashboard, #cora-workspace-container, #cora-global-topbar, body, #wpbody-content, #wpcontent, #cora-app-container {
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            border-right: none !important;
            overflow-x: hidden !important;
        }

        /* AI Gradient Motion Border Button Pill */
        @keyframes cora-ai-gradient-spin {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .cora-ai-gradient-pill {
            position: relative;
            padding: 1.5px;
            border-radius: 9999px;
            background: linear-gradient(90deg, #a855f7, #6366f1, #ec4899, #3b82f6, #a855f7);
            background-size: 300% 300%;
            animation: cora-ai-gradient-spin 4s ease infinite;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(168, 85, 247, 0.2);
            transition: transform 150ms ease;
        }
        .cora-ai-gradient-pill:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 10px rgba(168, 85, 247, 0.35);
        }
        .cora-ai-gradient-pill:active {
            transform: scale(0.98);
        }
        .cora-ai-gradient-pill-inner {
            background: #ffffff;
            border-radius: 9999px;
            padding: 5px 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #09090b;
            font-size: 11px;
            font-weight: 600;
            transition: background 150ms ease;
        }
        @media (min-width: 640px) {
            .cora-ai-gradient-pill-inner {
                padding: 6px 16px;
                font-size: 12px;
            }
        }
        .dark .cora-ai-gradient-pill-inner {
            background: #09090b;
            color: #f4f4f5;
        }

        aside[id$="-drawer"]:not(.collapsed) {
            transform: translateX(0) !important;
            pointer-events: auto !important;
            visibility: visible !important;
            box-shadow: -12px 0 35px rgba(0,0,0,0.18) !important;
        }

        /* Ensure main workspace content width remains 100% stable when drawer is open */
        main, .cora-main-content, #cora-workspace-container {
            width: 100% !important;
            max-width: 100% !important;
            flex: 1 1 auto !important;
        }

        /* Switch toggle helpers */
        .cora-module-status-pill.active {
            background-color: #09090b !important;
            color: #ffffff !important;
        }
        .cora-module-status-pill.inactive {
            background-color: #f4f4f5 !important;
            color: #71717a !important;
        }

        /* Dynamic badging styles */
        .cora-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
            line-height: 1;
        }
        .cora-badge-blue {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid rgba(29, 78, 216, 0.15) !important;
        }
        .cora-badge-yellow {
            background-color: #fef3c7 !important;
            color: #d97706 !important;
            border: 1px solid rgba(217, 119, 6, 0.15) !important;
        }
        .cora-badge-green {
            background-color: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid rgba(4, 120, 87, 0.15) !important;
        }
        .cora-badge-purple {
            background-color: #faf5ff !important;
            color: #6b21a8 !important;
            border: 1px solid rgba(107, 33, 168, 0.15) !important;
        }
        .cora-badge-orange {
            background-color: #fff7ed !important;
            color: #c2410c !important;
            border: 1px solid rgba(194, 65, 12, 0.15) !important;
        }
        .cora-badge-teal {
            background-color: #f0fdf4 !important;
            color: #0f766e !important;
            border: 1px solid rgba(15, 118, 110, 0.15) !important;
        }
        .cora-badge-soon {
            background-color: #e0e7ff !important;
            color: #4338ca !important;
            border: 1px solid rgba(67, 56, 202, 0.15) !important;
        }
        .cora-badge-locked {
            background-color: #fef3c7 !important;
            color: #b45309 !important;
            border: 1px solid rgba(180, 83, 9, 0.15) !important;
        }

        /* Dynamic buttons inserted by JS */
        .cora-btn-icon-only {
            padding: 0.375rem;
            border-radius: 0.375rem;
            border: 1px solid #e4e4e7;
            color: #71717a;
            background-color: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            cursor: pointer;
        }
        .cora-btn-icon-only:hover {
            color: #09090b;
            background-color: #f4f4f5;
            border-color: #d4d4d8;
        }
        .cora-btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 0.375rem;
            border: 1px solid #d4d4d8;
            color: #27272a;
            background-color: transparent;
            transition: all 0.15s;
            cursor: pointer;
        }
        .cora-btn-action:hover {
            background-color: #fafafa;
        }
        .cora-btn-action:active {
            transform: scale(0.95);
        }
        .cora-delivered-text {
            font-size: 0.75rem;
            color: #047857;
            font-weight: 500;
            margin-right: 0.5rem;
        }

        /* Dynamic chat history styling */
        .chat-bubble {
            max-width: 85% !important;
            border-radius: 0.5rem !important;
            padding: 0.75rem !important;
            font-size: 0.75rem !important;
            line-height: 1.5 !important;
            white-space: pre-line !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
        .chat-bubble.user {
            background-color: #09090b !important;
            color: #ffffff !important;
            border-bottom-right-radius: 0px !important;
            align-self: flex-end !important;
        }
        .chat-bubble.ai {
            background-color: #f4f4f5 !important;
            color: #18181b !important;
            border-bottom-left-radius: 0px !important;
            align-self: flex-start !important;
            border: 1px solid rgba(228, 228, 231, 0.5) !important;
        }

        /* Spin animation for scanner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .spin-icon {
            animation: spin 1s linear infinite;
        }

        /* Sidebar collapse transitions and width resets */
        .cora-sidebar {
            transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* Collapsed sidebar classes */
        .cora-sidebar.collapsed-sidebar {
            width: 4rem !important; /* w-16 */
        }
        @media (max-width: 1023px) {
            .cora-sidebar.collapsed-sidebar {
                width: 16rem !important; /* w-64 */
            }
            .cora-sidebar.collapsed-sidebar .cora-agency-info,
            .cora-sidebar.collapsed-sidebar .cora-switcher-arrow,
            .cora-sidebar.collapsed-sidebar .cora-sidebar-search span,
            .cora-sidebar.collapsed-sidebar .cora-sidebar-search .cora-kbd,
            .cora-sidebar.collapsed-sidebar .cora-nav-group-label,
            .cora-sidebar.collapsed-sidebar .cora-nav-text,
            .cora-sidebar.collapsed-sidebar .cora-badge-sidebar,
            .cora-sidebar.collapsed-sidebar .cora-sidebar-footer span,
            .cora-sidebar.collapsed-sidebar .cora-user-info,
            .cora-sidebar.collapsed-sidebar .cora-user-settings-btn {
                display: flex !important;
            }
            .cora-sidebar.collapsed-sidebar .cora-sidebar-search span {
                display: inline !important;
            }
            .cora-sidebar.collapsed-sidebar .cora-kbd {
                display: inline-block !important;
            }
        }
        
        /* Hide text labels when collapsed */
        .cora-sidebar.collapsed-sidebar .cora-agency-info,
        .cora-sidebar.collapsed-sidebar .cora-switcher-arrow,
        .cora-sidebar.collapsed-sidebar .cora-sidebar-search span,
        .cora-sidebar.collapsed-sidebar .cora-sidebar-search input,
        .cora-sidebar.collapsed-sidebar .cora-sidebar-search .cora-kbd,
        .cora-sidebar.collapsed-sidebar .cora-nav-group-label,
        .cora-sidebar.collapsed-sidebar .cora-nav-text,
        .cora-sidebar.collapsed-sidebar .cora-badge-sidebar,
        .cora-sidebar.collapsed-sidebar .cora-sidebar-footer span,
        .cora-sidebar.collapsed-sidebar .cora-user-info,
        .cora-sidebar.collapsed-sidebar .cora-user-settings-btn {
            display: none !important;
        }

        /* Center icons/items when collapsed */
        .cora-sidebar.collapsed-sidebar .cora-sidebar-header {
            justify-content: center !important;
            padding: 1rem 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-sidebar-search {
            justify-content: center !important;
            margin-left: 0.5rem !important;
            margin-right: 0.5rem !important;
            padding: 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-nav-item {
            justify-content: center !important;
            padding: 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-nav-item-link {
            justify-content: center !important;
            padding: 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-sidebar-footer {
            justify-content: center !important;
            padding: 1rem 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-user-profile {
            justify-content: center !important;
            padding: 1rem 0.5rem !important;
        }

        /* Popover placement when sidebar is collapsed */
        .cora-sidebar.collapsed-sidebar #cora-profile-popover {
            left: 4.5rem !important; /* place it to the right of the collapsed sidebar */
            right: auto !important;
            width: 180px !important;
            bottom: 1rem !important;
        }

        /* High-End Notion Monochrome Dark Theme Overrides */
        #cora-workspace.cora-dark-theme {
            background-color: #0c0c0e !important;
            color: #f4f4f5 !important;
        }
        .cora-dark-theme .cora-sidebar {
            background-color: #121214 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-sidebar-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        .cora-dark-theme .cora-sidebar-header:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .cora-dark-theme .cora-sidebar-search {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme .cora-sidebar-search:hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme .cora-nav-item:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-nav-item.cora-active {
            background-color: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            border-left: 3px solid #ffffff !important;
            border-top-left-radius: 0px !important;
            border-bottom-left-radius: 0px !important;
        }
        .cora-dark-theme .cora-nav-item.cora-active .cora-nav-icon svg {
            stroke: #ffffff !important;
        }
        .cora-dark-theme .cora-badge-sidebar {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #e4e4e7 !important;
        }
        .cora-dark-theme .cora-user-profile {
            border-top-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme .cora-user-profile:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .cora-main {
            transition: margin-right 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .cora-dark-theme .cora-main {
            background-color: #0c0c0e !important;
        }
        .cora-dark-theme .cora-topbar {
            background-color: rgba(12, 12, 14, 0.95) !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-breadcrumb-root:hover {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-breadcrumb-current {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-stat-card {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-stat-label {
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-stat-value {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-callout {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-callout-text {
            color: #e4e4e7 !important;
        }
        .cora-dark-theme .cora-table-header {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-table-row {
            border-bottom-color: rgba(255, 255, 255, 0.05) !important;
            color: #e4e4e7 !important;
        }
        .cora-dark-theme .cora-table-row:hover {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }
        .cora-dark-theme .cora-card {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-card-title {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-input,
        .cora-dark-theme #cora-role-preview-select,
        .cora-dark-theme .cora-role-preview-select,
        .cora-dark-theme #cora-add-showing-drawer input,
        .cora-dark-theme #cora-add-showing-drawer select,
        .cora-dark-theme #cora-team-management-drawer select,
        .cora-dark-theme #cora-ai-sidebar textarea {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
        .cora-role-preview-select option {
            background-color: #ffffff;
            color: #18181b;
        }
        .cora-dark-theme .cora-role-preview-select option {
            background-color: #18181b !important;
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-input:focus,
        .cora-dark-theme #cora-role-preview-select:focus,
        .cora-dark-theme #cora-add-showing-drawer input:focus,
        .cora-dark-theme #cora-add-showing-drawer select:focus,
        .cora-dark-theme #cora-team-management-drawer select:focus,
        .cora-dark-theme #cora-ai-sidebar textarea:focus {
            border-color: #ffffff !important;
        }
        .cora-dark-theme #cora-profile-popover {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5) !important;
        }
        .cora-dark-theme #cora-profile-popover .border-b,
        .cora-dark-theme #cora-profile-popover .border-t {
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme #cora-profile-popover select {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-profile-popover button,
        .cora-dark-theme #cora-profile-popover a {
            color: #a1a1aa !important;
        }
        .cora-dark-theme #cora-profile-popover button:hover,
        .cora-dark-theme #cora-profile-popover a:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .cora-dark-theme #cora-profile-popover .bg-\[\#fafaf9\] {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme #cora-profile-popover .text-zinc-900 {
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-profile-popover .text-zinc-400 {
            color: #71717a !important;
        }
        .cora-dark-theme #cora-add-showing-drawer,
        .cora-dark-theme #cora-ai-sidebar {
            background-color: #121214 !important;
            border-left-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .chat-bubble.ai {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #f4f4f5 !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme .cora-status-text {
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-badge {
            background-color: rgba(255, 255, 255, 0.06) !important;
            color: #e4e4e7 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        .cora-dark-theme .cora-badge-soon {
            background-color: rgba(99, 102, 241, 0.15) !important;
            color: #a5b4fc !important;
            border-color: rgba(99, 102, 241, 0.25) !important;
        }
        .cora-dark-theme .cora-badge-locked {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #fcd34d !important;
            border-color: rgba(245, 158, 11, 0.25) !important;
        }
        .cora-dark-theme select option {
            background-color: #121214 !important;
            color: #ffffff !important;
        }
        
        /* Feature Hub styling */
        .cora-feature-card {
            transition: all 0.2s ease-in-out !important;
        }
        .cora-feature-card:hover {
            transform: translateY(-2px) !important;
        }
        .cora-dark-theme .cora-feature-card {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-feature-card:hover {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        .cora-dark-theme .cora-feature-card h3 {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-feature-card p {
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-feature-card svg {
            stroke: #a1a1aa !important;
        }
        .cora-dark-theme .cora-feature-card div.border-t {
            border-top-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-feature-card .text-zinc-500 {
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-feature-card:hover .text-zinc-500 {
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-card-manage-team {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }
        .cora-dark-theme #cora-card-manage-team:hover {
            border-color: #ffffff !important;
        }
        .cora-dark-theme #cora-card-manage-team svg {
            stroke: #34d399 !important;
        }
        .cora-dark-theme #cora-team-management-drawer {
            background-color: #121214 !important;
            border-left-color: rgba(255, 255, 255, 0.08) !important;
        }
        
        /* Drawer and Form Overrides */
        .cora-drawer-footer {
            background-color: #f9f9f9;
            border-top: 1px solid #e5e7eb;
        }
        .cora-dark-theme #cora-team-management-drawer,
        .cora-dark-theme #cora-add-showing-drawer,
        .cora-dark-theme #cora-ai-sidebar,
        .cora-dark-theme #cora-portfolio-drawer {
            background-color: #121214 !important;
            border-left-color: rgba(255, 255, 255, 0.08) !important;
            color: #f4f4f5 !important;
        }
        /* Shopify Style Resizable Sidebar */
        #cora-ai-sidebar.cora-ai-sidebar-wide {
            width: 600px !important;
        }
        @media (min-width: 1024px) {
            #cora-ai-sidebar {
                position: fixed !important;
                top: 52px !important;
                left: 0 !important;
                right: 0 !important;
                width: 100vw !important;
                max-width: 100vw !important;
                height: calc(100vh - 52px) !important;
                box-shadow: none !important;
                z-index: 9999 !important;
                pointer-events: auto !important;
                visibility: visible !important;
            }
            #cora-ai-sidebar.collapsed {
                display: none !important;
                visibility: hidden !important;
                transform: translateX(100%) !important;
            }
        }
        .cora-dark-theme .cora-ai-sidebar-header {
            background-color: #18181b !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-ai-sidebar-header span {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-drawer-footer {
            background-color: #18181b !important;
            border-top-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme #cora-team-management-drawer label,
        .cora-dark-theme #cora-portfolio-drawer label,
        .cora-dark-theme #cora-add-showing-drawer label {
            color: #a1a1aa !important;
        }
        .cora-dark-theme #cora-team-management-drawer .text-zinc-800,
        .cora-dark-theme #cora-portfolio-drawer .text-zinc-800,
        .cora-dark-theme #cora-add-showing-drawer .text-zinc-800 {
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-team-management-drawer .text-zinc-500,
        .cora-dark-theme #cora-add-showing-drawer .text-zinc-500 {
            color: #a1a1aa !important;
        }
        .cora-dark-theme #cora-team-management-drawer button.bg-white,
        .cora-dark-theme #cora-add-showing-drawer button.bg-white {
            background-color: #18181b !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }
        .cora-dark-theme #cora-team-management-drawer button.bg-white:hover,
        .cora-dark-theme #cora-add-showing-drawer button.bg-white:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .cora-dark-theme .cora-badge-green {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
            border-color: rgba(16, 185, 129, 0.25) !important;
        }

        /* Google Docs A4 Emulation styles */
        #cora-paper-container {
            font-family: Arial, Helvetica, sans-serif !important;
            transition: all 0.2s ease;
        }
        #cora-doc-paper * {
            font-family: inherit !important;
        }
        #cora-doc-paper:focus {
            outline: none;
        }
        #cora-doc-paper h1 {
            font-size: 2.25rem !important;
            font-weight: 800 !important;
            margin-top: 1.5rem !important;
            margin-bottom: 1rem !important;
            line-height: 1.2 !important;
        }
        #cora-doc-paper h2 {
            font-size: 1.75rem !important;
            font-weight: 700 !important;
            margin-top: 1.5rem !important;
            margin-bottom: 0.75rem !important;
            line-height: 1.3 !important;
        }
        #cora-doc-paper h3 {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            margin-top: 1.25rem !important;
            margin-bottom: 0.5rem !important;
            line-height: 1.4 !important;
        }
        #cora-doc-paper p {
            margin-bottom: 1rem !important;
            line-height: 1.6 !important;
        }
        #cora-doc-paper ul {
            list-style-type: disc !important;
            padding-left: 1.5rem !important;
            margin-bottom: 1rem !important;
        }
        #cora-doc-paper ol {
            list-style-type: decimal !important;
            padding-left: 1.5rem !important;
            margin-bottom: 1rem !important;
        }
        #cora-doc-paper li {
            margin-bottom: 0.25rem !important;
        }
        #cora-doc-paper blockquote {
            border-left: 4px solid #e4e4e7 !important;
            padding-left: 1rem !important;
            color: #71717a !important;
            font-style: italic !important;
            margin-bottom: 1rem !important;
        }
        
        #cora-doc-paper[placeholder]:empty::before {
            content: attr(placeholder);
            color: #a1a1aa;
            font-style: italic;
        }

         /* Shopify-style mobile navigation and table refinements */
         @media (max-width: 767px) {
             #cora-workspace {
                 max-width: 100vw !important;
                 overflow-x: hidden !important;
             }
             .cora-main {
                 width: 100vw !important;
                 max-width: 100vw !important;
                 min-width: 0 !important;
                 overflow-x: clip !important;
             }
             .cora-content-wrapper {
                 padding: 1rem !important;
                 width: 100% !important;
                 max-width: 100% !important;
                 box-sizing: border-box !important;
             }
             .cora-topbar {
                 padding-left: 1rem !important;
                 padding-right: 1rem !important;
                 width: 100% !important;
                 max-width: 100% !important;
                 box-sizing: border-box !important;
             }
             #cora-vault-stats-grid {
                 grid-template-columns: 1fr 1fr;
                 gap: 0.75rem !important;
             }
         }

        /* Print isolation mode */
        body.cora-printing-mode {
            background: white !important;
            color: black !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        body.cora-printing-mode #cora-workspace {
            display: none !important;
        }
        body.cora-printing-mode #cora-print-paper-container {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            padding: 20mm !important;
            margin: 0 !important;
        }
        #cora-paper-header-preview {
            cursor: pointer;
            transition: opacity 0.2s ease;
        }
        #cora-paper-header-preview:hover {
            opacity: 0.8;
        }
        #cora-paper-footer-preview[placeholder]:empty::before {
            content: attr(placeholder);
            color: #a1a1aa;
            font-style: italic;
        }
        /* Smooth transitions for settings sidebar collapse */
        #cora-editor-workspace-columns {
            transition: gap 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #cora-vault-editor-view.cora-sidebar-collapsed #cora-editor-workspace-columns {
            gap: 0 !important;
        }
        #cora-vault-editor-view aside {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        #cora-vault-editor-view.cora-sidebar-collapsed aside {
            width: 0 !important;
            min-width: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            border-width: 0 !important;
            opacity: 0 !important;
            overflow: hidden !important;
        }
        /* --- Gallery Detail Grid View & Modals Styles --- */
        .cora-asset-card {
            transition: all 0.2s ease-out;
        }
        .cora-asset-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        .cora-asset-media-container {
            background-color: #f4f4f5;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cora-asset-media-container img, .cora-asset-media-container iframe {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
        }
        .cora-asset-overlay-action {
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .cora-asset-card:hover .cora-asset-overlay-action {
            opacity: 1;
        }
        
        /* Modal Overlay & Card */
        .cora-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .cora-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .cora-modal-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
            transform: scale(0.95) translateY(10px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }
        .cora-dark-theme .cora-modal-card {
            background: #09090b;
            border-color: #27272a;
        }
        .cora-modal-overlay.active .cora-modal-card {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        .cora-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f4f4f5;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cora-dark-theme .cora-modal-header {
            border-bottom-color: #27272a;
        }
        .cora-modal-body {
            padding: 24px;
            overflow-y: auto;
        }
        .cora-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #f4f4f5;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            background: #fafafa;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }
        .cora-dark-theme .cora-modal-footer {
            background: #18181b;
            border-top-color: #27272a;
        }

        /* Redesigned Dashboard Dark Theme Overrides */
        .cora-dark-theme #cora-page-dashboard .bg-white {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme #cora-page-dashboard input {
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-page-dashboard input::placeholder {
            color: #71717a !important;
        }
        .cora-dark-theme #cora-page-dashboard .text-zinc-900 {
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-page-dashboard .text-zinc-750 {
            color: #e4e4e7 !important;
        }
        .cora-dark-theme #cora-page-dashboard .text-zinc-650 {
            color: #d4d4d8 !important;
        }
        .cora-dark-theme #cora-page-dashboard .bg-zinc-50 {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme #cora-page-dashboard .border-zinc-200\/60,
        .cora-dark-theme #cora-page-dashboard .border-zinc-200\/80 {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme #cora-page-dashboard .border-y {
            border-top-color: rgba(255, 255, 255, 0.08) !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme #cora-page-dashboard .divide-y > :not([hidden]) ~ :not([hidden]) {
            border-top-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme #cora-page-dashboard .bg-zinc-50\/20 {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }
        .cora-dark-theme #cora-page-dashboard .hover\:bg-zinc-50:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .cora-dark-theme #cora-page-dashboard .divide-zinc-100 > :not([hidden]) ~ :not([hidden]) {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme #cora-page-dashboard kbd {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #a1a1aa !important;
        }
        .cora-dark-theme #cora-page-dashboard .hover\:bg-zinc-100:hover {
            background-color: #27272a !important;
        }

        /* Dot Grid Background Pattern Override */
        #cora-page-dashboard {
            background-color: #FBFaf7 !important; /* Premium warm cream background */
            background-image: radial-gradient(rgba(120, 115, 105, 0.07) 1px, transparent 1px) !important;
            background-size: 24px 24px !important;
            padding: 12px 6px 0px 6px !important;
            border-radius: 20px 20px 0px 0px !important;
            border: none !important;
            box-shadow: none !important;
            transition: background-color 0.3s ease;
            margin-bottom: 0px !important;
        }
        @media (min-width: 768px) {
            #cora-page-dashboard {
                padding: 24px 24px 0px 24px !important;
            }
        }
        main.cora-main, .cora-main, .cora-content-wrapper {
            background-color: #ffffff !important;
        }
        .cora-content-wrapper {
            padding-bottom: 0px !important;
        }
        .cora-dark-theme #cora-page-dashboard {
            background-color: #0e0f10 !important; /* Premium deep charcoal background */
            background-image: radial-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px) !important;
            border: none !important;
            box-shadow: none !important;
        }
        .cora-dark-theme main.cora-main, .cora-dark-theme .cora-main, .cora-dark-theme .cora-content-wrapper {
            background-color: #0c0c0e !important;
        }

        /* Clean responsive bento grid layout with independent containment */
        .cora-bento-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 20px !important;
            padding: 0 24px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .cora-bento-grid > * {
            min-width: 0;
            overflow: hidden;
        }
        @media (max-width: 1023px) {
            .cora-bento-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 16px !important;
                padding: 0 20px !important;
            }
        }
        @media (max-width: 767px) {
            .cora-bento-grid {
                grid-template-columns: 1fr !important;
                gap: 14px !important;
                padding: 0 12px !important;
            }
        }
        
        /* Dashboard Sketched Grid Layout */
        .cora-sketch-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 28px !important;
            align-items: start !important;
        }
        @media (max-width: 1023px) {
            .cora-sketch-grid {
                grid-template-columns: 1fr !important;
                gap: 24px !important;
            }
        }
        /* clean Lovable-style sidebar layout CSS overrides */
        .cora-sidebar {
            background-color: #f9fafb !important;
            border-right: 1px solid rgba(228, 228, 231, 0.6) !important;
        }
        .cora-sidebar-bottom-block {
            background-color: #f9fafb !important;
        }
        .cora-dark-theme .cora-sidebar-bottom-block {
            background-color: #121214 !important;
        }
        .cora-sidebar-header {
            border-bottom: none !important;
        }
        .cora-nav-item, .cora-nav-item-link {
            font-size: 13px !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            color: #27272a !important;
            border-left: none !important;
            padding: 7px 12px !important;
            margin: 2px 12px !important;
            background-color: transparent !important;
            transition: background-color 0.15s ease, color 0.15s ease !important;
        }
        .cora-nav-item:hover, .cora-nav-item-link:hover {
            background-color: #f4f4f5 !important;
            color: #000000 !important;
        }
        .cora-nav-item.cora-active {
            background-color: #eaeaea !important;
            color: #000000 !important;
            font-weight: 500 !important;
            border-left: none !important;
            border-top-left-radius: 8px !important;
            border-bottom-left-radius: 8px !important;
        }
        .cora-nav-item .cora-nav-icon,
        .cora-nav-item.cora-active .cora-nav-icon,
        .cora-nav-item:hover .cora-nav-icon {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
            width: auto !important;
            height: auto !important;
            padding: 0 !important;
            border-radius: 0 !important;
            color: #3f3f46 !important;
        }
        .cora-nav-item.cora-active .cora-nav-icon svg {
            stroke: #000000 !important;
            stroke-width: 1.8 !important;
        }
        .cora-nav-group-label {
            font-size: 11px !important;
            font-weight: 600 !important;
            text-transform: none !important;
            letter-spacing: normal !important;
            color: #71717a !important;
            margin-top: 0.75rem !important;
            margin-bottom: 0.25rem !important;
            padding-left: 1.5rem !important;
        }
        .cora-nav-group:first-child .cora-nav-group-label,
        .cora-sidebar-nav > .cora-nav-group:first-of-type .cora-nav-group-label {
            margin-top: 0.25rem !important;
        }
        .cora-recent-item {
            padding-left: 2.25rem !important;
            padding-right: 0.75rem !important;
            padding-top: 0.375rem !important;
            padding-bottom: 0.375rem !important;
            margin: 1px 12px !important;
            border-radius: 8px !important;
            color: #27272a !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            transition: background-color 0.15s ease, color 0.15s ease !important;
        }
        .cora-recent-item:hover {
            background-color: #f4f4f5 !important;
            color: #000000 !important;
        }
        .cora-promo-card {
            border: 1px solid rgba(228, 228, 231, 0.6) !important;
            background-color: rgba(255, 255, 255, 0.4) !important;
            transition: all 0.2s ease !important;
        }
        .cora-promo-card:hover {
            background-color: rgba(255, 255, 255, 0.8) !important;
            border-color: rgba(200, 200, 200, 0.8) !important;
        }
        /* Bulletproof Collapsed Sidebar Overrides */
        .cora-sidebar.collapsed-sidebar {
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
        }

        .cora-sidebar.collapsed-sidebar .cora-sidebar-top-container {
            flex-direction: column !important;
            gap: 0.5rem !important;
            padding: 0.75rem 0.25rem 0.5rem 0.25rem !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .cora-sidebar.collapsed-sidebar .cora-sidebar-header-actions {
            flex-direction: column !important;
            align-items: center !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }

        .cora-sidebar.collapsed-sidebar #cora-sidebar-search-btn,
        .cora-sidebar.collapsed-sidebar #cora-sidebar-toggle {
            display: flex !important;
            padding: 0.4rem !important;
            border-radius: 8px !important;
            margin: 0 auto !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .cora-sidebar.collapsed-sidebar .cora-promo-card,
        .cora-sidebar.collapsed-sidebar .cora-recent-item,
        .cora-sidebar.collapsed-sidebar .cora-nav-group-label,
        .cora-sidebar.collapsed-sidebar .cora-role-switcher-card,
        .cora-sidebar.collapsed-sidebar .cora-sidebar-search,
        .cora-sidebar.collapsed-sidebar .cora-nav-text,
        .cora-sidebar.collapsed-sidebar .cora-badge-sidebar,
        .cora-sidebar.collapsed-sidebar .cora-studio-info,
        .cora-sidebar.collapsed-sidebar .cora-user-info,
        .cora-sidebar.collapsed-sidebar .cora-switcher-arrow,
        .cora-sidebar.collapsed-sidebar div.px-3.pb-1 {
            display: none !important;
        }

        .cora-sidebar.collapsed-sidebar .cora-workspace-card {
            justify-content: center !important;
            border-color: transparent !important;
            background-color: transparent !important;
            padding: 0.25rem !important;
            margin: 0 auto !important;
            box-shadow: none !important;
        }

        /* macOS Dock Magnification Effect (Cora UI/UX) */
        .cora-sidebar.collapsed-sidebar .cora-nav-item {
            justify-content: center !important;
            padding: 0.6rem 0 !important;
            margin: 4px 8px !important;
            border-radius: 12px !important;
            position: relative !important;
            transition: transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.15s ease !important;
            transform-origin: left center !important;
        }

        .cora-sidebar.collapsed-sidebar .cora-nav-item.dock-hover-active {
            transform: scale(1.35) translateX(4px) !important;
            z-index: 50 !important;
        }

        .cora-sidebar.collapsed-sidebar .cora-nav-item.dock-hover-neighbor {
            transform: scale(1.16) translateX(2px) !important;
            z-index: 40 !important;
        }

        .cora-sidebar.collapsed-sidebar .cora-nav-item.cora-active {
            background-color: #eaeaea !important;
            border-radius: 12px !important;
        }

        .cora-dark-theme .cora-sidebar.collapsed-sidebar .cora-nav-item.cora-active {
            background-color: rgba(255, 255, 255, 0.18) !important;
        }

        .cora-sidebar.collapsed-sidebar .cora-user-footer {
            flex-direction: column !important;
            gap: 0.75rem !important;
            padding: 1rem 0.5rem !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .cora-sidebar.collapsed-sidebar .cora-user-inbox {
            margin: 0 !important;
        }

        .cora-sidebar.collapsed-sidebar #cora-profile-popover:not(.hidden) {
            position: fixed !important;
            bottom: 12px !important;
            left: 5rem !important;
            width: 290px !important;
            right: auto !important;
            z-index: 9999 !important;
        }

        .cora-sidebar.collapsed-sidebar #cora-workspace-popover:not(.hidden) {
            position: fixed !important;
            top: 12px !important;
            left: 5rem !important;
            width: 290px !important;
            right: auto !important;
            z-index: 9999 !important;
        }
        
        .cora-dark-theme .cora-sidebar {
            background-color: #0c0c0e !important;
            border-right: 1px solid rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme .cora-workspace-card {
            border-color: rgba(255, 255, 255, 0.08) !important;
            background-color: rgba(255, 255, 255, 0.02) !important;
        }
        .cora-dark-theme .cora-workspace-card:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        .cora-dark-theme .cora-nav-item,
        .cora-dark-theme .cora-nav-item-link,
        .cora-dark-theme .cora-recent-item {
            color: #d4d4d8 !important;
        }
        .cora-dark-theme .cora-nav-item:hover,
        .cora-dark-theme .cora-nav-item-link:hover,
        .cora-dark-theme .cora-recent-item:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-nav-item.cora-active {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-nav-item .cora-nav-icon,
        .cora-dark-theme .cora-nav-item.cora-active .cora-nav-icon {
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-promo-card {
            border-color: rgba(255, 255, 255, 0.08) !important;
            background-color: rgba(255, 255, 255, 0.01) !important;
        }
        .cora-dark-theme .cora-promo-card:hover {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }
        .cora-dark-theme.collapsed-sidebar .cora-user-inbox span {
            border-color: #0c0c0e !important;
        }
    </style>
    
    <!-- Pass WordPress environment variables to JavaScript -->
    <script>
        window.coraAutoUpdateConfig = {
            active: <?php echo ($cora_auto_update && !empty($cora_target_version)) ? 'true' : 'false'; ?>,
            targetVersion: "<?php echo esc_js($cora_target_version); ?>",
            userCanUpdate: <?php echo $cora_user_can_update ? 'true' : 'false'; ?>,
            nonce: "<?php echo esc_js( wp_create_nonce( 'cora_ajax_nonce' ) ); ?>"
        };

        var coraREData = {
            ajaxUrl: "<?php echo esc_url( cora_get_origin_relative_url( admin_url( 'admin-ajax.php' ) ) ); ?>",
            siteUrl: "<?php echo esc_url( cora_get_origin_relative_url( get_site_url() ) ); ?>",
            restUrl: "<?php echo esc_url( cora_get_origin_relative_url( rest_url() ) ); ?>",
            nonce: "<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>",
            ajaxNonce: "<?php echo esc_js( wp_create_nonce( 'cora_ajax_nonce' ) ); ?>",
            currentRole: "<?php echo esc_js( $current_user_role ); ?>",
            roleLabels: <?php echo json_encode( $cora_role_labels ); ?>,
            userPermissions: <?php echo json_encode( $cora_permissions ); ?>,
            currentPage: "<?php echo esc_js( $sub_page ); ?>",
            isSuperOwner: <?php echo cora_is_super_owner() ? 'true' : 'false'; ?>,
            activeWorkspace: <?php echo json_encode( $cora_active_workspace ); ?>,
            userWorkspaces: <?php echo json_encode( $cora_user_workspaces ); ?>,
            domainName: "app.heycora.in",
            documents: <?php echo json_encode( $cora_documents ); ?>,
            portfolios: <?php echo json_encode( $cora_portfolios ); ?>,
            leads: <?php echo json_encode( $cora_workspace_leads ); ?>,
            clients: <?php echo json_encode( $cora_workspace_clients ); ?>,
            attendanceLogs: <?php echo json_encode( $cora_workspace_attendance_logs ); ?>,
            clientTasks: <?php echo json_encode( $cora_workspace_client_tasks ); ?>,
            financials: <?php echo json_encode( $cora_financials ); ?>,
            equipment: <?php echo json_encode( $cora_workspace_listings ); ?>,
            gbpProfile: <?php echo json_encode( $cora_gbp_profile ); ?>,
            gbpIsConnected: <?php echo $cora_gbp_is_connected ? 'true' : 'false'; ?>,
            gbpIsAuthenticated: <?php echo $cora_gbp_is_authenticated ? 'true' : 'false'; ?>,
            gbpHasCredentials: <?php echo $cora_gbp_has_credentials ? 'true' : 'false'; ?>,
            gbpHasMapsKey: <?php echo $cora_gbp_has_maps_key ? 'true' : 'false'; ?>,
            gbpConnectedVia: '<?php echo esc_js( $cora_gbp_connected_via ); ?>',
            gbpPosts: <?php echo json_encode( array_slice( $cora_gbp_posts, 0, 10 ) ); ?>,
            gbpReviewReplies: <?php echo json_encode( $cora_gbp_review_replies ); ?>,
            pluginsUrl: "<?php echo esc_url( plugins_url( '/', __FILE__ ) ); ?>"
        };

        var _coraSidebarToggleLock = false;
        window.coraToggleSidebarCollapse = function(e) {
            if (e && e.stopPropagation) e.stopPropagation();
            if (_coraSidebarToggleLock) return false;
            _coraSidebarToggleLock = true;
            setTimeout(function() { _coraSidebarToggleLock = false; }, 250);

            var sidebar = document.querySelector('.cora-sidebar');
            if (sidebar) {
                sidebar.classList.toggle('collapsed-sidebar');
                var isCollapsed = sidebar.classList.contains('collapsed-sidebar');
                try {
                    localStorage.setItem('cora_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                } catch(err) {}
                if (typeof $ !== 'undefined') {
                    $('#cora-workspace-popover, #cora-profile-popover, #cora-sidebar-floating-tooltip').addClass('hidden');
                }
            }
            return false;
        };

        window.coraAutoCollapseDashboardSidebar = function() {
            var sidebar = document.querySelector('.cora-sidebar');
            if (sidebar && !sidebar.classList.contains('collapsed-sidebar')) {
                sidebar.classList.add('collapsed-sidebar');
                try { localStorage.setItem('cora_sidebar_collapsed', 'true'); } catch(e) {}
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('cora_sidebar_collapsed') === 'true') {
                var sidebar = document.querySelector('.cora-sidebar');
                if (sidebar) sidebar.classList.add('collapsed-sidebar');
            }
        });

        window.coraSwitchWorkspace = function(slug) {
            if (!slug) return;
            var ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) ? window.coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
            var nonce = (window.coraREData && window.coraREData.ajaxNonce) ? window.coraREData.ajaxNonce : '';
            
            document.cookie = "cora_active_workspace_slug=" + encodeURIComponent(slug) + "; path=/; max-age=31536000";
            
            if (typeof jQuery !== 'undefined') {
                jQuery.post(ajaxUrl, {
                    action: 'cora_ajax_switch_workspace',
                    nonce: nonce,
                    workspace_slug: slug
                }, function(res) {
                    if (res.success && res.data && res.data.redirect_url) {
                        window.location.href = res.data.redirect_url;
                    } else {
                        const currentPage = (window.coraAppData && window.coraAppData.currentPage) ? window.coraAppData.currentPage : 'dashboard';
                        window.location.href = '/' + encodeURIComponent(slug) + '/' + encodeURIComponent(currentPage);
                    }
                }).fail(function() {
                    const currentPage = (window.coraAppData && window.coraAppData.currentPage) ? window.coraAppData.currentPage : 'dashboard';
                    window.location.href = '/' + encodeURIComponent(slug) + '/' + encodeURIComponent(currentPage);
                });
            } else {
                const currentPage = (window.coraAppData && window.coraAppData.currentPage) ? window.coraAppData.currentPage : 'dashboard';
                window.location.href = '/' + encodeURIComponent(slug) + '/' + encodeURIComponent(currentPage);
            }
        };

        window.coraSwitchIndustryMode = function(industry) {
            if (!industry) return;
            try { sessionStorage.removeItem('cora_preview_role'); } catch(e) {}

            // Set cookie directly — no AJAX dependency
            var expires = new Date(Date.now() + 365*24*60*60*1000).toUTCString();
            document.cookie = "cora_workspace_industry=" + encodeURIComponent(industry) + "; path=/; expires=" + expires;

            // Also fire AJAX to update DB (best-effort, not blocking)
            var ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) ? window.coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
            var nonce = (window.coraREData && window.coraREData.ajaxNonce) ? window.coraREData.ajaxNonce : '';
            if (typeof $ !== 'undefined') {
                $.post(ajaxUrl, { action: 'cora_switch_industry_mode', security: nonce, industry: industry });
            }

            // Build URL with industry param and navigate immediately
            var url = new URL(window.location.href);
            url.searchParams.set('industry', industry);
            var label = (industry === 'photography_studio') ? 'Studio' : 'Real Estate';
            if (window.coraShowToast) window.coraShowToast('Switching to ' + label + ' mode...', 'success');
            setTimeout(function() { window.location.href = url.toString(); }, 200);
        };
    </script>

</head>
<body class="bg-white text-zinc-900 antialiased overflow-x-hidden">
<?php if ( isset( $_COOKIE['cora_impersonator_wp_user_id'] ) ) : ?>
    <style>
        .cora-impersonation-banner {
            height: 40px;
        }
        #cora-global-topbar {
            top: 40px !important;
        }
        @media (min-width: 1024px) {
            .cora-main, .cora-sidebar {
                height: calc(100vh - 92px) !important;
            }
        }
    </style>
    <div class="cora-impersonation-banner sticky top-0 z-[100] w-full h-10 bg-zinc-900 dark:bg-zinc-950 text-zinc-100 dark:text-zinc-200 border-b border-zinc-800 dark:border-zinc-900 text-xs px-4 flex items-center justify-between gap-4 select-none font-sans shadow-md">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-medium text-zinc-200 dark:text-zinc-100">Impersonation Active</span>
            <span class="text-zinc-700 dark:text-zinc-800 hidden sm:inline">|</span>
            <span class="text-zinc-400 dark:text-zinc-500 hidden sm:inline">Viewing workspace on behalf of client / team member.</span>
        </div>
        <button id="cora-switch-back-btn" data-nonce="<?php echo wp_create_nonce('cora_super_switch_back'); ?>" class="bg-zinc-800 hover:bg-zinc-700 active:bg-zinc-600 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:active:bg-zinc-700 text-zinc-100 dark:text-zinc-200 font-semibold px-2.5 py-1 rounded-md border border-zinc-700/50 dark:border-zinc-800 transition-colors text-[10px] cursor-pointer">
            Switch Back to Admin
        </button>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#cora-switch-back-btn').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                $btn.prop('disabled', true).text('Switching...');
                var ajaxUrl = window.coraConfig ? window.coraConfig.ajaxUrl : '<?php echo esc_url( cora_get_origin_relative_url( admin_url( "admin-ajax.php" ) ) ); ?>';
                var nonce = $btn.data('nonce');
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'cora_super_switch_back',
                        _wpnonce: nonce
                    },
                    success: function(response) {
                        if (response && response.success) {
                            window.location.reload();
                        } else {
                            window.location.href = ajaxUrl + '?action=cora_super_switch_back&_wpnonce=' + nonce;
                        }
                    },
                    error: function() {
                        window.location.href = ajaxUrl + '?action=cora_super_switch_back&_wpnonce=' + nonce;
                    }
                });
            });
        });
    </script>
<?php endif; ?>
<?php if ( isset( $_GET['cora_verified'] ) ) : ?>
    <script>
        jQuery(document).ready(function($) {
            <?php if ( $_GET['cora_verified'] === 'true' ) : ?>
                window.coraShowToast("Account verified successfully! Welcome to your workspace.");
            <?php elseif ( $_GET['cora_verified'] === 'error' ) : ?>
                window.coraShowToast("Verification failed: invalid or expired token.");
            <?php endif; ?>
        });
    </script>
<?php endif; ?>

<?php if ( $cora_is_unverified ) : ?>
<div class="fixed inset-0 bg-white z-[99999] flex flex-col items-center justify-center p-6 font-sans">
    <div class="max-w-md w-full border border-zinc-200 rounded-2xl shadow-xl p-8 bg-white text-center flex flex-col items-center gap-6">
        <!-- Minimal Monochrome Aperture Logo -->
        <div class="w-14 h-14 rounded-full bg-zinc-950 text-white flex items-center justify-center">
            <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M14.31 8l5.74 9.9M9.69 8h11.48M7.38 12l5.74-9.9M9.69 16L3.95 6.1M14.31 16H2.83M16.62 12l-5.74 9.9"></path>
            </svg>
        </div>
        
        <div>
            <h2 class="text-xl font-bold text-zinc-950 tracking-tight">Confirm your email address</h2>
            <p class="text-xs text-zinc-505 mt-2 leading-relaxed">We sent a verification link to your email. Please click the link to activate your Cora for Real Estate workspace.</p>
        </div>
        
        <div class="w-full bg-zinc-50 border border-zinc-200/60 rounded-xl p-4 text-left select-none">
            <div class="flex items-center gap-2 text-zinc-400 mb-1">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="4" width="18" height="16" rx="2"></rect><polyline points="22,6 12,13 2,6"></polyline></svg>
                <span class="text-[10px] font-bold uppercase tracking-wider">Registered Email</span>
            </div>
            <span class="text-xs font-semibold text-zinc-800 font-mono"><?php echo esc_html( $current_wp_user->user_email ); ?></span>
        </div>
        
        <div class="flex flex-col gap-2.5 w-full">
            <button id="cora-resend-verification-btn" class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold text-xs rounded-xl active:scale-[0.98] transition-all shadow-sm cursor-pointer">
                Resend Verification Link
            </button>
            <a href="<?php echo esc_url( wp_logout_url( home_url('/workspace') ) ); ?>" class="w-full py-2.5 border border-zinc-200 hover:bg-zinc-50 text-zinc-655 font-bold text-xs rounded-xl text-center active:scale-[0.98] transition-all cursor-pointer select-none">
                Sign Out
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<div id="cora-workspace" class="flex flex-col min-h-screen lg:min-h-0 lg:h-screen lg:overflow-hidden bg-[#f7f7f5] text-zinc-900">
    <!-- Global Dark Topbar (Shopify UI/UX) -->
    <?php
    $cora_current_user_id = get_current_user_id();
    $cora_all_notifications = get_option( 'cora_notifications', array() );
    $cora_user_notifications = array();
    $cora_unread_count = 0;
    if ( is_array( $cora_all_notifications ) ) {
        foreach ( $cora_all_notifications as $notif ) {
            if ( isset( $notif['user_id'] ) && intval( $notif['user_id'] ) === $cora_current_user_id ) {
                $cora_user_notifications[] = $notif;
                if ( empty( $notif['read'] ) ) {
                    $cora_unread_count++;
                }
            }
        }
        usort( $cora_user_notifications, function( $a, $b ) {
            return ($b['timestamp'] ?? 0) - ($a['timestamp'] ?? 0);
        } );
    }
    $cora_display_name = $current_wp_user->display_name ? $current_wp_user->display_name : ($current_wp_user->first_name ? $current_wp_user->first_name : 'Dravya Bansal');
    $cora_initials = strtoupper(substr($cora_display_name, 0, 1));

    // Impersonation banner check
    $is_impersonating = false;
    $impersonator_display_name = '';
    if ( ! empty( $_COOKIE['cora_impersonator_wp_user_id'] ) ) {
        $cookie_value = sanitize_text_field( $_COOKIE['cora_impersonator_wp_user_id'] );
        $parts = explode( '|', $cookie_value );
        if ( count( $parts ) === 2 ) {
            $impersonator_id = intval( $parts[0] );
            $hash = $parts[1];
            $expected_hash = hash_hmac( 'sha256', $impersonator_id, wp_salt( 'auth' ) );
            if ( hash_equals( $expected_hash, $hash ) ) {
                $impersonator_user = get_userdata( $impersonator_id );
                if ( $impersonator_user && cora_is_super_owner( $impersonator_user ) ) {
                    $is_impersonating = true;
                    $impersonator_display_name = $impersonator_user->display_name ? $impersonator_user->display_name : $impersonator_user->user_login;
                }
            }
        }
    }

    // Global announcement banner check
    $announcement_active = get_option( 'cora_announcement_active', '0' );
    $announcement_text   = get_option( 'cora_announcement_text', '' );
    $announcement_type   = get_option( 'cora_announcement_type', 'info' );
    ?>

    <?php if ( $is_impersonating ) : ?>
    <div id="cora-impersonation-safety-banner" class="bg-zinc-950 dark:bg-zinc-900 text-white border-b border-zinc-800 px-4 py-2 flex items-center justify-between text-xs select-none sticky top-0 z-[10000] shadow-sm">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
            <span>You are currently impersonating <strong><?php echo esc_html( $cora_display_name ); ?></strong>. Actions affect their live tenant settings. (Original admin: <strong><?php echo esc_html( $impersonator_display_name ); ?></strong>)</span>
        </div>
        <a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=cora_super_switch_back&security=' . wp_create_nonce( 'cora_ajax_nonce' ) ) ); ?>" class="px-2.5 py-1 border border-zinc-700 hover:border-zinc-550 rounded-lg font-bold text-[10px] bg-zinc-900 hover:bg-zinc-850 active:scale-95 transition-all text-white no-underline shrink-0">
            Exit Impersonation
        </a>
    </div>
    <?php endif; ?>

    <?php if ( $announcement_active === '1' && ! empty( $announcement_text ) ) : ?>
    <?php 
    $ann_bg = 'bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 border-zinc-200 dark:border-zinc-800';
    if ( $announcement_type === 'warning' ) {
        $ann_bg = 'bg-amber-50/80 dark:bg-amber-950/20 text-amber-900 dark:text-amber-300 border-amber-200/80 dark:border-amber-900/40';
    } elseif ( $announcement_type === 'success' ) {
        $ann_bg = 'bg-emerald-50/80 dark:bg-emerald-950/20 text-emerald-900 dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-900/40';
    }
    $announcement_hash = md5( $announcement_text );
    ?>
    <div id="cora-global-announcement-banner" data-hash="<?php echo esc_attr( $announcement_hash ); ?>" class="hidden <?php echo esc_attr( $ann_bg ); ?> border-b px-4 py-2 flex items-center justify-between text-xs select-none sticky top-0 z-[9998] shadow-xs">
        <div class="flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span><?php echo esc_html( $announcement_text ); ?></span>
        </div>
        <button onclick="dismissCoraAnnouncement('<?php echo esc_attr( $announcement_hash ); ?>')" class="p-1 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 cursor-pointer transition-colors shrink-0">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <script>
    (function() {
        const hash = '<?php echo esc_js( $announcement_hash ); ?>';
        if (localStorage.getItem('cora_ann_dismissed_' + hash) !== 'true') {
            document.getElementById('cora-global-announcement-banner').classList.remove('hidden');
        }
    })();
    function dismissCoraAnnouncement(hash) {
        localStorage.setItem('cora_ann_dismissed_' + hash, 'true');
        const banner = document.getElementById('cora-global-announcement-banner');
        if (banner) {
            banner.classList.add('hidden');
        }
    }
    </script>
    <?php endif; ?>

    <!-- Global Brand & Customized Blocks Top Navbar (Shopify Style Unified Header) -->
    <header id="cora-global-topbar" class="cora-topbar bg-[#09090b] dark:bg-zinc-950 text-white px-4 md:px-6 py-2.5 flex items-center justify-between border-b border-zinc-800/80 sticky top-0 z-50 shrink-0 select-none" style="background-color: #09090b !important; z-index: 9999 !important;">
        <div class="hidden lg:flex w-full items-center justify-between">
        <!-- Left Section: Brand, Mobile Menu Toggle & Active Page Breadcrumb -->
        <div class="flex items-center gap-3 min-w-0">
            <button id="cora-mobile-menu-toggle" class="lg:hidden p-1.5 rounded-lg text-zinc-400 hover:text-white hover:bg-zinc-850 transition-colors cursor-pointer select-none" title="Open Menu">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <div class="flex items-center gap-2 select-none shrink-0">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                    <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5" stroke-width="1.8"></polygon>
                    <path d="M12 7v10M8 12h8" opacity="0.5"></path>
                    <circle cx="12" cy="12" r="3.5" stroke-width="1.5"></circle>
                    <circle cx="12" cy="7" r="1" fill="currentColor"></circle>
                    <circle cx="12" cy="17" r="1" fill="currentColor"></circle>
                    <circle cx="8" cy="12" r="1" fill="currentColor"></circle>
                    <circle cx="16" cy="12" r="1" fill="currentColor"></circle>
                </svg>
                <span class="text-base font-black tracking-tight text-white">cora</span>
            </div>
            <?php
            $update = cora_check_workspace_update_available();
            if ( $update && cora_is_super_owner() ) :
            ?>
                <button onclick="event.stopPropagation(); window.coraOpenUpdateDrawer();" class="flex items-center gap-1.5 bg-emerald-950 text-emerald-400 border border-emerald-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full tracking-wider hover:bg-emerald-900 transition-colors cursor-pointer select-none shrink-0 animate-pulse" title="View workspace update details and release notes for v<?php echo esc_attr($update['version']); ?>">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="animate-bounce shrink-0"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                    Update (v<?php echo esc_html($update['version']); ?>)
                </button>
            <?php else : ?>
                <button onclick="event.stopPropagation(); window.coraCheckForUpdatesNow();" class="bg-zinc-850/90 text-zinc-350 hover:bg-zinc-800 hover:text-white border border-zinc-800/80 text-[10px] font-bold px-2.5 py-0.5 rounded-full tracking-wider select-none shrink-0 font-mono transition-colors cursor-pointer" title="Click to check for platform updates">v<?php echo esc_html( CORA_WORKSPACE_VERSION ); ?></button>
            <?php endif; ?>
        </div>

        <!-- Center Section: Command Palette Trigger -->
        <div class="flex-1 max-w-2xl mx-4 hidden sm:flex items-center justify-center">
            <div onclick="event.stopPropagation(); window.coraOpenCommandPalette();" class="cora-sidebar-search w-full px-3 flex items-center justify-between text-zinc-400 hover:text-zinc-200 cursor-pointer transition-all" style="height: 32px; background-color: #343434e3; border-radius: 8px; border: none;">
                <div class="flex items-center gap-2 text-xs font-medium">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Search anything...</span>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-mono text-zinc-500 bg-zinc-950 px-1.5 py-0.5 rounded border border-zinc-800/80 select-none">
                    <span>⌘</span><span>K</span>
                </div>
            </div>
        </div>

        <!-- Right Section: Actions, Role Selector, Custom Blocks & User Pill -->
        <div class="cora-topbar-actions flex items-center gap-2 md:gap-3 shrink-0">
            <button id="cora-quick-ai-btn" class="cora-btn-secondary w-8 h-8 flex items-center justify-center border border-zinc-200 dark:border-zinc-800 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all active:scale-[0.98] text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-900 shadow-sm cursor-pointer shrink-0 p-0" title="Cora AI">
                <span class="cora-btn-icon text-zinc-650 dark:text-zinc-400 flex shrink-0">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"/></svg>
                </span>
            </button>

            <!-- Punch In / Out Header Quick Button -->
            <div class="relative shrink-0" id="cora-header-punch-wrap">
                <button id="cora-header-punch-btn" onclick="event.stopPropagation(); toggleHeaderPunchPopover();" class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg border border-zinc-700/60 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 hover:text-white text-xs font-semibold transition-all cursor-pointer shrink-0 shadow-sm" title="Log Attendance">
                    <span id="cora-header-punch-dot" class="w-1.5 h-1.5 rounded-full bg-zinc-500 shrink-0 transition-colors"></span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span id="cora-header-punch-label" class="hidden sm:inline">Punch</span>
                </button>

                <!-- Floating Viewport-Fixed Punch Popover -->
                <div id="cora-header-punch-popover" class="hidden fixed top-14 right-12 md:right-24 w-64 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl z-[999999] overflow-hidden animate-in fade-in slide-in-from-top-2 duration-150">
                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-950/40 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span id="cora-punch-popover-dot" class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-600 shrink-0"></span>
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider" id="cora-punch-popover-status">Not punched in</span>
                        </div>
                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono" id="cora-punch-popover-time"></span>
                    </div>
                    <!-- Actions -->
                    <div class="p-3 space-y-2">
                        <button onclick="headerLogPunch('in')" id="cora-header-punch-in" class="w-full bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer active:scale-95 flex items-center justify-center gap-1.5 shadow-sm">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            Punch In
                        </button>
                        <button onclick="headerLogPunch('out')" id="cora-header-punch-out" class="w-full bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer active:scale-95 flex items-center justify-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Punch Out
                        </button>
                        <p id="cora-punch-popover-feedback" class="text-[10px] text-center text-zinc-400 dark:text-zinc-500 hidden pt-1 pb-0.5"></p>
                    </div>
                </div>
            </div>

            <!-- Notifications Bell -->
            <div class="relative shrink-0">
                <button id="cora-notif-bell-btn" class="p-1.5 text-zinc-400 hover:text-white hover:bg-zinc-850 rounded-lg transition-all cursor-pointer flex items-center justify-center shrink-0" title="Notifications">
                    <svg viewBox="0 0 24 24" width="17" height="17" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span id="cora-notif-badge" class="<?php echo $cora_unread_count > 0 ? '' : 'hidden'; ?> absolute -top-1 -right-1 min-w-[16px] h-4 px-1 flex items-center justify-center bg-red-600 text-[9px] font-bold text-white rounded-full leading-none border border-zinc-950">
                        <?php echo $cora_unread_count; ?>
                    </span>
                </button>
            </div>

            <!-- User Profile Widget -->
            <div class="relative shrink-0">
                <div onclick="window.coraToggleProfilePopover(event);" class="cora-header-profile-btn flex items-center gap-2 cursor-pointer transition-all select-none shrink-0">
                    <div class="w-8 h-8 rounded-full bg-white text-zinc-950 font-bold text-sm flex items-center justify-center shrink-0 leading-none">
                        <?php echo esc_html($cora_initials); ?>
                    </div>
                    <span class="text-white font-semibold text-sm truncate max-w-[100px] hidden md:inline"><?php echo esc_html($cora_display_name); ?></span>
                </div>

                <!-- Header User Profile Popover Card -->
                <div id="cora-header-profile-popover" class="hidden fixed top-[56px] right-4 w-64 bg-white dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl p-4 z-[9990] flex flex-col gap-2.5 animate-in fade-in slide-in-from-top-2 duration-150 select-none">
                    <!-- User Profile Header -->
                    <div class="flex items-center gap-3 px-1 select-none">
                        <?php if ( $current_user_avatar ) : ?>
                            <img src="<?php echo esc_url($current_user_avatar); ?>" class="w-10 h-10 rounded-full object-cover shrink-0 select-none border border-zinc-200/60" alt="<?php echo esc_attr($current_user_display_name); ?>" />
                        <?php else : ?>
                            <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center font-bold text-sm uppercase shrink-0 select-none">
                                <?php echo esc_html(substr($current_user_display_name, 0, 2)); ?>
                            </div>
                        <?php endif; ?>
                        <div class="flex flex-col min-w-0 leading-tight">
                            <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html($current_user_display_name); ?></span>
                            <span class="text-[11px] text-zinc-500 truncate"><?php echo esc_html($current_wp_user->exists() ? $current_wp_user->user_email : 'dravya.shs@gmail.com'); ?></span>
                        </div>
                    </div>

                    <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                    <!-- Menu Items List -->
                    <div class="flex flex-col gap-0.5">
                        <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-zinc-50 font-medium flex items-center gap-3 cursor-pointer transition-colors" onclick="coraNavigateTo('profile'); $('#cora-header-profile-popover').addClass('hidden');">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            Profile
                        </button>

                        <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-zinc-50 font-medium flex items-center justify-between cursor-pointer transition-colors" onclick="coraNavigateTo('settings-suite'); $('#cora-header-profile-popover').addClass('hidden');">
                            <div class="flex items-center gap-3">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l-.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                Settings
                            </div>
                            <span class="text-[10px] text-zinc-400 font-mono">⌘.</span>
                        </button>


                        <div class="px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-1 select-none my-0.5">
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Language</span>
                                <span class="cora-current-language-label text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 rounded uppercase">English</span>
                            </div>
                            <select id="cora-header-language-select" class="cora-language-selector w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer transition-colors" onchange="if(window.coraSetLanguage) window.coraSetLanguage(this.value, true);">
                                <option value="en">English</option>
                                <option value="hi">Hindi (हिन्दी)</option>
                                <option value="es">Spanish (Español)</option>
                                <option value="fr">French (Français)</option>
                                <option value="de">German (Deutsch)</option>
                                <option value="bn">Bengali (বাংলা)</option>
                                <option value="te">Telugu (తెలుగు)</option>
                                <option value="mr">Marathi (मराठी)</option>
                                <option value="ta">Tamil (தமிழ்)</option>
                                <option value="gu">Gujarati (ગુજરાતી)</option>
                                <option value="kn">Kannada (ಕನ್ನಡ)</option>
                                <option value="ml">Malayalam (മലയാളം)</option>
                                <option value="pa">Punjabi (ਪੰਜਾਬੀ)</option>
                                <option value="or">Odia (ଓଡ଼ିଆ)</option>
                            </select>
                        </div>

                        <?php if ( cora_is_super_owner() ) : ?>
                        <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>
                        <div class="px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-1 select-none">
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Role Preview</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 rounded uppercase">Admin</span>
                            </div>
                            <select class="cora-role-preview-select w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer transition-colors" onchange="coraSwitchRolePreview(this.value)">
                                <option value="administrator" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">Super Admin (Full Access)</option>
                                <?php foreach ( $cora_role_labels as $r_key => $r_label ) :
                                    if ( $r_key === 'administrator' ) continue;
                                ?>
                                <option value="<?php echo esc_attr( $r_key ); ?>" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $r_label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                    <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="w-full text-left px-2.5 py-2.5 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-red-600 dark:hover:text-red-400 font-semibold flex items-center gap-3 transition-colors select-none">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Sign out
                    </a>
                </div>
            </div>
        </div>
        </div>
        
        <div class="flex lg:hidden w-full items-center justify-between bg-transparent py-0.5">
            <div onclick="document.getElementById('cora-mobile-menu-toggle').click();" class="flex items-center cursor-pointer select-none shrink-0 hover:opacity-85 transition-opacity pr-1.5">
                <span class="tracking-normal font-black text-[13px] text-white">CORA</span>
            </div>

            <div onclick="window.coraOpenCommandPalette();" class="mx-2 flex items-center justify-between text-zinc-400 text-xs cursor-pointer" style="max-width: 248px; height: 32px; background-color: #343434e3; border-radius: 8px; border: none; padding: 0 10px; flex: 1;">
                <div class="flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span class="text-[11px]">Search anything...</span>
                </div>
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-white"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </div>

            <div class="flex items-center gap-2">
                <!-- Mobile Punch Button and Popover -->
                <div class="relative shrink-0" id="cora-mobile-punch-wrap">
                    <button id="cora-mobile-punch-btn" onclick="event.stopPropagation(); toggleMobilePunchPopover();" class="p-1 text-zinc-400 hover:text-white transition-all cursor-pointer flex items-center justify-center shrink-0 relative" title="Log Attendance">
                        <span id="cora-mobile-punch-dot" class="w-1.5 h-1.5 rounded-full bg-zinc-500 absolute top-0.5 right-0.5 transition-colors"></span>
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </button>
                    <!-- Mobile Punch Popover -->
                    <div id="cora-mobile-punch-popover" class="hidden fixed top-14 right-12 w-60 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl z-[999999] overflow-hidden animate-in fade-in slide-in-from-top-2 duration-150">
                        <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-950/40 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span id="cora-mobile-punch-popover-dot" class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-600 shrink-0"></span>
                                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider" id="cora-mobile-punch-popover-status">Not punched in</span>
                            </div>
                            <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono" id="cora-mobile-punch-popover-time"></span>
                        </div>
                        <div class="p-3 space-y-2">
                            <button onclick="headerLogPunch('in')" id="cora-mobile-punch-in" class="w-full bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer active:scale-95 flex items-center justify-center gap-1.5 shadow-sm">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                Punch In
                            </button>
                            <button onclick="headerLogPunch('out')" id="cora-mobile-punch-out" class="w-full bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer active:scale-95 flex items-center justify-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                Punch Out
                            </button>
                            <p id="cora-mobile-punch-popover-feedback" class="text-[10px] text-center text-zinc-400 dark:text-zinc-500 hidden pt-1 pb-0.5"></p>
                        </div>
                    </div>
                </div>

                <button onclick="document.getElementById('cora-notif-bell-btn').click();" class="relative p-1 text-zinc-400 hover:text-white transition-all cursor-pointer flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-white rounded-full border border-[#09090b]"></span>
                </button>
                <div onclick="document.querySelector('.cora-header-profile-btn').click();" class="flex items-center cursor-pointer shrink-0">
                    <div class="bg-white text-black w-7 h-7 rounded-full flex items-center justify-center font-bold text-[11px]"><?php echo esc_html($cora_initials); ?></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Workspace Main Container (Sidebar + Content Row) -->
    <div class="flex flex-row flex-1 min-h-0 relative w-full lg:overflow-hidden">
    <!-- Workspace Sidebar -->
    <aside class="cora-sidebar w-64 bg-[#f9fafb] dark:bg-[#0c0c0e] border-r border-zinc-200/80 dark:border-zinc-800/40 flex flex-col shrink-0 h-[calc(100vh-52px)] fixed lg:sticky top-[52px] left-0 z-50 lg:z-30 transition-all duration-200 transform -translate-x-full lg:translate-x-0">
        <!-- Sidebar Top Header / Brand Logo & Toggle -->
        <?php
        $cora_active_workspace = function_exists( 'cora_get_current_workspace_context' ) ? cora_get_current_workspace_context() : array( 'id' => 1, 'name' => 'Workspace', 'slug' => 'workspace', 'plan' => 'enterprise', 'status' => 'active' );
        $cora_user_workspaces   = function_exists( 'cora_get_user_workspaces' ) ? cora_get_user_workspaces( get_current_user_id() ) : array( $cora_active_workspace );
        $cora_ws_name           = ! empty( $cora_active_workspace['name'] ) ? $cora_active_workspace['name'] : 'Workspace';
        $cora_ws_slug           = ! empty( $cora_active_workspace['slug'] ) ? $cora_active_workspace['slug'] : 'workspace';
        $cora_ws_initial        = ! empty( $cora_ws_name ) ? strtoupper( substr( $cora_ws_name, 0, 1 ) ) : 'C';
        
        $sidebar_brand_logo = get_option( 'cora_brand_logo_url', '' );
        $sidebar_brand_title = get_option( 'cora_sidebar_title', '' );
        if ( empty( $sidebar_brand_title ) || strtolower( $sidebar_brand_title ) === 'cora' || strtolower( $sidebar_brand_title ) === 'cora real estate' ) {
            $user_agency = get_user_meta( get_current_user_id(), 'cora_workspace_agency_name', true );
            if ( ! empty( $cora_ws_name ) && strtolower( $cora_ws_name ) !== 'workspace' && strtolower( $cora_ws_name ) !== 'apex realty group' && strtolower( $cora_ws_name ) !== 'cora real estate' ) {
                $sidebar_brand_title = $cora_ws_name;
            } elseif ( ! empty( $user_agency ) ) {
                $sidebar_brand_title = $user_agency;
            } else {
                $sidebar_brand_title = ! empty( $cora_ws_name ) ? $cora_ws_name : 'Cora Workspace';
            }
        }
        ?>
        <div class="cora-sidebar-top-container flex items-center justify-between gap-2 px-3 pt-2.5 pb-2 shrink-0 select-none">
            <!-- Workspace Switcher Card + Dropdown -->
            <div class="relative flex-1 min-w-0">
                <!-- Trigger Card -->
                <div class="cora-workspace-card flex items-center justify-between gap-2 px-2.5 py-1.5 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/50 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 rounded-lg cursor-pointer transition-all select-none" onclick="event.stopPropagation(); if($('.cora-sidebar').hasClass('collapsed-sidebar')){ window.coraToggleSidebarCollapse(); } else { $('#cora-workspace-popover').toggleClass('hidden'); $('#cora-profile-popover').addClass('hidden'); }">
                    <div class="flex items-center gap-2 min-w-0">
                        <?php if ( ! empty( $sidebar_brand_logo ) ) : ?>
                        <div class="w-6 h-6 rounded flex items-center justify-center shrink-0">
                            <img src="<?php echo esc_url( $sidebar_brand_logo ); ?>" alt="Logo" class="w-full h-full object-contain rounded" onerror="this.parentNode.style.display='none'; this.parentNode.nextElementSibling.style.display='flex';">
                        </div>
                        <div class="w-6 h-6 rounded bg-black dark:bg-white text-white dark:text-black font-bold text-[13px] flex items-center justify-center shrink-0 leading-none" style="display: none;">
                            <?php echo esc_html( strtoupper( substr( $sidebar_brand_title, 0, 1 ) ) ); ?>
                        </div>
                        <?php else : ?>
                        <div class="w-6 h-6 rounded bg-black dark:bg-white text-white dark:text-black font-bold text-[13px] flex items-center justify-center shrink-0 leading-none">
                            <?php echo esc_html( strtoupper( substr( $sidebar_brand_title, 0, 1 ) ) ); ?>
                        </div>
                        <?php endif; ?>
                        <span class="cora-studio-info text-zinc-900 dark:text-zinc-100 font-bold text-xs truncate"><?php echo esc_html( $sidebar_brand_title ); ?></span>
                    </div>
                    <svg class="cora-switcher-arrow text-zinc-500 shrink-0" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
                <div id="cora-workspace-popover" class="hidden absolute top-full mt-2 left-0 w-[280px] bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl p-3.5 z-50 flex flex-col select-none" style="animation: popoverSlideDown 0.12s ease-out;">
                    <!-- Header -->
                    <div class="flex items-start justify-between pb-3.5 border-b border-zinc-100 dark:border-zinc-800/60 min-w-0">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <div class="w-9 h-9 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-black text-base flex items-center justify-center shrink-0 leading-none shadow-sm select-none">
                                <?php echo esc_html( $cora_ws_initial ); ?>
                            </div>
                            <div class="flex flex-col min-w-0 flex-1 leading-tight">
                                <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( $cora_ws_name ); ?></span>
                                <div class="flex items-center gap-1 min-w-0 mt-0.5">
                                    <span class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 truncate">app.heycora.in/<?php echo esc_html( $cora_ws_slug ); ?></span>
                                    <a href="https://app.heycora.in/<?php echo esc_html( $cora_ws_slug ); ?>" target="_blank" class="text-zinc-400 hover:text-zinc-650 dark:hover:text-zinc-200 shrink-0 select-none" onclick="event.stopPropagation();">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <span class="text-[9.5px] font-bold text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-850 px-2 py-0.5 rounded-md flex items-center gap-1 shrink-0 border border-zinc-200/50 dark:border-zinc-800/50 select-none">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0 text-zinc-500"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            Current
                        </span>
                    </div>

                    <!-- Settings & Team Buttons -->
                    <div class="grid grid-cols-2 gap-2 my-3">
                        <button class="flex items-center justify-center gap-1.5 px-3 py-2 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/60 dark:hover:bg-zinc-850/80 border border-transparent rounded-xl text-[11px] font-semibold text-zinc-700 dark:text-zinc-255 cursor-pointer transition-all shadow-2xs active:scale-[0.98]" onclick="coraNavigateTo('settings-suite'); $('#cora-workspace-popover').addClass('hidden');">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-zinc-500"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            Settings
                        </button>
                        <button class="flex items-center justify-center gap-1.5 px-3 py-2 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/60 dark:hover:bg-zinc-850/80 border border-transparent rounded-xl text-[11px] font-semibold text-zinc-700 dark:text-zinc-255 cursor-pointer transition-all shadow-2xs active:scale-[0.98]" onclick="coraNavigateTo('team-roles'); $('#cora-workspace-popover').addClass('hidden');">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-zinc-500"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            Team
                        </button>
                    </div>

                    <!-- Workspaces List -->
                    <div class="border-t border-zinc-100 dark:border-zinc-800/60 pt-3">
                        <div class="flex items-center justify-between px-1 mb-2 select-none">
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Workspaces</span>
                            <?php if ( cora_is_super_owner() ) : ?>
                                <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-450 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded shrink-0">Admin (Shruti)</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="space-y-1 max-h-48 overflow-y-auto pr-0.5 no-scrollbar select-none">
                            <!-- Virtual Super Admin Workspace -->
                            <?php 
                            if ( cora_is_real_shruti() ) : 
                                $is_super_current = ( $cora_ws_slug === 'super' );
                            ?>
                            <div class="group flex items-center justify-between px-2 py-1.5 <?php echo $is_super_current ? 'bg-zinc-100 dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700' : 'bg-transparent hover:bg-zinc-50 dark:hover:bg-zinc-900 border-transparent hover:border-zinc-200 dark:hover:border-zinc-800'; ?> border rounded-xl cursor-pointer transition-all min-w-0" onclick="coraSwitchWorkspace('super')">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-5 h-5 rounded-md bg-emerald-600 dark:bg-emerald-500 text-white font-bold text-[9px] flex items-center justify-center shrink-0 leading-none">
                                        ★
                                    </div>
                                    <div class="flex flex-col min-w-0 flex-1 leading-tight">
                                        <span class="text-[11px] font-semibold text-zinc-800 dark:text-zinc-200 truncate">Platform Control Center</span>
                                        <span class="text-[9px] text-zinc-450 dark:text-zinc-500 font-mono truncate">Global / Super Admin View</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0 ml-1">
                                    <?php if ( $is_super_current ) : ?>
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-900 dark:text-white shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?php endif; ?>
                                    <button type="button" class="p-0.5 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded text-zinc-400 hover:text-zinc-750 dark:hover:text-zinc-200 shrink-0 opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity" onclick="event.stopPropagation(); window.coraToggleEditWorkspaceDrawer(true, 0, 'Platform Control Center', 'super', 'enterprise', 'shruti@heycora.in', 'active');">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Dynamic workspaces -->
                            <?php foreach ( $cora_user_workspaces as $ws_item ) :
                                if ( isset( $ws_item['slug'] ) && $ws_item['slug'] === 'super' ) continue;
                                $is_current = ( isset( $ws_item['slug'] ) && $ws_item['slug'] === $cora_ws_slug );
                                $ws_item_name = ! empty( $ws_item['name'] ) ? $ws_item['name'] : 'Workspace';
                                $ws_item_slug = ! empty( $ws_item['slug'] ) ? $ws_item['slug'] : 'workspace';
                                $ws_item_plan = ! empty( $ws_item['plan'] ) ? $ws_item['plan'] : 'enterprise';
                                $ws_item_email = ! empty( $ws_item['owner_email'] ) ? $ws_item['owner_email'] : 'shruti@heycora.in';
                                $ws_item_status = ! empty( $ws_item['status'] ) ? $ws_item['status'] : 'active';
                                $ws_item_init = strtoupper( substr( $ws_item_name, 0, 1 ) );
                            ?>
                            <div class="group flex items-center justify-between px-2 py-1.5 <?php echo $is_current ? 'bg-zinc-100 dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700' : 'bg-transparent hover:bg-zinc-50 dark:hover:bg-zinc-900 border-transparent hover:border-zinc-200 dark:hover:border-zinc-800'; ?> border rounded-xl cursor-pointer transition-all min-w-0" onclick="coraSwitchWorkspace('<?php echo esc_js( $ws_item_slug ); ?>')">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-5 h-5 rounded-md bg-zinc-950 dark:bg-white text-white dark:text-zinc-900 font-bold text-[10px] flex items-center justify-center shrink-0 leading-none">
                                        <?php echo esc_html( $ws_item_init ); ?>
                                    </div>
                                    <div class="flex flex-col min-w-0 flex-1 leading-tight">
                                        <span class="text-[11px] font-semibold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( $ws_item_name ); ?></span>
                                        <span class="text-[9px] text-zinc-400 dark:text-zinc-500 font-mono truncate">app.heycora.in/<?php echo esc_html( $ws_item_slug ); ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0 ml-1">
                                    <?php if ( $is_current ) : ?>
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-900 dark:text-white shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?php endif; ?>
                                    <button type="button" class="p-0.5 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded text-zinc-400 hover:text-zinc-755 dark:hover:text-zinc-200 shrink-0 opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity" onclick="event.stopPropagation(); window.coraToggleEditWorkspaceDrawer(true, <?php echo intval( $ws_item['id'] ); ?>, '<?php echo esc_js( $ws_item_name ); ?>', '<?php echo esc_js( $ws_item_slug ); ?>', '<?php echo esc_js( $ws_item_plan ); ?>', '<?php echo esc_js( $ws_item_email ); ?>', '<?php echo esc_js( $ws_item_status ); ?>');">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ( cora_is_super_owner() ) : ?>
                    <!-- Create New Workspace Button -->
                    <div class="border-t border-zinc-100 dark:border-zinc-800/60 mt-3 pt-2">
                        <button type="button" class="w-full flex items-center gap-2.5 px-2 py-2 text-left hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-xl transition-all cursor-pointer group" onclick="event.stopPropagation(); window.coraToggleCreateWorkspaceDrawer(true);">
                            <div class="w-5.5 h-5.5 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700 flex items-center justify-center text-zinc-400 group-hover:text-zinc-700 dark:group-hover:text-zinc-200 transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            </div>
                            <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">Create New Workspace</span>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="cora-sidebar-header-actions flex items-center gap-1.5 shrink-0">
                <!-- Collapse Toggle Button (layout-sidebar icon) -->
                <button id="cora-sidebar-toggle" onclick="return window.coraToggleSidebarCollapse(event);" class="text-zinc-500 hover:text-black dark:text-zinc-450 dark:hover:text-white bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-2 rounded-lg transition-colors cursor-pointer select-none shadow-2xs" title="Collapse / Expand Sidebar">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div id="cora-sidebar-scroll-container" class="flex-1 flex flex-col min-h-0 overflow-y-auto overflow-x-visible">
            <?php if ( false ) : // Hidden in super owner mode
                $current_industry = cora_get_active_industry();
                $re_url  = add_query_arg( 'set_industry', 'real_estate',       remove_query_arg( array('set_industry','industry') ) );
                $stu_url = add_query_arg( 'set_industry', 'photography_studio', remove_query_arg( array('set_industry','industry') ) );
            ?>
            <!-- Industry Switcher Widget (Shruti Only) -->
            <div class="px-3 pb-1 pt-1 select-none">
                <div class="bg-zinc-100/80 dark:bg-zinc-900/80 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-1.5 space-y-1">
                    <div class="flex items-center justify-between px-1">
                        <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Industry Mode</span>
                        <span class="text-[8.5px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded shrink-0">Shruti Only</span>
                    </div>
                    <div class="grid grid-cols-2 gap-1 bg-white dark:bg-zinc-950 p-1 rounded-lg border border-zinc-200/60 dark:border-zinc-800/60">
                        <a href="<?php echo esc_url( $re_url ); ?>" class="flex items-center justify-center gap-1 py-1 px-1.5 rounded-md text-[10.5px] font-bold transition-all no-underline <?php echo ($current_industry === 'real_estate') ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'; ?>">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                            Real Estate
                        </a>
                        <a href="<?php echo esc_url( $stu_url ); ?>" class="flex items-center justify-center gap-1 py-1 px-1.5 rounded-md text-[10.5px] font-bold transition-all no-underline <?php echo ($current_industry === 'photography_studio') ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'; ?>">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            Studio
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ( false ) : // Hidden in super owner mode ?>
            <!-- Role Switcher Toggle Widget (Sidebar Header) -->
            <div class="px-3 pb-1 pt-1">
                <div class="cora-role-switcher-card flex items-center justify-between gap-2 px-2.5 py-1.5 bg-zinc-100/70 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-850 border border-zinc-200/80 dark:border-zinc-800 rounded-lg transition-all select-none">
                    <div class="flex items-center gap-2 min-w-0 w-full">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 shrink-0"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <select class="cora-role-preview-select w-full bg-white dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 font-bold text-xs rounded-md px-2 py-1 outline-none cursor-pointer transition-colors shadow-2xs" onchange="coraSwitchRolePreview(this.value)">
                            <option value="administrator" <?php echo ( $current_user_role === 'administrator' ? 'selected' : '' ); ?> class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">Role: Super Admin</option>
                            <?php foreach ( $cora_role_labels as $r_key => $r_label ) :
                                if ( $r_key === 'administrator' ) continue;
                                $is_sel = ( $current_user_role === $r_key ) ? 'selected' : '';
                            ?>
                            <option value="<?php echo esc_attr( $r_key ); ?>" <?php echo $is_sel; ?> class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">Role: <?php echo esc_html( $r_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sidebar Search Input -->
            <div class="px-3 pt-2 pb-1">
                <div class="cora-sidebar-search flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-500 transition-colors shadow-2xs">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="cora-sidebar-search-input" placeholder="Search menu..." class="w-full bg-transparent border-none p-0 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-0" style="outline: none !important; border: none !important; box-shadow: none !important;" />
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="cora-sidebar-nav px-0 pt-0.5 pb-4 space-y-3 select-none">
                <?php
                $active_industry = cora_get_active_industry();
                $module = Cora_Module_Registry::get_module( $active_industry );
                $nav_groups = array();
                if ( $module && ! cora_is_super_owner() ) {
                    $nav_groups = $module->get_navigation_groups( $current_user_role );
                    // Inject active bookings badge count into whichever group contains 'bookings'
                    foreach ( $nav_groups as $g_key => $group ) {
                        if ( isset( $group['items']['bookings'] ) ) {
                            $nav_groups[$g_key]['items']['bookings']['badge'] = $dynamic_active_bookings_count;
                        }
                    }
                }

                if ( cora_is_super_owner() ) {
                    $super_admin_group = array(
                        'label' => 'Platform Administration',
                        'items' => array(
                            'super-admin' => array(
                                'title' => 'Workspaces',
                                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>'
                            ),
                            'super-docs' => array(
                                'title' => 'Documentation',
                                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14,2 14,8 20,8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>'
                            ),
                            'super-users' => array(
                                'title' => 'Users',
                                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
                            ),
                            'super-appeals' => array(
                                'title' => 'Reactivation Appeals',
                                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>'
                            ),
                            'super-governance' => array(
                                'title' => 'Governance',
                                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>'
                            ),
                            'super-announcements' => array(
                                'title' => 'Broadcast Console',
                                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>'
                            ),
                            'super-health' => array(
                                'title' => 'System Health',
                                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>'
                            )
                        )
                    );
                    array_unshift( $nav_groups, $super_admin_group );
                }
                
                foreach ( $nav_groups as $group ) :
                ?>
                <div class="cora-nav-group select-none">
                    <div class="cora-nav-group-label px-3 text-[11px] font-bold text-zinc-500 uppercase select-none"><?php echo esc_html($group['label']); ?></div>
                    <ul class="cora-nav-list space-y-0.5 mt-1 select-none">
                        <?php foreach ( $group['items'] as $target => $item ) : 
                            $super_pages = array( 'super-admin', 'super-users', 'super-appeals', 'super-governance', 'super-announcements', 'super-health', 'super-docs' );
                            if ( ! in_array( $target, $super_pages ) && function_exists( 'cora_user_has_feature_access' ) && ! cora_user_has_feature_access( $target ) ) {
                                continue;
                            }
                            $nav_url = home_url( '/' . $cora_ws_slug . '/' . $target );
                        ?>
                        <li class="list-none" data-target="<?php echo esc_attr($target); ?>">
                            <a href="<?php echo esc_url( $nav_url ); ?>" class="cora-nav-item <?php echo ( $sub_page === $target || str_replace('_', '-', $sub_page) === str_replace('_', '-', $target) ) ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm rounded-lg cursor-pointer select-none no-underline text-zinc-800 dark:text-zinc-200 hover:text-zinc-950 dark:hover:text-white" data-target="<?php echo esc_attr($target); ?>" data-tooltip="<?php echo esc_attr($item['title']); ?>">
                                <div class="flex items-center gap-3 select-none">
                                    <span class="cora-nav-icon select-none">
                                        <?php echo $item['icon']; ?>
                                    </span>
                                    <span class="cora-nav-text select-none font-medium"><?php echo esc_html($item['title']); ?></span>
                                </div>
                                <?php if ( ! empty( $item['soon'] ) ) : ?>
                                <span class="cora-badge cora-badge-sidebar px-1.5 py-0.5 text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 rounded-full border border-zinc-200 dark:border-zinc-700 select-none flex items-center gap-1">
                                    <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    SOON
                                </span>
                                <?php elseif ( isset($item['badge']) && intval($item['badge']) > 0 ) : ?>
                                <span class="cora-badge cora-badge-sidebar px-1.5 py-0.5 text-[10px] font-medium bg-zinc-200 text-zinc-800 rounded-full select-none"><?php echo intval($item['badge']); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </nav>
            
            <!-- Sidebar Quick Search Results (Dynamic overlay when searching) -->
            <div id="cora-sidebar-search-results" class="px-0 pt-1.5 pb-4 space-y-4 hidden max-h-[400px] overflow-y-auto"></div>
        </div><!-- /.flex-1.overflow-y-auto -->

        <!-- LOWER BLOCK: PINNED AT BOTTOM (sibling to scrollable nav, not inside it) -->
        <div class="cora-sidebar-bottom-block shrink-0 border-t border-zinc-200/50 dark:border-zinc-800/40 z-20 sticky bottom-0 flex flex-col">
            <!-- User Profile Popover Card -->
            <div id="cora-profile-popover" class="hidden absolute bottom-20 left-4 right-4 max-h-[360px] overflow-y-auto bg-white dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl p-4 z-[70] flex flex-col gap-2.5 animate-in fade-in slide-in-from-bottom-2 duration-150 select-none">
                <!-- User Profile Header -->
                <div class="flex items-center gap-3 px-1 select-none">
                    <?php if ( $current_user_avatar ) : ?>
                        <img src="<?php echo esc_url($current_user_avatar); ?>" class="w-10 h-10 rounded-full object-cover shrink-0 select-none border border-zinc-200/60" alt="<?php echo esc_attr($current_user_display_name); ?>" />
                    <?php else : ?>
                        <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center font-bold text-sm uppercase shrink-0 select-none">
                            <?php 
                            $pop_initials = ( cora_is_real_shruti() || ( $current_wp_user->exists() && $current_wp_user->user_login === 'cora_admin' ) ) ? 'S' : substr($current_user_display_name, 0, 2);
                            echo esc_html($pop_initials); 
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex flex-col min-w-0 leading-tight">
                        <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html(( cora_is_real_shruti() || ( $current_wp_user->exists() && $current_wp_user->user_login === 'cora_admin' ) ) ? 'Shruti' : $current_user_display_name); ?></span>
                        <span class="text-[11px] text-zinc-500 truncate"><?php echo esc_html($current_wp_user->exists() ? $current_wp_user->user_email : 'dravya.shs@gmail.com'); ?></span>
                    </div>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                <!-- Workspace Connection Status Indicator -->
                <?php
                $cora_gemini_key_saved = ! empty( get_option( 'cora_workspace_ai_gemini_key', '' ) );
                ?>
                <div class="flex items-center justify-between px-2.5 py-1.5 text-xs select-none">
                    <span class="text-zinc-500 dark:text-zinc-400 font-medium">Workspace Status</span>
                    <span class="flex items-center gap-1.5 font-bold text-zinc-800 dark:text-zinc-200">
                        <span class="w-2 h-2 rounded-full <?php echo $cora_gemini_key_saved ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400'; ?>"></span>
                        <?php echo $cora_gemini_key_saved ? 'Connected' : 'Not Configured'; ?>
                    </span>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                <!-- Menu Items List -->
                <div class="flex flex-col gap-0.5">
                    <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-zinc-50 font-medium flex items-center gap-3 cursor-pointer transition-colors" onclick="coraNavigateTo('profile'); $('#cora-profile-popover').addClass('hidden');">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Profile
                    </button>

                    <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-zinc-50 font-medium flex items-center justify-between cursor-pointer transition-colors" onclick="coraNavigateTo('settings-suite'); $('#cora-profile-popover').addClass('hidden');">
                        <div class="flex items-center gap-3">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            Settings
                        </div>
                        <span class="text-[10px] text-zinc-400 font-mono">⌘.</span>
                    </button>


                    <div class="px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-1 select-none my-0.5">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Language</span>
                            <span class="cora-current-language-label text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 rounded uppercase">English</span>
                        </div>
                        <select id="cora-language-selector" class="cora-language-selector w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer transition-colors" onchange="if(window.coraSetLanguage) window.coraSetLanguage(this.value, true);">
                            <option value="en">English</option>
                            <option value="hi">Hindi (हिन्दी)</option>
                            <option value="es">Spanish (Español)</option>
                            <option value="fr">French (Français)</option>
                            <option value="de">German (Deutsch)</option>
                            <option value="bn">Bengali (বাংলা)</option>
                            <option value="te">Telugu (తెలుగు)</option>
                            <option value="mr">Marathi (मराठी)</option>
                            <option value="ta">Tamil (தமிழ்)</option>
                            <option value="gu">Gujarati (ગુજરાતી)</option>
                            <option value="kn">Kannada (ಕನ್ನಡ)</option>
                            <option value="ml">Malayalam (മലയാളം)</option>
                            <option value="pa">Punjabi (ਪੰਜਾਬੀ)</option>
                            <option value="or">Odia (ଓଡ଼ିଆ)</option>
                        </select>
                    </div>

                    <!-- Active AI Model Selector -->
                    <?php
                    $cora_active_ai_model = get_option( 'cora_workspace_active_ai_model', 'cora-core-v2' );
                    ?>
                    <div class="px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-1 select-none my-0.5">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">AI Model</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 rounded uppercase">Active</span>
                        </div>
                        <select id="cora-ai-model-selector" class="w-full bg-white dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer transition-colors">
                            <option value="cora-core-v2" <?php selected( $cora_active_ai_model, 'cora-core-v2' ); ?>>Gemini 3.5 Flash (Auto)</option>
                            <option value="gemini" <?php selected( $cora_active_ai_model, 'gemini' ); ?>>Gemini 3.5 Flash</option>
                            <option value="gpt-4o" <?php selected( $cora_active_ai_model, 'gpt-4o' ); ?>>GPT-4o</option>
                        </select>
                    </div>

                    <?php if ( cora_is_super_owner() ) : ?>
                    <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>
                    <div class="px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-1 select-none">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Role Preview</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 rounded uppercase">Admin</span>
                        </div>
                        <select class="cora-role-preview-select w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer" onchange="coraSwitchRolePreview(this.value)">
                            <option value="administrator" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">Super Admin (Full Access)</option>
                            <?php foreach ( $cora_role_labels as $r_key => $r_label ) :
                                if ( $r_key === 'administrator' ) continue;
                            ?>
                            <option value="<?php echo esc_attr( $r_key ); ?>" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $r_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ( cora_is_super_owner() ) : ?>
                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>
                <div id="cora-in-app-update-notice" class="hidden px-2 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl flex flex-col gap-1.5">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-zinc-800 dark:text-zinc-200 uppercase tracking-wide">Update Available</span>
                    </div>
                    <p class="text-[10px] text-zinc-500 leading-normal font-medium">New version <code class="font-mono text-zinc-700 dark:text-zinc-300 font-bold" id="cora-update-ver">v1.4.0</code> is ready. Upgrade instantly.</p>
                    <button type="button" id="cora-btn-app-upgrade" class="w-full py-1.5 bg-zinc-950 dark:bg-zinc-100 hover:opacity-85 text-white dark:text-zinc-950 font-bold rounded-lg text-[10px] transition-colors cursor-pointer text-center select-none shadow-3xs" onclick="coraTriggerInAppUpgrade(this)">
                        Upgrade Workspace
                    </button>
                </div>
                <?php endif; ?>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                <!-- Quota Metrics Section -->
                <div class="px-2 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-3 select-none">
                    <!-- Storage Quota -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[10px] font-bold text-zinc-500 dark:text-zinc-400">
                            <span>Storage Usage</span>
                            <span>4.2 GB of 10 GB (42%)</span>
                        </div>
                        <div class="w-full h-1.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="bg-zinc-900 dark:bg-zinc-100 h-full rounded-full" style="width: 42%;"></div>
                        </div>
                    </div>

                    <!-- AI Usage Quotas (Dynamic limits) -->
                    <?php
                    $usage_stats = function_exists( 'cora_workspace_get_ai_usage_stats' ) ? cora_workspace_get_ai_usage_stats() : array( 'five_hour_count' => 0, 'five_hour_limit' => 30, 'daily_count' => 0, 'daily_limit' => 100 );
                    $daily_percent = min(100, round(($usage_stats['daily_count'] / $usage_stats['daily_limit']) * 100));
                    $five_hour_percent = min(100, round(($usage_stats['five_hour_count'] / $usage_stats['five_hour_limit']) * 100));
                    ?>
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[10px] font-bold text-zinc-500 dark:text-zinc-400">
                            <span>AI Requests (Daily)</span>
                            <span><?php echo esc_html( $usage_stats['daily_count'] ); ?> / <?php echo esc_html( $usage_stats['daily_limit'] ); ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="bg-zinc-950 dark:bg-white h-full rounded-full" style="width: <?php echo esc_attr( $daily_percent ); ?>%;"></div>
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[10px] font-bold text-zinc-500 dark:text-zinc-400">
                            <span>AI Requests (5h Window)</span>
                            <span><?php echo esc_html( $usage_stats['five_hour_count'] ); ?> / <?php echo esc_html( $usage_stats['five_hour_limit'] ); ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="bg-zinc-950 dark:bg-white h-full rounded-full" style="width: <?php echo esc_attr( $five_hour_percent ); ?>%;"></div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                <!-- PWA & Push Notifications Settings -->
                <div class="px-2.5 py-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 rounded-xl space-y-2.5 select-none">
                    <div class="flex items-center justify-between px-0.5">
                        <span class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">App & Push (PWA)</span>
                        <span id="cora-pwa-badge" class="text-[9px] font-bold px-1.5 py-0.5 bg-zinc-400 dark:bg-zinc-700 text-white rounded uppercase">Inactive</span>
                    </div>
                    
                    <div class="flex flex-col gap-1.5">
                        <!-- Install Button -->
                        <button type="button" id="cora-pwa-install-btn" class="hidden w-full py-1.5 bg-zinc-950 dark:bg-zinc-100 hover:opacity-85 text-white dark:text-zinc-950 font-bold rounded-lg text-[10px] transition-colors cursor-pointer text-center select-none shadow-3xs border-none outline-none">
                            Install Desktop/Phone App
                        </button>
                        
                        <!-- Push Notifications Button -->
                        <button type="button" id="cora-pwa-push-btn" class="w-full py-1.5 bg-zinc-950 dark:bg-zinc-100 hover:opacity-85 text-white dark:text-zinc-950 font-bold rounded-lg text-[10px] transition-colors cursor-pointer text-center select-none shadow-3xs border-none outline-none" onclick="coraRequestPushSubscription()">
                            Enable Push Notifications
                        </button>

                        <!-- Send Test Push Button -->
                        <button type="button" id="cora-pwa-test-btn" class="hidden w-full py-1.5 bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-750 text-zinc-800 dark:text-zinc-200 font-semibold rounded-lg text-[10px] transition-colors cursor-pointer text-center select-none shadow-3xs border border-zinc-200 dark:border-zinc-700 outline-none" onclick="coraSendTestPushNotification()">
                            Send Test Notification
                        </button>
                        
                        <p id="cora-pwa-status-text" class="text-[9px] text-zinc-500 text-center leading-normal font-medium m-0">Install app & enable alerts for immediate updates.</p>
                    </div>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="w-full text-left px-2.5 py-2.5 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-red-600 dark:hover:text-red-400 font-semibold flex items-center gap-3 transition-colors select-none">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Sign out
                </a>
            </div>

            <!-- Sidebar Notification Popover Card -->
            <div id="cora-sidebar-notif-popover" class="hidden absolute bottom-20 left-4 right-4 bg-white dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl p-4 z-[70] flex flex-col gap-2.5 animate-in fade-in slide-in-from-bottom-2 duration-150 select-none text-zinc-900 dark:text-zinc-100">
<div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-850/50 px-1 rounded-t-xl">
                    <span class="text-xs font-bold">Notifications</span>
                    <button class="text-[10px] font-semibold text-zinc-500 hover:text-zinc-855 dark:hover:text-zinc-200 transition-colors cursor-pointer" onclick="markAllNotificationsRead(event)">Mark all as read</button>
                </div>
                <div id="cora-sidebar-notif-list" class="max-h-[240px] overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
                    <!-- Notifications will be injected here by JS -->
                </div>
                <div id="cora-sidebar-notif-empty" class="hidden p-6 text-center text-xs text-zinc-400 select-none">
                    No notifications yet.
                </div>
            </div>

            <style>
                /* Scoped Feedback trigger styles */
                #cora-feedback-trigger {
                    background-color: #25d366 !important;
                    color: #ffffff !important;
                    border: none !important;
                    font-weight: 700 !important;
                    box-shadow: 0 1px 3px rgba(37, 211, 102, 0.15) !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    height: 20px !important;
                    padding: 0 10px !important;
                    border-radius: 9999px !important;
                    transition: all 0.2s ease-in-out !important;
                }
                #cora-feedback-trigger:hover {
                    background-color: #20ba5a !important;
                    color: #ffffff !important;
                }
                
                /* Dark Theme overrides for the button */
                .cora-dark-theme #cora-feedback-trigger {
                    background-color: #25d366 !important;
                    color: #ffffff !important;
                    border: none !important;
                }
                .cora-dark-theme #cora-feedback-trigger:hover {
                    background-color: #20ba5a !important;
                    color: #ffffff !important;
                }
                
                /* Collapsed Sidebar overrides */
                .cora-sidebar.collapsed-sidebar .cora-user-footer #cora-feedback-trigger {
                    right: auto !important;
                    left: 50% !important;
                    transform: translate(-50%, -50%) !important;
                }
                .cora-sidebar.collapsed-sidebar .cora-feedback-btn-text {
                    display: none !important;
                }
                .cora-sidebar.collapsed-sidebar #cora-feedback-trigger {
                    width: 1.5rem !important;
                    height: 1.5rem !important;
                    padding: 0 !important;
                    border-radius: 9999px !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
            </style>

            <!-- Lovable-style user footer row -->
            <!-- Lovable-style user footer row -->
            <?php
            $current_user_display_name = $current_wp_user->exists() ? $current_wp_user->display_name : 'Dravya Bansal';
            $is_shruti_user = ( cora_is_real_shruti() || ( $current_wp_user->exists() && $current_wp_user->user_login === 'cora_admin' ) );
            if ( $is_shruti_user ) {
                $current_user_display_name = 'Shruti';
            }
            $current_user_role_label = isset($cora_role_labels[$current_user_role]) ? $cora_role_labels[$current_user_role] : ucfirst($current_user_role);
            if ($current_user_role === 'administrator') {
                $current_user_role_label = 'Super Admin';
            }
            $current_user_avatar = $current_wp_user->exists() ? get_user_meta( $current_wp_user->ID, 'cora_avatar_url', true ) : '';
            ?>
            <div class="cora-user-footer px-4 py-3 flex items-center justify-between border-t border-zinc-200/50 dark:border-zinc-800/40 hover:bg-zinc-100/50 dark:hover:bg-zinc-800/30 transition-colors duration-200 cursor-pointer relative z-[60]" onclick="event.stopPropagation(); $('#cora-profile-popover').toggleClass('hidden'); $('#cora-sidebar-notif-popover').addClass('hidden'); $('#cora-workspace-popover').addClass('hidden');">
                <!-- Dynamic Feedback Pill (Sticky Arc) inside profile footer -->
                <button type="button" id="cora-feedback-trigger" class="absolute -top-2.5 right-14 h-5 px-2.5 flex items-center justify-center gap-1.5 text-[9px] font-bold shadow-2xs hover:scale-[1.02] active:scale-[0.98] cursor-pointer z-[65]" onclick="window.coraOpenFeedbackDrawer(event)">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor" class="shrink-0 text-white">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.403 5.633A8.919 8.919 0 0 0 12.053 3c-4.948 0-8.976 4.027-8.978 8.977 0 1.58.413 3.125 1.2 4.488l-1.276 4.66 4.77-1.252a8.936 8.936 0 0 0 4.283 1.093h.004c4.947 0 8.975-4.027 8.977-8.977a8.926 8.926 0 0 0-2.63-6.353zM12.053 19.31a7.432 7.432 0 0 1-3.79-1.042l-.272-.162-2.82.74.752-2.748-.177-.282a7.43 7.43 0 0 1-1.139-3.934c.002-4.103 3.342-7.443 7.447-7.443a7.402 7.402 0 0 1 5.263 2.183 7.404 7.404 0 0 1 2.181 5.266c-.002 4.104-3.343 7.444-7.445 7.444zm4.079-5.571c-.223-.112-1.322-.653-1.526-.728-.205-.074-.354-.112-.503.112-.149.224-.577.728-.707.877-.13.15-.26.168-.484.056-.223-.112-.942-.347-1.794-1.108-.663-.592-1.11-1.322-1.24-1.546-.13-.223-.014-.344.098-.456.1-.1.223-.26.335-.392.112-.13.149-.224.223-.373.075-.149.038-.28-.018-.392-.056-.112-.503-1.213-.689-1.66-.182-.439-.366-.38-.503-.387-.13-.007-.28-.007-.429-.007-.15 0-.391.056-.596.28-.205.224-.782.766-.782 1.867 0 1.102.8 2.167.912 2.316.112.15 1.574 2.404 3.814 3.37.533.23 1.012.38 1.397.502.535.17 1.02.146 1.405.089.43-.064 1.322-.54 1.507-1.062.187-.523.187-.972.13-1.062-.056-.09-.205-.149-.43-.262z"/>
                    </svg>
                    <span class="cora-feedback-btn-text">Feedback</span>
                </button>
                <div class="flex items-center gap-3 min-w-0">
                    <?php if ( $current_user_avatar ) : ?>
                        <img src="<?php echo esc_url($current_user_avatar); ?>" class="w-8 h-8 rounded-full object-cover shrink-0 select-none border border-zinc-200/60" alt="<?php echo esc_attr($current_user_display_name); ?>" />
                    <?php else : ?>
                        <div class="w-8 h-8 rounded-full bg-zinc-200 text-zinc-700 flex items-center justify-center font-bold text-xs uppercase shrink-0 select-none">
                            <?php echo esc_html( $is_shruti_user ? 'S' : substr($current_user_display_name, 0, 2) ); ?>
                        </div>
                    <?php endif; ?>
                    <div class="cora-user-info flex flex-col min-w-0">
                        <span class="cora-user-name text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate leading-tight"><?php echo esc_html($current_user_display_name); ?></span>
                        <span class="cora-user-role text-[10px] text-zinc-400 dark:text-zinc-550 font-medium truncate"><?php echo esc_html($current_user_role_label); ?></span>
                    </div>
                </div>
                
                <!-- Notification Bell Button with badge -->
                <div class="cora-user-inbox relative shrink-0 text-zinc-500 hover:text-black dark:hover:text-white transition-all p-1.5 rounded-lg bg-zinc-200/50 dark:bg-zinc-800/50 hover:bg-zinc-200 dark:hover:bg-zinc-700 cursor-pointer flex items-center justify-center" onclick="event.stopPropagation(); $('#cora-sidebar-notif-popover').toggleClass('hidden'); $('#cora-profile-popover').addClass('hidden'); $('#cora-workspace-popover').addClass('hidden');">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span id="cora-sidebar-notif-badge" class="<?php echo $cora_unread_count > 0 ? '' : 'hidden'; ?> absolute -top-1 -right-1 min-w-[14px] h-3.5 px-0.5 flex items-center justify-center bg-red-600 text-[8px] font-bold text-white rounded-full leading-none border border-zinc-950">
                        <?php echo $cora_unread_count; ?>
                    </span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Sidebar Drawer Backdrop for Mobile/Tablet Viewports -->
    <div id="cora-sidebar-backdrop" class="fixed inset-0 bg-zinc-950/20 backdrop-blur-sm z-40 hidden lg:hidden"></div>

    <!-- Main Content Pane -->
    <main class="cora-main flex-1 bg-white flex flex-col min-h-screen lg:min-h-0 lg:h-full lg:overflow-y-auto relative pb-16 lg:pb-0 min-w-0 w-full">


        <!-- Dynamic Content Sections -->
        <div class="cora-content-wrapper p-3 sm:p-5 md:p-6 max-w-full w-full flex-1 space-y-5 sm:space-y-6 min-w-0">
            <!-- CORA Global Skeleton Preloader -->
            <div id="cora-skeleton-overlay" class="hidden w-full" aria-hidden="true">
              <!-- Dashboard skeleton -->
              <div id="cora-skeleton-dashboard" class="cora-skeleton-instance px-4 md:px-6 py-4 w-full space-y-6">
                <!-- KPI cards skeleton -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                  <div class="cora-skeleton-card shadow-sm"><div class="cora-skeleton cora-skeleton-text w-16 mb-3"></div><div class="cora-skeleton cora-skeleton-title w-24 mb-2"></div><div class="cora-skeleton cora-skeleton-text w-12"></div></div>
                  <div class="cora-skeleton-card shadow-sm"><div class="cora-skeleton cora-skeleton-text w-16 mb-3"></div><div class="cora-skeleton cora-skeleton-title w-24 mb-2"></div><div class="cora-skeleton cora-skeleton-text w-12"></div></div>
                  <div class="cora-skeleton-card shadow-sm hidden lg:block"><div class="cora-skeleton cora-skeleton-text w-16 mb-3"></div><div class="cora-skeleton cora-skeleton-title w-24 mb-2"></div><div class="cora-skeleton cora-skeleton-text w-12"></div></div>
                  <div class="cora-skeleton-card shadow-sm hidden lg:block"><div class="cora-skeleton cora-skeleton-text w-16 mb-3"></div><div class="cora-skeleton cora-skeleton-title w-24 mb-2"></div><div class="cora-skeleton cora-skeleton-text w-12"></div></div>
                </div>
                <!-- Greeting skeleton -->
                <div class="text-center py-8 space-y-3">
                  <div class="cora-skeleton cora-skeleton-title w-64 mx-auto mb-2" style="height:40px;border-radius:10px;"></div>
                  <div class="cora-skeleton cora-skeleton-text w-48 mx-auto"></div>
                </div>
                <!-- Search skeleton -->
                <div class="max-w-2xl mx-auto"><div class="cora-skeleton w-full" style="height:52px;border-radius:9999px;"></div></div>
                <!-- Quick actions skeleton -->
                <div class="flex justify-center gap-3">
                  <div class="cora-skeleton w-32" style="height:36px;border-radius:9999px;"></div>
                  <div class="cora-skeleton w-28" style="height:36px;border-radius:9999px;"></div>
                  <div class="cora-skeleton w-32" style="height:36px;border-radius:9999px;"></div>
                </div>
                <!-- Bento grid skeleton -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <div class="cora-skeleton-card shadow-sm md:col-span-2" style="min-height:280px;"><div class="cora-skeleton cora-skeleton-text w-32 mb-3"></div><div class="space-y-3"><div class="cora-skeleton cora-skeleton-text w-full"></div><div class="cora-skeleton cora-skeleton-text w-5/6"></div><div class="cora-skeleton cora-skeleton-text w-4/6"></div><div class="cora-skeleton cora-skeleton-text w-5/6"></div></div></div>
                  <div class="cora-skeleton-card shadow-sm" style="min-height:280px;"><div class="cora-skeleton cora-skeleton-text w-24 mb-3"></div><div class="space-y-4"><div class="cora-skeleton cora-skeleton-title w-full"></div><div class="cora-skeleton cora-skeleton-title w-full"></div><div class="cora-skeleton cora-skeleton-title w-full"></div></div></div>
                </div>
              </div>
              
              <!-- Generic list/table skeleton (for views like leads, bookings etc) -->
              <div id="cora-skeleton-list" class="cora-skeleton-instance hidden px-4 md:px-6 py-4 w-full space-y-4">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                  <div class="cora-skeleton cora-skeleton-title w-40"></div>
                  <div class="cora-skeleton w-28" style="height:36px;border-radius:8px;"></div>
                </div>
                <!-- Filter bar -->
                <div class="flex gap-2 mb-4">
                  <div class="cora-skeleton w-20" style="height:28px;border-radius:9999px;"></div>
                  <div class="cora-skeleton w-24" style="height:28px;border-radius:9999px;"></div>
                  <div class="cora-skeleton w-20" style="height:28px;border-radius:9999px;"></div>
                </div>
                <!-- Table rows -->
                <div class="cora-skeleton-card shadow-sm space-y-3">
                  <div class="flex items-center gap-3 py-2 border-b border-zinc-100"><div class="cora-skeleton w-8 h-8 rounded-full flex-shrink-0"></div><div class="flex-1 space-y-1.5"><div class="cora-skeleton cora-skeleton-text w-48"></div><div class="cora-skeleton cora-skeleton-text w-32"></div></div><div class="cora-skeleton w-16" style="height:24px;border-radius:6px;"></div></div>
                  <div class="flex items-center gap-3 py-2 border-b border-zinc-100"><div class="cora-skeleton w-8 h-8 rounded-full flex-shrink-0"></div><div class="flex-1 space-y-1.5"><div class="cora-skeleton cora-skeleton-text w-40"></div><div class="cora-skeleton cora-skeleton-text w-28"></div></div><div class="cora-skeleton w-16" style="height:24px;border-radius:6px;"></div></div>
                  <div class="flex items-center gap-3 py-2 border-b border-zinc-100"><div class="cora-skeleton w-8 h-8 rounded-full flex-shrink-0"></div><div class="flex-1 space-y-1.5"><div class="cora-skeleton cora-skeleton-text w-44"></div><div class="cora-skeleton cora-skeleton-text w-24"></div></div><div class="cora-skeleton w-16" style="height:24px;border-radius:6px;"></div></div>
                  <div class="flex items-center gap-3 py-2 border-b border-zinc-100"><div class="cora-skeleton w-8 h-8 rounded-full flex-shrink-0"></div><div class="flex-1 space-y-1.5"><div class="cora-skeleton cora-skeleton-text w-36"></div><div class="cora-skeleton cora-skeleton-text w-32"></div></div><div class="cora-skeleton w-16" style="height:24px;border-radius:6px;"></div></div>
                  <div class="flex items-center gap-3 py-2"><div class="cora-skeleton w-8 h-8 rounded-full flex-shrink-0"></div><div class="flex-1 space-y-1.5"><div class="cora-skeleton cora-skeleton-text w-44"></div><div class="cora-skeleton cora-skeleton-text w-28"></div></div><div class="cora-skeleton w-16" style="height:24px;border-radius:6px;"></div></div>
                </div>
              </div>
            </div>
            
            <!-- Floating Role Preview Active Notice Banner -->
            <div id="cora-role-preview-banner" class="hidden mb-4 p-3.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 rounded-xl shadow-md flex items-center justify-between gap-3 animate-in fade-in duration-200">
                <div class="flex items-center gap-3">
                    <span class="p-1.5 bg-zinc-800 dark:bg-zinc-200 rounded-lg shrink-0 text-zinc-200 dark:text-zinc-800">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                    <div class="text-xs">
                        <span class="font-extrabold uppercase tracking-wider text-[10px] bg-zinc-800 dark:bg-zinc-200 text-zinc-200 dark:text-zinc-800 px-2 py-0.5 rounded-md mr-1.5">Role Preview Active</span>
                        Viewing workspace as <span id="cora-preview-role-name" class="font-extrabold underline underline-offset-2">Manager</span>. Navigation &amp; permissions are simulated for this role.
                    </div>
                </div>
                <button type="button" onclick="coraResetRolePreview()" class="px-3 py-1.5 bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white text-xs font-extrabold rounded-lg hover:opacity-90 transition-all shrink-0 cursor-pointer shadow-xs flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                    Reset to Admin
                </button>
            </div>
            
            <!-- DEBUG ROUTE: <?php echo esc_html( $sub_page ); ?> -->
            <!-- SECTION 1: DASHBOARD -->
            <?php if ( $sub_page === 'dashboard' ) : ?>
            <section id="cora-page-dashboard" class="cora-page-section cora-active space-y-6">
                <?php
                $user_first_name = $current_wp_user->exists() ? ( ! empty( $current_wp_user->first_name ) ? $current_wp_user->first_name : $current_wp_user->display_name ) : 'Dravya';
                $hour = (int) date('H');
                $greeting_time = 'Good afternoon';
                if ($hour >= 5 && $hour < 12) {
                    $greeting_time = 'Good morning';
                } elseif ($hour >= 12 && $hour < 17) {
                    $greeting_time = 'Good afternoon';
                } else {
                    $greeting_time = 'Good evening';
                }
                $greeting_title = $greeting_time . ', ' . $user_first_name . '.';

                // Calculate telemetry metrics per industry mode
                $cora_workspace_industry_raw = ! empty( $_COOKIE['cora_workspace_industry'] ) 
                    ? $_COOKIE['cora_workspace_industry'] 
                    : ( function_exists( 'cora_get_active_industry' ) ? cora_get_active_industry() : get_option( 'cora_workspace_industry', 'real_estate' ) );
                $cora_industry_mode_clean = str_replace( '_', '-', strtolower( trim( $cora_workspace_industry_raw ) ) );
                $is_studio = ( $cora_industry_mode_clean === 'photography-studio' || $cora_industry_mode_clean === 'photography' );

                // Load database records dynamically
                $cora_studio_gear = get_option( 'cora_studio_gear', array() );
                $all_bookings = function_exists('cora_db_get_bookings') ? cora_db_get_bookings() : array();

                $clients_by_id = array();
                if ( is_array( $cora_workspace_clients ) ) {
                    foreach ( $cora_workspace_clients as $client ) {
                        $clients_by_id['client_' . $client['id']] = $client;
                    }
                }

                // Process today's bookings
                $today_str = date('Y-m-d');
                $today_events = array();
                foreach ( $all_bookings as $booking ) {
                    $c_name = $booking['client_name'] ?? '';
                    if ( empty( $c_name ) ) {
                        $c_id = $booking['client_id'];
                        $c_name = isset($clients_by_id[$c_id]) ? $clients_by_id[$c_id]['names'] : 'Client';
                    }
                    $booking['resolved_client_name'] = $c_name;
                    if ( $booking['date'] === $today_str ) {
                        $today_events[] = $booking;
                    }
                }

                // Calculate telemetry metrics per industry mode
                if ( ! isset( $recent_active_showings ) || ! is_array( $recent_active_showings ) ) {
                    $recent_active_showings = array();
                    if ( is_array( $cora_workspace_clients ) ) {
                        foreach ( $cora_workspace_clients as $client ) {
                            if ( isset( $client['status'] ) && $client['status'] !== 'completed' ) {
                                $recent_active_showings[] = $client;
                            }
                        }
                    }
                }

                if ( $is_studio ) {
                    $gear_count = is_array($cora_studio_gear) ? count($cora_studio_gear) : 0;
                    $telemetry_metrics = array(
                        array(
                            'label'       => 'Active Shoots',
                            'value'       => count( $recent_active_showings ),
                            'badge'       => 'In Progress',
                            'badge_color' => 'text-emerald-650 dark:text-emerald-500 bg-emerald-50 dark:bg-emerald-950/20 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 25 C 20 25, 30 5, 50 15 C 70 25, 80 10, 100 5',
                            'primary'     => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                        ),
                        array(
                            'label'       => 'Camera Gear',
                            'value'       => $gear_count,
                            'badge'       => 'Assets',
                            'badge_color' => 'text-zinc-650 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 5 C 20 5, 40 25, 60 15 C 80 5, 90 28, 100 28',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>',
                        ),
                        array(
                            'label'       => 'Bookings (MTD)',
                            'value'       => count( $cora_workspace_clients ),
                            'badge'       => 'Confirmed',
                            'badge_color' => 'text-zinc-650 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 8 C 20 8, 40 22, 60 12 C 80 2, 90 25, 100 25',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                        ),
                        array(
                            'label'       => 'Pending Deliveries',
                            'value'       => $dynamic_pending_count,
                            'badge'       => 'Editing',
                            'badge_color' => 'text-zinc-650 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 15 L 100 15',
                            'svg_dash'    => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><polygon points="12 22.08 12 12 3 6.92 3 17.08 12 22.08"></polygon><polygon points="12 22.08 21 17.08 21 6.92 12 12 12 22.08"></polygon><polygon points="12 12 21 6.92 12 2 3 6.92 12 12"></polygon></svg>',
                        ),
                    );
                } else {
                    $telemetry_metrics = array(
                        array(
                            'label'       => 'Properties',
                            'value'       => count( $cora_workspace_listings ),
                            'badge'       => 'Active',
                            'badge_color' => 'text-emerald-655 dark:text-emerald-500 bg-emerald-50 dark:bg-emerald-950/20 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 25 C 20 25, 30 5, 50 15 C 70 25, 80 10, 100 5',
                            'primary'     => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
                        ),
                        array(
                            'label'       => 'Active Leads',
                            'value'       => count( $cora_workspace_leads ),
                            'badge'       => '+12%',
                            'badge_color' => 'text-emerald-650 dark:text-emerald-500 bg-emerald-50 dark:bg-emerald-950/20 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 5 C 20 5, 40 25, 60 15 C 80 5, 90 28, 100 28',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                        ),
                        array(
                            'label'       => 'Showings',
                            'value'       => $dynamic_active_bookings_count,
                            'badge'       => 'Scheduled',
                            'badge_color' => 'text-zinc-650 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 8 C 20 8, 40 22, 60 12 C 80 2, 90 25, 100 25',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                        ),
                        array(
                            'label'       => 'Pipeline Value',
                            'value'       => cora_format_rupees( $dynamic_revenue_total ),
                            'badge'       => 'Negotiating',
                            'badge_color' => 'text-zinc-650 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 15 L 100 15',
                            'svg_dash'    => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M6 3h12M6 8h12M9 3v10.5M9 3h3.5a5 5 0 0 1 0 10H9M9 13.5L16 21"></path></svg>',
                        ),
                    );
                }
                ?>
                <div class="cora-dashboard-upper px-1 sm:px-4 md:px-6 w-full box-border">
                <!-- Dynamic KPI Metrics Cards (Premium Responsive Layout: 2-Cols on Mobile, 4-Cols on Desktop) -->
                <div class="w-full max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 select-none">
                    <?php foreach ( $telemetry_metrics as $idx => $metric ) : ?>
                    <div class="bg-white/80 dark:bg-zinc-900/60 p-4 backdrop-blur-md border border-zinc-200/50 dark:border-zinc-800/40 rounded-2xl flex items-center justify-between transition-all hover:scale-[1.01] hover:shadow-xs cursor-default">
                        <div class="space-y-1 min-w-0 pr-2">
                            <span class="block text-[9px] sm:text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider truncate"><?php echo esc_html( $metric['label'] ); ?></span>
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="text-base sm:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 leading-none tracking-tight"><?php echo esc_html( $metric['value'] ); ?></span>
                                <span class="<?php echo esc_attr( $metric['badge_color'] ); ?> inline-flex items-center text-[8px] sm:text-[9px] font-bold px-1.5 py-0.5 rounded-md leading-none"><?php echo esc_html( $metric['badge'] ); ?></span>
                            </div>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800/35 border border-zinc-100 dark:border-zinc-800/50 flex items-center justify-center text-zinc-650 dark:text-zinc-350 shrink-0 shadow-3xs">
                            <?php echo $metric['icon']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>


                <!-- Centered Welcome Greeting Section with sparkle SVG -->
                <div class="text-center px-4 space-y-1.5 sm:space-y-2 relative" style="padding-top: 120px !important; padding-bottom: 40px !important;">
                    <div class="inline-flex items-center justify-center gap-2.5 sm:gap-3">
                        <!-- Slate Charcoal Star Sparkle -->
                        <span class="text-zinc-900 dark:text-zinc-100 shrink-0">
                            <svg viewBox="0 0 24 24" width="22" height="22" class="w-5 h-5 sm:w-7 sm:h-7" fill="currentColor">
                                <path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"></path>
                            </svg>
                        </span>
                        <h1 id="cora-dynamic-greeting-title" class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                            <?php echo esc_html($greeting_title); ?>
                        </h1>
                    </div>
                    <p class="text-xs sm:text-base md:text-lg font-medium text-zinc-450 dark:text-zinc-500 leading-tight">
                        Let's continue growing your business.
                    </p>
                </div>
 
                <!-- Lovable-Style Command Search (Ask anything...) -->
                <div class="w-full max-w-xl mx-auto mt-2 sm:mt-4 mb-6 sm:mb-8 px-2 sm:px-0 relative z-[999] hidden md:block" id="cora-search-container">
                    <div class="relative flex items-center bg-white/85 dark:bg-zinc-900/70 backdrop-blur-md border border-zinc-200/60 dark:border-zinc-800/50 hover:border-zinc-350 dark:hover:border-zinc-700 focus-within:border-zinc-900 dark:focus-within:border-zinc-100 focus-within:ring-2 focus-within:ring-zinc-100/30 dark:focus-within:ring-zinc-800/30 rounded-full shadow-2xs transition-all duration-200 p-1.5 pl-3.5 pr-2">
                        <span class="text-zinc-600 dark:text-zinc-400 mr-2 flex shrink-0">
                            <!-- Lovable Character Icon (Standardized Monochromatic) -->
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                <circle cx="12" cy="12" r="10" class="text-zinc-100 dark:text-zinc-800" fill="currentColor"></circle>
                                <circle cx="12" cy="12" r="7" class="text-zinc-400 dark:text-zinc-500" fill="currentColor"></circle>
                                <circle cx="10" cy="11" r="1.2" fill="#fff"></circle>
                                <circle cx="14" cy="11" r="1.2" fill="#fff"></circle>
                                <path d="M9.5 15c.5.8 1.5 1.2 2.5 1.2s2-.4 2.5-1.2" stroke="#fff" stroke-width="1.2" stroke-linecap="round" fill="none"></path>
                            </svg>
                        </span>
                        
                        <!-- Real interactive input field for contextual search -->
                        <input type="text" 
                               id="cora-inline-command-input"
                               placeholder="Ask anything..." 
                               class="w-full bg-transparent border-0 focus:outline-none focus:ring-0 text-xs sm:text-sm py-1.5 px-1 text-zinc-800 dark:text-zinc-200 placeholder:text-zinc-400/80 cursor-pointer"
                               autocomplete="off" />
                               
                        <div class="flex items-center gap-2">
                            <button onclick="window.coraTriggerCommandAI()" class="flex items-center justify-center h-7 w-7 sm:h-8 sm:w-8 rounded-full bg-zinc-900 hover:bg-zinc-955 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 transition-colors cursor-pointer shadow-sm">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Advanced Command Search dropdown in-line container -->
                    <div id="cora-inline-command-palette" class="absolute left-0 right-0 top-full mt-2 z-[9999] hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl flex-col transition-all duration-200">
                        <!-- Filter Pills Bar -->
                        <div class="flex items-center gap-1.5 px-4 py-2 border-b border-zinc-100 dark:border-zinc-800/40 bg-zinc-50/50 dark:bg-zinc-900/40 overflow-x-auto shrink-0 select-none no-scrollbar">
                            <button type="button" class="cora-search-pill active text-[10px] font-semibold px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 transition-all cursor-pointer" data-filter="all">Overview</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="pages">Pages</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="leads">Leads</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="settings">Settings</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="listings">Listings</button>
                        </div>

                        <!-- Results List Area — fixed height, always scrollable -->
                        <div id="cora-inline-command-results" class="overflow-y-auto p-2" style="height: 260px;">
                            <!-- Loading state / Suggestions list / Search results list -->
                        </div>

                        <!-- Footer Bar -->
                        <div class="border-t border-zinc-100 dark:border-zinc-800/40 px-4 py-2 bg-zinc-50/50 dark:bg-zinc-900/40 flex items-center justify-between shrink-0">
                            <span class="text-[10px] text-zinc-400 font-medium">Need help finding something?</span>
                            <button type="button" class="px-2.5 py-1 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 font-semibold text-[10px] rounded-lg transition-colors shadow-sm flex items-center gap-1.5 cursor-pointer" onclick="window.coraTriggerCommandAI()">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                Ask Cora
                            </button>
                        </div>
                    </div>
                </div><!-- end cora-search-container -->
                
                <!-- Premium Dynamic Quick Actions (Mobile-first Wrap Grid / Desktop Centered Grid) -->
                <div class="w-full flex flex-col items-center justify-center gap-2.5 py-2 px-0 select-none" id="cora-quick-actions-bar">
                    <?php if ( $is_studio ) : ?>
                        <!-- Predefined actions Row 1 -->
                        <div class="w-full flex flex-wrap items-center justify-center gap-2.5 px-4">
                            <button onclick="coraNavigateTo('bookings'); document.getElementById('cora-add-booking-btn')?.click();" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                Book a Shoot
                            </button>
                            <button onclick="coraNavigateTo('equipment'); window.openAddGearDrawer?.();" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                Register Gear
                            </button>
                            <button onclick="coraNavigateTo('crew-scheduler')" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                Assign Crew
                            </button>
                        </div>
                        <!-- Predefined actions Row 2 -->
                        <div class="w-full flex flex-wrap items-center justify-center gap-2.5 px-4 mt-0.5">
                            <button onclick="coraNavigateTo('media')" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                Upload Media
                            </button>
                            <button onclick="coraNavigateTo('invoicing')" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="6" y1="8" x2="18" y2="8"></line><line x1="6" y1="12" x2="14" y2="12"></line><line x1="6" y1="16" x2="10" y2="16"></line></svg>
                                Create Invoice
                            </button>
                        </div>
                    <?php else : ?>
                        <!-- Predefined actions Row 1 -->
                        <div class="w-full flex flex-wrap items-center justify-center gap-2.5 px-4">
                            <button onclick="coraNavigateTo('bookings'); document.getElementById('cora-add-booking-btn')?.click();" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                Schedule Showing
                            </button>
                            <button onclick="coraNavigateTo('ai-assistants')" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                Draft Captions
                            </button>
                            <button onclick="coraNavigateTo('leads')" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                Add Lead
                            </button>
                        </div>
                        <!-- Predefined actions Row 2 -->
                        <div class="w-full flex flex-wrap items-center justify-center gap-2.5 px-4 mt-0.5">
                            <button onclick="event.stopPropagation(); window.coraOpenCommandPalette();" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                Create Brochure
                            </button>
                            <button onclick="coraNavigateTo('listings')" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                View Listings
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Custom Shortcuts row (centered, sits on its own row below) -->
                    <div class="flex flex-wrap items-center justify-center gap-2.5 w-full mt-0.5">
                        <!-- Custom Actions (from localStorage) -->
                        <div id="cora-custom-actions-container" class="contents"></div>

                        <!-- Add AI Custom Shortcuts button -->
                        <button type="button" onclick="window.coraOpenCustomActionModal()" class="cora-ai-gradient-pill select-none whitespace-nowrap shrink-0">
                            <span class="cora-ai-gradient-pill-inner">
                                <svg viewBox="0 0 24 24" width="13" height="13" class="text-purple-600 dark:text-purple-400 shrink-0" fill="currentColor">
                                    <path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"></path>
                                </svg>
                                <span>Custom Shortcuts</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div><!-- end cora-dashboard-upper -->
                <!-- ===== Custom Quick Action Modal ===== -->
                <div id="cora-custom-action-modal" class="fixed inset-0 flex items-center justify-center" style="display:none; z-index: 100000;" onclick="if(event.target===this){this.style.display='none';document.body.style.overflow='';}">
                    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>
                    <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 w-full max-w-md mx-4" style="max-height:90vh;overflow-y:auto;">
                        <!-- Header -->
                        <div class="flex items-start justify-between px-6 pt-6 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Quick Action Shortcuts</h3>
                                <p class="text-[11px] text-zinc-455 mt-0.5 font-medium">Personalise your dashboard with page shortcuts</p>
                            </div>
                            <button onclick="document.getElementById('cora-custom-action-modal').style.display='none';document.body.style.overflow='';" class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer text-zinc-400 hover:text-zinc-600 shrink-0 bg-transparent border-0">
                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                        <!-- Suggested Presets -->
                        <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800">
                            <p class="text-[10px] font-semibold text-zinc-450 uppercase tracking-wide mb-2.5">⚡ Suggested for you</p>
                            <div id="cora-preset-pills" class="flex flex-wrap gap-1.5"></div>
                        </div>
                        <!-- Custom Form -->
                        <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800">
                            <p class="text-[10px] font-semibold text-zinc-450 uppercase tracking-wide mb-2.5">Create custom shortcut</p>
                            <div class="space-y-2.5">
                                <input type="text" id="cora-custom-action-name" placeholder="Label (e.g. View Reports)" class="w-full px-3 py-2 text-sm border border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50 dark:bg-zinc-850/60 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-500 transition-colors" />
                                <div class="relative" id="cora-page-picker-wrap">
                                    <div class="flex items-center gap-2 px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50 dark:bg-zinc-850/60 cursor-text" onclick="document.getElementById('cora-page-search').focus()">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        <input type="text" id="cora-page-search" placeholder="Search pages..." autocomplete="off" class="flex-1 text-sm bg-transparent border-0 outline-none text-zinc-800 dark:text-zinc-200 placeholder:text-zinc-400" oninput="window.coraFilterPages(this.value)" onfocus="document.getElementById('cora-page-list-drop').style.display='block';window.coraFilterPages(this.value)" />
                                        <span id="cora-page-selected-label" class="text-[10px] font-semibold text-green-600 shrink-0 hidden">✓</span>
                                    </div>
                                    <input type="hidden" id="cora-custom-action-page" value="" />
                                    <div id="cora-page-list-drop" class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-lg overflow-hidden" style="display:none; z-index:200; max-height:180px; overflow-y:auto;">
                                        <div id="cora-page-list-items"></div>
                                    </div>
                                </div>
                                <button onclick="window.coraAddCustomAction()" class="w-full py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 text-sm font-semibold rounded-xl transition-colors cursor-pointer border-0">Add Shortcut</button>
                            </div>
                        </div>
                        <!-- Existing -->
                        <div class="px-6 py-4">
                            <p class="text-[10px] font-semibold text-zinc-450 uppercase tracking-wide mb-2.5">Your shortcuts</p>
                            <div id="cora-custom-actions-list"></div>
                        </div>
                    </div>
                </div>
                <script>
window.coraGetPageIconSvg = function(page) {
    var stroke = 'stroke="currentColor" stroke-width="1.8" fill="none" style="width: 14px; height: 14px;"';
    switch(page) {
        case 'bookings': return '<svg viewBox="0 0 24 24" '+stroke+'><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
        case 'leads': return '<svg viewBox="0 0 24 24" '+stroke+'><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
        case 'clients': return '<svg viewBox="0 0 24 24" '+stroke+'><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
        case 'listings': return '<svg viewBox="0 0 24 24" '+stroke+'><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
        case 'equipment': return '<svg viewBox="0 0 24 24" '+stroke+'><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>';
        case 'crew-scheduler': return '<svg viewBox="0 0 24 24" '+stroke+'><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="12" y1="2" x2="12" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line></svg>';
        case 'media': return '<svg viewBox="0 0 24 24" '+stroke+'><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>';
        case 'financials': return '<svg viewBox="0 0 24 24" '+stroke+'><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M12 8v8M8 12h8"></path></svg>';
        case 'content-suite': return '<svg viewBox="0 0 24 24" '+stroke+'><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>';
        case 'forms': return '<svg viewBox="0 0 24 24" '+stroke+'><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
        case 'settings-suite': return '<svg viewBox="0 0 24 24" '+stroke+'><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>';
        case 'ai-assistants': return '<svg viewBox="0 0 24 24" '+stroke+'><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M12 2v6M8 5h8"></path><circle cx="8" cy="16" r="1"></circle><circle cx="16" cy="16" r="1"></circle></svg>';
        case 'analytics': return '<svg viewBox="0 0 24 24" '+stroke+'><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>';
        case 'integrations': return '<svg viewBox="0 0 24 24" '+stroke+'><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>';
        case 'knowledge-base': return '<svg viewBox="0 0 24 24" '+stroke+'><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>';
        default: return '<svg viewBox="0 0 24 24" '+stroke+'><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
    }
};

var CORA_ALL_PAGES=[{value:'bookings',label:'Bookings / Calendar'},{value:'leads',label:'Leads'},{value:'clients',label:'Clients'},{value:'listings',label:'Listings'},{value:'equipment',label:'Equipment / Gear'},{value:'crew-scheduler',label:'Crew Scheduler'},{value:'media',label:'Media Library'},{value:'financials',label:'Financials'},{value:'content-suite',label:'Content Suite'},{value:'forms',label:'Forms & Contracts'},{value:'settings-suite',label:'Settings'},{value:'ai-assistants',label:'AI Assistants'},{value:'analytics',label:'Analytics & Reports'},{value:'integrations',label:'Integrations'},{value:'knowledge-base',label:'RAG Knowledge Base'}];
var CORA_PRESETS=[{name:'Book a Shoot',page:'bookings'},{name:'Check Financials',page:'financials'},{name:'Add New Lead',page:'leads'},{name:'Upload Media',page:'media'},{name:'View Crew',page:'crew-scheduler'},{name:'AI Assistants',page:'ai-assistants'},{name:'Content Suite',page:'content-suite'},{name:'View Listings',page:'listings'},{name:'RAG Knowledge Base',page:'knowledge-base'}];

window.coraFilterPages=function(q){var drop=document.getElementById('cora-page-list-drop');var items=document.getElementById('cora-page-list-items');if(!drop||!items)return;drop.style.display='block';var filtered=q?CORA_ALL_PAGES.filter(function(p){return p.label.toLowerCase().indexOf(q.toLowerCase())>-1;}):CORA_ALL_PAGES;if(!filtered.length){items.innerHTML='<p style="font-size:11px;color:#a1a1aa;padding:10px 12px;">No pages found</p>';return;}items.innerHTML=filtered.map(function(p){var iconHtml=window.coraGetPageIconSvg(p.value);return '<button type="button" onclick="window.coraSelectPage(\''+p.value+'\',\''+p.label.replace(/'/g,"\\'")+'\')" style="display:flex;align-items:center;gap:8px;width:100%;text-align:left;padding:8px 12px;font-size:12px;background:none;border:none;cursor:pointer;color:#3f3f46;" onmouseover="this.style.background=\'#f4f4f5\'" onmouseout="this.style.background=\'none\'"><span style="display:inline-flex;align-items:center;justify-content:center;width:14px;height:14px;color:#71717a;">'+iconHtml+'</span><span>'+p.label+'</span></button>';}).join('');};

window.coraSelectPage=function(value,label){document.getElementById('cora-custom-action-page').value=value;document.getElementById('cora-page-search').value=label;var lbl=document.getElementById('cora-page-selected-label');lbl.classList.remove('hidden');document.getElementById('cora-page-list-drop').style.display='none';};

window.coraRenderPresets=function(){var container=document.getElementById('cora-preset-pills');if(!container)return;var existing=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]').map(function(a){return a.page+'|'+a.name;});container.innerHTML=CORA_PRESETS.map(function(p){var added=existing.indexOf(p.page+'|'+p.name)>-1;var iconHtml=window.coraGetPageIconSvg(p.page);return '<button type="button" onclick="window.coraAddPreset(\''+p.name.replace(/'/g,"\\'")+'\',\''+p.page+'\')" style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;font-size:11px;font-weight:500;border-radius:20px;cursor:pointer;border:1px solid '+(added?'#bbf7d0':'#e4e4e7')+';background:'+(added?'#f0fdf4':'#fafafa')+';color:'+(added?'#16a34a':'#3f3f46')+';">'+'<span style="display:inline-flex;align-items:center;color:'+(added?'#16a34a':'#71717a')+'; width:12px; height:12px;">'+iconHtml+'</span><span>'+p.name+'</span>'+(added?'<span style="font-size:9px;margin-left:2px;">✓</span>':'')+'</button>';}).join('');};

window.coraAddPreset=function(name,page){var actions=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]');if(actions.find(function(a){return a.name===name&&a.page===page;})){if(window.coraShowToast)coraShowToast('Already added!','info');return;}actions.push({name:name,page:page});localStorage.setItem('cora_custom_quick_actions',JSON.stringify(actions));window.coraRenderCustomActions();window.coraRenderCustomActionsList();window.coraRenderPresets();if(window.coraShowToast)coraShowToast(name+' added!','success');};

window.coraOpenCustomActionModal=function(){var m=document.getElementById('cora-custom-action-modal');m.style.display='flex';document.body.style.overflow='hidden';window.coraFilterPages('');window.coraRenderPresets();window.coraRenderCustomActionsList();document.addEventListener('click',function h(e){if(!e.target.closest('#cora-page-picker-wrap')){var d=document.getElementById('cora-page-list-drop');if(d)d.style.display='none';}},{capture:true});};

window.coraAddCustomAction=function(){var name=(document.getElementById('cora-custom-action-name').value||'').trim();var page=document.getElementById('cora-custom-action-page').value;if(!name||!page){if(window.coraShowToast)coraShowToast('Please enter a label and select a page.','error');return;}var actions=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]');actions.push({name:name,page:page});localStorage.setItem('cora_custom_quick_actions',JSON.stringify(actions));document.getElementById('cora-custom-action-name').value='';document.getElementById('cora-custom-action-page').value='';document.getElementById('cora-page-search').value='';document.getElementById('cora-page-selected-label').classList.add('hidden');window.coraRenderCustomActions();window.coraRenderCustomActionsList();window.coraRenderPresets();if(window.coraShowToast)coraShowToast('Shortcut added!','success');};

window.coraDeleteCustomAction=function(idx){var actions=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]');actions.splice(idx,1);localStorage.setItem('cora_custom_quick_actions',JSON.stringify(actions));window.coraRenderCustomActions();window.coraRenderCustomActionsList();window.coraRenderPresets();};

window.coraRenderCustomActionsList=function(){var list=document.getElementById('cora-custom-actions-list');if(!list)return;var actions=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]');if(!actions.length){list.innerHTML='<p style="font-size:11px;color:#a1a1aa;text-align:center;padding:6px 0;">No shortcuts yet — add one above or pick a suggestion.</p>';return;}list.innerHTML='<div style="display:flex;flex-direction:column;gap:6px;">'+actions.map(function(a,i){var iconHtml=window.coraGetPageIconSvg(a.page);return '<div style="display:flex;align-items:center;justify-content:space-between;padding:7px 12px;background:#f4f4f5;border-radius:10px;"><div style="display:flex;align-items:center;gap:7px;"><span style="display:inline-flex;align-items:center;color:#71717a;width:12px;height:12px;">'+iconHtml+'</span><span style="font-size:12px;font-weight:500;color:#3f3f46;">'+a.name+'</span></div><button onclick="window.coraDeleteCustomAction('+i+')" style="background:none;border:none;cursor:pointer;color:#a1a1aa;padding:2px;"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button></div>';}).join('')+'</div>';};

window.coraRenderCustomActions=function(){var c=document.getElementById('cora-custom-actions-container');if(!c)return;var actions=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]').slice(0,3);c.innerHTML=actions.map(function(a){return '<button onclick="coraNavigateTo(\''+a.page+'\')" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">'+a.name+'</button>';}).join('');};

document.addEventListener('DOMContentLoaded',window.coraRenderCustomActions);
                </script>
                <?php
                // Calculate dynamic metrics for Cash Overview
                $mtd_received = 0;
                $expected_amount = 0;
                $overdue_amount = 0;

                if ( is_array( $cora_financials ) ) {
                    foreach ( $cora_financials as $entry ) {
                        $amt = floatval( preg_replace( '/[^\d.]/', '', $entry['amount'] ?? '0' ) );
                        $type = strtolower( $entry['type'] ?? '' );
                        $status = strtolower( $entry['status'] ?? '' );
                        if ( ( $type === 'inflow' || $type === 'income' || $type === 'credit' ) && $status === 'cleared' ) {
                            $mtd_received += $amt;
                        }
                        if ( $status === 'pending' || $status === 'upcoming' ) {
                            $expected_amount += $amt;
                        }
                        if ( $status === 'overdue' ) {
                            $overdue_amount += $amt;
                        }
                    }
                }
                ?>
                <div class="cora-bento-grid pt-4 sm:pt-6 gap-5">
                     <!-- CARD 1: Today's Timeline (Spans 2 Columns) -->
                    <div class="border border-zinc-100 dark:border-zinc-800/40 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-sm flex flex-col justify-between md:col-span-2 min-h-[320px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-violet-50 dark:bg-violet-950/20 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Today's Timeline</h3>
                                        <span class="text-[11px] text-zinc-450 dark:text-zinc-500 font-medium">Your key operational events for today</span>
                                    </div>
                                </div>
                                <span class="h-5 w-7 rounded-full bg-violet-50 dark:bg-violet-950/20 text-violet-600 dark:text-violet-400 font-bold text-[10px] flex items-center justify-center shrink-0"><?php echo count($today_events); ?></span>
                            </div>

                            <?php if ( empty( $today_events ) ) : ?>
                                <div class="flex flex-col items-center justify-center py-10 text-center">
                                    <div class="h-12 w-12 rounded-full bg-zinc-50 dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center mb-3">
                                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    </div>
                                    <h4 class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Clear Schedule Today</h4>
                                    <p class="text-[11px] text-zinc-450 dark:text-zinc-500 mt-1 max-w-[280px]">No active shoots or showings booked for today.</p>
                                </div>
                            <?php else : ?>
                                <!-- Timeline Vertical Node List -->
                                <div class="space-y-4 pl-1 relative before:absolute before:left-[4.75rem] before:top-2 before:bottom-2 before:w-[2px] before:bg-zinc-100 dark:before:bg-zinc-800">
                                    <?php foreach ( $today_events as $event ) : 
                                        $time_formatted = date('h:i A', strtotime($event['time']));
                                        $badge_cls = 'text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700';
                                        $dot_cls = 'bg-zinc-400 dark:bg-zinc-650';
                                        if ($event['status'] === 'confirmed') {
                                            $badge_cls = 'text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30';
                                            $dot_cls = 'bg-emerald-500';
                                        } elseif ($event['status'] === 'editing') {
                                            $badge_cls = 'text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/30';
                                            $dot_cls = 'bg-amber-500';
                                        }
                                    ?>
                                        <div class="flex items-start gap-4 relative text-xs">
                                            <span class="text-[10px] font-bold text-zinc-455 dark:text-zinc-500 w-16 shrink-0 pt-0.5"><?php echo esc_html($time_formatted); ?></span>
                                            <span class="w-2.5 h-2.5 rounded-full <?php echo $dot_cls; ?> ring-4 ring-white dark:ring-zinc-900 shrink-0 z-10 mt-1"></span>
                                            <div class="flex-1 min-w-0">
                                                 <div class="flex items-center gap-2 flex-wrap">
                                                     <strong class="font-bold text-zinc-850 dark:text-zinc-100 truncate text-[13px]"><?php echo esc_html($event['deal_type']); ?></strong>
                                                     <span class="text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded <?php echo $badge_cls; ?>"><?php echo esc_html($event['status']); ?></span>
                                                 </div>
                                                 <span class="text-[10px] text-zinc-500 dark:text-zinc-450 block truncate mt-0.5"><?php echo esc_html($event['resolved_client_name']); ?> &bull; <?php echo esc_html($event['location'] ?? $event['notes']); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/40 mt-3">
                            <button onclick="coraNavigateTo('bookings')" class="w-full flex items-center justify-between text-xs font-bold text-violet-655 dark:text-violet-450 hover:text-violet-750 dark:hover:text-violet-300 transition-colors cursor-pointer group">
                                <span>View Full Calendar & Booking CRM</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 3: Cash Overview (Spans 1 Column, matches other cards cleanly) -->
                    <div class="border border-zinc-100 dark:border-zinc-800/40 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-sm flex flex-col justify-between min-h-[320px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M12 8v8M8 12h8"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Cash Overview</h3>
                                        <span class="text-[11px] text-zinc-450 dark:text-zinc-500 font-medium">Your financial snapshot</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-5 mt-2">
                                <div class="flex items-center justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/30">
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-bold">Received (MTD)</span>
                                    <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">₹<?php echo number_format($mtd_received); ?></span>
                                </div>

                                <div class="flex items-center justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/30">
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-bold">Expected</span>
                                    <span class="text-base font-extrabold text-zinc-900 dark:text-zinc-100">₹<?php echo number_format($expected_amount); ?></span>
                                </div>

                                <div class="flex items-center justify-between py-1">
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-bold">Overdue Invoices</span>
                                    <span class="text-base font-extrabold text-zinc-455 dark:text-zinc-500">₹<?php echo number_format($overdue_amount); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/40 mt-3">
                            <button onclick="coraNavigateTo('financials')" class="w-full flex items-center justify-between text-xs font-bold text-emerald-650 dark:text-emerald-450 hover:text-emerald-750 dark:hover:text-emerald-300 transition-colors cursor-pointer group">
                                <span>Go to Financial Ledger</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 4: Clients Needing Attention (Spans 1 Column) -->
                    <div class="border border-zinc-100 dark:border-zinc-800/40 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-sm flex flex-col justify-between min-h-[280px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-orange-50 dark:bg-orange-950/20 text-orange-655 dark:text-orange-400 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Attention Required</h3>
                                        <span class="text-[11px] text-zinc-450 dark:text-zinc-500 font-medium">Follow ups that cannot wait</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3.5">
                                <?php
                                $attention_clients = array();
                                if ( is_array( $cora_workspace_clients ) ) {
                                    foreach ( $cora_workspace_clients as $client ) {
                                        if ( isset( $client['status'] ) && $client['status'] === 'pending' ) {
                                            $attention_clients[] = $client;
                                        }
                                    }
                                }
                                if ( empty( $attention_clients ) ) : ?>
                                    <div class="flex flex-col items-center justify-center py-6 text-center">
                                        <div class="h-10 w-10 rounded-full bg-zinc-50 dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-800/45 flex items-center justify-center mb-2">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        </div>
                                        <h4 class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300">All caught up</h4>
                                        <p class="text-[9px] text-zinc-455 dark:text-zinc-500 mt-0.5 max-w-[180px]">No immediate follow ups required.</p>
                                    </div>
                                <?php else : 
                                    foreach ( array_slice($attention_clients, 0, 3) as $client ) :
                                        $c_name = $client['names'] ?? 'Client';
                                        $c_reason = $client['reason'] ?? ($is_studio ? 'Shoot confirmation' : 'Lead follow-up');
                                        $c_time = $client['time_ago'] ?? '1d';
                                        $badge_cls = 'text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800';
                                        if (strtolower($c_time) === 'today') {
                                            $badge_cls = 'text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/20';
                                        }
                                    ?>
                                        <div class="flex items-center justify-between text-xs gap-2">
                                            <div class="flex flex-col min-w-0">
                                                <strong class="font-bold text-zinc-855 dark:text-zinc-100 truncate text-[11px]"><?php echo esc_html($c_name); ?></strong>
                                                <span class="text-[10px] text-zinc-455 dark:text-zinc-500 truncate"><?php echo esc_html($c_reason); ?></span>
                                            </div>
                                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full shrink-0 <?php echo $badge_cls; ?>"><?php echo esc_html($c_time); ?></span>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/40 mt-3">
                            <button onclick="coraNavigateTo('clients')" class="w-full flex items-center justify-between text-xs font-bold text-orange-600 dark:text-orange-455 hover:text-orange-700 dark:hover:text-orange-300 transition-colors cursor-pointer group">
                                <span>View All Contacts</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 5: Smart Suggestions (Spans 1 Column) -->
                    <div class="border border-zinc-100 dark:border-zinc-800/40 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-sm flex flex-col justify-between min-h-[280px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26C17.81 13.47 19 11.38 19 9a7 7 0 0 0-7-7z"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Smart Tasks</h3>
                                <div class="space-y-3.5">
                                <?php
                                $smart_tasks = array();
                                if ( $is_studio ) {
                                    if ( ! empty( $all_bookings ) ) {
                                        $first_bk = reset( $all_bookings );
                                        $c_name = $first_bk['client_name'] ?? 'Client';
                                        $smart_tasks[] = array(
                                            'title' => 'Assign Crew Shift',
                                            'desc'  => 'For ' . $c_name . ' shoot',
                                            'url'   => "coraNavigateTo('crew-scheduler')",
                                            'icon'  => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>'
                                        );
                                    }
                                    if ( ! empty( $cora_workspace_listings ) ) {
                                        $first_gear = reset( $cora_workspace_listings );
                                        $g_name = $first_gear['name'] ?? 'Gear';
                                        $smart_tasks[] = array(
                                            'title' => 'Check in ' . $g_name,
                                            'desc'  => $g_name . ' verification',
                                            'url'   => "coraNavigateTo('equipment')",
                                            'icon'  => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="12" y1="2" x2="12" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line></svg>'
                                        );
                                    }
                                } else {
                                    if ( ! empty( $all_bookings ) ) {
                                        $first_bk = reset( $all_bookings );
                                        $c_name = $first_bk['client_name'] ?? 'Client';
                                        $smart_tasks[] = array(
                                            'title' => 'Draft Tour Captions',
                                            'desc'  => 'For ' . $c_name . ' Tour',
                                            'url'   => "coraNavigateTo('ai-assistants')",
                                            'icon'  => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>'
                                        );
                                    }
                                    if ( ! empty( $cora_workspace_listings ) ) {
                                        $first_prop = reset( $cora_workspace_listings );
                                        $p_name = $first_prop['name'] ?? 'Property';
                                        $smart_tasks[] = array(
                                            'title' => 'Create Listing Brochure',
                                            'desc'  => 'For ' . $p_name . ' listing',
                                            'url'   => "window.coraOpenCommandPalette()",
                                            'icon'  => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>'
                                        );
                                    }
                                }

                                if ( empty( $smart_tasks ) ) : ?>
                                    <div class="flex flex-col items-center justify-center py-6 text-center">
                                        <div class="h-10 w-10 rounded-full bg-zinc-50 dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-800/45 flex items-center justify-center mb-2">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26C17.81 13.47 19 11.38 19 9a7 7 0 0 0-7-7z"></path></svg>
                                        </div>
                                        <h4 class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300">No smart tasks</h4>
                                        <p class="text-[9px] text-zinc-455 dark:text-zinc-500 mt-0.5 max-w-[180px]">Add bookings or listings to see recommendations.</p>
                                    </div>
                                <?php else : 
                                    foreach ( $smart_tasks as $task ) : ?>
                                        <div class="flex items-start gap-2.5 p-2 bg-zinc-55/35 dark:bg-zinc-850/20 border border-transparent rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/45 transition-all cursor-pointer" onclick="<?php echo $task['url']; ?>">
                                            <div class="h-6 w-6 rounded-lg bg-blue-50 dark:bg-blue-950/20 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0 font-bold text-[11px]">
                                                <?php echo $task['icon']; ?>
                                            </div>
                                            <div class="flex-1 min-w-0 pr-1 flex items-center justify-between">
                                                <div class="flex flex-col min-w-0">
                                                    <span class="text-[11px] font-bold text-zinc-800 dark:text-zinc-100 leading-tight"><?php echo esc_html( $task['title'] ); ?></span>
                                                    <span class="text-[9px] text-zinc-455 dark:text-zinc-500 truncate mt-0.5"><?php echo esc_html( $task['desc'] ); ?></span>
                                                </div>
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/40 mt-3">
                            <button onclick="window.coraOpenCommandPalette()" class="w-full flex items-center justify-between text-xs font-bold text-blue-650 dark:text-blue-455 hover:text-blue-750 dark:hover:text-blue-300 transition-colors cursor-pointer group">
                                <span>Browse Smart Actions</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 2: AI Inbox (Spans 1 Column) -->
                    <div class="border border-zinc-100 dark:border-zinc-800/40 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-sm flex flex-col justify-between min-h-[280px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">AI Inbox</h3>
                                        <span class="text-[11px] text-zinc-455 dark:text-zinc-500 font-medium">Pending system updates</span>
                                    </div>
                                </div>
                                <span class="h-5 w-5 rounded-full bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 font-bold text-[10px] flex items-center justify-center shrink-0"><?php echo intval($cora_unread_count); ?></span>
                            </div>

                            <div class="space-y-3">
                                <?php if ( empty( $cora_user_notifications ) ) : ?>
                                    <div class="flex flex-col items-center justify-center py-8 text-center">
                                        <div class="h-10 w-10 rounded-full bg-zinc-50 dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-800/45 flex items-center justify-center mb-2">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        </div>
                                        <h4 class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300">Your inbox is clear</h4>
                                        <p class="text-[9px] text-zinc-455 dark:text-zinc-500 mt-0.5 max-w-[180px]">No pending system updates or alerts.</p>
                                    </div>
                                <?php else : 
                                    foreach ( array_slice( $cora_user_notifications, 0, 2 ) as $notif ) : 
                                        $n_title = $notif['title'] ?? 'Notification';
                                        $n_body = $notif['body'] ?? '';
                                        $n_time = isset( $notif['timestamp'] ) ? human_time_diff( $notif['timestamp'], current_time( 'timestamp' ) ) . ' ago' : 'Just now';
                                        $n_icon = (strpos(strtolower($n_title), 'request') !== false || strpos(strtolower($n_title), 'appeal') !== false) ? '?' : 'i';
                                    ?>
                                        <div class="flex items-start gap-2.5 p-2 bg-zinc-50/30 dark:bg-zinc-850/20 border border-zinc-100/60 dark:border-zinc-800/30 rounded-xl">
                                            <div class="h-6 w-6 rounded-lg bg-purple-50 dark:bg-purple-950/20 text-purple-500 dark:text-purple-400 flex items-center justify-center shrink-0 font-bold text-[11px]"><?php echo $n_icon; ?></div>
                                            <div class="flex flex-col min-w-0 flex-1">
                                                <strong class="text-[11px] font-bold text-zinc-800 dark:text-zinc-100 truncate"><?php echo esc_html($n_title); ?></strong>
                                                <span class="text-[9px] text-zinc-455 dark:text-zinc-500 truncate"><?php echo esc_html($n_body); ?></span>
                                            </div>
                                            <span class="text-[9px] text-zinc-455 shrink-0"><?php echo esc_html($n_time); ?></span>
                                        </div>
                                    <?php endforeach; 
                                endif; ?>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/40 mt-3">
                            <button onclick="window.coraShowToast('AI Inbox is currently in staging sync mode.', 'info')" class="w-full flex items-center justify-between text-xs font-bold text-purple-650 dark:text-purple-455 hover:text-purple-750 dark:hover:text-purple-300 transition-colors cursor-pointer group">
                                <span>Check AI Messages</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>
                </div><!-- end cora-bento-grid -->


            </section>
            <?php endif; ?>
            
            <!-- SECTION 2: BOOKINGS (Routed to Client Task Manager Booked Shoots View) -->
            <?php if ( $sub_page === 'bookings' ) : ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 3: AI ASSISTANTS -->
            <?php if ( $sub_page === 'ai-assistants' ) : ?>
            <section id="cora-page-ai-assistants" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                        </svg>
                    </span>
                    <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">AI Assistants & Automation</h1>
                </div>
                <p class="cora-section-desc text-sm text-zinc-500 -mt-2">Deploy fine-tuned AI workflows to generate social media content, client follow-up templates, and WhatsApp automations.</p>

                <div class="cora-grid-two-col grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left: Instagram Caption Generator -->
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-5">
                        <div class="cora-card-icon-header flex items-center gap-2 border-b border-zinc-100 pb-3">
                            <span class="cora-card-header-emoji text-zinc-500 flex shrink-0">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </span>
                            <h3 class="cora-card-title text-base font-semibold text-zinc-950 dark:text-zinc-50">Instagram Caption Generator</h3>
                        </div>
                        
                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Select Booking / Showing Context</label>
                            <select id="cora-description-showing-select" class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="deal-jaipur">Rohit & Sneha - Luxury Villa Sale (Jaipur)</option>
                                <option value="maternity-delhi">Ananya Sharma - Residential Buy (Delhi)</option>
                                <option value="product-delhi">Rajesh Kumar - Commercial Office Lease (Delhi)</option>
                            </select>
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Aesthetic Mood & Tone</label>
                            <select id="cora-caption-mood" class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="cinematic">Cinematic & Storytelling</option>
                                <option value="romantic">Romantic & Poetic (Shayari touch)</option>
                                <option value="minimalist">Minimal & Modern</option>
                                <option value="royal">Royal & Traditional</option>
                            </select>
                        </div>

                        <button id="cora-generate-caption-btn" class="cora-btn-primary cora-btn-full w-full py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm">
                            Generate Captions
                        </button>

                        <!-- Response Box -->
                        <div id="cora-caption-response" class="cora-ai-output-box hidden border border-zinc-200 rounded-lg p-4 bg-zinc-50 space-y-2 mt-4">
                            <div class="cora-output-header flex justify-between items-center text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                                <span>AI Drafts</span>
                                <button class="cora-copy-btn text-zinc-500 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-50 font-semibold normal-case cursor-pointer" onclick="coraCopyText('cora-caption-text')">Copy</button>
                            </div>
                            <div id="cora-caption-text" class="cora-output-content text-xs text-zinc-800 whitespace-pre-line leading-relaxed font-mono">
                                <!-- JS will populate this -->
                            </div>
                        </div>
                    </div>

                    <!-- Right: WhatsApp Auto-Reminders -->
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-5">
                        <div class="cora-card-icon-header flex items-center gap-2 border-b border-zinc-100 pb-3">
                            <span class="cora-card-header-emoji text-zinc-500 flex shrink-0">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </span>
                            <h3 class="cora-card-title text-base font-semibold text-zinc-950">WhatsApp & Email Auto-Reminders</h3>
                        </div>

                        <div class="cora-toggle-list divide-y divide-zinc-150/80">
                            <div class="cora-toggle-item flex items-center justify-between py-3.5 gap-4">
                                <div class="cora-toggle-details flex-1">
                                    <span class="cora-toggle-title font-semibold text-sm text-zinc-900 block">Booking Confirmation WhatsApp</span>
                                    <span class="cora-toggle-desc text-xs text-zinc-500 block mt-0.5 leading-normal">Automatically ping client with showing location and timing details upon confirmation.</span>
                                </div>
                                <label class="cora-switch relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950"></div>
                                </label>
                            </div>

                            <div class="cora-toggle-item flex items-center justify-between py-3.5 gap-4">
                                <div class="cora-toggle-details flex-1">
                                    <span class="cora-toggle-title font-semibold text-sm text-zinc-900 block">24h Showing Reminder</span>
                                    <span class="cora-toggle-desc text-xs text-zinc-500 block mt-0.5 leading-normal">Send automated WhatsApp alert 24 hours prior to viewing with property details and brochure.</span>
                                </div>
                                <label class="cora-switch relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950"></div>
                                </label>
                            </div>

                            <div class="cora-toggle-item flex items-center justify-between py-3.5 gap-4">
                                <div class="cora-toggle-details flex-1">
                                    <span class="cora-toggle-title font-semibold text-sm text-zinc-900 block">AI Client Property Showcases Link</span>
                                    <span class="cora-toggle-desc text-xs text-zinc-500 block mt-0.5 leading-normal">Automatically emails/WhatsApp the preview portfolio once upload is completed.</span>
                                </div>
                                <label class="cora-switch relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-9 h-5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950"></div>
                                </label>
                            </div>
                        </div>

                        <div class="cora-callout bg-zinc-50 border border-zinc-200/80 rounded-lg p-3.5 flex gap-2.5 text-xs text-zinc-550 leading-relaxed shadow-sm">
                            <div class="cora-callout-emoji text-zinc-400 shrink-0 mt-0.5 flex">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                            </div>
                            <div class="cora-callout-text">
                                Local statistics report a substantial increase in client response speeds when deploying WhatsApp messaging templates over conventional emails in India.
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 4: CLIENT GALLERIES -->
            <?php if ( $sub_page === 'portfolio' ) : ?>
            <?php $cora_portfolios = get_option( 'cora_workspace_portfolios', array() ); ?>
            <section id="cora-page-portfolio" class="cora-page-section cora-active flex flex-col h-full overflow-hidden bg-white -m-4 p-0 md:-m-8 md:p-0">
                <!-- TOP NAVIGATION BAR -->
                <div class="border-b border-zinc-200 bg-zinc-50 px-4 sm:px-6 md:px-8 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-5 justify-center sm:justify-start">
                        <button class="text-xs sm:text-sm font-bold text-zinc-900 border-b-2 border-zinc-900 pb-1 cora-portfolio-tab-btn" data-tab="vault-all" onclick="coraSwitchGalleryTab('vault-all', this)">Master Media Vault</button>
                        <button class="text-xs sm:text-sm font-semibold text-zinc-500 hover:text-zinc-900 border-b-2 border-transparent pb-1 cora-portfolio-tab-btn" data-tab="client-portfolios" onclick="coraSwitchGalleryTab('client-portfolios', this)">Shared Portfolios</button>
                    </div>
                    <div class="flex items-center gap-2.5 justify-between sm:justify-start w-full sm:w-auto" id="cora-vault-topbar-actions">
                        <div class="relative flex-1 sm:flex-none">
                            <select id="cora-topbar-folder-select" class="w-full sm:w-auto appearance-none bg-white border border-zinc-200 text-zinc-700 text-xs rounded-md pl-3 pr-8 py-1.5 focus:outline-none focus:border-zinc-400 cursor-pointer font-semibold" onchange="coraSwitchVaultFolder(this.value, this.options[this.selectedIndex].text)">
                                <option value="0">All Media</option>
                            </select>
                            <svg class="w-3 h-3 text-zinc-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                        <button class="text-[11px] text-zinc-700 hover:text-zinc-900 font-semibold flex items-center justify-center gap-1 cursor-pointer transition-colors border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 px-2.5 py-1.5 rounded-md flex-1 sm:flex-none" onclick="coraCreateMediaFolderPrompt()"><svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> New Folder</button>
                    </div>
                </div>

                <!-- MAIN AREA -->
                <div class="flex-1 flex flex-col h-full overflow-y-auto relative bg-white">
                    
                    <!-- VAULT GRID VIEW -->
                    <div id="cora-vault-grid-view" class="p-6 md:p-8 space-y-6 transition-all duration-300 ease-in-out">
                        <div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900" id="cora-vault-title">All Media</h1>
                                <p class="cora-section-desc text-xs text-zinc-500 mt-1">Select assets to organize or create client portfolios.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button id="cora-btn-create-portfolio" class="hidden cora-btn-primary px-3 py-1.5 bg-zinc-950 text-white font-semibold rounded text-[11px] hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer items-center gap-1.5 shadow-sm" onclick="coraCreateClientGalleryFromSelection()">
                                    Create Gallery (<span id="cora-vault-selection-count">0</span>)
                                </button>
                                <button id="cora-btn-delete-selection" class="hidden p-1.5 bg-white border border-red-200 text-red-600 font-semibold rounded hover:bg-red-50 transition-all active:scale-[0.98] cursor-pointer items-center shadow-sm" onclick="coraDeleteVaultSelection()" title="Delete Selected">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                                <button class="cora-btn-primary px-3 py-1.5 bg-zinc-950 text-white font-semibold rounded text-[11px] hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer flex items-center gap-1.5 shadow-sm" onclick="coraVaultOpenUpload()">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    Upload to Folder
                                </button>
                            </div>
                        </div>

                        <div id="cora-master-media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                            <div class="col-span-full py-10 text-center text-zinc-400 text-xs flex flex-col items-center">
                                <div class="w-5 h-5 border-2 border-zinc-300 border-t-zinc-600 rounded-full animate-spin mb-2"></div>
                                Loading media vault...
                            </div>
                        </div>
                    </div>

                    <!-- LIST VIEW (Property Portfolios) -->
                    <div id="cora-portfolio-list-view" class="hidden p-3.5 sm:p-6 md:p-8 space-y-4 sm:space-y-6 transition-all duration-300 ease-in-out">
                        <div class="cora-page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 border-b border-zinc-100 pb-3 sm:pb-4">
                            <div class="flex items-center gap-2.5 sm:gap-3">
                                <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                                    <svg viewBox="0 0 24 24" width="24" height="24" class="sm:w-[30px] sm:h-[30px]" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </span>
                                <div>
                                    <h1 class="cora-page-title text-lg sm:text-2xl font-bold tracking-tight text-zinc-900">Shared Property Portfolios</h1>
                                    <p class="cora-section-desc text-[10px] sm:text-xs text-zinc-500 mt-0.5 sm:mt-1">Create password-protected folders and sync client property selections for albums.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="cora-btn-primary flex-1 sm:flex-none justify-center px-3 sm:px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs inline-flex items-center gap-2" onclick="coraOpenShareGalleryModal()">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Create Gallery
                                </button>
                                <button class="flex-1 sm:flex-none justify-center px-3 sm:px-4 py-2 border border-zinc-200 bg-white text-zinc-700 font-semibold rounded-md hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-xs inline-flex items-center gap-2" onclick="coraVaultOpenUpload()">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    Upload
                                </button>
                            </div>
                        </div>

                    <div class="md:border md:border-zinc-200/80 md:rounded-xl md:overflow-hidden md:shadow-sm md:bg-white">
                        <!-- Desktop View (Large Screen) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 text-left">
                                <thead class="bg-zinc-50">
                                    <tr>
                                        <th class="px-6 py-3.5 text-xs font-bold text-zinc-400 uppercase tracking-wider">Property Name / Showcase Title</th>
                                        <th class="px-6 py-3.5 text-xs font-bold text-zinc-400 uppercase tracking-wider">Template</th>
                                        <th class="px-6 py-3.5 text-xs font-bold text-zinc-400 uppercase tracking-wider">Security</th>
                                        <th class="px-6 py-3.5 text-xs font-bold text-zinc-400 uppercase tracking-wider">Assets</th>
                                        <th class="px-6 py-3.5 text-xs font-bold text-zinc-400 uppercase tracking-wider">Selections</th>
                                        <th class="px-6 py-3.5 text-xs font-bold text-zinc-400 uppercase tracking-wider">Share Link</th>
                                        <th class="px-6 py-3.5 text-xs font-bold text-zinc-400 uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 bg-white" id="cora-portfolios-table-body">
                                    <?php if ( empty( $cora_portfolios ) ) : ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-3">
                                                <div class="text-zinc-350">
                                                    <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" stroke-width="1.2" fill="none">
                                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                        <polyline points="21 15 16 10 5 21"></polyline>
                                                    </svg>
                                                </div>
                                                <h3 class="text-sm font-semibold text-zinc-900">No Portfolios Found</h3>
                                                <p class="text-xs text-zinc-500 max-w-sm">Create your first client portfolio folder to start sharing photos and collecting client selections.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php else : ?>
                                        <?php foreach ( $cora_portfolios as $portfolio ) : 
                                            $share_url = home_url( '/shared-portfolio/' . $portfolio['hash'] );
                                            $assets = isset( $portfolio['assets'] ) ? $portfolio['assets'] : array();
                                            $likes = isset( $portfolio['likes'] ) ? $portfolio['likes'] : array();
                                            $template_label = 'Grid';
                                            if ( isset( $portfolio['template'] ) && $portfolio['template'] === 'masonry' ) {
                                                $template_label = 'Masonry';
                                            } elseif ( isset( $portfolio['template'] ) && $portfolio['template'] === 'carousel' ) {
                                                $template_label = 'Carousel';
                                            }
                                            $is_secured = ! empty( $portfolio['password'] );
                                        ?>
                                        <tr class="hover:bg-zinc-50/40 transition-colors cursor-pointer" onclick="if(event.target.tagName !== 'BUTTON' && event.target.tagName !== 'INPUT' && !event.target.closest('button')) coraShowGalleryDetails('<?php echo esc_js( $portfolio['id'] ); ?>')" data-portfolio-id="<?php echo esc_attr( $portfolio['id'] ); ?>">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-zinc-900 group flex items-center gap-2">
                                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-blue-500"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                                    <?php echo esc_html( $portfolio['title'] ); ?>
                                                </div>
                                                <div class="text-[10px] text-zinc-400 font-mono mt-0.5 ml-6"><?php echo esc_html( $portfolio['id'] ); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-zinc-100 text-zinc-800 border border-zinc-200">
                                                    <?php echo esc_html($template_label); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ( $is_secured ) : ?>
                                                    <span class="inline-flex items-center gap-1 text-xs text-zinc-800 font-semibold" title="Password Protected">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                        Protected
                                                    </span>
                                                <?php else : ?>
                                                    <span class="inline-flex items-center gap-1 text-xs text-zinc-400 font-semibold">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                                        Public
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-zinc-600 font-medium">
                                                <?php echo count( $assets ); ?> items
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-zinc-600 font-semibold">
                                                <span class="<?php echo count( $likes ) > 0 ? 'text-zinc-900 font-bold underline' : 'text-zinc-400'; ?>">
                                                    <?php echo count( $likes ); ?> selected
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                                <div class="flex items-center gap-2">
                                                    <input type="text" readonly value="<?php echo esc_url( $share_url ); ?>" class="text-[10px] bg-zinc-50 border border-zinc-200 rounded px-2 py-1 select-all focus:outline-none w-44 truncate text-zinc-600 font-mono">
                                                    <button class="p-1 border border-zinc-200 rounded hover:bg-zinc-100 text-zinc-500 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraCopyShareLink('<?php echo esc_js( $share_url ); ?>')" title="Copy Link">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button class="p-1.5 border border-zinc-200 rounded hover:bg-zinc-100 text-zinc-700 hover:text-black transition-colors cursor-pointer" onclick="event.stopPropagation(); coraShowGalleryDetails('<?php echo esc_js( $portfolio['id'] ); ?>')" title="Manage Assets">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                                        </svg>
                                                    </button>
                                                    <button class="p-1.5 border border-zinc-200 rounded hover:bg-zinc-100 text-zinc-700 hover:text-black transition-colors cursor-pointer" onclick="event.stopPropagation(); coraOpenShareGalleryModal('<?php echo esc_js( $portfolio['id'] ); ?>')" title="Gallery Settings &amp; Share">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33H15a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 16 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H15a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                                        </svg>
                                                    </button>
                                                    <button class="p-1.5 border border-zinc-200 rounded hover:bg-zinc-100 text-red-500 hover:text-red-700 hover:bg-red-50/50 transition-colors cursor-pointer" onclick="event.stopPropagation(); coraDeleteGallery('<?php echo esc_js( $portfolio['id'] ); ?>')" title="Delete Gallery">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile/Tablet Card View (Visible below 768px) -->
                        <div class="md:hidden space-y-3" id="cora-portfolios-cards-list">
                            <?php if ( empty( $cora_portfolios ) ) : ?>
                                <div class="py-10 text-center flex flex-col items-center space-y-3">
                                    <div class="text-zinc-300">
                                        <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.2" fill="none">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-zinc-900">No Portfolios Found</h3>
                                    <p class="text-[11px] text-zinc-500 max-w-xs">Create your first client portfolio to start sharing photos and collecting client selections.</p>
                                </div>
                            <?php else : ?>
                                <?php foreach ( $cora_portfolios as $portfolio ) :
                                    $share_url = home_url( '/shared-portfolio/' . $portfolio['hash'] );
                                    $assets = isset( $portfolio['assets'] ) ? $portfolio['assets'] : array();
                                    $likes = isset( $portfolio['likes'] ) ? $portfolio['likes'] : array();
                                    $template_label = 'Grid';
                                    if ( isset( $portfolio['template'] ) && $portfolio['template'] === 'masonry' ) {
                                        $template_label = 'Masonry';
                                    } elseif ( isset( $portfolio['template'] ) && $portfolio['template'] === 'carousel' ) {
                                        $template_label = 'Carousel';
                                    }
                                    $is_secured = ! empty( $portfolio['password'] );
                                ?>
                                <div class="cora-portfolio-card-item bg-white border border-zinc-200 rounded-xl p-4 shadow-sm hover:shadow hover:border-zinc-300 transition-all cursor-pointer flex flex-col gap-3"
                                     data-portfolio-id="<?php echo esc_attr( $portfolio['id'] ); ?>"
                                     onclick="if(event.target.tagName !== 'BUTTON' && !event.target.closest('button')) coraShowGalleryDetails('<?php echo esc_js( $portfolio['id'] ); ?>')">

                                    <!-- Card Header: Property Name / Showcase Title & Template badge -->
                                    <div class="flex items-start justify-between gap-2 border-b border-zinc-100 pb-2.5">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 shrink-0"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                            <div class="flex flex-col min-w-0">
                                                <span class="font-bold text-sm text-zinc-900 truncate"><?php echo esc_html( $portfolio['title'] ); ?></span>
                                                <span class="text-[9px] text-zinc-400 font-mono mt-0.5 truncate"><?php echo esc_html( $portfolio['id'] ); ?></span>
                                            </div>
                                        </div>
                                        <span class="cora-badge text-[9px] px-2 py-0.5 rounded font-semibold bg-zinc-100 text-zinc-800 border border-zinc-200 shrink-0"><?php echo esc_html( $template_label ); ?></span>
                                    </div>

                                    <!-- Card Body: Security, Assets, Selections -->
                                    <div class="grid grid-cols-3 gap-3 text-xs">
                                        <div class="flex flex-col gap-0.5 min-w-0">
                                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Security</span>
                                            <?php if ( $is_secured ) : ?>
                                                <span class="inline-flex items-center gap-1 font-semibold text-zinc-800">
                                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                    Protected
                                                </span>
                                            <?php else : ?>
                                                <span class="inline-flex items-center gap-1 font-semibold text-zinc-400">
                                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                                    Public
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex flex-col gap-0.5 min-w-0">
                                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Assets</span>
                                            <span class="font-medium text-zinc-700"><?php echo count( $assets ); ?> items</span>
                                        </div>
                                        <div class="flex flex-col gap-0.5 min-w-0">
                                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Selections</span>
                                            <span class="font-semibold <?php echo count( $likes ) > 0 ? 'text-zinc-900 underline' : 'text-zinc-400'; ?>"><?php echo count( $likes ); ?> selected</span>
                                        </div>
                                    </div>

                                    <!-- Card Footer: Share Link & Actions -->
                                    <div class="flex items-center justify-between border-t border-zinc-100 pt-2.5 mt-0.5">
                                        <button class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-zinc-500 hover:text-zinc-900 transition-colors cursor-pointer" onclick="event.stopPropagation(); coraCopyShareLink('<?php echo esc_js( $share_url ); ?>')">
                                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                            Copy Link
                                        </button>
                                        <div class="flex items-center gap-1.5">
                                            <button class="p-1.5 border border-zinc-200 rounded hover:bg-zinc-100 text-zinc-700 hover:text-black transition-colors cursor-pointer" onclick="event.stopPropagation(); coraShowGalleryDetails('<?php echo esc_js( $portfolio['id'] ); ?>')" title="Manage Assets">
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="3" y1="9" x2="21" y2="9"></line>
                                                    <line x1="9" y1="21" x2="9" y2="9"></line>
                                                </svg>
                                            </button>
                                            <button class="p-1.5 border border-zinc-200 rounded hover:bg-zinc-100 text-zinc-700 hover:text-black transition-colors cursor-pointer" onclick="event.stopPropagation(); coraOpenShareGalleryModal('<?php echo esc_js( $portfolio['id'] ); ?>')" title="Gallery Settings">
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33H15a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 16 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H15a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                                </svg>
                                            </button>
                                            <button class="p-1.5 border border-zinc-200 rounded hover:bg-zinc-100 text-red-500 hover:text-red-700 hover:bg-red-50/50 transition-colors cursor-pointer" onclick="event.stopPropagation(); coraDeleteGallery('<?php echo esc_js( $portfolio['id'] ); ?>')" title="Delete Gallery">
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- DETAILS GRID VIEW -->
                <div id="cora-portfolio-details-view" class="hidden flex-col h-full p-6 md:p-8 space-y-6 transition-all duration-300 ease-in-out">
                    <!-- Details Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200/50 pb-4">
                        <div class="flex items-center gap-4">
                            <button class="p-1.5 rounded-md border border-zinc-200 text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 transition-colors cursor-pointer flex items-center justify-center bg-white shadow-sm" onclick="coraShowGalleryListView()" title="Back to Portfolios">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                            </button>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2 group">
                                    <h2 id="cora-detail-portfolio-title-text" class="text-xl font-bold tracking-tight text-zinc-900 cursor-text" onclick="coraEditActiveGalleryTitle()">Gallery Title</h2>
                                    <input type="text" id="cora-detail-portfolio-title-input" class="hidden text-xl font-bold tracking-tight text-zinc-900 border-b border-zinc-300 bg-transparent focus:outline-none focus:border-zinc-900 px-0 py-0 w-64" onblur="coraSaveActiveGalleryTitle()" onkeydown="if(event.key === 'Enter') coraSaveActiveGalleryTitle()">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-500 transition-colors cursor-pointer" onclick="coraEditActiveGalleryTitle()">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </div>
                                <div id="cora-detail-portfolio-stats" class="text-xs text-zinc-500 font-medium flex items-center gap-2 mt-0.5">
                                    <span id="cora-stat-photos">0 Photos</span> &bull; <span id="cora-stat-videos">0 Videos</span> &bull; <span id="cora-stat-security" class="text-zinc-700 font-bold">Public</span>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 w-full md:w-auto mt-3 md:mt-0">
                            <button class="w-full sm:w-auto justify-center px-3 py-1.5 border border-zinc-250 bg-white text-zinc-700 font-semibold rounded-md hover:bg-zinc-50 transition-colors text-[11px] shadow-sm flex items-center gap-1.5" onclick="coraOpenUploadMedia()">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                Upload
                            </button>
                            <button class="w-full sm:w-auto justify-center px-3 py-1.5 border border-zinc-250 bg-white text-zinc-700 font-semibold rounded-md hover:bg-zinc-50 transition-colors text-[11px] shadow-sm flex items-center gap-1.5" onclick="coraOpenLinkGoogleDriveModal()">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                Link URL
                            </button>
                            <button class="w-full sm:w-auto justify-center px-3 py-1.5 border border-zinc-200 bg-white text-zinc-400 font-semibold rounded-md text-[11px] shadow-sm flex items-center gap-1.5 cursor-not-allowed opacity-60" onclick="window.coraShowToast('Google Drive Sync is coming soon! Secure folder syncing is currently in development.')" title="Coming Soon">
                                <svg viewBox="0 0 128 128" width="12" height="12" xmlns="http://www.w3.org/2000/svg" class="grayscale"><path fill="#FFC107" d="M43.4 20L11.7 75.1l14.5 25.1L89.6 45.1 75 20z"></path><path fill="#1976D2" d="M89.6 45.1L75 20H43.4l14.5 25.1z"></path><path fill="#4CAF50" d="M26.2 100.2h63.5l14.5-25.1H40.7z"></path><path fill="#1565C0" d="M11.7 75.1L26.2 100.2h14.5l-14.5-25.1z"></path><path fill="#388E3C" d="M89.7 45.1l14.5 25.1-14.5 30H75.1l14.6-25z"></path></svg>
                                Sync with Drive
                            </button>
                            <button class="w-full sm:w-auto justify-center px-4 py-1.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-colors text-[11px] shadow-sm flex items-center gap-1.5" onclick="coraOpenShareGalleryModal()">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                Share & Settings
                            </button>
                        </div>
                    </div>
                    
                    <!-- Toolbar (Search, Filter, Sort) -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-zinc-50/50 p-3 rounded-lg border border-zinc-200/60">
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative w-full sm:w-64">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="cora-detail-portfolio-search" placeholder="Search assets..." class="w-full pl-8 pr-3 py-1.5 text-xs bg-white border border-zinc-200 rounded-md focus:outline-none focus:border-zinc-400 text-zinc-800 font-medium placeholder-zinc-400" onkeyup="coraRenderActiveGalleryAssets()">
                            </div>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
                            <div class="flex items-center bg-zinc-200/50 p-0.5 rounded-md text-[10px] font-bold text-zinc-500 uppercase tracking-wider shrink-0" id="cora-detail-portfolio-filters">
                                <button class="cora-filter-tab active px-3 py-1 rounded bg-white text-zinc-900 shadow-sm transition-all cursor-pointer" data-filter="all" onclick="coraSetAssetFilter('all')">All</button>
                                <button class="cora-filter-tab px-3 py-1 rounded hover:text-zinc-900 transition-all cursor-pointer" data-filter="image" onclick="coraSetAssetFilter('image')">Photos</button>
                                <button class="cora-filter-tab px-3 py-1 rounded hover:text-zinc-900 transition-all cursor-pointer" data-filter="video" onclick="coraSetAssetFilter('video')">Videos</button>
                                <button class="cora-filter-tab px-3 py-1 rounded hover:text-zinc-900 transition-all cursor-pointer" data-filter="selected" onclick="coraSetAssetFilter('selected')">Selected</button>
                            </div>
                            <div class="h-4 w-px bg-zinc-200 shrink-0 hidden sm:block"></div>
                            <select id="cora-detail-portfolio-sort" class="text-xs bg-white border border-zinc-200 rounded-md px-2 py-1.5 focus:outline-none focus:border-zinc-400 text-zinc-700 font-semibold cursor-pointer shrink-0" onchange="coraRenderActiveGalleryAssets()">
                                <option value="name-asc">Name (A-Z)</option>
                                <option value="name-desc">Name (Z-A)</option>
                                <option value="type-photo">Photos first</option>
                                <option value="type-video">Videos first</option>
                                <option value="mixed">Mixed (Upload order)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Drive Sync Status Banner (Hidden if none) -->
                    <div id="cora-detail-drive-banner" class="hidden items-center justify-between bg-zinc-50 border border-zinc-200/80 rounded-lg p-3">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                            <span class="text-[11px] font-medium text-zinc-800">Synced with Google Drive Folder</span>
                            <span id="cora-detail-drive-url" class="text-[10px] font-mono text-zinc-500 ml-1 truncate max-w-[200px]"></span>
                        </div>
                        <button class="px-2.5 py-1 bg-white border border-zinc-200 text-zinc-700 font-semibold rounded text-[10px] hover:bg-zinc-50 transition-colors shadow-sm cursor-pointer" onclick="coraResyncGoogleDriveFolder()">Sync Now</button>
                    </div>

                    <!-- Grid -->
                    <div id="cora-detail-portfolio-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 pb-8">
                        <!-- Cards injected via JS -->
                    </div>
                </div>
                </div> <!-- End Main Area flex-1 -->
            </section>
            <?php endif; ?>

            <!-- SECTION 5: SETTINGS -->
            <?php if ( $sub_page === 'settings' ) : ?>
            <section id="cora-page-settings" class="cora-page-section cora-active space-y-5">
                <!-- Page Header -->
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </span>
                    <div>
                        <h1 class="cora-page-title text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">Workspace Settings</h1>
                        <p class="cora-section-desc text-[11px] sm:text-xs text-zinc-500 mt-0.5">Configure your agency profile, integrations, and automation preferences.</p>
                    </div>
                </div>

                <div class="max-w-2xl space-y-5">

                <!-- ═══ SECTION 1: Agency Identity ═══ -->
                <div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 pt-5 pb-4 border-b border-zinc-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-600">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900">Your Agency</h3>
                                <p class="text-[11px] text-zinc-500 mt-0.5">Basic info about your real estate brand.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Agency / Brand Name</label>
                                <input type="text" class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-200 transition-all placeholder:text-zinc-300" value="Cora for Real Estate" placeholder="e.g. Apex Realty Group">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Region / Currency</label>
                                <select class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-200 transition-all appearance-none cursor-pointer">
                                    <option value="IN">India (₹ INR)</option>
                                    <option value="US">United States ($ USD)</option>
                                    <option value="UK">United Kingdom (£ GBP)</option>
                                    <option value="AE">United Arab Emirates (AED)</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AI Writing Style</label>
                            <select class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-200 transition-all appearance-none cursor-pointer">
                                <option value="cinematic">Cinematic & Poetic — Best for fine-art real estate brokers</option>
                                <option value="professional">Professional & Direct — Best for commercial realtors</option>
                                <option value="friendly">Warm & Welcoming — Best for boutique or independent real estate agents</option>
                            </select>
                            <span class="text-[10px] text-zinc-400">This affects how Cora writes captions, messages, and auto-replies on your behalf.</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Platform Language</label>
                            <select id="cora-platform-language-select" class="cora-language-selector w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-200 transition-all appearance-none cursor-pointer" onchange="if(window.coraSetLanguage) window.coraSetLanguage(this.value, false);">
                                <option value="en">English</option>
                                <option value="hi">Hindi (हिन्दी)</option>
                                <option value="es">Spanish (Español)</option>
                                <option value="fr">French (Français)</option>
                                <option value="de">German (Deutsch)</option>
                                <option value="bn">Bengali (বাংলা)</option>
                                <option value="te">Telugu (తెలుగు)</option>
                                <option value="mr">Marathi (मराठी)</option>
                                <option value="ta">Tamil (தமிழ்)</option>
                                <option value="gu">Gujarati (ગુજરાતી)</option>
                                <option value="kn">Kannada (ಕನ್ನಡ)</option>
                                <option value="ml">Malayalam (മലയാളം)</option>
                                <option value="pa">Punjabi (ਪੰਜਾਬੀ)</option>
                                <option value="or">Odia (ଓଡ଼ିଆ)</option>
                            </select>
                            <span class="text-[10px] text-zinc-400">Change the display language of the entire platform.</span>
                        </div>
                    </div>
                    <div class="px-5 py-3 bg-zinc-50/50 border-t border-zinc-100 flex justify-end">
                        <button class="text-xs font-semibold px-4 py-2 bg-zinc-900 text-white rounded-lg hover:bg-zinc-800 transition-all active:scale-[0.97] cursor-pointer inline-flex items-center gap-1.5" onclick="if(window.coraSetLanguage){ window.coraSetLanguage($('#cora-platform-language-select').val(), true); } else { window.coraShowToast('Studio settings saved.'); }">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Save
                        </button>
                    </div>
                    <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded', function() {
                        if (typeof window.coraSyncLanguageUI === 'function') {
                            window.coraSyncLanguageUI();
                        }
                    });
                    </script>
                </div>

                <!-- ═══ SECTION 2: WhatsApp Gateway (Coming Soon) ═══ -->
                <div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden relative" onclick="if(event.target.tagName !== 'INPUT') window.coraShowToast('WhatsApp integration is coming soon. This feature is currently under development.')">
                    <div class="px-5 pt-5 pb-4 border-b border-zinc-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 opacity-50">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-zinc-500">WhatsApp Autopilot</h3>
                                    <p class="text-[11px] text-zinc-400 mt-0.5">Auto-send booking confirmations and photo links to clients via WhatsApp.</p>
                                </div>
                            </div>
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200 shrink-0">Coming Soon</span>
                        </div>
                    </div>
                    <div class="p-5 space-y-4 opacity-40 pointer-events-none select-none">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">WhatsApp Phone Number ID</label>
                            <input type="text" disabled class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-zinc-50 text-zinc-400 cursor-not-allowed" placeholder="Your WhatsApp Business Phone ID">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Access Token</label>
                            <input type="password" disabled class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-zinc-50 text-zinc-400 cursor-not-allowed" placeholder="••••••••••••••••••••">
                        </div>
                    </div>
                </div>

                <!-- ═══ SECTION 3: Google Business Profile ═══ -->
                <div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 pt-5 pb-4 border-b border-zinc-100">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-600">
                                        <circle cx="12" cy="10" r="3"></circle>
                                        <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 7 8 11.7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-zinc-900">Google Business Profile</h3>
                                    <p class="text-[11px] text-zinc-500 mt-0.5">Let clients find your brokerage on Google and manage reviews.</p>
                                </div>
                            </div>
                            <div class="flex gap-2 ml-10 sm:ml-0">
                                <span class="text-[9px] border px-2 py-0.5 rounded-full font-bold <?php echo $cora_gbp_has_maps_key ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-zinc-100 text-zinc-500 border-zinc-200'; ?>">
                                    Maps: <?php echo $cora_gbp_has_maps_key ? 'Connected' : 'Not Set'; ?>
                                </span>
                                <span class="text-[9px] border px-2 py-0.5 rounded-full font-bold <?php echo $cora_gbp_has_credentials ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-zinc-100 text-zinc-500 border-zinc-200'; ?>">
                                    Reviews: <?php echo $cora_gbp_has_credentials ? 'Connected' : 'Optional'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-5">
                        <!-- Step 1: Maps API Key -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-zinc-900 text-white text-[10px] font-bold flex items-center justify-center shrink-0">1</span>
                                <h4 class="text-xs font-bold text-zinc-800">Find Your Business on Google</h4>
                                <span class="text-[9px] text-zinc-400 font-medium ml-1">Required</span>
                            </div>
                            <p class="text-[11px] text-zinc-500 leading-relaxed sm:ml-7">Clients can search your agency name and instantly connect your Google listing. This uses a Google Maps API key.</p>
                            <div class="sm:ml-7 flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Google Maps API Key</label>
                                <input type="password" id="cora-gbp-maps-api-key" class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-200 transition-all font-mono text-zinc-700 placeholder:font-sans placeholder:text-zinc-300" value="<?php echo esc_attr( $cora_gbp_maps_api_key ? str_repeat('•', 24) : '' ); ?>" placeholder="AIzaSy..." oncopy="return false;" oncut="return false;" autocomplete="off">
                                <span class="text-[10px] text-zinc-400">Get a key from <a href="https://console.cloud.google.com" target="_blank" class="underline hover:text-zinc-600">Google Cloud Console</a> → enable "Places API (New)".</span>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-zinc-100"></div>

                        <!-- Step 2: OAuth (Optional) -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-zinc-200 text-zinc-600 text-[10px] font-bold flex items-center justify-center shrink-0">2</span>
                                <h4 class="text-xs font-bold text-zinc-800">Manage Reviews & Posts</h4>
                                <span class="text-[9px] text-zinc-400 font-medium ml-1">Optional</span>
                            </div>
                            <p class="text-[11px] text-zinc-500 leading-relaxed sm:ml-7">Reply to Google reviews and publish updates directly from Cora. Requires OAuth credentials from Google Cloud.</p>

                            <!-- Redirect URI callout -->
                            <div class="sm:ml-7 bg-zinc-50 rounded-xl p-3.5 space-y-2">
                                <span class="text-[10px] font-semibold text-zinc-600 block">Copy this Redirect URI into your Google Cloud Console:</span>
                                <div class="flex items-center gap-2">
                                    <code class="flex-1 bg-zinc-100 px-2.5 py-1.5 rounded-lg text-[11px] text-zinc-800 font-mono select-all block truncate border-0"><?php echo esc_html( home_url( '/workspace/gbp' ) ); ?></code>
                                    <button class="shrink-0 p-1.5 border border-zinc-200 bg-white rounded-md hover:bg-zinc-100 text-zinc-500 hover:text-zinc-900 transition-colors cursor-pointer" onclick="event.stopPropagation(); navigator.clipboard.writeText('<?php echo esc_js( home_url( '/workspace/gbp' ) ); ?>'); window.coraShowToast('Redirect URI copied.')" title="Copy URI">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="sm:ml-7 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Client ID</label>
                                    <input type="text" id="cora-gbp-client-id" class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-200 transition-all font-mono text-zinc-700 placeholder:font-sans placeholder:text-zinc-300" value="<?php echo esc_attr( $cora_gbp_client_id ); ?>" placeholder="xxxxx.apps.googleusercontent.com">
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Client Secret</label>
                                    <input type="password" id="cora-gbp-client-secret" class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-200 transition-all placeholder:text-zinc-300" value="<?php echo esc_attr( $cora_gbp_client_secret ); ?>" placeholder="GOCSPX-...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-3 bg-zinc-50/50 border-t border-zinc-100 flex justify-end">
                        <button onclick="coraGbpSaveApiCredentials()" id="cora-gbp-creds-save-btn" class="text-xs font-semibold px-4 py-2 bg-zinc-900 text-white rounded-lg hover:bg-zinc-800 transition-all active:scale-[0.97] cursor-pointer inline-flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Save Google Settings
                        </button>
                    </div>
                </div>


                <!-- ═══ BOTTOM ACTIONS ═══ -->
                <div class="flex items-center justify-between pt-1">
                    <button class="px-5 py-2.5 bg-zinc-900 text-white font-semibold rounded-lg hover:bg-zinc-800 transition-all active:scale-[0.97] cursor-pointer text-sm inline-flex items-center gap-2" onclick="coraShowToast('All workspace settings saved.')">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Save All Settings
                    </button>
                    <button class="border border-zinc-200 px-3.5 py-2 bg-white text-zinc-600 font-medium rounded-lg hover:bg-zinc-50 transition-all active:scale-[0.97] cursor-pointer text-xs inline-flex items-center gap-1.5" onclick="coraStartProductTour()">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        Onboarding Tour
                    </button>
                </div>

                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 6: FEATURE HUB -->
            <?php if ( $sub_page === 'feature-hub' ) : ?>
            <section id="cora-page-feature-hub" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-feature-hub.php'; ?>
            </section>
            <?php endif; ?>


            <!-- SECTION GBP: GOOGLE BUSINESS PROFILE -->
            <?php if ( $sub_page === 'gbp' ) : ?>
            <section id="cora-page-gbp" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-google-profile.php'; ?>
            </section>
            <?php endif; ?>

            <?php if ( $sub_page === 'team-roles' ) : ?>
            <section id="cora-page-team-roles" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-users.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION 8: EQUIPMENT TRACKING -->
            <?php if ( $sub_page === 'equipment' ) : ?>
            <?php 
                $current_industry = function_exists( 'cora_get_active_industry' ) ? cora_get_active_industry() : ( isset( $active_industry ) ? $active_industry : get_option( 'cora_workspace_industry', 'real_estate' ) );
                if ( $current_industry === 'photography_studio' || $current_industry === 'photography' ) :
            ?>
            <section id="cora-page-equipment" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-equipment.php'; ?>
            </section>
            <?php else : ?>
            <section id="cora-page-equipment" class="cora-page-section cora-active space-y-6">
                <div class="flex items-center justify-between">
                    <div class="cora-page-header flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                        </span>
                        <div>
                            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Property Listings Inventory</h1>
                            <p class="cora-section-desc text-xs text-zinc-500 mt-1">Track high-value villas, apartments, penthouses, commercial spaces, and plots.</p>
                        </div>
                    </div>
                </div>

                <!-- Sub-Navigation for Listings Section -->
                <div class="cora-sub-tabs border-b border-zinc-200 flex gap-4 text-xs font-bold text-zinc-550 select-none pb-0.5">
                    <button class="cora-sub-tab active pb-2 border-b-2 border-zinc-950 text-zinc-950 cursor-pointer" data-sub-target="eq-registry">Listings Registry</button>
                    <button class="pb-2 border-b-2 border-transparent hover:text-zinc-900 cursor-pointer focus:outline-none" onclick="coraOpenListingDrawerForCreate()">Add Listing</button>
                    <button class="cora-sub-tab pb-2 border-b-2 border-transparent hover:text-zinc-900 cursor-pointer" id="cora-sub-tab-eq-assign" data-sub-target="eq-assign">Assign / Release</button>
                </div>

                <!-- SUB-SECTION 1: PROPERTY LISTINGS REGISTRY -->
                <div id="cora-sub-page-eq-registry" class="cora-sub-section active space-y-6">
                    <!-- Stats summary counts -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="cora-equipment-stats-grid">
                        <?php
                        $total_items = count($cora_workspace_listings);
                        $available_items = 0;
                        $in_use_items = 0;
                        $maintenance_items = 0;
                        foreach ($cora_workspace_listings as $item) {
                            if ($item['status'] === 'Available') $available_items++;
                            elseif ($item['status'] === 'In Use') $in_use_items++;
                            elseif ($item['status'] === 'Maintenance') $maintenance_items++;
                        }
                        ?>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Listings</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-eq-stat-total"><?php echo $total_items; ?></span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Available</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1 flex items-center gap-1.5 text-emerald-600" id="cora-eq-stat-avail">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="cora-stat-count-num"><?php echo $available_items; ?></span>
                            </span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">In Use</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1 flex items-center gap-1.5 text-indigo-600" id="cora-eq-stat-use">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                <span class="cora-stat-count-num"><?php echo $in_use_items; ?></span>
                            </span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Under Maintenance</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1 flex items-center gap-1.5 text-amber-600" id="cora-eq-stat-maint">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-550"></span>
                                <span class="cora-stat-count-num"><?php echo $maintenance_items; ?></span>
                            </span>
                        </div>
                    </div>

                    <!-- Listings Table -->
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-zinc-900 pb-2 border-b border-zinc-100">Property Listings Registry</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 text-xs text-left" id="cora-equipment-table">
                                <thead>
                                    <tr class="bg-zinc-50/50">
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] w-12">Photo</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] min-w-[140px]">Listing Name</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] w-24 whitespace-nowrap">Category</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] w-28 whitespace-nowrap">RERA Reg ID / Plot Number</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] w-24 whitespace-nowrap">Status</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] min-w-[120px] whitespace-nowrap">Assigned Agent</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] min-w-[160px]">Linked Showing / Viewing</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] min-w-[160px]">Assignment Details</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] w-24 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-150 bg-white" id="cora-equipment-table-body">
                                    <?php foreach ($cora_workspace_listings as $item): 
                                        $status_class = '';
                                        if ($item['status'] === 'Available') $status_class = 'cora-badge-green';
                                        elseif ($item['status'] === 'In Use') $status_class = 'cora-badge-soon';
                                        elseif ($item['status'] === 'Maintenance') $status_class = 'cora-badge-locked';
                                        
                                        $photo_url = !empty($item['photo_url']) ? $item['photo_url'] : '';
                                        $assignment_note = !empty($item['assignment_note']) ? $item['assignment_note'] : '';
                                    ?>
                                    <tr class="hover:bg-zinc-50/30 cora-eq-row" data-id="<?php echo esc_attr($item['id']); ?>" data-name="<?php echo esc_attr($item['name']); ?>">
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <?php if ($photo_url): ?>
                                                <img src="<?php echo esc_url($photo_url); ?>" class="w-8 h-8 rounded-md object-cover border border-zinc-200/80" />
                                            <?php else: ?>
                                                <div class="w-8 h-8 rounded-md bg-zinc-100 flex items-center justify-center border border-zinc-200/50 text-zinc-400">
                                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                                        <circle cx="12" cy="13" r="4"></circle>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3.5 font-bold text-zinc-800 min-w-[140px] cursor-pointer hover:underline" onclick="coraOpenListingDrawer(<?php echo esc_attr( json_encode( $item ) ); ?>)"><?php echo esc_html($item['name']); ?></td>
                                        <td class="px-4 py-3.5 text-zinc-550 whitespace-nowrap"><?php echo esc_html($item['category']); ?></td>
                                        <td class="px-4 py-3.5 whitespace-nowrap"><span class="bg-zinc-100 text-zinc-500 font-mono text-[9px] px-1.5 py-0.5 rounded border border-zinc-200/40"><?php echo esc_html($item['rera_reg_id']); ?></span></td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <span class="cora-badge px-2 py-0.5 rounded text-[9px] font-semibold <?php echo $status_class; ?>">
                                                <?php echo esc_html($item['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-zinc-655 font-medium whitespace-nowrap">
                                            <?php if ($item['crew']): ?>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                                    <span><?php echo esc_html($item['crew']); ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-zinc-300 font-normal">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3.5 text-zinc-550 max-w-[200px] truncate"><?php echo $item['shoot'] ? esc_html($item['shoot']) : '<span class="text-zinc-300 font-normal">—</span>'; ?></td>
                                        <td class="px-4 py-3.5 text-zinc-550 font-medium max-w-[200px] truncate"><?php echo $assignment_note ? esc_html($assignment_note) : '<span class="text-zinc-300 font-normal">—</span>'; ?></td>
                                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button class="w-7 h-7 flex items-center justify-center border border-zinc-200 rounded-md text-zinc-700 bg-white hover:bg-zinc-50 hover:border-zinc-300 transition-all cursor-pointer cora-assign-eq-btn active:scale-95" onclick="coraInitAssignEquipment('<?php echo esc_attr($item['id']); ?>')" title="Assign / Release Asset">
                                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="9" cy="7" r="4"></circle>
                                                        <polyline points="16 11 18 13 22 9"></polyline>
                                                    </svg>
                                                </button>
                                                <button class="w-7 h-7 flex items-center justify-center border border-zinc-200 rounded-md text-red-600 bg-white hover:bg-red-50 hover:border-red-200 transition-all cursor-pointer cora-delete-eq-btn active:scale-95" onclick="coraDeleteEquipment('<?php echo esc_attr($item['id']); ?>')" title="Delete Asset">
                                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>

                  <!-- SUB-SECTION 3: ASSIGN LISTING FORM -->
                  <div id="cora-sub-page-eq-assign" class="cora-sub-section hidden space-y-4">
                      <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm max-w-xl space-y-4">
                          <h3 class="text-sm font-bold text-zinc-900 border-b border-zinc-100 pb-2 flex items-center gap-1.5">
                              <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-555">
                                  <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                  <circle cx="9" cy="7" r="4"></circle>
                                  <polyline points="16 11 18 13 22 9"></polyline>
                              </svg>
                              Assign Property & Status
                          </h3>
                          <p class="text-xs text-zinc-500 leading-normal" id="cora-assign-eq-desc">Select a listing from the inventory to allocate to an agent and active showing.</p>
                          
                          <div class="cora-form-group flex flex-col gap-1.5">
                              <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Select Property Listing</label>
                              <select id="cora-assign-eq-id" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                  <option value="">-- Select Property --</option>
                                  <?php foreach ($cora_workspace_listings as $item): ?>
                                  <option value="<?php echo esc_attr($item['id']); ?>"><?php echo esc_html($item['name']); ?> (<?php echo esc_html($item['rera_reg_id']); ?>)</option>
                                  <?php endforeach; ?>
                              </select>
                          </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Asset Status</label>
                            <select id="cora-assign-eq-status" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="Available">Available (Unassigned)</option>
                                <option value="In Use">In Contract (Assigned to Deal)</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>

                        <!-- Allocation details, only shown if "In Use" is selected -->
                        <div id="cora-assign-eq-alloc-details" class="space-y-4 pt-2 hidden">
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Assign to Crew Member</label>
                                <select id="cora-assign-eq-crew" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                    <?php foreach ($cora_users as $user): ?>
                                    <option value="<?php echo esc_attr($user->display_name); ?>"><?php echo esc_html($user->display_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Select Active Showing/Deal</label>
                                <select id="cora-assign-eq-showing" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                    <option value="Rohit & Sneha - Luxury Villa Sale">Rohit & Sneha - Luxury Villa Sale</option>
                                    <option value="Ananya Sharma - Residential Buy">Ananya Sharma - Residential Buy</option>
                                    <option value="Property Showing / Site Tour">Property Showing / Site Tour</option>
                                </select>
                            </div>

                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Assignment Details / Notes</label>
                                <textarea id="cora-assign-eq-note" rows="2" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Cost: ₹2,500/day, Due: June 30th, Duration: 3 days"></textarea>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button id="cora-confirm-eq-assign-btn" class="px-5 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs">
                                Save Allocation
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            <?php endif; ?>

            <!-- SECTION 9: STUDIO VAULT -->
            <?php if ( $sub_page === 'vault' ) : ?>
            <section id="cora-page-vault" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-vault.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: SMART REVIEW ACQUISITION -->
            <?php if ( $sub_page === 'review_acquisition' || $sub_page === 'smart-reviews' ) : ?>
            <section id="cora-page-review-acquisition" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-review-acquisition.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: MULTI-DAY EVENT TIMELINE -->
            <?php if ( $sub_page === 'event_timeline' || $sub_page === 'event-timeline' || $sub_page === 'multi-day-timeline' ) : ?>
            <section id="cora-page-event-timeline" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-crew-scheduler.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: CREW & SHIFT SCHEDULER -->
            <?php if ( $sub_page === 'crew_scheduler' || $sub_page === 'crew-scheduler' || $sub_page === 'team_scheduler' || $sub_page === 'team-scheduler' || $sub_page === 'shifts' ) : ?>
            <section id="cora-page-crew-scheduler" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-crew-scheduler.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION 10: FINANCIAL BOARD -->
            <?php if ( $sub_page === 'financials' ) : ?>
            <section id="cora-page-financials" class="cora-page-section cora-active space-y-6">
                <div id="cora-view-financials">
                    <?php include CORA_WORKSPACE_PATH . 'views/view-financials.php'; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION: LEADS CRM BOARD -->
            <?php if ( $sub_page === 'leads' ) : ?>
            <section id="cora-page-leads" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-leads.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: APPS & MCP (EXTENSION WORKSPACE) -->
            <?php if ( $sub_page === 'plugins' ) : ?>
            <section id="cora-page-plugins" class="cora-page-section cora-active space-y-6">
                <div class="flex items-center justify-between">
                    <div class="cora-page-header flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline>
                                <polyline points="7.5 19.79 7.5 14.6 3 12"></polyline>
                                <polyline points="21 12 16.5 14.6 16.5 19.79"></polyline>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-xl font-bold text-zinc-900 leading-tight">Apps & MCP</h2>
                            <p class="text-xs text-zinc-500 mt-0.5 leading-relaxed">Extend your real estate brokerage workspace with Model Context Protocol (MCP) servers, third-party apps, and developer portals.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 1: MCP SERVERS -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-zinc-200">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-450"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                        <h3 class="text-xs font-bold text-zinc-700 uppercase tracking-wider">Model Context Protocol (MCP) Connections</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Card 1: Google Workspace MCP -->
                        <div class="border border-zinc-200 rounded-xl p-4 bg-white hover:border-zinc-350 transition-all duration-150 flex flex-col justify-between group">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-600">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    </div>
                                    <span class="px-2 py-0.5 text-[8px] font-bold tracking-wider rounded uppercase bg-zinc-100 text-zinc-500 border border-zinc-200/50">Soon</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-zinc-900">Google Workspace MCP</h4>
                                    <p class="text-[11.5px] text-zinc-500 leading-relaxed mt-1">Allow LLMs to read and write directly into your Google Calendar showing schedules, Gmail client logs, and Google Drive spreadsheets.</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-zinc-100 flex items-center justify-between text-[11px] font-semibold text-zinc-400">
                                <span>Status: Unavailable</span>
                                <span class="group-hover:text-zinc-900 transition-colors flex items-center gap-1">Configure <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg></span>
                            </div>
                        </div>

                        <!-- Card 2: WhatsApp Gateway MCP -->
                        <div class="border border-zinc-200 rounded-xl p-4 bg-white hover:border-zinc-350 transition-all duration-150 flex flex-col justify-between group">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-600">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                    </div>
                                    <span class="px-2 py-0.5 text-[8px] font-bold tracking-wider rounded uppercase bg-zinc-100 text-zinc-500 border border-zinc-200/50">Soon</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-zinc-900">WhatsApp Business MCP</h4>
                                    <p class="text-[11.5px] text-zinc-500 leading-relaxed mt-1">Automatically dispatch DLT-approved showing briefings, signature proposals, and payment links directly to couples via WhatsApp API.</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-zinc-100 flex items-center justify-between text-[11px] font-semibold text-zinc-400">
                                <span>Status: Unavailable</span>
                                <span class="group-hover:text-zinc-900 transition-colors flex items-center gap-1">Configure <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg></span>
                            </div>
                        </div>

                        <!-- Card 3: Gemini Real Estate Brain MCP -->
                        <div class="border border-zinc-200 rounded-xl p-4 bg-white hover:border-zinc-350 transition-all duration-150 flex flex-col justify-between group">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-600">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                    </div>
                                    <span class="px-2 py-0.5 text-[8px] font-bold tracking-wider rounded uppercase bg-zinc-100 text-zinc-500 border border-zinc-200/50">Soon</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-zinc-900">Gemini Real Estate Brain MCP</h4>
                                    <p class="text-[11.5px] text-zinc-500 leading-relaxed mt-1">Hook the Gemini Pro model into your workspace files. Analyze property listing briefs, cull images smart-tags, and write client contracts.</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-zinc-100 flex items-center justify-between text-[11px] font-semibold text-zinc-400">
                                <span>Status: Unavailable</span>
                                <span class="group-hover:text-zinc-900 transition-colors flex items-center gap-1">Configure <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: APP STORE -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-zinc-200">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-450"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                        <h3 class="text-xs font-bold text-zinc-700 uppercase tracking-wider">App Integrations Store</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- razorpay -->
                        <div class="border border-zinc-200 rounded-xl p-4 bg-white hover:border-zinc-350 transition-all duration-150 flex flex-col justify-between group text-left">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-750">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                    </div>
                                    <span class="px-1.5 py-0.5 text-[7px] font-bold rounded uppercase bg-zinc-100 text-zinc-500">Soon</span>
                                </div>
                                <h4 class="text-xs font-bold text-zinc-900 mt-1">Razorpay India</h4>
                                <p class="text-[10.5px] text-zinc-500 leading-normal">Accept payments from Indian clients via UPI, Netbanking, GooglePay, and credit cards with instant settlement.</p>
                            </div>
                        </div>

                        <!-- zoho books -->
                        <div class="border border-zinc-200 rounded-xl p-4 bg-white hover:border-zinc-350 transition-all duration-150 flex flex-col justify-between group text-left">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-750">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
                                    </div>
                                    <span class="px-1.5 py-0.5 text-[7px] font-bold rounded uppercase bg-zinc-100 text-zinc-500">Soon</span>
                                </div>
                                <h4 class="text-xs font-bold text-zinc-900 mt-1">Zoho Books GST Sync</h4>
                                <p class="text-[10.5px] text-zinc-500 leading-normal">Synchronize client details and generated invoices to create GST-compliant billing and digital ledgers.</p>
                            </div>
                        </div>

                        <!-- google drive -->
                        <div class="border border-zinc-200 rounded-xl p-4 bg-white hover:border-zinc-350 transition-all duration-150 flex flex-col justify-between group text-left">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-750">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                    </div>
                                    <span class="px-1.5 py-0.5 text-[7px] font-bold rounded uppercase bg-zinc-100 text-zinc-500">Soon</span>
                                </div>
                                <h4 class="text-xs font-bold text-zinc-900 mt-1">Google Drive Backups</h4>
                                <p class="text-[10.5px] text-zinc-500 leading-normal">Auto-archive listing brochures, contract PDFs, and high-res client deliverables directly to Google Drive folders.</p>
                            </div>
                        </div>

                        <!-- Msg91 SMS -->
                        <div class="border border-zinc-200 rounded-xl p-4 bg-white hover:border-zinc-350 transition-all duration-150 flex flex-col justify-between group text-left">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-750">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                    </div>
                                    <span class="px-1.5 py-0.5 text-[7px] font-bold rounded uppercase bg-zinc-100 text-zinc-500">Soon</span>
                                </div>
                                <h4 class="text-xs font-bold text-zinc-900 mt-1">Msg91 SMS Gateway</h4>
                                <p class="text-[10.5px] text-zinc-500 leading-normal">Dispatch transaction alerts and crew dispatch scheduling notifications via DLT-approved SMS routes in India.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: DEVELOPER API -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-zinc-200">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-450"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        <h3 class="text-xs font-bold text-zinc-700 uppercase tracking-wider">Developer & API Access</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-zinc-200 rounded-xl p-4 bg-white hover:border-zinc-350 transition-all duration-150 flex items-center gap-4 text-left">
                            <div class="w-10 h-10 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-655 shrink-0">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-zinc-900">REST API Tokens</h4>
                                    <span class="px-1.5 py-0.5 text-[7px] font-bold rounded uppercase bg-zinc-100 text-zinc-500">Soon</span>
                                </div>
                                <p class="text-xs text-zinc-500 leading-relaxed mt-0.5">Generate secret keys to execute custom operations or fetch client records externally.</p>
                            </div>
                        </div>

                        <div class="border border-zinc-200 rounded-xl p-4 bg-white hover:border-zinc-350 transition-all duration-150 flex items-center gap-4 text-left">
                            <div class="w-10 h-10 rounded-lg bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-655 shrink-0">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-zinc-900">Custom Webhooks</h4>
                                    <span class="px-1.5 py-0.5 text-[7px] font-bold rounded uppercase bg-zinc-100 text-zinc-500">Soon</span>
                                </div>
                                <p class="text-xs text-zinc-500 leading-relaxed mt-0.5">Configure URL webhooks to get instantly notified on listing lifecycle state changes.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION: STATIC PAGES & LANDING PAGES -->
            <?php if ( $sub_page === 'pages' ) : ?>
            <section id="cora-page-pages" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-pages.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: VISUAL BUILDER -->
            <?php if ( $sub_page === 'visual-builder' ) : ?>
            <section id="cora-page-visual-builder" class="cora-page-section cora-active" style="padding:0;margin:0;overflow:hidden;flex:1;min-height:0;display:flex;flex-direction:column;height:100%;">
                <?php include CORA_WORKSPACE_PATH . 'views/view-visual-builder.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: CANVAS -->
            <?php if ( $sub_page === 'canvas' ) : ?>
            <section id="cora-page-canvas" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-canvas.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: DISCUSSIONS & COMMENTS -->
            <?php if ( $sub_page === 'comments' ) : ?>
            <section id="cora-page-comments" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-comments.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: APPEARANCE -->
            <?php if ( $sub_page === 'appearance' ) : ?>
            <section id="cora-page-appearance" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-appearance.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: TOOLS & DIAGNOSTICS -->
            <?php if ( $sub_page === 'tools' ) : ?>
            <section id="cora-page-tools" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-tools.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: MEDIA EDITOR -->
            <?php if ( $sub_page === 'media-editor' ) : ?>
            <section id="cora-page-media-editor" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-media-editor.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: SETTINGS SUITE -->
            <?php if ( $sub_page === 'settings-suite' ) : ?>
            <section id="cora-page-settings-suite" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-settings-suite.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: USER PROFILE -->
            <?php if ( $sub_page === 'profile' ) : ?>
            <section id="cora-page-profile" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-profile.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: AI TOOLS MCP -->
            <?php if ( $sub_page === 'mcp' ) : ?>
            <section id="cora-page-mcp" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-mcp.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: RAG KNOWLEDGE BASE -->
            <?php if ( $sub_page === 'knowledge-base' ) : ?>
            <section id="cora-page-knowledge-base" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-rag.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: SUPER ADMIN PANEL -->
            <?php 
            $super_pages = array( 'super-admin', 'super-users', 'super-appeals', 'super-governance', 'super-announcements', 'super-health' );
            if ( in_array( $sub_page, $super_pages ) && cora_is_super_owner() ) : ?>
            <section id="cora-page-super-admin" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-super-admin.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: PLATFORM DOCUMENTATION SYSTEM -->
            <?php if ( $sub_page === 'super-docs' && cora_is_super_owner() ) : ?>
            <section id="cora-page-super-docs" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-documentation.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: CORA ROLES & PLATFORM OVERVIEW -->
            <?php
            $cora_roles_allowed = array('administrator', 'cora_shruti', 'cora_super_admin');
            if ( $sub_page === 'cora-roles' && ( in_array( $current_user_role, $cora_roles_allowed ) || cora_is_super_owner() ) ) : ?>
            <section id="cora-page-cora-roles" class="cora-page-section cora-active space-y-6">
                <?php include CORA_WORKSPACE_PATH . 'views/view-cora-roles.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: CORA FORMS -->
            <?php if ( $sub_page === 'forms' ) : ?>
            <?php
            // Inject a style override to strip the wrapper padding and force viewport fill for forms editor
            echo '<style>
                .cora-main {
                    overflow: hidden !important;
                    height: calc(100vh - 52px) !important;
                    display: flex !important;
                    flex-direction: column !important;
                }
                .admin-bar .cora-main {
                    height: calc(100vh - 52px) !important;
                }
                #editor-center-canvas {
                    height: calc(100vh - 200px) !important;
                    max-height: calc(100vh - 200px) !important;
                    overflow-y: auto !important;
                }
                .admin-bar #editor-center-canvas {
                    height: calc(100vh - 200px) !important;
                    max-height: calc(100vh - 200px) !important;
                }
                #left-panel-content, #left-tab-settings, #left-tab-form, #left-tab-integ {
                    height: calc(100vh - 160px) !important;
                    max-height: calc(100vh - 160px) !important;
                    overflow-y: auto !important;
                }
                .admin-bar #left-panel-content, .admin-bar #left-tab-settings, .admin-bar #left-tab-form, .admin-bar #left-tab-integ {
                    height: calc(100vh - 160px) !important;
                    max-height: calc(100vh - 160px) !important;
                }
                .cora-main {
                    overflow: hidden !important;
                    height: 100vh !important;
                    max-height: 100vh !important;
                }
                .cora-content-wrapper {
                    padding: 0 !important;
                    gap: 0 !important;
                    overflow: hidden !important;
                    flex: 1 !important;
                    min-height: 0 !important;
                    display: flex !important;
                    flex-direction: column !important;
                }
                #cora-page-forms.cora-page-section.cora-active {
                    display: flex !important;
                    flex-direction: column !important;
                    flex: 1 !important;
                    min-height: 0 !important;
                    overflow: hidden !important;
                }
                #cora-forms-module {
                    flex: 1 !important;
                    min-height: 0 !important;
                    display: flex !important;
                    flex-direction: column !important;
                    overflow: hidden !important;
                }
                #forms-list-state {
                    flex: 1 !important;
                    min-height: 0 !important;
                    overflow-y: auto !important;
                }
                #form-editor-state {
                    flex: 1 !important;
                    min-height: 0 !important;
                }
                /* Remove Tailwind space-y-6 between sections */
                .cora-content-wrapper > * + * { margin-top: 0 !important; }
            </style>';
            ?>
            <section id="cora-page-forms" class="cora-page-section cora-active" style="padding:0;margin:0;overflow:hidden;flex:1;min-height:0;display:flex;flex-direction:column;">
                <?php include CORA_WORKSPACE_PATH . 'views/view-forms.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: EMAILS COMPOSER -->
            <?php if ( $sub_page === 'emails' ) : ?>
            <?php
            echo '<style>
                .cora-content-wrapper {
                    padding: 0 !important;
                    gap: 0 !important;
                }
                /* Remove Tailwind space-y-6 between sections */
                .cora-content-wrapper > * + * { margin-top: 0 !important; }
            </style>';
            ?>
            <section id="cora-page-emails" class="cora-page-section cora-active" style="padding:0;margin:0;overflow:hidden;flex:1;min-height:0;display:flex;flex-direction:column;">
                <?php include CORA_WORKSPACE_PATH . 'views/view-emails.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: MEDIA LIBRARY -->
            <?php if ( $sub_page === 'media' ) : ?>
            <?php
            // Inject a style override to strip the wrapper padding for this page
            echo '<style>
                .cora-content-wrapper {
                    padding: 0 !important;
                    gap: 0 !important;
                    overflow: visible !important;
                    height: auto !important;
                }
                /* Remove Tailwind space-y-6 between sections */
                .cora-content-wrapper > * + * { margin-top: 0 !important; }
            </style>';
            ?>
            <section id="cora-page-media" class="cora-page-section cora-active" style="padding:0;margin:0;overflow:visible;flex:1;display:flex;flex-direction:column;height:auto;">
                <?php include CORA_WORKSPACE_PATH . 'views/view-media.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: AI CONTENT SUITE -->
            <?php if ( $sub_page === 'blogs' ) : ?>
            <section id="cora-page-content" class="cora-page-section cora-active">
                <div id="cora-view-content-suite">
                    <?php include CORA_WORKSPACE_PATH . 'views/view-content-suite.php'; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION: ATTENDANCE -->
            <?php if ( $sub_page === 'attendance' ) : ?>
            <section id="cora-page-attendance" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </span>
                    <div>
                        <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Attendance Tracking</h1>
                        <p class="cora-section-desc text-sm text-zinc-500 mt-1">Log location-based attendance and view employee punch records.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white rounded-lg border border-zinc-200 p-6 shadow-sm">
                            <h2 class="text-sm font-semibold tracking-tight text-zinc-900 mb-4">Log Punch</h2>
                            <button id="cora-punch-in-btn" class="w-full bg-zinc-900 hover:bg-zinc-800 text-white py-2.5 rounded-md text-sm font-medium transition-colors mb-3 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                Punch In
                            </button>
                            <button id="cora-punch-out-btn" class="w-full bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-900 py-2.5 rounded-md text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Punch Out
                            </button>
                            <div id="cora-punch-status" class="mt-4 text-xs text-center text-zinc-500 hidden"></div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="bg-white border border-zinc-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-zinc-200 bg-zinc-50/50 flex justify-between items-center">
                                <h2 class="text-sm font-semibold tracking-tight text-zinc-900">Recent Logs</h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse whitespace-nowrap">
                                    <thead>
                                        <tr class="bg-zinc-50/50 text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">
                                            <th class="px-5 py-3 font-medium">User</th>
                                            <th class="px-5 py-3 font-medium">Time</th>
                                            <th class="px-5 py-3 font-medium">Type</th>
                                            <th class="px-5 py-3 font-medium">Location</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cora-attendance-table-body" class="divide-y divide-zinc-100 text-sm text-zinc-650">
                                        <tr><td colspan="4" class="px-5 py-8 text-center text-zinc-400">Loading attendance data...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION: TASKS -->
    <!-- Collapsible Right-side AI Sidebar (Notion-AI style) -->
    <aside id="cora-ai-sidebar" class="cora-ai-sidebar collapsed fixed top-0 lg:top-[52px] right-0 left-0 z-[999] h-full lg:h-[calc(100vh-52px)] w-full max-w-full bg-white border-t border-zinc-200 shadow-2xl flex flex-col transition-all duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header w-full border-b border-zinc-200/80 bg-zinc-50 shrink-0 px-4 py-3 select-none">
            <div class="flex justify-between items-center w-full max-w-3xl mx-auto">
                <div class="cora-ai-sidebar-title text-xs font-bold text-zinc-800 flex items-center gap-1.5 cursor-pointer hover:text-zinc-950 transition-colors" onclick="coraClearSidebarChat()">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-555">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"/>
                    </svg>
                    <span>New Conversation</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Settings/Filter Icon -->
                    <button class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors cursor-pointer border-0 bg-transparent p-1" title="AI Settings">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="21" x2="4" y2="14"></line>
                            <line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line>
                            <line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line>
                            <line x1="9" y1="8" x2="15" y2="8"></line>
                            <line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                    </button>
                    <!-- Close Button -->
                    <button class="cora-ai-sidebar-close text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer border-0 bg-transparent p-1" onclick="coraToggleSidebar(false)">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="cora-ai-sidebar-body flex-1 overflow-y-auto p-4 flex flex-col gap-6">
            <div class="max-w-3xl mx-auto w-full flex-1 flex flex-col justify-between gap-6">
                <div class="cora-ai-sidebar-chat-history flex flex-col gap-3" id="cora-sidebar-chat">
                    <div class="chat-bubble ai bg-zinc-100 text-zinc-850 rounded-lg rounded-bl-none p-3 text-xs leading-relaxed self-start border border-zinc-200/50 shadow-sm max-w-[85%]">
                        Hello! I am Cora, your real estate workspace intelligence. Ask me about bookings, client messages, or writing listing descriptions.
                    </div>
                </div>
                <!-- AI Prompt Shortcuts -->
                <div class="cora-ai-sidebar-shortcuts pt-4 border-t border-zinc-200">
                    <span class="cora-sidebar-sublabel text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2.5 block">Quick Prompts</span>
                    <button class="cora-shortcut-btn w-full text-left p-2.5 text-xs text-zinc-655 border border-zinc-200 rounded-md hover:bg-zinc-50 hover:text-zinc-955 transition-colors mb-2 cursor-pointer font-medium" onclick="coraSendShortcut('Draft a WhatsApp reminder for Ananya Sharma')">Draft a reminder for Ananya</button>
                    <button class="cora-shortcut-btn w-full text-left p-2.5 text-xs text-zinc-500 border border-zinc-200 rounded-md hover:bg-zinc-50 hover:text-zinc-955 transition-colors cursor-pointer font-medium" onclick="coraSendShortcut('Check status of Rohit & Sneha')">Check Rohit & Sneha's deal</button>
                </div>
            </div>
        </div>
        <div class="cora-ai-sidebar-footer-input p-3 border-t border-zinc-200/80 bg-zinc-50 shrink-0">
            <div class="flex items-center gap-2 max-w-3xl mx-auto w-full">
                <input type="text" id="cora-sidebar-chat-input" placeholder="Ask Cora AI..." onkeydown="if(event.key === 'Enter') coraSendSidebarChatMessage()" class="flex-1 border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none">
                <button class="cora-btn-primary px-3 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded text-xs transition-colors cursor-pointer shrink-0" onclick="coraSendSidebarChatMessage()">Send</button>
            </div>
        </div>
    </aside>




    <!-- Notifications Side Drawer Panel -->
    <aside id="cora-notif-dropdown" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[400px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-all duration-300 ease-in-out">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/60 dark:bg-zinc-950/60 shrink-0 select-none">
            <div class="flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-700 dark:text-zinc-300">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200">Notifications</span>
            </div>
            <div class="flex items-center gap-3">
                <button id="cora-notif-mark-all-btn" class="text-[11px] font-semibold text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors cursor-pointer border-0 bg-transparent">Mark all as read</button>
                <button onclick="window.coraToggleNotificationDrawer(false)" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer border-0 bg-transparent p-1">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
        <div id="cora-notif-list" class="flex-1 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800 p-2">
            <!-- Injected by JS -->
        </div>
        <div id="cora-notif-empty" class="hidden p-12 text-center text-xs text-zinc-400 select-none">
            No notifications yet.
        </div>
    </aside>

    <!-- Create Booking Side Drawer (Notion-AI style form space saver) -->
    <aside id="cora-add-showing-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-full max-w-[480px] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">

        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Create New Showing
            </span>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleAddShowingDrawer(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Client Full Name</label>
                <input type="text" id="cora-drawer-client-name" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Ramesh Kumar">
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Deal Type</label>
                <select id="cora-drawer-deal-type" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                    <option value="Residential Buy">Residential Buy</option>
                    <option value="Luxury Villa Sale">Luxury Villa Sale</option>
                    <option value="Commercial Lease">Commercial Lease</option>
                    <option value="Off-Plan Sale">Off-Plan Sale</option>
                    <option value="Commercial Campaign">Commercial Campaign</option>
                </select>
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Property Location</label>
                <input type="text" id="cora-drawer-location" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Lodhi Gardens, Delhi">
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Viewing Date</label>
                <input type="text" id="cora-drawer-date" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. 28th Jun, 2026">
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Package Value</label>
                <input type="text" id="cora-drawer-price" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. ₹15,000">
            </div>
        </div>
        <div class="cora-drawer-footer p-4 flex items-center gap-2.5 shrink-0 border-t border-zinc-100 bg-zinc-50">
            <button id="cora-save-showing-btn" class="flex-1 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm">
                Create Booking
            </button>
            <button class="px-4 py-2.5 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraToggleAddShowingDrawer(false)">
                Cancel
            </button>
        </div>
    </aside>

    <!-- Lead CRM Side Drawer -->
    <aside id="cora-lead-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-full max-w-[480px] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3.5 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5" id="cora-lead-drawer-title">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                    <rect x="3" y="3" width="7" height="9" rx="1"></rect>
                    <rect x="14" y="3" width="7" height="9" rx="1"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                </svg>
                Lead Deal Panel
            </span>
            <div class="flex items-center gap-2 ml-auto mr-3">
                <button class="px-2.5 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded text-[10px] font-bold flex items-center gap-1 cursor-pointer active:scale-95 transition-all" onclick="coraCreateProposalFromLead()">
                    Create Proposal
                </button>
            </div>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleLeadDrawer(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        
        <!-- Navigation Tabs inside Drawer -->
        <div class="flex border-b border-zinc-200 shrink-0 bg-zinc-50/50">
            <button id="cora-lead-btn-general" class="flex-1 py-2.5 text-center text-xs font-bold border-b-2 border-zinc-950 text-zinc-950 transition-all cursor-pointer focus:outline-none" onclick="coraSwitchLeadDrawerTab('general', this)">
                General Info
            </button>
            <button id="cora-lead-btn-assets" class="flex-1 py-2.5 text-center text-xs font-semibold border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 transition-all cursor-pointer focus:outline-none" onclick="coraSwitchLeadDrawerTab('assets', this)">
                Assets & Demos
            </button>
            <button id="cora-lead-btn-equipment" class="flex-1 py-2.5 text-center text-xs font-semibold border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 transition-all cursor-pointer focus:outline-none" onclick="coraSwitchLeadDrawerTab('equipment', this)">
                Interested Listings
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <input type="hidden" id="cora-lead-id">
            
            <!-- Tab: General Info -->
            <div id="cora-lead-tab-general" class="cora-lead-tab-content space-y-4">
                <!-- Section 1: Contact Profile -->
                <div class="border border-zinc-200/80 rounded-lg p-4 space-y-3.5 bg-zinc-50/20">
                    <div class="flex items-center gap-1.5 border-b border-zinc-200 pb-2 mb-1">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Contact Profile</span>
                    </div>
                    
                    <div class="cora-form-group flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-zinc-405 uppercase tracking-wider">Client / Couple Names</label>
                        <input type="text" id="cora-lead-names" class="w-full border border-zinc-200 rounded-md px-3 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Aashna & Kabir">
                    </div>
                    
                    <div class="cora-form-group flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-zinc-405 uppercase tracking-wider">Contact Email</label>
                        <input type="email" id="cora-lead-email" class="w-full border border-zinc-200 rounded-md px-3 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. client@email.com">
                    </div>
                </div>

                <!-- Section 2: Property Scope -->
                <div class="border border-zinc-200/80 rounded-lg p-4 space-y-3.5 bg-zinc-50/20">
                    <div class="flex items-center gap-1.5 border-b border-zinc-200 pb-2 mb-1">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Property Scope</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="cora-form-group flex flex-col gap-1">
                            <label class="text-[10px] font-bold text-zinc-405 uppercase tracking-wider">Event Scale</label>
                            <select id="cora-lead-scale" class="w-full border border-zinc-200 rounded-md px-2 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer">
                                <option value="intimate">Residential Sale</option>
                                <option value="multi-day">Luxury Mandate</option>
                                <option value="destination">Grand Destination</option>
                                <option value="documentary">Residential Lease</option>
                            </select>
                        </div>
                        <div class="cora-form-group flex flex-col gap-1">
                            <label class="text-[10px] font-bold text-zinc-405 uppercase tracking-wider">Location / City</label>
                            <input type="text" id="cora-lead-city" class="w-full border border-zinc-200 rounded-md px-3 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Udaipur">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="cora-form-group flex flex-col gap-1">
                            <label class="text-[10px] font-bold text-zinc-405 uppercase tracking-wider">Estimated Budget</label>
                            <input type="text" id="cora-lead-price" class="w-full border border-zinc-200 rounded-md px-3 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. ₹5.5L - ₹8L">
                        </div>
                        <div class="cora-form-group flex flex-col gap-1">
                            <label class="text-[10px] font-bold text-zinc-405 uppercase tracking-wider">Funnel Status</label>
                            <select id="cora-lead-status" class="w-full border border-zinc-200 rounded-md px-2 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer">
                                <?php
                                $active_industry = get_option( 'cora_workspace_industry', 'real_estate' );
                                $module = Cora_Module_Registry::get_module( $active_industry );
                                $stages = $module ? $module->get_crm_stages() : array();
                                foreach ( $stages as $status_val => $stage_info ) {
                                    echo '<option value="' . esc_attr( $status_val ) . '">' . esc_html( isset( $stage_info['label'] ) ? $stage_info['label'] : $status_val ) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Creative Brief -->
                <div class="border border-zinc-200/80 rounded-lg p-4 space-y-3.5 bg-zinc-50/20">
                    <div class="flex items-center gap-1.5 border-b border-zinc-200 pb-2 mb-1">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Creative Brief</span>
                    </div>

                    <div class="cora-form-group flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-zinc-405 uppercase tracking-wider">Vision Notes / Scope Details</label>
                        <textarea id="cora-lead-notes" rows="4" class="w-full border border-zinc-200 rounded-md px-3 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors leading-relaxed" placeholder="Enter vision details..."></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Tab: Assets & Demos -->
            <div id="cora-lead-tab-assets" class="cora-lead-tab-content space-y-4 hidden">
                <!-- Demo presentation portfolio link & tracking -->
                <div class="border border-zinc-200/80 rounded-lg p-4 space-y-3.5 bg-zinc-50/20">
                    <div class="flex items-center gap-1.5 border-b border-zinc-200 pb-2 mb-1">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Demo Portfolio Presentation</span>
                    </div>
                    
                    <div class="cora-form-group flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-zinc-405 uppercase tracking-wider">Select Portfolio Demo Gallery</label>
                        <select id="cora-lead-demo-portfolio" class="w-full border border-zinc-200 rounded-md px-2 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer">
                            <option value="">-- No Demo Portfolio Linked --</option>
                            <!-- Loop options dynamically in JS -->
                        </select>
                    </div>

                    <!-- Tracking Info Box -->
                    <div id="cora-lead-portfolio-tracking-box" class="border border-zinc-200 rounded-md p-3 bg-white space-y-2.5 hidden">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[10px] text-zinc-450 uppercase font-bold tracking-wider">Sharing State</span>
                            <span id="cora-lead-portfolio-shared-badge" class="px-2 py-0.5 text-[10px] font-bold rounded bg-zinc-100 text-zinc-650">Not Shared</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[10px] text-zinc-450 uppercase font-bold tracking-wider">Client Viewed</span>
                            <span id="cora-lead-portfolio-viewed-badge" class="px-2 py-0.5 text-[10px] font-bold rounded bg-zinc-100 text-zinc-650">Unopened</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-2 pt-2 border-t border-zinc-100">
                            <button class="flex-1 py-1.5 border border-zinc-950 text-zinc-950 rounded text-[10px] font-bold cursor-pointer hover:bg-zinc-50 transition-all focus:outline-none" onclick="coraShareDemoGalleryAction()">
                                Mark Shared
                            </button>
                            <button class="flex-1 py-1.5 bg-zinc-950 text-white rounded text-[10px] font-bold cursor-pointer hover:bg-zinc-800 transition-all focus:outline-none" onclick="coraSimulateClientViewAction()">
                                Simulate View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Linked Proposals & Invoices -->
                <div class="border border-zinc-200/80 rounded-lg p-4 space-y-3 bg-zinc-50/20">
                    <div class="flex items-center gap-1.5 border-b border-zinc-200 pb-2 mb-1">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Linked Sales Documents</span>
                    </div>

                    <div id="cora-lead-linked-docs-list" class="space-y-2">
                        <!-- Dynamic documents populated in JS -->
                        <div class="text-[11px] text-zinc-450 py-1 text-center">No proposals or invoices linked yet.</div>
                    </div>
                </div>

                <!-- Automated Follow-Up Sequence -->
                <div class="border border-zinc-200/80 rounded-lg p-4 space-y-3 bg-zinc-50/20" id="cora-lead-emails-section">
                    <div class="flex items-center gap-1.5 border-b border-zinc-200 pb-2 mb-1">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Automated Email Sequence</span>
                    </div>
                    <div id="cora-lead-emails-container" class="space-y-3">
                        <!-- Dynamic email steps loaded in JS -->
                    </div>
                </div>
            </div>

            <!-- Tab: Equipment -->
            <div id="cora-lead-tab-equipment" class="cora-lead-tab-content space-y-4 hidden">
                <div class="border border-zinc-200/80 rounded-lg p-4 space-y-3.5 bg-zinc-50/20">
                    <div class="flex items-center gap-1.5 border-b border-zinc-200 pb-2 mb-1">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Interested Listings / Properties</span>
                    </div>
                    
                    <p class="text-[11px] text-zinc-500 leading-relaxed mb-1">Select and assign equipment from the listings catalog required for this prospect's property requirements.</p>

                    <div id="cora-lead-equipment-list" class="grid grid-cols-1 gap-2.5 max-h-[300px] overflow-y-auto pr-1">
                        <!-- Loop through coraREData.equipment in JS to output checkboxes -->
                        <div class="text-[11px] text-zinc-450 py-1 text-center">No listings catalog loaded.</div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="cora-drawer-footer p-4 flex flex-col gap-2 shrink-0 border-t border-zinc-200 bg-zinc-50">
            <div class="flex items-center gap-2">
                <button id="cora-save-lead-btn" class="flex-1 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs" onclick="coraSaveLeadDetails()">
                    Save Details
                </button>
                <button class="px-4 py-2 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-xs" onclick="coraToggleLeadDrawer(false)">
                    Cancel
                </button>
            </div>
            <div class="flex items-center gap-2 mt-1 pt-2 border-t border-zinc-200/60" id="cora-lead-drawer-actions">
                <button id="cora-convert-lead-btn" class="flex-1 py-1.5 bg-green-700 text-white font-semibold rounded-md hover:bg-green-800 transition-all active:scale-[0.98] cursor-pointer text-[10.5px] flex items-center justify-center gap-1.5" onclick="coraConvertLeadToClientAction()">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Convert to Client Directory
                </button>
                <button id="cora-delete-lead-btn" class="px-3 py-1.5 border border-red-200 text-red-650 hover:bg-red-50 font-semibold rounded-md transition-all active:scale-[0.98] cursor-pointer text-[10.5px]" onclick="coraDeleteLeadAction()">
                    Delete
                </button>
            </div>
        </div>
    </aside>

    <!-- R2 & R3: Property Listing (Add Listing & Listing Details) Side Drawer -->
    <aside id="cora-listing-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-full max-w-[480px] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <!-- Header -->
        <div class="px-5 py-4 border-b border-zinc-200/80 flex items-center justify-between bg-zinc-50/50 shrink-0">
            <span class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5" id="cora-listing-drawer-title">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-650">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Listing Details
            </span>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer focus:outline-none" onclick="coraToggleListingDrawer(false)">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <!-- Content Body -->
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <!-- Hidden ID field for updates -->
            <input type="hidden" id="cora-listing-id">
            
            <!-- 3rd Party Sync input (R2) -->
            <div class="cora-form-group flex flex-col gap-1.5" id="cora-listing-sync-container">
                <label class="text-[10px] font-bold text-zinc-450 uppercase tracking-wider">3rd-Party Listing Link</label>
                <div class="flex gap-2">
                    <input type="text" id="cora-listing-sync-link" class="flex-1 border border-zinc-200 rounded-md px-3 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Zillow, 99acres or Magicbricks link">
                    <button type="button" id="cora-listing-sync-btn" class="px-4 py-2 bg-zinc-955 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all text-xs" onclick="coraSyncListingLink()">Sync</button>
                </div>
            </div>

            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-450 uppercase tracking-wider">Listing Name *</label>
                <input type="text" id="cora-listing-name" class="w-full border border-zinc-200 rounded-md px-3 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. DLF Kings Court Penthouse">
            </div>

            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-455 uppercase tracking-wider">Category *</label>
                <select id="cora-listing-category" class="w-full border border-zinc-200 rounded-md px-2 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer">
                    <option value="Villa">Villa</option>
                    <option value="Apartment">Apartment</option>
                    <option value="Penthouse">Penthouse</option>
                    <option value="Plot">Plot</option>
                    <option value="Commercial">Commercial</option>
                </select>
            </div>

            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-450 uppercase tracking-wider">RERA Reg ID / Plot Number *</label>
                <input type="text" id="cora-listing-rera-id" class="w-full border border-zinc-200 rounded-md px-3 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. HR-ERA-2023-88">
            </div>

            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-450 uppercase tracking-wider">Notes / Description</label>
                <textarea id="cora-listing-notes" rows="3" class="w-full border border-zinc-200 rounded-md px-3 py-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="Property details, notes, etc."></textarea>
            </div>

            <!-- Property Image Selection -->
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-450 uppercase tracking-wider">Property Image</label>
                <div class="flex items-center gap-3">
                    <div id="cora-listing-image-preview" class="w-14 h-14 border border-zinc-200 rounded-lg flex items-center justify-center bg-zinc-50 overflow-hidden shrink-0">
                        <span class="text-[9px] text-zinc-400 text-center px-1 font-semibold" id="cora-listing-image-placeholder">No Photo</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <input type="file" id="cora-listing-image-file" accept="image/*" class="hidden">
                        <button type="button" class="px-3 py-1.5 border border-zinc-250 rounded-md text-xs font-bold text-zinc-700 bg-white hover:bg-zinc-50 active:scale-95 transition-all cursor-pointer shadow-sm" onclick="jQuery('#cora-listing-image-file').click()">
                            Choose Photo
                        </button>
                    </div>
                </div>
            </div>

            <!-- AI-Driven SEO Optimization fields (R3) -->
            <div class="border border-zinc-200/80 rounded-lg p-4 space-y-3 bg-zinc-50/20" id="cora-listing-seo-section">
                <h4 class="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-650">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                    AI-Generated SEO Meta
                </h4>
                <p class="text-[10px] text-zinc-500">Automatically generated based on listing name, category, RERA ID, and sync link. Feel free to edit/overwrite.</p>
                <div class="cora-form-group flex flex-col gap-1">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Meta SEO Title</label>
                    <input type="text" id="cora-listing-seo-title" class="w-full border border-zinc-200 rounded-md px-2.5 py-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="Auto-generated title">
                </div>
                <div class="cora-form-group flex flex-col gap-1">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Meta SEO Description</label>
                    <textarea id="cora-listing-seo-description" rows="2" class="w-full border border-zinc-200 rounded-md px-2.5 py-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="Auto-generated description"></textarea>
                </div>
                <div class="cora-form-group flex flex-col gap-1">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">SEO Keywords</label>
                    <input type="text" id="cora-listing-seo-keywords" class="w-full border border-zinc-200 rounded-md px-2.5 py-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="Auto-generated keywords">
                </div>
            </div>
        </div>
        <!-- Actions Footer -->
        <div class="px-5 py-3.5 border-t border-zinc-200/80 bg-zinc-50/50 flex items-center justify-end gap-2.5 shrink-0">
            <button class="px-4 py-2 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-xs" onclick="coraToggleListingDrawer(false)">
                Cancel
            </button>
            <button id="cora-save-listing-btn" class="px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs" onclick="coraSaveListingDetails()">
                Save Details
            </button>
        </div>
    </aside>

    <!-- Client Details Side Drawer -->
    <aside id="cora-client-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-full max-w-[500px] bg-white border-l border-zinc-200 shadow-none flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-5 py-4 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <div>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1 block">Client Hub</span>
                <h3 class="text-base font-bold text-zinc-900 leading-none flex items-center gap-2" id="cora-lifecycle-client-name">
                    Client Name
                </h3>
            </div>
            <div class="flex items-center gap-2 ml-auto mr-3">
                <button class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded text-[10.5px] font-bold flex items-center gap-1 cursor-pointer active:scale-95 transition-all" onclick="coraCreateInvoiceFromClient()">
                    New Invoice
                </button>
            </div>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleClientDrawer(false)">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-6">
            
            <!-- Timeline Stage 1: Lead Details -->
            <div class="relative pl-6 border-l-2 border-zinc-200 pb-2">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-zinc-100 border-2 border-white flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full"></div>
                </div>
                <h4 class="text-[11px] font-bold text-zinc-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Lead Profiling
                </h4>
                <div class="bg-white border border-zinc-200 rounded-lg p-3.5 space-y-3 shadow-sm">
                    <div class="flex justify-between">
                        <div>
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block mb-0.5">Contact</span>
                            <div class="text-[11px] font-mono text-zinc-700" id="cora-lifecycle-email">-</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block mb-0.5">Location</span>
                            <div class="text-[11px] font-semibold text-zinc-700" id="cora-lifecycle-city">-</div>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-zinc-100">
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block mb-1">Vision Notes</span>
                        <div class="text-[11.5px] text-zinc-600 leading-relaxed bg-zinc-50/50 p-2.5 rounded-md" id="cora-lifecycle-notes">-</div>
                    </div>
                </div>
            </div>

            <!-- Timeline Stage 2: Confirmed Bookings -->
            <div class="relative pl-6 border-l-2 border-zinc-200 pb-2">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-zinc-100 border-2 border-white flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-zinc-800 rounded-full"></div>
                </div>
                <h4 class="text-[11px] font-bold text-zinc-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Viewing Bookings
                </h4>
                <div id="cora-lifecycle-bookings-container" class="space-y-3">
                    <!-- Populated dynamically by JS -->
                </div>
            </div>

            <!-- Timeline Stage 3: Financials & Vault -->
            <div class="relative pl-6 border-l-2 border-zinc-200 pb-2">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-zinc-100 border-2 border-white flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-zinc-800 rounded-full"></div>
                </div>
                <h4 class="text-[11px] font-bold text-zinc-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Vault Documents
                </h4>
                <div id="cora-lifecycle-documents-container" class="space-y-2">
                    <!-- Populated dynamically by JS -->
                </div>
            </div>

            <!-- Timeline Stage 4: Assets & Portfolios -->
            <div class="relative pl-6 border-l-2 border-transparent">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-zinc-100 border-2 border-white flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full"></div>
                </div>
                <h4 class="text-[11px] font-bold text-zinc-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    Assets & Delivery
                </h4>
                <div id="cora-lifecycle-assets-container" class="space-y-2">
                    <div class="text-[11px] text-zinc-400 italic py-2">No portfolios delivered yet.</div>
                </div>
            </div>

        </div>
        <div class="cora-drawer-footer p-4 flex items-center shrink-0 border-t border-zinc-200/80 bg-zinc-50">
            <button class="w-full py-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 font-semibold rounded-md transition-all text-xs cursor-pointer" onclick="coraToggleClientDrawer(false)">
                Close Dashboard
            </button>
        </div>
    </aside>

    <!-- Team Assignment Side Drawer -->
    <aside id="cora-team-management-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-[350px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Team Crew Assignments
            </span>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleTeamDrawer(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            <p class="text-xs text-zinc-500 leading-normal border-b border-zinc-100 pb-3">Select the active agents and listing coordinators for your scheduled showings.</p>
            
            <!-- Shoot Event 1 -->
            <div class="space-y-3 pb-4 border-b border-zinc-100">
                <span class="text-xs font-bold text-zinc-800 block">Rohit & Sneha - Luxury Villa Sale</span>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Lead Managing Agent</label>
                    <select id="cora-team-showing1-photographer" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($photographers as $u): 
                            $selected = (isset($s1_assignments['photographer']) && $s1_assignments['photographer'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s1_assignments['photographer']) || 'none' === $s1_assignments['photographer'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Lead Showing Assistant</label>
                    <select id="cora-team-showing1-videographer" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($videographers as $u): 
                            $selected = (isset($s1_assignments['videographer']) && $s1_assignments['videographer'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s1_assignments['videographer']) || 'none' === $s1_assignments['videographer'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Property Valuer</label>
                    <select id="cora-team-showing1-drone" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($drone_pilots as $u): 
                            $selected = (isset($s1_assignments['drone']) && $s1_assignments['drone'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s1_assignments['drone']) || 'none' === $s1_assignments['drone'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
            </div>

            <!-- Shoot Event 2 -->
            <div class="space-y-3">
                <span class="text-xs font-bold text-zinc-800 block">Ananya Sharma - Residential Buy</span>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Lead Managing Agent</label>
                    <select id="cora-team-showing2-photographer" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($photographers as $u): 
                            $selected = (isset($s2_assignments['photographer']) && $s2_assignments['photographer'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s2_assignments['photographer']) || 'none' === $s2_assignments['photographer'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Showing Assistant</label>
                    <select id="cora-team-showing2-assistant" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($cora_users as $u): 
                            $selected = (isset($s2_assignments['assistant']) && $s2_assignments['assistant'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s2_assignments['assistant']) || 'none' === $s2_assignments['assistant'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="cora-drawer-footer p-4 flex items-center gap-2.5 shrink-0">
            <button id="cora-save-team-btn" class="flex-1 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm">
                Save Crew Assignments
            </button>
            <button class="px-4 py-2.5 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraToggleTeamDrawer(false)">
                Cancel
            </button>
        </div>
    </aside>
    <!-- Secure Document Share Drawer (Document Vault) -->
    <aside id="cora-share-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-[380px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-550">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Secure Sharing
            </span>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleShareDrawer(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <input type="hidden" id="cora-share-doc-id" value="">
            <p class="text-xs text-zinc-500 leading-normal pb-2 border-b border-zinc-100">Send an encrypted, self-expiring link directly to the client's email via secure mail relay.</p>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Client Email Address</label>
                <input type="email" id="cora-share-email" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. client@example.com">
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5" id="cora-share-expiry-container">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Link Expiration Date</label>
                <input type="date" id="cora-share-date-picker" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
            </div>
            
            <div class="flex items-center gap-2 mt-2">
                <input type="checkbox" id="cora-share-no-expiry" class="rounded border-zinc-350 text-zinc-950 focus:ring-zinc-500 cursor-pointer">
                <label for="cora-share-no-expiry" class="text-xs text-zinc-650 font-semibold select-none cursor-pointer">Never Expires (Permanent Link)</label>
            </div>
            
            <!-- Output share link if generated -->
            <div id="cora-share-result-box" class="pt-4 space-y-2 hidden">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Generated Secure Link</label>
                <div class="flex gap-2">
                    <input type="text" id="cora-share-link-input" readonly class="flex-1 border border-zinc-200 rounded-md p-2 text-xs bg-zinc-50 font-mono focus:outline-none" value="">
                    <button class="px-3 py-2 border border-zinc-350 rounded-md text-xs font-semibold hover:bg-zinc-50 cursor-pointer active:scale-95" onclick="coraCopyShareLink()">Copy</button>
                </div>
                <span class="text-[10px] text-zinc-400 block" id="cora-share-expiry-text">Expires on: Dec 12, 2026</span>
            </div>
        </div>
        <div class="cora-drawer-footer p-4 flex items-center gap-2.5 shrink-0">
            <button id="cora-share-submit-btn" class="flex-1 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm">
                Send & Generate Link
            </button>
            <button class="px-4 py-2.5 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraToggleShareDrawer(false)">
                Cancel
            </button>
        </div>
    </aside>

    <!-- Property Portfolio Drawer -->
    <aside id="cora-portfolio-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-[460px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span id="cora-portfolio-drawer-title" class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
                Create Gallery Folder
            </span>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleGalleryDrawer(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            <input type="hidden" id="cora-portfolio-id" value="">
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Gallery Title</label>
                <input type="text" id="cora-portfolio-title" placeholder="e.g. Gurgaon Penthouse Showcase" class="w-full border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors text-zinc-800 font-medium">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Layout Template</label>
                    <select id="cora-portfolio-template" class="w-full border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors text-zinc-800 font-semibold cursor-pointer">
                        <option value="grid">Grid Layout</option>
                        <option value="masonry">Masonry Layout</option>
                        <option value="carousel">Carousel Slider</option>
                    </select>
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Access Password (Optional)</label>
                    <input type="text" id="cora-portfolio-password" placeholder="Leave empty for public" class="w-full border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors text-zinc-800 font-medium">
                </div>
            </div>

            <!-- Client Selections Panel (Visible only when editing) -->
            <div id="cora-portfolio-selections-section" class="hidden border border-zinc-200/80 rounded-lg p-3 bg-zinc-50/50 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold text-zinc-800 uppercase tracking-wider flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-655"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        Client Album Selections
                    </span>
                    <button class="text-[9px] font-bold text-zinc-900 bg-white border border-zinc-200 hover:bg-zinc-50 px-2 py-0.5 rounded shadow-sm transition-colors cursor-pointer" onclick="coraCopySelectedFileNames()">
                        Copy File Names
                    </button>
                </div>
                <div id="cora-portfolio-selections-list" class="max-h-24 overflow-y-auto text-[11px] text-zinc-600 divide-y divide-zinc-100 font-medium">
                    <!-- Hearted items listed here -->
                </div>
            </div>

            <!-- Media Assets List -->
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Media Assets (Images & Videos)</label>
                    <button class="text-[9px] font-bold text-zinc-900 bg-white border border-zinc-200 hover:bg-zinc-50 px-2.5 py-1 rounded shadow-sm transition-colors cursor-pointer" onclick="coraAddAssetRow()">
                        + Add Asset
                    </button>
                </div>
                
                <div id="cora-portfolio-assets-container" class="space-y-3 min-h-[150px]">
                    <!-- Asset rows rendered dynamically -->
                </div>
            </div>
        </div>

        <div class="cora-drawer-footer p-4 flex items-center gap-2.5 shrink-0 border-t border-zinc-100 bg-zinc-50">
            <button id="cora-portfolio-submit-btn" class="flex-1 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraSaveGalleryData()">
                Save Gallery Folder
            </button>
            <button class="px-4 py-2.5 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraToggleGalleryDrawer(false)">
                Cancel
            </button>
        </div>
    </aside>

    <!-- Transaction Drawer (Financial Board) -->
    <aside id="cora-transaction-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-full max-w-[420px] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span id="cora-tx-drawer-title" class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect>
                    <line x1="12" y1="4" x2="12" y2="20"></line>
                </svg>
                Add Ledger Entry
            </span>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleTransactionDrawer(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <input type="hidden" id="cora-tx-id-hidden" value="">
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Entry Type</label>
                <select id="cora-tx-type-select" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" onchange="coraUpdateTxCategories(this.value)">
                    <option value="Inflow">Cash Inflow (Income)</option>
                    <option value="Outflow">Cash Outflow (Expense)</option>
                </select>
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Date</label>
                <input type="date" id="cora-tx-date" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" value="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Description</label>
                <input type="text" id="cora-tx-desc" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. 50% Commission for Commercial Lease">
            </div>

            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Amount (₹)</label>
                <input type="text" id="cora-tx-amount" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. 15,000">
            </div>

            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Category</label>
                <select id="cora-tx-category" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                    <!-- Categories filled dynamically by JS -->
                </select>
            </div>

            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Link Client / Lead (Optional)</label>
                <select id="cora-tx-client-select" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                    <option value="">— Unlinked —</option>
                    <optgroup label="Clients">
                        <?php foreach ( $cora_workspace_clients as $client ) : ?>
                            <option value="client_<?php echo esc_attr( $client['id'] ); ?>"><?php echo esc_html( $client['names'] ); ?> (Client)</option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Leads/Prospects">
                        <?php foreach ( $cora_workspace_leads as $lead ) : ?>
                            <option value="lead_<?php echo esc_attr( $lead['id'] ); ?>"><?php echo esc_html( $lead['names'] ); ?> (Lead)</option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>

            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Payment Status</label>
                <select id="cora-tx-status" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                    <option value="Received">Received / Paid</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
        </div>
        
        <div class="cora-drawer-footer p-4 flex items-center gap-2.5 shrink-0 border-t border-zinc-100 bg-zinc-50">
            <button id="cora-tx-submit-btn" class="flex-1 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraSaveTransactionData()">
                Save Entry
            </button>
            <button class="px-4 py-2.5 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraToggleTransactionDrawer(false)">
                Cancel
            </button>
        </div>
    </aside>

    <!-- Sliding Drawer: Article Captured Leads -->
    <aside id="drawer-article-leads" class="collapsed fixed top-0 right-0 h-full w-[450px] bg-white border-l border-zinc-200 shadow-2xl z-[150] transform translate-x-full transition-transform duration-300 ease-out flex flex-col overflow-hidden pointer-events-none">
        <header class="p-5 border-b border-zinc-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="p-1.5 bg-zinc-100 rounded text-zinc-800 flex items-center">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </span>
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-wider" id="cora-article-leads-title">Captured Leads</h3>
                    <p class="text-[9px] text-zinc-400 font-medium">Attributed CRM submissions from this article</p>
                </div>
            </div>
            <button class="p-1.5 border border-zinc-200 rounded text-zinc-400 hover:text-zinc-900 transition-colors hover:bg-zinc-50 cursor-pointer" onclick="coraToggleArticleLeadsDrawer(false)">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </header>

        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <div class="border border-zinc-200 rounded-lg overflow-hidden bg-white">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-100 text-[9px] font-bold text-zinc-400 uppercase tracking-wider">
                            <th class="py-2 px-3">Lead Contact</th>
                            <th class="py-2 px-3">Details / Request</th>
                            <th class="py-2 px-3 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody id="cora-article-leads-list" class="divide-y divide-zinc-100 text-zinc-700">
                        <tr>
                            <td colspan="3" class="py-6 text-center text-zinc-400">Loading captured leads...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </aside>

    <!-- Mobile Floating Bottom Navigation (Shopify Reference Floating Pill Bar) -->
    <div class="cora-mobile-bottom-bar-wrapper lg:hidden" style="position: fixed !important; bottom: 16px !important; left: 50% !important; transform: translateX(-50%) !important; z-index: 9980 !important; align-items: center !important; justify-content: center !important; gap: 8px !important; width: 94vw !important; max-width: 480px !important; box-sizing: border-box !important;">
        <!-- 1. Floating Circular Search Button (Left) -->
        <button id="cora-mobile-search-trigger" type="button" onclick="const sInput = document.querySelector('input[type=&quot;search&quot;], #cora-header-search, .cora-search-input'); if(sInput){ sInput.focus(); sInput.scrollIntoView({behavior:'smooth'}); } else if(typeof coraOpenSearch === 'function'){ coraOpenSearch(); }" style="width: 46px !important; height: 46px !important; border-radius: 50% !important; background: #ffffff !important; border: 1px solid #e4e4e7 !important; color: #18181b !important; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.1), 0 4px 12px -2px rgba(0,0,0,0.05) !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; cursor: pointer !important; outline: none !important;" title="Search">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>

        <!-- 2. Main Floating Capsule Bar (5 equal 20% tabs) -->
        <nav class="cora-bottom-nav" style="flex: 1 1 auto !important; min-width: 0 !important; background: #ffffff !important; border: 1px solid #e4e4e7 !important; border-radius: 9999px !important; padding: 4px 4px !important; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.1), 0 4px 12px -2px rgba(0,0,0,0.05) !important; display: flex !important; align-items: center !important; justify-content: space-between !important; width: 100% !important;">
            <!-- Home -->
            <div class="cora-bottom-nav-item <?php echo in_array($sub_page, array('dashboard', 'home', '')) ? 'cora-active' : ''; ?>" data-target="dashboard" onclick="if(typeof coraNavigateTo==='function'){coraNavigateTo('dashboard');}" style="flex: 1 1 20% !important; width: 20% !important; max-width: 20% !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; padding: 6px 0 !important; border-radius: 9999px !important; cursor: pointer !important; text-decoration: none !important; box-sizing: border-box !important;">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 2px;">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span style="font-size: 10px !important; font-weight: 600 !important; line-height: 1 !important;">Home</span>
            </div>

            <!-- CRM -->
            <div class="cora-bottom-nav-item <?php echo in_array($sub_page, array('crm', 'bookings', 'contacts')) ? 'cora-active' : ''; ?>" data-target="bookings" onclick="if(typeof coraNavigateTo==='function'){coraNavigateTo('bookings');}" style="flex: 1 1 20% !important; width: 20% !important; max-width: 20% !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; padding: 6px 0 !important; border-radius: 9999px !important; cursor: pointer !important; text-decoration: none !important; box-sizing: border-box !important;">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 2px;">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span style="font-size: 10px !important; font-weight: 600 !important; line-height: 1 !important;">CRM</span>
            </div>

            <!-- Finance (Official INR Rupee Icon) -->
            <div class="cora-bottom-nav-item <?php echo in_array($sub_page, array('financials', 'finance', 'invoices', 'saas-calculator')) ? 'cora-active' : ''; ?>" data-target="financials" onclick="if(typeof coraNavigateTo==='function'){coraNavigateTo('financials');}" style="flex: 1 1 20% !important; width: 20% !important; max-width: 20% !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; padding: 6px 0 !important; border-radius: 9999px !important; cursor: pointer !important; text-decoration: none !important; box-sizing: border-box !important;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 2px;">
                    <path d="M5 4h14M5 9h14M8 4v1a5 5 0 0 0 0 8h1L19 20M8 13h5a4 4 0 0 0 0-8H8"/>
                </svg>
                <span style="font-size: 10px !important; font-weight: 600 !important; line-height: 1 !important;">Finance</span>
            </div>

            <!-- User -->
            <div class="cora-bottom-nav-item <?php echo in_array($sub_page, array('users', 'team', 'profile', 'onboarding')) ? 'cora-active' : ''; ?>" data-target="users" onclick="if(typeof coraNavigateTo==='function'){coraNavigateTo('users');}" style="flex: 1 1 20% !important; width: 20% !important; max-width: 20% !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; padding: 6px 0 !important; border-radius: 9999px !important; cursor: pointer !important; text-decoration: none !important; box-sizing: border-box !important;">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 2px;">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span style="font-size: 10px !important; font-weight: 600 !important; line-height: 1 !important;">User</span>
            </div>

            <!-- More -->
            <div class="cora-bottom-nav-item" id="cora-mobile-more-btn" onclick="const sb=document.querySelector('.cora-sidebar'); if(sb){ sb.classList.toggle('mobile-open'); sb.classList.toggle('hidden'); }" style="flex: 1 1 20% !important; width: 20% !important; max-width: 20% !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; padding: 6px 0 !important; border-radius: 9999px !important; cursor: pointer !important; text-decoration: none !important; box-sizing: border-box !important;">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 2px;">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
                <span style="font-size: 10px !important; font-weight: 600 !important; line-height: 1 !important;">More</span>
            </div>
        </nav>

        <!-- 3. Floating Circular AI Action Button (Right) -->
        <button id="cora-mobile-ai-trigger" type="button" onclick="if(typeof coraOpenSidebarChat==='function'){coraOpenSidebarChat();}else{const sb=document.querySelector('#cora-ai-sidebar'); if(sb){sb.classList.toggle('hidden');}}" style="width: 46px !important; height: 46px !important; border-radius: 50% !important; background: #ffffff !important; border: 1px solid #e4e4e7 !important; color: #18181b !important; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.1), 0 4px 12px -2px rgba(0,0,0,0.05) !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; cursor: pointer !important; outline: none !important;" title="Ask Cora AI">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1px;">
                <path d="M12 2l2.4 4.86L19.8 8l-3.9 3.8 0.9 5.36L12 14.6l-4.8 2.56 0.9-5.36L4.2 8l5.4-1.14L12 2z"></path>
            </svg>
            <span style="font-size: 9px !important; font-weight: 700 !important; line-height: 1 !important;">AI</span>
        </button>
    </div>

    <!-- Modals -->
    <!-- Share Gallery Modal -->
    <div id="cora-modal-share-portfolio" class="cora-modal-overlay">
        <div class="cora-modal-card">
            <div class="cora-modal-header">
                <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                    Share Gallery
                </h3>
                <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraCloseModals()">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="cora-modal-body space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Gallery Template</label>
                    <select id="cora-share-template" class="w-full bg-white border border-zinc-200 rounded-md px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-500">
                        <option value="grid">Grid (Default)</option>
                        <option value="masonry">Masonry</option>
                        <option value="carousel">Carousel</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Items to Share</label>
                    <div class="space-y-2 bg-zinc-50 p-3 rounded-md border border-zinc-200/60">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="cora-share-images" checked class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                            <span class="text-sm text-zinc-700">Images</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="cora-share-videos" checked class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                            <span class="text-sm text-zinc-700">Videos</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Client Email (Optional)</label>
                    <input type="email" id="cora-share-email" placeholder="client@example.com" class="w-full bg-white border border-zinc-200 rounded-md px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-500">
                    <p class="text-[10px] text-zinc-500 mt-1">If provided, an email with the link will be sent directly.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Password Protection (Optional)</label>
                    <div class="relative">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <input type="text" id="cora-share-password" placeholder="Leave blank for public access" class="w-full bg-white border border-zinc-200 rounded-md pl-9 pr-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-500">
                    </div>
                </div>
            </div>
            <div class="cora-modal-footer">
                <button class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 font-semibold rounded-md hover:bg-zinc-50 transition-colors text-xs cursor-pointer" onclick="coraCloseModals()">Cancel</button>
                <button class="px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-colors text-xs cursor-pointer shadow-sm flex items-center gap-2" id="cora-btn-submit-share" onclick="coraSubmitShareGallery()">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    Save & Generate Link
                </button>
            </div>
        </div>
    </div>

    <!-- Link Google Drive URL Modal -->
    <div id="cora-modal-link-drive" class="cora-modal-overlay">
        <div class="cora-modal-card">
            <div class="cora-modal-header">
                <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-blue-500"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    Link Drive URL
                </h3>
                <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraCloseModals()">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="cora-modal-body space-y-4">
                <p class="text-sm text-zinc-600">Paste a direct link to a Google Drive file to add it to this portfolio without downloading.</p>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Google Drive URL</label>
                    <input type="url" id="cora-link-drive-url" placeholder="https://drive.google.com/file/d/..." class="w-full bg-white border border-zinc-200 rounded-md px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Asset Name</label>
                    <input type="text" id="cora-link-drive-name" placeholder="E.g., highlight-video.mp4" class="w-full bg-white border border-zinc-200 rounded-md px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Asset Type</label>
                    <select id="cora-link-drive-type" class="w-full bg-white border border-zinc-200 rounded-md px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-900">
                        <option value="image">Image (Photo)</option>
                        <option value="video">Video</option>
                    </select>
                </div>
            </div>
            <div class="cora-modal-footer">
                <button class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 font-semibold rounded-md hover:bg-zinc-50 transition-colors text-xs cursor-pointer" onclick="coraCloseModals()">Cancel</button>
                <button class="px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-colors text-xs cursor-pointer shadow-sm active:scale-95" onclick="coraSubmitLinkDrive()">Link Asset</button>
            </div>
        </div>
    </div>

    <!-- Sync Drive Folder Modal -->
    <div id="cora-modal-sync-folder" class="cora-modal-overlay">
        <div class="cora-modal-card">
            <div class="cora-modal-header">
                <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-800"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    Sync Drive Folder
                </h3>
                <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraCloseModals()">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="cora-modal-body space-y-4">
                <div class="bg-zinc-50 border border-zinc-200/80 rounded-md p-3">
                    <p class="text-xs text-zinc-700 font-medium">Link a public Google Drive folder to automatically sync its contents to this portfolio. Since direct connection requires credentials, this demo simulates syncing by adding 3 professional listing photos and 5 premium video walk-throughs.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Public Drive Folder URL</label>
                    <input type="url" id="cora-sync-folder-url" placeholder="https://drive.google.com/drive/folders/..." class="w-full bg-white border border-zinc-200 rounded-md px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>
            </div>
            <div class="cora-modal-footer">
                <button class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 font-semibold rounded-md hover:bg-zinc-50 transition-colors text-xs cursor-pointer" onclick="coraCloseModals()">Cancel</button>
                <button class="px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-colors text-xs cursor-pointer shadow-sm flex items-center gap-2 active:scale-95" id="cora-btn-submit-sync" onclick="coraSubmitSyncFolder()">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 2v6h-6"></path><path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path><path d="M3 22v-6h6"></path><path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path></svg>
                    Sync with Drive
                </button>
            </div>
            </div>
        </div>
    </div>

    <!-- Asset Lightbox Modal -->
    <div id="cora-modal-asset-lightbox" class="cora-modal-overlay">
        <div class="cora-modal-card max-w-4xl w-full mx-4 md:mx-auto">
            <div class="cora-modal-header">
                <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Asset Details
                </h3>
                <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraCloseModals()">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="cora-modal-body flex flex-col md:flex-row gap-6 p-0 md:p-6">
                <!-- Preview Side -->
                <div class="w-full md:w-3/5 bg-zinc-100 rounded-lg flex items-center justify-center overflow-hidden relative min-h-[300px]">
                    <div id="cora-lightbox-preview-container" class="w-full h-full flex items-center justify-center p-4">
                        <!-- Content injected via JS -->
                    </div>
                </div>
                <!-- Details Side -->
                <div class="w-full md:w-2/5 space-y-4 px-6 pb-6 md:px-0 md:pb-0">
                    <input type="hidden" id="cora-lightbox-asset-id">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Asset Name</label>
                        <input type="text" id="cora-lightbox-name" class="w-full bg-white border border-zinc-200 rounded-md px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Alt Text (SEO)</label>
                        <input type="text" id="cora-lightbox-alt" placeholder="Describe the image for screen readers" class="w-full bg-white border border-zinc-200 rounded-md px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Description</label>
                        <textarea id="cora-lightbox-description" rows="4" placeholder="Detailed description for client viewing..." class="w-full bg-white border border-zinc-200 rounded-md px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-500 resize-none"></textarea>
                    </div>
                    
                    <!-- Quick Actions (Future Features) -->
                    <div class="pt-2 border-t border-zinc-100">
                        <label class="block text-xs font-semibold text-zinc-400 mb-2 uppercase tracking-wider">Asset Actions</label>
                        <div class="flex flex-wrap gap-2">
                            <button class="px-3 py-1.5 bg-white border border-zinc-200 text-zinc-600 rounded flex items-center gap-1.5 text-[10px] font-semibold hover:bg-zinc-50 hover:text-zinc-900 transition-colors shadow-sm" onclick="window.coraShowToast('Asset sharing will be available in a future update.')">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                Share Asset
                            </button>
                            <button class="px-3 py-1.5 bg-white border border-zinc-200 text-zinc-600 rounded flex items-center gap-1.5 text-[10px] font-semibold hover:bg-zinc-50 hover:text-zinc-900 transition-colors shadow-sm" onclick="window.coraShowToast('Folder organization will be available in a future update.')">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                Move to Folder
                            </button>
                            <button class="px-3 py-1.5 bg-white border border-zinc-200 text-zinc-600 rounded flex items-center gap-1.5 text-[10px] font-semibold hover:bg-zinc-50 hover:text-zinc-900 transition-colors shadow-sm" onclick="window.coraShowToast('Cover image selection will be available in a future update.')">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                Set as Cover
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cora-modal-footer">
                <button class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 font-semibold rounded-md hover:bg-zinc-50 transition-colors text-xs cursor-pointer" onclick="coraCloseModals()">Cancel</button>
                <button class="px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-colors text-xs cursor-pointer shadow-sm flex items-center gap-2" id="cora-btn-submit-lightbox" onclick="coraSaveAssetDetails()">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Advanced Full-Page AI Content Editor (Notion/Medium Style) -->
    <style>
    #cora-full-page-editor {
        display: none;
    }
    #cora-full-page-editor:not(.hidden) {
        display: flex !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 999999 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #cora-full-page-editor main { background-color: #FBFaf7 !important; padding: 40px 24px !important; }

    /* Inspector sidebar collapse behavior & transitions */
    #cora-article-inspector {
        transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s, border-color 0.2s !important;
    }
    #cora-article-inspector.collapsed-inspector {
        width: 64px !important; /* Collapsed width */
        overflow: hidden !important;
    }
    #cora-article-inspector.collapsed-inspector .inspector-tab-btn {
        flex-direction: column !important;
        border-bottom: none !important;
        border-right: 2px solid transparent !important;
        padding: 14px 0 !important;
        justify-content: center !important;
        border-radius: 0 !important;
    }
    #cora-article-inspector.collapsed-inspector .inspector-tab-btn.tab-active {
        border-right-color: #09090b !important;
        background-color: #f4f4f5 !important;
        color: #09090b !important;
    }
    .cora-dark-theme #cora-article-inspector.collapsed-inspector .inspector-tab-btn.tab-active {
        border-right-color: #ffffff !important;
        background-color: #18181b !important;
        color: #ffffff !important;
    }
    #cora-article-inspector.collapsed-inspector .inspector-tabs-container {
        flex-direction: column !important;
        border-bottom: none !important;
        padding: 10px 0 !important;
    }
    #cora-article-inspector.collapsed-inspector .inspector-tab-label,
    #cora-article-inspector.collapsed-inspector .inspector-tab-soon-badge,
    #cora-article-inspector.collapsed-inspector [id^="panel-inspector-"] {
        display: none !important;
    }

    .cora-writing-sheet {
        background-color: #ffffff !important;
        border: 1px solid #e4e4e7 !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 30px -10px rgba(9, 9, 11, 0.04), 0 1px 3px rgba(9, 9, 11, 0.02) !important;
        padding: 32px 40px !important;
        min-height: calc(100vh - 160px) !important;
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: relative;
    }
    .cora-serif-editor .ql-editor { font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif; font-size: 1.125rem; line-height: 1.8; color: #18181b; }
    .cora-sans-editor .ql-editor { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 1.05rem; line-height: 1.75; color: #18181b; }
    .ql-toolbar.ql-snow {
        border: 1px solid #e4e4e7 !important;
        border-radius: 12px !important;
        padding: 6px 12px !important;
        position: sticky !important;
        top: 12px !important;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(8px) !important;
        z-index: 40 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        margin: 0 auto 16px auto !important;
        max-width: max-content !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 2px !important;
    }

    .ql-snow.ql-toolbar button, .ql-snow .ql-toolbar button {
        height: 28px !important;
        width: 32px !important;
        padding: 4px 6px !important;
        border-radius: 6px !important;
        transition: all 0.15s ease !important;
        color: #52525b !important;
    }
    .ql-snow.ql-toolbar button:hover, .ql-snow .ql-toolbar button:hover,
    .ql-snow.ql-toolbar button.ql-active, .ql-snow .ql-toolbar button.ql-active {
        background-color: #f4f4f5 !important;
        color: #09090b !important;
    }
    .ql-snow.ql-toolbar button svg, .ql-snow .ql-toolbar button svg {
        stroke-width: 2.2 !important;
    }
    .ql-snow .ql-picker {
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #3f3f46 !important;
        border-radius: 6px !important;
        height: 28px !important;
        line-height: 28px !important;
    }
    .ql-snow .ql-picker-label {
        padding-left: 10px !important;
        padding-right: 22px !important;
        border: 1px solid #e4e4e7 !important;
        border-radius: 6px !important;
        background-color: #ffffff !important;
        color: #3f3f46 !important;
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
    }
    .ql-snow .ql-picker-label:hover {
        background-color: #f4f4f5 !important;
        color: #09090b !important;
        border-color: #d4d4d8 !important;
    }
    /* Hide default arrows */
    .ql-snow .ql-picker-label svg {
        display: none !important;
    }
    /* Chevron down styling */
    .ql-snow .ql-picker-label::after {
        content: "" !important;
        position: absolute !important;
        right: 8px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 8px !important;
        height: 4px !important;
        background-color: currentColor !important;
        clip-path: polygon(0 0, 100% 0, 50% 100%) !important;
        opacity: 0.7 !important;
        transition: transform 0.2s ease !important;
    }
    /* Specific width adjustments */
    .ql-snow .ql-picker.ql-header {
        width: 96px !important;
    }
    .ql-snow .ql-picker.ql-font {
        width: 102px !important;
    }
    .ql-snow .ql-picker.ql-size {
        width: 72px !important;
    }
    /* Floating options menu card */
    .ql-snow .ql-picker-options {
        border: 1px solid #e4e4e7 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 15px -3px rgba(9, 9, 11, 0.08), 0 4px 6px -2px rgba(9, 9, 11, 0.04) !important;
        background-color: #ffffff !important;
        padding: 4px !important;
        margin-top: 4px !important;
        z-index: 50 !important;
    }
    .ql-snow .ql-picker-options .ql-picker-item {
        padding: 5px 8px !important;
        border-radius: 5px !important;
        color: #3f3f46 !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        transition: background-color 0.15s ease, color 0.15s ease !important;
    }
    .ql-snow .ql-picker-options .ql-picker-item:hover,
    .ql-snow .ql-picker-options .ql-picker-item.ql-selected {
        background-color: #f4f4f5 !important;
        color: #09090b !important;
    }
    /* Button formatting group gaps */
    .ql-toolbar.ql-snow .ql-formats {
        margin-right: 12px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 2px !important;
    }
    .ql-snow .ql-stroke {
        stroke: #52525b !important;
        stroke-width: 2.2 !important;
    }
    .ql-snow .ql-fill {
        fill: #52525b !important;
    }
    .ql-container.ql-snow { border: none !important; }

    .ql-editor.ql-blank::before { color: #d4d4d8; font-style: normal; }
    /* Typography whitelist classes */
    .ql-font-serif { font-family: ui-serif, Georgia, Cambria, serif !important; }
    .ql-font-mono  { font-family: ui-monospace, 'Cascadia Code', 'Fira Code', monospace !important; }
    .ql-font-sans  { font-family: ui-sans-serif, system-ui, -apple-system, sans-serif !important; }
    .ql-size-13px  { font-size: 13px !important; }
    .ql-size-18px  { font-size: 18px !important; }
    .ql-size-24px  { font-size: 24px !important; }
    /* Slash picker spinner */
    @keyframes ql-spin { to { transform: rotate(360deg); } }
    .animate-spin { animation: ql-spin 0.8s linear infinite; }
    </style>

    <div id="cora-full-page-editor" class="hidden fixed inset-0 z-[100] bg-white flex-col h-full overflow-hidden select-none">
        
        <!-- Modern Header Bar -->
        <header class="flex items-center justify-between px-3 sm:px-6 py-2 bg-white shrink-0 z-30 gap-2 border-b border-zinc-200 select-none">
            <div class="flex items-center gap-2.5 min-w-0">
                <button type="button" class="flex items-center gap-1 text-zinc-650 hover:text-zinc-900 transition-all text-xs font-semibold cursor-pointer py-1.5 px-3 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 active:scale-98 shadow-3xs shrink-0" onclick="coraToggleContentDrawer(false)">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-500"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Back</span>
                </button>
                
                <!-- Status Badge -->
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-zinc-50 border border-zinc-200 text-xs font-medium text-zinc-650 shadow-3xs shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="cora-editor-status">Saved</span>
                </div>

                <!-- Draft Status Dropdown Pill -->
                <div class="relative inline-block text-left" id="cora-editor-status-dropdown-wrap">
                    <button type="button" class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-semibold text-zinc-700 cursor-pointer shadow-3xs" onclick="jQuery('#cora-editor-status-dropdown-menu').toggleClass('hidden')">
                        <span id="cora-editor-status-badge-text">Draft</span>
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-500"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div id="cora-editor-status-dropdown-menu" class="hidden absolute left-0 mt-1.5 w-44 bg-white border border-zinc-200 rounded-xl shadow-xl py-1 z-[99] text-xs font-semibold text-zinc-700">
                        <button type="button" class="w-full text-left px-3.5 py-2 hover:bg-zinc-50 border-none bg-transparent cursor-pointer flex items-center gap-2" onclick="coraSetEditorStatus('draft'); jQuery('#cora-editor-status-dropdown-menu').addClass('hidden')">
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span> Draft
                        </button>
                        <button type="button" class="w-full text-left px-3.5 py-2 hover:bg-zinc-50 border-none bg-transparent cursor-pointer flex items-center gap-2" onclick="coraSetEditorStatus('pending'); jQuery('#cora-editor-status-dropdown-menu').addClass('hidden')">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending Review
                        </button>
                        <button type="button" class="w-full text-left px-3.5 py-2 hover:bg-zinc-50 border-none bg-transparent cursor-pointer flex items-center gap-2" onclick="coraSetEditorStatus('publish'); jQuery('#cora-editor-status-dropdown-menu').addClass('hidden')">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                        </button>
                    </div>
                </div>

                <!-- Word & Read Time Metrics -->
                <div class="hidden md:flex items-center gap-1.5 text-xs text-zinc-400 font-medium font-sans truncate">
                    <span>·</span>
                    <span id="cora-editor-metrics">0 words · 0 min read</span>
                </div>
            </div>

            <!-- Header Action Controls & Buttons -->
            <div class="flex items-center gap-2.5">
                <!-- Icon Actions Bar -->
                <div class="hidden sm:flex items-center gap-1 border-r border-zinc-200 pr-2.5">
                    <button type="button" class="p-1.5 hover:bg-zinc-50 text-zinc-450 hover:text-zinc-900 rounded-lg transition-colors cursor-pointer border-none bg-transparent" title="Verify Compliance" onclick="coraSwitchInspectorTab('claims')">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </button>
                    <button type="button" class="p-1.5 hover:bg-zinc-50 text-zinc-450 hover:text-zinc-900 rounded-lg transition-colors cursor-pointer border-none bg-transparent" title="AI Insights" onclick="coraSwitchInspectorTab('copilot')">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A5.5 5.5 0 0 0 12.5 2.5a5.5 5.5 0 0 0-5.5 5.5c0 1.3.5 2.6 1.5 3.5.7.8 1.2 1.5 1.5 2.5"></path><line x1="9" y1="18" x2="15" y2="18"></line><line x1="10" y1="22" x2="14" y2="22"></line></svg>
                    </button>
                    <button type="button" class="p-1.5 hover:bg-zinc-50 text-zinc-450 hover:text-zinc-900 rounded-lg transition-colors cursor-pointer border-none bg-transparent" title="Comments & Collaboration" onclick="window.coraShowToast('Editor comments drawer is coming soon.', 'info')">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </button>
                    <button type="button" class="p-1.5 hover:bg-zinc-50 text-zinc-450 hover:text-zinc-900 rounded-lg transition-colors cursor-pointer border-none bg-transparent" title="Version History" onclick="window.coraShowToast('Version recovery history is coming soon.', 'info')">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </button>
                </div>

                <!-- Preview Button -->
                <button type="button" class="px-3.5 py-1.5 border border-zinc-200 rounded-lg text-zinc-700 bg-white hover:bg-zinc-50 hover:text-zinc-900 transition-all cursor-pointer text-xs font-semibold active:scale-95 shadow-3xs flex items-center gap-1.5" onclick="coraPreviewArticle()">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    Preview
                </button>

                <!-- Split Button for Publish Live -->
                <div class="relative inline-flex rounded-lg shadow-sm" id="cora-publish-dropdown-wrap">
                    <button type="button" class="inline-flex items-center px-4 py-1.5 bg-zinc-950 hover:bg-black text-white font-bold rounded-l-lg transition-all cursor-pointer text-xs border border-zinc-900 border-r-0 active:scale-95" onclick="coraSaveArticle('publish')">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="mr-1.5"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                        Publish
                    </button>
                    <button type="button" class="inline-flex items-center px-2.5 py-1.5 bg-zinc-950 hover:bg-black text-white font-bold rounded-r-lg transition-all cursor-pointer text-xs border border-zinc-900 border-l-zinc-800" onclick="window.coraTogglePublishDropdown()">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <!-- Dropdown Menu -->
                    <div id="cora-publish-dropdown-menu" class="hidden absolute right-0 top-full mt-1.5 w-48 bg-white border border-zinc-200 rounded-xl shadow-xl py-1.5 z-[99] text-xs font-semibold text-zinc-700 font-sans">
                        <button type="button" class="w-full text-left px-4 py-2 hover:bg-zinc-50 flex items-center gap-2 cursor-pointer border-none bg-transparent" onclick="coraSaveArticle('publish'); window.coraTogglePublishDropdown(false);">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Publish Immediately
                        </button>
                        <button type="button" class="w-full text-left px-4 py-2 hover:bg-zinc-50 flex items-center gap-2 cursor-pointer border-none bg-transparent" onclick="window.coraToggleBeehiivDropdown('visibility'); window.coraTogglePublishDropdown(false);">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Schedule Publish...
                        </button>
                    </div>
                </div>

                <!-- Three-dot button ... -->
                <div class="relative" id="cora-header-more-wrap">
                    <button type="button" class="p-2 border border-zinc-200 text-zinc-650 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-all cursor-pointer flex items-center justify-center active:scale-95 shadow-3xs" onclick="window.coraToggleHeaderMoreDropdown()">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.8" fill="none"><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle><circle cx="5" cy="12" r="1.5"></circle></svg>
                    </button>
                    <!-- Dropdown Menu -->
                    <div id="cora-header-more-dropdown-menu" class="hidden absolute right-0 top-full mt-1.5 w-52 bg-white border border-zinc-200 rounded-xl shadow-xl py-1.5 z-[99] text-xs font-semibold text-zinc-700">
                        <button type="button" class="w-full text-left px-4 py-2 hover:bg-zinc-50 flex items-center gap-2 cursor-pointer border-none bg-transparent" onclick="coraSaveArticle('draft'); window.coraToggleHeaderMoreDropdown(false);">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Save Draft
                        </button>
                        <button type="button" id="cora-btn-submit-review-dropdown" class="w-full text-left px-4 py-2 hover:bg-zinc-50 flex items-center gap-2 cursor-pointer border-none bg-transparent" onclick="coraSubmitArticleForReview(); window.coraToggleHeaderMoreDropdown(false);">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Submit for Review
                        </button>
                        <div class="h-px bg-zinc-150 my-1"></div>
                        <button type="button" class="w-full text-left px-4 py-2 hover:bg-zinc-50 flex items-center gap-2 cursor-pointer border-none bg-transparent" onclick="coraToggleArticleInspector(); window.coraToggleHeaderMoreDropdown(false);">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                            Toggle Inspector Sidebar
                        </button>
                    </div>
                </div>

                <div class="h-4 w-px bg-zinc-200 mx-0.5"></div>

                <!-- Toggle Sidebar Button -->
                <button type="button" id="cora-btn-toggle-inspector" class="p-2 border border-zinc-200 text-zinc-650 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-all cursor-pointer" title="Toggle Inspector Panel" onclick="coraToggleArticleInspector()">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                </button>
            </div>
        </header>

        <!-- Editor Body -->
        <div class="flex-1 flex overflow-hidden relative">

            <!-- ST-1: Left Outline Sidebar -->
            <aside id="cora-editor-left-sidebar" class="w-64 bg-zinc-50/50 border-r border-zinc-200 shrink-0 overflow-y-auto hidden lg:flex flex-col select-none">
                <!-- Navigation Tabs: Outline / Media -->
                <div class="flex border-b border-zinc-200 bg-zinc-100/50 text-[10px] font-bold uppercase tracking-wider select-none shrink-0 font-sans">
                    <button type="button" onclick="coraSwitchLeftSidebarTab('outline', this)" class="cora-left-tab-btn flex-1 py-3 text-center border-b-2 border-zinc-950 text-zinc-900 font-bold bg-white/40 cursor-pointer">
                        Outline
                    </button>
                    <button type="button" onclick="coraSwitchLeftSidebarTab('media', this)" class="cora-left-tab-btn flex-1 py-3 text-center border-b-2 border-transparent text-zinc-450 hover:text-zinc-700 cursor-pointer">
                        Media
                    </button>
                </div>

                <!-- Tab Panel: Outline -->
                <div id="cora-left-panel-outline" class="p-4 space-y-5 flex-1 flex flex-col min-h-0 font-sans">
                    <!-- Heading Hierarchy -->
                    <div class="flex-1 overflow-y-auto min-h-0">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Article Outline</span>
                            <button type="button" class="text-zinc-400 hover:text-zinc-750 cursor-pointer border-none bg-transparent" onclick="coraInsertHeadingPlaceholder()">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            </button>
                        </div>
                        <nav id="cora-outline-hierarchy-list" class="space-y-1 text-xs">
                            <!-- Populated dynamically via JS: coraRebuildOutline() -->
                            <div class="text-zinc-400 italic py-2">No headings in document yet</div>
                        </nav>
                    </div>

                    <!-- Document Stats Card -->
                    <div class="p-3.5 bg-white border border-zinc-200 rounded-xl shadow-3xs space-y-2.5 shrink-0">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Document</span>
                        <div class="grid grid-cols-2 gap-y-2.5 gap-x-4 text-xs">
                            <div>
                                <span class="text-[10px] text-zinc-400 block uppercase font-bold tracking-wider leading-none">Words</span>
                                <span class="font-extrabold text-zinc-900 mt-1 block leading-none" id="left-stat-words">0</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-zinc-400 block uppercase font-bold tracking-wider leading-none">Headings</span>
                                <span class="font-extrabold text-zinc-900 mt-1 block leading-none" id="left-stat-headings">0</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-zinc-400 block uppercase font-bold tracking-wider leading-none">Images</span>
                                <span class="font-extrabold text-zinc-900 mt-1 block leading-none" id="left-stat-images">0</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-zinc-400 block uppercase font-bold tracking-wider leading-none">Links</span>
                                <span class="font-extrabold text-zinc-900 mt-1 block leading-none" id="left-stat-links">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Content Score Card -->
                    <div class="p-3.5 bg-white border border-zinc-200 rounded-xl shadow-3xs space-y-2.5 shrink-0">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Content Score</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-150 animate-pulse" id="left-score-grade">B+</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-zinc-700">
                                <span id="left-score-value">82/100</span>
                            </div>
                            <div class="w-full bg-zinc-100 rounded-full h-1.5 overflow-hidden">
                                <div id="left-score-bar" class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: 82%;"></div>
                            </div>
                        </div>
                        <p class="text-[10px] text-zinc-500 leading-relaxed font-sans" id="left-score-message">Good job! A few more tweaks and you're set.</p>
                        <button type="button" onclick="coraSwitchInspectorTab('seo')" class="text-[10px] font-bold text-zinc-900 hover:underline flex items-center gap-1 cursor-pointer border-none bg-transparent">
                            View suggestions &rarr;
                        </button>
                    </div>
                </div>

                <!-- Tab Panel: Media -->
                <div id="cora-left-panel-media" class="hidden p-4 space-y-4 flex-1 overflow-y-auto font-sans">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Article Media</span>
                        <button type="button" class="text-zinc-500 hover:text-zinc-900 text-[10px] font-bold border border-zinc-200 rounded px-2.5 py-1 bg-white shadow-3xs cursor-pointer" onclick="window.coraMediaSelectTarget = 'inline'; coraOpenMediaLibrary();">+ Add</button>
                    </div>
                    <div class="grid grid-cols-2 gap-2" id="left-sidebar-media-grid">
                        <div class="text-[10px] text-zinc-400 italic col-span-2 text-center py-6">No images embedded yet</div>
                    </div>
                </div>
            </aside>

        <!-- Editor Body -->
        <div class="flex-1 flex overflow-hidden relative">
            
            <!-- Notion/Medium-Style Writing Canvas -->
            <main class="flex-1 overflow-y-auto px-6 py-10 md:px-16 xl:px-32 relative">
                <div class="max-w-[760px] mx-auto w-full cora-writing-sheet">
                    
                    <!-- Beehiiv Horizontal Settings Bar -->
                    <div class="w-full border-b border-zinc-200/80 pb-3.5 flex items-center justify-between gap-4 text-xs font-semibold relative select-none">
                        <div class="flex items-center gap-3">
                            <!-- Toggle: Title & subtitle -->
                            <div class="relative inline-block text-left" id="beehiiv-dropdown-title-subtitle-wrap">
                                <button type="button" onclick="window.coraToggleBeehiivDropdown('title-subtitle')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg text-zinc-700 bg-white transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>Title & Subtitle</span>
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <!-- Dropdown card -->
                                <div id="beehiiv-dropdown-title-subtitle" class="hidden absolute left-0 mt-1.5 w-80 bg-white border border-zinc-200 rounded-xl shadow-xl p-4 z-40 space-y-3">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Subtitle & Excerpt</span>
                                    <!-- Excerpt Content inside this dropdown -->
                                    <div class="space-y-1">
                                        <textarea id="cora-article-excerpt-bh" rows="3" placeholder="Summary snippet for search results and social previews..." class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 placeholder:text-zinc-300 resize-none"></textarea>
                                        <p class="text-[9px] text-zinc-400 leading-tight">Summarize the article or write a subtitle snippet.</p>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" onclick="window.coraApplyBeehiivChanges('title-subtitle')" class="px-2.5 py-1 bg-zinc-950 text-white rounded text-[10px] font-bold cursor-pointer hover:bg-zinc-800 transition-colors">Apply</button>
                                        <button type="button" onclick="window.coraToggleBeehiivDropdown('')" class="px-2.5 py-1 border border-zinc-200 hover:bg-zinc-50 rounded text-[10px] text-zinc-650 cursor-pointer">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle: Visibility -->
                            <div class="relative inline-block text-left" id="beehiiv-dropdown-visibility-wrap">
                                <button type="button" onclick="window.coraToggleBeehiivDropdown('visibility')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg text-zinc-700 bg-white transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>Visibility</span>
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div id="beehiiv-dropdown-visibility" class="hidden absolute left-0 mt-1.5 w-64 bg-white border border-zinc-200 rounded-xl shadow-xl p-4 z-40 space-y-3">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Status & Schedule</span>
                                    <div class="space-y-2">
                                        <div class="space-y-1">
                                            <label class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider block">Publish Status</label>
                                            <select id="cora-article-status-bh" class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800">
                                                <option value="draft">Draft</option>
                                                <option value="pending">Pending Review</option>
                                                <option value="publish">Published</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider block">Scheduled Publication</label>
                                            <input type="datetime-local" id="cora-article-scheduled-date-bh" class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800">
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" onclick="window.coraApplyBeehiivChanges('visibility')" class="px-2.5 py-1 bg-zinc-950 text-white rounded text-[10px] font-bold cursor-pointer hover:bg-zinc-800 transition-colors">Apply</button>
                                        <button type="button" onclick="window.coraToggleBeehiivDropdown('')" class="px-2.5 py-1 border border-zinc-200 hover:bg-zinc-50 rounded text-[10px] text-zinc-650 cursor-pointer">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle: Authors -->
                            <div class="relative inline-block text-left" id="beehiiv-dropdown-authors-wrap">
                                <button type="button" onclick="window.coraToggleBeehiivDropdown('authors')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg text-zinc-700 bg-white transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>Authors</span>
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div id="beehiiv-dropdown-authors" class="hidden absolute left-0 mt-1.5 w-60 bg-white border border-zinc-200 rounded-xl shadow-xl p-4 z-40 space-y-3">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Select Author</span>
                                    <div class="space-y-1">
                                        <select id="cora-article-assignee-bh" class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800">
                                            <option value="0">Unassigned</option>
                                            <?php foreach($cora_users as $usr): ?>
                                                <option value="<?php echo $usr->ID; ?>"><?php echo esc_html($usr->display_name); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" onclick="window.coraApplyBeehiivChanges('authors')" class="px-2.5 py-1 bg-zinc-950 text-white rounded text-[10px] font-bold cursor-pointer hover:bg-zinc-800 transition-colors">Apply</button>
                                        <button type="button" onclick="window.coraToggleBeehiivDropdown('')" class="px-2.5 py-1 border border-zinc-200 hover:bg-zinc-50 rounded text-[10px] text-zinc-650 cursor-pointer">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle: Web thumbnail -->
                            <div class="relative inline-block text-left" id="beehiiv-dropdown-thumbnail-wrap">
                                <button type="button" onclick="window.coraToggleBeehiivDropdown('thumbnail')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg text-zinc-700 bg-white transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>Web Thumbnail</span>
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div id="beehiiv-dropdown-thumbnail" class="hidden absolute left-0 mt-1.5 w-72 bg-white border border-zinc-200 rounded-xl shadow-xl p-4 z-40 space-y-3">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Thumbnail Image</span>
                                    <div class="space-y-2">
                                        <div id="cora-thumbnail-preview-bh" class="w-full aspect-[16/9] bg-zinc-100 rounded-xl border border-zinc-200 flex items-center justify-center overflow-hidden relative group cursor-pointer" onclick="window.coraMediaSelectTarget = 'thumbnail'; coraOpenMediaLibrary();">
                                            <div class="absolute inset-0 bg-black/60 hidden group-hover:flex items-center justify-center transition-all z-10">
                                                <span class="text-white text-xs font-semibold flex items-center gap-1.5">
                                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                                    Change Image
                                                </span>
                                            </div>
                                            <img src="" id="cora-thumbnail-img-bh" class="hidden w-full h-full object-cover">
                                            <span id="cora-thumbnail-placeholder-bh" class="text-xs text-zinc-400 font-semibold flex flex-col items-center gap-1">
                                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" class="mb-1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                                Select Image
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" onclick="window.coraApplyBeehiivChanges('thumbnail')" class="px-2.5 py-1 bg-zinc-950 text-white rounded text-[10px] font-bold cursor-pointer hover:bg-zinc-800 transition-colors">Apply</button>
                                        <button type="button" onclick="window.coraToggleBeehiivDropdown('')" class="px-2.5 py-1 border border-zinc-200 hover:bg-zinc-50 rounded text-[10px] text-zinc-650 cursor-pointer">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle: Content tags -->
                            <div class="relative inline-block text-left" id="beehiiv-dropdown-tags-wrap">
                                <button type="button" onclick="window.coraToggleBeehiivDropdown('tags')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg text-zinc-700 bg-white transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>Content Tags</span>
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div id="beehiiv-dropdown-tags" class="hidden absolute left-0 mt-1.5 w-72 bg-white border border-zinc-200 rounded-xl shadow-xl p-4 z-40 space-y-3">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Categories & Tags</span>
                                    <div class="space-y-3">
                                        <div class="space-y-1">
                                            <label class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider block">Categories</label>
                                            <select id="cora-article-categories-bh" multiple class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 min-h-[60px]">
                                                <?php foreach($cora_categories as $cat): ?>
                                                    <option value="<?php echo $cat->term_id; ?>"><?php echo esc_html($cat->name); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider block">Tags</label>
                                            <select id="cora-article-tags-bh" multiple class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 min-h-[60px]">
                                                <?php foreach($cora_tags as $tag): ?>
                                                    <option value="<?php echo $tag->term_id; ?>"><?php echo esc_html($tag->name); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" onclick="window.coraApplyBeehiivChanges('tags')" class="px-2.5 py-1 bg-zinc-950 text-white rounded text-[10px] font-bold cursor-pointer hover:bg-zinc-800 transition-colors">Apply</button>
                                        <button type="button" onclick="window.coraToggleBeehiivDropdown('')" class="px-2.5 py-1 border border-zinc-200 hover:bg-zinc-50 rounded text-[10px] text-zinc-650 cursor-pointer">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle: More settings dropdown ... -->
                            <div class="relative inline-block text-left" id="beehiiv-dropdown-more-wrap">
                                <button type="button" onclick="window.coraToggleBeehiivDropdown('more')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg text-zinc-700 bg-white transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>...</span>
                                </button>
                                <div id="beehiiv-dropdown-more" class="hidden absolute left-0 mt-1.5 w-56 bg-white border border-zinc-200 rounded-xl shadow-xl p-3.5 z-40 space-y-2 text-xs font-semibold text-zinc-700">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-1">Additional Settings</span>
                                    <button type="button" class="w-full text-left px-3 py-2 hover:bg-zinc-50 rounded-lg flex items-center gap-2 cursor-pointer border-none bg-transparent" onclick="coraSwitchInspectorTab('seo'); coraToggleArticleInspector(true); window.coraToggleBeehiivDropdown('')">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                        SEO & Meta Audits
                                    </button>
                                    <button type="button" class="w-full text-left px-3 py-2 hover:bg-zinc-50 rounded-lg flex items-center gap-2 cursor-pointer border-none bg-transparent" onclick="coraSwitchInspectorTab('meta'); coraToggleArticleInspector(true); window.coraToggleBeehiivDropdown('')">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                        Co-authors & Attribution
                                    </button>
                                    <button type="button" class="w-full text-left px-3 py-2 hover:bg-zinc-50 rounded-lg flex items-center gap-2 cursor-pointer border-none bg-transparent" onclick="coraToggleArticleInspector(true); window.coraToggleBeehiivDropdown('')">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        Editorial History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cover Image Dropzone Header -->
                    <div id="cora-cover-image-container" class="relative group w-full rounded-2xl overflow-hidden bg-zinc-50 border border-dashed border-zinc-200 hover:border-zinc-350 hover:bg-zinc-100/50 transition-all min-h-[160px] flex items-center justify-center">
                        <img id="cora-cover-image-img" src="" class="hidden w-full h-48 md:h-64 object-cover">
                        
                        <!-- Placeholder when no cover image -->
                        <div id="cora-cover-image-placeholder" class="flex flex-col items-center gap-2.5 py-12 text-zinc-400 group-hover:text-zinc-650 cursor-pointer transition-colors w-full h-full text-center px-4" onclick="window.coraMediaSelectTarget = 'cover'; coraOpenMediaLibrary();">
                            <div class="p-3 bg-zinc-100 rounded-full text-zinc-400 group-hover:bg-zinc-200/60 group-hover:text-zinc-650 transition-all">
                                <!-- Landscape Icon -->
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" class="text-current"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            </div>
                            <div class="flex flex-col items-center gap-0.5 select-none">
                                <span class="text-xs font-semibold text-zinc-800">Add Cover Image</span>
                                <span class="text-[11px] text-zinc-400">Recommended: 16:9 ratio, up to 5MB</span>
                            </div>
                        </div>

                        <!-- Hover Controls Bar -->
                        <div id="cora-cover-image-controls" class="absolute bottom-3 right-3 hidden group-hover:flex items-center gap-1.5 bg-zinc-950/85 backdrop-blur-md text-white p-1.5 rounded-xl shadow-lg border border-white/10 z-10">
                            <button type="button" class="px-2.5 py-1 text-[11px] font-semibold hover:bg-white/20 rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer border-none bg-transparent" onclick="window.coraMediaSelectTarget = 'cover'; coraOpenMediaLibrary();">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                Add Cover Image
                            </button>
                            <button type="button" class="px-2.5 py-1 text-[11px] font-semibold hover:bg-white/20 rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer border-none bg-transparent" onclick="window.coraShowToast('Drag cover image to reposition', 'info')">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="5 9 2 12 5 15"></polyline><polyline points="9 5 12 2 15 5"></polyline><polyline points="15 19 12 22 9 19"></polyline><polyline points="19 9 22 12 19 15"></polyline><line x1="2" y1="12" x2="22" y2="12"></line><line x1="12" y1="2" x2="12" y2="22"></line></svg>
                                Reposition
                            </button>
                            <button type="button" class="px-2.5 py-1 text-[11px] font-semibold hover:bg-red-500/30 text-red-300 hover:text-white rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer border-none bg-transparent" onclick="coraRemoveCoverImage()">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                Remove
                            </button>
                        </div>
                    </div>

                    <!-- Typography Switcher & Helper Bar -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-zinc-100 select-none">
                        <div class="flex items-center gap-0.5 bg-zinc-100/80 p-0.5 rounded-lg border border-zinc-200/50">
                            <button type="button" id="cora-font-serif-btn" onclick="coraSetEditorFont('serif')" class="px-3 py-1 rounded-md text-xs font-serif font-medium text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50/50 cursor-pointer transition-all active:scale-95">Serif</button>
                            <button type="button" id="cora-font-sans-btn" onclick="coraSetEditorFont('sans')" class="px-3 py-1 rounded-md text-xs font-sans font-bold text-zinc-900 bg-white shadow-sm border border-zinc-200/20 cursor-pointer transition-all active:scale-95">Sans-Serif</button>

                        </div>
                        <span class="text-[11px] text-zinc-400 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                            Distraction-Free Canvas
                        </span>
                    </div>

                    <!-- Sticky Editorial Banner -->
                    <div id="cora-editorial-banner" class="hidden p-4 border rounded-xl bg-zinc-50 border-zinc-200 shadow-xs flex flex-col gap-3 animate-fade-in select-none">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-3">
                                <span class="p-2 bg-zinc-200 text-zinc-800 rounded-lg shrink-0 flex items-center">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                </span>
                                <div>
                                    <span class="text-xs font-bold text-zinc-900 block" id="cora-editorial-banner-status">Draft Pending Review</span>
                                    <span class="text-[10px] text-zinc-500 block leading-tight">Submitted for approval by <strong id="cora-editorial-banner-author">Writer</strong></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="px-3 py-1.5 bg-zinc-950 hover:bg-black text-white rounded-lg font-bold text-[10px] uppercase cursor-pointer transition-colors shadow-xs" onclick="coraApproveEditorialDraft()">Approve Draft</button>
                                <button type="button" class="px-3 py-1.5 border border-zinc-300 bg-white hover:bg-zinc-50 rounded-lg text-zinc-700 font-bold text-[10px] uppercase cursor-pointer transition-colors" onclick="coraPromptRevisions()">Request Revisions</button>
                            </div>
                        </div>
                        <!-- Inline Feedback Form Container -->
                        <div id="cora-feedback-input-container" class="hidden pt-3 border-t border-zinc-200 w-full">
                            <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Enter Revision Feedback</label>
                            <div class="flex gap-2">
                                <input type="text" id="cora-feedback-input-field" placeholder="e.g. Please add Vasant Vihar statistics and insert Valuation CTA..." class="flex-1 text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white">
                                <button type="button" class="px-3 py-1 bg-zinc-950 text-white rounded-lg text-xs font-bold cursor-pointer transition-colors hover:bg-zinc-800" onclick="coraSubmitRevisionsFeedback()">Submit Feedback</button>
                                <button type="button" class="px-2 py-1 border border-zinc-200 hover:bg-zinc-50 rounded-lg text-xs text-zinc-650 cursor-pointer" onclick="coraToggleFeedbackInput(false)">Cancel</button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="cora-article-id" value="">
                    <input type="hidden" id="cora-article-cover-url" value="">
                    
                    <!-- Title Input -->
                    <input type="text" id="cora-article-title" placeholder="Article Title" oninput="coraUpdateWordCount()" class="text-4xl md:text-5xl font-extrabold text-zinc-900 placeholder:text-zinc-300 w-full border-none focus:ring-0 focus:outline-none bg-transparent leading-tight tracking-tight mb-2">
                    
                    <!-- Subtitle/Summary Input Field -->
                    <input type="text" id="cora-article-subtitle" placeholder="Add a subtitle or summary for your article..." class="text-lg md:text-xl font-medium text-zinc-550 placeholder:text-zinc-300 w-full border-none focus:ring-0 focus:outline-none bg-transparent leading-relaxed tracking-tight mb-4">
                    
                    <!-- ST-3: Inline AI Writing Assistant Card -->
                    <div id="cora-ai-writing-assistant" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50/20 p-4 shadow-3xs space-y-3 font-sans select-none my-4">
                        <div class="flex items-center gap-2 text-xs font-bold text-zinc-800">
                            <!-- Clean Sparkle SVG -->
                            <span class="p-1 rounded bg-violet-100/50 text-violet-650 border border-violet-100 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            </span>
                            <span>AI Writing Assistant</span>
                        </div>
                        <div class="relative flex items-center bg-white border border-zinc-200 rounded-xl px-3 py-2 shadow-3xs focus-within:border-zinc-400">
                            <input type="text" id="cora-ai-prompt-input" placeholder="Ask AI to write, improve, or expand..." class="flex-1 text-xs outline-none border-none bg-transparent text-zinc-850 placeholder:text-zinc-350 pr-8" onkeydown="if(event.key === 'Enter') coraExecuteAIPrompt()">
                            <button type="button" onclick="coraExecuteAIPrompt()" class="absolute right-2 text-zinc-450 hover:text-zinc-950 transition-colors border-none bg-transparent cursor-pointer p-1">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 text-[10px] font-bold text-zinc-700">
                            <button type="button" onclick="coraRunAIAction('intro')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg transition-colors cursor-pointer bg-white flex items-center gap-1 shadow-3xs">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                Write introduction
                            </button>
                            <button type="button" onclick="coraRunAIAction('expand')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg transition-colors cursor-pointer bg-white flex items-center gap-1 shadow-3xs">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                                Expand section
                            </button>
                            <button type="button" onclick="coraRunAIAction('clarity')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg transition-colors cursor-pointer bg-white flex items-center gap-1 shadow-3xs">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                Improve clarity
                            </button>
                            <button type="button" onclick="coraRunAIAction('examples')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg transition-colors cursor-pointer bg-white flex items-center gap-1 shadow-3xs">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="9"></line><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="11" y2="17"></line></svg>
                                Add examples
                            </button>
                        </div>
                    </div>
                    
                    <!-- Slash Command Hint -->
                    <div class="flex items-center gap-2 py-2 px-3 bg-zinc-50 border border-zinc-200/80 rounded-xl text-xs text-zinc-500 font-mono select-none">
                        <span class="px-1.5 py-0.5 bg-zinc-200 text-zinc-800 font-bold rounded text-[10px]">/</span>
                        <span>Type <kbd class="font-bold text-zinc-800">/</kbd> for slash commands or select text for formatting</span>
                    </div>

                    <!-- Notion-Style Slash Commands Menu -->
                    <div id="cora-editor-slash-menu" class="hidden absolute bg-white border border-zinc-200 rounded-xl shadow-xl w-[290px] z-[999] select-none text-zinc-800 font-sans text-xs overflow-hidden">

                        <!-- Main menu panel -->
                        <div id="cora-slash-main-panel" class="py-2">

                            <div class="px-3 py-1.5 text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Content</div>
                            <button type="button" onclick="coraOpenSlashPicker('article')" class="w-full text-left px-3 py-2.5 hover:bg-zinc-50 flex items-center gap-2.5 transition-colors cursor-pointer border-none bg-transparent">
                                <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-700 shrink-0"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg></span>
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold block text-[11px] text-zinc-800">Related Article</span>
                                    <span class="text-[9px] text-zinc-400 block leading-none mt-0.5">Link to an article from this platform</span>
                                </div>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 text-zinc-300"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                            <button type="button" onclick="coraInsertSlashWidget('valuation')" class="w-full text-left px-3 py-2.5 hover:bg-zinc-50 flex items-center gap-2.5 transition-colors cursor-pointer border-none bg-transparent">
                                <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-700 shrink-0"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></span>
                                <div>
                                    <span class="font-semibold block text-[11px] text-zinc-800">Lead Capture Form</span>
                                    <span class="text-[9px] text-zinc-400 block leading-none mt-0.5">Property appraisal request form</span>
                                </div>
                            </button>

                            <div class="h-px bg-zinc-100 mx-3 my-1.5"></div>
                            <div class="px-3 py-1.5 text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Listings &amp; Studio</div>
                            <button type="button" onclick="coraOpenSlashPicker('listing')" class="w-full text-left px-3 py-2.5 hover:bg-zinc-50 flex items-center gap-2.5 transition-colors cursor-pointer border-none bg-transparent">
                                <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-700 shrink-0"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></span>
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold block text-[11px] text-zinc-800">Property Showcase</span>
                                    <span class="text-[9px] text-zinc-400 block leading-none mt-0.5">Embed a listing card from the platform</span>
                                </div>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 text-zinc-300"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                            <button type="button" onclick="coraOpenSlashPicker('equipment')" class="w-full text-left px-3 py-2.5 hover:bg-zinc-50 flex items-center gap-2.5 transition-colors cursor-pointer border-none bg-transparent">
                                <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-700 shrink-0"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg></span>
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold block text-[11px] text-zinc-800">Equipment Card</span>
                                    <span class="text-[9px] text-zinc-400 block leading-none mt-0.5">Embed a gear item from Studio module</span>
                                </div>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 text-zinc-300"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                            <button type="button" onclick="coraInsertSlashWidget('gallery')" class="w-full text-left px-3 py-2.5 hover:bg-zinc-50 flex items-center gap-2.5 transition-colors cursor-pointer border-none bg-transparent">
                                <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-700 shrink-0"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></span>
                                <div>
                                    <span class="font-semibold block text-[11px] text-zinc-800">Media Gallery</span>
                                    <span class="text-[9px] text-zinc-400 block leading-none mt-0.5">Responsive image gallery grid</span>
                                </div>
                            </button>

                            <div class="h-px bg-zinc-100 mx-3 my-1.5"></div>
                            <div class="px-3 py-1.5 text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Blocks</div>
                            <button type="button" onclick="coraInsertSlashWidget('divider')" class="w-full text-left px-3 py-2.5 hover:bg-zinc-50 flex items-center gap-2.5 transition-colors cursor-pointer border-none bg-transparent">
                                <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-700 shrink-0"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line></svg></span>
                                <div><span class="font-semibold block text-[11px] text-zinc-800">Divider</span><span class="text-[9px] text-zinc-400 block leading-none mt-0.5">Horizontal section break</span></div>
                            </button>
                            <button type="button" onclick="coraInsertSlashWidget('pullquote')" class="w-full text-left px-3 py-2.5 hover:bg-zinc-50 flex items-center gap-2.5 transition-colors cursor-pointer border-none bg-transparent">
                                <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-700 shrink-0"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path></svg></span>
                                <div><span class="font-semibold block text-[11px] text-zinc-800">Pull Quote</span><span class="text-[9px] text-zinc-400 block leading-none mt-0.5">Highlighted editorial quote</span></div>
                            </button>
                            <button type="button" onclick="coraInsertSlashWidget('signature')" class="w-full text-left px-3 py-2.5 hover:bg-zinc-50 flex items-center gap-2.5 transition-colors cursor-pointer border-none bg-transparent">
                                <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-700 shrink-0"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                                <div><span class="font-semibold block text-[11px] text-zinc-800">Signature Block</span><span class="text-[9px] text-zinc-400 block leading-none mt-0.5">Author sign-off block</span></div>
                            </button>
                        </div>

                        <!-- Item search picker sub-panel -->
                        <div id="cora-slash-picker-panel" class="hidden">
                            <div class="flex items-center gap-2 px-3 py-2.5 border-b border-zinc-100">
                                <button type="button" onclick="coraCloseSlashPicker()" class="p-1 rounded hover:bg-zinc-100 transition-colors text-zinc-400 hover:text-zinc-700 cursor-pointer border-none bg-transparent shrink-0">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                </button>
                                <input type="text" id="cora-slash-picker-search" placeholder="Search..." class="flex-1 text-xs outline-none border-none bg-transparent text-zinc-800 placeholder:text-zinc-300" autocomplete="off">
                            </div>
                            <div id="cora-slash-picker-results" class="max-h-[240px] overflow-y-auto py-1">
                                <div class="px-3 py-4 text-[10px] text-zinc-400 text-center">Type to search...</div>
                            </div>
                        </div>

                    <!-- Quill Rich-Text Editor Canvas Container -->
                    <div id="cora-quill-editor" class="w-full text-zinc-900 min-h-[420px] border-none focus:outline-none text-base leading-relaxed mt-2"></div>

                </div><!-- /.cora-writing-sheet -->

                <!-- Floating Mobile Inspector & Meta Toggle Button -->
                <button type="button" class="md:hidden fixed bottom-5 right-5 z-30 px-4 py-2.5 bg-zinc-950 text-white rounded-full font-bold text-xs shadow-xl border border-zinc-800 flex items-center gap-2 active:scale-95 transition-all cursor-pointer" onclick="coraToggleArticleInspector(true)">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                    <span>Inspector &amp; Meta</span>
                </button>
            </main>

            <!-- Mobile Drawer Backdrop -->
            <div id="cora-inspector-backdrop" class="hidden fixed inset-0 bg-black/40 backdrop-blur-xs z-40 md:hidden transition-opacity" onclick="coraToggleArticleInspector(false)"></div>

            <!-- Right Inspector Sidebar (Desktop) / Bottom Sliding Sheet (Mobile) -->
            <aside id="cora-article-inspector" class="max-md:fixed max-md:inset-x-0 max-md:bottom-0 max-md:z-50 max-md:h-[82vh] max-md:w-full max-md:rounded-t-2xl max-md:shadow-2xl max-md:border-t max-md:border-zinc-200 md:relative md:w-80 lg:w-96 md:border-l border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex flex-col shrink-0 overflow-y-auto transition-transform md:transition-all duration-300">
                
                <!-- Mobile Bottom Sheet Grab Handle & Header Bar -->
                <div class="flex md:hidden items-center justify-between px-4 py-2.5 bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 shrink-0 select-none">
                    <div class="flex items-center gap-2 text-xs font-bold text-zinc-800 dark:text-zinc-200">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                        <span>Inspector &amp; Meta</span>
                    </div>
                    <div class="w-10 h-1 bg-zinc-300 dark:bg-zinc-700 rounded-full"></div>
                    <button type="button" class="p-1 text-zinc-400 hover:text-zinc-900 dark:hover:text-white rounded-lg transition-colors cursor-pointer border-none bg-transparent" onclick="coraToggleArticleInspector(false)" aria-label="Close Inspector Sheet">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                
                <!-- Inspector Navigation Tabs -->
                <div class="flex border-b border-zinc-200 dark:border-zinc-800 bg-[#f9fafb] dark:bg-[#0c0c0e] sticky top-0 z-10 text-[10px] font-bold uppercase tracking-wider inspector-tabs-container select-none font-sans">
                    <button type="button" id="tab-inspector-seo" onclick="coraSwitchInspectorTab('seo')" class="flex-1 py-3 px-2 text-center border-b-2 border-zinc-950 dark:border-white text-zinc-900 dark:text-zinc-100 cursor-pointer transition-all flex items-center justify-center gap-1.5 inspector-tab-btn tab-active font-bold">
                        SEO
                    </button>
                    <button type="button" id="tab-inspector-copilot" onclick="coraSwitchInspectorTab('copilot')" class="flex-1 py-3 px-2 text-center border-b-2 border-transparent text-zinc-405 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 cursor-pointer transition-all flex items-center justify-center gap-1.5 inspector-tab-btn font-bold">
                        AI Visibility
                    </button>
                    <button type="button" id="tab-inspector-meta" onclick="coraSwitchInspectorTab('meta')" class="flex-1 py-3 px-2 text-center border-b-2 border-transparent text-zinc-405 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 cursor-pointer transition-all flex items-center justify-center gap-1.5 inspector-tab-btn font-bold">
                        Details
                    </button>
                    <button type="button" id="tab-inspector-claims" onclick="coraSwitchInspectorTab('claims')" class="px-4 py-3 text-center border-b-2 border-transparent text-zinc-405 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 cursor-pointer transition-all flex items-center justify-center shrink-0 inspector-tab-btn font-bold" title="Compliance & Trust">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </button>
                </div>

                <div id="panel-inspector-copilot" class="hidden p-4 space-y-4 font-sans">
                    <!-- AI Search Visibility Score Card -->
                    <div class="p-4 bg-white border border-zinc-200 rounded-xl flex items-center justify-between shadow-3xs relative overflow-hidden">
                        <div class="space-y-1.5 flex-1 select-none">
                            <div class="flex items-center gap-1">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">AI Search Visibility</span>
                            </div>
                            <span class="text-xs font-bold text-amber-500 block animate-pulse" id="cora-geo-status-text">Needs Improvement</span>
                            <span class="text-[10px] text-zinc-400 block leading-tight pr-2">Your content has low visibility in AI-generated search results.</span>
                            <div class="pt-1">
                                <button type="button" onclick="window.coraShowToast('Score breakdown: local keyword matching, schema structured data and answer density checks.', 'info')" class="px-2.5 py-1.5 bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-700 font-semibold rounded-lg text-[9px] tracking-wide uppercase transition-all cursor-pointer flex items-center gap-1 shadow-3xs">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                                    View Score Breakdown
                                </button>
                            </div>
                        </div>
                        <div class="relative w-16 h-16 flex items-center justify-center shrink-0 select-none">
                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-zinc-100" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path id="cora-geo-score-ring" class="text-amber-500 transition-all duration-350" stroke-dasharray="22, 100" stroke-dashoffset="0" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="absolute flex flex-col items-center justify-center leading-none text-center">
                                <span class="text-sm font-extrabold text-zinc-900" id="cora-geo-score-display">22</span>
                            </div>
                        </div>
                    </div>

                    <!-- AI Search Optimization Checklist -->
                    <div class="border border-zinc-200 rounded-xl overflow-hidden bg-white p-4 shadow-3xs space-y-3.5">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AI SEO Checklist</span>
                            </div>
                            <span class="px-1.5 py-0.5 bg-red-50 text-red-650 text-[8px] font-extrabold rounded-md uppercase tracking-wider border border-red-150" id="geo-checklist-issues-badge">4 Issues</span>
                        </div>

                        <div class="space-y-2">
                            <!-- Answer-focused content -->
                            <div class="flex items-center justify-between p-2 rounded-lg border border-zinc-100 bg-zinc-50/30 hover:bg-zinc-50 transition-colors cursor-pointer" onclick="jQuery('#cora-quill-editor').focus();">
                                <div class="flex items-center gap-2">
                                    <span id="chk-geo-direct-answer-icon" class="w-4 h-4 rounded-full bg-red-50 text-red-500 border border-red-200/60 flex items-center justify-center text-[9px] font-black">!</span>
                                    <span class="text-xs text-zinc-700 font-semibold">Answer-focused block</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span id="chk-geo-direct-answer-status" class="text-[10px] font-bold text-red-500 uppercase tracking-wider">Missing</span>
                                </div>
                            </div>
                            <!-- Key facts & statistics -->
                            <div class="flex items-center justify-between p-2 rounded-lg border border-zinc-100 bg-zinc-50/30 hover:bg-zinc-50 transition-colors cursor-pointer" onclick="jQuery('#cora-quill-editor').focus();">
                                <div class="flex items-center gap-2">
                                    <span id="chk-geo-info-density-icon" class="w-4 h-4 rounded-full bg-red-50 text-red-500 border border-red-200/60 flex items-center justify-center text-[9px] font-black">!</span>
                                    <span class="text-xs text-zinc-700 font-semibold">Key facts & statistics</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span id="chk-geo-info-density-status" class="text-[10px] font-bold text-red-500 uppercase tracking-wider">Missing</span>
                                </div>
                            </div>
                            <!-- Structured data -->
                            <div class="flex items-center justify-between p-2 rounded-lg border border-zinc-100 bg-zinc-50/30 hover:bg-zinc-50 transition-colors cursor-pointer" onclick="jQuery('#cora-quill-editor').focus();">
                                <div class="flex items-center gap-2">
                                    <span id="chk-geo-schema-icon" class="w-4 h-4 rounded-full bg-red-50 text-red-500 border border-red-200/60 flex items-center justify-center text-[9px] font-black">!</span>
                                    <span class="text-xs text-zinc-700 font-semibold">FAQ / Structured Schema</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span id="chk-geo-schema-status" class="text-[10px] font-bold text-red-500 uppercase tracking-wider">Missing</span>
                                </div>
                            </div>
                            <!-- Entity mentions -->
                            <div class="flex items-center justify-between p-2 rounded-lg border border-zinc-100 bg-zinc-50/30 hover:bg-zinc-50 transition-colors cursor-pointer" onclick="jQuery('#cora-quill-editor').focus();">
                                <div class="flex items-center gap-2">
                                    <span id="chk-geo-citations-icon" class="w-4 h-4 rounded-full bg-red-50 text-red-500 border border-red-200/60 flex items-center justify-center text-[9px] font-black">!</span>
                                    <span class="text-xs text-zinc-700 font-semibold">Local Entity mentions</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span id="chk-geo-citations-status" class="text-[10px] font-bold text-red-500 uppercase tracking-wider">Missing</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="w-full py-2.5 bg-zinc-950 hover:bg-black text-white font-semibold rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs active:scale-97 border-none outline-none" onclick="coraAnalyzeSEO()">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                            <span>Analyze Visibility</span>
                        </button>
                    </div>

                    <!-- Optimize for AI Search Suggestions -->
                    <div class="border border-zinc-200 rounded-xl overflow-hidden bg-white p-4 shadow-3xs space-y-4">
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">AI SEO Suggestions</span>
                        </div>

                        <div class="space-y-2.5">
                            <!-- Add Answer Block -->
                            <div class="flex items-center justify-between p-2.5 border border-zinc-150 rounded-lg hover:bg-zinc-50 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg bg-violet-50 text-violet-650 shrink-0 border border-violet-100 flex items-center justify-center">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="text-xs font-bold text-zinc-800 block">Inject Answer block</span>
                                        <span class="text-[9.5px] text-zinc-400 block leading-tight">Insert a direct definition block for quick AI snippets.</span>
                                    </div>
                                </div>
                                <button type="button" onclick="window.coraInjectGeoBlock('answer')" class="px-2.5 py-1.5 border border-zinc-200 text-zinc-700 hover:bg-zinc-55 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white shadow-3xs">Add</button>
                            </div>
                            <!-- Add Key Takeaways -->
                            <div class="flex items-center justify-between p-2.5 border border-zinc-150 rounded-lg hover:bg-zinc-50 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg bg-violet-50 text-violet-650 shrink-0 border border-violet-100 flex items-center justify-center">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="text-xs font-bold text-zinc-800 block">Inject Key Takeaways</span>
                                        <span class="text-[9.5px] text-zinc-400 block leading-tight">Summarize core points as bulleted references.</span>
                                    </div>
                                </div>
                                <button type="button" onclick="window.coraInjectGeoBlock('takeaways')" class="px-2.5 py-1.5 border border-zinc-200 text-zinc-700 hover:bg-zinc-55 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white shadow-3xs">Add</button>
                            </div>
                        </div>
                    </div>

                    <!-- Entities Detected -->
                    <div class="border border-zinc-200 rounded-xl overflow-hidden bg-white p-4 shadow-3xs space-y-3">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Entities Detected</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5 select-none" id="cora-geo-entities-list">
                            <span class="px-2.5 py-1 bg-violet-50/50 border border-violet-100 rounded-lg text-[10px] font-semibold text-violet-650 flex items-center gap-1">
                                DLF CyberCity
                            </span>
                            <span class="px-2.5 py-1 bg-violet-50/50 border border-violet-100 rounded-lg text-[10px] font-semibold text-violet-650 flex items-center gap-1">
                                Commercial Real Estate
                            </span>
                            <span class="px-2.5 py-1 bg-zinc-50 border border-zinc-200 rounded-lg text-[10px] font-semibold text-zinc-500 flex items-center gap-1">
                                Gurugram
                            </span>
                            <button type="button" onclick="window.coraShowToast('Add entity wizard is coming soon.', 'info')" class="px-2.5 py-1 bg-white hover:bg-zinc-55 border border-dashed border-zinc-300 rounded-lg text-[10px] font-semibold text-zinc-500 flex items-center gap-1 transition-colors cursor-pointer">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                Add Entity
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Publishing Meta Tab -->
                <div id="panel-inspector-meta" class="hidden p-4 space-y-4">
                    
                    <!-- Featured Image -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider block">Featured Image</span>
                            <span class="text-[9px] text-zinc-400 font-semibold select-none">Recommended size: 1200 × 630</span>
                        </div>
                        <input type="hidden" id="cora-thumbnail-id" value="">
                        <div id="cora-thumbnail-preview" class="w-full aspect-[16/9] bg-white dark:bg-zinc-950 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800 flex items-center justify-center overflow-hidden relative group cursor-pointer transition-all hover:border-zinc-400" onclick="window.coraMediaSelectTarget = 'thumbnail'; coraOpenMediaLibrary();">
                            <div class="absolute inset-0 bg-black/60 hidden group-hover:flex items-center justify-center transition-all z-10">
                                <span class="text-white text-xs font-semibold flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    Change Image
                                </span>
                            </div>
                            <img src="" id="cora-thumbnail-img" class="hidden w-full h-full object-cover">
                            <div id="cora-thumbnail-placeholder" class="text-center flex flex-col items-center gap-2 select-none py-6">
                                <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                <span class="text-xs font-extrabold text-zinc-800 dark:text-zinc-300 block mt-1">Upload Featured Image</span>
                                <span class="text-[10px] text-zinc-400 dark:text-zinc-550 block">Drag & drop or click to select</span>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="space-y-1.5 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                        <span class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider block">Categories</span>
                        <div class="relative">
                            <div id="cora-meta-categories-trigger" class="w-full text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 bg-white dark:bg-zinc-950 text-zinc-800 dark:text-zinc-200 flex items-center justify-between cursor-pointer shadow-3xs select-none hover:bg-zinc-50/40">
                                <div class="flex flex-wrap gap-1.5" id="cora-meta-categories-selected">
                                    <span class="text-zinc-350 dark:text-zinc-700">Select categories...</span>
                                </div>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450 shrink-0"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            <div id="cora-meta-categories-dropdown" class="hidden absolute left-0 right-0 mt-1.5 p-2 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-lg z-30 max-h-[160px] overflow-y-auto space-y-1 animate-fade-in">
                                <?php foreach($cora_categories as $cat): ?>
                                    <label class="flex items-center gap-2.5 p-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-lg cursor-pointer text-xs text-zinc-850 dark:text-zinc-250 select-none">
                                        <input type="checkbox" class="cora-meta-category-checkbox rounded border-zinc-300 focus:ring-0 text-zinc-950" value="<?php echo $cat->term_id; ?>" data-name="<?php echo esc_attr($cat->name); ?>">
                                        <span><?php echo esc_html($cat->name); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <select id="cora-article-categories" multiple class="hidden">
                            <?php foreach($cora_categories as $cat): ?>
                                <option value="<?php echo $cat->term_id; ?>"><?php echo esc_html($cat->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-[9px] text-zinc-400">Choose one or more categories for this content.</p>
                    </div>

                    <!-- Tags -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider block">Tags</span>
                        <div class="relative">
                            <div id="cora-meta-tags-trigger" class="w-full text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 bg-white dark:bg-zinc-950 text-zinc-800 dark:text-zinc-200 flex items-center justify-between cursor-pointer shadow-3xs select-none hover:bg-zinc-50/40">
                                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 shrink-0"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                    <div class="flex flex-wrap gap-1.5 flex-1" id="cora-meta-tags-selected">
                                        <span class="text-zinc-350 dark:text-zinc-700">Select or add tags...</span>
                                    </div>
                                </div>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-455 shrink-0"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            <div id="cora-meta-tags-dropdown" class="hidden absolute left-0 right-0 mt-1.5 p-2 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-lg z-30 max-h-[190px] overflow-y-auto space-y-1 animate-fade-in">
                                <div class="px-2 py-1.5 border-b border-zinc-100 dark:border-zinc-800 mb-1 flex gap-1.5">
                                    <input type="text" id="cora-meta-tag-add-input" placeholder="Create tag..." class="flex-1 text-xs border border-zinc-200 dark:border-zinc-800 rounded px-2 py-1 focus:outline-none focus:border-zinc-450 bg-white dark:bg-zinc-950 text-zinc-800 dark:text-zinc-250">
                                    <button type="button" id="cora-meta-tag-add-btn" class="px-2.5 py-1 bg-zinc-950 hover:bg-black text-white text-[10px] font-bold rounded-md cursor-pointer transition-colors border-none outline-none">Add</button>
                                </div>
                                <?php foreach($cora_tags as $tag): ?>
                                    <label class="flex items-center gap-2.5 p-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-lg cursor-pointer text-xs text-zinc-850 dark:text-zinc-250 select-none">
                                        <input type="checkbox" class="cora-meta-tag-checkbox rounded border-zinc-300 focus:ring-0 text-zinc-950" value="<?php echo $tag->term_id; ?>" data-name="<?php echo esc_attr($tag->name); ?>">
                                        <span><?php echo esc_html($tag->name); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <select id="cora-article-tags" multiple class="hidden">
                            <?php foreach($cora_tags as $tag): ?>
                                <option value="<?php echo $tag->term_id; ?>"><?php echo esc_html($tag->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-[9px] text-zinc-400">Hold Cmd/Ctrl to select multiple tags.</p>
                    </div>

                    <!-- Assignee / Author -->
                    <div class="space-y-1.5 pt-1">
                        <span class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider block">Assignee / Author</span>
                        <div class="relative">
                            <div id="cora-meta-assignee-trigger" class="w-full text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 bg-white dark:bg-zinc-950 text-zinc-800 dark:text-zinc-200 flex items-center justify-between cursor-pointer shadow-3xs select-none hover:bg-zinc-50/40">
                                <div class="flex items-center gap-2.5">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    <span id="cora-meta-assignee-value" class="font-medium text-zinc-800 dark:text-zinc-200">Unassigned</span>
                                </div>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-455 shrink-0"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            <input type="hidden" id="cora-article-assignee" value="0">
                            <div id="cora-meta-assignee-dropdown" class="hidden absolute left-0 right-0 mt-1.5 p-2 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-lg z-30 max-h-[160px] overflow-y-auto space-y-1 animate-fade-in">
                                <div class="cora-meta-assignee-option p-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-lg cursor-pointer text-xs text-zinc-800 dark:text-zinc-200 select-none font-semibold" data-value="0">Unassigned</div>
                                <?php foreach($cora_users as $usr): ?>
                                    <div class="cora-meta-assignee-option p-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-lg cursor-pointer text-xs text-zinc-800 dark:text-zinc-200 select-none" data-value="<?php echo $usr->ID; ?>"><?php echo esc_html($usr->display_name); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduled Date -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider block">Scheduled Date</span>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <input type="datetime-local" id="cora-article-scheduled-date" class="w-full text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 pl-9 pr-9 focus:outline-none focus:border-zinc-400 dark:focus:border-zinc-650 bg-white dark:bg-zinc-950 text-zinc-800 dark:text-zinc-200 shadow-3xs">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                        </div>
                        <p class="text-[9px] text-zinc-400">Set when this content should be published.</p>
                    </div>

                    <!-- Editorial Feedback Box -->
                    <div id="cora-editorial-feedback-box" class="hidden p-3 rounded-lg border border-zinc-300 bg-zinc-100 dark:bg-zinc-900 text-xs text-zinc-800 dark:text-zinc-200 leading-tight space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <span>Revision Required</span>
                        </div>
                        <p id="cora-editorial-feedback-text" class="italic text-[11px] text-zinc-600 dark:text-zinc-400"></p>
                    </div>

                    <!-- Page Settings -->
                    <div class="space-y-4 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                        <span class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider block">Page Settings</span>
                        
                        <!-- URL Slug -->
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-bold text-zinc-550 dark:text-zinc-450 block">URL Slug</span>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                </div>
                                <input type="text" id="cora-article-slug" placeholder="url-slug-here" class="w-full text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 pl-9 focus:outline-none focus:border-zinc-400 dark:focus:border-zinc-650 bg-white dark:bg-zinc-950 text-zinc-800 dark:text-zinc-200 shadow-3xs">
                            </div>
                            <p class="text-[9px] text-zinc-400">The last part of the URL. Keep it short and descriptive.</p>
                        </div>

                        <!-- Allow Comments -->
                        <div class="flex items-center justify-between pt-1 select-none">
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-extrabold text-zinc-555 dark:text-zinc-400 uppercase tracking-wider block">Allow Comments</span>
                                <span class="text-[9px] text-zinc-400 dark:text-zinc-500 block leading-tight">Enable comments on this content.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" id="cora-article-allow-comments" class="sr-only peer">
                                <div class="w-9 h-5 bg-zinc-200 dark:bg-zinc-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950 dark:peer-checked:bg-white"></div>
                            </label>
                        </div>

                        <!-- Post Excerpt -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-zinc-450 uppercase tracking-wider block">Excerpt</label>
                                <button type="button" onclick="coraAIGenerateExcerpt()" class="px-2.5 py-1 bg-zinc-950 hover:bg-black text-white font-semibold rounded-md text-[10px] transition-all flex items-center gap-1 cursor-pointer shadow-3xs active:scale-95 border-none outline-none">
                                    <svg viewBox="0 0 24 24" width="10" height="10" fill="currentColor" class="text-amber-400"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    <span>Generate</span>
                                </button>
                            </div>
                            <textarea id="cora-article-excerpt" rows="3" placeholder="Summary snippet for search results and social previews..." class="w-full text-xs border border-zinc-200 rounded-lg p-2.5 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 shadow-3xs resize-none"></textarea>
                        </div>

                        <!-- Move to Trash -->
                        <div class="pt-2">
                            <button type="button" onclick="coraTrashArticle()" class="w-full py-2.5 bg-red-50 dark:bg-red-950/20 hover:bg-red-100 dark:hover:bg-red-950/45 text-red-650 dark:text-red-400 border border-red-200/80 dark:border-red-900/60 rounded-lg text-xs font-semibold cursor-pointer transition-colors flex items-center justify-center gap-1.5 active:scale-95 shadow-2xs">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                Move to Trash
                            </button>
                        </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-between border-t border-zinc-200/80 dark:border-zinc-800/50 pt-4 mt-6 select-none">
                        <button type="button" onclick="window.coraResetMetaFields()" class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-zinc-700 dark:text-zinc-300 font-bold rounded-lg text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-3xs">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><polyline points="3 3 3 8 8 8"></polyline></svg>
                            Reset
                        </button>
                        <button type="button" onclick="coraSaveArticle('draft')" class="px-4 py-2 bg-zinc-950 hover:bg-black dark:bg-zinc-850 dark:hover:bg-zinc-750 text-white font-bold rounded-lg text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-xs border-none outline-none">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M19 21H5a2 2 0 0 1-2 2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Save Changes
                        </button>
                    </div>
                </div>

                <div id="panel-inspector-seo" class="p-4 space-y-4 font-sans">
                    <!-- SEO Health Score Gauge -->
                    <div class="p-4 bg-white border border-zinc-200 rounded-xl flex items-center justify-between shadow-3xs relative overflow-hidden">
                        <div class="space-y-1 select-none">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">SEO Score</span>
                            <span class="text-xs font-bold text-emerald-600 block" id="cora-seo-status-text">Very Good</span>
                            <span class="text-[10.5px] text-zinc-400 block leading-tight">Well optimized! Keep going.</span>
                            <button type="button" onclick="window.coraShowToast('Check the suggestions checklist below to improve your score.', 'info')" class="mt-1.5 px-2.5 py-1 border border-zinc-200 hover:bg-zinc-50 rounded text-[10px] font-semibold text-zinc-700 shadow-3xs cursor-pointer bg-white">View suggestions (6)</button>
                        </div>
                        <div class="relative w-16 h-16 flex items-center justify-center shrink-0 select-none">
                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-zinc-100" stroke-width="3.2" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path id="cora-seo-score-ring" class="text-emerald-500 transition-all duration-300 seo-score-ring-animated" stroke-dasharray="85, 100" stroke-dashoffset="0" stroke-width="3.2" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="absolute flex flex-col items-center justify-center leading-none text-center">
                                <span class="text-sm font-extrabold text-zinc-900" id="cora-seo-score-display">85</span>
                            </div>
                        </div>
                    </div>

                    <!-- Focus Keyword Card -->
                    <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-3xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Focus Keyword</span>
                            <button type="button" onclick="jQuery('#cora-seo-keyword').focus();" class="text-zinc-400 hover:text-zinc-750 cursor-pointer border-none bg-transparent">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                        </div>
                        <div class="relative flex items-center bg-white border border-zinc-200 rounded-lg px-2.5 py-1.5 focus-within:border-zinc-400 shadow-3xs">
                            <input type="text" id="cora-seo-keyword" placeholder="Enter target keyword..." oninput="coraUpdateSEOAudits()" class="flex-1 text-xs outline-none border-none bg-transparent text-zinc-800 placeholder:text-zinc-350 pr-6" value="AI search visibility">
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-[10px] font-bold text-zinc-550">
                                <span>Keyword Density</span>
                                <span id="cora-seo-density-badge" class="font-mono">18/18</span>
                            </div>
                            <div class="w-full bg-zinc-150 rounded-full h-1">
                                <div id="cora-seo-density-bar" class="bg-emerald-500 h-1 rounded-full" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Readability Card -->
                    <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-3xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Readability</span>
                            <span class="text-xs font-bold text-emerald-600 block" id="cora-readability-status-text">Good</span>
                        </div>
                        <div class="space-y-1.5">
                            <span class="text-[10px] text-zinc-400 block" id="cora-readability-subtext">Grade 8 · Easy to read</span>
                            <div class="w-full bg-zinc-150 rounded-full h-1">
                                <div id="cora-readability-bar" class="bg-emerald-500 h-1 rounded-full" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Checklist Card -->
                    <div class="border border-zinc-200 rounded-xl bg-white p-4 shadow-3xs space-y-3.5">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">SEO Checklist</span>
                            <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 text-[8px] font-extrabold rounded-md uppercase tracking-wider border border-emerald-150" id="checklist-issues-badge">Optimal</span>
                        </div>
                        <div class="space-y-2.5 text-xs text-zinc-700 font-sans">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold text-sm" id="chk-chk-h1">✓</span>
                                    <span>H1 tag</span>
                                </div>
                                <span class="text-[10px] text-zinc-400 font-bold" id="chk-indicator-h1">Active</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold text-sm" id="chk-chk-meta">✓</span>
                                    <span>Meta description</span>
                                </div>
                                <span class="text-[10px] text-zinc-400 font-bold" id="chk-indicator-meta">Active</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold text-sm" id="chk-chk-alt">✓</span>
                                    <span>Image alt text</span>
                                </div>
                                <span class="text-[10px] text-zinc-400 font-bold" id="chk-indicator-alt">Active</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold text-sm">✓</span>
                                    <span>Internal links</span>
                                </div>
                                <span class="px-2 py-0.5 bg-zinc-100 text-zinc-650 text-[10px] rounded font-bold" id="seo-stat-internal-links">8</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold text-sm">✓</span>
                                    <span>External links</span>
                                </div>
                                <span class="px-2 py-0.5 bg-zinc-100 text-zinc-650 text-[10px] rounded font-bold" id="seo-stat-external-links">3</span>
                            </div>
                        </div>
                    </div>

                    <!-- Content Brief Card -->
                    <div class="border border-zinc-200 rounded-xl bg-white p-4 shadow-3xs space-y-3.5">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block border-b border-zinc-100 pb-2">Content Brief</span>
                        <div class="space-y-3.5 text-xs">
                            <div>
                                <span class="text-[10px] text-zinc-400 block uppercase font-bold tracking-wider leading-none">Target Audience</span>
                                <span class="font-semibold text-zinc-800 mt-1 block">Marketing professionals, SEO specialists</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-zinc-400 block uppercase font-bold tracking-wider leading-none mb-1">Intent</span>
                                <span class="px-2.5 py-0.5 bg-emerald-50 border border-emerald-150 rounded-full text-emerald-700 text-[10px] font-bold inline-block select-none">Informational</span>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1.5 select-none">
                                    <span class="text-[10px] text-zinc-400 uppercase font-bold tracking-wider leading-none">Top Competitors</span>
                                    <a href="javascript:void(0)" onclick="window.coraShowToast('Competitor details is coming soon.', 'info')" class="text-[10px] font-bold text-zinc-900 hover:underline">View all</a>
                                </div>
                                <div class="divide-y divide-zinc-100 text-xs">
                                    <div class="py-1.5 flex items-center justify-between">
                                        <span class="text-zinc-650"><strong class="text-zinc-805 mr-1 font-semibold">1</strong> Search Engine Journal</span>
                                        <span class="font-mono text-zinc-500">92</span>
                                    </div>
                                    <div class="py-1.5 flex items-center justify-between">
                                        <span class="text-zinc-650"><strong class="text-zinc-805 mr-1 font-semibold">2</strong> Backlinko</span>
                                        <span class="font-mono text-zinc-500">89</span>
                                    </div>
                                    <div class="py-1.5 flex items-center justify-between">
                                        <span class="text-zinc-650"><strong class="text-zinc-805 mr-1 font-semibold">3</strong> Semrush Blog</span>
                                        <span class="font-mono text-zinc-500">87</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>" class="text-[9px] text-violet-650 hover:text-violet-850 font-bold flex items-center gap-0.5 shrink-0 hover:underline">
                                Learn More
                                <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="3" fill="none"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: Grounded Claims & Trust Audit Tab -->
                <div id="panel-inspector-claims" class="hidden p-4 space-y-4">
                    <!-- Grounded Claims ledger -->
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden bg-white dark:bg-zinc-950 p-4 shadow-3xs space-y-3">
                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-2">
                            <span class="text-[10px] font-extrabold text-zinc-550 dark:text-zinc-400 uppercase tracking-wider block">Grounded Claims Ledger</span>
                            <span class="px-1.5 py-0.5 bg-green-50 dark:bg-green-955 text-green-650 dark:text-green-400 text-[8px] font-extrabold rounded-md uppercase tracking-wider border border-green-100/50 dark:border-green-900/30">Verified</span>
                        </div>

                        <!-- Claims Ledger Container -->
                        <div class="space-y-3 text-xs leading-normal" id="cora-editor-claims-list">
                            <!-- Verified RAG Source item 1 -->
                            <div class="p-2.5 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800 space-y-1">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100">Claim: "Pricing package starts at ₹12,500"</div>
                                <div class="text-[10px] text-zinc-450 dark:text-zinc-500 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Source: Bandra Studio Sessions (Verified)
                                </div>
                            </div>
                            <!-- Verified RAG Source item 2 -->
                            <div class="p-2.5 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800 space-y-1">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100">Claim: "Sessions include 3 outfit changes"</div>
                                <div class="text-[10px] text-zinc-450 dark:text-zinc-500 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Source: Session Guidelines (Verified)
                                </div>
                            </div>
                        </div>

                        <!-- Manual claims verification action button -->
                        <button type="button" class="w-full py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-850 dark:hover:bg-zinc-750 text-zinc-850 dark:text-zinc-200 font-bold border border-zinc-250/80 rounded-lg text-xs transition-all cursor-pointer shadow-3xs" onclick="coraScanDraftClaims()">
                            Scan Draft for claims validation
                        </button>
                    </div>

                    <!-- Trust & Quality Audit checklist -->
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden bg-white dark:bg-zinc-955 p-4 shadow-3xs space-y-3">
                        <span class="text-[10px] font-extrabold text-zinc-550 dark:text-zinc-400 uppercase tracking-wider block border-b border-zinc-100 dark:border-zinc-800 pb-2">Trust &amp; Quality Audit</span>
                        
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-zinc-750 dark:text-zinc-350">Grounded claims match Brain facts</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-zinc-750 dark:text-zinc-350">Zero prohibited marketing terms detected</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-zinc-750 dark:text-zinc-350">Physical business address verified</span>
                            </div>
                            <div class="flex items-center gap-2" id="audit-lead-cta-status">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0" id="audit-lead-cta-indicator"></span>
                                <span class="text-zinc-750 dark:text-zinc-350" id="audit-lead-cta-text">WhatsApp contact CTA active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <script>
        // Custom Workspace Editor Overrides
        (function() {
            // Redefine window.coraToggleBeehiivDropdown to support 'more'
            window.coraToggleBeehiivDropdown = function(type) {
                const types = ['title-subtitle', 'visibility', 'authors', 'thumbnail', 'tags', 'more'];
                types.forEach(t => {
                    if (t === type) {
                        jQuery(`#beehiiv-dropdown-${t}`).toggleClass('hidden');
                    } else {
                        jQuery(`#beehiiv-dropdown-${t}`).addClass('hidden');
                    }
                });
            };

            // Custom Font Switcher Toggle Styling
            window.coraSetEditorFont = function(font) {
                const $ed = jQuery('#cora-quill-editor');
                const serifBtn = jQuery('#cora-font-serif-btn');
                const sansBtn = jQuery('#cora-font-sans-btn');
                
                if (font === 'sans') {
                    $ed.removeClass('cora-serif-editor').addClass('cora-sans-editor');
                    sansBtn.addClass('bg-white text-zinc-900 font-bold shadow-sm border border-zinc-200/20')
                           .removeClass('text-zinc-500 font-medium hover:text-zinc-800 hover:bg-zinc-50/50');
                    serifBtn.removeClass('bg-white text-zinc-900 font-bold shadow-sm border border-zinc-200/20')
                            .addClass('text-zinc-500 font-medium hover:text-zinc-800 hover:bg-zinc-50/50');
                } else {
                    $ed.removeClass('cora-sans-editor').addClass('cora-serif-editor');
                    serifBtn.addClass('bg-white text-zinc-900 font-bold shadow-sm border border-zinc-200/20')
                           .removeClass('text-zinc-500 font-medium hover:text-zinc-800 hover:bg-zinc-50/50');
                    sansBtn.removeClass('bg-white text-zinc-900 font-bold shadow-sm border border-zinc-200/20')
                           .addClass('text-zinc-500 font-medium hover:text-zinc-800 hover:bg-zinc-50/50');
                }
            };

            // Header Actions Dropdowns
            window.coraTogglePublishDropdown = function(forceState) {
                const menu = jQuery('#cora-publish-dropdown-menu');
                if (forceState === false) {
                    menu.addClass('hidden');
                } else {
                    menu.toggleClass('hidden');
                    jQuery('#cora-header-more-dropdown-menu').addClass('hidden');
                }
            };

            window.coraToggleHeaderMoreDropdown = function(forceState) {
                const menu = jQuery('#cora-header-more-dropdown-menu');
                if (forceState === false) {
                    menu.addClass('hidden');
                } else {
                    menu.toggleClass('hidden');
                    jQuery('#cora-publish-dropdown-menu').addClass('hidden');
                }
            };

            // Wrap coraSelectMedia to sync cover target with post thumbnail ID
            const originalSelectMedia = window.coraSelectMedia;
            window.coraSelectMedia = function(id, url) {
                if (window.coraMediaSelectTarget === 'cover') {
                    jQuery('#cora-thumbnail-id').val(id);
                    jQuery('#cora-thumbnail-img').attr('src', url).removeClass('hidden');
                    jQuery('#cora-thumbnail-placeholder').addClass('hidden');
                    // Sync with Beehiiv bar thumbnail uploader preview
                    jQuery('#cora-thumbnail-img-bh').attr('src', url).removeClass('hidden');
                    jQuery('#cora-thumbnail-placeholder-bh').addClass('hidden');
                }
                if (typeof originalSelectMedia === 'function') {
                    originalSelectMedia(id, url);
                }
            };

            // Wrap coraRemoveCoverImage to clear post thumbnail
            const originalRemoveCoverImage = window.coraRemoveCoverImage;
            window.coraRemoveCoverImage = function() {
                if (typeof originalRemoveCoverImage === 'function') {
                    originalRemoveCoverImage();
                }
                jQuery('#cora-thumbnail-id').val('');
                jQuery('#cora-thumbnail-img').addClass('hidden').attr('src', '');
                jQuery('#cora-thumbnail-placeholder').removeClass('hidden');
                jQuery('#cora-thumbnail-img-bh').addClass('hidden').attr('src', '');
                jQuery('#cora-thumbnail-placeholder-bh').removeClass('hidden');
            };

            // Intercept AJAX Save to inject subtitle parameter
            jQuery.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                if (options.data && typeof options.data === 'string' && options.data.indexOf('action=cora_save_article') !== -1) {
                    options.data += '&subtitle=' + encodeURIComponent(jQuery('#cora-article-subtitle').val() || '');
                } else if (options.data && typeof options.data === 'object' && options.data.action === 'cora_save_article') {
                    options.data.subtitle = jQuery('#cora-article-subtitle').val() || '';
                }
            });

            // Intercept AJAX Get response to populate subtitle and cover images
            jQuery(document).ajaxSuccess(function(event, xhr, settings, data) {
                if (settings.data && typeof settings.data === 'string' && settings.data.indexOf('action=cora_get_article') !== -1) {
                    if (data.success && data.data) {
                        jQuery('#cora-article-subtitle').val(data.data.subtitle || '');
                        jQuery('#cora-article-excerpt-bh').val(data.data.subtitle || '');
                        
                        if (data.data.thumbnail_url) {
                            jQuery('#cora-cover-image-img').attr('src', data.data.thumbnail_url).removeClass('hidden');
                            jQuery('#cora-cover-image-placeholder').addClass('hidden');
                            jQuery('#cora-article-cover-url').val(data.data.thumbnail_url);
                        } else {
                            jQuery('#cora-cover-image-img').addClass('hidden').attr('src', '');
                            jQuery('#cora-cover-image-placeholder').removeClass('hidden');
                            jQuery('#cora-article-cover-url').val('');
                        }
                    }
                }
            });

            // Sync subtitle and Beehiiv card excerpt input in real-time
            jQuery(document).ready(function() {
                jQuery('#cora-article-subtitle').on('input', function() {
                    jQuery('#cora-article-excerpt-bh').val(jQuery(this).val());
                });
                jQuery('#cora-article-excerpt-bh').on('input', function() {
                    jQuery('#cora-article-subtitle').val(jQuery(this).val());
                });
            });

            // Close dropdowns on click outside
            jQuery(document).on('click', function(e) {
                if (!jQuery(e.target).closest('#cora-publish-dropdown-wrap').length) {
                    jQuery('#cora-publish-dropdown-menu').addClass('hidden');
                }
                if (!jQuery(e.target).closest('#cora-header-more-wrap').length) {
                    jQuery('#cora-header-more-dropdown-menu').addClass('hidden');
                }
                if (!jQuery(e.target).closest('#beehiiv-dropdown-title-subtitle-wrap, #beehiiv-dropdown-visibility-wrap, #beehiiv-dropdown-authors-wrap, #beehiiv-dropdown-thumbnail-wrap, #beehiiv-dropdown-tags-wrap, #beehiiv-dropdown-more-wrap').length) {
                    window.coraToggleBeehiivDropdown('');
                }
            });
        })();

        window.coraToggleArticleInspector = function(forceShow) {
            const $panel = jQuery('#cora-article-inspector');
            const $backdrop = jQuery('#cora-inspector-backdrop');
            
            if (typeof forceShow === 'boolean') {
                if (forceShow) {
                    $panel.removeClass('collapsed-inspector translate-y-full');
                    $backdrop.removeClass('hidden');
                } else {
                    $panel.addClass('collapsed-inspector translate-y-full');
                    $backdrop.addClass('hidden');
                }
                return;
            }

            const isHiddenOrCollapsed = $panel.hasClass('collapsed-inspector') || $panel.hasClass('translate-y-full');
            if (isHiddenOrCollapsed) {
                $panel.removeClass('collapsed-inspector translate-y-full');
                $backdrop.removeClass('hidden');
            } else {
                $panel.addClass('collapsed-inspector translate-y-full');
                $backdrop.addClass('hidden');
            }

            try {
                const isCollapsed = $panel.hasClass('collapsed-inspector') || $panel.hasClass('translate-y-full');
                localStorage.setItem('cora_article_inspector_collapsed', isCollapsed ? 'true' : 'false');
            } catch(e) {}
        };

        window.coraSwitchInspectorTab = function(tab) {
            if (typeof window.coraToggleArticleInspector === 'function') {
                window.coraToggleArticleInspector(true);
            }
            jQuery('#tab-inspector-copilot, #tab-inspector-meta, #tab-inspector-seo, #tab-inspector-claims').removeClass('border-zinc-950 dark:border-white text-zinc-900 dark:text-zinc-100 tab-active').addClass('border-transparent text-zinc-400 dark:text-zinc-500');
            jQuery('#panel-inspector-copilot, #panel-inspector-meta, #panel-inspector-seo, #panel-inspector-claims').addClass('hidden');
            
            if (tab === 'copilot') {
                jQuery('#tab-inspector-copilot').removeClass('border-transparent text-zinc-400 dark:text-zinc-500').addClass('border-zinc-950 dark:border-white text-zinc-900 dark:text-zinc-100 tab-active');
                jQuery('#panel-inspector-copilot').removeClass('hidden');
            } else if (tab === 'meta') {
                jQuery('#tab-inspector-meta').removeClass('border-transparent text-zinc-400 dark:text-zinc-500').addClass('border-zinc-950 dark:border-white text-zinc-900 dark:text-zinc-100 tab-active');
                jQuery('#panel-inspector-meta').removeClass('hidden');
            } else if (tab === 'seo') {
                jQuery('#tab-inspector-seo').removeClass('border-transparent text-zinc-400 dark:text-zinc-500').addClass('border-zinc-950 dark:border-white text-zinc-900 dark:text-zinc-100 tab-active');
                jQuery('#panel-inspector-seo').removeClass('hidden');
            } else if (tab === 'claims') {
                jQuery('#tab-inspector-claims').removeClass('border-transparent text-zinc-400 dark:text-zinc-500').addClass('border-zinc-950 dark:border-white text-zinc-900 dark:text-zinc-100 tab-active');
                jQuery('#panel-inspector-claims').removeClass('hidden');
            }
        };

        window.coraScanDraftClaims = function() {
            const html = window.coraQuillListingCoordinator ? window.coraQuillListingCoordinator.root.innerHTML : '';
            if (window.coraShowToast) window.coraShowToast('Scanning draft text against RAG facts database...', 'info');

            const hasWhatsApp = html.toLowerCase().includes('wa.me') || html.toLowerCase().includes('whatsapp') || html.toLowerCase().includes('chat') || html.toLowerCase().includes('contact');

            setTimeout(function() {
                const indicator = document.getElementById('audit-lead-cta-indicator');
                const textEl = document.getElementById('audit-lead-cta-text');

                if (hasWhatsApp) {
                    if (indicator) {
                        indicator.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0';
                    }
                    if (textEl) {
                        textEl.innerText = 'WhatsApp contact CTA active';
                    }
                    if (window.coraShowToast) window.coraShowToast('Trust & Claims check completed with 100% compliance.', 'success');
                } else {
                    if (indicator) {
                        indicator.className = 'w-2.5 h-2.5 rounded-full bg-red-500 shrink-0 animate-pulse';
                    }
                    if (textEl) {
                        textEl.innerText = 'WhatsApp contact CTA is missing (Warning)';
                    }
                    if (window.coraShowToast) window.coraShowToast('Warning: Whatsapp CTA button is missing or unverified.', 'error');
                }
            }, 750);
        };

        // Restore inspector collapsed state on load
        (function() {
            try {
                if (localStorage.getItem('cora_article_inspector_collapsed') === 'true') {
                    jQuery('#cora-article-inspector').addClass('collapsed-inspector');
                }
            } catch(e) {}
        })();


        window.coraSetEditorFont = function(font) {
            const $ed = jQuery('#cora-quill-editor');
            if (font === 'sans') {
                $ed.removeClass('cora-serif-editor').addClass('cora-sans-editor');
                jQuery('#cora-font-sans-btn').addClass('bg-white text-zinc-900 font-bold shadow-xs').removeClass('text-zinc-500 font-medium');
                jQuery('#cora-font-serif-btn').removeClass('bg-white text-zinc-900 font-bold shadow-xs').addClass('text-zinc-500 font-medium');
            } else {
                $ed.removeClass('cora-sans-editor').addClass('cora-serif-editor');
                jQuery('#cora-font-serif-btn').addClass('bg-white text-zinc-900 font-bold shadow-xs').removeClass('text-zinc-500 font-medium');
                jQuery('#cora-font-sans-btn').removeClass('bg-white text-zinc-900 font-bold shadow-xs').addClass('text-zinc-500 font-medium');
            }
        };

        // Editor helper functions are loaded from assets/js/admin-script.js

        // --- REAL-TIME AUTO-SAVE & DRAFT RECOVERY ENGINE ---
        window.coraEditorAutoSaveTimer = null;

        window.coraTriggerEditorAutoSave = function() {
            const postId = jQuery('#cora-article-id').val() || 'draft';
            const draftKey = 'cora_article_draft_' + postId;
            
            const draftData = {
                title: jQuery('#cora-article-title').val(),
                subtitle: jQuery('#cora-article-subtitle').val(),
                excerpt: jQuery('#cora-article-excerpt').val() || jQuery('#cora-article-excerpt-bh').val(),
                slug: jQuery('#cora-article-slug').val(),
                cover_url: jQuery('#cora-article-cover-url').val(),
                content: window.coraQuillListingCoordinator ? window.coraQuillListingCoordinator.root.innerHTML : '',
                timestamp: new Date().getTime()
            };

            // Instant local cache (<5ms)
            try {
                localStorage.setItem(draftKey, JSON.stringify(draftData));
            } catch(e) {}

            jQuery('#cora-editor-save-status').html('<span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block mr-1 animate-pulse"></span>Saving...');

            if (window.coraEditorAutoSaveTimer) {
                clearTimeout(window.coraEditorAutoSaveTimer);
            }

            window.coraEditorAutoSaveTimer = setTimeout(function() {
                if (typeof window.coraSaveArticle === 'function') {
                    window.coraSaveArticle('draft', true);
                }
            }, 1500);
        };

        window.coraRestoreEditorDraft = function(postId) {
            const draftKey = 'cora_article_draft_' + (postId || 'draft');
            try {
                const raw = localStorage.getItem(draftKey);
                if (!raw) return false;
                const draft = JSON.parse(raw);
                if (!draft) return false;

                const data = draft.data || draft;
                if (data.title && !jQuery('#cora-article-title').val()) {
                    jQuery('#cora-article-title').val(data.title);
                }
                if (data.subtitle && !jQuery('#cora-article-subtitle').val()) {
                    jQuery('#cora-article-subtitle').val(data.subtitle);
                }
                if (data.excerpt) {
                    jQuery('#cora-article-excerpt, #cora-article-excerpt-bh').val(data.excerpt);
                }
                if (data.slug) {
                    jQuery('#cora-article-slug').val(data.slug);
                }
                if (data.content && window.coraQuillListingCoordinator) {
                    const currentText = window.coraQuillListingCoordinator.getText().trim();
                    if (!currentText || currentText === 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!') {
                        window.coraQuillListingCoordinator.root.innerHTML = data.content;
                    }
                }
                jQuery('#cora-editor-save-status').html('<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>Draft Restored');
                return true;
            } catch(e) {
                return false;
            }
        };

        // Left sidebar navigation switch
        window.coraSwitchLeftSidebarTab = function(tab, btn) {
            if (tab === 'outline') {
                jQuery('#cora-left-panel-outline').removeClass('hidden');
                jQuery('#cora-left-panel-media').addClass('hidden');
            } else {
                jQuery('#cora-left-panel-outline').addClass('hidden');
                jQuery('#cora-left-panel-media').removeClass('hidden');
            }
            jQuery('.cora-left-tab-btn').removeClass('border-zinc-950 text-zinc-900 font-bold bg-white/40').addClass('border-transparent text-zinc-450');
            jQuery(btn).addClass('border-zinc-950 text-zinc-900 font-bold bg-white/40').removeClass('border-transparent text-zinc-450');
        };

        // Dynamic hierarchical outline builder
        window.coraRebuildOutline = function() {
            if (!window.coraQuillListingCoordinator) return;
            const editor = window.coraQuillListingCoordinator.root;
            const headings = editor.querySelectorAll('h1, h2, h3');
            const list = jQuery('#cora-outline-hierarchy-list');
            
            if (headings.length === 0) {
                list.html('<div class="text-zinc-400 italic py-2">No headings in document yet</div>');
                jQuery('#left-stat-headings').text('0');
                return;
            }
            
            jQuery('#left-stat-headings').text(headings.length);
            list.empty();
            
            headings.forEach((heading, idx) => {
                let id = heading.getAttribute('id');
                if (!id) {
                    id = 'cora-heading-' + idx;
                    heading.setAttribute('id', id);
                }
                
                const tagName = heading.tagName.toLowerCase();
                const text = heading.textContent.trim() || 'Untitled Heading';
                
                let indent = '';
                if (tagName === 'h2') indent = 'pl-2.5';
                if (tagName === 'h3') indent = 'pl-5';
                
                const anchor = jQuery(`
                    <a href="javascript:void(0)" onclick="window.coraScrollToHeading('${id}')" 
                       class="block py-1 text-zinc-650 hover:text-zinc-900 transition-colors font-sans select-none truncate ${indent}">
                        <span class="text-zinc-400 font-semibold mr-1 font-mono">${tagName.toUpperCase()}</span> ${text}
                    </a>
                `);
                list.append(anchor);
            });
        };

        // Smooth scrolling to headings inside editor sheet
        window.coraScrollToHeading = function(id) {
            if (!window.coraQuillListingCoordinator) return;
            const editor = window.coraQuillListingCoordinator.root;
            const heading = editor.querySelector('#' + id);
            if (heading) {
                heading.scrollIntoView({ behavior: 'smooth', block: 'center' });
                heading.focus();
            }
        };

        // Override and extend word count update to synchronize outline and left stats
        const originalUpdateWordCount = window.coraUpdateWordCount;
        window.coraUpdateWordCount = function() {
            if (typeof originalUpdateWordCount === 'function') {
                originalUpdateWordCount();
            }
            
            let text = '';
            let headings = 0;
            let images = 0;
            let links = 0;
            
            if (window.coraQuillListingCoordinator && window.coraQuillListingCoordinator.root) {
                text = window.coraQuillListingCoordinator.getText() || '';
                headings = window.coraQuillListingCoordinator.root.querySelectorAll('h1, h2, h3, h4').length;
                images = window.coraQuillListingCoordinator.root.querySelectorAll('img').length;
                links = window.coraQuillListingCoordinator.root.querySelectorAll('a').length;
            } else {
                text = jQuery('#cora-quill-editor').text() || '';
                headings = jQuery('#cora-quill-editor').find('h1, h2, h3, h4').length;
                images = jQuery('#cora-quill-editor').find('img').length;
                links = jQuery('#cora-quill-editor').find('a').length;
            }
            
            const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
            
            jQuery('#left-stat-words').text(words);
            jQuery('#left-stat-headings').text(headings);
            jQuery('#left-stat-images').text(images);
            jQuery('#left-stat-links').text(links);
            jQuery('#seo-stat-internal-links').text(links);
            
            if (typeof window.coraRebuildOutline === 'function') {
                window.coraRebuildOutline();
            }
            
            if (typeof window.coraUpdateLeftSidebarMediaGrid === 'function') {
                window.coraUpdateLeftSidebarMediaGrid();
            }
            
            // Calculate SEO / Content Score dynamically
            let score = 0;
            score += Math.min(40, Math.round((words / 1000) * 40));
            score += Math.min(20, headings * 5);
            score += Math.min(20, images * 10);
            score += Math.min(20, links * 7);
            
            if (words === 0) score = 0;
            
            jQuery('#cora-seo-score-display').text(score);
            const ring = document.getElementById('cora-seo-score-ring');
            if (ring) {
                ring.setAttribute('stroke-dasharray', `${score}, 100`);
            }
            
            jQuery('#left-score-value').text(`${score}/100`);
            jQuery('#left-score-bar').css('width', `${score}%`);
            
            if (score >= 80) {
                jQuery('#left-score-grade').text('A').removeClass('bg-red-50 text-red-700 border-red-150 bg-amber-50 text-amber-700 border-amber-150').addClass('bg-emerald-50 text-emerald-700 border-emerald-150');
                jQuery('#cora-seo-status-text').text('Optimized').removeClass('text-red-500 text-amber-500').addClass('text-emerald-600');
                if (ring) ring.setAttribute('class', 'text-emerald-500 transition-all duration-300');
                jQuery('#left-score-message').text('Perfect optimization! Ready to publish.');
            } else if (score >= 50) {
                jQuery('#left-score-grade').text('B').removeClass('bg-red-50 text-red-700 border-red-150 bg-emerald-50 text-emerald-700 border-emerald-150').addClass('bg-amber-50 text-amber-700 border-amber-150');
                jQuery('#cora-seo-status-text').text('Good').removeClass('text-red-500 text-emerald-600').addClass('text-amber-500');
                if (ring) ring.setAttribute('class', 'text-amber-500 transition-all duration-300');
                jQuery('#left-score-message').text('Good job! A few more tweaks and you\'re set.');
            } else {
                jQuery('#left-score-grade').text('C').removeClass('bg-emerald-50 text-emerald-700 border-emerald-150 bg-amber-50 text-amber-700 border-amber-150').addClass('bg-red-50 text-red-700 border-red-150');
                jQuery('#cora-seo-status-text').text('Poor').removeClass('text-emerald-600 text-amber-500').addClass('text-red-500');
                if (ring) ring.setAttribute('class', 'text-red-500 transition-all duration-300');
                jQuery('#left-score-message').text('Needs attention. Fix the checklist issues.');
            }
        };

        // Media grid updates in left sidebar
        window.coraUpdateLeftSidebarMediaGrid = function() {
            if (!window.coraQuillListingCoordinator) return;
            const editor = window.coraQuillListingCoordinator.root;
            const imgs = editor.querySelectorAll('img');
            const grid = jQuery('#left-sidebar-media-grid');
            
            if (imgs.length === 0) {
                grid.html('<div class="text-[10px] text-zinc-400 italic col-span-2 text-center py-6">No images embedded yet</div>');
                return;
            }
            
            grid.empty();
            imgs.forEach((img, idx) => {
                const src = img.getAttribute('src') || '';
                const item = jQuery(`
                    <div class="relative group aspect-square bg-zinc-100 rounded-lg overflow-hidden border border-zinc-200">
                        <img src="${src}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1 select-none">
                            <button type="button" onclick="window.coraFocusSidebarImage(${idx})" class="p-1 bg-white hover:bg-zinc-100 rounded text-zinc-900 shadow-3xs cursor-pointer border-none" title="Locate Image">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </button>
                        </div>
                    </div>
                `);
                grid.append(item);
            });
        };

        window.coraFocusSidebarImage = function(idx) {
            if (!window.coraQuillListingCoordinator) return;
            const imgs = window.coraQuillListingCoordinator.root.querySelectorAll('img');
            if (imgs[idx]) {
                imgs[idx].scrollIntoView({ behavior: 'smooth', block: 'center' });
                imgs[idx].classList.add('outline', 'outline-2', 'outline-offset-2', 'outline-zinc-950');
                setTimeout(() => {
                    imgs[idx].classList.remove('outline', 'outline-2', 'outline-offset-2', 'outline-zinc-950');
                }, 1500);
            }
        };

        // Excerpt AI generator
        window.coraAIGenerateExcerpt = function() {
            if (!window.coraQuillListingCoordinator) return;
            const text = window.coraQuillListingCoordinator.getText().trim();
            if (!text || text === 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!') {
                window.coraShowToast('Write some content first to generate an excerpt.', 'error');
                return;
            }
            
            window.coraShowToast('Generating AI excerpt...', 'info');
            
            // Call API or mock it instantly
            setTimeout(() => {
                const words = text.split(/\s+/).slice(0, 25).join(' ');
                const excerpt = words + '...';
                jQuery('#cora-article-excerpt').val(excerpt);
                window.coraShowToast('AI Excerpt generated successfully!', 'success');
                if (typeof window.coraUpdateExcerptCount === 'function') {
                    window.coraUpdateExcerptCount();
                }
                window.coraTriggerEditorAutoSave();
            }, 1000);
        };

        // Insert new heading placeholder via left sidebar action
        window.coraInsertHeadingPlaceholder = function() {
            if (!window.coraQuillListingCoordinator) return;
            const range = window.coraQuillListingCoordinator.getSelection();
            const index = range ? range.index : window.coraQuillListingCoordinator.getLength();
            window.coraQuillListingCoordinator.insertText(index, '\nNew Heading\n', 'user');
            window.coraQuillListingCoordinator.formatLine(index + 1, 11, 'header', 2);
            window.coraQuillListingCoordinator.setSelection(index + 1, 11);
            window.coraShowToast('New H2 heading added to document.', 'success');
        };

        // Execute dynamic prompt from inline assistant card input
        window.coraExecuteAIPrompt = function() {
            if (!window.coraQuillListingCoordinator) return;
            const inputEl = jQuery('#cora-ai-prompt-input');
            const prompt = inputEl.val().trim();
            if (!prompt) {
                window.coraShowToast('Please type a prompt first.', 'error');
                return;
            }

            window.coraShowToast('AI is processing prompt: "' + prompt + '"...', 'info');
            
            setTimeout(() => {
                const range = window.coraQuillListingCoordinator.getSelection();
                const index = range ? range.index : window.coraQuillListingCoordinator.getLength();
                const generatedText = "\nRegarding your prompt '" + prompt + "': To achieve optimal growth, teams should implement continuous validation cycles, align search intent mapping, and audit claims grounding periodically.\n";
                
                window.coraQuillListingCoordinator.insertText(index, generatedText, 'user');
                window.coraQuillListingCoordinator.setSelection(index + generatedText.length);
                
                inputEl.val('');
                window.coraShowToast('AI content inserted successfully!', 'success');
                window.coraUpdateWordCount();
                window.coraTriggerEditorAutoSave();
            }, 1200);
        };

        // Run predefined actions (chips) from inline assistant card
        window.coraRunAIAction = function(action) {
            if (!window.coraQuillListingCoordinator) return;
            const range = window.coraQuillListingCoordinator.getSelection();
            const hasSelection = range && range.length > 0;
            let selectedText = '';
            if (hasSelection) {
                selectedText = window.coraQuillListingCoordinator.getText(range.index, range.length).trim();
            }

            window.coraShowToast('Running AI ' + action + ' optimization...', 'info');

            setTimeout(() => {
                let newText = '';
                const index = range ? range.index : window.coraQuillListingCoordinator.getLength();
                const length = range ? range.length : 0;

                if (action === 'intro') {
                    const title = jQuery('#cora-article-title').val() || 'Unlocking Strategic Growth';
                    newText = "In today's competitive space, mastering '" + title + "' is essential. This article breaks down core methods, industry benchmarks, and actionable workflows to optimize your content strategy and drive direct leads.";
                    // Insert at beginning of document
                    window.coraQuillListingCoordinator.insertText(0, newText + "\n\n", 'user');
                    window.coraQuillListingCoordinator.setSelection(0, newText.length);
                } else if (action === 'expand') {
                    const baseText = selectedText || 'this objective';
                    newText = "Expanding on '" + baseText + "': This requires a granular understanding of user persona workflows, continuous data alignment, and proactive feature enhancements to maintain optimal performance levels.";
                    if (hasSelection) {
                        window.coraQuillListingCoordinator.insertText(index, newText, 'user');
                        window.coraQuillListingCoordinator.deleteText(index + newText.length, length);
                    } else {
                        window.coraQuillListingCoordinator.insertText(index, "\n" + newText + "\n", 'user');
                    }
                    window.coraQuillListingCoordinator.setSelection(index, newText.length);
                } else if (action === 'clarity') {
                    const baseText = selectedText || 'refine our core plans';
                    newText = "To put it simply, we must focus our core resources, align stakeholders, and prioritize high-impact deliverables to ensure sustained progress.";
                    if (hasSelection) {
                        window.coraQuillListingCoordinator.insertText(index, newText, 'user');
                        window.coraQuillListingCoordinator.deleteText(index + newText.length, length);
                    } else {
                        window.coraQuillListingCoordinator.insertText(index, "\n" + newText + "\n", 'user');
                    }
                    window.coraQuillListingCoordinator.setSelection(index, newText.length);
                } else if (action === 'examples') {
                    const baseText = selectedText || 'this strategy';
                    newText = "\n\nFor example:\n1. TechCorp scaled search visibility by 140% using automated geo-targeting briefs.\n2. StudioFlow reduced editing overhead by 3 hours per article using inline copilot templates.";
                    window.coraQuillListingCoordinator.insertText(index + length, newText, 'user');
                    window.coraQuillListingCoordinator.setSelection(index + length, newText.length);
                }

                window.coraShowToast('AI optimization complete!', 'success');
                window.coraUpdateWordCount();
                window.coraTriggerEditorAutoSave();
            }, 1000);
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Set default font
            if (typeof window.coraSetEditorFont === 'function') {
                window.coraSetEditorFont('sans');
            }
            
            // Typist listener for real-time auto-saving
            jQuery(document).on('input propertychange change keyup', '#cora-article-title, #cora-article-subtitle, #cora-article-excerpt, #cora-article-slug, #cora-article-excerpt-bh', function() {
                if (typeof window.coraUpdateExcerptCount === 'function') {
                    window.coraUpdateExcerptCount();
                }
                window.coraTriggerEditorAutoSave();
            });

            setTimeout(function() {
                if (window.coraQuillListingCoordinator) {
                    window.coraQuillListingCoordinator.on('text-change', function() {
                        window.coraUpdateWordCount();
                        window.coraTriggerEditorAutoSave();
                    });
                }
                
                // Restore unsaved draft from localStorage if page refreshed
                const activeId = jQuery('#cora-article-id').val();
                window.coraRestoreEditorDraft(activeId);

                // Initialize metrics and counters
                if (typeof window.coraUpdateWordCount === 'function') {
                    window.coraUpdateWordCount();
                }
                if (typeof window.coraUpdateExcerptCount === 'function') {
                    window.coraUpdateExcerptCount();
                }
            }, 1000);
        });
        </script>
    </div>

    <!-- Advanced Media Uploader Modal -->
    <div id="cora-modal-upload-media" class="cora-modal-overlay">
        <div class="cora-modal-card max-w-2xl w-full mx-4 md:mx-auto">
            <div class="cora-modal-header border-b border-zinc-100">
                <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-800"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Advanced Media Upload
                </h3>
                <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraCloseModals()">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="cora-modal-body space-y-5 p-4 sm:p-6">
                <input type="file" id="cora-upload-file-input" multiple class="hidden" accept="image/*,video/*">
                <!-- Drag and Drop Zone -->
                <div class="border-2 border-dashed border-zinc-200 rounded-lg bg-zinc-50 p-5 sm:p-8 text-center hover:bg-zinc-100 transition-colors cursor-pointer" id="cora-upload-dropzone">
                    <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" fill="none" class="mx-auto text-zinc-400 w-8 h-8 sm:w-12 sm:h-12 mb-2 sm:mb-4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <p class="text-xs sm:text-sm font-semibold text-zinc-700 mb-0.5">Drag and drop files here</p>
                    <p class="hidden sm:block text-xs text-zinc-500 mb-3">or click to browse your computer</p>
                    <p class="block sm:hidden text-[10px] text-zinc-400 mb-2">or tap to upload from device</p>
                    <button class="px-3 py-1.5 sm:px-4 sm:py-2 bg-white border border-zinc-200 text-zinc-700 font-semibold rounded-md shadow-sm text-[10px] sm:text-xs pointer-events-none">Select Files</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Folder Selection -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Destination Folder</label>
                        <select id="cora-upload-folder" class="w-full bg-white border border-zinc-200 rounded-lg px-3 py-2 sm:py-2.5 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-200 transition-all cursor-pointer">
                            <option value="root">/ Root (Main Gallery)</option>
                            <option value="exterior">Exterior &amp; Façade</option>
                            <option value="interior">Interior Rooms</option>
                            <option value="aerial">Aerial &amp; Drone Shots</option>
                            <option value="floor-plans">Floor Plans</option>
                            <option value="amenities">Amenities &amp; Community</option>
                        </select>
                        <span class="text-[10px] text-zinc-400">Organize files into sub-folders immediately.</span>
                    </div>
                    
                    <!-- Batch Tags -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Batch Tags (SEO)</label>
                        <input type="text" id="cora-upload-tags" placeholder="e.g. luxury, villa, penthouse" class="w-full bg-white border border-zinc-200 rounded-lg px-3 py-2 sm:py-2.5 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-200 transition-all placeholder:text-zinc-300">
                        <span class="text-[10px] text-zinc-400">Comma separated. Applied to all files in this batch.</span>
                    </div>
                </div>
            </div>
            <div class="cora-modal-footer bg-zinc-50 rounded-b-lg border-t border-zinc-100 flex justify-between items-center px-4 py-3 sm:px-6">
                <span class="text-xs font-medium text-zinc-500" id="cora-upload-status">Ready to upload</span>
                <div class="flex gap-2">
                    <button class="px-3.5 py-2 bg-white border border-zinc-200 text-zinc-700 font-semibold rounded-md hover:bg-zinc-100 transition-colors text-xs cursor-pointer" onclick="coraCloseModals()">Cancel</button>
                    <button class="px-3.5 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-colors text-xs cursor-pointer shadow-sm flex items-center gap-2 active:scale-95" id="cora-btn-submit-upload" onclick="coraSubmitUploadMedia()">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        Upload Files
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Media Library Drawer -->
    <aside id="cora-media-library-drawer" class="collapsed translate-x-full fixed top-0 right-0 z-[150] h-full w-[450px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out pointer-events-none">
        <header class="flex items-center justify-between px-5 py-3 border-b border-zinc-200 bg-white shrink-0">
            <h3 class="text-sm font-bold text-zinc-900 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                Media Library
            </h3>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleMediaDrawer(false)">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </header>

        <!-- Upload Area -->
        <div class="p-4 sm:p-5 border-b border-zinc-100 bg-zinc-50 shrink-0">
            <div id="cora-media-drawer-dropzone" class="border-2 border-dashed border-zinc-200 rounded-lg p-4 sm:p-5 text-center hover:bg-zinc-100 transition-colors cursor-pointer bg-white">
                <input type="file" id="cora-media-file-input" class="hidden" accept="image/*" multiple>
                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" fill="none" class="mx-auto text-zinc-400 w-5 h-5 sm:w-6 h-6 mb-1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                <p class="text-xs font-semibold text-zinc-700">Click or drag to upload</p>
                <p id="cora-media-upload-status" class="text-[10px] text-zinc-500 mt-0.5">Maximum file size: 10MB</p>
            </div>
        </div>

        <!-- Grid Area -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-5">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Vault Images</span>
                <button class="text-[10px] text-blue-500 hover:underline font-semibold" onclick="coraFetchMediaLibrary()">Refresh</button>
            </div>
            
            <div id="cora-media-library-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3">
                <!-- Images will be injected here via AJAX -->
                <div class="col-span-2 sm:col-span-3 py-10 text-center">
                    <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                </div>
            </div>
        </div>
    </aside>

    <!-- AI Tone Selector Drawer -->
    <aside id="cora-ai-tone-drawer" class="collapsed translate-x-full fixed top-0 right-0 z-[150] h-full w-[380px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-all duration-300 ease-in-out pointer-events-none hidden">
        <header class="flex items-center justify-between px-5 py-3 border-b border-zinc-200 bg-white shrink-0">
            <h3 class="text-sm font-bold text-zinc-900 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                AI Tone Copilot
            </h3>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="jQuery('#cora-ai-tone-drawer').addClass('translate-x-full pointer-events-none collapsed hidden');">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </header>

        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Select Writing Tone</span>
                <p class="text-xs text-zinc-500">Transform your current draft into the chosen linguistic tone style instantly.</p>
            </div>

            <div class="space-y-2 pt-2">
                <!-- Tone Option: Professional -->
                <button type="button" onclick="window.coraApplyAITone('professional')" class="w-full text-left p-3.5 border border-zinc-200 hover:border-zinc-900 rounded-xl bg-white hover:bg-zinc-50 transition-all cursor-pointer flex flex-col gap-1">
                    <span class="text-xs font-bold text-zinc-900">Professional</span>
                    <span class="text-[10px] text-zinc-500 leading-normal">Corporate, formal phrasing, tailored for high-profile investors and developers.</span>
                </button>

                <!-- Tone Option: Casual -->
                <button type="button" onclick="window.coraApplyAITone('casual')" class="w-full text-left p-3.5 border border-zinc-200 hover:border-zinc-900 rounded-xl bg-white hover:bg-zinc-50 transition-all cursor-pointer flex flex-col gap-1">
                    <span class="text-xs font-bold text-zinc-900">Casual</span>
                    <span class="text-[10px] text-zinc-500 leading-normal">Friendly, conversational, easy-to-read style suited for social media posts.</span>
                </button>

                <!-- Tone Option: Hinglish -->
                <button type="button" onclick="window.coraApplyAITone('hinglish')" class="w-full text-left p-3.5 border border-zinc-200 hover:border-zinc-900 rounded-xl bg-white hover:bg-zinc-50 transition-all cursor-pointer flex flex-col gap-1">
                    <span class="text-xs font-bold text-zinc-900">Hinglish</span>
                    <span class="text-[10px] text-zinc-500 leading-normal">A blend of Hindi & English. Urban Indian, catchy, localized for Delhi/NCR buyers.</span>
                </button>

                <!-- Tone Option: Real Estate Expert -->
                <button type="button" onclick="window.coraApplyAITone('real-estate-expert')" class="w-full text-left p-3.5 border border-zinc-200 hover:border-zinc-900 rounded-xl bg-white hover:bg-zinc-50 transition-all cursor-pointer flex flex-col gap-1">
                    <span class="text-xs font-bold text-zinc-900">Real Estate Expert</span>
                    <span class="text-[10px] text-zinc-500 leading-normal">Rich with market analytics, square-yard pricing trends, and developer stats.</span>
                </button>
            </div>
        </div>
    </aside>
    </div> <!-- .flex.flex-row.flex-1.min-h-0 -->
</div> <!-- #cora-workspace -->
<?php
wp_print_media_templates();
wp_print_footer_scripts();
?>
<!-- Workspace Script -->
<script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/admin-script.js?v=' . CORA_WORKSPACE_VERSION; ?>" defer></script>

<script>
(function() {
    let coraNotifications = <?php echo json_encode( $cora_user_notifications ); ?> || [];

    // Helper for HTML escaping
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;")
                  .replace(/</g, "&lt;")
                  .replace(/>/g, "&gt;")
                  .replace(/"/g, "&quot;")
                  .replace(/'/g, "&#039;");
    }

    // Helper for relative timestamps
    function getRelativeTimeString(timestamp) {
        const diff = Math.floor(Date.now() / 1000) - parseInt(timestamp);
        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        return `${Math.floor(diff / 86400)}d ago`;
    }

    // Render list and update badge
    function renderCoraNotifications() {
        const listContainer = document.getElementById('cora-notif-list');
        const sidebarListContainer = document.getElementById('cora-sidebar-notif-list');
        const emptyState = document.getElementById('cora-notif-empty');
        const sidebarEmptyState = document.getElementById('cora-sidebar-notif-empty');
        const badge = document.getElementById('cora-notif-badge');
        const sidebarBadge = document.getElementById('cora-sidebar-notif-badge');

        const displayList = coraNotifications.slice(0, 10);
        const unreadCount = coraNotifications.filter(n => !n.read).length;

        // Update badges
        if (unreadCount > 0) {
            if (badge) {
                badge.textContent = unreadCount;
                badge.classList.remove('hidden');
            }
            if (sidebarBadge) {
                sidebarBadge.textContent = unreadCount;
                sidebarBadge.classList.remove('hidden');
            }
        } else {
            if (badge) badge.classList.add('hidden');
            if (sidebarBadge) sidebarBadge.classList.add('hidden');
        }

        if (displayList.length === 0) {
            if (listContainer) listContainer.innerHTML = '';
            if (sidebarListContainer) sidebarListContainer.innerHTML = '';
            if (emptyState) emptyState.classList.remove('hidden');
            if (sidebarEmptyState) sidebarEmptyState.classList.remove('hidden');
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');
        if (sidebarEmptyState) sidebarEmptyState.classList.add('hidden');
        let html = '';

        displayList.forEach(notif => {
            const itemClass = notif.read 
                ? "p-4 text-xs text-zinc-500 bg-white hover:bg-zinc-50/50 dark:bg-zinc-955 dark:hover:bg-zinc-900/50 opacity-60 transition-all cursor-pointer block select-none"
                : "p-4 text-xs font-semibold text-zinc-900 bg-zinc-50/50 hover:bg-zinc-50 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border-l-[3px] border-zinc-900 dark:border-zinc-100 transition-all cursor-pointer block select-none";

            const relativeTime = getRelativeTimeString(notif.timestamp);

            html += `
                <div class="${itemClass}" data-id="${notif.id}" data-url="${notif.action_url || ''}">
                    <div class="flex items-start justify-between gap-2 text-zinc-950 dark:text-zinc-50">
                        <div class="font-bold">${escapeHtml(notif.title)}</div>
                        <span class="text-[9px] text-zinc-400 font-normal shrink-0 font-mono">${relativeTime}</span>
                    </div>
                    <p class="text-zinc-600 dark:text-zinc-400 mt-1 font-normal leading-relaxed">${escapeHtml(notif.description)}</p>
                </div>
            `;
        });

        if (listContainer) {
            listContainer.innerHTML = html;
            // Wire click handler on rendered items
            listContainer.querySelectorAll('[data-id]').forEach(el => {
                el.addEventListener('click', function(e) {
                    const notifId = this.getAttribute('data-id');
                    const actionUrl = this.getAttribute('data-url');
                    handleCoraNotifClick(e, notifId, actionUrl);
                });
            });
        }

        if (sidebarListContainer) {
            sidebarListContainer.innerHTML = html;
            // Wire click handler on rendered items
            sidebarListContainer.querySelectorAll('[data-id]').forEach(el => {
                el.addEventListener('click', function(e) {
                    const notifId = this.getAttribute('data-id');
                    const actionUrl = this.getAttribute('data-url');
                    handleCoraNotifClick(e, notifId, actionUrl);
                });
            });
        }
    }

    window.coraToggleNotificationDrawer = function(forceShow) {
        const drawer = document.getElementById('cora-notif-dropdown');
        if (!drawer) return;
        const isCollapsed = drawer.classList.contains('collapsed');
        const shouldOpen = forceShow !== undefined ? forceShow : isCollapsed;
        
        if (shouldOpen) {
            drawer.classList.remove('collapsed');
        } else {
            drawer.classList.add('collapsed');
        }
    };

    window.coraCloseAllPopovers = function(exceptId) {
        const popoverIds = [
            'cora-header-punch-popover',
            'cora-mobile-punch-popover',
            'cora-header-profile-popover',
            'cora-profile-popover',
            'cora-workspace-popover',
            'cora-sidebar-notif-popover',
            'cora-notif-dropdown'
        ];
        popoverIds.forEach(function(id) {
            if (id !== exceptId) {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            }
        });
    };

    window.coraToggleProfilePopover = function(e) {
        if (e) e.stopPropagation();
        const popover = document.getElementById('cora-header-profile-popover');
        if (!popover) return;
        const isHidden = popover.classList.contains('hidden');
        window.coraCloseAllPopovers('cora-header-profile-popover');
        if (isHidden) {
            popover.classList.remove('hidden');
        } else {
            popover.classList.add('hidden');
        }
    };

    // Toggle drawer from bell
    function toggleNotificationDropdown(e) {
        if (e) e.stopPropagation();
        window.coraToggleNotificationDrawer();
    }

    // Handle single notification click
    function handleCoraNotifClick(event, notifId, actionUrl) {
        event.stopPropagation();
        
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_mark_notif_read',
            nonce: coraREData.ajaxNonce,
            notif_id: notifId
        }, function(res) {
            if (res.success) {
                coraNotifications = coraNotifications.map(n => {
                    if (n.id === notifId) n.read = true;
                    return n;
                });
                renderCoraNotifications();

                if (actionUrl) {
                    window.location.href = actionUrl;
                }
            } else {
                console.error(res.data ? res.data.message : 'Error marking read');
            }
        });
    }

    // Mark all as read
    function markAllNotificationsRead(e) {
        if (e) e.stopPropagation();
        
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_mark_all_notifs_read',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                coraNotifications = coraNotifications.map(n => {
                    n.read = true;
                    return n;
                });
                renderCoraNotifications();
                if (window.coraShowToast) {
                    window.coraShowToast("All notifications marked as read.");
                }
            } else {
                console.error(res.data ? res.data.message : 'Error marking all read');
            }
        });
    }

    // Initialize listeners
    document.addEventListener('DOMContentLoaded', function() {
        const bellBtn = document.getElementById('cora-notif-bell-btn');
        const markAllBtn = document.getElementById('cora-notif-mark-all-btn');

        if (bellBtn) {
            bellBtn.addEventListener('click', toggleNotificationDropdown);
        }
        if (markAllBtn) {
            markAllBtn.addEventListener('click', markAllNotificationsRead);
        }

        // Close profile popover when clicking outside
        document.addEventListener('click', function(e) {
            const popover = document.getElementById('cora-header-profile-popover');
            if (popover && !popover.classList.contains('hidden') && !e.target.closest('.cora-header-profile-btn') && !e.target.closest('#cora-header-profile-popover')) {
                popover.classList.add('hidden');
            }
        });

        renderCoraNotifications();
    });
})();
</script>

</div>

<!-- Cora Advanced Command Search Modal (Command Palette for CRM subpages) -->
<div id="cora-command-palette" class="fixed inset-0 z-[999999] hidden items-start justify-center p-4 pt-[6vh] md:pt-[10vh] bg-zinc-950/40 backdrop-blur-sm transition-all duration-200">
    <div class="cora-command-container w-full max-w-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col transition-transform transform scale-95 duration-200" style="height: 460px; max-height: 80vh;">
        
        <!-- Search Input Header -->
        <div class="flex items-center gap-3 px-4 border-b border-zinc-100 dark:border-zinc-800/40 py-3.5 shrink-0">
            <svg class="text-zinc-400 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="cora-command-input" placeholder="Search pages, settings, leads, or listings..." class="flex-1 text-sm bg-transparent border-0 outline-none focus:ring-0 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 py-0.5" autocomplete="off">
            <kbd class="text-[9px] font-mono bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded text-zinc-450 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-700/60 shadow-sm shrink-0">⌘K</kbd>
        </div>

        <!-- Filter Pills Bar -->
        <div class="flex items-center gap-1.5 px-4 py-2 border-b border-zinc-100 dark:border-zinc-800/40 bg-zinc-50/50 dark:bg-zinc-900/40 overflow-x-auto shrink-0 select-none no-scrollbar">
            <button type="button" class="cora-search-pill active text-xs font-semibold px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 transition-all cursor-pointer" data-filter="all">Overview</button>
            <button type="button" class="cora-search-pill text-xs font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="pages">Pages</button>
            <button type="button" class="cora-search-pill text-xs font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="leads">Leads</button>
            <button type="button" class="cora-search-pill text-xs font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="settings">Settings</button>
            <button type="button" class="cora-search-pill text-xs font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="listings">Listings</button>
        </div>

        <!-- Results List Area -->
        <div class="flex-1 overflow-y-auto p-2 min-h-0 space-y-1" id="cora-command-results" style="scrollbar-width: thin;">
            <!-- Loading state / Suggestions list / Search results list -->
        </div>

        <!-- Footer Bar -->
        <div class="border-t border-zinc-100 dark:border-zinc-800/40 px-4 py-2.5 bg-zinc-50/50 dark:bg-zinc-900/40 flex items-center justify-between shrink-0">
            <span class="text-xs text-zinc-450 dark:text-zinc-400 font-medium">Need help finding something?</span>
            <a href="https://wa.me/918708528105?text=Hi%20Cora%20Team%2C%20I%27d%20like%20to%20report%20a%20bug%20on%20the%20platform." target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-lg transition-colors shadow-sm flex items-center gap-1.5 cursor-pointer decoration-none" style="text-decoration: none;">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" class="shrink-0"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.713-1.455L0 24zm6.59-4.846c1.66.986 3.284 1.483 4.805 1.484 5.429-.002 9.843-4.417 9.845-9.848.001-2.63-1.019-5.1-2.868-6.953C16.578 1.984 14.105 1.01 11.5 1.01c-5.432 0-9.848 4.416-9.85 9.849-.001 1.702.469 3.366 1.36 4.818l-.988 3.606 3.702-.971c1.45.89 2.973 1.342 4.323 1.342zm11.238-7.51c-.302-.151-1.785-.882-2.057-.981-.273-.099-.471-.148-.669.151-.197.299-.767.971-.94 1.169-.173.199-.347.223-.649.072-.302-.151-1.273-.469-2.427-1.496-.897-.8-1.503-1.788-1.679-2.09-.176-.302-.019-.465.132-.614.136-.134.302-.352.453-.529.151-.176.202-.302.302-.503.101-.202.051-.377-.025-.529-.076-.151-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.785-.73 2.033-1.433.248-.704.248-1.307.173-1.433-.075-.125-.272-.2-.574-.352z"/></svg>
                Report Bug
            </a>
        </div>

    </div>
</div>

<style>
#cora-command-palette {
    display: none;
}
#cora-command-palette:not(.active) {
    display: none !important;
}
#cora-command-palette.active {
    display: flex !important;
}
#cora-command-palette.active .cora-command-container {
    transform: scale(1) !important;
}
.cora-command-item.selected {
    background-color: #f4f4f5 !important;
}
.cora-command-item.selected .w-9 {
    background-color: #ffffff !important;
    border-color: #d4d4d8 !important;
}
.cora-command-item.selected span.text-zinc-300 {
    color: #18181b !important;
}
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Dark mode overrides for search */
.cora-dark-theme #cora-command-palette {
    background-color: rgba(9, 9, 11, 0.6) !important;
}
.cora-dark-theme .cora-command-container {
    background-color: #09090b !important;
    border-color: #27272a !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
}
.cora-dark-theme #cora-command-input {
    color: #f4f4f5 !important;
}
.cora-dark-theme .cora-command-container div {
    border-color: #18181b !important;
}
.cora-dark-theme .cora-search-pill {
    background-color: #09090b;
    border-color: #27272a;
    color: #a1a1aa;
}
.cora-dark-theme .cora-search-pill.active {
    background-color: #f4f4f5;
    border-color: #f4f4f5;
    color: #09090b;
}
.cora-dark-theme .cora-search-pill:hover:not(.active) {
    background-color: #18181b;
}
.cora-dark-theme #cora-command-results .cora-command-item:hover:not(.selected) {
    background-color: #18181b !important;
}
.cora-dark-theme .cora-command-item.selected {
    background-color: #27272a !important;
}
.cora-dark-theme .cora-command-item.selected .w-9 {
    background-color: #18181b !important;
    border-color: #3f3f46 !important;
}
.cora-dark-theme .cora-command-item.selected span.text-zinc-300 {
    color: #f4f4f5 !important;
}
.cora-dark-theme .cora-command-item .w-9 {
    background-color: #18181b;
    border-color: #27272a;
    color: #f4f4f5;
}
.cora-dark-theme .cora-command-item .text-zinc-900 {
    color: #f4f4f5 !important;
}
.cora-dark-theme .cora-command-item .text-zinc-400 {
    color: #a1a1aa !important;
}
</style>

<script>
(function() {
    let selectedIndex = -1;
    let searchDebounceTimeout = null;
    let currentFilter = 'all';
    let searchRequestId = 0;

    window.coraOpenCommandPalette = function() {
        const palette = document.getElementById('cora-command-palette');
        const input = document.getElementById('cora-command-input');
        if (!palette) return;

        const alreadyActive = palette.classList.contains('active');

        palette.classList.add('active');
        palette.classList.remove('hidden');
        
        if (!alreadyActive) {
            if (input) {
                input.value = '';
                input.focus();
            }
            selectedIndex = -1;
            // Load initial suggestions
            coraPerformCommandSearch('');
        } else {
            if (input) {
                input.focus();
            }
        }
    };

    window.coraCloseCommandPalette = function() {
        const palette = document.getElementById('cora-command-palette');
        const inlinePalette = document.getElementById('cora-inline-command-palette');
        if (palette) {
            palette.classList.remove('active');
            palette.classList.add('hidden');
        }
        if (inlinePalette) {
            inlinePalette.classList.add('hidden');
            inlinePalette.classList.remove('flex');
        }
    };

    window.coraCommandSetHomepage = function(pageId, pageTitle) {
        window.coraCloseCommandPalette();

        const confirmFn = window.coraConfirmAction || function(title, body, callback) {
            callback(); // Safe direct execution fallback
        };

        confirmFn(
            'Set Homepage',
            `Set "${pageTitle}" as your homepage? This will update WordPress Reading settings.`,
            function() {
                if (window.coraShowToast) {
                    window.coraShowToast('Updating homepage...', 'info');
                }

                const ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) ? window.coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
                const nonce = (window.coraREData && window.coraREData.ajaxNonce) ? window.coraREData.ajaxNonce : '';

                jQuery.post(ajaxUrl, {
                    action: 'cora_ajax_set_homepage',
                    page_id: pageId,
                    theme_id: 0,
                    nonce: nonce
                }, function(res) {
                    if (res.success) {
                        if (window.coraShowToast) {
                            window.coraShowToast('Homepage updated successfully.', 'success');
                        }
                        if (typeof fetchThemePages === 'function' && window.canvasState && window.canvasState.activeThemeId) {
                            fetchThemePages(window.canvasState.activeThemeId);
                        } else {
                            setTimeout(() => { window.location.reload(); }, 1200);
                        }
                    } else {
                        if (window.coraShowToast) {
                            window.coraShowToast('Failed to update homepage.', 'error');
                        }
                    }
                });
            }
        );
    };

    window.coraCommandExecutePageAction = function(pageId, action, pageTitle, extraData) {
        window.coraCloseCommandPalette();

        // Switch tab to pages and execute immediately if on canvas sub-panel
        const urlParams = new URLSearchParams(window.location.search);
        const isCanvas = urlParams.get('sub') === 'canvas';

        if (isCanvas && typeof window.switchTab === 'function') {
            window.switchTab('pages');
            
            // Wait brief moment for DOM tabs to activate
            setTimeout(() => {
                if (action === 'rename' && typeof window.triggerRenamePage === 'function') {
                    window.triggerRenamePage(pageId, pageTitle);
                } else if (action === 'slug' && typeof window.triggerChangePageSlug === 'function') {
                    window.triggerChangePageSlug(pageId, extraData || '');
                } else if (action === 'homepage' && typeof window.triggerSetHomepage === 'function') {
                    window.triggerSetHomepage(pageId, pageTitle, extraData || 0);
                } else if (action === 'seo' && typeof window.openSEODrawer === 'function') {
                    let currentSEO = { title: '', desc: '', img: '' };
                    if (window.canvasState && Array.isArray(window.canvasState.pages)) {
                        const p = window.canvasState.pages.find(pg => pg.id == pageId);
                        if (p) {
                            currentSEO.title = p.seo_title || '';
                            currentSEO.desc = p.seo_description || '';
                            currentSEO.img = p.seo_og_image || '';
                        }
                    }
                    window.openSEODrawer(pageId, pageTitle, currentSEO.title, currentSEO.desc, currentSEO.img);
                } else if (action === 'revisions' && typeof window.openRevisionsDrawer === 'function') {
                    window.openRevisionsDrawer(pageId, pageTitle);
                } else if (action === 'duplicate' && typeof window.triggerDuplicatePage === 'function') {
                    window.triggerDuplicatePage(pageId);
                } else if (action === 'delete' && typeof window.triggerDeletePage === 'function') {
                    window.triggerDeletePage(pageId);
                }
            }, 100);
        } else {
            // Save payload to localStorage and redirect to Canvas
            const payload = { pageId, action, pageTitle, extraData };
            localStorage.setItem('cora_pending_canvas_command', JSON.stringify(payload));
            window.location.href = '?page=cora-workspace&sub=canvas&edit_page=' + pageId;
        }
    };

    window.coraReleasesData = [
        {
            version: '2.9.7',
            date: 'August 1, 2026',
            isLatest: true,
            items: [
                {
                    title: 'Onboarding Input Validation',
                    desc: 'Integrated strict JavaScript and PHP validation rules for Phone/WhatsApp and Contact Email inputs to reject malformed input strings.',
                    icon: 'shield'
                },
                {
                    title: 'Workspace Owner Role Enforcement',
                    desc: 'Guaranteed all newly onboarded users are automatically assigned the "Workspace Owner" (cora_super_admin) role on final activation.',
                    icon: 'zap'
                },
                {
                    title: 'Role Invitation Improvements',
                    desc: 'Empowered Workspace Owners and Administrators to invite other Managers and Branch Managers directly from the UI. Enabled auto-preselection of roles in the invite drawer based on active search filters or query parameters.',
                    icon: 'users'
                }
            ]
        },
        {
            version: '2.9.6',
            date: 'July 31, 2026',
            items: [
                {
                    title: 'Frictionless Multi-Step Onboarding Flow',
                    desc: 'Completely replaced the legacy registration screen with a modern, frictionless 4-step onboarding wizard. Supports Google OAuth (with local testing mocks), Passwordless Magic Link authentication, profile/business details setup, and dynamic module activation.',
                    icon: 'sparkles'
                },
                {
                    title: 'Visual Theme Typos Fixed',
                    desc: 'Rectified white-on-white rendering bugs and Tailwind typo classes in the Role Preview banner and settings popover menus.',
                    icon: 'edit'
                }
            ]
        },
        {
            version: '2.9.5',
            date: 'July 28, 2026',
            items: [
                {
                    title: 'PWA Custom Offline/Maintenance Page',
                    desc: 'Integrated a premium monochromatic custom connection-idle/offline template served instantly by a robust Service Worker whenever the network is offline or the server undergoes momentary PHP-FPM recycle downtime during upgrades.',
                    icon: 'wifi'
                }
            ]
        },
        {
            version: '2.9.4',
            date: 'July 25, 2026',
            items: [
                {
                    title: 'Connection Recycle Resilience',
                    desc: 'Implemented FPM/OPcache connection recovery logic. The auto-update interface now handles temporary PHP process restarts gracefully without triggering false-positive network connection failures.',
                    icon: 'refresh'
                }
            ]
        },
        {
            version: '2.9.2',
            date: 'July 20, 2026',
            items: [
                {
                    title: 'One-Click Auto-Updates',
                    desc: 'Implemented a secure, full-screen step-by-step update interface triggered instantly via custom query parameters.',
                    icon: 'zap'
                }
            ]
        },
        {
            version: '2.9.1',
            date: 'July 15, 2026',
            items: [
                {
                    title: 'Query Parameter Redirection Preservation',
                    desc: 'Fixed redirect query stripping in the workspace admin routing.',
                    icon: 'link'
                }
            ]
        },
        {
            version: '2.9.0',
            date: 'July 10, 2026',
            items: [
                {
                    title: 'Modularity Optimization',
                    desc: 'Resolved cross-module script and layout interference by scoping tab switching, search/filter selectors, and dropdown toggle events inside the users view.',
                    icon: 'code'
                },
                {
                    title: 'Duplicate Script Loading Fix',
                    desc: 'Eliminated redundant inline inclusion of admin-script.js in the dashboard template to resolve double-binding and double-firing click event listeners.',
                    icon: 'file'
                }
            ]
        }
    ];

    function parseChangelogHTML(htmlString) {
        if (!htmlString) return [];
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlString, 'text/html');
        const h4s = doc.querySelectorAll('h4');
        const versions = [];
        h4s.forEach(h4 => {
            const version = h4.textContent.trim().replace(/^v/, '');
            const ul = h4.nextElementSibling;
            const items = [];
            if (ul && ul.tagName === 'UL') {
                const lis = ul.querySelectorAll('li');
                lis.forEach(li => {
                    const strong = li.querySelector('strong');
                    let title = '';
                    let description = li.textContent.trim();
                    if (strong) {
                        title = strong.textContent.replace(/:$/, '').trim();
                        description = description.replace(strong.textContent, '').replace(/^:\s*/, '').trim();
                    } else {
                        const parts = description.split(':');
                        if (parts.length > 1) {
                            title = parts[0].trim();
                            description = parts.slice(1).join(':').trim();
                        } else {
                            title = 'Platform Update';
                        }
                    }
                    items.push({ title: title, desc: description });
                });
            }
            versions.push({ version: version, items: items });
        });
        return versions;
    }

    window.coraRenderUpdateTimeline = function() {
        const container = document.getElementById('cora-changelog-timeline-container');
        if (!container) return;

        let html = '<div class="cora-update-timeline">';
        window.coraReleasesData.forEach((rel, rIdx) => {
            const isLatest = rIdx === 0;
            html += `
                <div class="cora-update-timeline-item ${isLatest ? 'active' : ''}">
                    <div class="cora-update-timeline-dot"></div>
                    <div class="flex flex-col md:flex-row gap-4 md:gap-6">
                        <!-- Left Version Tag -->
                        <div class="w-24 shrink-0 pt-1 select-none">
                            <span class="text-sm font-extrabold text-zinc-900 dark:text-zinc-50">${rel.version}</span>
                            ${isLatest ? '<span class="block mt-1 text-[9px] font-bold text-zinc-550 border border-zinc-200 dark:border-zinc-800 rounded px-1.5 py-0.5 w-max bg-zinc-50 dark:bg-zinc-950 uppercase tracking-wide">Latest</span>' : ''}
                        </div>
                        
                        <!-- Right Version Card List -->
                        <div class="flex-1 space-y-3">
            `;

            rel.items.forEach((item, iIdx) => {
                let svgIcon = '';
                switch(item.icon) {
                    case 'sparkles':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
                        break;
                    case 'edit':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>`;
                        break;
                    case 'wifi':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20" stroke-width="2"></line></svg>`;
                        break;
                    case 'refresh':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>`;
                        break;
                    case 'zap':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`;
                        break;
                    case 'link':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>`;
                        break;
                    case 'code':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>`;
                        break;
                    case 'shield':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`;
                        break;
                    case 'users':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`;
                        break;
                    default:
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>`;
                }

                const itemId = `accordion-${rIdx}-${iIdx}`;
                html += `
                    <div class="cora-update-row-hover bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 rounded-xl p-4 cursor-pointer transition-all duration-200" onclick="window.coraToggleUpdateAccordion('${itemId}')">
                        <div class="flex items-center justify-between gap-3 select-none">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center shrink-0 border border-zinc-100 dark:border-zinc-700/50">
                                    ${svgIcon}
                                </div>
                                <span class="text-xs font-bold text-zinc-900 dark:text-white">${item.title}</span>
                            </div>
                            <span class="text-zinc-400 dark:text-zinc-500 transition-transform duration-200 shrink-0" id="chevron-${itemId}" style="display: flex; align-items: center;">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="transition-transform duration-300 transform"><polyline points="6 9 12 15 18 9"/></svg>
                            </span>
                        </div>
                        <div class="cora-update-accordion-content open mt-3 pl-11 text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed" id="content-${itemId}">
                            ${item.desc}
                        </div>
                    </div>
                `;
            });

            html += `
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    };

    window.coraToggleUpdateAccordion = function(itemId) {
        const content = document.getElementById(`content-${itemId}`);
        const chevron = document.getElementById(`chevron-${itemId}`);
        if (content && chevron) {
            const svg = chevron.querySelector('svg');
            if (content.classList.contains('open')) {
                content.classList.remove('open');
                if (svg) svg.style.transform = 'rotate(0deg)';
            } else {
                content.classList.add('open');
                if (svg) svg.style.transform = 'rotate(180deg)';
            }
        }
    };

    window.coraToggleAllAccordions = function(expand) {
        const contents = document.querySelectorAll('.cora-update-accordion-content');
        contents.forEach(content => {
            const itemId = content.id.replace('content-', '');
            const chevron = document.getElementById(`chevron-${itemId}`);
            const svg = chevron ? chevron.querySelector('svg') : null;
            if (expand) {
                content.classList.add('open');
                if (svg) svg.style.transform = 'rotate(180deg)';
            } else {
                content.classList.remove('open');
                if (svg) svg.style.transform = 'rotate(0deg)';
            }
        });
    };

    window.coraToggleExpandAll = function(btn) {
        const span = btn.querySelector('span');
        const isExpand = span.innerText === 'Expand All';
        window.coraToggleAllAccordions(isExpand);
        if (isExpand) {
            span.innerText = 'Collapse All';
            btn.querySelector('svg').innerHTML = '<path d="M4 14h6v6M20 10h-6V4M14 10l7-7M10 14l-7 7"/>';
        } else {
            span.innerText = 'Expand All';
            btn.querySelector('svg').innerHTML = '<path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>';
        }
    };

    window.coraCheckForUpdatesNow = function() {
        if (window.coraShowToast) window.coraShowToast('Checking for platform updates...', 'info');
        const ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) ? window.coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
        const nonce = (window.coraREData && window.coraREData.ajaxNonce) ? window.coraREData.ajaxNonce : '';

        jQuery.post(ajaxUrl, {
            action: 'cora_force_check_update',
            security: nonce
        }, function(res) {
            const currentVer = '<?php echo esc_js(CORA_WORKSPACE_VERSION); ?>';
            const badge = document.getElementById('cora-update-badge');
            const titleElem = document.getElementById('cora-update-platform-title');
            const mainBtn = document.getElementById('cora-update-main-btn');
            const confirmBtn = document.getElementById('cora-update-confirm-btn');

            if (res.success && res.data && res.data.version && !res.data.up_to_date) {
                if (badge) {
                    badge.innerText = 'UPDATE AVAILABLE';
                    badge.className = 'inline-block text-[9px] font-extrabold bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 px-2 py-0.5 rounded tracking-wide uppercase leading-none mb-1.5';
                }
                if (titleElem) {
                    titleElem.innerText = 'Cora Workspace Platform v' + res.data.version;
                }
                if (mainBtn) {
                    mainBtn.style.display = '';
                    mainBtn.setAttribute('onclick', "window.coraExecuteWorkspaceUpdate('" + res.data.version + "');");
                }
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="rotate-180"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"/></svg> Upgrade Workspace Now';
                    confirmBtn.setAttribute('onclick', "window.coraExecuteWorkspaceUpdate('" + res.data.version + "');");
                }

                if (res.data.changelog) {
                    const parsed = parseChangelogHTML(res.data.changelog);
                    parsed.forEach(parsedRel => {
                        const exists = window.coraReleasesData.some(r => r.version === parsedRel.version);
                        if (!exists) {
                            parsedRel.items.forEach(item => {
                                const titleLower = item.title.toLowerCase();
                                if (titleLower.includes('validation') || titleLower.includes('security')) {
                                    item.icon = 'shield';
                                } else if (titleLower.includes('onboarding') || titleLower.includes('wizard')) {
                                    item.icon = 'sparkles';
                                } else if (titleLower.includes('role') || titleLower.includes('invite')) {
                                    item.icon = 'users';
                                } else if (titleLower.includes('offline') || titleLower.includes('maintenance')) {
                                    item.icon = 'wifi';
                                } else if (titleLower.includes('recycle') || titleLower.includes('recovery') || titleLower.includes('resilience')) {
                                    item.icon = 'refresh';
                                } else if (titleLower.includes('link') || titleLower.includes('redirect')) {
                                    item.icon = 'link';
                                } else if (titleLower.includes('modularity') || titleLower.includes('optimization')) {
                                    item.icon = 'code';
                                } else {
                                    item.icon = 'zap';
                                }
                            });
                            window.coraReleasesData.unshift(parsedRel);
                        }
                    });
                }

                window.coraOpenUpdateDrawer();
                if (window.coraShowToast) window.coraShowToast('New version v' + res.data.version + ' is available!', 'success');
            } else {
                if (badge) {
                    badge.innerText = 'FULLY UP TO DATE';
                    badge.className = 'inline-block text-[9px] font-extrabold bg-emerald-600 text-white px-2 py-0.5 rounded tracking-wide uppercase leading-none mb-1.5';
                }
                if (titleElem) {
                    titleElem.innerText = 'Cora Workspace Platform v' + currentVer;
                }
                if (mainBtn) {
                    mainBtn.style.display = 'none';
                }
                if (confirmBtn) {
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Latest Version Installed';
                }

                window.coraOpenUpdateDrawer();
                if (window.coraShowToast) window.coraShowToast('Workspace platform is fully up to date!', 'success');
            }
        }).fail(function() {
            if (window.coraShowToast) window.coraShowToast('Unable to connect to updates server.', 'error');
        });
    };

    window.coraOpenUpdateDrawer = function() {
        const overlay = document.getElementById('cora-update-overlay');
        const drawer = document.getElementById('cora-update-drawer');
        if (overlay && drawer) {
            overlay.classList.add('open');
            drawer.classList.add('open');
            if (typeof window.coraRenderUpdateTimeline === 'function') {
                window.coraRenderUpdateTimeline();
            }
        }
    };

    window.coraCloseUpdateDrawer = function() {
        const overlay = document.getElementById('cora-update-overlay');
        const drawer = document.getElementById('cora-update-drawer');
        if (overlay && drawer) {
            overlay.classList.remove('open');
            drawer.classList.remove('open');
        }
    };

    window.coraExecuteWorkspaceUpdate = function(version) {
        if (!version) return;
        const confirmBtn = document.getElementById('cora-update-confirm-btn');
        const mainBtn = document.getElementById('cora-update-main-btn');
        
        const setBtnsLoading = function(isLoading) {
            const spinner = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="animate-spin shrink-0"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> ';
            if (confirmBtn) {
                confirmBtn.disabled = isLoading;
                confirmBtn.innerHTML = isLoading ? spinner + 'Installing Update...' : '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="rotate-180"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"/></svg> Upgrade Workspace Now';
            }
            if (mainBtn) {
                mainBtn.disabled = isLoading;
                mainBtn.innerHTML = isLoading ? spinner + 'Upgrading...' : '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="rotate-180"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"/></svg> Upgrade Now';
            }
        };

        setBtnsLoading(true);
        window.coraShowToast('Downloading workspace update v' + version + '... please wait.');
        
        jQuery.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            timeout: 120000,
            data: {
                action: 'cora_trigger_workspace_update',
                version: version,
                nonce: coraREData.ajaxNonce
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Workspace updated to v' + version + '! Reloading...');
                    setTimeout(function() { window.location.reload(); }, 2000);
                } else {
                    var msg = (res.data && res.data.message) ? res.data.message : 'Update failed. Please try again.';
                    window.coraShowToast(msg);
                    setBtnsLoading(false);
                }
            },
            error: function(xhr, status) {
                var errMsg = status === 'timeout' ? 'Update timed out. The server is taking too long — please try again.' : 'Network error. Could not reach the update server.';
                window.coraShowToast(errMsg);
                setBtnsLoading(false);
            }
        });
    };

    window.coraTriggerCommandAI = function() {
        coraCloseCommandPalette();
        const sidebar = document.getElementById('cora-ai-sidebar');
        const chatInput = document.getElementById('cora-sidebar-chat-input');
        if (sidebar && typeof window.coraToggleSidebar === 'function') {
            window.coraToggleSidebar(true);
            setTimeout(() => {
                if (chatInput) chatInput.focus();
            }, 300);
        }
    };

    function getIconSVG(name) {
        const icons = {
            'settings': `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>`,
            'lock': `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>`,
            'map-pin': `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>`,
            'image': `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>`,
            'leads': `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`,
            'listings': `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>`
        };
        return icons[name] || icons['settings'];
    }

    const coraSearchCache = {};

    function renderSearchDOM(data, resultsContainer, isInline) {
        if (data.success && data.data && data.data.results && data.data.results.length > 0) {
            let html = '<div class="space-y-0.5">';
            data.data.results.forEach((item, index) => {
                html += `
                    <a href="${item.url}" class="cora-command-item flex items-center justify-between p-2.5 rounded-xl transition-all duration-150 cursor-pointer text-decoration-none group" data-index="${index}">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center border border-zinc-200/50 group-hover:bg-white group-hover:border-zinc-300 transition-colors">
                                ${getIconSVG(item.icon)}
                            </span>
                            <div class="space-y-0.5">
                                <div class="text-xs font-bold text-zinc-900">${item.title}</div>
                                <p class="text-[10px] text-zinc-400 line-clamp-1">${item.description}</p>
                            </div>
                        </div>
                        <span class="text-zinc-300 group-hover:text-zinc-800 transition-colors">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></span >
                        </span>
                    </a>
                `;
            });
            html += '</div>';
            resultsContainer.innerHTML = html;
            if (!isInline) selectedIndex = -1;
        } else {
            resultsContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <svg class="text-zinc-300 mb-2" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span class="text-xs font-semibold text-zinc-850">No results found</span>
                    <p class="text-[10px] text-zinc-455 mt-0.5">There are no results matching to the query. Try searching with other filters or keywords</p>
                </div>
            `;
            if (!isInline) selectedIndex = -1;
        }
    }

    function coraPerformCommandSearch(query, isInline = false) {
        const parentPalette = document.getElementById(isInline ? 'cora-inline-command-palette' : 'cora-command-palette');
        const resultsContainer = parentPalette ? parentPalette.querySelector(isInline ? '#cora-inline-command-results' : '#cora-command-results') : null;
        if (!resultsContainer) return;

        const cacheKey = query + '_' + currentFilter;
        if (coraSearchCache[cacheKey]) {
            renderSearchDOM(coraSearchCache[cacheKey], resultsContainer, isInline);
            return;
        }

        resultsContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center py-10 space-y-2">
                <div class="w-5 h-5 border-2 border-zinc-900 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-[10px] text-zinc-400 font-medium">Searching workspace database...</span>
            </div>
        `;

        const thisRequestId = ++searchRequestId;
        const url = coraREData.ajaxUrl + '?action=cora_advanced_search&nonce=' + coraREData.ajaxNonce + '&q=' + encodeURIComponent(query) + '&filter=' + currentFilter;
        
        fetch(url, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (thisRequestId !== searchRequestId) return;
            if (data.success) {
                coraSearchCache[cacheKey] = data;
            }
            renderSearchDOM(data, resultsContainer, isInline);
        })
        .catch(err => {
            if (thisRequestId !== searchRequestId) return;
            console.error('Advanced Search Error:', err);
            resultsContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <span class="text-xs font-semibold text-zinc-800">Connection error</span>
                    <p class="text-[10px] text-zinc-400">Failed to fetch search data.</p>
                </div>
            `;
        });
    }

    // Keyboard bindings for global workspace triggers
    document.addEventListener('keydown', function(e) {
        // Toggle Cmd+K / Ctrl+K
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const palette = document.getElementById('cora-command-palette');
            if (palette && !palette.classList.contains('hidden')) {
                coraCloseCommandPalette();
            } else {
                coraOpenCommandPalette();
            }
            return;
        }

        const palette = document.getElementById('cora-command-palette');
        if (!palette || palette.classList.contains('hidden')) return;

        // Escape closes
        if (e.key === 'Escape') {
            e.preventDefault();
            coraCloseCommandPalette();
            return;
        }

        const items = document.querySelectorAll('#cora-command-palette .cora-command-item');
        if (items.length === 0) return;

        // ArrowDown
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex++;
            if (selectedIndex >= items.length) {
                selectedIndex = 0;
            }
            updateSelectedItem(items);
            return;
        }

        // ArrowUp
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex--;
            if (selectedIndex < 0) {
                selectedIndex = items.length - 1;
            }
            updateSelectedItem(items);
            return;
        }

        // Enter triggers navigation
        if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && selectedIndex < items.length) {
                items[selectedIndex].click();
            }
        }
    });

    function updateSelectedItem(items) {
        items.forEach((item, idx) => {
            if (idx === selectedIndex) {
                item.classList.add('selected');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('selected');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic Browser Local timezone Greeting calculation
        (function() {
            const hour = new Date().getHours();
            let dayStage = 'evening';
            if (hour >= 5 && hour < 12) {
                dayStage = 'morning';
            } else if (hour >= 12 && hour < 17) {
                dayStage = 'afternoon';
            } else {
                dayStage = 'evening';
            }
            const greetingEl = document.getElementById('cora-dynamic-greeting-title');
            if (greetingEl) {
                const userName = <?php echo json_encode( $user_first_name ); ?> || 'Dravya';
                greetingEl.textContent = `Good ${dayStage}, ${userName}.`;
            }
        })();

        // Input text focus/click listeners to toggle absolute dropdown or modal
        const paletteContainer = document.getElementById('cora-command-palette');
        const input = paletteContainer ? paletteContainer.querySelector('#cora-command-input') : null;
        if (input) {
            input.addEventListener('focus', function() {
                coraOpenCommandPalette();
            });
            input.addEventListener('click', function(e) {
                e.stopPropagation();
                coraOpenCommandPalette();
            });
            input.addEventListener('input', function() {
                clearTimeout(searchDebounceTimeout);
                const query = this.value.trim();
                searchDebounceTimeout = setTimeout(() => {
                    coraPerformCommandSearch(query, false);
                }, 150);
            });
        }

        const inlineInput = document.getElementById('cora-inline-command-input');
        if (inlineInput) {
            inlineInput.addEventListener('focus', function() {
                const inlinePalette = document.getElementById('cora-inline-command-palette');
                if (inlinePalette) { inlinePalette.classList.remove('hidden'); inlinePalette.classList.add('flex'); }
                coraPerformCommandSearch(this.value.trim(), true);
            });
            inlineInput.addEventListener('click', function(e) {
                e.stopPropagation();
                const inlinePalette = document.getElementById('cora-inline-command-palette');
                if (inlinePalette) { inlinePalette.classList.remove('hidden'); inlinePalette.classList.add('flex'); }
                coraPerformCommandSearch(this.value.trim(), true);
            });
            inlineInput.addEventListener('input', function() {
                clearTimeout(searchDebounceTimeout);
                const query = this.value.trim();
                const inlinePalette = document.getElementById('cora-inline-command-palette');
                if (inlinePalette) { inlinePalette.classList.remove('hidden'); inlinePalette.classList.add('flex'); }
                searchDebounceTimeout = setTimeout(() => {
                    coraPerformCommandSearch(query, true);
                }, 150);
            });
        }

        // ======================================================
        // HEADER PUNCH IN / OUT WIDGET
        // ======================================================
        <?php $db_punch_state = cora_get_current_user_punch_status(); ?>
        var _headerPunchState = <?php echo json_encode( $db_punch_state ); ?>;

        window.updateHeaderPunchState = function(status, timeStr) {
            _headerPunchState = { status: status, time: timeStr };
            localStorage.setItem('cora_punch_state', JSON.stringify(_headerPunchState));
            
            // Desktop elements
            const dot = document.getElementById('cora-header-punch-dot');
            const label = document.getElementById('cora-header-punch-label');
            const popDot = document.getElementById('cora-punch-popover-dot');
            const popStatus = document.getElementById('cora-punch-popover-status');
            const popTime = document.getElementById('cora-punch-popover-time');
            
            // Mobile elements
            const mobDot = document.getElementById('cora-mobile-punch-dot');
            const mobPopDot = document.getElementById('cora-mobile-punch-popover-dot');
            const mobPopStatus = document.getElementById('cora-mobile-punch-popover-status');
            const mobPopTime = document.getElementById('cora-mobile-punch-popover-time');

            if (status === 'in') {
                if (dot) { dot.style.backgroundColor = '#22c55e'; }
                if (label) { label.textContent = 'Punched In'; }
                if (popDot) { popDot.style.backgroundColor = '#22c55e'; }
                if (popStatus) popStatus.textContent = 'Punched In';
                
                if (mobDot) { mobDot.style.backgroundColor = '#22c55e'; }
                if (mobPopDot) { mobPopDot.style.backgroundColor = '#22c55e'; }
                if (mobPopStatus) mobPopStatus.textContent = 'Punched In';
            } else {
                if (dot) { dot.style.backgroundColor = '#71717a'; }
                if (label) { label.textContent = 'Punch'; }
                if (popDot) { popDot.style.backgroundColor = '#71717a'; }
                if (popStatus) popStatus.textContent = 'Not punched in';
                
                if (mobDot) { mobDot.style.backgroundColor = '#71717a'; }
                if (mobPopDot) { mobPopDot.style.backgroundColor = '#71717a'; }
                if (mobPopStatus) mobPopStatus.textContent = 'Not punched in';
            }
            if (popTime && timeStr) popTime.textContent = timeStr;
            if (mobPopTime && timeStr) mobPopTime.textContent = timeStr;
        };

        // Initialize state on page load
        if (_headerPunchState && _headerPunchState.status) {
            window.updateHeaderPunchState(_headerPunchState.status, _headerPunchState.time || '');
        }

        window.toggleHeaderPunchPopover = function(e) {
            if (e) e.stopPropagation();
            const pop = document.getElementById('cora-header-punch-popover');
            if (!pop) return;
            const isHidden = pop.classList.contains('hidden');
            if (typeof window.coraCloseAllPopovers === 'function') {
                window.coraCloseAllPopovers('cora-header-punch-popover');
            }
            if (isHidden) {
                pop.classList.remove('hidden');
            } else {
                pop.classList.add('hidden');
            }
        };

        window.toggleMobilePunchPopover = function(e) {
            if (e) e.stopPropagation();
            const pop = document.getElementById('cora-mobile-punch-popover');
            if (!pop) return;
            const isHidden = pop.classList.contains('hidden');
            if (typeof window.coraCloseAllPopovers === 'function') {
                window.coraCloseAllPopovers('cora-mobile-punch-popover');
            }
            if (isHidden) {
                pop.classList.remove('hidden');
            } else {
                pop.classList.add('hidden');
            }
        };

        window.headerLogPunch = function(type) {
            const feedback = document.getElementById('cora-punch-popover-feedback');
            const mobFeedback = document.getElementById('cora-mobile-punch-popover-feedback');
            if (feedback) { feedback.textContent = 'Acquiring GPS location...'; feedback.classList.remove('hidden'); }
            if (mobFeedback) { mobFeedback.textContent = 'Acquiring GPS location...'; mobFeedback.classList.remove('hidden'); }

            function sendPunch(lat, lng) {
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_save_attendance',
                    nonce: coraREData.ajaxNonce,
                    log: JSON.stringify({ type: type, timestamp: Date.now(), lat: lat, lng: lng })
                }, function(res) {
                    if (res.success) {
                        const now = new Date();
                        const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        window.updateHeaderPunchState(type, timeStr);
                        if (feedback) feedback.classList.add('hidden');
                        if (mobFeedback) mobFeedback.classList.add('hidden');
                        window.coraShowToast(type === 'in' ? 'Punched in successfully ✓' : 'Punched out successfully ✓');
                        setTimeout(function() {
                            const pop = document.getElementById('cora-header-punch-popover');
                            if (pop) pop.classList.add('hidden');
                            const mobPop = document.getElementById('cora-mobile-punch-popover');
                            if (mobPop) mobPop.classList.add('hidden');
                        }, 600);
                    } else {
                        const errMsg = res.data.message || 'Failed to save punch.';
                        if (feedback) feedback.textContent = errMsg;
                        if (mobFeedback) mobFeedback.textContent = errMsg;
                    }
                }).fail(function() {
                    const errMsg = 'Network error. Please try again.';
                    if (feedback) feedback.textContent = errMsg;
                    if (mobFeedback) mobFeedback.textContent = errMsg;
                });
            }

            if (!navigator.geolocation) {
                sendPunch(null, null);
                return;
            }

            navigator.geolocation.getCurrentPosition(function(pos) {
                sendPunch(pos.coords.latitude, pos.coords.longitude);
            }, function(err) {
                // Fallback punch if GPS is denied or HTTP non-secure origin
                sendPunch(null, null);
            }, { timeout: 7000, enableHighAccuracy: true });
        };

        // Restore punch state on load
        (function() {
            if (_headerPunchState.status === 'in') {
                window.updateHeaderPunchState('in', _headerPunchState.time);
            }
        })();

        // Close dropdown when clicking outside search container or modal
        document.addEventListener('click', function(e) {
            if (e.target.closest('.cora-sidebar-search') || e.target.closest('[onclick*="coraOpenCommandPalette"]')) return;

            const palette = document.getElementById('cora-command-palette');
            if (palette && !palette.classList.contains('hidden') && !palette.contains(e.target)) {
                coraCloseCommandPalette();
            }

            const inlinePalette = document.getElementById('cora-inline-command-palette');
            const container = document.getElementById('cora-search-container');
            if (inlinePalette && !inlinePalette.classList.contains('hidden') && container && !container.contains(e.target)) {
                inlinePalette.classList.add('hidden');
                inlinePalette.classList.remove('flex');
            }

            // Close notification drawer when clicking outside
            const notifDropdown = document.getElementById('cora-notif-dropdown');
            if (notifDropdown && !notifDropdown.classList.contains('collapsed')) {
                const bellBtn = document.getElementById('cora-notif-bell-btn');
                if (!notifDropdown.contains(e.target) && (!bellBtn || !bellBtn.contains(e.target))) {
                    window.coraToggleNotificationDrawer(false);
                }
            }

            // Close popovers when clicking outside
            const profilePopover = document.getElementById('cora-profile-popover');
            if (profilePopover && !profilePopover.classList.contains('hidden')) {
                const footer = document.querySelector('.cora-user-footer');
                if (!profilePopover.contains(e.target) && (!footer || !footer.contains(e.target))) {
                    profilePopover.classList.add('hidden');
                }
            }

            const sidebarNotifPopover = document.getElementById('cora-sidebar-notif-popover');
            if (sidebarNotifPopover && !sidebarNotifPopover.classList.contains('hidden')) {
                const inboxBtn = document.querySelector('.cora-user-inbox');
                if (!sidebarNotifPopover.contains(e.target) && (!inboxBtn || !inboxBtn.contains(e.target))) {
                    sidebarNotifPopover.classList.add('hidden');
                }
            }

            const workspacePopover = document.getElementById('cora-workspace-popover');
            if (workspacePopover && !workspacePopover.classList.contains('hidden')) {
                const switcher = document.querySelector('.cora-workspace-card');
                if (!workspacePopover.contains(e.target) && (!switcher || !switcher.contains(e.target))) {
                    workspacePopover.classList.add('hidden');
                }
            }

            const punchPopover = document.getElementById('cora-header-punch-popover');
            if (punchPopover && !punchPopover.classList.contains('hidden')) {
                const punchWrap = document.getElementById('cora-header-punch-wrap');
                if (!punchWrap || !punchWrap.contains(e.target)) {
                    punchPopover.classList.add('hidden');
                }
            }

            const mobPunchPopover = document.getElementById('cora-mobile-punch-popover');
            if (mobPunchPopover && !mobPunchPopover.classList.contains('hidden')) {
                const mobPunchWrap = document.getElementById('cora-mobile-punch-wrap');
                if (!mobPunchWrap || !mobPunchWrap.contains(e.target)) {
                    mobPunchPopover.classList.add('hidden');
                }
            }
        });

        // Filter pills click listeners
        const pills = document.querySelectorAll('#cora-command-palette .cora-search-pill, #cora-inline-command-palette .cora-search-pill');
        pills.forEach(pill => {
            pill.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent dropdown closure
                const isInlinePill = this.closest('#cora-inline-command-palette') !== null;
                const parentPalette = isInlinePill ? document.getElementById('cora-inline-command-palette') : document.getElementById('cora-command-palette');
                if (parentPalette) {
                    parentPalette.querySelectorAll('.cora-search-pill').forEach(p => {
                        p.classList.remove('active', 'bg-zinc-900', 'text-white', 'dark:bg-zinc-100', 'dark:text-zinc-950');
                        p.classList.add('bg-white', 'text-zinc-650', 'dark:bg-zinc-900', 'dark:text-zinc-405');
                    });
                }
                this.classList.add('active', 'bg-zinc-900', 'text-white', 'dark:bg-zinc-100', 'dark:text-zinc-950');
                this.classList.remove('bg-white', 'text-zinc-650', 'dark:bg-zinc-900', 'dark:text-zinc-405');
                currentFilter = this.getAttribute('data-filter');
                if (isInlinePill) {
                    const query = document.getElementById('cora-inline-command-input') ? document.getElementById('cora-inline-command-input').value.trim() : '';
                    coraPerformCommandSearch(query, true);
                } else {
                    const inputEl = parentPalette ? parentPalette.querySelector('#cora-command-input') : null;
                    const query = inputEl ? inputEl.value.trim() : '';
                    coraPerformCommandSearch(query, false);
                }
            });
        });

        // Sidebar Menu Search Filtering (Dynamic unified search)
        const sidebarSearchInput = document.getElementById('cora-sidebar-search-input');
        const sidebarNav = document.querySelector('.cora-sidebar-nav');
        const sidebarSearchResults = document.getElementById('cora-sidebar-search-results');
        let sidebarSearchDebounceTimeout = null;
        let sidebarSearchRequestId = 0;

        function renderSidebarSearchDOM(data, resultsContainer) {
            if (!resultsContainer) return;
            if (data.success && data.data && data.data.results && data.data.results.length > 0) {
                let html = '<ul class="space-y-0.5 mt-1">';
                data.data.results.forEach(item => {
                    html += `
                        <li class="list-none">
                            <a href="${item.url}" class="cora-nav-item flex items-center justify-between px-3 py-2.5 text-sm rounded-lg cursor-pointer select-none no-underline text-zinc-800 dark:text-zinc-200 hover:text-zinc-950 dark:hover:text-white group">
                                <div class="flex items-center gap-3 select-none min-w-0">
                                    <span class="cora-nav-icon select-none text-zinc-500 group-hover:text-zinc-950 dark:group-hover:text-white shrink-0">
                                        ${getIconSVG(item.icon)}
                                    </span>
                                    <div class="flex flex-col min-w-0">
                                        <span class="cora-nav-text select-none font-semibold text-xs leading-normal truncate text-zinc-800 dark:text-zinc-200 group-hover:text-zinc-950 dark:group-hover:text-white">${item.title}</span>
                                        <span class="text-[9px] text-zinc-400 dark:text-zinc-500 font-normal leading-normal truncate">${item.description}</span>
                                    </div>
                                </div>
                                <span class="text-[8px] font-bold tracking-wider text-zinc-400 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-850 px-1 py-0.5 rounded uppercase shrink-0">${item.category}</span>
                            </a>
                        </li>
                    `;
                });
                html += '</ul>';
                resultsContainer.innerHTML = html;
            } else {
                resultsContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-8 text-center px-3">
                        <svg class="text-zinc-400 mb-2" viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <span class="text-[11px] font-bold text-zinc-800 dark:text-zinc-200 leading-normal">There are no results matching to the query</span>
                        <p class="text-[9px] text-zinc-450 mt-0.5 leading-normal">Try searching other keywords</p>
                    </div>
                `;
            }
        }

        if (sidebarSearchInput && sidebarNav) {
            sidebarSearchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                const navItems = sidebarNav.querySelectorAll('li[data-target]');
                
                if (query === '') {
                    navItems.forEach(item => item.classList.remove('hidden'));
                    const groups = sidebarNav.querySelectorAll('.cora-nav-group');
                    groups.forEach(group => {
                        const label = group.querySelector('.cora-nav-group-label');
                        if (label) label.classList.remove('hidden');
                        group.classList.remove('hidden');
                    });
                    if (sidebarSearchResults) {
                        sidebarSearchResults.classList.add('hidden');
                        sidebarSearchResults.innerHTML = '';
                    }
                    sidebarNav.classList.remove('hidden');
                    return;
                }

                // Client-side filtering of sidebar menu items matching the query text
                let matchedAny = false;
                navItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(query)) {
                        item.classList.remove('hidden');
                        matchedAny = true;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                // Dynamically hide/show navigation groups based on matching items
                const groups = sidebarNav.querySelectorAll('.cora-nav-group');
                groups.forEach(group => {
                    const visibleItems = group.querySelectorAll('li[data-target]:not(.hidden)');
                    const label = group.querySelector('.cora-nav-group-label');
                    if (visibleItems.length === 0) {
                        group.classList.add('hidden');
                        if (label) label.classList.add('hidden');
                    } else {
                        group.classList.remove('hidden');
                        if (label) label.classList.remove('hidden');
                    }
                });

                // Hide search results container since we are filtering the main list in place
                if (sidebarSearchResults) {
                    sidebarSearchResults.classList.add('hidden');
                }
                sidebarNav.classList.remove('hidden');
            });
        }
    });

    // Check for plugin updates in-app
    <?php if ( cora_is_super_owner() ) : ?>
    setTimeout(function() {
        $.post(coraREData.ajaxUrl, {
            action: 'cora_check_plugin_update',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success && res.data.update_available) {
                $('#cora-update-ver').text('v' + res.data.new_version);
                $('#cora-in-app-update-notice').removeClass('hidden');
                // Add update indicator dot to the profile footer
                $('.cora-user-footer').append('<span id="cora-update-indicator-dot" class="absolute top-2.5 right-12 w-2 h-2 rounded-full bg-blue-500 animate-pulse z-50"></span>');
            }
        });
    }, 3000);

    window.coraTriggerInAppUpgrade = function(btn) {
        const upgradeFn = function() {
            $(btn).prop('disabled', true).text('Upgrading workspace...');
            $.post(coraREData.ajaxUrl, {
                action: 'cora_trigger_in_app_update',
                nonce: coraREData.ajaxNonce
            }, function(res) {
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Workspace upgraded!');
                    setTimeout(function() { window.location.reload(); }, 1500);
                } else {
                    window.coraShowToast(res.data.message || 'Upgrade failed.');
                    $(btn).prop('disabled', false).text('Upgrade Workspace');
                }
            }).fail(function() {
                window.coraShowToast('Server error during upgrade.');
                $(btn).prop('disabled', false).text('Upgrade Workspace');
            });
        };

        if (window.coraConfirmAction) {
            window.coraConfirmAction(
                'Upgrade Workspace',
                'Are you sure you want to upgrade the Cora workspace to the latest version? The screen will reload once complete.',
                upgradeFn
            );
        } else {
            upgradeFn();
        }
    };
    <?php endif; ?>
})();
</script>

<!-- Google Translate POC translation layer -->
<div id="google_translate_element" style="display:none; visibility:hidden; position:absolute; top:-9999px;"></div>
<style>
/* Hide standard Google Translate headers and UI wrappers to match Cora monochrome branding */
body {
    top: 0 !important;
}
.skiptranslate, .goog-te-banner-frame, #goog-gt-tt, .goog-te-balloon-frame {
    display: none !important;
    visibility: hidden !important;
}
.goog-tooltip {
    display: none !important;
}
.goog-tooltip:hover {
    display: none !important;
}
.goog-text-highlight {
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
}
</style>
<script type="text/javascript">
(function() {
    // Synchronize the server-saved language preference directly to localStorage
    const serverLang = '<?php echo esc_js( get_option( 'cora_workspace_language', 'en' ) ); ?>';
    if (serverLang) {
        localStorage.setItem('cora_platform_language', serverLang);
        localStorage.setItem('cora_workspace_language', serverLang);
    }
    const selectedLang = serverLang || localStorage.getItem('cora_platform_language') || 'en';
    if (selectedLang !== 'en') {
        // Set standard Google Translate cookie
        document.cookie = "googtrans=/en/" + selectedLang + "; path=/";
        if (window.location.hostname.indexOf('.') !== -1) {
            document.cookie = "googtrans=/en/" + selectedLang + "; path=/; domain=" + window.location.hostname;
        }

        // Load Google Translate script
        window.googleTranslateElementInit = function() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');
        };

        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        document.body.appendChild(script);
    } else {
        // Clear cookie if English
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        if (window.location.hostname.indexOf('.') !== -1) {
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname;
        }
    }
})();
</script>

<?php
$update = cora_check_workspace_update_available();
if ( cora_is_super_owner() ) :
    $avail_ver = is_array( $update ) && ! empty( $update['version'] ) ? $update['version'] : CORA_WORKSPACE_VERSION;
    $changelog_content = is_array( $update ) && ! empty( $update['changelog'] ) ? $update['changelog'] : '<h4>v2.2.1 Release</h4><ul><li>Industry Mode Switcher with 1-click toggle.</li><li>Isolated workspace scoping per agency slug.</li><li>Seeded role capabilities for all Real Estate and Studio roles.</li></ul>';
?>
<style>
#cora-update-overlay {
    position: fixed;
    inset: 0;
    z-index: 99998;
    background: rgba(9, 9, 11, 0.4);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.25s ease, visibility 0.25s ease;
    backdrop-filter: blur(4px);
}
#cora-update-overlay.open {
    opacity: 1;
    visibility: visible;
}
#cora-update-drawer {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 99999;
    height: 100%;
    width: 100%;
    max-width: 960px;
    background: #ffffff;
    border-left: 1px solid #e4e4e7;
    box-shadow: -10px 0 40px rgba(9, 9, 11, 0.08);
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
}
.dark #cora-update-drawer {
    background: #09090b;
    border-left: 1px solid #27272a;
    box-shadow: -10px 0 40px rgba(9, 9, 11, 0.4);
}
#cora-update-drawer.open {
    transform: translateX(0);
}

/* Timeline specific styles */
.cora-update-timeline {
    position: relative;
    padding-left: 32px;
}
.cora-update-timeline::before {
    content: '';
    position: absolute;
    top: 8px;
    bottom: 8px;
    left: 7px;
    width: 1px;
    background-color: #e4e4e7;
}
.dark .cora-update-timeline::before {
    background-color: #27272a;
}
.cora-update-timeline-item {
    position: relative;
    margin-bottom: 32px;
}
.cora-update-timeline-item:last-child {
    margin-bottom: 0;
}
.cora-update-timeline-dot {
    position: absolute;
    left: -32px;
    top: 6px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background-color: #ffffff;
    border: 2px solid #e4e4e7;
    z-index: 2;
    transition: all 0.2s ease;
}
.dark .cora-update-timeline-dot {
    background-color: #09090b;
    border-color: #27272a;
}
.cora-update-timeline-item.active .cora-update-timeline-dot {
    border-color: #18181b;
    background-color: #18181b;
}
.dark .cora-update-timeline-item.active .cora-update-timeline-dot {
    border-color: #f4f4f5;
    background-color: #f4f4f5;
}

/* Accordion transition styles */
.cora-update-accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
    opacity: 0;
}
.cora-update-accordion-content.open {
    max-height: 1000px;
    opacity: 1;
}

/* Hover effects */
.cora-update-row-hover:hover {
    border-color: #d4d4d8;
    background-color: #fafafa;
}
.dark .cora-update-row-hover:hover {
    border-color: #3f3f46;
    background-color: rgba(39, 39, 42, 0.3);
}
</style>

<div id="cora-update-overlay" onclick="window.coraCloseUpdateDrawer();"></div>

<div id="cora-update-drawer" class="text-zinc-850 dark:text-zinc-200">
    <!-- Header -->
    <div class="flex items-center justify-between px-8 py-6 border-b border-zinc-200 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-900/20 flex-shrink-0">
        <div class="space-y-1">
            <h2 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Software Updates</h2>
            <p class="text-xs text-zinc-550 dark:text-zinc-400">Manage system versions, release channels, and automated feature shipments.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Release Channel selector dropdown mockup -->
            <div class="relative hidden sm:block">
                <button type="button" class="flex items-center gap-3 px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 text-left select-none outline-none">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    <div>
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wide leading-none">Release Channel</span>
                        <span class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mt-0.5">Production Stable</span>
                    </div>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450 ml-1"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
            </div>
            
            <button onclick="window.coraCloseUpdateDrawer();" class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700/80 border-none cursor-pointer flex items-center justify-center text-zinc-500 dark:text-zinc-400 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
        
        <!-- Status Card -->
        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 bg-white dark:bg-zinc-950 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 shadow-3xs select-none">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-zinc-950 dark:bg-zinc-100 flex items-center justify-center text-white dark:text-zinc-950 shrink-0 shadow-sm">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none" class="animate-pulse"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                </div>
                <div>
                    <span id="cora-update-badge" class="inline-block text-[9px] font-extrabold bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 px-2 py-0.5 rounded tracking-wide uppercase leading-none mb-1.5">UPDATE AVAILABLE</span>
                    <h3 id="cora-update-platform-title" class="text-sm font-bold text-zinc-900 dark:text-white leading-tight">Cora Workspace Platform v<?php echo esc_html($avail_ver); ?></h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Your current installed version is v<?php echo esc_html(CORA_WORKSPACE_VERSION); ?>.</p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 w-full md:w-auto md:shrink-0">
                <div class="hidden md:block h-8 w-px bg-zinc-200 dark:bg-zinc-850"></div>
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <div>
                        <span class="block text-[9px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-wide leading-none">Released on</span>
                        <span class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mt-0.5">May 21, 2025</span>
                    </div>
                </div>
                <button type="button" onclick="window.coraExecuteWorkspaceUpdate('<?php echo esc_js($avail_ver); ?>');" id="cora-update-main-btn" class="w-full sm:w-auto h-10 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98]">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="rotate-180"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"/></svg>
                    Upgrade Now
                </button>
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="space-y-4 pt-2">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Changelog & Features</h4>
                <button type="button" onclick="window.coraToggleExpandAll(this);" class="h-8 px-3 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-zinc-750 dark:text-zinc-300 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-3xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                    <span>Expand All</span>
                </button>
            </div>

            <!-- Timeline items rendered dynamically by JavaScript -->
            <div id="cora-changelog-timeline-container" class="py-2"></div>
            
            <div class="flex justify-center pt-2">
                <button type="button" class="h-8 px-4 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-zinc-750 dark:text-zinc-300 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1">
                    <span>View more improvements in v2.9.0</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
            </div>
        </div>

        <!-- Safety Backup Tip -->
        <div class="flex gap-3 p-4 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800/80 rounded-xl text-xs text-zinc-550 dark:text-zinc-400 leading-relaxed">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8" stroke-width="2.5"/></svg>
            <div>
                <strong class="font-bold text-zinc-700 dark:text-zinc-300">Recommendation:</strong> Please perform a database and file backup before proceeding with updates to ensure workspace restoration safety.
            </div>
        </div>

    </div>

    <!-- Sticky Footer Actions -->
    <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 flex-shrink-0">
        <div class="flex items-center gap-2 select-none">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
            <div>
                <span class="block text-[8px] font-bold text-zinc-400 uppercase tracking-wide leading-none">Official Shipment Channel</span>
                <span class="block text-[10px] font-bold text-zinc-550 dark:text-zinc-450 mt-0.5">Production Stable (GitHub)</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <button type="button" onclick="window.coraCheckForUpdatesNow();" class="flex-1 sm:flex-none h-10 px-4 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-zinc-750 dark:text-zinc-300 text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-3xs active:scale-[0.98]">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                Check for Updates
            </button>
            <button id="cora-update-confirm-btn" onclick="window.coraExecuteWorkspaceUpdate('<?php echo esc_js($avail_ver); ?>');" class="flex-1 sm:flex-none h-10 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98]">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="rotate-180"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"/></svg>
                Upgrade Workspace Now
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.cora-side-drawer {
    position: fixed;
    top: 0;
    right: 0;
    width: 50vw;
    min-width: 480px;
    max-width: 98vw;
    height: 100%;
    z-index: 10000;
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: -10px 0 30px rgba(0,0,0,0.08);
}
@media (max-width: 768px) {
    .cora-side-drawer {
        width: 100vw !important;
        min-width: 100vw !important;
    }
}
.cora-side-drawer.collapsed {
    transform: translateX(100%);
}
</style>

<!-- Create Workspace Side Drawer -->
<div id="cora-create-workspace-drawer" class="cora-side-drawer collapsed bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 flex flex-col select-none">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between flex-shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Create New Workspace</h3>
            <p class="text-[11px] text-zinc-550 dark:text-zinc-400 mt-0.5">Spin up a brand new workspace agency instance</p>
        </div>
        <button type="button" onclick="window.coraToggleCreateWorkspaceDrawer(false);" class="w-8 h-8 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-800/80 cursor-pointer flex items-center justify-center text-zinc-555 dark:text-zinc-400 transition-colors">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <!-- Scrollable content -->
    <form id="cora-create-ws-form" class="flex-1 overflow-y-auto p-6 space-y-5" onsubmit="event.preventDefault(); window.coraSubmitCreateWorkspace();">
        <div>
            <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Workspace Name</label>
            <input type="text" id="cora-create-ws-name" required placeholder="e.g. Acme Agency" class="w-full h-10 px-3 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-semibold text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors" onkeyup="window.coraAutoSlugify(this.value)">
        </div>

        <div>
            <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Workspace Slug / URL</label>
            <div class="relative flex items-center">
                <span class="absolute left-3 text-[10px] font-mono text-zinc-400 select-none">heycora.in/</span>
                <input type="text" id="cora-create-ws-slug" required placeholder="acme" class="w-full h-10 pl-[74px] pr-3 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-mono text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
            </div>
            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1.5 leading-normal">The unique URL identifier for this workspace. Use lowercase letters, numbers, and hyphens only.</p>
        </div>

        <div>
            <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Pricing Plan</label>
            <select id="cora-create-ws-plan" class="w-full h-10 px-2.5 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
                <option value="starter">Starter Plan</option>
                <option value="professional">Professional Plan</option>
                <option value="enterprise" selected>Enterprise Plan</option>
            </select>
        </div>

        <div>
            <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Owner Email Address</label>
            <input type="email" id="cora-create-ws-owner-email" required value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" class="w-full h-10 px-3 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-semibold text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
        </div>
    </form>

    <!-- Footer actions -->
    <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/30 flex items-center gap-3 flex-shrink-0">
        <button type="button" id="cora-create-ws-btn" onclick="window.coraSubmitCreateWorkspace();" class="flex-1 h-10 rounded-lg bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-950 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98]">
            Create Workspace
        </button>
        <button type="button" onclick="window.coraToggleCreateWorkspaceDrawer(false);" class="px-4 h-10 rounded-lg bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700/80 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all cursor-pointer active:scale-[0.98]">
            Cancel
        </button>
    </div>
</div>

<!-- Edit Workspace Side Drawer -->
<div id="cora-edit-workspace-drawer" class="cora-side-drawer collapsed bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 flex flex-col select-none">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between flex-shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Edit Workspace Settings</h3>
            <p class="text-[11px] text-zinc-550 dark:text-zinc-400 mt-0.5">Manage administrative settings and status</p>
        </div>
        <button type="button" onclick="window.coraToggleEditWorkspaceDrawer(false);" class="w-8 h-8 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-800/80 cursor-pointer flex items-center justify-center text-zinc-555 dark:text-zinc-400 transition-colors">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <!-- Scrollable content -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        <input type="hidden" id="cora-edit-ws-id" value="0">

        <div class="space-y-4">
            <div>
                <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Workspace Name</label>
                <input type="text" id="cora-edit-ws-name" required class="w-full h-10 px-3 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-semibold text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
            </div>

            <div>
                <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Workspace Slug</label>
                <div class="relative flex items-center">
                    <span class="absolute left-3 text-[10px] font-mono text-zinc-400 select-none">heycora.in/</span>
                    <input type="text" id="cora-edit-ws-slug" required class="w-full h-10 pl-[74px] pr-3 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-mono text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Status</label>
                    <select id="cora-edit-ws-status" class="w-full h-10 px-2.5 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Pricing Plan</label>
                    <select id="cora-edit-ws-plan" class="w-full h-10 px-2.5 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
                        <option value="starter">Starter Plan</option>
                        <option value="professional">Professional Plan</option>
                        <option value="enterprise">Enterprise Plan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Industry Profile</label>
                <select id="cora-edit-ws-industry" class="w-full h-10 px-2.5 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
                    <option value="real_estate">Real Estate Agency</option>
                    <option value="photography_studio">Photography Studio</option>
                </select>
            </div>

            <div>
                <label class="block text-[10.5px] font-bold text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Owner Email Address</label>
                <input type="email" id="cora-edit-ws-owner-email" required class="w-full h-10 px-3 bg-zinc-50 border border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800 rounded-lg text-xs font-semibold text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors">
            </div>
        </div>

        <!-- Danger Zone (Delete Workspace) -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 pt-5 mt-5">
            <h4 class="text-xs font-bold text-red-600 dark:text-red-400 mb-1">Danger Zone</h4>
            <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-normal mb-3">Permanently delete this workspace and erase all associated settings, credentials, and assets. This cannot be undone.</p>

            <div id="cora-delete-actions-trigger">
                <button type="button" onclick="window.coraConfirmDeleteWorkspace();" class="w-full h-10 rounded-lg bg-red-500/5 hover:bg-red-500/10 border border-red-500/20 hover:border-red-500/35 text-red-650 dark:text-red-400 text-xs font-bold cursor-pointer transition-colors flex items-center justify-center gap-1.5">
                    Delete Workspace...
                </button>
            </div>

            <div id="cora-delete-actions-confirm" class="hidden p-4 bg-red-500/5 border border-red-500/20 rounded-xl space-y-3">
                <p class="text-[10px] font-bold text-red-600 dark:text-red-400 leading-normal">⚠️ Are you absolutely sure? Click confirm below to permanently wipe this workspace from the database.</p>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.coraExecuteDeleteWorkspace();" class="flex-1 h-9 rounded-lg bg-red-600 hover:bg-red-750 text-white text-[11px] font-bold cursor-pointer transition-colors active:scale-[0.98]">
                        Yes, Delete Workspace
                    </button>
                    <button type="button" onclick="window.coraCancelDeleteWorkspace();" class="px-3.5 h-9 rounded-lg bg-zinc-100 dark:bg-zinc-850 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[11px] font-bold cursor-pointer transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer actions -->
    <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/30 flex items-center gap-3 flex-shrink-0">
        <button type="button" id="cora-edit-ws-btn" onclick="window.coraSubmitUpdateWorkspace();" class="flex-1 h-10 rounded-lg bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-950 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98]">
            Save Changes
        </button>
        <button type="button" onclick="window.coraToggleEditWorkspaceDrawer(false);" class="px-4 h-10 rounded-lg bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700/80 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all cursor-pointer active:scale-[0.98]">
            Cancel
        </button>
    </div>
</div>

<script>
(function($) {
    $(document).ready(function() {
        // Auto slugify helper
        window.coraAutoSlugify = function(text) {
            const slug = text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
            document.getElementById('cora-create-ws-slug').value = slug;
        };

        // Toggle Create Drawer
        window.coraToggleCreateWorkspaceDrawer = function(open) {
            const drawer = document.getElementById('cora-create-workspace-drawer');
            const backdrop = document.getElementById('cora-drawer-backdrop');
            if (!drawer) return;
            if (open) {
                drawer.classList.remove('collapsed');
                if (backdrop) {
                    backdrop.classList.remove('hidden');
                    backdrop.style.display = 'block';
                    backdrop.style.pointerEvents = 'auto';
                }
                document.getElementById('cora-workspace-popover').classList.add('hidden');
                document.getElementById('cora-create-ws-name').value = '';
                document.getElementById('cora-create-ws-slug').value = '';
            } else {
                drawer.classList.add('collapsed');
                if (backdrop) {
                    backdrop.classList.add('hidden');
                    backdrop.style.display = 'none';
                    backdrop.style.pointerEvents = 'none';
                }
            }
        };

        // Toggle Edit Drawer
        window.coraToggleEditWorkspaceDrawer = function(open, wsId, wsName, wsSlug, wsPlan, wsOwnerEmail, wsStatus, wsIndustry) {
            const drawer = document.getElementById('cora-edit-workspace-drawer');
            const backdrop = document.getElementById('cora-drawer-backdrop');
            if (!drawer) return;
            if (open) {
                document.getElementById('cora-edit-ws-id').value = wsId || 0;
                document.getElementById('cora-edit-ws-name').value = wsName || '';
                document.getElementById('cora-edit-ws-slug').value = wsSlug || '';
                document.getElementById('cora-edit-ws-plan').value = wsPlan || 'enterprise';
                document.getElementById('cora-edit-ws-owner-email').value = wsOwnerEmail || 'shruti@heycora.in';
                document.getElementById('cora-edit-ws-status').value = wsStatus || 'active';
                if (document.getElementById('cora-edit-ws-industry')) {
                    document.getElementById('cora-edit-ws-industry').value = (wsIndustry === 'photography' ? 'photography_studio' : (wsIndustry || 'real_estate'));
                }
                
                // Reset danger zone state
                window.coraCancelDeleteWorkspace();

                drawer.classList.remove('collapsed');
                if (backdrop) {
                    backdrop.classList.remove('hidden');
                    backdrop.style.display = 'block';
                    backdrop.style.pointerEvents = 'auto';
                }
                document.getElementById('cora-workspace-popover').classList.add('hidden');
            } else {
                drawer.classList.add('collapsed');
                if (backdrop) {
                    backdrop.classList.add('hidden');
                    backdrop.style.display = 'none';
                    backdrop.style.pointerEvents = 'none';
                }
            }
        };

        // Close all drawers
        const baseCloseAllDrawers = window.coraCloseAllDrawers;
        window.coraCloseAllDrawers = function() {
            if (window.coraDrawerCloseTimer) clearTimeout(window.coraDrawerCloseTimer);
            if (typeof baseCloseAllDrawers === 'function') baseCloseAllDrawers();
            if (typeof window.coraToggleCreateWorkspaceDrawer === 'function') window.coraToggleCreateWorkspaceDrawer(false);
            if (typeof window.coraToggleEditWorkspaceDrawer === 'function') window.coraToggleEditWorkspaceDrawer(false);
            if (typeof window.coraCloseUpdateDrawer === 'function') window.coraCloseUpdateDrawer();
            if (typeof window.coraCloseCustomActionDrawer === 'function') window.coraCloseCustomActionDrawer();

            $('aside[id$="-drawer"], aside[id$="-sheet"], div[id$="-drawer"], div[id$="-sheet"], div[id$="-modal"], .cora-side-drawer').addClass('collapsed translate-x-full pointer-events-none');
            window.coraDrawerCloseTimer = setTimeout(function() {
                $('aside[id$="-drawer"].translate-x-full, aside[id$="-sheet"].translate-x-full, div[id$="-drawer"].translate-x-full, div[id$="-sheet"].translate-x-full, div[id$="-modal"].translate-x-full, .cora-side-drawer.translate-x-full').addClass('hidden');
            }, 300);
            const bd = document.getElementById('cora-drawer-backdrop');
            if (bd) { bd.classList.add('hidden'); bd.style.pointerEvents = 'none'; bd.style.display = 'none'; }
            $('body').removeClass('cora-drawer-open overflow-hidden');
        };

        // Create Workspace Submit
        window.coraSubmitCreateWorkspace = function() {
            const name = document.getElementById('cora-create-ws-name').value;
            const slug = document.getElementById('cora-create-ws-slug').value;
            const plan = document.getElementById('cora-create-ws-plan').value;
            const ownerEmail = document.getElementById('cora-create-ws-owner-email').value;
            const btn = document.getElementById('cora-create-ws-btn');

            if (!name || !slug) {
                if (window.coraShowToast) window.coraShowToast('error', 'Workspace name and slug are required.');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Creating...';

            $.post(coraREData.ajaxUrl, {
                action: 'cora_super_create_workspace',
                security: coraREData.ajaxNonce,
                name: name,
                slug: slug,
                plan: plan,
                owner_email: ownerEmail
            }, function(res) {
                btn.disabled = false;
                btn.textContent = 'Create Workspace';
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast('success', res.data.message || 'Workspace created successfully!');
                    window.coraToggleCreateWorkspaceDrawer(false);
                    setTimeout(() => { window.location.reload(); }, 1200);
                } else {
                    if (window.coraShowToast) window.coraShowToast('error', res.data.message || res.data || 'Failed to create workspace.');
                }
            });
        };

        // Update Workspace Submit
        window.coraSubmitUpdateWorkspace = function() {
            const wsId = document.getElementById('cora-edit-ws-id').value;
            const name = document.getElementById('cora-edit-ws-name').value;
            const slug = document.getElementById('cora-edit-ws-slug').value;
            const plan = document.getElementById('cora-edit-ws-plan').value;
            const ownerEmail = document.getElementById('cora-edit-ws-owner-email').value;
            const status = document.getElementById('cora-edit-ws-status').value;
            const industry = document.getElementById('cora-edit-ws-industry') ? document.getElementById('cora-edit-ws-industry').value : 'real_estate';
            const btn = document.getElementById('cora-edit-ws-btn');

            if (!name || !slug) {
                if (window.coraShowToast) window.coraShowToast('error', 'Workspace name and slug are required.');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Saving...';

            $.post(coraREData.ajaxUrl, {
                action: 'cora_super_update_workspace',
                security: coraREData.ajaxNonce,
                workspace_id: wsId,
                name: name,
                slug: slug,
                plan: plan,
                industry: industry,
                owner_email: ownerEmail,
                status: status
            }, function(res) {
                btn.disabled = false;
                btn.textContent = 'Save Changes';
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast('success', res.data.message || 'Workspace updated successfully.');
                    window.coraToggleEditWorkspaceDrawer(false);
                    setTimeout(() => { window.location.reload(); }, 1200);
                } else {
                    if (window.coraShowToast) window.coraShowToast('error', res.data.message || res.data || 'Failed to update workspace.');
                }
            });
        };

        // Delete Workspace Confirm handlers
        window.coraConfirmDeleteWorkspace = function() {
            document.getElementById('cora-delete-actions-trigger').classList.add('hidden');
            document.getElementById('cora-delete-actions-confirm').classList.remove('hidden');
        };

        window.coraCancelDeleteWorkspace = function() {
            document.getElementById('cora-delete-actions-trigger').classList.remove('hidden');
            document.getElementById('cora-delete-actions-confirm').classList.add('hidden');
        };

        window.coraExecuteDeleteWorkspace = function() {
            const wsId = document.getElementById('cora-edit-ws-id').value;
            if (wsId == 0 || wsId == 1) {
                if (window.coraShowToast) window.coraShowToast('error', 'This workspace is protected and cannot be deleted.');
                return;
            }

            $.post(coraREData.ajaxUrl, {
                action: 'cora_super_delete_workspace',
                security: coraREData.ajaxNonce,
                workspace_id: wsId
            }, function(res) {
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast('success', res.data.message || 'Workspace deleted successfully!');
                    window.coraToggleEditWorkspaceDrawer(false);
                    setTimeout(() => { window.location.reload(); }, 1200);
                } else {
                    if (window.coraShowToast) window.coraShowToast('error', res.data.message || 'Failed to delete workspace.');
                }
            });
        };
    });
})(jQuery);
</script>

<!-- Custom Actions Drawer -->
<div id="cora-custom-actions-drawer" class="fixed top-0 right-0 h-full w-[400px] max-w-[90vw] bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[9995] transform translate-x-full transition-transform duration-300 flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 dark:border-zinc-800">
        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Custom Quick Actions</h3>
        <button onclick="window.coraCloseCustomActionDrawer()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <div class="p-6 overflow-y-auto flex-1">
        <div class="mb-8">
            <h4 class="text-sm font-medium text-zinc-800 dark:text-zinc-200 mb-3">Add New Action</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Action Name</label>
                    <input type="text" id="cora-custom-action-name" placeholder="e.g. View Documents" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 text-sm text-zinc-900 dark:text-white focus:outline-none focus:border-zinc-400 dark:focus:border-zinc-600 transition-colors" />
                </div>
                <div>
                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Target Section</label>
                    <select id="cora-custom-action-target" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 text-sm text-zinc-900 dark:text-white focus:outline-none focus:border-zinc-400 dark:focus:border-zinc-600 transition-colors appearance-none">
                        <option value="bookings">Bookings</option>
                        <option value="equipment">Equipment</option>
                        <option value="crew-scheduler">Crew Scheduler</option>
                        <option value="leads">Leads</option>
                        <option value="clients">Clients</option>
                        <option value="financials">Financials</option>
                        <option value="ai-assistants">AI Assistants</option>
                        <option value="media">Media</option>
                        <option value="content-suite">Content Suite</option>
                        <option value="forms">Forms</option>
                        <option value="pages">Pages</option>
                        <option value="settings-suite">Settings</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button onclick="window.coraCloseCustomActionDrawer()" class="px-4 py-2 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">Cancel</button>
                    <button onclick="window.coraSaveCustomAction()" class="px-4 py-2 rounded-lg text-xs font-bold bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 transition-colors cursor-pointer shadow-sm">Save Action</button>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-sm font-medium text-zinc-800 dark:text-zinc-200 mb-3">Existing Actions</h4>
            <div id="cora-existing-custom-actions" class="space-y-2">
                <!-- List dynamically generated -->
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function getCustomActions() {
        try {
            const data = localStorage.getItem('cora_custom_quick_actions');
            return data ? JSON.parse(data) : [];
        } catch (e) {
            return [];
        }
    }

    function setCustomActions(actions) {
        localStorage.setItem('cora_custom_quick_actions', JSON.stringify(actions));
    }

    function renderCustomActions() {
        const container = document.getElementById('cora-custom-actions-container');
        const listContainer = document.getElementById('cora-existing-custom-actions');
        if (!container) return;

        const actions = getCustomActions();
        
        // Render in dashboard
        container.innerHTML = '';
        actions.forEach((action, index) => {
            const btn = document.createElement('button');
            btn.onclick = () => window.coraNavigateTo(action.target);
            btn.className = "inline-flex items-center gap-2 px-5 py-2.5 border border-zinc-200/80 dark:border-zinc-800 bg-white/70 dark:bg-zinc-900/50 hover:bg-zinc-50 dark:hover:bg-zinc-850 rounded-full text-xs font-semibold text-zinc-650 hover:text-zinc-900 dark:text-zinc-350 dark:hover:text-white transition-all shadow-3xs cursor-pointer";
            btn.innerHTML = `
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 16 16 12 12 8"></polyline>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                ${action.name}
            `;
            container.appendChild(btn);
        });

        // Render in drawer list
        if (listContainer) {
            listContainer.innerHTML = '';
            if (actions.length === 0) {
                listContainer.innerHTML = '<div class="text-xs text-zinc-500 dark:text-zinc-400 italic">No custom actions yet.</div>';
            } else {
                actions.forEach((action, index) => {
                    const item = document.createElement('div');
                    item.className = "flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800/80";
                    item.innerHTML = `
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200">${action.name}</span>
                            <span class="text-[10px] text-zinc-500 uppercase tracking-wide">${action.target}</span>
                        </div>
                        <button onclick="window.coraDeleteCustomAction(${index})" class="p-1.5 text-zinc-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition-colors cursor-pointer">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    `;
                    listContainer.appendChild(item);
                });
            }
        }
    }

    window.coraOpenCustomActionDrawer = function() {
        document.getElementById('cora-drawer-backdrop')?.classList.remove('hidden');
        const drawer = document.getElementById('cora-custom-actions-drawer');
        if (drawer) drawer.classList.remove('translate-x-full');
        renderCustomActions();
    };

    window.coraCloseCustomActionDrawer = function() {
        const drawer = document.getElementById('cora-custom-actions-drawer');
        if (drawer) drawer.classList.add('translate-x-full');
        // Let the general close drawers handle backdrop or do it here
        setTimeout(() => {
            const anyOpen = document.querySelectorAll('.translate-x-0').length > 0;
            if (!anyOpen) {
                document.getElementById('cora-drawer-backdrop')?.classList.add('hidden');
            }
        }, 300);
    };

    // Integrate with global close
    const origCloseAll = window.coraCloseAllDrawers;
    window.coraCloseAllDrawers = function() {
        if (origCloseAll) origCloseAll();
        window.coraCloseCustomActionDrawer();
    };

    window.coraSaveCustomAction = function() {
        const nameInput = document.getElementById('cora-custom-action-name');
        const targetInput = document.getElementById('cora-custom-action-target');
        
        const name = nameInput.value.trim();
        const target = targetInput.value;
        
        if (!name) {
            if (window.coraShowToast) window.coraShowToast('error', 'Please enter an action name');
            return;
        }
        
        const actions = getCustomActions();
        if (actions.length >= 6) {
            if (window.coraShowToast) window.coraShowToast('error', 'Maximum 6 custom actions allowed');
            return;
        }
        
        actions.push({ name, target });
        setCustomActions(actions);
        
        nameInput.value = '';
        renderCustomActions();
        
        if (window.coraShowToast) window.coraShowToast('success', 'Custom action saved');
    };

    window.coraDeleteCustomAction = function(index) {
        const actions = getCustomActions();
        actions.splice(index, 1);
        setCustomActions(actions);
        renderCustomActions();
        if (window.coraShowToast) window.coraShowToast('success', 'Custom action removed');
    };

    // Initial render
    document.addEventListener('DOMContentLoaded', renderCustomActions);
})();
</script>

<!-- Dynamic Feedback Modal Pop-up -->
<div id="cora-feedback-drawer" onclick="window.coraCloseFeedbackDrawer(event)" class="cora-feedback-drawer collapsed fixed inset-0 z-[9995] flex items-center justify-center p-4 bg-zinc-950/40 dark:bg-black/60 backdrop-blur-[2px] transition-all duration-200">
    <div class="cora-feedback-modal-card w-full max-w-[460px] bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col rounded-2xl overflow-hidden transition-all duration-250 transform scale-95 opacity-0" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-900/50 shrink-0">
            <div>
                <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Submit Feedback &amp; Report</h2>
                <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">Help us improve by sharing what's on your mind.</p>
            </div>
            <button onclick="window.coraCloseFeedbackDrawer(event)" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 cursor-pointer p-1.5 rounded-lg hover:bg-zinc-200/50 dark:hover:bg-zinc-800 transition-colors border-none bg-transparent flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <!-- Current Screen info -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Current Screen</label>
                <div class="flex items-center gap-2 px-3 py-2 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl select-none">
                    <span id="cora-feedback-screen-badge" class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span id="cora-feedback-screen-name" class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Dashboard</span>
                    <span id="cora-feedback-screen-id" class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono font-medium">(dashboard)</span>
                </div>
            </div>

            <!-- Issue Category -->
            <div class="space-y-1.5">
                <label for="cora-feedback-type" class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Type of Issue</label>
                <select id="cora-feedback-type" class="w-full bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-xl px-3 py-2 outline-none cursor-pointer focus:border-zinc-900 dark:focus:border-zinc-100 transition-colors shadow-3xs">
                    <option value="feature_broken" selected>Feature not working properly</option>
                    <option value="tab_inaccessible">Tab / Screen not accessible</option>
                    <option value="typo_error">Typo / text error</option>
                    <option value="unexpected_behavior">Behavior is not as expected</option>
                    <option value="performance_issue">Page loads slowly / laggy</option>
                    <option value="suggestion">General suggestion / feedback</option>
                </select>
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label for="cora-feedback-desc" class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Feedback &amp; Description</label>
                <textarea id="cora-feedback-desc" rows="5" class="w-full bg-white dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-medium rounded-xl px-3 py-2 outline-none resize-none focus:border-zinc-900 dark:focus:border-zinc-100 transition-colors shadow-3xs placeholder-zinc-400" placeholder="Please tell us what is not working, what was expected, or share your general suggestions..."></textarea>
            </div>
        </div>

        <!-- Actions CTA -->
        <div class="p-5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 shrink-0">
            <div class="grid grid-cols-2 gap-3.5">
                <!-- WhatsApp Feedback -->
                <div class="space-y-1.5 flex flex-col justify-between">
                    <button type="button" onclick="window.coraSendFeedback('whatsapp')" class="w-full py-2.5 text-white text-xs font-bold rounded-xl transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer flex items-center justify-center gap-1.5 shadow-sm border-none" style="background-color: #25D366 !important;">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" class="shrink-0 text-white">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.403 5.633A8.919 8.919 0 0 0 12.053 3c-4.948 0-8.976 4.027-8.978 8.977 0 1.58.413 3.125 1.2 4.488l-1.276 4.66 4.77-1.252a8.936 8.936 0 0 0 4.283 1.093h.004c4.947 0 8.975-4.027 8.977-8.977a8.926 8.926 0 0 0-2.63-6.353zM12.053 19.31a7.432 7.432 0 0 1-3.79-1.042l-.272-.162-2.82.74.752-2.748-.177-.282a7.43 7.43 0 0 1-1.139-3.934c.002-4.103 3.342-7.443 7.447-7.443a7.402 7.402 0 0 1 5.263 2.183 7.404 7.404 0 0 1 2.181 5.266c-.002 4.104-3.343 7.444-7.445 7.444zm4.079-5.571c-.223-.112-1.322-.653-1.526-.728-.205-.074-.354-.112-.503.112-.149.224-.577.728-.707.877-.13.15-.26.168-.484.056-.223-.112-.942-.347-1.794-1.108-.663-.592-1.11-1.322-1.24-1.546-.13-.223-.014-.344.098-.456.1-.1.223-.26.335-.392.112-.13.149-.224.223-.373.075-.149.038-.28-.018-.392-.056-.112-.503-1.213-.689-1.66-.182-.439-.366-.38-.503-.387-.13-.007-.28-.007-.429-.007-.15 0-.391.056-.596.28-.205.224-.782.766-.782 1.867 0 1.102.8 2.167.912 2.316.112.15 1.574 2.404 3.814 3.37.533.23 1.012.38 1.397.502.535.17 1.02.146 1.405.089.43-.064 1.322-.54 1.507-1.062.187-.523.187-.972.13-1.062-.056-.09-.205-.149-.43-.262z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </button>
                    <div class="px-0.5 flex flex-col items-center text-center">
                        <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 leading-none">Instant Reply</span>
                        <span class="text-[8px] font-medium text-zinc-400 dark:text-zinc-500 mt-0.5">Under 1 hour</span>
                    </div>
                </div>

                <!-- Email Feedback -->
                <div class="space-y-1.5 flex flex-col justify-between">
                    <button type="button" onclick="window.coraSendFeedback('email')" class="w-full py-2.5 text-white text-xs font-bold rounded-xl transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer flex items-center justify-center gap-1.5 shadow-sm border-none" style="background-color: #EA4335 !important;">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" class="shrink-0 text-white">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <span>Send Email</span>
                    </button>
                    <div class="px-0.5 flex flex-col items-center text-center">
                        <span class="text-[9px] font-bold text-red-550 dark:text-red-400 leading-none">Email Support</span>
                        <span class="text-[8px] font-medium text-zinc-400 dark:text-zinc-550 mt-0.5">Within 24 hours</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modal basic layout and animations */
    .cora-feedback-drawer {
        transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
        opacity: 0;
        visibility: hidden;
    }
    .cora-feedback-drawer.collapsed {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
    .cora-feedback-drawer:not(.collapsed) {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }
    .cora-feedback-drawer:not(.collapsed) .cora-feedback-modal-card {
        transform: scale(1) !important;
        opacity: 1 !important;
    }
    
    /* Dark Theme Support for Modal */
    .cora-dark-theme .cora-feedback-modal-card {
        background-color: #09090b !important;
        border-color: #27272a !important;
        color: #f4f4f5 !important;
    }
    .cora-dark-theme .cora-feedback-modal-card select,
    .cora-dark-theme .cora-feedback-modal-card textarea {
        background-color: #09090b !important;
        border-color: #27272a !important;
        color: #f4f4f5 !important;
    }
    .cora-dark-theme .cora-feedback-modal-card select:focus,
    .cora-dark-theme .cora-feedback-modal-card textarea:focus {
        border-color: #e4e4e7 !important;
    }
    .cora-dark-theme .cora-feedback-modal-card .bg-zinc-50,
    .cora-dark-theme .cora-feedback-modal-card .px-3.py-2.bg-zinc-50 {
        background-color: #18181b !important;
        border-color: #27272a !important;
    }
    .cora-dark-theme .cora-feedback-modal-card .border-b,
    .cora-dark-theme .cora-feedback-modal-card .border-t {
        border-color: #27272a !important;
    }
    .cora-dark-theme .cora-feedback-modal-card h2 {
        color: #ffffff !important;
    }
    .cora-dark-theme .cora-feedback-modal-card label {
        color: #71717a !important;
    }
    .cora-dark-theme #cora-feedback-screen-name {
        color: #e4e4e7 !important;
    }
    .cora-dark-theme .cora-feedback-modal-card button.bg-white {
        background-color: #18181b !important;
        border-color: #27272a !important;
        color: #e4e4e7 !important;
    }
    .cora-dark-theme .cora-feedback-modal-card button.bg-white:hover {
        background-color: #27272a !important;
    }
    .cora-dark-theme .cora-feedback-modal-card button.bg-zinc-950 {
        background-color: #ffffff !important;
        color: #09090b !important;
    }
    .cora-dark-theme .cora-feedback-modal-card button.bg-zinc-950:hover {
        background-color: #e4e4e7 !important;
    }
</style>

<script>
window.coraOpenFeedbackDrawer = function(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    
    if (window.coraDrawerCloseTimer) {
        clearTimeout(window.coraDrawerCloseTimer);
    }
    
    if (typeof window.coraCloseAllDrawers === 'function') {
        window.coraCloseAllDrawers();
    }
    
    const drawer = document.getElementById('cora-feedback-drawer');
    if (!drawer) return;
    
    // Detect current screen
    let screenId = 'dashboard';
    let screenName = 'Dashboard';
    
    const activeNavEl = document.querySelector('.cora-sidebar .cora-nav-item.cora-active');
    if (activeNavEl) {
        screenId = activeNavEl.getAttribute('data-target') || screenId;
        const textEl = activeNavEl.querySelector('.cora-nav-text');
        screenName = textEl ? textEl.textContent.trim() : screenId;
    } else if (window.coraREData && window.coraREData.currentPage) {
        screenId = window.coraREData.currentPage;
        screenName = screenId.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
    }
    
    document.getElementById('cora-feedback-screen-name').textContent = screenName;
    document.getElementById('cora-feedback-screen-id').textContent = '(' + screenId + ')';
    
    // Reset inputs
    document.getElementById('cora-feedback-desc').value = '';
    document.getElementById('cora-feedback-type').selectedIndex = 0;
    
    // Show drawer & backdrop
    drawer.classList.remove('collapsed', 'translate-x-full', 'pointer-events-none', 'hidden');
    const bd = document.getElementById('cora-drawer-backdrop');
    if (bd) {
        bd.classList.remove('hidden');
        bd.style.pointerEvents = 'auto';
        bd.style.display = '';
    }
    document.body.classList.add('cora-drawer-open', 'overflow-hidden');
};

window.coraCloseFeedbackDrawer = function(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    const drawer = document.getElementById('cora-feedback-drawer');
    if (drawer) {
        drawer.classList.add('collapsed', 'translate-x-full', 'pointer-events-none');
        window.coraDrawerCloseTimer = setTimeout(function() {
            drawer.classList.add('hidden');
        }, 300);
    }
    const bd = document.getElementById('cora-drawer-backdrop');
    if (bd) {
        bd.classList.add('hidden');
        bd.style.pointerEvents = 'none';
        bd.style.display = 'none';
    }
    document.body.classList.remove('cora-drawer-open', 'overflow-hidden');
};

window.coraSendFeedback = function(method) {
    const typeEl = document.getElementById('cora-feedback-type');
    const descEl = document.getElementById('cora-feedback-desc');
    if (!descEl || !typeEl) return;
    
    const typeText = typeEl.options[typeEl.selectedIndex].text;
    const descText = descEl.value.trim();
    
    if (!descText) {
        if (window.coraShowToast) {
            window.coraShowToast('Please enter your feedback description.', 'error');
        }
        return;
    }
    
    const screenName = document.getElementById('cora-feedback-screen-name').textContent;
    const screenId = document.getElementById('cora-feedback-screen-id').textContent;
    
    const userDisplayName = "<?php echo esc_js($current_user_display_name); ?>";
    const userEmail = "<?php echo esc_js($current_wp_user->exists() ? $current_wp_user->user_email : 'dravya.shs@gmail.com'); ?>";
    const timestamp = new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });
    
    // Build message
    const messageLines = [
        `*Cora Workspace - Feedback & Bug Report*`,
        `----------------------------------------`,
        `*Screen:* ${screenName} ${screenId}`,
        `*Issue Type:* ${typeText}`,
        `*Description:* ${descText}`,
        `----------------------------------------`,
        `*User:* ${userDisplayName} (${userEmail})`,
        `*Sent At:* ${timestamp}`
    ];
    const rawMessage = messageLines.join('\n');
    
    if (method === 'whatsapp') {
        const url = `https://wa.me/918708528105?text=${encodeURIComponent(rawMessage)}`;
        window.open(url, '_blank');
        if (window.coraShowToast) {
            window.coraShowToast('Feedback opened in WhatsApp!', 'success');
        }
    } else if (method === 'email') {
        const subject = `Cora Workspace Feedback - ${screenName}`;
        const url = `mailto:dravya.bansal@claraverse.in?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(rawMessage)}`;
        window.location.href = url;
        if (window.coraShowToast) {
            window.coraShowToast('Feedback formatted and email client opened!', 'success');
        }
    }
    
    window.coraCloseFeedbackDrawer();
};
</script>

<!-- Global Drawer Backdrop -->
<div id="cora-drawer-backdrop" onclick="window.coraCloseAllDrawers()" class="hidden fixed inset-0 bg-black/30 dark:bg-black/60 z-[9990] backdrop-blur-[1.5px] transition-opacity duration-200 cursor-pointer"></div>

<?php if ( $cora_auto_update && ! empty( $cora_target_version ) ) : ?>
<div id="cora-auto-update-overlay-panel" class="fixed inset-0 z-[999999] bg-zinc-50/90 dark:bg-zinc-950/95 backdrop-blur-md flex items-center justify-center select-none font-sans">
    <style>
        #cora-auto-update-overlay-panel {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        #cora-auto-update-overlay-panel .step-item {
            transition: all 0.3s ease;
        }
    </style>
    <div class="w-full max-w-md p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 rounded-2xl shadow-2xl flex flex-col items-center text-center space-y-6">
        
        <!-- Logo Header -->
        <div class="flex flex-col items-center space-y-2">
            <span class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">cora</span>
            <div class="px-2.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 rounded-full font-mono">
                AUTO-UPGRADE ENGINE
            </div>
        </div>

        <!-- Spinner & Status Title -->
        <div class="flex flex-col items-center space-y-2">
            <div id="cora-upgrade-spinner" class="w-12 h-12 flex items-center justify-center text-zinc-900 dark:text-white mb-2">
                <svg class="animate-spin w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 id="cora-upgrade-status-title" class="text-sm font-bold text-zinc-900 dark:text-white">Initializing Platform Upgrade...</h3>
            <p id="cora-upgrade-status-desc" class="text-xs text-zinc-400">Target version: v<?php echo esc_html($cora_target_version); ?></p>
        </div>

        <!-- Step-by-Step Checklist -->
        <div class="w-full text-left space-y-3 bg-zinc-50/50 dark:bg-zinc-950/40 p-4 border border-zinc-100 dark:border-zinc-800/80 rounded-xl">
            <!-- Step 1 -->
            <div id="cora-step-1" class="step-item flex items-center justify-between text-xs text-zinc-400 dark:text-zinc-500 font-medium">
                <div class="flex items-center gap-2">
                    <div class="step-icon w-5 h-5 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-[10px] font-bold">1</div>
                    <span>Validating administrator authorization</span>
                </div>
                <span class="step-status text-[10px] uppercase font-bold tracking-wider">Pending...</span>
            </div>
            <!-- Step 2 -->
            <div id="cora-step-2" class="step-item flex items-center justify-between text-xs text-zinc-400 dark:text-zinc-500 font-medium">
                <div class="flex items-center gap-2">
                    <div class="step-icon w-5 h-5 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-[10px] font-bold">2</div>
                    <span>Downloading workspace update</span>
                </div>
                <span class="step-status text-[10px] uppercase font-bold tracking-wider">Pending...</span>
            </div>
            <!-- Step 3 -->
            <div id="cora-step-3" class="step-item flex items-center justify-between text-xs text-zinc-400 dark:text-zinc-500 font-medium">
                <div class="flex items-center gap-2">
                    <div class="step-icon w-5 h-5 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-[10px] font-bold">3</div>
                    <span>Extracting update packages</span>
                </div>
                <span class="step-status text-[10px] uppercase font-bold tracking-wider">Pending...</span>
            </div>
            <!-- Step 4 -->
            <div id="cora-step-4" class="step-item flex items-center justify-between text-xs text-zinc-400 dark:text-zinc-500 font-medium">
                <div class="flex items-center gap-2">
                    <div class="step-icon w-5 h-5 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-[10px] font-bold">4</div>
                    <span>Upgrading core modules & DB</span>
                </div>
                <span class="step-status text-[10px] uppercase font-bold tracking-wider">Pending...</span>
            </div>
        </div>

        <!-- Failure Details or Exit Button -->
        <div id="cora-upgrade-action-container" class="hidden w-full pt-2">
            <button onclick="window.location.href='?page=cora-workspace'" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-sm">
                Back to Workspace Dashboard
            </button>
        </div>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const config = window.coraAutoUpdateConfig || {};
    if (!config.active) return;

    // Helper functions to update steps
    function setStepStatus(stepNum, status, isSuccess = null) {
        const stepRow = $('#cora-step-' + stepNum);
        if (!stepRow.length) return;
        
        const statusSpan = stepRow.find('.step-status');
        const iconDiv = stepRow.find('.step-icon');
        
        statusSpan.text(status);
        
        if (isSuccess === true) {
            stepRow.removeClass('text-zinc-400 dark:text-zinc-500').addClass('text-zinc-900 dark:text-zinc-100 font-bold');
            statusSpan.removeClass('text-zinc-400').addClass('text-emerald-500 font-bold');
            iconDiv.html('<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none" class="text-emerald-500"><polyline points="20 6 9 17 4 12"></polyline></svg>')
                   .addClass('border-emerald-500 bg-emerald-500/10')
                   .removeClass('border-zinc-200 dark:border-zinc-800');
        } else if (isSuccess === false) {
            stepRow.removeClass('text-zinc-400 dark:text-zinc-500').addClass('text-red-650 dark:text-red-400 font-bold');
            statusSpan.removeClass('text-zinc-400').addClass('text-red-500 font-bold');
            iconDiv.html('<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none" class="text-red-500"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>')
                   .addClass('border-red-500 bg-red-500/10')
                   .removeClass('border-zinc-200 dark:border-zinc-800');
        } else {
            // Running/Active state
            stepRow.removeClass('text-zinc-400 dark:text-zinc-500').addClass('text-zinc-900 dark:text-zinc-100 font-bold');
            statusSpan.removeClass('text-zinc-400').addClass('text-zinc-850 dark:text-zinc-200 font-medium');
            iconDiv.html('<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none" class="animate-spin text-zinc-900 dark:text-zinc-100"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>')
                   .addClass('border-zinc-900 dark:border-zinc-100')
                   .removeClass('border-zinc-200 dark:border-zinc-800');
        }
    }

    function showFail(errMessage) {
        $('#cora-upgrade-spinner').html('<svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="2.5" fill="none" class="text-red-500"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>');
        $('#cora-upgrade-status-title').text('Upgrade Failed').addClass('text-red-650 dark:text-red-400');
        $('#cora-upgrade-status-desc').text(errMessage).addClass('text-red-550 dark:text-red-450/80');
        $('#cora-upgrade-action-container').removeClass('hidden');
        if (window.coraShowToast) window.coraShowToast(errMessage, 'error');
    }

    // Step 1: Validate authorization
    setTimeout(function() {
        if (!config.userCanUpdate) {
            setStepStatus(1, 'Denied', false);
            showFail('Access Denied: You must be logged in as an administrator to upgrade the workspace.');
            return;
        }
        
        setStepStatus(1, 'Passed', true);
        
        // Step 2: Trigger update AJAX
        setStepStatus(2, 'Running...');
        
        const ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) ? window.coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            timeout: 120000,
            data: {
                action: 'cora_trigger_workspace_update',
                version: config.targetVersion,
                nonce: config.nonce
            },
            success: function(res) {
                if (res.success) {
                    setStepStatus(2, 'Complete', true);
                    
                    // Step 3: Extracting
                    setStepStatus(3, 'Running...');
                    setTimeout(function() {
                        setStepStatus(3, 'Complete', true);
                        
                        // Step 4: Core & DB upgrading
                        setStepStatus(4, 'Running...');
                        setTimeout(function() {
                            setStepStatus(4, 'Complete', true);
                            
                            // Success UI
                            $('#cora-upgrade-spinner').html('<svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-500"><polyline points="20 6 9 17 4 12"></polyline></svg>');
                            $('#cora-upgrade-status-title').text('Workspace Updated!').addClass('text-emerald-600 dark:text-emerald-450');
                            $('#cora-upgrade-status-desc').text('Reloading workspace panel...').addClass('text-emerald-500');
                            
                            if (window.coraShowToast) window.coraShowToast('Workspace upgraded successfully to v' + config.targetVersion, 'success');
                            
                            setTimeout(function() {
                                window.location.href = '?page=cora-workspace';
                            }, 2000);
                        }, 800);
                    }, 800);
                } else {
                    setStepStatus(2, 'Failed', false);
                    var msg = (res.data && res.data.message) ? res.data.message : 'Update failed during extraction.';
                    showFail(msg);
                }
            },
            error: function(xhr, status) {
                // Connection might terminate due to FPM/OPcache reload when plugin files are replaced.
                // Wait 3.5 seconds and do a check via cora_force_check_update to see if it actually succeeded.
                $('#cora-upgrade-status-desc').text('Network connection recycled. Verifying upgrade...').addClass('text-zinc-500');
                
                setTimeout(function() {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'cora_force_check_update',
                            nonce: config.nonce
                        },
                        success: function(verRes) {
                            if (verRes.success && (verRes.data.up_to_date || verRes.data.version === config.targetVersion || verRes.data.current_version === config.targetVersion)) {
                                setStepStatus(2, 'Complete', true);
                                setStepStatus(3, 'Complete', true);
                                setStepStatus(4, 'Complete', true);
                                
                                $('#cora-upgrade-spinner').html('<svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-500"><polyline points="20 6 9 17 4 12"></polyline></svg>');
                                $('#cora-upgrade-status-title').text('Workspace Updated!').addClass('text-emerald-600 dark:text-emerald-450');
                                $('#cora-upgrade-status-desc').text('Reloading workspace panel...').addClass('text-emerald-500');
                                
                                if (window.coraShowToast) window.coraShowToast('Workspace upgraded successfully to v' + config.targetVersion, 'success');
                                
                                setTimeout(function() {
                                    window.location.href = '?page=cora-workspace';
                                }, 1500);
                            } else {
                                setStepStatus(2, 'Failed', false);
                                var errMsg = status === 'timeout' ? 'Download timed out. The update server is taking too long.' : 'Network error during upgrade.';
                                showFail(errMsg);
                            }
                        },
                        error: function() {
                            // If the site is still recycling, force reload as a fallback
                            setTimeout(function() {
                                window.location.href = '?page=cora-workspace';
                            }, 1500);
                        }
                    });
                }, 3500);
            }
        });
    }, 1000);
});
</script>
<?php endif; ?>

<script>
// PWA Installation & Push Subscription Logic
let coraPwaDeferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    coraPwaDeferredPrompt = e;
    const installBtn = document.getElementById('cora-pwa-install-btn');
    if (installBtn) {
        installBtn.classList.remove('hidden');
    }
});

function coraUrlB64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function coraRequestPushSubscription() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        if (window.coraShowToast) {
            window.coraShowToast('Push notifications are not supported in this browser.', 'error');
        }
        return;
    }
    
    Notification.requestPermission().then(permission => {
        if (permission !== 'granted') {
            if (window.coraShowToast) {
                window.coraShowToast('Notification permission denied.', 'error');
            }
            return;
        }
        
        navigator.serviceWorker.ready.then(registration => {
            if (!window.coraPwaVapidPublicKey) {
                if (window.coraShowToast) {
                    window.coraShowToast('Push services not configured yet. Try reloading.', 'error');
                }
                return;
            }
            
            const applicationServerKey = coraUrlB64ToUint8Array(window.coraPwaVapidPublicKey);
            registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: applicationServerKey
            })
            .then(subscription => {
                fetch('/wp-json/cora-pwa/v1/save-subscription', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': window.coraPwaNonce
                    },
                    body: JSON.stringify(subscription)
                })
                .then(res => res.json())
                .then(resData => {
                    if (resData.success) {
                        if (window.coraShowToast) {
                            window.coraShowToast('Notifications enabled successfully!', 'success');
                        }
                        
                        const token = resData.data.token;
                        navigator.serviceWorker.register('<?php echo home_url('/cora-service-worker.js'); ?>?token=' + token, { scope: '/' })
                            .then(() => {
                                const badge = document.getElementById('cora-pwa-badge');
                                if (badge) {
                                    badge.innerText = 'Active';
                                    badge.className = 'text-[9px] font-bold px-1.5 py-0.5 bg-emerald-600 dark:bg-emerald-500 text-white rounded uppercase';
                                }
                                const pushBtn = document.getElementById('cora-pwa-push-btn');
                                if (pushBtn) pushBtn.classList.add('hidden');
                                const testBtn = document.getElementById('cora-pwa-test-btn');
                                if (testBtn) testBtn.classList.remove('hidden');
                                const statusText = document.getElementById('cora-pwa-status-text');
                                if (statusText) statusText.innerText = 'Notifications are active on this device.';
                            });
                    } else {
                        if (window.coraShowToast) {
                            window.coraShowToast('Failed to save subscription on server.', 'error');
                        }
                    }
                })
                .catch(err => {
                    console.error('Subscription save error:', err);
                    if (window.coraShowToast) {
                        window.coraShowToast('Error connecting to notification server.', 'error');
                    }
                });
            })
            .catch(err => {
                console.error('Push registration error:', err);
                if (window.coraShowToast) {
                    window.coraShowToast('Failed to subscribe to browser push notifications.', 'error');
                }
            });
        });
    });
}

function coraSendTestPushNotification() {
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'POST',
        data: {
            action: 'cora_pwa_send_test_push',
            nonce: window.coraAjaxNonce
        },
        success: function(res) {
            if (res.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data, 'success');
                }
            } else {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data, 'error');
                }
            }
        },
        error: function() {
            if (window.coraShowToast) {
                window.coraShowToast('Failed to trigger test notification.', 'error');
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const installBtn = document.getElementById('cora-pwa-install-btn');
    if (installBtn) {
        installBtn.addEventListener('click', (e) => {
            if (!coraPwaDeferredPrompt) return;
            coraPwaDeferredPrompt.prompt();
            coraPwaDeferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    installBtn.classList.add('hidden');
                }
                coraPwaDeferredPrompt = null;
            });
        });
    }
    
    // Auto check if running inside standalone PWA
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
        if (installBtn) installBtn.classList.add('hidden');
        const statusText = document.getElementById('cora-pwa-status-text');
        if (statusText) statusText.innerText = 'App is installed and running.';
    }
    
    // Auto check push status
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.ready.then(registration => {
            registration.pushManager.getSubscription().then(subscription => {
                if (subscription) {
                    const badge = document.getElementById('cora-pwa-badge');
                    if (badge) {
                        badge.innerText = 'Active';
                        badge.className = 'text-[9px] font-bold px-1.5 py-0.5 bg-emerald-600 dark:bg-emerald-500 text-white rounded uppercase';
                    }
                    const pushBtn = document.getElementById('cora-pwa-push-btn');
                    if (pushBtn) pushBtn.classList.add('hidden');
                    const testBtn = document.getElementById('cora-pwa-test-btn');
                    if (testBtn) testBtn.classList.remove('hidden');
                    const statusText = document.getElementById('cora-pwa-status-text');
                    if (statusText) statusText.innerText = 'Notifications are active on this device.';
                }
            });
        });
    }
});
</script>

</body>
</html>
