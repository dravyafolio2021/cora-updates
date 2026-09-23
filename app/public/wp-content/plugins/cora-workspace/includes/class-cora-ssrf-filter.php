<?php
/**
 * Cora Platform SSRF Filter & Safe HTTP Transport Engine (SEC-009)
 *
 * Validates external URLs against loopback, RFC1918 private networks,
 * carrier-grade NAT, multicast, cloud instance metadata services (AWS, GCP, Azure),
 * and unauthorized ports.
 *
 * @package Cora_Workspace
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_SSRF_Filter {

    /**
     * Forbidden IP CIDR ranges (IPv4 & IPv6)
     */
    private static $blacklisted_cidrs = array(
        '0.0.0.0/8',          // Current network
        '10.0.0.0/8',         // RFC1918 private
        '100.64.0.0/10',      // Carrier-grade NAT
        '127.0.0.0/8',        // Loopback
        '169.254.0.0/16',     // Link-local / Cloud metadata (AWS, GCP, Azure, DigitalOcean)
        '172.16.0.0/12',      // RFC1918 private
        '192.0.0.0/24',       // IETF Protocol Assignments
        '192.0.2.0/24',       // TEST-NET-1
        '192.168.0.0/16',     // RFC1918 private
        '198.18.0.0/15',      // Network benchmark tests
        '198.51.100.0/24',    // TEST-NET-2
        '203.0.113.0/24',     // TEST-NET-3
        '224.0.0.0/4',        // Multicast
        '240.0.0.0/4',        // Reserved
        '255.255.255.255/32', // Broadcast
        '::1/128',            // IPv6 Loopback
        'fc00::/7',           // IPv6 Unique local
        'fe80::/10',          // IPv6 Link-local
        '::ffff:0:0/96',      // IPv4-mapped IPv6
    );

    /**
     * Forbidden hostnames (case-insensitive)
     */
    private static $blacklisted_hosts = array(
        'localhost',
        'metadata.google.internal',
        'metadata.internal',
        'instance-data',
        '169.254.169.254',
        '169.254.169.253',
        '127.0.0.1',
        '::1',
        '0.0.0.0',
    );

    /**
     * Allowed URI schemes
     */
    private static $allowed_schemes = array( 'http', 'https' );

    /**
     * Allowed destination ports
     */
    private static $allowed_ports = array( 80, 443, 8080, 8443 );

    /**
     * Validate whether a destination URL is safe to fetch
     *
     * @param string $url
     * @param bool $allow_local_in_dev
     * @return bool
     */
    public static function is_url_safe( $url, $allow_local_in_dev = false ) {
        if ( empty( $url ) || ! is_string( $url ) ) {
            return false;
        }

        $parsed = parse_url( $url );
        if ( ! $parsed || empty( $parsed['host'] ) ) {
            return false;
        }

        // Scheme check
        $scheme = strtolower( $parsed['scheme'] ?? '' );
        if ( ! in_array( $scheme, self::$allowed_schemes, true ) ) {
            return false;
        }

        // Port check
        $port = isset( $parsed['port'] ) ? intval( $parsed['port'] ) : ( $scheme === 'https' ? 443 : 80 );
        if ( ! in_array( $port, self::$allowed_ports, true ) ) {
            return false;
        }

        $host = strtolower( trim( $parsed['host'] ) );

        // Strip surrounding brackets for IPv6
        $host = trim( $host, '[]' );

        // Hostname blocklist
        if ( in_array( $host, self::$blacklisted_hosts, true ) ) {
            if ( $allow_local_in_dev && function_exists( 'cora_is_local_environment' ) && cora_is_local_environment() ) {
                return true;
            }
            return false;
        }

        // If host is directly an IP address
        if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
            return self::is_ip_safe( $host, $allow_local_in_dev );
        }

        // Resolve DNS records to verify resolved IP addresses
        $ips = @gethostbynamel( $host );
        if ( empty( $ips ) || ! is_array( $ips ) ) {
            return false;
        }

        foreach ( $ips as $ip ) {
            if ( ! self::is_ip_safe( $ip, $allow_local_in_dev ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate an IP address against all restricted CIDR ranges
     *
     * @param string $ip
     * @param bool $allow_local_in_dev
     * @return bool
     */
    public static function is_ip_safe( $ip, $allow_local_in_dev = false ) {
        if ( empty( $ip ) ) {
            return false;
        }

        if ( $allow_local_in_dev && function_exists( 'cora_is_local_environment' ) && cora_is_local_environment() ) {
            if ( $ip === '127.0.0.1' || $ip === '::1' ) {
                return true;
            }
        }

        // Check against CIDR blocks
        foreach ( self::$blacklisted_cidrs as $cidr ) {
            if ( self::ip_in_cidr( $ip, $cidr ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if an IP is within a CIDR subnet
     *
     * @param string $ip
     * @param string $cidr
     * @return bool
     */
    private static function ip_in_cidr( $ip, $cidr ) {
        if ( strpos( $cidr, '/' ) === false ) {
            return $ip === $cidr;
        }

        list( $subnet, $mask ) = explode( '/', $cidr );

        // IPv4 CIDR check
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) && filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            $ip_long = ip2long( $ip );
            $subnet_long = ip2long( $subnet );
            $mask_long = -1 << ( 32 - intval( $mask ) );
            $subnet_long &= $mask_long;
            return ( $ip_long & $mask_long ) === $subnet_long;
        }

        // IPv6 CIDR check
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) && filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
            $ip_bin = inet_pton( $ip );
            $subnet_bin = inet_pton( $subnet );
            if ( false === $ip_bin || false === $subnet_bin ) {
                return false;
            }
            $mask = intval( $mask );
            $bytes = intval( $mask / 8 );
            $bits = $mask % 8;

            if ( substr( $ip_bin, 0, $bytes ) !== substr( $subnet_bin, 0, $bytes ) ) {
                return false;
            }
            if ( $bits > 0 ) {
                $ip_byte = ord( $ip_bin[ $bytes ] );
                $subnet_byte = ord( $subnet_bin[ $bytes ] );
                $mask_byte = ( 0xFF << ( 8 - $bits ) ) & 0xFF;
                return ( $ip_byte & $mask_byte ) === ( $subnet_byte & $mask_byte );
            }
            return true;
        }

        return false;
    }

    /**
     * Safe wrapper for WordPress remote GET requests
     *
     * @param string $url
     * @param array $args
     * @return array|WP_Error
     */
    public static function safe_remote_get( $url, array $args = array() ) {
        if ( ! self::is_url_safe( $url ) ) {
            if ( function_exists( 'cora_log_security_event' ) ) {
                cora_log_security_event( 'SSRF_BLOCKED', array( 'url' => $url ) );
            }
            return new WP_Error( 'ssrf_blocked', 'Destination URL blocked by platform security policy.', array( 'status' => 403 ) );
        }
        $args['redirection'] = 0; // Prevent open redirect SSRF bypass
        return wp_safe_remote_get( $url, $args );
    }

    /**
     * Safe wrapper for WordPress remote POST requests
     *
     * @param string $url
     * @param array $args
     * @return array|WP_Error
     */
    public static function safe_remote_post( $url, array $args = array() ) {
        if ( ! self::is_url_safe( $url ) ) {
            if ( function_exists( 'cora_log_security_event' ) ) {
                cora_log_security_event( 'SSRF_BLOCKED', array( 'url' => $url ) );
            }
            return new WP_Error( 'ssrf_blocked', 'Destination URL blocked by platform security policy.', array( 'status' => 403 ) );
        }
        $args['redirection'] = 0; // Prevent open redirect SSRF bypass
        return wp_safe_remote_post( $url, $args );
    }
}
