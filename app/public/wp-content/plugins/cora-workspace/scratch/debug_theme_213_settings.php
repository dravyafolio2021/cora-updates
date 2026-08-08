<?php
require_once __DIR__ . '/../../../../wp-load.php';

global $wpdb;
$settings = $wpdb->get_var("SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE id = 213");
$decoded = json_decode($settings, true);
echo "KEYS:\n";
print_r(array_keys($decoded));
if (isset($decoded['templates'])) {
    echo "TEMPLATES:\n";
    print_r($decoded['templates']);
} else {
    echo "TEMPLATES NOT SET in theme 213 settings!\n";
}
