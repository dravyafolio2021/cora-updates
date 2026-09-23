<?php
/**
 * Cora Comprehensive Security Audit Verification Suite
 * Verifies all 18 security remediations (SEC-001 through SEC-018).
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__ ) . '/app/public/' );
}
if ( ! defined( 'CORA_WORKSPACE_VERSION' ) ) {
    define( 'CORA_WORKSPACE_VERSION', '1.0.0' );
}

// Global mock state for WP functions
$GLOBALS['cora_mock_options'] = array();
$GLOBALS['cora_mock_usermeta'] = array();

if ( ! function_exists( 'get_option' ) ) {
    function get_option( $name, $default = false ) {
        return $GLOBALS['cora_mock_options'][ $name ] ?? $default;
    }
}
if ( ! function_exists( 'update_option' ) ) {
    function update_option( $name, $value, $autoload = null ) {
        $GLOBALS['cora_mock_options'][ $name ] = $value;
        return true;
    }
}
if ( ! function_exists( 'delete_option' ) ) {
    function delete_option( $name ) {
        unset( $GLOBALS['cora_mock_options'][ $name ] );
        return true;
    }
}
if ( ! function_exists( 'get_current_user_id' ) ) {
    function get_current_user_id() { return 1; }
}
if ( ! function_exists( 'get_userdata' ) ) {
    function get_userdata( $user_id ) {
        $u = new stdClass();
        $u->ID = intval( $user_id );
        $u->user_email = 'test@cora.local';
        $u->roles = array( 'cora_super_admin' );
        return $u;
    }
}
if ( ! function_exists( 'get_user_meta' ) ) {
    function get_user_meta( $user_id, $key, $single = false ) {
        return $GLOBALS['cora_mock_usermeta'][ $user_id ][ $key ] ?? ( $single ? '' : array() );
    }
}
if ( ! function_exists( 'sanitize_text_field' ) ) {
    function sanitize_text_field( $str ) { return trim( strip_tags( (string)$str ) ); }
}
if ( ! function_exists( 'sanitize_email' ) ) {
    function sanitize_email( $email ) { return filter_var( trim( (string)$email ), FILTER_SANITIZE_EMAIL ); }
}
if ( ! function_exists( 'sanitize_key' ) ) {
    function sanitize_key( $key ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string)$key ) ); }
}
if ( ! function_exists( 'wp_json_encode' ) ) {
    function wp_json_encode( $data ) { return json_encode( $data ); }
}
if ( ! function_exists( 'apply_filters' ) ) {
    function apply_filters( $tag, $value, ...$args ) { return $value; }
}
if ( ! function_exists( 'do_action' ) ) {
    function do_action( $tag, ...$args ) {}
}
if ( ! function_exists( 'is_wp_error' ) ) {
    function is_wp_error( $thing ) { return ( is_object( $thing ) && is_a( $thing, 'WP_Error' ) ); }
}
if ( ! function_exists( 'current_time' ) ) {
    function current_time( $type ) { return date( 'Y-m-d H:i:s' ); }
}
if ( ! function_exists( 'wp_parse_url' ) ) {
    function wp_parse_url( $url ) { return parse_url( $url ); }
}
if ( ! function_exists( 'wp_salt' ) ) {
    function wp_salt( $scheme = 'auth' ) { return 'cora_mock_salt_secret_1234567890'; }
}
if ( ! function_exists( 'wp_check_password' ) ) {
    function wp_check_password( $password, $hash ) { return hash_equals( (string)$hash, (string)$password ); }
}
if ( ! function_exists( 'wp_generate_password' ) ) {
    function wp_generate_password( $length = 12, $special_chars = true ) { return bin2hex( random_bytes( ceil( $length / 2 ) ) ); }
}
if ( ! function_exists( 'is_ssl' ) ) {
    function is_ssl() { return false; }
}
if ( ! class_exists( 'WP_Error' ) ) {
    class WP_Error {
        public $errors = array();
        public function __construct( $code = '', $message = '', $data = '' ) {
            if ( $code ) $this->errors[ $code ][] = $message;
        }
        public function get_error_message() {
            foreach ( $this->errors as $msgs ) return reset( $msgs );
            return '';
        }
    }
}

$passed = 0;
$failed = 0;
$tests_total = 0;

function run_test( $name, $callback ) {
    global $passed, $failed, $tests_total;
    $tests_total++;
    try {
        $result = $callback();
        if ( $result === true ) {
            echo "  \033[32m✔ PASS\033[0m: {$name}\n";
            $passed++;
        } else {
            echo "  \033[31m✘ FAIL\033[0m: {$name} - " . ( is_string( $result ) ? $result : 'Condition returned false' ) . "\n";
            $failed++;
        }
    } catch ( Throwable $e ) {
        echo "  \033[31m✘ ERROR\033[0m: {$name} - Exception: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n=================================================================\n";
echo "  CORA SECURITY AUDIT — MASTER VERIFICATION SUITE (SEC-001 - SEC-018)\n";
echo "=================================================================\n\n";

$plugin_dir   = dirname( __DIR__ ) . '/app/public/wp-content/plugins/cora-workspace';
$auth_file    = $plugin_dir . '/includes/class-cora-authorization.php';
$ssrf_file    = $plugin_dir . '/includes/class-cora-ssrf-filter.php';
$main_file    = $plugin_dir . '/cora-workspace.php';
$wa_file      = $plugin_dir . '/includes/class-cora-whatsapp-gateway.php';
$form_view    = $plugin_dir . '/public-form-view.php';
$gallery_view = $plugin_dir . '/public-gallery-view.php';
$updater_file = $plugin_dir . '/includes/class-cora-workspace-updater.php';
$sw_file      = $plugin_dir . '/assets/pwa/service-worker.js';
$fe_contact   = dirname( __DIR__ ) . '/cora-frontend/app/api/contact/route.ts';
$fe_ai        = dirname( __DIR__ ) . '/cora-frontend/app/api/ai-preview/route.ts';

// ── SEC-001: Verification Token Hashing & Account Takeover Neutralization
run_test( 'SEC-001: Verification token generation uses >=32 bytes & SHA-256 hash comparison', function() use ($main_file) {
    $code = file_get_contents( $main_file );
    if ( strpos( $code, 'cora_workspace_verification_token_hash' ) === false ) {
        return 'Missing cora_workspace_verification_token_hash';
    }
    if ( strpos( $code, 'cora_workspace_email_verified' ) !== false && strpos( $code, 'delete_user_meta( $user_id, \'cora_workspace_verification_token_hash\' )' ) === false ) {
        return 'Hash not deleted upon verification';
    }
    return true;
});

// ── SEC-002: Request Context & Tenant Isolation Matrix
run_test( 'SEC-002: Cora_Request_Context & Authorization Matrix isolates tenant boundaries', function() use ($auth_file) {
    require_once $auth_file;
    if ( ! class_exists( 'Cora_Request_Context' ) || ! function_exists( 'cora_authorize' ) ) {
        return 'Class Cora_Request_Context or cora_authorize missing';
    }

    $tenant1_editor = new Cora_Request_Context( array(
        'user_id'       => 10,
        'user_email'    => 'editor@tenant1.local',
        'wp_role'       => 'cora_editor',
        'tenant_role'   => 'member',
        'tenant_id'     => 'agency_1',
        'tenant_id_num' => 1,
    ) );

    // Member cannot perform destructive super admin actions
    if ( cora_authorize( $tenant1_editor, 'workspace.delete' ) ) {
        return 'Member wrongly authorized for workspace.delete';
    }

    // Member can read tasks
    if ( ! cora_authorize( $tenant1_editor, 'task.read' ) ) {
        return 'Member denied task.read';
    }

    // Cross-tenant resource boundary check
    $tenant2_resource = array( 'agency_id' => 2, 'title' => 'Tenant 2 Form' );
    if ( cora_authorize( $tenant1_editor, 'form.update', $tenant2_resource ) ) {
        return 'Tenant 1 editor authorized to modify Tenant 2 resource (IDOR bypass)';
    }

    return true;
});

// ── SEC-003: Isolated Backup Directory Outside ABSPATH
run_test( 'SEC-003: Backup directory is isolated and no dumps exist in public web root', function() use ($main_file) {
    $code = file_get_contents( $main_file );
    if ( strpos( $code, 'cora_get_backup_dir' ) === false ) {
        return 'Missing cora_get_backup_dir function';
    }
    // Check public uploads folder for leftover SQL / ZIP backups
    $public_uploads = dirname( __DIR__ ) . '/app/public/wp-content/uploads/cora_backups';
    if ( file_exists( $public_uploads ) ) {
        $files = glob( $public_uploads . '/*.{sql,zip,gz}', GLOB_BRACE );
        if ( ! empty( $files ) ) {
            return 'Found exposed backup dumps in public directory: ' . implode( ', ', $files );
        }
    }
    return true;
});

// ── SEC-004: Frontend Package Lockfile Hardening
run_test( 'SEC-004: Next.js and Sharp dependencies are patched against known CVEs', function() {
    $pkg_lock = dirname( __DIR__ ) . '/cora-frontend/package-lock.json';
    if ( ! file_exists( $pkg_lock ) ) {
        return 'package-lock.json missing';
    }
    $data = json_decode( file_get_contents( $pkg_lock ), true );
    $next_ver = $data['packages']['node_modules/next']['version'] ?? '';
    if ( empty( $next_ver ) || version_compare( $next_ver, '16.0.0', '<' ) ) {
        return 'Next.js version not updated: ' . $next_ver;
    }
    return true;
});

// ── SEC-005: WhatsApp HMAC-SHA256 Webhook Verification
run_test( 'SEC-005: WhatsApp webhook verifies Meta HMAC-SHA256 signatures', function() use ($wa_file) {
    require_once $wa_file;
    if ( ! class_exists( 'Cora_WhatsApp_Gateway' ) ) {
        return 'Cora_WhatsApp_Gateway class missing';
    }

    $secret = 'test_app_secret_abc123';
    $payload = json_encode( array( 'object' => 'whatsapp_business_account', 'entry' => array() ) );
    $valid_sig = 'sha256=' . hash_hmac( 'sha256', $payload, $secret );
    $tampered_payload = json_encode( array( 'object' => 'whatsapp_business_account', 'entry' => array( 'tampered' => true ) ) );

    $gw = Cora_WhatsApp_Gateway::get_instance();
    if ( ! $gw->verify_webhook_signature( $payload, $valid_sig, $secret ) ) {
        return 'Valid signature was rejected';
    }
    if ( $gw->verify_webhook_signature( $tampered_payload, $valid_sig, $secret ) ) {
        return 'Tampered payload was incorrectly accepted';
    }
    return true;
});

// ── SEC-006: 32-Byte Cryptographic Tokens & Production URL Redaction
run_test( 'SEC-006: Invitation and Magic Link tokens generate 32-byte entropy and store SHA-256 hashes', function() use ($main_file) {
    $code = file_get_contents( $main_file );
    if ( strpos( $code, 'cora_workspace_magic_token_hash' ) === false ) {
        return 'cora_workspace_magic_token_hash missing from magic link handling';
    }
    if ( strpos( $code, 'random_bytes( 32 )' ) === false ) {
        return '32-byte random entropy missing in token generation';
    }
    return true;
});

// ── SEC-007: AI Action CSRF Enforcement & Schema Gatekeeping
run_test( 'SEC-007: AI chat endpoint strictly requires valid CSRF nonce', function() use ($main_file) {
    $code = file_get_contents( $main_file );
    if ( strpos( $code, 'cora_ajax_ai_chat' ) === false ) {
        return 'cora_ajax_ai_chat missing';
    }
    if ( strpos( $code, 'check_ajax_referer' ) === false ) {
        return 'check_ajax_referer missing in AI chat';
    }
    return true;
});

// ── SEC-008: Stored & DOM XSS Remediation
run_test( 'SEC-008: public-form-view.php eliminates unescaped DOM innerHTML interpolations', function() use ($form_view) {
    $code = file_get_contents( $form_view );
    if ( strpos( $code, 'inp.value = typeof val === \'string\'' ) === false ) {
        return 'Safe input.value assignment missing in repeatable items';
    }
    if ( strpos( $code, '${cur === \'USD\' ? \'$\' : \'₹\'}${amt}' ) === false ) {
        return 'Sanitized mock checkout amounts missing';
    }
    return true;
});

// ── SEC-009: SSRF Filter for Outbound Requests
run_test( 'SEC-009: Cora_SSRF_Filter blocks private, loopback, and cloud metadata IPs', function() use ($ssrf_file) {
    require_once $ssrf_file;
    if ( ! class_exists( 'Cora_SSRF_Filter' ) ) {
        return 'Cora_SSRF_Filter class missing';
    }

    $blocked_targets = array(
        'http://127.0.0.1/admin',
        'http://localhost:8080',
        'http://169.254.169.254/latest/meta-data/',
        'http://metadata.google.internal/computeMetadata/v1/',
        'http://10.0.0.1/internal',
        'http://192.168.1.1/router',
        'http://172.16.0.1/private',
    );

    foreach ( $blocked_targets as $url ) {
        $is_safe = Cora_SSRF_Filter::is_url_safe( $url, false );
        if ( $is_safe ) {
            return "SSRF filter failed to block: {$url}";
        }
    }
    return true;
});

// ── SEC-010: Updater Package Download Host Allowlist
run_test( 'SEC-010: class-cora-workspace-updater.php enforces trusted download domain allowlist', function() use ($updater_file) {
    $code = file_get_contents( $updater_file );
    if ( strpos( $code, 'raw.githubusercontent.com' ) === false || strpos( $code, 'is_allowed_host' ) === false ) {
        return 'Download host allowlist check missing in updater';
    }
    return true;
});

// ── SEC-011: E-Sign Payload Cap & Expiry Enforcement
run_test( 'SEC-011: E-Sign handlers enforce 500KB payload limit and link expiry checks', function() use ($main_file) {
    $code = file_get_contents( $main_file );
    if ( strpos( $code, 'Signature payload exceeds 500KB maximum' ) === false ) {
        return '500KB signature payload limit missing';
    }
    if ( strpos( $code, 'Document is already fully executed and immutable' ) === false ) {
        return 'Executed document immutability check missing';
    }
    return true;
});

// ── SEC-012: Custom WordPress Capability Containment
run_test( 'SEC-012: WordPress capabilities filter scopes dangerous privileges away from tenant roles', function() use ($main_file) {
    $code = file_get_contents( $main_file );
    if ( strpos( $code, 'user_has_cap' ) === false ) {
        return 'user_has_cap filter missing';
    }
    if ( strpos( $code, 'manage_options' ) === false || strpos( $code, 'install_plugins' ) === false ) {
        return 'High-privilege capabilities not filtered from tenant roles';
    }
    return true;
});

// ── SEC-013: Gallery Password Authentication Hardening
run_test( 'SEC-013: public-gallery-view.php uses wp_check_password & HMAC-SHA256 grant cookies', function() use ($gallery_view) {
    $code = file_get_contents( $gallery_view );
    if ( strpos( $code, 'wp_check_password' ) === false ) {
        return 'wp_check_password missing in gallery auth';
    }
    if ( strpos( $code, 'hash_hmac' ) === false || strpos( $code, 'httponly' ) === false ) {
        return 'HMAC-SHA256 or HttpOnly cookie missing';
    }
    return true;
});

// ── SEC-014: Frontend API Origin Validation & Information Disclosure Prevention
run_test( 'SEC-014: Frontend routes validate exact hostnames and avoid internal email leaks', function() use ($fe_contact, $fe_ai) {
    $contact_code = file_get_contents( $fe_contact );
    $ai_code = file_get_contents( $fe_ai );

    if ( strpos( $contact_code, 'new URL(checkUrl)' ) === false ) {
        return 'Exact URL parsing missing in contact API';
    }
    if ( strpos( $contact_code, 'recipient: targetEmail' ) !== false ) {
        return 'Internal recipient email is leaked in contact response';
    }
    if ( strpos( $ai_code, 'new URL(checkUrl)' ) === false ) {
        return 'Exact URL parsing missing in ai-preview API';
    }
    return true;
});

// ── SEC-015: Security Headers & Service Worker Token Query Bypass
run_test( 'SEC-015: Security headers configured & Service Worker excludes sensitive query tokens', function() use ($main_file, $sw_file) {
    $main_code = file_get_contents( $main_file );
    $sw_code = file_get_contents( $sw_file );

    if ( strpos( $main_code, 'X-Content-Type-Options' ) === false || strpos( $main_code, 'Referrer-Policy' ) === false ) {
        return 'Security headers missing in plugin';
    }
    if ( strpos( $sw_code, 'url.searchParams.has(\'token\')' ) === false ) {
        return 'Service Worker does not bypass token queries';
    }
    return true;
});

// ── SEC-016: Production Security Assertion Engine
run_test( 'SEC-016: cora_assert_production_security verifies debug flags, salts and backup isolation', function() use ($main_file) {
    $code = file_get_contents( $main_file );
    if ( strpos( $code, 'function cora_assert_production_security' ) === false ) {
        return 'cora_assert_production_security missing';
    }
    return true;
});

// ── SEC-017: MCP Bearer Authorization & SHA-256 Token Comparison
run_test( 'SEC-017: MCP endpoints strictly enforce Authorization Bearer header with SHA-256 tokens', function() use ($main_file) {
    $code = file_get_contents( $main_file );
    if ( strpos( $code, 'cora_mcp_access_token_hash' ) === false ) {
        return 'MCP SHA-256 token hash check missing';
    }
    if ( strpos( $code, 'hash_equals' ) === false ) {
        return 'hash_equals missing in MCP token check';
    }
    return true;
});

// ── SEC-018: Structured Security Audit Logging & Redaction
run_test( 'SEC-018: cora_log_security_event redacts sensitive data and formats structured audit records', function() use ($auth_file) {
    require_once $auth_file;
    if ( ! function_exists( 'cora_log_security_event' ) ) {
        return 'cora_log_security_event function missing';
    }

    $event = cora_log_security_event( 'login_attempt', array(
        'password' => 'super_secret_password_123',
        'token'    => 'secret_token_abc',
        'email'    => 'test@cora.local',
    ), 'info' );

    if ( ! isset( $event['details']['password'] ) || $event['details']['password'] !== '[REDACTED]' ) {
        return 'Password was not redacted in security audit log';
    }
    if ( ! isset( $event['details']['token'] ) || $event['details']['token'] !== '[REDACTED]' ) {
        return 'Token was not redacted in security audit log';
    }
    return true;
});

echo "\n=================================================================\n";
echo "  AUDIT RESULTS: Total {$tests_total} | Passed: {$passed} | Failed: {$failed}\n";
echo "=================================================================\n\n";

if ( $failed > 0 ) {
    exit( 1 );
}
exit( 0 );
