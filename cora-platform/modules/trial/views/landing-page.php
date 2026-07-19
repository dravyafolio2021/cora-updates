<?php
/**
 * Cora Unified Onboarding Landing Page - demo.heycora.in
 * 
 * Interactive SaaS Subscription Cost Calculator + 10-Second Sandbox Signup Form.
 * Minimalist monochromatic aesthetic.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora AI — Real Estate Tech Audit & Workspace Generator</title>
    
    <!-- Enqueue Inter Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    
    <!-- Load jQuery (WordPress standard) -->
    <?php wp_print_scripts( array( 'jquery' ) ); ?>

    <style>
        :root {
            --bg-color: #FBFaf7; /* Warm cream */
            --primary-color: #18181b;
            --text-color: #27272a;
            --border-color: #e4e4e7;
            --font-sans: 'Inter', sans-serif;
            --font-serif: 'Playfair Display', serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: var(--font-sans);
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
        }

        .cora-landing-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 24px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(24, 24, 27, 0.05);
        }

        .logo-block {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 16px;
            color: var(--primary-color);
            letter-spacing: -0.03em;
        }

        .logo-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-trial {
            background-color: #f4f4f5;
            color: #71717a;
            border: 1px solid var(--border-color);
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Centered Hero Section Layout */
        .hero-centered {
            text-align: center;
            max-width: 800px;
            margin: 60px auto 40px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }

        .badge-india {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 550;
            color: #52525b;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .dot-orange {
            width: 6px;
            height: 6px;
            background-color: #f97316;
            border-radius: 50%;
            display: inline-block;
        }

        .hero-title-centered {
            font-size: 48px;
            font-weight: 800;
            color: var(--primary-color);
            line-height: 1.15;
            margin: 0;
            letter-spacing: -0.04em;
        }

        @media (max-width: 640px) {
            .hero-title-centered {
                font-size: 36px;
            }
        }

        .serif-one {
            font-family: var(--font-serif);
            font-style: italic;
            font-weight: 500;
            color: #52525b;
        }

        .hero-desc-centered {
            font-size: 15px;
            color: #52525b;
            line-height: 1.6;
            margin: 0;
            max-width: 680px;
        }

        .cta-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-top: 8px;
        }

        @media (max-width: 480px) {
            .cta-buttons {
                flex-direction: column;
                width: 100%;
            }
            .cta-buttons button {
                width: 100%;
            }
        }

        .btn-trial {
            padding: 14px 28px;
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.15s ease, transform 0.1s ease;
            box-shadow: 0 4px 10px rgba(24, 24, 27, 0.08);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-trial:hover {
            background-color: #27272a;
        }

        .btn-trial:active {
            transform: scale(0.98);
        }

        .btn-demo {
            padding: 14px 28px;
            background-color: #ffffff;
            color: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.15s ease, transform 0.1s ease;
        }

        .btn-demo:hover {
            background-color: #fafafa;
        }

        .btn-demo:active {
            transform: scale(0.98);
        }

        .cta-subtext {
            font-size: 11px;
            color: #a1a1aa;
            margin: 0;
        }

        /* Premium Dashboard Mockup styling */
        .dashboard-mockup {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(24, 24, 27, 0.05);
            overflow: hidden;
            margin: 40px auto 60px auto;
            text-align: left;
        }

        .mock-header {
            background: #fafafa;
            border-bottom: 1px solid #e4e4e7;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .mock-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .mock-dot-red { background: #ef4444; }
        .mock-dot-yellow { background: #f59e0b; }
        .mock-dot-green { background: #10b981; }

        .mock-body {
            display: flex;
            min-height: 480px;
            background: #fafafa;
        }

        @media (max-width: 768px) {
            .mock-body {
                flex-direction: column;
            }
            .mock-sidebar {
                width: 100% !important;
                border-right: none !important;
                border-bottom: 1px solid #e4e4e7;
            }
        }

        .mock-sidebar {
            width: 180px;
            background: #ffffff;
            border-right: 1px solid #e4e4e7;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .mock-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            font-size: 14px;
            color: var(--primary-color);
        }

        .mock-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .mock-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #71717a;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .mock-item.active {
            background: #fff7ed;
            color: #f97316;
        }

        .mock-content {
            flex: 1;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .mock-content-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .mock-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        .mock-subtitle {
            font-size: 11px;
            color: #71717a;
            margin: 2px 0 0 0;
        }

        .mock-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mock-search {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 11px;
            outline: none;
            width: 140px;
        }

        .mock-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            color: #18181b;
        }

        .mock-btn-orange {
            background: #f97316;
            color: #ffffff;
            border: none;
        }

        .mock-cols-board {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            flex: 1;
        }

        @media (max-width: 640px) {
            .mock-cols-board {
                grid-template-columns: 1fr;
            }
        }

        .mock-col {
            background: #f4f4f5;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .mock-col-header {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #71717a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 4px;
        }

        .mock-col-badge {
            background: #e4e4e7;
            padding: 1px 5px;
            border-radius: 10px;
            font-size: 8.5px;
        }

        .mock-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 8px 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.01);
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .mock-card-name {
            font-size: 11.5px;
            font-weight: 600;
            color: #18181b;
        }
        .mock-card-label {
            font-size: 9.5px;
            color: #71717a;
        }

        /* Floating WhatsApp AI Widget */
        .mock-whatsapp-ai {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            margin-top: 10px;
        }

        .wa-ai-head {
            font-size: 11px;
            font-weight: 700;
            color: #18181b;
            display: flex;
            align-items: center;
            gap: 6px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f4f4f5;
        }

        .wa-ai-dot {
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
        }

        .wa-ai-body {
            font-size: 11px;
            color: #52525b;
            line-height: 1.4;
            padding-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Sliding Signup Form Drawer */
        .cora-drawer-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(24, 24, 27, 0.4);
            backdrop-filter: blur(1.5px);
            z-index: 999998;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .cora-drawer-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .cora-drawer {
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            width: 420px;
            max-width: 95vw;
            background: #ffffff;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.08);
            z-index: 999999;
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
        }

        .cora-drawer.show {
            transform: translateX(0);
        }

        .drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid #f4f4f5;
            background: #fafafa;
        }

        .drawer-title {
            font-size: 16px;
            font-weight: 750;
            color: var(--primary-color);
            margin: 0;
        }

        .drawer-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #a1a1aa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .drawer-close:hover {
            color: var(--primary-color);
        }

        .drawer-body {
            padding: 24px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 10.5px;
            font-weight: 700;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13px;
            box-sizing: border-box;
            background-color: #ffffff;
            color: var(--primary-color);
            font-family: var(--font-sans);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(24, 24, 27, 0.08);
        }

        .form-input::placeholder {
            color: #d4d4d8;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: #ffffff;
            border: 0;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.15s ease, transform 0.1s ease;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #27272a;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-submit:disabled {
            background-color: #a1a1aa;
            cursor: not-allowed;
            transform: none;
        }

        .drawer-subtext {
            font-size: 10px;
            color: #a1a1aa;
            text-align: center;
            margin: 0;
            line-height: 1.4;
        }

        /* Onboarding Wizard Styles */
        .onboarding-step {
            display: none;
            opacity: 0;
            transform: translateY(8px) scale(0.995);
        }
        .onboarding-step.active {
            display: block !important;
            animation: fadeInStep 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeInStep {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .onboarding-progress {
            display: flex;
            gap: 6px;
            margin-bottom: 12px;
            justify-content: center;
            align-items: center;
        }
        .progress-dot {
            width: 32px;
            height: 4px;
            border-radius: 2px;
            background-color: #e4e4e7;
            transition: background-color 0.3s ease, width 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .progress-dot.active {
            background-color: #18181b;
            width: 48px;
        }
        .btn-google {
            width: 100%;
            padding: 12px;
            background-color: #ffffff;
            color: #18181b;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease;
            box-sizing: border-box;
        }
        .btn-google:hover {
            background-color: #fafafa;
            border-color: #d4d4d8;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .btn-google:active {
            transform: scale(0.98);
        }
        .verification-box {
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 20px;
            background: #fafafa;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            box-sizing: border-box;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.01);
        }

        /* Tool Auditor cost calculator section styling */
        .calculator-section {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 30px;
            margin-top: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.01);
        }

        .calculator-title {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #71717a;
            margin-bottom: 20px;
            border-bottom: 1px solid #f4f4f5;
            padding-bottom: 12px;
        }

        .calc-tool-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #f4f4f5;
        }

        .calc-tool-row:last-of-type {
            border-bottom: none;
        }

        .calc-tool-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .calc-checkbox {
            width: 16px;
            height: 16px;
            accent-color: var(--primary-color);
            cursor: pointer;
        }

        .calc-tool-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-color);
            cursor: pointer;
        }

        .calc-tool-desc {
            font-size: 10.5px;
            color: #a1a1aa;
            display: block;
            margin-top: 2px;
        }

        .calc-tool-cost {
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            color: #ef4444;
        }

        .calc-results-bar {
            margin-top: 24px;
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calc-results-left {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #991b1b;
        }

        .calc-results-value {
            font-size: 20px;
            font-weight: 800;
            color: #991b1b;
        }

        .calc-savings-box {
            margin-top: 12px;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calc-savings-left {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #065f46;
        }

        .calc-savings-value {
            font-size: 20px;
            font-weight: 800;
            color: #065f46;
        }

        footer {
            text-align: center;
            padding-top: 40px;
            border-top: 1px solid rgba(24, 24, 27, 0.05);
            font-size: 11px;
            color: #a1a1aa;
            margin-top: 40px;
        }

        /* Monochromatic Toast Notification styles */
        .cora-toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background-color: var(--primary-color);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 1000000;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
            white-space: nowrap;
        }

        .cora-toast.show {
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>
<body>

<div class="cora-landing-container">
    <header>
        <div class="logo-block">
            <div class="logo-circle">C</div>
            <span>Cora AI</span>
        </div>
        <span class="badge-trial">Free Sandbox</span>
    </header>

    <!-- Main Hero centered layout -->
    <main class="hero-centered">
        <div class="badge-india">
            <span class="dot-orange"></span>
            Built in India, for Indian real estate agencies
        </div>
        
        <h1 class="hero-title-centered">
            Run your entire agency.<br>On <span class="serif-one">one</span> platform.
        </h1>
        
        <p class="hero-desc-centered">
            Cora replaces the six tools your team juggles today — CRM, WhatsApp, listings, calling, payments and reports — with one AI-native system built for how Indian real estate actually works.
        </p>
        
        <div class="cta-buttons">
            <button class="btn-trial" onclick="openSignupDrawer()">
                Start 30-day free trial &rarr;
            </button>
            <button class="btn-demo" onclick="openSignupDrawer('demo')">
                Book a live demo
            </button>
        </div>
        
        <p class="cta-subtext">Full access. No credit card. Setup in under 10 minutes.</p>

        <!-- Premium Dashboard Mockup -->
        <div class="dashboard-mockup">
            <div class="mock-header">
                <span class="mock-dot mock-dot-red"></span>
                <span class="mock-dot mock-dot-yellow"></span>
                <span class="mock-dot mock-dot-green"></span>
            </div>
            <div class="mock-body">
                <div class="mock-sidebar">
                    <div class="mock-logo">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="#ea580c" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" class="inline-block"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                        <span>cora</span>
                    </div>
                    <div class="mock-menu">
                        <div class="mock-item active">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Leads</span>
                        </div>
                        <div class="mock-item">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><line x1="9" y1="9" x2="21" y2="9"></line></svg>
                            <span>Inventory</span>
                        </div>
                        <div class="mock-item">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Bookings</span>
                        </div>
                        <div class="mock-item">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            <span>Crew</span>
                        </div>
                    </div>
                </div>
                <div class="mock-content">
                    <div class="mock-content-head">
                        <div>
                            <h2 class="mock-title">Leads</h2>
                            <p class="mock-subtitle">Manage and track your leads in one place.</p>
                        </div>
                        <div class="mock-actions">
                            <input type="text" class="mock-search" placeholder="Search leads..." readonly />
                            <button class="mock-btn">Filters</button>
                            <button class="mock-btn mock-btn-orange">+ Add Lead</button>
                        </div>
                    </div>
                    <div class="mock-cols-board">
                        <div class="mock-col">
                            <div class="mock-col-header">
                                <span>New</span>
                                <span class="mock-col-badge">12</span>
                            </div>
                            <div class="mock-card">
                                <span class="mock-card-name">Aarav Sharma</span>
                                <span class="mock-card-label">DLF Phase 3</span>
                            </div>
                            <div class="mock-card">
                                <span class="mock-card-name">Priya Patel</span>
                                <span class="mock-card-label">Godrej Woods</span>
                            </div>
                        </div>
                        <div class="mock-col">
                            <div class="mock-col-header">
                                <span>Contacted</span>
                                <span class="mock-col-badge">8</span>
                            </div>
                            <div class="mock-card">
                                <span class="mock-card-name">Rohan Verma</span>
                                <span class="mock-card-label">M3M Golf Hills</span>
                            </div>
                        </div>
                        <div class="mock-col">
                            <div class="mock-col-header">
                                <span>Site Visit</span>
                                <span class="mock-col-badge">5</span>
                            </div>
                            <div class="mock-card">
                                <span class="mock-card-name">Ananya Sen</span>
                                <span class="mock-card-label">Shed scheduled</span>
                            </div>
                        </div>
                        <div class="mock-col">
                            <div class="mock-col-header">
                                <span>Booked</span>
                                <span class="mock-col-badge">3</span>
                            </div>
                            <div class="mock-card">
                                <span class="mock-card-name">Kabir Mehta</span>
                                <span class="mock-card-label">Booking Complete</span>
                            </div>
                        </div>
                    </div>

                    <!-- AI Assistant floating widget -->
                    <div class="mock-whatsapp-ai">
                        <div class="wa-ai-head">
                            <span class="wa-ai-dot"></span>
                            <span>WhatsApp AI Assistant</span>
                        </div>
                        <div class="wa-ai-body">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="#10b981" stroke-width="2.5" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            <span>This conversation is AI-powered. Cora AI is responding automatically.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tool Auditor cost calculator section lower down -->
        <div class="calculator-section" style="width: 100%;">
            <div class="calculator-title">Tool Subscription Cost Auditor</div>
            
            <div class="calc-tool-row">
                <div class="calc-tool-left">
                    <input type="checkbox" id="calc-crm" class="calc-checkbox" value="25000" checked onchange="calculateSavings()" />
                    <div>
                        <label for="calc-crm" class="calc-tool-label">Lead CRM & Pipelines</label>
                        <span class="calc-tool-desc">e.g. Sell.do / Salesforce (for 10 agents)</span>
                    </div>
                </div>
                <div class="calc-tool-cost">₹25,000/mo</div>
            </div>

            <div class="calc-tool-row">
                <div class="calc-tool-left">
                    <input type="checkbox" id="calc-hr" class="calc-checkbox" value="7000" checked onchange="calculateSavings()" />
                    <div>
                        <label for="calc-hr" class="calc-tool-label">Field Agent GPS Attendance</label>
                        <span class="calc-tool-desc">e.g. Keka HR / Spine HR</span>
                    </div>
                </div>
                <div class="calc-tool-cost">₹7,000/mo</div>
            </div>

            <div class="calc-tool-row">
                <div class="calc-tool-left">
                    <input type="checkbox" id="calc-wa" class="calc-checkbox" value="2000" checked onchange="calculateSavings()" />
                    <div>
                        <label for="calc-wa" class="calc-tool-label">WhatsApp Business API</label>
                        <span class="calc-tool-desc">e.g. Wati / AiSensy / Interakt</span>
                    </div>
                </div>
                <div class="calc-tool-cost">₹2,000/mo</div>
            </div>

            <div class="calc-tool-row">
                <div class="calc-tool-left">
                    <input type="checkbox" id="calc-drive" class="calc-checkbox" value="13000" checked onchange="calculateSavings()" />
                    <div>
                        <label for="calc-drive" class="calc-tool-label">Google Drive Storage (10 Users)</label>
                        <span class="calc-tool-desc">For heavy 4K site videos & KYC scans</span>
                    </div>
                </div>
                <div class="calc-tool-cost">₹13,000/mo</div>
            </div>

            <div class="calc-tool-row">
                <div class="calc-tool-left">
                    <input type="checkbox" id="calc-social" class="calc-checkbox" value="2500" checked onchange="calculateSavings()" />
                    <div>
                        <label for="calc-social" class="calc-tool-label">Social Media Scheduler</label>
                        <span class="calc-tool-desc">e.g. Hootsuite / Buffer</span>
                    </div>
                </div>
                <div class="calc-tool-cost">₹2,500/mo</div>
            </div>

            <!-- Monthly Spend Output -->
            <div class="calc-results-bar">
                <div class="calc-results-left">Current Monthly Spend:</div>
                <div class="calc-results-value" id="calc-outflow">₹49,500</div>
            </div>

            <!-- Savings with Cora Output -->
            <div class="calc-savings-box">
                <div class="calc-savings-left">Cora Annual Savings:</div>
                <div class="calc-savings-value" id="calc-savings">₹5,70,000</div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Cora Platform. Built for Indian Real Estate Agencies.</p>
    </footer>
</div>

<!-- Right-sliding signup drawer sheet -->
<div id="cora-drawer-overlay" class="cora-drawer-overlay" onclick="closeSignupDrawer()"></div>
<div id="cora-drawer" class="cora-drawer">
    <div class="drawer-header">
        <h3 class="drawer-title" id="drawer-headline">Launch Free Sandbox Site</h3>
        <button class="drawer-close" onclick="closeSignupDrawer()">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <div class="drawer-body">
        <p id="drawer-description" style="font-size: 12.5px; color: #71717a; margin: 0 0 10px 0; line-height: 1.5;">
            Zero hosting setup. Pre-seeded demo database. Get instant access to your admin workspace.
        </p>

        <!-- Progress Indicator -->
        <div id="onboarding-progress" class="onboarding-progress">
            <div class="progress-dot active" data-step="1"></div>
            <div class="progress-dot" data-step="2"></div>
            <div class="progress-dot" data-step="3"></div>
        </div>

        <!-- Step 1: Sign up with Google -->
        <div id="onboarding-step-1" class="onboarding-step">
            <p style="font-size: 13px; color: #27272a; margin-bottom: 20px; line-height: 1.5;">
                Create your secure account. Get started with Google for instant verification.
            </p>
            <button type="button" id="google-signup-btn" onclick="goToStep(2)" class="btn-google">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                </svg>
                Continue with Google
            </button>
            <div style="text-align: center; margin-top: 15px;">
                <span style="font-size: 11px; color: #a1a1aa;">or</span>
                <a href="javascript:void(0)" onclick="goToStep(2)" style="font-size: 11px; color: #18181b; font-weight: 600; text-decoration: underline; margin-left: 6px;">Sign up with email</a>
            </div>
        </div>

        <!-- Step 2: Email Verification -->
        <div id="onboarding-step-2" class="onboarding-step" style="display: none;">
            <div class="verification-box">
                <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" stroke-width="2" fill="none" style="color: #22c55e;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <div style="font-weight: 700; font-size: 14px; color: #18181b;">Check your email</div>
                <p style="font-size: 12px; color: #71717a; margin: 0; line-height: 1.5;">
                    We sent a verification link to your email address. Click the button below to verify and complete setup.
                </p>
            </div>
            <button type="button" id="verify-email-btn" onclick="goToStep(3)" class="btn-submit" style="margin-top: 15px;">
                Verify Email & Continue
            </button>
        </div>

        <!-- Step 3: Parameters Form -->
        <div id="onboarding-step-3" class="onboarding-step" style="display: none;">
            <form id="cora-signup-form" onsubmit="event.preventDefault(); handleCoraSignup();">
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label">Full Name</label>
                    <input type="text" id="signup-name" class="form-input" placeholder="e.g. Dravya Bansal" required />
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label">Agency Name</label>
                    <input type="text" id="signup-agency" class="form-input" placeholder="e.g. Apex Realty" required />
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="tel" id="signup-whatsapp" class="form-input" placeholder="e.g. +919876543210" required />
                </div>

                <div class="form-group" style="margin-bottom: 18px;">
                    <label class="form-label">City</label>
                    <input type="text" id="signup-city" class="form-input" placeholder="e.g. Gurgaon" required />
                </div>

                <button type="submit" id="submit-btn" class="btn-submit">
                    Spin Up My Workspace
                </button>
            </form>
        </div>

        <p class="drawer-subtext">By launching, you get 30 days free access. No credit card required.</p>
    </div>
</div>

<!-- Toast Overlay -->
<div id="cora-toast" class="cora-toast">Workspace generated! Redirecting...</div>

<script>
    function formatINR(number) {
        return '₹' + number.toLocaleString('en-IN');
    }

    function calculateSavings() {
        var totalMonthly = 0;
        var checkboxes = ['calc-crm', 'calc-hr', 'calc-wa', 'calc-drive', 'calc-social'];
        
        checkboxes.forEach(function(id) {
            var cb = document.getElementById(id);
            if (cb && cb.checked) {
                totalMonthly += parseInt(cb.value);
            }
        });

        var annualToolSpend = totalMonthly * 12;
        var coraProAnnual = 2000 * 12;
        var totalSavings = Math.max(0, annualToolSpend - coraProAnnual);

        document.getElementById('calc-outflow').innerHTML = formatINR(totalMonthly);
        document.getElementById('calc-savings').innerHTML = formatINR(totalSavings);
    }

    // Run initial calculations
    calculateSavings();

    function showToast(message) {
        var toast = document.getElementById('cora-toast');
        toast.innerHTML = message;
        toast.classList.add('show');
        setTimeout(function() {
            toast.classList.remove('show');
        }, 3000);
    }

    function goToStep(step) {
        document.querySelectorAll('.onboarding-step').forEach(function(el) {
            el.style.display = 'none';
        });
        var targetEl = document.getElementById('onboarding-step-' + step);
        if (targetEl) {
            targetEl.style.display = 'block';
        }
        var progressContainer = document.getElementById('onboarding-progress');
        if (progressContainer) {
            progressContainer.querySelectorAll('.progress-dot').forEach(function(dot) {
                var s = parseInt(dot.getAttribute('data-step'));
                if (s === step) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }
    }

    function openSignupDrawer(mode) {
        var prog = document.getElementById('onboarding-progress');
        if (mode === 'demo') {
            document.getElementById('drawer-headline').innerText = "Book a Live Demo";
            document.getElementById('drawer-description').innerText = "Schedule a 1-on-1 walkthrough session with our product experts to learn how Cora can run your agency on a single system.";
            document.getElementById('submit-btn').innerText = "Request Demo Call";
            if (prog) prog.style.display = 'none';
            goToStep(3);
        } else {
            document.getElementById('drawer-headline').innerText = "Launch Free Sandbox Site";
            document.getElementById('drawer-description').innerText = "Zero hosting setup. Pre-seeded demo database. Get instant access to your admin workspace.";
            document.getElementById('submit-btn').innerText = "Spin Up My Workspace";
            if (prog) prog.style.display = 'flex';
            goToStep(1);
        }
        document.getElementById('cora-drawer-overlay').classList.add('show');
        document.getElementById('cora-drawer').classList.add('show');
    }

    // Export function to window
    window.openSignupDrawer = openSignupDrawer;
    window.goToStep = goToStep;
    window.coraShowToast = showToast;

    function closeSignupDrawer() {
        document.getElementById('cora-drawer-overlay').classList.remove('show');
        document.getElementById('cora-drawer').classList.remove('show');
    }

    function handleCoraSignup() {
        var name = document.getElementById('signup-name').value;
        var agency = document.getElementById('signup-agency').value;
        var whatsapp = document.getElementById('signup-whatsapp').value;
        var city = document.getElementById('signup-city').value;
        var btn = document.getElementById('submit-btn');

        var isDemoMode = document.getElementById('drawer-headline').innerText.includes("Demo");

        btn.disabled = true;
        if (isDemoMode) {
            btn.innerHTML = 'Sending Request...';
        } else {
            btn.innerHTML = 'Provisioning Sandbox (5s)...';
            showToast("Spinning up subsite databases and assets...");
        }

        jQuery.post('<?php echo admin_url("admin-ajax.php"); ?>', {
            action: 'cora_trial_signup',
            name: name,
            agency_name: agency,
            whatsapp: whatsapp,
            city: city,
            _nonce: '<?php echo wp_create_nonce("cora_trial_signup"); ?>'
        }, function(response) {
            if (response.success) {
                if (isDemoMode) {
                    showToast("Demo request sent! We will contact you on WhatsApp.");
                    setTimeout(function() {
                        closeSignupDrawer();
                        btn.disabled = false;
                        btn.innerHTML = 'Request Demo Call';
                    }, 2000);
                } else {
                    showToast("Workspace ready! Opening dashboard...");
                    setTimeout(function() {
                        window.location.href = response.data.workspace_url;
                    }, 1000);
                }
            } else {
                btn.disabled = false;
                btn.innerHTML = isDemoMode ? 'Request Demo Call' : 'Spin Up My Workspace';
                
                if (response.data && response.data.redirect_url) {
                    showToast("Redirecting to existing active workspace...");
                    setTimeout(function() {
                        window.location.href = response.data.redirect_url;
                    }, 1500);
                } else {
                    showToast(response.data.message || "Failed to complete action.");
                }
            }
        }).fail(function() {
            btn.disabled = false;
            btn.innerHTML = isDemoMode ? 'Request Demo Call' : 'Spin Up My Workspace';
            showToast("Server connection error. Please try again.");
        });
    }
</script>

</body>
</html>
