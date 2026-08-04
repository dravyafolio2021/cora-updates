<?php
/**
 * Test Suite for granular capabilities, LLM doubts, and permissions matrix upgrades
 */
define( 'DOING_AJAX', true );
define( 'WP_ADMIN', true );

ini_set( 'mysqli.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock' );
ini_set( 'pdo_mysql.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock' );

require_once dirname( __FILE__ ) . '/../app/public/wp-load.php';

// Setup custom wp_die handlers to capture and not crash the script
add_filter( 'wp_die_ajax_handler', 'cora_test_die_handler' );
add_filter( 'wp_die_handler', 'cora_test_die_handler' );

if ( ! function_exists( 'cora_test_die_handler' ) ) {
function cora_test_die_handler() {
    return function( $message, $title = '', $args = array() ) {
        if ( is_wp_error( $message ) ) {
            $message = $message->get_error_message();
        }
        throw new Exception( (string) $message );
    };
}
}

function test_ajax_action( $action, $params = array(), $user_role = null ) {
    $old_post = $_POST;
    $old_request = $_REQUEST;
    $old_user_id = get_current_user_id();

    if ( $user_role === 'administrator' ) {
        $admins = get_users( array( 'role' => 'administrator' ) );
        if ( ! empty( $admins ) ) {
            wp_set_current_user( $admins[0]->ID );
        } else {
            wp_set_current_user( 1 );
        }
    } else {
        wp_set_current_user( 0 ); // Guest
    }

    $_POST = $params;
    $_POST['action'] = $action;

    if ( isset( $params['nonce'] ) && $params['nonce'] === 'VALID' ) {
        $_POST['nonce'] = wp_create_nonce( 'cora_ajax_nonce' );
    }
    if ( isset( $params['security'] ) && $params['security'] === 'VALID' ) {
        $_POST['security'] = wp_create_nonce( 'cora_ajax_nonce' );
    }

    $_REQUEST = $_POST;

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

    wp_set_current_user( $old_user_id );
    $_POST = $old_post;
    $_REQUEST = $old_request;

    return array(
        'raw'       => $output,
        'decoded'   => json_decode( $output, true ),
        'exception' => $error
    );
}

$results = array();
function assert_test( $name, $expression, $comment = '' ) {
    global $results;
    $results[] = array(
        'name' => $name,
        'passed' => (bool)$expression,
        'comment' => $comment
    );
}

echo "=== CORA NEW CAPABILITIES & AI ENDPOINTS TEST SUITE ===\n\n";

// Clear existing values
delete_option( 'cora_role_permission_levels' );
delete_option( 'cora_role_permissions' );
delete_option( 'cora_workspace_ai_permissions_training_prompt' );

// 1. Test cora_user_has_feature_level & cora_user_has_feature_access default fallbacks
assert_test(
    '1. Functions exist check',
    function_exists( 'cora_user_has_feature_level' ) && function_exists( 'cora_user_has_feature_access' ),
    'Both cora_user_has_feature_level and cora_user_has_feature_access should exist.'
);

$admin = get_users( array( 'role' => 'administrator' ) );
$admin_user = ! empty( $admin ) ? $admin[0] : null;

if ( $admin_user ) {
    assert_test(
        '2. Admin level check',
        cora_user_has_feature_level( 'dashboard', 'edit', $admin_user ) === true &&
        cora_user_has_feature_level( 'dashboard', 'view', $admin_user ) === true,
        'Admins should have full edit and view feature access.'
    );
}

// Create a test user with subscriber role or check a viewer/manager
$viewer_users = get_users( array( 'role' => 'cora_viewer' ) );
if ( empty( $viewer_users ) ) {
    // If no viewer exists, create one temporarily
    $viewer_id = wp_create_user( 'testviewer', 'ViewerPass123!', 'testviewer@example.com' );
    $viewer_user = get_userdata( $viewer_id );
    if ( $viewer_user ) {
        $viewer_user->set_role( 'cora_viewer' );
    }
} else {
    $viewer_user = $viewer_users[0];
}

if ( $viewer_user ) {
    // By default, cora_viewer has view/edit for dashboard, portfolio, bookings
    assert_test(
        '3. Default lists fallback',
        cora_user_has_feature_level( 'bookings', 'view', $viewer_user ) === true,
        'cora_viewer default fallback should grant view access to bookings.'
    );

    assert_test(
        '4. Backward Compatibility access check',
        cora_user_has_feature_access( 'bookings', $viewer_user ) === true,
        'cora_user_has_feature_access should return true since bookings has view access.'
    );
}

// 2. Test cora_ajax_save_permissions_matrix (granular + legacy sync)
$matrix_to_save = array(
    'cora_viewer' => array(
        'dashboard' => 'view',
        'bookings' => 'none',
        'portfolio' => 'edit'
    )
);

$save_res = test_ajax_action( 'cora_ajax_save_permissions_matrix', array(
    'security' => 'VALID',
    'matrix' => $matrix_to_save
), 'administrator' );

assert_test(
    '5. AJAX Save Matrix - Success response',
    ! empty( $save_res['decoded']['success'] ) && $save_res['decoded']['success'] === true,
    'Saving granular permission matrix should return success.'
);

$saved_levels = get_option( 'cora_role_permission_levels' );
assert_test(
    '6. Option cora_role_permission_levels updated',
    is_array( $saved_levels ) && isset( $saved_levels['cora_viewer'] ) && $saved_levels['cora_viewer']['portfolio'] === 'edit',
    'Option cora_role_permission_levels should contain stored levels.'
);

$saved_legacy = get_option( 'cora_role_permissions' );
assert_test(
    '7. Option cora_role_permissions sync check',
    is_array( $saved_legacy ) && isset( $saved_legacy['cora_viewer'] ) &&
    in_array( 'dashboard', $saved_legacy['cora_viewer'], true ) &&
    in_array( 'portfolio', $saved_legacy['cora_viewer'], true ) &&
    ! in_array( 'bookings', $saved_legacy['cora_viewer'], true ),
    'Option cora_role_permissions should only contain features set to view or edit.'
);

if ( $viewer_user ) {
    assert_test(
        '8. Capability checks using granular settings (view level)',
        cora_user_has_feature_level( 'portfolio', 'view', $viewer_user ) === true &&
        cora_user_has_feature_level( 'portfolio', 'edit', $viewer_user ) === true,
        'User should have edit and view access to portfolio.'
    );

    assert_test(
        '9. Capability checks using granular settings (none level)',
        cora_user_has_feature_level( 'bookings', 'view', $viewer_user ) === false,
        'User should have NO access to bookings after setting to none.'
    );
}

// 3. Test cora_ajax_save_training_prompt
$test_prompt = "Custom system training prompt for unit testing.";
$prompt_save_res = test_ajax_action( 'cora_ajax_save_training_prompt', array(
    'security' => 'VALID',
    'prompt' => $test_prompt
), 'administrator' );

assert_test(
    '10. Save training prompt success',
    ! empty( $prompt_save_res['decoded']['success'] ) && $prompt_save_res['decoded']['success'] === true &&
    get_option( 'cora_workspace_ai_permissions_training_prompt' ) === $test_prompt,
    'Admin should be able to save training prompt.'
);

// 4. Test cora_ajax_ask_llm_doubt
$ask_doubt_res = test_ajax_action( 'cora_ajax_ask_llm_doubt', array(
    'security' => 'VALID',
    'doubt' => 'How does the portfolio edit access work?',
    'platform' => 'Photography'
), 'administrator' );

assert_test(
    '11. Ask LLM Doubt AJAX call status',
    ! empty( $ask_doubt_res['decoded']['success'] ) && ! empty( $ask_doubt_res['decoded']['data']['reply'] ),
    'Doubt endpoint should successfully call AI API and retrieve reply. Provider: ' . ( $ask_doubt_res['decoded']['data']['provider'] ?? 'Unknown' )
);

// Clean up temporary viewer user if created
if ( isset( $viewer_id ) ) {
    wp_delete_user( $viewer_id );
}

// Output results
echo "\n### Test Results Summary\n\n";
echo "| Test Case | Passed? | Comment |\n";
echo "|---|---|---|\n";
foreach ( $results as $r ) {
    $passed_str = $r['passed'] ? '✅ PASS' : '❌ FAIL';
    echo "| {$r['name']} | {$passed_str} | {$r['comment']} |\n";
}
echo "\nDone.\n";
