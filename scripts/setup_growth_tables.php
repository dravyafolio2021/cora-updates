<?php
/**
 * Setup and verify Cora Growth Tables
 */
define( 'WP_USE_THEMES', false );
require_once __DIR__ . '/../app/public/wp-load.php';

echo "WordPress loaded successfully.\n";

// Force migration
Cora_Growth_DB::migrate();
echo "Migration executed.\n";

global $wpdb;
$tables = array(
    $wpdb->prefix . 'cora_content_entries',
    $wpdb->prefix . 'cora_content_revisions',
    $wpdb->prefix . 'cora_content_assets',
    $wpdb->prefix . 'cora_growth_queue',
    $wpdb->prefix . 'cora_growth_metrics',
    $wpdb->prefix . 'cora_growth_audit_log',
);

foreach ( $tables as $table ) {
    $exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) );
    echo "Table {$table}: " . ( $exists ? "EXISTS (OK)" : "MISSING (ERROR)" ) . "\n";
}

$token = get_option( 'cora_growth_service_token' );
echo "Growth Service Token: " . substr( $token, 0, 14 ) . "...\n";
echo "Growth Workspace ID: " . Cora_Growth_DB::DEFAULT_WORKSPACE_ID . "\n";
