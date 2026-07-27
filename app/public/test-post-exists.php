<?php
require_once __DIR__ . '/wp-load.php';
$post = get_post(1959);
if ($post) {
    echo "Post 1959 exists! Type: " . $post->post_type . ", Status: " . $post->post_status . ", Title: " . $post->post_title . "\n";
} else {
    echo "Post 1959 does NOT exist!\n";
}
