<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
require_once dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/wp-load.php';

foreach ( array( 1, 8, 9 ) as $uid ) {
    wp_set_current_user( $uid );
    $leads = cora_db_get_leads();
    echo "=== USER {$uid} (" . get_userdata( $uid )->user_login . ") ===\n";
    echo "Leads count: " . count( $leads ) . "\n";
    foreach ( $leads as $lead ) {
        echo "ID: {$lead['id']} | Name: {$lead['names']} | Status: {$lead['status']} | Score: {$lead['score']}\n";
    }
    echo "\n";
}
