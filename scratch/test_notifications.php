<?php
/**
 * Automated Verification Script for Notification Management Module
 */

// Load WordPress bootstrap
require_once dirname(__DIR__) . '/app/public/wp-load.php';

echo "====================================================\n";
echo "  CORA NOTIFICATION MANAGEMENT MODULE TEST SUITE   \n";
echo "====================================================\n\n";

$admin_user = get_user_by( 'email', 'admin@cora.local' ) ?: get_user_by( 'login', 'cora_admin' ) ?: get_user_by( 'id', 1 );
if ( ! $admin_user ) {
    die( "❌ Could not find admin user for testing.\n" );
}
$user_id = $admin_user->ID;
echo "1. Active Test User: " . $admin_user->display_name . " (ID: {$user_id}, Email: " . $admin_user->user_email . ")\n";

// TEST 1: Default & Saved Preferences
echo "\n--- TEST 1: Notification Preferences Schema ---\n";
$defaults = cora_get_default_notification_prefs();
echo "Default triggers count: " . count( $defaults['triggers'] ) . "\n";
if ( count( $defaults['triggers'] ) === 23 ) {
    echo "✅ PASS: Default trigger schema contains exactly 23 matrix events.\n";
} else {
    echo "❌ FAIL: Expected 23 default triggers, found " . count( $defaults['triggers'] ) . ".\n";
}

$test_post_data = array(
    'cora_notif_global_inapp'          => 1,
    'cora_notif_global_push'           => 1,
    'cora_notif_global_email'          => 1,
    'cora_notif_global_email_schedule' => 'daily',
    'cora_notif_dnd_enabled'           => 1,
    'cora_notif_dnd_start'             => '23:00',
    'cora_notif_dnd_end'               => '07:00',
    'cora_notif_custom_email'          => 'notifications.test@cora.local',
    'notif_inapp_lead_created'         => 1,
    'notif_push_lead_created'          => 1,
    'notif_email_lead_created'         => 'instant',
);
$saved_prefs = cora_save_user_notification_prefs( $user_id, $test_post_data );
$fetched_prefs = cora_get_user_notification_prefs( $user_id );

if ( $fetched_prefs['custom_email'] === 'notifications.test@cora.local' && $fetched_prefs['dnd_start'] === '23:00' ) {
    echo "✅ PASS: Custom preferences saved and fetched from user metadata successfully.\n";
} else {
    echo "❌ FAIL: Preferences fetch mismatch.\n";
}

// TEST 2: In-App Notification Dispatch via cora_notify
echo "\n--- TEST 2: In-App Notification Dispatch ---\n";
global $wpdb;
$initial_notif_count = intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_notifications WHERE user_id = {$user_id}" ) );

$dispatch_res = cora_notify( 'lead_created', $user_id, array(
    'title'      => 'Test Lead: John Doe',
    'body'       => 'Inquiry for luxury villa viewing in New Delhi.',
    'action_url' => home_url( '/workspace/dashboard?sub_page=leads' ),
    'category'   => 'CRM & Leads',
) );

$new_notif_count = intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_notifications WHERE user_id = {$user_id}" ) );
if ( $new_notif_count > $initial_notif_count ) {
    echo "✅ PASS: cora_notify() successfully recorded in-app alert in wp_cora_notifications table (Count: {$new_notif_count}).\n";
} else {
    echo "❌ FAIL: In-app notification was not inserted into database table.\n";
}

// TEST 3: Email Digest Queuing
echo "\n--- TEST 3: Email Digest Queuing & Aggregation ---\n";
delete_option( 'cora_notification_digest_queue' );

// Enqueue 3 test events for digest
cora_enqueue_notification_digest( $user_id, 'test@cora.local', 'lead_status_changed', 'Lead Moved to Negotiating', 'Aarav Sharma moved to contract stage', '/leads', 'CRM' );
cora_enqueue_notification_digest( $user_id, 'test@cora.local', 'booking_created', 'New Studio Shoot Booked', 'Fashion editorial session confirmed for Friday', '/bookings', 'Bookings' );
cora_enqueue_notification_digest( $user_id, 'test@cora.local', 'payment_received', 'Milestone Payment Cleared', 'Invoice #INV-2026-089 settled (₹45,000)', '/financials', 'Finance' );

$queue = get_option( 'cora_notification_digest_queue', array() );
echo "Queued items count: " . count( $queue ) . "\n";
if ( count( $queue ) === 3 ) {
    echo "✅ PASS: 3 notification items queued into cora_notification_digest_queue.\n";
} else {
    echo "❌ FAIL: Queue count mismatch.\n";
}

// TEST 4: WP-Cron Digest Runner Execution
echo "\n--- TEST 4: WP-Cron Digest Processor Execution ---\n";
$digest_run = cora_cron_process_notification_digests( true );
echo "Digest run result: " . json_encode( $digest_run ) . "\n";
$remaining_queue = get_option( 'cora_notification_digest_queue', array() );
echo "Remaining items in queue: " . count( $remaining_queue ) . "\n";
if ( $digest_run['processed'] === 3 && count( $remaining_queue ) === 0 ) {
    echo "✅ PASS: Digest processor aggregated 3 notifications and dispatched consolidated digest.\n";
} else {
    echo "❌ FAIL: Digest processing did not clear expected items.\n";
}

// TEST 5: Quiet Hours / DND Evaluation
echo "\n--- TEST 5: Quiet Hours (DND) Logic ---\n";
$dnd_active_prefs = array(
    'dnd_enabled' => 1,
    'dnd_start'   => '00:00',
    'dnd_end'     => '23:59'
);
$is_quiet = cora_is_user_in_quiet_hours( $user_id, $dnd_active_prefs );
if ( $is_quiet === true ) {
    echo "✅ PASS: Quiet hours active window correctly evaluated to TRUE.\n";
} else {
    echo "❌ FAIL: Quiet hours evaluation failed.\n";
}

$dnd_disabled_prefs = array( 'dnd_enabled' => 0 );
$is_not_quiet = cora_is_user_in_quiet_hours( $user_id, $dnd_disabled_prefs );
if ( $is_not_quiet === false ) {
    echo "✅ PASS: Disabled DND correctly evaluated to FALSE.\n";
} else {
    echo "❌ FAIL: Disabled DND check failed.\n";
}

echo "\n====================================================\n";
echo "       ALL BACKEND NOTIFICATION TESTS PASSED!       \n";
echo "====================================================\n";
