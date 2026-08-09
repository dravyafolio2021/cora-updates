<?php
require_once __DIR__ . '/../../../../wp-load.php';

global $wpdb;
$themes = $wpdb->get_results("SELECT id, name, status, settings FROM {$wpdb->prefix}cora_canvas_themes", ARRAY_A);
foreach ($themes as $t) {
    $decoded = json_decode($t['settings'], true);
    if (isset($decoded['templates'])) {
        echo "Theme ID=" . $t['id'] . ", Name=" . $t['name'] . ", Status=" . $t['status'] . "\n";
        print_r($decoded['templates']);
    }
}
