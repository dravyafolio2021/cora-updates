<?php
/**
 * Cora Universal MCP - Standards-Compliant Protocol Engine (2024-11-05)
 *
 * Implements JSON-RPC 2.0 and MCP specification for any MCP client
 * (ChatGPT, Claude, Gemini, Cursor, VS Code, Windsurf, custom agents).
 *
 * @package CoraWorkspace
 * @subpackage MCP
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Universal_MCP_Server {

    const PROTOCOL_VERSION = '2024-11-05';
    const SERVER_NAME      = 'cora-universal-mcp';
    const SERVER_VERSION   = '4.0.0';
    const RATE_LIMIT_RPM   = 120; // 120 requests per minute

    /**
     * Dispatch an incoming REST request to the MCP Engine
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function handle_request( $request ) {
        // 1. Authenticate Token
        $auth = Cora_OAuth_Server::authenticate_token( $request );

        if ( ! $auth['valid'] ) {
            return new WP_REST_Response( array(
                'jsonrpc' => '2.0',
                'error'   => array(
                    'code'    => -32001,
                    'message' => 'Unauthorized: ' . ( $auth['error'] ?? 'Missing or invalid Bearer token.' ),
                ),
                'id'      => null,
            ), 401 );
        }

        // 2. Enforce Rate Limiting
        if ( ! self::check_rate_limit( $auth ) ) {
            return new WP_REST_Response( array(
                'jsonrpc' => '2.0',
                'error'   => array(
                    'code'    => -32000,
                    'message' => 'Rate limit exceeded. Max 120 requests per minute allowed.',
                ),
                'id'      => null,
            ), 429 );
        }

        // 3. Handle GET / SSE Streaming Transport
        if ( $request->get_method() === 'GET' ) {
            return self::handle_sse_stream( $request, $auth );
        }

        // 4. Handle POST / JSON-RPC Request
        $body = $request->get_json_params();
        if ( empty( $body ) || ! is_array( $body ) ) {
            $raw_body = $request->get_body();
            $body = json_decode( $raw_body, true );
        }

        if ( empty( $body ) || ! is_array( $body ) ) {
            return self::error_response( -32700, 'Parse error: Invalid JSON payload.', null, 400 );
        }

        // Handle Batch requests or single request
        if ( isset( $body[0] ) && is_array( $body[0] ) ) {
            $batch_responses = array();
            foreach ( $body as $single_req ) {
                $res = self::process_jsonrpc_message( $single_req, $auth, $request );
                if ( $res !== null ) {
                    $batch_responses[] = ( $res instanceof WP_REST_Response ) ? $res->get_data() : $res;
                }
            }
            return new WP_REST_Response( $batch_responses, 200 );
        }

        $response = self::process_jsonrpc_message( $body, $auth, $request );
        if ( $response === null ) {
            // Notification response (no reply needed)
            return new WP_REST_Response( null, 204 );
        }

        return $response;
    }

    /**
     * Process a single JSON-RPC 2.0 message
     */
    private static function process_jsonrpc_message( $message, $auth, $request ) {
        $id     = isset( $message['id'] ) ? $message['id'] : null;
        $method = isset( $message['method'] ) ? sanitize_text_field( $message['method'] ) : '';
        $params = isset( $message['params'] ) && is_array( $message['params'] ) ? $message['params'] : array();

        if ( empty( $method ) ) {
            return self::error_response( -32600, 'Invalid Request: Missing method.', $id, 400 );
        }

        switch ( $method ) {
            // ── Protocol Negotiation & Lifecycle ─────────────────────────────
            case 'initialize':
                return self::handle_initialize( $params, $id );

            case 'notifications/initialized':
            case 'initialized':
                // Acknowledged client notification
                return null;

            case 'ping':
                return self::success_response( (object) array(), $id );

            // ── Tool Discovery & Execution ───────────────────────────────────
            case 'tools/list':
                return self::handle_list_tools( $params, $id, $auth );

            case 'tools/call':
                return self::handle_call_tool( $params, $id, $auth );

            // ── Resources & Prompts (Standard Fallbacks) ─────────────────────
            case 'resources/list':
                return self::success_response( array( 'resources' => array() ), $id );

            case 'prompts/list':
                return self::success_response( array( 'prompts' => array() ), $id );

            default:
                return self::error_response( -32601, "Method not found: {$method}", $id, 404 );
        }
    }

    /**
     * Handle initialize method (MCP handshake)
     */
    private static function handle_initialize( $params, $id ) {
        $client_proto = isset( $params['protocolVersion'] ) ? sanitize_text_field( $params['protocolVersion'] ) : self::PROTOCOL_VERSION;

        return self::success_response( array(
            'protocolVersion' => self::PROTOCOL_VERSION,
            'capabilities'    => array(
                'tools'     => array( 'listChanged' => false ),
                'logging'   => (object) array(),
                'resources' => (object) array(),
                'prompts'   => (object) array(),
            ),
            'serverInfo'      => array(
                'name'        => self::SERVER_NAME,
                'version'     => self::SERVER_VERSION,
                'title'       => 'Cora Universal MCP Server',
                'description' => 'Standards-compliant remote MCP server for autonomous workspace operations and growth publishing.',
            ),
            'instructions'    => 'You are connected to Cora Studio OS via the Universal Model Context Protocol (MCP). Use the provided tools to inspect and operate the user\'s workspace, manage CRM leads, execute projects, track tasks, query financial ledgers, and author/publish content.',
        ), $id );
    }

    /**
     * Handle tools/list method
     */
    private static function handle_list_tools( $params, $id, $auth ) {
        $all_tools = Cora_MCP_Tool_Registry::get_tools();
        $formatted_tools = array();

        foreach ( $all_tools as $tool_key => $tool ) {
            // Check if token has scope for this tool
            $has_scope = Cora_OAuth_Server::has_scope( $auth, $tool['requiredScope'] );
            if ( ! $has_scope ) {
                continue;
            }

            $formatted_tools[] = array(
                'name'          => $tool['name'],
                'description'   => $tool['description'],
                'inputSchema'   => $tool['inputSchema'],
                'readOnly'      => ! empty( $tool['readOnly'] ),
                'destructive'   => ! empty( $tool['destructive'] ),
                'openWorld'     => ! empty( $tool['openWorld'] ),
                'requiredScope' => $tool['requiredScope'],
            );
        }

        return self::success_response( array(
            'tools' => $formatted_tools,
        ), $id );
    }

    /**
     * Handle tools/call method
     */
    private static function handle_call_tool( $params, $id, $auth ) {
        $tool_name = isset( $params['name'] ) ? sanitize_text_field( $params['name'] ) : '';
        $arguments = isset( $params['arguments'] ) && is_array( $params['arguments'] ) ? $params['arguments'] : array();

        if ( empty( $tool_name ) ) {
            return self::error_response( -32602, 'Invalid params: Missing tool name.', $id, 400 );
        }

        $tool = Cora_MCP_Tool_Registry::get_tool( $tool_name );
        if ( ! $tool ) {
            return self::error_response( -32601, "Tool not found: {$tool_name}", $id, 404 );
        }

        // 1. Verify Scope
        if ( ! Cora_OAuth_Server::has_scope( $auth, $tool['requiredScope'] ) ) {
            self::log_audit( $auth, $tool_name, $tool, 'forbidden', 'Insufficient scope: requires ' . $tool['requiredScope'], $arguments, 0 );
            return self::error_response( -32003, "Forbidden: This tool requires the '{$tool['requiredScope']}' scope.", $id, 403 );
        }

        // 2. Strict Multi-Tenant Isolation
        // Never trust client-provided workspace_id over authenticated workspace
        if ( isset( $arguments['workspace_id'] ) && (string) $arguments['workspace_id'] !== (string) $auth['workspace_id'] ) {
            // Override with authenticated workspace
            $arguments['workspace_id'] = $auth['workspace_id'];
        }

        $start_time = microtime( true );
        $action_type = ! empty( $tool['destructive'] ) ? 'destructive' : ( empty( $tool['readOnly'] ) ? 'write' : 'read' );

        try {
            $handler = $tool['handler'];
            $canonical_name = Cora_MCP_Tool_Registry::resolve_tool_name( $tool_name );

            if ( is_callable( $handler ) ) {
                $result = call_user_func( $handler, $arguments, $auth, $canonical_name );
            } else {
                throw new Exception( "Tool handler for {$canonical_name} is uncallable." );
            }

            $exec_time = intval( ( microtime( true ) - $start_time ) * 1000 );

            if ( is_wp_error( $result ) ) {
                self::log_audit( $auth, $tool_name, $tool, 'error', $result->get_error_message(), $arguments, $exec_time );
                return self::success_response( array(
                    'content' => array(
                        array(
                            'type' => 'text',
                            'text' => 'Error: ' . $result->get_error_message(),
                        ),
                    ),
                    'isError' => true,
                ), $id );
            }

            // Success Tool Execution
            self::log_audit( $auth, $tool_name, $tool, 'success', null, $arguments, $exec_time );

            $text_content = is_string( $result ) ? $result : wp_json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

            return self::success_response( array(
                'content' => array(
                    array(
                        'type' => 'text',
                        'text' => $text_content,
                    ),
                ),
                'isError' => false,
            ), $id );

        } catch ( Exception $e ) {
            $exec_time = intval( ( microtime( true ) - $start_time ) * 1000 );
            self::log_audit( $auth, $tool_name, $tool, 'error', $e->getMessage(), $arguments, $exec_time );

            return self::success_response( array(
                'content' => array(
                    array(
                        'type' => 'text',
                        'text' => 'Execution Exception: ' . $e->getMessage(),
                    ),
                ),
                'isError' => true,
            ), $id );
        }
    }

    /**
     * Handle Server-Sent Events (SSE) Stream
     */
    private static function handle_sse_stream( $request, $auth ) {
        header( 'Content-Type: text/event-stream' );
        header( 'Cache-Control: no-cache' );
        header( 'Connection: keep-alive' );
        header( 'X-Accel-Buffering: no' );

        $session_id = bin2hex( wp_generate_password( 16, false ) );
        $post_url = home_url( '/mcp?session_id=' . $session_id );

        echo "event: endpoint\n";
        echo "data: " . esc_url_raw( $post_url ) . "\n\n";
        if ( ob_get_level() > 0 ) ob_flush();
        flush();

        $start_time = time();
        while ( time() - $start_time < 35 ) {
            if ( connection_aborted() ) {
                break;
            }

            $sse_message = get_transient( 'cora_mcp_sse_' . $session_id );
            if ( $sse_message ) {
                delete_transient( 'cora_mcp_sse_' . $session_id );
                echo "event: message\n";
                echo "data: " . wp_json_encode( $sse_message ) . "\n\n";
                if ( ob_get_level() > 0 ) ob_flush();
                flush();
            }

            echo ": heartbeat\n\n";
            if ( ob_get_level() > 0 ) ob_flush();
            flush();
            sleep( 1 );
        }
        exit;
    }

    /**
     * Rate limit checker (120 RPM per token/workspace)
     */
    private static function check_rate_limit( $auth ) {
        $key = 'cora_mcp_rl_' . ( $auth['token_id'] ?: ( $auth['workspace_id'] . '_' . $auth['user_id'] ) );
        $count = intval( get_transient( $key ) ?: 0 );

        if ( $count >= self::RATE_LIMIT_RPM ) {
            return false;
        }

        if ( $count === 0 ) {
            set_transient( $key, 1, 60 );
        } else {
            set_transient( $key, $count + 1, 60 );
        }

        return true;
    }

    /**
     * Log tool call to MCP audit log
     */
    private static function log_audit( $auth, $tool_name, $tool, $status, $error = null, $arguments = array(), $exec_time = 0 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'cora_mcp_audit_log';
        if ( ! function_exists( 'cora_table_exists' ) || ! cora_table_exists( $table ) ) {
            return;
        }

        $action_type = ! empty( $tool['destructive'] ) ? 'destructive' : ( empty( $tool['readOnly'] ) ? 'write' : 'read' );
        $ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' );

        $target_id = null;
        if ( isset( $arguments['id'] ) ) $target_id = (string) $arguments['id'];
        elseif ( isset( $arguments['client_id'] ) ) $target_id = (string) $arguments['client_id'];
        elseif ( isset( $arguments['lead_id'] ) ) $target_id = (string) $arguments['lead_id'];
        elseif ( isset( $arguments['task_id'] ) ) $target_id = (string) $arguments['task_id'];
        elseif ( isset( $arguments['project_id'] ) ) $target_id = (string) $arguments['project_id'];

        $wpdb->insert( $table, array(
            'user_id'            => $auth['user_id'],
            'workspace_id'       => (string) $auth['workspace_id'],
            'client_name'        => $auth['client_name'] ?? 'unknown',
            'token_id'           => $auth['token_id'] ?: null,
            'tool_name'          => $tool_name,
            'action_type'        => $action_type,
            'target_id'          => $target_id,
            'request_payload'    => wp_json_encode( $arguments ),
            'result_status'      => $status,
            'error_message'      => $error,
            'ip_address'         => $ip,
            'execution_time_ms'  => $exec_time,
            'created_at'         => current_time( 'mysql' ),
        ) );
    }

    /**
     * Helper to return standard JSON-RPC success response
     */
    private static function success_response( $result, $id ) {
        return new WP_REST_Response( array(
            'jsonrpc' => '2.0',
            'result'  => $result,
            'id'      => $id,
        ), 200 );
    }

    /**
     * Helper to return standard JSON-RPC error response
     */
    private static function error_response( $code, $message, $id = null, $http_status = 400 ) {
        return new WP_REST_Response( array(
            'jsonrpc' => '2.0',
            'error'   => array(
                'code'    => $code,
                'message' => $message,
            ),
            'id'      => $id,
        ), $http_status );
    }
}
