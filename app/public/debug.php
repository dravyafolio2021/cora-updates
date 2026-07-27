<?php
require_once __DIR__ . '/wp-load.php';
global $wpdb;
echo "THEMES:\n";
print_r($wpdb->get_results("SELECT id, name, status FROM {$wpdb->prefix}cora_canvas_themes", ARRAY_A));
echo "\nPAGES:\n";
print_r($wpdb->get_results("SELECT id, theme_id, title, slug, status FROM {$wpdb->prefix}cora_canvas_pages", ARRAY_A));
