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
if ( $url_step >= 2 && $url_step <= 5 && $is_logged_in ) {
    $initial_step = $url_step;
} elseif ( $is_logged_in && ! empty( $has_business ) && empty( $has_industry ) ) {
    $initial_step = 3;
} elseif ( $is_logged_in && empty( $has_business ) ) {
    $initial_step = 2;
} elseif ( $is_logged_in && $onboarding_done === '1' ) {
    $initial_step = 5; // Will redirect
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
    <meta name="color-scheme" content="light">
    <title>Cora — Get Started</title>
    <meta name="description" content="Create your Cora workspace. Set up your business in minutes with our guided onboarding.">
    <style>
        :root {
            color-scheme: only light !important;
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

        

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            transition: background-color 0.3s, color 0.3s;
            overflow-x: hidden;
        }

        /* ── Layout Wrapper ────────────────────────────────────── */
        #onboarding-page-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        #onboarding-brand-sidebar {
            width: 420px;
            background: linear-gradient(135deg, #e8f7f2 0%, #fbfdfc 100%);
            border-right: 1px solid var(--border-color);
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        

        #onboarding-form-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            overflow-y: auto;
            position: relative;
        }

        /* ── Brand Sidebar Elements ────────────────────────────── */
        .brand-sidebar-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-primary);
            user-select: none;
        }

        .brand-sidebar-content {
            margin-top: auto;
            margin-bottom: auto;
            max-width: 320px;
            z-index: 2;
        }

        .brand-sidebar-title {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.15;
            color: var(--text-primary);
            margin-bottom: 14px;
        }

        .brand-title-accent {
            font-family: Georgia, serif;
            font-style: italic;
            font-weight: 400;
            color: var(--text-primary);
        }

        .brand-sidebar-desc {
            font-size: 13px;
            line-height: 1.6;
            color: var(--text-secondary);
        }

        /* ── CSS-only 3D Scene ─────────────────────────────────── */
        .cora-scene-container {
            position: relative;
            width: 280px;
            height: 200px;
            margin: 40px auto 0 auto;
            perspective: 800px;
            z-index: 1;
        }

        .cora-scene-pedestal {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%) rotateX(65deg);
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.08);
            border: 2.2px dashed rgba(16, 185, 129, 0.22);
            box-shadow: 0 0 24px rgba(16, 185, 129, 0.04);
        }

        .cora-scene-logo-card {
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: 72px;
            height: 72px;
            border-radius: 16px;
            background: #09090b;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 32px rgba(9, 9, 11, 0.15);
            animation: floatLogo 4.2s ease-in-out infinite;
            z-index: 3;
        }

        

        .cora-scene-widget {
            position: absolute;
            background: rgba(255, 255, 255, 0.75);
            border: 1.5px solid rgba(16, 185, 129, 0.2);
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 8px 32px rgba(9, 9, 11, 0.03);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 2;
        }

        

        .widget-left {
            bottom: 80px;
            left: 15px;
            width: 100px;
            height: 64px;
            animation: floatLeftWidget 4.8s ease-in-out infinite;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .widget-right {
            bottom: 70px;
            right: 15px;
            width: 100px;
            height: 64px;
            animation: floatRightWidget 4.0s ease-in-out infinite;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 6px;
        }

        .widget-profile-line {
            height: 4px;
            background: rgba(16, 185, 129, 0.2);
            border-radius: 2px;
        }

        .line-short { width: 35%; }
        .line-medium { width: 65%; }
        .line-long { width: 85%; }

        .widget-chart-bar {
            width: 14px;
            background: rgba(16, 185, 129, 0.25);
            border-radius: 3px 3px 0 0;
        }

        @keyframes floatLogo {
            0%, 100% { transform: translate(-50%, 0px); }
            50% { transform: translate(-50%, -12px); }
        }
        @keyframes floatLeftWidget {
            0%, 100% { transform: translateY(0px) rotate(-4deg); }
            50% { transform: translateY(-9px) rotate(-1deg); }
        }
        @keyframes floatRightWidget {
            0%, 100% { transform: translateY(0px) rotate(4deg); }
            50% { transform: translateY(-8px) rotate(1deg); }
        }

        /* ── Onboarding Shell ──────────────────────────────────── */
        #onboarding-shell {
            width: 100%;
            max-width: 480px;
            transition: max-width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .ob-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 32px rgba(9, 9, 11, 0.03);
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        /* ── Progress Indicator ────────────────────────────────── */
        .step-progress-indicator {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 440px;
            margin: 0 auto 36px auto;
            padding: 0 4px;
        }

        .step-indicator-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-indicator-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-tertiary);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-indicator-label {
            margin-top: 8px;
            font-size: 10px;
            font-weight: 600;
            color: var(--text-tertiary);
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .step-indicator-circle .step-check {
            display: none;
        }

        .step-indicator-line {
            flex: 1;
            height: 2px;
            background: var(--border-color);
            margin: 0 -4px;
            transform: translateY(-16px);
            z-index: 1;
            transition: all 0.3s ease;
        }

        .step-indicator-item.active .step-indicator-circle {
            border-color: var(--text-primary);
            background: var(--text-primary);
            color: var(--card-bg);
            box-shadow: 0 0 0 4px rgba(24,24,27,0.06);
        }

        

        .step-indicator-item.active .step-indicator-label {
            color: var(--text-primary);
            font-weight: 700;
        }

        .step-indicator-item.done .step-indicator-circle {
            border-color: var(--accent);
            background: var(--accent);
            color: #ffffff;
        }

        .step-indicator-item.done .step-indicator-circle .step-num {
            display: none;
        }

        .step-indicator-item.done .step-indicator-circle .step-check {
            display: block;
            color: #ffffff;
        }

        .step-indicator-item.done .step-indicator-label {
            color: var(--text-secondary);
        }

        .step-indicator-line.done {
            background: var(--accent);
        }

        /* ── Typography & Forms ────────────────────────────────── */
        .ob-title {
            margin: 0 0 8px;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.2;
            color: var(--text-primary);
        }

        .ob-subtitle {
            margin: 0 0 28px;
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
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

        /* Input Prefix Styling */
        .input-group-with-icon {
            position: relative;
        }

        .input-group-with-icon .input-icon-prefix {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-group-with-icon input {
            padding-left: 44px !important;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 11px 14px;
            font-size: 14px;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        input:focus {
            border-color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(24,24,27,0.06);
        }

        

        .pw-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            z-index: 5;
        }

        .pw-toggle:hover { color: var(--text-primary); }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: var(--btn-bg);
            color: var(--btn-text);
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s, opacity 0.2s;
            margin-top: 8px;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .submit-btn:hover { background: var(--btn-hover); }
        .submit-btn:active { transform: scale(0.98); }
        .submit-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        .helper-text {
            font-size: 11px;
            color: var(--text-tertiary);
            margin-top: 6px;
        }

        .step-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-tertiary);
            margin-bottom: 8px;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: var(--text-tertiary);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .google-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            background: var(--card-bg);
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
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
            box-shadow: 0 2px 10px rgba(9, 9, 11, 0.04);
        }

        .google-pill {
            position: absolute;
            top: -8px;
            right: 12px;
            background: var(--accent-bg);
            color: var(--accent);
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.05em;
            border: 1px solid rgba(16,185,129,0.18);
        }

        .footer-link {
            margin-top: 24px;
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

        .secure-footer-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 11px;
            color: var(--text-tertiary);
            margin-top: 20px;
            font-weight: 500;
        }

        /* ── Inbox state ───────────────────────────────────────── */
        #inbox-state {
            display: none;
            text-align: center;
        }

        .inbox-icon {
            width: 52px;
            height: 52px;
            background: var(--bg-color);
            border: 1.5px solid var(--border-color);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--text-primary);
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

        /* ── Industry Grid & Cards (Step 3) ────────────────────── */
        .industry-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .industry-card {
            border: 1.5px solid var(--border-color);
            border-radius: 14px;
            padding: 24px 16px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 12px;
            background: var(--card-bg);
            position: relative;
        }

        .industry-card:hover {
            border-color: var(--card-hover-border);
            background: var(--input-hover-bg);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(9, 9, 11, 0.03);
        }

        .industry-card.selected {
            border-color: var(--text-primary);
            background: var(--input-hover-bg);
            box-shadow: 0 6px 20px rgba(9, 9, 11, 0.06);
        }

        .industry-card.selected::after {
            content: '';
            position: absolute;
            top: 12px;
            right: 12px;
            width: 8px;
            height: 8px;
            background: var(--accent);
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.18);
        }

        .industry-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .industry-card.selected .industry-icon {
            background: var(--text-primary);
            color: var(--card-bg);
            border-color: var(--text-primary);
        }

        .industry-name {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--text-primary);
        }

        .industry-desc {
            font-size: 11px;
            color: var(--text-secondary);
            line-height: 1.45;
        }

        .industry-card.locked {
            opacity: 0.55;
            cursor: not-allowed;
            pointer-events: none;
        }

        .coming-soon-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--border-light);
            color: var(--text-secondary);
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2.2px 6px;
            border-radius: 4px;
            letter-spacing: 0.05em;
            border: 1px solid var(--border-color);
        }

        /* ── Activation State (Step 4) ─────────────────────────── */
        .activation-state {
            text-align: center;
            padding: 24px 0;
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
            width: 56px;
            height: 56px;
            background: var(--accent);
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0.1);
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .activation-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .activation-sub {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .secure-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--accent-bg);
            border: 1px solid rgba(16, 185, 129, 0.15);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: var(--accent);
            margin: 24px auto 0 auto;
        }

        /* ── Toast ─────────────────────────────────────────────── */
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

        @keyframes toastSlideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Step Panel Visibility */
        .step-panel {
            display: none;
        }
        .step-panel.active {
            display: block;
        }

        /* ── Responsive media breakpoints ──────────────────────── */
        @media (max-width: 899px) {
            #onboarding-page-container {
                flex-direction: column;
            }
            #onboarding-brand-sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 36px 24px;
                justify-content: center;
                gap: 20px;
            }
            .brand-sidebar-content {
                max-width: 100%;
                margin: 0;
            }
            .cora-scene-container {
                display: none;
            }
            #onboarding-form-area {
                padding: 36px 16px;
            }
            #onboarding-shell {
                max-width: 100% !important;
            }
            .industry-grid {
                grid-template-columns: 1fr;
            }
        }
    }

    /* ── PWA Onboarding Styling ────────────────────────────── */
    .pwa-mockup-container {
        margin: 24px auto;
        width: 100%;
        max-width: 320px;
        display: flex;
        justify-content: center;
        user-select: none;
    }

    .pwa-mockup-frame {
        width: 100%;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .pwa-mockup-frame:hover {
        transform: translateY(-4px);
    }

    .pwa-mockup-header {
        height: 32px;
        background: #fafafa;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        padding: 0 16px;
        gap: 12px;
    }

    .pwa-mockup-dots {
        display: flex;
        gap: 6px;
    }

    .pwa-mockup-dots span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #e4e4e7;
    }

    .pwa-mockup-url {
        font-size: 10px;
        color: var(--text-tertiary);
        font-family: monospace;
        letter-spacing: 0.05em;
    }

    .pwa-mockup-content {
        padding: 32px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 12px;
        background: radial-gradient(circle at 50% 50%, #fafbfc 0%, #ffffff 100%);
    }

    .pwa-mockup-icon-card {
        width: 64px;
        height: 64px;
        background: #18181b;
        color: #ffffff;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 24px rgba(24, 24, 27, 0.15);
        animation: bounceIcon 3s ease-in-out infinite;
    }

    @keyframes bounceIcon {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .pwa-mockup-appname {
        font-size: 14px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }

    .pwa-mockup-badge {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #10b981;
        background: #ecfdf5;
        padding: 4px 8px;
        border-radius: 12px;
        border: 1px solid rgba(16, 185, 129, 0.15);
    }

    .pwa-instructions-box {
        background: #fafafa;
        border: 1px dashed var(--border-color);
        border-radius: 12px;
        padding: 16px;
        margin: 20px 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
        animation: fadeIn 0.3s ease-out;
        text-align: left;
    }

    .pwa-instructions-header {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-secondary);
    }

    .pwa-instructions-text {
        font-size: 11px;
        line-height: 1.5;
        color: var(--text-secondary);
    }

    .skip-btn {
        width: 100%;
        text-align: center;
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        cursor: pointer;
        margin-top: 16px;
        padding: 8px;
        transition: color 0.2s;
    }

    .skip-btn:hover {
        color: var(--text-primary);
    }
</style>
</head>
<body>

<div id="onboarding-page-container">
    <!-- Brand Sidebar -->
    <div id="onboarding-brand-sidebar">
        <div class="brand-sidebar-logo">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="margin-right:2px;">
                <rect width="14" height="14" rx="3.5" fill="currentColor" opacity="0.12"/>
                <rect x="3" y="3" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                <rect x="7.5" y="3" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                <rect x="3" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                <rect x="7.5" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor"/>
            </svg>
            <span>CORA</span> <span style="font-weight:400;color:var(--text-secondary);">PLATFORM</span>
        </div>
        
        <div class="brand-sidebar-content">
            <h1 class="brand-sidebar-title">Let's build <br><span class="brand-title-accent">your workspace</span></h1>
            <p class="brand-sidebar-desc">Tell us a bit about your business so we can personalize your Cora experience.</p>
        </div>
        
        <!-- CSS-only 3D Illustration Scene -->
        <div class="cora-scene-container">
            <div class="cora-scene-pedestal"></div>
            <div class="cora-scene-logo-card">
                <svg width="28" height="28" viewBox="0 0 14 14" fill="none">
                    <rect x="3" y="3" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                    <rect x="7.5" y="3" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                    <rect x="3" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                    <rect x="7.5" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor"/>
                </svg>
            </div>
            <div class="cora-scene-widget widget-left">
                <div class="widget-profile-line line-short"></div>
                <div class="widget-profile-line line-medium"></div>
                <div class="widget-profile-line line-long"></div>
            </div>
            <div class="cora-scene-widget widget-right">
                <div class="widget-chart-bar" style="height: 18px;"></div>
                <div class="widget-chart-bar" style="height: 32px;"></div>
                <div class="widget-chart-bar" style="height: 24px;"></div>
            </div>
        </div>
    </div>
    
    <!-- Form Area -->
    <div id="onboarding-form-area">
        <div id="onboarding-shell">
            <!-- Step Progress -->
            <div class="step-progress-indicator" id="step-progress">
                <div class="step-indicator-item" data-step="1">
                    <div class="step-indicator-circle">
                        <span class="step-num">1</span>
                        <svg class="step-check" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span class="step-indicator-label">Account</span>
                </div>
                <div class="step-indicator-line" data-line="1"></div>
                
                <div class="step-indicator-item" data-step="2">
                    <div class="step-indicator-circle">
                        <span class="step-num">2</span>
                        <svg class="step-check" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span class="step-indicator-label">Profile</span>
                </div>
                <div class="step-indicator-line" data-line="2"></div>
                
                <div class="step-indicator-item" data-step="3">
                    <div class="step-indicator-circle">
                        <span class="step-num">3</span>
                        <svg class="step-check" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span class="step-indicator-label">Industry</span>
                </div>
                <div class="step-indicator-line" data-line="3"></div>
                
                <div class="step-indicator-item" id="step-pwa-indicator" data-step="4">
                    <div class="step-indicator-circle">
                        <span class="step-num">4</span>
                        <svg class="step-check" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span class="step-indicator-label">App</span>
                </div>
                <div class="step-indicator-line" id="step-pwa-line" data-line="4"></div>
                
                <div class="step-indicator-item" data-step="5">
                    <div class="step-indicator-circle">
                        <span class="step-num">5</span>
                        <svg class="step-check" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span class="step-indicator-label">Finish</span>
                </div>
            </div>

            <div class="ob-card">
                <div class="cora-wordmark" style="display:none;">
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
                            <div class="input-group-with-icon">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <input type="email" id="ob-email" required placeholder="jane@company.com" autocomplete="email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ob-password">Password</label>
                            <div class="input-group-with-icon">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                <input type="password" id="ob-password" required placeholder="Min. 8 characters">
                                <button type="button" class="pw-toggle" onclick="togglePw('ob-password','eye-1-open','eye-1-closed')">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-1-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-1-closed" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="submit-btn" id="ob-register-btn">Create Account</button>
                        
                        <div style="margin-top:16px; text-align:center; font-size:12px;">
                            <a href="#" onclick="toggleObAuthMode('magic', event)" style="color:var(--text-secondary);text-decoration:none;font-weight:600;">Sign up with Magic Link →</a>
                        </div>
                    </form>

                    <form id="onboarding-magic-form" onsubmit="handleObMagicSubmit(event)" style="display:none;" autocomplete="off">
                        <div class="form-group">
                            <label for="ob-magic-email">Email Address</label>
                            <div class="input-group-with-icon">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <input type="email" id="ob-magic-email" required placeholder="jane@company.com" autocomplete="email">
                            </div>
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
                        <div class="input-group-with-icon">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input type="text" id="ob-full-name" required placeholder="e.g. Jane Smith" value="<?php echo esc_attr( $user_display_name ); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ob-business-name">Business Name</label>
                        <div class="input-group-with-icon">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <input type="text" id="ob-business-name" required placeholder="e.g. Skyline Realty, Studio Light" value="<?php echo esc_attr( $has_business ); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ob-phone">Phone / WhatsApp <span style="font-weight:500;text-transform:none;letter-spacing:0;color:var(--text-tertiary);">(optional)</span></label>
                        <div class="input-group-with-icon">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <input type="tel" id="ob-phone" placeholder="+91 98765 43210">
                        </div>
                        <div class="helper-text">Used for client communication & WhatsApp integration</div>
                    </div>
                    <div class="form-group">
                        <label for="ob-contact-email">Contact Email</label>
                        <div class="input-group-with-icon">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input type="email" id="ob-contact-email" placeholder="hello@yourbusiness.com" value="<?php echo esc_attr( $user_email ); ?>">
                        </div>
                        <div class="helper-text">Public email for your business profile</div>
                    </div>
                    <button type="submit" class="submit-btn" id="ob-business-btn">
                        Continue
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                    <div class="secure-footer-text">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Your information is secure and private
                    </div>
                </form>
            </div>

            <!-- ═══ STEP 3 — INDUSTRY SELECTION ═══ -->
            <div class="step-panel" id="step-3" data-step="3">
                <div class="step-label" style="text-align:center;">Step 3 of 4</div>
                <h2 class="ob-title" style="text-align:center; margin-bottom: 8px;">What's your industry?</h2>
                <p class="ob-subtitle" style="text-align:center; margin-bottom: 32px;">This helps us personalize your workspace with the right tools and templates.</p>

                <div class="industry-grid" id="industry-grid">
                    <div class="industry-card" data-industry="real_estate" onclick="selectIndustry(this)">
                        <div class="industry-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                        <div class="industry-name">Real Estate Agency</div>
                        <div class="industry-desc">Property listings, buyer leads, CRM pipeline, showings</div>
                    </div>

                    <div class="industry-card" data-industry="photography_studio" onclick="selectIndustry(this)">
                        <div class="industry-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                <circle cx="12" cy="13" r="4"/>
                            </svg>
                        </div>
                        <div class="industry-name">Photography Studio</div>
                        <div class="industry-desc">Client leads, shoot scheduling, equipment tracking</div>
                    </div>

                    <div class="industry-card locked" data-industry="marketing_agency" onclick="selectIndustry(this)">
                        <span class="coming-soon-badge">Coming Soon</span>
                        <div class="industry-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 5L6 9H2v6h4l5 4V5z"/>
                                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                                <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                            </svg>
                        </div>
                        <div class="industry-name">Marketing Agency</div>
                        <div class="industry-desc">Client projects, tasks, campaign performance</div>
                    </div>

                    <div class="industry-card locked" data-industry="it_services" onclick="selectIndustry(this)">
                        <span class="coming-soon-badge">Coming Soon</span>
                        <div class="industry-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                <line x1="8" y1="21" x2="16" y2="21"/>
                                <line x1="12" y1="17" x2="12" y2="21"/>
                            </svg>
                        </div>
                        <div class="industry-name">IT Services</div>
                        <div class="industry-desc">Projects, tickets, clients, team collaboration</div>
                    </div>

                    <div class="industry-card locked" data-industry="healthcare_practice" onclick="selectIndustry(this)">
                        <span class="coming-soon-badge">Coming Soon</span>
                        <div class="industry-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </div>
                        <div class="industry-name">Healthcare Practice</div>
                        <div class="industry-desc">Patient records, appointments, communication</div>
                    </div>

                    <div class="industry-card" data-industry="custom" onclick="selectIndustry(this)">
                        <div class="industry-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="21" x2="4" y2="14"/>
                                <line x1="4" y1="10" x2="4" y2="3"/>
                                <line x1="12" y1="21" x2="12" y2="12"/>
                                <line x1="12" y1="8" x2="12" y2="3"/>
                                <line x1="20" y1="21" x2="20" y2="16"/>
                                <line x1="20" y1="12" x2="20" y2="3"/>
                                <line x1="1" y1="14" x2="7" y2="14"/>
                                <line x1="9" y1="8" x2="15" y2="8"/>
                                <line x1="17" y1="16" x2="23" y2="16"/>
                            </svg>
                        </div>
                        <div class="industry-name">Custom / Other</div>
                        <div class="industry-desc">Configure your workspace your own way</div>
                    </div>
                </div>

                <button type="button" class="submit-btn" id="ob-industry-btn" onclick="handleIndustrySubmit()" disabled>
                    Continue
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
                <div class="secure-footer-text" style="margin-top:20px;">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Your data is secure. We'll never share it with anyone.
                </div>
            </div>

            <!-- ═══ STEP 4 — PWA APP PROMOTION ═══ -->
            <div class="step-panel" id="step-4" data-step="4">
                <div class="step-label" style="text-align:center;">Step 4 of 5</div>
                <h2 class="ob-title" style="text-align:center; margin-bottom: 8px;">Install Cora App</h2>
                <p class="ob-subtitle" style="text-align:center; margin-bottom: 24px;">For a premium desktop and mobile experience, install Cora to run standalone from your Dock or Home Screen.</p>

                <!-- PWA Animated Device Illustration Mockup -->
                <div class="pwa-mockup-container">
                    <div class="pwa-mockup-frame">
                        <div class="pwa-mockup-header">
                            <div class="pwa-mockup-dots"><span></span><span></span><span></span></div>
                            <div class="pwa-mockup-url">heycora.in</div>
                        </div>
                        <div class="pwa-mockup-content">
                            <div class="pwa-mockup-icon-card">
                                <svg width="28" height="28" viewBox="0 0 14 14" fill="none">
                                    <rect width="14" height="14" rx="3.5" fill="#18181b"/>
                                    <rect x="3" y="3" width="3.5" height="3.5" rx="1" fill="#ffffff"/>
                                    <rect x="7.5" y="3" width="3.5" height="3.5" rx="1" fill="#ffffff"/>
                                    <rect x="3" y="7.5" width="3.5" height="3.5" rx="1" fill="#ffffff"/>
                                    <rect x="7.5" y="7.5" width="3.5" height="3.5" rx="1" fill="#ffffff"/>
                                </svg>
                            </div>
                            <span class="pwa-mockup-appname">Cora Workspace</span>
                            <span class="pwa-mockup-badge">PWA Verified</span>
                        </div>
                    </div>
                </div>

                <!-- Action Install Button -->
                <button type="button" class="submit-btn" id="ob-pwa-install-btn" onclick="triggerPWAInstallation()">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" style="margin-right:4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Install Cora App
                </button>

                <!-- iOS Custom Instructions -->
                <div id="ob-pwa-ios-instructions" class="pwa-instructions-box" style="display:none;">
                    <div class="pwa-instructions-header">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-650"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <span>iOS Safari Setup</span>
                    </div>
                    <p class="pwa-instructions-text">Tap the Share icon <svg style="display:inline-block; vertical-align:middle;" viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/><path d="M18 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h5"/></svg> in Safari, scroll down and choose <strong>Add to Home Screen</strong>.</p>
                </div>

                <!-- Desktop Generic Instructions -->
                <div id="ob-pwa-desktop-instructions" class="pwa-instructions-box" style="display:none;">
                    <div class="pwa-instructions-header">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-650"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <span>App Installation</span>
                    </div>
                    <p class="pwa-instructions-text">Click the install prompt in your browser's address bar to add Cora to your device.</p>
                </div>

                <button type="button" class="skip-btn" onclick="skipPWAInstallation()">
                    Continue in Browser
                </button>
            </div>

            <!-- ═══ STEP 5 — ACTIVATION ═══ -->
            <div class="step-panel" id="step-5" data-step="5">
                <div class="activation-state" id="activation-state">
                    <div class="activation-spinner" id="activation-spinner"></div>
                    <div class="activation-check" id="activation-check">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div class="activation-title" id="activation-title">Setting up your workspace…</div>
                    <p class="activation-sub" id="activation-sub">Configuring features, CRM pipeline, and team roles for your industry.</p>
                    
                    <div class="secure-badge-pill" id="activation-secure-pill" style="display:none;">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-600 "><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>Your data is secure. We'll never share it with anyone.</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
</div>

<div id="cora-toast-container"></div>

<script>
(function() {
    'use strict';

    // ── Theme Standard (Light Mode) ───────────────────────────

    // ── State ─────────────────────────────────────────────────
    var currentStep = <?php echo intval( $initial_step ); ?>;
    var selectedIndustry = '';
    var registeredEmail = '';
    var ajaxUrl = '<?php echo esc_url( cora_get_origin_relative_url( admin_url( "admin-ajax.php" ) ) ); ?>';
    var nonce = '<?php echo esc_js( $nonce ); ?>';
    var loginNonce = '<?php echo esc_js( $login_nonce ); ?>';
    var isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    // ── Initialize ────────────────────────────────────────────
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    if (isStandalone) {
        var pwaIndicator = document.getElementById('step-pwa-indicator');
        var pwaLine = document.getElementById('step-pwa-line');
        if (pwaIndicator) pwaIndicator.style.display = 'none';
        if (pwaLine) pwaLine.style.display = 'none';
        var step5CircleNum = document.querySelector('.step-indicator-item[data-step="5"] .step-num');
        if (step5CircleNum) step5CircleNum.textContent = '4';
        if (currentStep === 4) {
            currentStep = 5;
        }
    }

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
        
        // Dynamically adjust shell width for step 3 (industry grid) to prevent compression
        var shell = document.getElementById('onboarding-shell');
        if (shell) {
            if (step === 3) {
                shell.style.maxWidth = '840px';
            } else {
                shell.style.maxWidth = '480px';
            }
        }

        currentStep = step;
        updateStepProgress(step);

        if (step === 5 && selectedIndustry) {
            triggerActivation();
        }
    }

    function updateStepProgress(step) {
        var items = document.querySelectorAll('.step-indicator-item');
        var lines = document.querySelectorAll('.step-indicator-line');

        items.forEach(function(item) {
            var itemStep = parseInt(item.getAttribute('data-step'));
            item.classList.remove('active', 'done');
            if (itemStep < step) item.classList.add('done');
            else if (itemStep === step) item.classList.add('active');
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
        if (card.classList.contains('locked')) {
            return;
        }
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
        if (isStandalone) {
            showStep(5);
        } else {
            showStep(4);
        }
    };

    // ── PWA Setup & Installation ──────────────────────────────
    var coraPwaDeferredPrompt = null;
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        coraPwaDeferredPrompt = e;
        var installBtn = document.getElementById('ob-pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'block';
        }
        var desktopInst = document.getElementById('ob-pwa-desktop-instructions');
        if (desktopInst) desktopInst.style.display = 'none';
    });

    window.triggerPWAInstallation = function() {
        if (coraPwaDeferredPrompt) {
            coraPwaDeferredPrompt.prompt();
            coraPwaDeferredPrompt.userChoice.then(function(choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    showToast('Cora App installation started.');
                    setTimeout(function() {
                        showStep(5);
                    }, 1000);
                } else {
                    showToast('Installation declined.');
                }
                coraPwaDeferredPrompt = null;
            });
        } else {
            var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            if (isIOS) {
                document.getElementById('ob-pwa-ios-instructions').style.display = 'block';
                document.getElementById('ob-pwa-desktop-instructions').style.display = 'none';
            } else {
                document.getElementById('ob-pwa-desktop-instructions').style.display = 'block';
                document.getElementById('ob-pwa-ios-instructions').style.display = 'none';
            }
        }
    };

    window.skipPWAInstallation = function() {
        showStep(5);
    };

    // ═══ STEP 5 — ACTIVATION ═════════════════════════════════

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
                
                var securePill = document.getElementById('activation-secure-pill');
                if (securePill) {
                    securePill.style.display = 'inline-flex';
                }

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
