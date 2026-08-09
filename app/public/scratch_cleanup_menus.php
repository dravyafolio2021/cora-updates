<?php
require_once __DIR__ . '/wp-load.php';

header('Content-Type: text/plain');

global $wpdb;

echo "--- FULL CANVAS THEMES MENU DUMP ---\n";
$themes = $wpdb->get_results("SELECT id, name, status, settings FROM {$wpdb->prefix}cora_canvas_themes ORDER BY id DESC", ARRAY_A);

foreach ($themes as $theme) {
    echo "\n=== Theme: {$theme['name']} (ID: {$theme['id']}, Status: {$theme['status']}) ===\n";
    
    if (empty($theme['settings'])) {
        echo "  (no settings)\n";
        continue;
    }

    $settings = json_decode($theme['settings'], true);
    
    // Check top-level menus key
    $menus = $settings['menus'] ?? [];
    echo "  settings.menus count: " . count($menus) . "\n";
    foreach ($menus as $m) {
        echo "  - [menus] ID: " . ($m['id'] ?? '?') . " | Name: " . ($m['name'] ?? '?') . " | Items: " . count($m['items'] ?? []) . "\n";
    }
    
    // Check all top-level keys
    echo "  Top-level keys: " . implode(', ', array_keys($settings)) . "\n";
}
