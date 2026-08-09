<?php
require_once '/Users/shrutian/Desktop/cora/app/public/wp-load.php';
$plugins = array(
    'cora-workspace/cora-workspace.php',
    'cora-real-estate/cora-real-estate.php',
    'elementor/elementor.php',
    'elementor-pro/elementor-pro.php'
);
update_option('active_plugins', $plugins);
echo "ACTIVE PLUGINS SET DIRECTLY:\n";
print_r(get_option('active_plugins'));
