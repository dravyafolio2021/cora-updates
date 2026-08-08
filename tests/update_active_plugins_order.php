<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('mysqli.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock');

$link = mysqli_connect('localhost', 'root', 'root', 'local');
if (!$link) {
    die('Connect Error: ' . mysqli_connect_error());
}

$plugins = array(
    'cora-real-estate/cora-real-estate.php',
    'cora-workspace/cora-workspace.php',
    'elementor/elementor.php',
    'elementor-pro/elementor-pro.php'
);
$serialized = serialize($plugins);

$stmt = mysqli_prepare($link, "UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
mysqli_stmt_bind_param($stmt, 's', $serialized);
$success = mysqli_stmt_execute($stmt);

if ($success) {
    echo "SUCCESS: Reordered active plugins alphabetically to prevent loading conflicts.\n";
    print_r($plugins);
} else {
    echo "Error updating options: " . mysqli_error($link);
}

mysqli_close($link);
