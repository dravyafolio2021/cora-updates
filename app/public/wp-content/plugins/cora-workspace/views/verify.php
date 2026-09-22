<?php
/**
 * Account Verification Route & Fallback Handler
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Attempt immediate verification if tokens are present
if ( function_exists( 'cora_workspace_handle_email_verification' ) ) {
    cora_workspace_handle_email_verification();
}

$error_type = sanitize_text_field( $_GET['error'] ?? '' );
$is_verified = ( isset( $_GET['verified'] ) && $_GET['verified'] === '1' ) || ( isset( $_GET['cora_verified'] ) && $_GET['cora_verified'] === 'true' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cora — Account Verification</title>
    <link rel="manifest" href="/cora-manifest.json?v=<?php echo defined('CORA_WORKSPACE_VERSION') ? CORA_WORKSPACE_VERSION : '4.0.0'; ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url( function_exists('cora_get_site_icon_asset_url') ? cora_get_site_icon_asset_url('192') : ( CORA_WORKSPACE_URL . 'assets/pwa/icon_192.png' ) ); ?>">
    <style>
        :root {
            color-scheme: only light !important;
            --bg-color: #fafafa;
            --card-bg: #ffffff;
            --text-primary: #18181b;
            --text-secondary: #52525b;
            --border-color: #e4e4e7;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 36px 32px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            text-align: center;
            box-sizing: border-box;
        }
        .brand {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #71717a;
            margin-bottom: 20px;
        }
        .icon-tile {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f4f4f5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        h2 {
            margin: 0 0 8px;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        p {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
            margin: 0 0 24px;
        }
        .btn {
            display: inline-block;
            width: 100%;
            padding: 11px 16px;
            font-size: 13px;
            font-weight: 700;
            background: #18181b;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            box-sizing: border-box;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background: #27272a;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">Cora Workspace</div>
        
        <?php if ( $is_verified || is_user_logged_in() ) : ?>
            <div class="icon-tile">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="#10b981" stroke-width="2.2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <h2>Email Verified!</h2>
            <p>Your workspace account has been verified. You can now access your business dashboard.</p>
            <a href="<?php echo esc_url( home_url( '/workspace/dashboard' ) ); ?>" class="btn">Go to Dashboard →</a>
        <?php elseif ( $error_type === 'verification_invalid' ) : ?>
            <div class="icon-tile">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="#ef4444" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <h2>Link Expired or Invalid</h2>
            <p>This verification link may have already been used or expired. Please sign in or request a new verification link.</p>
            <a href="<?php echo esc_url( home_url( '/workspace/login' ) ); ?>" class="btn">Go to Sign In →</a>
        <?php else : ?>
            <div class="icon-tile">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="#71717a" stroke-width="2.2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            </div>
            <h2>Check Your Inbox</h2>
            <p>We've sent a verification link to your email. Click the button inside the email to activate your workspace.</p>
            <a href="<?php echo esc_url( home_url( '/workspace/login' ) ); ?>" class="btn">Return to Sign In →</a>
        <?php endif; ?>
    </div>
</body>
</html>
