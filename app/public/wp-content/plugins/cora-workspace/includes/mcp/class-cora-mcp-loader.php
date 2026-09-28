<?php
/**
 * Cora Universal MCP - Loader & REST Route Orchestrator
 *
 * Boots MCP DB, OAuth 2.1 Server, Tool Registry, and REST API Endpoints.
 *
 * @package CoraWorkspace
 * @subpackage MCP
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_MCP_Loader {

    public static function init() {
        require_once __DIR__ . '/class-cora-mcp-db.php';
        require_once __DIR__ . '/class-cora-oauth-server.php';
        require_once __DIR__ . '/class-cora-mcp-tool-registry.php';
        require_once __DIR__ . '/class-cora-universal-mcp-server.php';

        Cora_MCP_DB::init();

        add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );
        add_action( 'init', array( __CLASS__, 'add_rewrite_rules' ) );
        add_filter( 'query_vars', array( __CLASS__, 'add_query_vars' ) );
        add_action( 'wp_loaded', array( __CLASS__, 'handle_pretty_endpoints' ), 20 );
        add_action( 'template_redirect', array( __CLASS__, 'handle_pretty_endpoints' ), 5 );
        add_action( 'wp_ajax_cora_get_active_mcp_connections', array( __CLASS__, 'ajax_get_connections' ) );
        add_action( 'wp_ajax_cora_revoke_mcp_connection', array( __CLASS__, 'ajax_revoke_connection' ) );
    }

    /**
     * Register REST API routes
     */
    public static function register_rest_routes() {
        $mcp_args = array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( 'Cora_Universal_MCP_Server', 'handle_request' ),
            'permission_callback' => '__return_true', // Auth handled internally via Bearer token
        );

        // 1. Universal MCP endpoints
        register_rest_route( 'cora/v1', '/mcp', $mcp_args );
        register_rest_route( 'cora-mcp/v1', '/endpoint', $mcp_args );

        // 2. OAuth 2.1 endpoints
        register_rest_route( 'cora/v1', '/oauth/authorize', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( 'Cora_OAuth_Server', 'handle_authorize' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'cora/v1', '/oauth/token', array(
            'methods'             => 'POST',
            'callback'            => array( 'Cora_OAuth_Server', 'handle_token' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'cora/v1', '/oauth/revoke', array(
            'methods'             => 'POST',
            'callback'            => array( 'Cora_OAuth_Server', 'handle_revoke' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'cora/v1', '/oauth/userinfo', array(
            'methods'             => 'GET',
            'callback'            => array( 'Cora_OAuth_Server', 'handle_userinfo' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'cora/v1', '/oauth/protected-resource', array(
            'methods'             => 'GET',
            'callback'            => function() {
                return new WP_REST_Response( Cora_OAuth_Server::get_protected_resource_metadata(), 200 );
            },
            'permission_callback' => '__return_true',
        ) );

        // 3. In-App Connections Management (Workspace UI)
        register_rest_route( 'cora/v1', '/mcp/connections', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'handle_get_connections' ),
            'permission_callback' => array( __CLASS__, 'check_ui_permission' ),
        ) );

        register_rest_route( 'cora/v1', '/mcp/connections/revoke', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'handle_revoke_connection' ),
            'permission_callback' => array( __CLASS__, 'check_ui_permission' ),
        ) );
    }

    /**
     * Add rewrite rules for pretty URLs
     */
    public static function add_rewrite_rules() {
        add_rewrite_rule( '^mcp/?$', 'index.php?cora_mcp_endpoint=1', 'top' );
        add_rewrite_rule( '^mcp/openapi\.json/?$', 'index.php?cora_openapi_spec=1', 'top' );
        add_rewrite_rule( '^oauth/authorize/?$', 'index.php?cora_oauth_authorize=1', 'top' );
        add_rewrite_rule( '^oauth/token/?$', 'index.php?cora_oauth_token=1', 'top' );
        add_rewrite_rule( '^oauth/revoke/?$', 'index.php?cora_oauth_revoke=1', 'top' );
        add_rewrite_rule( '^\.well-known/oauth-authorization-server/?$', 'index.php?cora_oauth_discovery=1', 'top' );
        add_rewrite_rule( '^\.well-known/openid-configuration/?$', 'index.php?cora_oauth_discovery=1', 'top' );
        add_rewrite_rule( '^\.well-known/oauth-protected-resource/?$', 'index.php?cora_oauth_protected_resource=1', 'top' );
        add_rewrite_rule( '^\.well-known/ai-plugin\.json/?$', 'index.php?cora_ai_plugin_manifest=1', 'top' );
        add_rewrite_rule( '^\.well-known/openapi\.json/?$', 'index.php?cora_openapi_spec=1', 'top' );
    }

    /**
     * Register query vars
     */
    public static function add_query_vars( $vars ) {
        $vars[] = 'cora_mcp_endpoint';
        $vars[] = 'cora_openapi_spec';
        $vars[] = 'cora_ai_plugin_manifest';
        $vars[] = 'cora_oauth_authorize';
        $vars[] = 'cora_oauth_token';
        $vars[] = 'cora_oauth_revoke';
        $vars[] = 'cora_oauth_discovery';
        $vars[] = 'cora_oauth_protected_resource';
        return $vars;
    }

    /**
     * Create a fully-populated WP_REST_Request from current PHP globals
     */
    public static function create_request_from_globals( $method, $route ) {
        $request = new WP_REST_Request( $method, $route );
        $request->set_query_params( $_GET );

        $raw_body = file_get_contents( 'php://input' );
        if ( ! empty( $raw_body ) ) {
            $request->set_body( $raw_body );
        }

        if ( ! empty( $_POST ) ) {
            $request->set_body_params( $_POST );
        }

        // Copy HTTP headers
        if ( function_exists( 'getallheaders' ) ) {
            $headers = getallheaders();
            if ( is_array( $headers ) ) {
                foreach ( $headers as $k => $v ) {
                    $request->set_header( strtolower( $k ), $v );
                }
            }
        }

        // Ensure authorization header is populated
        if ( ! $request->get_header( 'authorization' ) ) {
            if ( isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
                $request->set_header( 'authorization', $_SERVER['HTTP_AUTHORIZATION'] );
            } elseif ( isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
                $request->set_header( 'authorization', $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] );
            }
        }

        return $request;
    }

    /**
     * Handle pretty endpoints on template redirect or early init
     */
    public static function handle_pretty_endpoints() {
        $request_uri = parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ?: '';
        $home_path   = parse_url( home_url(), PHP_URL_PATH ) ?: '';
        if ( ! empty( $home_path ) && 0 === strpos( $request_uri, $home_path ) ) {
            $path = substr( $request_uri, strlen( $home_path ) );
        } else {
            $path = $request_uri;
        }
        $path = trim( $path, '/' );
        $path_parts = array_values( array_filter( explode( '/', $path ) ) );
        $first_seg  = $path_parts[0] ?? '';
        $second_seg = $path_parts[1] ?? '';

        $is_mcp = get_query_var( 'cora_mcp_endpoint' ) || ( $first_seg === 'mcp' && empty( $second_seg ) );
        $is_openapi = get_query_var( 'cora_openapi_spec' ) || ( $first_seg === 'mcp' && $second_seg === 'openapi.json' ) || ( $first_seg === '.well-known' && $second_seg === 'openapi.json' );
        $is_ai_plugin = get_query_var( 'cora_ai_plugin_manifest' ) || ( $first_seg === '.well-known' && $second_seg === 'ai-plugin.json' );
        $is_oauth_auth = get_query_var( 'cora_oauth_authorize' ) || ( $first_seg === 'oauth' && $second_seg === 'authorize' );
        $is_oauth_token = get_query_var( 'cora_oauth_token' ) || ( $first_seg === 'oauth' && $second_seg === 'token' );
        $is_oauth_revoke = get_query_var( 'cora_oauth_revoke' ) || ( $first_seg === 'oauth' && $second_seg === 'revoke' );
        $is_oauth_userinfo = ( $first_seg === 'oauth' && $second_seg === 'userinfo' );
        $is_oauth_discovery = get_query_var( 'cora_oauth_discovery' ) || ( $first_seg === '.well-known' && ( $second_seg === 'oauth-authorization-server' || $second_seg === 'openid-configuration' ) );
        $is_oauth_protected_resource = get_query_var( 'cora_oauth_protected_resource' ) || ( $first_seg === '.well-known' && $second_seg === 'oauth-protected-resource' );

        if ( $is_mcp ) {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $request = self::create_request_from_globals( $method, '/cora/v1/mcp' );
            $response = Cora_Universal_MCP_Server::handle_request( $request );
            self::send_rest_response( $response );
            exit;
        }

        if ( $is_openapi ) {
            if ( function_exists( 'cora_rest_mcp_openapi_handler' ) ) {
                $response = cora_rest_mcp_openapi_handler();
                self::send_rest_response( $response );
            }
            exit;
        }

        if ( $is_ai_plugin ) {
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Access-Control-Allow-Origin: *' );
            header( 'Cache-Control: public, max-age=3600' );
            $base = home_url();
            $manifest = array(
                'schema_version'        => 'v1',
                'name_for_human'        => 'Cora Studio OS',
                'name_for_model'        => 'cora',
                'description_for_human' => 'Universal workspace operations, CRM pipeline, tasks, bookings, ledger, and autonomous publishing.',
                'description_for_model' => 'Cora Studio OS provides universal MCP tools and REST actions to query workspace metrics, manage CRM leads, clients, tasks, projects, bookings, invoices, and author/publish content.',
                'auth'                  => array(
                    'type'                       => 'oauth',
                    'client_url'                 => $base . '/oauth/authorize',
                    'scope'                      => implode( ' ', array_keys( Cora_OAuth_Server::get_supported_scopes() ) ),
                    'authorization_url'          => $base . '/oauth/token',
                    'authorization_content_type' => 'application/x-www-form-urlencoded',
                ),
                'api'                   => array(
                    'type'                  => 'openapi',
                    'url'                   => $base . '/mcp/openapi.json',
                    'is_user_authenticated' => false,
                ),
                'logo_url'              => $base . '/wp-content/plugins/cora-workspace/assets/icons/cora-logo.svg',
                'contact_email'         => 'support@heycora.in',
                'legal_info_url'        => 'https://heycora.in/terms',
            );
            echo wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
            exit;
        }

        if ( $is_oauth_auth ) {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $request = self::create_request_from_globals( $method, '/cora/v1/oauth/authorize' );
            $response = Cora_OAuth_Server::handle_authorize( $request );
            if ( $response instanceof WP_REST_Response ) {
                self::send_rest_response( $response );
            }
            exit;
        }

        if ( $is_oauth_token ) {
            $request = self::create_request_from_globals( 'POST', '/cora/v1/oauth/token' );
            $response = Cora_OAuth_Server::handle_token( $request );
            self::send_rest_response( $response );
            exit;
        }

        if ( $is_oauth_revoke ) {
            $request = self::create_request_from_globals( 'POST', '/cora/v1/oauth/revoke' );
            $response = Cora_OAuth_Server::handle_revoke( $request );
            self::send_rest_response( $response );
            exit;
        }

        if ( $is_oauth_userinfo ) {
            $request = self::create_request_from_globals( 'GET', '/cora/v1/oauth/userinfo' );
            $response = Cora_OAuth_Server::handle_userinfo( $request );
            self::send_rest_response( $response );
            exit;
        }

        if ( $is_oauth_protected_resource ) {
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: public, max-age=3600' );
            echo wp_json_encode( Cora_OAuth_Server::get_protected_resource_metadata(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
            exit;
        }

        if ( $is_oauth_discovery ) {
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: public, max-age=3600' );
            echo wp_json_encode( Cora_OAuth_Server::get_oauth_metadata(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
            exit;
        }
    }

    /**
     * Send REST response helper
     */
    private static function send_rest_response( $response ) {
        if ( ! ( $response instanceof WP_REST_Response ) ) {
            return;
        }
        $status = $response->get_status();
        $data   = $response->get_data();

        status_header( $status );

        // Pass headers from response
        $headers = $response->get_headers();
        if ( ! empty( $headers ) ) {
            foreach ( $headers as $k => $v ) {
                header( "{$k}: {$v}" );
            }
        }

        if ( $status === 401 && empty( $headers['WWW-Authenticate'] ) ) {
            $meta_url = home_url( '/.well-known/oauth-protected-resource' );
            header( 'WWW-Authenticate: Bearer resource_metadata="' . $meta_url . '", realm="cora-mcp", error="invalid_token", error_description="Missing or invalid bearer token"' );
        }

        if ( $status === 204 ) {
            exit;
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo wp_json_encode( $data, JSON_UNESCAPED_SLASHES );
        exit;
    }

    /**
     * Check UI permission for in-app management
     */
    public static function check_ui_permission() {
        return is_user_logged_in();
    }

    /**
     * Get active OAuth connections for the current user/workspace
     */
    public static function handle_get_connections( $request = null ) {
        if ( ! is_user_logged_in() ) {
            $cookie_user_id = wp_validate_auth_cookie( '', 'logged_in' );
            if ( $cookie_user_id ) {
                wp_set_current_user( $cookie_user_id );
            }
        }
        $user_id = get_current_user_id();
        global $wpdb;
        $table_tokens = $wpdb->prefix . 'cora_oauth_tokens';
        $connections = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table_tokens ) ) {
            $rows = $wpdb->get_results(
                "SELECT id, client_id, client_name, workspace_id, scopes, expires_at, last_used_at, created_at, revoked
                 FROM {$table_tokens}
                 WHERE revoked = 0
                 ORDER BY id DESC"
            );

            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $connections[] = array(
                        'id'           => intval( $r->id ),
                        'client_id'    => $r->client_id,
                        'client_name'  => $r->client_name,
                        'workspace_id' => $r->workspace_id,
                        'scopes'       => array_filter( array_map( 'trim', explode( ' ', $r->scopes ) ) ),
                        'expires_at'   => $r->expires_at,
                        'last_used_at' => $r->last_used_at,
                        'created_at'   => $r->created_at,
                    );
                }
            }
        }

        return new WP_REST_Response( array(
            'success'     => true,
            'connections' => $connections,
            'endpoint'    => home_url( '/mcp' ),
        ), 200 );
    }

    /**
     * Revoke a specific OAuth connection
     */
    public static function handle_revoke_connection( $request ) {
        if ( ! is_user_logged_in() ) {
            $cookie_user_id = wp_validate_auth_cookie( '', 'logged_in' );
            if ( $cookie_user_id ) {
                wp_set_current_user( $cookie_user_id );
            }
        }
        $user_id = get_current_user_id();
        $raw_body = $request instanceof WP_REST_Request ? $request->get_body() : file_get_contents( 'php://input' );
        $json_data = json_decode( $raw_body, true );
        $token_id = intval( ( $request instanceof WP_REST_Request ? $request->get_param( 'id' ) : null ) ?? ( $json_data['id'] ?? ( $_POST['id'] ?? 0 ) ) );

        if ( ! $token_id ) {
            return new WP_REST_Response( array( 'success' => false, 'error' => 'Missing connection ID.' ), 400 );
        }

        global $wpdb;
        $table_tokens = $wpdb->prefix . 'cora_oauth_tokens';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table_tokens ) ) {
            $wpdb->update( $table_tokens, array( 'revoked' => 1 ), array( 'id' => $token_id ) );
        }

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * AJAX handler for get connections
     */
    public static function ajax_get_connections() {
        if ( ! is_user_logged_in() ) {
            $cookie_user_id = wp_validate_auth_cookie( '', 'logged_in' );
            if ( $cookie_user_id ) {
                wp_set_current_user( $cookie_user_id );
            }
        }
        $res = self::handle_get_connections( null );
        wp_send_json( $res->get_data() );
    }

    /**
     * AJAX handler for revoke connection
     */
    public static function ajax_revoke_connection() {
        if ( ! is_user_logged_in() ) {
            $cookie_user_id = wp_validate_auth_cookie( '', 'logged_in' );
            if ( $cookie_user_id ) {
                wp_set_current_user( $cookie_user_id );
            }
        }
        $request = new WP_REST_Request( 'POST', '/cora/v1/mcp/connections/revoke' );
        $raw_body = file_get_contents( 'php://input' );
        if ( ! empty( $raw_body ) ) {
            $request->set_body( $raw_body );
        }
        if ( ! empty( $_POST ) ) {
            $request->set_body_params( $_POST );
        }
        $res = self::handle_revoke_connection( $request );
        wp_send_json( $res->get_data() );
    }
}

// Auto-initialize when loaded
Cora_MCP_Loader::init();

