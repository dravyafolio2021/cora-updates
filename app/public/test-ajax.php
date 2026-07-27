<?php
require_once __DIR__ . '/wp-load.php';
global $wpdb;
$theme_id = 34;
$pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d ORDER BY is_homepage DESC, title ASC", $theme_id ), ARRAY_A );
echo json_encode($pages);
