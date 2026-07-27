<?php
require_once __DIR__ . '/wp-load.php';
$users = get_users();
foreach($users as $u) {
    echo "User: " . $u->display_name . " (ID: " . $u->ID . ")\n";
    echo "Roles: " . implode(', ', $u->roles) . "\n\n";
}
