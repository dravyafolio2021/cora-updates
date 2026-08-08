<?php
require_once __DIR__ . '/../../../../wp-load.php';

global $wpdb;
$menus = wp_get_nav_menus();
foreach ($menus as $m) {
    echo "WP Menu: ID=" . $m->term_id . ", Name=" . $m->name . ", Slug=" . $m->slug . "\n";
    $items = wp_get_nav_menu_items($m->term_id);
    echo "  Items (" . count($items) . "):\n";
    foreach ($items as $item) {
        echo "    - " . $item->title . " (ID=" . $item->ID . ", URL=" . $item->url . ")\n";
    }
}
