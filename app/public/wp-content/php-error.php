<?php
/**
 * Cora Workspace Platform - White-labeled PHP Error Template
 * Placed in wp-content/php-error.php to override the default WordPress critical error screen.
 * Shows zero mentions of WordPress and has a clean, professional Notion-styled monochromatic UI.
 */

$show_details = true;
$error = error_get_last();

// Set header status safely using native PHP functions
if ( ! headers_sent() ) {
    if ( function_exists( 'status_header' ) ) {
        status_header( 500 );
    } else {
        http_response_code( 500 );
    }
    header( 'Content-Type: text/html; charset=utf-8' );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workspace System Interruption</title>
    <style>
        :root {
            --bg-color: #fafaf9;
            --card-bg: #ffffff;
            --text-primary: #09090b;
            --text-secondary: #71717a;
            --border-color: #e4e4e7;
            --accent: #18181b;
            --accent-hover: #27272a;
        }
        
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #09090b;
                --card-bg: #18181b;
                --text-primary: #f4f4f5;
                --text-secondary: #a1a1aa;
                --border-color: #27272a;
                --accent: #f4f4f5;
                --accent-hover: #e4e4e7;
            }
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            line-height: 1.5;
        }
        
        .container {
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 36px 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            margin-bottom: 20px;
        }
        
        .logo-icon {
            width: 44px;
            height: 44px;
            margin: 0 auto 20px auto;
            border-radius: 10px;
            background-color: var(--accent);
            color: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }
        
        p {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 24px;
            padding: 0 10px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            padding: 0 20px;
            background-color: var(--accent);
            color: var(--card-bg);
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.15s, transform 0.1s;
        }
        
        .btn:hover {
            background-color: var(--accent-hover);
        }
        
        .btn:active {
            transform: scale(0.98);
        }
        
        .btn-secondary {
            background-color: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background-color: var(--border-color);
        }
        
        .details-box {
            margin-top: 24px;
            text-align: left;
            border-top: 1px dashed var(--border-color);
            padding-top: 16px;
        }
        
        .details-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .details-content {
            font-family: SFMono-Regular, Consolas, "Liberation Mono", Menlo, monospace;
            font-size: 11px;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px;
            overflow-x: auto;
            color: var(--text-primary);
            white-space: pre-wrap;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                    <line x1="15" y1="3" x2="15" y2="21"></line>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="3" y1="15" x2="21" y2="15"></line>
                </svg>
            </div>
            <h1>System Interruption</h1>
            <p>A critical execution boundary was encountered on this workspace. The system log has captured the details and support coordinates have been informed.</p>
            
            <a href="#" onclick="window.location.reload(); return false;" class="btn">Reload Workspace</a>
            <a href="/workspace" class="btn btn-secondary">Go to Dashboard</a>
            
            <?php if ( $show_details && isset( $error ) ) : ?>
                <div class="details-box">
                    <div class="details-title" onclick="const el = document.getElementById('err-log'); el.style.display = el.style.display === 'none' ? 'block' : 'none';">
                        <span>Developer Console Logs</span>
                        <span>[Toggle]</span>
                    </div>
                    <div id="err-log" class="details-content" style="display: none;">
                        <strong>Message:</strong> <?php echo htmlspecialchars( $error['message'], ENT_QUOTES, 'UTF-8' ); ?><br>
                        <strong>File:</strong> <?php echo htmlspecialchars( $error['file'], ENT_QUOTES, 'UTF-8' ); ?><br>
                        <strong>Line:</strong> <?php echo htmlspecialchars( $error['line'], ENT_QUOTES, 'UTF-8' ); ?><br>
                        <strong>Type:</strong> <?php echo htmlspecialchars( $error['type'], ENT_QUOTES, 'UTF-8' ); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
