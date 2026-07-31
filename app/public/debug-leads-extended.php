<?php
define('WP_USE_THEMES', false);
require_once('wp-load.php');

header('Content-Type: text/plain; charset=utf-8');

global $wpdb;

echo "=== wp_cora_leads DB TABLE ===\n";
$db_leads = $wpdb->get_results("SELECT id, agency_id, branch_id, first_name, last_name, status, converted_to_client FROM {$wpdb->prefix}cora_leads ORDER BY id ASC", ARRAY_A);
if ($db_leads) {
    foreach ($db_leads as $lead) {
        printf("ID: %s | Agency: %s | Branch: %s | Name: %s %s | Status: %s | Converted: %s\n",
            $lead['id'], $lead['agency_id'], $lead['branch_id'], $lead['first_name'], $lead['last_name'], $lead['status'], $lead['converted_to_client']);
    }
} else {
    echo "No leads found in database.\n";
}

echo "\n=== OPTION FLAGS ===\n";
echo "cora_seeded_3_leads_per_column_v2: " . var_export(get_option('cora_seeded_3_leads_per_column_v2'), true) . "\n";
echo "cora_migration_v2_complete: " . var_export(get_option('cora_migration_v2_complete'), true) . "\n";

echo "\n=== cora_workspace_leads OPTION ===\n";
$raw_opt = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'cora_workspace_leads'");
$opt_leads = !empty($raw_opt) ? maybe_unserialize($raw_opt) : array();
echo "Total leads in raw option: " . count($opt_leads) . "\n";
if (is_array($opt_leads)) {
    foreach ($opt_leads as $idx => $lead) {
        $id = $lead['id'] ?? 'N/A';
        $agency = $lead['agency_id'] ?? 'N/A';
        $branch = $lead['branch_id'] ?? 'N/A';
        $name = $lead['names'] ?? 'N/A';
        $status = $lead['status'] ?? 'N/A';
        echo "Index: $idx | ID: $id | Agency: $agency | Branch: $branch | Name: $name | Status: $status\n";
    }
}
