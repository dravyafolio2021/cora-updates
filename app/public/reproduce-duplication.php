<?php
define('WP_USE_THEMES', false);
require_once('wp-load.php');

header('Content-Type: text/plain');

// 1. Reset options
delete_option('cora_workspace_leads');
delete_option('cora_seeded_3_leads_per_column_v2');

// 2. Seed leads
cora_seed_3_leads_per_column();

// Check initial count
global $wpdb;
$raw_json = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'cora_workspace_leads'");
$items = !empty($raw_json) ? maybe_unserialize($raw_json) : array();
echo "Initial count in DB option: " . count($items) . "\n";

// 3. Set user to User 9 (cora_admin)
wp_set_current_user(9);
echo "Current user: cora_admin\n";
echo "Current agency ID: " . cora_get_current_user_agency_id() . "\n";
echo "Current branch ID: " . cora_get_current_user_branch_id() . "\n";

// 4. Simulate AJAX save twice
for ($i = 1; $i <= 2; $i++) {
    echo "\n--- Simulating Save #$i ---\n";
    $leads = get_option('cora_workspace_leads', array());
    echo "Fetched leads count: " . count($leads) . "\n";
    
    // Modify one lead
    if (!empty($leads)) {
        $leads[0]['status'] = 'Contacted';
    }
    
    update_option('cora_workspace_leads', $leads);
    
    $raw_json = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'cora_workspace_leads'");
    $items = !empty($raw_json) ? maybe_unserialize($raw_json) : array();
    echo "Count in DB option after save #$i: " . count($items) . "\n";
}
