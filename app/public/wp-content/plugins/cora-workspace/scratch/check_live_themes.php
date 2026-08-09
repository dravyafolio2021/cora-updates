<?php
require_once __DIR__ . '/../../../../wp-load.php';

global $wpdb;
$live_themes = $wpdb->get_results("SELECT id, name, status, agency_id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live'", ARRAY_A);
echo "Live Themes in Database:\n";
print_r($live_themes);
