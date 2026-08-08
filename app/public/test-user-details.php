<?php
require_once 'wp-load.php';

$email = 'admin@cora.local';
$user = get_user_by('email', $email);

if (!$user) {
    echo "USER NOT FOUND!\n";
    exit;
}

echo "=== USER INFO ===\n";
echo "ID: " . $user->ID . "\n";
echo "Login: " . $user->user_login . "\n";
echo "Email: " . $user->user_email . "\n";
echo "Roles: " . implode(', ', $user->roles) . "\n";

echo "\n=== USER META ===\n";
echo "cora_user_status: " . get_user_meta($user->ID, 'cora_user_status', true) . "\n";
echo "cora_agency_id: " . get_user_meta($user->ID, 'cora_agency_id', true) . "\n";
echo "cora_email_verified: " . get_user_meta($user->ID, 'cora_email_verified', true) . "\n";

$pass_match = wp_check_password('cora_secure_pass_123', $user->data->user_pass, $user->ID);
echo "\nPassword 'cora_secure_pass_123' matches: " . ($pass_match ? 'yes' : 'no') . "\n";
