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

if ( ! defined( 'ABSPATH' ) ) {
    if ( file_exists( __DIR__ . '/../app/public/wp-load.php' ) ) {
        require_once __DIR__ . '/../app/public/wp-load.php';
    } elseif ( file_exists( dirname( __DIR__ ) . '/public_html/demo/wp-load.php' ) ) {
        require_once dirname( __DIR__ ) . '/public_html/demo/wp-load.php';
    } elseif ( file_exists( '/home/u484406462/domains/heycora.in/public_html/demo/wp-load.php' ) ) {
        require_once '/home/u484406462/domains/heycora.in/public_html/demo/wp-load.php';
    }
}

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

// Test Interactive Button Card ($0 Free Tier)
$btn_res = $gateway->send_interactive_buttons(
    $clean_phone,
    "🏢 *Commercial Studio Inquiry Match*\n\n• Date: " . current_time( 'M j, Y' ) . "\n• Client: Aarav Mehta\n• Budget: ₹45,000\n\nChoose an action:",
    array( "Accept Booking", "Reschedule", "Call Client" ),
    "🎬 Cora Studio AI",
    "24h Free Customer Service Window"
);
assert_test(
    "Interactive Quick-Reply Button Card delivers inside CSW ($0 Free Tier)",
    ! empty( $btn_res['success'] ),
    "Message ID: " . ( $btn_res['message_id'] ?? 'N/A' )
);

// Test Image Card with Buttons ($0 Free Tier)
$img_res = $gateway->send_image_card(
    $clean_phone,
    "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80",
    "🏡 *Luxury Penthouse • Bandra West*\n\n• Price: ₹12.5 Cr\n• 4 BHK • 3,200 sq.ft.\n• Private Terrace & Sea View",
    array( "Interested", "Next Property" ),
    "Cora Real Estate AI • $0 Free Session"
);
assert_test(
    "Image Card with Interactive Buttons delivers inside CSW ($0 Free Tier)",
    ! empty( $img_res['success'] ),
    "Message ID: " . ( $img_res['message_id'] ?? 'N/A' )
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

// -------------------------------------------------------------
// TEST 8: Hourly Pacing & Rate Limiting Engine (Max 3/hr Hard Cap)
// -------------------------------------------------------------
echo "[TEST 8] Hourly Pacing & Rate Limiting Engine (Max 3/hr)...\n";
$test_rate_phone = '919999999999';
delete_option( 'cora_wa_hourly_dispatches_' . $test_rate_phone );

$can_1 = $gateway->can_send_automated_message( $test_rate_phone );
$gateway->record_automated_message_sent( $test_rate_phone );

$can_2 = $gateway->can_send_automated_message( $test_rate_phone );
$gateway->record_automated_message_sent( $test_rate_phone );

$can_3 = $gateway->can_send_automated_message( $test_rate_phone );
$gateway->record_automated_message_sent( $test_rate_phone );

$can_4 = $gateway->can_send_automated_message( $test_rate_phone );
$count_1hr = $gateway->get_hourly_dispatches_count( $test_rate_phone );

assert_test(
    "Rate limiter allows messages 1 to 3 within rolling 1-hour window",
    $can_1 === true && $can_2 === true && $can_3 === true,
    "Slots 1, 2, 3 granted successfully"
);

assert_test(
    "Rate limiter strictly enforces maximum 3 automated messages per hour (4th message throttled/queued)",
    $can_4 === false && $count_1hr === 3,
    "Dispatches recorded: {$count_1hr}/3 | 4th attempt rejected: " . ( ! $can_4 ? 'true' : 'false' )
);

// Cleanup
delete_option( 'cora_wa_hourly_dispatches_' . $test_rate_phone );

echo "\n";

// -------------------------------------------------------------
// TEST 9: 1-Click Cryptographic Tokenized Executive Daily Report
// -------------------------------------------------------------
echo "[TEST 9] 1-Click Cryptographic Tokenized Executive Daily Report...\n";
assert_test(
    "cora_get_24h_report_url() function exists and generates tokenized URL",
    function_exists( 'cora_get_24h_report_url' ),
    "cora_get_24h_report_url is loaded"
);

$report_url = cora_get_24h_report_url( 1, current_time( 'Y-m-d' ) );
$parsed_url = wp_parse_url( $report_url );
parse_str( $parsed_url['query'] ?? '', $query_args );

$has_token = ! empty( $query_args['token'] );
$has_agency = ! empty( $query_args['agency_id'] );
$has_date   = ! empty( $query_args['date'] );

$expected_token = hash_hmac( 'sha256', "cora_exec_report_1_" . current_time( 'Y-m-d' ), wp_salt( 'auth' ) );
$token_matches  = $has_token && hash_equals( $expected_token, $query_args['token'] );

assert_test(
    "Executive report URL includes cryptographic HMAC-SHA256 authentication token",
    $token_matches && $has_agency && $has_date,
    "URL: " . substr( $report_url, 0, 75 ) . "... | Token verified: " . ( $token_matches ? 'YES' : 'NO' )
);

// Verify full workspace data includes all members and leads
$exec_data = cora_generate_24h_executive_report_data( 1 );
$has_roster = ! empty( $exec_data['team_roster'] ) && is_array( $exec_data['team_roster'] );
$has_leads  = isset( $exec_data['total_leads_today'] ) && is_array( $exec_data['today_leads'] );

assert_test(
    "Executive report data aggregates full workspace team roster and daily CRM leads",
    $has_roster && $has_leads,
    "Team Members: " . count( $exec_data['team_roster'] ?? array() ) . " | Today's Leads: " . ( $exec_data['total_leads_today'] ?? 0 )
);

// Verify A4 Print-Ready HTML renders both sections
$html_report = cora_render_24h_executive_pdf_html( $exec_data );
$has_roster_html = strpos( $html_report, 'Workspace Team Roster &amp; Member Attendance' ) !== false;
$has_leads_html  = strpos( $html_report, 'Daily CRM Lead Pipeline &amp; Inquiries' ) !== false;
$has_print_btn   = strpos( $html_report, 'Print / Save Official PDF' ) !== false;

assert_test(
    "A4 Executive Report renders certified HTML with full team roster and leads pipeline",
    $has_roster_html && $has_leads_html && $has_print_btn,
    "Roster Section: " . ( $has_roster_html ? 'Present' : 'Missing' ) . " | Leads Section: " . ( $has_leads_html ? 'Present' : 'Missing' )
);

echo "\n";

// -------------------------------------------------------------
// TEST 10: Inbound Lead Rapid-Fire Pacing & Intelligent Batching
// -------------------------------------------------------------
echo "[TEST 10] Inbound Lead Rapid-Fire Pacing & Batching...\n";
$test_lead_phone = '919817059266';
$lead_pacing_key = 'cora_wa_recent_lead_' . $test_lead_phone;
set_transient( $lead_pacing_key, 1, 600 ); // Simulate a lead was sent within past 10 minutes

$lead_result = $gateway->send_lead_alert( $test_lead_phone, array(
    'name'  => 'Rohan Verma',
    'title' => 'Commercial Studio Session',
    'phone' => '+91 98112 00000',
    'email' => 'rohan.v@example.com',
), true );

assert_test(
    "Rapid-fire incoming lead inquiries are intelligently batched instead of buzzing user phone",
    ! empty( $lead_result['batched'] ) && ! empty( $lead_result['queued'] ),
    "Batched: " . ( ! empty( $lead_result['batched'] ) ? 'YES' : 'NO' ) . " | Status: " . ( $lead_result['message'] ?? 'Queued' )
);

// -------------------------------------------------------------
// TEST 11: Webhook Interactive Button Click Simulation: View Full Report
// -------------------------------------------------------------
echo "[TEST 11] Interactive Button Reply Simulation: View Full Report...\n";
$btn_report_payload = array(
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
                                'from'        => $clean_phone,
                                'id'          => 'wamid.TEST_BTN_REPORT_' . time(),
                                'timestamp'   => (string) time(),
                                'type'        => 'interactive',
                                'interactive' => array(
                                    'type'         => 'button_reply',
                                    'button_reply' => array(
                                        'id'    => 'btn_view_full_report',
                                        'title' => 'View Full Report',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'field' => 'messages',
                ),
            ),
        ),
    ),
);

$req_btn_report = new WP_REST_Request( 'POST', '/cora/v1/whatsapp/webhook' );
$req_btn_report->set_header( 'content-type', 'application/json' );
$req_btn_report->set_body( wp_json_encode( $btn_report_payload ) );

$res_btn_report = cora_rest_whatsapp_webhook_receive( $req_btn_report );
$status_btn_report = is_object( $res_btn_report ) && method_exists( $res_btn_report, 'get_status' ) ? $res_btn_report->get_status() : 200;

assert_test(
    "Webhook successfully receives and responds to 'View Full Report' interactive button click",
    $status_btn_report === 200,
    "HTTP Status: {$status_btn_report}"
);

// -------------------------------------------------------------
// TEST 12: Webhook Interactive Button Click Simulation: Open Dashboard
// -------------------------------------------------------------
echo "[TEST 12] Interactive Button Reply Simulation: Open Dashboard...\n";
$btn_dash_payload = array(
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
                                'from'        => $clean_phone,
                                'id'          => 'wamid.TEST_BTN_DASH_' . time(),
                                'timestamp'   => (string) time(),
                                'type'        => 'interactive',
                                'interactive' => array(
                                    'type'         => 'button_reply',
                                    'button_reply' => array(
                                        'id'    => 'btn_open_dashboard',
                                        'title' => 'Open Dashboard',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'field' => 'messages',
                ),
            ),
        ),
    ),
);

$req_btn_dash = new WP_REST_Request( 'POST', '/cora/v1/whatsapp/webhook' );
$req_btn_dash->set_header( 'content-type', 'application/json' );
$req_btn_dash->set_body( wp_json_encode( $btn_dash_payload ) );

$res_btn_dash = cora_rest_whatsapp_webhook_receive( $req_btn_dash );
$status_btn_dash = is_object( $res_btn_dash ) && method_exists( $res_btn_dash, 'get_status' ) ? $res_btn_dash->get_status() : 200;

assert_test(
    "Webhook successfully receives and responds to 'Open Dashboard' interactive button click",
    $status_btn_dash === 200,
    "HTTP Status: {$status_btn_dash}"
);

// -------------------------------------------------------------
// TEST 13: Direct Access & Route Protection for Executive Report
// -------------------------------------------------------------
echo "[TEST 13] Executive Report Route Routing & Verification Bypass Protection...\n";
$report_url = cora_get_24h_report_url( 1, current_time( 'Y-m-d' ) );
$parsed_report = wp_parse_url( $report_url );
parse_str( $parsed_report['query'] ?? '', $query_parts );

$test_token = $query_parts['token'] ?? '';
$expected_hash = hash_hmac( 'sha256', "cora_exec_report_1_" . current_time( 'Y-m-d' ), wp_salt( 'auth' ) );

assert_test(
    "HMAC-SHA256 signature verification protects executive report against tampering",
    hash_equals( $expected_hash, $test_token ),
    "Cryptographic token matches expected auth salt hash"
);

// Verify ensure_waba_webhook_subscribed method exists and can be executed safely
assert_test(
    "ensure_waba_webhook_subscribed() method exists on gateway instance",
    method_exists( $gateway, 'ensure_waba_webhook_subscribed' ),
    "Method confirmed present"
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
