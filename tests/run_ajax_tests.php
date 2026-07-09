<?php
/**
 * AJAX Empirical Test Suite for Cora Real Estate Platform
 */

// Define AJAX environment constants
define( 'DOING_AJAX', true );
define( 'WP_ADMIN', true );

// Set mysql socket path for LocalWP database compatibility
ini_set( 'mysqli.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock' );
ini_set( 'pdo_mysql.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock' );

// Load WordPress environment
require_once dirname( __FILE__ ) . '/../app/public/wp-load.php';

// Setup custom wp_die handlers to capture and not crash the script
add_filter( 'wp_die_ajax_handler', 'cora_test_die_handler' );
add_filter( 'wp_die_handler', 'cora_test_die_handler' );

function cora_test_die_handler() {
    return function( $message, $title = '', $args = array() ) {
        if ( is_wp_error( $message ) ) {
            $message = $message->get_error_message();
        }
        throw new Exception( (string) $message );
    };
}

/**
 * Execute a simulated AJAX action
 */
function test_ajax_action( $action, $params = array(), $user_role = null ) {
    $old_post = $_POST;
    $old_request = $_REQUEST;
    $old_user_id = get_current_user_id();

    // 1. Setup simulated user
    if ( $user_role === 'administrator' ) {
        $admins = get_users( array( 'role' => 'administrator' ) );
        if ( ! empty( $admins ) ) {
            wp_set_current_user( $admins[0]->ID );
        } else {
            echo "Warning: no administrator found in database.\n";
        }
    } elseif ( $user_role === 'cora_manager' ) {
        $managers = get_users( array( 'role' => 'cora_manager' ) );
        if ( ! empty( $managers ) ) {
            wp_set_current_user( $managers[0]->ID );
        } else {
            // Find/create manager or fallback to admin
            $admins = get_users( array( 'role' => 'administrator' ) );
            wp_set_current_user( ! empty( $admins ) ? $admins[0]->ID : 0 );
        }
    } else {
        wp_set_current_user( 0 ); // Guest
    }

    // 2. Prepare POST params
    $_POST = $params;
    $_POST['action'] = $action;

    // Generate valid nonce if requested
    if ( isset( $params['nonce'] ) && $params['nonce'] === 'VALID' ) {
        $_POST['nonce'] = wp_create_nonce( 'cora_ajax_nonce' );
    }
    if ( isset( $params['security'] ) && $params['security'] === 'VALID' ) {
        $_POST['security'] = wp_create_nonce( 'cora_ajax_nonce' );
    }

    $_REQUEST = $_POST;

    // 3. Buffer output and invoke action hook
    ob_start();
    $error = null;
    try {
        if ( is_user_logged_in() ) {
            do_action( 'wp_ajax_' . $action );
        } else {
            do_action( 'wp_ajax_nopriv_' . $action );
        }
    } catch ( Exception $e ) {
        $error = $e->getMessage();
    }
    $output = ob_get_clean();

    // Restore state
    wp_set_current_user( $old_user_id );
    $_POST = $old_post;
    $_REQUEST = $old_request;

    // Parse output
    $decoded = json_decode( $output, true );

    return array(
        'raw'       => $output,
        'decoded'   => $decoded,
        'exception' => $error
    );
}

// Global results accumulator
$results = array();

function report_test( $name, $result, $expected_success, $comment = '' ) {
    global $results;
    $success = isset( $result['decoded']['success'] ) ? $result['decoded']['success'] : null;
    $passed = ( $success === $expected_success );
    
    // In some cases if we expect failure, success should be false or we might get a die/error response (e.g. -1, 0, empty or 403)
    if ( ! $expected_success && ( $success === false || $result['raw'] === '-1' || $result['raw'] === '0' || $result['raw'] === '' || ! empty( $result['exception'] ) ) ) {
        $passed = true;
    }

    $results[] = array(
        'name' => $name,
        'passed' => $passed,
        'success_returned' => $success,
        'raw_output' => substr( trim( $result['raw'] ), 0, 100 ),
        'exception' => $result['exception'],
        'comment' => $comment
    );
}

echo "=== CORA AJAX EMPIRICAL VERIFICATION ===\n\n";

// --- TEST 1: GDPR Export without parameters (anonymous) ---
$res = test_ajax_action( 'cora_gdpr_export', array(), null );
report_test( 'GDPR Export - Anonymous (No nonce, no email)', $res, false, 'Should fail due to no nonce & anonymous' );

// --- TEST 2: GDPR Export - Admin, missing/invalid Nonce ---
$res = test_ajax_action( 'cora_gdpr_export', array( 'email' => 'test@example.com', 'nonce' => 'INVALID' ), 'administrator' );
report_test( 'GDPR Export - Admin, invalid nonce', $res, false, 'Should fail due to invalid nonce' );

// --- TEST 3: GDPR Export - Admin, valid Nonce, missing Email ---
$res = test_ajax_action( 'cora_gdpr_export', array( 'nonce' => 'VALID' ), 'administrator' );
// Notice: Let's see if it fails/warns when email is missing!
report_test( 'GDPR Export - Admin, valid nonce, missing email', $res, false, 'Should fail because email is required' );

// --- TEST 4: GDPR Export - Admin, valid Nonce, valid Email ---
$res = test_ajax_action( 'cora_gdpr_export', array( 'email' => 'test@example.com', 'nonce' => 'VALID' ), 'administrator' );
report_test( 'GDPR Export - Admin, valid nonce & email', $res, true, 'Should succeed' );

// --- TEST 5: GDPR Erase - Admin, valid Nonce, missing Email ---
$res = test_ajax_action( 'cora_gdpr_erase', array( 'nonce' => 'VALID' ), 'administrator' );
report_test( 'GDPR Erase - Admin, valid nonce, missing email', $res, false, 'Should fail because email is required' );

// --- TEST 6: Lead Submission - Public, valid params ---
$res = test_ajax_action( 'cora_re_submit_lead', array(
    'names' => 'John Doe',
    'email' => 'john@example.com',
    'scale' => 'Medium',
    'city'  => 'New York',
    'notes' => 'Looking for apartments',
    'price' => '$500,000'
), null );
report_test( 'Lead Submission - Public, valid params', $res, true, 'Should succeed' );

// --- TEST 7: Lead Submission - Public, missing Email ---
$res = test_ajax_action( 'cora_re_submit_lead', array(
    'names' => 'John Doe',
    'scale' => 'Medium',
    'city'  => 'New York'
), null );
report_test( 'Lead Submission - Public, missing email', $res, false, 'Should fail as email is required' );

// --- TEST 8: Lead Submission - Public, missing Names ---
$res = test_ajax_action( 'cora_re_submit_lead', array(
    'email' => 'john@example.com',
    'scale' => 'Medium',
    'city'  => 'New York'
), null );
report_test( 'Lead Submission - Public, missing names', $res, false, 'Should fail as names are required' );

// --- TEST 9: Save Booking - Admin, valid Nonce, valid params ---
$res = test_ajax_action( 'cora_save_booking', array(
    'nonce' => 'VALID',
    'client_name' => 'Alice Smith',
    'deal_type' => 'Residential Buy',
    'location' => 'Delhi Office',
    'date' => '2026-07-15',
    'price' => '₹20,000'
), 'administrator' );
report_test( 'Save Booking - Admin, valid nonce & params', $res, true, 'Should succeed' );

// --- TEST 10: Save Booking - Admin, valid Nonce, missing client name ---
$res = test_ajax_action( 'cora_save_booking', array(
    'nonce' => 'VALID',
    'deal_type' => 'Residential Buy',
    'location' => 'Delhi Office'
), 'administrator' );
report_test( 'Save Booking - Admin, valid nonce, missing client_name', $res, false, 'Should fail as client_name is required' );

// --- TEST 11: Save Booking - Admin, invalid Nonce ---
$res = test_ajax_action( 'cora_save_booking', array(
    'nonce' => 'INVALID',
    'client_name' => 'Alice Smith'
), 'administrator' );
report_test( 'Save Booking - Admin, invalid nonce', $res, false, 'Should fail due to invalid nonce' );

// --- TEST 12: Save Portfolio (Gallery) - Admin, valid Nonce, valid params ---
$res = test_ajax_action( 'cora_save_portfolio', array(
    'nonce' => 'VALID',
    'title' => 'Test Gallery',
    'template' => 'grid',
    'assets' => json_encode( array(
        array( 'id' => 'asset_1', 'type' => 'image', 'url' => 'https://example.com/img.jpg', 'name' => 'Image 1' )
    ) )
), 'administrator' );
report_test( 'Save Portfolio - Admin, valid nonce & params', $res, true, 'Should succeed' );

// --- TEST 13: Save Portfolio - Admin, valid Nonce, missing title ---
$res = test_ajax_action( 'cora_save_portfolio', array(
    'nonce' => 'VALID',
    'template' => 'grid'
), 'administrator' );
report_test( 'Save Portfolio - Admin, valid nonce, missing title', $res, false, 'Should fail as title is required' );

// --- TEST 14: 3rd-Party Listing Sync (R2) ---
$res = test_ajax_action( 'cora_sync_listing_link', array(
    'security' => 'VALID',
    'url'      => 'https://www.zillow.com/homedetails/123456_zpid/'
), 'administrator' );
report_test( 'Listing Sync - Zillow URL', $res, true, 'Should extract Zillow Villa details' );

// --- TEST 15: Save Listing with SEO (R3) ---
$res = test_ajax_action( 'cora_re_save_listing', array(
    'security'        => 'VALID',
    'name'            => 'Test Suite Penthouse',
    'category'        => 'Penthouse',
    'rera_reg_id'     => 'TST-RERA-77',
    'notes'           => 'Synced from unit test.',
    'seo_title'       => 'Custom Penthouse Title',
    'seo_description' => 'Custom description details',
    'seo_keywords'    => 'test, custom, seo'
), 'administrator' );
report_test( 'Save Listing - With Custom SEO', $res, true, 'Should successfully save with manually specified SEO fields' );

// --- TEST 16: REST API Lead Webhook (R1) ---
$rest_error = null;
$rest_response = null;
try {
    $request = new WP_REST_Request( 'POST', '/cora/v1/leads' );
    $request->set_body_params( array(
        'names' => 'REST Api Lead',
        'email' => 'rest@example.com',
        'scale' => 'Large',
        'city'  => 'Mumbai',
        'price' => '₹1.5Cr',
        'notes' => 'Looking for commercial office space'
    ) );
    $rest_response = cora_post_leads_rest( $request );
} catch ( Exception $e ) {
    $rest_error = $e->getMessage();
}

$passed_rest = false;
$raw_out = '';
if ( is_wp_error( $rest_response ) ) {
    $raw_out = $rest_response->get_error_message();
} elseif ( $rest_response instanceof WP_REST_Response ) {
    $data = $rest_response->get_data();
    if ( ! empty( $data['success'] ) && $data['success'] === true ) {
        $passed_rest = true;
    }
    $raw_out = json_encode( $data );
} else {
    $raw_out = $rest_error ?: '';
}

$results[] = array(
    'name'             => 'REST API Lead Webhook - Valid Payload',
    'passed'           => $passed_rest,
    'success_returned' => $passed_rest,
    'raw_output'       => substr( trim( $raw_out ), 0, 100 ),
    'exception'        => $rest_error,
    'comment'          => 'Should parse JSON, generate lead ID, and save'
);



// Print summary table in Markdown
echo "\n### Test Results Summary\n\n";
echo "| Test Case | Passed? | Success Status | Exception/Output | Comment |\n";
echo "|---|---|---|---|---|\n";
foreach ( $results as $r ) {
    $passed_str = $r['passed'] ? '✅ PASS' : '❌ FAIL';
    $status_str = ( null === $r['success_returned'] ) ? 'N/A' : ( $r['success_returned'] ? 'true' : 'false' );
    $err_str = ! empty( $r['exception'] ) ? $r['exception'] : ( ! empty( $r['raw_output'] ) ? $r['raw_output'] : '' );
    // Clean markdown characters
    $err_str = str_replace( array( "\r", "\n", '|' ), ' ', $err_str );
    echo "| {$r['name']} | {$passed_str} | {$status_str} | `{$err_str}` | {$r['comment']} |\n";
}

echo "\nDone.\n";
