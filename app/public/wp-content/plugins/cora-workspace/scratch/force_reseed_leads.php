<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
// Load WordPress bootstrap
require_once dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/wp-load.php';

global $wpdb;

echo "Deleting options...\n";
delete_option( 'cora_seeded_3_leads_per_column_v2' );
delete_option( 'cora_workspace_leads' );

echo "Truncating leads table...\n";
$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cora_leads" );

echo "Calling cora_seed_3_leads_per_column()...\n";
cora_seed_3_leads_per_column();

echo "Verifying leads in database...\n";
$db_leads = $wpdb->get_results( "SELECT id, first_name, status FROM {$wpdb->prefix}cora_leads", ARRAY_A );
foreach ( $db_leads as $lead ) {
    echo "ID: {$lead['id']} | Name: {$lead['first_name']} | Status: {$lead['status']}\n";
}

echo "\nDone!\n";
