<?php
/**
 * WhatsApp Cloud API Automation & Verification Script
 *
 * Verifies gateway connection, sends a test template message, and simulates automated lead alert.
 * Usage: php scripts/test_whatsapp_automation.php [optional_recipient_number]
 */

require_once __DIR__ . '/../app/public/wp-load.php';

if ( ! class_exists( 'Cora_WhatsApp_Gateway' ) ) {
    echo "❌ Error: Cora_WhatsApp_Gateway class not found.\n";
    exit( 1 );
}

$gateway = Cora_WhatsApp_Gateway::instance();
$settings = $gateway->get_settings();

echo "=====================================================\n";
echo "🟢 Cora WhatsApp Cloud API Gateway Automation Diagnostic\n";
echo "=====================================================\n";
echo "• Phone Number ID : " . ( $settings['phone_number_id'] ?: 'NOT SET' ) . "\n";
echo "• Access Token    : " . ( $settings['access_token'] ? substr( $settings['access_token'], 0, 15 ) . '...' : 'NOT SET' ) . "\n";
echo "• Admin Phone     : " . ( $settings['admin_phone'] ?: 'NOT SET' ) . "\n";
echo "• Default Template: " . ( $settings['default_template'] ?: 'jaspers_market_order_confirmation_v1' ) . "\n";
echo "• Webhook URL     : " . $settings['webhook_url'] . "\n";
echo "• Configured      : " . ( $settings['is_configured'] ? 'YES' : 'NO' ) . "\n";
echo "=====================================================\n\n";

if ( ! $settings['is_configured'] ) {
    echo "❌ Error: WhatsApp gateway credentials not configured.\n";
    exit( 1 );
}

// 1. Verify Connection
echo "Step 1: Verifying Meta Cloud API Connection...\n";
$conn = $gateway->verify_connection();
if ( $conn['success'] ) {
    $d = $conn['data'];
    echo "✅ Connection Verified!\n";
    echo "   - Verified Name : {$d['verified_name']}\n";
    echo "   - Display Number: {$d['display_phone_number']}\n";
    echo "   - Quality Rating: {$d['quality_rating']}\n\n";
} else {
    echo "❌ Connection Failed: " . ( $conn['error'] ?? 'Unknown error' ) . "\n\n";
    exit( 1 );
}

// 2. Determine target phone
$target_phone = ! empty( $argv[1] ) ? $argv[1] : ( $settings['admin_phone'] ?: '919817059266' );

echo "Step 2: Sending Order / Booking Confirmation Template to {$target_phone}...\n";
$tpl_res = $gateway->send_order_confirmation(
    $target_phone,
    'Rohan Verma',
    'ORD-' . wp_rand( 10000, 99999 ),
    current_time( 'M j, Y' ),
    $settings['default_template'] ?: 'jaspers_market_order_confirmation_v1'
);

if ( $tpl_res['success'] ) {
    echo "✅ Template message accepted by Meta! Message ID: {$tpl_res['message_id']}\n\n";
} else {
    echo "❌ Failed to send template message: " . ( $tpl_res['error'] ?? 'Unknown error' ) . "\n\n";
}

// 3. Automated Lead Alert Simulation
echo "Step 3: Simulating Automated Lead Notification Dispatch...\n";
$lead_res = $gateway->send_lead_alert(
    $target_phone,
    array(
        'id'    => 'LEAD-' . wp_rand( 1000, 9999 ),
        'title' => 'Studio Commercial Photography Inquiry',
        'name'  => 'Aarav Mehta',
        'phone' => '+91 98170 59266',
        'email' => 'client@example.com',
        'date'  => current_time( 'M j, Y, g:i a' )
    ),
    true // admin alert
);

if ( $lead_res['success'] ) {
    echo "✅ Automated Lead Alert dispatched successfully! Message ID: {$lead_res['message_id']}\n\n";
} else {
    echo "⚠️ Lead alert response: " . ( $lead_res['error'] ?? 'Check window or template' ) . "\n\n";
}

echo "=====================================================\n";
echo "🎉 WhatsApp Automated Messaging Test Complete!\n";
echo "=====================================================\n";
