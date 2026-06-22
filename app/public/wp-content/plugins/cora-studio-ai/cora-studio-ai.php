<?php
/**
 * Plugin Name: Cora Studio AI
 * Plugin URI: https://cora.ai
 * Description: A clean, minimal Notion-style workspace dashboard for photography studios in India and globally. Empowered with AI workflows, booking management, and photo helpers.
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
define( 'CORA_STUDIO_AI_VERSION', time() );
define( 'CORA_STUDIO_AI_PATH', plugin_dir_path( __FILE__ ) );
define( 'CORA_STUDIO_AI_URL', plugin_dir_url( __FILE__ ) );

/**
 * Add the admin menu page
 */
function cora_studio_ai_admin_menu() {
    add_menu_page(
        __( 'Cora Studio AI', 'cora-studio-ai' ),
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
            $allowed_features = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'vault', 'settings' );
        }

        // Prevent accessing disallowed sub-pages
        if ( $sub_page !== 'dashboard' && $sub_page !== 'feature-hub' && ! in_array( $sub_page, $allowed_features ) ) {
            wp_redirect( home_url( '/workspace/dashboard' ) );
            exit;
        }

        // Prevent browser caching
        nocache_headers();

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

    // Localize script to pass server variables if needed (e.g. site URL, ajaxurl)
    wp_localize_script( 'cora-admin-script', 'coraData', array(
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
 * Filter the admin page titles to remove the WordPress reference
 */
function cora_studio_ai_admin_title( $admin_title, $title ) {
    if ( isset( $_GET['page'] ) && 'cora-studio-ai' === $_GET['page'] ) {
        return __( 'Cora Studio AI', 'cora-studio-ai' );
    }
    return $admin_title;
}
add_filter( 'admin_title', 'cora_studio_ai_admin_title', 10, 2 );

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
    return __( 'Cora Studio AI', 'cora-studio-ai' );
}
add_filter( 'login_headertext', 'cora_studio_ai_login_logo_title' );

/**
 * Redirect default WP Dashboard index page (wp-admin/index.php) to Cora Studio AI
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
    return '<span>Cora Studio AI • Delhi Studio</span>';
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
            'administrator' => array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings' ),
            'cora_manager' => array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'vault' ),
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
            if ( ! in_array( 'financials', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'financials';
                $has_permission_updates = true;
            }
            if ( ! in_array( 'vault', $permissions['administrator'] ) ) {
                $permissions['administrator'][] = 'vault';
                $has_permission_updates = true;
            }
        }
        if ( is_array( $permissions ) && isset( $permissions['cora_manager'] ) ) {
            if ( ! in_array( 'vault', $permissions['cora_manager'] ) ) {
                $permissions['cora_manager'][] = 'vault';
                $has_permission_updates = true;
            }
        }
        if ( $has_permission_updates ) {
            update_option( 'cora_role_permissions', $permissions );
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
                'secured_shares' => array()
            ),
            array(
                'id' => 'doc_2',
                'title' => 'Invoice: Nitin Arora Studio - Ritz Carlton Shoot',
                'type' => 'Invoice',
                'amount' => '₹1,75,000',
                'status' => 'Paid',
                'created_date' => '2026-06-10',
                'content' => '<h3>Commercial Shoot Invoice</h3><p>Billing for the corporate photography and video assignment completed at Ritz Carlton.</p><p>Total Amount: ₹1,75,000 (Paid in full on June 12, 2026 via NEFT).</p>',
                'secured_shares' => array()
            ),
            array(
                'id' => 'doc_3',
                'title' => 'Contract: Delhi Fashion Week 2026 Agreement',
                'type' => 'Contract',
                'amount' => '₹3,20,000',
                'status' => 'Signed',
                'created_date' => '2026-06-18',
                'content' => '<h3>Delhi Fashion Week Coverage Agreement</h3><p>This contract outlines the deliverables and terms for Delhi Fashion Week coverage.</p><p><strong>Deliverables:</strong> Runway images, backstage coverage, and social media reels.</p><p><strong>Payment Terms:</strong> 50% advance (received), 50% upon delivery.</p>',
                'secured_shares' => array()
            )
        );
        update_option( 'cora_studio_documents', $initial_docs );
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
                __( "Hello,\n\nYou have been shared a secure access link for the following document: %s\n\nAccess Link: %s\nThis link is secure and will expire on: %s\n\nBest regards,\nCora Studio AI Team", "cora-studio-ai" ),
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


