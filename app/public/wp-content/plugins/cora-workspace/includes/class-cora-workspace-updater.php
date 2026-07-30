<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Workspace_Updater {
    private static $instance = null;
    private $plugin_file = 'cora-workspace/cora-workspace.php';
    private $plugin_slug = 'cora-workspace';
    private $default_update_url = 'https://raw.githubusercontent.com/dravyafolio2021/cora-updates/main/cora-workspace.json';

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Native WordPress plugins list page update checks
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_plugin_update_transient' ) );
        add_filter( 'plugins_api', array( $this, 'plugin_info_api' ), 20, 3 );
        
        // AJAX Actions for in-app / workspace dashboard checking and upgrading
        add_action( 'wp_ajax_cora_check_plugin_update', array( $this, 'ajax_check_plugin_update' ) );
        add_action( 'wp_ajax_cora_trigger_in_app_update', array( $this, 'ajax_trigger_in_app_update' ) );
    }

    /**
     * Get the configured updates JSON server URL
     */
    public function get_update_url() {
        return esc_url_raw( $this->default_update_url );
    }

    /**
     * Fetch the remote update JSON info
     * 
     * @param bool $force Force a fresh HTTP request
     * @return array|false The parsed JSON or false on failure
     */
    public function fetch_remote_update_info( $force = false ) {
        $transient_key = 'cora_workspace_update_info';
        $info = get_transient( $transient_key );

        if ( false === $info || $force ) {
            $url = $this->get_update_url();
            
            // Add a cache buster query arg to prevent GitHub raw cache issues
            $url = add_query_arg( 'cb', time(), $url );

            $response = wp_remote_get( $url, array(
                'timeout'    => 15,
                'user-agent' => 'Cora-Platform-Updater/' . CORA_WORKSPACE_VERSION
            ) );

            if ( is_wp_error( $response ) ) {
                return false;
            }

            $code = wp_remote_retrieve_response_code( $response );
            if ( 200 !== $code ) {
                return false;
            }

            $body = wp_remote_retrieve_body( $response );
            $info = json_decode( $body, true );

            if ( ! is_array( $info ) || empty( $info['version'] ) ) {
                return false;
            }

            // Cache it for 12 hours
            set_transient( $transient_key, $info, 12 * HOUR_IN_SECONDS );
            update_option( 'cora_workspace_last_update_check_time', current_time( 'mysql' ) );
        }

        return $info;
    }

    /**
     * Hook into the standard WordPress plugin update transient
     */
    public function check_plugin_update_transient( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $info = $this->fetch_remote_update_info();
        if ( $info && version_compare( CORA_WORKSPACE_VERSION, $info['version'], '<' ) ) {
            $obj = new stdClass();
            $obj->slug        = $this->plugin_slug;
            $obj->plugin      = $this->plugin_file;
            $obj->new_version = $info['version'];
            $obj->url         = 'https://cora.ai';
            $obj->package     = $info['download_url'];
            $obj->tested      = $info['tested'] ?? '6.7';
            
            $transient->response[ $this->plugin_file ] = $obj;
        }

        return $transient;
    }

    /**
     * Hook into the plugins API to provide info and changelogs on WP admin pages
     */
    public function plugin_info_api( $res, $action, $args ) {
        if ( 'plugin_information' !== $action ) {
            return $res;
        }

        if ( isset( $args->slug ) && $args->slug === $this->plugin_slug ) {
            $info = $this->fetch_remote_update_info();
            if ( $info ) {
                $res = new stdClass();
                $res->name          = $info['name'] ?? 'Cora Workspace Platform';
                $res->slug          = $this->plugin_slug;
                $res->version       = $info['version'];
                $res->tested        = $info['tested'] ?? '6.7';
                $res->requires      = '5.8';
                $res->author        = 'Cora AI Team';
                $res->homepage      = 'https://cora.ai';
                $res->download_link = $info['download_url'];
                $res->sections      = array(
                    'description' => $info['sections']['description'] ?? '',
                    'changelog'   => $info['sections']['changelog'] ?? ''
                );
                return $res;
            }
        }

        return $res;
    }

    /**
     * AJAX Action: Check for updates
     */
    public function ajax_check_plugin_update() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized capability.' ) );
        }

        $info = $this->fetch_remote_update_info( true );
        if ( ! $info ) {
            wp_send_json_error( array( 'message' => 'Failed to connect to the update server. Please check your internet connection or update server URL.' ) );
        }

        $update_available = version_compare( CORA_WORKSPACE_VERSION, $info['version'], '<' );
        $last_checked = get_option( 'cora_workspace_last_update_check_time', 'Never' );
        if ( 'Never' !== $last_checked ) {
            $last_checked = mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_checked );
        }

        wp_send_json_success( array(
            'update_available' => $update_available,
            'current_version'  => CORA_WORKSPACE_VERSION,
            'new_version'      => $info['version'],
            'changelog'        => $info['sections']['changelog'] ?? '',
            'description'      => $info['sections']['description'] ?? '',
            'last_checked'     => $last_checked
        ) );
    }

    /**
     * AJAX Action: Trigger in-app update programmatically
     */
    public function ajax_trigger_in_app_update() {
        check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized capability.' ) );
        }

        // 1. Force update the site transient to ensure WordPress knows the download URL
        delete_transient( 'cora_workspace_update_info' );
        delete_site_transient( 'update_plugins' );
        
        $info = $this->fetch_remote_update_info( true );
        if ( ! $info ) {
            wp_send_json_error( array( 'message' => 'Failed to retrieve update details from server.' ) );
        }

        if ( ! version_compare( CORA_WORKSPACE_VERSION, $info['version'], '<' ) ) {
            wp_send_json_error( array( 'message' => 'Your workspace is already up-to-date (v' . CORA_WORKSPACE_VERSION . ').' ) );
        }

        // Trigger native WP update transient update
        wp_update_plugins();

        // 2. Include necessary files for programmatic upgrades
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skins.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/misc.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        // Set up the filesystem credentials page URL for WP_Filesystem
        $url = wp_nonce_url( admin_url( 'admin-ajax.php?action=cora_trigger_in_app_update' ), 'cora_ajax_nonce' );
        $creds = request_filesystem_credentials( $url, '', false, false, null );

        if ( false === $creds ) {
            wp_send_json_error( array( 'message' => 'Filesystem credentials are required to upgrade the plugin.' ) );
        }

        if ( ! WP_Filesystem( $creds ) ) {
            wp_send_json_error( array( 'message' => 'Failed to initialize WordPress filesystem.' ) );
        }

        // 3. Perform programmatic update using Plugin_Upgrader quiet skin
        $skin = new Automatic_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        
        // This will download, unzip, delete the old plugin directory and replace with new one.
        $result = $upgrader->upgrade( $this->plugin_file );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        } elseif ( ! $result ) {
            wp_send_json_error( array( 'message' => 'Upgrade process failed. Please ensure the plugins directory is writable.' ) );
        } else {
            // Success! Upgrade was successful.
            // Since WordPress deletes the old plugin folder and copies files, it might deactivate the plugin.
            // Let's reactivate the plugin to ensure uninterrupted service!
            if ( ! is_plugin_active( $this->plugin_file ) ) {
                activate_plugin( $this->plugin_file );
            }

            // Log activity
            if ( function_exists( 'cora_log_activity' ) ) {
                cora_log_activity( 'Platform', 'Upgraded Cora Workspace Platform to v' . $info['version'] );
            }

            wp_send_json_success( array( 
                'message' => 'Cora Workspace Platform successfully upgraded to v' . $info['version'] . '! Reloading dashboard...' 
            ) );
        }
    }
}

Cora_Workspace_Updater::get_instance();
