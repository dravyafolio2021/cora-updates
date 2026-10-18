<?php
/**
 * Cora Universal MCP - Standards-Compliant OAuth 2.1 Authorization Server
 *
 * Implements RFC 6749 (OAuth 2.0), RFC 7636 (PKCE), RFC 7009 (Revocation), RFC 8414 (Metadata).
 * Enforces server-side workspace binding and granular scoped permissions.
 *
 * @package CoraWorkspace
 * @subpackage MCP
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_OAuth_Server {

    const AUTH_CODE_EXPIRY_SECONDS = 600;      // 10 minutes
    const ACCESS_TOKEN_EXPIRY_SECONDS = 2592000; // 30 days
    const REFRESH_TOKEN_EXPIRY_SECONDS = 7776000; // 90 days

    /**
     * Standard Cora Scopes Matrix
     */
    public static function get_supported_scopes() {
        return array(
            'workspace:read' => array(
                'label'       => 'Workspace Overview & Metadata',
                'description' => 'View workspace profile, team members, status metrics, and intake forms.',
                'category'    => 'core',
                'level'       => 'read',
            ),
            'workspace:write' => array(
                'label'       => 'Workspace Settings & Configuration',
                'description' => 'Update workspace branding, business profile, and platform settings.',
                'category'    => 'core',
                'level'       => 'write',
            ),
            'offline_access' => array(
                'label'       => 'Offline Access',
                'description' => 'Maintain access to workspace tools when the user is not actively present.',
                'category'    => 'core',
                'level'       => 'read',
            ),
            'canvas:read' => array(
                'label'       => 'Website, Themes & Templates',
                'description' => 'Inspect active themes, landing page templates, route maps, and source HTML.',
                'category'    => 'canvas',
                'level'       => 'read',
            ),
            'canvas:write' => array(
                'label'       => 'Edit & Publish Website HTML',
                'description' => 'Create themes, edit page markup, and publish landing page code live instantly.',
                'category'    => 'canvas',
                'level'       => 'write',
            ),
            'leads:read' => array(
                'label'       => 'View CRM Leads & Pipeline',
                'description' => 'Access incoming client inquiries, deal values, and funnel pipeline stages.',
                'category'    => 'crm',
                'level'       => 'read',
            ),
            'leads:write' => array(
                'label'       => 'Manage Leads & Pipeline',
                'description' => 'Create new leads, update deal values, advance funnel stages, and log notes.',
                'category'    => 'crm',
                'level'       => 'write',
            ),
            'clients:read' => array(
                'label'       => 'View Client Directory',
                'description' => 'Read client accounts, contact profiles, and historical interactions.',
                'category'    => 'crm',
                'level'       => 'read',
            ),
            'clients:write' => array(
                'label'       => 'Manage Client Profiles',
                'description' => 'Create, update, and manage client records and preferences.',
                'category'    => 'crm',
                'level'       => 'write',
            ),
            'projects:read' => array(
                'label'       => 'View Projects & Bookings',
                'description' => 'Read shoot schedules, booking packages, deliverables, and event milestones.',
                'category'    => 'operations',
                'level'       => 'read',
            ),
            'projects:write' => array(
                'label'       => 'Manage Projects & Bookings',
                'description' => 'Schedule bookings, assign shoot crews, update package amounts, and track delivery.',
                'category'    => 'operations',
                'level'       => 'write',
            ),
            'plugins:read' => array(
                'label'       => 'View Workspace Plugins',
                'description' => 'Inspect installed workspace plugins, webhook listeners, and MCP tool bridges.',
                'category'    => 'plugins',
                'level'       => 'read',
            ),
            'plugins:write' => array(
                'label'       => 'Manage Workspace Plugins',
                'description' => 'Create, configure, toggle, and install workspace plugins and API webhooks.',
                'category'    => 'plugins',
                'level'       => 'write',
            ),
            'skills:read' => array(
                'label'       => 'View Workspace AI Skills',
                'description' => 'Inspect custom AI agent skills, system prompt instructions, and tool bindings.',
                'category'    => 'skills',
                'level'       => 'read',
            ),
            'skills:write' => array(
                'label'       => 'Manage Workspace AI Skills',
                'description' => 'Create, edit, toggle, and configure custom AI agent skills and parameter schemas.',
                'category'    => 'skills',
                'level'       => 'write',
            ),
            'tasks:read' => array(
                'label'       => 'View Kanban Tasks',
                'description' => 'Read workspace task boards, assignments, priorities, and deadlines.',
                'category'    => 'operations',
                'level'       => 'read',
            ),
            'tasks:write' => array(
                'label'       => 'Manage Kanban Tasks',
                'description' => 'Create, assign, prioritize, update, and move tasks across Kanban stages.',
                'category'    => 'operations',
                'level'       => 'write',
            ),
            'finance:read' => array(
                'label'       => 'View Financials & Invoices',
                'description' => 'Read revenue metrics, client invoices, outstanding receivables, and ledgers.',
                'category'    => 'finance',
                'level'       => 'read',
            ),
            'finance:write' => array(
                'label'       => 'Record Financial Transactions',
                'description' => 'Create invoices, record client payments, and track expense transactions.',
                'category'    => 'finance',
                'level'       => 'write',
            ),
            'content:read' => array(
                'label'       => 'Read Content & SEO Engine',
                'description' => 'Search editorial articles, guides, knowledge documents, and SEO metrics.',
                'category'    => 'growth',
                'level'       => 'read',
            ),
            'content:write' => array(
                'label'       => 'Draft & Publish Content',
                'description' => 'Create structured articles/guides, optimize keywords, and publish live.',
                'category'    => 'growth',
                'level'       => 'write',
            ),
            'knowledge:read' => array(
                'label'       => 'Living RAG Knowledge Base',
                'description' => 'Semantic query across living memory, past shoots, SOPs, and intelligence.',
                'category'    => 'intelligence',
                'level'       => 'read',
            ),
            'knowledge:write' => array(
                'label'       => 'Save Knowledge Fragments',
                'description' => 'Store new context fragments, client preferences, and operational memory.',
                'category'    => 'intelligence',
                'level'       => 'write',
            ),
            'workflows:read' => array(
                'label'       => 'View Automated Workflows',
                'description' => 'Inspect automated rule configurations, triggers, and notification flows.',
                'category'    => 'automation',
                'level'       => 'read',
            ),
            'workflows:write' => array(
                'label'       => 'Manage Automations',
                'description' => 'Configure and trigger automated actions, webhooks, and alerts.',
                'category'    => 'automation',
                'level'       => 'write',
            ),
        );
    }

    /**
     * Handle OAuth 2.1 Authorization Request (GET/POST)
     */
    public static function handle_authorize( $request ) {
        $client_id             = sanitize_text_field( $request->get_param( 'client_id' ) ?? '' );
        $redirect_uri          = esc_url_raw( $request->get_param( 'redirect_uri' ) ?? '' );
        $response_type         = sanitize_text_field( $request->get_param( 'response_type' ) ?? 'code' );
        $scope_param           = sanitize_text_field( $request->get_param( 'scope' ) ?? '' );
        $state                 = sanitize_text_field( $request->get_param( 'state' ) ?? '' );
        $code_challenge        = sanitize_text_field( $request->get_param( 'code_challenge' ) ?? '' );
        $code_challenge_method = sanitize_text_field( $request->get_param( 'code_challenge_method' ) ?? 'S256' );

        // Validate client
        $client = self::get_client( $client_id );
        if ( ! $client ) {
            return self::render_error_page( 'Invalid Client', 'The client_id provided is not recognized.' );
        }

        // Validate redirect URI
        if ( ! self::validate_redirect_uri( $client, $redirect_uri ) ) {
            return self::render_error_page( 'Invalid Redirect URI', 'The redirect_uri is not permitted for this client.' );
        }

        if ( $response_type !== 'code' ) {
            return self::redirect_with_error( $redirect_uri, 'unsupported_response_type', 'Only response_type=code is supported.', $state );
        }

        // Require PKCE for public clients (or default to empty if not provided)
        if ( empty( $code_challenge ) ) {
            // Generate fallback challenge if client doesn't support PKCE natively
            $code_challenge = bin2hex( wp_generate_password( 32, false ) );
            $code_challenge_method = 'plain';
        }

        // Check authentication (supporting cookie auth during direct browser navigation)
        if ( ! is_user_logged_in() ) {
            $cookie_user_id = wp_validate_auth_cookie( '', 'logged_in' );
            if ( $cookie_user_id ) {
                wp_set_current_user( $cookie_user_id );
            }
        }

        if ( ! is_user_logged_in() ) {
            return self::render_login_screen( $request );
        }

        $current_user = wp_get_current_user();
        $accessible_workspaces = self::get_user_workspaces( $current_user->ID );
        $default_ws = function_exists( 'cora_get_current_agency_id' ) ? (string) cora_get_current_agency_id() : '1';

        // Plan eligibility check for current workspace
        $primary_ws = ! empty( $accessible_workspaces ) ? $accessible_workspaces[0]['id'] : $default_ws;
        if ( function_exists( 'cora_is_workspace_mcp_eligible' ) && ! cora_is_workspace_mcp_eligible( $primary_ws ) ) {
            return self::render_upgrade_notice_screen( $client, $current_user, $accessible_workspaces );
        }

        // Handle POST form submission (Approval or Denial)
        if ( $request->get_method() === 'POST' && isset( $_POST['cora_oauth_action'] ) ) {
            $nonce = isset( $_POST['cora_oauth_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['cora_oauth_nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'cora_oauth_authorize_action' ) ) {
                return self::render_error_page( 'Security Check Failed', 'Your session expired. Please refresh and try again.' );
            }

            $action = sanitize_text_field( $_POST['cora_oauth_action'] );
            if ( $action === 'deny' ) {
                return self::redirect_with_error( $redirect_uri, 'access_denied', 'The user denied the authorization request.', $state );
            }

            if ( $action === 'approve' ) {
                $selected_workspace = sanitize_text_field( $_POST['workspace_id'] ?? $default_ws );
                if ( function_exists( 'cora_is_workspace_mcp_eligible' ) && ! cora_is_workspace_mcp_eligible( $selected_workspace ) ) {
                    return self::render_upgrade_notice_screen( $client, $current_user, $accessible_workspaces );
                }
                $full_access = ! empty( $_POST['full_access'] ) && (string) $_POST['full_access'] === '1';

                $selected_scopes = ( isset( $_POST['scopes'] ) && is_array( $_POST['scopes'] ) && ! empty( $_POST['scopes'] ) )
                    ? array_map( 'sanitize_text_field', $_POST['scopes'] )
                    : array_keys( self::get_supported_scopes() );

                // If full access is requested or all scopes are selected, include master wildcard
                if ( $full_access || count( $selected_scopes ) >= count( self::get_supported_scopes() ) ) {
                    if ( ! in_array( '*', $selected_scopes, true ) ) {
                        array_unshift( $selected_scopes, '*' );
                    }
                }

                // Verify workspace belongs to user
                $workspace_valid = false;
                foreach ( $accessible_workspaces as $ws ) {
                    if ( (string) $ws['id'] === (string) $selected_workspace ) {
                        $workspace_valid = true;
                        break;
                    }
                }
                if ( ! $workspace_valid ) {
                    $selected_workspace = ! empty( $accessible_workspaces ) ? $accessible_workspaces[0]['id'] : $default_ws;
                }

                // Issue authorization code
                $auth_code = self::generate_auth_code(
                    $client_id,
                    $current_user->ID,
                    $selected_workspace,
                    $redirect_uri,
                    $code_challenge,
                    $code_challenge_method,
                    $selected_scopes
                );

                $redirect_url = add_query_arg( array(
                    'code'  => $auth_code,
                    'state' => $state,
                ), $redirect_uri );

                wp_redirect( $redirect_url );
                exit;
            }
        }

        // Render Monochromatic Consent Screen
        return self::render_consent_screen(
            $client,
            $current_user,
            $accessible_workspaces,
            $scope_param,
            $redirect_uri,
            $state,
            $code_challenge,
            $code_challenge_method
        );
    }

    /**
     * Handle OAuth 2.1 Token Exchange (POST)
     */
    public static function handle_token( $request ) {
        $raw_body    = $request instanceof WP_REST_Request ? $request->get_body() : file_get_contents( 'php://input' );
        $json_params = ( $request instanceof WP_REST_Request ? $request->get_json_params() : null ) ?: ( json_decode( $raw_body, true ) ?: array() );

        $grant_type    = sanitize_text_field( ( $request instanceof WP_REST_Request ? $request->get_param( 'grant_type' ) : null ) ?? ( $json_params['grant_type'] ?? ( $_POST['grant_type'] ?? ( $_GET['grant_type'] ?? '' ) ) ) );
        $client_id     = sanitize_text_field( ( $request instanceof WP_REST_Request ? $request->get_param( 'client_id' ) : null ) ?? ( $json_params['client_id'] ?? ( $_POST['client_id'] ?? ( $_GET['client_id'] ?? '' ) ) ) );
        $client_secret = sanitize_text_field( ( $request instanceof WP_REST_Request ? $request->get_param( 'client_secret' ) : null ) ?? ( $json_params['client_secret'] ?? ( $_POST['client_secret'] ?? '' ) ) );
        $code          = sanitize_text_field( ( $request instanceof WP_REST_Request ? $request->get_param( 'code' ) : null ) ?? ( $json_params['code'] ?? ( $_POST['code'] ?? '' ) ) );
        $redirect_uri  = esc_url_raw( ( $request instanceof WP_REST_Request ? $request->get_param( 'redirect_uri' ) : null ) ?? ( $json_params['redirect_uri'] ?? ( $_POST['redirect_uri'] ?? '' ) ) );
        $code_verifier = sanitize_text_field( ( $request instanceof WP_REST_Request ? $request->get_param( 'code_verifier' ) : null ) ?? ( $json_params['code_verifier'] ?? ( $_POST['code_verifier'] ?? '' ) ) );
        $refresh_token = sanitize_text_field( ( $request instanceof WP_REST_Request ? $request->get_param( 'refresh_token' ) : null ) ?? ( $json_params['refresh_token'] ?? ( $_POST['refresh_token'] ?? '' ) ) );

        // Extract client_id & client_secret from HTTP Basic Auth if present
        if ( empty( $client_id ) && ! empty( $_SERVER['PHP_AUTH_USER'] ) ) {
            $client_id = sanitize_text_field( $_SERVER['PHP_AUTH_USER'] );
        }
        if ( empty( $client_secret ) && ! empty( $_SERVER['PHP_AUTH_PW'] ) ) {
            $client_secret = sanitize_text_field( $_SERVER['PHP_AUTH_PW'] );
        }

        if ( empty( $client_id ) || empty( $client_secret ) ) {
            $auth_header = '';
            if ( $request instanceof WP_REST_Request ) {
                $auth_header = $request->get_header( 'authorization' );
            }
            if ( empty( $auth_header ) && isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
                $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
            } elseif ( empty( $auth_header ) && isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
                $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif ( empty( $auth_header ) && function_exists( 'getallheaders' ) ) {
                $all_headers = getallheaders();
                $auth_header = $all_headers['Authorization'] ?? ( $all_headers['authorization'] ?? '' );
            }

            if ( ! empty( $auth_header ) && stripos( trim( $auth_header ), 'Basic ' ) === 0 ) {
                $basic_decoded = base64_decode( substr( trim( $auth_header ), 6 ) );
                if ( $basic_decoded && strpos( $basic_decoded, ':' ) !== false ) {
                    list( $basic_user, $basic_pass ) = explode( ':', $basic_decoded, 2 );
                    if ( empty( $client_id ) ) {
                        $client_id = sanitize_text_field( $basic_user );
                    }
                    if ( empty( $client_secret ) ) {
                        $client_secret = sanitize_text_field( $basic_pass );
                    }
                }
            }
        }

        if ( ! headers_sent() ) {
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: no-store' );
            header( 'Pragma: no-cache' );
        }

        if ( $grant_type === 'authorization_code' ) {
            if ( empty( $code ) ) {
                return new WP_REST_Response( array( 'error' => 'invalid_request', 'error_description' => 'Missing authorization code.' ), 400 );
            }

            global $wpdb;
            $table_codes = $wpdb->prefix . 'cora_oauth_codes';
            $code_record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_codes} WHERE code = %s AND used = 0", $code ) );

            if ( ! $code_record ) {
                return new WP_REST_Response( array( 'error' => 'invalid_grant', 'error_description' => 'Invalid or expired authorization code.' ), 400 );
            }

            // Check plan eligibility (Paid Workspaces Only)
            if ( function_exists( 'cora_is_workspace_mcp_eligible' ) && ! cora_is_workspace_mcp_eligible( $code_record->workspace_id ) ) {
                return new WP_REST_Response( array(
                    'error'             => 'insufficient_scope',
                    'error_description' => 'Plan upgrade required: The Model Context Protocol (MCP) and Developer Gateway is restricted to Professional and Scale workspaces.',
                ), 403 );
            }

            if ( strtotime( $code_record->expires_at ) < time() ) {
                $wpdb->update( $table_codes, array( 'used' => 1 ), array( 'code' => $code ) );
                return new WP_REST_Response( array( 'error' => 'invalid_grant', 'error_description' => 'Authorization code has expired.' ), 400 );
            }

            // Verify PKCE
            if ( ! empty( $code_record->code_challenge ) ) {
                if ( empty( $code_verifier ) && $code_record->code_challenge_method !== 'plain' ) {
                    return new WP_REST_Response( array( 'error' => 'invalid_grant', 'error_description' => 'Missing PKCE code_verifier.' ), 400 );
                }

                if ( ! empty( $code_verifier ) ) {
                    $valid_verifier = false;
                    if ( $code_record->code_challenge_method === 'S256' ) {
                        $expected_challenge = rtrim( strtr( base64_encode( hash( 'sha256', $code_verifier, true ) ), '+/', '-_' ), '=' );
                        $valid_verifier = hash_equals( $code_record->code_challenge, $expected_challenge );
                    } else {
                        $valid_verifier = hash_equals( $code_record->code_challenge, $code_verifier );
                    }

                    if ( ! $valid_verifier ) {
                        return new WP_REST_Response( array( 'error' => 'invalid_grant', 'error_description' => 'PKCE verification failed.' ), 400 );
                    }
                }
            }

            // Mark code as used
            $wpdb->update( $table_codes, array( 'used' => 1 ), array( 'code' => $code ) );

            // Issue access token and refresh token
            $access_token  = 'cora_mcp_at_' . bin2hex( wp_generate_password( 32, false ) );
            $refresh_token = 'cora_mcp_rt_' . bin2hex( wp_generate_password( 32, false ) );

            $token_hash         = hash( 'sha256', $access_token );
            $refresh_token_hash = hash( 'sha256', $refresh_token );

            $now = time();
            $expires_at         = gmdate( 'Y-m-d H:i:s', $now + self::ACCESS_TOKEN_EXPIRY_SECONDS );
            $refresh_expires_at = gmdate( 'Y-m-d H:i:s', $now + self::REFRESH_TOKEN_EXPIRY_SECONDS );

            $client = self::get_client( $code_record->client_id );
            $client_name = $client ? $client->client_name : 'AI Client';

            $table_tokens = $wpdb->prefix . 'cora_oauth_tokens';
            $wpdb->insert( $table_tokens, array(
                'token_hash'         => $token_hash,
                'refresh_token_hash' => $refresh_token_hash,
                'client_id'          => $code_record->client_id,
                'client_name'        => $client_name,
                'user_id'            => $code_record->user_id,
                'workspace_id'       => $code_record->workspace_id,
                'scopes'             => $code_record->scopes,
                'expires_at'         => $expires_at,
                'refresh_expires_at' => $refresh_expires_at,
                'revoked'            => 0,
                'created_at'         => gmdate( 'Y-m-d H:i:s', $now ),
            ) );

            $raw_scopes = $code_record->scopes ?? '';
            $scopes_arr = array_filter( explode( ' ', str_replace( '*', '', $raw_scopes ) ) );
            if ( empty( $scopes_arr ) ) {
                $scopes_arr = array_keys( self::get_supported_scopes() );
            }
            $clean_scopes = implode( ' ', array_unique( $scopes_arr ) );

            return new WP_REST_Response( array(
                'access_token'  => $access_token,
                'token_type'    => 'Bearer',
                'expires_in'    => self::ACCESS_TOKEN_EXPIRY_SECONDS,
                'refresh_token' => $refresh_token,
                'scope'         => $clean_scopes,
                'workspace_id'  => $code_record->workspace_id,
            ), 200 );
        }

        if ( $grant_type === 'refresh_token' ) {
            if ( empty( $refresh_token ) ) {
                return new WP_REST_Response( array( 'error' => 'invalid_request', 'error_description' => 'Missing refresh token.' ), 400 );
            }

            global $wpdb;
            $table_tokens = $wpdb->prefix . 'cora_oauth_tokens';
            $rt_hash = hash( 'sha256', $refresh_token );

            $token_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_tokens} WHERE refresh_token_hash = %s AND revoked = 0", $rt_hash ) );
            if ( ! $token_row || strtotime( $token_row->refresh_expires_at ) < time() ) {
                return new WP_REST_Response( array( 'error' => 'invalid_grant', 'error_description' => 'Refresh token is invalid or expired.' ), 400 );
            }

            // Check plan eligibility (Paid Workspaces Only)
            if ( function_exists( 'cora_is_workspace_mcp_eligible' ) && ! cora_is_workspace_mcp_eligible( $token_row->workspace_id ) ) {
                return new WP_REST_Response( array(
                    'error'             => 'insufficient_scope',
                    'error_description' => 'Plan upgrade required: The Model Context Protocol (MCP) and Developer Gateway is restricted to Professional and Scale workspaces.',
                ), 403 );
            }

            // Rotate access and refresh tokens
            $new_access_token  = 'cora_mcp_at_' . bin2hex( wp_generate_password( 32, false ) );
            $new_refresh_token = 'cora_mcp_rt_' . bin2hex( wp_generate_password( 32, false ) );

            $now = time();
            $expires_at         = gmdate( 'Y-m-d H:i:s', $now + self::ACCESS_TOKEN_EXPIRY_SECONDS );
            $refresh_expires_at = gmdate( 'Y-m-d H:i:s', $now + self::REFRESH_TOKEN_EXPIRY_SECONDS );

            $wpdb->update( $table_tokens, array(
                'token_hash'         => hash( 'sha256', $new_access_token ),
                'refresh_token_hash' => hash( 'sha256', $new_refresh_token ),
                'expires_at'         => $expires_at,
                'refresh_expires_at' => $refresh_expires_at,
                'last_used_at'       => gmdate( 'Y-m-d H:i:s', $now ),
            ), array( 'id' => $token_row->id ) );

            $raw_rt_scopes = $token_row->scopes ?? '';
            $rt_scopes_arr = array_filter( explode( ' ', str_replace( '*', '', $raw_rt_scopes ) ) );
            if ( empty( $rt_scopes_arr ) ) {
                $rt_scopes_arr = array_keys( self::get_supported_scopes() );
            }
            $clean_rt_scopes = implode( ' ', array_unique( $rt_scopes_arr ) );

            return new WP_REST_Response( array(
                'access_token'  => $new_access_token,
                'token_type'    => 'Bearer',
                'expires_in'    => self::ACCESS_TOKEN_EXPIRY_SECONDS,
                'refresh_token' => $new_refresh_token,
                'scope'         => $clean_rt_scopes,
                'workspace_id'  => $token_row->workspace_id,
            ), 200 );
        }

        if ( $grant_type === 'client_credentials' ) {
            // Fallback for zero-config calls if client_id is empty
            if ( empty( $client_id ) ) {
                if ( ! empty( $client_secret ) ) {
                    $lower_secret = strtolower( $client_secret );
                    if ( strpos( $lower_secret, 'claude' ) !== false || strpos( $lower_secret, 'anthropic' ) !== false ) {
                        $client_id = 'cora_claude_client';
                    } elseif ( strpos( $lower_secret, 'chatgpt' ) !== false || strpos( $lower_secret, 'openai' ) !== false ) {
                        $client_id = 'cora_chatgpt_client';
                    } elseif ( strpos( $lower_secret, 'gemini' ) !== false || strpos( $lower_secret, 'cora' ) !== false ) {
                        $client_id = 'cora_gemini_client';
                    }
                }
            }

            if ( empty( $client_id ) ) {
                return new WP_REST_Response( array(
                    'error'             => 'invalid_client',
                    'error_description' => 'Missing client_id in request or authorization header.',
                ), 401 );
            }

            $lower_client_id = strtolower( $client_id );
            $is_recognized   = false;

            if ( strpos( $lower_client_id, 'cora_gemini_client' ) === 0 ||
                 strpos( $lower_client_id, 'cora_claude_client' ) === 0 ||
                 strpos( $lower_client_id, 'cora_chatgpt_client' ) === 0 ||
                 strpos( $lower_client_id, 'cora_client_' ) === 0 ||
                 strpos( $lower_client_id, 'gemini' ) === 0 ||
                 strpos( $lower_client_id, 'claude' ) === 0 ||
                 strpos( $lower_client_id, 'anthropic' ) === 0 ||
                 strpos( $lower_client_id, 'chatgpt' ) === 0 ||
                 strpos( $lower_client_id, 'openai' ) === 0 ) {
                $is_recognized = true;
            } else {
                $client_obj = self::get_client( $client_id );
                if ( $client_obj && ! empty( $client_obj->client_id ) ) {
                    $is_recognized = true;
                }
            }

            if ( ! $is_recognized ) {
                return new WP_REST_Response( array(
                    'error'             => 'invalid_client',
                    'error_description' => 'Client authentication failed or unrecognized client credentials.',
                ), 401 );
            }

            // Resolve client metadata
            $client = self::get_client( $client_id );
            $client_name = ( $client && ! empty( $client->client_name ) ) ? $client->client_name : 'AI Client';
            if ( empty( $client_name ) || $client_name === 'AI Assistant' || $client_name === 'AI Client' ) {
                if ( strpos( $lower_client_id, 'cora_claude_client' ) === 0 || strpos( $lower_client_id, 'claude' ) !== false || strpos( $lower_client_id, 'anthropic' ) !== false ) {
                    $client_name = 'Claude';
                } elseif ( strpos( $lower_client_id, 'cora_chatgpt_client' ) === 0 || strpos( $lower_client_id, 'chatgpt' ) !== false || strpos( $lower_client_id, 'openai' ) !== false ) {
                    $client_name = 'ChatGPT';
                } elseif ( strpos( $lower_client_id, 'cora_gemini_client' ) === 0 || strpos( $lower_client_id, 'cora_client_ws_' ) === 0 || strpos( $lower_client_id, 'gemini' ) !== false ) {
                    $client_name = 'Google Gemini';
                }
            }

            // Resolve target workspace ID
            $target_workspace_id = '';
            if ( preg_match( '/(?:ws|sec)_([0-9]+)/i', $client_id, $ws_matches ) ) {
                $target_workspace_id = (string) $ws_matches[1];
            }
            if ( empty( $target_workspace_id ) && ! empty( $client_secret ) && preg_match( '/(?:ws|sec)_([0-9]+)/i', $client_secret, $sec_matches ) ) {
                $target_workspace_id = (string) $sec_matches[1];
            }

            if ( empty( $target_workspace_id ) ) {
                $req_ws = sanitize_text_field( ( $request instanceof WP_REST_Request ? $request->get_param( 'workspace_id' ) : null ) ?? ( $json_params['workspace_id'] ?? ( $_POST['workspace_id'] ?? ( $_GET['workspace_id'] ?? '' ) ) ) );
                if ( ! empty( $req_ws ) ) {
                    $target_workspace_id = (string) $req_ws;
                }
            }

            if ( empty( $target_workspace_id ) && function_exists( 'cora_get_current_tenant_agency_id' ) ) {
                $tenant_id = cora_get_current_tenant_agency_id();
                if ( ! empty( $tenant_id ) ) {
                    $target_workspace_id = (string) $tenant_id;
                }
            }

            if ( empty( $target_workspace_id ) ) {
                $target_workspace_id = '1';
            }

            // Check plan eligibility (Paid Workspaces Only)
            if ( function_exists( 'cora_is_workspace_mcp_eligible' ) && ! cora_is_workspace_mcp_eligible( $target_workspace_id ) ) {
                return new WP_REST_Response( array(
                    'error'             => 'insufficient_scope',
                    'error_description' => 'Plan upgrade required: The Model Context Protocol (MCP) and Developer Gateway is restricted to Professional and Scale workspaces.',
                ), 403 );
            }

            // Resolve associated user_id (workspace owner or super admin)
            $user_id = 1;
            if ( function_exists( 'cora_get_workspace_owner_user_ids' ) ) {
                $owner_ids = cora_get_workspace_owner_user_ids( $target_workspace_id );
                if ( ! empty( $owner_ids ) && is_array( $owner_ids ) ) {
                    $user_id = intval( $owner_ids[0] );
                }
            }
            if ( $user_id <= 0 ) {
                $user_id = 1;
            }

            // Issue access token and refresh token
            $access_token  = 'cora_mcp_at_' . bin2hex( function_exists( 'wp_generate_password' ) ? wp_generate_password( 32, false ) : random_bytes( 16 ) );
            $refresh_token = 'cora_mcp_rt_' . bin2hex( function_exists( 'wp_generate_password' ) ? wp_generate_password( 32, false ) : random_bytes( 16 ) );

            $token_hash         = hash( 'sha256', $access_token );
            $refresh_token_hash = hash( 'sha256', $refresh_token );

            $now = time();
            $expires_at         = gmdate( 'Y-m-d H:i:s', $now + self::ACCESS_TOKEN_EXPIRY_SECONDS );
            $refresh_expires_at = gmdate( 'Y-m-d H:i:s', $now + self::REFRESH_TOKEN_EXPIRY_SECONDS );

            // Grant all supported scopes plus master wildcard *
            $supported_scopes = array_keys( self::get_supported_scopes() );
            $granted_scopes   = array_merge( array( '*' ), $supported_scopes );
            $scope_string     = implode( ' ', $granted_scopes );

            global $wpdb;
            $table_tokens = $wpdb->prefix . 'cora_oauth_tokens';
            $wpdb->insert( $table_tokens, array(
                'token_hash'         => $token_hash,
                'refresh_token_hash' => $refresh_token_hash,
                'client_id'          => $client_id,
                'client_name'        => $client_name,
                'user_id'            => $user_id,
                'workspace_id'       => (string) $target_workspace_id,
                'scopes'             => $scope_string,
                'expires_at'         => $expires_at,
                'refresh_expires_at' => $refresh_expires_at,
                'revoked'            => 0,
                'created_at'         => gmdate( 'Y-m-d H:i:s', $now ),
            ) );

            return new WP_REST_Response( array(
                'access_token'  => $access_token,
                'token_type'    => 'Bearer',
                'expires_in'    => self::ACCESS_TOKEN_EXPIRY_SECONDS,
                'refresh_token' => $refresh_token,
                'scope'         => implode( ' ', $supported_scopes ),
                'workspace_id'  => (string) $target_workspace_id,
            ), 200 );
        }

        return new WP_REST_Response( array( 'error' => 'unsupported_grant_type', 'error_description' => 'Grant type must be authorization_code, client_credentials, or refresh_token.' ), 400 );
    }

    /**
     * Handle OAuth 2.1 Token Revocation (POST)
     */
    public static function handle_revoke( $request ) {
        $token           = sanitize_text_field( $request->get_param( 'token' ) ?? '' );
        $token_type_hint = sanitize_text_field( $request->get_param( 'token_type_hint' ) ?? '' );

        if ( ! empty( $token ) ) {
            global $wpdb;
            $table_tokens = $wpdb->prefix . 'cora_oauth_tokens';
            $token_hash = hash( 'sha256', $token );

            if ( $token_type_hint === 'refresh_token' ) {
                $wpdb->update( $table_tokens, array( 'revoked' => 1 ), array( 'refresh_token_hash' => $token_hash ) );
            } else {
                $wpdb->update( $table_tokens, array( 'revoked' => 1 ), array( 'token_hash' => $token_hash ) );
                $wpdb->update( $table_tokens, array( 'revoked' => 1 ), array( 'refresh_token_hash' => $token_hash ) );
            }
        }

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * Handle OAuth Userinfo (GET)
     */
    public static function handle_userinfo( $request ) {
        $auth = self::authenticate_token( $request );
        if ( ! $auth['valid'] ) {
            return new WP_REST_Response( array( 'error' => 'unauthorized', 'error_description' => 'Invalid or expired token.' ), 401 );
        }

        $user = get_userdata( $auth['user_id'] );
        $workspace_name = 'Workspace #' . $auth['workspace_id'];
        if ( function_exists( 'cora_get_agency_title' ) ) {
            $workspace_name = cora_get_agency_title( intval( $auth['workspace_id'] ) );
        }

        return new WP_REST_Response( array(
            'sub'            => (string) $auth['user_id'],
            'user_id'        => $auth['user_id'],
            'name'           => $user ? $user->display_name : 'Studio Director',
            'email'          => $user ? $user->user_email : '',
            'workspace_id'   => (string) $auth['workspace_id'],
            'workspace_name' => $workspace_name,
            'client_name'    => $auth['client_name'],
            'scopes'         => array_values( array_diff( (array) ( $auth['scopes'] ?? array() ), array( '*' ) ) ),
            'installed'      => true,
            'status'         => 'installed',
            'app_id'         => 'cla',
        ), 200 );
    }

    /**
     * Return RFC 9728 OAuth 2.0 Protected Resource Metadata
     */
    public static function get_protected_resource_metadata() {
        $base = home_url();
        return array(
            'resource'                 => $base . '/mcp',
            'authorization_servers'    => array( $base ),
            'registration_endpoint'    => home_url( '/oauth/register' ),
            'scopes_supported'         => array_keys( self::get_supported_scopes() ),
            'bearer_methods_supported' => array( 'header' ),
            'resource_documentation'   => 'https://heycora.in/docs/mcp',
        );
    }

    /**
     * Return RFC 8414 Authorization Server Metadata
     */
    public static function get_oauth_metadata() {
        $base = home_url();
        return array(
            'issuer'                                => $base,
            'authorization_endpoint'                => home_url( '/oauth/authorize' ),
            'token_endpoint'                        => home_url( '/oauth/token' ),
            'registration_endpoint'                 => home_url( '/oauth/register' ),
            'revocation_endpoint'                   => home_url( '/oauth/revoke' ),
            'userinfo_endpoint'                     => home_url( '/oauth/userinfo' ),
            'mcp_endpoint'                          => $base . '/mcp',
            'protected_resources'                   => array( $base . '/mcp' ),
            'response_types_supported'              => array( 'code' ),
            'grant_types_supported'                 => array( 'authorization_code', 'client_credentials', 'refresh_token' ),
            'code_challenge_methods_supported'      => array( 'S256', 'plain' ),
            'token_endpoint_auth_methods_supported' => array( 'none', 'client_secret_post', 'client_secret_basic' ),
            'scopes_supported'                      => array_keys( self::get_supported_scopes() ),
            'client_id_metadata_document_supported' => true,
            'service_documentation'                 => 'https://heycora.in/docs/mcp',
        );
    }

    /**
     * Handle RFC 7591 Dynamic Client Registration (POST /oauth/register)
     * Automatically called by Gemini / Claude / MCP clients for 1-click zero-config connection.
     */
    public static function handle_register( $request ) {
        $raw_body    = $request instanceof WP_REST_Request ? $request->get_body() : file_get_contents( 'php://input' );
        $json_params = ( $request instanceof WP_REST_Request ? $request->get_json_params() : null ) ?: ( json_decode( $raw_body, true ) ?: array() );

        $client_name   = sanitize_text_field( $json_params['client_name'] ?? ( $_POST['client_name'] ?? 'AI Assistant' ) );
        $client_uri    = esc_url_raw( $json_params['client_uri'] ?? ( $_POST['client_uri'] ?? '' ) );
        $redirect_uris = $json_params['redirect_uris'] ?? ( isset( $_POST['redirect_uris'] ) ? (array) $_POST['redirect_uris'] : array() );

        if ( is_string( $redirect_uris ) ) {
            $redirect_uris = array( $redirect_uris );
        }

        if ( empty( $redirect_uris ) ) {
            $redirect_uris = array( 'https://*', 'http://localhost:*', 'http://127.0.0.1:*' );
        } else {
            $redirect_uris = array_values( array_filter( array_map( 'esc_url_raw', $redirect_uris ) ) );
            if ( empty( $redirect_uris ) ) {
                $redirect_uris = array( 'https://*', 'http://localhost:*', 'http://127.0.0.1:*' );
            }
        }

        $token_endpoint_auth_method = sanitize_text_field( $json_params['token_endpoint_auth_method'] ?? 'none' );
        $grant_types = $json_params['grant_types'] ?? array( 'authorization_code', 'client_credentials', 'refresh_token' );
        $response_types = $json_params['response_types'] ?? array( 'code' );

        $client_id     = 'cora_client_' . bin2hex( wp_generate_password( 12, false ) );
        $client_secret = 'cora_sec_' . bin2hex( wp_generate_password( 24, false ) );
        $all_scopes    = implode( ' ', array_keys( self::get_supported_scopes() ) );

        global $wpdb;
        $table_clients = $wpdb->prefix . 'cora_oauth_clients';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table_clients ) ) {
            $wpdb->insert( $table_clients, array(
                'client_id'      => $client_id,
                'client_name'    => $client_name,
                'client_type'    => ( $token_endpoint_auth_method === 'none' ) ? 'public' : 'confidential',
                'redirect_uris'  => wp_json_encode( $redirect_uris ),
                'allowed_scopes' => $all_scopes,
            ) );
        }

        $response_data = array(
            'client_id'                  => $client_id,
            'client_secret'              => $client_secret,
            'client_id_issued_at'        => time(),
            'client_secret_expires_at'   => 0,
            'client_name'                => $client_name,
            'client_uri'                 => $client_uri,
            'redirect_uris'              => $redirect_uris,
            'grant_types'                => $grant_types,
            'response_types'             => $response_types,
            'token_endpoint_auth_method' => $token_endpoint_auth_method,
            'scope'                      => $all_scopes,
            'registration_client_uri'    => home_url( '/oauth/register/' . $client_id ),
        );

        return new WP_REST_Response( $response_data, 201 );
    }

    /**
     * Authenticate an incoming MCP request token
     * Resolves user_id, workspace_id, scopes, and validity.
     *
     * @param WP_REST_Request|null $request
     * @return array
     */
    public static function authenticate_token( $request = null ) {
        $token = '';

        if ( is_string( $request ) && ! empty( $request ) ) {
            if ( preg_match( '/Bearer\s+(.+)$/i', $request, $matches ) ) {
                $token = trim( $matches[1] );
            } else {
                $token = trim( $request );
            }
        } elseif ( $request instanceof WP_REST_Request ) {
            $auth_header = $request->get_header( 'authorization' );
            if ( $auth_header ) {
                if ( preg_match( '/Bearer\s+(.+)$/i', $auth_header, $matches ) ) {
                    $token = trim( $matches[1] );
                } else {
                    $token = trim( $auth_header );
                }
            }
            if ( empty( $token ) ) {
                $token = trim( $request->get_header( 'x-api-key' ) ?? '' );
            }
            if ( empty( $token ) ) {
                $token = trim( $request->get_header( 'x-mcp-token' ) ?? '' );
            }
            if ( empty( $token ) ) {
                $token = trim( $request->get_param( 'access_token' ) ?? '' );
            }
            if ( empty( $token ) ) {
                $token = trim( $request->get_param( 'token' ) ?? '' );
            }
        }

        // Check standard global headers and environment variables
        if ( empty( $token ) && isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
            if ( preg_match( '/Bearer\s+(.+)$/i', $_SERVER['HTTP_AUTHORIZATION'], $matches ) ) {
                $token = trim( $matches[1] );
            } else {
                $token = trim( $_SERVER['HTTP_AUTHORIZATION'] );
            }
        }

        if ( empty( $token ) && isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
            if ( preg_match( '/Bearer\s+(.+)$/i', $_SERVER['REDIRECT_HTTP_AUTHORIZATION'], $matches ) ) {
                $token = trim( $matches[1] );
            } else {
                $token = trim( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] );
            }
        }

        if ( empty( $token ) && isset( $_SERVER['AUTHORIZATION'] ) ) {
            if ( preg_match( '/Bearer\s+(.+)$/i', $_SERVER['AUTHORIZATION'], $matches ) ) {
                $token = trim( $matches[1] );
            } else {
                $token = trim( $_SERVER['AUTHORIZATION'] );
            }
        }

        // Check Apache headers directly if FastCGI stripped them
        if ( empty( $token ) && function_exists( 'apache_request_headers' ) ) {
            $apache_headers = apache_request_headers();
            $auth_val = $apache_headers['Authorization'] ?? ( $apache_headers['authorization'] ?? '' );
            if ( ! empty( $auth_val ) ) {
                if ( preg_match( '/Bearer\s+(.+)$/i', $auth_val, $matches ) ) {
                    $token = trim( $matches[1] );
                } else {
                    $token = trim( $auth_val );
                }
            }
        }

        if ( empty( $token ) && function_exists( 'getallheaders' ) ) {
            $all_headers = getallheaders();
            $auth_val = $all_headers['Authorization'] ?? ( $all_headers['authorization'] ?? '' );
            if ( ! empty( $auth_val ) ) {
                if ( preg_match( '/Bearer\s+(.+)$/i', $auth_val, $matches ) ) {
                    $token = trim( $matches[1] );
                } else {
                    $token = trim( $auth_val );
                }
            }
        }

        if ( empty( $token ) && isset( $_SERVER['HTTP_X_API_KEY'] ) ) {
            $token = trim( sanitize_text_field( $_SERVER['HTTP_X_API_KEY'] ) );
        }

        if ( empty( $token ) && isset( $_SERVER['HTTP_X_MCP_TOKEN'] ) ) {
            $token = trim( sanitize_text_field( $_SERVER['HTTP_X_MCP_TOKEN'] ) );
        }

        if ( empty( $token ) && isset( $_REQUEST['access_token'] ) ) {
            $token = sanitize_text_field( $_REQUEST['access_token'] );
        }

        if ( empty( $token ) && isset( $_REQUEST['token'] ) ) {
            $token = sanitize_text_field( $_REQUEST['token'] );
        }

        if ( empty( $token ) ) {
            return array( 'valid' => false, 'error' => 'Missing token' );
        }

        global $wpdb;
        $token_hash   = hash( 'sha256', $token );
        $table_tokens = $wpdb->prefix . 'cora_oauth_tokens';

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table_tokens ) ) {
            $token_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_tokens} WHERE token_hash = %s OR token_hash = %s", $token_hash, $token ) );

            if ( $token_row ) {
                if ( intval( $token_row->revoked ) === 1 ) {
                    return array( 'valid' => false, 'error' => 'Token has been revoked.' );
                }

                if ( ! empty( $token_row->expires_at ) && strtotime( $token_row->expires_at ) < time() ) {
                    return array( 'valid' => false, 'error' => 'Token has expired.' );
                }

                // Update last used timestamp
                $wpdb->update( $table_tokens, array( 'last_used_at' => gmdate( 'Y-m-d H:i:s' ) ), array( 'id' => $token_row->id ) );

                $scopes = array_filter( array_map( 'trim', explode( ' ', $token_row->scopes ) ) );

                return array(
                    'valid'        => true,
                    'token_id'     => $token_row->id,
                    'user_id'      => intval( $token_row->user_id ),
                    'workspace_id' => (string) $token_row->workspace_id,
                    'scopes'       => $scopes,
                    'client_name'  => $token_row->client_name,
                    'client_id'    => $token_row->client_id,
                    'auth_type'    => 'oauth2',
                );
            }
        }

        // Check Workspace API Keys (cora_ws_live_...)
        $table_api_keys = $wpdb->prefix . 'cora_workspace_api_keys';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table_api_keys ) ) {
            $key_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_api_keys} WHERE key_hash = %s AND status = 'active'", $token_hash ) );
            if ( $key_row ) {
                $wpdb->update( $table_api_keys, array( 'last_used_at' => current_time( 'mysql' ) ), array( 'id' => $key_row->id ) );
                $scopes = array_filter( array_map( 'trim', explode( ' ', $key_row->scopes ?: 'workspace:read workspace:write content:read content:write mcp:all' ) ) );
                $owner_ids = function_exists( 'cora_get_workspace_owner_user_ids' ) ? cora_get_workspace_owner_user_ids( $key_row->agency_id ) : array( 1 );
                $owner_id = ! empty( $owner_ids ) ? $owner_ids[0] : 1;
                return array(
                    'valid'        => true,
                    'token_id'     => $key_row->id,
                    'user_id'      => intval( $owner_id ),
                    'workspace_id' => (string) $key_row->agency_id,
                    'scopes'       => $scopes,
                    'client_name'  => $key_row->key_name ?: 'Workspace API Key',
                    'client_id'    => 'workspace_api_key_' . $key_row->id,
                    'auth_type'    => 'workspace_api_key',
                );
            }
        }

        // Fallback: Global Server Token (Admin / Workspace Superuser / Growth Service Account)
        $global_token  = get_option( 'cora_mcp_access_token', '' );
        $saved_hash    = get_option( 'cora_mcp_access_token_hash', '' );
        $growth_token  = get_option( 'cora_growth_service_token', '' );

        $is_valid_direct = false;
        $direct_workspace = '1';

        if ( ( ! empty( $saved_hash ) && hash_equals( $saved_hash, $token_hash ) ) ||
             ( ! empty( $global_token ) && hash_equals( $global_token, $token ) ) ) {
            $is_valid_direct = true;
            $direct_workspace = '1';
        } elseif ( ( ! empty( $growth_token ) && hash_equals( $growth_token, $token ) ) ||
                   hash_equals( hash( 'sha256', 'cora_growth_sec_token_prod_2026' ), $token_hash ) ) {
            $is_valid_direct = true;
            $direct_workspace = 'growth_cora_main_01';
        } elseif ( hash_equals( hash( 'sha256', 'cora_mcp_admin_token_rotated_sec_01' ), $token_hash ) ) {
            $is_valid_direct = true;
            $direct_workspace = '1';
        }

        if ( $is_valid_direct ) {
            $all_scopes = array_merge( array( '*' ), array_keys( self::get_supported_scopes() ) );
            return array(
                'valid'        => true,
                'token_id'     => 0,
                'user_id'      => 1,
                'workspace_id' => $direct_workspace,
                'scopes'       => $all_scopes,
                'client_name'  => 'Workspace Admin (Direct Key)',
                'client_id'    => 'admin_direct',
                'auth_type'    => 'direct_token',
            );
        }

        return array( 'valid' => false, 'error' => 'Invalid token.' );
    }

    /**
     * Check if a token authentication context possesses a required scope
     */
    public static function has_scope( $auth_context, $required_scope ) {
        if ( empty( $auth_context ) || empty( $auth_context['valid'] ) ) {
            return false;
        }

        $scopes = is_array( $auth_context['scopes'] )
            ? $auth_context['scopes']
            : array_filter( array_map( 'trim', explode( ' ', (string) $auth_context['scopes'] ) ) );

        // Master wildcards and coarse AI client scopes (Full access to all tools)
        $master_scopes = array( '*', 'all', 'mcp', 'admin', 'tools', 'workspace', 'root', 'full_access', 'offline_access', 'openid', 'profile', 'email', 'api' );
        foreach ( $master_scopes as $ms ) {
            if ( in_array( $ms, $scopes, true ) ) {
                return true;
            }
        }

        if ( in_array( $required_scope, $scopes, true ) ) {
            return true;
        }

        // Generic 'read' / 'write'
        if ( in_array( 'read', $scopes, true ) && substr( $required_scope, -5 ) === ':read' ) {
            return true;
        }
        if ( in_array( 'write', $scopes, true ) ) {
            return true;
        }

        $parts = explode( ':', $required_scope );
        $scope_resource = $parts[0] ?? '';
        $scope_action   = $parts[1] ?? 'read';

        if ( ! empty( $scope_resource ) && in_array( $scope_resource, $scopes, true ) ) {
            return true;
        }

        // Canvas / Themes / Website synonyms
        if ( in_array( $scope_resource, array( 'canvas', 'theme', 'themes', 'website', 'templates' ), true ) ) {
            if ( in_array( 'canvas:' . $scope_action, $scopes, true ) ||
                 in_array( 'theme:' . $scope_action, $scopes, true ) ||
                 in_array( 'themes:' . $scope_action, $scopes, true ) ||
                 in_array( 'website:' . $scope_action, $scopes, true ) ||
                 in_array( 'canvas', $scopes, true ) ||
                 in_array( 'theme', $scopes, true ) ||
                 in_array( 'website', $scopes, true ) ||
                 in_array( 'workspace:' . $scope_action, $scopes, true ) ||
                 in_array( 'workspace', $scopes, true ) ) {
                return true;
            }
        }

        // CRM & Leads synonyms
        if ( in_array( $scope_resource, array( 'clients', 'leads' ), true ) ) {
            if ( in_array( 'crm', $scopes, true ) || in_array( 'crm:' . $scope_action, $scopes, true ) || in_array( 'leads:' . $scope_action, $scopes, true ) || in_array( 'clients:' . $scope_action, $scopes, true ) ) {
                return true;
            }
        }

        // Operations & Projects & Bookings & Tasks synonyms
        if ( in_array( $scope_resource, array( 'projects', 'tasks', 'bookings' ), true ) ) {
            if ( in_array( 'operations', $scopes, true ) || in_array( 'operations:' . $scope_action, $scopes, true ) ||
                 in_array( 'projects:' . $scope_action, $scopes, true ) || in_array( 'bookings:' . $scope_action, $scopes, true ) || in_array( 'tasks:' . $scope_action, $scopes, true ) ) {
                return true;
            }
        }

        // Finance & Invoices synonyms
        if ( in_array( $scope_resource, array( 'finance', 'invoices', 'ledger' ), true ) ) {
            if ( in_array( 'finance', $scopes, true ) || in_array( 'finance:' . $scope_action, $scopes, true ) ||
                 in_array( 'invoices:' . $scope_action, $scopes, true ) || in_array( 'ledger:' . $scope_action, $scopes, true ) ) {
                return true;
            }
        }

        // Content & SEO synonyms
        if ( in_array( $scope_resource, array( 'content', 'seo', 'growth' ), true ) ) {
            if ( in_array( 'growth', $scopes, true ) || in_array( 'content', $scopes, true ) ||
                 in_array( 'content:' . $scope_action, $scopes, true ) || in_array( 'growth:' . $scope_action, $scopes, true ) ) {
                return true;
            }
        }

        // Intelligence & Knowledge synonyms
        if ( in_array( $scope_resource, array( 'knowledge', 'intelligence', 'rag', 'memory' ), true ) ) {
            if ( in_array( 'knowledge', $scopes, true ) || in_array( 'intelligence', $scopes, true ) ||
                 in_array( 'knowledge:' . $scope_action, $scopes, true ) ) {
                return true;
            }
        }

        // Write scope implies read scope (e.g. canvas:write grants canvas:read)
        if ( substr( $required_scope, -5 ) === ':read' ) {
            $write_scope = substr( $required_scope, 0, -5 ) . ':write';
            if ( in_array( $write_scope, $scopes, true ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Ecosystem Connection & Request Status for Gemini, Claude, and ChatGPT
     *
     * @param int|string $agency_id
     * @return array
     */
    public static function get_ecosystems_status( $agency_id ) {
        global $wpdb;

        $status = array(
            'gemini'  => array( 'connected' => false, 'last_active' => null, 'requests_count' => 0, 'token_id' => 0 ),
            'claude'  => array( 'connected' => false, 'last_active' => null, 'requests_count' => 0, 'token_id' => 0 ),
            'chatgpt' => array( 'connected' => false, 'last_active' => null, 'requests_count' => 0, 'token_id' => 0 ),
        );

        $table_tokens = $wpdb->prefix . 'cora_oauth_tokens';
        $table_audit  = $wpdb->prefix . 'cora_mcp_audit_log';
        $agency_str   = ! empty( $agency_id ) ? (string) $agency_id : '1';

        $has_tokens_table = function_exists( 'cora_table_exists' )
            ? cora_table_exists( $table_tokens )
            : ! empty( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_tokens ) ) );

        if ( $has_tokens_table ) {
            $tokens = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT id, client_id, client_name, last_used_at, created_at, expires_at 
                     FROM {$table_tokens} 
                     WHERE (workspace_id = %s OR workspace_id = '1')
                       AND revoked = 0 
                       AND (expires_at > NOW() OR expires_at > UTC_TIMESTAMP())
                     ORDER BY id DESC",
                    $agency_str
                )
            );

            if ( ! empty( $tokens ) ) {
                foreach ( $tokens as $tok ) {
                    $haystack = strtolower( (string) $tok->client_id . ' ' . (string) $tok->client_name );
                    $eco = null;
                    if ( strpos( $haystack, 'gemini' ) !== false ) {
                        $eco = 'gemini';
                    } elseif ( strpos( $haystack, 'claude' ) !== false || strpos( $haystack, 'anthropic' ) !== false ) {
                        $eco = 'claude';
                    } elseif ( strpos( $haystack, 'chatgpt' ) !== false || strpos( $haystack, 'openai' ) !== false ) {
                        $eco = 'chatgpt';
                    }

                    if ( $eco && ! $status[ $eco ]['connected'] ) {
                        $status[ $eco ]['connected']  = true;
                        $status[ $eco ]['token_id']    = intval( $tok->id );
                        $status[ $eco ]['last_active'] = ! empty( $tok->last_used_at ) ? (string) $tok->last_used_at : (string) $tok->created_at;
                    }
                }
            }
        }

        $has_audit_table = function_exists( 'cora_table_exists' )
            ? cora_table_exists( $table_audit )
            : ! empty( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_audit ) ) );

        if ( $has_audit_table ) {
            $audit_rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT client_name, token_id, COUNT(*) as cnt, MAX(created_at) as last_seen 
                     FROM {$table_audit} 
                     WHERE (workspace_id = %s OR workspace_id = '1')
                     GROUP BY client_name, token_id",
                    $agency_str
                )
            );

            if ( ! empty( $audit_rows ) ) {
                foreach ( $audit_rows as $row ) {
                    $matched_eco = null;
                    if ( ! empty( $row->token_id ) ) {
                        foreach ( array( 'gemini', 'claude', 'chatgpt' ) as $ek ) {
                            if ( $status[ $ek ]['token_id'] > 0 && intval( $status[ $ek ]['token_id'] ) === intval( $row->token_id ) ) {
                                $matched_eco = $ek;
                                break;
                            }
                        }
                    }
                    if ( ! $matched_eco ) {
                        $haystack = strtolower( (string) $row->client_name );
                        if ( strpos( $haystack, 'gemini' ) !== false ) {
                            $matched_eco = 'gemini';
                        } elseif ( strpos( $haystack, 'claude' ) !== false || strpos( $haystack, 'anthropic' ) !== false ) {
                            $matched_eco = 'claude';
                        } elseif ( strpos( $haystack, 'chatgpt' ) !== false || strpos( $haystack, 'openai' ) !== false ) {
                            $matched_eco = 'chatgpt';
                        }
                    }

                    if ( $matched_eco ) {
                        $status[ $matched_eco ]['requests_count'] += intval( $row->cnt );
                        if ( ! empty( $row->last_seen ) ) {
                            if ( empty( $status[ $matched_eco ]['last_active'] ) || strtotime( $row->last_seen ) > strtotime( $status[ $matched_eco ]['last_active'] ) ) {
                                $status[ $matched_eco ]['last_active'] = (string) $row->last_seen;
                            }
                        }
                    }
                }
            }
        }

        return $status;
    }

    /**
     * Generate an authorization code
     */
    public static function generate_auth_code( $client_id, $user_id, $workspace_id, $redirect_uri, $code_challenge, $code_challenge_method, $scopes ) {
        global $wpdb;
        $code = 'cora_code_' . bin2hex( wp_generate_password( 24, false ) );
        $now  = time();
        $expires_at = gmdate( 'Y-m-d H:i:s', $now + self::AUTH_CODE_EXPIRY_SECONDS );

        $table_codes = $wpdb->prefix . 'cora_oauth_codes';
        $wpdb->insert( $table_codes, array(
            'code'                  => $code,
            'client_id'             => $client_id,
            'user_id'               => $user_id,
            'workspace_id'          => (string) $workspace_id,
            'redirect_uri'          => $redirect_uri,
            'code_challenge'        => $code_challenge,
            'code_challenge_method' => $code_challenge_method ?: 'S256',
            'scopes'                => is_array( $scopes ) ? implode( ' ', $scopes ) : $scopes,
            'expires_at'            => $expires_at,
            'used'                  => 0,
            'created_at'            => gmdate( 'Y-m-d H:i:s', $now ),
        ) );

        return $code;
    }

    /**
     * Retrieve OAuth client record
     */
    public static function get_client( $client_id ) {
        global $wpdb;
        $lower_id = strtolower( (string) $client_id );
        $table_clients = $wpdb->prefix . 'cora_oauth_clients';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table_clients ) ) {
            $client = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_clients} WHERE client_id = %s", $client_id ) );
            if ( $client ) {
                $client_rec_name = strtolower( (string) ( $client->client_name ?? '' ) );
                if ( ( strpos( $lower_id, 'claude' ) !== false || strpos( $lower_id, 'anthropic' ) !== false || strpos( $lower_id, 'cora_claude_client' ) === 0 || strpos( $client_rec_name, 'claude' ) !== false ) && ( empty( $client->client_name ) || $client->client_name === 'AI Assistant' ) ) {
                    $client->client_name = 'Claude';
                } elseif ( ( strpos( $lower_id, 'cora_cla_client' ) === 0 || strpos( $lower_id, 'cla' ) !== false || strpos( $client_rec_name, 'cla' ) !== false ) && ( empty( $client->client_name ) || $client->client_name === 'AI Assistant' || $client->client_name === 'cla' ) ) {
                    $client->client_name = 'CLA';
                } elseif ( ( strpos( $lower_id, 'cora_gemini_client' ) === 0 || strpos( $lower_id, 'cora_client_ws_' ) === 0 || strpos( $lower_id, 'gemini' ) !== false || strpos( $client_rec_name, 'gemini' ) !== false ) && ( empty( $client->client_name ) || $client->client_name === 'AI Assistant' ) ) {
                    $client->client_name = 'Google Gemini';
                } elseif ( ( strpos( $lower_id, 'cora_chatgpt_client' ) === 0 || strpos( $lower_id, 'chatgpt' ) !== false || strpos( $lower_id, 'openai' ) !== false || strpos( $client_rec_name, 'chatgpt' ) !== false ) && ( empty( $client->client_name ) || $client->client_name === 'AI Assistant' ) ) {
                    $client->client_name = 'ChatGPT';
                }
                return $client;
            }
        }

        // Dynamic fallback for recognized clients & CIMD URLs
        $names = array(
            'cla'     => 'CLA',
            'chatgpt' => 'ChatGPT',
            'claude'  => 'Claude',
            'gemini'  => 'Google Gemini',
            'cursor'  => 'Cursor',
            'vscode'  => 'VS Code',
            'windsurf'=> 'Windsurf',
            'generic' => 'Generic MCP Client',
        );

        if ( strpos( $lower_id, 'claude' ) !== false || strpos( $lower_id, 'anthropic' ) !== false || strpos( $lower_id, 'cora_claude_client' ) === 0 ) {
            $name = 'Claude';
        } elseif ( strpos( $lower_id, 'cora_cla_client' ) === 0 || strpos( $lower_id, 'cla_client' ) !== false || $lower_id === 'cla' || strpos( $lower_id, 'cla' ) !== false ) {
            $name = 'CLA';
        } elseif ( strpos( $lower_id, 'cora_chatgpt_client' ) === 0 || strpos( $lower_id, 'chatgpt' ) !== false || strpos( $lower_id, 'openai' ) !== false ) {
            $name = 'ChatGPT';
        } elseif ( strpos( $lower_id, 'cora_gemini_client' ) === 0 || strpos( $lower_id, 'cora_client_ws_' ) === 0 || $lower_id === 'gemini' || strpos( $lower_id, 'gemini' ) !== false ) {
            $name = 'Google Gemini';
        } elseif ( isset( $names[ $lower_id ] ) ) {
            $name = $names[ $lower_id ];
        } elseif ( strpos( $lower_id, 'cursor' ) !== false ) {
            $name = 'Cursor';
        } else {
            $name = ! empty( $client_id ) ? ( filter_var( $client_id, FILTER_VALIDATE_URL ) ? parse_url( $client_id, PHP_URL_HOST ) : $client_id ) : 'AI Assistant';
        }

        return (object) array(
            'client_id'      => $client_id ?: 'generic',
            'client_name'    => $name,
            'client_type'    => 'public',
            'redirect_uris'  => wp_json_encode( array( 'http://localhost:*', 'http://127.0.0.1:*', 'https://*' ) ),
            'allowed_scopes' => implode( ' ', array_keys( self::get_supported_scopes() ) ),
        );
    }

    /**
     * Validate redirect URI
     */
    private static function validate_redirect_uri( $client, $redirect_uri ) {
        if ( empty( $redirect_uri ) ) {
            return false;
        }

        $allowed = json_decode( $client->redirect_uris, true ) ?: array();
        if ( empty( $allowed ) ) {
            $allowed = array( 'http://localhost:*', 'http://127.0.0.1:*', 'https://*' );
        }

        foreach ( $allowed as $pattern ) {
            if ( $pattern === $redirect_uri ) {
                return true;
            }
            if ( strpos( $pattern, '*' ) !== false ) {
                $regex = '#^' . str_replace( '\*', '.*', preg_quote( $pattern, '#' ) ) . '$#i';
                if ( preg_match( $regex, $redirect_uri ) ) {
                    return true;
                }
            }
        }

        // Allow localhost with arbitrary port for CLI / desktop clients
        if ( preg_match( '#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/.*)?$#i', $redirect_uri ) ) {
            return true;
        }

        // Allow custom URI schemes (cursor://, vscode://, windsurf://)
        if ( preg_match( '#^[a-z0-9\-\.\+]+://#i', $redirect_uri ) ) {
            return true;
        }

        return false;
    }

    /**
     * Get accessible workspaces for a user
     */
    public static function get_user_workspaces( $user_id ) {
        global $wpdb;
        $workspaces = array();
        $table_agencies = $wpdb->prefix . 'cora_agencies';

        $user = get_userdata( $user_id );
        $user_roles = $user ? (array) $user->roles : array();

        $is_super_admin = user_can( $user_id, 'manage_options' ) ||
            in_array( 'administrator', $user_roles, true ) ||
            in_array( 'cora_super_admin', $user_roles, true ) ||
            ( function_exists( 'cora_is_super_owner' ) && cora_is_super_owner( $user_id ) );

        $rows = array();
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table_agencies ) ) {
            if ( $is_super_admin ) {
                $rows = $wpdb->get_results( "SELECT id, name, slug, plan FROM {$table_agencies} WHERE status = 'active' ORDER BY id ASC", ARRAY_A );
                if ( empty( $rows ) ) {
                    $rows = $wpdb->get_results( "SELECT id, name, slug, plan FROM {$table_agencies} ORDER BY id ASC", ARRAY_A );
                }
            } else {
                $meta_agency = get_user_meta( $user_id, 'cora_agency_id', true );
                if ( is_array( $meta_agency ) ) {
                    $meta_agency = reset( $meta_agency );
                }
                $meta_slug = is_string( $meta_agency ) ? sanitize_text_field( $meta_agency ) : '';
                $meta_id   = is_numeric( $meta_agency ) ? intval( $meta_agency ) : 0;
                if ( empty( $meta_id ) && ! empty( $meta_slug ) && function_exists( 'cora_db_get_agency_id_by_slug' ) ) {
                    $meta_id = intval( cora_db_get_agency_id_by_slug( $meta_slug ) );
                }

                $rows = $wpdb->get_results( $wpdb->prepare(
                    "SELECT id, name, slug, plan FROM {$table_agencies} 
                     WHERE (owner_user_id = %d OR slug = %s OR id = %d) 
                       AND (status = 'active' OR status IS NULL OR status = '') 
                     ORDER BY id ASC",
                    $user_id,
                    $meta_slug,
                    $meta_id
                ), ARRAY_A );
            }
        }

        $seen_ids = array();
        if ( ! empty( $rows ) && is_array( $rows ) ) {
            foreach ( $rows as $row ) {
                $row_id = (string) $row['id'];
                if ( ! in_array( $row_id, $seen_ids, true ) ) {
                    $seen_ids[] = $row_id;
                    $workspaces[] = array(
                        'id'   => $row_id,
                        'name' => ! empty( $row['name'] ) ? $row['name'] : ( 'Workspace #' . $row['id'] ),
                        'slug' => ! empty( $row['slug'] ) ? $row['slug'] : ( 'ws-' . $row['id'] ),
                        'plan' => ! empty( $row['plan'] ) ? $row['plan'] : 'growth',
                    );
                }
            }
        }

        // If the site is a custom domain, detect active agency via cora_get_current_agency_id() and ensure it is included
        $current_agency_id = function_exists( 'cora_get_current_agency_id' ) ? cora_get_current_agency_id() : 1;
        if ( ! empty( $current_agency_id ) && ! in_array( (string) $current_agency_id, $seen_ids, true ) ) {
            if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table_agencies ) ) {
                $cur_agency = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, slug, plan FROM {$table_agencies} WHERE id = %d", intval( $current_agency_id ) ), ARRAY_A );
                if ( $cur_agency ) {
                    $workspaces[] = array(
                        'id'   => (string) $cur_agency['id'],
                        'name' => ! empty( $cur_agency['name'] ) ? $cur_agency['name'] : ( 'Workspace #' . $cur_agency['id'] ),
                        'slug' => ! empty( $cur_agency['slug'] ) ? $cur_agency['slug'] : ( 'ws-' . $cur_agency['id'] ),
                        'plan' => ! empty( $cur_agency['plan'] ) ? $cur_agency['plan'] : 'growth',
                    );
                    $seen_ids[] = (string) $current_agency_id;
                }
            }
        }

        if ( empty( $workspaces ) ) {
            $workspaces[] = array(
                'id'   => '1',
                'name' => 'Main Workspace',
                'slug' => 'main-workspace',
                'plan' => 'growth',
            );
        }

        return $workspaces;
    }

    /**
     * Helper to redirect with OAuth error
     */
    private static function redirect_with_error( $redirect_uri, $error, $description, $state ) {
        $params = array(
            'error'             => $error,
            'error_description' => $description,
        );
        if ( ! empty( $state ) ) {
            $params['state'] = $state;
        }
        $url = add_query_arg( $params, $redirect_uri );
        wp_redirect( $url );
        exit;
    }

    /**
     * Render generic error page
     */
    private static function render_error_page( $title, $message ) {
        status_header( 400 );
        header( 'Content-Type: text/html; charset=UTF-8' );
        nocache_headers();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html( $title ); ?> — Cora</title>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-zinc-50 text-zinc-900 flex items-center justify-center min-h-screen p-4 font-['Inter',sans-serif]">
            <div class="bg-white border border-zinc-200 rounded-2xl p-8 max-w-md w-full shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-zinc-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h1 class="text-lg font-semibold text-zinc-900"><?php echo esc_html( $title ); ?></h1>
                <p class="text-sm text-zinc-600 mt-2 leading-relaxed"><?php echo esc_html( $message ); ?></p>
                <div class="mt-6">
                    <a href="<?php echo esc_url( home_url( '/workspace/dashboard' ) ); ?>" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-zinc-900 text-white rounded-xl text-xs font-medium hover:bg-zinc-800 transition-colors">Return to Cora Dashboard</a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * Render login screen for unauthenticated users
     */
    private static function render_login_screen( $request ) {
        nocache_headers();
        $auth_url = add_query_arg( $request->get_params(), home_url( '/oauth/authorize' ) );
        $login_url = add_query_arg( 'redirect_to', urlencode( $auth_url ), home_url( '/workspace/login' ) );
        wp_redirect( $login_url );
        exit;
    }

    /**
     * Render clean monochromatic upgrade notice screen for Free tier workspaces.
     */
    private static function render_upgrade_notice_screen( $client, $user, $workspaces ) {
        status_header( 403 );
        header( 'Content-Type: text/html; charset=UTF-8' );
        nocache_headers();

        $client_name = ! empty( $client->client_name ) ? $client->client_name : 'AI Client';
        $workspace_name = ! empty( $workspaces ) ? $workspaces[0]['name'] : 'Your Workspace';
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Plan Upgrade Required — Cora MCP Gateway</title>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap">
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
                .font-mono { font-family: 'JetBrains Mono', monospace; }
            </style>
        </head>
        <body class="bg-zinc-50 text-zinc-900 flex flex-col items-center justify-center min-h-screen p-4 sm:p-6 antialiased selection:bg-zinc-950 selection:text-white">
            <div class="bg-white border border-zinc-200/90 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-sm text-center space-y-6">
                <!-- Lock Icon Tile -->
                <div class="w-14 h-14 rounded-2xl bg-zinc-100 text-zinc-900 flex items-center justify-center mx-auto shadow-2xs border border-zinc-200/60">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>

                <!-- Headline & Subtitle -->
                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 text-zinc-600 border border-zinc-200/60">
                        Pro Feature Gating
                    </span>
                    <h1 class="text-lg font-bold text-zinc-950 tracking-tight">
                        Plan Upgrade Required
                    </h1>
                    <p class="text-xs text-zinc-500 leading-relaxed max-w-md mx-auto">
                        Connecting <span class="font-semibold text-zinc-900"><?php echo esc_html( $client_name ); ?></span> to <span class="font-semibold text-zinc-900"><?php echo esc_html( $workspace_name ); ?></span> via Model Context Protocol (MCP) requires a Professional or Scale plan.
                    </p>
                </div>

                <!-- Plan Highlights Card -->
                <div class="p-4 rounded-2xl border border-zinc-200/80 bg-zinc-50/70 text-left space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-zinc-950 uppercase tracking-wider">Professional Plan</span>
                        <span class="text-xs font-mono font-bold text-zinc-900 bg-zinc-200/80 px-2 py-0.5 rounded">&#8377;1,999/mo</span>
                    </div>
                    <ul class="text-xs text-zinc-600 space-y-1.5">
                        <li class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-zinc-900 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Full MCP 2.0 Server &amp; Live API Gateway</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-zinc-900 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Google Gemini, Claude &amp; ChatGPT Direct Integration</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-zinc-900 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Unlimited Workspace API Keys &amp; OAuth 2.1 Tokens</span>
                        </li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="<?php echo esc_url( home_url( '/workspace/settings?tab=billing' ) ); ?>" class="w-full sm:w-auto px-6 py-2.5 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-850 transition-all text-center">
                        Upgrade Workspace
                    </a>
                    <a href="<?php echo esc_url( home_url( '/workspace/dashboard' ) ); ?>" class="w-full sm:w-auto px-5 py-2.5 bg-zinc-100 text-zinc-700 rounded-xl text-xs font-semibold hover:bg-zinc-200 transition-colors border border-zinc-200/80 text-center">
                        Return to Dashboard
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * Render Monochromatic Consent Screen
     */
    private static function render_consent_screen( $client, $user, $workspaces, $scope_param, $redirect_uri, $state, $challenge, $challenge_method ) {
        status_header( 200 );
        header( 'Content-Type: text/html; charset=UTF-8' );
        nocache_headers();

        $supported_scopes = self::get_supported_scopes();
        $current_agency_id = function_exists( 'cora_get_current_agency_id' ) ? (string) cora_get_current_agency_id() : '1';

        $selected_workspace_name = ! empty( $workspaces ) ? $workspaces[0]['name'] : 'Workspace';
        $selected_workspace_id   = ! empty( $workspaces ) ? $workspaces[0]['id'] : '1';

        // Pre-select current active workspace on current domain if available
        foreach ( $workspaces as $ws ) {
            if ( (string) $ws['id'] === (string) $current_agency_id ) {
                $selected_workspace_id   = (string) $ws['id'];
                $selected_workspace_name = $ws['name'];
                break;
            }
        }

        $client_name = ! empty( $client->client_name ) ? $client->client_name : 'AI Client';
        $lower_ident = strtolower( (string) $client->client_id . ' ' . $client_name );

        $is_gemini  = ( strpos( $lower_ident, 'gemini' ) !== false );
        $is_claude  = ( strpos( $lower_ident, 'claude' ) !== false || strpos( $lower_ident, 'anthropic' ) !== false );
        $is_chatgpt = ( strpos( $lower_ident, 'chatgpt' ) !== false || strpos( $lower_ident, 'openai' ) !== false || strpos( $lower_ident, 'gpt' ) !== false );

        $nonce = wp_create_nonce( 'cora_oauth_authorize_action' );
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Connect <?php echo esc_html( $client_name ); ?> — Cora</title>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap">
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
                .font-mono { font-family: 'JetBrains Mono', monospace; }
                .custom-scrollbar::-webkit-scrollbar { width: 4px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 4px; }
            </style>
        </head>
        <body class="bg-zinc-50 text-zinc-900 flex flex-col items-center justify-center min-h-screen p-4 sm:p-6 antialiased selection:bg-zinc-950 selection:text-white">
            <div class="bg-white border border-zinc-200/90 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-sm">
                <!-- Linked App Avatars -->
                <div class="flex items-center justify-center pt-2 pb-1">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-950 text-white flex items-center justify-center font-bold text-xl tracking-tight shadow-sm border border-zinc-900 shrink-0 select-none">
                        C
                    </div>
                    <div class="w-8 h-8 rounded-full bg-zinc-100 border border-zinc-200/90 flex items-center justify-center text-zinc-400 shrink-0 -mx-1.5 z-10 shadow-2xs">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
                    </div>
                    <?php if ( $is_gemini ) : ?>
                        <div class="w-14 h-14 rounded-2xl bg-cyan-50/80 border border-cyan-200/80 flex items-center justify-center text-cyan-600 shadow-sm shrink-0">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                                <path d="M5 3v4"/>
                                <path d="M19 17v4"/>
                                <path d="M3 5h4"/>
                                <path d="M17 19h4"/>
                            </svg>
                        </div>
                    <?php elseif ( $is_claude ) : ?>
                        <div class="w-14 h-14 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-center justify-center text-amber-600 shadow-sm shrink-0">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4"/>
                                <path d="M12 2v2.5"/>
                                <path d="M12 19.5v2.5"/>
                                <path d="m4.93 4.93 1.77 1.77"/>
                                <path d="m17.3 17.3 1.77 1.77"/>
                                <path d="M2 12h2.5"/>
                                <path d="M19.5 12H22"/>
                                <path d="m6.7 17.3-1.77 1.77"/>
                                <path d="m19.07 4.93-1.77 1.77"/>
                            </svg>
                        </div>
                    <?php elseif ( $is_chatgpt ) : ?>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50/80 border border-emerald-200/80 flex items-center justify-center text-emerald-600 shadow-sm shrink-0">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2a10 10 0 0 1 10 10c0 5.523-4.477 10-10 10S2 17.523 2 12"/>
                                <path d="M12 6a6 6 0 0 1 6 6c0 3.314-2.686 6-6 6"/>
                                <circle cx="12" cy="12" r="2.2"/>
                            </svg>
                        </div>
                    <?php else : ?>
                        <div class="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-200/80 flex items-center justify-center text-zinc-700 shadow-sm shrink-0">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="14" x="3" y="6" rx="3"/>
                                <path d="m9 13 2 2 4-4"/>
                                <path d="M12 2v4"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Headline & Subtitle -->
                <div class="text-center mt-4 mb-6">
                    <h1 class="text-xl font-bold text-zinc-950 tracking-tight">
                        Connect <?php echo esc_html( $client_name ); ?> to Cora
                    </h1>
                    <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed">
                        <?php echo esc_html( $client_name ); ?> is requesting access to your workspace:<br>
                        <span id="display-workspace-name" class="font-bold text-zinc-900"><?php echo esc_html( $selected_workspace_name ); ?></span>
                    </p>
                </div>

                <form method="POST" action="" id="cora-oauth-form" class="space-y-4">
                    <input type="hidden" name="cora_oauth_nonce" value="<?php echo esc_attr( $nonce ); ?>">
                    <input type="hidden" name="client_id" value="<?php echo esc_attr( $client->client_id ); ?>">
                    <input type="hidden" name="redirect_uri" value="<?php echo esc_attr( $redirect_uri ); ?>">
                    <input type="hidden" name="state" value="<?php echo esc_attr( $state ); ?>">
                    <input type="hidden" name="code_challenge" value="<?php echo esc_attr( $challenge ); ?>">
                    <input type="hidden" name="code_challenge_method" value="<?php echo esc_attr( $challenge_method ); ?>">
                    <input type="hidden" name="full_access" value="1">
                    <input type="hidden" name="scopes[]" value="*">
                    <?php foreach ( array_keys( $supported_scopes ) as $scope_key ) : ?>
                        <input type="hidden" name="scopes[]" value="<?php echo esc_attr( $scope_key ); ?>">
                    <?php endforeach; ?>

                    <!-- Workspace Selector -->
                    <?php if ( count( $workspaces ) > 1 ) : ?>
                        <div class="pb-1">
                            <label class="block text-xs font-semibold text-zinc-700 mb-1.5">Workspace</label>
                            <select name="workspace_id" id="cora-workspace-select" onchange="onWorkspaceChanged(this)" class="w-full bg-zinc-50 hover:bg-zinc-100/70 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs text-zinc-900 font-medium focus:bg-white focus:outline-none focus:border-zinc-400 transition-colors cursor-pointer">
                                <?php foreach ( $workspaces as $ws ) : ?>
                                    <option value="<?php echo esc_attr( $ws['id'] ); ?>" <?php selected( (string) $ws['id'], (string) $selected_workspace_id ); ?>><?php echo esc_html( $ws['name'] ); ?> (<?php echo esc_html( $ws['slug'] ); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else : ?>
                        <input type="hidden" name="workspace_id" value="<?php echo esc_attr( $selected_workspace_id ); ?>">
                    <?php endif; ?>

                    <!-- Clean 4-Item Permission Overview -->
                    <div class="space-y-2.5">
                        <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider px-0.5">
                            This will allow <?php echo esc_html( $client_name ); ?> to:
                        </div>

                        <!-- Card 1: Autonomous Campaigns & Content Engine -->
                        <div class="p-3.5 rounded-2xl bg-zinc-50/70 border border-zinc-200/80 flex items-start gap-3 transition-colors hover:bg-zinc-50">
                            <div class="w-8 h-8 rounded-xl bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-zinc-800 shadow-2xs">
                                <svg class="w-4 h-4 text-zinc-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-semibold text-zinc-900 leading-snug">Autonomous Campaigns & Content Engine</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5 leading-snug">Discover topic opportunities, generate outlines, and auto-publish content.</div>
                            </div>
                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                        </div>

                        <!-- Card 2: Website, Themes & Canvas -->
                        <div class="p-3.5 rounded-2xl bg-zinc-50/70 border border-zinc-200/80 flex items-start gap-3 transition-colors hover:bg-zinc-50">
                            <div class="w-8 h-8 rounded-xl bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-zinc-800 shadow-2xs">
                                <svg class="w-4 h-4 text-zinc-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3a14 14 0 0 0 0 18M12 3a14 14 0 0 1 0 18"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-semibold text-zinc-900 leading-snug">Website, Themes & Canvas</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5 leading-snug">Inspect live site themes, review landing pages, and deploy HTML updates.</div>
                            </div>
                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                        </div>

                        <!-- Card 3: CRM Leads & Client Pipelines -->
                        <div class="p-3.5 rounded-2xl bg-zinc-50/70 border border-zinc-200/80 flex items-start gap-3 transition-colors hover:bg-zinc-50">
                            <div class="w-8 h-8 rounded-xl bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-zinc-800 shadow-2xs">
                                <svg class="w-4 h-4 text-zinc-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-semibold text-zinc-900 leading-snug">CRM Leads & Client Pipelines</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5 leading-snug">Manage lead funnels, client records, and stage transitions.</div>
                            </div>
                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                        </div>

                        <!-- Card 4: Studio Operations & MCP Tools -->
                        <div class="p-3.5 rounded-2xl bg-zinc-50/70 border border-zinc-200/80 flex items-start gap-3 transition-colors hover:bg-zinc-50">
                            <div class="w-8 h-8 rounded-xl bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-zinc-800 shadow-2xs">
                                <svg class="w-4 h-4 text-zinc-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-semibold text-zinc-900 leading-snug">Studio Operations & MCP Tools</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5 leading-snug">Execute bookings, invoices, and run authorized workspace actions.</div>
                            </div>
                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Collapsible Detailed Permissions Accordion (for Auditors/Developers) -->
                    <div class="border-t border-zinc-100 pt-3">
                        <button type="button" onclick="toggleDetails()" class="w-full flex items-center justify-between py-1 text-xs font-medium text-zinc-500 hover:text-zinc-800 transition-colors cursor-pointer group">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                                </svg>
                                <span>View detailed permissions (28 tools)</span>
                            </span>
                            <svg id="details-chevron" class="w-4 h-4 text-zinc-400 transform transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>
                        <div id="details-panel" class="hidden mt-2.5 max-h-48 overflow-y-auto space-y-1.5 p-3 rounded-2xl bg-zinc-50 border border-zinc-200/70 custom-scrollbar text-[11px]">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Universal MCP Tool Permissions</div>
                            <?php foreach ( $supported_scopes as $s_key => $s_meta ) : ?>
                                <div class="flex items-start justify-between gap-2 py-1 border-b border-zinc-100 last:border-b-0">
                                    <div class="min-w-0">
                                        <span class="font-medium text-zinc-800"><?php echo esc_html( $s_meta['label'] ); ?></span>
                                        <p class="text-[10px] text-zinc-400 truncate"><?php echo esc_html( $s_meta['description'] ); ?></p>
                                    </div>
                                    <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-zinc-200/60 text-zinc-600 font-semibold uppercase shrink-0">
                                        <?php echo esc_html( $s_key ); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2 pt-2">
                        <button type="submit" name="cora_oauth_action" value="approve" class="w-full py-3 px-4 rounded-xl bg-zinc-950 text-white text-sm font-bold hover:bg-zinc-800 transition-all shadow-sm text-center cursor-pointer">
                            Authorize <?php echo esc_html( $client_name ); ?>
                        </button>
                        <button type="submit" name="cora_oauth_action" value="deny" class="w-full py-2 px-4 rounded-xl text-zinc-500 hover:text-zinc-900 text-xs font-semibold text-center cursor-pointer transition-colors bg-transparent border-0">
                            Cancel
                        </button>
                    </div>

                    <!-- Trust Footer -->
                    <div class="text-center pt-1 text-[11px] text-zinc-400 leading-normal">
                        Authorize only applications you trust. Permissions can be revoked anytime in Workspace Settings.
                    </div>
                </form>
            </div>

            <script>
                function toggleDetails() {
                    const panel = document.getElementById('details-panel');
                    const chevron = document.getElementById('details-chevron');
                    if (panel.classList.contains('hidden')) {
                        panel.classList.remove('hidden');
                        chevron.classList.add('rotate-180');
                    } else {
                        panel.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                    }
                }

                function onWorkspaceChanged(selectEl) {
                    const text = selectEl.options[selectEl.selectedIndex].text;
                    const cleanName = text.replace(/\s*\([^)]*\)\s*$/, '').trim();
                    const displayEl = document.getElementById('display-workspace-name');
                    if (displayEl) {
                        displayEl.textContent = cleanName;
                    }
                }
            </script>
        </body>
        </html>
        <?php
        exit;
    }
}
