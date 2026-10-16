<?php
/**
 * Verification Test Suite: Plugins in App Module & Open API/MCP Architecture
 *
 * Runs full lifecycle tests on:
 * - Multi-tenant workspace database tables
 * - Starter plugins and AI skills seeding
 * - Custom plugin and skill CRUD
 * - Skill execution runtime and audit logging
 * - AI generator functions
 * - Workspace scoped API keys
 * - Dynamic MCP tool registration and tool resolution
 * - Rule 3 privacy compliance
 */

define( 'WP_USE_THEMES', false );
require_once dirname( __DIR__ ) . '/app/public/wp-load.php';

echo "====================================================================\n";
echo "   CORA WORKSPACE: PLUGINS, MODULES & SKILLS VERIFICATION SUITE    \n";
echo "====================================================================\n\n";

$pass_count = 0;
$fail_count = 0;

function run_test( $name, $callback ) {
    global $pass_count, $fail_count;
    echo "[TEST] {$name}... ";
    try {
        $result = call_user_func( $callback );
        if ( $result === true ) {
            echo "PASSED\n";
            $pass_count++;
        } else {
            echo "FAILED: " . ( is_string( $result ) ? $result : 'Check condition failed' ) . "\n";
            $fail_count++;
        }
    } catch ( Exception $e ) {
        echo "EXCEPTION: " . $e->getMessage() . "\n";
        $fail_count++;
    }
}

// 1. Database Schema
run_test( "Database tables existence", function() {
    global $wpdb;
    $tables = array(
        $wpdb->prefix . 'cora_workspace_plugins',
        $wpdb->prefix . 'cora_workspace_skills',
        $wpdb->prefix . 'cora_workspace_api_keys',
        $wpdb->prefix . 'cora_workspace_plugin_logs'
    );
    foreach ( $tables as $table ) {
        $found = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" );
        if ( $found !== $table ) {
            return "Table {$table} does not exist.";
        }
    }
    return true;
} );

// 2. Starter Plugins Seeding
run_test( "Starter plugins seeded for default workspace", function() {
    $plugins = Cora_Plugins_Skills_Engine::get_workspace_plugins( '1' );
    if ( empty( $plugins ) || count( $plugins ) < 5 ) {
        return "Expected at least 5 starter plugins, found " . count( $plugins );
    }
    $slugs = array_column( $plugins, 'plugin_slug' );
    $required = array( 'webhook-dispatcher', 'google-calendar-sync', 'payment-gateway-listener', 'client-portal-notifier', 'daily-ledger-reconciler' );
    foreach ( $required as $r ) {
        if ( ! in_array( $r, $slugs, true ) ) {
            return "Missing starter plugin: {$r}";
        }
    }
    return true;
} );

// 3. Starter Skills Seeding
run_test( "Starter AI skills seeded for default workspace", function() {
    $skills = Cora_Plugins_Skills_Engine::get_workspace_skills( '1' );
    if ( empty( $skills ) || count( $skills ) < 5 ) {
        return "Expected at least 5 starter skills, found " . count( $skills );
    }
    $slugs = array_column( $skills, 'skill_slug' );
    $required = array( 'client-intake-specialist', 'gst-invoice-auditor', 'high-ticket-lead-qualifier', 'shoot-gear-readiness-auditor', 'portfolio-storyteller' );
    foreach ( $required as $r ) {
        if ( ! in_array( $r, $slugs, true ) ) {
            return "Missing starter skill: {$r}";
        }
    }
    return true;
} );

// 4. Custom Plugin CRUD
run_test( "Custom plugin create, toggle, and delete", function() {
    $agency_id = '1';
    $custom_plugin = array(
        'name'            => 'Test WhatsApp Auto-Responder',
        'plugin_slug'     => 'test-whatsapp-auto-responder',
        'version'         => '1.0.0',
        'description'     => 'Test plugin for automated messaging.',
        'category'        => 'Integrations',
        'type'            => 'custom',
        'config_json'     => json_encode( array( 'phone_number_id' => '123456789' ) ),
        'mcp_tools_json'  => json_encode( array( array( 'name' => 'send_test_msg', 'description' => 'Sends test WhatsApp message' ) ) ),
        'author'          => 'Studio Director',
        'created_by_model'=> 'test_suite'
    );

    $id = Cora_Plugins_Skills_Engine::save_plugin( $custom_plugin, $agency_id );
    if ( ! $id ) {
        return "Failed to insert custom plugin.";
    }

    // Toggle
    global $wpdb;
    $table = $wpdb->prefix . 'cora_workspace_plugins';
    $wpdb->update( $table, array( 'status' => 'inactive' ), array( 'id' => $id ) );
    $status = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM {$table} WHERE id = %d", $id ) );
    if ( $status !== 'inactive' ) {
        return "Toggle status failed.";
    }

    // Cleanup
    $wpdb->delete( $table, array( 'id' => $id ) );
    return true;
} );

// 5. Custom Skill CRUD
run_test( "Custom AI skill create, toggle, and delete", function() {
    $agency_id = '1';
    $custom_skill = array(
        'name'              => 'Test Commercial Deal Closer',
        'skill_slug'        => 'test-commercial-deal-closer',
        'description'       => 'Test skill for deal closing strategy.',
        'category'          => 'CRM & Growth',
        'instruction_prompt'=> 'You are the commercial closer. Review deal objections and propose 50/50 milestone payment terms.',
        'trigger_phrases'   => 'close deal, handle objection, retainer negotiation',
        'parameters_schema' => json_encode( array( 'type' => 'object', 'properties' => array( 'deal_value' => array( 'type' => 'number' ) ) ) ),
        'bound_mcp_tools'   => json_encode( array( 'cora_list_leads' ) ),
        'execution_type'    => 'prompt_behavior',
        'model_preference'  => 'claude-3.5-sonnet',
        'created_by_model'  => 'test_suite'
    );

    $id = Cora_Plugins_Skills_Engine::save_skill( $custom_skill, $agency_id );
    if ( ! $id ) {
        return "Failed to insert custom skill.";
    }

    global $wpdb;
    $table = $wpdb->prefix . 'cora_workspace_skills';
    $wpdb->update( $table, array( 'status' => 'inactive' ), array( 'id' => $id ) );
    $status = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM {$table} WHERE id = %d", $id ) );
    if ( $status !== 'inactive' ) {
        return "Skill toggle status failed.";
    }

    // Cleanup
    $wpdb->delete( $table, array( 'id' => $id ) );
    return true;
} );

// 6. Skill Execution Runtime
run_test( "Skill execution runtime and audit log recording", function() {
    $agency_id = '1';
    $res = Cora_Plugins_Skills_Engine::execute_skill(
        'gst-invoice-auditor',
        array( 'taxable_amount' => 100000, 'client_state' => 'Maharashtra' ),
        $agency_id,
        'test_runner'
    );

    if ( is_wp_error( $res ) ) {
        return "Execution returned WP_Error: " . $res->get_error_message();
    }
    if ( empty( $res['skill_slug'] ) || $res['skill_slug'] !== 'gst-invoice-auditor' ) {
        return "Invalid execution response payload.";
    }

    // Verify audit log
    global $wpdb;
    $table_logs = $wpdb->prefix . 'cora_workspace_plugin_logs';
    $log = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$table_logs} WHERE agency_id = %s AND target_slug = 'gst-invoice-auditor' ORDER BY id DESC LIMIT 1",
        $agency_id
    ), ARRAY_A );

    if ( empty( $log ) || $log['caller_model'] !== 'test_runner' ) {
        return "Telemetry log entry was not recorded.";
    }
    return true;
} );

// 7. AI Plugin Generator
run_test( "AI Plugin Generator function", function() {
    $res = Cora_Plugins_Skills_Engine::generate_plugin_with_ai( "Sync shoots with external cloud backup and send completion webhook", '1' );
    if ( empty( $res['success'] ) || empty( $res['plugin_id'] ) ) {
        return "AI Plugin generator failed.";
    }
    // Clean up generated test plugin
    global $wpdb;
    $table = $wpdb->prefix . 'cora_workspace_plugins';
    $wpdb->delete( $table, array( 'id' => $res['plugin_id'] ) );
    return true;
} );

// 8. AI Skill Generator
run_test( "AI Skill Generator function", function() {
    $res = Cora_Plugins_Skills_Engine::generate_skill_with_ai( "Audits drone pilot DGCA licenses and weather wind knots before takeoff", '1' );
    if ( empty( $res['success'] ) || empty( $res['skill_id'] ) ) {
        return "AI Skill generator failed.";
    }
    // Clean up generated test skill
    global $wpdb;
    $table = $wpdb->prefix . 'cora_workspace_skills';
    $wpdb->delete( $table, array( 'id' => $res['skill_id'] ) );
    return true;
} );

// 9. Workspace Scoped API Keys
run_test( "Workspace Scoped API Key creation and verification", function() {
    $agency_id = '1';
    $key_info = Cora_Plugins_Skills_Engine::create_api_key( "Test Antigravity Agent Key", array( 'workspace:read', 'plugins:all', 'skills:all' ), $agency_id );
    if ( empty( $key_info['api_key'] ) || strpos( $key_info['api_key'], 'cora_ws_live_' ) !== 0 ) {
        return "Generated key format is invalid.";
    }

    // Authenticate with mock request object
    $mock_request = new WP_REST_Request( 'GET', '/cora/v1/workspace/plugins' );
    $mock_request->set_header( 'X-Cora-API-Key', $key_info['api_key'] );

    $authenticated_row = Cora_Plugins_Skills_Engine::authenticate_api_key( $mock_request );
    if ( empty( $authenticated_row ) || $authenticated_row['id'] != $key_info['id'] ) {
        return "API key authentication failed.";
    }

    // Revoke
    Cora_Plugins_Skills_Engine::revoke_api_key( $key_info['id'], $agency_id );
    $reauth = Cora_Plugins_Skills_Engine::authenticate_api_key( $mock_request );
    if ( $reauth !== false ) {
        return "Revoked API key was incorrectly authenticated.";
    }

    // Clean up
    global $wpdb;
    $wpdb->delete( $wpdb->prefix . 'cora_workspace_api_keys', array( 'id' => $key_info['id'] ) );
    return true;
} );

// 10. Dynamic MCP Tool Registration & Resolution
run_test( "Dynamic MCP Tool Registration in Cora_MCP_Tool_Registry", function() {
    if ( ! class_exists( 'Cora_MCP_Tool_Registry' ) ) {
        return "Cora_MCP_Tool_Registry class missing.";
    }
    $tools = Cora_MCP_Tool_Registry::get_tools();

    // Verify LLM management tools exist
    $required_tools = array(
        'cora_list_workspace_plugins',
        'cora_create_workspace_plugin',
        'cora_toggle_workspace_plugin',
        'cora_list_workspace_skills',
        'cora_create_workspace_skill',
        'cora_execute_workspace_skill',
        'cora_get_workspace_modules',
        'cora_toggle_workspace_module'
    );

    foreach ( $required_tools as $t ) {
        if ( ! isset( $tools[ $t ] ) ) {
            return "Missing MCP management tool: {$t}";
        }
    }

    // Verify dynamic skill tools exist
    if ( ! isset( $tools['cora_skill_gst_invoice_auditor'] ) ) {
        return "Missing dynamic MCP tool for cora_skill_gst_invoice_auditor";
    }

    // Verify tool name resolution
    $resolved = Cora_MCP_Tool_Registry::resolve_tool_name( 'skill_gst_invoice_auditor' );
    if ( $resolved !== 'cora_skill_gst_invoice_auditor' ) {
        return "resolve_tool_name did not map skill_gst_invoice_auditor correctly. Got: {$resolved}";
    }

    return true;
} );

// 11. Strict Rule 3 Privacy Compliance
run_test( "Rule 3 Identity Privacy Compliance (Zero owner name in new/modified code)", function() {
    $files = array(
        dirname( __DIR__ ) . '/app/public/wp-content/plugins/cora-workspace/includes/class-cora-plugins-skills-engine.php',
        dirname( __DIR__ ) . '/app/public/wp-content/plugins/cora-workspace/views/view-feature-hub.php'
    );
    foreach ( $files as $f ) {
        $content = file_get_contents( $f );
        if ( preg_match( '/\bshrut[ia]\b/i', $content ) ) {
            return "Forbidden owner name detected in: {$f}";
        }
    }
    return true;
} );

// 12. Workspace Plugin Upload via JSON Manifest
run_test( "Workspace Plugin Upload via JSON Manifest Specification", function() {
    $agency_id = '1';
    $manifest = json_encode( array(
        'name'        => 'Custom AirTable Studio CRM Sync',
        'plugin_slug' => 'airtable-crm-sync',
        'description' => 'Tenant-scoped AirTable sync engine',
        'category'    => 'Integrations',
        'version'     => '1.2.0',
        'author'      => 'Studio Lead',
        'config'      => array( 'base_id' => 'app123456789' ),
        'mcp_tools'   => array(
            array(
                'name'        => 'airtable_sync_contacts',
                'description' => 'Sync contacts to AirTable',
                'parameters'  => array( 'type' => 'object', 'properties' => array( 'limit' => array( 'type' => 'number' ) ) )
            )
        )
    ) );

    $res = Cora_Plugins_Skills_Engine::upload_plugin( $manifest, $agency_id );
    if ( is_wp_error( $res ) ) {
        return "Upload returned WP_Error: " . $res->get_error_message();
    }
    if ( empty( $res['id'] ) || $res['plugin_slug'] !== 'airtable-crm-sync' ) {
        return "Invalid upload response payload.";
    }

    // Verify row attributes in database
    global $wpdb;
    $table = $wpdb->prefix . 'cora_workspace_plugins';
    $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $res['id'] ), ARRAY_A );
    if ( empty( $row ) ) {
        return "Uploaded plugin record not found in database.";
    }
    if ( $row['type'] !== 'uploaded' || $row['created_by_model'] !== 'workspace_upload' || $row['agency_id'] !== $agency_id ) {
        return "Uploaded plugin metadata mismatch. Got type={$row['type']}, created_by={$row['created_by_model']}, agency={$row['agency_id']}";
    }

    // Clean up
    $wpdb->delete( $table, array( 'id' => $res['id'] ) );
    return true;
} );

// 13. Workspace Skill Upload via Antigravity SKILL.md format
run_test( "Workspace Skill Upload via Antigravity SKILL.md Format", function() {
    $agency_id = '1';
    $skill_md = "---\n" .
        "name: Drone Pre-Flight Safety Inspector\n" .
        "skill_slug: drone-safety-inspector\n" .
        "description: Verifies airspace permissions and equipment checklists\n" .
        "category: Production Operations\n" .
        "trigger_phrases: drone check, airspace clearance, uav flight\n" .
        "bound_mcp_tools:\n" .
        "  - cora_check_equipment\n" .
        "model_preference: claude-3.5-sonnet\n" .
        "---\n\n" .
        "You are the Drone Safety Agent for this studio. Verify all propeller integrity before takeoff.";

    $res = Cora_Plugins_Skills_Engine::upload_skill( $skill_md, $agency_id );
    if ( is_wp_error( $res ) ) {
        return "Upload returned WP_Error: " . $res->get_error_message();
    }
    if ( empty( $res['id'] ) || $res['skill_slug'] !== 'drone-safety-inspector' ) {
        return "Invalid upload response payload.";
    }

    global $wpdb;
    $table = $wpdb->prefix . 'cora_workspace_skills';
    $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $res['id'] ), ARRAY_A );
    if ( empty( $row ) ) {
        return "Uploaded skill record not found in database.";
    }
    if ( $row['created_by_model'] !== 'workspace_upload' || $row['agency_id'] !== $agency_id ) {
        return "Uploaded skill metadata mismatch. Got created_by={$row['created_by_model']}, agency={$row['agency_id']}";
    }
    if ( strpos( $row['instruction_prompt'], 'Drone Safety Agent' ) === false ) {
        return "Instruction prompt body was not parsed correctly from Markdown.";
    }

    // Clean up
    $wpdb->delete( $table, array( 'id' => $res['id'] ) );
    return true;
} );

// 14. Strict Cross-Tenant Isolation
run_test( "Cross-Tenant Workspace Isolation (Tenant A vs Tenant B)", function() {
    $tenant_a = '901';
    $tenant_b = '902';

    // Upload plugin & skill in tenant A
    $plugin_res = Cora_Plugins_Skills_Engine::upload_plugin( json_encode( array(
        'name'        => 'Tenant A Secret Vault Sync',
        'plugin_slug' => 'tenant-a-secret-vault-sync',
        'category'    => 'Integrations'
    ) ), $tenant_a );

    $skill_res = Cora_Plugins_Skills_Engine::upload_skill( "---\nname: Tenant A Secret Agent\nskill_slug: tenant-a-secret-agent\n---\nSecret instructions", $tenant_a );

    if ( is_wp_error( $plugin_res ) || is_wp_error( $skill_res ) ) {
        return "Setup of tenant A assets failed.";
    }

    // Query tenant B assets
    $b_plugins = Cora_Plugins_Skills_Engine::get_workspace_plugins( $tenant_b );
    $b_skills  = Cora_Plugins_Skills_Engine::get_workspace_skills( $tenant_b );

    $b_plugin_slugs = array_column( $b_plugins, 'plugin_slug' );
    $b_skill_slugs  = array_column( $b_skills, 'skill_slug' );

    // Tenant B must NOT see tenant A assets
    if ( in_array( 'tenant-a-secret-vault-sync', $b_plugin_slugs, true ) ) {
        return "LEAK DETECTED: Tenant B can see Tenant A's uploaded plugin!";
    }
    if ( in_array( 'tenant-a-secret-agent', $b_skill_slugs, true ) ) {
        return "LEAK DETECTED: Tenant B can see Tenant A's uploaded skill!";
    }

    // Clean up tenant A
    global $wpdb;
    $wpdb->delete( $wpdb->prefix . 'cora_workspace_plugins', array( 'id' => $plugin_res['id'] ) );
    $wpdb->delete( $wpdb->prefix . 'cora_workspace_skills', array( 'id' => $skill_res['id'] ) );
    return true;
} );

// 15. Plugin Action Dispatch Fault Isolation & Zero-Crash Shield
run_test( "Plugin Action Dispatch Zero-Crash Fault Isolation", function() {
    $agency_id = '1';

    // 15a. Valid dispatch on starter plugin
    $res = Cora_Plugins_Skills_Engine::dispatch_plugin_action(
        'webhook-dispatcher',
        'lead_captured',
        array( 'lead_id' => 101, 'deal_value' => 50000 ),
        $agency_id,
        'test_runner'
    );

    if ( empty( $res['execution_status'] ) || $res['execution_status'] !== 'success' ) {
        return "Dispatch on active starter plugin failed.";
    }

    // 15b. Faulty URL handling: plugin with malformed/unreachable URL must NOT crash PHP
    $bad_plugin_id = Cora_Plugins_Skills_Engine::save_plugin( array(
        'name'        => 'Faulty Webhook Plugin',
        'plugin_slug' => 'faulty-webhook-plugin',
        'category'    => 'Integrations',
        'config_json' => json_encode( array( 'webhook_url' => 'http://999.999.999.999/invalid-endpoint' ) )
    ), $agency_id );

    $fault_res = Cora_Plugins_Skills_Engine::dispatch_plugin_action(
        'faulty-webhook-plugin',
        'test_failure',
        array( 'data' => 'test' ),
        $agency_id,
        'test_runner'
    );

    // Verify it trapped the error gracefully without throwing unhandled fatal error
    if ( $fault_res['execution_status'] !== 'error' || $fault_res['success'] !== false ) {
        return "Fault isolation failed to catch invalid URL gracefully.";
    }

    // Clean up
    global $wpdb;
    $wpdb->delete( $wpdb->prefix . 'cora_workspace_plugins', array( 'id' => $bad_plugin_id ) );
    return true;
} );

echo "\n--------------------------------------------------------------------\n";
echo "TEST RESULTS: {$pass_count} PASSED, {$fail_count} FAILED\n";
echo "--------------------------------------------------------------------\n";

if ( $fail_count > 0 ) {
    exit( 1 );
}
exit( 0 );
