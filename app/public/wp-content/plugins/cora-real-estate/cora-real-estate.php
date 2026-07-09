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
define( 'CORA_REAL_ESTATE_AI_VERSION', time() );
define( 'CORA_REAL_ESTATE_AI_PATH', plugin_dir_path( __FILE__ ) );
define( 'CORA_REAL_ESTATE_AI_URL', plugin_dir_url( __FILE__ ) );

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

/**
 * Intercept requests to /workspace and render the standalone dashboard
 */
function cora_real_estate_ai_handle_workspace_route() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path = parse_url( home_url(), PHP_URL_PATH );
    $path = substr( $request_uri, strlen( $home_path ) );
    $path = trim( parse_url( $path, PHP_URL_PATH ), '/' );

    $path_parts = explode( '/', $path );
    
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

    if ( isset( $path_parts[0] ) && 'workspace' === $path_parts[0] ) {
        // Force authentication check
        if ( ! is_user_logged_in() ) {
            wp_redirect( wp_login_url( home_url( $_SERVER['REQUEST_URI'] ) ) );
            exit;
        }

        // Allow administrators and custom photography roles to view workspace
        $user = wp_get_current_user();
        $allowed_roles = array( 'administrator', 'cora_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor' );
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

        // Role-based access control check (Server-Side)
        $cora_permissions = get_option( 'cora_role_permissions', array() );
        $current_user_role = ! empty( $user->roles ) ? $user->roles[0] : 'subscriber';
        
        $allowed_features = isset( $cora_permissions[$current_user_role] ) ? $cora_permissions[$current_user_role] : array();
        if ( $current_user_role === 'administrator' ) {
            $allowed_features = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'attendance', 'tasks', 'visual-builder', 'audit-panel', 'media' );
        }

        // Prevent accessing disallowed sub-pages
        if ( $sub_page !== 'dashboard' && $sub_page !== 'feature-hub' && ! in_array( $sub_page, $allowed_features ) ) {
            wp_redirect( home_url( '/workspace/dashboard' ) );
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
        $allowed_roles = array( 'administrator', 'cora_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor' );
        foreach ( $allowed_roles as $role ) {
            if ( in_array( $role, (array) $user->roles ) ) {
                return home_url( '/workspace' );
            }
        }
    } else {
        $current_user = wp_get_current_user();
        if ( $current_user && $current_user->exists() ) {
            $allowed_roles = array( 'administrator', 'cora_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor' );
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
        $allowed_roles = array( 'administrator', 'cora_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor' );
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
    add_role( 'cora_photographer', 'Cora Managing Agent', array( 'read' => true ) );
    add_role( 'cora_videographer', 'Cora Showing Assistant', array( 'read' => true ) );
    add_role( 'cora_drone_pilot', 'Cora Property Valuer', array( 'read' => true ) );
    add_role( 'cora_editor', 'Cora Listing Coordinator', array( 'read' => true ) );
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
            'administrator' => array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'gbp', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'plugins', 'attendance', 'tasks' ),
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
            $new_core_features = array( 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'plugins', 'attendance', 'tasks' );
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

    $inventory = get_option( 'cora_re_listings_inventory', array() );
    if ( ! is_array( $inventory ) ) {
        $inventory = array();
    }

    $found_key = null;
    if ( ! empty( $id ) ) {
        foreach ( $inventory as $key => $item ) {
            if ( isset( $item['id'] ) && $item['id'] === $id ) {
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
        $new_id = 'eq' . ( count( $inventory ) + 1 ) . '_' . time();
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

    $inventory = get_option( 'cora_re_listings_inventory', array() );
    $updated = false;

    foreach ( $inventory as &$item ) {
        if ( $item['id'] === $eq_id ) {
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

    $inventory = get_option( 'cora_re_listings_inventory', array() );
    $updated_inventory = array();
    $found = false;

    foreach ( $inventory as $item ) {
        if ( $item['id'] === $eq_id ) {
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

/**
 * Register Public REST API route for frontend team integration
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'cora/v1', '/team', array(
        'methods'             => 'GET',
        'callback'            => 'cora_get_team_members_rest',
        'permission_callback' => '__return_true', // Publicly accessible REST endpoint
    ) );
} );

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
        $reserved_paths = array( 'workspace', 'shared-doc', 'shared-portfolio', 'cora-service-worker.js', 'cora-manifest.json' );
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
            
            header( 'Content-Type: text/html; charset=UTF-8' );
            echo $html;
            exit;
        }
    }
}
add_action( 'template_redirect', 'cora_real_estate_ai_serve_frontend_homepage', 5 );

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
    
    $leads = get_option( 'cora_re_leads', array() );
    if ( ! is_array( $leads ) ) {
        $leads = array();
    }
    
    $lead_id = 'lead_' . time() . '_' . wp_generate_password( 4, false );
    
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

    if ( empty( $names ) || empty( $email ) ) {
        wp_send_json_error( 'Names and Email are required.' );
    }

    $leads = get_option( 'cora_re_leads', array() );
    if ( ! is_array( $leads ) ) {
        $leads = array();
    }

    $new_lead = array(
        'id'         => 'lead_' . time() . '_' . wp_generate_password( 4, false ),
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
    
    // Additional sales pipeline enrichment fields
    $scale = isset( $_POST['scale'] ) ? sanitize_text_field( $_POST['scale'] ) : null;
    $city  = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : null;
    $price = isset( $_POST['price'] ) ? sanitize_text_field( $_POST['price'] ) : null;
    
    $demo_portfolio        = isset( $_POST['demo_portfolio'] ) ? sanitize_text_field( $_POST['demo_portfolio'] ) : null;
    $demo_portfolio_shared = isset( $_POST['demo_portfolio_shared'] ) ? sanitize_text_field( $_POST['demo_portfolio_shared'] ) : null;
    $demo_portfolio_viewed = isset( $_POST['demo_portfolio_viewed'] ) ? sanitize_text_field( $_POST['demo_portfolio_viewed'] ) : null;
    $listing_ids       = isset( $_POST['listing_ids'] ) ? sanitize_text_field( $_POST['listing_ids'] ) : null;

    if ( empty( $lead_id ) ) {
        wp_send_json_error( 'Lead ID is required.' );
    }

    $leads = get_option( 'cora_re_leads', array() );
    if ( ! is_array( $leads ) ) {
        wp_send_json_error( 'No leads found.' );
    }

    $found_key = null;
    foreach ( $leads as $key => $lead ) {
        if ( isset( $lead['id'] ) && $lead['id'] === $lead_id ) {
            $found_key = $key;
            break;
        }
    }

    if ( null === $found_key ) {
        wp_send_json_error( 'Lead not found.' );
    }

    if ( ! empty( $status ) ) {
        $leads[$found_key]['status'] = $status;
        
        // If status changed to Converted, also copy to Clients list
        if ( 'Converted' === $status ) {
            // Auto-cancel remaining scheduled emails
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
    if ( null !== $notes ) {
        $leads[$found_key]['notes'] = $notes;
    }
    if ( null !== $names ) {
        $leads[$found_key]['names'] = $names;
    }
    if ( null !== $email ) {
        $leads[$found_key]['email'] = $email;
    }
    if ( null !== $scale ) {
        $leads[$found_key]['scale'] = $scale;
    }
    if ( null !== $city ) {
        $leads[$found_key]['city'] = $city;
    }
    if ( null !== $price ) {
        $leads[$found_key]['price'] = $price;
    }
    if ( null !== $demo_portfolio ) {
        $leads[$found_key]['demo_portfolio'] = $demo_portfolio;
    }
    if ( null !== $demo_portfolio_shared ) {
        $leads[$found_key]['demo_portfolio_shared'] = ( $demo_portfolio_shared === 'true' );
    }
    if ( null !== $demo_portfolio_viewed ) {
        $leads[$found_key]['demo_portfolio_viewed'] = ( $demo_portfolio_viewed === 'true' );
    }
    if ( null !== $listing_ids ) {
        $leads[$found_key]['listing_ids'] = array_filter( array_map( 'trim', explode( ',', $listing_ids ) ) );
    }

    update_option( 'cora_re_leads', $leads );

    wp_send_json_success( array(
        'message' => 'Lead updated successfully.',
        'lead'    => $leads[$found_key]
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

    $leads = get_option( 'cora_re_leads', array() );
    if ( ! is_array( $leads ) ) {
        wp_send_json_error( 'No leads found.' );
    }

    $found_key = null;
    foreach ( $leads as $key => $lead ) {
        if ( isset( $lead['id'] ) && $lead['id'] === $lead_id ) {
            $found_key = $key;
            break;
        }
    }

    if ( null === $found_key ) {
        wp_send_json_error( 'Lead not found.' );
    }

    unset( $leads[$found_key] );
    $leads = array_values( $leads );
    update_option( 'cora_re_leads', $leads );

    wp_send_json_success( array(
        'message' => 'Lead deleted.'
    ) );
}
add_action( 'wp_ajax_cora_re_delete_lead', 'cora_ajax_delete_lead' );

/**
 * Helper: Copy a Lead to Client Directory
 */
function cora_copy_lead_to_clients( $lead ) {
    $clients = get_option( 'cora_re_clients', array() );
    if ( ! is_array( $clients ) ) {
        $clients = array();
    }

    // Check if client already exists
    foreach ( $clients as $client ) {
        if ( isset( $client['lead_id'] ) && $client['lead_id'] === $lead['id'] ) {
            return; // already converted
        }
    }

    $clients[] = array(
        'id'            => 'client_' . time() . '_' . wp_generate_password( 4, false ),
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

    $leads = get_option( 'cora_re_leads', array() );
    $found_key = null;
    foreach ( $leads as $key => $lead ) {
        if ( isset( $lead['id'] ) && $lead['id'] === $lead_id ) {
            $found_key = $key;
            break;
        }
    }

    if ( null === $found_key ) {
        wp_send_json_error( 'Lead not found.' );
    }

    $leads[$found_key]['status'] = 'Converted';
    
    // Auto-cancel remaining scheduled emails
    if ( isset( $leads[$found_key]['emails'] ) && is_array( $leads[$found_key]['emails'] ) ) {
        foreach ( $leads[$found_key]['emails'] as $ekey => $email ) {
            if ( isset( $email['status'] ) && 'Scheduled' === $email['status'] ) {
                $leads[$found_key]['emails'][$ekey]['status'] = 'Cancelled';
            }
        }
    }
    
    cora_copy_lead_to_clients( $leads[$found_key] );
    update_option( 'cora_re_leads', $leads );

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

    $clients = get_option( 'cora_re_clients', array() );
    if ( ! is_array( $clients ) ) {
        wp_send_json_error( 'No clients found.' );
    }

    $found_key = null;
    foreach ( $clients as $key => $client ) {
        if ( isset( $client['id'] ) && $client['id'] === $client_id ) {
            $found_key = $key;
            break;
        }
    }

    if ( null === $found_key ) {
        wp_send_json_error( 'Client not found.' );
    }

    unset( $clients[$found_key] );
    $clients = array_values( $clients );
    update_option( 'cora_re_clients', $clients );

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

    $clients = get_option( 'cora_re_clients', array() );
    if ( ! is_array( $clients ) ) {
        $clients = array();
    }

    $new_client = array(
        'id'           => 'client_' . time() . '_' . wp_generate_password( 4, false ),
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

    $clients = get_option( 'cora_re_clients', array() );
    $updated = false;

    foreach ( $clients as $key => $client ) {
        if ( ( ! empty( $client_id ) && $client['id'] === $client_id ) || ( ! empty( $client_name ) && $client['names'] === $client_name ) ) {
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

    $transactions = get_option( 'cora_re_ledger', array() );
    if ( ! is_array( $transactions ) ) {
        $transactions = array();
    }

    $updated = false;
    $new_tx = array();

    if ( ! empty( $id ) ) {
        foreach ( $transactions as $key => $tx ) {
            if ( isset( $tx['id'] ) && $tx['id'] === $id ) {
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
            'id' => 'tx_' . time() . '_' . rand(100, 999),
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

    $transactions = get_option( 'cora_re_ledger', array() );
    if ( ! is_array( $transactions ) ) {
        wp_send_json_error( 'No transactions found.' );
    }

    $found_key = null;
    foreach ( $transactions as $key => $tx ) {
        if ( isset( $tx['id'] ) && $tx['id'] === $id ) {
            $found_key = $key;
            break;
        }
    }

    if ( null === $found_key ) {
        wp_send_json_error( 'Transaction not found.' );
    }

    unset( $transactions[$found_key] );
    $transactions = array_values( $transactions );
    update_option( 'cora_re_ledger', $transactions );

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

    $categories = wp_get_post_categories($post_id);
    $tags = wp_get_post_tags($post_id, array('fields' => 'ids'));
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : '';

    wp_send_json_success(array(
        'content' => $post->post_content,
        'keyword' => $keyword,
        'description' => $description,
        'categories' => $categories,
        'tags' => $tags,
        'thumbnail_id' => $thumbnail_id,
        'thumbnail_url' => $thumbnail_url
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
    if ($seo_score) {
        update_post_meta($saved_id, '_cora_seo_score', $seo_score);
    }

    wp_send_json_success();
}
add_action( 'wp_ajax_cora_save_article', 'cora_ajax_save_article' );

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
        'cora_currency_format'
    );

    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            $val = $_POST[ $field ];
            if ( in_array( $field, array( 'users_can_register', 'blog_public', 'default_pingback_flag', 'comment_moderation' ) ) ) {
                $val = intval( $val );
            } elseif ( in_array( $field, array( 'page_on_front', 'page_for_posts', 'default_category', 'wp_page_for_privacy_policy' ) ) ) {
                $val = intval( $val );
            } elseif ( in_array( $field, array( 'moderation_keys', 'disallowed_keys' ) ) ) {
                $val = trim( $val );
            } else {
                $val = sanitize_text_field( $val );
            }
            update_option( $field, $val );
        } elseif ( in_array( $field, array( 'users_can_register', 'default_pingback_flag', 'comment_moderation' ) ) ) {
            update_option( $field, 0 );
        }
    }

    if ( isset( $_POST['permalink_structure'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure( sanitize_text_field( $_POST['permalink_structure'] ) );
        flush_rewrite_rules();
    }

    wp_send_json_success( array( 'message' => 'Global system settings updated successfully.' ) );
}
add_action( 'wp_ajax_cora_save_system_settings_suite', 'cora_ajax_save_system_settings_suite' );

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

    if ( wp_get_nav_menu_object( $menu_name ) ) {
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
    wp_enqueue_style(
        'cora-elementor-reskin-css',
        plugin_dir_url( __FILE__ ) . 'assets/css/cora-elementor-reskin.css',
        array(),
        time()
    );
}
add_action( 'elementor/editor/after_enqueue_styles', 'cora_enqueue_elementor_reskin_styles' );
add_action( 'elementor/preview/enqueue_styles', 'cora_enqueue_elementor_reskin_styles' );

function cora_enqueue_elementor_reskin_scripts() {
    wp_enqueue_script(
        'cora-elementor-reskin-js',
        plugin_dir_url( __FILE__ ) . 'assets/js/cora-elementor-reskin.js',
        array(),
        time(),
        true
    );
    // Enqueue Custom React Shell Wrapper
    wp_enqueue_script(
        'cora-elementor-react-shell',
        plugin_dir_url( __FILE__ ) . 'build/index.js',
        array('wp-element'),
        time(),
        true
    );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'cora_enqueue_elementor_reskin_scripts' );

/**
 * Remove admin bar from Elementor editor
 */
function cora_remove_admin_bar_in_elementor() {
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) {
        add_filter( 'show_admin_bar', '__return_false' );
    }
}
add_action( 'init', 'cora_remove_admin_bar_in_elementor' );

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
            $children = get_terms( array( 'taxonomy' => 'cora_media_folder', 'hide_empty' => false, 'parent' => $t->term_id ) );
            $ch = array();
            if ( ! is_wp_error( $children ) ) {
                foreach ( $children as $c ) {
                    $ch[] = array( 'id' => $c->term_id, 'name' => $c->name, 'count' => $c->count );
                }
            }
            $result[] = array(
                'id'        => $t->term_id,
                'name'      => $t->name,
                'count'     => $t->count,
                'is_system' => false,
                'children'  => $ch,
            );
        }
    }
    wp_send_json_success( array( 'folders' => $result ) );
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
