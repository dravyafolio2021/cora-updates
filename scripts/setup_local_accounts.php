<?php
// Script to provision local testing accounts for Cora Workspace
require_once '/Users/shrutian/Desktop/cora/app/public/wp-load.php';

global $wpdb;

echo "=== Provisioning Local Test Accounts ===\n";

// 1. Ensure Agencies exist in wp_cora_agencies
$agencies = array(
    array( 'id' => 1, 'name' => 'Cora Real Estate Agency', 'slug' => 'real-estate', 'status' => 'active' ),
    array( 'id' => 2, 'name' => 'Cora Photography Studio', 'slug' => 'studio', 'status' => 'active' ),
);

foreach ($agencies as $ag) {
    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM wp_cora_agencies WHERE id = %d", $ag['id']));
    if (!$exists) {
        $wpdb->insert('wp_cora_agencies', array(
            'id' => $ag['id'],
            'name' => $ag['name'],
            'slug' => $ag['slug'],
            'owner_user_id' => 1,
            'plan' => 'enterprise',
            'status' => 'active',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ));
        echo "Created agency: {$ag['name']} (ID: {$ag['id']})\n";
    } else {
        $wpdb->update('wp_cora_agencies', array(
            'name' => $ag['name'],
            'slug' => $ag['slug'],
            'status' => 'active'
        ), array('id' => $ag['id']));
        echo "Updated agency: {$ag['name']} (ID: {$ag['id']})\n";
    }
}

// 2. Define Accounts to Provision
$accounts = array(
    array(
        'username' => 're_owner',
        'email'    => 'owner.realestate@cora.local',
        'password' => 'cora_secure_pass_123',
        'display'  => 'Real Estate Workspace Owner',
        'role'     => 'cora_super_admin',
        'agency_id'=> 1,
        'industry' => 'real_estate'
    ),
    array(
        'username' => 'studio_owner',
        'email'    => 'owner.studio@cora.local',
        'password' => 'cora_secure_pass_123',
        'display'  => 'Photography Studio Workspace Owner',
        'role'     => 'cora_super_admin',
        'agency_id'=> 2,
        'industry' => 'photography_studio'
    ),
    array(
        'username' => 'cora_admin',
        'email'    => 'admin@cora.local',
        'password' => 'cora_secure_pass_123',
        'display'  => 'Platform Super Admin (Shruti)',
        'role'     => 'administrator',
        'agency_id'=> 1,
        'industry' => 'real_estate'
    )
);

foreach ($accounts as $acc) {
    $user = get_user_by('login', $acc['username']);
    if (!$user) {
        $user = get_user_by('email', $acc['email']);
    }
    
    if (!$user) {
        $user_id = wp_create_user($acc['username'], $acc['password'], $acc['email']);
        if (is_wp_error($user_id)) {
            echo "ERROR creating {$acc['username']}: " . $user_id->get_error_message() . "\n";
            continue;
        }
        $user = get_user_by('id', $user_id);
        echo "Created WP User: {$acc['username']} (ID: {$user_id})\n";
    } else {
        $user_id = $user->ID;
        if ( ! wp_check_password( $acc['password'], $user->data->user_pass, $user_id ) ) {
            wp_set_password($acc['password'], $user_id);
            echo "Reset password for WP User: {$acc['username']} (ID: {$user_id})\n";
        } else {
            echo "Password already correct for WP User: {$acc['username']} (ID: {$user_id})\n";
        }
    }
    
    // Set display name and WP role
    wp_update_user(array(
        'ID'           => $user_id,
        'display_name' => $acc['display'],
        'role'         => $acc['role']
    ));
    
    // Provision / update entry in wp_cora_users
    $cora_user_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM wp_cora_users WHERE wp_user_id = %d", $user_id));
    if (!$cora_user_exists) {
        $wpdb->insert('wp_cora_users', array(
            'wp_user_id'  => $user_id,
            'agency_id'   => $acc['agency_id'],
            'branch_id'   => 1,
            'role'        => 'agency_owner',
            'status'      => 'active',
            'invited_by'  => 1,
            'last_active' => current_time('mysql'),
            'created_at'  => current_time('mysql'),
            'updated_at'  => current_time('mysql')
        ));
    } else {
        $wpdb->update('wp_cora_users', array(
            'agency_id'  => $acc['agency_id'],
            'role'       => 'agency_owner',
            'status'     => 'active',
            'updated_at' => current_time('mysql')
        ), array('wp_user_id' => $user_id));
    }
    
    // Set user industry meta preference & auto-verify email for local testing
    $agency_slug = ( $acc['agency_id'] == 1 ) ? 'real-estate' : 'studio';
    update_user_meta($user_id, 'cora_agency_id', $agency_slug);
    update_user_meta($user_id, 'cora_user_agency_id', $acc['agency_id']);
    update_user_meta($user_id, 'cora_preferred_industry', $acc['industry']);
    update_user_meta($user_id, 'cora_email_verified', 1);
    update_user_meta($user_id, 'cora_user_status', 'active');
    update_user_meta($user_id, 'cora_onboarding_completed', '1');
}

echo "✅ Local accounts provisioning complete!\n";
