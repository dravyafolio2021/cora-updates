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
    const GRAPH_API_VERSION = 'v21.0';
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
        $enabled      = get_option( 'cora_wa_enabled', '1' ) === '1';

        // Auto-generate default verify token if empty
        if ( empty( $verify_token ) ) {
            $verify_token = 'cora_wa_' . substr( md5( wp_salt( 'auth' ) . 'cora_wa_webhook' ), 0, 16 );
            update_option( 'cora_wa_verify_token', $verify_token );
        }

        return array(
            'phone_number_id' => trim( $phone_id ),
            'waba_id'         => trim( $waba_id ),
            'access_token'    => trim( $access_token ),
            'verify_token'    => trim( $verify_token ),
            'enabled'         => $enabled,
            'is_configured'   => ! empty( $phone_id ) && ! empty( $access_token ),
            'webhook_url'     => rest_url( 'cora/v1/whatsapp/webhook' ),
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
     * @return bool
     */
    public function save_settings( $phone_number_id, $waba_id, $access_token, $verify_token = '', $enabled = true ) {
        update_option( 'cora_wa_phone_number_id', sanitize_text_field( trim( $phone_number_id ) ) );
        update_option( 'cora_wa_waba_id', sanitize_text_field( trim( $waba_id ) ) );
        update_option( 'cora_wa_access_token', sanitize_text_field( trim( $access_token ) ) );
        if ( ! empty( $verify_token ) ) {
            update_option( 'cora_wa_verify_token', sanitize_text_field( trim( $verify_token ) ) );
        }
        update_option( 'cora_wa_enabled', $enabled ? '1' : '0' );

        // Clear connection cache
        delete_transient( 'cora_wa_connection_status' );

        return true;
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
            $params = array();
            foreach ( $body_parameters as $param ) {
                $params[] = array(
                    'type' => 'text',
                    'text' => (string) $param,
                );
            }
            $components[] = array(
                'type'       => 'body',
                'parameters' => $params,
            );
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
            : "✨ *Cora Workspace WhatsApp Test*\n\nYour WhatsApp Business Cloud API integration is successfully connected and operating.\n\n• *Status*: Connected 🟢\n• *Timestamp*: {$time_str}\n• *Engine*: Cora Workspace Cloud Gateway\n\n_Reply to this message anytime to unlock a free 24-hour customer service window._";

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
