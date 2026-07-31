<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
define( 'DOING_AJAX', true );
require_once dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/wp-load.php';

// Set current user to user 8 (shravya)
wp_set_current_user( 8 );

// 1. Get initial status
$leads_before = cora_db_get_leads();
$lead_1_before = null;
foreach ( $leads_before as $l ) {
    if ( (string)$l['id'] === '1' ) {
        $lead_1_before = $l;
        break;
    }
}
echo "Status before update: " . ($lead_1_before ? $lead_1_before['status'] : 'Not found') . "\n";

// 2. Simulate AJAX update stage
$_POST['lead_id'] = '1';
$_POST['new_stage'] = 'Contacted';
$_POST['security'] = wp_create_nonce( 'cora_ajax_nonce' );

// We catch the wp_send_json output
ob_start();
try {
    cora_ajax_update_lead_stage();
} catch ( Exception $e ) {
    echo "Exception: " . $e->getMessage() . "\n";
}
$ajax_output = ob_get_clean();
echo "AJAX output: " . $ajax_output . "\n";

// 3. Get status after update in the same request
$leads_after = cora_db_get_leads();
$lead_1_after = null;
foreach ( $leads_after as $l ) {
    if ( (string)$l['id'] === '1' ) {
        $lead_1_after = $l;
        break;
    }
}
echo "Status after update: " . ($lead_1_after ? $lead_1_after['status'] : 'Not found') . "\n";
