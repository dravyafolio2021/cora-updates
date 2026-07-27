<?php
require_once __DIR__ . '/wp-load.php';
$user = get_userdata(1);
echo "User 1 (cora) can manage_options: " . ($user->has_cap('manage_options') ? 'YES' : 'NO') . "\n";
echo "User 1 roles: " . implode(', ', $user->roles) . "\n";
