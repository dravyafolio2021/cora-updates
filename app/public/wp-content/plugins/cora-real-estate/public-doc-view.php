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
        $favicon_url = CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png';
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

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .container {
                border: none;
                box-shadow: none;
            }
            .header-bar, .expiry-box, .actions {
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

<?php if ( ! $is_expired ) : ?>
<script>
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
                window.location.reload(); // Refresh to trigger server-side expiry warning
                return;
            }
            
            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Add warning color if less than 1 hour remaining
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
</script>
<?php endif; ?>

</body>
</html>
