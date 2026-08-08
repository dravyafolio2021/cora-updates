<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '/Users/shrutian/Desktop/cora/app/public/wp-load.php';
$plugins = array(
    'cora-workspace/cora-workspace.php',
    'cora-real-estate/cora-real-estate.php',
    'elementor/elementor.php',
    'elementor-pro/elementor-pro.php'
);
activate_plugins($plugins);
echo "ACTIVE PLUGINS:\n";
print_r(get_option('active_plugins'));
