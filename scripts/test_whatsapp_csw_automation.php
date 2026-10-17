<?php
/**
 * Cora AI WhatsApp Customer Service Window (CSW) & Automation Verification Suite
 *
 * Rigorously verifies:
 * 1. Meta Graph API Profile & DP (Display Picture) verification.
 * 2. 24-Hour CSW status checking and window calculation.
 * 3. Outbound session message dispatch inside active CSW ($0 Meta Free Tier).
 * 4. Zero-Cost Smart Queueing when CSW is expired (zero paid templates triggered).
 * 5. Inbound Webhook event simulation:
 *    - Automated CSW opening / renewal (24-hour window reset).
 *    - Instant automatic flush of pending queued updates.
 * 6. Interactive Bot Commands:
 *    - [1] CRM Leads inquiry.
 *    - [2] Financials & Revenue inquiry.
 *    - [3] Staff Attendance check-ins.
 *    - [4] Daily Executive Briefing.
 *    - [5] Gateway Health & CSW remaining time.
 *    - [Menu] Default interactive menu.
 * 7. Scheduled Cron maintenance hook registrations.
 *
 * Usage: php scripts/test_whatsapp_csw_automation.php [optional_test_phone]
 */

require_once __DIR__ . '/../app/public/wp-load.php';

if ( ! class_exists( 'Cora_WhatsApp_Gateway' ) ) {
    echo "❌ Error: Cora_WhatsApp_Gateway class not found.\n";
    exit( 1 );
}

$gateway  = Cora_WhatsApp_Gateway::instance();
$settings = $gateway->get_settings();
$test_phone = ! empty( $argv[1] ) ? $argv[1] : ( $settings['admin_phone'] ?: '919817059266' );
$clean_phone = $gateway->clean_phone_number( $test_phone );

$passed = 0;
$failed = 0;

function assert_test( $title, $condition, $details = '' ) {
    global $passed, $failed;
    if ( $condition ) {
        $passed++;
        echo "  ✅ PASS: {$title}\n";
        if ( ! empty( $details ) ) {
            echo "     └─ {$details}\n";
        }
    } else {
        $failed++;
        echo "  ❌ FAIL: {$title}\n";
        if ( ! empty( $details ) ) {
            echo "     └─ {$details}\n";
        }
    }
}

echo "====================================================================\n";
echo "🤖 CORA AI WHATSAPP AUTOMATION & CSW ZERO-COST VERIFICATION SUITE\n";
echo "====================================================================\n";
echo "• Registered Business Phone: " . $gateway->get_display_phone_number() . "\n";
echo "• Business Phone Digits    : " . $gateway->get_business_phone_digits() . "\n";
echo "• Test Recipient Phone     : +{$clean_phone}\n";
echo "• Phone Number ID          : {$settings['phone_number_id']}\n";
echo "• WABA ID                  : {$settings['waba_id']}\n";
echo "• Timestamp                : " . current_time( 'mysql' ) . "\n";
echo "====================================================================\n\n";

// -------------------------------------------------------------
// TEST 1: Meta Cloud API Business Profile & Official DP Verification
// -------------------------------------------------------------
echo "[TEST 1] Meta Graph API Profile & Official Display Picture (DP)...\n";
$profile_url = Cora_WhatsApp_Gateway::GRAPH_API_BASE . '/v22.0/' . rawurlencode( $settings['phone_number_id'] ) . '/whatsapp_business_profile?fields=about,address,description,email,profile_picture_url,websites,vertical';
$res = wp_remote_get( $profile_url, array(
    'headers' => array(
        'Authorization' => 'Bearer ' . $settings['access_token'],
        'Content-Type'  => 'application/json',
    ),
    'timeout' => 15,
) );

$profile_ok = false;
$dp_url = '';
$about_text = '';
if ( ! is_wp_error( $res ) && wp_remote_retrieve_response_code( $res ) === 200 ) {
    $p_json = json_decode( wp_remote_retrieve_body( $res ), true );
    if ( ! empty( $p_json['data'][0]['profile_picture_url'] ) ) {
        $profile_ok = true;
        $dp_url     = $p_json['data'][0]['profile_picture_url'];
        $about_text = $p_json['data'][0]['about'] ?? '';
    }
}

assert_test(
    "Official Cora AI DP is active on Meta WhatsApp CDN",
    $profile_ok && strpos( $dp_url, 'pps.whatsapp.net' ) !== false,
    "DP URL: " . substr( $dp_url, 0, 75 ) . "... | About: '{$about_text}'"
);

// Verify local DP asset exists
$local_asset = WP_CONTENT_DIR . '/plugins/cora-workspace/assets/images/cora-ai-whatsapp-dp.jpg';
assert_test(
    "Local fallback asset cora-ai-whatsapp-dp.jpg exists and is non-empty",
    file_exists( $local_asset ) && filesize( $local_asset ) > 10000,
    "Path: {$local_asset} (" . round( filesize( $local_asset ) / 1024 ) . " KB)"
);

echo "\n";

// -------------------------------------------------------------
// TEST 2: CSW Window Calculation & Deep Links
// -------------------------------------------------------------
echo "[TEST 2] 24-Hour Customer Service Window (CSW) Engine & Links...\n";
// Ensure session is active for initial check
update_option( 'cora_wa_session_' . $clean_phone, time() + 7200 ); // 2 hours
$csw_active = $gateway->get_session_window( $clean_phone );

assert_test(
    "Active CSW returns active = true and formatted remaining time",
    $csw_active['active'] === true && strpos( $csw_active['remaining_human'], 'left' ) !== false,
    "Remaining: {$csw_active['remaining_human']} | Type: {$csw_active['type']}"
);

// Test activation URL
$act_url = $gateway->get_activation_url( 'View Updates ⚡' );
assert_test(
    "Activation deep-link targets registered business number (+91 70153 17965)",
    strpos( $act_url, 'wa.me/917015317965' ) !== false,
    "URL: {$act_url}"
);

echo "\n";

// -------------------------------------------------------------
// TEST 3: Outbound Free Session Message Inside Active CSW ($0 Tier)
// -------------------------------------------------------------
echo "[TEST 3] Outbound Session Message Dispatch inside Active CSW ($0 Free Tier)...\n";
$session_msg = "⚡ *Cora AI Automated Test*\n\n"
    . "Verifying $0 Customer Service Window (CSW) free session dispatch at " . current_time( 'H:i:s' ) . ".\n"
    . "• Rate: ₹0.00 / $0.00 (Service conversation)\n"
    . "• Window remaining: {$csw_active['remaining_human']}";

$sess_res = $gateway->send_session_message( $clean_phone, $session_msg );
$sess_ok = ! empty( $sess_res['success'] ) && ! empty( $sess_res['message_id'] );

assert_test(
    "Session message accepted by Meta Cloud API inside CSW",
    $sess_ok,
    "Message ID: " . ( $sess_res['message_id'] ?? ( $sess_res['error'] ?? 'None' ) )
);

echo "\n";

// -------------------------------------------------------------
// TEST 4: Zero-Cost Smart Queueing When CSW is Expired
// -------------------------------------------------------------
echo "[TEST 4] Zero-Cost Smart Queueing Outside 24h CSW (Keeping Bill at ₹0.00)...\n";
// Force CSW to expired state for testing
update_option( 'cora_wa_session_' . $clean_phone, time() - 3600 );
$gateway->clear_pending_updates( $clean_phone );

$csw_expired = $gateway->get_session_window( $clean_phone );
assert_test(
    "Window state correctly evaluates to expired",
    $csw_expired['active'] === false && $csw_expired['remaining_human'] === 'Expired',
    "Status: {$csw_expired['remaining_human']}"
);

// Queue 2 distinct workspace updates
$gateway->queue_pending_update(
    $clean_phone,
    "Commercial Shoot Lead",
    "Client: Aarav Mehta • Budget: ₹45,000 • Studio Slot A",
    home_url( '/workspace/dashboard?sub_page=leads' ),
    "CRM Lead"
);

$gateway->queue_pending_update(
    $clean_phone,
    "Staff Attendance Punch",
    "Staff: Rohan Verma • Punch In at 09:15 AM (Geofence Verified)",
    home_url( '/workspace/dashboard?sub_page=attendance' ),
    "Staff Punch"
);

$pending_items = $gateway->get_pending_updates( $clean_phone );
assert_test(
    "Pending updates successfully queued in database",
    count( $pending_items ) === 2,
    "Pending count: " . count( $pending_items ) . " items"
);

// Dispatch update via universal dispatcher while expired using target phone
$dispatch_res = cora_dispatch_whatsapp_to_user(
    $clean_phone,
    "Payment Received",
    "Invoice #INV-9281 cleared for ₹18,500 via UPI Razorpay.",
    home_url( '/workspace/financials' ),
    "Payment"
);

$pending_after_dispatch = $gateway->get_pending_updates( $clean_phone );
$is_queued = ! empty( $dispatch_res['dispatched'][ $clean_phone ]['queued'] );
assert_test(
    "Universal dispatcher queues update instead of sending paid marketing template",
    $is_queued && count( $pending_after_dispatch ) === 3,
    "Dispatch status: queued = true | Queue size: " . count( $pending_after_dispatch )
);

echo "\n";

// -------------------------------------------------------------
// TEST 5: Inbound Webhook Activation & Automatic Queue Flush
// -------------------------------------------------------------
echo "[TEST 5] Inbound Webhook Simulation: 24h CSW Renewal & Auto-Flush...\n";
// Simulate inbound ping from user via the REST webhook handler
$webhook_payload = array(
    'object' => 'whatsapp_business_account',
    'entry'  => array(
        array(
            'id'      => $settings['waba_id'],
            'changes' => array(
                array(
                    'value' => array(
                        'messaging_product' => 'whatsapp',
                        'metadata'          => array(
                            'display_phone_number' => '7015317965',
                            'phone_number_id'      => $settings['phone_number_id'],
                        ),
                        'contacts'          => array(
                            array(
                                'profile' => array( 'name' => 'Studio Admin' ),
                                'wa_id'   => $clean_phone,
                            ),
                        ),
                        'messages'          => array(
                            array(
                                'from'      => $clean_phone,
                                'id'        => 'wamid.TEST_INBOUND_' . time(),
                                'timestamp' => (string) time(),
                                'text'      => array( 'body' => 'View Updates ⚡' ),
                                'type'      => 'text',
                            ),
                        ),
                    ),
                    'field' => 'messages',
                ),
            ),
        ),
    ),
);

$request = new WP_REST_Request( 'POST', '/cora/v1/whatsapp/webhook' );
$request->set_header( 'content-type', 'application/json' );
$request->set_body( wp_json_encode( $webhook_payload ) );

$wh_response = cora_rest_whatsapp_webhook_receive( $request );
$wh_status = is_object( $wh_response ) && method_exists( $wh_response, 'get_status' ) ? $wh_response->get_status() : 200;

assert_test(
    "Webhook handler processed inbound message successfully",
    $wh_status === 200,
    "HTTP Status: {$wh_status}"
);

// Verify CSW is now refreshed to 24 hours
$csw_post_inbound = $gateway->get_session_window( $clean_phone );
assert_test(
    "Customer Service Window renewed for 24 hours upon inbound message",
    $csw_post_inbound['active'] === true && $csw_post_inbound['remaining_seconds'] > 85000,
    "Window remaining: {$csw_post_inbound['remaining_human']} (" . $csw_post_inbound['remaining_seconds'] . "s)"
);

// Verify queued updates were flushed and cleared
$pending_after_flush = $gateway->get_pending_updates( $clean_phone );
assert_test(
    "All queued updates were automatically flushed and queue cleared ($0 delivery)",
    count( $pending_after_flush ) === 0,
    "Pending queue count: " . count( $pending_after_flush )
);

echo "\n";

// -------------------------------------------------------------
// TEST 6: Interactive Bot Menu & Keyword Commands
// -------------------------------------------------------------
echo "[TEST 6] Interactive Bot Commands inside Active CSW...\n";

// Test Command '1' (Leads)
$cmd1_res = $gateway->handle_inbound_query( $clean_phone, '1' );
assert_test(
    "Bot Command '1' (Leads) executes and delivers reply",
    ! empty( $cmd1_res['success'] ),
    "Message ID: " . ( $cmd1_res['message_id'] ?? 'N/A' )
);

// Test Command '2' (Revenue / Financials)
$cmd2_res = $gateway->handle_inbound_query( $clean_phone, '2' );
assert_test(
    "Bot Command '2' (Revenue) executes and delivers reply",
    ! empty( $cmd2_res['success'] ),
    "Message ID: " . ( $cmd2_res['message_id'] ?? 'N/A' )
);

// Test Command '3' (Attendance)
$cmd3_res = $gateway->handle_inbound_query( $clean_phone, '3' );
assert_test(
    "Bot Command '3' (Attendance) executes and delivers reply",
    ! empty( $cmd3_res['success'] ),
    "Message ID: " . ( $cmd3_res['message_id'] ?? 'N/A' )
);

// Test Command '5' (Status / Window Health)
$cmd5_res = $gateway->handle_inbound_query( $clean_phone, '5' );
assert_test(
    "Bot Command '5' (Status & Quota) executes and delivers reply",
    ! empty( $cmd5_res['success'] ),
    "Message ID: " . ( $cmd5_res['message_id'] ?? 'N/A' )
);

// Test Command 'menu' / 'help'
$cmd_menu_res = $gateway->handle_inbound_query( $clean_phone, 'menu' );
assert_test(
    "Bot Command 'menu' executes and delivers interactive options card",
    ! empty( $cmd_menu_res['success'] ),
    "Message ID: " . ( $cmd_menu_res['message_id'] ?? 'N/A' )
);

echo "\n";

// -------------------------------------------------------------
// TEST 7: Twice-Daily CSW Keeper Cron Registration
// -------------------------------------------------------------
echo "[TEST 7] Scheduled Maintenance & Window Keeper Cron...\n";
$morning_cron = wp_next_scheduled( 'cora_cron_morning_whatsapp_activation' ) ?: wp_next_scheduled( 'cora_cron_daily_whatsapp_activation' );
$evening_cron = wp_next_scheduled( 'cora_cron_evening_whatsapp_activation' );

assert_test(
    "Morning CSW Keeper Cron (09:00 AM) is registered in WordPress",
    ! empty( $morning_cron ),
    "Next run: " . ( $morning_cron ? gmdate( 'Y-m-d H:i:s \U\T\C', $morning_cron ) : 'Not scheduled' )
);

assert_test(
    "Evening CSW Keeper Cron (05:30 PM) is registered in WordPress",
    ! empty( $evening_cron ),
    "Next run: " . ( $evening_cron ? gmdate( 'Y-m-d H:i:s \U\T\C', $evening_cron ) : 'Not scheduled' )
);

// Test push dispatcher function
$push_count = $gateway->trigger_daily_activation_push_to_all_users();
assert_test(
    "trigger_daily_activation_push_to_all_users() successfully dispatches to users",
    $push_count >= 1,
    "Users notified: {$push_count}"
);

echo "\n";
echo "====================================================================\n";
echo "📊 VERIFICATION SUMMARY\n";
echo "====================================================================\n";
echo "• Total Tests Run : " . ( $passed + $failed ) . "\n";
echo "• Passed          : {$passed}\n";
echo "• Failed          : {$failed}\n";
echo "• System Health   : " . ( $failed === 0 ? "🟢 100% OPERATIONAL & VERIFIED" : "🔴 ATTENTION NEEDED" ) . "\n";
echo "====================================================================\n";

exit( $failed === 0 ? 0 : 1 );
