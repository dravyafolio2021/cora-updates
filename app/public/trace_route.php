<?php
define('WP_USE_THEMES', false);
require_once('/Users/shrutian/Desktop/cora/app/public/wp-load.php');

header('Content-Type: text/plain');

global $wpdb;
$t_pages = $wpdb->prefix . 'cora_docs_pages';

$request_uri = '/docs/media-library';
$home_path = '';
$path = substr( $request_uri, strlen( $home_path ) );
$path = trim( parse_url( $path, PHP_URL_PATH ), '/' );

$path_parts = explode( '/', $path );
$sub_slug = isset( $path_parts[1] ) ? sanitize_title( $path_parts[1] ) : '';

echo "PATH: " . $path . "\n";
echo "SUB_SLUG: " . $sub_slug . "\n";

$pages = $wpdb->get_results(
    "SELECT * FROM {$t_pages} ORDER BY category, title",
    ARRAY_A
);
echo "PAGES COUNT: " . count($pages) . "\n";

$active_page = null;
if ( ! empty( $sub_slug ) ) {
    foreach ( $pages as $p ) {
        if ( $p['slug'] === $sub_slug ) {
            $active_page = $p;
            break;
        }
    }
}

echo "ACTIVE PAGE TITLE: " . ($active_page ? $active_page['title'] : 'None') . "\n";
echo "ACTIVE PAGE SLUG: " . ($active_page ? $active_page['slug'] : 'None') . "\n";
