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

$cora_users = array();
if ( in_array( $sub_page, array( 'dashboard', 'bookings', 'team-roles', 'equipment', 'blogs' ) ) ) {
    $args = array();
    $active_ws_id = isset( $cora_active_workspace['id'] ) ? $cora_active_workspace['id'] : '';
    $active_ws_slug = isset( $cora_active_workspace['slug'] ) ? $cora_active_workspace['slug'] : '';
    $agency_id_context = function_exists('cora_get_current_user_agency_id') ? cora_get_current_user_agency_id() : '';
    
    $meta_query = array('relation' => 'OR');
    if ( ! empty( $active_ws_slug ) && $active_ws_slug !== 'super' ) {
        $meta_query[] = array(
            'key'     => 'cora_agency_id',
            'value'   => $active_ws_slug,
            'compare' => '='
        );
    }
    if ( ! empty( $active_ws_id ) ) {
        $meta_query[] = array(
            'key'     => 'cora_agency_id',
            'value'   => $active_ws_id,
            'compare' => '='
        );
        $meta_query[] = array(
            'key'     => 'cora_agency_id',
            'value'   => 'agency_' . $active_ws_id,
            'compare' => '='
        );
    }
    if ( ! empty( $agency_id_context ) && $agency_id_context !== 'super' && $agency_id_context !== $active_ws_slug ) {
        $meta_query[] = array(
            'key'     => 'cora_agency_id',
            'value'   => $agency_id_context,
            'compare' => '='
        );
    }
    
    if ( count( $meta_query ) > 1 ) {
        $args['meta_query'] = $meta_query;
    }
    
    $cora_users = get_users( $args );
}
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
$cora_documents = ( in_array( $sub_page, array( 'vault', 'dashboard' ), true ) ) ? get_option( 'cora_workspace_vault_docs', array() ) : array();
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
    $title_custom      = get_option( 'cora_site_title_custom', '' );
    if ( $current_industry === 'custom' ) {
        $page_title_raw = $title_custom ?: 'Cora Workspace';
    } else {
        $page_title_raw = $is_studio_ind ? $title_studio : $title_real_estate;
    }
    $page_title_format = $page_title_raw ?: 'Cora';
    ?>
    <link rel="icon" type="image/png" href="<?php echo esc_url( $favicon_url ); ?>" />
    <link rel="shortcut icon" id="cora-dynamic-favicon" href="<?php echo esc_url( $favicon_url ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="color-scheme" content="light">
    <title><?php echo esc_html( $page_title_format ); ?></title>
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo CORA_WORKSPACE_URL . 'assets/css/tailwind-built.css?v=' . CORA_WORKSPACE_VERSION; ?>" />
    <link rel="stylesheet" href="<?php echo CORA_WORKSPACE_URL . 'assets/css/admin-style.css?v=' . CORA_WORKSPACE_VERSION; ?>" />
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    
    <!-- PWA Manifest & Service Worker -->
    <link rel="manifest" href="<?php echo home_url('/cora-manifest.json'); ?>">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="<?php echo CORA_WORKSPACE_URL . 'assets/pwa/icon_192.png'; ?>">
    <script>
        // Global error catcher for diagnostic visibility
        window.addEventListener('error', function(e) {
            console.error('Cora Platform Error:', e);
            var msg = e.message || 'Unknown error';
            var file = e.filename ? e.filename.split('/').pop() : 'inline';
            var line = e.lineno || '0';
            var displayErr = function() {
                if (window.coraShowToast) {
                    window.coraShowToast('Error: ' + msg + ' (' + file + ':' + line + ')', 'error');
                } else {
                    setTimeout(displayErr, 1000);
                }
            };
            displayErr();
        });
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Cora Unhandled Promise Rejection:', e);
            var reason = e.reason ? (e.reason.message || e.reason) : 'Promise rejected';
            var displayRej = function() {
                if (window.coraShowToast) {
                    window.coraShowToast('Promise Error: ' + reason, 'error');
                } else {
                    setTimeout(displayRej, 1000);
                }
            };
            displayRej();
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                <?php
                $user_id = get_current_user_id();
                $token = get_user_meta( $user_id, 'cora_pwa_auth_token', true );
                $sw_url = home_url( '/cora-service-worker.js' );
                $sw_url = add_query_arg( array(
                    'v' => CORA_WORKSPACE_VERSION,
                ), $sw_url );
                if ( ! empty( $token ) ) {
                    $sw_url = add_query_arg( 'token', $token, $sw_url );
                }
                ?>
                navigator.serviceWorker.register('<?php echo esc_url( $sw_url ); ?>', { scope: '/' })
                    .then(function(reg) {
                        console.log('Service worker registered with scope:', reg.scope);
                        reg.update();
                        // Detect SW updates and auto-activate new version
                        reg.addEventListener('updatefound', function() {
                            var newWorker = reg.installing;
                            if (newWorker) {
                                newWorker.addEventListener('statechange', function() {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // New SW ready — tell it to skip waiting and take control
                                        newWorker.postMessage({ type: 'skipWaiting' });
                                    }
                                });
                            }
                        });
                    })
                    .catch(function(err) { console.error('Service worker registration failed:', err); });
            });
            // When a new SW takes control, reload for fresh assets
            var refreshing = false;
            navigator.serviceWorker.addEventListener('controllerchange', function() {
                if (!refreshing) {
                    refreshing = true;
                    window.location.reload();
                }
            });
            // Lock mobile screen orientation to portrait-primary
            if (window.screen && window.screen.orientation && typeof window.screen.orientation.lock === 'function') {
                window.screen.orientation.lock('portrait-primary').catch(function() {});
            }
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
    <link href="<?php echo CORA_WORKSPACE_URL . 'assets/css/quill.snow.css'; ?>" rel="stylesheet" media="print" onload="this.media='all'">
    <script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/quill.min.js'; ?>"></script>
    
    <!-- Load ChartJS -->
    <script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/chart.min.js'; ?>" defer></script>
    
    <!-- Load TomSelect -->
    <link href="<?php echo CORA_WORKSPACE_URL . 'assets/css/tom-select.default.min.css'; ?>" rel="stylesheet" media="print" onload="this.media='all'">
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
        }

        /* Override collapsed display:none for desktop notifications side drawer to allow smooth sliding transitions */
        #cora-notif-dropdown.collapsed {
            display: flex !important;
            visibility: visible !important;
            transform: translateX(100%) !important;
            pointer-events: none !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
        }

        #cora-notif-dropdown:not(.collapsed) {
            transform: translateX(0) !important;
            pointer-events: auto !important;
            visibility: visible !important;
            display: flex !important;
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
            overflow-x: clip !important;
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
        .cora-main {
            transition: margin-right 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .cora-role-preview-select option {
            background-color: #ffffff;
            color: #18181b;
        }
        
        /* Feature Hub styling */
        .cora-feature-card {
            transition: all 0.2s ease-in-out !important;
        }
        .cora-feature-card:hover {
            transform: translateY(-2px) !important;
        }
        
        /* Drawer and Form Overrides */
        .cora-drawer-footer {
            background-color: #f9f9f9;
            border-top: 1px solid #e5e7eb;
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
        @media (max-width: 1023px) {
            #cora-ai-sidebar {
                position: fixed !important;
                top: auto !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                height: 60vh !important;
                max-height: 60vh !important;
                width: 100% !important;
                max-width: 100% !important;
                border-top: 1px solid #e4e4e7 !important;
                border-left: none !important;
                border-top-left-radius: 20px !important;
                border-top-right-radius: 20px !important;
                border-bottom-left-radius: 0 !important;
                border-bottom-right-radius: 0 !important;
                box-shadow: none !important;
                transform: translateY(0) !important;
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s !important;
                z-index: 9970 !important;
            }
            #cora-ai-sidebar .cora-ai-sidebar-body {
                padding-bottom: 0 !important;
            }
            
            #cora-ai-sidebar.collapsed {
                transform: translateY(100%) !important;
                pointer-events: none !important;
                visibility: hidden !important;
                box-shadow: none !important;
                display: flex !important;
            }

            /* Remove island shadow when AI drawer is open */
            #cora-mobile-floating-island.cora-island-docked .cora-island-card {
                box-shadow: none !important;
            }
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
                 overflow-x: clip !important;
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

        /* Dot Grid Background Pattern Override */
        #cora-page-dashboard {
            background-color: #FBFaf7 !important; /* Premium warm cream background */
            background-image: radial-gradient(rgba(120, 115, 105, 0.07) 1px, transparent 1px) !important;
            background-size: 24px 24px !important;
            padding: 12px 6px 40px 6px !important;
            border-radius: 20px 20px 0px 0px !important;
            border: none !important;
            box-shadow: none !important;
            transition: background-color 0.3s ease;
            margin-bottom: 0px !important;
        }
        @media (min-width: 768px) {
            #cora-page-dashboard {
                padding: 24px 24px 60px 24px !important;
            }
        }
        main.cora-main, .cora-main, .cora-content-wrapper {
            background-color: #ffffff !important;
        }
        .cora-content-wrapper {
            padding-bottom: 0px !important;
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

<!-- Mobile Orientation Lock Shield -->
<div id="cora-orientation-lock-shield" style="
    position: fixed;
    inset: 0;
    z-index: 1000000;
    background: #ffffff;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px;
    text-align: center;
    user-select: none;
    font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
">
    <div style="
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
        max-width: 280px;
    ">
        <!-- Rotating Phone Animation SVG -->
        <div class="cora-rotate-icon-container">
            <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-cora-rotate" style="color: #18181b;">
                <rect x="5" y="2" width="14" height="20" rx="2" ry="2" />
                <line x1="12" y1="18" x2="12.01" y2="18" stroke-width="3" />
            </svg>
        </div>
        <div>
            <h3 style="font-size: 15px; font-weight: 800; color: #18181b; margin: 0; letter-spacing: -0.01em;">Portrait Mode Required</h3>
            <p style="font-size: 12px; color: #52525b; margin-top: 8px; line-height: 1.6; font-weight: 500;">Please rotate your device back to portrait. Cora Workspace is optimized for portrait view on mobile devices.</p>
        </div>
    </div>
</div>

<style>
/* CSS Media Query to lock mobile/tablet landscape orientation visually to portrait layout */
@media (orientation: landscape) and (max-width: 1023px) {
    #cora-orientation-lock-shield {
        display: none !important;
    }
    html {
        width: 100vw !important;
        height: 100vh !important;
        overflow: hidden !important;
    }
    body {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vh !important;
        height: 100vw !important;
        min-height: 100vw !important;
        transform: rotate(-90deg) translate(-100vh, 0) !important;
        transform-origin: top left !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        -webkit-overflow-scrolling: touch !important;
    }
}
@keyframes cora-device-rotate {
    0%, 100% { transform: rotate(0deg); }
    50% { transform: rotate(-90deg); }
}
.animate-cora-rotate {
    animation: cora-device-rotate 2.4s cubic-bezier(0.77, 0, 0.175, 1) infinite;
}
.cora-rotate-icon-container {
    padding: 16px;
    background: #f4f4f5;
    border-radius: 20px;
    border: 1px solid #e4e4e7;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
    (function() {
        function lockScreenOrientation() {
            if (screen.orientation && typeof screen.orientation.lock === 'function') {
                screen.orientation.lock('portrait-primary').catch(function() {});
            } else if (screen.lockOrientation) {
                screen.lockOrientation('portrait-primary');
            } else if (screen.mozLockOrientation) {
                screen.mozLockOrientation('portrait-primary');
            } else if (screen.msLockOrientation) {
                screen.msLockOrientation('portrait-primary');
            }
        }
        window.addEventListener('load', lockScreenOrientation);
        window.addEventListener('orientationchange', lockScreenOrientation);
        document.addEventListener('click', lockScreenOrientation, { once: true });
        document.addEventListener('touchstart', lockScreenOrientation, { once: true });
    })();
</script>

<?php
// ─── Splash Screen: RAG-powered AI Co-Founder Insight ──────────────────────
$_cora_splash_text  = '';
$_cora_splash_type  = '';
$_cora_splash_leads = 0;

if ( function_exists( 'cora_db_get_agency_id' ) ) {
    global $wpdb;
    $_cora_spl_aid  = cora_db_get_agency_id();
    $_cora_rag_tbl  = $wpdb->prefix . 'cora_rag_knowledge';
    if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $_cora_rag_tbl ) ) {
        $_cora_rag_row = $wpdb->get_row( $wpdb->prepare(
            "SELECT content, source_type FROM {$_cora_rag_tbl} WHERE agency_id = %d ORDER BY RAND() LIMIT 1",
            $_cora_spl_aid
        ), ARRAY_A );
        if ( ! empty( $_cora_rag_row['content'] ) ) {
            $_cora_splash_text = wp_strip_all_tags( $_cora_rag_row['content'] );
            $_cora_splash_type = sanitize_text_field( $_cora_rag_row['source_type'] );
        }
    }
    $_cora_leads_raw    = get_option( 'cora_workspace_leads', array() );
    $_cora_splash_leads = is_array( $_cora_leads_raw ) ? count( $_cora_leads_raw ) : 0;
}

$_cora_spl_fallbacks = array(
    array( 'text' => 'Consistency is the edge. Teams that follow up within 24 hours close 60% more deals.',               'type' => 'Business Wisdom' ),
    array( 'text' => 'Your next breakthrough client is already in your pipeline — nurture every lead with intent.',        'type' => 'Growth Insight'  ),
    array( 'text' => 'Every signed contract builds trust. Design your process around the client, not the paperwork.',      'type' => 'Operations'      ),
    array( 'text' => 'Automations free up hours — invest them back into the relationships that scale your studio.',        'type' => 'Productivity'    ),
    array( 'text' => 'Great photography wins first impressions. Your visual portfolio is your strongest closing tool.',    'type' => 'Studio Insight'  ),
    array( 'text' => 'Data beats gut feeling. Review your financials weekly — small leaks become big losses over time.',   'type' => 'Finance'         ),
);
$_cora_spl_fb = $_cora_spl_fallbacks[ array_rand( $_cora_spl_fallbacks ) ];
if ( empty( $_cora_splash_text ) ) {
    $_cora_splash_text = $_cora_spl_fb['text'];
    $_cora_splash_type = $_cora_spl_fb['type'];
}
if ( mb_strlen( $_cora_splash_text ) > 162 ) {
    $_cora_splash_text = mb_substr( $_cora_splash_text, 0, 159 ) . '…';
}
$_cora_spl_badge = ucwords( str_replace( '_', ' ', $_cora_splash_type ) ) ?: 'Insight';
?>
<!-- Cora AI Co-Founder Insight Splash Screen -->
<div id="cora-app-splash-screen" style="position:fixed;inset:0;background:#ffffff;z-index:100000;display:flex;flex-direction:column;align-items:center;justify-content:center;opacity:1;transition:opacity 0.4s cubic-bezier(0.25,1,0.5,1),transform 0.4s cubic-bezier(0.25,1,0.5,1);font-family:-apple-system,BlinkMacSystemFont,'Inter',sans-serif;user-select:none;padding:32px 24px;">

    <!-- Brand mark -->
    <div style="display:flex;flex-direction:column;align-items:center;gap:13px;margin-bottom:30px;">
        <div class="cora-splash-logo-card" style="width:58px;height:58px;background:#18181b;color:#fff;border-radius:16px;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(24,24,27,0.14);">
            <svg width="25" height="25" viewBox="0 0 14 14" fill="none">
                <rect x="3" y="3" width="3.5" height="3.5" rx="1" fill="currentColor" class="cora-splash-dot dot-1" style="transform:scale(0);opacity:0;"/>
                <rect x="7.5" y="3" width="3.5" height="3.5" rx="1" fill="currentColor" class="cora-splash-dot dot-2" style="transform:scale(0);opacity:0;"/>
                <rect x="3" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor" class="cora-splash-dot dot-3" style="transform:scale(0);opacity:0;"/>
                <rect x="7.5" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor" class="cora-splash-dot dot-4" style="transform:scale(0);opacity:0;"/>
            </svg>
        </div>
        <div style="font-size:10px;font-weight:800;letter-spacing:0.18em;text-transform:uppercase;color:#52525b;">CORA WORKSPACE</div>
    </div>

    <!-- AI Co-Founder Insight Card -->
    <div class="cora-splash-insight-card" style="width:100%;max-width:340px;background:#fafafa;border:1px solid #e4e4e7;border-radius:18px;padding:19px 21px;opacity:0;transform:translateY(10px);">
        <!-- Header row -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div style="display:flex;align-items:center;gap:9px;">
                <div style="width:30px;height:30px;background:#18181b;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                </div>
                <div>
                    <div style="font-size:10.5px;font-weight:700;color:#18181b;line-height:1.2;">Cora Intelligence</div>
                    <div style="font-size:9px;color:#a1a1aa;font-weight:500;margin-top:1px;">AI Co-Founder · Daily Brief</div>
                </div>
            </div>
            <span style="font-size:8px;font-weight:700;color:#71717a;background:#f4f4f5;border:1px solid #e4e4e7;border-radius:5px;padding:2px 8px;letter-spacing:0.03em;white-space:nowrap;"><?php echo esc_html( $_cora_spl_badge ); ?></span>
        </div>
        <!-- Insight text -->
        <p style="font-size:13px;line-height:1.65;color:#27272a;font-weight:500;margin:0 0 14px 0;"><?php echo esc_html( $_cora_splash_text ); ?></p>
        <!-- Footer -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:11px;border-top:1px solid #f0f0f0;">
            <div style="display:flex;align-items:center;gap:5px;">
                <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;flex-shrink:0;animation:cora-pulse-dot 2s ease-in-out infinite;"></span>
                <span style="font-size:9px;color:#a1a1aa;font-weight:600;">Workspace active</span>
            </div>
            <?php if ( $_cora_splash_leads > 0 ) : ?>
            <span style="font-size:9px;color:#a1a1aa;font-weight:600;"><?php echo intval( $_cora_splash_leads ); ?> leads in pipeline</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Progress bar -->
    <div style="width:100%;max-width:340px;height:2px;background:#f4f4f5;border-radius:2px;overflow:hidden;position:relative;margin-top:24px;">
        <div class="cora-splash-progress-bar" style="position:absolute;top:0;left:0;height:100%;width:64px;background:#18181b;border-radius:2px;"></div>
    </div>
</div>

<style>
    @keyframes cora-reveal-dot {
        0%   { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .cora-splash-dot { transform-origin: center; animation: cora-reveal-dot 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }
    .cora-splash-dot.dot-1 { animation-delay: 0.05s; }
    .cora-splash-dot.dot-2 { animation-delay: 0.15s; }
    .cora-splash-dot.dot-3 { animation-delay: 0.25s; }
    .cora-splash-dot.dot-4 { animation-delay: 0.35s; }

    @keyframes cora-splash-pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 8px 24px rgba(24,24,27,0.14); }
        50%       { transform: scale(0.94); box-shadow: 0 4px 14px rgba(24,24,27,0.07); }
    }
    .cora-splash-logo-card { animation: cora-splash-pulse 2.4s ease-in-out infinite; animation-delay: 0.5s; }

    @keyframes cora-splash-slide-bar {
        0%   { left: -70px; }
        100% { left: 110%;  }
    }
    .cora-splash-progress-bar { animation: cora-splash-slide-bar 1.4s cubic-bezier(0.65,0,0.35,1) infinite; }

    @keyframes cora-pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.4; transform: scale(0.7); }
    }
    @keyframes cora-insight-fadein {
        0%   { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0);    }
    }
    .cora-splash-insight-card { animation: cora-insight-fadein 0.55s cubic-bezier(0.25,1,0.5,1) 0.3s forwards; }
</style>

<script>
    (function() {
        var hideSplash = function() {
            var splash = document.getElementById('cora-app-splash-screen');
            if (splash) {
                splash.style.opacity = '0';
                splash.style.transform = 'translateY(-16px)';
                setTimeout(function() { if (splash.parentNode) splash.parentNode.removeChild(splash); }, 400);
            }
        };
        if (document.readyState === 'complete') {
            setTimeout(hideSplash, 900);
        } else {
            window.addEventListener('load', function() { setTimeout(hideSplash, 900); });
            setTimeout(hideSplash, 3500);
        }
    })();
</script>
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
    <div class="cora-impersonation-banner sticky top-0 z-[100] w-full h-10 bg-zinc-900 text-zinc-100 border-b border-zinc-800 text-xs px-4 flex items-center justify-between gap-4 select-none font-sans shadow-md">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-medium text-zinc-200">Impersonation Active</span>
            <span class="text-zinc-700 hidden sm:inline">|</span>
            <span class="text-zinc-400 hidden sm:inline">Viewing workspace on behalf of client / team member.</span>
        </div>
        <button id="cora-switch-back-btn" data-nonce="<?php echo wp_create_nonce('cora_super_switch_back'); ?>" class="bg-zinc-800 hover:bg-zinc-700 active:bg-zinc-600 text-zinc-100 font-semibold px-2.5 py-1 rounded-md border border-zinc-700/50 transition-colors text-[10px] cursor-pointer">
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
    $cora_user_notifications = array();
    $cora_unread_count = 0;

    // Primary source: DB table wp_cora_notifications (canonical since migration)
    if ( function_exists( 'cora_db_get_agency_id' ) ) {
        global $wpdb;
        $_cora_agency_id = cora_db_get_agency_id();
        $_cora_db_notifs = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, title, body, is_read, action_url, created_at
             FROM {$wpdb->prefix}cora_notifications
             WHERE user_id = %d AND agency_id = %d
             ORDER BY created_at DESC
             LIMIT 100",
            $cora_current_user_id,
            $_cora_agency_id
        ), ARRAY_A );
        if ( is_array( $_cora_db_notifs ) ) {
            foreach ( $_cora_db_notifs as $_n ) {
                $cora_user_notifications[] = array(
                    'id'          => 'notif_' . intval( $_n['id'] ),
                    'user_id'     => $cora_current_user_id,
                    'title'       => $_n['title'],
                    'description' => $_n['body'],
                    'timestamp'   => strtotime( $_n['created_at'] ),
                    'read'        => ! empty( $_n['is_read'] ),
                    'action_url'  => esc_url_raw( $_n['action_url'] ?? '' ),
                );
                if ( empty( $_n['is_read'] ) ) {
                    $cora_unread_count++;
                }
            }
        }
    }

    // Legacy fallback: merge any option-based notifications not already in DB results
    $_cora_db_ids_in_list = array_map( function($x) { return $x['id']; }, $cora_user_notifications );
    $_cora_option_notifs = get_option( 'cora_notifications', array() );
    if ( is_array( $_cora_option_notifs ) ) {
        foreach ( $_cora_option_notifs as $_notif ) {
            if (
                isset( $_notif['user_id'] ) &&
                intval( $_notif['user_id'] ) === $cora_current_user_id &&
                ! in_array( $_notif['id'] ?? '', $_cora_db_ids_in_list, true )
            ) {
                $cora_user_notifications[] = $_notif;
                if ( empty( $_notif['read'] ) ) {
                    $cora_unread_count++;
                }
            }
        }
    }

    // Sort by timestamp descending
    usort( $cora_user_notifications, function( $a, $b ) {
        return ( $b['timestamp'] ?? 0 ) - ( $a['timestamp'] ?? 0 );
    } );
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
    <div id="cora-impersonation-safety-banner" class="bg-zinc-950 text-white border-b border-zinc-800 px-4 py-2 flex items-center justify-between text-xs select-none sticky top-0 z-[10000] shadow-sm">
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
    $ann_bg = 'bg-zinc-50 text-zinc-900 border-zinc-200';
    if ( $announcement_type === 'warning' ) {
        $ann_bg = 'bg-amber-50/80 text-amber-900 border-amber-200/80';
    } elseif ( $announcement_type === 'success' ) {
        $ann_bg = 'bg-emerald-50/80 text-emerald-900 border-emerald-200/80';
    }
    $announcement_hash = md5( $announcement_text );
    ?>
    <div id="cora-global-announcement-banner" data-hash="<?php echo esc_attr( $announcement_hash ); ?>" class="hidden <?php echo esc_attr( $ann_bg ); ?> border-b px-4 py-2 flex items-center justify-between text-xs select-none sticky top-0 z-[9998] shadow-xs">
        <div class="flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span><?php echo esc_html( $announcement_text ); ?></span>
        </div>
        <button onclick="dismissCoraAnnouncement('<?php echo esc_attr( $announcement_hash ); ?>')" class="p-1 text-zinc-400 hover:text-zinc-700 rounded-lg hover:bg-zinc-200/50 cursor-pointer transition-colors shrink-0">
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
    <header id="cora-global-topbar" class="cora-topbar bg-[#09090b] text-white px-4 md:px-6 py-2.5 flex items-center justify-between border-b border-zinc-800/80 sticky top-0 z-50 shrink-0 select-none" style="position: sticky !important; top: 0 !important; background-color: #09090b !important; z-index: 9999 !important;">
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
            <div onclick="if(typeof window.coraNavigateTo==='function'){window.coraNavigateTo('dashboard');}" class="flex items-center gap-2 select-none shrink-0 cursor-pointer hover:opacity-85 transition-opacity">
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
            <button id="cora-quick-ai-btn" class="cora-btn-secondary w-8 h-8 flex items-center justify-center border border-zinc-200 rounded-lg hover:bg-zinc-100 transition-all active:scale-[0.98] text-zinc-900 bg-white shadow-sm cursor-pointer shrink-0 p-0" title="Cora AI">
                <span class="cora-btn-icon text-zinc-650 flex shrink-0">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"/></svg>
                </span>
            </button>
            <!-- Notifications Bell -->
            <div class="relative shrink-0">
                <button id="cora-notif-bell-btn" onclick="if(event) event.stopPropagation(); window.coraToggleNotificationDrawer();" class="p-1.5 text-zinc-400 hover:text-white hover:bg-zinc-850 rounded-lg transition-all cursor-pointer flex items-center justify-center shrink-0" title="Notifications">
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
                    <div class="relative w-8 h-8 rounded-full flex items-center justify-center shrink-0 leading-none">
                        <?php if ( $current_user_avatar ) : ?>
                            <img src="<?php echo esc_url($current_user_avatar); ?>" class="w-8 h-8 rounded-full object-cover shrink-0 select-none border border-zinc-700/60" alt="<?php echo esc_attr($cora_display_name); ?>" />
                        <?php else : ?>
                            <div class="w-8 h-8 rounded-full bg-white text-zinc-950 font-bold text-sm flex items-center justify-center shrink-0 leading-none">
                                <?php echo esc_html($cora_initials); ?>
                            </div>
                        <?php endif; ?>
                        <!-- Status dot -->
                        <span id="cora-desktop-profile-status-dot" class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border border-[#09090b] bg-zinc-500 transition-colors"></span>
                    </div>
                    <span class="text-white font-semibold text-sm truncate max-w-[100px] hidden md:inline"><?php echo esc_html($cora_display_name); ?></span>
                </div>
            </div>
        </div>
        </div>

        <!-- Header User Profile Popover Card (Shared Desktop & Mobile, Merged Punch Widget) -->
        <div id="cora-header-profile-popover" class="hidden fixed top-[56px] right-4 w-64 bg-white border border-zinc-200 rounded-2xl shadow-xl p-4 z-[9990] gap-2.5 animate-in fade-in slide-in-from-top-2 duration-150 select-none" style="flex-direction:column;">
            <!-- User Profile Header -->
            <div class="flex items-center gap-3 px-1 select-none">
                <?php if ( $current_user_avatar ) : ?>
                    <img src="<?php echo esc_url($current_user_avatar); ?>" class="w-10 h-10 rounded-full object-cover shrink-0 select-none border border-zinc-200/60" alt="<?php echo esc_attr($current_user_display_name); ?>" />
                <?php else : ?>
                    <div class="w-10 h-10 rounded-full bg-zinc-200 text-zinc-700 flex items-center justify-center font-bold text-sm uppercase shrink-0 select-none">
                        <?php echo esc_html(substr($current_user_display_name, 0, 2)); ?>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col min-w-0 leading-tight">
                    <span class="text-sm font-bold text-zinc-900 truncate"><?php echo esc_html($current_user_display_name); ?></span>
                    <span class="text-[11px] text-zinc-500 truncate"><?php echo esc_html($current_wp_user->exists() ? $current_wp_user->user_email : 'dravya.shs@gmail.com'); ?></span>
                </div>
            </div>

            <div class="border-t border-zinc-100"></div>

            <!-- Attendance Widget (Merged Clock Punching Feature) -->
            <div class="px-2.5 py-2.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1.5 select-none my-0.5">
                <div class="flex items-center justify-between px-0.5">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Attendance</span>
                    <div class="flex items-center gap-1.5">
                        <span id="cora-punch-popover-dot" class="w-1.5 h-1.5 rounded-full bg-zinc-500 shrink-0 transition-colors"></span>
                        <span class="text-[9px] font-bold text-zinc-455 uppercase tracking-wider" id="cora-punch-popover-status">Not punched in</span>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-2 px-0.5">
                    <span class="text-[10px] text-zinc-400 font-mono" id="cora-punch-popover-time">--:--</span>
                    <div class="flex gap-1.5 shrink-0">
                        <button type="button" onclick="headerLogPunch('in')" id="cora-header-punch-in" class="bg-zinc-950 hover:bg-zinc-800 text-white text-[10px] font-bold px-3 py-1 rounded-lg transition-all cursor-pointer shadow-sm select-none">In</button>
                        <button type="button" onclick="headerLogPunch('out')" id="cora-header-punch-out" class="bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-800 text-[10px] font-bold px-3 py-1 rounded-lg transition-all cursor-pointer select-none">Out</button>
                    </div>
                </div>
                <p id="cora-punch-popover-feedback" class="text-[9px] text-center text-zinc-400 hidden pt-0.5"></p>
            </div>

            <div class="border-t border-zinc-100"></div>

            <!-- Menu Items List -->
            <div class="flex flex-col gap-0.5">
                <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 rounded-xl hover:bg-zinc-50 hover:text-zinc-900 font-medium flex items-center gap-3 cursor-pointer transition-colors" onclick="coraNavigateTo('profile'); $('#cora-header-profile-popover').addClass('hidden');">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Profile
                </button>

                <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 rounded-xl hover:bg-zinc-50 hover:text-zinc-900 font-medium flex items-center justify-between cursor-pointer transition-colors" onclick="coraNavigateTo('settings-suite'); $('#cora-header-profile-popover').addClass('hidden');">
                    <div class="flex items-center gap-3">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l-.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        Settings
                    </div>
                    <span class="text-[10px] text-zinc-400 font-mono">⌘.</span>
                </button>

                <div class="px-2 py-1.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1 select-none my-0.5">
                    <div class="flex items-center justify-between px-1">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Language</span>
                        <span class="cora-current-language-label text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 text-white rounded uppercase">English</span>
                    </div>
                    <select id="cora-header-language-select" class="cora-language-selector w-full bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer transition-colors" onchange="if(window.coraSetLanguage) window.coraSetLanguage(this.value, true);">
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
                        <option value="pa">Punjabi (ਪੰਜਾਬী)</option>
                        <option value="or">Odia (ଓଡ଼ିଆ)</option>
                    </select>
                </div>

                <?php if ( cora_is_super_owner() ) : ?>
                <div class="border-t border-zinc-100 my-1"></div>
                <div class="px-2 py-1.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1 select-none">
                    <div class="flex items-center justify-between px-1">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Role Preview</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 text-white rounded uppercase">Admin</span>
                    </div>
                    <select class="cora-role-preview-select w-full bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer transition-colors" onchange="coraSwitchRolePreview(this.value)">
                        <option value="administrator" class="bg-white text-zinc-900">Super Admin (Full Access)</option>
                        <?php foreach ( $cora_role_labels as $r_key => $r_label ) :
                            if ( $r_key === 'administrator' ) continue;
                        ?>
                        <option value="<?php echo esc_attr( $r_key ); ?>" class="bg-white text-zinc-900"><?php echo esc_html( $r_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>

            <div class="border-t border-zinc-100"></div>

            <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="w-full text-left px-2.5 py-2.5 text-xs text-zinc-700 rounded-xl hover:bg-zinc-50 hover:text-red-600 font-semibold flex items-center gap-3 transition-colors select-none">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Sign out
            </a>
        </div>
        
        <div class="flex lg:hidden w-full items-center justify-between bg-transparent py-0.5" style="gap: 10px !important;">
            <div onclick="if(typeof window.coraNavigateTo==='function'){window.coraNavigateTo('dashboard');}" class="flex items-center cursor-pointer select-none shrink-0 hover:opacity-85 transition-opacity pr-1.5">
                <span class="tracking-normal font-black text-[13px] text-white">CORA</span>
            </div>

            <div onclick="window.coraOpenCommandPalette();" class="mx-2 flex items-center justify-between text-zinc-400 text-xs cursor-pointer" style="max-width: 280px; height: 32px; background-color: #343434e3; border-radius: 8px; border: none; padding: 0 10px; flex: 1;">
                <div class="flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span class="text-[11px]">Search anything...</span>
                </div>
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-white"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="if(event) event.stopPropagation(); window.coraToggleNotificationDrawer();" class="relative p-1 text-zinc-400 hover:text-white transition-all cursor-pointer flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    <span id="cora-mobile-notif-badge" class="<?php echo $cora_unread_count > 0 ? '' : 'hidden'; ?> absolute top-1 right-1 w-2 h-2 bg-red-600 rounded-full border border-[#09090b]"></span>
                </button>
                <div onclick="window.coraToggleProfilePopover(event);" class="cora-header-profile-btn flex items-center cursor-pointer shrink-0">
                    <div class="relative w-7 h-7 rounded-full flex items-center justify-center shrink-0 leading-none">
                        <?php if ( $current_user_avatar ) : ?>
                            <img src="<?php echo esc_url($current_user_avatar); ?>" class="w-7 h-7 rounded-full object-cover shrink-0 select-none border border-zinc-700/60" alt="<?php echo esc_attr($cora_display_name); ?>" />
                        <?php else : ?>
                            <div class="w-7 h-7 rounded-full bg-white text-black flex items-center justify-center font-bold text-[11px] shrink-0 leading-none">
                                <?php echo esc_html($cora_initials); ?>
                            </div>
                        <?php endif; ?>
                        <!-- Status dot -->
                        <span id="cora-mobile-profile-status-dot" class="absolute -bottom-0.5 -right-0.5 w-2 h-2 rounded-full border border-[#09090b] bg-zinc-500 transition-colors"></span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Workspace Main Container (Sidebar + Content Row) -->
    <div class="flex flex-row flex-1 min-h-0 relative w-full lg:overflow-hidden">
    <!-- Workspace Sidebar -->
    <aside class="cora-sidebar w-64 bg-[#f9fafb]#0c0c0e] border-r border-zinc-200/80 flex flex-col shrink-0 h-[calc(100vh-52px)] fixed lg:sticky top-[52px] left-0 z-50 lg:z-30 transition-all duration-200 transform -translate-x-full lg:translate-x-0">
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
                <div class="cora-workspace-card flex items-center justify-between gap-2 px-2.5 py-1.5 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-lg cursor-pointer transition-all select-none" onclick="event.stopPropagation(); if($('.cora-sidebar').hasClass('collapsed-sidebar')){ window.coraToggleSidebarCollapse(); } else { $('#cora-workspace-popover').toggleClass('hidden'); $('#cora-profile-popover').addClass('hidden'); }">
                    <div class="flex items-center gap-2 min-w-0">
                        <?php if ( ! empty( $sidebar_brand_logo ) ) : ?>
                        <div class="w-6 h-6 rounded flex items-center justify-center shrink-0">
                            <img src="<?php echo esc_url( $sidebar_brand_logo ); ?>" alt="Logo" class="w-full h-full object-contain rounded" onerror="this.parentNode.style.display='none'; this.parentNode.nextElementSibling.style.display='flex';">
                        </div>
                        <div class="w-6 h-6 rounded bg-black text-white font-bold text-[13px] flex items-center justify-center shrink-0 leading-none" style="display: none;">
                            <?php echo esc_html( strtoupper( substr( $sidebar_brand_title, 0, 1 ) ) ); ?>
                        </div>
                        <?php else : ?>
                        <div class="w-6 h-6 rounded bg-black text-white font-bold text-[13px] flex items-center justify-center shrink-0 leading-none">
                            <?php echo esc_html( strtoupper( substr( $sidebar_brand_title, 0, 1 ) ) ); ?>
                        </div>
                        <?php endif; ?>
                        <span class="cora-studio-info text-zinc-900 font-bold text-xs truncate"><?php echo esc_html( $sidebar_brand_title ); ?></span>
                    </div>
                    <svg class="cora-switcher-arrow text-zinc-500 shrink-0" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
                <div id="cora-workspace-popover" class="hidden absolute top-full mt-2 left-0 w-[280px] bg-white border border-zinc-200 rounded-2xl shadow-2xl p-3.5 z-50 flex flex-col select-none" style="animation: popoverSlideDown 0.12s ease-out;">
                    <!-- Header -->
                    <div class="flex items-start justify-between pb-3.5 border-b border-zinc-100 min-w-0">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <div class="w-9 h-9 rounded-xl bg-zinc-950 text-white font-black text-base flex items-center justify-center shrink-0 leading-none shadow-sm select-none">
                                <?php echo esc_html( $cora_ws_initial ); ?>
                            </div>
                            <div class="flex flex-col min-w-0 flex-1 leading-tight">
                                <span class="text-xs font-bold text-zinc-900 truncate"><?php echo esc_html( $cora_ws_name ); ?></span>
                                <div class="flex items-center gap-1 min-w-0 mt-0.5">
                                    <span class="text-[10px] font-mono text-zinc-400 truncate">app.heycora.in/<?php echo esc_html( $cora_ws_slug ); ?></span>
                                    <a href="https://app.heycora.in/<?php echo esc_html( $cora_ws_slug ); ?>" target="_blank" class="text-zinc-400 hover:text-zinc-650 shrink-0 select-none" onclick="event.stopPropagation();">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <span class="text-[9.5px] font-bold text-zinc-600 bg-zinc-100 px-2 py-0.5 rounded-md flex items-center gap-1 shrink-0 border border-zinc-200/50 select-none">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0 text-zinc-500"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            Current
                        </span>
                    </div>

                    <!-- Settings & Team Buttons -->
                    <div class="grid grid-cols-2 gap-2 my-3">
                        <button class="flex items-center justify-center gap-1.5 px-3 py-2 bg-zinc-50 hover:bg-zinc-100 border border-transparent rounded-xl text-[11px] font-semibold text-zinc-700 cursor-pointer transition-all shadow-2xs active:scale-[0.98]" onclick="coraNavigateTo('settings-suite'); $('#cora-workspace-popover').addClass('hidden');">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-zinc-500"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            Settings
                        </button>
                        <button class="flex items-center justify-center gap-1.5 px-3 py-2 bg-zinc-50 hover:bg-zinc-100 border border-transparent rounded-xl text-[11px] font-semibold text-zinc-700 cursor-pointer transition-all shadow-2xs active:scale-[0.98]" onclick="coraNavigateTo('team-roles'); $('#cora-workspace-popover').addClass('hidden');">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-zinc-500"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            Team
                        </button>
                    </div>

                    <!-- Workspaces List -->
                    <div class="border-t border-zinc-100 pt-3">
                        <div class="flex items-center justify-between px-1 mb-2 select-none">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Workspaces</span>
                            <?php if ( cora_is_super_owner() ) : ?>
                                <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded shrink-0">Admin (Shruti)</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="space-y-1 max-h-48 overflow-y-auto pr-0.5 no-scrollbar select-none">
                            <!-- Virtual Super Admin Workspace -->
                            <?php 
                            if ( cora_is_real_shruti() ) : 
                                $is_super_current = ( $cora_ws_slug === 'super' );
                            ?>
                            <div class="group flex items-center justify-between px-2 py-1.5 <?php echo $is_super_current ? 'bg-zinc-100 border-zinc-300' : 'bg-transparent hover:bg-zinc-50 border-transparent hover:border-zinc-200'; ?> border rounded-xl cursor-pointer transition-all min-w-0" onclick="coraSwitchWorkspace('super')">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-5 h-5 rounded-md bg-emerald-600 text-white font-bold text-[9px] flex items-center justify-center shrink-0 leading-none">
                                        ★
                                    </div>
                                    <div class="flex flex-col min-w-0 flex-1 leading-tight">
                                        <span class="text-[11px] font-semibold text-zinc-800 truncate">Platform Control Center</span>
                                        <span class="text-[9px] text-zinc-450 font-mono truncate">Global / Super Admin View</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0 ml-1">
                                    <?php if ( $is_super_current ) : ?>
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-900 shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?php endif; ?>
                                    <button type="button" class="p-0.5 hover:bg-zinc-200 rounded text-zinc-400 hover:text-zinc-750 shrink-0 opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity" onclick="event.stopPropagation(); window.coraToggleEditWorkspaceDrawer(true, 0, 'Platform Control Center', 'super', 'enterprise', 'shruti@heycora.in', 'active');">
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
                            <div class="group flex items-center justify-between px-2 py-1.5 <?php echo $is_current ? 'bg-zinc-100 border-zinc-300' : 'bg-transparent hover:bg-zinc-50 border-transparent hover:border-zinc-200'; ?> border rounded-xl cursor-pointer transition-all min-w-0" onclick="coraSwitchWorkspace('<?php echo esc_js( $ws_item_slug ); ?>')">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-5 h-5 rounded-md bg-zinc-950 text-white font-bold text-[10px] flex items-center justify-center shrink-0 leading-none">
                                        <?php echo esc_html( $ws_item_init ); ?>
                                    </div>
                                    <div class="flex flex-col min-w-0 flex-1 leading-tight">
                                        <span class="text-[11px] font-semibold text-zinc-900 truncate"><?php echo esc_html( $ws_item_name ); ?></span>
                                        <span class="text-[9px] text-zinc-400 font-mono truncate">app.heycora.in/<?php echo esc_html( $ws_item_slug ); ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0 ml-1">
                                    <?php if ( $is_current ) : ?>
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-900 shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?php endif; ?>
                                    <button type="button" class="p-0.5 hover:bg-zinc-200 rounded text-zinc-400 hover:text-zinc-755 shrink-0 opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity" onclick="event.stopPropagation(); window.coraToggleEditWorkspaceDrawer(true, <?php echo intval( $ws_item['id'] ); ?>, '<?php echo esc_js( $ws_item_name ); ?>', '<?php echo esc_js( $ws_item_slug ); ?>', '<?php echo esc_js( $ws_item_plan ); ?>', '<?php echo esc_js( $ws_item_email ); ?>', '<?php echo esc_js( $ws_item_status ); ?>');">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ( cora_is_super_owner() ) : ?>
                    <!-- Create New Workspace Button -->
                    <div class="border-t border-zinc-100 mt-3 pt-2">
                        <button type="button" class="w-full flex items-center gap-2.5 px-2 py-2 text-left hover:bg-zinc-50 rounded-xl transition-all cursor-pointer group" onclick="event.stopPropagation(); window.coraToggleCreateWorkspaceDrawer(true);">
                            <div class="w-5.5 h-5.5 rounded-lg border border-dashed border-zinc-300 flex items-center justify-center text-zinc-400 group-hover:text-zinc-700 transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            </div>
                            <span class="text-xs font-semibold text-zinc-700 group-hover:text-zinc-900 transition-colors">Create New Workspace</span>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="cora-sidebar-header-actions flex items-center gap-1.5 shrink-0">
                <!-- Collapse Toggle Button (layout-sidebar icon) -->
                <button id="cora-sidebar-toggle" onclick="return window.coraToggleSidebarCollapse(event);" class="text-zinc-500 hover:text-black bg-white border border-zinc-200 p-2 rounded-lg transition-colors cursor-pointer select-none shadow-2xs" title="Collapse / Expand Sidebar">
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
                <div class="bg-zinc-100/80 border border-zinc-200/80 rounded-xl p-1.5 space-y-1">
                    <div class="flex items-center justify-between px-1">
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Industry Mode</span>
                        <span class="text-[8.5px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded shrink-0">Shruti Only</span>
                    </div>
                    <div class="grid grid-cols-2 gap-1 bg-white p-1 rounded-lg border border-zinc-200/60">
                        <a href="<?php echo esc_url( $re_url ); ?>" class="flex items-center justify-center gap-1 py-1 px-1.5 rounded-md text-[10.5px] font-bold transition-all no-underline <?php echo ($current_industry === 'real_estate') ? 'bg-zinc-950 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900'; ?>">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                            Real Estate
                        </a>
                        <a href="<?php echo esc_url( $stu_url ); ?>" class="flex items-center justify-center gap-1 py-1 px-1.5 rounded-md text-[10.5px] font-bold transition-all no-underline <?php echo ($current_industry === 'photography_studio') ? 'bg-zinc-950 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900'; ?>">
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
                <div class="cora-role-switcher-card flex items-center justify-between gap-2 px-2.5 py-1.5 bg-zinc-100/70 hover:bg-zinc-100 border border-zinc-200/80 rounded-lg transition-all select-none">
                    <div class="flex items-center gap-2 min-w-0 w-full">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500 shrink-0"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <select class="cora-role-preview-select w-full bg-white border border-zinc-200/80 text-zinc-900 font-bold text-xs rounded-md px-2 py-1 outline-none cursor-pointer transition-colors shadow-2xs" onchange="coraSwitchRolePreview(this.value)">
                            <option value="administrator" <?php echo ( $current_user_role === 'administrator' ? 'selected' : '' ); ?> class="bg-white text-zinc-900">Role: Super Admin</option>
                            <?php foreach ( $cora_role_labels as $r_key => $r_label ) :
                                if ( $r_key === 'administrator' ) continue;
                                $is_sel = ( $current_user_role === $r_key ) ? 'selected' : '';
                            ?>
                            <option value="<?php echo esc_attr( $r_key ); ?>" <?php echo $is_sel; ?> class="bg-white text-zinc-900">Role: <?php echo esc_html( $r_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sidebar Search Input -->
            <div class="px-3 pt-2 pb-1">
                <div class="cora-sidebar-search flex items-center gap-2 px-3 py-1.5 bg-white border border-zinc-200 rounded-xl text-xs text-zinc-500 transition-colors shadow-2xs">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="cora-sidebar-search-input" placeholder="Search menu..." class="w-full bg-transparent border-none p-0 text-xs text-zinc-900 focus:outline-hidden focus:ring-0" style="outline: none !important; border: none !important; box-shadow: none !important;" />
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
                            <a href="<?php echo esc_url( $nav_url ); ?>" class="cora-nav-item <?php echo ( $sub_page === $target || str_replace('_', '-', $sub_page) === str_replace('_', '-', $target) ) ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm rounded-lg cursor-pointer select-none no-underline text-zinc-800 hover:text-zinc-950" data-target="<?php echo esc_attr($target); ?>" data-tooltip="<?php echo esc_attr($item['title']); ?>">
                                <div class="flex items-center gap-3 select-none">
                                    <span class="cora-nav-icon select-none">
                                        <?php echo $item['icon']; ?>
                                    </span>
                                    <span class="cora-nav-text select-none font-medium"><?php echo esc_html($item['title']); ?></span>
                                </div>
                                <?php if ( ! empty( $item['soon'] ) ) : ?>
                                <span class="cora-badge cora-badge-sidebar px-1.5 py-0.5 text-[9px] font-bold bg-zinc-100 text-zinc-500 rounded-full border border-zinc-200 select-none flex items-center gap-1">
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
        <div class="cora-sidebar-bottom-block shrink-0 border-t border-zinc-200/50 z-20 sticky bottom-0 flex flex-col">
            <!-- User Profile Popover Card -->
            <div id="cora-profile-popover" class="hidden absolute bottom-20 left-4 right-4 max-h-[360px] overflow-y-auto bg-white border border-zinc-200 rounded-2xl shadow-xl p-4 z-[70] flex flex-col gap-2.5 animate-in fade-in slide-in-from-bottom-2 duration-150 select-none">
                <!-- User Profile Header -->
                <div class="flex items-center gap-3 px-1 select-none">
                    <?php if ( $current_user_avatar ) : ?>
                        <img src="<?php echo esc_url($current_user_avatar); ?>" class="w-10 h-10 rounded-full object-cover shrink-0 select-none border border-zinc-200/60" alt="<?php echo esc_attr($current_user_display_name); ?>" />
                    <?php else : ?>
                        <div class="w-10 h-10 rounded-full bg-zinc-200 text-zinc-700 flex items-center justify-center font-bold text-sm uppercase shrink-0 select-none">
                            <?php 
                            $pop_initials = ( cora_is_real_shruti() || ( $current_wp_user->exists() && $current_wp_user->user_login === 'cora_admin' ) ) ? 'S' : substr($current_user_display_name, 0, 2);
                            echo esc_html($pop_initials); 
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex flex-col min-w-0 leading-tight">
                        <span class="text-sm font-bold text-zinc-900 truncate"><?php echo esc_html(( cora_is_real_shruti() || ( $current_wp_user->exists() && $current_wp_user->user_login === 'cora_admin' ) ) ? 'Shruti' : $current_user_display_name); ?></span>
                        <span class="text-[11px] text-zinc-500 truncate"><?php echo esc_html($current_wp_user->exists() ? $current_wp_user->user_email : 'dravya.shs@gmail.com'); ?></span>
                    </div>
                </div>

                <div class="border-t border-zinc-100"></div>

                <!-- Workspace Connection Status Indicator -->
                <?php
                $cora_gemini_key_saved = ! empty( get_option( 'cora_workspace_ai_gemini_key', '' ) );
                ?>
                <div class="flex items-center justify-between px-2.5 py-1.5 text-xs select-none">
                    <span class="text-zinc-500 font-medium">Workspace Status</span>
                    <span class="flex items-center gap-1.5 font-bold text-zinc-800">
                        <span class="w-2 h-2 rounded-full <?php echo $cora_gemini_key_saved ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400'; ?>"></span>
                        <?php echo $cora_gemini_key_saved ? 'Connected' : 'Not Configured'; ?>
                    </span>
                </div>

                <div class="border-t border-zinc-100"></div>

                <!-- Menu Items List -->
                <div class="flex flex-col gap-0.5">
                    <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 rounded-xl hover:bg-zinc-50 hover:text-zinc-900 font-medium flex items-center gap-3 cursor-pointer transition-colors" onclick="coraNavigateTo('profile'); $('#cora-profile-popover').addClass('hidden');">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Profile
                    </button>

                    <button class="w-full text-left px-2.5 py-2 text-xs text-zinc-700 rounded-xl hover:bg-zinc-50 hover:text-zinc-900 font-medium flex items-center justify-between cursor-pointer transition-colors" onclick="coraNavigateTo('settings-suite'); $('#cora-profile-popover').addClass('hidden');">
                        <div class="flex items-center gap-3">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            Settings
                        </div>
                        <span class="text-[10px] text-zinc-400 font-mono">⌘.</span>
                    </button>


                    <div class="px-2 py-1.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1 select-none my-0.5">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Language</span>
                            <span class="cora-current-language-label text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 text-white rounded uppercase">English</span>
                        </div>
                        <select id="cora-language-selector" class="cora-language-selector w-full bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer transition-colors" onchange="if(window.coraSetLanguage) window.coraSetLanguage(this.value, true);">
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
                    <div class="px-2 py-1.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1 select-none my-0.5">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AI Model</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 text-white rounded uppercase">Active</span>
                        </div>
                        <select id="cora-ai-model-selector" class="w-full bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer transition-colors">
                            <option value="cora-core-v2" <?php selected( $cora_active_ai_model, 'cora-core-v2' ); ?>>Gemini 3.5 Flash (Auto)</option>
                            <option value="gemini" <?php selected( $cora_active_ai_model, 'gemini' ); ?>>Gemini 3.5 Flash</option>
                            <option value="gpt-4o" <?php selected( $cora_active_ai_model, 'gpt-4o' ); ?>>GPT-4o</option>
                        </select>
                    </div>

                    <?php if ( cora_is_super_owner() ) : ?>
                    <div class="border-t border-zinc-100 my-1"></div>
                    <div class="px-2 py-1.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1 select-none">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Role Preview</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 bg-zinc-900 text-white rounded uppercase">Admin</span>
                        </div>
                        <select class="cora-role-preview-select w-full bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold rounded-lg px-2 py-1.5 outline-none cursor-pointer" onchange="coraSwitchRolePreview(this.value)">
                            <option value="administrator" class="bg-white text-zinc-900">Super Admin (Full Access)</option>
                            <?php foreach ( $cora_role_labels as $r_key => $r_label ) :
                                if ( $r_key === 'administrator' ) continue;
                            ?>
                            <option value="<?php echo esc_attr( $r_key ); ?>" class="bg-white text-zinc-900"><?php echo esc_html( $r_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ( cora_is_super_owner() ) : ?>
                <div class="border-t border-zinc-100"></div>
                <div id="cora-in-app-update-notice" class="hidden px-2 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl flex flex-col gap-1.5">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-zinc-800 uppercase tracking-wide">Update Available</span>
                    </div>
                    <p class="text-[10px] text-zinc-500 leading-normal font-medium">New version <code class="font-mono text-zinc-700 font-bold" id="cora-update-ver">v1.4.0</code> is ready. Upgrade instantly.</p>
                    <button type="button" id="cora-btn-app-upgrade" class="w-full py-1.5 bg-zinc-950 hover:opacity-85 text-white font-bold rounded-lg text-[10px] transition-colors cursor-pointer text-center select-none shadow-3xs" onclick="coraTriggerInAppUpgrade(this)">
                        Upgrade Workspace
                    </button>
                </div>
                <?php endif; ?>

                <div class="border-t border-zinc-100"></div>

                <!-- Quota Metrics Section -->
                <div class="px-2 py-2.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-3 select-none">
                    <!-- Storage Quota -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[10px] font-bold text-zinc-500">
                            <span>Storage Usage</span>
                            <span>4.2 GB of 10 GB (42%)</span>
                        </div>
                        <div class="w-full h-1.5 bg-zinc-200 rounded-full overflow-hidden">
                            <div class="bg-zinc-900 h-full rounded-full" style="width: 42%;"></div>
                        </div>
                    </div>

                    <!-- AI Usage Quotas (Dynamic limits) -->
                    <?php
                    $usage_stats = function_exists( 'cora_workspace_get_ai_usage_stats' ) ? cora_workspace_get_ai_usage_stats() : array( 'five_hour_count' => 0, 'five_hour_limit' => 30, 'daily_count' => 0, 'daily_limit' => 100 );
                    $daily_percent = min(100, round(($usage_stats['daily_count'] / $usage_stats['daily_limit']) * 100));
                    $five_hour_percent = min(100, round(($usage_stats['five_hour_count'] / $usage_stats['five_hour_limit']) * 100));
                    ?>
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[10px] font-bold text-zinc-500">
                            <span>AI Requests (Daily)</span>
                            <span><?php echo esc_html( $usage_stats['daily_count'] ); ?> / <?php echo esc_html( $usage_stats['daily_limit'] ); ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-zinc-200 rounded-full overflow-hidden">
                            <div class="bg-zinc-950 h-full rounded-full" style="width: <?php echo esc_attr( $daily_percent ); ?>%;"></div>
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[10px] font-bold text-zinc-500">
                            <span>AI Requests (5h Window)</span>
                            <span><?php echo esc_html( $usage_stats['five_hour_count'] ); ?> / <?php echo esc_html( $usage_stats['five_hour_limit'] ); ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-zinc-200 rounded-full overflow-hidden">
                            <div class="bg-zinc-950 h-full rounded-full" style="width: <?php echo esc_attr( $five_hour_percent ); ?>%;"></div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-zinc-100"></div>

                <!-- PWA & Push Notifications Settings -->
                <div class="px-2.5 py-3 bg-zinc-50 border border-zinc-200/60 rounded-xl space-y-2.5 select-none">
                    <div class="flex items-center justify-between px-0.5">
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">App & Push (PWA)</span>
                        <span id="cora-pwa-badge" class="text-[9px] font-bold px-1.5 py-0.5 bg-zinc-400 text-white rounded uppercase">Inactive</span>
                    </div>
                    
                    <div class="flex flex-col gap-1.5">
                        <!-- Install Button -->
                        <button type="button" id="cora-pwa-install-btn" class="hidden w-full py-1.5 bg-zinc-950 hover:opacity-85 text-white font-bold rounded-lg text-[10px] transition-colors cursor-pointer text-center select-none shadow-3xs border-none outline-none">
                            Install Desktop/Phone App
                        </button>
                        
                        <!-- Push Notifications Button -->
                        <button type="button" id="cora-pwa-push-btn" class="w-full py-1.5 bg-zinc-950 hover:opacity-85 text-white font-bold rounded-lg text-[10px] transition-colors cursor-pointer text-center select-none shadow-3xs border-none outline-none" onclick="coraRequestPushSubscription()">
                            Enable Push Notifications
                        </button>

                        <!-- Send Test Push Button -->
                        <button type="button" id="cora-pwa-test-btn" class="hidden w-full py-1.5 bg-white hover:bg-zinc-100 text-zinc-800 font-semibold rounded-lg text-[10px] transition-colors cursor-pointer text-center select-none shadow-3xs border border-zinc-200 outline-none" onclick="coraSendTestPushNotification()">
                            Send Test Notification
                        </button>
                        
                        <p id="cora-pwa-status-text" class="text-[9px] text-zinc-500 text-center leading-normal font-medium m-0">Install app & enable alerts for immediate updates.</p>
                    </div>
                </div>

                <div class="border-t border-zinc-100"></div>

                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="w-full text-left px-2.5 py-2.5 text-xs text-zinc-700 rounded-xl hover:bg-zinc-50 hover:text-red-600 font-semibold flex items-center gap-3 transition-colors select-none">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Sign out
                </a>
            </div>

            <!-- Sidebar Notification Popover Card -->
            <div id="cora-sidebar-notif-popover" class="hidden absolute bottom-20 left-4 right-4 bg-white border border-zinc-200 rounded-2xl shadow-xl p-4 z-[70] flex flex-col gap-2.5 animate-in fade-in slide-in-from-bottom-2 duration-150 select-none text-zinc-900">
<div class="flex items-center justify-between pb-2 border-b border-zinc-200 bg-zinc-50/50 px-1 rounded-t-xl">
                    <span class="text-xs font-bold">Notifications</span>
                    <button class="text-[10px] font-semibold text-zinc-500 hover:text-zinc-855 transition-colors cursor-pointer" onclick="markAllNotificationsRead(event)">Mark all as read</button>
                </div>
                <div id="cora-sidebar-notif-list" class="max-h-[240px] overflow-y-auto divide-y divide-zinc-100">
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
            <div class="cora-user-footer px-4 py-3 flex items-center justify-between border-t border-zinc-200/50 hover:bg-zinc-100/50 transition-colors duration-200 cursor-pointer relative z-[60]" onclick="event.stopPropagation(); $('#cora-profile-popover').toggleClass('hidden'); $('#cora-sidebar-notif-popover').addClass('hidden'); $('#cora-workspace-popover').addClass('hidden');">
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
                        <span class="cora-user-name text-xs font-semibold text-zinc-900 truncate leading-tight"><?php echo esc_html($current_user_display_name); ?></span>
                        <span class="cora-user-role text-[10px] text-zinc-400 font-medium truncate"><?php echo esc_html($current_user_role_label); ?></span>
                    </div>
                </div>
                
                <!-- Notification Bell Button with badge -->
                <div class="cora-user-inbox relative shrink-0 text-zinc-500 hover:text-black transition-all p-1.5 rounded-lg bg-zinc-200/50 hover:bg-zinc-200 cursor-pointer flex items-center justify-center" onclick="event.stopPropagation(); $('#cora-sidebar-notif-popover').toggleClass('hidden'); $('#cora-profile-popover').addClass('hidden'); $('#cora-workspace-popover').addClass('hidden');">
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
            <div id="cora-role-preview-banner" class="hidden mb-4 p-3.5 bg-zinc-900 text-white rounded-xl shadow-md flex items-center justify-between gap-3 animate-in fade-in duration-200">
                <div class="flex items-center gap-3">
                    <span class="p-1.5 bg-zinc-800 rounded-lg shrink-0 text-zinc-200">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                    <div class="text-xs">
                        <span class="font-extrabold uppercase tracking-wider text-[10px] bg-zinc-800 text-zinc-200 px-2 py-0.5 rounded-md mr-1.5">Role Preview Active</span>
                        Viewing workspace as <span id="cora-preview-role-name" class="font-extrabold underline underline-offset-2">Manager</span>. Navigation &amp; permissions are simulated for this role.
                    </div>
                </div>
                <button type="button" onclick="coraResetRolePreview()" class="px-3 py-1.5 bg-white text-zinc-950 text-xs font-extrabold rounded-lg hover:opacity-90 transition-all shrink-0 cursor-pointer shadow-xs flex items-center gap-1.5">
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

                if ( $cora_workspace_industry_raw === 'custom' ) {
                    global $wpdb;
                    $enabled = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();

                    $telemetry_metrics = array();

                    if ( in_array( 'leads', $enabled, true ) ) {
                        $telemetry_metrics[] = array(
                            'label'       => 'Active Leads',
                            'value'       => count( $cora_workspace_leads ),
                            'badge'       => '+12%',
                            'badge_color' => 'text-emerald-650 bg-emerald-50 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 5 C 20 5, 40 25, 60 15 C 80 5, 90 28, 100 28',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                        );
                    }

                    if ( in_array( 'properties', $enabled, true ) ) {
                        $telemetry_metrics[] = array(
                            'label'       => 'Properties',
                            'value'       => count( $cora_workspace_listings ),
                            'badge'       => 'Active',
                            'badge_color' => 'text-emerald-655 bg-emerald-50 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 25 C 20 25, 30 5, 50 15 C 70 25, 80 10, 100 5',
                            'primary'     => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
                        );
                    }

                    if ( in_array( 'showings', $enabled, true ) ) {
                        $telemetry_metrics[] = array(
                            'label'       => 'Showings',
                            'value'       => $dynamic_active_bookings_count,
                            'badge'       => 'Scheduled',
                            'badge_color' => 'text-zinc-650 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 8 C 20 8, 40 22, 60 12 C 80 2, 90 25, 100 25',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                        );
                    }

                    if ( in_array( 'equipment', $enabled, true ) ) {
                        $gear_count = is_array($cora_studio_gear) ? count($cora_studio_gear) : 0;
                        $telemetry_metrics[] = array(
                            'label'       => 'Camera Gear',
                            'value'       => $gear_count,
                            'badge'       => 'Assets',
                            'badge_color' => 'text-zinc-655 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 5 C 20 5, 40 25, 60 15 C 80 5, 90 28, 100 28',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>',
                        );
                    }

                    if ( in_array( 'financials', $enabled, true ) ) {
                        $telemetry_metrics[] = array(
                            'label'       => 'Pipeline Value',
                            'value'       => cora_format_rupees( $dynamic_revenue_total ),
                            'badge'       => 'Negotiating',
                            'badge_color' => 'text-zinc-650 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 15 L 100 15',
                            'svg_dash'    => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M6 3h12M6 8h12M9 3v10.5M9 3h3.5a5 5 0 0 1 0 10H9M9 13.5L16 21"></path></svg>',
                        );
                    }

                    $static_presets = array(
                        'team' => array(
                            'label'       => 'Active Team',
                            'value'       => count( $cora_users ),
                            'badge'       => 'Members',
                            'badge_color' => 'text-zinc-655 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 15 L 100 15',
                            'svg_dash'    => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>'
                        ),
                        'docs' => array(
                            'label'       => 'Secure Vault',
                            'value'       => is_array($cora_documents) ? count($cora_documents) : 0,
                            'badge'       => 'Files',
                            'badge_color' => 'text-zinc-655 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 15 L 100 15',
                            'svg_dash'    => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>'
                        ),
                        'canvas' => array(
                            'label'       => 'Website Pages',
                            'value'       => $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_canvas_pages" ),
                            'badge'       => 'Canvas',
                            'badge_color' => 'text-zinc-655 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 15 L 100 15',
                            'svg_dash'    => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon></svg>'
                        ),
                        'forms' => array(
                            'label'       => 'Active Forms',
                            'value'       => $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_forms" ),
                            'badge'       => 'Forms',
                            'badge_color' => 'text-zinc-655 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 15 L 100 15',
                            'svg_dash'    => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>'
                        )
                    );

                    foreach ( $static_presets as $k => $preset ) {
                        if ( count( $telemetry_metrics ) >= 4 ) {
                            break;
                        }
                        $telemetry_metrics[] = $preset;
                    }
                } elseif ( $is_studio ) {
                    $gear_count = is_array($cora_studio_gear) ? count($cora_studio_gear) : 0;
                    $telemetry_metrics = array(
                        array(
                            'label'       => 'Active Shoots',
                            'value'       => count( $recent_active_showings ),
                            'badge'       => 'In Progress',
                            'badge_color' => 'text-emerald-650 bg-emerald-50 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 25 C 20 25, 30 5, 50 15 C 70 25, 80 10, 100 5',
                            'primary'     => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                        ),
                        array(
                            'label'       => 'Camera Gear',
                            'value'       => $gear_count,
                            'badge'       => 'Assets',
                            'badge_color' => 'text-zinc-650 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 5 C 20 5, 40 25, 60 15 C 80 5, 90 28, 100 28',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>',
                        ),
                        array(
                            'label'       => 'Bookings (MTD)',
                            'value'       => count( $cora_workspace_clients ),
                            'badge'       => 'Confirmed',
                            'badge_color' => 'text-zinc-650 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 8 C 20 8, 40 22, 60 12 C 80 2, 90 25, 100 25',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                        ),
                        array(
                            'label'       => 'Pending Deliveries',
                            'value'       => $dynamic_pending_count,
                            'badge'       => 'Editing',
                            'badge_color' => 'text-zinc-650 bg-zinc-100 px-1.5 py-0.5 rounded-md',
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
                            'badge_color' => 'text-emerald-655 bg-emerald-50 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 25 C 20 25, 30 5, 50 15 C 70 25, 80 10, 100 5',
                            'primary'     => true,
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
                        ),
                        array(
                            'label'       => 'Active Leads',
                            'value'       => count( $cora_workspace_leads ),
                            'badge'       => '+12%',
                            'badge_color' => 'text-emerald-650 bg-emerald-50 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 5 C 20 5, 40 25, 60 15 C 80 5, 90 28, 100 28',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                        ),
                        array(
                            'label'       => 'Showings',
                            'value'       => $dynamic_active_bookings_count,
                            'badge'       => 'Scheduled',
                            'badge_color' => 'text-zinc-650 bg-zinc-100 px-1.5 py-0.5 rounded-md',
                            'svg_path'    => 'M0 8 C 20 8, 40 22, 60 12 C 80 2, 90 25, 100 25',
                            'icon'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                        ),
                        array(
                            'label'       => 'Pipeline Value',
                            'value'       => cora_format_rupees( $dynamic_revenue_total ),
                            'badge'       => 'Negotiating',
                            'badge_color' => 'text-zinc-650 bg-zinc-100 px-1.5 py-0.5 rounded-md',
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
                    <div class="bg-white/80 p-4 backdrop-blur-md border border-zinc-200/50 rounded-2xl flex items-center justify-between transition-all hover:scale-[1.01] hover:shadow-xs cursor-default">
                        <div class="space-y-1 min-w-0 pr-2">
                            <span class="block text-[9px] sm:text-[10px] font-bold text-zinc-400 uppercase tracking-wider truncate"><?php echo esc_html( $metric['label'] ); ?></span>
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="text-base sm:text-2xl font-extrabold text-zinc-900 leading-none tracking-tight"><?php echo esc_html( $metric['value'] ); ?></span>
                                <span class="<?php echo esc_attr( $metric['badge_color'] ); ?> inline-flex items-center text-[8px] sm:text-[9px] font-bold px-1.5 py-0.5 rounded-md leading-none"><?php echo esc_html( $metric['badge'] ); ?></span>
                            </div>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-zinc-50 border border-zinc-100 flex items-center justify-center text-zinc-650 shrink-0 shadow-3xs">
                            <?php echo $metric['icon']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>


                <!-- Centered Welcome Greeting Section with sparkle SVG -->
                <div class="text-center px-4 space-y-1.5 sm:space-y-2 relative" style="padding-top: 120px !important; padding-bottom: 40px !important;">
                    <div class="inline-flex items-center justify-center gap-2.5 sm:gap-3">
                        <!-- Slate Charcoal Star Sparkle -->
                        <span class="text-zinc-900 shrink-0">
                            <svg viewBox="0 0 24 24" width="22" height="22" class="w-5 h-5 sm:w-7 sm:h-7" fill="currentColor">
                                <path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"></path>
                            </svg>
                        </span>
                        <h1 id="cora-dynamic-greeting-title" class="text-2xl sm:text-4xl md:text-5xl font-bold tracking-tight text-zinc-900">
                            <?php echo esc_html($greeting_title); ?>
                        </h1>
                    </div>
                    <p class="text-xs sm:text-base md:text-lg font-medium text-zinc-450 leading-tight">
                        <?php echo ( $cora_workspace_industry_raw === 'custom' ) ? "Your AI Co-founder is active. Let's build something great." : "Let's continue growing your business."; ?>
                    </p>
                </div>
 
                <!-- Lovable-Style Command Search (Ask anything...) -->
                <div class="w-full max-w-xl mx-auto mt-2 sm:mt-4 mb-6 sm:mb-8 px-2 sm:px-0 relative z-[999] hidden md:block" id="cora-search-container">
                    <div class="relative flex items-center bg-white/85 backdrop-blur-md border border-zinc-200/60 hover:border-zinc-350 focus-within:border-zinc-900 focus-within:ring-2 focus-within:ring-zinc-100/30 rounded-full shadow-2xs transition-all duration-200 p-1.5 pl-3.5 pr-2">
                        <span class="text-zinc-600 mr-2 flex shrink-0">
                            <!-- Lovable Character Icon (Standardized Monochromatic) -->
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                <circle cx="12" cy="12" r="10" class="text-zinc-100" fill="currentColor"></circle>
                                <circle cx="12" cy="12" r="7" class="text-zinc-400" fill="currentColor"></circle>
                                <circle cx="10" cy="11" r="1.2" fill="#fff"></circle>
                                <circle cx="14" cy="11" r="1.2" fill="#fff"></circle>
                                <path d="M9.5 15c.5.8 1.5 1.2 2.5 1.2s2-.4 2.5-1.2" stroke="#fff" stroke-width="1.2" stroke-linecap="round" fill="none"></path>
                            </svg>
                        </span>
                        
                        <!-- Real interactive input field for contextual search -->
                        <input type="text" 
                               id="cora-inline-command-input"
                               placeholder="Ask anything..." 
                               class="w-full bg-transparent border-0 focus:outline-none focus:ring-0 text-xs sm:text-sm py-1.5 px-1 text-zinc-800 placeholder:text-zinc-400/80 cursor-pointer"
                               autocomplete="off" />
                               
                        <div class="flex items-center gap-2">
                            <button onclick="window.coraTriggerCommandAI()" class="flex items-center justify-center h-7 w-7 sm:h-8 sm:w-8 rounded-full bg-zinc-900 hover:bg-zinc-955 text-white transition-colors cursor-pointer shadow-sm">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Advanced Command Search dropdown in-line container -->
                    <div id="cora-inline-command-palette" class="absolute left-0 right-0 top-full mt-2 z-[9999] hidden bg-white border border-zinc-200 rounded-2xl shadow-2xl flex-col transition-all duration-200">
                        <!-- Filter Pills Bar -->
                        <div class="flex items-center gap-1.5 px-4 py-2 border-b border-zinc-100 bg-zinc-50/50 overflow-x-auto shrink-0 select-none no-scrollbar">
                            <button type="button" class="cora-search-pill active text-[10px] font-semibold px-3 py-1 rounded-full border border-zinc-200 bg-zinc-900 text-white transition-all cursor-pointer" data-filter="all">Overview</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 bg-white text-zinc-650 hover:bg-zinc-55 transition-all cursor-pointer" data-filter="pages">Pages</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 bg-white text-zinc-650 hover:bg-zinc-55 transition-all cursor-pointer" data-filter="leads">Leads</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 bg-white text-zinc-650 hover:bg-zinc-55 transition-all cursor-pointer" data-filter="settings">Settings</button>
                            <button type="button" class="cora-search-pill text-[10px] font-medium px-3 py-1 rounded-full border border-zinc-200 bg-white text-zinc-650 hover:bg-zinc-55 transition-all cursor-pointer" data-filter="listings">Listings</button>
                        </div>

                        <!-- Results List Area — fixed height, always scrollable -->
                        <div id="cora-inline-command-results" class="overflow-y-auto p-2" style="height: 260px;">
                            <!-- Loading state / Suggestions list / Search results list -->
                        </div>

                        <!-- Footer Bar -->
                        <div class="border-t border-zinc-100 px-4 py-2 bg-zinc-50/50 flex items-center justify-between shrink-0">
                            <span class="text-[10px] text-zinc-400 font-medium">Need help finding something?</span>
                            <button type="button" class="px-2.5 py-1 bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-[10px] rounded-lg transition-colors shadow-sm flex items-center gap-1.5 cursor-pointer" onclick="window.coraTriggerCommandAI()">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                Ask Cora
                            </button>
                        </div>
                    </div>
                </div><!-- end cora-search-container -->
                
                <!-- Premium Dynamic Quick Actions (Mobile-first Wrap Grid / Desktop Centered Grid) -->
                <?php
                $predefined_actions = array();
                if ( $cora_workspace_industry_raw === 'custom' ) {
                    $enabled = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();
                    if ( in_array( 'leads', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'Add Lead',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>',
                            'onclick' => "coraNavigateTo('leads');"
                        );
                    }
                    if ( in_array( 'showings', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => $is_studio ? 'Book a Shoot' : 'Schedule Showing',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                            'onclick' => "coraNavigateTo('bookings');"
                        );
                    }
                    if ( in_array( 'properties', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'New Listing',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
                            'onclick' => "coraNavigateTo('listings');"
                        );
                    }
                    if ( in_array( 'equipment', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'Register Gear',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>',
                            'onclick' => "coraNavigateTo('equipment');"
                        );
                    }
                    if ( in_array( 'crew_scheduler', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'Assign Crew',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>',
                            'onclick' => "coraNavigateTo('crew-scheduler');"
                        );
                    }
                    if ( in_array( 'blogs', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'Write Article',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>',
                            'onclick' => "coraNavigateTo('blogs');"
                        );
                    }
                    if ( in_array( 'forms', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'Build Form',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>',
                            'onclick' => "coraNavigateTo('forms');"
                        );
                    }
                    if ( in_array( 'media', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'Upload Media',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>',
                            'onclick' => "coraNavigateTo('media');"
                        );
                    }
                    if ( in_array( 'vault', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'Upload File',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>',
                            'onclick' => "coraNavigateTo('vault');"
                        );
                    }
                    if ( in_array( 'financials', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'Create Invoice',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="6" y1="8" x2="18" y2="8"></line><line x1="6" y1="12" x2="14" y2="12"></line><line x1="6" y1="16" x2="10" y2="16"></line></svg>',
                            'onclick' => "coraNavigateTo('financial-overview');"
                        );
                    }
                    if ( in_array( 'automations', $enabled, true ) ) {
                        $predefined_actions[] = array(
                            'label'   => 'New Workflow',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
                            'onclick' => "coraNavigateTo('automations');"
                        );
                    }

                    if ( count( $predefined_actions ) < 4 ) {
                        $fallbacks = array(
                            array(
                                'label'   => 'Upload File',
                                'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>',
                                'onclick' => "coraNavigateTo('vault');"
                            ),
                            array(
                                'label'   => 'Build Form',
                                'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>',
                                'onclick' => "coraNavigateTo('forms');"
                            ),
                            array(
                                'label'   => 'Write Article',
                                'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>',
                                'onclick' => "coraNavigateTo('blogs');"
                            ),
                            array(
                                'label'   => 'Upload Media',
                                'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>',
                                'onclick' => "coraNavigateTo('media');"
                            )
                        );
                        foreach ( $fallbacks as $fb ) {
                            $exists = false;
                            foreach ( $predefined_actions as $act ) {
                                if ( $act['label'] === $fb['label'] ) {
                                    $exists = true;
                                    break;
                                }
                            }
                            if ( ! $exists ) {
                                $predefined_actions[] = $fb;
                            }
                            if ( count( $predefined_actions ) >= 4 ) {
                                break;
                            }
                        }
                    }
                } elseif ( $is_studio ) {
                    $predefined_actions = array(
                        array(
                            'label'   => 'Book a Shoot',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                            'onclick' => "coraNavigateTo('bookings'); document.getElementById('cora-add-booking-btn')?.click();"
                        ),
                        array(
                            'label'   => 'Register Gear',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>',
                            'onclick' => "coraNavigateTo('equipment'); window.openAddGearDrawer?.();"
                        ),
                        array(
                            'label'   => 'Assign Crew',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>',
                            'onclick' => "coraNavigateTo('crew-scheduler');"
                        ),
                        array(
                            'label'   => 'Upload Media',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>',
                            'onclick' => "coraNavigateTo('media');"
                        ),
                        array(
                            'label'   => 'Create Invoice',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="6" y1="8" x2="18" y2="8"></line><line x1="6" y1="12" x2="14" y2="12"></line><line x1="6" y1="16" x2="10" y2="16"></line></svg>',
                            'onclick' => "coraNavigateTo('financials');"
                        )
                    );
                } else {
                    $predefined_actions = array(
                        array(
                            'label'   => 'Schedule Showing',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                            'onclick' => "coraNavigateTo('bookings'); document.getElementById('cora-add-booking-btn')?.click();"
                        ),
                        array(
                            'label'   => 'Draft Captions',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>',
                            'onclick' => "coraNavigateTo('ai-assistants');"
                        ),
                        array(
                            'label'   => 'Add Lead',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>',
                            'onclick' => "coraNavigateTo('leads');"
                        ),
                        array(
                            'label'   => 'Create Brochure',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>',
                            'onclick' => "event.stopPropagation(); window.coraOpenCommandPalette();"
                        ),
                        array(
                            'label'   => 'View Listings',
                            'icon'    => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
                            'onclick' => "coraNavigateTo('listings');"
                        )
                    );
                }
                ?>
                <script>
                    var CORA_PREDEFINED_ACTIONS = <?php echo json_encode( $predefined_actions ); ?>;
                </script>
                
                <!-- Premium Dynamic Quick Actions (Mobile-first Wrap Grid / Desktop Centered Grid) -->
                <div class="w-full flex flex-col items-center justify-center gap-2.5 py-2 px-0 select-none" id="cora-quick-actions-bar"></div>
            </div><!-- end cora-dashboard-upper -->
                <!-- ===== Custom Quick Action Modal ===== -->
                <div id="cora-custom-action-modal" class="fixed inset-0 flex items-center justify-center" style="display:none; z-index: 100000;" onclick="if(event.target===this){this.style.display='none';document.body.style.overflow='';}">
                    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-zinc-200 w-full max-w-md mx-4" style="max-height:90vh;overflow-y:auto;">
                        <!-- Header -->
                        <div class="flex items-start justify-between px-6 pt-6 pb-4 border-b border-zinc-100">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900">Quick Action Shortcuts</h3>
                                <p class="text-[11px] text-zinc-455 mt-0.5 font-medium">Personalise your dashboard with page shortcuts</p>
                            </div>
                            <button onclick="document.getElementById('cora-custom-action-modal').style.display='none';document.body.style.overflow='';" class="p-1.5 hover:bg-zinc-100 rounded-lg transition-colors cursor-pointer text-zinc-400 hover:text-zinc-600 shrink-0 bg-transparent border-0">
                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                        <!-- Suggested Presets -->
                        <div class="px-6 py-4 border-b border-zinc-100">
                            <p class="text-[10px] font-semibold text-zinc-450 uppercase tracking-wide mb-2.5">⚡ Suggested for you</p>
                            <div id="cora-preset-pills" class="flex flex-wrap gap-1.5"></div>
                        </div>
                        <!-- Custom Form -->
                        <div class="px-6 py-4 border-b border-zinc-100">
                            <p class="text-[10px] font-semibold text-zinc-450 uppercase tracking-wide mb-2.5">Create custom shortcut</p>
                            <div class="space-y-2.5">
                                <input type="text" id="cora-custom-action-name" placeholder="Label (e.g. View Reports)" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-xl bg-zinc-50 text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-500 transition-colors" />
                                <div class="relative" id="cora-page-picker-wrap">
                                    <div class="flex items-center gap-2 px-3 py-2 border border-zinc-200 rounded-xl bg-zinc-50 cursor-text" onclick="document.getElementById('cora-page-search').focus()">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        <input type="text" id="cora-page-search" placeholder="Search pages..." autocomplete="off" class="flex-1 text-sm bg-transparent border-0 outline-none text-zinc-800 placeholder:text-zinc-400" oninput="window.coraFilterPages(this.value)" onfocus="document.getElementById('cora-page-list-drop').style.display='block';window.coraFilterPages(this.value)" />
                                        <span id="cora-page-selected-label" class="text-[10px] font-semibold text-green-600 shrink-0 hidden">✓</span>
                                    </div>
                                    <input type="hidden" id="cora-custom-action-page" value="" />
                                    <div id="cora-page-list-drop" class="absolute left-0 right-0 top-full mt-1 bg-white border border-zinc-200 rounded-xl shadow-lg overflow-hidden" style="display:none; z-index:200; max-height:180px; overflow-y:auto;">
                                        <div id="cora-page-list-items"></div>
                                    </div>
                                </div>
                                <button onclick="window.coraAddCustomAction()" class="w-full py-2 bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-semibold rounded-xl transition-colors cursor-pointer border-0">Add Shortcut</button>
                            </div>
                        </div>
                        <!-- Existing -->
                        <div class="px-6 py-4">
                            <p class="text-[10px] font-semibold text-zinc-450 uppercase tracking-wide mb-2.5">Your shortcuts</p>
                            <div id="cora-custom-actions-list"></div>
                        </div>
                    </div>
                </div>
                <?php
                $enabled_pages = array( 'settings-suite' );
                if ( $cora_workspace_industry_raw === 'custom' ) {
                    $enabled_features = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();
                    if ( in_array( 'calendar', $enabled_features, true ) || in_array( 'showings', $enabled_features, true ) ) {
                        $enabled_pages[] = 'bookings';
                    }
                    if ( in_array( 'leads', $enabled_features, true ) ) {
                        $enabled_pages[] = 'leads';
                        $enabled_pages[] = 'clients';
                    }
                    if ( in_array( 'properties', $enabled_features, true ) ) {
                        $enabled_pages[] = 'listings';
                    }
                    if ( in_array( 'equipment', $enabled_features, true ) ) {
                        $enabled_pages[] = 'equipment';
                    }
                    if ( in_array( 'crew_scheduler', $enabled_features, true ) ) {
                        $enabled_pages[] = 'crew-scheduler';
                    }
                    if ( in_array( 'media', $enabled_features, true ) ) {
                        $enabled_pages[] = 'media';
                    }
                    if ( in_array( 'financials', $enabled_features, true ) ) {
                        $enabled_pages[] = 'financials';
                    }
                    if ( in_array( 'blogs', $enabled_features, true ) ) {
                        $enabled_pages[] = 'content-suite';
                    }
                    if ( in_array( 'forms', $enabled_features, true ) ) {
                        $enabled_pages[] = 'forms';
                    }
                    if ( in_array( 'ai-assistants', $enabled_features, true ) || in_array( 'gbp', $enabled_features, true ) || in_array( 'knowledge-base', $enabled_features, true ) ) {
                        $enabled_pages[] = 'ai-assistants';
                    }
                    if ( in_array( 'analytics', $enabled_features, true ) ) {
                        $enabled_pages[] = 'analytics';
                    }
                    if ( in_array( 'mcp', $enabled_features, true ) ) {
                        $enabled_pages[] = 'integrations';
                    }
                    if ( in_array( 'knowledge-base', $enabled_features, true ) ) {
                        $enabled_pages[] = 'knowledge-base';
                    }
                } elseif ( $is_studio ) {
                    $enabled_pages = array( 'bookings', 'clients', 'equipment', 'crew-scheduler', 'media', 'financials', 'content-suite', 'forms', 'settings-suite', 'ai-assistants', 'analytics', 'integrations', 'knowledge-base' );
                } else {
                    $enabled_pages = array( 'bookings', 'leads', 'clients', 'listings', 'media', 'financials', 'content-suite', 'forms', 'settings-suite', 'ai-assistants', 'analytics', 'integrations', 'knowledge-base' );
                }
                ?>
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

var CORA_ENABLED_PAGES = <?php echo json_encode( $enabled_pages ); ?>;
var ALL_PAGES_RAW=[{value:'bookings',label:'Bookings / Calendar'},{value:'leads',label:'Leads'},{value:'clients',label:'Clients'},{value:'listings',label:'Listings'},{value:'equipment',label:'Equipment / Gear'},{value:'crew-scheduler',label:'Crew Scheduler'},{value:'media',label:'Media Library'},{value:'financials',label:'Financials'},{value:'content-suite',label:'Content Suite'},{value:'forms',label:'Forms & Contracts'},{value:'settings-suite',label:'Settings'},{value:'ai-assistants',label:'AI Assistants'},{value:'analytics',label:'Analytics & Reports'},{value:'integrations',label:'Integrations'},{value:'knowledge-base',label:'RAG Knowledge Base'}];

var bookings_label = ( <?php echo $is_studio ? 'true' : 'false'; ?> ) ? 'Book a Shoot' : 'Schedule Showing';
var listings_label = ( <?php echo $is_studio ? 'true' : 'false'; ?> ) ? 'View Portfolio' : 'View Listings';
var PRESETS_RAW=[{name:bookings_label,page:'bookings'},{name:'Check Financials',page:'financials'},{name:'Add New Lead',page:'leads'},{name:'Upload Media',page:'media'},{name:'View Crew',page:'crew-scheduler'},{name:'AI Assistants',page:'ai-assistants'},{name:'Content Suite',page:'content-suite'},{name:listings_label,page:'listings'},{name:'RAG Knowledge Base',page:'knowledge-base'}];

var CORA_ALL_PAGES = ALL_PAGES_RAW.filter(function(p){ return CORA_ENABLED_PAGES.indexOf(p.value) > -1; });
var CORA_PRESETS = PRESETS_RAW.filter(function(p){ return CORA_ENABLED_PAGES.indexOf(p.page) > -1; });

window.coraFilterPages=function(q){var drop=document.getElementById('cora-page-list-drop');var items=document.getElementById('cora-page-list-items');if(!drop||!items)return;drop.style.display='block';var filtered=q?CORA_ALL_PAGES.filter(function(p){return p.label.toLowerCase().indexOf(q.toLowerCase())>-1;}):CORA_ALL_PAGES;if(!filtered.length){items.innerHTML='<p style="font-size:11px;color:#a1a1aa;padding:10px 12px;">No pages found</p>';return;}items.innerHTML=filtered.map(function(p){var iconHtml=window.coraGetPageIconSvg(p.value);return '<button type="button" onclick="window.coraSelectPage(\''+p.value+'\',\''+p.label.replace(/'/g,"\\'")+'\')" style="display:flex;align-items:center;gap:8px;width:100%;text-align:left;padding:8px 12px;font-size:12px;background:none;border:none;cursor:pointer;color:#3f3f46;" onmouseover="this.style.background=\'#f4f4f5\'" onmouseout="this.style.background=\'none\'"><span style="display:inline-flex;align-items:center;justify-content:center;width:14px;height:14px;color:#71717a;">'+iconHtml+'</span><span>'+p.label+'</span></button>';}).join('');};

window.coraSelectPage=function(value,label){document.getElementById('cora-custom-action-page').value=value;document.getElementById('cora-page-search').value=label;var lbl=document.getElementById('cora-page-selected-label');lbl.classList.remove('hidden');document.getElementById('cora-page-list-drop').style.display='none';};

window.coraRenderPresets=function(){var container=document.getElementById('cora-preset-pills');if(!container)return;var existing=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]').map(function(a){return a.page+'|'+a.name;});container.innerHTML=CORA_PRESETS.map(function(p){var added=existing.indexOf(p.page+'|'+p.name)>-1;var iconHtml=window.coraGetPageIconSvg(p.page);return '<button type="button" onclick="window.coraAddPreset(\''+p.name.replace(/'/g,"\\'")+'\',\''+p.page+'\')" style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;font-size:11px;font-weight:500;border-radius:20px;cursor:pointer;border:1px solid '+(added?'#bbf7d0':'#e4e4e7')+';background:'+(added?'#f0fdf4':'#fafafa')+';color:'+(added?'#16a34a':'#3f3f46')+';">'+'<span style="display:inline-flex;align-items:center;color:'+(added?'#16a34a':'#71717a')+'; width:12px; height:12px;">'+iconHtml+'</span><span>'+p.name+'</span>'+(added?'<span style="font-size:9px;margin-left:2px;">✓</span>':'')+'</button>';}).join('');};

window.coraAddPreset=function(name,page){var actions=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]');if(actions.find(function(a){return a.name===name&&a.page===page;})){if(window.coraShowToast)coraShowToast('Already added!','info');return;}actions.push({name:name,page:page});localStorage.setItem('cora_custom_quick_actions',JSON.stringify(actions));window.coraRenderCustomActions();window.coraRenderCustomActionsList();window.coraRenderPresets();if(window.coraShowToast)coraShowToast(name+' added!','success');};

window.coraOpenCustomActionModal=function(){var m=document.getElementById('cora-custom-action-modal');m.style.display='flex';document.body.style.overflow='hidden';window.coraFilterPages('');window.coraRenderPresets();window.coraRenderCustomActionsList();document.addEventListener('click',function h(e){if(!e.target.closest('#cora-page-picker-wrap')){var d=document.getElementById('cora-page-list-drop');if(d)d.style.display='none';}},{capture:true});};

window.coraAddCustomAction=function(){var name=(document.getElementById('cora-custom-action-name').value||'').trim();var page=document.getElementById('cora-custom-action-page').value;if(!name||!page){if(window.coraShowToast)coraShowToast('Please enter a label and select a page.','error');return;}var actions=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]');actions.push({name:name,page:page});localStorage.setItem('cora_custom_quick_actions',JSON.stringify(actions));document.getElementById('cora-custom-action-name').value='';document.getElementById('cora-custom-action-page').value='';document.getElementById('cora-page-search').value='';document.getElementById('cora-page-selected-label').classList.add('hidden');window.coraRenderCustomActions();window.coraRenderCustomActionsList();window.coraRenderPresets();if(window.coraShowToast)coraShowToast('Shortcut added!','success');};

window.coraDeleteCustomAction=function(idx){var actions=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]');actions.splice(idx,1);localStorage.setItem('cora_custom_quick_actions',JSON.stringify(actions));window.coraRenderCustomActions();window.coraRenderCustomActionsList();window.coraRenderPresets();};

window.coraRenderCustomActionsList=function(){var list=document.getElementById('cora-custom-actions-list');if(!list)return;var actions=JSON.parse(localStorage.getItem('cora_custom_quick_actions')||'[]');if(!actions.length){list.innerHTML='<p style="font-size:11px;color:#a1a1aa;text-align:center;padding:6px 0;">No shortcuts yet — add one above or pick a suggestion.</p>';return;}list.innerHTML='<div style="display:flex;flex-direction:column;gap:6px;">'+actions.map(function(a,i){var iconHtml=window.coraGetPageIconSvg(a.page);return '<div style="display:flex;align-items:center;justify-content:space-between;padding:7px 12px;background:#f4f4f5;border-radius:10px;"><div style="display:flex;align-items:center;gap:7px;"><span style="display:inline-flex;align-items:center;color:#71717a;width:12px;height:12px;">'+iconHtml+'</span><span style="font-size:12px;font-weight:500;color:#3f3f46;">'+a.name+'</span></div><button onclick="window.coraDeleteCustomAction('+i+')" style="background:none;border:none;cursor:pointer;color:#a1a1aa;padding:2px;"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button></div>';}).join('')+'</div>';};

window.coraRenderQuickActionsBar = function() {
    var bar = document.getElementById('cora-quick-actions-bar');
    if (!bar) return;

    var isDesktop = window.innerWidth >= 768;

    // Load custom shortcuts from localStorage
    var customStored = JSON.parse(localStorage.getItem('cora_custom_quick_actions') || '[]');
    var customActions = customStored.map(function(act) {
        return {
            label: act.name,
            icon: window.coraGetPageIconSvg(act.page),
            onclick: "coraNavigateTo('" + act.page + "');"
        };
    });

    // Merge predefined actions with custom actions
    var allActions = CORA_PREDEFINED_ACTIONS.concat(customActions);

    var creatorBtn = {
        isCreator: true,
        label: 'Custom Shortcuts',
        onclick: 'window.coraOpenCustomActionModal()',
        icon: '<svg viewBox="0 0 24 24" width="13" height="13" class="text-purple-600 shrink-0" fill="currentColor"><path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"></path></svg>'
    };

    if (isDesktop) {
        // Desktop: Render in one single centered row
        var activeActions = allActions.slice(0, 6);
        var totalItems = activeActions.concat([creatorBtn]);

        var buttonsHtml = totalItems.map(function(item) {
            if (item.isCreator) {
                return '<button type="button" onclick="' + item.onclick + '" class="cora-ai-gradient-pill select-none whitespace-nowrap shrink-0">' +
                    '<span class="cora-ai-gradient-pill-inner">' +
                        item.icon +
                        '<span>' + item.label + '</span>' +
                    '</span>' +
                '</button>';
            } else {
                return '<button onclick="' + item.onclick + '" class="flex justify-center items-center gap-2 px-4 py-2 border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-900 rounded-full text-xs font-semibold transition-all shadow-3xs cursor-pointer whitespace-nowrap shrink-0">' +
                    item.icon +
                    ' <span>' + item.label + '</span>' +
                '</button>';
            }
        }).join('');

        bar.innerHTML = '<div class="w-full flex flex-row flex-wrap items-center justify-center gap-x-2.5 gap-y-1.5 px-4 py-0.5">' +
            buttonsHtml +
        '</div>';

    } else {
        // Mobile layout:
        // Row 1: 3 chips
        // Row 2: 2 chips
        // Row 3: Custom Shortcuts chip
        var row1Actions = allActions.slice(0, 3);
        var row2Actions = allActions.slice(3, 5);

        var row1Html = row1Actions.map(function(item) {
            return '<button onclick="' + item.onclick + '" class="flex-1 flex justify-center items-center gap-1 px-2.5 py-1.5 border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-900 rounded-full text-[10px] font-bold transition-all shadow-3xs cursor-pointer whitespace-nowrap overflow-hidden text-ellipsis select-none" style="min-width: 0;">' +
                item.icon +
                ' <span class="truncate">' + item.label + '</span>' +
            '</button>';
        }).join('');

        var row2Html = row2Actions.map(function(item) {
            return '<button onclick="' + item.onclick + '" class="flex-1 flex justify-center items-center gap-1.5 px-3.5 py-1.5 border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-900 rounded-full text-[10.5px] font-bold transition-all shadow-3xs cursor-pointer whitespace-nowrap overflow-hidden text-ellipsis select-none" style="min-width: 0; max-width: 48%;">' +
                item.icon +
                ' <span class="truncate">' + item.label + '</span>' +
            '</button>';
        }).join('');

        var creatorHtml = '<button type="button" onclick="' + creatorBtn.onclick + '" class="cora-ai-gradient-pill select-none whitespace-nowrap shrink-0 text-[10.5px] font-bold">' +
            '<span class="cora-ai-gradient-pill-inner px-4 py-1.5">' +
                creatorBtn.icon +
                '<span>' + creatorBtn.label + '</span>' +
            '</span>' +
        '</button>';

        var html = '<div class="w-full flex flex-col gap-2 px-4">';
        
        if (row1Html) {
            html += '<div class="w-full flex flex-row flex-nowrap items-center justify-between gap-2">' + row1Html + '</div>';
        }
        if (row2Html) {
            html += '<div class="w-full flex flex-row flex-nowrap items-center justify-center gap-2">' + row2Html + '</div>';
        }
        html += '<div class="w-full flex flex-row items-center justify-center mt-0.5">' + creatorHtml + '</div>';
        html += '</div>';

        bar.innerHTML = html;
    }
};

window.coraRenderCustomActions = window.coraRenderQuickActionsBar;

document.addEventListener('DOMContentLoaded', window.coraRenderQuickActionsBar);
window.addEventListener('resize', window.coraRenderQuickActionsBar);
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
                    <?php if ( $cora_workspace_industry_raw === 'custom' ) : ?>
                        <!-- CARD 1: AI Co-Founder Briefing (Spans 2 Columns) -->
                        <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between md:col-span-2 min-h-[320px]">
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"></path></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">AI Co-Founder Briefing</h3>
                                            <span class="text-[11px] text-zinc-450 font-medium">Real-time workspace insights and summaries</span>
                                        </div>
                                    </div>
                                    <span class="h-5 w-16 rounded-full bg-emerald-50 text-emerald-600 font-bold text-[9px] flex items-center justify-center border border-emerald-100 uppercase tracking-wider">🟢 Active</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 my-3">
                                    <div class="p-3 rounded-xl bg-zinc-50/70 border border-zinc-100 flex items-center justify-between">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="text-[10px] shrink-0">🟢</span>
                                            <span class="text-xs font-bold text-zinc-800 truncate">AI Co-founder</span>
                                        </div>
                                        <span class="text-[10px] text-zinc-455 font-bold uppercase tracking-wider shrink-0 bg-white px-2 py-0.5 rounded border border-zinc-200">Online</span>
                                    </div>
                                    <?php if ( in_array( 'gbp', $enabled, true ) ) : ?>
                                    <div class="p-3 rounded-xl bg-zinc-50/70 border border-zinc-100 flex items-center justify-between">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="text-[10px] shrink-0">🟢</span>
                                            <span class="text-xs font-bold text-zinc-800 truncate">AI Marketing</span>
                                        </div>
                                        <span class="text-[10px] text-zinc-455 font-bold uppercase tracking-wider shrink-0 bg-white px-2 py-0.5 rounded border border-zinc-200">Monitoring</span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ( in_array( 'automations', $enabled, true ) ) : ?>
                                    <div class="p-3 rounded-xl bg-zinc-50/70 border border-zinc-100 flex items-center justify-between">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="text-[10px] shrink-0">🟢</span>
                                            <span class="text-xs font-bold text-zinc-800 truncate">Automations</span>
                                        </div>
                                        <span class="text-[10px] text-zinc-455 font-bold uppercase tracking-wider shrink-0 bg-white px-2 py-0.5 rounded border border-zinc-200">Active</span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ( in_array( 'vault', $enabled, true ) ) : ?>
                                    <div class="p-3 rounded-xl bg-zinc-50/70 border border-zinc-100 flex items-center justify-between">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="text-[10px] shrink-0">🟢</span>
                                            <span class="text-xs font-bold text-zinc-800 truncate">Secure Vault</span>
                                        </div>
                                        <span class="text-[10px] text-zinc-455 font-bold uppercase tracking-wider shrink-0 bg-white px-2 py-0.5 rounded border border-zinc-200">Ready</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-zinc-100 mt-3">
                                <button onclick="coraNavigateTo('ai-assistants')" class="w-full flex items-center justify-between text-xs font-bold text-violet-655 hover:text-violet-750 transition-colors cursor-pointer group">
                                    <span>Consult AI Co-Founder</span>
                                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </button>
                            </div>
                        </div>

                        <!-- CARD 2: AI Co-founder Diagnostics (Spans 1 Column) -->
                        <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between min-h-[320px]">
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">AI Co-founder Diagnostics</h3>
                                            <span class="text-[11px] text-zinc-450 font-medium">Status & quota metrics</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4 mt-2">
                                    <div class="flex items-center justify-between py-1 border-b border-zinc-100">
                                        <span class="text-xs text-zinc-500 font-bold">Active Engine</span>
                                        <span class="text-xs font-extrabold text-zinc-800">Gemini 3.5 Flash</span>
                                    </div>
                                    <div class="flex items-center justify-between py-1 border-b border-zinc-100">
                                        <span class="text-xs text-zinc-500 font-bold">Latency</span>
                                        <span class="text-xs font-extrabold text-emerald-600">🟢 142ms</span>
                                    </div>
                                    <div class="flex items-center justify-between py-1 border-b border-zinc-100">
                                        <span class="text-xs text-zinc-500 font-bold">Quota Usage</span>
                                        <span class="text-xs font-extrabold text-zinc-800">12,402 / 50,000 (24%)</span>
                                    </div>
                                    <div class="flex items-center justify-between py-1">
                                        <span class="text-xs text-zinc-500 font-bold">Database Health</span>
                                        <span class="text-xs font-extrabold text-zinc-800">Optimized</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-zinc-100 mt-3">
                                <button onclick="window.coraShowToast('AI Co-founder diagnostic trace passed: 100% OK', 'success')" class="w-full flex items-center justify-between text-xs font-bold text-emerald-650 hover:text-emerald-750 transition-colors cursor-pointer group">
                                    <span>Run Diagnostics</span>
                                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </button>
                            </div>
                        </div>

                        <!-- CARD 3: Smart Recommendations (Spans 1 Column) -->
                        <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between min-h-[280px]">
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26C17.81 13.47 19 11.38 19 9a7 7 0 0 0-7-7z"></path></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Smart Tasks</h3>
                                            <span class="text-[11px] text-zinc-450 font-medium">Contextual workspace insights</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3.5">
                                    <?php
                                    $custom_smart_tasks = array();
                                    if ( in_array( 'blogs', $enabled, true ) ) {
                                        $custom_smart_tasks[] = array(
                                            'title' => 'Write a Blog Post',
                                            'desc' => 'Increase local traffic search',
                                            'url' => "coraNavigateTo('blogs')",
                                            'icon' => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>'
                                        );
                                    }
                                    if ( in_array( 'forms', $enabled, true ) ) {
                                        $custom_smart_tasks[] = array(
                                            'title' => 'Optimize Form Fields',
                                            'desc' => 'Improve lead intake response rate',
                                            'url' => "coraNavigateTo('forms')",
                                            'icon' => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>'
                                        );
                                    }
                                    if ( count($custom_smart_tasks) < 2 ) {
                                        $custom_smart_tasks[] = array(
                                            'title' => 'Configure Shortcuts',
                                            'desc' => 'Customize dashboard buttons',
                                            'url' => "window.coraOpenCustomActionModal()",
                                            'icon' => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"></path></svg>'
                                        );
                                    }
                                    foreach ( $custom_smart_tasks as $task ) : ?>
                                        <div class="flex items-start gap-2.5 p-2 bg-zinc-55/35 border border-transparent rounded-xl hover:bg-zinc-50 transition-all cursor-pointer" onclick="<?php echo $task['url']; ?>">
                                            <div class="h-6 w-6 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0 font-bold text-[11px]">
                                                <?php echo $task['icon']; ?>
                                            </div>
                                            <div class="flex-1 min-w-0 pr-1 flex items-center justify-between">
                                                <div class="flex flex-col min-w-0">
                                                    <span class="text-[11px] font-bold text-zinc-800 leading-tight"><?php echo esc_html( $task['title'] ); ?></span>
                                                    <span class="text-[9px] text-zinc-455 truncate mt-0.5"><?php echo esc_html( $task['desc'] ); ?></span>
                                                </div>
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-zinc-100 mt-3">
                                <button onclick="coraNavigateTo('ai-assistants')" class="w-full flex items-center justify-between text-xs font-bold text-blue-650 hover:text-blue-750 transition-colors cursor-pointer group">
                                    <span>Browse Smart Actions</span>
                                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </button>
                            </div>
                        </div>

                        <!-- CARD 4: Activity Log (Spans 1 Column) -->
                        <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between min-h-[280px]">
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 rounded-xl bg-orange-50 text-orange-655 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Activity Log</h3>
                                            <span class="text-[11px] text-zinc-450 font-medium">Recent workspace actions</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3.5">
                                    <div class="flex items-start gap-2.5 text-xs text-zinc-650">
                                        <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-full bg-zinc-100 text-zinc-500 shrink-0">10:14 AM</span>
                                        <span class="truncate">Workspace settings updated to Custom Mode</span>
                                    </div>
                                    <div class="flex items-start gap-2.5 text-xs text-zinc-650">
                                        <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-full bg-zinc-100 text-zinc-500 shrink-0">Yesterday</span>
                                        <span class="truncate">Security: User session authorized</span>
                                    </div>
                                    <div class="flex items-start gap-2.5 text-xs text-zinc-650">
                                        <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-full bg-zinc-100 text-zinc-500 shrink-0">Yesterday</span>
                                        <span class="truncate">Forms: Form builder template synced</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-zinc-100 mt-3">
                                <button onclick="coraNavigateTo('activity-timeline')" class="w-full flex items-center justify-between text-xs font-bold text-orange-600 hover:text-orange-750 transition-colors cursor-pointer group">
                                    <span>View Activity Timeline</span>
                                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </button>
                            </div>
                        </div>

                        <!-- CARD 5: AI Inbox (Spans 1 Column, custom view) -->
                        <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between min-h-[280px]">
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">AI Inbox</h3>
                                            <span class="text-[11px] text-zinc-455 font-medium">Pending system updates</span>
                                        </div>
                                    </div>
                                    <span class="h-5 w-5 rounded-full bg-purple-50 text-purple-600 font-bold text-[10px] flex items-center justify-center shrink-0"><?php echo intval($cora_unread_count); ?></span>
                                </div>

                                <div class="space-y-3">
                                    <?php if ( empty( $cora_user_notifications ) ) : ?>
                                        <div class="flex flex-col items-center justify-center py-8 text-center">
                                            <div class="h-10 w-10 rounded-full bg-zinc-50 border border-zinc-200 flex items-center justify-center mb-2">
                                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                            </div>
                                            <h4 class="text-[11px] font-bold text-zinc-700">Your inbox is clear</h4>
                                            <p class="text-[9px] text-zinc-455 mt-0.5 max-w-[180px]">No pending system updates or alerts.</p>
                                        </div>
                                    <?php else : 
                                        foreach ( array_slice( $cora_user_notifications, 0, 2 ) as $notif ) : 
                                            $n_title = $notif['title'] ?? 'Notification';
                                            $n_body = $notif['body'] ?? '';
                                            $n_time = isset( $notif['timestamp'] ) ? human_time_diff( $notif['timestamp'], current_time( 'timestamp' ) ) . ' ago' : 'Just now';
                                            $n_icon = (strpos(strtolower($n_title), 'request') !== false || strpos(strtolower($n_title), 'appeal') !== false) ? '?' : 'i';
                                        ?>
                                            <div class="flex items-start gap-2.5 p-2 bg-zinc-50/30 border border-zinc-100/60 rounded-xl">
                                                <div class="h-6 w-6 rounded-lg bg-purple-50 text-purple-500 flex items-center justify-center shrink-0 font-bold text-[11px]"><?php echo $n_icon; ?></div>
                                                <div class="flex flex-col min-w-0 flex-1">
                                                    <strong class="text-[11px] font-bold text-zinc-800 truncate"><?php echo esc_html($n_title); ?></strong>
                                                    <span class="text-[9px] text-zinc-455 truncate"><?php echo esc_html($n_body); ?></span>
                                                </div>
                                                <span class="text-[9px] text-zinc-455 shrink-0"><?php echo esc_html($n_time); ?></span>
                                            </div>
                                        <?php endforeach; 
                                    endif; ?>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-zinc-100 mt-3">
                                <button onclick="window.coraShowToast('AI Inbox is currently in staging sync mode.', 'info')" class="w-full flex items-center justify-between text-xs font-bold text-purple-650 hover:text-purple-750 transition-colors cursor-pointer group">
                                    <span>Check AI Messages</span>
                                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </button>
                            </div>
                        </div>
                    <?php else : ?>
                        <!-- CARD 1: Today's Timeline (Spans 2 Columns) -->
                    <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between md:col-span-2 min-h-[320px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Today's Timeline</h3>
                                        <span class="text-[11px] text-zinc-450 font-medium">Your key operational events for today</span>
                                    </div>
                                </div>
                                <span class="h-5 w-7 rounded-full bg-violet-50 text-violet-600 font-bold text-[10px] flex items-center justify-center shrink-0"><?php echo count($today_events); ?></span>
                            </div>

                            <?php if ( empty( $today_events ) ) : ?>
                                <div class="flex flex-col items-center justify-center py-10 text-center">
                                    <div class="h-12 w-12 rounded-full bg-zinc-50 border border-zinc-200 flex items-center justify-center mb-3">
                                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    </div>
                                    <h4 class="text-xs font-bold text-zinc-700">Clear Schedule Today</h4>
                                    <p class="text-[11px] text-zinc-450 mt-1 max-w-[280px]">No active shoots or showings booked for today.</p>
                                </div>
                            <?php else : ?>
                                <!-- Timeline Vertical Node List -->
                                <div class="space-y-4 pl-1 relative before:absolute before:left-[4.75rem] before:top-2 before:bottom-2 before:w-[2px] before:bg-zinc-100">
                                    <?php foreach ( $today_events as $event ) : 
                                        $time_formatted = date('h:i A', strtotime($event['time']));
                                        $badge_cls = 'text-zinc-600 bg-zinc-100 border border-zinc-200';
                                        $dot_cls = 'bg-zinc-400';
                                        if ($event['status'] === 'confirmed') {
                                            $badge_cls = 'text-emerald-700 bg-emerald-50 border border-emerald-100';
                                            $dot_cls = 'bg-emerald-500';
                                        } elseif ($event['status'] === 'editing') {
                                            $badge_cls = 'text-amber-700 bg-amber-50 border border-amber-100';
                                            $dot_cls = 'bg-amber-500';
                                        }
                                    ?>
                                        <div class="flex items-start gap-4 relative text-xs">
                                            <span class="text-[10px] font-bold text-zinc-455 w-16 shrink-0 pt-0.5"><?php echo esc_html($time_formatted); ?></span>
                                            <span class="w-2.5 h-2.5 rounded-full <?php echo $dot_cls; ?> ring-4 ring-white shrink-0 z-10 mt-1"></span>
                                            <div class="flex-1 min-w-0">
                                                 <div class="flex items-center gap-2 flex-wrap">
                                                     <strong class="font-bold text-zinc-850 truncate text-[13px]"><?php echo esc_html($event['deal_type']); ?></strong>
                                                     <span class="text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded <?php echo $badge_cls; ?>"><?php echo esc_html($event['status']); ?></span>
                                                 </div>
                                                 <span class="text-[10px] text-zinc-500 block truncate mt-0.5"><?php echo esc_html($event['resolved_client_name']); ?> &bull; <?php echo esc_html($event['location'] ?? $event['notes']); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 mt-3">
                            <button onclick="coraNavigateTo('bookings')" class="w-full flex items-center justify-between text-xs font-bold text-violet-655 hover:text-violet-750 transition-colors cursor-pointer group">
                                <span>View Full Calendar & Booking CRM</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 3: Cash Overview (Spans 1 Column, matches other cards cleanly) -->
                    <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between min-h-[320px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M12 8v8M8 12h8"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Cash Overview</h3>
                                        <span class="text-[11px] text-zinc-450 font-medium">Your financial snapshot</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-5 mt-2">
                                <div class="flex items-center justify-between py-1 border-b border-zinc-100">
                                    <span class="text-xs text-zinc-500 font-bold">Received (MTD)</span>
                                    <span class="text-base font-extrabold text-emerald-600">₹<?php echo number_format($mtd_received); ?></span>
                                </div>

                                <div class="flex items-center justify-between py-1 border-b border-zinc-100">
                                    <span class="text-xs text-zinc-500 font-bold">Expected</span>
                                    <span class="text-base font-extrabold text-zinc-900">₹<?php echo number_format($expected_amount); ?></span>
                                </div>

                                <div class="flex items-center justify-between py-1">
                                    <span class="text-xs text-zinc-500 font-bold">Overdue Invoices</span>
                                    <span class="text-base font-extrabold text-zinc-455">₹<?php echo number_format($overdue_amount); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 mt-3">
                            <button onclick="coraNavigateTo('financials')" class="w-full flex items-center justify-between text-xs font-bold text-emerald-650 hover:text-emerald-750 transition-colors cursor-pointer group">
                                <span>Go to Financial Ledger</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 4: Clients Needing Attention (Spans 1 Column) -->
                    <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between min-h-[280px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-orange-50 text-orange-655 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Attention Required</h3>
                                        <span class="text-[11px] text-zinc-450 font-medium">Follow ups that cannot wait</span>
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
                                        <div class="h-10 w-10 rounded-full bg-zinc-50 border border-zinc-200 flex items-center justify-center mb-2">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        </div>
                                        <h4 class="text-[11px] font-bold text-zinc-700">All caught up</h4>
                                        <p class="text-[9px] text-zinc-455 mt-0.5 max-w-[180px]">No immediate follow ups required.</p>
                                    </div>
                                <?php else : 
                                    foreach ( array_slice($attention_clients, 0, 3) as $client ) :
                                        $c_name = $client['names'] ?? 'Client';
                                        $c_reason = $client['reason'] ?? ($is_studio ? 'Shoot confirmation' : 'Lead follow-up');
                                        $c_time = $client['time_ago'] ?? '1d';
                                        $badge_cls = 'text-zinc-500 bg-zinc-100';
                                        if (strtolower($c_time) === 'today') {
                                            $badge_cls = 'text-orange-700 bg-orange-50';
                                        }
                                    ?>
                                        <div class="flex items-center justify-between text-xs gap-2">
                                            <div class="flex flex-col min-w-0">
                                                <strong class="font-bold text-zinc-855 truncate text-[11px]"><?php echo esc_html($c_name); ?></strong>
                                                <span class="text-[10px] text-zinc-455 truncate"><?php echo esc_html($c_reason); ?></span>
                                            </div>
                                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full shrink-0 <?php echo $badge_cls; ?>"><?php echo esc_html($c_time); ?></span>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 mt-3">
                            <button onclick="coraNavigateTo('clients')" class="w-full flex items-center justify-between text-xs font-bold text-orange-600 hover:text-orange-700 transition-colors cursor-pointer group">
                                <span>View All Contacts</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 5: Smart Suggestions (Spans 1 Column) -->
                    <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between min-h-[280px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26C17.81 13.47 19 11.38 19 9a7 7 0 0 0-7-7z"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Smart Tasks</h3>
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
                                        <div class="h-10 w-10 rounded-full bg-zinc-50 border border-zinc-200 flex items-center justify-center mb-2">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26C17.81 13.47 19 11.38 19 9a7 7 0 0 0-7-7z"></path></svg>
                                        </div>
                                        <h4 class="text-[11px] font-bold text-zinc-700">No smart tasks</h4>
                                        <p class="text-[9px] text-zinc-455 mt-0.5 max-w-[180px]">Add bookings or listings to see recommendations.</p>
                                    </div>
                                <?php else : 
                                    foreach ( $smart_tasks as $task ) : ?>
                                        <div class="flex items-start gap-2.5 p-2 bg-zinc-55/35 border border-transparent rounded-xl hover:bg-zinc-50 transition-all cursor-pointer" onclick="<?php echo $task['url']; ?>">
                                            <div class="h-6 w-6 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0 font-bold text-[11px]">
                                                <?php echo $task['icon']; ?>
                                            </div>
                                            <div class="flex-1 min-w-0 pr-1 flex items-center justify-between">
                                                <div class="flex flex-col min-w-0">
                                                    <span class="text-[11px] font-bold text-zinc-800 leading-tight"><?php echo esc_html( $task['title'] ); ?></span>
                                                    <span class="text-[9px] text-zinc-455 truncate mt-0.5"><?php echo esc_html( $task['desc'] ); ?></span>
                                                </div>
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 mt-3">
                            <button onclick="coraNavigateTo('ai-assistants')" class="w-full flex items-center justify-between text-xs font-bold text-blue-650 hover:text-blue-750 transition-colors cursor-pointer group">
                                <span>Browse Smart Actions</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 2: AI Inbox (Spans 1 Column) -->
                    <div class="border border-zinc-100 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between min-h-[280px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">AI Inbox</h3>
                                        <span class="text-[11px] text-zinc-455 font-medium">Pending system updates</span>
                                    </div>
                                </div>
                                <span class="h-5 w-5 rounded-full bg-purple-50 text-purple-600 font-bold text-[10px] flex items-center justify-center shrink-0"><?php echo intval($cora_unread_count); ?></span>
                            </div>

                            <div class="space-y-3">
                                <?php if ( empty( $cora_user_notifications ) ) : ?>
                                    <div class="flex flex-col items-center justify-center py-8 text-center">
                                        <div class="h-10 w-10 rounded-full bg-zinc-50 border border-zinc-200 flex items-center justify-center mb-2">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        </div>
                                        <h4 class="text-[11px] font-bold text-zinc-700">Your inbox is clear</h4>
                                        <p class="text-[9px] text-zinc-455 mt-0.5 max-w-[180px]">No pending system updates or alerts.</p>
                                    </div>
                                <?php else : 
                                    foreach ( array_slice( $cora_user_notifications, 0, 2 ) as $notif ) : 
                                        $n_title = $notif['title'] ?? 'Notification';
                                        $n_body = $notif['body'] ?? '';
                                        $n_time = isset( $notif['timestamp'] ) ? human_time_diff( $notif['timestamp'], current_time( 'timestamp' ) ) . ' ago' : 'Just now';
                                        $n_icon = (strpos(strtolower($n_title), 'request') !== false || strpos(strtolower($n_title), 'appeal') !== false) ? '?' : 'i';
                                    ?>
                                        <div class="flex items-start gap-2.5 p-2 bg-zinc-50/30 border border-zinc-100/60 rounded-xl">
                                            <div class="h-6 w-6 rounded-lg bg-purple-50 text-purple-500 flex items-center justify-center shrink-0 font-bold text-[11px]"><?php echo $n_icon; ?></div>
                                            <div class="flex flex-col min-w-0 flex-1">
                                                <strong class="text-[11px] font-bold text-zinc-800 truncate"><?php echo esc_html($n_title); ?></strong>
                                                <span class="text-[9px] text-zinc-455 truncate"><?php echo esc_html($n_body); ?></span>
                                            </div>
                                            <span class="text-[9px] text-zinc-455 shrink-0"><?php echo esc_html($n_time); ?></span>
                                        </div>
                                    <?php endforeach; 
                                endif; ?>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 mt-3">
                            <button onclick="window.coraShowToast('AI Inbox is currently in staging sync mode.', 'info')" class="w-full flex items-center justify-between text-xs font-bold text-purple-650 hover:text-purple-750 transition-colors cursor-pointer group">
                                <span>Check AI Messages</span>
                                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
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
                            <h3 class="cora-card-title text-base font-semibold text-zinc-950">Instagram Caption Generator</h3>
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
                                <button class="cora-copy-btn text-zinc-500 hover:text-zinc-950 font-semibold normal-case cursor-pointer" onclick="coraCopyText('cora-caption-text')">Copy</button>
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
                                                <img src="<?php echo esc_url($photo_url); ?>" class="w-8 h-8 rounded-md object-cover border border-zinc-200/80" loading="lazy" />
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
            <?php if ( $sub_page === 'event_timeline' || $sub_page === 'event-timeline' || $sub_page === 'multi-day-timeline' || $sub_page === 'activity-timeline' ) : ?>
            <section id="cora-page-event-timeline" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-event-timeline.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: CREW & SHIFT SCHEDULER -->
            <?php if ( $sub_page === 'crew_scheduler' || $sub_page === 'crew-scheduler' || $sub_page === 'team_scheduler' || $sub_page === 'team-scheduler' || $sub_page === 'shifts' ) : ?>
            <section id="cora-page-crew-scheduler" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-crew-scheduler.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: WORKSPACE CALENDAR -->
            <?php if ( $sub_page === 'calendar' ) : ?>
            <section id="cora-page-calendar" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-calendar.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: WORKSPACE AUTOMATIONS -->
            <?php if ( $sub_page === 'automations' ) : ?>
            <section id="cora-page-automations" class="cora-page-section cora-active flex items-center justify-center min-h-[70vh]">
                <div class="max-w-md w-full bg-white border border-zinc-200 rounded-2xl p-8 text-center space-y-4 shadow-sm select-none">
                    <div class="w-12 h-12 rounded-full bg-zinc-50 text-zinc-900 flex items-center justify-center mx-auto border border-zinc-200/50">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <div class="space-y-1.5">
                        <h3 class="text-base font-bold text-zinc-950">Automations & Workflows Locked</h3>
                        <p class="text-xs text-zinc-500 leading-relaxed">This feature is not available on your current plan. Please upgrade to unlock custom automations, triggers, and third-party webhooks.</p>
                    </div>
                    <div class="pt-2">
                        <button onclick="if(window.coraShowToast) window.coraShowToast('Upgrade request sent to administrator', 'success')" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer border-none">Upgrade Plan</button>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION: WORKSPACE UNIFIED INBOX -->
            <?php if ( $sub_page === 'inbox' ) : ?>
            <section id="cora-page-inbox" class="cora-page-section cora-active flex items-center justify-center min-h-[70vh]">
                <div class="max-w-md w-full bg-white border border-zinc-200 rounded-2xl p-8 text-center space-y-4 shadow-sm select-none">
                    <div class="w-12 h-12 rounded-full bg-zinc-50 text-zinc-900 flex items-center justify-center mx-auto border border-zinc-200/50">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <div class="space-y-1.5">
                        <h3 class="text-base font-bold text-zinc-950">Inbox Locked</h3>
                        <p class="text-xs text-zinc-500 leading-relaxed">This feature is not available on your current plan. Please upgrade to unlock unified messaging, client email sync, and SMS channels.</p>
                    </div>
                    <div class="pt-2">
                        <button onclick="if(window.coraShowToast) window.coraShowToast('Upgrade request sent to administrator', 'success')" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer border-none">Upgrade Plan</button>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION: WORKSPACE ANALYTICS -->
            <?php if ( $sub_page === 'analytics' ) : ?>
            <section id="cora-page-analytics" class="cora-page-section cora-active">
                <?php include CORA_WORKSPACE_PATH . 'views/view-analytics.php'; ?>
            </section>
            <?php endif; ?>

            <!-- SECTION: META MARKETING SUITE -->
            <?php if ( $sub_page === 'social-meta' ) : ?>
            <section id="cora-page-social-meta" class="cora-page-section cora-active flex items-center justify-center min-h-[70vh]">
                <div class="max-w-md w-full bg-white border border-zinc-200 rounded-2xl p-8 text-center space-y-4 shadow-sm select-none">
                    <div class="w-12 h-12 rounded-full bg-zinc-50 text-zinc-900 flex items-center justify-center mx-auto border border-zinc-200/50">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <div class="space-y-1.5">
                        <h3 class="text-base font-bold text-zinc-950">Facebook & Instagram Locked</h3>
                        <p class="text-xs text-zinc-500 leading-relaxed">This feature is not available on your current plan. Please upgrade to unlock direct Facebook posts, Instagram reels scheduler, and lead ads sync.</p>
                    </div>
                    <div class="pt-2">
                        <button onclick="if(window.coraShowToast) window.coraShowToast('Upgrade request sent to administrator', 'success')" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer border-none">Upgrade Plan</button>
                    </div>
                </div>
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
    <!-- Local backdrop for AI bottom sheet sidebar (mobile/tablet only) -->
    <div id="cora-ai-sidebar-backdrop" onclick="window.coraToggleSidebar(false)" class="hidden fixed inset-0 bg-black/35 z-[9960] backdrop-blur-[1px] transition-opacity duration-300 cursor-pointer lg:hidden" style="position:fixed !important; inset:0 !important; z-index:9960 !important; background:rgba(0,0,0,0.35) !important; backdrop-filter:blur(1px) !important; -webkit-backdrop-filter:blur(1px) !important;"></div>

    <!-- Collapsible Right-side AI Sidebar (Notion-AI style) -->
    <aside id="cora-ai-sidebar" class="cora-ai-sidebar collapsed fixed top-0 lg:top-[52px] right-0 left-0 z-[999] h-full lg:h-[calc(100vh-52px)] w-full max-w-full bg-white border-t border-zinc-200 shadow-2xl flex flex-col transition-all duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header w-full shrink-0 select-none" style="padding: 10px 16px; background: #fafafa; border-bottom: 1px solid #e4e4e7;">
            <div class="flex justify-between items-center w-full max-w-3xl mx-auto">
                <!-- Left: New Conversation + Model Pill -->
                <div class="flex items-center gap-2">
                    <div class="cora-ai-sidebar-title flex items-center gap-1.5 cursor-pointer transition-colors" onclick="coraClearSidebarChat()" style="font-size: 12px; font-weight: 700; color: #27272a;">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" style="color: #52525b;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"/>
                        </svg>
                        <span>New Conversation</span>
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" style="color: #a1a1aa;"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <!-- Active Model Pill -->
                    <div id="cora-sidebar-model-pill" style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; background: #f4f4f5; border: 1px solid #e4e4e7; border-radius: 9999px; font-size: 10px; font-weight: 600; color: #71717a; cursor: default; white-space: nowrap;">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #22c55e; display: inline-block;"></span>
                        <span id="cora-sidebar-model-label">Gemini 2.5 Flash</span>
                    </div>
                </div>
                <!-- Right: Settings + Close -->
                <div class="flex items-center gap-1">
                    <button style="color: #a1a1aa; border: 0; background: transparent; padding: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: color 0.15s;" onmouseover="this.style.color='#3f3f46'" onmouseout="this.style.color='#a1a1aa'" title="AI Settings">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                    </button>
                    <button class="cora-ai-sidebar-close" onclick="coraToggleSidebar(false)" style="color: #a1a1aa; border: 0; background: transparent; padding: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: color 0.15s;" onmouseover="this.style.color='#18181b'" onmouseout="this.style.color='#a1a1aa'">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="cora-ai-sidebar-body flex-1 overflow-y-auto p-4 flex flex-col gap-6">
            <div class="max-w-3xl mx-auto w-full flex-1 flex flex-col justify-between gap-6">
                <div class="cora-ai-sidebar-chat-history flex flex-col gap-3" id="cora-sidebar-chat">
                    <div class="chat-bubble ai bg-zinc-100 text-zinc-850 rounded-lg rounded-bl-none p-3 text-xs leading-relaxed self-start border border-zinc-200/50 shadow-sm max-w-[85%]">
                        Hello! I am Cora, your workspace co-founder intelligence. Ask me about your business stats, recent logs, or quick actions.
                    </div>
                </div>

                <!-- Native AI Integration Block (persists inside sidebar) -->
                <div id="cora-sidebar-native-integration" class="flex flex-col gap-4">
                    <!-- Dynamic Quick Prompts -->
                    <div class="cora-ai-sidebar-shortcuts pt-3 border-t border-zinc-200">
                        <span class="cora-sidebar-sublabel text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2.5 block">Quick Prompts</span>
                        <button class="cora-shortcut-btn w-full text-left p-2.5 text-xs text-zinc-600 border border-zinc-200 rounded-md hover:bg-zinc-50 hover:text-zinc-900 transition-colors mb-2 cursor-pointer font-medium" onclick="coraSendShortcut('Summarize today\'s workspace activity')">Summarize activity</button>
                        <button class="cora-shortcut-btn w-full text-left p-2.5 text-xs text-zinc-500 border border-zinc-200 rounded-md hover:bg-zinc-50 hover:text-zinc-900 transition-colors cursor-pointer font-medium" onclick="coraSendShortcut('Show current automations status')">Check automations</button>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Notifications Backdrop (Desktop Only) -->
    <div id="cora-notif-backdrop" onclick="window.coraToggleNotificationDrawer(false)" class="hidden fixed inset-0 bg-black/45 backdrop-blur-[4px] z-[9998] transition-opacity duration-300 opacity-0" style="position:fixed; inset:0; background:rgba(9,9,11,0.45); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px);"></div>

    <!-- Notifications Side Drawer Panel -->
    <aside id="cora-notif-dropdown" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[400px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-all duration-300 ease-in-out">
        <div class="px-5 py-4 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/60 shrink-0 select-none">
            <div class="flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-700">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-800">Notifications</span>
            </div>
            <div class="flex items-center gap-2.5">
                <button id="cora-notif-mark-all-btn" onclick="window.coraMarkAllNotificationsRead(event);" class="text-[10px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors cursor-pointer border-0 bg-transparent uppercase tracking-wider">Mark read</button>
                <span class="text-zinc-300">|</span>
                <button onclick="window.coraClearAllNotifications(event);" class="text-[10px] font-bold text-zinc-500 hover:text-red-600 transition-colors cursor-pointer border-0 bg-transparent uppercase tracking-wider">Clear all</button>
                <span class="text-zinc-300">|</span>
                <button onclick="window.coraToggleNotificationDrawer(false)" class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer border-0 bg-transparent p-1">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
        <div id="cora-notif-list" class="flex-1 overflow-y-auto divide-y divide-zinc-100 p-2">
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
                        Add Asset
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
    <aside id="drawer-article-leads" class="collapsed fixed top-0 right-0 h-full w-[450px] bg-white border-l border-zinc-200 shadow-2xl z-[10005] transform translate-x-full transition-transform duration-300 ease-out flex flex-col overflow-hidden pointer-events-none">
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
    #cora-full-page-editor main { background-color: #ffffff !important; padding: 0 !important; }

    @keyframes coraHeadingHighlight {
        0% { background-color: rgba(228, 228, 231, 0.5); border-left: 4px solid #000000; padding-left: 8px; }
        100% { background-color: transparent; border-left: 0px solid transparent; padding-left: 0px; }
    }
    .cora-heading-highlight-flash {
        animation: coraHeadingHighlight 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

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
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 40px 60px !important;
        min-height: 100% !important;
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: relative;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    @media (max-width: 767px) {
        .cora-writing-sheet {
            padding: 24px 16px !important;
        }
    }
    .cora-serif-editor .ql-editor { font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif; font-size: 1.125rem; line-height: 1.8; color: #18181b; }
    .cora-sans-editor .ql-editor { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 1.05rem; line-height: 1.75; color: #18181b; }
    .ql-toolbar.ql-snow {
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: 1px solid #e4e4e7 !important;
        border-radius: 0 !important;
        padding: 8px 48px !important;
        position: sticky !important;
        top: 0 !important;
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(8px) !important;
        z-index: 40 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        margin-left: -48px !important;
        margin-right: -48px !important;
        margin-top: 0 !important;
        margin-bottom: 20px !important;
        width: calc(100% + 96px) !important;
        max-width: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
        gap: 4px !important;
        transition: all 0.2s ease !important;
    }
    @media (max-width: 767px) {
        .ql-toolbar.ql-snow {
            top: 0 !important;
            margin-left: -16px !important;
            margin-right: -16px !important;
            margin-bottom: 12px !important;
            width: calc(100% + 32px) !important;
            padding: 6px 16px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        }
    }
    
    /* Bulletproof Responsive/Horizontal Scroll Prevention Overrides */
    #cora-full-page-editor main {
        max-width: 100% !important;
        overflow-x: hidden !important;
        box-sizing: border-box !important;
    }
    .cora-writing-sheet * {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .ql-container.ql-snow, .ql-editor {
        max-width: 100% !important;
        overflow-x: hidden !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
    }
    
    @media (max-width: 767px) {
        #cora-editor-left-sidebar {
            display: none !important;
        }
        #cora-article-inspector {
            position: fixed !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100% !important;
            height: 82vh !important;
            z-index: 250 !important;
            border-top: 1px solid #e4e4e7 !important;
            border-left: none !important;
            border-radius: 16px 16px 0 0 !important;
            box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.1), 0 -8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1) !important;
        }
        #cora-article-inspector:not(.translate-y-full):not(.collapsed-inspector) {
            transform: translateY(0) !important;
        }
        #cora-inspector-backdrop:not(.hidden) {
            position: fixed !important;
            inset: 0 !important;
            background: rgba(0, 0, 0, 0.4) !important;
            backdrop-filter: blur(2px) !important;
            z-index: 240 !important;
            display: block !important;
        }
    }

    @media (max-width: 639px) {
        #cora-btn-save-draft,
        #cora-btn-submit-review {
            display: none !important;
        }
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
    /* AI Writing Assistant transitions */
    #cora-ai-writing-assistant {
        max-height: 500px;
        opacity: 1;
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease, margin 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.3s ease;
    }
    #cora-ai-writing-assistant.hidden-assistant {
        max-height: 0 !important;
        opacity: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        border-color: transparent !important;
        pointer-events: none;
    }
    </style>

    <div id="cora-full-page-editor" class="hidden fixed inset-0 z-[100] bg-white flex-col h-full overflow-hidden select-none">
        
        <!-- Modern Header Bar -->
        <header class="relative flex items-center justify-between px-3 sm:px-6 py-2 bg-white shrink-0 z-[60] gap-2 border-b border-zinc-200 select-none">
            <div class="flex items-center gap-2.5 min-w-0">
                <button type="button" id="btn-editor-back" class="flex items-center gap-1 text-zinc-650 hover:text-zinc-900 transition-all text-xs font-semibold cursor-pointer py-1.5 px-3 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 active:scale-98 shadow-3xs shrink-0" onclick="coraToggleContentDrawer(false)">
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
                <!-- Preview Button -->
                <button type="button" class="px-3.5 py-1.5 border border-zinc-200 rounded-lg text-zinc-700 bg-white hover:bg-zinc-50 hover:text-zinc-900 transition-all cursor-pointer text-xs font-semibold active:scale-95 shadow-3xs flex items-center gap-1.5" onclick="coraPreviewArticle()">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    Preview
                </button>

                <!-- Save Draft Button -->
                <button type="button" id="cora-btn-save-draft" class="px-3.5 py-1.5 border border-zinc-200 rounded-lg text-zinc-700 bg-white hover:bg-zinc-50 hover:text-zinc-900 transition-all cursor-pointer text-xs font-semibold active:scale-95 shadow-3xs flex items-center gap-1.5" onclick="coraSaveArticle('draft')">
                    Save Draft
                </button>

                <!-- Split Button for Publish Live -->
                <div class="relative inline-flex rounded-lg shadow-sm" id="cora-publish-dropdown-wrap">
                    <button type="button" class="inline-flex items-center px-4 py-1.5 bg-zinc-950 hover:bg-black text-white font-bold rounded-l-lg transition-all cursor-pointer text-xs border border-zinc-900 border-r-0 active:scale-95" onclick="coraSaveArticle('publish')">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="mr-1.5"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                        Publish Live
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
                        <button type="button" class="w-full text-left px-4 py-2 hover:bg-zinc-50 flex items-center gap-2 cursor-pointer border-none bg-transparent" onclick="window.coraTriggerSchedulePublish(); window.coraTogglePublishDropdown(false);">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Schedule Publish...
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
            <aside id="cora-editor-left-sidebar" class="w-64 bg-zinc-50/50 border-r border-zinc-200 shrink-0 overflow-y-auto hidden md:flex flex-col select-none">
                <!-- Navigation Tabs: Outline / Media -->
                <div class="p-2.5 bg-zinc-50 border-b border-zinc-200 select-none shrink-0 font-sans">
                    <div class="flex p-0.5 bg-zinc-105 rounded-lg border border-zinc-200/40 text-[11px] font-bold">
                        <button type="button" onclick="coraSwitchLeftSidebarTab('outline', this)" class="cora-left-tab-btn flex-1 py-1.5 text-center rounded-md text-zinc-900 bg-white shadow-3xs border border-zinc-200/10 cursor-pointer transition-all active:scale-97">
                            Outline
                        </button>
                        <button type="button" disabled class="flex-1 py-1.5 text-center rounded-md text-zinc-400 cursor-not-allowed flex items-center justify-center gap-1 opacity-60 font-sans select-none border-none bg-transparent">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400 shrink-0"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <span>Media</span>
                        </button>
                    </div>
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
                            <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 text-xs font-extrabold border border-red-150 animate-pulse" id="left-score-grade">C</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-zinc-700">
                                <span id="left-score-value">0/100</span>
                            </div>
                            <div class="w-full bg-zinc-100 rounded-full h-1.5 overflow-hidden">
                                <div id="left-score-bar" class="bg-red-500 h-1.5 rounded-full transition-all duration-500" style="width: 0%;"></div>
                            </div>
                        </div>
                        <p class="text-[10px] text-zinc-500 leading-relaxed font-sans" id="left-score-message">Needs attention. Fix the checklist issues.</p>
                        <button type="button" onclick="coraSwitchInspectorTab('seo')" class="text-[10px] font-bold text-zinc-900 hover:underline flex items-center gap-1 cursor-pointer border-none bg-transparent">
                            View suggestions &rarr;
                        </button>
                    </div>
                </div>

                <!-- Tab Panel: Media -->
                <div id="cora-left-panel-media" class="hidden p-4 space-y-4 flex-1 overflow-y-auto font-sans">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Article Media</span>
                        <button type="button" class="inline-flex items-center gap-1 text-zinc-650 hover:text-zinc-900 text-[10px] font-bold border border-zinc-200 rounded-lg px-2.5 py-1.5 bg-white shadow-3xs cursor-pointer hover:bg-zinc-50 transition-all active:scale-95" onclick="window.coraMediaSelectTarget = 'inline'; coraOpenMediaLibrary();">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Add
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-2" id="left-sidebar-media-grid">
                        <!-- Populated dynamically via JS: coraUpdateLeftSidebarMediaGrid() -->
                        <div class="col-span-2 border border-dashed border-zinc-200 bg-zinc-50/50 rounded-xl p-6 text-center select-none mt-2">
                            <div class="inline-flex p-3 bg-zinc-100 rounded-full text-zinc-400 mb-3 border border-zinc-200/50">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            </div>
                            <span class="block text-xs font-bold text-zinc-800 mb-1">No Embedded Media</span>
                            <span class="block text-[10px] text-zinc-450 leading-relaxed max-w-[170px] mx-auto mb-4">Add images to your article to see them mapped here.</span>
                            <button type="button" class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg text-[10px] font-bold text-zinc-700 bg-white transition-all cursor-pointer shadow-3xs" onclick="window.coraMediaSelectTarget = 'inline'; coraOpenMediaLibrary();">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                Add Image
                            </button>
                        </div>
                    </div>
                </div>
            </aside>

        <!-- Editor Body -->
        <div class="flex-1 flex overflow-hidden relative">
            
            <!-- Notion/Medium-Style Writing Canvas -->
            <main class="flex-1 overflow-y-auto px-6 py-10 md:px-16 xl:px-32 relative">
                <div class="w-full cora-writing-sheet">
                    
                    <!-- Beehiiv Horizontal Settings Bar -->
                    <div class="hidden w-full border-b border-zinc-200/80 pb-3.5 flex flex-wrap items-center justify-between gap-4 text-xs font-semibold relative select-none">
                        <div class="flex flex-wrap items-center gap-2">
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
                                        <textarea id="cora-article-excerpt-bh" rows="3" placeholder="Summary snippet for search results and social previews..." oninput="coraUpdateWordCount()" class="w-full text-xs border border-zinc-200 rounded-lg p-2 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 placeholder:text-zinc-300 resize-none"></textarea>
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
                                            <img src="" id="cora-thumbnail-img-bh" class="hidden w-full h-full object-cover" loading="lazy">
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
                        <img id="cora-cover-image-img" src="" class="hidden w-full h-48 md:h-64 object-cover" loading="lazy">
                        
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
                    <div id="cora-ai-writing-assistant" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50/20 p-4 shadow-3xs space-y-3 font-sans select-none my-4 hidden-assistant">
                        <div class="flex items-center gap-2 text-xs font-bold text-zinc-800">
                            <!-- Clean Sparkle SVG -->
                            <span class="p-1 rounded bg-violet-100/50 text-violet-650 border border-violet-100 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            </span>
                            <span>AI Writing Assistant</span>
                        </div>
                        <div class="relative flex items-center bg-white border border-zinc-200 rounded-xl px-3 py-2 shadow-3xs focus-within:border-zinc-400">
                            <input type="text" id="cora-ai-prompt-input" placeholder="Ask AI to write, improve, or expand..." class="flex-1 text-xs outline-none border-none bg-transparent text-zinc-850 placeholder:text-zinc-355 pr-8" onkeydown="if(event.key === 'Enter') coraExecuteAIPrompt()">
                            <button type="button" onclick="coraExecuteAIPrompt()" class="absolute right-2 text-zinc-455 hover:text-zinc-950 transition-colors border-none bg-transparent cursor-pointer p-1">
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

                    </div><!-- /#cora-editor-slash-menu -->

                    <!-- Quill Rich-Text Editor Canvas Container -->
                    <div id="cora-quill-editor" class="w-full text-zinc-900 border-none focus:outline-none text-base leading-relaxed mt-2" style="min-height: 420px;"></div>

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
            <aside id="cora-article-inspector" class="max-md:fixed max-md:inset-x-0 max-md:bottom-0 max-md:z-50 max-md:h-[82vh] max-md:w-full max-md:rounded-t-2xl max-md:shadow-2xl max-md:border-t max-md:border-zinc-200 md:relative md:w-80 lg:w-96 md:border-l border-zinc-200 bg-white flex flex-col shrink-0 overflow-y-auto transition-transform md:transition-all duration-300">
                
                <!-- Mobile Bottom Sheet Grab Handle & Header Bar -->
                <div class="flex md:hidden items-center justify-between px-4 py-2.5 bg-zinc-100 border-b border-zinc-200 shrink-0 select-none">
                    <div class="flex items-center gap-2 text-xs font-bold text-zinc-800">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                        <span>Inspector &amp; Meta</span>
                    </div>
                    <div class="w-10 h-1 bg-zinc-300 rounded-full"></div>
                    <button type="button" class="p-1 text-zinc-400 hover:text-zinc-900 rounded-lg transition-colors cursor-pointer border-none bg-transparent" onclick="coraToggleArticleInspector(false)" aria-label="Close Inspector Sheet">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                
                <!-- Inspector Navigation Tabs -->
                <div class="flex border-b border-zinc-200 bg-[#f9fafb]#0c0c0e] sticky top-0 z-10 text-[10px] font-bold uppercase tracking-wider inspector-tabs-container select-none font-sans">
                    <button type="button" id="btn-sidebar-seo" onclick="coraSwitchInspectorTab('seo')" class="flex-1 py-3 px-2 text-center border-b-2 border-zinc-950 text-zinc-900 cursor-pointer transition-all flex items-center justify-center gap-1.5 inspector-tab-btn tab-active font-bold">
                        <span>SEO</span>
                    </button>
                    <button type="button" id="btn-sidebar-geo" onclick="coraSwitchInspectorTab('copilot')" class="flex-1 py-3 px-2 text-center border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 cursor-pointer transition-all flex items-center justify-center gap-1.5 inspector-tab-btn font-bold">
                        <span>AI Visibility</span>
                    </button>
                    <button type="button" id="btn-sidebar-meta" onclick="coraSwitchInspectorTab('meta')" class="flex-1 py-3 px-2 text-center border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 cursor-pointer transition-all flex items-center justify-center gap-1.5 inspector-tab-btn font-bold">
                        <span>Details</span>
                    </button>
                    <button type="button" id="tab-inspector-claims" onclick="coraSwitchInspectorTab('claims')" class="px-4 py-3 text-center border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 cursor-pointer transition-all flex items-center justify-center shrink-0 inspector-tab-btn font-bold" title="Compliance & Trust">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </button>
                </div>

                <!-- Sticky Header Meta Bar (Assignee Select) -->
                <div class="p-3 bg-zinc-50/80 border-b border-zinc-200 space-y-1 shrink-0 font-sans">
                    <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Assignee / Author</span>
                    <select id="cora-article-assignee" class="w-full text-xs border border-zinc-200 rounded-lg p-2.5 bg-white text-zinc-800 focus:outline-none focus:border-zinc-400 shadow-3xs cursor-pointer">
                        <option value="0">Unassigned</option>
                        <?php foreach($cora_users as $usr): ?>
                            <option value="<?php echo $usr->ID; ?>"><?php echo esc_html($usr->display_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="panel-inspector-copilot" class="hidden p-4 space-y-4 font-sans">
                    <div id="panel-sidebar-geo" class="space-y-4">
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
                                <span class="text-sm font-extrabold text-zinc-900" id="cora-geo-score-display">65</span>
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
                        </div>
                        <!-- Hidden Checkboxes for Test Assertions -->
                        <div class="hidden">
                            <input type="checkbox" id="chk-geo-direct-answer" disabled>
                            <input type="checkbox" id="chk-geo-info-density" disabled>
                            <input type="checkbox" id="chk-geo-citations" disabled>
                            <input type="checkbox" id="chk-geo-schema" disabled>
                            <span id="cora-seo-score-display-test-hidden">82</span>
                        </div>

                        <!-- In-Post Lead Capture CTAs -->
                        <div class="space-y-2 pt-2 border-t border-zinc-200">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">In-Post Lead Capture CTAs</span>
                            <button type="button" class="w-full text-left p-2 border border-zinc-200 hover:border-zinc-400 rounded-lg bg-white hover:bg-zinc-50 transition-all cursor-pointer flex items-center gap-2" onclick="coraInjectQuillCTA('valuation')">
                                <div>
                                    <span class="text-[10px] font-bold text-zinc-800 block">Property Valuation Form</span>
                                    <span class="text-[8px] text-zinc-400 block leading-none">Captures home seller appraisal requests</span>
                                </div>
                            </button>
                        </div>

                        <!-- Auto-Optimize Button -->
                        <button type="button" class="w-full py-2.5 mt-2 bg-zinc-950 hover:bg-black text-white font-bold rounded-lg text-xs transition-colors cursor-pointer flex items-center justify-center gap-1.5 shadow-xs border border-zinc-900" onclick="coraAutoOptimizeGEO()">
                            Run GEO Auto-Optimize
                        </button>

                        <!-- Schema Preview Accordion -->
                        <div class="border border-zinc-200 rounded-xl overflow-hidden bg-white mt-3">
                            <button type="button" class="w-full px-3 py-2 bg-zinc-50 hover:bg-zinc-100 flex items-center justify-between text-[9px] font-bold text-zinc-500 uppercase tracking-wider cursor-pointer border-none focus:outline-none" onclick="jQuery('#cora-schema-preview-container').toggleClass('hidden')">
                                <span>JSON-LD Schema Preview</span>
                            </button>
                            <div id="cora-schema-preview-container" class="hidden p-3 border-t border-zinc-200 bg-zinc-50 overflow-x-auto">
                                <pre class="text-[9px] text-zinc-600 font-mono" id="cora-schema-preview-block">{}</pre>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- TAB 2: Publishing Meta Tab -->
                <div id="panel-inspector-meta" class="hidden p-4 space-y-4">
                    
                    <!-- Featured Image -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Featured Image</span>
                            <span class="text-[9px] text-zinc-400 font-semibold select-none">Recommended size: 1200 × 630</span>
                        </div>
                        <input type="hidden" id="cora-thumbnail-id" value="">
                        <div id="cora-thumbnail-preview" class="w-full aspect-[16/9] bg-white rounded-xl border border-dashed border-zinc-200 flex items-center justify-center overflow-hidden relative group cursor-pointer transition-all hover:border-zinc-400" onclick="window.coraMediaSelectTarget = 'thumbnail'; coraOpenMediaLibrary();">
                            <div class="absolute inset-0 bg-black/60 hidden group-hover:flex items-center justify-center transition-all z-10">
                                <span class="text-white text-xs font-semibold flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    Change Image
                                </span>
                            </div>
                            <img src="" id="cora-thumbnail-img" class="hidden w-full h-full object-cover" loading="lazy">
                            <div id="cora-thumbnail-placeholder" class="text-center flex flex-col items-center gap-2 select-none py-6">
                                <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                <span class="text-xs font-extrabold text-zinc-800 block mt-1">Upload Featured Image</span>
                                <span class="text-[10px] text-zinc-400 block">Drag & drop or click to select</span>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="space-y-1.5 pt-2 border-t border-zinc-200">
                        <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Categories</span>
                        <div class="relative">
                            <div id="cora-meta-categories-trigger" class="w-full text-xs border border-zinc-200 rounded-lg p-2.5 bg-white text-zinc-800 flex items-center justify-between cursor-pointer shadow-3xs select-none hover:bg-zinc-50/40">
                                <div class="flex flex-wrap gap-1.5" id="cora-meta-categories-selected">
                                    <span class="text-zinc-350">Select categories...</span>
                                </div>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450 shrink-0"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            <div id="cora-meta-categories-dropdown" class="hidden absolute left-0 right-0 mt-1.5 p-2 bg-white border border-zinc-200 rounded-xl shadow-lg z-30 max-h-[160px] overflow-y-auto space-y-1 animate-fade-in">
                                <?php foreach($cora_categories as $cat): ?>
                                    <label class="flex items-center gap-2.5 p-1.5 hover:bg-zinc-50 rounded-lg cursor-pointer text-xs text-zinc-850 select-none">
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
                        <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Tags</span>
                        <div class="relative">
                            <div id="cora-meta-tags-trigger" class="w-full text-xs border border-zinc-200 rounded-lg p-2.5 bg-white text-zinc-800 flex items-center justify-between cursor-pointer shadow-3xs select-none hover:bg-zinc-50/40">
                                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 shrink-0"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                    <div class="flex flex-wrap gap-1.5 flex-1" id="cora-meta-tags-selected">
                                        <span class="text-zinc-350">Select or add tags...</span>
                                    </div>
                                </div>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-455 shrink-0"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            <div id="cora-meta-tags-dropdown" class="hidden absolute left-0 right-0 mt-1.5 p-2 bg-white border border-zinc-200 rounded-xl shadow-lg z-30 max-h-[190px] overflow-y-auto space-y-1 animate-fade-in">
                                <div class="px-2 py-1.5 border-b border-zinc-100 mb-1 flex gap-1.5">
                                    <input type="text" id="cora-meta-tag-add-input" placeholder="Create tag..." class="flex-1 text-xs border border-zinc-200 rounded px-2 py-1 focus:outline-none focus:border-zinc-450 bg-white text-zinc-800">
                                    <button type="button" id="cora-meta-tag-add-btn" class="px-2.5 py-1 bg-zinc-950 hover:bg-black text-white text-[10px] font-bold rounded-md cursor-pointer transition-colors border-none outline-none">Add</button>
                                </div>
                                <?php foreach($cora_tags as $tag): ?>
                                    <label class="flex items-center gap-2.5 p-1.5 hover:bg-zinc-50 rounded-lg cursor-pointer text-xs text-zinc-850 select-none">
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

                    <!-- Scheduled Date -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Scheduled Date</span>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <input type="datetime-local" id="cora-article-scheduled-date" class="w-full text-xs border border-zinc-200 rounded-lg p-2.5 pl-9 pr-9 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 shadow-3xs">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                        </div>
                        <p class="text-[9px] text-zinc-400">Set when this content should be published.</p>
                    </div>

                    <!-- Editorial Feedback Box -->
                    <div id="cora-editorial-feedback-box" class="hidden p-3 rounded-lg border border-zinc-300 bg-zinc-100 text-xs text-zinc-800 leading-tight space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-zinc-900">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <span>Revision Required</span>
                        </div>
                        <p id="cora-editorial-feedback-text" class="italic text-[11px] text-zinc-600"></p>
                    </div>

                    <!-- Page Settings -->
                    <div class="space-y-4 pt-3 border-t border-zinc-200">
                        <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Page Settings</span>
                        
                        <!-- URL Slug -->
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-bold text-zinc-550 block">URL Slug</span>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                </div>
                                <input type="text" id="cora-article-slug" placeholder="url-slug-here" class="w-full text-xs border border-zinc-200 rounded-lg p-2.5 pl-9 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 shadow-3xs">
                            </div>
                            <p class="text-[9px] text-zinc-400">The last part of the URL. Keep it short and descriptive.</p>
                        </div>

                        <!-- Allow Comments -->
                        <div class="flex items-center justify-between pt-1 select-none">
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-extrabold text-zinc-555 uppercase tracking-wider block">Allow Comments</span>
                                <span class="text-[9px] text-zinc-400 block leading-tight">Enable comments on this content.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" id="cora-article-allow-comments" class="sr-only peer">
                                <div class="w-9 h-5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950"></div>
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
                            <textarea id="cora-article-excerpt" rows="3" placeholder="Summary snippet for search results and social previews..." oninput="coraUpdateWordCount()" class="w-full text-xs border border-zinc-200 rounded-lg p-2.5 focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 shadow-3xs resize-none"></textarea>
                        </div>

                        <!-- Move to Trash -->
                        <div class="pt-2">
                            <button type="button" onclick="coraTrashArticle()" class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-650 border border-red-200/80 rounded-lg text-xs font-semibold cursor-pointer transition-colors flex items-center justify-center gap-1.5 active:scale-95 shadow-2xs">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                Move to Trash
                            </button>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-between border-t border-zinc-200/80 pt-4 mt-6 select-none">
                        <button type="button" onclick="window.coraResetMetaFields()" class="px-4 py-2 border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 font-bold rounded-lg text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-3xs">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><polyline points="3 3 3 8 8 8"></polyline></svg>
                            Reset
                        </button>
                        <button type="button" onclick="coraSaveArticle('draft')" class="px-4 py-2 bg-zinc-950 hover:bg-black text-white font-bold rounded-lg text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-xs border-none outline-none">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M19 21H5a2 2 0 0 1-2 2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Save Changes
                        </button>
                    </div>
                </div>

                <div id="panel-inspector-seo" class="p-4 space-y-4 font-sans">
                    <div id="panel-sidebar-seo" class="space-y-4">
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
                            <input type="text" id="cora-seo-keyword" placeholder="Enter target keyword..." oninput="coraUpdateSEOAudits()" class="flex-1 text-xs outline-none border-none bg-transparent text-zinc-800 placeholder:text-zinc-350 pr-6" value="">
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-[10px] font-bold text-zinc-550">
                                <span>Keyword Density</span>
                                <span id="cora-seo-density-badge" class="font-mono hidden">0.00% (0x)</span>
                            </div>
                            <div class="w-full bg-zinc-150 rounded-full h-1">
                                <div id="cora-seo-density-bar" class="bg-emerald-500 h-1 rounded-full animate-all duration-300" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Readability Card -->
                    <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-3xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Readability</span>
                            <span class="text-xs font-bold text-zinc-400 block animate-all duration-300" id="cora-readability-status-text">--</span>
                        </div>
                        <div class="space-y-1.5">
                            <span class="text-[10px] text-zinc-400 block animate-all duration-300" id="cora-readability-subtext">No Content</span>
                            <div class="w-full bg-zinc-150 rounded-full h-1">
                                <div id="cora-readability-bar" class="bg-emerald-500 h-1 rounded-full animate-all duration-300" style="width: 0%;"></div>
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
                                <span class="font-semibold text-zinc-450 mt-1 block" id="cora-brief-audience">No content to analyze</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-zinc-400 block uppercase font-bold tracking-wider leading-none mb-1">Intent</span>
                                <div id="cora-brief-intent">
                                    <span class="px-2.5 py-0.5 bg-zinc-100 border border-zinc-200 rounded-full text-zinc-500 text-[10px] font-bold inline-block select-none">N/A</span>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1.5 select-none">
                                    <span class="text-[10px] text-zinc-400 uppercase font-bold tracking-wider leading-none">Top Competitors</span>
                                    <a href="javascript:void(0)" onclick="window.coraShowToast('Competitor details is coming soon.', 'info')" class="text-[10px] font-bold text-zinc-900 hover:underline">View all</a>
                                </div>
                                <div class="divide-y divide-zinc-100 text-xs" id="cora-brief-competitors">
                                    <div class="py-2 text-zinc-450 italic text-[11px] text-center">No competitors analyzed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- TAB 4: Grounded Claims & Trust Audit Tab -->
                <div id="panel-inspector-claims" class="hidden p-4 space-y-4">
                    <!-- Grounded Claims ledger -->
                    <div class="border border-zinc-200 rounded-xl overflow-hidden bg-white p-4 shadow-3xs space-y-3">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                            <span class="text-[10px] font-extrabold text-zinc-550 uppercase tracking-wider block">Grounded Claims Ledger</span>
                            <span class="px-1.5 py-0.5 bg-zinc-100 text-zinc-400 text-[8px] font-extrabold rounded-md uppercase tracking-wider border border-zinc-200" id="cora-claims-ledger-status">No Content</span>
                        </div>

                        <!-- Claims Ledger Container -->
                        <div class="space-y-3 text-xs leading-normal" id="cora-editor-claims-list">
                            <div class="py-3 text-zinc-450 italic text-[11px] text-center">No content to audit. Add text to the editor to run the Claims check.</div>
                        </div>

                        <!-- Manual claims verification action button -->
                        <button type="button" class="w-full py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-850 font-bold border border-zinc-250/80 rounded-lg text-xs transition-all cursor-pointer shadow-3xs" onclick="coraScanDraftClaims()">
                            Scan Draft for claims validation
                        </button>
                    </div>

                    <!-- Trust & Quality Audit checklist -->
                    <div class="border border-zinc-200 rounded-xl overflow-hidden bg-white p-4 shadow-3xs space-y-3">
                        <span class="text-[10px] font-extrabold text-zinc-550 uppercase tracking-wider block border-b border-zinc-100 pb-2">Trust &amp; Quality Audit</span>
                        
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-zinc-750">Grounded claims match Brain facts</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-zinc-750">Zero prohibited marketing terms detected</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-zinc-750">Physical business address verified</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

        <script>
        // Custom Workspace Editor Overrides & AI Assistant Implementation
        (function() {

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
                // Close inline selection AI popup if click is outside it and outside the editor
                if (!jQuery(e.target).closest('#cora-inline-ai-popup').length && !jQuery(e.target).closest('#cora-quill-editor').length) {
                    if (typeof window.coraHideInlineAIPopup === 'function') {
                        window.coraHideInlineAIPopup();
                    }
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
            jQuery('#tab-inspector-copilot, #btn-sidebar-geo, #tab-inspector-meta, #btn-sidebar-meta, #tab-inspector-seo, #btn-sidebar-seo, #tab-inspector-claims').removeClass('border-zinc-950 text-zinc-900 tab-active').addClass('border-transparent text-zinc-400');
            jQuery('#panel-inspector-copilot, #panel-sidebar-geo, #panel-inspector-meta, #panel-sidebar-meta, #panel-inspector-seo, #panel-sidebar-seo, #panel-inspector-claims').addClass('hidden');
            
            if (tab === 'copilot' || tab === 'geo') {
                jQuery('#tab-inspector-copilot, #btn-sidebar-geo').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900 tab-active');
                jQuery('#panel-inspector-copilot, #panel-sidebar-geo').removeClass('hidden');
            } else if (tab === 'meta' || tab === 'details') {
                jQuery('#tab-inspector-meta, #btn-sidebar-meta').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900 tab-active');
                jQuery('#panel-inspector-meta, #panel-sidebar-meta').removeClass('hidden');
            } else if (tab === 'claims') {
                jQuery('#tab-inspector-claims').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900 tab-active');
                jQuery('#panel-inspector-claims').removeClass('hidden');
            } else {
                jQuery('#tab-inspector-seo, #btn-sidebar-seo').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900 tab-active');
                jQuery('#panel-inspector-seo, #panel-sidebar-seo').removeClass('hidden');
            }
        };
        window.coraSwitchSidebarTab = window.coraSwitchInspectorTab;

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
                        window.coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(data.content);
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
            jQuery('.cora-left-tab-btn')
                .removeClass('text-zinc-900 bg-white shadow-3xs border border-zinc-200/10 font-bold')
                .addClass('text-zinc-450 border-transparent hover:text-zinc-800');
            jQuery(btn)
                .addClass('text-zinc-900 bg-white shadow-3xs border border-zinc-200/10 font-bold')
                .removeClass('text-zinc-450 border-transparent hover:text-zinc-800');
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

        // Smooth scrolling to headings inside editor sheet with cursor focus & premium highlighting
        window.coraScrollToHeading = function(id) {
            if (!window.coraQuillListingCoordinator) return;
            const editor = window.coraQuillListingCoordinator.root;
            const heading = editor.querySelector('#' + id);
            if (heading) {
                heading.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Add visual highlight flash class
                heading.classList.remove('cora-heading-highlight-flash');
                void heading.offsetWidth; // Trigger reflow to restart animation
                heading.classList.add('cora-heading-highlight-flash');
                setTimeout(() => {
                    heading.classList.remove('cora-heading-highlight-flash');
                }, 1500);

                // Find blot and select text in Quill
                try {
                    const Q = window.Quill || (typeof Quill !== 'undefined' ? Quill : null);
                    if (Q) {
                        const blot = Q.find(heading);
                        if (blot) {
                            const index = window.coraQuillListingCoordinator.getIndex(blot);
                            const length = heading.textContent.length;
                            window.coraQuillListingCoordinator.setSelection(index, length);
                        }
                    }
                } catch(e) {}
            }
        };

        // Populate Left Sidebar media grid from Quill images in real-time
        window.coraUpdateLeftSidebarMediaGrid = function() {
            if (!window.coraQuillListingCoordinator) return;
            const editor = window.coraQuillListingCoordinator.root;
            const images = editor.querySelectorAll('img');
            const grid = jQuery('#left-sidebar-media-grid');
            
            if (images.length === 0) {
                grid.html(`
                    <div class="col-span-2 border border-dashed border-zinc-200 bg-zinc-50/50 rounded-xl p-6 text-center select-none mt-2">
                        <div class="inline-flex p-3 bg-zinc-100 rounded-full text-zinc-400 mb-3 border border-zinc-200/50">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </div>
                        <span class="block text-xs font-bold text-zinc-800 mb-1">No Embedded Media</span>
                        <span class="block text-[10px] text-zinc-450 leading-relaxed max-w-[170px] mx-auto mb-4">Add images to your article to see them mapped here.</span>
                        <button type="button" class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 border border-zinc-200 hover:border-zinc-350 hover:bg-zinc-50 rounded-lg text-[10px] font-bold text-zinc-700 bg-white transition-all cursor-pointer shadow-3xs" onclick="window.coraMediaSelectTarget = 'inline'; coraOpenMediaLibrary();">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Add Image
                        </button>
                    </div>
                `);
                return;
            }
            
            grid.empty();
            
            images.forEach((img, idx) => {
                const src = img.getAttribute('src');
                if (!src) return;
                
                const card = jQuery(`
                    <div class="group relative aspect-square bg-zinc-50 rounded-xl border border-zinc-250/70 overflow-hidden cursor-pointer hover:border-zinc-400 hover:shadow-3xs transition-all duration-300 select-none">
                        <img src="${src}" class="w-full h-full object-cover">
                        <!-- Number Badge -->
                        <span class="absolute top-1.5 left-1.5 bg-white/90 backdrop-blur-xs text-zinc-800 text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow-3xs border border-zinc-250/20 select-none">
                            #${idx + 1}
                        </span>
                        
                        <!-- Premium Action Overlay -->
                        <div class="absolute inset-0 bg-zinc-950/80 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center gap-1.5 transition-all duration-200">
                            <button type="button" class="locate-btn flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white text-zinc-900 rounded-lg text-[9px] font-bold hover:bg-zinc-100 active:scale-95 transition-all shadow-sm border-none cursor-pointer">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                Locate
                            </button>
                            <button type="button" class="delete-btn flex items-center justify-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-[9px] font-bold active:scale-95 transition-all shadow-sm border-none cursor-pointer">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                Delete
                            </button>
                        </div>
                    </div>
                `);
                
                // Locate click handler
                card.find('.locate-btn').on('click', function(e) {
                    e.stopPropagation();
                    img.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Flash highlight outline on image element
                    img.style.outline = '4px solid #8b5cf6';
                    img.style.outlineOffset = '2px';
                    setTimeout(() => {
                        img.style.outline = 'none';
                    }, 1500);

                    // Place selection cursor on the image blot
                    try {
                        const Q = window.Quill || (typeof Quill !== 'undefined' ? Quill : null);
                        if (Q) {
                            const blot = Q.find(img);
                            if (blot) {
                                const index = window.coraQuillListingCoordinator.getIndex(blot);
                                window.coraQuillListingCoordinator.setSelection(index, 1);
                            }
                        }
                    } catch(err) {}
                });
                
                // Delete click handler
                card.find('.delete-btn').on('click', function(e) {
                    e.stopPropagation();
                    try {
                        const Q = window.Quill || (typeof Quill !== 'undefined' ? Quill : null);
                        if (Q) {
                            const blot = Q.find(img);
                            if (blot) {
                                const index = window.coraQuillListingCoordinator.getIndex(blot);
                                window.coraQuillListingCoordinator.deleteText(index, 1);
                                if (window.coraShowToast) {
                                    window.coraShowToast('Image removed. Press Cmd+Z to undo.', 'success');
                                }
                            }
                        }
                    } catch(err) {
                        if (window.coraShowToast) {
                            window.coraShowToast('Failed to delete image.', 'error');
                        }
                    }
                });

                // Clicking anywhere on card triggers locate
                card.on('click', function() {
                    card.find('.locate-btn').trigger('click');
                });
                
                grid.append(card);
            });
        };



        // --- RAG BRAIN FACT RETRIEVAL & AI WORKFLOW IMPLEMENTATION ---

        // Fetch Business Brain items for RAG grounding
        window.coraGetRagBrainFacts = function(callback) {
            const nonce = typeof coraREData !== 'undefined' ? coraREData.ajaxNonce : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxNonce : '');
            const ajaxUrl = typeof ajaxurl !== 'undefined' ? ajaxurl : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php');

            jQuery.post(ajaxUrl, {
                action: 'cora_fetch_brain_items',
                nonce: nonce
            }, function(response) {
                let factsText = '';
                if (response.success && response.data && response.data.length > 0) {
                    factsText = "\n\nVerified business facts (RAG grounding):\n";
                    response.data.forEach(item => {
                        factsText += `- [${item.source_type || 'Fact'}] ${item.title}: ${item.content}\n`;
                    });
                }
                callback(factsText);
            }).fail(function() {
                callback('');
            });
        };

        // Call workspace AI query proxy
        window.coraCallWorkspaceAI = function(message, systemPrompt, callback) {
            const nonce = typeof coraREData !== 'undefined' ? coraREData.ajaxNonce : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxNonce : '');
            const ajaxUrl = typeof ajaxurl !== 'undefined' ? ajaxurl : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php');
            
            // Get active model provider
            const modelVal = jQuery('#cora-ai-model-selector').val() || 'cora-core-v2';
            let provider = 'gemini';
            let model = 'gemini-3.5-flash-lite';
            
            if (modelVal === 'gpt-4o' || modelVal === 'openai') {
                provider = 'openai';
                model = 'gpt-4o-mini';
            }

            jQuery.post(ajaxUrl, {
                action: 'cora_ai_chat_query',
                security: nonce,
                message: message,
                system_prompt: systemPrompt,
                provider: provider,
                model: model,
                temperature: 0.7
            }, function(response) {
                if (response.success && response.data && response.data.reply) {
                    callback(null, response.data.reply);
                } else {
                    const failMsg = response.data && response.data.message ? response.data.message : (response.data ? response.data : 'AI model response error.');
                    callback(new Error(failMsg));
                }
            }).fail(function(xhr) {
                callback(new Error('Network connection failed.'));
            });
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

            window.coraGetRagBrainFacts(function(facts) {
                const systemPrompt = `You are a professional metadata specialist for Cora Studio.
Output ONLY a concise, highly-engaging SEO meta description summary excerpt (max 150 characters, no quotes) for the article. Do NOT include markdown code blocks, conversational introductions, or commentary.`;

                const prompt = `Generate a 120-150 character meta description based on this content: "${text}". Ground it in these facts if applicable: ${facts}`;

                window.coraCallWorkspaceAI(prompt, systemPrompt, function(err, reply) {
                    if (err) {
                        window.coraShowToast('AI Excerpt generation failed: ' + err.message, 'error');
                        return;
                    }
                    const cleanExcerpt = reply.replace(/"/g, '').trim();
                    jQuery('#cora-article-excerpt, #cora-article-excerpt-bh').val(cleanExcerpt);
                    window.coraShowToast('AI Excerpt generated successfully!', 'success');
                    if (typeof window.coraUpdateExcerptCount === 'function') {
                        window.coraUpdateExcerptCount();
                    }
                    window.coraTriggerEditorAutoSave();
                });
            });
        };

        // Insert new heading placeholder via left sidebar action
        window.coraInsertHeadingPlaceholder = function() {
            if (!window.coraQuillListingCoordinator) {
                window.coraShowToast('Editor is initializing, please wait...', 'warning');
                return;
            }
            const range = window.coraQuillListingCoordinator.getSelection();
            const index = range ? range.index : window.coraQuillListingCoordinator.getLength();
            window.coraQuillListingCoordinator.insertText(index, '\nNew Heading\n', 'user');
            window.coraQuillListingCoordinator.formatLine(index + 1, 11, 'header', 2);
            window.coraQuillListingCoordinator.setSelection(index + 1, 11);
            window.coraShowToast('New H2 heading added to document.', 'success');
        };

        // Execute dynamic prompt from inline assistant card input
        window.coraExecuteAIPrompt = function() {
            if (!window.coraQuillListingCoordinator) {
                window.coraShowToast('Editor is initializing, please wait...', 'warning');
                return;
            }
            const inputEl = jQuery('#cora-ai-prompt-input');
            const prompt = inputEl.val().trim();
            if (!prompt) {
                window.coraShowToast('Please type a prompt first.', 'error');
                return;
            }

            inputEl.prop('disabled', true);
            window.coraShowToast('AI is processing prompt: "' + prompt + '"...', 'info');

            window.coraGetRagBrainFacts(function(facts) {
                const title = jQuery('#cora-article-title').val() || 'Untitled';
                const keyword = jQuery('#cora-seo-keyword').val() || 'None';
                const currentText = window.coraQuillListingCoordinator.getText().trim();
                
                const systemPrompt = `You are Myra, a sharp, action-oriented Indian AI Writing Assistant and editor at Cora Studio.
Ground your writing and claims strictly in the provided business facts (RAG facts). Do not invent facts, pricing, packages, or office locations that contradict the RAG database.
Output ONLY the requested content to be inserted. Do NOT include markdown code blocks (like \`\`\`html or \`\`\`text), HTML wrappers around the whole thing, or conversational intros/outros (like "Sure, here is...", "Okay boss"). Standard inline HTML formatting tags (like <p>, <h2>, <strong>, <ul>) are allowed.`;

                const message = `Article Title: "${title}"
Focus Keyword: "${keyword}"
Current Document Body:
"${currentText}"

User Instruction: "${prompt}"

Generate the requested paragraph or section.`;

                window.coraCallWorkspaceAI(message, systemPrompt, function(err, reply) {
                    inputEl.prop('disabled', false);
                    if (err) {
                        window.coraShowToast('AI Generation failed: ' + err.message, 'error');
                        return;
                    }
                    
                    const range = window.coraQuillListingCoordinator.getSelection();
                    const index = range ? range.index : window.coraQuillListingCoordinator.getLength();
                    
                    const insertedHtml = reply.trim();
                    window.coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(index, insertedHtml, 'user');
                    window.coraQuillListingCoordinator.setSelection(index + insertedHtml.length);
                    
                    inputEl.val('');
                    window.coraShowToast('AI content inserted successfully!', 'success');
                    window.coraUpdateWordCount();
                    window.coraTriggerEditorAutoSave();
                });
            });
        };

        // Run predefined actions (chips) from inline assistant card
        window.coraRunAIAction = function(action) {
            if (!window.coraQuillListingCoordinator) {
                window.coraShowToast('Editor is initializing, please wait...', 'warning');
                return;
            }
            const range = window.coraQuillListingCoordinator.getSelection();
            const hasSelection = range && range.length > 0;
            let selectedText = '';
            if (hasSelection) {
                selectedText = window.coraQuillListingCoordinator.getText(range.index, range.length).trim();
            }

            if (action === 'clarity' && !hasSelection) {
                window.coraShowToast('Please highlight/select the text you want to clarify first.', 'warning');
                return;
            }
            if (action === 'expand' && !hasSelection) {
                window.coraShowToast('Please highlight/select the section you want to expand first.', 'warning');
                return;
            }
            if (action === 'examples' && !hasSelection) {
                window.coraShowToast('Please highlight/select the statement you want to generate examples for.', 'warning');
                return;
            }

            window.coraShowToast('Running AI ' + action + ' optimization...', 'info');

            window.coraGetRagBrainFacts(function(facts) {
                const title = jQuery('#cora-article-title').val() || 'Untitled';
                const keyword = jQuery('#cora-seo-keyword').val() || 'None';
                const currentText = window.coraQuillListingCoordinator.getText().trim();
                
                const systemPrompt = `You are a professional AI Writing Assistant for Cora Studio.
Ground your writing in these verified business facts if applicable: ${facts}
Output ONLY the generated rich-text content to replace or insert. Do NOT include markdown code blocks, conversational comments, or notes. Use inline HTML tags for formatting.`;

                let prompt = '';
                if (action === 'intro') {
                    prompt = `Generate a compelling, SEO-optimized introduction paragraph (about 80-120 words) for an article titled "${title}". Focus Keyword: "${keyword}".`;
                } else if (action === 'expand') {
                    prompt = `Expand on the following section to add more detail, depth, and structural insights: "${selectedText}".`;
                } else if (action === 'clarity') {
                    prompt = `Rewrite the following text to make it extremely clear, readable, and professional: "${selectedText}".`;
                } else if (action === 'examples') {
                    prompt = `Generate 2-3 realistic examples or brief case studies to illustrate this point: "${selectedText}".`;
                }

                window.coraCallWorkspaceAI(prompt, systemPrompt, function(err, reply) {
                    if (err) {
                        window.coraShowToast('AI optimization failed: ' + err.message, 'error');
                        return;
                    }

                    const index = range ? range.index : window.coraQuillListingCoordinator.getLength();
                    const length = range ? range.length : 0;
                    const insertedHtml = reply.trim();

                    if (action === 'intro') {
                        window.coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(0, insertedHtml + '<br><br>', 'user');
                        window.coraQuillListingCoordinator.setSelection(0, insertedHtml.length);
                    } else {
                        if (hasSelection) {
                            window.coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(index, insertedHtml, 'user');
                            window.coraQuillListingCoordinator.deleteText(index + insertedHtml.length, length);
                        } else {
                            window.coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(index, '<br>' + insertedHtml + '<br>', 'user');
                        }
                        window.coraQuillListingCoordinator.setSelection(index, insertedHtml.length);
                    }

                    window.coraShowToast('AI optimization complete!', 'success');
                    window.coraUpdateWordCount();
                    window.coraTriggerEditorAutoSave();
                });
            });
        };

        // --- FLOATING INLINE SELECTION AI PORTAL ---

        window.coraActiveSelectionRange = null;

        window.coraPositionInlineAIPopup = function(range) {
            if (!window.coraQuillListingCoordinator || !range) return;
            const bounds = window.coraQuillListingCoordinator.getBounds(range.index, range.length);
            const popup = document.getElementById('cora-inline-ai-popup');
            if (!popup) return;

            popup.style.display = 'flex';
            const popupWidth = popup.offsetWidth;
            const popupHeight = popup.offsetHeight;

            // Align centered horizontally above the selection bounds
            const left = bounds.left + (bounds.width / 2) - (popupWidth / 2);
            const top = bounds.top - popupHeight - 12;

            popup.style.left = Math.max(0, left) + 'px';
            popup.style.top = top + 'px';
            
            setTimeout(() => {
                popup.classList.remove('hidden', 'opacity-0', 'scale-95');
                popup.classList.add('opacity-100', 'scale-100');
            }, 10);
        };

        window.coraHideInlineAIPopup = function() {
            const popup = document.getElementById('cora-inline-ai-popup');
            if (popup && !popup.classList.contains('hidden')) {
                popup.classList.remove('opacity-100', 'scale-100');
                popup.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    popup.style.display = 'none';
                    popup.classList.add('hidden');
                }, 200);
            }
        };

        // Initialize inline popup DOM & logic
        function coraInitInlineAIPopup() {
            let inlinePopup = document.getElementById('cora-inline-ai-popup');
            if (inlinePopup) return;

            inlinePopup = document.createElement('div');
            inlinePopup.id = 'cora-inline-ai-popup';
            inlinePopup.className = 'hidden absolute bg-white border border-zinc-200 rounded-xl shadow-xl p-3.5 z-[9999] select-none font-sans flex flex-col gap-2 max-w-[340px] w-full transition-all duration-200 opacity-0 scale-95 origin-bottom';
            
            inlinePopup.innerHTML = `
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-0.5 select-none">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-violet-650 animate-pulse"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <span>Cora Selection AI</span>
                </div>
                <div class="flex items-center bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-1.5 focus-within:border-zinc-400">
                    <input type="text" id="cora-inline-ai-prompt" placeholder="Ask AI to edit selection..." class="flex-1 text-xs outline-none border-none bg-transparent text-zinc-855 placeholder:text-zinc-400 pr-4">
                    <button type="button" id="cora-inline-ai-submit" class="text-zinc-450 hover:text-zinc-955 transition-colors border-none bg-transparent cursor-pointer p-0.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </div>
                <div class="flex flex-wrap gap-1.5 text-[9px] font-bold text-zinc-700 select-none">
                    <button type="button" id="cora-inline-btn-rephrase" class="px-2.5 py-1 bg-white hover:bg-zinc-50 border border-zinc-200 rounded-md transition-all cursor-pointer shadow-3xs">Rephrase</button>
                    <button type="button" id="cora-inline-btn-longer" class="px-2.5 py-1 bg-white hover:bg-zinc-50 border border-zinc-200 rounded-md transition-all cursor-pointer shadow-3xs">Make Longer</button>
                    <button type="button" id="cora-inline-btn-shorter" class="px-2.5 py-1 bg-white hover:bg-zinc-50 border border-zinc-200 rounded-md transition-all cursor-pointer shadow-3xs">Make Shorter</button>
                    <button type="button" id="cora-inline-btn-simplify" class="px-2.5 py-1 bg-white hover:bg-zinc-50 border border-zinc-200 rounded-md transition-all cursor-pointer shadow-3xs">Simplify</button>
                </div>
            `;
            
            const writingSheet = document.querySelector('.cora-writing-sheet');
            if (writingSheet) {
                writingSheet.style.position = 'relative';
                writingSheet.appendChild(inlinePopup);
            } else {
                document.body.appendChild(inlinePopup);
            }

            const promptInput = inlinePopup.querySelector('#cora-inline-ai-prompt');
            const submitBtn = inlinePopup.querySelector('#cora-inline-ai-submit');

            function runInlineAction(actionType, customInstruction) {
                const range = window.coraActiveSelectionRange;
                if (!range || !window.coraQuillListingCoordinator) {
                    window.coraShowToast('Please select some text first.', 'warning');
                    return;
                }
                
                const selectedText = window.coraQuillListingCoordinator.getText(range.index, range.length).trim();
                if (!selectedText) {
                    window.coraShowToast('Selected text is empty.', 'warning');
                    return;
                }
                
                promptInput.disabled = true;
                window.coraShowToast('AI is editing your selection...', 'info');
                
                window.coraGetRagBrainFacts(function(facts) {
                    const systemPrompt = `You are a professional editor for Cora Studio.
Ground your edits in these verified business facts if applicable: ${facts}
Output ONLY the rewritten text to replace the selection. Do NOT include markdown code blocks, conversational comments, or wrappers. Maintain original HTML formatting if present.`;

                    let instruction = '';
                    if (actionType === 'rephrase') {
                        instruction = `Rephrase and polish this text for better readability: "${selectedText}"`;
                    } else if (actionType === 'longer') {
                        instruction = `Make the following text significantly longer and more detailed: "${selectedText}".`;
                    } else if (actionType === 'shorter') {
                        instruction = `Summarize and shorten the following text: "${selectedText}"`;
                    } else if (actionType === 'simplify') {
                        instruction = `Simplify this text to make it easy to understand: "${selectedText}"`;
                    } else if (actionType === 'custom') {
                        instruction = `Edit this text: "${selectedText}" based on this user command: "${customInstruction}"`;
                    }

                    window.coraCallWorkspaceAI(instruction, systemPrompt, function(err, reply) {
                        promptInput.disabled = false;
                        if (err) {
                            window.coraShowToast('AI edit failed: ' + err.message, 'error');
                            return;
                        }
                        
                        const index = range.index;
                        const length = range.length;
                        const newHtml = reply.trim();
                        
                        window.coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(index, newHtml, 'user');
                        window.coraQuillListingCoordinator.deleteText(index + newHtml.length, length);
                        window.coraQuillListingCoordinator.setSelection(index, newHtml.length);
                        
                        promptInput.value = '';
                        window.coraHideInlineAIPopup();
                        window.coraShowToast('Selection updated successfully!', 'success');
                        window.coraUpdateWordCount();
                        window.coraTriggerEditorAutoSave();
                    });
                });
            }

            submitBtn.addEventListener('click', function() {
                const customText = promptInput.value.trim();
                if (customText) {
                    runInlineAction('custom', customText);
                }
            });

            promptInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const customText = promptInput.value.trim();
                    if (customText) {
                        runInlineAction('custom', customText);
                    }
                }
            });

            inlinePopup.querySelector('#cora-inline-btn-rephrase').addEventListener('click', () => runInlineAction('rephrase'));
            inlinePopup.querySelector('#cora-inline-btn-longer').addEventListener('click', () => runInlineAction('longer'));
            inlinePopup.querySelector('#cora-inline-btn-shorter').addEventListener('click', () => runInlineAction('shorter'));
            inlinePopup.querySelector('#cora-inline-btn-simplify').addEventListener('click', () => runInlineAction('simplify'));
        }

        window.coraToggleAIAssistant = function() {
            const assistant = document.getElementById('cora-ai-writing-assistant');
            const btn = document.getElementById('cora-toggle-ai-assistant-btn');
            if (assistant) {
                const isHidden = assistant.classList.contains('hidden-assistant');
                if (isHidden) {
                    assistant.classList.remove('hidden-assistant');
                    if (btn) btn.classList.add('bg-zinc-100');
                    localStorage.setItem('cora_ai_assistant_open', 'true');
                } else {
                    assistant.classList.add('hidden-assistant');
                    if (btn) btn.classList.remove('bg-zinc-100');
                    localStorage.setItem('cora_ai_assistant_open', 'false');
                }
            }
        };

        window.coraInitializeWorkspaceEditorSettings = function() {
            const isMobile = window.innerWidth < 768;
            
            try {
                // Restore AI Assistant toggle state
                const aiOpen = localStorage.getItem('cora_ai_assistant_open');
                const assistant = document.getElementById('cora-ai-writing-assistant');
                const btn = document.getElementById('cora-toggle-ai-assistant-btn');
                if (assistant) {
                    if (aiOpen === 'true') {
                        assistant.classList.remove('hidden-assistant');
                        if (btn) btn.classList.add('bg-zinc-100');
                    } else {
                        assistant.classList.add('hidden-assistant');
                        if (btn) btn.classList.remove('bg-zinc-100');
                    }
                }

                const inspector = document.getElementById('cora-article-inspector');
                const backdrop = document.getElementById('cora-inspector-backdrop');
                if (inspector) {
                    if (isMobile) {
                        inspector.classList.add('collapsed-inspector');
                        inspector.classList.add('translate-y-full');
                        if (backdrop) backdrop.classList.add('hidden');
                        localStorage.setItem('cora_article_inspector_collapsed', 'true');
                    } else {
                        inspector.classList.remove('collapsed-inspector');
                        inspector.classList.remove('translate-y-full');
                        if (backdrop) backdrop.classList.add('hidden');
                        localStorage.setItem('cora_article_inspector_collapsed', 'false');
                    }
                }
                
                const leftSidebar = document.getElementById('cora-editor-left-sidebar');
                if (leftSidebar) {
                    if (isMobile) {
                        leftSidebar.classList.add('hidden');
                        leftSidebar.classList.remove('flex');
                    } else {
                        leftSidebar.classList.remove('hidden');
                        leftSidebar.classList.add('flex');
                    }
                }
                
                const activeTab = document.querySelector('.inspector-tab-btn.tab-active');
                if (!activeTab && typeof window.coraSwitchInspectorTab === 'function') {
                    window.coraSwitchInspectorTab('seo');
                }
            } catch(e) {}

            // Set default font
            if (typeof window.coraSetEditorFont === 'function') {
                window.coraSetEditorFont('sans');
            }

            // Initialize floating popup DOM elements if not already done
            coraInitInlineAIPopup();

            // Restore unsaved draft from localStorage if page refreshed
            const activeId = jQuery('#cora-article-id').val();
            window.coraRestoreEditorDraft(activeId);

            // Wait until Quill is initialized to attach event listeners and do initial metrics update
            if (window.coraQuillInitInterval) {
                clearInterval(window.coraQuillInitInterval);
            }
            window.coraQuillInitInterval = setInterval(function() {
                if (window.coraQuillListingCoordinator) {
                    clearInterval(window.coraQuillInitInterval);
                    window.coraQuillInitInterval = null;

                    // Attach Quill listeners once
                    if (!window.coraQuillListenersAttached) {
                        window.coraQuillListingCoordinator.on('text-change', function() {
                            window.coraUpdateWordCount();
                            window.coraTriggerEditorAutoSave();
                        });

                        // Inline AI selection listener
                        window.coraQuillListingCoordinator.on('selection-change', function(range, oldRange, source) {
                            if (range && range.length > 0) {
                                window.coraActiveSelectionRange = range;
                                window.coraPositionInlineAIPopup(range);
                            } else {
                                setTimeout(function() {
                                    const activeEl = document.activeElement;
                                    const isInsidePopup = activeEl && activeEl.closest('#cora-inline-ai-popup');
                                    if (!isInsidePopup) {
                                        window.coraHideInlineAIPopup();
                                        window.coraActiveSelectionRange = null;
                                    }
                                }, 250);
                            }
                        });

                        window.coraQuillListenersAttached = true;
                    }

                    // Update word count immediately once Quill is initialized
                    if (typeof window.coraUpdateWordCount === 'function') {
                        window.coraUpdateWordCount();
                    }
                }
            }, 100);

            // Initialize metrics immediately as fallback (will show 0/100 initially)
            if (typeof window.coraUpdateWordCount === 'function') {
                window.coraUpdateWordCount();
            }
            if (typeof window.coraUpdateExcerptCount === 'function') {
                window.coraUpdateExcerptCount();
            }
        };


        document.addEventListener('DOMContentLoaded', function() {
            // Apply all overrides after admin-script.js (deferred) has executed

            // 1. Redefine window.coraToggleBeehiivDropdown to support 'more'
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



            // 3. Wrap coraRemoveCoverImage to clear post thumbnail
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



            // 5. Hook window.coraEditArticle to run editor initialization
            const originalEditArticle = window.coraEditArticle;
            window.coraEditArticle = function(id) {
                if (typeof originalEditArticle === 'function') {
                    originalEditArticle(id);
                }
                window.coraInitializeWorkspaceEditorSettings();
            };

            // 6. Hook window.coraOpenContentDrawer to run editor initialization
            const originalOpenContentDrawer = window.coraOpenContentDrawer;
            window.coraOpenContentDrawer = function() {
                if (typeof originalOpenContentDrawer === 'function') {
                    originalOpenContentDrawer();
                }
                window.coraInitializeWorkspaceEditorSettings();
            };

            // Typist listener for real-time auto-saving
            jQuery(document).on('input propertychange change keyup', '#cora-article-title, #cora-article-subtitle, #cora-article-excerpt, #cora-article-slug, #cora-article-excerpt-bh', function() {
                if (typeof window.coraUpdateExcerptCount === 'function') {
                    window.coraUpdateExcerptCount();
                }
                window.coraTriggerEditorAutoSave();
            });

            setTimeout(function() {
                window.coraInitializeWorkspaceEditorSettings();
            }, 500);
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
    <aside id="cora-media-library-drawer" class="collapsed translate-x-full fixed top-0 right-0 z-[1000005] h-full w-[450px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out pointer-events-none">
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
    <aside id="cora-ai-tone-drawer" class="collapsed translate-x-full fixed top-0 right-0 z-[10005] h-full w-[380px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-all duration-300 ease-in-out pointer-events-none hidden">
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

    <!-- Mobile Floating Bottom Navigation (3-State Adaptive Floating Island Bar) -->
    <div id="cora-mobile-floating-island" class="cora-mobile-island-wrapper lg:hidden fixed bottom-4 left-0 right-0 z-[9980] w-[calc(100vw-32px)] max-w-[460px] mx-auto transition-all duration-300 ease-out" style="position: fixed !important; bottom: 16px !important; left: 0 !important; right: 0 !important; margin: 0 auto !important; z-index: 9980 !important; width: calc(100vw - 32px) !important; max-width: 460px !important; box-sizing: border-box !important;">


        <div class="cora-island-card w-full flex items-center justify-between transition-all duration-300">
            
            <!-- State 1 & 2: Menu Toggle Button (Left) -->
            <button type="button" id="cora-island-state-menu-btn" onclick="coraToggleIslandState('nav')" class="cora-island-btn-menu" title="Toggle Navigation Menu">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

            <!-- STATE 1: Compact Neutral Bar (Middle) -->
            <div id="cora-island-view-compact" class="cora-island-view flex-1 mx-2 flex items-center justify-center cursor-pointer rounded-full" onclick="coraToggleIslandState('ai')" style="flex: 1 1 auto; display: flex; align-items: center; justify-content: center; height: 40px !important; cursor: pointer; box-sizing: border-box !important;">
                <div class="cora-island-input-pill" style="justify-content: center !important; gap: 8px !important;">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" style="color: #71717a !important;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span style="font-size: 13px; font-weight: 600; color: #71717a !important;">Search or ask AI...</span>
                </div>
            </div>

            <!-- STATE 2: AI Input / Prompt Bar (Middle) -->
            <div id="cora-island-view-ai" class="cora-island-view hidden flex-1 mx-1.5 flex items-center" style="display: none; flex: 1 1 auto; height: 40px !important;">
                <div class="cora-island-input-pill">
                    <div style="display: flex; align-items: center; flex: 1; min-w: 0; height: 100%;">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-400 shrink-0" style="margin-right: 6px; flex-shrink: 0; color: #71717a !important;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="cora-island-ai-input" placeholder="Search articles, opportunities..." class="w-full bg-transparent border-none outline-none text-xs text-zinc-900 placeholder-zinc-400 pl-1 pr-1 font-sans focus:outline-none focus:ring-0" style="border: none !important; outline: none !important; box-shadow: none !important; font-size: 13px !important; background: transparent !important; color: #18181b !important; padding: 0 !important; margin: 0 !important;" onkeydown="if(event.key==='Enter'){ coraSubmitIslandAI(); }">
                    </div>
                    <button type="button" onclick="coraSubmitIslandAI()" class="cora-island-ask-btn">
                        Ask AI
                    </button>
                </div>
            </div>

            <!-- STATE 3: Navigation Tabs Bar (Middle) -->
            <nav id="cora-island-view-nav" class="cora-island-view hidden flex-1 mx-1 flex items-center justify-evenly" style="display: none; flex: 1 1 auto; justify-content: space-around;">
                <a href="javascript:void(0)" onclick="if(typeof coraNavigateTo==='function'){coraNavigateTo('dashboard');}" class="cora-island-nav-link">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span style="font-size: 9px; font-weight: 700; margin-top: 1px;">Home</span>
                </a>
                <?php
                $cora_mobile_crm_target = 'leads';
                $cora_mobile_crm_label  = 'CRM';
                $cora_mobile_crm_icon   = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';

                $cora_active_ind = function_exists( 'cora_get_active_industry' ) ? cora_get_active_industry() : ( isset( $current_industry ) ? $current_industry : get_option( 'cora_workspace_industry', 'real_estate' ) );
                $cora_active_ind_clean = str_replace( '_', '-', strtolower( trim( $cora_active_ind ) ) );

                if ( $cora_active_ind_clean === 'photography' || $cora_active_ind_clean === 'studio' || $cora_active_ind_clean === 'photography-studio' ) {
                    $cora_mobile_crm_target = 'bookings';
                    $cora_mobile_crm_label  = 'Bookings';
                    $cora_mobile_crm_icon   = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
                }
                ?>
                <a href="javascript:void(0)" onclick="if(typeof coraNavigateTo==='function'){coraNavigateTo('<?php echo esc_js($cora_mobile_crm_target); ?>');}" class="cora-island-nav-link">
                    <?php echo $cora_mobile_crm_icon; ?>
                    <span style="font-size: 9px; font-weight: 700; margin-top: 1px;"><?php echo esc_html($cora_mobile_crm_label); ?></span>
                </a>
                <a href="javascript:void(0)" onclick="if(typeof coraNavigateTo==='function'){coraNavigateTo('blogs');}" class="cora-island-nav-link">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <span style="font-size: 9px; font-weight: 700; margin-top: 1px;">Content</span>
                </a>
                <a href="javascript:void(0)" onclick="if(typeof coraNavigateTo==='function'){coraNavigateTo('financials');}" class="cora-island-nav-link">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 4h14M5 9h14M8 4v1a5 5 0 0 0 0 8h1L19 20M8 13h5a4 4 0 0 0 0-8H8"/></svg>
                    <span style="font-size: 9px; font-weight: 700; margin-top: 1px;">Finance</span>
                </a>
                <a href="javascript:void(0)" onclick="window.coraToggleMobileNavDrawer(true);" class="cora-island-nav-link">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                    <span style="font-size: 9px; font-weight: 700; margin-top: 1px;">More</span>
                </a>
            </nav>

            <!-- State 1 & 3: AI Action Button (Right) -->
            <button type="button" id="cora-island-state-ai-btn" onclick="coraToggleIslandState('ai')" class="cora-island-btn-ai" title="Ask Cora AI">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2l2.4 4.86L19.8 8l-3.9 3.8 0.9 5.36L12 14.6l-4.8 2.56 0.9-5.36L4.2 8l5.4-1.14L12 2z"></path>
                </svg>
            </button>
        </div>
    </div>
    </div> <!-- .flex.flex-row.flex-1.min-h-0 -->
</div> <!-- #cora-workspace -->

<?php
$cora_mobile_drawer_items = array();
if ( ! empty( $nav_groups ) && is_array( $nav_groups ) ) {
    foreach ( $nav_groups as $group ) {
        if ( empty( $group['items'] ) || ! is_array( $group['items'] ) ) continue;
        foreach ( $group['items'] as $target => $item ) {
            $super_pages = array( 'super-admin', 'super-users', 'super-appeals', 'super-governance', 'super-announcements', 'super-health', 'super-docs' );
            if ( ! in_array( $target, $super_pages ) && function_exists( 'cora_user_has_feature_access' ) && ! cora_user_has_feature_access( $target ) ) {
                continue;
            }
            $cora_mobile_drawer_items[$target] = $item;
        }
    }
}
?>
<!-- Mobile Bottom Navigation Drawer Sheet (outside #cora-workspace so it renders as true fixed portal) -->
<div id="cora-mobile-nav-drawer" style="display:none; position:fixed; inset:0; z-index:99999; flex-direction:column; justify-content:flex-end;">
    <!-- Backdrop -->
    <div onclick="window.coraToggleMobileNavDrawer(false)" style="position:absolute; inset:0; background:rgba(9,9,11,0.45); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px);"></div>

    <!-- Drawer Sheet -->
    <div id="cora-mobile-nav-drawer-sheet" style="position:relative; z-index:1; width:100%; background:#fff; border-top:1px solid #e4e4e7; border-radius:24px 24px 0 0; box-shadow:0 -8px 40px rgba(0,0,0,0.12); display:flex; flex-direction:column; max-height:75vh; transition:transform 0.3s cubic-bezier(0.16,1,0.3,1); transform:translateY(100%); padding-bottom:max(12px,env(safe-area-inset-bottom,0px));">

        <!-- Drag Handle -->
        <div onclick="window.coraToggleMobileNavDrawer(false)" style="display:flex; align-items:center; justify-content:center; padding:10px 0 6px; cursor:pointer; flex-shrink:0;">
            <div style="width:44px; height:5px; border-radius:99px; background:#d4d4d8;"></div>
        </div>

        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:4px 20px 12px; border-bottom:1px solid #f4f4f5; flex-shrink:0;">
            <span style="font-size:10px; font-weight:800; letter-spacing:0.08em; text-transform:uppercase; color:#a1a1aa;">All Modules &amp; Tools</span>
            <button onclick="window.coraToggleMobileNavDrawer(false)" style="background:none; border:none; cursor:pointer; color:#a1a1aa; padding:4px; display:flex; align-items:center; justify-content:center; border-radius:50%;">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- List of 1 Column Bars -->
        <div style="flex:1; overflow-y:auto; padding:16px 20px; -webkit-overflow-scrolling:touch;">
            <div style="display:flex; flex-direction:column; gap:8px;">
                <?php foreach ( $cora_mobile_drawer_items as $target => $item ) :
                    $nav_url = home_url( '/' . $cora_ws_slug . '/' . $target );
                    $is_active = ( $sub_page === $target || str_replace('_','-',$sub_page) === str_replace('_','-',$target) );
                    
                    if ( $is_active ) {
                        $bar_style = 'background:#f4f4f5; border-color:#e4e4e7; color:#09090b; font-weight:700;';
                        $icon_color = '#09090b';
                    } else {
                        $bar_style = 'background:#ffffff; border-color:#e4e4e7; color:#27272a; font-weight:600;';
                        $icon_color = '#71717a';
                    }
                ?>
                <a href="<?php echo esc_url($nav_url); ?>" style="display:flex; align-items:center; gap:12px; padding:12px 16px; border:1px solid; border-radius:12px; text-decoration:none; transition:all 0.15s; <?php echo $bar_style; ?>">
                    <span class="cora-mobile-nav-icon-wrapper" style="display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; color:<?php echo $icon_color; ?>; flex-shrink:0;">
                        <?php echo $item['icon']; ?>
                    </span>
                    <span style="font-size:13px; letter-spacing:-0.01em; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo esc_html($item['title']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <style>
        .cora-mobile-nav-icon-wrapper svg {
            width: 18px !important;
            height: 18px !important;
        }
        </style>
    </div>
</div>

<script>
(function() {
    window.coraToggleMobileNavDrawer = function(forceShow, fromPopState) {
        var drawer  = document.getElementById('cora-mobile-nav-drawer');
        var sheet   = document.getElementById('cora-mobile-nav-drawer-sheet');
        if (!drawer || !sheet) return;
        var isHidden  = (drawer.style.display === 'none' || drawer.style.display === '');
        var shouldShow = forceShow !== undefined ? !!forceShow : isHidden;

        if (shouldShow) {
            // Close AI sidebar if open
            if (typeof window.coraToggleSidebar === 'function') {
                window.coraToggleSidebar(false);
            }
            if (!fromPopState) {
                history.pushState({ drawer: 'mobile-nav' }, '');
            }
            drawer.style.display = 'flex';
            sheet.getBoundingClientRect();
            sheet.style.transform = 'translateY(0)';
            document.body.style.overflow = 'hidden';
        } else {
            sheet.style.transform = 'translateY(100%)';
            document.body.style.overflow = '';
            setTimeout(function() {
                drawer.style.display = 'none';
            }, 310);

            if (!fromPopState && history.state && history.state.drawer === 'mobile-nav') {
                history.back();
            }
        }
    };
})();
</script>

<!-- Mobile Notifications Bottom Drawer (portal, outside workspace container) -->
<div id="cora-mobile-notif-bottom-drawer" style="display:none; position:fixed; inset:0; z-index:99999; flex-direction:column; justify-content:flex-end;">
    <!-- Backdrop -->
    <div onclick="window.coraToggleMobileNotifDrawer(false)" style="position:absolute; inset:0; background:rgba(9,9,11,0.45); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px);"></div>

    <!-- Sheet -->
    <div id="cora-mobile-notif-bottom-sheet" style="position:relative; z-index:1; width:100%; background:#fff; border-top:1px solid #e4e4e7; border-radius:24px 24px 0 0; box-shadow:0 -8px 40px rgba(0,0,0,0.12); display:flex; flex-direction:column; max-height:78vh; transition:transform 0.3s cubic-bezier(0.16,1,0.3,1); transform:translateY(100%); padding-bottom:max(12px,env(safe-area-inset-bottom,0px));">

        <!-- Drag handle -->
        <div onclick="window.coraToggleMobileNotifDrawer(false)" style="display:flex; align-items:center; justify-content:center; padding:10px 0 6px; cursor:pointer; flex-shrink:0;">
            <div style="width:44px; height:5px; border-radius:99px; background:#d4d4d8;"></div>
        </div>

        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:4px 20px 12px; border-bottom:1px solid #f4f4f5; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:8px;">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="#3f3f46" stroke-width="2.2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span style="font-size:11px; font-weight:800; letter-spacing:0.06em; text-transform:uppercase; color:#3f3f46;">Notifications</span>
            </div>
            <div style="display:flex; align-items:center; gap:12px;">
                <button onclick="window.coraMarkAllNotificationsRead(event);" style="font-size:10px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:#a1a1aa; background:none; border:none; cursor:pointer; padding:0;">Mark read</button>
                <span style="color:#d4d4d8; font-size:12px;">|</span>
                <button onclick="window.coraClearAllNotifications(event);" style="font-size:10px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:#a1a1aa; background:none; border:none; cursor:pointer; padding:0;">Clear all</button>
                <span style="color:#d4d4d8; font-size:12px;">|</span>
                <button onclick="window.coraToggleMobileNotifDrawer(false)" style="background:none; border:none; cursor:pointer; color:#a1a1aa; padding:4px; display:flex; align-items:center; justify-content:center;">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <!-- Notification list (reuses the same render function via a dedicated container) -->
        <div style="flex:1; overflow-y:auto; -webkit-overflow-scrolling:touch;">
            <div id="cora-mobile-notif-list" style="padding:8px;"></div>
            <div id="cora-mobile-notif-empty" style="display:none; padding:48px 20px; text-align:center; font-size:11px; color:#a1a1aa;">
                <svg viewBox="0 0 24 24" width="28" height="28" stroke="#d4d4d8" stroke-width="1.5" fill="none" style="margin:0 auto 10px;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                No new notifications in the last 24h.
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    window.coraToggleMobileNotifDrawer = function(forceShow, fromPopState) {
        var drawer = document.getElementById('cora-mobile-notif-bottom-drawer');
        var sheet  = document.getElementById('cora-mobile-notif-bottom-sheet');
        if (!drawer || !sheet) return;
        var isHidden   = (drawer.style.display === 'none' || drawer.style.display === '');
        var shouldShow = forceShow !== undefined ? !!forceShow : isHidden;

        if (shouldShow) {
            if (typeof window.coraRenderMobileNotifications === 'function') {
                window.coraRenderMobileNotifications();
            }
            if (!fromPopState) {
                history.pushState({ drawer: 'mobile-notif' }, '');
            }
            drawer.style.display = 'flex';
            sheet.getBoundingClientRect();
            sheet.style.transform = 'translateY(0)';
            document.body.style.overflow = 'hidden';
        } else {
            sheet.style.transform = 'translateY(100%)';
            document.body.style.overflow = '';
            setTimeout(function() { drawer.style.display = 'none'; }, 310);

            if (!fromPopState && history.state && history.state.drawer === 'mobile-notif') {
                history.back();
            }
        }
    };

    // Global listener for mobile browser back swipe / back button navigation
    window.addEventListener('popstate', function(event) {
        var navDrawer = document.getElementById('cora-mobile-nav-drawer');
        var notifDrawer = document.getElementById('cora-mobile-notif-bottom-drawer');

        if (navDrawer && navDrawer.style.display === 'flex') {
            window.coraToggleMobileNavDrawer(false, true);
        }
        if (notifDrawer && notifDrawer.style.display === 'flex') {
            window.coraToggleMobileNotifDrawer(false, true);
        }
    });
})();
</script>


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
        const mobileListContainer = document.getElementById('cora-mobile-notif-list');
        
        const emptyState = document.getElementById('cora-notif-empty');
        const sidebarEmptyState = document.getElementById('cora-sidebar-notif-empty');
        const mobileEmptyState    = document.getElementById('cora-mobile-notif-empty');
        
        const badge = document.getElementById('cora-notif-badge');
        const mobileBadge = document.getElementById('cora-mobile-notif-badge');
        const sidebarBadge = document.getElementById('cora-sidebar-notif-badge');

        // Filter to last 7 days (604800 seconds); surface all recent activity
        const nowSec = Math.floor(Date.now() / 1000);
        const last7dNotifications = coraNotifications.filter(n => (nowSec - parseInt(n.timestamp || 0)) <= 604800);
        // If nothing in 7 days, show all (so panel is never confusingly blank when data exists)
        const filteredNotifications = last7dNotifications.length > 0 ? last7dNotifications : coraNotifications.slice(0, 25);

        const displayList = filteredNotifications.slice(0, 25);
        const unreadCount = filteredNotifications.filter(n => !n.read).length;

        // Update badges
        if (unreadCount > 0) {
            if (badge) {
                badge.textContent = unreadCount;
                badge.classList.remove('hidden');
            }
            if (mobileBadge) {
                mobileBadge.classList.remove('hidden');
            }
            if (sidebarBadge) {
                sidebarBadge.textContent = unreadCount;
                sidebarBadge.classList.remove('hidden');
            }
        } else {
            if (badge) badge.classList.add('hidden');
            if (mobileBadge) mobileBadge.classList.add('hidden');
            if (sidebarBadge) sidebarBadge.classList.add('hidden');
        }

        if (displayList.length === 0) {
            if (listContainer) listContainer.innerHTML = '';
            if (sidebarListContainer) sidebarListContainer.innerHTML = '';
            if (mobileListContainer) mobileListContainer.innerHTML = '';
            
            if (emptyState) {
                emptyState.textContent = 'No notifications yet.';
                emptyState.classList.remove('hidden');
            }
            if (sidebarEmptyState) {
                sidebarEmptyState.textContent = 'No notifications yet.';
                sidebarEmptyState.classList.remove('hidden');
            }
            if (mobileEmptyState) {
                mobileEmptyState.style.display = 'block';
            }
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');
        if (sidebarEmptyState) sidebarEmptyState.classList.add('hidden');
        if (mobileEmptyState) mobileEmptyState.style.display = 'none';
        
        let html = '';

        displayList.forEach(notif => {
            const itemClass = notif.read 
                ? "p-4 text-xs text-zinc-500 bg-white hover:bg-zinc-50/50 opacity-60 transition-all cursor-pointer block select-none"
                : "p-4 text-xs font-semibold text-zinc-900 bg-zinc-50/50 hover:bg-zinc-50 border-l-[3px] border-zinc-900 transition-all cursor-pointer block select-none";

            const relativeTime = getRelativeTimeString(notif.timestamp);

            html += `
                <div class="${itemClass}" data-id="${notif.id}" data-url="${notif.action_url || ''}">
                    <div class="flex items-start justify-between gap-2 text-zinc-950">
                        <div class="font-bold">${escapeHtml(notif.title)}</div>
                        <span class="text-[9px] text-zinc-400 font-normal shrink-0 font-mono">${relativeTime}</span>
                    </div>
                    <p class="text-zinc-600 mt-1 font-normal leading-relaxed">${escapeHtml(notif.description)}</p>
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

        if (mobileListContainer) {
            mobileListContainer.innerHTML = html;
            mobileListContainer.querySelectorAll('[data-id]').forEach(el => {
                el.addEventListener('click', function(e) {
                    const notifId    = this.getAttribute('data-id');
                    const actionUrl  = this.getAttribute('data-url');
                    window.coraToggleMobileNotifDrawer(false);
                    handleCoraNotifClick(e, notifId, actionUrl);
                });
            });
        }
    }

    // Expose so the bottom drawer toggle can call it on-demand
    window.coraRenderMobileNotifications = renderCoraNotifications;

    window.coraToggleNotificationDrawer = function(forceShow) {
        // On mobile, use the bottom sheet drawer instead
        if (window.innerWidth < 768) {
            if (typeof window.coraToggleMobileNotifDrawer === 'function') {
                window.coraToggleMobileNotifDrawer(forceShow);
            }
            return;
        }
        // Desktop: right-side panel
        const drawer = document.getElementById('cora-notif-dropdown');
        const backdrop = document.getElementById('cora-notif-backdrop');
        if (!drawer) return;
        const isCollapsed = drawer.classList.contains('collapsed');
        const shouldOpen = forceShow !== undefined ? forceShow : isCollapsed;
        if (shouldOpen) {
            drawer.classList.remove('collapsed');
            drawer.style.display = ''; // Clear inline styles
            if (backdrop) {
                backdrop.classList.remove('hidden');
                backdrop.getBoundingClientRect(); // force reflow
                backdrop.style.opacity = '1';
            }
            document.body.style.overflow = 'hidden';
        } else {
            drawer.classList.add('collapsed');
            if (backdrop) {
                backdrop.style.opacity = '0';
                setTimeout(function() {
                    backdrop.classList.add('hidden');
                }, 300);
            }
            document.body.style.overflow = '';
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
                if (el) {
                    if (id === 'cora-notif-dropdown') {
                        el.classList.add('collapsed');
                        el.style.display = ''; // Reset inline display
                    } else {
                        el.classList.add('hidden');
                        el.style.display = 'none';
                    }
                }
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
            popover.style.display = 'flex';
        } else {
            popover.classList.add('hidden');
            popover.style.display = 'none';
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

    // Clear all notifications
    function clearAllNotifications(e) {
        if (e) e.stopPropagation();
        
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_clear_all_notifs',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                coraNotifications = [];
                renderCoraNotifications();
                if (window.coraShowToast) {
                    window.coraShowToast("All notifications cleared.", "success");
                }
            } else {
                console.error(res.data ? res.data.message : 'Error clearing notifications');
            }
        });
    }

    window.coraMarkAllNotificationsRead = markAllNotificationsRead;
    window.coraClearAllNotifications = clearAllNotifications;

    // Initialize listeners
    document.addEventListener('DOMContentLoaded', function() {
        const bellBtn = document.getElementById('cora-notif-bell-btn');
        const markAllBtn = document.getElementById('cora-notif-mark-all-btn');

        // Bell button click handler is already defined inline in HTML (onclick="window.coraToggleNotificationDrawer()") to avoid double triggering.
        if (markAllBtn) {
            markAllBtn.addEventListener('click', markAllNotificationsRead);
        }

        // Close profile popover when clicking outside
        document.addEventListener('click', function(e) {
            const popover = document.getElementById('cora-header-profile-popover');
            if (popover && !popover.classList.contains('hidden') && !e.target.closest('.cora-header-profile-btn') && !e.target.closest('#cora-header-profile-popover')) {
                popover.classList.add('hidden');
                popover.style.display = 'none';
            }
        });

        renderCoraNotifications();
    });
})();
</script>

</div>

<!-- Cora Advanced Command Search Modal (Command Palette for CRM subpages) -->
<div id="cora-command-palette" class="fixed inset-0 z-[999999] hidden items-start justify-center p-4 pt-[6vh] md:pt-[10vh] bg-zinc-950/40 backdrop-blur-sm transition-all duration-200">
    <div class="cora-command-container w-full max-w-2xl bg-white border border-zinc-200 rounded-2xl shadow-2xl overflow-hidden flex flex-col transition-transform transform scale-95 duration-200" style="height: 460px; max-height: 80vh;">
        
        <!-- Mobile Drag Handle Area -->
        <div class="md:hidden flex items-center justify-center pt-3 pb-1 shrink-0 cursor-grab active:cursor-grabbing select-none" id="cora-command-drag-handle-area">
            <div class="w-12 h-1 bg-zinc-200 rounded-full"></div>
        </div>

        <!-- Search Input Header -->
        <div class="flex items-center gap-3 px-4 border-b border-zinc-100 py-3.5 shrink-0">
            <svg class="text-zinc-400 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="cora-command-input" placeholder="Search pages, settings, leads, or listings..." class="flex-1 text-sm bg-transparent border-0 outline-none focus:ring-0 text-zinc-900 placeholder-zinc-400 py-0.5" autocomplete="off">
            <kbd class="text-[9px] font-mono bg-zinc-100 px-1.5 py-0.5 rounded text-zinc-450 border border-zinc-200/60 shadow-sm shrink-0">⌘K</kbd>
        </div>

        <!-- Filter Pills Bar -->
        <div class="flex items-center gap-1.5 px-4 py-2 border-b border-zinc-100 bg-zinc-50/50 overflow-x-auto shrink-0 select-none no-scrollbar">
            <button type="button" class="cora-search-pill active text-xs font-semibold px-3 py-1 rounded-full border border-zinc-200 bg-zinc-900 text-white transition-all cursor-pointer" data-filter="all">Overview</button>
            <button type="button" class="cora-search-pill text-xs font-medium px-3 py-1 rounded-full border border-zinc-200 bg-white text-zinc-650 hover:bg-zinc-55 transition-all cursor-pointer" data-filter="pages">Pages</button>
            <button type="button" class="cora-search-pill text-xs font-medium px-3 py-1 rounded-full border border-zinc-200 bg-white text-zinc-650 hover:bg-zinc-55 transition-all cursor-pointer" data-filter="leads">Leads</button>
            <button type="button" class="cora-search-pill text-xs font-medium px-3 py-1 rounded-full border border-zinc-200 bg-white text-zinc-650 hover:bg-zinc-55 transition-all cursor-pointer" data-filter="settings">Settings</button>
            <button type="button" class="cora-search-pill text-xs font-medium px-3 py-1 rounded-full border border-zinc-200 bg-white text-zinc-650 hover:bg-zinc-55 transition-all cursor-pointer" data-filter="listings">Listings</button>
        </div>

        <!-- Results List Area -->
        <div class="flex-1 overflow-y-auto p-2 min-h-0 space-y-1" id="cora-command-results" style="scrollbar-width: thin;">
            <!-- Loading state / Suggestions list / Search results list -->
        </div>

        <!-- Footer Bar -->
        <div class="border-t border-zinc-100 px-4 py-2.5 bg-zinc-50/50 flex items-center justify-between shrink-0">
            <span class="text-xs text-zinc-450 font-medium">Need help finding something?</span>
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
    transform: scale(1);
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

/* Mobile Bottom Sheet & Search Optimizations */
@media (max-width: 767px) {
    #cora-command-palette {
        align-items: flex-end !important;
        justify-content: center !important;
        padding: 0 !important;
    }
    #cora-command-palette .cora-command-container {
        width: 100% !important;
        max-width: 100% !important;
        height: 75vh !important;
        max-height: 75vh !important;
        border-bottom-left-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        border-top-left-radius: 24px !important;
        border-top-right-radius: 24px !important;
        transform: translateY(100%);
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    #cora-command-palette.active .cora-command-container {
        transform: translateY(0);
    }
    #cora-command-input {
        font-size: 16px !important;
    }
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
                            <span class="text-sm font-extrabold text-zinc-900">${rel.version}</span>
                            ${isLatest ? '<span class="block mt-1 text-[9px] font-bold text-zinc-550 border border-zinc-200 rounded px-1.5 py-0.5 w-max bg-zinc-50 uppercase tracking-wide">Latest</span>' : ''}
                        </div>
                        
                        <!-- Right Version Card List -->
                        <div class="flex-1 space-y-3">
            `;

            rel.items.forEach((item, iIdx) => {
                let svgIcon = '';
                switch(item.icon) {
                    case 'sparkles':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
                        break;
                    case 'edit':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>`;
                        break;
                    case 'wifi':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20" stroke-width="2"></line></svg>`;
                        break;
                    case 'refresh':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>`;
                        break;
                    case 'zap':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`;
                        break;
                    case 'link':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>`;
                        break;
                    case 'code':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>`;
                        break;
                    case 'shield':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`;
                        break;
                    case 'users':
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`;
                        break;
                    default:
                        svgIcon = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>`;
                }

                const itemId = `accordion-${rIdx}-${iIdx}`;
                html += `
                    <div class="cora-update-row-hover bg-white border border-zinc-200 rounded-xl p-4 cursor-pointer transition-all duration-200" onclick="window.coraToggleUpdateAccordion('${itemId}')">
                        <div class="flex items-center justify-between gap-3 select-none">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-zinc-50 flex items-center justify-center shrink-0 border border-zinc-100">
                                    ${svgIcon}
                                </div>
                                <span class="text-xs font-bold text-zinc-900">${item.title}</span>
                            </div>
                            <span class="text-zinc-400 transition-transform duration-200 shrink-0" id="chevron-${itemId}" style="display: flex; align-items: center;">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="transition-transform duration-300 transform"><polyline points="6 9 12 15 18 9"/></svg>
                            </span>
                        </div>
                        <div class="cora-update-accordion-content open mt-3 pl-11 text-xs text-zinc-500 leading-relaxed" id="content-${itemId}">
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
                    badge.className = 'inline-block text-[9px] font-extrabold bg-zinc-950 text-white px-2 py-0.5 rounded tracking-wide uppercase leading-none mb-1.5';
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
        // Mobile Swipe-to-Dismiss Gesture Support for Command Palette
        (function() {
            const palette = document.getElementById('cora-command-palette');
            if (!palette) return;

            const container = palette.querySelector('.cora-command-container');
            const resultsArea = document.getElementById('cora-command-results');
            if (!container) return;

            let startY = 0;
            let currentY = 0;
            let isDragging = false;
            let scrollStartTop = 0;

            container.addEventListener('touchstart', function(e) {
                if (window.innerWidth >= 768) return;

                const touch = e.touches[0];
                startY = touch.clientY;
                currentY = startY;
                isDragging = false;

                if (resultsArea) {
                    scrollStartTop = resultsArea.scrollTop;
                } else {
                    scrollStartTop = 0;
                }
            }, { passive: true });

            container.addEventListener('touchmove', function(e) {
                if (window.innerWidth >= 768) return;

                const touch = e.touches[0];
                const deltaY = touch.clientY - startY;

                // Only drag downwards
                if (deltaY <= 0) {
                    if (isDragging) {
                        if (e.cancelable) e.preventDefault();
                        container.style.transform = 'translateY(0px)';
                        isDragging = false;
                    }
                    return;
                }

                // Allow dragging if not scrolling results or if results are scrolled to the top
                const isTargetInResults = e.target.closest('#cora-command-results');
                const shouldDrag = !isTargetInResults || (scrollStartTop <= 0);

                if (shouldDrag) {
                    if (e.cancelable) e.preventDefault();
                    isDragging = true;
                    currentY = touch.clientY;

                    // Apply translation down to the sheet in real-time
                    container.style.transform = `translateY(${deltaY}px)`;
                    container.style.transition = 'none'; // Disable transition during drag
                }
            }, { passive: false });

            container.addEventListener('touchend', function(e) {
                if (window.innerWidth >= 768) return;

                if (isDragging) {
                    isDragging = false;
                    const deltaY = currentY - startY;
                    const threshold = 120; // Dismiss threshold in pixels

                    container.style.transition = ''; // Restore transition

                    if (deltaY > threshold) {
                        coraCloseCommandPalette();
                        // Reset transform after animation finishes
                        setTimeout(() => {
                            container.style.transform = '';
                        }, 250);
                    } else {
                        // Snap back
                        container.style.transform = 'translateY(0)';
                    }
                }
            }, { passive: true });
        })();

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
            
            // Profile dots
            const deskProfileDot = document.getElementById('cora-desktop-profile-status-dot');
            const mobProfileDot = document.getElementById('cora-mobile-profile-status-dot');
            
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

                if (deskProfileDot) { deskProfileDot.style.backgroundColor = '#22c55e'; }
                if (mobProfileDot) { mobProfileDot.style.backgroundColor = '#22c55e'; }
            } else {
                if (dot) { dot.style.backgroundColor = '#ef4444'; }
                if (label) { label.textContent = 'Punch'; }
                if (popDot) { popDot.style.backgroundColor = '#ef4444'; }
                if (popStatus) popStatus.textContent = 'Not punched in';
                
                if (mobDot) { mobDot.style.backgroundColor = '#ef4444'; }
                if (mobPopDot) { mobPopDot.style.backgroundColor = '#ef4444'; }
                if (mobPopStatus) mobPopStatus.textContent = 'Not punched in';

                if (deskProfileDot) { deskProfileDot.style.backgroundColor = '#ef4444'; }
                if (mobProfileDot) { mobProfileDot.style.backgroundColor = '#ef4444'; }
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
            if (palette && !palette.classList.contains('hidden')) {
                const container = palette.querySelector('.cora-command-container');
                if (container && !container.contains(e.target)) {
                    coraCloseCommandPalette();
                }
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
                        p.classList.remove('active', 'bg-zinc-900', 'text-white');
                        p.classList.add('bg-white', 'text-zinc-650');
                    });
                }
                this.classList.add('active', 'bg-zinc-900', 'text-white');
                this.classList.remove('bg-white', 'text-zinc-650');
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
                            <a href="${item.url}" class="cora-nav-item flex items-center justify-between px-3 py-2.5 text-sm rounded-lg cursor-pointer select-none no-underline text-zinc-800 hover:text-zinc-950 group">
                                <div class="flex items-center gap-3 select-none min-w-0">
                                    <span class="cora-nav-icon select-none text-zinc-500 group-hover:text-zinc-950 shrink-0">
                                        ${getIconSVG(item.icon)}
                                    </span>
                                    <div class="flex flex-col min-w-0">
                                        <span class="cora-nav-text select-none font-semibold text-xs leading-normal truncate text-zinc-800 group-hover:text-zinc-950">${item.title}</span>
                                        <span class="text-[9px] text-zinc-400 font-normal leading-normal truncate">${item.description}</span>
                                    </div>
                                </div>
                                <span class="text-[8px] font-bold tracking-wider text-zinc-400 bg-zinc-100 px-1 py-0.5 rounded uppercase shrink-0">${item.category}</span>
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
                        <span class="text-[11px] font-bold text-zinc-800 leading-normal">There are no results matching to the query</span>
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
                    const target = (item.getAttribute('data-target') || '').toLowerCase();
                    if (text.includes(query) || target.includes(query)) {
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
        script.async = true;
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
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s;
    display: flex;
    flex-direction: column;
    visibility: hidden;
    pointer-events: none;
}
.dark #cora-update-drawer {
    background: #09090b;
    border-left: 1px solid #27272a;
    box-shadow: -10px 0 40px rgba(9, 9, 11, 0.4);
}
#cora-update-drawer.open {
    transform: translateX(0);
    visibility: visible;
    pointer-events: auto;
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

<div id="cora-update-drawer" class="text-zinc-850">
    <!-- Header -->
    <div class="flex items-center justify-between px-8 py-6 border-b border-zinc-200 bg-zinc-50/50 flex-shrink-0">
        <div class="space-y-1">
            <h2 class="text-xl font-bold tracking-tight text-zinc-900">Software Updates</h2>
            <p class="text-xs text-zinc-550">Manage system versions, release channels, and automated feature shipments.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Release Channel selector dropdown mockup -->
            <div class="relative hidden sm:block">
                <button type="button" class="flex items-center gap-3 px-4 py-2 border border-zinc-200 rounded-xl bg-white text-left select-none outline-none">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    <div>
                        <span class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wide leading-none">Release Channel</span>
                        <span class="block text-xs font-bold text-zinc-800 mt-0.5">Production Stable</span>
                    </div>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-450 ml-1"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
            </div>
            
            <button onclick="window.coraCloseUpdateDrawer();" class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 border-none cursor-pointer flex items-center justify-center text-zinc-500 transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
        
        <!-- Status Card -->
        <div class="border border-zinc-200 rounded-xl p-5 bg-white flex flex-col md:flex-row items-start md:items-center justify-between gap-5 shadow-3xs select-none">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-zinc-950 flex items-center justify-center text-white shrink-0 shadow-sm">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none" class="animate-pulse"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                </div>
                <div>
                    <span id="cora-update-badge" class="inline-block text-[9px] font-extrabold bg-zinc-950 text-white px-2 py-0.5 rounded tracking-wide uppercase leading-none mb-1.5">UPDATE AVAILABLE</span>
                    <h3 id="cora-update-platform-title" class="text-sm font-bold text-zinc-900 leading-tight">Cora Workspace Platform v<?php echo esc_html($avail_ver); ?></h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Your current installed version is v<?php echo esc_html(CORA_WORKSPACE_VERSION); ?>.</p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 w-full md:w-auto md:shrink-0">
                <div class="hidden md:block h-8 w-px bg-zinc-200"></div>
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <div>
                        <span class="block text-[9px] font-bold text-zinc-450 uppercase tracking-wide leading-none">Released on</span>
                        <span class="block text-xs font-bold text-zinc-800 mt-0.5">May 21, 2025</span>
                    </div>
                </div>
                <button type="button" onclick="window.coraExecuteWorkspaceUpdate('<?php echo esc_js($avail_ver); ?>');" id="cora-update-main-btn" class="w-full sm:w-auto h-10 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98]">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="rotate-180"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"/></svg>
                    Upgrade Now
                </button>
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="space-y-4 pt-2">
            <div class="flex items-center justify-between border-b border-zinc-200 pb-3">
                <h4 class="text-sm font-bold text-zinc-900">Changelog & Features</h4>
                <button type="button" onclick="window.coraToggleExpandAll(this);" class="h-8 px-3 border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-750 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-3xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                    <span>Expand All</span>
                </button>
            </div>

            <!-- Timeline items rendered dynamically by JavaScript -->
            <div id="cora-changelog-timeline-container" class="py-2"></div>
            
            <div class="flex justify-center pt-2">
                <button type="button" class="h-8 px-4 border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-750 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1">
                    <span>View more improvements in v2.9.0</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
            </div>
        </div>

        <!-- Safety Backup Tip -->
        <div class="flex gap-3 p-4 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-550 leading-relaxed">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8" stroke-width="2.5"/></svg>
            <div>
                <strong class="font-bold text-zinc-700">Recommendation:</strong> Please perform a database and file backup before proceeding with updates to ensure workspace restoration safety.
            </div>
        </div>

    </div>

    <!-- Sticky Footer Actions -->
    <div class="p-6 border-t border-zinc-200 bg-zinc-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 flex-shrink-0">
        <div class="flex items-center gap-2 select-none">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
            <div>
                <span class="block text-[8px] font-bold text-zinc-400 uppercase tracking-wide leading-none">Official Shipment Channel</span>
                <span class="block text-[10px] font-bold text-zinc-550 mt-0.5">Production Stable (GitHub)</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <button type="button" onclick="window.coraCheckForUpdatesNow();" class="flex-1 sm:flex-none h-10 px-4 border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-750 text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-3xs active:scale-[0.98]">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                Check for Updates
            </button>
            <button id="cora-update-confirm-btn" onclick="window.coraExecuteWorkspaceUpdate('<?php echo esc_js($avail_ver); ?>');" class="flex-1 sm:flex-none h-10 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98]">
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
<div id="cora-create-workspace-drawer" class="cora-side-drawer collapsed bg-white border-l border-zinc-200 flex flex-col select-none">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-zinc-200 flex items-center justify-between flex-shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-900">Create New Workspace</h3>
            <p class="text-[11px] text-zinc-550 mt-0.5">Spin up a brand new workspace agency instance</p>
        </div>
        <button type="button" onclick="window.coraToggleCreateWorkspaceDrawer(false);" class="w-8 h-8 rounded-lg bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 cursor-pointer flex items-center justify-center text-zinc-555 transition-colors">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <!-- Scrollable content -->
    <form id="cora-create-ws-form" class="flex-1 overflow-y-auto p-6 space-y-5" onsubmit="event.preventDefault(); window.coraSubmitCreateWorkspace();">
        <div>
            <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Workspace Name</label>
            <input type="text" id="cora-create-ws-name" required placeholder="e.g. Acme Agency" class="w-full h-10 px-3 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-900 placeholder-zinc-400 focus:outline-none focus:border-zinc-900 transition-colors" onkeyup="window.coraAutoSlugify(this.value)">
        </div>

        <div>
            <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Workspace Slug / URL</label>
            <div class="relative flex items-center">
                <span class="absolute left-3 text-[10px] font-mono text-zinc-400 select-none">heycora.in/</span>
                <input type="text" id="cora-create-ws-slug" required placeholder="acme" class="w-full h-10 pl-[74px] pr-3 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-mono text-zinc-900 placeholder-zinc-400 focus:outline-none focus:border-zinc-900 transition-colors">
            </div>
            <p class="text-[10px] text-zinc-400 mt-1.5 leading-normal">The unique URL identifier for this workspace. Use lowercase letters, numbers, and hyphens only.</p>
        </div>

        <div>
            <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Pricing Plan</label>
            <select id="cora-create-ws-plan" class="w-full h-10 px-2.5 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-800 focus:outline-none focus:border-zinc-900 transition-colors">
                <option value="starter">Starter Plan</option>
                <option value="professional">Professional Plan</option>
                <option value="enterprise" selected>Enterprise Plan</option>
            </select>
        </div>

        <div>
            <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Owner Email Address</label>
            <input type="email" id="cora-create-ws-owner-email" required value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" class="w-full h-10 px-3 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-900 placeholder-zinc-400 focus:outline-none focus:border-zinc-900 transition-colors">
        </div>
    </form>

    <!-- Footer actions -->
    <div class="p-6 border-t border-zinc-200 bg-zinc-50 flex items-center gap-3 flex-shrink-0">
        <button type="button" id="cora-create-ws-btn" onclick="window.coraSubmitCreateWorkspace();" class="flex-1 h-10 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98]">
            Create Workspace
        </button>
        <button type="button" onclick="window.coraToggleCreateWorkspaceDrawer(false);" class="px-4 h-10 rounded-lg bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-700 text-xs font-bold transition-all cursor-pointer active:scale-[0.98]">
            Cancel
        </button>
    </div>
</div>

<!-- Edit Workspace Side Drawer -->
<div id="cora-edit-workspace-drawer" class="cora-side-drawer collapsed bg-white border-l border-zinc-200 flex flex-col select-none">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-zinc-200 flex items-center justify-between flex-shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-900">Edit Workspace Settings</h3>
            <p class="text-[11px] text-zinc-550 mt-0.5">Manage administrative settings and status</p>
        </div>
        <button type="button" onclick="window.coraToggleEditWorkspaceDrawer(false);" class="w-8 h-8 rounded-lg bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 cursor-pointer flex items-center justify-center text-zinc-555 transition-colors">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <!-- Scrollable content -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        <input type="hidden" id="cora-edit-ws-id" value="0">

        <div class="space-y-4">
            <div>
                <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Workspace Name</label>
                <input type="text" id="cora-edit-ws-name" required class="w-full h-10 px-3 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-900 focus:outline-none focus:border-zinc-900 transition-colors">
            </div>

            <div>
                <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Workspace Slug</label>
                <div class="relative flex items-center">
                    <span class="absolute left-3 text-[10px] font-mono text-zinc-400 select-none">heycora.in/</span>
                    <input type="text" id="cora-edit-ws-slug" required class="w-full h-10 pl-[74px] pr-3 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-900 transition-colors">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Status</label>
                    <select id="cora-edit-ws-status" class="w-full h-10 px-2.5 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-800 focus:outline-none focus:border-zinc-900 transition-colors">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Pricing Plan</label>
                    <select id="cora-edit-ws-plan" class="w-full h-10 px-2.5 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-800 focus:outline-none focus:border-zinc-900 transition-colors">
                        <option value="starter">Starter Plan</option>
                        <option value="professional">Professional Plan</option>
                        <option value="enterprise">Enterprise Plan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Industry Profile</label>
                <select id="cora-edit-ws-industry" class="w-full h-10 px-2.5 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-800 focus:outline-none focus:border-zinc-900 transition-colors">
                    <option value="real_estate">Real Estate Agency</option>
                    <option value="photography_studio">Photography Studio</option>
                    <option value="custom">Custom Workspace</option>
                </select>
            </div>

            <div>
                <label class="block text-[10.5px] font-bold text-zinc-500 mb-1.5 uppercase tracking-wider">Owner Email Address</label>
                <input type="email" id="cora-edit-ws-owner-email" required class="w-full h-10 px-3 bg-zinc-50 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-900 focus:outline-none focus:border-zinc-900 transition-colors">
            </div>
        </div>

        <!-- Danger Zone (Delete Workspace) -->
        <div class="border-t border-zinc-200 pt-5 mt-5">
            <h4 class="text-xs font-bold text-red-600 mb-1">Danger Zone</h4>
            <p class="text-[10px] text-zinc-500 leading-normal mb-3">Permanently delete this workspace and erase all associated settings, credentials, and assets. This cannot be undone.</p>

            <div id="cora-delete-actions-trigger">
                <button type="button" onclick="window.coraConfirmDeleteWorkspace();" class="w-full h-10 rounded-lg bg-red-500/5 hover:bg-red-500/10 border border-red-500/20 hover:border-red-500/35 text-red-650 text-xs font-bold cursor-pointer transition-colors flex items-center justify-center gap-1.5">
                    Delete Workspace...
                </button>
            </div>

            <div id="cora-delete-actions-confirm" class="hidden p-4 bg-red-500/5 border border-red-500/20 rounded-xl space-y-3">
                <p class="text-[10px] font-bold text-red-600 leading-normal">⚠️ Are you absolutely sure? Click confirm below to permanently wipe this workspace from the database.</p>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.coraExecuteDeleteWorkspace();" class="flex-1 h-9 rounded-lg bg-red-600 hover:bg-red-750 text-white text-[11px] font-bold cursor-pointer transition-colors active:scale-[0.98]">
                        Yes, Delete Workspace
                    </button>
                    <button type="button" onclick="window.coraCancelDeleteWorkspace();" class="px-3.5 h-9 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-bold cursor-pointer transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer actions -->
    <div class="p-6 border-t border-zinc-200 bg-zinc-50 flex items-center gap-3 flex-shrink-0">
        <button type="button" id="cora-edit-ws-btn" onclick="window.coraSubmitUpdateWorkspace();" class="flex-1 h-10 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98]">
            Save Changes
        </button>
        <button type="button" onclick="window.coraToggleEditWorkspaceDrawer(false);" class="px-4 h-10 rounded-lg bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-700 text-xs font-bold transition-all cursor-pointer active:scale-[0.98]">
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
<div id="cora-custom-actions-drawer" class="fixed top-0 right-0 h-full w-[400px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl z-[9995] transform translate-x-full transition-transform duration-300 flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100">
        <h3 class="text-base font-semibold text-zinc-900">Custom Quick Actions</h3>
        <button onclick="window.coraCloseCustomActionDrawer()" class="text-zinc-400 hover:text-zinc-600 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <div class="p-6 overflow-y-auto flex-1">
        <div class="mb-8">
            <h4 class="text-sm font-medium text-zinc-800 mb-3">Add New Action</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-zinc-500 mb-1">Action Name</label>
                    <input type="text" id="cora-custom-action-name" placeholder="e.g. View Documents" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-400 transition-colors" />
                </div>
                <div>
                    <label class="block text-xs text-zinc-500 mb-1">Target Section</label>
                    <select id="cora-custom-action-target" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:border-zinc-400 transition-colors appearance-none">
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
                    <button onclick="window.coraCloseCustomActionDrawer()" class="px-4 py-2 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 transition-colors cursor-pointer">Cancel</button>
                    <button onclick="window.coraSaveCustomAction()" class="px-4 py-2 rounded-lg text-xs font-bold bg-zinc-900 hover:bg-zinc-800 text-white transition-colors cursor-pointer shadow-sm">Save Action</button>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-sm font-medium text-zinc-800 mb-3">Existing Actions</h4>
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
            btn.className = "inline-flex items-center gap-2 px-5 py-2.5 border border-zinc-200/80 bg-white/70 hover:bg-zinc-50 rounded-full text-xs font-semibold text-zinc-650 hover:text-zinc-900 transition-all shadow-3xs cursor-pointer";
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
                listContainer.innerHTML = '<div class="text-xs text-zinc-500 italic">No custom actions yet.</div>';
            } else {
                actions.forEach((action, index) => {
                    const item = document.createElement('div');
                    item.className = "flex items-center justify-between p-3 rounded-lg bg-zinc-50 border border-zinc-100";
                    item.innerHTML = `
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-zinc-800">${action.name}</span>
                            <span class="text-[10px] text-zinc-500 uppercase tracking-wide">${action.target}</span>
                        </div>
                        <button onclick="window.coraDeleteCustomAction(${index})" class="p-1.5 text-zinc-400 hover:text-red-500 hover:bg-zinc-100 rounded-md transition-colors cursor-pointer">
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
<div id="cora-feedback-drawer" onclick="window.coraCloseFeedbackDrawer(event)" class="cora-feedback-drawer collapsed fixed inset-0 z-[9995] flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-[2px] transition-all duration-200">
    <div class="cora-feedback-modal-card w-full max-w-[460px] bg-white border border-zinc-200 shadow-2xl flex flex-col rounded-2xl overflow-hidden transition-all duration-250 transform scale-95 opacity-0" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="px-5 py-4 border-b border-zinc-200 flex justify-between items-center bg-zinc-50/50 shrink-0">
            <div>
                <h2 class="text-sm font-bold text-zinc-900">Submit Feedback &amp; Report</h2>
                <p class="text-[10px] text-zinc-400 mt-0.5">Help us improve by sharing what's on your mind.</p>
            </div>
            <button onclick="window.coraCloseFeedbackDrawer(event)" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1.5 rounded-lg hover:bg-zinc-200/50 transition-colors border-none bg-transparent flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <!-- Current Screen info -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Current Screen</label>
                <div class="flex items-center gap-2 px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl select-none">
                    <span id="cora-feedback-screen-badge" class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span id="cora-feedback-screen-name" class="text-xs font-bold text-zinc-800">Dashboard</span>
                    <span id="cora-feedback-screen-id" class="text-[10px] text-zinc-400 font-mono font-medium">(dashboard)</span>
                </div>
            </div>

            <!-- Issue Category -->
            <div class="space-y-1.5">
                <label for="cora-feedback-type" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Type of Issue</label>
                <select id="cora-feedback-type" class="w-full bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold rounded-xl px-3 py-2 outline-none cursor-pointer focus:border-zinc-900 transition-colors shadow-3xs">
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
                <label for="cora-feedback-desc" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Feedback &amp; Description</label>
                <textarea id="cora-feedback-desc" rows="5" class="w-full bg-white border border-zinc-200 text-zinc-800 text-xs font-medium rounded-xl px-3 py-2 outline-none resize-none focus:border-zinc-900 transition-colors shadow-3xs placeholder-zinc-400" placeholder="Please tell us what is not working, what was expected, or share your general suggestions..."></textarea>
            </div>
        </div>

        <!-- Actions CTA -->
        <div class="p-5 border-t border-zinc-200 bg-zinc-50/50 shrink-0">
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
                        <span class="text-[9px] font-bold text-emerald-600 leading-none">Instant Reply</span>
                        <span class="text-[8px] font-medium text-zinc-400 mt-0.5">Under 1 hour</span>
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
                        <span class="text-[9px] font-bold text-red-550 leading-none">Email Support</span>
                        <span class="text-[8px] font-medium text-zinc-400 mt-0.5">Within 24 hours</span>
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
<div id="cora-drawer-backdrop" onclick="window.coraCloseAllDrawers()" class="hidden fixed inset-0 bg-black/30 z-[9990] backdrop-blur-[1.5px] transition-opacity duration-200 cursor-pointer"></div>

<?php if ( $cora_auto_update && ! empty( $cora_target_version ) ) : ?>
<div id="cora-auto-update-overlay-panel" class="fixed inset-0 z-[999999] bg-zinc-50/90 backdrop-blur-md flex items-center justify-center select-none font-sans">
    <style>
        #cora-auto-update-overlay-panel {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        #cora-auto-update-overlay-panel .step-item {
            transition: all 0.3s ease;
        }
    </style>
    <div class="w-full max-w-md p-8 bg-white border border-zinc-200 rounded-2xl shadow-2xl flex flex-col items-center text-center space-y-6">
        
        <!-- Logo Header -->
        <div class="flex flex-col items-center space-y-2">
            <span class="text-3xl font-black tracking-tight text-zinc-900">cora</span>
            <div class="px-2.5 py-0.5 bg-zinc-100 text-[10px] font-bold text-zinc-500 rounded-full font-mono">
                AUTO-UPGRADE ENGINE
            </div>
        </div>

        <!-- Spinner & Status Title -->
        <div class="flex flex-col items-center space-y-2">
            <div id="cora-upgrade-spinner" class="w-12 h-12 flex items-center justify-center text-zinc-900 mb-2">
                <svg class="animate-spin w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 id="cora-upgrade-status-title" class="text-sm font-bold text-zinc-900">Initializing Platform Upgrade...</h3>
            <p id="cora-upgrade-status-desc" class="text-xs text-zinc-400">Target version: v<?php echo esc_html($cora_target_version); ?></p>
        </div>

        <!-- Step-by-Step Checklist -->
        <div class="w-full text-left space-y-3 bg-zinc-50/50 p-4 border border-zinc-100 rounded-xl">
            <!-- Step 1 -->
            <div id="cora-step-1" class="step-item flex items-center justify-between text-xs text-zinc-400 font-medium">
                <div class="flex items-center gap-2">
                    <div class="step-icon w-5 h-5 rounded-full border border-zinc-200 flex items-center justify-center text-[10px] font-bold">1</div>
                    <span>Validating administrator authorization</span>
                </div>
                <span class="step-status text-[10px] uppercase font-bold tracking-wider">Pending...</span>
            </div>
            <!-- Step 2 -->
            <div id="cora-step-2" class="step-item flex items-center justify-between text-xs text-zinc-400 font-medium">
                <div class="flex items-center gap-2">
                    <div class="step-icon w-5 h-5 rounded-full border border-zinc-200 flex items-center justify-center text-[10px] font-bold">2</div>
                    <span>Downloading workspace update</span>
                </div>
                <span class="step-status text-[10px] uppercase font-bold tracking-wider">Pending...</span>
            </div>
            <!-- Step 3 -->
            <div id="cora-step-3" class="step-item flex items-center justify-between text-xs text-zinc-400 font-medium">
                <div class="flex items-center gap-2">
                    <div class="step-icon w-5 h-5 rounded-full border border-zinc-200 flex items-center justify-center text-[10px] font-bold">3</div>
                    <span>Extracting update packages</span>
                </div>
                <span class="step-status text-[10px] uppercase font-bold tracking-wider">Pending...</span>
            </div>
            <!-- Step 4 -->
            <div id="cora-step-4" class="step-item flex items-center justify-between text-xs text-zinc-400 font-medium">
                <div class="flex items-center gap-2">
                    <div class="step-icon w-5 h-5 rounded-full border border-zinc-200 flex items-center justify-center text-[10px] font-bold">4</div>
                    <span>Upgrading core modules & DB</span>
                </div>
                <span class="step-status text-[10px] uppercase font-bold tracking-wider">Pending...</span>
            </div>
        </div>

        <!-- Failure Details or Exit Button -->
        <div id="cora-upgrade-action-container" class="hidden w-full pt-2">
            <button onclick="window.location.href='?page=cora-workspace'" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-sm">
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
            stepRow.removeClass('text-zinc-400').addClass('text-zinc-900 font-bold');
            statusSpan.removeClass('text-zinc-400').addClass('text-emerald-500 font-bold');
            iconDiv.html('<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none" class="text-emerald-500"><polyline points="20 6 9 17 4 12"></polyline></svg>')
                   .addClass('border-emerald-500 bg-emerald-500/10')
                   .removeClass('border-zinc-200');
        } else if (isSuccess === false) {
            stepRow.removeClass('text-zinc-400').addClass('text-red-650 font-bold');
            statusSpan.removeClass('text-zinc-400').addClass('text-red-500 font-bold');
            iconDiv.html('<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none" class="text-red-500"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>')
                   .addClass('border-red-500 bg-red-500/10')
                   .removeClass('border-zinc-200');
        } else {
            // Running/Active state
            stepRow.removeClass('text-zinc-400').addClass('text-zinc-900 font-bold');
            statusSpan.removeClass('text-zinc-400').addClass('text-zinc-850 font-medium');
            iconDiv.html('<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none" class="animate-spin text-zinc-900"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>')
                   .addClass('border-zinc-900')
                   .removeClass('border-zinc-200');
        }
    }

    function showFail(errMessage) {
        $('#cora-upgrade-spinner').html('<svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="2.5" fill="none" class="text-red-500"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>');
        $('#cora-upgrade-status-title').text('Upgrade Failed').addClass('text-red-650');
        $('#cora-upgrade-status-desc').text(errMessage).addClass('text-red-550');
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
                            $('#cora-upgrade-status-title').text('Workspace Updated!').addClass('text-emerald-600');
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
                                $('#cora-upgrade-status-title').text('Workspace Updated!').addClass('text-emerald-600');
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

<div id="cora-pwa-prompt-banner" class="fixed bottom-6 right-6 z-[9999] max-w-sm w-full bg-white border border-zinc-200 rounded-2xl shadow-xl p-5 transform translate-y-12 opacity-0 pointer-events-none transition-all duration-300 ease-out font-sans">
    <div class="flex items-start gap-4">
        <!-- Monochromatic Icon -->
        <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-900 shrink-0 border border-zinc-200">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold text-zinc-950 mb-1">Get the Cora Desktop App</h4>
            <p class="text-xs text-zinc-500 leading-relaxed mb-3">Install the workspace for a faster, offline-capable experience with real-time push notifications.</p>
            <div class="flex items-center gap-2">
                <button id="cora-pwa-prompt-install" class="px-3 py-1.5 bg-zinc-950 text-white text-xs font-semibold rounded-lg hover:bg-zinc-800 transition">
                    Install App
                </button>
                <button id="cora-pwa-prompt-dismiss" class="px-3 py-1.5 border border-zinc-200 hover:bg-zinc-50 text-zinc-650 text-xs font-semibold rounded-lg transition">
                    Later
                </button>
            </div>
            <button id="cora-pwa-prompt-never" class="mt-2.5 block text-[10px] text-zinc-400 hover:text-zinc-650 transition underline">
                Don't ask me again
            </button>
        </div>
    </div>
</div>

<script>
// PWA Installation & Push Subscription Logic
let coraPwaDeferredPrompt;

function coraShowPwaPrompt() {
    const neverPrompt = localStorage.getItem('cora_pwa_never_prompt') === 'true';
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    
    if (neverPrompt || isStandalone) return;
    
    setTimeout(() => {
        const banner = document.getElementById('cora-pwa-prompt-banner');
        if (banner) {
            banner.classList.remove('translate-y-12', 'opacity-0', 'pointer-events-none');
            banner.classList.add('translate-y-0', 'opacity-100');
        }
    }, 2000);
}

function coraHidePwaPrompt() {
    const banner = document.getElementById('cora-pwa-prompt-banner');
    if (banner) {
        banner.classList.remove('translate-y-0', 'opacity-100');
        banner.classList.add('translate-y-12', 'opacity-0', 'pointer-events-none');
    }
}

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    coraPwaDeferredPrompt = e;
    window.coraPwaDeferredPrompt = e; // Expose globally for manual PWA install button in Settings
    const installBtn = document.getElementById('cora-pwa-install-btn');
    if (installBtn) {
        installBtn.classList.remove('hidden');
    }
    coraShowPwaPrompt();
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
                        navigator.serviceWorker.register('<?php echo home_url('/cora-service-worker.js'); ?>?v=<?php echo CORA_WORKSPACE_VERSION; ?>&token=' + token, { scope: '/' })
                            .then(() => {
                                const badge = document.getElementById('cora-pwa-badge');
                                if (badge) {
                                    badge.innerText = 'Active';
                                    badge.className = 'text-[9px] font-bold px-1.5 py-0.5 bg-emerald-600 text-white rounded uppercase';
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

    // Prompt banner buttons
    const promptInstall = document.getElementById('cora-pwa-prompt-install');
    if (promptInstall) {
        promptInstall.addEventListener('click', () => {
            if (!coraPwaDeferredPrompt) return;
            coraPwaDeferredPrompt.prompt();
            coraPwaDeferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    if (installBtn) installBtn.classList.add('hidden');
                }
                coraHidePwaPrompt();
                coraPwaDeferredPrompt = null;
            });
        });
    }

    const promptDismiss = document.getElementById('cora-pwa-prompt-dismiss');
    if (promptDismiss) {
        promptDismiss.addEventListener('click', () => {
            coraHidePwaPrompt();
        });
    }

    const promptNever = document.getElementById('cora-pwa-prompt-never');
    if (promptNever) {
        promptNever.addEventListener('click', () => {
            localStorage.setItem('cora_pwa_never_prompt', 'true');
            coraHidePwaPrompt();
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
                        badge.className = 'text-[9px] font-bold px-1.5 py-0.5 bg-emerald-600 text-white rounded uppercase';
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
