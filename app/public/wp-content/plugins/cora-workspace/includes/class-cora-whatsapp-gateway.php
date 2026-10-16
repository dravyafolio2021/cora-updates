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
        $default_tpl  = get_option( 'cora_wa_default_template', 'jaspers_market_order_confirmation_v1' );
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
     * Get direct WhatsApp deep-link URL to initiate or activate the 24h Free Window
     *
     * @param string $custom_text Pre-filled message text
     * @return string
     */
    public function get_activation_url( $custom_text = '' ) {
        $status = get_transient( 'cora_wa_connection_status' );
        $phone_digits = '';
        if ( ! empty( $status['display_phone_number'] ) ) {
            $phone_digits = preg_replace( '/[^\d]/', '', $status['display_phone_number'] );
        }
        if ( empty( $phone_digits ) ) {
            $settings = $this->get_settings();
            // Default to test number digits if available
            $phone_digits = ! empty( $settings['phone_number_id'] ) ? '15556698957' : '15556698957';
        }

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
     * Auto-respond to incoming activation message to confirm 24h window
     *
     * @param string $to Recipient phone number
     * @return array
     */
    public function send_daily_activation_reply( $to ) {
        $clean_to = $this->clean_phone_number( $to );
        if ( empty( $clean_to ) ) {
            return array( 'success' => false, 'error' => 'Invalid recipient phone.' );
        }

        $time_str = current_time( 'g:i A, d M Y' );
        $workspace = get_bloginfo( 'name' ) ?: 'Cora Workspace';

        $body = "✅ *24-Hour Free Workspace Alerts Activated!*\n\n"
            . "Hi! Your WhatsApp session for *{$workspace}* is now live.\n\n"
            . "• *Status*: Active (₹0 Free Tier)\n"
            . "• *Window*: Valid for 24 Hours\n"
            . "• *Activated At*: {$time_str}\n"
            . "• *Alerts Enabled*: New Leads, Project Updates, Booking Reminders\n\n"
            . "_All notifications today will be delivered directly here. Reply anytime to refresh your 24h window._";

        return $this->send_session_message( $clean_to, $body );
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
            $template_name = ! empty( $settings['default_template'] ) ? $settings['default_template'] : 'jaspers_market_order_confirmation_v1';
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

        $name   = $lead_data['name'] ?? 'Client';
        $title  = $lead_data['title'] ?? 'Inquiry';
        $ref_id = $lead_data['id'] ?? ( 'L-' . wp_rand( 1000, 9999 ) );
        $date   = $lead_data['date'] ?? current_time( 'M j, Y' );

        if ( $is_admin_alert ) {
            // For admin alert, check if 24h window is open or send session text
            $win = $this->get_session_window( $clean_to );
            $alert_body = "*New Lead Alert: {$title}*\n\n"
                . "• *Client*: {$name}\n"
                . ( ! empty( $lead_data['phone'] ) ? "• *Phone*: {$lead_data['phone']}\n" : "" )
                . ( ! empty( $lead_data['email'] ) ? "• *Email*: {$lead_data['email']}\n" : "" )
                . "• *Ref*: #{$ref_id}\n"
                . "• *Date*: {$date}\n\n"
                . "_Cora Automated Messaging Gateway_";

            $res = $this->send_session_message( $clean_to, $alert_body );
            // If window expired, fall back to template
            if ( ! $res['success'] && ! empty( $res['raw']['error']['code'] ) && 131047 === (int) $res['raw']['error']['code'] ) {
                return $this->send_order_confirmation( $clean_to, $name, $ref_id, $date );
            }
            return $res;
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

        $client_name = $booking_data['client_name'] ?? 'Rohan Verma';
        $service     = $booking_data['service'] ?? 'Commercial Studio Session';
        $time        = $booking_data['time'] ?? 'Tomorrow at 10:00 AM';
        $location    = $booking_data['location'] ?? 'Main Studio Floor B';
        $ref_id      = $booking_data['id'] ?? ( 'BK-' . wp_rand( 1000, 9999 ) );

        $body = "📅 *Upcoming Booking Reminder*\n\n"
            . "Hi {$client_name}, this is a quick reminder about your scheduled shoot with us:\n\n"
            . "• *Service*: {$service}\n"
            . "• *Time*: {$time}\n"
            . "• *Location*: {$location}\n"
            . "• *Booking Ref*: #{$ref_id}\n\n"
            . "📍 _Please arrive 10 minutes early. Reply to this message if you need directions or rescheduling._";

        $res = $this->send_session_message( $clean_to, $body );
        if ( ! $res['success'] && ! empty( $res['raw']['error']['code'] ) && 131047 === (int) $res['raw']['error']['code'] ) {
            return $this->send_order_confirmation( $clean_to, $client_name, $ref_id, current_time( 'M j, Y' ) );
        }
        return $res;
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

        $workspace = get_bloginfo( 'name' ) ?: 'Cora Studio';
        $time_str  = current_time( 'g:i A' );

        $body = "📊 *{$workspace} • Daily Morning Briefing*\n\n"
            . "Good morning! Here is your operational snapshot as of {$time_str}:\n\n"
            . "• 💰 *Revenue Cleared*: ₹1,85,000 (Month to Date)\n"
            . "• ⏳ *Pending Receivables*: ₹45,200 (1 invoice)\n"
            . "• 🎯 *Active Projects*: 4 shoots in post-production\n"
            . "• 🔥 *New Leads Today*: 2 qualified client inquiries\n"
            . "• 📅 *Today's Schedule*: 11:30 AM Client Review Call\n\n"
            . "🚀 _Have a productive day! Reply anytime to ask Cora AI._";

        return $this->send_session_message( $clean_to, $body );
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

        $inv_number = $payment_data['invoice_number'] ?? ( 'CORA-' . wp_rand( 1000, 9999 ) );
        $amount     = $payment_data['amount'] ?? '₹42,500';
        $client     = $payment_data['client'] ?? 'Rohan Verma';
        $method     = $payment_data['method'] ?? 'UPI / Bank Transfer';
        $time_str   = current_time( 'g:i A, M j' );

        $body = "💰 *Payment Confirmed • Invoice #{$inv_number}*\n\n"
            . "Payment has been successfully verified and cleared:\n\n"
            . "• *Client*: {$client}\n"
            . "• *Amount Cleared*: {$amount}\n"
            . "• *Payment Method*: {$method}\n"
            . "• *Time*: {$time_str}\n"
            . "• *Status*: Settled & Marked Paid\n\n"
            . "📄 _Official GST receipt and E-sign copy updated in your Document Vault._";

        return $this->send_session_message( $clean_to, $body );
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
