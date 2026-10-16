<?php
/**
 * End-to-End Verification Test Script: Content Suite REST API, MCP Tools & UI AJAX
 *
 * Runs via CLI using the Local PHP 8.2 binary:
 * /Users/shrutian/Library/Application\ Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php scripts/test_content_suite_e2e.php
 */

define( 'WP_USE_THEMES', false );
define( 'WP_ADMIN', true );
define( 'DOING_AJAX', true );
$_SERVER['HTTP_HOST']   = 'cora.local';
$_SERVER['REQUEST_URI'] = '/wp-admin/admin-ajax.php';

require_once __DIR__ . '/../app/public/wp-load.php';

add_filter( 'wp_die_ajax_handler', function() {
    return function() {};
} );

echo "========================================================\n";
echo " CORA CONTENT SUITE E2E INTEGRATION & MCP TEST RUNNER\n";
echo "========================================================\n\n";

$pass_count = 0;
$fail_count = 0;

function cora_assert( $condition, $message ) {
    global $pass_count, $fail_count;
    if ( $condition ) {
        echo "  [PASS] {$message}\n";
        $pass_count++;
    } else {
        echo "  [FAIL] {$message}\n";
        $fail_count++;
    }
}

// Set active tenant agency context
$agency_id = 2; // Cora Photography Studio
if ( function_exists( 'cora_set_current_tenant_agency_id' ) ) {
    cora_set_current_tenant_agency_id( $agency_id );
}
$admin_user = get_user_by( 'login', 'cora_admin' ) ?: get_user_by( 'id', 1 );
wp_set_current_user( $admin_user->ID );

echo "--- 1. Workspace API Key Creation & Auth Bridge ---\n";
$key_data = Cora_Plugins_Skills_Engine::create_api_key(
    'E2E Test Content Agent',
    array( 'content:read', 'content:write', 'workspace:read' ),
    $agency_id
);
cora_assert( ! empty( $key_data['api_key'] ), "Generated live API key: {$key_data['key_prefix']}" );
$api_token = $key_data['api_key'];

// Test Auth Bridge
$auth_result = Cora_OAuth_Server::authenticate_token( 'Bearer ' . $api_token );
cora_assert( ! empty( $auth_result['valid'] ), "Cora_OAuth_Server authenticated workspace live key" );
cora_assert( (int) $auth_result['workspace_id'] === $agency_id, "Tenant agency ID correctly resolved to {$agency_id}" );
cora_assert( in_array( 'content:read', $auth_result['scopes'] ), "Scopes contain content:read" );
cora_assert( in_array( 'content:write', $auth_result['scopes'] ), "Scopes contain content:write" );

echo "\n--- 2. REST API Controller (/wp-json/cora/v1/content) ---\n";
// A. Create Article via REST
$req_create = new WP_REST_Request( 'POST', '/cora/v1/content' );
$req_create->set_header( 'Authorization', 'Bearer ' . $api_token );
$req_create->set_param( 'title', 'Commercial Studio Lighting Masterclass' );
$req_create->set_param( 'content', '<h2>Studio Lighting Essentials</h2><p>Proper strobe balance creates striking portraits and editorial results.</p><h2>How to Shape Key Light?</h2><p>Use a 5-foot octabox feathered at 45 degrees.</p>' );
$req_create->set_param( 'focus_keyword', 'commercial studio lighting' );
$req_create->set_param( 'meta_desc', 'Complete guide to commercial studio lighting setups and modifier techniques.' );
$req_create->set_param( 'status', 'draft' );

$res_create = Cora_Workspace_Content_REST_Controller::create_article( $req_create );
cora_assert( ! is_wp_error( $res_create ) && $res_create->get_status() === 201, "POST /content created article successfully (201)" );
$created_data = $res_create->get_data();
$test_post_id = $created_data['article']['id'] ?? 0;
cora_assert( $test_post_id > 0, "Created post ID: #{$test_post_id}" );
cora_assert( $created_data['article']['status'] === 'draft', "Initial status is 'draft'" );

// B. Single Article Fetch with SEO & GEO
$req_get = new WP_REST_Request( 'GET', '/cora/v1/content/' . $test_post_id );
$req_get->set_header( 'Authorization', 'Bearer ' . $api_token );
$req_get->set_param( 'id', $test_post_id );
$res_get = Cora_Workspace_Content_REST_Controller::get_single_article( $req_get );
cora_assert( ! is_wp_error( $res_get ) && $res_get->get_status() === 200, "GET /content/{id} fetched article (200)" );
$art_data = $res_get->get_data()['article'] ?? array();
cora_assert( isset( $art_data['seo']['seo_score'] ), "SEO score present: {$art_data['seo']['seo_score']}" );
cora_assert( isset( $art_data['geo']['geo_score'] ), "GEO score present: {$art_data['geo']['geo_score']}" );

// C. Update Article
$req_update = new WP_REST_Request( 'POST', '/cora/v1/content/' . $test_post_id );
$req_update->set_header( 'Authorization', 'Bearer ' . $api_token );
$req_update->set_param( 'id', $test_post_id );
$req_update->set_param( 'title', 'Commercial Studio Lighting Masterclass (Updated)' );
$res_update = Cora_Workspace_Content_REST_Controller::update_article( $req_update );
cora_assert( ! is_wp_error( $res_update ) && $res_update->get_status() === 200, "PATCH/POST /content/{id} updated article (200)" );
$updated_post = get_post( $test_post_id );
cora_assert( $updated_post->post_title === 'Commercial Studio Lighting Masterclass (Updated)', "Post title correctly updated in database" );

// D. Algorithmic 11-Point SEO Audit
$req_audit = new WP_REST_Request( 'POST', '/cora/v1/content/' . $test_post_id . '/seo-audit' );
$req_audit->set_header( 'Authorization', 'Bearer ' . $api_token );
$req_audit->set_param( 'id', $test_post_id );
$res_audit = Cora_Workspace_Content_REST_Controller::run_seo_audit( $req_audit );
cora_assert( ! is_wp_error( $res_audit ) && $res_audit->get_status() === 200, "POST /content/{id}/seo-audit executed audit (200)" );
$audit_data = $res_audit->get_data();
cora_assert( count( $audit_data['checks'] ) >= 10, "Audit returned 10+ granular SEO/GEO validation checks" );

// E. Publish and Unpublish
$req_pub = new WP_REST_Request( 'POST', '/cora/v1/content/' . $test_post_id . '/publish' );
$req_pub->set_header( 'Authorization', 'Bearer ' . $api_token );
$req_pub->set_param( 'id', $test_post_id );
$res_pub = Cora_Workspace_Content_REST_Controller::publish_article( $req_pub );
cora_assert( ! is_wp_error( $res_pub ) && $res_pub->get_status() === 200, "POST /content/{id}/publish published article (200)" );
cora_assert( get_post_status( $test_post_id ) === 'publish', "WP post status is now 'publish'" );

$req_unpub = new WP_REST_Request( 'POST', '/cora/v1/content/' . $test_post_id . '/unpublish' );
$req_unpub->set_header( 'Authorization', 'Bearer ' . $api_token );
$req_unpub->set_param( 'id', $test_post_id );
$res_unpub = Cora_Workspace_Content_REST_Controller::unpublish_article( $req_unpub );
cora_assert( ! is_wp_error( $res_unpub ) && $res_unpub->get_status() === 200, "POST /content/{id}/unpublish reverted article to draft (200)" );
cora_assert( get_post_status( $test_post_id ) === 'draft', "WP post status reverted to 'draft'" );

// F. Content Opportunities
$req_opps = new WP_REST_Request( 'GET', '/cora/v1/content/opportunities' );
$req_opps->set_header( 'Authorization', 'Bearer ' . $api_token );
$res_opps = Cora_Workspace_Content_REST_Controller::get_opportunities( $req_opps );
cora_assert( ! is_wp_error( $res_opps ) && $res_opps->get_status() === 200, "GET /content/opportunities fetched opportunities (200)" );

// G. Batch Operations
$req_batch = new WP_REST_Request( 'POST', '/cora/v1/content/batch' );
$req_batch->set_header( 'Authorization', 'Bearer ' . $api_token );
$req_batch->set_param( 'action', 'publish' );
$req_batch->set_param( 'article_ids', array( $test_post_id ) );
$res_batch = Cora_Workspace_Content_REST_Controller::batch_update_articles( $req_batch );
cora_assert( ! is_wp_error( $res_batch ) && $res_batch->get_status() === 200, "POST /content/batch executed bulk update (200)" );

echo "\n--- 3. MCP Tool Registry & Execution ---\n";
$all_tools = Cora_MCP_Tool_Registry::get_tools();
$expected_mcp_tools = array(
    'cora_list_articles',
    'cora_get_article',
    'cora_create_article',
    'cora_update_article',
    'cora_delete_article',
    'cora_publish_article',
    'cora_unpublish_article',
    'cora_bulk_update_articles',
    'cora_audit_article_seo',
    'cora_list_content_opportunities',
    'cora_generate_content_opportunities',
    'cora_generate_article_outline',
);

foreach ( $expected_mcp_tools as $tname ) {
    cora_assert( isset( $all_tools[ $tname ] ), "MCP tool registered: {$tname}" );
}

// Test MCP tool execution via handlers
$auth_context = array(
    'valid'        => true,
    'user_id'      => $admin_user->ID,
    'workspace_id' => $agency_id,
    'scopes'       => array( 'content:read', 'content:write', 'workspace:read' ),
);

// MCP: cora_list_articles
$mcp_list = Cora_MCP_Tool_Registry::handle_list_articles( array( 'limit' => 5 ), $auth_context );
cora_assert( is_array( $mcp_list ) && isset( $mcp_list['articles'] ), "MCP handle_list_articles returned articles payload" );

// MCP: cora_get_article
$mcp_get = Cora_MCP_Tool_Registry::handle_get_article( array( 'id' => $test_post_id ), $auth_context );
cora_assert( is_array( $mcp_get ) && isset( $mcp_get['article']['title'] ), "MCP handle_get_article returned single article details" );

// MCP: cora_audit_article_seo
$mcp_audit = Cora_MCP_Tool_Registry::handle_audit_article_seo( array( 'id' => $test_post_id ), $auth_context );
cora_assert( is_array( $mcp_audit ) && isset( $mcp_audit['seo_score'] ), "MCP handle_audit_article_seo executed successfully" );

// Clean up test post via MCP delete
$mcp_del = Cora_MCP_Tool_Registry::handle_delete_article( array( 'id' => $test_post_id, 'force' => true ), $auth_context );
cora_assert( is_array( $mcp_del ) && ! empty( $mcp_del['deleted'] ), "MCP handle_delete_article permanently cleaned up test post" );

echo "\n--- 4. UI AJAX Handlers & Calendar Rescheduling ---\n";
// Create a temporary post for AJAX tests
$ajax_post_id = wp_insert_post( array(
    'post_title'   => 'Calendar Reschedule Test Post',
    'post_content' => '<h2>Intro</h2><p>Sample content for calendar reschedule.</p>',
    'post_status'  => 'draft',
    'post_type'    => 'post',
    'post_author'  => $admin_user->ID,
) );

// Test cora_ajax_update_article_date
$_POST['post_id']     = $ajax_post_id;
$_POST['target_date'] = '2026-11-15';
$_POST['nonce']       = wp_create_nonce( 'cora_ajax_nonce' );

// Capture json output
ob_start();
try {
    cora_ajax_update_article_date();
} catch ( WPDieException $e ) {}
$res_date_raw = ob_get_clean();
$res_date = json_decode( $res_date_raw, true );
cora_assert( ! empty( $res_date['success'] ), "cora_ajax_update_article_date succeeded: {$res_date_raw}" );
$rescheduled_post = get_post( $ajax_post_id );
cora_assert( strpos( $rescheduled_post->post_date, '2026-11-15' ) === 0, "Post date updated to 2026-11-15" );

// Test GEO Auto-Optimize AJAX
ob_start();
try {
    cora_ajax_geo_auto_optimize();
} catch ( WPDieException $e ) {}
$res_geo_raw = ob_get_clean();
$res_geo = json_decode( $res_geo_raw, true );
cora_assert( ! empty( $res_geo['success'] ), "cora_ajax_geo_auto_optimize succeeded (geo_score: " . ( $res_geo['data']['geo_score'] ?? 'N/A' ) . ")" );

// Clean up AJAX test post
wp_delete_post( $ajax_post_id, true );

// Clean up test API key
Cora_Plugins_Skills_Engine::revoke_api_key( $key_data['id'], $agency_id );

echo "\n--- 5. Rule 3 Privacy Rule Verification ---\n";
$files_to_check = array(
    __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/class-cora-workspace-content-rest-controller.php',
    __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/mcp/class-cora-mcp-tool-registry.php',
    __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/mcp/class-cora-universal-mcp-server.php',
    __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/views/view-content-suite.php',
);

$privacy_violation = false;
foreach ( $files_to_check as $filepath ) {
    $content = file_get_contents( $filepath );
    if ( preg_match( '/\bshruti\b/i', $content, $matches ) ) {
        echo "  [FAIL] Privacy Rule 3 violation found in " . basename( $filepath ) . "\n";
        $privacy_violation = true;
    }
}
cora_assert( ! $privacy_violation, "Zero occurrences of owner name across modified files (Rule 3 compliant)" );

echo "\n========================================================\n";
echo " TEST SUMMARY: {$pass_count} PASSED, {$fail_count} FAILED\n";
echo "========================================================\n";

if ( $fail_count > 0 ) {
    exit( 1 );
}
exit( 0 );
