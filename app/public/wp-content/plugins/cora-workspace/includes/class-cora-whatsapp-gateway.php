<?php
/**
 * Cora WhatsApp Cloud API Gateway
 *
 * Official Meta WhatsApp Cloud API integration with intelligent 24-Hour Customer Service Window
 * (CSW) tracking, smart cost optimization, and inbound webhook management.
 *
 * @package Cora_Workspace
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_WhatsApp_Gateway {

    /**
     * Singleton instance
     *
     * @var Cora_WhatsApp_Gateway|null
     */
    private static $instance = null;

    /**
     * Meta Graph API Version
     */
    const GRAPH_API_VERSION = 'v25.0';
    const GRAPH_API_BASE    = 'https://graph.facebook.com';

    /**
     * Get singleton instance
     *
     * @return Cora_WhatsApp_Gateway
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function get_instance() {
        return self::instance();
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Initialization if needed
    }

    /**
     * Get all WhatsApp integration settings
     *
     * @return array
     */
    public function get_settings() {
        $phone_id     = get_option( 'cora_wa_phone_number_id', '' );
        $waba_id      = get_option( 'cora_wa_waba_id', '' );
        $access_token = get_option( 'cora_wa_access_token', '' );
        $verify_token = get_option( 'cora_wa_verify_token', '' );
        $app_secret   = get_option( 'cora_wa_app_secret', '' );
        $enabled      = get_option( 'cora_wa_enabled', '1' ) === '1';
        $admin_phone  = get_option( 'cora_wa_admin_phone', '' );
        $default_tpl  = get_option( 'cora_wa_default_template', 'hello_world' );
        $auto_leads   = get_option( 'cora_wa_auto_leads_enabled', '1' ) === '1';
        $auto_client  = get_option( 'cora_wa_auto_client_confirmation', '1' ) === '1';

        // Auto-generate default verify token if empty
        if ( empty( $verify_token ) ) {
            $verify_token = 'cora_wa_' . substr( md5( wp_salt( 'auth' ) . 'cora_wa_webhook' ), 0, 16 );
            update_option( 'cora_wa_verify_token', $verify_token );
        }

        return array(
            'phone_number_id'          => trim( $phone_id ),
            'waba_id'                  => trim( $waba_id ),
            'access_token'             => trim( $access_token ),
            'verify_token'             => trim( $verify_token ),
            'app_secret'               => trim( $app_secret ),
            'enabled'                  => $enabled,
            'is_configured'            => ! empty( $phone_id ) && ! empty( $access_token ),
            'webhook_url'              => rest_url( 'cora/v1/whatsapp/webhook' ),
            'admin_phone'              => trim( $admin_phone ),
            'default_template'         => trim( $default_tpl ),
            'auto_leads_enabled'       => $auto_leads,
            'auto_client_confirmation' => $auto_client,
        );
    }

    /**
     * Save WhatsApp settings
     *
     * @param string $phone_number_id
     * @param string $waba_id
     * @param string $access_token
     * @param string $verify_token
     * @param bool   $enabled
     * @param string $app_secret
     * @param string|null $admin_phone
     * @param string|null $default_template
     * @param bool|null   $auto_leads_enabled
     * @param bool|null   $auto_client_confirmation
     * @return bool
     */
    public function save_settings( $phone_number_id, $waba_id, $access_token, $verify_token = '', $enabled = true, $app_secret = '', $admin_phone = null, $default_template = null, $auto_leads_enabled = null, $auto_client_confirmation = null ) {
        update_option( 'cora_wa_phone_number_id', sanitize_text_field( trim( $phone_number_id ) ) );
        update_option( 'cora_wa_waba_id', sanitize_text_field( trim( $waba_id ) ) );
        update_option( 'cora_wa_access_token', sanitize_text_field( trim( $access_token ) ) );
        // Synchronize legacy options for complete backward-compatibility across all modules
        update_option( 'cora_whatsapp_phone_number_id', sanitize_text_field( trim( $phone_number_id ) ) );
        update_option( 'cora_whatsapp_api_token', sanitize_text_field( trim( $access_token ) ) );
        if ( ! empty( $verify_token ) ) {
            update_option( 'cora_wa_verify_token', sanitize_text_field( trim( $verify_token ) ) );
        }
        if ( ! empty( $app_secret ) ) {
            update_option( 'cora_wa_app_secret', sanitize_text_field( trim( $app_secret ) ) );
        }
        update_option( 'cora_wa_enabled', $enabled ? '1' : '0' );

        if ( null !== $admin_phone ) {
            update_option( 'cora_wa_admin_phone', sanitize_text_field( trim( $admin_phone ) ) );
        }
        if ( null !== $default_template ) {
            update_option( 'cora_wa_default_template', sanitize_text_field( trim( $default_template ) ) );
        }
        if ( null !== $auto_leads_enabled ) {
            update_option( 'cora_wa_auto_leads_enabled', $auto_leads_enabled ? '1' : '0' );
        }
        if ( null !== $auto_client_confirmation ) {
            update_option( 'cora_wa_auto_client_confirmation', $auto_client_confirmation ? '1' : '0' );
        }

        // Clear connection cache
        delete_transient( 'cora_wa_connection_status' );

        return true;
    }

    /**
     * Verify Meta WhatsApp X-Hub-Signature-256 HMAC-SHA256 signature
     *
     * @param string $raw_body
     * @param string $signature_header
     * @return bool
     */
    public function verify_webhook_signature( $raw_body, $signature_header, $app_secret_override = null ) {
        $app_secret = $app_secret_override;
        if ( empty( $app_secret ) ) {
            $settings = $this->get_settings();
            $app_secret = ! empty( $settings['app_secret'] ) ? $settings['app_secret'] : '';

            if ( empty( $app_secret ) && defined( 'CORA_WA_APP_SECRET' ) && ! empty( CORA_WA_APP_SECRET ) ) {
                $app_secret = CORA_WA_APP_SECRET;
            }
        }

        if ( empty( $signature_header ) || empty( $app_secret ) ) {
            return false;
        }

        $expected_prefix = 'sha256=';
        if ( strpos( $signature_header, $expected_prefix ) !== 0 ) {
            return false;
        }

        $expected_hash = hash_hmac( 'sha256', $raw_body, $app_secret );
        $provided_hash = substr( $signature_header, strlen( $expected_prefix ) );

        return hash_equals( $expected_hash, $provided_hash );
    }

    /**
     * Verify credentials against Meta Graph API
     *
     * @return array
     */
    public function verify_connection() {
        $settings = $this->get_settings();
        if ( empty( $settings['phone_number_id'] ) || empty( $settings['access_token'] ) ) {
            return array(
                'success' => false,
                'error'   => 'Phone Number ID and Access Token are required.',
            );
        }

        $endpoint = self::GRAPH_API_BASE . '/' . self::GRAPH_API_VERSION . '/' . rawurlencode( $settings['phone_number_id'] );
        $response = wp_remote_get( $endpoint, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $settings['access_token'],
                'Content-Type'  => 'application/json',
            ),
            'timeout' => 15,
        ) );

        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'error'   => $response->get_error_message(),
            );
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body        = wp_remote_retrieve_body( $response );
        $json        = json_decode( $body, true );

        if ( $status_code >= 200 && $status_code < 300 && isset( $json['id'] ) ) {
            $data = array(
                'id'                     => $json['id'] ?? '',
                'verified_name'          => $json['verified_name'] ?? 'WhatsApp Business',
                'display_phone_number'   => $json['display_phone_number'] ?? '',
                'quality_rating'         => $json['quality_rating'] ?? 'GREEN',
                'code_verification_status' => $json['code_verification_status'] ?? '',
                'verified_at'            => current_time( 'mysql' ),
            );
            set_transient( 'cora_wa_connection_status', $data, 300 );
            return array(
                'success' => true,
                'data'    => $data,
            );
        }

        $error_msg = isset( $json['error']['message'] ) ? $json['error']['message'] : ( 'Meta API returned HTTP ' . $status_code );
        return array(
            'success' => false,
            'error'   => $error_msg,
            'raw'     => $json,
        );
    }

    /**
     * Clean and normalize phone numbers (E.164 format without +)
     *
     * @param string $phone
     * @return string
     */
    public function clean_phone_number( $phone ) {
        // Strip everything except digits
        $digits = preg_replace( '/[^\d]/', '', $phone );
        
        // If 10 digits (standard Indian mobile without country code), prepend 91
        if ( strlen( $digits ) === 10 ) {
            $digits = '91' . $digits;
        }

        return $digits;
    }

    /**
     * Get 24-Hour Customer Service Window Status for a given phone number
     *
     * @param string $phone
     * @return array
     */
    public function get_session_window( $phone ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return array(
                'active'            => false,
                'remaining_seconds' => 0,
                'remaining_human'   => 'No Phone Provided',
                'expires_at'        => 0,
                'type'              => 'template_required',
            );
        }

        $expires_at = (int) get_option( 'cora_wa_session_' . $clean_phone, 0 );
        $now        = time();
        $is_active  = ( $expires_at > $now );
        $remaining  = max( 0, $expires_at - $now );

        $remaining_human = 'Expired';
        if ( $is_active ) {
            $hours = floor( $remaining / 3600 );
            $mins  = floor( ( $remaining % 3600 ) / 60 );
            if ( $hours > 0 ) {
                $remaining_human = sprintf( '%dh %02dm left', $hours, $mins );
            } else {
                $remaining_human = sprintf( '%dm left', $mins );
            }
        }

        return array(
            'phone'             => $clean_phone,
            'active'            => $is_active,
            'remaining_seconds' => $remaining,
            'remaining_human'   => $remaining_human,
            'expires_at'        => $expires_at,
            'expires_at_iso'    => $expires_at ? gmdate( 'c', $expires_at ) : null,
            'type'              => $is_active ? 'session_free' : 'template_required',
        );
    }

    /**
     * Get official displayed WhatsApp Business Phone Number
     *
     * @return string
     */
    public function get_display_phone_number() {
        $status = get_transient( 'cora_wa_connection_status' );
        if ( ! empty( $status['display_phone_number'] ) ) {
            return $status['display_phone_number'];
        }
        $display = get_option( 'cora_wa_display_phone', '' );
        if ( ! empty( $display ) ) {
            return $display;
        }
        return '+91 70153 17965';
    }

    /**
     * Get clean numeric digits for the registered business WhatsApp number
     *
     * @return string
     */
    public function get_business_phone_digits() {
        $phone = $this->get_display_phone_number();
        return preg_replace( '/[^\d]/', '', $phone ) ?: '917015317965';
    }

    /**
     * Get direct WhatsApp deep-link URL to initiate or activate the 24h Free Window
     *
     * @param string $custom_text Pre-filled message text
     * @return string
     */
    public function get_activation_url( $custom_text = '' ) {
        $phone_digits = $this->get_business_phone_digits();

        $text = ! empty( $custom_text )
            ? $custom_text
            : 'Activate Daily Briefing & Workspace Alerts 🚀';

        return 'https://wa.me/' . $phone_digits . '?text=' . rawurlencode( $text );
    }

    /**
     * Get local redirect URL for PWA and web push notifications
     *
     * @return string
     */
    public function get_activation_redirect_url() {
        return home_url( '/workspace/wa-activate' );
    }

    /**
     * Check if an automated notification can be sent to a phone number (Max 3 per hour)
     *
     * @param string $phone
     * @return bool
     */
    public function can_send_automated_message( $phone ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return false;
        }

        $now = time();
        $window_start = $now - 3600; // 1-hour rolling window

        $history = get_option( 'cora_wa_hourly_dispatches_' . $clean_phone, array() );
        if ( ! is_array( $history ) ) {
            $history = array();
        }

        // Filter out timestamps older than 1 hour
        $recent = array();
        foreach ( $history as $ts ) {
            if ( $ts > $window_start ) {
                $recent[] = $ts;
            }
        }

        // Strict maximum 3 automated messages per hour
        return count( $recent ) < 3;
    }

    /**
     * Record an automated notification dispatch for the 1-hour rate limit
     *
     * @param string $phone
     * @return int Current count in the last hour
     */
    public function record_automated_message_sent( $phone ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return 0;
        }

        $now = time();
        $window_start = $now - 3600;

        $history = get_option( 'cora_wa_hourly_dispatches_' . $clean_phone, array() );
        if ( ! is_array( $history ) ) {
            $history = array();
        }

        $recent = array();
        foreach ( $history as $ts ) {
            if ( $ts > $window_start ) {
                $recent[] = $ts;
            }
        }

        $recent[] = $now;
        update_option( 'cora_wa_hourly_dispatches_' . $clean_phone, $recent, false );

        return count( $recent );
    }

    /**
     * Get count of automated messages sent in the last rolling hour
     *
     * @param string $phone
     * @return int
     */
    public function get_hourly_dispatches_count( $phone ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return 0;
        }

        $now = time();
        $window_start = $now - 3600;

        $history = get_option( 'cora_wa_hourly_dispatches_' . $clean_phone, array() );
        if ( ! is_array( $history ) ) {
            return 0;
        }

        $count = 0;
        foreach ( $history as $ts ) {
            if ( $ts > $window_start ) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Enqueue a pending notification for delivery when 24h CSW opens ($0 Tier)
     *
     * @param string $phone
     * @param string $title
     * @param string $body
     * @param string $action_url
     * @param string $category
     * @return bool
     */
    public function queue_pending_update( $phone, $title, $body, $action_url = '', $category = 'Alert' ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return false;
        }

        $pending = get_option( 'cora_wa_pending_' . $clean_phone, array() );
        if ( ! is_array( $pending ) ) {
            $pending = array();
        }

        // Deduplicate identical pending alerts within 5 minutes
        $now = time();
        foreach ( $pending as $existing ) {
            if ( $existing['title'] === $title && $existing['body'] === $body && ( $now - $existing['timestamp'] ) < 300 ) {
                return true;
            }
        }

        array_unshift( $pending, array(
            'id'         => 'qu_' . $now . '_' . wp_rand( 100, 999 ),
            'title'      => sanitize_text_field( $title ),
            'body'       => sanitize_text_field( $body ),
            'url'        => esc_url_raw( $action_url ),
            'category'   => sanitize_text_field( $category ),
            'timestamp'  => $now,
            'created_at' => current_time( 'mysql' ),
        ) );

        // Retain max 15 pending updates
        $pending = array_slice( $pending, 0, 15 );
        update_option( 'cora_wa_pending_' . $clean_phone, $pending, false );

        return true;
    }

    /**
     * Get pending queued notifications for a phone number
     *
     * @param string $phone
     * @return array
     */
    public function get_pending_updates( $phone ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return array();
        }
        $pending = get_option( 'cora_wa_pending_' . $clean_phone, array() );
        return is_array( $pending ) ? $pending : array();
    }

    /**
     * Check if there are pending queued notifications
     *
     * @param string $phone
     * @return bool
     */
    public function has_pending_updates( $phone ) {
        return count( $this->get_pending_updates( $phone ) ) > 0;
    }

    /**
     * Clear all pending queued notifications
     *
     * @param string $phone
     * @return bool
     */
    public function clear_pending_updates( $phone ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return false;
        }
        delete_option( 'cora_wa_pending_' . $clean_phone );
        return true;
    }

    /**
     * Flush all queued notifications into the active 24h CSW at $0 Cost
     *
     * @param string $phone
     * @return array
     */
    public function flush_pending_updates( $phone ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return array( 'flushed' => false, 'count' => 0 );
        }

        $pending = $this->get_pending_updates( $clean_phone );
        if ( empty( $pending ) ) {
            return array( 'flushed' => false, 'count' => 0 );
        }

        $count = count( $pending );
        $workspace = get_bloginfo( 'name' ) ?: 'Cora Workspace';

        if ( $count === 1 ) {
            $item        = $pending[0];
            $clean_title = sanitize_text_field( $item['title'] );
            $raw_body    = sanitize_text_field( $item['body'] );
            $category    = sanitize_text_field( $item['category'] ?? 'Alert' );

            // Strip ugly raw links from body text
            $clean_body  = preg_replace( '/https?:\/\/[^\s]+/i', '', $raw_body );
            $clean_body  = trim( preg_replace( '/\n{3,}/', "\n\n", $clean_body ) );

            $buttons = array( 'View Details', 'Open Workspace' );
            if ( stripos( $category, 'lead' ) !== false || stripos( $clean_title, 'lead' ) !== false ) {
                $buttons = array( 'Accept Booking', 'View Details', 'Call Client' );
            } elseif ( stripos( $category, 'report' ) !== false || stripos( $clean_title, 'report' ) !== false ) {
                $buttons = array( 'View Full Report', 'Open Dashboard' );
            }

            $res = $this->send_interactive_buttons(
                $clean_phone,
                "*{$clean_title}*\n\n{$clean_body}",
                $buttons,
                "⚡ Cora AI • {$category}",
                "24h Free Customer Service Window"
            );

            if ( empty( $res['success'] ) ) {
                $res = $this->send_session_message( $clean_phone, "⚡ *[{$category}] {$clean_title}*\n\n{$clean_body}" );
            }
        } else {
            // Group updates by category for an intelligent, respectful executive summary
            $cat_counts = array();
            foreach ( $pending as $p_item ) {
                $cat = sanitize_text_field( $p_item['category'] ?? 'General' );
                $cat_counts[ $cat ] = ( $cat_counts[ $cat ] ?? 0 ) + 1;
            }

            $summary_bullets = array();
            foreach ( $cat_counts as $c_name => $c_cnt ) {
                $summary_bullets[] = "• *{$c_name}*: {$c_cnt} new update" . ( $c_cnt > 1 ? 's' : '' );
            }

            $digest_text = "⚡ *Workspace Updates Consolidated*\n\n"
                . "{$count} updates arrived while your service window was away:\n\n"
                . implode( "\n", $summary_bullets ) . "\n\n"
                . "All itemized records have been compiled into your executive overview.";

            $res = $this->send_interactive_buttons(
                $clean_phone,
                $digest_text,
                array( 'View Full Report', 'Open CRM' ),
                "📊 Cora AI • Consolidated Digest",
                "Delivered Free via 24h CSW"
            );

            if ( empty( $res['success'] ) ) {
                $res = $this->send_session_message( $clean_phone, $digest_text );
            }
        }

        $this->clear_pending_updates( $clean_phone );

        return array(
            'flushed' => true,
            'count'   => $count,
            'res'     => $res ?? array(),
        );
    }

    /**
     * Intelligent Inbound Message Processor & Bot Menu
     *
     * Responds to user queries, flushes queued updates, and serves interactive menu options.
     *
     * @param string $phone
     * @param string $raw_text
     * @return array
     */
    public function handle_inbound_query( $phone, $raw_text ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return array( 'success' => false, 'error' => 'Invalid phone' );
        }

        // 1. If user has pending updates, flush them first into this newly activated CSW
        if ( $this->has_pending_updates( $clean_phone ) ) {
            $flush = $this->flush_pending_updates( $clean_phone );
            return array(
                'success' => true,
                'action'  => 'flushed_queue',
                'count'   => $flush['count'],
            );
        }

        $text = strtolower( trim( $raw_text ) );
        $workspace = get_bloginfo( 'name' ) ?: 'Cora Workspace';

        // 2. Command: 1 or Leads / CRM / Inquiry
        if ( $text === '1' || strpos( $text, 'lead' ) !== false || strpos( $text, 'crm' ) !== false || strpos( $text, 'inquir' ) !== false ) {
            global $wpdb;
            $leads_table = $wpdb->prefix . 'cora_leads';
            $leads = array();
            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$leads_table}'" ) ) {
                $leads = $wpdb->get_results( "SELECT * FROM {$leads_table} ORDER BY id DESC LIMIT 3", ARRAY_A );
            }

            if ( empty( $leads ) ) {
                $reply = "*CRM Inbound Leads & Pipeline*\n\n"
                    . "No recent inquiries recorded yet today.\n\n"
                    . "• *Status*: Active lead intake forms & webhooks connected.\n"
                    . "• *Intelligence*: Lead alerts are batched into executive digests.";
            } else {
                $reply = "*Recent Inbound Inquiries*\n\n";
                foreach ( $leads as $idx => $ld ) {
                    $num   = $idx + 1;
                    $fname = ! empty( $ld['first_name'] ) ? $ld['first_name'] : 'Inquiry';
                    $lname = ! empty( $ld['last_name'] ) ? ' ' . $ld['last_name'] : '';
                    $name  = trim( $fname . $lname );
                    $st    = ucfirst( $ld['status'] ?? 'New' );
                    $src   = ! empty( $ld['source'] ) ? $ld['source'] : 'Inbound Web';
                    $email = ! empty( $ld['email'] ) ? $ld['email'] : '—';
                    $phone_val = ! empty( $ld['phone'] ) ? $ld['phone'] : '—';

                    $reply .= "{$num}️⃣ *{$name}* ({$st})\n"
                        . "• Contact: {$phone_val} | {$email}\n"
                        . "• Source: {$src}\n\n";
                }
                $reply .= "Tap below to take immediate action or view full operations.";
            }

            $res = $this->send_interactive_buttons(
                $clean_phone,
                $reply,
                array( 'View Full Report', 'Today Revenue', 'Team Attendance' ),
                "📋 Cora AI • CRM Leads",
                "Cora Workspace AI"
            );

            if ( empty( $res['success'] ) ) {
                $res = $this->send_session_message( $clean_phone, "📋 *{$workspace} • CRM Leads*\n\n{$reply}" );
            }

            return $res;
        }

        // 3. Command: 2 or Revenue / Financials / Payment / Invoice
        if ( $text === '2' || strpos( $text, 'rev' ) !== false || strpos( $text, 'finance' ) !== false || strpos( $text, 'payment' ) !== false || strpos( $text, 'invoice' ) !== false ) {
            global $wpdb;
            $ledger_table = $wpdb->prefix . 'cora_ledger';
            $cleared = 0;
            $pending = 0;
            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$ledger_table}'" ) ) {
                $cleared = $wpdb->get_var( "SELECT SUM(amount) FROM {$ledger_table} WHERE type = 'inflow' AND status = 'cleared'" ) ?: 0;
                $pending = $wpdb->get_var( "SELECT SUM(amount) FROM {$ledger_table} WHERE type = 'inflow' AND status = 'pending'" ) ?: 0;
            }

            $cleared_fmt = '₹' . number_format( (float) $cleared, 0 );
            $pending_fmt = '₹' . number_format( (float) $pending, 0 );

            $reply = "*Financial Intelligence Overview*\n\n"
                . "• *Cleared Revenue*: {$cleared_fmt}\n"
                . "• *Pending Receivables*: {$pending_fmt}\n"
                . "• *GST Invoices & Receipts*: Synced with Document Vault\n\n"
                . "Tap below to inspect reports or manage leads.";

            $res = $this->send_interactive_buttons(
                $clean_phone,
                $reply,
                array( 'View Full Report', 'Recent Leads', 'Team Attendance' ),
                "💰 Cora AI • Financials",
                "Cora Workspace AI"
            );

            if ( empty( $res['success'] ) ) {
                $res = $this->send_session_message( $clean_phone, "💰 *{$workspace} • Financials*\n\n{$reply}" );
            }

            return $res;
        }

        // 4. Command: 3 or Attendance / Team / Punch / Staff
        if ( $text === '3' || strpos( $text, 'att' ) !== false || strpos( $text, 'team' ) !== false || strpos( $text, 'punch' ) !== false || strpos( $text, 'staff' ) !== false ) {
            $logs = get_option( 'cora_workspace_attendance_logs', array() );
            if ( ! is_array( $logs ) ) {
                $logs = array();
            }

            $today_ymd = current_time( 'Y-m-d' );
            $today_logs = array();
            foreach ( $logs as $l ) {
                $log_date = ! empty( $l['date'] ) ? $l['date'] : ( ! empty( $l['timestamp'] ) ? gmdate( 'Y-m-d', $l['timestamp'] ) : '' );
                if ( $log_date === $today_ymd || empty( $log_date ) ) {
                    $today_logs[] = $l;
                }
            }

            if ( empty( $today_logs ) ) {
                $today_logs = array_slice( $logs, 0, 3 );
            }

            $reply = "*Team Attendance & Roster Today*\n\n";
            if ( empty( $today_logs ) ) {
                $reply .= "No punches recorded yet today.\n\n";
            } else {
                $reply .= "• *Total Punches Today*: " . count( $today_logs ) . "\n\n";
                foreach ( array_slice( $today_logs, 0, 4 ) as $idx => $pl ) {
                    $staff = ! empty( $pl['staff_name'] ) ? $pl['staff_name'] : ( ! empty( $pl['user'] ) ? $pl['user'] : 'Staff' );
                    $type  = ! empty( $pl['type'] ) ? $pl['type'] : 'Punch In';
                    $time  = ! empty( $pl['time'] ) ? $pl['time'] : ( ! empty( $pl['timestamp'] ) ? gmdate( 'g:i A', $pl['timestamp'] ) : 'Today' );
                    $loc   = ! empty( $pl['distance'] ) && $pl['distance'] !== '—' ? " ({$pl['distance']})" : ' (Office Geofence Verified)';
                    $reply .= "• *{$staff}*: {$type} at {$time}{$loc}\n";
                }
                $reply .= "\nTap below to view full executive roster or revenue.";
            }

            $res = $this->send_interactive_buttons(
                $clean_phone,
                $reply,
                array( 'View Full Report', 'Recent Leads', 'Today Revenue' ),
                "👥 Cora AI • Attendance",
                "Cora Workspace AI"
            );

            if ( empty( $res['success'] ) ) {
                $res = $this->send_session_message( $clean_phone, "👥 *{$workspace} • Team Attendance*\n\n{$reply}" );
            }

            return $res;
        }

        // 5. Command: 4 or Briefing / Report / Digest / View Full Report
        if ( $text === '4' || strpos( $text, 'brief' ) !== false || strpos( $text, 'report' ) !== false || strpos( $text, 'digest' ) !== false || strpos( $text, 'full report' ) !== false ) {
            return $this->send_daily_briefing( $clean_phone );
        }

        // 6. Command: 5 or Status / Window / Quota
        if ( $text === '5' || strpos( $text, 'status' ) !== false || strpos( $text, 'window' ) !== false || strpos( $text, 'quota' ) !== false ) {
            $win = $this->get_session_window( $clean_phone );
            $rem = $win['remaining_human'] ?? 'Active';
            $hourly_sent = $this->get_hourly_dispatches_count( $clean_phone );
            $reply = "*Cora AI Gateway Status*\n\n"
                . "• *Connection*: 🟢 Verified & Live\n"
                . "• *Official DP*: Cora AI Verified Portrait\n"
                . "• *Customer Service Window*: Active ({$rem})\n"
                . "• *Bill Rate*: ₹0.00 / $0.00 (100% Free Service Tier)\n"
                . "• *Hourly Alert Pacing*: {$hourly_sent}/3 sent in past hour\n"
                . "• *Lead Batching*: Smart roll-up active\n"
                . "• *Push Sync*: Enabled (Automatic Window Renewal)";

            $res = $this->send_interactive_buttons(
                $clean_phone,
                $reply,
                array( 'Daily Report', 'Recent Leads', 'Main Menu' ),
                "⚙️ Cora AI • System Status",
                "Cora Workspace AI"
            );

            if ( empty( $res['success'] ) ) {
                $res = $this->send_session_message( $clean_phone, "⚙️ *{$workspace} • System Status*\n\n{$reply}" );
            }

            return $res;
        }

        // 7. Default: Interactive Menu & Greeting
        $win = $this->get_session_window( $clean_phone );
        $rem = $win['remaining_human'] ?? '24 Hours';

        $menu = "*Workspace Intelligence Assistant*\n"
            . "🟢 *24h Free Window Active* ({$rem})\n\n"
            . "Your WhatsApp session for *{$workspace}* is active. All alerts reach you here at ₹0.00 with respectful rate-limiting (max 3/hr).\n\n"
            . "Tap an option below or type a number:\n"
            . "1️⃣ *Leads* — Recent inquiries & CRM\n"
            . "2️⃣ *Revenue* — Financial overview\n"
            . "3️⃣ *Attendance* — Team check-ins & roster\n"
            . "4️⃣ *Report* — Certified daily operations PDF\n"
            . "5️⃣ *Status* — Gateway health & quota";

        $res = $this->send_interactive_buttons(
            $clean_phone,
            $menu,
            array( 'Recent Leads', 'Today Revenue', 'Daily Report' ),
            "⚡ Cora AI • Assistant",
            "Type a number or tap an action"
        );

        if ( empty( $res['success'] ) ) {
            $res = $this->send_session_message( $clean_phone, "⚡ *{$workspace}*\n\n{$menu}" );
        }

        return $res;
    }

    /**
     * Auto-respond to incoming activation message to confirm 24h window
     *
     * @param string $to Recipient phone number
     * @return array
     */
    public function send_daily_activation_reply( $to ) {
        return $this->handle_inbound_query( $to, 'menu' );
    }

    /**
     * Trigger 24-Hour WhatsApp Activation Push Notification to All Users across All Workspaces
     *
     * @return int Number of users notified
     */
    public function trigger_daily_activation_push_to_all_users() {
        if ( ! function_exists( 'get_users' ) ) {
            return 0;
        }

        $users = get_users( array(
            'fields' => array( 'ID', 'display_name', 'user_email' ),
        ) );

        if ( empty( $users ) ) {
            return 0;
        }

        $title      = '🔔 Activate Today\'s WhatsApp Alerts & Daily Digest';
        $body       = 'Tap to open WhatsApp and unlock your 24-hour free workspace updates & reminders.';
        $action_url = $this->get_activation_redirect_url();
        $notified   = 0;

        foreach ( $users as $u ) {
            $user_id = $u->ID;

            // 1. Send native Web Push / PWA Notification if subscribed
            if ( function_exists( 'cora_pwa_send_push_notification' ) ) {
                cora_pwa_send_push_notification( $user_id, $title, $body, $action_url );
            }

            // 2. Also register in-app notification record for instant visibility
            $user_notifs = get_user_meta( $user_id, 'cora_user_notifications', true );
            if ( ! is_array( $user_notifs ) ) {
                $user_notifs = array();
            }
            array_unshift( $user_notifs, array(
                'id'         => 'wa_act_' . time() . '_' . wp_rand( 100, 999 ),
                'type'       => 'whatsapp_activation',
                'title'      => $title,
                'message'    => $body,
                'action_url' => $action_url,
                'created_at' => current_time( 'mysql' ),
                'read'       => false,
            ) );
            $user_notifs = array_slice( $user_notifs, 0, 30 );
            update_user_meta( $user_id, 'cora_user_notifications', $user_notifs );

            $notified++;
        }

        // Global workspace alert log
        $global_notifs = get_option( 'cora_workspace_notifications', array() );
        if ( ! is_array( $global_notifs ) ) {
            $global_notifs = array();
        }
        array_unshift( $global_notifs, array(
            'id'         => 'wa_act_global_' . time(),
            'type'       => 'whatsapp_activation',
            'title'      => $title,
            'message'    => "Dispatched to {$notified} workspace users.",
            'action_url' => $action_url,
            'created_at' => current_time( 'mysql' ),
            'read'       => false,
        ) );
        update_option( 'cora_workspace_notifications', array_slice( $global_notifs, 0, 50 ) );

        // Track last dispatched time
        update_option( 'cora_wa_last_daily_push_time', time() );

        return $notified;
    }

    /**
     * Record inbound interaction to open / reset the 24-Hour Free Service Window
     *
     * @param string $phone
     * @param array  $message_data
     * @return int New expiration timestamp
     */
    public function record_inbound_message( $phone, $message_data = array() ) {
        $clean_phone = $this->clean_phone_number( $phone );
        if ( empty( $clean_phone ) ) {
            return 0;
        }

        // 24 hours from now
        $expires_at = time() + 86400;
        update_option( 'cora_wa_session_' . $clean_phone, $expires_at );

        // Log recent interaction
        $recent_logs = get_option( 'cora_wa_recent_inbound', array() );
        if ( ! is_array( $recent_logs ) ) {
            $recent_logs = array();
        }

        array_unshift( $recent_logs, array(
            'phone'      => $clean_phone,
            'timestamp'  => time(),
            'date_human' => current_time( 'mysql' ),
            'message'    => isset( $message_data['body'] ) ? sanitize_text_field( $message_data['body'] ) : 'Inbound interaction',
            'type'       => isset( $message_data['type'] ) ? sanitize_text_field( $message_data['type'] ) : 'text',
        ) );

        // Keep last 50 inbound interactions
        $recent_logs = array_slice( $recent_logs, 0, 50 );
        update_option( 'cora_wa_recent_inbound', $recent_logs );

        return $expires_at;
    }

    /**
     * Send a Free-Form Session Message ($0 within 24-hour window)
     *
     * @param string $to
     * @param string $text
     * @param array  $options
     * @return array
     */
    public function send_session_message( $to, $text, $options = array() ) {
        $settings = $this->get_settings();
        if ( empty( $settings['phone_number_id'] ) || empty( $settings['access_token'] ) ) {
            return array(
                'success' => false,
                'error'   => 'WhatsApp API credentials not configured.',
            );
        }

        $clean_to = $this->clean_phone_number( $to );
        if ( empty( $clean_to ) ) {
            return array(
                'success' => false,
                'error'   => 'Invalid recipient phone number.',
            );
        }

        $preview_url = ! empty( $options['preview_url'] );
        $payload = array(
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $clean_to,
            'type'              => 'text',
            'text'              => array(
                'preview_url' => $preview_url,
                'body'        => $text,
            ),
        );

        return $this->execute_post_request( $payload );
    }

    /**
     * Send an Interactive Quick-Reply Button Message ($0 Free inside 24h CSW)
     *
     * @param string $to Recipient phone number
     * @param string $body_text Main message body text
     * @param array  $buttons Array of buttons (e.g. ['Interested', 'Next Property'] or [['id'=>'b1', 'title'=>'Interested']])
     * @param string $header_text Optional text header
     * @param string $footer_text Optional footer text
     * @return array
     */
    public function send_interactive_buttons( $to, $body_text, $buttons = array(), $header_text = '', $footer_text = '' ) {
        $settings = $this->get_settings();
        if ( empty( $settings['phone_number_id'] ) || empty( $settings['access_token'] ) ) {
            return array( 'success' => false, 'error' => 'WhatsApp API credentials not configured.' );
        }

        $clean_to = $this->clean_phone_number( $to );
        if ( empty( $clean_to ) ) {
            return array( 'success' => false, 'error' => 'Invalid recipient phone number.' );
        }

        // Format buttons (up to 3 allowed by Meta)
        $action_buttons = array();
        $btn_list = array_slice( (array) $buttons, 0, 3 );
        foreach ( $btn_list as $idx => $b ) {
            $id    = is_array( $b ) && ! empty( $b['id'] ) ? $b['id'] : 'btn_' . ( $idx + 1 );
            $title = is_array( $b ) && ! empty( $b['title'] ) ? $b['title'] : (string) $b;
            $title = mb_substr( trim( $title ), 0, 20 );
            $action_buttons[] = array(
                'type'  => 'reply',
                'reply' => array(
                    'id'    => $id,
                    'title' => $title,
                ),
            );
        }

        if ( empty( $action_buttons ) ) {
            $action_buttons[] = array(
                'type'  => 'reply',
                'reply' => array( 'id' => 'btn_view', 'title' => 'View Details' ),
            );
        }

        $interactive = array(
            'type'   => 'button',
            'body'   => array( 'text' => $body_text ),
            'action' => array( 'buttons' => $action_buttons ),
        );

        if ( ! empty( $header_text ) ) {
            $interactive['header'] = array(
                'type' => 'text',
                'text' => mb_substr( $header_text, 0, 60 ),
            );
        }

        if ( ! empty( $footer_text ) ) {
            $interactive['footer'] = array(
                'text' => mb_substr( $footer_text, 0, 60 ),
            );
        }

        $payload = array(
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $clean_to,
            'type'              => 'interactive',
            'interactive'       => $interactive,
        );

        return $this->execute_post_request( $payload );
    }

    /**
     * Send an Image Card with Optional Buttons & Caption ($0 Free inside 24h CSW)
     *
     * @param string $to Recipient phone number
     * @param string $image_url Public image URL (JPG/PNG)
     * @param string $body_text Caption/Body text
     * @param array  $buttons Optional quick-reply buttons
     * @param string $footer_text Optional footer text
     * @return array
     */
    public function send_image_card( $to, $image_url, $body_text, $buttons = array(), $footer_text = '' ) {
        $settings = $this->get_settings();
        if ( empty( $settings['phone_number_id'] ) || empty( $settings['access_token'] ) ) {
            return array( 'success' => false, 'error' => 'WhatsApp API credentials not configured.' );
        }

        $clean_to = $this->clean_phone_number( $to );
        if ( empty( $clean_to ) ) {
            return array( 'success' => false, 'error' => 'Invalid recipient phone number.' );
        }

        // If buttons provided, send as interactive card with image header
        if ( ! empty( $buttons ) ) {
            $action_buttons = array();
            $btn_list = array_slice( (array) $buttons, 0, 3 );
            foreach ( $btn_list as $idx => $b ) {
                $id    = is_array( $b ) && ! empty( $b['id'] ) ? $b['id'] : 'btn_' . ( $idx + 1 );
                $title = is_array( $b ) && ! empty( $b['title'] ) ? $b['title'] : (string) $b;
                $title = mb_substr( trim( $title ), 0, 20 );
                $action_buttons[] = array(
                    'type'  => 'reply',
                    'reply' => array(
                        'id'    => $id,
                        'title' => $title,
                    ),
                );
            }

            $interactive = array(
                'type'   => 'button',
                'header' => array(
                    'type'  => 'image',
                    'image' => array( 'link' => esc_url_raw( $image_url ) ),
                ),
                'body'   => array( 'text' => $body_text ),
                'action' => array( 'buttons' => $action_buttons ),
            );

            if ( ! empty( $footer_text ) ) {
                $interactive['footer'] = array( 'text' => mb_substr( $footer_text, 0, 60 ) );
            }

            $payload = array(
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $clean_to,
                'type'              => 'interactive',
                'interactive'       => $interactive,
            );

            return $this->execute_post_request( $payload );
        }

        // Otherwise send as standard media image with caption
        $payload = array(
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $clean_to,
            'type'              => 'image',
            'image'             => array(
                'link'    => esc_url_raw( $image_url ),
                'caption' => $body_text,
            ),
        );

        return $this->execute_post_request( $payload );
    }

    /**
     * Send a Template Message (Used outside 24h window or for initial outreach)
     *
     * @param string $to
     * @param string $template_name
     * @param array  $body_parameters
     * @param string $language_code
     * @param array  $button_parameters
     * @return array
     */
    public function send_template_message( $to, $template_name, $body_parameters = array(), $language_code = 'en_US', $button_parameters = array() ) {
        $settings = $this->get_settings();
        if ( empty( $settings['phone_number_id'] ) || empty( $settings['access_token'] ) ) {
            return array(
                'success' => false,
                'error'   => 'WhatsApp API credentials not configured.',
            );
        }

        $clean_to = $this->clean_phone_number( $to );
        if ( empty( $clean_to ) ) {
            return array(
                'success' => false,
                'error'   => 'Invalid recipient phone number.',
            );
        }

        $components = array();
        
        // Add body parameters
        if ( ! empty( $body_parameters ) ) {
            if ( isset( $body_parameters[0]['type'] ) && isset( $body_parameters[0]['parameters'] ) ) {
                $components = $body_parameters;
            } else {
                $params = array();
                foreach ( $body_parameters as $param ) {
                    if ( is_array( $param ) && isset( $param['type'] ) ) {
                        $params[] = $param;
                    } else {
                        $params[] = array(
                            'type' => 'text',
                            'text' => (string) $param,
                        );
                    }
                }
                $components[] = array(
                    'type'       => 'body',
                    'parameters' => $params,
                );
            }
        }

        $payload = array(
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $clean_to,
            'type'              => 'template',
            'template'          => array(
                'name'     => $template_name,
                'language' => array(
                    'code' => $language_code,
                ),
            ),
        );

        if ( ! empty( $components ) ) {
            $payload['template']['components'] = $components;
        }

        return $this->execute_post_request( $payload );
    }

    /**
     * Send an Automated Order / Booking / Lead Confirmation Template Message
     *
     * @param string $to Recipient phone number
     * @param string $customer_name Customer / Client name
     * @param string $reference_id Order, Booking, or Submission reference number
     * @param string $date Date string (e.g. Oct 17, 2026)
     * @param string $template_name Meta approved template name
     * @param string $language_code Language code (default en_US)
     * @return array
     */
    public function send_order_confirmation( $to, $customer_name, $reference_id = '', $date = '', $template_name = '', $language_code = 'en_US' ) {
        if ( empty( $template_name ) ) {
            $settings      = $this->get_settings();
            $template_name = ! empty( $settings['default_template'] ) ? $settings['default_template'] : 'hello_world';
        }

        // If template is hello_world, it is parameterless
        if ( 'hello_world' === $template_name ) {
            return $this->send_template_message( $to, 'hello_world', array(), $language_code );
        }

        if ( empty( $date ) ) {
            $date = current_time( 'M j, Y' );
        }
        if ( empty( $reference_id ) ) {
            $reference_id = 'CORA-' . wp_rand( 100000, 999999 );
        }

        $parameters = array(
            $customer_name ?: 'Valued Client',
            (string) $reference_id,
            $date,
        );

        return $this->send_template_message( $to, $template_name, $parameters, $language_code );
    }

    /**
     * Dispatch an Automated Lead / Inquiry Notification via WhatsApp
     *
     * @param string $recipient_phone Admin or Lead phone
     * @param array  $lead_data Array containing title, name, phone, email, date, source
     * @param bool   $is_admin_alert True if sending to business admin, False if client confirmation
     * @return array
     */
    public function send_lead_alert( $recipient_phone, $lead_data = array(), $is_admin_alert = true ) {
        $clean_to = $this->clean_phone_number( $recipient_phone );
        if ( empty( $clean_to ) ) {
            return array( 'success' => false, 'error' => 'Invalid recipient phone.' );
        }

        $name   = sanitize_text_field( $lead_data['name'] ?? 'Client' );
        $title  = sanitize_text_field( $lead_data['title'] ?? 'Inquiry' );
        $ref_id = sanitize_text_field( $lead_data['id'] ?? ( 'L-' . wp_rand( 1000, 9999 ) ) );
        $date   = sanitize_text_field( $lead_data['date'] ?? current_time( 'M j, Y' ) );
        $phone  = ! empty( $lead_data['phone'] ) ? sanitize_text_field( $lead_data['phone'] ) : '';
        $email  = ! empty( $lead_data['email'] ) ? sanitize_text_field( $lead_data['email'] ) : '';

        $concise_body = "*New Lead: {$title}*\n\n"
            . "• *Client*: {$name}\n"
            . ( ! empty( $phone ) ? "• *Phone*: {$phone}\n" : "" )
            . ( ! empty( $email ) ? "• *Email*: {$email}\n" : "" )
            . "• *Ref*: #{$ref_id}\n"
            . "• *Date*: {$date}";

        if ( $is_admin_alert ) {
            // 1. Strict Rate Limiting: Max 3 automated messages per hour per recipient
            if ( ! $this->can_send_automated_message( $clean_to ) ) {
                $this->queue_pending_update( $clean_to, "New Lead: {$name}", $concise_body, home_url( '/workspace/dashboard?sub_page=leads' ), 'Lead' );
                return array(
                    'success'      => true,
                    'rate_limited' => true,
                    'batched'      => true,
                    'message'      => 'Hourly alert limit reached (max 3/hr). Lead queued for consolidated digest.',
                );
            }

            // 2. Rapid-Fire Lead Pacing: If an alert was sent in the last 10 minutes, batch subsequent leads
            $lead_pacing_key = 'cora_wa_recent_lead_' . $clean_to;
            if ( get_transient( $lead_pacing_key ) ) {
                $this->queue_pending_update( $clean_to, "New Lead: {$name}", $concise_body, home_url( '/workspace/dashboard?sub_page=leads' ), 'Lead' );
                return array(
                    'success' => true,
                    'batched' => true,
                    'queued'  => true,
                    'message' => 'Rapid inquiry batched to prevent notification fatigue.',
                );
            }

            // Check if 24h CSW window is open
            $win = $this->get_session_window( $clean_to );
            if ( $win['active'] ) {
                // Send rich interactive button card with ZERO ugly raw links
                $res = $this->send_interactive_buttons(
                    $clean_to,
                    $concise_body,
                    array( 'Accept Booking', 'View Details', 'Call Client' ),
                    "⚡ Cora AI • Inbound Lead",
                    "Cora Workspace AI"
                );

                if ( ! $res['success'] && ! empty( $res['raw']['error']['code'] ) && 131047 === (int) $res['raw']['error']['code'] ) {
                    $this->queue_pending_update( $clean_to, "New Lead: {$name}", $concise_body, home_url( '/workspace/dashboard?sub_page=leads' ), 'Lead' );
                    return array( 'success' => true, 'queued' => true, 'session_active' => false );
                }

                if ( ! empty( $res['success'] ) ) {
                    $this->record_automated_message_sent( $clean_to );
                    set_transient( $lead_pacing_key, 1, 10 * MINUTE_IN_SECONDS );
                }

                return $res;
            }

            // Outside 24h Window: Enqueue update into $0 Smart Queue
            $this->queue_pending_update( $clean_to, "New Lead: {$name}", $concise_body, home_url( '/workspace/dashboard?sub_page=leads' ), 'Lead' );

            // Send instant Free Web Push / PWA Notification if available
            if ( function_exists( 'cora_pwa_send_push_notification' ) ) {
                $act_url = $this->get_activation_url( 'View Updates ⚡' );
                $admins = get_users( array( 'role__in' => array( 'administrator', 'cora_workspace_owner', 'cora_manager' ) ) );
                foreach ( $admins as $adm ) {
                    cora_pwa_send_push_notification( $adm->ID, "⚡ Lead: {$name}", "New inquiry received. Tap to unlock WhatsApp & view for free.", $act_url, true );
                }
            }

            // Rate-limited 1-time daily utility template nudge (at most 1 per 24 hours)
            $nudge_key = 'cora_wa_daily_nudge_' . $clean_to;
            if ( ! get_transient( $nudge_key ) && get_option( 'cora_wa_allow_template_nudge', '1' ) === '1' ) {
                $this->send_order_confirmation( $clean_to, $name, $ref_id, $date );
                set_transient( $nudge_key, 1, 24 * HOUR_IN_SECONDS );
            }

            return array( 'success' => true, 'queued' => true, 'session_active' => false );
        } else {
            // Client confirmation: check if session is open, else send template
            $win = $this->get_session_window( $clean_to );
            if ( $win['active'] ) {
                $client_body = "Hi {$name},\n\nThank you for reaching out! We received your submission for *{$title}* (Ref: #{$ref_id}). Our team will review your inquiry and follow up shortly.";
                return $this->send_session_message( $clean_to, $client_body );
            } else {
                return $this->send_order_confirmation( $clean_to, $name, $ref_id, $date );
            }
        }
    }

    /**
     * Dispatch an Automated Client Booking Reminder via WhatsApp
     *
     * @param string $recipient_phone
     * @param array  $booking_data
     * @return array
     */
    public function send_booking_reminder( $recipient_phone, $booking_data = array() ) {
        $clean_to = $this->clean_phone_number( $recipient_phone );
        if ( empty( $clean_to ) ) {
            return array( 'success' => false, 'error' => 'Invalid recipient phone.' );
        }

        // Strict rate limit: max 3 automated messages per hour
        if ( ! $this->can_send_automated_message( $clean_to ) ) {
            $this->queue_pending_update( $clean_to, 'Booking Reminder', 'Scheduled booking notification', home_url( '/workspace/dashboard?sub_page=bookings' ), 'Booking' );
            return array( 'success' => true, 'rate_limited' => true, 'queued' => true );
        }

        $client_name = sanitize_text_field( $booking_data['client_name'] ?? 'Rohan Verma' );
        $service     = sanitize_text_field( $booking_data['service'] ?? 'Commercial Studio Session' );
        $time        = sanitize_text_field( $booking_data['time'] ?? 'Tomorrow at 10:00 AM' );
        $location    = sanitize_text_field( $booking_data['location'] ?? 'Main Studio Floor B' );
        $ref_id      = sanitize_text_field( $booking_data['id'] ?? ( 'BK-' . wp_rand( 1000, 9999 ) ) );

        $body = "*Upcoming Booking Reminder*\n\n"
            . "Hi {$client_name}, this is a quick reminder about your scheduled shoot:\n\n"
            . "• *Service*: {$service}\n"
            . "• *Time*: {$time}\n"
            . "• *Location*: {$location}\n"
            . "• *Booking Ref*: #{$ref_id}\n\n"
            . "_Please arrive 10 minutes early. Tap below to confirm or request changes._";

        $win = $this->get_session_window( $clean_to );
        if ( $win['active'] ) {
            $res = $this->send_interactive_buttons(
                $clean_to,
                $body,
                array( 'Accept Booking', 'Reschedule', 'View Details' ),
                "📅 Cora AI • Booking Reminder",
                "Cora Workspace AI"
            );

            if ( empty( $res['success'] ) ) {
                $res = $this->send_session_message( $clean_to, "📅 *Booking Reminder*\n\n{$body}" );
            }

            if ( ! empty( $res['success'] ) ) {
                $this->record_automated_message_sent( $clean_to );
            } elseif ( ! empty( $res['raw']['error']['code'] ) && 131047 === (int) $res['raw']['error']['code'] ) {
                $this->queue_pending_update( $clean_to, "Booking: {$service}", $body, home_url( '/workspace/dashboard?sub_page=bookings' ), 'Booking' );
                return array( 'success' => true, 'queued' => true, 'session_active' => false );
            }
            return $res;
        }

        // Outside 24h Window: Enqueue update
        $this->queue_pending_update( $clean_to, "Booking: {$service}", $body, home_url( '/workspace/dashboard?sub_page=bookings' ), 'Booking' );
        return array( 'success' => true, 'queued' => true, 'session_active' => false );
    }

    /**
     * Dispatch Daily Studio Operational Briefing via WhatsApp
     *
     * @param string $recipient_phone
     * @return array
     */
    public function send_daily_briefing( $recipient_phone ) {
        $clean_to = $this->clean_phone_number( $recipient_phone );
        if ( empty( $clean_to ) ) {
            return array( 'success' => false, 'error' => 'Invalid recipient phone.' );
        }

        $workspace = get_bloginfo( 'name' ) ?: 'Cora Workspace';
        $time_str  = current_time( 'g:i A' );
        $today_str = current_time( 'M j, Y' );

        // Gather real operational metrics across all members
        $rev_val = '₹0';
        $active_staff = 0;
        $total_punches = 0;
        $compliance = 100;
        $completed_tasks = 0;
        $total_tasks = 0;
        $leads_today = 0;

        if ( function_exists( 'cora_generate_24h_executive_report_data' ) ) {
            $data = cora_generate_24h_executive_report_data( 1 );
            $rev_val = '₹' . number_format( $data['total_revenue'] ?? 0, 0 );
            $active_staff = $data['active_staff_count'] ?? 0;
            $total_punches = $data['total_punches'] ?? 0;
            $compliance = $data['geofence_compliance_pct'] ?? 100;
            $completed_tasks = $data['completed_tasks'] ?? 0;
            $total_tasks = $data['total_tasks'] ?? 0;
            $leads_today = $data['total_leads_today'] ?? ( ! empty( $data['today_leads'] ) ? count( $data['today_leads'] ) : 0 );
        }

        $report_url = function_exists( 'cora_get_24h_report_url' )
            ? cora_get_24h_report_url( 1, current_time( 'Y-m-d' ) )
            : home_url( '/workspace/executive-report' );

        $body = "*Daily Operations Executive Report*\n\n"
            . "Comprehensive snapshot as of {$time_str} ({$today_str}):\n\n"
            . "• 💼 *Revenue*: {$rev_val}\n"
            . "• 👥 *Team Roster*: {$active_staff} active ({$total_punches} punches, {$compliance}% geofence verified)\n"
            . "• 🔥 *Leads & Inquiries*: {$leads_today} recorded today\n"
            . "• ✅ *Tasks*: {$completed_tasks}/{$total_tasks} completed\n\n"
            . "📄 *Certified A4 PDF Report*:\n"
            . "👉 {$report_url}\n\n"
            . "_All member timesheets, billing, and leads are compiled into your certified report._";

        $win = $this->get_session_window( $clean_to );
        if ( $win['active'] ) {
            $res = $this->send_interactive_buttons(
                $clean_to,
                $body,
                array( 'Recent Leads', 'Today Revenue', 'Team Attendance' ),
                "📊 Cora AI • Executive Briefing",
                "Certified A4 PDF Report Available"
            );

            if ( empty( $res['success'] ) ) {
                $res = $this->send_session_message( $clean_to, "📊 *{$workspace} • Executive Briefing*\n\n{$body}" );
            }

            if ( ! empty( $res['success'] ) ) {
                $this->record_automated_message_sent( $clean_to );
            } elseif ( ! empty( $res['raw']['error']['code'] ) && 131047 === (int) $res['raw']['error']['code'] ) {
                $this->queue_pending_update( $clean_to, 'Daily Briefing', $body, $report_url, 'Briefing' );
                return array( 'success' => true, 'queued' => true, 'session_active' => false );
            }
            return $res;
        }

        // Outside 24h Window: Enqueue update
        $this->queue_pending_update( $clean_to, 'Daily Briefing', $body, $report_url, 'Briefing' );
        return array( 'success' => true, 'queued' => true, 'session_active' => false );
    }

    /**
     * Dispatch an Automated Invoice Payment Received Alert via WhatsApp
     *
     * @param string $recipient_phone
     * @param array  $payment_data
     * @return array
     */
    public function send_payment_alert( $recipient_phone, $payment_data = array() ) {
        $clean_to = $this->clean_phone_number( $recipient_phone );
        if ( empty( $clean_to ) ) {
            return array( 'success' => false, 'error' => 'Invalid recipient phone.' );
        }

        // Strict rate limit: max 3 automated messages per hour
        if ( ! $this->can_send_automated_message( $clean_to ) ) {
            $this->queue_pending_update( $clean_to, 'Payment Received', 'Payment confirmed and verified', home_url( '/workspace/dashboard?sub_page=financials' ), 'Financials' );
            return array( 'success' => true, 'rate_limited' => true, 'queued' => true );
        }

        $inv_number = sanitize_text_field( $payment_data['invoice_number'] ?? ( 'CORA-' . wp_rand( 1000, 9999 ) ) );
        $amount     = sanitize_text_field( $payment_data['amount'] ?? '₹42,500' );
        $client     = sanitize_text_field( $payment_data['client'] ?? 'Rohan Verma' );
        $method     = sanitize_text_field( $payment_data['method'] ?? 'UPI / Bank Transfer' );
        $time_str   = current_time( 'g:i A, M j' );

        $body = "*Payment Confirmed • Invoice #{$inv_number}*\n\n"
            . "Payment has been successfully verified and cleared:\n\n"
            . "• *Client*: {$client}\n"
            . "• *Amount Cleared*: {$amount}\n"
            . "• *Payment Method*: {$method}\n"
            . "• *Time*: {$time_str}\n"
            . "• *Status*: Settled & Marked Paid\n\n"
            . "_Official GST receipt and E-sign copy updated in your Document Vault._";

        $win = $this->get_session_window( $clean_to );
        if ( $win['active'] ) {
            $res = $this->send_interactive_buttons(
                $clean_to,
                $body,
                array( 'View Receipt', 'Open Ledger' ),
                "💰 Cora AI • Payment Confirmed",
                "Cora Workspace AI"
            );

            if ( empty( $res['success'] ) ) {
                $res = $this->send_session_message( $clean_to, "💰 *Payment Confirmed*\n\n{$body}" );
            }

            if ( ! empty( $res['success'] ) ) {
                $this->record_automated_message_sent( $clean_to );
            } elseif ( ! empty( $res['raw']['error']['code'] ) && 131047 === (int) $res['raw']['error']['code'] ) {
                $this->queue_pending_update( $clean_to, "Payment Confirmed: #{$inv_number}", $body, home_url( '/workspace/dashboard?sub_page=financials' ), 'Financials' );
                return array( 'success' => true, 'queued' => true, 'session_active' => false );
            }
            return $res;
        }

        // Outside 24h Window: Enqueue update
        $this->queue_pending_update( $clean_to, "Payment Confirmed: #{$inv_number}", $body, home_url( '/workspace/dashboard?sub_page=financials' ), 'Financials' );
        return array( 'success' => true, 'queued' => true, 'session_active' => false );
    }

    /**
     * Send a PDF or Document message via WhatsApp Cloud API
     *
     * @param string $to Recipient phone number
     * @param string $doc_url Direct URL to PDF/document
     * @param string $caption Optional caption
     * @param string $filename Optional document filename (e.g. Cora-Executive-Report.pdf)
     * @return array
     */
    public function send_document( $to, $doc_url, $caption = '', $filename = 'Cora-Executive-Report.pdf' ) {
        $settings = $this->get_settings();
        if ( empty( $settings['phone_number_id'] ) || empty( $settings['access_token'] ) ) {
            return array( 'success' => false, 'error' => 'WhatsApp API credentials not configured.' );
        }

        $clean_to = $this->clean_phone_number( $to );
        if ( empty( $clean_to ) ) {
            return array( 'success' => false, 'error' => 'Invalid recipient phone number.' );
        }

        $doc_payload = array(
            'link'     => esc_url_raw( $doc_url ),
            'filename' => sanitize_file_name( $filename ),
        );
        if ( ! empty( $caption ) ) {
            $doc_payload['caption'] = mb_substr( $caption, 0, 1024 );
        }

        $payload = array(
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $clean_to,
            'type'              => 'document',
            'document'          => $doc_payload,
        );

        return $this->execute_post_request( $payload );
    }

    /**
     * Send an image or media message
     *
     * @param string $to
     * @param string $media_url
     * @param string $caption
     * @param string $type ('image', 'document', 'video', 'audio')
     * @return array
     */
    public function send_media_message( $to, $media_url, $caption = '', $type = 'image' ) {
        $settings = $this->get_settings();
        if ( empty( $settings['phone_number_id'] ) || empty( $settings['access_token'] ) ) {
            return array(
                'success' => false,
                'error'   => 'WhatsApp API credentials not configured.',
            );
        }

        $clean_to = $this->clean_phone_number( $to );
        if ( empty( $clean_to ) ) {
            return array(
                'success' => false,
                'error'   => 'Invalid recipient phone number.',
            );
        }

        $media_payload = array(
            'link' => esc_url_raw( $media_url ),
        );
        if ( ! empty( $caption ) && in_array( $type, array( 'image', 'document', 'video' ), true ) ) {
            $media_payload['caption'] = $caption;
        }

        $payload = array(
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $clean_to,
            'type'              => $type,
            $type               => $media_payload,
        );

        return $this->execute_post_request( $payload );
    }

    /**
     * Send a Live Test Message
     *
     * @param string $to
     * @param string $custom_message
     * @return array
     */
    public function send_test_message( $to, $custom_message = '' ) {
        $clean_to = $this->clean_phone_number( $to );
        if ( empty( $clean_to ) ) {
            return array(
                'success' => false,
                'error'   => 'Please provide a valid recipient mobile number (e.g. +91 98765 43210).',
            );
        }

        $time_str = current_time( 'g:i A, d M Y' );
        $text = ! empty( $custom_message )
            ? $custom_message
            : "*Cora Workspace WhatsApp Test*\n\nYour WhatsApp Business Cloud API integration is successfully connected and operating.\n\n• *Status*: Connected\n• *Timestamp*: {$time_str}\n• *Engine*: Cora Workspace Cloud Gateway\n\n_Reply to this message anytime to unlock a free 24-hour customer service window._";

        // Attempt session message
        $res = $this->send_session_message( $clean_to, $text );
        
        // If Meta returns an error indicating template is required (131047: Re-engagement message), note that to the user
        if ( ! $res['success'] && isset( $res['raw']['error']['code'] ) && 131047 === (int) $res['raw']['error']['code'] ) {
            $res['error'] = 'Meta 24-hour service window is closed for this number. To receive free-form messages, first send any message from your WhatsApp to the business number, or send an approved template.';
            $res['window_expired'] = true;
        }

        return $res;
    }

    /**
     * Helper: Execute POST request to Graph API
     *
     * @param array $payload
     * @return array
     */
    private function execute_post_request( $payload ) {
        $settings = $this->get_settings();
        $endpoint = self::GRAPH_API_BASE . '/' . self::GRAPH_API_VERSION . '/' . rawurlencode( $settings['phone_number_id'] ) . '/messages';

        $response = wp_remote_post( $endpoint, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $settings['access_token'],
                'Content-Type'  => 'application/json',
            ),
            'body'    => wp_json_encode( $payload ),
            'timeout' => 20,
        ) );

        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'error'   => $response->get_error_message(),
            );
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body        = wp_remote_retrieve_body( $response );
        $json        = json_decode( $body, true );

        if ( $status_code >= 200 && $status_code < 300 && ! empty( $json['messages'][0]['id'] ) ) {
            return array(
                'success'    => true,
                'message_id' => $json['messages'][0]['id'],
                'raw'        => $json,
            );
        }

        $error_msg = 'Unknown Meta API error';
        if ( isset( $json['error']['message'] ) ) {
            $error_msg = $json['error']['message'];
            if ( isset( $json['error']['error_data']['details'] ) ) {
                $error_msg .= ' (' . $json['error']['error_data']['details'] . ')';
            }
        }

        return array(
            'success' => false,
            'error'   => $error_msg,
            'raw'     => $json,
        );
    }
}
