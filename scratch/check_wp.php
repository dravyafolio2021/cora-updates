<?php
require_once '/Users/shrutian/Desktop/cora/app/public/wp-load.php';
echo "ACTIVE PLUGINS:\n";
print_r(get_option('active_plugins'));
echo "\nTHEME:\n";
echo get_option('stylesheet') . "\n";
echo "\nCANVAS THEMES:\n";
global $wpdb;
$themes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cora_canvas_themes", ARRAY_A);
print_r($themes);
