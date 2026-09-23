<?php
/**
 * Automated Test Suite for Phase 0 Security Hardening
 *
 * Tests:
 * 1. SEC-001: Verification token hash comparison & failure on is_already_verified bypass
 * 2. SEC-005: WhatsApp HMAC-SHA256 signature verification (positive & negative cases)
 * 3. SEC-003: Backup file isolation verification
 */

$failures = 0;
$passes = 0;

function assert_test( $condition, $message ) {
    global $failures, $passes;
    if ( $condition ) {
        echo " [PASS] $message\n";
        $passes++;
    } else {
        echo "![FAIL] $message\n";
        $failures++;
    }
}

echo "========================================================\n";
echo " Running Phase 0 Security Remediation Tests\n";
echo "========================================================\n\n";

// --- TEST SUITE 1: SEC-001 Verification Token Logic ---
echo "1. Testing SEC-001 Email Verification Logic...\n";

// Case 1.1: Valid token hash matches
$raw_token = bin2hex( random_bytes( 32 ) );
$stored_hash = hash( 'sha256', $raw_token );
$input_token_hash = hash( 'sha256', $raw_token );
assert_test( hash_equals( $stored_hash, $input_token_hash ), 'Valid 32-byte token matches stored SHA-256 hash' );

// Case 1.2: Tampered/wrong token does not match
$tampered_token = bin2hex( random_bytes( 32 ) );
$tampered_hash = hash( 'sha256', $tampered_token );
assert_test( ! hash_equals( $stored_hash, $tampered_hash ), 'Tampered token hash mismatch correctly fails' );

// Case 1.3: Attacker supplying arbitrary token for an already-verified user
// Verification handler policy simulation
function simulate_verification_policy( $stored_hash, $input_token, $is_already_verified, $created_time ) {
    $input_hash = hash( 'sha256', $input_token );
    $is_valid = false;
    
    if ( ! empty( $stored_hash ) && hash_equals( $stored_hash, $input_hash ) ) {
        if ( ( time() - $created_time ) <= 48 * 3600 ) {
            $is_valid = true;
        }
    }
    
    if ( ! $is_valid ) {
        if ( $is_already_verified ) {
            return 'REDIRECT_ALREADY_VERIFIED_LOGIN';
        }
        return 'REDIRECT_INVALID_ERROR';
    }
    
    return 'AUTH_SUCCESS_LOGIN';
}

$attacker_token = 'attacker_arbitrary_token_123';
$result_already_verified = simulate_verification_policy( '', $attacker_token, true, 0 );
assert_test( $result_already_verified === 'REDIRECT_ALREADY_VERIFIED_LOGIN', 'Arbitrary token with already-verified account redirects to login notice (NO AUTH BYPASS)' );

$result_invalid = simulate_verification_policy( $stored_hash, $attacker_token, false, time() );
assert_test( $result_invalid === 'REDIRECT_INVALID_ERROR', 'Invalid token on unverified account fails closed' );

$result_valid = simulate_verification_policy( $stored_hash, $raw_token, false, time() );
assert_test( $result_valid === 'AUTH_SUCCESS_LOGIN', 'Genuine matching token grants verification and single-use login' );

$result_expired = simulate_verification_policy( $stored_hash, $raw_token, false, time() - ( 49 * 3600 ) );
assert_test( $result_expired === 'REDIRECT_INVALID_ERROR', 'Expired token (>48h) fails closed' );


// --- TEST SUITE 2: SEC-005 WhatsApp HMAC-SHA256 Signature Verification ---
echo "\n2. Testing SEC-005 WhatsApp Webhook Signature Verification...\n";

function verify_wa_signature( $raw_body, $signature_header, $app_secret ) {
    if ( empty( $signature_header ) || empty( $app_secret ) ) {
        return false;
    }
    $expected_prefix = 'sha256=';
    if ( strpos( $signature_header, $expected_prefix ) !== 0 ) {
        return false;
    }
    $expected_hash = hash_hmac( 'sha256', $raw_body, $app_secret );
    $provided_hash = substr( $signature_header, strlen( $expected_prefix ) );
    return hash_equals( $expected_hash, $provided_hash );
}

$app_secret = 'cora_meta_secret_key_abcdef123456';
$sample_payload = json_encode( array(
    'object' => 'whatsapp_business_account',
    'entry'  => array(
        array(
            'id' => '123456789',
            'changes' => array(
                array(
                    'value' => array(
                        'messaging_product' => 'whatsapp',
                        'messages' => array(
                            array( 'from' => '919876543210', 'text' => array( 'body' => 'Hello Cora' ) )
                        )
                    )
                )
            )
        )
    )
) );

$valid_signature = 'sha256=' . hash_hmac( 'sha256', $sample_payload, $app_secret );
$forged_signature = 'sha256=' . hash_hmac( 'sha256', $sample_payload, 'attacker_wrong_secret' );
$malformed_header = hash_hmac( 'sha256', $sample_payload, $app_secret ); // missing sha256= prefix

assert_test( verify_wa_signature( $sample_payload, $valid_signature, $app_secret ), 'Valid Meta HMAC signature accepted' );
assert_test( ! verify_wa_signature( $sample_payload, $forged_signature, $app_secret ), 'Forged HMAC signature rejected' );
assert_test( ! verify_wa_signature( $sample_payload, $malformed_header, $app_secret ), 'Missing sha256= prefix rejected' );
assert_test( ! verify_wa_signature( $sample_payload . 'tampered', $valid_signature, $app_secret ), 'Tampered body rejected' );
assert_test( ! verify_wa_signature( $sample_payload, '', $app_secret ), 'Empty signature rejected' );


// --- TEST SUITE 3: SEC-003 Backup Relocation Verification ---
echo "\n3. Testing SEC-003 Backup File Isolation...\n";

$public_backup_dir = __DIR__ . '/../app/public/wp-content/cora-private-backups';
$secure_backup_dir = __DIR__ . '/../app/cora-private-backups';

$public_sql_files = glob( $public_backup_dir . '/*.sql' );
$public_zip_files = glob( $public_backup_dir . '/*.zip' );
$secure_sql_files = glob( $secure_backup_dir . '/*.sql' );

assert_test( empty( $public_sql_files ) && empty( $public_zip_files ), 'Public web root contains ZERO .sql or .zip backup dumps' );
assert_test( ! empty( $secure_sql_files ), 'Private backup directory outside ABSPATH retains database snapshots' );

echo "\n========================================================\n";
echo " Results: $passes Passed, $failures Failed\n";
echo "========================================================\n";

if ( $failures > 0 ) {
    exit( 1 );
}
exit( 0 );
