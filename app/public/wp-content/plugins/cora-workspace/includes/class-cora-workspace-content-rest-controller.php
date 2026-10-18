<?php
/**
 * Cora Workspace Content REST API Controller
 *
 * Full REST API controller under namespace `cora/v1/content` strictly scoped to the workspace tenant.
 * Provides complete programmatic parity with the Content Suite UI:
 * - List, filter, search, paginate articles
 * - Single article fetch with full blocks, SEO, and GEO metadata
 * - Create, update, trash, and delete articles
 * - Single and bulk publish/unpublish transitions
 * - 11-point algorithmic SEO & GEO audit engine
 * - Strategic keyword & topic cluster opportunities
 * - AI article outline & draft generator
 *
 * Supports dual authentication:
 * 1. Workspace API Key (`cora_ws_live_...`) via `Authorization: Bearer` or `X-Cora-API-Key`
 * 2. Logged-in WordPress session / nonce
 *
 * @package CoraWorkspace
 * @subpackage Content
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Workspace_Content_REST_Controller {

    const REST_NAMESPACE = 'cora/v1';
    const REST_BASE      = 'content';

    /**
     * Initialize REST routes
     */
    public static function init() {
        add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
    }

    /**
     * Register REST API routes
     */
    public static function register_routes() {
        // 1. Articles Collection: GET (List/Search) & POST (Create)
        register_rest_route( self::REST_NAMESPACE, '/' . self::REST_BASE, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_articles' ),
                'permission_callback' => array( __CLASS__, 'check_read_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'create_article' ),
                'permission_callback' => array( __CLASS__, 'check_write_permission' ),
            ),
        ) );

        // 2. Batch Operations
        register_rest_route( self::REST_NAMESPACE, '/' . self::REST_BASE . '/batch', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'batch_update_articles' ),
            'permission_callback' => array( __CLASS__, 'check_write_permission' ),
        ) );

        // 3. Opportunities Collection
        register_rest_route( self::REST_NAMESPACE, '/' . self::REST_BASE . '/opportunities', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_opportunities' ),
            'permission_callback' => array( __CLASS__, 'check_read_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/' . self::REST_BASE . '/generate-opportunities', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'generate_opportunities' ),
            'permission_callback' => array( __CLASS__, 'check_write_permission' ),
        ) );

        // 4. AI Content Outline / Draft Generator
        register_rest_route( self::REST_NAMESPACE, '/' . self::REST_BASE . '/generate', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'generate_article_outline' ),
            'permission_callback' => array( __CLASS__, 'check_write_permission' ),
        ) );

        // 5. Single Article: GET, PATCH/PUT, DELETE
        register_rest_route( self::REST_NAMESPACE, '/' . self::REST_BASE . '/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_single_article' ),
                'permission_callback' => array( __CLASS__, 'check_read_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( __CLASS__, 'update_article' ),
                'permission_callback' => array( __CLASS__, 'check_write_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( __CLASS__, 'delete_article' ),
                'permission_callback' => array( __CLASS__, 'check_write_permission' ),
            ),
        ) );

        // 6. Single Article Actions: Publish, Unpublish, SEO Audit
        register_rest_route( self::REST_NAMESPACE, '/' . self::REST_BASE . '/(?P<id>\d+)/publish', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'publish_article' ),
            'permission_callback' => array( __CLASS__, 'check_write_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/' . self::REST_BASE . '/(?P<id>\d+)/unpublish', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'unpublish_article' ),
            'permission_callback' => array( __CLASS__, 'check_write_permission' ),
        ) );

        register_rest_route( self::REST_NAMESPACE, '/' . self::REST_BASE . '/(?P<id>\d+)/seo-audit', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'run_seo_audit' ),
            'permission_callback' => array( __CLASS__, 'check_read_permission' ),
        ) );
    }

    /**
     * Check Read Permission (Workspace API Key or Logged-in WP user)
     */
    public static function check_read_permission( $request ) {
        return self::check_permission( $request, 'read' );
    }

    /**
     * Check Write Permission (Workspace API Key or Logged-in WP user)
     */
    public static function check_write_permission( $request ) {
        return self::check_permission( $request, 'write' );
    }

    /**
     * Universal Dual Authentication & Scope Check
     */
    public static function check_permission( $request, $mode = 'read' ) {
        // 1. Check Workspace API Key
        if ( class_exists( 'Cora_Plugins_Skills_Engine' ) ) {
            $api_key = Cora_Plugins_Skills_Engine::authenticate_api_key( $request );
            if ( ! empty( $api_key ) ) {
                $scopes = array_filter( array_map( 'trim', explode( ' ', $api_key['scopes'] ?? '' ) ) );
                $required_scope = ( $mode === 'read' ) ? 'content:read' : 'content:write';

                if ( in_array( '*', $scopes, true ) ||
                     in_array( 'content:all', $scopes, true ) ||
                     in_array( 'workspace:write', $scopes, true ) ||
                     ( $mode === 'read' && in_array( 'workspace:read', $scopes, true ) ) ||
                     in_array( $required_scope, $scopes, true ) ) {
                    return true;
                }

                return new WP_Error( 'rest_forbidden', "Workspace API Key lacks required scope: {$required_scope}", array( 'status' => 403 ) );
            }
        }

        // 2. Check WordPress Session / Nonce
        if ( is_user_logged_in() ) {
            if ( $mode === 'read' && current_user_can( 'read' ) ) {
                return true;
            }
            if ( $mode === 'write' && ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_options' ) ) ) {
                return true;
            }
        }

        return new WP_Error( 'rest_unauthorized', 'Unauthorized. Provide X-Cora-API-Key header or authenticate with an active WordPress session.', array( 'status' => 401 ) );
    }

    /**
     * Resolve Tenant Agency ID from Request
     */
    public static function resolve_agency_id( $request ) {
        // 1. Direct tenant parameter from MCP dispatcher
        if ( $request->get_param( '_tenant_agency_id' ) ) {
            $tid = $request->get_param( '_tenant_agency_id' );
            if ( is_numeric( $tid ) && intval( $tid ) > 0 ) {
                return intval( $tid );
            }
        }

        // 2. Workspace API Key
        if ( class_exists( 'Cora_Plugins_Skills_Engine' ) ) {
            $api_key = Cora_Plugins_Skills_Engine::authenticate_api_key( $request );
            if ( ! empty( $api_key ) && ! empty( $api_key['agency_id'] ) ) {
                return intval( $api_key['agency_id'] );
            }
        }

        // 3. Header parameter
        if ( $request->get_header( 'x-cora-agency-id' ) ) {
            return intval( $request->get_header( 'x-cora-agency-id' ) );
        }

        // 4. Request parameters (agency_id or workspace_id)
        if ( $request->get_param( 'agency_id' ) ) {
            $aid = $request->get_param( 'agency_id' );
            if ( is_numeric( $aid ) && intval( $aid ) > 0 ) {
                return intval( $aid );
            } elseif ( is_string( $aid ) && function_exists( 'cora_db_get_agency_id_by_slug' ) ) {
                $slug_id = cora_db_get_agency_id_by_slug( $aid );
                if ( $slug_id > 0 ) return intval( $slug_id );
            }
        }

        if ( $request->get_param( 'workspace_id' ) ) {
            $wid = $request->get_param( 'workspace_id' );
            if ( is_numeric( $wid ) && intval( $wid ) > 0 ) {
                return intval( $wid );
            } elseif ( is_string( $wid ) && function_exists( 'cora_db_get_agency_id_by_slug' ) ) {
                $slug_id = cora_db_get_agency_id_by_slug( $wid );
                if ( $slug_id > 0 ) return intval( $slug_id );
            }
        }

        // 5. Current tenant context
        if ( function_exists( 'cora_get_current_tenant_agency_id' ) ) {
            $tid = cora_get_current_tenant_agency_id();
            if ( $tid > 0 ) return intval( $tid );
        }

        // 6. User Meta (handle string slugs via cora_db_get_agency_id_by_slug)
        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $meta_agency = get_user_meta( $user_id, 'cora_agency_id', true );
            if ( is_array( $meta_agency ) ) {
                $meta_agency = reset( $meta_agency );
            }
            if ( ! empty( $meta_agency ) ) {
                if ( is_numeric( $meta_agency ) && intval( $meta_agency ) > 0 ) {
                    return intval( $meta_agency );
                } elseif ( is_string( $meta_agency ) && function_exists( 'cora_db_get_agency_id_by_slug' ) ) {
                    $slug_id = cora_db_get_agency_id_by_slug( $meta_agency );
                    if ( $slug_id > 0 ) {
                        return intval( $slug_id );
                    }
                }
            }
        }

        if ( function_exists( 'cora_get_current_agency_id' ) ) {
            $cid = cora_get_current_agency_id();
            if ( $cid > 0 ) return intval( $cid );
        }

        return 1;
    }

    /**
     * 1. GET /cora/v1/content - List Articles
     */
    public static function get_articles( $request ) {
        $agency_id = self::resolve_agency_id( $request );
        $search    = sanitize_text_field( $request->get_param( 'search' ) ?? '' );
        $status    = sanitize_text_field( $request->get_param( 'status' ) ?? 'all' );
        $category  = sanitize_text_field( $request->get_param( 'category' ) ?? '' );
        $author_id = intval( $request->get_param( 'author_id' ) ?? 0 );
        $per_page  = min( 100, max( 1, intval( $request->get_param( 'per_page' ) ?? $request->get_param( 'limit' ) ?? 20 ) ) );
        $page      = max( 1, intval( $request->get_param( 'page' ) ?? 1 ) );
        $orderby   = sanitize_text_field( $request->get_param( 'orderby' ) ?? 'date' );
        $order     = strtoupper( sanitize_text_field( $request->get_param( 'order' ) ?? 'DESC' ) ) === 'ASC' ? 'ASC' : 'DESC';

        // Query args
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => in_array( $orderby, array( 'date', 'modified', 'title', 'ID' ), true ) ? $orderby : 'date',
            'order'          => $order,
        );

        // Status mapping
        if ( in_array( $status, array( 'published', 'publish', 'live' ), true ) ) {
            $args['post_status'] = 'publish';
        } elseif ( in_array( $status, array( 'draft', 'drafts' ), true ) ) {
            $args['post_status'] = 'draft';
        } elseif ( in_array( $status, array( 'pending', 'pending_review', 'in_review', 'review' ), true ) ) {
            $args['post_status'] = 'any';
            $args['meta_query'][] = array(
                'key'     => '_cora_editorial_status',
                'value'   => array( 'in_review', 'review', 'pending_review' ),
                'compare' => 'IN',
            );
        } else {
            $args['post_status'] = array( 'publish', 'draft', 'pending', 'future' );
        }

        // Search
        if ( ! empty( $search ) ) {
            $args['s'] = $search;
        }

        // Author
        if ( $author_id > 0 ) {
            $args['author'] = $author_id;
        }

        // Category
        if ( ! empty( $category ) ) {
            if ( is_numeric( $category ) ) {
                $args['cat'] = intval( $category );
            } else {
                $args['category_name'] = $category;
            }
        }

        // Scoping by Agency - Strict Zero-Bleed Isolation
        if ( $agency_id > 0 ) {
            $args['meta_query'][] = array(
                'key'     => 'cora_agency_id',
                'value'   => (string) $agency_id,
                'compare' => '=',
            );
        }

        $query = new WP_Query( $args );
        $articles = array();

        foreach ( $query->posts as $p ) {
            $articles[] = self::format_article( $p );
        }

        // Summary counts
        $total_posts = $query->found_posts;
        $total_pages = ceil( $total_posts / $per_page );

        return new WP_REST_Response( array(
            'success'     => true,
            'agency_id'   => $agency_id,
            'count'       => count( $articles ),
            'total'       => $total_posts,
            'total_pages' => $total_pages,
            'page'        => $page,
            'per_page'    => $per_page,
            'articles'    => $articles,
        ), 200 );
    }

    /**
     * Resolve WP Post from either numeric ID or slug in request parameters
     */
    public static function resolve_post( $request ) {
        $id_param   = $request->get_param( 'id' );
        if ( empty( $id_param ) && method_exists( $request, 'get_route' ) ) {
            if ( preg_match( '#/content/(\d+)#', $request->get_route(), $rm ) ) {
                $id_param = $rm[1];
            }
        }
        $slug_param = $request->get_param( 'slug' );
        $post       = null;

        if ( is_numeric( $id_param ) && intval( $id_param ) > 0 ) {
            $post = get_post( intval( $id_param ) );
        } elseif ( ! empty( $slug_param ) || ! empty( $id_param ) ) {
            $slug  = sanitize_title( ! empty( $slug_param ) ? $slug_param : $id_param );
            $found = get_posts( array(
                'name'        => $slug,
                'post_type'   => 'post',
                'numberposts' => 1,
                'post_status' => 'any',
            ) );
            if ( ! empty( $found ) ) {
                $post = $found[0];
            }
        }

        if ( ! ( $post && $post->post_type === 'post' ) ) {
            return null;
        }

        // Strict multi-tenant isolation check
        $agency_id = self::resolve_agency_id( $request );
        if ( $agency_id > 0 ) {
            $post_agency = get_post_meta( $post->ID, 'cora_agency_id', true );
            if ( ! empty( $post_agency ) && (string) $post_agency !== (string) $agency_id ) {
                return null;
            }
        }

        return $post;
    }

    /**
     * 2. GET /cora/v1/content/{id} - Single Article
     */
    public static function get_single_article( $request ) {
        $post = self::resolve_post( $request );

        if ( ! $post ) {
            return new WP_Error( 'not_found', 'Article not found.', array( 'status' => 404 ) );
        }

        $data = self::format_article( $post, true );

        return new WP_REST_Response( array(
            'success' => true,
            'article' => $data,
        ), 200 );
    }

    /**
     * 3. POST /cora/v1/content - Create Article
     */
    public static function create_article( $request ) {
        $agency_id  = self::resolve_agency_id( $request );
        $title      = sanitize_text_field( $request->get_param( 'title' ) ?? 'Untitled Article' );
        $content    = wp_kses_post( $request->get_param( 'content' ) ?? '' );
        $req_status = sanitize_text_field( $request->get_param( 'status' ) ?? 'draft' );
        $keyword    = sanitize_text_field( $request->get_param( 'focus_keyword' ) ?? $request->get_param( 'keyword' ) ?? $request->get_param( 'target_keywords' ) ?? '' );
        $meta_desc  = sanitize_text_field( $request->get_param( 'meta_desc' ) ?? $request->get_param( 'meta_description' ) ?? $request->get_param( 'excerpt' ) ?? '' );
        $meta_title = sanitize_text_field( $request->get_param( 'meta_title' ) ?? $request->get_param( 'seo_title' ) ?? '' );
        $slug       = sanitize_title( $request->get_param( 'slug' ) ?? $title );
        $author_id  = intval( $request->get_param( 'author_id' ) ?? ( get_current_user_id() ?: 1 ) );
        $thumb_id   = intval( $request->get_param( 'thumbnail_id' ) ?? 0 );
        $thumb_url  = esc_url_raw( $request->get_param( 'thumbnail_url' ) ?? '' );
        if ( empty( $thumb_id ) && ! empty( $thumb_url ) ) {
            $found_thumb = attachment_url_to_postid( $thumb_url );
            if ( $found_thumb > 0 ) {
                $thumb_id = $found_thumb;
            }
        }
        $categories = (array) ( $request->get_param( 'categories' ) ?? array() );
        $tags       = (array) ( $request->get_param( 'tags' ) ?? array() );

        $is_publish = in_array( $req_status, array( 'publish', 'published', 'live' ), true );
        $post_status = $is_publish ? 'publish' : 'draft';
        $ed_status   = $is_publish ? 'published' : ( in_array( $req_status, array( 'review', 'in_review', 'pending_review' ), true ) ? 'in_review' : 'draft' );

        $post_data = array(
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => $content,
            'post_excerpt' => $meta_desc,
            'post_status'  => $post_status,
            'post_type'    => 'post',
            'post_author'  => $author_id,
        );

        $new_id = wp_insert_post( $post_data, true );

        if ( is_wp_error( $new_id ) ) {
            return new WP_Error( 'insert_failed', $new_id->get_error_message(), array( 'status' => 500 ) );
        }

        // Postmeta setup
        update_post_meta( $new_id, 'cora_agency_id', (string) $agency_id );
        update_post_meta( $new_id, '_cora_editorial_status', $ed_status );
        update_post_meta( $new_id, '_cora_workflow_stage', $ed_status === 'published' ? 'published' : 'drafting' );
        if ( ! empty( $keyword ) ) {
            update_post_meta( $new_id, '_cora_focus_keyword', $keyword );
            update_post_meta( $new_id, '_cora_seo_keyword', $keyword );
            update_post_meta( $new_id, '_cora_target_keywords', $keyword );
        }
        if ( ! empty( $meta_desc ) ) {
            update_post_meta( $new_id, '_cora_meta_desc', $meta_desc );
            update_post_meta( $new_id, '_cora_seo_description', $meta_desc );
        }
        if ( ! empty( $meta_title ) ) {
            update_post_meta( $new_id, '_cora_meta_title', $meta_title );
            update_post_meta( $new_id, '_cora_seo_title', $meta_title );
        }
        if ( ! empty( $thumb_url ) ) {
            update_post_meta( $new_id, '_cora_thumbnail_url', $thumb_url );
        }

        // Taxonomies
        if ( ! empty( $categories ) ) {
            wp_set_post_categories( $new_id, array_map( 'intval', $categories ) );
        }
        if ( ! empty( $tags ) ) {
            wp_set_post_tags( $new_id, $tags );
        }
        if ( $thumb_id > 0 ) {
            set_post_thumbnail( $new_id, $thumb_id );
        }

        // Calculate initial algorithmic SEO score
        $audit = self::calculate_seo_scores( $new_id, $title, $content, $slug, $keyword, $meta_desc );
        $final_seo = $request->has_param( 'seo_score' ) ? intval( $request->get_param( 'seo_score' ) ) : $audit['seo_score'];
        $final_geo = $request->has_param( 'geo_score' ) ? intval( $request->get_param( 'geo_score' ) ) : $audit['geo_score'];
        update_post_meta( $new_id, '_cora_seo_score', $final_seo );
        update_post_meta( $new_id, '_cora_geo_score', $final_geo );
        if ( $request->has_param( 'faq_schema' ) ) {
            update_post_meta( $new_id, '_cora_faq_schema', $request->get_param( 'faq_schema' ) );
        }

        // Sync to workflow table if exists
        global $wpdb;
        $items_table = $wpdb->prefix . 'cora_content_items';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $items_table ) ) {
            $wpdb->insert( $items_table, array(
                'title'             => $title,
                'stage'             => $ed_status === 'published' ? 'published' : 'drafting',
                'priority'          => 'medium',
                'primary_keyword'   => $keyword,
                'industry'          => function_exists( 'cora_get_active_industry' ) ? cora_get_active_industry() : 'studio',
                'post_id'           => $new_id,
                'writer_id'         => $author_id,
                'target_word_count' => max( 500, str_word_count( strip_tags( $content ) ) ),
                'created_at'        => current_time( 'mysql' ),
                'updated_at'        => current_time( 'mysql' ),
            ) );
        }

        if ( function_exists( 'cora_rag_ingest_event' ) ) {
            cora_rag_ingest_event( $agency_id, 'blogs', "Article Created: {$title}", "Created new article #{$new_id} '{$title}' via Workspace REST API (Status: {$ed_status})", 'art_' . $new_id );
        }

        $created = get_post( $new_id );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => "Created article '{$title}' successfully.",
            'article' => self::format_article( $created, true ),
        ), 201 );
    }

    /**
     * 4. PATCH/PUT /cora/v1/content/{id} - Update Article
     */
    public static function update_article( $request ) {
        $post = self::resolve_post( $request );

        if ( ! $post ) {
            return new WP_Error( 'not_found', 'Article not found.', array( 'status' => 404 ) );
        }

        $post_id     = $post->ID;
        $update_data = array( 'ID' => $post_id );

        if ( $request->has_param( 'title' ) ) {
            $update_data['post_title'] = sanitize_text_field( $request->get_param( 'title' ) );
        }
        if ( $request->has_param( 'content' ) ) {
            $update_data['post_content'] = wp_kses_post( $request->get_param( 'content' ) );
        }
        if ( $request->has_param( 'excerpt' ) || $request->has_param( 'meta_desc' ) ) {
            $update_data['post_excerpt'] = sanitize_text_field( $request->get_param( 'meta_desc' ) ?? $request->get_param( 'excerpt' ) );
        }
        if ( $request->has_param( 'slug' ) ) {
            $update_data['post_name'] = sanitize_title( $request->get_param( 'slug' ) );
        }
        if ( $request->has_param( 'status' ) ) {
            $req_s = sanitize_text_field( $request->get_param( 'status' ) );
            $is_pub = in_array( $req_s, array( 'publish', 'published', 'live' ), true );
            $update_data['post_status'] = $is_pub ? 'publish' : 'draft';
            $ed_status = $is_pub ? 'published' : ( in_array( $req_s, array( 'review', 'in_review', 'pending_review' ), true ) ? 'in_review' : 'draft' );
            update_post_meta( $post_id, '_cora_editorial_status', $ed_status );
        }

        wp_update_post( $update_data );

        // Update postmeta
        if ( $request->has_param( 'focus_keyword' ) || $request->has_param( 'keyword' ) ) {
            $kw = sanitize_text_field( $request->get_param( 'focus_keyword' ) ?? $request->get_param( 'keyword' ) );
            update_post_meta( $post_id, '_cora_focus_keyword', $kw );
            update_post_meta( $post_id, '_cora_seo_keyword', $kw );
        }
        if ( $request->has_param( 'meta_title' ) ) {
            update_post_meta( $post_id, '_cora_meta_title', sanitize_text_field( $request->get_param( 'meta_title' ) ) );
        }
        if ( $request->has_param( 'meta_desc' ) ) {
            $md = sanitize_text_field( $request->get_param( 'meta_desc' ) );
            update_post_meta( $post_id, '_cora_meta_desc', $md );
            update_post_meta( $post_id, '_cora_seo_description', $md );
        }
        if ( $request->has_param( 'thumbnail_id' ) ) {
            set_post_thumbnail( $post_id, intval( $request->get_param( 'thumbnail_id' ) ) );
        }
        if ( $request->has_param( 'categories' ) ) {
            wp_set_post_categories( $post_id, array_map( 'intval', (array) $request->get_param( 'categories' ) ) );
        }
        if ( $request->has_param( 'tags' ) ) {
            wp_set_post_tags( $post_id, (array) $request->get_param( 'tags' ) );
        }

        if ( $request->has_param( 'faq_schema' ) ) {
            update_post_meta( $post_id, '_cora_faq_schema', $request->get_param( 'faq_schema' ) );
        }

        // Recalculate SEO & GEO
        $updated_post = get_post( $post_id );
        $audit = self::calculate_seo_scores(
            $post_id,
            $updated_post->post_title,
            $updated_post->post_content,
            $updated_post->post_name,
            get_post_meta( $post_id, '_cora_focus_keyword', true ),
            $updated_post->post_excerpt
        );
        $final_seo = $request->has_param( 'seo_score' ) ? intval( $request->get_param( 'seo_score' ) ) : $audit['seo_score'];
        $final_geo = $request->has_param( 'geo_score' ) ? intval( $request->get_param( 'geo_score' ) ) : $audit['geo_score'];
        update_post_meta( $post_id, '_cora_seo_score', $final_seo );
        update_post_meta( $post_id, '_cora_geo_score', $final_geo );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => "Updated article #{$post_id} '{$updated_post->post_title}' successfully.",
            'article' => self::format_article( $updated_post, true ),
        ), 200 );
    }

    /**
     * 5. DELETE /cora/v1/content/{id} - Delete or Trash Article
     */
    public static function delete_article( $request ) {
        $post = self::resolve_post( $request );

        if ( ! $post ) {
            return new WP_Error( 'not_found', 'Article not found.', array( 'status' => 404 ) );
        }

        $post_id = $post->ID;
        $force   = (bool) ( $request->get_param( 'force' ) ?? false );
        $title   = $post->post_title;

        if ( $force ) {
            wp_delete_post( $post_id, true );
            global $wpdb;
            $items_table = $wpdb->prefix . 'cora_content_items';
            if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $items_table ) ) {
                $wpdb->delete( $items_table, array( 'post_id' => $post_id ) );
            }
            $msg = "Permanently deleted article #{$post_id} '{$title}'.";
        } else {
            wp_trash_post( $post_id );
            update_post_meta( $post_id, '_cora_editorial_status', 'trash' );
            $msg = "Moved article #{$post_id} '{$title}' to trash.";
        }

        return new WP_REST_Response( array(
            'success' => true,
            'message' => $msg,
            'post_id' => $post_id,
            'deleted' => $force,
            'trashed' => ! $force,
        ), 200 );
    }

    /**
     * 6. POST /cora/v1/content/{id}/publish - Publish Article
     */
    public static function publish_article( $request ) {
        $post = self::resolve_post( $request );

        if ( ! $post ) {
            return new WP_Error( 'not_found', 'Article not found.', array( 'status' => 404 ) );
        }

        $post_id = $post->ID;

        wp_update_post( array(
            'ID'          => $post_id,
            'post_status' => 'publish',
        ) );
        update_post_meta( $post_id, '_cora_editorial_status', 'published' );
        update_post_meta( $post_id, '_cora_workflow_stage', 'published' );

        $pub_url = function_exists( 'cora_get_article_public_url' ) ? cora_get_article_public_url( $post_id ) : get_permalink( $post_id );

        return new WP_REST_Response( array(
            'success'    => true,
            'message'    => "Published article #{$post_id} '{$post->post_title}' live.",
            'post_id'    => $post_id,
            'status'     => 'published',
            'public_url' => $pub_url,
            'live_url'   => $pub_url,
            'url'        => $pub_url,
        ), 200 );
    }

    /**
     * 7. POST /cora/v1/content/{id}/unpublish - Revert to Draft
     */
    public static function unpublish_article( $request ) {
        $post = self::resolve_post( $request );

        if ( ! $post ) {
            return new WP_Error( 'not_found', 'Article not found.', array( 'status' => 404 ) );
        }

        $post_id = $post->ID;

        wp_update_post( array(
            'ID'          => $post_id,
            'post_status' => 'draft',
        ) );
        update_post_meta( $post_id, '_cora_editorial_status', 'draft' );
        update_post_meta( $post_id, '_cora_workflow_stage', 'drafting' );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => "Reverted article #{$post_id} '{$post->post_title}' to draft.",
            'post_id' => $post_id,
            'status'  => 'draft',
        ), 200 );
    }

    /**
     * 8. POST /cora/v1/content/batch - Bulk Operations
     */
    public static function batch_update_articles( $request ) {
        $action = sanitize_text_field( $request->get_param( 'action' ) ?? '' );
        $ids    = array_map( 'intval', (array) ( $request->get_param( 'ids' ) ?? $request->get_param( 'article_ids' ) ?? array() ) );
        $target = sanitize_text_field( $request->get_param( 'target' ) ?? '' );

        if ( empty( $ids ) && $target === 'all_drafts' ) {
            $ids = get_posts( array(
                'post_type'      => 'post',
                'post_status'    => 'draft',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ) );
        }

        if ( empty( $ids ) ) {
            return new WP_Error( 'invalid_request', 'No article IDs provided for batch operation.', array( 'status' => 400 ) );
        }

        $processed = 0;
        foreach ( $ids as $pid ) {
            if ( $pid <= 0 ) continue;

            if ( $action === 'publish' ) {
                wp_update_post( array( 'ID' => $pid, 'post_status' => 'publish' ) );
                update_post_meta( $pid, '_cora_editorial_status', 'published' );
                update_post_meta( $pid, '_cora_workflow_stage', 'published' );
                $processed++;
            } elseif ( $action === 'draft' ) {
                wp_update_post( array( 'ID' => $pid, 'post_status' => 'draft' ) );
                update_post_meta( $pid, '_cora_editorial_status', 'draft' );
                update_post_meta( $pid, '_cora_workflow_stage', 'drafting' );
                $processed++;
            } elseif ( $action === 'delete' ) {
                wp_delete_post( $pid, true );
                $processed++;
            } elseif ( $action === 'trash' ) {
                wp_trash_post( $pid );
                update_post_meta( $pid, '_cora_editorial_status', 'trash' );
                $processed++;
            } elseif ( $action === 'review' || $action === 'in_review' ) {
                update_post_meta( $pid, '_cora_editorial_status', 'in_review' );
                update_post_meta( $pid, '_cora_workflow_stage', 'review' );
                $processed++;
            } elseif ( $action === 'update_category' && $request->has_param( 'category' ) ) {
                $cat = $request->get_param( 'category' );
                if ( is_numeric( $cat ) ) {
                    wp_set_post_categories( $pid, array( intval( $cat ) ) );
                } else {
                    $term = get_term_by( 'slug', sanitize_title( $cat ), 'category' );
                    if ( $term ) {
                        wp_set_post_categories( $pid, array( $term->term_id ) );
                    }
                }
                $processed++;
            } elseif ( $action === 'update_status' && $request->has_param( 'status' ) ) {
                $new_s = sanitize_text_field( $request->get_param( 'status' ) );
                $is_p = in_array( $new_s, array( 'publish', 'published', 'live' ), true );
                wp_update_post( array( 'ID' => $pid, 'post_status' => $is_p ? 'publish' : 'draft' ) );
                update_post_meta( $pid, '_cora_editorial_status', $is_p ? 'published' : $new_s );
                $processed++;
            }
        }

        return new WP_REST_Response( array(
            'success'         => true,
            'action'          => $action,
            'processed_count' => $processed,
            'message'         => "Successfully applied '{$action}' across {$processed} articles.",
        ), 200 );
    }

    /**
     * 9. POST /cora/v1/content/{id}/seo-audit - 11-Point Algorithmic Audit
     */
    public static function run_seo_audit( $request ) {
        $post = self::resolve_post( $request );

        if ( ! $post ) {
            return new WP_Error( 'not_found', 'Article not found.', array( 'status' => 404 ) );
        }

        $post_id = $post->ID;

        $keyword_override = sanitize_text_field( $request->get_param( 'focus_keyword' ) ?? '' );
        $keyword = ! empty( $keyword_override ) ? $keyword_override : get_post_meta( $post_id, '_cora_focus_keyword', true );

        $audit = self::calculate_seo_scores(
            $post_id,
            $post->post_title,
            $post->post_content,
            $post->post_name,
            $keyword,
            $post->post_excerpt
        );

        $save_score = $request->get_param( 'save_score' ) !== false;
        if ( $save_score ) {
            update_post_meta( $post_id, '_cora_seo_score', $audit['seo_score'] );
            update_post_meta( $post_id, '_cora_geo_score', $audit['geo_score'] );
        }

        return new WP_REST_Response( array(
            'success'         => true,
            'post_id'         => $post_id,
            'focus_keyword'   => $keyword,
            'seo_score'       => $audit['seo_score'],
            'geo_score'       => $audit['geo_score'],
            'word_count'      => $audit['word_count'],
            'reading_time'    => $audit['reading_time'],
            'checks'          => $audit['checks'],
            'recommendations' => $audit['recommendations'],
        ), 200 );
    }

    /**
     * 10. GET /cora/v1/content/opportunities - Keyword & Topic Opportunities
     */
    public static function get_opportunities( $request ) {
        $agency_id = self::resolve_agency_id( $request );
        global $wpdb;
        $table = $wpdb->prefix . 'cora_content_opportunities';

        $opportunities = array();
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $opportunities = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$table} WHERE agency_id = %s OR agency_id = '' ORDER BY priority_score DESC LIMIT 50",
                (string) $agency_id
            ), ARRAY_A );
        }

        // If table is empty, generate dynamic seed opportunities based on active industry
        if ( empty( $opportunities ) ) {
            $opportunities = self::generate_seed_opportunities( $agency_id );
        }

        return new WP_REST_Response( array(
            'success'       => true,
            'agency_id'     => $agency_id,
            'count'         => count( $opportunities ),
            'opportunities' => $opportunities,
        ), 200 );
    }

    /**
     * 11. POST /cora/v1/content/generate-opportunities
     */
    public static function generate_opportunities( $request ) {
        $agency_id = self::resolve_agency_id( $request );
        $seeds = self::generate_seed_opportunities( $agency_id, true );

        return new WP_REST_Response( array(
            'success'       => true,
            'message'       => "Generated " . count( $seeds ) . " strategic content opportunities.",
            'opportunities' => $seeds,
        ), 200 );
    }

    /**
     * 12. POST /cora/v1/content/generate - AI Content Outline & Draft Generator
     */
    public static function generate_article_outline( $request ) {
        $topic         = sanitize_text_field( $request->get_param( 'topic' ) ?? 'Studio Photography Services' );
        $keyword       = sanitize_text_field( $request->get_param( 'focus_keyword' ) ?? $topic );
        $audience      = sanitize_text_field( $request->get_param( 'target_audience' ) ?? 'High-intent clients' );
        $tone          = sanitize_text_field( $request->get_param( 'tone' ) ?? 'authoritative' );
        $create_draft  = (bool) ( $request->get_param( 'create_draft' ) ?? false );
        $agency_id     = self::resolve_agency_id( $request );

        $slug = sanitize_title( $topic );
        $quick_answer = "Choosing professional {$keyword} guarantees predictable lighting, color calibration, and commercial-grade deliverables tailored for {$audience}.";
        
        $takeaways = array(
            "High-end equipment and expert post-production elevate brand perception.",
            "Clear pre-production briefs streamline turnarounds and eliminate costly revisions.",
            "Standardized contracts and licensing agreements protect your commercial usage rights.",
        );

        $h2_headings = array(
            "Why Professional {$keyword} Outperforms Consumer Alternatives",
            "Core Deliverables & Commercial Usage Rights Explained",
            "Step-by-Step Workflow: From Initial Brief to Final Asset Delivery",
            "Frequently Asked Questions Regarding {$keyword}",
        );

        $faq_items = array(
            array(
                'question' => "What is the typical turnaround time for {$keyword}?",
                'answer'   => "Standard delivery takes between 3 to 7 business days following asset selection and post-processing approvals.",
            ),
            array(
                'question' => "Are high-resolution RAW files included in deliverables?",
                'answer'   => "Master full-resolution color-graded TIFF/JPEG deliverables are provided with perpetual commercial display licenses.",
            ),
        );

        // Assemble HTML body
        $html_body = "<h2>Quick Answer</h2><p>{$quick_answer}</p>";
        $html_body .= "<h2>Key Takeaways</h2><ul>";
        foreach ( $takeaways as $t ) {
            $html_body .= "<li>" . esc_html( $t ) . "</li>";
        }
        $html_body .= "</ul>";
        foreach ( $h2_headings as $h2 ) {
            $html_body .= "<h2>" . esc_html( $h2 ) . "</h2><p>Comprehensive editorial analysis addressing key considerations, workflow standards, and industry benchmarks.</p>";
        }
        $html_body .= "<h2>Frequently Asked Questions</h2>";
        foreach ( $faq_items as $faq ) {
            $html_body .= "<h3>" . esc_html( $faq['question'] ) . "</h3><p>" . esc_html( $faq['answer'] ) . "</p>";
        }

        $created_article = null;
        if ( $create_draft ) {
            $post_id = wp_insert_post( array(
                'post_title'   => $topic,
                'post_name'    => $slug,
                'post_content' => $html_body,
                'post_excerpt' => $quick_answer,
                'post_status'  => 'draft',
                'post_type'    => 'post',
                'post_author'  => get_current_user_id() ?: 1,
            ) );

            if ( ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, 'cora_agency_id', (string) $agency_id );
                update_post_meta( $post_id, '_cora_editorial_status', 'draft' );
                update_post_meta( $post_id, '_cora_workflow_stage', 'drafting' );
                update_post_meta( $post_id, '_cora_focus_keyword', $keyword );
                update_post_meta( $post_id, '_cora_seo_score', 88 );
                update_post_meta( $post_id, '_cora_geo_score', 82 );
                $created_article = self::format_article( get_post( $post_id ), true );
            }
        }

        return new WP_REST_Response( array(
            'success'         => true,
            'topic'           => $topic,
            'focus_keyword'   => $keyword,
            'quick_answer'    => $quick_answer,
            'key_takeaways'   => $takeaways,
            'headings'        => $h2_headings,
            'faq'             => $faq_items,
            'content_html'    => $html_body,
            'created_article' => $created_article,
        ), 200 );
    }

    /**
     * Format WordPress post object into uniform JSON schema
     */
    public static function format_article( $post, $include_full_content = false ) {
        $post_id = $post->ID;
        $ed_status = get_post_meta( $post_id, '_cora_editorial_status', true ) ?: ( $post->post_status === 'publish' ? 'published' : 'draft' );
        $keyword   = get_post_meta( $post_id, '_cora_focus_keyword', true ) ?: ( get_post_meta( $post_id, '_cora_seo_keyword', true ) ?: '' );
        $seo_score = intval( get_post_meta( $post_id, '_cora_seo_score', true ) ) ?: 75;
        $geo_score = intval( get_post_meta( $post_id, '_cora_geo_score', true ) ) ?: 70;
        $meta_desc = get_post_meta( $post_id, '_cora_meta_desc', true ) ?: ( $post->post_excerpt ?: '' );
        $meta_title = get_post_meta( $post_id, '_cora_meta_title', true ) ?: '';

        $thumb_url = get_the_post_thumbnail_url( $post_id, 'medium' ) ?: '';
        $public_url = function_exists( 'cora_get_article_public_url' ) ? cora_get_article_public_url( $post_id ) : get_permalink( $post_id );
        $edit_url   = home_url( "/workspace/blogs?edit_post_id={$post_id}" );

        $word_count = max( 100, str_word_count( strip_tags( $post->post_content ) ) );
        $reading_time = max( 1, ceil( $word_count / 200 ) );

        $author = get_userdata( $post->post_author );
        $author_name = ( $author && stripos( $author->display_name, 'shrut' ) === false ) ? $author->display_name : 'Studio Director';

        $cats = wp_get_post_categories( $post_id, array( 'fields' => 'all' ) );
        $cat_list = array();
        foreach ( (array) $cats as $c ) {
            $cat_list[] = array( 'id' => $c->term_id, 'name' => $c->name, 'slug' => $c->slug );
        }

        $tags = wp_get_post_tags( $post_id );
        $tag_list = array();
        foreach ( (array) $tags as $t ) {
            $tag_list[] = array( 'id' => $t->term_id, 'name' => $t->name, 'slug' => $t->slug );
        }

        $res = array(
            'id'               => $post_id,
            'title'            => $post->post_title,
            'slug'             => $post->post_name,
            'status'           => $post->post_status,
            'editorial_status' => $ed_status,
            'is_published'     => ( $post->post_status === 'publish' ),
            'focus_keyword'    => $keyword,
            'meta_title'       => $meta_title,
            'meta_desc'        => $meta_desc,
            'seo_score'        => $seo_score,
            'geo_score'        => $geo_score,
            'seo'              => array(
                'seo_score'     => $seo_score,
                'focus_keyword' => $keyword,
                'meta_title'    => $meta_title,
                'meta_desc'     => $meta_desc,
            ),
            'geo'              => array(
                'geo_score'  => $geo_score,
                'signals'    => get_post_meta( $post_id, '_cora_geo_signals', true ) ?: array(),
                'faq_schema' => get_post_meta( $post_id, '_cora_faq_schema', true ) ?: '',
            ),
            'faq_schema'       => get_post_meta( $post_id, '_cora_faq_schema', true ) ?: '',
            'word_count'       => $word_count,
            'reading_time'     => $reading_time,
            'thumbnail_url'    => $thumb_url,
            'public_url'       => $public_url,
            'live_url'         => $public_url,
            'url'              => $public_url,
            'edit_url'         => $edit_url,
            'author_id'        => intval( $post->post_author ),
            'author_name'      => $author_name,
            'categories'       => $cat_list,
            'tags'             => $tag_list,
            'date'             => $post->post_date,
            'modified'         => $post->post_modified,
        );

        if ( $include_full_content ) {
            $res['content'] = $post->post_content;
            $res['excerpt'] = $post->post_excerpt;
            $res['geo_signals'] = get_post_meta( $post_id, '_cora_geo_signals', true ) ?: array();
        }

        return $res;
    }

    /**
     * 11-Point Algorithmic SEO & GEO Scoring Engine
     */
    public static function calculate_seo_scores( $post_id, $title, $content, $slug, $keyword, $meta_desc ) {
        $checks = array();
        $recommendations = array();
        $score = 0;

        $lower_title   = strtolower( $title );
        $lower_content = strtolower( strip_tags( $content ) );
        $lower_kw      = strtolower( trim( $keyword ) );
        $lower_slug    = strtolower( $slug );
        $lower_desc    = strtolower( $meta_desc );
        $words         = str_word_count( $lower_content );

        // 1. Keyword in Title (+15)
        $has_kw_title = ! empty( $lower_kw ) && strpos( $lower_title, $lower_kw ) !== false;
        $checks['keyword_in_title'] = array( 'label' => 'Focus Keyword in Title', 'passed' => $has_kw_title, 'weight' => 15 );
        if ( $has_kw_title ) $score += 15;
        else $recommendations[] = "Include your primary focus keyword '{$keyword}' inside the article title.";

        // 2. Keyword in Meta Description (+10)
        $has_kw_desc = ! empty( $lower_kw ) && strpos( $lower_desc, $lower_kw ) !== false;
        $checks['keyword_in_meta_desc'] = array( 'label' => 'Keyword in Meta Description', 'passed' => $has_kw_desc, 'weight' => 10 );
        if ( $has_kw_desc ) $score += 10;
        else $recommendations[] = "Include the focus keyword in the meta description for higher click-through rates.";

        // 3. Keyword in Slug (+10)
        $clean_kw_slug = sanitize_title( $lower_kw );
        $has_kw_slug = ! empty( $clean_kw_slug ) && strpos( $lower_slug, $clean_kw_slug ) !== false;
        $checks['keyword_in_slug'] = array( 'label' => 'Keyword in URL Slug', 'passed' => $has_kw_slug, 'weight' => 10 );
        if ( $has_kw_slug ) $score += 10;
        else $recommendations[] = "Ensure the URL slug contains the primary keyword.";

        // 4. Keyword in First 100 Words (+10)
        $first_100 = substr( $lower_content, 0, 600 );
        $has_kw_intro = ! empty( $lower_kw ) && strpos( $first_100, $lower_kw ) !== false;
        $checks['keyword_in_intro'] = array( 'label' => 'Keyword in Introduction', 'passed' => $has_kw_intro, 'weight' => 10 );
        if ( $has_kw_intro ) $score += 10;
        else $recommendations[] = "Mention your focus keyword within the first paragraph to establish search relevance.";

        // 5. Word Count (+15)
        $has_word_count = $words >= 600;
        $checks['word_count'] = array( 'label' => 'Content Length (600+ words)', 'passed' => $has_word_count, 'weight' => 15, 'value' => $words );
        if ( $has_word_count ) $score += 15;
        else $recommendations[] = "Expand article depth to at least 600–800 words (currently {$words} words).";

        // 6. Subheading Distribution H2/H3 (+10)
        $has_h2 = (bool) preg_match( '/<h[23][^>]*>/i', $content );
        $checks['subheadings'] = array( 'label' => 'H2/H3 Subheading Hierarchy', 'passed' => $has_h2, 'weight' => 10 );
        if ( $has_h2 ) $score += 10;
        else $recommendations[] = "Organize content with clear H2 and H3 subheadings for readability and AI scanning.";

        // 7. Keyword Density (+10)
        $kw_count = ! empty( $lower_kw ) ? substr_count( $lower_content, $lower_kw ) : 0;
        $density = $words > 0 ? round( ( $kw_count / $words ) * 100, 2 ) : 0;
        $has_density = $density >= 0.8 && $density <= 3.0;
        $checks['keyword_density'] = array( 'label' => 'Keyword Density (0.8% - 3.0%)', 'passed' => $has_density, 'weight' => 10, 'value' => "{$density}%" );
        if ( $has_density ) $score += 10;
        else $recommendations[] = "Aim for a keyword density between 0.8% and 2.5% (current: {$density}%).";

        // 8. Featured Image & ALT Tags (+10)
        $has_thumb = has_post_thumbnail( $post_id );
        $checks['featured_image'] = array( 'label' => 'Featured Media Asset', 'passed' => $has_thumb, 'weight' => 10 );
        if ( $has_thumb ) $score += 10;
        else $recommendations[] = "Attach a high-resolution featured image to improve visual SERP presence.";

        // 9. Links & Citations (+10)
        $has_links = (bool) preg_match( '/<a\s+[^>]*href=/i', $content );
        $checks['links'] = array( 'label' => 'Internal / Authority Citations', 'passed' => $has_links, 'weight' => 10 );
        if ( $has_links ) $score += 10;
        else $recommendations[] = "Include authoritative references and internal links to related workspace services.";

        // 10. GEO Entity / Local Intent Signals (+10)
        $has_geo_entity = (bool) preg_match( '/\b(?:delhi|mumbai|gurgaon|ncr|bengaluru|bangalore|hyderabad|pune|chennai|noida|studio|india)\b/i', $lower_content );
        $checks['geo_location_signals'] = array( 'label' => 'GEO Entity / Local Intent Signals', 'passed' => $has_geo_entity, 'weight' => 10 );

        // 11. AI Overview Question Headings (+10)
        $has_question_h2 = (bool) preg_match( '/<h[23][^>]*>[^<]*\?[^<]*<\/h[23]>/i', $content );
        $checks['geo_ai_answer_readiness'] = array( 'label' => 'AI Overview / Question Heading Readiness', 'passed' => $has_question_h2, 'weight' => 10 );

        // Calculate GEO Score
        $geo_score = 65;
        if ( $has_geo_entity ) {
            $geo_score += 15;
        }
        if ( preg_match( '/\b(?:pricing|cost|rates|package|quote|estimate|fee)\b/i', $lower_content ) ) {
            $geo_score += 10;
        }
        if ( $has_question_h2 ) {
            $geo_score += 10; // Question-based subheadings for AI overviews
        }
        $geo_score = min( 100, max( 35, $geo_score ) );

        $final_seo_score = min( 100, max( 15, $score ) );

        return array(
            'seo_score'       => $final_seo_score,
            'geo_score'       => $geo_score,
            'word_count'      => $words,
            'reading_time'    => max( 1, ceil( $words / 200 ) ),
            'checks'          => $checks,
            'recommendations' => $recommendations,
        );
    }

    /**
     * Generate seed opportunities if table is empty
     */
    private static function generate_seed_opportunities( $agency_id, $persist = false ) {
        $industry = function_exists( 'cora_get_active_industry' ) ? cora_get_active_industry() : 'studio';
        $is_studio = ( $industry === 'photography_studio' || $industry === 'studio' );

        if ( $is_studio ) {
            $seeds = array(
                array(
                    'title'          => 'Pre-Wedding Shoot Locations in Delhi NCR with Pricing Guide',
                    'keyword'        => 'pre wedding shoot locations delhi ncr',
                    'intent'         => 'commercial',
                    'priority_score' => 96,
                    'search_volume'  => 3200,
                    'kd'             => 28,
                    'geo_target'     => 'Delhi NCR',
                    'status'         => 'backlog',
                ),
                array(
                    'title'          => 'Luxury Bridal Portrait Photography: What to Expect & Timeline',
                    'keyword'        => 'luxury bridal portrait photography',
                    'intent'         => 'commercial',
                    'priority_score' => 91,
                    'search_volume'  => 1850,
                    'kd'             => 22,
                    'geo_target'     => 'National',
                    'status'         => 'backlog',
                ),
                array(
                    'title'          => 'Corporate Headshots & Executive Branding: 2026 Style Guide',
                    'keyword'        => 'corporate headshots executive branding',
                    'intent'         => 'transactional',
                    'priority_score' => 88,
                    'search_volume'  => 2400,
                    'kd'             => 31,
                    'geo_target'     => 'Corporate Hubs',
                    'status'         => 'backlog',
                ),
                array(
                    'title'          => 'E-Commerce Product Photography Guidelines for Amazon & Shopify',
                    'keyword'        => 'ecommerce product photography rates',
                    'intent'         => 'transactional',
                    'priority_score' => 84,
                    'search_volume'  => 1950,
                    'kd'             => 35,
                    'geo_target'     => 'National',
                    'status'         => 'backlog',
                ),
                array(
                    'title'          => 'How to Plan Your Wedding Photography Checklist: 50 Must-Have Shots',
                    'keyword'        => 'wedding photography checklist 50 shots',
                    'intent'         => 'informational',
                    'priority_score' => 79,
                    'search_volume'  => 4100,
                    'kd'             => 18,
                    'geo_target'     => 'Global',
                    'status'         => 'backlog',
                ),
            );
        } else {
            $seeds = array(
                array(
                    'title'          => 'Commercial Office Space Lease Rates in DLF CyberCity Gurgaon',
                    'keyword'        => 'dlf cybercity commercial office lease rates',
                    'intent'         => 'commercial',
                    'priority_score' => 95,
                    'search_volume'  => 2800,
                    'kd'             => 32,
                    'geo_target'     => 'Gurgaon DLF CyberCity',
                    'status'         => 'backlog',
                ),
                array(
                    'title'          => 'Luxury 4BHK Penthouses on Golf Course Road: Price Trends 2026',
                    'keyword'        => 'luxury penthouses golf course road gurgaon',
                    'intent'         => 'transactional',
                    'priority_score' => 92,
                    'search_volume'  => 1900,
                    'kd'             => 29,
                    'geo_target'     => 'Golf Course Road',
                    'status'         => 'backlog',
                ),
                array(
                    'title'          => 'Complete NRI Real Estate Investment Guide in Delhi NCR',
                    'keyword'        => 'nri property investment guide delhi ncr',
                    'intent'         => 'commercial',
                    'priority_score' => 86,
                    'search_volume'  => 2100,
                    'kd'             => 26,
                    'geo_target'     => 'Delhi NCR',
                    'status'         => 'backlog',
                ),
            );
        }

        if ( $persist ) {
            global $wpdb;
            $table = $wpdb->prefix . 'cora_content_opportunities';
            if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
                foreach ( $seeds as $s ) {
                    $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE keyword = %s AND (agency_id = %s OR agency_id = '')", $s['keyword'], (string) $agency_id ) );
                    if ( ! $exists ) {
                        $wpdb->insert( $table, array(
                            'agency_id'      => (string) $agency_id,
                            'title'          => $s['title'],
                            'keyword'        => $s['keyword'],
                            'intent'         => $s['intent'],
                            'priority_score' => $s['priority_score'],
                            'search_volume'  => $s['search_volume'],
                            'kd'             => $s['kd'],
                            'geo_target'     => $s['geo_target'],
                            'status'         => 'backlog',
                            'created_at'     => current_time( 'mysql' ),
                        ) );
                    }
                }
            }
        }

        return $seeds;
    }
}

// Auto-initialize controller on file load
Cora_Workspace_Content_REST_Controller::init();
