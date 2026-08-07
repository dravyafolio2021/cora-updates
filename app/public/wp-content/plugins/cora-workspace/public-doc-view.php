<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php
    $favicon_url = get_option( 'cora_brand_favicon_url', '' );
    if ( empty( $favicon_url ) ) {
        $favicon_url = CORA_WORKSPACE_URL . 'assets/images/cora-favicon.png';
    }
    ?>
    <link rel="icon" type="image/png" href="<?php echo esc_url( $favicon_url ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $found_doc['title'] ); ?> • Secure Preview</title>
    <!-- Outfit and Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-display: 'Outfit', sans-serif;
        }
        body {
            font-family: var(--font-sans);
            background-color: #fafaf9;
            color: #18181b;
            margin: 0;
            padding: 40px 20px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }
        .header-bar {
            background-color: #18181b;
            color: #ffffff;
            padding: 24px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .header-logo {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 18px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header-tag {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            background: rgba(255, 255, 255, 0.15);
            padding: 4px 10px;
            border-radius: 9999px;
            margin-left: auto;
        }
        .content-area {
            padding: 40px 48px;
        }
        .doc-meta {
            display: flex;
            gap: 24px;
            border-bottom: 1px solid #f4f4f5;
            padding-bottom: 24px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }
        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .meta-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #a1a1aa;
        }
        .meta-value {
            font-size: 13px;
            font-weight: 600;
            color: #27272a;
        }
        .doc-title {
            font-family: var(--font-display);
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #09090b;
            margin: 0 0 16px 0;
        }
        .badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .badge-proposal { background: #f4f4f5; color: #18181b; border: 1px solid #e4e4e7; }
        .badge-invoice { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .badge-contract { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        
        .doc-body {
            font-size: 14px;
            color: #27272a;
            line-height: 1.7;
        }
        .doc-body h3 {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 600;
            margin-top: 24px;
            margin-bottom: 12px;
            color: #09090b;
        }
        .doc-body ul, .doc-body ol {
            padding-left: 20px;
            margin-bottom: 16px;
        }
        .doc-body li {
            margin-bottom: 6px;
        }
        
        /* Expiry Box */
        .expiry-box {
            background-color: #fafaf9;
            border: 1px solid #e4e4e7;
            border-radius: 12px;
            padding: 16px 24px;
            margin-top: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .expiry-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .expiry-indicator {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background-color: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        }
        .expiry-indicator.expiring {
            background-color: #f59e0b;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.5);
        }
        .expiry-text {
            font-size: 12px;
            font-weight: 500;
            color: #52525b;
        }
        .expiry-timer {
            font-family: monospace;
            font-size: 14px;
            font-weight: 700;
            color: #18181b;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            padding: 4px 10px;
            border-radius: 6px;
        }
        
        /* Expired layout */
        .expired-container {
            text-align: center;
            padding: 80px 48px;
        }
        .expired-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px auto;
        }
        .expired-title {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 12px 0;
            color: #09090b;
        }
        .expired-desc {
            font-size: 13px;
            color: #71717a;
            max-width: 400px;
            margin: 0 auto;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 32px;
            border-top: 1px solid #f4f4f5;
            padding-top: 24px;
        }
        .btn-print {
            font-family: var(--font-sans);
            font-weight: 600;
            font-size: 12px;
            background-color: #18181b;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print:hover {
            background-color: #27272a;
        }

        /* Branding Elements Styles */
        .doc-logo-branding {
            margin-bottom: 24px;
            text-align: left;
        }
        .doc-logo-branding img {
            max-height: 60px;
            max-width: 200px;
            object-fit: contain;
        }
        .doc-footer-branding {
            border-top: 1px solid #e4e4e7;
            padding-top: 16px;
            margin-top: 32px;
            font-size: 11px;
            color: #71717a;
            text-align: center;
        }

        /* E-Sign Portal Styles */
        .esign-portal-section {
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid #e4e4e7;
        }
        .esign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 16px;
        }
        @media (max-width: 640px) {
            .esign-grid {
                grid-template-columns: 1fr;
            }
        }
        .esign-card {
            background-color: #fafaf9;
            border: 1px solid #e4e4e7;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .esign-card-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #71717a;
        }
        .esign-canvas-wrapper {
            border: 1.5px dashed #d4d4d8;
            border-radius: 12px;
            background: #ffffff;
            overflow: hidden;
            position: relative;
        }
        .esign-canvas {
            width: 100%;
            height: 120px;
            display: block;
            cursor: crosshair;
        }
        .esign-input {
            width: 100%;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 13px;
            outline: none;
            box-sizing: border-box;
            font-family: var(--font-sans);
        }
        .esign-input:focus {
            border-color: #18181b;
        }
        .esign-btn-submit {
            background-color: #18181b;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.15s;
            font-family: var(--font-sans);
        }
        .esign-btn-submit:hover {
            background-color: #27272a;
        }
        .esign-btn-clear {
            background: none;
            border: none;
            color: #71717a;
            font-size: 11px;
            font-weight: 600;
            text-decoration: underline;
            cursor: pointer;
            align-self: flex-end;
        }
        .esign-stamp-box {
            height: 60px;
            border-bottom: 1px dashed #e4e4e7;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border-radius: 8px;
            padding: 4px;
        }
        .esign-stamp-box img {
            max-height: 50px;
            max-width: 100%;
            object-fit: contain;
        }
        .esign-verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #10b981;
            font-size: 11px;
            font-weight: 700;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .container {
                border: none;
                box-shadow: none;
            }
            .header-bar, .expiry-box, .actions, #guest-esign-clear-btn, #guest-signer-name, #guest-signer-email, #guest-esign-submit-btn, .esign-canvas-wrapper {
                display: none !important;
            }
            .content-area {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <?php if ( $is_expired ) : ?>
        <div class="expired-container">
            <div class="expired-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h2 class="expired-title">Secure Access Expired</h2>
            <p class="expired-desc">This document sharing link has reached its designated expiration date and is no longer accessible. Please contact the studio manager to request a new link.</p>
        </div>
    <?php else : ?>
        <div class="header-bar">
            <div class="header-logo">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="inline">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>Cora for Real Estate Secure Share</span>
            </div>
            <span class="header-tag">Protected Link</span>
        </div>

        <div class="content-area">
            <?php if ( ! empty( $found_doc['logo_url'] ) ) : ?>
                <div class="doc-logo-branding">
                    <img src="<?php echo esc_url( $found_doc['logo_url'] ); ?>" alt="Logo">
                </div>
            <?php endif; ?>

            <h1 class="doc-title"><?php echo esc_html( $found_doc['title'] ); ?></h1>
            
            <div class="doc-meta">
                <div class="meta-item">
                    <span class="meta-label">Document Type</span>
                    <span class="meta-value">
                        <span class="badge badge-<?php echo strtolower( $found_doc['type'] ); ?>">
                            <?php echo esc_html( $found_doc['type'] ); ?>
                        </span>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Issue Date</span>
                    <span class="meta-value"><?php echo esc_html( date('M d, Y', strtotime($found_doc['created_date'])) ); ?></span>
                </div>
                <?php if ( ! empty( $found_doc['amount'] ) ) : ?>
                    <div class="meta-item">
                        <span class="meta-label">Total Amount</span>
                        <span class="meta-value"><?php echo esc_html( $found_doc['amount'] ); ?></span>
                    </div>
                <?php endif; ?>
                <div class="meta-item">
                    <span class="meta-label">Status</span>
                    <span class="meta-value"><?php echo esc_html( $found_doc['status'] ); ?></span>
                </div>
            </div>

            <div class="doc-body">
                <?php echo wp_kses_post( $found_doc['content'] ); ?>
            </div>

            <!-- DUAL E-SIGNATURE INTERFACE -->
            <div class="esign-portal-section">
                <h3 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; margin-top: 0; margin-bottom: 8px; color: #09090b;">Authorization & E-Signature</h3>
                <p style="font-size: 12px; color: #71717a; margin-top: 0; margin-bottom: 20px;">This document requires electronic signatures from both the preparing studio representative and the client signatory.</p>
                
                <div class="esign-grid">
                    <!-- Column 1: Prepared By (Admin) -->
                    <div class="esign-card">
                        <span class="esign-card-title">Prepared By (Workspace Signatory)</span>
                        <?php if ( ! empty($found_doc['admin_signed']) ) : ?>
                            <div class="esign-stamp-box">
                                <img src="<?php echo esc_attr($found_doc['admin_signature_data']); ?>" alt="Admin Signature Stamp">
                            </div>
                            <div style="font-size: 12px; font-weight: 600; color: #18181b;">
                                <?php echo esc_html($found_doc['admin_signer_name'] ?? 'Authorized representative'); ?>
                            </div>
                            <div style="font-size: 11px; color: #71717a; font-family: monospace;">
                                Signed: <?php echo esc_html(date('d M Y H:i', strtotime($found_doc['admin_signed_at']))); ?>
                            </div>
                            <span class="esign-verified-badge">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Verified Stamp
                            </span>
                        <?php else : ?>
                            <div style="height: 60px; border-bottom: 1px dashed #e4e4e7; display: flex; align-items: center; justify-content: center; color: #a1a1aa; font-size: 12px; font-style: italic; background: #ffffff; border-radius: 8px;">
                                Awaiting Workspace Sign-off
                            </div>
                            <div style="font-size: 12px; font-weight: 600; color: #71717a;">Authorized Signatory</div>
                        <?php endif; ?>
                    </div>

                    <!-- Column 2: Client Acceptance (Client) -->
                    <div class="esign-card" id="client-esign-card-container">
                        <span class="esign-card-title">Client Acceptance (Client Signatory)</span>
                        <?php if ( ! empty($found_doc['client_signed']) || ! empty($found_doc['signed']) ) : 
                            $cl_name = $found_doc['client_signer_name'] ?? $found_doc['signer_name'] ?? 'Client Signatory';
                            $cl_date = $found_doc['client_signed_at'] ?? $found_doc['signed_at'] ?? '';
                            $cl_sig = $found_doc['client_signature_data'] ?? $found_doc['signature_data'] ?? $found_doc['signature_image'] ?? '';
                        ?>
                            <div class="esign-stamp-box">
                                <?php if ( ! empty($cl_sig) ) : ?>
                                    <img src="<?php echo esc_attr($cl_sig); ?>" alt="Client Signature Stamp">
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 12px; font-weight: 600; color: #18181b;">
                                <?php echo esc_html($cl_name); ?>
                            </div>
                            <div style="font-size: 11px; color: #71717a; font-family: monospace;">
                                Signed: <?php echo esc_html(empty($cl_date) ? '' : date('d M Y H:i', strtotime($cl_date))); ?>
                            </div>
                            <span class="esign-verified-badge">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Verified Stamp
                            </span>
                        <?php else : ?>
                            <!-- Drawing Canvas and Form for Guest -->
                            <div class="esign-canvas-wrapper">
                                <canvas id="guest-esign-canvas" class="esign-canvas"></canvas>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <span style="font-size: 10px; color: #a1a1aa;">Draw signature above</span>
                                <button type="button" id="guest-esign-clear-btn" class="esign-btn-clear">Clear Pad</button>
                            </div>
                            
                            <div style="space-y-2 mt-2">
                                <input type="text" id="guest-signer-name" placeholder="Your Full Name *" class="esign-input" value="<?php echo esc_attr($found_doc['client_name'] ?? ''); ?>">
                                <input type="email" id="guest-signer-email" placeholder="Your Email Address *" class="esign-input" style="margin-top: 8px;" value="<?php echo esc_attr($found_doc['client_email'] ?? ''); ?>">
                            </div>
                            
                            <button type="button" id="guest-esign-submit-btn" class="esign-btn-submit" style="margin-top: 12px;">
                                Confirm & Execute E-Signature Stamp →
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ( ! empty( $found_doc['footer_text'] ) ) : ?>
                <div class="doc-footer-branding">
                    <?php echo esc_html( $found_doc['footer_text'] ); ?>
                </div>
            <?php endif; ?>

            <div class="expiry-box">
                <div class="expiry-left">
                    <div class="expiry-indicator" id="timer-indicator"></div>
                    <span class="expiry-text">
                        <?php if ( empty( $found_share['expiry_time'] ) || intval( $found_share['expiry_time'] ) === 0 ) : ?>
                            This secure sharing link is active and permanent (no expiry).
                        <?php else : ?>
                            This secure link is active. It will expire in:
                        <?php endif; ?>
                    </span>
                </div>
                <span class="expiry-timer <?php echo ( empty( $found_share['expiry_time'] ) || intval( $found_share['expiry_time'] ) === 0 ) ? 'hidden' : ''; ?>" id="countdown-timer">--:--:--</span>
            </div>

            <div class="actions">
                <button onclick="window.print()" class="btn-print">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print or Save PDF
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="guest-toast" class="hidden fixed bottom-6 right-6 bg-zinc-950 text-white text-xs font-bold px-4 py-3 rounded-xl shadow-lg z-[9999] transition-all transform translate-y-2 opacity-0"></div>

<?php if ( ! $is_expired ) : ?>
<script>
    // Expiry timer logic
    (function() {
        const expiryTime = <?php echo intval( $found_share['expiry_time'] ); ?> * 1000;
        
        if (expiryTime === 0) {
            const countdownEl = document.getElementById('countdown-timer');
            if (countdownEl) {
                countdownEl.innerText = "Never";
            }
            return;
        }
        
        function updateTimer() {
            const now = new Date().getTime();
            const distance = expiryTime - now;
            
            if (distance < 0) {
                document.getElementById('countdown-timer').innerText = "Expired";
                const indicator = document.getElementById('timer-indicator');
                if (indicator) {
                    indicator.classList.remove('expiring');
                    indicator.style.backgroundColor = '#ef4444';
                    indicator.style.boxShadow = '0 0 8px rgba(239, 68, 68, 0.5)';
                }
                window.location.reload();
                return;
            }
            
            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            if (distance < 1000 * 60 * 60) {
                const indicator = document.getElementById('timer-indicator');
                if (indicator) {
                    indicator.classList.add('expiring');
                    indicator.style.backgroundColor = '#f59e0b';
                    indicator.style.boxShadow = '0 0 8px rgba(245, 158, 11, 0.5)';
                }
            }
            
            const pad = (num) => String(num).padStart(2, '0');
            const countdownEl = document.getElementById('countdown-timer');
            if (countdownEl) {
                countdownEl.innerText = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            }
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
    })();

    // Toast helpers
    function showGuestToast(msg) {
        const toast = document.getElementById('guest-toast');
        if (!toast) return;
        toast.textContent = msg;
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 50);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(2px)';
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 3000);
    }

    // Guest client e-signature pad logic
    (function() {
        const canvas = document.getElementById('guest-esign-canvas');
        if (!canvas) return;

        canvas.width = canvas.clientWidth || 300;
        canvas.height = canvas.clientHeight || 120;

        const ctx = canvas.getContext('2d');
        ctx.lineWidth = 2.2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#18181b';

        let drawing = false;
        let hasStrokes = false;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        }

        canvas.addEventListener('mousedown', (e) => {
            drawing = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        });
        canvas.addEventListener('mousemove', (e) => {
            if (!drawing) return;
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            hasStrokes = true;
        });
        window.addEventListener('mouseup', () => { drawing = false; });

        canvas.addEventListener('touchstart', (e) => {
            drawing = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            e.preventDefault();
        });
        canvas.addEventListener('touchmove', (e) => {
            if (!drawing) return;
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            hasStrokes = true;
            e.preventDefault();
        });
        canvas.addEventListener('touchend', () => { drawing = false; });

        document.getElementById('guest-esign-clear-btn').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasStrokes = false;
        });

        document.getElementById('guest-esign-submit-btn').addEventListener('click', () => {
            const name = document.getElementById('guest-signer-name').value.trim();
            const email = document.getElementById('guest-signer-email').value.trim();

            if (!name || !email) {
                showGuestToast('Please provide your full name and email.');
                return;
            }
            if (!hasStrokes) {
                showGuestToast('Please draw your signature before submitting.');
                return;
            }

            const signatureData = canvas.toDataURL();
            
            document.getElementById('guest-esign-submit-btn').disabled = true;
            document.getElementById('guest-esign-submit-btn').textContent = 'Processing Stamp...';

            const params = new URLSearchParams({
                action: 'cora_client_esign',
                doc_id: '<?php echo esc_js($found_doc["id"]); ?>',
                share_hash: '<?php echo esc_js($hash); ?>',
                signer_name: name,
                signer_email: email,
                signature_data: signatureData
            });

            fetch('<?php echo esc_url(cora_get_origin_relative_url(admin_url("admin-ajax.php"))); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showGuestToast('E-Signature submitted successfully!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showGuestToast(data.data || 'Failed to submit signature.');
                    document.getElementById('guest-esign-submit-btn').disabled = false;
                    document.getElementById('guest-esign-submit-btn').textContent = 'Confirm & Execute E-Signature Stamp →';
                }
            })
            .catch(err => {
                showGuestToast('Connection error. Please try again.');
                document.getElementById('guest-esign-submit-btn').disabled = false;
                document.getElementById('guest-esign-submit-btn').textContent = 'Confirm & Execute E-Signature Stamp →';
            });
        });
    })();
</script>
<?php endif; ?>

<?php
// Inject Made in Cora backlink badge
include CORA_WORKSPACE_PATH . 'views/view-backlink-badge.php';
?>
</body>
</html>
