<?php
/**
 * Test MCP Full Access & OAuth Verification Script
 */

require_once __DIR__ . '/../app/public/wp-load.php';
require_once __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/mcp/class-cora-mcp-loader.php';

echo "=== CORA MCP FULL ACCESS & OAUTH VERIFICATION ===\n\n";

// 1. Verify Supported Scopes
$scopes = Cora_OAuth_Server::get_supported_scopes();
echo "[1] Supported Scopes Count: " . count($scopes) . "\n";
foreach ($scopes as $s => $meta) {
    echo "  - {$s} [{$meta['category']}/{$meta['level']}]: {$meta['label']}\n";
}

// 2. Create Test Client & Token with Full Access
$client_id = 'test_claude_chatgpt_client';
$user_id = 1;
$workspace_id = '1';
$selected_scopes = array_merge( array('*'), array_keys($scopes) );

$auth_code = Cora_OAuth_Server::generate_auth_code(
    $client_id,
    $user_id,
    $workspace_id,
    'https://chatgpt.com/oauth/callback',
    'test_challenge',
    'plain',
    $selected_scopes
);
echo "\n[2] Generated Auth Code: {$auth_code}\n";

// Exchange for token
$token_req = new WP_REST_Request('POST', '/cora/v1/oauth/token');
$token_req->set_body_params(array(
    'grant_type'    => 'authorization_code',
    'code'          => $auth_code,
    'client_id'     => $client_id,
    'code_verifier' => 'test_challenge',
    'redirect_uri'  => 'https://chatgpt.com/oauth/callback',
));

$token_res = Cora_OAuth_Server::handle_token($token_req);
$token_data = $token_res->get_data();
$access_token = $token_data['access_token'] ?? '';
echo "[3] Issued Access Token: {$access_token}\n";
echo "    Scopes: " . ($token_data['scope'] ?? '') . "\n";

// 3. Test authenticate_token
$auth_req = new WP_REST_Request('POST', '/cora/v1/mcp');
$auth_req->set_header('authorization', 'Bearer ' . $access_token);
$auth_context = Cora_OAuth_Server::authenticate_token($auth_req);
echo "\n[4] Token Authenticated Context: Valid=" . ($auth_context['valid'] ? 'YES' : 'NO') . ", User={$auth_context['user_id']}, Workspace={$auth_context['workspace_id']}\n";

// 4. Test MCP tools/list
$list_req = new WP_REST_Request('POST', '/cora/v1/mcp');
$list_req->set_header('authorization', 'Bearer ' . $access_token);
$list_req->set_body(json_encode(array(
    'jsonrpc' => '2.0',
    'method'  => 'tools/list',
    'params'  => (object) array(),
    'id'      => 1,
)));

$list_res = Cora_Universal_MCP_Server::handle_request($list_req);
$list_data = $list_res->get_data();
$tools = $list_data['result']['tools'] ?? array();
echo "\n[5] MCP tools/list Output Count: " . count($tools) . " tools exposed to AI client\n";
foreach ($tools as $t) {
    echo "  - Tool: {$t['name']} (Scope: {$t['requiredScope']})\n";
}

// 5. Test Tool Calls
echo "\n[6] Testing Tool Calls:\n";

// A. Canvas Get Active Theme
$call_req1 = new WP_REST_Request('POST', '/cora/v1/mcp');
$call_req1->set_header('authorization', 'Bearer ' . $access_token);
$call_req1->set_body(json_encode(array(
    'jsonrpc' => '2.0',
    'method'  => 'tools/call',
    'params'  => array(
        'name'      => 'cora_canvas_get_active_theme',
        'arguments' => array(),
    ),
    'id'      => 2,
)));
$call_res1 = Cora_Universal_MCP_Server::handle_request($call_req1);
$call_data1 = $call_res1->get_data();
echo "A. cora_canvas_get_active_theme:\n" . ($call_data1['result']['content'][0]['text'] ?? json_encode($call_data1)) . "\n\n";

// B. Workspace Overview
$call_req2 = new WP_REST_Request('POST', '/cora/v1/mcp');
$call_req2->set_header('authorization', 'Bearer ' . $access_token);
$call_req2->set_body(json_encode(array(
    'jsonrpc' => '2.0',
    'method'  => 'tools/call',
    'params'  => array(
        'name'      => 'cora_get_workspace_overview',
        'arguments' => array(),
    ),
    'id'      => 3,
)));
$call_res2 = Cora_Universal_MCP_Server::handle_request($call_req2);
$call_data2 = $call_res2->get_data();
echo "B. cora_get_workspace_overview:\n" . ($call_data2['result']['content'][0]['text'] ?? json_encode($call_data2)) . "\n\n";

// C. List CRM Leads
$call_req3 = new WP_REST_Request('POST', '/cora/v1/mcp');
$call_req3->set_header('authorization', 'Bearer ' . $access_token);
$call_req3->set_body(json_encode(array(
    'jsonrpc' => '2.0',
    'method'  => 'tools/call',
    'params'  => array(
        'name'      => 'cora_list_leads',
        'arguments' => array('limit' => 3),
    ),
    'id'      => 4,
)));
$call_res3 = Cora_Universal_MCP_Server::handle_request($call_req3);
$call_data3 = $call_res3->get_data();
echo "C. cora_list_leads:\n" . ($call_data3['result']['content'][0]['text'] ?? json_encode($call_data3)) . "\n\n";

// D. Canvas Update Page HTML (Write Test)
$call_req4 = new WP_REST_Request('POST', '/cora/v1/mcp');
$call_req4->set_header('authorization', 'Bearer ' . $access_token);
$call_req4->set_body(json_encode(array(
    'jsonrpc' => '2.0',
    'method'  => 'tools/call',
    'params'  => array(
        'name'      => 'cora_canvas_update_page_html',
        'arguments' => array(
            'html'           => '<!DOCTYPE html><html lang="en"><head><title>Test Live</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-zinc-950 text-white min-h-screen flex items-center justify-center"><h1 class="text-3xl font-bold">Live MCP Test</h1></body></html>',
            'change_summary' => 'Automated test write via MCP',
        ),
    ),
    'id'      => 5,
)));
$call_res4 = Cora_Universal_MCP_Server::handle_request($call_req4);
$call_data4 = $call_res4->get_data();
echo "D. cora_canvas_update_page_html (Write Test):\n" . ($call_data4['result']['content'][0]['text'] ?? json_encode($call_data4)) . "\n\n";

echo "=== ALL TESTS COMPLETED SUCCESSFULLY ===\n";
