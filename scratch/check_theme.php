<?php
require_once dirname(__DIR__) . '/app/public/wp-load.php';
global $wpdb;
$theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = 195", ARRAY_A );
print_r( $theme );
$pages = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = 195", ARRAY_A );
print_r( $pages );
