<?php

/**
 * Plugin Name: Cora for Studio
 * Plugin URI: https://cora.ai
 * Description: The ultimate AI workspace and CRM for modern photography studios. Features smart shoot bookings, WhatsApp integrations, AI image culling, financial tracking, Google Business Profile management, and real-time attendance logging.
  * Version: 1.2.0
 * Author: Dravya Bansal (ClaraVerse)
 * Author URI: https://cora.ai
 * License: GPL2
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants
define( 'CORA_STUDIO_AI_VERSION', time() );
define( 'CORA_STUDIO_AI_PATH', plugin_dir_path( __FILE__ ) );
define( 'CORA_STUDIO_AI_URL', plugin_dir_url( __FILE__ ) );

/**
 * Add the admin menu page
 */
function cora_studio_ai_admin_menu() {
    add_menu_page(
        __( 'Cora for Studio', 'cora-studio-ai' ),
        __( 'Cora AI', 'cora-studio-ai' ),
        'manage_options',
        'cora-studio-ai',
        'cora_studio_ai_render_dashboard',
        'dashicons-superhero', // Custom icon placeholder
        2 // High position in the sidebar
    );
}
add_action( 'admin_menu', 'cora_studio_ai_admin_menu' );

/**
 * Render the dashboard page
 */
/**
 * Render the dashboard page (redirects to the standalone workspace URL)
 */
function cora_studio_ai_render_dashboard() {
    wp_redirect( home_url( '/workspace' ) );
    exit;
}
add_action( 'admin_menu', 'cora_studio_ai_admin_menu' );

/**
 * Intercept requests to /workspace and render the standalone dashboard
 */
function cora_studio_ai_handle_workspace_route() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path = parse_url( home_url(), PHP_URL_PATH );
    $path = substr( $request_uri, strlen( $home_path ) );
    $path = trim( parse_url( $path, PHP_URL_PATH ), '/' );

    $path_parts = explode( '/', $path );


    if ( isset( $path_parts[0] ) && 'shared-doc' === $path_parts[0] ) {
        $hash = isset( $path_parts[1] ) ? sanitize_text_field( $path_parts[1] ) : '';
        if ( ! empty( $hash ) ) {
            $documents = get_option( 'cora_studio_documents', array() );
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
                include CORA_STUDIO_AI_PATH . 'public-doc-view.php';
                exit;
            }
        }
        wp_die( __( 'Invalid or expired secure document link.', 'cora-studio-ai' ), __( 'Access Denied', 'cora-studio-ai' ), array( 'response' => 403 ) );
    }

    if ( isset( $path_parts[0] ) && 'shared-gallery' === $path_parts[0] ) {
        $hash = isset( $path_parts[1] ) ? sanitize_text_field( $path_parts[1] ) : '';
        if ( ! empty( $hash ) ) {
            $galleries = get_option( 'cora_studio_galleries', array() );
            $found_gallery = null;
            if ( is_array( $galleries ) ) {
                foreach ( $galleries as $gallery ) {
                    if ( isset( $gallery['hash'] ) && $gallery['hash'] === $hash ) {
                        $found_gallery = $gallery;
                        break;
                    }
                }
            }

            if ( $found_gallery ) {
                nocache_headers();
                include CORA_STUDIO_AI_PATH . 'public-gallery-view.php';
                exit;
            }
        }
        wp_die( __( 'Invalid or secure gallery link.', 'cora-studio-ai' ), __( 'Access Denied', 'cora-studio-ai' ), array( 'response' => 403 ) );
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
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'cora-studio-ai' ) );
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
            $allowed_features = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'gallery', 'leads', 'clients', 'blogs', 'gbp', 'plugins', 'my-profile' );
        }
        // My Profile is accessible by all logged-in users
        if ( ! in_array( 'my-profile', $allowed_features ) ) {
            $allowed_features[] = 'my-profile';
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
        include CORA_STUDIO_AI_PATH . 'admin-dashboard.php';
        exit;
    }
}
add_action( 'template_redirect', 'cora_studio_ai_handle_workspace_route' );

/**
 * Enqueue scripts and styles only on our admin page
 */
function cora_studio_ai_admin_assets( $hook ) {
    // Only load on our plugin page
    if ( 'toplevel_page_cora-studio-ai' !== $hook ) {
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
        'cora-admin-style',
        CORA_STUDIO_AI_URL . 'assets/css/admin-style.css',
        array(),
        CORA_STUDIO_AI_VERSION
    );

    // Enqueue scripts
    wp_enqueue_script(
        'cora-admin-script',
        CORA_STUDIO_AI_URL . 'assets/js/admin-script.js',
        array( 'jquery' ),
        CORA_STUDIO_AI_VERSION,
        true // Load in footer
    );

    // Enqueue WordPress media libraries for logo upload
    wp_enqueue_media();

    // Localize script to pass server variables if needed (e.g. site URL, ajaxurl)
    wp_localize_script( 'cora-admin-script', 'coraWPData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'siteUrl' => get_site_url(),
        'restUrl' => esc_url_raw( rest_url() ),
        'nonce'   => wp_create_nonce( 'wp_rest' ),
        'ajaxNonce' => wp_create_nonce( 'cora_ajax_nonce' ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'cora_studio_ai_admin_assets' );

/**
 * Redirect custom studio roles and administrators to our custom dashboard after login
 */
function cora_studio_ai_login_redirect( $redirect_to, $request, $user ) {
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
add_filter( 'login_redirect', 'cora_studio_ai_login_redirect', 10, 3 );

/**
 * Handle direct login event redirect
 */
function cora_studio_ai_on_wp_login( $user_login, $user ) {
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
add_action( 'wp_login', 'cora_studio_ai_on_wp_login', 10, 2 );

/**
 * Restrict non-administrators from accessing the default WP Admin backend entirely
 */
function cora_studio_ai_restrict_admin_access() {
    if ( is_admin() && ! current_user_can( 'manage_options' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url( '/workspace' ) );
        exit;
    }
}
add_action( 'admin_init', 'cora_studio_ai_restrict_admin_access' );

/**
 * Force users_can_register option to enable user signups
 */
function cora_studio_ai_users_can_register( $value ) {
    return 1;
}
add_filter( 'option_users_can_register', 'cora_studio_ai_users_can_register' );

/**
 * Render custom fields on the WordPress registration form
 */
function cora_studio_ai_register_form() {
    $studio_name = ( ! empty( $_POST['cora_studio_name'] ) ) ? sanitize_text_field( $_POST['cora_studio_name'] ) : '';
    $phone = ( ! empty( $_POST['cora_phone'] ) ) ? sanitize_text_field( $_POST['cora_phone'] ) : '';
    $role = ( ! empty( $_POST['cora_role'] ) ) ? sanitize_text_field( $_POST['cora_role'] ) : 'cora_manager';
    ?>
    <p>
        <label for="cora_password"><?php _e( 'Password', 'cora-studio-ai' ); ?><br />
        <input type="password" name="cora_password" id="cora_password" class="input" value="" size="25" autocomplete="new-password" /></label>
    </p>
    <p>
        <label for="cora_confirm_password"><?php _e( 'Confirm Password', 'cora-studio-ai' ); ?><br />
        <input type="password" name="cora_confirm_password" id="cora_confirm_password" class="input" value="" size="25" autocomplete="new-password" /></label>
    </p>
    <p>
        <label for="cora_studio_name"><?php _e( 'Studio Name', 'cora-studio-ai' ); ?><br />
        <input type="text" name="cora_studio_name" id="cora_studio_name" class="input" value="<?php echo esc_attr( $studio_name ); ?>" size="25" /></label>
    </p>
    <p>
        <label for="cora_phone"><?php _e( 'Phone Number', 'cora-studio-ai' ); ?><br />
        <input type="text" name="cora_phone" id="cora_phone" class="input" value="<?php echo esc_attr( $phone ); ?>" size="25" /></label>
    </p>
    <p>
        <label for="cora_role"><?php _e( 'Studio Role', 'cora-studio-ai' ); ?><br />
        <select name="cora_role" id="cora_role" class="input" style="width: 100%; height: 40px; margin-top: 2px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.08); border-radius: 6px; background: #fafafa; font-family: inherit; font-size: 14px; padding: 0 10px;">
            <option value="cora_manager" <?php selected( $role, 'cora_manager' ); ?>>Manager</option>
            <option value="cora_photographer" <?php selected( $role, 'cora_photographer' ); ?>>Photographer</option>
            <option value="cora_videographer" <?php selected( $role, 'cora_videographer' ); ?>>Videographer</option>
            <option value="cora_drone_pilot" <?php selected( $role, 'cora_drone_pilot' ); ?>>Drone Pilot</option>
            <option value="cora_editor" <?php selected( $role, 'cora_editor' ); ?>>Editor</option>
        </select></label>
    </p>
    <?php
}
add_action( 'register_form', 'cora_studio_ai_register_form' );

/**
 * Validate custom fields on registration submission
 */
function cora_studio_ai_registration_errors( $errors, $sanitized_user_login, $user_email ) {
    if ( empty( $_POST['cora_password'] ) ) {
        $errors->add( 'cora_password_error', __( '<strong>Error:</strong> Password is required.', 'cora-studio-ai' ) );
    } elseif ( strlen( $_POST['cora_password'] ) < 6 ) {
        $errors->add( 'cora_password_length_error', __( '<strong>Error:</strong> Password must be at least 6 characters.', 'cora-studio-ai' ) );
    }
    
    if ( ! empty( $_POST['cora_password'] ) && $_POST['cora_password'] !== $_POST['cora_confirm_password'] ) {
        $errors->add( 'cora_password_mismatch_error', __( '<strong>Error:</strong> Passwords do not match.', 'cora-studio-ai' ) );
    }
    
    if ( empty( $_POST['cora_studio_name'] ) ) {
        $errors->add( 'cora_studio_name_error', __( '<strong>Error:</strong> Studio Name is required.', 'cora-studio-ai' ) );
    }
    
    return $errors;
}
add_filter( 'registration_errors', 'cora_studio_ai_registration_errors', 10, 3 );

/**
 * Handle custom user data saving and verification token delivery
 */
function cora_studio_ai_user_register( $user_id ) {
    // 1. Save password directly (overrides default random password generation)
    if ( ! empty( $_POST['cora_password'] ) ) {
        wp_set_password( $_POST['cora_password'], $user_id );
    }
    
    // 2. Save custom profile meta
    if ( ! empty( $_POST['cora_studio_name'] ) ) {
        update_user_meta( $user_id, 'cora_studio_name', sanitize_text_field( $_POST['cora_studio_name'] ) );
    }
    if ( ! empty( $_POST['cora_phone'] ) ) {
        update_user_meta( $user_id, 'cora_phone', sanitize_text_field( $_POST['cora_phone'] ) );
    }
    
    // 3. Save verification info
    $token = bin2hex( random_bytes( 16 ) );
    update_user_meta( $user_id, 'cora_email_verified', '0' );
    update_user_meta( $user_id, 'cora_verification_token', $token );
    
    // 4. Update role to selected studio role
    $role = ( ! empty( $_POST['cora_role'] ) ) ? sanitize_text_field( $_POST['cora_role'] ) : 'cora_manager';
    $user = get_user_by( 'id', $user_id );
    if ( $user ) {
        $user->set_role( $role );
    }
    
    // 5. Send automated confirmation email
    cora_send_verification_email( $user_id );
}
add_action( 'user_register', 'cora_studio_ai_user_register' );

/**
 * Dispatch verification link via wp_mail to the newly registered user
 */
function cora_send_verification_email( $user_id ) {
    $user = get_user_by( 'id', $user_id );
    if ( ! $user ) {
        return false;
    }
    
    $token = get_user_meta( $user_id, 'cora_verification_token', true );
    if ( ! $token ) {
        // Generate new token if missing
        $token = bin2hex( random_bytes( 16 ) );
        update_user_meta( $user_id, 'cora_verification_token', $token );
    }
    
    $verify_url = add_query_arg(
        array(
            'cora_verify_token' => $token,
            'cora_user_id'      => $user_id,
        ),
        home_url( '/workspace' )
    );
    
    $to = $user->user_email;
    $subject = 'Activate your Cora for Studio Workspace';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    $studio_name = get_option( 'cora_workspace_name', 'Cora for Studio' );
    $current_user = wp_get_current_user();
    $admin_name = $current_user->exists() ? $current_user->display_name : '';
    
    if ( ! empty( $admin_name ) ) {
        $from_name = $admin_name . ' via ' . $studio_name;
        $invitation_text = esc_html( $admin_name ) . ' has invited you to join the <strong>' . esc_html( $studio_name ) . '</strong> workspace on Cora.';
    } else {
        $from_name = $studio_name;
        $invitation_text = 'Welcome to <strong>' . esc_html( $studio_name ) . '</strong>! Please verify your email address to unlock your photography CRM dashboard, automated WhatsApp pipelines, and AI caption culling engines.';
    }
    
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
            <div class="logo">' . esc_html( $studio_name ) . '</div>
            <h2>Confirm your workspace registration</h2>
            <p>' . $invitation_text . '</p>
            <p><a href="' . esc_url( $verify_url ) . '" class="btn" style="color:#ffffff;">Verify Email Address</a></p>
            <p class="footer">If you did not request this account, please ignore this email.</p>
        </div>
    </body>
    </html>
    ';
    
    // Add temporary filter for custom From Name
    $from_name_filter = function() use ( $from_name ) {
        return $from_name;
    };
    add_filter( 'wp_mail_from_name', $from_name_filter );

    $result_mail = wp_mail( $to, $subject, $message, $headers );

    // Clean up filter
    remove_filter( 'wp_mail_from_name', $from_name_filter );
    
    return $result_mail;
}

/**
 * Catch verification token from URL
 */
function cora_studio_ai_handle_email_verification() {
    if ( ! empty( $_GET['cora_verify_token'] ) && ! empty( $_GET['cora_user_id'] ) ) {
        $user_id = intval( $_GET['cora_user_id'] );
        $url_token = sanitize_text_field( $_GET['cora_verify_token'] );
        
        $saved_token = get_user_meta( $user_id, 'cora_verification_token', true );
        
        if ( $saved_token && $saved_token === $url_token ) {
            update_user_meta( $user_id, 'cora_email_verified', '1' );
            delete_user_meta( $user_id, 'cora_verification_token' );
            
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
add_action( 'init', 'cora_studio_ai_handle_email_verification' );

/**
 * AJAX handler for resending verification email
 */
function cora_ajax_resend_verification() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( array( 'message' => 'User not logged in.' ) );
    }
    
    $verified = get_user_meta( $user_id, 'cora_email_verified', true );
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
add_action( 'wp_ajax_cora_resend_verification', 'cora_ajax_resend_verification' );

/**
 * Load custom login stylesheet for white-labeled login screen
 */
function cora_studio_ai_login_assets() {
    // Load Inter Font on Login page
    wp_enqueue_style(
        'cora-login-font-inter',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'cora-login-style',
        CORA_STUDIO_AI_URL . 'assets/css/login-style.css',
        array(),
        CORA_STUDIO_AI_VERSION
    );
}
add_action( 'login_enqueue_scripts', 'cora_studio_ai_login_assets' );

/**
 * Change the login logo URL to point to home instead of wordpress.org
 */
function cora_studio_ai_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'cora_studio_ai_login_logo_url' );

/**
 * Change the login logo title attribute (tooltip)
 */
function cora_studio_ai_login_logo_title() {
    return __( 'Cora for Studio', 'cora-studio-ai' );
}
add_filter( 'login_headertext', 'cora_studio_ai_login_logo_title' );

/**
 * Redirect default WP Dashboard index page (wp-admin/index.php) to Cora for Studio
 */
function cora_studio_ai_dashboard_redirect() {
    global $pagenow;
    if ( 'index.php' === $pagenow && ! isset( $_GET['page'] ) && ! isset( $_GET['no_cora_redirect'] ) ) {
        wp_redirect( home_url( '/workspace' ) );
        exit;
    }
}
add_action( 'admin_init', 'cora_studio_ai_dashboard_redirect' );

/**
 * Remove WordPress Logo node from global Admin Bar
 */
function cora_studio_ai_remove_wp_logo() {
    global $wp_admin_bar;
    if ( is_object( $wp_admin_bar ) ) {
        $wp_admin_bar->remove_node( 'wp-logo' );
    }
}
add_action( 'wp_before_admin_bar_render', 'cora_studio_ai_remove_wp_logo', 999 );

/**
 * Replace WordPress admin footer credits with Cora branding
 */
function cora_studio_ai_footer_text() {
    return '<span>Cora for Studio • Delhi Studio</span>';
}
add_filter( 'admin_footer_text', 'cora_studio_ai_footer_text', 999 );

/**
 * Replace WordPress version in footer with Cora build version
 */
function cora_studio_ai_footer_version() {
    return 'v2.0.0';
}
add_filter( 'update_footer', 'cora_studio_ai_footer_version', 999 );

/**
 * Register custom taxonomies for media
 */
function cora_studio_ai_register_taxonomies() {
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
add_action( 'init', 'cora_studio_ai_register_taxonomies' );

/**
 * Register photography-specific user roles for Indian/Global studios
 */
function cora_studio_ai_register_roles() {
    add_role( 'cora_manager', 'Cora Manager', array( 'read' => true ) );
    add_role( 'cora_photographer', 'Cora Photographer', array( 'read' => true ) );
    add_role( 'cora_videographer', 'Cora Videographer', array( 'read' => true ) );
    add_role( 'cora_drone_pilot', 'Cora Drone Pilot', array( 'read' => true ) );
    add_role( 'cora_editor', 'Cora Editor', array( 'read' => true ) );
}
add_action( 'init', 'cora_studio_ai_register_roles' );

/**
 * Seed initial dashboard options data: crew users, permissions, and equipment inventory
 */
function cora_studio_ai_seed_data() {
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

    if ( ! get_option( 'cora_equipment_inventory' ) ) {
        $equipment = array(
            array( 'id' => 'eq1', 'name' => 'Sony A7IV Body', 'category' => 'Camera', 'serial' => 'SN-87429103', 'status' => 'Available', 'crew' => '', 'shoot' => '' ),
            array( 'id' => 'eq2', 'name' => 'Sony 24-70mm f/2.8 GM II', 'category' => 'Lens', 'serial' => 'SN-32948172', 'status' => 'Available', 'crew' => '', 'shoot' => '' ),
            array( 'id' => 'eq3', 'name' => 'DJI Mavic 3 Pro', 'category' => 'Drone', 'serial' => 'SN-90182471', 'status' => 'Available', 'crew' => '', 'shoot' => '' ),
            array( 'id' => 'eq4', 'name' => 'DJI Ronin RS3 Pro', 'category' => 'Gimbal', 'serial' => 'SN-44910283', 'status' => 'Available', 'crew' => '', 'shoot' => '' ),
            array( 'id' => 'eq5', 'name' => 'Aputure 600d Pro Light', 'category' => 'Light', 'serial' => 'SN-10928374', 'status' => 'Available', 'crew' => '', 'shoot' => '' )
        );
        update_option( 'cora_equipment_inventory', $equipment );
    } else {
        // Dynamic cleanup: reset any equipment currently assigned to dummy users
        $equipment = get_option( 'cora_equipment_inventory', array() );
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
            update_option( 'cora_equipment_inventory', $equipment );
        }
    }

    if ( ! get_option( 'cora_role_permissions' ) ) {
        $default_permissions = array(
            'administrator' => array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'gallery', 'leads', 'clients', 'gbp' ),
            'cora_manager' => array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'vault', 'gallery', 'leads', 'clients', 'gbp' ),
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
            if ( ! in_array( 'gallery', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'gallery';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'gbp', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'gbp';
                $has_permission_updates = true;
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
            if ( ! in_array( 'gallery', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'gallery';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'gbp', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'gbp';
                $has_permission_updates = true;
            }
        }
        if ( $has_permission_updates ) {
            update_option( 'cora_role_permissions', $permissions );
        }
    }

    // Seed initial leads
    if ( ! get_option( 'cora_studio_leads' ) ) {
        $initial_leads = array(
            array(
                'id' => 'lead_sample_1',
                'names' => 'Kabir & Kiara',
                'email' => 'kabir.kiara@gmail.com',
                'scale' => 'destination',
                'city' => 'Udaipur',
                'notes' => 'Looking for cinematic, documentary-style wedding photography over 3 days.',
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
                'notes' => 'Intimate beachside wedding. Need pre-wedding shoot and 1 day event coverage.',
                'price' => '₹1,50,000',
                'status' => 'Proposal Sent',
                'emails' => array(),
                'created_at' => time() - 3600*24*4
            )
        );
        update_option( 'cora_studio_leads', $initial_leads );
    }

    // Seed initial clients
    if ( ! get_option( 'cora_studio_clients' ) ) {
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
                'shoot_type' => 'Maternity Portrait',
                'shoot_date' => '24th Jun, 2026'
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
                'shoot_type' => 'Destination Wedding',
                'shoot_date' => '20th Jun, 2026'
            ),
            array(
                'id' => 'client_3',
                'names' => 'Rajesh Kumar (Studio B)',
                'email' => 'rk.enterprises@gmail.com',
                'scale' => 'documentary',
                'city' => 'Studio A, Delhi',
                'price' => '₹40,000',
                'converted_at' => time() - 3600*24*20,
                'status' => 'completed',
                'shoot_type' => 'Product Shoot',
                'shoot_date' => '15th Jun, 2026'
            )
        );
        update_option( 'cora_studio_clients', $initial_clients );
    }

    // Migrate existing clients to include status and metadata
    $cora_existing_clients = get_option( 'cora_studio_clients', array() );
    if ( is_array( $cora_existing_clients ) ) {
        $cora_modified = false;
        foreach ( $cora_existing_clients as $key => $client ) {
            if ( ! isset( $client['status'] ) ) {
                if ( $client['id'] === 'client_2' || ( $client['names'] ?? '' ) === 'Rohit & Sneha' ) {
                    $cora_existing_clients[$key]['status'] = 'editing';
                    $cora_existing_clients[$key]['shoot_type'] = 'Destination Wedding';
                    $cora_existing_clients[$key]['shoot_date'] = '20th Jun, 2026';
                } elseif ( $client['id'] === 'client_3' || strpos( ( $client['names'] ?? '' ), 'Rajesh' ) !== false ) {
                    $cora_existing_clients[$key]['status'] = 'completed';
                    $cora_existing_clients[$key]['shoot_type'] = 'Product Shoot';
                    $cora_existing_clients[$key]['shoot_date'] = '15th Jun, 2026';
                } else {
                    $cora_existing_clients[$key]['status'] = 'confirmed';
                    $cora_existing_clients[$key]['shoot_type'] = 'Maternity Portrait';
                    $cora_existing_clients[$key]['shoot_date'] = '24th Jun, 2026';
                }
                $cora_modified = true;
            }
        }
        if ( $cora_modified ) {
            update_option( 'cora_studio_clients', $cora_existing_clients );
        }
    }

    // Seed initial documents
    if ( ! get_option( 'cora_studio_documents' ) ) {
        $initial_docs = array(
            array(
                'id' => 'doc_1',
                'title' => 'Proposal: Arjun & Priya Wedding Coverage',
                'type' => 'Proposal',
                'amount' => '₹4,50,000',
                'status' => 'Sent',
                'created_date' => '2026-06-15',
                'content' => '<h3>Wedding Photography & Videography Proposal</h3><p>We are delighted to cover your upcoming wedding ceremonies in Delhi. The package includes:</p><ul><li>2 Days Traditional + Cinematic Video coverage</li><li>2 Senior Photographers & 2 Senior Videographers</li><li>1 Drone Pilot for aerial capture</li><li>Pre-wedding shoot in Goa</li><li>Full-resolution digital delivery & Premium Hardcover Album</li></ul>',
                'client_link' => 'lead_sample_1',
                'secured_shares' => array()
            ),
            array(
                'id' => 'doc_2',
                'title' => 'Invoice: Nitin Arora Studio - Ritz Carlton Shoot',
                'type' => 'Invoice',
                'amount' => '₹1,80,000',
                'status' => 'Paid',
                'created_date' => '2026-06-10',
                'content' => '<h3>Commercial Shoot Invoice</h3><p>Billing for the corporate photography and video assignment completed at Ritz Carlton.</p><p>Total Amount: ₹1,80,000 (Paid in full on June 12, 2026 via NEFT).</p>',
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
        update_option( 'cora_studio_documents', $initial_docs );
    }

    // Seed initial financials
    if ( ! get_option( 'cora_studio_financials' ) ) {
        $initial_txs = array(
            array(
                'id' => 'tx_1',
                'date' => date( 'Y-m-d', time() - 3600*24*2 ),
                'description' => 'Booking Advance - Ananya Sharma (Maternity Portrait)',
                'type' => 'Inflow',
                'amount' => 10000,
                'category' => 'Advance Booking Fee',
                'status' => 'Received',
                'client_link' => 'client_1',
            ),
            array(
                'id' => 'tx_2',
                'date' => date( 'Y-m-d', time() - 3600*24*5 ),
                'description' => 'Final Payment - Rajesh Kumar (Product Shoot)',
                'type' => 'Inflow',
                'amount' => 40000,
                'category' => 'Client Package Payment',
                'status' => 'Received',
                'client_link' => 'client_3',
            ),
            array(
                'id' => 'tx_3',
                'date' => date( 'Y-m-d', time() - 3600*24*3 ),
                'description' => 'Second Shooter Payout - Rohit & Sneha Destination Wedding',
                'type' => 'Outflow',
                'amount' => 15000,
                'category' => 'Crew / Assistant Payout',
                'status' => 'Paid',
                'client_link' => 'client_2',
            ),
            array(
                'id' => 'tx_4',
                'date' => date( 'Y-m-d', time() - 3600*24*4 ),
                'description' => 'Equipment Rental - Sony 50mm f/1.2 GM (Ananya Shoot)',
                'type' => 'Outflow',
                'amount' => 3500,
                'category' => 'Equipment Rental',
                'status' => 'Paid',
                'client_link' => 'client_1',
            ),
            array(
                'id' => 'tx_5',
                'date' => date( 'Y-m-d', time() - 3600*24*15 ),
                'description' => 'Studio Rent & Electricity - June 2026',
                'type' => 'Outflow',
                'amount' => 25000,
                'category' => 'Studio Rent / Utilities',
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
        update_option( 'cora_studio_financials', $initial_txs );
    }

    // Seed initial galleries
    if ( ! get_option( 'cora_studio_galleries' ) ) {
        $initial_galleries = array(
            array(
                'id' => 'gallery_sample_1',
                'hash' => 'wedding-ceremony',
                'title' => 'Arjun & Priya - Wedding Ceremony',
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
                'id' => 'gallery_sample_2',
                'hash' => 'pre-wedding-goa',
                'title' => 'Pre-Wedding Shoot (Goa)',
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
        update_option( 'cora_studio_galleries', $initial_galleries );
    }

    // Migrate existing galleries to add client_email if not set
    $existing_galleries = get_option( 'cora_studio_galleries', array() );
    if ( is_array( $existing_galleries ) ) {
        $gallery_modified = false;
        foreach ( $existing_galleries as $key => $gallery ) {
            if ( ! isset( $gallery['client_email'] ) ) {
                if ( $gallery['id'] === 'gallery_sample_2' || strpos( $gallery['title'], 'Goa' ) !== false ) {
                    $existing_galleries[$key]['client_email'] = 'rohit.sneha@outlook.com';
                    $gallery_modified = true;
                } elseif ( $gallery['id'] === 'gallery_sample_1' || strpos( $gallery['title'], 'Arjun' ) !== false ) {
                    $existing_galleries[$key]['client_email'] = 'kabir.kiara@gmail.com';
                    $gallery_modified = true;
                }
            }
        }
        if ( $gallery_modified ) {
            update_option( 'cora_studio_galleries', $existing_galleries );
        }
    }
}
add_action( 'init', 'cora_studio_ai_seed_data' );

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
    $permissions['administrator'] = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'settings' );

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
        'cora_manager' => 'Manager',
        'cora_photographer' => 'Photographer',
        'cora_videographer' => 'Videographer',
        'cora_drone_pilot' => 'Drone Pilot',
        'cora_editor' => 'Editor'
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
 * AJAX Handler: Save Equipment
 */
function cora_ajax_save_equipment() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    $user = wp_get_current_user();
    if ( ! current_user_can( 'manage_options' ) && ! in_array( 'cora_manager', (array) $user->roles ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $name = sanitize_text_field( $_POST['name'] );
    $category = sanitize_text_field( $_POST['category'] );
    $serial = sanitize_text_field( $_POST['serial'] );

    if ( empty( $name ) || empty( $category ) || empty( $serial ) ) {
        wp_send_json_error( 'All fields are required.' );
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

    $inventory = get_option( 'cora_equipment_inventory', array() );
    $new_item = array(
        'id' => 'eq' . ( count( $inventory ) + 1 ) . '_' . time(),
        'name' => $name,
        'category' => $category,
        'serial' => $serial,
        'status' => 'Available',
        'crew' => '',
        'shoot' => '',
        'photo_url' => $photo_url,
        'assignment_note' => ''
    );
    $inventory[] = $new_item;
    update_option( 'cora_equipment_inventory', $inventory );

    wp_send_json_success( $new_item );
}
add_action( 'wp_ajax_cora_save_equipment', 'cora_ajax_save_equipment' );

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
    $shoot_title = sanitize_text_field( $_POST['shoot_title'] );
    $status = sanitize_text_field( $_POST['status'] );
    $assignment_note = isset( $_POST['assignment_note'] ) ? sanitize_text_field( $_POST['assignment_note'] ) : '';

    $inventory = get_option( 'cora_equipment_inventory', array() );
    $updated = false;

    foreach ( $inventory as &$item ) {
        if ( $item['id'] === $eq_id ) {
            $item['status'] = $status;
            $item['crew'] = ( 'Available' === $status || 'Maintenance' === $status ) ? '' : $crew_name;
            $item['shoot'] = ( 'Available' === $status || 'Maintenance' === $status ) ? '' : $shoot_title;
            $item['assignment_note'] = ( 'Available' === $status || 'Maintenance' === $status ) ? '' : $assignment_note;
            $updated = true;
            break;
        }
    }

    if ( $updated ) {
        update_option( 'cora_equipment_inventory', $inventory );
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

    $shoot_id = sanitize_text_field( $_POST['shoot_id'] );
    $crew = isset( $_POST['crew'] ) ? $_POST['crew'] : array();

    $assignments = get_option( 'cora_shoot_crew_assignments', array() );
    $assignments[$shoot_id] = $crew;
    update_option( 'cora_shoot_crew_assignments', $assignments );

    wp_send_json_success( 'Crew assignments saved successfully.' );
}
add_action( 'wp_ajax_cora_save_crew_assignments', 'cora_ajax_save_crew_assignments' );

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
        'cora_manager' => 'Manager',
        'cora_photographer' => 'Photographer',
        'cora_videographer' => 'Videographer',
        'cora_drone_pilot' => 'Drone Pilot',
        'cora_editor' => 'Editor'
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

    $inventory = get_option( 'cora_equipment_inventory', array() );
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
        update_option( 'cora_equipment_inventory', $updated_inventory );
        wp_send_json_success( 'Equipment deleted successfully.' );
    } else {
        wp_send_json_error( 'Equipment item not found.' );
    }
}
add_action( 'wp_ajax_cora_delete_equipment', 'cora_ajax_delete_equipment' );

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

    $documents = get_option( 'cora_studio_documents', array() );
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

    update_option( 'cora_studio_documents', $documents );
    wp_send_json_success( $new_doc );
}
add_action( 'wp_ajax_cora_save_document', 'cora_ajax_save_document' );

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

    $documents = get_option( 'cora_studio_documents', array() );
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
            $expiry_text_formatted = $no_expiry ? __( 'Never (Permanent Link)', 'cora-studio-ai' ) : date( 'Y-m-d H:i:s', $expiry_time );
            
            $subject = sprintf( __( 'Secure Document Share: %s', 'cora-studio-ai' ), $doc['title'] );
            $message = sprintf(
                __( "Hello,\n\nYou have been shared a secure access link for the following document: %s\n\nAccess Link: %s\nThis link is secure and will expire on: %s\n\nBest regards,\nCora for Studio Team", "cora-studio-ai" ),
                $doc['title'],
                $share_link,
                $expiry_text_formatted
            );
            
            wp_mail( $email, $subject, $message );
            break;
        }
    }

    if ( $found ) {
        update_option( 'cora_studio_documents', $documents );
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
 * Callback: REST endpoint to fetch active studio team members
 */
function cora_get_team_members_rest() {
    $users = get_users();
    $cora_role_labels = array(
        'administrator' => 'Super Admin',
        'cora_manager' => 'Manager',
        'cora_photographer' => 'Photographer',
        'cora_videographer' => 'Videographer',
        'cora_drone_pilot' => 'Drone Pilot',
        'cora_editor' => 'Editor'
    );

    $team = array();
    foreach ( $users as $user ) {
        $roles = $user->roles;
        $role_key = ! empty( $roles ) ? $roles[0] : '';
        // Only return users who have studio-related roles or administrator
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
function cora_studio_ai_serve_frontend_homepage() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path = parse_url( home_url(), PHP_URL_PATH );
    $path = substr( $request_uri, strlen( $home_path ) );
    $path = trim( parse_url( $path, PHP_URL_PATH ), '/' );

    // Skip hijacking if this is a workspace or shared resource route
    if ( strpos( $path, 'workspace' ) === 0 || strpos( $path, 'shared-doc' ) === 0 || strpos( $path, 'shared-gallery' ) === 0 ) {
        return;
    }

    if ( is_front_page() && ! is_admin() ) {
        $frontend_file = plugin_dir_path( __FILE__ ) . 'nitin-arora-photography/index.html';
        if ( file_exists( $frontend_file ) ) {
            $html = file_get_contents( $frontend_file );
            
            // Rewrite relative asset paths dynamically to absolute plugin URLs
            $plugin_assets_url = plugins_url( 'nitin-arora-photography/assets/', __FILE__ );
            
            // Replace relative paths
            $html = str_replace( 'src="assets/', 'src="' . $plugin_assets_url, $html );
            $html = str_replace( 'href="assets/', 'href="' . $plugin_assets_url, $html );
            $html = str_replace( 'url(\'assets/', 'url(\'' . $plugin_assets_url, $html );
            $html = str_replace( 'url("assets/', 'url("' . $plugin_assets_url, $html );
            $html = str_replace( 'content="assets/', 'content="' . $plugin_assets_url, $html );
            
            // Rewrite hardcoded admin-ajax URL to resolve dynamically on any WP installation
            $html = str_replace( '/wp-admin/admin-ajax.php', admin_url( 'admin-ajax.php' ), $html );
            
            header( 'Content-Type: text/html; charset=UTF-8' );
            echo $html;
            exit;
        }
    }
}
add_action( 'template_redirect', 'cora_studio_ai_serve_frontend_homepage', 5 );

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
function cora_current_user_can_manage_galleries() {
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
function cora_ajax_save_gallery() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! cora_current_user_can_manage_galleries() ) {
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

    $galleries = get_option( 'cora_studio_galleries', array() );
    if ( ! is_array( $galleries ) ) {
        $galleries = array();
    }

    $found_key = null;
    if ( ! empty( $id ) ) {
        foreach ( $galleries as $key => $gallery ) {
            if ( isset( $gallery['id'] ) && $gallery['id'] === $id ) {
                $found_key = $key;
                break;
            }
        }
    }

    if ( null !== $found_key ) {
        // Update existing gallery, retaining existing likes
        $existing_likes = isset( $galleries[$found_key]['likes'] ) ? $galleries[$found_key]['likes'] : array();
        
        // Clean likes of deleted assets
        $valid_asset_ids = wp_list_pluck( $sanitized_assets, 'id' );
        $cleaned_likes = array();
        foreach ( $existing_likes as $like_id ) {
            if ( in_array( $like_id, $valid_asset_ids ) ) {
                $cleaned_likes[] = $like_id;
            }
        }

        $galleries[$found_key]['title'] = $title;
        $galleries[$found_key]['template'] = $template;
        $galleries[$found_key]['password'] = $password;
        $galleries[$found_key]['assets'] = $sanitized_assets;
        $galleries[$found_key]['likes'] = $cleaned_likes;
        $galleries[$found_key]['share_images'] = $share_images;
        $galleries[$found_key]['share_videos'] = $share_videos;
        $galleries[$found_key]['client_email'] = $client_email;
        $galleries[$found_key]['drive_folder_url'] = $drive_folder_url;
    } else {
        // Create new gallery
        $new_id = 'gallery_' . time() . '_' . wp_generate_password( 4, false );
        $hash = md5( $new_id . wp_generate_password( 8, false ) );
        $galleries[] = array(
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

    update_option( 'cora_studio_galleries', $galleries );
    wp_send_json_success( array( 'message' => 'Gallery saved successfully.' ) );
}
add_action( 'wp_ajax_cora_save_gallery', 'cora_ajax_save_gallery' );

/**
 * AJAX Action: Delete Gallery
 */
function cora_ajax_delete_gallery() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! cora_current_user_can_manage_galleries() ) {
        wp_send_json_error( 'Access Denied: insufficient permissions.' );
    }

    $id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
    if ( empty( $id ) ) {
        wp_send_json_error( 'Gallery ID is required.' );
    }

    $galleries = get_option( 'cora_studio_galleries', array() );
    if ( ! is_array( $galleries ) ) {
        $galleries = array();
    }

    $updated_galleries = array();
    $deleted = false;
    foreach ( $galleries as $gallery ) {
        if ( isset( $gallery['id'] ) && $gallery['id'] === $id ) {
            $deleted = true;
            continue;
        }
        $updated_galleries[] = $gallery;
    }

    if ( $deleted ) {
        update_option( 'cora_studio_galleries', $updated_galleries );
        wp_send_json_success( array( 'message' => 'Gallery deleted successfully.' ) );
    } else {
        wp_send_json_error( 'Gallery not found.' );
    }
}
add_action( 'wp_ajax_cora_delete_gallery', 'cora_ajax_delete_gallery' );

/**
 * AJAX Action: Toggle Gallery Asset Like (Public Endpoint)
 */
function cora_ajax_toggle_gallery_like() {
    $gallery_hash = isset( $_POST['gallery_hash'] ) ? sanitize_text_field( $_POST['gallery_hash'] ) : '';
    $asset_id = isset( $_POST['asset_id'] ) ? sanitize_text_field( $_POST['asset_id'] ) : '';
    $liked = isset( $_POST['liked'] ) && $_POST['liked'] === 'true';

    if ( empty( $gallery_hash ) || empty( $asset_id ) ) {
        wp_send_json_error( 'Invalid request parameters.' );
    }

    $galleries = get_option( 'cora_studio_galleries', array() );
    if ( ! is_array( $galleries ) ) {
        wp_send_json_error( 'Galleries store is empty.' );
    }

    $found_key = null;
    foreach ( $galleries as $key => $gallery ) {
        if ( isset( $gallery['hash'] ) && $gallery['hash'] === $gallery_hash ) {
            $found_key = $key;
            break;
        }
    }

    if ( null === $found_key ) {
        wp_send_json_error( 'Gallery not found.' );
    }

    $likes = isset( $galleries[$found_key]['likes'] ) ? $galleries[$found_key]['likes'] : array();
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

    $galleries[$found_key]['likes'] = $likes;
    update_option( 'cora_studio_galleries', $galleries );

    wp_send_json_success( array(
        'message' => 'Selection updated.',
        'likes_count' => count( $likes )
    ) );
}
add_action( 'wp_ajax_cora_toggle_gallery_like', 'cora_ajax_toggle_gallery_like' );
add_action( 'wp_ajax_nopriv_cora_toggle_gallery_like', 'cora_ajax_toggle_gallery_like' );

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

    $leads = get_option( 'cora_studio_leads', array() );
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
    update_option( 'cora_studio_leads', $leads );

    wp_send_json_success( array(
        'message' => 'Inquiry logged successfully!',
        'lead'    => $new_lead
    ) );
}
add_action( 'wp_ajax_cora_submit_lead', 'cora_ajax_submit_lead' );
add_action( 'wp_ajax_nopriv_cora_submit_lead', 'cora_ajax_submit_lead' );

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
    
    $demo_gallery        = isset( $_POST['demo_gallery'] ) ? sanitize_text_field( $_POST['demo_gallery'] ) : null;
    $demo_gallery_shared = isset( $_POST['demo_gallery_shared'] ) ? sanitize_text_field( $_POST['demo_gallery_shared'] ) : null;
    $demo_gallery_viewed = isset( $_POST['demo_gallery_viewed'] ) ? sanitize_text_field( $_POST['demo_gallery_viewed'] ) : null;
    $equipment_ids       = isset( $_POST['equipment_ids'] ) ? sanitize_text_field( $_POST['equipment_ids'] ) : null;

    if ( empty( $lead_id ) ) {
        wp_send_json_error( 'Lead ID is required.' );
    }

    $leads = get_option( 'cora_studio_leads', array() );
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
    if ( null !== $demo_gallery ) {
        $leads[$found_key]['demo_gallery'] = $demo_gallery;
    }
    if ( null !== $demo_gallery_shared ) {
        $leads[$found_key]['demo_gallery_shared'] = ( $demo_gallery_shared === 'true' );
    }
    if ( null !== $demo_gallery_viewed ) {
        $leads[$found_key]['demo_gallery_viewed'] = ( $demo_gallery_viewed === 'true' );
    }
    if ( null !== $equipment_ids ) {
        $leads[$found_key]['equipment_ids'] = array_filter( array_map( 'trim', explode( ',', $equipment_ids ) ) );
    }

    update_option( 'cora_studio_leads', $leads );

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

    $leads = get_option( 'cora_studio_leads', array() );
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
    update_option( 'cora_studio_leads', $leads );

    wp_send_json_success( array(
        'message' => 'Lead deleted.'
    ) );
}
add_action( 'wp_ajax_cora_delete_lead', 'cora_ajax_delete_lead' );

/**
 * Helper: Copy a Lead to Client Directory
 */
function cora_copy_lead_to_clients( $lead ) {
    $clients = get_option( 'cora_studio_clients', array() );
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
        'shoot_date'    => '25th Jun, 2026',
        'shoot_type'    => 'Maternity Portrait',
        'equipment_ids' => isset( $lead['equipment_ids'] ) ? $lead['equipment_ids'] : array(),
        'demo_gallery'  => isset( $lead['demo_gallery'] ) ? $lead['demo_gallery'] : ''
    );

    update_option( 'cora_studio_clients', $clients );
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

    $leads = get_option( 'cora_studio_leads', array() );
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
    update_option( 'cora_studio_leads', $leads );

    wp_send_json_success( array(
        'message' => 'Lead converted to Client directory successfully.'
    ) );
}
add_action( 'wp_ajax_cora_convert_lead_to_client', 'cora_ajax_convert_lead_to_client' );

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

    $clients = get_option( 'cora_studio_clients', array() );
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
    update_option( 'cora_studio_clients', $clients );

    wp_send_json_success( array(
        'message' => 'Client removed from directory.'
    ) );
}
add_action( 'wp_ajax_cora_delete_client', 'cora_ajax_delete_client' );

/**
 * AJAX Action: Add / Save a new Shoot Booking (Client)
 */
function cora_ajax_save_booking() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }

    $name = sanitize_text_field( $_POST['client_name'] ?? '' );
    $type = sanitize_text_field( $_POST['shoot_type'] ?? 'Maternity Portrait' );
    $location = sanitize_text_field( $_POST['location'] ?? 'Delhi Studio' );
    $date = sanitize_text_field( $_POST['date'] ?? '' );
    $price = sanitize_text_field( $_POST['price'] ?? '₹15,000' );

    if ( empty( $name ) ) {
        wp_send_json_error( 'Client name is required.' );
    }

    $clients = get_option( 'cora_studio_clients', array() );
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
        'shoot_date'   => $date,
        'shoot_type'   => $type
    );

    $clients[] = $new_client;
    update_option( 'cora_studio_clients', $clients );

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

    $clients = get_option( 'cora_studio_clients', array() );
    $updated = false;

    foreach ( $clients as $key => $client ) {
        if ( ( ! empty( $client_id ) && $client['id'] === $client_id ) || ( ! empty( $client_name ) && $client['names'] === $client_name ) ) {
            $clients[$key]['status'] = $status;
            $updated = true;
            break;
        }
    }

    if ( $updated ) {
        update_option( 'cora_studio_clients', $clients );
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
    
    $event_label = 'wedding / shoot';
    if ( $lead_scale === 'intimate' ) $event_label = 'intimate wedding';
    if ( $lead_scale === 'multi-day' ) $event_label = 'multi-day celebration';
    if ( $lead_scale === 'destination' ) $event_label = 'grand destination wedding';
    if ( $lead_scale === 'documentary' ) $event_label = 'pre-wedding documentary';

    $location_str = ! empty( $lead_city ) ? " in " . $lead_city : "";

    return array(
        array(
            'id'            => 'email_' . time() . '_1',
            'step'          => 1,
            'trigger_delay' => 'Immediate',
            'subject'       => "Thank you for reaching out, " . $first_name . "!",
            'body'          => "Hi " . $first_name . ",\n\nThank you so much for contacting Cora for Studio. We are thrilled to hear about your upcoming " . $event_label . $location_str . "!\n\nOur team is currently reviewing your inquiry notes and vision details. We will be in touch within the next 24 hours to schedule our initial creative consultation.\n\nWarm regards,\nCora for Studio Agent",
            'status'        => 'Sent',
            'sent_at'       => time(),
        ),
        array(
            'id'            => 'email_' . time() . '_2',
            'step'          => 2,
            'trigger_delay' => 'Day 1 Follow-up',
            'subject'       => "Our Latest Work & Visual Styles",
            'body'          => "Hi " . $first_name . ",\n\nWhile we prepare for our consultation, we wanted to share a few curated highlights of our recent " . $event_label . " work.\n\nWe focus on capturing raw, authentic moments and crafting them into everlasting visual narratives. You can explore our featured galleries inside the Studio Workspace.\n\nLooking forward to speaking with you!\n\nBest,\nCora for Studio Agent",
            'status'        => 'Scheduled',
            'sent_at'       => null,
        ),
        array(
            'id'            => 'email_' . time() . '_3',
            'step'          => 3,
            'trigger_delay' => 'Day 3 Consultation Call',
            'subject'       => "Let's align your vision - Book a calendar slot",
            'body'          => "Hi " . $first_name . ",\n\nWe would love to get a consultation on the books. This helps us align on the creative direction, event timeline, and custom packages for your " . $event_label . ".\n\nPlease select a convenient time slot via our staging link: https://calendly.com/cora-studio/creative-consultation\n\nSpeak soon!\n\nBest regards,\nCora for Studio Agent",
            'status'        => 'Scheduled',
            'sent_at'       => null,
        ),
        array(
            'id'            => 'email_' . time() . '_4',
            'step'          => 4,
            'trigger_delay' => 'Day 5 Final Follow-up',
            'subject'       => "Quick follow-up from Cora Studio",
            'body'          => "Hi " . $first_name . ",\n\nJust wanted to send a quick follow-up to see if you had any questions about our previous emails or if you'd like to book that consultation call.\n\nWe only accept a limited number of commissions per season to ensure premium focus for every couple, and we'd love to work with you on your " . $event_label . ".\n\nLet us know if you have any questions!\n\nWarmly,\nCora for Studio Agent",
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

    $leads = get_option( 'cora_studio_leads', array() );
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
    update_option( 'cora_studio_leads', $leads );

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

    $transactions = get_option( 'cora_studio_financials', array() );
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

    update_option( 'cora_studio_financials', $transactions );
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

    $transactions = get_option( 'cora_studio_financials', array() );
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
    update_option( 'cora_studio_financials', $transactions );

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

    $documents = get_option( 'cora_studio_documents', array() );
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
                <span class="logo-text">CORA FOR STUDIO</span>
            </div>
            <div class="content">
                ' . ( ! empty( $message_override ) ? $message_override : $doc['content'] ) . '
            </div>
            <div class="footer">
                ' . esc_html( ! empty( $doc['footer_text'] ) ? $doc['footer_text'] : '© Cora for Studio. All rights reserved.' ) . '
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
    
    update_option( 'cora_studio_documents', $documents );

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

// ==========================================
// ATTENDANCE & GEOLOCATION
// ==========================================

/**
 * Handle staff punch in
 */
function cora_ajax_punch_in() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) wp_send_json_error( 'Not authenticated.' );

    $lat = isset( $_POST['lat'] ) ? sanitize_text_field( wp_unslash( $_POST['lat'] ) ) : '';
    $lng = isset( $_POST['lng'] ) ? sanitize_text_field( wp_unslash( $_POST['lng'] ) ) : '';

    if ( empty( $lat ) || empty( $lng ) ) {
        wp_send_json_error( 'Location data is missing.' );
    }

    $user_id = get_current_user_id();
    $dt = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
    $today = $dt->format('Y-m-d');
    $now = $dt->format('Y-m-d H:i:s');

    $attendance_logs = get_option( 'cora_attendance_logs', array() );

    // Ensure array structure
    if ( ! isset( $attendance_logs[ $user_id ] ) ) {
        $attendance_logs[ $user_id ] = array();
    }

    // Check if already punched in today
    if ( isset( $attendance_logs[ $user_id ][ $today ] ) && ! empty( $attendance_logs[ $user_id ][ $today ]['punch_in'] ) ) {
        wp_send_json_error( 'You have already punched in today.' );
    }

    $office_loc = get_option( 'cora_office_location', false );
    $flagged = false;
    $flag_reason = '';
    
    if ( $office_loc && ! empty( $office_loc['lat'] ) && ! empty( $office_loc['lng'] ) ) {
        $distance = cora_calculate_distance( $lat, $lng, $office_loc['lat'], $office_loc['lng'] );
        if ( $distance > 1000 ) {
            $flagged = true;
            $flag_reason = 'Outside 1000m radius (' . round($distance) . 'm)';
        }
    }

    $attendance_logs[ $user_id ][ $today ] = array(
        'punch_in'      => $now,
        'punch_out'     => '',
        'punch_in_lat'  => $lat,
        'punch_in_lng'  => $lng,
        'punch_out_lat' => '',
        'punch_out_lng' => '',
        'flagged'       => $flagged,
        'flag_reason'   => $flag_reason
    );

    update_option( 'cora_attendance_logs', $attendance_logs );

    // Send email notification to admin
    $admin_email = get_option( 'admin_email' );
    $current_user = wp_get_current_user();
    $user_display_name = $current_user->exists() ? $current_user->display_name : 'Staff';
    $subject = sprintf( '[Attendance] %s Punched In', $user_display_name );
    $message = sprintf(
        "Staff member %s has punched in.\n\nTime: %s\nLatitude: %s\nLongitude: %s\nStatus: %s",
        $user_display_name,
        $now,
        $lat,
        $lng,
        $flagged ? 'Flagged (' . $flag_reason . ')' : 'Normal'
    );
    wp_mail( $admin_email, $subject, $message );

    wp_send_json_success( array(
        'message' => 'Punched in successfully.',
        'log'     => $attendance_logs[ $user_id ][ $today ]
    ) );
}
add_action( 'wp_ajax_cora_punch_in', 'cora_ajax_punch_in' );

/**
 * Handle staff punch out
 */
function cora_ajax_punch_out() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) wp_send_json_error( 'Not authenticated.' );

    $lat = isset( $_POST['lat'] ) ? sanitize_text_field( wp_unslash( $_POST['lat'] ) ) : '';
    $lng = isset( $_POST['lng'] ) ? sanitize_text_field( wp_unslash( $_POST['lng'] ) ) : '';

    $user_id = get_current_user_id();
    $dt = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
    $today = $dt->format('Y-m-d');
    $now = $dt->format('Y-m-d H:i:s');

    $attendance_logs = get_option( 'cora_attendance_logs', array() );

    if ( ! isset( $attendance_logs[ $user_id ][ $today ] ) || empty( $attendance_logs[ $user_id ][ $today ]['punch_in'] ) ) {
        wp_send_json_error( 'You have not punched in today.' );
    }

    if ( ! empty( $attendance_logs[ $user_id ][ $today ]['punch_out'] ) ) {
        wp_send_json_error( 'You have already punched out today.' );
    }

    $office_loc = get_option( 'cora_office_location', false );
    $flagged = isset($attendance_logs[ $user_id ][ $today ]['flagged']) ? $attendance_logs[ $user_id ][ $today ]['flagged'] : false;
    $flag_reason = isset($attendance_logs[ $user_id ][ $today ]['flag_reason']) ? $attendance_logs[ $user_id ][ $today ]['flag_reason'] : '';
    
    if ( $office_loc && ! empty( $office_loc['lat'] ) && ! empty( $office_loc['lng'] ) ) {
        $distance = cora_calculate_distance( $lat, $lng, $office_loc['lat'], $office_loc['lng'] );
        if ( $distance > 1000 ) {
            $flagged = true;
            $flag_reason .= ($flag_reason ? ' | ' : '') . 'Punch Out: Outside 1000m (' . round($distance) . 'm)';
        }
    }

    $attendance_logs[ $user_id ][ $today ]['punch_out']     = $now;
    $attendance_logs[ $user_id ][ $today ]['punch_out_lat'] = $lat;
    $attendance_logs[ $user_id ][ $today ]['punch_out_lng'] = $lng;
    $attendance_logs[ $user_id ][ $today ]['flagged']       = $flagged;
    $attendance_logs[ $user_id ][ $today ]['flag_reason']   = $flag_reason;

    update_option( 'cora_attendance_logs', $attendance_logs );

    // Send email notification to admin
    $admin_email = get_option( 'admin_email' );
    $current_user = wp_get_current_user();
    $user_display_name = $current_user->exists() ? $current_user->display_name : 'Staff';
    $subject = sprintf( '[Attendance] %s Punched Out', $user_display_name );
    $message = sprintf(
        "Staff member %s has punched out.\n\nTime: %s\nLatitude: %s\nLongitude: %s\nStatus: %s",
        $user_display_name,
        $now,
        $lat,
        $lng,
        $flagged ? 'Flagged (' . $flag_reason . ')' : 'Normal'
    );
    wp_mail( $admin_email, $subject, $message );

    wp_send_json_success( array(
        'message' => 'Punched out successfully.',
        'log'     => $attendance_logs[ $user_id ][ $today ]
    ) );
}
add_action( 'wp_ajax_cora_punch_out', 'cora_ajax_punch_out' );

/**
 * Get attendance logs (Admin sees all, User sees own)
 */
function cora_ajax_get_attendance() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) wp_send_json_error( 'Not authenticated.' );

    $user = wp_get_current_user();
    $is_admin = in_array( 'administrator', (array) $user->roles ) || in_array( 'cora_manager', (array) $user->roles );
    $user_id = get_current_user_id();
    
    $attendance_logs = get_option( 'cora_attendance_logs', array() );
    
    $result = array();

    if ( $is_admin ) {
        // Admin gets all users
        foreach ( $attendance_logs as $uid => $logs ) {
            $user_info = get_userdata( $uid );
            $name = $user_info ? $user_info->display_name : 'Unknown User';
            foreach ( $logs as $date => $log ) {
                $log['user_id'] = $uid;
                $log['name'] = $name;
                $log['date'] = $date;
                $result[] = $log;
            }
        }
    } else {
        // Staff gets only their own
        if ( isset( $attendance_logs[ $user_id ] ) ) {
            foreach ( $attendance_logs[ $user_id ] as $date => $log ) {
                $log['user_id'] = $user_id;
                $log['name'] = $user->display_name;
                $log['date'] = $date;
                $result[] = $log;
            }
        }
    }

    // Sort by date DESC
    usort($result, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    // Get Office Location
    $office_loc = get_option( 'cora_office_location', array() );
    $office_lat = isset( $office_loc['lat'] ) ? floatval( $office_loc['lat'] ) : 0;
    $office_lng = isset( $office_loc['lng'] ) ? floatval( $office_loc['lng'] ) : 0;

    $is_super_admin = current_user_can( 'administrator' );
    $now = time();
    foreach ( $result as &$log_item ) {
        $log_item['can_edit'] = $is_super_admin;
        
        // Check if out of bounds (200m radius)
        $log_item['flagged'] = false;
        
        $status = isset($log_item['status']) ? $log_item['status'] : '';
        
        if ( $status === 'approved' ) {
            $log_item['flagged'] = false;
        } else if ( $status === 'rejected' ) {
            $log_item['flagged'] = true;
            $log_item['flag_reason'] = 'Rejected by Admin';
        } else if ( $office_lat && $office_lng && !empty($log_item['punch_in_lat']) && !empty($log_item['punch_in_lng']) ) {
            $user_lat = floatval($log_item['punch_in_lat']);
            $user_lng = floatval($log_item['punch_in_lng']);
            
            // Haversine formula
            $earth_radius = 6371000; // meters
            $dLat = deg2rad($user_lat - $office_lat);
            $dLng = deg2rad($user_lng - $office_lng);
            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($office_lat)) * cos(deg2rad($user_lat)) * sin($dLng/2) * sin($dLng/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            $distance = $earth_radius * $c;
            
            if ($distance > 1000) {
                $log_item['flagged'] = true;
                $log_item['distance'] = round($distance);
                $log_item['flag_reason'] = 'Outside 1000m (' . round($distance) . 'm away)';
            }
        }
        
        // Super Admin gets manage options
        $log_item['can_manage'] = $is_super_admin;
    }
    unset( $log_item );

    // Calculate dynamic overview stats for today
    $dt = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
    $today = $dt->format('Y-m-d');
    
    $team_users = get_users( array(
        'role__in' => array( 'administrator', 'cora_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor' )
    ) );
    $total_team = count( $team_users );
    
    $present_today = 0;
    $flagged_today = 0;
    
    foreach ( $attendance_logs as $uid => $days ) {
        if ( isset( $days[$today] ) && ! empty( $days[$today]['punch_in'] ) ) {
            $present_today++;
            
            // Re-verify compliance using distance check or status
            $today_log = $days[$today];
            $flagged = false;
            $status = isset($today_log['status']) ? $today_log['status'] : '';
            if ( $status === 'approved' ) {
                $flagged = false;
            } else if ( $status === 'rejected' ) {
                $flagged = true;
            } else if ( $office_lat && $office_lng && !empty($today_log['punch_in_lat']) && !empty($today_log['punch_in_lng']) ) {
                $user_lat = floatval($today_log['punch_in_lat']);
                $user_lng = floatval($today_log['punch_in_lng']);
                $earth_radius = 6371000;
                $dLat = deg2rad($user_lat - $office_lat);
                $dLng = deg2rad($user_lng - $office_lng);
                $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($office_lat)) * cos(deg2rad($user_lat)) * sin($dLng/2) * sin($dLng/2);
                $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                $distance = $earth_radius * $c;
                if ($distance > 1000) {
                    $flagged = true;
                }
            }
            if ( $flagged ) {
                $flagged_today++;
            }
        }
    }
    
    $missing_absent = max( 0, $total_team - $present_today );
    
    $response_data = array(
        'logs'  => $result,
        'stats' => array(
            'total_team'        => $total_team,
            'present_today'     => $present_today,
            'missing_absent'    => $missing_absent,
            'flagged_locations' => $flagged_today
        )
    );
 
    wp_send_json_success( $response_data );
}
add_action( 'wp_ajax_cora_get_attendance', 'cora_ajax_get_attendance' );


/**
 * AJAX: Add attendance record manually (Super Admin only)
 */
function cora_ajax_add_attendance() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'administrator' ) ) {
        wp_send_json_error( 'Only the Super Admin is authorized to add attendance records manually.' );
    }

    $target_user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
    $target_date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
    $punch_in = isset( $_POST['punch_in'] ) ? sanitize_text_field( wp_unslash( $_POST['punch_in'] ) ) : '';
    $punch_out = isset( $_POST['punch_out'] ) ? sanitize_text_field( wp_unslash( $_POST['punch_out'] ) ) : '';
    $reason = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( $_POST['reason'] ) ) : '';

    if ( ! $target_user_id ) {
        wp_send_json_error( 'Please select a team member.' );
    }
    if ( empty( $target_date ) ) {
        wp_send_json_error( 'Please select a date.' );
    }
    if ( empty( $punch_in ) ) {
        wp_send_json_error( 'Punch in time is required.' );
    }
    if ( empty( $reason ) ) {
        wp_send_json_error( 'Reason for manual entry is required.' );
    }

    $attendance_logs = get_option( 'cora_attendance_logs', array() );

    if ( ! isset( $attendance_logs[ $target_user_id ] ) ) {
        $attendance_logs[ $target_user_id ] = array();
    }

    if ( isset( $attendance_logs[ $target_user_id ][ $target_date ] ) ) {
        wp_send_json_error( 'An attendance record already exists for this member on ' . $target_date . '.' );
    }

    $current_user = wp_get_current_user();
    $dt = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
    $now_timestamp = $dt->format('Y-m-d H:i:s');

    $attendance_logs[ $target_user_id ][ $target_date ] = array(
        'punch_in'      => $punch_in,
        'punch_out'     => $punch_out,
        'punch_in_lat'  => '',
        'punch_in_lng'  => '',
        'punch_out_lat' => '',
        'punch_out_lng' => '',
        'flagged'       => false,
        'flag_reason'   => '',
        'status'        => 'approved',
        'edit_history'  => array(
            array(
                'editor_id' => get_current_user_id(),
                'editor_name' => $current_user->display_name,
                'timestamp' => $now_timestamp,
                'reason' => 'Manual Entry: ' . $reason,
                'old_punch_in' => '',
                'old_punch_out' => '',
                'new_punch_in' => $punch_in,
                'new_punch_out' => $punch_out,
            )
        )
    );

    update_option( 'cora_attendance_logs', $attendance_logs );

    wp_send_json_success( array(
        'message' => 'Attendance record added successfully.'
    ) );
}
add_action( 'wp_ajax_cora_add_attendance', 'cora_ajax_add_attendance' );


/**
 * Edit attendance record
 */
function cora_ajax_edit_attendance() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) wp_send_json_error( 'Not authenticated.' );

    if ( ! current_user_can( 'administrator' ) ) {
        wp_send_json_error( 'Only the Super Admin is authorized to edit attendance records.' );
    }

    $current_user = wp_get_current_user();
    $editor_id = get_current_user_id();

    $target_user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
    $target_date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
    
    $new_punch_in = isset( $_POST['punch_in'] ) ? sanitize_text_field( wp_unslash( $_POST['punch_in'] ) ) : '';
    $new_punch_out = isset( $_POST['punch_out'] ) ? sanitize_text_field( wp_unslash( $_POST['punch_out'] ) ) : '';
    $reason = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( $_POST['reason'] ) ) : '';

    if ( empty( $reason ) ) {
        wp_send_json_error( 'Reason for edit is required.' );
    }

    if ( ! $target_user_id || empty( $target_date ) ) {
        wp_send_json_error( 'Missing record identifiers.' );
    }



    $attendance_logs = get_option( 'cora_attendance_logs', array() );

    if ( ! isset( $attendance_logs[ $target_user_id ][ $target_date ] ) ) {
        wp_send_json_error( 'Record not found.' );
    }

    $record = &$attendance_logs[ $target_user_id ][ $target_date ];

    // Prepare audit entry
    $dt = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
    $now = $dt->format('Y-m-d H:i:s');

    $audit_entry = array(
        'editor_id' => $editor_id,
        'editor_name' => $current_user->display_name,
        'timestamp' => $now,
        'reason' => $reason,
        'old_punch_in' => $record['punch_in'],
        'old_punch_out' => $record['punch_out'],
        'new_punch_in' => $new_punch_in,
        'new_punch_out' => $new_punch_out,
    );
    
    if ( ! isset( $record['edit_history'] ) ) {
        $record['edit_history'] = array();
    }
    
    $record['edit_history'][] = $audit_entry;

    $record['punch_in'] = $new_punch_in;
    $record['punch_out'] = $new_punch_out;

    update_option( 'cora_attendance_logs', $attendance_logs );

    // Send email notification
    $admin_email = get_option( 'admin_email' );
    $subject = 'Attendance Edit Notification';
    $message = sprintf(
        "An attendance record was edited.\n\nEditor: %s\nTarget User ID: %d\nDate: %s\nReason: %s\n\nOld Punch In: %s\nOld Punch Out: %s\nNew Punch In: %s\nNew Punch Out: %s",
        $current_user->display_name,
        $target_user_id,
        $target_date,
        $reason,
        $audit_entry['old_punch_in'],
        $audit_entry['old_punch_out'],
        $new_punch_in,
        $new_punch_out
    );
    wp_mail( $admin_email, $subject, $message );
    
    wp_send_json_success( array(
        'message' => 'Attendance record updated.',
        'log' => $record
    ) );
}
add_action( 'wp_ajax_cora_edit_attendance', 'cora_ajax_edit_attendance' );

/**
 * Manage attendance record (Approve, Reject, Delete)
 */
function cora_ajax_manage_attendance() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'administrator' ) ) wp_send_json_error( 'Only super admin can perform this action.' );

    $target_user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
    $target_date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
    $action_type = isset( $_POST['manage_action'] ) ? sanitize_text_field( wp_unslash( $_POST['manage_action'] ) ) : '';

    if ( ! $target_user_id || empty( $target_date ) || empty( $action_type ) ) {
        wp_send_json_error( 'Missing parameters.' );
    }

    $attendance_logs = get_option( 'cora_attendance_logs', array() );

    if ( ! isset( $attendance_logs[ $target_user_id ][ $target_date ] ) ) {
        wp_send_json_error( 'Record not found.' );
    }

    if ( $action_type === 'delete' ) {
        unset( $attendance_logs[ $target_user_id ][ $target_date ] );
        $msg = 'Attendance log deleted.';
    } else if ( $action_type === 'approve' || $action_type === 'reject' ) {
        $attendance_logs[ $target_user_id ][ $target_date ]['status'] = $action_type;
        $msg = 'Attendance log ' . $action_type . 'd.';
    } else {
        wp_send_json_error( 'Invalid action.' );
    }

    update_option( 'cora_attendance_logs', $attendance_logs );
    
    wp_send_json_success( array(
        'message' => $msg
    ) );
}
add_action( 'wp_ajax_cora_manage_attendance', 'cora_ajax_manage_attendance' );

// Function to calculate distance between two coordinates in meters
function cora_calculate_distance($lat1, $lon1, $lat2, $lon2) {
    if (empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) return 0;
    $earth_radius = 6371000; // in meters
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    return $earth_radius * $c;
}

// Set office location
function cora_ajax_set_office_location() {
    check_ajax_referer('cora_ajax_nonce', 'nonce');
    if (!current_user_can('manage_options') && !current_user_can('cora_manager')) {
        wp_send_json_error('Permission denied.');
    }
    
    $lat = isset($_POST['lat']) ? sanitize_text_field(wp_unslash($_POST['lat'])) : '';
    $lng = isset($_POST['lng']) ? sanitize_text_field(wp_unslash($_POST['lng'])) : '';
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    
    if (empty($lat) || empty($lng)) {
        wp_send_json_error('Location data is missing.');
    }
    
    update_option('cora_office_location', array('lat' => $lat, 'lng' => $lng, 'name' => $name));
    wp_send_json_success('Office location saved successfully.');
}
add_action('wp_ajax_cora_set_office_location', 'cora_ajax_set_office_location');

function cora_ajax_get_office_location() {
    check_ajax_referer('cora_ajax_nonce', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error('Permission denied.');
    }
    $office_loc = get_option('cora_office_location', array());
    wp_send_json_success($office_loc);
}
add_action('wp_ajax_cora_get_office_location', 'cora_ajax_get_office_location');

function cora_ajax_resolve_map_url() {
    check_ajax_referer('cora_ajax_nonce', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error('Permission denied.');
    }
    $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';
    if (empty($url) || strpos($url, 'http') !== 0) {
        wp_send_json_error('Invalid URL.');
    }
    
    $final_url = $url;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
    }
    
    preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $final_url, $matches);
    if ($matches) {
        wp_send_json_success(array('lat' => $matches[1], 'lng' => $matches[2], 'url' => $final_url));
    } else {
        wp_send_json_error('Could not extract coordinates.');
    }
}
add_action('wp_ajax_cora_resolve_map_url', 'cora_ajax_resolve_map_url');

// Update user profile
function cora_ajax_update_my_profile() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Permission denied.' );
    }
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    // Update display name
    $display_name = isset( $_POST['display_name'] ) ? sanitize_text_field( $_POST['display_name'] ) : '';
    if ( ! empty( $display_name ) ) {
        wp_update_user( array( 'ID' => $user_id, 'display_name' => $display_name ) );
    }

    // Update email (only if valid and not already taken)
    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    if ( ! empty( $email ) && is_email( $email ) && $email !== $current_user->user_email ) {
        $existing = email_exists( $email );
        if ( $existing && $existing !== $user_id ) {
            wp_send_json_error( 'This email address is already in use by another account.' );
        }
        wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
    }

    // Update phone (user meta)
    if ( isset( $_POST['phone'] ) ) {
        update_user_meta( $user_id, 'cora_phone', sanitize_text_field( $_POST['phone'] ) );
    }

    // Update bio (user meta)
    if ( isset( $_POST['bio'] ) ) {
        update_user_meta( $user_id, 'description', sanitize_textarea_field( $_POST['bio'] ) );
    }

    // Handle avatar file upload
    if ( ! empty( $_FILES['avatar_file'] ) && $_FILES['avatar_file']['size'] > 0 ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        $attachment_id = media_handle_upload( 'avatar_file', 0 );
        if ( ! is_wp_error( $attachment_id ) ) {
            $avatar_url = wp_get_attachment_url( $attachment_id );
            update_user_meta( $user_id, 'cora_avatar_url', $avatar_url );
        }
    }

    wp_send_json_success( 'Profile updated successfully.' );
}
add_action( 'wp_ajax_cora_update_my_profile', 'cora_ajax_update_my_profile' );

// Schedule daily attendance report cron event at 9:00 PM IST
function cora_schedule_daily_attendance_report() {
    if ( ! wp_next_scheduled( 'cora_daily_attendance_report_event' ) ) {
        $timezone = new DateTimeZone('Asia/Kolkata');
        $time_at_9pm = new DateTime('21:00:00', $timezone);
        if ( $time_at_9pm->getTimestamp() < time() ) {
            $time_at_9pm->modify('+1 day');
        }
        wp_schedule_event( $time_at_9pm->getTimestamp(), 'daily', 'cora_daily_attendance_report_event' );
    }
}
add_action( 'wp', 'cora_schedule_daily_attendance_report' );

// Clean up cron event on deactivation
register_deactivation_hook( __FILE__, 'cora_clear_daily_attendance_report_cron' );
function cora_clear_daily_attendance_report_cron() {
    wp_clear_scheduled_hook( 'cora_daily_attendance_report_event' );
}

// Daily summary report generator and dispatcher
function cora_send_daily_attendance_report() {
    $dt = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
    $today = $dt->format('Y-m-d');
    $today_human = $dt->format('d M, Y');

    $attendance_logs = get_option( 'cora_attendance_logs', array() );
    
    $users = get_users();
    $user_map = array();
    foreach ( $users as $u ) {
        $user_map[$u->ID] = $u->display_name ? $u->display_name : $u->user_login;
    }

    $report_lines = array();
    $report_lines[] = "Daily Attendance Report - " . $today_human;
    $report_lines[] = "=====================================\n";

    $has_logs = false;
    if ( ! empty( $attendance_logs ) ) {
        foreach ( $attendance_logs as $user_id => $days ) {
            if ( isset( $days[$today] ) ) {
                $has_logs = true;
                $log = $days[$today];
                $name = isset( $user_map[$user_id] ) ? $user_map[$user_id] : 'User ID ' . $user_id;
                
                $punch_in_time = ! empty( $log['punch_in'] ) ? date( 'h:i A', strtotime( $log['punch_in'] ) ) : 'N/A';
                $punch_out_time = ! empty( $log['punch_out'] ) ? date( 'h:i A', strtotime( $log['punch_out'] ) ) : 'N/A';
                $status = ! empty( $log['flagged'] ) ? 'Flagged (' . $log['flag_reason'] . ')' : 'Normal';
                
                $report_lines[] = sprintf(
                    "Staff: %s\n- Punch In: %s\n- Punch Out: %s\n- Status: %s\n",
                    $name,
                    $punch_in_time,
                    $punch_out_time,
                    $status
                );
            }
        }
    }

    if ( ! $has_logs ) {
        $report_lines[] = "No attendance logs recorded for today.";
    }

    $admin_email = get_option( 'admin_email' );
    $subject = 'Daily Attendance Summary: ' . $today_human;
    $message = implode( "\n", $report_lines );

    wp_mail( $admin_email, $subject, $message );
}
add_action( 'cora_daily_attendance_report_event', 'cora_send_daily_attendance_report' );

// ═══ DYNAMIC REMOTE PLUGIN UPDATER SYSTEM ═══
class Cora_Plugin_Updater {
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
            $obj->slug = 'cora-studio-ai-locked';
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
        
        if ( isset( $args->slug ) && $args->slug === 'cora-studio-ai-locked' ) {
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
            $res->slug = 'cora-studio-ai-locked';
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

/**
 * AJAX: Save Studio settings (Brand Name + Updates URL)
 */
function cora_ajax_save_studio_settings() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized.' );
    }
    
    $brand_name  = sanitize_text_field( $_POST['brand_name'] ?? '' );
    $updates_url = esc_url_raw( $_POST['updates_url'] ?? '' );
    
    if ( ! empty( $brand_name ) ) {
        update_option( 'cora_studio_brand_name', $brand_name );
    }
    if ( ! empty( $updates_url ) ) {
        update_option( 'cora_updates_server_url', $updates_url );
    } else {
        delete_option( 'cora_updates_server_url' );
    }
    
    wp_send_json_success( 'Studio settings saved.' );
}
add_action( 'wp_ajax_cora_save_studio_settings', 'cora_ajax_save_studio_settings' );

$cora_updates_url = get_option( 'cora_updates_server_url', 'https://raw.githubusercontent.com/dravyafolio2021/heycora/main/updates/cora-studio-ai-locked.json' );
new Cora_Plugin_Updater( __FILE__, $cora_updates_url );



