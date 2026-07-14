<?php
require_once 'wp-load.php';
$url = 'https://maps.app.goo.gl/bmDCj8GbxBxPZ1Na6';
$response = wp_remote_head($url, array('redirection' => 5));
if (is_wp_error($response)) {
    echo "Error";
} else {
    // wp_remote_head follows redirects? 
    // Actually we can just do wp_remote_get with redirection = 5 and get the final URL?
    // Wait, let's just use curl in PHP
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    $res = curl_exec($ch);
    $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    echo $final_url;
}
