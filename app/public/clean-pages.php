<?php
define('WP_USE_THEMES', false);
require('wp-load.php');

$pages = get_posts(array(
    'post_type' => 'page',
    'numberposts' => -1,
    'post_status' => 'any'
));

$count = 0;
foreach ($pages as $page) {
    if (strpos($page->post_title, 'AAAAA') !== false || strpos($page->post_title, 'Cancel Del') !== false) {
        wp_delete_post($page->ID, true);
        $count++;
    }
}
echo "Deleted $count dummy pages.\n";
