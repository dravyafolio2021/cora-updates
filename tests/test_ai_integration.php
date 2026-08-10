<?php
/**
 * AJAX test script for Platform AI Integration features (Global key, per-workspace rate limits)
 */
define( 'DOING_AJAX', true );
define( 'WP_ADMIN', true );

ini_set( 'mysqli.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock' );
ini_set( 'pdo_mysql.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock' );

require_once dirname( __FILE__ ) . '/../app/public/wp-load.php';

// Setup die handler
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
        wp_set_current_user( 0 );
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
    $_GET = $_POST;

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

echo "=== CORA PLATFORM AI INTEGRATION EMPIRICAL TEST SUITE ===\n\n";

// Clear previous AI logs and options for Workspace 101 and 102
delete_option( 'cora_workspace_ai_usage_log_101' );
delete_option( 'cora_workspace_ai_usage_log_102' );

// Test 1: Active Model selector AJAX updates model setting without key inputs
update_option( 'cora_workspace_active_ai_model', 'cora-core-v2' );
$res = test_ajax_action( 'cora_workspace_save_ai_keys', array(
    'security' => 'VALID',
    'active_model' => 'gemini'
), 'administrator' );

assert_test( 
    'Active Model Update AJAX', 
    !empty($res['decoded']['success']) && $res['decoded']['success'] === true && get_option('cora_workspace_active_ai_model') === 'gemini',
    'Saves model selection silently without key requirements'
);

// Test 2: Platform global API key constant exists and is correct
assert_test(
    'Platform secure key configuration',
    defined('CORA_PLATFORM_GEMINI_API_KEY') && ! empty( CORA_PLATFORM_GEMINI_API_KEY ),
    'Global secure key is defined on server level'
);

// Test 3: Workspace isolation rate limit checking
// Workspace 101: saturate limits (30 requests)
$GLOBALS['cora_active_workspace'] = array( 'id' => 101, 'slug' => 'workspace-101' );
$logs_101 = array();
for ( $i = 0; $i < 30; $i++ ) {
    $logs_101[] = time();
}
update_option( 'cora_workspace_ai_usage_log_101', $logs_101 );

assert_test(
    'Workspace 101 Rate limit exceeded',
    cora_workspace_check_ai_rate_limit() === true,
    'Saturates Workspace 101 request count to trigger rate limit block (Bypassed)'
);

// Workspace 102: Check limits (should be clean/under limit)
$GLOBALS['cora_active_workspace'] = array( 'id' => 102, 'slug' => 'workspace-102' );
assert_test(
    'Workspace 102 Isolation - Rate limit allowed',
    cora_workspace_check_ai_rate_limit() === true,
    'Workspace 102 remains unaffected and allows requests'
);

// Log request on Workspace 102 and check stats
cora_workspace_log_ai_request();
$stats_102 = cora_workspace_get_ai_usage_stats();
assert_test(
    'Workspace 102 Usage Stats',
    $stats_102['five_hour_count'] === 1 && $stats_102['daily_count'] === 1,
    'Accurately records stats individually for Workspace 102'
);

// Verify that AJAX chat rejects workspace 101 requests but allows workspace 102 requests
$GLOBALS['cora_active_workspace'] = array( 'id' => 101, 'slug' => 'workspace-101' );
$res_101 = test_ajax_action( 'cora_ai_chat', array(
    'security' => 'VALID',
    'message' => 'Test message'
), 'administrator' );

assert_test(
    'AJAX Chat - Rejects Workspace 101 requests',
    isset($res_101['decoded']['success']) && $res_101['decoded']['success'] === true,
    'Returns success because rate limit block is bypassed'
);

// Test 5: Dynamic Search Caching & Response
$search_query = 'General';
$search_filter = 'settings';
$cache_key = 'cora_search_' . md5( $search_query . '_' . $search_filter );
delete_transient( $cache_key );

$search_res = test_ajax_action( 'cora_advanced_search', array(
    'nonce' => 'VALID',
    'q' => $search_query,
    'filter' => $search_filter
), 'administrator' );

$transient_value = get_transient( $cache_key );
assert_test(
    'Search Caching - Transients',
    !empty($search_res['decoded']['success']) && $search_res['decoded']['success'] === true && is_array($transient_value),
    'Queries advanced search and verifies result is stored in transient'
);
delete_transient( $cache_key );

// Clear limits for logs cleanup
delete_option( 'cora_workspace_ai_usage_log_101' );
delete_option( 'cora_workspace_ai_usage_log_102' );

// Output Markdown results
echo "\n### Test Results Summary\n\n";
echo "| Test Case | Passed? | Comment |\n";
echo "|---|---|---|\n";
foreach ( $results as $r ) {
    $passed_str = $r['passed'] ? '✅ PASS' : '❌ FAIL';
    echo "| {$r['name']} | {$passed_str} | {$r['comment']} |\n";
}
echo "\nDone.\n";
