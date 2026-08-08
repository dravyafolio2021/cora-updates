<?php
require_once __DIR__ . '/../app/public/wp-load.php';

global $wpdb;

echo "=== cora_git_sync_enabled ===\n";
var_dump( get_option( 'cora_git_sync_enabled' ) );

echo "\n=== Canvas Theme 195 ===\n";
$theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = 195", ARRAY_A );
print_r( $theme );

echo "\n=== All Canvas Themes ===\n";
$all_themes = $wpdb->get_results( "SELECT id, name, slug, status, type, settings FROM {$wpdb->prefix}cora_canvas_themes", ARRAY_A );
print_r( $all_themes );

echo "\n=== Upload Dir ===\n";
$upload = wp_get_upload_dir();
print_r( $upload );

$sync_dirs = glob( $upload['basedir'] . '/cora-git-sync*' );
echo "Found sync dirs: \n";
print_r( $sync_dirs );

if ( ! empty( $sync_dirs ) ) {
    foreach ( $sync_dirs as $d ) {
        echo "Dir $d contents:\n";
        print_r( glob( $d . '/*' ) );
    }
}

echo "\n=== Theme 195 Pages ===\n";
$pages = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = 195", ARRAY_A );
print_r( $pages );
