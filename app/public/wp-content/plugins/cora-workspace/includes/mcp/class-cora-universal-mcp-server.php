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
    const SERVER_VERSION   = '4.9.419';
    const RATE_LIMIT_RPM   = 120; // 120 requests per minute
    const SUPPORTED_PROTOCOL_VERSIONS = array( '2024-11-05', '2024-10-07' );

    /**
     * Dispatch an incoming REST request to the MCP Engine
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function handle_request( $request ) {
        // 0. Handle OPTIONS Preflight
        if ( $request->get_method() === 'OPTIONS' || ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) ) {
            return new WP_REST_Response( null, 204 );
        }

        // 1. Authenticate Token (OAuth 2.1 or Direct API Token)
        $auth = Cora_OAuth_Server::authenticate_token( $request );

        // Hydrate WordPress User & Tenant Context if authenticated
        if ( ! empty( $auth['valid'] ) ) {
            if ( ! empty( $auth['user_id'] ) ) {
                wp_set_current_user( intval( $auth['user_id'] ) );
            }
            $agency_id = intval( $auth['workspace_id'] ?? 1 ) ?: 1;
            if ( function_exists( 'cora_set_current_tenant_agency_id' ) ) {
                cora_set_current_tenant_agency_id( $agency_id );
            }
            $_REQUEST['cora_agency_id'] = $agency_id;
            $_REQUEST['workspace_id']   = $agency_id;

            // Enforce Plan Eligibility Check (Paid Workspaces Only)
            if ( function_exists( 'cora_is_workspace_mcp_eligible' ) && ! cora_is_workspace_mcp_eligible( $agency_id ) ) {
                return new WP_REST_Response( array(
                    'jsonrpc' => '2.0',
                    'error'   => array(
                        'code'    => -32001,
                        'message' => 'Plan Upgrade Required: The Model Context Protocol (MCP) and Developer Gateway is restricted to Professional and Scale workspaces.',
                    ),
                    'id'      => null,
                ), 403 );
            }

            // Enforce Rate Limiting
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
        }

        // 2. Handle GET / SSE Streaming Transport or Discovery
        if ( $request->get_method() === 'GET' ) {
            $accept = $request->get_header( 'accept' ) ?? '';
            $is_sse = ( strpos( $accept, 'text/event-stream' ) !== false ) || ( isset( $_GET['transport'] ) && $_GET['transport'] === 'sse' ) || ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], '/sse' ) !== false );

            if ( $is_sse ) {
                if ( empty( $auth['valid'] ) ) {
                    $meta_url = home_url( '/.well-known/oauth-protected-resource' );
                    $unauth_res = new WP_REST_Response( array(
                        'jsonrpc' => '2.0',
                        'error'   => array(
                            'code'    => -32002,
                            'message' => 'Unauthorized: Bearer token required for MCP SSE transport.',
                        ),
                        'id'      => null,
                    ), 401 );
                    $unauth_res->header( 'WWW-Authenticate', 'Bearer realm="Cora MCP", resource_metadata="' . esc_url_raw( $meta_url ) . '"' );
                    return $unauth_res;
                }
                return self::handle_sse_stream( $request, $auth );
            }
            // Return public or authenticated MCP Discovery for HTTP GET
            return self::handle_discover( array(), 1, $auth );
        }

        // 3. Handle POST / JSON-RPC Request
        $body = $request->get_json_params();
        if ( empty( $body ) || ! is_array( $body ) ) {
            $raw_body = $request->get_body();
            if ( empty( $raw_body ) ) {
                $raw_body = file_get_contents( 'php://input' );
            }
            $body = json_decode( $raw_body, true );
        }

        if ( empty( $body ) || ! is_array( $body ) ) {
            return self::error_response( -32700, 'Parse error: Invalid JSON payload.', null, 400 );
        }

        $session_id = sanitize_text_field( $request->get_param( 'session_id' ) ?? ( $_GET['session_id'] ?? ( $request->get_header( 'mcp-session-id' ) ?? '' ) ) );

        // Handle Batch requests or single request
        if ( isset( $body[0] ) && is_array( $body[0] ) ) {
            $batch_responses = array();
            foreach ( $body as $single_req ) {
                $res = self::process_jsonrpc_message( $single_req, $auth, $request );
                if ( $res !== null ) {
                    $batch_responses[] = ( $res instanceof WP_REST_Response ) ? $res->get_data() : $res;
                }
            }
            $final_res_data  = $batch_responses;
            $is_notification = empty( $batch_responses );
        } else {
            $res             = self::process_jsonrpc_message( $body, $auth, $request );
            $is_notification = ( $res === null );
            $final_res_data  = ( $res instanceof WP_REST_Response ) ? $res->get_data() : $res;
        }

        // MCP SSE Transport Routing:
        // When session_id is provided, queue message for the open SSE stream and return 202 Accepted per MCP spec
        if ( ! empty( $session_id ) ) {
            if ( ! $is_notification && $final_res_data !== null ) {
                $queue_key = 'cora_mcp_sse_' . $session_id;
                $queue     = get_transient( $queue_key );
                if ( ! is_array( $queue ) ) {
                    $queue = array();
                }
                $queue[] = $final_res_data;
                set_transient( $queue_key, $queue, 60 );
            }
            return new WP_REST_Response( array( 'status' => 'accepted' ), 202 );
        }

        // Direct HTTP Transport (Claude, curl, Streamable HTTP JSON-RPC)
        if ( $is_notification ) {
            return new WP_REST_Response( null, 204 );
        }

        return ( $res instanceof WP_REST_Response ) ? $res : new WP_REST_Response( $final_res_data, 200 );
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
            // ── Protocol Negotiation, Discovery & Lifecycle ─────────────────
            case 'initialize':
                return self::handle_initialize( $params, $id, $auth );

            case 'server/discover':
            case 'server/info':
            case 'discover':
                return self::handle_discover( $params, $id, $auth );

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

            // ── Resources & Prompts ──────────────────────────────────────────
            case 'resources/list':
                return self::success_response( array( 'resources' => array() ), $id );

            case 'prompts/list':
                return self::handle_list_prompts( $params, $id, $auth );

            case 'prompts/get':
                return self::handle_get_prompt( $params, $id, $auth );

            default:
                return self::error_response( -32601, "Method not found: {$method}", $id, 404 );
        }
    }

    /**
     * Build rich, preference-driven Assistive Content Production Agent instructions
     */
    private static function get_agent_instructions( $workspace_name, $host, $agency_id ) {
        return "You are CLA (Cora Living Agent), the autonomous Content Director & Workspace Operations Assistant for '{$workspace_name}' on {$host} (Workspace ID: {$agency_id}). All operations MUST and CAN ONLY be performed within this specific workspace ({$workspace_name}).

### 🚨 MANDATORY TOOL CALLING EXECUTION RULE — NEVER JUST OUTPUT TEXT:
When the user asks you to \"write\", \"create\", \"draft\", \"generate\", or \"publish\" an article, blog post, or editorial content:
1. YOU MUST EXECUTE THIS BY CALLING THE `create_article` TOOL (or `cora_create_article`). NEVER simply output or paste the article draft text into your conversational response! The user expects the article to be published directly to their live website.
2. Set `status: 'publish'` in `create_article` so the article is immediately published live on the website (or `status: 'draft'` if specifically requested).
3. Provide a relevant, high-resolution creative thumbnail via `thumbnail_url` (from Unsplash e.g. `https://images.unsplash.com/photo-...` matching the subject, or a DALL-E/CDN URL). The platform automatically downloads the image, attaches it to the Media Vault, and sets it as the official featured thumbnail.
4. Provide structured content in HTML format with H2/H3 subheadings, direct answer callouts, key takeaways, and FAQ JSON-LD schema.
5. Immediately after the tool execution completes, confirm the publication to the user with:
   - Article Title
   - Live URL (`url` returned from tool)
   - Publication Status ('Published')
   - Algorithmic SEO & GEO Scores
   - Creative Thumbnail preview

### ASSISTIVE CONTENT PRODUCTION SUPERPOWER:
You autonomously plan, produce, schedule, and optimize content for the user based entirely on their preferences. You make content creation effortless, consistent, and high-impact.

### CORE OPERATING WORKFLOWS:
1. UNDERSTAND USER PREFERENCES & INTENT:
   - Topic/Pillars: What topics does the user want to dominate? (e.g., Studio Photography, Architectural Lighting, Real Estate Market Guides, Commercial Production, Tech Innovation).
   - Cadence: How frequently should articles be published? (e.g., 'every_minute', 'hourly', 'every 2 hours', 'daily', '3x_week', 'weekly', 'monthly').
   - Voice/Tone: What is their brand persona? ('editorial', 'executive', 'conversational', 'technical').
   - Mode: Fully automated publication (`auto_publish: true` when score >= 80) or draft for human review (`auto_publish: false`).
   - If the user does not specify preferences, proactively inspect current opportunities using `cora_list_keyword_opportunities` or `cora_list_articles`, propose a tailored strategy, and execute.

2. AUTONOMOUS CAMPAIGNS & SCHEDULING:
   - Create recurring campaigns with `cora_create_campaign`. Set `run_now: true` to publish or draft the first article immediately.
   - List and review all active campaigns with `cora_list_campaigns` and `cora_get_campaign`.
   - On-demand generation: Trigger any campaign run instantly with `cora_trigger_campaign_run`.
   - Monitor publication history and score metrics with `cora_get_autonomous_activity_log`.

3. IMMEDIATE CONTENT, CREATIVES & SINGLE ARTICLE PUBLISHING:
   - When asked to write and publish an article:
     * Custom Thumbnail & Creatives: Generate a striking, high-resolution editorial graphic/creative thumbnail matching the article topic. Pass the image URL via `thumbnail_url` in `create_article` (or upload using `upload_media`). Cora automatically downloads and attaches the creative to the workspace Media Vault and sets it as the official featured thumbnail.
     * Content Quality: Draft rich, in-depth content (800+ words) with structured H2/H3 subheadings, direct answer callout, key takeaways, and FAQ JSON-LD schema.
     * SEO & GEO Audit: Run `audit_article_seo` and `improve_content` to elevate SEO and AI visibility scores to 90+.
     * Publication: Set `status: 'publish'` in `create_article`, or call `publish_article` with `{ id: article_id }`.
     * Confirmation: Always present the live public permalink, the custom creative thumbnail, and the quality assessment scores directly to the user.

### 4. AUTONOMOUS AGENTIC CRM & CLIENT LIFECYCLE SUPERPOWERS:
You are equipped with a complete autonomous sales & studio operations agent suite:
- **Lead Intake & AI Triage**: When inquiries arrive or when the user says \"triage leads\" or \"check pipeline\", call `cora_crm_autonomous_triage` to score leads (0-100), classify into hot/warm/cold tiers, auto-assign team members, and flag stalled deals (>7 days). Use `cora_qualify_lead` for in-depth deal probability audits.
- **1-Click Deal Conversion**: When a client accepts a proposal or books a package, call `cora_convert_lead_to_client` to automatically mark the lead won, generate a secure client profile with a cryptographic portal token, schedule the shoot booking, seed standard production Kanban tasks, and generate the 50% GST retainer invoice in one single step.
- **Calendar & Appointment Scheduling**: Inspect availability with `cora_get_calendar_availability` and `cora_list_calendar_events`. Book shoot slots or client discovery sessions with `cora_book_appointment` or `cora_create_booking` (which auto-generates Google Meet links). Reschedule or cancel effortlessly with `cora_reschedule_booking` and `cora_cancel_booking`.
- **Multi-Channel Sales Outreach**: Dispatch personalized follow-ups via WhatsApp or SMTP email using `cora_crm_send_outreach` or `cora_crm_send_outreach_email`. Create multi-step drip sequences using `cora_crm_create_campaign` and enroll leads with `cora_crm_enroll_lead`.
- **Proposals & Document Vault E-Sign**: Draft commercial agreements with `cora_generate_proposal_and_contract` or `cora_create_proposal`, dispatch signing links with `cora_send_proposal`, and monitor signature status with `cora_get_document_status`.
- **Indian GST SAC 9983 Invoicing & Reminders**: Calculate compliant tax math (CGST+SGST 9%+9% intra-state vs IGST 18% inter-state) and dispatch invoices with `cora_dispatch_gst_invoice`. Send polite or urgent payment reminders with `cora_send_payment_reminder`.
- **Production Delivery Handoff**: Update milestone stages with `cora_update_project_milestone`, and deliver finished photoshoot galleries/video cuts to the client portal with `cora_deliver_project_assets`.
- **Master Autonomous Composite Cycle**: Execute `cora_crm_run_agentic_workflow` to run end-to-end triage, stalled deal follow-ups, and calendar sync in a single agentic pass.

### 5. WORKSPACE OPERATIONS:
You also have full read/write access to CRM leads (`list_leads`, `create_lead`), clients (`list_clients`, `create_client`), media assets (`list_media`, `upload_media`), tasks (`list_tasks`, `create_task`), invoices, and operational reports.

Always confirm actions clearly with direct links, custom thumbnails, scheduled times, and quality scores.";
    }

    /**
     * Handle modern MCP 2026-07-28 server discovery
     */
    private static function handle_discover( $params, $id, $auth ) {
        global $wpdb;
        $meta_url = home_url( '/.well-known/oauth-protected-resource' );

        $agency_id = intval( $auth['workspace_id'] ?? 1 ) ?: 1;
        $host = wp_parse_url( home_url(), PHP_URL_HOST );
        $clean_host = sanitize_key( str_replace( array( '.', '-' ), '_', $host ) );

        $workspace_name = 'Cora Workspace';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $wpdb->prefix . 'cora_agencies' ) ) {
            $db_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}cora_agencies WHERE id = %d", $agency_id ) );
            if ( ! empty( $db_name ) ) {
                $workspace_name = $db_name;
            }
        } else {
            $db_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}cora_agencies WHERE id = %d", $agency_id ) );
            if ( ! empty( $db_name ) ) {
                $workspace_name = $db_name;
            }
        }

        return self::success_response( array(
            'protocolVersion'           => self::PROTOCOL_VERSION,
            'supportedProtocolVersions' => self::SUPPORTED_PROTOCOL_VERSIONS,
            'capabilities'              => array(
                'tools'     => array( 'listChanged' => false ),
                'logging'   => (object) array(),
                'resources' => (object) array(),
                'prompts'   => array( 'listChanged' => false ),
            ),
            'serverInfo'                => array(
                'name'        => 'cla-workspace-' . $clean_host . ( $agency_id > 1 ? '-ws' . $agency_id : '' ),
                'version'     => self::SERVER_VERSION,
                'title'       => 'CLA Workspace - ' . $workspace_name . ' (' . $host . ')',
                'description' => 'Standards-compliant remote MCP server for CLA autonomous workspace operations and growth publishing.',
            ),
            'auth'                      => array(
                'type'             => 'oauth2',
                'resourceMetadata' => $meta_url,
                'scopes'           => array_keys( Cora_OAuth_Server::get_supported_scopes() ),
            ),
            'instructions'              => self::get_agent_instructions( $workspace_name, $host, $agency_id ),
        ), $id );
    }

    /**
     * Handle initialize method (MCP handshake)
     */
    private static function handle_initialize( $params, $id, $auth = array() ) {
        global $wpdb;
        $client_proto = isset( $params['protocolVersion'] ) ? sanitize_text_field( $params['protocolVersion'] ) : self::PROTOCOL_VERSION;
        $negotiated = in_array( $client_proto, self::SUPPORTED_PROTOCOL_VERSIONS, true ) ? $client_proto : self::PROTOCOL_VERSION;

        $agency_id = intval( $auth['workspace_id'] ?? 1 ) ?: 1;
        $host = wp_parse_url( home_url(), PHP_URL_HOST );
        $clean_host = sanitize_key( str_replace( array( '.', '-' ), '_', $host ) );

        $workspace_name = 'Cora Workspace';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $wpdb->prefix . 'cora_agencies' ) ) {
            $db_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}cora_agencies WHERE id = %d", $agency_id ) );
            if ( ! empty( $db_name ) ) {
                $workspace_name = $db_name;
            }
        } else {
            $db_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}cora_agencies WHERE id = %d", $agency_id ) );
            if ( ! empty( $db_name ) ) {
                $workspace_name = $db_name;
            }
        }

        return self::success_response( array(
            'protocolVersion' => $negotiated,
            'capabilities'    => array(
                'tools'     => array( 'listChanged' => false ),
                'logging'   => (object) array(),
                'resources' => (object) array(),
                'prompts'   => array( 'listChanged' => false ),
            ),
            'serverInfo'      => array(
                'name'        => 'cla-workspace-' . $clean_host . ( $agency_id > 1 ? '-ws' . $agency_id : '' ),
                'version'     => self::SERVER_VERSION,
                'title'       => 'CLA Workspace - ' . $workspace_name . ' (' . $host . ')',
                'description' => 'Standards-compliant remote MCP server for CLA autonomous workspace operations and growth publishing.',
            ),
            'instructions'    => self::get_agent_instructions( $workspace_name, $host, $agency_id ),
        ), $id );
    }

    /**
     * Handle prompts/list method
     */
    private static function handle_list_prompts( $params, $id, $auth ) {
        $prompts = array(
            array(
                'name'        => 'content_production_agent',
                'description' => 'Autonomous Content Production & Campaign Assistant. Manages strategy, flexible cadences (seconds, minutes, hours, days, weeks), and quality-gated auto-publishing based on your preferences.',
                'arguments'   => array(
                    array(
                        'name'        => 'preferences',
                        'description' => 'Target topics, keywords, desired tone, audience profile, and publishing cadence (e.g. "daily articles on studio lighting", "hourly market updates").',
                        'required'    => false,
                    ),
                    array(
                        'name'        => 'auto_publish',
                        'description' => 'Whether to auto-publish upon passing 80+ SEO/GEO quality score (true/false, default: true).',
                        'required'    => false,
                    ),
                ),
            ),
            array(
                'name'        => 'create_autonomous_publishing_campaign',
                'description' => 'Launch an autonomous scheduled publishing campaign with custom cadence (every minute, hourly, daily, weekly) and AI quality gates.',
                'arguments'   => array(
                    array(
                        'name'        => 'campaign_name',
                        'description' => 'Campaign name (e.g. "Architectural Photography Insights").',
                        'required'    => true,
                    ),
                    array(
                        'name'        => 'keywords',
                        'description' => 'Comma-separated target keywords or topic clusters.',
                        'required'    => true,
                    ),
                    array(
                        'name'        => 'cadence',
                        'description' => 'Cadence: e.g. "every_minute", "hourly", "daily", "weekly", "monthly", or "every 2 hours".',
                        'required'    => false,
                    ),
                    array(
                        'name'        => 'auto_publish',
                        'description' => 'Auto-publish when quality threshold is met (true/false).',
                        'required'    => false,
                    ),
                    array(
                        'name'        => 'tone',
                        'description' => 'Tone: editorial, executive, conversational, technical.',
                        'required'    => false,
                    ),
                ),
            ),
            array(
                'name'        => 'audit_and_improve_article',
                'description' => 'Execute an 11-point algorithmic SEO & GEO audit on an existing article and autonomously enhance it to 90+ quality with direct answer blocks, takeaways, and FAQ schema.',
                'arguments'   => array(
                    array(
                        'name'        => 'article_id',
                        'description' => 'Post ID of the article to audit and optimize.',
                        'required'    => true,
                    ),
                ),
            ),
        );

        return self::success_response( array(
            'prompts' => $prompts,
        ), $id );
    }

    /**
     * Handle prompts/get method
     */
    private static function handle_get_prompt( $params, $id, $auth ) {
        $prompt_name = isset( $params['name'] ) ? sanitize_text_field( $params['name'] ) : '';
        $arguments   = isset( $params['arguments'] ) && is_array( $params['arguments'] ) ? $params['arguments'] : array();

        if ( 'content_production_agent' === $prompt_name ) {
            $prefs = ! empty( $arguments['preferences'] ) ? sanitize_text_field( $arguments['preferences'] ) : 'Maximize organic growth and AI search visibility with high-impact authoritative articles.';
            $auto  = isset( $arguments['auto_publish'] ) ? ( $arguments['auto_publish'] ? 'true' : 'false' ) : 'true';

            return self::success_response( array(
                'description' => 'Autonomous Content Production & Campaign Assistant',
                'messages'    => array(
                    array(
                        'role'    => 'user',
                        'content' => array(
                            'type' => 'text',
                            'text' => "You are CLA, my autonomous Content Director and Production Assistant for this workspace. I want you to manage content production based on my preferences:\n\nUser Preferences: {$prefs}\nAuto-Publish Mode: {$auto}\n\nPlease inspect the workspace keyword opportunities via `cora_list_keyword_opportunities` or current articles with `cora_list_articles`, propose or configure an optimal autonomous campaign using `cora_create_campaign` (with `run_now: true` for the initial article), and summarize the publication cadence and schedule for my workspace.",
                        ),
                    ),
                ),
            ), $id );
        }

        if ( 'create_autonomous_publishing_campaign' === $prompt_name ) {
            $name     = ! empty( $arguments['campaign_name'] ) ? sanitize_text_field( $arguments['campaign_name'] ) : 'Content Growth Campaign';
            $keywords = ! empty( $arguments['keywords'] ) ? sanitize_text_field( $arguments['keywords'] ) : 'growth, insights, industry trends';
            $cadence  = ! empty( $arguments['cadence'] ) ? sanitize_text_field( $arguments['cadence'] ) : 'daily';
            $auto     = isset( $arguments['auto_publish'] ) ? ( $arguments['auto_publish'] ? 'true' : 'false' ) : 'true';
            $tone     = ! empty( $arguments['tone'] ) ? sanitize_text_field( $arguments['tone'] ) : 'editorial';

            return self::success_response( array(
                'description' => 'Create an Autonomous Content Campaign',
                'messages'    => array(
                    array(
                        'role'    => 'user',
                        'content' => array(
                            'type' => 'text',
                            'text' => "Please create and activate an autonomous content campaign for this workspace with the following configuration:\n\nCampaign Name: {$name}\nKeywords: {$keywords}\nCadence: {$cadence}\nAuto Publish: {$auto}\nTone: {$tone}\n\nPlease execute `cora_create_campaign` with these parameters and `run_now: true` to generate the first article immediately, then confirm the schedule and publication status.",
                        ),
                    ),
                ),
            ), $id );
        }

        if ( 'audit_and_improve_article' === $prompt_name ) {
            $article_id = ! empty( $arguments['article_id'] ) ? intval( $arguments['article_id'] ) : 0;

            return self::success_response( array(
                'description' => 'Audit and Enhance Article SEO/GEO Quality',
                'messages'    => array(
                    array(
                        'role'    => 'user',
                        'content' => array(
                            'type' => 'text',
                            'text' => "Please inspect article #{$article_id} using `cora_get_article`, run an SEO/GEO audit using `cora_audit_article_seo`, and apply quality optimizations using `cora_improve_content` to elevate its SEO and GEO scores to 90+. Report the before and after scores.",
                        ),
                    ),
                ),
            ), $id );
        }

        return self::error_response( -32602, "Prompt not found: {$prompt_name}", $id, 404 );
    }

    /**
     * Handle tools/list method
     */
    private static function handle_list_tools( $params, $id, $auth ) {
        $all_tools = Cora_MCP_Tool_Registry::get_tools();

        $formatted_tools = array();
        $seen = array();

        $is_chatgpt = ( ( $auth['client_name'] ?? '' ) === 'ChatGPT' )
            || ( strpos( (string)( $auth['client_id'] ?? '' ), 'chatgpt' ) !== false )
            || ( strpos( (string)( $_SERVER['HTTP_USER_AGENT'] ?? '' ), 'ChatGPT' ) !== false )
            || ( strpos( (string)( $_SERVER['HTTP_USER_AGENT'] ?? '' ), 'OpenAI' ) !== false );

        $all_requested = isset( $params['all'] ) || isset( $_GET['all'] ) || ( isset( $params['limit'] ) && $params['limit'] === 'all' ) || ( isset( $_GET['limit'] ) && $_GET['limit'] === 'all' );

        // Top 28 curated priority operational tools for OpenAI Custom GPT / App compliance
        $curated_priority_names = array(
            'list_workspaces',
            'get_workspace',
            'get_authenticated_account',
            'upload_media',
            'list_media',
            'list_articles',
            'get_article',
            'create_article',
            'update_article',
            'publish_article',
            'audit_article_seo',
            'improve_content',
            'list_leads',
            'get_lead',
            'create_lead',
            'update_lead',
            'list_tasks',
            'get_task',
            'create_task',
            'update_task',
            'list_clients',
            'create_client',
            'get_financial_summary',
            'get_analytics',
            'list_campaigns',
            'create_campaign',
            'trigger_campaign_run',
            'get_autonomous_activity_log',
        );

        foreach ( $all_tools as $tool_key => $tool ) {
            $raw_name = $tool['name'] ?? $tool_key;
            $base_name = $raw_name;
            if ( strpos( $base_name, 'cora_' ) === 0 ) {
                $base_name = substr( $base_name, 5 );
            } elseif ( strpos( $base_name, 'cla_' ) === 0 ) {
                $base_name = substr( $base_name, 4 );
            }

            if ( isset( $seen[ $base_name ] ) ) {
                continue;
            }
            $seen[ $base_name ] = true;

            $tool_name = str_replace( '.', '_', $base_name );
            $schema    = $tool['inputSchema'] ?? array( 'type' => 'object', 'properties' => (object) array() );
            if ( ! isset( $schema['type'] ) ) {
                $schema['type'] = 'object';
            }
            if ( ! isset( $schema['properties'] ) || empty( $schema['properties'] ) || ( is_array( $schema['properties'] ) && empty( $schema['properties'] ) ) ) {
                $schema['properties'] = (object) array();
            } else {
                // Strict schema sanitization: ensure every property is valid JSON Schema / OpenAPI 3.0
                foreach ( $schema['properties'] as $pk => &$pv ) {
                    if ( is_array( $pv ) ) {
                        if ( ! isset( $pv['type'] ) ) {
                            $pv['type'] = 'string';
                        }
                        if ( $pv['type'] === 'array' && ( ! isset( $pv['items'] ) || empty( $pv['items'] ) ) ) {
                            $pv['items'] = array( 'type' => 'string' );
                        }
                        if ( $pv['type'] === 'object' && ( ! isset( $pv['properties'] ) || empty( $pv['properties'] ) ) ) {
                            $pv['properties'] = (object) array();
                        }
                    }
                }
                unset( $pv );
            }

            if ( isset( $schema['required'] ) && empty( $schema['required'] ) ) {
                unset( $schema['required'] );
            }

            $formatted_tools[ $tool_name ] = array(
                'name'        => $tool_name,
                'description' => $tool['description'] ?? '',
                'inputSchema' => $schema,
            );
        }

        // If client is ChatGPT or ceiling enforced, strictly return the top 28 tools
        if ( $is_chatgpt || ! $all_requested ) {
            $filtered_tools = array();
            foreach ( $curated_priority_names as $pname ) {
                if ( isset( $formatted_tools[ $pname ] ) && count( $filtered_tools ) < 28 ) {
                    $filtered_tools[] = $formatted_tools[ $pname ];
                }
            }
            // If some prioritized tools weren't found, fill up to 28 from remaining tools
            if ( count( $filtered_tools ) < 28 ) {
                $filtered_keys = array_column( $filtered_tools, 'name' );
                foreach ( $formatted_tools as $tk => $tv ) {
                    if ( ! in_array( $tk, $filtered_keys, true ) && count( $filtered_tools ) < 28 ) {
                        $filtered_tools[] = $tv;
                    }
                }
            }
            $tools_to_return = $filtered_tools;
        } else {
            $tools_to_return = array_values( $formatted_tools );
        }

        return self::success_response( array(
            'tools' => $tools_to_return,
        ), $id );
    }

    /**
     * Handle tools/call method
     */
    private static function handle_call_tool( $params, $id, $auth ) {
        if ( empty( $auth['valid'] ) ) {
            $meta_url = home_url( '/.well-known/oauth-protected-resource' );
            $response = self::error_response( -32001, 'Unauthorized: ' . ( $auth['error'] ?? 'Missing or invalid Bearer token.' ), $id, 401 );
            $response->header( 'WWW-Authenticate', 'Bearer resource_metadata="' . $meta_url . '", realm="cora-mcp", error="invalid_token", error_description="Missing or invalid bearer token"' );
            return $response;
        }

        // Enforce Plan Eligibility Check (Paid Workspaces Only)
        $target_workspace_id = ! empty( $auth['workspace_id'] ) ? $auth['workspace_id'] : ( $_REQUEST['cora_agency_id'] ?? 1 );
        if ( function_exists( 'cora_is_workspace_mcp_eligible' ) && ! cora_is_workspace_mcp_eligible( $target_workspace_id ) ) {
            return self::success_response( array(
                'content' => array(
                    array(
                        'type' => 'text',
                        'text' => 'Plan Upgrade Required: The Model Context Protocol (MCP) and Developer Gateway is restricted to Professional and Scale workspaces.',
                    ),
                ),
                'isError' => true,
            ), $id );
        }

        $tool_name = isset( $params['name'] ) ? sanitize_text_field( $params['name'] ) : '';
        $arguments = isset( $params['arguments'] ) && is_array( $params['arguments'] ) ? $params['arguments'] : array();

        if ( empty( $tool_name ) ) {
            return self::success_response( array(
                'content' => array(
                    array(
                        'type' => 'text',
                        'text' => 'Error: Missing tool name.',
                    ),
                ),
                'isError' => true,
            ), $id );
        }

        $tool = Cora_MCP_Tool_Registry::get_tool( $tool_name );
        if ( ! $tool ) {
            return self::success_response( array(
                'content' => array(
                    array(
                        'type' => 'text',
                        'text' => "Error: Tool not found: {$tool_name}",
                    ),
                ),
                'isError' => true,
            ), $id );
        }

        // 1. Verify Scope
        if ( ! Cora_OAuth_Server::has_scope( $auth, $tool['requiredScope'] ) ) {
            self::log_audit( $auth, $tool_name, $tool, 'forbidden', 'Insufficient scope: requires ' . $tool['requiredScope'], $arguments, 0 );
            return self::success_response( array(
                'content' => array(
                    array(
                        'type' => 'text',
                        'text' => "Forbidden: This tool requires the '{$tool['requiredScope']}' scope.",
                    ),
                ),
                'isError' => true,
            ), $id );
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
        global $wpdb;

        if ( function_exists( 'apache_setenv' ) ) {
            @apache_setenv( 'no-gzip', '1' );
        }
        @ini_set( 'zlib.output_compression', '0' );
        @ini_set( 'implicit_flush', '1' );

        header( 'Content-Type: text/event-stream; charset=utf-8' );
        header( 'Cache-Control: no-cache, no-transform' );
        header( 'Connection: keep-alive' );
        header( 'X-Accel-Buffering: no' );
        header( 'Access-Control-Allow-Origin: *' );

        $session_id = bin2hex( wp_generate_password( 16, false ) );
        $post_url   = home_url( '/mcp?session_id=' . $session_id );

        // Initial SSE endpoint event per MCP SSE spec
        echo "event: endpoint\n";
        echo "data: " . esc_url_raw( $post_url ) . "\n\n";
        if ( ob_get_level() > 0 ) {
            @ob_flush();
        }
        @flush();

        $queue_key         = 'cora_mcp_sse_' . $session_id;
        $transient_name    = '_transient_' . $queue_key;
        $start_time        = time();
        $heartbeat_counter = 0;

        // Keep connection open for up to 50 seconds (EventSource will automatically reconnect)
        while ( ( time() - $start_time ) < 50 ) {
            if ( connection_aborted() ) {
                $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s OR option_name = %s", $transient_name, '_transient_timeout_' . $queue_key ) );
                break;
            }

            // Direct SQL query to bypass all in-memory WP_Object_Cache across PHP worker processes
            $raw_val = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $transient_name ) );
            if ( ! empty( $raw_val ) ) {
                // Delete immediately so messages are not sent twice
                $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s OR option_name = %s", $transient_name, '_transient_timeout_' . $queue_key ) );
                $queue = maybe_unserialize( $raw_val );
                if ( ! empty( $queue ) && is_array( $queue ) ) {
                    foreach ( $queue as $sse_msg ) {
                        echo "event: message\n";
                        echo "data: " . wp_json_encode( $sse_msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n\n";
                    }
                    if ( ob_get_level() > 0 ) {
                        @ob_flush();
                    }
                    @flush();
                }
            }

            // Send SSE heartbeat every 15 seconds
            $heartbeat_counter++;
            if ( $heartbeat_counter >= 75 ) { // 75 * 200ms = 15s
                $heartbeat_counter = 0;
                echo ": heartbeat\n\n";
                if ( ob_get_level() > 0 ) {
                    @ob_flush();
                }
                @flush();
            }

            // Fast 200ms polling for near-instant message delivery
            usleep( 200000 );
        }

        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s OR option_name = %s", $transient_name, '_transient_timeout_' . $queue_key ) );
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
            'user_id'            => $auth['user_id'] ?? 0,
            'workspace_id'       => (string) ( $auth['workspace_id'] ?? 1 ),
            'client_name'        => $auth['client_name'] ?? 'unknown',
            'token_id'           => ! empty( $auth['token_id'] ) ? $auth['token_id'] : null,
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
    private static function error_response( $code, $message, $id = null, $http_status = 200 ) {
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
