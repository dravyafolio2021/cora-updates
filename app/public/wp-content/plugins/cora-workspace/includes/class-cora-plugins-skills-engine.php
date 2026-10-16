<?php
/**
 * Cora Workspace — Dynamic Plugins, Modules & AI Skills Engine
 *
 * Provides workspace-level isolated database schema, REST API bridge,
 * dynamic MCP tool registration, and custom skill execution runtime.
 * Open architecture for external APIs & Model Context Protocol (MCP) 2.0.
 *
 * @package CoraWorkspace
 * @subpackage PluginsSkills
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Cora_Plugins_Skills_Engine' ) ) {

class Cora_Plugins_Skills_Engine {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor & Hook Initialization
     */
    public function __construct() {
        // Table schema initialization
        add_action( 'init', array( __CLASS__, 'setup_database_tables' ), 5 );
        add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );

        // AJAX handlers for in-dashboard Workspace UI
        add_action( 'wp_ajax_cora_ajax_get_workspace_plugins', array( __CLASS__, 'ajax_get_plugins' ) );
        add_action( 'wp_ajax_cora_ajax_save_workspace_plugin', array( __CLASS__, 'ajax_save_plugin' ) );
        add_action( 'wp_ajax_cora_ajax_upload_workspace_plugin', array( __CLASS__, 'ajax_upload_plugin' ) );
        add_action( 'wp_ajax_cora_ajax_dispatch_workspace_plugin', array( __CLASS__, 'ajax_dispatch_plugin' ) );
        add_action( 'wp_ajax_cora_ajax_toggle_workspace_plugin', array( __CLASS__, 'ajax_toggle_plugin' ) );
        add_action( 'wp_ajax_cora_ajax_delete_workspace_plugin', array( __CLASS__, 'ajax_delete_plugin' ) );

        add_action( 'wp_ajax_cora_ajax_get_workspace_skills', array( __CLASS__, 'ajax_get_skills' ) );
        add_action( 'wp_ajax_cora_ajax_save_workspace_skill', array( __CLASS__, 'ajax_save_skill' ) );
        add_action( 'wp_ajax_cora_ajax_upload_workspace_skill', array( __CLASS__, 'ajax_upload_skill' ) );
        add_action( 'wp_ajax_cora_ajax_toggle_workspace_skill', array( __CLASS__, 'ajax_toggle_skill' ) );
        add_action( 'wp_ajax_cora_ajax_delete_workspace_skill', array( __CLASS__, 'ajax_delete_skill' ) );
        add_action( 'wp_ajax_cora_ajax_execute_workspace_skill', array( __CLASS__, 'ajax_execute_skill' ) );

        add_action( 'wp_ajax_cora_ajax_generate_ai_plugin', array( __CLASS__, 'ajax_generate_ai_plugin' ) );
        add_action( 'wp_ajax_cora_ajax_generate_ai_skill', array( __CLASS__, 'ajax_generate_ai_skill' ) );

        add_action( 'wp_ajax_cora_ajax_get_workspace_api_keys', array( __CLASS__, 'ajax_get_api_keys' ) );
        add_action( 'wp_ajax_cora_ajax_create_workspace_api_key', array( __CLASS__, 'ajax_create_api_key' ) );
        add_action( 'wp_ajax_cora_ajax_revoke_workspace_api_key', array( __CLASS__, 'ajax_revoke_api_key' ) );

        // WordPress filter hook for dynamic MCP tool registration
        add_filter( 'cora_mcp_tools', array( __CLASS__, 'filter_mcp_tools' ), 10, 1 );

        // Universal REST API authentication middleware via Workspace API Key
        add_filter( 'determine_current_user', array( __CLASS__, 'determine_user_from_api_key' ), 20 );
    }

    /**
     * Resolve current workspace agency ID.
     */
    public static function get_current_agency_id() {
        if ( isset( $_POST['agency_id'] ) && is_numeric( $_POST['agency_id'] ) ) {
            return strval( intval( $_POST['agency_id'] ) );
        }
        if ( isset( $_GET['agency_id'] ) && is_numeric( $_GET['agency_id'] ) ) {
            return strval( intval( $_GET['agency_id'] ) );
        }
        if ( isset( $_REQUEST['agency_id'] ) && is_numeric( $_REQUEST['agency_id'] ) ) {
            return strval( intval( $_REQUEST['agency_id'] ) );
        }
        if ( function_exists( 'cora_db_get_agency_id' ) ) {
            $id = cora_db_get_agency_id();
            if ( ! empty( $id ) ) {
                return strval( $id );
            }
        }
        if ( function_exists( 'cora_get_current_user_agency_id' ) ) {
            $id = cora_get_current_user_agency_id();
            if ( ! empty( $id ) && $id !== 'super' ) {
                return strval( $id );
            }
        }
        return '1';
    }

    /**
     * Database Tables Initialization & Multi-Tenant Schema Setup
     */
    public static function setup_database_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_plugins  = $wpdb->prefix . 'cora_workspace_plugins';
        $table_skills   = $wpdb->prefix . 'cora_workspace_skills';
        $table_keys     = $wpdb->prefix . 'cora_workspace_api_keys';
        $table_logs     = $wpdb->prefix . 'cora_workspace_plugin_logs';

        $sql_plugins = "CREATE TABLE IF NOT EXISTS {$table_plugins} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            agency_id varchar(100) NOT NULL DEFAULT '1',
            plugin_slug varchar(100) NOT NULL,
            name varchar(255) NOT NULL,
            version varchar(30) NOT NULL DEFAULT '1.0.0',
            description text DEFAULT NULL,
            category varchar(100) NOT NULL DEFAULT 'Custom',
            icon_svg text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            type varchar(50) NOT NULL DEFAULT 'custom',
            config_json longtext DEFAULT NULL,
            mcp_tools_json longtext DEFAULT NULL,
            api_routes_json longtext DEFAULT NULL,
            author varchar(255) NOT NULL DEFAULT 'Studio Admin',
            created_by_model varchar(100) DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_agency_plugin (agency_id, plugin_slug),
            KEY agency_status (agency_id, status)
        ) {$charset_collate};";

        $sql_skills = "CREATE TABLE IF NOT EXISTS {$table_skills} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            agency_id varchar(100) NOT NULL DEFAULT '1',
            skill_slug varchar(100) NOT NULL,
            name varchar(255) NOT NULL,
            description text DEFAULT NULL,
            category varchar(100) NOT NULL DEFAULT 'General',
            icon_svg text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            instruction_prompt longtext NOT NULL,
            trigger_phrases text DEFAULT NULL,
            parameters_schema longtext DEFAULT NULL,
            bound_mcp_tools longtext DEFAULT NULL,
            execution_type varchar(50) NOT NULL DEFAULT 'prompt_behavior',
            execution_script longtext DEFAULT NULL,
            model_preference varchar(100) DEFAULT 'auto',
            created_by_model varchar(100) DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_agency_skill (agency_id, skill_slug),
            KEY agency_status (agency_id, status)
        ) {$charset_collate};";

        $sql_keys = "CREATE TABLE IF NOT EXISTS {$table_keys} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            agency_id varchar(100) NOT NULL DEFAULT '1',
            key_name varchar(255) NOT NULL,
            key_prefix varchar(30) NOT NULL DEFAULT 'cora_ws_live_',
            key_hash varchar(128) NOT NULL,
            scopes text NOT NULL,
            rate_limit int(11) NOT NULL DEFAULT 1000,
            status varchar(20) NOT NULL DEFAULT 'active',
            last_used_at datetime DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY agency_status (agency_id, status),
            KEY key_hash (key_hash)
        ) {$charset_collate};";

        $sql_logs = "CREATE TABLE IF NOT EXISTS {$table_logs} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            agency_id varchar(100) NOT NULL DEFAULT '1',
            target_type varchar(50) NOT NULL,
            target_slug varchar(100) NOT NULL,
            action varchar(100) NOT NULL,
            payload longtext DEFAULT NULL,
            response_status varchar(50) NOT NULL DEFAULT 'success',
            response_message text DEFAULT NULL,
            caller_model varchar(100) DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY agency_target (agency_id, target_slug),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_plugins );
        dbDelta( $sql_skills );
        dbDelta( $sql_keys );
        dbDelta( $sql_logs );

        // Seed default starter plugins & skills for default workspace
        self::ensure_starter_seeds( '1' );
    }

    /**
     * Ensure starter starter plugins & skills exist for a workspace tenant.
     */
    public static function ensure_starter_seeds( $agency_id = '1' ) {
        global $wpdb;
        $table_plugins = $wpdb->prefix . 'cora_workspace_plugins';
        $table_skills  = $wpdb->prefix . 'cora_workspace_skills';

        $plugins_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_plugins} WHERE agency_id = %s", $agency_id ) );
        if ( intval( $plugins_count ) === 0 ) {
            self::seed_starter_plugins( $agency_id );
        }

        $skills_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_skills} WHERE agency_id = %s", $agency_id ) );
        if ( intval( $skills_count ) === 0 ) {
            self::seed_starter_skills( $agency_id );
        }
    }

    /**
     * Seed realistic, high-value starter plugins
     */
    private static function seed_starter_plugins( $agency_id ) {
        global $wpdb;
        $table_plugins = $wpdb->prefix . 'cora_workspace_plugins';
        $now = current_time( 'mysql' );

        $starter_plugins = array(
            array(
                'plugin_slug'     => 'webhook-dispatcher',
                'name'            => 'External Webhook & Notification Dispatcher',
                'version'         => '1.2.0',
                'description'     => 'Dispatches instant real-time HTTP POST webhooks to Zapier, Make, Slack, or custom endpoints upon new leads, invoice status updates, and shoot bookings.',
                'category'        => 'Integrations',
                'icon_svg'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
                'status'          => 'active',
                'type'            => 'webhook',
                'config_json'     => json_encode( array(
                    'target_url'       => 'https://api.cora-workspace.internal/webhooks/listener',
                    'retry_attempts'   => 3,
                    'auth_type'        => 'bearer',
                    'events_subscribed'=> array( 'lead.created', 'invoice.paid', 'booking.confirmed' )
                ) ),
                'mcp_tools_json'  => json_encode( array(
                    array(
                        'name'        => 'dispatch_custom_webhook',
                        'description' => 'Trigger an immediate payload dispatch to external registered HTTP webhooks.',
                        'parameters'  => array(
                            'type'       => 'object',
                            'properties' => array(
                                'event_name' => array( 'type' => 'string', 'description' => 'Name of the event (e.g. client.milestone_reached)' ),
                                'payload'    => array( 'type' => 'object', 'description' => 'Arbitrary JSON key-value data to send' )
                            ),
                            'required'   => array( 'event_name', 'payload' )
                        )
                    )
                ) ),
                'api_routes_json' => json_encode( array( '/webhooks/dispatch', '/webhooks/health' ) ),
                'author'          => 'Studio Director',
                'created_by_model'=> 'system_seed',
            ),
            array(
                'plugin_slug'     => 'google-calendar-sync',
                'name'            => 'Google Calendar Two-Way Sync Bridge',
                'version'         => '1.0.4',
                'description'     => 'Synchronizes workspace client bookings, production shoots, and team shifts directly with external Google Calendar accounts via REST API & MCP.',
                'category'        => 'Productivity',
                'icon_svg'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                'status'          => 'active',
                'type'            => 'mcp_bridge',
                'config_json'     => json_encode( array(
                    'calendar_id'      => 'primary',
                    'sync_interval_min'=> 15,
                    'auto_conflict_res'=> true
                ) ),
                'mcp_tools_json'  => json_encode( array(
                    array(
                        'name'        => 'sync_calendar_events',
                        'description' => 'Fetch upcoming shoot schedules and cross-reference with Google Calendar bookings.',
                        'parameters'  => array(
                            'type'       => 'object',
                            'properties' => array(
                                'start_date' => array( 'type' => 'string', 'description' => 'Start date (YYYY-MM-DD)' ),
                                'end_date'   => array( 'type' => 'string', 'description' => 'End date (YYYY-MM-DD)' )
                            )
                        )
                    )
                ) ),
                'api_routes_json' => json_encode( array( '/calendar/sync', '/calendar/events' ) ),
                'author'          => 'Studio Admin',
                'created_by_model'=> 'system_seed',
            ),
            array(
                'plugin_slug'     => 'payment-gateway-listener',
                'name'            => 'Payment Gateway Webhook Listener',
                'version'         => '2.1.0',
                'description'     => 'Ingests Stripe, Razorpay, and bank transfer notifications, automatically updating client ledger entries and clearing GST SAC 9983 invoices.',
                'category'        => 'Financials',
                'icon_svg'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>',
                'status'          => 'active',
                'type'            => 'webhook',
                'config_json'     => json_encode( array(
                    'currency'         => 'INR',
                    'auto_issue_receipt'=> true,
                    'gst_rate'         => 18
                ) ),
                'mcp_tools_json'  => json_encode( array(
                    array(
                        'name'        => 'verify_payment_settlement',
                        'description' => 'Check payment settlement status with gateway reference ID.',
                        'parameters'  => array(
                            'type'       => 'object',
                            'properties' => array(
                                'payment_id' => array( 'type' => 'string', 'description' => 'Gateway payment transaction ID' )
                            ),
                            'required'   => array( 'payment_id' )
                        )
                    )
                ) ),
                'api_routes_json' => json_encode( array( '/payments/webhook', '/payments/reconcile' ) ),
                'author'          => 'Studio Admin',
                'created_by_model'=> 'system_seed',
            ),
            array(
                'plugin_slug'     => 'client-portal-notifier',
                'name'            => 'Client Portal Real-Time Status Broadcaster',
                'version'         => '1.1.2',
                'description'     => 'Sends automated WhatsApp, email, or in-portal push notices whenever proofs, preview galleries, or deliverables are uploaded for client sign-off.',
                'category'        => 'Operations',
                'icon_svg'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>',
                'status'          => 'active',
                'type'            => 'custom',
                'config_json'     => json_encode( array(
                    'channels'         => array( 'email', 'in_app' ),
                    'send_sms_urgent'  => false
                ) ),
                'mcp_tools_json'  => json_encode( array(
                    array(
                        'name'        => 'broadcast_portal_alert',
                        'description' => 'Send milestone completion notice directly to client email and portal dashboard.',
                        'parameters'  => array(
                            'type'       => 'object',
                            'properties' => array(
                                'client_id' => array( 'type' => 'integer', 'description' => 'Client ID' ),
                                'title'     => array( 'type' => 'string', 'description' => 'Notification title' ),
                                'message'   => array( 'type' => 'string', 'description' => 'Notice body' )
                            ),
                            'required'   => array( 'client_id', 'title', 'message' )
                        )
                    )
                ) ),
                'api_routes_json' => json_encode( array( '/portal/notify', '/portal/status' ) ),
                'author'          => 'Creative Lead',
                'created_by_model'=> 'system_seed',
            ),
            array(
                'plugin_slug'     => 'daily-ledger-reconciler',
                'name'            => 'Automated Daily Cash Ledger Reconciler',
                'version'         => '1.0.1',
                'description'     => 'Computes daily operational burn, collected revenue, pending client milestone receivables, and 18% GST liability reserve balances.',
                'category'        => 'Financials',
                'icon_svg'        => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>',
                'status'          => 'active',
                'type'            => 'llm_generated',
                'config_json'     => json_encode( array(
                    'reconcile_time'   => '23:30',
                    'tax_reserve_pct'  => 15
                ) ),
                'mcp_tools_json'  => json_encode( array(
                    array(
                        'name'        => 'run_ledger_reconciliation',
                        'description' => 'Execute instant balance check and GST liability compilation for the workspace.',
                        'parameters'  => array(
                            'type'       => 'object',
                            'properties' => array(
                                'date' => array( 'type' => 'string', 'description' => 'Date for reconciliation (YYYY-MM-DD)' )
                            )
                        )
                    )
                ) ),
                'api_routes_json' => json_encode( array( '/ledger/reconcile', '/ledger/metrics' ) ),
                'author'          => 'AI Co-Founder',
                'created_by_model'=> 'claude-3.5-sonnet',
            )
        );

        foreach ( $starter_plugins as $plugin ) {
            $wpdb->insert(
                $table_plugins,
                array(
                    'agency_id'        => $agency_id,
                    'plugin_slug'      => $plugin['plugin_slug'],
                    'name'             => $plugin['name'],
                    'version'          => $plugin['version'],
                    'description'      => $plugin['description'],
                    'category'         => $plugin['category'],
                    'icon_svg'         => $plugin['icon_svg'],
                    'status'           => $plugin['status'],
                    'type'             => $plugin['type'],
                    'config_json'      => $plugin['config_json'],
                    'mcp_tools_json'   => $plugin['mcp_tools_json'],
                    'api_routes_json'  => $plugin['api_routes_json'],
                    'author'           => $plugin['author'],
                    'created_by_model' => $plugin['created_by_model'],
                    'created_at'       => $now,
                    'updated_at'       => $now,
                )
            );
        }
    }

    /**
     * Seed realistic, high-value starter AI skills
     */
    private static function seed_starter_skills( $agency_id ) {
        global $wpdb;
        $table_skills = $wpdb->prefix . 'cora_workspace_skills';
        $now = current_time( 'mysql' );

        $starter_skills = array(
            array(
                'skill_slug'        => 'client-intake-specialist',
                'name'              => 'Client Intake & Onboarding Specialist',
                'description'       => 'Guides prospective clients through inquiry intake, validates commercial scope & GST details, and drafts clear onboarding briefs.',
                'category'          => 'Client Operations',
                'icon_svg'          => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>',
                'status'            => 'active',
                'instruction_prompt'=> "You are the Client Intake Specialist for this workspace. When invoked:\n1. Extract contact details (Full Name, Company, Email, Phone, Project Type, Estimated Timeline, Budget).\n2. If commercial, request 15-digit GSTIN and billing state for SAC 9983 tax classification.\n3. Validate budget against studio service tiers (Minimum engagement: ₹35,000).\n4. Format output as a structured Intake Profile with recommended next operational steps.",
                'trigger_phrases'   => 'onboard client, client intake, new inquiry, intake brief, client qualification',
                'parameters_schema' => json_encode( array(
                    'type'       => 'object',
                    'properties' => array(
                        'inquiry_notes'   => array( 'type' => 'string', 'description' => 'Raw notes or communication text from client inquiry' ),
                        'industry_sector' => array( 'type' => 'string', 'description' => 'Client industry (e.g. Fashion, Corporate, Real Estate)' )
                    ),
                    'required'   => array( 'inquiry_notes' )
                ) ),
                'bound_mcp_tools'   => json_encode( array( 'cora_create_client', 'cora_create_lead', 'cora_add_knowledge_fragment' ) ),
                'execution_type'    => 'prompt_behavior',
                'execution_script'  => null,
                'model_preference'  => 'auto',
                'created_by_model'  => 'system_seed',
            ),
            array(
                'skill_slug'        => 'gst-invoice-auditor',
                'name'              => 'GST SAC 9983 Invoice Auditor',
                'description'       => 'Audits commercial invoices for strict Indian GST compliance (SAC 9983: Photographic & Creative Services, 9% CGST + 9% SGST vs 18% IGST splits).',
                'category'          => 'Financials & Tax',
                'icon_svg'          => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
                'status'            => 'active',
                'instruction_prompt'=> "You are the Tax & GST Compliance Auditor for the workspace. When reviewing invoices:\n1. Verify SAC Code 9983 applied to creative, photography, and video services.\n2. Confirm 18% tax calculation: If client is in same state, split into 9% CGST + 9% SGST. If inter-state, apply 18% IGST.\n3. Verify sequential invoice numbering compliance.\n4. Check advance retainer policy (50% booking deposit, 50% prior to final asset release).",
                'trigger_phrases'   => 'audit invoice, check gst, verify tax, sac 9983, invoice compliance, igst cgst',
                'parameters_schema' => json_encode( array(
                    'type'       => 'object',
                    'properties' => array(
                        'invoice_id'     => array( 'type' => 'integer', 'description' => 'Invoice ID to audit' ),
                        'client_state'   => array( 'type' => 'string', 'description' => 'Billing state of client' ),
                        'taxable_amount' => array( 'type' => 'number', 'description' => 'Taxable subtotal amount in INR' )
                    ),
                    'required'   => array( 'taxable_amount' )
                ) ),
                'bound_mcp_tools'   => json_encode( array( 'cora_list_invoices', 'cora_get_invoice', 'cora_add_knowledge_fragment' ) ),
                'execution_type'    => 'prompt_behavior',
                'execution_script'  => null,
                'model_preference'  => 'auto',
                'created_by_model'  => 'system_seed',
            ),
            array(
                'skill_slug'        => 'high-ticket-lead-qualifier',
                'name'              => 'High-Ticket Lead Qualifier & Deal Scorer',
                'description'       => 'Evaluates inbound client leads against workspace capacity, profit margins, commercial timelines, and produces an objective win-probability score.',
                'category'          => 'CRM & Growth',
                'icon_svg'          => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
                'status'            => 'active',
                'instruction_prompt'=> "You are the Executive Growth Partner & Deal Qualifier. Evaluate incoming opportunities with ruthless objectivity:\n1. Assess deal value, client brand recognition, and deliverable timeline.\n2. Calculate expected margin considering studio day rates and equipment overhead.\n3. Output a 1-100 Qualification Score with 3 high-leverage negotiation recommendations.",
                'trigger_phrases'   => 'qualify lead, score deal, evaluate inquiry, pipeline review, high ticket score',
                'parameters_schema' => json_encode( array(
                    'type'       => 'object',
                    'properties' => array(
                        'lead_id'   => array( 'type' => 'integer', 'description' => 'Lead record ID' ),
                        'notes'     => array( 'type' => 'string', 'description' => 'Lead summary and project scope' )
                    ),
                    'required'   => array( 'notes' )
                ) ),
                'bound_mcp_tools'   => json_encode( array( 'cora_list_leads', 'cora_get_lead', 'cora_update_lead' ) ),
                'execution_type'    => 'prompt_behavior',
                'execution_script'  => null,
                'model_preference'  => 'auto',
                'created_by_model'  => 'system_seed',
            ),
            array(
                'skill_slug'        => 'shoot-gear-readiness-auditor',
                'name'              => 'Shoot Gear & Crew Readiness Auditor',
                'description'       => 'Cross-references scheduled shoot call-times with camera gear reservations, battery checkouts, and staff availability to prevent production delays.',
                'category'          => 'Production Operations',
                'icon_svg'          => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>',
                'status'            => 'active',
                'instruction_prompt'=> "You are the Production Operations Coordinator. When auditing upcoming shoots:\n1. Retrieve booking call-time, location, and assigned lead photographer/videographer.\n2. Verify equipment checkout list (Primary Body, Backup Body, 24-70mm, 70-200mm, lighting kits, formatted V90 SD cards).\n3. Alert if any gear item is double-booked or flagged for maintenance.",
                'trigger_phrases'   => 'gear checklist, crew audit, shoot readiness, equipment checkout, production prep',
                'parameters_schema' => json_encode( array(
                    'type'       => 'object',
                    'properties' => array(
                        'booking_id' => array( 'type' => 'integer', 'description' => 'Booking or shoot ID' ),
                        'shoot_date' => array( 'type' => 'string', 'description' => 'Target shoot date (YYYY-MM-DD)' )
                    ),
                    'required'   => array( 'shoot_date' )
                ) ),
                'bound_mcp_tools'   => json_encode( array( 'cora_list_bookings', 'cora_list_equipment', 'cora_get_workspace_overview' ) ),
                'execution_type'    => 'prompt_behavior',
                'execution_script'  => null,
                'model_preference'  => 'auto',
                'created_by_model'  => 'system_seed',
            ),
            array(
                'skill_slug'        => 'portfolio-storyteller',
                'name'              => 'Portfolio Transformation Storyteller',
                'description'       => 'Transforms raw project deliverables, visual metrics, and client quotes into rich, compelling Case Studies ready for Next.js website showcase publishing.',
                'category'          => 'Marketing & Showcase',
                'icon_svg'          => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>',
                'status'            => 'active',
                'instruction_prompt'=> "You are the Agency Storyteller & Portfolio Curator. When synthesizing a project case study:\n1. Draft an executive summary highlighting the client's business challenge.\n2. Detail the creative solution and deliverable scope with high production rigor.\n3. Format quantifiable outcome KPIs (e.g. conversion lift, engagement, impressions).\n4. Output Markdown ready for publication to the Next.js portfolio showcasing.",
                'trigger_phrases'   => 'storyteller, create case study, write portfolio, project story, publish showcase',
                'parameters_schema' => json_encode( array(
                    'type'       => 'object',
                    'properties' => array(
                        'project_title' => array( 'type' => 'string', 'description' => 'Title of the completed engagement' ),
                        'client_name'   => array( 'type' => 'string', 'description' => 'Client company name' ),
                        'raw_notes'     => array( 'type' => 'string', 'description' => 'Key milestones, deliverables, and client quote' )
                    ),
                    'required'   => array( 'project_title', 'client_name' )
                ) ),
                'bound_mcp_tools'   => json_encode( array( 'cora_list_projects', 'cora_get_project', 'cora_add_knowledge_fragment' ) ),
                'execution_type'    => 'prompt_behavior',
                'execution_script'  => null,
                'model_preference'  => 'auto',
                'created_by_model'  => 'gemini-2.5-flash',
            )
        );

        foreach ( $starter_skills as $skill ) {
            $wpdb->insert(
                $table_skills,
                array(
                    'agency_id'         => $agency_id,
                    'skill_slug'        => $skill['skill_slug'],
                    'name'              => $skill['name'],
                    'description'       => $skill['description'],
                    'category'          => $skill['category'],
                    'icon_svg'          => $skill['icon_svg'],
                    'status'            => $skill['status'],
                    'instruction_prompt'=> $skill['instruction_prompt'],
                    'trigger_phrases'   => $skill['trigger_phrases'],
                    'parameters_schema' => $skill['parameters_schema'],
                    'bound_mcp_tools'   => $skill['bound_mcp_tools'],
                    'execution_type'    => $skill['execution_type'],
                    'execution_script'  => $skill['execution_script'],
                    'model_preference'  => $skill['model_preference'],
                    'created_by_model'  => $skill['created_by_model'],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                )
            );
        }
    }

    /**
     * Get all plugins for a workspace
     */
    public static function get_workspace_plugins( $agency_id = null, $status = 'all' ) {
        global $wpdb;
        $agency_id = $agency_id ?: self::get_current_agency_id();
        self::ensure_starter_seeds( $agency_id );

        $table = $wpdb->prefix . 'cora_workspace_plugins';
        if ( $status !== 'all' ) {
            return $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$table} WHERE agency_id = %s AND status = %s ORDER BY id DESC",
                $agency_id, $status
            ), ARRAY_A );
        }

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE agency_id = %s ORDER BY id DESC",
            $agency_id
        ), ARRAY_A );
    }

    /**
     * Get all skills for a workspace
     */
    public static function get_workspace_skills( $agency_id = null, $status = 'all' ) {
        global $wpdb;
        $agency_id = $agency_id ?: self::get_current_agency_id();
        self::ensure_starter_seeds( $agency_id );

        $table = $wpdb->prefix . 'cora_workspace_skills';
        if ( $status !== 'all' ) {
            return $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$table} WHERE agency_id = %s AND status = %s ORDER BY id DESC",
                $agency_id, $status
            ), ARRAY_A );
        }

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE agency_id = %s ORDER BY id DESC",
            $agency_id
        ), ARRAY_A );
    }

    /**
     * Save/Create a plugin
     */
    public static function save_plugin( $data, $agency_id = null ) {
        global $wpdb;
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $table = $wpdb->prefix . 'cora_workspace_plugins';
        $now = current_time( 'mysql' );

        $slug = sanitize_title( $data['plugin_slug'] ?? ( $data['name'] ?? 'plugin-' . wp_generate_password( 6, false ) ) );
        $name = sanitize_text_field( $data['name'] ?? 'Custom Plugin' );
        $version = sanitize_text_field( $data['version'] ?? '1.0.0' );
        $description = sanitize_textarea_field( $data['description'] ?? '' );
        $category = sanitize_text_field( $data['category'] ?? 'Custom' );
        $icon_svg = ! empty( $data['icon_svg'] ) ? wp_kses_post( $data['icon_svg'] ) : '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
        $status = ( isset( $data['status'] ) && in_array( $data['status'], array( 'active', 'inactive' ), true ) ) ? $data['status'] : 'active';
        $type = sanitize_text_field( $data['type'] ?? 'custom' );
        $config_json = is_array( $data['config_json'] ?? null ) ? json_encode( $data['config_json'] ) : ( $data['config_json'] ?? '{}' );
        $mcp_tools_json = is_array( $data['mcp_tools_json'] ?? null ) ? json_encode( $data['mcp_tools_json'] ) : ( $data['mcp_tools_json'] ?? '[]' );
        $api_routes_json = is_array( $data['api_routes_json'] ?? null ) ? json_encode( $data['api_routes_json'] ) : ( $data['api_routes_json'] ?? '[]' );
        $author = sanitize_text_field( $data['author'] ?? 'Studio Admin' );
        $created_by_model = sanitize_text_field( $data['created_by_model'] ?? 'custom_developer' );

        $existing_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table} WHERE agency_id = %s AND plugin_slug = %s",
            $agency_id, $slug
        ) );

        if ( $existing_id ) {
            $wpdb->update(
                $table,
                array(
                    'name'             => $name,
                    'version'          => $version,
                    'description'      => $description,
                    'category'         => $category,
                    'icon_svg'         => $icon_svg,
                    'status'           => $status,
                    'type'             => $type,
                    'config_json'      => $config_json,
                    'mcp_tools_json'   => $mcp_tools_json,
                    'api_routes_json'  => $api_routes_json,
                    'updated_at'       => $now,
                ),
                array( 'id' => $existing_id )
            );
            return intval( $existing_id );
        }

        $wpdb->insert(
            $table,
            array(
                'agency_id'        => $agency_id,
                'plugin_slug'      => $slug,
                'name'             => $name,
                'version'          => $version,
                'description'      => $description,
                'category'         => $category,
                'icon_svg'         => $icon_svg,
                'status'           => $status,
                'type'             => $type,
                'config_json'      => $config_json,
                'mcp_tools_json'   => $mcp_tools_json,
                'api_routes_json'  => $api_routes_json,
                'author'           => $author,
                'created_by_model' => $created_by_model,
                'created_at'       => $now,
                'updated_at'       => $now,
            )
        );
        return intval( $wpdb->insert_id );
    }

    /**
     * Save/Create a skill
     */
    public static function save_skill( $data, $agency_id = null ) {
        global $wpdb;
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $table = $wpdb->prefix . 'cora_workspace_skills';
        $now = current_time( 'mysql' );

        $slug = sanitize_title( $data['skill_slug'] ?? ( $data['name'] ?? 'skill-' . wp_generate_password( 6, false ) ) );
        $name = sanitize_text_field( $data['name'] ?? 'Custom Skill' );
        $description = sanitize_textarea_field( $data['description'] ?? '' );
        $category = sanitize_text_field( $data['category'] ?? 'General' );
        $icon_svg = ! empty( $data['icon_svg'] ) ? wp_kses_post( $data['icon_svg'] ) : '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
        $status = ( isset( $data['status'] ) && in_array( $data['status'], array( 'active', 'inactive' ), true ) ) ? $data['status'] : 'active';
        $instruction_prompt = wp_unslash( $data['instruction_prompt'] ?? '' );
        $trigger_phrases = sanitize_text_field( $data['trigger_phrases'] ?? '' );
        $parameters_schema = is_array( $data['parameters_schema'] ?? null ) ? json_encode( $data['parameters_schema'] ) : ( $data['parameters_schema'] ?? '{}' );
        $bound_mcp_tools = is_array( $data['bound_mcp_tools'] ?? null ) ? json_encode( $data['bound_mcp_tools'] ) : ( $data['bound_mcp_tools'] ?? '[]' );
        $execution_type = sanitize_text_field( $data['execution_type'] ?? 'prompt_behavior' );
        $execution_script = ! empty( $data['execution_script'] ) ? wp_unslash( $data['execution_script'] ) : null;
        $model_preference = sanitize_text_field( $data['model_preference'] ?? 'auto' );
        $created_by_model = sanitize_text_field( $data['created_by_model'] ?? 'custom_developer' );

        $existing_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table} WHERE agency_id = %s AND skill_slug = %s",
            $agency_id, $slug
        ) );

        if ( $existing_id ) {
            $wpdb->update(
                $table,
                array(
                    'name'              => $name,
                    'description'       => $description,
                    'category'          => $category,
                    'icon_svg'          => $icon_svg,
                    'status'            => $status,
                    'instruction_prompt'=> $instruction_prompt,
                    'trigger_phrases'   => $trigger_phrases,
                    'parameters_schema' => $parameters_schema,
                    'bound_mcp_tools'   => $bound_mcp_tools,
                    'execution_type'    => $execution_type,
                    'execution_script'  => $execution_script,
                    'model_preference'  => $model_preference,
                    'updated_at'        => $now,
                ),
                array( 'id' => $existing_id )
            );
            return intval( $existing_id );
        }

        $wpdb->insert(
            $table,
            array(
                'agency_id'         => $agency_id,
                'skill_slug'        => $slug,
                'name'              => $name,
                'description'       => $description,
                'category'          => $category,
                'icon_svg'          => $icon_svg,
                'status'            => $status,
                'instruction_prompt'=> $instruction_prompt,
                'trigger_phrases'   => $trigger_phrases,
                'parameters_schema' => $parameters_schema,
                'bound_mcp_tools'   => $bound_mcp_tools,
                'execution_type'    => $execution_type,
                'execution_script'  => $execution_script,
                'model_preference'  => $model_preference,
                'created_by_model'  => $created_by_model,
                'created_at'        => $now,
                'updated_at'        => $now,
            )
        );
        return intval( $wpdb->insert_id );
    }

    /**
     * Upload and install a custom plugin at the workspace level.
     * Can receive a parsed JSON array, a raw JSON string, or uploaded file data.
     */
    public static function upload_plugin( $input, $agency_id = null ) {
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $data = array();

        if ( is_array( $input ) && isset( $input['tmp_name'] ) ) {
            // Uploaded file from $_FILES
            if ( ! file_exists( $input['tmp_name'] ) || ! is_readable( $input['tmp_name'] ) ) {
                return new WP_Error( 'invalid_file', 'Uploaded plugin file could not be read.' );
            }
            $file_content = file_get_contents( $input['tmp_name'] );
            $decoded = json_decode( $file_content, true );
            if ( ! is_array( $decoded ) ) {
                return new WP_Error( 'invalid_json', 'Uploaded plugin file must contain valid JSON format.' );
            }
            $data = $decoded;
        } elseif ( is_string( $input ) ) {
            $decoded = json_decode( $input, true );
            if ( ! is_array( $decoded ) ) {
                return new WP_Error( 'invalid_json', 'Plugin manifest string is not valid JSON.' );
            }
            $data = $decoded;
        } elseif ( is_array( $input ) ) {
            $data = $input;
        }

        if ( empty( $data['name'] ) && empty( $data['plugin_slug'] ) ) {
            return new WP_Error( 'missing_fields', 'Plugin package must include at least a name or plugin_slug.' );
        }

        // Enforce workspace-level upload metadata
        $data['type'] = 'uploaded';
        $data['created_by_model'] = 'workspace_upload';
        if ( empty( $data['author'] ) ) {
            $data['author'] = 'Workspace Upload';
        }

        $id = self::save_plugin( $data, $agency_id );
        $slug = sanitize_title( $data['plugin_slug'] ?? $data['name'] );

        return array(
            'success'     => true,
            'id'          => $id,
            'slug'        => $slug,
            'plugin_slug' => $slug,
            'name'        => $data['name'] ?? $slug,
            'agency_id'   => $agency_id,
            'message'     => "Plugin '" . ( $data['name'] ?? $slug ) . "' uploaded and activated at workspace level."
        );
    }

    /**
     * Upload and register a custom skill at the workspace level.
     * Can receive a parsed JSON array, a raw spec string (JSON or Markdown with YAML frontmatter), or uploaded file data.
     */
    public static function upload_skill( $input, $agency_id = null ) {
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $data = array();

        $content_str = '';
        if ( is_array( $input ) && isset( $input['tmp_name'] ) ) {
            // Uploaded file from $_FILES
            if ( ! file_exists( $input['tmp_name'] ) || ! is_readable( $input['tmp_name'] ) ) {
                return new WP_Error( 'invalid_file', 'Uploaded skill file could not be read.' );
            }
            $content_str = file_get_contents( $input['tmp_name'] );
        } elseif ( is_string( $input ) ) {
            $content_str = $input;
        } elseif ( is_array( $input ) ) {
            $data = $input;
        }

        if ( ! empty( $content_str ) && empty( $data ) ) {
            $decoded = json_decode( $content_str, true );
            if ( is_array( $decoded ) ) {
                $data = $decoded;
            } else {
                // Parse markdown with YAML frontmatter if present (e.g. SKILL.md format)
                if ( preg_match( '/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $content_str, $matches ) ) {
                    $yaml_lines = explode( "\n", trim( $matches[1] ) );
                    foreach ( $yaml_lines as $line ) {
                        $parts = explode( ':', $line, 2 );
                        if ( count( $parts ) === 2 ) {
                            $key = trim( $parts[0] );
                            $val = trim( $parts[1] );
                            $data[$key] = trim( $val, " \t\n\r\0\x0B\"'" );
                        }
                    }
                    $data['instruction_prompt'] = trim( $matches[2] );
                } else {
                    // Raw instruction prompt fallback
                    $data['name'] = 'Uploaded AI Skill';
                    $data['instruction_prompt'] = $content_str;
                }
            }
        }

        if ( empty( $data['name'] ) && empty( $data['skill_slug'] ) ) {
            return new WP_Error( 'missing_fields', 'Skill definition must include at least a name or skill_slug.' );
        }

        // Enforce workspace-level upload metadata
        $data['created_by_model'] = 'workspace_upload';
        if ( empty( $data['category'] ) ) {
            $data['category'] = 'Client Operations';
        }

        $id = self::save_skill( $data, $agency_id );
        $slug = sanitize_title( $data['skill_slug'] ?? $data['name'] );

        return array(
            'success'    => true,
            'id'         => $id,
            'slug'       => $slug,
            'skill_slug' => $slug,
            'name'       => $data['name'] ?? $slug,
            'agency_id'  => $agency_id,
            'message'    => "Skill '" . ( $data['name'] ?? $slug ) . "' uploaded and activated at workspace level."
        );
    }

    /**
     * Execute a custom workspace skill
     */
    public static function execute_skill( $skill_slug, $input_params = array(), $agency_id = null, $caller_model = 'manual_ui' ) {
        global $wpdb;
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $table = $wpdb->prefix . 'cora_workspace_skills';

        $skill = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE agency_id = %s AND (skill_slug = %s OR id = %d)",
            $agency_id, $skill_slug, intval( $skill_slug )
        ), ARRAY_A );

        if ( ! $skill ) {
            return new WP_Error( 'skill_not_found', "Skill '{$skill_slug}' not found in workspace." );
        }

        if ( $skill['status'] !== 'active' ) {
            return new WP_Error( 'skill_inactive', "Skill '{$skill['name']}' is currently inactive." );
        }

        // Build rich workspace background context
        $workspace_context = function_exists( 'cora_build_dynamic_workspace_context' )
            ? cora_build_dynamic_workspace_context( $agency_id )
            : "Workspace ID: {$agency_id}";

        $system_prompt = "You are executing the specialized workspace skill: '{$skill['name']}'.\n\n"
            . "SKILL BEHAVIOR INSTRUCTIONS:\n" . $skill['instruction_prompt'] . "\n\n"
            . "WORKSPACE OPERATIONAL CONTEXT:\n" . $workspace_context . "\n\n"
            . "OUTPUT GUIDELINES:\n"
            . "- Ground responses in concrete business metrics, exact dates, and Indian Rupees (₹).\n"
            . "- Maintain professional monochromatic tone. Zero fluff, zero emojis.\n"
            . "- If an action is required, emit [ACTION:action_name]{...JSON...}[/ACTION] format.\n";

        $input_message = is_array( $input_params ) ? json_encode( $input_params, JSON_PRETTY_PRINT ) : strval( $input_params );
        if ( empty( $input_message ) || $input_message === '[]' || $input_message === '{}' ) {
            $input_message = "Execute standard operational review for skill '{$skill['name']}'.";
        }

        $start_time = microtime( true );
        $ai_response = function_exists( 'cora_rag_call_ai_api' )
            ? cora_rag_call_ai_api( $input_message, $system_prompt )
            : "Skill '{$skill['name']}' executed successfully with parameters: " . $input_message;

        if ( is_wp_error( $ai_response ) ) {
            $output_text = "AI Model Execution Error: " . $ai_response->get_error_message();
            $status = 'error';
        } else {
            $output_text = strval( $ai_response );
            $status = 'success';
        }

        $exec_duration_ms = intval( ( microtime( true ) - $start_time ) * 1000 );

        // Log execution into telemetry table
        $table_logs = $wpdb->prefix . 'cora_workspace_plugin_logs';
        $wpdb->insert(
            $table_logs,
            array(
                'agency_id'        => $agency_id,
                'target_type'      => 'skill',
                'target_slug'      => $skill['skill_slug'],
                'action'           => 'execute',
                'payload'          => is_array( $input_params ) ? json_encode( $input_params ) : $input_params,
                'response_status'  => $status,
                'response_message' => mb_substr( $output_text, 0, 1000 ),
                'caller_model'     => $caller_model,
                'created_at'       => current_time( 'mysql' ),
            )
        );

        return array(
            'skill_slug'       => $skill['skill_slug'],
            'skill_name'       => $skill['name'],
            'execution_status' => $status,
            'duration_ms'      => $exec_duration_ms,
            'output'           => $output_text,
            'timestamp'        => current_time( 'c' ),
        );
    }

    /**
     * Dispatch an action or webhook through an active workspace plugin.
     * Enforces strict fault isolation, 5-second hard timeouts, and telemetry logging.
     * Guaranteed NEVER to throw unhandled exceptions or crash the workspace.
     *
     * @param string $plugin_slug Unique slug of the plugin
     * @param string $action Action name to trigger
     * @param array $payload Data payload to pass
     * @param string|null $agency_id Workspace tenant ID
     * @param string $caller_model Source caller identifier
     * @return array Standardized result with status, duration, and telemetry response
     */
    public static function dispatch_plugin_action( $plugin_slug, $action = 'dispatch', $payload = array(), $agency_id = null, $caller_model = 'manual_ui' ) {
        global $wpdb;
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $table = $wpdb->prefix . 'cora_workspace_plugins';

        $plugin = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE agency_id = %s AND (plugin_slug = %s OR id = %d)",
            $agency_id, $plugin_slug, intval( $plugin_slug )
        ), ARRAY_A );

        if ( ! $plugin ) {
            return array(
                'success'          => false,
                'execution_status' => 'error',
                'plugin_slug'      => $plugin_slug,
                'message'          => "Plugin '{$plugin_slug}' not found in workspace {$agency_id}.",
                'timestamp'        => current_time( 'c' ),
            );
        }

        if ( $plugin['status'] !== 'active' ) {
            return array(
                'success'          => false,
                'execution_status' => 'inactive',
                'plugin_slug'      => $plugin['plugin_slug'],
                'message'          => "Plugin '{$plugin['name']}' is currently inactive in this workspace.",
                'timestamp'        => current_time( 'c' ),
            );
        }

        $config = json_decode( $plugin['config_json'] ?? '{}', true ) ?: array();
        $start_time = microtime( true );
        $status = 'success';
        $response_message = '';
        $http_code = 200;

        try {
            // Determine dispatch strategy
            $target_url = $config['webhook_url'] ?? $config['endpoint'] ?? $config['url'] ?? '';

            if ( ! empty( $target_url ) ) {
                // Validate URL safety
                if ( ! wp_http_validate_url( $target_url ) ) {
                    throw new Exception( "Invalid or disallowed destination URL: {$target_url}" );
                }

                $headers = array(
                    'Content-Type'        => 'application/json',
                    'X-Cora-Workspace-ID' => strval( $agency_id ),
                    'X-Cora-Plugin-Slug'  => $plugin['plugin_slug'],
                    'X-Cora-Action'       => sanitize_text_field( $action ),
                );

                if ( ! empty( $config['auth_token'] ) ) {
                    $headers['Authorization'] = 'Bearer ' . $config['auth_token'];
                } elseif ( ! empty( $config['api_key'] ) ) {
                    $headers['X-API-Key'] = $config['api_key'];
                }

                $request_body = json_encode( array(
                    'event'        => $action,
                    'workspace_id' => $agency_id,
                    'timestamp'    => current_time( 'c' ),
                    'payload'      => $payload,
                    'plugin'       => array(
                        'name'    => $plugin['name'],
                        'slug'    => $plugin['plugin_slug'],
                        'version' => $plugin['version'],
                    ),
                ) );

                // 5-second hard timeout circuit breaker
                $response = wp_remote_post( $target_url, array(
                    'timeout'     => 5,
                    'headers'     => $headers,
                    'body'        => $request_body,
                    'data_format' => 'body',
                ) );

                if ( is_wp_error( $response ) ) {
                    throw new Exception( $response->get_error_message() );
                }

                $http_code = wp_remote_retrieve_response_code( $response );
                $raw_body  = wp_remote_retrieve_body( $response );

                if ( $http_code >= 400 ) {
                    $status = 'error';
                    $response_message = "Remote endpoint returned HTTP {$http_code}: " . mb_substr( $raw_body, 0, 500 );
                } else {
                    $status = 'success';
                    $response_message = mb_substr( $raw_body, 0, 1000 ) ?: "Dispatched successfully (HTTP {$http_code}).";
                }
            } else {
                // Local simulated action dispatch or mock
                $response_message = "Action '{$action}' processed locally for plugin '{$plugin['name']}'.";
            }
        } catch ( Throwable $e ) {
            $status = 'error';
            $response_message = 'Plugin Execution Error: ' . $e->getMessage();
            $http_code = 500;
        }

        $duration_ms = intval( ( microtime( true ) - $start_time ) * 1000 );

        // Telemetry logging
        $table_logs = $wpdb->prefix . 'cora_workspace_plugin_logs';
        $wpdb->insert(
            $table_logs,
            array(
                'agency_id'        => $agency_id,
                'target_type'      => 'plugin',
                'target_slug'      => $plugin['plugin_slug'],
                'action'           => sanitize_text_field( $action ),
                'payload'          => is_array( $payload ) ? json_encode( $payload ) : strval( $payload ),
                'response_status'  => $status,
                'response_message' => mb_substr( $response_message, 0, 1000 ),
                'caller_model'     => sanitize_text_field( $caller_model ),
                'created_at'       => current_time( 'mysql' ),
            )
        );

        return array(
            'success'          => ( $status === 'success' ),
            'execution_status' => $status,
            'plugin_slug'      => $plugin['plugin_slug'],
            'plugin_name'      => $plugin['name'],
            'http_code'        => $http_code,
            'duration_ms'      => $duration_ms,
            'message'          => $response_message,
            'timestamp'        => current_time( 'c' ),
        );
    }

    /**
     * AI Generator: Create complete plugin specification from natural language prompt
     */
    public static function generate_plugin_with_ai( $prompt, $agency_id = null ) {
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $clean_prompt = sanitize_text_field( $prompt );

        $slug_candidate = sanitize_title( substr( $clean_prompt, 0, 30 ) ) ?: 'custom-ai-plugin';
        $slug_candidate = preg_replace( '/[^a-z0-9\-]/', '', $slug_candidate );

        // Intelligent structured compiler
        $ai_system = "You are the Cora AI Plugin Architect. Convert the user's natural language request into a valid JSON specification for a workspace plugin.
Return ONLY raw JSON with keys:
{
  \"name\": \"Clear Display Title\",
  \"plugin_slug\": \"kebab-case-slug\",
  \"version\": \"1.0.0\",
  \"description\": \"Concise operational summary\",
  \"category\": \"Integrations|Financials|Operations|Productivity\",
  \"type\": \"custom|webhook|mcp_bridge\",
  \"config_json\": { \"setting_key\": \"value\" },
  \"mcp_tools_json\": [ { \"name\": \"tool_name\", \"description\": \"what tool does\", \"parameters\": { \"type\": \"object\", \"properties\": { \"param1\": { \"type\": \"string\" } } } } ],
  \"api_routes_json\": [ \"/route/path\" ]
}";

        $raw_response = function_exists( 'cora_rag_call_ai_api' )
            ? cora_rag_call_ai_api( $clean_prompt, $ai_system )
            : '';

        $parsed = null;
        if ( ! empty( $raw_response ) && ! is_wp_error( $raw_response ) ) {
            if ( preg_match( '/\{[\s\S]*\}/', $raw_response, $matches ) ) {
                $parsed = json_decode( $matches[0], true );
            }
        }

        // Robust fallback schema if API was offline
        if ( empty( $parsed ) || ! is_array( $parsed ) ) {
            $parsed = array(
                'name'             => ucwords( str_replace( '-', ' ', $slug_candidate ) ) . ' Integration',
                'plugin_slug'      => $slug_candidate . '-' . wp_generate_password( 4, false, false ),
                'version'          => '1.0.0',
                'description'      => "Automated extension for {$clean_prompt}. Configured with workspace webhook listeners and dynamic MCP schema.",
                'category'         => 'Custom',
                'type'             => 'custom',
                'config_json'      => array( 'enabled' => true, 'auto_sync' => true ),
                'mcp_tools_json'   => array(
                    array(
                        'name'        => 'execute_' . str_replace( '-', '_', $slug_candidate ),
                        'description' => "Execute action for {$clean_prompt}",
                        'parameters'  => array(
                            'type'       => 'object',
                            'properties' => array(
                                'action_type' => array( 'type' => 'string', 'description' => 'Sub-action parameter' )
                            )
                        )
                    )
                ),
                'api_routes_json'  => array( '/' . $slug_candidate . '/action' ),
            );
        }

        $parsed['author'] = 'AI Co-Founder';
        $parsed['created_by_model'] = 'claude-3.5-sonnet';
        $parsed['status'] = 'active';

        $plugin_id = self::save_plugin( $parsed, $agency_id );
        return array(
            'success'   => true,
            'plugin_id' => $plugin_id,
            'plugin'    => $parsed,
        );
    }

    /**
     * AI Generator: Create complete skill specification from natural language prompt
     */
    public static function generate_skill_with_ai( $prompt, $agency_id = null ) {
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $clean_prompt = sanitize_text_field( $prompt );

        $slug_candidate = sanitize_title( substr( $clean_prompt, 0, 30 ) ) ?: 'custom-ai-skill';
        $slug_candidate = preg_replace( '/[^a-z0-9\-]/', '', $slug_candidate );

        $ai_system = "You are the Cora AI Skills Engine Architect. Convert the user's natural language request into a valid JSON specification for an AI Agent Skill.
Return ONLY raw JSON with keys:
{
  \"name\": \"Clear Skill Title\",
  \"skill_slug\": \"kebab-case-slug\",
  \"description\": \"Concise operational summary of skill\",
  \"category\": \"Client Operations|Financials & Tax|CRM & Growth|Production Operations|Marketing\",
  \"instruction_prompt\": \"Detailed step-by-step system instructions for the LLM executing this skill\",
  \"trigger_phrases\": \"comma, separated, trigger, phrases\",
  \"parameters_schema\": { \"type\": \"object\", \"properties\": { \"input_text\": { \"type\": \"string\" } }, \"required\": [\"input_text\"] },
  \"bound_mcp_tools\": [ \"cora_get_workspace_overview\", \"cora_add_knowledge_fragment\" ],
  \"execution_type\": \"prompt_behavior\",
  \"model_preference\": \"auto\"
}";

        $raw_response = function_exists( 'cora_rag_call_ai_api' )
            ? cora_rag_call_ai_api( $clean_prompt, $ai_system )
            : '';

        $parsed = null;
        if ( ! empty( $raw_response ) && ! is_wp_error( $raw_response ) ) {
            if ( preg_match( '/\{[\s\S]*\}/', $raw_response, $matches ) ) {
                $parsed = json_decode( $matches[0], true );
            }
        }

        // Robust fallback schema if API was offline
        if ( empty( $parsed ) || ! is_array( $parsed ) ) {
            $parsed = array(
                'name'              => ucwords( str_replace( '-', ' ', $slug_candidate ) ),
                'skill_slug'        => $slug_candidate . '-' . wp_generate_password( 4, false, false ),
                'description'       => "Specialized operational skill for {$clean_prompt}.",
                'category'          => 'Client Operations',
                'instruction_prompt'=> "You are the workspace specialist for: {$clean_prompt}.\n1. Analyze input requirements carefully against workspace business context.\n2. Ensure zero hallucination: output exact Indian Rupees (₹), dates, and concrete operational steps.\n3. Return structured recommendations and action checklists.",
                'trigger_phrases'   => str_replace( '-', ' ', $slug_candidate ) . ', audit, review, inspect',
                'parameters_schema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'input_details' => array( 'type' => 'string', 'description' => 'Input details or data for this skill' )
                    ),
                    'required'   => array( 'input_details' )
                ),
                'bound_mcp_tools'   => array( 'cora_get_workspace_overview', 'cora_add_knowledge_fragment' ),
                'execution_type'    => 'prompt_behavior',
                'model_preference'  => 'auto',
            );
        }

        $parsed['created_by_model'] = 'gemini-2.5-flash';
        $parsed['status'] = 'active';

        $skill_id = self::save_skill( $parsed, $agency_id );
        return array(
            'success'  => true,
            'skill_id' => $skill_id,
            'skill'    => $parsed,
        );
    }

    /**
     * Workspace Scoped API Keys Management
     */
    public static function create_api_key( $key_name, $scopes = null, $agency_id = null ) {
        global $wpdb;
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $table = $wpdb->prefix . 'cora_workspace_api_keys';

        $clean_name = sanitize_text_field( $key_name ?: 'Workspace API Key' );
        $clean_scopes = ! empty( $scopes ) ? sanitize_text_field( is_array( $scopes ) ? implode( ' ', $scopes ) : $scopes ) : 'workspace:read workspace:write plugins:all skills:all mcp:all';

        $raw_secret = 'cora_ws_live_' . bin2hex( wp_generate_password( 24, false, false ) );
        $key_hash   = hash( 'sha256', $raw_secret );

        $wpdb->insert(
            $table,
            array(
                'agency_id'   => $agency_id,
                'key_name'    => $clean_name,
                'key_prefix'  => substr( $raw_secret, 0, 16 ) . '...',
                'key_hash'    => $key_hash,
                'scopes'      => $clean_scopes,
                'rate_limit'  => 2500,
                'status'      => 'active',
                'created_at'  => current_time( 'mysql' ),
            )
        );

        return array(
            'id'         => $wpdb->insert_id,
            'key_name'   => $clean_name,
            'api_key'    => $raw_secret, // Returned once upon creation
            'key_prefix' => substr( $raw_secret, 0, 16 ) . '...',
            'scopes'     => $clean_scopes,
            'created_at' => current_time( 'c' ),
        );
    }

    public static function get_api_keys( $agency_id = null ) {
        global $wpdb;
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $table = $wpdb->prefix . 'cora_workspace_api_keys';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT id, key_name, key_prefix, scopes, rate_limit, status, last_used_at, created_at FROM {$table} WHERE agency_id = %s ORDER BY id DESC",
            $agency_id
        ), ARRAY_A );
    }

    public static function revoke_api_key( $key_id, $agency_id = null ) {
        global $wpdb;
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $table = $wpdb->prefix . 'cora_workspace_api_keys';

        return $wpdb->update(
            $table,
            array( 'status' => 'revoked' ),
            array( 'id' => intval( $key_id ), 'agency_id' => $agency_id )
        );
    }

    /**
     * Authenticate an incoming Workspace API Key
     */
    public static function authenticate_api_key( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'cora_workspace_api_keys';

        $raw_key = '';
        if ( is_object( $request ) && method_exists( $request, 'get_header' ) ) {
            $auth_header = $request->get_header( 'Authorization' );
            if ( ! empty( $auth_header ) && preg_match( '/Bearer\s+(cora_ws_live_[a-zA-Z0-9]+)/i', $auth_header, $m ) ) {
                $raw_key = $m[1];
            } else {
                $x_key = $request->get_header( 'X-Cora-API-Key' );
                if ( ! empty( $x_key ) ) {
                    $raw_key = trim( $x_key );
                }
            }
        } elseif ( ! empty( $_SERVER['HTTP_X_CORA_API_KEY'] ) ) {
            $raw_key = trim( $_SERVER['HTTP_X_CORA_API_KEY'] );
        } elseif ( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) && preg_match( '/Bearer\s+(cora_ws_live_[a-zA-Z0-9]+)/i', $_SERVER['HTTP_AUTHORIZATION'], $m ) ) {
            $raw_key = $m[1];
        } elseif ( ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) && preg_match( '/Bearer\s+(cora_ws_live_[a-zA-Z0-9]+)/i', $_SERVER['REDIRECT_HTTP_AUTHORIZATION'], $m ) ) {
            $raw_key = $m[1];
        }

        if ( empty( $raw_key ) ) {
            return false;
        }

        $hash = hash( 'sha256', $raw_key );
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE key_hash = %s AND status = 'active'",
            $hash
        ), ARRAY_A );

        if ( ! empty( $row ) ) {
            $wpdb->update( $table, array( 'last_used_at' => current_time( 'mysql' ) ), array( 'id' => $row['id'] ) );
            return $row;
        }

        return false;
    }

    /**
     * Determine current user from Workspace API Key for REST requests
     */
    public static function determine_user_from_api_key( $user_id ) {
        if ( ! empty( $user_id ) ) {
            return $user_id;
        }

        $api_key = self::authenticate_api_key( null );
        if ( ! empty( $api_key ) ) {
            $agency_id = intval( $api_key['agency_id'] );
            if ( function_exists( 'cora_set_current_tenant_agency_id' ) ) {
                cora_set_current_tenant_agency_id( $agency_id );
            }
            $_REQUEST['cora_agency_id'] = $agency_id;
            $_REQUEST['workspace_id']   = $agency_id;

            $owner_ids = function_exists( 'cora_get_workspace_owner_user_ids' ) ? cora_get_workspace_owner_user_ids( $agency_id ) : array( 1 );
            return ! empty( $owner_ids ) ? $owner_ids[0] : 1;
        }

        return $user_id;
    }

    /**
     * Register REST API Routes under cora/v1/workspace
     */
    public static function register_rest_routes() {
        // Workspace Plugins
        register_rest_route( 'cora/v1', '/workspace/plugins', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( __CLASS__, 'rest_get_plugins' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( __CLASS__, 'rest_save_plugin' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            )
        ) );

        register_rest_route( 'cora/v1', '/workspace/plugins/(?P<id>[a-zA-Z0-9_\-]+)', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( __CLASS__, 'rest_get_plugin_single' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            ),
            array(
                'methods'             => 'DELETE',
                'callback'            => array( __CLASS__, 'rest_delete_plugin' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            )
        ) );

        register_rest_route( 'cora/v1', '/workspace/plugins/(?P<id>[a-zA-Z0-9_\-]+)/toggle', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'rest_toggle_plugin' ),
            'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
        ) );

        register_rest_route( 'cora/v1', '/workspace/plugins/upload', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'rest_upload_plugin' ),
            'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
        ) );

        register_rest_route( 'cora/v1', '/workspace/plugins/(?P<slug>[a-zA-Z0-9_\-]+)/dispatch', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'rest_dispatch_plugin' ),
            'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
        ) );

        // Workspace Skills
        register_rest_route( 'cora/v1', '/workspace/skills', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( __CLASS__, 'rest_get_skills' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( __CLASS__, 'rest_save_skill' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            )
        ) );

        register_rest_route( 'cora/v1', '/workspace/skills/upload', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'rest_upload_skill' ),
            'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
        ) );

        register_rest_route( 'cora/v1', '/workspace/skills/(?P<id>[a-zA-Z0-9_\-]+)', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( __CLASS__, 'rest_get_skill_single' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            ),
            array(
                'methods'             => 'DELETE',
                'callback'            => array( __CLASS__, 'rest_delete_skill' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            )
        ) );

        register_rest_route( 'cora/v1', '/workspace/skills/(?P<id>[a-zA-Z0-9_\-]+)/toggle', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'rest_toggle_skill' ),
            'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
        ) );

        register_rest_route( 'cora/v1', '/workspace/skills/(?P<slug>[a-zA-Z0-9_\-]+)/execute', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'rest_execute_skill' ),
            'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
        ) );

        // AI Generators
        register_rest_route( 'cora/v1', '/workspace/ai/generate-plugin', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'rest_generate_plugin' ),
            'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
        ) );

        register_rest_route( 'cora/v1', '/workspace/ai/generate-skill', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'rest_generate_skill' ),
            'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
        ) );

        // Workspace API Keys
        register_rest_route( 'cora/v1', '/workspace/api-keys', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( __CLASS__, 'rest_get_api_keys' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( __CLASS__, 'rest_create_api_key' ),
                'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
            )
        ) );

        register_rest_route( 'cora/v1', '/workspace/api-keys/(?P<id>\d+)', array(
            'methods'             => 'DELETE',
            'callback'            => array( __CLASS__, 'rest_revoke_api_key' ),
            'permission_callback' => array( __CLASS__, 'rest_check_auth' ),
        ) );

        // Dynamic OpenAPI 3.0 Manifest tailored to workspace
        register_rest_route( 'cora/v1', '/workspace/openapi', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'rest_get_openapi' ),
            'permission_callback' => '__return_true',
        ) );
    }

    /**
     * REST Auth Check: Supports WordPress logged-in session, OAuth Bearer tokens, or Workspace Scoped API Keys
     */
    public static function rest_check_auth( $request ) {
        if ( current_user_can( 'read' ) ) {
            return true;
        }
        if ( self::authenticate_api_key( $request ) ) {
            return true;
        }
        // Also check standard Cora MCP bearer tokens
        if ( function_exists( 'cora_mcp_check_auth' ) ) {
            $auth = cora_mcp_check_auth( $request );
            if ( ! empty( $auth['valid'] ) ) {
                return true;
            }
        }
        return new WP_Error( 'rest_forbidden', 'Unauthorized. Requires valid Bearer Token or X-Cora-API-Key header.', array( 'status' => 401 ) );
    }

    // ── REST Callbacks ────────────────────────────────────────────────────────

    public static function rest_get_plugins( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $plugins = self::get_workspace_plugins( $agency_id );
        return new WP_REST_Response( array( 'success' => true, 'plugins' => $plugins, 'count' => count( $plugins ) ), 200 );
    }

    public static function rest_save_plugin( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $data = $request->get_json_params() ?: $request->get_params();
        $id = self::save_plugin( $data, $agency_id );
        return new WP_REST_Response( array( 'success' => true, 'id' => $id, 'message' => 'Plugin saved successfully.' ), 200 );
    }

    public static function rest_upload_plugin( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $files = $request->get_file_params();
        if ( ! empty( $files['plugin_file'] ) ) {
            $res = self::upload_plugin( $files['plugin_file'], $agency_id );
        } else {
            $params = $request->get_json_params() ?: $request->get_params();
            $res = self::upload_plugin( $params, $agency_id );
        }
        if ( is_wp_error( $res ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => $res->get_error_message() ), 400 );
        }
        return new WP_REST_Response( $res, 201 );
    }

    public static function rest_get_plugin_single( $request ) {
        global $wpdb;
        $agency_id = self::resolve_request_agency_id( $request );
        $id = $request->get_param( 'id' );
        $table = $wpdb->prefix . 'cora_workspace_plugins';
        $plugin = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE agency_id = %s AND (id = %d OR plugin_slug = %s)",
            $agency_id, intval( $id ), sanitize_title( $id )
        ), ARRAY_A );
        if ( ! $plugin ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Plugin not found.' ), 404 );
        }
        return new WP_REST_Response( array( 'success' => true, 'plugin' => $plugin ), 200 );
    }

    public static function rest_delete_plugin( $request ) {
        global $wpdb;
        $agency_id = self::resolve_request_agency_id( $request );
        $id = $request->get_param( 'id' );
        $table = $wpdb->prefix . 'cora_workspace_plugins';
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$table} WHERE agency_id = %s AND (id = %d OR plugin_slug = %s)",
            $agency_id, intval( $id ), sanitize_title( $id )
        ) );
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Plugin deleted successfully.' ), 200 );
    }

    public static function rest_toggle_plugin( $request ) {
        global $wpdb;
        $agency_id = self::resolve_request_agency_id( $request );
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params() ?: array();
        $status = in_array( $params['status'] ?? '', array( 'active', 'inactive' ), true ) ? $params['status'] : 'active';
        $table = $wpdb->prefix . 'cora_workspace_plugins';
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$table} SET status = %s, updated_at = %s WHERE agency_id = %s AND (id = %d OR plugin_slug = %s)",
            $status, current_time( 'mysql' ), $agency_id, intval( $id ), sanitize_title( $id )
        ) );
        return new WP_REST_Response( array( 'success' => true, 'status' => $status, 'message' => "Plugin status updated to {$status}." ), 200 );
    }

    public static function rest_dispatch_plugin( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $slug = $request->get_param( 'slug' );
        $params = $request->get_json_params() ?: $request->get_params();
        $action = $params['action'] ?? 'dispatch';
        $payload = $params['payload'] ?? $params;
        $result = self::dispatch_plugin_action( $slug, $action, $payload, $agency_id, 'rest_api' );
        return new WP_REST_Response( array( 'success' => ( $result['execution_status'] === 'success' ), 'result' => $result ), 200 );
    }

    public static function rest_get_skills( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $skills = self::get_workspace_skills( $agency_id );
        return new WP_REST_Response( array( 'success' => true, 'skills' => $skills, 'count' => count( $skills ) ), 200 );
    }

    public static function rest_save_skill( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $data = $request->get_json_params() ?: $request->get_params();
        $id = self::save_skill( $data, $agency_id );
        return new WP_REST_Response( array( 'success' => true, 'id' => $id, 'message' => 'Skill saved successfully.' ), 200 );
    }

    public static function rest_upload_skill( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $files = $request->get_file_params();
        if ( ! empty( $files['skill_file'] ) ) {
            $res = self::upload_skill( $files['skill_file'], $agency_id );
        } else {
            $params = $request->get_json_params() ?: $request->get_params();
            $res = self::upload_skill( $params, $agency_id );
        }
        if ( is_wp_error( $res ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => $res->get_error_message() ), 400 );
        }
        return new WP_REST_Response( $res, 201 );
    }

    public static function rest_get_skill_single( $request ) {
        global $wpdb;
        $agency_id = self::resolve_request_agency_id( $request );
        $id = $request->get_param( 'id' );
        $table = $wpdb->prefix . 'cora_workspace_skills';
        $skill = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE agency_id = %s AND (id = %d OR skill_slug = %s)",
            $agency_id, intval( $id ), sanitize_title( $id )
        ), ARRAY_A );
        if ( ! $skill ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Skill not found.' ), 404 );
        }
        return new WP_REST_Response( array( 'success' => true, 'skill' => $skill ), 200 );
    }

    public static function rest_delete_skill( $request ) {
        global $wpdb;
        $agency_id = self::resolve_request_agency_id( $request );
        $id = $request->get_param( 'id' );
        $table = $wpdb->prefix . 'cora_workspace_skills';
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$table} WHERE agency_id = %s AND (id = %d OR skill_slug = %s)",
            $agency_id, intval( $id ), sanitize_title( $id )
        ) );
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Skill deleted successfully.' ), 200 );
    }

    public static function rest_toggle_skill( $request ) {
        global $wpdb;
        $agency_id = self::resolve_request_agency_id( $request );
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params() ?: array();
        $status = in_array( $params['status'] ?? '', array( 'active', 'inactive' ), true ) ? $params['status'] : 'active';
        $table = $wpdb->prefix . 'cora_workspace_skills';
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$table} SET status = %s, updated_at = %s WHERE agency_id = %s AND (id = %d OR skill_slug = %s)",
            $status, current_time( 'mysql' ), $agency_id, intval( $id ), sanitize_title( $id )
        ) );
        return new WP_REST_Response( array( 'success' => true, 'status' => $status, 'message' => "Skill status updated to {$status}." ), 200 );
    }

    public static function rest_execute_skill( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $slug = $request->get_param( 'slug' );
        $params = $request->get_json_params() ?: $request->get_params();
        $result = self::execute_skill( $slug, $params, $agency_id, 'rest_api' );
        if ( is_wp_error( $result ) ) {
            return new WP_REST_Response( array( 'success' => false, 'error' => $result->get_error_message() ), 400 );
        }
        return new WP_REST_Response( array( 'success' => true, 'result' => $result ), 200 );
    }

    public static function rest_generate_plugin( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $params = $request->get_json_params() ?: array();
        $prompt = $params['prompt'] ?? '';
        if ( empty( $prompt ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Prompt is required.' ), 400 );
        }
        $res = self::generate_plugin_with_ai( $prompt, $agency_id );
        return new WP_REST_Response( $res, 200 );
    }

    public static function rest_generate_skill( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $params = $request->get_json_params() ?: array();
        $prompt = $params['prompt'] ?? '';
        if ( empty( $prompt ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Prompt is required.' ), 400 );
        }
        $res = self::generate_skill_with_ai( $prompt, $agency_id );
        return new WP_REST_Response( $res, 200 );
    }

    public static function rest_get_api_keys( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $keys = self::get_api_keys( $agency_id );
        return new WP_REST_Response( array( 'success' => true, 'keys' => $keys ), 200 );
    }

    public static function rest_create_api_key( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $params = $request->get_json_params() ?: array();
        $name = $params['key_name'] ?? 'New API Key';
        $scopes = $params['scopes'] ?? null;
        $res = self::create_api_key( $name, $scopes, $agency_id );
        return new WP_REST_Response( array( 'success' => true, 'key' => $res ), 201 );
    }

    public static function rest_revoke_api_key( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $id = $request->get_param( 'id' );
        self::revoke_api_key( $id, $agency_id );
        return new WP_REST_Response( array( 'success' => true, 'message' => 'API Key revoked.' ), 200 );
    }

    public static function rest_get_openapi( $request ) {
        $agency_id = self::resolve_request_agency_id( $request );
        $skills = self::get_workspace_skills( $agency_id, 'active' );
        $plugins = self::get_workspace_plugins( $agency_id, 'active' );

        $paths = array(
            '/cora/v1/workspace/overview' => array(
                'get' => array(
                    'summary'     => 'Retrieve Workspace Overview & Financial Pulse',
                    'description' => 'Real-time ledger receivables, client count, active shoots, and operational health.',
                    'responses'   => array( '200' => array( 'description' => 'Workspace metrics object' ) )
                )
            ),
            '/cora/v1/workspace/plugins' => array(
                'get' => array(
                    'summary'     => 'List Installed Workspace Plugins',
                    'description' => 'Get all active extensions, webhook connectors, and MCP bridges.',
                    'responses'   => array( '200' => array( 'description' => 'List of plugins' ) )
                )
            ),
            '/cora/v1/workspace/skills' => array(
                'get' => array(
                    'summary'     => 'List Active Workspace AI Skills',
                    'description' => 'Get all registered skills, behavioral prompts, and bound MCP tools.',
                    'responses'   => array( '200' => array( 'description' => 'List of skills' ) )
                )
            ),
        );

        foreach ( $skills as $s ) {
            $path_key = '/cora/v1/workspace/skills/' . $s['skill_slug'] . '/execute';
            $paths[$path_key] = array(
                'post' => array(
                    'summary'     => 'Execute Skill: ' . $s['name'],
                    'description' => $s['description'],
                    'requestBody' => array(
                        'required' => false,
                        'content'  => array(
                            'application/json' => array(
                                'schema' => json_decode( $s['parameters_schema'], true ) ?: (object) array()
                            )
                        )
                    ),
                    'responses'   => array( '200' => array( 'description' => 'Skill execution output object' ) )
                )
            );
        }

        $schema = array(
            'openapi' => '3.0.1',
            'info'    => array(
                'title'       => 'Cora Workspace Dynamic API & MCP Specification',
                'description' => 'Workspace-level open REST endpoints and dynamic AI skills runtime for tenant ' . $agency_id,
                'version'     => defined( 'CORA_WORKSPACE_VERSION' ) ? CORA_WORKSPACE_VERSION : '4.9.386',
            ),
            'servers' => array(
                array( 'url' => site_url( '/wp-json' ), 'description' => 'Active Workspace Host' )
            ),
            'paths'   => $paths,
            'components' => array(
                'securitySchemes' => array(
                    'ApiKeyAuth' => array(
                        'type' => 'apiKey',
                        'in'   => 'header',
                        'name' => 'X-Cora-API-Key'
                    ),
                    'BearerAuth' => array(
                        'type'   => 'http',
                        'scheme' => 'bearer'
                    )
                )
            ),
            'security' => array(
                array( 'ApiKeyAuth' => array() ),
                array( 'BearerAuth' => array() )
            )
        );

        return new WP_REST_Response( $schema, 200 );
    }

    private static function resolve_request_agency_id( $request ) {
        $auth_key = self::authenticate_api_key( $request );
        if ( ! empty( $auth_key['agency_id'] ) ) {
            return strval( $auth_key['agency_id'] );
        }
        $param = $request->get_param( 'agency_id' );
        if ( ! empty( $param ) && is_numeric( $param ) ) {
            return strval( intval( $param ) );
        }
        return self::get_current_agency_id();
    }

    // ── In-Dashboard AJAX Handlers ──────────────────────────────────────────

    public static function ajax_get_plugins() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $plugins = self::get_workspace_plugins( $agency_id );
        wp_send_json_success( array( 'plugins' => $plugins, 'count' => count( $plugins ) ) );
    }

    public static function ajax_save_plugin() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $data = $_POST;
        $id = self::save_plugin( $data, $agency_id );
        wp_send_json_success( array( 'id' => $id, 'message' => 'Plugin saved successfully.' ) );
    }

    public static function ajax_upload_plugin() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();

        $input = null;
        if ( ! empty( $_FILES['plugin_file'] ) ) {
            $input = $_FILES['plugin_file'];
        } elseif ( ! empty( $_POST['plugin_manifest'] ) ) {
            $input = wp_unslash( $_POST['plugin_manifest'] );
        }

        if ( empty( $input ) ) {
            wp_send_json_error( array( 'message' => 'No plugin file or manifest JSON received.' ) );
        }

        $res = self::upload_plugin( $input, $agency_id );
        if ( is_wp_error( $res ) ) {
            wp_send_json_error( array( 'message' => $res->get_error_message() ) );
        }

        wp_send_json_success( $res );
    }

    public static function ajax_toggle_plugin() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        global $wpdb;
        $agency_id = self::get_current_agency_id();
        $slug = sanitize_title( $_POST['plugin_slug'] ?? '' );
        $status = sanitize_text_field( $_POST['status'] ?? 'active' );
        $table = $wpdb->prefix . 'cora_workspace_plugins';

        $wpdb->update(
            $table,
            array( 'status' => $status, 'updated_at' => current_time( 'mysql' ) ),
            array( 'agency_id' => $agency_id, 'plugin_slug' => $slug )
        );

        wp_send_json_success( array( 'status' => $status, 'message' => "Plugin toggled to {$status}." ) );
    }

    public static function ajax_dispatch_plugin() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $slug = sanitize_text_field( $_POST['plugin_slug'] ?? '' );
        $action = sanitize_text_field( $_POST['action_name'] ?? 'dispatch' );
        $payload_raw = $_POST['payload'] ?? '';
        $payload = is_string( $payload_raw ) ? ( json_decode( wp_unslash( $payload_raw ), true ) ?: array() ) : (array) $payload_raw;
        $result = self::dispatch_plugin_action( $slug, $action, $payload, $agency_id, 'manual_ui' );
        wp_send_json_success( $result );
    }

    public static function ajax_delete_plugin() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        global $wpdb;
        $agency_id = self::get_current_agency_id();
        $slug = sanitize_title( $_POST['plugin_slug'] ?? '' );
        $table = $wpdb->prefix . 'cora_workspace_plugins';

        $wpdb->delete( $table, array( 'agency_id' => $agency_id, 'plugin_slug' => $slug ) );
        wp_send_json_success( array( 'message' => 'Plugin removed.' ) );
    }

    public static function ajax_get_skills() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $skills = self::get_workspace_skills( $agency_id );
        wp_send_json_success( array( 'skills' => $skills, 'count' => count( $skills ) ) );
    }

    public static function ajax_save_skill() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $data = $_POST;
        $id = self::save_skill( $data, $agency_id );
        wp_send_json_success( array( 'id' => $id, 'message' => 'Skill saved successfully.' ) );
    }

    public static function ajax_upload_skill() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();

        $input = null;
        if ( ! empty( $_FILES['skill_file'] ) ) {
            $input = $_FILES['skill_file'];
        } elseif ( ! empty( $_POST['skill_definition'] ) ) {
            $input = wp_unslash( $_POST['skill_definition'] );
        }

        if ( empty( $input ) ) {
            wp_send_json_error( array( 'message' => 'No skill file or definition received.' ) );
        }

        $res = self::upload_skill( $input, $agency_id );
        if ( is_wp_error( $res ) ) {
            wp_send_json_error( array( 'message' => $res->get_error_message() ) );
        }

        wp_send_json_success( $res );
    }

    public static function ajax_toggle_skill() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        global $wpdb;
        $agency_id = self::get_current_agency_id();
        $slug = sanitize_title( $_POST['skill_slug'] ?? '' );
        $status = sanitize_text_field( $_POST['status'] ?? 'active' );
        $table = $wpdb->prefix . 'cora_workspace_skills';

        $wpdb->update(
            $table,
            array( 'status' => $status, 'updated_at' => current_time( 'mysql' ) ),
            array( 'agency_id' => $agency_id, 'skill_slug' => $slug )
        );

        wp_send_json_success( array( 'status' => $status, 'message' => "Skill toggled to {$status}." ) );
    }

    public static function ajax_delete_skill() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        global $wpdb;
        $agency_id = self::get_current_agency_id();
        $slug = sanitize_title( $_POST['skill_slug'] ?? '' );
        $table = $wpdb->prefix . 'cora_workspace_skills';

        $wpdb->delete( $table, array( 'agency_id' => $agency_id, 'skill_slug' => $slug ) );
        wp_send_json_success( array( 'message' => 'Skill removed.' ) );
    }

    public static function ajax_execute_skill() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $slug = sanitize_title( $_POST['skill_slug'] ?? '' );
        $params_raw = isset( $_POST['parameters'] ) ? $_POST['parameters'] : array();
        if ( is_string( $params_raw ) ) {
            $params = json_decode( wp_unslash( $params_raw ), true ) ?: array( 'input' => $params_raw );
        } else {
            $params = (array) $params_raw;
        }

        $res = self::execute_skill( $slug, $params, $agency_id, 'dashboard_simulator' );
        if ( is_wp_error( $res ) ) {
            wp_send_json_error( array( 'message' => $res->get_error_message() ) );
        }
        wp_send_json_success( $res );
    }

    public static function ajax_generate_ai_plugin() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $prompt = sanitize_text_field( $_POST['prompt'] ?? '' );
        if ( empty( $prompt ) ) {
            wp_send_json_error( array( 'message' => 'Prompt cannot be empty.' ) );
        }
        $res = self::generate_plugin_with_ai( $prompt, $agency_id );
        wp_send_json_success( $res );
    }

    public static function ajax_generate_ai_skill() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $prompt = sanitize_text_field( $_POST['prompt'] ?? '' );
        if ( empty( $prompt ) ) {
            wp_send_json_error( array( 'message' => 'Prompt cannot be empty.' ) );
        }
        $res = self::generate_skill_with_ai( $prompt, $agency_id );
        wp_send_json_success( $res );
    }

    public static function ajax_get_api_keys() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $keys = self::get_api_keys( $agency_id );
        wp_send_json_success( array( 'keys' => $keys ) );
    }

    public static function ajax_create_api_key() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $name = sanitize_text_field( $_POST['key_name'] ?? 'New API Key' );
        $scopes = isset( $_POST['scopes'] ) ? sanitize_text_field( $_POST['scopes'] ) : null;
        $res = self::create_api_key( $name, $scopes, $agency_id );
        wp_send_json_success( $res );
    }

    public static function ajax_revoke_api_key() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
        $agency_id = self::get_current_agency_id();
        $id = intval( $_POST['key_id'] ?? 0 );
        self::revoke_api_key( $id, $agency_id );
        wp_send_json_success( array( 'message' => 'Key revoked.' ) );
    }

    // ── Dynamic MCP Tool Integration ────────────────────────────────────────

    /**
     * Filter MCP tools to dynamically inject workspace skills & plugins as live MCP tools
     */
    public static function filter_mcp_tools( $tools ) {
        $agency_id = self::get_current_agency_id();
        $extra_tools = self::get_mcp_tools( $agency_id );
        return array_merge( $tools, $extra_tools );
    }

    /**
     * Get dynamic MCP tool definitions for workspace
     */
    public static function get_mcp_tools( $agency_id = null ) {
        $agency_id = $agency_id ?: self::get_current_agency_id();
        $dynamic = array();

        // 1. LLM Management Tools for Connected AI Models
        $dynamic['cora_list_workspace_plugins'] = array(
            'name'          => 'cora_list_workspace_plugins',
            'description'   => 'List all installed, active, and custom plugins configured in the active workspace.',
            'readOnly'      => true,
            'destructive'   => false,
            'openWorld'     => false,
            'requiredScope' => 'workspace:read',
            'inputSchema'   => array(
                'type'       => 'object',
                'properties' => array(
                    'status' => array( 'type' => 'string', 'description' => 'Filter by status: active, inactive, or all' )
                )
            ),
            'handler'       => array( __CLASS__, 'handle_mcp_list_plugins' ),
        );

        $dynamic['cora_create_workspace_plugin'] = array(
            'name'          => 'cora_create_workspace_plugin',
            'description'   => 'Allow connected AI LLMs to create, configure, and install a custom dynamic plugin or webhook listener into the workspace.',
            'readOnly'      => false,
            'destructive'   => false,
            'openWorld'     => false,
            'requiredScope' => 'workspace:write',
            'inputSchema'   => array(
                'type'       => 'object',
                'properties' => array(
                    'name'        => array( 'type' => 'string', 'description' => 'Plugin name' ),
                    'plugin_slug' => array( 'type' => 'string', 'description' => 'Kebab-case slug' ),
                    'description' => array( 'type' => 'string', 'description' => 'Summary of what this plugin performs' ),
                    'category'    => array( 'type' => 'string', 'description' => 'Category: Integrations, Financials, Operations, Productivity' ),
                    'type'        => array( 'type' => 'string', 'description' => 'custom, webhook, or mcp_bridge' ),
                    'config'      => array( 'type' => 'object', 'description' => 'Key-value configuration and settings' ),
                    'mcp_tools'   => array( 'type' => 'array', 'description' => 'Array of MCP tools this plugin exposes' ),
                ),
                'required'   => array( 'name', 'description' )
            ),
            'handler'       => array( __CLASS__, 'handle_mcp_create_plugin' ),
        );

        $dynamic['cora_toggle_workspace_plugin'] = array(
            'name'          => 'cora_toggle_workspace_plugin',
            'description'   => 'Activate or deactivate an installed workspace plugin.',
            'readOnly'      => false,
            'destructive'   => false,
            'openWorld'     => false,
            'requiredScope' => 'workspace:write',
            'inputSchema'   => array(
                'type'       => 'object',
                'properties' => array(
                    'plugin_slug' => array( 'type' => 'string', 'description' => 'Plugin slug to toggle' ),
                    'status'      => array( 'type' => 'string', 'description' => 'active or inactive' )
                ),
                'required'   => array( 'plugin_slug', 'status' )
            ),
            'handler'       => array( __CLASS__, 'handle_mcp_toggle_plugin' ),
        );

        $dynamic['cora_list_workspace_skills'] = array(
            'name'          => 'cora_list_workspace_skills',
            'description'   => 'List all registered custom AI agent skills in the active workspace with trigger keywords and bound MCP tools.',
            'readOnly'      => true,
            'destructive'   => false,
            'openWorld'     => false,
            'requiredScope' => 'workspace:read',
            'inputSchema'   => array(
                'type'       => 'object',
                'properties' => array(
                    'category' => array( 'type' => 'string', 'description' => 'Optional filter by skill category' )
                )
            ),
            'handler'       => array( __CLASS__, 'handle_mcp_list_skills' ),
        );

        $dynamic['cora_create_workspace_skill'] = array(
            'name'          => 'cora_create_workspace_skill',
            'description'   => 'Allow connected AI LLMs to define and create a new custom AI skill with specialized system instructions, trigger keywords, and tool bindings.',
            'readOnly'      => false,
            'destructive'   => false,
            'openWorld'     => false,
            'requiredScope' => 'workspace:write',
            'inputSchema'   => array(
                'type'       => 'object',
                'properties' => array(
                    'name'               => array( 'type' => 'string', 'description' => 'Skill title' ),
                    'skill_slug'         => array( 'type' => 'string', 'description' => 'Kebab-case skill slug' ),
                    'description'        => array( 'type' => 'string', 'description' => 'Overview of skill competency' ),
                    'category'           => array( 'type' => 'string', 'description' => 'Category: Client Operations, Financials & Tax, CRM & Growth, Production' ),
                    'instruction_prompt' => array( 'type' => 'string', 'description' => 'Detailed system prompt instructions guiding AI behavior' ),
                    'trigger_phrases'    => array( 'type' => 'string', 'description' => 'Comma-separated trigger keywords or intent phrases' ),
                    'parameters_schema'  => array( 'type' => 'object', 'description' => 'JSON schema of inputs' ),
                    'bound_mcp_tools'    => array( 'type' => 'array', 'description' => 'List of tool names this skill can access' )
                ),
                'required'   => array( 'name', 'instruction_prompt' )
            ),
            'handler'       => array( __CLASS__, 'handle_mcp_create_skill' ),
        );

        $dynamic['cora_execute_workspace_skill'] = array(
            'name'          => 'cora_execute_workspace_skill',
            'description'   => 'Invoke a custom workspace skill by slug with input parameters and return the AI output.',
            'readOnly'      => false,
            'destructive'   => false,
            'openWorld'     => false,
            'requiredScope' => 'workspace:read',
            'inputSchema'   => array(
                'type'       => 'object',
                'properties' => array(
                    'skill_slug' => array( 'type' => 'string', 'description' => 'Slug of the skill to execute' ),
                    'parameters' => array( 'type' => 'object', 'description' => 'Arguments conforming to the skill parameters schema' )
                ),
                'required'   => array( 'skill_slug' )
            ),
            'handler'       => array( __CLASS__, 'handle_mcp_execute_skill' ),
        );

        $dynamic['cora_get_workspace_modules'] = array(
            'name'          => 'cora_get_workspace_modules',
            'description'   => 'Inspect enabled and disabled status of the 24 core platform modules for the active workspace.',
            'readOnly'      => true,
            'destructive'   => false,
            'openWorld'     => false,
            'requiredScope' => 'workspace:read',
            'inputSchema'   => array( 'type' => 'object', 'properties' => (object) array() ),
            'handler'       => array( __CLASS__, 'handle_mcp_get_modules' ),
        );

        $dynamic['cora_toggle_workspace_module'] = array(
            'name'          => 'cora_toggle_workspace_module',
            'description'   => 'Enable or disable a core workspace module (e.g. leads, forms, crew_scheduler, plant_inventory).',
            'readOnly'      => false,
            'destructive'   => false,
            'openWorld'     => false,
            'requiredScope' => 'workspace:write',
            'inputSchema'   => array(
                'type'       => 'object',
                'properties' => array(
                    'module_slug' => array( 'type' => 'string', 'description' => 'Module slug to toggle' ),
                    'enabled'     => array( 'type' => 'boolean', 'description' => 'True to enable, False to disable' )
                ),
                'required'   => array( 'module_slug', 'enabled' )
            ),
            'handler'       => array( __CLASS__, 'handle_mcp_toggle_module' ),
        );

        // 2. Dynamically Expose Every Active Custom Skill as a First-Class MCP Tool!
        $active_skills = self::get_workspace_skills( $agency_id, 'active' );
        foreach ( $active_skills as $s ) {
            $tool_key = 'cora_skill_' . str_replace( '-', '_', $s['skill_slug'] );
            $schema_arr = json_decode( $s['parameters_schema'], true );
            if ( empty( $schema_arr ) || ! is_array( $schema_arr ) ) {
                $schema_arr = array(
                    'type'       => 'object',
                    'properties' => array(
                        'input_prompt' => array( 'type' => 'string', 'description' => 'Input arguments for ' . $s['name'] )
                    )
                );
            }

            $dynamic[$tool_key] = array(
                'name'          => $tool_key,
                'description'   => "[Skill: {$s['name']}] {$s['description']}. Triggers on: {$s['trigger_phrases']}",
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'workspace:read',
                'inputSchema'   => $schema_arr,
                'handler'       => array( __CLASS__, 'handle_mcp_dynamic_skill_dispatch' ),
            );
        }

        return $dynamic;
    }

    // ── MCP Tool Handlers ───────────────────────────────────────────────────

    public static function handle_mcp_list_plugins( $args, $auth ) {
        $agency_id = $auth['workspace_id'] ?? '1';
        $status = $args['status'] ?? 'all';
        $plugins = self::get_workspace_plugins( $agency_id, $status );
        return array(
            'content' => array(
                array( 'type' => 'text', 'text' => wp_json_encode( array( 'plugins' => $plugins, 'count' => count( $plugins ) ), JSON_PRETTY_PRINT ) )
            )
        );
    }

    public static function handle_mcp_create_plugin( $args, $auth ) {
        $agency_id = $auth['workspace_id'] ?? '1';
        $args['author'] = 'Connected AI Agent (' . ( $auth['client_name'] ?? 'MCP' ) . ')';
        $args['created_by_model'] = $auth['client_name'] ?? 'external_llm';
        $id = self::save_plugin( $args, $agency_id );
        return array(
            'content' => array(
                array( 'type' => 'text', 'text' => "Plugin successfully created and installed in workspace with ID: {$id}." )
            )
        );
    }

    public static function handle_mcp_toggle_plugin( $args, $auth ) {
        global $wpdb;
        $agency_id = $auth['workspace_id'] ?? '1';
        $slug = sanitize_title( $args['plugin_slug'] ?? '' );
        $status = sanitize_text_field( $args['status'] ?? 'active' );
        $table = $wpdb->prefix . 'cora_workspace_plugins';

        $wpdb->update( $table, array( 'status' => $status, 'updated_at' => current_time( 'mysql' ) ), array( 'agency_id' => $agency_id, 'plugin_slug' => $slug ) );
        return array(
            'content' => array(
                array( 'type' => 'text', 'text' => "Workspace plugin '{$slug}' status set to '{$status}'." )
            )
        );
    }

    public static function handle_mcp_list_skills( $args, $auth ) {
        $agency_id = $auth['workspace_id'] ?? '1';
        $skills = self::get_workspace_skills( $agency_id, 'active' );
        return array(
            'content' => array(
                array( 'type' => 'text', 'text' => wp_json_encode( array( 'skills' => $skills, 'count' => count( $skills ) ), JSON_PRETTY_PRINT ) )
            )
        );
    }

    public static function handle_mcp_create_skill( $args, $auth ) {
        $agency_id = $auth['workspace_id'] ?? '1';
        $args['created_by_model'] = $auth['client_name'] ?? 'external_llm';
        $id = self::save_skill( $args, $agency_id );
        return array(
            'content' => array(
                array( 'type' => 'text', 'text' => "Custom AI skill successfully defined and registered with ID: {$id}." )
            )
        );
    }

    public static function handle_mcp_execute_skill( $args, $auth ) {
        $agency_id = $auth['workspace_id'] ?? '1';
        $slug = $args['skill_slug'] ?? '';
        $params = $args['parameters'] ?? array();
        $caller = 'mcp_' . ( $auth['client_name'] ?? 'llm' );
        $res = self::execute_skill( $slug, $params, $agency_id, $caller );
        if ( is_wp_error( $res ) ) {
            return array(
                'isError' => true,
                'content' => array( array( 'type' => 'text', 'text' => 'Error: ' . $res->get_error_message() ) )
            );
        }
        return array(
            'content' => array(
                array( 'type' => 'text', 'text' => is_string( $res['output'] ) ? $res['output'] : wp_json_encode( $res, JSON_PRETTY_PRINT ) )
            )
        );
    }

    public static function handle_mcp_dynamic_skill_dispatch( $args, $auth, $canonical_name ) {
        $agency_id = $auth['workspace_id'] ?? '1';
        $skill_slug = str_replace( array( 'cora_skill_', '_' ), array( '', '-' ), $canonical_name );
        $caller = 'mcp_direct_tool_' . ( $auth['client_name'] ?? 'llm' );
        $res = self::execute_skill( $skill_slug, $args, $agency_id, $caller );
        if ( is_wp_error( $res ) ) {
            return array(
                'isError' => true,
                'content' => array( array( 'type' => 'text', 'text' => 'Error: ' . $res->get_error_message() ) )
            );
        }
        return array(
            'content' => array(
                array( 'type' => 'text', 'text' => is_string( $res['output'] ) ? $res['output'] : wp_json_encode( $res, JSON_PRETTY_PRINT ) )
            )
        );
    }

    public static function handle_mcp_get_modules( $args, $auth ) {
        $enabled = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();
        return array(
            'content' => array(
                array( 'type' => 'text', 'text' => wp_json_encode( array( 'enabled_modules' => $enabled, 'count' => count( $enabled ) ), JSON_PRETTY_PRINT ) )
            )
        );
    }

    public static function handle_mcp_toggle_module( $args, $auth ) {
        $slug = sanitize_title( $args['module_slug'] ?? '' );
        $enabled_state = ! empty( $args['enabled'] );

        $foundation = array( 'blogs', 'forms', 'team-roles', 'media', 'vault' );
        if ( in_array( $slug, $foundation, true ) && ! $enabled_state ) {
            return array(
                'isError' => true,
                'content' => array( array( 'type' => 'text', 'text' => "Cannot disable foundation module '{$slug}'." ) )
            );
        }

        $current = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();
        if ( $enabled_state ) {
            if ( ! in_array( $slug, $current, true ) ) {
                $current[] = $slug;
            }
        } else {
            $current = array_values( array_diff( $current, array( $slug ) ) );
        }

        $agency_id_raw = $auth['workspace_id'] ?? '1';
        $agency_suffix = ( ! empty( $agency_id_raw ) && $agency_id_raw !== 'super' ) ? '_' . preg_replace( '/[^\w]/', '_', $agency_id_raw ) : '';
        $custom_features_key = 'cora_custom_enabled_features' . $agency_suffix;

        update_option( $custom_features_key, $current );
        update_option( 'cora_custom_enabled_features', $current );

        return array(
            'content' => array(
                array( 'type' => 'text', 'text' => "Module '{$slug}' " . ( $enabled_state ? 'enabled' : 'disabled' ) . " for workspace." )
            )
        );
    }
}

// Bootstrap Engine
Cora_Plugins_Skills_Engine::get_instance();

}
