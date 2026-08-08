<?php
require_once __DIR__ . '/../../../../wp-load.php';

global $wpdb;
$menu_id = 400;

$preview_theme_id = cora_get_preview_theme_id();
echo "cora_get_preview_theme_id() = " . var_export($preview_theme_id, true) . "\n";

if ( $preview_theme_id > 0 ) {
    $theme_id = $preview_theme_id;
} else {
    $live_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
    $theme_id = $live_theme ? intval( $live_theme['id'] ) : 0;
}
echo "Resolved Theme ID = " . var_export($theme_id, true) . "\n";

if ( ! $theme_id ) {
    echo "No theme ID, exiting\n";
    exit;
}

$theme = $wpdb->get_row( $wpdb->prepare( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d LIMIT 1", $theme_id ), ARRAY_A );
if ( ! $theme ) {
    echo "Theme not found in DB, exiting\n";
    exit;
}

$settings = json_decode( $theme['settings'], true ) ?: array();
if ( empty( $settings['menus'] ) || ! is_array( $settings['menus'] ) ) {
    echo "empty(settings['menus']) is true or not array, exiting\n";
    exit;
}

$menu_term_id = $menu_id;
$menu_slug = '';

$matched_menu = null;
foreach ( $settings['menus'] as $m ) {
    if ( $menu_term_id > 0 && isset( $m['wp_term_id'] ) && intval( $m['wp_term_id'] ) === $menu_term_id ) {
        $matched_menu = $m;
        break;
    }
}

if ( ! $matched_menu ) {
    echo "No matched menu in settings for ID=$menu_term_id\n";
} else {
    echo "Matched Menu:\n";
    print_r($matched_menu);
}
