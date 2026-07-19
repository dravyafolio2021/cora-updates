<?php
/**
 * AJAX Challenger Test Script
 * Bootstraps WordPress and tests the plugin's AJAX endpoints for correctness, robustness, nonces, and edge cases.
 */

// Enable full error reporting to catch notices/warnings
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

// Bootstrap WordPress
define( 'WP_ADMIN', true );
define( 'DOING_AJAX', true );
require_once __DIR__ . '/app/public/wp-load.php';

// Custom die handler to catch wp_die calls without terminating PHP execution
$die_handler = function() {
    return function( $message, $title, $args ) {
        $response_code = isset( $args['response'] ) ? $args['response'] : 500;
        // If message is empty or numeric (e.g. -1 from check_ajax_referer), convert it to string
        if ( empty( $message ) ) {
            $message = '-1';
        }
        throw new Exception( $message, $response_code );
    };
};
add_filter( 'wp_die_handler', $die_handler );
add_filter( 'wp_die_ajax_handler', $die_handler );
add_filter( 'wp_die_xmlrpc_handler', $die_handler );

// Helper function to run AJAX hook
function run_ajax_action( $action, $post_data = [], $user_id = 0, $use_valid_nonce = true ) {
    if ( $user_id > 0 ) {
        wp_set_current_user( $user_id );
    } else {
        wp_set_current_user( 0 );
    }

    if ( $use_valid_nonce ) {
        $post_data['nonce'] = wp_create_nonce( 'cora_ajax_nonce' );
    }

    // Reset global state
    $_POST = $post_data;
    $_REQUEST = $post_data;
    
    // Capture stdout
    ob_start();
    $caught_exception = null;
    try {
        do_action( 'wp_ajax_' . $action );
    } catch ( Exception $e ) {
        $caught_exception = $e;
    }
    $output = ob_get_clean();
    
    // Attempt to decode JSON
    $json = json_decode( $output, true );
    
    return [
        'output'    => $output,
        'json'      => $json,
        'exception' => $caught_exception,
    ];
}

// Setup Admin User
$admins = get_users( array( 'role' => 'administrator' ) );
$admin_id = ! empty( $admins ) ? $admins[0]->ID : 0;
if ( ! $admin_id ) {
    $admin_id = wp_create_user( 'test_admin', 'test_admin_pass', 'test_admin@example.com' );
    $user = new WP_User( $admin_id );
    $user->set_role( 'administrator' );
}

$test_results = [];

// Test 1: Invalid Nonce (should fail check_ajax_referer)
$res1 = run_ajax_action( 'cora_gdpr_export', [ 'nonce' => 'bad_nonce', 'email' => 'test@example.com' ], $admin_id, false );
$test_results['invalid_nonce'] = [
    'description' => 'Test with an invalid nonce',
    'output' => $res1['output'],
    'exception_msg' => $res1['exception'] ? $res1['exception']->getMessage() : null,
    'passed' => ( $res1['output'] === '-1' || ( $res1['exception'] && $res1['exception']->getMessage() === '-1' ) ),
];

// Test 2: Missing Nonce (should fail check_ajax_referer)
$res2 = run_ajax_action( 'cora_gdpr_export', [ 'email' => 'test@example.com' ], $admin_id, false );
$test_results['missing_nonce'] = [
    'description' => 'Test with a missing nonce',
    'output' => $res2['output'],
    'exception_msg' => $res2['exception'] ? $res2['exception']->getMessage() : null,
    'passed' => ( $res2['output'] === '-1' || ( $res2['exception'] && $res2['exception']->getMessage() === '-1' ) ),
];

// Test 3: Valid Nonce but Unauthorized User
$res3 = run_ajax_action( 'cora_gdpr_export', [ 'email' => 'test@example.com' ], 0, true );
$test_results['unauthorized_user'] = [
    'description' => 'Test with a valid nonce but no authenticated user',
    'output' => $res3['output'],
    'exception_msg' => $res3['exception'] ? $res3['exception']->getMessage() : null,
    'json' => $res3['json'],
    'passed' => ( isset( $res3['json']['success'] ) && $res3['json']['success'] === false && strpos( $res3['json']['data']['message'], 'Unauthorized' ) !== false ),
];

// Test 4: Valid Nonce, Authorized User, Valid GDPR Export
$res4 = run_ajax_action( 'cora_gdpr_export', [ 'email' => 'test@example.com' ], $admin_id, true );
$test_results['gdpr_export_valid'] = [
    'description' => 'Test GDPR export with valid parameters and auth',
    'output' => $res4['output'],
    'exception_msg' => $res4['exception'] ? $res4['exception']->getMessage() : null,
    'json' => $res4['json'],
    'passed' => ( isset( $res4['json']['success'] ) && $res4['json']['success'] === true && strpos( $res4['json']['data']['message'], 'test@example.com' ) !== false ),
];

// Test 5: Valid Nonce, Authorized User, Missing GDPR Export Email (Edge Case)
$res5 = run_ajax_action( 'cora_gdpr_export', [], $admin_id, true );
$test_results['gdpr_export_missing_email'] = [
    'description' => 'Test GDPR export with missing email parameter',
    'output' => $res5['output'],
    'exception_msg' => $res5['exception'] ? $res5['exception']->getMessage() : null,
    'json' => $res5['json'],
    'passed' => ( isset( $res5['json']['success'] ) && $res5['json']['success'] === false ), // Expect validation failure
];

// Test 6: Valid Nonce, Authorized User, Valid GDPR Erase
$res6 = run_ajax_action( 'cora_gdpr_erase', [ 'email' => 'erase@example.com' ], $admin_id, true );
$test_results['gdpr_erase_valid'] = [
    'description' => 'Test GDPR erase with valid parameters and auth',
    'output' => $res6['output'],
    'exception_msg' => $res6['exception'] ? $res6['exception']->getMessage() : null,
    'json' => $res6['json'],
    'passed' => ( isset( $res6['json']['success'] ) && $res6['json']['success'] === true && strpos( $res6['json']['data']['message'], 'erase@example.com' ) !== false ),
];

// Test 7: Valid Nonce, Authorized User, Missing GDPR Erase Email (Edge Case)
$res7 = run_ajax_action( 'cora_gdpr_erase', [], $admin_id, true );
$test_results['gdpr_erase_missing_email'] = [
    'description' => 'Test GDPR erase with missing email parameter',
    'output' => $res7['output'],
    'exception_msg' => $res7['exception'] ? $res7['exception']->getMessage() : null,
    'json' => $res7['json'],
    'passed' => ( isset( $res7['json']['success'] ) && $res7['json']['success'] === false ), // Expect validation failure
];

// Test 8: XML WXR Export
$res8 = run_ajax_action( 'cora_export_xml', [], $admin_id, true );
$test_results['export_xml'] = [
    'description' => 'Test XML WXR export',
    'output' => $res8['output'],
    'exception_msg' => $res8['exception'] ? $res8['exception']->getMessage() : null,
    'json' => $res8['json'],
    'passed' => ( isset( $res8['json']['success'] ) && $res8['json']['success'] === true ),
];

// Test 9: Save Media Metadata with Invalid Attachment ID
$res9 = run_ajax_action( 'cora_save_media_metadata', [ 'attachment_id' => 0 ], $admin_id, true );
$test_results['save_media_metadata_invalid_id'] = [
    'description' => 'Test save media metadata with invalid attachment ID',
    'output' => $res9['output'],
    'exception_msg' => $res9['exception'] ? $res9['exception']->getMessage() : null,
    'json' => $res9['json'],
    'passed' => ( isset( $res9['json']['success'] ) && $res9['json']['success'] === false && strpos( $res9['json']['data']['message'], 'Invalid attachment ID' ) !== false ),
];

// Test 10: Valid Nonce, Authorized User, Invalid GDPR Export Email (Format check)
$res10 = run_ajax_action( 'cora_gdpr_export', [ 'email' => 'not-an-email-format' ], $admin_id, true );
$test_results['gdpr_export_invalid_email_format'] = [
    'description' => 'Test GDPR export with invalid email format',
    'output' => $res10['output'],
    'exception_msg' => $res10['exception'] ? $res10['exception']->getMessage() : null,
    'json' => $res10['json'],
    'passed' => ( isset( $res10['json']['success'] ) && $res10['json']['success'] === false ), // Expect validation failure
];

// Output results as JSON
echo json_encode( $test_results, JSON_PRETTY_PRINT ) . "\n";
