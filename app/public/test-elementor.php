<?php
require_once __DIR__ . '/wp-load.php';
echo "Elementor Active: " . (class_exists('Elementor\Plugin') ? 'YES' : 'NO') . "\n";
echo "Active Plugins:\n";
print_r(get_option('active_plugins'));
