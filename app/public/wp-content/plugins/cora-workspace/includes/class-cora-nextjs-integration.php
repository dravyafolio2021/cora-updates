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
            'cora_nextjs_sync_repo',
            'cora_nextjs_save_live_url',
            'cora_nextjs_build_repo',
            'cora_nextjs_github_webhook'
        );

        foreach ( $actions as $action ) {
            add_action( 'wp_ajax_' . $action, array( $this, $action ) );
            if ( $action === 'cora_nextjs_github_webhook' ) {
                add_action( 'wp_ajax_nopriv_' . $action, array( $this, $action ) );
            }
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

        // 5. GitHub Automated Push Webhook -> Auto-Rebuild Static Site
        register_rest_route( self::REST_NAMESPACE, '/nextjs/github-webhook', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( $this, 'rest_github_webhook' ),
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
     * Core: Scan GitHub Repository for Next.js Routes
     *
     * @param string $repo_raw
     * @param string $branch
     * @param string $pat
     * @return array|WP_Error
     */
    public static function scan_repository_routes( $repo_raw, $branch = 'main', $pat = '' ) {
        if ( empty( $repo_raw ) ) {
            return new WP_Error( 'missing_repo', 'GitHub repository URL or owner/repo is required.' );
        }

        // Extract owner/repo
        $repo_slug = '';
        if ( preg_match( '/github\.com\/([^\/]+)\/([^\/\#\?]+)/i', $repo_raw, $m ) ) {
            $repo_slug = trim( $m[1] ) . '/' . trim( preg_replace( '/\.git$/i', '', $m[2] ) );
        } elseif ( count( explode( '/', trim( $repo_raw, '/' ) ) ) === 2 ) {
            $repo_slug = trim( $repo_raw, '/' );
        } else {
            return new WP_Error( 'invalid_repo_format', 'Invalid repository format. Please enter owner/repo or full GitHub URL.' );
        }

        if ( empty( $pat ) ) {
            $pat = get_user_meta( get_current_user_id(), 'cora_github_access_token', true ) ?: get_option( 'cora_git_sync_token', '' );
        }

        if ( empty( $pat ) ) {
            return new WP_Error( 'missing_pat', 'GitHub Personal Access Token (PAT) is required.' );
        }

        // Save token for workspace convenience
        update_option( 'cora_git_sync_token', $pat );
        $pat = trim( $pat );
        $headers = array(
            'Authorization'        => ( str_starts_with( $pat, 'github_pat_' ) ? 'Bearer ' : 'token ' ) . $pat,
            'Accept'               => 'application/vnd.github+json',
            'User-Agent'           => 'Cora-Platform-NextJS/1.0',
            'X-GitHub-Api-Version' => '2022-11-28'
        );

        // 1. Fetch Repository Info & Validate Access (with fallback auth header)
        $repo_resp = wp_remote_get( "https://api.github.com/repos/{$repo_slug}", array( 'headers' => $headers, 'timeout' => 15 ) );
        if ( ! is_wp_error( $repo_resp ) && in_array( wp_remote_retrieve_response_code( $repo_resp ), array( 401, 404 ) ) ) {
            // Try alternative Bearer format if token failed
            $alt_headers = $headers;
            $alt_headers['Authorization'] = ( str_starts_with( $headers['Authorization'], 'Bearer ' ) ? 'token ' : 'Bearer ' ) . $pat;
            $alt_resp = wp_remote_get( "https://api.github.com/repos/{$repo_slug}", array( 'headers' => $alt_headers, 'timeout' => 15 ) );
            if ( ! is_wp_error( $alt_resp ) && wp_remote_retrieve_response_code( $alt_resp ) === 200 ) {
                $repo_resp = $alt_resp;
                $headers = $alt_headers;
            }
        }

        if ( is_wp_error( $repo_resp ) ) {
            return new WP_Error( 'github_conn_failed', 'Failed to connect to GitHub: ' . $repo_resp->get_error_message() );
        }

        $repo_code = wp_remote_retrieve_response_code( $repo_resp );
        if ( $repo_code === 401 ) {
            return new WP_Error( 'github_unauthorized', 'Invalid or expired GitHub Personal Access Token (401 Unauthorized).' );
        } elseif ( $repo_code === 404 ) {
            return new WP_Error( 'github_not_found', "Repository '{$repo_slug}' not found or is private. If it is private, please ensure your GitHub PAT has the 'repo' scope (Classic) or 'Contents: Read-only' (Fine-Grained)." );
        } elseif ( $repo_code === 403 ) {
            return new WP_Error( 'github_forbidden', 'Access denied by GitHub (403 Forbidden). Rate limit exceeded or token lacks required permissions.' );
        } elseif ( $repo_code !== 200 ) {
            return new WP_Error( 'github_api_error', "GitHub API error (HTTP {$repo_code}). Verify token permissions and repo name." );
        }

        $repo_data = json_decode( wp_remote_retrieve_body( $repo_resp ), true ) ?: array();
        $default_branch = $repo_data['default_branch'] ?? 'main';
        $is_private = ! empty( $repo_data['private'] );

        $discovered_live_url = '';
        if ( ! empty( $repo_data['homepage'] ) && filter_var( $repo_data['homepage'], FILTER_VALIDATE_URL ) ) {
            $discovered_live_url = rtrim( trim( $repo_data['homepage'] ), '/' );
        }

        // Also query GitHub deployments for any active production deployment URLs if homepage is empty
        if ( empty( $discovered_live_url ) ) {
            $dep_resp = wp_remote_get( "https://api.github.com/repos/{$repo_slug}/deployments?per_page=5", array( 'headers' => $headers, 'timeout' => 10 ) );
            if ( ! is_wp_error( $dep_resp ) && wp_remote_retrieve_response_code( $dep_resp ) === 200 ) {
                $deps = json_decode( wp_remote_retrieve_body( $dep_resp ), true ) ?: array();
                if ( is_array( $deps ) ) {
                    foreach ( $deps as $dep ) {
                        if ( ! empty( $dep['id'] ) ) {
                            $status_resp = wp_remote_get( "https://api.github.com/repos/{$repo_slug}/deployments/{$dep['id']}/statuses?per_page=1", array( 'headers' => $headers, 'timeout' => 10 ) );
                            if ( ! is_wp_error( $status_resp ) && wp_remote_retrieve_response_code( $status_resp ) === 200 ) {
                                $statuses = json_decode( wp_remote_retrieve_body( $status_resp ), true ) ?: array();
                                if ( ! empty( $statuses[0]['environment_url'] ) && filter_var( $statuses[0]['environment_url'], FILTER_VALIDATE_URL ) ) {
                                    $discovered_live_url = rtrim( trim( $statuses[0]['environment_url'] ), '/' );
                                    break;
                                } elseif ( ! empty( $statuses[0]['target_url'] ) && filter_var( $statuses[0]['target_url'], FILTER_VALIDATE_URL ) && false === strpos( $statuses[0]['target_url'], 'github.com' ) ) {
                                    $discovered_live_url = rtrim( trim( $statuses[0]['target_url'] ), '/' );
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        // 2. Fetch Branches Roster
        $branches_list = array();
        $branches_resp = wp_remote_get( "https://api.github.com/repos/{$repo_slug}/branches?per_page=100", array( 'headers' => $headers, 'timeout' => 15 ) );
        if ( ! is_wp_error( $branches_resp ) && wp_remote_retrieve_response_code( $branches_resp ) === 200 ) {
            $branches_data = json_decode( wp_remote_retrieve_body( $branches_resp ), true ) ?: array();
            foreach ( $branches_data as $b_item ) {
                if ( ! empty( $b_item['name'] ) ) {
                    $branches_list[] = $b_item['name'];
                }
            }
        }

        if ( empty( $branches_list ) ) {
            $branches_list = array( $default_branch );
        } else {
            // Place default branch first
            if ( in_array( $default_branch, $branches_list ) ) {
                $branches_list = array_values( array_unique( array_merge( array( $default_branch ), $branches_list ) ) );
            }
        }

        // Use selected branch or fallback to default
        if ( empty( $branch ) || ( ! in_array( $branch, $branches_list ) && ! empty( $branches_list ) ) ) {
            $branch = $default_branch;
        }

        // 3. Fetch Recursive Git Tree
        $tree_resp = wp_remote_get( "https://api.github.com/repos/{$repo_slug}/git/trees/{$branch}?recursive=1", array( 'headers' => $headers, 'timeout' => 25 ) );
        if ( is_wp_error( $tree_resp ) || wp_remote_retrieve_response_code( $tree_resp ) !== 200 ) {
            // Fallback retry with default branch if different
            if ( $branch !== $default_branch ) {
                $branch = $default_branch;
                $tree_resp = wp_remote_get( "https://api.github.com/repos/{$repo_slug}/git/trees/{$branch}?recursive=1", array( 'headers' => $headers, 'timeout' => 25 ) );
            }
        }

        if ( is_wp_error( $tree_resp ) || wp_remote_retrieve_response_code( $tree_resp ) !== 200 ) {
            return new WP_Error( 'github_tree_failed', "Could not inspect branch '{$branch}'. Check branch name or Git permissions.", array(
                'branches'       => $branches_list,
                'default_branch' => $default_branch,
                'is_private'     => $is_private
            ) );
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

            // Next.js App Router (app/page.tsx, src/app/about/page.tsx, cora-frontend/app/page.tsx)
            if ( preg_match( '#^(?:.+/)?(?:src/)?app/(.+/)?page\.(tsx|jsx|js|ts)$#i', $path, $matches ) ) {
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

            // Next.js Pages Router (pages/index.tsx, pages/about.tsx, cora-frontend/pages/index.tsx)
            if ( preg_match( '#^(?:.+/)?(?:src/)?pages/(.+)\.(tsx|jsx|js|ts)$#i', $path, $matches ) ) {
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

        return array(
            'repo'                => $repo_slug,
            'branch'              => $branch,
            'default_branch'      => $default_branch,
            'branches'            => $branches_list,
            'is_private'          => $is_private,
            'framework'           => $framework_name,
            'is_app_router'       => $is_app_router,
            'has_typescript'      => $has_typescript,
            'has_tailwind'        => $has_tailwind,
            'discovered_live_url' => $discovered_live_url,
            'routes_count'        => count( $route_list ),
            'routes'              => $route_list
        );
    }

    /**
     * AJAX: Scan GitHub Repository for Next.js Routes
     */
    public function cora_nextjs_scan_routes() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

        $repo_raw = isset( $_POST['repo'] ) ? sanitize_text_field( wp_unslash( $_POST['repo'] ) ) : '';
        $branch   = isset( $_POST['branch'] ) ? sanitize_text_field( wp_unslash( $_POST['branch'] ) ) : 'main';
        $pat      = isset( $_POST['pat'] ) ? sanitize_text_field( wp_unslash( $_POST['pat'] ) ) : '';

        $result = self::scan_repository_routes( $repo_raw, $branch, $pat );

        if ( is_wp_error( $result ) ) {
            $data = array( 'message' => $result->get_error_message() );
            $err_data = $result->get_error_data();
            if ( is_array( $err_data ) ) {
                $data = array_merge( $data, $err_data );
            }
            wp_send_json_error( $data );
        }

        wp_send_json_success( $result );
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

    /**
     * AJAX: Save Next.js Live Deployment / Local Dev URL
     */
    public function cora_nextjs_save_live_url() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : ( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : '' );
        if ( ! wp_verify_nonce( $nonce, 'cora_ajax_nonce' ) && ! wp_verify_nonce( $nonce, 'wp_rest' ) && ! check_ajax_referer( 'cora_ajax_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page.' ) );
        }

        if ( ! current_user_can( 'edit_theme_options' ) && ! current_user_can( 'manage_options' ) && ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => 'Permission denied. Please log in.' ) );
        }

        $theme_id = intval( $_POST['theme_id'] ?? 0 );
        $live_url = trim( wp_unslash( $_POST['live_url'] ?? '' ) );

        if ( ! empty( $live_url ) ) {
            if ( ! preg_match( '#^https?://#i', $live_url ) ) {
                $live_url = 'http://' . $live_url;
            }
            $live_url = rtrim( $live_url, '/' );
            if ( ! filter_var( $live_url, FILTER_VALIDATE_URL ) && strpos( $live_url, 'localhost' ) === false ) {
                wp_send_json_error( array( 'message' => 'Invalid URL format. Please provide a valid HTTP or HTTPS URL.' ) );
            }
        }

        global $wpdb;
        if ( $theme_id <= 0 ) {
            $live_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' ORDER BY id DESC LIMIT 1", ARRAY_A );
            if ( ! $live_theme ) {
                $live_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes ORDER BY id DESC LIMIT 1", ARRAY_A );
            }
            $theme_id = $live_theme ? intval( $live_theme['id'] ) : 0;
        }

        if ( $theme_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'Theme ID is required.' ) );
        }

        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );

        if ( ! $theme ) {
            wp_send_json_error( array( 'message' => 'Theme not found.' ) );
        }

        $settings = json_decode( $theme['settings'] ?? '{}', true ) ?: array();
        $settings['source']          = 'nextjs';
        $settings['nextjs_live_url'] = $live_url;
        $settings['live_url']        = $live_url;

        $wpdb->update(
            "{$wpdb->prefix}cora_canvas_themes",
            array(
                'settings'   => wp_json_encode( $settings ),
                'updated_at' => current_time( 'mysql' )
            ),
            array( 'id' => $theme_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        wp_send_json_success( array(
            'message'  => 'Next.js live URL saved successfully.',
            'live_url' => $live_url,
            'theme_id' => $theme_id,
        ) );
    }

    /**
     * AJAX: Build Connected Repository (Expo, Next.js, Vite, React) into Static Site
     */
    public function cora_nextjs_build_repo() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : ( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : '' );
        if ( ! wp_verify_nonce( $nonce, 'cora_ajax_nonce' ) && ! wp_verify_nonce( $nonce, 'wp_rest' ) && ! check_ajax_referer( 'cora_ajax_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page.' ) );
        }

        if ( ! current_user_can( 'edit_theme_options' ) && ! current_user_can( 'manage_options' ) && ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => 'Permission denied. Please log in.' ) );
        }

        global $wpdb;
        $theme_id = intval( $_POST['theme_id'] ?? 0 );
        if ( $theme_id <= 0 ) {
            $live_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' ORDER BY id DESC LIMIT 1", ARRAY_A );
            if ( ! $live_theme ) {
                $live_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes ORDER BY id DESC LIMIT 1", ARRAY_A );
            }
            $theme_id = $live_theme ? intval( $live_theme['id'] ) : 0;
        }

        if ( $theme_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'Theme ID is required.' ) );
        }

        $result = self::execute_static_build( $theme_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message'    => $result->get_error_message(),
                'error_code' => $result->get_error_code(),
                'logs'       => $result->get_error_data()
            ) );
        }

        wp_send_json_success( $result );
    }

    /**
     * REST: GitHub Webhook to trigger auto-rebuild on git push
     */
    public function rest_github_webhook( $request ) {
        $body = $request->get_json_params() ?: $request->get_body_params();
        $theme_id = intval( $request->get_param( 'theme_id' ) ?: 0 );

        global $wpdb;
        if ( $theme_id <= 0 && ! empty( $body['repository']['full_name'] ) ) {
            $repo_slug = trim( $body['repository']['full_name'] );
            $match_theme = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE settings LIKE %s ORDER BY id DESC LIMIT 1", '%' . $wpdb->esc_like( $repo_slug ) . '%' ), ARRAY_A );
            if ( $match_theme ) {
                $theme_id = intval( $match_theme['id'] );
            }
        }

        if ( $theme_id <= 0 ) {
            $live_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' ORDER BY id DESC LIMIT 1", ARRAY_A );
            $theme_id = $live_theme ? intval( $live_theme['id'] ) : 0;
        }

        if ( $theme_id <= 0 ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Target theme not found.' ), 404 );
        }

        $result = self::execute_static_build( $theme_id );
        if ( is_wp_error( $result ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => $result->get_error_message(), 'logs' => $result->get_error_data() ), 500 );
        }

        return new WP_REST_Response( array( 'success' => true, 'message' => 'Static build completed from GitHub webhook.', 'data' => $result ), 200 );
    }

    /**
     * AJAX: Public / NoPriv GitHub Webhook Handler
     */
    public function cora_nextjs_github_webhook() {
        $theme_id = intval( $_GET['theme_id'] ?? ( $_POST['theme_id'] ?? 0 ) );
        $body = json_decode( file_get_contents( 'php://input' ), true ) ?: $_POST;

        global $wpdb;
        if ( $theme_id <= 0 && ! empty( $body['repository']['full_name'] ) ) {
            $repo_slug = trim( $body['repository']['full_name'] );
            $match_theme = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE settings LIKE %s ORDER BY id DESC LIMIT 1", '%' . $wpdb->esc_like( $repo_slug ) . '%' ), ARRAY_A );
            if ( $match_theme ) {
                $theme_id = intval( $match_theme['id'] );
            }
        }

        if ( $theme_id <= 0 ) {
            $live_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' ORDER BY id DESC LIMIT 1", ARRAY_A );
            $theme_id = $live_theme ? intval( $live_theme['id'] ) : 0;
        }

        if ( $theme_id <= 0 ) {
            wp_send_json_error( array( 'message' => 'Target theme not found.' ), 404 );
        }

        $result = self::execute_static_build( $theme_id );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message(), 'logs' => $result->get_error_data() ), 500 );
        }

        wp_send_json_success( $result );
    }

    /**
     * Core: Execute Static Build & Prerendering for a Canvas Theme
     *
     * @param int $theme_id
     * @return array|WP_Error
     */
    public static function execute_static_build( $theme_id ) {
        global $wpdb;
        $theme_id = intval( $theme_id );

        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        if ( ! $theme ) {
            return new WP_Error( 'theme_not_found', 'Theme workspace not found.' );
        }

        $settings = json_decode( $theme['settings'] ?? '{}', true ) ?: array();
        $repo_raw = $settings['github_repo'] ?? '';
        $branch   = $settings['github_branch'] ?? 'main';
        $pat      = $settings['lovable_pat'] ?? ( $settings['github_pat'] ?? get_option( 'cora_git_sync_token', '' ) );

        if ( empty( $repo_raw ) ) {
            return new WP_Error( 'missing_repo', 'No GitHub repository connected to this theme.' );
        }

        // Parse owner/repo
        $repo_slug = '';
        if ( preg_match( '/github\.com\/([^\/]+)\/([^\/\#\?]+)/i', $repo_raw, $m ) ) {
            $repo_slug = trim( $m[1] ) . '/' . trim( preg_replace( '/\.git$/i', '', $m[2] ) );
        } elseif ( count( explode( '/', trim( $repo_raw, '/' ) ) ) === 2 ) {
            $repo_slug = trim( $repo_raw, '/' );
        } else {
            return new WP_Error( 'invalid_repo', 'Invalid repository format. Please enter owner/repo or full GitHub URL.' );
        }

        $upload_dir = wp_upload_dir();
        $build_base = trailingslashit( $upload_dir['basedir'] ) . 'cora-nextjs-builds';
        $theme_build_dir = $build_base . '/theme_' . $theme_id;
        $repo_dir = $theme_build_dir . '/repo';

        if ( ! file_exists( $theme_build_dir ) ) {
            wp_mkdir_p( $theme_build_dir );
        }
        if ( ! file_exists( $repo_dir ) ) {
            wp_mkdir_p( $repo_dir );
        }

        $git_log = array();
        $git_bin = null;
        $possible_gits = array( 'git', '/usr/bin/git', '/usr/local/bin/git', '/opt/homebrew/bin/git' );
        foreach ( $possible_gits as $g ) {
            $test_out = array();
            $test_ret = 0;
            @exec( "$g --version 2>&1", $test_out, $test_ret );
            if ( $test_ret === 0 ) {
                $git_bin = $g;
                break;
            }
        }

        $auth_repo_url = ! empty( $pat ) ? "https://{$pat}@github.com/{$repo_slug}.git" : "https://github.com/{$repo_slug}.git";

        if ( $git_bin ) {
            if ( is_dir( $repo_dir . '/.git' ) ) {
                $fetch_cmd = "cd " . escapeshellarg( $repo_dir ) . " && {$git_bin} remote set-url origin " . escapeshellarg( $auth_repo_url ) . " 2>&1 && {$git_bin} fetch origin " . escapeshellarg( $branch ) . " --depth 1 2>&1 && {$git_bin} reset --hard origin/" . escapeshellarg( $branch ) . " 2>&1";
                @exec( $fetch_cmd, $git_log, $git_ret );
            } else {
                $clone_cmd = "{$git_bin} clone --depth 1 -b " . escapeshellarg( $branch ) . " " . escapeshellarg( $auth_repo_url ) . " " . escapeshellarg( $repo_dir ) . " 2>&1";
                @exec( $clone_cmd, $git_log, $git_ret );
            }
        }

        // Fallback: If git clone didn't produce package.json, try GitHub zipball archive download
        if ( ! file_exists( $repo_dir . '/package.json' ) && ! glob( $repo_dir . '/*/package.json' ) ) {
            $zip_url = "https://api.github.com/repos/{$repo_slug}/zipball/" . urlencode( $branch );
            $headers = array(
                'User-Agent' => 'Cora-Workspace-Build/1.0',
                'Accept'     => 'application/vnd.github+json',
            );
            if ( ! empty( $pat ) ) {
                $headers['Authorization'] = ( str_starts_with( $pat, 'github_pat_' ) ? 'Bearer ' : 'token ' ) . $pat;
            }

            $zip_res = wp_remote_get( $zip_url, array( 'headers' => $headers, 'timeout' => 60 ) );
            if ( ! is_wp_error( $zip_res ) && wp_remote_retrieve_response_code( $zip_res ) === 200 ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                WP_Filesystem();
                $temp_zip = $theme_build_dir . '/repo_temp.zip';
                file_put_contents( $temp_zip, wp_remote_retrieve_body( $zip_res ) );
                $unzip_dir = $theme_build_dir . '/unzipped_' . time();
                wp_mkdir_p( $unzip_dir );
                $unzipped = unzip_file( $temp_zip, $unzip_dir );
                @unlink( $temp_zip );
                if ( ! is_wp_error( $unzipped ) ) {
                    $extracted_dirs = glob( $unzip_dir . '/*', GLOB_ONLYDIR );
                    if ( ! empty( $extracted_dirs ) ) {
                        self::copy_dir_recursive( $extracted_dirs[0], $repo_dir );
                    }
                    self::delete_dir_recursive( $unzip_dir );
                }
            }
        }

        // Locate package.json
        $working_dir = $repo_dir;
        if ( ! file_exists( $working_dir . '/package.json' ) ) {
            $sub_packages = glob( $repo_dir . '/*/package.json' );
            if ( ! empty( $sub_packages ) ) {
                $working_dir = dirname( $sub_packages[0] );
            }
        }

        if ( ! file_exists( $working_dir . '/package.json' ) ) {
            return new WP_Error( 'missing_package_json', 'Could not locate package.json in repository. Build failed.', implode( "\n", $git_log ) );
        }

        // Auto-create .openai/hosting.json if needed by OpenAI/Codex Vite templates
        if ( ! file_exists( $working_dir . '/.openai/hosting.json' ) ) {
            @wp_mkdir_p( $working_dir . '/.openai' );
            @file_put_contents( $working_dir . '/.openai/hosting.json', '{"d1":null,"r2":null}' );
        }

        $package_json = json_decode( file_get_contents( $working_dir . '/package.json' ), true ) ?: array();
        $deps = array_merge( $package_json['dependencies'] ?? array(), $package_json['devDependencies'] ?? array() );

        $framework = 'nextjs';

        if ( isset( $deps['expo'] ) ) {
            $framework = 'expo';
            $extra_deps = '';
            if ( empty( $deps['react-dom'] ) || empty( $deps['react-native-web'] ) ) {
                $extra_deps = 'npm install --no-audit react-dom@19.1.0 react-native-web@^0.21.0 --prefer-offline 2>&1 && ';
            }
            $build_cmd = 'npm install --no-audit 2>&1 && ' . $extra_deps . 'npx expo export -p web 2>&1';
        } elseif ( isset( $deps['next'] ) ) {
            $framework = 'nextjs';
            $build_cmd = 'npm install --no-audit 2>&1 && npm run build 2>&1';
        } elseif ( isset( $deps['vinext'] ) ) {
            $framework = 'vinext';
            $build_cmd = 'npm install --no-audit 2>&1 && npm run build 2>&1';
        } elseif ( isset( $deps['vite'] ) ) {
            $framework = 'vite';
            $build_cmd = 'npm install --no-audit 2>&1 && npm run build 2>&1';
        } else {
            $framework = 'react';
            $build_cmd = 'npm install --no-audit 2>&1 && npm run build 2>&1';
        }

        // Environment paths: includes user local standalone binaries and limits thread count for shared hosts
        $path_env = 'export PATH=$HOME/.local/bin:$HOME/.local/share/node-v22.14.0-linux-x64/bin:$HOME/.local/share/node-v20.18.0-linux-x64/bin:/opt/homebrew/bin:/usr/local/bin:/usr/bin:/bin:$PATH; export RAYON_NUM_THREADS=1; export UV_THREADPOOL_SIZE=1;';

        // Verify Node.js / npm existence
        $node_check = array();
        $node_ret = 0;
        @exec( "{$path_env} && node -v 2>&1 && npm -v 2>&1", $node_check, $node_ret );

        if ( $node_ret !== 0 ) {
            // Auto-install portable node if missing
            $inst_out = array();
            $inst_code = 0;
            @exec( "mkdir -p ~/.local/share ~/.local/bin && curl -fsSL https://nodejs.org/dist/v22.14.0/node-v22.14.0-linux-x64.tar.gz | tar -xz -C ~/.local/share/ && ln -sf ~/.local/share/node-v22.14.0-linux-x64/bin/node ~/.local/bin/node && ln -sf ~/.local/share/node-v22.14.0-linux-x64/bin/npm ~/.local/bin/npm && ln -sf ~/.local/share/node-v22.14.0-linux-x64/bin/npx ~/.local/bin/npx", $inst_out, $inst_code );
            @exec( "{$path_env} && node -v 2>&1 && npm -v 2>&1", $node_check, $node_ret );
        }

        if ( $node_ret !== 0 ) {
            return new WP_Error( 'node_missing', 'Node.js or npm is not installed or accessible on the server.', implode( "\n", $node_check ) );
        }

        // Remove stale lockfile if conflicting target version exists
        if ( file_exists( $working_dir . '/package-lock.json' ) ) {
            @unlink( $working_dir . '/package-lock.json' );
        }

        // Execute build
        $exec_cmd = "{$path_env} && cd " . escapeshellarg( $working_dir ) . " && " . $build_cmd;
        $build_output = array();
        $build_exit_code = 0;
        @exec( $exec_cmd, $build_output, $build_exit_code );

        // If Vinext / Next.js SSR server worker exists (dist/server/index.js), prerender all scanned routes into static HTML!
        $server_worker = $working_dir . '/dist/server/index.js';
        if ( file_exists( $server_worker ) ) {
            $scanned_routes = array( '/' );
            $page_rows = $wpdb->get_results( $wpdb->prepare( "SELECT slug, is_homepage FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d", $theme_id ), ARRAY_A );
            if ( ! empty( $page_rows ) ) {
                foreach ( $page_rows as $prow ) {
                    $r_path = ( ! empty( $prow['is_homepage'] ) || $prow['slug'] === 'home' ) ? '/' : '/' . ltrim( $prow['slug'], '/' );
                    $scanned_routes[] = $r_path;
                }
            }
            $scanned_routes = array_values( array_unique( $scanned_routes ) );
            $routes_json = json_encode( $scanned_routes );

            $prerender_script = "
import('./dist/server/index.js').then(async ({ default: worker }) => {
  const { writeFileSync, mkdirSync } = await import('node:fs');
  const { resolve, dirname } = await import('node:path');
  const routes = {$routes_json};
  for (const route of routes) {
    try {
      const res = await worker.fetch(new Request('https://cora.internal' + route));
      if (res.status === 200) {
        const html = await res.text();
        const cleanRoute = route === '/' ? '/index.html' : route.replace(/\\/$/, '') + '/index.html';
        const outPath = resolve('./dist/client' + cleanRoute);
        mkdirSync(dirname(outPath), { recursive: true });
        writeFileSync(outPath, html, 'utf8');
        if (route !== '/') {
          const flatPath = resolve('./dist/client' + route.replace(/\\/$/, '') + '.html');
          mkdirSync(dirname(flatPath), { recursive: true });
          writeFileSync(flatPath, html, 'utf8');
        }
      }
    } catch(err) {
      console.error('Prerender error on ' + route, err);
    }
  }
  console.log('Prerender complete.');
}).catch(console.error);
";
            $prerender_file = $working_dir . '/cora_prerender_temp.mjs';
            @file_put_contents( $prerender_file, $prerender_script );
            $prerender_out = array();
            $prerender_ret = 0;
            @exec( "{$path_env} && cd " . escapeshellarg( $working_dir ) . " && node cora_prerender_temp.mjs 2>&1", $prerender_out, $prerender_ret );
            @unlink( $prerender_file );
            $build_output = array_merge( $build_output, $prerender_out );
        }

        $build_logs_str = implode( "\n", array_merge( $git_log, $build_output ) );

        // Scan for static output directory
        $candidate_dirs = array( 'dist/client', 'out', 'dist', 'build', 'web-build', '.next/server/pages', '.next/standalone/out' );
        $static_source_path = '';

        foreach ( $candidate_dirs as $cand ) {
            $test_path = $working_dir . '/' . $cand;
            if ( is_dir( $test_path ) ) {
                $static_source_path = realpath( $test_path );
                break;
            }
        }

        if ( empty( $static_source_path ) && $working_dir !== $repo_dir ) {
            foreach ( $candidate_dirs as $cand ) {
                $test_path = $repo_dir . '/' . $cand;
                if ( is_dir( $test_path ) ) {
                    $static_source_path = realpath( $test_path );
                    break;
                }
            }
        }

        if ( empty( $static_source_path ) || ! is_dir( $static_source_path ) ) {
            return new WP_Error( 'static_dir_missing', 'Build completed but no static output directory (dist/client, out, dist, build, web-build) was found.', $build_logs_str );
        }

        // Copy compiled static files into standard static output directory
        $final_static_dir = $theme_build_dir . '/out';
        if ( ! file_exists( $final_static_dir ) ) {
            wp_mkdir_p( $final_static_dir );
        }
        self::copy_dir_recursive( $static_source_path, $final_static_dir );

        // Count static files
        $file_count = 0;
        try {
            $iter = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $final_static_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ( $iter as $f ) {
                if ( $f->isFile() ) {
                    $file_count++;
                }
            }
        } catch ( Exception $e ) {}

        // Save theme settings
        $settings['source']            = 'nextjs';
        $settings['is_static_built']   = true;
        $settings['static_build_path'] = $final_static_dir;
        $settings['static_built_at']   = current_time( 'mysql' );
        $settings['framework']         = $framework;

        $wpdb->update(
            "{$wpdb->prefix}cora_canvas_themes",
            array(
                'settings'   => wp_json_encode( $settings ),
                'updated_at' => current_time( 'mysql' )
            ),
            array( 'id' => $theme_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        return array(
            'success'     => true,
            'message'     => 'Static site built successfully.',
            'static_dir'  => $final_static_dir,
            'framework'   => $framework,
            'files_count' => $file_count,
            'theme_id'    => $theme_id,
            'build_logs'  => $build_logs_str
        );
    }

    /**
     * Helper: Recursively copy directory
     */
    public static function copy_dir_recursive( $src, $dst ) {
        $dir = opendir( $src );
        if ( ! $dir ) return;
        @wp_mkdir_p( $dst );
        while ( false !== ( $file = readdir( $dir ) ) ) {
            if ( ( $file !== '.' ) && ( $file !== '..' ) ) {
                if ( is_dir( $src . '/' . $file ) ) {
                    self::copy_dir_recursive( $src . '/' . $file, $dst . '/' . $file );
                } else {
                    copy( $src . '/' . $file, $dst . '/' . $file );
                }
            }
        }
        closedir( $dir );
    }

    /**
     * Helper: Recursively delete directory
     */
    public static function delete_dir_recursive( $dir ) {
        if ( ! is_dir( $dir ) ) return;
        $files = array_diff( scandir( $dir ), array( '.', '..' ) );
        foreach ( $files as $file ) {
            ( is_dir( "$dir/$file" ) ) ? self::delete_dir_recursive( "$dir/$file" ) : @unlink( "$dir/$file" );
        }
        return @rmdir( $dir );
    }
}

Cora_NextJS_Integration::get_instance();

}
