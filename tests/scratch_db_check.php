<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define( 'WP_ADMIN', true );
ini_set( 'mysqli.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock' );

try {
    require_once dirname( __FILE__ ) . '/../app/public/wp-load.php';
    echo "WordPress loaded successfully!\n";
} catch (Throwable $e) {
    echo "Caught Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
