<?php
/**
 * Cora Growth Workspace - Scoped REST API Controller
 *
 * Exposes first-party REST endpoints under /wp-json/cora-growth/v1/
 * strictly scoped to the Growth Workspace.
 *
 * @package CoraWorkspace
 * @subpackage Growth
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Growth_API {

    const REST_NAMESPACE = 'cora-growth/v1';
    const WORKSPACE_ID = 'growth_cora_main_01';

    /**
     * Register REST API routes
     */
    public static function register_routes() {
        // Content CRUD & Actions
        register_rest_route( self::REST_NAMESPACE, '/content', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_content_list' ),
                'permission_callback' => array( __CLASS__, 'check_read_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'create_content' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/content/(?P<id>[a-zA-Z0-9_\-]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_single_content' ),
                'permission_callback' => array( __CLASS__, 'check_read_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( __CLASS__, 'update_content' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( __CLASS__, 'delete_content' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
        ) );

        // Content Actions
        register_rest_route( self::REST_NAMESPACE, '/content/(?P<id>[a-zA-Z0-9_\-]+)/validate', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'validate_content_endpoint' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/content/(?P<id>[a-zA-Z0-9_\-]+)/publish', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'publish_content' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/content/(?P<id>[a-zA-Z0-9_\-]+)/unpublish', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'unpublish_content' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/content/(?P<id>[a-zA-Z0-9_\-]+)/preview', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'generate_preview_token' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/content/(?P<id>[a-zA-Z0-9_\-]+)/revisions', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_revisions' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/content/(?P<id>[a-zA-Z0-9_\-]+)/rollback', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'rollback_content' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        // Public Preview Resolver (Token validated)
        register_rest_route( self::REST_NAMESPACE, '/preview/(?P<token>[a-zA-Z0-9_\-]+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'resolve_preview_token' ),
            'permission_callback' => '__return_true', // Validated via HMAC signature inside handler
        ) );

        // Assets
        register_rest_route( self::REST_NAMESPACE, '/assets', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_assets' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'upload_or_attach_asset' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/assets/upload', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'upload_or_attach_asset' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        // Growth Queue
        register_rest_route( self::REST_NAMESPACE, '/queue', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_queue_jobs' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'create_queue_job' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/queue/(?P<id>[a-zA-Z0-9_\-]+)', array(
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => array( __CLASS__, 'update_queue_job' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        // Analytics & Discovery
        register_rest_route( self::REST_NAMESPACE, '/metrics', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_growth_metrics_summary' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/metrics/(?P<id>[a-zA-Z0-9_\-]+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_single_content_metrics' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/discovery/(?P<type>topics|keywords|link-graph|opportunities)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_discovery_data' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/discovery/check-overlap', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'check_content_overlap_endpoint' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        // Types & Blocks Metadata Schemas
        register_rest_route( self::REST_NAMESPACE, '/schema', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_schema_definitions' ),
            'permission_callback' => '__return_true',
        ) );

        // Remote Authenticated MCP Endpoint (Model Context Protocol)
        register_rest_route( self::REST_NAMESPACE, '/mcp', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'handle_mcp_discovery' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'handle_mcp_request' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
        ) );
    }

    /**
     * Check permission: Service Account token or Cora Logged-in admin
     */
    public static function check_permission( $request ) {
        // 1. Check Authorization Bearer Token
        $auth_header = $request->get_header( 'authorization' );
        if ( $auth_header && preg_match( '/Bearer\s+(.*)$/i', $auth_header, $matches ) ) {
            $token = trim( $matches[1] );
            $valid_token = get_option( 'cora_growth_service_token' );
            if ( $valid_token && hash_equals( $valid_token, $token ) ) {
                return true;
            }
        }

        // 2. Check local cookie / session for logged-in admin / workspace manager
        if ( current_user_can( 'manage_options' ) || current_user_can( 'edit_posts' ) ) {
            return true;
        }

        // Local development bypass if explicitly permitted
        if ( defined( 'CORA_DEV_MODE' ) && CORA_DEV_MODE ) {
            return true;
        }

        return new WP_Error( 'rest_forbidden', 'Unauthorized Growth Workspace access.', array( 'status' => 401 ) );
    }

    /**
     * Check read permission: allows public reads for published content, token required for drafts
     */
    public static function check_read_permission( $request ) {
        // Always allow if authorized
        $auth = self::check_permission( $request );
        if ( ! is_wp_error( $auth ) && $auth === true ) {
            return true;
        }

        // Allow public read only for published status queries
        return true;
    }

    /**
     * Get content list with filters
     */
    public static function get_content_list( $request ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';

        $type = $request->get_param( 'type' );
        $status = $request->get_param( 'status' );
        $search = $request->get_param( 'search' );
        $limit = min( 100, max( 1, intval( $request->get_param( 'limit' ) ?: 20 ) ) );
        $page = max( 1, intval( $request->get_param( 'page' ) ?: 1 ) );
        $offset = ( $page - 1 ) * $limit;

        $where = array( "workspace_id = %s" );
        $params = array( self::WORKSPACE_ID );

        // If not authenticated, only return published
        $is_authed = ! is_wp_error( self::check_permission( $request ) );
        if ( ! $is_authed ) {
            $where[] = "status = 'published'";
        } elseif ( ! empty( $status ) ) {
            $where[] = "status = %s";
            $params[] = sanitize_text_field( $status );
        }

        if ( ! empty( $type ) ) {
            $where[] = "type = %s";
            $params[] = sanitize_text_field( $type );
        }

        if ( ! empty( $search ) ) {
            $where[] = "(title LIKE %s OR primary_keyword LIKE %s OR excerpt LIKE %s)";
            $wildcard = '%' . $wpdb->esc_like( sanitize_text_field( $search ) ) . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $where_sql = implode( ' AND ', $where );
        $total = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_content} WHERE {$where_sql}", $params ) );

        $query_params = array_merge( $params, array( $limit, $offset ) );
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table_content} WHERE {$where_sql} ORDER BY updated_at DESC LIMIT %d OFFSET %d",
            $query_params
        ), ARRAY_A );

        $items = array_map( array( __CLASS__, 'format_content_entry' ), $rows );

        return rest_ensure_response( array(
            'success'    => true,
            'items'      => $items,
            'total'      => intval( $total ),
            'page'       => $page,
            'total_pages'=> ceil( $total / $limit ),
        ) );
    }

    /**
     * Get single content by ID or Slug
     */
    public static function get_single_content( $request ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';
        $id_or_slug = sanitize_text_field( $request->get_param( 'id' ) );

        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_content} WHERE workspace_id = %s AND (id = %s OR slug = %s) LIMIT 1",
            self::WORKSPACE_ID,
            $id_or_slug,
            $id_or_slug
        ), ARRAY_A );

        if ( ! $row ) {
            return new WP_Error( 'not_found', 'Content entry not found.', array( 'status' => 404 ) );
        }

        // If not authenticated and not published, deny
        $is_authed = ! is_wp_error( self::check_permission( $request ) );
        if ( ! $is_authed && $row['status'] !== 'published' ) {
            return new WP_Error( 'forbidden', 'Content is not published.', array( 'status' => 403 ) );
        }

        return rest_ensure_response( array(
            'success' => true,
            'item'    => self::format_content_entry( $row ),
        ) );
    }

    /**
     * Create content entry
     */
    public static function create_content( $request ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';

        $body = $request->get_json_params() ?: $request->get_body_params() ?: $request->get_params();
        if ( empty( $body['title'] ) ) {
            return new WP_Error( 'missing_title', 'Title is required.', array( 'status' => 400 ) );
        }

        $id = ! empty( $body['id'] ) ? sanitize_text_field( $body['id'] ) : 'cnt_' . wp_generate_password( 12, false, false );
        $type = ! empty( $body['type'] ) ? sanitize_key( $body['type'] ) : 'article';
        $slug = ! empty( $body['slug'] ) ? sanitize_title( $body['slug'] ) : sanitize_title( $body['title'] );
        $status = ! empty( $body['status'] ) ? sanitize_key( $body['status'] ) : 'draft';

        // Check validation
        $body['id'] = $id;
        $body['slug'] = $slug;
        $body['type'] = $type;

        $validation = Cora_Content_Validator::validate( $body, 'save_draft', self::WORKSPACE_ID );
        if ( ! $validation['valid'] && $status === 'published' ) {
            return new WP_Error( 'validation_failed', 'Content validation failed before publishing.', array(
                'status' => 422,
                'errors' => $validation['errors'],
            ) );
        }

        $now = current_time( 'mysql' );
        $published_at = ( $status === 'published' ) ? $now : null;

        // Structured JSON blocks / chapters
        $raw_blocks = isset( $body['content'] ) ? $body['content'] : ( isset( $body['content_blocks'] ) ? $body['content_blocks'] : ( isset( $body['body'] ) ? $body['body'] : array() ) );
        $blocks = array();
        if ( is_string( $raw_blocks ) && ! empty( $raw_blocks ) ) {
            $json_decoded = json_decode( $raw_blocks, true );
            if ( is_array( $json_decoded ) ) {
                $blocks = $json_decoded;
            } else {
                $blocks = array(
                    array(
                        'id'      => 'blk_' . wp_generate_password( 8, false, false ),
                        'type'    => 'rich_text',
                        'version' => 1,
                        'data'    => array(
                            'text' => $raw_blocks,
                            'html' => wpautop( esc_html( $raw_blocks ) ),
                        ),
                    ),
                );
            }
        } elseif ( is_array( $raw_blocks ) ) {
            $blocks = $raw_blocks;
        }

        if ( empty( $blocks ) ) {
            $blocks = array(
                array(
                    'id'      => 'blk_' . wp_generate_password( 8, false, false ),
                    'type'    => 'rich_text',
                    'version' => 1,
                    'data'    => array(
                        'text' => ! empty( $body['excerpt'] ) ? $body['excerpt'] : 'Draft content for ' . $body['title'],
                        'html' => '<p>' . esc_html( ! empty( $body['excerpt'] ) ? $body['excerpt'] : 'Draft content for ' . $body['title'] ) . '</p>',
                    ),
                ),
            );
        }

        $sanitized_blocks = array();
        foreach ( $blocks as $blk ) {
            $sanitized_blocks[] = Cora_Block_Registry::sanitize_block( $blk );
        }

        $chapters = isset( $body['chapters'] ) ? $body['chapters'] : array();
        $author = isset( $body['author'] ) ? $body['author'] : array(
            'name'   => 'Dravya Bansal',
            'role'   => 'Co-founder & CEO, Cora',
            'avatar' => '/images/founder.jpeg',
        );
        if ( isset( $author['name'] ) && $author['name'] === 'Dravya Agarwal' ) {
            $author['name'] = 'Dravya Bansal';
        }

        $seo = isset( $body['seo'] ) ? ( is_string( $body['seo'] ) ? json_decode( $body['seo'], true ) : $body['seo'] ) : array(
            'title'            => sanitize_text_field( $body['title'] ),
            'meta_description' => ! empty( $body['excerpt'] ) ? sanitize_text_field( $body['excerpt'] ) : '',
            'og_image'         => ! empty( $body['og_image'] ) ? esc_url_raw( $body['og_image'] ) : '',
        );

        if ( isset( $body['quick_answer'] ) ) {
            $seo['quick_answer'] = $body['quick_answer'];
        }
        if ( isset( $body['category'] ) ) {
            $seo['category'] = sanitize_title( $body['category'] );
        }

        $data = array(
            'id'                     => $id,
            'workspace_id'           => self::WORKSPACE_ID,
            'type'                   => $type,
            'schema_version'         => 1,
            'title'                  => sanitize_text_field( $body['title'] ),
            'slug'                   => $slug,
            'status'                 => $status,
            'excerpt'                => ! empty( $body['excerpt'] ) ? sanitize_textarea_field( $body['excerpt'] ) : '',
            'target_icp'             => ! empty( $body['target_icp'] ) ? sanitize_text_field( $body['target_icp'] ) : 'agency_founders',
            'primary_keyword'        => ! empty( $body['primary_keyword'] ) ? sanitize_text_field( $body['primary_keyword'] ) : '',
            'secondary_keywords_json'=> ! empty( $body['secondary_keywords'] ) ? wp_json_encode( $body['secondary_keywords'] ) : '[]',
            'search_intent'          => ! empty( $body['search_intent'] ) ? sanitize_text_field( $body['search_intent'] ) : 'informational',
            'read_time'              => ! empty( $body['read_time'] ) ? sanitize_text_field( $body['read_time'] ) : '5 min read',
            'seo_json'               => wp_json_encode( $seo ),
            'share_json'             => ! empty( $body['share'] ) ? wp_json_encode( $body['share'] ) : '{}',
            'content_blocks_json'    => wp_json_encode( $sanitized_blocks ),
            'chapters_json'          => wp_json_encode( $chapters ),
            'sources_json'           => ! empty( $body['sources'] ) ? wp_json_encode( $body['sources'] ) : '[]',
            'relationships_json'     => ! empty( $body['relationships'] ) ? wp_json_encode( $body['relationships'] ) : '[]',
            'assets_json'            => ! empty( $body['assets'] ) ? wp_json_encode( $body['assets'] ) : '[]',
            'cta_json'               => ! empty( $body['cta'] ) ? wp_json_encode( $body['cta'] ) : '{}',
            'author_json'            => wp_json_encode( $author ),
            'created_at'             => $now,
            'updated_at'             => $now,
            'published_at'           => $published_at,
        );

        // Check if existing record exists with id or slug
        $existing = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$table_content} WHERE workspace_id = %s AND (id = %s OR slug = %s) LIMIT 1", self::WORKSPACE_ID, $id, $slug ), ARRAY_A );
        if ( $existing ) {
            $wpdb->update( $table_content, $data, array( 'id' => $existing['id'], 'workspace_id' => self::WORKSPACE_ID ) );
            $id = $existing['id'];
        } else {
            $inserted = $wpdb->insert( $table_content, $data );
            if ( ! $inserted ) {
                return new WP_Error( 'db_insert_error', 'Failed to create content entry in DB: ' . $wpdb->last_error, array( 'status' => 500 ) );
            }
        }

        // Record revision snapshot
        self::save_revision( $id, $data, 'Initial creation' );

        // Log audit event
        self::log_audit( 'create_content', $id, null, 'rev_init', array( 'type' => $type, 'title' => $body['title'] ) );

        $formatted = self::format_content_entry( $data );

        return rest_ensure_response( array(
            'success' => true,
            'id'      => $id,
            'slug'    => $slug,
            'item'    => $formatted,
            'entry'   => $formatted,
        ) );
    }

    /**
     * Update content entry
     */
    public static function update_content( $request ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';
        $id = sanitize_text_field( $request->get_param( 'id' ) );

        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_content} WHERE workspace_id = %s AND id = %s LIMIT 1",
            self::WORKSPACE_ID,
            $id
        ), ARRAY_A );

        if ( ! $existing ) {
            return new WP_Error( 'not_found', 'Content entry not found.', array( 'status' => 404 ) );
        }

        $body = $request->get_json_params() ?: $request->get_body_params() ?: $request->get_params();
        $now = current_time( 'mysql' );

        $updates = array( 'updated_at' => $now );

        if ( isset( $body['title'] ) ) $updates['title'] = sanitize_text_field( $body['title'] );
        if ( isset( $body['slug'] ) ) $updates['slug'] = sanitize_title( $body['slug'] );
        if ( isset( $body['type'] ) ) $updates['type'] = sanitize_key( $body['type'] );
        if ( isset( $body['status'] ) ) {
            $updates['status'] = sanitize_key( $body['status'] );
            if ( $updates['status'] === 'published' && empty( $existing['published_at'] ) ) {
                $updates['published_at'] = $now;
            }
        }
        if ( isset( $body['excerpt'] ) ) $updates['excerpt'] = sanitize_textarea_field( $body['excerpt'] );
        if ( isset( $body['target_icp'] ) ) $updates['target_icp'] = sanitize_text_field( $body['target_icp'] );
        if ( isset( $body['primary_keyword'] ) ) $updates['primary_keyword'] = sanitize_text_field( $body['primary_keyword'] );
        if ( isset( $body['secondary_keywords'] ) ) $updates['secondary_keywords_json'] = wp_json_encode( $body['secondary_keywords'] );
        if ( isset( $body['search_intent'] ) ) $updates['search_intent'] = sanitize_text_field( $body['search_intent'] );
        if ( isset( $body['read_time'] ) ) $updates['read_time'] = sanitize_text_field( $body['read_time'] );
        if ( isset( $body['seo'] ) ) $updates['seo_json'] = wp_json_encode( $body['seo'] );
        if ( isset( $body['quick_answer'] ) || isset( $body['category'] ) ) {
            $seo = ! empty( $updates['seo_json'] ) ? json_decode( $updates['seo_json'], true ) : ( ! empty( $existing['seo_json'] ) ? json_decode( $existing['seo_json'], true ) : array() );
            if ( isset( $body['quick_answer'] ) ) {
                $seo['quick_answer'] = $body['quick_answer'];
            }
            if ( isset( $body['category'] ) ) {
                $seo['category'] = sanitize_title( $body['category'] );
            }
            $updates['seo_json'] = wp_json_encode( $seo );
        }
        if ( isset( $body['share'] ) ) $updates['share_json'] = wp_json_encode( $body['share'] );
        if ( isset( $body['sources'] ) ) $updates['sources_json'] = wp_json_encode( $body['sources'] );
        if ( isset( $body['relationships'] ) ) $updates['relationships_json'] = wp_json_encode( $body['relationships'] );
        if ( isset( $body['assets'] ) ) $updates['assets_json'] = wp_json_encode( $body['assets'] );
        if ( isset( $body['cta'] ) ) $updates['cta_json'] = wp_json_encode( $body['cta'] );
        if ( isset( $body['author'] ) ) $updates['author_json'] = wp_json_encode( $body['author'] );
        if ( isset( $body['chapters'] ) ) $updates['chapters_json'] = wp_json_encode( $body['chapters'] );

        if ( isset( $body['content'] ) || isset( $body['content_blocks'] ) ) {
            $blocks = isset( $body['content'] ) ? $body['content'] : $body['content_blocks'];
            $sanitized = array();
            if ( is_array( $blocks ) ) {
                foreach ( $blocks as $b ) {
                    $sanitized[] = Cora_Block_Registry::sanitize_block( $b );
                }
            }
            $updates['content_blocks_json'] = wp_json_encode( $sanitized );
        }

        $wpdb->update( $table_content, $updates, array( 'id' => $id, 'workspace_id' => self::WORKSPACE_ID ) );

        $updated_row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_content} WHERE workspace_id = %s AND id = %s LIMIT 1",
            self::WORKSPACE_ID,
            $id
        ), ARRAY_A );

        // Save revision
        $change_reason = ! empty( $body['change_reason'] ) ? sanitize_text_field( $body['change_reason'] ) : 'Updated content';
        $rev_id = self::save_revision( $id, $updated_row, $change_reason );

        // Audit log
        self::log_audit( 'update_content', $id, null, $rev_id, array( 'reason' => $change_reason ) );

        return rest_ensure_response( array(
            'success' => true,
            'id'      => $id,
            'item'    => self::format_content_entry( $updated_row ),
        ) );
    }

    /**
     * Delete / Archive content
     */
    public static function delete_content( $request ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';
        $id = sanitize_text_field( $request->get_param( 'id' ) );

        $wpdb->update( $table_content, array( 'status' => 'archived' ), array( 'id' => $id, 'workspace_id' => self::WORKSPACE_ID ) );
        self::log_audit( 'archive_content', $id, null, null );

        return rest_ensure_response( array( 'success' => true, 'message' => 'Content archived.' ) );
    }

    /**
     * Validate content endpoint
     */
    public static function validate_content_endpoint( $request ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';
        $id = sanitize_text_field( $request->get_param( 'id' ) );

        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_content} WHERE workspace_id = %s AND id = %s LIMIT 1",
            self::WORKSPACE_ID,
            $id
        ), ARRAY_A );

        if ( ! $row ) {
            return new WP_Error( 'not_found', 'Content entry not found.', array( 'status' => 404 ) );
        }

        $entry = self::format_content_entry( $row );
        $result = Cora_Content_Validator::validate( $entry, 'publish', self::WORKSPACE_ID );

        return rest_ensure_response( $result );
    }

    /**
     * Publish content endpoint with validation gate, Next.js revalidation, and live public verification
     */
    public static function publish_content( $request ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';
        $id = sanitize_text_field( $request->get_param( 'id' ) );

        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_content} WHERE workspace_id = %s AND id = %s LIMIT 1",
            self::WORKSPACE_ID,
            $id
        ), ARRAY_A );

        if ( ! $row ) {
            return new WP_Error( 'not_found', 'Content entry not found.', array( 'status' => 404 ) );
        }

        $entry = self::format_content_entry( $row );

        // 1. Validate payload
        $validation = Cora_Content_Validator::validate( $entry, 'publish', self::WORKSPACE_ID );
        if ( ! $validation['valid'] ) {
            return new WP_Error( 'validation_failed', 'Cannot publish: content has validation errors.', array(
                'status'   => 422,
                'errors'   => $validation['errors'],
                'warnings' => $validation['warnings'],
            ) );
        }

        // 2. Save revision snapshot
        $rev_id = self::save_revision( $id, $entry, 'Published content' );

        // 3. Update DB status to published
        $now = current_time( 'mysql' );
        $wpdb->update(
            $table_content,
            array(
                'status'       => 'published',
                'published_at' => ! empty( $row['published_at'] ) ? $row['published_at'] : $now,
                'updated_at'   => $now,
            ),
            array( 'id' => $id, 'workspace_id' => self::WORKSPACE_ID )
        );

        // 4. Trigger Next.js ISR revalidation
        $revalidated = self::trigger_nextjs_revalidation( $row['type'], $row['slug'] );

        // 5. Determine public URL and perform live verification HTTP fetch
        $frontend_url = defined( 'CORA_FRONTEND_URL' ) ? CORA_FRONTEND_URL : ( get_option( 'cora_frontend_url' ) ?: 'http://localhost:3000' );
        $path = ( $row['type'] === 'guide' ? '/guides/' : '/blog/' ) . $row['slug'];
        $live_url = rtrim( $frontend_url, '/' ) . $path;

        $response = wp_remote_get( $live_url, array(
            'timeout'     => 6,
            'redirection' => 5,
            'headers'     => array(
                'User-Agent' => 'CoraGrowthLiveVerifier/1.0',
            ),
        ) );

        $checks = array(
            'http'      => false,
            'title'     => false,
            'canonical' => false,
            'robots'    => false,
            'og'        => false,
        );
        $failure_reasons = array();

        if ( is_wp_error( $response ) ) {
            $failure_reasons[] = 'Live verification HTTP request failed: ' . $response->get_error_message();
        } else {
            $code = wp_remote_retrieve_response_code( $response );
            $body = wp_remote_retrieve_body( $response );

            // Check HTTP 200
            if ( $code === 200 ) {
                $checks['http'] = true;
            } else {
                $failure_reasons[] = "Live URL returned HTTP {$code} instead of 200.";
            }

            // Check Title / H1 in HTML body
            $expected_title = trim( $row['title'] );
            $title_in_html = stripos( $body, htmlspecialchars( $expected_title, ENT_QUOTES, 'UTF-8' ) ) !== false ||
                             stripos( $body, $expected_title ) !== false ||
                             preg_match( '/<h1[^>]*>.*?<\/h1>/is', $body );

            if ( $title_in_html ) {
                $checks['title'] = true;
            } else {
                $failure_reasons[] = 'Expected title or H1 was not found in live HTML response.';
            }

            // Check Canonical tag
            if ( preg_match( '/<link[^>]+rel=["\']canonical["\'][^>]*>/i', $body, $canon_match ) ) {
                $checks['canonical'] = true;
            } else {
                $failure_reasons[] = 'Canonical link tag (<link rel="canonical">) missing in HTML head.';
            }

            // Check Robots meta allows indexing
            if ( preg_match( '/<meta[^>]+name=["\']robots["\'][^>]*content=["\']([^"\']+)["\'][^>]*>/i', $body, $robots_match ) ) {
                if ( stripos( $robots_match[1], 'noindex' ) === false ) {
                    $checks['robots'] = true;
                } else {
                    $failure_reasons[] = 'Robots meta specifies noindex on published page.';
                }
            } else {
                $checks['robots'] = true;
            }

            // Check OG title / image present
            $has_og_title = preg_match( '/<meta[^>]+property=["\']og:title["\'][^>]*>/i', $body );
            $has_og_image = preg_match( '/<meta[^>]+property=["\']og:image["\'][^>]*>/i', $body );
            if ( $has_og_title || $has_og_image ) {
                $checks['og'] = true;
            } else {
                $failure_reasons[] = 'OpenGraph meta tags (og:title or og:image) missing in HTML head.';
            }
        }

        $all_checks_passed = $checks['http'] && $checks['title'] && $checks['canonical'] && $checks['robots'] && $checks['og'];

        // 6. Log audit
        self::log_audit( 'publish_content', $id, null, $rev_id, array(
            'revalidated' => $revalidated,
            'verified'    => $all_checks_passed,
            'checks'      => $checks,
        ) );

        $response_data = array(
            'success'     => true,
            'published'   => true,
            'verified'    => $all_checks_passed,
            'url'         => $path,
            'live_url'    => $live_url,
            'checks'      => $checks,
            'revalidated' => $revalidated,
        );

        if ( ! empty( $failure_reasons ) ) {
            $response_data['failure_reasons'] = $failure_reasons;
        }

        return rest_ensure_response( $response_data );
    }

    /**
     * Unpublish content
     */
    public static function unpublish_content( $request ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';
        $id = sanitize_text_field( $request->get_param( 'id' ) );

        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_content} WHERE workspace_id = %s AND id = %s LIMIT 1", self::WORKSPACE_ID, $id ), ARRAY_A );
        if ( ! $row ) {
            return new WP_Error( 'not_found', 'Content entry not found.', array( 'status' => 404 ) );
        }

        $wpdb->update( $table_content, array( 'status' => 'draft' ), array( 'id' => $id, 'workspace_id' => self::WORKSPACE_ID ) );
        self::trigger_nextjs_revalidation( $row['type'], $row['slug'] );
        self::log_audit( 'unpublish_content', $id, null, null );

        return rest_ensure_response( array( 'success' => true, 'message' => 'Content unpublished to draft.' ) );
    }

    /**
     * Generate signed preview token
     */
    public static function generate_preview_token( $request ) {
        $id = sanitize_text_field( $request->get_param( 'id' ) );
        $token = Cora_Growth_Preview_Service::generate_token( $id, self::WORKSPACE_ID );
        $url = Cora_Growth_Preview_Service::get_preview_url( $id, self::WORKSPACE_ID );

        return rest_ensure_response( array(
            'success'     => true,
            'token'       => $token,
            'preview_url' => $url,
        ) );
    }

    /**
     * Resolve preview token to full payload for Next.js preview route
     */
    public static function resolve_preview_token( $request ) {
        global $wpdb;
        $token = sanitize_text_field( $request->get_param( 'token' ) );

        $payload = Cora_Growth_Preview_Service::validate_token( $token );
        if ( ! $payload ) {
            return new WP_Error( 'invalid_preview_token', 'Invalid or expired preview token.', array( 'status' => 403 ) );
        }

        $table_content = $wpdb->prefix . 'cora_content_entries';
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_content} WHERE workspace_id = %s AND id = %s LIMIT 1",
            $payload['wid'],
            $payload['cid']
        ), ARRAY_A );

        if ( ! $row ) {
            return new WP_Error( 'not_found', 'Preview content not found.', array( 'status' => 404 ) );
        }

        return rest_ensure_response( array(
            'success' => true,
            'preview' => true,
            'item'    => self::format_content_entry( $row ),
        ) );
    }

    /**
     * Get revision history
     */
    public static function get_revisions( $request ) {
        global $wpdb;
        $table_revisions = $wpdb->prefix . 'cora_content_revisions';
        $id = sanitize_text_field( $request->get_param( 'id' ) );

        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT revision_id, content_id, actor, change_reason, created_at FROM {$table_revisions} WHERE workspace_id = %s AND content_id = %s ORDER BY created_at DESC",
            self::WORKSPACE_ID,
            $id
        ), ARRAY_A );

        return rest_ensure_response( array( 'success' => true, 'revisions' => $rows ?: array() ) );
    }

    /**
     * Rollback content to specific revision
     */
    public static function rollback_content( $request ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';
        $table_revisions = $wpdb->prefix . 'cora_content_revisions';

        $body = $request->get_json_params() ?: $request->get_body_params() ?: $request->get_params();
        $id = sanitize_text_field( $request->get_param( 'id' ) ?: ( $body['id'] ?? $body['content_id'] ?? '' ) );
        $revision_id = sanitize_text_field( $body['revision_id'] ?? $request->get_param( 'revision_id' ) ?? '' );

        $rev = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_revisions} WHERE workspace_id = %s AND content_id = %s AND revision_id = %s LIMIT 1",
            self::WORKSPACE_ID,
            $id,
            $revision_id
        ), ARRAY_A );

        if ( ! $rev ) {
            return new WP_Error( 'revision_not_found', "Revision snapshot not found. Requested content_id: '{$id}', revision_id: '{$revision_id}', workspace: '" . self::WORKSPACE_ID . "'", array( 'status' => 404 ) );
        }

        $snapshot = json_decode( $rev['snapshot_json'], true );
        if ( ! $snapshot ) {
            return new WP_Error( 'corrupt_snapshot', 'Corrupt revision snapshot.', array( 'status' => 500 ) );
        }

        // Apply snapshot fields
        $updates = array(
            'title'                  => $snapshot['title'] ?? '',
            'slug'                   => $snapshot['slug'] ?? '',
            'type'                   => $snapshot['type'] ?? 'article',
            'excerpt'                => $snapshot['excerpt'] ?? '',
            'primary_keyword'        => $snapshot['primary_keyword'] ?? '',
            'secondary_keywords_json'=> is_array( $snapshot['secondary_keywords'] ?? null ) ? wp_json_encode( $snapshot['secondary_keywords'] ) : ( $snapshot['secondary_keywords_json'] ?? '[]' ),
            'search_intent'          => $snapshot['search_intent'] ?? '',
            'seo_json'               => is_array( $snapshot['seo'] ?? null ) ? wp_json_encode( $snapshot['seo'] ) : ( $snapshot['seo_json'] ?? '{}' ),
            'content_blocks_json'    => is_array( $snapshot['content'] ?? null ) ? wp_json_encode( $snapshot['content'] ) : ( $snapshot['content_blocks_json'] ?? '[]' ),
            'chapters_json'          => is_array( $snapshot['chapters'] ?? null ) ? wp_json_encode( $snapshot['chapters'] ) : ( $snapshot['chapters_json'] ?? '[]' ),
            'updated_at'             => current_time( 'mysql' ),
        );

        $wpdb->update( $table_content, $updates, array( 'id' => $id, 'workspace_id' => self::WORKSPACE_ID ) );

        self::save_revision( $id, $updates, "Rolled back to revision {$revision_id}" );
        self::log_audit( 'rollback_content', $id, $revision_id, null );

        return rest_ensure_response( array( 'success' => true, 'message' => "Restored revision {$revision_id} successfully." ) );
    }

    /**
     * Asset uploads and listing
     */
    public static function get_assets( $request ) {
        global $wpdb;
        $table_assets = $wpdb->prefix . 'cora_content_assets';

        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table_assets} WHERE workspace_id = %s ORDER BY created_at DESC LIMIT 50",
            self::WORKSPACE_ID
        ), ARRAY_A );

        return rest_ensure_response( array( 'success' => true, 'assets' => $rows ?: array() ) );
    }

    public static function upload_or_attach_asset( $request ) {
        global $wpdb;
        $table_assets = $wpdb->prefix . 'cora_content_assets';

        $body = $request->get_json_params() ?: $request->get_body_params() ?: $request->get_params();
        $asset_id = ! empty( $body['asset_id'] ) ? sanitize_text_field( $body['asset_id'] ) : 'ast_' . wp_generate_password( 10, false, false );
        $filename = sanitize_text_field( $body['filename'] ?? 'asset.webp' );
        $clean_name = preg_replace( '/[^a-zA-Z0-9._-]/', '_', $filename );
        $target_filename = "{$asset_id}_{$clean_name}";
        $mime_type = sanitize_mime_type( $body['mime_type'] ?? 'image/webp' );

        // Allowed MIME validation
        $allowed_mimes = array(
            'image/png',
            'image/jpeg',
            'image/webp',
            'image/svg+xml',
            'application/pdf',
        );
        if ( ! in_array( $mime_type, $allowed_mimes, true ) ) {
            return new WP_Error( 'invalid_mime', "Disallowed MIME type: {$mime_type}. Allowed: PNG, JPG, WebP, SVG, PDF.", array( 'status' => 400 ) );
        }

        $uploads_dir_backend = ABSPATH . 'wp-content/uploads/growth';
        $project_root = dirname( dirname( rtrim( ABSPATH, '/\\' ) ) );
        $uploads_dir_frontend = $project_root . '/cora-frontend/public/uploads/growth';
        if ( ! is_dir( $uploads_dir_backend ) ) wp_mkdir_p( $uploads_dir_backend );
        if ( ! is_dir( $uploads_dir_frontend ) ) wp_mkdir_p( $uploads_dir_frontend );

        $file_size = intval( $body['file_size'] ?? 0 );
        if ( ! empty( $body['base64_data'] ) ) {
            $raw_bytes = base64_decode( $body['base64_data'] );

            // Max file size check (15MB)
            if ( strlen( $raw_bytes ) > 15 * 1024 * 1024 ) {
                return new WP_Error( 'file_too_large', 'File exceeds maximum upload size of 15MB.', array( 'status' => 400 ) );
            }

            // Security Hardening: SVG Sanitization
            if ( $mime_type === 'image/svg+xml' || preg_match( '/\.svg$/i', $filename ) ) {
                $raw_svg = $raw_bytes;
                // Strip <script>...</script>
                $raw_svg = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $raw_svg );
                // Strip on* event handlers (onload, onclick, onerror, onmouseover, etc.)
                $raw_svg = preg_replace( '/\s+on[a-z]+\s*=\s*(["\'][^"\']*["\']|[^\s>]+)/i', '', $raw_svg );
                // Strip javascript: URLs in href or xlink:href
                $raw_svg = preg_replace( '/(href|xlink:href)\s*=\s*["\']\s*javascript:[^"\']*["\']/i', '$1=""', $raw_svg );
                // Strip foreignObject, iframe, embed, object, applet tags
                $raw_svg = preg_replace( '/<\/?(foreignObject|iframe|embed|object|applet)\b[^>]*>/i', '', $raw_svg );
                $raw_bytes = $raw_svg;
            }

            file_put_contents( "{$uploads_dir_backend}/{$target_filename}", $raw_bytes );
            file_put_contents( "{$uploads_dir_frontend}/{$target_filename}", $raw_bytes );
            $file_size = strlen( $raw_bytes );
        }

        $file_url = ! empty( $body['file_url'] ) ? esc_url_raw( $body['file_url'] ) : "/uploads/growth/{$target_filename}";

        $data = array(
            'asset_id'     => $asset_id,
            'workspace_id' => self::WORKSPACE_ID,
            'filename'     => $filename,
            'file_url'     => $file_url,
            'mime_type'    => $mime_type,
            'width'        => intval( $body['width'] ?? 0 ),
            'height'       => intval( $body['height'] ?? 0 ),
            'file_size'    => $file_size,
            'alt_text'     => sanitize_text_field( $body['alt_text'] ?? '' ),
            'title'        => sanitize_text_field( $body['title'] ?? $filename ),
            'caption'      => sanitize_textarea_field( $body['caption'] ?? '' ),
            'source'       => sanitize_text_field( $body['source'] ?? 'growth_agent' ),
            'created_at'   => current_time( 'mysql' ),
        );

        $wpdb->insert( $table_assets, $data );
        self::log_audit( 'upload_asset', $asset_id, null, null, array( 'filename' => $filename, 'mime_type' => $mime_type, 'file_size' => $file_size ) );
        return rest_ensure_response( array( 'success' => true, 'asset' => $data ) );
    }

    /**
     * Growth Queue Jobs
     */
    public static function get_queue_jobs( $request ) {
        global $wpdb;
        $table_queue = $wpdb->prefix . 'cora_growth_queue';

        $status = $request->get_param( 'status' );
        $where = array( "workspace_id = %s" );
        $params = array( self::WORKSPACE_ID );

        if ( ! empty( $status ) ) {
            $where[] = "status = %s";
            $params[] = sanitize_text_field( $status );
        }

        $where_sql = implode( ' AND ', $where );
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table_queue} WHERE {$where_sql} ORDER BY created_at DESC LIMIT 100",
            $params
        ), ARRAY_A );

        $formatted = array_map( function( $j ) {
            $j['requirement_spec'] = ! empty( $j['requirement_spec_json'] ) ? json_decode( $j['requirement_spec_json'], true ) : null;
            return $j;
        }, $rows );

        return rest_ensure_response( array( 'success' => true, 'jobs' => $formatted ) );
    }

    public static function create_queue_job( $request ) {
        global $wpdb;
        $table_queue = $wpdb->prefix . 'cora_growth_queue';

        $body = $request->get_json_params();
        $id = ! empty( $body['id'] ) ? sanitize_text_field( $body['id'] ) : 'job_' . wp_generate_password( 10, false, false );
        $now = current_time( 'mysql' );

        $data = array(
            'id'                    => $id,
            'workspace_id'          => self::WORKSPACE_ID,
            'type'                  => sanitize_text_field( $body['type'] ?? 'ARTICLE' ),
            'title'                 => sanitize_text_field( $body['title'] ?? 'Untitled Growth Task' ),
            'priority'              => sanitize_text_field( $body['priority'] ?? 'medium' ),
            'status'                => sanitize_text_field( $body['status'] ?? 'backlog' ),
            'target_keyword'        => sanitize_text_field( $body['target_keyword'] ?? '' ),
            'target_url'            => esc_url_raw( $body['target_url'] ?? '' ),
            'source'                => sanitize_text_field( $body['source'] ?? 'Growth Agent' ),
            'assigned_to'           => sanitize_text_field( $body['assigned_to'] ?? 'Cora Growth Agent' ),
            'result_content_id'     => sanitize_text_field( $body['result_content_id'] ?? '' ),
            'requirement_spec_json' => ! empty( $body['requirement_spec'] ) ? wp_json_encode( $body['requirement_spec'] ) : null,
            'created_at'            => $now,
            'updated_at'            => $now,
        );

        $wpdb->insert( $table_queue, $data );
        return rest_ensure_response( array( 'success' => true, 'job' => $data ) );
    }

    public static function update_queue_job( $request ) {
        global $wpdb;
        $table_queue = $wpdb->prefix . 'cora_growth_queue';
        $id = sanitize_text_field( $request->get_param( 'id' ) );
        $body = $request->get_json_params();

        $updates = array( 'updated_at' => current_time( 'mysql' ) );
        if ( isset( $body['status'] ) ) {
            $updates['status'] = sanitize_text_field( $body['status'] );
            if ( $updates['status'] === 'published' || $updates['status'] === 'done' ) {
                $updates['completed_at'] = current_time( 'mysql' );
            }
        }
        if ( isset( $body['priority'] ) ) $updates['priority'] = sanitize_text_field( $body['priority'] );
        if ( isset( $body['result_content_id'] ) ) $updates['result_content_id'] = sanitize_text_field( $body['result_content_id'] );
        if ( isset( $body['requirement_spec'] ) ) $updates['requirement_spec_json'] = wp_json_encode( $body['requirement_spec'] );

        $wpdb->update( $table_queue, $updates, array( 'id' => $id, 'workspace_id' => self::WORKSPACE_ID ) );
        return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
    }

    /**
     * Discovery Data
     */
    public static function get_discovery_data( $request ) {
        $type = $request->get_param( 'type' );
        switch ( $type ) {
            case 'topics':
            case 'keywords':
                $data = Cora_Growth_Analytics::get_existing_topics_and_keywords( self::WORKSPACE_ID );
                return rest_ensure_response( array( 'success' => true, 'data' => $data[ $type ] ) );
            case 'link-graph':
                $graph = Cora_Growth_Analytics::get_internal_link_graph( self::WORKSPACE_ID );
                return rest_ensure_response( array( 'success' => true, 'link_graph' => $graph ) );
            case 'opportunities':
                $opps = Cora_Growth_Analytics::get_search_opportunities( self::WORKSPACE_ID );
                return rest_ensure_response( array( 'success' => true, 'opportunities' => $opps ) );
            default:
                return new WP_Error( 'invalid_type', 'Invalid discovery type.', array( 'status' => 400 ) );
        }
    }

    /**
     * Performance Metrics Summary
     */
    public static function get_growth_metrics_summary( $request ) {
        global $wpdb;
        $table_metrics = $wpdb->prefix . 'cora_growth_metrics';

        $summary = $wpdb->get_row( $wpdb->prepare(
            "SELECT 
                SUM(impressions) as total_impressions,
                SUM(clicks) as total_clicks,
                AVG(ctr) as avg_ctr,
                AVG(position) as avg_position,
                SUM(sessions) as total_sessions,
                SUM(conversions) as total_conversions
            FROM {$table_metrics}
            WHERE workspace_id = %s",
            self::WORKSPACE_ID
        ), ARRAY_A );

        return rest_ensure_response( array(
            'success' => true,
            'summary' => array(
                'impressions' => intval( $summary['total_impressions'] ?? 0 ),
                'clicks'      => intval( $summary['total_clicks'] ?? 0 ),
                'avg_ctr'     => round( floatval( $summary['avg_ctr'] ?? 0 ), 2 ),
                'avg_position'=> round( floatval( $summary['avg_position'] ?? 0 ), 1 ),
                'sessions'    => intval( $summary['total_sessions'] ?? 0 ),
                'conversions' => intval( $summary['total_conversions'] ?? 0 ),
            ),
        ) );
    }

    public static function get_single_content_metrics( $request ) {
        $id = sanitize_text_field( $request->get_param( 'id' ) );
        $data = Cora_Growth_Analytics::get_content_performance( $id, self::WORKSPACE_ID );
        return rest_ensure_response( array( 'success' => true, 'performance' => $data ) );
    }

    /**
     * Schema definitions (Available content types and blocks)
     */
    public static function get_schema_definitions( $request ) {
        return rest_ensure_response( array(
            'success'       => true,
            'content_types' => Cora_Content_Type_Registry::get_all_types(),
            'blocks'        => Cora_Block_Registry::get_all_blocks(),
        ) );
    }

    /**
     * Endpoint for Content Overlap Check
     */
    public static function check_content_overlap_endpoint( $request ) {
        $body = $request->get_json_params() ?: $request->get_body_params() ?: $request->get_params();
        $result = self::check_content_overlap( $body, self::WORKSPACE_ID );
        return rest_ensure_response( $result );
    }

    /**
     * Algorithm for Content Overlap / Duplication Check
     *
     * @param array  $payload      Input: title, slug, primary_keyword, search_intent, type, [id]
     * @param string $workspace_id Tenancy ID
     * @return array { risk: 'low'|'medium'|'high', matches: array }
     */
    public static function check_content_overlap( $payload, $workspace_id = self::WORKSPACE_ID ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';

        $title = isset( $payload['title'] ) ? trim( $payload['title'] ) : '';
        $slug = isset( $payload['slug'] ) ? sanitize_title( $payload['slug'] ) : ( $title ? sanitize_title( $title ) : '' );
        $primary_keyword = isset( $payload['primary_keyword'] ) ? trim( mb_strtolower( $payload['primary_keyword'] ) ) : '';
        $search_intent = isset( $payload['search_intent'] ) ? trim( mb_strtolower( $payload['search_intent'] ) ) : '';
        $exclude_id = isset( $payload['id'] ) ? sanitize_text_field( $payload['id'] ) : ( isset( $payload['exclude_id'] ) ? sanitize_text_field( $payload['exclude_id'] ) : '' );

        // Fetch candidates in workspace
        $query = "SELECT id, title, slug, type, status, primary_keyword, search_intent FROM {$table_content} WHERE workspace_id = %s";
        $params = array( $workspace_id );

        if ( ! empty( $exclude_id ) ) {
            $query .= " AND id != %s";
            $params[] = $exclude_id;
        }

        $rows = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

        $matches = array();
        $highest_risk = 'low';

        $input_title_clean = mb_strtolower( trim( preg_replace( '/[^\p{L}\p{N}\s]/u', '', $title ) ) );
        $input_words = array_values( array_filter( explode( ' ', $input_title_clean ) ) );

        if ( ! empty( $rows ) ) {
            foreach ( $rows as $row ) {
                $cand_slug = sanitize_title( $row['slug'] );
                $cand_keyword = trim( mb_strtolower( $row['primary_keyword'] ?? '' ) );
                $cand_intent = trim( mb_strtolower( $row['search_intent'] ?? '' ) );
                $cand_title = trim( $row['title'] ?? '' );
                $cand_title_clean = mb_strtolower( trim( preg_replace( '/[^\p{L}\p{N}\s]/u', '', $cand_title ) ) );
                $cand_words = array_values( array_filter( explode( ' ', $cand_title_clean ) ) );

                $match_reason = null;
                $risk_level = null;

                // 1. Exact slug collision -> Risk: high
                if ( ! empty( $slug ) && $cand_slug === $slug ) {
                    $risk_level = 'high';
                    $match_reason = 'Exact slug collision';
                }
                // 2. Exact primary keyword with same search intent -> Risk: high
                elseif ( ! empty( $primary_keyword ) && ! empty( $cand_keyword ) && $primary_keyword === $cand_keyword && ! empty( $search_intent ) && ! empty( $cand_intent ) && $search_intent === $cand_intent ) {
                    $risk_level = 'high';
                    $match_reason = 'Primary keyword & search intent collision';
                } else {
                    // 3. Title similarity (Levenshtein / similar_text / word intersection > 60%) or primary keyword overlap -> Risk: medium
                    $similarity_percent = 0;
                    if ( ! empty( $input_title_clean ) && ! empty( $cand_title_clean ) ) {
                        similar_text( $input_title_clean, $cand_title_clean, $similarity_percent );

                        // Word intersection (Jaccard)
                        $intersection = array_intersect( $input_words, $cand_words );
                        $union = array_unique( array_merge( $input_words, $cand_words ) );
                        $jaccard = ! empty( $union ) ? ( count( $intersection ) / count( $union ) ) : 0;
                        if ( $jaccard >= 0.60 || $similarity_percent >= 60 ) {
                            $risk_level = 'medium';
                            $match_reason = 'High title similarity (' . round( max( $similarity_percent, $jaccard * 100 ) ) . '%)';
                        }
                    }

                    if ( ! $risk_level && ! empty( $primary_keyword ) && ! empty( $cand_keyword ) ) {
                        if ( $primary_keyword === $cand_keyword || strpos( $primary_keyword, $cand_keyword ) !== false || strpos( $cand_keyword, $primary_keyword ) !== false ) {
                            $risk_level = 'medium';
                            $match_reason = 'Primary keyword overlap';
                        }
                    }
                }

                if ( $risk_level ) {
                    $matches[] = array(
                        'id'     => $row['id'],
                        'title'  => $row['title'],
                        'slug'   => $row['slug'],
                        'type'   => $row['type'],
                        'status' => $row['status'],
                        'reason' => $match_reason,
                    );

                    if ( $risk_level === 'high' ) {
                        $highest_risk = 'high';
                    } elseif ( $highest_risk !== 'high' ) {
                        $highest_risk = 'medium';
                    }
                }
            }
        }

        return array(
            'risk'    => $highest_risk,
            'matches' => $matches,
        );
    }

    /**
     * Helper: Format content DB row to clean API JSON structure
     */
    public static function format_content_entry( $row ) {
        if ( ! is_array( $row ) ) return null;

        $seo = ! empty( $row['seo_json'] ) ? json_decode( $row['seo_json'], true ) : array();
        $quick_answer = isset( $seo['quick_answer'] ) ? $seo['quick_answer'] : null;
        $category = isset( $seo['category'] ) ? $seo['category'] : ( ! empty( $row['target_icp'] ) && array_key_exists( $row['target_icp'], Cora_Content_Type_Registry::get_canonical_categories() ) ? $row['target_icp'] : 'operations' );

        return array(
            'id'                 => $row['id'],
            'workspace_id'       => $row['workspace_id'],
            'type'               => $row['type'],
            'schema_version'     => intval( $row['schema_version'] ),
            'title'              => $row['title'],
            'slug'               => $row['slug'],
            'category'           => $category,
            'status'             => $row['status'],
            'excerpt'            => $row['excerpt'],
            'target_icp'         => $row['target_icp'],
            'primary_keyword'    => $row['primary_keyword'],
            'secondary_keywords' => ! empty( $row['secondary_keywords_json'] ) ? json_decode( $row['secondary_keywords_json'], true ) : array(),
            'search_intent'      => $row['search_intent'],
            'read_time'          => $row['read_time'] ?: '5 min read',
            'quick_answer'       => $quick_answer,
            'seo'                => $seo,
            'share'              => ! empty( $row['share_json'] ) ? json_decode( $row['share_json'], true ) : array(),
            'content'            => ! empty( $row['content_blocks_json'] ) ? json_decode( $row['content_blocks_json'], true ) : array(),
            'chapters'           => ! empty( $row['chapters_json'] ) ? json_decode( $row['chapters_json'], true ) : array(),
            'sources'            => ! empty( $row['sources_json'] ) ? json_decode( $row['sources_json'], true ) : array(),
            'relationships'      => ! empty( $row['relationships_json'] ) ? json_decode( $row['relationships_json'], true ) : array(),
            'assets'             => ! empty( $row['assets_json'] ) ? json_decode( $row['assets_json'], true ) : array(),
            'cta'                => ! empty( $row['cta_json'] ) ? json_decode( $row['cta_json'], true ) : array(),
            'author'             => ! empty( $row['author_json'] ) ? json_decode( $row['author_json'], true ) : array(),
            'created_at'         => $row['created_at'],
            'updated_at'         => $row['updated_at'],
            'published_at'       => $row['published_at'],
            'public_url'         => ( $row['type'] === 'guide' ? '/guides/' : '/blog/' ) . $row['slug'],
        );
    }

    /**
     * Helper: Save immutable revision snapshot
     */
    private static function save_revision( $content_id, $data, $reason = '' ) {
        global $wpdb;
        $table_revisions = $wpdb->prefix . 'cora_content_revisions';
        $rev_id = 'rev_' . wp_generate_password( 12, false, false );

        $wpdb->insert( $table_revisions, array(
            'revision_id'   => $rev_id,
            'content_id'    => $content_id,
            'workspace_id'  => self::WORKSPACE_ID,
            'actor'         => 'Cora Growth Agent',
            'snapshot_json' => is_string( $data ) ? $data : wp_json_encode( $data ),
            'change_reason' => $reason,
            'created_at'    => current_time( 'mysql' ),
        ) );

        return $rev_id;
    }

    /**
     * Helper: Log audit event
     */
    private static function log_audit( $action, $content_id = null, $prev_rev = null, $new_rev = null, $meta = array() ) {
        global $wpdb;
        $table_audit = $wpdb->prefix . 'cora_growth_audit_log';

        $wpdb->insert( $table_audit, array(
            'workspace_id'      => self::WORKSPACE_ID,
            'actor'             => 'Cora Growth Agent',
            'action'            => $action,
            'content_id'        => $content_id,
            'previous_revision' => $prev_rev,
            'new_revision'      => $new_rev,
            'metadata_json'     => ! empty( $meta ) ? wp_json_encode( $meta ) : null,
            'created_at'        => current_time( 'mysql' ),
        ) );
    }

    /**
     * Remote MCP Discovery Endpoint (Lists all growth.* tools with JSON schemas)
     */
    public static function handle_mcp_discovery( $request ) {
        $tools = self::get_mcp_tool_definitions();
        return rest_ensure_response( array(
            'jsonrpc' => '2.0',
            'result'  => array(
                'tools'       => $tools,
                'serverInfo'  => array(
                    'name'    => 'Cora Growth MCP Remote Server',
                    'version' => '1.0.0',
                    'workspace' => self::WORKSPACE_ID,
                ),
            ),
        ) );
    }

    /**
     * Remote MCP Tool Execution Endpoint (Supports JSON-RPC 2.0 and direct REST payload)
     */
    public static function handle_mcp_request( $request ) {
        $body = $request->get_json_params();
        $method = $body['method'] ?? '';
        $id = $body['id'] ?? 1;

        // JSON-RPC tools/list
        if ( $method === 'tools/list' ) {
            return rest_ensure_response( array(
                'jsonrpc' => '2.0',
                'id'      => $id,
                'result'  => array(
                    'tools' => self::get_mcp_tool_definitions(),
                ),
            ) );
        }

        // Determine tool name and arguments
        $tool_name = '';
        $arguments = array();

        if ( $method === 'tools/call' && ! empty( $body['params']['name'] ) ) {
            $tool_name = sanitize_text_field( $body['params']['name'] );
            $arguments = (array) ( $body['params']['arguments'] ?? array() );
        } elseif ( ! empty( $body['tool'] ) ) {
            $tool_name = sanitize_text_field( $body['tool'] );
            $arguments = (array) ( $body['arguments'] ?? array() );
        } elseif ( ! empty( $body['name'] ) ) {
            $tool_name = sanitize_text_field( $body['name'] );
            $arguments = (array) ( $body['arguments'] ?? array() );
        }

        if ( empty( $tool_name ) ) {
            return new WP_Error( 'invalid_mcp_request', 'Missing tool name or method.', array( 'status' => 400 ) );
        }

        // Execute scoped tool
        $result = self::execute_mcp_tool( $tool_name, $arguments );
        if ( is_wp_error( $result ) ) {
            return rest_ensure_response( array(
                'jsonrpc' => '2.0',
                'id'      => $id,
                'error'   => array(
                    'code'    => $result->get_error_data()['status'] ?? -32603,
                    'message' => $result->get_error_message(),
                ),
                'isError' => true,
            ) );
        }

        return rest_ensure_response( array(
            'jsonrpc' => '2.0',
            'id'      => $id,
            'result'  => array(
                'content' => array(
                    array(
                        'type' => 'text',
                        'text' => is_string( $result ) ? $result : wp_json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ),
                    ),
                ),
                'data'    => $result,
                'isError' => false,
            ),
        ) );
    }

    /**
     * Dispatch MCP tool to internal REST handler
     */
    public static function execute_mcp_tool( $tool_name, $arguments, $workspace_id = 'growth-cora-master' ) {
        $action = strtolower( trim( str_replace( array( 'cora.', 'growth.', 'cora_', 'growth_' ), '', $tool_name ) ) );

        switch ( $action ) {
            case 'list_content':
                $req = new WP_REST_Request( 'GET', '/cora-growth/v1/content' );
                if ( ! empty( $arguments['type'] ) ) $req->set_param( 'type', sanitize_text_field( $arguments['type'] ) );
                if ( ! empty( $arguments['status'] ) ) $req->set_param( 'status', sanitize_text_field( $arguments['status'] ) );
                if ( ! empty( $arguments['search'] ) ) $req->set_param( 'search', sanitize_text_field( $arguments['search'] ) );
                if ( ! empty( $arguments['limit'] ) ) $req->set_param( 'limit', intval( $arguments['limit'] ) );
                $res = self::get_content_list( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'get_content':
                $id = sanitize_text_field( $arguments['id_or_slug'] ?? $arguments['id'] ?? $arguments['slug'] ?? '' );
                if ( empty( $id ) ) return new WP_Error( 'missing_id', 'Argument id_or_slug is required.', array( 'status' => 400 ) );
                $req = new WP_REST_Request( 'GET', "/cora-growth/v1/content/{$id}" );
                $req->set_param( 'id', $id );
                $res = self::get_single_content( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'search_content':
                $req = new WP_REST_Request( 'GET', '/cora-growth/v1/content' );
                $req->set_param( 'search', sanitize_text_field( $arguments['query'] ?? $arguments['search'] ?? '' ) );
                if ( ! empty( $arguments['type'] ) ) $req->set_param( 'type', sanitize_text_field( $arguments['type'] ) );
                $res = self::get_content_list( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'create_article':
                $req = new WP_REST_Request( 'POST', '/cora-growth/v1/content' );
                $arguments['type'] = 'article';
                if ( empty( $arguments['author'] ) || ( is_array( $arguments['author'] ) && ( $arguments['author']['name'] ?? '' ) === 'Dravya Agarwal' ) ) {
                    $arguments['author'] = array(
                        'name'   => 'Dravya Bansal',
                        'role'   => 'Co-founder & CEO, Cora',
                        'avatar' => '/images/founder.jpeg',
                    );
                }
                $req->set_body_params( $arguments );
                $res = self::create_content( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'update_article':
                $id = sanitize_text_field( $arguments['id'] ?? '' );
                if ( empty( $id ) ) return new WP_Error( 'missing_id', 'Article ID is required.', array( 'status' => 400 ) );
                $req = new WP_REST_Request( 'POST', "/cora-growth/v1/content/{$id}" );
                $req->set_param( 'id', $id );
                $req->set_body_params( $arguments );
                $res = self::update_content( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'create_guide':
                $req = new WP_REST_Request( 'POST', '/cora-growth/v1/content' );
                $arguments['type'] = 'guide';
                if ( empty( $arguments['author'] ) || ( is_array( $arguments['author'] ) && ( $arguments['author']['name'] ?? '' ) === 'Dravya Agarwal' ) ) {
                    $arguments['author'] = array(
                        'name'   => 'Dravya Bansal',
                        'role'   => 'Co-founder & CEO, Cora',
                        'avatar' => '/images/founder.jpeg',
                    );
                }
                $req->set_body_params( $arguments );
                $res = self::create_content( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'update_guide':
                $id = sanitize_text_field( $arguments['id'] ?? '' );
                if ( empty( $id ) ) return new WP_Error( 'missing_id', 'Guide ID is required.', array( 'status' => 400 ) );
                $req = new WP_REST_Request( 'POST', "/cora-growth/v1/content/{$id}" );
                $req->set_param( 'id', $id );
                $req->set_body_params( $arguments );
                $res = self::update_content( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'upload_asset':
            case 'attach_asset':
                $req = new WP_REST_Request( 'POST', '/cora-growth/v1/assets/upload' );
                $req->set_body_params( $arguments );
                $res = self::upload_or_attach_asset( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'validate':
            case 'validate_content':
                $id = sanitize_text_field( $arguments['id'] ?? $arguments['content_id'] ?? '' );
                if ( empty( $id ) ) return new WP_Error( 'missing_id', 'Content ID is required for validation.', array( 'status' => 400 ) );
                $req = new WP_REST_Request( 'POST', "/cora-growth/v1/content/{$id}/validate" );
                $req->set_param( 'id', $id );
                $res = self::validate_content_endpoint( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'preview':
                $id = sanitize_text_field( $arguments['id'] ?? $arguments['content_id'] ?? '' );
                if ( empty( $id ) ) return new WP_Error( 'missing_id', 'Content ID is required for preview token.', array( 'status' => 400 ) );
                $req = new WP_REST_Request( 'POST', "/cora-growth/v1/content/{$id}/preview" );
                $req->set_param( 'id', $id );
                $res = self::generate_preview_token( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'publish':
            case 'publish_content':
                $id = sanitize_text_field( $arguments['id'] ?? $arguments['content_id'] ?? '' );
                if ( empty( $id ) ) return new WP_Error( 'missing_id', 'Content ID is required for publish.', array( 'status' => 400 ) );
                $req = new WP_REST_Request( 'POST', "/cora-growth/v1/content/{$id}/publish" );
                $req->set_param( 'id', $id );
                $res = self::publish_content( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'rollback':
                $id = sanitize_text_field( $arguments['id'] ?? $arguments['content_id'] ?? '' );
                $revision_id = sanitize_text_field( $arguments['revision_id'] ?? '' );
                if ( empty( $id ) || empty( $revision_id ) ) return new WP_Error( 'missing_params', 'Content ID and revision_id are required.', array( 'status' => 400 ) );
                $req = new WP_REST_Request( 'POST', "/cora-growth/v1/content/{$id}/rollback" );
                $req->set_url_params( array( 'id' => $id ) );
                $req->set_param( 'id', $id );
                $req->set_param( 'revision_id', $revision_id );
                $req->set_body_params( array( 'id' => $id, 'revision_id' => $revision_id ) );
                $res = self::rollback_content( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'get_revisions':
                $id = sanitize_text_field( $arguments['id'] ?? $arguments['content_id'] ?? '' );
                if ( empty( $id ) ) return new WP_Error( 'missing_id', 'Content ID is required for revisions.', array( 'status' => 400 ) );
                $req = new WP_REST_Request( 'GET', "/cora-growth/v1/content/{$id}/revisions" );
                $req->set_param( 'id', $id );
                $res = self::get_revisions( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'check_overlap':
            case 'check_content_overlap':
                return self::check_content_overlap( $arguments, self::WORKSPACE_ID );

            case 'get_performance':
                $id = sanitize_text_field( $arguments['id'] ?? $arguments['content_id'] ?? '' );
                $days = intval( $arguments['days'] ?? 30 );
                if ( ! empty( $id ) ) {
                    return Cora_Growth_Analytics::get_content_performance( $id, self::WORKSPACE_ID, $days );
                }
                $req = new WP_REST_Request( 'GET', '/cora-growth/v1/metrics' );
                $req->set_param( 'days', $days );
                $res = self::get_growth_metrics_summary( $req );
                return $res instanceof WP_REST_Response ? $res->get_data() : $res;

            case 'get_search_opportunities':
                return Cora_Growth_Analytics::get_search_opportunities( self::WORKSPACE_ID );

            case 'manage_queue':
                $action = sanitize_text_field( $arguments['action'] ?? 'list' );
                if ( $action === 'create' ) {
                    $req = new WP_REST_Request( 'POST', '/cora-growth/v1/queue' );
                    $req->set_body_params( $arguments['job'] ?? $arguments );
                    $res = self::create_queue_job( $req );
                    return $res instanceof WP_REST_Response ? $res->get_data() : $res;
                } elseif ( $action === 'update' ) {
                    $job_id = sanitize_text_field( $arguments['job_id'] ?? $arguments['id'] ?? '' );
                    $req = new WP_REST_Request( 'POST', "/cora-growth/v1/queue/{$job_id}" );
                    $req->set_param( 'id', $job_id );
                    $req->set_body_params( $arguments['updates'] ?? $arguments );
                    $res = self::update_queue_job( $req );
                    return $res instanceof WP_REST_Response ? $res->get_data() : $res;
                } else {
                    $req = new WP_REST_Request( 'GET', '/cora-growth/v1/queue' );
                    if ( ! empty( $arguments['status'] ) ) $req->set_param( 'status', sanitize_text_field( $arguments['status'] ) );
                    $res = self::get_queue_jobs( $req );
                    return $res instanceof WP_REST_Response ? $res->get_data() : $res;
                }

            default:
                return new WP_Error( 'unknown_tool', "Tool '{$tool_name}' not found in Cora Growth Workspace.", array( 'status' => 404 ) );
        }
    }

    /**
     * MCP Tool Definitions with Schemas
     */
    private static function get_mcp_tool_definitions() {
        return array(
            array(
                'name'        => 'growth.list_content',
                'description' => 'List content entries in Cora Growth Workspace with optional filters for type, status, and keyword search.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'type'   => array( 'type' => 'string', 'enum' => array( 'article', 'guide', 'landing_page', 'comparison', 'research_report' ) ),
                        'status' => array( 'type' => 'string', 'enum' => array( 'draft', 'review', 'ready', 'published', 'archived' ) ),
                        'search' => array( 'type' => 'string' ),
                        'limit'  => array( 'type' => 'number', 'default' => 20 ),
                    ),
                ),
            ),
            array(
                'name'        => 'growth.get_content',
                'description' => 'Retrieve a single content entry with all structured blocks, metadata, and SEO settings.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id_or_slug' => array( 'type' => 'string', 'description' => 'Unique content entry ID or slug.' ),
                    ),
                    'required'   => array( 'id_or_slug' ),
                ),
            ),
            array(
                'name'        => 'growth.search_content',
                'description' => 'Search existing published and draft content to check for topic duplication and internal linking targets.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'query' => array( 'type' => 'string' ),
                        'type'  => array( 'type' => 'string' ),
                    ),
                    'required'   => array( 'query' ),
                ),
            ),
            array(
                'name'        => 'growth.check_content_overlap',
                'description' => 'Perform lightweight keyword and semantic overlap check before content creation.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'title'           => array( 'type' => 'string', 'description' => 'Target article or guide title' ),
                        'slug'            => array( 'type' => 'string', 'description' => 'Target URL slug' ),
                        'primary_keyword' => array( 'type' => 'string', 'description' => 'Main target keyword' ),
                        'search_intent'   => array( 'type' => 'string', 'description' => 'Target search intent (informational, commercial, etc.)' ),
                        'type'            => array( 'type' => 'string', 'description' => 'Content type (article, guide, etc.)' ),
                        'id'              => array( 'type' => 'string', 'description' => 'Optional existing content ID to exclude' ),
                    ),
                    'required'   => array( 'title' ),
                ),
            ),
            array(
                'name'        => 'growth.create_article',
                'description' => 'Create a new long-form editorial article in Cora Growth CMS with structured JSON blocks.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'                 => array( 'type' => 'string' ),
                        'title'              => array( 'type' => 'string' ),
                        'slug'               => array( 'type' => 'string' ),
                        'category'           => array( 'type' => 'string', 'enum' => array( 'operations', 'client-management', 'sales-proposals', 'growth', 'ai-automation', 'finance', 'agency-profitability', 'research' ) ),
                        'excerpt'            => array( 'type' => 'string' ),
                        'primary_keyword'    => array( 'type' => 'string' ),
                        'secondary_keywords' => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
                        'search_intent'      => array( 'type' => 'string' ),
                        'read_time'          => array( 'type' => 'string' ),
                        'quick_answer'       => array( 'type' => 'object', 'description' => 'Structured quick answer for AI Search / GEO extraction' ),
                        'content'            => array( 'type' => 'array', 'items' => array( 'type' => 'object' ) ),
                        'seo'                => array( 'type' => 'object' ),
                        'sources'            => array( 'type' => 'array', 'items' => array( 'type' => 'object' ) ),
                        'relationships'      => array( 'type' => 'array', 'items' => array( 'type' => 'object' ) ),
                        'author'             => array( 'type' => 'object' ),
                    ),
                    'required'   => array( 'title', 'slug', 'excerpt' ),
                ),
            ),
            array(
                'name'        => 'growth.update_article',
                'description' => 'Update an existing article with new blocks or metadata, saving an immutable revision snapshot.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'            => array( 'type' => 'string' ),
                        'title'         => array( 'type' => 'string' ),
                        'slug'          => array( 'type' => 'string' ),
                        'category'      => array( 'type' => 'string' ),
                        'quick_answer'  => array( 'type' => 'object' ),
                        'content'       => array( 'type' => 'array', 'items' => array( 'type' => 'object' ) ),
                        'change_reason' => array( 'type' => 'string' ),
                    ),
                    'required'   => array( 'id' ),
                ),
            ),
            array(
                'name'        => 'growth.create_guide',
                'description' => 'Create a multi-chapter pillar guide in Cora Growth CMS with structured chapters and downloadable assets.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'              => array( 'type' => 'string' ),
                        'title'           => array( 'type' => 'string' ),
                        'slug'            => array( 'type' => 'string' ),
                        'category'        => array( 'type' => 'string', 'enum' => array( 'operations', 'client-management', 'sales-proposals', 'growth', 'ai-automation', 'finance', 'agency-profitability', 'research' ) ),
                        'excerpt'         => array( 'type' => 'string' ),
                        'primary_keyword' => array( 'type' => 'string' ),
                        'quick_answer'    => array( 'type' => 'object' ),
                        'chapters'        => array( 'type' => 'array', 'items' => array( 'type' => 'object' ) ),
                        'seo'             => array( 'type' => 'object' ),
                        'cta'             => array( 'type' => 'object' ),
                        'sources'         => array( 'type' => 'array', 'items' => array( 'type' => 'object' ) ),
                        'author'          => array( 'type' => 'object' ),
                    ),
                    'required'   => array( 'title', 'slug', 'chapters' ),
                ),
            ),
            array(
                'name'        => 'growth.update_guide',
                'description' => 'Update a pillar guide with modified chapters or assets.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'            => array( 'type' => 'string' ),
                        'title'         => array( 'type' => 'string' ),
                        'category'      => array( 'type' => 'string' ),
                        'quick_answer'  => array( 'type' => 'object' ),
                        'chapters'      => array( 'type' => 'array', 'items' => array( 'type' => 'object' ) ),
                        'change_reason' => array( 'type' => 'string' ),
                    ),
                    'required'   => array( 'id' ),
                ),
            ),
            array(
                'name'        => 'growth.upload_asset',
                'description' => 'Upload a media asset (PNG, JPG, WebP, sanitized SVG, or PDF) to the Cora Growth Workspace.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'filename'    => array( 'type' => 'string' ),
                        'mime_type'   => array( 'type' => 'string' ),
                        'base64_data' => array( 'type' => 'string' ),
                        'file_url'    => array( 'type' => 'string' ),
                        'alt_text'    => array( 'type' => 'string' ),
                        'width'       => array( 'type' => 'number' ),
                        'height'      => array( 'type' => 'number' ),
                    ),
                    'required'   => array( 'filename', 'mime_type' ),
                ),
            ),
            array(
                'name'        => 'growth.attach_asset',
                'description' => 'Attach an uploaded asset to a content entry or chapter.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'content_id' => array( 'type' => 'string' ),
                        'asset_id'   => array( 'type' => 'string' ),
                        'role'       => array( 'type' => 'string' ),
                    ),
                    'required'   => array( 'content_id', 'asset_id' ),
                ),
            ),
            array(
                'name'        => 'growth.validate',
                'description' => 'Validate content schema, required blocks, and SEO metadata prior to publication.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id' => array( 'type' => 'string' ),
                    ),
                    'required'   => array( 'id' ),
                ),
            ),
            array(
                'name'        => 'growth.preview',
                'description' => 'Generate a secure HMAC-signed temporary preview token and live preview URL.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id' => array( 'type' => 'string' ),
                    ),
                    'required'   => array( 'id' ),
                ),
            ),
            array(
                'name'        => 'growth.publish',
                'description' => 'Publish a content entry and automatically trigger Next.js on-demand ISR revalidation and live verification.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id' => array( 'type' => 'string' ),
                    ),
                    'required'   => array( 'id' ),
                ),
            ),
            array(
                'name'        => 'growth.get_revisions',
                'description' => 'Get immutable revision snapshots history for a content entry.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id' => array( 'type' => 'string', 'description' => 'Content entry ID' ),
                    ),
                    'required'   => array( 'id' ),
                ),
            ),
            array(
                'name'        => 'growth.rollback',
                'description' => 'Rollback content to a specific previous revision snapshot.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'          => array( 'type' => 'string' ),
                        'revision_id' => array( 'type' => 'string' ),
                    ),
                    'required'   => array( 'id', 'revision_id' ),
                ),
            ),
            array(
                'name'        => 'growth.get_performance',
                'description' => 'Retrieve organic growth performance metrics (impressions, clicks, CTR, position, conversions) from GA4/GSC.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'   => array( 'type' => 'string' ),
                        'days' => array( 'type' => 'number', 'default' => 30 ),
                    ),
                ),
            ),
            array(
                'name'        => 'growth.get_search_opportunities',
                'description' => 'Retrieve striking-distance organic search keyword opportunities (positions 4-20) for optimization.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(),
                ),
            ),
            array(
                'name'        => 'growth.manage_queue',
                'description' => 'List, create, or update background autonomous Growth Agent jobs in the queue.',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'action' => array( 'type' => 'string', 'enum' => array( 'list', 'create', 'update' ) ),
                        'status' => array( 'type' => 'string' ),
                        'job'    => array( 'type' => 'object' ),
                        'job_id' => array( 'type' => 'string' ),
                    ),
                ),
            ),
        );
    }

    /**
     * Helper: Send Next.js on-demand ISR revalidation webhook
     */
    private static function trigger_nextjs_revalidation( $type, $slug ) {
        $frontend_url = defined( 'CORA_FRONTEND_URL' ) ? CORA_FRONTEND_URL : 'http://localhost:3000';
        $reval_url = trailingslashit( $frontend_url ) . 'api/revalidate';
        $secret = get_option( 'cora_growth_revalidate_secret' ) ?: 'cora_revalidate_secret_staging_2026';

        $path = ( $type === 'guide' ? '/guides/' : '/blog/' ) . $slug;
        $tags = array( 'growth-content', $type, $slug );

        $response = wp_remote_post( $reval_url, array(
            'timeout' => 5,
            'headers' => array(
                'Content-Type'              => 'application/json',
                'Authorization'             => 'Bearer ' . $secret,
                'x-cora-revalidate-secret'  => $secret,
            ),
            'body'    => wp_json_encode( array(
                'secret' => $secret,
                'path'   => $path,
                'tags'   => $tags,
            ) ),
        ) );

        return ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200;
    }
}
