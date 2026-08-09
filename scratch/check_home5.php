<?php
require_once '/Users/shrutian/Desktop/cora/app/public/wp-load.php';
$post = get_page_by_path('home-5');
if ($post) {
    echo "POST EXISTS:\n";
    echo "ID: " . $post->ID . "\n";
    echo "Slug: " . $post->post_name . "\n";
    echo "Template Meta: " . get_post_meta($post->ID, '_wp_page_template', true) . "\n";
    global $wpdb;
    $cora_page = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE wp_post_id = %d", $post->ID), ARRAY_A);
    echo "CORA CANVAS PAGE:\n";
    print_r($cora_page);
} else {
    echo "POST 'home-5' DOES NOT EXIST!\n";
}
