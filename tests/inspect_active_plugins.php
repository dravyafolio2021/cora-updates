<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('mysqli.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock');

$link = mysqli_connect('localhost', 'root', 'root', 'local');
if (!$link) {
    die('Connect Error: ' . mysqli_connect_error());
}

$result = mysqli_query($link, "SELECT option_value FROM wp_options WHERE option_name = 'active_plugins'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $plugins = unserialize($row['option_value']);
    print_r($plugins);
} else {
    echo "Error: " . mysqli_error($link);
}
mysqli_close($link);
