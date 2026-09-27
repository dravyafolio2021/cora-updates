<?php
/**
 * Cora Growth Workspace - Temporary Signed Preview Token Service
 *
 * Generates and validates cryptographic, expiring preview tokens for Next.js preview rendering.
 *
 * @package CoraWorkspace
 * @subpackage Growth
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Growth_Preview_Service {

    const TOKEN_EXPIRY_SECONDS = 86400; // 24 Hours

    /**
     * Get preview secret key
     */
    private static function get_secret() {
        $secret = get_option( 'cora_growth_preview_secret' );
        if ( ! $secret ) {
            $secret = 'cora_prev_sec_' . wp_generate_password( 32, true, true );
            update_option( 'cora_growth_preview_secret', $secret );
        }
        return $secret;
    }

    /**
     * Generate a signed preview token for a content ID
     *
     * @param string $content_id
     * @param string $workspace_id
     * @param int    $ttl_seconds
     * @return string
     */
    public static function generate_token( $content_id, $workspace_id = 'growth_cora_main_01', $ttl_seconds = self::TOKEN_EXPIRY_SECONDS ) {
        $expires = time() + $ttl_seconds;
        $payload_data = $content_id . '|' . $workspace_id . '|' . $expires;
        $signature = hash_hmac( 'sha256', $payload_data, self::get_secret() );

        $token_payload = array(
            'cid' => $content_id,
            'wid' => $workspace_id,
            'exp' => $expires,
            'sig' => $signature,
        );

        return rtrim( strtr( base64_encode( wp_json_encode( $token_payload ) ), '+/', '-_' ), '=' );
    }

    /**
     * Validate a preview token and return payload if valid
     *
     * @param string $token
     * @return array|false
     */
    public static function validate_token( $token ) {
        $json_str = base64_decode( strtr( $token, '-_', '+/' ) );
        if ( ! $json_str ) {
            return false;
        }

        $data = json_decode( $json_str, true );
        if ( ! $data || empty( $data['cid'] ) || empty( $data['wid'] ) || empty( $data['exp'] ) || empty( $data['sig'] ) ) {
            return false;
        }

        // Check expiration
        if ( time() > intval( $data['exp'] ) ) {
            return false;
        }

        // Verify cryptographic HMAC signature
        $payload_data = $data['cid'] . '|' . $data['wid'] . '|' . $data['exp'];
        $expected_sig = hash_hmac( 'sha256', $payload_data, self::get_secret() );

        if ( ! hash_equals( $expected_sig, $data['sig'] ) ) {
            return false;
        }

        return $data;
    }

    /**
     * Build preview URL for Next.js
     *
     * @param string $content_id
     * @param string $workspace_id
     * @return string
     */
    public static function get_preview_url( $content_id, $workspace_id = 'growth_cora_main_01' ) {
        $token = self::generate_token( $content_id, $workspace_id );
        $frontend_url = defined( 'CORA_FRONTEND_URL' ) ? CORA_FRONTEND_URL : ( get_option( 'cora_frontend_url' ) ?: 'https://heycora.in' );
        return trailingslashit( $frontend_url ) . 'preview/' . $token;
    }
}
