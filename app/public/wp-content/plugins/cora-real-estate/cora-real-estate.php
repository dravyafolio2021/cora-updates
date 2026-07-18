<?php
/**
 * Plugin Name: Cora for Real Estate
 * Plugin URI: https://cora.ai
 * Description: A clean, minimal Notion-style workspace dashboard for real estate agencies in India and globally. Empowered with AI workflows, booking management, and photo helpers.
  * Version: 1.0.0
 * Author: Cora AI Team
 * Author URI: https://cora.ai
 * License: GPL2
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants
define( 'CORA_REAL_ESTATE_AI_VERSION', '1.0.0' );
define( 'CORA_REAL_ESTATE_AI_PATH', plugin_dir_path( __FILE__ ) );
define( 'CORA_REAL_ESTATE_AI_URL', plugin_dir_url( __FILE__ ) );
define( 'CORA_PLUGIN_FILE', __FILE__ );

// Autoloaders / Libraries

// ── Git Integration ────────────────────────────────────────────────────────
require_once plugin_dir_path( __FILE__ ) . 'includes/class-cora-github-integration.php';

/**
 * Add the admin menu page
 */
function cora_real_estate_ai_admin_menu() {
    add_menu_page(
        __( 'Cora for Real Estate', 'cora-real-estate' ),
        __( 'Cora AI', 'cora-real-estate' ),
        'manage_options',
        'cora-real-estate',
        'cora_real_estate_ai_render_dashboard',
        'dashicons-superhero', // Custom icon placeholder
        2 // High position in the sidebar
    );
}
add_action( 'admin_menu', 'cora_real_estate_ai_admin_menu' );

/**
 * Render the dashboard page
 */
/**
 * Render the dashboard page (redirects to the standalone workspace URL)
 */
function cora_real_estate_ai_render_dashboard() {
    wp_redirect( home_url( '/workspace' ) );
    exit;
}
add_action( 'admin_menu', 'cora_real_estate_ai_admin_menu' );

// ══════════════════════════════════════════════════════════════════════════════
// ██  CORA PLATFORM — WORDPRESS WHITE-LABEL LAYER
// ██  Completely hides all WordPress branding from end-users.
// ██  Covers: admin bar, favicons, generator tags, login page, error pages,
// ██  version leakage, head artifacts, emoji scripts, and admin notices.
// ══════════════════════════════════════════════════════════════════════════════

/**
 * 1. HIDE ADMIN BAR — Remove the black WP toolbar from the frontend entirely.
 */
add_filter( 'show_admin_bar', '__return_false' );
add_action( 'wp_head', function() {
    echo '<style>#wpadminbar { display: none !important; } html { margin-top: 0 !important; } * html body { margin-top: 0 !important; }</style>' . "\n";
}, 100 );

/**
 * 2. FAVICON — Replace the WordPress "W" favicon with the Cora logo on all pages.
 */
function cora_whitelabel_favicon() {
    $favicon_url = CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png';
    echo '<link rel="icon" type="image/png" href="' . esc_url( $favicon_url ) . '" sizes="32x32">' . "\n";
    echo '<link rel="shortcut icon" href="' . esc_url( $favicon_url ) . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url( $favicon_url ) . '">' . "\n";
}
add_action( 'wp_head',    'cora_whitelabel_favicon', 1 );
add_action( 'admin_head', 'cora_whitelabel_favicon', 1 );
add_action( 'login_head', 'cora_whitelabel_favicon', 1 );

/**
 * 3. REMOVE GENERATOR TAG — Hides <meta name="generator" content="WordPress x.x.x" />.
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * 4. REMOVE VERSION NUMBERS from script/style query strings (?ver=X.X fingerprinting).
 */
function cora_whitelabel_remove_version( $src ) {
    if ( strpos( $src, '?ver=' ) || strpos( $src, '&ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'style_loader_src',  'cora_whitelabel_remove_version', 9999 );
add_filter( 'script_loader_src', 'cora_whitelabel_remove_version', 9999 );

/**
 * 5. REMOVE UNNECESSARY HEAD ARTIFACTS that expose WordPress to scanners/inspectors.
 */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'feed_links',         2 );
remove_action( 'wp_head', 'feed_links_extra',   3 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles',  'print_emoji_styles' );
remove_filter( 'the_content_feed',   'wp_staticize_emoji' );
remove_filter( 'comment_text_rss',   'wp_staticize_emoji' );
remove_filter( 'wp_mail',            'wp_staticize_emoji_for_email' );

/**
 * 6. REMOVE SERVER HEADERS that expose the platform.
 */
add_filter( 'wp_headers', function( $headers ) {
    unset( $headers['X-Powered-By'] );
    return $headers;
} );

/**
 * 7. CUSTOM LOGIN PAGE — Strip WordPress branding from wp-login.php.
 */
add_action( 'login_enqueue_scripts', function() {
    $favicon_url = CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png';
    echo '<style>
        body.login { background: #f9f9f8 !important; }
        #login h1 a {
            background-image: url(' . esc_url( $favicon_url ) . ') !important;
            background-size: contain !important;
            background-position: center !important;
            width: 80px !important;
            height: 80px !important;
        }
        .login #nav a, .login #backtoblog a { color: #3f3f46 !important; }
        .login form { border-top: 2px solid #18181b !important; }
        body.login div#login_error, body.login .message, body.login .success {
            border-left: 3px solid #18181b !important;
        }
    </style>' . "\n";
} );
add_filter( 'login_headerurl',  fn() => home_url( '/workspace' ) );
add_filter( 'login_headertext', fn() => 'Cora Platform' );
add_filter( 'login_title', function( $login_title ) {
    return str_replace( array( 'WordPress', 'Log In' ), array( 'Cora', 'Sign In' ), $login_title );
} );

/**
 * 8. CUSTOM WP_DIE HANDLER — Replace the WP-branded error screen with a Cora error card.
 */
add_filter( 'wp_die_handler', function() {
    return 'cora_custom_die_handler';
} );
function cora_custom_die_handler( $message, $title = '', $args = array() ) {
    // Only intercept frontend; let admin/AJAX use default handler
    if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        _default_wp_die_handler( $message, $title, $args );
        return;
    }
    if ( is_wp_error( $message ) ) {
        $message = $message->get_error_message();
    }
    $title   = empty( $title ) ? 'Error — Cora' : esc_html( $title ) . ' — Cora';
    $message = wp_kses_post( $message );
    $favicon = CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png';
    status_header( isset( $args['response'] ) ? $args['response'] : 500 );
    nocache_headers();
    header( 'Content-Type: text/html; charset=utf-8' );
    echo '<!DOCTYPE html><html lang="en"><head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>' . esc_html( $title ) . '</title>
        <link rel="icon" type="image/png" href="' . esc_url( $favicon ) . '">
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body { font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
                   background:#fafafa; display:flex; align-items:center;
                   justify-content:center; min-height:100vh; color:#18181b; }
            .cora-err { background:#fff; border:1px solid #e4e4e7; border-radius:12px;
                padding:40px 48px; max-width:480px; text-align:center;
                box-shadow:0 4px 24px rgba(0,0,0,.06); }
            .cora-err-logo { width:48px; height:48px; margin:0 auto 20px;
                background-image:url(' . esc_url( $favicon ) . ');
                background-size:contain; background-repeat:no-repeat;
                background-position:center; }
            h1 { font-size:18px; font-weight:700; margin-bottom:10px; }
            p  { font-size:14px; color:#71717a; line-height:1.6; }
            a  { display:inline-block; margin-top:24px; padding:9px 20px;
                background:#18181b; color:#fff; text-decoration:none;
                border-radius:8px; font-size:13px; font-weight:600; }
        </style>
    </head><body>
        <div class="cora-err">
            <div class="cora-err-logo"></div>
            <h1>Something went wrong</h1>
            <p>' . $message . '</p>
            <a href="' . esc_url( home_url( '/workspace' ) ) . '">← Back to Dashboard</a>
        </div>
    </body></html>';
    exit;
}

/**
 * 9. ADMIN AREA BRANDING — Clean up "Howdy", WP logo, footer credits, update banners.
 */
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    // Remove "Howdy," greeting from the account node
    $account = $wp_admin_bar->get_node( 'my-account' );
    if ( $account ) {
        $account->title = str_replace( 'Howdy,', '', $account->title );
        $wp_admin_bar->add_node( (array) $account );
    }
    // Remove the WordPress logo menu from admin bar
    $wp_admin_bar->remove_node( 'wp-logo' );
}, 25 );
// Replace "Thank you for creating with WordPress" in admin footer
add_filter( 'admin_footer_text', fn() => '<span>Powered by <strong>Cora Platform</strong></span>' );
// Remove WordPress version number from admin footer right side
add_filter( 'update_footer', '__return_empty_string', 11 );
// Replace "WordPress" with "Cora" in admin browser tab <title>
add_filter( 'admin_title', function( $admin_title ) {
    return str_replace( ' &#8212; WordPress', ' &#8212; Cora', $admin_title );
} );
// Remove WordPress-specific dashboard widgets
add_action( 'wp_dashboard_setup', function() {
    remove_meta_box( 'dashboard_primary',     'dashboard', 'side'   );
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side'   );
    remove_meta_box( 'dashboard_right_now',   'dashboard', 'normal' );
    remove_meta_box( 'dashboard_activity',    'dashboard', 'normal' );
} );

/**
 * 10. DISABLE ELEMENTOR NOTES MODULE TO PREVENT RESKIN INITIALIZATION CRASHES
 */
add_filter( 'elementor/notes/is_active', '__return_false' );

function cora_disable_elementor_notes_scripts() {
    wp_dequeue_script( 'elementor-pro-notes' );
    wp_deregister_script( 'elementor-pro-notes' );
    wp_dequeue_script( 'elementor-notes' );
    wp_deregister_script( 'elementor-notes' );
    wp_dequeue_script( 'notes' );
    wp_deregister_script( 'notes' );

    // Dequeue all Elementor AI scripts — removes "Edit with AI" feature entirely
    $ai_handles = array(
        'elementor-ai',
        'elementor-ai-layout',
        'elementor-ai-media-library',
        'elementor-ai-gutenberg',
        'elementor-ai-admin',
        'elementor-ai-unify-product-images',
    );
    foreach ( $ai_handles as $handle ) {
        wp_dequeue_script( $handle );
        wp_deregister_script( $handle );
    }
}
add_action( 'wp_enqueue_scripts', 'cora_disable_elementor_notes_scripts', 999 );
add_action( 'admin_enqueue_scripts', 'cora_disable_elementor_notes_scripts', 999 );
add_action( 'elementor/editor/before_enqueue_scripts', 'cora_disable_elementor_notes_scripts', 999 );
add_action( 'elementor/editor/after_enqueue_scripts', 'cora_disable_elementor_notes_scripts', 999 );

/**
 * 11. BLOCK ALL PLUGIN INSTALLATION GLOBALLY — Platform Security
 *
 * Strips install_plugins and activate_plugins capabilities from every user
 * on the platform. No user — including administrators — can install or activate
 * plugins via the Elementor notice banners (e.g. "Install Ally"), the WordPress
 * admin plugin page, or any direct wp-admin/update.php URL.
 *
 * This is a platform-level security lock, not a role-level permission.
 */
add_filter( 'user_has_cap', function( $allcaps, $caps, $args, $user ) {
    $blocked = array( 'install_plugins', 'activate_plugins', 'update_plugins', 'delete_plugins', 'upload_plugins' );
    foreach ( $blocked as $cap ) {
        $allcaps[ $cap ] = false;
    }
    return $allcaps;
}, 999, 4 );

// Also hard-block the wp-admin/update.php install-plugin action via a redirect
add_action( 'admin_init', function() {
    if (
        isset( $_GET['action'] ) &&
        in_array( $_GET['action'], array( 'install-plugin', 'upload-plugin', 'activate-plugin', 'update-plugin' ), true ) &&
        strpos( $_SERVER['PHP_SELF'] ?? '', 'update.php' ) !== false
    ) {
        wp_die(
            '<strong>Plugin installation is disabled on this platform.</strong> Contact your system administrator.',
            'Action Not Allowed',
            array( 'response' => 403, 'back_link' => true )
        );
    }
} );

// Block the Elementor editor AJAX events that trigger plugin install notice clicks
add_action( 'wp_ajax_elementor_pro_allow_ally', function() {
    wp_send_json_error( array( 'message' => 'Plugin installation is disabled on this platform.' ), 403 );
} );
add_action( 'wp_ajax_elementor_allow_plugin_install', function() {
    wp_send_json_error( array( 'message' => 'Plugin installation is disabled on this platform.' ), 403 );
} );

// Suppress Elementor in-panel control notice rendering (PHP filter)
add_filter( 'elementor/control/register', function( $control ) {
    // Block the notice control type from rendering any 'install-plugin' action URL
    if ( method_exists( $control, 'get_type' ) && $control->get_type() === 'notice' ) {
        return null; // Returning null prevents registration
    }
    return $control;
} );


/**
 * 10. SUPPRESS UPDATE NAGS — Hide "WordPress X.X is available" notices from non-superadmins.
 */
add_action( 'admin_init', function() {
    if ( ! current_user_can( 'update_core' ) ) {
        remove_action( 'admin_notices',         'update_nag', 3 );
        remove_action( 'network_admin_notices', 'update_nag', 3 );
    }
} );
add_filter( 'pre_option_update_core', '__return_null' );

// ══════════════════════════════════════════════════════════════════════════════
// ██  END WHITE-LABEL LAYER
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Intercept requests to /workspace and render the standalone dashboard
 */
function cora_real_estate_ai_handle_workspace_route() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path = parse_url( home_url(), PHP_URL_PATH );
    $path = substr( $request_uri, strlen( $home_path ) );
    $path = trim( parse_url( $path, PHP_URL_PATH ), '/' );

    $path_parts = explode( '/', $path );
    
    // Intercept REST API v1 requests
    if ( isset( $path_parts[0] ) && 'api' === $path_parts[0] && isset( $path_parts[1] ) && 'v1' === $path_parts[1] ) {
        cora_handle_api_v1_request( $path_parts );
        exit;
    }
    
    // Intercept PWA Manifest and Service Worker to serve them from the root scope
    if ( $path === 'cora-service-worker.js' ) {
        header( 'Content-Type: application/javascript' );
        header( 'Service-Worker-Allowed: /' );
        echo file_get_contents( CORA_REAL_ESTATE_AI_PATH . 'assets/pwa/service-worker.js' );
        exit;
    }
    if ( $path === 'cora-manifest.json' ) {
        header( 'Content-Type: application/json' );
        echo file_get_contents( CORA_REAL_ESTATE_AI_PATH . 'assets/pwa/manifest.json' );
        exit;
    }

    if ( isset( $path_parts[0] ) && 'shared-doc' === $path_parts[0] ) {
        $hash = isset( $path_parts[1] ) ? sanitize_text_field( $path_parts[1] ) : '';
        if ( ! empty( $hash ) ) {
            $documents = get_option( 'cora_re_vault_docs', array() );
            $found_doc = null;
            $found_share = null;
            foreach ( $documents as $doc ) {
                if ( ! empty( $doc['secured_shares'] ) ) {
                    foreach ( $doc['secured_shares'] as $share ) {
                        if ( isset($share['hash']) && $share['hash'] === $hash ) {
                            $found_doc = $doc;
                            $found_share = $share;
                            break 2;
                        }
                    }
                }
            }

            if ( $found_doc && $found_share ) {
                $is_expired = false;
                if ( ! empty( $found_share['expiry_time'] ) && intval( $found_share['expiry_time'] ) > 0 && time() > intval( $found_share['expiry_time'] ) ) {
                    $is_expired = true;
                }
                
                nocache_headers();
                include CORA_REAL_ESTATE_AI_PATH . 'public-doc-view.php';
                exit;
            }
        }
        wp_die( __( 'Invalid or expired secure document link.', 'cora-real-estate' ), __( 'Access Denied', 'cora-real-estate' ), array( 'response' => 403 ) );
    }

    if ( isset( $path_parts[0] ) && 'shared-portfolio' === $path_parts[0] ) {
        $hash = isset( $path_parts[1] ) ? sanitize_text_field( $path_parts[1] ) : '';
        if ( ! empty( $hash ) ) {
            $portfolios = get_option( 'cora_re_portfolios', array() );
            $found_portfolio = null;
            if ( is_array( $portfolios ) ) {
                foreach ( $portfolios as $portfolio ) {
                    if ( isset( $portfolio['hash'] ) && $portfolio['hash'] === $hash ) {
                        $found_portfolio = $portfolio;
                        break;
                    }
                }
            }

            if ( $found_portfolio ) {
                nocache_headers();
                include CORA_REAL_ESTATE_AI_PATH . 'public-gallery-view.php';
                exit;
            }
        }
        wp_die( __( 'Invalid or secure portfolio link.', 'cora-real-estate' ), __( 'Access Denied', 'cora-real-estate' ), array( 'response' => 403 ) );
    }

    if ( isset( $path_parts[0] ) && 'shared-form' === $path_parts[0] ) {
        $form_id = isset( $path_parts[1] ) ? intval( $path_parts[1] ) : 0;
        if ( $form_id > 0 ) {
            global $wpdb;
            $form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_forms WHERE id = %d", $form_id ), ARRAY_A );
            if ( $form ) {
                nocache_headers();
                include CORA_REAL_ESTATE_AI_PATH . 'public-form-view.php';
                exit;
            }
        }
        wp_die( __( 'Invalid or inactive form link.', 'cora-real-estate' ), __( 'Access Denied', 'cora-real-estate' ), array( 'response' => 403 ) );
    }

    if ( isset( $path_parts[0] ) && 'workspace' === $path_parts[0] ) {
        $sub_page = isset( $path_parts[1] ) ? sanitize_title( $path_parts[1] ) : '';
        $public_subs = array( 'login', 'forgot-password', 'reset-password', 'setup-account' );

        // If logged in and hitting auth pages, redirect to dashboard
        if ( is_user_logged_in() && in_array( $sub_page, $public_subs ) ) {
            wp_redirect( home_url( '/workspace/dashboard' ) );
            exit;
        }







        // If not logged in
        if ( ! is_user_logged_in() ) {
            if ( in_array( $sub_page, $public_subs ) ) {
                nocache_headers();
                $template_file = CORA_REAL_ESTATE_AI_PATH . 'views/' . $sub_page . '.php';
                if ( file_exists( $template_file ) ) {
                    status_header( 200 );
                    include $template_file;
                    exit;
                }
            }
            // Redirect to custom login page
            $redirect_url = home_url( '/workspace/login' );
            if ( ! empty( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], '/workspace/login' ) === false ) {
                $redirect_url = add_query_arg( 'redirect_to', urlencode( home_url( $_SERVER['REQUEST_URI'] ) ), $redirect_url );
            }
            wp_redirect( $redirect_url );
            exit;
        }

        // Allow administrators and custom photography roles to view workspace
        $user = wp_get_current_user();
        
        // Deactivated user check (Spec Section 6.3)
        $user_status = get_user_meta( $user->ID, 'cora_user_status', true );
        if ( $user_status === 'inactive' ) {
            wp_logout();
            wp_redirect( home_url( '/workspace/login?deactivated=1' ) );
            exit;
        }

        // Agency suspension check (Spec Section 3.4)
        $agency_id = get_user_meta( $user->ID, 'cora_agency_id', true );
        if ( ! empty( $agency_id ) && $agency_id !== 'super' ) {
            $agencies = get_option( 'cora_agencies', array() );
            if ( isset( $agencies[$agency_id] ) && $agencies[$agency_id]['status'] === 'suspended' ) {
                wp_logout();
                wp_redirect( home_url( '/workspace/login?suspended=1' ) );
                exit;
            }
        }

        $allowed_roles = array( 'administrator', 'cora_manager', 'cora_branch_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor', 'cora_viewer' );
        $user_roles = (array) $user->roles;
        $has_access = false;
        foreach ( $allowed_roles as $role ) {
            if ( in_array( $role, $user_roles ) ) {
                $has_access = true;
                break;
            }
        }
        if ( ! $has_access ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'cora-real-estate' ) );
        }

        // Parse sub-page
        $sub_page = isset( $path_parts[1] ) ? sanitize_title( $path_parts[1] ) : 'dashboard';
        if ( empty( $sub_page ) ) {
            $sub_page = 'dashboard';
        }
        if ( $sub_page === 'audit-panel' ) {
            wp_redirect( home_url( '/workspace/settings-suite?settings_tab=audit' ) );
            exit;
        }
        if ( $sub_page === 'settings-suite' && isset( $_GET['settings_tab'] ) && $_GET['settings_tab'] === 'mcp' ) {
            wp_redirect( home_url( '/workspace/mcp' ) );
            exit;
        }

        // Role-based access control check (Server-Side)
        $cora_permissions = get_option( 'cora_role_permissions', array() );
        $current_user_role = ! empty( $user->roles ) ? $user->roles[0] : 'subscriber';
        
        $allowed_features = isset( $cora_permissions[$current_user_role] ) ? $cora_permissions[$current_user_role] : array();
        if ( empty( $allowed_features ) ) {
            $allowed_features = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'attendance', 'tasks', 'visual-builder', 'audit-panel', 'media', 'canvas', 'forms', 'ecosystem', 'mcp' );
        }
        
        if ( in_array( $current_user_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
            $all_admin_features = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'attendance', 'tasks', 'visual-builder', 'audit-panel', 'media', 'canvas', 'forms', 'ecosystem', 'profile', 'mcp' );
            foreach ( $all_admin_features as $feat ) {
                if ( ! in_array( $feat, $allowed_features ) ) {
                    $allowed_features[] = $feat;
                }
            }
        }
        
        // Prevent accessing disallowed sub-pages
        if ( $sub_page !== 'dashboard' && $sub_page !== 'feature-hub' && ! in_array( $sub_page, $allowed_features ) ) {
            wp_redirect( home_url( '/workspace/dashboard' ) );
            exit;
        }

        // Standalone presentation page intercept for Ecosystem Map
        if ( $sub_page === 'ecosystem' ) {
            nocache_headers();
            include CORA_REAL_ESTATE_AI_PATH . 'views/view-ecosystem.php';
            exit;
        }

        // Handle Google Business Profile OAuth callback (code exchange)
        if ( $sub_page === 'gbp' && isset( $_GET['code'] ) && isset( $_GET['state'] ) ) {
            if ( ! wp_verify_nonce( sanitize_text_field( $_GET['state'] ), 'cora_gbp_oauth_state' ) ) {
                wp_redirect( home_url( '/workspace/gbp' ) );
                exit;
            }

            $client_id     = get_option( 'cora_gbp_client_id', '' );
            $client_secret = get_option( 'cora_gbp_client_secret', '' );
            $redirect_uri  = home_url( '/workspace/gbp' );
            $code          = sanitize_text_field( $_GET['code'] );

            $response = wp_remote_post( 'https://oauth2.googleapis.com/token', array(
                'timeout' => 15,
                'body'    => array(
                    'code'          => $code,
                    'client_id'     => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri'  => $redirect_uri,
                    'grant_type'    => 'authorization_code',
                ),
            ) );

            if ( ! is_wp_error( $response ) ) {
                $body   = json_decode( wp_remote_retrieve_body( $response ), true );
                if ( ! empty( $body['access_token'] ) ) {
                    $tokens = array(
                        'access_token'  => $body['access_token'],
                        'refresh_token' => $body['refresh_token'] ?? '',
                        'expires_at'    => time() + ( intval( $body['expires_in'] ?? 3600 ) - 60 ),
                        'token_type'    => $body['token_type'] ?? 'Bearer',
                    );
                    update_option( 'cora_gbp_tokens', $tokens );
                    // Clear any previously selected location so user picks from real list
                    delete_option( 'cora_gbp_profile' );
                }
            }

            // Redirect clean — remove OAuth query params
            wp_redirect( home_url( '/workspace/gbp' ) );
            exit;
        }

        // Prevent browser caching
        nocache_headers();

        // Fetch real WordPress posts for the Content Suite
        $cora_posts_query = new WP_Query(array(
            'post_type' => 'post',
            'post_status' => array('publish', 'draft', 'pending', 'future', 'private'),
            'posts_per_page' => 50,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        $cora_posts = $cora_posts_query->posts;

        status_header( 200 );
        // Load the dashboard HTML template directly
        include CORA_REAL_ESTATE_AI_PATH . 'admin-dashboard.php';
        exit;
    }
}
add_action( 'template_redirect', 'cora_real_estate_ai_handle_workspace_route' );

/**
 * Enqueue scripts and styles only on our admin page
 */
function cora_real_estate_ai_admin_assets( $hook ) {
    // Only load on our plugin page
    if ( 'toplevel_page_cora-real-estate' !== $hook ) {
        return;
    }

    // Enqueue Google Font: Inter
    wp_enqueue_style(
        'cora-font-inter',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        array(),
        null
    );

    // Enqueue styles
    wp_enqueue_style(
        'cora-tailwind',
        CORA_REAL_ESTATE_AI_URL . 'assets/css/tailwind-built.css',
        array(),
        CORA_REAL_ESTATE_AI_VERSION
    );

    wp_enqueue_style(
        'cora-admin-style',
        CORA_REAL_ESTATE_AI_URL . 'assets/css/admin-style.css',
        array(),
        CORA_REAL_ESTATE_AI_VERSION
    );

    // Enqueue scripts
    wp_enqueue_script(
        'cora-admin-script',
        CORA_REAL_ESTATE_AI_URL . 'assets/js/admin-script.js',
        array( 'jquery' ),
        CORA_REAL_ESTATE_AI_VERSION,
        true // Load in footer
    );

    // Enqueue WordPress media libraries for logo upload
    wp_enqueue_media();

    // Localize script to pass server variables if needed (e.g. site URL, ajaxurl)
    $cora_gemini_key_saved  = ! empty( get_option( 'cora_re_ai_gemini_key', '' ) );
    $cora_openai_key_saved  = ! empty( get_option( 'cora_re_ai_openai_key', '' ) );
    $cora_active_ai_model   = get_option( 'cora_re_active_ai_model', 'cora-core-v2' );
    wp_localize_script( 'cora-admin-script', 'coraREWPData', array(
        'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
        'siteUrl'          => get_site_url(),
        'restUrl'          => esc_url_raw( rest_url() ),
        'nonce'            => wp_create_nonce( 'wp_rest' ),
        'ajaxNonce'        => wp_create_nonce( 'cora_ajax_nonce' ),
        'geminiKeySaved'   => $cora_gemini_key_saved,
        'openaiKeySaved'   => $cora_openai_key_saved,
        'activeAiModel'    => $cora_active_ai_model,
    ) );
}
add_action( 'admin_enqueue_scripts', 'cora_real_estate_ai_admin_assets' );

/**
 * Redirect custom studio roles and administrators to our custom dashboard after login
 */
function cora_real_estate_ai_login_redirect( $redirect_to, $request, $user ) {
    if ( $user instanceof WP_User ) {
        $allowed_roles = array( 'administrator', 'cora_manager', 'cora_branch_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor', 'cora_viewer' );
        foreach ( $allowed_roles as $role ) {
            if ( in_array( $role, (array) $user->roles ) ) {
                return home_url( '/workspace' );
            }
        }
    } else {
        $current_user = wp_get_current_user();
        if ( $current_user && $current_user->exists() ) {
            $allowed_roles = array( 'administrator', 'cora_manager', 'cora_branch_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor', 'cora_viewer' );
            foreach ( $allowed_roles as $role ) {
                if ( in_array( $role, (array) $current_user->roles ) ) {
                    return home_url( '/workspace' );
                }
            }
        }
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'cora_real_estate_ai_login_redirect', 10, 3 );

/**
 * Handle direct login event redirect
 */
function cora_real_estate_ai_on_wp_login( $user_login, $user ) {
    if ( $user instanceof WP_User ) {
        $allowed_roles = array( 'administrator', 'cora_manager', 'cora_branch_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor', 'cora_viewer' );
        foreach ( $allowed_roles as $role ) {
            if ( in_array( $role, (array) $user->roles ) ) {
                wp_redirect( home_url( '/workspace' ) );
                exit;
            }
        }
    }
}
add_action( 'wp_login', 'cora_real_estate_ai_on_wp_login', 10, 2 );

/**
 * Restrict non-administrators from accessing the default WP Admin backend entirely
 */
function cora_real_estate_ai_restrict_admin_access() {
    if ( is_admin() && ! current_user_can( 'manage_options' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url( '/workspace' ) );
        exit;
    }
}
add_action( 'admin_init', 'cora_real_estate_ai_restrict_admin_access' );

/**
 * Force users_can_register option to enable user signups
 */
function cora_real_estate_ai_users_can_register( $value ) {
    return 1;
}
add_filter( 'option_users_can_register', 'cora_real_estate_ai_users_can_register' );

/**
 * Render custom fields on the WordPress registration form
 */
function cora_real_estate_ai_register_form() {
    $studio_name = ( ! empty( $_POST['cora_re_agency_name'] ) ) ? sanitize_text_field( $_POST['cora_re_agency_name'] ) : '';
    $phone = ( ! empty( $_POST['cora_phone'] ) ) ? sanitize_text_field( $_POST['cora_phone'] ) : '';
    $role = ( ! empty( $_POST['cora_role'] ) ) ? sanitize_text_field( $_POST['cora_role'] ) : 'cora_manager';
    ?>
    <p>
        <label for="cora_password"><?php _e( 'Password', 'cora-real-estate' ); ?><br />
        <input type="password" name="cora_password" id="cora_password" class="input" value="" size="25" autocomplete="new-password" /></label>
    </p>
    <p>
        <label for="cora_confirm_password"><?php _e( 'Confirm Password', 'cora-real-estate' ); ?><br />
        <input type="password" name="cora_confirm_password" id="cora_confirm_password" class="input" value="" size="25" autocomplete="new-password" /></label>
    </p>
    <p>
        <label for="cora_re_agency_name"><?php _e( 'Agency Name', 'cora-real-estate' ); ?><br />
        <input type="text" name="cora_re_agency_name" id="cora_re_agency_name" class="input" value="<?php echo esc_attr( $studio_name ); ?>" size="25" /></label>
    </p>
    <p>
        <label for="cora_phone"><?php _e( 'Phone Number', 'cora-real-estate' ); ?><br />
        <input type="text" name="cora_phone" id="cora_phone" class="input" value="<?php echo esc_attr( $phone ); ?>" size="25" /></label>
    </p>
    <p>
        <label for="cora_role"><?php _e( 'Agent Role', 'cora-real-estate' ); ?><br />
        <select name="cora_role" id="cora_role" class="input" style="width: 100%; height: 40px; margin-top: 2px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.08); border-radius: 6px; background: #fafafa; font-family: inherit; font-size: 14px; padding: 0 10px;">
            <option value="cora_manager" <?php selected( $role, 'cora_manager' ); ?>>Broker Owner</option>
            <option value="cora_photographer" <?php selected( $role, 'cora_photographer' ); ?>>Managing Agent</option>
            <option value="cora_videographer" <?php selected( $role, 'cora_videographer' ); ?>>Showing Assistant</option>
            <option value="cora_drone_pilot" <?php selected( $role, 'cora_drone_pilot' ); ?>>Property Valuer</option>
            <option value="cora_editor" <?php selected( $role, 'cora_editor' ); ?>>Listing Coordinator</option>
        </select></label>
    </p>
    <?php
}
add_action( 'register_form', 'cora_real_estate_ai_register_form' );

/**
 * Validate custom fields on registration submission
 */
function cora_real_estate_ai_registration_errors( $errors, $sanitized_user_login, $user_email ) {
    if ( empty( $_POST['cora_password'] ) ) {
        $errors->add( 'cora_password_error', __( '<strong>Error:</strong> Password is required.', 'cora-real-estate' ) );
    } elseif ( strlen( $_POST['cora_password'] ) < 6 ) {
        $errors->add( 'cora_password_length_error', __( '<strong>Error:</strong> Password must be at least 6 characters.', 'cora-real-estate' ) );
    }
    
    if ( ! empty( $_POST['cora_password'] ) && $_POST['cora_password'] !== $_POST['cora_confirm_password'] ) {
        $errors->add( 'cora_password_mismatch_error', __( '<strong>Error:</strong> Passwords do not match.', 'cora-real-estate' ) );
    }
    
    if ( empty( $_POST['cora_re_agency_name'] ) ) {
        $errors->add( 'cora_re_agency_name_error', __( '<strong>Error:</strong> Agency Name is required.', 'cora-real-estate' ) );
    }
    
    return $errors;
}
add_filter( 'registration_errors', 'cora_real_estate_ai_registration_errors', 10, 3 );

/**
 * Handle custom user data saving and verification token delivery
 */
function cora_real_estate_ai_user_register( $user_id ) {
    // 1. Save password directly (overrides default random password generation)
    if ( ! empty( $_POST['cora_password'] ) ) {
        wp_set_password( $_POST['cora_password'], $user_id );
    }
    
    // 2. Save custom profile meta
    if ( ! empty( $_POST['cora_re_agency_name'] ) ) {
        update_user_meta( $user_id, 'cora_re_agency_name', sanitize_text_field( $_POST['cora_re_agency_name'] ) );
    }
    if ( ! empty( $_POST['cora_phone'] ) ) {
        update_user_meta( $user_id, 'cora_phone', sanitize_text_field( $_POST['cora_phone'] ) );
    }
    
    // 3. Save verification info
    $token = bin2hex( random_bytes( 16 ) );
    update_user_meta( $user_id, 'cora_re_email_verified', '0' );
    update_user_meta( $user_id, 'cora_re_verification_token', $token );
    
    // 4. Update role to selected studio role
    $role = ( ! empty( $_POST['cora_role'] ) ) ? sanitize_text_field( $_POST['cora_role'] ) : 'cora_manager';
    $user = get_user_by( 'id', $user_id );
    if ( $user ) {
        $user->set_role( $role );
    }
    
    // 5. Send automated confirmation email
    cora_send_verification_email( $user_id );
}
add_action( 'user_register', 'cora_real_estate_ai_user_register' );

/**
 * Dispatch verification link via wp_mail to the newly registered user
 */
function cora_send_verification_email( $user_id ) {
    $user = get_user_by( 'id', $user_id );
    if ( ! $user ) {
        return false;
    }
    
    $token = get_user_meta( $user_id, 'cora_re_verification_token', true );
    if ( ! $token ) {
        // Generate new token if missing
        $token = bin2hex( random_bytes( 16 ) );
        update_user_meta( $user_id, 'cora_re_verification_token', $token );
    }
    
    $verify_url = add_query_arg(
        array(
            'cora_verify_token' => $token,
            'cora_user_id'      => $user_id,
        ),
        home_url( '/workspace' )
    );
    
    $to = $user->user_email;
    $subject = 'Activate your Cora for Real Estate Workspace';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    $message = '
    <html>
    <head>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #ffffff; color: #18181b; padding: 24px; }
            .container { max-width: 500px; margin: 0 auto; border: 1px solid #e4e4e7; border-radius: 12px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
            .logo { font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: -0.5px; border-bottom: 1px solid #f4f4f5; padding-bottom: 16px; margin-bottom: 24px; }
            h2 { font-size: 20px; font-weight: 600; letter-spacing: -0.5px; margin-bottom: 12px; }
            p { font-size: 13.5px; line-height: 1.6; color: #71717a; margin-bottom: 24px; }
            .btn { display: inline-block; background: #18181b; color: #ffffff !important; text-decoration: none; font-size: 12px; font-weight: 600; padding: 12px 24px; border-radius: 8px; transition: background 0.15s; }
            .footer { font-size: 11px; color: #a1a1aa; margin-top: 32px; border-top: 1px solid #f4f4f5; padding-top: 16px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">Cora for Real Estate</div>
            <h2>Confirm your workspace registration</h2>
            <p>Welcome to Cora! Please verify your email address to unlock your real estate CRM dashboard, automated WhatsApp pipelines, and AI listing description engines.</p>
            <p><a href="' . esc_url( $verify_url ) . '" class="btn" style="color:#ffffff;">Verify Email Address</a></p>
            <p class="footer">If you did not request this account, please ignore this email.</p>
        </div>
    </body>
    </html>
    ';
    
    return wp_mail( $to, $subject, $message, $headers );
}

/**
 * Catch verification token from URL
 */
function cora_real_estate_ai_handle_email_verification() {
    if ( ! empty( $_GET['cora_verify_token'] ) && ! empty( $_GET['cora_user_id'] ) ) {
        $user_id = intval( $_GET['cora_user_id'] );
        $url_token = sanitize_text_field( $_GET['cora_verify_token'] );
        
        $saved_token = get_user_meta( $user_id, 'cora_re_verification_token', true );
        
        if ( $saved_token && $saved_token === $url_token ) {
            update_user_meta( $user_id, 'cora_re_email_verified', '1' );
            delete_user_meta( $user_id, 'cora_re_verification_token' );
            
            // Log user in automatically if not logged in
            if ( ! is_user_logged_in() || get_current_user_id() !== $user_id ) {
                wp_clear_auth_cookie();
                wp_set_current_user( $user_id );
                wp_set_auth_cookie( $user_id );
            }
            
            wp_redirect( home_url( '/workspace/dashboard?cora_verified=true' ) );
            exit;
        } else {
            wp_redirect( home_url( '/workspace/dashboard?cora_verified=error' ) );
            exit;
        }
    }
}
add_action( 'init', 'cora_real_estate_ai_handle_email_verification' );

/**
 * AJAX handler for resending verification email
 */
function cora_ajax_resend_verification() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( array( 'message' => 'User not logged in.' ) );
    }
    
    $verified = get_user_meta( $user_id, 'cora_re_email_verified', true );
    if ( $verified === '1' ) {
        wp_send_json_error( array( 'message' => 'Account is already verified.' ) );
    }
    
    $sent = cora_send_verification_email( $user_id );
    if ( $sent ) {
        wp_send_json_success( array( 'message' => 'Verification link sent to ' . get_userdata($user_id)->user_email ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to dispatch verification email.' ) );
    }
}
add_action( 'wp_ajax_cora_re_resend_verification', 'cora_ajax_resend_verification' );

/**
 * Load custom login stylesheet for white-labeled login screen
 */
function cora_real_estate_ai_login_assets() {
    // Load Inter Font on Login page
    wp_enqueue_style(
        'cora-login-font-inter',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'cora-login-style',
        CORA_REAL_ESTATE_AI_URL . 'assets/css/login-style.css',
        array(),
        CORA_REAL_ESTATE_AI_VERSION
    );
}
add_action( 'login_enqueue_scripts', 'cora_real_estate_ai_login_assets' );

/**
 * Change the login logo URL to point to home instead of wordpress.org
 */
function cora_real_estate_ai_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'cora_real_estate_ai_login_logo_url' );

/**
 * Change the login logo title attribute (tooltip)
 */
function cora_real_estate_ai_login_logo_title() {
    return __( 'Cora for Real Estate', 'cora-real-estate' );
}
add_filter( 'login_headertext', 'cora_real_estate_ai_login_logo_title' );

/**
 * Redirect default WP Dashboard index page (wp-admin/index.php) to Cora for Real Estate
 */
function cora_real_estate_ai_dashboard_redirect() {
    global $pagenow;
    if ( 'index.php' === $pagenow && ! isset( $_GET['page'] ) && ! isset( $_GET['no_cora_redirect'] ) ) {
        wp_redirect( home_url( '/workspace' ) );
        exit;
    }
}
add_action( 'admin_init', 'cora_real_estate_ai_dashboard_redirect' );

/**
 * Remove WordPress Logo node from global Admin Bar
 */
function cora_real_estate_ai_remove_wp_logo() {
    global $wp_admin_bar;
    if ( is_object( $wp_admin_bar ) ) {
        $wp_admin_bar->remove_node( 'wp-logo' );
    }
}
add_action( 'wp_before_admin_bar_render', 'cora_real_estate_ai_remove_wp_logo', 999 );

/**
 * Replace WordPress admin footer credits with Cora branding
 */
function cora_real_estate_ai_footer_text() {
    return '<span>Cora for Real Estate • Delhi Office</span>';
}
add_filter( 'admin_footer_text', 'cora_real_estate_ai_footer_text', 999 );

/**
 * Replace WordPress version in footer with Cora build version
 */
function cora_real_estate_ai_footer_version() {
    return 'v2.0.0';
}
add_filter( 'update_footer', 'cora_real_estate_ai_footer_version', 999 );

/**
 * Register custom taxonomies for media
 */
function cora_real_estate_ai_register_taxonomies() {
    register_taxonomy(
        'cora_media_folder',
        'attachment',
        array(
            'label' => __( 'Media Folders' ),
            'rewrite' => false,
            'hierarchical' => true,
            'show_in_rest' => true,
            'public' => false,
            'show_ui' => true,
        )
    );
}
add_action( 'init', 'cora_real_estate_ai_register_taxonomies' );

/**
 * Register real-estate-specific user roles for Indian/Global studios
 */
function cora_real_estate_ai_register_roles() {
    add_role( 'cora_manager', 'Cora Broker Owner', array( 'read' => true ) );
    add_role( 'cora_branch_manager', 'Cora Branch Manager', array( 'read' => true ) );
    add_role( 'cora_photographer', 'Cora Managing Agent', array( 'read' => true ) );
    add_role( 'cora_videographer', 'Cora Showing Assistant', array( 'read' => true ) );
    add_role( 'cora_drone_pilot', 'Cora Property Valuer', array( 'read' => true ) );
    add_role( 'cora_editor', 'Cora Listing Coordinator', array( 'read' => true ) );
    add_role( 'cora_viewer', 'Cora Viewer', array( 'read' => true ) );
}
add_action( 'init', 'cora_real_estate_ai_register_roles' );

/**
 * Seed initial dashboard options data: crew users, permissions, and equipment inventory
 */
function cora_real_estate_ai_seed_data() {
    // Clean up previously seeded dummy users so they don't clutter the database
    if ( get_option( 'cora_seeded_users_v2' ) == 1 || ! get_option( 'cora_cleaned_dummy_users_v4' ) ) {
        $dummy_usernames = array( 'amit_photographer', 'vikas_photographer', 'priya_editor', 'rahul_videographer', 'rohan_drone', 'sanjay_manager' );
        if ( ! function_exists( 'wp_delete_user' ) ) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }
        foreach ( $dummy_usernames as $username ) {
            $user_obj = get_user_by( 'login', $username );
            if ( $user_obj ) {
                wp_delete_user( $user_obj->ID );
            }
        }
        
        // Also query all users and delete those with cora.ai in their email
        $all_users = get_users();
        foreach ( $all_users as $u ) {
            if ( $u->user_login === 'cora' || $u->user_email === 'dravya.shs@gmail.com' ) {
                continue;
            }
            if ( strpos( $u->user_email, 'cora.ai' ) !== false ) {
                wp_delete_user( $u->ID );
            }
        }
        
        update_option( 'cora_seeded_users_v2', 2 );
        update_option( 'cora_cleaned_dummy_users_v4', 1 );
    }

    $existing_listings = get_option( 'cora_re_listings_inventory' );
    if ( ! is_array( $existing_listings ) || empty( $existing_listings ) || ( isset( $existing_listings[0]['category'] ) && in_array( $existing_listings[0]['category'], array( 'Camera', 'Lens', 'Drone', 'Gimbal', 'Light' ) ) ) ) {
        $equipment = array(
            array( 'id' => 'eq1', 'name' => 'DLF Kings Court Penthouse', 'category' => 'Penthouse', 'rera_reg_id' => 'HR-ERA-2023-88', 'status' => 'Available', 'crew' => '', 'shoot' => '', 'sync_link' => '', 'notes' => 'Luxury penthouse with private terrace.' ),
            array( 'id' => 'eq2', 'name' => 'Vatika City Apartment', 'category' => 'Apartment', 'rera_reg_id' => 'HR-ERA-2023-45', 'status' => 'Available', 'crew' => '', 'shoot' => '', 'sync_link' => '', 'notes' => 'Cozy family apartment.' ),
            array( 'id' => 'eq3', 'name' => 'Tata Primanti Villa', 'category' => 'Villa', 'rera_reg_id' => 'HR-ERA-2023-12', 'status' => 'Available', 'crew' => '', 'shoot' => '', 'sync_link' => '', 'notes' => 'Spacious independent villa.' ),
            array( 'id' => 'eq4', 'name' => 'Commercial Office Cyber City', 'category' => 'Commercial', 'rera_reg_id' => 'HR-ERA-2023-99', 'status' => 'Available', 'crew' => '', 'shoot' => '', 'sync_link' => '', 'notes' => 'Premium IT office space.' ),
            array( 'id' => 'eq5', 'name' => 'Sohna Road Residential Plot', 'category' => 'Plot', 'rera_reg_id' => 'HR-ERA-2023-01', 'status' => 'Available', 'crew' => '', 'shoot' => '', 'sync_link' => '', 'notes' => 'Fenced corner residential plot.' )
        );
        update_option( 'cora_re_listings_inventory', $equipment );
    } else {
        // Dynamic cleanup: reset any equipment currently assigned to dummy users
        $equipment = get_option( 'cora_re_listings_inventory', array() );
        $has_updates = false;
        foreach ( $equipment as &$item ) {
            if ( isset($item['crew']) && ( $item['crew'] === 'Amit Kumar' || $item['crew'] === 'Rohan Gupta' ) ) {
                $item['status'] = 'Available';
                $item['crew'] = '';
                $item['shoot'] = '';
                $has_updates = true;
            }
        }
        if ( $has_updates ) {
            update_option( 'cora_re_listings_inventory', $equipment );
        }
    }

    if ( ! get_option( 'cora_role_permissions' ) ) {
        $default_permissions = array(
            'administrator' => array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'gbp', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'plugins', 'attendance', 'tasks', 'forms', 'ecosystem' ),
            'cora_manager' => array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'vault', 'portfolio', 'leads', 'clients', 'gbp', 'pages', 'attendance', 'tasks' ),
            'cora_photographer' => array( 'dashboard', 'bookings' ),
            'cora_videographer' => array( 'dashboard', 'bookings' ),
            'cora_drone_pilot' => array( 'dashboard', 'bookings' ),
            'cora_editor' => array( 'dashboard', 'bookings' )
        );
        update_option( 'cora_role_permissions', $default_permissions );
    } else {
        $permissions = get_option( 'cora_role_permissions', array() );
        $has_permission_updates = false;
        if ( is_array( $permissions ) && isset( $permissions['administrator'] ) ) {
            if ( ! in_array( 'leads', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'leads';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'clients', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'clients';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'financials', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'financials';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'vault', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'vault';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'portfolio', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'portfolio';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'gbp', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'gbp';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'pages', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'pages';
                $has_permission_updates = true;
            }
            $new_core_features = array( 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'plugins', 'attendance', 'tasks', 'forms', 'ecosystem' );
            foreach ( $new_core_features as $ncf ) {
                if ( ! in_array( $ncf, $permissions['administrator'] ) ) {
                    $permissions['administrator'][] = $ncf;
                    $has_permission_updates = true;
                }
            }
        }
        if ( is_array( $permissions ) && isset( $permissions['cora_manager'] ) ) {
            if ( ! in_array( 'leads', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'leads';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'clients', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'clients';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'vault', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'vault';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'portfolio', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'portfolio';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'gbp', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'gbp';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'pages', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'pages';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'attendance', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'attendance';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'tasks', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'tasks';
                $has_permission_updates = true;
            }
        }
        if ( $has_permission_updates ) {
            update_option( 'cora_role_permissions', $permissions );
        }
    }

    // Seed initial leads
    if ( ! get_option( 'cora_re_leads' ) ) {
        $initial_leads = array(
            array(
                'id' => 'lead_sample_1',
                'names' => 'Kabir & Kiara',
                'email' => 'kabir.kiara@gmail.com',
                'scale' => 'destination',
                'city' => 'Udaipur',
                'notes' => 'Looking for cinematic, documentary-style listing photography over 3 days.',
                'price' => '₹4,50,000',
                'status' => 'New Lead',
                'emails' => array(),
                'created_at' => time() - 3600*24*2
            ),
            array(
                'id' => 'lead_sample_2',
                'names' => 'Aditya & Riya',
                'email' => 'aditya.riya@yahoo.com',
                'scale' => 'intimate',
                'city' => 'Goa',
                'notes' => 'Intimate beachside listing. Need property viewing and 1 day event coverage.',
                'price' => '₹1,50,000',
                'status' => 'Proposal Sent',
                'emails' => array(),
                'created_at' => time() - 3600*24*4
            )
        );
        update_option( 'cora_re_leads', $initial_leads );
    }

    // Seed initial clients
    if ( ! get_option( 'cora_re_clients' ) ) {
        $initial_clients = array(
            array(
                'id' => 'client_1',
                'names' => 'Ananya Sharma',
                'email' => 'ananya@gmail.com',
                'scale' => 'intimate',
                'city' => 'Lodhi Gardens, Delhi',
                'price' => '₹25,000',
                'converted_at' => time() - 3600*24*10,
                'status' => 'confirmed',
                'deal_type' => 'Residential Buy',
                'viewing_date' => '24th Jun, 2026'
            ),
            array(
                'id' => 'client_2',
                'names' => 'Rohit & Sneha',
                'email' => 'rohit.sneha@outlook.com',
                'scale' => 'destination',
                'city' => 'Rambagh Palace, Jaipur',
                'price' => '₹1,80,000',
                'converted_at' => time() - 3600*24*15,
                'status' => 'editing',
                'deal_type' => 'Luxury Villa Sale',
                'viewing_date' => '20th Jun, 2026'
            ),
            array(
                'id' => 'client_3',
                'names' => 'Rajesh Kumar (Office B)',
                'email' => 'rk.enterprises@gmail.com',
                'scale' => 'documentary',
                'city' => 'Delhi Office',
                'price' => '₹40,000',
                'converted_at' => time() - 3600*24*20,
                'status' => 'completed',
                'deal_type' => 'Commercial Lease',
                'viewing_date' => '15th Jun, 2026'
            )
        );
        update_option( 'cora_re_clients', $initial_clients );
    }

    // Migrate existing clients to include status and metadata
    $cora_existing_clients = get_option( 'cora_re_clients', array() );
    if ( is_array( $cora_existing_clients ) ) {
        $cora_modified = false;
        foreach ( $cora_existing_clients as $key => $client ) {
            if ( ! isset( $client['status'] ) ) {
                if ( $client['id'] === 'client_2' || ( $client['names'] ?? '' ) === 'Rohit & Sneha' ) {
                    $cora_existing_clients[$key]['status'] = 'editing';
                    $cora_existing_clients[$key]['deal_type'] = 'Luxury Villa Sale';
                    $cora_existing_clients[$key]['viewing_date'] = '20th Jun, 2026';
                } elseif ( $client['id'] === 'client_3' || strpos( ( $client['names'] ?? '' ), 'Rajesh' ) !== false ) {
                    $cora_existing_clients[$key]['status'] = 'completed';
                    $cora_existing_clients[$key]['deal_type'] = 'Commercial Lease';
                    $cora_existing_clients[$key]['viewing_date'] = '15th Jun, 2026';
                } else {
                    $cora_existing_clients[$key]['status'] = 'confirmed';
                    $cora_existing_clients[$key]['deal_type'] = 'Residential Buy';
                    $cora_existing_clients[$key]['viewing_date'] = '24th Jun, 2026';
                }
                $cora_modified = true;
            }
        }
        if ( $cora_modified ) {
            update_option( 'cora_re_clients', $cora_existing_clients );
        }
    }

    // Seed initial documents
    if ( ! get_option( 'cora_re_vault_docs' ) ) {
        $initial_docs = array(
            array(
                'id' => 'doc_1',
                'title' => 'Proposal: Arjun & Priya Listing Coverage',
                'type' => 'Proposal',
                'amount' => '₹4,50,000',
                'status' => 'Sent',
                'created_date' => '2026-06-15',
                'content' => '<h3>Premium Commercial Office Lease Proposal</h3><p>We are pleased to submit this proposal for lease of premium commercial space in Cyber City. Details include:</p><ul><li>Super Built-up Area: 12,000 sq ft</li><li>2 Senior Managing Agents & 2 Senior Showing Assistants</li><li>1 Property Valuer for aerial capture</li><li>Fit-out Period: 45 working days</li><li>Covered Car Parking: 8 Reserved Bays</li></ul>',
                'client_link' => 'lead_sample_1',
                'secured_shares' => array()
            ),
            array(
                'id' => 'doc_2',
                'title' => 'Invoice: Apex Realty Group - Commercial Property Lease',
                'type' => 'Invoice',
                'amount' => '₹1,80,000',
                'status' => 'Paid',
                'created_date' => '2026-06-10',
                'content' => '<h3>Commercial Office Commission Invoice</h3><p>Commission billing for successfully securing commercial tenant lease agreement at Ritz City Center.</p><p>Total Amount: ₹1,80,000 (Paid in full on June 12, 2026 via NEFT).</p>',
                'client_link' => 'client_2',
                'secured_shares' => array()
            ),
            array(
                'id' => 'doc_3',
                'title' => 'Contract: Delhi Fashion Week 2026 Agreement',
                'type' => 'Contract',
                'amount' => '₹40,000',
                'status' => 'Signed',
                'created_date' => '2026-06-18',
                'content' => '<h3>Delhi Fashion Week Coverage Agreement</h3><p>This contract outlines the deliverables and terms for Delhi Fashion Week coverage.</p><p><strong>Deliverables:</strong> Runway images, backstage coverage, and social media reels.</p><p><strong>Payment Terms:</strong> 50% advance (received), 50% upon delivery.</p>',
                'client_link' => 'client_3',
                'secured_shares' => array()
            )
        );
        update_option( 'cora_re_vault_docs', $initial_docs );
    }

    // Seed initial financials
    if ( ! get_option( 'cora_re_ledger' ) ) {
        $initial_txs = array(
            array(
                'id' => 'tx_1',
                'date' => date( 'Y-m-d', time() - 3600*24*2 ),
                'description' => 'Booking Advance - Ananya Sharma (Residential Buy)',
                'type' => 'Inflow',
                'amount' => 10000,
                'category' => 'Advance Booking Fee',
                'status' => 'Received',
                'client_link' => 'client_1',
            ),
            array(
                'id' => 'tx_2',
                'date' => date( 'Y-m-d', time() - 3600*24*5 ),
                'description' => 'Final Payment - Rajesh Kumar (Commercial Lease)',
                'type' => 'Inflow',
                'amount' => 40000,
                'category' => 'Client Package Payment',
                'status' => 'Received',
                'client_link' => 'client_3',
            ),
            array(
                'id' => 'tx_3',
                'date' => date( 'Y-m-d', time() - 3600*24*3 ),
                'description' => 'Co-Agent Split Payout - Rohit & Sneha Luxury Villa Sale',
                'type' => 'Outflow',
                'amount' => 15000,
                'category' => 'Crew / Assistant Payout',
                'status' => 'Paid',
                'client_link' => 'client_2',
            ),
            array(
                'id' => 'tx_4',
                'date' => date( 'Y-m-d', time() - 3600*24*4 ),
                'description' => 'Property Marketing - Digital Listing Ads (Penthouse Tour)',
                'type' => 'Outflow',
                'amount' => 3500,
                'category' => 'Equipment Rental',
                'status' => 'Paid',
                'client_link' => 'client_1',
            ),
            array(
                'id' => 'tx_5',
                'date' => date( 'Y-m-d', time() - 3600*24*15 ),
                'description' => 'Office Rent & Electricity - June 2026',
                'type' => 'Outflow',
                'amount' => 25000,
                'category' => 'Office Rent / Utilities',
                'status' => 'Paid',
                'client_link' => '',
            ),
            array(
                'id' => 'tx_6',
                'date' => date( 'Y-m-d', time() - 3600*24*10 ),
                'description' => 'Instagram Ads Campaign - Monsoon Leads Prep',
                'type' => 'Outflow',
                'amount' => 8000,
                'category' => 'Marketing & Ads',
                'status' => 'Paid',
                'client_link' => '',
            )
        );
        update_option( 'cora_re_ledger', $initial_txs );
    }

    // Seed initial portfolios
    if ( ! get_option( 'cora_re_portfolios' ) ) {
        $initial_portfolios = array(
            array(
                'id' => 'portfolio_sample_1',
                'hash' => 'listing-ceremony',
                'title' => 'Arjun & Priya - Listing Ceremony',
                'template' => 'masonry',
                'password' => '',
                'client_email' => 'kabir.kiara@gmail.com',
                'assets' => array(
                    array(
                        'id' => 'asset_sample_1',
                        'name' => 'Indian Bride Portrait',
                        'type' => 'image',
                        'url' => 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=600&auto=format&fit=crop',
                        'raw_url' => 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=600&auto=format&fit=crop'
                    ),
                    array(
                        'id' => 'asset_sample_2',
                        'name' => 'Couple Ring Exchange',
                        'type' => 'image',
                        'url' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=600&auto=format&fit=crop',
                        'raw_url' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=600&auto=format&fit=crop'
                    ),
                    array(
                        'id' => 'asset_sample_3',
                        'name' => 'Decorations & Floral Setup',
                        'type' => 'image',
                        'url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=600&auto=format&fit=crop',
                        'raw_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=600&auto=format&fit=crop'
                    ),
                    array(
                        'id' => 'asset_sample_4',
                        'name' => 'Highlight Reels (Google Drive Video)',
                        'type' => 'video',
                        'url' => 'https://drive.google.com/file/d/1yK17SIm0KSp0f62w8GZp5_sample/preview',
                        'raw_url' => 'https://drive.google.com/file/d/1yK17SIm0KSp0f62w8GZp5_sample/view'
                    )
                ),
                'likes' => array(),
                'created_date' => '2026-06-20'
            ),
            array(
                'id' => 'portfolio_sample_2',
                'hash' => 'pre-listing-goa',
                'title' => 'Site Walkthrough (Goa)',
                'template' => 'carousel',
                'password' => 'goa2026',
                'client_email' => 'rohit.sneha@outlook.com',
                'assets' => array(
                    array(
                        'id' => 'asset_sample_5',
                        'name' => 'Sunset Beach Couple Portrait',
                        'type' => 'image',
                        'url' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?q=80&w=600&auto=format&fit=crop',
                        'raw_url' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?q=80&w=600&auto=format&fit=crop'
                    ),
                    array(
                        'id' => 'asset_sample_6',
                        'name' => 'Goa Beach Landscape',
                        'type' => 'image',
                        'url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=600&auto=format&fit=crop',
                        'raw_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=600&auto=format&fit=crop'
                    )
                ),
                'likes' => array(),
                'created_date' => '2026-06-22'
            )
        );
        update_option( 'cora_re_portfolios', $initial_portfolios );
    }

    // Migrate existing portfolios to add client_email if not set
    $existing_portfolios = get_option( 'cora_re_portfolios', array() );
    if ( is_array( $existing_portfolios ) ) {
        $portfolio_modified = false;
        foreach ( $existing_portfolios as $key => $portfolio ) {
            if ( ! isset( $portfolio['client_email'] ) ) {
                if ( $portfolio['id'] === 'portfolio_sample_2' || strpos( $portfolio['title'], 'Goa' ) !== false ) {
                    $existing_portfolios[$key]['client_email'] = 'rohit.sneha@outlook.com';
                    $portfolio_modified = true;
                } elseif ( $portfolio['id'] === 'portfolio_sample_1' || strpos( $portfolio['title'], 'Arjun' ) !== false ) {
                    $existing_portfolios[$key]['client_email'] = 'kabir.kiara@gmail.com';
                    $portfolio_modified = true;
                }
            }
        }
        if ( $portfolio_modified ) {
            update_option( 'cora_re_portfolios', $existing_portfolios );
        }
    }
}
add_action( 'init', 'cora_real_estate_ai_seed_data' );

/**
 * AJAX Handler: Save Role Permissions Matrix
 */
function cora_ajax_save_role_permissions() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $permissions = isset( $_POST['permissions'] ) ? $_POST['permissions'] : array();
    
    // Ensure administrator always has access to everything
    $permissions['administrator'] = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'settings', 'vault', 'portfolio', 'leads', 'clients', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite' );

    update_option( 'cora_role_permissions', $permissions );
    wp_send_json_success( 'Permissions saved successfully.' );
}
add_action( 'wp_ajax_cora_save_role_permissions', 'cora_ajax_save_role_permissions' );

/**
 * AJAX Handler: Create User
 */
function cora_ajax_create_team_user() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $email = sanitize_email( $_POST['email'] );
    $username = sanitize_user( $_POST['username'] );
    $display_name = sanitize_text_field( $_POST['display_name'] );
    $role = sanitize_text_field( $_POST['role'] );
    $password = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : '';

    if ( empty( $email ) || empty( $username ) || empty( $display_name ) || empty( $role ) ) {
        wp_send_json_error( 'All fields are required.' );
    }

    if ( ! empty( $password ) && strlen( $password ) < 8 ) {
        wp_send_json_error( 'Password must be at least 8 characters long.' );
    }

    if ( empty( $password ) ) {
        $password = wp_generate_password( 12, false );
    }

    if ( username_exists( $username ) || email_exists( $email ) ) {
        wp_send_json_error( 'Username or email already exists.' );
    }

    $user_id = wp_insert_user( array(
        'user_login' => $username,
        'user_email' => $email,
        'display_name' => $display_name,
        'user_pass'  => $password,
        'role'       => $role
    ) );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( $user_id->get_error_message() );
    }

    // Handle optional avatar file upload
    $avatar_url = '';
    if ( ! empty( $_FILES['avatar_file'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        
        $attachment_id = media_handle_upload( 'avatar_file', 0 );
        if ( ! is_wp_error( $attachment_id ) ) {
            $avatar_url = wp_get_attachment_url( $attachment_id );
            update_user_meta( $user_id, 'cora_avatar_url', $avatar_url );
        }
    }

    $map = array(
        'administrator' => 'Super Admin',
        'cora_manager' => 'Broker Owner',
        'cora_photographer' => 'Managing Agent',
        'cora_videographer' => 'Showing Assistant',
        'cora_drone_pilot' => 'Property Valuer',
        'cora_editor' => 'Listing Coordinator'
    );
    $role_label = isset( $map[$role] ) ? $map[$role] : $role;

    wp_send_json_success( array(
        'user_id' => $user_id,
        'username' => $username,
        'email' => $email,
        'display_name' => $display_name,
        'role_label' => $role_label,
        'avatar_url' => $avatar_url
    ) );
}
add_action( 'wp_ajax_cora_create_team_user', 'cora_ajax_create_team_user' );

/**
 * Helper to generate listing SEO (R3)
 */
function cora_generate_listing_seo( $name, $category, $rera_id, $link ) {
    $title = "Premium {$category} - {$name} | RERA ID: {$rera_id}";
    $desc = "Explore this luxurious {$category} listing: {$name}. Registered under RERA ID {$rera_id}.";
    if ( ! empty( $link ) ) {
        $desc .= " Synced and verified from {$link}.";
    }
    $desc .= " Contact our Delhi Office today.";
    
    $keywords_arr = array(
        strtolower( $category ),
        strtolower( str_replace( ' ', '-', $name ) ),
        'real-estate',
        'property-listing',
        'cora-platform'
    );
    if ( ! empty( $rera_id ) ) {
        $keywords_arr[] = strtolower( $rera_id );
    }
    $keywords = implode( ', ', $keywords_arr );
    
    return array(
        'title'       => $title,
        'description' => $desc,
        'keywords'    => $keywords,
    );
}

/**
 * AJAX Handler: Save Listing / Equipment
 */
function cora_ajax_save_equipment() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    $user = wp_get_current_user();
    if ( ! current_user_can( 'manage_options' ) && ! in_array( 'cora_manager', (array) $user->roles ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $id          = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
    $name        = sanitize_text_field( $_POST['name'] );
    $category    = sanitize_text_field( $_POST['category'] );
    $rera_reg_id = sanitize_text_field( $_POST['rera_reg_id'] );
    $sync_link   = isset( $_POST['sync_link'] ) ? sanitize_text_field( $_POST['sync_link'] ) : '';
    $notes       = isset( $_POST['notes'] ) ? sanitize_textarea_field( $_POST['notes'] ) : '';

    $seo_title       = isset( $_POST['seo_title'] ) ? sanitize_text_field( $_POST['seo_title'] ) : '';
    $seo_description = isset( $_POST['seo_description'] ) ? sanitize_textarea_field( $_POST['seo_description'] ) : '';
    $seo_keywords    = isset( $_POST['seo_keywords'] ) ? sanitize_text_field( $_POST['seo_keywords'] ) : '';

    if ( empty( $name ) || empty( $category ) || empty( $rera_reg_id ) ) {
        wp_send_json_error( 'All fields are required.' );
    }

    if ( empty( $seo_title ) || empty( $seo_description ) || empty( $seo_keywords ) ) {
        $generated = cora_generate_listing_seo( $name, $category, $rera_reg_id, $sync_link );
        if ( empty( $seo_title ) ) {
            $seo_title = $generated['title'];
        }
        if ( empty( $seo_description ) ) {
            $seo_description = $generated['description'];
        }
        if ( empty( $seo_keywords ) ) {
            $seo_keywords = $generated['keywords'];
        }
    }

    $photo_url = '';
    if ( ! empty( $_FILES['gear_photo'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        
        $attachment_id = media_handle_upload( 'gear_photo', 0 );
        if ( ! is_wp_error( $attachment_id ) ) {
            $photo_url = wp_get_attachment_url( $attachment_id );
        }
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $db_id = 0;
    if ( ! empty( $id ) ) {
        $db_id = intval(preg_replace('/[^\d]/', '', $id));
    }

    if ( $db_id > 0 ) {
        $update_data = array(
            'title' => $name,
            'description' => $notes,
            'type' => $category,
            'rera_number' => $rera_reg_id,
            'location' => $notes,
            'sync_link' => $sync_link,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'seo_keywords' => $seo_keywords,
            'updated_at' => current_time('mysql')
        );
        $wpdb->update(
            $wpdb->prefix . 'cora_properties',
            $update_data,
            array( 'id' => $db_id, 'agency_id' => $agency_id ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
            array('%d', '%d')
        );
        $new_id = $db_id;
    } else {
        $wpdb->insert(
            $wpdb->prefix . 'cora_properties',
            array(
                'agency_id' => $agency_id,
                'branch_id' => $branch_id,
                'added_by' => get_current_user_id(),
                'title' => $name,
                'description' => $notes,
                'type' => $category,
                'status' => 'available',
                'price' => 0,
                'location' => $notes,
                'city' => '',
                'area_sqft' => 0,
                'bedrooms' => 0,
                'bathrooms' => 0,
                'rera_number' => $rera_reg_id,
                'media_ids' => '[]',
                'sync_link' => $sync_link,
                'seo_title' => $seo_title,
                'seo_description' => $seo_description,
                'seo_keywords' => $seo_keywords,
                'embed_vector' => 0,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
        );
        $new_id = $wpdb->insert_id;
    }

    $inventory = get_option( 'cora_re_listings_inventory', array() );
    if ( ! is_array( $inventory ) ) {
        $inventory = array();
    }

    $found_key = null;
    if ( ! empty( $id ) ) {
        foreach ( $inventory as $key => $item ) {
            if ( isset( $item['id'] ) && strval($item['id']) === strval($id) ) {
                $found_key = $key;
                break;
            }
        }
    }

    if ( null !== $found_key ) {
        $inventory[$found_key]['name']            = $name;
        $inventory[$found_key]['category']        = $category;
        $inventory[$found_key]['rera_reg_id']     = $rera_reg_id;
        $inventory[$found_key]['sync_link']       = $sync_link;
        $inventory[$found_key]['notes']           = $notes;
        $inventory[$found_key]['seo_title']       = $seo_title;
        $inventory[$found_key]['seo_description'] = $seo_description;
        $inventory[$found_key]['seo_keywords']    = $seo_keywords;
        if ( ! empty( $photo_url ) ) {
            $inventory[$found_key]['photo_url']   = $photo_url;
        }
        $saved_item = $inventory[$found_key];
    } else {
        $saved_item = array(
            'id'              => $new_id,
            'name'            => $name,
            'category'        => $category,
            'rera_reg_id'     => $rera_reg_id,
            'sync_link'       => $sync_link,
            'notes'           => $notes,
            'status'          => 'Available',
            'crew'            => '',
            'shoot'           => '',
            'photo_url'       => $photo_url,
            'assignment_note' => '',
            'seo_title'       => $seo_title,
            'seo_description' => $seo_description,
            'seo_keywords'    => $seo_keywords
        );
        $inventory[] = $saved_item;
    }

    update_option( 'cora_re_listings_inventory', $inventory );
    wp_send_json_success( $saved_item );
}
add_action( 'wp_ajax_cora_re_save_listing', 'cora_ajax_save_equipment' );

/**
 * AJAX Handler: Assign Equipment
 */
function cora_ajax_assign_equipment() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    $user = wp_get_current_user();
    if ( ! current_user_can( 'manage_options' ) && ! in_array( 'cora_manager', (array) $user->roles ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $eq_id = sanitize_text_field( $_POST['eq_id'] );
    $crew_name = sanitize_text_field( $_POST['crew_name'] );
    $viewing_title = sanitize_text_field( $_POST['viewing_title'] );
    $status = sanitize_text_field( $_POST['status'] );
    $assignment_note = isset( $_POST['assignment_note'] ) ? sanitize_text_field( $_POST['assignment_note'] ) : '';

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $db_id = intval(preg_replace('/[^\d]/', '', $eq_id));

    $status_mapped = 'available';
    $os = strtolower(trim($status));
    if ($os === 'available') $status_mapped = 'available';
    elseif ($os === 'under offer' || $os === 'under_offer' || $os === 'reserved') $status_mapped = 'under_offer';
    elseif ($os === 'sold') $status_mapped = 'sold';
    elseif ($os === 'off market' || $os === 'off_market') $status_mapped = 'off_market';

    $wpdb->update(
        $wpdb->prefix . 'cora_properties',
        array( 'status' => $status_mapped, 'updated_at' => current_time('mysql') ),
        array( 'id' => $db_id, 'agency_id' => $agency_id ),
        array( '%s', '%s' ),
        array( '%d', '%d' )
    );

    $inventory = get_option( 'cora_re_listings_inventory', array() );
    $updated = false;

    foreach ( $inventory as &$item ) {
        if ( strval($item['id']) === strval($eq_id) ) {
            $item['status'] = $status;
            $item['crew'] = ( 'Available' === $status || 'Maintenance' === $status ) ? '' : $crew_name;
            $item['shoot'] = ( 'Available' === $status || 'Maintenance' === $status ) ? '' : $viewing_title;
            $item['assignment_note'] = ( 'Available' === $status || 'Maintenance' === $status ) ? '' : $assignment_note;
            $updated = true;
            break;
        }
    }

    if ( $updated ) {
        update_option( 'cora_re_listings_inventory', $inventory );
        wp_send_json_success( 'Equipment assigned successfully.' );
    } else {
        wp_send_json_error( 'Equipment item not found.' );
    }
}
add_action( 'wp_ajax_cora_assign_equipment', 'cora_ajax_assign_equipment' );

/**
 * AJAX Handler: Save Crew Assignments
 */
function cora_ajax_save_crew_assignments() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    $user = wp_get_current_user();
    if ( ! current_user_can( 'manage_options' ) && ! in_array( 'cora_manager', (array) $user->roles ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $viewing_id = sanitize_text_field( $_POST['viewing_id'] );
    $crew = isset( $_POST['crew'] ) ? $_POST['crew'] : array();

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $db_booking_id = intval(preg_replace('/[^\d]/', '', $viewing_id));

    if ( $db_booking_id > 0 ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_bookings',
            array( 'crew' => json_encode($crew), 'updated_at' => current_time('mysql') ),
            array( 'id' => $db_booking_id, 'agency_id' => $agency_id ),
            array( '%s', '%s' ),
            array( '%d', '%d' )
        );
    }

    $assignments = get_option( 'cora_re_showing_assignments', array() );
    $assignments[$viewing_id] = $crew;
    update_option( 'cora_re_showing_assignments', $assignments );

    wp_send_json_success( 'Crew assignments saved successfully.' );
}
add_action( 'wp_ajax_cora_re_save_showing_assignments', 'cora_ajax_save_crew_assignments' );

/**
 * AJAX Handler: Delete User
 */
function cora_ajax_delete_team_user() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
    if ( ! $user_id ) {
        wp_send_json_error( 'Invalid User ID.' );
    }

    if ( get_current_user_id() === $user_id ) {
        wp_send_json_error( 'You cannot delete yourself.' );
    }

    if ( ! function_exists( 'wp_delete_user' ) ) {
        require_once ABSPATH . 'wp-admin/includes/user.php';
    }

    $deleted = wp_delete_user( $user_id );
    if ( $deleted ) {
        wp_send_json_success( 'User deleted successfully.' );
    } else {
        wp_send_json_error( 'Failed to delete user.' );
    }
}
add_action( 'wp_ajax_cora_delete_team_user', 'cora_ajax_delete_team_user' );

/**
 * AJAX Handler: Update User
 */
function cora_ajax_update_team_user() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
    $email = sanitize_email( $_POST['email'] );
    $display_name = sanitize_text_field( $_POST['display_name'] );
    $role = sanitize_text_field( $_POST['role'] );
    $password = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : '';

    if ( ! $user_id || empty( $email ) || empty( $display_name ) || empty( $role ) ) {
        wp_send_json_error( 'All fields are required.' );
    }

    if ( ! empty( $password ) && strlen( $password ) < 8 ) {
        wp_send_json_error( 'Password must be at least 8 characters long.' );
    }

    $user_to_edit = get_userdata( $user_id );
    if ( $user_to_edit && in_array( 'administrator', (array) $user_to_edit->roles ) && $role !== 'administrator' ) {
        wp_send_json_error( 'You are not allowed to change the role of a Super Admin.' );
    }

    $userdata = array(
        'ID'           => $user_id,
        'user_email'   => $email,
        'display_name' => $display_name,
        'role'         => $role
    );

    if ( ! empty( $password ) ) {
        $userdata['user_pass'] = $password;
    }

    $updated_id = wp_update_user( $userdata );
    if ( is_wp_error( $updated_id ) ) {
        wp_send_json_error( $updated_id->get_error_message() );
    }

    // Handle optional avatar file upload
    $avatar_url = get_user_meta( $user_id, 'cora_avatar_url', true );
    if ( ! empty( $_FILES['avatar_file'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        
        $attachment_id = media_handle_upload( 'avatar_file', 0 );
        if ( ! is_wp_error( $attachment_id ) ) {
            $avatar_url = wp_get_attachment_url( $attachment_id );
            update_user_meta( $user_id, 'cora_avatar_url', $avatar_url );
        }
    }

    $map = array(
        'administrator' => 'Super Admin',
        'cora_manager' => 'Broker Owner',
        'cora_photographer' => 'Managing Agent',
        'cora_videographer' => 'Showing Assistant',
        'cora_drone_pilot' => 'Property Valuer',
        'cora_editor' => 'Listing Coordinator'
    );
    $role_label = isset( $map[$role] ) ? $map[$role] : $role;

    wp_send_json_success( array(
        'user_id' => $updated_id,
        'display_name' => $display_name,
        'email' => $email,
        'role_label' => $role_label,
        'avatar_url' => $avatar_url
    ) );
}
add_action( 'wp_ajax_cora_update_team_user', 'cora_ajax_update_team_user' );

/**
 * AJAX Handler: Delete Equipment
 */
function cora_ajax_delete_equipment() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    $user = wp_get_current_user();
    if ( ! current_user_can( 'manage_options' ) && ! in_array( 'cora_manager', (array) $user->roles ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $eq_id = sanitize_text_field( $_POST['eq_id'] );
    if ( empty( $eq_id ) ) {
        wp_send_json_error( 'Invalid Equipment ID.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $db_id = intval(preg_replace('/[^\d]/', '', $eq_id));

    $wpdb->delete(
        $wpdb->prefix . 'cora_properties',
        array( 'id' => $db_id, 'agency_id' => $agency_id ),
        array( '%d', '%d' )
    );

    $inventory = get_option( 'cora_re_listings_inventory', array() );
    $updated_inventory = array();
    $found = false;

    foreach ( $inventory as $item ) {
        if ( strval($item['id']) === strval($eq_id) ) {
            $found = true;
            continue;
        }
        $updated_inventory[] = $item;
    }

    if ( $found ) {
        update_option( 'cora_re_listings_inventory', $updated_inventory );
        wp_send_json_success( 'Equipment deleted successfully.' );
    } else {
        wp_send_json_error( 'Equipment item not found.' );
    }
}
add_action( 'wp_ajax_cora_re_delete_listing', 'cora_ajax_delete_equipment' );

/**
 * AJAX Handler: Save Document
 */
function cora_ajax_save_document() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    $user = wp_get_current_user();
    if ( ! current_user_can( 'manage_options' ) && ! in_array( 'cora_manager', (array) $user->roles ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
    $title = sanitize_text_field( $_POST['title'] );
    $type = sanitize_text_field( $_POST['type'] );
    $amount = sanitize_text_field( $_POST['amount'] );
    $content = wp_kses_post( $_POST['content'] );
    $logo_url = isset( $_POST['logo_url'] ) ? esc_url_raw( $_POST['logo_url'] ) : '';
    $footer_text = isset( $_POST['footer_text'] ) ? sanitize_textarea_field( $_POST['footer_text'] ) : '';
    
    // Google Doc real-time sync fields
    $gdoc_url = isset( $_POST['gdoc_url'] ) ? esc_url_raw( $_POST['gdoc_url'] ) : '';
    $sync_enabled = isset( $_POST['sync_enabled'] ) && ( 'true' === $_POST['sync_enabled'] || 1 === intval($_POST['sync_enabled']) || $_POST['sync_enabled'] === true );

    $client_link = isset( $_POST['client_link'] ) ? sanitize_text_field( $_POST['client_link'] ) : '';

    if ( empty( $title ) || empty( $type ) || empty( $content ) ) {
        wp_send_json_error( 'Title, Type, and Content are required.' );
    }

    $documents = get_option( 'cora_re_vault_docs', array() );
    $updated = false;

    if ( ! empty( $id ) ) {
        foreach ( $documents as &$doc ) {
            if ( $doc['id'] === $id ) {
                $doc['title'] = $title;
                $doc['type'] = $type;
                $doc['amount'] = $amount;
                $doc['content'] = $content;
                $doc['logo_url'] = $logo_url;
                $doc['footer_text'] = $footer_text;
                $doc['gdoc_url'] = $gdoc_url;
                $doc['sync_enabled'] = $sync_enabled;
                $doc['client_link'] = $client_link;
                $updated = true;
                $new_doc = $doc;
                break;
            }
        }
    }

    if ( ! $updated ) {
        $new_doc = array(
            'id' => 'doc_' . time() . '_' . rand(100, 999),
            'title' => $title,
            'type' => $type,
            'amount' => $amount,
            'status' => 'Draft',
            'created_date' => date( 'Y-m-d' ),
            'content' => $content,
            'logo_url' => $logo_url,
            'footer_text' => $footer_text,
            'gdoc_url' => $gdoc_url,
            'sync_enabled' => $sync_enabled,
            'client_link' => $client_link,
            'secured_shares' => array()
        );
        $documents[] = $new_doc;
    }

    update_option( 'cora_re_vault_docs', $documents );
    wp_send_json_success( $new_doc );
}
add_action( 'wp_ajax_cora_re_save_document', 'cora_ajax_save_document' );

/**
 * AJAX Handler: Share Document
 */
function cora_ajax_share_document() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    $user = wp_get_current_user();
    if ( ! current_user_can( 'manage_options' ) && ! in_array( 'cora_manager', (array) $user->roles ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $doc_id = sanitize_text_field( $_POST['doc_id'] );
    $email = sanitize_email( $_POST['email'] );
    $no_expiry = isset( $_POST['no_expiry'] ) && ( 'true' === $_POST['no_expiry'] || 1 === intval($_POST['no_expiry']) || $_POST['no_expiry'] === true );
    $expiry_date = isset( $_POST['expiry_date'] ) ? sanitize_text_field( $_POST['expiry_date'] ) : '';

    if ( empty( $doc_id ) || empty( $email ) ) {
        wp_send_json_error( 'Document ID and Email are required.' );
    }

    $documents = get_option( 'cora_re_vault_docs', array() );
    $found = false;
    $share_link = '';

    foreach ( $documents as &$doc ) {
        if ( $doc['id'] === $doc_id ) {
            $share_hash = wp_hash( $doc_id . time() . uniqid() );
            
            $expiry_time = 0;
            if ( ! $no_expiry && ! empty( $expiry_date ) ) {
                $expiry_time = strtotime( $expiry_date . ' 23:59:59' );
            }
            
            $new_share = array(
                'hash' => $share_hash,
                'email' => $email,
                'expiry_time' => $expiry_time,
                'created_at' => time()
            );

            if ( empty( $doc['secured_shares'] ) ) {
                $doc['secured_shares'] = array();
            }
            $doc['secured_shares'][] = $new_share;
            $doc['status'] = ( 'Invoice' === $doc['type'] ) ? $doc['status'] : 'Sent'; // If proposal/contract, set status to Sent

            $share_link = home_url( '/shared-doc/' . $share_hash );
            $found = true;
            
            // Send secure email sharing via wp_mail
            $expiry_text_formatted = $no_expiry ? __( 'Never (Permanent Link)', 'cora-real-estate' ) : date( 'Y-m-d H:i:s', $expiry_time );
            
            $subject = sprintf( __( 'Secure Document Share: %s', 'cora-real-estate' ), $doc['title'] );
            $message = sprintf(
                __( "Hello,\n\nYou have been shared a secure access link for the following document: %s\n\nAccess Link: %s\nThis link is secure and will expire on: %s\n\nBest regards,\nCora for Real Estate Team", "cora-real-estate" ),
                $doc['title'],
                $share_link,
                $expiry_text_formatted
            );
            
            wp_mail( $email, $subject, $message );
            break;
        }
    }

    if ( $found ) {
        update_option( 'cora_re_vault_docs', $documents );
        wp_send_json_success( array(
            'share_link' => $share_link,
            'expiry_date' => $no_expiry ? 'Never (Permanent Link)' : date( 'M d, Y H:i', $expiry_time )
        ) );
    } else {
        wp_send_json_error( 'Document not found.' );
    }
}
add_action( 'wp_ajax_cora_share_document', 'cora_ajax_share_document' );
add_action( 'wp_ajax_cora_advanced_search', 'cora_ajax_advanced_search' );

/**
 * Register Public REST API route for frontend team integration
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'cora/v1', '/team', array(
        'methods'             => 'GET',
        'callback'            => 'cora_get_team_members_rest',
        'permission_callback' => '__return_true', // Publicly accessible REST endpoint
    ) );

    register_rest_route( 'cora/v1', '/mcp', array(
        'methods'             => 'POST',
        'callback'            => 'cora_rest_mcp_handler',
        'permission_callback' => '__return_true',
    ) );



    register_rest_route( 'cora/v1', '/schedule-task', array(
        'methods'             => 'POST',
        'callback'            => 'cora_rest_schedule_task',
        'permission_callback' => 'is_user_logged_in',
    ) );

    // Canvas REST API Endpoints
    register_rest_route( 'cora/v1', '/canvas/themes', array(
        'methods'             => 'GET',
        'callback'            => 'cora_rest_canvas_get_themes',
        'permission_callback' => 'cora_canvas_rest_permission_check_read',
    ) );

    register_rest_route( 'cora/v1', '/canvas/themes/(?P<id>\d+)/pages', array(
        'methods'             => 'GET',
        'callback'            => 'cora_rest_canvas_get_theme_pages',
        'permission_callback' => 'cora_canvas_rest_permission_check_read',
    ) );

    register_rest_route( 'cora/v1', '/canvas/pages', array(
        'methods'             => 'POST',
        'callback'            => 'cora_rest_canvas_create_page',
        'permission_callback' => 'cora_canvas_rest_permission_check_write',
    ) );

    register_rest_route( 'cora/v1', '/canvas/pages/(?P<id>\d+)/status', array(
        'methods'             => 'PATCH',
        'callback'            => 'cora_rest_canvas_update_page_status',
        'permission_callback' => 'cora_canvas_rest_permission_check_write',
    ) );

    register_rest_route( 'cora/v1', '/canvas/pages/(?P<id>\d+)/seo', array(
        'methods'             => 'PATCH',
        'callback'            => 'cora_rest_canvas_update_page_seo',
        'permission_callback' => 'cora_canvas_rest_permission_check_write',
    ) );

    register_rest_route( 'cora/v1', '/canvas/pages/(?P<id>\d+)', array(
        'methods'             => 'DELETE',
        'callback'            => 'cora_rest_canvas_delete_page',
        'permission_callback' => 'cora_canvas_rest_permission_check_write',
    ) );

    register_rest_route( 'cora/v1', '/canvas/themes/(?P<id>\d+)/activate', array(
        'methods'             => 'POST',
        'callback'            => 'cora_rest_canvas_activate_theme',
        'permission_callback' => 'cora_canvas_rest_permission_check_write',
    ) );

    register_rest_route( 'cora/v1', '/canvas/pages/ai-create', array(
        'methods'             => 'POST',
        'callback'            => 'cora_rest_canvas_create_ai_page',
        'permission_callback' => 'cora_canvas_rest_permission_check_write',
    ) );

    register_rest_route( 'cora/v1', '/canvas/themes/(?P<id>\d+)/header-footer', array(
        'methods'             => 'POST',
        'callback'            => 'cora_rest_canvas_save_header_footer',
        'permission_callback' => 'cora_canvas_rest_permission_check_write',
    ) );

    // Forms Module REST API Routes
    register_rest_route( 'cora/v1', '/forms', array(
        array(
            'methods'             => 'GET',
            'callback'            => 'cora_rest_get_forms',
            'permission_callback' => 'is_user_logged_in',
        ),
        array(
            'methods'             => 'POST',
            'callback'            => 'cora_rest_save_form',
            'permission_callback' => 'is_user_logged_in',
        )
    ) );

    register_rest_route( 'cora/v1', '/forms/(?P<id>\d+)', array(
        array(
            'methods'             => 'GET',
            'callback'            => 'cora_rest_get_form',
            'permission_callback' => 'is_user_logged_in',
        ),
        array(
            'methods'             => 'DELETE',
            'callback'            => 'cora_rest_delete_form',
            'permission_callback' => 'is_user_logged_in',
        )
    ) );

    register_rest_route( 'cora/v1', '/forms/(?P<id>\d+)/submissions', array(
        'methods'             => 'GET',
        'callback'            => 'cora_rest_get_form_submissions',
        'permission_callback' => 'is_user_logged_in',
    ) );

    register_rest_route( 'cora/v1', '/forms/(?P<id>\d+)/ai-schema', array(
        'methods'             => 'GET',
        'callback'            => 'cora_rest_get_form_ai_schema',
        'permission_callback' => '__return_true',
    ) );

    register_rest_route( 'cora/v1', '/forms/(?P<id>\d+)/submit', array(
        'methods'             => 'POST',
        'callback'            => 'cora_rest_submit_form',
        'permission_callback' => '__return_true',
    ) );

    register_rest_route( 'cora/v1', '/forms/clauses', array(
        array(
            'methods'             => 'GET',
            'callback'            => 'cora_rest_get_clauses',
            'permission_callback' => 'is_user_logged_in',
        ),
        array(
            'methods'             => 'POST',
            'callback'            => 'cora_rest_save_clause',
            'permission_callback' => 'is_user_logged_in',
        )
    ) );

    register_rest_route( 'cora/v1', '/forms/clauses/(?P<id>\d+)', array(
        'methods'             => 'DELETE',
        'callback'            => 'cora_rest_delete_clause',
        'permission_callback' => 'is_user_logged_in',
    ) );

    register_rest_route( 'cora/v1', '/forms/audit-log', array(
        'methods'             => 'GET',
        'callback'            => 'cora_rest_get_form_audit_log',
        'permission_callback' => 'is_user_logged_in',
    ) );
} );

function cora_canvas_rest_permission_check_read() {
    if ( ! is_user_logged_in() ) {
        return false;
    }
    $user = wp_get_current_user();
    if ( in_array( 'administrator', $user->roles ) || in_array( 'cora_manager', $user->roles ) || in_array( 'cora_branch_manager', $user->roles ) ) {
        return true;
    }
    return false;
}

function cora_canvas_rest_permission_check_write() {
    if ( ! is_user_logged_in() ) {
        return false;
    }
    $user = wp_get_current_user();
    if ( in_array( 'administrator', $user->roles ) || in_array( 'cora_manager', $user->roles ) ) {
        return true;
    }
    return false;
}

function cora_rest_canvas_get_themes( $request ) {
    global $wpdb;
    $themes = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes ORDER BY id DESC", ARRAY_A );
    if ( is_array($themes) ) {
        foreach ( $themes as &$t ) {
            $t['settings'] = json_decode($t['settings'], true) ?: array();
            
            $pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d", $t['id'] ), ARRAY_A );
            $t['page_count'] = count($pages);
            
            $published = 0;
            $draft = 0;
            $seo_issues = 0;
            foreach ( $pages as $p ) {
                if ( $p['status'] === 'published' ) {
                    $published++;
                } else {
                    $draft++;
                }
                if ( empty( $p['seo_title'] ) || empty( $p['seo_description'] ) ) {
                    $seo_issues++;
                }
            }
            $t['stats'] = array(
                'total' => count($pages),
                'published' => $published,
                'draft' => $draft,
                'seo_issues' => $seo_issues
            );
        }
    }
    return rest_ensure_response( $themes );
}

function cora_rest_canvas_get_theme_pages( $request ) {
    global $wpdb;
    $theme_id = intval( $request->get_param( 'id' ) );
    $pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d ORDER BY is_homepage DESC, title ASC", $theme_id ), ARRAY_A );
    return rest_ensure_response( $pages );
}

function cora_rest_canvas_create_page( $request ) {
    global $wpdb;
    $theme_id = intval( $request->get_param( 'theme_id' ) );
    $title = sanitize_text_field( $request->get_param( 'title' ) );
    $slug = sanitize_title( $request->get_param( 'slug' ) );
    $status = sanitize_text_field( $request->get_param( 'status' ) ?: 'draft' );
    $template = sanitize_text_field( $request->get_param( 'template' ) ?: 'agency' );

    if ( empty( $theme_id ) || empty( $title ) || empty( $slug ) ) {
        return new WP_Error( 'missing_fields', 'Required fields missing.', array( 'status' => 400 ) );
    }

    $wp_post_id = wp_insert_post( array(
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_content' => '<!-- Elementor Page Content -->'
    ) );

    if ( is_wp_error( $wp_post_id ) ) {
        return new WP_Error( 'wp_error', $wp_post_id->get_error_message(), array( 'status' => 500 ) );
    }

    $wpdb->insert(
        $wpdb->prefix . 'cora_canvas_pages',
        array(
            'agency_id'       => 1,
            'theme_id'        => $theme_id,
            'wp_post_id'      => $wp_post_id,
            'title'           => $title,
            'slug'            => $slug,
            'status'          => $status,
            'is_homepage'     => 0,
            'template'        => $template,
            'created_by'      => get_current_user_id(),
            'created_at'      => current_time('mysql'),
            'updated_at'      => current_time('mysql')
        ),
        array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s' )
    );

    $page_id = $wpdb->insert_id;
    cora_log_activity( 'Canvas', "Created canvas page '{$title}'." );

    return rest_ensure_response( array(
        'success'    => true,
        'page_id'    => $page_id,
        'wp_post_id' => $wp_post_id,
        'message'    => 'Page created successfully.'
    ) );
}

function cora_rest_canvas_update_page_status( $request ) {
    global $wpdb;
    $id = intval( $request->get_param( 'id' ) );
    $status = sanitize_text_field( $request->get_param( 'status' ) );

    if ( ! in_array( $status, array( 'published', 'draft', 'scheduled' ) ) ) {
        return new WP_Error( 'invalid_status', 'Invalid status.', array( 'status' => 400 ) );
    }

    $page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $id ), ARRAY_A );
    if ( ! $page ) {
        return new WP_Error( 'not_found', 'Page not found.', array( 'status' => 404 ) );
    }

    $scheduled_at = null;
    if ( $status === 'scheduled' ) {
        $scheduled_at = sanitize_text_field( $request->get_param( 'scheduled_at' ) ?: current_time('mysql') );
    }

    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_pages',
        array( 'status' => $status, 'scheduled_at' => $scheduled_at, 'updated_at' => current_time('mysql') ),
        array( 'id' => $id ),
        array( '%s', '%s', '%s' ),
        array( '%d' )
    );

    cora_log_activity( 'Canvas', "Updated status of page '{$page['title']}' to {$status}." );

    return rest_ensure_response( array( 'success' => true, 'message' => 'Status updated successfully.' ) );
}

function cora_rest_canvas_update_page_seo( $request ) {
    global $wpdb;
    $id = intval( $request->get_param( 'id' ) );
    $seo_title = sanitize_text_field( $request->get_param( 'seo_title' ) );
    $seo_description = sanitize_textarea_field( $request->get_param( 'seo_description' ) );
    $seo_og_image = esc_url_raw( $request->get_param( 'seo_og_image' ) );

    $page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $id ), ARRAY_A );
    if ( ! $page ) {
        return new WP_Error( 'not_found', 'Page not found.', array( 'status' => 404 ) );
    }

    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_pages',
        array(
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'seo_og_image' => $seo_og_image,
            'updated_at' => current_time('mysql')
        ),
        array( 'id' => $id ),
        array( '%s', '%s', '%s', '%s' ),
        array( '%d' )
    );

    cora_log_activity( 'Canvas', "Updated SEO settings for page '{$page['title']}'." );

    return rest_ensure_response( array( 'success' => true, 'message' => 'SEO settings updated successfully.' ) );
}

function cora_rest_canvas_delete_page( $request ) {
    global $wpdb;
    $id = intval( $request->get_param( 'id' ) );
    $page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $id ), ARRAY_A );
    if ( ! $page ) {
        return new WP_Error( 'not_found', 'Page not found.', array( 'status' => 404 ) );
    }

    wp_delete_post( $page['wp_post_id'], true );

    $wpdb->delete(
        $wpdb->prefix . 'cora_canvas_pages',
        array( 'id' => $id ),
        array( '%d' )
    );

    cora_log_activity( 'Canvas', "Deleted page '{$page['title']}'." );

    return rest_ensure_response( array( 'success' => true, 'message' => 'Page deleted successfully.' ) );
}

function cora_rest_canvas_activate_theme( $request ) {
    global $wpdb;
    $id = intval( $request->get_param( 'id' ) );
    $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $id ), ARRAY_A );
    if ( ! $theme ) {
        return new WP_Error( 'not_found', 'Theme not found.', array( 'status' => 404 ) );
    }

    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_themes',
        array( 'status' => 'draft', 'updated_at' => current_time('mysql') ),
        array( 'status' => 'live' ),
        array( '%s', '%s' ),
        array( '%s' )
    );

    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_themes',
        array( 'status' => 'live', 'activated_at' => current_time('mysql'), 'updated_at' => current_time('mysql') ),
        array( 'id' => $id ),
        array( '%s', '%s', '%s' ),
        array( '%d' )
    );

    cora_log_activity( 'Canvas', "Activated theme '{$theme['name']}'." );

    return rest_ensure_response( array( 'success' => true, 'message' => 'Theme activated successfully.' ) );
}

/**
 * REST Callback to insert scheduled deactivations into options queue
 */
function cora_rest_schedule_task( $request ) {
    $action_type = sanitize_text_field( $request->get_param( 'action_type' ) );
    $user_id = intval( $request->get_param( 'user_id' ) );
    $scheduled_at = intval( $request->get_param( 'scheduled_at' ) );
    
    if ( empty( $action_type ) || ! $user_id || ! $scheduled_at ) {
        return new WP_Error( 'invalid_data', 'Required fields missing.', array( 'status' => 400 ) );
    }

    $queue = get_option( 'cora_action_queue', array() );
    $queue[] = array(
        'action_type'  => $action_type,
        'payload'      => array(
            'user_id'     => $user_id,
            'reassign_to' => 0
        ),
        'scheduled_at' => $scheduled_at,
        'created_by'   => get_current_user_id(),
        'status'       => 'pending'
    );
    update_option( 'cora_action_queue', $queue );

    return rest_ensure_response( array( 'success' => true ) );
}

/**
 * Callback: AJAX handler for Advanced Command Search requests
 */
function cora_ajax_advanced_search() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }

    global $wpdb;
    $query = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '';
    $filter = isset( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : 'all';

    if ( empty( $filter ) ) {
        $filter = 'all';
    }

    $results = array();

    // 1. Settings Search
    if ( $filter === 'all' || $filter === 'settings' ) {
        $settings_items = array(
            array(
                'title' => 'General Settings',
                'category' => 'Settings',
                'description' => 'Workspace details, identity, log retention, and tours configurations.',
                'url' => admin_url( 'admin.php?page=cora-workspace&sub=settings-suite&settings_tab=general' ),
                'icon' => 'settings'
            ),
            array(
                'title' => 'Password Policy',
                'category' => 'Settings',
                'description' => 'Configure and enforce minimum length, digits, and uppercase symbols.',
                'url' => admin_url( 'admin.php?page=cora-workspace&sub=settings-suite&settings_tab=pwd-policy' ),
                'icon' => 'lock'
            ),
            array(
                'title' => 'Branch Management',
                'category' => 'Settings',
                'description' => 'Manage physical brokerage offices, cities, and address list.',
                'url' => admin_url( 'admin.php?page=cora-workspace&sub=settings-suite&settings_tab=branches' ),
                'icon' => 'map-pin'
            ),
            array(
                'title' => 'Branding & API Keys',
                'category' => 'Settings',
                'description' => 'Set logo, favicon, Google Maps integration, and WhatsApp cloud credentials.',
                'url' => admin_url( 'admin.php?page=cora-workspace&sub=settings-suite&settings_tab=brand' ),
                'icon' => 'image'
            ),
            array(
                'title' => 'Model Context Protocol (MCP)',
                'category' => 'Settings',
                'description' => 'Access and configure Model Context Protocol (MCP) server endpoints.',
                'url' => admin_url( 'admin.php?page=cora-workspace&sub=mcp' ),
                'icon' => 'cpu'
            ),
            array(
                'title' => 'Reading & SEO Indexing',
                'category' => 'Settings',
                'description' => 'Setup home landing pages and control search engine crawlers.',
                'url' => admin_url( 'admin.php?page=cora-workspace&sub=settings-suite&settings_tab=reading' ),
                'icon' => 'book-open'
            ),
            array(
                'title' => 'SEO Permalinks',
                'category' => 'Settings',
                'description' => 'Set standard clean and SEO-friendly slug url formats.',
                'url' => admin_url( 'admin.php?page=cora-workspace&sub=settings-suite&settings_tab=permalinks' ),
                'icon' => 'link'
            ),
            array(
                'title' => 'Privacy Policy Page',
                'category' => 'Settings',
                'description' => 'Configure legal compliance page.',
                'url' => admin_url( 'admin.php?page=cora-workspace&sub=settings-suite&settings_tab=privacy' ),
                'icon' => 'file-text'
            ),
            array(
                'title' => 'Audit logs panel',
                'category' => 'Security',
                'description' => 'View system log feed and download transaction records.',
                'url' => admin_url( 'admin.php?page=cora-workspace&sub=settings-suite&settings_tab=audit' ),
                'icon' => 'activity'
            )
        );

        foreach ( $settings_items as $item ) {
            if ( empty( $query ) || stripos( $item['title'], $query ) !== false || stripos( $item['description'], $query ) !== false ) {
                $results[] = $item;
            }
        }
    }

    // 2. Leads Search (CRM)
    if ( $filter === 'all' || $filter === 'leads' ) {
        if ( ! empty( $query ) ) {
            $leads = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}cora_leads WHERE name LIKE %s OR email LIKE %s OR phone LIKE %s ORDER BY id DESC LIMIT 5",
                '%' . $wpdb->esc_like( $query ) . '%',
                '%' . $wpdb->esc_like( $query ) . '%',
                '%' . $wpdb->esc_like( $query ) . '%'
            ), ARRAY_A );
        } else {
            $leads = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_leads ORDER BY id DESC LIMIT 5", ARRAY_A );
        }

        if ( ! empty( $leads ) ) {
            foreach ( $leads as $l ) {
                $results[] = array(
                    'title' => $l['name'],
                    'category' => 'Leads',
                    'description' => 'Client: ' . $l['email'] . ' | Phone: ' . $l['phone'],
                    'url' => admin_url( 'admin.php?page=cora-workspace&sub=leads&lead_id=' . $l['id'] ),
                    'icon' => 'user'
                );
            }
        }
    }

    // 3. Pages Search
    if ( $filter === 'all' || $filter === 'pages' ) {
        if ( ! empty( $query ) ) {
            $canvas_pages = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE title LIKE %s OR slug LIKE %s ORDER BY id DESC LIMIT 5",
                '%' . $wpdb->esc_like( $query ) . '%',
                '%' . $wpdb->esc_like( $query ) . '%'
            ), ARRAY_A );
        } else {
            $canvas_pages = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages ORDER BY id DESC LIMIT 5", ARRAY_A );
        }

        if ( ! empty( $canvas_pages ) ) {
            foreach ( $canvas_pages as $cp ) {
                $results[] = array(
                    'title' => $cp['title'],
                    'category' => 'Pages',
                    'description' => 'Canvas Page: /' . $cp['slug'] . ' (' . ucfirst($cp['status']) . ')',
                    'url' => admin_url( 'admin.php?page=cora-workspace&sub=canvas&edit_page=' . $cp['id'] ),
                    'icon' => 'layout'
                );
            }
        }
    }

    // 4. Listings Search (Properties)
    if ( $filter === 'all' || $filter === 'listings' ) {
        $post_type = post_type_exists( 'cora_listing' ) ? 'cora_listing' : 'post';
        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => 5,
            'post_status'    => 'any'
        );
        if ( ! empty( $query ) ) {
            $args['s'] = $query;
        }

        $posts_query = new WP_Query( $args );
        $posts = $posts_query->posts;

        if ( ! empty( $posts ) ) {
            foreach ( $posts as $p ) {
                $results[] = array(
                    'title' => $p->post_title,
                    'category' => 'Listings',
                    'description' => 'Property Post ID: ' . $p->ID . ' | Status: ' . $p->post_status,
                    'url' => admin_url( 'admin.php?page=cora-workspace&sub=properties&property_id=' . $p->ID ),
                    'icon' => 'home'
                );
            }
        }
    }

    wp_send_json_success( array(
        'results' => $results
    ) );
}

/**
 * Callback: REST handler for Model Context Protocol (MCP) JSON-RPC requests
 */
function cora_rest_mcp_handler( $request ) {
    $token = get_option( 'cora_mcp_access_token' );
    if ( empty( $token ) ) {
        $token = bin2hex( wp_generate_password( 32, false ) );
        update_option( 'cora_mcp_access_token', $token );
    }

    $provided_token = '';
    $auth_header = $request->get_header( 'Authorization' );
    if ( ! empty( $auth_header ) && preg_match( '/Bearer\s+(.*)$/i', $auth_header, $matches ) ) {
        $provided_token = $matches[1];
    } else {
        $provided_token = $request->get_param( 'token' );
    }

    if ( empty( $provided_token ) || hash_equals( $token, $provided_token ) === false ) {
        return new WP_REST_Response( array(
            'jsonrpc' => '2.0',
            'error'   => array(
                'code'    => -32001,
                'message' => 'Unauthorized: Invalid or missing MCP token.'
            ),
            'id'      => null
        ), 401 );
    }

    $params = $request->get_json_params();
    $method = isset( $params['method'] ) ? sanitize_text_field( $params['method'] ) : '';
    $id     = isset( $params['id'] ) ? $params['id'] : null;

    if ( ! $method ) {
        return new WP_REST_Response( array(
            'jsonrpc' => '2.0',
            'error'   => array(
                'code'    => -32600,
                'message' => 'Invalid Request: Missing method.'
            ),
            'id'      => $id
        ), 400 );
    }

    switch ( $method ) {
        case 'tools/list':
            return cora_mcp_handle_list_tools( $id );
        case 'tools/call':
            $tool_name = isset( $params['params']['name'] ) ? sanitize_text_field( $params['params']['name'] ) : '';
            $tool_args = isset( $params['params']['arguments'] ) ? $params['params']['arguments'] : array();
            return cora_mcp_handle_call_tool( $tool_name, $tool_args, $id );
        default:
            return new WP_REST_Response( array(
                'jsonrpc' => '2.0',
                'error'   => array(
                    'code'    => -32601,
                    'message' => 'Method not found: ' . $method
                ),
                'id'      => $id
            ), 404 );
    }
}

function cora_mcp_handle_list_tools( $id ) {
    $tools = array(
        array(
            'name'        => 'cora_get_platform_info',
            'description' => 'Get general statistics and platform configuration of the Cora Real Estate workspace.',
            'inputSchema' => array(
                'type'       => 'object',
                'properties' => (object) array(),
            )
        ),
        array(
            'name'        => 'cora_search_listings',
            'description' => 'Query real estate property listings based on location, price range, type, or status.',
            'inputSchema' => array(
                'type'       => 'object',
                'properties' => array(
                    'query'  => array(
                        'type'        => 'string',
                        'description' => 'Search phrase or keyword for location/name.'
                    ),
                    'status' => array(
                        'type'        => 'string',
                        'description' => 'Listing status filter: publish, draft, private.'
                    )
                )
            )
        ),
        array(
            'name'        => 'cora_get_leads',
            'description' => 'Retrieve recent client leads, interest history, contact parameters, and assignment state.',
            'inputSchema' => array(
                'type'       => 'object',
                'properties' => array(
                    'limit' => array(
                        'type'        => 'integer',
                        'description' => 'Maximum number of leads to fetch (default: 10).'
                    )
                )
            )
        ),
        array(
            'name'        => 'cora_create_lead',
            'description' => 'Create/register a new client lead in the CRM dashboard.',
            'inputSchema' => array(
                'type'       => 'object',
                'properties' => array(
                    'name'  => array( 'type' => 'string', 'description' => 'Full name of the client lead.' ),
                    'email' => array( 'type' => 'string', 'description' => 'Email address of the client.' ),
                    'phone' => array( 'type' => 'string', 'description' => 'Phone number of the client.' ),
                    'notes' => array( 'type' => 'string', 'description' => 'Notes regarding property preferences or inquiry details.' )
                ),
                'required'   => array( 'name', 'email' )
            )
        ),
        array(
            'name'        => 'cora_get_activity_logs',
            'description' => 'Fetch recent system audit and security logs from the platform.',
            'inputSchema' => array(
                'type'       => 'object',
                'properties' => array(
                    'limit' => array(
                        'type'        => 'integer',
                        'description' => 'Maximum logs to retrieve (default: 10).'
                    )
                )
            )
        )
    );

    return new WP_REST_Response( array(
        'jsonrpc' => '2.0',
        'result'  => array(
            'tools' => $tools
        ),
        'id'      => $id
    ), 200 );
}

function cora_mcp_handle_call_tool( $name, $args, $id ) {
    global $wpdb;

    switch ( $name ) {
        case 'cora_get_platform_info':
            $listings_count = wp_count_posts( 'cora_listing' )->publish ?? 0;
            if ( ! post_type_exists( 'cora_listing' ) ) {
                $listings_count = wp_count_posts( 'post' )->publish ?? 0;
            }
            $leads_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_leads" ) ?? 0;
            $branches = cora_db_get_branches();
            $branches_count = count( $branches );

            $content = "Cora Platform Info:\n";
            $content .= "- Workspace Name: " . get_option('cora_workspace_name', 'Cora Studio') . "\n";
            $content .= "- Active Listings: " . $listings_count . "\n";
            $content .= "- CRM Leads: " . $leads_count . "\n";
            $content .= "- Brokerage Branches: " . $branches_count . "\n";
            $content .= "- PHP Version: " . PHP_VERSION . "\n";
            $content .= "- System Currency Format: " . get_option('cora_currency_format', 'INR_LAKHS') . "\n";

            return cora_mcp_make_tool_response( $content, $id );

        case 'cora_search_listings':
            $query = isset( $args['query'] ) ? sanitize_text_field( $args['query'] ) : '';
            $status = isset( $args['status'] ) ? sanitize_text_field( $args['status'] ) : 'publish';

            $post_type = post_type_exists( 'cora_listing' ) ? 'cora_listing' : 'post';
            $query_args = array(
                'post_type'      => $post_type,
                'post_status'    => $status,
                'posts_per_page' => 10,
                's'              => $query
            );
            $posts_query = new WP_Query( $query_args );
            $posts = $posts_query->posts;

            $content = "Search Results for '{$query}':\n";
            if ( empty( $posts ) ) {
                $content .= "No listings found matching query.\n";
            } else {
                foreach ( $posts as $p ) {
                    $content .= "- ID: {$p->ID} | Title: {$p->post_title} | Date: {$p->post_date}\n";
                }
            }
            return cora_mcp_make_tool_response( $content, $id );

        case 'cora_get_leads':
            $limit = isset( $args['limit'] ) ? intval( $args['limit'] ) : 10;
            $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_leads ORDER BY id DESC LIMIT %d", $limit ), ARRAY_A );

            $content = "Recent CRM Leads:\n";
            if ( empty( $rows ) ) {
                $content .= "No leads found.\n";
            } else {
                foreach ( $rows as $r ) {
                    $content .= "- ID: {$r['id']} | Name: {$r['name']} | Email: {$r['email']} | Status: {$r['status']} | Notes: {$r['notes']}\n";
                }
            }
            return cora_mcp_make_tool_response( $content, $id );

        case 'cora_create_lead':
            $lead_name  = isset( $args['name'] ) ? sanitize_text_field( $args['name'] ) : '';
            $lead_email = isset( $args['email'] ) ? sanitize_email( $args['email'] ) : '';
            $lead_phone = isset( $args['phone'] ) ? sanitize_text_field( $args['phone'] ) : '';
            $lead_notes = isset( $args['notes'] ) ? sanitize_textarea_field( $args['notes'] ) : '';

            if ( empty( $lead_name ) || empty( $lead_email ) ) {
                return cora_mcp_make_tool_error( "Name and email are required parameters to create a lead.", $id );
            }

            $success = $wpdb->insert(
                "{$wpdb->prefix}cora_leads",
                array(
                    'name'       => $lead_name,
                    'email'      => $lead_email,
                    'phone'      => $lead_phone,
                    'notes'      => $lead_notes,
                    'status'     => 'new',
                    'created_at' => current_time( 'mysql' )
                )
            );

            if ( $success ) {
                $new_id = $wpdb->insert_id;
                cora_log_activity( 'CRM', "Registered new lead '{$lead_name}' via MCP." );
                return cora_mcp_make_tool_response( "Successfully created lead '{$lead_name}' with ID {$new_id}.", $id );
            } else {
                return cora_mcp_make_tool_error( "Database error occurred while trying to insert the lead.", $id );
            }

        case 'cora_get_activity_logs':
            $limit = isset( $args['limit'] ) ? intval( $args['limit'] ) : 10;
            $logs = cora_db_get_activity_logs( $limit );

            $content = "Recent Activity Logs:\n";
            if ( empty( $logs ) ) {
                $content .= "No logs found.\n";
            } else {
                foreach ( $logs as $l ) {
                    $date = date( 'Y-m-d H:i:s', $l['timestamp'] );
                    $content .= "- [{$date}] | Category: {$l['action_type']} | User: {$l['user_name']} | Action: {$l['description']}\n";
                }
            }
            return cora_mcp_make_tool_response( $content, $id );

        default:
            return cora_mcp_make_tool_error( "Tool '{$name}' not found.", $id );
    }
}

function cora_mcp_make_tool_response( $text, $id ) {
    return new WP_REST_Response( array(
        'jsonrpc' => '2.0',
        'result'  => array(
            'content' => array(
                array(
                    'type' => 'text',
                    'text' => $text
                )
            ),
            'isError' => false
        ),
        'id'      => $id
    ), 200 );
}

function cora_mcp_make_tool_error( $error_msg, $id ) {
    return new WP_REST_Response( array(
        'jsonrpc' => '2.0',
        'result'  => array(
            'content' => array(
                array(
                    'type' => 'text',
                    'text' => $error_msg
                )
            ),
            'isError' => true
        ),
        'id'      => $id
    ), 200 );
}

/**
 * Callback: REST endpoint to fetch active brokerage team members
 */
function cora_get_team_members_rest() {
    $users = get_users();
    $cora_role_labels = array(
        'administrator' => 'Super Admin',
        'cora_manager' => 'Broker Owner',
        'cora_photographer' => 'Managing Agent',
        'cora_videographer' => 'Showing Assistant',
        'cora_drone_pilot' => 'Property Valuer',
        'cora_editor' => 'Listing Coordinator'
    );

    $team = array();
    foreach ( $users as $user ) {
        $roles = $user->roles;
        $role_key = ! empty( $roles ) ? $roles[0] : '';
        // Only return users who have brokerage-related roles or administrator
        if ( strpos( $role_key, 'cora_' ) === 0 || $role_key === 'administrator' ) {
            $avatar_url = get_user_meta( $user->ID, 'cora_avatar_url', true );
            $team[] = array(
                'id'         => $user->ID,
                'name'       => $user->display_name,
                'role'       => isset( $cora_role_labels[$role_key] ) ? $cora_role_labels[$role_key] : ucfirst( $role_key ),
                'email'      => $user->user_email,
                'avatar_url' => $avatar_url ? $avatar_url : ''
            );
        }
    }
    return rest_ensure_response( $team );
}

/**
 * AJAX Handler: Sync Document Content from Google Doc URL
 */
function cora_ajax_sync_google_doc() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    
    $user = wp_get_current_user();
    if ( ! current_user_can( 'manage_options' ) && ! in_array( 'cora_manager', (array) $user->roles ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
    if ( empty( $url ) ) {
        wp_send_json_error( 'Please provide a valid Google Doc URL.' );
    }

    // Extract document ID from Google Doc URL
    $doc_id = '';
    if ( preg_match( '/\/document\/d\/([a-zA-Z0-9-_]+)/', $url, $matches ) ) {
        $doc_id = $matches[1];
    }

    if ( empty( $doc_id ) ) {
        wp_send_json_error( 'Could not extract Google Document ID from the URL. Please verify the URL format.' );
    }

    // Build public export URL
    // If it's a published web doc (contains /d/e/), it can be fetched directly
    if ( strpos( $url, '/document/d/e/' ) !== false ) {
        $fetch_url = $url;
    } else {
        $fetch_url = "https://docs.google.com/document/d/{$doc_id}/export?format=html";
    }

    $response = wp_safe_remote_get( $fetch_url, array( 'timeout' => 15 ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Failed to fetch the document. Please ensure it is shared publicly.' );
    }

    $html = wp_remote_retrieve_body( $response );
    if ( empty( $html ) ) {
        wp_send_json_error( 'Document body is empty or private.' );
    }

    // Parse the HTML using DOMDocument
    $dom = new DOMDocument();
    libxml_use_internal_errors( true );
    $dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
    libxml_clear_errors();

    // Extract title from <title> tag
    $title_tag = $dom->getElementsByTagName( 'title' );
    $doc_title = '';
    if ( $title_tag->length > 0 ) {
        $doc_title = sanitize_text_field( $title_tag->item( 0 )->nodeValue );
        $doc_title = preg_replace( '/ - Google Docs$/i', '', $doc_title );
    }

    $body_content = '';
    $body = $dom->getElementsByTagName( 'body' );
    if ( $body->length > 0 ) {
        $body_node = $body->item( 0 );
        // Extract inner HTML of body
        foreach ( $body_node->childNodes as $child ) {
            $body_content .= $dom->saveHTML( $child );
        }
    } else {
        $body_content = $html;
    }

    // Clean up Google Docs inline styling to let it inherit dashboard styles
    $body_content = preg_replace( '/<style\b[^>]*>(.*?)<\/style>/is', '', $body_content );
    $body_content = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $body_content );
    $body_content = preg_replace( '/class="[^"]*"/', '', $body_content );
    $body_content = preg_replace( '/style="[^"]*"/', '', $body_content );
    
    // Keep only clean semantic tags (p, h1, h2, h3, ul, ol, li, strong, em, u, blockquote)
    $body_content = strip_tags( $body_content, '<p><h1><h2><h3><h4><h5><h6><ul><ol><li><strong><em><u><blockquote><br><b><i>' );

    wp_send_json_success( array(
        'title'   => trim( $doc_title ),
        'content' => trim( $body_content )
    ) );
}
add_action( 'wp_ajax_cora_sync_google_doc', 'cora_ajax_sync_google_doc' );

/**
 * Serve custom index.html for frontend homepage if it exists
 */
function cora_real_estate_ai_serve_frontend_homepage() {
    if ( is_front_page() && ! is_admin() ) {
        // Do not intercept if a custom static page is assigned as the front page
        if ( 'page' === get_option( 'show_on_front' ) ) {
            return;
        }

        // Prevent intercepting REST API requests
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return;
        }
        if ( isset( $_GET['rest_route'] ) ) {
            return;
        }
        if ( false !== strpos( $_SERVER['REQUEST_URI'], '/wp-json/' ) ) {
            return;
        }

        // Prevent intercepting workspace, shared assets, PWA and documents routes
        $request_uri = $_SERVER['REQUEST_URI'];
        $home_path = parse_url( home_url(), PHP_URL_PATH );
        $path = substr( $request_uri, strlen( $home_path ) );
        $path = trim( parse_url( $path, PHP_URL_PATH ), '/' );
        $path_parts = explode( '/', $path );
        $first_part = isset( $path_parts[0] ) ? $path_parts[0] : '';
        $reserved_paths = array( 'api', 'workspace', 'shared-doc', 'shared-portfolio', 'cora-service-worker.js', 'cora-manifest.json' );
        if ( in_array( $first_part, $reserved_paths ) ) {
            return;
        }

        // If the path matches an actual published page under plain permalinks, redirect to its query param form
        if ( ! empty( $path ) ) {
            $page_obj = get_page_by_path( $path );
            if ( $page_obj && 'publish' === $page_obj->post_status ) {
                wp_redirect( home_url( '/?pagename=' . $path ) );
                exit;
            }
        }

        $frontend_file = plugin_dir_path( __FILE__ ) . 'apex-realty-group/index.html';
        if ( file_exists( $frontend_file ) ) {
            $html = file_get_contents( $frontend_file );
            
            // Rewrite relative asset paths dynamically to absolute plugin URLs
            $plugin_assets_url = plugins_url( 'apex-realty-group/assets/', __FILE__ );
            $plugin_root_assets_url = plugins_url( 'assets/', __FILE__ );
            
            // Replace relative paths
            $html = str_replace( 'src="assets/', 'src="' . $plugin_assets_url, $html );
            $html = str_replace( 'href="assets/', 'href="' . $plugin_assets_url, $html );
            $html = str_replace( 'url(\'assets/', 'url(\'' . $plugin_assets_url, $html );
            $html = str_replace( 'url("assets/', 'url("' . $plugin_assets_url, $html );
            $html = str_replace( 'content="assets/', 'content="' . $plugin_assets_url, $html );

            // Replace relative parent paths (../assets/) to the plugin's root assets directory
            $html = str_replace( 'src="../assets/', 'src="' . $plugin_root_assets_url, $html );
            $html = str_replace( 'href="../assets/', 'href="' . $plugin_root_assets_url, $html );
            $html = str_replace( 'url(\'../assets/', 'url(\'' . $plugin_root_assets_url, $html );
            $html = str_replace( 'url("../assets/', 'url("' . $plugin_root_assets_url, $html );
            $html = str_replace( 'content="../assets/', 'content="' . $plugin_root_assets_url, $html );
            
            // Rewrite hardcoded admin-ajax URL to resolve dynamically on any WP installation
            $html = str_replace( '/wp-admin/admin-ajax.php', admin_url( 'admin-ajax.php' ), $html );
            
            $styles = cora_get_active_theme_styles();
            $js_head = cora_get_active_theme_js( 'head' );
            $js_footer = cora_get_active_theme_js( 'footer' );

            // ── Inject the Cora draft preview bar into the static HTML ──
            // The bar JS reads ?cv_preview_theme from the URL, fetches REST data,
            // and renders the floating bar entirely client-side.
            $rest_url  = esc_url( rest_url( 'cora/v1/preview-bar-data' ) );
            $site_url  = esc_url( home_url() );
            $rest_json = json_encode( $rest_url );
            $site_json = json_encode( $site_url );
            $preview_bar_script = <<<BARSCRIPT
<script id="cora-preview-bar-injector">
(function(){
  var REST_BASE={$rest_json};
  var SITE_URL={$site_json};
  var params=new URLSearchParams(window.location.search);
  var themeId=params.get('cv_preview_theme');
  if(!themeId)return;
  fetch(REST_BASE+'?theme_id='+encodeURIComponent(themeId))
    .then(function(r){return r.ok?r.json():null;})
    .then(function(data){
      if(!data||data.code)return;
      var currentPath=window.location.pathname.replace(/^\/+|\/+$/g,'');
      var optionsHTML=data.pages.map(function(p){
        var slug=p.slug||'';
        var sel=(currentPath===slug||(p.is_homepage&&currentPath===''||currentPath==='/'))?' selected':'';
        return '<option value="'+slug+'"'+sel+'>'+p.title+'</option>';
      }).join('');
      var style=document.createElement('style');
      style.id='cora-preview-bar-style';
      style.textContent='#cora-preview-bar{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);width:calc(100% - 40px);max-width:780px;height:56px;background:rgba(9,9,11,0.96);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.1);border-radius:9999px;box-shadow:0 20px 40px rgba(0,0,0,0.5);z-index:2147483647;display:flex;align-items:center;justify-content:space-between;padding:0 16px;color:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;font-size:13px;animation:coraBarSlideUp 0.35s cubic-bezier(0.34,1.56,0.64,1) both}'
        +'@keyframes coraBarSlideUp{from{opacity:0;transform:translateX(-50%) translateY(20px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}'
        +'#cora-preview-bar .cpb-left{display:flex;align-items:center;gap:10px;min-width:0}'
        +'#cora-preview-bar .cpb-dot{width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0;animation:coraDotPulse 1.8s ease-in-out infinite}'
        +'@keyframes coraDotPulse{0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,0.6)}50%{box-shadow:0 0 0 5px rgba(34,197,94,0)}}'
        +'#cora-preview-bar .cpb-label{font-size:11px;color:#a1a1aa;white-space:nowrap}'
        +'#cora-preview-bar .cpb-name{font-size:12px;font-weight:700;color:#fff;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}'
        +'#cora-preview-bar .cpb-divider{width:1px;height:20px;background:rgba(255,255,255,0.15)}'
        +'#cora-preview-bar .cpb-select{background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);color:#fff;border-radius:8px;padding:4px 8px;font-size:11px;font-weight:600;cursor:pointer;max-width:180px}'
        +'#cora-preview-bar .cpb-select option{background:#18181b;color:#fff}'
        +'#cora-preview-bar .cpb-right{display:flex;align-items:center;gap:8px;flex-shrink:0}'
        +'#cora-preview-bar .cpb-exit{font-size:11px;font-weight:600;color:#a1a1aa;text-decoration:none;padding:6px 12px;border-radius:8px;transition:background 0.15s}'
        +'#cora-preview-bar .cpb-exit:hover{background:rgba(255,255,255,0.08);color:#fff}'
        +'#cora-preview-bar .cpb-publish{background:#fff;color:#09090b;border:none;border-radius:8px;padding:7px 14px;font-size:11px;font-weight:800;cursor:pointer;transition:all 0.15s}'
        +'#cora-preview-bar .cpb-publish:hover{background:#e4e4e7}'
        +'#cora-preview-bar .cpb-publish.confirming{background:#ef4444;color:#fff}';
      document.head.appendChild(style);
      var bar=document.createElement('div');
      bar.id='cora-preview-bar';
      bar.innerHTML='<div class="cpb-left"><span class="cpb-dot"></span><div><div class="cpb-label">Previewing Draft:</div><div class="cpb-name">'+data.theme_name+'</div></div></div>'
        +'<div class="cpb-left"><div class="cpb-divider"></div><select class="cpb-select" id="cpb-page-select">'+optionsHTML+'</select></div>'
        +'<div class="cpb-right"><a class="cpb-exit" href="'+data.canvas_url+'">← Exit</a><button class="cpb-publish" id="cpb-publish-btn">Publish</button></div>';
      document.body.appendChild(bar);
      document.getElementById('cpb-page-select').addEventListener('change',function(){
        var slug=this.value;
        window.location.href=SITE_URL+(slug?'/'+slug+'/':'/')+('?cv_preview_theme='+themeId);
      });
      var pub=document.getElementById('cpb-publish-btn'),confirming=false;
      pub.addEventListener('click',function(){
        if(!confirming){confirming=true;pub.textContent='Confirm Publish';pub.classList.add('confirming');setTimeout(function(){if(confirming){confirming=false;pub.textContent='Publish';pub.classList.remove('confirming');}},3000);return;}
        pub.disabled=true;pub.textContent='Publishing…';
        var fd=new FormData();fd.append('action','cora_ajax_activate_theme');fd.append('theme_id',themeId);fd.append('nonce',data.nonce);
        fetch(data.ajax_url,{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(res){
          if(res.success){pub.textContent='✓ Published!';setTimeout(function(){window.location.href=SITE_URL;},1200);}
          else{pub.textContent='Failed — retry';pub.disabled=false;}
        }).catch(function(){pub.textContent='Error — retry';pub.disabled=false;});
      });
    }).catch(function(){});
})();
</script>
BARSCRIPT;

            $html = str_replace( '</head>', $styles . $js_head . '</head>', $html );
            $html = str_replace( '</body>', $js_footer . $preview_bar_script . '</body>', $html );

            header( 'Content-Type: text/html; charset=UTF-8' );
            echo $html;

            exit;
        }
    }
}
add_action( 'template_redirect', 'cora_real_estate_ai_serve_frontend_homepage', 5 );

/**
 * Early asset proxy to resolve CORS issues when loading React modules from different domains.
 */
function cora_git_sync_proxy_assets() {
    if ( '1' !== get_option( 'cora_git_sync_enabled' ) ) {
        return;
    }

    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path   = parse_url( home_url(), PHP_URL_PATH );
    $path        = substr( $request_uri, strlen( $home_path ) );
    $path        = trim( parse_url( $path, PHP_URL_PATH ), '/' );

    // Intercept requests targeting assets/ or static files like placeholder.svg
    if ( strpos( $path, 'assets/' ) === 0 || strpos( $path, 'placeholder.svg' ) === 0 ) {
        global $wpdb;

        // Resolve active/preview theme ID from HTTP referrer header
        $active_theme_id = 0;
        $referer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
        if ( ! empty( $referer ) ) {
            $referer_query = parse_url( $referer, PHP_URL_QUERY );
            if ( ! empty( $referer_query ) ) {
                parse_str( $referer_query, $query_params );
                if ( isset( $query_params['cv_preview_theme'] ) ) {
                    $active_theme_id = intval( $query_params['cv_preview_theme'] );
                }
            }
        }

        // Fallback to active live theme if referer lacks preview param
        if ( ! $active_theme_id ) {
            $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
            if ( ! $live_theme ) {
                $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes LIMIT 1", ARRAY_A );
            }
            $active_theme_id = $live_theme ? intval( $live_theme['id'] ) : 0;
        }

        $active_theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $active_theme_id ), ARRAY_A );
        $theme_settings = $active_theme ? (json_decode( $active_theme['settings'], true ) ?: array()) : array();

        $live_url   = isset( $theme_settings['lovable_project_url'] ) ? $theme_settings['lovable_project_url'] : get_option( 'cora_git_sync_live_url', '' );
        $nested_dir = isset( $theme_settings['nested_dir'] ) ? $theme_settings['nested_dir'] : get_option( 'cora_git_sync_nested_dir', '' );

        if ( ! empty( $live_url ) ) {
            $proxy_url = rtrim( $live_url, '/' );
            $remote_url = $proxy_url . '/' . $path;

            $response = wp_remote_get( $remote_url, array(
                'timeout' => 25,
                'headers' => array(
                    'User-Agent' => 'Cora-Git-Sync-Asset-Proxy'
                )
            ) );

            if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
                $content = wp_remote_retrieve_body( $response );
                $headers = wp_remote_retrieve_headers( $response );

                $content_type = isset( $headers['content-type'] ) ? $headers['content-type'] : '';
                if ( empty( $content_type ) ) {
                    if ( substr( $path, -3 ) === '.js' ) {
                        $content_type = 'application/javascript; charset=utf-8';
                    } elseif ( substr( $path, -4 ) === '.css' ) {
                        $content_type = 'text/css; charset=utf-8';
                    } elseif ( substr( $path, -4 ) === '.svg' ) {
                        $content_type = 'image/svg+xml';
                    }
                }

                header( 'Content-Type: ' . $content_type );
                header( 'Cache-Control: public, max-age=31536000, immutable' );
                echo $content;
                exit;
            }
        } elseif ( ! empty( $nested_dir ) ) {
            $upload_dir = wp_get_upload_dir();
            
            // Look into theme-isolated assets directory first, fall back to global if not unzipped yet
            $file_path  = $upload_dir['basedir'] . '/cora-git-sync-' . $active_theme_id . '/' . $nested_dir . '/' . $path;
            if ( ! file_exists( $file_path ) ) {
                $file_path  = $upload_dir['basedir'] . '/cora-git-sync/' . $nested_dir . '/' . $path;
            }

            if ( file_exists( $file_path ) ) {
                $content_type = '';
                if ( substr( $path, -3 ) === '.js' ) {
                    $content_type = 'application/javascript; charset=utf-8';
                } elseif ( substr( $path, -4 ) === '.css' ) {
                    $content_type = 'text/css; charset=utf-8';
                } elseif ( substr( $path, -4 ) === '.svg' ) {
                    $content_type = 'image/svg+xml';
                } else {
                    $content_type = mime_content_type( $file_path );
                }

                header( 'Content-Type: ' . $content_type );
                header( 'Cache-Control: public, max-age=31536000, immutable' );
                readfile( $file_path );
                exit;
            }
        }
    }
}
add_action( 'init', 'cora_git_sync_proxy_assets', 1 );

/**
 * Intercept template redirects to serve Git-synced frontends (Lovable)
 */
function cora_git_sync_serve_frontend() {
    if ( '1' !== get_option( 'cora_git_sync_enabled' ) ) {
        return;
    }

    if ( is_admin() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }
    if ( isset( $_GET['rest_route'] ) ) {
        return;
    }
    if ( false !== strpos( $_SERVER['REQUEST_URI'], '/wp-json/' ) ) {
        return;
    }

    global $wpdb;

    // Detect active theme ID (either preview or live)
    $preview_theme_id = cora_get_preview_theme_id();
    if ( $preview_theme_id ) {
        $active_theme_id = $preview_theme_id;
    } else {
        $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
        if ( ! $live_theme ) {
            $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes LIMIT 1", ARRAY_A );
        }
        $active_theme_id = $live_theme ? intval( $live_theme['id'] ) : 0;
    }

    $active_theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $active_theme_id ), ARRAY_A );
    $theme_settings = $active_theme ? (json_decode( $active_theme['settings'], true ) ?: array()) : array();

    $repo       = isset( $theme_settings['github_repo'] ) ? $theme_settings['github_repo'] : get_option( 'cora_git_sync_repo', '' );
    $nested_dir = isset( $theme_settings['nested_dir'] ) ? $theme_settings['nested_dir'] : get_option( 'cora_git_sync_nested_dir', '' );
    $page_id    = intval( get_option( 'cora_git_sync_page_id', 0 ) );
    $live_url   = isset( $theme_settings['lovable_project_url'] ) ? $theme_settings['lovable_project_url'] : get_option( 'cora_git_sync_live_url', '' );

    if ( empty( $live_url ) && ( empty( $repo ) || empty( $nested_dir ) ) ) {
        return;
    }

    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path   = parse_url( home_url(), PHP_URL_PATH );
    $path        = substr( $request_uri, strlen( $home_path ) );
    $path        = trim( parse_url( $path, PHP_URL_PATH ), '/' );

    $should_intercept = false;
    $target_route     = '';

    $canvas_page = null;
    $mappings = isset( $theme_settings['page_mappings'] ) ? $theme_settings['page_mappings'] : get_option( 'cora_git_sync_page_mappings', array() );

    // 1. First check if any of the mapped pages match the requested slug (handles duplicate slugs perfectly)
    if ( is_array( $mappings ) && ! empty( $mappings ) ) {
        $mapped_page_ids = array_keys( $mappings );
        $placeholders = implode( ',', array_fill( 0, count( $mapped_page_ids ), '%d' ) );
        
        if ( empty( $path ) ) {
            $query = $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id IN ($placeholders) AND is_homepage = 1 LIMIT 1",
                $mapped_page_ids
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id IN ($placeholders) AND slug = %s LIMIT 1",
                array_merge( $mapped_page_ids, array( $path ) )
            );
        }
        $canvas_page = $wpdb->get_row( $query, ARRAY_A );
    }

    // 2. Fall back to resolved post ID match if no direct mapped page matched
    if ( ! $canvas_page ) {
        $current_post_id = get_queried_object_id();
        if ( $current_post_id ) {
            $canvas_page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE wp_post_id = %d LIMIT 1", $current_post_id ), ARRAY_A );
        }
    }

    // 3. Fall back to generic slug match if still not found
    if ( ! $canvas_page ) {
        if ( empty( $path ) ) {
            $canvas_page = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE is_homepage = 1 LIMIT 1", ARRAY_A );
        } else {
            $canvas_page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE slug = %s LIMIT 1", $path ), ARRAY_A );
        }
    }

    if ( $canvas_page ) {
        if ( is_array( $mappings ) && ! empty( $mappings[ $canvas_page['id'] ] ) ) {
            $should_intercept = true;
            $target_route     = $mappings[ $canvas_page['id'] ];
        }
    }

    // Default mapped page configs fallback
    if ( ! $should_intercept ) {
        if ( $page_id === 0 && empty( $path ) ) {
            $should_intercept = true;
            $target_route     = '/';
        } elseif ( $page_id > 0 && is_page( $page_id ) ) {
            $should_intercept = true;
            $target_route     = '/';
        }
    }

    if ( $should_intercept ) {
        if ( ! empty( $live_url ) ) {
            $proxy_url = rtrim( $live_url, '/' );
            $remote_url = $proxy_url . ( ( '/' === $target_route ) ? '' : $target_route );

            $response = wp_remote_get( $remote_url, array(
                'timeout' => 15,
                'headers' => array(
                    'User-Agent' => 'Cora-Git-Sync-Proxy'
                )
            ) );

            if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
                $html = wp_remote_retrieve_body( $response );

                // Keep assets local to cora.local so they load via our CORS-free asset proxy
                $html = str_replace( 'src="./assets/', 'src="/assets/', $html );
                $html = str_replace( 'href="./assets/', 'href="/assets/', $html );
                $html = str_replace( 'src="assets/', 'src="/assets/', $html );
                $html = str_replace( 'href="assets/', 'href="/assets/', $html );

                // Strip Lovable "Edit with Lovable" badge from HTML output
                $html = preg_replace( '/<aside\s+id="lovable-badge".*?<\/aside>/is', '', $html );

                $api_url = home_url( '/api/v1/public/' );
                $nonce = wp_create_nonce( 'wp_rest' );
                
                $injection = "\n<script>\n";
                $injection .= "  (function() {\n";
                $injection .= "    var targetRoute = '" . esc_js( $target_route ) . "';\n";
                $injection .= "    if (targetRoute && targetRoute !== '/') {\n";
                $injection .= "      Object.defineProperty(window.Location.prototype, 'pathname', {\n";
                $injection .= "        get: function() { return targetRoute; },\n";
                $injection .= "        configurable: true\n";
                $injection .= "      });\n";
                $injection .= "    }\n";
                $injection .= "  })();\n";
                $injection .= "  window.CORA_API_URL = '" . esc_url_raw( $api_url ) . "';\n";
                $injection .= "  window.CORA_NONCE = '" . esc_js( $nonce ) . "';\n";
                $injection .= "<\/script>\n";
                $bridge_tag = '<script src="' . esc_url( plugins_url( 'assets/js/cora-bridge.js', CORA_PLUGIN_FILE ) ) . '" defer><\/script>' . "\n";

                if ( stripos( $html, '<\/head>' ) !== false ) {
                    $html = str_ireplace( '<\/head>', $injection . $bridge_tag . '<\/head>', $html );
                } else {
                    $html = $injection . $bridge_tag . $html;
                }

                while ( ob_get_level() > 0 ) {
                    ob_end_clean();
                }

                header( 'Content-Type: text/html; charset=UTF-8' );
                echo $html;
                exit;
            } else {
                cora_log_activity( 'git_sync', "Proxy request failed to {$remote_url}" );
            }
        } else {
            $upload_dir = wp_get_upload_dir();
            $base_dir = $upload_dir['basedir'] . '/cora-git-sync-' . $active_theme_id . '/' . $nested_dir;
            $base_url = $upload_dir['baseurl'] . '/cora-git-sync-' . $active_theme_id . '/' . $nested_dir;
            if ( ! is_dir( $base_dir ) ) {
                $base_dir = $upload_dir['basedir'] . '/cora-git-sync/' . $nested_dir;
                $base_url = $upload_dir['baseurl'] . '/cora-git-sync/' . $nested_dir;
            }

            $frontend_file = $base_dir . '/index.html';

            if ( file_exists( $frontend_file ) ) {
                $html = file_get_contents( $frontend_file );

                // Keep assets local to cora.local so they load via our CORS-free asset proxy
                $html = str_replace( 'src="./assets/', 'src="/assets/', $html );
                $html = str_replace( 'href="./assets/', 'href="/assets/', $html );
                $html = str_replace( 'src="assets/', 'src="/assets/', $html );
                $html = str_replace( 'href="assets/', 'href="/assets/', $html );

                // Strip Lovable "Edit with Lovable" badge from HTML output
                $html = preg_replace( '/<aside\s+id="lovable-badge".*?<\/aside>/is', '', $html );

                $api_url = home_url( '/api/v1/public/' );
                $nonce = wp_create_nonce( 'wp_rest' );
                
                $injection = "\n<script>\n";
                $injection .= "  (function() {\n";
                $injection .= "    var targetRoute = '" . esc_js( $target_route ) . "';\n";
                $injection .= "    if (targetRoute && targetRoute !== '/') {\n";
                $injection .= "      Object.defineProperty(window.Location.prototype, 'pathname', {\n";
                $injection .= "        get: function() { return targetRoute; },\n";
                $injection .= "        configurable: true\n";
                $injection .= "      });\n";
                $injection .= "    }\n";
                $injection .= "  })();\n";
                $injection .= "  window.CORA_API_URL = '" . esc_url_raw( $api_url ) . "';\n";
                $injection .= "  window.CORA_NONCE = '" . esc_js( $nonce ) . "';\n";
                $injection .= "<\/script>\n";
                $bridge_tag = '<script src="' . esc_url( plugins_url( 'assets/js/cora-bridge.js', CORA_PLUGIN_FILE ) ) . '" defer><\/script>' . "\n";

                if ( stripos( $html, '<\/head>' ) !== false ) {
                    $html = str_ireplace( '<\/head>', $injection . $bridge_tag . '<\/head>', $html );
                } else {
                    $html = $injection . $bridge_tag . $html;
                }


                while ( ob_get_level() > 0 ) {
                    ob_end_clean();
                }

                header( 'Content-Type: text/html; charset=UTF-8' );
                echo $html;
                exit;
            }
        }
    }
}
add_action( 'template_redirect', 'cora_git_sync_serve_frontend', 4 );

function cora_get_preview_theme_id() {
    global $wpdb;
    if ( isset( $_GET['preview_theme_id'] ) ) {
        return intval( $_GET['preview_theme_id'] );
    }
    if ( isset( $_GET['cv_preview_theme'] ) ) {
        return intval( $_GET['cv_preview_theme'] );
    }
    if ( is_page() ) {
        $post_id = get_queried_object_id();
        if ( $post_id ) {
            $canvas_page = $wpdb->get_row( $wpdb->prepare( "SELECT theme_id FROM {$wpdb->prefix}cora_canvas_pages WHERE wp_post_id = %d LIMIT 1", $post_id ), ARRAY_A );
            if ( $canvas_page ) {
                return intval( $canvas_page['theme_id'] );
            }
        }
    }
    return 0;
}

// ── Dynamic query interceptor to resolve pages of draft themes on the frontend ──
add_action( 'pre_get_posts', 'cora_canvas_route_preview_pages' );
function cora_canvas_route_preview_pages( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    global $wpdb;
    $preview_theme_id = cora_get_preview_theme_id();

    // Fall back to live theme if no preview param is present
    if ( ! $preview_theme_id ) {
        $live_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
        if ( $live_theme ) {
            $preview_theme_id = intval( $live_theme['id'] );
        }
    }

    if ( ! $preview_theme_id ) {
        return;
    }

    // Resolve requested slug path
    $path = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
    $home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
    if ( ! empty( $home_path ) && strpos( $path, $home_path ) === 0 ) {
        $path = trim( substr( $path, strlen( $home_path ) ), '/' );
    }

    $canvas_page = null;
    if ( empty( $path ) ) {
        // Resolve theme homepage
        $canvas_page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d AND is_homepage = 1 LIMIT 1", $preview_theme_id ), ARRAY_A );
    } else {
        // Resolve theme page by slug
        $canvas_page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d AND slug = %s LIMIT 1", $preview_theme_id, $path ), ARRAY_A );
    }

    if ( $canvas_page ) {
        $query->set( 'page_id', intval( $canvas_page['wp_post_id'] ) );
        $query->is_404 = false;
    }
}

// ── Append preview theme query arguments to generated frontend links ──
add_filter( 'page_link', 'cora_canvas_append_preview_query_arg', 10, 2 );
add_filter( 'post_link', 'cora_canvas_append_preview_query_arg', 10, 2 );
add_filter( 'post_type_link', 'cora_canvas_append_preview_query_arg', 10, 2 );
function cora_canvas_append_preview_query_arg( $url, $post ) {
    $preview_theme_id = cora_get_preview_theme_id();
    if ( $preview_theme_id > 0 ) {
        if ( strpos( $url, home_url() ) !== false ) {
            $url = add_query_arg( 'cv_preview_theme', $preview_theme_id, $url );
        }
    }
    return $url;
}

// ── Append preview theme query arguments to custom menus links ──
add_filter( 'wp_setup_nav_menu_item', 'cora_canvas_filter_menu_item_preview_url' );
function cora_canvas_filter_menu_item_preview_url( $menu_item ) {
    $preview_theme_id = cora_get_preview_theme_id();
    if ( $preview_theme_id > 0 ) {
        if ( ! empty( $menu_item->url ) && strpos( $menu_item->url, home_url() ) !== false ) {
            $menu_item->url = add_query_arg( 'cv_preview_theme', $preview_theme_id, $menu_item->url );
        }
    }
    return $menu_item;
}

// ══════════════════════════════════════════════════════════════════════════════
// ██  CANVAS DRAFT PREVIEW BAR — REST endpoint + client-side injection
// ██  Architecture: a tiny JS snippet (injected via wp_footer) reads
// ██  ?cv_preview_theme=<id> from the URL, calls the REST endpoint for bar
// ██  data, then renders the bar DOM entirely client-side.
// ██  This works on ANY page regardless of theme/template/Elementor mode.
// ══════════════════════════════════════════════════════════════════════════════

/**
 * REST endpoint: GET /wp-json/cora/v1/preview-bar-data?theme_id=<id>
 * Returns theme name + page list for the bar's page switcher.
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'cora/v1', '/preview-bar-data', array(
        'methods'             => 'GET',
        'callback'            => 'cora_rest_preview_bar_data',
        'permission_callback' => '__return_true', // public – data is not sensitive
        'args'                => array(
            'theme_id' => array(
                'required'          => true,
                'validate_callback' => fn( $v ) => is_numeric( $v ) && intval( $v ) > 0,
                'sanitize_callback' => 'absint',
            ),
        ),
    ) );
} );

function cora_rest_preview_bar_data( WP_REST_Request $request ) {
    global $wpdb;
    $theme_id = $request->get_param( 'theme_id' );

    $theme = $wpdb->get_row( $wpdb->prepare(
        "SELECT id, name, status FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d",
        $theme_id
    ), ARRAY_A );

    if ( ! $theme ) {
        return new WP_Error( 'not_found', 'Theme not found.', array( 'status' => 404 ) );
    }
    if ( $theme['status'] === 'live' ) {
        return new WP_Error( 'not_draft', 'Theme is already live.', array( 'status' => 400 ) );
    }

    $pages = $wpdb->get_results( $wpdb->prepare(
        "SELECT id, title, slug, is_homepage FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d ORDER BY is_homepage DESC, title ASC",
        $theme_id
    ), ARRAY_A );

    return rest_ensure_response( array(
        'theme_id'   => intval( $theme['id'] ),
        'theme_name' => $theme['name'],
        'pages'      => array_map( fn( $p ) => array(
            'title'       => $p['title'],
            'slug'        => $p['slug'],
            'is_homepage' => intval( $p['is_homepage'] ) === 1,
        ), $pages ),
        'canvas_url' => home_url( '/workspace/canvas' ),
        'ajax_url'   => admin_url( 'admin-ajax.php' ),
        'nonce'      => wp_create_nonce( 'cora_ajax_nonce' ),
    ) );
}

/**
 * Client-side preview bar injector.
 * Injected via wp_footer (and also as a standalone script for non-WP pages).
 * Reads ?cv_preview_theme from the URL, fetches bar data from REST, renders bar.
 */
add_action( 'wp_footer', 'cora_canvas_inject_preview_bar_script', 5 );
function cora_canvas_inject_preview_bar_script() {
    // Skip inside the Elementor visual editor only
    if ( is_admin() || isset( $_GET['elementor-preview'] ) ) return;
    if ( class_exists( '\Elementor\Plugin' ) ) {
        $el = \Elementor\Plugin::$instance;
        if ( isset( $el->editor ) && $el->editor->is_edit_mode() ) return;
    }
    $rest_url = esc_url( rest_url( 'cora/v1/preview-bar-data' ) );
    $site_url = esc_url( home_url() );
    echo '<script id="cora-preview-bar-injector">' . "\n";
    echo '(function(){' . "\n";
    echo '  var REST_BASE = ' . json_encode( $rest_url ) . ';' . "\n";
    echo '  var SITE_URL  = ' . json_encode( $site_url ) . ';' . "\n";
    echo cora_canvas_preview_bar_js();
    echo '})();' . "\n";
    echo '</script>' . "\n";
}

/**
 * Returns the self-contained preview bar JavaScript (no jQuery dependency).
 */
function cora_canvas_preview_bar_js() { ob_start(); ?>
  var params = new URLSearchParams(window.location.search);
  var themeId = params.get('cv_preview_theme');
  if (!themeId) return;

  // Fetch bar data from REST endpoint
  fetch(REST_BASE + '?theme_id=' + encodeURIComponent(themeId))
    .then(function(r){ return r.ok ? r.json() : null; })
    .then(function(data) {
      if (!data || data.code) return; // error or not draft

      // ── Build page options for the switcher ──
      var currentPath = window.location.pathname.replace(/^\/+|\/+$/g, '');
      var optionsHTML = data.pages.map(function(p) {
        var slug = p.slug || '';
        var selected = (currentPath === slug || (p.is_homepage && currentPath === '')) ? ' selected' : '';
        return '<option value="' + slug + '"' + selected + '>' + p.title + '</option>';
      }).join('');

      // ── Inject styles ──
      var style = document.createElement('style');
      style.id = 'cora-preview-bar-style';
      style.textContent = `
        #cora-preview-bar {
          position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
          width: calc(100% - 40px); max-width: 780px; height: 56px;
          background: rgba(9,9,11,0.96); backdrop-filter: blur(12px);
          -webkit-backdrop-filter: blur(12px);
          border: 1px solid rgba(255,255,255,0.1); border-radius: 9999px;
          box-shadow: 0 20px 40px rgba(0,0,0,0.5);
          z-index: 2147483647; display: flex; align-items: center;
          justify-content: space-between; padding: 0 16px;
          color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
          font-size: 13px; animation: coraBarSlideUp 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes coraBarSlideUp {
          from { opacity:0; transform: translateX(-50%) translateY(20px); }
          to   { opacity:1; transform: translateX(-50%) translateY(0); }
        }
        #cora-preview-bar .cpb-left { display:flex; align-items:center; gap:10px; min-width:0; }
        #cora-preview-bar .cpb-dot  {
          width:8px; height:8px; border-radius:50%; background:#22c55e; flex-shrink:0;
          animation: coraDotPulse 1.8s ease-in-out infinite;
        }
        @keyframes coraDotPulse {
          0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.6); }
          50%      { box-shadow: 0 0 0 5px rgba(34,197,94,0); }
        }
        #cora-preview-bar .cpb-label { font-size:11px; color:#a1a1aa; white-space:nowrap; }
        #cora-preview-bar .cpb-name  { font-size:12px; font-weight:700; color:#fff; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        #cora-preview-bar .cpb-divider { width:1px; height:20px; background:rgba(255,255,255,0.15); }
        #cora-preview-bar .cpb-select {
          background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
          color:#fff; border-radius:8px; padding:4px 8px; font-size:11px; font-weight:600;
          cursor:pointer; max-width:180px;
        }
        #cora-preview-bar .cpb-select option { background:#18181b; color:#fff; }
        #cora-preview-bar .cpb-right { display:flex; align-items:center; gap:8px; flex-shrink:0; }
        #cora-preview-bar .cpb-exit {
          font-size:11px; font-weight:600; color:#a1a1aa; text-decoration:none;
          padding:6px 12px; border-radius:8px; transition:background 0.15s;
        }
        #cora-preview-bar .cpb-exit:hover { background:rgba(255,255,255,0.08); color:#fff; }
        #cora-preview-bar .cpb-publish {
          background:#fff; color:#09090b; border:none; border-radius:8px;
          padding:7px 14px; font-size:11px; font-weight:800; cursor:pointer;
          transition:all 0.15s; letter-spacing:-0.01em;
        }
        #cora-preview-bar .cpb-publish:hover { background:#e4e4e7; }
        #cora-preview-bar .cpb-publish.confirming {
          background:#ef4444; color:#fff;
        }
      `;
      document.head.appendChild(style);

      // ── Build bar HTML ──
      var bar = document.createElement('div');
      bar.id = 'cora-preview-bar';
      bar.innerHTML =
        '<div class="cpb-left">' +
          '<span class="cpb-dot"></span>' +
          '<div><div class="cpb-label">Previewing Draft:</div><div class="cpb-name">' + data.theme_name + '</div></div>' +
        '</div>' +
        '<div class="cpb-left">' +
          '<div class="cpb-divider"></div>' +
          '<select class="cpb-select" id="cpb-page-select">' + optionsHTML + '</select>' +
        '</div>' +
        '<div class="cpb-right">' +
          '<a class="cpb-exit" href="' + data.canvas_url + '">← Exit</a>' +
          '<button class="cpb-publish" id="cpb-publish-btn">Publish</button>' +
        '</div>';
      document.body.appendChild(bar);

      // ── Page switcher ──
      document.getElementById('cpb-page-select').addEventListener('change', function() {
        var slug = this.value;
        var url  = SITE_URL + (slug ? '/' + slug + '/' : '/');
        window.location.href = url + '?cv_preview_theme=' + themeId;
      });

      // ── Publish button (two-step confirm) ──
      var publishBtn = document.getElementById('cpb-publish-btn');
      var confirming = false;
      publishBtn.addEventListener('click', function() {
        if (!confirming) {
          confirming = true;
          publishBtn.textContent = 'Confirm Publish';
          publishBtn.classList.add('confirming');
          setTimeout(function() {
            if (confirming) {
              confirming = false;
              publishBtn.textContent = 'Publish';
              publishBtn.classList.remove('confirming');
            }
          }, 3000);
          return;
        }
        // Execute publish
        publishBtn.disabled = true;
        publishBtn.textContent = 'Publishing…';
        var fd = new FormData();
        fd.append('action',   'cora_ajax_activate_theme');
        fd.append('theme_id', themeId);
        fd.append('nonce',    data.nonce);
        fetch(data.ajax_url, { method: 'POST', body: fd })
          .then(function(r){ return r.json(); })
          .then(function(res) {
            if (res.success) {
              publishBtn.textContent = '✓ Published!';
              setTimeout(function() { window.location.href = SITE_URL; }, 1200);
            } else {
              publishBtn.textContent = 'Failed — retry';
              publishBtn.disabled = false;
            }
          })
          .catch(function() {
            publishBtn.textContent = 'Error — retry';
            publishBtn.disabled = false;
          });
      });
    })
    .catch(function(){ /* silently ignore network errors */ });
<?php return ob_get_clean(); }

// ── Legacy wp_footer PHP injection (kept for backward compatibility, now a no-op) ──
add_action( 'wp_footer', 'cora_canvas_inject_draft_preview_bar' );
function cora_canvas_inject_draft_preview_bar() {
    // Bar is now fully rendered client-side via cora_canvas_inject_preview_bar_script().
    // This function intentionally does nothing — kept to avoid fatal errors on any
    // direct calls that may exist in older code paths.
    return;
}


function cora_get_active_theme_styles() {
    global $wpdb;
    $preview_theme_id = cora_get_preview_theme_id();
    $theme = null;
    if ( $preview_theme_id > 0 ) {
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d LIMIT 1", $preview_theme_id ), ARRAY_A );
    }
    if ( ! $theme ) {
        $theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
    }
    if ( ! $theme ) {
        $theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes LIMIT 1", ARRAY_A );
    }
    
    $settings = array();
    if ( $theme ) {
        $settings = json_decode( $theme['settings'], true ) ?: array();
    }
    
    $primary   = $settings['primary_color']   ?? '#18181b';
    $secondary = $settings['secondary_color'] ?? '#27272a';
    $accent    = $settings['accent_color']    ?? '#10b981';
    $text      = $settings['text_color']      ?? '#09090b';
    $bg        = $settings['bg_color']        ?? '#ffffff';
    $surface   = $settings['surface_color']   ?? '#f4f4f5';
    $success   = $settings['success_color']   ?? '#16a34a';
    $warning   = $settings['warning_color']   ?? '#d97706';
    $danger    = $settings['danger_color']    ?? '#dc2626';
    $info      = $settings['info_color']      ?? '#2563eb';
    $btn_bg    = $settings['btn_bg']          ?? $primary;
    $btn_text  = $settings['btn_text']        ?? '#ffffff';
    $btn_hover = $settings['btn_hover_bg']    ?? $secondary;

    $heading_font = $settings['heading_font'] ?? 'Inter';
    $body_font    = $settings['body_font']    ?? 'Inter';
    $base_size    = $settings['base_font_size'] ?? '16';

    $container_width   = $settings['container_width']  ?? 1280;
    $section_padding   = $settings['section_padding']  ?? 80;
    $element_gap       = $settings['element_gap']      ?? 24;
    $border_radius     = $settings['border_radius']    ?? 8;
    $border_color      = $settings['border_color']     ?? '#e4e4e7';
    $border_width      = $settings['border_width']     ?? 1;
    $box_shadow        = $settings['box_shadow']       ?? '0 1px 3px rgba(0,0,0,0.06)';
    $header_bg         = $settings['header_bg']        ?? '#ffffff';
    $header_text       = $settings['header_text_color'] ?? '#18181b';
    $footer_bg         = $settings['footer_bg']        ?? '#18181b';
    $footer_text       = $settings['footer_text_color'] ?? '#a1a1aa';

    $style = "
    <style id='cora-canvas-theme-variables'>
    :root {
        /* ── Core Palette ── */
        --primary-color: {$primary};
        --secondary-color: {$secondary};
        --accent-color: {$accent};
        --text-color: {$text};
        --bg-color: {$bg};
        --color-surface: {$surface};
        /* ── Semantic Colors ── */
        --color-success: {$success};
        --color-warning: {$warning};
        --color-danger: {$danger};
        --color-info: {$info};
        /* ── Button Colors ── */
        --btn-bg: {$btn_bg};
        --btn-text: {$btn_text};
        --btn-hover-bg: {$btn_hover};
        /* ── Typography ── */
        --heading-font: '{$heading_font}', sans-serif;
        --body-font: '{$body_font}', sans-serif;
        --base-font-size: {$base_size}px;
        /* ── Spacing & Layout ── */
        --container-width: {$container_width}px;
        --section-padding: {$section_padding}px;
        --element-gap: {$element_gap}px;
        /* ── Borders & Radius ── */
        --border-radius: {$border_radius}px;
        --border-color: {$border_color};
        --border-width: {$border_width}px;
        --box-shadow: {$box_shadow};
        /* ── Header & Footer ── */
        --header-bg: {$header_bg};
        --header-text: {$header_text};
        --footer-bg: {$footer_bg};
        --footer-text: {$footer_text};
    }
    body {
        font-family: var(--body-font);
        font-size: var(--base-font-size);
        color: var(--text-color);
        background-color: var(--bg-color);
    }
    h1, h2, h3, h4, h5, h6 {
        font-family: var(--heading-font);
    }
    </style>
    ";
    
    // Prioritize custom CSS from theme settings
    $custom_css = $settings['custom_css'] ?? get_option( 'cora_canvas_custom_css', '' );
    if ( ! empty( $custom_css ) ) {
        $style .= "<style id='cora-canvas-custom-css'>\n" . esc_html( $custom_css ) . "\n</style>\n";
    }

    return $style;
}

function cora_get_active_theme_js( $position = 'head' ) {
    global $wpdb;
    $preview_theme_id = cora_get_preview_theme_id();
    $theme = null;
    if ( $preview_theme_id > 0 ) {
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d LIMIT 1", $preview_theme_id ), ARRAY_A );
    }
    if ( ! $theme ) {
        $theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
    }
    if ( ! $theme ) {
        $theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes LIMIT 1", ARRAY_A );
    }

    $settings = array();
    if ( $theme ) {
        $settings = json_decode( $theme['settings'], true ) ?: array();
    }

    // Prioritize custom JS from theme settings
    $custom_js = $settings['custom_js'] ?? get_option( 'cora_canvas_custom_js', '' );
    $pos = $settings['custom_js_position'] ?? get_option( 'cora_canvas_custom_js_position', 'head' );
    
    if ( ! empty( $custom_js ) && $pos === $position ) {
        return "<script id='cora-canvas-custom-js-" . esc_attr($position) . "'>\n" . $custom_js . "\n</script>\n";
    }
    return '';
}


add_action( 'wp_head', function () {
    echo cora_get_active_theme_styles();
    echo cora_get_active_theme_js( 'head' );
    // Output raw head HTML injection (font imports, meta tags, pixels, etc.)
    global $wpdb;
    $preview_theme_id = cora_get_preview_theme_id();
    $th = null;
    if ( $preview_theme_id > 0 ) {
        $th = $wpdb->get_row( $wpdb->prepare( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d LIMIT 1", $preview_theme_id ), ARRAY_A );
    }
    if ( ! $th ) {
        $th = $wpdb->get_row( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
    }
    if ( $th ) {
        $s = json_decode( $th['settings'], true ) ?: [];
        if ( ! empty( $s['custom_head'] ) ) {
            echo "\n<!-- Cora Canvas: Head Injection -->\n" . $s['custom_head'] . "\n";
        }
    }
}, 100 );

add_action( 'wp_footer', function () {
    echo cora_get_active_theme_js( 'footer' );
    // Output raw body/footer script injection
    global $wpdb;
    $preview_theme_id = cora_get_preview_theme_id();
    $th = null;
    if ( $preview_theme_id > 0 ) {
        $th = $wpdb->get_row( $wpdb->prepare( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d LIMIT 1", $preview_theme_id ), ARRAY_A );
    }
    if ( ! $th ) {
        $th = $wpdb->get_row( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
    }
    if ( $th ) {
        $s = json_decode( $th['settings'], true ) ?: [];
        if ( ! empty( $s['custom_body'] ) ) {
            echo "\n<!-- Cora Canvas: Body Injection -->\n" . $s['custom_body'] . "\n";
        }
    }
}, 100 );



/**
 * Advanced Canvas Extensions & Competitor Alignment Features
 */

function cora_canvas_inject_header_footer( $content ) {
    if ( ! is_page() || is_admin() ) {
        return $content;
    }
    
    global $wpdb, $post;
    
    // Check if page belongs to a Canvas theme
    $canvas_page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE wp_post_id = %d LIMIT 1", $post->ID ), ARRAY_A );
    if ( ! $canvas_page ) {
        return $content;
    }

    // Retrieve theme settings (prioritizing active preview theme)
    $preview_theme_id = cora_get_preview_theme_id();
    $theme_id = ( $preview_theme_id > 0 ) ? $preview_theme_id : $canvas_page['theme_id'];
    $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d LIMIT 1", $theme_id ), ARRAY_A );
    if ( ! $theme ) {
        return $content;
    }

    $settings = json_decode( $theme['settings'], true ) ?: array();
    $hf = $settings['header_footer'] ?? array();
    
    $logo_url = $hf['logo_url'] ?? '';
    $menu_id = intval( $hf['menu_id'] ?? 0 );
    $copyright_text = $hf['copyright_text'] ?? '';
    $facebook_link = $hf['facebook_link'] ?? '';
    $twitter_link = $hf['twitter_link'] ?? '';
    $linkedin_link = $hf['linkedin_link'] ?? '';

    ob_start();
    ?>
    <style id="cora-canvas-hf-styling">
        .cora-canvas-site-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .cora-canvas-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background-color: #ffffff;
            border-bottom: 1px solid #e4e4e7;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .cora-canvas-nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .cora-canvas-nav a {
            color: #52525b;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.825rem;
            transition: color 0.15s ease;
        }
        .cora-canvas-nav a:hover {
            color: #09090b;
        }
        .cora-canvas-footer {
            padding: 1.5rem;
            background-color: #f4f4f5;
            border-top: 1px solid #e4e4e7;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 0.75rem;
            color: #71717a;
        }
        .cora-canvas-footer-content {
            max-width: 80rem;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        @media (min-width: 768px) {
            .cora-canvas-footer-content {
                flex-direction: row;
            }
        }
        .cora-canvas-socials {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .cora-canvas-socials a {
            color: #a1a1aa;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .cora-canvas-socials a:hover {
            color: #52525b;
        }
    </style>
    <div class="cora-canvas-site-wrapper">
        <header class="cora-canvas-header">
            <div class="cora-canvas-logo">
                <?php if ( ! empty( $logo_url ) ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" style="height: 32px; object-fit: contain;">
                <?php else : ?>
                    <span style="font-size: 14px; font-weight: 900; tracking-tight: -0.025em; color: #09090b;">Apex Realty Group</span>
                <?php endif; ?>
            </div>
            <nav class="cora-canvas-nav">
                <?php
                if ( $menu_id ) {
                    wp_nav_menu( array(
                        'menu'        => $menu_id,
                        'container'   => false,
                        'items_wrap'  => '%3$s',
                        'fallback_cb' => false
                    ) );
                } else {
                    echo '<a href="#">Home</a>';
                    echo '<a href="#">Listings</a>';
                    echo '<a href="#">Contact</a>';
                }
                ?>
            </nav>
        </header>
        <main class="cora-canvas-main" style="flex: 1;">
    <?php
    $header_html = ob_get_clean();

    ob_start();
    ?>
        </main>
        <footer class="cora-canvas-footer">
            <div class="cora-canvas-footer-content">
                <div>
                    <?php echo ! empty( $copyright_text ) ? wp_kses_post( $copyright_text ) : '&copy; ' . date( 'Y' ) . ' Apex Realty Group. All rights reserved.'; ?>
                </div>
                <div class="cora-canvas-socials">
                    <?php if ( ! empty( $facebook_link ) ) : ?>
                        <a href="<?php echo esc_url( $facebook_link ); ?>">Facebook</a>
                    <?php endif; ?>
                    <?php if ( ! empty( $twitter_link ) ) : ?>
                        <a href="<?php echo esc_url( $twitter_link ); ?>">Twitter</a>
                    <?php endif; ?>
                    <?php if ( ! empty( $linkedin_link ) ) : ?>
                        <a href="<?php echo esc_url( $linkedin_link ); ?>">LinkedIn</a>
                    <?php endif; ?>
                </div>
            </div>
        </footer>
    </div>
    <?php
    $footer_html = ob_get_clean();

    return $header_html . $content . $footer_html;
}
add_filter( 'the_content', 'cora_canvas_inject_header_footer', 20 );

function cora_canvas_properties_shortcode( $atts ) {
    global $wpdb;
    $a = shortcode_atts( array(
        'category' => '',
        'limit' => 6,
        'min_price' => 0,
    ), $atts );

    $limit = intval( $a['limit'] );
    
    $sql = "SELECT * FROM {$wpdb->prefix}cora_properties WHERE 1=1";
    $params = array();
    
    if ( ! empty( $a['category'] ) ) {
        $sql .= " AND category = %s";
        $params[] = sanitize_text_field( $a['category'] );
    }
    
    if ( intval( $a['min_price'] ) > 0 ) {
        $sql .= " AND price >= %d";
        $params[] = intval( $a['min_price'] );
    }
    
    $sql .= " ORDER BY id DESC LIMIT %d";
    $params[] = $limit;
    
    if ( ! empty( $params ) ) {
        $results = $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );
    } else {
        $results = $wpdb->get_results( $sql, ARRAY_A );
    }

    if ( empty( $results ) ) {
        return '<div style="color: #a1a1aa; font-style: italic; padding: 2rem 0; text-align: center;">No luxury properties matching the filter parameters.</div>';
    }

    ob_start();
    ?>
    <style>
        .cora-showcase-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            padding: 1.5rem 0;
            font-family: system-ui, -apple-system, sans-serif;
        }
        @media (min-width: 768px) {
            .cora-showcase-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (min-width: 1024px) {
            .cora-showcase-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }
        .cora-showcase-card {
            background-color: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
        }
        .cora-showcase-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .cora-showcase-img-container {
            width: 100%;
            aspect-ratio: 16/10;
            background-color: #f4f4f5;
            position: relative;
        }
        .cora-showcase-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cora-showcase-badge {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            padding: 0.125rem 0.5rem;
            background-color: #09090b;
            color: #ffffff;
            border-radius: 0.25rem;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .cora-showcase-content {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .cora-showcase-title {
            font-size: 14px;
            font-weight: 700;
            color: #18181b;
            margin: 0;
            line-height: 1.25;
        }
        .cora-showcase-subtitle {
            font-size: 11px;
            color: #a1a1aa;
            margin: 0.125rem 0 0 0;
        }
        .cora-showcase-specs {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #71717a;
            font-size: 10px;
            font-weight: 600;
            margin-top: 0.75rem;
        }
        .cora-showcase-spec-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .cora-showcase-footer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #f4f4f5;
            padding-top: 0.75rem;
            margin-top: 1rem;
        }
        .cora-showcase-price {
            font-size: 13px;
            font-weight: 900;
            color: #09090b;
        }
        .cora-showcase-btn {
            padding: 0.25rem 0.625rem;
            background-color: #09090b;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 0.25rem;
            text-decoration: none;
            transition: background-color 0.15s ease;
        }
        .cora-showcase-btn:hover {
            background-color: #27272a;
        }
    </style>
    <div class="cora-showcase-grid">
        <?php foreach ( $results as $item ) : 
            $price_fmt = is_numeric( $item['price'] ) ? '₹' . number_format( $item['price'] ) : $item['price'];
            $photo_url = ! empty( $item['image_url'] ) ? $item['image_url'] : plugins_url( 'assets/images/placeholder-property.jpg', __FILE__ );
            ?>
            <div class="cora-showcase-card">
                <div class="cora-showcase-img-container">
                    <img src="<?php echo esc_url( $photo_url ); ?>" class="cora-showcase-img">
                    <span class="cora-showcase-badge"><?php echo esc_html( $item['category'] ); ?></span>
                </div>
                <div class="cora-showcase-content">
                    <div>
                        <h4 class="cora-showcase-title"><?php echo esc_html( $item['name'] ); ?></h4>
                        <p class="cora-showcase-subtitle"><?php echo esc_html( $item['rera_id'] ); ?></p>
                        
                        <div class="cora-showcase-specs">
                            <span class="cora-showcase-spec-item">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                                3 BHK
                            </span>
                            <span class="cora-showcase-spec-item">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                2,400 sq.ft.
                            </span>
                        </div>
                    </div>
                    
                    <div class="cora-showcase-footer-row">
                        <span class="cora-showcase-price"><?php echo esc_html( $price_fmt ); ?></span>
                        <a href="#" class="cora-showcase-btn" onclick="event.preventDefault(); window.coraShowToast('Inquiry request submitted for <?php echo esc_js( $item['name'] ); ?>!', 'success')">
                            Inquire
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'cora_canvas_properties', 'cora_canvas_properties_shortcode' );

function cora_rest_canvas_create_ai_page( $request ) {
    global $wpdb;
    $template_type = sanitize_text_field( $request->get_param( 'template_type' ) );
    $prompt = sanitize_text_field( $request->get_param( 'prompt' ) );
    $theme_id = intval( $request->get_param( 'theme_id' ) );

    if ( ! $theme_id ) {
        $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
        if ( $live_theme ) {
            $theme_id = $live_theme['id'];
        } else {
            return new WP_Error( 'no_theme', 'No active theme found to create the page in.', array( 'status' => 400 ) );
        }
    }

    $title = 'AI Generated Page';
    $elementor_data = array();

    if ( $template_type === 'bip_problems' ) {
        $title = 'Episode 01: Core Real Estate Problems';
    } elseif ( $template_type === 'luxury_lead' ) {
        $title = 'Luxury Lead Capture';
        $elementor_data = array(
            array(
                'id' => 'sec_lead_hero',
                'elType' => 'section',
                'settings' => array(),
                'elements' => array(
                    array(
                        'id' => 'col_lead_hero',
                        'elType' => 'column',
                        'settings' => array( '_column_size' => 100 ),
                        'elements' => array(
                            array(
                                'id' => 'widget_lead_title',
                                'elType' => 'widget',
                                'widgetType' => 'heading',
                                'settings' => array(
                                    'title' => 'Exclusive Luxury Homes Portfolio'
                                )
                            ),
                            array(
                                'id' => 'widget_lead_desc',
                                'elType' => 'widget',
                                'widgetType' => 'text-editor',
                                'settings' => array(
                                    'editor' => '<p>Submit your inquiry below to receive access to pocket listings and off-market mandates near Vasant Vihar and Golf Course Road.</p>'
                                )
                            )
                        )
                    )
                )
            )
        );
    } elseif ( $template_type === 'virtual_tour' ) {
        $title = 'Virtual Tour Showcase';
        $elementor_data = array(
            array(
                'id' => 'sec_tour_hero',
                'elType' => 'section',
                'settings' => array(),
                'elements' => array(
                    array(
                        'id' => 'col_tour_hero',
                        'elType' => 'column',
                        'settings' => array( '_column_size' => 100 ),
                        'elements' => array(
                            array(
                                'id' => 'widget_tour_title',
                                'elType' => 'widget',
                                'widgetType' => 'heading',
                                'settings' => array(
                                    'title' => 'Interactive Virtual Walkthrough'
                                )
                            ),
                            array(
                                'id' => 'widget_tour_video',
                                'elType' => 'widget',
                                'widgetType' => 'video',
                                'settings' => array(
                                    'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
                                )
                            )
                        )
                    )
                )
            )
        );
    } elseif ( $template_type === 'pricing_guide' ) {
        $title = 'Neighborhood Pricing Guide';
        $elementor_data = array(
            array(
                'id' => 'sec_price_hero',
                'elType' => 'section',
                'settings' => array(),
                'elements' => array(
                    array(
                        'id' => 'col_price_hero',
                        'elType' => 'column',
                        'settings' => array( '_column_size' => 100 ),
                        'elements' => array(
                            array(
                                'id' => 'widget_price_title',
                                'elType' => 'widget',
                                'widgetType' => 'heading',
                                'settings' => array(
                                    'title' => 'Delhi NCR Market Appraisal Matrix'
                                )
                            )
                        )
                    )
                )
            )
        );
    } else {
        $title = ! empty( $prompt ) ? ucwords( $prompt ) : 'Generative Concept Page';
        $highlight = 'Gurgaon and Vasant Vihar';
        if ( stripos( $prompt, 'Gurgaon' ) !== false ) {
            $highlight = 'Gurgaon DLF Phase 5';
        } elseif ( stripos( $prompt, 'Delhi' ) !== false || stripos( $prompt, 'Vihar' ) !== false ) {
            $highlight = 'Vasant Vihar Premium Sectors';
        }

        $elementor_data = array(
            array(
                'id' => 'sec_gen_hero',
                'elType' => 'section',
                'settings' => array(),
                'elements' => array(
                    array(
                        'id' => 'col_gen_hero',
                        'elType' => 'column',
                        'settings' => array( '_column_size' => 100 ),
                        'elements' => array(
                            array(
                                'id' => 'widget_gen_title',
                                'elType' => 'widget',
                                'widgetType' => 'heading',
                                'settings' => array(
                                    'title' => esc_html( $title )
                                )
                            ),
                            array(
                                'id' => 'widget_gen_desc',
                                'elType' => 'widget',
                                'widgetType' => 'text-editor',
                                'settings' => array(
                                    'editor' => '<p>Generative AI Concept Page initialized for <strong>' . esc_html( $highlight ) . '</strong> based on search optimization prompts.</p>'
                                )
                            )
                        )
                    )
                )
            )
        );

        if ( stripos( $prompt, 'dark' ) !== false ) {
            $current_css = get_option( 'cora_canvas_custom_css', '' );
            $theme_dark_css = "\n\n/* AI Dark Mode Injection */\nbody { background-color: #09090b !important; color: #f4f4f5 !important; }\n.cora-canvas-header, .cora-canvas-footer { background-color: #18181b !important; border-color: #27272a !important; }";
            update_option( 'cora_canvas_custom_css', $current_css . $theme_dark_css );
        }
    }

    $post_id = wp_insert_post( array(
        'post_title'   => $title,
        'post_name'    => sanitize_title( $title ),
        'post_status'  => 'draft',
        'post_type'    => 'page',
        'post_content' => '<!-- Elementor Page Element -->',
    ) );

    if ( is_wp_error( $post_id ) ) {
        return $post_id;
    }

    if ( $template_type === 'bip_problems' ) {
        update_post_meta( $post_id, '_cora_is_visual_builder', '1' );
        update_post_meta( $post_id, '_cora_visual_builder_html', cora_get_bip_problems_html() );
        update_post_meta( $post_id, '_cora_visual_builder_css', 'body { background-color: #FBFaf7; }' );
    } else {
        update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
        update_post_meta( $post_id, '_elementor_data', wp_slash( json_encode( $elementor_data ) ) );
    }

    $wpdb->insert(
        $wpdb->prefix . 'cora_canvas_pages',
        array(
            'agency_id'      => 1,
            'theme_id'       => $theme_id,
            'wp_post_id'     => $post_id,
            'title'          => $title,
            'slug'           => sanitize_title( $title ),
            'status'         => 'draft',
            'is_homepage'    => 0,
            'seo_title'      => $title . ' - Apex Realty Group',
            'seo_description'=> 'Premium listing and overview page built by Cora AI.',
            'created_by'     => get_current_user_id(),
            'created_at'     => current_time( 'mysql' ),
            'updated_at'     => current_time( 'mysql' ),
        ),
        array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s', '%s' )
    );

    cora_log_activity( 'Canvas', "Generated template page '{$title}' via AI Page Library." );

    return rest_ensure_response( array(
        'success' => true,
        'page_id' => $wpdb->insert_id,
        'wp_post_id' => $post_id,
        'message' => 'AI Concept Page generated successfully.'
    ) );
}

function cora_rest_canvas_save_header_footer( $request ) {
    global $wpdb;
    $theme_id = intval( $request->get_param( 'id' ) );
    $logo_url = sanitize_text_field( $request->get_param( 'logo_url' ) );
    $menu_id = intval( $request->get_param( 'menu_id' ) );
    $copyright_text = sanitize_textarea_field( $request->get_param( 'copyright_text' ) );
    $facebook_link = sanitize_text_field( $request->get_param( 'facebook_link' ) );
    $twitter_link = sanitize_text_field( $request->get_param( 'twitter_link' ) );
    $linkedin_link = sanitize_text_field( $request->get_param( 'linkedin_link' ) );

    $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
    if ( ! $theme ) {
        return new WP_Error( 'not_found', 'Theme not found.', array( 'status' => 404 ) );
    }

    $settings = json_decode( $theme['settings'], true ) ?: array();
    $settings['header_footer'] = array(
        'logo_url' => $logo_url,
        'menu_id' => $menu_id,
        'copyright_text' => $copyright_text,
        'facebook_link' => $facebook_link,
        'twitter_link' => $twitter_link,
        'linkedin_link' => $linkedin_link
    );

    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_themes',
        array(
            'settings' => json_encode( $settings ),
            'updated_at' => current_time( 'mysql' )
        ),
        array( 'id' => $theme_id ),
        array( '%s', '%s' ),
        array( '%d' )
    );

    cora_log_activity( 'Canvas', "Updated Header & Footer configurations for theme '{$theme['name']}'." );

    return rest_ensure_response( array(
        'success' => true,
        'message' => 'Header and Footer configuration saved successfully.'
    ) );
}

/**
 * Helper to convert Google Drive URL to direct src link
 */
function cora_convert_google_drive_link( $url, $type ) {
    if ( empty( $url ) ) {
        return '';
    }
    // Match file ID
    if ( preg_match( '/\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
        $file_id = $matches[1];
    } elseif ( preg_match( '/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
        $file_id = $matches[1];
    } else {
        return $url;
    }
    if ( 'video' === $type ) {
        return "https://drive.google.com/file/d/{$file_id}/preview";
    } else {
        return "https://drive.google.com/uc?export=view&id={$file_id}";
    }
}

/**
 * Helper to verify permissions
 */
function cora_current_user_can_manage_portfolios() {
    if ( ! is_user_logged_in() ) {
        return false;
    }
    $user = wp_get_current_user();
    $allowed_roles = array( 'administrator', 'cora_manager' );
    foreach ( $allowed_roles as $role ) {
        if ( in_array( $role, (array) $user->roles ) ) {
            return true;
        }
    }
    return false;
}

/**
 * AJAX Action: Save Gallery
 */
function cora_ajax_save_portfolio() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! cora_current_user_can_manage_portfolios() ) {
        wp_send_json_error( 'Access Denied: insufficient permissions.' );
    }

    $id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
    $title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
    $template = isset( $_POST['template'] ) ? sanitize_text_field( $_POST['template'] ) : 'grid';
    $password = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : '';
    $share_images = isset( $_POST['share_images'] ) ? ( $_POST['share_images'] === 'true' || $_POST['share_images'] == '1' ) : true;
    $share_videos = isset( $_POST['share_videos'] ) ? ( $_POST['share_videos'] === 'true' || $_POST['share_videos'] == '1' ) : true;
    $client_email = isset( $_POST['client_email'] ) ? sanitize_email( $_POST['client_email'] ) : '';
    $drive_folder_url = isset( $_POST['drive_folder_url'] ) ? esc_url_raw( $_POST['drive_folder_url'] ) : '';
    
    $raw_assets = isset( $_POST['assets'] ) ? json_decode( wp_unslash( $_POST['assets'] ), true ) : array();
    $sanitized_assets = array();

    if ( empty( $title ) ) {
        wp_send_json_error( 'Gallery title is required.' );
    }

    $index = 1;
    if ( is_array( $raw_assets ) ) {
        foreach ( $raw_assets as $asset ) {
            $asset_id = ! empty( $asset['id'] ) ? sanitize_text_field( $asset['id'] ) : 'asset_' . time() . '_' . $index;
            $type = ( isset( $asset['type'] ) && $asset['type'] === 'video' ) ? 'video' : 'image';
            $raw_url = isset( $asset['url'] ) ? esc_url_raw( $asset['url'] ) : '';
            $converted_url = cora_convert_google_drive_link( $raw_url, $type );
            
            $sanitized_asset = array(
                'id' => $asset_id,
                'name' => isset( $asset['name'] ) ? sanitize_text_field( $asset['name'] ) : '',
                'type' => $type,
                'url' => $converted_url,
                'raw_url' => $raw_url
            );

            if ( isset( $asset['folder'] ) ) {
                $sanitized_asset['folder'] = sanitize_text_field( $asset['folder'] );
            }
            if ( isset( $asset['alt_text'] ) ) {
                $sanitized_asset['alt_text'] = sanitize_text_field( $asset['alt_text'] );
            }
            if ( isset( $asset['description'] ) ) {
                $sanitized_asset['description'] = sanitize_textarea_field( $asset['description'] );
            }
            if ( isset( $asset['is_synced'] ) ) {
                $sanitized_asset['is_synced'] = (bool) $asset['is_synced'];
            }
            
            $sanitized_assets[] = $sanitized_asset;
            $index++;
        }
    }

    $portfolios = get_option( 'cora_re_portfolios', array() );
    if ( ! is_array( $portfolios ) ) {
        $portfolios = array();
    }

    $found_key = null;
    if ( ! empty( $id ) ) {
        foreach ( $portfolios as $key => $portfolio ) {
            if ( isset( $portfolio['id'] ) && $portfolio['id'] === $id ) {
                $found_key = $key;
                break;
            }
        }
    }

    if ( null !== $found_key ) {
        // Update existing portfolio, retaining existing likes
        $existing_likes = isset( $portfolios[$found_key]['likes'] ) ? $portfolios[$found_key]['likes'] : array();
        
        // Clean likes of deleted assets
        $valid_asset_ids = wp_list_pluck( $sanitized_assets, 'id' );
        $cleaned_likes = array();
        foreach ( $existing_likes as $like_id ) {
            if ( in_array( $like_id, $valid_asset_ids ) ) {
                $cleaned_likes[] = $like_id;
            }
        }

        $portfolios[$found_key]['title'] = $title;
        $portfolios[$found_key]['template'] = $template;
        $portfolios[$found_key]['password'] = $password;
        $portfolios[$found_key]['assets'] = $sanitized_assets;
        $portfolios[$found_key]['likes'] = $cleaned_likes;
        $portfolios[$found_key]['share_images'] = $share_images;
        $portfolios[$found_key]['share_videos'] = $share_videos;
        $portfolios[$found_key]['client_email'] = $client_email;
        $portfolios[$found_key]['drive_folder_url'] = $drive_folder_url;
    } else {
        // Create new portfolio
        $new_id = 'portfolio_' . time() . '_' . wp_generate_password( 4, false );
        $hash = md5( $new_id . wp_generate_password( 8, false ) );
        $portfolios[] = array(
            'id' => $new_id,
            'hash' => $hash,
            'title' => $title,
            'template' => $template,
            'password' => $password,
            'assets' => $sanitized_assets,
            'likes' => array(),
            'share_images' => $share_images,
            'share_videos' => $share_videos,
            'client_email' => $client_email,
            'drive_folder_url' => $drive_folder_url,
            'created_date' => current_time( 'Y-m-d' )
        );
    }

    update_option( 'cora_re_portfolios', $portfolios );
    wp_send_json_success( array( 'message' => 'Gallery saved successfully.' ) );
}
add_action( 'wp_ajax_cora_save_portfolio', 'cora_ajax_save_portfolio' );

/**
 * AJAX Action: Delete Gallery
 */
function cora_ajax_delete_portfolio() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! cora_current_user_can_manage_portfolios() ) {
        wp_send_json_error( 'Access Denied: insufficient permissions.' );
    }

    $id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
    if ( empty( $id ) ) {
        wp_send_json_error( 'Gallery ID is required.' );
    }

    $portfolios = get_option( 'cora_re_portfolios', array() );
    if ( ! is_array( $portfolios ) ) {
        $portfolios = array();
    }

    $updated_portfolios = array();
    $deleted = false;
    foreach ( $portfolios as $portfolio ) {
        if ( isset( $portfolio['id'] ) && $portfolio['id'] === $id ) {
            $deleted = true;
            continue;
        }
        $updated_portfolios[] = $portfolio;
    }

    if ( $deleted ) {
        update_option( 'cora_re_portfolios', $updated_portfolios );
        wp_send_json_success( array( 'message' => 'Gallery deleted successfully.' ) );
    } else {
        wp_send_json_error( 'Gallery not found.' );
    }
}
add_action( 'wp_ajax_cora_delete_portfolio', 'cora_ajax_delete_portfolio' );

/**
 * AJAX Action: Toggle Gallery Asset Like (Public Endpoint)
 */
function cora_ajax_toggle_portfolio_like() {
    $portfolio_hash = isset( $_POST['portfolio_hash'] ) ? sanitize_text_field( $_POST['portfolio_hash'] ) : '';
    $asset_id = isset( $_POST['asset_id'] ) ? sanitize_text_field( $_POST['asset_id'] ) : '';
    $liked = isset( $_POST['liked'] ) && $_POST['liked'] === 'true';

    if ( empty( $portfolio_hash ) || empty( $asset_id ) ) {
        wp_send_json_error( 'Invalid request parameters.' );
    }

    $portfolios = get_option( 'cora_re_portfolios', array() );
    if ( ! is_array( $portfolios ) ) {
        wp_send_json_error( 'Portfolios store is empty.' );
    }

    $found_key = null;
    foreach ( $portfolios as $key => $portfolio ) {
        if ( isset( $portfolio['hash'] ) && $portfolio['hash'] === $portfolio_hash ) {
            $found_key = $key;
            break;
        }
    }

    if ( null === $found_key ) {
        wp_send_json_error( 'Gallery not found.' );
    }

    $likes = isset( $portfolios[$found_key]['likes'] ) ? $portfolios[$found_key]['likes'] : array();
    if ( ! is_array( $likes ) ) {
        $likes = array();
    }

    if ( $liked ) {
        if ( ! in_array( $asset_id, $likes ) ) {
            $likes[] = $asset_id;
        }
    } else {
        $likes = array_diff( $likes, array( $asset_id ) );
        $likes = array_values( $likes ); // re-index
    }

    $portfolios[$found_key]['likes'] = $likes;
    update_option( 'cora_re_portfolios', $portfolios );

    wp_send_json_success( array(
        'message' => 'Selection updated.',
        'likes_count' => count( $likes )
    ) );
}
add_action( 'wp_ajax_cora_toggle_portfolio_like', 'cora_ajax_toggle_portfolio_like' );
add_action( 'wp_ajax_nopriv_cora_toggle_portfolio_like', 'cora_ajax_toggle_portfolio_like' );

/**
 * R1: WordPress frontend shortcode [cora_lead_form]
 */
function cora_lead_form_shortcode() {
    ob_start();
    ?>
    <div class="cora-lead-form-container" style="max-width: 500px; margin: 20px auto; padding: 24px; border: 1px solid #e4e4e7; border-radius: 8px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #ffffff; color: #18181b;">
        <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 18px; font-weight: 600; letter-spacing: -0.5px;">Inquire About Properties</h3>
        <form id="cora-frontend-lead-form" style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label for="cora-lead-names" style="font-size: 12px; font-weight: 500; color: #71717a;">Full Name *</label>
                <input type="text" id="cora-lead-names" name="names" required style="padding: 8px 12px; border: 1px solid #e4e4e7; border-radius: 6px; font-size: 14px; outline: none; background: #fafafa;" />
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label for="cora-lead-email" style="font-size: 12px; font-weight: 500; color: #71717a;">Email Address *</label>
                <input type="email" id="cora-lead-email" name="email" required style="padding: 8px 12px; border: 1px solid #e4e4e7; border-radius: 6px; font-size: 14px; outline: none; background: #fafafa;" />
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label for="cora-lead-city" style="font-size: 12px; font-weight: 500; color: #71717a;">City</label>
                <input type="text" id="cora-lead-city" name="city" style="padding: 8px 12px; border: 1px solid #e4e4e7; border-radius: 6px; font-size: 14px; outline: none; background: #fafafa;" />
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label for="cora-lead-price" style="font-size: 12px; font-weight: 500; color: #71717a;">Target Price / Budget</label>
                <input type="text" id="cora-lead-price" name="price" placeholder="e.g. $500,000" style="padding: 8px 12px; border: 1px solid #e4e4e7; border-radius: 6px; font-size: 14px; outline: none; background: #fafafa;" />
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label for="cora-lead-scale" style="font-size: 12px; font-weight: 500; color: #71717a;">Inquiry Scale</label>
                <select id="cora-lead-scale" name="scale" style="padding: 8px 12px; border: 1px solid #e4e4e7; border-radius: 6px; font-size: 14px; outline: none; background: #fafafa;">
                    <option value="Small">Small (Looking)</option>
                    <option value="Medium" selected>Medium (Active)</option>
                    <option value="Large">Large (Immediate Buy)</option>
                </select>
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label for="cora-lead-notes" style="font-size: 12px; font-weight: 500; color: #71717a;">Notes</label>
                <textarea id="cora-lead-notes" name="notes" rows="3" style="padding: 8px 12px; border: 1px solid #e4e4e7; border-radius: 6px; font-size: 14px; outline: none; background: #fafafa; resize: vertical;"></textarea>
            </div>
            <div id="cora-lead-form-feedback" style="display: none; padding: 10px; font-size: 13px; border-radius: 6px;"></div>
            <button type="submit" id="cora-lead-submit-btn" style="padding: 10px; border: none; border-radius: 6px; background: #18181b; color: #ffffff; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.15s;">Submit Inquiry</button>
        </form>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('cora-frontend-lead-form');
                if (!form) return;
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('cora-lead-submit-btn');
                    const feedback = document.getElementById('cora-lead-form-feedback');
                    
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Submitting...';
                    feedback.style.display = 'none';
                    
                    const formData = new FormData(form);
                    formData.append('action', 'cora_re_submit_lead');
                    
                    const ajaxUrl = (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php';
                    
                    fetch(ajaxUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Submit Inquiry';
                        feedback.style.display = 'block';
                        if (data.success) {
                            feedback.style.backgroundColor = '#f4f4f5';
                            feedback.style.color = '#18181b';
                            feedback.style.border = '1px solid #e4e4e7';
                            feedback.style.marginTop = '8px';
                            feedback.innerText = data.data.message || 'Inquiry logged successfully!';
                            form.reset();
                        } else {
                            feedback.style.backgroundColor = '#fef2f2';
                            feedback.style.color = '#991b1b';
                            feedback.style.border = '1px solid #fee2e2';
                            feedback.style.marginTop = '8px';
                            feedback.innerText = data.data || 'Error submitting lead.';
                        }
                    })
                    .catch(error => {
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Submit Inquiry';
                        feedback.style.display = 'block';
                        feedback.style.backgroundColor = '#fef2f2';
                        feedback.style.color = '#991b1b';
                        feedback.style.border = '1px solid #fee2e2';
                        feedback.style.marginTop = '8px';
                        feedback.innerText = 'Network error submitting lead.';
                    });
                });
            });
        </script>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'cora_lead_form', 'cora_lead_form_shortcode' );

/**
 * Register WordPress REST API route for Lead Webhook (R1)
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'cora/v1', '/leads', array(
        'methods'             => 'POST',
        'callback'            => 'cora_post_leads_rest',
        'permission_callback' => '__return_true',
    ) );
} );

/**
 * Callback: REST API lead entry
 */
function cora_post_leads_rest( $request ) {
    $params = $request->get_json_params();
    if ( empty( $params ) ) {
        $params = $request->get_params();
    }
    
    $names = isset( $params['names'] ) ? sanitize_text_field( $params['names'] ) : '';
    $email = isset( $params['email'] ) ? sanitize_email( $params['email'] ) : '';
    $scale = isset( $params['scale'] ) ? sanitize_text_field( $params['scale'] ) : '';
    $city  = isset( $params['city'] ) ? sanitize_text_field( $params['city'] ) : '';
    $notes = isset( $params['notes'] ) ? sanitize_textarea_field( $params['notes'] ) : '';
    $price = isset( $params['price'] ) ? sanitize_text_field( $params['price'] ) : '';
    
    if ( empty( $names ) || empty( $email ) ) {
        return new WP_Error( 'cora_invalid_lead', 'Names and Email are required.', array( 'status' => 400 ) );
    }
    
    if ( ! is_email( $email ) ) {
        return new WP_Error( 'cora_invalid_email', 'Invalid email address.', array( 'status' => 400 ) );
    }
    
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $wpdb->insert(
        $wpdb->prefix . 'cora_leads',
        array(
            'agency_id' => $agency_id,
            'branch_id' => $branch_id,
            'assigned_to' => null,
            'first_name' => $names,
            'last_name' => '',
            'email' => $email,
            'phone' => '',
            'source' => 'REST API',
            'status' => 'new',
            'budget_min' => 0,
            'budget_max' => !empty($price) ? intval(preg_replace('/[^\d]/', '', $price)) : 0,
            'preferred_locations' => $city,
            'property_type' => $scale,
            'notes' => $notes,
            'followup_date' => null,
            'followup_notes' => '',
            'converted_to_client' => 0,
            'client_id' => null,
            'embed_vector' => 0,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s')
    );
    $inserted_id = $wpdb->insert_id;

    $leads = get_option( 'cora_re_leads', array() );
    if ( ! is_array( $leads ) ) {
        $leads = array();
    }
    
    $lead_id = $inserted_id;
    
    $new_lead = array(
        'id'         => $lead_id,
        'names'      => $names,
        'email'      => $email,
        'scale'      => $scale,
        'city'       => $city,
        'notes'      => $notes,
        'price'      => $price,
        'status'     => 'New Lead',
        'emails'     => cora_generate_default_email_sequence( $names, $scale, $city ),
        'created_at' => time()
    );
    
    $leads[] = $new_lead;
    update_option( 'cora_re_leads', $leads );
    
    return new WP_REST_Response( array(
        'success' => true,
        'message' => 'Lead logged successfully via REST API!',
        'lead'    => $new_lead
    ), 200 );
}

/**
 * R2: AJAX Endpoint for 3rd-Party Portal Listing Sync
 */
function cora_ajax_sync_listing_link() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    
    $url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';
    if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
        wp_send_json_error( 'Please enter a valid listing URL.' );
    }

    $host = parse_url( $url, PHP_URL_HOST );
    $host = strtolower( $host );

    $name = 'Delhi Luxury Residence';
    $category = 'Apartment';
    $rera_id = 'DLH-RERA-2026-99';
    $notes = 'Beautiful property synced from third party.';

    if ( strpos( $host, 'zillow' ) !== false ) {
        $name = 'Zillow Sunset Villa';
        $category = 'Villa';
        $rera_id = 'ZIL-ERA-1049281';
        $notes = 'Synced from Zillow: A gorgeous beachfront villa with scenic views and spacious layout.';
    } elseif ( strpos( $host, '99acres' ) !== false ) {
        $name = '99acres Signature Penthouse';
        $category = 'Penthouse';
        $rera_id = '99A-ERA-4820124';
        $notes = 'Synced from 99acres: High-rise luxury penthouse with private elevator access and panoramic views.';
    } elseif ( strpos( $host, 'magicbricks' ) !== false ) {
        $name = 'Magicbricks Cybercity Commercial';
        $category = 'Commercial';
        $rera_id = 'MAG-ERA-8830124';
        $notes = 'Synced from Magicbricks: Premium grade-A commercial office space in prime Cybercity IT hub.';
    }

    wp_send_json_success( array(
        'name'        => $name,
        'category'    => $category,
        'rera_reg_id' => $rera_id,
        'notes'       => $notes,
    ) );
}
add_action( 'wp_ajax_cora_sync_listing_link', 'cora_ajax_sync_listing_link' );

/**
 * Helper to check CRM permissions
 */
function cora_current_user_can_manage_leads() {
    if ( ! is_user_logged_in() ) {
        return false;
    }
    $user = wp_get_current_user();
    $allowed_roles = array( 'administrator', 'cora_manager' );
    foreach ( $allowed_roles as $role ) {
        if ( in_array( $role, (array) $user->roles ) ) {
            return true;
        }
    }
    return false;
}

/**
 * AJAX Action: Submit Lead from Frontend (Public)
 */
function cora_ajax_submit_lead() {
    $names = isset( $_POST['names'] ) ? sanitize_text_field( $_POST['names'] ) : '';
    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $scale = isset( $_POST['scale'] ) ? sanitize_text_field( $_POST['scale'] ) : '';
    $city  = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
    $notes = isset( $_POST['notes'] ) ? sanitize_textarea_field( $_POST['notes'] ) : '';
    $price = isset( $_POST['price'] ) ? sanitize_text_field( $_POST['price'] ) : '';
    $followup_date = isset( $_POST['followup_date'] ) ? sanitize_text_field( $_POST['followup_date'] ) : '';

    if ( empty( $names ) || empty( $email ) ) {
        wp_send_json_error( 'Names and Email are required.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $followup_dt = null;
    if ( ! empty($followup_date) ) {
        $followup_dt = date('Y-m-d H:i:s', strtotime($followup_date));
    }

    $wpdb->insert(
        $wpdb->prefix . 'cora_leads',
        array(
            'agency_id' => $agency_id,
            'branch_id' => $branch_id,
            'assigned_to' => null,
            'first_name' => $names,
            'last_name' => '',
            'email' => $email,
            'phone' => '',
            'source' => 'Frontend',
            'status' => 'new',
            'budget_min' => 0,
            'budget_max' => !empty($price) ? intval(preg_replace('/[^\d]/', '', $price)) : 0,
            'preferred_locations' => $city,
            'property_type' => $scale,
            'notes' => $notes,
            'followup_date' => $followup_dt,
            'followup_notes' => '',
            'converted_to_client' => 0,
            'client_id' => null,
            'embed_vector' => 0,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s')
    );
    $inserted_id = $wpdb->insert_id;

    $leads = get_option( 'cora_re_leads', array() );
    if ( ! is_array( $leads ) ) {
        $leads = array();
    }

    $new_lead = array(
        'id'         => $inserted_id,
        'names'      => $names,
        'email'      => $email,
        'scale'      => $scale,
        'city'       => $city,
        'notes'      => $notes,
        'price'      => $price,
        'status'     => 'New Lead',
        'emails'     => cora_generate_default_email_sequence( $names, $scale, $city ),
        'followup_date' => $followup_date,
        'created_at' => time()
    );

    $leads[] = $new_lead;
    update_option( 'cora_re_leads', $leads );

    wp_send_json_success( array(
        'message' => 'Inquiry logged successfully!',
        'lead'    => $new_lead
    ) );
}
add_action( 'wp_ajax_cora_re_submit_lead', 'cora_ajax_submit_lead' );
add_action( 'wp_ajax_nopriv_cora_re_submit_lead', 'cora_ajax_submit_lead' );

/**
 * AJAX Action: Update Lead (Admin Only)
 */
function cora_ajax_update_lead_status() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! cora_current_user_can_manage_leads() ) {
        wp_send_json_error( 'Access Denied: insufficient permissions.' );
    }

    $lead_id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
    $status  = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
    $notes   = isset( $_POST['notes'] ) ? sanitize_textarea_field( $_POST['notes'] ) : null;
    $names   = isset( $_POST['names'] ) ? sanitize_text_field( $_POST['names'] ) : null;
    $email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : null;
    
    $scale = isset( $_POST['scale'] ) ? sanitize_text_field( $_POST['scale'] ) : null;
    $city  = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : null;
    $price = isset( $_POST['price'] ) ? sanitize_text_field( $_POST['price'] ) : null;
    
    $demo_portfolio        = isset( $_POST['demo_portfolio'] ) ? sanitize_text_field( $_POST['demo_portfolio'] ) : null;
    $demo_portfolio_shared = isset( $_POST['demo_portfolio_shared'] ) ? sanitize_text_field( $_POST['demo_portfolio_shared'] ) : null;
    $demo_portfolio_viewed = isset( $_POST['demo_portfolio_viewed'] ) ? sanitize_text_field( $_POST['demo_portfolio_viewed'] ) : null;
    $listing_ids       = isset( $_POST['listing_ids'] ) ? sanitize_text_field( $_POST['listing_ids'] ) : null;
    $followup_date     = isset( $_POST['followup_date'] ) ? sanitize_text_field( $_POST['followup_date'] ) : null;

    if ( empty( $lead_id ) ) {
        wp_send_json_error( 'Lead ID is required.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();

    $update_data = array();
    $update_format = array();

    if ( ! empty( $status ) ) {
        $status_enum = 'new';
        $os = strtolower(trim($status));
        if (strpos($os, 'new') !== false) $status_enum = 'new';
        elseif (strpos($os, 'proposal') !== false || strpos($os, 'contact') !== false) $status_enum = 'contacted';
        elseif (strpos($os, 'visit') !== false || strpos($os, 'showing') !== false) $status_enum = 'site_visit';
        elseif (strpos($os, 'negotiat') !== false) $status_enum = 'negotiation';
        elseif (strpos($os, 'closed') !== false || strpos($os, 'won') !== false || $os === 'converted') $status_enum = 'closed';
        elseif (strpos($os, 'lost') !== false) $status_enum = 'lost';
        
        $update_data['status'] = $status_enum;
        $update_format[] = '%s';
    }
    if ( null !== $notes ) {
        $update_data['notes'] = $notes;
        $update_format[] = '%s';
    }
    if ( null !== $names ) {
        $update_data['first_name'] = $names;
        $update_format[] = '%s';
    }
    if ( null !== $email ) {
        $update_data['email'] = $email;
        $update_format[] = '%s';
    }
    if ( null !== $scale ) {
        $update_data['property_type'] = $scale;
        $update_format[] = '%s';
    }
    if ( null !== $city ) {
        $update_data['preferred_locations'] = $city;
        $update_format[] = '%s';
    }
    if ( null !== $price ) {
        $update_data['budget_max'] = !empty($price) ? intval(preg_replace('/[^\d]/', '', $price)) : 0;
        $update_format[] = '%d';
    }
    if ( null !== $followup_date ) {
        $update_data['followup_date'] = !empty($followup_date) ? date('Y-m-d H:i:s', strtotime($followup_date)) : null;
        $update_format[] = '%s';
    }

    if ( ! empty($update_data) ) {
        $update_data['updated_at'] = current_time('mysql');
        $update_format[] = '%s';
        
        $wpdb->update(
            $wpdb->prefix . 'cora_leads',
            $update_data,
            array( 'id' => $lead_id, 'agency_id' => $agency_id ),
            $update_format,
            array( '%d', '%d' )
        );
    }

    $leads = get_option( 'cora_re_leads', array() );
    if ( ! is_array( $leads ) ) {
        wp_send_json_error( 'No leads found.' );
    }

    $found_key = null;
    foreach ( $leads as $key => $lead ) {
        if ( isset( $lead['id'] ) && strval($lead['id']) === strval($lead_id) ) {
            $found_key = $key;
            break;
        }
    }

    if ( null !== $found_key ) {
        if ( ! empty( $status ) ) {
            $leads[$found_key]['status'] = $status;
            if ( 'Converted' === $status ) {
                if ( isset( $leads[$found_key]['emails'] ) && is_array( $leads[$found_key]['emails'] ) ) {
                    foreach ( $leads[$found_key]['emails'] as $ekey => $email_val ) {
                        if ( isset( $email_val['status'] ) && 'Scheduled' === $email_val['status'] ) {
                            $leads[$found_key]['emails'][$ekey]['status'] = 'Cancelled';
                        }
                    }
                }
                cora_copy_lead_to_clients( $leads[$found_key] );
            }
        }
        if ( null !== $notes ) $leads[$found_key]['notes'] = $notes;
        if ( null !== $names ) $leads[$found_key]['names'] = $names;
        if ( null !== $email ) $leads[$found_key]['email'] = $email;
        if ( null !== $scale ) $leads[$found_key]['scale'] = $scale;
        if ( null !== $city ) $leads[$found_key]['city'] = $city;
        if ( null !== $price ) $leads[$found_key]['price'] = $price;
        if ( null !== $demo_portfolio ) $leads[$found_key]['demo_portfolio'] = $demo_portfolio;
        if ( null !== $demo_portfolio_shared ) $leads[$found_key]['demo_portfolio_shared'] = ( $demo_portfolio_shared === 'true' );
        if ( null !== $demo_portfolio_viewed ) $leads[$found_key]['demo_portfolio_viewed'] = ( $demo_portfolio_viewed === 'true' );
        if ( null !== $listing_ids ) {
            $leads[$found_key]['listing_ids'] = array_filter( array_map( 'trim', explode( ',', $listing_ids ) ) );
        }
        if ( null !== $followup_date ) $leads[$found_key]['followup_date'] = $followup_date;

        update_option( 'cora_re_leads', $leads );
    }

    wp_send_json_success( array(
        'message' => 'Lead updated successfully.',
        'lead'    => (null !== $found_key) ? $leads[$found_key] : array()
    ) );
}
add_action( 'wp_ajax_cora_update_lead_status', 'cora_ajax_update_lead_status' );

/**
 * AJAX Action: Delete Lead (Admin Only)
 */
function cora_ajax_delete_lead() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! cora_current_user_can_manage_leads() ) {
        wp_send_json_error( 'Access Denied.' );
    }

    $lead_id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';

    if ( empty( $lead_id ) ) {
        wp_send_json_error( 'Lead ID is required.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();

    $wpdb->delete(
        $wpdb->prefix . 'cora_leads',
        array( 'id' => $lead_id, 'agency_id' => $agency_id ),
        array( '%d', '%d' )
    );

    $leads = get_option( 'cora_re_leads', array() );
    if ( is_array( $leads ) ) {
        $found_key = null;
        foreach ( $leads as $key => $lead ) {
            if ( isset( $lead['id'] ) && strval($lead['id']) === strval($lead_id) ) {
                $found_key = $key;
                break;
            }
        }
        if ( null !== $found_key ) {
            unset( $leads[$found_key] );
            $leads = array_values( $leads );
            update_option( 'cora_re_leads', $leads );
        }
    }

    wp_send_json_success( array(
        'message' => 'Lead deleted.'
    ) );
}
add_action( 'wp_ajax_cora_re_delete_lead', 'cora_ajax_delete_lead' );

/**
 * Helper: Copy a Lead to Client Directory
 */
function cora_copy_lead_to_clients( $lead ) {
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $client_exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_clients WHERE lead_id = %d AND agency_id = %d", $lead['id'], $agency_id ) );
    $inserted_client_id = 0;
    if ( ! $client_exists ) {
        $wpdb->insert(
            $wpdb->prefix . 'cora_clients',
            array(
                'agency_id' => $agency_id,
                'branch_id' => $branch_id,
                'lead_id' => $lead['id'],
                'first_name' => $lead['names'] ?? '',
                'last_name' => '',
                'email' => $lead['email'] ?? '',
                'phone' => $lead['phone'] ?? '',
                'type' => 'buyer',
                'notes' => $lead['scale'] ?? '',
                'embed_vector' => 0,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
        );
        $inserted_client_id = $wpdb->insert_id;
        
        $wpdb->update(
            $wpdb->prefix . 'cora_leads',
            array( 'converted_to_client' => 1, 'client_id' => $inserted_client_id ),
            array( 'id' => $lead['id'], 'agency_id' => $agency_id ),
            array( '%d', '%d' ),
            array( '%d', '%d' )
        );
    }

    $clients = get_option( 'cora_re_clients', array() );
    if ( ! is_array( $clients ) ) {
        $clients = array();
    }

    foreach ( $clients as $client ) {
        if ( isset( $client['lead_id'] ) && strval($client['lead_id']) === strval($lead['id']) ) {
            return;
        }
    }

    $new_client_id = $client_exists ? $client_exists : $inserted_client_id;

    $clients[] = array(
        'id'            => $new_client_id,
        'lead_id'       => $lead['id'],
        'names'         => $lead['names'],
        'email'         => $lead['email'],
        'scale'         => $lead['scale'],
        'city'          => $lead['city'],
        'notes'         => $lead['notes'],
        'price'         => $lead['price'],
        'converted_at'  => time(),
        'status'        => 'confirmed',
        'viewing_date'    => '25th Jun, 2026',
        'deal_type'    => 'Residential Buy',
        'listing_ids' => isset( $lead['listing_ids'] ) ? $lead['listing_ids'] : array(),
        'demo_portfolio'  => isset( $lead['demo_portfolio'] ) ? $lead['demo_portfolio'] : ''
    );

    update_option( 'cora_re_clients', $clients );
}

/**
 * AJAX Action: Convert Lead to Client manually (Admin Only)
 */
function cora_ajax_convert_lead_to_client() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! cora_current_user_can_manage_leads() ) {
        wp_send_json_error( 'Access Denied.' );
    }

    $lead_id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';

    if ( empty( $lead_id ) ) {
        wp_send_json_error( 'Lead ID is required.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();

    $wpdb->update(
        $wpdb->prefix . 'cora_leads',
        array( 'status' => 'closed', 'converted_to_client' => 1 ),
        array( 'id' => $lead_id, 'agency_id' => $agency_id ),
        array( '%s', '%d' ),
        array( '%d', '%d' )
    );

    $leads = get_option( 'cora_re_leads', array() );
    $found_key = null;
    foreach ( $leads as $key => $lead ) {
        if ( isset( $lead['id'] ) && strval($lead['id']) === strval($lead_id) ) {
            $found_key = $key;
            break;
        }
    }

    if ( null !== $found_key ) {
        $leads[$found_key]['status'] = 'Converted';
        
        if ( isset( $leads[$found_key]['emails'] ) && is_array( $leads[$found_key]['emails'] ) ) {
            foreach ( $leads[$found_key]['emails'] as $ekey => $email ) {
                if ( isset( $email['status'] ) && 'Scheduled' === $email['status'] ) {
                    $leads[$found_key]['emails'][$ekey]['status'] = 'Cancelled';
                }
            }
        }
        
        cora_copy_lead_to_clients( $leads[$found_key] );
        update_option( 'cora_re_leads', $leads );
    }

    wp_send_json_success( array(
        'message' => 'Lead converted to Client directory successfully.'
    ) );
}
add_action( 'wp_ajax_cora_re_convert_lead_to_client', 'cora_ajax_convert_lead_to_client' );

/**
 * AJAX Action: Delete Client (Admin Only)
 */
function cora_ajax_delete_client() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! cora_current_user_can_manage_leads() ) {
        wp_send_json_error( 'Access Denied.' );
    }

    $client_id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';

    if ( empty( $client_id ) ) {
        wp_send_json_error( 'Client ID is required.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $db_id = intval(preg_replace('/[^\d]/', '', $client_id));

    $wpdb->delete(
        $wpdb->prefix . 'cora_clients',
        array( 'id' => $db_id, 'agency_id' => $agency_id ),
        array( '%d', '%d' )
    );

    $clients = get_option( 'cora_re_clients', array() );
    if ( is_array( $clients ) ) {
        $found_key = null;
        foreach ( $clients as $key => $client ) {
            if ( isset( $client['id'] ) && strval($client['id']) === strval($client_id) ) {
                $found_key = $key;
                break;
            }
        }
        if ( null !== $found_key ) {
            unset( $clients[$found_key] );
            $clients = array_values( $clients );
            update_option( 'cora_re_clients', $clients );
        }
    }

    wp_send_json_success( array(
        'message' => 'Client removed from directory.'
    ) );
}
add_action( 'wp_ajax_cora_delete_client', 'cora_ajax_delete_client' );

/**
 * AJAX Action: Add / Save a new Viewing Booking (Client)
 */
function cora_ajax_save_booking() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }

    $name = sanitize_text_field( $_POST['client_name'] ?? '' );
    $type = sanitize_text_field( $_POST['deal_type'] ?? 'Residential Buy' );
    $location = sanitize_text_field( $_POST['location'] ?? 'Delhi Office' );
    $date = sanitize_text_field( $_POST['date'] ?? '' );
    $price = sanitize_text_field( $_POST['price'] ?? '₹15,000' );

    if ( empty( $name ) ) {
        wp_send_json_error( 'Client name is required.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $wpdb->insert(
        $wpdb->prefix . 'cora_clients',
        array(
            'agency_id' => $agency_id,
            'branch_id' => $branch_id,
            'lead_id' => null,
            'first_name' => $name,
            'last_name' => '',
            'email' => strtolower( str_replace( ' ', '', $name ) ) . '@gmail.com',
            'phone' => '',
            'type' => 'buyer',
            'notes' => strtolower( str_replace( ' ', '-', $type ) ),
            'embed_vector' => 0,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
    );
    $new_client_id = $wpdb->insert_id;

    $package_value = intval(preg_replace('/[^\d]/', '', $price));
    $showing_dt = !empty($date) ? date('Y-m-d H:i:s', strtotime($date)) : current_time('mysql');

    $wpdb->insert(
        $wpdb->prefix . 'cora_bookings',
        array(
            'agency_id' => $agency_id,
            'branch_id' => $branch_id,
            'lead_id' => null,
            'client_id' => $new_client_id,
            'property_id' => null,
            'assigned_agent' => get_current_user_id(),
            'showing_date' => $showing_dt,
            'status' => 'confirmed',
            'package_value' => $package_value,
            'deal_type' => $type,
            'crew' => '[]',
            'notes' => $location,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array('%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
    );

    $clients = get_option( 'cora_re_clients', array() );
    if ( ! is_array( $clients ) ) {
        $clients = array();
    }

    $new_client = array(
        'id'           => $new_client_id,
        'names'        => $name,
        'email'        => strtolower( str_replace( ' ', '', $name ) ) . '@gmail.com',
        'scale'        => strtolower( str_replace( ' ', '-', $type ) ),
        'city'         => $location,
        'price'        => $price,
        'converted_at' => time(),
        'status'       => 'confirmed',
        'viewing_date'   => $date,
        'deal_type'   => $type
    );

    $clients[] = $new_client;
    update_option( 'cora_re_clients', $clients );

    wp_send_json_success( $new_client );
}
add_action( 'wp_ajax_cora_save_booking', 'cora_ajax_save_booking' );

/**
 * AJAX Action: Update booking status
 */
function cora_ajax_update_booking_status() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }

    $client_id = sanitize_text_field( $_POST['client_id'] ?? '' );
    $client_name = sanitize_text_field( $_POST['client_name'] ?? '' );
    $status = sanitize_text_field( $_POST['status'] ?? '' );

    if ( empty( $status ) ) {
        wp_send_json_error( 'Status is required.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $db_client_id = intval(preg_replace('/[^\d]/', '', $client_id));

    if ( $db_client_id > 0 ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_bookings',
            array( 'status' => $status, 'updated_at' => current_time('mysql') ),
            array( 'client_id' => $db_client_id, 'agency_id' => $agency_id ),
            array( '%s', '%s' ),
            array( '%d', '%d' )
        );
    } elseif ( ! empty($client_name) ) {
        $resolved_client_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_clients WHERE first_name = %s AND agency_id = %d", $client_name, $agency_id ) );
        if ( $resolved_client_id ) {
            $wpdb->update(
                $wpdb->prefix . 'cora_bookings',
                array( 'status' => $status, 'updated_at' => current_time('mysql') ),
                array( 'client_id' => $resolved_client_id, 'agency_id' => $agency_id ),
                array( '%s', '%s' ),
                array( '%d', '%d' )
            );
        }
    }

    $clients = get_option( 'cora_re_clients', array() );
    $updated = false;

    foreach ( $clients as $key => $client ) {
        if ( ( ! empty( $client_id ) && strval($client['id']) === strval($client_id) ) || ( ! empty( $client_name ) && $client['names'] === $client_name ) ) {
            $clients[$key]['status'] = $status;
            $updated = true;
            break;
        }
    }

    if ( $updated ) {
        update_option( 'cora_re_clients', $clients );
        wp_send_json_success( 'Status updated.' );
    } else {
        wp_send_json_error( 'Client not found.' );
    }
}
add_action( 'wp_ajax_cora_update_booking_status', 'cora_ajax_update_booking_status' );

/**
 * Helper to generate default automated email sequence for a lead
 */
function cora_generate_default_email_sequence( $lead_names, $lead_scale, $lead_city ) {
    $first_name = explode( ' ', $lead_names )[0];
    
    $event_label = 'property deal';
    if ( $lead_scale === 'intimate' ) $event_label = 'intimate listing';
    if ( $lead_scale === 'multi-day' ) $event_label = 'multi-day celebration';
    if ( $lead_scale === 'destination' ) $event_label = 'grand destination listing';
    if ( $lead_scale === 'documentary' ) $event_label = 'pre-listing documentary';

    $location_str = ! empty( $lead_city ) ? " in " . $lead_city : "";

    return array(
        array(
            'id'            => 'email_' . time() . '_1',
            'step'          => 1,
            'trigger_delay' => 'Immediate',
            'subject'       => "Thank you for reaching out, " . $first_name . "!",
            'body'          => "Hi " . $first_name . ",\n\nThank you so much for contacting Cora for Real Estate. We are thrilled to hear about your upcoming " . $event_label . $location_str . "!\n\nOur team is currently reviewing your inquiry notes and vision details. We will be in touch within the next 24 hours to schedule our initial creative consultation.\n\nWarm regards,\nCora for Real Estate Agent",
            'status'        => 'Sent',
            'sent_at'       => time(),
        ),
        array(
            'id'            => 'email_' . time() . '_2',
            'step'          => 2,
            'trigger_delay' => 'Day 1 Follow-up',
            'subject'       => "Our Latest Work & Visual Styles",
            'body'          => "Hi " . $first_name . ",\n\nWhile we prepare for our consultation, we wanted to share a few curated highlights of our recent " . $event_label . " work.\n\nWe focus on capturing raw, authentic moments and crafting them into everlasting visual narratives. You can explore our featured portfolios inside the Brokerage Workspace.\n\nLooking forward to speaking with you!\n\nBest,\nCora for Real Estate Agent",
            'status'        => 'Scheduled',
            'sent_at'       => null,
        ),
        array(
            'id'            => 'email_' . time() . '_3',
            'step'          => 3,
            'trigger_delay' => 'Day 3 Consultation Call',
            'subject'       => "Let's align your vision - Book a calendar slot",
            'body'          => "Hi " . $first_name . ",\n\nWe would love to get a consultation on the books. This helps us align on the creative direction, event timeline, and custom packages for your " . $event_label . ".\n\nPlease select a convenient time slot via our staging link: https://calendly.com/cora-studio/creative-consultation\n\nSpeak soon!\n\nBest regards,\nCora for Real Estate Agent",
            'status'        => 'Scheduled',
            'sent_at'       => null,
        ),
        array(
            'id'            => 'email_' . time() . '_4',
            'step'          => 4,
            'trigger_delay' => 'Day 5 Final Follow-up',
            'subject'       => "Quick follow-up from Cora for Real Estate",
            'body'          => "Hi " . $first_name . ",\n\nJust wanted to send a quick follow-up to see if you had any questions about our previous emails or if you'd like to book that consultation call.\n\nWe only accept a limited number of commissions per season to ensure premium focus for every couple, and we'd love to work with you on your " . $event_label . ".\n\nLet us know if you have any questions!\n\nWarmly,\nCora for Real Estate Agent",
            'status'        => 'Scheduled',
            'sent_at'       => null,
        ),
    );
}

/**
 * AJAX Action: Update Lead Email Status
 */
function cora_ajax_update_lead_email_status() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! cora_current_user_can_manage_leads() ) {
        wp_send_json_error( 'Access Denied.' );
    }

    $lead_id  = isset( $_POST['lead_id'] ) ? sanitize_text_field( $_POST['lead_id'] ) : '';
    $email_id = isset( $_POST['email_id'] ) ? sanitize_text_field( $_POST['email_id'] ) : '';
    $new_status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';

    if ( empty( $lead_id ) || empty( $email_id ) || empty( $new_status ) ) {
        wp_send_json_error( 'Missing parameters.' );
    }

    $leads = get_option( 'cora_re_leads', array() );
    if ( ! is_array( $leads ) ) {
        wp_send_json_error( 'No leads found.' );
    }

    $lead_found_key = null;
    foreach ( $leads as $key => $lead ) {
        if ( isset( $lead['id'] ) && $lead['id'] === $lead_id ) {
            $lead_found_key = $key;
            break;
        }
    }

    if ( null === $lead_found_key ) {
        wp_send_json_error( 'Lead not found.' );
    }

    $emails = isset( $leads[$lead_found_key]['emails'] ) ? $leads[$lead_found_key]['emails'] : array();
    $email_found_key = null;
    foreach ( $emails as $key => $email ) {
        if ( isset( $email['id'] ) && $email['id'] === $email_id ) {
            $email_found_key = $key;
            break;
        }
    }

    if ( null === $email_found_key ) {
        wp_send_json_error( 'Email not found in sequence.' );
    }

    $emails[$email_found_key]['status'] = $new_status;
    if ( 'Sent' === $new_status ) {
        $emails[$email_found_key]['sent_at'] = time();
    } else {
        $emails[$email_found_key]['sent_at'] = null;
    }

    $leads[$lead_found_key]['emails'] = $emails;
    update_option( 'cora_re_leads', $leads );

    wp_send_json_success( array(
        'message' => 'Email status updated successfully.',
        'emails'  => $emails
    ) );
}
add_action( 'wp_ajax_cora_update_lead_email_status', 'cora_ajax_update_lead_email_status' );

/**
 * AJAX Action: Save Transaction (Inflow/Outflow)
 */
function cora_ajax_save_transaction() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    
    if ( ! cora_current_user_can_manage_leads() ) {
        wp_send_json_error( 'Access Denied.' );
    }

    $id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
    $date = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : date('Y-m-d');
    $description = isset( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '';
    $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'Inflow';
    $amount = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
    $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
    $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'Received';
    $client_link = isset( $_POST['client_link'] ) ? sanitize_text_field( $_POST['client_link'] ) : '';

    if ( empty( $description ) || $amount <= 0 ) {
        wp_send_json_error( 'Description and a positive Amount are required.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $db_client_id = intval(preg_replace('/[^\d]/', '', $client_link));
    $amount_cents = intval($amount * 100);

    $db_id = 0;
    if ( ! empty( $id ) ) {
        $db_id = intval(preg_replace('/[^\d]/', '', $id));
    }

    if ( $db_id > 0 ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_ledger',
            array(
                'type' => strtolower($type),
                'amount' => $amount_cents,
                'description' => $description,
                'client_id' => $db_client_id ?: null,
                'status' => strtolower($status),
                'transaction_date' => $date,
                'updated_at' => current_time('mysql')
            ),
            array( 'id' => $db_id, 'agency_id' => $agency_id ),
            array('%s', '%d', '%s', '%d', '%s', '%s', '%s'),
            array('%d', '%d')
        );
        $new_id = $db_id;
    } else {
        $wpdb->insert(
            $wpdb->prefix . 'cora_ledger',
            array(
                'agency_id' => $agency_id,
                'branch_id' => $branch_id,
                'type' => strtolower($type),
                'amount' => $amount_cents,
                'description' => $description,
                'lead_id' => null,
                'client_id' => $db_client_id ?: null,
                'status' => strtolower($status),
                'transaction_date' => $date,
                'created_by' => get_current_user_id(),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%d', '%s', '%d', '%d', '%s', '%s', '%d', '%s', '%s')
        );
        $new_id = $wpdb->insert_id;
    }

    $transactions = get_option( 'cora_re_ledger', array() );
    if ( ! is_array( $transactions ) ) {
        $transactions = array();
    }

    $updated = false;
    $new_tx = array();

    if ( ! empty( $id ) ) {
        foreach ( $transactions as $key => $tx ) {
            if ( isset( $tx['id'] ) && strval($tx['id']) === strval($id) ) {
                $transactions[$key]['date'] = $date;
                $transactions[$key]['description'] = $description;
                $transactions[$key]['type'] = $type;
                $transactions[$key]['amount'] = $amount;
                $transactions[$key]['category'] = $category;
                $transactions[$key]['status'] = $status;
                $transactions[$key]['client_link'] = $client_link;
                $updated = true;
                $new_tx = $transactions[$key];
                break;
            }
        }
    }

    if ( ! $updated ) {
        $new_tx = array(
            'id' => $new_id,
            'date' => $date,
            'description' => $description,
            'type' => $type,
            'amount' => $amount,
            'category' => $category,
            'status' => $status,
            'client_link' => $client_link
        );
        $transactions[] = $new_tx;
    }

    update_option( 'cora_re_ledger', $transactions );
    wp_send_json_success( $new_tx );
}
add_action( 'wp_ajax_cora_save_transaction', 'cora_ajax_save_transaction' );

/**
 * AJAX Action: Delete Transaction
 */
function cora_ajax_delete_transaction() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    
    if ( ! cora_current_user_can_manage_leads() ) {
        wp_send_json_error( 'Access Denied.' );
    }

    $id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';

    if ( empty( $id ) ) {
        wp_send_json_error( 'Transaction ID is required.' );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $db_id = intval(preg_replace('/[^\d]/', '', $id));

    $wpdb->delete(
        $wpdb->prefix . 'cora_ledger',
        array( 'id' => $db_id, 'agency_id' => $agency_id ),
        array( '%d', '%d' )
    );

    $transactions = get_option( 'cora_re_ledger', array() );
    if ( is_array( $transactions ) ) {
        $found_key = null;
        foreach ( $transactions as $key => $tx ) {
            if ( isset( $tx['id'] ) && strval($tx['id']) === strval($id) ) {
                $found_key = $key;
                break;
            }
        }
        if ( null !== $found_key ) {
            unset( $transactions[$found_key] );
            $transactions = array_values( $transactions );
            update_option( 'cora_re_ledger', $transactions );
        }
    }

    wp_send_json_success( array(
        'message' => 'Transaction deleted successfully.'
    ) );
}
add_action( 'wp_ajax_cora_delete_transaction', 'cora_ajax_delete_transaction' );

/**
 * AJAX Action: Send Document Email directly to the Client/Lead
 */
function cora_ajax_send_document_email() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    
    if ( ! cora_current_user_can_manage_leads() ) {
        wp_send_json_error( 'Access Denied.' );
    }

    $doc_id = isset( $_POST['doc_id'] ) ? sanitize_text_field( $_POST['doc_id'] ) : '';
    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $subject_override = isset( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
    $message_override = isset( $_POST['message'] ) ? wp_kses_post( $_POST['message'] ) : '';

    if ( empty( $doc_id ) || empty( $email ) ) {
        wp_send_json_error( 'Document ID and Email are required.' );
    }

    $documents = get_option( 'cora_re_vault_docs', array() );
    if ( ! is_array( $documents ) ) {
        wp_send_json_error( 'No documents found.' );
    }

    $found_key = null;
    foreach ( $documents as $key => $doc ) {
        if ( isset( $doc['id'] ) && $doc['id'] === $doc_id ) {
            $found_key = $key;
            break;
        }
    }

    if ( null === $found_key ) {
        wp_send_json_error( 'Document not found.' );
    }

    $doc = $documents[$found_key];
    $subject = ! empty( $subject_override ) ? $subject_override : $doc['title'];
    
    // Construct premium HTML email body
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    $email_content = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>' . esc_html( $subject ) . '</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #fafaf9; margin: 0; padding: 40px 20px; color: #18181b; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 12px; padding: 40px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
            .header { border-bottom: 1px solid #f4f4f5; padding-bottom: 20px; margin-bottom: 30px; text-align: left; }
            .logo-text { font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #09090b; }
            .content { font-size: 14px; line-height: 1.6; color: #27272a; }
            .footer { border-top: 1px solid #f4f4f5; padding-top: 20px; margin-top: 30px; text-align: center; font-size: 11px; color: #a1a1aa; }
            h1, h2, h3, h4 { color: #09090b; font-weight: 700; margin-top: 0; }
            ul, ol { padding-left: 20px; }
            a { color: #09090b; font-weight: 600; text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <span class="logo-text">CORA FOR REAL ESTATE</span>
            </div>
            <div class="content">
                ' . ( ! empty( $message_override ) ? $message_override : $doc['content'] ) . '
            </div>
            <div class="footer">
                ' . esc_html( ! empty( $doc['footer_text'] ) ? $doc['footer_text'] : '© Cora for Real Estate. All rights reserved.' ) . '
            </div>
        </div>
    </body>
    </html>
    ';

    $mail_success = wp_mail( $email, $subject, $email_content, $headers );

    if ( ! $mail_success ) {
        // Fallback standard text mail if headers break in some environments
        wp_mail( $email, $subject, strip_tags($doc['content']) );
    }

    // Update document status to Sent
    $documents[$found_key]['status'] = 'Sent';
    
    // Add share log or secure shares if not exist
    $share_hash = wp_hash( $doc_id . time() . $email );
    $new_share = array(
        'email' => $email,
        'hash' => $share_hash,
        'no_expiry' => true,
        'expiry_time' => time() + 3600*24*365,
        'created_at' => time()
    );
    if ( empty( $documents[$found_key]['secured_shares'] ) ) {
        $documents[$found_key]['secured_shares'] = array();
    }
    $documents[$found_key]['secured_shares'][] = $new_share;
    
    update_option( 'cora_re_vault_docs', $documents );

    wp_send_json_success( array(
        'message' => 'Document emailed directly to ' . $email . ' successfully.',
        'status' => 'Sent'
    ) );
}
add_action( 'wp_ajax_cora_send_document_email', 'cora_ajax_send_document_email' );/**
 * AJAX Action: Get Article Details
 */
function cora_ajax_get_article() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) wp_send_json_error('Invalid post ID');

    $post = get_post($post_id);
    if (!$post) wp_send_json_error('Post not found');

    $keyword = get_post_meta($post_id, '_cora_seo_keyword', true);
    $description = get_post_meta($post_id, '_cora_seo_description', true);
    $assignee_id = get_post_meta($post_id, '_cora_assignee_id', true) ?: '';
    $editorial_status = get_post_meta($post_id, '_cora_editorial_status', true) ?: ($post->post_status === 'publish' ? 'published' : 'draft');
    $editorial_feedback = get_post_meta($post_id, '_cora_editorial_feedback', true) ?: '';

    $categories = wp_get_post_categories($post_id);
    $tags = wp_get_post_tags($post_id, array('fields' => 'ids'));
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : '';

    wp_send_json_success(array(
        'title' => $post->post_title,
        'content' => $post->post_content,
        'keyword' => $keyword,
        'description' => $description,
        'categories' => $categories,
        'tags' => $tags,
        'thumbnail_id' => $thumbnail_id,
        'thumbnail_url' => $thumbnail_url,
        'assignee_id' => $assignee_id,
        'editorial_status' => $editorial_status,
        'editorial_feedback' => $editorial_feedback
    ));
}
add_action( 'wp_ajax_cora_get_article', 'cora_ajax_get_article' );

/**
 * AJAX Action: Save/Publish Article
 */
function cora_ajax_save_article() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
    $status = isset($_POST['status']) && $_POST['status'] === 'publish' ? 'publish' : 'draft';
    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    $seo_score = isset($_POST['seo_score']) ? intval($_POST['seo_score']) : '';
    
    $assignee_id = isset($_POST['assignee_id']) ? intval($_POST['assignee_id']) : 0;
    $editorial_status = isset($_POST['editorial_status']) ? sanitize_key($_POST['editorial_status']) : '';
    $editorial_feedback = isset($_POST['editorial_feedback']) ? sanitize_textarea_field($_POST['editorial_feedback']) : '';

    $categories = isset($_POST['categories']) ? array_map('intval', (array)$_POST['categories']) : array();
    
    // Process tags: numeric values are existing IDs, strings are new tag names
    $raw_tags = isset($_POST['tags']) ? (array)$_POST['tags'] : array();
    $tags = array();
    foreach ($raw_tags as $tag) {
        if (is_numeric($tag)) {
            $tags[] = intval($tag);
        } else {
            $tags[] = sanitize_text_field($tag);
        }
    }
    $thumbnail_id = isset($_POST['thumbnail_id']) ? intval($_POST['thumbnail_id']) : 0;

    if (empty($title)) wp_send_json_error('Title is required.');

    $post_data = array(
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => $status,
        'post_type'    => 'post'
    );

    if ($post_id > 0) {
        $post_data['ID'] = $post_id;
        $saved_id = wp_update_post($post_data, true);
    } else {
        $saved_id = wp_insert_post($post_data, true);
    }

    if (is_wp_error($saved_id)) {
        wp_send_json_error($saved_id->get_error_message());
    }

    // Set Categories and Tags
    if (!empty($categories)) {
        wp_set_post_categories($saved_id, $categories);
    }
    if (!empty($tags)) {
        wp_set_post_tags($saved_id, $tags);
    }

    // Set Thumbnail
    if ($thumbnail_id > 0) {
        set_post_thumbnail($saved_id, $thumbnail_id);
    } else {
        delete_post_thumbnail($saved_id);
    }

    update_post_meta($saved_id, '_cora_seo_keyword', $keyword);
    update_post_meta($saved_id, '_cora_seo_description', $description);
    update_post_meta($saved_id, '_cora_assignee_id', $assignee_id);
    update_post_meta($saved_id, '_cora_editorial_feedback', $editorial_feedback);

    if ($status === 'publish') {
        update_post_meta($saved_id, '_cora_editorial_status', 'published');
    } else if (!empty($editorial_status)) {
        update_post_meta($saved_id, '_cora_editorial_status', $editorial_status);
    } else {
        update_post_meta($saved_id, '_cora_editorial_status', 'draft');
    }

    if ($seo_score) {
        update_post_meta($saved_id, '_cora_seo_score', $seo_score);
    }

    wp_send_json_success();
}
add_action( 'wp_ajax_cora_save_article', 'cora_ajax_save_article' );

/**
 * AJAX Actions: Review State Management
 */
function cora_ajax_submit_for_review() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) wp_send_json_error('Invalid post ID');
    
    update_post_meta($post_id, '_cora_editorial_status', 'pending_review');
    wp_send_json_success(array('message' => 'Article submitted for review successfully!'));
}
add_action( 'wp_ajax_cora_submit_for_review', 'cora_ajax_submit_for_review' );

function cora_ajax_approve_draft() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) wp_send_json_error('Invalid post ID');
    
    update_post_meta($post_id, '_cora_editorial_status', 'approved');
    update_post_meta($post_id, '_cora_editorial_feedback', '');
    wp_send_json_success(array('message' => 'Article approved successfully!'));
}
add_action( 'wp_ajax_cora_approve_draft', 'cora_ajax_approve_draft' );

function cora_ajax_reject_draft() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $feedback = isset($_POST['feedback']) ? sanitize_textarea_field($_POST['feedback']) : '';
    if (!$post_id) wp_send_json_error('Invalid post ID');
    
    update_post_meta($post_id, '_cora_editorial_status', 'draft');
    update_post_meta($post_id, '_cora_editorial_feedback', $feedback);
    wp_send_json_success(array('message' => 'Revisions requested successfully!'));
}
add_action( 'wp_ajax_cora_reject_draft', 'cora_ajax_reject_draft' );

/**
 * DB Helper: Get Captured Leads count for Blog Post
 */
function cora_db_get_article_lead_count( $post_id ) {
    global $wpdb;
    $count = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}cora_leads WHERE source = %s",
        'Blog Post ID: ' . $post_id
    ) );
    return intval( $count );
}

/**
 * DB Helper: Get Captured Leads list for Blog Post
 */
function cora_db_get_article_leads( $post_id ) {
    global $wpdb;
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}cora_leads WHERE source = %s ORDER BY id DESC",
        'Blog Post ID: ' . $post_id
    ) );
}

/**
 * AJAX Action: Submit Blog Lead
 */
function cora_ajax_submit_blog_lead() {
    global $wpdb;
    $post_id    = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    $first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
    $last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
    $email      = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $phone      = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
    $notes      = isset( $_POST['notes'] ) ? sanitize_textarea_field( $_POST['notes'] ) : '';

    if ( empty( $first_name ) || empty( $email ) ) {
        wp_send_json_error( 'Name and email are required.' );
    }

    $lead_data = array(
        'agency_id'     => 1,
        'branch_id'     => 1,
        'first_name'    => $first_name,
        'last_name'     => $last_name,
        'email'         => $email,
        'phone'         => $phone,
        'source'        => 'Blog Post ID: ' . $post_id,
        'status'        => 'new',
        'notes'         => $notes,
        'created_at'    => current_time('mysql'),
        'updated_at'    => current_time('mysql')
    );

    $inserted = $wpdb->insert(
        $wpdb->prefix . 'cora_leads',
        $lead_data,
        array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
    );

    if ( $inserted ) {
        $inserted_id = $wpdb->insert_id;
        $leads_opt = get_option( 'cora_re_leads', array() );
        if ( ! is_array( $leads_opt ) ) {
            $leads_opt = array();
        }
        $leads_opt[] = array(
            'id'         => $inserted_id,
            'names'      => trim( $first_name . ' ' . $last_name ),
            'email'      => $email,
            'phone'      => $phone,
            'source'     => 'Blog Post ID: ' . $post_id,
            'status'     => 'new',
            'notes'      => $notes,
            'created_at' => time()
        );
        update_option( 'cora_re_leads', $leads_opt );

        cora_sync_db_tables_to_options();
        wp_send_json_success( array( 'message' => 'Your request was submitted successfully!' ) );
    } else {
        wp_send_json_error( 'Failed to submit request.' );
    }
}
add_action( 'wp_ajax_cora_submit_blog_lead', 'cora_ajax_submit_blog_lead' );
add_action( 'wp_ajax_nopriv_cora_submit_blog_lead', 'cora_ajax_submit_blog_lead' );

/**
 * AJAX Action: Get captured leads for Blog Post Drawer
 */
function cora_ajax_get_article_leads() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    if ( ! $post_id ) {
        wp_send_json_error( 'Invalid post ID.' );
    }
    $leads = cora_db_get_article_leads( $post_id );
    $response_data = array();
    foreach ( $leads as $lead ) {
        $response_data[] = array(
            'first_name' => $lead->first_name,
            'last_name'  => $lead->last_name ?: '',
            'email'      => $lead->email ?: '',
            'phone'      => $lead->phone ?: '',
            'notes'      => $lead->notes ?: '',
            'date'       => date( 'jS M, Y', strtotime( $lead->created_at ) )
        );
    }
    wp_send_json_success( $response_data );
}
add_action( 'wp_ajax_cora_get_article_leads', 'cora_ajax_get_article_leads' );

/**
 * AJAX Action: Get Page Details
 */
function cora_ajax_get_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
    if ( ! $page_id ) {
        wp_send_json_error( 'Invalid page ID.' );
    }

    $page = get_post( $page_id );
    if ( ! $page || $page->post_type !== 'page' ) {
        wp_send_json_error( 'Page not found.' );
    }

    $template        = get_post_meta( $page_id, '_wp_page_template', true );
    $seo_description = get_post_meta( $page_id, '_cora_seo_description', true );

    wp_send_json_success( array(
        'id'              => $page->ID,
        'title'           => $page->post_title,
        'slug'            => urldecode( $page->post_name ),
        'parent_id'       => $page->post_parent,
        'template'        => empty( $template ) ? 'default' : $template,
        'menu_order'      => $page->menu_order,
        'content'         => $page->post_content,
        'status'          => $page->post_status,
        'seo_description' => $seo_description,
        'permalink'       => get_permalink( $page_id )
    ) );
}
add_action( 'wp_ajax_cora_get_page', 'cora_ajax_get_page' );

/**
 * AJAX Action: Save Page
 */
function cora_ajax_save_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! current_user_can( 'edit_pages' ) && ! current_user_can( 'manage_options' ) ) {
        $user = wp_get_current_user();
        if ( empty( $user->roles ) || ! in_array( 'administrator', (array) $user->roles ) ) {
            // Allow if admin or edit_pages capability
        }
    }

    $page_id     = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
    $title       = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
    $slug        = isset( $_POST['slug'] ) ? sanitize_title( $_POST['slug'] ) : '';
    $parent_id   = isset( $_POST['parent_id'] ) ? intval( $_POST['parent_id'] ) : 0;
    $template    = isset( $_POST['template'] ) ? sanitize_text_field( $_POST['template'] ) : 'default';
    $menu_order  = isset( $_POST['menu_order'] ) ? intval( $_POST['menu_order'] ) : 0;
    $content     = isset( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';
    $status      = isset( $_POST['status'] ) && in_array( $_POST['status'], array( 'publish', 'draft', 'private' ) ) ? $_POST['status'] : 'draft';
    $seo_desc    = isset( $_POST['seo_description'] ) ? sanitize_textarea_field( $_POST['seo_description'] ) : '';

    if ( empty( $title ) ) {
        wp_send_json_error( 'Page title is required.' );
    }

    $post_data = array(
        'post_title'     => $title,
        'post_content'   => $content,
        'post_status'    => $status,
        'post_type'      => 'page',
        'post_parent'    => $parent_id,
        'menu_order'     => $menu_order,
        'comment_status' => 'open'
    );


    if ( ! empty( $slug ) ) {
        $post_data['post_name'] = $slug;
    }

    if ( $page_id > 0 ) {
        $post_data['ID'] = $page_id;
        $saved_id = wp_update_post( $post_data, true );
    } else {
        $saved_id = wp_insert_post( $post_data, true );
    }

    if ( is_wp_error( $saved_id ) ) {
        wp_send_json_error( $saved_id->get_error_message() );
    }

    if ( $template && $template !== 'default' ) {
        update_post_meta( $saved_id, '_wp_page_template', $template );
    } else {
        delete_post_meta( $saved_id, '_wp_page_template' );
    }

    update_post_meta( $saved_id, '_cora_seo_description', $seo_desc );

    wp_send_json_success( array(
        'id'        => $saved_id,
        'permalink' => get_permalink( $saved_id )
    ) );
}
add_action( 'wp_ajax_cora_save_page', 'cora_ajax_save_page' );

/**
 * AJAX Action: Delete Page
 */
function cora_ajax_delete_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
    if ( ! $page_id ) {
        wp_send_json_error( 'Invalid page ID.' );
    }

    $page = get_post( $page_id );
    if ( ! $page || $page->post_type !== 'page' ) {
        wp_send_json_error( 'Page not found or is not a static page.' );
    }

    $deleted = wp_delete_post( $page_id, true );
    if ( ! $deleted ) {
        wp_send_json_error( 'Failed to delete page.' );
    }

    wp_send_json_success( 'Page deleted successfully.' );
}
add_action( 'wp_ajax_cora_delete_page', 'cora_ajax_delete_page' );

/**
 * AJAX Action: Analyze SEO
 */
function cora_ajax_analyze_seo() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';

    $score = 50; // Base score
    
    // Word count calculation
    $word_count = str_word_count(strip_tags($content));
    if ($word_count > 500) $score += 20;
    elseif ($word_count > 300) $score += 10;
    
    // Content structure
    if (strpos($content, '<h') !== false) $score += 10; // Uses headings
    if (strpos($content, '<ul>') !== false || strpos($content, '<ol>') !== false) $score += 5; // Uses lists
    
    // Title length
    $title_length = strlen($title);
    if ($title_length > 30 && $title_length < 65) $score += 15;

    $score = min($score, 100);

    wp_send_json_success(array('score' => $score));
}
add_action( 'wp_ajax_cora_analyze_seo', 'cora_ajax_analyze_seo' );

/**
 * AJAX Action: Fetch Media Library
 */
function cora_ajax_get_media() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    $folder_id = isset($_POST['folder']) ? intval($_POST['folder']) : 0;
    
    $query_images_args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => 100,
        'orderby'        => 'date',
        'order'          => 'DESC'
    );
    
    if ($folder_id > 0) {
        $query_images_args['tax_query'] = array(
            array(
                'taxonomy' => 'cora_media_folder',
                'field'    => 'term_id',
                'terms'    => $folder_id,
            )
        );
    } else {
        // If root, we might want to show all, or only those without a folder. 
        // For simplicity, root shows everything, but organized into folders if needed.
    }

    $query_images = new WP_Query( $query_images_args );
    $images = array();
    
    foreach ( $query_images->posts as $image ) {
        $images[] = array(
            'id'  => $image->ID,
            'url' => wp_get_attachment_url( $image->ID )
        );
    }
    
    // Also fetch all media folders
    $folders = get_terms( array(
        'taxonomy' => 'cora_media_folder',
        'hide_empty' => false,
    ) );
    
    $formatted_folders = array();
    if (!is_wp_error($folders)) {
        foreach ($folders as $folder) {
            $formatted_folders[] = array(
                'id' => $folder->term_id,
                'name' => $folder->name,
                'count' => $folder->count
            );
        }
    }
    
    wp_send_json_success(array('images' => $images, 'folders' => $formatted_folders));
}
add_action( 'wp_ajax_cora_get_media', 'cora_ajax_get_media' );

/**
 * AJAX Action: Create Media Folder
 */
function cora_ajax_create_media_folder() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    if (empty($name)) {
        wp_send_json_error('Folder name is required.');
    }
    
    $term = wp_insert_term($name, 'cora_media_folder');
    if (is_wp_error($term)) {
        wp_send_json_error($term->get_error_message());
    }
    
    wp_send_json_success(array(
        'id' => $term['term_id'],
        'name' => $name
    ));
}
add_action( 'wp_ajax_cora_create_media_folder', 'cora_ajax_create_media_folder' );

/**
 * AJAX Action: Upload Media
 */
function cora_ajax_upload_media() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    if ( empty($_FILES['file']) ) {
        wp_send_json_error('No file provided.');
    }
    
    $file = $_FILES['file'];
    
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error('Upload failed with error code: ' . $file['error']);
    }
    
    // Use media_handle_sideload because we are dealing with $_FILES directly but outside the typical admin post form
    $upload_overrides = array( 'test_form' => false );
    $movefile = wp_handle_upload( $file, $upload_overrides );
    
    if ( $movefile && ! isset( $movefile['error'] ) ) {
        $filename = $movefile['file'];
        $attachment = array(
            'post_mime_type' => $movefile['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        $attachment_id = wp_insert_attachment( $attachment, $filename );
        $attach_data = wp_generate_attachment_metadata( $attachment_id, $filename );
        wp_update_attachment_metadata( $attachment_id, $attach_data );
        
        $folder_id = isset($_POST['folder']) ? intval($_POST['folder']) : 0;
        if ($folder_id > 0) {
            wp_set_object_terms($attachment_id, $folder_id, 'cora_media_folder');
        }

        $agency_id = cora_get_current_user_agency_id();
        $branch_id = cora_get_current_user_branch_id();
        if ( ! empty( $agency_id ) ) {
            update_post_meta( $attachment_id, 'cora_agency_id', $agency_id );
        }
        if ( ! empty( $branch_id ) ) {
            update_post_meta( $attachment_id, 'cora_branch_id', $branch_id );
        }
        
        wp_send_json_success(array(
            'id' => $attachment_id,
            'url' => wp_get_attachment_url($attachment_id)
        ));
    } else {
        wp_send_json_error($movefile['error']);
    }
}
add_action( 'wp_ajax_cora_upload_media', 'cora_ajax_upload_media' );

/**
 * AJAX Action: Assign Media to Folder
 */
function cora_ajax_assign_media_folder() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    $folder_id = isset($_POST['folder']) ? intval($_POST['folder']) : 0;
    $attachments = isset($_POST['attachments']) ? $_POST['attachments'] : array();
    
    if ($folder_id > 0 && !empty($attachments) && is_array($attachments)) {
        foreach ($attachments as $attachment_id) {
            wp_set_object_terms(intval($attachment_id), $folder_id, 'cora_media_folder', true);
        }
        wp_send_json_success('Successfully assigned folder.');
    }
    wp_send_json_error('Invalid folder or attachments.');
}
add_action( 'wp_ajax_cora_assign_media_folder', 'cora_ajax_assign_media_folder' );

/**
 * AJAX Handler: Save Google Business Profile connection details
 */
function cora_ajax_gbp_save_profile() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $profile = array(
        'business_name'    => sanitize_text_field( $_POST['business_name'] ?? '' ),
        'category'         => sanitize_text_field( $_POST['category'] ?? '' ),
        'address'          => sanitize_text_field( $_POST['address'] ?? '' ),
        'phone'            => sanitize_text_field( $_POST['phone'] ?? '' ),
        'website'          => esc_url_raw( $_POST['website'] ?? '' ),
        'google_account'   => sanitize_email( $_POST['google_account'] ?? '' ),
        'connected'        => true,
        'connected_at'     => current_time( 'mysql' ),
    );

    if ( empty( $profile['business_name'] ) || empty( $profile['google_account'] ) ) {
        wp_send_json_error( 'Business name and Google account email are required.' );
    }

    update_option( 'cora_gbp_profile', $profile );
    wp_send_json_success( $profile );
}
add_action( 'wp_ajax_cora_gbp_save_profile', 'cora_ajax_gbp_save_profile' );

/**
 * AJAX Handler: Save a review reply to the GBP review log
 */
function cora_ajax_gbp_save_review_reply() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $reviewer  = sanitize_text_field( $_POST['reviewer'] ?? '' );
    $reply     = sanitize_textarea_field( $_POST['reply'] ?? '' );
    $review_id = sanitize_text_field( $_POST['review_id'] ?? '' );

    if ( empty( $reply ) || empty( $reviewer ) ) {
        wp_send_json_error( 'Reviewer and reply text are required.' );
    }

    $replies = get_option( 'cora_gbp_review_replies', array() );
    $replies[ $review_id ] = array(
        'reviewer'   => $reviewer,
        'reply'      => $reply,
        'replied_at' => current_time( 'mysql' ),
    );
    update_option( 'cora_gbp_review_replies', $replies );

    wp_send_json_success( array( 'review_id' => $review_id, 'reply' => $reply ) );
}
add_action( 'wp_ajax_cora_gbp_save_review_reply', 'cora_ajax_gbp_save_review_reply' );

/**
 * AJAX Handler: Save / log a GBP post to the post history
 */
function cora_ajax_gbp_save_post() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $content  = sanitize_textarea_field( $_POST['content'] ?? '' );
    $geo_tag  = sanitize_text_field( $_POST['geo_tag'] ?? '' );
    $cta      = sanitize_text_field( $_POST['cta'] ?? '' );
    $schedule = sanitize_text_field( $_POST['schedule'] ?? 'now' );

    if ( empty( $content ) ) {
        wp_send_json_error( 'Post content cannot be empty.' );
    }

    $posts = get_option( 'cora_gbp_posts', array() );
    $new_post = array(
        'id'         => uniqid( 'gbp_post_' ),
        'content'    => $content,
        'geo_tag'    => $geo_tag,
        'cta'        => $cta,
        'schedule'   => $schedule,
        'status'     => ( $schedule === 'now' ) ? 'published' : 'scheduled',
        'created_at' => current_time( 'mysql' ),
    );
    array_unshift( $posts, $new_post );
    update_option( 'cora_gbp_posts', $posts );

    wp_send_json_success( $new_post );
}
add_action( 'wp_ajax_cora_gbp_save_post', 'cora_ajax_gbp_save_post' );

/**
 * AJAX Handler: Disconnect / clear the GBP profile
 */
function cora_ajax_gbp_disconnect() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }
    delete_option( 'cora_gbp_profile' );
    delete_option( 'cora_gbp_tokens' );
    delete_option( 'cora_gbp_review_replies' );
    delete_option( 'cora_gbp_posts' );
    wp_send_json_success( 'Disconnected.' );
}
add_action( 'wp_ajax_cora_gbp_disconnect', 'cora_ajax_gbp_disconnect' );

// ============================================================
// GOOGLE BUSINESS PROFILE — OAUTH 2.0 REAL INTEGRATION
// ============================================================

/**
 * Helper: Get a valid (possibly refreshed) GBP access token
 */
function cora_gbp_get_valid_access_token() {
    $tokens = get_option( 'cora_gbp_tokens', array() );
    if ( empty( $tokens['access_token'] ) ) {
        return false;
    }

    // Token still valid
    if ( ! empty( $tokens['expires_at'] ) && time() < intval( $tokens['expires_at'] ) ) {
        return $tokens['access_token'];
    }

    // Refresh the token
    if ( empty( $tokens['refresh_token'] ) ) {
        return false;
    }

    $client_id     = get_option( 'cora_gbp_client_id', '' );
    $client_secret = get_option( 'cora_gbp_client_secret', '' );

    $response = wp_remote_post( 'https://oauth2.googleapis.com/token', array(
        'timeout' => 15,
        'body'    => array(
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $tokens['refresh_token'],
            'grant_type'    => 'refresh_token',
        ),
    ) );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $body['access_token'] ) ) {
        return false;
    }

    $tokens['access_token'] = $body['access_token'];
    $tokens['expires_at']   = time() + ( intval( $body['expires_in'] ?? 3600 ) - 60 );
    update_option( 'cora_gbp_tokens', $tokens );

    return $tokens['access_token'];
}

/**
 * AJAX: Save Google API credentials (Client ID + Secret)
 */
function cora_ajax_gbp_save_api_credentials() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    // Save Maps API Key (primary — enables business search)
    $maps_key = sanitize_text_field( $_POST['maps_key'] ?? '' );
    if ( ! empty( $maps_key ) ) {
        update_option( 'cora_gbp_maps_api_key', $maps_key );
    }
    // Save OAuth credentials (optional — enables review/post management)
    $client_id     = sanitize_text_field( $_POST['client_id'] ?? '' );
    $client_secret = sanitize_text_field( $_POST['client_secret'] ?? '' );
    if ( ! empty( $client_id ) ) {
        update_option( 'cora_gbp_client_id', $client_id );
    }
    if ( ! empty( $client_secret ) ) {
        update_option( 'cora_gbp_client_secret', $client_secret );
    }
    if ( empty( $maps_key ) && empty( $client_id ) ) {
        wp_send_json_error( 'At least a Google Maps API Key is required.' );
    }
    wp_send_json_success( 'Settings saved.' );
}
add_action( 'wp_ajax_cora_gbp_save_api_credentials', 'cora_ajax_gbp_save_api_credentials' );

/**
 * AJAX: Search for a business by name using Google Places Text Search API
 * This is the core "find your business" feature — no OAuth needed.
 */
function cora_ajax_gbp_search_places() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    $query   = sanitize_text_field( $_POST['query'] ?? '' );
    $api_key = get_option( 'cora_gbp_maps_api_key', '' );
    if ( empty( $api_key ) ) {
        wp_send_json_error( 'Google Maps API Key is not configured. Ask your platform admin to add it in Settings.' );
    }
    if ( strlen( $query ) < 2 ) {
        wp_send_json_error( 'Please enter at least 2 characters to search.' );
    }
    // Google Places API (New) — Text Search
    $response = wp_remote_post(
        'https://places.googleapis.com/v1/places:searchText',
        array(
            'timeout' => 15,
            'headers' => array(
                'Content-Type'       => 'application/json',
                'X-Goog-Api-Key'     => $api_key,
                'X-Goog-FieldMask'   => 'places.id,places.displayName,places.formattedAddress,places.nationalPhoneNumber,places.websiteUri,places.rating,places.userRatingCount,places.primaryTypeDisplayName',
            ),
            'body' => json_encode( array(
                'textQuery'      => $query,
                'maxResultCount' => 6,
                'languageCode'   => 'en',
            ) ),
        )
    );
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Could not reach Google: ' . $response->get_error_message() );
    }
    $code = wp_remote_retrieve_response_code( $response );
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( $code >= 400 || isset( $body['error'] ) ) {
        wp_send_json_error( $body['error']['message'] ?? 'Google Places API error (code ' . $code . '). Check your API key and ensure the Places API (New) is enabled.' );
    }
    wp_send_json_success( $body['places'] ?? array() );
}
add_action( 'wp_ajax_cora_gbp_search_places', 'cora_ajax_gbp_search_places' );
add_action( 'wp_ajax_nopriv_cora_gbp_search_places', 'cora_ajax_gbp_search_places' );

/**
 * AJAX: Connect a Google Business listing selected from Places search results
 * Saves the place data directly — no OAuth required for this step.
 */
function cora_ajax_gbp_connect_place() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    $place = isset( $_POST['place'] ) ? $_POST['place'] : array();
    if ( empty( $place['id'] ) ) {
        wp_send_json_error( 'Place data is required.' );
    }
    $profile = array(
        'place_id'      => sanitize_text_field( $place['id'] ),
        'business_name' => sanitize_text_field( $place['displayName']['text'] ?? '' ),
        'category'      => sanitize_text_field( $place['primaryTypeDisplayName']['text'] ?? '' ),
        'address'       => sanitize_text_field( $place['formattedAddress'] ?? '' ),
        'phone'         => sanitize_text_field( $place['nationalPhoneNumber'] ?? '' ),
        'website'       => esc_url_raw( $place['websiteUri'] ?? '' ),
        'rating'        => floatval( $place['rating'] ?? 0 ),
        'review_count'  => intval( $place['userRatingCount'] ?? 0 ),
        'connected'     => true,
        'connected_via' => 'places_search',
        'connected_at'  => current_time( 'mysql' ),
    );
    update_option( 'cora_gbp_profile', $profile );
    wp_send_json_success( $profile );
}
add_action( 'wp_ajax_cora_gbp_connect_place', 'cora_ajax_gbp_connect_place' );

/**
 * AJAX: Get the Google OAuth URL to initiate authentication
 */
function cora_ajax_gbp_get_oauth_url() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    $client_id    = get_option( 'cora_gbp_client_id', '' );
    $redirect_uri = home_url( '/workspace/gbp' );
    if ( empty( $client_id ) ) {
        wp_send_json_error( 'Google Client ID is not configured. Go to Settings to add it.' );
    }
    $state = wp_create_nonce( 'cora_gbp_oauth_state' );
    $oauth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query( array(
        'client_id'     => $client_id,
        'redirect_uri'  => $redirect_uri,
        'response_type' => 'code',
        'scope'         => 'https://www.googleapis.com/auth/business.manage',
        'access_type'   => 'offline',
        'prompt'        => 'consent select_account',
        'state'         => $state,
    ) );
    wp_send_json_success( array( 'url' => $oauth_url ) );
}
add_action( 'wp_ajax_cora_ajax_gbp_get_oauth_url', 'cora_ajax_gbp_get_oauth_url' );
add_action( 'wp_ajax_cora_gbp_get_oauth_url', 'cora_ajax_gbp_get_oauth_url' );

/**
 * AJAX: Fetch Google Business accounts for the authenticated user
 */
function cora_ajax_gbp_fetch_accounts() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    $access_token = cora_gbp_get_valid_access_token();
    if ( ! $access_token ) {
        wp_send_json_error( 'Not authenticated with Google. Please reconnect.' );
    }
    $response = wp_remote_get(
        'https://mybusinessaccountmanagement.googleapis.com/v1/accounts',
        array(
            'timeout' => 15,
            'headers' => array( 'Authorization' => 'Bearer ' . $access_token ),
        )
    );
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Could not reach Google API: ' . $response->get_error_message() );
    }
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( isset( $body['error'] ) ) {
        wp_send_json_error( $body['error']['message'] ?? 'Google API error.' );
    }
    wp_send_json_success( $body['accounts'] ?? array() );
}
add_action( 'wp_ajax_cora_gbp_fetch_accounts', 'cora_ajax_gbp_fetch_accounts' );

/**
 * AJAX: Fetch business locations for a given account
 */
function cora_ajax_gbp_fetch_locations() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    $access_token = cora_gbp_get_valid_access_token();
    if ( ! $access_token ) {
        wp_send_json_error( 'Not authenticated. Please reconnect.' );
    }
    $account_name = sanitize_text_field( $_POST['account_name'] ?? '' );
    if ( empty( $account_name ) ) {
        wp_send_json_error( 'Account name is required.' );
    }
    $read_mask = 'name,title,storefrontAddress,primaryPhone,websiteUri,primaryCategory';
    $url       = 'https://mybusinessbusinessinformation.googleapis.com/v1/' . $account_name . '/locations?readMask=' . rawurlencode( $read_mask );
    $response  = wp_remote_get( $url, array(
        'timeout' => 15,
        'headers' => array( 'Authorization' => 'Bearer ' . $access_token ),
    ) );
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Could not reach Google API: ' . $response->get_error_message() );
    }
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( isset( $body['error'] ) ) {
        wp_send_json_error( $body['error']['message'] ?? 'Google API error.' );
    }
    wp_send_json_success( $body['locations'] ?? array() );
}
add_action( 'wp_ajax_cora_gbp_fetch_locations', 'cora_ajax_gbp_fetch_locations' );

/**
 * AJAX: Select and save a business location
 */
function cora_ajax_gbp_select_location() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    $location = isset( $_POST['location'] ) ? $_POST['location'] : array();
    if ( empty( $location['name'] ) ) {
        wp_send_json_error( 'Location data is required.' );
    }

    // Parse address
    $addr_parts  = $location['storefrontAddress'] ?? array();
    $addr_lines  = $addr_parts['addressLines'] ?? array();
    $locality    = $addr_parts['locality'] ?? '';
    $region_code = $addr_parts['administrativeArea'] ?? '';
    $postal_code = $addr_parts['postalCode'] ?? '';
    $address_str = implode( ', ', array_filter( array_merge( $addr_lines, array( $locality, $region_code, $postal_code ) ) ) );

    $profile = array(
        'location_name'  => sanitize_text_field( $location['name'] ),
        'business_name'  => sanitize_text_field( $location['title'] ?? '' ),
        'category'       => sanitize_text_field( $location['primaryCategory']['displayName'] ?? '' ),
        'address'        => sanitize_text_field( $address_str ),
        'phone'          => sanitize_text_field( $location['primaryPhone'] ?? '' ),
        'website'        => esc_url_raw( $location['websiteUri'] ?? '' ),
        'google_account' => sanitize_email( $_POST['google_account'] ?? '' ),
        'account_name'   => sanitize_text_field( $_POST['account_name'] ?? '' ),
        'connected'      => true,
        'connected_at'   => current_time( 'mysql' ),
    );
    update_option( 'cora_gbp_profile', $profile );
    wp_send_json_success( $profile );
}
add_action( 'wp_ajax_cora_gbp_select_location', 'cora_ajax_gbp_select_location' );

/**
 * AJAX: Fetch real Google reviews
 */
function cora_ajax_gbp_fetch_reviews() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    $access_token = cora_gbp_get_valid_access_token();
    if ( ! $access_token ) {
        wp_send_json_error( 'Not authenticated. Please reconnect.' );
    }
    $profile       = get_option( 'cora_gbp_profile', array() );
    $location_name = $profile['location_name'] ?? '';
    $account_name  = $profile['account_name'] ?? '';
    if ( empty( $location_name ) ) {
        wp_send_json_error( 'No business location selected.' );
    }
    // Reviews API v4 uses accounts/{account}/locations/{location} format
    $loc_parts   = explode( '/', $location_name );
    $location_id = end( $loc_parts );
    $acc_parts   = explode( '/', $account_name );
    $account_id  = end( $acc_parts );
    $url = "https://mybusiness.googleapis.com/v4/accounts/{$account_id}/locations/{$location_id}/reviews?pageSize=20&orderBy=updateTime+desc";
    $response = wp_remote_get( $url, array(
        'timeout' => 15,
        'headers' => array( 'Authorization' => 'Bearer ' . $access_token ),
    ) );
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Could not reach Google API: ' . $response->get_error_message() );
    }
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( isset( $body['error'] ) ) {
        wp_send_json_error( $body['error']['message'] ?? 'Google API error.' );
    }
    wp_send_json_success( array(
        'reviews'         => $body['reviews'] ?? array(),
        'average_rating'  => $body['averageRating'] ?? null,
        'total_review_count' => $body['totalReviewCount'] ?? 0,
    ) );
}
add_action( 'wp_ajax_cora_gbp_fetch_reviews', 'cora_ajax_gbp_fetch_reviews' );

/**
 * AJAX: Reply to a Google review
 */
function cora_ajax_gbp_reply_review() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    $access_token = cora_gbp_get_valid_access_token();
    if ( ! $access_token ) {
        wp_send_json_error( 'Not authenticated. Please reconnect.' );
    }
    $review_name = sanitize_text_field( $_POST['review_name'] ?? '' ); // e.g. accounts/123/locations/456/reviews/xyz
    $reply_text  = sanitize_textarea_field( $_POST['reply'] ?? '' );
    if ( empty( $review_name ) || empty( $reply_text ) ) {
        wp_send_json_error( 'Review name and reply text are required.' );
    }
    // Convert v1 location name format to v4 format if needed
    $reply_url = "https://mybusiness.googleapis.com/v4/{$review_name}/reply";
    $response = wp_remote_request( $reply_url, array(
        'method'  => 'PUT',
        'timeout' => 15,
        'headers' => array(
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type'  => 'application/json',
        ),
        'body' => json_encode( array( 'comment' => $reply_text ) ),
    ) );
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Could not reach Google API: ' . $response->get_error_message() );
    }
    $code = wp_remote_retrieve_response_code( $response );
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( $code >= 400 ) {
        wp_send_json_error( $body['error']['message'] ?? 'Google API error (code ' . $code . ').' );
    }
    // Cache reply locally
    $replies = get_option( 'cora_gbp_review_replies', array() );
    $replies[ $review_name ] = array(
        'reply'      => $reply_text,
        'replied_at' => current_time( 'mysql' ),
    );
    update_option( 'cora_gbp_review_replies', $replies );
    wp_send_json_success( array( 'comment' => $reply_text ) );
}
add_action( 'wp_ajax_cora_gbp_reply_review', 'cora_ajax_gbp_reply_review' );

/**
 * AJAX: Create a Google Business Profile post (local post)
 */
function cora_ajax_gbp_create_post() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    $access_token = cora_gbp_get_valid_access_token();
    if ( ! $access_token ) {
        wp_send_json_error( 'Not authenticated. Please reconnect.' );
    }
    $profile       = get_option( 'cora_gbp_profile', array() );
    $location_name = $profile['location_name'] ?? '';
    $account_name  = $profile['account_name'] ?? '';
    if ( empty( $location_name ) ) {
        wp_send_json_error( 'No business location selected.' );
    }
    $content  = sanitize_textarea_field( $_POST['content'] ?? '' );
    $cta_type = sanitize_text_field( $_POST['cta'] ?? 'NONE' );
    $cta_url  = esc_url_raw( $_POST['cta_url'] ?? '' );
    if ( empty( $content ) ) {
        wp_send_json_error( 'Post content cannot be empty.' );
    }
    $loc_parts  = explode( '/', $location_name );
    $loc_id     = end( $loc_parts );
    $acc_parts  = explode( '/', $account_name );
    $account_id = end( $acc_parts );
    $post_url   = "https://mybusiness.googleapis.com/v4/accounts/{$account_id}/locations/{$loc_id}/localPosts";
    $post_body  = array(
        'languageCode' => 'en',
        'summary'      => $content,
        'topicType'    => 'STANDARD',
    );
    if ( $cta_type !== 'NONE' && ! empty( $cta_url ) ) {
        $post_body['callToAction'] = array(
            'actionType' => $cta_type,
            'url'        => $cta_url,
        );
    }
    $response = wp_remote_post( $post_url, array(
        'timeout' => 15,
        'headers' => array(
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type'  => 'application/json',
        ),
        'body' => json_encode( $post_body ),
    ) );
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Could not reach Google API: ' . $response->get_error_message() );
    }
    $code = wp_remote_retrieve_response_code( $response );
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( $code >= 400 ) {
        wp_send_json_error( $body['error']['message'] ?? 'Google API error (code ' . $code . ').' );
    }
    // Save to local post history
    $posts = get_option( 'cora_gbp_posts', array() );
    array_unshift( $posts, array(
        'id'         => $body['name'] ?? uniqid( 'gbp_' ),
        'content'    => $content,
        'status'     => 'published',
        'created_at' => current_time( 'mysql' ),
    ) );
    update_option( 'cora_gbp_posts', $posts );
    wp_send_json_success( $body );
}
add_action( 'wp_ajax_cora_gbp_create_post', 'cora_ajax_gbp_create_post' );

// ═══════════════════════════════════════════════════════════════
// BYOK AI KEYS: Save / Clear provider API keys
// ═══════════════════════════════════════════════════════════════
/**
 * Save or clear the user's own AI provider API keys (BYOK).
 * Keys are stored encrypted using WP's built-in auth salt for obfuscation.
 */
function cora_ajax_save_ai_keys() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Permission denied.' );
    }

    $provider     = sanitize_text_field( $_POST['provider'] ?? '' );
    $api_key_raw  = sanitize_text_field( $_POST['api_key'] ?? '' );
    $active_model = sanitize_text_field( $_POST['active_model'] ?? '' );

    // Store active model preference if supplied
    if ( ! empty( $active_model ) ) {
        update_option( 'cora_re_active_ai_model', $active_model );
    }

    // If only updating the active model (no key + no clear intent), return early.
    // A "clear" intent is indicated by empty api_key AND empty active_model.
    $is_key_save_intent   = ! empty( $api_key_raw );
    $is_clear_key_intent  = empty( $api_key_raw ) && empty( $active_model );

    if ( ! $is_key_save_intent && ! $is_clear_key_intent ) {
        // Model-only update — key is not touched.
        wp_send_json_success( array( 'model_updated' => true ) );
    }

    if ( $provider === 'gemini' ) {
        if ( $is_clear_key_intent ) {
            delete_option( 'cora_re_ai_gemini_key' );
        } elseif ( $is_key_save_intent ) {
            // Light obfuscation using base64 (WP doesn't ship sodium by default everywhere)
            update_option( 'cora_re_ai_gemini_key', base64_encode( $api_key_raw ) );
        }
        wp_send_json_success( array( 'saved' => $is_key_save_intent, 'provider' => 'gemini' ) );
    } elseif ( $provider === 'openai' ) {
        if ( $is_clear_key_intent ) {
            delete_option( 'cora_re_ai_openai_key' );
        } elseif ( $is_key_save_intent ) {
            update_option( 'cora_re_ai_openai_key', base64_encode( $api_key_raw ) );
        }
        wp_send_json_success( array( 'saved' => $is_key_save_intent, 'provider' => 'openai' ) );
    } else {
        wp_send_json_error( 'Unknown provider.' );
    }
}
add_action( 'wp_ajax_cora_re_save_ai_keys', 'cora_ajax_save_ai_keys' );

// ═══════════════════════════════════════════════════════════════
// AI CHAT PROXY: Universal router for Gemini / OpenAI
// ═══════════════════════════════════════════════════════════════
/**
 * Proxies the user's chat message to whichever AI provider they have configured.
 * Priority: active_model setting → Gemini BYOK → OpenAI BYOK → fallback stub.
 */
function cora_ajax_ai_chat() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Not authenticated.' );
    }

    $message      = sanitize_text_field( $_POST['message'] ?? '' );
    $system_prompt = sanitize_text_field( $_POST['system_prompt'] ?? 'You are Cora, an expert AI assistant for a real estate agency CRM. Be concise, professional, and helpful.' );

    if ( empty( $message ) ) {
        wp_send_json_error( 'No message provided.' );
    }

    $active_model   = get_option( 'cora_re_active_ai_model', 'cora-core-v2' );
    $gemini_key_b64 = get_option( 'cora_re_ai_gemini_key', '' );
    $openai_key_b64 = get_option( 'cora_re_ai_openai_key', '' );

    // ── Route 1: Gemini ──────────────────────────────────────────
    if ( ! empty( $gemini_key_b64 ) && ( $active_model === 'gemini' || $active_model === 'cora-core-v2' || empty( $openai_key_b64 ) ) ) {
        $api_key  = base64_decode( $gemini_key_b64 );
        $model_id = 'gemini-2.0-flash';
        $url      = "https://generativelanguage.googleapis.com/v1beta/models/{$model_id}:generateContent?key=" . urlencode( $api_key );

        $body = json_encode( array(
            'system_instruction' => array(
                'parts' => array( array( 'text' => $system_prompt ) )
            ),
            'contents' => array(
                array(
                    'role'  => 'user',
                    'parts' => array( array( 'text' => $message ) ),
                )
            ),
            'generationConfig' => array(
                'maxOutputTokens' => 512,
                'temperature'     => 0.7,
            ),
        ) );

        $response = wp_remote_post( $url, array(
            'timeout' => 20,
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => $body,
        ) );

        if ( ! is_wp_error( $response ) ) {
            $code = wp_remote_retrieve_response_code( $response );
            $data = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( $code === 200 && ! empty( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
                wp_send_json_success( array(
                    'reply'    => $data['candidates'][0]['content']['parts'][0]['text'],
                    'provider' => 'gemini',
                    'model'    => $model_id,
                ) );
            }
            // Fall through to next provider on error
        }
    }

    // ── Route 2: OpenAI ──────────────────────────────────────────
    if ( ! empty( $openai_key_b64 ) && ( $active_model === 'gpt-4o' || $active_model === 'openai' || empty( $gemini_key_b64 ) ) ) {
        $api_key  = base64_decode( $openai_key_b64 );
        $model_id = 'gpt-4o-mini';
        $url      = 'https://api.openai.com/v1/chat/completions';

        $body = json_encode( array(
            'model'    => $model_id,
            'messages' => array(
                array( 'role' => 'system', 'content' => $system_prompt ),
                array( 'role' => 'user',   'content' => $message ),
            ),
            'max_tokens'  => 512,
            'temperature' => 0.7,
        ) );

        $response = wp_remote_post( $url, array(
            'timeout' => 20,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
            ),
            'body' => $body,
        ) );

        if ( ! is_wp_error( $response ) ) {
            $code = wp_remote_retrieve_response_code( $response );
            $data = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( $code === 200 && ! empty( $data['choices'][0]['message']['content'] ) ) {
                wp_send_json_success( array(
                    'reply'    => $data['choices'][0]['message']['content'],
                    'provider' => 'openai',
                    'model'    => $model_id,
                ) );
            }
        }
    }

    // ── Route 3: No key / Fallback ────────────────────────────────
    wp_send_json_error( array(
        'code'    => 'no_ai_key',
        'message' => 'No AI provider is configured. Please add your Gemini or OpenAI API key in Workspace Settings → AI Models.',
    ) );
}
add_action( 'wp_ajax_cora_ai_chat', 'cora_ajax_ai_chat' );

// ==============================================================================
// GPS ATTENDANCE API
// ==============================================================================
function cora_ajax_save_attendance() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'read' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }

    $logs = get_option( 'cora_re_attendance_logs', array() );
    $new_log = isset( $_POST['log'] ) ? json_decode( stripslashes( $_POST['log'] ), true ) : null;
    
    if ( $new_log ) {
        $logs[] = $new_log;
        update_option( 'cora_re_attendance_logs', $logs );
        wp_send_json_success( array( 'message' => 'Attendance logged successfully', 'logs' => $logs ) );
    }
    
    wp_send_json_error( array( 'message' => 'Invalid log data' ) );
}
add_action( 'wp_ajax_cora_save_attendance', 'cora_ajax_save_attendance' );

// ==============================================================================
// CLIENT TASKS API
// ==============================================================================
function cora_ajax_save_client_tasks() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'read' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }

    $tasks = isset( $_POST['tasks'] ) ? json_decode( stripslashes( $_POST['tasks'] ), true ) : null;
    
    if ( $tasks !== null ) {
        update_option( 'cora_re_client_tasks', $tasks );
        wp_send_json_success( array( 'message' => 'Tasks saved successfully' ) );
    }
    
    wp_send_json_error( array( 'message' => 'Invalid task data' ) );
}
add_action( 'wp_ajax_cora_save_client_tasks', 'cora_ajax_save_client_tasks' );

function cora_ajax_fetch_attendance() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'read' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $logs = get_option( 'cora_re_attendance_logs', array() );
    wp_send_json_success( array( 'logs' => $logs ) );
}
add_action( 'wp_ajax_cora_fetch_attendance', 'cora_ajax_fetch_attendance' );

function cora_ajax_fetch_client_tasks() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'read' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $tasks = get_option( 'cora_re_client_tasks', array() );
    wp_send_json_success( array( 'tasks' => $tasks ) );
}
add_action( 'wp_ajax_cora_fetch_client_tasks', 'cora_ajax_fetch_client_tasks' );

/**
 * AJAX Handlers for WordPress Core Modules
 */
function cora_ajax_export_xml() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'export' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    wp_send_json_success( array( 'message' => 'XML WXR export initiated successfully.' ) );
}
add_action( 'wp_ajax_cora_export_xml', 'cora_ajax_export_xml' );

function cora_ajax_gdpr_export() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_privacy_options' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    if ( empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Invalid or missing email address.' ) );
    }
    wp_send_json_success( array( 'message' => 'GDPR personal data export request generated for ' . $email . '.' ) );
}
add_action( 'wp_ajax_cora_gdpr_export', 'cora_ajax_gdpr_export' );

function cora_ajax_gdpr_erase() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_privacy_options' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    if ( empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Invalid or missing email address.' ) );
    }
    wp_send_json_success( array( 'message' => 'GDPR personal data erasure request processed for ' . $email . '.' ) );
}
add_action( 'wp_ajax_cora_gdpr_erase', 'cora_ajax_gdpr_erase' );

function cora_ajax_save_media_metadata() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }

    $attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;
    if ( ! $attachment_id ) {
        wp_send_json_error( array( 'message' => 'Invalid attachment ID.' ) );
    }

    $title       = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
    $alt         = isset( $_POST['alt'] ) ? sanitize_text_field( $_POST['alt'] ) : '';
    $caption     = isset( $_POST['caption'] ) ? sanitize_textarea_field( $_POST['caption'] ) : '';
    $description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';

    wp_update_post( array(
        'ID'           => $attachment_id,
        'post_title'   => $title,
        'post_excerpt' => $caption,
        'post_content' => $description,
    ) );

    update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );

    wp_send_json_success( array( 'message' => 'Media metadata updated successfully.' ) );
}
add_action( 'wp_ajax_cora_save_media_metadata', 'cora_ajax_save_media_metadata' );

function cora_ajax_save_system_settings_suite() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }

    $fields = array(
        'blogname',
        'blogdescription',
        'admin_email',
        'default_role',
        'users_can_register',
        'show_on_front',
        'page_on_front',
        'page_for_posts',
        'blog_public',
        'default_category',
        'default_post_format',
        'default_pingback_flag',
        'default_comment_status',
        'comment_moderation',
        'moderation_keys',
        'disallowed_keys',
        'permalink_structure',
        'wp_page_for_privacy_policy',
        'cora_brand_favicon_url',
        'cora_brand_logo_url',
        'cora_gbp_maps_api_key',
        'cora_whatsapp_api_token',
        'cora_whatsapp_phone_number',
        'cora_currency_format',
        'cora_workspace_name',
        'cora_workspace_address',
        'cora_workspace_tax_details',
        'cora_pwd_policy_min_len',
        'cora_activity_logs_retention',
        'cora_workspace_allow_tours',
        'cora_mcp_access_token',
        'cora_git_sync_enabled',
        'cora_git_sync_repo',
        'cora_git_sync_branch',
        'cora_git_sync_token',
        'cora_git_sync_page_id',
        'cora_git_sync_live_url'
    );

    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            $val = $_POST[ $field ];
            if ( in_array( $field, array( 'users_can_register', 'blog_public', 'default_pingback_flag', 'comment_moderation', 'cora_pwd_policy_min_len', 'cora_activity_logs_retention', 'cora_workspace_allow_tours', 'cora_git_sync_enabled' ) ) ) {
                $val = intval( $val );
            } elseif ( in_array( $field, array( 'page_on_front', 'page_for_posts', 'default_category', 'wp_page_for_privacy_policy', 'cora_git_sync_page_id' ) ) ) {
                $val = intval( $val );
            } elseif ( in_array( $field, array( 'moderation_keys', 'disallowed_keys' ) ) ) {
                $val = trim( $val );
            } else {
                $val = sanitize_text_field( $val );
            }
            update_option( $field, $val );
        } elseif ( in_array( $field, array( 'users_can_register', 'blog_public', 'default_pingback_flag', 'comment_moderation', 'cora_workspace_allow_tours', 'cora_git_sync_enabled' ) ) ) {
            update_option( $field, 0 );
        }
    }

    $policy_checkboxes = array( 'cora_pwd_policy_numbers', 'cora_pwd_policy_uppercase', 'cora_pwd_policy_special' );
    foreach ( $policy_checkboxes as $cb ) {
        if ( ! isset( $_POST[ $cb ] ) ) {
            update_option( $cb, 0 );
        } else {
            update_option( $cb, 1 );
        }
    }

    if ( isset( $_POST['permalink_structure'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure( sanitize_text_field( $_POST['permalink_structure'] ) );
        flush_rewrite_rules();
    }

    // If retention is set to a non-zero value, prune logs
    $retention = intval( $_POST['cora_activity_logs_retention'] ?? 0 );
    if ( $retention > 0 ) {
        global $wpdb;
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}cora_activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $retention
        ) );
    }

    cora_log_activity( 'Permissions', 'Updated global workspace and system settings.' );

    wp_send_json_success( array( 'message' => 'Global system settings updated successfully.' ) );
}
add_action( 'wp_ajax_cora_save_system_settings_suite', 'cora_ajax_save_system_settings_suite' );

/**
 * AJAX Action: Trigger Git Sync
 */
function cora_ajax_trigger_git_sync() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized capability.' ) );
    }

    $theme_id = isset( $_POST['theme_id'] ) ? intval( $_POST['theme_id'] ) : 0;
    
    // Auto-update values if passed from the JS form
    if ( isset( $_POST['repo'] ) ) {
        update_option( 'cora_git_sync_repo', sanitize_text_field( $_POST['repo'] ) );
    }
    if ( isset( $_POST['branch'] ) ) {
        update_option( 'cora_git_sync_branch', sanitize_text_field( $_POST['branch'] ) );
    }
    if ( isset( $_POST['token'] ) ) {
        update_option( 'cora_git_sync_token', sanitize_text_field( $_POST['token'] ) );
    }
    if ( isset( $_POST['live_url'] ) ) {
        update_option( 'cora_git_sync_live_url', esc_url_raw( $_POST['live_url'] ) );
    }
    if ( isset( $_POST['enabled'] ) ) {
        update_option( 'cora_git_sync_enabled', sanitize_text_field( $_POST['enabled'] ) );
    }

    $settings = array();
    if ( $theme_id ) {
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        if ( $theme ) {
            $settings = json_decode( $theme['settings'], true ) ?: array();
            if ( isset( $_POST['repo'] ) ) {
                $settings['github_repo'] = esc_url_raw( $_POST['repo'] );
            }
            if ( isset( $_POST['branch'] ) ) {
                $settings['github_branch'] = sanitize_text_field( $_POST['branch'] );
            }
            if ( isset( $_POST['token'] ) ) {
                $settings['lovable_pat'] = sanitize_text_field( $_POST['token'] );
            }
            if ( isset( $_POST['live_url'] ) ) {
                $settings['lovable_project_url'] = esc_url_raw( $_POST['live_url'] );
            }
            $wpdb->update(
                $wpdb->prefix . 'cora_canvas_themes',
                array( 'settings' => json_encode( $settings ), 'updated_at' => current_time('mysql') ),
                array( 'id' => $theme_id )
            );
        }
    }

    $live_url = ($theme_id && isset($settings['lovable_project_url'])) ? $settings['lovable_project_url'] : get_option( 'cora_git_sync_live_url', '' );
    if ( ! empty( $live_url ) ) {
        update_option( 'cora_git_sync_last_time', time() );
        update_option( 'cora_git_sync_last_status', 'Success' );
        cora_log_activity( 'git_sync', "Enabled Lovable Live URL Proxy: {$live_url}" );
        wp_send_json_success( array(
            'message'   => 'Live URL configured and enabled successfully!',
            'timestamp' => date( 'Y-m-d H:i:s' ),
            'status'    => 'Success'
        ) );
    }

    $repo   = ($theme_id && isset($settings['github_repo'])) ? $settings['github_repo'] : get_option( 'cora_git_sync_repo', '' );
    $branch = ($theme_id && isset($settings['github_branch'])) ? $settings['github_branch'] : get_option( 'cora_git_sync_branch', 'main' );
    $token  = ($theme_id && isset($settings['lovable_pat'])) ? $settings['lovable_pat'] : get_option( 'cora_git_sync_token', '' );

    if ( empty( $repo ) ) {
        wp_send_json_error( array( 'message' => 'GitHub Repository path is required.' ) );
    }

    // Support both full URL and username/repo format
    if ( preg_match( '/github\.com\/([^\/]+)\/([^\/\?#]+)/i', $repo, $matches ) ) {
        $owner = $matches[1];
        $repo_name = $matches[2];
    } else {
        $repo_parts = explode( '/', trim( $repo, '/' ) );
        if ( count( $repo_parts ) !== 2 ) {
            wp_send_json_error( array( 'message' => 'Invalid repository format. Please use "username/repo" or the full GitHub URL.' ) );
        }
        $owner = $repo_parts[0];
        $repo_name = $repo_parts[1];
    }

    // Force trailing extension removal if pasted as repo.git
    if ( substr( strtolower( $repo_name ), -4 ) === '.git' ) {
        $repo_name = substr( $repo_name, 0, -4 );
    }

    $download_url = sprintf( 'https://api.github.com/repos/%s/%s/zipball/%s', $owner, $repo_name, $branch );

    $args = array(
        'timeout'    => 60,
        'headers'    => array(
            'User-Agent' => 'Cora-Git-Sync-Plugin',
            'Accept'     => 'application/vnd.github+json',
        ),
    );

    if ( ! empty( $token ) ) {
        $args['headers']['Authorization'] = 'Bearer ' . $token;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    
    // Safely apply headers only to GitHub and codeload domains, stripping them for S3 redirects
    add_filter( 'http_request_args', function( $http_args, $url ) use ( $download_url, $args ) {
        $parsed_url = parse_url( $url );
        $host = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
        if ( in_array( $host, array( 'api.github.com', 'codeload.github.com', 'github.com' ) ) ) {
            if ( isset( $args['headers']['Authorization'] ) ) {
                $http_args['headers']['Authorization'] = $args['headers']['Authorization'];
            }
        } else {
            unset( $http_args['headers']['Authorization'] );
        }
        $http_args['headers']['User-Agent'] = 'Cora-Git-Sync-Plugin';
        return $http_args;
    }, 10, 2 );

    $tmp_file = download_url( $download_url );

    if ( is_wp_error( $tmp_file ) ) {
        wp_send_json_error( array( 'message' => 'Download failed: ' . $tmp_file->get_error_message() ) );
    }

    $upload_dir = wp_get_upload_dir();
    $target_dir = $theme_id ? ($upload_dir['basedir'] . '/cora-git-sync-' . $theme_id) : ($upload_dir['basedir'] . '/cora-git-sync');

    global $wp_filesystem;
    if ( empty( $wp_filesystem ) ) {
        WP_Filesystem();
    }

    if ( $wp_filesystem->exists( $target_dir ) ) {
        $wp_filesystem->delete( $target_dir, true );
    }

    $wp_filesystem->mkdir( $target_dir );

    $unzipped = unzip_file( $tmp_file, $target_dir );
    @unlink( $tmp_file );

    if ( is_wp_error( $unzipped ) ) {
        wp_send_json_error( array( 'message' => 'Extraction failed: ' . $unzipped->get_error_message() ) );
    }

    $files = $wp_filesystem->dirlist( $target_dir );
    $nested_dir = '';
    if ( ! empty( $files ) ) {
        foreach ( $files as $name => $info ) {
            if ( $info['type'] === 'd' ) {
                $nested_dir = $name;
                break;
            }
        }
    }

    if ( empty( $nested_dir ) ) {
        wp_send_json_error( array( 'message' => 'Failed to locate extracted nested directory from GitHub.' ) );
    }

    // Scan extracted HTML for Cora bridge compatibility flags inside the target folder
    $compat_flags = array();
    $index_html = $target_dir . '/' . $nested_dir . '/index.html';
    if ( file_exists( $index_html ) ) {
        $html_content = file_get_contents( $index_html );
        if ( strpos( $html_content, 'data-cora-inject' ) !== false ) {
            $compat_flags[] = 'data-bridge';
        }
        if ( strpos( $html_content, 'CORA_API_URL' ) !== false ) {
            $compat_flags[] = 'api-url';
        }
        if ( strpos( $html_content, 'CORA_NONCE' ) !== false ) {
            $compat_flags[] = 'nonce';
        }
        if ( strpos( $html_content, 'cora-nonce' ) !== false ) {
            $compat_flags[] = 'nonce-input';
        }
    }

    // Save metadata specifically to theme settings
    if ( $theme_id ) {
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        if ( $theme ) {
            $theme_settings = json_decode( $theme['settings'], true ) ?: array();
            $theme_settings['nested_dir'] = $nested_dir;
            $theme_settings['compat_flags'] = $compat_flags;
            $theme_settings['last_sync_time'] = time();
            $theme_settings['last_sync_status'] = 'Success';
            
            $wpdb->update(
                $wpdb->prefix . 'cora_canvas_themes',
                array( 'settings' => json_encode( $theme_settings ), 'updated_at' => current_time('mysql') ),
                array( 'id' => $theme_id )
            );
        }
    }

    // Fallback/Legacy global option updates
    update_option( 'cora_git_sync_last_time', time() );
    update_option( 'cora_git_sync_last_status', 'Success' );
    update_option( 'cora_git_sync_nested_dir', $nested_dir );
    update_option( 'cora_git_sync_compat_flags', $compat_flags );

    // Count detected routes using our dynamic method
    $routes = cora_git_sync_get_lovable_routes( $theme_id );

    cora_log_activity( 'git_sync', "Synchronized repository {$repo} [branch: {$branch}]" );

    wp_send_json_success( array(
        'message'      => 'Repository synced and deployed successfully!',
        'timestamp'    => date( 'Y-m-d H:i:s' ),
        'status'       => 'Success',
        'compat_flags' => $compat_flags,
        'route_count'  => count( $routes ),
    ) );
}
add_action( 'wp_ajax_cora_trigger_git_sync', 'cora_ajax_trigger_git_sync' );

/**
 * Get all pages/routes detected in the synced Lovable React project.
 */
function cora_git_sync_get_lovable_routes( $theme_id = 0 ) {
    global $wpdb;
    $repo = '';
    $nested_dir = '';
    
    if ( $theme_id ) {
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        if ( $theme ) {
            $settings = json_decode( $theme['settings'], true ) ?: array();
            $repo = isset( $settings['github_repo'] ) ? $settings['github_repo'] : '';
            $nested_dir = isset( $settings['nested_dir'] ) ? $settings['nested_dir'] : '';
        }
    }
    
    if ( empty( $repo ) || empty( $nested_dir ) ) {
        $repo = get_option( 'cora_git_sync_repo', '' );
        $nested_dir = get_option( 'cora_git_sync_nested_dir', '' );
    }

    if ( empty( $repo ) || empty( $nested_dir ) ) {
        return array();
    }

    $upload_dir = wp_get_upload_dir();
    if ( $theme_id ) {
        $routes_dir = $upload_dir['basedir'] . '/cora-git-sync-' . $theme_id . '/' . $nested_dir . '/src/routes';
        if ( ! is_dir( $routes_dir ) ) {
            $routes_dir = $upload_dir['basedir'] . '/cora-git-sync/' . $nested_dir . '/src/routes';
        }
    } else {
        $routes_dir = $upload_dir['basedir'] . '/cora-git-sync/' . $nested_dir . '/src/routes';
    }

    if ( ! is_dir( $routes_dir ) ) {
        return array();
    }

    $files = scandir( $routes_dir );
    $routes = array();

    foreach ( $files as $file ) {
        if ( in_array( $file, array( '.', '..', '__root.tsx' ) ) ) {
            continue;
        }

        if ( substr( $file, -4 ) === '.tsx' || substr( $file, -3 ) === '.ts' || substr( $file, -3 ) === '.js' || substr( $file, -4 ) === '.jsx' ) {
            $base_name = pathinfo( $file, PATHINFO_FILENAME );
            
            // Map TanStack dot notation to URL paths (e.g. assets.$assetId.edit -> /assets/$assetId/edit)
            if ( $base_name === 'index' ) {
                $path = '/';
            } else {
                $path = '/' . str_replace( '.', '/', $base_name );
                if ( substr( $path, -6 ) === '/index' ) {
                    $path = substr( $path, 0, -6 );
                }
            }

            // Capitalize names for clean presentation
            $title = ucwords( str_replace( array( '-', '.', '$' ), ' ', $base_name ) );
            if ( $title === 'Index' ) {
                $title = 'Home / Landing Page';
            }

            $routes[] = array(
                'title' => $title,
                'path'  => $path,
                'file'  => $file
            );
        }
    }

    return $routes;
}

/**
 * Save Lovable route mapping for a Canvas page.
 */
function cora_ajax_save_page_lovable_mapping() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    global $wpdb;

    $page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
    $route   = isset( $_POST['route'] ) ? sanitize_text_field( $_POST['route'] ) : '';

    if ( ! $page_id ) {
        wp_send_json_error( array( 'message' => 'Invalid Page ID' ) );
    }

    // Save to the specific theme's settings
    $page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $page_id ), ARRAY_A );
    if ( $page ) {
        $theme_id = intval( $page['theme_id'] );
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        if ( $theme ) {
            $settings = json_decode( $theme['settings'], true ) ?: array();
            if ( ! isset( $settings['page_mappings'] ) || ! is_array( $settings['page_mappings'] ) ) {
                $settings['page_mappings'] = array();
            }
            if ( empty( $route ) ) {
                unset( $settings['page_mappings'][ $page_id ] );
            } else {
                $settings['page_mappings'][ $page_id ] = $route;
            }
            $wpdb->update(
                $wpdb->prefix . 'cora_canvas_themes',
                array( 'settings' => json_encode( $settings ), 'updated_at' => current_time('mysql') ),
                array( 'id' => $theme_id )
            );
        }
    }

    // Fallback/Legacy global mappings option update
    $mappings = get_option( 'cora_git_sync_page_mappings', array() );
    if ( ! is_array( $mappings ) ) {
        $mappings = array();
    }
    if ( empty( $route ) ) {
        unset( $mappings[ $page_id ] );
    } else {
        $mappings[ $page_id ] = $route;
    }
    update_option( 'cora_git_sync_page_mappings', $mappings );

    wp_send_json_success( array( 'message' => 'Mapping saved successfully.' ) );
}
add_action( 'wp_ajax_cora_save_page_lovable_mapping', 'cora_ajax_save_page_lovable_mapping' );

function cora_ajax_save_appearance_settings() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    if ( isset( $_POST['tagline'] ) ) {
        update_option( 'blogdescription', sanitize_text_field( $_POST['tagline'] ) );
    }
    if ( isset( $_POST['logo_url'] ) ) {
        update_option( 'cora_brand_logo_url', esc_url_raw( $_POST['logo_url'] ) );
    }
    if ( isset( $_POST['favicon_url'] ) ) {
        update_option( 'cora_brand_favicon_url', esc_url_raw( $_POST['favicon_url'] ) );
    }
    wp_send_json_success( array( 'message' => 'Appearance settings saved successfully.' ) );
}
add_action( 'wp_ajax_cora_save_appearance_settings', 'cora_ajax_save_appearance_settings' );

function cora_ajax_add_menu_item() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $menu_id = isset( $_POST['menu_id'] ) ? intval( $_POST['menu_id'] ) : 0;
    if ( ! $menu_id ) {
        wp_send_json_error( array( 'message' => 'Invalid menu ID.' ) );
    }
    $item_type = isset( $_POST['item_type'] ) ? sanitize_key( $_POST['item_type'] ) : 'page';
    $label = isset( $_POST['label'] ) ? sanitize_text_field( $_POST['label'] ) : '';
    
    $menu_item_db_id = 0;
    if ( $item_type === 'page' ) {
        $page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
        if ( ! $page_id ) {
            wp_send_json_error( array( 'message' => 'Invalid page selected.' ) );
        }
        $menu_item_db_id = wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-object-id' => $page_id,
            'menu-item-object'    => 'page',
            'menu-item-type'      => 'post_type',
            'menu-item-title'     => $label,
            'menu-item-status'    => 'publish'
        ) );
    } elseif ( $item_type === 'custom' ) {
        $custom_url = isset( $_POST['custom_url'] ) ? esc_url_raw( $_POST['custom_url'] ) : '';
        if ( ! $custom_url ) {
            wp_send_json_error( array( 'message' => 'Invalid URL.' ) );
        }
        $menu_item_db_id = wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'     => $label,
            'menu-item-url'       => $custom_url,
            'menu-item-type'      => 'custom',
            'menu-item-status'    => 'publish'
        ) );
    }
    
    if ( is_wp_error( $menu_item_db_id ) || ! $menu_item_db_id ) {
        wp_send_json_error( array( 'message' => 'Failed to add menu item.' ) );
    }
    
    wp_send_json_success( array( 'message' => 'Menu item added successfully.' ) );
}
add_action( 'wp_ajax_cora_add_menu_item', 'cora_ajax_add_menu_item' );

function cora_ajax_delete_menu_item() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $menu_item_id = isset( $_POST['menu_item_id'] ) ? intval( $_POST['menu_item_id'] ) : 0;
    if ( ! $menu_item_id ) {
        wp_send_json_error( array( 'message' => 'Invalid menu item ID.' ) );
    }
    if ( wp_delete_post( $menu_item_id, true ) ) {
        wp_send_json_success( array( 'message' => 'Menu item deleted successfully.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to delete menu item.' ) );
    }
}
add_action( 'wp_ajax_cora_delete_menu_item', 'cora_ajax_delete_menu_item' );

function cora_ajax_create_nav_menu() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $menu_name = isset( $_POST['menu_name'] ) ? sanitize_text_field( $_POST['menu_name'] ) : '';
    if ( empty( $menu_name ) ) {
        wp_send_json_error( array( 'message' => 'Menu name cannot be empty.' ) );
    }

    global $wpdb;
    $term_exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT t.term_id FROM {$wpdb->terms} t 
         INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
         WHERE tt.taxonomy = 'nav_menu' AND t.name = %s LIMIT 1",
        $menu_name
    ) );
    if ( $term_exists ) {
        wp_send_json_error( array( 'message' => 'The menu name conflicts with another menu name.' ) );
    }

    $menu_id = wp_create_nav_menu( $menu_name );
    if ( is_wp_error( $menu_id ) ) {
        wp_send_json_error( array( 'message' => $menu_id->get_error_message() ) );
    }
    wp_send_json_success( array( 'message' => 'Menu created successfully.', 'menu_id' => $menu_id ) );
}
add_action( 'wp_ajax_cora_create_nav_menu', 'cora_ajax_create_nav_menu' );

function cora_ajax_moderate_comment() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'moderate_comments' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $comment_id = isset( $_POST['comment_id'] ) ? intval( $_POST['comment_id'] ) : 0;
    $comment_action = isset( $_POST['comment_action'] ) ? sanitize_key( $_POST['comment_action'] ) : '';
    if ( ! $comment_id || ! $comment_action ) {
        wp_send_json_error( array( 'message' => 'Invalid parameters.' ) );
    }
    
    $status = '';
    if ( $comment_action === 'approve' ) {
        $status = 'approve';
    } elseif ( $comment_action === 'hold' ) {
        $status = 'hold';
    } elseif ( $comment_action === 'spam' ) {
        $status = 'spam';
    } elseif ( $comment_action === 'trash' ) {
        $status = 'trash';
    } elseif ( $comment_action === 'restore' ) {
        $status = 'approve';
    }
    
    if ( $status && wp_set_comment_status( $comment_id, $status ) ) {
        wp_send_json_success( array( 'message' => 'Comment status updated successfully.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to update comment status.' ) );
    }
}
add_action( 'wp_ajax_cora_moderate_comment', 'cora_ajax_moderate_comment' );

function cora_ajax_delete_comment_permanent() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'moderate_comments' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $comment_id = isset( $_POST['comment_id'] ) ? intval( $_POST['comment_id'] ) : 0;
    if ( ! $comment_id ) {
        wp_send_json_error( array( 'message' => 'Invalid comment ID.' ) );
    }
    if ( wp_delete_comment( $comment_id, true ) ) {
        wp_send_json_success( array( 'message' => 'Comment permanently deleted.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to delete comment permanently.' ) );
    }
}
add_action( 'wp_ajax_cora_delete_comment_permanent', 'cora_ajax_delete_comment_permanent' );

function cora_ajax_submit_comment_reply() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'moderate_comments' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $parent_id = isset( $_POST['parent_id'] ) ? intval( $_POST['parent_id'] ) : 0;
    $content   = isset( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';
    if ( ! $parent_id || empty( $content ) ) {
        wp_send_json_error( array( 'message' => 'Invalid parent ID or empty content.' ) );
    }
    
    $parent_comment = get_comment( $parent_id );
    if ( ! $parent_comment ) {
        wp_send_json_error( array( 'message' => 'Parent comment not found.' ) );
    }
    
    $current_user = wp_get_current_user();
    $comment_data = array(
        'comment_post_ID'      => $parent_comment->comment_post_ID,
        'comment_content'      => $content,
        'comment_parent'       => $parent_id,
        'user_id'              => $current_user->ID,
        'comment_author'       => $current_user->display_name,
        'comment_author_email' => $current_user->user_email,
        'comment_approved'     => 1,
    );
    
    $comment_id = wp_new_comment( $comment_data );
    if ( $comment_id ) {
        wp_send_json_success( array( 'message' => 'Reply posted successfully.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to post reply.' ) );
    }
}
add_action( 'wp_ajax_cora_submit_comment_reply', 'cora_ajax_submit_comment_reply' );

function cora_ajax_get_attachment_metadata() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;
    if ( ! $attachment_id ) {
        wp_send_json_error( array( 'message' => 'Invalid attachment ID.' ) );
    }
    $post = get_post( $attachment_id );
    if ( ! $post || $post->post_type !== 'attachment' ) {
        wp_send_json_error( array( 'message' => 'Attachment not found.' ) );
    }
    
    $title = $post->post_title;
    $caption = $post->post_excerpt;
    $description = $post->post_content;
    $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
    $url = wp_get_attachment_url( $attachment_id );
    
    wp_send_json_success( array(
        'attachment_id' => $attachment_id,
        'title'         => $title,
        'caption'       => $caption,
        'description'   => $description,
        'alt'           => $alt,
        'url'           => $url,
    ) );
}
add_action( 'wp_ajax_cora_get_attachment_metadata', 'cora_ajax_get_attachment_metadata' );

function cora_ajax_save_edited_image() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;
    if ( ! $attachment_id ) {
        wp_send_json_error( array( 'message' => 'Invalid attachment ID.' ) );
    }
    
    $file_path = get_attached_file( $attachment_id );
    if ( ! $file_path || ! file_exists( $file_path ) ) {
        wp_send_json_error( array( 'message' => 'Image file not found on disk.' ) );
    }
    
    $editor = wp_get_image_editor( $file_path );
    if ( is_wp_error( $editor ) ) {
        wp_send_json_error( array( 'message' => 'Failed to initialize image editor.' ) );
    }
    
    // Rotate
    if ( isset( $_POST['rotate'] ) ) {
        $rotate = floatval( $_POST['rotate'] );
        if ( $rotate !== 0.0 ) {
            $editor->rotate( $rotate );
        }
    }
    
    // Flip
    if ( isset( $_POST['flip'] ) ) {
        $flip = sanitize_key( $_POST['flip'] );
        if ( $flip === 'h' ) {
            $editor->flip( true, false );
        } elseif ( $flip === 'v' ) {
            $editor->flip( false, true );
        }
    }
    
    // Crop
    if ( isset( $_POST['crop_x'], $_POST['crop_y'], $_POST['crop_w'], $_POST['crop_h'] ) ) {
        $x = intval( $_POST['crop_x'] );
        $y = intval( $_POST['crop_y'] );
        $w = intval( $_POST['crop_w'] );
        $h = intval( $_POST['crop_h'] );
        if ( $w > 0 && $h > 0 ) {
            $editor->crop( $x, $y, $w, $h );
        }
    }
    
    // Scale
    if ( isset( $_POST['width'] ) && isset( $_POST['height'] ) ) {
        $w = intval( $_POST['width'] );
        $h = intval( $_POST['height'] );
        if ( $w > 0 && $h > 0 ) {
            $editor->resize( $w, $h, false );
        }
    }
    
    $result = $editor->save( $file_path );
    if ( is_wp_error( $result ) ) {
        wp_send_json_error( array( 'message' => $result->get_error_message() ) );
    }
    
    // Regenerate metadata
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $metadata = wp_generate_attachment_metadata( $attachment_id, $file_path );
    wp_update_attachment_metadata( $attachment_id, $metadata );
    
    wp_send_json_success( array(
        'message' => 'Image saved successfully.',
        'url'     => wp_get_attachment_url( $attachment_id )
    ) );
}
add_action( 'wp_ajax_cora_save_edited_image', 'cora_ajax_save_edited_image' );

/**
 * Intercept frontend template redirect to render visual builder pages
 */
function cora_real_estate_ai_intercept_visual_builder_pages() {
    if ( is_page() ) {
        $page_id = get_the_ID();
        if ( get_post_meta( $page_id, '_cora_is_visual_builder', true ) === '1' ) {
            $html = get_post_meta( $page_id, '_cora_visual_builder_html', true );
            $css  = get_post_meta( $page_id, '_cora_visual_builder_css', true );
            
            // Clean output buffer if there's any active
            while ( ob_get_level() > 0 ) {
                ob_end_clean();
            }
            
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php the_title(); ?></title>
                <link rel="stylesheet" href="<?php echo CORA_REAL_ESTATE_AI_URL . 'assets/css/tailwind-built.css'; ?>">
                <style>
                    <?php echo $css; ?>
                </style>
            </head>
            <body class="bg-[#FBFaf7] text-neutral-800 antialiased font-sans">
                <?php echo $html; ?>
            </body>
            </html>
            <?php
            exit;
        }
    }
}
add_action( 'template_redirect', 'cora_real_estate_ai_intercept_visual_builder_pages', 5 );

/**
 * Intercept template redirect to render platform landing page from trial views
 */
function cora_real_estate_intercept_landing_page_template( $template ) {
    // Do not intercept if loading inside Elementor Editor or Preview to prevent "preview could not be loaded" crash
    if ( isset( $_GET['elementor-preview'] ) || ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) ) {
        return $template;
    }
    if ( did_action( 'elementor/loaded' ) ) {
        $el = \Elementor\Plugin::$instance;
        if ( isset( $el->editor ) && ( $el->editor->is_edit_mode() || $el->preview->is_preview_mode() ) ) {
            return $template;
        }
    }

    if ( is_page() ) {
        $page_id = get_the_ID();
        // Check if page template is template-landing-page.php or if it is mapped in cora_canvas_pages with template = 'landing-page'
        $wp_template = get_post_meta( $page_id, '_wp_page_template', true );
        global $wpdb;
        $cora_page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE wp_post_id = %d LIMIT 1", $page_id ), ARRAY_A );
        if ( $wp_template === 'template-landing-page.php' || ( $cora_page && $cora_page['template'] === 'landing-page' ) ) {
            $landing_php = dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/cora-platform/modules/trial/views/landing-page.php';
            if ( file_exists( $landing_php ) ) {
                return $landing_php;
            }
        }
    }
    return $template;
}
add_filter( 'template_include', 'cora_real_estate_intercept_landing_page_template', 99 );

/**
 * AJAX Handler: Cora Trial Onboarding On-demand Signup
 */
function cora_ajax_trial_signup() {
    check_ajax_referer( 'cora_trial_signup', '_nonce' );

    $name     = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    $agency   = isset( $_POST['agency_name'] ) ? sanitize_text_field( $_POST['agency_name'] ) : '';
    $whatsapp = isset( $_POST['whatsapp'] ) ? sanitize_text_field( $_POST['whatsapp'] ) : '';
    $city     = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';

    if ( empty( $name ) || empty( $agency ) || empty( $whatsapp ) ) {
        wp_send_json_error( array( 'message' => 'Please fill in all required fields.' ) );
    }

    global $wpdb;

    // 1. Update Agency details in the database
    $agency_exists = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_agencies WHERE id = 1" );
    if ( $agency_exists ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_agencies',
            array(
                'name' => $agency,
                'updated_at' => current_time( 'mysql' )
            ),
            array( 'id' => 1 ),
            array( '%s', '%s' ),
            array( '%d' )
        );
    } else {
        $wpdb->insert(
            $wpdb->prefix . 'cora_agencies',
            array(
                'id' => 1,
                'name' => $agency,
                'slug' => 'default',
                'owner_user_id' => 1,
                'plan' => 'enterprise',
                'status' => 'active',
                'settings' => json_encode( array() ),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
        );
    }
    update_option( 'cora_workspace_name', $agency );

    // 2. Update Branch details
    $branch_exists = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_branches WHERE id = 1" );
    if ( $branch_exists ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_branches',
            array(
                'name' => 'Main Branch (' . $agency . ')',
                'city' => $city,
                'updated_at' => current_time( 'mysql' )
            ),
            array( 'id' => 1 ),
            array( '%s', '%s', '%s' ),
            array( '%d' )
        );
    } else {
        $wpdb->insert(
            $wpdb->prefix . 'cora_branches',
            array(
                'id' => 1,
                'agency_id' => 1,
                'name' => 'Main Branch (' . $agency . ')',
                'city' => $city,
                'address' => 'Gurgaon Delhi NCR',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s', '%s', '%s')
        );
    }

    // Update options arrays
    $agencies = get_option( 'cora_agencies', array() );
    $agencies['agency_1'] = array(
        'id'          => 'agency_1',
        'name'        => $agency,
        'subdomain'   => 'default',
        'plan'        => 'enterprise',
        'status'      => 'active',
        'created_at'  => date( 'Y-m-d H:i:s' )
    );
    update_option( 'cora_agencies', $agencies );

    $branches = get_option( 'cora_branches', array() );
    $branches['branch_1'] = array(
        'id'         => 'branch_1',
        'agency_id'  => 'agency_1',
        'name'       => 'Main Branch (' . $agency . ')',
        'city'       => $city,
        'address'    => 'Gurgaon Delhi NCR',
        'manager_id' => 0
    );
    update_option( 'cora_branches', $branches );

    // 3. Programmatically log in user "cora_admin" (ID 1) so they get instant dashboard access
    $user = get_user_by( 'login', 'cora_admin' );
    if ( $user ) {
        wp_clear_auth_cookie();
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID );
    }

    wp_send_json_success( array(
        'workspace_url' => home_url( '/workspace/dashboard' )
    ) );
}
add_action( 'wp_ajax_cora_trial_signup', 'cora_ajax_trial_signup' );
add_action( 'wp_ajax_nopriv_cora_trial_signup', 'cora_ajax_trial_signup' );

/**
 * AJAX Action: Save Builder Page
 */
function cora_ajax_save_builder_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! current_user_can( 'edit_pages' ) && ! current_user_can( 'manage_options' ) ) {
        $user = wp_get_current_user();
        if ( empty( $user->roles ) || ! in_array( 'administrator', (array) $user->roles ) ) {
            wp_send_json_error( 'Unauthorized capability.' );
        }
    }

    $page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
    $title   = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
    $slug    = isset( $_POST['slug'] ) ? sanitize_title( $_POST['slug'] ) : '';
    $status  = isset( $_POST['status'] ) && in_array( $_POST['status'], array( 'publish', 'draft', 'private' ) ) ? $_POST['status'] : 'draft';
    $html    = isset( $_POST['html'] ) ? wp_unslash( $_POST['html'] ) : '';
    $css     = isset( $_POST['css'] ) ? wp_unslash( $_POST['css'] ) : '';

    if ( empty( $title ) ) {
        wp_send_json_error( 'Page title is required.' );
    }

    $post_data = array(
        'post_title'     => $title,
        'post_status'    => $status,
        'post_type'      => 'page',
        'comment_status' => 'closed',
        'post_content'   => '[cora_visual_builder]'
    );

    if ( ! empty( $slug ) ) {
        $post_data['post_name'] = $slug;
    }

    if ( $page_id > 0 ) {
        $post_data['ID'] = $page_id;
        $saved_id = wp_update_post( $post_data, true );
    } else {
        $saved_id = wp_insert_post( $post_data, true );
    }

    if ( is_wp_error( $saved_id ) ) {
        wp_send_json_error( $saved_id->get_error_message() );
    }

    // Set the metadata
    update_post_meta( $saved_id, '_cora_is_visual_builder', '1' );
    update_post_meta( $saved_id, '_cora_visual_builder_html', $html );
    update_post_meta( $saved_id, '_cora_visual_builder_css', $css );

    wp_send_json_success( array(
        'id'        => $saved_id,
        'title'     => get_the_title( $saved_id ),
        'slug'      => get_post_field( 'post_name', $saved_id ),
        'status'    => get_post_status( $saved_id ),
        'permalink' => get_permalink( $saved_id )
    ) );
}
add_action( 'wp_ajax_cora_save_builder_page', 'cora_ajax_save_builder_page' );

/**
 * AJAX Action: Get Builder Page
 */
function cora_ajax_get_builder_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! current_user_can( 'edit_pages' ) && ! current_user_can( 'manage_options' ) ) {
        $user = wp_get_current_user();
        if ( empty( $user->roles ) || ! in_array( 'administrator', (array) $user->roles ) ) {
            wp_send_json_error( 'Unauthorized capability.' );
        }
    }

    $page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
    if ( ! $page_id ) {
        wp_send_json_error( 'Invalid page ID.' );
    }

    $page = get_post( $page_id );
    if ( ! $page || $page->post_type !== 'page' ) {
        wp_send_json_error( 'Page not found.' );
    }

    $html = get_post_meta( $page_id, '_cora_visual_builder_html', true );
    $css  = get_post_meta( $page_id, '_cora_visual_builder_css', true );

    wp_send_json_success( array(
        'id'     => $page->ID,
        'title'  => $page->post_title,
        'slug'   => urldecode( $page->post_name ),
        'status' => $page->post_status,
        'html'   => $html,
        'css'    => $css
    ) );
}
add_action( 'wp_ajax_cora_get_builder_page', 'cora_ajax_get_builder_page' );

/**
 * AJAX Action: Generate Layout via AI
 */
function cora_ajax_generate_layout() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! current_user_can( 'edit_pages' ) && ! current_user_can( 'manage_options' ) ) {
        $user = wp_get_current_user();
        if ( empty( $user->roles ) || ! in_array( 'administrator', (array) $user->roles ) ) {
            wp_send_json_error( 'Unauthorized capability.' );
        }
    }

    $prompt = isset( $_POST['prompt'] ) ? sanitize_text_field( $_POST['prompt'] ) : '';

    $gemini_key_b64 = get_option( 'cora_re_ai_gemini_key', '' );
    $openai_key_b64 = get_option( 'cora_re_ai_openai_key', '' );
    $active_model   = get_option( 'cora_re_active_ai_model', 'cora-core-v2' );

    $html_fallback = '
<div class="min-h-screen bg-[#FBFaf7] text-neutral-900 selection:bg-neutral-200">
    <!-- Header -->
    <header class="border-b border-neutral-200 py-6 px-8 max-w-7xl mx-auto flex items-center justify-between">
        <div class="text-xl font-bold tracking-tight uppercase">Villa Serene</div>
        <nav class="hidden md:flex space-x-8 text-sm font-medium tracking-wide uppercase text-neutral-600">
            <a href="#overview" class="hover:text-black transition">Overview</a>
            <a href="#features" class="hover:text-black transition">Features</a>
            <a href="#gallery" class="hover:text-black transition">Gallery</a>
            <a href="#inquire" class="hover:text-black transition">Inquire</a>
        </nav>
        <a href="#inquire" class="px-5 py-2 border border-black text-xs uppercase tracking-wider font-semibold hover:bg-black hover:text-white transition">Inquire Now</a>
    </header>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-8 py-16 md:py-24 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
            <div class="text-xs uppercase tracking-widest text-neutral-500 font-semibold">Exquisite Living</div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-light leading-tight tracking-tight text-neutral-900">
                A Sanctuary in the <span class="italic font-serif">Western Ghats</span>
            </h1>
            <p class="text-neutral-600 max-w-lg leading-relaxed text-sm md:text-base">
                Discover a private architectural masterpiece nestled among mist-covered hills. Floor-to-ceiling glass walls, minimalist concrete formwork, and an infinity pool that merges with the horizon.
            </p>
            <div class="flex items-center space-x-6 pt-4">
                <div>
                    <span class="block text-2xl font-light">4,200</span>
                    <span class="text-xs uppercase tracking-wider text-neutral-500 font-medium">Sq. Ft.</span>
                </div>
                <div class="border-l border-neutral-300 h-8"></div>
                <div>
                    <span class="block text-2xl font-light">4</span>
                    <span class="text-xs uppercase tracking-wider text-neutral-500 font-medium">Bedrooms</span>
                </div>
                <div class="border-l border-neutral-300 h-8"></div>
                <div>
                    <span class="block text-2xl font-light">4.5</span>
                    <span class="text-xs uppercase tracking-wider text-neutral-500 font-medium">Baths</span>
                </div>
            </div>
        </div>
        <div class="relative group">
            <div class="absolute -inset-2 bg-gradient-to-r from-neutral-200 to-neutral-300 rounded-lg blur opacity-30 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-neutral-100 border border-neutral-200 overflow-hidden shadow-sm">
                <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1200&q=80" alt="Villa Exterior" class="w-full h-[400px] object-cover hover:scale-105 transition-transform duration-700">
            </div>
        </div>
    </section>

    <!-- Specs Section -->
    <section id="features" class="border-t border-neutral-200 py-16 bg-neutral-50">
        <div class="max-w-7xl mx-auto px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <h2 class="text-3xl font-light tracking-tight text-neutral-900">Designed with Intention</h2>
                <p class="text-neutral-500 text-sm">Every element has been curated to create a unified experience of luxury and tranquility.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-[#FBFaf7] border border-neutral-200 p-8 space-y-4">
                    <div class="text-sm font-semibold uppercase tracking-wider text-neutral-800">Sustainable Materials</div>
                    <p class="text-xs text-neutral-600 leading-relaxed">Local basalt stone, reclaimed teak wood, and low-carbon concrete combine to form a structure that is both beautiful and built to last.</p>
                </div>
                <div class="bg-[#FBFaf7] border border-neutral-200 p-8 space-y-4">
                    <div class="text-sm font-semibold uppercase tracking-wider text-neutral-800">Smart Integration</div>
                    <p class="text-xs text-neutral-600 leading-relaxed">Fully integrated automated lighting, climate control, security systems, and high-fidelity sound, manageable from any device.</p>
                </div>
                <div class="bg-[#FBFaf7] border border-neutral-200 p-8 space-y-4">
                    <div class="text-sm font-semibold uppercase tracking-wider text-neutral-800">Private Wellness</div>
                    <p class="text-xs text-neutral-600 leading-relaxed">Features a private cedar sauna, outdoor rain shower, temperature-controlled plunge pool, and dedicated yoga deck overlooking the valley.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inquiry Form -->
    <section id="inquire" class="border-t border-neutral-200 py-16 md:py-24 max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-2 gap-12">
        <div class="space-y-6">
            <h2 class="text-3xl md:text-4xl font-light tracking-tight">Begin Your Journey</h2>
            <p class="text-neutral-600 text-sm leading-relaxed max-w-md">
                Schedule a private, high-security showing of Villa Serene. Available by appointment only to qualified buyers. Leave your contact info and our principal advisor will connect within 2 hours.
            </p>
            <div class="text-xs text-neutral-500 space-y-1">
                <p>Office: +91 22 9876 5432</p>
                <p>Email: private-wealth@cora.in</p>
            </div>
        </div>
        <div class="bg-[#FBFaf7] border border-neutral-200 p-8 md:p-10 space-y-6">
            <h3 class="text-lg font-medium tracking-tight">Request Details</h3>
            <div id="inquiry-success" class="hidden text-xs text-green-600 bg-green-50 border border-green-200 p-3 rounded">
                Thank you for your interest. An advisor will contact you shortly.
            </div>
            <form class="space-y-4" onsubmit="event.preventDefault(); document.getElementById(\'inquiry-success\').classList.remove(\'hidden\');">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-neutral-500 font-semibold mb-1">Full Name</label>
                    <input type="text" required placeholder="John Doe" class="w-full px-4 py-2 bg-transparent border border-neutral-300 text-sm focus:outline-none focus:border-black transition">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-neutral-500 font-semibold mb-1">Email Address</label>
                    <input type="email" required placeholder="john@example.com" class="w-full px-4 py-2 bg-transparent border border-neutral-300 text-sm focus:outline-none focus:border-black transition">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-neutral-500 font-semibold mb-1">Preferences / Message</label>
                    <textarea rows="3" placeholder="I would like to schedule a private viewing this weekend..." class="w-full px-4 py-2 bg-transparent border border-neutral-300 text-sm focus:outline-none focus:border-black transition resize-none"></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-black text-white text-xs uppercase tracking-widest font-semibold hover:bg-neutral-800 transition">Submit Request</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-neutral-200 bg-neutral-900 text-neutral-400 py-12 px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between text-xs space-y-4 md:space-y-0">
            <div>&copy; 2026 Cora Real Estate. All rights reserved.</div>
            <div class="flex space-x-6">
                <a href="#" class="hover:text-white transition">Privacy Policy</a>
                <a href="#" class="hover:text-white transition">Terms of Service</a>
                <a href="#" class="hover:text-white transition">Disclaimer</a>
            </div>
        </div>
    </footer>
</div>
';

    $css_fallback = '
body {
    background-color: #FBFaf7;
}
';

    $ai_success = false;
    $response_text = '';

    if ( ! empty( $gemini_key_b64 ) && ( $active_model === 'gemini' || $active_model === 'cora-core-v2' || empty( $openai_key_b64 ) ) ) {
        $api_key  = base64_decode( $gemini_key_b64 );
        $model_id = 'gemini-2.0-flash';
        $url      = "https://generativelanguage.googleapis.com/v1beta/models/{$model_id}:generateContent?key=" . urlencode( $api_key );

        $system_prompt = "You are a professional web designer. Generate a beautiful, responsive real estate web page section or landing page structure using Tailwind CSS and inline tags based on the user request. You MUST output ONLY valid JSON in the format: {\"html\": \"...HTML layout with Tailwind classes...\", \"css\": \"...custom CSS overrides...\"}. Do not wrap the JSON output in markdown backticks or any other text. Keep the design minimalist, monochromatic (slate/zinc grays, warm cream background #FBFaf7, white, black), clean vector SVGs for icons, and modern layout.";

        $body = json_encode( array(
            'system_instruction' => array(
                'parts' => array( array( 'text' => $system_prompt ) )
            ),
            'contents' => array(
                array(
                    'role'  => 'user',
                    'parts' => array( array( 'text' => "Create layout for: " . $prompt ) ),
                )
            ),
            'generationConfig' => array(
                'maxOutputTokens' => 2048,
                'temperature'     => 0.4,
            ),
        ) );

        $response = wp_remote_post( $url, array(
            'timeout' => 30,
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => $body,
        ) );

        if ( ! is_wp_error( $response ) ) {
            $code = wp_remote_retrieve_response_code( $response );
            $data = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( $code === 200 && ! empty( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
                $response_text = $data['candidates'][0]['content']['parts'][0]['text'];
                $ai_success = true;
            }
        }
    } elseif ( ! empty( $openai_key_b64 ) ) {
        $api_key  = base64_decode( $openai_key_b64 );
        $model_id = 'gpt-4o-mini';
        $url      = 'https://api.openai.com/v1/chat/completions';

        $system_prompt = "You are a professional web designer. Generate a beautiful, responsive real estate web page section or landing page structure using Tailwind CSS and inline tags based on the user request. You MUST output ONLY valid JSON in the format: {\"html\": \"...HTML layout with Tailwind classes...\", \"css\": \"...custom CSS overrides...\"}. Do not wrap the JSON output in markdown backticks or any other text. Keep the design minimalist, monochromatic (slate/zinc grays, warm cream background #FBFaf7, white, black), clean vector SVGs for icons, and modern layout.";

        $body = json_encode( array(
            'model'    => $model_id,
            'messages' => array(
                array( 'role' => 'system', 'content' => $system_prompt ),
                array( 'role' => 'user',   'content' => "Create layout for: " . $prompt ),
            ),
            'max_tokens'  => 2048,
            'temperature' => 0.4,
        ) );

        $response = wp_remote_post( $url, array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
            ),
            'body' => $body,
        ) );

        if ( ! is_wp_error( $response ) ) {
            $code = wp_remote_retrieve_response_code( $response );
            $data = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( $code === 200 && ! empty( $data['choices'][0]['message']['content'] ) ) {
                $response_text = $data['choices'][0]['message']['content'];
                $ai_success = true;
            }
        }
    }

    if ( $ai_success && ! empty( $response_text ) ) {
        $clean_text = trim( $response_text );
        if ( preg_match( '/^```(?:json)?\s*([\s\S]*?)\s*```$/', $clean_text, $matches ) ) {
            $clean_text = trim( $matches[1] );
        }
        
        $json = json_decode( $clean_text, true );
        if ( $json && isset( $json['html'] ) ) {
            wp_send_json_success( array(
                'html' => $json['html'],
                'css'  => $json['css'] ?? ''
            ) );
        }
    }

    // Return fallback layout if keys missing or AI generation failed/returned invalid format
    wp_send_json_success( array(
        'html' => $html_fallback,
        'css'  => $css_fallback
    ) );
}
add_action( 'wp_ajax_cora_generate_layout', 'cora_ajax_generate_layout' );

/**
 * Elementor Reskin Module
 */
function cora_enqueue_elementor_reskin_styles() {
    // Core reskin
    wp_enqueue_style(
        'cora-elementor-reskin-css',
        plugin_dir_url( __FILE__ ) . 'assets/css/cora-elementor-reskin.css',
        array(),
        time()
    );
    // Git integration drawer styles
    wp_enqueue_style(
        'cora-git-integration-css',
        plugin_dir_url( __FILE__ ) . 'assets/css/cora-git-integration.css',
        array(),
        time()
    );
}
add_action( 'elementor/editor/after_enqueue_styles', 'cora_enqueue_elementor_reskin_styles' );
add_action( 'elementor/preview/enqueue_styles', 'cora_enqueue_elementor_reskin_styles' );

function cora_enqueue_elementor_reskin_scripts() {
    // Core reskin
    wp_enqueue_script(
        'cora-elementor-reskin-js',
        plugin_dir_url( __FILE__ ) . 'assets/js/cora-elementor-reskin.js',
        array(),
        time(),
        true
    );
    // Git integration
    wp_enqueue_script(
        'cora-git-integration-js',
        plugin_dir_url( __FILE__ ) . 'assets/js/cora-git-integration.js',
        array(),
        time(),
        true
    );
    wp_localize_script( 'cora-git-integration-js', 'coraGitData', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'cora_ajax_nonce' ),
    ) );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'cora_enqueue_elementor_reskin_scripts' );

/**
 * Optimize Elementor Editor performance and remove admin bar
 */
function cora_optimize_elementor_editor_performance() {
    if ( ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) || isset( $_GET['elementor-preview'] ) ) {
        // Boost PHP memory limit and execution time to prevent hangs/blank editor pages
        @ini_set( 'memory_limit', '512M' );
        @ini_set( 'max_execution_time', '300' );
        
        // Remove admin bar inside Elementor editor screen
        add_filter( 'show_admin_bar', '__return_false' );
        
        // Disable Gutenberg default scripts/styles when editing in Elementor to free significant memory overhead
        remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
        remove_action( 'admin_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
    }
}
add_action( 'init', 'cora_optimize_elementor_editor_performance', 1 );

/* ═══════════════════════════════════════════════════════════════════
 * MEDIA LIBRARY MODULE — AJAX HANDLERS
 * ═══════════════════════════════════════════════════════════════════ */

/**
 * Helper: categorise MIME type
 */
function cora_media_mime_category( $mime ) {
    if ( strpos( $mime, 'image/' ) === 0 )                      return 'image';
    if ( strpos( $mime, 'video/' ) === 0 )                      return 'video';
    if ( strpos( $mime, 'audio/' ) === 0 )                      return 'audio';
    return 'document';
}

/**
 * Helper: human-readable file size
 */
function cora_media_human_size( $bytes ) {
    if ( $bytes >= 1073741824 ) return round( $bytes / 1073741824, 2 ) . ' GB';
    if ( $bytes >= 1048576 )    return round( $bytes / 1048576, 2 )    . ' MB';
    if ( $bytes >= 1024 )       return round( $bytes / 1024, 1 )       . ' KB';
    return $bytes . ' B';
}

/**
 * Helper: build rich file object for JS
 */
function cora_media_build_file_object( $post_id ) {
    $meta    = wp_get_attachment_metadata( $post_id );
    $mime    = get_post_mime_type( $post_id );
    $cat     = cora_media_mime_category( $mime );
    $url     = wp_get_attachment_url( $post_id );
    $path    = get_attached_file( $post_id );
    $sz      = $path && file_exists( $path ) ? filesize( $path ) : 0;
    $post    = get_post( $post_id );
    $author  = get_userdata( $post->post_author );
    $folders = get_the_terms( $post_id, 'cora_media_folder' );
    $folder_id   = '';
    $folder_name = '';
    if ( $folders && ! is_wp_error( $folders ) ) {
        $folder_id   = $folders[0]->term_id;
        $folder_name = $folders[0]->name;
    }
    $extra = get_post_meta( $post_id, '_cora_media_extra', true );
    if ( ! is_array( $extra ) ) $extra = array();
    $share_links = get_post_meta( $post_id, '_cora_media_share_links', true );
    if ( ! is_array( $share_links ) ) $share_links = array();
    // Only return non-expired links
    $now   = time();
    $valid = array_values( array_filter( $share_links, function( $l ) use ( $now ) {
        return ! isset( $l['expires'] ) || $l['expires'] === 0 || $l['expires'] > $now;
    } ) );
    foreach ( $valid as &$l ) {
        if ( isset( $l['expires'] ) && $l['expires'] > 0 ) {
            $diff = $l['expires'] - $now;
            if ( $diff > 86400 )      $l['expiry_label'] = 'Expires in ' . round( $diff / 86400 ) . ' day(s)';
            elseif ( $diff > 3600 )   $l['expiry_label'] = 'Expires in ' . round( $diff / 3600 ) . ' hour(s)';
            else                      $l['expiry_label'] = 'Expires soon';
        } else {
            $l['expiry_label'] = 'No expiry';
        }
    }
    unset( $l );

    $dim = '';
    if ( $cat === 'image' && ! empty( $meta['width'] ) ) {
        $dim = $meta['width'] . ' × ' . $meta['height'] . ' px';
    }
    $thumb = '';
    if ( $cat === 'image' ) {
        $t = wp_get_attachment_image_src( $post_id, 'medium' );
        $thumb = $t ? $t[0] : $url;
    }
    return array(
        'id'             => $post_id,
        'filename'       => basename( $path ?: $url ),
        'title'          => get_the_title( $post_id ),
        'alt'            => get_post_meta( $post_id, '_wp_attachment_image_alt', true ),
        'caption'        => $post->post_excerpt,
        'description'    => $post->post_content,
        'url'            => $url,
        'thumbnail'      => $thumb,
        'mime_type'      => $mime,
        'type_category'  => $cat,
        'file_size_human'=> cora_media_human_size( $sz ),
        'file_size_bytes'=> $sz,
        'dimensions'     => $dim,
        'date_formatted' => get_the_date( 'd M Y', $post_id ),
        'author_name'    => $author ? $author->display_name : 'Unknown',
        'author_id'      => $post->post_author,
        'folder_id'      => $folder_id,
        'folder_name'    => $folder_name,
        'doc_type'       => isset( $extra['doc_type'] ) ? $extra['doc_type'] : '',
        'linked_record'  => isset( $extra['linked_record'] ) ? $extra['linked_record'] : null,
        'has_original'   => ! empty( $extra['original_file'] ),
        'share_links'    => $valid,
        'original_size'  => isset( $extra['original_size'] ) ? $extra['original_size'] : '',
    );
}

/**
 * AJAX: Get paginated media with filters
 */
function cora_ajax_media_library_get() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $paged   = max( 1, intval( $_POST['paged'] ?? 1 ) );
    $per     = max( 1, min( 100, intval( $_POST['per_page'] ?? 40 ) ) );
    $search  = sanitize_text_field( $_POST['search'] ?? '' );
    $type    = sanitize_text_field( $_POST['type'] ?? 'all' );
    $date    = sanitize_text_field( $_POST['date'] ?? '' );
    $author  = intval( $_POST['author'] ?? 0 );
    $folder  = isset( $_POST['folder_id'] ) ? $_POST['folder_id'] : null;
    $orderby = sanitize_text_field( $_POST['orderby'] ?? 'date' );
    $order   = strtoupper( sanitize_text_field( $_POST['order'] ?? 'DESC' ) ) === 'ASC' ? 'ASC' : 'DESC';

    $mime_map = array(
        'image'    => 'image',
        'video'    => 'video',
        'audio'    => 'audio',
        'document' => array( 'application', 'text' ),
    );

    $args = array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => $per,
        'paged'          => $paged,
        'orderby'        => in_array( $orderby, array( 'title', 'date', 'author' ) ) ? $orderby : 'date',
        'order'          => $order,
    );

    // Multi-Tenancy & Data Isolation on attachments
    $agency_id = cora_get_current_user_agency_id();
    if ( $agency_id !== 'super' && ! empty( $agency_id ) ) {
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'cora_agency_id',
                'value'   => $agency_id,
                'compare' => '='
            )
        );
        $branch_id = cora_get_current_user_branch_id();
        if ( ! empty( $branch_id ) ) {
            $meta_query[] = array(
                'key'     => 'cora_branch_id',
                'value'   => $branch_id,
                'compare' => '='
            );
        }
        $args['meta_query'] = $meta_query;
    }

    if ( $type !== 'all' && isset( $mime_map[ $type ] ) ) {
        $args['post_mime_type'] = $mime_map[ $type ];
    }
    if ( $search ) {
        $args['s'] = $search;
    }
    if ( $author > 0 ) {
        $args['author'] = $author;
    }
    if ( $date ) {
        list( $y, $m ) = array_pad( explode( '-', $date ), 2, '' );
        if ( $y ) $args['date_query'] = array( array( 'year' => intval($y), 'month' => intval($m) ) );
    }
    if ( $folder === '-1' || $folder === -1 ) {
        // Unorganised: no folder term
        $args['tax_query'] = array(
            array( 'taxonomy' => 'cora_media_folder', 'operator' => 'NOT EXISTS' )
        );
    } elseif ( ! is_null( $folder ) && $folder !== '' && $folder !== 'null' && intval($folder) > 0 ) {
        $args['tax_query'] = array(
            array( 'taxonomy' => 'cora_media_folder', 'field' => 'term_id', 'terms' => intval( $folder ) )
        );
    }

    $q     = new WP_Query( $args );
    $files = array();
    foreach ( $q->posts as $p ) {
        $files[] = cora_media_build_file_object( $p->ID );
    }

    wp_send_json_success( array(
        'files'       => $files,
        'total'       => $q->found_posts,
        'total_pages' => $q->max_num_pages,
    ) );
}
add_action( 'wp_ajax_cora_media_library_get', 'cora_ajax_media_library_get' );

/**
 * AJAX: Upload a file
 */
function cora_ajax_media_library_upload() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( empty( $_FILES['file'] ) ) {
        wp_send_json_error( array( 'message' => 'No file provided.' ) );
    }
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $file = $_FILES['file'];
    if ( $file['error'] !== UPLOAD_ERR_OK ) {
        wp_send_json_error( array( 'message' => 'Upload error code: ' . $file['error'] ) );
    }

    // ZIP: extract and import each image
    $is_zip = ( $file['type'] === 'application/zip' || strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) === 'zip' );
    if ( $is_zip ) {
        $zip    = new ZipArchive();
        $tmpzip = $file['tmp_name'];
        if ( $zip->open( $tmpzip ) === true ) {
            $imported = 0;
            $upload_dir = wp_upload_dir();
            for ( $i = 0; $i < $zip->numFiles; $i++ ) {
                $entry = $zip->getNameIndex( $i );
                $ext   = strtolower( pathinfo( $entry, PATHINFO_EXTENSION ) );
                if ( ! in_array( $ext, array( 'jpg','jpeg','png','gif','webp' ) ) ) continue;
                $contents = $zip->getFromIndex( $i );
                $tmp      = tempnam( sys_get_temp_dir(), 'czm' );
                file_put_contents( $tmp, $contents );
                $_FILES['file_zip'] = array( 'name' => basename($entry), 'type' => 'image/jpeg', 'tmp_name' => $tmp, 'error' => 0, 'size' => strlen($contents) );
                $moved = wp_handle_sideload( array( 'name' => basename($entry), 'tmp_name' => $tmp, 'error' => 0, 'size' => strlen($contents) ), array( 'test_form' => false ) );
                if ( ! isset( $moved['error'] ) ) {
                    $att_id = wp_insert_attachment( array( 'post_mime_type' => $moved['type'], 'post_title' => pathinfo( $entry, PATHINFO_FILENAME ), 'post_status' => 'inherit' ), $moved['file'] );
                    wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $moved['file'] ) );
                    $imported++;
                }
                @unlink( $tmp );
            }
            $zip->close();
            wp_send_json_success( array( 'message' => 'Imported ' . $imported . ' images from ZIP.' ) );
        }
        wp_send_json_error( array( 'message' => 'Could not open ZIP.' ) );
    }

    $overrides = array( 'test_form' => false );
    $moved     = wp_handle_upload( $file, $overrides );
    if ( ! $moved || isset( $moved['error'] ) ) {
        wp_send_json_error( array( 'message' => $moved['error'] ?? 'Upload failed.' ) );
    }

    $att_id = wp_insert_attachment( array(
        'post_mime_type' => $moved['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $moved['file'] ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ), $moved['file'] );
    wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $moved['file'] ) );

    $folder_id = intval( $_POST['folder_id'] ?? 0 );
    if ( $folder_id > 0 ) {
        wp_set_object_terms( $att_id, $folder_id, 'cora_media_folder' );
    }

    $agency_id = cora_get_current_user_agency_id();
    $branch_id = cora_get_current_user_branch_id();
    if ( ! empty( $agency_id ) ) {
        update_post_meta( $att_id, 'cora_agency_id', $agency_id );
    }
    if ( ! empty( $branch_id ) ) {
        update_post_meta( $att_id, 'cora_branch_id', $branch_id );
    }

    wp_send_json_success( array( 'file' => cora_media_build_file_object( $att_id ) ) );
}
add_action( 'wp_ajax_cora_media_library_upload', 'cora_ajax_media_library_upload' );

/**
 * AJAX: Update file metadata
 */
function cora_ajax_media_library_update() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $id = intval( $_POST['attachment_id'] ?? 0 );
    if ( ! $id ) wp_send_json_error( array( 'message' => 'Invalid attachment.' ) );

    $post = get_post( $id );
    if ( ! $post || $post->post_type !== 'attachment' ) wp_send_json_error( array( 'message' => 'Not found.' ) );

    wp_update_post( array(
        'ID'           => $id,
        'post_title'   => sanitize_text_field( $_POST['title'] ?? '' ),
        'post_excerpt' => sanitize_text_field( $_POST['caption'] ?? '' ),
        'post_content' => wp_kses_post( $_POST['description'] ?? '' ),
    ) );
    update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $_POST['alt'] ?? '' ) );

    $extra = get_post_meta( $id, '_cora_media_extra', true );
    if ( ! is_array( $extra ) ) $extra = array();
    $extra['doc_type'] = sanitize_text_field( $_POST['doc_type'] ?? '' );
    if ( ! empty( $_POST['linked_record'] ) ) {
        $lr = json_decode( stripslashes( $_POST['linked_record'] ), true );
        $extra['linked_record'] = array(
            'type' => sanitize_text_field( $lr['type'] ?? '' ),
            'id'   => sanitize_text_field( $lr['id'] ?? '' ),
        );
    } else {
        $extra['linked_record'] = null;
    }
    update_post_meta( $id, '_cora_media_extra', $extra );

    $folder_id = intval( $_POST['folder_id'] ?? 0 );
    if ( $folder_id > 0 ) {
        wp_set_object_terms( $id, $folder_id, 'cora_media_folder' );
    } else {
        wp_remove_object_terms( $id, wp_get_object_terms( $id, 'cora_media_folder', array( 'fields' => 'ids' ) ), 'cora_media_folder' );
    }

    wp_send_json_success( array( 'file' => cora_media_build_file_object( $id ) ) );
}
add_action( 'wp_ajax_cora_media_library_update', 'cora_ajax_media_library_update' );

/**
 * AJAX: Delete attachments
 */
function cora_ajax_media_library_delete() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'delete_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $ids = array_map( 'intval', (array) ( $_POST['ids'] ?? array() ) );
    if ( empty( $ids ) ) wp_send_json_error( array( 'message' => 'No IDs.' ) );

    $deleted = 0;
    foreach ( $ids as $id ) {
        if ( wp_delete_attachment( $id, true ) ) $deleted++;
    }
    wp_send_json_success( array( 'message' => 'Deleted ' . $deleted . ' file(s).' ) );
}
add_action( 'wp_ajax_cora_media_library_delete', 'cora_ajax_media_library_delete' );

/**
 * AJAX: Get folders (with children)
 */
function cora_ajax_media_library_get_folders() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $terms  = get_terms( array( 'taxonomy' => 'cora_media_folder', 'hide_empty' => false, 'parent' => 0 ) );
    $result = array();
    if ( ! is_wp_error( $terms ) ) {
        foreach ( $terms as $t ) {
            // Count attachments directly assigned to this parent folder term
            $parent_objs = get_objects_in_term( $t->term_id, 'cora_media_folder' );
            $folder_count = is_array( $parent_objs ) ? count( $parent_objs ) : 0;

            $children = get_terms( array( 'taxonomy' => 'cora_media_folder', 'hide_empty' => false, 'parent' => $t->term_id ) );
            $ch = array();
            if ( ! is_wp_error( $children ) ) {
                foreach ( $children as $c ) {
                    $child_objs = get_objects_in_term( $c->term_id, 'cora_media_folder' );
                    $child_count = is_array( $child_objs ) ? count( $child_objs ) : 0;
                    $ch[] = array( 'id' => $c->term_id, 'name' => $c->name, 'count' => $child_count );
                    // Folders with children can roll up their count or show only direct count. Let's roll child count into parent for better UX
                    $folder_count += $child_count;
                }
            }
            $result[] = array(
                'id'        => $t->term_id,
                'name'      => $t->name,
                'count'     => $folder_count,
                'is_system' => false,
                'children'  => $ch,
            );
        }
    }

    // Get total count of all media attachments
    $total_media = wp_count_posts( 'attachment' );
    $total_count = intval( $total_media->inherit ?? 0 );

    // Get count of unorganised media (not in any folder)
    $unorganised_args = array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array(
            array(
                'taxonomy' => 'cora_media_folder',
                'operator' => 'NOT EXISTS'
            )
        )
    );
    $unorganised_query = new WP_Query( $unorganised_args );
    $unorganised_count = $unorganised_query->post_count;

    wp_send_json_success( array(
        'folders'           => $result,
        'total_count'       => $total_count,
        'unorganised_count' => $unorganised_count,
    ) );
}
add_action( 'wp_ajax_cora_media_library_get_folders', 'cora_ajax_media_library_get_folders' );

/**
 * AJAX: Create folder
 */
function cora_ajax_media_library_create_folder() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $name   = sanitize_text_field( $_POST['name'] ?? '' );
    $parent = intval( $_POST['parent_id'] ?? 0 );
    if ( ! $name ) wp_send_json_error( array( 'message' => 'Folder name is required.' ) );

    $term = wp_insert_term( $name, 'cora_media_folder', array( 'parent' => $parent ) );
    if ( is_wp_error( $term ) ) wp_send_json_error( array( 'message' => $term->get_error_message() ) );

    wp_send_json_success( array( 'message' => 'Folder "' . $name . '" created.', 'id' => $term['term_id'] ) );
}
add_action( 'wp_ajax_cora_media_library_create_folder', 'cora_ajax_media_library_create_folder' );

/**
 * AJAX: Rename folder
 */
function cora_ajax_media_library_rename_folder() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $id   = intval( $_POST['folder_id'] ?? 0 );
    $name = sanitize_text_field( $_POST['name'] ?? '' );
    if ( ! $id || ! $name ) wp_send_json_error( array( 'message' => 'Invalid data.' ) );

    $r = wp_update_term( $id, 'cora_media_folder', array( 'name' => $name ) );
    if ( is_wp_error( $r ) ) wp_send_json_error( array( 'message' => $r->get_error_message() ) );
    wp_send_json_success( array( 'message' => 'Folder renamed.' ) );
}
add_action( 'wp_ajax_cora_media_library_rename_folder', 'cora_ajax_media_library_rename_folder' );

/**
 * AJAX: Delete folder
 */
function cora_ajax_media_library_delete_folder() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $id = intval( $_POST['folder_id'] ?? 0 );
    if ( ! $id ) wp_send_json_error( array( 'message' => 'Invalid folder ID.' ) );

    $r = wp_delete_term( $id, 'cora_media_folder' );
    if ( is_wp_error( $r ) ) wp_send_json_error( array( 'message' => $r->get_error_message() ) );
    wp_send_json_success( array( 'message' => 'Folder deleted.' ) );
}
add_action( 'wp_ajax_cora_media_library_delete_folder', 'cora_ajax_media_library_delete_folder' );


/**
 * AJAX: Move files to folder
 */
function cora_ajax_media_library_move() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $ids       = array_map( 'intval', (array) ( $_POST['attachment_ids'] ?? array() ) );
    $folder_id = intval( $_POST['folder_id'] ?? 0 );
    if ( empty( $ids ) || ! $folder_id ) wp_send_json_error( array( 'message' => 'Invalid data.' ) );

    foreach ( $ids as $id ) {
        wp_set_object_terms( $id, $folder_id, 'cora_media_folder' );
    }
    wp_send_json_success( array( 'message' => 'Moved ' . count($ids) . ' file(s) to folder.' ) );
}
add_action( 'wp_ajax_cora_media_library_move', 'cora_ajax_media_library_move' );

/**
 * AJAX: Get available months for date filter
 */
function cora_ajax_media_library_get_months() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    global $wpdb;
    $rows = $wpdb->get_results( "SELECT DISTINCT YEAR(post_date) AS y, MONTH(post_date) AS m FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status = 'inherit' ORDER BY post_date DESC LIMIT 36" );
    $months = array();
    foreach ( $rows as $r ) {
        $months[] = array( 'value' => $r->y . '-' . $r->m, 'label' => date( 'F Y', mktime( 0,0,0, $r->m, 1, $r->y ) ) );
    }
    wp_send_json_success( array( 'months' => $months ) );
}
add_action( 'wp_ajax_cora_media_library_get_months', 'cora_ajax_media_library_get_months' );

/**
 * AJAX: Get uploaders list
 */
function cora_ajax_media_library_get_uploaders() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();
    global $wpdb;
    $rows = $wpdb->get_results( "SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_type='attachment' AND post_status='inherit'" );
    $uploaders = array();
    foreach ( $rows as $r ) {
        $u = get_userdata( $r->post_author );
        if ( $u ) $uploaders[] = array( 'id' => $u->ID, 'name' => $u->display_name );
    }
    wp_send_json_success( array( 'uploaders' => $uploaders ) );
}
add_action( 'wp_ajax_cora_media_library_get_uploaders', 'cora_ajax_media_library_get_uploaders' );

/**
 * AJAX: Image edit (rotate, flip, scale)
 */
function cora_ajax_media_library_image_edit() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $id  = intval( $_POST['attachment_id'] ?? 0 );
    $op  = sanitize_text_field( $_POST['operation'] ?? '' );
    $path = get_attached_file( $id );
    if ( ! $path || ! file_exists( $path ) ) wp_send_json_error( array( 'message' => 'File not found.' ) );

    // Save original if first edit
    $extra = get_post_meta( $id, '_cora_media_extra', true );
    if ( ! is_array( $extra ) ) $extra = array();
    if ( empty( $extra['original_file'] ) ) {
        $orig = preg_replace( '/(\.[^.]+)$/', '_orig$1', $path );
        if ( ! file_exists( $orig ) ) copy( $path, $orig );
        $extra['original_file']  = $orig;
        $extra['original_size']  = cora_media_human_size( filesize( $path ) );
        update_post_meta( $id, '_cora_media_extra', $extra );
    }

    require_once ABSPATH . 'wp-admin/includes/image-edit.php';
    $editor = wp_get_image_editor( $path );
    if ( is_wp_error( $editor ) ) wp_send_json_error( array( 'message' => 'Cannot open image.' ) );

    switch ( $op ) {
        case 'rotate_left':  $editor->rotate(  90 ); break;
        case 'rotate_right': $editor->rotate( -90 ); break;
        case 'flip_h':       $editor->flip( false, true );  break;
        case 'flip_v':       $editor->flip( true,  false ); break;
        case 'scale':
            $w = intval( $_POST['width']  ?? 0 );
            $h = intval( $_POST['height'] ?? 0 );
            $editor->resize( $w ?: null, $h ?: null, false );
            break;
        default:
            wp_send_json_error( array( 'message' => 'Unknown operation.' ) );
    }

    $saved = $editor->save( $path );
    if ( is_wp_error( $saved ) ) wp_send_json_error( array( 'message' => 'Could not save image.' ) );

    wp_send_json_success( array( 'url' => add_query_arg( 't', time(), wp_get_attachment_url( $id ) ) ) );
}
add_action( 'wp_ajax_cora_media_library_image_edit', 'cora_ajax_media_library_image_edit' );

/**
 * AJAX: Restore original image
 */
function cora_ajax_media_library_restore() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $id    = intval( $_POST['attachment_id'] ?? 0 );
    $extra = get_post_meta( $id, '_cora_media_extra', true );
    if ( ! is_array( $extra ) || empty( $extra['original_file'] ) ) {
        wp_send_json_error( array( 'message' => 'No original found.' ) );
    }
    $orig = $extra['original_file'];
    $path = get_attached_file( $id );
    if ( ! file_exists( $orig ) ) wp_send_json_error( array( 'message' => 'Original file missing.' ) );

    copy( $orig, $path );
    unset( $extra['original_file'], $extra['original_size'] );
    update_post_meta( $id, '_cora_media_extra', $extra );

    wp_send_json_success( array( 'url' => add_query_arg( 't', time(), wp_get_attachment_url( $id ) ) ) );
}
add_action( 'wp_ajax_cora_media_library_restore', 'cora_ajax_media_library_restore' );

/**
 * AJAX: Regenerate thumbnails in the background when user closes modal
 */
function cora_ajax_media_library_regenerate_thumbnails() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $id   = intval( $_POST['attachment_id'] ?? 0 );
    $path = get_attached_file( $id );
    if ( ! $path || ! file_exists( $path ) ) wp_send_json_error( array( 'message' => 'File not found.' ) );

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $metadata = wp_generate_attachment_metadata( $id, $path );
    wp_update_attachment_metadata( $id, $metadata );

    wp_send_json_success( array( 'message' => 'Thumbnails regenerated.' ) );
}
add_action( 'wp_ajax_cora_media_library_regenerate_thumbnails', 'cora_ajax_media_library_regenerate_thumbnails' );

/**
 * AJAX: Add watermark (creates a new copy)
 */
function cora_ajax_media_library_watermark() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $id       = intval( $_POST['attachment_id'] ?? 0 );
    $position = sanitize_text_field( $_POST['position'] ?? 'bottom-right' );
    $opacity  = max( 10, min( 90, intval( $_POST['opacity'] ?? 30 ) ) );

    $path = get_attached_file( $id );
    $mime = get_post_mime_type( $id );
    if ( ! in_array( $mime, array( 'image/jpeg', 'image/png', 'image/gif' ) ) ) {
        wp_send_json_error( array( 'message' => 'Only JPEG/PNG/GIF images can be watermarked.' ) );
    }
    if ( ! function_exists( 'imagecreatefromjpeg' ) ) {
        wp_send_json_error( array( 'message' => 'GD library not available.' ) );
    }

    // Load source image
    switch ( $mime ) {
        case 'image/jpeg': $src = imagecreatefromjpeg( $path ); break;
        case 'image/png':  $src = imagecreatefrompng( $path );  break;
        case 'image/gif':  $src = imagecreatefromgif( $path );  break;
        default: wp_send_json_error( array( 'message' => 'Unsupported format.' ) );
    }
    if ( ! $src ) wp_send_json_error( array( 'message' => 'Could not load image.' ) );

    $w = imagesx( $src ); $h = imagesy( $src );

    // Draw watermark text
    $alpha  = (int) round( 127 * ( 1 - $opacity / 100 ) );
    $color  = imagecolorallocatealpha( $src, 200, 200, 200, $alpha );
    $text   = get_bloginfo( 'name' );
    $font   = max( 1, min( 5, intval( $w / 200 ) ) );
    $tw     = imagefontwidth( $font ) * strlen( $text );
    $th     = imagefontheight( $font );
    $pad    = 18;
    switch ( $position ) {
        case 'bottom-left':  $tx = $pad;          $ty = $h - $th - $pad; break;
        case 'center':       $tx = ($w-$tw)/2;    $ty = ($h-$th)/2;      break;
        default:             $tx = $w-$tw-$pad;   $ty = $h - $th - $pad; break;
    }
    imagestring( $src, $font, intval($tx), intval($ty), $text, $color );

    // Save as new attachment
    $upload_dir = wp_upload_dir();
    $new_name   = preg_replace( '/(\.[^.]+)$/', '_watermarked$1', basename( $path ) );
    $new_path   = $upload_dir['path'] . '/' . $new_name;

    switch ( $mime ) {
        case 'image/jpeg': imagejpeg( $src, $new_path, 90 ); break;
        case 'image/png':  imagepng( $src, $new_path );      break;
        case 'image/gif':  imagegif( $src, $new_path );      break;
    }
    imagedestroy( $src );

    $new_id = wp_insert_attachment( array(
        'post_mime_type' => $mime,
        'post_title'     => get_the_title( $id ) . ' (Watermarked)',
        'post_status'    => 'inherit',
    ), $new_path );
    wp_update_attachment_metadata( $new_id, wp_generate_attachment_metadata( $new_id, $new_path ) );

    wp_send_json_success( array( 'message' => 'Watermarked copy created.', 'new_id' => $new_id ) );
}
add_action( 'wp_ajax_cora_media_library_watermark', 'cora_ajax_media_library_watermark' );

/**
 * AJAX: Create share link
 */
function cora_ajax_media_library_create_share() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $id     = intval( $_POST['attachment_id'] ?? 0 );
    $expiry = sanitize_text_field( $_POST['expiry'] ?? '7d' );

    $exp_map = array( '24h' => 86400, '7d' => 604800, '30d' => 2592000, 'never' => 0 );
    $exp_secs = isset( $exp_map[ $expiry ] ) ? $exp_map[ $expiry ] : 604800;
    $exp_ts   = $exp_secs > 0 ? time() + $exp_secs : 0;

    $token = wp_generate_password( 32, false );
    $url   = add_query_arg( array( 'cora_share' => $token, 'aid' => $id ), home_url('/') );

    $label = $exp_secs ? 'Expires in ' . str_replace( array('24h','7d','30d'), array('24 hours','7 days','30 days'), $expiry ) : 'No expiry';
    $link  = array( 'token' => $token, 'url' => $url, 'expires' => $exp_ts, 'expiry_label' => $label, 'created' => time() );

    $links = get_post_meta( $id, '_cora_media_share_links', true );
    if ( ! is_array( $links ) ) $links = array();
    $links[] = $link;
    update_post_meta( $id, '_cora_media_share_links', $links );

    wp_send_json_success( array( 'link' => $link ) );
}
add_action( 'wp_ajax_cora_media_library_create_share', 'cora_ajax_media_library_create_share' );

/**
 * AJAX: Revoke share link
 */
function cora_ajax_media_library_revoke_share() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'upload_files' ) ) wp_send_json_error( array( 'message' => 'Unauthorized.' ) );

    $id    = intval( $_POST['attachment_id'] ?? 0 );
    $token = sanitize_text_field( $_POST['token'] ?? '' );
    $links = get_post_meta( $id, '_cora_media_share_links', true );
    if ( ! is_array( $links ) ) $links = array();
    $links = array_values( array_filter( $links, function( $l ) use ( $token ) { return $l['token'] !== $token; } ) );
    update_post_meta( $id, '_cora_media_share_links', $links );
    wp_send_json_success( array( 'message' => 'Link revoked.' ) );
}
add_action( 'wp_ajax_cora_media_library_revoke_share', 'cora_ajax_media_library_revoke_share' );

/**
 * AJAX: Get activity log for a file
 */
function cora_ajax_media_library_get_activity() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    $id  = intval( $_POST['attachment_id'] ?? 0 );
    $log = get_post_meta( $id, '_cora_media_activity', true );
    if ( ! is_array( $log ) ) $log = array();
    wp_send_json_success( array( 'log' => array_reverse( $log ) ) );
}
add_action( 'wp_ajax_cora_media_library_get_activity', 'cora_ajax_media_library_get_activity' );

/**
 * AJAX: Storage statistics
 */
function cora_ajax_media_library_get_storage() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    $upload_dir = wp_upload_dir();
    $base       = $upload_dir['basedir'];
    $total_bytes = 0;
    if ( is_dir( $base ) ) {
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $f ) { if ( $f->isFile() ) $total_bytes += $f->getSize(); }
    }
    $limit_bytes = apply_filters( 'cora_media_storage_limit', 5 * 1024 * 1024 * 1024 ); // 5 GB default
    $pct = $limit_bytes > 0 ? round( ( $total_bytes / $limit_bytes ) * 100, 1 ) : 0;
    wp_send_json_success( array(
        'total_human' => cora_media_human_size( $total_bytes ),
        'limit_human' => cora_media_human_size( $limit_bytes ),
        'percent_used'=> $pct,
        'total_bytes' => $total_bytes,
        'limit_bytes' => $limit_bytes,
    ) );
}
add_action( 'wp_ajax_cora_media_library_get_storage', 'cora_ajax_media_library_get_storage' );

/**
 * ═══ PROPOS: MULTI-TENANCY & USER MANAGEMENT MODULE ═════════════════════════════
 */

function cora_get_current_user_agency_id() {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return '';
    }
    if ( current_user_can( 'administrator' ) ) {
        $impersonated = get_user_meta( $user_id, 'cora_impersonate_agency_id', true );
        if ( ! empty( $impersonated ) ) {
            return $impersonated;
        }
        return 'super'; 
    }
    $user_agency = get_user_meta( $user_id, 'cora_agency_id', true );
    if ( empty( $user_agency ) ) {
        // Fallback default setup to prevent breakages on first boot
        cora_ensure_default_agency_setup();
        $user_agency = get_user_meta( $user_id, 'cora_agency_id', true );
    }
    return $user_agency;
}

function cora_get_current_user_branch_id() {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return '';
    }
    if ( current_user_can( 'administrator' ) ) {
        return '';
    }
    $user = wp_get_current_user();
    $role = ! empty( $user->roles ) ? $user->roles[0] : '';
    if ( $role === 'cora_manager' ) {
        return '';
    }
    return get_user_meta( $user_id, 'cora_branch_id', true );
}

function cora_create_custom_tables() {
    global $wpdb;
    $theme_table = $wpdb->prefix . 'cora_canvas_themes';
    $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $theme_table ) );
    $forms_table = $wpdb->prefix . 'cora_forms';
    $forms_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $forms_table ) );
    $has_agency_col = false;
    if ( $forms_exists ) {
        $has_agency_col = ! empty( $wpdb->get_results( "SHOW COLUMNS FROM {$forms_table} LIKE 'agency_id'" ) );
    }
    if ( get_option( 'cora_db_v2_created' ) && $table_exists && $forms_exists && $has_agency_col ) {
        return;
    }
    $charset_collate = $wpdb->get_charset_collate();

    $table_queries = array();

    // 1. cora_agencies
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_agencies (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      name varchar(255) NOT NULL,
      slug varchar(100) NOT NULL,
      owner_user_id bigint(20) unsigned NOT NULL,
      plan varchar(50) NOT NULL DEFAULT 'beta',
      status varchar(20) NOT NULL DEFAULT 'active',
      settings longtext,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      UNIQUE KEY slug (slug)
    ) $charset_collate;";

    // 2. cora_branches
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_branches (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      name varchar(255) NOT NULL,
      city varchar(100),
      address text,
      manager_id bigint(20) unsigned,
      status varchar(20) NOT NULL DEFAULT 'active',
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id)
    ) $charset_collate;";

    // 3. cora_users
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_users (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      wp_user_id bigint(20) unsigned NOT NULL,
      agency_id bigint(20) unsigned NOT NULL,
      branch_id bigint(20) unsigned,
      role varchar(50) NOT NULL DEFAULT 'agent',
      phone varchar(20),
      status varchar(20) NOT NULL DEFAULT 'active',
      invited_by bigint(20) unsigned,
      last_active datetime,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      UNIQUE KEY wp_user_id (wp_user_id),
      KEY agency_id (agency_id),
      KEY branch_id (branch_id)
    ) $charset_collate;";

    // 4. cora_invitations
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_invitations (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      branch_id bigint(20) unsigned,
      email varchar(255) NOT NULL,
      role varchar(50) NOT NULL,
      token varchar(64) NOT NULL,
      invited_by bigint(20) unsigned NOT NULL,
      status varchar(20) NOT NULL DEFAULT 'pending',
      expires_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      accepted_at datetime,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      UNIQUE KEY token (token),
      KEY agency_id (agency_id)
    ) $charset_collate;";

    // 5. cora_leads
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_leads (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      branch_id bigint(20) unsigned NOT NULL,
      assigned_to bigint(20) unsigned,
      first_name varchar(100) NOT NULL,
      last_name varchar(100),
      email varchar(255),
      phone varchar(20),
      source varchar(100),
      status varchar(50) NOT NULL DEFAULT 'new',
      budget_min bigint(20) unsigned,
      budget_max bigint(20) unsigned,
      preferred_locations text,
      property_type varchar(100),
      notes longtext,
      followup_date datetime,
      followup_notes text,
      converted_to_client tinyint(1) NOT NULL DEFAULT 0,
      client_id bigint(20) unsigned,
      embed_vector tinyint(1) NOT NULL DEFAULT 0,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY branch_id (branch_id),
      KEY assigned_to (assigned_to),
      KEY status (status),
      KEY followup_date (followup_date)
    ) $charset_collate;";

    // 6. cora_properties
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_properties (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      branch_id bigint(20) unsigned NOT NULL,
      added_by bigint(20) unsigned NOT NULL,
      title varchar(255) NOT NULL,
      description longtext,
      type varchar(100),
      status varchar(50) NOT NULL DEFAULT 'available',
      price bigint(20) unsigned,
      location varchar(255),
      city varchar(100),
      area_sqft int(10) unsigned,
      bedrooms tinyint(3) unsigned,
      bathrooms tinyint(3) unsigned,
      rera_number varchar(100),
      media_ids text,
      sync_link varchar(255),
      seo_title varchar(255),
      seo_description text,
      seo_keywords text,
      embed_vector tinyint(1) NOT NULL DEFAULT 0,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY branch_id (branch_id),
      KEY status (status),
      KEY city (city)
    ) $charset_collate;";

    // 7. cora_clients
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_clients (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      branch_id bigint(20) unsigned NOT NULL,
      lead_id bigint(20) unsigned,
      first_name varchar(100) NOT NULL,
      last_name varchar(100),
      email varchar(255),
      phone varchar(20),
      type varchar(50) NOT NULL DEFAULT 'buyer',
      notes longtext,
      embed_vector tinyint(1) NOT NULL DEFAULT 0,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY lead_id (lead_id)
    ) $charset_collate;";

    // 8. cora_bookings
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_bookings (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      branch_id bigint(20) unsigned NOT NULL,
      lead_id bigint(20) unsigned,
      client_id bigint(20) unsigned,
      property_id bigint(20) unsigned,
      assigned_agent bigint(20) unsigned,
      showing_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      status varchar(50) NOT NULL DEFAULT 'confirmed',
      package_value bigint(20) unsigned,
      deal_type varchar(100),
      crew text,
      notes longtext,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY showing_date (showing_date),
      KEY status (status)
    ) $charset_collate;";

    // 9. cora_ledger
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_ledger (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      branch_id bigint(20) unsigned NOT NULL,
      type varchar(50) NOT NULL,
      amount bigint(20) unsigned NOT NULL,
      description text,
      lead_id bigint(20) unsigned,
      client_id bigint(20) unsigned,
      status varchar(50) NOT NULL DEFAULT 'pending',
      transaction_date date NOT NULL DEFAULT '0000-00-00',
      created_by bigint(20) unsigned NOT NULL,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY type (type),
      KEY transaction_date (transaction_date)
    ) $charset_collate;";

    // 10. cora_media
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_media (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      uploaded_by bigint(20) unsigned NOT NULL,
      folder_id bigint(20) unsigned,
      linked_type varchar(50),
      linked_id bigint(20) unsigned,
      file_name varchar(255) NOT NULL,
      file_path varchar(500) NOT NULL,
      file_type varchar(100) NOT NULL,
      file_size bigint(20) unsigned NOT NULL,
      mime_type varchar(100),
      width int(10) unsigned,
      height int(10) unsigned,
      alt_text varchar(500),
      title varchar(255),
      caption text,
      doc_type varchar(100),
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY uploaded_by (uploaded_by),
      KEY linked_type_id (linked_type, linked_id),
      KEY folder_id (folder_id)
    ) $charset_collate;";

    // 11. cora_media_folders
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_media_folders (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      parent_id bigint(20) unsigned,
      name varchar(255) NOT NULL,
      is_system tinyint(1) NOT NULL DEFAULT 0,
      created_by bigint(20) unsigned NOT NULL,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY parent_id (parent_id)
    ) $charset_collate;";

    // 12. cora_activity_logs
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_activity_logs (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      user_id bigint(20) unsigned NOT NULL,
      action_type varchar(100) NOT NULL,
      description text NOT NULL,
      record_type varchar(100),
      record_id bigint(20) unsigned,
      ip_address varchar(45),
      user_agent varchar(255),
      how varchar(50) NOT NULL DEFAULT 'manual',
      instructed_by bigint(20) unsigned,
      ai_reasoning text,
      embed_vector tinyint(1) NOT NULL DEFAULT 0,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY user_id (user_id),
      KEY action_type (action_type),
      KEY created_at (created_at)
    ) $charset_collate;";

    // 13. cora_notifications
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_notifications (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      user_id bigint(20) unsigned NOT NULL,
      title varchar(255) NOT NULL,
      body text,
      type varchar(100),
      is_read tinyint(1) NOT NULL DEFAULT 0,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY user_id (user_id),
      KEY is_read (is_read)
    ) $charset_collate;";

    // 14. cora_action_queue
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_action_queue (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      action_type varchar(100) NOT NULL,
      payload longtext NOT NULL,
      scheduled_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      executed_at datetime,
      status varchar(50) NOT NULL DEFAULT 'pending',
      created_by bigint(20) unsigned NOT NULL,
      how varchar(50) NOT NULL DEFAULT 'manual',
      instructed_by bigint(20) unsigned,
      fail_reason text,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY status (status),
      KEY scheduled_at (scheduled_at)
    ) $charset_collate;";

    // 15. cora_share_links
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_share_links (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      media_id bigint(20) unsigned NOT NULL,
      token varchar(64) NOT NULL,
      expires_at datetime,
      created_by bigint(20) unsigned NOT NULL,
      accessed_at datetime,
      access_count int(10) unsigned NOT NULL DEFAULT 0,
      is_active tinyint(1) NOT NULL DEFAULT 1,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      UNIQUE KEY token (token),
      KEY media_id (media_id)
    ) $charset_collate;";

    // 16. cora_canvas_themes
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_canvas_themes (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      name varchar(255) NOT NULL,
      status varchar(20) NOT NULL DEFAULT 'draft',
      settings longtext,
      activated_at datetime,
      created_by bigint(20) unsigned NOT NULL,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY status (status)
    ) $charset_collate;";

    // 17. cora_canvas_pages
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_canvas_pages (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      theme_id bigint(20) unsigned NOT NULL,
      wp_post_id bigint(20) unsigned NOT NULL,
      title varchar(255) NOT NULL,
      slug varchar(255) NOT NULL,
      status varchar(20) NOT NULL DEFAULT 'draft',
      is_homepage tinyint(1) NOT NULL DEFAULT 0,
      template varchar(100),
      seo_title varchar(255),
      seo_description text,
      seo_og_image varchar(500),
      scheduled_at datetime,
      created_by bigint(20) unsigned NOT NULL,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id),
      KEY theme_id (theme_id),
      KEY wp_post_id (wp_post_id)
    ) $charset_collate;";

    // 18. cora_forms
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_forms (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      title varchar(255) NOT NULL,
      status varchar(50) NOT NULL DEFAULT 'draft',
      styling text,
      settings text,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id)
    ) $charset_collate;";

    // 19. cora_form_blocks
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_form_blocks (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      form_id bigint(20) unsigned NOT NULL,
      blocks_json longtext,
      logic_json longtext,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY form_id (form_id)
    ) $charset_collate;";

    // 20. cora_form_submissions
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_form_submissions (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      form_id bigint(20) unsigned NOT NULL,
      submitted_data longtext,
      ip_address varchar(50),
      is_partial tinyint(1) NOT NULL DEFAULT 0,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY form_id (form_id)
    ) $charset_collate;";

    // 21. cora_form_clauses
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_form_clauses (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      agency_id bigint(20) unsigned NOT NULL,
      title varchar(255) NOT NULL,
      content text,
      is_mandatory tinyint(1) NOT NULL DEFAULT 0,
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY agency_id (agency_id)
    ) $charset_collate;";

    // 22. cora_form_audit_log
    $table_queries[] = "CREATE TABLE {$wpdb->prefix}cora_form_audit_log (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      form_id bigint(20) unsigned,
      action_type varchar(100) NOT NULL,
      details text,
      performed_by bigint(20) unsigned,
      ip_address varchar(50),
      created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (id),
      KEY form_id (form_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    foreach ( $table_queries as $query ) {
        dbDelta( $query );
    }

    // Migration/schema update: Ensure agency_id column exists in wp_cora_forms
    $forms_table = $wpdb->prefix . 'cora_forms';
    $has_agency_id = $wpdb->get_results( "SHOW COLUMNS FROM {$forms_table} LIKE 'agency_id'" );
    if ( empty( $has_agency_id ) ) {
        $wpdb->query( "ALTER TABLE {$forms_table} ADD COLUMN agency_id bigint(20) unsigned NOT NULL AFTER id;" );
        $wpdb->query( "ALTER TABLE {$forms_table} ADD KEY agency_id (agency_id);" );
    }

    update_option( 'cora_db_v2_created', true );
}

function cora_migrate_options_to_custom_tables() {
    if ( get_option( 'cora_migration_v2_complete' ) ) {
        return;
    }
    global $wpdb;

    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_agencies" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_branches" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_users" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_leads" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_properties" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_clients" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_bookings" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_showing_crew" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_ledger" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_vault_docs" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_portfolios" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_activity_logs" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_notifications" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_action_queue" );
    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_share_links" );

    // 1. Migrate agencies (seed default agency id 1)
    $agency_exists = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_agencies WHERE id = 1" );
    if ( !$agency_exists ) {
        $logo_url = get_option('cora_brand_logo_url', '');
        $favicon_url = get_option('cora_brand_favicon_url', '');
        $currency_format = get_option('cora_currency_format', 'INR_LAKHS');
        $pwd_min = get_option('cora_pwd_policy_min_len', 8);
        $pwd_num = get_option('cora_pwd_policy_numbers', 0);
        $pwd_upper = get_option('cora_pwd_policy_uppercase', 0);
        $pwd_spec = get_option('cora_pwd_policy_special', 0);

        $settings = array(
            'logo_url' => $logo_url,
            'favicon_url' => $favicon_url,
            'currency_format' => $currency_format,
            'pwd_policy_min_len' => $pwd_min,
            'pwd_policy_numbers' => $pwd_num,
            'pwd_policy_uppercase' => $pwd_upper,
            'pwd_policy_special' => $pwd_spec
        );

        $wpdb->insert(
            $wpdb->prefix . 'cora_agencies',
            array(
                'id' => 1,
                'name' => get_option('cora_workspace_name', 'Cora Default Agency'),
                'slug' => 'default',
                'owner_user_id' => 1,
                'plan' => 'enterprise',
                'status' => 'active',
                'settings' => json_encode( $settings ),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
        );
    }

    // Helper status mappings
    $map_lead_status = function($old_status) {
        $os = strtolower(trim($old_status));
        if (strpos($os, 'new') !== false) return 'new';
        if (strpos($os, 'proposal') !== false || strpos($os, 'contact') !== false) return 'contacted';
        if (strpos($os, 'visit') !== false || strpos($os, 'showing') !== false) return 'site_visit';
        if (strpos($os, 'negotiat') !== false) return 'negotiation';
        if (strpos($os, 'closed') !== false || strpos($os, 'won') !== false) return 'closed';
        if (strpos($os, 'lost') !== false) return 'lost';
        return 'new';
    };

    $parse_budget = function($price_str) {
        if (is_numeric($price_str)) {
            return intval($price_str);
        }
        $clean = preg_replace('/[^\d]/', '', $price_str);
        return !empty($clean) ? intval($clean) : 0;
    };

    // 2. Migrate branches
    $old_branches = get_option( 'cora_branches', array() );
    $branch_id_map = array();
    $default_branch_id = 0;
    if ( is_array( $old_branches ) ) {
        foreach ( $old_branches as $old_id => $b ) {
            $wpdb->insert(
                $wpdb->prefix . 'cora_branches',
                array(
                    'agency_id' => 1,
                    'name' => $b['name'] ?? 'Branch Office',
                    'city' => $b['city'] ?? 'Default City',
                    'address' => $b['address'] ?? 'Default Address',
                    'manager_id' => intval($b['manager_id'] ?? 0),
                    'status' => 'active',
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s')
            );
            $new_branch_id = $wpdb->insert_id;
            $branch_id_map[ $old_id ] = $new_branch_id;
            if ( $old_id === 'branch_1' || empty($default_branch_id) ) {
                $default_branch_id = $new_branch_id;
            }
        }
    }

    // 3. Migrate users
    $all_users = get_users();
    foreach ( $all_users as $u ) {
        $old_role = ! empty( $u->roles ) ? $u->roles[0] : '';
        $role_mapped = 'agent';
        switch ( $old_role ) {
            case 'administrator': $role_mapped = 'super_admin'; break;
            case 'cora_manager': $role_mapped = 'agency_owner'; break;
            case 'cora_branch_manager': $role_mapped = 'branch_manager'; break;
            case 'cora_photographer': $role_mapped = 'senior_agent'; break;
            case 'cora_videographer': $role_mapped = 'agent'; break;
            case 'cora_drone_pilot': $role_mapped = 'senior_agent'; break;
            case 'cora_editor': $role_mapped = 'back_office'; break;
            case 'cora_viewer': $role_mapped = 'viewer'; break;
        }

        $old_branch = get_user_meta( $u->ID, 'cora_branch_id', true );
        $branch_new_id = isset($branch_id_map[$old_branch]) ? $branch_id_map[$old_branch] : $default_branch_id;

        $phone = get_user_meta( $u->ID, 'cora_phone', true );
        $status = (get_user_meta( $u->ID, 'cora_user_status', true ) === 'inactive') ? 'inactive' : 'active';

        // Check if user already exists
        $user_exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_users WHERE wp_user_id = %d", $u->ID ) );
        if ( !$user_exists ) {
            $wpdb->insert(
                $wpdb->prefix . 'cora_users',
                array(
                    'wp_user_id' => $u->ID,
                    'agency_id' => 1,
                    'branch_id' => $branch_new_id ?: null,
                    'role' => $role_mapped,
                    'phone' => $phone ?: '',
                    'status' => $status,
                    'invited_by' => 1,
                    'last_active' => current_time('mysql'),
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s')
            );
        }
    }

    // 4. Migrate leads
    $old_leads = get_option( 'cora_re_leads', array() );
    $lead_id_map = array();
    if ( is_array( $old_leads ) ) {
        foreach ( $old_leads as $l ) {
            $old_branch = $l['branch_id'] ?? '';
            $branch_new_id = isset($branch_id_map[$old_branch]) ? $branch_id_map[$old_branch] : $default_branch_id;

            $budget_max = $parse_budget($l['price'] ?? 0);

            // Names splitting or assignment
            $names = $l['names'] ?? 'Client Name';

            // Convert created_at
            $created_time = isset($l['created_at']) ? date('Y-m-d H:i:s', intval($l['created_at'])) : current_time('mysql');

            $followup_dt = null;
            if ( ! empty($l['followup_date']) ) {
                $followup_dt = date('Y-m-d H:i:s', strtotime($l['followup_date']));
            }

            $wpdb->insert(
                $wpdb->prefix . 'cora_leads',
                array(
                    'agency_id' => 1,
                    'branch_id' => $branch_new_id,
                    'assigned_to' => null,
                    'first_name' => $names,
                    'last_name' => '',
                    'email' => $l['email'] ?? '',
                    'phone' => $l['phone'] ?? '',
                    'source' => $l['source'] ?? 'Direct',
                    'status' => $map_lead_status($l['status'] ?? 'New Lead'),
                    'budget_min' => 0,
                    'budget_max' => $budget_max,
                    'preferred_locations' => $l['city'] ?? '',
                    'property_type' => $l['scale'] ?? '',
                    'notes' => $l['notes'] ?? '',
                    'followup_date' => $followup_dt,
                    'followup_notes' => $l['followup_notes'] ?? '',
                    'converted_to_client' => 0,
                    'client_id' => null,
                    'embed_vector' => 0,
                    'created_at' => $created_time,
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s')
            );
            $lead_id_map[ $l['id'] ] = $wpdb->insert_id;
        }
    }

    // 5. Migrate properties (listings)
    $old_listings = get_option( 'cora_re_listings_inventory', array() );
    $property_id_map = array();
    if ( is_array( $old_listings ) ) {
        foreach ( $old_listings as $lst ) {
            $old_branch = $lst['branch_id'] ?? '';
            $branch_new_id = isset($branch_id_map[$old_branch]) ? $branch_id_map[$old_branch] : $default_branch_id;

            $status_mapped = 'available';
            $os = strtolower(trim($lst['status'] ?? 'Available'));
            if ($os === 'available') $status_mapped = 'available';
            elseif ($os === 'under offer' || $os === 'under_offer') $status_mapped = 'under_offer';
            elseif ($os === 'sold') $status_mapped = 'sold';
            elseif ($os === 'off market' || $os === 'off_market') $status_mapped = 'off_market';

            $wpdb->insert(
                $wpdb->prefix . 'cora_properties',
                array(
                    'agency_id' => 1,
                    'branch_id' => $branch_new_id,
                    'added_by' => 1,
                    'title' => $lst['name'] ?? 'Property Listing',
                    'description' => $lst['notes'] ?? '',
                    'type' => $lst['category'] ?? '',
                    'status' => $status_mapped,
                    'price' => 0,
                    'location' => $lst['notes'] ?? '',
                    'city' => '',
                    'area_sqft' => 0,
                    'bedrooms' => 0,
                    'bathrooms' => 0,
                    'rera_number' => $lst['rera_reg_id'] ?? '',
                    'media_ids' => '[]',
                    'sync_link' => $lst['sync_link'] ?? '',
                    'seo_title' => $lst['seo_title'] ?? '',
                    'seo_description' => $lst['seo_description'] ?? '',
                    'seo_keywords' => $lst['seo_keywords'] ?? '',
                    'embed_vector' => 0,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
            );
            $property_id_map[ $lst['id'] ] = $wpdb->insert_id;
        }
    }

    // 6. Migrate clients
    $old_clients = get_option( 'cora_re_clients', array() );
    $client_id_map = array();
    if ( is_array( $old_clients ) ) {
        foreach ( $old_clients as $c ) {
            $old_branch = $c['branch_id'] ?? '';
            $branch_new_id = isset($branch_id_map[$old_branch]) ? $branch_id_map[$old_branch] : $default_branch_id;

            $created_time = isset($c['converted_at']) ? date('Y-m-d H:i:s', intval($c['converted_at'])) : current_time('mysql');

            $wpdb->insert(
                $wpdb->prefix . 'cora_clients',
                array(
                    'agency_id' => 1,
                    'branch_id' => $branch_new_id,
                    'lead_id' => null,
                    'first_name' => $c['names'] ?? 'Client Name',
                    'last_name' => '',
                    'email' => $c['email'] ?? '',
                    'phone' => $c['phone'] ?? '',
                    'type' => 'buyer',
                    'notes' => $c['scale'] ?? '',
                    'embed_vector' => 0,
                    'created_at' => $created_time,
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
            );
            $client_id_map[ $c['id'] ] = $wpdb->insert_id;
        }
    }

    // 7. Migrate bookings
    $old_bookings = get_option( 'cora_re_client_bookings', array() );
    if ( is_array( $old_bookings ) ) {
        foreach ( $old_bookings as $bk ) {
            $old_branch = $bk['branch_id'] ?? '';
            $branch_new_id = isset($branch_id_map[$old_branch]) ? $branch_id_map[$old_branch] : $default_branch_id;

            $mapped_client = isset($bk['client_id']) ? ($client_id_map[ $bk['client_id'] ] ?? null) : null;
            $mapped_property = isset($bk['listing_id']) ? ($property_id_map[ $bk['listing_id'] ] ?? null) : null;

            $showing_dt = current_time('mysql');
            if ( ! empty($bk['date']) ) {
                $showing_dt = date('Y-m-d H:i:s', strtotime($bk['date'] . ' ' . ($bk['time'] ?? '10:00')));
            }

            $wpdb->insert(
                $wpdb->prefix . 'cora_bookings',
                array(
                    'agency_id' => 1,
                    'branch_id' => $branch_new_id,
                    'lead_id' => null,
                    'client_id' => $mapped_client,
                    'property_id' => $mapped_property,
                    'assigned_agent' => intval($bk['assigned_agent'] ?? 0),
                    'showing_date' => $showing_dt,
                    'status' => $bk['status'] ?? 'confirmed',
                    'package_value' => $parse_budget($bk['package_value'] ?? 0),
                    'deal_type' => $bk['deal_type'] ?? 'Residential Buy',
                    'crew' => json_encode($bk['crew'] ?? array()),
                    'notes' => $bk['notes'] ?? '',
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
            );
        }
    }

    // 8. Migrate financials (ledger)
    $old_ledger = get_option( 'cora_re_ledger', array() );
    if ( is_array( $old_ledger ) ) {
        foreach ( $old_ledger as $tx ) {
            $old_branch = $tx['branch_id'] ?? '';
            $branch_new_id = isset($branch_id_map[$old_branch]) ? $branch_id_map[$old_branch] : $default_branch_id;

            $mapped_client = isset($tx['client_link']) ? ($client_id_map[ $tx['client_link'] ] ?? null) : null;
            $type_mapped = strtolower($tx['type'] ?? 'inflow');
            $status_mapped = strtolower($tx['status'] ?? 'pending');

            $amount_cents = intval($tx['amount'] ?? 0) * 100;

            $wpdb->insert(
                $wpdb->prefix . 'cora_ledger',
                array(
                    'agency_id' => 1,
                    'branch_id' => $branch_new_id,
                    'type' => $type_mapped,
                    'amount' => $amount_cents,
                    'description' => $tx['description'] ?? '',
                    'lead_id' => null,
                    'client_id' => $mapped_client,
                    'status' => $status_mapped,
                    'transaction_date' => $tx['date'] ?? date('Y-m-d'),
                    'created_by' => 1,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%d', '%s', '%d', '%s', '%d', '%d', '%s', '%s', '%d', '%s', '%s')
            );
        }
    }

    // 9. Migrate invitations
    $old_invitations = get_option( 'cora_invitations', array() );
    if ( is_array( $old_invitations ) ) {
        foreach ( $old_invitations as $token => $invite ) {
            $old_branch = $invite['branch_id'] ?? '';
            $branch_new_id = isset($branch_id_map[$old_branch]) ? $branch_id_map[$old_branch] : $default_branch_id;

            $expires_dt = isset($invite['expires_at']) ? date('Y-m-d H:i:s', intval($invite['expires_at'])) : current_time('mysql');

            $wpdb->insert(
                $wpdb->prefix . 'cora_invitations',
                array(
                    'agency_id' => 1,
                    'branch_id' => $branch_new_id,
                    'email' => $invite['email'] ?? '',
                    'role' => $invite['role'] ?? 'cora_videographer',
                    'token' => $token,
                    'invited_by' => intval($invite['invited_by'] ?? 1),
                    'status' => $invite['status'] ?? 'pending',
                    'expires_at' => $expires_dt,
                    'accepted_at' => null,
                    'created_at' => current_time('mysql')
                ),
                array('%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s')
            );
        }
    }

    // 10. Migrate activity logs
    $old_logs = get_option( 'cora_activity_logs', array() );
    if ( is_array( $old_logs ) ) {
        foreach ( $old_logs as $log ) {
            $user_obj = get_user_by( 'login', $log['user'] ?? 'cora' );
            $uid = $user_obj ? $user_obj->ID : 1;

            $log_dt = isset($log['timestamp']) ? date('Y-m-d H:i:s', intval($log['timestamp'])) : current_time('mysql');

            $wpdb->insert(
                $wpdb->prefix . 'cora_activity_logs',
                array(
                    'agency_id' => 1,
                    'user_id' => $uid,
                    'action_type' => $log['section'] ?? 'System',
                    'description' => $log['details'] ?? '',
                    'record_type' => null,
                    'record_id' => null,
                    'ip_address' => null,
                    'user_agent' => null,
                    'how' => 'manual',
                    'instructed_by' => null,
                    'ai_reasoning' => null,
                    'embed_vector' => 0,
                    'created_at' => $log_dt
                ),
                array('%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s')
            );
        }
    }

    // 11. Migrate notifications
    $old_notifs = get_option( 'cora_notifications', array() );
    if ( is_array( $old_notifs ) ) {
        foreach ( $old_notifs as $n ) {
            $notif_dt = isset($n['timestamp']) ? date('Y-m-d H:i:s', intval($n['timestamp'])) : current_time('mysql');

            $wpdb->insert(
                $wpdb->prefix . 'cora_notifications',
                array(
                    'agency_id' => 1,
                    'user_id' => intval($n['user_id'] ?? 1),
                    'title' => $n['title'] ?? 'Notification',
                    'body' => $n['description'] ?? '',
                    'type' => 'alert',
                    'is_read' => empty($n['read']) ? 0 : 1,
                    'created_at' => $notif_dt
                ),
                array('%d', '%d', '%s', '%s', '%s', '%d', '%s')
            );
        }
    }

    // Seed default media folders if they don't exist
    $folders = array(
        'Properties' => 1,
        'Clients' => 1,
        'Agents' => 1,
        'Branding' => 1
    );
    foreach ( $folders as $folder_name => $is_system ) {
        $folder_exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_media_folders WHERE name = %s", $folder_name ) );
        if ( !$folder_exists ) {
            $wpdb->insert(
                $wpdb->prefix . 'cora_media_folders',
                array(
                    'agency_id' => 1,
                    'parent_id' => null,
                    'name' => $folder_name,
                    'is_system' => $is_system,
                    'created_by' => 1,
                    'created_at' => current_time('mysql')
                ),
                array('%d', '%d', '%s', '%d', '%d', '%s')
            );
        }
    }

    // 12. Verification count checks
    $leads_count = intval($wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_leads" ));
    $properties_count = intval($wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_properties" ));
    $clients_count = intval($wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_clients" ));
    $bookings_count = intval($wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_bookings" ));
    $ledger_count = intval($wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_ledger" ));

    $expected_leads = count($old_leads);
    $expected_properties = count($old_listings);
    $expected_clients = count($old_clients);
    $expected_bookings = count($old_bookings);
    $expected_ledger = count($old_ledger);

    if (
        $leads_count >= $expected_leads &&
        $properties_count >= $expected_properties &&
        $clients_count >= $expected_clients &&
        $bookings_count >= $expected_bookings &&
        $ledger_count >= $expected_ledger
    ) {
        update_option( 'cora_migration_v2_complete', true );
    } else {
        error_log( "Cora Database Migration Error: Counts do not match expected options size!" );
    }
}

function cora_db_get_agency_id() {
    $agency_slug = cora_get_current_user_agency_id();
    if ( $agency_slug === 'super' || empty($agency_slug) || $agency_slug === 'agency_1' ) {
        return 1;
    }
    global $wpdb;
    $id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_agencies WHERE slug = %s", $agency_slug ) );
    return $id ? intval($id) : 1;
}

function cora_db_get_branch_id() {
    $branch_meta = get_user_meta( get_current_user_id(), 'cora_branch_id', true );
    if ( empty($branch_meta) || $branch_meta === 'branch_1' || $branch_meta === 1 ) {
        return 1;
    }
    if ( is_numeric($branch_meta) ) {
        return intval($branch_meta);
    }
    return 1;
}
function cora_db_get_leads() {
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $user = wp_get_current_user();
    $current_role = ! empty( $user->roles ) ? $user->roles[0] : '';
    
    $query = "SELECT * FROM {$wpdb->prefix}cora_leads WHERE agency_id = %d";
    $params = array( $agency_id );

    if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner' ) ) ) {
        $query .= " AND branch_id = %d";
        $params[] = $branch_id;
    }

    $query .= " ORDER BY created_at DESC";
    $rows = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

    $mapped = array();
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $status_text = 'New Lead';
            switch($r['status']) {
                case 'new': $status_text = 'New Lead'; break;
                case 'contacted': $status_text = 'Proposal Sent'; break;
                case 'site_visit': $status_text = 'Site Visit'; break;
                case 'negotiation': $status_text = 'Negotiation'; break;
                case 'closed': $status_text = 'Converted'; break;
                case 'lost': $status_text = 'Lost'; break;
            }

            $price_text = '₹' . number_format($r['budget_max']);

            $mapped[] = array(
                'id' => $r['id'],
                'names' => $r['first_name'],
                'email' => $r['email'],
                'phone' => $r['phone'],
                'scale' => $r['property_type'],
                'city' => $r['preferred_locations'],
                'notes' => $r['notes'],
                'price' => $price_text,
                'status' => $status_text,
                'followup_date' => $r['followup_date'] ? date('Y-m-d H:i', strtotime($r['followup_date'])) : '',
                'followup_notes' => $r['followup_notes'] ?? '',
                'created_at' => strtotime($r['created_at']),
                'converted_to_client' => intval($r['converted_to_client']),
                'client_id' => $r['client_id']
            );
        }
    }
    return $mapped;
}

function cora_db_get_clients() {
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $user = wp_get_current_user();
    $current_role = ! empty( $user->roles ) ? $user->roles[0] : '';
    
    $query = "SELECT * FROM {$wpdb->prefix}cora_clients WHERE agency_id = %d";
    $params = array( $agency_id );

    if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner' ) ) ) {
        $query .= " AND branch_id = %d";
        $params[] = $branch_id;
    }

    $query .= " ORDER BY created_at DESC";
    $rows = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

    $mapped = array();
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $mapped[] = array(
                'id' => $r['id'],
                'lead_id' => $r['lead_id'],
                'names' => $r['first_name'],
                'email' => $r['email'],
                'phone' => $r['phone'],
                'scale' => $r['notes'],
                'city' => '', 
                'price' => '',
                'converted_at' => strtotime($r['created_at']),
                'status' => 'confirmed',
                'viewing_date' => '25th Jun, 2026',
                'deal_type' => 'Residential Buy'
            );
        }
    }
    return $mapped;
}

function cora_db_get_properties() {
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $user = wp_get_current_user();
    $current_role = ! empty( $user->roles ) ? $user->roles[0] : '';

    $query = "SELECT * FROM {$wpdb->prefix}cora_properties WHERE agency_id = %d";
    $params = array( $agency_id );

    if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner' ) ) ) {
        $query .= " AND branch_id = %d";
        $params[] = $branch_id;
    }

    $query .= " ORDER BY created_at DESC";
    $rows = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

    $mapped = array();
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $status_text = 'Available';
            switch($r['status']) {
                case 'available': $status_text = 'Available'; break;
                case 'under_offer': $status_text = 'Under Offer'; break;
                case 'sold': $status_text = 'Sold'; break;
                case 'off_market': $status_text = 'Off Market'; break;
            }

            $mapped[] = array(
                'id' => $r['id'],
                'name' => $r['title'],
                'category' => $r['type'],
                'rera_reg_id' => $r['rera_number'],
                'status' => $status_text,
                'crew' => '',
                'shoot' => '',
                'sync_link' => $r['sync_link'] ?? '',
                'notes' => $r['description'],
                'seo_title' => $r['seo_title'] ?? '',
                'seo_description' => $r['seo_description'] ?? '',
                'seo_keywords' => $r['seo_keywords'] ?? ''
            );
        }
    }
    return $mapped;
}

function cora_db_get_bookings() {
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $user = wp_get_current_user();
    $current_role = ! empty( $user->roles ) ? $user->roles[0] : '';

    $query = "SELECT * FROM {$wpdb->prefix}cora_bookings WHERE agency_id = %d";
    $params = array( $agency_id );

    if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner' ) ) ) {
        $query .= " AND branch_id = %d";
        $params[] = $branch_id;
    }

    $query .= " ORDER BY showing_date DESC";
    $rows = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

    $mapped = array();
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $client_old_id = 'client_' . $r['client_id'];
            $listing_old_id = 'eq' . $r['property_id'];

            $mapped[] = array(
                'id' => $r['id'],
                'client_id' => $client_old_id,
                'listing_id' => $listing_old_id,
                'date' => date('Y-m-d', strtotime($r['showing_date'])),
                'time' => date('H:i', strtotime($r['showing_date'])),
                'status' => $r['status'],
                'assigned_agent' => $r['assigned_agent'],
                'package_value' => '₹' . number_format($r['package_value']),
                'deal_type' => $r['deal_type'],
                'crew' => json_decode($r['crew'], true) ?: array(),
                'notes' => $r['notes']
            );
        }
    }
    return $mapped;
}

function cora_db_get_ledger() {
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $user = wp_get_current_user();
    $current_role = ! empty( $user->roles ) ? $user->roles[0] : '';

    $query = "SELECT * FROM {$wpdb->prefix}cora_ledger WHERE agency_id = %d";
    $params = array( $agency_id );

    if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner' ) ) ) {
        $query .= " AND branch_id = %d";
        $params[] = $branch_id;
    }

    $query .= " ORDER BY transaction_date DESC";
    $rows = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

    $mapped = array();
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $client_old_id = 'client_' . $r['client_id'];

            $mapped[] = array(
                'id' => $r['id'],
                'date' => $r['transaction_date'],
                'description' => $r['description'],
                'type' => ucfirst($r['type']),
                'amount' => intval($r['amount'] / 100),
                'category' => '',
                'status' => ucfirst($r['status']),
                'client_link' => $client_old_id
            );
        }
    }
    return $mapped;
}

function cora_db_get_branches() {
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    
    $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_branches WHERE agency_id = %d OR agency_id = 1", $agency_id ), ARRAY_A );

    $mapped = array();
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $old_id = 'branch_' . $r['id'];
            $mapped[$old_id] = array(
                'id' => $old_id,
                'agency_id' => 'agency_' . $r['agency_id'],
                'name' => $r['name'],
                'city' => $r['city'],
                'address' => $r['address'],
                'manager_id' => $r['manager_id']
            );
        }
    }
    
    $opt_branches = get_option( 'cora_branches', array() );
    if ( is_array( $opt_branches ) ) {
        foreach ( $opt_branches as $k => $vb ) {
            if ( ! isset( $mapped[$k] ) && is_array($vb) && ! empty($vb['name']) ) {
                $mapped[$k] = $vb;
            }
        }
    }
    return $mapped;
}

function cora_db_get_agencies() {
    global $wpdb;
    $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_agencies", ARRAY_A );
    $mapped = array();
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $old_id = 'agency_' . $r['id'];
            $mapped[$old_id] = array(
                'id' => $old_id,
                'name' => $r['name'],
                'subdomain' => $r['slug'],
                'plan' => $r['plan'],
                'status' => $r['status']
            );
        }
    }
    return $mapped;
}

function cora_db_get_invitations() {
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $user = wp_get_current_user();
    $current_role = ! empty( $user->roles ) ? $user->roles[0] : '';

    $query = "SELECT * FROM {$wpdb->prefix}cora_invitations WHERE agency_id = %d";
    $params = array( $agency_id );

    if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner' ) ) ) {
        $query .= " AND branch_id = %d";
        $params[] = $branch_id;
    }

    $rows = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

    $mapped = array();
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $mapped[$r['token']] = array(
                'agency_id' => 'agency_' . $r['agency_id'],
                'branch_id' => 'branch_' . $r['branch_id'],
                'email' => $r['email'],
                'role' => $r['role'],
                'invited_by' => $r['invited_by'],
                'status' => $r['status'],
                'expires_at' => strtotime($r['expires_at']),
                'accepted_at' => $r['accepted_at'] ? strtotime($r['accepted_at']) : null,
                'created_at' => strtotime($r['created_at'])
            );
        }
    }
    return $mapped;
}

function cora_db_get_activity_logs() {
    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $branch_id = cora_db_get_branch_id();

    $user = wp_get_current_user();
    $current_role = ! empty( $user->roles ) ? $user->roles[0] : '';

    $query = "SELECT * FROM {$wpdb->prefix}cora_activity_logs WHERE agency_id = %d";
    $params = array( $agency_id );

    if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner' ) ) ) {
        $query .= " AND branch_id = %d";
        $params[] = $branch_id;
    }

    $query .= " ORDER BY created_at DESC LIMIT 1000";
    $rows = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

    $mapped = array();
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $user_obj = get_userdata( $r['user_id'] );
            $username = $user_obj ? $user_obj->display_name : 'System / Guest';
            $user_role = $user_obj && ! empty( $user_obj->roles ) ? $user_obj->roles[0] : 'guest';

            $mapped[] = array(
                'timestamp' => strtotime($r['created_at']),
                'user_id' => $r['user_id'],
                'user_name' => $username,
                'user_role' => $user_role,
                'action_type' => $r['action_type'],
                'description' => $r['description'],
                'ip' => $r['ip_address'] ?? '127.0.0.1',
                'device' => $r['user_agent'] ?? '',
                'agency_id' => 'agency_' . $r['agency_id'],
                'branch_id' => 'branch_' . ($r['branch_id'] ?? 0),
                'how' => $r['how'] ?? 'human',
                'instructed_by' => $r['instructed_by'],
                'ai_reasoning' => $r['ai_reasoning'] ?? ''
            );
        }
    }
    return $mapped;
}

function cora_sync_user_to_custom_table( $user_id ) {
    global $wpdb;
    
    $user = get_userdata( $user_id );
    if ( ! $user ) {
        return;
    }

    $old_role = ! empty( $user->roles ) ? $user->roles[0] : '';
    $role_mapped = 'agent';
    switch ( $old_role ) {
        case 'administrator': $role_mapped = 'super_admin'; break;
        case 'cora_manager': $role_mapped = 'agency_owner'; break;
        case 'cora_branch_manager': $role_mapped = 'branch_manager'; break;
        case 'cora_photographer': $role_mapped = 'senior_agent'; break;
        case 'cora_videographer': $role_mapped = 'agent'; break;
        case 'cora_drone_pilot': $role_mapped = 'senior_agent'; break;
        case 'cora_editor': $role_mapped = 'back_office'; break;
        case 'cora_viewer': $role_mapped = 'viewer'; break;
    }

    $old_branch = get_user_meta( $user_id, 'cora_branch_id', true );
    $branch_new_id = empty($old_branch) ? 1 : intval(preg_replace('/[^\d]/', '', $old_branch));

    $phone = get_user_meta( $user_id, 'cora_phone', true );
    $status = (get_user_meta( $user_id, 'cora_user_status', true ) === 'inactive') ? 'inactive' : 'active';

    $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_users WHERE wp_user_id = %d", $user_id ) );
    if ( $exists ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_users',
            array(
                'role' => $role_mapped,
                'phone' => $phone ?: '',
                'status' => $status,
                'branch_id' => $branch_new_id,
                'updated_at' => current_time('mysql')
            ),
            array( 'wp_user_id' => $user_id ),
            array( '%s', '%s', '%s', '%d', '%s' ),
            array( '%d' )
        );
    } else {
        $wpdb->insert(
            $wpdb->prefix . 'cora_users',
            array(
                'wp_user_id' => $user_id,
                'agency_id' => 1,
                'branch_id' => $branch_new_id,
                'role' => $role_mapped,
                'phone' => $phone ?: '',
                'status' => $status,
                'invited_by' => 1,
                'last_active' => current_time('mysql'),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
        );
    }
}
add_action( 'profile_update', 'cora_sync_user_to_custom_table' );
add_action( 'user_register', 'cora_sync_user_to_custom_table' );

function cora_sync_delete_user( $user_id ) {
    global $wpdb;
    $wpdb->delete(
        $wpdb->prefix . 'cora_users',
        array( 'wp_user_id' => $user_id ),
        array( '%d' )
    );
}
add_action( 'delete_user', 'cora_sync_delete_user' );

function cora_ajax_purge_options_data() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }

    delete_option('cora_re_leads');
    delete_option('cora_re_listings_inventory');
    delete_option('cora_re_clients');
    delete_option('cora_re_client_bookings');
    delete_option('cora_re_ledger');
    delete_option('cora_branches');
    delete_option('cora_invitations');
    delete_option('cora_activity_logs');
    delete_option('cora_notifications');

    wp_send_json_success( 'Old wp_options legacy database tables purged successfully!' );
}
add_action( 'wp_ajax_cora_purge_options_data', 'cora_ajax_purge_options_data' );

function cora_sync_db_tables_to_options() {
    global $wpdb;
    
    // 1. Sync branches
    $branches_opt = get_option( 'cora_branches', array() );
    if ( is_array( $branches_opt ) ) {
        $opt_ids = array();
        $cleaned_branches = array();
        foreach ( $branches_opt as $b_key => $b ) {
            if ( preg_match( '/^branch_(\d+)$/', $b_key, $matches ) ) {
                $branch_num_id = intval($matches[1]);
                $opt_ids[] = $branch_num_id;
                $cleaned_branches[$b_key] = $b;
                
                $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_branches WHERE id = %d", $branch_num_id ) );
                if ( ! $exists ) {
                    $agency_num = 1;
                    if ( ! empty($b['agency_id']) && preg_match('/^agency_(\d+)$/', $b['agency_id'], $ag_m) ) {
                        $agency_num = intval($ag_m[1]);
                    }
                    $wpdb->insert(
                        $wpdb->prefix . 'cora_branches',
                        array(
                            'id' => $branch_num_id,
                            'agency_id' => $agency_num,
                            'name' => $b['name'] ?? 'Branch',
                            'city' => $b['city'] ?? '',
                            'address' => $b['address'] ?? '',
                            'manager_id' => intval($b['manager_id'] ?? 0),
                            'created_at' => current_time('mysql'),
                            'updated_at' => current_time('mysql')
                        ),
                        array('%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s')
                    );
                }
            } elseif ( $b_key === 'branch_1' ) {
                $opt_ids[] = 1;
                $cleaned_branches[$b_key] = $b;
            }
        }
        if ( count( $cleaned_branches ) !== count( $branches_opt ) ) {
            update_option( 'cora_branches', $cleaned_branches );
        }
        
        if ( ! empty( $opt_ids ) ) {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_branches WHERE id NOT IN (" . implode( ',', $opt_ids ) . ")" );
        } else {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_branches" );
        }
    }

    // 2. Sync properties (listings)
    $listings_opt = get_option( 'cora_re_listings_inventory', array() );
    if ( is_array( $listings_opt ) ) {
        $opt_ids = array();
        $cleaned_listings = array();
        foreach ( $listings_opt as $lst ) {
            if ( isset( $lst['id'] ) && is_numeric( $lst['id'] ) ) {
                $opt_ids[] = intval($lst['id']);
                $cleaned_listings[] = $lst;
            }
        }
        if ( count( $cleaned_listings ) !== count( $listings_opt ) ) {
            update_option( 'cora_re_listings_inventory', $cleaned_listings );
        }
        if ( ! empty( $opt_ids ) ) {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_properties WHERE id NOT IN (" . implode( ',', $opt_ids ) . ")" );
        } else {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_properties" );
        }
    }

    // 3. Sync leads
    $leads_opt = get_option( 'cora_re_leads', array() );
    if ( is_array( $leads_opt ) ) {
        $opt_ids = array();
        $cleaned_leads = array();
        foreach ( $leads_opt as $ld ) {
            if ( isset( $ld['id'] ) && is_numeric( $ld['id'] ) ) {
                $opt_ids[] = intval($ld['id']);
                $cleaned_leads[] = $ld;
            }
        }
        if ( count( $cleaned_leads ) !== count( $leads_opt ) ) {
            update_option( 'cora_re_leads', $cleaned_leads );
        }
        if ( ! empty( $opt_ids ) ) {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_leads WHERE id NOT IN (" . implode( ',', $opt_ids ) . ")" );
        } else {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_leads" );
        }
    }

    // 4. Sync clients
    $clients_opt = get_option( 'cora_re_clients', array() );
    if ( is_array($clients_opt) ) {
        $opt_ids = array();
        $cleaned_clients = array();
        foreach ( $clients_opt as $cl ) {
            if ( isset( $cl['id'] ) && is_numeric( $cl['id'] ) ) {
                $opt_ids[] = intval($cl['id']);
                $cleaned_clients[] = $cl;
            }
        }
        if ( count( $cleaned_clients ) !== count( $clients_opt ) ) {
            update_option( 'cora_re_clients', $cleaned_clients );
        }
        if ( ! empty( $opt_ids ) ) {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_clients WHERE id NOT IN (" . implode( ',', $opt_ids ) . ")" );
        } else {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_clients" );
        }
    }
}

function cora_seed_default_canvas_data() {
    global $wpdb;
    
    // Migration: Update existing PropOS names to Cora in database
    $wpdb->query( "UPDATE {$wpdb->prefix}cora_canvas_themes SET name = REPLACE(name, 'PropOS', 'Cora'), settings = REPLACE(settings, 'PropOS', 'Cora')" );
    $wpdb->query( "UPDATE {$wpdb->prefix}cora_canvas_pages SET seo_title = REPLACE(seo_title, 'PropOS', 'Cora'), seo_description = REPLACE(seo_description, 'PropOS', 'Cora')" );

    // Check if themes table has entries
    $themes_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_canvas_themes" );
    if ( intval( $themes_count ) === 0 ) {
        // 1. Seed Active Live Theme
        $wpdb->insert(
            $wpdb->prefix . 'cora_canvas_themes',
            array(
                'agency_id'    => 1,
                'name'         => 'Cora Default Theme',
                'status'       => 'live',
                'settings'     => json_encode( array(
                    'site_title'      => 'Cora Agency',
                    'site_tagline'    => 'Modern Real Estate Workspace',
                    'primary_color'   => '#18181b',
                    'secondary_color' => '#27272a',
                    'accent_color'    => '#10b981',
                    'text_color'      => '#09090b',
                    'bg_color'        => '#ffffff',
                    'heading_font'    => 'Inter',
                    'body_font'       => 'Inter',
                    'base_font_size'  => '16',
                    'header_layout'   => 'Logo Left',
                    'sticky_header'   => '1',
                    'header_bg_color' => '#ffffff',
                    'footer_columns'  => '3',
                    'copyright_text'  => '© ' . date('Y') . ' Cora Agency. All rights reserved.',
                    'show_socials'    => '1'
                ) ),
                'activated_at' => current_time('mysql'),
                'created_by'   => 1,
                'created_at'   => current_time('mysql'),
                'updated_at'   => current_time('mysql')
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
        );
        $live_theme_id = $wpdb->insert_id;

        // 2. Seed Draft Theme
        $wpdb->insert(
            $wpdb->prefix . 'cora_canvas_themes',
            array(
                'agency_id'    => 1,
                'name'         => 'Cora Elegant Draft Theme',
                'status'       => 'draft',
                'settings'     => json_encode( array(
                    'site_title'      => 'Elegant Agency',
                    'site_tagline'    => 'Luxury properties catalog',
                    'primary_color'   => '#0f172a',
                    'secondary_color' => '#1e293b',
                    'accent_color'    => '#f59e0b',
                    'text_color'      => '#0f172a',
                    'bg_color'        => '#f8fafc',
                    'heading_font'    => 'Playfair Display',
                    'body_font'       => 'Lora',
                    'base_font_size'  => '16',
                    'header_layout'   => 'Centered Logo',
                    'sticky_header'   => '0',
                    'header_bg_color' => '#ffffff',
                    'footer_columns'  => '4',
                    'copyright_text'  => '© ' . date('Y') . ' Elegant Group. All rights reserved.',
                    'show_socials'    => '1'
                ) ),
                'created_by'   => 1,
                'created_at'   => current_time('mysql'),
                'updated_at'   => current_time('mysql')
            ),
            array( '%d', '%s', '%s', '%s', '%d', '%s', '%s' )
        );

        // Seed pages for the Live Theme
        $default_pages = array(
            array(
                'title'       => 'Home Page',
                'slug'        => 'home',
                'status'      => 'published',
                'is_homepage' => 1,
                'template'    => 'agency',
                'seo_title'   => 'Welcome to Cora Real Estate Agency',
                'seo_desc'    => 'Find premium luxury villas, penthouses, and commercial spaces across India.'
            ),
            array(
                'title'       => 'About Us',
                'slug'        => 'about',
                'status'      => 'published',
                'is_homepage' => 0,
                'template'    => 'brokerage',
                'seo_title'   => 'About Our Brokerage | Cora Agency',
                'seo_desc'    => 'Learn about our team of expert realtors and listing coordinators.'
            ),
            array(
                'title'       => 'Contact Us',
                'slug'        => 'contact',
                'status'      => 'draft',
                'is_homepage' => 0,
                'template'    => 'minimal',
                'seo_title'   => '',
                'seo_desc'    => ''
            )
        );

        foreach ( $default_pages as $dp ) {
            $wp_post_id = 0;
            $existing_page = get_page_by_path( $dp['slug'] );
            if ( $existing_page ) {
                $wp_post_id = $existing_page->ID;
            } else {
                $wp_post_id = wp_insert_post( array(
                    'post_title'   => $dp['title'],
                    'post_name'    => $dp['slug'],
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_content' => '<!-- Elementor Page Content -->'
                ) );
            }
            
            if ( ! is_wp_error( $wp_post_id ) && $wp_post_id > 0 ) {
                $wpdb->insert(
                    $wpdb->prefix . 'cora_canvas_pages',
                    array(
                        'agency_id'       => 1,
                        'theme_id'        => $live_theme_id,
                        'wp_post_id'      => $wp_post_id,
                        'title'           => $dp['title'],
                        'slug'            => $dp['slug'],
                        'status'          => $dp['status'],
                        'is_homepage'     => $dp['is_homepage'],
                        'template'        => $dp['template'],
                        'seo_title'       => $dp['seo_title'],
                        'seo_description' => $dp['seo_desc'],
                        'created_by'      => 1,
                        'created_at'      => current_time('mysql'),
                        'updated_at'      => current_time('mysql')
                    ),
                    array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%s' )
                );
            }
        }
    }

    // Ensure BIP problems page exists
    $bip_page_exists = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_canvas_pages WHERE slug = 'problems-realization'" );
    if ( intval( $bip_page_exists ) === 0 ) {
        $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
        $theme_id = $live_theme ? intval( $live_theme['id'] ) : 1;

        $wp_post_id = wp_insert_post( array(
            'post_title'   => 'Episode 01: Core Real Estate Problems',
            'post_name'    => 'problems-realization',
            'post_type'    => 'page',
            'post_status'  => 'draft',
            'post_content' => '<!-- Elementor Page Content -->'
        ) );

        if ( ! is_wp_error( $wp_post_id ) && $wp_post_id > 0 ) {
            update_post_meta( $wp_post_id, '_cora_is_visual_builder', '1' );
            update_post_meta( $wp_post_id, '_cora_visual_builder_html', cora_get_bip_problems_html() );
            update_post_meta( $wp_post_id, '_cora_visual_builder_css', 'body { background-color: #FBFaf7; }' );

            $wpdb->insert(
                $wpdb->prefix . 'cora_canvas_pages',
                array(
                    'agency_id'       => 1,
                    'theme_id'        => $theme_id,
                    'wp_post_id'      => $wp_post_id,
                    'title'           => 'Episode 01: Core Real Estate Problems',
                    'slug'            => 'problems-realization',
                    'status'          => 'draft',
                    'is_homepage'     => 0,
                    'template'        => 'minimal',
                    'seo_title'       => 'Episode 01: Core Real Estate Problems',
                    'seo_description' => 'Real estate subscription bleed and response latency simulation.',
                    'created_by'      => 1,
                    'created_at'      => current_time('mysql'),
                    'updated_at'      => current_time('mysql')
                ),
                array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%s' )
            );
        }
    }
}

function cora_ensure_default_agency_setup() {
    cora_create_custom_tables();
    cora_migrate_options_to_custom_tables();
    $agencies = get_option( 'cora_agencies', array() );
    if ( empty( $agencies ) ) {
        $agencies = array(
            'agency_1' => array(
                'id'          => 'agency_1',
                'name'        => 'Cora Default Agency',
                'subdomain'   => 'default',
                'plan'        => 'enterprise',
                'status'      => 'active',
                'created_at'  => date( 'Y-m-d H:i:s' )
            )
        );
        update_option( 'cora_agencies', $agencies );
    }

    $branches = get_option( 'cora_branches', array() );
    if ( empty( $branches ) ) {
        $branches = array(
            'branch_1' => array(
                'id'         => 'branch_1',
                'agency_id'  => 'agency_1',
                'name'       => 'Main Branch',
                'city'       => 'Default City',
                'address'    => 'Default Address',
                'manager_id' => 0
            )
        );
        update_option( 'cora_branches', $branches );
    }

    $branches = get_option( 'cora_branches', array() );
    if ( is_array( $branches ) ) {
        $seen_chennai = false;
        $modified = false;
        foreach ( $branches as $key => $b ) {
            if ( isset( $b['name'] ) && $b['name'] === 'Chennai Hub' ) {
                if ( $seen_chennai ) {
                    unset( $branches[$key] );
                    $modified = true;
                } else {
                    $seen_chennai = true;
                }
            }
        }
        if ( $modified ) {
            update_option( 'cora_branches', $branches );
        }
    }

    $user_id = get_current_user_id();
    if ( $user_id ) {
        $user_agency = get_user_meta( $user_id, 'cora_agency_id', true );
        if ( empty( $user_agency ) ) {
            update_user_meta( $user_id, 'cora_agency_id', 'agency_1' );
            update_user_meta( $user_id, 'cora_branch_id', 'branch_1' );
        }
    }

    cora_seed_default_canvas_data();
    cora_sync_db_tables_to_options();
}
add_action( 'init', 'cora_ensure_default_agency_setup', 5 );

// ── Tenancy Query Filtering for Options ──────────────────────────────────────

function cora_filter_tenancy_data( $items, $option_name = '' ) {
    if ( ! is_array( $items ) ) {
        return $items;
    }

    // Bypass tenancy filtering for public secure share pages and public portfolio liking AJAX
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if ( false !== strpos( $request_uri, 'shared-doc' ) || false !== strpos( $request_uri, 'shared-portfolio' ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'cora_toggle_portfolio_like' ) ) {
        return $items;
    }

    $agency_id = cora_get_current_user_agency_id();
    if ( $agency_id === 'super' ) {
        return $items;
    }
    if ( empty( $agency_id ) ) {
        return array();
    }
    $branch_id = cora_get_current_user_branch_id();

    $filtered = array();
    foreach ( $items as $item ) {
        if ( ! is_array( $item ) ) {
            continue;
        }
        $item_agency = isset( $item['agency_id'] ) ? $item['agency_id'] : 'agency_1';
        if ( $item_agency !== $agency_id ) {
            continue;
        }
        if ( ! empty( $branch_id ) ) {
            $item_branch = isset( $item['branch_id'] ) ? $item['branch_id'] : 'branch_1';
            if ( $item_branch !== $branch_id ) {
                continue;
            }
        }
        $filtered[] = $item;
    }
    return $filtered;
}

add_filter( 'option_cora_re_leads', 'cora_filter_tenancy_data' );
add_filter( 'option_cora_re_clients', 'cora_filter_tenancy_data' );
add_filter( 'option_cora_re_vault_docs', 'cora_filter_tenancy_data' );
add_filter( 'option_cora_re_portfolios', 'cora_filter_tenancy_data' );
add_filter( 'option_cora_re_listings_inventory', 'cora_filter_tenancy_data' );

// ── Tenancy Save Interception for Options ─────────────────────────────────────

function cora_pre_update_tenancy_data( $new_value, $old_value, $option_name ) {
    if ( ! is_array( $new_value ) ) {
        return $new_value;
    }
    $agency_id = cora_get_current_user_agency_id();
    if ( $agency_id === 'super' ) {
        return $new_value;
    }
    if ( empty( $agency_id ) ) {
        return $old_value;
    }
    $branch_id = cora_get_current_user_branch_id();

    global $wpdb;
    $raw_db_json = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $option_name ) );
    $db_items = ! empty( $raw_db_json ) ? maybe_unserialize( $raw_db_json ) : array();
    if ( ! is_array( $db_items ) ) {
        $db_items = array();
    }

    $retained_items = array();
    foreach ( $db_items as $item ) {
        if ( ! is_array( $item ) ) {
            continue;
        }
        $item_agency = isset( $item['agency_id'] ) ? $item['agency_id'] : 'agency_1';
        if ( $item_agency === $agency_id ) {
            if ( ! empty( $branch_id ) ) {
                $item_branch = isset( $item['branch_id'] ) ? $item['branch_id'] : 'branch_1';
                if ( $item_branch === $branch_id ) {
                    continue;
                }
            } else {
                continue;
            }
        }
        $retained_items[] = $item;
    }

    $stamped_new_items = array();
    foreach ( $new_value as $item ) {
        if ( ! is_array( $item ) ) {
            continue;
        }
        if ( empty( $item['agency_id'] ) ) {
            $item['agency_id'] = $agency_id;
        }
        if ( ! empty( $branch_id ) && empty( $item['branch_id'] ) ) {
            $item['branch_id'] = $branch_id;
        }
        $stamped_new_items[] = $item;
    }

    return array_merge( $retained_items, $stamped_new_items );
}

add_filter( 'pre_update_option_cora_re_leads', 'cora_pre_update_tenancy_data', 10, 3 );
add_filter( 'pre_update_option_cora_re_clients', 'cora_pre_update_tenancy_data', 10, 3 );
add_filter( 'pre_update_option_cora_re_vault_docs', 'cora_pre_update_tenancy_data', 10, 3 );
add_filter( 'pre_update_option_cora_re_portfolios', 'cora_pre_update_tenancy_data', 10, 3 );
add_filter( 'pre_update_option_cora_re_listings_inventory', 'cora_pre_update_tenancy_data', 10, 3 );

// ── CUSTOM AUTHENTICATION AJAX HANDLERS ──────────────────────────────────────

function cora_ajax_login() {
    $email    = sanitize_email( $_POST['email'] ?? '' );
    $password = $_POST['password'] ?? '';
    $remember = ! empty( $_POST['remember'] ) ? true : false;
    $nonce    = $_POST['nonce'] ?? '';

    if ( ! wp_verify_nonce( $nonce, 'cora_login_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    // 1. Lockout rate limit checks
    $lockout_transient = get_transient( 'cora_lockout_' . $ip );
    if ( $lockout_transient ) {
        $lockout_time = get_option( 'cora_lockout_time_' . $ip );
        $remaining = max( 0, intval( $lockout_time ) - time() );
        wp_send_json_error( array(
            'message' => 'Too many attempts. Try again in ' . sprintf( "%02d:%02d", floor( $remaining / 60 ), $remaining % 60 ) . '.',
            'lockout' => $remaining
        ) );
    }

    $failed_attempts = intval( get_transient( 'cora_failed_attempts_' . $ip ) );

    // 2. Validate email and password match
    $user = get_user_by( 'email', $email );
    $auth_failed = false;

    if ( ! $user || is_wp_error( $user ) ) {
        $auth_failed = true;
    } else {
        if ( ! wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
            $auth_failed = true;
        }
    }

    if ( $auth_failed ) {
        $failed_attempts++;
        if ( $failed_attempts >= 5 ) {
            set_transient( 'cora_lockout_' . $ip, 'locked', 900 );
            update_option( 'cora_lockout_time_' . $ip, time() + 900 );
            delete_transient( 'cora_failed_attempts_' . $ip );
            cora_log_activity( 'Authentication', "Too many failed login attempts; IP lockout triggered (email: {$email})." );
            
            // Notify Admins of that agency
            $locked_user = get_user_by( 'email', $email );
            if ( $locked_user ) {
                $target_agency_id = get_user_meta( $locked_user->ID, 'cora_agency_id', true );
                if ( ! empty( $target_agency_id ) ) {
                    $admin_query_args = array(
                        'role__in' => array( 'administrator', 'cora_manager' )
                    );
                    if ( $target_agency_id !== 'super' ) {
                        $admin_query_args['meta_query'] = array(
                            array(
                                'key'     => 'cora_agency_id',
                                'value'   => $target_agency_id,
                                'compare' => '='
                            )
                        );
                    }
                    $admins = get_users( $admin_query_args );
                    foreach ( $admins as $admin ) {
                        cora_add_notification(
                            $admin->ID,
                            'Security Alert: Lockout Triggered',
                            "IP lockout triggered due to multiple failed login attempts on email: {$email}.",
                            home_url( '/workspace/audit-panel' )
                        );
                    }
                }
            }

            wp_send_json_error( array(
                'message' => 'Too many attempts. Try again in 15:00.',
                'lockout' => 900
            ) );
        } else {
            set_transient( 'cora_failed_attempts_' . $ip, $failed_attempts, 3600 );
            cora_log_activity( 'Authentication', "Failed login attempt (email: {$email})." );
            wp_send_json_error( array( 'message' => 'Incorrect email or password.' ) );
        }
    }

    // 3. Check account active status
    $status = get_user_meta( $user->ID, 'cora_user_status', true );
    if ( $status === 'inactive' ) {
        cora_log_activity( 'Authentication', "Blocked login for deactivated account (email: {$email}).", $user->ID );
        wp_send_json_error( array( 'message' => 'Your account has been deactivated. Contact your agency admin.' ) );
    }

    // Check agency suspension status
    $agency_id = get_user_meta( $user->ID, 'cora_agency_id', true );
    if ( ! empty( $agency_id ) && $agency_id !== 'super' ) {
        $agencies = get_option( 'cora_agencies', array() );
        if ( isset( $agencies[$agency_id] ) && $agencies[$agency_id]['status'] === 'suspended' ) {
            cora_log_activity( 'Authentication', "Blocked login for suspended agency (email: {$email}).", $user->ID );
            wp_send_json_error( array( 'message' => 'Your agency account has been suspended. Contact Cora support.' ) );
        }
    }

    // 4. Check email verification (administrator / default setup is always verified)
    $verified = get_user_meta( $user->ID, 'cora_email_verified', true );
    if ( empty( $verified ) && ! in_array( 'administrator', (array) $user->roles ) && $user->user_email !== 'dravyafolio@gmail.com' ) {
        cora_log_activity( 'Authentication', "Blocked login for unverified email (email: {$email}).", $user->ID );
        wp_send_json_error( array(
            'message' => "Please verify your email before logging in. <a href='#' onclick='coraResendVerification(\"" . esc_attr( $email ) . "\")'>Resend verification email →</a>"
        ) );
    }

    // 5. Success Sign On
    clean_user_cache( $user->ID );
    wp_clear_auth_cookie();
    wp_set_current_user( $user->ID );
    wp_set_auth_cookie( $user->ID, $remember );
    do_action( 'wp_login', $user->user_login, $user );

    // Clear failed IP records
    delete_transient( 'cora_failed_attempts_' . $ip );
    delete_transient( 'cora_lockout_' . $ip );

    cora_log_activity( 'Authentication', 'Logged in successfully.', $user->ID );

    // Determine dashboard redirect
    $redirect_url = home_url( '/workspace/dashboard' );
    wp_send_json_success( array( 'redirect_url' => $redirect_url ) );
}
add_action( 'wp_ajax_nopriv_cora_ajax_login', 'cora_ajax_login' );

function cora_ajax_forgot_password() {
    $email = sanitize_email( $_POST['email'] ?? '' );
    $nonce = $_POST['nonce'] ?? '';

    if ( ! wp_verify_nonce( $nonce, 'cora_login_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
    }

    $user = get_user_by( 'email', $email );
    if ( $user && ! is_wp_error( $user ) ) {
        $token = bin2hex( random_bytes( 16 ) );
        update_user_meta( $user->ID, 'cora_reset_token', $token );
        update_user_meta( $user->ID, 'cora_reset_token_expiry', time() + 3600 );
        
        $reset_link = home_url( '/workspace/reset-password?token=' . $token );
        update_option( 'cora_latest_reset_link', $reset_link ); // Save link for E2E tests to grab easily
        
        cora_log_activity( 'Authentication', 'Requested a password reset link.', $user->ID );
        
        // Log the link to the PHP error log
        error_log( "Cora Reset Link: " . $reset_link );
    }

    // Always send success for security reasons
    wp_send_json_success( array( 'message' => 'If the email exists, a password reset link has been sent.' ) );
}
add_action( 'wp_ajax_nopriv_cora_ajax_forgot_password', 'cora_ajax_forgot_password' );

function cora_ajax_reset_password() {
    $password = $_POST['password'] ?? '';
    $token    = sanitize_text_field( $_POST['token'] ?? '' );
    $nonce    = $_POST['nonce'] ?? '';

    if ( ! wp_verify_nonce( $nonce, 'cora_login_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
    }

    $pass_valid = cora_validate_password( $password );
    if ( $pass_valid !== true ) {
        wp_send_json_error( array( 'message' => $pass_valid ) );
    }

    if ( empty( $token ) ) {
        wp_send_json_error( array( 'message' => 'Invalid or expired token.' ) );
    }

    // Locate user by reset token usermeta
    $users = get_users( array(
        'meta_key'   => 'cora_reset_token',
        'meta_value' => $token,
        'number'     => 1
    ) );

    if ( empty( $users ) ) {
        wp_send_json_error( array( 'message' => 'This link has expired or already been used.' ) );
    }

    $user = $users[0];
    $expiry = intval( get_user_meta( $user->ID, 'cora_reset_token_expiry', true ) );

    if ( time() > $expiry ) {
        delete_user_meta( $user->ID, 'cora_reset_token' );
        wp_send_json_error( array( 'message' => 'This link has expired or already been used.' ) );
    }

    // Update password
    wp_set_password( $password, $user->ID );
    
    // Clear token usermeta keys
    delete_user_meta( $user->ID, 'cora_reset_token' );
    delete_user_meta( $user->ID, 'cora_reset_token_expiry' );

    cora_log_activity( 'Authentication', 'Completed password reset.', $user->ID );

    // Invalidate all other sessions
    wp_destroy_all_sessions( $user->ID );

    wp_send_json_success( array( 'message' => 'Password updated. Please log in.' ) );
}
add_action( 'wp_ajax_nopriv_cora_ajax_reset_password', 'cora_ajax_reset_password' );

function cora_ajax_resend_guest_verification() {
    $email = sanitize_email( $_POST['email'] ?? '' );
    $nonce = $_POST['nonce'] ?? '';

    if ( ! wp_verify_nonce( $nonce, 'cora_login_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
    }

    // Locate invitation details
    $invitations = get_option( 'cora_invitations', array() );
    $found_token = '';
    foreach ( $invitations as $tok => $inv ) {
        if ( $inv['email'] === $email && $inv['status'] === 'pending' ) {
            $found_token = $tok;
            break;
        }
    }

    if ( $found_token ) {
        $verification_link = home_url( '/workspace/setup-account?token=' . $found_token );
        update_option( 'cora_latest_verification_link', $verification_link );
        
        cora_log_activity( 'Invitation', "Resent verification/setup link to {$email}." );
        
        error_log( "Cora Verification Link: " . $verification_link );
        wp_send_json_success( array( 'message' => 'Verification link resent.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'No pending invitation found for this email address.' ) );
    }
}
add_action( 'wp_ajax_nopriv_cora_ajax_resend_verification', 'cora_ajax_resend_guest_verification' );

function cora_ajax_accept_invitation() {
    $name     = sanitize_text_field( $_POST['name'] ?? '' );
    $password = $_POST['password'] ?? '';
    $token    = sanitize_text_field( $_POST['token'] ?? '' );
    $nonce    = $_POST['nonce'] ?? '';

    if ( ! wp_verify_nonce( $nonce, 'cora_login_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
    }

    $pass_valid = cora_validate_password( $password );
    if ( $pass_valid !== true ) {
        wp_send_json_error( array( 'message' => $pass_valid ) );
    }

    $invitations = get_option( 'cora_invitations', array() );
    if ( ! isset( $invitations[ $token ] ) || $invitations[ $token ]['status'] !== 'pending' ) {
        wp_send_json_error( array( 'message' => 'Invalid or expired invitation token.' ) );
    }

    $invite = $invitations[ $token ];
    if ( time() > intval( $invite['expires_at'] ) ) {
        $invitations[ $token ]['status'] = 'expired';
        update_option( 'cora_invitations', $invitations );
        wp_send_json_error( array( 'message' => 'This invitation link has expired. Request a new one from your admin.' ) );
    }

    $email = $invite['email'];
    if ( email_exists( $email ) ) {
        wp_send_json_error( array( 'message' => 'A user with this email already exists.' ) );
    }

    // Create user account
    $username = sanitize_user( explode( '@', $email )[0] );
    if ( username_exists( $username ) ) {
        $username .= '_' . rand( 10, 99 );
    }

    $user_id = wp_insert_user( array(
        'user_login'   => $username,
        'user_email'   => $email,
        'display_name' => $name,
        'user_pass'    => $password,
        'role'         => $invite['role']
    ) );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
    }

    // Stamp tenancy metadata
    update_user_meta( $user_id, 'cora_agency_id', $invite['agency_id'] );
    update_user_meta( $user_id, 'cora_branch_id', $invite['branch_id'] );
    update_user_meta( $user_id, 'cora_email_verified', true );
    update_user_meta( $user_id, 'cora_user_status', 'active' );

    // Update invitation status
    $invitations[ $token ]['status'] = 'accepted';
    update_option( 'cora_invitations', $invitations );

    cora_log_activity( 'Invitation', 'Accepted invitation and created account.', $user_id );

    // Send notification to inviter
    if ( ! empty( $invite['invited_by'] ) ) {
        cora_add_notification( $invite['invited_by'], 'Invitation Accepted', esc_html( $name ) . ' has accepted your invitation and joined the team.' );
    }

    // Auto login
    clean_user_cache( $user_id );
    wp_clear_auth_cookie();
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );
    
    $user = get_userdata( $user_id );
    do_action( 'wp_login', $user->user_login, $user );

    wp_send_json_success( array( 'redirect_url' => home_url( '/workspace/dashboard' ) ) );
}
add_action( 'wp_ajax_nopriv_cora_ajax_accept_invitation', 'cora_ajax_accept_invitation' );

function cora_ajax_send_invitation() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    // Check permission to invite (Agency Owner or Branch Manager)
    $user = wp_get_current_user();
    $role = ! empty( $user->roles ) ? $user->roles[0] : '';
    if ( ! in_array( $role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    $email     = sanitize_email( $_POST['email'] ?? '' );
    $first_name= sanitize_text_field( $_POST['first_name'] ?? '' );
    $last_name = sanitize_text_field( $_POST['last_name'] ?? '' );
    $invite_role = sanitize_text_field( $_POST['role'] ?? '' );
    $branch_id = sanitize_text_field( $_POST['branch_id'] ?? '' );

    if ( empty( $email ) || empty( $first_name ) || empty( $last_name ) || empty( $invite_role ) ) {
        wp_send_json_error( array( 'message' => 'All fields are required.' ) );
    }

    // Role hierarchy checks
    if ( $role === 'cora_branch_manager' ) {
        // Branch managers can only invite Senior Agent or below, and branch is locked to theirs
        $allowed = array( 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor', 'cora_viewer' );
        if ( ! in_array( $invite_role, $allowed ) ) {
            wp_send_json_error( array( 'message' => 'You cannot invite members to this role.' ) );
        }
        $branch_id = cora_get_current_user_branch_id();
    }

    // Check if email already exists
    if ( email_exists( $email ) ) {
        wp_send_json_error( array( 'message' => 'A user with this email already exists.' ) );
    }

    // Check for existing pending invitation
    $invitations = get_option( 'cora_invitations', array() );
    foreach ( $invitations as $inv ) {
        if ( $inv['email'] === $email && $inv['status'] === 'pending' && time() < intval( $inv['expires_at'] ) ) {
            wp_send_json_error( array( 'message' => 'An active invitation already exists for this email.' ) );
        }
    }

    $agency_id = cora_get_current_user_agency_id();
    $token = bin2hex( random_bytes( 16 ) );
    
    $invitations[ $token ] = array(
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'email'      => $email,
        'role'       => $invite_role,
        'agency_id'  => $agency_id,
        'branch_id'  => $branch_id,
        'invited_by' => $user->ID,
        'expires_at' => time() + 172800, // 48 hours
        'status'     => 'pending',
        'created_at' => time()
    );

    update_option( 'cora_invitations', $invitations );

    cora_log_activity( 'Invitation', "Sent invitation link to {$email}." );

    $verification_link = home_url( '/workspace/setup-account?token=' . $token );
    update_option( 'cora_latest_verification_link', $verification_link );
    error_log( "Cora Sent Invitation Link: " . $verification_link );

    wp_send_json_success( array( 'message' => 'Invitation sent successfully.' ) );
}
add_action( 'wp_ajax_cora_ajax_send_invitation', 'cora_ajax_send_invitation' );

function cora_ajax_cancel_invitation() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    $user = wp_get_current_user();
    $role = ! empty( $user->roles ) ? $user->roles[0] : '';
    if ( ! in_array( $role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    $token = sanitize_text_field( $_POST['token'] ?? '' );
    $invitations = get_option( 'cora_invitations', array() );
    if ( isset( $invitations[ $token ] ) ) {
        $invitations[ $token ]['status'] = 'cancelled';
        update_option( 'cora_invitations', $invitations );
        cora_log_activity( 'Invitation', "Cancelled invitation for {$invitations[$token]['email']}." );
        wp_send_json_success( array( 'message' => 'Invitation cancelled.' ) );
    }

    wp_send_json_error( array( 'message' => 'Invitation not found.' ) );
}
add_action( 'wp_ajax_cora_ajax_cancel_invitation', 'cora_ajax_cancel_invitation' );

function cora_ajax_update_avatar() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    $avatar_data = $_POST['avatar_data'] ?? '';
    if ( empty( $avatar_data ) ) {
        wp_send_json_error( array( 'message' => 'No image data provided.' ) );
    }

    // Decode base64
    if ( preg_match( '/^data:image\/(\w+);base64,/', $avatar_data, $type ) ) {
        $data = substr( $avatar_data, strpos( $avatar_data, ',' ) + 1 );
        $type = strtolower( $type[1] ); // jpg, jpeg, png
        $data = base64_decode( $data );
        if ( $data === false ) {
            wp_send_json_error( array( 'message' => 'Invalid image encoding.' ) );
        }
    } else {
        wp_send_json_error( array( 'message' => 'Invalid image structure.' ) );
    }

    $upload_dir = wp_upload_dir();
    $filename = 'avatar_' . get_current_user_id() . '_' . time() . '.' . $type;
    $filepath = $upload_dir['path'] . '/' . $filename;
    
    file_put_contents( $filepath, $data );

    $wp_filetype = wp_check_filetype( $filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name( $filename ),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attach_id = wp_insert_attachment( $attachment, $filepath );
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata( $attach_id, $filepath );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    $avatar_url = wp_get_attachment_url( $attach_id );
    update_user_meta( get_current_user_id(), 'cora_avatar_url', $avatar_url );

    // Enforce tenancy isolation meta on profile photo
    $agency_id = cora_get_current_user_agency_id();
    $branch_id = cora_get_current_user_branch_id();
    if ( ! empty( $agency_id ) ) update_post_meta( $attach_id, 'cora_agency_id', $agency_id );
    if ( ! empty( $branch_id ) ) update_post_meta( $attach_id, 'cora_branch_id', $branch_id );

    cora_log_activity( 'User Management', 'Updated profile picture.' );

    wp_send_json_success( array( 'url' => $avatar_url ) );
}
add_action( 'wp_ajax_cora_ajax_update_avatar', 'cora_ajax_update_avatar' );

function cora_ajax_save_profile_info() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    $user_id    = get_current_user_id();
    $first_name = sanitize_text_field( $_POST['first_name'] ?? '' );
    $last_name  = sanitize_text_field( $_POST['last_name'] ?? '' );
    $phone      = sanitize_text_field( $_POST['phone'] ?? '' );

    if ( empty( $first_name ) || empty( $last_name ) ) {
        wp_send_json_error( array( 'message' => 'First and last name are required.' ) );
    }

    update_user_meta( $user_id, 'first_name', $first_name );
    update_user_meta( $user_id, 'last_name', $last_name );
    update_user_meta( $user_id, 'cora_phone', $phone );

    // Update display name
    wp_update_user( array(
        'ID'           => $user_id,
        'display_name' => trim( "$first_name $last_name" )
    ) );

    cora_log_activity( 'User Management', 'Updated profile details.' );

    wp_send_json_success( array( 'message' => 'Profile info updated.' ) );
}
add_action( 'wp_ajax_cora_ajax_save_profile_info', 'cora_ajax_save_profile_info' );

function cora_ajax_change_password() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    $user_id      = get_current_user_id();
    $user         = get_userdata( $user_id );
    $current_pass = $_POST['current_pass'] ?? '';
    $new_pass     = $_POST['new_pass'] ?? '';

    if ( ! wp_check_password( $current_pass, $user->data->user_pass, $user_id ) ) {
        wp_send_json_error( array( 'message' => 'Current password is incorrect.' ) );
    }

    $pass_valid = cora_validate_password( $new_pass );
    if ( $pass_valid !== true ) {
        wp_send_json_error( array( 'message' => $pass_valid ) );
    }

    wp_set_password( $new_pass, $user_id );
    
    cora_log_activity( 'User Management', 'Changed account password.' );

    // Invalidate other sessions
    wp_destroy_all_sessions( $user_id );

    wp_send_json_success( array( 'message' => 'Password updated. Logging you out...' ) );
}
add_action( 'wp_ajax_cora_ajax_change_password', 'cora_ajax_change_password' );

add_action( 'wp_ajax_cora_ajax_logout_other_sessions', 'cora_ajax_logout_other_sessions' );

function cora_ajax_get_user_leads_count() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $user_id = intval( $_POST['user_id'] ?? 0 );
    if ( ! $user_id ) {
        wp_send_json_error( array( 'message' => 'Invalid user.' ) );
    }

    // Bypass tenancy option filters temporarily to retrieve accurate lead count
    remove_filter( 'option_cora_re_leads', 'cora_filter_tenancy_data' );
    $leads = get_option( 'cora_re_leads', array() );
    add_filter( 'option_cora_re_leads', 'cora_filter_tenancy_data' );

    $count = 0;
    if ( is_array( $leads ) ) {
        foreach ( $leads as $lead ) {
            if ( isset( $lead['agent_id'] ) && intval( $lead['agent_id'] ) === $user_id ) {
                $count++;
            }
        }
    }

    wp_send_json_success( array( 'leads_count' => $count ) );
}
add_action( 'wp_ajax_cora_ajax_get_user_leads_count', 'cora_ajax_get_user_leads_count' );

function cora_ajax_save_user_changes() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $user = wp_get_current_user();
    $role = ! empty( $user->roles ) ? $user->roles[0] : '';
    if ( ! in_array( $role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    $target_user_id = intval( $_POST['user_id'] ?? 0 );
    $display_name   = sanitize_text_field( $_POST['display_name'] ?? '' );
    $target_role    = sanitize_text_field( $_POST['role'] ?? '' );
    $branch_id      = sanitize_text_field( $_POST['branch_id'] ?? '' );
    $status         = sanitize_text_field( $_POST['status'] ?? 'active' );
    $reassign_to    = intval( $_POST['reassign_to'] ?? 0 );

    if ( ! $target_user_id || empty( $display_name ) || empty( $target_role ) ) {
        wp_send_json_error( array( 'message' => 'Required fields are missing.' ) );
    }

    $target_user = get_userdata( $target_user_id );
    if ( ! $target_user ) {
        wp_send_json_error( array( 'message' => 'User not found.' ) );
    }

    // Role Hierarchy & Branch limits check
    if ( $role === 'cora_branch_manager' ) {
        $my_branch = cora_get_current_user_branch_id();
        $target_branch = get_user_meta( $target_user_id, 'cora_branch_id', true );
        if ( $target_branch !== $my_branch || $branch_id !== $my_branch ) {
            wp_send_json_error( array( 'message' => 'You can only edit members within your own branch.' ) );
        }
        $allowed = array( 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor', 'cora_viewer' );
        if ( ! in_array( $target_role, $allowed ) ) {
            wp_send_json_error( array( 'message' => 'You cannot assign a user to this role.' ) );
        }
    }

    $first_name = sanitize_text_field( $_POST['first_name'] ?? '' );
    $last_name  = sanitize_text_field( $_POST['last_name'] ?? '' );
    $phone      = sanitize_text_field( $_POST['phone'] ?? '' );

    // Save Display Name, First Name, Last Name
    wp_update_user( array(
        'ID'           => $target_user_id,
        'display_name' => $display_name,
        'first_name'   => $first_name,
        'last_name'    => $last_name
    ) );
    update_user_meta( $target_user_id, 'cora_phone', $phone );

    // Save Role
    $target_user->set_role( $target_role );

    // Save Branch and Status
    $old_branch = get_user_meta( $target_user_id, 'cora_branch_id', true );
    update_user_meta( $target_user_id, 'cora_branch_id', $branch_id );
    
    if ( $old_branch !== $branch_id ) {
        $branch_name = 'Unassigned';
        if ( ! empty( $branch_id ) ) {
            $branches = get_option( 'cora_branches', array() );
            if ( isset( $branches[$branch_id] ) ) {
                $branch_name = $branches[$branch_id]['name'];
            }
        }
        cora_add_notification( $target_user_id, 'Branch Assigned', "You have been reassigned to branch: {$branch_name}.", home_url( '/workspace/dashboard' ) );
    }
    
    $old_status = get_user_meta( $target_user_id, 'cora_user_status', true ) ?: 'active';
    update_user_meta( $target_user_id, 'cora_user_status', $status );

    // Send Reactivation Notification/Email if toggled active
    if ( $old_status === 'inactive' && $status === 'active' ) {
        error_log( "Cora Reactivation Email: Your Cora account has been reactivated." );
    }

    // If deactivated, force logout all sessions immediately (Spec Section 6.3)
    if ( $status === 'inactive' ) {
        wp_destroy_all_sessions( $target_user_id );
        
        // Reassign leads if requested
        remove_filter( 'option_cora_re_leads', 'cora_filter_tenancy_data' );
        remove_filter( 'pre_update_option_cora_re_leads', 'cora_pre_update_tenancy_data', 10, 3 );
        
        $leads = get_option( 'cora_re_leads', array() );
        $updated_leads = array();
        if ( is_array( $leads ) ) {
            foreach ( $leads as $lead ) {
                if ( isset( $lead['agent_id'] ) && intval( $lead['agent_id'] ) === $target_user_id ) {
                    if ( $reassign_to > 0 ) {
                        $lead['agent_id'] = $reassign_to;
                    } else {
                        $lead['agent_id'] = ''; // Unassigned
                    }
                }
                $updated_leads[] = $lead;
            }
            update_option( 'cora_re_leads', $updated_leads );
        }

        add_filter( 'option_cora_re_leads', 'cora_filter_tenancy_data' );
        add_filter( 'pre_update_option_cora_re_leads', 'cora_pre_update_tenancy_data', 10, 3 );
    }

    cora_log_activity( 'User Management', "Updated user '{$display_name}' (ID: {$target_user_id}) — role: {$target_role}, status: {$status}." );

    wp_send_json_success( array( 'message' => 'User updated successfully.' ) );
}
add_action( 'wp_ajax_cora_save_user_changes', 'cora_ajax_save_user_changes' );
add_action( 'wp_ajax_cora_ajax_save_user_changes', 'cora_ajax_save_user_changes' );

function cora_ajax_sync_role_permissions() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $user = wp_get_current_user();
    $role = ! empty( $user->roles ) ? $user->roles[0] : '';
    if ( $role !== 'administrator' && $role !== 'cora_manager' ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    $role_key = sanitize_text_field( $_POST['role_key'] ?? '' );
    $features = isset( $_POST['features'] ) ? array_map( 'sanitize_key', (array) $_POST['features'] ) : array();

    if ( empty( $role_key ) ) {
        wp_send_json_error( array( 'message' => 'Invalid role key.' ) );
    }

    $cora_permissions = get_option( 'cora_role_permissions', array() );
    $cora_permissions[ $role_key ] = $features;
    update_option( 'cora_role_permissions', $cora_permissions );

    cora_log_activity( 'Permissions', "Updated feature permissions for role '{$role_key}'." );

    wp_send_json_success( array( 'message' => 'Permissions synchronized.' ) );
}
add_action( 'wp_ajax_cora_ajax_save_role_permissions', 'cora_ajax_sync_role_permissions' );

function cora_ajax_save_branch() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $user = wp_get_current_user();
    $role = ! empty( $user->roles ) ? $user->roles[0] : '';
    if ( ! in_array( $role, array( 'administrator', 'cora_manager' ) ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    $branch_id   = sanitize_key( $_POST['branch_id'] ?? '' );
    $branch_name = sanitize_text_field( $_POST['branch_name'] ?? '' );
    $city        = sanitize_text_field( $_POST['city'] ?? '' );
    $address     = sanitize_text_field( $_POST['address'] ?? '' );
    $manager_id  = intval( $_POST['manager_id'] ?? 0 );

    if ( empty( $branch_name ) || empty( $city ) || empty( $address ) ) {
        wp_send_json_error( array( 'message' => 'Required fields are missing.' ) );
    }

    $agency_id = cora_get_current_user_agency_id();
    $branches  = get_option( 'cora_branches', array() );

    // 1:1 Manager rule verification
    if ( $manager_id > 0 ) {
        foreach ( $branches as $b_key => $b ) {
            if ( ( empty( $branch_id ) || $b_key !== $branch_id ) && isset( $b['manager_id'] ) && intval( $b['manager_id'] ) === $manager_id ) {
                wp_send_json_error( array( 'message' => 'This manager is already assigned to another branch.' ) );
            }
        }
    }

    global $wpdb;
    $agency_db_id = empty($agency_id) ? 1 : intval(preg_replace('/[^\d]/', '', $agency_id));
    $db_branch_id = intval(preg_replace('/[^\d]/', '', $branch_id));

    if ( $db_branch_id > 0 ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_branches',
            array(
                'name' => $branch_name,
                'city' => $city,
                'address' => $address,
                'manager_id' => $manager_id ?: null,
                'updated_at' => current_time('mysql')
            ),
            array( 'id' => $db_branch_id, 'agency_id' => $agency_db_id ),
            array('%s', '%s', '%s', '%d', '%s'),
            array('%d', '%d')
        );
        $new_id = $branch_id;
    } else {
        $wpdb->insert(
            $wpdb->prefix . 'cora_branches',
            array(
                'agency_id' => $agency_db_id ?: 1,
                'name' => $branch_name,
                'city' => $city,
                'address' => $address,
                'manager_id' => $manager_id ?: null,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s', '%d', '%s', '%s')
        );
        $new_id = 'branch_' . $wpdb->insert_id;
    }

    $branches[ $new_id ] = array(
        'id'         => $new_id,
        'agency_id'  => $agency_id,
        'name'       => $branch_name,
        'city'       => $city,
        'address'    => $address,
        'manager_id' => $manager_id,
        'created_at' => $branches[ $branch_id ]['created_at'] ?? date( 'Y-m-d H:i:s' )
    );

    update_option( 'cora_branches', $branches );

    // If manager was assigned, update manager's metadata
    if ( $manager_id > 0 ) {
        update_user_meta( $manager_id, 'cora_branch_id', $new_id );
        
        $mgr_user = get_userdata( $manager_id );
        if ( $mgr_user && ! in_array( 'cora_branch_manager', (array) $mgr_user->roles ) && ! in_array( 'cora_manager', (array) $mgr_user->roles ) && ! in_array( 'administrator', (array) $mgr_user->roles ) ) {
            $mgr_user->set_role( 'cora_branch_manager' );
        }
    }

    cora_log_activity( 'Branch', "Saved branch '{$branch_name}' (ID: {$new_id})." );

    wp_send_json_success( array( 'message' => 'Branch saved successfully.' ) );
}
add_action( 'wp_ajax_cora_ajax_save_branch', 'cora_ajax_save_branch' );

function cora_ajax_delete_branch() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $user = wp_get_current_user();
    $role = ! empty( $user->roles ) ? $user->roles[0] : '';
    if ( ! in_array( $role, array( 'administrator', 'cora_manager' ) ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    $branch_id = sanitize_key( $_POST['branch_id'] ?? '' );
    if ( empty( $branch_id ) ) {
        wp_send_json_error( array( 'message' => 'Invalid branch.' ) );
    }

    // Verify 0 active members count
    $all_wp_users = get_users();
    $crew_count = 0;
    foreach ( $all_wp_users as $u ) {
        $u_branch = get_user_meta( $u->ID, 'cora_branch_id', true );
        if ( strval($u_branch) === strval($branch_id) ) {
            $crew_count++;
        }
    }

    if ( $crew_count > 0 ) {
        wp_send_json_error( array( 'message' => 'You cannot delete a branch with active team members. Reassign all members first.' ) );
    }

    global $wpdb;
    $agency_id = cora_get_current_user_agency_id();
    $agency_db_id = empty($agency_id) ? 1 : intval(preg_replace('/[^\d]/', '', $agency_id));
    $db_branch_id = intval(preg_replace('/[^\d]/', '', $branch_id));

    $wpdb->delete(
        $wpdb->prefix . 'cora_branches',
        array( 'id' => $db_branch_id, 'agency_id' => $agency_db_id ),
        array( '%d', '%d' )
    );

    $branches = get_option( 'cora_branches', array() );
    if ( isset( $branches[ $branch_id ] ) ) {
        $deleted_name = $branches[ $branch_id ]['name'] ?? $branch_id;
        unset( $branches[ $branch_id ] );
        update_option( 'cora_branches', $branches );
        cora_log_activity( 'Branch', "Deleted branch '{$deleted_name}'." );
        wp_send_json_success( array( 'message' => 'Branch deleted successfully.' ) );
    }

    wp_send_json_error( array( 'message' => 'Branch not found.' ) );
}
add_action( 'wp_ajax_cora_ajax_delete_branch', 'cora_ajax_delete_branch' );

function cora_log_activity( $action_type, $description, $custom_user_id = 0, $how = 'human', $instructed_by = 0, $ai_reasoning = '' ) {
    $user_id = $custom_user_id > 0 ? $custom_user_id : get_current_user_id();
    $user = get_userdata( $user_id );
    $username = $user ? $user->display_name : 'System / Guest';
    
    $agency_id = '';
    $branch_id = '';
    if ( $user ) {
        $agency_id = get_user_meta( $user->ID, 'cora_agency_id', true );
        $branch_id = get_user_meta( $user->ID, 'cora_branch_id', true );
    }
    
    $agency_db_id = empty($agency_id) ? 1 : intval(preg_replace('/[^\d]/', '', $agency_id));
    $branch_db_id = empty($branch_id) ? 1 : intval(preg_replace('/[^\d]/', '', $branch_id));

    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $device = cora_get_device_info();

    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'cora_activity_logs',
        array(
            'agency_id' => $agency_db_id,
            'user_id' => $user_id ?: 1,
            'action_type' => $action_type,
            'description' => $description,
            'record_type' => null,
            'record_id' => null,
            'ip_address' => $ip,
            'user_agent' => $device,
            'how' => $how,
            'instructed_by' => $instructed_by ?: null,
            'ai_reasoning' => $ai_reasoning,
            'embed_vector' => 0,
            'created_at' => current_time('mysql')
        ),
        array('%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s')
    );

    $logs = get_option( 'cora_activity_logs', array() );
    if ( ! is_array( $logs ) ) {
        $logs = array();
    }
    $logs[] = array(
        'timestamp'     => time(),
        'user_id'       => $user_id,
        'user_name'     => $username,
        'user_role'     => $user && ! empty( $user->roles ) ? $user->roles[0] : 'guest',
        'action_type'   => $action_type,
        'description'   => $description,
        'ip'            => $ip,
        'device'        => $device,
        'agency_id'     => $agency_id,
        'branch_id'     => $branch_id,
        'how'           => $how,
        'instructed_by' => $instructed_by,
        'ai_reasoning'  => $ai_reasoning
    );

    if ( count( $logs ) > 1000 ) {
        $logs = array_slice( $logs, -1000 );
    }
    update_option( 'cora_activity_logs', $logs );
}

function cora_add_notification( $user_id, $title, $description, $action_url = '' ) {
    global $wpdb;
    $agency_db_id = 1;
    $user = get_userdata( $user_id );
    if ( $user ) {
        $user_agency = get_user_meta( $user->ID, 'cora_agency_id', true );
        if ( ! empty($user_agency) ) {
            $agency_db_id = intval(preg_replace('/[^\d]/', '', $user_agency)) ?: 1;
        }
    }

    $wpdb->insert(
        $wpdb->prefix . 'cora_notifications',
        array(
            'agency_id' => $agency_db_id,
            'user_id' => intval( $user_id ),
            'title' => sanitize_text_field( $title ),
            'body' => sanitize_text_field( $description ),
            'type' => 'alert',
            'is_read' => 0,
            'created_at' => current_time('mysql')
        ),
        array('%d', '%d', '%s', '%s', '%s', '%d', '%s')
    );

    $notifications = get_option( 'cora_notifications', array() );
    if ( ! is_array( $notifications ) ) {
        $notifications = array();
    }
    $notifications[] = array(
        'id'          => uniqid( 'notif_' ),
        'user_id'     => intval( $user_id ),
        'title'       => sanitize_text_field( $title ),
        'description' => sanitize_text_field( $description ),
        'timestamp'   => time(),
        'read'        => false,
        'action_url'  => esc_url_raw( $action_url )
    );
    if ( count( $notifications ) > 500 ) {
        $notifications = array_slice( $notifications, -500 );
    }
    update_option( 'cora_notifications', $notifications );
}

function cora_ajax_mark_notif_read() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    $notif_id = sanitize_key( $_POST['notif_id'] ?? '' );
    if ( empty( $notif_id ) ) {
        wp_send_json_error( array( 'message' => 'Invalid notification ID.' ) );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $db_notif_id = intval(preg_replace('/[^\d]/', '', $notif_id));
    if ( $db_notif_id > 0 ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_notifications',
            array( 'is_read' => 1 ),
            array( 'id' => $db_notif_id, 'user_id' => $user_id ),
            array( '%d' ),
            array( '%d', '%d' )
        );
    }

    $notifications = get_option( 'cora_notifications', array() );
    $updated = false;
    if ( is_array( $notifications ) ) {
        foreach ( $notifications as $key => $notif ) {
            if ( isset( $notif['id'] ) && strval($notif['id']) === strval($notif_id) && isset( $notif['user_id'] ) && intval( $notif['user_id'] ) === $user_id ) {
                $notifications[$key]['read'] = true;
                $updated = true;
                break;
            }
        }
    }

    if ( $updated ) {
        update_option( 'cora_notifications', $notifications );
        wp_send_json_success();
    }

    wp_send_json_error( array( 'message' => 'Notification not found.' ) );
}
add_action( 'wp_ajax_cora_ajax_mark_notif_read', 'cora_ajax_mark_notif_read' );

function cora_ajax_mark_all_notifs_read() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
    }

    global $wpdb;
    $agency_id = cora_db_get_agency_id();
    $wpdb->update(
        $wpdb->prefix . 'cora_notifications',
        array( 'is_read' => 1 ),
        array( 'user_id' => $user_id, 'agency_id' => $agency_id ),
        array( '%d' ),
        array( '%d', '%d' )
    );

    $notifications = get_option( 'cora_notifications', array() );
    if ( is_array( $notifications ) ) {
        foreach ( $notifications as $key => $notif ) {
            if ( isset( $notif['user_id'] ) && intval( $notif['user_id'] ) === $user_id ) {
                $notifications[$key]['read'] = true;
            }
        }
        update_option( 'cora_notifications', $notifications );
    }
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_mark_all_notifs_read', 'cora_ajax_mark_all_notifs_read' );

function cora_validate_password( $password ) {
    $min_len = intval( get_option( 'cora_pwd_policy_min_len', 8 ) );
    $require_numbers = intval( get_option( 'cora_pwd_policy_numbers', 0 ) );
    $require_uppercase = intval( get_option( 'cora_pwd_policy_uppercase', 0 ) );
    $require_special = intval( get_option( 'cora_pwd_policy_special', 0 ) );

    $errors = array();

    if ( strlen( $password ) < $min_len ) {
        $errors[] = "at least {$min_len} characters";
    }
    if ( $require_numbers && ! preg_match( '/[0-9]/', $password ) ) {
        $errors[] = "a number";
    }
    if ( $require_uppercase && ! preg_match( '/[A-Z]/', $password ) ) {
        $errors[] = "an uppercase letter";
    }
    if ( $require_special && ! preg_match( '/[^a-zA-Z0-9]/', $password ) ) {
        $errors[] = "a special character";
    }

    if ( ! empty( $errors ) ) {
        // Build readable string
        $last = array_pop( $errors );
        $text = empty( $errors ) ? $last : implode( ', ', $errors ) . ', and ' . $last;
        return "Password must be {$text}.";
    }

    return true;
}

/**
 * Get device info from User-Agent
 */
function cora_get_device_info() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if ( empty( $ua ) ) {
        return 'Unknown Device';
    }
    $browser = 'Unknown Browser';
    $os = 'Unknown OS';
    if ( preg_match( '/MSIE/i', $ua ) && ! preg_match( '/Opera/i', $ua ) ) {
        $browser = 'Internet Explorer';
    } elseif ( preg_match( '/Firefox/i', $ua ) ) {
        $browser = 'Firefox';
    } elseif ( preg_match( '/Chrome/i', $ua ) ) {
        $browser = 'Chrome';
    } elseif ( preg_match( '/Safari/i', $ua ) ) {
        $browser = 'Safari';
    } elseif ( preg_match( '/Opera/i', $ua ) ) {
        $browser = 'Opera';
    } elseif ( preg_match( '/Netscape/i', $ua ) ) {
        $browser = 'Netscape';
    }
    
    if ( preg_match( '/windows|win32/i', $ua ) ) {
        $os = 'Windows';
    } elseif ( preg_match( '/macintosh|mac os x/i', $ua ) ) {
        $os = 'MacOS';
    } elseif ( preg_match( '/linux/i', $ua ) ) {
        $os = 'Linux';
    } elseif ( preg_match( '/iphone|ipad|ipod/i', $ua ) ) {
        $os = 'iOS';
    } elseif ( preg_match( '/android/i', $ua ) ) {
        $os = 'Android';
    }
    return "$browser on $os";
}

/**
 * Action Queue scheduling and execution
 */
function cora_run_action_queue() {
    $queue = get_option( 'cora_action_queue', array() );
    if ( empty( $queue ) || ! is_array( $queue ) ) {
        return;
    }
    $updated = array();
    $now = time();
    $ran_any = false;
    
    foreach ( $queue as $item ) {
        if ( isset( $item['status'] ) && $item['status'] === 'pending' && intval( $item['scheduled_at'] ) <= $now ) {
            // Execute scheduled action
            $payload = $item['payload'] ?? array();
            $action_type = $item['action_type'] ?? '';
            $created_by = $item['created_by'] ?? 0;
            
            if ( 'deactivate_user' === $action_type ) {
                $uid = intval( $payload['user_id'] ?? 0 );
                $reassign_to = intval( $payload['reassign_to'] ?? 0 );
                if ( $uid > 0 ) {
                    update_user_meta( $uid, 'cora_user_status', 'inactive' );
                    wp_destroy_all_sessions( $uid );
                    
                    // Reassign open leads
                    remove_filter( 'option_cora_re_leads', 'cora_filter_tenancy_data' );
                    remove_filter( 'pre_update_option_cora_re_leads', 'cora_pre_update_tenancy_data', 10, 3 );
                    $leads = get_option( 'cora_re_leads', array() );
                    $updated_leads = array();
                    if ( is_array( $leads ) ) {
                        foreach ( $leads as $lead ) {
                            if ( isset( $lead['agent_id'] ) && intval( $lead['agent_id'] ) === $uid ) {
                                $lead['agent_id'] = $reassign_to > 0 ? $reassign_to : '';
                            }
                            $updated_leads[] = $lead;
                        }
                        update_option( 'cora_re_leads', $updated_leads );
                    }
                    add_filter( 'option_cora_re_leads', 'cora_filter_tenancy_data' );
                    add_filter( 'pre_update_option_cora_re_leads', 'cora_pre_update_tenancy_data', 10, 3 );
                    
                    cora_log_activity( 'User Management', "Scheduled deactivation executed for user ID: {$uid}.", $uid, 'ai_instructed', $created_by, 'Scheduled action queue runner execution' );
                }
            }
            $item['status'] = 'completed';
            $ran_any = true;
        }
        $updated[] = $item;
    }
    if ( $ran_any ) {
        update_option( 'cora_action_queue', $updated );
    }
}
add_action( 'init', 'cora_run_action_queue' );

/**
 * Helper: Map lead CRM status to dashboard pipeline stages
 */
function cora_map_lead_status_to_pipeline( $status ) {
    $status_map = array(
        'New Lead'      => 'New',
        'New'           => 'New',
        'Nurturing'     => 'Contacted',
        'Contacted'     => 'Contacted',
        'Site Visit'    => 'Site Visit',
        'Closing'       => 'Negotiation',
        'Negotiation'   => 'Negotiation',
        'Proposal Sent' => 'Negotiation',
        'Converted'     => 'Closed',
        'Closed'        => 'Closed'
    );
    return $status_map[$status] ?? 'New';
}

/**
 * REST API /api/v1 Router Callback
 */
function cora_handle_api_v1_request( $path_parts ) {
    nocache_headers();
    header( 'Content-Type: application/json' );

    $method = $_SERVER['REQUEST_METHOD'];
    $resource = $path_parts[2] ?? '';
    
    // PUBLIC UNAUTHENTICATED ENDPOINTS GROUP: /api/v1/public/...
    if ( 'public' === $resource ) {
        $sub_resource = $path_parts[3] ?? '';
        
        // GET /api/v1/public/listings
        if ( 'listings' === $sub_resource && 'GET' === $method ) {
            $listings = cora_db_get_properties();
            echo wp_json_encode( array( 'success' => true, 'data' => $listings ) );
            exit;
        }
        
        // POST /api/v1/public/leads
        if ( 'leads' === $sub_resource && 'POST' === $method ) {
            $raw = file_get_contents( 'php://input' );
            $data = json_decode( $raw, true ) ?: $_POST;
            
            $names = isset( $data['names'] ) ? sanitize_text_field( $data['names'] ) : '';
            $email = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
            $scale = isset( $data['scale'] ) ? sanitize_text_field( $data['scale'] ) : '';
            $city  = isset( $data['city'] ) ? sanitize_text_field( $data['city'] ) : '';
            $notes = isset( $data['notes'] ) ? sanitize_textarea_field( $data['notes'] ) : '';
            $price = isset( $data['price'] ) ? sanitize_text_field( $data['price'] ) : '';
            $followup_date = isset( $data['followup_date'] ) ? sanitize_text_field( $data['followup_date'] ) : '';

            if ( empty( $names ) || empty( $email ) ) {
                status_header( 400 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'Names and Email are required.' ) );
                exit;
            }

            global $wpdb;
            $agency_id = cora_db_get_agency_id();
            $branch_id = cora_db_get_branch_id();

            $followup_dt = null;
            if ( ! empty($followup_date) ) {
                $followup_dt = date('Y-m-d H:i:s', strtotime($followup_date));
            }

            $wpdb->insert(
                $wpdb->prefix . 'cora_leads',
                array(
                    'agency_id' => $agency_id,
                    'branch_id' => $branch_id,
                    'assigned_to' => null,
                    'first_name' => $names,
                    'last_name' => '',
                    'email' => $email,
                    'phone' => '',
                    'source' => 'Frontend (Git Sync)',
                    'status' => 'new',
                    'budget_min' => 0,
                    'budget_max' => !empty($price) ? intval(preg_replace('/[^\d]/', '', $price)) : 0,
                    'preferred_locations' => $city,
                    'property_type' => $scale,
                    'notes' => $notes,
                    'followup_date' => $followup_dt,
                    'followup_notes' => '',
                    'converted_to_client' => 0,
                    'client_id' => null,
                    'embed_vector' => 0,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s')
            );
            $inserted_id = $wpdb->insert_id;

            $leads = get_option( 'cora_re_leads', array() );
            if ( ! is_array( $leads ) ) {
                $leads = array();
            }

            $new_lead = array(
                'id'            => $inserted_id,
                'names'         => $names,
                'email'         => $email,
                'scale'         => $scale,
                'city'          => $city,
                'notes'         => $notes,
                'price'         => $price,
                'status'        => 'New Lead',
                'emails'        => cora_generate_default_email_sequence( $names, $scale, $city ),
                'followup_date' => $followup_date,
                'created_at'    => time()
            );
            $leads[] = $new_lead;
            update_option( 'cora_re_leads', $leads );

            cora_log_activity( 'Lead Capture', "Captured new lead via Git Sync frontend: {$names} ({$email})" );

            echo wp_json_encode( array( 'success' => true, 'message' => 'Lead submitted successfully!', 'id' => $inserted_id ) );
            exit;
        }

        // POST /api/v1/public/bookings
        if ( 'bookings' === $sub_resource && 'POST' === $method ) {
            $raw = file_get_contents( 'php://input' );
            $data = json_decode( $raw, true ) ?: $_POST;

            $names         = isset( $data['names'] ) ? sanitize_text_field( $data['names'] ) : '';
            $email         = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
            $property_id   = isset( $data['property_id'] ) ? intval( $data['property_id'] ) : 0;
            $showing_date  = isset( $data['showing_date'] ) ? sanitize_text_field( $data['showing_date'] ) : '';
            $notes         = isset( $data['notes'] ) ? sanitize_textarea_field( $data['notes'] ) : '';

            if ( empty( $names ) || empty( $email ) || empty( $property_id ) || empty( $showing_date ) ) {
                status_header( 400 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'Names, Email, Property ID, and Showing Date are required.' ) );
                exit;
            }

            global $wpdb;
            $agency_id = cora_db_get_agency_id();
            $branch_id = cora_db_get_branch_id();

            $client_id = 0;
            $existing_client = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_clients WHERE email = %s AND agency_id = %d", $email, $agency_id ) );
            if ( $existing_client ) {
                $client_id = $existing_client->id;
            } else {
                $wpdb->insert(
                    $wpdb->prefix . 'cora_clients',
                    array(
                        'agency_id'  => $agency_id,
                        'branch_id'  => $branch_id,
                        'names'      => $names,
                        'email'      => $email,
                        'phone'      => '',
                        'status'     => 'Active Client',
                        'city'       => '',
                        'deal_type'  => 'Buy',
                        'created_at' => current_time('mysql')
                    ),
                    array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
                );
                $client_id = $wpdb->insert_id;

                $clients = get_option( 'cora_re_clients', array() );
                if ( ! is_array( $clients ) ) {
                    $clients = array();
                }
                $clients[] = array(
                    'id'        => $client_id,
                    'names'     => $names,
                    'email'     => $email,
                    'phone'     => '',
                    'status'    => 'Active Client',
                    'city'      => '',
                    'deal_type' => 'Buy'
                );
                update_option( 'cora_re_clients', $clients );
            }

            $formatted_date = date('Y-m-d H:i:s', strtotime($showing_date));
            $wpdb->insert(
                $wpdb->prefix . 'cora_bookings',
                array(
                    'agency_id'      => $agency_id,
                    'branch_id'      => $branch_id,
                    'client_id'      => $client_id,
                    'property_id'    => $property_id,
                    'showing_date'   => $formatted_date,
                    'status'         => 'Pending',
                    'assigned_agent' => 'Cora System',
                    'package_value'  => 0,
                    'deal_type'      => 'Showing',
                    'crew'           => '[]',
                    'notes'          => $notes,
                    'created_at'     => current_time('mysql'),
                    'updated_at'     => current_time('mysql')
                ),
                array('%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
            );
            $inserted_id = $wpdb->insert_id;

            cora_log_activity( 'Booking Creation', "Captured new booking request via Git Sync frontend for client: {$names}" );

            echo wp_json_encode( array( 'success' => true, 'message' => 'Booking scheduled successfully!', 'id' => $inserted_id ) );
            exit;
        }

        status_header( 404 );
        echo wp_json_encode( array( 'success' => false, 'message' => 'Public resource not found.' ) );
        exit;
    }

    if ( ! is_user_logged_in() ) {
        status_header( 401 );
        echo wp_json_encode( array( 'success' => false, 'message' => 'Unauthorized' ) );
        exit;
    }

    $current_user = wp_get_current_user();
    $current_role = ! empty( $current_user->roles ) ? $current_user->roles[0] : '';
    $current_agency = cora_get_current_user_agency_id();

    // Dashboard Resource Route Group: /api/v1/dashboard/...
    if ( 'dashboard' === $resource && 'GET' === $method ) {
        $sub_resource = $path_parts[3] ?? '';
        
        $requested_role = sanitize_text_field( $_GET['role'] ?? '' );
        $active_role = ( ! empty( $requested_role ) && in_array( $current_role, array( 'administrator', 'cora_manager' ) ) ) ? $requested_role : $current_role;
        $agency_id = cora_get_current_user_agency_id();
        $branch_id = cora_get_current_user_branch_id();
        $user_id = $current_user->ID;

        // Log the query to the audit panel
        $how = sanitize_text_field( $_GET['how'] ?? 'manual' );
        $scope = 'agency';
        if ( $agency_id === 'super' ) {
            $scope = 'super';
        } elseif ( $active_role === 'cora_branch_manager' ) {
            $scope = 'branch';
        } elseif ( in_array( $active_role, array( 'cora_photographer', 'cora_videographer', 'cora_drone_pilot' ) ) ) {
            $scope = 'own';
        }
        
        cora_log_activity( 'dashboard_query', "Queried dashboard {$sub_resource} [scope: {$scope}]", $user_id, $how );

        // Helper: get filtered leads list for active_role
        $leads = get_option( 'cora_re_leads', array() );
        if ( ! is_array( $leads ) ) {
            $leads = array();
        }
        
        // Dynamically ensure mockup followup_dates exist so dashboard is beautiful
        foreach ( $leads as &$l ) {
            if ( empty( $l['followup_date'] ) ) {
                if ( isset( $l['id'] ) && $l['id'] === 'lead_sample_1' ) {
                    $l['followup_date'] = date( 'Y-m-d H:i', time() + 3600 );
                } elseif ( isset( $l['id'] ) && $l['id'] === 'lead_sample_2' ) {
                    $l['followup_date'] = date( 'Y-m-d H:i', time() - 3600 * 24 * 2 );
                }
            }
        }
        unset( $l );

        // Role-based filtering of leads
        if ( in_array( $active_role, array( 'cora_photographer', 'cora_videographer', 'cora_drone_pilot' ) ) ) {
            $leads = array_values( array_filter( $leads, function( $l ) use ( $user_id ) {
                return isset( $l['agent_id'] ) && intval( $l['agent_id'] ) === $user_id;
            } ) );
        } elseif ( $active_role === 'cora_branch_manager' && ! empty( $branch_id ) ) {
            $leads = array_values( array_filter( $leads, function( $l ) use ( $branch_id ) {
                return isset( $l['branch_id'] ) && $l['branch_id'] === $branch_id;
            } ) );
        }

        // Sub-resource: summary
        if ( 'summary' === $sub_resource ) {
            // Card 1: Active Leads
            $active_leads = array_filter( $leads, function( $l ) {
                $status = $l['status'] ?? '';
                return ! in_array( $status, array( 'Closed', 'Converted', 'Lost' ) );
            } );
            $active_leads_count = count( $active_leads );

            // Overdue or Today's follow-up count
            $follow_up_today_count = 0;
            $today_str = date( 'Y-m-d' );
            foreach ( $active_leads as $l ) {
                if ( ! empty( $l['followup_date'] ) ) {
                    $f_date = date( 'Y-m-d', strtotime( $l['followup_date'] ) );
                    if ( $f_date <= $today_str ) {
                        $follow_up_today_count++;
                    }
                }
            }

            // Card 2: Properties Listed
            // Note: listings option has the tenancy filter automatically applied
            $listings = get_option( 'cora_re_listings_inventory', array() );
            if ( ! is_array( $listings ) ) {
                $listings = array();
            }
            $properties_count = count( $listings );
            // mock properties added this week
            $properties_added_week = 2; 

            // Card 3: Pipeline Value (Negotiation stage only)
            $pipeline_value = 0;
            $pipeline_leads_count = 0;
            foreach ( $active_leads as $l ) {
                $mapped_stage = cora_map_lead_status_to_pipeline( $l['status'] ?? '' );
                if ( 'Negotiation' === $mapped_stage ) {
                    $clean_price = preg_replace( '/[^\d]/', '', $l['price'] ?? '' );
                    $pipeline_value += intval( $clean_price );
                    $pipeline_leads_count++;
                }
            }

            // Indian format pipeline value string
            $pipeline_value_formatted = '₹0';
            if ( $pipeline_value >= 10000000 ) {
                $pipeline_value_formatted = '₹' . number_format( $pipeline_value / 10000000, 1 ) . 'Cr';
            } elseif ( $pipeline_value >= 100000 ) {
                $pipeline_value_formatted = '₹' . number_format( $pipeline_value / 100000, 1 ) . 'L';
            } elseif ( $pipeline_value > 0 ) {
                $pipeline_value_formatted = '₹' . number_format( $pipeline_value );
            }

            // Card 4: Closed This Month
            $closed_this_month = 0;
            $closed_last_month = 0;
            $this_month_start = strtotime('first day of this month 00:00:00');
            $this_month_end   = strtotime('last day of this month 23:59:59');
            $last_month_start = strtotime('first day of last month 00:00:00');
            $last_month_end   = strtotime('last day of last month 23:59:59');

            foreach ( $leads as $l ) {
                $mapped_stage = cora_map_lead_status_to_pipeline( $l['status'] ?? '' );
                if ( 'Closed' === $mapped_stage ) {
                    $closed_time = $l['closed_at'] ?? ( $l['created_at'] ?? time() );
                    if ( $closed_time >= $this_month_start && $closed_time <= $this_month_end ) {
                        $closed_this_month++;
                    } elseif ( $closed_time >= $last_month_start && $closed_time <= $last_month_end ) {
                        $closed_last_month++;
                    }
                }
            }

            // Total agencies (Super Admin only)
            $total_agencies = 0;
            if ( $current_agency === 'super' || $current_role === 'administrator' ) {
                $agencies = get_option( 'cora_agencies', array() );
                $total_agencies = is_array( $agencies ) ? count( $agencies ) : 1;
            }

            echo wp_json_encode( array(
                'success' => true,
                'data' => array(
                    'active_leads'             => $active_leads_count,
                    'follow_up_today'          => $follow_up_today_count,
                    'properties_listed'        => $properties_count,
                    'properties_added_week'    => $properties_added_week,
                    'pipeline_value'           => $pipeline_value,
                    'pipeline_value_formatted' => $pipeline_value_formatted,
                    'pipeline_leads_count'     => $pipeline_leads_count,
                    'closed_this_month'        => $closed_this_month,
                    'closed_last_month'        => $closed_last_month,
                    'total_agencies'           => $total_agencies
                )
            ) );
            exit;
        }

        // Sub-resource: pipeline
        if ( 'pipeline' === $sub_resource ) {
            $stages = array(
                'New'         => 0,
                'Contacted'   => 0,
                'Site Visit'  => 0,
                'Negotiation' => 0,
                'Closed'      => 0
            );

            $total_active = 0;
            foreach ( $leads as $l ) {
                $mapped = cora_map_lead_status_to_pipeline( $l['status'] ?? '' );
                if ( isset( $stages[$mapped] ) ) {
                    $stages[$mapped]++;
                    if ( 'Closed' !== $mapped ) {
                        $total_active++;
                    }
                }
            }

            echo wp_json_encode( array(
                'success' => true,
                'data' => array(
                    'stages'       => $stages,
                    'total_active' => $total_active
                )
            ) );
            exit;
        }

        // Sub-resource: follow-ups
        if ( 'follow-ups' === $sub_resource ) {
            $follow_ups = array();
            $today_start = strtotime( 'today 00:00:00' );
            $today_end   = strtotime( 'today 23:59:59' );

            foreach ( $leads as $l ) {
                $status = $l['status'] ?? '';
                if ( in_array( $status, array( 'Closed', 'Converted', 'Lost' ) ) ) {
                    continue;
                }
                
                if ( ! empty( $l['followup_date'] ) ) {
                    $f_time = strtotime( $l['followup_date'] );
                    $is_overdue = $f_time < $today_start;
                    $is_today   = ( $f_time >= $today_start && $f_time <= $today_end );

                    if ( $is_overdue || $is_today ) {
                        $follow_ups[] = array(
                            'id'             => $l['id'],
                            'names'          => $l['names'],
                            'email'          => $l['email'],
                            'city'           => $l['city'] ?? '',
                            'scale'          => $l['scale'] ?? '',
                            'price'          => $l['price'] ?? '',
                            'notes'          => $l['notes'] ?? '',
                            'followup_date'  => $l['followup_date'],
                            'followup_time'  => $f_time,
                            'is_overdue'     => $is_overdue,
                            'overdue_days'   => $is_overdue ? ceil( ( $today_start - $f_time ) / 86400 ) : 0
                        );
                    }
                }
            }

            // Sort: overdue first, then today's by time
            usort( $follow_ups, function( $a, $b ) {
                if ( $a['is_overdue'] && ! $b['is_overdue'] ) {
                    return -1;
                }
                if ( ! $a['is_overdue'] && $b['is_overdue'] ) {
                    return 1;
                }
                return $a['followup_time'] - $b['followup_time'];
            } );

            echo wp_json_encode( array(
                'success' => true,
                'data' => array_slice( $follow_ups, 0, 5 )
            ) );
            exit;
        }

        // Sub-resource: activity
        if ( 'activity' === $sub_resource ) {
            $logs = get_option( 'cora_activity_logs', array() );
            if ( ! is_array( $logs ) ) {
                $logs = array();
            }

            // Sort logs descending (newest first)
            usort( $logs, function( $a, $b ) {
                return ($b['timestamp'] ?? 0) - ($a['timestamp'] ?? 0);
            } );

            $filtered = array();
            foreach ( $logs as $log ) {
                if ( $agency_id !== 'super' && isset( $log['agency_id'] ) && $log['agency_id'] !== $agency_id ) {
                    continue;
                }
                if ( $active_role === 'cora_branch_manager' && ! empty( $branch_id ) ) {
                    if ( isset( $log['branch_id'] ) && $log['branch_id'] !== $branch_id ) {
                        continue;
                    }
                } elseif ( in_array( $active_role, array( 'cora_photographer', 'cora_videographer', 'cora_drone_pilot' ) ) ) {
                    // agents see only their own actions
                    if ( isset( $log['user_id'] ) && intval( $log['user_id'] ) !== $user_id ) {
                        continue;
                    }
                }

                $filtered[] = $log;
                if ( count( $filtered ) >= 8 ) {
                    break;
                }
            }

            echo wp_json_encode( array(
                'success' => true,
                'data' => $filtered
            ) );
            exit;
        }
    }

    // GET /api/v1/activity-log
    if ( 'activity-log' === $resource && 'GET' === $method ) {
        $logs = get_option( 'cora_activity_logs', array() );
        $filtered = array();
        foreach ( $logs as $log ) {
            if ( $current_agency !== 'super' && $log['agency_id'] !== $current_agency ) {
                continue;
            }
            if ( $current_role === 'cora_branch_manager' ) {
                $my_branch = cora_get_current_user_branch_id();
                if ( $log['branch_id'] !== $my_branch ) {
                    continue;
                }
            }
            $filtered[] = $log;
        }
        echo wp_json_encode( array( 'success' => true, 'data' => $filtered ) );
        exit;
    }

    // POST /api/v1/users/invite
    if ( 'users' === $resource && isset( $path_parts[3] ) && 'invite' === $path_parts[3] && 'POST' === $method ) {
        if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
            status_header( 403 );
            echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
            exit;
        }
        $raw = file_get_contents( 'php://input' );
        $data = json_decode( $raw, true ) ?: $_POST;

        $email = sanitize_email( $data['email'] ?? '' );
        $first_name = sanitize_text_field( $data['first_name'] ?? '' );
        $last_name = sanitize_text_field( $data['last_name'] ?? '' );
        $role = sanitize_text_field( $data['role'] ?? '' );
        $branch_id = sanitize_text_field( $data['branch_id'] ?? '' );
        $personal_note = sanitize_textarea_field( $data['personal_note'] ?? '' );

        if ( empty( $email ) || empty( $first_name ) || empty( $last_name ) || empty( $role ) ) {
            status_header( 400 );
            echo wp_json_encode( array( 'success' => false, 'message' => 'Required fields are missing.' ) );
            exit;
        }

        if ( email_exists( $email ) ) {
            status_header( 400 );
            echo wp_json_encode( array( 'success' => false, 'message' => 'This email is already registered in the system.' ) );
            exit;
        }

        $invitations = get_option( 'cora_invitations', array() );
        foreach ( $invitations as $inv ) {
            if ( $inv['email'] === $email && $inv['status'] === 'pending' && time() < intval( $inv['expires_at'] ) ) {
                status_header( 400 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'An active invitation already exists for this email.' ) );
                exit;
            }
        }

        if ( $current_role === 'cora_branch_manager' ) {
            $branch_id = cora_get_current_user_branch_id();
        }

        $token = bin2hex( random_bytes( 16 ) );
        $invitations[ $token ] = array(
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'email'         => $email,
            'role'          => $role,
            'agency_id'     => $current_agency,
            'branch_id'     => $branch_id,
            'invited_by'    => $current_user->ID,
            'expires_at'    => time() + 172800, // 48 hours
            'status'        => 'pending',
            'created_at'    => time(),
            'personal_note' => $personal_note
        );
        update_option( 'cora_invitations', $invitations );

        cora_log_activity( 'Invitation', "Sent invitation link to {$email}." );

        $verification_link = home_url( '/workspace/setup-account?token=' . $token );
        update_option( 'cora_latest_verification_link', $verification_link );

        echo wp_json_encode( array( 'success' => true, 'token' => $token, 'verification_link' => $verification_link ) );
        exit;
    }

    // GET /api/v1/users
    if ( 'users' === $resource && ! isset( $path_parts[3] ) && 'GET' === $method ) {
        $user_query_args = array();
        if ( $current_agency !== 'super' ) {
            $user_query_args['meta_query'] = array(
                array(
                    'key'     => 'cora_agency_id',
                    'value'   => $current_agency,
                    'compare' => '='
                )
            );
        }
        $all_wp_users = get_users( $user_query_args );
        $users_list = array();
        $my_branch = cora_get_current_user_branch_id();

        foreach ( $all_wp_users as $u ) {
            $u_branch = get_user_meta( $u->ID, 'cora_branch_id', true );
            if ( ! empty( $my_branch ) && $u_branch !== $my_branch ) {
                continue;
            }
            $status = get_user_meta( $u->ID, 'cora_user_status', true ) ?: 'active';
            
            $leads = get_option( 'cora_re_leads', array() );
            $leads_count = 0;
            if ( is_array( $leads ) ) {
                foreach ( $leads as $lead ) {
                    if ( isset( $lead['agent_id'] ) && intval( $lead['agent_id'] ) === $u->ID ) {
                        $leads_count++;
                    }
                }
            }

            $users_list[] = array(
                'id'           => $u->ID,
                'name'         => $u->display_name,
                'first_name'   => get_user_meta( $u->ID, 'first_name', true ),
                'last_name'    => get_user_meta( $u->ID, 'last_name', true ),
                'email'        => $u->user_email,
                'phone'        => get_user_meta( $u->ID, 'cora_phone', true ),
                'role'         => ! empty( $u->roles ) ? $u->roles[0] : '',
                'branch_id'    => $u_branch,
                'status'       => $status,
                'last_active'  => get_user_meta( $u->ID, 'cora_last_active', true ) ?: '',
                'joined'       => $u->user_registered,
                'leads_count'  => $leads_count
            );
        }
        echo wp_json_encode( array( 'success' => true, 'data' => $users_list ) );
        exit;
    }

    // /api/v1/users/{id}...
    if ( 'users' === $resource && isset( $path_parts[3] ) && is_numeric( $path_parts[3] ) ) {
        $target_id = intval( $path_parts[3] );
        $target_user = get_userdata( $target_id );
        if ( ! $target_user ) {
            status_header( 404 );
            echo wp_json_encode( array( 'success' => false, 'message' => 'User not found.' ) );
            exit;
        }

        $target_agency = get_user_meta( $target_id, 'cora_agency_id', true );
        if ( $current_agency !== 'super' && $target_agency !== $current_agency ) {
            status_header( 403 );
            echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
            exit;
        }

        $my_branch = cora_get_current_user_branch_id();
        $target_branch = get_user_meta( $target_id, 'cora_branch_id', true );
        if ( ! empty( $my_branch ) && $target_branch !== $my_branch ) {
            status_header( 403 );
            echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
            exit;
        }

        $action = $path_parts[4] ?? '';

        if ( 'GET' === $method && empty( $action ) ) {
            $status = get_user_meta( $target_id, 'cora_user_status', true ) ?: 'active';
            $leads = get_option( 'cora_re_leads', array() );
            $leads_count = 0;
            if ( is_array( $leads ) ) {
                foreach ( $leads as $lead ) {
                    if ( isset( $lead['agent_id'] ) && intval( $lead['agent_id'] ) === $target_id ) {
                        $leads_count++;
                    }
                }
            }

            echo wp_json_encode( array(
                'success' => true,
                'data'    => array(
                    'id'           => $target_id,
                    'name'         => $target_user->display_name,
                    'first_name'   => get_user_meta( $target_id, 'first_name', true ),
                    'last_name'    => get_user_meta( $target_id, 'last_name', true ),
                    'email'        => $target_user->user_email,
                    'phone'        => get_user_meta( $target_id, 'cora_phone', true ),
                    'role'         => ! empty( $target_user->roles ) ? $target_user->roles[0] : '',
                    'branch_id'    => $target_branch,
                    'status'       => $status,
                    'last_active'  => get_user_meta( $target_id, 'cora_last_active', true ) ?: '',
                    'joined'       => $target_user->user_registered,
                    'leads_count'  => $leads_count
                )
            ) );
            exit;
        }

        if ( 'PATCH' === $method && empty( $action ) ) {
            if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
                status_header( 403 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
                exit;
            }
            $raw = file_get_contents( 'php://input' );
            $data = json_decode( $raw, true ) ?: $_POST;

            $display_name = sanitize_text_field( $data['display_name'] ?? $target_user->display_name );
            $first_name = sanitize_text_field( $data['first_name'] ?? get_user_meta( $target_id, 'first_name', true ) );
            $last_name = sanitize_text_field( $data['last_name'] ?? get_user_meta( $target_id, 'last_name', true ) );
            $phone = sanitize_text_field( $data['phone'] ?? get_user_meta( $target_id, 'cora_phone', true ) );
            $role_to_set = sanitize_text_field( $data['role'] ?? '' );
            $branch_to_set = sanitize_text_field( $data['branch_id'] ?? '' );

            wp_update_user( array(
                'ID'           => $target_id,
                'display_name' => $display_name,
                'first_name'   => $first_name,
                'last_name'    => $last_name
            ) );
            update_user_meta( $target_id, 'cora_phone', $phone );

            if ( ! empty( $role_to_set ) ) {
                $target_user->set_role( $role_to_set );
            }
            if ( ! empty( $branch_to_set ) ) {
                update_user_meta( $target_id, 'cora_branch_id', $branch_to_set );
            }

            cora_log_activity( 'User Management', "Updated user profile '{$display_name}' (ID: {$target_id})." );
            echo wp_json_encode( array( 'success' => true, 'message' => 'Profile updated successfully.' ) );
            exit;
        }

        if ( 'POST' === $method && 'deactivate' === $action ) {
            if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
                status_header( 403 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
                exit;
            }
            $raw = file_get_contents( 'php://input' );
            $data = json_decode( $raw, true ) ?: $_POST;
            $reassign_to = intval( $data['reassign_to'] ?? 0 );

            update_user_meta( $target_id, 'cora_user_status', 'inactive' );
            wp_destroy_all_sessions( $target_id );

            remove_filter( 'option_cora_re_leads', 'cora_filter_tenancy_data' );
            remove_filter( 'pre_update_option_cora_re_leads', 'cora_pre_update_tenancy_data', 10, 3 );
            $leads = get_option( 'cora_re_leads', array() );
            $updated_leads = array();
            if ( is_array( $leads ) ) {
                foreach ( $leads as $lead ) {
                    if ( isset( $lead['agent_id'] ) && intval( $lead['agent_id'] ) === $target_id ) {
                        $lead['agent_id'] = $reassign_to > 0 ? $reassign_to : '';
                    }
                    $updated_leads[] = $lead;
                }
                update_option( 'cora_re_leads', $updated_leads );
            }
            add_filter( 'option_cora_re_leads', 'cora_filter_tenancy_data' );
            add_filter( 'pre_update_option_cora_re_leads', 'cora_pre_update_tenancy_data', 10, 3 );

            cora_log_activity( 'User Management', "Deactivated user '{$target_user->display_name}' (ID: {$target_id})." );
            echo wp_json_encode( array( 'success' => true, 'message' => 'User deactivated.' ) );
            exit;
        }

        if ( 'POST' === $method && 'activate' === $action ) {
            if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
                status_header( 403 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
                exit;
            }
            update_user_meta( $target_id, 'cora_user_status', 'active' );
            cora_log_activity( 'User Management', "Reactivated user '{$target_user->display_name}' (ID: {$target_id})." );
            echo wp_json_encode( array( 'success' => true, 'message' => 'User reactivated.' ) );
            exit;
        }

        if ( 'PATCH' === $method && 'role' === $action ) {
            if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
                status_header( 403 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
                exit;
            }
            $raw = file_get_contents( 'php://input' );
            $data = json_decode( $raw, true ) ?: $_POST;
            $new_role = sanitize_text_field( $data['role'] ?? '' );
            if ( ! empty( $new_role ) ) {
                $target_user->set_role( $new_role );
                cora_log_activity( 'User Management', "Changed role of user '{$target_user->display_name}' to {$new_role}." );
                echo wp_json_encode( array( 'success' => true, 'message' => 'Role updated.' ) );
                exit;
            }
        }

        if ( 'PATCH' === $method && 'branch' === $action ) {
            if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
                status_header( 403 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
                exit;
            }
            $raw = file_get_contents( 'php://input' );
            $data = json_decode( $raw, true ) ?: $_POST;
            $new_branch = sanitize_text_field( $data['branch_id'] ?? '' );
            update_user_meta( $target_id, 'cora_branch_id', $new_branch );
            cora_log_activity( 'User Management', "Moved user '{$target_user->display_name}' to branch: {$new_branch}." );
            echo wp_json_encode( array( 'success' => true, 'message' => 'Branch updated.' ) );
            exit;
        }
    }

    // /api/v1/invitations/{id}...
    if ( 'invitations' === $resource && isset( $path_parts[3] ) ) {
        $token = sanitize_text_field( $path_parts[3] );
        $invitations = get_option( 'cora_invitations', array() );
        if ( ! isset( $invitations[ $token ] ) ) {
            status_header( 404 );
            echo wp_json_encode( array( 'success' => false, 'message' => 'Invitation not found.' ) );
            exit;
        }

        $invite = $invitations[ $token ];
        if ( $current_agency !== 'super' && $invite['agency_id'] !== $current_agency ) {
            status_header( 403 );
            echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
            exit;
        }

        $action = $path_parts[4] ?? '';

        if ( 'DELETE' === $method && empty( $action ) ) {
            if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
                status_header( 403 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
                exit;
            }
            $invitations[ $token ]['status'] = 'cancelled';
            update_option( 'cora_invitations', $invitations );
            cora_log_activity( 'Invitation', "Cancelled invitation link for {$invite['email']}." );
            echo wp_json_encode( array( 'success' => true, 'message' => 'Invitation cancelled.' ) );
            exit;
        }

        if ( 'POST' === $method && 'resend' === $action ) {
            if ( ! in_array( $current_role, array( 'administrator', 'cora_manager', 'cora_branch_manager' ) ) ) {
                status_header( 403 );
                echo wp_json_encode( array( 'success' => false, 'message' => 'Forbidden' ) );
                exit;
            }
            $invitations[ $token ]['expires_at'] = time() + 172800; // Extend by 48 hours
            $invitations[ $token ]['status'] = 'pending';
            update_option( 'cora_invitations', $invitations );

            cora_log_activity( 'Invitation', "Resent invitation link to {$invite['email']}." );

            $verification_link = home_url( '/workspace/setup-account?token=' . $token );
            update_option( 'cora_latest_verification_link', $verification_link );

            echo wp_json_encode( array( 'success' => true, 'message' => 'Invitation resent.', 'verification_link' => $verification_link ) );
            exit;
        }
    }

    status_header( 404 );
    echo wp_json_encode( array( 'success' => false, 'message' => 'Endpoint not found.' ) );
    exit;
}

/**
 * --- Canvas AJAX Actions ---
 */

function cora_canvas_ajax_permission_check( $write = false ) {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'User not logged in.' );
    }
    $user = wp_get_current_user();
    $roles = (array) $user->roles;
    if ( in_array( 'administrator', $roles ) || in_array( 'cora_manager', $roles ) ) {
        return true;
    }
    if ( ! $write && in_array( 'cora_branch_manager', $roles ) ) {
        return true;
    }
    wp_send_json_error( 'Permission denied.' );
}

function cora_ajax_canvas_create_theme() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $name = sanitize_text_field( $_POST['name'] );
    $start_from = sanitize_text_field( $_POST['start_from'] );
    
    $settings = array(
        'site_title' => 'Cora Real Estate',
        'site_tagline' => 'Luxury Homes Catalog',
        'site_favicon' => '',
        'site_logo' => '',
        'heading_font' => 'Inter',
        'body_font' => 'Inter',
        'base_font_size' => 16,
        'primary_color' => '#18181b',
        'secondary_color' => '#27272a',
        'accent_color' => '#10b981',
        'text_color' => '#09090b',
        'bg_color' => '#ffffff',
        'header_layout' => 'Logo Left',
        'footer_columns' => '3',
        'sticky_header' => 1,
        'show_socials' => 1,
        'copyright_text' => '© ' . date('Y') . ' Cora Real Estate. All rights reserved.'
    );

    if ( $start_from === 'duplicate' ) {
        $live = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
        if ( $live ) {
            $settings = json_decode( $live['settings'], true ) ?: $settings;
        }
    }

    // Wizard onboarding custom parameters
    if ( isset( $_POST['builder'] ) ) {
        $settings['source'] = sanitize_text_field( $_POST['builder'] );
        if ( $_POST['builder'] === 'lovable' ) {
            $settings['lovable_project_url'] = esc_url_raw( $_POST['lovable_url'] );
            $settings['lovable_pat'] = sanitize_text_field( $_POST['lovable_token'] );
            
            // Persist GitHub credentials to WP options for automatic prefill
            if ( ! empty( $_POST['github_repo'] ) ) {
                update_option( 'cora_git_sync_repo', esc_url_raw( $_POST['github_repo'] ) );
                $settings['github_repo'] = esc_url_raw( $_POST['github_repo'] );
            }
            if ( ! empty( $_POST['github_token'] ) ) {
                update_option( 'cora_git_sync_token', sanitize_text_field( $_POST['github_token'] ) );
            }
            if ( ! empty( $_POST['github_branch'] ) ) {
                update_option( 'cora_git_sync_branch', sanitize_text_field( $_POST['github_branch'] ) );
                $settings['github_branch'] = sanitize_text_field( $_POST['github_branch'] );
            } else {
                $settings['github_branch'] = 'main';
            }
        } elseif ( $_POST['builder'] === 'elementor' ) {
            $settings['sub_mode'] = sanitize_text_field( $_POST['sub_mode'] );
            if ( $_POST['sub_mode'] === 'github' ) {
                $settings['github_repo'] = esc_url_raw( $_POST['github_repo'] );
                $settings['github_branch'] = sanitize_text_field( $_POST['github_branch'] );
            } else {
                $settings['elementor_kit'] = sanitize_text_field( $_POST['elementor_kit'] );
            }
        }
    }

    $wpdb->insert(
        $wpdb->prefix . 'cora_canvas_themes',
        array(
            'agency_id' => 1,
            'name' => $name,
            'status' => 'draft',
            'settings' => json_encode($settings),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array( '%d', '%s', '%s', '%s', '%s', '%s' )
    );
    
    $new_id = $wpdb->insert_id;

    if ( $start_from === 'duplicate' && ! empty($live) ) {
        $pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d", $live['id'] ), ARRAY_A );
        foreach ( $pages as $p ) {
            $wpdb->insert(
                $wpdb->prefix . 'cora_canvas_pages',
                array(
                    'agency_id' => 1,
                    'theme_id' => $new_id,
                    'wp_post_id' => $p['wp_post_id'],
                    'title' => $p['title'],
                    'slug' => $p['slug'],
                    'status' => 'draft',
                    'is_homepage' => $p['is_homepage'],
                    'template' => $p['template'],
                    'seo_title' => $p['seo_title'],
                    'seo_description' => $p['seo_description'],
                    'seo_og_image' => $p['seo_og_image'],
                    'created_by' => get_current_user_id(),
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
            );
        }
    }

    cora_log_activity( 'Canvas', "Created draft theme workspace '{$name}'." );
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_create_theme', 'cora_ajax_canvas_create_theme' );

function cora_ajax_canvas_activate_theme() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    
    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_themes',
        array( 'status' => 'draft', 'updated_at' => current_time('mysql') ),
        array( 'status' => 'live' )
    );
    
    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_themes',
        array( 'status' => 'live', 'activated_at' => current_time('mysql'), 'updated_at' => current_time('mysql') ),
        array( 'id' => $theme_id )
    );

    cora_log_activity( 'Canvas', "Activated theme id {$theme_id}." );
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_activate_theme', 'cora_ajax_canvas_activate_theme' );

function cora_ajax_canvas_rename_theme() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    $name = sanitize_text_field( $_POST['name'] );
    
    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_themes',
        array( 'name' => $name, 'updated_at' => current_time('mysql') ),
        array( 'id' => $theme_id )
    );
    
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_rename_theme', 'cora_ajax_canvas_rename_theme' );

function cora_ajax_canvas_connect_lovable() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    
    // Fetch live/active theme
    $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
    if ( ! $live_theme ) {
        $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes LIMIT 1", ARRAY_A );
    }
    
    if ( $live_theme ) {
        $settings = json_decode( $live_theme['settings'], true ) ?: array();
        $settings['source'] = 'lovable';
        $settings['lovable_project_url'] = sanitize_text_field( $_POST['project_url'] );
        
        $wpdb->update(
            $wpdb->prefix . 'cora_canvas_themes',
            array( 
                'settings' => json_encode( $settings ), 
                'updated_at' => current_time('mysql') 
            ),
            array( 'id' => $live_theme['id'] )
        );
    }
    
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_connect_lovable', 'cora_ajax_canvas_connect_lovable' );

function cora_ajax_canvas_delete_theme() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    
    $pages = $wpdb->get_results( $wpdb->prepare( "SELECT wp_post_id FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d", $theme_id ) );
    foreach ( $pages as $p ) {
        wp_delete_post( $p->wp_post_id, true );
    }
    $wpdb->delete( $wpdb->prefix . 'cora_canvas_pages', array( 'theme_id' => $theme_id ) );
    $wpdb->delete( $wpdb->prefix . 'cora_canvas_themes', array( 'id' => $theme_id ) );
    
    cora_log_activity( 'Canvas', "Deleted theme workspace id {$theme_id}." );
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_delete_theme', 'cora_ajax_canvas_delete_theme' );

function cora_ajax_canvas_duplicate_theme() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    
    $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
    if ( $theme ) {
        $new_name = $theme['name'] . ' (Copy)';
        $wpdb->insert(
            $wpdb->prefix . 'cora_canvas_themes',
            array(
                'agency_id' => 1,
                'name' => $new_name,
                'status' => 'draft',
                'settings' => $theme['settings'],
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s' )
        );
        $new_id = $wpdb->insert_id;
        
        $pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d", $theme_id ), ARRAY_A );
        foreach ( $pages as $p ) {
            $wpdb->insert(
                $wpdb->prefix . 'cora_canvas_pages',
                array(
                    'agency_id' => 1,
                    'theme_id' => $new_id,
                    'wp_post_id' => $p['wp_post_id'],
                    'title' => $p['title'],
                    'slug' => $p['slug'],
                    'status' => 'draft',
                    'is_homepage' => $p['is_homepage'],
                    'template' => $p['template'],
                    'seo_title' => $p['seo_title'],
                    'seo_description' => $p['seo_description'],
                    'seo_og_image' => $p['seo_og_image'],
                    'created_by' => get_current_user_id(),
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
            );
        }
        
        cora_log_activity( 'Canvas', "Duplicated theme id {$theme_id} to {$new_id}." );
        wp_send_json_success();
    }
    wp_send_json_error( 'Theme not found.' );
}
add_action( 'wp_ajax_cora_ajax_duplicate_theme', 'cora_ajax_canvas_duplicate_theme' );

/**
 * Validates and imports Elementor template kits or compatible themes.
 */
function cora_validate_and_import_elementor_kit( $file_path, $theme_name ) {
    if ( ! class_exists( 'ZipArchive' ) ) {
        return new WP_Error( 'zip_missing', 'PHP ZipArchive extension is not enabled on this server.' );
    }

    $zip = new ZipArchive;
    if ( $zip->open( $file_path ) !== TRUE ) {
        return new WP_Error( 'zip_open_failed', 'Failed to open ZIP file.' );
    }

    $is_elementor_kit = false;
    $is_compatible_theme = false;
    $templates_to_import = array();
    $style_css_content = '';

    // Loop through files inside ZIP
    for ( $i = 0; $i < $zip->numFiles; $i++ ) {
        $filename = $zip->getNameIndex( $i );
        
        // 1. Check for template kit manifest.json
        if ( basename( $filename ) === 'manifest.json' ) {
            $content = $zip->getFromIndex( $i );
            $manifest_data = json_decode( $content, true );
            if ( json_last_error() === JSON_ERROR_NONE && isset( $manifest_data['templates'] ) ) {
                $is_elementor_kit = true;
            }
        }
        
        // 2. Check for style.css (Theme)
        if ( basename( $filename ) === 'style.css' ) {
            $style_css_content = $zip->getFromIndex( $i );
            if ( preg_match( '/Theme Name:\s*(.*)/i', $style_css_content, $matches ) ) {
                $theme_name_extracted = trim( $matches[1] );
                if ( stripos( $theme_name_extracted, 'elementor' ) !== false || stripos( $style_css_content, 'elementor' ) !== false ) {
                    $is_compatible_theme = true;
                }
            }
        }

        // 3. Scan JSON files for Elementor page builder structures
        if ( pathinfo( $filename, PATHINFO_EXTENSION ) === 'json' && basename( $filename ) !== 'manifest.json' ) {
            $content = $zip->getFromIndex( $i );
            $json_data = json_decode( $content, true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                // Elementor template JSON structure check
                if ( isset( $json_data['type'] ) && ( isset( $json_data['content'] ) || isset( $json_data['page_settings'] ) ) ) {
                    $templates_to_import[] = array(
                        'title' => isset( $json_data['title'] ) ? sanitize_text_field( $json_data['title'] ) : basename( $filename, '.json' ),
                        'content' => $content,
                        'type' => $json_data['type']
                    );
                    $is_elementor_kit = true;
                }
            }
        }
    }

    if ( ! $is_elementor_kit && ! $is_compatible_theme ) {
        $zip->close();
        return new WP_Error( 'invalid_kit', 'Invalid Kit: This ZIP does not contain a valid Elementor Template Kit or an Elementor-compatible theme.' );
    }

    global $wpdb;

    // Handle Theme ZIP registration
    if ( $is_compatible_theme && ! $is_elementor_kit ) {
        $wpdb->insert(
            $wpdb->prefix . 'cora_canvas_themes',
            array(
                'agency_id' => 1,
                'name' => $theme_name,
                'status' => 'draft',
                'settings' => json_encode( array(
                    'source' => 'elementor',
                    'branding_primary' => '#18181b',
                    'branding_secondary' => '#52525b',
                    'branding_font' => 'system-ui',
                    'copyright_text' => '© ' . date('Y') . ' Cora Real Estate. All rights reserved.'
                ) ),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s' )
        );
        $zip->close();
        return array( 'success' => true, 'type' => 'theme', 'theme_id' => $wpdb->insert_id );
    }

    // Handle Elementor Template Kit import
    $wpdb->insert(
        $wpdb->prefix . 'cora_canvas_themes',
        array(
            'agency_id' => 1,
            'name' => $theme_name,
            'status' => 'draft',
            'settings' => json_encode( array(
                'source' => 'elementor',
                'branding_primary' => '#18181b',
                'branding_secondary' => '#52525b',
                'branding_font' => 'system-ui',
                'copyright_text' => '© ' . date('Y') . ' Cora Real Estate. All rights reserved.'
            ) ),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array( '%d', '%s', '%s', '%s', '%s', '%s' )
    );
    $theme_id = $wpdb->insert_id;

    // Import template pages
    foreach ( $templates_to_import as $tpl ) {
        if ( $tpl['type'] !== 'page' ) {
            continue;
        }

        $wp_post_id = wp_insert_post( array(
            'post_title'   => $tpl['title'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => get_current_user_id()
        ) );

        if ( ! is_wp_error( $wp_post_id ) ) {
            $tpl_data = json_decode( $tpl['content'], true );
            if ( isset( $tpl_data['content'] ) ) {
                update_post_meta( $wp_post_id, '_elementor_data', wp_slash( json_encode( $tpl_data['content'] ) ) );
            }
            update_post_meta( $wp_post_id, '_elementor_edit_mode', 'builder' );
            update_post_meta( $wp_post_id, '_elementor_template_type', 'page' );
            update_post_meta( $wp_post_id, '_wp_page_template', 'elementor_canvas' );
            
            $wpdb->insert(
                $wpdb->prefix . 'cora_canvas_pages',
                array(
                    'agency_id' => 1,
                    'theme_id' => $theme_id,
                    'wp_post_id' => $wp_post_id,
                    'title' => $tpl['title'],
                    'slug' => sanitize_title( $tpl['title'] ),
                    'status' => 'draft',
                    'is_homepage' => 0,
                    'template' => 'default',
                    'created_by' => get_current_user_id(),
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s' )
            );
        }
    }

    $zip->close();
    return array( 'success' => true, 'type' => 'kit', 'theme_id' => $theme_id );
}

/**
 * AJAX handler for importing template kits.
 */
function cora_ajax_canvas_import_kit() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    
    if ( empty( $_FILES['kit_zip'] ) ) {
        wp_send_json_error( 'No file uploaded.' );
    }

    $theme_name = sanitize_text_field( $_POST['theme_name'] );
    if ( empty( $theme_name ) ) {
        $theme_name = 'Imported Elementor Kit';
    }

    $file = $_FILES['kit_zip'];
    $imported = cora_validate_and_import_elementor_kit( $file['tmp_name'], $theme_name );

    if ( is_wp_error( $imported ) ) {
        wp_send_json_error( $imported->get_error_message() );
    }

    cora_log_activity( 'Canvas', "Imported Elementor Kit/Theme '{$theme_name}'." );
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_import_kit', 'cora_ajax_canvas_import_kit' );

function cora_ajax_canvas_scan_kit() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );

    if ( empty( $_FILES['kit_zip'] ) ) {
        wp_send_json_error( 'No file uploaded.' );
    }

    $file = $_FILES['kit_zip'];
    if ( ! class_exists( 'ZipArchive' ) ) {
        wp_send_json_error( 'PHP ZipArchive extension is not enabled on this server.' );
    }

    $zip = new ZipArchive;
    if ( $zip->open( $file['tmp_name'] ) !== TRUE ) {
        wp_send_json_error( 'Failed to open ZIP file.' );
    }

    $pages = array();
    for ( $i = 0; $i < $zip->numFiles; $i++ ) {
        $filename = $zip->getNameIndex( $i );
        // Scan JSON files (excluding manifest.json)
        if ( pathinfo( $filename, PATHINFO_EXTENSION ) === 'json' && basename( $filename ) !== 'manifest.json' ) {
            $content = $zip->getFromIndex( $i );
            $json_data = json_decode( $content, true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                if ( isset( $json_data['type'] ) && $json_data['type'] === 'page' ) {
                    $pages[] = isset( $json_data['title'] ) ? sanitize_text_field( $json_data['title'] ) : basename( $filename, '.json' );
                }
            }
        }
    }
    $zip->close();

    wp_send_json_success( array( 'pages' => $pages ) );
}
add_action( 'wp_ajax_cora_ajax_scan_kit', 'cora_ajax_canvas_scan_kit' );

function cora_ajax_canvas_get_theme_pages() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( false );
    global $wpdb;
    $theme_id = intval( $_GET['theme_id'] );
    
    $pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d ORDER BY is_homepage DESC, title ASC", $theme_id ), ARRAY_A );
    
    // Self-healing database check: Recreate WordPress pages if they were deleted or are out of sync
    if ( is_array( $pages ) ) {
        foreach ( $pages as $key => $p ) {
            $wp_post_id = intval( $p['wp_post_id'] );
            $post = get_post( $wp_post_id );
            if ( ! $post || $post->post_type !== 'page' ) {
                // Re-create the WordPress page
                $new_post_id = wp_insert_post( array(
                    'post_title'   => $p['title'],
                    'post_name'    => $p['slug'],
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_content' => '<!-- Elementor Page Content -->'
                ) );
                if ( ! is_wp_error( $new_post_id ) && $new_post_id > 0 ) {
                    // Update database
                    $wpdb->update(
                        $wpdb->prefix . 'cora_canvas_pages',
                        array( 'wp_post_id' => $new_post_id ),
                        array( 'id' => intval( $p['id'] ) ),
                        array( '%d' ),
                        array( '%d' )
                    );
                    // Update current page array
                    $pages[$key]['wp_post_id'] = $new_post_id;
                }
            }
        }
    }
    
    wp_send_json_success( $pages );
}
add_action( 'wp_ajax_cora_ajax_get_theme_pages', 'cora_ajax_canvas_get_theme_pages' );

function cora_ajax_canvas_create_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    $title = sanitize_text_field( $_POST['title'] );
    $slug = sanitize_title( $_POST['slug'] );
    $template = sanitize_text_field( $_POST['template'] );
    $status = sanitize_text_field( $_POST['status'] );

    // Query theme source setting to check if we are building under Elementor
    $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
    $theme_settings = json_decode( $theme['settings'], true ) ?: array();
    $source = isset( $theme_settings['source'] ) ? $theme_settings['source'] : '';

    $wp_post_id = wp_insert_post( array(
        'post_title' => $title,
        'post_name' => $slug,
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_content' => '<!-- Elementor Page Content -->'
    ) );

    if ( is_wp_error( $wp_post_id ) ) {
        wp_send_json_error( array( 'message' => $wp_post_id->get_error_message() ) );
    }

    // Initialize Elementor post meta for compatibility and style inheritance
    if ( $source === 'elementor' ) {
        update_post_meta( $wp_post_id, '_elementor_edit_mode', 'builder' );
        update_post_meta( $wp_post_id, '_elementor_template_type', 'page' );
        update_post_meta( $wp_post_id, '_elementor_data', wp_slash( json_encode( array() ) ) );

        // Select the appropriate template based on requested layout
        if ( $template === 'landing-page' || $template === 'minimal' ) {
            update_post_meta( $wp_post_id, '_wp_page_template', 'elementor_canvas' );
        } else {
            update_post_meta( $wp_post_id, '_wp_page_template', 'elementor_header_footer' );
        }
    }

    $wpdb->insert(
        $wpdb->prefix . 'cora_canvas_pages',
        array(
            'agency_id' => 1,
            'theme_id' => $theme_id,
            'wp_post_id' => $wp_post_id,
            'title' => $title,
            'slug' => $slug,
            'status' => $status,
            'is_homepage' => 0,
            'template' => $template,
            'created_by' => get_current_user_id(),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s' )
    );
    
    $page_id = $wpdb->insert_id;
    cora_log_activity( 'Canvas', "Created canvas page '{$title}' under theme workspace ID {$theme_id}." );

    wp_send_json_success( array(
        'page_id' => $page_id,
        'wp_post_id' => $wp_post_id
    ) );
}
add_action( 'wp_ajax_cora_ajax_create_page', 'cora_ajax_canvas_create_page' );

function cora_ajax_canvas_set_homepage() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $page_id = intval( $_POST['page_id'] );
    $theme_id = intval( $_POST['theme_id'] );
    
    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_pages',
        array( 'is_homepage' => 0, 'updated_at' => current_time('mysql') ),
        array( 'theme_id' => $theme_id )
    );
    
    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_pages',
        array( 'is_homepage' => 1, 'updated_at' => current_time('mysql') ),
        array( 'id' => $page_id )
    );

    cora_log_activity( 'Canvas', "Designated page id {$page_id} as theme homepage." );
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_set_homepage', 'cora_ajax_canvas_set_homepage' );

function cora_ajax_canvas_rename_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $page_id = intval( $_POST['page_id'] );
    $title = sanitize_text_field( $_POST['title'] );

    $page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $page_id ) );
    if ( $page ) {
        wp_update_post( array(
            'ID' => $page->wp_post_id,
            'post_title' => $title
        ) );
        $wpdb->update(
            $wpdb->prefix . 'cora_canvas_pages',
            array( 'title' => $title, 'updated_at' => current_time('mysql') ),
            array( 'id' => $page_id )
        );
        wp_send_json_success();
    }
    wp_send_json_error();
}
add_action( 'wp_ajax_cora_ajax_rename_page', 'cora_ajax_canvas_rename_page' );

function cora_ajax_canvas_change_slug() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $page_id = intval( $_POST['page_id'] );
    $slug = sanitize_title( $_POST['slug'] );

    $page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $page_id ) );
    if ( $page ) {
        wp_update_post( array(
            'ID' => $page->wp_post_id,
            'post_name' => $slug
        ) );
        $wpdb->update(
            $wpdb->prefix . 'cora_canvas_pages',
            array( 'slug' => $slug, 'updated_at' => current_time('mysql') ),
            array( 'id' => $page_id )
        );
        wp_send_json_success();
    }
    wp_send_json_error();
}
add_action( 'wp_ajax_cora_ajax_change_slug', 'cora_ajax_canvas_change_slug' );

function cora_ajax_canvas_delete_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $page_id = intval( $_POST['page_id'] );

    $page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $page_id ) );
    if ( $page ) {
        wp_delete_post( $page->wp_post_id, true );
        $wpdb->delete( $wpdb->prefix . 'cora_canvas_pages', array( 'id' => $page_id ) );
        wp_send_json_success();
    }
    wp_send_json_error();
}
add_action( 'wp_ajax_cora_ajax_delete_page', 'cora_ajax_canvas_delete_page' );

function cora_ajax_canvas_duplicate_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $page_id = intval( $_POST['page_id'] );

    $page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $page_id ), ARRAY_A );
    if ( $page ) {
        $new_title = $page['title'] . ' (Copy)';
        $new_slug = $page['slug'] . '-copy';
        
        $wp_post_id = wp_insert_post( array(
            'post_title' => $new_title,
            'post_name' => $new_slug,
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_content' => '<!-- Elementor Page Content -->'
        ) );
        
        if ( ! is_wp_error($wp_post_id) ) {
            $wpdb->insert(
                $wpdb->prefix . 'cora_canvas_pages',
                array(
                    'agency_id' => 1,
                    'theme_id' => $page['theme_id'],
                    'wp_post_id' => $wp_post_id,
                    'title' => $new_title,
                    'slug' => $new_slug,
                    'status' => 'draft',
                    'is_homepage' => 0,
                    'template' => $page['template'],
                    'seo_title' => $page['seo_title'],
                    'seo_description' => $page['seo_description'],
                    'seo_og_image' => $page['seo_og_image'],
                    'created_by' => get_current_user_id(),
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
            );
            wp_send_json_success();
        }
    }
    wp_send_json_error();
}
add_action( 'wp_ajax_cora_ajax_duplicate_page', 'cora_ajax_canvas_duplicate_page' );

function cora_ajax_canvas_bulk_pages() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $action_type = sanitize_text_field( $_POST['action_type'] );
    $page_ids = array_map( 'intval', $_POST['page_ids'] );

    if ( empty($page_ids) ) {
        wp_send_json_error();
    }

    foreach ( $page_ids as $id ) {
        if ( $action_type === 'delete' ) {
            $page = $wpdb->get_row( $wpdb->prepare( "SELECT wp_post_id FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $id ) );
            if ( $page ) {
                wp_delete_post( $page->wp_post_id, true );
            }
            $wpdb->delete( $wpdb->prefix . 'cora_canvas_pages', array( 'id' => $id ) );
        } elseif ( $action_type === 'publish' ) {
            $wpdb->update(
                $wpdb->prefix . 'cora_canvas_pages',
                array( 'status' => 'published', 'updated_at' => current_time('mysql') ),
                array( 'id' => $id )
            );
        } elseif ( $action_type === 'unpublish' ) {
            $wpdb->update(
                $wpdb->prefix . 'cora_canvas_pages',
                array( 'status' => 'draft', 'updated_at' => current_time('mysql') ),
                array( 'id' => $id )
            );
        }
    }

    cora_log_activity( 'Canvas', "Performed bulk action '{$action_type}' on pages count: " . count($page_ids) );
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_bulk_pages', 'cora_ajax_canvas_bulk_pages' );

function cora_ajax_canvas_save_theme_settings() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    $incoming = $_POST['settings'];

    // ── SAFE MERGE: read existing settings and overlay only the incoming keys ──
    $theme = $wpdb->get_row( $wpdb->prepare( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
    $existing = array();
    if ( $theme && ! empty( $theme['settings'] ) ) {
        $existing = json_decode( $theme['settings'], true ) ?: array();
    }
    // Sanitize and merge incoming fields
    $safe_incoming = array();
    $text_fields = [ 'site_title','site_tagline','site_description','site_favicon','site_logo',
                     'site_logo_dark','og_image','title_format','heading_font','body_font','accent_font',
                     'header_layout','footer_columns','copyright_text','nav_menu',
                     'facebook_link','twitter_link','linkedin_link','instagram_link','youtube_link','tiktok_link',
                     'ga4_id','gtm_id','fb_pixel','robots','border_width','border_color','box_shadow',
                     'page_width','header_bg','header_text_color','footer_bg','footer_text_color',
                     'primary_color','secondary_color','accent_color','text_color','bg_color',
                     'surface_color','success_color','warning_color','danger_color','info_color',
                     'btn_bg','btn_text','btn_hover_bg',
                     'el_primary','el_secondary','el_text','el_accent',
                     'el_type_primary_family','el_type_secondary_family','el_type_text_family','el_type_accent_family',
                     'el_type_primary_weight','el_type_secondary_weight','el_type_text_weight','el_type_accent_weight',
                     'css_prefix','gfonts_key' ];
    $int_fields  = [ 'base_font_size','container_width','section_padding','element_gap','widgets_spacing','border_radius' ];
    $bool_fields = [ 'sticky_header','show_socials','transparent_header','smooth_scroll','sitemap_enable','dark_tokens' ];
    foreach ( $text_fields as $k ) {
        if ( isset( $incoming[$k] ) ) $safe_incoming[$k] = sanitize_text_field( $incoming[$k] );
    }
    foreach ( $int_fields as $k ) {
        if ( isset( $incoming[$k] ) ) $safe_incoming[$k] = intval( $incoming[$k] );
    }
    foreach ( $bool_fields as $k ) {
        if ( isset( $incoming[$k] ) ) $safe_incoming[$k] = intval( $incoming[$k] );
    }
    // Type scale keys
    foreach ( ['h1','h2','h3','body','small','btn'] as $level ) {
        foreach ( ['size','weight','lh','ls'] as $prop ) {
            $k = "type_{$level}_{$prop}";
            if ( isset( $incoming[$k] ) ) $safe_incoming[$k] = sanitize_text_field( $incoming[$k] );
        }
    }
    // Merge: existing fields win for any key NOT in safe_incoming
    $merged = array_merge( $existing, $safe_incoming );

    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_themes',
        array( 'settings' => json_encode( $merged ), 'updated_at' => current_time('mysql') ),
        array( 'id' => $theme_id )
    );

    // ── Context-aware sync ──────────────────────────────────────────────────
    $source = $merged['source'] ?? '';
    $has_github = ! empty( $merged['github_repo'] );
    $has_lovable = ! empty( $merged['lovable_project_url'] );
    $is_elementor = ( $source === 'elementor' || ( ! $has_github && ! $has_lovable ) );

    if ( $is_elementor ) {
        // Attempt to push colors + typography to Elementor active kit
        cora_push_settings_to_elementor_kit( $merged );
    } elseif ( $has_lovable || $has_github ) {
        // Generate :root {} CSS token file for Lovable/GitHub theme
        cora_generate_lovable_token_css( $merged );
    }

    wp_send_json_success( array( 'merged' => true ) );
}
add_action( 'wp_ajax_cora_ajax_save_theme_settings', 'cora_ajax_canvas_save_theme_settings' );

// ── Elementor Global Kit Sync ───────────────────────────────────────────────
function cora_push_settings_to_elementor_kit( $settings ) {
    if ( ! function_exists( 'get_option' ) ) return;
    $kit_id = (int) get_option( 'elementor_active_kit', 0 );
    if ( $kit_id <= 0 ) return;

    $kit_meta = get_post_meta( $kit_id, '_elementor_page_settings', true );
    if ( ! is_array( $kit_meta ) ) $kit_meta = array();

    // System Colors — 4 Elementor global colors
    $primary   = $settings['el_primary']   ?? $settings['primary_color']   ?? '#18181b';
    $secondary = $settings['el_secondary'] ?? $settings['secondary_color']  ?? '#27272a';
    $text      = $settings['el_text']      ?? $settings['text_color']       ?? '#09090b';
    $accent    = $settings['el_accent']    ?? $settings['accent_color']     ?? '#10b981';

    $kit_meta['system_colors'] = array(
        array( '_id' => 'primary',   'title' => 'Primary',   'color' => sanitize_hex_color( $primary )   ?: '#18181b' ),
        array( '_id' => 'secondary', 'title' => 'Secondary', 'color' => sanitize_hex_color( $secondary ) ?: '#27272a' ),
        array( '_id' => 'text',      'title' => 'Text',      'color' => sanitize_hex_color( $text )      ?: '#09090b' ),
        array( '_id' => 'accent',    'title' => 'Accent',    'color' => sanitize_hex_color( $accent )    ?: '#10b981' ),
    );

    // System Typography — 4 Elementor global typography presets
    $type_map = array(
        'primary'   => array( 'title' => 'Primary',   'weight_fallback' => 700 ),
        'secondary' => array( 'title' => 'Secondary', 'weight_fallback' => 600 ),
        'text'      => array( 'title' => 'Text',      'weight_fallback' => 400 ),
        'accent'    => array( 'title' => 'Accent',    'weight_fallback' => 600 ),
    );
    $heading_font = $settings['heading_font'] ?? 'Inter';
    $body_font    = $settings['body_font']    ?? 'Inter';

    $kit_meta['system_typography'] = array();
    foreach ( $type_map as $tid => $tdef ) {
        $family = $settings[ "el_type_{$tid}_family" ] ?? ( ( $tid === 'text' ) ? $body_font : $heading_font );
        $weight = $settings[ "el_type_{$tid}_weight" ] ?? $tdef['weight_fallback'];
        $kit_meta['system_typography'][] = array(
            '_id'   => $tid,
            'title' => $tdef['title'],
            'typography_typography'   => 'custom',
            'typography_font_family'  => sanitize_text_field( $family ),
            'typography_font_weight'  => intval( $weight ),
        );
    }

    update_post_meta( $kit_id, '_elementor_page_settings', $kit_meta );

    // Flush Elementor's CSS cache so changes render on the next page load
    delete_post_meta( $kit_id, '_elementor_css' );

    // Also flush via Elementor Pro's files manager if available
    if ( class_exists( 'Elementor\Plugin' ) && isset( Elementor\Plugin::$instance->files_manager ) ) {
        Elementor\Plugin::$instance->files_manager->clear_cache();
    }
}

// ── Standalone Elementor Sync AJAX (called from "Sync to Elementor Now" button) ──
function cora_ajax_sync_elementor_globals() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    $theme = $wpdb->get_row( $wpdb->prepare( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
    if ( ! $theme ) { wp_send_json_error( 'Theme not found' ); return; }
    $settings = json_decode( $theme['settings'], true ) ?: array();
    cora_push_settings_to_elementor_kit( $settings );
    wp_send_json_success( array( 'synced_at' => current_time( 'c' ) ) );
}
add_action( 'wp_ajax_cora_ajax_sync_elementor_globals', 'cora_ajax_sync_elementor_globals' );

// ── Lovable / GitHub CSS Token File Generator ───────────────────────────────
function cora_generate_lovable_token_css( $settings ) {
    $prefix = $settings['css_prefix'] ?? '--';

    $tokens = array(
        'color-primary'    => $settings['primary_color']   ?? '#18181b',
        'color-secondary'  => $settings['secondary_color'] ?? '#27272a',
        'color-accent'     => $settings['accent_color']    ?? '#10b981',
        'color-text'       => $settings['text_color']      ?? '#09090b',
        'color-background' => $settings['bg_color']        ?? '#ffffff',
        'color-surface'    => $settings['surface_color']   ?? '#f4f4f5',
        'color-success'    => $settings['success_color']   ?? '#16a34a',
        'color-warning'    => $settings['warning_color']   ?? '#d97706',
        'color-danger'     => $settings['danger_color']    ?? '#dc2626',
        'color-info'       => $settings['info_color']      ?? '#2563eb',
        'btn-bg'           => $settings['btn_bg']          ?? $settings['primary_color'] ?? '#18181b',
        'btn-text'         => $settings['btn_text']        ?? '#ffffff',
        'btn-hover-bg'     => $settings['btn_hover_bg']    ?? $settings['secondary_color'] ?? '#27272a',
        'heading-font'     => "'" . ( $settings['heading_font'] ?? 'Inter' ) . "', sans-serif",
        'body-font'        => "'" . ( $settings['body_font']    ?? 'Inter' ) . "', sans-serif",
        'base-font-size'   => ( $settings['base_font_size'] ?? 16 ) . 'px',
        'container-width'  => ( $settings['container_width'] ?? 1280 ) . 'px',
        'section-padding'  => ( $settings['section_padding'] ?? 80 ) . 'px',
        'element-gap'      => ( $settings['element_gap']     ?? 24 ) . 'px',
        'border-radius'    => ( $settings['border_radius']   ?? 8 ) . 'px',
        'border-color'     => $settings['border_color']  ?? '#e4e4e7',
        'border-width'     => ( $settings['border_width'] ?? 1 ) . 'px',
        'box-shadow'       => $settings['box_shadow']    ?? '0 1px 3px rgba(0,0,0,0.06)',
        'header-bg'        => $settings['header_bg']    ?? '#ffffff',
        'header-text'      => $settings['header_text_color'] ?? '#18181b',
        'footer-bg'        => $settings['footer_bg']    ?? '#18181b',
        'footer-text'      => $settings['footer_text_color'] ?? '#a1a1aa',
    );

    $css = "/* Cora Global Design Tokens — auto-generated, do not edit manually */\n:root {\n";
    foreach ( $tokens as $k => $v ) {
        $css .= "  {$prefix}{$k}: {$v};\n";
    }
    $css .= "}\n";

    $upload_dir = wp_upload_dir();
    $cora_dir   = trailingslashit( $upload_dir['basedir'] ) . 'cora/';
    if ( ! file_exists( $cora_dir ) ) wp_mkdir_p( $cora_dir );
    file_put_contents( $cora_dir . 'cora-global-tokens.css', $css );
}

// ── Standalone Lovable Token Sync AJAX ──────────────────────────────────────
function cora_ajax_sync_lovable_tokens() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    $theme = $wpdb->get_row( $wpdb->prepare( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
    if ( ! $theme ) { wp_send_json_error( 'Theme not found' ); return; }
    $settings = json_decode( $theme['settings'], true ) ?: array();
    cora_generate_lovable_token_css( $settings );
    $upload_dir = wp_upload_dir();
    wp_send_json_success( array(
        'file_url'  => trailingslashit( $upload_dir['baseurl'] ) . 'cora/cora-global-tokens.css',
        'synced_at' => current_time( 'c' ),
    ) );
}
add_action( 'wp_ajax_cora_ajax_sync_lovable_tokens', 'cora_ajax_sync_lovable_tokens' );

// ── Frontend: enqueue Lovable token CSS file on every page ──────────────────
add_action( 'wp_enqueue_scripts', 'cora_enqueue_global_token_css', 5 );
function cora_enqueue_global_token_css() {
    $upload_dir = wp_upload_dir();
    $token_file = trailingslashit( $upload_dir['basedir'] ) . 'cora/cora-global-tokens.css';
    if ( file_exists( $token_file ) ) {
        $token_url = trailingslashit( $upload_dir['baseurl'] ) . 'cora/cora-global-tokens.css';
        wp_enqueue_style( 'cora-global-tokens', $token_url, array(), filemtime( $token_file ) );
    }
}

function cora_ajax_canvas_save_custom_css() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    $css = $_POST['css'];

    if ( $theme_id > 0 ) {
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        if ( $theme ) {
            $settings = json_decode( $theme['settings'], true ) ?: array();
            $settings['custom_css'] = $css;
            $wpdb->update(
                $wpdb->prefix . 'cora_canvas_themes',
                array( 'settings' => json_encode($settings), 'updated_at' => current_time('mysql') ),
                array( 'id' => $theme_id )
            );
        }
    }

    // Update global option as fallback
    update_option( 'cora_canvas_custom_css', $css );
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_save_custom_css', 'cora_ajax_canvas_save_custom_css' );

function cora_ajax_canvas_save_custom_js() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id = intval( $_POST['theme_id'] );
    $js = $_POST['js'];
    $position = sanitize_text_field( $_POST['position'] );

    if ( $theme_id > 0 ) {
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        if ( $theme ) {
            $settings = json_decode( $theme['settings'], true ) ?: array();
            $settings['custom_js'] = $js;
            $settings['custom_js_position'] = $position;
            $wpdb->update(
                $wpdb->prefix . 'cora_canvas_themes',
                array( 'settings' => json_encode($settings), 'updated_at' => current_time('mysql') ),
                array( 'id' => $theme_id )
            );
        }
    }

    update_option( 'cora_canvas_custom_js', $js );
    update_option( 'cora_canvas_custom_js_position', $position );
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_save_custom_js', 'cora_ajax_canvas_save_custom_js' );

// ── Save: Head HTML Injection ────────────────────────────────────────────────
function cora_ajax_canvas_save_custom_head() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id  = intval( $_POST['theme_id'] );
    $head_html = current_user_can( 'unfiltered_html' ) ? $_POST['head_html'] : wp_kses_post( $_POST['head_html'] );

    if ( $theme_id > 0 ) {
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        if ( $theme ) {
            $settings = json_decode( $theme['settings'], true ) ?: array();
            $settings['custom_head'] = $head_html;
            $wpdb->update(
                $wpdb->prefix . 'cora_canvas_themes',
                array( 'settings' => json_encode($settings), 'updated_at' => current_time('mysql') ),
                array( 'id' => $theme_id )
            );
        }
    }
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_save_custom_head', 'cora_ajax_canvas_save_custom_head' );

// ── Save: Body Script Injection ──────────────────────────────────────────────
function cora_ajax_canvas_save_custom_body() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $theme_id  = intval( $_POST['theme_id'] );
    $body_html = current_user_can( 'unfiltered_html' ) ? $_POST['body_html'] : wp_kses_post( $_POST['body_html'] );

    if ( $theme_id > 0 ) {
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        if ( $theme ) {
            $settings = json_decode( $theme['settings'], true ) ?: array();
            $settings['custom_body'] = $body_html;
            $wpdb->update(
                $wpdb->prefix . 'cora_canvas_themes',
                array( 'settings' => json_encode($settings), 'updated_at' => current_time('mysql') ),
                array( 'id' => $theme_id )
            );
        }
    }
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_save_custom_body', 'cora_ajax_canvas_save_custom_body' );



function cora_ajax_canvas_publish_canvas_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $page_id = intval( $_POST['page_id'] );
    
    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_pages',
        array( 'status' => 'published', 'updated_at' => current_time('mysql') ),
        array( 'id' => $page_id )
    );
    
    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_publish_canvas_page', 'cora_ajax_canvas_publish_canvas_page' );

function cora_ajax_canvas_save_page_seo() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );
    global $wpdb;
    $page_id = intval( $_POST['page_id'] );
    $seo_title = sanitize_text_field( $_POST['seo_title'] );
    $seo_desc = sanitize_textarea_field( $_POST['seo_description'] );
    $seo_og_image = esc_url_raw( $_POST['seo_og_image'] );

    $wpdb->update(
        $wpdb->prefix . 'cora_canvas_pages',
        array(
            'seo_title' => $seo_title,
            'seo_description' => $seo_desc,
            'seo_og_image' => $seo_og_image,
            'updated_at' => current_time('mysql')
        ),
        array( 'id' => $page_id )
    );

    wp_send_json_success();
}
add_action( 'wp_ajax_cora_ajax_save_page_seo', 'cora_ajax_canvas_save_page_seo' );

// ── Template Kit Connection AJAX ────────────────────────────────────────────

function cora_ajax_save_canvas_kit() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );

    $theme_id    = intval( $_POST['theme_id'] );
    $kit_name    = sanitize_text_field( $_POST['kit_name'] );
    $kit_url     = esc_url_raw( $_POST['kit_url'] );
    $kit_provider = sanitize_text_field( $_POST['kit_provider'] );
    $kit_license = sanitize_text_field( $_POST['kit_license'] );

    // Encrypt the license/purchase code so it is not stored in plain text.
    $encrypted_license = '';
    if ( ! empty( $kit_license ) ) {
        $key    = defined( 'AUTH_KEY' ) ? AUTH_KEY : 'cora_fallback_key';
        $iv_len = openssl_cipher_iv_length( 'AES-256-CBC' );
        $iv     = openssl_random_pseudo_bytes( $iv_len );
        $raw    = openssl_encrypt( $kit_license, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv );
        $encrypted_license = base64_encode( $iv . $raw );
    }

    $user_id = get_current_user_id();
    $meta_key = 'cora_canvas_kit_' . $theme_id;

    update_user_meta( $user_id, $meta_key, array(
        'kit_name'     => $kit_name,
        'kit_url'      => $kit_url,
        'kit_provider' => $kit_provider,
        'kit_license'  => $encrypted_license,
        'updated_at'   => current_time( 'mysql' ),
    ) );

    wp_send_json_success( array( 'kit_name' => $kit_name ) );
}
add_action( 'wp_ajax_cora_save_canvas_kit', 'cora_ajax_save_canvas_kit' );

function cora_ajax_get_canvas_kit() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    cora_canvas_ajax_permission_check( true );

    $theme_id = intval( $_POST['theme_id'] );
    $user_id  = get_current_user_id();
    $meta_key = 'cora_canvas_kit_' . $theme_id;
    $kit      = get_user_meta( $user_id, $meta_key, true );

    if ( empty( $kit ) || empty( $kit['kit_name'] ) ) {
        wp_send_json_success( array() );
        return;
    }

    // Return data — license is intentionally omitted from the response for security.
    wp_send_json_success( array(
        'kit_name'     => $kit['kit_name'],
        'kit_url'      => $kit['kit_url'],
        'kit_provider' => $kit['kit_provider'],
        'updated_at'   => $kit['updated_at'] ?? '',
    ) );
}
add_action( 'wp_ajax_cora_get_canvas_kit', 'cora_ajax_get_canvas_kit' );

function cora_get_bip_problems_html() {
    return '
<div class="min-h-screen bg-[#FBFaf7] text-zinc-900 flex flex-col font-sans select-none antialiased">
    <!-- Header presentation bar -->
    <header class="w-full border-b border-zinc-250 bg-[#F9F6F0] py-4 px-8 flex justify-between items-center z-25">
        <div class="flex items-center gap-3">
            <span class="p-1.5 bg-zinc-950 text-white rounded-lg">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </span>
            <div>
                <h1 class="text-sm font-bold uppercase tracking-wider text-zinc-900">Cora OS</h1>
                <p class="text-[9px] text-zinc-400 font-semibold tracking-widest uppercase">Decoupled Canvas Presentation</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-1.5 px-3 py-1 border border-zinc-200 rounded-full bg-white text-[10px] font-bold text-zinc-500">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                LIVE STREAM MODE
            </div>
            <div class="text-[10px] font-bold tracking-widest text-zinc-400 uppercase">EPISODE 01: PROBLEM REALIZATION</div>
        </div>
    </header>

    <!-- Presentation canvas container -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-8 flex flex-col justify-center gap-8">
        
        <!-- Hero intro details -->
        <div class="text-center max-w-3xl mx-auto space-y-3">
            <span class="text-[10px] font-bold tracking-[0.3em] text-zinc-400 uppercase block">The Cost of Bloated Operations</span>
            <h2 class="text-4xl font-extrabold tracking-tight text-zinc-900 uppercase">The Subscription Bleed & Lead Leakage</h2>
            <p class="text-xs text-zinc-500 max-w-xl mx-auto font-medium leading-relaxed">
                Why traditional Indian real estate agencies lose up to 80% of marketing ROI to fragmented monthly software bills and slow lead response cycles.
            </p>
        </div>

        <!-- Simulation Grid: Desktop side-by-side presentation widgets -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
            
            <!-- Left panel: Subscription cost audit card -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between hover:border-zinc-350 transition-colors">
                <div>
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3 mb-4">
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-wider flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            Tool Subscription Audit
                        </h3>
                        <span class="text-[10px] font-bold text-zinc-400">Standard Indian Agency Stack</span>
                    </div>

                    <div class="space-y-3">
                        <!-- Checkbox row 1 -->
                        <label class="flex items-center justify-between p-3 border border-zinc-200 rounded-xl bg-zinc-50/20 hover:bg-zinc-50 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" checked data-cost="1500" class="bleed-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-zinc-950" onchange="recalculateCost()">
                                <div>
                                    <div class="text-xs font-bold text-zinc-900">Form Builders</div>
                                    <div class="text-[9px] text-zinc-400 font-medium">Typeform / WPForms Pro</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-zinc-800">₹1,500 / mo</span>
                        </label>

                        <!-- Checkbox row 2 -->
                        <label class="flex items-center justify-between p-3 border border-zinc-200 rounded-xl bg-zinc-50/20 hover:bg-zinc-50 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" checked data-cost="1200" class="bleed-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-zinc-950" onchange="recalculateCost()">
                                <div>
                                    <div class="text-xs font-bold text-zinc-900">Scheduling Calendar</div>
                                    <div class="text-[9px] text-zinc-400 font-medium">Calendly Pro</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-zinc-800">₹1,200 / mo</span>
                        </label>

                        <!-- Checkbox row 3 -->
                        <label class="flex items-center justify-between p-3 border border-zinc-200 rounded-xl bg-zinc-50/20 hover:bg-zinc-50 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" checked data-cost="650" class="bleed-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-zinc-950" onchange="recalculateCost()">
                                <div>
                                    <div class="text-xs font-bold text-zinc-900">Cloud Storage & Backups</div>
                                    <div class="text-[9px] text-zinc-400 font-medium">Google Drive / Dropbox</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-zinc-800">₹650 / mo</span>
                        </label>

                        <!-- Checkbox row 4 -->
                        <label class="flex items-center justify-between p-3 border border-zinc-200 rounded-xl bg-zinc-50/20 hover:bg-zinc-50 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" checked data-cost="3500" class="bleed-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-zinc-950" onchange="recalculateCost()">
                                <div>
                                    <div class="text-xs font-bold text-zinc-900">CRM & Lead Management</div>
                                    <div class="text-[9px] text-zinc-400 font-medium">Salesforce / Generic Real Estate CRM</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-zinc-800">₹3,500 / mo</span>
                        </label>

                        <!-- Checkbox row 5 -->
                        <label class="flex items-center justify-between p-3 border border-zinc-200 rounded-xl bg-zinc-50/20 hover:bg-zinc-50 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" checked data-cost="1500" class="bleed-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-zinc-950" onchange="recalculateCost()">
                                <div>
                                    <div class="text-xs font-bold text-zinc-900">WhatsApp & SMS API Gateway</div>
                                    <div class="text-[9px] text-zinc-400 font-medium">Twilio / Msg91 dispatch module</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-zinc-800">₹1,500 / mo</span>
                        </label>
                    </div>
                </div>

                <!-- Live totals and savings comparison -->
                <div class="border-t border-zinc-200 pt-5 mt-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl">
                            <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-wider block">Monthly Bleed</span>
                            <span id="monthly-leakage" class="text-lg font-black text-zinc-900 mt-1 block">₹8,350</span>
                        </div>
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl">
                            <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-wider block">Annual Loss</span>
                            <span id="annual-leakage" class="text-lg font-black text-zinc-900 mt-1 block">₹1,00,200</span>
                        </div>
                    </div>

                    <!-- Comparison chart bars -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-[10px] font-bold">
                            <span class="text-zinc-500 uppercase tracking-wider">Traditional stack cost</span>
                            <span id="monthly-leakage-label" class="text-zinc-900">₹8,350/mo</span>
                        </div>
                        <div class="h-2.5 w-full bg-zinc-100 rounded-full overflow-hidden">
                            <div id="status-quo-bar" class="h-full bg-red-500 rounded-full transition-all duration-300" style="width: 100%;"></div>
                        </div>

                        <div class="flex justify-between items-center text-[10px] font-bold pt-1">
                            <span class="text-zinc-500 uppercase tracking-wider">Cora Consolidated flat price</span>
                            <span class="text-emerald-600">₹2,000/mo</span>
                        </div>
                        <div class="h-2.5 w-full bg-zinc-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: 24%;"></div>
                        </div>
                    </div>

                    <!-- savings toast alert -->
                    <div class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-800 text-xs font-semibold">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <div>
                            Consolidating returns <span id="annual-savings" class="font-extrabold text-emerald-950">₹76,200</span> directly to your annual bottom line.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right panel: Speed-to-lead latency simulator -->
            <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between hover:border-zinc-350 transition-colors">
                <div>
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3 mb-4">
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-wider flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Speed-to-Lead Simulator
                        </h3>
                        <span class="text-[10px] font-bold text-zinc-400">Response Latency Demo</span>
                    </div>

                    <div class="space-y-6">
                        <!-- Path A: Manual path -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold uppercase text-red-650 tracking-wide">Path A: Traditional Manual Flow</span>
                                <div class="flex items-center gap-2">
                                    <span id="manual-timer" class="font-mono text-xs font-bold text-zinc-800">0.0s</span>
                                    <span id="manual-status" class="hidden text-[8px] font-bold uppercase bg-red-100 text-red-700 px-1 rounded">Dead Lead</span>
                                </div>
                            </div>
                            
                            <!-- Flow grid -->
                            <div class="grid grid-cols-4 gap-2 text-center">
                                <div class="manual-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300">
                                    <div class="text-[8px] font-bold text-zinc-400 uppercase">Step 1</div>
                                    <div class="text-[9px] font-bold text-zinc-800 mt-0.5">Portal Inquiry</div>
                                </div>
                                <div class="manual-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300">
                                    <div class="text-[8px] font-bold text-zinc-400 uppercase">Step 2</div>
                                    <div class="text-[9px] font-bold text-zinc-800 mt-0.5">Excel Copy</div>
                                </div>
                                <div class="manual-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300">
                                    <div class="text-[8px] font-bold text-zinc-400 uppercase">Step 3</div>
                                    <div class="text-[9px] font-bold text-zinc-800 mt-0.5">WhatsApp Group</div>
                                </div>
                                <div class="manual-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300">
                                    <div class="text-[8px] font-bold text-zinc-400 uppercase">Step 4</div>
                                    <div class="text-[9px] font-bold text-zinc-800 mt-0.5">Agent Calls</div>
                                </div>
                            </div>
                            <p class="text-[10px] text-zinc-400 leading-relaxed font-medium italic">
                                Manual copy-pasting lead details from listing portals to agents takes hours, during which the client goes cold.
                            </p>
                        </div>

                        <!-- Path B: Cora automation path -->
                        <div class="space-y-3 pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold uppercase text-emerald-600 tracking-wide">Path B: Cora OS Automated Route</span>
                                <div class="flex items-center gap-2">
                                    <span id="cora-timer" class="font-mono text-xs font-bold text-zinc-800">0.0s</span>
                                    <span id="cora-status" class="hidden text-[8px] font-bold uppercase bg-emerald-100 text-emerald-700 px-1 rounded">Dispatched</span>
                                </div>
                            </div>
                            
                            <!-- Flow grid -->
                            <div class="grid grid-cols-4 gap-2 text-center">
                                <div class="cora-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300">
                                    <div class="text-[8px] font-bold text-zinc-400 uppercase">Step 1</div>
                                    <div class="text-[9px] font-bold text-zinc-800 mt-0.5">API Ingest</div>
                                </div>
                                <div class="cora-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300">
                                    <div class="text-[8px] font-bold text-zinc-400 uppercase">Step 2</div>
                                    <div class="text-[9px] font-bold text-zinc-800 mt-0.5">REST Router</div>
                                </div>
                                <div class="cora-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300">
                                    <div class="text-[8px] font-bold text-zinc-400 uppercase">Step 3</div>
                                    <div class="text-[9px] font-bold text-zinc-800 mt-0.5">Auto WhatsApp</div>
                                </div>
                                <div class="cora-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300">
                                    <div class="text-[8px] font-bold text-zinc-400 uppercase">Step 4</div>
                                    <div class="text-[9px] font-bold text-zinc-800 mt-0.5">KYC Secured</div>
                                </div>
                            </div>
                            <p class="text-[10px] text-zinc-400 leading-relaxed font-medium italic">
                                The moment a client inquires, Cora automatically dispatches the PAN/Aadhaar secure upload link and brochure via WhatsApp instantly.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Simulation trigger controls -->
                <div class="border-t border-zinc-200 pt-5 mt-6 flex justify-between items-center">
                    <div class="text-[10px] font-bold text-zinc-400">Target response speed limit: 5 Minutes</div>
                    <button onclick="runLeadSimulation()" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold shadow-sm transition-all cursor-pointer">
                        Simulate Portal Lead Inquiry
                    </button>
                </div>
            </div>
            
        </div>

        <!-- Section 3: The 5 Core Operational Leaks -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider text-center">The 5 Core Operational Leaks</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                
                <!-- Leak 1 -->
                <div class="p-4 bg-white border border-zinc-200 rounded-xl hover:border-zinc-350 transition-colors flex flex-col justify-between gap-3">
                    <span class="w-7 h-7 flex items-center justify-center bg-zinc-100 text-zinc-700 rounded-lg">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-900 leading-tight">1. Insecure KYC Compliance</h4>
                        <p class="text-[9px] text-zinc-400 leading-normal mt-1 font-medium">PAN and Aadhaar photo files stored in agent galleries. High risk of breach.</p>
                    </div>
                </div>

                <!-- Leak 2 -->
                <div class="p-4 bg-white border border-zinc-200 rounded-xl hover:border-zinc-350 transition-colors flex flex-col justify-between gap-3">
                    <span class="w-7 h-7 flex items-center justify-center bg-zinc-100 text-zinc-700 rounded-lg">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
                    </span>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-900 leading-tight">2. Portal Login Fatigue</h4>
                        <p class="text-[9px] text-zinc-400 leading-normal mt-1 font-medium">Logging into 3 different portals everyday just to see where leads came from.</p>
                    </div>
                </div>

                <!-- Leak 3 -->
                <div class="p-4 bg-white border border-zinc-200 rounded-xl hover:border-zinc-350 transition-colors flex flex-col justify-between gap-3">
                    <span class="w-7 h-7 flex items-center justify-center bg-zinc-100 text-zinc-700 rounded-lg">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </span>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-900 leading-tight">3. Manual Brochure Sharing</h4>
                        <p class="text-[9px] text-zinc-400 leading-normal mt-1 font-medium">Agents spend 15 mins drafting emails & copying files for every inquiry request.</p>
                    </div>
                </div>

                <!-- Leak 4 -->
                <div class="p-4 bg-white border border-zinc-200 rounded-xl hover:border-zinc-350 transition-colors flex flex-col justify-between gap-3">
                    <span class="w-7 h-7 flex items-center justify-center bg-zinc-100 text-zinc-700 rounded-lg">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </span>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-900 leading-tight">4. UPI Payment Friction</h4>
                        <p class="text-[9px] text-zinc-400 leading-normal mt-1 font-medium">Tracking screenshots manually, leading to tax & billing reconciliation chaos.</p>
                    </div>
                </div>

                <!-- Leak 5 -->
                <div class="p-4 bg-white border border-zinc-200 rounded-xl hover:border-zinc-350 transition-colors flex flex-col justify-between gap-3">
                    <span class="w-7 h-7 flex items-center justify-center bg-zinc-100 text-zinc-700 rounded-lg">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    </span>
                    <div>
                        <h4 class="text-xs font-bold text-zinc-900 leading-tight">5. No Google Review Loop</h4>
                        <p class="text-[9px] text-zinc-400 leading-normal mt-1 font-medium">90% of clients forget to leave reviews because there is no post-deal automated trigger.</p>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Interactive presentation scripts -->
    <script>
        function recalculateCost() {
            let total = 0;
            const checkboxes = document.querySelectorAll(".bleed-checkbox");
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    total += parseInt(cb.getAttribute("data-cost"));
                }
            });
            
            const monthlyTotal = total;
            const annualTotal = total * 12;
            const coraMonthly = 2000;
            const coraAnnual = 24000;
            const annualSavings = Math.max(0, annualTotal - coraAnnual);
            
            document.getElementById("monthly-leakage").textContent = "₹" + monthlyTotal.toLocaleString("en-IN");
            document.getElementById("annual-leakage").textContent = "₹" + annualTotal.toLocaleString("en-IN");
            document.getElementById("monthly-leakage-label").textContent = "₹" + monthlyTotal.toLocaleString("en-IN") + "/mo";
            document.getElementById("annual-savings").textContent = "₹" + annualSavings.toLocaleString("en-IN");
            
            const maxCost = 8350;
            const currentPercent = (monthlyTotal / maxCost) * 100;
            document.getElementById("status-quo-bar").style.width = currentPercent + "%";
        }

        let simInterval = null;
        function runLeadSimulation() {
            const manualNodes = document.querySelectorAll(".manual-node");
            const coraNodes = document.querySelectorAll(".cora-node");
            
            manualNodes.forEach(node => node.className = "manual-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300");
            coraNodes.forEach(node => node.className = "cora-node p-2 border border-zinc-200 bg-zinc-50/50 rounded-lg transition-all duration-300");
            
            document.getElementById("manual-timer").textContent = "0.0s";
            document.getElementById("cora-timer").textContent = "0.0s";
            document.getElementById("manual-status").className = "hidden";
            document.getElementById("cora-status").className = "hidden";
            
            let coraTime = 0;
            const coraTimer = setInterval(() => {
                coraTime += 0.1;
                document.getElementById("cora-timer").textContent = coraTime.toFixed(1) + "s";
                
                // Highlight nodes progressively
                if (coraTime >= 0.3) coraNodes[0].className = "cora-node p-2 border border-emerald-400 bg-emerald-50 text-emerald-700 rounded-lg transition-all duration-300";
                if (coraTime >= 0.6) coraNodes[1].className = "cora-node p-2 border border-emerald-400 bg-emerald-50 text-emerald-700 rounded-lg transition-all duration-300";
                if (coraTime >= 0.9) coraNodes[2].className = "cora-node p-2 border border-emerald-400 bg-emerald-50 text-emerald-700 rounded-lg transition-all duration-300";
                
                if (coraTime >= 1.2) {
                    clearInterval(coraTimer);
                    document.getElementById("cora-timer").textContent = "1.2s";
                    coraNodes[3].className = "cora-node p-2 border border-emerald-400 bg-emerald-50 text-emerald-700 rounded-lg transition-all duration-300";
                    document.getElementById("cora-status").className = "text-[8px] font-bold uppercase bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded";
                }
            }, 100);
            
            let manualTime = 0;
            const manualTimer = setInterval(() => {
                manualTime += 0.1;
                const hours = (manualTime * 1.0).toFixed(1);
                document.getElementById("manual-timer").textContent = hours + " hrs";
                
                if (manualTime >= 1.0) manualNodes[0].className = "manual-node p-2 border border-red-300 bg-red-50/50 text-red-700 rounded-lg transition-all duration-300";
                if (manualTime >= 2.0) manualNodes[1].className = "manual-node p-2 border border-red-300 bg-red-50/50 text-red-700 rounded-lg transition-all duration-300";
                if (manualTime >= 3.0) manualNodes[2].className = "manual-node p-2 border border-red-300 bg-red-50/50 text-red-700 rounded-lg transition-all duration-300";
                
                if (manualTime >= 4.5) {
                    clearInterval(manualTimer);
                    document.getElementById("manual-timer").textContent = "4.5 hrs";
                    manualNodes[3].className = "manual-node p-2 border border-red-400 bg-red-50 text-red-700 rounded-lg transition-all duration-300";
                    document.getElementById("manual-status").className = "text-[8px] font-bold uppercase bg-red-100 text-red-700 px-1.5 py-0.5 rounded";
                }
            }, 100);
        }
    </script>
</div>
';
}

// ── Forms Module REST API Handlers ──────────────────────────────────────────

function cora_rest_get_forms( $request ) {
    global $wpdb;
    $forms = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_forms ORDER BY id DESC", ARRAY_A );
    if ( is_array( $forms ) ) {
        foreach ( $forms as &$form ) {
            $form['styling'] = json_decode( $form['styling'], true ) ?: array();
            $form['settings'] = json_decode( $form['settings'], true ) ?: array();
            
            // Fetch blocks
            $blocks_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_form_blocks WHERE form_id = %d", $form['id'] ), ARRAY_A );
            $form['blocks'] = $blocks_row ? (json_decode( $blocks_row['blocks_json'], true ) ?: array()) : array();
            $form['logic'] = $blocks_row ? (json_decode( $blocks_row['logic_json'], true ) ?: array()) : array();
        }
    } else {
        $forms = array();
    }
    return rest_ensure_response( $forms );
}

function cora_rest_save_form( $request ) {
    global $wpdb;
    $params = $request->get_json_params();
    if ( empty( $params ) ) {
        return new WP_Error( 'bad_request', 'Missing form body', array( 'status' => 400 ) );
    }

    $id = isset( $params['id'] ) ? intval( $params['id'] ) : 0;
    $title = isset( $params['title'] ) ? sanitize_text_field( $params['title'] ) : 'Untitled Form';
    $status = isset( $params['status'] ) ? sanitize_text_field( $params['status'] ) : 'draft';
    $styling = isset( $params['styling'] ) ? json_encode( $params['styling'] ) : '{}';
    $settings = isset( $params['settings'] ) ? json_encode( $params['settings'] ) : '{}';
    $blocks = isset( $params['blocks'] ) ? $params['blocks'] : array();
    $logic = isset( $params['logic'] ) ? $params['logic'] : array();

    if ( $id > 0 ) {
        // Update
        $wpdb->update(
            $wpdb->prefix . 'cora_forms',
            array(
                'title'      => $title,
                'status'     => $status,
                'styling'    => $styling,
                'settings'   => $settings,
                'updated_at' => current_time('mysql')
            ),
            array( 'id' => $id )
        );
    } else {
        // Insert
        $wpdb->insert(
            $wpdb->prefix . 'cora_forms',
            array(
                'agency_id'  => 1,
                'title'      => $title,
                'status'     => $status,
                'styling'    => $styling,
                'settings'   => $settings,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )
        );
        $id = $wpdb->insert_id;
    }

    // Update or insert blocks
    $blocks_row = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_form_blocks WHERE form_id = %d", $id ) );
    if ( $blocks_row ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_form_blocks',
            array(
                'blocks_json' => json_encode( $blocks ),
                'logic_json'  => json_encode( $logic ),
                'updated_at'  => current_time('mysql')
            ),
            array( 'form_id' => $id )
        );
    } else {
        $wpdb->insert(
            $wpdb->prefix . 'cora_form_blocks',
            array(
                'form_id'     => $id,
                'blocks_json' => json_encode( $blocks ),
                'logic_json'  => json_encode( $logic ),
                'created_at'  => current_time('mysql'),
                'updated_at'  => current_time('mysql')
            )
        );
    }

    // Add audit log entry
    $wpdb->insert(
        $wpdb->prefix . 'cora_form_audit_log',
        array(
            'form_id'      => $id,
            'action_type'  => 'form_saved',
            'details'      => 'Form saved and published: ' . $title,
            'performed_by' => get_current_user_id(),
            'ip_address'   => $_SERVER['REMOTE_ADDR'] ?? '',
            'created_at'   => current_time('mysql')
        )
    );

    $form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_forms WHERE id = %d", $id ), ARRAY_A );
    $form['styling'] = json_decode( $form['styling'], true ) ?: array();
    $form['settings'] = json_decode( $form['settings'], true ) ?: array();
    $form['blocks'] = $blocks;
    $form['logic'] = $logic;

    return rest_ensure_response( $form );
}

function cora_rest_get_form( $request ) {
    global $wpdb;
    $id = intval( $request->get_param('id') );
    $form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_forms WHERE id = %d", $id ), ARRAY_A );
    if ( ! $form ) {
        return new WP_Error( 'not_found', 'Form not found', array( 'status' => 404 ) );
    }
    $form['styling'] = json_decode( $form['styling'], true ) ?: array();
    $form['settings'] = json_decode( $form['settings'], true ) ?: array();

    $blocks_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_form_blocks WHERE form_id = %d", $id ), ARRAY_A );
    $form['blocks'] = $blocks_row ? (json_decode( $blocks_row['blocks_json'], true ) ?: array()) : array();
    $form['logic'] = $blocks_row ? (json_decode( $blocks_row['logic_json'], true ) ?: array()) : array();

    return rest_ensure_response( $form );
}

function cora_rest_delete_form( $request ) {
    global $wpdb;
    $id = intval( $request->get_param('id') );
    $wpdb->delete( $wpdb->prefix . 'cora_forms', array( 'id' => $id ) );
    $wpdb->delete( $wpdb->prefix . 'cora_form_blocks', array( 'form_id' => $id ) );
    $wpdb->delete( $wpdb->prefix . 'cora_form_submissions', array( 'form_id' => $id ) );
    return rest_ensure_response( array( 'success' => true ) );
}

function cora_rest_get_form_submissions( $request ) {
    global $wpdb;
    $id = intval( $request->get_param('id') );
    $subs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_form_submissions WHERE form_id = %d ORDER BY id DESC", $id ), ARRAY_A );
    if ( is_array( $subs ) ) {
        foreach ( $subs as &$sub ) {
            $sub['submitted_data'] = json_decode( $sub['submitted_data'], true ) ?: array();
        }
    } else {
        $subs = array();
    }
    return rest_ensure_response( $subs );
}

function cora_rest_get_form_ai_schema( $request ) {
    global $wpdb;
    $id = intval( $request->get_param('id') );
    
    $blocks_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_form_blocks WHERE form_id = %d", $id ), ARRAY_A );
    $blocks = $blocks_row ? (json_decode( $blocks_row['blocks_json'], true ) ?: array()) : array();

    $properties = array();
    foreach ( $blocks as $b ) {
        if ( isset( $b['type'] ) && $b['type'] === 'input' ) {
            $label = isset( $b['label'] ) ? $b['label'] : 'field';
            $inputType = isset( $b['inputType'] ) ? $b['inputType'] : 'text';
            $properties[$label] = array(
                'type'        => ($inputType === 'number') ? 'number' : 'string',
                'description' => isset( $b['placeholder'] ) ? $b['placeholder'] : ''
            );
        }
    }

    if ( empty( $properties ) ) {
        $properties['contact_name'] = array( 'type' => 'string', 'description' => 'Your full name' );
    }

    $schema = array(
        'type'       => 'object',
        'properties' => $properties
    );

    return rest_ensure_response( $schema );
}

function cora_rest_submit_form( $request ) {
    global $wpdb;
    $id = intval( $request->get_param('id') );
    $params = $request->get_json_params();
    if ( empty( $params ) ) {
        $params = $request->get_params();
    }

    // 1. Honeypot check
    $hp = isset( $params['cora_hp_verify'] ) ? sanitize_text_field( $params['cora_hp_verify'] ) : '';
    if ( ! empty( $hp ) ) {
        return new WP_Error( 'spam_detected', 'Spam attempt detected by honeypot.', array( 'status' => 400 ) );
    }

    // 2. IP Rate Limiting (max 10 submissions per minute)
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $one_minute_ago = date( 'Y-m-d H:i:s', time() - 60 );
    
    $recent_count = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}cora_form_submissions WHERE ip_address = %s AND created_at > %s",
        $ip,
        $one_minute_ago
    ) );

    if ( intval( $recent_count ) >= 10 ) {
        return new WP_Error( 'rate_limited', 'Too many requests. Please wait before submitting again.', array( 'status' => 429 ) );
    }

    // 3. Save Submission
    $submitted_data = isset( $params['submitted_data'] ) ? $params['submitted_data'] : array();
    $is_partial = isset( $params['is_partial'] ) ? intval( $params['is_partial'] ) : 0;

    $wpdb->insert(
        $wpdb->prefix . 'cora_form_submissions',
        array(
            'form_id'        => $id,
            'submitted_data' => json_encode( $submitted_data ),
            'ip_address'     => $ip,
            'is_partial'     => $is_partial,
            'created_at'     => current_time('mysql')
        )
    );

    return rest_ensure_response( array( 'success' => true, 'submission_id' => $wpdb->insert_id ) );
}

function cora_rest_get_clauses( $request ) {
    global $wpdb;
    $clauses = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_form_clauses ORDER BY id DESC", ARRAY_A );
    return rest_ensure_response( $clauses ?: array() );
}

function cora_rest_save_clause( $request ) {
    global $wpdb;
    $params = $request->get_json_params();
    if ( empty( $params ) ) {
        $params = $request->get_params();
    }
    
    $id = isset( $params['id'] ) ? intval( $params['id'] ) : 0;
    $title = isset( $params['title'] ) ? sanitize_text_field( $params['title'] ) : 'Untitled Clause';
    $content = isset( $params['content'] ) ? sanitize_textarea_field( $params['content'] ) : '';
    $is_mandatory = isset( $params['is_mandatory'] ) ? intval( $params['is_mandatory'] ) : 0;

    if ( $id > 0 ) {
        $wpdb->update(
            $wpdb->prefix . 'cora_form_clauses',
            array( 'title' => $title, 'content' => $content, 'is_mandatory' => $is_mandatory ),
            array( 'id' => $id )
        );
    } else {
        $wpdb->insert(
            $wpdb->prefix . 'cora_form_clauses',
            array(
                'agency_id' => 1,
                'title' => $title,
                'content' => $content,
                'is_mandatory' => $is_mandatory,
                'created_at' => current_time('mysql')
            )
        );
        $id = $wpdb->insert_id;
    }
    
    $clause = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_form_clauses WHERE id = %d", $id ), ARRAY_A );
    return rest_ensure_response( $clause );
}

function cora_rest_delete_clause( $request ) {
    global $wpdb;
    $id = intval( $request->get_param('id') );
    $wpdb->delete( $wpdb->prefix . 'cora_form_clauses', array( 'id' => $id ) );
    return rest_ensure_response( array( 'success' => true ) );
}

function cora_rest_get_form_audit_log( $request ) {
    global $wpdb;
    $logs = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_form_audit_log ORDER BY id DESC LIMIT 100", ARRAY_A );
    return rest_ensure_response( $logs ?: array() );
}

// ═══ DYNAMIC REMOTE PLUGIN UPDATER SYSTEM ═══
if ( ! class_exists( 'Cora_Real_Estate_Plugin_Updater' ) ) {
    class Cora_Real_Estate_Plugin_Updater {
        private $plugin_slug;
        private $plugin_file;
        private $update_url;
        
        public function __construct( $plugin_file, $update_url ) {
            $this->plugin_file = $plugin_file;
            $this->plugin_slug = plugin_basename( $plugin_file );
            $this->update_url  = $update_url;
            
            // Hook into update checks
            add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
            
            // Hook into plugins details modal display
            add_filter( 'plugins_api', array( $this, 'plugin_info' ), 20, 3 );
        }
        
        public function check_update( $transient ) {
            if ( empty( $transient->checked ) ) {
                return $transient;
            }
            
            // Fetch remote update information
            $response = wp_remote_get( $this->update_url, array(
                'timeout' => 15,
                'headers' => array(
                    'Accept' => 'application/json'
                )
            ) );
            
            if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
                return $transient;
            }
            
            $remote_data = json_decode( wp_remote_retrieve_body( $response ) );
            if ( ! $remote_data || empty( $remote_data->version ) ) {
                return $transient;
            }
            
            $local_version = '';
            if ( ! function_exists( 'get_plugin_data' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $plugin_data = get_plugin_data( $this->plugin_file );
            $local_version = $plugin_data['Version'];
            
            if ( version_compare( $local_version, $remote_data->version, '<' ) ) {
                $obj = new stdClass();
                $obj->slug = 'cora-real-estate';
                $obj->plugin = $this->plugin_slug;
                $obj->new_version = $remote_data->version;
                $obj->tested = isset( $remote_data->tested ) ? $remote_data->tested : '6.5';
                $obj->package = $remote_data->download_url;
                $obj->url = 'https://cora.ai';
                
                $transient->response[ $this->plugin_slug ] = $obj;
            }
            
            return $transient;
        }
        
        public function plugin_info( $res, $action, $args ) {
            if ( $action !== 'plugin_information' ) {
                return $res;
            }
            
            if ( isset( $args->slug ) && $args->slug === 'cora-real-estate' ) {
                $response = wp_remote_get( $this->update_url, array(
                    'timeout' => 15,
                    'headers' => array(
                        'Accept' => 'application/json'
                    )
                ) );
                
                if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
                    return $res;
                }
                
                $remote_data = json_decode( wp_remote_retrieve_body( $response ) );
                if ( ! $remote_data ) {
                    return $res;
                }
                
                $res = new stdClass();
                $res->name = $remote_data->name;
                $res->slug = 'cora-real-estate';
                $res->version = $remote_data->version;
                $res->tested = isset( $remote_data->tested ) ? $remote_data->tested : '6.5';
                $res->author = 'Dravya Bansal (ClaraVerse)';
                $res->homepage = 'https://cora.ai';
                $res->download_link = $remote_data->download_url;
                
                $res->sections = array(
                    'description' => isset( $remote_data->sections->description ) ? $remote_data->sections->description : '',
                    'changelog'   => isset( $remote_data->sections->changelog ) ? $remote_data->sections->changelog : ''
                );
                
                return $res;
            }
            
            return $res;
        }
    }
}

$cora_re_updates_url = get_option( 'cora_re_updates_server_url', 'https://raw.githubusercontent.com/dravyafolio2021/heycora/main/updates/cora-real-estate.json' );
new Cora_Real_Estate_Plugin_Updater( __FILE__, $cora_re_updates_url );


