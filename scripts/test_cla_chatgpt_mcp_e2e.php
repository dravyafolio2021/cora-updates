<?php
/**
 * CLA / ChatGPT MCP 12-Step Automated End-to-End Verification Suite
 *
 * Verifies that ChatGPT operates as a fully functional AI operator for CORA:
 * 1. Authenticated account identification (get_authenticated_account)
 * 2. Workspace discovery (list_workspaces)
 * 3. Workspace inspection & module capabilities (get_workspace)
 * 4. Read CRM leads (list_leads)
 * 5. Create task (create_task)
 * 6. Read back task (get_task)
 * 7. Update task (update_task)
 * 8. Create CMS article with SEO metadata (create_article)
 * 9. Upload media asset & thumbnail (upload_media)
 * 10. Publish article and verify live canonical URL (publish_article)
 * 11. Multi-tenant security check (reject cross-workspace access)
 * 12. Cleanup created test records (delete_task, delete_media, delete article)
 */

define( 'WP_USE_THEMES', false );
require_once __DIR__ . '/../app/public/wp-load.php';
require_once __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/mcp/class-cora-mcp-loader.php';

echo "====================================================================\n";
echo "   CORA WORKSPACE OS - CLA & CHATGPT MCP 12-STEP E2E VERIFICATION    \n";
echo "====================================================================\n\n";

$step_results = array();
function report_step( $step_num, $name, $success, $details = '' ) {
    global $step_results;
    $status = $success ? 'PASS [✓]' : 'FAIL [✗]';
    echo sprintf( "[Step %02d] %-48s %s\n", $step_num, $name, $status );
    if ( ! empty( $details ) ) {
        echo "          Details: " . trim( $details ) . "\n";
    }
    $step_results[ $step_num ] = $success;
}

// -------------------------------------------------------------------------
// Setup: Issue an active OAuth Bearer Token for CLA / ChatGPT
// -------------------------------------------------------------------------
$client_id    = 'cla_chatgpt_operator';
$user_id      = 1;
$workspace_id = '1';

$scopes_all = array_merge( array( '*' ), array_keys( Cora_OAuth_Server::get_supported_scopes() ) );
$auth_code = Cora_OAuth_Server::generate_auth_code(
    $client_id,
    $user_id,
    $workspace_id,
    'https://chatgpt.com/oauth/callback',
    'cla_verifier_challenge',
    'plain',
    $scopes_all
);

$token_req = new WP_REST_Request( 'POST', '/cora/v1/oauth/token' );
$token_req->set_body_params( array(
    'grant_type'    => 'authorization_code',
    'code'          => $auth_code,
    'client_id'     => $client_id,
    'code_verifier' => 'cla_verifier_challenge',
    'redirect_uri'  => 'https://chatgpt.com/oauth/callback',
) );

$token_res = Cora_OAuth_Server::handle_token( $token_req );
$token_data = $token_res->get_data();
$access_token = $token_data['access_token'] ?? '';

if ( empty( $access_token ) ) {
    echo "FATAL: Could not generate OAuth token for CLA.\n";
    exit( 1 );
}

echo "Authenticated CLA Token: " . substr( $access_token, 0, 18 ) . "...\n\n";

// Helper function to invoke MCP tools via JSON-RPC
function invoke_mcp_tool( $tool_name, $arguments = array(), $token = null ) {
    global $access_token;
    $tk = $token ?: $access_token;

    $req = new WP_REST_Request( 'POST', '/cora/v1/mcp' );
    $req->set_header( 'authorization', 'Bearer ' . $tk );
    $req->set_header( 'content-type', 'application/json' );
    $req->set_body( wp_json_encode( array(
        'jsonrpc' => '2.0',
        'id'      => rand( 100, 9999 ),
        'method'  => 'tools/call',
        'params'  => array(
            'name'      => $tool_name,
            'arguments' => $arguments,
        ),
    ) ) );

    $server = new Cora_Universal_MCP_Server();
    $res = $server->handle_request( $req );
    return $res->get_data();
}

// -------------------------------------------------------------------------
// Step 1: Connect and identify authenticated CORA account
// -------------------------------------------------------------------------
$step1_res = invoke_mcp_tool( 'get_authenticated_account' );
$step1_data = $step1_res['result']['content'][0]['text'] ?? null;
$step1_json = $step1_data ? json_decode( $step1_data, true ) : null;
$step1_pass = ! empty( $step1_json['authenticated'] ) && $step1_json['installed'] === true && ! empty( $step1_json['active_workspace_id'] );
report_step( 1, 'Authenticate and identify account', $step1_pass,
    $step1_pass ? "Account: {$step1_json['display_name']} | Workspace: {$step1_json['active_workspace_name']} (#{$step1_json['active_workspace_id']})" : wp_json_encode( $step1_res ) );

// -------------------------------------------------------------------------
// Step 2: List accessible workspaces
// -------------------------------------------------------------------------
$step2_res = invoke_mcp_tool( 'list_workspaces', array( 'status' => 'all' ) );
$step2_data = $step2_res['result']['content'][0]['text'] ?? null;
$step2_json = $step2_data ? json_decode( $step2_data, true ) : null;
$step2_pass = ! empty( $step2_json['workspaces'] ) && is_array( $step2_json['workspaces'] ) && count( $step2_json['workspaces'] ) >= 1;
report_step( 2, 'List accessible workspaces', $step2_pass,
    $step2_pass ? "Found {$step2_json['total']} workspaces. Active: #{$step2_json['active_workspace_id']}" : wp_json_encode( $step2_res ) );

// -------------------------------------------------------------------------
// Step 3: Retrieve selected workspace modules and available operations
// -------------------------------------------------------------------------
$step3_res = invoke_mcp_tool( 'get_workspace', array( 'workspace_id' => '1' ) );
$step3_data = $step3_res['result']['content'][0]['text'] ?? null;
$step3_json = $step3_data ? json_decode( $step3_data, true ) : null;
$step3_pass = ! empty( $step3_json['workspace_id'] ) && ! empty( $step3_json['modules']['websites_cms'] ) && ! empty( $step3_json['modules']['media_vault'] );
report_step( 3, 'Inspect workspace modules & capabilities', $step3_pass,
    $step3_pass ? "Workspace: '{$step3_json['name']}' | Industry: {$step3_json['industry']} | Active modules: " . count( $step3_json['modules'] ) : wp_json_encode( $step3_res ) );

// -------------------------------------------------------------------------
// Step 4: Read existing CRM leads
// -------------------------------------------------------------------------
$step4_res = invoke_mcp_tool( 'list_leads', array( 'limit' => 5 ) );
$step4_data = $step4_res['result']['content'][0]['text'] ?? null;
$step4_json = $step4_data ? json_decode( $step4_data, true ) : null;
$step4_pass = isset( $step4_json['total'] ) && is_array( $step4_json['leads'] );
report_step( 4, 'Read CRM leads from workspace', $step4_pass,
    $step4_pass ? "Retrieved {$step4_json['total']} leads in workspace." : wp_json_encode( $step4_res ) );

// -------------------------------------------------------------------------
// Step 5: Create a test task
// -------------------------------------------------------------------------
$created_task_id = null;
$step5_res = invoke_mcp_tool( 'create_task', array(
    'title'       => 'CLA Automation Test Task - Q4 Followups',
    'description' => 'Test task created autonomously by CLA ChatGPT integration test suite.',
    'priority'    => 'high',
    'status'      => 'todo',
    'due_date'    => date( 'Y-m-d', strtotime( '+7 days' ) ),
) );
$step5_data = $step5_res['result']['content'][0]['text'] ?? null;
$step5_json = $step5_data ? json_decode( $step5_data, true ) : null;
$step5_pass = ! empty( $step5_json['success'] ) && ! empty( $step5_json['task_id'] );
if ( $step5_pass ) {
    $created_task_id = $step5_json['task_id'];
}
report_step( 5, 'Create new operational task', $step5_pass,
    $step5_pass ? "Created task ID #{$created_task_id} ('{$step5_json['title']}')" : wp_json_encode( $step5_res ) );

// -------------------------------------------------------------------------
// Step 6: Read it back to verify persistence
// -------------------------------------------------------------------------
$step6_pass = false;
$step6_details = 'Task ID missing';
if ( $created_task_id ) {
    $step6_res = invoke_mcp_tool( 'get_task', array( 'task_id' => $created_task_id ) );
    $step6_data = $step6_res['result']['content'][0]['text'] ?? null;
    $step6_json = $step6_data ? json_decode( $step6_data, true ) : null;
    $step6_pass = ! empty( $step6_json['id'] ) && intval( $step6_json['id'] ) === intval( $created_task_id );
    $step6_details = $step6_pass ? "Verified persistent task #{$step6_json['id']} | Status: {$step6_json['status']}" : wp_json_encode( $step6_res );
}
report_step( 6, 'Read back task for persistence verification', $step6_pass, $step6_details );

// -------------------------------------------------------------------------
// Step 7: Update that task
// -------------------------------------------------------------------------
$step7_pass = false;
$step7_details = 'Task ID missing';
if ( $created_task_id ) {
    $step7_res = invoke_mcp_tool( 'update_task', array(
        'task_id'  => $created_task_id,
        'status'   => 'in_progress',
        'priority' => 'urgent',
    ) );
    $step7_data = $step7_res['result']['content'][0]['text'] ?? null;
    $step7_json = $step7_data ? json_decode( $step7_data, true ) : null;
    $step7_pass = ! empty( $step7_json['success'] ) && ! empty( $step7_json['updated'] );
    $step7_details = $step7_pass ? "Task #{$created_task_id} updated to in_progress / urgent" : wp_json_encode( $step7_res );
}
report_step( 7, 'Update task status and priority', $step7_pass, $step7_details );

// -------------------------------------------------------------------------
// Step 8: Create a CMS article with SEO metadata
// -------------------------------------------------------------------------
$created_article_id = null;
$step8_res = invoke_mcp_tool( 'create_article', array(
    'title'           => 'Architectural Photography Lighting Guide 2026',
    'content'         => '<h2>Mastering Ambient & Artificial Light</h2><p>In modern commercial photography, balancing ambient sunset tones with soft diffused strobes creates depth and drama.</p>',
    'excerpt'         => 'Complete guide to interior architectural lighting and production workflows.',
    'target_keywords' => array( 'architectural photography', 'studio lighting' ),
    'seo_title'       => 'Architectural Photography Lighting Guide | Cora Studio',
    'meta_description'=> 'Learn proven architectural lighting techniques for luxury interiors and commercial spaces.',
    'seo_score'       => 94,
    'geo_score'       => 88,
    'status'          => 'draft',
) );
$step8_data = $step8_res['result']['content'][0]['text'] ?? null;
$step8_json = $step8_data ? json_decode( $step8_data, true ) : null;
$created_article_id = $step8_json['article']['id'] ?? ( $step8_json['id'] ?? ( $step8_json['article_id'] ?? null ) );
$step8_pass = ! empty( $created_article_id );
$art_title  = $step8_json['article']['title'] ?? ( $step8_json['title'] ?? 'Article' );
report_step( 8, 'Create CMS article with SEO metadata', $step8_pass,
    $step8_pass ? "Created article ID #{$created_article_id} ('{$art_title}')" : wp_json_encode( $step8_res ) );

// -------------------------------------------------------------------------
// Step 9: Upload media asset and attach thumbnail
// -------------------------------------------------------------------------
$uploaded_media_id = null;
$sample_png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
$step9_res = invoke_mcp_tool( 'upload_media', array(
    'file_base64' => $sample_png,
    'file_name'   => 'cla-test-thumbnail.png',
    'title'       => 'CLA Article Hero Thumbnail',
    'alt_text'    => 'Architectural lighting hero thumbnail',
) );
$step9_data = $step9_res['result']['content'][0]['text'] ?? null;
$step9_json = $step9_data ? json_decode( $step9_data, true ) : null;
$step9_pass = ! empty( $step9_json['success'] ) && ! empty( $step9_json['media_id'] ) && ! empty( $step9_json['url'] );
if ( $step9_pass ) {
    $uploaded_media_id = $step9_json['media_id'];
    // Attach thumbnail to article
    if ( $created_article_id ) {
        set_post_thumbnail( $created_article_id, $uploaded_media_id );
    }
}
report_step( 9, 'Upload media asset & generate thumbnail', $step9_pass,
    $step9_pass ? "Media ID: #{$uploaded_media_id} | URL: {$step9_json['url']}" : wp_json_encode( $step9_res ) );

// -------------------------------------------------------------------------
// Step 10: Publish article and retrieve verified live URL
// -------------------------------------------------------------------------
$step10_pass = false;
$step10_details = 'Article ID missing';
if ( $created_article_id ) {
    $step10_res = invoke_mcp_tool( 'publish_article', array(
        'id' => $created_article_id,
    ) );
    $step10_data = $step10_res['result']['content'][0]['text'] ?? null;
    $step10_json = $step10_data ? json_decode( $step10_data, true ) : null;
    $live_url = $step10_json['live_url'] ?? ( $step10_json['public_url'] ?? ( $step10_json['url'] ?? '' ) );
    $step10_status = $step10_json['status'] ?? '';
    $step10_pass = ( $step10_status === 'published' || $step10_status === 'publish' ) && ! empty( $live_url );
    $step10_details = $step10_pass ? "Published live at: {$live_url}" : wp_json_encode( $step10_res );
}
report_step( 10, 'Publish article & verify live canonical URL', $step10_pass, $step10_details );

// -------------------------------------------------------------------------
// Step 11: Security check: Confirm cross-workspace operations are rejected
// -------------------------------------------------------------------------
// Create a temporary token for Workspace 2 and attempt to query Workspace 1's task
$client_id2 = 'cla_guest_operator';
$auth_code2 = Cora_OAuth_Server::generate_auth_code(
    $client_id2,
    2, // User 2 / non-admin
    '2', // Workspace 2
    'https://chatgpt.com/oauth/callback',
    'test_challenge_ws2',
    'plain',
    array( 'workspace:read', 'workspace:write' )
);

$t_req2 = new WP_REST_Request( 'POST', '/cora/v1/oauth/token' );
$t_req2->set_body_params( array(
    'grant_type'    => 'authorization_code',
    'code'          => $auth_code2,
    'client_id'     => $client_id2,
    'code_verifier' => 'test_challenge_ws2',
    'redirect_uri'  => 'https://chatgpt.com/oauth/callback',
) );
$t_res2 = Cora_OAuth_Server::handle_token( $t_req2 );
$t_data2 = $t_res2->get_data();
$ws2_token = $t_data2['access_token'] ?? '';

// Attempt to read workspace 1's task using workspace 2's token
$step11_res = invoke_mcp_tool( 'get_task', array( 'task_id' => $created_task_id ), $ws2_token );
$step11_is_error = ! empty( $step11_res['result']['isError'] ) || isset( $step11_res['error'] );
$step11_pass = $step11_is_error;
report_step( 11, 'Reject unauthorized cross-workspace queries', $step11_pass,
    $step11_pass ? "Cross-workspace task read was rejected as expected: " . ( $step11_res['result']['content'][0]['text'] ?? '' ) : "SECURITY FAILURE: Cross-workspace access was permitted!" );

// -------------------------------------------------------------------------
// Step 12: Clean up test records
// -------------------------------------------------------------------------
$cleanup_task = false;
$cleanup_art  = false;
$cleanup_med  = false;

if ( $created_task_id ) {
    $del_task_res = invoke_mcp_tool( 'delete_task', array( 'task_id' => $created_task_id ) );
    $del_task_data = $del_task_res['result']['content'][0]['text'] ?? null;
    $del_task_json = $del_task_data ? json_decode( $del_task_data, true ) : null;
    $cleanup_task  = ! empty( $del_task_json['deleted'] );
}

if ( $created_article_id ) {
    $wp_del = wp_delete_post( $created_article_id, true );
    $cleanup_art = (bool) $wp_del;
}

if ( $uploaded_media_id ) {
    $del_med_res = invoke_mcp_tool( 'delete_media', array( 'media_id' => $uploaded_media_id ) );
    $del_med_data = $del_med_res['result']['content'][0]['text'] ?? null;
    $del_med_json = $del_med_data ? json_decode( $del_med_data, true ) : null;
    $cleanup_med  = ! empty( $del_med_json['deleted'] );
}

$step12_pass = $cleanup_task && $cleanup_art && $cleanup_med;
report_step( 12, 'Clean up test records (task, article, media)', $step12_pass,
    "Task cleaned: " . ( $cleanup_task ? 'YES' : 'NO' ) . " | Article cleaned: " . ( $cleanup_art ? 'YES' : 'NO' ) . " | Media cleaned: " . ( $cleanup_med ? 'YES' : 'NO' ) );

echo "\n--------------------------------------------------------------------\n";
$total_passed = count( array_filter( $step_results ) );
$total_steps  = count( $step_results );
echo sprintf( "SUMMARY: %d / %d Tests Passed (%.1f%%)\n", $total_passed, $total_steps, ( $total_passed / $total_steps ) * 100 );
echo "--------------------------------------------------------------------\n";

if ( $total_passed === $total_steps ) {
    echo "SUCCESS: CLA / ChatGPT Integration is 100% operational!\n";
    exit( 0 );
} else {
    echo "ERROR: One or more steps failed. Check output details above.\n";
    exit( 1 );
}
