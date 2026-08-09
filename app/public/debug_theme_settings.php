<?php
require_once __DIR__ . '/wp-load.php';
header('Content-Type: application/json');

$target_url = 'https://7c56589c-8718-4572-8046-5588fb176b92.lovableproject.com/contact';
$response = wp_remote_get($target_url, array('timeout' => 15));

if (is_wp_error($response)) {
    echo json_encode(array(
        'error' => $response->get_error_message()
    ), JSON_PRETTY_PRINT);
} else {
    echo json_encode(array(
        'code' => wp_remote_retrieve_response_code($response),
        'headers' => wp_remote_retrieve_headers($response)->getAll(),
        'body_preview' => substr(wp_remote_retrieve_body($response), 0, 500)
    ), JSON_PRETTY_PRINT);
}
