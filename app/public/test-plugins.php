<?php
require_once __DIR__ . '/wp-load.php';
$active_plugins = get_option('active_plugins');
print_r($active_plugins);
