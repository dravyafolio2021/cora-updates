<?php
define('WP_USE_THEMES', false);
require_once('wp-load.php');

header('Content-Type: text/plain');

// Retrieve directly from wp_options to bypass filters
global $wpdb;
$raw_json = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'cora_workspace_leads'");
$items = !empty($raw_json) ? maybe_unserialize($raw_json) : array();

echo "Total items in DB option: " . count($items) . "\n\n";
foreach ($items as $idx => $item) {
    $id = $item['id'] ?? 'N/A';
    $name = $item['names'] ?? 'N/A';
    $agency = $item['agency_id'] ?? 'N/A';
    $branch = $item['branch_id'] ?? 'N/A';
    $status = $item['status'] ?? 'N/A';
    echo "Index: $idx | ID: $id | Agency: $agency | Branch: $branch | Name: $name | Status: $status\n";
}
