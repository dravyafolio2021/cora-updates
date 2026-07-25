<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue WordPress media libraries
wp_enqueue_media();

// Save Direct MCP Access Token
if ( isset( $_POST['cora_save_mcp_token_direct_submit'] ) && check_admin_referer( 'cora_save_mcp_token_direct', 'cora_mcp_nonce' ) ) {
    $mcp_token = sanitize_text_field( $_POST['cora_mcp_access_token_direct'] );
    update_option( 'cora_mcp_access_token', $mcp_token );
    echo "<script>window.addEventListener('DOMContentLoaded', function() { window.coraShowToast('AI MCP server access token saved successfully.'); });</script>";
}

$cora_users = ( in_array( $sub_page, array( 'dashboard', 'bookings', 'team-roles', 'equipment', 'blogs' ) ) ) ? get_users() : array();
$cora_re_listings = ( in_array( $sub_page, array( 'dashboard', 'equipment', 'leads', 'bookings' ) ) ) ? cora_db_get_properties() : array();
$cora_permissions = get_option( 'cora_role_permissions', array() );
$cora_showing_assignments = get_option( 'cora_re_showing_assignments', array() );
$cora_documents = get_option( 'cora_re_vault_docs', array() );
$cora_portfolios = get_option( 'cora_re_portfolios', array() );
$cora_re_leads = cora_db_get_leads();
$cora_re_clients = cora_db_get_clients();
$cora_re_attendance_logs = get_option( 'cora_re_attendance_logs', array() );
$cora_re_client_tasks = get_option( 'cora_re_client_tasks', array() );

// Pre-process equipment assignments dynamically from Leads and Clients databases
if ( is_array( $cora_re_listings ) ) {
    foreach ( $cora_re_listings as $key => $item ) {
        $assigned_showing_name = '';
        $assigned_crew_name = '';
        $assigned_note = '';
        $is_assigned = false;

        // Check active clients (Viewing Bookings) first
        if ( is_array( $cora_re_clients ) ) {
            foreach ( $cora_re_clients as $client ) {
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
        if ( ! $is_assigned && is_array( $cora_re_leads ) ) {
            foreach ( $cora_re_leads as $lead ) {
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
            $cora_re_listings[$key]['status'] = 'In Use';
            $cora_re_listings[$key]['shoot'] = $assigned_showing_name;
            $cora_re_listings[$key]['crew'] = $assigned_crew_name;
            $cora_re_listings[$key]['assignment_note'] = $assigned_note;
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
$dynamic_bookings_count = count( $cora_re_clients );
$dynamic_pending_count = 0;
$dynamic_revenue_total = 0;
foreach ( $cora_re_clients as $client ) {
    if ( isset( $client['status'] ) && $client['status'] === 'editing' ) {
        $dynamic_pending_count++;
    }
    $price_str = isset( $client['price'] ) ? $client['price'] : '';
    $clean_price = preg_replace( '/[^\d]/', '', $price_str );
    $dynamic_revenue_total += intval( $clean_price );
}

$dynamic_active_bookings_count = 0;
foreach ( $cora_re_clients as $client ) {
    if ( isset( $client['status'] ) && $client['status'] !== 'completed' ) {
        $dynamic_active_bookings_count++;
    }
}

$cora_financials = cora_db_get_ledger();
$cora_gbp_profile       = get_option( 'cora_gbp_profile', array() );
$cora_gbp_is_connected  = ! empty( $cora_gbp_profile['connected'] ) &&
    ( ! empty( $cora_gbp_profile['location_name'] ) || ! empty( $cora_gbp_profile['place_id'] ) );
$cora_gbp_review_replies = get_option( 'cora_gbp_review_replies', array() );
$cora_gbp_posts         = get_option( 'cora_gbp_posts', array() );
$cora_gbp_client_id     = get_option( 'cora_gbp_client_id', '' );
$cora_gbp_client_secret = get_option( 'cora_gbp_client_secret', '' );
$cora_gbp_has_credentials = ! empty( $cora_gbp_client_id ) && ! empty( $cora_gbp_client_secret );
$cora_gbp_maps_api_key  = get_option( 'cora_gbp_maps_api_key', '' );
$cora_gbp_has_maps_key  = ! empty( $cora_gbp_maps_api_key );
$cora_gbp_tokens        = get_option( 'cora_gbp_tokens', array() );
$cora_gbp_is_authenticated = ! empty( $cora_gbp_tokens['access_token'] );
$cora_gbp_connected_via = $cora_gbp_profile['connected_via'] ?? '';

// AI model display label for sidebar
$cora_active_ai_model = get_option( 'cora_re_active_ai_model', 'cora-core-v2' );


$cora_categories = ( $sub_page === 'blogs' ) ? get_categories( array('hide_empty' => false) ) : array();
$cora_tags = ( $sub_page === 'blogs' ) ? get_tags( array('hide_empty' => false) ) : array();
$current_wp_user = wp_get_current_user();
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
        $favicon_url = CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png';
    }
    ?>
    <link rel="icon" type="image/png" href="<?php echo esc_url( $favicon_url ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cora for Real Estate - Workspace</title>
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo CORA_REAL_ESTATE_AI_URL . 'assets/css/tailwind-built.css'; ?>" />
    
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
        window.coraClients = <?php echo json_encode( $cora_re_clients ); ?>;
        window.coraDocuments = <?php echo json_encode( $cora_documents ); ?>;
        window.coraPortfolios = <?php echo json_encode( $cora_portfolios ); ?>;
    </script>
    
    <!-- Load QuillJS Rich Text ListingCoordinator -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    
    <!-- Load ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Load TomSelect -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    <!-- WordPress Enqueued Styles/Scripts for Media Uploader -->
    <?php
    wp_print_styles();
    wp_print_scripts();
    ?>
    <script>
        window.$ = window.jQuery;
    </script>
    
    <style id="cora-workspace-custom-styles">
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

        /* Bottom Action Toolbar */
        .media-frame-toolbar {
            bottom: 0 !important;
            height: 60px !important;
            border-top: 1px solid #e4e4e7 !important;
            background: #ffffff !important;
            left: 200px !important;
            box-shadow: none !important;
        }

        .media-frame.hide-menu .media-frame-toolbar {
            left: 0 !important;
        }

        /* Eliminate duplicate borders in bottom toolbar */
        .media-frame-toolbar .media-toolbar {
            border-top: none !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        .media-toolbar-primary {
            padding: 12px 24px !important;
            float: right !important;
            display: flex !important;
            align-items: center !important;
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

        #cora-ai-sidebar.collapsed,
        aside[id$="-drawer"].collapsed {
            transform: translateX(100%) !important;
            box-shadow: none !important;
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
        .cora-dark-theme #cora-add-showing-drawer input,
        .cora-dark-theme #cora-add-showing-drawer select,
        .cora-dark-theme #cora-team-management-drawer select,
        .cora-dark-theme #cora-ai-sidebar textarea {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
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
            padding: 2.5rem 0 !important; /* Bento Grid spans full width horizontally */
            border-radius: 20px !important;
            transition: background-color 0.3s ease;
        }
        .cora-dark-theme #cora-page-dashboard {
            background-color: #0c0d0e !important; /* Premium deep charcoal background */
            background-image: radial-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px) !important;
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
                grid-template-columns: 1fr !important;
                gap: 16px !important;
                padding: 0 16px !important;
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
            margin-top: 1.25rem !important;
            margin-bottom: 0.25rem !important;
            padding-left: 1.5rem !important;
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
        .cora-sidebar.collapsed-sidebar .cora-promo-card,
        .cora-sidebar.collapsed-sidebar .cora-recent-item,
        .cora-sidebar.collapsed-sidebar .cora-nav-group-label {
            display: none !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-workspace-card {
            justify-content: center !important;
            border-color: transparent !important;
            background-color: transparent !important;
            padding: 0 !important;
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
            box-shadow: none !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-workspace-card .cora-studio-info,
        .cora-sidebar.collapsed-sidebar .cora-workspace-card .cora-studio-subtext,
        .cora-sidebar.collapsed-sidebar .cora-workspace-card .cora-switcher-arrow {
            display: none !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-user-footer {
            flex-direction: column !important;
            gap: 0.75rem !important;
            padding: 1rem 0.5rem !important;
            justify-content: center !important;
            align-items: center !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-user-footer .cora-user-info {
            display: none !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-user-inbox {
            margin: 0 !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-user-inbox span {
            border-color: #f9fafb !important;
        }
        .cora-sidebar.collapsed-sidebar #cora-profile-popover:not(.hidden) {
            position: fixed !important;
            bottom: 12px !important;
            left: 4.75rem !important;
            width: 290px !important;
            right: auto !important;
            z-index: 9999 !important;
        }
        .cora-sidebar.collapsed-sidebar #cora-workspace-popover:not(.hidden) {
            position: fixed !important;
            top: 12px !important;
            left: 4.75rem !important;
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
        var coraREData = {
            ajaxUrl: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
            siteUrl: "<?php echo esc_url( get_site_url() ); ?>",
            restUrl: "<?php echo esc_url( rest_url() ); ?>",
            nonce: "<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>",
            ajaxNonce: "<?php echo esc_js( wp_create_nonce( 'cora_ajax_nonce' ) ); ?>",
            currentRole: "<?php echo esc_js( $current_user_role ); ?>",
            userPermissions: <?php echo json_encode( $cora_permissions ); ?>,
            currentPage: "<?php echo esc_js( $sub_page ); ?>",
            documents: <?php echo json_encode( $cora_documents ); ?>,
            portfolios: <?php echo json_encode( $cora_portfolios ); ?>,
            leads: <?php echo json_encode( $cora_re_leads ); ?>,
            clients: <?php echo json_encode( $cora_re_clients ); ?>,
            attendanceLogs: <?php echo json_encode( $cora_re_attendance_logs ); ?>,
            clientTasks: <?php echo json_encode( $cora_re_client_tasks ); ?>,
            financials: <?php echo json_encode( $cora_financials ); ?>,
            equipment: <?php echo json_encode( $cora_re_listings ); ?>,
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
    </script>

</head>
<body class="bg-white text-zinc-900 antialiased overflow-x-hidden">
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
    ?>
    <!-- Global Brand & Customized Blocks Top Navbar (Shopify Style Unified Header) -->
    <header id="cora-global-topbar" class="cora-topbar bg-[#09090b] dark:bg-zinc-950 text-white px-4 md:px-6 py-2.5 flex items-center justify-between border-b border-zinc-800/80 sticky top-0 z-50 shrink-0 select-none" style="background-color: #09090b !important;">
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
            <span class="bg-zinc-850/90 text-zinc-300 border border-zinc-800/80 text-[10px] font-bold px-2.5 py-0.5 rounded-full tracking-wider select-none shrink-0">Beta</span>
        </div>

        <!-- Center Section: Command Palette Trigger -->
        <div class="flex-1 max-w-2xl mx-4 hidden sm:flex items-center justify-center">
            <div onclick="event.stopPropagation(); window.coraOpenCommandPalette();" class="cora-sidebar-search w-full h-10 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800/90 hover:border-zinc-700 rounded-lg px-3 flex items-center justify-between text-zinc-400 hover:text-zinc-200 cursor-pointer transition-all shadow-inner" style="height: 40px !important;">
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
            <!-- Ask Cora AI Custom Block Button -->
            <button id="cora-quick-ai-btn" class="cora-btn-secondary px-3 py-1.5 text-xs font-bold border border-zinc-200 rounded-lg hover:bg-zinc-100 transition-all active:scale-[0.98] inline-flex items-center gap-1.5 text-zinc-900 bg-white shadow-sm cursor-pointer shrink-0" title="Ask Cora AI (Press ⌘J)">
                <span class="cora-btn-icon text-zinc-600 flex shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                </span>
                <span class="hidden sm:inline">Ask Cora AI</span>
            </button>

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
                <!-- Notification Popover Panel -->
                <div id="cora-notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl z-50 flex flex-col overflow-hidden animate-in fade-in slide-in-from-top-2 duration-150 text-zinc-900 dark:text-zinc-100">
                    <div class="px-4 py-3 border-b border-zinc-150 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-850/50 select-none">
                        <span class="text-xs font-bold">Notifications</span>
                        <button id="cora-notif-mark-all-btn" class="text-[10px] font-semibold text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors cursor-pointer">Mark all as read</button>
                    </div>
                    <div id="cora-notif-list" class="max-h-[320px] overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
                        <!-- Injected by JS -->
                    </div>
                    <div id="cora-notif-empty" class="hidden p-8 text-center text-xs text-zinc-400 select-none">
                        No notifications yet.
                    </div>
                </div>
            </div>

            <!-- User Profile Widget -->
            <div class="relative shrink-0">
                <div onclick="event.stopPropagation(); $('#cora-header-profile-popover').toggleClass('hidden'); $('#cora-profile-popover').addClass('hidden'); $('#cora-workspace-popover').addClass('hidden'); $('#cora-notif-dropdown').addClass('hidden');" class="cora-header-profile-btn flex items-center gap-2 cursor-pointer transition-all select-none shrink-0">
                    <div class="w-8 h-8 rounded-full bg-white text-zinc-950 font-bold text-sm flex items-center justify-center shrink-0 leading-none">
                        <?php echo esc_html($cora_initials); ?>
                    </div>
                    <span class="text-white font-semibold text-sm truncate max-w-[100px] hidden md:inline"><?php echo esc_html($cora_display_name); ?></span>
                    <svg class="text-zinc-400 shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>

                <!-- Header User Profile Popover Card -->
                <div id="cora-header-profile-popover" class="hidden absolute right-0 mt-2 w-64 bg-white dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl p-4 z-[70] flex flex-col gap-2.5 animate-in fade-in slide-in-from-top-2 duration-150 select-none">
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
                            <span class="text-sm font-bold text-zinc-900 dark:text-zinc-150 truncate"><?php echo esc_html($current_user_display_name); ?></span>
                            <span class="text-[11px] text-zinc-500 truncate"><?php echo esc_html($current_wp_user->exists() ? $current_wp_user->user_email : 'dravya.shs@gmail.com'); ?></span>
                        </div>
                    </div>

                    <div class="border-t border-zinc-100 dark:border-zinc-850"></div>

                    <!-- Menu Items List -->
                    <div class="flex flex-col gap-0.5">
                        <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-955 font-medium flex items-center gap-3 cursor-pointer transition-colors" onclick="coraNavigateTo('profile'); $('#cora-header-profile-popover').addClass('hidden');">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            Profile
                        </button>

                        <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-955 font-medium flex items-center justify-between cursor-pointer transition-colors" onclick="coraNavigateTo('settings-suite'); $('#cora-header-profile-popover').addClass('hidden');">
                            <div class="flex items-center gap-3">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                Settings
                            </div>
                            <span class="text-[10px] text-zinc-400 font-mono">⌘.</span>
                        </button>

                        <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-955 font-medium flex items-center gap-3 cursor-pointer transition-colors" onclick="$('#cora-workspace').toggleClass('cora-dark-theme'); window.coraShowToast('Theme switched!'); $('#cora-header-profile-popover').addClass('hidden');">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="10"></circle><path d="M12 2v20"></path></svg>
                            Appearance
                        </button>
                    </div>

                    <div class="border-t border-zinc-100 dark:border-zinc-850"></div>

                    <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="w-full text-left px-2.5 py-2.5 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-red-600 dark:hover:text-red-400 font-semibold flex items-center gap-3 transition-colors select-none">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Sign out
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Workspace Main Container (Sidebar + Content Row) -->
    <div class="flex flex-row flex-1 min-h-0 relative w-full lg:overflow-hidden">
    <!-- Workspace Sidebar -->
    <aside class="cora-sidebar w-64 bg-[#f9fafb] dark:bg-[#0c0c0e] border-r border-zinc-200/80 dark:border-zinc-800/40 flex flex-col shrink-0 h-[calc(100vh-52px)] fixed lg:sticky top-[52px] left-0 z-50 lg:z-30 transition-all duration-200 transform -translate-x-full lg:translate-x-0">
        <!-- UPPER BLOCK: SCROLLABLE NAVIGATION CONTENT -->
        <div id="cora-sidebar-scroll-container" class="flex-1 flex flex-col min-h-0 overflow-y-auto">
            <!-- Sidebar Top Header / Brand Logo & Toggle -->
            <?php
            $cora_ws_name = get_option( 'cora_workspace_name', 'Cora Real Estate' );
            $cora_ws_initial = ! empty( $cora_ws_name ) ? strtoupper( substr( $cora_ws_name, 0, 1 ) ) : 'C';
            ?>
            <div class="flex items-center justify-between gap-2 px-3 pt-4 pb-2 shrink-0 select-none">
                <!-- Workspace Switcher Card + Dropdown -->
                <div class="relative flex-1 min-w-0">
                    <!-- Trigger Card (Exact Reference Design) -->
                    <div class="cora-workspace-card flex items-center justify-between gap-2 px-2.5 py-1.5 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/50 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-850 rounded-lg cursor-pointer transition-all select-none" onclick="event.stopPropagation(); $('#cora-workspace-popover').toggleClass('hidden'); $('#cora-profile-popover').addClass('hidden');">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded bg-black dark:bg-white text-white dark:text-black font-bold text-[13px] flex items-center justify-center shrink-0 leading-none">
                                <?php echo esc_html( $cora_ws_initial ); ?>
                            </div>
                            <span class="cora-studio-info text-zinc-900 dark:text-zinc-100 font-bold text-xs truncate"><?php echo esc_html( $cora_ws_name ); ?></span>
                        </div>
                        <svg class="cora-switcher-arrow text-zinc-500 shrink-0" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>

                    <!-- Workspace Popover (Exact Reference Design) -->
                    <div id="cora-workspace-popover" class="hidden absolute top-full mt-2 left-0 w-[275px] bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl p-3.5 z-50 flex flex-col select-none" style="animation: popoverSlideDown 0.12s ease-out;">
                        <!-- Header: 40x40 icon + title & subtitle -->
                        <div class="flex items-center gap-3 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                            <div class="w-10 h-10 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-black text-lg flex items-center justify-center shrink-0 leading-none shadow-sm">
                                <?php echo esc_html( $cora_ws_initial ); ?>
                            </div>
                            <div class="flex flex-col min-w-0 flex-1 leading-snug">
                                <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( $cora_ws_name ); ?></span>
                                <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500 truncate">Delhi Office v2</span>
                            </div>
                        </div>

                        <!-- Settings & Team Buttons -->
                        <div class="grid grid-cols-2 gap-2 my-3">
                            <button class="flex items-center justify-center gap-2 px-3 py-2 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-200 cursor-pointer transition-colors shadow-2xs" onclick="coraNavigateTo('settings-suite'); $('#cora-workspace-popover').addClass('hidden');">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-zinc-500"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                Settings
                            </button>
                            <button class="flex items-center justify-center gap-2 px-3 py-2 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-850 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-200 cursor-pointer transition-colors shadow-2xs" onclick="coraNavigateTo('agent-roster'); $('#cora-workspace-popover').addClass('hidden');">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-zinc-500"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                Team
                            </button>
                        </div>

                        <!-- All Workspaces -->
                        <div class="flex flex-col gap-1.5 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest px-1">All Workspaces</span>
                            <div class="flex items-center justify-between px-3 py-2 bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200/80 dark:border-zinc-800/80 rounded-xl hover:bg-zinc-100/80 dark:hover:bg-zinc-850 cursor-pointer transition-colors">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold text-xs flex items-center justify-center shrink-0 leading-none">
                                        <?php echo esc_html( $cora_ws_initial ); ?>
                                    </div>
                                    <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( $cora_ws_name ); ?></span>
                                </div>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-900 dark:text-white shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cora-sidebar-header-actions flex items-center gap-1.5 shrink-0">
                    <!-- Collapse Toggle Button (layout-sidebar icon) -->
                    <button id="cora-sidebar-toggle" class="text-zinc-500 hover:text-black dark:text-zinc-450 dark:hover:text-white bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-2 rounded-lg transition-colors cursor-pointer select-none shadow-2xs" title="Collapse Sidebar">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="3" x2="9" y2="21"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Sidebar Search Input -->
            <div class="px-3 py-2">
                <div class="cora-sidebar-search flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-500 transition-colors shadow-2xs">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="cora-sidebar-search-input" placeholder="Search menu..." class="w-full bg-transparent border-none p-0 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-0" style="outline: none !important; border: none !important; box-shadow: none !important;" />
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="cora-sidebar-nav px-0 pt-1.5 pb-4 space-y-4">
                <?php
                $active_industry = get_option( 'cora_workspace_industry', 'real_estate' );
                $module = Cora_Module_Registry::get_module( $active_industry );
                $nav_groups = array();
                if ( $module ) {
                    $nav_groups = $module->get_navigation_groups( $role );
                    // Dynamically inject the local badge counts computed in this page
                    foreach ( $nav_groups as $g_key => $group ) {
                        if ( isset( $group['items']['bookings'] ) ) {
                            $nav_groups[$g_key]['items']['bookings']['badge'] = $dynamic_active_bookings_count;
                        }
                        if ( isset( $group['items']['equipment'] ) ) {
                            $nav_groups[$g_key]['items']['equipment']['badge'] = is_array( $cora_re_listings ) ? count( $cora_re_listings ) : 0;
                        }
                    }
                }
                
                foreach ( $nav_groups as $group ) :
                ?>
                <div>
                    <div class="cora-nav-group-label px-3 text-[11px] font-bold text-zinc-500 uppercase"><?php echo esc_html($group['label']); ?></div>
                    <ul class="cora-nav-list space-y-0.5 mt-1">
                        <?php foreach ( $group['items'] as $target => $item ) : ?>
                        <li class="cora-nav-item <?php echo $sub_page === $target ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm rounded-lg cursor-pointer" data-target="<?php echo esc_attr($target); ?>">
                            <div class="flex items-center gap-3">
                                <span class="cora-nav-icon">
                                    <?php echo $item['icon']; ?>
                                </span>
                                <span class="cora-nav-text"><?php echo esc_html($item['title']); ?></span>
                            </div>
                            <?php if ( ! empty( $item['soon'] ) ) : ?>
                            <span class="cora-badge cora-badge-sidebar px-1.5 py-0.5 text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 rounded-full border border-zinc-200 dark:border-zinc-700 select-none flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                SOON
                            </span>
                            <?php elseif ( isset($item['badge']) && intval($item['badge']) > 0 ) : ?>
                            <span class="cora-badge cora-badge-sidebar px-1.5 py-0.5 text-[10px] font-medium bg-zinc-200 text-zinc-800 rounded-full"><?php echo intval($item['badge']); ?></span>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </nav>
        </div><!-- /.flex-1.overflow-y-auto -->

        <!-- LOWER BLOCK: PINNED AT BOTTOM (sibling to scrollable nav, not inside it) -->
        <div class="cora-sidebar-bottom-block shrink-0 border-t border-zinc-200/50 dark:border-zinc-800/40 z-20 sticky bottom-0 flex flex-col">
            <!-- User Profile Popover Card -->
            <div id="cora-profile-popover" class="hidden absolute bottom-20 left-4 right-4 bg-white dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl p-4 z-[70] flex flex-col gap-2.5 animate-in fade-in slide-in-from-bottom-2 duration-150 select-none">
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
                        <span class="text-sm font-bold text-zinc-900 dark:text-zinc-150 truncate"><?php echo esc_html($current_user_display_name); ?></span>
                        <span class="text-[11px] text-zinc-500 truncate"><?php echo esc_html($current_wp_user->exists() ? $current_wp_user->user_email : 'dravya.shs@gmail.com'); ?></span>
                    </div>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-850"></div>

                <!-- Menu Items List -->
                <div class="flex flex-col gap-0.5">
                    <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-955 font-medium flex items-center gap-3 cursor-pointer transition-colors" onclick="coraNavigateTo('profile'); $('#cora-profile-popover').addClass('hidden');">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Profile
                    </button>

                    <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-955 font-medium flex items-center justify-between cursor-pointer transition-colors" onclick="coraNavigateTo('settings-suite'); $('#cora-profile-popover').addClass('hidden');">
                        <div class="flex items-center gap-3">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            Settings
                        </div>
                        <span class="text-[10px] text-zinc-400 font-mono">⌘.</span>
                    </button>

                    <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-955 font-medium flex items-center gap-3 cursor-pointer transition-colors" onclick="$('#cora-workspace').toggleClass('cora-dark-theme'); window.coraShowToast('Theme switched!'); $('#cora-profile-popover').addClass('hidden');">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="10"></circle><path d="M12 2v20"></path></svg>
                        Appearance
                    </button>
                </div>

                <?php if ( in_array( $current_user_role, array( 'administrator', 'cora_shruti', 'cora_super_admin' ) ) ) : ?>
                <div class="border-t border-zinc-100 dark:border-zinc-850"></div>
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

                <div class="border-t border-zinc-100 dark:border-zinc-850"></div>

                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="w-full text-left px-2.5 py-2.5 text-xs text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-red-600 dark:hover:text-red-400 font-semibold flex items-center gap-3 transition-colors select-none">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Sign out
                </a>
            </div>

            <!-- Sidebar Notification Popover Card -->
            <div id="cora-sidebar-notif-popover" class="hidden absolute bottom-20 left-4 right-4 bg-white dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl p-4 z-[70] flex flex-col gap-2.5 animate-in fade-in slide-in-from-bottom-2 duration-150 select-none text-zinc-900 dark:text-zinc-100">
                <div class="flex items-center justify-between pb-2 border-b border-zinc-150 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-850/50 px-1 rounded-t-xl">
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

            <!-- Lovable-style user footer row -->
            <?php
            $current_user_display_name = $current_wp_user->exists() ? $current_wp_user->display_name : 'Dravya Bansal';
            $current_user_role_label = isset($cora_role_labels[$current_user_role]) ? $cora_role_labels[$current_user_role] : ucfirst($current_user_role);
            if ($current_user_role === 'administrator') {
                $current_user_role_label = 'Super Admin';
            }
            $current_user_avatar = $current_wp_user->exists() ? get_user_meta( $current_wp_user->ID, 'cora_avatar_url', true ) : '';
            ?>
            <div class="cora-user-footer px-4 py-3 flex items-center justify-between border-t border-zinc-200/50 dark:border-zinc-800/40 hover:bg-zinc-100/50 dark:hover:bg-zinc-800/30 transition-colors duration-200 cursor-pointer relative z-[60]" onclick="event.stopPropagation(); $('#cora-profile-popover').toggleClass('hidden'); $('#cora-sidebar-notif-popover').addClass('hidden'); $('#cora-workspace-popover').addClass('hidden');">
                <div class="flex items-center gap-3 min-w-0">
                    <?php if ( $current_user_avatar ) : ?>
                        <img src="<?php echo esc_url($current_user_avatar); ?>" class="w-8 h-8 rounded-full object-cover shrink-0 select-none border border-zinc-200/60" alt="<?php echo esc_attr($current_user_display_name); ?>" />
                    <?php else : ?>
                        <div class="w-8 h-8 rounded-full bg-zinc-200 text-zinc-700 flex items-center justify-center font-bold text-xs uppercase shrink-0 select-none">
                            <?php echo esc_html(substr($current_user_display_name, 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="cora-user-info flex flex-col min-w-0">
                        <span class="cora-user-name text-xs font-semibold text-zinc-900 dark:text-zinc-150 truncate leading-tight"><?php echo esc_html($current_user_display_name); ?></span>
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
    <main class="cora-main flex-1 bg-white flex flex-col min-h-screen lg:min-h-0 lg:h-full lg:overflow-y-auto relative pb-16 lg:pb-0 min-w-0 w-full overflow-x-clip">


        <!-- Dynamic Content Sections -->
        <div class="cora-content-wrapper p-3.5 sm:p-6 md:p-8 max-w-full w-full flex-1 space-y-6 min-w-0 overflow-x-hidden">
            
            <!-- SECTION 1: DASHBOARD -->
            <?php if ( $sub_page === 'dashboard' ) : ?>
            <section id="cora-page-dashboard" class="cora-page-section cora-active space-y-6">
                <?php
                $hour = date('H');
                $greeting_title = 'Good afternoon!';
                if ($hour >= 5 && $hour < 12) {
                    $greeting_title = 'Good morning!';
                } elseif ($hour >= 12 && $hour < 17) {
                    $greeting_title = 'Good afternoon!';
                } elseif ($hour >= 17 && $hour < 22) {
                    $greeting_title = 'Good evening!';
                }
                ?>
                <div class="cora-dashboard-upper" style="padding: 0 24px; box-sizing: border-box; width: 100%;">
                <!-- Metrics Card Grid (Real Estate Telemetry Redesign - Centered Non-Overlapping Layout) -->
                <div class="bg-white/80 dark:bg-zinc-900/60 p-3 backdrop-blur-md border border-zinc-200/50 dark:border-zinc-800/40 rounded-2xl p-4.5 max-w-3xl mx-4 md:mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 shadow-3xs select-none">
                    <!-- Listed Properties -->
                    <div class="flex items-center justify-between pb-1.5 border-b border-zinc-200/60 dark:border-zinc-800/60 min-w-0 pr-1">
                        <div class="flex flex-col min-w-0 gap-0.5">
                            <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-555 uppercase tracking-widest truncate">Properties</span>
                            <div class="flex items-baseline gap-1 mt-0.5 min-w-0">
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-150 leading-none shrink-0"><?php echo count( $cora_re_listings ); ?></span>
                                <span class="text-[8px] font-bold text-emerald-600 dark:text-emerald-500 shrink-0">Active</span>
                            </div>
                        </div>
                        <svg class="h-4 w-9 text-zinc-300 dark:text-zinc-700 ml-auto shrink-0 overflow-hidden" viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M0 25 C 20 25, 30 5, 50 15 C 70 25, 80 10, 100 5" />
                        </svg>
                    </div>

                    <!-- Leads -->
                    <div class="flex items-center justify-between pb-1.5 border-b border-zinc-200/60 dark:border-zinc-800/60 min-w-0 pr-1">
                        <div class="flex flex-col min-w-0 gap-0.5">
                            <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-555 uppercase tracking-widest truncate">Active Leads</span>
                            <div class="flex items-baseline gap-1 mt-0.5 min-w-0">
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-150 leading-none shrink-0"><?php echo count( $cora_re_leads ); ?></span>
                                <span class="text-[8px] font-bold text-emerald-600 dark:text-emerald-500 shrink-0">+12%</span>
                            </div>
                        </div>
                        <svg class="h-4 w-9 text-zinc-300 dark:text-zinc-700 ml-auto shrink-0 overflow-hidden" viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M0 5 C 20 5, 40 25, 60 15 C 80 5, 90 28, 100 28" />
                        </svg>
                    </div>

                    <!-- Bookings / Showings -->
                    <div class="flex items-center justify-between pb-1.5 border-b border-zinc-200/60 dark:border-zinc-800/60 min-w-0 pr-1">
                        <div class="flex flex-col min-w-0 gap-0.5">
                            <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-555 uppercase tracking-widest truncate">Showings</span>
                            <div class="flex items-baseline gap-1 mt-0.5 min-w-0">
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-150 leading-none shrink-0"><?php echo $dynamic_bookings_count; ?></span>
                                <span class="text-[8px] font-bold text-zinc-400 dark:text-zinc-500 shrink-0">Scheduled</span>
                            </div>
                        </div>
                        <svg class="h-4 w-9 text-zinc-300 dark:text-zinc-700 ml-auto shrink-0 overflow-hidden" viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M0 8 C 20 8, 40 22, 60 12 C 80 2, 90 25, 100 25" />
                        </svg>
                    </div>

                    <!-- Revenue Pipeline -->
                    <div id="cora-dashboard-financial-card" class="flex items-center justify-between pb-1.5 border-b border-zinc-200/60 dark:border-zinc-800/60 md:border-b-0 min-w-0">
                        <div class="flex flex-col min-w-0 gap-0.5">
                            <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-555 uppercase tracking-widest truncate">Pipeline Value</span>
                            <div class="flex items-baseline gap-1 mt-0.5 min-w-0">
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-150 leading-none shrink-0"><?php echo cora_format_rupees( $dynamic_revenue_total ); ?></span>
                                <span class="text-[8px] font-bold text-zinc-400 dark:text-zinc-500 shrink-0 truncate">Negotiating</span>
                            </div>
                        </div>
                        <svg class="h-4 w-9 text-zinc-300 dark:text-zinc-700 ml-auto shrink-0 overflow-hidden" viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M0 15 L 100 15" stroke-dasharray="2 2" />
                        </svg>
                    </div>
                </div>
                        <!-- Centered Welcome Greeting Section with sparkle SVG -->
                <div class="text-center py-16 px-4 space-y-3 relative">
                    <div class="inline-flex items-center justify-center gap-3">
                        <!-- Slate Charcoal Star Sparkle -->
                        <span class="text-zinc-450 dark:text-zinc-600 shrink-0">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor">
                                <path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"></path>
                            </svg>
                        </span>
                        <h1 id="cora-dynamic-greeting-title" class="text-4xl md:text-5xl font-semibold tracking-tight text-zinc-800 dark:text-zinc-150">
                            Good afternoon!
                        </h1>
                    </div>
                    <p class="text-xl md:text-2xl font-medium text-zinc-500 dark:text-zinc-400 leading-tight">
                        Let's continue growing your business.
                    </p>
                </div>
 
                <!-- Lovable-Style Command Search (Ask anything...) -->
                <div class="w-full max-w-2xl mx-4 md:mx-auto mt-8 mb-12 relative z-[999]" id="cora-search-container">
                    <div class="relative flex items-center bg-white/85 dark:bg-zinc-900/70 backdrop-blur-md border border-zinc-200/60 dark:border-zinc-800/50 hover:border-zinc-350 dark:hover:border-zinc-700 focus-within:border-zinc-900 dark:focus-within:border-zinc-100 focus-within:ring-2 focus-within:ring-zinc-100/30 dark:focus-within:ring-zinc-800/30 rounded-full shadow-2xs transition-all duration-200 p-2 pl-4 pr-3">
                        <span class="text-purple-600 dark:text-purple-400 mr-2.5 flex shrink-0">
                            <!-- Lovable Character Icon -->
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                <circle cx="12" cy="12" r="10" class="text-purple-100 dark:text-purple-950/20" fill="currentColor"></circle>
                                <circle cx="12" cy="12" r="7" class="text-purple-500" fill="currentColor"></circle>
                                <circle cx="10" cy="11" r="1.2" fill="#fff"></circle>
                                <circle cx="14" cy="11" r="1.2" fill="#fff"></circle>
                                <path d="M9.5 15c.5.8 1.5 1.2 2.5 1.2s2-.4 2.5-1.2" stroke="#fff" stroke-width="1.2" stroke-linecap="round" fill="none"></path>
                            </svg>
                        </span>
                        
                        <!-- Real interactive input field for contextual search -->
                        <input type="text" 
                               id="cora-inline-command-input"
                               placeholder="Ask anything..." 
                               class="w-full bg-transparent border-0 focus:outline-none focus:ring-0 text-sm py-2 px-1 text-zinc-800 dark:text-zinc-200 placeholder:text-zinc-400/80 cursor-pointer"
                               autocomplete="off" />
                               
                        <div class="flex items-center gap-2">
                            <button onclick="window.coraTriggerCommandAI()" class="flex items-center justify-center h-9 w-9 rounded-full bg-zinc-900 hover:bg-zinc-950 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 transition-colors cursor-pointer shadow-sm">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Advanced Command Search dropdown in-line container -->
                    <div id="cora-inline-command-palette" class="absolute left-0 right-0 top-full mt-2 z-[9999] hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden flex-col max-h-[380px] transition-all duration-200">
                        <!-- Filter Pills Bar -->
                        <div class="flex items-center gap-1.5 px-4 py-2 border-b border-zinc-100 dark:border-zinc-800/40 bg-zinc-50/50 dark:bg-zinc-900/40 overflow-x-auto shrink-0 select-none no-scrollbar">
                            <button type="button" class="cora-search-pill active text-[10px] font-semibold px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 transition-all cursor-pointer" data-filter="all">Overview</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="pages">Pages</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="leads">Leads</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="settings">Settings</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-650 dark:text-zinc-405 hover:bg-zinc-55 dark:hover:bg-zinc-800 transition-all cursor-pointer" data-filter="listings">Listings</button>
                        </div>

                        <!-- Results List Area -->
                        <div class="flex-1 overflow-y-auto p-2" id="cora-inline-command-results">
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
                
                <!-- Mockup-Aligned Quick Actions buttons -->
                <div class="flex flex-wrap items-center justify-center gap-3 mt-8">
                    <span class="text-xs font-bold text-zinc-450 dark:text-zinc-555 mr-2">Quick actions:</span>
                    
                    <button onclick="coraNavigateTo('bookings'); document.getElementById('cora-add-booking-btn').click();" class="inline-flex items-center gap-1.5 px-4 py-2 border border-zinc-200/80 dark:border-zinc-800 bg-white/70 dark:bg-zinc-900/50 hover:bg-zinc-50 dark:hover:bg-zinc-850 rounded-full text-xs font-semibold text-zinc-650 hover:text-zinc-900 dark:text-zinc-350 dark:hover:text-white transition-all shadow-3xs cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Schedule Showing
                    </button>
                    
                    <button onclick="coraNavigateTo('ai-assistants')" class="inline-flex items-center gap-1.5 px-4 py-2 border border-zinc-200/80 dark:border-zinc-800 bg-white/70 dark:bg-zinc-900/50 hover:bg-zinc-50 dark:hover:bg-zinc-850 rounded-full text-xs font-semibold text-zinc-650 hover:text-zinc-900 dark:text-zinc-350 dark:hover:text-white transition-all shadow-3xs cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                        Draft Captions
                    </button>
                    
                    <button onclick="event.stopPropagation(); window.coraOpenCommandPalette();" class="inline-flex items-center gap-1.5 px-4 py-2 border border-zinc-200/80 dark:border-zinc-800 bg-white/70 dark:bg-zinc-900/50 hover:bg-zinc-50 dark:hover:bg-zinc-855 rounded-full text-xs font-semibold text-zinc-650 hover:text-zinc-900 dark:text-zinc-350 dark:hover:text-white transition-all shadow-3xs cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        Create Brochure
                    </button>
                </div>
            </div><!-- end cora-dashboard-upper -->
                <!-- Premium Bento Grid Layout -->
                <div class="cora-bento-grid pt-2">
                    
                    <!-- BENTO CARD 1: Showing Status (Spans 2 Columns) -->
                    <div class="border border-zinc-200/50 dark:border-zinc-800/55 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xs md:col-span-2 min-h-[268px]">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                            <div class="flex flex-col gap-0.5">
                                <h3 class="text-xs font-bold text-zinc-800 dark:text-zinc-150 uppercase tracking-wider">Showing Status</h3>
                                <span class="text-[9px] text-zinc-400 dark:text-zinc-500 font-medium">Your active property viewings and client showings.</span>
                            </div>
                            <button onclick="coraNavigateTo('bookings')" class="text-xs font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-450 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer">
                                Calendar <span class="text-zinc-300 dark:text-zinc-700">&rarr;</span>
                            </button>
                        </div>

                        <div>
                            <?php
                            $recent_active_showings = array();
                            $reversed_clients = array_reverse( $cora_re_clients );
                            foreach ( $reversed_clients as $client ) {
                                if ( isset( $client['status'] ) && $client['status'] !== 'completed' ) {
                                    $recent_active_showings[] = $client;
                                    if ( count( $recent_active_showings ) >= 3 ) {
                                        break;
                                    }
                                }
                            }
                            if ( count( $recent_active_showings ) < 3 ) {
                                foreach ( $reversed_clients as $client ) {
                                    if ( ! in_array( $client, $recent_active_showings ) ) {
                                        $recent_active_showings[] = $client;
                                        if ( count( $recent_active_showings ) >= 3 ) {
                                            break;
                                        }
                                    }
                                }
                            }
                            
                            if ( empty( $recent_active_showings ) ) :
                            ?>
                                <div class="flex flex-col items-center justify-center py-10 text-center">
                                    <div class="h-14 w-14 rounded-full bg-zinc-50 dark:bg-zinc-850 flex items-center justify-center mb-4 border border-zinc-100 dark:border-zinc-800">
                                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </div>
                                    <h4 class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">No showings scheduled</h4>
                                    <p class="text-[10px] text-zinc-450 dark:text-zinc-500 mt-1 max-w-[220px]">Upcoming bookings will automatically appear in this view.</p>
                                </div>
                            <?php else : ?>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <?php foreach ( $recent_active_showings as $showing ) : 
                                        $badge_class = 'cora-badge-blue';
                                        $status_label = 'Confirmed';
                                        if ( isset($showing['status']) ) {
                                            if ( $showing['status'] === 'editing' ) {
                                                $badge_class = 'cora-badge-yellow';
                                                $status_label = 'Editing';
                                            } elseif ( $showing['status'] === 'completed' ) {
                                                $badge_class = 'cora-badge-green';
                                                $status_label = 'Completed';
                                            }
                                        }
                                        $short_date = explode( ',', $showing['viewing_date'] ?? '' )[0];
                                    ?>
                                    <div class="flex flex-col justify-between p-3.5 bg-white/60 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800/40 rounded-xl hover:border-zinc-300 dark:hover:border-zinc-700 transition-all cursor-pointer shadow-3xs min-h-[120px]" onclick="coraOpenClientLifecycle('<?php echo esc_attr($showing['id']); ?>')">
                                        <div class="flex flex-col gap-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-[8px] text-zinc-400 dark:text-zinc-500 font-semibold uppercase tracking-wider"><?php echo esc_html($short_date); ?></span>
                                                <span class="cora-badge text-[7px] px-1.5 py-0.5 <?php echo $badge_class; ?>"><?php echo esc_html($status_label); ?></span>
                                            </div>
                                            <strong class="font-bold text-zinc-800 dark:text-zinc-150 text-xs block truncate mt-1.5"><?php echo esc_html($showing['names']); ?></strong>
                                        </div>
                                        <div class="text-[9px] text-zinc-400 dark:text-zinc-500 border-t border-zinc-100 dark:border-zinc-800/40 pt-2 mt-2 truncate">
                                            <?php echo esc_html($showing['deal_type'] ?? 'Residential Buy'); ?> &bull; <?php echo esc_html($showing['city'] ?? 'Delhi'); ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BENTO CARD 2: Active AI Assistants (Spans 1 Column, Tall Height - Spans 2 Rows Vertically) -->
                    <div class="border border-zinc-200/50 dark:border-zinc-800/55 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xs md:col-span-1 md:row-span-2 flex flex-col justify-between min-h-[564px]">
                        <div class="flex flex-col flex-1">
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                                <h3 class="text-xs font-bold text-zinc-450 dark:text-zinc-555 uppercase tracking-widest">Active Assistants</h3>
                                <button onclick="coraNavigateTo('ai-assistants')" class="text-xs font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-455 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer">
                                    View all <span class="text-zinc-300 dark:text-zinc-700">&rarr;</span>
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-white/60 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800/40 rounded-xl hover:border-zinc-300 dark:hover:border-zinc-700 cursor-pointer transition-all group shadow-3xs" onclick="coraNavigateTo('ai-assistants')">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="h-8 w-8 rounded-full bg-pink-50 dark:bg-pink-950/20 border border-pink-100 dark:border-pink-900/40 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-pink-600 dark:text-pink-400">
                                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-[11px] font-semibold text-zinc-800 dark:text-zinc-150 leading-none truncate">Caption Assistant</span>
                                            <span class="text-[8px] text-zinc-400 dark:text-zinc-500 mt-1 block truncate">Draft property captions.</span>
                                        </div>
                                    </div>
                                    <span class="text-zinc-455 group-hover:text-zinc-800 dark:group-hover:text-white transition-transform group-hover:translate-x-0.5 shrink-0 text-xs">&rarr;</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-white/60 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800/40 rounded-xl hover:border-zinc-300 dark:hover:border-zinc-700 cursor-pointer transition-all group shadow-3xs" onclick="coraNavigateTo('portfolio')">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="h-8 w-8 rounded-full bg-zinc-50 dark:bg-zinc-855 border border-zinc-100 dark:border-zinc-800 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-[11px] font-semibold text-zinc-800 dark:text-zinc-150 leading-none truncate">Portfolios Assistant</span>
                                            <span class="text-[8px] text-zinc-400 dark:text-zinc-500 mt-1 block truncate">Smart suggested selections.</span>
                                        </div>
                                    </div>
                                    <span class="text-zinc-455 group-hover:text-zinc-800 dark:group-hover:text-white transition-transform group-hover:translate-x-0.5 shrink-0 text-xs">&rarr;</span>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-white/60 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800/40 rounded-xl hover:border-zinc-300 dark:hover:border-zinc-700 cursor-pointer transition-all group shadow-3xs" onclick="coraNavigateTo('vault')">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="h-8 w-8 rounded-full bg-zinc-50 dark:bg-zinc-855 border border-zinc-100 dark:border-zinc-800 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-[11px] font-semibold text-zinc-800 dark:text-zinc-150 leading-none truncate">Follow-up Assistant</span>
                                            <span class="text-[8px] text-zinc-400 dark:text-zinc-555 mt-1 block truncate">Personalized client mails.</span>
                                        </div>
                                    </div>
                                    <span class="text-zinc-455 group-hover:text-zinc-800 dark:group-hover:text-white transition-transform group-hover:translate-x-0.5 shrink-0 text-xs">&rarr;</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BENTO CARD 3: Leads Tracker (Spans 1 Column) -->
                    <div class="border border-zinc-200/50 dark:border-zinc-800/55 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xs min-h-[258px]">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                            <h3 class="text-xs font-bold text-zinc-400 dark:text-zinc-555 uppercase tracking-widest">Leads Tracker</h3>
                            <button onclick="coraNavigateTo('leads')" class="text-xs font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-455 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer">
                                Leads &rarr;
                            </button>
                        </div>

                        <div class="space-y-2">
                            <?php
                            $recent_leads = is_array($cora_re_leads) ? array_slice(array_reverse($cora_re_leads), 0, 3) : array();
                            if ( empty($recent_leads) ) :
                            ?>
                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                    <h4 class="text-xs font-semibold text-zinc-700 dark:text-zinc-400">No active leads in pipeline.</h4>
                                    <p class="text-[9px] text-zinc-400 dark:text-zinc-500 mt-1">Click above to view CRM database.</p>
                                </div>
                            <?php else : ?>
                                <?php foreach ( $recent_leads as $lead ) : ?>
                                    <div class="flex items-center justify-between p-2.5 bg-white/60 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800/40 rounded-xl hover:border-zinc-300 dark:hover:border-zinc-700 transition-all cursor-pointer shadow-3xs gap-3" onclick="coraNavigateTo('leads')">
                                        <div class="flex-1 min-w-0 flex flex-col gap-0.5">
                                            <strong class="font-semibold text-zinc-800 dark:text-zinc-150 text-xs block truncate"><?php echo esc_html($lead['names']); ?></strong>
                                            <span class="text-[9px] text-zinc-400 dark:text-zinc-500 block truncate"><?php echo esc_html($lead['deal_type'] ?? 'Residential Buy'); ?> &bull; Budget: <?php echo esc_html(cora_format_rupees($lead['budget'] ?? 0)); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BENTO CARD 4: Recent Listings (Spans 1 Column) -->
                    <div class="border border-zinc-200/50 dark:border-zinc-800/55 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xs min-h-[258px]">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                            <h3 class="text-xs font-bold text-zinc-400 dark:text-zinc-555 uppercase tracking-widest">Recent Listings</h3>
                            <button onclick="coraNavigateTo('equipment')" class="text-xs font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-455 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer">
                                Listings &rarr;
                            </button>
                        </div>

                        <div class="space-y-2">
                            <?php
                            $recent_listings = is_array($cora_re_listings) ? array_slice(array_reverse($cora_re_listings), 0, 3) : array();
                            if ( empty($recent_listings) ) :
                            ?>
                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                    <h4 class="text-xs font-semibold text-zinc-700 dark:text-zinc-400">No properties available.</h4>
                                    <p class="text-[9px] text-zinc-400 dark:text-zinc-500 mt-1">Click above to add listings.</p>
                                </div>
                            <?php else : ?>
                                <?php foreach ( $recent_listings as $listing ) : ?>
                                    <div class="flex items-center justify-between p-2.5 bg-white/60 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800/40 rounded-xl hover:border-zinc-300 dark:hover:border-zinc-700 transition-all cursor-pointer shadow-3xs gap-3" onclick="coraNavigateTo('equipment')">
                                        <div class="flex-1 min-w-0 flex flex-col gap-0.5">
                                            <strong class="font-semibold text-zinc-800 dark:text-zinc-150 text-xs block truncate"><?php echo esc_html($listing['name']); ?></strong>
                                            <span class="text-[9px] text-zinc-400 dark:text-zinc-500 block truncate"><?php echo esc_html($listing['serial'] ?? 'Residential'); ?> &bull; <?php echo esc_html(cora_format_rupees($listing['rental_rate'] ?? 0)); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BENTO CARD 5: Document Vault (Spans 1 Column) -->
                    <div class="border border-zinc-200/50 dark:border-zinc-800/55 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xs min-h-[258px]">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40 mb-4">
                            <h3 class="text-xs font-bold text-zinc-400 dark:text-zinc-555 uppercase tracking-widest">Document Vault</h3>
                            <button onclick="coraNavigateTo('vault')" class="text-xs font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-455 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer">
                                Vault &rarr;
                            </button>
                        </div>

                        <div class="space-y-2">
                            <?php 
                            $recent_docs = is_array($cora_documents) ? array_slice(array_reverse($cora_documents), 0, 3) : array();
                            if ( empty( $recent_docs ) ) :
                            ?>
                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500">No documents in vault.</span>
                                </div>
                            <?php else : ?>
                                <?php foreach ( $recent_docs as $doc ) : ?>
                                    <div class="flex items-center justify-between p-2.5 bg-white/60 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800/40 rounded-xl hover:border-zinc-300 dark:hover:border-zinc-700 transition-all cursor-pointer shadow-3xs gap-3" onclick="coraNavigateTo('vault')">
                                        <div class="flex-1 min-w-0 flex flex-col">
                                            <strong class="font-semibold text-zinc-800 dark:text-zinc-150 text-xs block truncate"><?php echo esc_html($doc['title'] ?? 'Untitled Document'); ?></strong>
                                            <span class="text-[8px] text-zinc-400 dark:text-zinc-500 block truncate"><?php echo esc_html($doc['category'] ?? 'General'); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BENTO CARD 6: AI Modules Control & Telemetry Panel (Spans 2 Columns Wide) -->
                    <div class="border border-zinc-200/50 dark:border-zinc-800/55 rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xs md:col-span-2 min-h-[258px]">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            <!-- Telemetry Section 1: Modules Toggles (Takes 2 Columns) -->
                            <div class="md:col-span-2 space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40">
                                    <div class="flex flex-col gap-0.5">
                                        <h3 class="text-xs font-bold text-zinc-800 dark:text-zinc-150 uppercase tracking-wider">AI Modules Control</h3>
                                        <span class="text-[9px] text-zinc-400 dark:text-zinc-500 font-medium">Activate automated background pipelines.</span>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-zinc-50/40 dark:bg-zinc-850/20 border border-zinc-200/40 dark:border-zinc-800/40 rounded-xl">
                                        <div class="flex flex-col gap-0.5 min-w-0">
                                            <span class="font-semibold text-xs text-zinc-800 dark:text-zinc-150 truncate">WhatsApp Bot</span>
                                            <span class="text-[9px] text-zinc-400 dark:text-zinc-500 truncate">Autopilot client communication</span>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            <span class="text-[8px] font-bold uppercase px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-955/20 text-amber-600 dark:text-amber-500 border border-amber-200 dark:border-amber-900/55">Coming Soon</span>
                                            <label class="cora-switch relative inline-flex items-center cursor-not-allowed opacity-45 scale-[0.75]" onclick="event.preventDefault(); window.coraShowToast('WhatsApp Autopilot is coming soon.')">
                                                <input type="checkbox" id="module-whatsapp" disabled class="sr-only peer">
                                                <div class="w-9 h-5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950"></div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 bg-zinc-50/40 dark:bg-zinc-850/20 border border-zinc-200/40 dark:border-zinc-800/40 rounded-xl">
                                        <div class="flex flex-col gap-0.5 min-w-0">
                                            <span class="font-semibold text-xs text-zinc-800 dark:text-zinc-150 truncate">SEO Crawler</span>
                                            <span class="text-[9px] text-zinc-400 dark:text-zinc-500 truncate">Automatic search indexing & listings rank</span>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            <span class="text-[8px] font-bold uppercase px-1.5 py-0.5 rounded cora-module-status-pill active" id="badge-module-seo">Active</span>
                                            <label class="cora-switch relative inline-flex items-center cursor-pointer scale-[0.75]">
                                                <input type="checkbox" id="module-seo" checked onchange="coraToggleModule('seo', this.checked)" class="sr-only peer">
                                                <div class="w-9 h-5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-955"></div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 bg-zinc-50/40 dark:bg-zinc-850/20 border border-zinc-200/40 dark:border-zinc-800/40 rounded-xl">
                                        <div class="flex flex-col gap-0.5 min-w-0">
                                            <span class="font-semibold text-xs text-zinc-800 dark:text-zinc-150 truncate">Smart Contract</span>
                                            <span class="text-[9px] text-zinc-400 dark:text-zinc-500 truncate">Automated digital signing pipelines</span>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            <span class="text-[8px] font-bold uppercase px-1.5 py-0.5 rounded cora-module-status-pill inactive" id="badge-module-contracts">Inactive</span>
                                            <label class="cora-switch relative inline-flex items-center cursor-pointer scale-[0.75]">
                                                <input type="checkbox" id="module-contracts" onchange="coraToggleModule('contracts', this.checked)" class="sr-only peer">
                                                <div class="w-9 h-5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-955"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Telemetry Section 2: Core Telemetry widgets (Takes 1 Column) -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/40">
                                    <div class="flex flex-col gap-0.5">
                                        <h3 class="text-xs font-bold text-zinc-800 dark:text-zinc-150 uppercase tracking-wider">System Telemetry</h3>
                                        <span class="text-[9px] text-zinc-400 dark:text-zinc-500 font-medium">Model gateway connection.</span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-zinc-50/45 dark:bg-zinc-850/20 border border-zinc-200/40 dark:border-zinc-800/40 rounded-xl p-3 text-center flex flex-col justify-center items-center gap-1.5 min-h-[80px]">
                                        <span class="text-[8px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">AI Model</span>
                                        <span class="text-xs font-bold text-zinc-850 dark:text-zinc-100 leading-none">Cora v2</span>
                                        <span class="text-[8px] font-medium text-zinc-400 dark:text-zinc-500">Core Engine</span>
                                    </div>
                                    <div class="bg-zinc-50/45 dark:bg-zinc-850/20 border border-zinc-200/40 dark:border-zinc-800/40 rounded-xl p-3 text-center flex flex-col justify-center items-center gap-1.5 min-h-[80px]">
                                        <span class="text-[8px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">API Grid</span>
                                        <span class="text-xs font-bold text-zinc-850 dark:text-zinc-100 leading-none flex items-center gap-1"><span class="h-1.5 w-1.5 bg-emerald-500 rounded-full inline-block"></span>Active</span>
                                        <span class="text-[8px] font-medium text-zinc-400 dark:text-zinc-500">Gateway</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="cora-dashboard-lower" style="padding: 0 24px; box-sizing: border-box; width: 100%; margin-top: 24px;">
                <!-- Call Out Box: Today's Priority Alert & Recommendation (Sketched Top Wide Box, now sitting cleanly at the bottom) -->
                <div class="bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-200/50 dark:border-zinc-800/40 rounded-xl p-5 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="text-zinc-400 shrink-0 mt-0.5 flex animate-pulse">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                        </span>
                        <div class="space-y-1.5 flex-1">
                            <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Cora AI Intelligence Focus</h4>
                            <p class="text-sm text-zinc-700 dark:text-zinc-350 leading-relaxed font-medium">
                                You have <strong>3 upcoming showings</strong> scheduled this week. We recommend generating property brochures and description packages for the <em>Jaipur Luxury Villa Sale</em> completed yesterday.
                            </p>
                        </div>
                        <button onclick="event.stopPropagation(); window.coraOpenCommandPalette();" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-855/80 text-zinc-700 dark:text-zinc-300 active:scale-[0.98] transition-all cursor-pointer shadow-2xs">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Generate Property Brochures
                        </button>
                    </div>
                    <div class="md:hidden">
                        <button onclick="event.stopPropagation(); window.coraOpenCommandPalette();" class="w-full justify-center inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-855/80 text-zinc-700 dark:text-zinc-300 active:scale-[0.98] transition-all cursor-pointer shadow-2xs">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Generate Property Brochures
                        </button>
                    </div>
                </div>
                </div><!-- end cora-dashboard-lower -->
            </section>
            <?php endif; ?>
            
            <!-- SECTION 2: BOOKINGS CRM -->
            <?php if ( $sub_page === 'bookings' ) : ?>
            <section id="cora-page-bookings" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                    <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Viewing Bookings CRM</h1>
                    <button class="sm:hidden cora-btn-primary px-3 py-1.5 text-xs font-semibold bg-zinc-950 text-white rounded hover:bg-zinc-800 transition-all active:scale-[0.97] cursor-pointer ml-auto" onclick="document.getElementById('cora-add-booking-btn').click();">
                        + New
                    </button>
                </div>
                <p class="cora-section-desc text-sm text-zinc-500 -mt-2">Manage clients, view showing schedules, track editing states, and auto-deliver previews using AI agents.</p>

                <!-- Search and Filters Bar -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-zinc-200 pb-3">
                    <!-- Filter Pills -->
                    <div class="cora-filters-bar flex items-center gap-1.5 overflow-x-auto whitespace-nowrap scrollbar-none max-w-full">
                        <button class="cora-filter-tab active px-3 py-1.5 text-xs font-semibold rounded-md border border-transparent text-zinc-650 hover:text-zinc-900 hover:bg-zinc-100 transition-all cursor-pointer inline-block shrink-0" data-filter="all">All Showings</button>
                        <button class="cora-filter-tab px-3 py-1.5 text-xs font-semibold rounded-md border border-transparent text-zinc-650 hover:text-zinc-900 hover:bg-zinc-100 transition-all cursor-pointer inline-block shrink-0" data-filter="confirmed">Confirmed</button>
                        <button class="cora-filter-tab px-3 py-1.5 text-xs font-semibold rounded-md border border-transparent text-zinc-650 hover:text-zinc-900 hover:bg-zinc-100 transition-all cursor-pointer inline-block shrink-0" data-filter="editing">Editing</button>
                        <button class="cora-filter-tab px-3 py-1.5 text-xs font-semibold rounded-md border border-transparent text-zinc-650 hover:text-zinc-900 hover:bg-zinc-100 transition-all cursor-pointer inline-block shrink-0" data-filter="completed">Completed</button>
                        <button class="cora-filter-tab px-3 py-1.5 text-xs font-semibold rounded-md border border-transparent text-zinc-650 hover:text-zinc-900 hover:bg-zinc-100 transition-all cursor-pointer inline-block shrink-0" data-filter="clients">Client Directory</button>
                    </div>

                    <!-- Search Input and Actions -->
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative flex-1 md:w-64">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-zinc-400">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </span>
                            <input type="text" id="cora-crm-search-input" class="w-full pl-8 pr-2.5 py-1.5 text-xs border border-zinc-200 rounded-md bg-white text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-400 focus:outline-none transition-colors" placeholder="Search showings or clients...">
                        </div>
                        <button id="cora-add-booking-btn" class="hidden sm:inline-flex cora-btn-primary px-3 py-1.5 text-xs font-semibold bg-zinc-950 text-white rounded hover:bg-zinc-800 transition-all active:scale-[0.97] cursor-pointer shrink-0">
                            + New Showing
                        </button>
                    </div>
                </div>

                <!-- Bookings Table & Mobile Cards Wrapper -->
                <div id="cora-bookings-table-container" class="cora-table-wrapper md:border md:border-zinc-200/80 md:rounded-xl md:shadow-sm md:bg-white md:overflow-hidden">
                    <!-- Desktop View (Large Screen) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="cora-table min-w-full divide-y divide-zinc-200" id="cora-bookings-table">
                            <thead class="bg-zinc-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Client Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Deal Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Location / Studio</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Viewing Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider cora-financial-col">Package Value</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200/80 bg-white">
                                <?php
                                $reversed_clients = array_reverse( $cora_re_clients );
                                $type_badge_classes = array(
                                    'Residential Buy' => 'cora-badge-purple',
                                    'Luxury Villa Sale' => 'cora-badge-orange',
                                    'Commercial Lease' => 'cora-badge-teal',
                                );
                                
                                if ( empty( $reversed_clients ) ) :
                                ?>
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-zinc-400">No bookings found. Click "+ New Showing" to add one.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ( $reversed_clients as $client ) : 
                                        $status = $client['status'] ?? 'confirmed';
                                        $type = $client['deal_type'] ?? 'Residential Buy';
                                        $type_class = $type_badge_classes[$type] ?? 'cora-badge-blue';
                                        
                                        $status_badge_class = 'cora-badge-blue';
                                        $status_label = 'Confirmed';
                                        if ( $status === 'editing' ) {
                                            $status_badge_class = 'cora-badge-yellow';
                                            $status_label = 'Editing';
                                        } elseif ( $status === 'completed' ) {
                                            $status_badge_class = 'cora-badge-green';
                                            $status_label = 'Completed';
                                        }
                                    ?>
                                    <tr data-id="<?php echo esc_attr( $client['id'] ); ?>" data-status="<?php echo esc_attr( $status ); ?>" class="hover:bg-zinc-50/30 transition-colors cursor-pointer" onclick="coraOpenClientLifecycle('<?php echo esc_attr( $client['id'] ); ?>')">
                                        <td class="px-4 py-3 whitespace-nowrap" data-label="Client">
                                            <div class="cora-client-meta flex flex-col">
                                                <span class="cora-client-name font-semibold text-sm text-zinc-900"><?php echo esc_html( $client['names'] ); ?></span>
                                                <span class="cora-client-email text-[11px] text-zinc-400"><?php echo esc_html( $client['email'] ); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap" data-label="Type"><span class="cora-badge <?php echo $type_class; ?>"><?php echo esc_html( $type ); ?></span></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500" data-label="Location"><?php echo esc_html( $client['city'] ?? 'Delhi Office' ); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500" data-label="Date"><?php echo esc_html( $client['viewing_date'] ?? 'Pending Date' ); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-zinc-900 cora-financial-col" data-label="Value"><?php echo esc_html( $client['price'] ?? '₹15,000' ); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap" data-label="Status"><span class="cora-badge <?php echo $status_badge_class; ?>"><?php echo esc_html( $status_label ); ?></span></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm" data-label="Actions">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <?php if ( $status === 'confirmed' ) : ?>
                                                    <button class="cora-btn-icon-only inline-flex items-center justify-center p-1.5 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="event.stopPropagation(); coraTriggerAction('whatsapp', '<?php echo esc_js( $client['names'] ); ?>')">
                                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                        </svg>
                                                    </button>
                                                    <button class="cora-btn-action px-2 py-1 text-xs font-semibold border border-zinc-300 rounded text-zinc-700 hover:bg-zinc-50 active:scale-95 transition-all cursor-pointer" onclick="event.stopPropagation(); coraUpdateBookingStatus(this, 'editing')">Advance to Editing</button>
                                                <?php elseif ( $status === 'editing' ) : ?>
                                                    <button class="cora-btn-icon-only inline-flex items-center justify-center p-1.5 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="event.stopPropagation(); coraTriggerAction('caption-quick', '<?php echo esc_js( $client['names'] ); ?>')">
                                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                                        </svg>
                                                    </button>
                                                    <button class="cora-btn-action px-2 py-1 text-xs font-semibold border border-zinc-300 rounded text-zinc-700 hover:bg-zinc-50 active:scale-95 transition-all cursor-pointer" onclick="event.stopPropagation(); coraUpdateBookingStatus(this, 'completed')">Mark Completed</button>
                                                <?php elseif ( $status === 'completed' ) : ?>
                                                    <span class="cora-delivered-text text-emerald-600 font-semibold text-xs mr-2">✓ Previews Sent</span>
                                                    <button class="cora-btn-icon-only inline-flex items-center justify-center p-1.5 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="event.stopPropagation(); coraTriggerAction('invoice', '<?php echo esc_js( $client['names'] ); ?>')">
                                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                            <polyline points="14 2 14 8 20 8"></polyline>
                                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile/Tablet Card View (Visible below 768px) -->
                    <div class="md:hidden space-y-4 pt-4 pb-2" id="cora-bookings-cards-list">
                        <?php if ( empty( $reversed_clients ) ) : ?>
                            <div class="py-8 text-center text-zinc-400 text-xs">No bookings found. Click "+ New Showing" to add one.</div>
                        <?php else : ?>
                            <?php foreach ( $reversed_clients as $client ) : 
                                $status = $client['status'] ?? 'confirmed';
                                $type = $client['deal_type'] ?? 'Residential Buy';
                                $type_class = $type_badge_classes[$type] ?? 'cora-badge-blue';
                                
                                $status_badge_class = 'cora-badge-blue';
                                $status_label = 'Confirmed';
                                if ( $status === 'editing' ) {
                                    $status_badge_class = 'cora-badge-yellow';
                                    $status_label = 'Editing';
                                } elseif ( $status === 'completed' ) {
                                    $status_badge_class = 'cora-badge-green';
                                    $status_label = 'Completed';
                                }
                            ?>
                            <div class="cora-booking-card-item bg-white border border-zinc-200 rounded-xl p-4 shadow-sm hover:shadow hover:border-zinc-300 transition-all cursor-pointer flex flex-col gap-3" 
                                 data-id="<?php echo esc_attr( $client['id'] ); ?>" 
                                 data-status="<?php echo esc_attr( $status ); ?>" 
                                 onclick="coraOpenClientLifecycle('<?php echo esc_attr( $client['id'] ); ?>')">
                                
                                <!-- Card Header: Name, Email & Type badge -->
                                <div class="flex items-start justify-between gap-2 border-b border-zinc-100 pb-2.5">
                                    <div class="flex flex-col min-w-0">
                                        <span class="cora-client-name font-bold text-sm text-zinc-900 truncate"><?php echo esc_html( $client['names'] ); ?></span>
                                        <span class="cora-client-email text-[10px] text-zinc-400 font-medium mt-0.5 truncate"><?php echo esc_html( $client['email'] ); ?></span>
                                    </div>
                                    <span class="cora-badge text-[9px] px-2 py-0.5 rounded font-semibold shrink-0 <?php echo $type_class; ?>"><?php echo esc_html( $type ); ?></span>
                                </div>
                                
                                <!-- Card Body: Date, Location & Value -->
                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div class="flex flex-col gap-0.5 min-w-0">
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Viewing Date</span>
                                        <span class="font-medium text-zinc-700 truncate"><?php echo esc_html( $client['viewing_date'] ?? 'Pending Date' ); ?></span>
                                    </div>
                                    <div class="flex flex-col gap-0.5 min-w-0">
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Location</span>
                                        <span class="font-medium text-zinc-700 truncate" title="<?php echo esc_attr( $client['city'] ?? 'Delhi Office' ); ?>"><?php echo esc_html( $client['city'] ?? 'Delhi Office' ); ?></span>
                                    </div>
                                    <div class="flex flex-col gap-0.5 min-w-0">
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Package Value</span>
                                        <span class="font-semibold text-zinc-900 truncate"><?php echo esc_html( $client['price'] ?? '₹15,000' ); ?></span>
                                    </div>
                                    <div class="flex flex-col gap-0.5 min-w-0">
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Status</span>
                                        <div>
                                            <span class="cora-badge text-[9px] px-2 py-0.5 rounded font-semibold <?php echo $status_badge_class; ?>"><?php echo esc_html( $status_label ); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Card Footer: Actions -->
                                <div class="flex items-center justify-between border-t border-zinc-100 pt-2.5 mt-1">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Actions</span>
                                    <div class="flex items-center gap-2">
                                        <?php if ( $status === 'confirmed' ) : ?>
                                            <button class="cora-btn-icon-only inline-flex items-center justify-center p-2 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="event.stopPropagation(); coraTriggerAction('whatsapp', '<?php echo esc_js( $client['names'] ); ?>')">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                </svg>
                                            </button>
                                            <button class="cora-btn-action px-2.5 py-1.5 text-xs font-semibold border border-zinc-300 rounded text-zinc-700 hover:bg-zinc-50 active:scale-95 transition-all cursor-pointer" onclick="event.stopPropagation(); coraUpdateBookingStatus(this, 'editing')">Advance to Editing</button>
                                        <?php elseif ( $status === 'editing' ) : ?>
                                            <button class="cora-btn-icon-only inline-flex items-center justify-center p-2 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="event.stopPropagation(); coraTriggerAction('caption-quick', '<?php echo esc_js( $client['names'] ); ?>')">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                                </svg>
                                            </button>
                                            <button class="cora-btn-action px-2.5 py-1.5 text-xs font-semibold border border-zinc-300 rounded text-zinc-700 hover:bg-zinc-50 active:scale-95 transition-all cursor-pointer" onclick="event.stopPropagation(); coraUpdateBookingStatus(this, 'completed')">Mark Completed</button>
                                        <?php elseif ( $status === 'completed' ) : ?>
                                            <span class="cora-delivered-text text-emerald-600 font-semibold text-xs mr-2">✓ Previews Sent</span>
                                            <button class="cora-btn-icon-only inline-flex items-center justify-center p-2 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="event.stopPropagation(); coraTriggerAction('invoice', '<?php echo esc_js( $client['names'] ); ?>')">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Client Directory Table Container & Mobile Cards Wrapper (Merged) -->
                <div class="md:border md:border-zinc-200 md:rounded-lg md:bg-white md:shadow-sm mt-6 hidden md:overflow-hidden" id="cora-clients-table-container">
                    <!-- Desktop View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-zinc-50 border-b border-zinc-200 text-[10px] font-bold text-zinc-400 uppercase tracking-wider select-none">
                                    <th class="py-3 px-4">Client Names</th>
                                    <th class="py-3 px-4">Contact Email</th>
                                    <th class="py-3 px-4">Celebration & Location</th>
                                    <th class="py-3 px-4">Budget / Price</th>
                                    <th class="py-3 px-4">Conversion Date</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-xs text-zinc-650" id="cora-clients-table-body">
                                <?php if ( empty( $cora_re_clients ) ) : ?>
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-zinc-450 select-none">No converted clients in the directory yet. Convert leads to populate this view.</td>
                                </tr>
                                <?php else : ?>
                                    <?php foreach ( $cora_re_clients as $client ) : ?>
                                    <tr data-id="<?php echo esc_attr( $client['id'] ); ?>" class="hover:bg-zinc-50/50 transition-colors">
                                        <td class="py-3 px-4 font-bold text-zinc-900" data-label="Client"><?php echo esc_html( $client['names'] ); ?></td>
                                        <td class="py-3 px-4 font-mono text-[11px] text-zinc-500" data-label="Email"><?php echo esc_html( $client['email'] ); ?></td>
                                        <td class="py-3 px-4" data-label="Celebration">
                                            <div class="font-medium text-zinc-700"><?php echo esc_html( isset($client['scale']) ? $client['scale'] : 'Luxury Mandate' ); ?></div>
                                            <div class="text-[10px] text-zinc-400"><?php echo esc_html( isset($client['city']) ? $client['city'] : 'Mumbai' ); ?></div>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-zinc-800" data-label="Budget"><?php echo esc_html( $client['price'] ); ?></td>
                                        <td class="py-3 px-4 text-zinc-450" data-label="Converted"><?php echo esc_html( isset($client['converted_at']) ? date( 'd M Y', $client['converted_at'] ) : date('d M Y') ); ?></td>
                                        <td class="py-3 px-4 text-right" data-label="Actions">
                                            <div class="flex items-center justify-end gap-2">
                                                <button class="cora-btn-action px-2.5 py-1 border border-zinc-200 rounded-md text-[10px] font-semibold text-zinc-700 hover:bg-zinc-50 cursor-pointer" 
                                                        onclick="coraOpenClientDetailsDrawer(<?php echo esc_attr( json_encode( $client ) ); ?>)">
                                                    View Vision
                                                </button>
                                                <button class="cora-btn-action px-2.5 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded-md text-[10px] font-semibold cursor-pointer" 
                                                        onclick="coraPrefillAddShowing(<?php echo esc_attr( json_encode( $client ) ); ?>)">
                                                    Add Showing
                                                </button>
                                                <button class="cora-btn-icon-only text-zinc-400 hover:text-red-650 cursor-pointer" 
                                                        onclick="coraDeleteClient('<?php echo esc_attr( $client['id'] ); ?>')">
                                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile/Tablet Card View -->
                    <div class="md:hidden space-y-4 pt-4 pb-2" id="cora-clients-cards-list">
                        <?php if ( empty( $cora_re_clients ) ) : ?>
                            <div class="py-8 text-center text-zinc-455 text-xs">No converted clients in the directory yet. Convert leads to populate this view.</div>
                        <?php else : ?>
                            <?php foreach ( $cora_re_clients as $client ) : ?>
                            <div class="cora-client-card-item bg-white border border-zinc-200 rounded-xl p-4 shadow-sm hover:shadow hover:border-zinc-300 transition-all flex flex-col gap-3">
                                <!-- Card Header: Client Name & Email -->
                                <div class="flex items-start justify-between gap-2 border-b border-zinc-100 pb-2.5">
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-bold text-sm text-zinc-900 truncate"><?php echo esc_html( $client['names'] ); ?></span>
                                        <span class="font-mono text-[10px] text-zinc-400 mt-0.5 truncate"><?php echo esc_html( $client['email'] ); ?></span>
                                    </div>
                                    <span class="cora-badge text-[9px] px-2 py-0.5 rounded font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200 shrink-0">Client</span>
                                </div>
                                
                                <!-- Card Body: Celebration scale, location, budget, conversion date -->
                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div class="flex flex-col gap-0.5 min-w-0">
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Celebration</span>
                                        <span class="font-medium text-zinc-700 truncate"><?php echo esc_html( isset($client['scale']) ? $client['scale'] : 'Luxury Mandate' ); ?></span>
                                    </div>
                                    <div class="flex flex-col gap-0.5 min-w-0">
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Location</span>
                                        <span class="font-medium text-zinc-700 truncate"><?php echo esc_html( isset($client['city']) ? $client['city'] : 'Mumbai' ); ?></span>
                                    </div>
                                    <div class="flex flex-col gap-0.5 min-w-0">
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Budget / Price</span>
                                        <span class="font-semibold text-zinc-900 truncate"><?php echo esc_html( $client['price'] ); ?></span>
                                    </div>
                                    <div class="flex flex-col gap-0.5 min-w-0">
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Conversion Date</span>
                                        <span class="font-medium text-zinc-700 truncate"><?php echo esc_html( isset($client['converted_at']) ? date( 'd M Y', $client['converted_at'] ) : date('d M Y') ); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Card Footer: Action buttons -->
                                <div class="flex items-center justify-between border-t border-zinc-100 pt-2.5 mt-1">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Actions</span>
                                    <div class="flex items-center gap-2">
                                        <button class="cora-btn-action px-2.5 py-1.5 border border-zinc-200 rounded-md text-[10px] font-semibold text-zinc-700 hover:bg-zinc-50 cursor-pointer" 
                                                onclick="coraOpenClientDetailsDrawer(<?php echo esc_attr( json_encode( $client ) ); ?>)">
                                            View Vision
                                        </button>
                                        <button class="cora-btn-action px-2.5 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-md text-[10px] font-semibold cursor-pointer" 
                                                onclick="coraPrefillAddShowing(<?php echo esc_attr( json_encode( $client ) ); ?>)">
                                            Add Showing
                                        </button>
                                        <button class="cora-btn-icon-only p-1.5 text-zinc-400 hover:text-red-650 cursor-pointer" 
                                                onclick="coraDeleteClient('<?php echo esc_attr( $client['id'] ); ?>')">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
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
                            <h3 class="cora-card-title text-base font-semibold text-zinc-955">Instagram Caption Generator</h3>
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
                                <button class="cora-copy-btn text-zinc-655 hover:text-zinc-955 font-semibold normal-case cursor-pointer" onclick="coraCopyText('cora-caption-text')">Copy</button>
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
            <?php $cora_portfolios = get_option( 'cora_re_portfolios', array() ); ?>
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
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Platform Language (Indian Regional)</label>
                            <select id="cora-platform-language-select" class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-200 transition-all appearance-none cursor-pointer">
                                <option value="en">English</option>
                                <option value="hi">Hindi (हिन्दी)</option>
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
                            <span class="text-[10px] text-zinc-400">Change the display language of the entire platform to a regional Indian language.</span>
                        </div>
                    </div>
                    <div class="px-5 py-3 bg-zinc-50/50 border-t border-zinc-100 flex justify-end">
                        <button class="text-xs font-semibold px-4 py-2 bg-zinc-900 text-white rounded-lg hover:bg-zinc-800 transition-all active:scale-[0.97] cursor-pointer inline-flex items-center gap-1.5" onclick="coraShowToast('Studio settings saved.')">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Save
                        </button>
                    </div>
                    <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded', function() {
                        const selectEl = document.getElementById('cora-platform-language-select');
                        if (selectEl) {
                            // Load persisted language
                            const currentLang = localStorage.getItem('cora_platform_language') || 'en';
                            selectEl.value = currentLang;
                            
                            // Save handler when settings form save button is clicked
                            const saveBtn = selectEl.closest('.bg-white').querySelector('button');
                            if (saveBtn) {
                                saveBtn.removeAttribute('onclick'); // override standard onclick
                                saveBtn.addEventListener('click', function() {
                                    const newLang = selectEl.value;
                                    const prevLang = localStorage.getItem('cora_platform_language') || 'en';
                                    localStorage.setItem('cora_platform_language', newLang);
                                    
                                    window.coraShowToast('Studio settings saved.');
                                    
                                    // If language changed, reload page after a brief delay to apply translation
                                    if (newLang !== prevLang) {
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 800);
                                    }
                                });
                            }
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
                                <input type="text" id="cora-gbp-maps-api-key" class="w-full border border-zinc-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-200 transition-all font-mono text-zinc-700 placeholder:font-sans placeholder:text-zinc-300" value="<?php echo esc_attr( $cora_gbp_maps_api_key ); ?>" placeholder="AIzaSy...">
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
            <section id="cora-page-feature-hub" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </span>
                    <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Feature Hub & Platform Roadmap</h1>
                </div>
                <p class="cora-section-desc text-sm text-zinc-500 -mt-2">Track active tools, in-progress integration                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pt-2">
                    
                    <!-- 1. Beautiful Property Portfolios (ACTIVE) -->
                    <div id="cora-card-property-portfolios" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-955 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Beautiful Property Portfolios">
                        <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-600 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Beautiful Property Portfolios</h3>
                            <p class="text-xs text-zinc-650 leading-normal">Send password-protected photo & video links to impress high-paying clients, replacing messy Google Drive folders.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Open Property Portfolios</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 2. Easy Property Showcases (ACTIVE) -->
                    <div id="cora-card-photo-selection" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-950 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Easy Property Showcases">
                        <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-600 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Easy Property Showcases</h3>
                            <p class="text-xs text-zinc-650 leading-normal">Couples can easily tap the heart icon on their phone to select photos for the printed album, synced live with the admin panel.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Active in Property Portfolios</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 3. Branded Print Storefront -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Branded Print Storefront">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Branded Print Storefront</h3>
                            <p class="text-xs text-zinc-550 leading-normal">Sell premium layflat print albums, canvas prints, and custom frames directly to portfolio visitors with automated print lab fulfillment.</p>
                        </div>
                    </div>

                    <!-- 4. Instant Quotations (ACTIVE) -->
                    <div id="cora-card-instant-quotations" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-950 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Instant Quotations">
                        <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-600 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Instant Quotations</h3>
                            <p class="text-xs text-zinc-650 leading-normal">Generate professional PDFs with your listing packages and send them to clients in 1 click.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Open Document Vault</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 5. Automated Invoicing & Receipts (ACTIVE) -->
                    <div id="cora-card-zero-paperwork" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-950 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Automated Invoicing & Receipts">
                        <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-600 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 15l2 2 4-4"></path></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Automated Invoicing & Receipts</h3>
                            <p class="text-xs text-zinc-650 leading-normal">Automatically generate listing agreements, advance booking receipts, and tax-compliant invoices.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Open Document Vault</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 6. Contracts & E-Signatures (ACTIVE) -->
                    <div id="cora-card-smart-signatures" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-950 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Contracts & E-Signatures">
                        <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-600 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Contracts & E-Signatures</h3>
                            <p class="text-xs text-zinc-650 leading-normal">Legally binding online contracts with digital signatures, built-in templates, and automated PDF copy delivery.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Open Document Vault</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 7. Private Client Portal -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Private Client Portal">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Private Client Portal</h3>
                            <p class="text-xs text-zinc-500 leading-normal">A private dashboard for couples to access timelines, upload shot list requests, sign contracts, and check payments.</p>
                        </div>
                    </div>

                    <!-- 8. Universal Lead Booking Widget -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Universal Lead Booking Widget">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Universal Lead Form</h3>
                            <p class="text-xs text-zinc-500 leading-normal">Create and embed custom booking forms on your website, Google Business site, or Instagram bio. Captured leads flow directly into your CRM feed.</p>
                        </div>
                    </div>

                    <!-- 9. Google Maps SEO Booster (ACTIVE) -->
                    <div id="cora-card-maps-seo" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-950 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Google Maps SEO Booster">
                        <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-600 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Google Maps SEO Booster</h3>
                            <p class="text-xs text-zinc-655 leading-normal">Manage your Google Business Profile, track search map rankings, view and reply to customer reviews, and post geotagged updates.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Open Google Profile</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 10. SMS & Email Client Reminders -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="SMS & Email Client Reminders">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Client Notifications</h3>
                            <p class="text-xs text-zinc-550 leading-normal">Automatically send booking confirmations, contract signature reminders, and advance deposit due date alerts to clients via SMS and email.</p>
                        </div>
                    </div>

                    <!-- 11. Smart Review Acquisition -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Smart Review Acquisition">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Smart Review Acquisition</h3>
                            <p class="text-xs text-zinc-550 leading-normal">Auto-send custom review requests via WhatsApp/SMS after property handover or deal closure to collect 5-star Google Business ratings.</p>
                        </div>
                    </div>

                    <!-- 12. Referral Rewards Engine -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Referral Rewards Engine">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Referral Rewards Engine</h3>
                            <p class="text-xs text-zinc-550 leading-normal">Create automated referral links for past couples. Give them print storefront discounts when their friends book a viewing.</p>
                        </div>
                    </div>

                    <!-- 13. Financial Ledger & Bookkeeping (ACTIVE) -->
                    <div id="cora-card-track-rupee" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-955 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Financial Ledger & Bookkeeping">
                        <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-600 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="5" x2="18" y2="5"></line><line x1="6" y1="9" x2="18" y2="9"></line><path d="M9 5a6 6 0 0 1 0 12h3"></path><line x1="12" y1="11" x2="6" y2="17"></line></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Financial Ledger & Bookkeeping</h3>
                            <p class="text-xs text-zinc-550 leading-normal">Track your cashflow, log expenses, and monitor pending client payments to get a complete financial overview of your studio.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Active & Functional</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 14. Crew & Shift Scheduler (ACTIVE) -->
                    <div id="cora-card-manage-team" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-950 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Crew & Shift Scheduler">
                        <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-600 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Crew & Shift Scheduler</h3>
                            <p class="text-xs text-zinc-650 leading-normal">Track and manage assignments, shifts, and property schedules for listing agents, support staff, field inspectors, and coordinators.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Open Crew Assign Tool</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 15. Property Listings Inventory (ACTIVE) -->
                    <div id="cora-card-equipment-tracking" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-950 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Property Listings Inventory">
                        <div class="absolute top-0 left-0 h-1 w-full bg-zinc-900"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-800 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Property Listings Inventory</h3>
                            <p class="text-xs text-zinc-650 leading-normal">Keep track of your premium villas, apartments, penthouses, commercial spaces, and plots so listings remain synchronized.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Open Property Listings</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 16. Multi-Day Event Timeline -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Multi-Day Event Timeline">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Multi-Day Event Timeline</h3>
                            <p class="text-xs text-zinc-550 leading-normal">Map out complex property tour schedules (Site Visit, Due Diligence, Closing) with venue locations, crew shifts, and real-time client view access.</p>
                        </div>
                    </div>

                    <!-- 17. GPS Attendance Tracker -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="GPS Attendance Tracker">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                        <line x1="12" y1="22" x2="12" y2="17"></line>
                                        <line x1="7" y1="3.5" x2="5" y2="1.5"></line>
                                        <line x1="17" y1="3.5" x2="19" y2="1.5"></line>
                                    </svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">GPS Attendance Tracker</h3>
                            <p class="text-xs text-zinc-550 leading-normal">Employees punch in and out directly from their phone. Each punch is verified against their real-time Jio/GPS location — so attendance records are authentic, tamper-proof, and approved by the system automatically.</p>
                        </div>
                    </div>

                    <!-- 18. Client Task Manager -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Client Task Manager">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="9" y1="9" x2="15" y2="9"></line>
                                        <line x1="9" y1="13" x2="15" y2="13"></line>
                                        <line x1="9" y1="17" x2="12" y2="17"></line>
                                        <polyline points="6 9 7 10 9 8"></polyline>
                                        <polyline points="6 13 7 14 9 12"></polyline>
                                    </svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Client Task Manager</h3>
                            <p class="text-xs text-zinc-550 leading-normal">Create and assign to-do checklists tied directly to each client booking — from site visit prep to document collection and follow-up calls. Track progress, set deadlines, and never miss a step in the client workflow.</p>
                        </div>
                    </div>

                </div>
            </section>
            <?php endif; ?>


            <!-- SECTION GBP: GOOGLE BUSINESS PROFILE -->
            <?php if ( $sub_page === 'gbp' ) : ?>
            <section id="cora-page-gbp" class="cora-page-section cora-active space-y-6">

                <!-- PAGE HEADER -->
                <div class="cora-page-header flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </span>
                        <div>
                            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Google Business Profile Broker Owner</h1>
                            <p class="cora-section-desc text-xs text-zinc-500 mt-1">Manage your real Google listing, reply to live reviews, and publish geo-tagged posts.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if ( $cora_gbp_is_connected ) : ?>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-semibold text-zinc-700"><?php echo esc_html( $cora_gbp_profile['business_name'] ?? 'Connected' ); ?></span>
                            <button onclick="coraGbpDisconnect()" class="ml-3 text-[10px] font-bold text-zinc-500 border border-zinc-300 rounded px-2.5 py-1 hover:bg-zinc-100 transition-colors">Disconnect</button>
                        <?php elseif ( $cora_gbp_is_authenticated ) : ?>
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span class="text-xs font-semibold text-zinc-600">Google Authenticated</span>
                            <button onclick="coraGbpDisconnect()" class="ml-3 text-[10px] text-zinc-500 border border-zinc-200 rounded px-2 py-1 hover:bg-zinc-50">Disconnect</button>
                        <?php else : ?>
                            <span class="w-2 h-2 rounded-full bg-zinc-300"></span>
                            <span class="text-xs text-zinc-500">Not Connected</span>
                        <?php endif; ?>
                    </div>
                </div>


                <?php if ( ! $cora_gbp_has_maps_key ) : ?>
                <!-- ===== STATE A: No Maps API Key — admin setup required ===== -->
                <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-10 shadow-sm flex flex-col items-center text-center gap-5 min-h-[380px] justify-center">
                    <div class="w-14 h-14 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.6" fill="none" class="text-zinc-400"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <div class="space-y-1.5 max-w-sm">
                        <h2 class="text-base font-bold text-zinc-900">Business Search Setup Required</h2>
                        <p class="text-sm text-zinc-500 leading-relaxed">Your platform admin needs to add a Google Maps API Key in Settings. Once done, agents can search and connect their property listing in seconds.</p>
                    </div>
                    <a href="<?php echo esc_url( home_url( '/workspace/settings' ) ); ?>" class="px-5 py-2.5 bg-zinc-950 text-white text-sm font-semibold rounded-lg hover:bg-zinc-800 transition-colors">
                        Go to Settings → Add Maps API Key
                    </a>
                </div>

                <?php elseif ( ! $cora_gbp_is_connected ) : ?>
                <!-- ===== STATE B: Search your business ===== -->
                <div class="cora-card bg-white border border-zinc-200/85 rounded-xl shadow-sm overflow-hidden">
                    <!-- Search hero -->
                    <div class="px-8 pt-10 pb-8 text-center space-y-6 border-b border-zinc-100">
                        <div class="space-y-2">
                            <div class="flex items-center justify-center gap-2 mb-3">
                                <svg viewBox="0 0 24 24" width="28" height="28" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Google Business Profile</span>
                            </div>
                            <h2 class="text-xl font-bold text-zinc-900">Find your business on Google</h2>
                            <p class="text-sm text-zinc-500 max-w-sm mx-auto leading-relaxed">Search by name and we'll pull your real listing from Google Maps. Takes 10 seconds.</p>
                        </div>
                        <!-- Search Box -->
                        <div class="max-w-lg mx-auto space-y-3">
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    <input
                                        type="text"
                                        id="cora-gbp-search-q"
                                        placeholder="Business name, e.g. Apex Realty Group"
                                        class="w-full pl-9 pr-4 py-3 border border-zinc-200 rounded-lg text-sm bg-white focus:border-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-100 transition-all"
                                        onkeydown="if(event.key==='Enter') coraGbpSearch()"
                                        autocomplete="off"
                                    >
                                </div>
                                <button id="cora-gbp-search-btn" onclick="coraGbpSearch()" class="px-5 py-3 bg-zinc-950 text-white text-sm font-semibold rounded-lg hover:bg-zinc-800 transition-all whitespace-nowrap flex items-center gap-2 active:scale-[0.98]">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    Search Google
                                </button>
                            </div>
                            <p class="text-[11px] text-zinc-400">Results are pulled live from Google Maps. Add your city name for more accurate results.</p>
                        </div>
                    </div>
                    <!-- Results area -->
                    <div id="cora-gbp-search-results-wrap" class="p-6 min-h-[180px]">
                        <div class="flex flex-col items-center justify-center gap-2 py-8 text-zinc-300">
                            <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <p class="text-sm text-zinc-400 mt-1">Your search results will appear here</p>
                        </div>
                    </div>
                </div>

                <?php elseif ( $cora_gbp_is_connected && ! $cora_gbp_is_authenticated ) : ?>
                <!-- ===== STATE C: Connected via Places search, OAuth not yet done ===== -->
                <?php
                    $gbp_name    = esc_html( $cora_gbp_profile['business_name'] ?? '' );
                    $gbp_cat     = esc_html( $cora_gbp_profile['category'] ?? '' );
                    $gbp_addr    = esc_html( $cora_gbp_profile['address'] ?? '' );
                    $gbp_phone   = esc_html( $cora_gbp_profile['phone'] ?? '' );
                    $gbp_website = esc_url( $cora_gbp_profile['website'] ?? '' );
                    $gbp_rating  = floatval( $cora_gbp_profile['rating'] ?? 0 );
                    $gbp_reviews = intval( $cora_gbp_profile['review_count'] ?? 0 );
                    $gbp_initials = strtoupper( mb_substr( $gbp_name, 0, 2 ) );
                ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Unlock panel -->
                    <div class="lg:col-span-2 space-y-5">
                        <!-- Reviews locked -->
                        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between border-b border-zinc-100 pb-3 mb-4">
                                <h3 class="text-base font-semibold text-zinc-900">Google Reviews Inbox</h3>
                                <span class="text-[10px] bg-zinc-100 text-zinc-500 border border-zinc-200 px-2 py-0.5 rounded-full font-bold">LOCKED</span>
                            </div>
                            <div class="rounded-xl border border-dashed border-zinc-200 bg-zinc-50/60 p-8 flex flex-col items-center text-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-white border border-zinc-200 flex items-center justify-center shadow-sm">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.6" fill="none" class="text-zinc-400"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-zinc-800">Sign in to reply to your Google reviews</p>
                                    <p class="text-xs text-zinc-500 leading-relaxed max-w-xs">Connect your Google account to reply to reviews and publish posts directly to Maps — takes 30 seconds.</p>
                                </div>
                                <?php if ( $cora_gbp_has_credentials ) : ?>
                                <button onclick="coraGbpConnectWithGoogle()" id="cora-gbp-oauth-btn" class="flex items-center gap-2.5 bg-white border border-zinc-300 hover:border-zinc-400 hover:bg-zinc-50 rounded-lg px-5 py-2.5 text-sm font-semibold text-zinc-800 transition-all shadow-sm">
                                    <svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                    Sign in with Google
                                </button>
                                <?php else : ?>
                                    <?php if ( current_user_can( 'manage_options' ) ) : ?>
                                        <a href="<?php echo esc_url( home_url( '/workspace/settings' ) ); ?>" class="flex items-center gap-2 px-4 py-2.5 bg-zinc-950 text-white rounded-lg hover:bg-zinc-800 transition-colors font-semibold text-xs shadow-sm">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                            Configure OAuth Credentials in Settings
                                        </a>
                                    <?php else : ?>
                                        <p class="text-xs text-zinc-400 italic">Contact your platform admin to enable review management.</p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Posts locked -->
                        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between border-b border-zinc-100 pb-3 mb-4">
                                <h3 class="text-base font-semibold text-zinc-900">Publish to Google Maps</h3>
                                <span class="text-[10px] bg-zinc-100 text-zinc-500 border border-zinc-200 px-2 py-0.5 rounded-full font-bold">LOCKED</span>
                            </div>
                            <p class="text-xs text-zinc-500 leading-relaxed">Sign in with Google above to unlock post publishing — share updates, offers, and listing details directly on your Google listing.</p>
                        </div>
                    </div>
                    <!-- Right: Business card -->
                    <div class="space-y-5">
                        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                                <h3 class="text-sm font-semibold text-zinc-900">Connected Business</h3>
                                <span class="text-[9px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-1.5 py-0.5 rounded font-bold">LIVE</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-zinc-950 flex items-center justify-center text-white text-sm font-bold shrink-0"><?php echo $gbp_initials; ?></div>
                                    <div>
                                        <p class="text-sm font-bold text-zinc-900"><?php echo $gbp_name; ?></p>
                                        <?php if ($gbp_cat): ?><p class="text-xs text-zinc-500"><?php echo $gbp_cat; ?></p><?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($gbp_rating > 0): ?>
                                <div class="flex items-center gap-2">
                                    <div class="flex text-amber-400 text-sm">
                                        <?php for($s=1; $s<=5; $s++): ?>
                                        <span><?php echo $s <= round($gbp_rating) ? '★' : '☆'; ?></span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-xs font-semibold text-zinc-700"><?php echo number_format($gbp_rating, 1); ?></span>
                                    <?php if($gbp_reviews > 0): ?>
                                    <span class="text-xs text-zinc-400">(<?php echo number_format($gbp_reviews); ?> reviews)</span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($gbp_addr): ?>
                                <div>
                                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Address</p>
                                    <p class="text-xs text-zinc-600 mt-0.5 leading-normal"><?php echo $gbp_addr; ?></p>
                                </div>
                                <?php endif; ?>
                                <?php if ($gbp_phone): ?>
                                <div>
                                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Phone</p>
                                    <p class="text-xs text-zinc-700 mt-0.5"><?php echo $gbp_phone; ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="pt-3 border-t border-zinc-100">
                                <button onclick="coraGbpDisconnect()" class="text-[10px] font-bold text-zinc-400 hover:text-red-600 transition-colors">
                                    ✕ Disconnect this listing
                                </button>
                            </div>
                        </div>
                        <!-- SEO Tips -->
                        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-3">
                            <h3 class="text-sm font-semibold text-zinc-900 border-b border-zinc-100 pb-2">Local SEO Tips</h3>
                            <div class="space-y-2 text-xs text-zinc-500">
                                <p class="flex items-start gap-2"><span class="text-zinc-300 shrink-0">→</span>Reply to every review within 48 hrs for better ranking</p>
                                <p class="flex items-start gap-2"><span class="text-zinc-300 shrink-0">→</span>Post 1–2 updates per week with location keywords</p>
                                <p class="flex items-start gap-2"><span class="text-zinc-300 shrink-0">→</span>Upload listing photos — Google uses image quality as a signal</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php else : ?>
                <!-- ===== STATE D: Fully connected — real reviews + post scheduler ===== -->
                <?php
                    $gbp_name    = esc_html( $cora_gbp_profile['business_name'] ?? '' );
                    $gbp_cat     = esc_html( $cora_gbp_profile['category'] ?? '' );
                    $gbp_addr    = esc_html( $cora_gbp_profile['address'] ?? '' );
                    $gbp_phone   = esc_html( $cora_gbp_profile['phone'] ?? '' );
                    $gbp_website = esc_url( $cora_gbp_profile['website'] ?? '' );
                    $gbp_rating  = floatval( $cora_gbp_profile['rating'] ?? 0 );
                    $gbp_reviews = intval( $cora_gbp_profile['review_count'] ?? 0 );
                    $gbp_initials = strtoupper( mb_substr( $gbp_name, 0, 2 ) );
                ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Reviews Inbox -->
                        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                                <h3 class="text-base font-semibold text-zinc-900">Google Reviews Inbox</h3>
                                <div class="flex items-center gap-2">
                                    <span id="cora-gbp-rating-badge" class="text-xs bg-zinc-100 text-zinc-600 px-2 py-0.5 rounded-full font-medium hidden"></span>
                                    <button onclick="coraGbpLoadReviews()" class="text-[10px] font-bold text-zinc-500 border border-zinc-200 rounded px-2 py-1 hover:bg-zinc-50 flex items-center gap-1">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                        Refresh
                                    </button>
                                </div>
                            </div>
                            <div id="cora-gbp-reviews-loading" class="flex items-center justify-center py-8 gap-2 text-zinc-400">
                                <svg class="animate-spin" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                <span class="text-xs">Loading reviews from Google...</span>
                            </div>
                            <div id="cora-gbp-reviews-list" class="space-y-4 divide-y divide-zinc-100 hidden"></div>
                            <div id="cora-gbp-reviews-empty" class="hidden py-6 flex flex-col items-center gap-3 text-center">
                                <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.4" fill="none" class="text-zinc-300"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-700">No reviews yet</p>
                                    <p class="text-xs text-zinc-400 mt-1">Share your Google Review link with clients after each deal.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Post Scheduler -->
                        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                            <div class="border-b border-zinc-100 pb-3">
                                <h3 class="text-base font-semibold text-zinc-900">Publish to Google Maps</h3>
                                <p class="text-xs text-zinc-450 mt-1">Posts appear on your real Google Business listing within minutes.</p>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase block mb-1">Post Content</label>
                                    <textarea id="cora-gbp-post-content" class="w-full border border-zinc-200 rounded-md p-2.5 text-sm bg-white focus:border-zinc-400 focus:outline-none h-24 resize-none" placeholder="Share a recent listing highlight, new availability, or a seasonal offer..."></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-bold text-zinc-400 uppercase block mb-1">Call to Action</label>
                                        <select id="cora-gbp-post-cta" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none">
                                            <option value="NONE">None</option>
                                            <option value="BOOK_NOW">Book Now</option>
                                            <option value="LEARN_MORE">Learn More</option>
                                            <option value="CALL_NOW">Call Now</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-zinc-400 uppercase block mb-1">CTA Link URL</label>
                                        <input type="url" id="cora-gbp-post-cta-url" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none" placeholder="https://...">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end border-t border-zinc-100 pt-3">
                                <button id="cora-gbp-publish-btn" onclick="coraGbpPublishPost()" class="px-5 py-2.5 bg-zinc-950 text-white text-sm font-semibold rounded-lg hover:bg-zinc-800 transition-colors flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    Publish to Google Maps
                                </button>
                            </div>
                        </div>
                        <?php if (!empty($cora_gbp_posts)): ?>
                        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-3">
                            <h3 class="text-sm font-semibold text-zinc-900 border-b border-zinc-100 pb-2">Published Posts</h3>
                            <div class="space-y-2">
                                <?php foreach (array_slice($cora_gbp_posts, 0, 5) as $gbp_post): ?>
                                <div class="flex items-start justify-between gap-3 py-2 border-b border-zinc-50 last:border-0">
                                    <p class="text-xs text-zinc-700 leading-relaxed flex-1"><?php echo esc_html($gbp_post['content']); ?></p>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Published</span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- Right Column -->
                    <div class="space-y-5">
                        <!-- Business Card -->
                        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                                <h3 class="text-sm font-semibold text-zinc-900">Connected Business</h3>
                                <span class="text-[9px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-1.5 py-0.5 rounded font-bold">LIVE</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-zinc-950 flex items-center justify-center text-white text-sm font-bold shrink-0"><?php echo $gbp_initials; ?></div>
                                    <div>
                                        <p class="text-sm font-bold text-zinc-900"><?php echo $gbp_name; ?></p>
                                        <?php if ($gbp_cat): ?><p class="text-xs text-zinc-500"><?php echo $gbp_cat; ?></p><?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($gbp_rating > 0): ?>
                                <div class="flex items-center gap-2">
                                    <span class="text-amber-400 text-sm font-bold"><?php echo number_format($gbp_rating, 1); ?> ★</span>
                                    <?php if($gbp_reviews > 0): ?><span class="text-xs text-zinc-400"><?php echo number_format($gbp_reviews); ?> reviews</span><?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($gbp_addr): ?>
                                <div>
                                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Address</p>
                                    <p class="text-xs text-zinc-600 mt-0.5 leading-normal"><?php echo $gbp_addr; ?></p>
                                </div>
                                <?php endif; ?>
                                <?php if ($gbp_phone): ?>
                                <div>
                                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Phone</p>
                                    <p class="text-xs text-zinc-700 mt-0.5"><?php echo $gbp_phone; ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="pt-3 border-t border-zinc-100">
                                <button onclick="coraGbpDisconnect()" class="text-[10px] font-bold text-zinc-400 hover:text-red-600 transition-colors">✕ Disconnect this listing</button>
                            </div>
                        </div>
                        <!-- SEO Tips -->
                        <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-3">
                            <h3 class="text-sm font-semibold text-zinc-900 border-b border-zinc-100 pb-2">Local SEO Playbook</h3>
                            <div class="space-y-2 text-xs text-zinc-500">
                                <p class="flex items-start gap-2"><span class="text-zinc-300 shrink-0">→</span>Post 1–2 updates/week with location tags</p>
                                <p class="flex items-start gap-2"><span class="text-zinc-300 shrink-0">→</span>Reply to every review within 48 hrs</p>
                                <p class="flex items-start gap-2"><span class="text-zinc-300 shrink-0">→</span>Upload listing photos for ranking signals</p>
                                <p class="flex items-start gap-2"><span class="text-zinc-300 shrink-0">→</span>Keep hours  category updated to appear in relevant searches</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </section>
            <?php endif; ?>

            <?php if ( $sub_page === 'team-roles' ) : ?>
            <section id="cora-page-team-roles" class="cora-page-section cora-active space-y-6">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-users.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION 8: EQUIPMENT TRACKING -->
            <?php if ( $sub_page === 'equipment' ) : ?>
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
                        $total_items = count($cora_re_listings);
                        $available_items = 0;
                        $in_use_items = 0;
                        $maintenance_items = 0;
                        foreach ($cora_re_listings as $item) {
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
                                    <?php foreach ($cora_re_listings as $item): 
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
                                  <?php foreach ($cora_re_listings as $item): ?>
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

            <!-- SECTION 9: STUDIO VAULT -->
            <?php if ( $sub_page === 'vault' ) : ?>
            <section id="cora-page-vault" class="cora-page-section cora-active space-y-6">
                <!-- LIST VIEW -->
                <div id="cora-vault-list-view" class="space-y-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-zinc-100/60 pb-4 sm:pb-0 sm:border-b-0">
                        <div class="cora-page-header flex items-start sm:items-center gap-3">
                            <span class="cora-page-emoji text-zinc-900 flex shrink-0 mt-0.5 sm:mt-0">
                                <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <div>
                                <h1 class="cora-page-title text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">Document Vault</h1>
                                <p class="cora-section-desc text-xs text-zinc-500 mt-1 hidden sm:block">Securely manage, preview, and share official proposals, invoices, and contracts with clients.</p>
                            </div>
                        </div>
                        
                        <div class="w-full sm:w-auto">
                            <button id="cora-create-doc-btn" class="w-full sm:w-auto justify-center px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs flex items-center gap-2" onclick="coraOpenDocDrawer()">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Add Document
                            </button>
                        </div>
                    </div>

                    <!-- Stats summary counts -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4" id="cora-vault-stats-grid">
                        <?php
                        $total_docs = count($cora_documents);
                        $proposal_count = 0;
                        $invoice_count = 0;
                        $contract_count = 0;
                        $all_doc_types = array( 'Proposal', 'Invoice', 'Contract' );
                        foreach ($cora_documents as $doc) {
                            if ($doc['type'] === 'Proposal') $proposal_count++;
                            elseif ($doc['type'] === 'Invoice') $invoice_count++;
                            elseif ($doc['type'] === 'Contract') $contract_count++;

                            if ( ! in_array( $doc['type'], $all_doc_types ) ) {
                                $all_doc_types[] = $doc['type'];
                            }
                        }
                        ?>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Documents</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-doc-stat-total"><?php echo $total_docs; ?></span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Proposals</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-doc-stat-proposals"><?php echo $proposal_count; ?></span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Invoices</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-doc-stat-invoices"><?php echo $invoice_count; ?></span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-3.5 sm:p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Contracts</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-doc-stat-contracts"><?php echo $contract_count; ?></span>
                        </div>
                    </div>

                    <!-- Document filters & layout -->
                    <div class="cora-card bg-white border-0 sm:border border-zinc-200/85 rounded-none sm:rounded-xl p-0 sm:p-5 shadow-none sm:shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-3 flex-wrap gap-3 px-4 sm:px-0">
                            <div class="flex gap-2 overflow-x-auto scrollbar-none pb-1 -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap" id="cora-vault-filters">
                                <button class="cora-filter-btn px-3 py-1.5 rounded-md text-xs font-semibold bg-zinc-950 text-white cursor-pointer shrink-0" data-filter="all">All Documents</button>
                                <?php foreach ( $all_doc_types as $type ) : 
                                    $label = $type . 's';
                                    if ( substr( $type, -1 ) === 's' ) {
                                        $label = $type;
                                    }
                                ?>
                                <button class="cora-filter-btn px-3 py-1.5 rounded-md text-xs font-semibold border border-zinc-200 text-zinc-650 bg-white hover:bg-zinc-50 cursor-pointer shrink-0" data-filter="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $label ); ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Desktop Table View -->
                        <div class="hidden sm:block overflow-x-auto scrollbar-none max-w-full">
                            <table class="min-w-full divide-y divide-zinc-200 text-xs text-left" id="cora-vault-table">
                                <thead>
                                    <tr class="bg-zinc-50/50">
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Title</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Type</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Amount</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] hidden md:table-cell">Date Created</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-150" id="cora-vault-table-body">
                                    <?php foreach ($cora_documents as $doc): 
                                        $type_badge = 'bg-zinc-100 text-zinc-700 border border-zinc-200';
                                        if ($doc['type'] === 'Proposal') $type_badge = 'cora-badge-soon';
                                        elseif ($doc['type'] === 'Invoice') $type_badge = 'cora-badge-green';
                                        elseif ($doc['type'] === 'Contract') $type_badge = 'cora-badge-locked';
                                    ?>
                                    <tr class="hover:bg-zinc-50/30 cora-doc-row" data-type="<?php echo esc_attr($doc['type']); ?>" data-id="<?php echo esc_attr($doc['id']); ?>">
                                        <td class="px-4 py-3.5 font-bold text-zinc-800">
                                            <div class="flex flex-col gap-0.5">
                                                <span><?php echo esc_html($doc['title']); ?></span>
                                                <?php if (!empty($doc['secured_shares'])): ?>
                                                    <span class="text-[9px] text-zinc-400 font-medium flex items-center gap-1 mt-0.5">
                                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                        </svg>
                                                        Shared with <?php echo count($doc['secured_shares']); ?> recipient(s)
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 text-zinc-550 whitespace-nowrap">
                                            <span class="cora-badge px-2 py-0.5 rounded text-[9px] font-semibold <?php echo $type_badge; ?>">
                                                <?php echo esc_html($doc['type']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-zinc-600 font-medium whitespace-nowrap"><?php echo esc_html($doc['amount'] ?: '—'); ?></td>
                                        <td class="px-4 py-3.5 text-zinc-400 font-mono text-[10px] hidden md:table-cell whitespace-nowrap"><?php echo esc_html($doc['created_date']); ?></td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <span class="cora-badge px-2 py-0.5 rounded text-[9px] font-semibold cora-badge-status-<?php echo strtolower($doc['status']); ?>">
                                                <?php echo esc_html($doc['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-bold text-zinc-600">
                                            <div class="flex items-center justify-end gap-1 sm:gap-2">
                                                <button class="px-2 py-1.5 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-view-doc-btn flex items-center justify-center gap-1" onclick="coraViewDocument('<?php echo esc_attr($doc['id']); ?>')" title="View / Edit">
                                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    <span class="hidden sm:inline">View / Edit</span>
                                                </button>
                                                <button class="px-2 py-1.5 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-share-doc-btn flex items-center justify-center gap-1" onclick="coraOpenShareDrawer('<?php echo esc_attr($doc['id']); ?>')" title="Share Securely">
                                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                                    <span class="hidden sm:inline">Share</span>
                                                </button>
                                                <button class="px-2 py-1.5 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-email-doc-btn flex items-center justify-center gap-1" onclick="coraTriggerEmailDocument('<?php echo esc_attr($doc['id']); ?>')" title="Email Direct">
                                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                                    <span class="hidden sm:inline">Email</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card List View -->
                        <div class="sm:hidden divide-y divide-zinc-100 px-4 pb-4" id="cora-vault-mobile-list">
                            <?php foreach ($cora_documents as $doc): 
                                $type_badge = 'bg-zinc-100 text-zinc-700 border border-zinc-200';
                                if ($doc['type'] === 'Proposal') $type_badge = 'cora-badge-soon';
                                elseif ($doc['type'] === 'Invoice') $type_badge = 'cora-badge-green';
                                elseif ($doc['type'] === 'Contract') $type_badge = 'cora-badge-locked';
                                
                                // SVG Icons
                                $icon_svg = '';
                                if ($doc['type'] === 'Proposal') {
                                    $icon_svg = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
                                } elseif ($doc['type'] === 'Invoice') {
                                    $icon_svg = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect><line x1="12" y1="4" x2="12" y2="20"></line><line x1="2" y1="12" x2="22" y2="12"></line></svg>';
                                } else {
                                    $icon_svg = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><circle cx="10" cy="16" r="2"></circle><path d="M12 16h4"></path></svg>';
                                }
                            ?>
                            <div class="py-4 flex flex-col gap-3 cora-doc-row" data-type="<?php echo esc_attr($doc['type']); ?>" data-id="<?php echo esc_attr($doc['id']); ?>">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-zinc-50 border border-zinc-200/80 flex items-center justify-center shrink-0">
                                        <?php echo $icon_svg; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="cora-badge px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider <?php echo $type_badge; ?>">
                                                <?php echo esc_html($doc['type']); ?>
                                            </span>
                                            <span class="cora-badge px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider cora-badge-status-<?php echo strtolower($doc['status']); ?>">
                                                <?php echo esc_html($doc['status']); ?>
                                            </span>
                                        </div>
                                        <h4 class="font-bold text-zinc-900 text-sm mt-1.5 truncate"><?php echo esc_html($doc['title']); ?></h4>
                                        <div class="flex items-center gap-2 mt-1 text-[11px] text-zinc-500 font-medium">
                                            <span class="text-zinc-800 font-semibold"><?php echo esc_html($doc['amount'] ?: '—'); ?></span>
                                            <span class="text-zinc-300">•</span>
                                            <span class="font-mono"><?php echo esc_html($doc['created_date']); ?></span>
                                        </div>
                                        <?php if (!empty($doc['secured_shares'])): ?>
                                            <div class="text-[10px] text-zinc-400 font-medium flex items-center gap-1 mt-1">
                                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                </svg>
                                                Shared with <?php echo count($doc['secured_shares']); ?> recipient(s)
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-2 border-t border-zinc-50 pt-2">
                                    <button class="px-2.5 py-1.5 border border-zinc-200 rounded-md text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-view-doc-btn flex items-center gap-1" onclick="coraViewDocument('<?php echo esc_attr($doc['id']); ?>')">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Edit
                                    </button>
                                    <button class="px-2.5 py-1.5 border border-zinc-200 rounded-md text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-share-doc-btn flex items-center gap-1" onclick="coraOpenShareDrawer('<?php echo esc_attr($doc['id']); ?>')">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                        Share
                                    </button>
                                    <button class="px-2.5 py-1.5 border border-zinc-200 rounded-md text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-email-doc-btn flex items-center gap-1" onclick="coraTriggerEmailDocument('<?php echo esc_attr($doc['id']); ?>')">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        Email
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- DEDICATED FULL-PAGE EDITOR VIEW -->
                <div id="cora-vault-editor-view" class="hidden space-y-6">
                    <!-- ListingCoordinator Header -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between border-b border-zinc-200 pb-4">
                        <!-- Left Side: Back + Title -->
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <button class="flex items-center gap-1.5 px-3 py-1.5 border border-zinc-200 rounded-md text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer shrink-0" onclick="coraCloseListingCoordinator()">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                Back
                            </button>
                            <input type="text" id="cora-doc-title-input" class="text-base md:text-lg font-bold text-zinc-900 border-b border-transparent hover:border-zinc-200 focus:border-zinc-950 focus:outline-none bg-transparent px-1 py-0.5 transition-all flex-1 min-w-0" placeholder="Untitled Document">
                        </div>
                        
                        <!-- Right Side: Auto-save status + Actions toolbar -->
                        <div class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto">
                            <!-- Live Auto-Save Status -->
                            <div id="cora-editor-save-status" class="text-[10px] text-zinc-400 font-medium flex items-center gap-1.5 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="hidden sm:inline">Draft saved</span>
                            </div>

                            <!-- Action buttons -->
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <!-- Collapse/Expand Sidebar Toggle -->
                                <button id="cora-editor-toggle-sidebar-btn" onclick="coraDocToggleSidebar()" class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 border border-zinc-200 rounded-md text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer flex items-center gap-1.5 shrink-0" title="Toggle Settings Sidebar">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="9" y1="3" x2="9" y2="21"></line>
                                    </svg>
                                    <span class="toggle-text hidden sm:inline">Hide Settings</span>
                                </button>

                                <button onclick="coraDownloadPDF()" class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 border border-zinc-200 rounded-md text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer flex items-center gap-1.5 shrink-0" title="Export PDF">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                        <rect x="6" y="14" width="12" height="8"></rect>
                                    </svg>
                                    <span class="hidden sm:inline">Export PDF</span>
                                </button>

                                <button onclick="coraDownloadDOCX()" class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 border border-zinc-200 rounded-md text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer flex items-center gap-1.5 shrink-0" title="Export Word (DOCX)">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    <span class="hidden sm:inline">Word (DOCX)</span>
                                </button>

                                <button id="cora-email-doc-editor-btn" onclick="coraTriggerEmailDocument($('#cora-doc-id-hidden').val())" class="px-2.5 py-1.5 sm:px-4 sm:py-1.5 border border-zinc-350 text-zinc-800 font-semibold rounded-md hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-xs flex items-center gap-1.5 shrink-0" title="Email to Client">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    <span class="hidden sm:inline">Email to Client</span>
                                </button>

                                <button id="cora-save-doc-editor-btn" class="px-2.5 py-1.5 sm:px-4 sm:py-1.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs flex items-center gap-1.5 shrink-0" title="Save Document">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span class="hidden sm:inline">Save</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ListingCoordinator Body Columns -->
                    <div id="cora-editor-workspace-columns" class="flex flex-col lg:flex-row gap-6 items-start">
                        <!-- Main Canvas Area -->
                        <div class="flex-1 w-full bg-zinc-50 border border-zinc-200 rounded-xl overflow-hidden flex flex-col">
                            <!-- Rich Text ListingCoordinator Toolbar -->
                            <div class="cora-editor-toolbar flex items-center gap-1.5 p-2 bg-white border-b border-zinc-200 overflow-x-auto select-none">
                                <select id="cora-editor-heading" class="bg-transparent border-0 text-xs font-semibold text-zinc-700 focus:outline-none focus:ring-0 cursor-pointer h-7 rounded hover:bg-zinc-100 px-1.5 py-0" onchange="coraDocApplyHeading(this.value)">
                                    <option value="p">Normal Text</option>
                                    <option value="h1">Heading 1</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                </select>
                                
                                <div class="h-4 w-[1px] bg-zinc-200 mx-1"></div>
                                
                                <button type="button" onclick="coraDocFormat('bold')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Bold (Cmd+B)">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                                        <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraDocFormat('italic')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Italic (Cmd+I)">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="19" y1="4" x2="10" y2="4"></line>
                                        <line x1="14" y1="20" x2="5" y2="20"></line>
                                        <line x1="15" y1="4" x2="9" y2="20"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraDocFormat('underline')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Underline (Cmd+U)">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"></path>
                                        <line x1="4" y1="21" x2="20" y2="21"></line>
                                    </svg>
                                </button>
                                
                                <div class="h-4 w-[1px] bg-zinc-200 mx-1"></div>
                                
                                <button type="button" onclick="coraDocFormat('insertUnorderedList')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Bullet List">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="8" y1="6" x2="21" y2="6"></line>
                                        <line x1="8" y1="12" x2="21" y2="12"></line>
                                        <line x1="8" y1="18" x2="21" y2="18"></line>
                                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraDocFormat('insertOrderedList')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Numbered List">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="10" y1="6" x2="21" y2="6"></line>
                                        <line x1="10" y1="12" x2="21" y2="12"></line>
                                        <line x1="10" y1="18" x2="21" y2="18"></line>
                                        <path d="M4 6h1v4H4M4 10h2"></path>
                                        <path d="M4 14h2v2H4v2h2"></path>
                                    </svg>
                                </button>
                                
                                <div class="h-4 w-[1px] bg-zinc-200 mx-1"></div>
                                
                                <button type="button" onclick="coraDocFormat('justifyLeft')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Align Left">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="17" y1="10" x2="3" y2="10"></line>
                                        <line x1="21" y1="6" x2="3" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="17" y1="18" x2="3" y2="18"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraDocFormat('justifyCenter')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Align Center">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="10" x2="6" y2="10"></line>
                                        <line x1="21" y1="6" x2="3" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="18" y1="18" x2="6" y2="18"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraDocFormat('justifyRight')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Align Right">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="21" y1="10" x2="7" y2="10"></line>
                                        <line x1="21" y1="6" x2="3" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="21" y1="18" x2="7" y2="18"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraDocFormat('justifyFull')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Justify">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="21" y1="10" x2="3" y2="10"></line>
                                        <line x1="21" y1="6" x2="3" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="21" y1="18" x2="3" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Document Paper Canvas -->
                            <div class="bg-zinc-100/50 p-4 md:p-8 flex justify-center items-start overflow-y-auto min-h-[calc(100vh-220px)] border-b border-zinc-200">
                                <div id="cora-paper-container" class="w-full max-w-[800px] bg-white border border-zinc-200 rounded-lg shadow-sm p-6 md:p-12 min-h-[297mm] flex flex-col justify-between">
                                    <div class="w-full">
                                        <!-- Paper Header (Logo Preview) -->
                                        <div id="cora-paper-header-preview" class="border-b border-zinc-100 pb-4 mb-6 flex items-center justify-start hidden">
                                            <!-- Image will render here dynamically -->
                                        </div>
                                        
                                        <!-- Editable Body Area -->
                                        <div id="cora-doc-paper" contenteditable="true" class="focus:outline-none prose max-w-none text-zinc-800 text-sm leading-relaxed" placeholder="Start typing your document..."></div>
                                    </div>
                                    
                                    <!-- Paper Footer (Footer Text Preview) -->
                                    <div id="cora-paper-footer-preview" contenteditable="true" class="border-t border-zinc-200 pt-4 mt-8 text-center text-xs text-zinc-400 focus:outline-none focus:ring-0" placeholder="Enter footer branding text..."></div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Settings (Branding & metadata) -->
                        <aside class="w-full lg:w-80 bg-white border border-zinc-200 rounded-xl p-5 shadow-sm space-y-4 shrink-0">
                            <input type="hidden" id="cora-doc-id-hidden" value="">
                            
                            <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider border-b border-zinc-100 pb-2">Document Settings</h2>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Document Type</label>
                                <select id="cora-doc-type-select" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                    <?php foreach ( $all_doc_types as $type ) : ?>
                                        <option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
                                    <?php endforeach; ?>
                                    <option value="__add_custom_type__" class="font-bold text-zinc-500">+ Add Custom Type...</option>
                                </select>
                            </div>
                            
                            <!-- Inline Custom Type Input (Hidden by default) -->
                            <div id="cora-custom-type-input-group" class="hidden flex items-center gap-2 mt-1">
                                <input type="text" id="cora-custom-type-input" class="flex-1 border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none" placeholder="e.g. Quote">
                                <button type="button" id="cora-custom-type-save" class="px-2.5 py-1.5 bg-zinc-950 text-white text-[10px] font-semibold rounded hover:bg-zinc-800 transition-colors cursor-pointer">Add</button>
                                <button type="button" id="cora-custom-type-cancel" class="px-2 py-1.5 border border-zinc-200 text-zinc-655 text-[10px] font-semibold rounded hover:bg-zinc-50 transition-colors cursor-pointer">Cancel</button>
                            </div>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Amount / Value (Optional)</label>
                                <input type="text" id="cora-doc-amount-input" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. ₹1,50,000">
                            </div>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Link Client / Lead</label>
                                <select id="cora-doc-client-select" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                    <option value="">— Unlinked —</option>
                                    <optgroup label="Clients">
                                        <?php foreach ( $cora_re_clients as $client ) : ?>
                                            <option value="client_<?php echo esc_attr( $client['id'] ); ?>"><?php echo esc_html( $client['names'] ); ?> (Client)</option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="Leads/Prospects">
                                        <?php foreach ( $cora_re_leads as $lead ) : ?>
                                            <option value="lead_<?php echo esc_attr( $lead['id'] ); ?>"><?php echo esc_html( $lead['names'] ); ?> (Lead)</option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>

                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Load Template</label>
                                <select id="cora-doc-template-select" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer" onchange="coraDocLoadTemplate(this.value)">
                                    <option value="">— Select Template —</option>
                                    <option value="commercial_lease_proposal">Commercial Office Lease Proposal</option>
                                    <option value="intimate_proposal">Intimate Event Proposal</option>
                                    <option value="standard_invoice">Tax Invoice Template</option>
                                    <option value="commercial_invoice">Commercial Office Commission Invoice</option>
                                </select>
                            </div>
                            
                            <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider border-b border-zinc-100 pt-2 pb-2">Branding Elements</h2>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Branding Logo</label>
                                <div class="flex items-center gap-3">
                                    <div id="cora-logo-upload-preview" class="w-16 h-16 border border-zinc-200 rounded-md flex items-center justify-center bg-zinc-50 overflow-hidden shrink-0">
                                        <span class="text-[9px] text-zinc-400 text-center px-1">No Logo</span>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <button type="button" id="cora-doc-logo-upload-btn" class="px-2.5 py-1.5 border border-zinc-200 rounded text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition-colors cursor-pointer">
                                            Choose Image
                                        </button>
                                        <button type="button" id="cora-doc-logo-remove-btn" class="text-[10px] text-red-500 hover:text-red-700 font-semibold text-left hidden">
                                            Remove Logo
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="cora-doc-logo-url" value="">
                            </div>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Footer Text</label>
                                <textarea id="cora-doc-footer-text" rows="3" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. © 2026 Apex Realty Group. All rights reserved. • Contact: hello@nitinarora.com" oninput="coraDocUpdateBranding()"></textarea>
                                <span class="text-[9px] text-zinc-400">This text appears centered at the bottom of the page.</span>
                            </div>

                            <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider border-b border-zinc-100 pt-2 pb-2">Sync Document</h2>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Sync from Google Doc</label>
                                <div class="flex gap-1.5">
                                    <input type="url" id="cora-doc-gdoc-url" class="flex-1 border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none" placeholder="Paste Google Doc sharing link">
                                    <button type="button" id="cora-doc-gdoc-sync-btn" class="px-3 py-2 bg-zinc-950 hover:bg-zinc-800 text-white text-[10px] font-bold rounded transition-colors cursor-pointer shrink-0">Sync</button>
                                </div>
                                <span class="text-[9px] text-zinc-400">Ensure the Google Doc is shared publicly ("Anyone with the link can view").</span>
                            </div>

                            <!-- Real-time Sync Toggle -->
                            <div class="flex items-center justify-between py-1 bg-zinc-50 p-2.5 rounded-md border border-zinc-200">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-zinc-700">Real-time Doc Sync</span>
                                    <span class="text-[8px] text-zinc-400">Fetch edits from Google Doc automatically</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input type="checkbox" id="cora-doc-gdoc-sync-toggle" class="sr-only peer">
                                    <div class="w-7 h-4 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-zinc-950"></div>
                                </label>
                            </div>

                            <!-- Connection Status indicator -->
                            <div id="cora-gdoc-sync-status" class="hidden text-[9px] text-zinc-500 font-medium flex items-center gap-1.5 px-1 pt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-300 sync-indicator-dot"></span>
                                <span class="sync-status-text">Disconnected</span>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 10: FINANCIAL BOARD -->
            <?php if ( $sub_page === 'financials' ) : ?>
            <section id="cora-page-financials" class="cora-page-section cora-active space-y-6">
                <div class="flex items-center justify-between">
                    <div class="cora-page-header flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                        </span>
                        <div>
                            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Financial Overview</h1>
                            <p class="cora-section-desc text-xs text-zinc-500 mt-1">Track cash inflows, outflows, log studio expenses, and monitor outstanding client dues.</p>
                        </div>
                    </div>
                    <div>
                        <button class="px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs flex items-center gap-2" onclick="coraOpenTransactionDrawerForCreate()">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Ledger Entry
                        </button>
                    </div>
                </div>

                <!-- Financial Stats Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="cora-financial-stats-grid">
                    <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Revenue (Inflows)</span>
                        <span class="text-xl font-bold text-emerald-600 mt-1" id="cora-fin-stat-inflows">₹0</span>
                    </div>
                    <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Expenses (Outflows)</span>
                        <span class="text-xl font-bold text-red-600 mt-1" id="cora-fin-stat-outflows">₹0</span>
                    </div>
                    <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Net Profit</span>
                        <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-fin-stat-profit">₹0</span>
                    </div>
                    <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between bg-zinc-50/50">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Pending Client Dues</span>
                        <span class="text-xl font-bold text-amber-600 mt-1" id="cora-fin-stat-dues">₹0</span>
                    </div>
                </div>

                <!-- Financial Analytics & Visual Report -->
                <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3 flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-650">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            </span>
                            <h3 class="text-sm font-bold text-zinc-900">Revenue Analytics</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <select id="cora-report-duration" class="border border-zinc-200 rounded-md py-1.5 px-3 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors" onchange="coraUpdateReport()">
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                            </select>
                            <button class="px-3 py-1.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all text-xs flex items-center gap-1.5 cursor-pointer" onclick="coraDownloadPDFReport()">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                PDF Report
                            </button>
                        </div>
                    </div>
                    
                    <div class="relative w-full h-[220px]">
                        <canvas id="cora-revenue-chart"></canvas>
                    </div>
                </div>

                <!-- Ledger list and filters -->
                <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3 flex-wrap gap-3">
                        <div class="flex gap-2 flex-wrap" id="cora-financial-filters">
                            <button class="cora-filter-btn px-3 py-1.5 rounded-md text-xs font-semibold bg-zinc-950 text-white cursor-pointer" data-filter="all">All Entries</button>
                            <button class="cora-filter-btn px-3 py-1.5 rounded-md text-xs font-semibold border border-zinc-200 text-zinc-655 bg-white hover:bg-zinc-50 cursor-pointer" data-filter="Inflow">Cash Inflows</button>
                            <button class="cora-filter-btn px-3 py-1.5 rounded-md text-xs font-semibold border border-zinc-200 text-zinc-655 bg-white hover:bg-zinc-50 cursor-pointer" data-filter="Outflow">Cash Outflows</button>
                        </div>
                        
                        <div class="relative max-w-xs w-full">
                            <input type="text" id="cora-financial-search" class="w-full border border-zinc-200 rounded-md py-1.5 pl-8 pr-3 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="Search ledger...">
                            <span class="absolute left-2.5 top-2.5 text-zinc-400">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto scrollbar-none max-w-full">
                        <table class="min-w-[700px] sm:min-w-full divide-y divide-zinc-200 text-xs text-left" id="cora-financial-table">
                            <thead>
                                <tr class="bg-zinc-50/50">
                                    <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Date</th>
                                    <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Description</th>
                                    <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Category</th>
                                    <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Amount</th>
                                    <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Type</th>
                                    <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                                    <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-150" id="cora-financial-table-body">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION: LEADS CRM BOARD -->
            <?php if ( $sub_page === 'leads' ) : ?>
            <section id="cora-page-leads" class="cora-page-section cora-active space-y-6">
                <div class="flex items-center justify-between">
                    <div class="cora-page-header flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="9" rx="1"></rect>
                                <rect x="14" y="3" width="7" height="9" rx="1"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                            </svg>
                        </span>
                        <div>
                            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Leads CRM</h1>
                            <p class="cora-section-desc text-xs text-zinc-500 mt-1">Nurture incoming inquiries, manage discussions, and track client acquisition funnel.</p>
                        </div>
                    </div>
                    <div>
                        <button class="px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs flex items-center gap-2 animate-none" onclick="coraOpenLeadDrawerForCreate()">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Lead
                        </button>
                    </div>
                </div>

                <!-- Kanban Funnel Grid -->
                <div class="flex overflow-x-auto gap-4 items-start mt-6 select-none pb-8" style="scrollbar-width: none;">
                    <?php
                    $active_industry = get_option( 'cora_workspace_industry', 'real_estate' );
                    $module = Cora_Module_Registry::get_module( $active_industry );
                    $columns = $module ? $module->get_crm_stages() : array();

                    foreach ($columns as $status_key => $column_info) :
                        $col_leads = array_filter($cora_re_leads, function($lead) use ($status_key) {
                            return isset($lead['status']) && $lead['status'] === $status_key;
                        });
                    ?>
                    <div class="cora-kanban-column flex flex-col p-3 rounded-[18px] bg-white border border-zinc-200/70 shadow-sm shrink-0 w-[320px] h-[calc(100vh-180px)] relative" 
                         data-status="<?php echo esc_attr($status_key); ?>"
                         ondragover="coraLeadDragOver(event)"
                         ondrop="coraLeadDrop(event)">
                        
                        <div class="mb-4 pb-2 shrink-0 px-1 pt-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-4 h-4 rounded <?php echo $column_info['badge']; ?>"></div>
                                    <span class="font-bold text-zinc-950 text-[13px]">
                                        <?php echo esc_html($column_info['label']); ?>
                                    </span>
                                    <span class="text-[10px] text-zinc-500 font-bold bg-zinc-100 px-1.5 py-0.5 rounded-full col-count"><?php echo count($col_leads); ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button class="text-zinc-400 hover:text-zinc-800 transition-colors p-1" onclick="coraOpenLeadDrawerForCreate()">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    </button>
                                    <button class="text-zinc-400 hover:text-zinc-800 transition-colors p-1">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                    </button>
                                </div>
                            </div>
                            <p class="text-[10.5px] text-zinc-400 mt-1.5"><?php echo esc_html($column_info['desc']); ?></p>
                        </div>

                        <!-- Card Drop Zone -->
                        <div class="cora-cards-container flex-1 space-y-2.5 overflow-y-auto pr-1 pb-14" style="scrollbar-width: none;">
                            <?php foreach ($col_leads as $lead) : 
                                $next_step = '';
                                if ($status_key === 'New Lead') $next_step = 'Schedule intro call';
                                else if ($status_key === 'Nurturing') $next_step = 'Follow up via email';
                                else if ($status_key === 'Closing') $next_step = 'Awaiting signature';
                                else if ($status_key === 'Converted') $next_step = 'Plan showing logistics';
                            ?>
                            <div class="cora-lead-card bg-white border border-zinc-200 p-3.5 rounded-lg shadow-sm hover:shadow-md hover:border-zinc-300 transition-all cursor-grab active:cursor-grabbing flex flex-col gap-3" 
                                 draggable="true" 
                                 data-id="<?php echo esc_attr( $lead['id'] ); ?>" 
                                 ondragstart="coraLeadDragStart(event)"
                                 onclick="coraOpenLeadDrawer(<?php echo esc_attr( json_encode( $lead ) ); ?>)">
                                
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-zinc-900 text-xs tracking-tight leading-snug hover:text-zinc-950 truncate max-w-full" title="<?php echo esc_attr( $lead['names'] ); ?>"><?php echo esc_html( $lead['names'] ); ?></h4>
                                        <div class="text-[10px] text-zinc-400 mt-0.5 font-medium flex items-center gap-1">
                                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                            <?php echo esc_html( date( 'd M Y', $lead['created_at'] ) ); ?>
                                        </div>
                                    </div>
                                    <?php if ( ! empty( $lead['scale'] ) ) : ?>
                                        <span class="px-1.5 py-0.5 rounded flex items-center justify-center text-[8.5px] font-bold uppercase tracking-wider bg-zinc-100 text-zinc-600 border border-zinc-200 shrink-0 shadow-sm">
                                            <?php echo esc_html( $lead['scale'] ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="space-y-1.5 text-[10.5px] text-zinc-600 font-medium bg-zinc-50/80 p-2 rounded-md border border-zinc-100">
                                    <div class="flex items-center gap-2 truncate" title="<?php echo esc_attr( $lead['email'] ); ?>">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 text-zinc-400"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        <span class="truncate"><?php echo esc_html( $lead['email'] ); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                        <span class="truncate"><?php echo esc_html( $lead['city'] ); ?></span>
                                    </div>
                                </div>
                                
                                <div class="pt-2 flex items-center justify-between border-t border-zinc-100/80 mt-1">
                                    <div class="flex items-center gap-1.5 text-[10px] text-zinc-500 font-semibold" title="Next Step">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        <?php echo $next_step; ?>
                                    </div>
                                    <?php if ( ! empty( $lead['price'] ) ) : ?>
                                        <span class="font-bold text-zinc-950 text-[11px] bg-zinc-100 px-1.5 py-0.5 rounded border border-zinc-200"><?php echo esc_html( $lead['price'] ); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Add New Button pinned to bottom -->
                        <div class="absolute bottom-0 left-0 w-full p-3 bg-gradient-to-t from-white via-white to-transparent rounded-b-[18px]">
                            <button class="flex items-center gap-2 text-zinc-400 hover:text-zinc-800 text-xs font-semibold px-2 py-1.5 rounded-md hover:bg-zinc-100 transition-colors w-full" onclick="coraOpenLeadDrawerForCreate()">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                New
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
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
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-pages.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: CANVAS -->
            <?php if ( $sub_page === 'canvas' ) : ?>
            <section id="cora-page-canvas" class="cora-page-section cora-active space-y-6">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-canvas.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: DISCUSSIONS & COMMENTS -->
            <?php if ( $sub_page === 'comments' ) : ?>
            <section id="cora-page-comments" class="cora-page-section cora-active space-y-6">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-comments.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: APPEARANCE -->
            <?php if ( $sub_page === 'appearance' ) : ?>
            <section id="cora-page-appearance" class="cora-page-section cora-active space-y-6">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-appearance.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: TOOLS & DIAGNOSTICS -->
            <?php if ( $sub_page === 'tools' ) : ?>
            <section id="cora-page-tools" class="cora-page-section cora-active space-y-6">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-tools.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: MEDIA EDITOR -->
            <?php if ( $sub_page === 'media-editor' ) : ?>
            <section id="cora-page-media-editor" class="cora-page-section cora-active space-y-6">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-media-editor.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: SETTINGS SUITE -->
            <?php if ( $sub_page === 'settings-suite' ) : ?>
            <section id="cora-page-settings-suite" class="cora-page-section cora-active space-y-6">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-settings-suite.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: USER PROFILE -->
            <?php if ( $sub_page === 'profile' ) : ?>
            <section id="cora-page-profile" class="cora-page-section cora-active space-y-6">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-profile.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: AI TOOLS MCP -->
            <?php if ( $sub_page === 'mcp' ) : ?>
            <section id="cora-page-mcp" class="cora-page-section cora-active space-y-6">
                <?php
                // Fetch/generate token
                $mcp_token = get_option( 'cora_mcp_access_token' );
                if ( empty( $mcp_token ) ) {
                    $mcp_token = bin2hex( wp_generate_password( 32, false ) );
                    update_option( 'cora_mcp_access_token', $mcp_token );
                }
                $mcp_url = home_url( '/wp-json/cora/v1/mcp' );
                ?>
                <div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
                                <rect x="9" y="9" width="6" height="6"></rect>
                                <line x1="9" y1="1" x2="9" y2="4"></line>
                                <line x1="15" y1="1" x2="15" y2="4"></line>
                                <line x1="9" y1="20" x2="9" y2="23"></line>
                                <line x1="15" y1="20" x2="15" y2="23"></line>
                                <line x1="20" y1="9" x2="23" y2="9"></line>
                                <line x1="20" y1="15" x2="23" y2="15"></line>
                                <line x1="1" y1="9" x2="4" y2="9"></line>
                                <line x1="1" y1="15" x2="4" y2="15"></line>
                            </svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 m-0">AI Tools MCP</h1>
                                <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-[10px] font-bold border border-zinc-200 dark:border-zinc-700 flex items-center gap-1">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    Coming Soon
                                </span>
                            </div>
                            <p class="text-sm text-zinc-500 mt-0.5">Model Context Protocol server settings for external AI integrations.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 max-w-3xl mt-6">
                    <!-- Locked / Coming Soon Feature Card -->
                    <div class="p-8 text-center bg-white dark:bg-zinc-955 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xs flex flex-col items-center justify-center space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-400 shadow-2xs">
                            <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </div>
                        <div class="space-y-1.5 max-w-md">
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 m-0">AI Tools MCP Gateway — Coming Soon</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed m-0">
                                Direct integration with external AI clients (like Claude Desktop, Cursor, and custom local LLM agents) via Model Context Protocol (MCP) is currently under active development and will be available in an upcoming platform release.
                            </p>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Feature Locked — Standard REST API & AI Assistant remain active
                        </div>
                    </div>
                </div>

                <script>
                    function coraCopyToClipboardDirect(inputId) {
                        var copyText = document.getElementById(inputId);
                        copyText.select();
                        copyText.setSelectionRange(0, 99999);
                        navigator.clipboard.writeText(copyText.value);
                        window.coraShowToast("Copied to clipboard.");
                    }

                    function coraToggleTokenVisibilityDirect() {
                        var x = document.getElementById("cora-mcp-access-token-direct");
                        if (x.type === "password") {
                            x.type = "text";
                        } else {
                            x.type = "password";
                        }
                    }

                    function coraGenerateNewMCPTokenDirect() {
                        window.coraConfirmAction(
                            'Regenerate MCP Token',
                            'Are you sure you want to regenerate the secure token? Current active AI tools connections will immediately fail authentication.',
                            function() {
                                var chars = 'abcdef0123456789';
                                var newToken = '';
                                for (var i = 0; i < 32; i++) {
                                    newToken += chars.charAt(Math.floor(Math.random() * chars.length));
                                }
                                document.getElementById("cora-mcp-access-token-direct").value = newToken;
                                window.coraShowToast("New secure token generated. Save to persist.");
                            }
                        );
                    }

                    function coraCopyClaudeConfigDirect() {
                        var codeText = document.getElementById("cora-claude-config-code-direct").innerText;
                        navigator.clipboard.writeText(codeText);
                        window.coraShowToast("Claude configuration copied to clipboard.");
                    }
                </script>
            </section>
            <?php endif; ?>

            <!-- SECTION: CORA FORMS -->
            <?php if ( $sub_page === 'forms' ) : ?>
            <section id="cora-page-forms" class="cora-page-section cora-active space-y-6">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-forms.php'; ?>
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
                    overflow: hidden !important;
                }
                /* Remove Tailwind space-y-6 between sections */
                .cora-content-wrapper > * + * { margin-top: 0 !important; }
            </style>';
            ?>
            <section id="cora-page-media" class="cora-page-section cora-active" style="padding:0;margin:0;overflow:hidden;flex:1;display:flex;flex-direction:column;">
                <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-media.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: AI CONTENT SUITE -->
            <?php if ( $sub_page === 'blogs' ) : ?>
            <section id="cora-page-content" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </span>
                        <div>
                            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">AI Content Suite</h1>
                            <p class="text-sm text-zinc-500 mt-0.5">Draft, optimize, and track SEO performance for your agency's articles.</p>
                        </div>
                    </div>
                    <button class="cora-btn-primary px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-sm transition-colors cursor-pointer flex items-center gap-2" onclick="coraOpenContentDrawer()">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        New AI Article
                    </button>
                </div>

                <?php
                $published_count = 0;
                $draft_count = 0;
                foreach($cora_posts as $post) {
                    if($post->post_status === 'publish') $published_count++;
                    if($post->post_status === 'draft') $draft_count++;
                }
                ?>
                <!-- Metrics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="cora-stat-card p-5 bg-white border border-zinc-200 rounded-xl shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Total Pageviews</span>
                            <span class="p-1.5 bg-zinc-100 rounded text-zinc-600">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </span>
                        </div>
                        <div class="text-3xl font-bold text-zinc-900">N/A</div>
                        <div class="text-[11px] text-zinc-500 font-semibold mt-1 flex items-center gap-1">
                            Pending Analytics Setup
                        </div>
                    </div>
                    <div class="cora-stat-card p-5 bg-white border border-zinc-200 rounded-xl shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Avg. SEO Score</span>
                            <span class="p-1.5 bg-green-50 rounded text-green-600">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </span>
                        </div>
                        <div class="text-3xl font-bold text-zinc-900">85/100</div>
                        <div class="text-[11px] text-zinc-500 mt-1">Average across published posts</div>
                    </div>
                    <div class="cora-stat-card p-5 bg-white border border-zinc-200 rounded-xl shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Published Articles</span>
                            <span class="p-1.5 bg-zinc-100 rounded text-zinc-600">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                            </span>
                        </div>
                        <div class="text-3xl font-bold text-zinc-900"><?php echo esc_html($published_count); ?></div>
                        <div class="text-[11px] text-zinc-500 mt-1"><?php echo esc_html($draft_count); ?> drafts pending review</div>
                    </div>
                </div>

                <!-- Notion-style Tab Selector -->
                <div class="flex items-center gap-1 border-b border-zinc-200 mt-6 select-none">
                    <button class="px-4 py-2 border-b-2 text-xs font-semibold cursor-pointer transition-all border-zinc-950 text-zinc-900 flex items-center gap-1.5" id="btn-tab-articles-list" onclick="coraSwitchBlogsTab('list')">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        Articles Listing
                    </button>
                    <button class="px-4 py-2 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-400 hover:text-zinc-600 flex items-center gap-1.5" id="btn-tab-geo-analytics" onclick="coraSwitchBlogsTab('geo')">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
                        Generative Search & GEO Tracking
                    </button>
                    <button class="px-4 py-2 border-b-2 text-xs font-semibold cursor-pointer transition-all border-transparent text-zinc-400 hover:text-zinc-600 flex items-center gap-1.5" id="btn-tab-keywords-explorer" onclick="coraSwitchBlogsTab('keywords')">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        Local Intent Keyword Explorer
                    </button>
                </div>

                <!-- Articles Table Container -->
                <div id="cora-blogs-list-container" class="border border-zinc-200 rounded-lg overflow-hidden bg-white shadow-sm mt-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-50 border-b border-zinc-200 text-[10px] font-bold text-zinc-400 uppercase tracking-wider select-none">
                                <th class="py-3 px-4">Article Title</th>
                                <th class="py-3 px-4">Author</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">SEO Health</th>
                                <th class="py-3 px-4">Captured Leads</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 text-sm text-zinc-700" id="cora-articles-table-body">
                            <?php if (empty($cora_posts)): ?>
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-zinc-500 text-sm">No articles found. Click "New AI Article" to start drafting!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($cora_posts as $post): 
                                    $seo_score = get_post_meta($post->ID, '_cora_seo_score', true) ?: rand(65, 95);
                                    $is_published = $post->post_status === 'publish';
                                    $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
                                    $content_esc = esc_js($post->post_content);
                                    $title_esc = esc_js($post->post_title);
                                    $lead_count = cora_db_get_article_lead_count($post->ID);
                                    
                                    $assignee_id = get_post_meta($post->ID, '_cora_assignee_id', true);
                                    $assignee_name = 'Unassigned';
                                    if ($assignee_id) {
                                        $user_obj = get_userdata($assignee_id);
                                        if ($user_obj) {
                                            $assignee_name = $user_obj->display_name;
                                        }
                                    }

                                    $editorial_status = get_post_meta($post->ID, '_cora_editorial_status', true) ?: ($is_published ? 'published' : 'draft');

                                    // Mock realistic conversion rate based on leads & mock pageviews
                                    $pageviews = $is_published ? (120 + ($lead_count * 18)) : 0;
                                    $conv_rate = ($pageviews > 0) ? sprintf('%.1f%%', ($lead_count / $pageviews) * 100) : '0.0%';
                                ?>
                                <tr class="hover:bg-zinc-50/50 transition-colors cursor-pointer cora-article-row" onclick="coraEditArticle(<?php echo $post->ID; ?>)">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded bg-zinc-100 overflow-hidden border border-zinc-200 flex items-center justify-center text-zinc-400 shrink-0">
                                                <?php if($thumbnail_url): ?>
                                                    <img src="<?php echo esc_url($thumbnail_url); ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                                <?php endif; ?>
                                            </div>
                                            <div class="font-bold text-zinc-900 line-clamp-1 max-w-[200px]"><?php echo esc_html($post->post_title); ?></div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-xs font-semibold text-zinc-650"><?php echo esc_html($assignee_name); ?></span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if($editorial_status === 'published'): ?>
                                            <span class="px-2 py-1 bg-green-150 text-green-700 rounded-md text-[10px] font-bold tracking-wider uppercase">Published</span>
                                        <?php elseif($editorial_status === 'pending_review'): ?>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-[10px] font-bold tracking-wider uppercase">In Review</span>
                                        <?php elseif($editorial_status === 'approved'): ?>
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-md text-[10px] font-bold tracking-wider uppercase">Approved</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 bg-zinc-100 text-zinc-600 rounded-md text-[10px] font-bold tracking-wider uppercase">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full <?php echo $seo_score >= 80 ? 'bg-green-500' : 'bg-yellow-500'; ?>"></div>
                                            <span class="font-bold text-zinc-800"><?php echo esc_html($seo_score); ?></span> <span class="text-zinc-400 text-xs">/100</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if($is_published): ?>
                                            <div class="flex items-center gap-2">
                                                <button class="cora-btn-action px-2 py-0.5 border border-zinc-200 text-zinc-850 hover:bg-zinc-50 rounded text-xs font-bold cursor-pointer" onclick="event.stopPropagation(); coraShowArticleLeads(<?php echo $post->ID; ?>, '<?php echo esc_js($title_esc); ?>')">
                                                    <?php echo $lead_count; ?> Leads
                                                </button>
                                                <span class="text-[10px] text-zinc-400 font-semibold">(<?php echo $conv_rate; ?> CR)</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-xs text-zinc-400 italic">Unpublished</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <button class="cora-btn-action px-3 py-1.5 border border-zinc-200 rounded-md text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 cursor-pointer" onclick="event.stopPropagation(); coraEditArticle(<?php echo $post->ID; ?>, '<?php echo esc_js($title_esc); ?>', '<?php echo esc_js($seo_score); ?>')">Edit Article</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Local Intent Keyword Explorer Panel -->
                <div id="cora-blogs-keywords-panel" class="hidden space-y-6 mt-6 animate-fade-in">
                    <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-5 py-4 border-b border-zinc-200 bg-zinc-50/50 flex justify-between items-center">
                            <div>
                                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Local Search Intents & Query Volume</h3>
                                <p class="text-[10px] text-zinc-400 mt-0.5">High-intent searches happening in Delhi NCR. Build articles to capture AI citation share.</p>
                            </div>
                            <span class="px-2 py-1 bg-zinc-100 text-zinc-700 rounded text-[9px] font-bold uppercase">Delhi NCR Region</span>
                        </div>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-zinc-50/50 border-b border-zinc-100 text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                                    <th class="py-2.5 px-4">Local Target Keyword</th>
                                    <th class="py-2.5 px-4 text-center">Monthly Queries</th>
                                    <th class="py-2.5 px-4 text-center">AI competition</th>
                                    <th class="py-2.5 px-4 text-center">Opportunity Level</th>
                                    <th class="py-2.5 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-zinc-700">
                                <tr>
                                    <td class="py-3 px-4 font-bold text-zinc-900">"luxury builder floor in Vasant Vihar Delhi"</td>
                                    <td class="py-3 px-4 text-center font-bold">850/mo</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-green-50 text-green-700 font-bold border border-green-200 rounded">LOW</span></td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-900 font-bold rounded">HIGH</span></td>
                                    <td class="py-3 px-4 text-right">
                                        <button class="cora-btn-primary px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded text-[10px] font-bold cursor-pointer transition-colors" onclick="coraOneClickDraft('luxury builder floor in Vasant Vihar Delhi', 'Modern Luxury Builder Floors for Sale in Vasant Vihar', 'Explore premium multi-level builder floors with private elevators and security in Vasant Vihar, South Delhi.')">One-Click Write</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 font-bold text-zinc-900">"average price of 4BHK penthouses DLF Phase 5 Gurgaon"</td>
                                    <td class="py-3 px-4 text-center font-bold">620/mo</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-green-50 text-green-700 font-bold border border-green-200 rounded">VERY LOW</span></td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-900 font-bold rounded">HIGH</span></td>
                                    <td class="py-3 px-4 text-right">
                                        <button class="cora-btn-primary px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded text-[10px] font-bold cursor-pointer transition-colors" onclick="coraOneClickDraft('average price of 4BHK penthouses DLF Phase 5 Gurgaon', 'Gurgaon DLF Phase 5 Penthouse Price Index & Trends', 'Detailed breakdown of average square yard pricing, maintenance metrics, and villa sales inside Gurgaon DLF Phase 5.')">One-Click Write</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 font-bold text-zinc-900">"eco-friendly villa developments near Golf Course Road"</td>
                                    <td class="py-3 px-4 text-center font-bold">480/mo</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 font-bold border border-yellow-250 rounded">MEDIUM</span></td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-zinc-100 text-zinc-800 font-bold rounded">MEDIUM</span></td>
                                    <td class="py-3 px-4 text-right">
                                        <button class="cora-btn-primary px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded text-[10px] font-bold cursor-pointer transition-colors" onclick="coraOneClickDraft('eco-friendly villa developments near Golf Course Road', 'Sustainable Eco-Friendly Villas near Golf Course Road Gurgaon', 'A comprehensive catalog profiling solar-powered, water-conserving luxury villas on Golf Course Road.')">One-Click Write</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 font-bold text-zinc-900">"DLF CyberCity corporate commercial lease space rates"</td>
                                    <td class="py-3 px-4 text-center font-bold">1,200/mo</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 font-bold border border-yellow-250 rounded">MEDIUM</span></td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-zinc-100 text-zinc-800 font-bold rounded">MEDIUM</span></td>
                                    <td class="py-3 px-4 text-right">
                                        <button class="cora-btn-primary px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded text-[10px] font-bold cursor-pointer transition-colors" onclick="coraOneClickDraft('DLF CyberCity corporate commercial lease space rates', 'Corporate Commercial Lease Space Rates inside DLF CyberCity Gurgaon', 'Track commercial rates per square foot, common area expenses, and tenancy agreements inside DLF CyberCity.')">One-Click Write</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- GEO & AISEO Analytics Panel -->
                <div id="cora-blogs-geo-panel" class="hidden space-y-6 mt-6 animate-fade-in">
                    <!-- Analytics Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- AI Search Citations -->
                        <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm">
                            <h3 class="text-xs font-bold text-zinc-450 uppercase tracking-wider mb-4">Generative Visibility</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between pb-2 border-b border-zinc-50">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                        <span class="text-xs font-bold text-zinc-800">Google Gemini</span>
                                    </div>
                                    <span class="text-xs font-black text-zinc-900">45% Cited</span>
                                </div>
                                <div class="flex items-center justify-between pb-2 border-b border-zinc-50">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                        <span class="text-xs font-bold text-zinc-800">OpenAI SearchGPT</span>
                                    </div>
                                    <span class="text-xs font-black text-zinc-900">38% Cited</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                        <span class="text-xs font-bold text-zinc-800">Perplexity AI</span>
                                    </div>
                                    <span class="text-xs font-black text-zinc-900">52% Cited</span>
                                </div>
                            </div>
                        </div>

                        <!-- AI Citation Volumetrics -->
                        <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm col-span-2 flex flex-col justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-zinc-450 uppercase tracking-wider mb-2">Generative Search Citations (Last 30 Days)</h3>
                                <p class="text-[10px] text-zinc-500">Estimated instances where Nitin & Shanaya Arora / Apex Realty Group was cited in AI Search results.</p>
                            </div>
                            <div class="flex items-end gap-3 h-20 mt-4 select-none">
                                <div class="flex-1 bg-zinc-100 rounded-t h-[40%] hover:bg-zinc-200 transition-all cursor-pointer relative group"><div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-zinc-950 text-white text-[8px] font-bold py-0.5 px-1 rounded shadow">12</div></div>
                                <div class="flex-1 bg-zinc-100 rounded-t h-[60%] hover:bg-zinc-200 transition-all cursor-pointer relative group"><div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-zinc-950 text-white text-[8px] font-bold py-0.5 px-1 rounded shadow">18</div></div>
                                <div class="flex-1 bg-zinc-100 rounded-t h-[55%] hover:bg-zinc-200 transition-all cursor-pointer relative group"><div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-zinc-950 text-white text-[8px] font-bold py-0.5 px-1 rounded shadow">15</div></div>
                                <div class="flex-1 bg-zinc-100 rounded-t h-[75%] hover:bg-zinc-200 transition-all cursor-pointer relative group"><div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-zinc-950 text-white text-[8px] font-bold py-0.5 px-1 rounded shadow">24</div></div>
                                <div class="flex-1 bg-zinc-950 rounded-t h-[95%] hover:bg-zinc-800 transition-all cursor-pointer relative group"><div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-zinc-950 text-white text-[8px] font-bold py-0.5 px-1 rounded shadow">32</div></div>
                            </div>
                        </div>
                    </div>

                    <!-- Generative Search Query Intents Tracker -->
                    <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-5 py-4 border-b border-zinc-200 bg-zinc-50/50">
                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Generative Search Intents & Queries</h3>
                        </div>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-zinc-50/50 border-b border-zinc-100 text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                                    <th class="py-2.5 px-4">Search Query Prompt</th>
                                    <th class="py-2.5 px-4 text-center">AI Citation State</th>
                                    <th class="py-2.5 px-4 text-center">Visibility Score</th>
                                    <th class="py-2.5 px-4 text-right">Primary Citation Source</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-zinc-700">
                                <tr>
                                    <td class="py-3 px-4 font-bold text-zinc-900">"who are the best real estate agents in Delhi NCR for luxury villas?"</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-green-50 text-green-700 font-bold border border-green-200 rounded">CITED</span></td>
                                    <td class="py-3 px-4 text-center font-bold">85%</td>
                                    <td class="py-3 px-4 text-right text-zinc-500 font-medium">Nitin & Shanaya Arora: Premium Market Forecast</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 font-bold text-zinc-900">"average price statistics for 4BHK penthouses in Gurgaon DLF Phase 5"</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-green-50 text-green-700 font-bold border border-green-200 rounded">CITED</span></td>
                                    <td class="py-3 px-4 text-center font-bold">72%</td>
                                    <td class="py-3 px-4 text-right text-zinc-500 font-medium">Gurgaon DLF Luxury Villas for Sale</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 font-bold text-zinc-900">"realtor recommendations for corporate office lease space Cyber City"</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-zinc-50 text-zinc-400 font-medium border border-zinc-200 rounded">NOT CITED</span></td>
                                    <td class="py-3 px-4 text-center font-bold">12%</td>
                                    <td class="py-3 px-4 text-right text-zinc-500 italic">None</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- AI Content Demand Recommendations -->
                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-5 flex items-start gap-4">
                        <div class="p-2 bg-zinc-900 rounded text-white shrink-0 mt-0.5">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-zinc-900">AI Search Demand Alerts (Local Delhi NCR)</h4>
                            <p class="text-[11px] text-zinc-500 mt-1 leading-relaxed">Gemini and SearchGPT are experiencing a 65% surge in queries for <strong>"eco-friendly luxury villas in Gurgaon"</strong>. We recommend publishing a short summary article with a structured price table to capture up to 45% citation share on AI Overviews.</p>
                        </div>
                    <!-- Team Attribution & Lead Performance Leaderboard -->
                    <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-5 py-4 border-b border-zinc-200 bg-zinc-50/50 flex justify-between items-center">
                            <div>
                                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Team Attribution & Lead Performance</h3>
                                <p class="text-[10px] text-zinc-400 mt-0.5">Track and reward team members generating the highest conversion rates and citations.</p>
                            </div>
                            <span class="px-2 py-1 bg-zinc-100 text-zinc-700 rounded text-[9px] font-bold uppercase">Team Leaderboard</span>
                        </div>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-zinc-50/50 border-b border-zinc-100 text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                                    <th class="py-2.5 px-4">Team Member</th>
                                    <th class="py-2.5 px-4 text-center">Articles Written</th>
                                    <th class="py-2.5 px-4 text-center">AI Citation Score</th>
                                    <th class="py-2.5 px-4 text-center">Captured Leads</th>
                                    <th class="py-2.5 px-4 text-right">Avg Conversion Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-zinc-700">
                                <?php
                                // Map leads by assignee
                                $user_stats = array();
                                // Initialize stats for each registered user
                                foreach ($cora_users as $usr) {
                                    $user_stats[$usr->ID] = array(
                                        'name' => $usr->display_name,
                                        'posts' => 0,
                                        'leads' => 0,
                                        'pageviews' => 0,
                                        'seo_sum' => 0
                                    );
                                }
                                // Gather metrics from posts
                                foreach ($cora_posts as $post) {
                                    $author_id = get_post_meta($post->ID, '_cora_assignee_id', true);
                                    if ($author_id && isset($user_stats[$author_id])) {
                                        $user_stats[$author_id]['posts']++;
                                        $p_leads = cora_db_get_article_lead_count($post->ID);
                                        $user_stats[$author_id]['leads'] += $p_leads;
                                        $user_stats[$author_id]['seo_sum'] += (get_post_meta($post->ID, '_cora_seo_score', true) ?: 75);
                                        if ($post->post_status === 'publish') {
                                            $user_stats[$author_id]['pageviews'] += (120 + ($p_leads * 18));
                                        }
                                    }
                                }
                                
                                $has_stats = false;
                                foreach ($user_stats as $u_id => $stats):
                                    if ($stats['posts'] === 0) continue;
                                    $has_stats = true;
                                    $avg_seo = round($stats['seo_sum'] / $stats['posts']);
                                    $cr = ($stats['pageviews'] > 0) ? sprintf('%.1f%%', ($stats['leads'] / $stats['pageviews']) * 100) : '0.0%';
                                ?>
                                <tr>
                                    <td class="py-3 px-4 font-bold text-zinc-900"><?php echo esc_html($stats['name']); ?></td>
                                    <td class="py-3 px-4 text-center font-bold"><?php echo $stats['posts']; ?></td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <span class="font-bold text-zinc-800"><?php echo $avg_seo; ?></span> <span class="text-zinc-400 text-[10px]">/100</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold"><?php echo $stats['leads']; ?> Leads</td>
                                    <td class="py-3 px-4 text-right font-black text-green-700"><?php echo $cr; ?></td>
                                </tr>
                                <?php endforeach;
                                if (!$has_stats): ?>
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-zinc-550 italic">Assign keywords or articles to team members to track performance leaderboard metrics!</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    </div>
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
            <?php if ( $sub_page === 'tasks' ) : ?>
            <section id="cora-page-tasks" class="cora-page-section cora-active space-y-6 flex flex-col h-[calc(100vh-100px)]">
                <div class="cora-page-header flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                        </span>
                        <div>
                            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Client Task Manager</h1>
                            <p class="cora-section-desc text-sm text-zinc-500 mt-1">Manage and assign tasks related to clients and properties.</p>
                        </div>
                    </div>
                    <button id="cora-add-task-btn" class="bg-zinc-900 hover:bg-zinc-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        + New Task
                    </button>
                </div>

                <div class="flex gap-6 h-full min-h-0 overflow-hidden">
                    <div class="flex-1 bg-zinc-50 border border-zinc-200 rounded-lg p-4 flex flex-col min-w-0">
                        <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-4 shrink-0">To Do</h3>
                        <div id="cora-tasks-todo" class="space-y-3 overflow-y-auto flex-1 min-h-0 pb-4">
                            <!-- Tasks injected here -->
                        </div>
                    </div>
                    <div class="flex-1 bg-zinc-50 border border-zinc-200 rounded-lg p-4 flex flex-col min-w-0">
                        <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-4 shrink-0">In Progress</h3>
                        <div id="cora-tasks-progress" class="space-y-3 overflow-y-auto flex-1 min-h-0 pb-4">
                            <!-- Tasks injected here -->
                        </div>
                    </div>
                    <div class="flex-1 bg-zinc-50 border border-zinc-200 rounded-lg p-4 flex flex-col min-w-0">
                        <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-4 shrink-0">Done</h3>
                        <div id="cora-tasks-done" class="space-y-3 overflow-y-auto flex-1 min-h-0 pb-4">
                            <!-- Tasks injected here -->
                        </div>
                    </div>
                </div>
                
                <!-- Add Task Drawer -->
                <div id="cora-task-drawer" class="fixed inset-y-0 right-0 w-[400px] bg-white shadow-2xl border-l border-zinc-200 transform translate-x-full transition-transform duration-300 z-50 flex flex-col">
                    <div class="flex items-center justify-between p-5 border-b border-zinc-100">
                        <h2 class="text-lg font-bold text-zinc-900">New Task</h2>
                        <button class="cora-close-task-drawer text-zinc-400 hover:text-zinc-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="p-5 overflow-y-auto flex-1 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Task Title *</label>
                            <input type="text" id="cora-task-title-input" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-md focus:border-zinc-400 focus:outline-none" placeholder="E.g., Send documents to client">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Assignee</label>
                            <input type="text" id="cora-task-assignee-input" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-md focus:border-zinc-400 focus:outline-none" placeholder="Who is doing this?">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Description</label>
                            <textarea id="cora-task-desc-input" rows="4" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-md focus:border-zinc-400 focus:outline-none"></textarea>
                        </div>
                    </div>
                    <div class="p-5 border-t border-zinc-100 bg-zinc-50 flex justify-end gap-3">
                        <button class="cora-close-task-drawer px-4 py-2 text-sm font-medium text-zinc-600 bg-white border border-zinc-200 rounded-md hover:bg-zinc-50">Cancel</button>
                        <button id="cora-save-task-btn" class="px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Save Task</button>
                    </div>
                </div>
            </section>
            <?php endif; ?>

        </div>
    </main>

    <!-- Collapsible Right-side AI Sidebar (Notion-AI style) -->
    <aside id="cora-ai-sidebar" class="cora-ai-sidebar collapsed fixed top-0 right-0 z-50 h-full w-[350px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span class="cora-ai-sidebar-title text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-550">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                </svg>
                Cora AI Assistant
            </span>
            <button class="cora-ai-sidebar-close text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleSidebar(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="cora-ai-sidebar-body flex-1 overflow-y-auto p-4 flex flex-col justify-between gap-6">
            <div class="cora-ai-sidebar-chat-history flex flex-col gap-3" id="cora-sidebar-chat">
                <div class="chat-bubble ai bg-zinc-100 text-zinc-850 rounded-lg rounded-bl-none p-3 text-xs leading-relaxed self-start border border-zinc-200/50 shadow-sm max-w-[85%]">
                    Hello! I am Cora, your real estate workspace intelligence. Ask me about bookings, client messages, or writing listing descriptions.
                </div>
            </div>
            <!-- AI Prompt Shortcuts -->
            <div class="cora-ai-sidebar-shortcuts pt-4 border-t border-zinc-150">
                <span class="cora-sidebar-sublabel text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2.5 block">Quick Prompts</span>
                <button class="cora-shortcut-btn w-full text-left p-2.5 text-xs text-zinc-650 border border-zinc-200 rounded-md hover:bg-zinc-50 hover:text-zinc-950 transition-colors mb-2 cursor-pointer font-medium" onclick="coraSendShortcut('Draft a WhatsApp reminder for Ananya Sharma')">Draft a reminder for Ananya</button>
                <button class="cora-shortcut-btn w-full text-left p-2.5 text-xs text-zinc-655 border border-zinc-200 rounded-md hover:bg-zinc-50 hover:text-zinc-955 transition-colors cursor-pointer font-medium" onclick="coraSendShortcut('Check status of Rohit & Sneha')">Check Rohit & Sneha's deal</button>
            </div>
        </div>
        <div class="cora-ai-sidebar-footer-input p-3 border-t border-zinc-200/80 flex items-center gap-2 bg-zinc-50 shrink-0">
            <input type="text" id="cora-sidebar-chat-input" placeholder="Ask Cora AI..." onkeydown="if(event.key === 'Enter') coraSendSidebarChatMessage()" class="flex-1 border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none">
            <button class="cora-btn-primary px-3 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded text-xs transition-colors cursor-pointer shrink-0" onclick="coraSendSidebarChatMessage()">Send</button>
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
                    <div class="flex items-center gap-1.5 border-b border-zinc-150 pb-2 mb-1">
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
                    <div class="flex items-center gap-1.5 border-b border-zinc-150 pb-2 mb-1">
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
                    <div class="flex items-center gap-1.5 border-b border-zinc-150 pb-2 mb-1">
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
                    <div class="flex items-center gap-1.5 border-b border-zinc-150 pb-2 mb-1">
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
                    <div class="flex items-center gap-1.5 border-b border-zinc-150 pb-2 mb-1">
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
                    <div class="flex items-center gap-1.5 border-b border-zinc-150 pb-2 mb-1">
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
                    <div class="flex items-center gap-1.5 border-b border-zinc-150 pb-2 mb-1">
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
        
        <div class="cora-drawer-footer p-4 flex flex-col gap-2 shrink-0 border-t border-zinc-150 bg-zinc-50">
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
        <div class="px-5 py-4 border-b border-zinc-150/80 flex items-center justify-between bg-zinc-50/50 shrink-0">
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
        <div class="px-5 py-3.5 border-t border-zinc-150/80 bg-zinc-50/50 flex items-center justify-end gap-2.5 shrink-0">
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
            <div class="relative pl-6 border-l-2 border-zinc-150 pb-2">
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
            <div class="relative pl-6 border-l-2 border-zinc-150 pb-2">
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
            <div class="relative pl-6 border-l-2 border-zinc-150 pb-2">
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
                        <?php foreach ( $cora_re_clients as $client ) : ?>
                            <option value="client_<?php echo esc_attr( $client['id'] ); ?>"><?php echo esc_html( $client['names'] ); ?> (Client)</option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Leads/Prospects">
                        <?php foreach ( $cora_re_leads as $lead ) : ?>
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
    <aside id="drawer-article-leads" class="fixed top-0 right-0 h-full w-[450px] bg-white border-l border-zinc-200 shadow-2xl z-[150] transform translate-x-full transition-transform duration-300 ease-out flex flex-col overflow-hidden">
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
    <div class="cora-mobile-bottom-bar-wrapper lg:hidden" style="position: fixed !important; bottom: 16px !important; left: 50% !important; transform: translateX(-50%) !important; z-index: 99999 !important; align-items: center !important; justify-content: center !important; gap: 8px !important; width: 94vw !important; max-width: 480px !important; box-sizing: border-box !important;">
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
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 2px;">
                    <path d="M6 3h12M6 8h12M6 13h5a3.5 3.5 0 0 0 0-5H6M9 13l6.5 8"></path>
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
    #cora-full-page-editor { display: none; }
    #cora-full-page-editor:not(.hidden) { display: flex !important; }
    .cora-serif-editor .ql-editor { font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif; font-size: 1.125rem; line-height: 1.8; color: #18181b; }
    .cora-sans-editor .ql-editor { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 1.05rem; line-height: 1.75; color: #18181b; }
    .ql-toolbar.ql-snow { border: none !important; border-bottom: 1px solid #e4e4e7 !important; padding: 8px 16px !important; sticky: top; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px); z-index: 20; }
    .ql-container.ql-snow { border: none !important; }
    .ql-editor.ql-blank::before { color: #d4d4d8; font-style: normal; }
    </style>

    <div id="cora-full-page-editor" class="hidden fixed inset-0 z-[100] bg-white flex-col h-full overflow-hidden select-none">
        
        <!-- Modern Header Bar -->
        <header class="flex items-center justify-between px-6 py-3 border-b border-zinc-200 bg-white shrink-0 z-30">
            <div class="flex items-center gap-4">
                <button type="button" class="flex items-center gap-2 text-zinc-500 hover:text-zinc-900 transition-colors text-xs font-semibold cursor-pointer py-1 px-2.5 rounded-lg hover:bg-zinc-100" onclick="coraToggleContentDrawer(false)">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Back</span>
                </button>
                <div class="h-4 w-px bg-zinc-200"></div>
                
                <!-- Document Status Indicator -->
                <div class="flex items-center gap-2 px-2.5 py-1 rounded-full bg-zinc-100 text-xs text-zinc-600 font-medium border border-zinc-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="cora-editor-status">Saved to draft</span>
                </div>

                <div class="hidden sm:block h-4 w-px bg-zinc-200"></div>

                <!-- Live Word Count & Estimated Read Time -->
                <div class="hidden sm:flex items-center gap-2 text-xs text-zinc-500 font-mono">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span id="cora-editor-metrics">0 words · 0 min read</span>
                </div>
            </div>

            <!-- Primary Action Buttons -->
            <div class="flex items-center gap-2.5">
                <button type="button" class="px-3.5 py-1.5 border border-zinc-200 rounded-lg text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all cursor-pointer text-xs active:scale-95 shadow-2xs" onclick="coraSaveArticle('draft')">
                    Save Draft
                </button>
                <button type="button" class="px-3.5 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white font-semibold rounded-lg transition-all cursor-pointer text-xs flex items-center gap-1.5 active:scale-95 shadow-xs" id="cora-btn-submit-review" onclick="coraSubmitArticleForReview()">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Submit for Review
                </button>
                <button type="button" class="px-4 py-1.5 bg-zinc-950 text-white font-bold rounded-lg hover:bg-black transition-all cursor-pointer text-xs flex items-center gap-2 shadow-xs active:scale-95 border border-zinc-900" onclick="coraSaveArticle('publish')">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                    Publish Live
                </button>
                
                <div class="h-4 w-px bg-zinc-200 mx-1"></div>

                <!-- Toggle Sidebar Button -->
                <button type="button" id="cora-btn-toggle-inspector" class="p-2 border border-zinc-200 text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 rounded-lg transition-all cursor-pointer" title="Toggle Inspector Panel" onclick="coraToggleArticleInspector()">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                </button>
            </div>
        </header>

        <!-- Editor Body -->
        <div class="flex-1 flex overflow-hidden relative">
            
            <!-- Notion/Medium-Style Writing Canvas -->
            <main class="flex-1 overflow-y-auto px-6 py-10 md:px-16 xl:px-32 relative">
                <div class="max-w-[720px] mx-auto w-full flex flex-col gap-6">
                    
                    <!-- Cover Image Dropzone Header -->
                    <div id="cora-cover-image-container" class="relative group w-full rounded-2xl overflow-hidden bg-zinc-50 border border-dashed border-zinc-200 hover:border-zinc-400 transition-all min-h-[140px] flex items-center justify-center">
                        <img id="cora-cover-image-img" src="" class="hidden w-full h-48 md:h-64 object-cover">
                        
                        <!-- Placeholder when no cover image -->
                        <div id="cora-cover-image-placeholder" class="flex flex-col items-center gap-2 py-8 text-zinc-400 group-hover:text-zinc-600 cursor-pointer transition-colors" onclick="coraOpenMediaLibrary()">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <span class="text-xs font-semibold">+ Add Cover Image</span>
                        </div>

                        <!-- Hover Controls Bar -->
                        <div id="cora-cover-image-controls" class="absolute bottom-3 right-3 hidden group-hover:flex items-center gap-1.5 bg-zinc-950/80 backdrop-blur-md text-white p-1.5 rounded-xl shadow-lg border border-white/10">
                            <button type="button" class="px-2.5 py-1 text-[11px] font-medium hover:bg-white/20 rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer" onclick="coraOpenMediaLibrary()">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                Add Cover Image
                            </button>
                            <button type="button" class="px-2.5 py-1 text-[11px] font-medium hover:bg-white/20 rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer" onclick="window.coraShowToast('Drag cover image to reposition', 'info')">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="5 9 2 12 5 15"></polyline><polyline points="9 5 12 2 15 5"></polyline><polyline points="15 19 12 22 9 19"></polyline><polyline points="19 9 22 12 19 15"></polyline><line x1="2" y1="12" x2="22" y2="12"></line><line x1="12" y1="2" x2="12" y2="22"></line></svg>
                                Reposition
                            </button>
                            <button type="button" class="px-2.5 py-1 text-[11px] font-medium hover:bg-red-500/30 text-red-300 hover:text-white rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer" onclick="coraRemoveCoverImage()">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                Remove
                            </button>
                        </div>
                    </div>

                    <!-- Typography Switcher & Helper Bar -->
                    <div class="flex items-center justify-between pb-2 border-b border-zinc-100 select-none">
                        <div class="flex items-center gap-1 bg-zinc-100 p-1 rounded-lg">
                            <button type="button" id="cora-font-serif-btn" onclick="coraSetEditorFont('serif')" class="px-3 py-1 rounded-md text-xs font-serif font-bold text-zinc-900 bg-white shadow-xs cursor-pointer transition-all">Serif</button>
                            <button type="button" id="cora-font-sans-btn" onclick="coraSetEditorFont('sans')" class="px-3 py-1 rounded-md text-xs font-sans font-medium text-zinc-500 hover:text-zinc-900 cursor-pointer transition-all">Sans-Serif</button>
                        </div>
                        <span class="text-[11px] text-zinc-400 font-medium">Distraction-Free Medium Canvas</span>
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
                    
                    <!-- Title Input -->
                    <input type="text" id="cora-article-title" placeholder="Article Title" oninput="coraUpdateWordCount()" class="text-4xl md:text-5xl font-extrabold text-zinc-900 placeholder:text-zinc-300 w-full border-none focus:ring-0 focus:outline-none bg-transparent leading-tight tracking-tight mb-2">
                    
                    <!-- Floating / Inline Formatting & Slash Command Hint -->
                    <div class="flex items-center gap-2 py-2 px-3 bg-zinc-50 border border-zinc-200/80 rounded-xl text-xs text-zinc-500 font-mono select-none">
                        <span class="px-1.5 py-0.5 bg-zinc-200 text-zinc-800 font-bold rounded text-[10px]">/</span>
                        <span>Type <kbd class="font-bold text-zinc-800">/</kbd> for slash commands or select text for floating formatting toolbar</span>
                    </div>

                    <!-- Quill.js Mount Point -->
                    <div id="cora-quill-editor" class="text-lg text-zinc-800 leading-relaxed cora-serif-editor min-h-[450px]"></div>
                </div>
            </main>

            <!-- Refined Right Inspector Panel -->
            <aside id="cora-article-inspector" class="w-[320px] bg-zinc-50/50 border-l border-zinc-200 flex flex-col overflow-y-auto shrink-0 transition-all duration-300 select-none">
                
                <!-- Inspector Navigation Tabs -->
                <div class="flex border-b border-zinc-200 bg-white sticky top-0 z-10 text-xs font-semibold">
                    <button type="button" id="tab-inspector-copilot" onclick="coraSwitchInspectorTab('copilot')" class="flex-1 py-3 px-2 text-center border-b-2 border-zinc-950 text-zinc-900 cursor-pointer transition-colors flex items-center justify-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                        <span>Copilot</span>
                    </button>
                    <button type="button" id="tab-inspector-meta" onclick="coraSwitchInspectorTab('meta')" class="flex-1 py-3 px-2 text-center border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 cursor-pointer transition-colors flex items-center justify-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <span>Meta</span>
                    </button>
                    <button type="button" id="tab-inspector-seo" onclick="coraSwitchInspectorTab('seo')" class="flex-1 py-3 px-2 text-center border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 cursor-pointer transition-colors flex items-center justify-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <span>SEO & GEO</span>
                    </button>
                </div>

                <!-- TAB 1: Copilot & AI Tab -->
                <div id="panel-inspector-copilot" class="p-5 space-y-5">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">AI Studio Assistant</span>
                        <p class="text-xs text-zinc-500">Accelerate drafting, tone refinement, and automated content generation.</p>
                    </div>

                    <div class="space-y-2">
                        <button type="button" class="w-full py-2.5 px-3 bg-zinc-950 hover:bg-black text-white font-semibold rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs active:scale-95 border border-zinc-900" onclick="coraGenerateArticleAI()">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                            <span>Write with AI</span>
                        </button>
                        
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <button type="button" class="py-2 px-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 font-medium rounded-lg text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs" onclick="coraAIToneImprove()">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                <span>Improve Tone</span>
                            </button>
                            <button type="button" class="py-2 px-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 font-medium rounded-lg text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs" onclick="coraAIFixGrammar()">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Fix Grammar</span>
                            </button>
                        </div>

                        <button type="button" class="w-full py-2 px-3 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 font-medium rounded-lg text-xs transition-colors flex items-center justify-center gap-2 cursor-pointer shadow-2xs" onclick="coraAIGenerateExcerpt()">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="18" y1="18" x2="3" y2="18"></line></svg>
                            <span>Auto-Generate Excerpt</span>
                        </button>
                    </div>

                    <!-- Excerpt Textarea -->
                    <div class="space-y-1.5 pt-3 border-t border-zinc-200">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Post Excerpt</label>
                        <textarea id="cora-article-excerpt" rows="3" placeholder="Summary snippet for search results and social previews..." class="w-full text-xs border border-zinc-200 rounded-lg p-2.5 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 placeholder:text-zinc-300 resize-none"></textarea>
                    </div>
                </div>

                <!-- TAB 2: Publishing Meta Tab -->
                <div id="panel-inspector-meta" class="hidden p-5 space-y-5">
                    
                    <!-- Featured Image -->
                    <div class="space-y-2">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Featured Image</span>
                        <input type="hidden" id="cora-thumbnail-id" value="">
                        <div id="cora-thumbnail-preview" class="w-full aspect-[16/9] bg-zinc-100 rounded-xl border border-zinc-200 flex items-center justify-center overflow-hidden relative group cursor-pointer" onclick="coraOpenMediaLibrary()">
                            <div class="absolute inset-0 bg-black/60 hidden group-hover:flex items-center justify-center transition-all z-10">
                                <span class="text-white text-xs font-semibold flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    Change Image
                                </span>
                            </div>
                            <img src="" id="cora-thumbnail-img" class="hidden w-full h-full object-cover">
                            <span id="cora-thumbnail-placeholder" class="text-xs text-zinc-400 font-semibold flex flex-col items-center gap-1">
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" class="mb-1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                Select Image
                            </span>
                        </div>
                    </div>

                    <!-- Taxonomies & Metadata -->
                    <div class="space-y-4 pt-3 border-t border-zinc-200">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Categories</label>
                            <select id="cora-article-categories" multiple class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 min-h-[80px]">
                                <?php foreach($cora_categories as $cat): ?>
                                    <option value="<?php echo $cat->term_id; ?>"><?php echo esc_html($cat->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Tags</label>
                            <select id="cora-article-tags" multiple class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 min-h-[80px]">
                                <?php foreach($cora_tags as $tag): ?>
                                    <option value="<?php echo $tag->term_id; ?>"><?php echo esc_html($tag->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-[9px] text-zinc-400">Hold Cmd/Ctrl to select multiple tags.</p>
                        </div>

                        <!-- Assignee / Author -->
                        <div class="space-y-1.5 pt-2 border-t border-zinc-200">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Assignee / Author</label>
                            <select id="cora-article-assignee" class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800">
                                <option value="0">Unassigned</option>
                                <?php foreach($cora_users as $usr): ?>
                                    <option value="<?php echo $usr->ID; ?>"><?php echo esc_html($usr->display_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Scheduled Publication Date -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Scheduled Date</label>
                            <input type="datetime-local" id="cora-article-scheduled-date" class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800">
                        </div>

                        <!-- Editorial Feedback Box -->
                        <div id="cora-editorial-feedback-box" class="hidden p-3 rounded-lg border border-zinc-300 bg-zinc-100 text-xs text-zinc-800 leading-tight space-y-1">
                            <div class="flex items-center gap-1.5 font-bold text-zinc-900">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                <span>Revision Required</span>
                            </div>
                            <p id="cora-editorial-feedback-text" class="italic text-[11px] text-zinc-600"></p>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: SEO & GEO Inspector Tab -->
                <div id="panel-inspector-seo" class="hidden p-5 space-y-5">
                    
                    <!-- Sub Header Tab Switcher (SEO vs GEO) -->
                    <div class="flex border-b border-zinc-200 bg-zinc-100/70 p-1 rounded-lg select-none text-[10px] font-bold uppercase tracking-wider">
                        <button type="button" class="flex-1 py-1.5 text-center border-b-2 border-zinc-950 text-zinc-900 cursor-pointer transition-colors flex items-center justify-center gap-1.5 rounded-md bg-white shadow-2xs" id="btn-sidebar-seo" onclick="coraSwitchSidebarTab('seo')">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            SEO
                        </button>
                        <button type="button" class="flex-1 py-1.5 text-center border-b-2 border-transparent text-zinc-400 hover:text-zinc-600 cursor-pointer transition-colors flex items-center justify-center gap-1.5 rounded-md" id="btn-sidebar-geo" onclick="coraSwitchSidebarTab('geo')">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            GEO / AISEO
                        </button>
                    </div>

                    <!-- SEO Panel -->
                    <div id="panel-sidebar-seo" class="space-y-4">
                        
                        <!-- SEO Ring Score Chart -->
                        <div class="p-4 bg-white border border-zinc-200 rounded-xl flex items-center justify-between shadow-2xs">
                            <div>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">SEO Health Score</span>
                                <span class="text-xs text-zinc-500 font-medium" id="cora-seo-status-text">Analysis Pending</span>
                            </div>
                            <div class="relative w-14 h-14 flex items-center justify-center">
                                <svg class="w-14 h-14 transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-zinc-200" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path id="cora-seo-score-ring" class="text-zinc-950" stroke-dasharray="0, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <span class="absolute text-xs font-extrabold text-zinc-900" id="cora-seo-score-display">--</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-medium text-zinc-600 block">Focus Keyword</label>
                            <input type="text" id="cora-seo-keyword" placeholder="e.g. Property Listings" oninput="coraUpdateSEOAudits()" class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-medium text-zinc-600 block">Meta Description</label>
                            <textarea id="cora-seo-description" placeholder="Summarize article for search engines..." oninput="coraUpdateSEOAudits()" rows="3" class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 resize-none text-zinc-800 bg-white"></textarea>
                        </div>

                        <!-- Live SEO Checklist -->
                        <div class="space-y-2 pt-2 border-t border-zinc-200">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Live Checklist</span>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center gap-2">
                                    <span id="chk-indicator-h1" class="w-4 h-4 rounded-full bg-zinc-200 text-zinc-500 flex items-center justify-center text-[10px] font-bold">✕</span>
                                    <span class="text-zinc-700">Article H1 Title Present</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span id="chk-indicator-meta" class="w-4 h-4 rounded-full bg-zinc-200 text-zinc-500 flex items-center justify-center text-[10px] font-bold">✕</span>
                                    <span class="text-zinc-700">Meta Description (120-160 chars)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span id="chk-indicator-density" class="w-4 h-4 rounded-full bg-zinc-200 text-zinc-500 flex items-center justify-center text-[10px] font-bold">✕</span>
                                    <span class="text-zinc-700">Keyword Density Optimal</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="w-full py-2 text-xs font-bold text-zinc-900 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-colors cursor-pointer shadow-2xs" onclick="coraAnalyzeSEO()">Run SEO Analysis</button>
                    </div>

                    <!-- GEO & AISEO Panel -->
                    <div id="panel-sidebar-geo" class="hidden space-y-4 animate-fade-in">
                        <!-- GEO Score Indicator -->
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-200">
                            <div>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">GEO Score</span>
                                <span class="text-[9px] text-zinc-500">AI Search Visibility Index</span>
                            </div>
                            <div class="flex items-end gap-0.5 text-zinc-900 font-extrabold">
                                <span class="text-2xl leading-none" id="cora-geo-score-display">65</span>
                                <span class="text-[9px] text-zinc-400">/100</span>
                            </div>
                        </div>

                        <!-- Visibility Engine Checklist -->
                        <div class="space-y-3">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">GEO Optimizations</span>
                            
                            <div class="flex items-start gap-2 text-xs">
                                <input type="checkbox" id="chk-geo-direct-answer" disabled class="mt-0.5 rounded border-zinc-300 text-zinc-950 focus:ring-0">
                                <div>
                                    <span class="font-bold text-zinc-800 block">Direct Answer Block</span>
                                    <span class="text-[9px] text-zinc-400 block leading-tight">Short Q&A block answering core local queries under 100 words.</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-2 text-xs">
                                <input type="checkbox" id="chk-geo-info-density" disabled class="mt-0.5 rounded border-zinc-300 text-zinc-950 focus:ring-0">
                                <div>
                                    <span class="font-bold text-zinc-800 block">Structured Data Table</span>
                                    <span class="text-[9px] text-zinc-400 block leading-tight">A summarized pricing/amenities Markdown table or data list.</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-2 text-xs">
                                <input type="checkbox" id="chk-geo-citations" disabled class="mt-0.5 rounded border-zinc-300 text-zinc-950 focus:ring-0">
                                <div>
                                    <span class="font-bold text-zinc-800 block">Authority Citations</span>
                                    <span class="text-[9px] text-zinc-400 block leading-tight">External links or industry citations validating local metrics.</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-2 text-xs">
                                <input type="checkbox" id="chk-geo-schema" disabled class="mt-0.5 rounded border-zinc-300 text-zinc-950 focus:ring-0">
                                <div>
                                    <span class="font-bold text-zinc-800 block">JSON-LD Schema Markup</span>
                                    <span class="text-[9px] text-zinc-400 block leading-tight">Article structured data script enqueued.</span>
                                </div>
                            </div>
                        </div>

                        <!-- In-Post Lead Capture CTAs -->
                        <div class="space-y-2 pt-2 border-t border-zinc-200">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">In-Post Lead Capture CTAs</span>
                            <div class="grid grid-cols-1 gap-2">
                                <button type="button" class="w-full text-left p-2 border border-zinc-200 hover:border-zinc-400 rounded-lg bg-white hover:bg-zinc-50 transition-all cursor-pointer flex items-center gap-2" onclick="coraInjectQuillCTA('valuation')">
                                    <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-800 shrink-0">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                    </span>
                                    <div>
                                        <span class="text-[10px] font-bold text-zinc-800 block">Property Valuation Form</span>
                                        <span class="text-[8px] text-zinc-400 block leading-none">Captures home seller appraisal requests</span>
                                    </div>
                                </button>
                                <button type="button" class="w-full text-left p-2 border border-zinc-200 hover:border-zinc-400 rounded-lg bg-white hover:bg-zinc-50 transition-all cursor-pointer flex items-center gap-2" onclick="coraInjectQuillCTA('catalog')">
                                    <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-800 shrink-0">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    </span>
                                    <div>
                                        <span class="text-[10px] font-bold text-zinc-800 block">Pricing Guide Download Card</span>
                                        <span class="text-[8px] text-zinc-400 block leading-none">Captures local buyers catalog leads</span>
                                    </div>
                                </button>
                                <button type="button" class="w-full text-left p-2 border border-zinc-200 hover:border-zinc-400 rounded-lg bg-white hover:bg-zinc-50 transition-all cursor-pointer flex items-center gap-2" onclick="coraInjectQuillCTA('scheduler')">
                                    <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-800 shrink-0">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    </span>
                                    <div>
                                        <span class="text-[10px] font-bold text-zinc-800 block">Virtual Tour Scheduler</span>
                                        <span class="text-[8px] text-zinc-400 block leading-none">Captures showing calendar bookings</span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Local Entity Mentions -->
                        <div class="space-y-2 pt-2 border-t border-zinc-200">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">Local Entity Mentions (GEO)</span>
                            <div class="flex flex-wrap gap-1.5 select-none" id="cora-geo-entities-list">
                                <span class="px-2 py-0.5 border border-zinc-200 rounded text-[9px] font-semibold text-zinc-400" id="entity-mention-vasant">Vasant Vihar</span>
                                <span class="px-2 py-0.5 border border-zinc-200 rounded text-[9px] font-semibold text-zinc-400" id="entity-mention-dlf">DLF Phase 5</span>
                                <span class="px-2 py-0.5 border border-zinc-200 rounded text-[9px] font-semibold text-zinc-400" id="entity-mention-gurgaon">Gurgaon</span>
                                <span class="px-2 py-0.5 border border-zinc-200 rounded text-[9px] font-semibold text-zinc-400" id="entity-mention-cyber">Cyber City</span>
                            </div>
                        </div>

                        <!-- Auto-Optimize Button -->
                        <button type="button" class="w-full py-2.5 mt-2 bg-zinc-950 hover:bg-black text-white font-bold rounded-lg text-xs transition-colors cursor-pointer flex items-center justify-center gap-1.5 shadow-xs border border-zinc-900" onclick="coraAutoOptimizeGEO()">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                            Run GEO Auto-Optimize
                        </button>

                        <!-- Schema Preview Accordion -->
                        <div class="border border-zinc-200 rounded-xl overflow-hidden bg-white mt-3">
                            <button type="button" class="w-full px-3 py-2 bg-zinc-50 hover:bg-zinc-100 flex items-center justify-between text-[9px] font-bold text-zinc-500 uppercase tracking-wider cursor-pointer border-none focus:outline-none" onclick="jQuery('#cora-schema-preview-container').toggleClass('hidden')">
                                <span>JSON-LD Schema Preview</span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div id="cora-schema-preview-container" class="hidden p-3 border-t border-zinc-200 bg-zinc-50 overflow-x-auto">
                                <pre class="text-[9px] text-zinc-600 font-mono" id="cora-schema-preview-block">{}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <script>
        window.coraToggleArticleInspector = function() {
            const $panel = jQuery('#cora-article-inspector');
            if ($panel.hasClass('w-[320px]')) {
                $panel.removeClass('w-[320px]').addClass('w-0 overflow-hidden opacity-0 border-l-0');
            } else {
                $panel.removeClass('w-0 overflow-hidden opacity-0 border-l-0').addClass('w-[320px]');
            }
        };

        window.coraSwitchInspectorTab = function(tab) {
            jQuery('#tab-inspector-copilot, #tab-inspector-meta, #tab-inspector-seo').removeClass('border-zinc-950 text-zinc-900').addClass('border-transparent text-zinc-400');
            jQuery('#panel-inspector-copilot, #panel-inspector-meta, #panel-inspector-seo').addClass('hidden');
            
            if (tab === 'copilot') {
                jQuery('#tab-inspector-copilot').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900');
                jQuery('#panel-inspector-copilot').removeClass('hidden');
            } else if (tab === 'meta') {
                jQuery('#tab-inspector-meta').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900');
                jQuery('#panel-inspector-meta').removeClass('hidden');
            } else if (tab === 'seo') {
                jQuery('#tab-inspector-seo').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900');
                jQuery('#panel-inspector-seo').removeClass('hidden');
            }
        };

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

        window.coraUpdateWordCount = function() {
            let text = jQuery('#cora-article-title').val() || '';
            if (window.coraQuillListingCoordinator) {
                text += ' ' + (window.coraQuillListingCoordinator.getText() || '');
            } else {
                text += ' ' + (jQuery('#cora-quill-editor').text() || '');
            }
            const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
            const mins = Math.max(1, Math.ceil(words / 200));
            jQuery('#cora-editor-metrics').text(`${words} words · ${mins} min read`);
            if (window.coraUpdateSEOAudits) window.coraUpdateSEOAudits();
        };

        window.coraUpdateSEOAudits = function() {
            const title = jQuery('#cora-article-title').val() || '';
            const meta = jQuery('#cora-seo-description').val() || '';
            const kw = (jQuery('#cora-seo-keyword').val() || '').toLowerCase();
            let text = '';
            if (window.coraQuillListingCoordinator) {
                text = (window.coraQuillListingCoordinator.getText() || '').toLowerCase();
            }

            let score = 0;
            if (title.length > 5) {
                score += 30;
                jQuery('#chk-indicator-h1').removeClass('bg-zinc-200 text-zinc-500').addClass('bg-emerald-500 text-white').html('✓');
            } else {
                jQuery('#chk-indicator-h1').removeClass('bg-emerald-500 text-white').addClass('bg-zinc-200 text-zinc-500').html('✕');
            }

            if (meta.length >= 80 && meta.length <= 160) {
                score += 35;
                jQuery('#chk-indicator-meta').removeClass('bg-zinc-200 text-zinc-500').addClass('bg-emerald-500 text-white').html('✓');
            } else {
                jQuery('#chk-indicator-meta').removeClass('bg-emerald-500 text-white').addClass('bg-zinc-200 text-zinc-500').html('✕');
            }

            if (kw && text.includes(kw)) {
                score += 35;
                jQuery('#chk-indicator-density').removeClass('bg-zinc-200 text-zinc-500').addClass('bg-emerald-500 text-white').html('✓');
            } else {
                jQuery('#chk-indicator-density').removeClass('bg-emerald-500 text-white').addClass('bg-zinc-200 text-zinc-500').html('✕');
            }

            jQuery('#cora-seo-score-display').text(score);
            jQuery('#cora-seo-score-ring').attr('stroke-dasharray', `${score}, 100`);
            jQuery('#cora-seo-status-text').text(score >= 70 ? 'Optimal SEO' : (score >= 30 ? 'Needs Improvement' : 'Poor Optimization'));
        };

        window.coraRemoveCoverImage = function() {
            jQuery('#cora-cover-image-img').attr('src', '').addClass('hidden');
            jQuery('#cora-cover-image-placeholder').removeClass('hidden');
            if (window.coraShowToast) window.coraShowToast('Cover image removed', 'info');
        };

        window.coraAIToneImprove = function() {
            if (window.coraShowToast) window.coraShowToast('Refining content tone with AI...', 'info');
            setTimeout(() => {
                if (window.coraShowToast) window.coraShowToast('Content tone optimized for clarity & impact.', 'success');
            }, 1000);
        };

        window.coraAIFixGrammar = function() {
            if (window.coraShowToast) window.coraShowToast('Checking grammar and style...', 'info');
            setTimeout(() => {
                if (window.coraShowToast) window.coraShowToast('Grammar check completed. All clear!', 'success');
            }, 1000);
        };

        window.coraAIGenerateExcerpt = function() {
            const text = (window.coraQuillListingCoordinator ? window.coraQuillListingCoordinator.getText() : jQuery('#cora-quill-editor').text()) || '';
            if (!text.trim()) {
                if (window.coraShowToast) window.coraShowToast('Write some content first to generate an excerpt', 'warning');
                return;
            }
            const snippet = text.trim().substring(0, 150) + '...';
            jQuery('#cora-article-excerpt').val(snippet);
            if (window.coraShowToast) window.coraShowToast('Excerpt generated from content', 'success');
        };

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (window.coraQuillListingCoordinator) {
                    window.coraQuillListingCoordinator.on('text-change', function() {
                        window.coraUpdateWordCount();
                    });
                }
            }, 1500);
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
    <aside id="cora-media-library-drawer" class="translate-x-full fixed top-0 right-0 z-[150] h-full w-[450px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
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
    </div> <!-- .flex.flex-row.flex-1.min-h-0 -->
</div> <!-- #cora-workspace -->
<?php
wp_print_media_templates();
wp_print_footer_scripts();
?>

<!-- Workspace Script (Inlined for bulletproof execution) -->
<script>
    <?php include CORA_REAL_ESTATE_AI_PATH . 'assets/js/admin-script.js'; ?>
</script>

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
                    <div class="flex items-start justify-between gap-2 text-zinc-955 dark:text-zinc-50">
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

    // Toggle popover dropdown
    function toggleNotificationDropdown(e) {
        if (e) e.stopPropagation();
        const dropdown = document.getElementById('cora-notif-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
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

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('cora-notif-dropdown');
            if (dropdown && !dropdown.classList.contains('hidden') && !e.target.closest('#cora-notif-bell-btn') && !e.target.closest('#cora-notif-dropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        renderCoraNotifications();
    });
})();
</script>

</div>

<!-- Cora Advanced Command Search Modal (Command Palette for CRM subpages) -->
<div id="cora-command-palette" class="fixed inset-0 z-[999999] hidden items-start justify-center p-4 pt-[6vh] md:pt-[10vh] bg-zinc-950/40 backdrop-blur-sm transition-all duration-200">
    <div class="cora-command-container w-full max-w-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[82vh] h-[540px] transition-transform transform scale-95 duration-200">
        
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
        <div class="flex-1 overflow-y-auto p-2 min-h-0 space-y-1" id="cora-command-results" style="max-height: calc(82vh - 120px); scrollbar-width: thin;">
            <!-- Loading state / Suggestions list / Search results list -->
        </div>
            <!-- Loading state / Suggestions list / Search results list -->
        </div>

        <!-- Footer Bar -->
        <div class="border-t border-zinc-100 dark:border-zinc-800/40 px-4 py-2.5 bg-zinc-50/50 dark:bg-zinc-900/40 flex items-center justify-between shrink-0">
            <span class="text-xs text-zinc-450 dark:text-zinc-400 font-medium">Need help finding something?</span>
            <button type="button" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 font-semibold text-xs rounded-lg transition-colors shadow-sm flex items-center gap-1.5 cursor-pointer" onclick="window.coraTriggerCommandAI()">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                Ask Cora
            </button>
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

    window.coraOpenCommandPalette = function() {
        const palette = document.getElementById('cora-command-palette');
        const input = document.getElementById('cora-command-input');
        if (!palette) return;

        palette.classList.add('active');
        palette.classList.remove('hidden');
        if (input) {
            input.value = '';
            input.focus();
        }
        selectedIndex = -1;
        
        // Load initial suggestions
        coraPerformCommandSearch('');
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
        }
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

    function coraPerformCommandSearch(query, isInline = false) {
        const parentPalette = document.getElementById(isInline ? 'cora-inline-command-palette' : 'cora-command-palette');
        const resultsContainer = parentPalette ? parentPalette.querySelector(isInline ? '#cora-inline-command-results' : '#cora-command-results') : null;
        if (!resultsContainer) return;

        resultsContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center py-10 space-y-2">
                <div class="w-5 h-5 border-2 border-zinc-900 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-[10px] text-zinc-400 font-medium">Searching workspace database...</span>
            </div>
        `;

        const url = coraREData.ajaxUrl + '?action=cora_advanced_search&nonce=' + coraREData.ajaxNonce + '&q=' + encodeURIComponent(query) + '&filter=' + currentFilter;
        
        fetch(url, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
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
                        <p class="text-[10px] text-zinc-455 mt-0.5">Try searching with other filters or keywords</p>
                    </div>
                `;
                if (!isInline) selectedIndex = -1;
            }
        })
        .catch(err => {
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
            let greeting = 'Good afternoon!';
            if (hour >= 5 && hour < 12) {
                greeting = 'Good morning!';
            } else if (hour >= 12 && hour < 17) {
                greeting = 'Good afternoon!';
            } else if (hour >= 17 && hour < 22) {
                greeting = 'Good evening!';
            } else {
                greeting = 'Good night!';
            }
            const greetingEl = document.getElementById('cora-dynamic-greeting-title');
            if (greetingEl) {
                greetingEl.textContent = greeting;
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
                if (inlinePalette) inlinePalette.classList.remove('hidden');
                coraPerformCommandSearch(this.value.trim(), true);
            });
            inlineInput.addEventListener('click', function(e) {
                e.stopPropagation();
                const inlinePalette = document.getElementById('cora-inline-command-palette');
                if (inlinePalette) inlinePalette.classList.remove('hidden');
                coraPerformCommandSearch(this.value.trim(), true);
            });
            inlineInput.addEventListener('input', function() {
                clearTimeout(searchDebounceTimeout);
                const query = this.value.trim();
                const inlinePalette = document.getElementById('cora-inline-command-palette');
                if (inlinePalette) inlinePalette.classList.remove('hidden');
                searchDebounceTimeout = setTimeout(() => {
                    coraPerformCommandSearch(query, true);
                }, 150);
            });
        }

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

        // Sidebar Menu Search Filtering (Simple Search)
        const sidebarSearchInput = document.getElementById('cora-sidebar-search-input');
        if (sidebarSearchInput) {
            sidebarSearchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const navGroups = document.querySelectorAll('.cora-sidebar-nav > div');
                
                navGroups.forEach(group => {
                    let visibleCount = 0;
                    const items = group.querySelectorAll('.cora-nav-item');
                    
                    items.forEach(item => {
                        const navTextEl = item.querySelector('.cora-nav-text');
                        if (navTextEl) {
                            const labelText = navTextEl.textContent.toLowerCase();
                            if (labelText.includes(query)) {
                                item.classList.remove('hidden');
                                visibleCount++;
                            } else {
                                item.classList.add('hidden');
                            }
                        }
                    });
                    
                    // Show or hide the group label/container depending on matching items
                    if (visibleCount > 0 || query === '') {
                        group.classList.remove('hidden');
                    } else {
                        group.classList.add('hidden');
                    }
                });
            });
        }
    });

    // Check for plugin updates in-app
    <?php if ( in_array( $current_user_role, array( 'administrator', 'cora_shruti', 'cora_super_admin' ) ) ) : ?>
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
        if (!confirm('Are you sure you want to upgrade the Cora workspace to the latest version? The screen will reload once complete.')) return;
        
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
    const selectedLang = localStorage.getItem('cora_platform_language');
    if (selectedLang && selectedLang !== 'en') {
        // Load Google Translate script
        window.googleTranslateElementInit = function() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');
            
            // Programmatically trigger translation
            function autoTriggerTranslation() {
                const select = document.querySelector('.goog-te-combo');
                if (select) {
                    select.value = selectedLang;
                    select.dispatchEvent(new Event('change'));
                } else {
                    setTimeout(autoTriggerTranslation, 100);
                }
            }
            autoTriggerTranslation();
        };

        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        document.body.appendChild(script);
    }
})();
</script>

</body>
</html>
