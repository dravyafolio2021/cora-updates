<?php
require_once __DIR__ . '/../../../../wp-load.php';

global $wpdb;
$results = $wpdb->get_results("SELECT id, name, status, settings FROM {$wpdb->prefix}cora_canvas_themes", ARRAY_A);
print_r($results);
