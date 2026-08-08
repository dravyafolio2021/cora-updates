<?php
require_once dirname(__DIR__) . '/app/public/wp-load.php';
global $wpdb;
$posts = $wpdb->get_results( "SELECT ID, post_title, post_name, post_status, post_content FROM {$wpdb->posts} WHERE post_content LIKE '%Ambassador%' OR post_title LIKE '%Ambassador%'", ARRAY_A );
print_r( $posts );
