<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
// Load WordPress bootstrap
require_once dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/wp-load.php';

global $wpdb;

$leads_table = $wpdb->prefix . 'cora_leads';
$db_leads = $wpdb->get_results( "SELECT id, first_name, status FROM {$leads_table}", ARRAY_A );
$option_leads = get_option( 'cora_workspace_leads', array() );

echo "=== MIGRATION OPTIONS ===\n";
echo "cora_migration_v2_complete: " . var_export( get_option( 'cora_migration_v2_complete' ), true ) . "\n";
echo "cora_seeded_3_leads_per_column_v2: " . var_export( get_option( 'cora_seeded_3_leads_per_column_v2' ), true ) . "\n";

echo "\n=== DATABASE LEADS ({$leads_table}) ===\n";
if ( empty( $db_leads ) ) {
    echo "No leads in database.\n";
} else {
    foreach ( $db_leads as $lead ) {
        echo "ID: {$lead['id']} | Name: {$lead['first_name']} | Status: {$lead['status']}\n";
    }
}

echo "\n=== OPTION LEADS (cora_workspace_leads) ===\n";
if ( empty( $option_leads ) ) {
    echo "No leads in option.\n";
} else {
    foreach ( $option_leads as $lead ) {
        $id = $lead['id'] ?? 'N/A';
        $names = $lead['names'] ?? 'N/A';
        $status = $lead['status'] ?? 'N/A';
        echo "ID: {$id} | Name: {$names} | Status: {$status}\n";
    }
}
