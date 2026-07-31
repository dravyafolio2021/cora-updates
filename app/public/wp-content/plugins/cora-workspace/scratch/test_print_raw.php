<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
require_once dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/wp-load.php';

global $wpdb;
$raw = $wpdb->get_var( "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'cora_workspace_leads'" );
$unserialized = maybe_unserialize( $raw );
echo "=== RAW SERIALIZED LENGTH ===\n";
echo strlen($raw) . " bytes\n";
echo "\n=== UNSERIALIZED CONTENT ===\n";
print_r( $unserialized );
