<?php
define('WP_USE_THEMES', false);
require_once dirname(__DIR__) . '/app/public/wp-load.php';

$query = 'Password';
$filter = 'all';

$settings_items = array(
    array(
        'title' => 'General Settings',
        'category' => 'Settings',
        'description' => 'Workspace details, identity, log retention, and tours configurations.',
        'url' => home_url( '/workspace/settings-suite?settings_tab=general' ),
        'icon' => 'settings',
        'tags' => array( 'site title', 'tagline', 'identity', 'general', 'timezone' )
    ),
    array(
        'title' => 'Language Settings',
        'category' => 'Settings',
        'description' => 'Configure platform display language (English, Hindi, Bengali, Telugu, Marathi, Tamil, etc.).',
        'url' => home_url( '/workspace/settings-suite?settings_tab=general' ),
        'icon' => 'globe',
        'tags' => array( 'language', 'translate', 'locale', 'hindi', 'english' )
    ),
    array(
        'title' => 'Password Policy',
        'category' => 'Settings',
        'description' => 'Configure and enforce minimum length, digits, and uppercase symbols.',
        'url' => home_url( '/workspace/settings-suite?settings_tab=pwd-policy' ),
        'icon' => 'lock',
        'tags' => array( 'password', 'security', 'digits', 'uppercase', 'policy' )
    )
);

foreach ($settings_items as $item) {
    $score = cora_search_similarity_score($query, $item['title'], $item['description'], $item['tags']);
    echo "Item: " . $item['title'] . " | Score: " . $score . "\n";
}
