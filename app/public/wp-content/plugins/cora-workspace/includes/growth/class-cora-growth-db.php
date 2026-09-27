<?php
/**
 * Cora Growth Workspace - Database Infrastructure & Migration
 *
 * Dedicated tables for Content Studio, Revisions, Assets, Growth Queue, Metrics, and Audit Log.
 * Strictly scoped by workspace_id ('growth_workspace').
 *
 * @package CoraWorkspace
 * @subpackage Growth
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Growth_DB {

    const DB_VERSION = '1.0.1';
    const DB_VERSION_OPTION = 'cora_growth_db_version';
    const DEFAULT_WORKSPACE_ID = 'growth_cora_main_01';

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
     * Run migrations to create or update all growth tables
     */
    public static function migrate() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        // 1. Content Entries Table (Generic, structured schema)
        $table_content = $wpdb->prefix . 'cora_content_entries';
        $sql_content = "CREATE TABLE {$table_content} (
            id VARCHAR(64) NOT NULL,
            workspace_id VARCHAR(64) NOT NULL DEFAULT 'growth_cora_main_01',
            type VARCHAR(32) NOT NULL DEFAULT 'article',
            schema_version INT NOT NULL DEFAULT 1,
            title TEXT NOT NULL,
            slug VARCHAR(191) NOT NULL,
            status VARCHAR(32) NOT NULL DEFAULT 'draft',
            excerpt TEXT NULL,
            target_icp VARCHAR(128) NULL,
            primary_keyword VARCHAR(191) NULL,
            secondary_keywords_json LONGTEXT NULL,
            search_intent VARCHAR(64) NULL,
            read_time VARCHAR(32) NULL,
            seo_json LONGTEXT NULL,
            share_json LONGTEXT NULL,
            content_blocks_json LONGTEXT NULL,
            chapters_json LONGTEXT NULL,
            sources_json LONGTEXT NULL,
            relationships_json LONGTEXT NULL,
            assets_json LONGTEXT NULL,
            cta_json LONGTEXT NULL,
            author_json LONGTEXT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            published_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY idx_workspace_status (workspace_id, status),
            KEY idx_workspace_slug (workspace_id, slug),
            KEY idx_workspace_type (workspace_id, type),
            KEY idx_primary_keyword (primary_keyword)
        ) {$charset_collate};";
        dbDelta( $sql_content );

        // 2. Content Revisions Table (Immutable history snapshot)
        $table_revisions = $wpdb->prefix . 'cora_content_revisions';
        $sql_revisions = "CREATE TABLE {$table_revisions} (
            id BIGINT NOT NULL AUTO_INCREMENT,
            revision_id VARCHAR(64) NOT NULL,
            content_id VARCHAR(64) NOT NULL,
            workspace_id VARCHAR(64) NOT NULL DEFAULT 'growth_cora_main_01',
            actor VARCHAR(128) NOT NULL DEFAULT 'Cora Growth Agent',
            snapshot_json LONGTEXT NOT NULL,
            change_reason TEXT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uk_revision_id (revision_id),
            KEY idx_content_rev (content_id, created_at),
            KEY idx_workspace (workspace_id)
        ) {$charset_collate};";
        dbDelta( $sql_revisions );

        // 3. Content Assets Table (Media, PDFs, Infographics)
        $table_assets = $wpdb->prefix . 'cora_content_assets';
        $sql_assets = "CREATE TABLE {$table_assets} (
            id BIGINT NOT NULL AUTO_INCREMENT,
            asset_id VARCHAR(64) NOT NULL,
            workspace_id VARCHAR(64) NOT NULL DEFAULT 'growth_cora_main_01',
            filename VARCHAR(255) NOT NULL,
            file_url TEXT NOT NULL,
            mime_type VARCHAR(64) NOT NULL,
            width INT NULL,
            height INT NULL,
            file_size BIGINT NOT NULL DEFAULT 0,
            alt_text TEXT NULL,
            title VARCHAR(255) NULL,
            caption TEXT NULL,
            source VARCHAR(128) NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uk_asset_id (asset_id),
            KEY idx_workspace (workspace_id),
            KEY idx_mime_type (mime_type)
        ) {$charset_collate};";
        dbDelta( $sql_assets );

        // 4. Growth Queue Table (First-party organic growth task queue)
        $table_queue = $wpdb->prefix . 'cora_growth_queue';
        $sql_queue = "CREATE TABLE {$table_queue} (
            id VARCHAR(64) NOT NULL,
            workspace_id VARCHAR(64) NOT NULL DEFAULT 'growth_cora_main_01',
            type VARCHAR(64) NOT NULL DEFAULT 'ARTICLE',
            title TEXT NOT NULL,
            priority VARCHAR(32) NOT NULL DEFAULT 'medium',
            status VARCHAR(32) NOT NULL DEFAULT 'backlog',
            target_keyword VARCHAR(191) NULL,
            target_url TEXT NULL,
            source VARCHAR(128) NULL,
            assigned_to VARCHAR(128) NOT NULL DEFAULT 'Cora Growth Agent',
            result_content_id VARCHAR(64) NULL,
            requirement_spec_json LONGTEXT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            completed_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY idx_workspace_status (workspace_id, status),
            KEY idx_type (type),
            KEY idx_priority (priority)
        ) {$charset_collate};";
        dbDelta( $sql_queue );

        // 5. Growth Metrics Table (GSC + GA4 performance data)
        $table_metrics = $wpdb->prefix . 'cora_growth_metrics';
        $sql_metrics = "CREATE TABLE {$table_metrics} (
            id BIGINT NOT NULL AUTO_INCREMENT,
            workspace_id VARCHAR(64) NOT NULL DEFAULT 'growth_cora_main_01',
            content_id VARCHAR(64) NULL,
            target_url TEXT NOT NULL,
            metric_date DATE NOT NULL,
            query VARCHAR(191) NULL,
            impressions INT NOT NULL DEFAULT 0,
            clicks INT NOT NULL DEFAULT 0,
            ctr FLOAT NOT NULL DEFAULT 0,
            position FLOAT NOT NULL DEFAULT 0,
            sessions INT NOT NULL DEFAULT 0,
            engaged_sessions INT NOT NULL DEFAULT 0,
            conversions INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_content_date (content_id, metric_date),
            KEY idx_workspace_date (workspace_id, metric_date),
            KEY idx_query (query)
        ) {$charset_collate};";
        dbDelta( $sql_metrics );

        // 6. Growth Audit Log Table (Tracking all automated & user actions)
        $table_audit = $wpdb->prefix . 'cora_growth_audit_log';
        $sql_audit = "CREATE TABLE {$table_audit} (
            id BIGINT NOT NULL AUTO_INCREMENT,
            workspace_id VARCHAR(64) NOT NULL DEFAULT 'growth_cora_main_01',
            actor VARCHAR(128) NOT NULL DEFAULT 'Cora Growth Agent',
            action VARCHAR(64) NOT NULL,
            content_id VARCHAR(64) NULL,
            previous_revision VARCHAR(64) NULL,
            new_revision VARCHAR(64) NULL,
            metadata_json LONGTEXT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_workspace_actor (workspace_id, actor),
            KEY idx_content (content_id)
        ) {$charset_collate};";
        dbDelta( $sql_audit );

        self::ensure_growth_workspace_seeded();

        update_option( self::DB_VERSION_OPTION, self::DB_VERSION );
    }

    /**
     * Ensure dedicated Growth Workspace and service account exist
     */
    public static function ensure_growth_workspace_seeded() {
        global $wpdb;
        $workspaces_table = $wpdb->prefix . 'cora_workspaces';

        // Check if cora_workspaces table exists
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $workspaces_table ) ) === $workspaces_table ) {
            $existing = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$workspaces_table} WHERE workspace_id = %s OR id = %s", self::DEFAULT_WORKSPACE_ID, self::DEFAULT_WORKSPACE_ID ) );
            if ( ! $existing ) {
                $wpdb->insert(
                    $workspaces_table,
                    array(
                        'id'             => self::DEFAULT_WORKSPACE_ID,
                        'name'           => 'Cora Growth',
                        'industry'       => 'growth_marketing',
                        'owner_id'       => 1,
                        'created_at'     => current_time( 'mysql' ),
                        'updated_at'     => current_time( 'mysql' ),
                        'status'         => 'active',
                    ),
                    array( '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
                );
            }
        }

        // Store growth service account API token if not exists
        if ( ! get_option( 'cora_growth_service_token' ) ) {
            $token = 'cora_growth_sec_' . bin2hex( random_bytes( 24 ) );
            update_option( 'cora_growth_service_token', $token );
        }

        // Store preview secret key
        if ( ! get_option( 'cora_growth_preview_secret' ) ) {
            $secret = 'cora_prev_sec_' . bin2hex( random_bytes( 24 ) );
            update_option( 'cora_growth_preview_secret', $secret );
        }

        // Store revalidation secret key
        if ( ! get_option( 'cora_growth_revalidate_secret' ) ) {
            $reval = 'cora_reval_sec_' . bin2hex( random_bytes( 24 ) );
            update_option( 'cora_growth_revalidate_secret', $reval );
        }
    }
}
