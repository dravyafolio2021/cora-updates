<?php
/**
 * Cora Universal MCP - Database Infrastructure & Migration
 *
 * Dedicated tables for OAuth 2.1 Server (Clients, Codes, Tokens) and MCP Audit Log.
 *
 * @package CoraWorkspace
 * @subpackage MCP
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_MCP_DB {

    const DB_VERSION = '1.0.0';
    const DB_VERSION_OPTION = 'cora_mcp_db_version';

    /**
     * Initialize DB hooks
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'check_and_migrate' ) );
    }

    /**
     * Check if DB tables need migration
     */
    public static function check_and_migrate() {
        $installed_version = get_option( self::DB_VERSION_OPTION, '0.0.0' );
        if ( version_compare( $installed_version, self::DB_VERSION, '<' ) ) {
            self::migrate();
        }
    }

    /**
     * Run migrations to create or update all MCP OAuth & Audit tables
     */
    public static function migrate() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        // 1. OAuth Clients Table
        $table_clients = $wpdb->prefix . 'cora_oauth_clients';
        $sql_clients = "CREATE TABLE {$table_clients} (
            client_id VARCHAR(64) NOT NULL,
            client_name VARCHAR(128) NOT NULL,
            client_secret_hash VARCHAR(64) NULL,
            client_type VARCHAR(32) NOT NULL DEFAULT 'public',
            redirect_uris TEXT NOT NULL,
            allowed_scopes TEXT NOT NULL,
            icon_url VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (client_id)
        ) {$charset_collate};";
        dbDelta( $sql_clients );

        // 2. OAuth Authorization Codes Table (PKCE RFC 7636)
        $table_codes = $wpdb->prefix . 'cora_oauth_codes';
        $sql_codes = "CREATE TABLE {$table_codes} (
            code VARCHAR(128) NOT NULL,
            client_id VARCHAR(64) NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            workspace_id VARCHAR(64) NOT NULL DEFAULT '1',
            redirect_uri TEXT NOT NULL,
            code_challenge VARCHAR(128) NOT NULL,
            code_challenge_method VARCHAR(16) NOT NULL DEFAULT 'S256',
            scopes TEXT NOT NULL,
            expires_at DATETIME NOT NULL,
            used TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (code),
            KEY idx_client (client_id),
            KEY idx_user (user_id),
            KEY idx_expires (expires_at)
        ) {$charset_collate};";
        dbDelta( $sql_codes );

        // 3. OAuth Tokens Table
        $table_tokens = $wpdb->prefix . 'cora_oauth_tokens';
        $sql_tokens = "CREATE TABLE {$table_tokens} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            token_hash VARCHAR(64) NOT NULL,
            refresh_token_hash VARCHAR(64) NULL,
            client_id VARCHAR(64) NOT NULL,
            client_name VARCHAR(128) NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            workspace_id VARCHAR(64) NOT NULL DEFAULT '1',
            scopes TEXT NOT NULL,
            expires_at DATETIME NOT NULL,
            refresh_expires_at DATETIME NULL,
            revoked TINYINT(1) NOT NULL DEFAULT 0,
            last_used_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY uk_token_hash (token_hash),
            KEY idx_refresh_token (refresh_token_hash),
            KEY idx_user_workspace (user_id, workspace_id),
            KEY idx_client (client_id),
            KEY idx_revoked (revoked)
        ) {$charset_collate};";
        dbDelta( $sql_tokens );

        // 4. MCP Audit Log Table
        $table_audit = $wpdb->prefix . 'cora_mcp_audit_log';
        $sql_audit = "CREATE TABLE {$table_audit} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            workspace_id VARCHAR(64) NOT NULL DEFAULT '1',
            client_name VARCHAR(128) NOT NULL DEFAULT 'unknown',
            token_id BIGINT UNSIGNED NULL,
            tool_name VARCHAR(128) NOT NULL,
            action_type VARCHAR(32) NOT NULL DEFAULT 'read',
            target_id VARCHAR(128) NULL,
            request_payload LONGTEXT NULL,
            result_status VARCHAR(32) NOT NULL DEFAULT 'success',
            error_message TEXT NULL,
            ip_address VARCHAR(45) NULL,
            execution_time_ms INT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_workspace (workspace_id),
            KEY idx_user (user_id),
            KEY idx_tool (tool_name),
            KEY idx_created (created_at)
        ) {$charset_collate};";
        dbDelta( $sql_audit );

        self::seed_default_clients();

        update_option( self::DB_VERSION_OPTION, self::DB_VERSION );
    }

    /**
     * Seed well-known MCP client profiles
     */
    private static function seed_default_clients() {
        global $wpdb;
        $table_clients = $wpdb->prefix . 'cora_oauth_clients';

        $default_clients = array(
            array(
                'client_id'      => 'chatgpt',
                'client_name'    => 'ChatGPT',
                'client_type'    => 'public',
                'redirect_uris'  => wp_json_encode( array( 'https://chatgpt.com/aip/oauth/callback', 'https://chat.openai.com/aip/oauth/callback' ) ),
                'allowed_scopes' => 'workspace:read clients:read clients:write leads:read leads:write projects:read projects:write tasks:read tasks:write content:read content:write finance:read finance:write knowledge:read'
            ),
            array(
                'client_id'      => 'claude',
                'client_name'    => 'Claude',
                'client_type'    => 'public',
                'redirect_uris'  => wp_json_encode( array( 'https://claude.ai/api/oauth/callback', 'http://localhost:*', 'http://127.0.0.1:*' ) ),
                'allowed_scopes' => 'workspace:read clients:read clients:write leads:read leads:write projects:read projects:write tasks:read tasks:write content:read content:write finance:read finance:write knowledge:read'
            ),
            array(
                'client_id'      => 'gemini',
                'client_name'    => 'Gemini',
                'client_type'    => 'public',
                'redirect_uris'  => wp_json_encode( array( 'https://gemini.google.com/oauth/callback', 'http://localhost:*', 'http://127.0.0.1:*' ) ),
                'allowed_scopes' => 'workspace:read clients:read clients:write leads:read leads:write projects:read projects:write tasks:read tasks:write content:read content:write finance:read finance:write knowledge:read'
            ),
            array(
                'client_id'      => 'cursor',
                'client_name'    => 'Cursor',
                'client_type'    => 'public',
                'redirect_uris'  => wp_json_encode( array( 'cursor://anysphere.cursor-mcp/oauth/callback', 'http://localhost:*', 'http://127.0.0.1:*' ) ),
                'allowed_scopes' => 'workspace:read clients:read clients:write leads:read leads:write projects:read projects:write tasks:read tasks:write content:read content:write finance:read finance:write knowledge:read'
            ),
            array(
                'client_id'      => 'vscode',
                'client_name'    => 'VS Code',
                'client_type'    => 'public',
                'redirect_uris'  => wp_json_encode( array( 'vscode://vscode.mcp/oauth/callback', 'vscode-insiders://vscode.mcp/oauth/callback', 'http://localhost:*' ) ),
                'allowed_scopes' => 'workspace:read clients:read clients:write leads:read leads:write projects:read projects:write tasks:read tasks:write content:read content:write finance:read finance:write knowledge:read'
            ),
            array(
                'client_id'      => 'windsurf',
                'client_name'    => 'Windsurf',
                'client_type'    => 'public',
                'redirect_uris'  => wp_json_encode( array( 'windsurf://codeium.windsurf-mcp/oauth/callback', 'http://localhost:*' ) ),
                'allowed_scopes' => 'workspace:read clients:read clients:write leads:read leads:write projects:read projects:write tasks:read tasks:write content:read content:write finance:read finance:write knowledge:read'
            ),
            array(
                'client_id'      => 'generic',
                'client_name'    => 'Generic MCP Client',
                'client_type'    => 'public',
                'redirect_uris'  => wp_json_encode( array( 'http://localhost:*', 'http://127.0.0.1:*', 'https://*' ) ),
                'allowed_scopes' => 'workspace:read clients:read clients:write leads:read leads:write projects:read projects:write tasks:read tasks:write content:read content:write finance:read finance:write knowledge:read'
            ),
        );

        foreach ( $default_clients as $client ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_clients} WHERE client_id = %s", $client['client_id'] ) );
            if ( ! $exists ) {
                $wpdb->insert( $table_clients, $client );
            }
        }
    }
}
