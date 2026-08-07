<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

header('Content-Type: text/plain');

echo "--- LISTING ALL MENUS ---\n";

$menus = wp_get_nav_menus();
if (empty($menus)) {
    echo "No menus found.\n";
} else {
    foreach ($menus as $menu) {
        echo "Menu Name: {$menu->name} (ID: {$menu->term_id}, Slug: {$menu->slug})\n";
    }
}
