<?php
/**
 * Cora Platform - Self-Healing Database Error Drop-in (wp-content/db-error.php)
 * 
 * Intercepts all database connection failures, attempts automatic connection retries,
 * provides clean JSON responses for AJAX/REST, and renders a monochromatic self-healing
 * recovery screen with auto-reload for browser sessions.
 */

// 1. Attempt immediate reconnection retry if constants are available
if ( defined( 'DB_USER' ) && defined( 'DB_PASSWORD' ) && defined( 'DB_NAME' ) && defined( 'DB_HOST' ) ) {
    $db_host = DB_HOST;
    $db_port = null;
    $db_socket = null;
    
    if ( false !== strpos( $db_host, ':' ) ) {
        $parts = explode( ':', $db_host, 2 );
        $db_host = $parts[0];
        if ( is_numeric( $parts[1] ) ) {
            $db_port = (int) $parts[1];
        } else {
            $db_socket = $parts[1];
        }
    }

    // Try up to 3 rapid reconnect attempts with progressive backoff
    for ( $attempt = 1; $attempt <= 3; $attempt++ ) {
        usleep( $attempt * 100000 ); // 100ms, 200ms, 300ms
        $test_conn = @mysqli_init();
        if ( $test_conn ) {
            @mysqli_options( $test_conn, MYSQLI_OPT_CONNECT_TIMEOUT, 3 );
            $connected = @mysqli_real_connect( $test_conn, $db_host, DB_USER, DB_PASSWORD, DB_NAME, $db_port, $db_socket );
            if ( $connected ) {
                @mysqli_close( $test_conn );
                // Reconnection successful! Seamlessly refresh current request
                if ( ! headers_sent() ) {
                    header( 'Refresh: 0' );
                }
                echo '<script>window.location.reload();</script>';
                exit;
            }
        }
    }
}

// 2. Determine if this is an AJAX, REST, or API request
$is_ajax_or_api = false;
if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || 
     ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ||
     ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' ) ||
     ( isset( $_SERVER['HTTP_ACCEPT'] ) && strpos( $_SERVER['HTTP_ACCEPT'], 'application/json' ) !== false ) ||
     ( isset( $_SERVER['REQUEST_URI'] ) && ( strpos( $_SERVER['REQUEST_URI'], '/wp-json/' ) !== false || strpos( $_SERVER['REQUEST_URI'], 'admin-ajax.php' ) !== false ) ) ) {
    $is_ajax_or_api = true;
}

if ( $is_ajax_or_api ) {
    if ( ! headers_sent() ) {
        http_response_code( 503 );
        header( 'Content-Type: application/json; charset=UTF-8' );
        header( 'Retry-After: 2' );
    }
    echo json_encode( array(
        'success' => false,
        'code'    => 'db_temporarily_busy',
        'message' => 'Database connection is temporarily busy. Automatically retrying...',
        'retry'   => true
    ) );
    exit;
}

// 3. Render monochromatic self-healing recovery screen for web browsers
if ( ! headers_sent() ) {
    http_response_code( 503 );
    header( 'Retry-After: 2' );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Connecting to Workspace — Cora Platform</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: #09090b;
            color: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Inter", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }
        .cora-recovery-card {
            background: #18181b;
            border: 1px solid #27272a;
            border-radius: 20px;
            max-width: 440px;
            width: 100%;
            padding: 32px 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            text-align: center;
        }
        .cora-beacon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: #27272a;
            margin-bottom: 20px;
            position: relative;
        }
        .cora-spinner {
            width: 24px;
            height: 24px;
            border: 2px solid #3f3f46;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: cora-spin 0.8s linear infinite;
        }
        @keyframes cora-spin {
            to { transform: rotate(360deg); }
        }
        h1 {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }
        p {
            font-size: 13px;
            color: #a1a1aa;
            line-height: 1.5;
            margin-bottom: 24px;
        }
        .cora-countdown-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: #27272a;
            border: 1px solid #3f3f46;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            color: #e4e4e7;
            font-family: ui-monospace, SFMono-Regular, "JetBrains Mono", Menlo, monospace;
            margin-bottom: 20px;
        }
        .cora-btn-retry {
            display: block;
            width: 100%;
            padding: 10px 16px;
            background: #ffffff;
            color: #09090b;
            border: none;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.15s ease;
            text-decoration: none;
        }
        .cora-btn-retry:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="cora-recovery-card">
        <div class="cora-beacon-wrap">
            <div class="cora-spinner"></div>
        </div>
        <h1>Connecting to Workspace</h1>
        <p>The workspace engine is actively synchronizing with the database. Reconnecting automatically...</p>
        
        <div class="cora-countdown-pill">
            <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:#22c55e;"></span>
            <span>Retrying in <span id="cora-timer">2</span>s</span>
        </div>

        <button onclick="window.location.reload();" class="cora-btn-retry">
            Retry Connection Now
        </button>
    </div>

    <script>
        (function() {
            var count = 2;
            var timerEl = document.getElementById('cora-timer');
            var interval = setInterval(function() {
                count--;
                if (timerEl) timerEl.textContent = count;
                if (count <= 0) {
                    clearInterval(interval);
                    window.location.reload();
                }
            }, 1000);
        })();
    </script>
</body>
</html>
