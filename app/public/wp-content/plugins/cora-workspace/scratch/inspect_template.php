<?php
require_once __DIR__ . '/../../../../wp-load.php';

global $wpdb;
$post_id = 2527;
$post = get_post($post_id);
if ($post) {
    echo "Post ID=$post_id\n";
    echo "  Title: " . $post->post_title . "\n";
    echo "  Post Type: " . $post->post_type . "\n";
    echo "  Post Status: " . $post->post_status . "\n";
    $meta = get_post_meta($post_id);
    echo "  Meta:\n";
    print_r($meta);
} else {
    echo "Post ID=$post_id NOT found!\n";
}
