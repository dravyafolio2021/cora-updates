<?php
require_once '/Users/shrutian/Desktop/cora/app/public/wp-load.php';
global $wpdb;

echo "=== cora_branches OPTION ===\n";
print_r(get_option('cora_branches'));

echo "\n=== wp_cora_branches TABLE ===\n";
$rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cora_branches", ARRAY_A);
print_r($rows);
