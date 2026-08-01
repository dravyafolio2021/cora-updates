<?php
/**
 * Cora Workspace — Multi-Step Onboarding Wizard
 *
 * 4-step guided flow:
 *   Step 1: Authentication (Google OAuth / Email+Password)
 *   Step 2: Business Details (name, phone, contact email)
 *   Step 3: Industry Selection (Real Estate / Photography / Custom)
 *   Step 4: Workspace Activation (auto-configure + redirect)
 *
 * @package CoraWorkspace
 * @since   2.9.6
 */

// Determine initial state for step routing
$is_logged_in       = is_user_logged_in();
$current_user_id    = $is_logged_in ? get_current_user_id() : 0;
$onboarding_done    = $is_logged_in ? get_user_meta( $current_user_id, 'cora_onboarding_completed', true ) : '';
$has_business       = $is_logged_in ? get_user_meta( $current_user_id, 'cora_workspace_agency_name', true ) : '';
$has_industry       = $is_logged_in ? get_user_meta( $current_user_id, 'cora_onboarding_industry_selected', true ) : '';
$user_email         = $is_logged_in ? wp_get_current_user()->user_email : '';
$user_display_name  = $is_logged_in ? wp_get_current_user()->display_name : '';

// Google OAuth config
$google_enabled  = ( get_option( 'cora_onboarding_google_enabled', 1 ) && ! empty( get_option( 'cora_google_client_id', '' ) ) ) || cora_is_local_environment();
$email_enabled   = get_option( 'cora_onboarding_email_enabled', 1 );
$reg_enabled     = get_option( 'cora_onboarding_enabled', 1 );
$google_auth_url = home_url( '/workspace/auth/google' );

// Determine starting step
$url_step = isset( $_GET['step'] ) ? intval( $_GET['step'] ) : 0;
if ( $url_step >= 2 && $url_step <= 4 && $is_logged_in ) {
    $initial_step = $url_step;
} elseif ( $is_logged_in && ! empty( $has_business ) && empty( $has_industry ) ) {
    $initial_step = 3;
} elseif ( $is_logged_in && empty( $has_business ) ) {
    $initial_step = 2;
} elseif ( $is_logged_in && $onboarding_done === '1' ) {
    $initial_step = 4; // Will redirect
} else {
    $initial_step = 1;
}

$nonce = wp_create_nonce( 'cora_onboarding_nonce' );
$login_nonce = wp_create_nonce( 'cora_login_nonce' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora — Get Started</title>
    <meta name="description" content="Create your Cora workspace. Set up your business in minutes with our guided onboarding.">
    <style>
        :root {
            --bg-color: #fcfcfc;
            --card-bg: #ffffff;
            --text-primary: #18181b;
            --text-secondary: #52525b;
            --text-tertiary: #a1a1aa;
            --border-color: #e4e4e7;
            --border-light: #f4f4f5;
            --input-bg: #ffffff;
            --input-hover-bg: #fafafa;
            --btn-bg: #18181b;
            --btn-hover: #27272a;
            --btn-text: #ffffff;
            --accent: #10b981;
            --accent-bg: #ecfdf5;
            --divider-color: #f0f0f0;
            --card-hover-border: #a1a1aa;
            --step-inactive: #e4e4e7;
            --step-active: #18181b;
            --step-done: #10b981;
        }

        .cora-dark-theme {
            --bg-color: #09090b;
            --card-bg: #111113;
            --text-primary: #f4f4f5;
            --text-secondary: #a1a1aa;
            --text-tertiary: #71717a;
            --border-color: #27272a;
            --border-light: #1c1c1e;
            --input-bg: #18181b;
            --input-hover-bg: #1c1c1e;
            --btn-bg: #f4f4f5;
            --btn-hover: #e4e4e7;
            --btn-text: #09090b;
            --accent: #10b981;
            --accent-bg: rgba(16,185,129,0.08);
            --divider-color: #27272a;
            --card-hover-border: #52525b;
            --step-inactive: #27272a;
            --step-active: #f4f4f5;
            --step-done: #10b981;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
            transition: background-color 0.3s, color 0.3s;
            overflow-x: hidden;
        }

        /* ── Layout Container ──────────────────────────────────── */
        #onboarding-shell {
            width: 100%;
            max-width: 460px;
            position: relative;
        }

        /* ── Step Progress Bar ─────────────────────────────────── */
        .step-progress {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 32px;
            padding: 0 20px;
        }

        .step-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--step-inactive);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            position: relative;
        }

        .step-dot.active {
            background: var(--step-active);
            width: 12px;
            height: 12px;
            box-shadow: 0 0 0 4px rgba(24,24,27,0.06);
        }

        .cora-dark-theme .step-dot.active {
            box-shadow: 0 0 0 4px rgba(244,244,245,0.06);
        }

        .step-dot.done {
            background: var(--step-done);
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: var(--step-inactive);
            max-width: 48px;
            transition: background 0.4s ease;
        }

        .step-line.done {
            background: var(--step-done);
        }

        /* ── Card ──────────────────────────────────────────────── */
        .ob-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }

        /* ── Steps Container ───────────────────────────────────── */
        .steps-viewport {
            position: relative;
            overflow: hidden;
        }

        .step-panel {
            display: none;
            animation: fadeSlideIn 0.35s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .step-panel.active {
            display: block;
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateX(24px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ── Wordmark ──────────────────────────────────────────── */
        .cora-wordmark {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-secondary);
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cora-wordmark svg { flex-shrink: 0; }

        /* ── Typography ────────────────────────────────────────── */
        .ob-title {
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.25;
        }

        .ob-subtitle {
            margin: 0 0 28px;
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* ── Google Button ─────────────────────────────────────── */
        .google-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 11px 14px;
            background: var(--card-bg);
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            position: relative;
        }

        .google-btn:hover {
            background: var(--input-hover-bg);
            border-color: var(--card-hover-border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .google-pill {
            position: absolute;
            top: -8px;
            right: 12px;
            background: var(--accent-bg);
            color: var(--accent);
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.05em;
            border: 1px solid rgba(16,185,129,0.2);
        }

        /* ── Divider ───────────────────────────────────────────── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: var(--text-tertiary);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        /* ── Form Elements ─────────────────────────────────────── */
        .form-group {
            margin-bottom: 16px;
            position: relative;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            color: var(--text-secondary);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        input:focus {
            border-color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(24,24,27,0.07);
        }

        .cora-dark-theme input:focus {
            box-shadow: 0 0 0 3px rgba(244,244,245,0.05);
        }

        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 31px;
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .pw-toggle:hover { color: var(--text-primary); }

        /* ── Buttons ───────────────────────────────────────────── */
        .submit-btn {
            width: 100%;
            padding: 11px;
            background: var(--btn-bg);
            color: var(--btn-text);
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s, opacity 0.2s;
            margin-top: 4px;
            font-family: inherit;
            letter-spacing: -0.01em;
        }

        .submit-btn:hover { background: var(--btn-hover); }
        .submit-btn:active { transform: scale(0.98); }
        .submit-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        /* ── Footer Links ──────────────────────────────────────── */
        .footer-link {
            margin-top: 22px;
            text-align: center;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .footer-link a {
            color: var(--text-primary);
            font-weight: 700;
            text-decoration: none;
        }

        .footer-link a:hover { text-decoration: underline; }

        /* ── Inbox State (Email Verify) ────────────────────────── */
        #inbox-state {
            display: none;
            text-align: center;
        }

        .inbox-icon {
            width: 56px;
            height: 56px;
            background: var(--bg-color);
            border: 1.5px solid var(--border-color);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .inbox-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }

        .inbox-sub {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .inbox-sub strong { color: var(--text-primary); }

        .resend-btn {
            background: none;
            border: none;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            text-decoration: underline;
            font-family: inherit;
            padding: 0;
        }

        .resend-btn:hover { color: var(--text-primary); }

        /* ── Industry Cards (Step 3) ───────────────────────────── */
        .industry-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .industry-card {
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            padding: 18px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            position: relative;
        }

        .industry-card:hover {
            border-color: var(--card-hover-border);
            background: var(--input-hover-bg);
        }

        .industry-card.selected {
            border-color: var(--text-primary);
            background: var(--input-hover-bg);
        }

        .industry-card.selected::after {
            content: '';
            position: absolute;
            top: 12px;
            right: 14px;
            width: 8px;
            height: 8px;
            background: var(--accent);
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
        }

        .industry-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--text-secondary);
        }

        .industry-info { flex: 1; min-width: 0; }

        .industry-name {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 3px;
        }

        .industry-desc {
            font-size: 11.5px;
            color: var(--text-secondary);
            line-height: 1.45;
        }

        /* ── Activation State (Step 4) ─────────────────────────── */
        .activation-state {
            text-align: center;
            padding: 20px 0;
        }

        .activation-spinner {
            width: 48px;
            height: 48px;
            border: 3px solid var(--border-color);
            border-top-color: var(--text-primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 24px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .activation-check {
            width: 48px;
            height: 48px;
            background: var(--accent);
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .activation-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }

        .activation-sub {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* ── Toast System ──────────────────────────────────────── */
        #cora-toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column-reverse;
            gap: 8px;
        }

        .cora-toast {
            background: var(--btn-bg);
            color: var(--btn-text);
            border: 1px solid var(--border-color);
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            animation: toastSlideIn 0.25s ease-out;
            max-width: 340px;
        }

        .cora-toast a { color: inherit; font-weight: 700; }

        @keyframes toastSlideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* ── Helper ─────────────────────────────────────────────── */
        .helper-text {
            font-size: 11px;
            color: var(--text-tertiary);
            margin-top: 4px;
        }

        .step-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-tertiary);
            margin-bottom: 14px;
        }

        /* ── Responsive ────────────────────────────────────────── */
        @media (max-width: 480px) {
            .ob-card { padding: 28px 20px; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
            #onboarding-shell { max-width: 100%; }
        }
    </style>
</head>
<body>

<div id="onboarding-shell">
    <!-- Step Progress -->
    <div class="step-progress" id="step-progress">
        <div class="step-dot" data-step="1"></div>
        <div class="step-line" data-line="1"></div>
        <div class="step-dot" data-step="2"></div>
        <div class="step-line" data-line="2"></div>
        <div class="step-dot" data-step="3"></div>
        <div class="step-line" data-line="3"></div>
        <div class="step-dot" data-step="4"></div>
    </div>

    <div class="ob-card">
        <div class="cora-wordmark">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <rect width="14" height="14" rx="3.5" fill="currentColor" opacity="0.12"/>
                <rect x="3" y="3" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                <rect x="7.5" y="3" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                <rect x="3" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                <rect x="7.5" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor"/>
            </svg>
            Cora Platform
        </div>

        <div class="steps-viewport">

            <!-- ═══ STEP 1 — AUTHENTICATION ═══ -->
            <div class="step-panel" id="step-1" data-step="1">
                <div class="step-label">Step 1 of 4</div>
                <h2 class="ob-title">Create your workspace</h2>
                <p class="ob-subtitle">Get started in under 2 minutes. No credit card required.</p>

                <?php if ( ! $reg_enabled ) : ?>
                <div style="text-align:center; padding: 24px 0; color: var(--text-secondary); font-size:13px;">
                    <p>Registration is currently closed. Please contact your administrator.</p>
                    <a href="<?php echo esc_url( home_url( '/workspace/login' ) ); ?>" style="color:var(--text-primary);font-weight:700;font-size:12px;">← Back to sign in</a>
                </div>
                <?php else : ?>

                <div id="reg-form-state">
                    <?php if ( $google_enabled ) : ?>
                    <a href="<?php echo esc_url( $google_auth_url ); ?>" class="google-btn" id="google-btn">
                        <span class="google-pill">RECOMMENDED</span>
                        <svg width="18" height="18" viewBox="0 0 48 48" fill="none">
                            <path d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" fill="#FFC107"/>
                            <path d="M6.306,14.691l6.571,4.819C14.655,15.108,19.003,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" fill="#FF3D00"/>
                            <path d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z" fill="#4CAF50"/>
                            <path d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z" fill="#1976D2"/>
                        </svg>
                        Continue with Google
                    </a>
                    <?php endif; ?>

                    <?php if ( $google_enabled && $email_enabled ) : ?>
                    <div class="divider">or continue with email</div>
                    <?php endif; ?>

                    <?php if ( $email_enabled ) : ?>
                    <form id="onboarding-register-form" onsubmit="handleRegisterSubmit(event)" autocomplete="off">
                        <div class="form-group">
                            <label for="ob-email">Email Address</label>
                            <input type="email" id="ob-email" required placeholder="jane@company.com" autocomplete="email">
                        </div>
                        <div class="form-group">
                            <label for="ob-password">Password</label>
                            <input type="password" id="ob-password" required placeholder="Min. 8 characters">
                            <button type="button" class="pw-toggle" onclick="togglePw('ob-password','eye-1-open','eye-1-closed')">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-1-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-1-closed" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        <button type="submit" class="submit-btn" id="ob-register-btn">Create Account</button>
                        
                        <div style="margin-top:16px; text-align:center; font-size:12px;">
                            <a href="#" onclick="toggleObAuthMode('magic', event)" style="color:var(--text-secondary);text-decoration:none;font-weight:600;">Sign up with Magic Link →</a>
                        </div>
                    </form>

                    <form id="onboarding-magic-form" onsubmit="handleObMagicSubmit(event)" style="display:none;" autocomplete="off">
                        <div class="form-group">
                            <label for="ob-magic-email">Email Address</label>
                            <input type="email" id="ob-magic-email" required placeholder="jane@company.com" autocomplete="email">
                        </div>
                        <button type="submit" class="submit-btn" id="ob-magic-btn">Send Magic Link</button>
                        
                        <div style="margin-top:16px; text-align:center; font-size:12px;">
                            <a href="#" onclick="toggleObAuthMode('password', event)" style="color:var(--text-secondary);text-decoration:none;font-weight:600;">← Sign up with Password</a>
                        </div>
                    </form>
                    <?php endif; ?>

                    <div class="footer-link">
                        Already have an account? <a href="<?php echo esc_url( home_url( '/workspace/login' ) ); ?>">Sign in</a>
                    </div>
                </div>

                <!-- Check Inbox State -->
                <div id="inbox-state">
                    <div class="inbox-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div class="inbox-title">Check your inbox</div>
                    <p class="inbox-sub">We sent a verification link to <strong id="inbox-email"></strong>. Click the link to verify your email and continue setup.</p>
                    <p style="font-size:12px; color:var(--text-secondary); margin-bottom:6px;">Didn't receive it?</p>
                    <button class="resend-btn" onclick="handleResend()">Resend verification email</button>
                    <div class="footer-link" style="margin-top:28px;">
                        <a href="<?php echo esc_url( home_url( '/workspace/login' ) ); ?>">← Back to sign in</a>
                    </div>
                </div>

                <?php endif; ?>
            </div>

            <!-- ═══ STEP 2 — BUSINESS DETAILS ═══ -->
            <div class="step-panel" id="step-2" data-step="2">
                <div class="step-label">Step 2 of 4</div>
                <h2 class="ob-title">Tell us about your business</h2>
                <p class="ob-subtitle">This helps us personalize your workspace experience.</p>

                <form id="onboarding-business-form" onsubmit="handleBusinessSubmit(event)" autocomplete="off">
                    <div class="form-group">
                        <label for="ob-full-name">Your Full Name</label>
                        <input type="text" id="ob-full-name" required placeholder="e.g. Jane Smith" value="<?php echo esc_attr( $user_display_name ); ?>">
                    </div>
                    <div class="form-group">
                        <label for="ob-business-name">Business Name</label>
                        <input type="text" id="ob-business-name" required placeholder="e.g. Skyline Realty, Studio Light" value="<?php echo esc_attr( $has_business ); ?>">
                    </div>
                    <div class="form-group">
                        <label for="ob-phone">Phone / WhatsApp <span style="font-weight:500;text-transform:none;letter-spacing:0;color:var(--text-tertiary);">(optional)</span></label>
                        <input type="tel" id="ob-phone" placeholder="+91 98765 43210">
                        <div class="helper-text">Used for client communication & WhatsApp integration</div>
                    </div>
                    <div class="form-group">
                        <label for="ob-contact-email">Contact Email</label>
                        <input type="email" id="ob-contact-email" placeholder="hello@yourbusiness.com" value="<?php echo esc_attr( $user_email ); ?>">
                        <div class="helper-text">Public email for your business profile</div>
                    </div>
                    <button type="submit" class="submit-btn" id="ob-business-btn">Continue</button>
                </form>
            </div>

            <!-- ═══ STEP 3 — INDUSTRY SELECTION ═══ -->
            <div class="step-panel" id="step-3" data-step="3">
                <div class="step-label">Step 3 of 4</div>
                <h2 class="ob-title">What's your industry?</h2>
                <p class="ob-subtitle">We'll configure your workspace with the right tools, CRM pipeline, and team roles.</p>

                <div class="industry-grid" id="industry-grid">
                    <div class="industry-card" data-industry="real_estate" onclick="selectIndustry(this)">
                        <div class="industry-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                        <div class="industry-info">
                            <div class="industry-name">Real Estate Agency</div>
                            <div class="industry-desc">Property listings, buyer leads, CRM pipeline, showing scheduler</div>
                        </div>
                    </div>

                    <div class="industry-card" data-industry="photography_studio" onclick="selectIndustry(this)">
                        <div class="industry-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                <circle cx="12" cy="13" r="4"/>
                            </svg>
                        </div>
                        <div class="industry-info">
                            <div class="industry-name">Photography Studio</div>
                            <div class="industry-desc">Client leads, shoot scheduling, equipment tracking, team roles</div>
                        </div>
                    </div>

                    <div class="industry-card" data-industry="custom" onclick="selectIndustry(this)">
                        <div class="industry-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        </div>
                        <div class="industry-info">
                            <div class="industry-name">Custom / Self-Managed</div>
                            <div class="industry-desc">Access all features from every industry. Configure your own workflow.</div>
                        </div>
                    </div>
                </div>

                <button type="button" class="submit-btn" id="ob-industry-btn" onclick="handleIndustrySubmit()" disabled>Continue</button>
            </div>

            <!-- ═══ STEP 4 — ACTIVATION ═══ -->
            <div class="step-panel" id="step-4" data-step="4">
                <div class="activation-state" id="activation-state">
                    <div class="activation-spinner" id="activation-spinner"></div>
                    <div class="activation-check" id="activation-check">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div class="activation-title" id="activation-title">Setting up your workspace…</div>
                    <p class="activation-sub" id="activation-sub">Configuring features, CRM pipeline, and team roles for your industry.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="cora-toast-container"></div>

<script>
(function() {
    'use strict';

    // ── Theme Detection ───────────────────────────────────────
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.body.classList.add('cora-dark-theme');
    }

    // ── State ─────────────────────────────────────────────────
    var currentStep = <?php echo intval( $initial_step ); ?>;
    var selectedIndustry = '';
    var registeredEmail = '';
    var ajaxUrl = '<?php echo admin_url( "admin-ajax.php" ); ?>';
    var nonce = '<?php echo esc_js( $nonce ); ?>';
    var loginNonce = '<?php echo esc_js( $login_nonce ); ?>';
    var isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    // ── Initialize ────────────────────────────────────────────
    updateStepProgress(currentStep);
    showStep(currentStep);

    // Handle URL error params (from Google OAuth)
    var params = new URLSearchParams(window.location.search);
    var error = params.get('error');
    if (error) {
        var errorMsgs = {
            'google_disabled'    : 'Google sign-in is not available.',
            'oauth_state'        : 'OAuth security check failed. Please try again.',
            'oauth_token'        : 'Could not connect to Google. Please try again.',
            'user_create'        : 'Failed to create account. Please try email registration.',
            'registration_closed': 'New registrations are currently closed.'
        };
        if (errorMsgs[error]) showToast(errorMsgs[error]);
    }

    // ── Step Navigation ───────────────────────────────────────
    function showStep(step) {
        var panels = document.querySelectorAll('.step-panel');
        panels.forEach(function(p) { p.classList.remove('active'); });
        var target = document.getElementById('step-' + step);
        if (target) {
            target.classList.add('active');
        }
        currentStep = step;
        updateStepProgress(step);

        if (step === 4 && selectedIndustry) {
            triggerActivation();
        }
    }

    function updateStepProgress(step) {
        var dots = document.querySelectorAll('.step-dot');
        var lines = document.querySelectorAll('.step-line');

        dots.forEach(function(dot) {
            var dotStep = parseInt(dot.getAttribute('data-step'));
            dot.classList.remove('active', 'done');
            if (dotStep < step) dot.classList.add('done');
            else if (dotStep === step) dot.classList.add('active');
        });

        lines.forEach(function(line) {
            var lineStep = parseInt(line.getAttribute('data-line'));
            line.classList.remove('done');
            if (lineStep < step) line.classList.add('done');
        });
    }

    // ── Toast ─────────────────────────────────────────────────
    function showToast(msg) {
        var container = document.getElementById('cora-toast-container');
        var toast = document.createElement('div');
        toast.className = 'cora-toast';
        toast.innerHTML = msg;
        container.appendChild(toast);
        setTimeout(function() {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(function() { toast.remove(); }, 300);
        }, 4500);
    }
    window.coraShowToast = showToast;

    // ── Password Toggle ───────────────────────────────────────
    window.togglePw = function(inputId, openId, closedId) {
        var input = document.getElementById(inputId);
        var open = document.getElementById(openId);
        var closed = document.getElementById(closedId);
        if (input.type === 'password') {
            input.type = 'text';
            open.style.display = 'none';
            closed.style.display = 'block';
        } else {
            input.type = 'password';
            open.style.display = 'block';
            closed.style.display = 'none';
        }
    };

    // ═══ STEP 1 — REGISTRATION ═══════════════════════════════

    window.handleRegisterSubmit = function(e) {
        e.preventDefault();

        var email    = document.getElementById('ob-email').value.trim();
        var password = document.getElementById('ob-password').value;

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showToast('Please enter a valid email address.'); return; }
        if (password.length < 8) { showToast('Password must be at least 8 characters.'); return; }

        var btn = document.getElementById('ob-register-btn');
        btn.disabled = true;
        btn.textContent = 'Creating account\u2026';

        var formData = new FormData();
        formData.append('action', 'cora_self_register');
        formData.append('name', '');
        formData.append('agency', '');
        formData.append('email', email);
        formData.append('industry', 'real_estate');
        formData.append('password', password);
        formData.append('confirm', password);
        formData.append('nonce', loginNonce);

        fetch(ajaxUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                showInboxState(email, res.data.dev_verify_url);
            } else {
                showToast(res.data.message || 'Something went wrong. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Create Account';
            }
        })
        .catch(function() {
            showToast('Network error. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Create Account';
        });
    };

    window.toggleObAuthMode = function(mode, e) {
        if (e) e.preventDefault();
        var regForm = document.getElementById('onboarding-register-form');
        var magicForm = document.getElementById('onboarding-magic-form');
        if (mode === 'magic') {
            regForm.style.display = 'none';
            magicForm.style.display = 'block';
        } else {
            regForm.style.display = 'block';
            magicForm.style.display = 'none';
        }
    };

    window.handleObMagicSubmit = function(e) {
        e.preventDefault();
        var email = document.getElementById('ob-magic-email').value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showToast('Please enter a valid email address.');
            return;
        }

        var btn = document.getElementById('ob-magic-btn');
        btn.disabled = true;
        btn.textContent = 'Sending link\u2026';

        var formData = new FormData();
        formData.append('action', 'cora_request_magic_link');
        formData.append('email', email);
        formData.append('nonce', loginNonce);

        fetch(ajaxUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                showToast(res.data.message);
                if (res.data.dev_magic_url) {
                    // Dev Mode: Show mock magic link so they can click it!
                    var oldDev = document.getElementById('ob-dev-magic-notice');
                    if (oldDev) oldDev.remove();

                    var devDiv = document.createElement('div');
                    devDiv.id = 'ob-dev-magic-notice';
                    devDiv.style.marginTop = '16px';
                    devDiv.style.padding = '12px';
                    devDiv.style.background = '#fef08a';
                    devDiv.style.color = '#854d0e';
                    devDiv.style.border = '1px solid #fef08a';
                    devDiv.style.borderRadius = '8px';
                    devDiv.style.fontSize = '11.5px';
                    devDiv.style.textAlign = 'left';
                    devDiv.style.lineHeight = '1.4';
                    devDiv.innerHTML = '<strong>[Dev Mode] Magic Link generated:</strong><br><a href="' + res.data.dev_magic_url + '" style="color:#854d0e; font-weight:700; text-decoration:underline;">Click here to simulate magic link login →</a>';
                    document.getElementById('onboarding-magic-form').appendChild(devDiv);
                }
            } else {
                showToast(res.data.message);
                btn.disabled = false;
                btn.textContent = 'Send Magic Link';
            }
        })
        .catch(function() {
            showToast('Network error. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Send Magic Link';
        });
    };

    function showInboxState(email, devVerifyUrl) {
        registeredEmail = email;
        document.getElementById('reg-form-state').style.display = 'none';
        document.getElementById('inbox-state').style.display = 'block';
        document.getElementById('inbox-email').textContent = email;
        
        var oldNotice = document.getElementById('dev-notice');
        if (oldNotice) oldNotice.remove();

        if (devVerifyUrl) {
            var inboxState = document.getElementById('inbox-state');
            var devDiv = document.createElement('div');
            devDiv.id = 'dev-notice';
            devDiv.style.marginTop = '20px';
            devDiv.style.padding = '12px';
            devDiv.style.background = '#fef08a';
            devDiv.style.color = '#854d0e';
            devDiv.style.border = '1px solid #fef08a';
            devDiv.style.borderRadius = '8px';
            devDiv.style.fontSize = '11.5px';
            devDiv.style.textAlign = 'left';
            devDiv.style.lineHeight = '1.4';
            devDiv.innerHTML = '<strong>[Dev Mode] Email verification URL:</strong><br><a href="' + devVerifyUrl + '" style="color:#854d0e; font-weight:700; text-decoration:underline;">Click here to simulate verification & verify email →</a>';
            inboxState.appendChild(devDiv);
        }
    }

    window.handleResend = function() {
        if (!registeredEmail) return;
        showToast('Sending verification link\u2026');
        var formData = new FormData();
        formData.append('action', 'cora_ajax_resend_verification');
        formData.append('email', registeredEmail);
        formData.append('nonce', loginNonce);
        fetch(ajaxUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) { showToast(res.data.message || 'Verification link sent!'); });
    };

    // ═══ STEP 2 — BUSINESS DETAILS ═══════════════════════════

    window.handleBusinessSubmit = function(e) {
        e.preventDefault();

        var fullName      = document.getElementById('ob-full-name').value.trim();
        var businessName  = document.getElementById('ob-business-name').value.trim();
        var phone         = document.getElementById('ob-phone').value.trim();
        var contactEmail  = document.getElementById('ob-contact-email').value.trim();

        if (!fullName) { showToast('Please enter your full name.'); return; }
        if (!businessName) { showToast('Please enter your business name.'); return; }

        if (phone) {
            var phoneClean = phone.replace(/[\s\-\+\(\)]/g, '');
            if (!/^\d{7,15}$/.test(phoneClean)) {
                showToast('Please enter a valid phone number (7 to 15 digits).');
                return;
            }
        }

        if (contactEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contactEmail)) {
            showToast('Please enter a valid contact email address.');
            return;
        }

        var btn = document.getElementById('ob-business-btn');
        btn.disabled = true;
        btn.textContent = 'Saving\u2026';

        var formData = new FormData();
        formData.append('action', 'cora_onboarding_save_business');
        formData.append('full_name', fullName);
        formData.append('business_name', businessName);
        formData.append('phone', phone);
        formData.append('contact_email', contactEmail);
        formData.append('nonce', nonce);

        fetch(ajaxUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                showStep(3);
            } else {
                showToast(res.data.message || 'Failed to save. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Continue';
            }
        })
        .catch(function() {
            showToast('Network error. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Continue';
        });
    };

    // ═══ STEP 3 — INDUSTRY SELECTION ═════════════════════════

    window.selectIndustry = function(card) {
        var cards = document.querySelectorAll('.industry-card');
        cards.forEach(function(c) { c.classList.remove('selected'); });
        card.classList.add('selected');
        selectedIndustry = card.getAttribute('data-industry');
        document.getElementById('ob-industry-btn').disabled = false;
    };

    window.handleIndustrySubmit = function() {
        if (!selectedIndustry) {
            showToast('Please select an industry.');
            return;
        }
        showStep(4);
    };

    // ═══ STEP 4 — ACTIVATION ═════════════════════════════════

    function triggerActivation() {
        var formData = new FormData();
        formData.append('action', 'cora_onboarding_activate_workspace');
        formData.append('industry', selectedIndustry);
        formData.append('nonce', nonce);

        fetch(ajaxUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                document.getElementById('activation-spinner').style.display = 'none';
                var check = document.getElementById('activation-check');
                check.style.display = 'flex';
                document.getElementById('activation-title').textContent = 'Your workspace is ready!';
                document.getElementById('activation-sub').textContent = 'Redirecting you to your dashboard\u2026';

                setTimeout(function() {
                    window.location.href = res.data.redirect_url;
                }, 1500);
            } else {
                showToast(res.data.message || 'Activation failed. Please try again.');
                showStep(3);
            }
        })
        .catch(function() {
            showToast('Network error during activation. Please try again.');
            showStep(3);
        });
    }

})();
</script>
</body>
</html>
