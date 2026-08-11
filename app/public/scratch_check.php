<?php
require_once dirname(__FILE__) . '/wp-load.php';

header('Content-Type: text/plain');

global $wpdb;

echo "--- POSTS WITH SLUG home-5 OR onboarding ---\n";
$posts = $wpdb->get_results("SELECT ID, post_title, post_name, post_status FROM {$wpdb->posts} WHERE post_name IN ('home-5', 'onboarding')");
foreach ($posts as $p) {
    $template = get_post_meta($p->ID, '_wp_page_template', true);
    echo "ID: {$p->ID} | Title: {$p->post_title} | Slug: {$p->post_name} | Status: {$p->post_status} | Meta Template: {$template}\n";
}

echo "\n--- CANVAS PAGES ---\n";
$canvas_pages = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cora_canvas_pages");
foreach ($canvas_pages as $cp) {
    echo "ID: {$cp->id} | Title: {$cp->title} | Slug: {$cp->slug} | WP Post ID: {$cp->wp_post_id} | Template: {$cp->template}\n";
}

echo "\n--- SHOW ON FRONT OPTIONS ---\n";
echo "show_on_front: " . get_option('show_on_front') . "\n";
echo "page_on_front: " . get_option('page_on_front') . "\n";
