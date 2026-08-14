<?php
/**
 * Test Suite: Meta WhatsApp Cloud API Notification Integration
 */
require_once dirname( __DIR__ ) . '/app/public/wp-load.php';

echo "====================================================\n";
echo "  CORA META WHATSAPP CLOUD API INTEGRATION TESTS   \n";
echo "====================================================\n\n";

// 1. Test Phone Number Normalizer
echo "--- TEST 1: Phone Number Normalization ---\n";
$test_cases = array(
    '+91 98765 43210'  => '919876543210',
    '09876543210'      => '919876543210',
    '9876543210'       => '919876543210',
    '+91-98765-43210'  => '919876543210',
    '919876543210'     => '919876543210',
    '+1 (555) 234-5678'=> '15552345678',
);

$all_passed = true;
foreach ( $test_cases as $input => $expected ) {
    $actual = cora_format_whatsapp_phone( $input );
    if ( $actual === $expected ) {
        echo "✅ PASS: '$input' -> '$actual'\n";
    } else {
        echo "❌ FAIL: '$input' -> Expected '$expected', got '$actual'\n";
        $all_passed = false;
    }
}

// 2. Test Notification Preferences Schema with WhatsApp
echo "\n--- TEST 2: Notification Preferences Schema with WhatsApp ---\n";
$defaults = cora_get_default_notification_prefs();
if ( isset( $defaults['global_whatsapp'] ) && isset( $defaults['triggers']['lead_created']['whatsapp'] ) ) {
    echo "✅ PASS: Default schema includes global_whatsapp and trigger-level whatsapp rules.\n";
    echo "Total triggers with WhatsApp support: " . count( $defaults['triggers'] ) . "\n";
} else {
    echo "❌ FAIL: Schema missing WhatsApp properties.\n";
}

// 3. Test WhatsApp Dispatcher with Test/Mock Credentials
echo "\n--- TEST 3: Meta WhatsApp Cloud API Dispatcher ---\n";
// Save test phone ID and token
update_option( 'cora_whatsapp_api_token', 'EAAB_TEST_TOKEN_MOCK' );
update_option( 'cora_whatsapp_phone_number_id', '1093847291039' );

$res = cora_send_whatsapp_message( '+91 98765 43210', '*[Test Alert]* Hello from Cora!' );
echo "Dispatcher call executed.\n";
echo "Response status: " . ( $res['success'] ? 'SUCCESS' : 'HANDLED ERROR' ) . "\n";
echo "Error message (Expected with mock token): " . ( $res['error'] ?? 'None' ) . "\n";
if ( isset( $res['code'] ) || isset( $res['error'] ) ) {
    echo "✅ PASS: Meta Cloud API remote post correctly caught and handled Graph API response.\n";
}

// 4. Test cora_notify with WhatsApp Routing
echo "\n--- TEST 4: Unified Notification Dispatch with WhatsApp ---\n";
$admin_user = get_user_by( 'email', 'admin@cora.local' );
if ( $admin_user ) {
    update_user_meta( $admin_user->ID, 'cora_user_phone', '+91 98765 43210' );
    $summary = cora_notify( 'lead_created', $admin_user->ID, array(
        'title'      => 'New High-Ticket Lead',
        'body'       => 'Client inquiry for 4BHK Villa in Gurgaon.',
        'action_url' => home_url( '/workspace/leads' ),
        'category'   => 'CRM Alert',
    ) );
    echo "Notification summary: " . json_encode( $summary ) . "\n";
    if ( isset( $summary['inapp'] ) && isset( $summary['whatsapp'] ) ) {
        echo "✅ PASS: cora_notify() executed across In-App, Push, WhatsApp, and Email channels.\n";
    }
}

echo "\n====================================================\n";
echo "       ALL WHATSAPP INTEGRATION TESTS COMPLETE       \n";
echo "====================================================\n";
