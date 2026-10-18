<?php
/**
 * Test Suite: Autonomous Content Campaigns & Multi-Model MCP Connectors
 */

define( 'DOING_TESTS', true );

require_once __DIR__ . '/../app/public/wp-load.php';
require_once __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/class-cora-autonomous-campaigns.php';
require_once __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/mcp/class-cora-mcp-tool-registry.php';

echo "\n========================================================\n";
echo " CORA AUTONOMOUS CAMPAIGNS & MCP E2E TEST RUNNER\n";
echo "========================================================\n\n";

$passed = 0;
$failed = 0;

function assert_test( $condition, $message ) {
    global $passed, $failed;
    if ( $condition ) {
        echo "  [PASS] " . $message . "\n";
        $passed++;
    } else {
        echo "  [FAIL] " . $message . "\n";
        $failed++;
    }
}

// 1. Data Model & Seeding
echo "--- 1. Campaign Data Model & Seed Retrieval ---\n";
$agency_id = 2; // Photography Studio test workspace
$campaigns = Cora_Autonomous_Campaigns::get_campaigns( $agency_id );
assert_test( is_array( $campaigns ), "Retrieved campaigns array from persistent storage" );
assert_test( count( $campaigns ) >= 2, "Default seeded campaigns present (count: " . count( $campaigns ) . ")" );

// Verify seeded campaign properties
$seed = $campaigns[0];
assert_test( ! empty( $seed['id'] ), "Campaign has unique ID: " . ( $seed['id'] ?? '' ) );
assert_test( ! empty( $seed['name'] ), "Campaign has name: " . ( $seed['name'] ?? '' ) );
assert_test( isset( $seed['min_seo_score'] ), "Campaign enforces min SEO score: " . ( $seed['min_seo_score'] ?? '' ) );
assert_test( isset( $seed['min_geo_score'] ), "Campaign enforces min GEO score: " . ( $seed['min_geo_score'] ?? '' ) );

// 2. Campaign Creation & Lifecycle
echo "\n--- 2. Campaign Creation & Pipeline Execution ---\n";
$new_campaign = Cora_Autonomous_Campaigns::create_campaign( array(
    'name'           => 'Automated Luxury Studio Series 2026',
    'topic_keywords' => array( 'luxury studio hire', 'commercial lighting', 'fashion shoot delhi' ),
    'cadence'        => 'weekly',
    'auto_publish'   => true,
    'min_seo_score'  => 85,
    'min_geo_score'  => 85,
    'tone'           => 'executive',
    'assigned_ai'    => 'gemini',
), $agency_id );

assert_test( ! empty( $new_campaign['id'] ), "Created new campaign with ID: " . $new_campaign['id'] );
$cmp_id = $new_campaign['id'];

// Trigger autonomous execution
$trigger_result = Cora_Autonomous_Campaigns::trigger_campaign( $cmp_id, $agency_id, 'mcp_test' );
assert_test( ! empty( $trigger_result['success'] ), "Campaign triggered successfully" );
assert_test( ! empty( $trigger_result['post_id'] ), "Generated article post ID: #" . ( $trigger_result['post_id'] ?? '' ) );
assert_test( isset( $trigger_result['seo_score'] ) && $trigger_result['seo_score'] >= 80, "Article achieved high SEO score: " . ( $trigger_result['seo_score'] ?? '' ) );
assert_test( isset( $trigger_result['geo_score'] ) && $trigger_result['geo_score'] >= 80, "Article achieved high GEO score: " . ( $trigger_result['geo_score'] ?? '' ) );
assert_test( in_array( $trigger_result['status'], array( 'publish', 'published', 'draft' ) ), "Valid post status applied: " . $trigger_result['status'] );

// Check updated campaign counts
$updated_cmp = Cora_Autonomous_Campaigns::get_campaign( $cmp_id, $agency_id );
assert_test( $updated_cmp['articles_generated_count'] >= 1, "Campaign articles count incremented: " . $updated_cmp['articles_generated_count'] );
assert_test( ! empty( $updated_cmp['last_run_at'] ), "Campaign last run timestamp recorded: " . $updated_cmp['last_run_at'] );

// 3. Autonomous Content Improvement Engine
echo "\n--- 3. Autonomous Content Improvement (SEO/GEO 90+) ---\n";
$post_id = $trigger_result['post_id'];
$improve_result = Cora_Autonomous_Campaigns::improve_content( $post_id, $agency_id );
assert_test( ! empty( $improve_result['success'] ), "Content improvement pipeline executed successfully" );
assert_test( $improve_result['seo_score'] >= 90, "Improved SEO score >= 90 (Achieved: " . $improve_result['seo_score'] . ")" );
assert_test( $improve_result['geo_score'] >= 90, "Improved GEO score >= 90 (Achieved: " . $improve_result['geo_score'] . ")" );

// 4. MCP Tools Registry Verification
echo "\n--- 4. Universal MCP Tools Registry Verification ---\n";
$mcp_tools = Cora_MCP_Tool_Registry::get_tools();
$expected_tools = array(
    'cora_create_campaign',
    'cora_list_campaigns',
    'cora_get_campaign',
    'cora_update_campaign',
    'cora_trigger_campaign_run',
    'cora_improve_content',
    'cora_get_autonomous_activity_log',
);

foreach ( $expected_tools as $tool_name ) {
    assert_test( isset( $mcp_tools[ $tool_name ] ), "MCP tool registered: " . $tool_name );
    if ( isset( $mcp_tools[ $tool_name ] ) ) {
        assert_test( ! empty( $mcp_tools[ $tool_name ]['description'] ), "  -> Description present for " . $tool_name );
        assert_test( is_callable( $mcp_tools[ $tool_name ]['handler'] ), "  -> Handler callable for " . $tool_name );
    }
}

// 5. MCP Handler Simulation
echo "\n--- 5. MCP Tool Handler Simulation ---\n";
$auth_context = array( 'workspace_id' => $agency_id, 'valid' => true );
$list_response = Cora_MCP_Tool_Registry::handle_list_campaigns( array(), $auth_context );
assert_test( isset( $list_response['campaigns'] ) && is_array( $list_response['campaigns'] ), "cora_list_campaigns returned valid payload" );

$activity_response = Cora_MCP_Tool_Registry::handle_get_autonomous_activity_log( array( 'limit' => 5 ), $auth_context );
assert_test( isset( $activity_response['activity_log'] ) && is_array( $activity_response['activity_log'] ), "cora_get_autonomous_activity_log returned valid payload" );

// Cleanup test records
echo "\n--- 6. Cleanup Test Records ---\n";
Cora_Autonomous_Campaigns::delete_campaign( $cmp_id, $agency_id );
wp_delete_post( $post_id, true );
echo "  [CLEANUP] Deleted temporary test campaign and post #{$post_id}\n";

// Rule 3 Privacy Verification
echo "\n--- 7. Rule 3 Privacy Compliance Verification ---\n";
$files_to_check = array(
    __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/class-cora-autonomous-campaigns.php',
    __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/mcp/class-cora-mcp-tool-registry.php',
    __DIR__ . '/../app/public/wp-content/plugins/cora-workspace/includes/partials/workspace-developer-gateway-drawer.php',
);
$privacy_violations = 0;
foreach ( $files_to_check as $file ) {
    $content = file_get_contents( $file );
    if ( preg_match( '/\bshruti\b/i', $content ) ) {
        $privacy_violations++;
    }
}
assert_test( $privacy_violations === 0, "Zero occurrences of owner name across modified files (Rule 3 compliant)" );

echo "\n========================================================\n";
echo " TEST SUMMARY: {$passed} PASSED, {$failed} FAILED\n";
echo "========================================================\n\n";

exit( $failed > 0 ? 1 : 0 );
