<?php
/**
 * Cora Canvas - Next.js Native & Headless Integration Engine
 *
 * Enables seamless connection between modern Next.js projects (App Router / Pages Router)
 * and the Cora Workspace, providing headless REST endpoints, on-demand ISR revalidation,
 * GitHub route auto-discovery, dynamic content/props management, and lead funnels.
 *
 * @package CoraWorkspace
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Cora_NextJS_Integration' ) ) {

class Cora_NextJS_Integration {

    const REST_NAMESPACE = 'cora-canvas/v1';
    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        $this->register_ajax_actions();
    }

    private function register_ajax_actions() {
        $actions = array(
            'cora_nextjs_scan_routes',
            'cora_nextjs_trigger_isr',
            'cora_nextjs_save_page_props',
            'cora_nextjs_sync_repo'
        );

        foreach ( $actions as $action ) {
            add_action( 'wp_ajax_' . $action, array( $this, $action ) );
        }
    }

    /**
     * Register Headless REST API routes for Next.js
     */
    public function register_rest_routes() {
        // 1. Theme Manifest & Global Config
        register_rest_route( self::REST_NAMESPACE, '/nextjs/manifest', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_manifest' ),
            'permission_callback' => '__return_true',
        ) );

        // 2. Individual Page Props & SEO
        register_rest_route( self::REST_NAMESPACE, '/nextjs/page', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_page' ),
            'permission_callback' => '__return_true',
        ) );

        // 3. Next.js On-Demand ISR Revalidation Trigger
        register_rest_route( self::REST_NAMESPACE, '/nextjs/revalidate', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( $this, 'rest_trigger_revalidate' ),
            'permission_callback' => '__return_true',
        ) );

        // 4. Next.js Form Submission to Cora CRM
        register_rest_route( self::REST_NAMESPACE, '/nextjs/forms/submit', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'rest_submit_form' ),
            'permission_callback' => '__return_true',
        ) );
    }

    /**
     * REST: Get Theme Manifest (Tokens, Navigation, Routes, Global Settings)
     */
    public function rest_get_manifest( $request ) {
        global $wpdb;

        $theme_id = $request->get_param( 'theme_id' );
        if ( ! $theme_id ) {
            $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
            if ( ! $live_theme ) {
                $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes ORDER BY id DESC LIMIT 1", ARRAY_A );
            }
            $theme = $live_theme;
        } else {
            $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", intval( $theme_id ) ), ARRAY_A );
        }

        if ( ! $theme ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => 'No active Cora theme found.'
            ), 404 );
        }

        $settings = json_decode( $theme['settings'] ?? '{}', true ) ?: array();
        $pages = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, title, slug, is_homepage, status, seo_title, seo_description, seo_og_image, template, updated_at, created_at 
             FROM {$wpdb->prefix}cora_canvas_pages 
             WHERE theme_id = %d AND status != 'trash'
             ORDER BY is_homepage DESC, id ASC",
            $theme['id']
        ), ARRAY_A );

        $formatted_pages = array();
        foreach ( $pages as $p ) {
            $page_meta = get_post_meta( $p['id'], '_cora_nextjs_props', true );
            $props = ! empty( $page_meta ) ? ( is_array( $page_meta ) ? $page_meta : json_decode( $page_meta, true ) ) : array();

            $route_path = ( $p['is_homepage'] == 1 ) ? '/' : '/' . ltrim( $p['slug'], '/' );

            $formatted_pages[] = array(
                'id'              => intval( $p['id'] ),
                'title'           => $p['title'],
                'slug'            => $p['slug'],
                'route'           => $route_path,
                'is_homepage'     => (bool) $p['is_homepage'],
                'status'          => $p['status'],
                'seo'             => array(
                    'title'       => $p['seo_title'] ?: $p['title'],
                    'description' => $p['seo_description'] ?: ( $settings['site_tagline'] ?? '' ),
                    'og_image'    => $p['seo_og_image'] ?: ( $settings['site_logo'] ?? '' ),
                ),
                'props'           => $props ?: new stdClass(),
                'updated_at'      => $p['updated_at']
            );
        }

        $data = array(
            'success'      => true,
            'theme'        => array(
                'id'       => intval( $theme['id'] ),
                'name'     => $theme['name'],
                'status'   => $theme['status'],
                'source'   => $settings['source'] ?? 'nextjs',
                'repo'     => $settings['github_repo'] ?? '',
                'branch'   => $settings['github_branch'] ?? 'main',
                'live_url' => $settings['nextjs_live_url'] ?? $settings['live_url'] ?? '',
            ),
            'site'         => array(
                'title'       => $settings['site_title'] ?? get_bloginfo( 'name' ),
                'tagline'     => $settings['site_tagline'] ?? get_bloginfo( 'description' ),
                'logo'        => $settings['site_logo'] ?? '',
                'favicon'     => $settings['site_favicon'] ?? '',
                'copyright'   => $settings['copyright_text'] ?? ( '© ' . date('Y') . ' Cora. All rights reserved.' ),
            ),
            'tokens'       => array(
                'primary'      => $settings['primary_color'] ?? '#18181b',
                'secondary'    => $settings['secondary_color'] ?? '#27272a',
                'accent'       => $settings['accent_color'] ?? '#10b981',
                'text'         => $settings['text_color'] ?? '#09090b',
                'bg'           => $settings['bg_color'] ?? '#ffffff',
                'heading_font' => $settings['heading_font'] ?? 'Inter',
                'body_font'    => $settings['body_font'] ?? 'Inter',
            ),
            'pages'        => $formatted_pages,
            'api_version'  => '1.0.0',
            'generated_at' => current_time( 'c' )
        );

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * REST: Get Single Page Props and SEO
     */
    public function rest_get_page( $request ) {
        global $wpdb;

        $slug     = sanitize_title( $request->get_param( 'slug' ) );
        $route    = $request->get_param( 'route' );
        $page_id  = intval( $request->get_param( 'id' ) );
        $theme_id = intval( $request->get_param( 'theme_id' ) );

        if ( ! empty( $route ) ) {
            $trimmed = trim( $route, '/' );
            $slug = empty( $trimmed ) ? 'home' : $trimmed;
        }

        $query = "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE ";
        $params = array();

        if ( $page_id > 0 ) {
            $query .= "id = %d";
            $params[] = $page_id;
        } elseif ( $slug === 'home' || $slug === '' || $route === '/' ) {
            $query .= "is_homepage = 1";
            if ( $theme_id > 0 ) {
                $query .= " AND theme_id = %d";
                $params[] = $theme_id;
            }
        } else {
            $query .= "slug = %s";
            $params[] = $slug;
            if ( $theme_id > 0 ) {
                $query .= " AND theme_id = %d";
                $params[] = $theme_id;
            }
        }

        $query .= " AND status != 'trash' ORDER BY id DESC LIMIT 1";

        $page = ! empty( $params ) ? $wpdb->get_row( $wpdb->prepare( $query, $params ), ARRAY_A ) : $wpdb->get_row( $query, ARRAY_A );

        if ( ! $page ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => 'Page not found.'
            ), 404 );
        }

        $page_meta = get_post_meta( $page['id'], '_cora_nextjs_props', true );
        $props = ! empty( $page_meta ) ? ( is_array( $page_meta ) ? $page_meta : json_decode( $page_meta, true ) ) : array();

        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $page['theme_id'] ), ARRAY_A );
        $theme_settings = json_decode( $theme['settings'] ?? '{}', true ) ?: array();

        $route_path = ( $page['is_homepage'] == 1 ) ? '/' : '/' . ltrim( $page['slug'], '/' );

        return new WP_REST_Response( array(
            'success'     => true,
            'page'        => array(
                'id'          => intval( $page['id'] ),
                'theme_id'    => intval( $page['theme_id'] ),
                'title'       => $page['title'],
                'slug'        => $page['slug'],
                'route'       => $route_path,
                'is_homepage' => (bool) $page['is_homepage'],
                'status'      => $page['status'],
                'seo'         => array(
                    'title'          => $page['seo_title'] ?: $page['title'],
                    'description'    => $page['seo_description'] ?: ( $theme_settings['site_tagline'] ?? '' ),
                    'og_image'       => $page['seo_og_image'] ?: ( $theme_settings['site_logo'] ?? '' ),
                    'canonical_url'  => ( $theme_settings['nextjs_live_url'] ?? '' ) . $route_path,
                ),
                'props'       => $props ?: new stdClass(),
                'updated_at'  => $page['updated_at']
            )
        ), 200 );
    }

    /**
     * REST: Trigger On-Demand ISR Revalidation on Next.js Deployment
     */
    public function rest_trigger_revalidate( $request ) {
        $secret   = sanitize_text_field( $request->get_param( 'secret' ) );
        $path     = sanitize_text_field( $request->get_param( 'path' ) ?: '/' );
        $theme_id = intval( $request->get_param( 'theme_id' ) );

        global $wpdb;
        $theme = null;
        if ( $theme_id > 0 ) {
            $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        } else {
            $theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
        }

        if ( ! $theme ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Theme not found.' ), 404 );
        }

        $settings    = json_decode( $theme['settings'] ?? '{}', true ) ?: array();
        $live_url    = rtrim( $settings['nextjs_live_url'] ?? $settings['live_url'] ?? '', '/' );
        $auth_secret = $settings['nextjs_revalidate_secret'] ?? get_option( 'cora_nextjs_revalidate_secret', '' );

        if ( ! empty( $auth_secret ) && $secret !== $auth_secret ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Invalid revalidation secret.' ), 403 );
        }

        if ( empty( $live_url ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'No live Next.js deployment URL configured.' ), 400 );
        }

        $start_time = microtime( true );
        $target_url = $live_url . '/api/revalidate?' . http_build_query( array(
            'secret' => $auth_secret,
            'path'   => $path
        ) );

        $res = wp_remote_post( $target_url, array(
            'timeout' => 15,
            'headers' => array( 'User-Agent' => 'Cora-Workspace-ISR/1.0' )
        ) );

        $duration_ms = round( ( microtime( true ) - $start_time ) * 1000 );

        if ( is_wp_error( $res ) ) {
            return new WP_REST_Response( array(
                'success'     => false,
                'message'     => 'Failed to reach Next.js webhook: ' . $res->get_error_message(),
                'target_url'  => $target_url,
                'duration_ms' => $duration_ms
            ), 500 );
        }

        $code = wp_remote_retrieve_response_code( $res );
        $body = wp_remote_retrieve_body( $res );

        return new WP_REST_Response( array(
            'success'     => ( $code >= 200 && $code < 300 ),
            'http_code'   => $code,
            'path'        => $path,
            'duration_ms' => $duration_ms,
            'response'    => json_decode( $body, true ) ?: $body
        ), ( $code >= 200 && $code < 300 ) ? 200 : 400 );
    }

    /**
     * REST: Handle Next.js Form Submission into Cora Leads CRM
     */
    public function rest_submit_form( $request ) {
        global $wpdb;

        $body = $request->get_json_params() ?: $request->get_body_params();
        $name    = sanitize_text_field( $body['name'] ?? $body['full_name'] ?? 'Anonymous' );
        $email   = sanitize_email( $body['email'] ?? '' );
        $phone   = sanitize_text_field( $body['phone'] ?? $body['telephone'] ?? '' );
        $message = sanitize_textarea_field( $body['message'] ?? $body['notes'] ?? '' );
        $source  = sanitize_text_field( $body['source'] ?? 'Next.js Frontend' );
        $form_id = sanitize_text_field( $body['form_id'] ?? 'contact' );

        if ( empty( $email ) && empty( $phone ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => 'Please provide at least an email address or phone number.'
            ), 400 );
        }

        // Insert into Cora leads table if present
        $leads_table = $wpdb->prefix . 'cora_leads';
        $inserted = false;

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $leads_table ) ) {
            $inserted = $wpdb->insert(
                $leads_table,
                array(
                    'agency_id'   => 1,
                    'name'        => $name,
                    'email'       => $email,
                    'phone'       => $phone,
                    'notes'       => $message,
                    'source'      => $source,
                    'status'      => 'new',
                    'created_at'  => current_time( 'mysql' ),
                    'updated_at'  => current_time( 'mysql' )
                ),
                array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
            );
        }

        cora_log_activity( 'Lead Funnel', "New lead received from Next.js form: {$name} ({$email})" );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => 'Thank you! Your submission has been received.',
            'lead_id' => $inserted ? $wpdb->insert_id : null
        ), 200 );
    }

    /**
     * AJAX: Scan GitHub Repository for Next.js Routes
     */
    public function cora_nextjs_scan_routes() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

        $repo_raw = isset( $_POST['repo'] ) ? sanitize_text_field( wp_unslash( $_POST['repo'] ) ) : '';
        $branch   = isset( $_POST['branch'] ) ? sanitize_text_field( wp_unslash( $_POST['branch'] ) ) : 'main';
        $pat      = isset( $_POST['pat'] ) ? sanitize_text_field( wp_unslash( $_POST['pat'] ) ) : '';

        if ( empty( $repo_raw ) ) {
            wp_send_json_error( array( 'message' => 'GitHub repository URL or owner/repo is required.' ) );
        }

        // Extract owner/repo
        $repo_slug = '';
        if ( preg_match( '/github\.com\/([^\/]+)\/([^\/\#\?]+)/i', $repo_raw, $m ) ) {
            $repo_slug = trim( $m[1] ) . '/' . trim( preg_replace( '/\.git$/i', '', $m[2] ) );
        } elseif ( count( explode( '/', trim( $repo_raw, '/' ) ) ) === 2 ) {
            $repo_slug = trim( $repo_raw, '/' );
        } else {
            wp_send_json_error( array( 'message' => 'Invalid repository format. Please enter owner/repo or full GitHub URL.' ) );
        }

        if ( empty( $pat ) ) {
            $pat = get_user_meta( get_current_user_id(), 'cora_github_access_token', true ) ?: get_option( 'cora_git_sync_token', '' );
        }

        if ( empty( $pat ) ) {
            wp_send_json_error( array( 'message' => 'GitHub Personal Access Token (PAT) is required.' ) );
        }

        // Save token for workspace convenience
        update_option( 'cora_git_sync_token', $pat );
        update_user_meta( get_current_user_id(), 'cora_github_access_token', $pat );

        $headers = array(
            'Authorization'      => 'Bearer ' . $pat,
            'Accept'             => 'application/vnd.github+json',
            'User-Agent'         => 'Cora-Platform-NextJS/1.0',
            'X-GitHub-Api-Version' => '2022-11-28'
        );

        // 1. Fetch Repository Info & Validate Access
        $repo_resp = wp_remote_get( "https://api.github.com/repos/{$repo_slug}", array( 'headers' => $headers, 'timeout' => 15 ) );
        if ( is_wp_error( $repo_resp ) || wp_remote_retrieve_response_code( $repo_resp ) !== 200 ) {
            $err_msg = is_wp_error( $repo_resp ) ? $repo_resp->get_error_message() : 'Repository not accessible. Verify token permissions and repo name.';
            wp_send_json_error( array( 'message' => $err_msg ) );
        }

        // 2. Fetch Recursive Git Tree
        $tree_resp = wp_remote_get( "https://api.github.com/repos/{$repo_slug}/git/trees/{$branch}?recursive=1", array( 'headers' => $headers, 'timeout' => 25 ) );
        if ( is_wp_error( $tree_resp ) || wp_remote_retrieve_response_code( $tree_resp ) !== 200 ) {
            wp_send_json_error( array( 'message' => "Could not inspect branch '{$branch}'. Check branch name." ) );
        }

        $tree_data = json_decode( wp_remote_retrieve_body( $tree_resp ), true );
        $tree_files = $tree_data['tree'] ?? array();

        $routes = array();
        $is_app_router = false;
        $is_pages_router = false;
        $has_tailwind = false;
        $has_typescript = false;

        foreach ( $tree_files as $file ) {
            $path = $file['path'];

            if ( preg_match( '/\.(ts|tsx)$/', $path ) ) $has_typescript = true;
            if ( preg_match( '/tailwind\.config\./', $path ) ) $has_tailwind = true;

            // Next.js App Router (app/page.tsx, src/app/about/page.tsx)
            if ( preg_match( '#^(?:src/)?app/(.+/)?page\.(tsx|jsx|js|ts)$#i', $path, $matches ) ) {
                $is_app_router = true;
                $sub_path = isset( $matches[1] ) ? trim( $matches[1], '/' ) : '';
                
                // Skip route groups like (marketing), (auth) in clean URL calculation
                $cleaned_segments = array();
                if ( ! empty( $sub_path ) ) {
                    $segments = explode( '/', $sub_path );
                    foreach ( $segments as $seg ) {
                        if ( ! preg_match( '/^\(.*\)$/', $seg ) ) {
                            $cleaned_segments[] = $seg;
                        }
                    }
                }

                $clean_route = empty( $cleaned_segments ) ? '/' : '/' . implode( '/', $cleaned_segments );
                $slug = empty( $cleaned_segments ) ? 'home' : implode( '-', $cleaned_segments );

                $is_dynamic = preg_match( '/\[.*\]/', $clean_route );
                $title = self::format_route_title( $clean_route, $slug );

                $routes[ $clean_route ] = array(
                    'path'        => $clean_route,
                    'slug'        => $slug,
                    'title'       => $title,
                    'file'        => $path,
                    'router'      => 'app',
                    'is_dynamic'  => $is_dynamic,
                    'is_homepage' => ( $clean_route === '/' )
                );
            }

            // Next.js Pages Router (pages/index.tsx, pages/about.tsx)
            if ( preg_match( '#^(?:src/)?pages/(.+)\.(tsx|jsx|js|ts)$#i', $path, $matches ) ) {
                $sub = $matches[1];
                if ( ! preg_match( '#^(_app|_document|_error|api/)#i', $sub ) ) {
                    $is_pages_router = true;
                    $clean_route = ( $sub === 'index' ) ? '/' : '/' . $sub;
                    $slug = ( $sub === 'index' ) ? 'home' : str_replace( '/', '-', $sub );
                    $is_dynamic = preg_match( '/\[.*\]/', $clean_route );
                    $title = self::format_route_title( $clean_route, $slug );

                    if ( ! isset( $routes[ $clean_route ] ) ) {
                        $routes[ $clean_route ] = array(
                            'path'        => $clean_route,
                            'slug'        => $slug,
                            'title'       => $title,
                            'file'        => $path,
                            'router'      => 'pages',
                            'is_dynamic'  => $is_dynamic,
                            'is_homepage' => ( $clean_route === '/' )
                        );
                    }
                }
            }
        }

        // Always ensure Home route exists
        if ( empty( $routes['/'] ) ) {
            $routes['/'] = array(
                'path'        => '/',
                'slug'        => 'home',
                'title'       => 'Home',
                'file'        => 'app/page.tsx',
                'router'      => 'app',
                'is_dynamic'  => false,
                'is_homepage' => true
            );
        }

        // Format into indexed list sorted with homepage first
        $route_list = array_values( $routes );
        usort( $route_list, function( $a, $b ) {
            if ( $a['is_homepage'] ) return -1;
            if ( $b['is_homepage'] ) return 1;
            return strcmp( $a['path'], $b['path'] );
        } );

        $framework_name = $is_app_router ? 'Next.js 14/15 (App Router)' : ( $is_pages_router ? 'Next.js (Pages Router)' : 'Next.js React Native' );

        wp_send_json_success( array(
            'repo'           => $repo_slug,
            'branch'         => $branch,
            'framework'      => $framework_name,
            'is_app_router'  => $is_app_router,
            'has_typescript' => $has_typescript,
            'has_tailwind'   => $has_tailwind,
            'routes_count'   => count( $route_list ),
            'routes'         => $route_list
        ) );
    }

    private static function format_route_title( $route, $slug ) {
        if ( $route === '/' ) return 'Home';
        $clean = trim( $slug, '/' );
        $clean = preg_replace( '/[\[\]]/', '', $clean );
        $clean = str_replace( array( '-', '_' ), ' ', $clean );
        return ucwords( $clean );
    }

    /**
     * AJAX: Trigger ISR Revalidation from Canvas Workspace
     */
    public function cora_nextjs_trigger_isr() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

        $theme_id = intval( $_POST['theme_id'] ?? 0 );
        $path     = sanitize_text_field( $_POST['path'] ?? '/' );

        global $wpdb;
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );

        if ( ! $theme ) {
            wp_send_json_error( array( 'message' => 'Theme workspace not found.' ) );
        }

        $settings = json_decode( $theme['settings'] ?? '{}', true ) ?: array();
        $live_url = rtrim( $settings['nextjs_live_url'] ?? $settings['live_url'] ?? '', '/' );
        $secret   = $settings['nextjs_revalidate_secret'] ?? get_option( 'cora_nextjs_revalidate_secret', '' );

        if ( empty( $live_url ) ) {
            wp_send_json_error( array( 'message' => 'Please configure your Live Next.js Deployment URL in Theme Settings.' ) );
        }

        $start_time = microtime( true );
        $webhook_url = $live_url . '/api/revalidate?' . http_build_query( array(
            'secret' => $secret,
            'path'   => $path
        ) );

        $res = wp_remote_post( $webhook_url, array(
            'timeout' => 12,
            'headers' => array( 'User-Agent' => 'Cora-Canvas-ISR/1.0' )
        ) );

        $duration_ms = round( ( microtime( true ) - $start_time ) * 1000 );

        if ( is_wp_error( $res ) ) {
            wp_send_json_error( array(
                'message'     => 'Webhook request failed: ' . $res->get_error_message(),
                'webhook_url' => $webhook_url,
                'duration_ms' => $duration_ms
            ) );
        }

        $code = wp_remote_retrieve_response_code( $res );
        $body = wp_remote_retrieve_body( $res );
        $json = json_decode( $body, true );

        if ( $code >= 200 && $code < 300 ) {
            wp_send_json_success( array(
                'message'     => "ISR Revalidation successful for '{$path}' ({$duration_ms}ms)",
                'status_code' => $code,
                'duration_ms' => $duration_ms,
                'response'    => $json ?: $body
            ) );
        } else {
            wp_send_json_error( array(
                'message'     => "Next.js returned HTTP {$code}. Ensure your /api/revalidate handler matches the secret.",
                'status_code' => $code,
                'response'    => $json ?: $body
            ) );
        }
    }

    /**
     * AJAX: Save Page Props & Headless Content Block
     */
    public function cora_nextjs_save_page_props() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

        $page_id  = intval( $_POST['page_id'] ?? 0 );
        $theme_id = intval( $_POST['theme_id'] ?? 0 );
        $props_raw = $_POST['props'] ?? '{}';
        $props = is_array( $props_raw ) ? $props_raw : ( json_decode( wp_unslash( $props_raw ), true ) ?: array() );

        if ( ! $page_id ) {
            wp_send_json_error( array( 'message' => 'Invalid page ID.' ) );
        }

        // Save props into page meta
        update_post_meta( $page_id, '_cora_nextjs_props', $props );

        // Update timestamps
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'cora_canvas_pages',
            array( 'updated_at' => current_time( 'mysql' ) ),
            array( 'id' => $page_id )
        );

        // If auto-revalidate is enabled, trigger ISR for this page
        $auto_revalidate = ! empty( $_POST['auto_revalidate'] );
        $revalidated = false;

        if ( $auto_revalidate && $theme_id > 0 ) {
            $page = $wpdb->get_row( $wpdb->prepare( "SELECT slug, is_homepage FROM {$wpdb->prefix}cora_canvas_pages WHERE id = %d", $page_id ), ARRAY_A );
            if ( $page ) {
                $path = ( $page['is_homepage'] == 1 ) ? '/' : '/' . ltrim( $page['slug'], '/' );
                $theme = $wpdb->get_row( $wpdb->prepare( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
                $settings = json_decode( $theme['settings'] ?? '{}', true ) ?: array();
                $live_url = rtrim( $settings['nextjs_live_url'] ?? $settings['live_url'] ?? '', '/' );
                $secret = $settings['nextjs_revalidate_secret'] ?? '';

                if ( ! empty( $live_url ) && ! empty( $secret ) ) {
                    wp_remote_post( $live_url . '/api/revalidate?' . http_build_query( array( 'secret' => $secret, 'path' => $path ) ), array( 'timeout' => 5 ) );
                    $revalidated = true;
                }
            }
        }

        wp_send_json_success( array(
            'message'     => 'Page props saved successfully.' . ( $revalidated ? ' Live ISR cache revalidated.' : '' ),
            'revalidated' => $revalidated
        ) );
    }

    /**
     * AJAX: Resync Next.js Routes from GitHub
     */
    public function cora_nextjs_sync_repo() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

        $theme_id = intval( $_POST['theme_id'] ?? 0 );
        if ( ! $theme_id ) {
            wp_send_json_error( array( 'message' => 'Theme ID is required.' ) );
        }

        global $wpdb;
        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );

        if ( ! $theme ) {
            wp_send_json_error( array( 'message' => 'Theme not found.' ) );
        }

        $settings = json_decode( $theme['settings'] ?? '{}', true ) ?: array();
        $repo     = $settings['github_repo'] ?? '';
        $branch   = $settings['github_branch'] ?? 'main';
        $pat      = $settings['lovable_pat'] ?? get_option( 'cora_git_sync_token', '' );

        if ( empty( $repo ) ) {
            wp_send_json_error( array( 'message' => 'No GitHub repository linked to this theme.' ) );
        }

        $_POST['repo']   = $repo;
        $_POST['branch'] = $branch;
        $_POST['pat']    = $pat;

        // Perform scan internally
        $this->cora_nextjs_scan_routes();
    }
}

Cora_NextJS_Integration::get_instance();

}
