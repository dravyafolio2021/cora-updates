<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
require_once dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/wp-load.php';

$active_plugins = get_option( 'active_plugins' );
echo "=== ACTIVE PLUGINS ===\n";
print_r( $active_plugins );
