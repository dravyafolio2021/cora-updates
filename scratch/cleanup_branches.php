<?php
require_once '/Users/shrutian/Desktop/cora/app/public/wp-load.php';
global $wpdb;

echo "=== CLEANING UP CHENNAI HUB ===\n";

// Delete from option
$branches = get_option('cora_branches', array());
foreach ($branches as $key => $b) {
    if (strpos($b['name'] ?? '', 'Chennai Hub') !== false) {
        unset($branches[$key]);
        echo "Removed from option: $key\n";
    }
}
update_option('cora_branches', $branches);

// Delete from DB table
$wpdb->query("DELETE FROM {$wpdb->prefix}cora_branches WHERE name LIKE '%Chennai Hub%'");
echo "Removed from database table.\n";

echo "=== DONE ===\n";
