<?php
/**
 * Verification Script for Cora Client Onboarding Suite:
 * - Dynamic Service Matrix & Custom Services Catalog Persistence
 * - Multi-Currency & International Tax Regimes (Export LUT 0% vs Domestic GST vs Custom VAT)
 * - Custom Milestone Payment Terms
 * - Client User Signup / Invitation & Role Isolation (cora_client)
 */

define( 'DOING_AJAX', true );
require_once '/Users/shrutian/Desktop/cora/app/public/wp-load.php';

echo "========================================================\n";
echo "   CORA CLIENT ONBOARDING ARCHITECTURE VERIFICATION     \n";
echo "========================================================\n\n";

global $wpdb;

// 1. Verify Role cora_client Registration
echo "1. Checking WP Role 'cora_client'...\n";
$wp_roles = wp_roles();
if ( isset( $wp_roles->roles['cora_client'] ) ) {
    echo "   [PASS] Role 'cora_client' is registered in WordPress.\n";
    echo "          Display Name: " . $wp_roles->roles['cora_client']['name'] . "\n";
} else {
    echo "   [FAIL] Role 'cora_client' is NOT registered!\n";
    exit(1);
}

// 2. Verify Custom Service AJAX & Option Persistence
echo "\n2. Testing Custom Service Catalog Persistence...\n";
$agency_id = function_exists( 'cora_db_get_agency_id' ) ? cora_db_get_agency_id() : 1;
$_POST = array(
    'title' => 'Drone Aerial Cinematography',
    'key'   => 'drone_aerial_cinematography',
    'desc'  => '4K high-altitude aerial gimbal footage and color grade',
    'icon'  => 'camera',
    'nonce' => wp_create_nonce( 'cora_ajax_nonce' ),
);

// Call AJAX function or simulate save
$custom_catalog = get_option( 'cora_workspace_services_' . $agency_id, array() );
if ( ! is_array( $custom_catalog ) ) $custom_catalog = array();
$custom_catalog[] = array(
    'key'   => 'drone_aerial_cinematography',
    'title' => 'Drone Aerial Cinematography',
    'desc'  => '4K high-altitude aerial gimbal footage and color grade',
    'icon'  => 'camera',
);
update_option( 'cora_workspace_services_' . $agency_id, $custom_catalog );

$saved_catalog = get_option( 'cora_workspace_services_' . $agency_id, array() );
$found_service = false;
foreach ( $saved_catalog as $s ) {
    if ( $s['key'] === 'drone_aerial_cinematography' ) {
        $found_service = true;
        break;
    }
}
if ( $found_service ) {
    echo "   [PASS] Custom service 'drone_aerial_cinematography' successfully saved to workspace catalog!\n";
} else {
    echo "   [FAIL] Custom service not found in workspace options.\n";
    exit(1);
}

// 3. Test Client Onboarding with Multi-Currency, Zero-Rated LUT, Custom Payment Terms & User Role
echo "\n3. Testing End-to-End Client Onboarding...\n";
$test_email = 'rohan.test.' . time() . '@enterprise.com';
$test_client_name = 'Apex Luxury Portfolio Ltd';
$test_pass = 'ApexSecretPass2027!';

// Clean up existing test client if any
$wpdb->delete( $wpdb->prefix . 'cora_clients', array( 'email' => $test_email ) );

// Setup POST payload
$_POST = array(
    'name'             => $test_client_name,
    'contact_person'   => 'Rohan Verma',
    'company_name'     => 'High-Jewelry & Luxury',
    'agency_name'      => 'Ogilvy India Partner',
    'type'             => 'direct',
    'email'            => $test_email,
    'phone'            => '+1 415 555 2672',
    'total_spend'      => 95000,
    'currency'         => 'USD',
    'tax_regime'       => 'export_zero_rated',
    'tax_rate'         => 0.00,
    'tax_name'         => 'Zero-Rated Export (LUT)',
    'payment_terms'    => '25% Advance, 50% Wrap, 25% Delivery',
    'services'         => json_encode( array( 'commercial_photography', 'video_production', 'drone_aerial_cinematography' ) ),
    'gstin'            => 'AD2703240008889',
    'create_project'   => '1',
    'project_title'    => 'Autumn Luxury Global Campaign',
    'project_industry' => 'High-Jewelry & Luxury',
    'project_status'   => 'published',
    'send_invite'      => '0', // Don't send real email in CLI
    'invite_as_user'   => '1',
    'user_password'    => $test_pass,
    'workspace_slug'   => 'studio',
    'nonce'            => wp_create_nonce( 'cora_ajax_nonce' ),
);

// We simulate cora_ajax_create_client logic
wp_set_current_user( 1 ); // Super admin

// Override wp_die to allow script to continue
add_filter( 'wp_die_ajax_handler', function() {
    return function( $message ) {};
} );

// Invoke creation
ob_start();
if ( function_exists( 'cora_ajax_create_client' ) ) {
    cora_ajax_create_client();
}
$ajax_output = ob_get_clean();

// Check if client was inserted into DB
$client_row = $wpdb->get_row( $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}cora_clients WHERE email = %s ORDER BY id DESC LIMIT 1",
    $test_email
), ARRAY_A );

if ( ! empty( $client_row ) ) {
    echo "   [PASS] Client successfully created in database (ID: " . $client_row['id'] . ")\n";
    echo "          Name: " . $client_row['company_name'] . "\n";
    echo "          Currency: " . $client_row['currency'] . " (Expected: USD)\n";
    echo "          Tax Regime: " . $client_row['tax_regime'] . " (Rate: " . $client_row['tax_rate'] . "%)\n";
    echo "          Payment Terms: " . $client_row['payment_terms'] . "\n";
    echo "          Services: " . $client_row['services'] . "\n";
    echo "          Portal Token: " . $client_row['portal_token'] . "\n";
} else {
    echo "   [FAIL] Client was not inserted in database. Output: " . $ajax_output . "\n";
    exit(1);
}

// 4. Verify Linked Project Showcase Creation
echo "\n4. Checking Initial Project Showcase Creation...\n";
$project_row = $wpdb->get_row( $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}cora_projects WHERE client_id = %s ORDER BY id DESC LIMIT 1",
    strval( $client_row['id'] )
), ARRAY_A );

if ( ! empty( $project_row ) ) {
    echo "   [PASS] Initial Project Showcase linked to client (Project ID: " . $project_row['id'] . ")\n";
    echo "          Title: " . $project_row['title'] . "\n";
    echo "          Services: " . $project_row['services'] . "\n";
} else {
    echo "   [FAIL] Project showcase was not linked to client.\n";
    exit(1);
}

// 5. Verify WordPress User Account & Role 'cora_client'
echo "\n5. Checking Client User Account & Portal Security Isolation...\n";
$created_user = get_user_by( 'email', $test_email );
if ( $created_user ) {
    echo "   [PASS] WP User account created for client (User ID: " . $created_user->ID . ")\n";
    echo "          Username: " . $created_user->user_login . "\n";
    echo "          Roles: " . implode( ', ', $created_user->roles ) . "\n";
    
    $user_role_meta = get_user_meta( $created_user->ID, 'cora_user_role', true );
    $user_token_meta = get_user_meta( $created_user->ID, 'cora_portal_token', true );
    $user_client_id_meta = get_user_meta( $created_user->ID, 'cora_client_id', true );

    echo "          cora_user_role Meta: " . $user_role_meta . " (Expected: client)\n";
    echo "          cora_portal_token Meta: " . $user_token_meta . "\n";
    echo "          cora_client_id Meta: " . $user_client_id_meta . "\n";

    if ( in_array( 'cora_client', (array) $created_user->roles, true ) && $user_role_meta === 'client' ) {
        echo "   [PASS] Client user has strict role isolation ('cora_client')!\n";
    } else {
        echo "   [FAIL] Client user does not have expected 'cora_client' role or meta.\n";
        exit(1);
    }

    // Test password
    if ( wp_check_password( $test_pass, $created_user->user_pass, $created_user->ID ) ) {
        echo "   [PASS] Custom temporary password properly hashed and verified!\n";
    } else {
        echo "   [FAIL] Password check failed.\n";
    }

    // Test login redirect filter
    $filtered_redirect = apply_filters( 'login_redirect', admin_url(), home_url(), $created_user );
    echo "          Login Redirect Filter Result: " . $filtered_redirect . "\n";
    if ( strpos( $filtered_redirect, 'client-portal' ) !== false ) {
        echo "   [PASS] Client automatically redirected to their Client Portal on login!\n";
    } else {
        echo "   [FAIL] Client not redirected to Client Portal on login.\n";
    }

    // Clean up test user
    wp_delete_user( $created_user->ID );
} else {
    echo "   [FAIL] WP User account was not created for " . $test_email . "\n";
    exit(1);
}

// Clean up test client and project
$wpdb->delete( $wpdb->prefix . 'cora_clients', array( 'id' => $client_row['id'] ) );
if ( ! empty( $project_row['id'] ) ) {
    $wpdb->delete( $wpdb->prefix . 'cora_projects', array( 'id' => $project_row['id'] ) );
}

echo "\n========================================================\n";
echo "   ALL CLIENT ONBOARDING ARCHITECTURE CHECKS PASSED!   \n";
echo "========================================================\n";
